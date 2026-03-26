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
		var tocPrimary = document.querySelector(".bb-blog-single-content__toc--primary");
		if (!tocPrimary) return;

		var tocFixed = document.querySelector(".bb-blog-single-content__toc--fixed");
		var article = document.querySelector(".bb-blog-single-content__article");
		var mobileMediaQuery = window.matchMedia("(max-width: 767px)");
		var tocLinks = Array.prototype.slice.call(
			document.querySelectorAll(".bb-blog-single-content__toc .bb-blog-single-content__toc-link")
		);
		var progressFills = document.querySelectorAll(".bb-blog-single-content__toc-progress-fill");

		if (!tocLinks.length) return;

		var sections = tocLinks
			.map(function (link) {
				var href = link.getAttribute("href") || "";
				if (!href || href.charAt(0) !== "#") return null;
				return document.getElementById(href.slice(1));
			})
			.filter(function (section, index, arr) {
				return !!section && arr.indexOf(section) === index;
			});

		if (!sections.length) return;

		var tocFixedToggle = tocFixed ? tocFixed.querySelector(".bb-blog-single-content__toc-toggle") : null;
		var tocFixedList = tocFixed ? tocFixed.querySelector(".bb-blog-single-content__toc-list") : null;

		function setActive(sectionId) {
			tocLinks.forEach(function (link) {
				var isActive = link.getAttribute("href") === "#" + sectionId;
				var item = link.closest(".bb-blog-single-content__toc-item");

				link.classList.toggle("is-active", isActive);
				if (item) item.classList.toggle("is-active", isActive);
			});
		}

		function setFixedBarVisible(visible) {
			if (!tocFixed || !tocFixedToggle || !tocFixedList) return;

			tocFixed.classList.toggle("is-visible", visible);
			tocFixed.setAttribute("aria-hidden", visible ? "false" : "true");

			if (!visible) {
				tocFixed.classList.add("is-collapsed");
				tocFixedToggle.setAttribute("aria-expanded", "false");
				tocFixedToggle.setAttribute(
					"aria-label",
					"Expand table of contents"
				);
			}
		}

		function setFixedCollapsed(collapsed) {
			if (!tocFixed || !tocFixedToggle || !tocFixedList) return;
			var isCollapsed = tocFixed.classList.contains("is-collapsed");
			if (collapsed === isCollapsed) return;

			tocFixed.classList.toggle("is-collapsed", collapsed);
			tocFixedToggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
			tocFixedToggle.setAttribute(
				"aria-label",
				collapsed ? "Expand table of contents" : "Collapse table of contents"
			);
		}

		function getStickyOffset() {
			var header = document.querySelector(".bb-header");
			return (header ? header.offsetHeight : 0) + 8;
		}

		function getAnchorOffset() {
			var header = document.querySelector(".bb-header");
			var base = (header ? header.offsetHeight : 0) + 24;
			if (!mobileMediaQuery.matches) {
				return base;
			}
			var tocForHeight = tocFixed && tocFixed.classList.contains("is-visible") ? tocFixed : tocPrimary;
			return base + tocForHeight.offsetHeight + 12;
		}

		function updateFixedBarVisibility() {
			if (!tocFixed || !tocFixedToggle || !tocFixedList) return;

			if (!mobileMediaQuery.matches) {
				setFixedBarVisible(false);
				return;
			}

			var rect = tocPrimary.getBoundingClientRect();
			var vh = window.innerHeight || document.documentElement.clientHeight;

			// Entirely below the viewport — user has not scrolled to the TOC yet.
			if (rect.top >= vh) {
				setFixedBarVisible(false);
				return;
			}

			// Entirely above the viewport — user scrolled past the TOC upward.
			var scrolledPastUpward = rect.bottom <= 0;

			setFixedBarVisible(scrolledPastUpward);
		}

		if (tocFixedToggle) {
			tocFixedToggle.addEventListener("click", function () {
				setFixedCollapsed(!tocFixed.classList.contains("is-collapsed"));
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
			if (!article || !progressFills.length) return;

			var header = document.querySelector(".bb-header");
			var headerOffset = (header ? header.offsetHeight : 0) + 24;
			var articleRect = article.getBoundingClientRect();
			var articleTop = articleRect.top + window.pageYOffset - headerOffset;
			var articleHeight = article.offsetHeight;
			var maxScrollable = Math.max(1, articleHeight - window.innerHeight * 0.45);
			var progress = (window.pageYOffset - articleTop) / maxScrollable;
			var widthPct = Math.max(0, Math.min(1, progress)) * 100 + "%";

			Array.prototype.forEach.call(progressFills, function (fill) {
				fill.style.width = widthPct;
			});
		}

		var progressTicking = false;
		function onScrollProgress() {
			if (progressTicking) return;
			progressTicking = true;
			window.requestAnimationFrame(function () {
				updateProgress();
				updateFixedBarVisibility();
				progressTicking = false;
			});
		}

		function onResize() {
			updateProgress();
			updateFixedBarVisibility();
		}

		window.addEventListener("scroll", onScrollProgress, { passive: true });
		window.addEventListener("resize", onResize);

		if (typeof mobileMediaQuery.addEventListener === "function") {
			mobileMediaQuery.addEventListener("change", function () {
				updateFixedBarVisibility();
			});
		} else if (typeof mobileMediaQuery.addListener === "function") {
			mobileMediaQuery.addListener(function () {
				updateFixedBarVisibility();
			});
		}

		setActive(sections[0].id);
		updateFixedBarVisibility();
		updateProgress();
	}

	initShareActions();
	initToc();
})();
