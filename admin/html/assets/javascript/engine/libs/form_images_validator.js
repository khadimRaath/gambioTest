'use strict';

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
(function (exports) {

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
		onValidationError: function onValidationError(event, errors) {},


		/**
   * Invoked callback method on validation success.
   *
   * @param {jQuery.Event} event Triggered form submit event.
   * @abstract
   */
		onValidationSuccess: function onValidationSuccess(event) {}
	};
})(jse.libs.form_images_validator);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZvcm1faW1hZ2VzX3ZhbGlkYXRvci5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwiZm9ybV9pbWFnZXNfdmFsaWRhdG9yIiwiZXhwb3J0cyIsImNhbGxiYWNrTWV0aG9kcyIsIm9uVmFsaWRhdGlvbkVycm9yIiwiZXZlbnQiLCJlcnJvcnMiLCJvblZhbGlkYXRpb25TdWNjZXNzIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MscUJBQVQsR0FBaUNGLElBQUlDLElBQUosQ0FBU0MscUJBQVQsSUFBa0MsRUFBbkU7O0FBRUE7Ozs7Ozs7O0FBUUMsV0FBU0MsT0FBVCxFQUFrQjs7QUFFbEI7O0FBRUE7Ozs7OztBQUtBQSxTQUFRQyxlQUFSLEdBQTBCO0FBQ3pCOzs7Ozs7O0FBT0FDLG1CQVJ5Qiw2QkFRUEMsS0FSTyxFQVFBQyxNQVJBLEVBUVEsQ0FDaEMsQ0FUd0I7OztBQVd6Qjs7Ozs7O0FBTUFDLHFCQWpCeUIsK0JBaUJMRixLQWpCSyxFQWlCRSxDQUMxQjtBQWxCd0IsRUFBMUI7QUFvQkEsQ0E3QkEsRUE2QkNOLElBQUlDLElBQUosQ0FBU0MscUJBN0JWLENBQUQiLCJmaWxlIjoiZm9ybV9pbWFnZXNfdmFsaWRhdG9yLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBmb3JtX2ltYWdlc192YWxpZGF0b3IuanMgMjAxNi0wOC0yOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmZvcm1faW1hZ2VzX3ZhbGlkYXRvciA9IGpzZS5saWJzLmZvcm1faW1hZ2VzX3ZhbGlkYXRvciB8fCB7fTtcblxuLyoqXG4gKiBGb3JtIEltYWdlcyBWYWxpZGF0b3IgTGlicmFyeS5cbiAqXG4gKiBQcm92aWRlcyBjYWxsYmFjayBtZXRob2RzIHRoYXQgY2FuIGJlIG92ZXJyaWRkZW4gdG8gcHJvdmlkZSBjdXN0b20gZnVuY3Rpb25hbGl0eS5cbiAqXG4gKiBAbW9kdWxlIEFkbWluL0xpYnMvZm9ybV9pbWFnZXNfdmFsaWRhdG9yXG4gKiBAZXhwb3J0cyBqc2UubGlicy5mb3JtX2ltYWdlc192YWxpZGF0b3JcbiAqL1xuKGZ1bmN0aW9uKGV4cG9ydHMpIHtcblx0XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8qKlxuXHQgKiBQcm92aWRlcyBjYWxsYmFjayBtZXRob2RzLCB0aGF0IGNhbiBiZSBvdmVycmlkZGVuLlxuXHQgKlxuXHQgKiBAdHlwZSB7T2JqZWN0fVxuXHQgKi9cblx0ZXhwb3J0cy5jYWxsYmFja01ldGhvZHMgPSB7XG5cdFx0LyoqXG5cdFx0ICogSW52b2tlZCBjYWxsYmFjayBtZXRob2Qgb24gdmFsaWRhdGlvbiBlcnJvcnMuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnQgVHJpZ2dlcmVkIGZvcm0gc3VibWl0IGV2ZW50LlxuXHRcdCAqIEBwYXJhbSB7SFRNTEVsZW1lbnRbXX0gZXJyb3JzIEFycmF5IGNvbnRhaW5pbmcgdGhlIGlucHV0IGZpZWxkcyB0aGF0IGZhaWxlZCBvbiB0aGUgdmFsaWRhdGlvbi5cblx0XHQgKiBAYWJzdHJhY3Rcblx0XHQgKi9cblx0XHRvblZhbGlkYXRpb25FcnJvcihldmVudCwgZXJyb3JzKSB7XG5cdFx0fSxcblxuXHRcdC8qKlxuXHRcdCAqIEludm9rZWQgY2FsbGJhY2sgbWV0aG9kIG9uIHZhbGlkYXRpb24gc3VjY2Vzcy5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudCBUcmlnZ2VyZWQgZm9ybSBzdWJtaXQgZXZlbnQuXG5cdFx0ICogQGFic3RyYWN0XG5cdFx0ICovXG5cdFx0b25WYWxpZGF0aW9uU3VjY2VzcyhldmVudCkge1xuXHRcdH1cblx0fTtcbn0oanNlLmxpYnMuZm9ybV9pbWFnZXNfdmFsaWRhdG9yKSk7XG4iXX0=
