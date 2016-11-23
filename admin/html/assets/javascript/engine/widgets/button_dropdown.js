'use strict';

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
gx.widgets.module('button_dropdown', ['user_configuration_service'], function (data) {

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
	var _loadConfigurations = function _loadConfigurations() {

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
		$.each(options.config_keys, function (index, configKey) {
			// Create deferred object for configuration value fetch.
			var configDeferred = $.Deferred();

			// Fetch configuration value from service.
			// Adds the fetched value to the `configValues` object and resolves the promise.
			userConfigurationService.get({
				data: {
					userId: options.user_id,
					configurationKey: configKey
				},
				onSuccess: function onSuccess(response) {
					configValues[configKey] = response.configurationValue;
					configDeferred.resolve();
				},
				onError: function onError() {
					configDeferred.resolve();
				}
			});

			configDeferreds.push(configDeferred);
		});

		// If all requests for the configuration values has been processed
		// then the main promise will be resolved with all configuration values as given parameter.
		$.when.apply(null, configDeferreds).done(function () {
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
	var _findDropdownElements = function _findDropdownElements() {
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
		$(document).ready(function () {
			$this.find(options.dropdown_selector).each(function (index, element) {
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
				$.each(dataAttributes, function (index, attribute) {
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
	var _showDropdown = function _showDropdown(element) {
		// Perform fade in.
		$(element).stop().addClass('hover').fadeIn(options.fade);

		// Fix position.
		_repositionDropdown(element);
	};

	/**
  * Hides dropdown action list.
  *
  * @param {HTMLElement} element Dropdown action list element.
  * @private
  */
	var _hideDropdown = function _hideDropdown(element) {
		// Perform fade out.
		$(element).stop().removeClass('hover').fadeOut(options.fade);
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
	var _repositionDropdown = function _repositionDropdown(element) {
		// Wrap element in jQuery and save shortcut to dropdown action list element.
		var $list = $(element);

		// Reference to button element.
		var $button = $list.closest(options.dropdown_selector);

		// Reset any possible CSS position modifications.
		$list.css({ left: '', top: '' });

		// Check dropdown position and perform reposition if needed.
		if ($list.offset().left + $list.width() > window.innerWidth) {
			var toMoveLeftPixels = $list.width() - $button.width();
			$list.css('margin-left', '-' + toMoveLeftPixels + 'px');
		}

		if ($list.offset().top + $list.height() > window.innerHeight) {
			var toMoveUpPixels = $list.height() + 10; // 10px fine-tuning
			$list.css('margin-top', '-' + toMoveUpPixels + 'px');
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
	var _mainButtonClickHandler = function _mainButtonClickHandler(event) {
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
	var _caretButtonClickHandler = function _caretButtonClickHandler(event) {
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
	var _listItemClickHandler = function _listItemClickHandler(event) {
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
	var _outsideClickHandler = function _outsideClickHandler(event) {
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
	var _makeWidgets = function _makeWidgets(elements, configuration) {

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
		$.each(elements, function (index, elementObject) {
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
			$button.addClass('btn').after($caretButton);

			// Add class to dropdown button element.
			$element.addClass('js-button-dropdown');

			// Add configuration value to container, if key and value exist.
			if (configuration && elementObject.config_key && configuration[elementObject.config_key] || elementObject.config_value) {
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
			$element.fadeIn(options.fade.duration, function () {
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
	var _saveUserConfiguration = function _saveUserConfiguration(key, value) {
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
	module.init = function (done) {
		$.when(_findDropdownElements(), _loadConfigurations()).then(_makeWidgets).then(done);
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImJ1dHRvbl9kcm9wZG93bi5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwidXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlIiwianNlIiwibGlicyIsInVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlIiwiY2FyZXRCdXR0b25UZW1wbGF0ZSIsImRlZmF1bHRzIiwiZmFkZSIsImR1cmF0aW9uIiwiZWFzaW5nIiwiZHJvcGRvd25fc2VsZWN0b3IiLCJjb25maWdfdmFsdWVfYXR0cmlidXRlIiwib3B0aW9ucyIsImV4dGVuZCIsInNlbGVjdG9ycyIsImVsZW1lbnQiLCJtYWluQnV0dG9uIiwiY2FyZXRCdXR0b24iLCJjb25maWdfa2V5cyIsInNwbGl0IiwiX2xvYWRDb25maWd1cmF0aW9ucyIsImRlZmVycmVkIiwiRGVmZXJyZWQiLCJjb25maWdEZWZlcnJlZHMiLCJjb25maWdWYWx1ZXMiLCJ1c2VyX2lkIiwibGVuZ3RoIiwicmVzb2x2ZSIsImVhY2giLCJpbmRleCIsImNvbmZpZ0tleSIsImNvbmZpZ0RlZmVycmVkIiwiZ2V0IiwidXNlcklkIiwiY29uZmlndXJhdGlvbktleSIsIm9uU3VjY2VzcyIsInJlc3BvbnNlIiwiY29uZmlndXJhdGlvblZhbHVlIiwib25FcnJvciIsInB1c2giLCJ3aGVuIiwiYXBwbHkiLCJkb25lIiwiX2ZpbmREcm9wZG93bkVsZW1lbnRzIiwiZWxlbWVudHMiLCJkYXRhQXR0cmlidXRlcyIsImRvY3VtZW50IiwicmVhZHkiLCJmaW5kIiwiJGVsZW1lbnQiLCJlbGVtZW50T2JqZWN0IiwiYXR0cmlidXRlIiwiaGlkZSIsIl9zaG93RHJvcGRvd24iLCJzdG9wIiwiYWRkQ2xhc3MiLCJmYWRlSW4iLCJfcmVwb3NpdGlvbkRyb3Bkb3duIiwiX2hpZGVEcm9wZG93biIsInJlbW92ZUNsYXNzIiwiZmFkZU91dCIsIiRsaXN0IiwiJGJ1dHRvbiIsImNsb3Nlc3QiLCJjc3MiLCJsZWZ0IiwidG9wIiwib2Zmc2V0Iiwid2lkdGgiLCJ3aW5kb3ciLCJpbm5lcldpZHRoIiwidG9Nb3ZlTGVmdFBpeGVscyIsImhlaWdodCIsImlubmVySGVpZ2h0IiwidG9Nb3ZlVXBQaXhlbHMiLCJfbWFpbkJ1dHRvbkNsaWNrSGFuZGxlciIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJ0cmlnZ2VyIiwiX2NhcmV0QnV0dG9uQ2xpY2tIYW5kbGVyIiwic2libGluZ3MiLCJsaXN0SXNWaXNpYmxlIiwiaGFzQ2xhc3MiLCJfbGlzdEl0ZW1DbGlja0hhbmRsZXIiLCIkc2VsZiIsImNvbmZpZ1ZhbHVlIiwiX3NhdmVVc2VyQ29uZmlndXJhdGlvbiIsIl9vdXRzaWRlQ2xpY2tIYW5kbGVyIiwiX21ha2VXaWRnZXRzIiwiY29uZmlndXJhdGlvbiIsIiRzZWNvbmRhcnlCdXR0b24iLCIkY2FyZXRCdXR0b24iLCJjbG9uZSIsImN1c3RvbV9jYXJldF9idG5fY2xhc3MiLCJhZnRlciIsImNvbmZpZ19rZXkiLCJjb25maWdfdmFsdWUiLCJ2YWx1ZSIsImF0dHIiLCJvbiIsImtleSIsIkVycm9yIiwic2V0IiwiaW5pdCIsInRoZW4iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBcUVBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxpQkFERCxFQUdDLENBQUMsNEJBQUQsQ0FIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7OztBQUlBQyxTQUFRQyxFQUFFLElBQUYsQ0FMVDs7O0FBT0M7Ozs7QUFJQUMsNEJBQTJCQyxJQUFJQyxJQUFKLENBQVNDLDBCQVhyQzs7O0FBYUM7Ozs7QUFJQUMsdUJBQXNCLDZFQWpCdkI7OztBQW1CQzs7OztBQUlBQyxZQUFXO0FBQ1Y7Ozs7QUFJQUMsUUFBTTtBQUNM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OEMsUUFBRixFQUFZc0QsRUFBWixDQUFlLE9BQWYsRUFBd0JaLG9CQUF4Qjs7QUFFQTtBQUNBbkUsV0FBU00sT0FBVDs7QUFFQTtBQUNBLFNBQU9OLFFBQVA7QUFDQSxFQTlFRDs7QUFnRkE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7O0FBT0EsS0FBSWtFLHlCQUF5QixTQUF6QkEsc0JBQXlCLENBQVNjLEdBQVQsRUFBY0gsS0FBZCxFQUFxQjtBQUNqRDtBQUNBLE1BQUksQ0FBQ0csR0FBRCxJQUFRLENBQUNILEtBQWIsRUFBb0I7QUFDbkIsU0FBTSxJQUFJSSxLQUFKLENBQVUsOEJBQVYsQ0FBTjtBQUNBOztBQUVEO0FBQ0FyRywyQkFBeUJzRyxHQUF6QixDQUE2QjtBQUM1QnpHLFNBQU07QUFDTG1DLFlBQVFyQixRQUFRYSxPQURYO0FBRUxTLHNCQUFrQm1FLEdBRmI7QUFHTGhFLHdCQUFvQjZEO0FBSGY7QUFEc0IsR0FBN0I7QUFPQSxFQWREOztBQWdCQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBckcsUUFBTzJHLElBQVAsR0FBYyxVQUFTOUQsSUFBVCxFQUFlO0FBQzVCMUMsSUFBRXdDLElBQUYsQ0FBT0csdUJBQVAsRUFBZ0N2QixxQkFBaEMsRUFDRXFGLElBREYsQ0FDT2hCLFlBRFAsRUFFRWdCLElBRkYsQ0FFTy9ELElBRlA7QUFHQSxFQUpEOztBQU1BO0FBQ0EsUUFBTzdDLE1BQVA7QUFDQSxDQXZqQkYiLCJmaWxlIjoiYnV0dG9uX2Ryb3Bkb3duLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBidXR0b25fZHJvcGRvd24uanMgMjAxNi0wNy0xNVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQnV0dG9uIERyb3Bkb3duIFdpZGdldFxuICpcbiAqIEFkZHMgdGhlIGRyb3Bkb3duIGZ1bmN0aW9uYWxpdHkgdG8gbXVsdGlwbGUgZWxlbWVudHMgaW5zaWRlIGEgcGFyZW50IGNvbnRhaW5lci4gWW91IGNhbiBhZGQgbmV3IEhUTUwgXG4gKiBvcHRpb25zIHRvIGVhY2ggZHJvcGRvd24gaW5zdGFuY2UgbWFudWFsbHkgb3IgZHluYW1pY2FsbHkgdGhyb3VnaCB0aGUgQWRtaW4vTGlicy9idXR0b25fZHJvcGRvd24gbGlicmFyeS4gXG4gKiBcbiAqIE9wdGlvbmFsbHksIHRoZSB3aWRnZXQgaGFzIGFsc28gdGhlIGFiaWxpdHkgdG8gc3RvcmUgdGhlIGxhc3QgY2xpY2tlZCBvcHRpb24gYW5kIGRpc3BsYXkgaXQgYXMgdGhlIGRlZmF1bHQgXG4gKiBhY3Rpb24gdGhlIG5leHQgdGltZSB0aGUgcGFnZSBpcyBsb2FkZWQuIFRoaXMgaXMgdmVyeSB1c2VmdWwgd2hlbmV2ZXIgdGhlcmUgYXJlIG1hbnkgb3B0aW9ucyBpbnNpZGUgdGhlIFxuICogZHJvcGRvd24gbGlzdC5cbiAqIFxuICogIyMjIFBhcmVudCBDb250YWluZXIgT3B0aW9uc1xuICogXG4gKiAqKkNvbmZpZ3VyYXRpb24gS2V5cyB8IGBkYXRhLWJ1dHRvbl9kcm9wZG93bi1jb25maWdfa2V5c2AgfCBTdHJpbmcgfCBPcHRpb25hbCoqXG4gKiBcbiAqIFByb3ZpZGUgYSB1bmlxdWUga2V5IHdoaWNoIHdpbGwgYmUgdXNlZCB0byBzdG9yZSB0aGUgbGF0ZXN0IHVzZXIgc2VsZWN0aW9uLiBQcmVmZXIgdG8gcHJlZml4IHlvdXIgY29uZmlnIGtleSBcbiAqIGluIG9yZGVyIHRvIGF2b2lkIGNvbGxpc2lvbnMgd2l0aCBvdGhlciBpbnN0YW5jZXMgb2YgdGhlIHdpZGdldC5cbiAqIFxuICogKipVc2VyIElEIHwgYGRhdGEtYnV0dG9uX2Ryb3Bkb3duLXVzZXJfaWRgIHwgTnVtYmVyIHwgT3B0aW9uYWwqKiBcbiAqIFxuICogR2l2ZSB0aGUgY3VycmVudCB1c2VyIGRhdGFiYXNlIElEIHRoYXQgd2lsbCBiZSB1c2VkIHRvIGFzc29jaWF0ZSBoaXMgbGF0ZXN0IHNlbGVjdGlvbiB3aXRoIHRoZSBjb3JyZXNwb25kaW5nIFxuICogYnV0dG9uIGRyb3Bkb3duIHdpZGdldC5cbiAqIFxuICogIyMjIFdpZGdldCBJbnN0YW5jZSBPcHRpb25zXG4gKiBcbiAqICoqVXNlIEJ1dHRvbiBEcm9wZG93biB8IGBkYXRhLXVzZS1idXR0b25fZHJvcGRvd25gIHwgQm9vbGVhbiB8IFJlcXVpcmVkKiogXG4gKiBcbiAqIFRoaXMgb3B0aW9uLWZsYWcgd2lsbCBtYXJrIHRoZSBlbGVtZW50cyBpbnNpZGUgdGhlIHBhcmVudCBjb250YWluZXIsIHRoYXQgd2lsbCBiZSBjb252ZXJ0ZWQgaW50byBcbiAqIGJ1dHRvbi1kcm9wZG93biB3aWRnZXRzLlxuICogXG4gKiAqKkNvbmZpZ3VyYXRpb24gS2V5IHwgYGRhdGEtY29uZmlnX2tleWAgfCBTdHJpbmcgfCBSZXF1aXJlZCoqXG4gKiBcbiAqIFByb3ZpZGUgdGhlIGNvbmZpZ3VyYXRpb24ga2V5IGZvciB0aGUgc2luZ2xlIGJ1dHRvbi1kcm9wZG93biBpbnN0YW5jZS5cbiAqIFxuICogKipDb25maWd1cmF0aW9uIFZhbHVlIHwgYGRhdGEtY29uZmlnX2tleWAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXG4gKlxuICogUHJvdmlkZSBkaXJlY3RseSB0aGUgY29uZmlndXJhdGlvbiB2YWx1ZSBpbiBvcmRlciB0byBhdm9pZCBleHRyYSBBSkFYIHJlcXVlc3RzLlxuICogXG4gKiAqKkN1c3RvbSBDYXJldCBCdXR0b24gQ2xhc3MgfCBgZGF0YS1jdXN0b21fY2FyZXRfYnRuX2NsYXNzYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcbiAqIFxuICogQXR0YWNoIGFkZGl0aW9uYWwgY2xhc3NlcyB0byB0aGUgY2FyZXQgYnV0dG9uIGVsZW1lbnQgKHRoZSBvbmUgd2l0aCB0aGUgYXJyb3cpLiBVc2UgdGhpcyBvcHRpb24gaWYgeW91IFxuICogd2FudCB0byBhZGQgYSBjbGFzcyB0aGF0IHRoZSBwcmltYXJ5IGJ1dHRvbiBhbHJlYWR5IGhhcyBzbyB0aGF0IGJvdGggc2hhcmUgdGhlIHNhbWUgc3R5bGUgKGUuZy4gYnRuLXByaW1hcnkpLlxuICogXG4gKiAjIyMgRXhhbXBsZVxuICogYGBgaHRtbFxuICogPCEtLSBUaGlzIGVsZW1lbnQgcmVwcmVzZW50cyB0aGUgcGFyZW50IGNvbnRhaW5lci4gLS0+XG4gKiA8ZGl2XG4gKiAgIGRhdGEtZ3gtd2lkZ2V0PVwiYnV0dG9uX2Ryb3Bkb3duXCJcbiAqICAgZGF0YS1idXR0b25fZHJvcGRvd24tY29uZmlnX2tleXM9XCJvcmRlci1zaW5nbGUgb3JkZXItbXVsdGlcIlxuICogICBkYXRhLWJ1dHRvbl9kcm9wZG93bi11c2VyX2lkPVwiMlwiPlxuICogXG4gKiAgIDwhLS0gVGhpcyBlbGVtZW50IHJlcHJlc2VudHMgdGhlIGJ1dHRvbiBkcm9wZG93biB3aWRnZXQuIC0tPlxuICogICA8ZGl2XG4gKiAgICAgICBkYXRhLXVzZS1idXR0b25fZHJvcGRvd249XCJ0cnVlXCIgXG4gKiAgICAgICBkYXRhLWNvbmZpZ19rZXk9XCJvcmRlci1zaW5nbGVcIlxuICogICAgICAgZGF0YS1jdXN0b21fY2FyZXRfYnRuX2NsYXNzPVwiY2xhc3MxXCI+XG4gKiAgICAgPGJ1dHRvbj5QcmltYXJ5IEJ1dHRvbjwvYnV0dG9uPlxuICogICAgIDx1bD5cbiAqICAgICAgIDxsaT48c3Bhbj5DaGFuZ2Ugc3RhdHVzPC9zcGFuPjwvbGk+XG4gKiAgICAgICA8bGk+PHNwYW4+RGVsZXRlPC9zcGFuPjwvbGk+XG4gKiAgICAgPC91bD5cbiAqICAgPC9kaXY+XG4gKiA8L2Rpdj5cbiAqIGBgYFxuICpcbiAqICoqTm90aWNlOioqIFRoaXMgd2lkZ2V0IHdhcyBidWlsdCBmb3IgdXNhZ2UgaW4gY29tcGF0aWJpbGl0eSBtb2RlLiBUaGUgbmV3IGFkbWluIHBhZ2VzIHVzZSB0aGUgQm9vdHN0cmFwXG4gKiBidXR0b24gZHJvcGRvd24gbWFya3VwIHdoaWNoIGFscmVhZHkgZnVuY3Rpb25zIGxpa2UgdGhpcyBtb2R1bGUuIERvIG5vdCB1c2UgaXQgb24gbmV3IGFkbWluIHBhZ2VzLiBcbiAqIFxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL2J1dHRvbl9kcm9wZG93blxuICovXG5neC53aWRnZXRzLm1vZHVsZShcblx0J2J1dHRvbl9kcm9wZG93bicsXG5cdFxuXHRbJ3VzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlJ10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogV2lkZ2V0IFJlZmVyZW5jZVxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFVzZXJDb25maWd1cmF0aW9uU2VydmljZSBhbGlhcy5cblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdHVzZXJDb25maWd1cmF0aW9uU2VydmljZSA9IGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIENhcmV0IGJ1dHRvbiB0ZW1wbGF0ZS5cblx0XHRcdCAqIEB0eXBlIHtzdHJpbmd9XG5cdFx0XHQgKi9cblx0XHRcdGNhcmV0QnV0dG9uVGVtcGxhdGUgPSAnPGJ1dHRvbiBjbGFzcz1cImJ0blwiIHR5cGU9XCJidXR0b25cIj48aSBjbGFzcz1cImZhIGZhLWNhcmV0LWRvd25cIj48L2k+PC9idXR0b24+Jyxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIEZhZGUgYW5pbWF0aW9uIG9wdGlvbnMuXG5cdFx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRmYWRlOiB7XG5cdFx0XHRcdFx0ZHVyYXRpb246IDMwMCxcblx0XHRcdFx0XHRlYXNpbmc6ICdzd2luZydcblx0XHRcdFx0fSxcblx0XHRcdFx0XG5cdFx0XHRcdC8qKlxuXHRcdFx0XHQgKiBTdHJpbmcgZm9yIGRyb3Bkb3duIHNlbGVjdG9yLlxuXHRcdFx0XHQgKiBUaGlzIHNlbGVjdG9yIGlzIHVzZWQgdG8gZmluZCBhbmQgYWN0aXZhdGUgYWxsIGJ1dHRvbiBkcm9wZG93bnMuXG5cdFx0XHRcdCAqXG5cdFx0XHRcdCAqIEB0eXBlIHtzdHJpbmd9XG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRkcm9wZG93bl9zZWxlY3RvcjogJ1tkYXRhLXVzZS1idXR0b25fZHJvcGRvd25dJyxcblx0XHRcdFx0XG5cdFx0XHRcdC8qKlxuXHRcdFx0XHQgKiBBdHRyaWJ1dGUgd2hpY2ggcmVwcmVzZW50cyB0aGUgdXNlciBjb25maWd1cmF0aW9uIHZhbHVlLlxuXHRcdFx0XHQgKiBUaGUgdmFsdWUgb2YgdGhpcyBhdHRyaWJ1dGUgd2lsbCBiZSBzZXQuXG5cdFx0XHRcdCAqXG5cdFx0XHRcdCAqIEB0eXBlIHtzdHJpbmd9XG5cdFx0XHRcdCAqL1xuXHRcdFx0XHRjb25maWdfdmFsdWVfYXR0cmlidXRlOiAnZGF0YS1jb25maWd1cmF0aW9uX3ZhbHVlJ1xuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBXaWRnZXQgT3B0aW9uc1xuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRWxlbWVudCBzZWxlY3RvciBzaG9ydGN1dHMuXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRzZWxlY3RvcnMgPSB7XG5cdFx0XHRcdGVsZW1lbnQ6IG9wdGlvbnMuZHJvcGRvd25fc2VsZWN0b3IsXG5cdFx0XHRcdG1haW5CdXR0b246ICdidXR0b246bnRoLWNoaWxkKDEpJyxcblx0XHRcdFx0Y2FyZXRCdXR0b246ICdidXR0b246bnRoLWNoaWxkKDIpJ1xuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBTcGxpdCBzcGFjZS1zZXBhcmF0ZWQgZW50cmllcyB0byBhcnJheSB2YWx1ZXMuXG5cdFx0ICogQHR5cGUge2FycmF5fVxuXHRcdCAqL1xuXHRcdG9wdGlvbnMuY29uZmlnX2tleXMgPSBvcHRpb25zLmNvbmZpZ19rZXlzID8gb3B0aW9ucy5jb25maWdfa2V5cy5zcGxpdCgnICcpIDogW107XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTIC0gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBMb2FkcyB0aGUgdXNlciBjb25maWd1cmF0aW9uIHZhbHVlcyBmb3IgZWFjaCBwcm92aWRlZCBrZXkuXG5cdFx0ICogUmV0dXJucyBhIERlZmVycmVkIG9iamVjdCB3aXRoIGFuIG9iamVjdCB3aXRoIGNvbmZpZ3VyYXRpb25cblx0XHQgKiBhcyBrZXkgYW5kIHJlc3BlY3RpdmUgdmFsdWVzIG9yIG51bGwgaWYgbm8gcmVxdWVzdCBjb25kaXRpb25zIGFyZSBzZXQuXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7alF1ZXJ5LkRlZmVycmVkfVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9sb2FkQ29uZmlndXJhdGlvbnMgPSBmdW5jdGlvbigpIHtcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNYWluIGRlZmVycmVkIG9iamVjdCB3aGljaCB3aWxsIGJlIHJldHVybmVkLlxuXHRcdFx0ICogQHR5cGUge2pRdWVyeS5EZWZlcnJlZH1cblx0XHRcdCAqL1xuXHRcdFx0dmFyIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFRoaXMgYXJyYXkgd2lsbCBjb250YWluIGFsbCBkZWZlcnJlZCBhamF4IHJlcXVlc3QgdG8gdGhlIHVzZXIgY29uZmlndXJhdGlvbiBzZXJ2aWNlLlxuXHRcdFx0ICogQGV4YW1wbGVcblx0XHRcdCAqICAgICAgW0RlZmVycmVkLCBEZWZlcnJlZF1cblx0XHRcdCAqIEB0eXBlIHthcnJheX1cblx0XHRcdCAqL1xuXHRcdFx0dmFyIGNvbmZpZ0RlZmVycmVkcyA9IFtdO1xuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFVzZXIgY29uZmlndXJhdGlvbiBrZXkgYW5kIHZhbHVlcyBzdG9yYWdlLlxuXHRcdFx0ICogQGV4YW1wbGVcblx0XHRcdCAqICAgICAge1xuXHRcdFx0ICogICAgICAgICAgY29uZmlnS2V5OiAnY29uZmlnVmFsdWUnXG5cdFx0XHQgKiAgICAgIH1cblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdHZhciBjb25maWdWYWx1ZXMgPSB7fTtcblx0XHRcdFxuXHRcdFx0Ly8gUmV0dXJuIGltbWVkaWF0ZWx5IGlmIHRoZSB1c2VyIGNvbmZpZ3VyYXRpb24gc2VydmljZSBpcyBub3QgbmVlZGVkLlxuXHRcdFx0aWYgKCFvcHRpb25zLnVzZXJfaWQgfHwgIW9wdGlvbnMuY29uZmlnX2tleXMubGVuZ3RoKSB7XG5cdFx0XHRcdHJldHVybiBkZWZlcnJlZC5yZXNvbHZlKG51bGwpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBJdGVyYXRlIG92ZXIgZWFjaCBjb25maWd1cmF0aW9uIHZhbHVlIHByb3ZpZGVkIGluIHRoZSBlbGVtZW50XG5cdFx0XHQkLmVhY2gob3B0aW9ucy5jb25maWdfa2V5cywgZnVuY3Rpb24oaW5kZXgsIGNvbmZpZ0tleSkge1xuXHRcdFx0XHQvLyBDcmVhdGUgZGVmZXJyZWQgb2JqZWN0IGZvciBjb25maWd1cmF0aW9uIHZhbHVlIGZldGNoLlxuXHRcdFx0XHR2YXIgY29uZmlnRGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBGZXRjaCBjb25maWd1cmF0aW9uIHZhbHVlIGZyb20gc2VydmljZS5cblx0XHRcdFx0Ly8gQWRkcyB0aGUgZmV0Y2hlZCB2YWx1ZSB0byB0aGUgYGNvbmZpZ1ZhbHVlc2Agb2JqZWN0IGFuZCByZXNvbHZlcyB0aGUgcHJvbWlzZS5cblx0XHRcdFx0dXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlLmdldCh7XG5cdFx0XHRcdFx0ZGF0YToge1xuXHRcdFx0XHRcdFx0dXNlcklkOiBvcHRpb25zLnVzZXJfaWQsXG5cdFx0XHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiBjb25maWdLZXlcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdG9uU3VjY2VzczogZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHRcdGNvbmZpZ1ZhbHVlc1tjb25maWdLZXldID0gcmVzcG9uc2UuY29uZmlndXJhdGlvblZhbHVlO1xuXHRcdFx0XHRcdFx0Y29uZmlnRGVmZXJyZWQucmVzb2x2ZSgpO1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0b25FcnJvcjogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRjb25maWdEZWZlcnJlZC5yZXNvbHZlKCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblx0XHRcdFx0XG5cdFx0XHRcdGNvbmZpZ0RlZmVycmVkcy5wdXNoKGNvbmZpZ0RlZmVycmVkKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBJZiBhbGwgcmVxdWVzdHMgZm9yIHRoZSBjb25maWd1cmF0aW9uIHZhbHVlcyBoYXMgYmVlbiBwcm9jZXNzZWRcblx0XHRcdC8vIHRoZW4gdGhlIG1haW4gcHJvbWlzZSB3aWxsIGJlIHJlc29sdmVkIHdpdGggYWxsIGNvbmZpZ3VyYXRpb24gdmFsdWVzIGFzIGdpdmVuIHBhcmFtZXRlci5cblx0XHRcdCQud2hlbi5hcHBseShudWxsLCBjb25maWdEZWZlcnJlZHMpLmRvbmUoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGRlZmVycmVkLnJlc29sdmUoY29uZmlnVmFsdWVzKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBSZXR1cm4gZGVmZXJyZWQgb2JqZWN0LlxuXHRcdFx0cmV0dXJuIGRlZmVycmVkO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRmluZHMgYWxsIGRyb3Bkb3duIGVsZW1lbnRzLlxuXHRcdCAqIFJldHVybnMgYSBkZWZlcnJlZCBvYmplY3Qgd2l0aCBhbiBlbGVtZW50IGxpc3Qgb2JqZWN0LlxuXHRcdCAqIFRoaXMgZnVuY3Rpb24gaGlkZXMgdGhlIGRyb3Bkb3duIGVsZW1lbnRzLlxuXHRcdCAqXG5cdFx0ICogQHJldHVybiB7alF1ZXJ5LkRlZmVycmVkfVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9maW5kRHJvcGRvd25FbGVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZlcnJlZCBvYmplY3Qgd2hpY2ggd2lsbCBiZSByZXR1cm5lZC5cblx0XHRcdCAqIEB0eXBlIHtqUXVlcnkuRGVmZXJyZWR9XG5cdFx0XHQgKi9cblx0XHRcdHZhciBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBFbGVtZW50cyB3aXRoIGVsZW1lbnQgYW5kIGRhdGEgYXR0cmlidXRlIGluZm9ybWF0aW9ucy5cblx0XHRcdCAqIEBleGFtcGxlXG5cdFx0XHQgKiAgICAgIFt7XG5cdFx0XHQgKiAgICAgICAgICBlbGVtZW50OiA8ZGl2Pixcblx0XHRcdCAqICAgICAgICAgIGN1c3RvbV9jYXJldF9idG5fY2xhc3M6ICdidG4tcHJpbWFyeSdcblx0XHRcdCAqICAgICAgICAgIGNvbmZpZ0tleTogJ29yZGVyTXVsdGlTZWxlY3QnXG5cdFx0XHQgKiAgICAgIH1dXG5cdFx0XHQgKiBAdHlwZSB7YXJyYXl9XG5cdFx0XHQgKi9cblx0XHRcdHZhciBlbGVtZW50cyA9IFtdO1xuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEFycmF5IG9mIGRhdGEgYXR0cmlidXRlcyBmb3IgdGhlIGRyb3Bkb3duIGVsZW1lbnRzIHdoaWNoIHdpbGwgYmUgY2hlY2tlZC5cblx0XHRcdCAqIEB0eXBlIHthcnJheX1cblx0XHRcdCAqL1xuXHRcdFx0dmFyIGRhdGFBdHRyaWJ1dGVzID0gWydjdXN0b21fY2FyZXRfYnRuX2NsYXNzJywgJ2NvbmZpZ19rZXknLCAnY29uZmlnX3ZhbHVlJ107XG5cdFx0XHRcblx0XHRcdC8vIEZpbmQgZHJvcGRvd24gZWxlbWVudHMgd2hlbiBET00gaXMgcmVhZHlcblx0XHRcdC8vIGFuZCByZXNvbHZlIHByb21pc2UgcGFzc2luZyBmb3VuZCBlbGVtZW50cyBhcyBwYXJhbWV0ZXIuXG5cdFx0XHQkKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpIHtcblx0XHRcdFx0JHRoaXMuZmluZChvcHRpb25zLmRyb3Bkb3duX3NlbGVjdG9yKS5lYWNoKGZ1bmN0aW9uKGluZGV4LCBlbGVtZW50KSB7XG5cdFx0XHRcdFx0LyoqXG5cdFx0XHRcdFx0ICogalF1ZXJ5IHdyYXBwZWQgZWxlbWVudCBzaG9ydGN1dC5cblx0XHRcdFx0XHQgKiBAdHlwZSB7alF1ZXJ5fVxuXHRcdFx0XHRcdCAqL1xuXHRcdFx0XHRcdHZhciAkZWxlbWVudCA9ICQoZWxlbWVudCk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0LyoqXG5cdFx0XHRcdFx0ICogRWxlbWVudCBpbmZvIG9iamVjdC5cblx0XHRcdFx0XHQgKiBXaWxsIGJlIHB1c2hlZCB0byBgZWxlbWVudHNgIGFycmF5LlxuXHRcdFx0XHRcdCAqIEBleGFtcGxlXG5cdFx0XHRcdFx0ICogICAgICB7XG5cdFx0XHRcdFx0ICogICAgICAgICAgZWxlbWVudDogPGRpdj4sXG5cdFx0XHRcdFx0ICogICAgICAgICAgY3VzdG9tX2NhcmV0X2J0bl9jbGFzczogJ2J0bi1wcmltYXJ5J1xuXHRcdFx0XHRcdCAqICAgICAgICAgIGNvbmZpZ0tleTogJ29yZGVyTXVsdGlTZWxlY3QnXG5cdFx0XHRcdFx0ICogICAgICB9XG5cdFx0XHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdFx0XHQgKi9cblx0XHRcdFx0XHR2YXIgZWxlbWVudE9iamVjdCA9IHt9O1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEFkZCBlbGVtZW50IHRvIGVsZW1lbnQgaW5mbyBvYmplY3QuXG5cdFx0XHRcdFx0ZWxlbWVudE9iamVjdC5lbGVtZW50ID0gZWxlbWVudDtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBJdGVyYXRlIG92ZXIgZWFjaCBkYXRhIGF0dHJpYnV0ZSBrZXkgYW5kIGNoZWNrIGZvciBkYXRhIGF0dHJpYnV0ZSBleGlzdGVuY2UuXG5cdFx0XHRcdFx0Ly8gSWYgZGF0YS1hdHRyaWJ1dGUgZXhpc3RzLCB0aGUga2V5IGFuZCB2YWx1ZSB3aWxsIGJlIGFkZGVkIHRvIGVsZW1lbnQgaW5mbyBvYmplY3QuXG5cdFx0XHRcdFx0JC5lYWNoKGRhdGFBdHRyaWJ1dGVzLCBmdW5jdGlvbihpbmRleCwgYXR0cmlidXRlKSB7XG5cdFx0XHRcdFx0XHRpZiAoYXR0cmlidXRlIGluICRlbGVtZW50LmRhdGEoKSkge1xuXHRcdFx0XHRcdFx0XHRlbGVtZW50T2JqZWN0W2F0dHJpYnV0ZV0gPSAkZWxlbWVudC5kYXRhKGF0dHJpYnV0ZSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gUHVzaCB0aGlzIGVsZW1lbnQgaW5mbyBvYmplY3QgdG8gYGVsZW1lbnRzYCBhcnJheS5cblx0XHRcdFx0XHRlbGVtZW50cy5wdXNoKGVsZW1lbnRPYmplY3QpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEhpZGUgZWxlbWVudFxuXHRcdFx0XHRcdCRlbGVtZW50LmhpZGUoKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBSZXNvbHZlIHRoZSBwcm9taXNlIHBhc3NpbmcgaW4gdGhlIGVsZW1lbnRzIGFzIGFyZ3VtZW50LlxuXHRcdFx0XHRkZWZlcnJlZC5yZXNvbHZlKGVsZW1lbnRzKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBSZXR1cm4gZGVmZXJyZWQgb2JqZWN0LlxuXHRcdFx0cmV0dXJuIGRlZmVycmVkO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTIC0gRFJPUERPV04gVE9HR0xFXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogU2hvd3MgZHJvcGRvd24gYWN0aW9uIGxpc3QuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50IERyb3Bkb3duIGFjdGlvbiBsaXN0IGVsZW1lbnQuXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3Nob3dEcm9wZG93biA9IGZ1bmN0aW9uKGVsZW1lbnQpIHtcblx0XHRcdC8vIFBlcmZvcm0gZmFkZSBpbi5cblx0XHRcdCQoZWxlbWVudClcblx0XHRcdFx0LnN0b3AoKVxuXHRcdFx0XHQuYWRkQ2xhc3MoJ2hvdmVyJylcblx0XHRcdFx0LmZhZGVJbihvcHRpb25zLmZhZGUpO1xuXHRcdFx0XG5cdFx0XHQvLyBGaXggcG9zaXRpb24uXG5cdFx0XHRfcmVwb3NpdGlvbkRyb3Bkb3duKGVsZW1lbnQpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSGlkZXMgZHJvcGRvd24gYWN0aW9uIGxpc3QuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50IERyb3Bkb3duIGFjdGlvbiBsaXN0IGVsZW1lbnQuXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2hpZGVEcm9wZG93biA9IGZ1bmN0aW9uKGVsZW1lbnQpIHtcblx0XHRcdC8vIFBlcmZvcm0gZmFkZSBvdXQuXG5cdFx0XHQkKGVsZW1lbnQpXG5cdFx0XHRcdC5zdG9wKClcblx0XHRcdFx0LnJlbW92ZUNsYXNzKCdob3ZlcicpXG5cdFx0XHRcdC5mYWRlT3V0KG9wdGlvbnMuZmFkZSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBGaXhlcyB0aGUgZHJvcGRvd24gYWN0aW9uIGxpc3QgdG8gZW5zdXJlIHRoYXQgdGhlIGFjdGlvbiBsaXN0IGlzIGFsd2F5cyB2aXNpYmxlLlxuXHRcdCAqXG5cdFx0ICogU29tZXRpbWVzIHdoZW4gdGhlIGJ1dHRvbiBkcm9wZG93biB3aWRnZXQgaXMgbmVhciB0aGUgd2luZG93IGJvcmRlcnMgdGhlIGxpc3QgbWlnaHRcblx0XHQgKiBub3QgYmUgdmlzaWJsZS4gVGhpcyBmdW5jdGlvbiB3aWxsIGNoYW5nZSBpdHMgcG9zaXRpb24gaW4gb3JkZXIgdG8gYWx3YXlzIGJlIHZpc2libGUuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge0hUTUxFbGVtZW50fSBlbGVtZW50IERyb3Bkb3duIGFjdGlvbiBsaXN0IGVsZW1lbnQuXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3JlcG9zaXRpb25Ecm9wZG93biA9IGZ1bmN0aW9uKGVsZW1lbnQpIHtcblx0XHRcdC8vIFdyYXAgZWxlbWVudCBpbiBqUXVlcnkgYW5kIHNhdmUgc2hvcnRjdXQgdG8gZHJvcGRvd24gYWN0aW9uIGxpc3QgZWxlbWVudC5cblx0XHRcdHZhciAkbGlzdCA9ICQoZWxlbWVudCk7XG5cdFx0XHRcblx0XHRcdC8vIFJlZmVyZW5jZSB0byBidXR0b24gZWxlbWVudC5cblx0XHRcdHZhciAkYnV0dG9uID0gJGxpc3QuY2xvc2VzdChvcHRpb25zLmRyb3Bkb3duX3NlbGVjdG9yKTtcblx0XHRcdFxuXHRcdFx0Ly8gUmVzZXQgYW55IHBvc3NpYmxlIENTUyBwb3NpdGlvbiBtb2RpZmljYXRpb25zLlxuXHRcdFx0JGxpc3QuY3NzKHtsZWZ0OiAnJywgdG9wOiAnJ30pO1xuXHRcdFx0XG5cdFx0XHQvLyBDaGVjayBkcm9wZG93biBwb3NpdGlvbiBhbmQgcGVyZm9ybSByZXBvc2l0aW9uIGlmIG5lZWRlZC5cblx0XHRcdGlmICgkbGlzdC5vZmZzZXQoKS5sZWZ0ICsgJGxpc3Qud2lkdGgoKSA+IHdpbmRvdy5pbm5lcldpZHRoKSB7XG5cdFx0XHRcdHZhciB0b01vdmVMZWZ0UGl4ZWxzID0gJGxpc3Qud2lkdGgoKSAtICRidXR0b24ud2lkdGgoKTtcblx0XHRcdFx0JGxpc3QuY3NzKCdtYXJnaW4tbGVmdCcsICctJyArICh0b01vdmVMZWZ0UGl4ZWxzKSArICdweCcpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAoJGxpc3Qub2Zmc2V0KCkudG9wICsgJGxpc3QuaGVpZ2h0KCkgPiB3aW5kb3cuaW5uZXJIZWlnaHQpIHtcblx0XHRcdFx0dmFyIHRvTW92ZVVwUGl4ZWxzID0gJGxpc3QuaGVpZ2h0KCkgKyAxMDsgLy8gMTBweCBmaW5lLXR1bmluZ1xuXHRcdFx0XHQkbGlzdC5jc3MoJ21hcmdpbi10b3AnLCAnLScgKyAodG9Nb3ZlVXBQaXhlbHMpICsgJ3B4Jyk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBQUklWQVRFIE1FVEhPRFMgLSBFVkVOVCBIQU5ETEVSU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXMgY2xpY2sgZXZlbnRzIG9uIHRoZSBtYWluIGJ1dHRvbiAoYWN0aW9uIGJ1dHRvbikuXG5cdFx0ICogUGVyZm9ybXMgbWFpbiBidXR0b24gYWN0aW9uLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX21haW5CdXR0b25DbGlja0hhbmRsZXIgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XG5cdFx0XHQkKHRoaXMpLnRyaWdnZXIoJ3BlcmZvcm06YWN0aW9uJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBIYW5kbGVzIGNsaWNrIGV2ZW50cyBvbiB0aGUgZHJvcGRvd24gYnV0dG9uIChjYXJldCBidXR0b24pLlxuXHRcdCAqIFNob3dzIG9yIGhpZGVzIHRoZSBkcm9wZG93biBhY3Rpb24gbGlzdC5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jYXJldEJ1dHRvbkNsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogU2hvcnRjdXQgcmVmZXJlbmNlIHRvIGRyb3Bkb3duIGFjdGlvbiBsaXN0IGVsZW1lbnQuXG5cdFx0XHQgKiBAdHlwZSB7alF1ZXJ5fVxuXHRcdFx0ICovXG5cdFx0XHR2YXIgJGxpc3QgPSAkKHRoaXMpLnNpYmxpbmdzKCd1bCcpO1xuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERldGVybWluZXMgd2hldGhlciB0aGUgZHJvcGRvd24gYWN0aW9uIGxpc3QgaXMgdmlzaWJsZS5cblx0XHRcdCAqIEB0eXBlIHtib29sZWFufVxuXHRcdFx0ICovXG5cdFx0XHR2YXIgbGlzdElzVmlzaWJsZSA9ICRsaXN0Lmhhc0NsYXNzKCdob3ZlcicpO1xuXHRcdFx0XG5cdFx0XHQvLyBIaWRlIG9yIHNob3cgZHJvcGRvd24sIGRlcGVuZGVudCBvbiBpdHMgdmlzaWJpbGl0eSBzdGF0ZS5cblx0XHRcdGlmIChsaXN0SXNWaXNpYmxlKSB7XG5cdFx0XHRcdF9oaWRlRHJvcGRvd24oJGxpc3QpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0X3Nob3dEcm9wZG93bigkbGlzdCk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBIYW5kbGVzIGNsaWNrIGV2ZW50cyBvbiB0aGUgZHJvcGRvd24gYWN0aW9uIGxpc3QuXG5cdFx0ICogSGlkZXMgdGhlIGRyb3Bkb3duLCBzYXZlcyB0aGUgY2hvc2VuIHZhbHVlIHRocm91Z2hcblx0XHQgKiB0aGUgdXNlciBjb25maWd1cmF0aW9uIHNlcnZpY2UgYW5kIHBlcmZvcm0gdGhlIHNlbGVjdGVkIGFjdGlvbi5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9saXN0SXRlbUNsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogUmVmZXJlbmNlIHRvIGB0aGlzYCBlbGVtZW50LCB3cmFwcGVkIGluIGpRdWVyeS5cblx0XHRcdCAqIEB0eXBlIHtqUXVlcnl9XG5cdFx0XHQgKi9cblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyk7XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogUmVmZXJlbmNlIHRvIGRyb3Bkb3duIGFjdGlvbiBsaXN0IGVsZW1lbnQuXG5cdFx0XHQgKiBAdHlwZSB7alF1ZXJ5fVxuXHRcdFx0ICovXG5cdFx0XHR2YXIgJGxpc3QgPSAkc2VsZi5jbG9zZXN0KCd1bCcpO1xuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFJlZmVyZW5jZSB0byBidXR0b24gZHJvcGRvd24gZWxlbWVudC5cblx0XHRcdCAqIEB0eXBlIHtqUXVlcnl9XG5cdFx0XHQgKi9cblx0XHRcdHZhciAkYnV0dG9uID0gJHNlbGYuY2xvc2VzdChvcHRpb25zLmRyb3Bkb3duX3NlbGVjdG9yKTtcblx0XHRcdFxuXHRcdFx0Ly8gSGlkZSBkcm9wZG93bi5cblx0XHRcdF9oaWRlRHJvcGRvd24oJGxpc3QpO1xuXHRcdFx0XG5cdFx0XHQvLyBTYXZlIHVzZXIgY29uZmlndXJhdGlvbiBkYXRhLlxuXHRcdFx0dmFyIGNvbmZpZ0tleSA9ICRidXR0b24uZGF0YSgnY29uZmlnX2tleScpLFxuXHRcdFx0XHRjb25maWdWYWx1ZSA9ICRzZWxmLmRhdGEoJ3ZhbHVlJyk7XG5cdFx0XHRcblx0XHRcdGlmIChjb25maWdLZXkgJiYgY29uZmlnVmFsdWUpIHtcblx0XHRcdFx0X3NhdmVVc2VyQ29uZmlndXJhdGlvbihjb25maWdLZXksIGNvbmZpZ1ZhbHVlKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gUGVyZm9ybSBhY3Rpb24uXG5cdFx0XHQkc2VsZi50cmlnZ2VyKCdwZXJmb3JtOmFjdGlvbicpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSGFuZGxlcyBjbGljayBldmVudHMgb3V0c2lkZSBvZiB0aGUgYnV0dG9uIGFyZWEuXG5cdFx0ICogSGlkZXMgbXVsdGlwbGUgb3BlbmVkIGRyb3Bkb3ducy5cblx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfb3V0c2lkZUNsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHQvKipcblx0XHRcdCAqIEVsZW1lbnQgc2hvcnRjdXQgdG8gYWxsIG9wZW5lZCBkcm9wZG93biBhY3Rpb24gbGlzdHMuXG5cdFx0XHQgKiBAdHlwZSB7alF1ZXJ5fVxuXHRcdFx0ICovXG5cdFx0XHR2YXIgJGxpc3QgPSAkKCd1bC5ob3ZlcicpO1xuXHRcdFx0XG5cdFx0XHQvLyBIaWRlIGFsbCBvcGVuZWQgZHJvcGRvd25zLlxuXHRcdFx0X2hpZGVEcm9wZG93bigkbGlzdCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBQUklWQVRFIE1FVEhPRFMgLSBDUkVBVEUgV0lER0VUU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEFkZHMgdGhlIGRyb3Bkb3duIGZ1bmN0aW9uYWxpdHkgdG8gdGhlIGJ1dHRvbnMuXG5cdFx0ICpcblx0XHQgKiBEZXZlbG9wZXJzIGNhbiBtYW51YWxseSBhZGQgbmV3IGA8bGk+YCBpdGVtcyB0byB0aGUgbGlzdCBpbiBvcmRlciB0byBkaXNwbGF5IG1vcmUgb3B0aW9uc1xuXHRcdCAqIHRvIHRoZSB1c2Vycy5cblx0XHQgKlxuXHRcdCAqIFRoaXMgZnVuY3Rpb24gZmFkZXMgdGhlIGRyb3Bkb3duIGVsZW1lbnRzIGluLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHthcnJheX0gZWxlbWVudHMgTGlzdCBvZiBlbGVtZW50cyBpbmZvcyBvYmplY3Qgd2hpY2ggY29udGFpbnMgdGhlIGVsZW1lbnQgaXRzZWxmIGFuZCBkYXRhIGF0dHJpYnV0ZXMuXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGNvbmZpZ3VyYXRpb24gT2JqZWN0IHdpdGggZmV0Y2hlZCBjb25maWd1cmF0aW9uIGtleSBhbmQgdmFsdWVzLlxuXHRcdCAqXG5cdFx0ICogQHJldHVybiB7alF1ZXJ5LkRlZmVycmVkfVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9tYWtlV2lkZ2V0cyA9IGZ1bmN0aW9uKGVsZW1lbnRzLCBjb25maWd1cmF0aW9uKSB7XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmZXJyZWQgb2JqZWN0IHdoaWNoIHdpbGwgYmUgcmV0dXJuZWQuXG5cdFx0XHQgKiBAdHlwZSB7alF1ZXJ5LkRlZmVycmVkfVxuXHRcdFx0ICovXG5cdFx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVGhlIHNlY29uZGFyeSBidXR0b24gd2hpY2ggd2lsbCB0b2dnbGUgdGhlIGxpc3QgdmlzaWJpbGl0eS5cblx0XHRcdCAqIEB0eXBlIHtqUXVlcnl9XG5cdFx0XHQgKi9cblx0XHRcdHZhciAkc2Vjb25kYXJ5QnV0dG9uID0gJChjYXJldEJ1dHRvblRlbXBsYXRlKTtcblx0XHRcdFxuXHRcdFx0Ly8gSXRlcmF0ZSBvdmVyIGVhY2ggZWxlbWVudCBhbmQgY3JlYXRlIGRyb3Bkb3duIHdpZGdldC5cblx0XHRcdCQuZWFjaChlbGVtZW50cywgZnVuY3Rpb24oaW5kZXgsIGVsZW1lbnRPYmplY3QpIHtcblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIEJ1dHRvbiBkcm9wZG93biBlbGVtZW50LlxuXHRcdFx0XHQgKiBAdHlwZSB7alF1ZXJ5fVxuXHRcdFx0XHQgKi9cblx0XHRcdFx0dmFyICRlbGVtZW50ID0gJChlbGVtZW50T2JqZWN0LmVsZW1lbnQpO1xuXHRcdFx0XHRcblx0XHRcdFx0LyoqXG5cdFx0XHRcdCAqIEJ1dHRvbiBkcm9wZG93biBlbGVtZW50J3MgYnV0dG9ucy5cblx0XHRcdFx0ICogQHR5cGUge2pRdWVyeX1cblx0XHRcdFx0ICovXG5cdFx0XHRcdHZhciAkYnV0dG9uID0gJGVsZW1lbnQuZmluZCgnYnV0dG9uOmZpcnN0Jyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvKipcblx0XHRcdFx0ICogQ2xvbmVkIGNhcmV0IGJ1dHRvbiB0ZW1wbGF0ZS5cblx0XHRcdFx0ICogQHR5cGUge2pRdWVyeX1cblx0XHRcdFx0ICovXG5cdFx0XHRcdHZhciAkY2FyZXRCdXR0b24gPSAkc2Vjb25kYXJ5QnV0dG9uLmNsb25lKCk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBBZGQgY3VzdG9tIGNsYXNzIHRvIHRlbXBsYXRlLCBpZiBkZWZpbmVkLlxuXHRcdFx0XHRpZiAoZWxlbWVudE9iamVjdC5jdXN0b21fY2FyZXRfYnRuX2NsYXNzKSB7XG5cdFx0XHRcdFx0JGNhcmV0QnV0dG9uLmFkZENsYXNzKGVsZW1lbnRPYmplY3QuY3VzdG9tX2NhcmV0X2J0bl9jbGFzcyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdC8vIEFkZCBDU1MgY2xhc3MgdG8gYnV0dG9uIGFuZCBwbGFjZSB0aGUgY2FyZXQgYnV0dG9uLlxuXHRcdFx0XHQkYnV0dG9uXG5cdFx0XHRcdFx0LmFkZENsYXNzKCdidG4nKVxuXHRcdFx0XHRcdC5hZnRlcigkY2FyZXRCdXR0b24pO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gQWRkIGNsYXNzIHRvIGRyb3Bkb3duIGJ1dHRvbiBlbGVtZW50LlxuXHRcdFx0XHQkZWxlbWVudFxuXHRcdFx0XHRcdC5hZGRDbGFzcygnanMtYnV0dG9uLWRyb3Bkb3duJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBBZGQgY29uZmlndXJhdGlvbiB2YWx1ZSB0byBjb250YWluZXIsIGlmIGtleSBhbmQgdmFsdWUgZXhpc3QuXG5cdFx0XHRcdGlmIChjb25maWd1cmF0aW9uICYmIGVsZW1lbnRPYmplY3QuY29uZmlnX2tleSAmJiBjb25maWd1cmF0aW9uW2VsZW1lbnRPYmplY3QuY29uZmlnX2tleV0gXG5cdFx0XHRcdFx0fHwgZWxlbWVudE9iamVjdC5jb25maWdfdmFsdWUpIHtcblx0XHRcdFx0XHR2YXIgdmFsdWUgPSBlbGVtZW50T2JqZWN0LmNvbmZpZ192YWx1ZSB8fCBjb25maWd1cmF0aW9uW2VsZW1lbnRPYmplY3QuY29uZmlnX2tleV07IFxuXHRcdFx0XHRcdCRlbGVtZW50LmF0dHIob3B0aW9ucy5jb25maWdfdmFsdWVfYXR0cmlidXRlLCB2YWx1ZSk7XG5cdFx0XHRcdH0gXG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBBdHRhY2ggZXZlbnQgaGFuZGxlcjogQ2xpY2sgb24gZmlyc3QgYnV0dG9uIChtYWluIGFjdGlvbiBidXR0b24pLlxuXHRcdFx0XHQkZWxlbWVudC5vbignY2xpY2snLCBzZWxlY3RvcnMubWFpbkJ1dHRvbiwgX21haW5CdXR0b25DbGlja0hhbmRsZXIpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gQXR0YWNoIGV2ZW50IGhhbmRsZXI6IENsaWNrIG9uIGRyb3Bkb3duIGJ1dHRvbiAoY2FyZXQgYnV0dG9uKS5cblx0XHRcdFx0JGVsZW1lbnQub24oJ2NsaWNrJywgc2VsZWN0b3JzLmNhcmV0QnV0dG9uLCBfY2FyZXRCdXR0b25DbGlja0hhbmRsZXIpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gQXR0YWNoIGV2ZW50IGhhbmRsZXI6IENsaWNrIG9uIGRyb3Bkb3duIGFjdGlvbiBsaXN0IGl0ZW0uXG5cdFx0XHRcdCRlbGVtZW50Lm9uKCdjbGljaycsICd1bCBzcGFuLCB1bCBhJywgX2xpc3RJdGVtQ2xpY2tIYW5kbGVyKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIEZhZGUgaW4gZWxlbWVudC5cblx0XHRcdFx0JGVsZW1lbnQuZmFkZUluKG9wdGlvbnMuZmFkZS5kdXJhdGlvbiwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JGVsZW1lbnQuY3NzKCdkaXNwbGF5JywgJycpO1xuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBBdHRhY2ggZXZlbnQgaGFuZGxlcjogQ2xvc2UgZHJvcGRvd24gb24gb3V0c2lkZSBjbGljay5cblx0XHRcdCQoZG9jdW1lbnQpLm9uKCdjbGljaycsIF9vdXRzaWRlQ2xpY2tIYW5kbGVyKTtcblx0XHRcdFxuXHRcdFx0Ly8gUmVzb2x2ZSBwcm9taXNlLlxuXHRcdFx0ZGVmZXJyZWQucmVzb2x2ZSgpO1xuXHRcdFx0XG5cdFx0XHQvLyBSZXR1cm4gZGVmZXJyZWQgb2JqZWN0LlxuXHRcdFx0cmV0dXJuIGRlZmVycmVkO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTIC0gU0FWRSBVU0VSIENPTkZJR1VSQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBTYXZlcyBhIHVzZXIgY29uZmlndXJhdGlvbiB2YWx1ZS5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBrZXkgQ29uZmlndXJhdGlvbiBrZXkuXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHZhbHVlIENvbmZpZ3VyYXRpb24gdmFsdWUuXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3NhdmVVc2VyQ29uZmlndXJhdGlvbiA9IGZ1bmN0aW9uKGtleSwgdmFsdWUpIHtcblx0XHRcdC8vIFRocm93IGVycm9yIGlmIG5vIGNvbXBsZXRlIGRhdGEgaGFzIGJlZW4gcHJvdmlkZWQuXG5cdFx0XHRpZiAoIWtleSB8fCAhdmFsdWUpIHtcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdObyBjb25maWd1cmF0aW9uIGRhdGEgcGFzc2VkJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFNhdmUgdmFsdWUgdG8gZGF0YWJhc2UgdmlhIHVzZXIgY29uZmlndXJhdGlvbiBzZXJ2aWNlLlxuXHRcdFx0dXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlLnNldCh7XG5cdFx0XHRcdGRhdGE6IHtcblx0XHRcdFx0XHR1c2VySWQ6IG9wdGlvbnMudXNlcl9pZCxcblx0XHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiBrZXksXG5cdFx0XHRcdFx0Y29uZmlndXJhdGlvblZhbHVlOiB2YWx1ZVxuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIG1vZHVsZSwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkLndoZW4oX2ZpbmREcm9wZG93bkVsZW1lbnRzKCksIF9sb2FkQ29uZmlndXJhdGlvbnMoKSlcblx0XHRcdFx0LnRoZW4oX21ha2VXaWRnZXRzKVxuXHRcdFx0XHQudGhlbihkb25lKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
