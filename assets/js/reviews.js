"use strict";

(function () {
	if (!document.body.classList.contains("home")) return;

	var section = document.querySelector("[data-reviews]");
	if (!section) return;

	var track = section.querySelector("[data-reviews-track]");
	var prevButton = section.querySelector("[data-reviews-nav='prev']");
	var nextButton = section.querySelector("[data-reviews-nav='next']");

	if (!track || !prevButton || !nextButton) return;

	function getScrollStep() {
		var firstCard = track.querySelector(".bb-review-card");
		if (!firstCard) return 320;

		var cardWidth = firstCard.getBoundingClientRect().width;
		var styles = window.getComputedStyle(track);
		var gap = parseFloat(styles.columnGap || styles.gap || "0");
		return cardWidth + gap;
	}

	function updateArrowsState() {
		var maxScrollLeft = track.scrollWidth - track.clientWidth;
		prevButton.disabled = track.scrollLeft <= 2;
		nextButton.disabled = track.scrollLeft >= maxScrollLeft - 2;
	}

	function scrollReviews(direction) {
		track.scrollBy({
			left: direction * getScrollStep(),
			behavior: "smooth",
		});
	}

	prevButton.addEventListener("click", function () {
		scrollReviews(-1);
	});

	nextButton.addEventListener("click", function () {
		scrollReviews(1);
	});

	track.addEventListener("scroll", updateArrowsState, { passive: true });
	window.addEventListener("resize", updateArrowsState);
	updateArrowsState();
})();
