'use strict';

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
gx.controllers.module('images_validation', [gx.source + '/libs/form_images_validator', gx.source + '/libs/info_messages'], function () {

	'use strict';

	// Module element, which represents a form.

	var $this = $(this);

	// Shortcut to form images validator library.
	var imageValidatorLib = jse.libs.form_images_validator;

	// Shortcut to info messages library.
	var infoMsgLib = jse.libs.info_messages;

	// Module object.
	var module = {};

	// CSS class name of the image container error alert.
	var invalidImageMsgAlertClassName = 'invalid-image-alert';

	/**
  * Clears any errors message.
  *
  * @private
  */
	var _clearErrors = function _clearErrors() {
		// Remove all messages from message stack.
		infoMsgLib.truncate();

		// Remove all image container error alerts.
		$this.find(invalidImageMsgAlertClassName).remove();
	};

	/**
  * Marks an image container with an error.
  *
  * @param {HTMLElement} fileInputElement Current iteration element.
  * @private
  */
	var _markError = function _markError(fileInputElement) {
		// Image container element.
		var $wrapper = $(fileInputElement).parents('.product-image-wrapper');

		// Create image container alert element.
		var $errorMsg = $('<div/>', {
			class: 'alert alert-danger ' + invalidImageMsgAlertClassName,
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
	var _onValidationError = function _onValidationError(event, failedElements) {
		// Prevent form submit.
		event.preventDefault();

		// Clear error messages.
		_clearErrors();

		// Add error to message stack.
		infoMsgLib.addError(jse.core.lang.translate('TXT_FORM_SUBMIT_IMAGE_ERROR', 'categories'));

		// Mark errors.
		failedElements.forEach(function (element) {
			return _markError(element);
		});

		// Scroll to top.
		window.scrollTo(0, 0);
	};

	/**
  * Clears any error message while submitting form.
  *
  * @private
  */
	var _onValidationSuccess = function _onValidationSuccess() {
		return _clearErrors();
	};

	// Module initialize function.
	module.init = function (done) {
		// Override form images validator methods.
		imageValidatorLib.callbackMethods.onValidationError = _onValidationError;
		imageValidatorLib.callbackMethods.onValidationSuccess = _onValidationSuccess;

		// Finish initialization.
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInByb2R1Y3QvaW1hZ2VzX3ZhbGlkYXRpb24uanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsInNvdXJjZSIsIiR0aGlzIiwiJCIsImltYWdlVmFsaWRhdG9yTGliIiwianNlIiwibGlicyIsImZvcm1faW1hZ2VzX3ZhbGlkYXRvciIsImluZm9Nc2dMaWIiLCJpbmZvX21lc3NhZ2VzIiwiaW52YWxpZEltYWdlTXNnQWxlcnRDbGFzc05hbWUiLCJfY2xlYXJFcnJvcnMiLCJ0cnVuY2F0ZSIsImZpbmQiLCJyZW1vdmUiLCJfbWFya0Vycm9yIiwiJHdyYXBwZXIiLCJmaWxlSW5wdXRFbGVtZW50IiwicGFyZW50cyIsIiRlcnJvck1zZyIsImNsYXNzIiwidGV4dCIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwicHJlcGVuZCIsIl9vblZhbGlkYXRpb25FcnJvciIsImV2ZW50IiwiZmFpbGVkRWxlbWVudHMiLCJwcmV2ZW50RGVmYXVsdCIsImFkZEVycm9yIiwiZm9yRWFjaCIsImVsZW1lbnQiLCJ3aW5kb3ciLCJzY3JvbGxUbyIsIl9vblZhbGlkYXRpb25TdWNjZXNzIiwiaW5pdCIsImNhbGxiYWNrTWV0aG9kcyIsIm9uVmFsaWRhdGlvbkVycm9yIiwib25WYWxpZGF0aW9uU3VjY2VzcyIsImRvbmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7O0FBU0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLG1CQURELEVBR0MsQ0FDSUYsR0FBR0csTUFEUCxrQ0FFSUgsR0FBR0csTUFGUCx5QkFIRCxFQVFDLFlBQVc7O0FBRVY7O0FBRUE7O0FBQ0EsS0FBTUMsUUFBUUMsRUFBRSxJQUFGLENBQWQ7O0FBRUE7QUFDQSxLQUFNQyxvQkFBb0JDLElBQUlDLElBQUosQ0FBU0MscUJBQW5DOztBQUVBO0FBQ0EsS0FBTUMsYUFBYUgsSUFBSUMsSUFBSixDQUFTRyxhQUE1Qjs7QUFFQTtBQUNBLEtBQU1ULFNBQVMsRUFBZjs7QUFFQTtBQUNBLEtBQU1VLGdDQUFnQyxxQkFBdEM7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsZUFBZSxTQUFmQSxZQUFlLEdBQU07QUFDMUI7QUFDQUgsYUFBV0ksUUFBWDs7QUFFQTtBQUNBVixRQUNFVyxJQURGLENBQ09ILDZCQURQLEVBRUVJLE1BRkY7QUFHQSxFQVJEOztBQVVBOzs7Ozs7QUFNQSxLQUFNQyxhQUFhLFNBQWJBLFVBQWEsbUJBQW9CO0FBQ3RDO0FBQ0EsTUFBTUMsV0FBV2IsRUFBRWMsZ0JBQUYsRUFBb0JDLE9BQXBCLENBQTRCLHdCQUE1QixDQUFqQjs7QUFFQTtBQUNBLE1BQU1DLFlBQVloQixFQUFFLFFBQUYsRUFBWTtBQUM3QmlCLGtDQUE2QlYsNkJBREE7QUFFN0JXLFNBQU1oQixJQUFJaUIsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsbUNBQXhCLEVBQTZELFlBQTdEO0FBRnVCLEdBQVosQ0FBbEI7O0FBS0E7QUFDQVIsV0FBU1MsT0FBVCxDQUFpQk4sU0FBakI7QUFDQSxFQVpEOztBQWNBOzs7Ozs7O0FBT0EsS0FBTU8scUJBQXFCLFNBQXJCQSxrQkFBcUIsQ0FBQ0MsS0FBRCxFQUFRQyxjQUFSLEVBQTJCO0FBQ3JEO0FBQ0FELFFBQU1FLGNBQU47O0FBRUE7QUFDQWxCOztBQUVBO0FBQ0FILGFBQVdzQixRQUFYLENBQW9CekIsSUFBSWlCLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLDZCQUF4QixFQUF1RCxZQUF2RCxDQUFwQjs7QUFFQTtBQUNBSSxpQkFBZUcsT0FBZixDQUF1QjtBQUFBLFVBQVdoQixXQUFXaUIsT0FBWCxDQUFYO0FBQUEsR0FBdkI7O0FBRUE7QUFDQUMsU0FBT0MsUUFBUCxDQUFnQixDQUFoQixFQUFtQixDQUFuQjtBQUNBLEVBZkQ7O0FBaUJBOzs7OztBQUtBLEtBQU1DLHVCQUF1QixTQUF2QkEsb0JBQXVCO0FBQUEsU0FBTXhCLGNBQU47QUFBQSxFQUE3Qjs7QUFFQTtBQUNBWCxRQUFPb0MsSUFBUCxHQUFjLGdCQUFRO0FBQ3JCO0FBQ0FoQyxvQkFBa0JpQyxlQUFsQixDQUFrQ0MsaUJBQWxDLEdBQXNEWixrQkFBdEQ7QUFDQXRCLG9CQUFrQmlDLGVBQWxCLENBQWtDRSxtQkFBbEMsR0FBd0RKLG9CQUF4RDs7QUFFQTtBQUNBSztBQUNBLEVBUEQ7O0FBU0E7QUFDQSxRQUFPeEMsTUFBUDtBQUNBLENBekdGIiwiZmlsZSI6InByb2R1Y3QvaW1hZ2VzX3ZhbGlkYXRpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGltYWdlc192YWxpZGF0aW9uLmpzIDIwMTYtMDgtMjlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFByb2R1Y3QgSW1hZ2VzIFZhbGlkYXRpb24gQ29udHJvbGxlclxuICpcbiAqIFRoaXMgY29udHJvbGxlciBvdmVycmlkZXMgdGhlIGRlZmF1bHQgY2FsbGJhY2sgbWV0aG9kcyBvZiB0aGUgZm9ybSBpbWFnZSB2YWxpZGF0aW9uIGxpYnJhcnkuXG4gKiBCZWZvcmUgc3VibWl0dGluZyB0aGUgZm9ybSwgYWxsIGZpbGUgZXh0ZW5zaW9ucyBvZiB0aGUgc2VsZWN0ZWQgaW1hZ2VzIHdpbGwgYmUgdmFsaWRhdGVkLlxuICogT24gZmFpbGVkIHZhbGlkYXRpb24sIHRoZSByZXNwZWN0aXZlIGVsZW1lbnRzIHdpbGwgYmUgbWFya2VkLlxuICpcbiAqIEBtb2R1bGUgQ29udHJvbGxlcnMvaW1hZ2VzX3ZhbGlkYXRpb25cbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQnaW1hZ2VzX3ZhbGlkYXRpb24nLFxuXG5cdFtcblx0XHRgJHtneC5zb3VyY2V9L2xpYnMvZm9ybV9pbWFnZXNfdmFsaWRhdG9yYCxcblx0XHRgJHtneC5zb3VyY2V9L2xpYnMvaW5mb19tZXNzYWdlc2Bcblx0XSxcblxuXHRmdW5jdGlvbigpIHtcblx0XHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcdFxuXHRcdC8vIE1vZHVsZSBlbGVtZW50LCB3aGljaCByZXByZXNlbnRzIGEgZm9ybS5cblx0XHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XG5cblx0XHQvLyBTaG9ydGN1dCB0byBmb3JtIGltYWdlcyB2YWxpZGF0b3IgbGlicmFyeS5cblx0XHRjb25zdCBpbWFnZVZhbGlkYXRvckxpYiA9IGpzZS5saWJzLmZvcm1faW1hZ2VzX3ZhbGlkYXRvcjtcblxuXHRcdC8vIFNob3J0Y3V0IHRvIGluZm8gbWVzc2FnZXMgbGlicmFyeS5cblx0XHRjb25zdCBpbmZvTXNnTGliID0ganNlLmxpYnMuaW5mb19tZXNzYWdlcztcblxuXHRcdC8vIE1vZHVsZSBvYmplY3QuXG5cdFx0Y29uc3QgbW9kdWxlID0ge307XG5cblx0XHQvLyBDU1MgY2xhc3MgbmFtZSBvZiB0aGUgaW1hZ2UgY29udGFpbmVyIGVycm9yIGFsZXJ0LlxuXHRcdGNvbnN0IGludmFsaWRJbWFnZU1zZ0FsZXJ0Q2xhc3NOYW1lID0gJ2ludmFsaWQtaW1hZ2UtYWxlcnQnO1xuXG5cdFx0LyoqXG5cdFx0ICogQ2xlYXJzIGFueSBlcnJvcnMgbWVzc2FnZS5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0Y29uc3QgX2NsZWFyRXJyb3JzID0gKCkgPT4ge1xuXHRcdFx0Ly8gUmVtb3ZlIGFsbCBtZXNzYWdlcyBmcm9tIG1lc3NhZ2Ugc3RhY2suXG5cdFx0XHRpbmZvTXNnTGliLnRydW5jYXRlKCk7XG5cblx0XHRcdC8vIFJlbW92ZSBhbGwgaW1hZ2UgY29udGFpbmVyIGVycm9yIGFsZXJ0cy5cblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5maW5kKGludmFsaWRJbWFnZU1zZ0FsZXJ0Q2xhc3NOYW1lKVxuXHRcdFx0XHQucmVtb3ZlKCk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIE1hcmtzIGFuIGltYWdlIGNvbnRhaW5lciB3aXRoIGFuIGVycm9yLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudH0gZmlsZUlucHV0RWxlbWVudCBDdXJyZW50IGl0ZXJhdGlvbiBlbGVtZW50LlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0Y29uc3QgX21hcmtFcnJvciA9IGZpbGVJbnB1dEVsZW1lbnQgPT4ge1xuXHRcdFx0Ly8gSW1hZ2UgY29udGFpbmVyIGVsZW1lbnQuXG5cdFx0XHRjb25zdCAkd3JhcHBlciA9ICQoZmlsZUlucHV0RWxlbWVudCkucGFyZW50cygnLnByb2R1Y3QtaW1hZ2Utd3JhcHBlcicpO1xuXG5cdFx0XHQvLyBDcmVhdGUgaW1hZ2UgY29udGFpbmVyIGFsZXJ0IGVsZW1lbnQuXG5cdFx0XHRjb25zdCAkZXJyb3JNc2cgPSAkKCc8ZGl2Lz4nLCB7XG5cdFx0XHRcdGNsYXNzOiBgYWxlcnQgYWxlcnQtZGFuZ2VyICR7aW52YWxpZEltYWdlTXNnQWxlcnRDbGFzc05hbWV9YCxcblx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RYVF9JTUFHRV9JTlZBTElEX0VYVEVOU0lPTl9FUlJPUicsICdjYXRlZ29yaWVzJylcblx0XHRcdH0pO1xuXG5cdFx0XHQvLyBQdXQgYWxlcnQgaW50byBpbWFnZSBjb250YWluZXIuXG5cdFx0XHQkd3JhcHBlci5wcmVwZW5kKCRlcnJvck1zZyk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIE1hcmtzIHRoZSBmYWlsZWQgZWxlbWVudHMgYW5kIHByZXZlbnRzIHRoZSBmb3JtIHN1Ym1pdC5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudCBUcmlnZ2VyZWQgZm9ybSBzdWJtaXQgZXZlbnQuXG5cdFx0ICogQHBhcmFtIHtIVE1MRWxlbWVudFtdfSBmYWlsZWRFbGVtZW50cyBBcnJheSBjb250YWluaW5nIHRoZSBpbnB1dCBmaWVsZHMgdGhhdCBmYWlsZWQgb24gdGhlIHZhbGlkYXRpb24uXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHRjb25zdCBfb25WYWxpZGF0aW9uRXJyb3IgPSAoZXZlbnQsIGZhaWxlZEVsZW1lbnRzKSA9PiB7XG5cdFx0XHQvLyBQcmV2ZW50IGZvcm0gc3VibWl0LlxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuXHRcdFx0Ly8gQ2xlYXIgZXJyb3IgbWVzc2FnZXMuXG5cdFx0XHRfY2xlYXJFcnJvcnMoKTtcblxuXHRcdFx0Ly8gQWRkIGVycm9yIHRvIG1lc3NhZ2Ugc3RhY2suXG5cdFx0XHRpbmZvTXNnTGliLmFkZEVycm9yKGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdUWFRfRk9STV9TVUJNSVRfSU1BR0VfRVJST1InLCAnY2F0ZWdvcmllcycpKTtcblxuXHRcdFx0Ly8gTWFyayBlcnJvcnMuXG5cdFx0XHRmYWlsZWRFbGVtZW50cy5mb3JFYWNoKGVsZW1lbnQgPT4gX21hcmtFcnJvcihlbGVtZW50KSk7XG5cblx0XHRcdC8vIFNjcm9sbCB0byB0b3AuXG5cdFx0XHR3aW5kb3cuc2Nyb2xsVG8oMCwgMCk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIENsZWFycyBhbnkgZXJyb3IgbWVzc2FnZSB3aGlsZSBzdWJtaXR0aW5nIGZvcm0uXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdGNvbnN0IF9vblZhbGlkYXRpb25TdWNjZXNzID0gKCkgPT4gX2NsZWFyRXJyb3JzKCk7XG5cblx0XHQvLyBNb2R1bGUgaW5pdGlhbGl6ZSBmdW5jdGlvbi5cblx0XHRtb2R1bGUuaW5pdCA9IGRvbmUgPT4ge1xuXHRcdFx0Ly8gT3ZlcnJpZGUgZm9ybSBpbWFnZXMgdmFsaWRhdG9yIG1ldGhvZHMuXG5cdFx0XHRpbWFnZVZhbGlkYXRvckxpYi5jYWxsYmFja01ldGhvZHMub25WYWxpZGF0aW9uRXJyb3IgPSBfb25WYWxpZGF0aW9uRXJyb3I7XG5cdFx0XHRpbWFnZVZhbGlkYXRvckxpYi5jYWxsYmFja01ldGhvZHMub25WYWxpZGF0aW9uU3VjY2VzcyA9IF9vblZhbGlkYXRpb25TdWNjZXNzO1xuXG5cdFx0XHQvLyBGaW5pc2ggaW5pdGlhbGl6YXRpb24uXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
