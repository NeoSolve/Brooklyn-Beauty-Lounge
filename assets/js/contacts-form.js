"use strict";

(function () {
	var CONTACT_FORM_SELECTOR = ".bb-contacts-form";
	var PHONE_MASK = "+718-000-0000";
	var customSelectEventsReady = false;

	function containsLetter(value) {
		return /\p{L}/u.test(String(value || ""));
	}

	function removeLetters(value) {
		return String(value || "").replace(/\p{L}+/gu, "");
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

	function initPhoneMask(scope) {
		var formScope = scope || document;
		var phoneInputs = formScope.querySelectorAll(".bb-contacts-form input[type='tel']");
		if (!phoneInputs.length) return;

		phoneInputs.forEach(function (input) {
			if (input.dataset.bbPhoneMaskReady === "1") return;
			input.dataset.bbPhoneMaskReady = "1";

			input.setAttribute("placeholder", PHONE_MASK);
			input.setAttribute("inputmode", "tel");
			input.setAttribute("autocomplete", "tel-national");

			input.addEventListener("keydown", function (event) {
				if (event.ctrlKey || event.metaKey || event.altKey || event.key.length !== 1) return;
				if (containsLetter(event.key)) {
					event.preventDefault();
				}
			});

			input.addEventListener("input", function () {
				var sanitizedValue = removeLetters(input.value);
				if (sanitizedValue !== input.value) {
					input.value = sanitizedValue;
				}
			});
		});
	}

	function initContactsForm(scope) {
		moveSubmitIntoDisclaimer(scope);
		initPhoneMask(scope);
		initEmailPlaceholder(scope);
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
