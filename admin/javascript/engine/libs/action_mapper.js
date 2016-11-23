/* --------------------------------------------------------------
 action_mapper.js 2016-02-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.action_mapper = jse.libs.action_mapper || {};

/**
 * ## Action Mapper Library
 *
 * Maps a dropdown button action item event to another page element ($button). This library
 * must be used to quickly redirect user actions to existing but hidden UI elements like table row
 * actions. When a callback function is passed as an argument the action item will override the default
 * behaviour.
 *
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 * 
 * ```javascript
 * gx.controller.module(
 *   'my_custom_page',
 *   
 *   [
 *      gx.source + '/libs/action_mapper'   
 *   ],
 *   
 *   function(data) {
 *      // Module code ... 
 *   }); 
 *```
 * ### Example
 *
 * The HTML for the target button: 
 * 
 * ```html
 * <button id="button1">Button 1</button>
 * ```
 *
 * The JavaScript code that will map an action to to a button dropdown widget for the target button:
 * 
 * ```javascript
 * // Define a custom callback function.
 * function customCallbackFunc(event) {
 *     console.log('Function called!');
 * };
 *
 * // Map an event to a new dropdown action item.
 * var options = {
 *   // A new action item will be created in this widget.
 *   $dropdown: $('#button-dropdown'), 
 *
 *   // Target element will be triggered when the user clicks the dropdown action item.  
 *   $target: $('#target-button'), 
 *   
 *   // Target event name to be triggered.
 *   event: 'click',   
 *   
 *   // (optional) Provide a function to override the default event handler.
 *   callback: customCallbackFunc, 
 *   
 *   // (optional) Add a custom action title for the dropdown button.
 *   title: 'Action Title' 
 * }
 * 
 * jse.libs.action_mapper.bind(options);
 * ```
 *
 * By clicking on the "Button 1" you will receive a "Function called!" in the console!
 *
 * @module Admin/Libs/action_mapper
 * @exports jse.libs.action_mapper
 */
(function(exports) {
	
	'use strict';
	
	/**
	 * Triggers a specific event from an element.
	 *
	 * Some situations require a different approach than just using the "trigger" method.
	 *
	 * @param {object} $element Destination element to be triggered.
	 * @param {object} event Event options can be used for creating new conditions.
	 *
	 * @private
	 */
	var _triggerEvent = function($element, event) {
		if ($element.prop('tagName') === 'A' && event.type === 'click') {
			$element.get(0).click();
		} else {
			$element.trigger(event.type);
		}
	};
	
	/**
	 * Binds the event to a new dropdown action item.
	 *
	 * @param options See bind documentation.
	 *
	 * @private
	 */
	var _bindEvent = function(options) {
		var $dropdown = options.$dropdown,
			action = options.action,
			$target = options.$target,
			eventName = options.event,
			callback = options.callback || false,
			title = options.title || (options.$target.length ? options.$target.text() : '<No Action Title Provided>'),
			$li = $('<li></li>');
		
		$li.html('<span data-value="' + action + '">' + title + '</span>');
		$dropdown.find('ul').append($li);
		
		$li.find('span').on(eventName, function(event) {
			if (callback !== false) {
				//event.preventDefault();
				//event.stopPropagation();
				callback.call($li.find('span'), event);
			} else {
				_triggerEvent($target, event);
			}
		});
	};
	
	/**
	 * Binds the event
	 *
	 * This method is the initializing point for all event bindings.
	 *
	 * @param {object} options Contains all elements, function and event description
	 * @param {string} options.$dropdown Selector for the button dropdown element (div).
	 * @param {string} [options.$target] (optional) Selector for the target element of the mapping.
	 * @param {string} options.event The name of the event. The event will be triggered on source and
	 * destination element (e.g. "click", "mouseleave").
	 * @param {function} [options.callback] (optional) Function that will be called when the event of the
	 * destination element is triggered. OVERWRITES THE ACTUAL EVENT FOR THE  DESTINATION ELEMENT.
	 * @param {string} title (optional) Provide an action title for the dropdown if no $target was defined.
	 */
	exports.bind = function(options) {
		_bindEvent(options);
	};
	
})(jse.libs.action_mapper);
