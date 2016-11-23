/* --------------------------------------------------------------
 images_validation.js 2016-08-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Product Images Validation Controller
 *
 * This controller overrides the default callback methods of the form image validation library.
 * Before submitting the form, all file extensions of the selected images will be validated.
 * On failed validation, the respective elements will be marked.
 *
 * @module Controllers/images_validation
 */
gx.controllers.module(
	'images_validation',

	[
		`${gx.source}/libs/form_images_validator`,
		`${gx.source}/libs/info_messages`
	],

	function() {
			
		'use strict';
			
		// Module element, which represents a form.
		const $this = $(this);

		// Shortcut to form images validator library.
		const imageValidatorLib = jse.libs.form_images_validator;

		// Shortcut to info messages library.
		const infoMsgLib = jse.libs.info_messages;

		// Module object.
		const module = {};

		// CSS class name of the image container error alert.
		const invalidImageMsgAlertClassName = 'invalid-image-alert';

		/**
		 * Clears any errors message.
		 *
		 * @private
		 */
		const _clearErrors = () => {
			// Remove all messages from message stack.
			infoMsgLib.truncate();

			// Remove all image container error alerts.
			$this
				.find(invalidImageMsgAlertClassName)
				.remove();
		};

		/**
		 * Marks an image container with an error.
		 *
		 * @param {HTMLElement} fileInputElement Current iteration element.
		 * @private
		 */
		const _markError = fileInputElement => {
			// Image container element.
			const $wrapper = $(fileInputElement).parents('.product-image-wrapper');

			// Create image container alert element.
			const $errorMsg = $('<div/>', {
				class: `alert alert-danger ${invalidImageMsgAlertClassName}`,
				text: jse.core.lang.translate('TXT_IMAGE_INVALID_EXTENSION_ERROR', 'categories')
			});

			// Put alert into image container.
			$wrapper.prepend($errorMsg);
		};

		/**
		 * Marks the failed elements and prevents the form submit.
		 *
		 * @param {jQuery.Event} event Triggered form submit event.
		 * @param {HTMLElement[]} failedElements Array containing the input fields that failed on the validation.
		 * @private
		 */
		const _onValidationError = (event, failedElements) => {
			// Prevent form submit.
			event.preventDefault();

			// Clear error messages.
			_clearErrors();

			// Add error to message stack.
			infoMsgLib.addError(jse.core.lang.translate('TXT_FORM_SUBMIT_IMAGE_ERROR', 'categories'));

			// Mark errors.
			failedElements.forEach(element => _markError(element));

			// Scroll to top.
			window.scrollTo(0, 0);
		};

		/**
		 * Clears any error message while submitting form.
		 *
		 * @private
		 */
		const _onValidationSuccess = () => _clearErrors();

		// Module initialize function.
		module.init = done => {
			// Override form images validator methods.
			imageValidatorLib.callbackMethods.onValidationError = _onValidationError;
			imageValidatorLib.callbackMethods.onValidationSuccess = _onValidationSuccess;

			// Finish initialization.
			done();
		};

		// Return data to module engine.
		return module;
	});
