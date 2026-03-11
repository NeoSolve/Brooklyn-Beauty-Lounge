"use strict";

(function () {
	var section = document.querySelector("[data-faq]");
	if (!section) return;

	var items = Array.prototype.slice.call(section.querySelectorAll("[data-faq-item]"));
	if (!items.length) return;

	function getPanel(item) {
		return item.querySelector("[data-faq-panel]");
	}

	function getTrigger(item) {
		return item.querySelector("[data-faq-trigger]");
	}

	function openItem(item) {
		var panel = getPanel(item);
		var trigger = getTrigger(item);
		if (!panel || !trigger) return;

		item.classList.add("is-open");
		trigger.setAttribute("aria-expanded", "true");
		panel.hidden = false;
		panel.style.maxHeight = "";
	}

	items.forEach(function (item) {
		openItem(item);
	});
})();
