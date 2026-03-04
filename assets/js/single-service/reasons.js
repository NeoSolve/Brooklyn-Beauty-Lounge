/**
 * Single service — Reasons block: video play (YouTube/open URL or uploaded file).
 */
(function () {
	var wrap = document.querySelector('.bb-service-reasons__video-wrap');
	if (!wrap) return;

	var playBtn = wrap.querySelector('.bb-service-reasons__play');
	var videoUrl = wrap.getAttribute('data-video-url');
	var videoFile = wrap.getAttribute('data-video-file');
	var videoEl = wrap.querySelector('.bb-service-reasons__video-el');
	var posterImg = wrap.querySelector('.bb-service-reasons__video-poster[data-poster]');
	var posterOverlay = wrap.querySelector('.bb-service-reasons__video-poster-overlay');

	if (!playBtn) return;

	function hidePoster() {
		if (posterImg) posterImg.classList.add('bb-service-reasons__video-poster--hidden');
		if (posterOverlay) posterOverlay.classList.add('bb-service-reasons__video-poster--hidden');
	}

	function showPoster() {
		if (posterImg) posterImg.classList.remove('bb-service-reasons__video-poster--hidden');
		if (posterOverlay) posterOverlay.classList.remove('bb-service-reasons__video-poster--hidden');
	}

	playBtn.addEventListener('click', function () {
		if (videoFile && videoEl) {
			if (videoEl.paused) {
				hidePoster();
				videoEl.classList.add('bb-service-reasons__video-el--active');
				playBtn.classList.add('bb-service-reasons__play--hidden');
				videoEl.play();
			}
		} else if (videoUrl && videoUrl.trim() !== '') {
			window.open(videoUrl.trim(), '_blank', 'noopener,noreferrer');
		}
	});

	if (videoEl) {
		videoEl.addEventListener('pause', function () {
			showPoster();
			videoEl.classList.remove('bb-service-reasons__video-el--active');
			playBtn.classList.remove('bb-service-reasons__play--hidden');
		});
		videoEl.addEventListener('ended', function () {
			showPoster();
			videoEl.classList.remove('bb-service-reasons__video-el--active');
			playBtn.classList.remove('bb-service-reasons__play--hidden');
		});
	}
})();
