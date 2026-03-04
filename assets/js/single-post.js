"use strict";

(function () {
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
					window.setTimeout(function () {
						link.classList.remove("is-copied");
					}, 1400);
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

		var progressTicking = false;
		function onScrollProgress() {
			if (progressTicking) return;

			progressTicking = true;
			window.requestAnimationFrame(function () {
				updateProgress();
				progressTicking = false;
			});
		}

		window.addEventListener("scroll", onScrollProgress, { passive: true });
		window.addEventListener("resize", updateProgress);

		setActive(sections[0].id);
		updateProgress();
	}

	initShareActions();
	initToc();
})();
