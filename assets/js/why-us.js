"use strict";

(function () {
	var whyUsSection = document.querySelector("[data-why-us]");
	if (!whyUsSection) {
		return;
	}

	var counterElements = whyUsSection.querySelectorAll(".bb-why-us__value");
	var countersStarted = false;
	var cards = Array.prototype.slice.call(
		whyUsSection.querySelectorAll(".bb-why-us__card")
	);

	function updateWhyUsCardHeights() {
		if (!cards.length) {
			return;
		}

		// On mobile layout cards идут одной колонкой — высоту выравнивать не нужно.
		if (!window.matchMedia("(min-width: 768px)").matches) {
			cards.forEach(function (card) {
				card.style.minHeight = "";
			});
			return;
		}

		// Сначала сбрасываем min-height, чтобы корректно замерить натуральную высоту.
		cards.forEach(function (card) {
			card.style.minHeight = "";
		});

		var maxHeight = 0;
		cards.forEach(function (card) {
			var cardHeight = card.offsetHeight;
			if (cardHeight > maxHeight) {
				maxHeight = cardHeight;
			}
		});

		if (!maxHeight) {
			return;
		}

		cards.forEach(function (card) {
			card.style.minHeight = String(maxHeight) + "px";
		});
	}

	// Обновляем высоту при изменении размеров окна (с лёгким дебаунсом).
	var resizeTimeoutId = null;
	window.addEventListener("resize", function () {
		if (resizeTimeoutId !== null) {
			window.clearTimeout(resizeTimeoutId);
		}
		resizeTimeoutId = window.setTimeout(function () {
			updateWhyUsCardHeights();
		}, 150);
	});

	// Дополнительно выравниваем высоту после полной загрузки страницы (шрифты/картинки).
	window.addEventListener("load", function () {
		updateWhyUsCardHeights();
		// Небольшая задержка на случай, если шрифты дорисуются чуть позже.
		window.setTimeout(updateWhyUsCardHeights, 200);
	});

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

		// После запуска анимаций и появления блока выравниваем высоту карточек.
		updateWhyUsCardHeights();
	}

	if (!("IntersectionObserver" in window)) {
		whyUsSection.classList.add("is-visible");
		runCounters();
		updateWhyUsCardHeights();
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
				updateWhyUsCardHeights();
				currentObserver.unobserve(entry.target);
			});
		},
		{
			threshold: 0.2,
		}
	);

	observer.observe(whyUsSection);
})();
