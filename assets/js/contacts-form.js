"use strict";

(function () {
	var CONTACT_FORM_SELECTOR = ".bb-contacts-form";
	var PHONE_MASK = "+1-___-___-____";
	var customSelectEventsReady = false;

	function getDigits(value) {
		return String(value || "").replace(/\D/g, "").slice(0, 11);
	}

	function formatPhoneByMask(digits) {
		var normalized = String(digits || "").replace(/\D/g, "").slice(0, 11);
		var localDigits = normalized;
		var partA = "";
		var partB = "";
		var partC = "";
		var result = "";

		if (!normalized.length) return "";

		if (11 === normalized.length && "1" === normalized.charAt(0)) {
			localDigits = normalized.slice(1);
		} else {
			localDigits = normalized.slice(0, 10);
		}

		partA = localDigits.slice(0, 3);
		partB = localDigits.slice(3, 6);
		partC = localDigits.slice(6, 10);

		result = "+1-" + partA;

		if (partB.length) {
			result += "-" + partB;
		}

		if (partC.length) {
			result += "-" + partC;
		}

		return result;
	}

	function normalizePhoneInputValue(input) {
		if (!input) return;
		var digits = getDigits(input.value);
		input.value = formatPhoneByMask(digits);
	}

	function onPhonePaste(event) {
		var input = event.currentTarget;
		if (!input) return;

		event.preventDefault();
		var clipboard = event.clipboardData ? event.clipboardData.getData("text") : "";
		var pastedDigits = getDigits(clipboard);

		input.value = formatPhoneByMask(pastedDigits);
	}

	function closeCustomSelect(wrapper) {
		if (!wrapper) return;
		wrapper.classList.remove("is-open");
	}

	function openCustomSelect(wrapper) {
		if (!wrapper) return;
		wrapper.classList.add("is-open");
	}

	function updateCustomSelectLabel(nativeSelect, trigger) {
		if (!nativeSelect || !trigger) return;
		var option = nativeSelect.options[nativeSelect.selectedIndex];
		trigger.textContent = option ? option.text : "";
	}

	function initCareerCustomSelects(scope) {
		var formScope = scope || document;
		var selects = formScope.querySelectorAll("#career-questionnaire-form .wpcf7-form select");
		if (!selects.length) return;

		selects.forEach(function (select) {
			if (select.dataset.bbCustomSelectReady === "1") return;
			select.dataset.bbCustomSelectReady = "1";

			var wrapper = document.createElement("div");
			wrapper.className = "bb-cq-select";

			var trigger = document.createElement("button");
			trigger.type = "button";
			trigger.className = "bb-cq-select__trigger";
			trigger.setAttribute("aria-haspopup", "listbox");
			trigger.setAttribute("aria-expanded", "false");

			var list = document.createElement("div");
			list.className = "bb-cq-select__menu";
			list.setAttribute("role", "listbox");

			Array.prototype.slice.call(select.options).forEach(function (option, index) {
				var item = document.createElement("button");
				item.type = "button";
				item.className = "bb-cq-select__option";
				item.setAttribute("role", "option");
				item.textContent = option.text;
				item.dataset.value = option.value;

				if (option.selected) {
					item.classList.add("is-selected");
					item.setAttribute("aria-selected", "true");
				}

				item.addEventListener("click", function () {
					select.selectedIndex = index;
					select.dispatchEvent(new Event("change", { bubbles: true }));

					Array.prototype.slice.call(list.querySelectorAll(".bb-cq-select__option")).forEach(function (btn) {
						btn.classList.remove("is-selected");
						btn.removeAttribute("aria-selected");
					});

					item.classList.add("is-selected");
					item.setAttribute("aria-selected", "true");
					updateCustomSelectLabel(select, trigger);
					closeCustomSelect(wrapper);
					trigger.setAttribute("aria-expanded", "false");
				});

				list.appendChild(item);
			});

			var parent = select.parentNode;
			parent.insertBefore(wrapper, select);
			wrapper.appendChild(select);
			wrapper.appendChild(trigger);
			wrapper.appendChild(list);

			select.classList.add("bb-cq-select__native");
			updateCustomSelectLabel(select, trigger);

			trigger.addEventListener("click", function () {
				var shouldOpen = !wrapper.classList.contains("is-open");
				document.querySelectorAll("#career-questionnaire-form .bb-cq-select.is-open").forEach(function (opened) {
					closeCustomSelect(opened);
					var openedTrigger = opened.querySelector(".bb-cq-select__trigger");
					if (openedTrigger) openedTrigger.setAttribute("aria-expanded", "false");
				});

				if (shouldOpen) {
					openCustomSelect(wrapper);
					trigger.setAttribute("aria-expanded", "true");
				} else {
					trigger.setAttribute("aria-expanded", "false");
				}
			});

			select.addEventListener("change", function () {
				updateCustomSelectLabel(select, trigger);
			});
		});

		if (!customSelectEventsReady) {
			customSelectEventsReady = true;

			document.addEventListener("click", function (event) {
				document.querySelectorAll("#career-questionnaire-form .bb-cq-select.is-open").forEach(function (opened) {
					if (!opened.contains(event.target)) {
						closeCustomSelect(opened);
						var openedTrigger = opened.querySelector(".bb-cq-select__trigger");
						if (openedTrigger) openedTrigger.setAttribute("aria-expanded", "false");
					}
				});
			});

			document.addEventListener("keydown", function (event) {
				if ("Escape" !== event.key) return;
				document.querySelectorAll("#career-questionnaire-form .bb-cq-select.is-open").forEach(function (opened) {
					closeCustomSelect(opened);
					var openedTrigger = opened.querySelector(".bb-cq-select__trigger");
					if (openedTrigger) openedTrigger.setAttribute("aria-expanded", "false");
				});
			});
		}
	}

	function initEmailPlaceholder(scope) {
		var formScope = scope || document;
		var emailInputs = formScope.querySelectorAll(".bb-contacts-form input[type='email']");
		if (!emailInputs.length) return;

		emailInputs.forEach(function (input) {
			if (input.dataset.bbEmailPlaceholderReady === "1") return;
			input.dataset.bbEmailPlaceholderReady = "1";
			input.setAttribute("placeholder", "Example@mail.com");
		});
	}

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

			input.setAttribute("placeholder", PHONE_MASK);
			input.setAttribute("maxlength", String(PHONE_MASK.length));
			input.setAttribute("inputmode", "tel");
			input.setAttribute("autocomplete", "tel-national");

			normalizePhoneInputValue(input);

			input.addEventListener("input", function () {
				normalizePhoneInputValue(input);
			});

			input.addEventListener("paste", onPhonePaste);
		});
	}

	function initContactsForm(scope) {
		moveSubmitIntoDisclaimer(scope);
		initPhonePlaceholder(scope);
		initEmailPlaceholder(scope);
		initCareerCustomSelects(scope);
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
