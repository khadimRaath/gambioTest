/* --------------------------------------------------------------
 url_arguments.js 2016-05-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.url_arguments = jse.libs.url_arguments || {};

/**
 * ## URL Arguments Library
 *
 * This library is created to help coding when values of URL are required.
 *
 * @module JSE/Libs/url_arguments
 * @exports jse.libs.url_arguments
 */
(function(exports) {
	
	'use strict';
	
	/**
	 * Returns all URL parameters from the provided URL.
	 *
	 * @param {string} url (optional) The URL to be parsed. If not provided the current location will be used.
	 *
	 * @return {object} Returns an object that contains the parameters in key-value pairs.
	 * 
	 * @deprecated Use the $.deparam method which can better parse the GET parameters. 
	 */
	exports.getUrlParameters = function(url) {
		var parameters = {},
			search = (url) ? url.replace(/.*\?/, '') : location.search.substring(1),
			result;
		
		if (search === null || search === '') {
			return parameters;
		}
		
		result = search.split('&');
		
		for (var i = 0; i < result.length; i++) {
			var tmp = result[i].split('=');
			parameters[tmp[0]] = tmp[1];
		}
		
		return parameters;
	};
	
	
	/**
	 * Returns an object which is equal to the PHPs magic $_GET array.
	 *
	 * @returns {object} Contains the current URL parameters.
	 *
	 * @todo Remove this method in v1.5 of JS Engine.
	 */
	exports.getParameterArray = function() {
		jse.core.debug.warn('The "getParameterArray" function is deprecated as of v1.3 and will be removed in v1.5. '
			+ 'Use the "getUrlParameters" method instead.');
		return exports.getUrlParameters();
	};
	
	/**
	 * Returns the current filename.
	 *
	 * @returns string Current filename.
	 */
	exports.getCurrentFile = function() {
		var urlArray = window.location.pathname.split('/');
		return urlArray[urlArray.length - 1];
	};
	
	/**
	 * Replaces a specific parameter value inside an URL.
	 *
	 * @param url The URL containing the parameter.
	 * @param parameter The parameter name to be replaced.
	 * @param value The new value of the parameter.
	 *
	 * @returns {string} Returns the updated URL string.
	 */
	exports.replaceParameterValue = function(url, parameter, value) {
		var regex = new RegExp('(' + parameter + '=)[^\&]+');
		
		url = url.replace(regex, '$1' + value);
		
		if (url.search(parameter + '=') === -1 && value !== undefined) {
			if (url.search(/\?/) === -1) {
				url += '?' + encodeURIComponent(parameter) + '=' + encodeURIComponent(value);
			} else if (url.substr(url.length - 1, 1) === '?') {
				url += encodeURIComponent(parameter) + '=' + encodeURIComponent(value);
			} else {
				url += '&' + encodeURIComponent(parameter) + '=' + encodeURIComponent(value);
			}
		}
		
		return url;
	};
	
})(jse.libs.url_arguments);
