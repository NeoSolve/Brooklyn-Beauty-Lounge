"use strict";

// Theme entry point. Keep lightweight for now.

(function () {
	function normalizeHost(hostname) {
		var host = (hostname || "").toLowerCase();
		return host.indexOf("www.") === 0 ? host.slice(4) : host;
	}

	function mergeRel(existingRel, requiredTokens) {
		var relTokens = (existingRel || "")
			.toLowerCase()
			.split(/\s+/)
			.filter(Boolean);

		requiredTokens.forEach(function (token) {
			if (relTokens.indexOf(token) === -1) {
				relTokens.push(token);
			}
		});

		return relTokens.join(" ");
	}

	function shouldSkipHref(href) {
		if (!href) return true;

		var trimmedHref = href.trim();
		if (!trimmedHref) return true;

		return (
			trimmedHref.charAt(0) === "#" ||
			trimmedHref.indexOf("/") === 0 ||
			trimmedHref.indexOf("?") === 0 ||
			trimmedHref.indexOf("mailto:") === 0 ||
			trimmedHref.indexOf("tel:") === 0 ||
			trimmedHref.indexOf("javascript:") === 0
		);
	}

	function makeExternalLinksOpenInNewTab() {
		var siteHost = normalizeHost(window.location.hostname);
		var requiredRelTokens = ["nofollow"];
		var links = document.querySelectorAll("a[href]");

		links.forEach(function (link) {
			if (link.hasAttribute("download")) return;

			var rawHref = link.getAttribute("href");
			if (shouldSkipHref(rawHref)) return;

			var parsedUrl;
			try {
				parsedUrl = new URL(rawHref, window.location.origin);
			} catch (error) {
				return;
			}

			if (parsedUrl.protocol !== "http:" && parsedUrl.protocol !== "https:") return;

			var linkHost = normalizeHost(parsedUrl.hostname);
			var isInternal = linkHost === siteHost || linkHost.endsWith("." + siteHost);
			if (isInternal) return;

			link.setAttribute("target", "_blank");
			link.setAttribute("rel", mergeRel(link.getAttribute("rel"), requiredRelTokens));
		});
	}

	makeExternalLinksOpenInNewTab();

	// Header: fixed, no hide on scroll
	var header = document.querySelector(".bb-header");
	if (!header) return;

	var isHomePage = document.body.classList.contains("home");
	var hero = document.querySelector(".bb-hero");
	var footer = document.querySelector(".bb-footer");
	var ticking = false;

	function updateHeaderOffset() {
		document.documentElement.style.setProperty("--bb-header-height", header.offsetHeight + "px");
	}

	function updateHeader() {
		var scrollY = window.scrollY || 0;

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

		if (footer) {
			var footerRect = footer.getBoundingClientRect();
			var headerHeight = header.offsetHeight;
			if (footerRect.top <= headerHeight) {
				header.classList.add("bb-header--over-footer");
			} else {
				header.classList.remove("bb-header--over-footer");
			}
		}

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

	var burgerButton = header.querySelector("[data-header-burger]");
	var mobileNav = header.querySelector(".bb-header__nav");
	var menuLinkedElements = header.querySelectorAll(".bb-header__nav a, .bb-header__meta a, .bb-header__social a, .bb-header__block--right > .btn");

	function setMobileMenuState(isOpen) {
		header.classList.toggle("bb-header--menu-open", isOpen);
		document.documentElement.classList.toggle("bb-mobile-menu-open", isOpen);
		document.body.classList.toggle("bb-mobile-menu-open", isOpen);
		if (burgerButton) {
			burgerButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
			burgerButton.setAttribute("aria-label", isOpen ? "Close menu" : "Open menu");
		}
		if (mobileNav) {
			mobileNav.setAttribute("aria-hidden", isOpen ? "false" : "true");
		}
	}

	function closeMobileMenu() {
		setMobileMenuState(false);
	}

	if (burgerButton && mobileNav) {
		setMobileMenuState(false);

		burgerButton.addEventListener("click", function () {
			setMobileMenuState(!header.classList.contains("bb-header--menu-open"));
		});

		menuLinkedElements.forEach(function (link) {
			link.addEventListener("click", closeMobileMenu);
		});

		document.addEventListener("keydown", function (event) {
			if (event.key === "Escape") {
				closeMobileMenu();
			}
		});
	}

	updateHeaderOffset();
	updateHeader();
	syncSocialPosition();
	window.addEventListener("scroll", onScroll, { passive: true });
	window.addEventListener("resize", function () {
		updateHeaderOffset();
		requestSocialSync();
		if (window.innerWidth > 980) {
			closeMobileMenu();
		}
	});
})();
