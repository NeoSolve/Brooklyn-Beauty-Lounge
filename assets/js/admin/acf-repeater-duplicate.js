(function ($) {
	'use strict';

	var TARGET_REPEATER_KEYS = [
		'field_brooklyn_beauty_single_service_prices_sections',
		'field_brooklyn_beauty_single_service_prices_sections_cards',
		'field_brooklyn_beauty_single_service_prices_cards'
	];

	function injectDuplicateButtons(context) {
		var $context = context ? $(context) : $(document);

		$context.find('.acf-field-repeater').each(function () {
			var $repeaterField = $(this);
			var fieldKey = $repeaterField.data('key');

			if (TARGET_REPEATER_KEYS.indexOf(fieldKey) === -1) {
				return;
			}

			$repeaterField.find('.acf-row:not(.acf-clone)').each(function () {
				var $row = $(this);
				var $handle = $row.find('.acf-row-handle.remove');

				if (!$handle.length || $handle.find('.bb-acf-duplicate-row').length) {
					return;
				}

				var $removeButton = $handle.find('a[data-event="remove-row"]').first();
				var $duplicateButton = $('<a href="#" class="button button-small bb-acf-duplicate-row" data-event="duplicate-row">Duplicate</a>');

				if ($removeButton.length) {
					$duplicateButton.insertBefore($removeButton);
				} else {
					$handle.append($duplicateButton);
				}
			});
		});
	}

	function boot() {
		if (typeof acf === 'undefined') {
			return;
		}

		acf.addAction('ready', injectDuplicateButtons);
		acf.addAction('append', injectDuplicateButtons);
	}

	boot();
	$(window).on('load', boot);
})(jQuery);
