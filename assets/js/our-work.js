"use strict";

(function () {
	if (!document.body.classList.contains("home")) return;

	var workSection = document.querySelector(".bb-our-work-section");
	if (!workSection) return;

	var track = workSection.querySelector("[data-work-track]");
	var prevButton = workSection.querySelector("[data-work-nav='prev']");
	var nextButton = workSection.querySelector("[data-work-nav='next']");

	if (!track || !prevButton || !nextButton) return;

	var items = track.querySelectorAll(".bb-work__item");
	if (!items.length) return;

	// Clone items twice so we have 3 identical sets for seamless loop
	function cloneItems() {
		var fragment1 = document.createDocumentFragment();
		var fragment2 = document.createDocumentFragment();
		for (var i = 0; i < items.length; i++) {
			fragment1.appendChild(items[i].cloneNode(true));
			fragment2.appendChild(items[i].cloneNode(true));
		}
		track.appendChild(fragment1);
		track.appendChild(fragment2);
	}
	cloneItems();

	function getOneSetWidth() {
		return track.scrollWidth / 3;
	}

	function isMobileViewport() {
		return window.matchMedia("(max-width: 720px)").matches;
	}

	function getCenteredScrollLeft(item) {
		if (!item) return getOneSetWidth();
		return item.offsetLeft + item.offsetWidth / 2 - track.clientWidth / 2;
	}

	function setInitialTrackPosition() {
		var setWidth = getOneSetWidth();

		if (!isMobileViewport()) {
			track.scrollLeft = setWidth;
			return;
		}

		var allItems = track.querySelectorAll(".bb-work__item");
		var baseIndex = items.length;
		var secondItemIndex = baseIndex + (items.length > 1 ? 1 : 0);
		var targetItem = allItems[secondItemIndex];

		track.scrollLeft = getCenteredScrollLeft(targetItem);
	}

	setInitialTrackPosition();

	function getScrollStep() {
		var firstItem = track.querySelector(".bb-work__item");
		if (!firstItem) return 280;

		var itemWidth = firstItem.getBoundingClientRect().width;
		var styles = window.getComputedStyle(track);
		var gap = parseFloat(styles.columnGap || styles.gap || "0");
		return itemWidth + gap;
	}

	function normalizeScrollPosition() {
		var setWidth = getOneSetWidth();
		var left = track.scrollLeft;
		if (left >= setWidth * 2 - 1) {
			track.scrollLeft = left - setWidth;
		} else if (left <= 1) {
			track.scrollLeft = left + setWidth;
		}
	}

	var scrollEndTimer = null;
	function onTrackScroll() {
		if (scrollEndTimer) clearTimeout(scrollEndTimer);
		scrollEndTimer = setTimeout(function () {
			scrollEndTimer = null;
			normalizeScrollPosition();
		}, 150);
	}

	function onTrackScrollEnd() {
		if (scrollEndTimer) clearTimeout(scrollEndTimer);
		scrollEndTimer = null;
		normalizeScrollPosition();
	}

	track.addEventListener("scroll", onTrackScroll, { passive: true });
	if ("onscrollend" in window) {
		track.addEventListener("scrollend", onTrackScrollEnd);
	}

	function scrollWork(direction) {
		var step = getScrollStep();
		track.scrollBy({
			left: direction * step,
			behavior: "smooth",
		});
	}

	function updateVideoButtonState(item, video) {
		if (!item || !video) return;
		if (video.paused || video.ended) {
			item.classList.add("is-video-paused");
		} else {
			item.classList.remove("is-video-paused");
		}
	}

	function playWorkYoutube(item) {
		if (!item || item.classList.contains("is-playing")) return;

		var youtubeId = item.dataset.youtubeId;
		if (!youtubeId) return;

		var image = item.querySelector(".bb-work__image");
		if (image) {
			image.remove();
		}

		var existing = item.querySelector(".bb-work__youtube");
		if (existing) {
			existing.parentNode.removeChild(existing);
		}

		/* Match single-service Reasons block (reasons.js): same embed URL + iframe attrs */
		var iframe = document.createElement("iframe");
		iframe.className = "bb-work__youtube";
		iframe.src =
			"https://www.youtube.com/embed/" +
			encodeURIComponent(youtubeId) +
			"?autoplay=1&rel=0&modestbranding=1&playsinline=1";
		iframe.setAttribute("frameborder", "0");
		iframe.setAttribute(
			"allow",
			"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
		);
		iframe.setAttribute("allowfullscreen", "");
		iframe.setAttribute("title", "Video");

		item.appendChild(iframe);
		item.classList.add("is-playing");
		item.classList.remove("is-video-paused");
	}

	function playWorkVideo(item) {
		if (!item || item.classList.contains("is-playing")) return;

		if (item.dataset.youtubeId) {
			playWorkYoutube(item);
			return;
		}

		var videoUrl = item.dataset.videoUrl;
		if (!videoUrl) return;

		var image = item.querySelector(".bb-work__image");
		if (image) {
			image.remove();
		}

		var video = document.createElement("video");
		video.className = "bb-work__video";
		video.src = videoUrl;
		video.playsInline = true;
		video.autoplay = true;
		video.preload = "metadata";

		video.addEventListener("ended", function () {
			video.currentTime = 0;
			updateVideoButtonState(item, video);
		});
		video.addEventListener("play", function () {
			updateVideoButtonState(item, video);
		});
		video.addEventListener("pause", function () {
			updateVideoButtonState(item, video);
		});

		item.appendChild(video);
		item.classList.add("is-playing");
		item.classList.remove("is-video-paused");

		var playPromise = video.play();
		if (playPromise && typeof playPromise.catch === "function") {
			playPromise.catch(function () {
				updateVideoButtonState(item, video);
			});
		}
	}

	function toggleVideoPlayPause(item) {
		var video = item.querySelector(".bb-work__video");
		if (!video) return;
		if (video.paused) {
			var playPromise = video.play();
			if (playPromise && typeof playPromise.catch === "function") {
				playPromise.catch(function () {
					updateVideoButtonState(item, video);
				});
			}
		} else {
			video.pause();
		}
	}

	prevButton.addEventListener("click", function () {
		scrollWork(-1);
	});

	nextButton.addEventListener("click", function () {
		scrollWork(1);
	});

	track.addEventListener("click", function (e) {
		var button = e.target.closest(".bb-work__play-btn");
		if (button) {
			var item = button.closest(".bb-work__item");
			if (item.classList.contains("is-playing")) {
				toggleVideoPlayPause(item);
			} else {
				playWorkVideo(item);
			}
		}

		var video = e.target.closest("video.bb-work__video");
		if (video) {
			var videoItem = video.closest(".bb-work__item");
			if (videoItem && videoItem.classList.contains("is-playing")) {
				toggleVideoPlayPause(videoItem);
			}
		}
	});

})();
