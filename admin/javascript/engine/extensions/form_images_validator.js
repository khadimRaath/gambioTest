/* --------------------------------------------------------------
 form_images_validator.js 2016-08-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Form images validator.
 *
 * Searches for file inputs in a form and validates their file extensions.
 * For a passed/failed validation, a provided callback will be invoked.
 *
 * The validation is triggered on form submit.
 *
 * Consider the respective library file for custom callback method definitions.
 *
 * Possible optional options are:
 *      - valid_extensions {String} Valid file extensions (pipe-separated, e.g.: 'gif|jpg|tiff')
 *      - selector {String} File input selector.
 *
 * @module Admin/Extensions/form_images_validator
 */
gx.extensions.module(
	'form_images_validator',

	[
		`${gx.source}/libs/form_images_validator`
	],

	function(data) {
			
		'use strict';
			
		// Module element, which represents a form.
		const $this = $(this);

		// Module default parameters.
		const defaults = {
			// Valid image file extensions (pipe separated).
			validExtensions: 'gif|jpg|jpeg|tiff|png',

			// File input fields selector.
			selector: '[type="file"]'
		};

		// Shortcut to extension library.
		const library = jse.libs.form_images_validator;

		// Module options.
		const options = $.extend(true, {}, defaults, data);

		// Erroneous elements array.
		let failedElements = [];

		// Module object.
		const module = {};

		// File extension validator regex.
		const regex = new RegExp(`\.(${options.validExtensions})`, 'i');

		// Iterator function for the file input fields.
		const _validationIterator = function() {
			// Omit element if current iteration element does not have a `files` property
			// or if no file has been set.
			if (!this.files || !this.files[0]) {
				return;
			}

			// Get file name.this
			const fileName = this.files[0].name;

			// Validate file name.
			const validationPassed = regex.test(fileName);

			// Push this element to the map array if validation has failed. Otherwise the element is omitted.
			if (!validationPassed) {
				failedElements.push(this);
			}
		};

		// Handler for the form submit event.
		// Iterates over each file input field and validates its file name.
		const _onFormSubmit = event => {
			// Empty erroneous elements array.
			failedElements = [];

			// Validate.
			$this
				.find(options.selector)
				.each(_validationIterator);

			// Invoke respective callback method.
			if (failedElements.length) {
				library.callbackMethods.onValidationError(event, failedElements);
			} else {
				library.callbackMethods.onValidationSuccess(event);
			}
		};

		// Module initialize function.
		module.init = done => {
			$this.on('submit', _onFormSubmit);
			done();
		};

		// Return data to module engine.
		return module;
	}
);