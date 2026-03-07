"use strict";

(function () {
	function run() {
		var popups = Array.prototype.slice.call(document.querySelectorAll("[data-bb-promo-popup]"));
		if (!popups.length) {
			return;
		}
		if (document.body.dataset.bbPopupQueueInitialized === "1") {
			return;
		}
		document.body.dataset.bbPopupQueueInitialized = "1";

		var showTimeout = null;
		var hideTimeout = null;
		var currentPopup = null;
		var currentIndex = -1;
		var closeAnimationMs = 220;
		var isClosing = false;

		function canUseStorage() {
			try {
				return typeof window.localStorage !== "undefined";
			} catch (error) {
				return false;
			}
		}

		function getStorageKey(popup) {
			var popupId = popup.getAttribute("data-popup-id") || "";
			return popupId ? "bbPopupClosed_" + popupId : "";
		}

		function shouldShowOnlyOnce(popup) {
			return popup.getAttribute("data-show-once") === "1";
		}

		function hasPopupBeenClosed(popup) {
			var storageKey = getStorageKey(popup);
			if (!shouldShowOnlyOnce(popup) || !storageKey || !canUseStorage()) {
				return false;
			}

			try {
				return window.localStorage.getItem(storageKey) === "1";
			} catch (error) {
				return false;
			}
		}

		function markPopupClosed(popup) {
			var storageKey = getStorageKey(popup);
			if (!shouldShowOnlyOnce(popup) || !storageKey || !canUseStorage()) {
				return;
			}

			try {
				window.localStorage.setItem(storageKey, "1");
			} catch (error) {
				// Ignore storage write errors.
			}
		}

		function getInitialDelayMs(popup) {
			var raw = parseInt(popup.getAttribute("data-initial-delay-ms") || "0", 10);
			return Number.isFinite(raw) && raw > 0 ? raw : 0;
		}

		function getNextDelayMs(popup) {
			var raw = parseInt(popup.getAttribute("data-next-delay-ms") || "0", 10);
			return Number.isFinite(raw) && raw > 0 ? raw : 0;
		}

		function findNextPopupIndex(startIndex) {
			for (var i = startIndex + 1; i < popups.length; i++) {
				if (!hasPopupBeenClosed(popups[i])) {
					return i;
				}
			}
			return -1;
		}

		function openPopupAt(index) {
			var popup = popups[index];
			if (!popup) {
				return;
			}
			if (hasPopupBeenClosed(popup)) {
				var nextSkippedIndex = findNextPopupIndex(index);
				if (nextSkippedIndex !== -1) {
					openPopupAt(nextSkippedIndex);
				}
				return;
			}

			currentPopup = popup;
			currentIndex = index;
			isClosing = false;
			popup.classList.remove("is-closing");
			popup.hidden = false;
			window.requestAnimationFrame(function () {
				popup.classList.add("is-visible");
			});
			document.body.classList.add("bb-popup-open");
		}

		function closeCurrentPopup() {
			if (!currentPopup || currentPopup.hidden || isClosing) {
				return;
			}

			var closedPopup = currentPopup;
			var closedIndex = currentIndex;
			var nextDelayMs = getNextDelayMs(closedPopup);

			isClosing = true;
			closedPopup.classList.remove("is-visible");
			closedPopup.classList.add("is-closing");
			document.body.classList.remove("bb-popup-open");
			markPopupClosed(closedPopup);

			hideTimeout = window.setTimeout(function () {
				closedPopup.hidden = true;
				closedPopup.classList.remove("is-closing");

				var nextIndex = findNextPopupIndex(closedIndex);
				currentPopup = null;
				currentIndex = -1;
				isClosing = false;

				if (nextIndex === -1) {
					hideTimeout = null;
					return;
				}

				showTimeout = window.setTimeout(function () {
					openPopupAt(nextIndex);
					showTimeout = null;
				}, nextDelayMs);

				hideTimeout = null;
			}, closeAnimationMs);
		}

		var firstIndex = findNextPopupIndex(-1);
		if (firstIndex === -1) {
			return;
		}

		popups.forEach(function (popup) {
			popup.dataset.bbPopupInitialized = "1";
			var dialog = popup.querySelector(".bb-promo-popup__dialog");
			var closeControls = popup.querySelectorAll("[data-bb-popup-close]");

			closeControls.forEach(function (control) {
				control.addEventListener("click", function (event) {
					event.preventDefault();
					event.stopPropagation();
					if (currentPopup === popup) {
						closeCurrentPopup();
					}
				});
			});

			if (dialog) {
				dialog.addEventListener("click", function (event) {
					event.stopPropagation();
				});
			}
		});

		document.addEventListener("keydown", function (event) {
			if (event.key === "Escape" && currentPopup && !currentPopup.hidden) {
				closeCurrentPopup();
			}
		});

		var firstDelayMs = getInitialDelayMs(popups[firstIndex]);
		if (firstDelayMs > 0) {
			showTimeout = window.setTimeout(function () {
				openPopupAt(firstIndex);
				showTimeout = null;
			}, firstDelayMs);
		} else {
			openPopupAt(firstIndex);
		}

		window.addEventListener("beforeunload", function () {
			if (showTimeout) {
				window.clearTimeout(showTimeout);
			}
			if (hideTimeout) {
				window.clearTimeout(hideTimeout);
			}
		});
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", run);
	} else {
		run();
	}
	window.addEventListener("load", run, { once: true });
})();
