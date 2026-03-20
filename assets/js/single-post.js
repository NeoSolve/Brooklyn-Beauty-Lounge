"use strict";

(function () {
	var activeToastTimer = null;

	function showCopyToast() {
		var toast = document.querySelector(".bb-copy-toast");

		if (!toast) {
			toast = document.createElement("div");
			toast.className = "bb-copy-toast";
			toast.setAttribute("role", "status");
			toast.setAttribute("aria-live", "polite");
			toast.textContent = "Link copied!";
			document.body.appendChild(toast);
		}

		if (activeToastTimer) {
			clearTimeout(activeToastTimer);
		}

		toast.classList.remove("is-visible");
		void toast.offsetWidth;
		toast.classList.add("is-visible");

		activeToastTimer = window.setTimeout(function () {
			toast.classList.remove("is-visible");
			activeToastTimer = null;
		}, 2500);
	}

	function initShareActions() {
		var shareCopyLinks = Array.prototype.slice.call(
			document.querySelectorAll('[data-share-action="copy"][data-share-url]')
		);

		if (!shareCopyLinks.length) return;

		shareCopyLinks.forEach(function (link) {
			link.addEventListener("click", function (event) {
				event.preventDefault();

				var shareUrl = link.getAttribute("data-share-url") || window.location.href;
				var canUseClipboard = navigator.clipboard && typeof navigator.clipboard.writeText === "function";

				function markCopied() {
					link.classList.add("is-copied");
					showCopyToast();
					window.setTimeout(function () {
						link.classList.remove("is-copied");
					}, 2400);
				}

				if (canUseClipboard) {
					navigator.clipboard
						.writeText(shareUrl)
						.then(markCopied)
						.catch(function () {});
					return;
				}

				var tempInput = document.createElement("input");
				tempInput.type = "text";
				tempInput.value = shareUrl;
				document.body.appendChild(tempInput);
				tempInput.select();

				try {
					document.execCommand("copy");
					markCopied();
				} catch (error) {
					// Legacy fallback can fail silently.
				}

				document.body.removeChild(tempInput);
			});
		});
	}

	function initToc() {
		var toc = document.querySelector(".bb-blog-single-content__toc");
		if (!toc) return;

		var article = document.querySelector(".bb-blog-single-content__article");
		var progressFill = toc.querySelector(".bb-blog-single-content__toc-progress-fill");
		var tocToggle = toc.querySelector(".bb-blog-single-content__toc-toggle");
		var mobileMediaQuery = window.matchMedia("(max-width: 767px)");
		var tocLinks = Array.prototype.slice.call(
			toc.querySelectorAll(".bb-blog-single-content__toc-link")
		);

		if (!tocLinks.length) return;

		var sections = tocLinks
			.map(function (link) {
				var href = link.getAttribute("href") || "";
				if (!href || href.charAt(0) !== "#") return null;
				return document.getElementById(href.slice(1));
			})
			.filter(function (section) {
				return !!section;
			});

		if (!sections.length) return;

		function setActive(sectionId) {
			tocLinks.forEach(function (link) {
				var isActive = link.getAttribute("href") === "#" + sectionId;
				var item = link.closest(".bb-blog-single-content__toc-item");

				link.classList.toggle("is-active", isActive);
				if (item) item.classList.toggle("is-active", isActive);
			});
		}

		var tocList = toc.querySelector(".bb-blog-single-content__toc-list");
		var tocPlaceholder = document.createElement("div");
		var fixedThresholdY = 0;

		tocPlaceholder.className = "bb-blog-single-content__toc-placeholder";
		tocPlaceholder.setAttribute("aria-hidden", "true");
		tocPlaceholder.style.display = "none";
		tocPlaceholder.style.height = "0px";
		toc.parentNode.insertBefore(tocPlaceholder, toc.nextSibling);

		function getStickyOffset() {
			var header = document.querySelector(".bb-header");
			return (header ? header.offsetHeight : 0) + 8;
		}

		function getAnchorOffset() {
			var header = document.querySelector(".bb-header");
			var base = (header ? header.offsetHeight : 0) + 24;
			if (mobileMediaQuery.matches) {
				return base + toc.offsetHeight + 12;
			}
			return base;
		}

		function updateTocMetrics() {
			if (!mobileMediaQuery.matches || toc.classList.contains("is-sticky")) return;
			var stickyOffset = getStickyOffset();
			var tocRect = toc.getBoundingClientRect();
			fixedThresholdY = Math.max(0, tocRect.top + window.pageYOffset - stickyOffset);
		}

		function setCollapsed(collapsed) {
			if (!tocToggle || !tocList) return;
			var isCollapsed = toc.classList.contains("is-collapsed");
			if (collapsed === isCollapsed) return;

			toc.classList.toggle("is-collapsed", collapsed);
			tocToggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
			tocToggle.setAttribute(
				"aria-label",
				collapsed ? "Expand table of contents" : "Collapse table of contents"
			);

			if (collapsed) {
				tocList.style.maxHeight = tocList.scrollHeight + "px";
				void tocList.offsetHeight;
				tocList.style.maxHeight = "0px";
			} else {
				tocList.style.maxHeight = tocList.scrollHeight + "px";
				var onEnd = function () {
					tocList.removeEventListener("transitionend", onEnd);
					if (!toc.classList.contains("is-collapsed")) {
						tocList.style.maxHeight = "none";
					}
				};
				tocList.addEventListener("transitionend", onEnd);
			}
		}

		function setFixed(fixed) {
			if (!mobileMediaQuery.matches) fixed = false;
			if (fixed === toc.classList.contains("is-sticky")) return;

			if (fixed) {
				var tocRect = toc.getBoundingClientRect();
				tocPlaceholder.style.display = "block";
				tocPlaceholder.style.height = toc.offsetHeight + "px";
				toc.style.left = tocRect.left + "px";
				toc.style.width = tocRect.width + "px";
				toc.style.top = getStickyOffset() + "px";
				toc.classList.add("is-sticky");
				return;
			}

			toc.classList.remove("is-sticky");
			toc.style.left = "";
			toc.style.width = "";
			toc.style.top = "";
			tocPlaceholder.style.display = "none";
			tocPlaceholder.style.height = "0px";
			updateTocMetrics();
		}

		if (tocToggle) {
			tocToggle.addEventListener("click", function () {
				setCollapsed(!toc.classList.contains("is-collapsed"));
			});
		}

		tocLinks.forEach(function (link) {
			link.addEventListener("click", function (event) {
				var href = link.getAttribute("href") || "";
				if (!href || href.charAt(0) !== "#") return;

				var target = document.querySelector(href);
				if (!target) return;

				event.preventDefault();

				var offset = getAnchorOffset();
				var top = target.getBoundingClientRect().top + window.pageYOffset - offset;

				window.scrollTo({ top: top, behavior: "smooth" });
				if (window.history && typeof window.history.replaceState === "function") {
					window.history.replaceState(null, "", href);
				}
			});
		});

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						setActive(entry.target.id);
					}
				});
			},
			{
				rootMargin: "-30% 0px -60% 0px",
				threshold: 0,
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});

		function updateProgress() {
			if (!article || !progressFill) return;

			var header = document.querySelector(".bb-header");
			var headerOffset = (header ? header.offsetHeight : 0) + 24;
			var articleRect = article.getBoundingClientRect();
			var articleTop = articleRect.top + window.pageYOffset - headerOffset;
			var articleHeight = article.offsetHeight;
			var maxScrollable = Math.max(1, articleHeight - window.innerHeight * 0.45);
			var progress = (window.pageYOffset - articleTop) / maxScrollable;

			progressFill.style.width = Math.max(0, Math.min(1, progress)) * 100 + "%";
		}

		function updateStickyState() {
			if (!mobileMediaQuery.matches) {
				setFixed(false);
				return;
			}
			updateTocMetrics();
			setFixed(window.pageYOffset >= fixedThresholdY);
		}

		var progressTicking = false;
		function onScrollProgress() {
			if (progressTicking) return;
			progressTicking = true;
			window.requestAnimationFrame(function () {
				updateProgress();
				updateStickyState();
				progressTicking = false;
			});
		}

		window.addEventListener("scroll", onScrollProgress, { passive: true });
		window.addEventListener("resize", function () {
			updateProgress();
			updateStickyState();
		});

		if (typeof mobileMediaQuery.addEventListener === "function") {
			mobileMediaQuery.addEventListener("change", function () {
				setFixed(false);
				updateStickyState();
			});
		} else if (typeof mobileMediaQuery.addListener === "function") {
			mobileMediaQuery.addListener(function () {
				setFixed(false);
				updateStickyState();
			});
		}

		setActive(sections[0].id);

		if (mobileMediaQuery.matches) {
			toc.classList.add("is-collapsed");
			if (tocToggle) {
				tocToggle.setAttribute("aria-expanded", "false");
				tocToggle.setAttribute("aria-label", "Expand table of contents");
			}
			if (tocList) tocList.style.maxHeight = "0px";
		} else {
			if (tocList) tocList.style.maxHeight = "none";
		}

		updateTocMetrics();
		updateProgress();
		updateStickyState();
	}

	initShareActions();
	initToc();
})();
