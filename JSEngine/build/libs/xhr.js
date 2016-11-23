'use strict';

/* --------------------------------------------------------------
 xhr.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.xhr = jse.libs.xhr || {};

/**
 * ## AJAX Library
 * 
 * This library contains wrapper-methods for the original jquery AJAX methods ('ajax', 'post', 'get'). 
 * 
 * @module JSE/Libs/xhr
 * @exports jse.libs.xhr
 */
(function (exports) {

	'use strict';

	/**
  * Default AJAX Options
  *
  * @type {object}
  */

	var defaultAjaxOptions = {
		type: 'post',
		dataType: 'json',
		cache: false,
		async: true
	};

	/**
  * Wrapper for the jquery "ajax" method. 
  *
  * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
  *
  * @return {object} Returns an ajax compatible promise object.
  */
	exports.ajax = function (parameters, ignoreFail) {

		var $pageToken = $('input[name="page_token"]');

		parameters = parameters || {};
		parameters.data = parameters.data || {};

		// If no page token was provided try to use the existing one.
		if (!parameters.data.page_token) {
			parameters.data.page_token = $pageToken.length ? $pageToken.val() : '';
		}

		var options = $.extend({}, defaultAjaxOptions, parameters),
		    deferred = $.Deferred(),
		    promise = deferred.promise();

		/**
   * Default fail handler
   * 
   * @param {string} message Message that will be shown
   */
		var _failHandler = function _failHandler(message) {
			message = message || 'JavaScript AJAX Error';
			//Modal.error({"content": message});
			deferred.reject();
		};

		// The ajax call
		var ajax = $.ajax(options).done(function (result) {
			// Check if it is an JSON-compatible result, if so, check the success message.
			if (result.success !== undefined && result.success === false && !ignoreFail) {
				_failHandler(result.message);
			} else {
				// set new page_token
				if (result.page_token !== undefined) {
					$pageToken.val(result.page_token);
				}
				deferred.resolve(result);
			}
		}).fail(function () {
			if (!ignoreFail) {
				_failHandler();
			} else {
				deferred.reject();
			}
		});

		// Add an ajax abort method to the promise, for cases where we need to abort the AJAX request.
		promise.abort = function () {
			ajax.abort();
		};

		return promise;
	};

	/**
  * Wrapper function for the jquery "get" method.
  *
  * @param {object} parameters AJAX-config object which will be merged with the default settings from config.
  *
  * @return {object} Returns an ajax compatible promise object.
  */
	exports.get = function (parameters, ignoreFail) {
		return exports.ajax($.extend({}, { type: 'get' }, parameters), ignoreFail);
	};

	/**
  * Wrapper function for the jquery "post" method.
  *
  * @param {object} parameters AJAX-config object which gets merged with the default settings from config.
  *
  * @return {object} Returns an ajax compatible promise object.
  */
	exports.post = function (parameters, ignoreFail) {
		return exports.ajax($.extend({}, { type: 'post' }, parameters), ignoreFail);
	};
})(jse.libs.xhr);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInhoci5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwieGhyIiwiZXhwb3J0cyIsImRlZmF1bHRBamF4T3B0aW9ucyIsInR5cGUiLCJkYXRhVHlwZSIsImNhY2hlIiwiYXN5bmMiLCJhamF4IiwicGFyYW1ldGVycyIsImlnbm9yZUZhaWwiLCIkcGFnZVRva2VuIiwiJCIsImRhdGEiLCJwYWdlX3Rva2VuIiwibGVuZ3RoIiwidmFsIiwib3B0aW9ucyIsImV4dGVuZCIsImRlZmVycmVkIiwiRGVmZXJyZWQiLCJwcm9taXNlIiwiX2ZhaWxIYW5kbGVyIiwibWVzc2FnZSIsInJlamVjdCIsImRvbmUiLCJyZXN1bHQiLCJzdWNjZXNzIiwidW5kZWZpbmVkIiwicmVzb2x2ZSIsImZhaWwiLCJhYm9ydCIsImdldCIsInBvc3QiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxHQUFULEdBQWVGLElBQUlDLElBQUosQ0FBU0MsR0FBVCxJQUFnQixFQUEvQjs7QUFFQTs7Ozs7Ozs7QUFRQSxDQUFDLFVBQVVDLE9BQVYsRUFBbUI7O0FBRW5COztBQUVBOzs7Ozs7QUFLQSxLQUFJQyxxQkFBcUI7QUFDeEJDLFFBQU0sTUFEa0I7QUFFeEJDLFlBQVUsTUFGYztBQUd4QkMsU0FBTyxLQUhpQjtBQUl4QkMsU0FBTztBQUppQixFQUF6Qjs7QUFPQTs7Ozs7OztBQU9BTCxTQUFRTSxJQUFSLEdBQWUsVUFBVUMsVUFBVixFQUFzQkMsVUFBdEIsRUFBa0M7O0FBRWhELE1BQUlDLGFBQWFDLEVBQUUsMEJBQUYsQ0FBakI7O0FBRUFILGVBQWFBLGNBQWMsRUFBM0I7QUFDQUEsYUFBV0ksSUFBWCxHQUFrQkosV0FBV0ksSUFBWCxJQUFtQixFQUFyQzs7QUFFQTtBQUNBLE1BQUksQ0FBQ0osV0FBV0ksSUFBWCxDQUFnQkMsVUFBckIsRUFBaUM7QUFDaENMLGNBQVdJLElBQVgsQ0FBZ0JDLFVBQWhCLEdBQThCSCxXQUFXSSxNQUFaLEdBQXNCSixXQUFXSyxHQUFYLEVBQXRCLEdBQXlDLEVBQXRFO0FBQ0E7O0FBRUQsTUFBSUMsVUFBVUwsRUFBRU0sTUFBRixDQUFTLEVBQVQsRUFBYWYsa0JBQWIsRUFBaUNNLFVBQWpDLENBQWQ7QUFBQSxNQUNDVSxXQUFXUCxFQUFFUSxRQUFGLEVBRFo7QUFBQSxNQUVDQyxVQUFVRixTQUFTRSxPQUFULEVBRlg7O0FBSUE7Ozs7O0FBS0EsTUFBSUMsZUFBZSxTQUFmQSxZQUFlLENBQVVDLE9BQVYsRUFBbUI7QUFDckNBLGFBQVVBLFdBQVcsdUJBQXJCO0FBQ0E7QUFDQUosWUFBU0ssTUFBVDtBQUNBLEdBSkQ7O0FBTUE7QUFDQSxNQUFJaEIsT0FBT0ksRUFBRUosSUFBRixDQUFPUyxPQUFQLEVBQWdCUSxJQUFoQixDQUFxQixVQUFVQyxNQUFWLEVBQWtCO0FBQ2pEO0FBQ0EsT0FBSUEsT0FBT0MsT0FBUCxLQUFtQkMsU0FBbkIsSUFBZ0NGLE9BQU9DLE9BQVAsS0FBbUIsS0FBbkQsSUFBNEQsQ0FBQ2pCLFVBQWpFLEVBQTZFO0FBQzVFWSxpQkFBYUksT0FBT0gsT0FBcEI7QUFDQSxJQUZELE1BRU87QUFDTjtBQUNBLFFBQUlHLE9BQU9aLFVBQVAsS0FBc0JjLFNBQTFCLEVBQXFDO0FBQ3BDakIsZ0JBQVdLLEdBQVgsQ0FBZVUsT0FBT1osVUFBdEI7QUFDQTtBQUNESyxhQUFTVSxPQUFULENBQWlCSCxNQUFqQjtBQUNBO0FBQ0QsR0FYVSxFQVdSSSxJQVhRLENBV0gsWUFBWTtBQUNuQixPQUFJLENBQUNwQixVQUFMLEVBQWlCO0FBQ2hCWTtBQUNBLElBRkQsTUFFTztBQUNOSCxhQUFTSyxNQUFUO0FBQ0E7QUFDRCxHQWpCVSxDQUFYOztBQW1CQTtBQUNBSCxVQUFRVSxLQUFSLEdBQWdCLFlBQVk7QUFDM0J2QixRQUFLdUIsS0FBTDtBQUNBLEdBRkQ7O0FBSUEsU0FBT1YsT0FBUDtBQUNBLEVBckREOztBQXdEQTs7Ozs7OztBQU9BbkIsU0FBUThCLEdBQVIsR0FBYyxVQUFVdkIsVUFBVixFQUFzQkMsVUFBdEIsRUFBa0M7QUFDL0MsU0FBT1IsUUFBUU0sSUFBUixDQUFhSSxFQUFFTSxNQUFGLENBQVMsRUFBVCxFQUFhLEVBQUNkLE1BQU0sS0FBUCxFQUFiLEVBQTRCSyxVQUE1QixDQUFiLEVBQXNEQyxVQUF0RCxDQUFQO0FBQ0EsRUFGRDs7QUFJQTs7Ozs7OztBQU9BUixTQUFRK0IsSUFBUixHQUFlLFVBQVV4QixVQUFWLEVBQXNCQyxVQUF0QixFQUFrQztBQUNoRCxTQUFPUixRQUFRTSxJQUFSLENBQWFJLEVBQUVNLE1BQUYsQ0FBUyxFQUFULEVBQWEsRUFBQ2QsTUFBTSxNQUFQLEVBQWIsRUFBNkJLLFVBQTdCLENBQWIsRUFBdURDLFVBQXZELENBQVA7QUFDQSxFQUZEO0FBSUEsQ0FyR0QsRUFxR0dYLElBQUlDLElBQUosQ0FBU0MsR0FyR1oiLCJmaWxlIjoieGhyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB4aHIuanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLnhociA9IGpzZS5saWJzLnhociB8fCB7fTtcblxuLyoqXG4gKiAjIyBBSkFYIExpYnJhcnlcbiAqIFxuICogVGhpcyBsaWJyYXJ5IGNvbnRhaW5zIHdyYXBwZXItbWV0aG9kcyBmb3IgdGhlIG9yaWdpbmFsIGpxdWVyeSBBSkFYIG1ldGhvZHMgKCdhamF4JywgJ3Bvc3QnLCAnZ2V0JykuIFxuICogXG4gKiBAbW9kdWxlIEpTRS9MaWJzL3hoclxuICogQGV4cG9ydHMganNlLmxpYnMueGhyXG4gKi9cbihmdW5jdGlvbiAoZXhwb3J0cykge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuXHQvKipcblx0ICogRGVmYXVsdCBBSkFYIE9wdGlvbnNcblx0ICpcblx0ICogQHR5cGUge29iamVjdH1cblx0ICovXG5cdHZhciBkZWZhdWx0QWpheE9wdGlvbnMgPSB7XG5cdFx0dHlwZTogJ3Bvc3QnLFxuXHRcdGRhdGFUeXBlOiAnanNvbicsXG5cdFx0Y2FjaGU6IGZhbHNlLFxuXHRcdGFzeW5jOiB0cnVlXG5cdH07XG5cblx0LyoqXG5cdCAqIFdyYXBwZXIgZm9yIHRoZSBqcXVlcnkgXCJhamF4XCIgbWV0aG9kLiBcblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IHBhcmFtZXRlcnMgQUpBWC1jb25maWcgb2JqZWN0IHdoaWNoIGdldHMgbWVyZ2VkIHdpdGggdGhlIGRlZmF1bHQgc2V0dGluZ3MgZnJvbSBjb25maWcuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhbiBhamF4IGNvbXBhdGlibGUgcHJvbWlzZSBvYmplY3QuXG5cdCAqL1xuXHRleHBvcnRzLmFqYXggPSBmdW5jdGlvbiAocGFyYW1ldGVycywgaWdub3JlRmFpbCkge1xuXG5cdFx0dmFyICRwYWdlVG9rZW4gPSAkKCdpbnB1dFtuYW1lPVwicGFnZV90b2tlblwiXScpO1xuXG5cdFx0cGFyYW1ldGVycyA9IHBhcmFtZXRlcnMgfHwge307XG5cdFx0cGFyYW1ldGVycy5kYXRhID0gcGFyYW1ldGVycy5kYXRhIHx8IHt9O1xuXG5cdFx0Ly8gSWYgbm8gcGFnZSB0b2tlbiB3YXMgcHJvdmlkZWQgdHJ5IHRvIHVzZSB0aGUgZXhpc3Rpbmcgb25lLlxuXHRcdGlmICghcGFyYW1ldGVycy5kYXRhLnBhZ2VfdG9rZW4pIHtcblx0XHRcdHBhcmFtZXRlcnMuZGF0YS5wYWdlX3Rva2VuID0gKCRwYWdlVG9rZW4ubGVuZ3RoKSA/ICRwYWdlVG9rZW4udmFsKCkgOiAnJztcblx0XHR9XG5cblx0XHR2YXIgb3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCBkZWZhdWx0QWpheE9wdGlvbnMsIHBhcmFtZXRlcnMpLFxuXHRcdFx0ZGVmZXJyZWQgPSAkLkRlZmVycmVkKCksXG5cdFx0XHRwcm9taXNlID0gZGVmZXJyZWQucHJvbWlzZSgpO1xuXG5cdFx0LyoqXG5cdFx0ICogRGVmYXVsdCBmYWlsIGhhbmRsZXJcblx0XHQgKiBcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gbWVzc2FnZSBNZXNzYWdlIHRoYXQgd2lsbCBiZSBzaG93blxuXHRcdCAqL1xuXHRcdHZhciBfZmFpbEhhbmRsZXIgPSBmdW5jdGlvbiAobWVzc2FnZSkge1xuXHRcdFx0bWVzc2FnZSA9IG1lc3NhZ2UgfHwgJ0phdmFTY3JpcHQgQUpBWCBFcnJvcic7XG5cdFx0XHQvL01vZGFsLmVycm9yKHtcImNvbnRlbnRcIjogbWVzc2FnZX0pO1xuXHRcdFx0ZGVmZXJyZWQucmVqZWN0KCk7XG5cdFx0fTtcblxuXHRcdC8vIFRoZSBhamF4IGNhbGxcblx0XHR2YXIgYWpheCA9ICQuYWpheChvcHRpb25zKS5kb25lKGZ1bmN0aW9uIChyZXN1bHQpIHtcblx0XHRcdC8vIENoZWNrIGlmIGl0IGlzIGFuIEpTT04tY29tcGF0aWJsZSByZXN1bHQsIGlmIHNvLCBjaGVjayB0aGUgc3VjY2VzcyBtZXNzYWdlLlxuXHRcdFx0aWYgKHJlc3VsdC5zdWNjZXNzICE9PSB1bmRlZmluZWQgJiYgcmVzdWx0LnN1Y2Nlc3MgPT09IGZhbHNlICYmICFpZ25vcmVGYWlsKSB7XG5cdFx0XHRcdF9mYWlsSGFuZGxlcihyZXN1bHQubWVzc2FnZSk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQvLyBzZXQgbmV3IHBhZ2VfdG9rZW5cblx0XHRcdFx0aWYgKHJlc3VsdC5wYWdlX3Rva2VuICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0XHQkcGFnZVRva2VuLnZhbChyZXN1bHQucGFnZV90b2tlbik7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZGVmZXJyZWQucmVzb2x2ZShyZXN1bHQpO1xuXHRcdFx0fVxuXHRcdH0pLmZhaWwoZnVuY3Rpb24gKCkge1xuXHRcdFx0aWYgKCFpZ25vcmVGYWlsKSB7XG5cdFx0XHRcdF9mYWlsSGFuZGxlcigpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0ZGVmZXJyZWQucmVqZWN0KCk7XG5cdFx0XHR9XG5cdFx0fSk7XG5cblx0XHQvLyBBZGQgYW4gYWpheCBhYm9ydCBtZXRob2QgdG8gdGhlIHByb21pc2UsIGZvciBjYXNlcyB3aGVyZSB3ZSBuZWVkIHRvIGFib3J0IHRoZSBBSkFYIHJlcXVlc3QuXG5cdFx0cHJvbWlzZS5hYm9ydCA9IGZ1bmN0aW9uICgpIHtcblx0XHRcdGFqYXguYWJvcnQoKTtcblx0XHR9O1xuXG5cdFx0cmV0dXJuIHByb21pc2U7XG5cdH07XG5cblxuXHQvKipcblx0ICogV3JhcHBlciBmdW5jdGlvbiBmb3IgdGhlIGpxdWVyeSBcImdldFwiIG1ldGhvZC5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IHBhcmFtZXRlcnMgQUpBWC1jb25maWcgb2JqZWN0IHdoaWNoIHdpbGwgYmUgbWVyZ2VkIHdpdGggdGhlIGRlZmF1bHQgc2V0dGluZ3MgZnJvbSBjb25maWcuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhbiBhamF4IGNvbXBhdGlibGUgcHJvbWlzZSBvYmplY3QuXG5cdCAqL1xuXHRleHBvcnRzLmdldCA9IGZ1bmN0aW9uIChwYXJhbWV0ZXJzLCBpZ25vcmVGYWlsKSB7XG5cdFx0cmV0dXJuIGV4cG9ydHMuYWpheCgkLmV4dGVuZCh7fSwge3R5cGU6ICdnZXQnfSwgcGFyYW1ldGVycyksIGlnbm9yZUZhaWwpO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBXcmFwcGVyIGZ1bmN0aW9uIGZvciB0aGUganF1ZXJ5IFwicG9zdFwiIG1ldGhvZC5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IHBhcmFtZXRlcnMgQUpBWC1jb25maWcgb2JqZWN0IHdoaWNoIGdldHMgbWVyZ2VkIHdpdGggdGhlIGRlZmF1bHQgc2V0dGluZ3MgZnJvbSBjb25maWcuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhbiBhamF4IGNvbXBhdGlibGUgcHJvbWlzZSBvYmplY3QuXG5cdCAqL1xuXHRleHBvcnRzLnBvc3QgPSBmdW5jdGlvbiAocGFyYW1ldGVycywgaWdub3JlRmFpbCkge1xuXHRcdHJldHVybiBleHBvcnRzLmFqYXgoJC5leHRlbmQoe30sIHt0eXBlOiAncG9zdCd9LCBwYXJhbWV0ZXJzKSwgaWdub3JlRmFpbCk7XG5cdH07XG5cbn0pKGpzZS5saWJzLnhocik7XG4iXX0=
