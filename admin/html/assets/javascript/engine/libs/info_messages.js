'use strict';

/* --------------------------------------------------------------
 info_messages.js 2016-07-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.info_messages = jse.libs.info_messages || {};

/**
 * ## Info Messages library
 *
 * Use this library to add messages into admin's notification system (top right corner). There are multiple 
 * types of notification entries 'error', 'info', 'warning' and 'success'. Use the respective method for 
 * each one of them. 
 * 
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 * 
 * ```javascript
 * gx.controller.module(
 *   'my_custom_page',
 *
 *   [
 *      gx.source + '/libs/info_messages'
 *   ],
 *
 *   function(data) {
 *      // Module code ... 
 *   });
 *```
 *
 * @module Admin/Libs/info_messages
 * @exports jse.libs.info_messages
 */
(function (exports) {

	'use strict';

	/**
  * Container element for info messages
  *
  * @type {object}
  */

	var $messagesContainer = $('.message_stack_container, .message-stack');

	/**
  * Appends a message box to the info messages container and displays it
  *
  * @param {string} message Message to be displayed.
  * @param {string} type Message type can be one of the "info", "warning", "error" & "success".
  *
  * @private
  */
	var _add = function _add(message, type) {
		var $alert = $('<div class="alert alert-' + type + '" data-gx-compatibility="close_alert_box">' + '<button type="button" class="close" data-dismuss="alert">×</button>' + message + '</div>');

		$alert.find('.close').on('click', function () {
			$(this).parent('.alert').hide();
		});

		$messagesContainer.append($alert);
		$messagesContainer.show();
	};

	/**
  * Removes all messages inside the message container.
  */
	exports.truncate = function () {
		$messagesContainer.empty();
	};

	/**
  * Adds a red error message.
  *
  * @param {string} message Message to be displayed.
  */
	exports.addError = function (message) {
		_add(message, 'danger');
	};

	/**
  * Adds a blue info message.
  *
  * @param {string} message Message to be displayed.
  */
	exports.addInfo = function (message) {
		_add(message, 'info');
	};

	/**
  * Adds a green success message.
  *
  * @param {string} message Message to be displayed.
  */
	exports.addSuccess = function (message) {
		_add(message, 'success');
	};

	/**
  * Adds a yellow warning message.
  *
  * @param {string} message Message to be displayed.
  */
	exports.addWarning = function (message) {
		_add(message, 'warning');
	};
})(jse.libs.info_messages);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluZm9fbWVzc2FnZXMuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsImluZm9fbWVzc2FnZXMiLCJleHBvcnRzIiwiJG1lc3NhZ2VzQ29udGFpbmVyIiwiJCIsIl9hZGQiLCJtZXNzYWdlIiwidHlwZSIsIiRhbGVydCIsImZpbmQiLCJvbiIsInBhcmVudCIsImhpZGUiLCJhcHBlbmQiLCJzaG93IiwidHJ1bmNhdGUiLCJlbXB0eSIsImFkZEVycm9yIiwiYWRkSW5mbyIsImFkZFN1Y2Nlc3MiLCJhZGRXYXJuaW5nIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLElBQUlDLElBQUosQ0FBU0MsYUFBVCxHQUF5QkYsSUFBSUMsSUFBSixDQUFTQyxhQUFULElBQTBCLEVBQW5EOztBQUVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBeUJBLENBQUMsVUFBU0MsT0FBVCxFQUFrQjs7QUFFbEI7O0FBRUE7Ozs7OztBQUtBLEtBQUlDLHFCQUFxQkMsRUFBRSwwQ0FBRixDQUF6Qjs7QUFFQTs7Ozs7Ozs7QUFRQSxLQUFJQyxPQUFPLFNBQVBBLElBQU8sQ0FBU0MsT0FBVCxFQUFrQkMsSUFBbEIsRUFBd0I7QUFDbEMsTUFBSUMsU0FBU0osRUFBRSw2QkFBNkJHLElBQTdCLEdBQW9DLDRDQUFwQyxHQUNkLHFFQURjLEdBQzBERCxPQUQxRCxHQUNvRSxRQUR0RSxDQUFiOztBQUdBRSxTQUFPQyxJQUFQLENBQVksUUFBWixFQUFzQkMsRUFBdEIsQ0FBeUIsT0FBekIsRUFBa0MsWUFBVztBQUM1Q04sS0FBRSxJQUFGLEVBQVFPLE1BQVIsQ0FBZSxRQUFmLEVBQXlCQyxJQUF6QjtBQUNBLEdBRkQ7O0FBSUFULHFCQUFtQlUsTUFBbkIsQ0FBMEJMLE1BQTFCO0FBQ0FMLHFCQUFtQlcsSUFBbkI7QUFDQSxFQVZEOztBQVlBOzs7QUFHQVosU0FBUWEsUUFBUixHQUFtQixZQUFXO0FBQzdCWixxQkFBbUJhLEtBQW5CO0FBQ0EsRUFGRDs7QUFJQTs7Ozs7QUFLQWQsU0FBUWUsUUFBUixHQUFtQixVQUFTWCxPQUFULEVBQWtCO0FBQ3BDRCxPQUFLQyxPQUFMLEVBQWMsUUFBZDtBQUNBLEVBRkQ7O0FBSUE7Ozs7O0FBS0FKLFNBQVFnQixPQUFSLEdBQWtCLFVBQVNaLE9BQVQsRUFBa0I7QUFDbkNELE9BQUtDLE9BQUwsRUFBYyxNQUFkO0FBQ0EsRUFGRDs7QUFJQTs7Ozs7QUFLQUosU0FBUWlCLFVBQVIsR0FBcUIsVUFBU2IsT0FBVCxFQUFrQjtBQUN0Q0QsT0FBS0MsT0FBTCxFQUFjLFNBQWQ7QUFDQSxFQUZEOztBQUlBOzs7OztBQUtBSixTQUFRa0IsVUFBUixHQUFxQixVQUFTZCxPQUFULEVBQWtCO0FBQ3RDRCxPQUFLQyxPQUFMLEVBQWMsU0FBZDtBQUNBLEVBRkQ7QUFJQSxDQTFFRCxFQTBFR1AsSUFBSUMsSUFBSixDQUFTQyxhQTFFWiIsImZpbGUiOiJpbmZvX21lc3NhZ2VzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbmZvX21lc3NhZ2VzLmpzIDIwMTYtMDctMjdcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5qc2UubGlicy5pbmZvX21lc3NhZ2VzID0ganNlLmxpYnMuaW5mb19tZXNzYWdlcyB8fCB7fTtcblxuLyoqXG4gKiAjIyBJbmZvIE1lc3NhZ2VzIGxpYnJhcnlcbiAqXG4gKiBVc2UgdGhpcyBsaWJyYXJ5IHRvIGFkZCBtZXNzYWdlcyBpbnRvIGFkbWluJ3Mgbm90aWZpY2F0aW9uIHN5c3RlbSAodG9wIHJpZ2h0IGNvcm5lcikuIFRoZXJlIGFyZSBtdWx0aXBsZSBcbiAqIHR5cGVzIG9mIG5vdGlmaWNhdGlvbiBlbnRyaWVzICdlcnJvcicsICdpbmZvJywgJ3dhcm5pbmcnIGFuZCAnc3VjY2VzcycuIFVzZSB0aGUgcmVzcGVjdGl2ZSBtZXRob2QgZm9yIFxuICogZWFjaCBvbmUgb2YgdGhlbS4gXG4gKiBcbiAqIFlvdSB3aWxsIG5lZWQgdG8gcHJvdmlkZSB0aGUgZnVsbCBVUkwgaW4gb3JkZXIgdG8gbG9hZCB0aGlzIGxpYnJhcnkgYXMgYSBkZXBlbmRlbmN5IHRvIGEgbW9kdWxlOlxuICogXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiBneC5jb250cm9sbGVyLm1vZHVsZShcbiAqICAgJ215X2N1c3RvbV9wYWdlJyxcbiAqXG4gKiAgIFtcbiAqICAgICAgZ3guc291cmNlICsgJy9saWJzL2luZm9fbWVzc2FnZXMnXG4gKiAgIF0sXG4gKlxuICogICBmdW5jdGlvbihkYXRhKSB7XG4gKiAgICAgIC8vIE1vZHVsZSBjb2RlIC4uLiBcbiAqICAgfSk7XG4gKmBgYFxuICpcbiAqIEBtb2R1bGUgQWRtaW4vTGlicy9pbmZvX21lc3NhZ2VzXG4gKiBAZXhwb3J0cyBqc2UubGlicy5pbmZvX21lc3NhZ2VzXG4gKi9cbihmdW5jdGlvbihleHBvcnRzKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvKipcblx0ICogQ29udGFpbmVyIGVsZW1lbnQgZm9yIGluZm8gbWVzc2FnZXNcblx0ICpcblx0ICogQHR5cGUge29iamVjdH1cblx0ICovXG5cdHZhciAkbWVzc2FnZXNDb250YWluZXIgPSAkKCcubWVzc2FnZV9zdGFja19jb250YWluZXIsIC5tZXNzYWdlLXN0YWNrJyk7XG5cdFxuXHQvKipcblx0ICogQXBwZW5kcyBhIG1lc3NhZ2UgYm94IHRvIHRoZSBpbmZvIG1lc3NhZ2VzIGNvbnRhaW5lciBhbmQgZGlzcGxheXMgaXRcblx0ICpcblx0ICogQHBhcmFtIHtzdHJpbmd9IG1lc3NhZ2UgTWVzc2FnZSB0byBiZSBkaXNwbGF5ZWQuXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIE1lc3NhZ2UgdHlwZSBjYW4gYmUgb25lIG9mIHRoZSBcImluZm9cIiwgXCJ3YXJuaW5nXCIsIFwiZXJyb3JcIiAmIFwic3VjY2Vzc1wiLlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0dmFyIF9hZGQgPSBmdW5jdGlvbihtZXNzYWdlLCB0eXBlKSB7XG5cdFx0dmFyICRhbGVydCA9ICQoJzxkaXYgY2xhc3M9XCJhbGVydCBhbGVydC0nICsgdHlwZSArICdcIiBkYXRhLWd4LWNvbXBhdGliaWxpdHk9XCJjbG9zZV9hbGVydF9ib3hcIj4nICtcblx0XHRcdCc8YnV0dG9uIHR5cGU9XCJidXR0b25cIiBjbGFzcz1cImNsb3NlXCIgZGF0YS1kaXNtdXNzPVwiYWxlcnRcIj7DlzwvYnV0dG9uPicgKyBtZXNzYWdlICsgJzwvZGl2PicpO1xuXHRcdFxuXHRcdCRhbGVydC5maW5kKCcuY2xvc2UnKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcblx0XHRcdCQodGhpcykucGFyZW50KCcuYWxlcnQnKS5oaWRlKCk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0JG1lc3NhZ2VzQ29udGFpbmVyLmFwcGVuZCgkYWxlcnQpO1xuXHRcdCRtZXNzYWdlc0NvbnRhaW5lci5zaG93KCk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogUmVtb3ZlcyBhbGwgbWVzc2FnZXMgaW5zaWRlIHRoZSBtZXNzYWdlIGNvbnRhaW5lci5cblx0ICovXG5cdGV4cG9ydHMudHJ1bmNhdGUgPSBmdW5jdGlvbigpIHtcblx0XHQkbWVzc2FnZXNDb250YWluZXIuZW1wdHkoKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBBZGRzIGEgcmVkIGVycm9yIG1lc3NhZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBtZXNzYWdlIE1lc3NhZ2UgdG8gYmUgZGlzcGxheWVkLlxuXHQgKi9cblx0ZXhwb3J0cy5hZGRFcnJvciA9IGZ1bmN0aW9uKG1lc3NhZ2UpIHtcblx0XHRfYWRkKG1lc3NhZ2UsICdkYW5nZXInKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBBZGRzIGEgYmx1ZSBpbmZvIG1lc3NhZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBtZXNzYWdlIE1lc3NhZ2UgdG8gYmUgZGlzcGxheWVkLlxuXHQgKi9cblx0ZXhwb3J0cy5hZGRJbmZvID0gZnVuY3Rpb24obWVzc2FnZSkge1xuXHRcdF9hZGQobWVzc2FnZSwgJ2luZm8nKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBBZGRzIGEgZ3JlZW4gc3VjY2VzcyBtZXNzYWdlLlxuXHQgKlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gbWVzc2FnZSBNZXNzYWdlIHRvIGJlIGRpc3BsYXllZC5cblx0ICovXG5cdGV4cG9ydHMuYWRkU3VjY2VzcyA9IGZ1bmN0aW9uKG1lc3NhZ2UpIHtcblx0XHRfYWRkKG1lc3NhZ2UsICdzdWNjZXNzJyk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogQWRkcyBhIHllbGxvdyB3YXJuaW5nIG1lc3NhZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBtZXNzYWdlIE1lc3NhZ2UgdG8gYmUgZGlzcGxheWVkLlxuXHQgKi9cblx0ZXhwb3J0cy5hZGRXYXJuaW5nID0gZnVuY3Rpb24obWVzc2FnZSkge1xuXHRcdF9hZGQobWVzc2FnZSwgJ3dhcm5pbmcnKTtcblx0fTtcblx0XG59KShqc2UubGlicy5pbmZvX21lc3NhZ2VzKTtcbiJdfQ==
