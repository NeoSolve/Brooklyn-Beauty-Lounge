"use strict";

(function () {
	var section = document.querySelector("[data-promotions]");
	if (!section) return;

	var track = section.querySelector("[data-promotions-track]");
	var viewport = section.querySelector("[data-promotions-viewport]");
	var prevButton = section.querySelector("[data-promotions-nav='prev']");
	var nextButton = section.querySelector("[data-promotions-nav='next']");

	if (!track || !viewport || !prevButton || !nextButton) return;

	var originals = Array.from(track.querySelectorAll("[data-promotion-slide]"));
	var slideCountOriginal = originals.length;

	if (!slideCountOriginal) return;

	var reduceMotion =
		window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

	var loops = slideCountOriginal > 1;

	if (loops) {
		var cloneLast = originals[slideCountOriginal - 1].cloneNode(true);
		var cloneFirst = originals[0].cloneNode(true);

		cloneLast.classList.remove("is-active");
		cloneFirst.classList.remove("is-active");

		Array.from(cloneLast.querySelectorAll("[id]")).forEach(function (el) {
			el.removeAttribute("id");
		});
		Array.from(cloneFirst.querySelectorAll("[id]")).forEach(function (el) {
			el.removeAttribute("id");
		});

		track.insertBefore(cloneLast, originals[0]);
		track.appendChild(cloneFirst);
	}

	var slides = track.querySelectorAll("[data-promotion-slide]");

	var attrInterval = section.getAttribute("data-promotions-autoplay-seconds");
	var intervalSecParsed = attrInterval !== null ? parseInt(attrInterval, 10) : NaN;
	var autoplaySeconds = !isNaN(intervalSecParsed) ? Math.max(0, Math.min(120, intervalSecParsed)) : 5;
	var autoplayMs = autoplaySeconds > 0 ? autoplaySeconds * 1000 : 0;
	if (reduceMotion) {
		autoplayMs = 0;
	}

	var autoplayTimer = null;
	var currentDomIndex = 0;

	if (loops) {
		currentDomIndex = Array.from(slides).findIndex(function (el) {
			return el.classList.contains("is-active");
		});
		if (currentDomIndex < 0) currentDomIndex = 1;
	} else {
		currentDomIndex = Array.from(slides).findIndex(function (el) {
			return el.classList.contains("is-active");
		});
		if (currentDomIndex < 0) currentDomIndex = 0;
	}

	var pendingTeleportHandler = null;
	var pendingTeleportSlide = null;

	function applySlideAccessibility() {
		slides.forEach(function (slide, index) {
			var isActive = index === currentDomIndex;
			slide.classList.toggle("is-active", isActive);
			slide.setAttribute("aria-hidden", isActive ? "false" : "true");
			if (isActive) slide.removeAttribute("inert");
			else slide.setAttribute("inert", "");
		});
	}

	function cancelPendingTeleport() {
		if (pendingTeleportHandler && pendingTeleportSlide) {
			pendingTeleportSlide.removeEventListener("transitionend", pendingTeleportHandler);
		}
		pendingTeleportHandler = null;
		pendingTeleportSlide = null;
	}

	function setSlidesTransitionDisabled(disabled) {
		Array.from(slides).forEach(function (slide) {
			if (disabled) {
				slide.style.setProperty("transition", "none");
			} else {
				slide.style.removeProperty("transition");
			}
		});
	}

	function jumpWithoutTransition(targetDomIndex) {
		cancelPendingTeleport();
		setSlidesTransitionDisabled(true);
		currentDomIndex = targetDomIndex;
		applySlideAccessibility();
		void viewport.offsetHeight;
		setSlidesTransitionDisabled(false);
	}

	function scheduleCloneTeleportAfterTransitionIfNeeded() {
		cancelPendingTeleport();
		if (!loops) return;

		var lastDomIdx = slides.length - 1;
		var onEdge = currentDomIndex === lastDomIdx || currentDomIndex === 0;

		if (!onEdge) return;

		if (reduceMotion) {
			if (currentDomIndex === lastDomIdx) jumpWithoutTransition(1);
			else if (currentDomIndex === 0) jumpWithoutTransition(slideCountOriginal);
			return;
		}

		var activeEl = slides[currentDomIndex];
		if (!activeEl) return;

		pendingTeleportSlide = activeEl;

		pendingTeleportHandler = function (e) {
			if (e.target !== activeEl) return;
			if (e.propertyName !== "opacity") return;

			activeEl.removeEventListener("transitionend", pendingTeleportHandler);
			pendingTeleportHandler = null;
			pendingTeleportSlide = null;

			if (currentDomIndex === lastDomIdx) jumpWithoutTransition(1);
			else if (currentDomIndex === 0) jumpWithoutTransition(slideCountOriginal);
		};

		activeEl.addEventListener("transitionend", pendingTeleportHandler);
	}

	function updateSlides(nextDomIndex) {
		currentDomIndex = nextDomIndex;
		applySlideAccessibility();
		scheduleCloneTeleportAfterTransitionIfNeeded();
	}

	function move(direction) {
		if (!loops) {
			var singleLast = slides.length - 1;
			var nx = currentDomIndex + direction;
			if (nx > singleLast) nx = 0;
			if (nx < 0) nx = singleLast;
			cancelPendingTeleport();
			updateSlides(nx);
			return;
		}

		cancelPendingTeleport();
		var maxIdx = slides.length - 1;

		if (direction > 0) {
			if (currentDomIndex < maxIdx) updateSlides(currentDomIndex + 1);
		} else if (currentDomIndex > 0) {
			updateSlides(currentDomIndex - 1);
		}
	}

	function setupButtons() {
		if (!loops) {
			prevButton.disabled = true;
			nextButton.disabled = true;
			return;
		}

		prevButton.disabled = false;
		nextButton.disabled = false;
	}

	function clearAutoplay() {
		if (autoplayTimer) {
			window.clearInterval(autoplayTimer);
			autoplayTimer = null;
		}
	}

	function startAutoplay() {
		clearAutoplay();
		if (!autoplayMs || !loops) return;
		autoplayTimer = window.setInterval(function () {
			move(1);
		}, autoplayMs);
	}

	prevButton.addEventListener("click", function () {
		clearAutoplay();
		move(-1);
		startAutoplay();
	});

	nextButton.addEventListener("click", function () {
		clearAutoplay();
		move(1);
		startAutoplay();
	});

	section.addEventListener("mouseenter", clearAutoplay);
	section.addEventListener("mouseleave", startAutoplay);

	document.addEventListener("visibilitychange", function () {
		if (document.hidden) clearAutoplay();
		else startAutoplay();
	});

	setupButtons();
	cancelPendingTeleport();
	updateSlides(currentDomIndex);

	startAutoplay();
})();
