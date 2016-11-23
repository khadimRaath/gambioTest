/* --------------------------------------------------------------
 button_dropdown.js 2016-06-10
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.button_dropdown = jse.libs.button_dropdown || {};

/**
 * ## Button Dropdown Library
 *
 * This library contains helper functions that make the manipulation of a button dropdown
 * widget easier.
 *
 * You will need to provide the full URL in order to load this library as a dependency to a module:
 *
 * ```javascript
 * gx.controller.module(
 *   'my_custom_page',
 *
 *   [
 *      gx.source + '/libs/button_dropdown'
 *   ],
 *
 *   function(data) {
 *      // Module code ... 
 *   });
 *```
 *
 * ### Example
 *
 * ```javascript
 * var $buttonDropdown = $('#my.js-button-dropdown');
 *
 * // Map an action to a dropdown item.
 * jse.libs.button_dropdown.mapAction($buttonDropdown, action, section, callback, $targetRecentButton);
 *
 * // Change recent button.
 * jse.libs.button_dropdown.changeDefualtButton($buttonDropdown, text, callback, $targetRecentButton);
 *
 * // Add a separator in a dropdown list.
 * jse.libs.button_dropdown.addDropdownSeperator($buttonDropdown);
 * ```
 *
 * @todo Further improve the code and the comments of this library.
 *
 * @module Admin/Libs/button_dropdown
 * @exports jse.libs.button_dropdown
 */
