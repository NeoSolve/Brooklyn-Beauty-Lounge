"use strict";

(function () {
	var section = document.querySelector("[data-faq]");
	if (!section) return;

	var items = Array.prototype.slice.call(section.querySelectorAll("[data-faq-item]"));
	if (!items.length) return;

	var mobileQuery = window.matchMedia("(max-width: 1024px)");

	function getPanel(item) {
		return item.querySelector("[data-faq-panel]");
	}

	function getTrigger(item) {
		return item.querySelector("[data-faq-trigger]");
	}

	function openItem(item, instant) {
		var panel = getPanel(item);
		var trigger = getTrigger(item);
		if (!panel || !trigger) return;

		item.classList.add("is-open");
		trigger.setAttribute("aria-expanded", "true");
		panel.hidden = false;

		if (!mobileQuery.matches) {
			panel.style.maxHeight = "";
			return;
		}

		var targetHeight = panel.scrollHeight + "px";
		if (instant) {
			panel.style.maxHeight = targetHeight;
			return;
		}

		panel.style.maxHeight = "0px";
		requestAnimationFrame(function () {
			panel.style.maxHeight = targetHeight;
		});
	}

	function closeItem(item, instant) {
		var panel = getPanel(item);
		var trigger = getTrigger(item);
		if (!panel || !trigger) return;

		item.classList.remove("is-open");
		trigger.setAttribute("aria-expanded", "false");

		if (!mobileQuery.matches) {
			panel.style.maxHeight = "";
			panel.hidden = false;
			return;
		}

		if (instant) {
			panel.style.maxHeight = "0px";
			return;
		}

		panel.style.maxHeight = panel.scrollHeight + "px";
		requestAnimationFrame(function () {
			panel.style.maxHeight = "0px";
		});
	}

	function applyModeState() {
		if (!mobileQuery.matches) {
			items.forEach(function (item) {
				openItem(item, true);
			});
			return;
		}

		var hasOpen = false;
		items.forEach(function (item) {
			if (item.classList.contains("is-open") && !hasOpen) {
				openItem(item, true);
				hasOpen = true;
				return;
			}

			closeItem(item, true);
		});

		if (!hasOpen) {
			openItem(items[0], true);
		}
	}

	items.forEach(function (item) {
		var trigger = getTrigger(item);
		if (!trigger) return;

		trigger.addEventListener("click", function () {
			if (!mobileQuery.matches) return;

			var isOpen = item.classList.contains("is-open");
			items.forEach(function (otherItem) {
				if (otherItem !== item) {
					closeItem(otherItem, false);
				}
			});

			if (isOpen) {
				closeItem(item, false);
			} else {
				openItem(item, false);
			}
		});
	});

	section.addEventListener("transitionend", function (event) {
		if (!mobileQuery.matches) return;
		if (!(event.target instanceof HTMLElement)) return;
		if (!event.target.matches("[data-faq-panel]")) return;

		if (event.target.style.maxHeight === "0px") {
			event.target.hidden = true;
		}
	});

	mobileQuery.addEventListener("change", applyModeState);
	window.addEventListener("resize", applyModeState);

	applyModeState();
})();
