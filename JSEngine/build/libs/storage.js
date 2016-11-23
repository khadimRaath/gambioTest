'use strict';

/* --------------------------------------------------------------
 storage.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Storage */

jse.libs.storage = jse.libs.storage || {};

/**
 * ## Browser Storage API Library
 *
 * This library handles the HTML storage functionality. You can either store information in the session or the local 
 * storage of the browser.
 * 
 * @todo The concept of the library is good but the implementation and API need to be improved (simple getter and 
 * setter methods).  
 *
 * @module JSE/Libs/storage
 * @exports jse.libs.storage
 * @ignore
 */
(function (exports) {

	'use strict';

	/**
  * JavaScript Storage Object
  * 
  * @type {boolean}
  */

	var webStorage = Storage !== undefined ? true : false;

	/**
  * Stores a value in the browser storage.
  *
  * @param {object} store Storage handler object.
  * @param {boolean} overwrite Whether to overwrite an existing storage value.
  * @param {string} value String defining the value key name to be stored.
  * @param {object} dataset Contains the information to be stored.
  * @param {number} userId User id will be used to identify stored information of a specific user.
  *
  * @return {boolean} Returns the operation result.
  * 
  * @private
  */
	var _store = function _store(store, overwrite, value, dataset, userId) {

		var dataCache = null,
		    result = null;

		if (webStorage) {
			dataCache = store.getItem('user_' + userId);
			dataCache = dataCache || '{}';
			dataCache = $.parseJSON(dataCache);

			if (overwrite || dataCache[value] === undefined) {
				dataCache[value] = dataset;
			} else {
				dataCache[value] = $.extend({}, dataCache[value], dataset);
			}

			result = JSON.stringify(dataCache);
			store.setItem('user_' + userId, result);
			return true;
		}
		return false;
	};

	/**
  * Restores data from the browser storage.
  *
  * @param {object} store Storage handler object.
  * @param {string} value Value key name to be retrieved.
  * @param {number} userId User id that owns the value.
  *
  * @return {object} Returns the value if exists or an empty object if not.
  * 
  * @private
  */
	var _restore = function _restore(store, value, userId) {

		var dataCache = null;

		if (webStorage) {
			dataCache = store.getItem('user_' + userId);
			dataCache = dataCache || '{}';
			dataCache = $.parseJSON(dataCache);
			return dataCache[value] || {};
		}
		return {};
	};

	/**
  * Stores data in the browser storage.
  *
  * @param {array} destinations Array containing where to store the data (session, local).
  * @param {object} dataset Data to be stored.
  * @param {boolean} overwrite Whether to overwrite existing values.
  *
  * @return {object} Returns a promise object.
  */
	exports.store = function (destinations, dataset, overwrite) {

		var userID = $('body').data().userId,
		    resultObject = {},
		    promises = [];

		$.each(destinations, function (dest, value) {
			var localDeferred = $.Deferred();
			promises.push(localDeferred);

			switch (dest) {
				case 'session':
					resultObject.session = _store(sessionStorage, overwrite, value, dataset, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'local':
					resultObject.local = _store(localStorage, overwrite, value, dataset, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'server':
					// @todo Remove this case because it is not supported.
					localDeferred.resolve(resultObject);
					break;
				default:
					break;
			}
		});

		return $.when.apply(undefined, promises).promise();
	};

	/**
  * Restores data from the browser storage.
  *
  * @param {array} sources Defines the source of the data to be retrieved (session, local).
  *
  * @return {object} Returns a promise object.
  */
	exports.restore = function (sources) {
		var userID = $('body').data().userId,
		    resultObject = {},
		    promises = [];

		$.each(sources, function (src, value) {
			var localDeferred = $.Deferred();
			promises.push(localDeferred);

			switch (src) {
				case 'session':
					resultObject.session = _restore(sessionStorage, value, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'local':
					resultObject.local = _restore(localStorage, value, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'server':
					// @todo Remove this case because it is not supported.
					localDeferred.resolve(resultObject);
					break;
				default:
					break;
			}
		});

		return $.when.apply(undefined, promises).then(function (result) {
			return $.extend(true, {}, result.local || {}, result.session || {}, result.server || {});
		}).promise();
	};
})(jse.libs.storage);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInN0b3JhZ2UuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInN0b3JhZ2UiLCJleHBvcnRzIiwid2ViU3RvcmFnZSIsIlN0b3JhZ2UiLCJ1bmRlZmluZWQiLCJfc3RvcmUiLCJzdG9yZSIsIm92ZXJ3cml0ZSIsInZhbHVlIiwiZGF0YXNldCIsInVzZXJJZCIsImRhdGFDYWNoZSIsInJlc3VsdCIsImdldEl0ZW0iLCIkIiwicGFyc2VKU09OIiwiZXh0ZW5kIiwiSlNPTiIsInN0cmluZ2lmeSIsInNldEl0ZW0iLCJfcmVzdG9yZSIsImRlc3RpbmF0aW9ucyIsInVzZXJJRCIsImRhdGEiLCJyZXN1bHRPYmplY3QiLCJwcm9taXNlcyIsImVhY2giLCJkZXN0IiwibG9jYWxEZWZlcnJlZCIsIkRlZmVycmVkIiwicHVzaCIsInNlc3Npb24iLCJzZXNzaW9uU3RvcmFnZSIsInJlc29sdmUiLCJsb2NhbCIsImxvY2FsU3RvcmFnZSIsIndoZW4iLCJhcHBseSIsInByb21pc2UiLCJyZXN0b3JlIiwic291cmNlcyIsInNyYyIsInRoZW4iLCJzZXJ2ZXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7QUFFQUEsSUFBSUMsSUFBSixDQUFTQyxPQUFULEdBQW1CRixJQUFJQyxJQUFKLENBQVNDLE9BQVQsSUFBb0IsRUFBdkM7O0FBRUE7Ozs7Ozs7Ozs7Ozs7QUFhQyxXQUFVQyxPQUFWLEVBQW1COztBQUVuQjs7QUFFQTs7Ozs7O0FBS0EsS0FBSUMsYUFBY0MsWUFBWUMsU0FBYixHQUEwQixJQUExQixHQUFpQyxLQUFsRDs7QUFFQTs7Ozs7Ozs7Ozs7OztBQWFBLEtBQUlDLFNBQVMsU0FBVEEsTUFBUyxDQUFVQyxLQUFWLEVBQWlCQyxTQUFqQixFQUE0QkMsS0FBNUIsRUFBbUNDLE9BQW5DLEVBQTRDQyxNQUE1QyxFQUFvRDs7QUFFaEUsTUFBSUMsWUFBWSxJQUFoQjtBQUFBLE1BQ0NDLFNBQVMsSUFEVjs7QUFHQSxNQUFJVixVQUFKLEVBQWdCO0FBQ2ZT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O0FBRUEsV0FBUWEsR0FBUjtBQUNDLFNBQUssU0FBTDtBQUNDakIsa0JBQWFPLE9BQWIsR0FBdUJYLFNBQVNZLGNBQVQsRUFBeUJ4QixLQUF6QixFQUFnQ2MsTUFBaEMsQ0FBdkI7QUFDQU0sbUJBQWNLLE9BQWQsQ0FBc0JULFlBQXRCO0FBQ0E7QUFDRCxTQUFLLE9BQUw7QUFDQ0Esa0JBQWFVLEtBQWIsR0FBcUJkLFNBQVNlLFlBQVQsRUFBdUIzQixLQUF2QixFQUE4QmMsTUFBOUIsQ0FBckI7QUFDQU0sbUJBQWNLLE9BQWQsQ0FBc0JULFlBQXRCO0FBQ0E7QUFDRCxTQUFLLFFBQUw7QUFBZTtBQUNkSSxtQkFBY0ssT0FBZCxDQUFzQlQsWUFBdEI7QUFDQTtBQUNEO0FBQ0M7QUFiRjtBQWVBLEdBbkJEOztBQXFCQSxTQUFPVixFQUFFc0IsSUFBRixDQUNMQyxLQURLLENBQ0NqQyxTQURELEVBQ1lxQixRQURaLEVBRUxpQixJQUZLLENBRUEsVUFBVTlCLE1BQVYsRUFBa0I7QUFDakIsVUFBT0UsRUFBRUUsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixPQUFPc0IsS0FBUCxJQUFnQixFQUFuQyxFQUF1Q3RCLE9BQU9tQixPQUFQLElBQWtCLEVBQXpELEVBQTZEbkIsT0FBTytCLE1BQVAsSUFBaUIsRUFBOUUsQ0FBUDtBQUNBLEdBSkQsRUFLTEwsT0FMSyxFQUFQO0FBTUEsRUFoQ0Q7QUFrQ0EsQ0F4SkEsRUF3SkN4QyxJQUFJQyxJQUFKLENBQVNDLE9BeEpWLENBQUQiLCJmaWxlIjoic3RvcmFnZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc3RvcmFnZS5qcyAyMDE2LTAyLTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyogZ2xvYmFscyBTdG9yYWdlICovXG5cbmpzZS5saWJzLnN0b3JhZ2UgPSBqc2UubGlicy5zdG9yYWdlIHx8IHt9O1xuXG4vKipcbiAqICMjIEJyb3dzZXIgU3RvcmFnZSBBUEkgTGlicmFyeVxuICpcbiAqIFRoaXMgbGlicmFyeSBoYW5kbGVzIHRoZSBIVE1MIHN0b3JhZ2UgZnVuY3Rpb25hbGl0eS4gWW91IGNhbiBlaXRoZXIgc3RvcmUgaW5mb3JtYXRpb24gaW4gdGhlIHNlc3Npb24gb3IgdGhlIGxvY2FsIFxuICogc3RvcmFnZSBvZiB0aGUgYnJvd3Nlci5cbiAqIFxuICogQHRvZG8gVGhlIGNvbmNlcHQgb2YgdGhlIGxpYnJhcnkgaXMgZ29vZCBidXQgdGhlIGltcGxlbWVudGF0aW9uIGFuZCBBUEkgbmVlZCB0byBiZSBpbXByb3ZlZCAoc2ltcGxlIGdldHRlciBhbmQgXG4gKiBzZXR0ZXIgbWV0aG9kcykuICBcbiAqXG4gKiBAbW9kdWxlIEpTRS9MaWJzL3N0b3JhZ2VcbiAqIEBleHBvcnRzIGpzZS5saWJzLnN0b3JhZ2VcbiAqIEBpZ25vcmVcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG5cdC8qKlxuXHQgKiBKYXZhU2NyaXB0IFN0b3JhZ2UgT2JqZWN0XG5cdCAqIFxuXHQgKiBAdHlwZSB7Ym9vbGVhbn1cblx0ICovXG5cdHZhciB3ZWJTdG9yYWdlID0gKFN0b3JhZ2UgIT09IHVuZGVmaW5lZCkgPyB0cnVlIDogZmFsc2U7XG5cblx0LyoqXG5cdCAqIFN0b3JlcyBhIHZhbHVlIGluIHRoZSBicm93c2VyIHN0b3JhZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBzdG9yZSBTdG9yYWdlIGhhbmRsZXIgb2JqZWN0LlxuXHQgKiBAcGFyYW0ge2Jvb2xlYW59IG92ZXJ3cml0ZSBXaGV0aGVyIHRvIG92ZXJ3cml0ZSBhbiBleGlzdGluZyBzdG9yYWdlIHZhbHVlLlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gdmFsdWUgU3RyaW5nIGRlZmluaW5nIHRoZSB2YWx1ZSBrZXkgbmFtZSB0byBiZSBzdG9yZWQuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBkYXRhc2V0IENvbnRhaW5zIHRoZSBpbmZvcm1hdGlvbiB0byBiZSBzdG9yZWQuXG5cdCAqIEBwYXJhbSB7bnVtYmVyfSB1c2VySWQgVXNlciBpZCB3aWxsIGJlIHVzZWQgdG8gaWRlbnRpZnkgc3RvcmVkIGluZm9ybWF0aW9uIG9mIGEgc3BlY2lmaWMgdXNlci5cblx0ICpcblx0ICogQHJldHVybiB7Ym9vbGVhbn0gUmV0dXJucyB0aGUgb3BlcmF0aW9uIHJlc3VsdC5cblx0ICogXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX3N0b3JlID0gZnVuY3Rpb24gKHN0b3JlLCBvdmVyd3JpdGUsIHZhbHVlLCBkYXRhc2V0LCB1c2VySWQpIHtcblxuXHRcdHZhciBkYXRhQ2FjaGUgPSBudWxsLFxuXHRcdFx0cmVzdWx0ID0gbnVsbDtcblxuXHRcdGlmICh3ZWJTdG9yYWdlKSB7XG5cdFx0XHRkYXRhQ2FjaGUgPSBzdG9yZS5nZXRJdGVtKCd1c2VyXycgKyB1c2VySWQpO1xuXHRcdFx0ZGF0YUNhY2hlID0gZGF0YUNhY2hlIHx8ICd7fSc7XG5cdFx0XHRkYXRhQ2FjaGUgPSAkLnBhcnNlSlNPTihkYXRhQ2FjaGUpO1xuXG5cdFx0XHRpZiAob3ZlcndyaXRlIHx8IGRhdGFDYWNoZVt2YWx1ZV0gPT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRkYXRhQ2FjaGVbdmFsdWVdID0gZGF0YXNldDtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdGRhdGFDYWNoZVt2YWx1ZV0gPSAkLmV4dGVuZCh7fSwgZGF0YUNhY2hlW3ZhbHVlXSwgZGF0YXNldCk7XG5cdFx0XHR9XG5cblx0XHRcdHJlc3VsdCA9IEpTT04uc3RyaW5naWZ5KGRhdGFDYWNoZSk7XG5cdFx0XHRzdG9yZS5zZXRJdGVtKCd1c2VyXycgKyB1c2VySWQsIHJlc3VsdCk7XG5cdFx0XHRyZXR1cm4gdHJ1ZTtcblx0XHR9XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBSZXN0b3JlcyBkYXRhIGZyb20gdGhlIGJyb3dzZXIgc3RvcmFnZS5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IHN0b3JlIFN0b3JhZ2UgaGFuZGxlciBvYmplY3QuXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSB2YWx1ZSBWYWx1ZSBrZXkgbmFtZSB0byBiZSByZXRyaWV2ZWQuXG5cdCAqIEBwYXJhbSB7bnVtYmVyfSB1c2VySWQgVXNlciBpZCB0aGF0IG93bnMgdGhlIHZhbHVlLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgdGhlIHZhbHVlIGlmIGV4aXN0cyBvciBhbiBlbXB0eSBvYmplY3QgaWYgbm90LlxuXHQgKiBcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfcmVzdG9yZSA9IGZ1bmN0aW9uIChzdG9yZSwgdmFsdWUsIHVzZXJJZCkge1xuXG5cdFx0dmFyIGRhdGFDYWNoZSA9IG51bGw7XG5cblx0XHRpZiAod2ViU3RvcmFnZSkge1xuXHRcdFx0ZGF0YUNhY2hlID0gc3RvcmUuZ2V0SXRlbSgndXNlcl8nICsgdXNlcklkKTtcblx0XHRcdGRhdGFDYWNoZSA9IGRhdGFDYWNoZSB8fCAne30nO1xuXHRcdFx0ZGF0YUNhY2hlID0gJC5wYXJzZUpTT04oZGF0YUNhY2hlKTtcblx0XHRcdHJldHVybiBkYXRhQ2FjaGVbdmFsdWVdIHx8IHt9O1xuXHRcdH1cblx0XHRyZXR1cm4ge307XG5cdH07XG5cblx0LyoqXG5cdCAqIFN0b3JlcyBkYXRhIGluIHRoZSBicm93c2VyIHN0b3JhZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7YXJyYXl9IGRlc3RpbmF0aW9ucyBBcnJheSBjb250YWluaW5nIHdoZXJlIHRvIHN0b3JlIHRoZSBkYXRhIChzZXNzaW9uLCBsb2NhbCkuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBkYXRhc2V0IERhdGEgdG8gYmUgc3RvcmVkLlxuXHQgKiBAcGFyYW0ge2Jvb2xlYW59IG92ZXJ3cml0ZSBXaGV0aGVyIHRvIG92ZXJ3cml0ZSBleGlzdGluZyB2YWx1ZXMuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0LlxuXHQgKi9cblx0ZXhwb3J0cy5zdG9yZSA9IGZ1bmN0aW9uIChkZXN0aW5hdGlvbnMsIGRhdGFzZXQsIG92ZXJ3cml0ZSkge1xuXG5cdFx0dmFyIHVzZXJJRCA9ICQoJ2JvZHknKS5kYXRhKCkudXNlcklkLFxuXHRcdFx0cmVzdWx0T2JqZWN0ID0ge30sXG5cdFx0XHRwcm9taXNlcyA9IFtdO1xuXG5cdFx0JC5lYWNoKGRlc3RpbmF0aW9ucywgZnVuY3Rpb24gKGRlc3QsIHZhbHVlKSB7XG5cdFx0XHR2YXIgbG9jYWxEZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcdHByb21pc2VzLnB1c2gobG9jYWxEZWZlcnJlZCk7XG5cblx0XHRcdHN3aXRjaCAoZGVzdCkge1xuXHRcdFx0XHRjYXNlICdzZXNzaW9uJzpcblx0XHRcdFx0XHRyZXN1bHRPYmplY3Quc2Vzc2lvbiA9IF9zdG9yZShzZXNzaW9uU3RvcmFnZSwgb3ZlcndyaXRlLCB2YWx1ZSwgZGF0YXNldCwgdXNlcklEKTtcblx0XHRcdFx0XHRsb2NhbERlZmVycmVkLnJlc29sdmUocmVzdWx0T2JqZWN0KTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnbG9jYWwnOlxuXHRcdFx0XHRcdHJlc3VsdE9iamVjdC5sb2NhbCA9IF9zdG9yZShsb2NhbFN0b3JhZ2UsIG92ZXJ3cml0ZSwgdmFsdWUsIGRhdGFzZXQsIHVzZXJJRCk7XG5cdFx0XHRcdFx0bG9jYWxEZWZlcnJlZC5yZXNvbHZlKHJlc3VsdE9iamVjdCk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ3NlcnZlcic6IC8vIEB0b2RvIFJlbW92ZSB0aGlzIGNhc2UgYmVjYXVzZSBpdCBpcyBub3Qgc3VwcG9ydGVkLlxuXHRcdFx0XHRcdGxvY2FsRGVmZXJyZWQucmVzb2x2ZShyZXN1bHRPYmplY3QpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0fVxuXHRcdH0pO1xuXG5cdFx0cmV0dXJuICQud2hlbi5hcHBseSh1bmRlZmluZWQsIHByb21pc2VzKS5wcm9taXNlKCk7XG5cblx0fTtcblxuXHQvKipcblx0ICogUmVzdG9yZXMgZGF0YSBmcm9tIHRoZSBicm93c2VyIHN0b3JhZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7YXJyYXl9IHNvdXJjZXMgRGVmaW5lcyB0aGUgc291cmNlIG9mIHRoZSBkYXRhIHRvIGJlIHJldHJpZXZlZCAoc2Vzc2lvbiwgbG9jYWwpLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYSBwcm9taXNlIG9iamVjdC5cblx0ICovXG5cdGV4cG9ydHMucmVzdG9yZSA9IGZ1bmN0aW9uIChzb3VyY2VzKSB7XG5cdFx0dmFyIHVzZXJJRCA9ICQoJ2JvZHknKS5kYXRhKCkudXNlcklkLFxuXHRcdFx0cmVzdWx0T2JqZWN0ID0ge30sXG5cdFx0XHRwcm9taXNlcyA9IFtdO1xuXG5cdFx0JC5lYWNoKHNvdXJjZXMsIGZ1bmN0aW9uIChzcmMsIHZhbHVlKSB7XG5cdFx0XHR2YXIgbG9jYWxEZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcdHByb21pc2VzLnB1c2gobG9jYWxEZWZlcnJlZCk7XG5cblx0XHRcdHN3aXRjaCAoc3JjKSB7XG5cdFx0XHRcdGNhc2UgJ3Nlc3Npb24nOlxuXHRcdFx0XHRcdHJlc3VsdE9iamVjdC5zZXNzaW9uID0gX3Jlc3RvcmUoc2Vzc2lvblN0b3JhZ2UsIHZhbHVlLCB1c2VySUQpO1xuXHRcdFx0XHRcdGxvY2FsRGVmZXJyZWQucmVzb2x2ZShyZXN1bHRPYmplY3QpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdsb2NhbCc6XG5cdFx0XHRcdFx0cmVzdWx0T2JqZWN0LmxvY2FsID0gX3Jlc3RvcmUobG9jYWxTdG9yYWdlLCB2YWx1ZSwgdXNlcklEKTtcblx0XHRcdFx0XHRsb2NhbERlZmVycmVkLnJlc29sdmUocmVzdWx0T2JqZWN0KTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnc2VydmVyJzogLy8gQHRvZG8gUmVtb3ZlIHRoaXMgY2FzZSBiZWNhdXNlIGl0IGlzIG5vdCBzdXBwb3J0ZWQuXG5cdFx0XHRcdFx0bG9jYWxEZWZlcnJlZC5yZXNvbHZlKHJlc3VsdE9iamVjdCk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gJC53aGVuXG5cdFx0XHQuYXBwbHkodW5kZWZpbmVkLCBwcm9taXNlcylcblx0XHRcdC50aGVuKGZ1bmN0aW9uIChyZXN1bHQpIHtcblx0XHRcdFx0ICAgICAgcmV0dXJuICQuZXh0ZW5kKHRydWUsIHt9LCByZXN1bHQubG9jYWwgfHwge30sIHJlc3VsdC5zZXNzaW9uIHx8IHt9LCByZXN1bHQuc2VydmVyIHx8IHt9KTtcblx0XHRcdCAgICAgIH0pXG5cdFx0XHQucHJvbWlzZSgpO1xuXHR9O1xuXG59KGpzZS5saWJzLnN0b3JhZ2UpKTtcbiJdfQ==
