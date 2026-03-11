"use strict";

(function () {
	if (!document.body.classList.contains("home")) return;

	var section = document.querySelector("[data-reviews]");
	if (!section) return;

	var track = section.querySelector("[data-reviews-track]");
	var prevButton = section.querySelector("[data-reviews-nav='prev']");
	var nextButton = section.querySelector("[data-reviews-nav='next']");

	if (!track || !prevButton || !nextButton) return;
	var cards = track.querySelectorAll(".bb-review-card");
	if (!cards.length) return;

	function cloneCards() {
		var fragment1 = document.createDocumentFragment();
		var fragment2 = document.createDocumentFragment();
		for (var i = 0; i < cards.length; i++) {
			fragment1.appendChild(cards[i].cloneNode(true));
			fragment2.appendChild(cards[i].cloneNode(true));
		}
		track.appendChild(fragment1);
		track.appendChild(fragment2);
	}
	cloneCards();

	function getScrollStep() {
		var firstCard = track.querySelector(".bb-review-card");
		if (!firstCard) return 320;

		var cardWidth = firstCard.getBoundingClientRect().width;
		var styles = window.getComputedStyle(track);
		var gap = parseFloat(styles.columnGap || styles.gap || "0");
		return cardWidth + gap;
	}

	function getSetWidth() {
		return track.scrollWidth / 3;
	}

	function getCenterOffset() {
		var firstCard = track.querySelector(".bb-review-card");
		if (!firstCard) return 0;

		var isMobile = window.matchMedia("(max-width: 767px)").matches;
		if (!isMobile) return 0;

		var cardWidth = firstCard.getBoundingClientRect().width;
		return Math.max(0, (track.clientWidth - cardWidth) / 2);
	}

	function getAnchorScrollLeft() {
		return getSetWidth() - getCenterOffset();
	}

	function normalizeScrollPosition() {
		var setWidth = getSetWidth();
		var left = track.scrollLeft;
		var anchor = getAnchorScrollLeft();
		var upperBound = anchor + setWidth;

		if (left >= upperBound - 1) {
			track.scrollLeft = left - setWidth;
		} else if (left < anchor - 1) {
			track.scrollLeft = left + setWidth;
		}
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

	var scrollTicking = false;
	track.addEventListener(
		"scroll",
		function () {
			if (scrollTicking) return;
			scrollTicking = true;
			requestAnimationFrame(function () {
				normalizeScrollPosition();
				scrollTicking = false;
			});
		},
		{ passive: true }
	);

	function setInitialPosition() {
		track.scrollLeft = getAnchorScrollLeft();
	}

	setInitialPosition();
	window.addEventListener("resize", normalizeScrollPosition, { passive: true });
})();
