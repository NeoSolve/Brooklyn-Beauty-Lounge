"use strict";

(function () {
	if (!document.body.classList.contains("home")) return;

	var workSection = document.querySelector(".bb-our-work-section");
	if (!workSection) return;

	var track = workSection.querySelector("[data-work-track]");
	var prevButton = workSection.querySelector("[data-work-nav='prev']");
	var nextButton = workSection.querySelector("[data-work-nav='next']");

	if (!track || !prevButton || !nextButton) return;

	function getScrollStep() {
		var firstItem = track.querySelector(".bb-work__item");
		if (!firstItem) return 280;

		var itemWidth = firstItem.getBoundingClientRect().width;
		var styles = window.getComputedStyle(track);
		var gap = parseFloat(styles.columnGap || styles.gap || "0");
		return itemWidth + gap;
	}

	function updateArrowsState() {
		var maxScrollLeft = track.scrollWidth - track.clientWidth;
		prevButton.disabled = track.scrollLeft <= 2;
		nextButton.disabled = track.scrollLeft >= maxScrollLeft - 2;
	}

	function scrollWork(direction) {
		var step = getScrollStep();
		track.scrollBy({
			left: direction * step,
			behavior: "smooth",
		});
	}

	function playWorkVideo(item) {
		if (!item || item.classList.contains("is-playing")) return;

		var videoUrl = item.dataset.videoUrl;
		if (!videoUrl) return;

		var image = item.querySelector(".bb-work__image");
		if (image) {
			image.remove();
		}
		var playButton = item.querySelector(".bb-work__play-btn");
		if (playButton) {
			playButton.remove();
		}

		var video = document.createElement("video");
		video.className = "bb-work__video";
		video.src = videoUrl;
		video.controls = true;
		video.playsInline = true;
		video.autoplay = true;
		video.preload = "metadata";

		item.appendChild(video);
		item.classList.add("is-playing");

		var playPromise = video.play();
		if (playPromise && typeof playPromise.catch === "function") {
			playPromise.catch(function () {
				// Ignore autoplay restrictions; controls stay visible.
			});
		}
	}

	prevButton.addEventListener("click", function () {
		scrollWork(-1);
	});

	nextButton.addEventListener("click", function () {
		scrollWork(1);
	});

	var playButtons = track.querySelectorAll(".bb-work__play-btn");
	playButtons.forEach(function (button) {
		button.addEventListener("click", function () {
			var item = button.closest(".bb-work__item");
			playWorkVideo(item);
		});
	});

	track.addEventListener("scroll", updateArrowsState, { passive: true });
	window.addEventListener("resize", updateArrowsState);
	updateArrowsState();
})();
