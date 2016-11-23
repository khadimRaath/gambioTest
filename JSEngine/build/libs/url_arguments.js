'use strict';

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
(function (exports) {

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

	exports.getUrlParameters = function (url) {
		var parameters = {},
		    search = url ? url.replace(/.*\?/, '') : location.search.substring(1),
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
	exports.getParameterArray = function () {
		jse.core.debug.warn('The "getParameterArray" function is deprecated as of v1.3 and will be removed in v1.5. ' + 'Use the "getUrlParameters" method instead.');
		return exports.getUrlParameters();
	};

	/**
  * Returns the current filename.
  *
  * @returns string Current filename.
  */
	exports.getCurrentFile = function () {
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
	exports.replaceParameterValue = function (url, parameter, value) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInVybF9hcmd1bWVudHMuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInVybF9hcmd1bWVudHMiLCJleHBvcnRzIiwiZ2V0VXJsUGFyYW1ldGVycyIsInVybCIsInBhcmFtZXRlcnMiLCJzZWFyY2giLCJyZXBsYWNlIiwibG9jYXRpb24iLCJzdWJzdHJpbmciLCJyZXN1bHQiLCJzcGxpdCIsImkiLCJsZW5ndGgiLCJ0bXAiLCJnZXRQYXJhbWV0ZXJBcnJheSIsImNvcmUiLCJkZWJ1ZyIsIndhcm4iLCJnZXRDdXJyZW50RmlsZSIsInVybEFycmF5Iiwid2luZG93IiwicGF0aG5hbWUiLCJyZXBsYWNlUGFyYW1ldGVyVmFsdWUiLCJwYXJhbWV0ZXIiLCJ2YWx1ZSIsInJlZ2V4IiwiUmVnRXhwIiwidW5kZWZpbmVkIiwiZW5jb2RlVVJJQ29tcG9uZW50Iiwic3Vic3RyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsYUFBVCxHQUF5QkYsSUFBSUMsSUFBSixDQUFTQyxhQUFULElBQTBCLEVBQW5EOztBQUVBOzs7Ozs7OztBQVFBLENBQUMsVUFBU0MsT0FBVCxFQUFrQjs7QUFFbEI7O0FBRUE7Ozs7Ozs7Ozs7QUFTQUEsU0FBUUMsZ0JBQVIsR0FBMkIsVUFBU0MsR0FBVCxFQUFjO0FBQ3hDLE1BQUlDLGFBQWEsRUFBakI7QUFBQSxNQUNDQyxTQUFVRixHQUFELEdBQVFBLElBQUlHLE9BQUosQ0FBWSxNQUFaLEVBQW9CLEVBQXBCLENBQVIsR0FBa0NDLFNBQVNGLE1BQVQsQ0FBZ0JHLFNBQWhCLENBQTBCLENBQTFCLENBRDVDO0FBQUEsTUFFQ0MsTUFGRDs7QUFJQSxNQUFJSixXQUFXLElBQVgsSUFBbUJBLFdBQVcsRUFBbEMsRUFBc0M7QUFDckMsVUFBT0QsVUFBUDtBQUNBOztBQUVESyxXQUFTSixPQUFPSyxLQUFQLENBQWEsR0FBYixDQUFUOztBQUVBLE9BQUssSUFBSUMsSUFBSSxDQUFiLEVBQWdCQSxJQUFJRixPQUFPRyxNQUEzQixFQUFtQ0QsR0FBbkMsRUFBd0M7QUFDdkMsT0FBSUUsTUFBTUosT0FBT0UsQ0FBUCxFQUFVRCxLQUFWLENBQWdCLEdBQWhCLENBQVY7QUFDQU4sY0FBV1MsSUFBSSxDQUFKLENBQVgsSUFBcUJBLElBQUksQ0FBSixDQUFyQjtBQUNBOztBQUVELFNBQU9ULFVBQVA7QUFDQSxFQWpCRDs7QUFvQkE7Ozs7Ozs7QUFPQUgsU0FBUWEsaUJBQVIsR0FBNEIsWUFBVztBQUN0Q2hCLE1BQUlpQixJQUFKLENBQVNDLEtBQVQsQ0FBZUMsSUFBZixDQUFvQiw0RkFDakIsNENBREg7QUFFQSxTQUFPaEIsUUFBUUMsZ0JBQVIsRUFBUDtBQUNBLEVBSkQ7O0FBTUE7Ozs7O0FBS0FELFNBQVFpQixjQUFSLEdBQXlCLFlBQVc7QUFDbkMsTUFBSUMsV0FBV0MsT0FBT2IsUUFBUCxDQUFnQmMsUUFBaEIsQ0FBeUJYLEtBQXpCLENBQStCLEdBQS9CLENBQWY7QUFDQSxTQUFPUyxTQUFTQSxTQUFTUCxNQUFULEdBQWtCLENBQTNCLENBQVA7QUFDQSxFQUhEOztBQUtBOzs7Ozs7Ozs7QUFTQVgsU0FBUXFCLHFCQUFSLEdBQWdDLFVBQVNuQixHQUFULEVBQWNvQixTQUFkLEVBQXlCQyxLQUF6QixFQUFnQztBQUMvRCxNQUFJQyxRQUFRLElBQUlDLE1BQUosQ0FBVyxNQUFNSCxTQUFOLEdBQWtCLFVBQTdCLENBQVo7O0FBRUFwQixRQUFNQSxJQUFJRyxPQUFKLENBQVltQixLQUFaLEVBQW1CLE9BQU9ELEtBQTFCLENBQU47O0FBRUEsTUFBSXJCLElBQUlFLE1BQUosQ0FBV2tCLFlBQVksR0FBdkIsTUFBZ0MsQ0FBQyxDQUFqQyxJQUFzQ0MsVUFBVUcsU0FBcEQsRUFBK0Q7QUFDOUQsT0FBSXhCLElBQUlFLE1BQUosQ0FBVyxJQUFYLE1BQXFCLENBQUMsQ0FBMUIsRUFBNkI7QUFDNUJGLFdBQU8sTUFBTXlCLG1CQUFtQkwsU0FBbkIsQ0FBTixHQUFzQyxHQUF0QyxHQUE0Q0ssbUJBQW1CSixLQUFuQixDQUFuRDtBQUNBLElBRkQsTUFFTyxJQUFJckIsSUFBSTBCLE1BQUosQ0FBVzFCLElBQUlTLE1BQUosR0FBYSxDQUF4QixFQUEyQixDQUEzQixNQUFrQyxHQUF0QyxFQUEyQztBQUNqRFQsV0FBT3lCLG1CQUFtQkwsU0FBbkIsSUFBZ0MsR0FBaEMsR0FBc0NLLG1CQUFtQkosS0FBbkIsQ0FBN0M7QUFDQSxJQUZNLE1BRUE7QUFDTnJCLFdBQU8sTUFBTXlCLG1CQUFtQkwsU0FBbkIsQ0FBTixHQUFzQyxHQUF0QyxHQUE0Q0ssbUJBQW1CSixLQUFuQixDQUFuRDtBQUNBO0FBQ0Q7O0FBRUQsU0FBT3JCLEdBQVA7QUFDQSxFQWhCRDtBQWtCQSxDQW5GRCxFQW1GR0wsSUFBSUMsSUFBSixDQUFTQyxhQW5GWiIsImZpbGUiOiJ1cmxfYXJndW1lbnRzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB1cmxfYXJndW1lbnRzLmpzIDIwMTYtMDUtMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UubGlicy51cmxfYXJndW1lbnRzID0ganNlLmxpYnMudXJsX2FyZ3VtZW50cyB8fCB7fTtcblxuLyoqXG4gKiAjIyBVUkwgQXJndW1lbnRzIExpYnJhcnlcbiAqXG4gKiBUaGlzIGxpYnJhcnkgaXMgY3JlYXRlZCB0byBoZWxwIGNvZGluZyB3aGVuIHZhbHVlcyBvZiBVUkwgYXJlIHJlcXVpcmVkLlxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvdXJsX2FyZ3VtZW50c1xuICogQGV4cG9ydHMganNlLmxpYnMudXJsX2FyZ3VtZW50c1xuICovXG4oZnVuY3Rpb24oZXhwb3J0cykge1xuXHRcblx0J3VzZSBzdHJpY3QnO1xuXHRcblx0LyoqXG5cdCAqIFJldHVybnMgYWxsIFVSTCBwYXJhbWV0ZXJzIGZyb20gdGhlIHByb3ZpZGVkIFVSTC5cblx0ICpcblx0ICogQHBhcmFtIHtzdHJpbmd9IHVybCAob3B0aW9uYWwpIFRoZSBVUkwgdG8gYmUgcGFyc2VkLiBJZiBub3QgcHJvdmlkZWQgdGhlIGN1cnJlbnQgbG9jYXRpb24gd2lsbCBiZSB1c2VkLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYW4gb2JqZWN0IHRoYXQgY29udGFpbnMgdGhlIHBhcmFtZXRlcnMgaW4ga2V5LXZhbHVlIHBhaXJzLlxuXHQgKiBcblx0ICogQGRlcHJlY2F0ZWQgVXNlIHRoZSAkLmRlcGFyYW0gbWV0aG9kIHdoaWNoIGNhbiBiZXR0ZXIgcGFyc2UgdGhlIEdFVCBwYXJhbWV0ZXJzLiBcblx0ICovXG5cdGV4cG9ydHMuZ2V0VXJsUGFyYW1ldGVycyA9IGZ1bmN0aW9uKHVybCkge1xuXHRcdHZhciBwYXJhbWV0ZXJzID0ge30sXG5cdFx0XHRzZWFyY2ggPSAodXJsKSA/IHVybC5yZXBsYWNlKC8uKlxcPy8sICcnKSA6IGxvY2F0aW9uLnNlYXJjaC5zdWJzdHJpbmcoMSksXG5cdFx0XHRyZXN1bHQ7XG5cdFx0XG5cdFx0aWYgKHNlYXJjaCA9PT0gbnVsbCB8fCBzZWFyY2ggPT09ICcnKSB7XG5cdFx0XHRyZXR1cm4gcGFyYW1ldGVycztcblx0XHR9XG5cdFx0XG5cdFx0cmVzdWx0ID0gc2VhcmNoLnNwbGl0KCcmJyk7XG5cdFx0XG5cdFx0Zm9yICh2YXIgaSA9IDA7IGkgPCByZXN1bHQubGVuZ3RoOyBpKyspIHtcblx0XHRcdHZhciB0bXAgPSByZXN1bHRbaV0uc3BsaXQoJz0nKTtcblx0XHRcdHBhcmFtZXRlcnNbdG1wWzBdXSA9IHRtcFsxXTtcblx0XHR9XG5cdFx0XG5cdFx0cmV0dXJuIHBhcmFtZXRlcnM7XG5cdH07XG5cdFxuXHRcblx0LyoqXG5cdCAqIFJldHVybnMgYW4gb2JqZWN0IHdoaWNoIGlzIGVxdWFsIHRvIHRoZSBQSFBzIG1hZ2ljICRfR0VUIGFycmF5LlxuXHQgKlxuXHQgKiBAcmV0dXJucyB7b2JqZWN0fSBDb250YWlucyB0aGUgY3VycmVudCBVUkwgcGFyYW1ldGVycy5cblx0ICpcblx0ICogQHRvZG8gUmVtb3ZlIHRoaXMgbWV0aG9kIGluIHYxLjUgb2YgSlMgRW5naW5lLlxuXHQgKi9cblx0ZXhwb3J0cy5nZXRQYXJhbWV0ZXJBcnJheSA9IGZ1bmN0aW9uKCkge1xuXHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSBcImdldFBhcmFtZXRlckFycmF5XCIgZnVuY3Rpb24gaXMgZGVwcmVjYXRlZCBhcyBvZiB2MS4zIGFuZCB3aWxsIGJlIHJlbW92ZWQgaW4gdjEuNS4gJ1xuXHRcdFx0KyAnVXNlIHRoZSBcImdldFVybFBhcmFtZXRlcnNcIiBtZXRob2QgaW5zdGVhZC4nKTtcblx0XHRyZXR1cm4gZXhwb3J0cy5nZXRVcmxQYXJhbWV0ZXJzKCk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogUmV0dXJucyB0aGUgY3VycmVudCBmaWxlbmFtZS5cblx0ICpcblx0ICogQHJldHVybnMgc3RyaW5nIEN1cnJlbnQgZmlsZW5hbWUuXG5cdCAqL1xuXHRleHBvcnRzLmdldEN1cnJlbnRGaWxlID0gZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHVybEFycmF5ID0gd2luZG93LmxvY2F0aW9uLnBhdGhuYW1lLnNwbGl0KCcvJyk7XG5cdFx0cmV0dXJuIHVybEFycmF5W3VybEFycmF5Lmxlbmd0aCAtIDFdO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFJlcGxhY2VzIGEgc3BlY2lmaWMgcGFyYW1ldGVyIHZhbHVlIGluc2lkZSBhbiBVUkwuXG5cdCAqXG5cdCAqIEBwYXJhbSB1cmwgVGhlIFVSTCBjb250YWluaW5nIHRoZSBwYXJhbWV0ZXIuXG5cdCAqIEBwYXJhbSBwYXJhbWV0ZXIgVGhlIHBhcmFtZXRlciBuYW1lIHRvIGJlIHJlcGxhY2VkLlxuXHQgKiBAcGFyYW0gdmFsdWUgVGhlIG5ldyB2YWx1ZSBvZiB0aGUgcGFyYW1ldGVyLlxuXHQgKlxuXHQgKiBAcmV0dXJucyB7c3RyaW5nfSBSZXR1cm5zIHRoZSB1cGRhdGVkIFVSTCBzdHJpbmcuXG5cdCAqL1xuXHRleHBvcnRzLnJlcGxhY2VQYXJhbWV0ZXJWYWx1ZSA9IGZ1bmN0aW9uKHVybCwgcGFyYW1ldGVyLCB2YWx1ZSkge1xuXHRcdHZhciByZWdleCA9IG5ldyBSZWdFeHAoJygnICsgcGFyYW1ldGVyICsgJz0pW15cXCZdKycpO1xuXHRcdFxuXHRcdHVybCA9IHVybC5yZXBsYWNlKHJlZ2V4LCAnJDEnICsgdmFsdWUpO1xuXHRcdFxuXHRcdGlmICh1cmwuc2VhcmNoKHBhcmFtZXRlciArICc9JykgPT09IC0xICYmIHZhbHVlICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdGlmICh1cmwuc2VhcmNoKC9cXD8vKSA9PT0gLTEpIHtcblx0XHRcdFx0dXJsICs9ICc/JyArIGVuY29kZVVSSUNvbXBvbmVudChwYXJhbWV0ZXIpICsgJz0nICsgZW5jb2RlVVJJQ29tcG9uZW50KHZhbHVlKTtcblx0XHRcdH0gZWxzZSBpZiAodXJsLnN1YnN0cih1cmwubGVuZ3RoIC0gMSwgMSkgPT09ICc/Jykge1xuXHRcdFx0XHR1cmwgKz0gZW5jb2RlVVJJQ29tcG9uZW50KHBhcmFtZXRlcikgKyAnPScgKyBlbmNvZGVVUklDb21wb25lbnQodmFsdWUpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0dXJsICs9ICcmJyArIGVuY29kZVVSSUNvbXBvbmVudChwYXJhbWV0ZXIpICsgJz0nICsgZW5jb2RlVVJJQ29tcG9uZW50KHZhbHVlKTtcblx0XHRcdH1cblx0XHR9XG5cdFx0XG5cdFx0cmV0dXJuIHVybDtcblx0fTtcblx0XG59KShqc2UubGlicy51cmxfYXJndW1lbnRzKTtcbiJdfQ==
