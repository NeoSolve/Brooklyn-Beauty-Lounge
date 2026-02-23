"use strict";

(function () {
	var CONTACT_FORM_SELECTOR = ".bb-contacts-form";
	var PREFIX = "718";
	var MAX_LOCAL_DIGITS = 7;

	function moveSubmitIntoDisclaimer(scope) {
		var formScope = scope || document;
		var grids = formScope.querySelectorAll(".bb-contacts-form .cf7-grid");

		grids.forEach(function (grid) {
			var disclaimer = grid.querySelector(":scope > .cf7-disclaimer.cf7-full");
			if (!disclaimer) return;

			var children = Array.prototype.slice.call(grid.children);
			var submitContainer = children.find(function (child) {
				return child.querySelector("input[type='submit'], button[type='submit']");
			});

			if (!submitContainer || submitContainer === disclaimer || disclaimer.contains(submitContainer)) {
				return;
			}

			disclaimer.appendChild(submitContainer);
			disclaimer.classList.add("cf7-disclaimer--with-submit");
			submitContainer.classList.add("cf7-submit-inside-disclaimer");
		});
	}

	function formatPhone(rawValue) {
		var digits = String(rawValue || "").replace(/\D/g, "");
		var localDigits = digits.indexOf(PREFIX) === 0 ? digits.slice(PREFIX.length) : digits;
		localDigits = localDigits.slice(0, MAX_LOCAL_DIGITS);

		var formatted = "+" + PREFIX;

		if (localDigits.length > 0) {
			formatted += "-" + localDigits.slice(0, 3);
		}

		if (localDigits.length > 3) {
			formatted += "-" + localDigits.slice(3, 7);
		}

		return formatted;
	}

	function initPhoneMask(scope) {
		var formScope = scope || document;
		var phoneInputs = formScope.querySelectorAll(".bb-contacts-form input[type='tel']");
		if (!phoneInputs.length) return;

		phoneInputs.forEach(function (input) {
			if (input.dataset.bbPhoneMaskReady === "1") return;
			input.dataset.bbPhoneMaskReady = "1";

			input.setAttribute("placeholder", "+718-000-0000");
			input.setAttribute("inputmode", "numeric");
			input.value = formatPhone(input.value);

			input.addEventListener("focus", function () {
				if (!input.value) {
					input.value = "+718";
				}
			});

			input.addEventListener("input", function () {
				input.value = formatPhone(input.value);
			});
		});
	}

	function initContactsForm(scope) {
		moveSubmitIntoDisclaimer(scope);
		initPhoneMask(scope);
	}

	if (document.querySelector(CONTACT_FORM_SELECTOR)) {
		initContactsForm(document);
	}

	document.addEventListener("wpcf7submit", function (event) {
		var form = event.target;
		if (!form) return;

		var contactsSection = form.closest(CONTACT_FORM_SELECTOR);
		if (!contactsSection) return;

		window.setTimeout(function () {
			initContactsForm(contactsSection);
		});
	});
})();
