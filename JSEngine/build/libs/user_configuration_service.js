'use strict';

/* --------------------------------------------------------------
 user_configuration_service.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.user_configuration_service = jse.libs.user_configuration_service || {};

/**
 * ## User Configuration Library
 *
 * This library is an adapter for the UserConfigurationService of the shop's backend codebase. It will 
 * perform AJAX requests for getting/setting user config data that is a robust way to store information 
 * about a specific user. 
 *
 *```js
 * var options= {
 *     data: {
 *         userId: 1,  // Current user ID
 *         configurationKey: 'recentSearchArea', // Configuration key
 *         configurationValue: '', // Configuration value (only for posting)
 *     },
 *
 *     onSuccess: function (data) {}, // Callback function, that will be executed on successful request,
 *                                    // contains the response as argument.
 *
 *     onError: function (data) {},   // Callback function, that will be executed on failed request.
 * }
 *
 * jse.libs.user_configuration_service.set(options); // Set values
 *
 * jse.libs.user_configuration_service.get(options); // Get values
 * ```
 * 
 * @todo Rename this library to "user_configuration". 
 *
 * @module JSE/Libs/user_configuration_service
 * @exports jse.libs.user_configuration_service
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// DEFAULTS
	// ------------------------------------------------------------------------

	/**
  * Default Library Settings
  *
  * @type {object}
  */

	var defaults = {
		// URL
		baseUrl: 'admin.php?do=UserConfiguration',
		urlSet: '/set',
		urlGet: '/get'
	};

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Handles success requests.
  *
  * @param {object} data - Data returned from server
  * @param {object} params - Parameters
  *
  * @private
  */
	var _handleSuccess = function _handleSuccess(data, params) {
		var response = {};
		if (data.success && data.configurationValue) {
			response = data;
		}
		if (typeof params.onSuccess === 'function') {
			params.onSuccess(response);
		}
	};

	/**
  * Performs AJAX request
  *
  * @param {object} params Contains the request parameters.
  * @param {string} params.type - type of request
  * @param {function} params.onSuccess - callback on success
  * @param {function} params.onError - callback on success
  * @param {object} params.data - request parameter
  *
  * @throws Error
  *
  * @private
  */
	var _request = function _request(params) {
		$.ajax({
			url: [defaults.baseUrl, params.type === 'set' ? defaults.urlSet : defaults.urlGet].join(''),
			dataType: 'json',
			data: params.data,
			method: params.type === 'set' ? 'post' : 'get',
			success: function success(data) {
				if (params.type === 'get') {
					// GET
					_handleSuccess(data, params);
				} else {
					// POST
					if (data.success) {
						_handleSuccess({}, params);
					} else if (typeof params.onError === 'function') {
						params.onError(data);
					}
				}
			},
			error: function error(data) {
				if (typeof params.onError === 'function') {
					params.onError(data);
				}
			}
		});
	};

	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------

	/**
  * Returns the user configuration value.
  *
  * @param {object} options
  * @param {function} options.onSuccess - callback on success
  * @param {function} options.onError - callback on success
  * @param {object} options.data - request parameter
  */
	exports.get = function (options) {
		options.type = 'get';
		_request(options);
	};

	/**
  * Sets the user configuration value.
  *
  * @param {object} options
  * @param {function} options.onSuccess - callback on success
  * @param {function} options.onError - callback on success
  * @param {object} options.data - request parameter
  */
	exports.set = function (options) {
		options.type = 'set';
		_request(options);
	};
})(jse.libs.user_configuration_service);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJ1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSIsImV4cG9ydHMiLCJkZWZhdWx0cyIsImJhc2VVcmwiLCJ1cmxTZXQiLCJ1cmxHZXQiLCJfaGFuZGxlU3VjY2VzcyIsImRhdGEiLCJwYXJhbXMiLCJyZXNwb25zZSIsInN1Y2Nlc3MiLCJjb25maWd1cmF0aW9uVmFsdWUiLCJvblN1Y2Nlc3MiLCJfcmVxdWVzdCIsIiQiLCJhamF4IiwidXJsIiwidHlwZSIsImpvaW4iLCJkYXRhVHlwZSIsIm1ldGhvZCIsIm9uRXJyb3IiLCJlcnJvciIsImdldCIsIm9wdGlvbnMiLCJzZXQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQywwQkFBVCxHQUFzQ0YsSUFBSUMsSUFBSixDQUFTQywwQkFBVCxJQUF1QyxFQUE3RTs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQStCQSxDQUFDLFVBQVVDLE9BQVYsRUFBbUI7O0FBRW5COztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7O0FBS0EsS0FBSUMsV0FBVztBQUNkO0FBQ0FDLFdBQVMsZ0NBRks7QUFHZEMsVUFBUSxNQUhNO0FBSWRDLFVBQVE7QUFKTSxFQUFmOztBQU9BO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7Ozs7QUFRQSxLQUFJQyxpQkFBaUIsU0FBakJBLGNBQWlCLENBQVVDLElBQVYsRUFBZ0JDLE1BQWhCLEVBQXdCO0FBQzVDLE1BQUlDLFdBQVcsRUFBZjtBQUNBLE1BQUlGLEtBQUtHLE9BQUwsSUFBZ0JILEtBQUtJLGtCQUF6QixFQUE2QztBQUM1Q0YsY0FBV0YsSUFBWDtBQUNBO0FBQ0QsTUFBSSxPQUFPQyxPQUFPSSxTQUFkLEtBQTRCLFVBQWhDLEVBQTRDO0FBQzNDSixVQUFPSSxTQUFQLENBQWlCSCxRQUFqQjtBQUNBO0FBQ0QsRUFSRDs7QUFVQTs7Ozs7Ozs7Ozs7OztBQWFBLEtBQUlJLFdBQVcsU0FBWEEsUUFBVyxDQUFVTCxNQUFWLEVBQWtCO0FBQ2hDTSxJQUFFQyxJQUFGLENBQU87QUFDTkMsUUFBSyxDQUNKZCxTQUFTQyxPQURMLEVBRUhLLE9BQU9TLElBQVAsS0FBZ0IsS0FBaEIsR0FBd0JmLFNBQVNFLE1BQWpDLEdBQTBDRixTQUFTRyxNQUZoRCxFQUdIYSxJQUhHLENBR0UsRUFIRixDQURDO0FBS05DLGFBQVUsTUFMSjtBQU1OWixTQUFNQyxPQUFPRCxJQU5QO0FBT05hLFdBQVNaLE9BQU9TLElBQVAsS0FBZ0IsS0FBaEIsR0FBd0IsTUFBeEIsR0FBaUMsS0FQcEM7QUFRTlAsWUFBUyxpQkFBVUgsSUFBVixFQUFnQjtBQUN4QixRQUFJQyxPQUFPUyxJQUFQLEtBQWdCLEtBQXBCLEVBQTJCO0FBQUU7QUFDNUJYLG9CQUFlQyxJQUFmLEVBQXFCQyxNQUFyQjtBQUNBLEtBRkQsTUFFTztBQUFFO0FBQ0gsU0FBSUQsS0FBS0csT0FBVCxFQUFrQjtBQUNyQkoscUJBQWUsRUFBZixFQUFtQkUsTUFBbkI7QUFDSSxNQUZELE1BRU8sSUFBSSxPQUFPQSxPQUFPYSxPQUFkLEtBQTBCLFVBQTlCLEVBQTBDO0FBQy9DYixhQUFPYSxPQUFQLENBQWVkLElBQWY7QUFDRDtBQUNOO0FBQ0QsSUFsQks7QUFtQk5lLFVBQU8sZUFBVWYsSUFBVixFQUFnQjtBQUN0QixRQUFJLE9BQU9DLE9BQU9hLE9BQWQsS0FBMEIsVUFBOUIsRUFBMEM7QUFDekNiLFlBQU9hLE9BQVAsQ0FBZWQsSUFBZjtBQUNBO0FBQ0Q7QUF2QkssR0FBUDtBQXlCQSxFQTFCRDs7QUE0QkE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7OztBQVFBTixTQUFRc0IsR0FBUixHQUFjLFVBQVVDLE9BQVYsRUFBbUI7QUFDaENBLFVBQVFQLElBQVIsR0FBZSxLQUFmO0FBQ0FKLFdBQVNXLE9BQVQ7QUFDQSxFQUhEOztBQUtBOzs7Ozs7OztBQVFBdkIsU0FBUXdCLEdBQVIsR0FBYyxVQUFVRCxPQUFWLEVBQW1CO0FBQ2hDQSxVQUFRUCxJQUFSLEdBQWUsS0FBZjtBQUNBSixXQUFTVyxPQUFUO0FBQ0EsRUFIRDtBQUtBLENBakhELEVBaUhHMUIsSUFBSUMsSUFBSixDQUFTQywwQkFqSFoiLCJmaWxlIjoidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UubGlicy51c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSA9IGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlIHx8IHt9O1xuXG4vKipcbiAqICMjIFVzZXIgQ29uZmlndXJhdGlvbiBMaWJyYXJ5XG4gKlxuICogVGhpcyBsaWJyYXJ5IGlzIGFuIGFkYXB0ZXIgZm9yIHRoZSBVc2VyQ29uZmlndXJhdGlvblNlcnZpY2Ugb2YgdGhlIHNob3AncyBiYWNrZW5kIGNvZGViYXNlLiBJdCB3aWxsIFxuICogcGVyZm9ybSBBSkFYIHJlcXVlc3RzIGZvciBnZXR0aW5nL3NldHRpbmcgdXNlciBjb25maWcgZGF0YSB0aGF0IGlzIGEgcm9idXN0IHdheSB0byBzdG9yZSBpbmZvcm1hdGlvbiBcbiAqIGFib3V0IGEgc3BlY2lmaWMgdXNlci4gXG4gKlxuICpgYGBqc1xuICogdmFyIG9wdGlvbnM9IHtcbiAqICAgICBkYXRhOiB7XG4gKiAgICAgICAgIHVzZXJJZDogMSwgIC8vIEN1cnJlbnQgdXNlciBJRFxuICogICAgICAgICBjb25maWd1cmF0aW9uS2V5OiAncmVjZW50U2VhcmNoQXJlYScsIC8vIENvbmZpZ3VyYXRpb24ga2V5XG4gKiAgICAgICAgIGNvbmZpZ3VyYXRpb25WYWx1ZTogJycsIC8vIENvbmZpZ3VyYXRpb24gdmFsdWUgKG9ubHkgZm9yIHBvc3RpbmcpXG4gKiAgICAgfSxcbiAqXG4gKiAgICAgb25TdWNjZXNzOiBmdW5jdGlvbiAoZGF0YSkge30sIC8vIENhbGxiYWNrIGZ1bmN0aW9uLCB0aGF0IHdpbGwgYmUgZXhlY3V0ZWQgb24gc3VjY2Vzc2Z1bCByZXF1ZXN0LFxuICogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAvLyBjb250YWlucyB0aGUgcmVzcG9uc2UgYXMgYXJndW1lbnQuXG4gKlxuICogICAgIG9uRXJyb3I6IGZ1bmN0aW9uIChkYXRhKSB7fSwgICAvLyBDYWxsYmFjayBmdW5jdGlvbiwgdGhhdCB3aWxsIGJlIGV4ZWN1dGVkIG9uIGZhaWxlZCByZXF1ZXN0LlxuICogfVxuICpcbiAqIGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLnNldChvcHRpb25zKTsgLy8gU2V0IHZhbHVlc1xuICpcbiAqIGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLmdldChvcHRpb25zKTsgLy8gR2V0IHZhbHVlc1xuICogYGBgXG4gKiBcbiAqIEB0b2RvIFJlbmFtZSB0aGlzIGxpYnJhcnkgdG8gXCJ1c2VyX2NvbmZpZ3VyYXRpb25cIi4gXG4gKlxuICogQG1vZHVsZSBKU0UvTGlicy91c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZVxuICogQGV4cG9ydHMganNlLmxpYnMudXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2VcbiAqL1xuKGZ1bmN0aW9uIChleHBvcnRzKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBERUZBVUxUU1xuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblxuXHQvKipcblx0ICogRGVmYXVsdCBMaWJyYXJ5IFNldHRpbmdzXG5cdCAqXG5cdCAqIEB0eXBlIHtvYmplY3R9XG5cdCAqL1xuXHR2YXIgZGVmYXVsdHMgPSB7XG5cdFx0Ly8gVVJMXG5cdFx0YmFzZVVybDogJ2FkbWluLnBocD9kbz1Vc2VyQ29uZmlndXJhdGlvbicsXG5cdFx0dXJsU2V0OiAnL3NldCcsXG5cdFx0dXJsR2V0OiAnL2dldCdcblx0fTtcblxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIEhhbmRsZXMgc3VjY2VzcyByZXF1ZXN0cy5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IGRhdGEgLSBEYXRhIHJldHVybmVkIGZyb20gc2VydmVyXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBwYXJhbXMgLSBQYXJhbWV0ZXJzXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX2hhbmRsZVN1Y2Nlc3MgPSBmdW5jdGlvbiAoZGF0YSwgcGFyYW1zKSB7XG5cdFx0dmFyIHJlc3BvbnNlID0ge307XG5cdFx0aWYgKGRhdGEuc3VjY2VzcyAmJiBkYXRhLmNvbmZpZ3VyYXRpb25WYWx1ZSkge1xuXHRcdFx0cmVzcG9uc2UgPSBkYXRhO1xuXHRcdH1cblx0XHRpZiAodHlwZW9mIHBhcmFtcy5vblN1Y2Nlc3MgPT09ICdmdW5jdGlvbicpIHtcblx0XHRcdHBhcmFtcy5vblN1Y2Nlc3MocmVzcG9uc2UpO1xuXHRcdH1cblx0fTtcblx0XG5cdC8qKlxuXHQgKiBQZXJmb3JtcyBBSkFYIHJlcXVlc3Rcblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IHBhcmFtcyBDb250YWlucyB0aGUgcmVxdWVzdCBwYXJhbWV0ZXJzLlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gcGFyYW1zLnR5cGUgLSB0eXBlIG9mIHJlcXVlc3Rcblx0ICogQHBhcmFtIHtmdW5jdGlvbn0gcGFyYW1zLm9uU3VjY2VzcyAtIGNhbGxiYWNrIG9uIHN1Y2Nlc3Ncblx0ICogQHBhcmFtIHtmdW5jdGlvbn0gcGFyYW1zLm9uRXJyb3IgLSBjYWxsYmFjayBvbiBzdWNjZXNzXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBwYXJhbXMuZGF0YSAtIHJlcXVlc3QgcGFyYW1ldGVyXG5cdCAqXG5cdCAqIEB0aHJvd3MgRXJyb3Jcblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfcmVxdWVzdCA9IGZ1bmN0aW9uIChwYXJhbXMpIHtcblx0XHQkLmFqYXgoe1xuXHRcdFx0dXJsOiBbXG5cdFx0XHRcdGRlZmF1bHRzLmJhc2VVcmwsXG5cdFx0XHRcdChwYXJhbXMudHlwZSA9PT0gJ3NldCcgPyBkZWZhdWx0cy51cmxTZXQgOiBkZWZhdWx0cy51cmxHZXQpXG5cdFx0XHRdLmpvaW4oJycpLFxuXHRcdFx0ZGF0YVR5cGU6ICdqc29uJyxcblx0XHRcdGRhdGE6IHBhcmFtcy5kYXRhLFxuXHRcdFx0bWV0aG9kOiAocGFyYW1zLnR5cGUgPT09ICdzZXQnID8gJ3Bvc3QnIDogJ2dldCcpLFxuXHRcdFx0c3VjY2VzczogZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdFx0aWYgKHBhcmFtcy50eXBlID09PSAnZ2V0JykgeyAvLyBHRVRcblx0XHRcdFx0XHRfaGFuZGxlU3VjY2VzcyhkYXRhLCBwYXJhbXMpO1xuXHRcdFx0XHR9IGVsc2UgeyAvLyBQT1NUXG4gICAgICAgICAgaWYgKGRhdGEuc3VjY2Vzcykge1xuXHRcdFx0XHRcdCAgX2hhbmRsZVN1Y2Nlc3Moe30sIHBhcmFtcyk7XG4gICAgICAgICAgfSBlbHNlIGlmICh0eXBlb2YgcGFyYW1zLm9uRXJyb3IgPT09ICdmdW5jdGlvbicpIHtcbiAgICAgICAgICAgIHBhcmFtcy5vbkVycm9yKGRhdGEpO1xuICAgICAgICAgIH1cblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdGVycm9yOiBmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHRpZiAodHlwZW9mIHBhcmFtcy5vbkVycm9yID09PSAnZnVuY3Rpb24nKSB7XG5cdFx0XHRcdFx0cGFyYW1zLm9uRXJyb3IoZGF0YSk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9KTtcblx0fTtcblxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gUFVCTElDIE1FVEhPRFNcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0LyoqXG5cdCAqIFJldHVybnMgdGhlIHVzZXIgY29uZmlndXJhdGlvbiB2YWx1ZS5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnNcblx0ICogQHBhcmFtIHtmdW5jdGlvbn0gb3B0aW9ucy5vblN1Y2Nlc3MgLSBjYWxsYmFjayBvbiBzdWNjZXNzXG5cdCAqIEBwYXJhbSB7ZnVuY3Rpb259IG9wdGlvbnMub25FcnJvciAtIGNhbGxiYWNrIG9uIHN1Y2Nlc3Ncblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMuZGF0YSAtIHJlcXVlc3QgcGFyYW1ldGVyXG5cdCAqL1xuXHRleHBvcnRzLmdldCA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG5cdFx0b3B0aW9ucy50eXBlID0gJ2dldCc7XG5cdFx0X3JlcXVlc3Qob3B0aW9ucyk7XG5cdH07XG5cblx0LyoqXG5cdCAqIFNldHMgdGhlIHVzZXIgY29uZmlndXJhdGlvbiB2YWx1ZS5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnNcblx0ICogQHBhcmFtIHtmdW5jdGlvbn0gb3B0aW9ucy5vblN1Y2Nlc3MgLSBjYWxsYmFjayBvbiBzdWNjZXNzXG5cdCAqIEBwYXJhbSB7ZnVuY3Rpb259IG9wdGlvbnMub25FcnJvciAtIGNhbGxiYWNrIG9uIHN1Y2Nlc3Ncblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMuZGF0YSAtIHJlcXVlc3QgcGFyYW1ldGVyXG5cdCAqL1xuXHRleHBvcnRzLnNldCA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XG5cdFx0b3B0aW9ucy50eXBlID0gJ3NldCc7XG5cdFx0X3JlcXVlc3Qob3B0aW9ucyk7XG5cdH07XG5cbn0pKGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlKTtcbiJdfQ==
