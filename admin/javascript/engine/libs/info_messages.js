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
(function(exports) {
	
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
	var _add = function(message, type) {
		var $alert = $('<div class="alert alert-' + type + '" data-gx-compatibility="close_alert_box">' +
			'<button type="button" class="close" data-dismuss="alert">×</button>' + message + '</div>');
		
		$alert.find('.close').on('click', function() {
			$(this).parent('.alert').hide();
		});
		
		$messagesContainer.append($alert);
		$messagesContainer.show();
	};
	
	/**
	 * Removes all messages inside the message container.
	 */
	exports.truncate = function() {
		$messagesContainer.empty();
	};
	
	/**
	 * Adds a red error message.
	 *
	 * @param {string} message Message to be displayed.
	 */
	exports.addError = function(message) {
		_add(message, 'danger');
	};
	
	/**
	 * Adds a blue info message.
	 *
	 * @param {string} message Message to be displayed.
	 */
	exports.addInfo = function(message) {
		_add(message, 'info');
	};
	
	/**
	 * Adds a green success message.
	 *
	 * @param {string} message Message to be displayed.
	 */
	exports.addSuccess = function(message) {
		_add(message, 'success');
	};
	
	/**
	 * Adds a yellow warning message.
	 *
	 * @param {string} message Message to be displayed.
	 */
	exports.addWarning = function(message) {
		_add(message, 'warning');
	};
	
})(jse.libs.info_messages);
