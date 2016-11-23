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
	var _handleSuccess = function (data, params) {
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
	var _request = function (params) {
		$.ajax({
			url: [
				defaults.baseUrl,
				(params.type === 'set' ? defaults.urlSet : defaults.urlGet)
			].join(''),
			dataType: 'json',
			data: params.data,
			method: (params.type === 'set' ? 'post' : 'get'),
			success: function (data) {
				if (params.type === 'get') { // GET
					_handleSuccess(data, params);
				} else { // POST
          if (data.success) {
					  _handleSuccess({}, params);
          } else if (typeof params.onError === 'function') {
            params.onError(data);
          }
				}
			},
			error: function (data) {
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
