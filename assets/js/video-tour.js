(() => {
	"use strict";

	const videoBlocks = document.querySelectorAll("#video-tour .bb-video--has-play");

	videoBlocks.forEach((videoBlock) => {
		const video = videoBlock.querySelector("video");
		const playButton = videoBlock.querySelector(".bb-video__play-btn");

		if (!video || !playButton) {
			return;
		}

		video.controls = false;
		video.removeAttribute("controls");

		const syncState = () => {
			const isPlaying = !video.paused && !video.ended;
			videoBlock.classList.toggle("is-playing", isPlaying);
		};

		const playVideo = () => {
			const playPromise = video.play();
			if (playPromise && typeof playPromise.catch === "function") {
				playPromise.catch(() => {});
			}
		};

		playButton.addEventListener("click", (event) => {
			event.preventDefault();
			playVideo();
		});

		video.addEventListener("click", () => {
			if (video.paused || video.ended) {
				playVideo();
			} else {
				video.pause();
			}
		});

		video.addEventListener("play", syncState);
		video.addEventListener("pause", syncState);
		video.addEventListener("ended", syncState);

		syncState();
	});
})();
