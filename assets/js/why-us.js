"use strict";

(function () {
	var whyUsSection = document.querySelector("[data-why-us]");
	if (!whyUsSection) {
		return;
	}

	var counterElements = whyUsSection.querySelectorAll(".bb-why-us__value");
	var countersStarted = false;

	function runCounters() {
		if (countersStarted) {
			return;
		}

		countersStarted = true;

		counterElements.forEach(function (element, index) {
			var originalText = (element.textContent || "").trim();
			if (!originalText) {
				return;
			}

			var numericPart = originalText.replace(/[^\d]/g, "");
			var targetValue = parseInt(numericPart, 10);
			if (!targetValue || Number.isNaN(targetValue)) {
				return;
			}

			var prefix = originalText.match(/^[^\d]+/);
			var suffix = originalText.match(/[^\d]+$/);
			var duration = 1200 + index * 180;
			var startTime = null;

			function updateCounter(timestamp) {
				if (!startTime) {
					startTime = timestamp;
				}

				var elapsed = timestamp - startTime;
				var progress = Math.min(elapsed / duration, 1);
				var eased = 1 - Math.pow(1 - progress, 3);
				var currentValue = Math.round(targetValue * eased);
				var formattedValue = String(currentValue);

				element.textContent =
					(prefix ? prefix[0] : "") + formattedValue + (suffix ? suffix[0] : "");

				if (progress < 1) {
					window.requestAnimationFrame(updateCounter);
					return;
				}

				element.textContent = originalText;
			}

			window.requestAnimationFrame(updateCounter);
		});
	}

	if (!("IntersectionObserver" in window)) {
		whyUsSection.classList.add("is-visible");
		runCounters();
		return;
	}

	var observer = new IntersectionObserver(
		function (entries, currentObserver) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}

				entry.target.classList.add("is-visible");
				runCounters();
				currentObserver.unobserve(entry.target);
			});
		},
		{
			threshold: 0.2,
		}
	);

	observer.observe(whyUsSection);
})();
