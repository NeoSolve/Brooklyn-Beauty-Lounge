"use strict";

(function () {
	var section = document.querySelector("[data-faq]");
	if (!section) return;

	var items = Array.prototype.slice.call(section.querySelectorAll("[data-faq-item]"));
	if (!items.length) return;

	function getPanel(item) {
		return item.querySelector("[data-faq-panel]");
	}

	function openItem(item) {
		var panel = getPanel(item);
		if (!panel) return;

		item.classList.add("is-open");
		panel.hidden = false;
		panel.style.maxHeight = "";
	}

	items.forEach(function (item) {
		openItem(item);
	});
})();
