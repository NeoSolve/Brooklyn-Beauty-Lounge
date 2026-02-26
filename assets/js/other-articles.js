"use strict";

(function () {
	var section = document.querySelector("[data-other-articles]");
	if (!section) return;

	var track = section.querySelector("[data-other-articles-track]");
	var prevButton = section.querySelector("[data-other-articles-nav='prev']");
	var nextButton = section.querySelector("[data-other-articles-nav='next']");

	if (!track || !prevButton || !nextButton) return;
	var cards = track.querySelectorAll(".bb-blog-card");
	if (!cards.length) return;

	function getScrollStep() {
		var firstCard = track.querySelector(".bb-blog-card");
		if (!firstCard) return 340;

		var cardWidth = firstCard.getBoundingClientRect().width;
		var styles = window.getComputedStyle(track);
		var gap = parseFloat(styles.columnGap || styles.gap || "0") || 20;
		return cardWidth + gap;
	}

	function updateButtons() {
		var maxScroll = track.scrollWidth - track.clientWidth;
		prevButton.disabled = track.scrollLeft <= 1;
		nextButton.disabled = track.scrollLeft >= maxScroll - 1;
	}

	function scrollCarousel(direction) {
		track.scrollBy({
			left: direction * getScrollStep(),
			behavior: "smooth",
		});
	}

	prevButton.addEventListener("click", function () {
		scrollCarousel(-1);
	});

	nextButton.addEventListener("click", function () {
		scrollCarousel(1);
	});

	var scrollTicking = false;
	track.addEventListener(
		"scroll",
		function () {
			if (scrollTicking) return;
			scrollTicking = true;
			requestAnimationFrame(function () {
				updateButtons();
				scrollTicking = false;
			});
		},
		{ passive: true }
	);

	updateButtons();
})();
