"use strict";

(function () {
	if (!document.body.classList.contains("home")) return;

	var section = document.querySelector("[data-promotions]");
	if (!section) return;

	var slides = section.querySelectorAll("[data-promotion-slide]");
	var prevButton = section.querySelector("[data-promotions-nav='prev']");
	var nextButton = section.querySelector("[data-promotions-nav='next']");

	if (!slides.length || !prevButton || !nextButton) return;

	var currentIndex = 0;
	for (var i = 0; i < slides.length; i += 1) {
		if (slides[i].classList.contains("is-active")) {
			currentIndex = i;
			break;
		}
	}

	function updateSlides(nextIndex) {
		currentIndex = nextIndex;

		slides.forEach(function (slide, index) {
			var isActive = index === currentIndex;
			slide.classList.toggle("is-active", isActive);
			slide.setAttribute("aria-hidden", isActive ? "false" : "true");
		});
	}

	function move(direction) {
		var lastIndex = slides.length - 1;
		var nextIndex = currentIndex + direction;

		if (nextIndex > lastIndex) nextIndex = 0;
		if (nextIndex < 0) nextIndex = lastIndex;

		updateSlides(nextIndex);
	}

	function setupButtons() {
		if (slides.length <= 1) {
			prevButton.disabled = true;
			nextButton.disabled = true;
			return;
		}

		prevButton.disabled = false;
		nextButton.disabled = false;
	}

	prevButton.addEventListener("click", function () {
		move(-1);
	});

	nextButton.addEventListener("click", function () {
		move(1);
	});

	setupButtons();
	updateSlides(currentIndex);
})();
