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
		var lastScrollY = window.pageYOffset;
		var collapseThresholdY = 0;
		var fixedThresholdY = 0;
		var manualExpandedWhileFixed = false;

		tocPlaceholder.className = "bb-blog-single-content__toc-placeholder";
		tocPlaceholder.setAttribute("aria-hidden", "true");
		tocPlaceholder.style.display = "none";
		tocPlaceholder.style.height = "0px";
		toc.parentNode.insertBefore(tocPlaceholder, toc.nextSibling);

		function getStickyOffset() {
			var header = document.querySelector(".bb-header");
			return (header ? header.offsetHeight : 0) + 8;
		}

		function syncPlaceholderHeight() {
			if (!mobileMediaQuery.matches || !toc.classList.contains("is-sticky")) return;
			tocPlaceholder.style.height = toc.offsetHeight + "px";
		}

		function updateTocMetrics() {
			if (!mobileMediaQuery.matches || toc.classList.contains("is-sticky")) return;

			var stickyOffset = getStickyOffset();
			var tocRect = toc.getBoundingClientRect();
			var tocTop = tocRect.top + window.pageYOffset;

			fixedThresholdY = Math.max(0, tocTop - stickyOffset);
			collapseThresholdY = Math.max(0, fixedThresholdY - 36);
		}

		function getCollapsedListHeight() {
			return 0;
		}

		function getExpandedListHeight() {
			if (!tocList) return 0;
			var currentMaxHeight = tocList.style.maxHeight;
			tocList.style.maxHeight = "none";
			var fullHeight = tocList.scrollHeight;
			tocList.style.maxHeight = currentMaxHeight;
			return fullHeight;
		}

		function setCollapseProgress(progress, animate) {
			if (!tocToggle || !tocList) return;

			var clampedProgress = Math.max(0, Math.min(1, progress));
			var expandedHeight = getExpandedListHeight();
			var collapsedHeight = getCollapsedListHeight();
			var targetHeight =
				expandedHeight - (expandedHeight - collapsedHeight) * clampedProgress;
			var isCollapsed = clampedProgress >= 0.999;

			toc.classList.toggle("is-collapsed", isCollapsed);
			tocToggle.setAttribute("aria-expanded", isCollapsed ? "false" : "true");
			tocToggle.setAttribute(
				"aria-label",
				isCollapsed ? "Expand table of contents" : "Collapse table of contents"
			);

			tocList.style.transition = animate ? "" : "none";
			tocList.style.maxHeight =
				clampedProgress <= 0.001 ? "none" : Math.max(collapsedHeight, targetHeight) + "px";

			if (!animate) {
				void tocList.offsetHeight;
				tocList.style.transition = "";
			}

			window.requestAnimationFrame(syncPlaceholderHeight);
		}

		function setCollapsed(collapsed) {
			if (!tocToggle || !tocList) return;

			var expandedHeight = getExpandedListHeight();
			var collapsedHeight = getCollapsedListHeight();
			var fromHeight = tocList.offsetHeight;
			var toHeight = collapsed ? collapsedHeight : expandedHeight;

			toc.classList.toggle("is-collapsed", collapsed);
			tocToggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
			tocToggle.setAttribute(
				"aria-label",
				collapsed ? "Expand table of contents" : "Collapse table of contents"
			);

			tocList.style.transition = "none";
			tocList.style.maxHeight = fromHeight + "px";
			void tocList.offsetHeight;
			tocList.style.transition = "";
			tocList.style.maxHeight = (toHeight || 0) + "px";

			if (!collapsed) {
				var onEnd = function () {
					tocList.removeEventListener("transitionend", onEnd);
					if (!toc.classList.contains("is-collapsed")) {
						tocList.style.maxHeight = "none";
					}
					syncPlaceholderHeight();
				};
				tocList.addEventListener("transitionend", onEnd);
			} else {
				window.requestAnimationFrame(syncPlaceholderHeight);
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
				window.requestAnimationFrame(syncPlaceholderHeight);
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
				var isCollapsed = toc.classList.contains("is-collapsed");
				var nextCollapsed = !isCollapsed;

				if (toc.classList.contains("is-sticky")) {
					manualExpandedWhileFixed = !nextCollapsed;
				} else if (!nextCollapsed) {
					manualExpandedWhileFixed = false;
				}
				setCollapsed(nextCollapsed);
			});
		}

		tocLinks.forEach(function (link) {
			link.addEventListener("click", function (event) {
				var href = link.getAttribute("href") || "";
				if (!href || href.charAt(0) !== "#") return;

				var target = document.querySelector(href);
				if (!target) return;

				event.preventDefault();

				var header = document.querySelector(".bb-header");
				var offset = (header ? header.offsetHeight : 0) + 24;
				var top = target.getBoundingClientRect().top + window.pageYOffset - offset;

				window.scrollTo({ top: top, behavior: "smooth" });
				if (window.history && typeof window.history.replaceState === "function") {
					window.history.replaceState(null, "", href);
				}

				if (mobileMediaQuery.matches) {
					manualExpandedWhileFixed = false;
					setCollapsed(true);
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
			var clampedProgress = Math.max(0, Math.min(1, progress));

			progressFill.style.width = clampedProgress * 100 + "%";
		}

		function updateStickyState() {
			var isMobile = mobileMediaQuery.matches;
			var currentScrollY = window.pageYOffset;
			var isScrollingDown = currentScrollY > lastScrollY;

			if (!isMobile) {
				setFixed(false);
				manualExpandedWhileFixed = false;
				setCollapseProgress(0, false);
				lastScrollY = currentScrollY;
				return;
			}

			updateTocMetrics();

			var collapseRange = Math.max(1, fixedThresholdY - collapseThresholdY);
			var collapseProgress = (currentScrollY - collapseThresholdY) / collapseRange;
			var shouldFix = currentScrollY >= fixedThresholdY;

			if (!manualExpandedWhileFixed && (isScrollingDown || currentScrollY < fixedThresholdY)) {
				setCollapseProgress(collapseProgress, false);
			}

			if (shouldFix) {
				if (!manualExpandedWhileFixed) {
					setCollapseProgress(1, false);
				}
				setFixed(true);
			} else {
				manualExpandedWhileFixed = false;
				setFixed(false);
			}

			lastScrollY = currentScrollY;
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
			syncPlaceholderHeight();
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
		if (tocList) tocList.style.maxHeight = "none";
		updateTocMetrics();
		updateProgress();
		updateStickyState();
	}

	initShareActions();
	initToc();
})();
