'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 dynamic_shop_messages.js 2016-05-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Dynamic Shop Messages
 *
 * This extension module is meant to be executed once in every page load. Its purpose is to display
 * custom notifications into various positions of the HTML. The notification source may vary in each
 * case but the original data should come from Gambio's Customer Portal.
 *
 * The module supports the use of a "url" option which will be used for loading the JSON data through an
 * AJAX call.
 * 
 * ### Options 
 * 
 * **Data Source URL | `data-dynamic_shop_messages-url` | String | Optional**
 * 
 * Provide the URL which will be used to fetch the dynamic shop messages. By default the DynamicShopMessages
 * controller will be used. 
 * 
 * **Response Envelope | `data-dynamic_shop_messages-response-envelope` | String | Optional**
 * 
 * Set a custom response envelop for the response object. By default "MESSAGES" will be used, because this is 
 * the envelope from the Gambio Portal response. 
 *
 * ### Example
 * 
 * ```html
 * <div data-gx-extension="dynamic_shop_messages"
 *     data-dynamic_shop_messages-url="http://custom-url.com/myscript.php"
 *     data-dynamic_shop_messages-response-envelope="MESSAGES">
 *   <-- HTML CONTENT -->
 * </div>
 * ```
 *
 * @module Admin/Extensions/dynamic_shop_messages
 * @ignore
 */
