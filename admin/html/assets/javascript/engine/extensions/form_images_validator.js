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
gx.extensions.module('form_images_validator', [gx.source + '/libs/form_images_validator'], function (data) {

	'use strict';

	// Module element, which represents a form.

	var $this = $(this);

	// Module default parameters.
	var defaults = {
		// Valid image file extensions (pipe separated).
		validExtensions: 'gif|jpg|jpeg|tiff|png',

		// File input fields selector.
		selector: '[type="file"]'
	};

	// Shortcut to extension library.
	var library = jse.libs.form_images_validator;

	// Module options.
	var options = $.extend(true, {}, defaults, data);

	// Erroneous elements array.
	var failedElements = [];

	// Module object.
	var module = {};

	// File extension validator regex.
	var regex = new RegExp('.(' + options.validExtensions + ')', 'i');

	// Iterator function for the file input fields.
	var _validationIterator = function _validationIterator() {
		// Omit element if current iteration element does not have a `files` property
		// or if no file has been set.
		if (!this.files || !this.files[0]) {
			return;
		}

		// Get file name.this
		var fileName = this.files[0].name;

		// Validate file name.
		var validationPassed = regex.test(fileName);

		// Push this element to the map array if validation has failed. Otherwise the element is omitted.
		if (!validationPassed) {
			failedElements.push(this);
		}
	};

	// Handler for the form submit event.
	// Iterates over each file input field and validates its file name.
	var _onFormSubmit = function _onFormSubmit(event) {
		// Empty erroneous elements array.
		failedElements = [];

		// Validate.
		$this.find(options.selector).each(_validationIterator);

		// Invoke respective callback method.
		if (failedElements.length) {
			library.callbackMethods.onValidationError(event, failedElements);
		} else {
			library.callbackMethods.onValidationSuccess(event);
		}
	};

	// Module initialize function.
	module.init = function (done) {
		$this.on('submit', _onFormSubmit);
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZvcm1faW1hZ2VzX3ZhbGlkYXRvci5qcyJdLCJuYW1lcyI6WyJneCIsImV4dGVuc2lvbnMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJ2YWxpZEV4dGVuc2lvbnMiLCJzZWxlY3RvciIsImxpYnJhcnkiLCJqc2UiLCJsaWJzIiwiZm9ybV9pbWFnZXNfdmFsaWRhdG9yIiwib3B0aW9ucyIsImV4dGVuZCIsImZhaWxlZEVsZW1lbnRzIiwicmVnZXgiLCJSZWdFeHAiLCJfdmFsaWRhdGlvbkl0ZXJhdG9yIiwiZmlsZXMiLCJmaWxlTmFtZSIsIm5hbWUiLCJ2YWxpZGF0aW9uUGFzc2VkIiwidGVzdCIsInB1c2giLCJfb25Gb3JtU3VibWl0IiwiZmluZCIsImVhY2giLCJsZW5ndGgiLCJjYWxsYmFja01ldGhvZHMiLCJvblZhbGlkYXRpb25FcnJvciIsImV2ZW50Iiwib25WYWxpZGF0aW9uU3VjY2VzcyIsImluaXQiLCJvbiIsImRvbmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7OztBQWdCQUEsR0FBR0MsVUFBSCxDQUFjQyxNQUFkLENBQ0MsdUJBREQsRUFHQyxDQUNJRixHQUFHRyxNQURQLGlDQUhELEVBT0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBOztBQUNBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBO0FBQ0EsS0FBTUMsV0FBVztBQUNoQjtBQUNBQyxtQkFBaUIsdUJBRkQ7O0FBSWhCO0FBQ0FDLFlBQVU7QUFMTSxFQUFqQjs7QUFRQTtBQUNBLEtBQU1DLFVBQVVDLElBQUlDLElBQUosQ0FBU0MscUJBQXpCOztBQUVBO0FBQ0EsS0FBTUMsVUFBVVIsRUFBRVMsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CUixRQUFuQixFQUE2QkgsSUFBN0IsQ0FBaEI7O0FBRUE7QUFDQSxLQUFJWSxpQkFBaUIsRUFBckI7O0FBRUE7QUFDQSxLQUFNZCxTQUFTLEVBQWY7O0FBRUE7QUFDQSxLQUFNZSxRQUFRLElBQUlDLE1BQUosUUFBaUJKLFFBQVFOLGVBQXpCLFFBQTZDLEdBQTdDLENBQWQ7O0FBRUE7QUFDQSxLQUFNVyxzQkFBc0IsU0FBdEJBLG1CQUFzQixHQUFXO0FBQ3RDO0FBQ0E7QUFDQSxNQUFJLENBQUMsS0FBS0MsS0FBTixJQUFlLENBQUMsS0FBS0EsS0FBTCxDQUFXLENBQVgsQ0FBcEIsRUFBbUM7QUFDbEM7QUFDQTs7QUFFRDtBQUNBLE1BQU1DLFdBQVcsS0FBS0QsS0FBTCxDQUFXLENBQVgsRUFBY0UsSUFBL0I7O0FBRUE7QUFDQSxNQUFNQyxtQkFBbUJOLE1BQU1PLElBQU4sQ0FBV0gsUUFBWCxDQUF6Qjs7QUFFQTtBQUNBLE1BQUksQ0FBQ0UsZ0JBQUwsRUFBdUI7QUFDdEJQLGtCQUFlUyxJQUFmLENBQW9CLElBQXBCO0FBQ0E7QUFDRCxFQWpCRDs7QUFtQkE7QUFDQTtBQUNBLEtBQU1DLGdCQUFnQixTQUFoQkEsYUFBZ0IsUUFBUztBQUM5QjtBQUNBVixtQkFBaUIsRUFBakI7O0FBRUE7QUFDQVgsUUFDRXNCLElBREYsQ0FDT2IsUUFBUUwsUUFEZixFQUVFbUIsSUFGRixDQUVPVCxtQkFGUDs7QUFJQTtBQUNBLE1BQUlILGVBQWVhLE1BQW5CLEVBQTJCO0FBQzFCbkIsV0FBUW9CLGVBQVIsQ0FBd0JDLGlCQUF4QixDQUEwQ0MsS0FBMUMsRUFBaURoQixjQUFqRDtBQUNBLEdBRkQsTUFFTztBQUNOTixXQUFRb0IsZUFBUixDQUF3QkcsbUJBQXhCLENBQTRDRCxLQUE1QztBQUNBO0FBQ0QsRUFmRDs7QUFpQkE7QUFDQTlCLFFBQU9nQyxJQUFQLEdBQWMsZ0JBQVE7QUFDckI3QixRQUFNOEIsRUFBTixDQUFTLFFBQVQsRUFBbUJULGFBQW5CO0FBQ0FVO0FBQ0EsRUFIRDs7QUFLQTtBQUNBLFFBQU9sQyxNQUFQO0FBQ0EsQ0FyRkYiLCJmaWxlIjoiZm9ybV9pbWFnZXNfdmFsaWRhdG9yLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBmb3JtX2ltYWdlc192YWxpZGF0b3IuanMgMjAxNi0wOC0yOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogRm9ybSBpbWFnZXMgdmFsaWRhdG9yLlxuICpcbiAqIFNlYXJjaGVzIGZvciBmaWxlIGlucHV0cyBpbiBhIGZvcm0gYW5kIHZhbGlkYXRlcyB0aGVpciBmaWxlIGV4dGVuc2lvbnMuXG4gKiBGb3IgYSBwYXNzZWQvZmFpbGVkIHZhbGlkYXRpb24sIGEgcHJvdmlkZWQgY2FsbGJhY2sgd2lsbCBiZSBpbnZva2VkLlxuICpcbiAqIFRoZSB2YWxpZGF0aW9uIGlzIHRyaWdnZXJlZCBvbiBmb3JtIHN1Ym1pdC5cbiAqXG4gKiBDb25zaWRlciB0aGUgcmVzcGVjdGl2ZSBsaWJyYXJ5IGZpbGUgZm9yIGN1c3RvbSBjYWxsYmFjayBtZXRob2QgZGVmaW5pdGlvbnMuXG4gKlxuICogUG9zc2libGUgb3B0aW9uYWwgb3B0aW9ucyBhcmU6XG4gKiAgICAgIC0gdmFsaWRfZXh0ZW5zaW9ucyB7U3RyaW5nfSBWYWxpZCBmaWxlIGV4dGVuc2lvbnMgKHBpcGUtc2VwYXJhdGVkLCBlLmcuOiAnZ2lmfGpwZ3x0aWZmJylcbiAqICAgICAgLSBzZWxlY3RvciB7U3RyaW5nfSBGaWxlIGlucHV0IHNlbGVjdG9yLlxuICpcbiAqIEBtb2R1bGUgQWRtaW4vRXh0ZW5zaW9ucy9mb3JtX2ltYWdlc192YWxpZGF0b3JcbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCdmb3JtX2ltYWdlc192YWxpZGF0b3InLFxuXG5cdFtcblx0XHRgJHtneC5zb3VyY2V9L2xpYnMvZm9ybV9pbWFnZXNfdmFsaWRhdG9yYFxuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcdFxuXHRcdC8vIE1vZHVsZSBlbGVtZW50LCB3aGljaCByZXByZXNlbnRzIGEgZm9ybS5cblx0XHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XG5cblx0XHQvLyBNb2R1bGUgZGVmYXVsdCBwYXJhbWV0ZXJzLlxuXHRcdGNvbnN0IGRlZmF1bHRzID0ge1xuXHRcdFx0Ly8gVmFsaWQgaW1hZ2UgZmlsZSBleHRlbnNpb25zIChwaXBlIHNlcGFyYXRlZCkuXG5cdFx0XHR2YWxpZEV4dGVuc2lvbnM6ICdnaWZ8anBnfGpwZWd8dGlmZnxwbmcnLFxuXG5cdFx0XHQvLyBGaWxlIGlucHV0IGZpZWxkcyBzZWxlY3Rvci5cblx0XHRcdHNlbGVjdG9yOiAnW3R5cGU9XCJmaWxlXCJdJ1xuXHRcdH07XG5cblx0XHQvLyBTaG9ydGN1dCB0byBleHRlbnNpb24gbGlicmFyeS5cblx0XHRjb25zdCBsaWJyYXJ5ID0ganNlLmxpYnMuZm9ybV9pbWFnZXNfdmFsaWRhdG9yO1xuXG5cdFx0Ly8gTW9kdWxlIG9wdGlvbnMuXG5cdFx0Y29uc3Qgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSk7XG5cblx0XHQvLyBFcnJvbmVvdXMgZWxlbWVudHMgYXJyYXkuXG5cdFx0bGV0IGZhaWxlZEVsZW1lbnRzID0gW107XG5cblx0XHQvLyBNb2R1bGUgb2JqZWN0LlxuXHRcdGNvbnN0IG1vZHVsZSA9IHt9O1xuXG5cdFx0Ly8gRmlsZSBleHRlbnNpb24gdmFsaWRhdG9yIHJlZ2V4LlxuXHRcdGNvbnN0IHJlZ2V4ID0gbmV3IFJlZ0V4cChgXFwuKCR7b3B0aW9ucy52YWxpZEV4dGVuc2lvbnN9KWAsICdpJyk7XG5cblx0XHQvLyBJdGVyYXRvciBmdW5jdGlvbiBmb3IgdGhlIGZpbGUgaW5wdXQgZmllbGRzLlxuXHRcdGNvbnN0IF92YWxpZGF0aW9uSXRlcmF0b3IgPSBmdW5jdGlvbigpIHtcblx0XHRcdC8vIE9taXQgZWxlbWVudCBpZiBjdXJyZW50IGl0ZXJhdGlvbiBlbGVtZW50IGRvZXMgbm90IGhhdmUgYSBgZmlsZXNgIHByb3BlcnR5XG5cdFx0XHQvLyBvciBpZiBubyBmaWxlIGhhcyBiZWVuIHNldC5cblx0XHRcdGlmICghdGhpcy5maWxlcyB8fCAhdGhpcy5maWxlc1swXSkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cblx0XHRcdC8vIEdldCBmaWxlIG5hbWUudGhpc1xuXHRcdFx0Y29uc3QgZmlsZU5hbWUgPSB0aGlzLmZpbGVzWzBdLm5hbWU7XG5cblx0XHRcdC8vIFZhbGlkYXRlIGZpbGUgbmFtZS5cblx0XHRcdGNvbnN0IHZhbGlkYXRpb25QYXNzZWQgPSByZWdleC50ZXN0KGZpbGVOYW1lKTtcblxuXHRcdFx0Ly8gUHVzaCB0aGlzIGVsZW1lbnQgdG8gdGhlIG1hcCBhcnJheSBpZiB2YWxpZGF0aW9uIGhhcyBmYWlsZWQuIE90aGVyd2lzZSB0aGUgZWxlbWVudCBpcyBvbWl0dGVkLlxuXHRcdFx0aWYgKCF2YWxpZGF0aW9uUGFzc2VkKSB7XG5cdFx0XHRcdGZhaWxlZEVsZW1lbnRzLnB1c2godGhpcyk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8vIEhhbmRsZXIgZm9yIHRoZSBmb3JtIHN1Ym1pdCBldmVudC5cblx0XHQvLyBJdGVyYXRlcyBvdmVyIGVhY2ggZmlsZSBpbnB1dCBmaWVsZCBhbmQgdmFsaWRhdGVzIGl0cyBmaWxlIG5hbWUuXG5cdFx0Y29uc3QgX29uRm9ybVN1Ym1pdCA9IGV2ZW50ID0+IHtcblx0XHRcdC8vIEVtcHR5IGVycm9uZW91cyBlbGVtZW50cyBhcnJheS5cblx0XHRcdGZhaWxlZEVsZW1lbnRzID0gW107XG5cblx0XHRcdC8vIFZhbGlkYXRlLlxuXHRcdFx0JHRoaXNcblx0XHRcdFx0LmZpbmQob3B0aW9ucy5zZWxlY3Rvcilcblx0XHRcdFx0LmVhY2goX3ZhbGlkYXRpb25JdGVyYXRvcik7XG5cblx0XHRcdC8vIEludm9rZSByZXNwZWN0aXZlIGNhbGxiYWNrIG1ldGhvZC5cblx0XHRcdGlmIChmYWlsZWRFbGVtZW50cy5sZW5ndGgpIHtcblx0XHRcdFx0bGlicmFyeS5jYWxsYmFja01ldGhvZHMub25WYWxpZGF0aW9uRXJyb3IoZXZlbnQsIGZhaWxlZEVsZW1lbnRzKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdGxpYnJhcnkuY2FsbGJhY2tNZXRob2RzLm9uVmFsaWRhdGlvblN1Y2Nlc3MoZXZlbnQpO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvLyBNb2R1bGUgaW5pdGlhbGl6ZSBmdW5jdGlvbi5cblx0XHRtb2R1bGUuaW5pdCA9IGRvbmUgPT4ge1xuXHRcdFx0JHRoaXMub24oJ3N1Ym1pdCcsIF9vbkZvcm1TdWJtaXQpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH1cbik7Il19
