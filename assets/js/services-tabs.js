"use strict";

(function () {
	var canUseHover = window.matchMedia("(hover: hover) and (pointer: fine)").matches;
	var prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	var heroImageFadeDuration = 520;
	var serviceHeroMedia = document.querySelector(".page-template-page-services .bb-services-hero__media");
	var serviceHeroImage = serviceHeroMedia ? serviceHeroMedia.querySelector("img") : null;
	var serviceHeroFallback = serviceHeroMedia ? serviceHeroMedia.querySelector(".bb-services-hero__media-fallback") : null;
	var serviceHeroTransitionImage = null;
	var defaultHeroImageUrl = serviceHeroMedia ? serviceHeroMedia.getAttribute("data-default-image-url") || "" : "";
	var defaultHeroImageAlt = serviceHeroMedia ? serviceHeroMedia.getAttribute("data-default-image-alt") || "" : "";
	var heroImageSwapRequestId = 0;

	function ensureServiceHeroImage() {
		if (!serviceHeroMedia || serviceHeroImage) return serviceHeroImage;

		serviceHeroImage = document.createElement("img");
		serviceHeroImage.loading = "lazy";
		serviceHeroImage.decoding = "async";
		serviceHeroMedia.insertBefore(serviceHeroImage, serviceHeroMedia.firstChild);

		return serviceHeroImage;
	}

	function ensureServiceHeroTransitionImage() {
		if (!serviceHeroMedia || serviceHeroTransitionImage) return serviceHeroTransitionImage;

		serviceHeroTransitionImage = document.createElement("img");
		serviceHeroTransitionImage.className = "bb-services-hero__media-transition";
		serviceHeroTransitionImage.alt = "";
		serviceHeroTransitionImage.decoding = "async";
		serviceHeroTransitionImage.setAttribute("aria-hidden", "true");
		serviceHeroTransitionImage.hidden = true;
		serviceHeroMedia.appendChild(serviceHeroTransitionImage);

		return serviceHeroTransitionImage;
	}

	function clearServiceHeroTransitionImage() {
		if (!serviceHeroTransitionImage) return;

		serviceHeroTransitionImage.classList.remove("is-visible");
		serviceHeroTransitionImage.hidden = true;
		serviceHeroTransitionImage.removeAttribute("src");
	}

	function applyServiceHeroImage(imageUrl, altText) {
		if (!serviceHeroMedia) return;

		if (!imageUrl) {
			if (serviceHeroImage) {
				serviceHeroImage.remove();
				serviceHeroImage = null;
			}

			if (serviceHeroFallback) {
				serviceHeroFallback.hidden = false;
			}

			clearServiceHeroTransitionImage();
			return;
		}

		var heroImage = ensureServiceHeroImage();
		if (!heroImage) return;

		heroImage.src = imageUrl;
		heroImage.alt = altText || "";
		heroImage.hidden = false;

		if (serviceHeroFallback) {
			serviceHeroFallback.hidden = true;
		}
	}

	function setServiceHeroImage(imageUrl, altText) {
		if (!serviceHeroMedia) return;

		var nextImageUrl = imageUrl || "";
		var nextAltText = altText || "";
		var currentImageUrl = serviceHeroImage ? serviceHeroImage.currentSrc || serviceHeroImage.src || "" : "";

		if (currentImageUrl === nextImageUrl && (!serviceHeroFallback || serviceHeroFallback.hidden || !nextImageUrl)) {
			if (serviceHeroImage && nextImageUrl) {
				serviceHeroImage.alt = nextAltText;
			}
			return;
		}

		heroImageSwapRequestId += 1;
		var requestId = heroImageSwapRequestId;

		if (!nextImageUrl || prefersReducedMotion) {
			applyServiceHeroImage(nextImageUrl, nextAltText);
			return;
		}

		var preloadImage = new Image();
		var hasHandledPreload = false;
		preloadImage.decoding = "async";
		function handlePreloadedHeroImage() {
			if (hasHandledPreload) return;
			hasHandledPreload = true;

			if (requestId !== heroImageSwapRequestId) return;

			var transitionImage = ensureServiceHeroTransitionImage();
			if (!transitionImage) {
				applyServiceHeroImage(nextImageUrl, nextAltText);
				return;
			}

			transitionImage.classList.remove("is-visible");
			transitionImage.hidden = false;
			transitionImage.src = nextImageUrl;

			window.requestAnimationFrame(function () {
				if (requestId !== heroImageSwapRequestId) return;
				transitionImage.classList.add("is-visible");
			});

			window.setTimeout(function () {
				if (requestId !== heroImageSwapRequestId) return;

				applyServiceHeroImage(nextImageUrl, nextAltText);
				transitionImage.classList.remove("is-visible");

				window.setTimeout(function () {
					if (requestId !== heroImageSwapRequestId) return;
					clearServiceHeroTransitionImage();
				}, heroImageFadeDuration);
			}, heroImageFadeDuration);
		}

		preloadImage.onload = handlePreloadedHeroImage;
		preloadImage.onerror = function () {
			if (requestId !== heroImageSwapRequestId) return;
			applyServiceHeroImage(nextImageUrl, nextAltText);
		};
		preloadImage.src = nextImageUrl;

		if (preloadImage.complete) {
			handlePreloadedHeroImage();
		}
	}

	function resetServiceHeroImage() {
		setServiceHeroImage(defaultHeroImageUrl, defaultHeroImageAlt);
	}

	// Make whole service cards clickable on all pages (independent of AJAX tabs).
	var servicesCardsContainers = document.querySelectorAll(".bb-services-cards");
	servicesCardsContainers.forEach(function (servicesCardsContainer) {
		function getCardMoreLink(event) {
			var card = event.target.closest(".bb-service-card");
			if (!card || card.classList.contains("bb-service-card--placeholder")) return null;

			// Don't override clicks on existing interactive elements.
			if (event.target.closest("a, button")) return null;

			var moreLink = card.querySelector(".bb-service-card__more-link");
			return moreLink && moreLink.href ? moreLink : null;
		}

		servicesCardsContainer.addEventListener("click", function (event) {
			var moreLink = getCardMoreLink(event);
			if (!moreLink) return;

			if (event.metaKey || event.ctrlKey) {
				window.open(moreLink.href, "_blank", "noopener");
				return;
			}

			window.location.href = moreLink.href;
		});

		servicesCardsContainer.addEventListener("auxclick", function (event) {
			if (event.button !== 1) return;

			var moreLink = getCardMoreLink(event);
			if (!moreLink) return;

			event.preventDefault();
			window.open(moreLink.href, "_blank", "noopener");
		});

		servicesCardsContainer.addEventListener("mouseover", function (event) {
			if (!canUseHover) return;

			var media = event.target.closest(".bb-service-card__media");
			if (!media || !servicesCardsContainer.contains(media)) return;
			if (media.contains(event.relatedTarget)) return;

			var hoverImage = media.getAttribute("data-hover-image") || "";
			if (!hoverImage) return;

			setServiceHeroImage(hoverImage, media.getAttribute("data-hover-alt") || "");
		});

		servicesCardsContainer.addEventListener("mouseout", function (event) {
			if (!canUseHover) return;

			var media = event.target.closest(".bb-service-card__media");
			if (!media || !servicesCardsContainer.contains(media)) return;
			if (media.contains(event.relatedTarget)) return;
			if (
				event.relatedTarget &&
				event.relatedTarget.closest &&
				event.relatedTarget.closest(".bb-service-card__media")
			) {
				return;
			}

			resetServiceHeroImage();
		});
	});

	var sections = document.querySelectorAll(".bb-services-section");
	if (!sections.length || typeof bbServicesAjax === "undefined") return;

	sections.forEach(function (servicesSection) {
		var ajaxAction = servicesSection.dataset.ajaxAction;
		if (!ajaxAction) return;

		var filtersWrap = servicesSection.querySelector(".bb-services-filters-wrap");
		var filtersContainer = servicesSection.querySelector(".bb-services-filters");
		var filtersTrack = servicesSection.querySelector(".bb-services-filters__track");
		var filtersIndicator = servicesSection.querySelector(".bb-services-filters__indicator");
		var filterButtons = servicesSection.querySelectorAll(".bb-services-filters__button");
		var servicesCardsContainer = servicesSection.querySelector(".bb-services-cards");

		if (!filtersWrap || !filtersContainer || !filtersTrack || !filtersIndicator || !filterButtons.length || !servicesCardsContainer) return;

		var defaultFilter = servicesSection.dataset.defaultFilter || "all-services";
		var errorMessage = servicesSection.dataset.emptyMessage || "Unable to load items.";
		var requestCounter = 0;

		function setActiveServiceFilter(activeFilter) {
			filterButtons.forEach(function (button) {
				var item = button.closest(".bb-services-filters__item");
				var isActive = button.dataset.filter === activeFilter;

				button.setAttribute("aria-pressed", isActive ? "true" : "false");
				if (item) {
					item.classList.toggle("is-active", isActive);
				}
			});
		}

		function updateFiltersScrollIndicator() {
			var maxScroll = filtersContainer.scrollWidth - filtersContainer.clientWidth;
			var scrollRatio = 0;
			var maxOffset = Math.max(filtersTrack.clientWidth - filtersIndicator.offsetWidth, 0);
			var sliderOffset = 0;

			if (maxScroll > 0) {
				scrollRatio = filtersContainer.scrollLeft / maxScroll;
			}

			sliderOffset = maxOffset * scrollRatio;
			filtersWrap.style.setProperty("--bb-services-slider-offset", sliderOffset.toFixed(2) + "px");
		}

		function loadServicesByCategory(categorySlug) {
			requestCounter += 1;
			var requestId = requestCounter;
			var payload = new URLSearchParams();

			payload.append("action", ajaxAction);
			payload.append("nonce", bbServicesAjax.nonce);
			payload.append("category", categorySlug || defaultFilter);

			servicesCardsContainer.classList.add("is-loading");

			fetch(bbServicesAjax.ajaxUrl, {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
				},
				body: payload.toString(),
				credentials: "same-origin",
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (result) {
					if (requestId !== requestCounter) return;

					if (result && result.success && result.data) {
						if (result.data.cards !== undefined) {
							resetServiceHeroImage();
							servicesCardsContainer.innerHTML = result.data.cards;
							var paginationContainer = servicesSection.querySelector(".bb-blog-posts__pagination");
							if (paginationContainer && result.data.pagination !== undefined) {
								paginationContainer.innerHTML = result.data.pagination;
							}
							return;
						}
						if (typeof result.data.html === "string") {
							resetServiceHeroImage();
							servicesCardsContainer.innerHTML = result.data.html;
							return;
						}
					}

					servicesCardsContainer.innerHTML = '<div class="bb-services-cards__empty">' + errorMessage + "</div>";
				})
				.catch(function () {
					if (requestId !== requestCounter) return;
					servicesCardsContainer.innerHTML = '<div class="bb-services-cards__empty">' + errorMessage + "</div>";
				})
				.finally(function () {
					if (requestId !== requestCounter) return;
					servicesCardsContainer.classList.remove("is-loading");
				});
		}

		function scrollFilterToCenter(button) {
			var item = button.closest(".bb-services-filters__item");
			if (!item) return;

			var itemRect = item.getBoundingClientRect();
			var containerRect = filtersContainer.getBoundingClientRect();
			var offset = itemRect.left - containerRect.left + filtersContainer.scrollLeft
				- (filtersContainer.clientWidth - item.offsetWidth) / 2;

			filtersContainer.scrollTo({ left: Math.max(offset, 0), behavior: "smooth" });
		}

		filterButtons.forEach(function (button) {
			button.addEventListener("click", function () {
				var selectedFilter = button.dataset.filter || defaultFilter;
				setActiveServiceFilter(selectedFilter);
				loadServicesByCategory(selectedFilter);
				scrollFilterToCenter(button);
			});
		});

		filtersContainer.addEventListener("scroll", updateFiltersScrollIndicator, { passive: true });
		window.addEventListener("resize", updateFiltersScrollIndicator);
		updateFiltersScrollIndicator();
	});
})();
