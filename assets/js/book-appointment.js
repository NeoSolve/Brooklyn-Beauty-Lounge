"use strict";

(function () {
	if (!document.body.classList.contains("home")) return;

	var section = document.querySelector("[data-book-appointment]");
	if (!section) return;

	var button = section.querySelector("[data-book-scroll]");
	if (!button) return;

	button.addEventListener("click", function (event) {
		var href = button.getAttribute("href");
		if (!href || href.charAt(0) !== "#") return;

		var target = document.querySelector(href);
		if (!target || target === section) return;

		event.preventDefault();
		target.scrollIntoView({
			behavior: "smooth",
			block: "start",
		});
	});
})();
