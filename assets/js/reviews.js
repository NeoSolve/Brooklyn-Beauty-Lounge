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
	var originalCardsCount = cards.length;

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
		var allCards = track.querySelectorAll(".bb-review-card");
		if (allCards.length <= originalCardsCount) {
			return track.scrollWidth / 3;
		}

		var firstSetStart = allCards[0].offsetLeft;
		var secondSetStart = allCards[originalCardsCount].offsetLeft;
		var measuredSetWidth = secondSetStart - firstSetStart;

		if (measuredSetWidth > 0) return measuredSetWidth;
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

	function getNormalizedScrollLeft(left) {
		var setWidth = getSetWidth();
		var anchor = getAnchorScrollLeft();
		var upperBound = anchor + setWidth;
		var normalizedLeft = left;

		if (!setWidth) return normalizedLeft;

		while (normalizedLeft >= upperBound - 1) {
			normalizedLeft -= setWidth;
		}

		while (normalizedLeft < anchor - 1) {
			normalizedLeft += setWidth;
		}

		return normalizedLeft;
	}

	function normalizeScrollPosition() {
		var normalizedLeft = getNormalizedScrollLeft(track.scrollLeft);
		if (Math.abs(normalizedLeft - track.scrollLeft) > 1) {
			track.scrollLeft = normalizedLeft;
		}
	}

	function scrollReviews(direction) {
		var step = getScrollStep();
		var currentLeft = getNormalizedScrollLeft(track.scrollLeft);
		if (Math.abs(currentLeft - track.scrollLeft) > 1) {
			track.scrollLeft = currentLeft;
		}

		isProgrammaticScroll = true;
		window.clearTimeout(scrollEndTimeout);

		track.scrollTo({
			left: currentLeft + direction * step,
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
	var scrollEndTimeout = 0;
	var isProgrammaticScroll = false;
	track.addEventListener(
		"scroll",
		function () {
			if (isProgrammaticScroll) {
				window.clearTimeout(scrollEndTimeout);
				scrollEndTimeout = window.setTimeout(function () {
					isProgrammaticScroll = false;
					normalizeScrollPosition();
				}, 260);
				return;
			}

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
