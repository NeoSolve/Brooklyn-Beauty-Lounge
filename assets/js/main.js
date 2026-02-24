"use strict";

// Theme entry point. Keep lightweight for now.

(function () {
	// Header: hide on scroll down, show on scroll up (site-wide)
	var header = document.querySelector(".bb-header");
	if (!header) return;

	var isHomePage = document.body.classList.contains("home");
	var hero = document.querySelector(".bb-hero");
	var lastScrollY = window.scrollY || 0;
	var ticking = false;

	function updateHeaderOffset() {
		document.documentElement.style.setProperty("--bb-header-height", header.offsetHeight + "px");
	}

	function updateHeader() {
		var scrollY = window.scrollY || 0;
		var scrollingDown = scrollY > lastScrollY;

		if (scrollY <= 10) {
			header.classList.remove("bb-header--hidden");
		} else if (scrollingDown) {
			header.classList.add("bb-header--hidden");
		} else {
			header.classList.remove("bb-header--hidden");
		}

		if (scrollY > 0) {
			header.classList.add("bb-header--scrolled");
		} else {
			header.classList.remove("bb-header--scrolled");
		}

		if (isHomePage) {
			var heroBottom = hero ? hero.offsetHeight : 0;
			if (scrollY >= heroBottom) {
				header.classList.add("bb-header--past-hero");
			} else {
				header.classList.remove("bb-header--past-hero");
			}
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

	var socialSyncRAF = 0;
	function syncSocialPosition() {
		if (!isHomePage || !hero) return;
		var videoWrap = hero.querySelector(".bb-hero__video-wrap");
		var headerInner = header.querySelector(".bb-header__inner");
		var socialBlock = header.querySelector(".bb-header__social");
		var firstIcon = header.querySelector(".bb-header__social-icon");
		if (!videoWrap || !headerInner || !socialBlock || !firstIcon) return;
		var iconInset = firstIcon.getBoundingClientRect().left - socialBlock.getBoundingClientRect().left;
		var offset = videoWrap.getBoundingClientRect().left - headerInner.getBoundingClientRect().left - iconInset;
		headerInner.style.setProperty("--social-left", offset + "px");
	}

	function requestSocialSync() {
		cancelAnimationFrame(socialSyncRAF);
		socialSyncRAF = requestAnimationFrame(syncSocialPosition);
	}

	updateHeaderOffset();
	updateHeader();
	syncSocialPosition();
	window.addEventListener("scroll", onScroll, { passive: true });
	window.addEventListener("resize", function () {
		updateHeaderOffset();
		requestSocialSync();
	});
})();
