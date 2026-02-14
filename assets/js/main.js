"use strict";

// Theme entry point. Keep lightweight for now.

(function () {
	// Header: hide on scroll down, show on scroll up (home only)
	var header = document.querySelector(".bb-header");
	if (!header || !document.body.classList.contains("home")) return;

	var hero = document.querySelector(".bb-hero");
	var lastScrollY = window.scrollY || 0;
	var ticking = false;

	function updateHeader() {
		var scrollY = window.scrollY || 0;
		if (scrollY > lastScrollY) {
			header.classList.add("bb-header--hidden");
		} else {
			header.classList.remove("bb-header--hidden");
		}
		if (scrollY > 0) {
			header.classList.add("bb-header--scrolled");
		} else {
			header.classList.remove("bb-header--scrolled");
		}
		var heroBottom = hero ? hero.offsetHeight : 0;
		if (scrollY >= heroBottom) {
			header.classList.add("bb-header--past-hero");
		} else {
			header.classList.remove("bb-header--past-hero");
		}
		lastScrollY = scrollY;
		ticking = false;
	}

	function onScroll() {
		if (!ticking) {
			requestAnimationFrame(updateHeader);
			ticking = true;
		}
	}

	updateHeader();
	window.addEventListener("scroll", onScroll, { passive: true });
})();
