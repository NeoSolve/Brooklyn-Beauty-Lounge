/**
 * Hero video fullscreen toggle (open/close)
 */
(function () {
	var CLICK_ANIMATION_MS = 170;
	var container = document.getElementById('bb-hero-video-fullscreen');
	if (!container) return;

	var btnOpen = container.querySelector('[data-hero-video-open]');
	var btnClose = container.querySelector('[data-hero-video-close]');
	var video = container.querySelector('[data-hero-video]');

	function setPageFullscreenState(isActive) {
		document.documentElement.classList.toggle('bb-hero-video-open', isActive);
		document.body.classList.toggle('bb-hero-video-open', isActive);
	}

	function isFullscreen() {
		return !!(
			document.fullscreenElement ||
			document.webkitFullscreenElement ||
			document.mozFullScreenElement ||
			document.msFullscreenElement
		);
	}

	function isFullscreenTarget(el) {
		var fs = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
		return fs === el;
	}

	function requestFullscreen(el) {
		if (el.requestFullscreen) {
			return el.requestFullscreen();
		}
		if (el.webkitRequestFullscreen) {
			el.webkitRequestFullscreen();
			return Promise.resolve();
		}
		if (el.mozRequestFullScreen) {
			el.mozRequestFullScreen();
			return Promise.resolve();
		}
		if (el.msRequestFullscreen) {
			el.msRequestFullscreen();
			return Promise.resolve();
		}
		return Promise.resolve();
	}

	function exitFullscreen() {
		if (document.exitFullscreen) {
			return document.exitFullscreen();
		}
		if (document.webkitExitFullscreen) {
			document.webkitExitFullscreen();
			return Promise.resolve();
		}
		if (document.mozCancelFullScreen) {
			document.mozCancelFullScreen();
			return Promise.resolve();
		}
		if (document.msExitFullscreen) {
			document.msExitFullscreen();
			return Promise.resolve();
		}
		return Promise.resolve();
	}

	function animateButtonAndRun(button, callback) {
		if (!button || typeof callback !== 'function') return;
		if (button.dataset.animating === '1') return;

		button.dataset.animating = '1';
		button.classList.remove('is-clicked');
		// Force reflow so repeated clicks retrigger animation reliably.
		void button.offsetWidth;
		button.classList.add('is-clicked');

		window.setTimeout(function () {
			button.classList.remove('is-clicked');
			button.dataset.animating = '0';
			callback();
		}, CLICK_ANIMATION_MS);
	}

	function updateUI() {
		var full = isFullscreen() && isFullscreenTarget(container);
		container.classList.toggle('is-fullscreen', full);
		if (btnClose) {
			btnClose.hidden = !full;
		}
		setPageFullscreenState(full);
	}

	function onFullscreenChange() {
		updateUI();
	}

	if (btnOpen) {
		btnOpen.addEventListener('click', function () {
			animateButtonAndRun(btnOpen, function () {
				requestFullscreen(container)
					.then(function () {
						container.classList.add('is-fullscreen');
						if (btnClose) btnClose.hidden = false;
						setPageFullscreenState(true);
						if (video && video.play) {
							video.play().catch(function () {});
						}
					})
					.catch(function () {
						// Ignore fullscreen rejections silently.
					});
			});
		});
	}

	if (btnClose) {
		btnClose.addEventListener('click', function () {
			animateButtonAndRun(btnClose, function () {
				if (isFullscreen() && isFullscreenTarget(container)) {
					exitFullscreen().catch(function () {});
					return;
				}

				container.classList.remove('is-fullscreen');
				btnClose.hidden = true;
				setPageFullscreenState(false);
			});
		});
	}

	document.addEventListener('fullscreenchange', onFullscreenChange);
	document.addEventListener('webkitfullscreenchange', onFullscreenChange);
	document.addEventListener('mozfullscreenchange', onFullscreenChange);
	document.addEventListener('MSFullscreenChange', onFullscreenChange);
})();
