/**
 * Single service — Explore block: show more/show less toggles.
 */
(function () {
	var sections = document.querySelectorAll('.single-service .bb-service-explore__section');
	if (!sections.length) return;

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var desktopMedia = window.matchMedia('(min-width: 1025px)');

	function updateDesktopSectionNumbers() {
		sections.forEach(function (section) {
			var copy = section.querySelector('.bb-service-explore__section-copy');
			var title = section.querySelector('.bb-service-explore__section-title');
			var desktopNumber = section.querySelector('.bb-service-explore__section-number--desktop');

			if (!copy || !title || !desktopNumber) return;

			if (!desktopMedia.matches) {
				copy.style.removeProperty('--bb-explore-number-left');
				return;
			}

			var range = document.createRange();
			range.selectNodeContents(title);

			var rects = range.getClientRects();
			if (!rects.length) {
				copy.style.removeProperty('--bb-explore-number-left');
				return;
			}

			var titleRect = title.getBoundingClientRect();
			var copyRect = copy.getBoundingClientRect();
			var firstLineRect = rects[0];
			var lineTop = firstLineRect.top;
			var lineBottom = firstLineRect.bottom;

			for (var index = 1; index < rects.length; index++) {
				var rect = rects[index];
				if (Math.abs(rect.top - lineTop) < 1 && Math.abs(rect.bottom - lineBottom) < 1) {
					if (rect.right > firstLineRect.right) {
						firstLineRect = rect;
					}
					continue;
				}

				break;
			}

			var numberLeft = firstLineRect.right - copyRect.left;
			var numberTop = titleRect.top - copyRect.top;

			copy.style.setProperty('--bb-explore-number-left', Math.round(numberLeft) + 'px');
			copy.style.setProperty('--bb-explore-number-top', Math.round(numberTop) + 'px');
		});
	}

	sections.forEach(function (section) {
		var toggle = section.querySelector('.bb-service-explore__toggle');
		var list = section.querySelector('.bb-service-explore__list');

		if (!toggle || !list) return;

		var label = toggle.querySelector('.bb-service-explore__toggle-label');
		var showMoreText = toggle.getAttribute('data-show-more') || 'show more';
		var showLessText = toggle.getAttribute('data-show-less') || 'show less';

		function setExpanded(isExpanded, animate) {
			section.classList.toggle('is-expanded', isExpanded);
			toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');

			if (label) {
				label.textContent = isExpanded ? showLessText : showMoreText;
			}

			if (reduceMotion) {
				list.style.transition = 'none';
			}

			if (!animate || reduceMotion) {
				list.style.maxHeight = isExpanded ? list.scrollHeight + 'px' : '0px';
				list.style.opacity = isExpanded ? '1' : '0';
				return;
			}

			if (isExpanded) {
				list.style.maxHeight = '0px';
				list.style.opacity = '0';
				requestAnimationFrame(function () {
					list.style.maxHeight = list.scrollHeight + 'px';
					list.style.opacity = '1';
				});
				return;
			}

			list.style.maxHeight = list.scrollHeight + 'px';
			list.style.opacity = '1';
			requestAnimationFrame(function () {
				list.style.maxHeight = '0px';
				list.style.opacity = '0';
			});
		}

		// Start collapsed on load; keeps content visible when JS is unavailable.
		setExpanded(false, false);

		toggle.addEventListener('click', function () {
			var isExpanded = toggle.getAttribute('aria-expanded') === 'true';
			setExpanded(!isExpanded, true);
		});

		window.addEventListener('resize', function () {
			if (toggle.getAttribute('aria-expanded') === 'true') {
				list.style.maxHeight = list.scrollHeight + 'px';
			}
		});
	});

	updateDesktopSectionNumbers();
	window.addEventListener('load', updateDesktopSectionNumbers);
	window.addEventListener('resize', updateDesktopSectionNumbers);
})();
