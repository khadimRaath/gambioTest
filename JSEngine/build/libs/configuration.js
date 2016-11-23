'use strict';

/* --------------------------------------------------------------
 configuration.js 2016-08-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.configuration = jse.libs.configuration || {};

/**
 * ## Configurations Library
 *
 * This library makes it possible to receive and/or set shop configurations.
 */

(function (exports) {
	'use strict';

	var pageToken = jse.core.config.get('pageToken');
	var baseUrl = jse.core.config.get('appUrl') + '/shop.php?do=JsConfiguration';

	/**
  * Get the configuration value by the provided key.
  *
  * @param key Configuration key.
  *
  * @returns {Promise}
  */
	exports.get = function (key) {
		return new Promise(function (resolve, reject) {
			var url = baseUrl + '/Get';
			$.ajax({ url: url, data: { key: key, pageToken: pageToken } }).done(function (response) {
				return resolve(response);
			}).fail(function (error) {
				return reject(error);
			});
		});
	};

	/**
  * Set the provided value to the provided key.
  *
  * @param key Configuration key.
  * @param value Configuration value.
  *
  * @returns {Promise}
  */
	exports.set = function (key, value) {
		return new Promise(function (resolve, reject) {
			var url = baseUrl + '/Set';
			$.ajax({ url: url, method: 'POST', data: { key: key, value: value, pageToken: pageToken } }).done(function (response) {
				return resolve(response);
			}).fail(function (error) {
				return reject(error);
			});
		});
	};
})(jse.libs.configuration);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbmZpZ3VyYXRpb24uanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsImNvbmZpZ3VyYXRpb24iLCJleHBvcnRzIiwicGFnZVRva2VuIiwiY29yZSIsImNvbmZpZyIsImdldCIsImJhc2VVcmwiLCJQcm9taXNlIiwicmVzb2x2ZSIsInJlamVjdCIsInVybCIsIiQiLCJhamF4IiwiZGF0YSIsImtleSIsImRvbmUiLCJyZXNwb25zZSIsImZhaWwiLCJlcnJvciIsInNldCIsInZhbHVlIiwibWV0aG9kIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsYUFBVCxHQUF5QkYsSUFBSUMsSUFBSixDQUFTQyxhQUFULElBQTBCLEVBQW5EOztBQUVBOzs7Ozs7QUFNQSxDQUFDLFVBQVNDLE9BQVQsRUFBa0I7QUFDbEI7O0FBRUEsS0FBTUMsWUFBWUosSUFBSUssSUFBSixDQUFTQyxNQUFULENBQWdCQyxHQUFoQixDQUFvQixXQUFwQixDQUFsQjtBQUNBLEtBQU1DLFVBQWFSLElBQUlLLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsQ0FBYixpQ0FBTjs7QUFFQTs7Ozs7OztBQU9BSixTQUFRSSxHQUFSLEdBQWMsZUFBTztBQUNwQixTQUFPLElBQUlFLE9BQUosQ0FBWSxVQUFDQyxPQUFELEVBQVVDLE1BQVYsRUFBcUI7QUFDdkMsT0FBTUMsTUFBU0osT0FBVCxTQUFOO0FBQ0FLLEtBQUVDLElBQUYsQ0FBTyxFQUFFRixRQUFGLEVBQVFHLE1BQU0sRUFBRUMsUUFBRixFQUFPWixvQkFBUCxFQUFkLEVBQVAsRUFDRWEsSUFERixDQUNPO0FBQUEsV0FBWVAsUUFBUVEsUUFBUixDQUFaO0FBQUEsSUFEUCxFQUVFQyxJQUZGLENBRU87QUFBQSxXQUFTUixPQUFPUyxLQUFQLENBQVQ7QUFBQSxJQUZQO0FBR0EsR0FMTSxDQUFQO0FBTUEsRUFQRDs7QUFTQTs7Ozs7Ozs7QUFRQWpCLFNBQVFrQixHQUFSLEdBQWMsVUFBQ0wsR0FBRCxFQUFNTSxLQUFOLEVBQWdCO0FBQzdCLFNBQU8sSUFBSWIsT0FBSixDQUFZLFVBQUNDLE9BQUQsRUFBVUMsTUFBVixFQUFxQjtBQUN2QyxPQUFNQyxNQUFTSixPQUFULFNBQU47QUFDQUssS0FBRUMsSUFBRixDQUFPLEVBQUVGLFFBQUYsRUFBT1csUUFBUSxNQUFmLEVBQXVCUixNQUFNLEVBQUVDLFFBQUYsRUFBT00sWUFBUCxFQUFjbEIsb0JBQWQsRUFBN0IsRUFBUCxFQUNFYSxJQURGLENBQ087QUFBQSxXQUFZUCxRQUFRUSxRQUFSLENBQVo7QUFBQSxJQURQLEVBRUVDLElBRkYsQ0FFTztBQUFBLFdBQVNSLE9BQU9TLEtBQVAsQ0FBVDtBQUFBLElBRlA7QUFHQSxHQUxNLENBQVA7QUFNQSxFQVBEO0FBU0EsQ0F2Q0QsRUF1Q0dwQixJQUFJQyxJQUFKLENBQVNDLGFBdkNaIiwiZmlsZSI6ImNvbmZpZ3VyYXRpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gY29uZmlndXJhdGlvbi5qcyAyMDE2LTA4LTI0XHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuanNlLmxpYnMuY29uZmlndXJhdGlvbiA9IGpzZS5saWJzLmNvbmZpZ3VyYXRpb24gfHwge307XHJcblxyXG4vKipcclxuICogIyMgQ29uZmlndXJhdGlvbnMgTGlicmFyeVxyXG4gKlxyXG4gKiBUaGlzIGxpYnJhcnkgbWFrZXMgaXQgcG9zc2libGUgdG8gcmVjZWl2ZSBhbmQvb3Igc2V0IHNob3AgY29uZmlndXJhdGlvbnMuXHJcbiAqL1xyXG5cclxuKGZ1bmN0aW9uKGV4cG9ydHMpIHtcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Y29uc3QgcGFnZVRva2VuID0ganNlLmNvcmUuY29uZmlnLmdldCgncGFnZVRva2VuJyk7XHJcblx0Y29uc3QgYmFzZVVybCA9IGAke2pzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpfS9zaG9wLnBocD9kbz1Kc0NvbmZpZ3VyYXRpb25gO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEdldCB0aGUgY29uZmlndXJhdGlvbiB2YWx1ZSBieSB0aGUgcHJvdmlkZWQga2V5LlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIGtleSBDb25maWd1cmF0aW9uIGtleS5cclxuXHQgKlxyXG5cdCAqIEByZXR1cm5zIHtQcm9taXNlfVxyXG5cdCAqL1xyXG5cdGV4cG9ydHMuZ2V0ID0ga2V5ID0+IHtcclxuXHRcdHJldHVybiBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XHJcblx0XHRcdGNvbnN0IHVybCA9IGAke2Jhc2VVcmx9L0dldGA7XHJcblx0XHRcdCQuYWpheCh7IHVybCAsIGRhdGE6IHsga2V5LCBwYWdlVG9rZW4gfX0pXHJcblx0XHRcdFx0LmRvbmUocmVzcG9uc2UgPT4gcmVzb2x2ZShyZXNwb25zZSkpXHJcblx0XHRcdFx0LmZhaWwoZXJyb3IgPT4gcmVqZWN0KGVycm9yKSk7XHJcblx0XHR9KTtcclxuXHR9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFNldCB0aGUgcHJvdmlkZWQgdmFsdWUgdG8gdGhlIHByb3ZpZGVkIGtleS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSBrZXkgQ29uZmlndXJhdGlvbiBrZXkuXHJcblx0ICogQHBhcmFtIHZhbHVlIENvbmZpZ3VyYXRpb24gdmFsdWUuXHJcblx0ICpcclxuXHQgKiBAcmV0dXJucyB7UHJvbWlzZX1cclxuXHQgKi9cclxuXHRleHBvcnRzLnNldCA9IChrZXksIHZhbHVlKSA9PiB7XHJcblx0XHRyZXR1cm4gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT4ge1xyXG5cdFx0XHRjb25zdCB1cmwgPSBgJHtiYXNlVXJsfS9TZXRgO1xyXG5cdFx0XHQkLmFqYXgoeyB1cmwsIG1ldGhvZDogJ1BPU1QnLCBkYXRhOiB7IGtleSwgdmFsdWUsIHBhZ2VUb2tlbiB9fSlcclxuXHRcdFx0XHQuZG9uZShyZXNwb25zZSA9PiByZXNvbHZlKHJlc3BvbnNlKSlcclxuXHRcdFx0XHQuZmFpbChlcnJvciA9PiByZWplY3QoZXJyb3IpKTtcclxuXHRcdH0pO1xyXG5cdH07XHJcblx0XHJcbn0pKGpzZS5saWJzLmNvbmZpZ3VyYXRpb24pO1xyXG4iXX0=
