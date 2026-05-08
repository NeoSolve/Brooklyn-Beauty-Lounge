"use strict";

// Theme entry point. Keep lightweight for now.

(function () {
	function normalizeHost(hostname) {
		var host = (hostname || "").toLowerCase();
		return host.indexOf("www.") === 0 ? host.slice(4) : host;
	}

	function shouldOpenExternalHostInNewTab(hostname) {
		var host = normalizeHost(hostname);

		if (!host) return false;

		return true;
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

			if (!shouldOpenExternalHostInNewTab(linkHost)) {
				if (link.getAttribute("target") === "_blank") {
					link.removeAttribute("target");
				}
				link.setAttribute("rel", mergeRel(link.getAttribute("rel"), ["nofollow"]));
				return;
			}

			link.setAttribute("target", "_blank");
			link.setAttribute(
				"rel",
				mergeRel(link.getAttribute("rel"), ["nofollow", "noopener", "noreferrer"])
			);
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
		var socialBlock = header.querySelector(".bb-header__block--right .bb-header__social");
		if (!socialBlock) return;
		var firstIcon = socialBlock.querySelector(".bb-header__social-icon");
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
	var mobileNav = document.querySelector(".bb-mobile-nav");
	var mobileCloseButton = mobileNav ? mobileNav.querySelector("[data-mobile-close]") : null;
	var mobileSubmenuToggles = [];

	(function buildMobilePanel() {
		if (!mobileNav) return;

		var navHeader = mobileNav.querySelector(".bb-mobile-nav__header");

		var meta = header.querySelector(".bb-header__meta");
		if (meta) {
			var metaClone = meta.cloneNode(true);
			metaClone.className = "bb-mobile-meta";
			metaClone.removeAttribute("style");
			var insertRef = navHeader ? navHeader.nextSibling : mobileNav.firstChild;
			mobileNav.insertBefore(metaClone, insertRef);
		}

		var social = header.querySelector(".bb-header__social");
		if (social) {
			var socialClone = social.cloneNode(true);
			socialClone.className = "bb-mobile-social";
			socialClone.removeAttribute("aria-label");
			socialClone.removeAttribute("style");
			mobileNav.appendChild(socialClone);
		}

		var cta = header.querySelector(".bb-header__block--right > .btn");
		if (cta) {
			var ctaClone = cta.cloneNode(true);
			ctaClone.classList.add("bb-mobile-cta");
			mobileNav.appendChild(ctaClone);
		}
	})();

	function initMobileSubmenus() {
		if (!mobileNav) return;

		mobileSubmenuToggles = [];

		var parentItems = mobileNav.querySelectorAll(".bb-mobile-nav__menu .menu-item-has-children");
		parentItems.forEach(function (item, index) {
			var submenu = item.querySelector(":scope > .sub-menu");
			if (!submenu) return;

			submenu.classList.remove("is-open");
			item.classList.remove("is-open");
			submenu.style.maxHeight = "0px";

			var existingToggle = item.querySelector(":scope > .bb-mobile-nav__submenu-toggle");
			if (existingToggle) {
				existingToggle.remove();
			}

			var toggle = document.createElement("button");
			var submenuId = "bb-mobile-submenu-" + index;
			toggle.type = "button";
			toggle.className = "bb-mobile-nav__submenu-toggle";
			toggle.setAttribute("aria-expanded", "false");
			toggle.setAttribute("aria-controls", submenuId);
			toggle.setAttribute("aria-label", "Toggle submenu");

			submenu.id = submenuId;
			submenu.setAttribute("aria-hidden", "true");

			toggle.addEventListener("click", function (event) {
				event.preventDefault();
				var isOpen = toggle.getAttribute("aria-expanded") === "true";
				toggle.setAttribute("aria-expanded", isOpen ? "false" : "true");

				if (isOpen) {
					submenu.style.maxHeight = submenu.scrollHeight + "px";
					submenu.classList.remove("is-open");
					item.classList.remove("is-open");
					/* force reflow before collapsing to animate close reliably */
					submenu.offsetHeight;
					submenu.style.maxHeight = "0px";
					submenu.setAttribute("aria-hidden", "true");
					return;
				}

				submenu.classList.add("is-open");
				item.classList.add("is-open");
				submenu.setAttribute("aria-hidden", "false");
				submenu.style.maxHeight = "0px";
				/* force reflow before expanding to animate open reliably */
				submenu.offsetHeight;
				submenu.style.maxHeight = submenu.scrollHeight + "px";
			});

			item.insertBefore(toggle, submenu);
			mobileSubmenuToggles.push(toggle);
		});
	}

	function isMobileMenuOpen() {
		return mobileNav && mobileNav.classList.contains("is-open");
	}

	function setMobileMenuState(isOpen) {
		if (!mobileNav) return;
		if (isOpen) {
			document.documentElement.classList.add("bb-mobile-menu-open");
			document.body.classList.add("bb-mobile-menu-open");
			mobileNav.classList.add("is-open");
		} else {
			/* Сначала снимаем фон с html/body, пока оверлей ещё перекрывает экран — иначе в Safari
			   safe-area на кадр остаётся «кремовой» после скрытия меню */
			document.documentElement.classList.remove("bb-mobile-menu-open");
			document.body.classList.remove("bb-mobile-menu-open");
			mobileNav.classList.remove("is-open");
		}
		if (burgerButton) {
			burgerButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
			burgerButton.setAttribute("aria-label", isOpen ? "Close menu" : "Open menu");
		}
		mobileNav.setAttribute("aria-hidden", isOpen ? "false" : "true");
	}

	function closeMobileMenu() {
		setMobileMenuState(false);
		mobileSubmenuToggles.forEach(function (toggle) {
			var submenuId = toggle.getAttribute("aria-controls");
			var submenu = submenuId ? mobileNav.querySelector("#" + submenuId) : null;
			toggle.setAttribute("aria-expanded", "false");
			if (submenu) {
				submenu.classList.remove("is-open");
				var parentItem = toggle.closest(".menu-item-has-children");
				if (parentItem) {
					parentItem.classList.remove("is-open");
				}
				submenu.style.maxHeight = "0px";
				submenu.setAttribute("aria-hidden", "true");
			}
		});
	}

	if (burgerButton && mobileNav) {
		initMobileSubmenus();
		setMobileMenuState(false);

		burgerButton.addEventListener("click", function () {
			setMobileMenuState(!isMobileMenuOpen());
		});

		if (mobileCloseButton) {
			mobileCloseButton.addEventListener("click", closeMobileMenu);
		}

		var menuLinkedElements = mobileNav.querySelectorAll("a, .bb-mobile-cta");
		menuLinkedElements.forEach(function (link) {
			link.addEventListener("click", function (event) {
				var parentItem = link.closest(".menu-item-has-children");
				var submenu = parentItem ? parentItem.querySelector(":scope > .sub-menu") : null;
				var href = (link.getAttribute("href") || "").trim();

				if (submenu && (href === "" || href === "#")) {
					event.preventDefault();
					var toggleButton = parentItem.querySelector(":scope > .bb-mobile-nav__submenu-toggle");
					if (toggleButton) {
						toggleButton.click();
					}
					return;
				}

				closeMobileMenu();
			});
		});

		document.addEventListener("keydown", function (event) {
			if (event.key === "Escape") {
				closeMobileMenu();
			}
		});
	}

	/* ------------------------------------------------------------------ */
	/* Desktop dropdown                                                   */
	/* JS owns the open/close state for two reasons:                      */
	/*   1. Safari has invalidation bugs with `:has(:hover)` so the       */
	/*      header background/colors couldn't reliably react to a hovered */
	/*      child via CSS alone.                                          */
	/*   2. We want a small close-buffer, Esc/outside-click handling and  */
	/*      proper aria-expanded semantics.                               */
	/* The CSS still keeps :hover / :focus-within selectors as a no-JS    */
	/* fallback for opening the panel itself.                             */
	/* ------------------------------------------------------------------ */
	var desktopMenu = header.querySelector(".bb-header__menu");
	var desktopParentItems = desktopMenu
		? Array.prototype.slice.call(desktopMenu.querySelectorAll(":scope > .menu-item-has-children"))
		: [];

	if (desktopParentItems.length) {
		var DROPDOWN_CLOSE_DELAY = 120;
		var HEADER_RESET_DELAY = 420;
		var dropdownCloseTimers = new WeakMap();
		var headerResetTimer = 0;
		var desktopMQ = window.matchMedia("(min-width: 981px)");

		function isDesktopViewport() {
			return desktopMQ.matches;
		}

		function clearCloseTimer(item) {
			var t = dropdownCloseTimers.get(item);
			if (t) {
				clearTimeout(t);
				dropdownCloseTimers.delete(item);
			}
		}

		function refreshHeaderState() {
			var anyOpen = desktopParentItems.some(function (it) {
				return it.classList.contains("is-open");
			});
			if (anyOpen) {
				clearTimeout(headerResetTimer);
				header.classList.add("bb-header--submenu-open");
			} else {
				headerResetTimer = setTimeout(function () {
					header.classList.remove("bb-header--submenu-open");
				}, HEADER_RESET_DELAY);
			}
		}

		function setItemOpen(item, isOpen) {
			item.classList.toggle("is-open", isOpen);
			var topLink = item.querySelector(":scope > a");
			if (topLink) {
				topLink.setAttribute("aria-expanded", isOpen ? "true" : "false");
			}
		}

		function openDropdown(item) {
			if (!isDesktopViewport()) return;
			clearCloseTimer(item);
			desktopParentItems.forEach(function (other) {
				if (other !== item && other.classList.contains("is-open")) {
					clearCloseTimer(other);
					setItemOpen(other, false);
				}
			});
			setItemOpen(item, true);
			refreshHeaderState();
		}

		function closeDropdown(item, immediate) {
			clearCloseTimer(item);
			var doClose = function () {
				setItemOpen(item, false);
				refreshHeaderState();
			};
			if (immediate) {
				doClose();
			} else {
				dropdownCloseTimers.set(item, setTimeout(doClose, DROPDOWN_CLOSE_DELAY));
			}
		}

		function closeAllDropdowns(immediate) {
			desktopParentItems.forEach(function (item) {
				closeDropdown(item, immediate);
			});
			if (immediate) {
				clearTimeout(headerResetTimer);
				header.classList.remove("bb-header--submenu-open");
			}
		}

		desktopParentItems.forEach(function (item) {
			setItemOpen(item, false);

			var topLink = item.querySelector(":scope > a");
			var href = topLink ? (topLink.getAttribute("href") || "").trim() : "";
			if (topLink && (href === "" || href === "#")) {
				topLink.addEventListener("click", function (event) {
					event.preventDefault();
				});
				topLink.style.cursor = "default";
			}

			item.addEventListener("mouseenter", function () {
				openDropdown(item);
			});
			item.addEventListener("mouseleave", function () {
				closeDropdown(item, false);
			});
			item.addEventListener("focusin", function () {
				openDropdown(item);
			});
			item.addEventListener("focusout", function (event) {
				if (!item.contains(event.relatedTarget)) {
					closeDropdown(item, false);
				}
			});
		});

		document.addEventListener("keydown", function (event) {
			if (event.key === "Escape") {
				closeAllDropdowns(true);
			}
		});

		document.addEventListener("click", function (event) {
			if (!isDesktopViewport()) return;
			if (header.contains(event.target)) return;
			closeAllDropdowns(true);
		});

		var handleViewportChange = function () {
			if (!isDesktopViewport()) {
				closeAllDropdowns(true);
			}
		};
		if (typeof desktopMQ.addEventListener === "function") {
			desktopMQ.addEventListener("change", handleViewportChange);
		} else if (typeof desktopMQ.addListener === "function") {
			desktopMQ.addListener(handleViewportChange);
		}
	}

	updateHeaderOffset();
	updateHeader();
	syncSocialPosition();
	window.addEventListener("scroll", onScroll, { passive: true });
	window.addEventListener("resize", function () {
		updateHeaderOffset();
		requestSocialSync();
		mobileSubmenuToggles.forEach(function (toggle) {
			var submenuId = toggle.getAttribute("aria-controls");
			var submenu = submenuId ? mobileNav.querySelector("#" + submenuId) : null;
			if (!submenu) return;
			if (toggle.getAttribute("aria-expanded") === "true") {
				submenu.style.maxHeight = submenu.scrollHeight + "px";
			}
		});
		if (window.innerWidth > 980) {
			closeMobileMenu();
		}
	});
})();
