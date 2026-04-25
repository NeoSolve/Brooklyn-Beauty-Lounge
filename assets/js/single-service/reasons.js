/**
 * Single service — Reasons block: video play (YouTube/Vimeo embed or uploaded file).
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

	function getYouTubeId(url) {
		var patterns = [
			/(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/
		];
		for (var i = 0; i < patterns.length; i++) {
			var m = url.match(patterns[i]);
			if (m && m[1]) return m[1];
		}
		return null;
	}

	function getVimeoId(url) {
		var patterns = [
			/vimeo\.com\/(?:video\/|channels\/[^\/]+\/|groups\/[^\/]+\/videos\/)?(\d+)/,
			/player\.vimeo\.com\/video\/(\d+)/
		];
		for (var i = 0; i < patterns.length; i++) {
			var m = url.match(patterns[i]);
			if (m && m[1]) return m[1];
		}
		return null;
	}

	function buildEmbedUrl(url) {
		var ytId = getYouTubeId(url);
		if (ytId) {
			return 'https://www.youtube.com/embed/' + ytId + '?autoplay=1&rel=0&modestbranding=1&playsinline=1';
		}
		var vmId = getVimeoId(url);
		if (vmId) {
			return 'https://player.vimeo.com/video/' + vmId + '?autoplay=1&title=0&byline=0&portrait=0';
		}
		return null;
	}

	function embedExternalVideo(url) {
		var embedSrc = buildEmbedUrl(url);
		if (!embedSrc) {
			window.open(url.trim(), '_blank', 'noopener,noreferrer');
			return;
		}

		var existing = wrap.querySelector('.bb-service-reasons__video-iframe');
		if (existing) {
			existing.parentNode.removeChild(existing);
		}

		var iframe = document.createElement('iframe');
		iframe.className = 'bb-service-reasons__video-iframe';
		iframe.src = embedSrc;
		iframe.setAttribute('frameborder', '0');
		iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
		iframe.setAttribute('allowfullscreen', '');
		iframe.setAttribute('title', 'Video');

		wrap.appendChild(iframe);

		hidePoster();
		playBtn.classList.add('bb-service-reasons__play--hidden');
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
			embedExternalVideo(videoUrl.trim());
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
