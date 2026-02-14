"use strict";

(function () {
	if (!document.body.classList.contains("home")) return;

	var servicesSection = document.querySelector(".bb-services-section");
	if (!servicesSection) return;

	var filterButtons = servicesSection.querySelectorAll(".bb-services-filters__button");
	var servicesCardsContainer = servicesSection.querySelector(".bb-services-cards");

	if (!filterButtons.length || !servicesCardsContainer || typeof bbServicesAjax === "undefined") return;

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

	function loadServicesByCategory(categorySlug) {
		requestCounter += 1;
		var requestId = requestCounter;
		var payload = new URLSearchParams();

		payload.append("action", "bb_filter_services");
		payload.append("nonce", bbServicesAjax.nonce);
		payload.append("category", categorySlug || "all-services");

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

				if (result && result.success && result.data && typeof result.data.html === "string") {
					servicesCardsContainer.innerHTML = result.data.html;
					return;
				}

				servicesCardsContainer.innerHTML = '<div class="bb-services-cards__empty">Unable to load services.</div>';
			})
			.catch(function () {
				if (requestId !== requestCounter) return;
				servicesCardsContainer.innerHTML = '<div class="bb-services-cards__empty">Unable to load services.</div>';
			})
			.finally(function () {
				if (requestId !== requestCounter) return;
				servicesCardsContainer.classList.remove("is-loading");
			});
	}

	filterButtons.forEach(function (button) {
		button.addEventListener("click", function () {
			var selectedFilter = button.dataset.filter || "all-services";
			setActiveServiceFilter(selectedFilter);
			loadServicesByCategory(selectedFilter);
		});
	});
})();
