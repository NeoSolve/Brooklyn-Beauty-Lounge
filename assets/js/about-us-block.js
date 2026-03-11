"use strict";

(function () {
	var section = document.querySelector("[data-about-us]");
	if (!section) {
		return;
	}

	var aboutLayout = section.querySelector(".bb-about-us");
	var panel = section.querySelector(".bb-about-us__panel");
	var textColumns = section.querySelector(".bb-about-us__text-columns");
	var imageWrap = section.querySelector(".bb-about-us__image-wrap");
	var secondTextColumn =
		textColumns && textColumns.children.length > 1 ? textColumns.children[1] : null;

	function syncImagePosition() {
		if (!aboutLayout || !panel || !textColumns || !imageWrap || !window.matchMedia) {
			return;
		}

		var isMobile = window.matchMedia("(max-width: 767px)").matches;

		if (isMobile) {
			if (imageWrap.parentNode !== textColumns) {
				if (secondTextColumn && secondTextColumn.parentNode === textColumns) {
					textColumns.insertBefore(imageWrap, secondTextColumn);
				} else {
					textColumns.appendChild(imageWrap);
				}
			}
			return;
		}

		if (imageWrap.parentNode !== aboutLayout) {
			aboutLayout.insertBefore(imageWrap, panel.nextSibling);
		}
	}

	syncImagePosition();

	var mobileQuery = window.matchMedia("(max-width: 767px)");
	if (typeof mobileQuery.addEventListener === "function") {
		mobileQuery.addEventListener("change", syncImagePosition);
	} else if (typeof mobileQuery.addListener === "function") {
		mobileQuery.addListener(syncImagePosition);
	}

	var slider = section.querySelector("[data-about-slider]");
	if (!slider) {
		return;
	}

	var slides = Array.prototype.slice.call(slider.querySelectorAll("[data-about-slide]"));
	var gallerySlides = Array.prototype.slice.call(
		section.querySelectorAll("[data-about-gallery-item]")
	);
	var prevButtons = Array.prototype.slice.call(
		slider.querySelectorAll("[data-about-nav='prev']")
	);
	var nextButtons = Array.prototype.slice.call(
		slider.querySelectorAll("[data-about-nav='next']")
	);

	if (!slides.length || !prevButtons.length || !nextButtons.length) {
		return;
	}

	var framesCount = Math.max(slides.length, gallerySlides.length, 1);
	var activeIndex = Math.max(
		0,
		slides.findIndex(function (slide) {
			return slide.classList.contains("is-active");
		})
	);
	activeIndex = Math.min(activeIndex, framesCount - 1);

	function renderSlides() {
		if (slides.length) {
			var textIndex = activeIndex % slides.length;
			slides.forEach(function (slide, index) {
				var isActive = index === textIndex;
				slide.classList.toggle("is-active", isActive);
				slide.setAttribute("aria-hidden", isActive ? "false" : "true");
			});
		}

		if (gallerySlides.length) {
			var galleryIndex = activeIndex % gallerySlides.length;
			gallerySlides.forEach(function (gallerySlide, index) {
				var isActive = index === galleryIndex;
				gallerySlide.classList.toggle("is-active", isActive);
				gallerySlide.setAttribute("aria-hidden", isActive ? "false" : "true");
			});
		}

		var hasMultipleSlides = framesCount > 1;
		prevButtons.forEach(function (button) {
			button.disabled = !hasMultipleSlides;
		});
		nextButtons.forEach(function (button) {
			button.disabled = !hasMultipleSlides;
		});
	}

	function goToSlide(step) {
		if (framesCount <= 1) {
			return;
		}

		activeIndex = (activeIndex + step + framesCount) % framesCount;
		renderSlides();
	}

	prevButtons.forEach(function (button) {
		button.addEventListener("click", function () {
			goToSlide(-1);
		});
	});

	nextButtons.forEach(function (button) {
		button.addEventListener("click", function () {
			goToSlide(1);
		});
	});

	renderSlides();
})();
