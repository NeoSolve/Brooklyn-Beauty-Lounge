"use strict";

(function () {
	var toc = document.querySelector(".bb-blog-single-content__toc");
	if (!toc) return;

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

	setActive(sections[0].id);
})();
