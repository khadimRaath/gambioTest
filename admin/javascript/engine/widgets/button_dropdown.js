/* --------------------------------------------------------------
 button_dropdown.js 2016-07-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Button Dropdown Widget
 *
 * Adds the dropdown functionality to multiple elements inside a parent container. You can add new HTML 
 * options to each dropdown instance manually or dynamically through the Admin/Libs/button_dropdown library. 
 * 
 * Optionally, the widget has also the ability to store the last clicked option and display it as the default 
 * action the next time the page is loaded. This is very useful whenever there are many options inside the 
 * dropdown list.
 * 
 * ### Parent Container Options
 * 
 * **Configuration Keys | `data-button_dropdown-config_keys` | String | Optional**
 * 
 * Provide a unique key which will be used to store the latest user selection. Prefer to prefix your config key 
 * in order to avoid collisions with other instances of the widget.
 * 
 * **User ID | `data-button_dropdown-user_id` | Number | Optional** 
 * 
 * Give the current user database ID that will be used to associate his latest selection with the corresponding 
 * button dropdown widget.
 * 
 * ### Widget Instance Options
 * 
 * **Use Button Dropdown | `data-use-button_dropdown` | Boolean | Required** 
 * 
 * This option-flag will mark the elements inside the parent container, that will be converted into 
 * button-dropdown widgets.
 * 
 * **Configuration Key | `data-config_key` | String | Required**
 * 
 * Provide the configuration key for the single button-dropdown instance.
 * 
 * **Configuration Value | `data-config_key` | String | Optional**
 *
 * Provide directly the configuration value in order to avoid extra AJAX requests.
 * 
 * **Custom Caret Button Class | `data-custom_caret_btn_class` | String | Optional**
 * 
 * Attach additional classes to the caret button element (the one with the arrow). Use this option if you 
 * want to add a class that the primary button already has so that both share the same style (e.g. btn-primary).
 * 
 * ### Example
 * ```html
 * <!-- This element represents the parent container. -->
 * <div
 *   data-gx-widget="button_dropdown"
 *   data-button_dropdown-config_keys="order-single order-multi"
 *   data-button_dropdown-user_id="2">
 * 
 *   <!-- This element represents the button dropdown widget. -->
 *   <div
 *       data-use-button_dropdown="true" 
 *       data-config_key="order-single"
 *       data-custom_caret_btn_class="class1">
 *     <button>Primary Button</button>
 *     <ul>
 *       <li><span>Change status</span></li>
 *       <li><span>Delete</span></li>
 *     </ul>
 *   </div>
 * </div>
 * ```
 *
 * **Notice:** This widget was built for usage in compatibility mode. The new admin pages use the Bootstrap
 * button dropdown markup which already functions like this module. Do not use it on new admin pages. 
 * 
 * @module Admin/Widgets/button_dropdown
 */