(function(exports) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------
	
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
	 * @param {object} options See bind documentation.
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
	 * Initializes the default button.
	 *
	 * @param {object} $dropdown The affected button dropdown selector.
	 * @param {object} configValue Configuration value that comes from the UserConfigurationService.
	 * @param {object} title The caption of the default action button.
	 * @param {object} callback (optional) Callback function for the new action.
	 * @param {object} $targetDefaultButton (optional) Selector for the default button.
	 */
	var _initDefaultAction = function($dropdown, configValue, title, callback, $targetDefaultButton) {
		var interval = setInterval(function() {
			if (typeof $dropdown.attr('data-configuration_value') !== 'undefined') {
				// Sets the recent action button loaded from database.
				if ($dropdown.attr('data-configuration_value') === configValue) {
					exports.changeDefaultAction($dropdown, title, callback, $targetDefaultButton);
				}
				
				clearInterval(interval);
			}
		}, 300);
	};
	
	// ------------------------------------------------------------------------
	// PUBLIC METHODS
	// ------------------------------------------------------------------------
	
	/**
	 * Adds a new item to the dropdown.
	 *
	 * @param {string} translationPhrase Translation phrase key.
	 * @param {string} translationSection Translation section of the phrase.
	 * @param {function} customCallback Define a custom callback.
	 * @param {object} $targetDefaultButton (optional) A custom selector which dropdown buttons should be changed.
	 */
	exports.mapAction =
		function($dropdown, translationPhrase, translationSection, customCallback, $targetDefaultButton) {
			var $target = $targetDefaultButton || $dropdown,
				title = (translationSection !== '')
					? jse.core.lang.translate(translationPhrase, translationSection)
					: translationPhrase;
			
			// Sets the first action as recent action button, if no recent action has benn set so far.
			if (!$dropdown.find('ul li').length && $dropdown.find('button:first').text().trim() === '') {
				exports.changeDefaultAction($dropdown, title, customCallback, $target);
			}
			
			_initDefaultAction($dropdown, translationPhrase, title, customCallback, $target);
			
			var options = {
				action: translationPhrase,
				$dropdown: $dropdown,
				title: title,
				event: 'perform:action',
				callback: function(event) {
					customCallback(event);
					exports.changeDefaultAction($(this), title, customCallback, $target);
				}
			};
			
			_bindEvent(options);
		};
	
	/**
	 * Changes the default action of the button.
	 *
	 * @param {object} $button The affected button dropdown widget.
	 * @param {string} title Text of the new button.
	 * @param {string} callback The callback
	 * @param {object} $targetDefaultButton A custom element for which button should be changed.
	 */
	exports.changeDefaultAction = function($dropdown, title, callback, $targetDefaultButton) {
		var $target = $targetDefaultButton || $dropdown,
			icon = $target.data('icon');
		
		if (title.length) {
			$target
				.find('button:first')
				.off('perform:action')
				.on('perform:action', callback);
		}
		
		$target
			.find('button:first')
			.text(title);
		
		$target
			.find('button:first')
			.prop('title', title.trim());
		
		if (typeof icon !== 'undefined') {
			$target
				.find('button:first')
				.prepend($('<i class="fa fa-' + icon + ' btn-icon"></i>'));
		}
	};
	
	/**
	 * Add button-dropdown action.
	 *
	 * This method works with the Bootstrap markup button-dropdowns and enables you to add actions with callbacks
	 * existing button dropdown elements.
	 *
	 * The action object can have the following attributes and default values:
	 *
	 * {
	 *   text: '{Undefined}', // The text to be displayed. 
	 *   href: '#', // URL for the <a> element.  
	 *   target: '', // Target attribute for <a> element. 
	 *   class: '', // Add custom classes to the <a> element.
	 *   data: {}, // Add data to the <a> element. 
	 *   isDefault: false, // Whether the action is the default action. 
	 *   callback: function(e) {} // Callback for click event of the <a> element. 
	 * }
	 *
	 * @param {object} $dropdown The jQuery selector of the button dropdown wrapper.
	 * @param {object} action An object containing the action information.
	 */
	exports.addAction = function($dropdown, action) {
		let $li = $('<li/>'),
			$a = $('<a />');
		
		$a
			.text(action.text || '{Undefined}')
			.attr('href', action.href || '#')
			.attr('target', action.target || '')
			.addClass(action.class || '')
			.data(action.data)
			.appendTo($li);
		
		if (action.isDefault) {
			exports.setDefaultAction($dropdown, $a); 
		}
		
		if (action.callback) {
			$a.on('click', action.callback);
		}
		
		$li.appendTo($dropdown.find('ul'));
	};
	
	/**
	 * Adds a separator to the dropdown list.
	 *
	 * The separator will be added at the end of the list.
	 *
	 * @param {object} $dropdown The jQuery selector of the button dropdown wrapper.
	 * @param {bool} compatibilityMarkup (optional) Whether to use the compatibility markup.
	 */
	exports.addSeparator = function($dropdown, compatibilityMarkup = false) {
		let html = !compatibilityMarkup ? '<li role="separator" class="divider"></li>' : '<li><hr></li>';
		$dropdown.find('ul').append(html);
	};
	
	/**
	 * Bind button dropdown default action. 
	 * 
	 * This method will update the default action of the dropdown upon click. This is useful for storing the 
	 * default actions and then using them to display the default one with every new instance of the button 
	 * dropdown. 
	 * 
	 * Important: The <a> elements need to have the "configurationValue" data property that defines a unique string
	 * for the selected action. 
	 * 
	 * @param {object} $dropdowns The jQuery selector of the button dropdowns wrapper.
	 * @param {number} userId The ID of the current user. 
	 * @param {string} configurationKey The configuration key to be saved. 
	 * @param {object} userConfigurationService The user_configuration_service module (needs to be passed explicitly).  
	 */
	exports.bindDefaultAction = function($dropdowns, userId, configurationKey,  userConfigurationService) {
		$dropdowns.on('click', 'a', function() {
			const params = {
				data: {
					userId,
					configurationKey, 
					configurationValue: $(this).data('configurationValue')
				}
			}; 
			
			userConfigurationService.set(params);
			
			exports.setDefaultAction($dropdowns, $(this)); 
		}); 
	};
	
	/**
	 * Set the default action item for button dropdowns.
	 *
	 * @param {object} $dropdowns jQuery selector for the button dropdowns.
	 * @param {object} $actionLink jQuery selector for the action link to be set as default.
	 */
	exports.setDefaultAction = function($dropdowns, $actionLink) {
		$dropdowns.each((index, dropdown) => {
			const $dropdownButton = $(dropdown).children('button:first');
			
			$dropdownButton
				.text($actionLink.text())
				.off('click')
				.on('click', () => {
					// Do nothing when the dropdown is grayed out.
					if ($dropdownButton.hasClass('disabled')) {
						return;
					}
					$(dropdown)
						.find(`li:eq(${$actionLink.parent().index()}) a`)[0]
						.click();
				});
		});
	};
	
})(jse.libs.button_dropdown);
