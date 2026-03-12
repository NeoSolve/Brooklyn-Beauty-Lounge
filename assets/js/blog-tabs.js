"use strict";

(function () {
	var section = document.getElementById("blog-posts");
	if (!section || typeof bbBlogAjax === "undefined") return;

	var filterButtons = section.querySelectorAll(".bb-blog-filters__button");
	var filtersWrap = section.querySelector(".bb-blog-filters-wrap");
	var filtersContainer = section.querySelector(".bb-blog-filters");
	var filtersTrack = section.querySelector(".bb-blog-filters__track");
	var filtersIndicator = section.querySelector(".bb-blog-filters__indicator");
	var cardsContainer = section.querySelector(".bb-blog-cards");
	var paginationContainer = section.querySelector(".bb-blog-posts__pagination");
	var blogPageUrl = section.dataset.blogPageUrl || bbBlogAjax.blogPageUrl || "";
	var blogPageId = section.dataset.blogPageId || (bbBlogAjax.blogPageId != null ? String(bbBlogAjax.blogPageId) : "") || "";

	if (!filterButtons.length || !cardsContainer) return;

	var requestCounter = 0;
	var emptyMessage = "No blog posts found in this category.";

	function setActiveTab(activeSlug) {
		filterButtons.forEach(function (button) {
			var item = button.closest(".bb-blog-filters__item");
			var isActive = (button.dataset.category || "") === activeSlug;
			button.setAttribute("aria-current", isActive ? "true" : "false");
			if (item) {
				item.classList.toggle("is-active", isActive);
			}
		});
	}

	function updateFiltersScrollIndicator() {
		if (!filtersWrap || !filtersContainer || !filtersTrack || !filtersIndicator) return;

		var maxScroll = filtersContainer.scrollWidth - filtersContainer.clientWidth;
		var scrollRatio = 0;
		var maxOffset = Math.max(filtersTrack.clientWidth - filtersIndicator.offsetWidth, 0);
		var sliderOffset = 0;

		if (maxScroll > 0) {
			scrollRatio = filtersContainer.scrollLeft / maxScroll;
		}

		sliderOffset = maxOffset * scrollRatio;
		filtersWrap.style.setProperty("--bb-blog-slider-offset", sliderOffset.toFixed(2) + "px");
	}

	function loadCategory(categorySlug, paged) {
		paged = paged || 1;
		requestCounter += 1;
		var requestId = requestCounter;

		var baseUrl = blogPageUrl || "";
		if (categorySlug && categorySlug !== "all-posts") {
			baseUrl = baseUrl + (baseUrl.indexOf("?") !== -1 ? "&" : "?") + "category=" + encodeURIComponent(categorySlug);
		}

		var payload = new URLSearchParams();
		payload.append("action", "bb_filter_blog_posts");
		payload.append("nonce", bbBlogAjax.nonce);
		payload.append("category", categorySlug || "all-posts");
		payload.append("paged", String(paged));
		if (baseUrl) {
			payload.append("base_url", baseUrl);
		}
		if (blogPageId) {
			payload.append("blog_page_id", blogPageId);
		}

		cardsContainer.setAttribute("aria-busy", "true");
		cardsContainer.classList.add("is-loading");

		fetch(bbBlogAjax.ajaxUrl, {
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
					if (typeof result.data.cards === "string") {
						cardsContainer.innerHTML = result.data.cards;
					}
					if (paginationContainer && typeof result.data.pagination === "string") {
						paginationContainer.innerHTML = result.data.pagination;
					}
					return;
				}

				cardsContainer.innerHTML = "<div class=\"bb-blog-cards__empty\">" + emptyMessage + "</div>";
				if (paginationContainer) {
					paginationContainer.innerHTML = "";
				}
			})
			.catch(function () {
				if (requestId !== requestCounter) return;
				cardsContainer.innerHTML = "<div class=\"bb-blog-cards__empty\">" + emptyMessage + "</div>";
				if (paginationContainer) {
					paginationContainer.innerHTML = "";
				}
			})
			.finally(function () {
				if (requestId !== requestCounter) return;
				cardsContainer.setAttribute("aria-busy", "false");
				cardsContainer.classList.remove("is-loading");
			});
	}

	filterButtons.forEach(function (button) {
		button.addEventListener("click", function (event) {
			var slug = button.dataset.category || "all-posts";
			var href = button.getAttribute("href");
			if (!slug && href) {
				try {
					var params = new URLSearchParams(href.split("?")[1] || "");
					slug = params.get("category") || "all-posts";
				} catch (e) {
					slug = "all-posts";
				}
			}

			event.preventDefault();
			setActiveTab(slug);
			loadCategory(slug, 1);

			if (typeof history !== "undefined" && history.pushState && href) {
				history.pushState({ blogCategory: slug }, "", href);
			}
		});
	});

	if (filtersWrap && filtersContainer && filtersTrack && filtersIndicator) {
		filtersContainer.addEventListener("scroll", updateFiltersScrollIndicator, { passive: true });
		window.addEventListener("resize", updateFiltersScrollIndicator);
		updateFiltersScrollIndicator();
	}

	window.addEventListener("popstate", function (event) {
		var slug = (event.state && event.state.blogCategory) || "all-posts";
		setActiveTab(slug);
		loadCategory(slug, 1);
	});
})();
