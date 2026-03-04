/**
 * Single service — Explore block: show more/show less toggles.
 */
(function () {
	var sections = document.querySelectorAll('.single-service .bb-service-explore__section');
	if (!sections.length) return;

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
})();