gx.extensions.module('dynamic_shop_messages', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {
		url: jse.core.config.get('appUrl') + '/admin/admin.php?do=DynamicShopMessages',
		lifetime: 30000, // maximum search lifetime (ms)
		responseEnvelope: 'MESSAGES'
	},


	/**
  * Final Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Checks if an HTML markup string is valid.
  *
  * {@link http://stackoverflow.com/a/14216406}
  *
  * @param {string} html The HTML markup to be validated.
  *
  * @returns {bool} Returns the validation result.
  */
	var _validateHtml = function _validateHtml(html) {
		var doc = document.createElement('div');
		doc.innerHTML = html;
		return doc.innerHTML === html;
	};

	/**
  * Check the current page matches the target_page value of the JSON data.
  *
  * @param {string|array} targetPageValue Contains a URL string or an array of URLs to be matched.
  *
  * @return {bool} Returns the validation check.
  */
	var _checkTargetPage = function _checkTargetPage(targetPageValue) {
		var result = false;

		if ((typeof targetPageValue === 'undefined' ? 'undefined' : _typeof(targetPageValue)) !== 'object') {
			targetPageValue = [targetPageValue];
		}

		$.each(targetPageValue, function () {
			var regex = new RegExp(this);

			if (window.location.href === jse.core.config.get('appUrl') + '/admin/' + this || regex.test(window.location.href)) {
				result = true;
				return false; // exit loop
			}
		});

		return result;
	};

	/**
  * Try to apply the dynamic message data into the page.
  *
  * @param {array} messages
  */
	var _apply = function _apply(messages) {
		$.each(messages, function (index, entry) {
			try {
				// Check if we have target information in the message entry.
				if (entry.target_page === undefined || entry.target_selector === undefined) {
					throw new TypeError('No target information provided. Skipping to the next entry...');
				}

				// Check if we are in the target page.
				if (!_checkTargetPage(entry.target_page)) {
					throw new TypeError('The entry is not targeted for the current page. Skipping to the next entry...');
				}

				// Find the target selector and append the HTML message. The module will keep on searching
				// for the target selector for as long as the "options.lifetime" value is.
				var currentTimestamp = Date.now;

				var intv = setInterval(function () {
					var $target = $this.find(entry.target_selector);

					if ($target.length > 0) {
						var htmlBackup = $target.html();
						$target.append(entry.message);

						// Check if the current HTML is valid and revert it otherwise.
						if (!_validateHtml($target.html())) {
							$target.html(htmlBackup);
							jse.core.debug.error('Dynamic message couldn\'t be applied.', entry);
						}

						clearInterval(intv); // stop searching
					}

					if (Date.now - currentTimestamp > options.lifetime) {
						clearInterval(intv);
						throw Error('Search lifetime limit exceeded, no element matched the provided selector.');
					}
				}, 300);
			} catch (e) {
				return true; // Continue loop with next message entry.
			}
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$.getJSON(options.url).done(function (response) {
			_apply(response[options.responseEnvelope]);
		}).fail(function (jqXHR, textStatus, errorThrown) {
			jse.core.debug.info('Could not load the dynamic shop messages.', jqXHR, textStatus, errorThrown);
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImR5bmFtaWNfc2hvcF9tZXNzYWdlcy5qcyJdLCJuYW1lcyI6WyJneCIsImV4dGVuc2lvbnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJ1cmwiLCJqc2UiLCJjb3JlIiwiY29uZmlnIiwiZ2V0IiwibGlmZXRpbWUiLCJyZXNwb25zZUVudmVsb3BlIiwib3B0aW9ucyIsImV4dGVuZCIsIl92YWxpZGF0ZUh0bWwiLCJodG1sIiwiZG9jIiwiZG9jdW1lbnQiLCJjcmVhdGVFbGVtZW50IiwiaW5uZXJIVE1MIiwiX2NoZWNrVGFyZ2V0UGFnZSIsInRhcmdldFBhZ2VWYWx1ZSIsInJlc3VsdCIsImVhY2giLCJyZWdleCIsIlJlZ0V4cCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsInRlc3QiLCJfYXBwbHkiLCJtZXNzYWdlcyIsImluZGV4IiwiZW50cnkiLCJ0YXJnZXRfcGFnZSIsInVuZGVmaW5lZCIsInRhcmdldF9zZWxlY3RvciIsIlR5cGVFcnJvciIsImN1cnJlbnRUaW1lc3RhbXAiLCJEYXRlIiwibm93IiwiaW50diIsInNldEludGVydmFsIiwiJHRhcmdldCIsImZpbmQiLCJsZW5ndGgiLCJodG1sQmFja3VwIiwiYXBwZW5kIiwibWVzc2FnZSIsImRlYnVnIiwiZXJyb3IiLCJjbGVhckludGVydmFsIiwiRXJyb3IiLCJlIiwiaW5pdCIsImRvbmUiLCJnZXRKU09OIiwicmVzcG9uc2UiLCJmYWlsIiwianFYSFIiLCJ0ZXh0U3RhdHVzIiwiZXJyb3JUaHJvd24iLCJpbmZvIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFtQ0FBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLHVCQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJ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LEdBQThCMUIsUUFBUUYsUUFBMUMsRUFBb0Q7QUFDbkR5QyxvQkFBY1YsSUFBZDtBQUNBLFlBQU1XLE1BQ0wsMkVBREssQ0FBTjtBQUVBO0FBQ0QsS0FyQlUsRUFxQlIsR0FyQlEsQ0FBWDtBQXVCQSxJQXZDRCxDQXVDRSxPQUFPQyxDQUFQLEVBQVU7QUFDWCxXQUFPLElBQVAsQ0FEVyxDQUNFO0FBQ2I7QUFDRCxHQTNDRDtBQTRDQSxFQTdDRDs7QUErQ0E7QUFDQTtBQUNBOztBQUVBckQsUUFBT3NELElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJwRCxJQUFFcUQsT0FBRixDQUFVNUMsUUFBUVAsR0FBbEIsRUFDRWtELElBREYsQ0FDTyxVQUFTRSxRQUFULEVBQW1CO0FBQ3hCM0IsVUFBTzJCLFNBQVM3QyxRQUFRRCxnQkFBakIsQ0FBUDtBQUNBLEdBSEYsRUFJRStDLElBSkYsQ0FJTyxVQUFTQyxLQUFULEVBQWdCQyxVQUFoQixFQUE0QkMsV0FBNUIsRUFBeUM7QUFDOUN2RCxPQUFJQyxJQUFKLENBQVMwQyxLQUFULENBQWVhLElBQWYsQ0FBb0IsMkNBQXBCLEVBQWlFSCxLQUFqRSxFQUF3RUMsVUFBeEUsRUFDQ0MsV0FERDtBQUVBLEdBUEY7O0FBU0FOO0FBQ0EsRUFYRDs7QUFhQSxRQUFPdkQsTUFBUDtBQUNBLENBbEtGIiwiZmlsZSI6ImR5bmFtaWNfc2hvcF9tZXNzYWdlcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZHluYW1pY19zaG9wX21lc3NhZ2VzLmpzIDIwMTYtMDUtMTFcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIER5bmFtaWMgU2hvcCBNZXNzYWdlc1xuICpcbiAqIFRoaXMgZXh0ZW5zaW9uIG1vZHVsZSBpcyBtZWFudCB0byBiZSBleGVjdXRlZCBvbmNlIGluIGV2ZXJ5IHBhZ2UgbG9hZC4gSXRzIHB1cnBvc2UgaXMgdG8gZGlzcGxheVxuICogY3VzdG9tIG5vdGlmaWNhdGlvbnMgaW50byB2YXJpb3VzIHBvc2l0aW9ucyBvZiB0aGUgSFRNTC4gVGhlIG5vdGlmaWNhdGlvbiBzb3VyY2UgbWF5IHZhcnkgaW4gZWFjaFxuICogY2FzZSBidXQgdGhlIG9yaWdpbmFsIGRhdGEgc2hvdWxkIGNvbWUgZnJvbSBHYW1iaW8ncyBDdXN0b21lciBQb3J0YWwuXG4gKlxuICogVGhlIG1vZHVsZSBzdXBwb3J0cyB0aGUgdXNlIG9mIGEgXCJ1cmxcIiBvcHRpb24gd2hpY2ggd2lsbCBiZSB1c2VkIGZvciBsb2FkaW5nIHRoZSBKU09OIGRhdGEgdGhyb3VnaCBhblxuICogQUpBWCBjYWxsLlxuICogXG4gKiAjIyMgT3B0aW9ucyBcbiAqIFxuICogKipEYXRhIFNvdXJjZSBVUkwgfCBgZGF0YS1keW5hbWljX3Nob3BfbWVzc2FnZXMtdXJsYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqIFxuICogUHJvdmlkZSB0aGUgVVJMIHdoaWNoIHdpbGwgYmUgdXNlZCB0byBmZXRjaCB0aGUgZHluYW1pYyBzaG9wIG1lc3NhZ2VzLiBCeSBkZWZhdWx0IHRoZSBEeW5hbWljU2hvcE1lc3NhZ2VzXG4gKiBjb250cm9sbGVyIHdpbGwgYmUgdXNlZC4gXG4gKiBcbiAqICoqUmVzcG9uc2UgRW52ZWxvcGUgfCBgZGF0YS1keW5hbWljX3Nob3BfbWVzc2FnZXMtcmVzcG9uc2UtZW52ZWxvcGVgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICogXG4gKiBTZXQgYSBjdXN0b20gcmVzcG9uc2UgZW52ZWxvcCBmb3IgdGhlIHJlc3BvbnNlIG9iamVjdC4gQnkgZGVmYXVsdCBcIk1FU1NBR0VTXCIgd2lsbCBiZSB1c2VkLCBiZWNhdXNlIHRoaXMgaXMgXG4gKiB0aGUgZW52ZWxvcGUgZnJvbSB0aGUgR2FtYmlvIFBvcnRhbCByZXNwb25zZS4gXG4gKlxuICogIyMjIEV4YW1wbGVcbiAqIFxuICogYGBgaHRtbFxuICogPGRpdiBkYXRhLWd4LWV4dGVuc2lvbj1cImR5bmFtaWNfc2hvcF9tZXNzYWdlc1wiXG4gKiAgICAgZGF0YS1keW5hbWljX3Nob3BfbWVzc2FnZXMtdXJsPVwiaHR0cDovL2N1c3RvbS11cmwuY29tL215c2NyaXB0LnBocFwiXG4gKiAgICAgZGF0YS1keW5hbWljX3Nob3BfbWVzc2FnZXMtcmVzcG9uc2UtZW52ZWxvcGU9XCJNRVNTQUdFU1wiPlxuICogICA8LS0gSFRNTCBDT05URU5UIC0tPlxuICogPC9kaXY+XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvZHluYW1pY19zaG9wX21lc3NhZ2VzXG4gKiBAaWdub3JlXG4gKi9cbmd4LmV4dGVuc2lvbnMubW9kdWxlKFxuXHQnZHluYW1pY19zaG9wX21lc3NhZ2VzJyxcblx0XG5cdFtdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRsZXRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHR1cmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89RHluYW1pY1Nob3BNZXNzYWdlcycsXG5cdFx0XHRcdGxpZmV0aW1lOiAzMDAwMCwgLy8gbWF4aW11bSBzZWFyY2ggbGlmZXRpbWUgKG1zKVxuXHRcdFx0XHRyZXNwb25zZUVudmVsb3BlOiAnTUVTU0FHRVMnXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRlVOQ1RJT05TXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ2hlY2tzIGlmIGFuIEhUTUwgbWFya3VwIHN0cmluZyBpcyB2YWxpZC5cblx0XHQgKlxuXHRcdCAqIHtAbGluayBodHRwOi8vc3RhY2tvdmVyZmxvdy5jb20vYS8xNDIxNjQwNn1cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBodG1sIFRoZSBIVE1MIG1hcmt1cCB0byBiZSB2YWxpZGF0ZWQuXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7Ym9vbH0gUmV0dXJucyB0aGUgdmFsaWRhdGlvbiByZXN1bHQuXG5cdFx0ICovXG5cdFx0dmFyIF92YWxpZGF0ZUh0bWwgPSBmdW5jdGlvbihodG1sKSB7XG5cdFx0XHR2YXIgZG9jID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XG5cdFx0XHRkb2MuaW5uZXJIVE1MID0gaHRtbDtcblx0XHRcdHJldHVybiAoZG9jLmlubmVySFRNTCA9PT0gaHRtbCk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDaGVjayB0aGUgY3VycmVudCBwYWdlIG1hdGNoZXMgdGhlIHRhcmdldF9wYWdlIHZhbHVlIG9mIHRoZSBKU09OIGRhdGEuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge3N0cmluZ3xhcnJheX0gdGFyZ2V0UGFnZVZhbHVlIENvbnRhaW5zIGEgVVJMIHN0cmluZyBvciBhbiBhcnJheSBvZiBVUkxzIHRvIGJlIG1hdGNoZWQuXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJuIHtib29sfSBSZXR1cm5zIHRoZSB2YWxpZGF0aW9uIGNoZWNrLlxuXHRcdCAqL1xuXHRcdHZhciBfY2hlY2tUYXJnZXRQYWdlID0gZnVuY3Rpb24odGFyZ2V0UGFnZVZhbHVlKSB7XG5cdFx0XHR2YXIgcmVzdWx0ID0gZmFsc2U7XG5cdFx0XHRcblx0XHRcdGlmICh0eXBlb2YgdGFyZ2V0UGFnZVZhbHVlICE9PSAnb2JqZWN0Jykge1xuXHRcdFx0XHR0YXJnZXRQYWdlVmFsdWUgPSBbdGFyZ2V0UGFnZVZhbHVlXTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JC5lYWNoKHRhcmdldFBhZ2VWYWx1ZSwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciByZWdleCA9IG5ldyBSZWdFeHAodGhpcyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAod2luZG93LmxvY2F0aW9uLmhyZWYgPT09IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi8nICsgdGhpc1xuXHRcdFx0XHRcdHx8IHJlZ2V4LnRlc3Qod2luZG93LmxvY2F0aW9uLmhyZWYpKSB7XG5cdFx0XHRcdFx0cmVzdWx0ID0gdHJ1ZTtcblx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7IC8vIGV4aXQgbG9vcFxuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0cmV0dXJuIHJlc3VsdDtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFRyeSB0byBhcHBseSB0aGUgZHluYW1pYyBtZXNzYWdlIGRhdGEgaW50byB0aGUgcGFnZS5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7YXJyYXl9IG1lc3NhZ2VzXG5cdFx0ICovXG5cdFx0dmFyIF9hcHBseSA9IGZ1bmN0aW9uKG1lc3NhZ2VzKSB7XG5cdFx0XHQkLmVhY2gobWVzc2FnZXMsIGZ1bmN0aW9uKGluZGV4LCBlbnRyeSkge1xuXHRcdFx0XHR0cnkge1xuXHRcdFx0XHRcdC8vIENoZWNrIGlmIHdlIGhhdmUgdGFyZ2V0IGluZm9ybWF0aW9uIGluIHRoZSBtZXNzYWdlIGVudHJ5LlxuXHRcdFx0XHRcdGlmIChlbnRyeS50YXJnZXRfcGFnZSA9PT0gdW5kZWZpbmVkIHx8IGVudHJ5LnRhcmdldF9zZWxlY3RvciA9PT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdFx0XHR0aHJvdyBuZXcgVHlwZUVycm9yKCdObyB0YXJnZXQgaW5mb3JtYXRpb24gcHJvdmlkZWQuIFNraXBwaW5nIHRvIHRoZSBuZXh0IGVudHJ5Li4uJyk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIENoZWNrIGlmIHdlIGFyZSBpbiB0aGUgdGFyZ2V0IHBhZ2UuXG5cdFx0XHRcdFx0aWYgKCFfY2hlY2tUYXJnZXRQYWdlKGVudHJ5LnRhcmdldF9wYWdlKSkge1xuXHRcdFx0XHRcdFx0dGhyb3cgbmV3IFR5cGVFcnJvcihcblx0XHRcdFx0XHRcdFx0J1RoZSBlbnRyeSBpcyBub3QgdGFyZ2V0ZWQgZm9yIHRoZSBjdXJyZW50IHBhZ2UuIFNraXBwaW5nIHRvIHRoZSBuZXh0IGVudHJ5Li4uJyk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEZpbmQgdGhlIHRhcmdldCBzZWxlY3RvciBhbmQgYXBwZW5kIHRoZSBIVE1MIG1lc3NhZ2UuIFRoZSBtb2R1bGUgd2lsbCBrZWVwIG9uIHNlYXJjaGluZ1xuXHRcdFx0XHRcdC8vIGZvciB0aGUgdGFyZ2V0IHNlbGVjdG9yIGZvciBhcyBsb25nIGFzIHRoZSBcIm9wdGlvbnMubGlmZXRpbWVcIiB2YWx1ZSBpcy5cblx0XHRcdFx0XHR2YXIgY3VycmVudFRpbWVzdGFtcCA9IERhdGUubm93O1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdHZhciBpbnR2ID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHR2YXIgJHRhcmdldCA9ICR0aGlzLmZpbmQoZW50cnkudGFyZ2V0X3NlbGVjdG9yKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0aWYgKCR0YXJnZXQubGVuZ3RoID4gMCkge1xuXHRcdFx0XHRcdFx0XHR2YXIgaHRtbEJhY2t1cCA9ICR0YXJnZXQuaHRtbCgpO1xuXHRcdFx0XHRcdFx0XHQkdGFyZ2V0LmFwcGVuZChlbnRyeS5tZXNzYWdlKTtcblx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdC8vIENoZWNrIGlmIHRoZSBjdXJyZW50IEhUTUwgaXMgdmFsaWQgYW5kIHJldmVydCBpdCBvdGhlcndpc2UuXG5cdFx0XHRcdFx0XHRcdGlmICghX3ZhbGlkYXRlSHRtbCgkdGFyZ2V0Lmh0bWwoKSkpIHtcblx0XHRcdFx0XHRcdFx0XHQkdGFyZ2V0Lmh0bWwoaHRtbEJhY2t1cCk7XG5cdFx0XHRcdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ0R5bmFtaWMgbWVzc2FnZSBjb3VsZG5cXCd0IGJlIGFwcGxpZWQuJywgZW50cnkpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRjbGVhckludGVydmFsKGludHYpOyAvLyBzdG9wIHNlYXJjaGluZ1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRpZiAoRGF0ZS5ub3cgLSBjdXJyZW50VGltZXN0YW1wID4gb3B0aW9ucy5saWZldGltZSkge1xuXHRcdFx0XHRcdFx0XHRjbGVhckludGVydmFsKGludHYpO1xuXHRcdFx0XHRcdFx0XHR0aHJvdyBFcnJvcihcblx0XHRcdFx0XHRcdFx0XHQnU2VhcmNoIGxpZmV0aW1lIGxpbWl0IGV4Y2VlZGVkLCBubyBlbGVtZW50IG1hdGNoZWQgdGhlIHByb3ZpZGVkIHNlbGVjdG9yLicpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0sIDMwMCk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdH0gY2F0Y2ggKGUpIHtcblx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTsgLy8gQ29udGludWUgbG9vcCB3aXRoIG5leHQgbWVzc2FnZSBlbnRyeS5cblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JC5nZXRKU09OKG9wdGlvbnMudXJsKVxuXHRcdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdF9hcHBseShyZXNwb25zZVtvcHRpb25zLnJlc3BvbnNlRW52ZWxvcGVdKTtcblx0XHRcdFx0fSlcblx0XHRcdFx0LmZhaWwoZnVuY3Rpb24oanFYSFIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duKSB7XG5cdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuaW5mbygnQ291bGQgbm90IGxvYWQgdGhlIGR5bmFtaWMgc2hvcCBtZXNzYWdlcy4nLCBqcVhIUiwgdGV4dFN0YXR1cyxcblx0XHRcdFx0XHRcdGVycm9yVGhyb3duKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
