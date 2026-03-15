"use strict";

(function () {
	var CONTACT_FORM_SELECTOR = ".bb-contacts-form";
	var PHONE_PREFIX = "+1 ";
	var PHONE_PLACEHOLDER = "+1 XXX XXX XXXX";
	var PHONE_DIGITS_REQUIRED = 10;
	var customSelectEventsReady = false;

	function containsLetter(value) {
		return /\p{L}/u.test(String(value || ""));
	}

	function removeLetters(value) {
		return String(value || "").replace(/\p{L}+/gu, "");
	}

	function extractDigits(value) {
		var str = String(value || "");
		if (str.indexOf(PHONE_PREFIX) === 0) {
			str = str.slice(PHONE_PREFIX.length);
		}
		return str.replace(/\D/g, "");
	}

	function formatPhone(digits) {
		if (digits.length === 0) return PHONE_PREFIX;
		if (digits.length <= 3) return PHONE_PREFIX + digits;
		if (digits.length <= 6) return PHONE_PREFIX + digits.slice(0, 3) + " " + digits.slice(3);
		return PHONE_PREFIX + digits.slice(0, 3) + " " + digits.slice(3, 6) + " " + digits.slice(6, 10);
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
		if (option && String(option.value || "").trim() === "") {
			trigger.classList.add("is-placeholder");
		} else {
			trigger.classList.remove("is-placeholder");
		}
	}

	function initCareerSingleChoiceFullFields(scope) {
		var formScope = scope || document;
		var form = formScope.querySelector("#career-questionnaire-form .wpcf7-form");
		if (!form) return;

		var fullFields = form.querySelectorAll(".bb-cq-field.bb-cq-field--full");
		fullFields.forEach(function (field) {
			var checkboxes = field.querySelectorAll('input[type="checkbox"]');
			if (!checkboxes.length) return;

			checkboxes.forEach(function (checkbox) {
				if (checkbox.dataset.bbSingleChoiceReady === "1") return;
				checkbox.dataset.bbSingleChoiceReady = "1";

				checkbox.addEventListener("change", function () {
					if (!this.checked) return;
					checkboxes.forEach(function (other) {
						if (other !== checkbox) {
							other.checked = false;
						}
					});
				});
			});
		});
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

	function isValidEmail(value) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(String(value || "").trim());
	}

	function showFieldError(input, message) {
		var wrapper = input.closest(".wpcf7-form-control-wrap");
		if (!wrapper) return;
		var existing = wrapper.querySelector(".wpcf7-not-valid-tip.bb-field-error");
		if (existing) existing.remove();

		input.classList.add("wpcf7-not-valid");
		input.setAttribute("aria-invalid", "true");

		var tip = document.createElement("span");
		tip.className = "wpcf7-not-valid-tip bb-field-error";
		tip.setAttribute("role", "alert");
		tip.textContent = message;
		wrapper.appendChild(tip);
	}

	function clearFieldError(input) {
		var wrapper = input.closest(".wpcf7-form-control-wrap");
		if (!wrapper) return;
		var existing = wrapper.querySelector(".wpcf7-not-valid-tip.bb-field-error");
		if (existing) existing.remove();
		input.classList.remove("wpcf7-not-valid");
		input.removeAttribute("aria-invalid");
	}

	function initEmailValidation(scope) {
		var formScope = scope || document;
		var emailInputs = formScope.querySelectorAll(".bb-contacts-form input[type='email']");
		if (!emailInputs.length) return;

		emailInputs.forEach(function (input) {
			if (input.dataset.bbEmailPlaceholderReady === "1") return;
			input.dataset.bbEmailPlaceholderReady = "1";
			input.setAttribute("placeholder", "Example@mail.com");

			input.addEventListener("input", function () {
				if (input.value.trim() !== "" && isValidEmail(input.value)) {
					clearFieldError(input);
				}
			});

			var form = input.closest("form");
			if (form && !form.dataset.bbEmailValidateReady) {
				form.dataset.bbEmailValidateReady = "1";
				form.addEventListener("submit", function (event) {
					var allEmails = form.querySelectorAll("input[type='email']");
					var hasError = false;
					allEmails.forEach(function (emailField) {
						var val = emailField.value.trim();
						if (val !== "" && !isValidEmail(val)) {
							showFieldError(emailField, "Please enter a valid email address.");
							hasError = true;
						}
					});
					if (hasError) {
						event.preventDefault();
						event.stopImmediatePropagation();
					}
				});
			}
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

	function showPhoneError(input, message) {
		var wrapper = input.closest(".wpcf7-form-control-wrap");
		if (!wrapper) return;
		var existing = wrapper.querySelector(".wpcf7-not-valid-tip.bb-phone-error");
		if (existing) existing.remove();

		input.classList.add("wpcf7-not-valid");
		input.setAttribute("aria-invalid", "true");

		var tip = document.createElement("span");
		tip.className = "wpcf7-not-valid-tip bb-phone-error";
		tip.setAttribute("role", "alert");
		tip.textContent = message;
		wrapper.appendChild(tip);
	}

	function clearPhoneError(input) {
		var wrapper = input.closest(".wpcf7-form-control-wrap");
		if (!wrapper) return;
		var existing = wrapper.querySelector(".wpcf7-not-valid-tip.bb-phone-error");
		if (existing) existing.remove();
		input.classList.remove("wpcf7-not-valid");
		input.removeAttribute("aria-invalid");
	}

	function enforcePrefix(input) {
		var val = input.value;
		if (val.indexOf(PHONE_PREFIX) !== 0) {
			var digits = extractDigits(val).slice(0, PHONE_DIGITS_REQUIRED);
			input.value = formatPhone(digits);
		}
		if (input.selectionStart < PHONE_PREFIX.length) {
			input.setSelectionRange(PHONE_PREFIX.length, PHONE_PREFIX.length);
		}
	}

	function initPhoneMask(scope) {
		var formScope = scope || document;
		var phoneInputs = formScope.querySelectorAll(".bb-contacts-form input[type='tel']");
		if (!phoneInputs.length) return;

		phoneInputs.forEach(function (input) {
			if (input.dataset.bbPhoneMaskReady === "1") return;
			input.dataset.bbPhoneMaskReady = "1";

			input.setAttribute("placeholder", PHONE_PLACEHOLDER);
			input.setAttribute("inputmode", "tel");
			input.setAttribute("autocomplete", "tel-national");
			input.setAttribute("maxlength", "16");

			input.addEventListener("focus", function () {
				if (input.value === "") {
					input.value = PHONE_PREFIX;
				}
				window.setTimeout(function () { enforcePrefix(input); }, 0);
			});

			input.addEventListener("blur", function () {
				if (extractDigits(input.value).length === 0) {
					input.value = "";
				}
			});

			input.addEventListener("click", function () {
				enforcePrefix(input);
			});

			input.addEventListener("keydown", function (event) {
				var cursorPos = input.selectionStart || 0;

				if (event.key === "Backspace" && cursorPos <= PHONE_PREFIX.length) {
					event.preventDefault();
					return;
				}
				if (event.key === "Delete" && cursorPos < PHONE_PREFIX.length) {
					event.preventDefault();
					return;
				}
				if (event.key === "ArrowLeft" && cursorPos <= PHONE_PREFIX.length) {
					event.preventDefault();
					return;
				}

				if (event.ctrlKey || event.metaKey || event.altKey || event.key.length !== 1) return;
				if (containsLetter(event.key)) {
					event.preventDefault();
					return;
				}
				if (/\d/.test(event.key) && extractDigits(input.value).length >= PHONE_DIGITS_REQUIRED) {
					event.preventDefault();
				}
			});

			input.addEventListener("input", function () {
				var digits = extractDigits(input.value).slice(0, PHONE_DIGITS_REQUIRED);
				var formatted = formatPhone(digits);
				if (input.value !== formatted) {
					input.value = formatted;
				}
				if (digits.length === PHONE_DIGITS_REQUIRED) {
					clearPhoneError(input);
				}
			});

			input.addEventListener("paste", function (event) {
				event.preventDefault();
				var pasted = (event.clipboardData || window.clipboardData || "").getData("text");
				var rawDigits = String(pasted || "").replace(/\D/g, "");
				if (rawDigits.length === 11 && rawDigits[0] === "1") {
					rawDigits = rawDigits.slice(1);
				}
				input.value = formatPhone(rawDigits.slice(0, PHONE_DIGITS_REQUIRED));
				input.dispatchEvent(new Event("input", { bubbles: true }));
			});

			var form = input.closest("form");
			if (form && !form.dataset.bbPhoneValidateReady) {
				form.dataset.bbPhoneValidateReady = "1";
				form.addEventListener("submit", function (event) {
					var telInputs = form.querySelectorAll("input[type='tel']");
					var hasError = false;
					telInputs.forEach(function (tel) {
						var digits = extractDigits(tel.value);
						if (digits.length > 0 && digits.length < PHONE_DIGITS_REQUIRED) {
							showPhoneError(tel, "Please enter a 10-digit phone number.");
							hasError = true;
						}
					});
					if (hasError) {
						event.preventDefault();
						event.stopImmediatePropagation();
					}
				});
			}
		});
	}

	function initContactsForm(scope) {
		moveSubmitIntoDisclaimer(scope);
		initPhoneMask(scope);
		initEmailValidation(scope);
		initCareerSingleChoiceFullFields(scope);
		initCareerCustomSelects(scope);
	}

	function tryInit() {
		var sections = document.querySelectorAll(CONTACT_FORM_SELECTOR);
		if (!sections.length) return;
		sections.forEach(function (section) {
			if (section.querySelector(".wpcf7-form")) {
				initContactsForm(section);
			}
		});
	}

	tryInit();

	document.addEventListener("wpcf7submit", function (event) {
		var form = event.target;
		if (!form) return;

		var contactsSection = form.closest(CONTACT_FORM_SELECTOR);
		if (!contactsSection) return;

		window.setTimeout(function () {
			initContactsForm(contactsSection);
		});
	});

	var observerTarget = document.querySelector(CONTACT_FORM_SELECTOR);
	if (observerTarget) {
		var observer = new MutationObserver(function () {
			if (observerTarget.querySelector(".wpcf7-form select")) {
				tryInit();
				observer.disconnect();
			}
		});
		observer.observe(observerTarget, { childList: true, subtree: true });
	}
})();