gx.widgets.module(
	'button_dropdown',
	
	['user_configuration_service'],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Widget Reference
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * UserConfigurationService alias.
			 * @type {object}
			 */
			userConfigurationService = jse.libs.user_configuration_service,
			
			/**
			 * Caret button template.
			 * @type {string}
			 */
			caretButtonTemplate = '<button class="btn" type="button"><i class="fa fa-caret-down"></i></button>',
			
			/**
			 * Default Widget Options
			 * @type {object}
			 */
			defaults = {
				/**
				 * Fade animation options.
				 * @type {object}
				 */
				fade: {
					duration: 300,
					easing: 'swing'
				},
				
				/**
				 * String for dropdown selector.
				 * This selector is used to find and activate all button dropdowns.
				 *
				 * @type {string}
				 */
				dropdown_selector: '[data-use-button_dropdown]',
				
				/**
				 * Attribute which represents the user configuration value.
				 * The value of this attribute will be set.
				 *
				 * @type {string}
				 */
				config_value_attribute: 'data-configuration_value'
			},
			
			/**
			 * Final Widget Options
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Element selector shortcuts.
			 * @type {object}
			 */
			selectors = {
				element: options.dropdown_selector,
				mainButton: 'button:nth-child(1)',
				caretButton: 'button:nth-child(2)'
			},
			
			/**
			 * Module Object
			 * @type {object}
			 */
			module = {};
		
		/**
		 * Split space-separated entries to array values.
		 * @type {array}
		 */
		options.config_keys = options.config_keys ? options.config_keys.split(' ') : [];
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS - INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Loads the user configuration values for each provided key.
		 * Returns a Deferred object with an object with configuration
		 * as key and respective values or null if no request conditions are set.
		 *
		 * @returns {jQuery.Deferred}
		 * @private
		 */
		var _loadConfigurations = function() {
			
			/**
			 * Main deferred object which will be returned.
			 * @type {jQuery.Deferred}
			 */
			var deferred = $.Deferred();
			
			/**
			 * This array will contain all deferred ajax request to the user configuration service.
			 * @example
			 *      [Deferred, Deferred]
			 * @type {array}
			 */
			var configDeferreds = [];
			
			/**
			 * User configuration key and values storage.
			 * @example
			 *      {
			 *          configKey: 'configValue'
			 *      }
			 * @type {object}
			 */
			var configValues = {};
			
			// Return immediately if the user configuration service is not needed.
			if (!options.user_id || !options.config_keys.length) {
				return deferred.resolve(null);
			}
			
			// Iterate over each configuration value provided in the element
			$.each(options.config_keys, function(index, configKey) {
				// Create deferred object for configuration value fetch.
				var configDeferred = $.Deferred();
				
				// Fetch configuration value from service.
				// Adds the fetched value to the `configValues` object and resolves the promise.
				userConfigurationService.get({
					data: {
						userId: options.user_id,
						configurationKey: configKey
					},
					onSuccess: function(response) {
						configValues[configKey] = response.configurationValue;
						configDeferred.resolve();
					},
					onError: function() {
						configDeferred.resolve();
					}
				});
				
				configDeferreds.push(configDeferred);
			});
			
			// If all requests for the configuration values has been processed
			// then the main promise will be resolved with all configuration values as given parameter.
			$.when.apply(null, configDeferreds).done(function() {
				deferred.resolve(configValues);
			});
			
			// Return deferred object.
			return deferred;
		};
		
		/**
		 * Finds all dropdown elements.
		 * Returns a deferred object with an element list object.
		 * This function hides the dropdown elements.
		 *
		 * @return {jQuery.Deferred}
		 * @private
		 */
		var _findDropdownElements = function() {
			/**
			 * Deferred object which will be returned.
			 * @type {jQuery.Deferred}
			 */
			var deferred = $.Deferred();
			
			/**
			 * Elements with element and data attribute informations.
			 * @example
			 *      [{
			 *          element: <div>,
			 *          custom_caret_btn_class: 'btn-primary'
			 *          configKey: 'orderMultiSelect'
			 *      }]
			 * @type {array}
			 */
			var elements = [];
			
			/**
			 * Array of data attributes for the dropdown elements which will be checked.
			 * @type {array}
			 */
			var dataAttributes = ['custom_caret_btn_class', 'config_key', 'config_value'];
			
			// Find dropdown elements when DOM is ready
			// and resolve promise passing found elements as parameter.
			$(document).ready(function() {
				$this.find(options.dropdown_selector).each(function(index, element) {
					/**
					 * jQuery wrapped element shortcut.
					 * @type {jQuery}
					 */
					var $element = $(element);
					
					/**
					 * Element info object.
					 * Will be pushed to `elements` array.
					 * @example
					 *      {
					 *          element: <div>,
					 *          custom_caret_btn_class: 'btn-primary'
					 *          configKey: 'orderMultiSelect'
					 *      }
					 * @type {object}
					 */
					var elementObject = {};
					
					// Add element to element info object.
					elementObject.element = element;
					
					// Iterate over each data attribute key and check for data attribute existence.
					// If data-attribute exists, the key and value will be added to element info object.
					$.each(dataAttributes, function(index, attribute) {
						if (attribute in $element.data()) {
							elementObject[attribute] = $element.data(attribute);
						}
					});
					
					// Push this element info object to `elements` array.
					elements.push(elementObject);
					
					// Hide element
					$element.hide();
				});
				
				// Resolve the promise passing in the elements as argument.
				deferred.resolve(elements);
			});
			
			// Return deferred object.
			return deferred;
		};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS - DROPDOWN TOGGLE
		// ------------------------------------------------------------------------
		
		/**
		 * Shows dropdown action list.
		 *
		 * @param {HTMLElement} element Dropdown action list element.
		 * @private
		 */
		var _showDropdown = function(element) {
			// Perform fade in.
			$(element)
				.stop()
				.addClass('hover')
				.fadeIn(options.fade);
			
			// Fix position.
			_repositionDropdown(element);
		};
		
		/**
		 * Hides dropdown action list.
		 *
		 * @param {HTMLElement} element Dropdown action list element.
		 * @private
		 */
		var _hideDropdown = function(element) {
			// Perform fade out.
			$(element)
				.stop()
				.removeClass('hover')
				.fadeOut(options.fade);
		};
		
		/**
		 * Fixes the dropdown action list to ensure that the action list is always visible.
		 *
		 * Sometimes when the button dropdown widget is near the window borders the list might
		 * not be visible. This function will change its position in order to always be visible.
		 *
		 * @param {HTMLElement} element Dropdown action list element.
		 * @private
		 */
		var _repositionDropdown = function(element) {
			// Wrap element in jQuery and save shortcut to dropdown action list element.
			var $list = $(element);
			
			// Reference to button element.
			var $button = $list.closest(options.dropdown_selector);
			
			// Reset any possible CSS position modifications.
			$list.css({left: '', top: ''});
			
			// Check dropdown position and perform reposition if needed.
			if ($list.offset().left + $list.width() > window.innerWidth) {
				var toMoveLeftPixels = $list.width() - $button.width();
				$list.css('margin-left', '-' + (toMoveLeftPixels) + 'px');
			}
			
			if ($list.offset().top + $list.height() > window.innerHeight) {
				var toMoveUpPixels = $list.height() + 10; // 10px fine-tuning
				$list.css('margin-top', '-' + (toMoveUpPixels) + 'px');
			}
		};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS - EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * Handles click events on the main button (action button).
		 * Performs main button action.
		 *
		 * @param {jQuery.Event} event
		 * @private
		 */
		var _mainButtonClickHandler = function(event) {
			event.preventDefault();
			event.stopPropagation();
			
			$(this).trigger('perform:action');
		};
		
		/**
		 * Handles click events on the dropdown button (caret button).
		 * Shows or hides the dropdown action list.
		 *
		 * @param {jQuery.Event} event
		 * @private
		 */
		var _caretButtonClickHandler = function(event) {
			event.preventDefault();
			event.stopPropagation();
			
			/**
			 * Shortcut reference to dropdown action list element.
			 * @type {jQuery}
			 */
			var $list = $(this).siblings('ul');
			
			/**
			 * Determines whether the dropdown action list is visible.
			 * @type {boolean}
			 */
			var listIsVisible = $list.hasClass('hover');
			
			// Hide or show dropdown, dependent on its visibility state.
			if (listIsVisible) {
				_hideDropdown($list);
			} else {
				_showDropdown($list);
			}
		};
		
		/**
		 * Handles click events on the dropdown action list.
		 * Hides the dropdown, saves the chosen value through
		 * the user configuration service and perform the selected action.
		 *
		 * @param {jQuery.Event} event
		 * @private
		 */
		var _listItemClickHandler = function(event) {
			event.preventDefault();
			event.stopPropagation();
			
			/**
			 * Reference to `this` element, wrapped in jQuery.
			 * @type {jQuery}
			 */
			var $self = $(this);
			
			/**
			 * Reference to dropdown action list element.
			 * @type {jQuery}
			 */
			var $list = $self.closest('ul');
			
			/**
			 * Reference to button dropdown element.
			 * @type {jQuery}
			 */
			var $button = $self.closest(options.dropdown_selector);
			
			// Hide dropdown.
			_hideDropdown($list);
			
			// Save user configuration data.
			var configKey = $button.data('config_key'),
				configValue = $self.data('value');
			
			if (configKey && configValue) {
				_saveUserConfiguration(configKey, configValue);
			}
			
			// Perform action.
			$self.trigger('perform:action');
		};
		
		/**
		 * Handles click events outside of the button area.
		 * Hides multiple opened dropdowns.
		 * @param {jQuery.Event} event
		 * @private
		 */
		var _outsideClickHandler = function(event) {
			/**
			 * Element shortcut to all opened dropdown action lists.
			 * @type {jQuery}
			 */
			var $list = $('ul.hover');
			
			// Hide all opened dropdowns.
			_hideDropdown($list);
		};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS - CREATE WIDGETS
		// ------------------------------------------------------------------------
		
		/**
		 * Adds the dropdown functionality to the buttons.
		 *
		 * Developers can manually add new `<li>` items to the list in order to display more options
		 * to the users.
		 *
		 * This function fades the dropdown elements in.
		 *
		 * @param {array} elements List of elements infos object which contains the element itself and data attributes.
		 * @param {object} configuration Object with fetched configuration key and values.
		 *
		 * @return {jQuery.Deferred}
		 * @private
		 */
		var _makeWidgets = function(elements, configuration) {
			
			/**
			 * Deferred object which will be returned.
			 * @type {jQuery.Deferred}
			 */
			var deferred = $.Deferred();
			
			/**
			 * The secondary button which will toggle the list visibility.
			 * @type {jQuery}
			 */
			var $secondaryButton = $(caretButtonTemplate);
			
			// Iterate over each element and create dropdown widget.
			$.each(elements, function(index, elementObject) {
				/**
				 * Button dropdown element.
				 * @type {jQuery}
				 */
				var $element = $(elementObject.element);
				
				/**
				 * Button dropdown element's buttons.
				 * @type {jQuery}
				 */
				var $button = $element.find('button:first');
				
				/**
				 * Cloned caret button template.
				 * @type {jQuery}
				 */
				var $caretButton = $secondaryButton.clone();
				
				// Add custom class to template, if defined.
				if (elementObject.custom_caret_btn_class) {
					$caretButton.addClass(elementObject.custom_caret_btn_class);
				}
				
				// Add CSS class to button and place the caret button.
				$button
					.addClass('btn')
					.after($caretButton);
				
				// Add class to dropdown button element.
				$element
					.addClass('js-button-dropdown');
				
				// Add configuration value to container, if key and value exist.
				if (configuration && elementObject.config_key && configuration[elementObject.config_key] 
					|| elementObject.config_value) {
					var value = elementObject.config_value || configuration[elementObject.config_key]; 
					$element.attr(options.config_value_attribute, value);
				} 
				
				// Attach event handler: Click on first button (main action button).
				$element.on('click', selectors.mainButton, _mainButtonClickHandler);
				
				// Attach event handler: Click on dropdown button (caret button).
				$element.on('click', selectors.caretButton, _caretButtonClickHandler);
				
				// Attach event handler: Click on dropdown action list item.
				$element.on('click', 'ul span, ul a', _listItemClickHandler);
				
				// Fade in element.
				$element.fadeIn(options.fade.duration, function() {
					$element.css('display', '');
				});
			});
			
			// Attach event handler: Close dropdown on outside click.
			$(document).on('click', _outsideClickHandler);
			
			// Resolve promise.
			deferred.resolve();
			
			// Return deferred object.
			return deferred;
		};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS - SAVE USER CONFIGURATION
		// ------------------------------------------------------------------------
		
		/**
		 * Saves a user configuration value.
		 *
		 * @param {string} key Configuration key.
		 * @param {string} value Configuration value.
		 * @private
		 */
		var _saveUserConfiguration = function(key, value) {
			// Throw error if no complete data has been provided.
			if (!key || !value) {
				throw new Error('No configuration data passed');
			}
			
			// Save value to database via user configuration service.
			userConfigurationService.set({
				data: {
					userId: options.user_id,
					configurationKey: key,
					configurationValue: value
				}
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			$.when(_findDropdownElements(), _loadConfigurations())
				.then(_makeWidgets)
				.then(done);
		};
		
		// Return data to module engine.
		return module;
	});
