'use strict';

/* --------------------------------------------------------------
 form_changes_checker.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## From Changes Checker Extension
 * 
 * Stores all form data inside $(this) an waits for an trigger to compare the data with the 
 * original. A, with the trigger delivered deferred object gets resolved or rejected depending 
 * on the result.
 *
 * @todo Create some jQuery selector methods so that it is easier to check if something was changed.
 * @todo The extension must add a 'changed' or 'updated' class to the form so that other modules or code can determine
 * directly that something was changed.
 * @todo If a value is changed inside a input/select/textarea element this plugin must automatically perform the check.
 * Currently it just waits for the consumers to call the 'formchanges.check' event.
 * 
 * @module Admin/Extensions/form_changes_checker
 * @ignore
 */
gx.extensions.module('form_changes_checker', ['form'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Extension Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Options for Extension
  *
  * @type {object}
  */
	defaults = {
		'ignoreClass': '.ignore_changes'
	},


	/**
  * Final Extension Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Initial Form Data
  *
  * @type {array}
  * 
  * @todo Replace the initial value to an object.
  */
	formData = [],


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// EVENT HANDLER
	// ------------------------------------------------------------------------

	/**
  * Check Forms
  *
  * Function to compare the original data with the data that is currently in the
  * form. the given deferred object gets resolved or rejected.
  *
  * @param {object} event jQuery event object
  * @param {object} deferred JSON object containing the deferred object.
  */
	var _checkForms = function _checkForms(event, deferred) {
		event.stopPropagation();

		deferred = deferred.deferred;

		var newData = jse.libs.form.getData($this, options.ignoreClass),
		    cache = JSON.stringify(formData),
		    current = JSON.stringify(newData),
		    returnData = {
			'original': $.extend({}, formData),
			'current': $.extend({}, newData)
		};

		if (cache === current) {
			deferred.resolve(returnData);
		} else {
			deferred.reject(returnData);
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Init function of the extension, called by the engine.
  */
	module.init = function (done) {

		formData = jse.libs.form.getData($this, options.ignoreClass);
		$this.on('formchanges.check', _checkForms).on('formchanges.update', function () {
			// Updates the form data stored in cache
			formData = jse.libs.form.getData($this, options.ignoreClass);
		});

		$('body').on('formchanges.check', function (e, d) {
			// Event listener that performs on every formchanges.check trigger that isn't handled 
			// by the form_changes_checker
			if (d && d.deferred) {
				d.deferred.resolve();
			}
		});

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImZvcm1fY2hhbmdlc19jaGVja2VyLmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJmb3JtRGF0YSIsIl9jaGVja0Zvcm1zIiwiZXZlbnQiLCJkZWZlcnJlZCIsInN0b3BQcm9wYWdhdGlvbiIsIm5ld0RhdGEiLCJqc2UiLCJsaWJzIiwiZm9ybSIsImdldERhdGEiLCJpZ25vcmVDbGFzcyIsImNhY2hlIiwiSlNPTiIsInN0cmluZ2lmeSIsImN1cnJlbnQiLCJyZXR1cm5EYXRhIiwicmVzb2x2ZSIsInJlamVjdCIsImluaXQiLCJkb25lIiwib24iLCJlIiwiZCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7O0FBZ0JBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FDQyxzQkFERCxFQUdDLENBQUMsTUFBRCxDQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVc7QUFDVixpQkFBZTtBQURMLEVBYlo7OztBQWlCQzs7Ozs7QUFLQUMsV0FBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0F0Qlg7OztBQXdCQzs7Ozs7OztBQU9BTSxZQUFXLEVBL0JaOzs7QUFpQ0M7Ozs7O0FBS0FQLFVBQVMsRUF0Q1Y7O0FBd0NBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7Ozs7O0FBU0EsS0FBSVEsY0FBYyxTQUFkQSxXQUFjLENBQVNDLEtBQVQsRUFBZ0JDLFFBQWhCLEVBQTBCO0FBQzNDRCxRQUFNRSxlQUFOOztBQUVBRCxhQUFXQSxTQUFTQSxRQUFwQjs7QUFFQSxNQUFJRSxVQUFVQyxJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsT0FBZCxDQUFzQmQsS0FBdEIsRUFBNkJHLFFBQVFZLFdBQXJDLENBQWQ7QUFBQSxNQUNDQyxRQUFRQyxLQUFLQyxTQUFMLENBQWViLFFBQWYsQ0FEVDtBQUFBLE1BRUNjLFVBQVVGLEtBQUtDLFNBQUwsQ0FBZVIsT0FBZixDQUZYO0FBQUEsTUFHQ1UsYUFBYTtBQUNaLGVBQVluQixFQUFFRyxNQUFGLENBQVMsRUFBVCxFQUFhQyxRQUFiLENBREE7QUFFWixjQUFXSixFQUFFRyxNQUFGLENBQVMsRUFBVCxFQUFhTSxPQUFiO0FBRkMsR0FIZDs7QUFRQSxNQUFJTSxVQUFVRyxPQUFkLEVBQXVCO0FBQ3RCWCxZQUFTYSxPQUFULENBQWlCRCxVQUFqQjtBQUNBLEdBRkQsTUFFTztBQUNOWixZQUFTYyxNQUFULENBQWdCRixVQUFoQjtBQUNBO0FBQ0QsRUFsQkQ7O0FBb0JBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0F0QixRQUFPeUIsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTs7QUFFNUJuQixhQUFXTSxJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsT0FBZCxDQUFzQmQsS0FBdEIsRUFBNkJHLFFBQVFZLFdBQXJDLENBQVg7QUFDQWYsUUFDRXlCLEVBREYsQ0FDSyxtQkFETCxFQUMwQm5CLFdBRDFCLEVBRUVtQixFQUZGLENBRUssb0JBRkwsRUFFMkIsWUFBVztBQUNwQztBQUNBcEIsY0FBV00sSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLE9BQWQsQ0FBc0JkLEtBQXRCLEVBQTZCRyxRQUFRWSxXQUFyQyxDQUFYO0FBQ0EsR0FMRjs7QUFPQWQsSUFBRSxNQUFGLEVBQVV3QixFQUFWLENBQWEsbUJBQWIsRUFBa0MsVUFBU0MsQ0FBVCxFQUFZQyxDQUFaLEVBQWU7QUFDaEQ7QUFDQTtBQUNBLE9BQUlBLEtBQUtBLEVBQUVuQixRQUFYLEVBQXFCO0FBQ3BCbUIsTUFBRW5CLFFBQUYsQ0FBV2EsT0FBWDtBQUNBO0FBQ0QsR0FORDs7QUFRQUc7QUFDQSxFQW5CRDs7QUFxQkE7QUFDQSxRQUFPMUIsTUFBUDtBQUNBLENBcEhGIiwiZmlsZSI6ImZvcm1fY2hhbmdlc19jaGVja2VyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBmb3JtX2NoYW5nZXNfY2hlY2tlci5qcyAyMDE1LTEwLTE1IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBGcm9tIENoYW5nZXMgQ2hlY2tlciBFeHRlbnNpb25cbiAqIFxuICogU3RvcmVzIGFsbCBmb3JtIGRhdGEgaW5zaWRlICQodGhpcykgYW4gd2FpdHMgZm9yIGFuIHRyaWdnZXIgdG8gY29tcGFyZSB0aGUgZGF0YSB3aXRoIHRoZSBcbiAqIG9yaWdpbmFsLiBBLCB3aXRoIHRoZSB0cmlnZ2VyIGRlbGl2ZXJlZCBkZWZlcnJlZCBvYmplY3QgZ2V0cyByZXNvbHZlZCBvciByZWplY3RlZCBkZXBlbmRpbmcgXG4gKiBvbiB0aGUgcmVzdWx0LlxuICpcbiAqIEB0b2RvIENyZWF0ZSBzb21lIGpRdWVyeSBzZWxlY3RvciBtZXRob2RzIHNvIHRoYXQgaXQgaXMgZWFzaWVyIHRvIGNoZWNrIGlmIHNvbWV0aGluZyB3YXMgY2hhbmdlZC5cbiAqIEB0b2RvIFRoZSBleHRlbnNpb24gbXVzdCBhZGQgYSAnY2hhbmdlZCcgb3IgJ3VwZGF0ZWQnIGNsYXNzIHRvIHRoZSBmb3JtIHNvIHRoYXQgb3RoZXIgbW9kdWxlcyBvciBjb2RlIGNhbiBkZXRlcm1pbmVcbiAqIGRpcmVjdGx5IHRoYXQgc29tZXRoaW5nIHdhcyBjaGFuZ2VkLlxuICogQHRvZG8gSWYgYSB2YWx1ZSBpcyBjaGFuZ2VkIGluc2lkZSBhIGlucHV0L3NlbGVjdC90ZXh0YXJlYSBlbGVtZW50IHRoaXMgcGx1Z2luIG11c3QgYXV0b21hdGljYWxseSBwZXJmb3JtIHRoZSBjaGVjay5cbiAqIEN1cnJlbnRseSBpdCBqdXN0IHdhaXRzIGZvciB0aGUgY29uc3VtZXJzIHRvIGNhbGwgdGhlICdmb3JtY2hhbmdlcy5jaGVjaycgZXZlbnQuXG4gKiBcbiAqIEBtb2R1bGUgQWRtaW4vRXh0ZW5zaW9ucy9mb3JtX2NoYW5nZXNfY2hlY2tlclxuICogQGlnbm9yZVxuICovXG5neC5leHRlbnNpb25zLm1vZHVsZShcblx0J2Zvcm1fY2hhbmdlc19jaGVja2VyJyxcblx0XG5cdFsnZm9ybSddLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIEV4dGVuc2lvbiBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zIGZvciBFeHRlbnNpb25cblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0J2lnbm9yZUNsYXNzJzogJy5pZ25vcmVfY2hhbmdlcydcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgRXh0ZW5zaW9uIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBJbml0aWFsIEZvcm0gRGF0YVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHthcnJheX1cblx0XHRcdCAqIFxuXHRcdFx0ICogQHRvZG8gUmVwbGFjZSB0aGUgaW5pdGlhbCB2YWx1ZSB0byBhbiBvYmplY3QuXG5cdFx0XHQgKi9cblx0XHRcdGZvcm1EYXRhID0gW10sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBDaGVjayBGb3Jtc1xuXHRcdCAqXG5cdFx0ICogRnVuY3Rpb24gdG8gY29tcGFyZSB0aGUgb3JpZ2luYWwgZGF0YSB3aXRoIHRoZSBkYXRhIHRoYXQgaXMgY3VycmVudGx5IGluIHRoZVxuXHRcdCAqIGZvcm0uIHRoZSBnaXZlbiBkZWZlcnJlZCBvYmplY3QgZ2V0cyByZXNvbHZlZCBvciByZWplY3RlZC5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGRlZmVycmVkIEpTT04gb2JqZWN0IGNvbnRhaW5pbmcgdGhlIGRlZmVycmVkIG9iamVjdC5cblx0XHQgKi9cblx0XHR2YXIgX2NoZWNrRm9ybXMgPSBmdW5jdGlvbihldmVudCwgZGVmZXJyZWQpIHtcblx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XG5cdFx0XHRkZWZlcnJlZCA9IGRlZmVycmVkLmRlZmVycmVkO1xuXHRcdFx0XG5cdFx0XHR2YXIgbmV3RGF0YSA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdGhpcywgb3B0aW9ucy5pZ25vcmVDbGFzcyksXG5cdFx0XHRcdGNhY2hlID0gSlNPTi5zdHJpbmdpZnkoZm9ybURhdGEpLFxuXHRcdFx0XHRjdXJyZW50ID0gSlNPTi5zdHJpbmdpZnkobmV3RGF0YSksXG5cdFx0XHRcdHJldHVybkRhdGEgPSB7XG5cdFx0XHRcdFx0J29yaWdpbmFsJzogJC5leHRlbmQoe30sIGZvcm1EYXRhKSxcblx0XHRcdFx0XHQnY3VycmVudCc6ICQuZXh0ZW5kKHt9LCBuZXdEYXRhKVxuXHRcdFx0XHR9O1xuXHRcdFx0XG5cdFx0XHRpZiAoY2FjaGUgPT09IGN1cnJlbnQpIHtcblx0XHRcdFx0ZGVmZXJyZWQucmVzb2x2ZShyZXR1cm5EYXRhKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdGRlZmVycmVkLnJlamVjdChyZXR1cm5EYXRhKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgZXh0ZW5zaW9uLCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdFxuXHRcdFx0Zm9ybURhdGEgPSBqc2UubGlicy5mb3JtLmdldERhdGEoJHRoaXMsIG9wdGlvbnMuaWdub3JlQ2xhc3MpO1xuXHRcdFx0JHRoaXNcblx0XHRcdFx0Lm9uKCdmb3JtY2hhbmdlcy5jaGVjaycsIF9jaGVja0Zvcm1zKVxuXHRcdFx0XHQub24oJ2Zvcm1jaGFuZ2VzLnVwZGF0ZScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdC8vIFVwZGF0ZXMgdGhlIGZvcm0gZGF0YSBzdG9yZWQgaW4gY2FjaGVcblx0XHRcdFx0XHRmb3JtRGF0YSA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdGhpcywgb3B0aW9ucy5pZ25vcmVDbGFzcyk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkKCdib2R5Jykub24oJ2Zvcm1jaGFuZ2VzLmNoZWNrJywgZnVuY3Rpb24oZSwgZCkge1xuXHRcdFx0XHQvLyBFdmVudCBsaXN0ZW5lciB0aGF0IHBlcmZvcm1zIG9uIGV2ZXJ5IGZvcm1jaGFuZ2VzLmNoZWNrIHRyaWdnZXIgdGhhdCBpc24ndCBoYW5kbGVkIFxuXHRcdFx0XHQvLyBieSB0aGUgZm9ybV9jaGFuZ2VzX2NoZWNrZXJcblx0XHRcdFx0aWYgKGQgJiYgZC5kZWZlcnJlZCkge1xuXHRcdFx0XHRcdGQuZGVmZXJyZWQucmVzb2x2ZSgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
