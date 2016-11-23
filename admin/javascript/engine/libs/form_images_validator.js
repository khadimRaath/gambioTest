/* --------------------------------------------------------------
 form_images_validator.js 2016-08-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.form_images_validator = jse.libs.form_images_validator || {};

/**
 * Form Images Validator Library.
 *
 * Provides callback methods that can be overridden to provide custom functionality.
 *
 * @module Admin/Libs/form_images_validator
 * @exports jse.libs.form_images_validator
 */
(function(exports) {
	
	'use strict';
	
	/**
	 * Provides callback methods, that can be overridden.
	 *
	 * @type {Object}
	 */
	exports.callbackMethods = {
		/**
		 * Invoked callback method on validation errors.
		 *
		 * @param {jQuery.Event} event Triggered form submit event.
		 * @param {HTMLElement[]} errors Array containing the input fields that failed on the validation.
		 * @abstract
		 */
		onValidationError(event, errors) {
		},

		/**
		 * Invoked callback method on validation success.
		 *
		 * @param {jQuery.Event} event Triggered form submit event.
		 * @abstract
		 */
		onValidationSuccess(event) {
		}
	};
}(jse.libs.form_images_validator));
