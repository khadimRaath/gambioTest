'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

/* --------------------------------------------------------------
 search.js 2016-04-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Global Search Controller Module
 */
gx.controllers.module('search', ['user_configuration_service'], function (data) {

	'use strict';

	// --------------------------------------------------------------------
	// VARIABLES
	// --------------------------------------------------------------------

	/**
  * Module Instance
  *
  * @type {Object}
  */

	var _this3 = this;

	var module = {
		bindings: {
			input: $(this).find('.search-input')
		}
	};

	// --------------------------------------------------------------------
	// FUNCTIONS
	// --------------------------------------------------------------------

	var SearchController = function () {
		/**
   * Creates a new search controller.
   *
   * @param {Number} customerId Customer ID.
   * @param {jQuery} $element Search bar controller element.
   * @param {Object} UserCfgService User configuration library.
   * @param {Object} Binding Data binding object.
   * @param {Function} done JS-Engine finish callback function.
   */
		function SearchController(customerId, $element, UserCfgService, Binding, done) {
			_classCallCheck(this, SearchController);

			// Set element properties.
			this.$searchBar = $element;
			this.$input = this.$searchBar.find('.search-input');
			this.$list = this.$searchBar.find('.search-list');
			this.$listItems = this.$list.find('.search-list-item');
			this.$button = $('.actions .search');

			// List item search term placeholder selector string.
			this.listItemSearchTermPlaceholderSelector = '.search-term-placeholder';

			// Set default entity.
			this.DEFAULT_ENTITY = 'orders';

			// Two-way data binding object.
			this.binding = Binding;

			// Set user configuration service.
			this.userCfgService = UserCfgService;

			// Current search entity.
			this.entity = null;

			// Call JS-Engine module finish callback.
			done();
		}

		/**
   * Attaches the event listeners to the widget elements and sets the search entity.
   *
   * @return {SearchController} Same instance for method chaining.
   *
   * @public
   */


		_createClass(SearchController, [{
			key: 'initialize',
			value: function initialize() {
				var _this = this;

				// Bind input events.
				this.$input.on('click', function (event) {
					return _this._onInputClick(event);
				}).on('keyup', function (event) {
					return _this._onInputKeyUp(event);
				});

				// Bind window events.
				$(window).on('click', function (event) {
					return _this._onOutsideClick(event);
				}).on('blur', function (event) {
					return _this._onWindowBlur(event);
				});

				// Bind list item events.
				this.$listItems.on('click', function (event) {
					return _this._onListClick(event);
				});

				// Bind button event.
				this.$button.on('click', function (event) {
					return _this._onButtonClick(event);
				});

				// Set search entity.
				this._setEntity(data.recentSearchArea || this.DEFAULT_ENTITY);

				return this;
			}

			/**
    * Saves the entity as user configuration value.
    *
    * @param {String} value Configuration value.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_setUserCfgValue',
			value: function _setUserCfgValue(value) {
				// User configuration service request options.
				var data = $.extend(true, {}, this.userCfgData, { configurationValue: value });

				// Save configuration value to server.
				this.userCfgService.set({ data: data });

				return this;
			}

			/**
    * Sets the search entity and activates the respective list item element.
    *
    * @param {String} entity Search entity name.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_setEntity',
			value: function _setEntity(entity) {
				// Set internal entity value.
				this.entity = entity;

				// Select item in search entity list.
				this.$list.find('li[data-search-entity="' + entity + '"]').addClass('active');

				return this;
			}

			/**
    * Handles event for the click action on the input field.
    *
    * @param {jQuery.Event} event Fired event.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onInputClick',
			value: function _onInputClick(event) {
				this._togglePlaceholder(true)._fillTermInListItems()._toggleDropdown(true);

				return this;
			}

			/**
    * Handles event for the key up press action within the input field.
    *
    * @param  {jQuery.Event} event Event fired.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onInputKeyUp',
			value: function _onInputKeyUp(event) {
				// Key codes.
				var KEY_ESC = 27;
				var KEY_ARROW_UP = 38;
				var KEY_ARROW_DOWN = 40;
				var KEY_ENTER = 13;

				switch (event.which) {
					// Hide search bar on escape key.
					case KEY_ESC:
						this._toggleDropdown(false)._togglePlaceholder(false).$input.trigger('blur');
						break;

					// Start the search on return key.
					case KEY_ENTER:
						this._performSearch();
						break;

					// Cycle selection through search entity list items on vertical arrow keys.
					case KEY_ARROW_UP:
					case KEY_ARROW_DOWN:
						var direction = event.which === KEY_ARROW_UP ? 'up' : 'down';
						this._cycleDropdownSelection(direction);
						break;

					// Fill search term into dropdown list items on letter keypress.
					default:
						this._fillTermInListItems();
				}

				return this;
			}

			/**
    * Handles event for the click action outside of the controller area.
    *
    * @param  {jQuery.Event} event Event fired.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onOutsideClick',
			value: function _onOutsideClick(event) {
				// Clicked target element.
				var $target = event.target;

				// Target element verifiers.
				var isNotTargetSearchArea = !this.$searchBar.has($target).length;
				var isNotTargetSearchButton = !this.$button.has($target).length;

				// Clear the placeholder and hide dropdown,
				// if clicked target is not within search area.
				if (isNotTargetSearchArea && isNotTargetSearchButton) {
					this._toggleDropdown(false)._togglePlaceholder(false).$input.trigger('blur');
				}

				return this;
			}

			/**
    * Handles event for the click action on a dropdown list item.
    *
    * @param  {jQuery.Event} event Event fired.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onListClick',
			value: function _onListClick(event) {
				// Get entity from list item.
				var entity = $(event.currentTarget).data('searchEntity');

				this._togglePlaceholder(true, entity)._setEntity(entity)._performSearch();

				return this;
			}

			/**
    * Handles event for the button click action.
    *
    * @param {jQuery.Event} event Fired event.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onButtonClick',
			value: function _onButtonClick(event) {
				// Proxy click and focus to the search input field.
				this.$input.trigger('click').trigger('focus');

				return this;
			}

			/**
    * Handles event for window inactivation.
    *
    * @param {jQuery.Event} event Fired event.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_onWindowBlur',
			value: function _onWindowBlur(event) {
				this._toggleDropdown(false)._togglePlaceholder(false).$input.trigger('blur');
			}

			/**
    * Fetches the translation for the entity.
    *
    * @param {String} entity Search entity.
    *
    * @return {String} Translated entity label text.
    */

		}, {
			key: '_getTranslationForEntity',
			value: function _getTranslationForEntity(entity) {
				// Language section.
				var section = 'admin_labels';

				// Translation text key.
				var key = 'admin_search_' + entity;

				// Return fetched translation text.
				return jse.core.lang.translate(key, section);
			}

			/**
    * Prepends the current search term into the dropdown list items.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_fillTermInListItems',
			value: function _fillTermInListItems() {
				var _this2 = this;

				// Get each entity search list item
				// and prepend current search term on to the elements.
				this.$list.find(this.listItemSearchTermPlaceholderSelector).each(function (index, element) {
					return $(element).text(_this2.binding.get());
				});

				return this;
			}

			/**
    * Shows and hides the dropdown.
    *
    * @param {Boolean} doShow Show the dropdown?
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_toggleDropdown',
			value: function _toggleDropdown(doShow) {
				// Class for visible dropdown.
				var ACTIVE_CLASS = 'active';

				// Toggle dropdown dependent on the provided boolean value.
				if (doShow) {
					this.$list.addClass(ACTIVE_CLASS);
				} else {
					this.$list.removeClass(ACTIVE_CLASS);
				}

				return this;
			}

			/**
    * Shows and hides the input placeholder.
    *
    * @param {Boolean} doShow Show the placeholder?
    *
    * @param {String} entity Entity to show as placeholder (optional).
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_togglePlaceholder',
			value: function _togglePlaceholder(doShow, entity) {
				if (doShow) {
					var _entity = _entity || this._getTranslationForEntity(this.entity);
					this.$input.prop('placeholder', _entity);
				} else {
					this.$input.prop('placeholder', '');
				}

				return this;
			}

			/**
    * Cycles selection through the dropdown.
    *
    * @param {String} direction Cycling direction (e.g: 'up' or 'down').
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_cycleDropdownSelection',
			value: function _cycleDropdownSelection(direction) {
				// Search entity list item elements.
				var $currentItem = this.$listItems.filter('.active');
				var $firstItem = this.$listItems.first();
				var $lastItem = this.$listItems.last();

				// Determine the next selected element of the direction.
				var $followingItem = direction === 'up' ? $currentItem.prev() : $currentItem.next();

				// If there is no next element, then the first/last element is selected.
				if (!$followingItem.length) {
					$followingItem = direction === 'up' ? $lastItem : $firstItem;
				}

				// Fetch search entity from next list item.
				var entity = $followingItem.data('searchEntity');

				// Remove selection style from current list item.
				$currentItem.removeClass('active');

				// Set entity value and select entity on the list item and set placeholder.
				this._setEntity(entity)._togglePlaceholder(true, entity);

				return this;
			}

			/**
    * Saves the search entity to the user configuration and performs the search.
    *
    * @return {SearchController} Same instance for method chaining.
    *
    * @private
    */

		}, {
			key: '_performSearch',
			value: function _performSearch() {
				// Default hyperlink open mode: Open in same tab.
				var OPEN_MODE = '_self';

				// Url prefix collection.
				var urlPrefixes = {
					customers: 'customers.php?search=',
					categories: 'categories.php?search=',
					orders: 'admin.php?' + $.param({
						do: 'OrdersOverview',
						filter: {
							number: ''
						}
					})
				};

				// Compose search URL with search term.
				var url = urlPrefixes[this.entity] + encodeURIComponent(this.binding.get());

				// Save selected entity to server via user configuration service.
				this._setUserCfgValue(this.entity);

				// Open composed URL.
				window.open(url, OPEN_MODE);
			}
		}]);

		return SearchController;
	}();

	// --------------------------------------------------------------------
	// INITIALIZATION
	// --------------------------------------------------------------------

	module.init = function (done) {
		var customerId = data.customerId;
		var $element = $(_this3);
		var UserCfgService = jse.libs.user_configuration_service;
		var Binding = module.bindings.input;

		var Search = new SearchController(customerId, $element, UserCfgService, Binding, done);
		Search.initialize();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9oZWFkZXIvc2VhcmNoLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiYmluZGluZ3MiLCJpbnB1dCIsIiQiLCJmaW5kIiwiU2VhcmNoQ29udHJvbGxlciIsImN1c3RvbWVySWQiLCIkZWxlbWVudCIsIlVzZXJDZmdTZXJ2aWNlIiwiQmluZGluZyIsImRvbmUiLCIkc2VhcmNoQmFyIiwiJGlucHV0IiwiJGxpc3QiLCIkbGlzdEl0ZW1zIiwiJGJ1dHRvbiIsImxpc3RJdGVtU2VhcmNoVGVybVBsYWNlaG9sZGVyU2VsZWN0b3IiLCJERUZBVUxUX0VOVElUWSIsImJpbmRpbmciLCJ1c2VyQ2ZnU2VydmljZSIsImVudGl0eSIsIm9uIiwiX29uSW5wdXRDbGljayIsImV2ZW50IiwiX29uSW5wdXRLZXlVcCIsIndpbmRvdyIsIl9vbk91dHNpZGVDbGljayIsIl9vbldpbmRvd0JsdXIiLCJfb25MaXN0Q2xpY2siLCJfb25CdXR0b25DbGljayIsIl9zZXRFbnRpdHkiLCJyZWNlbnRTZWFyY2hBcmVhIiwidmFsdWUiLCJleHRlbmQiLCJ1c2VyQ2ZnRGF0YSIsImNvbmZpZ3VyYXRpb25WYWx1ZSIsInNldCIsImFkZENsYXNzIiwiX3RvZ2dsZVBsYWNlaG9sZGVyIiwiX2ZpbGxUZXJtSW5MaXN0SXRlbXMiLCJfdG9nZ2xlRHJvcGRvd24iLCJLRVlfRVNDIiwiS0VZX0FSUk9XX1VQIiwiS0VZX0FSUk9XX0RPV04iLCJLRVlfRU5URVIiLCJ3aGljaCIsInRyaWdnZXIiLCJfcGVyZm9ybVNlYXJjaCIsImRpcmVjdGlvbiIsIl9jeWNsZURyb3Bkb3duU2VsZWN0aW9uIiwiJHRhcmdldCIsInRhcmdldCIsImlzTm90VGFyZ2V0U2VhcmNoQXJlYSIsImhhcyIsImxlbmd0aCIsImlzTm90VGFyZ2V0U2VhcmNoQnV0dG9uIiwiY3VycmVudFRhcmdldCIsInNlY3Rpb24iLCJrZXkiLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsImVhY2giLCJpbmRleCIsImVsZW1lbnQiLCJ0ZXh0IiwiZ2V0IiwiZG9TaG93IiwiQUNUSVZFX0NMQVNTIiwicmVtb3ZlQ2xhc3MiLCJfZ2V0VHJhbnNsYXRpb25Gb3JFbnRpdHkiLCJwcm9wIiwiJGN1cnJlbnRJdGVtIiwiZmlsdGVyIiwiJGZpcnN0SXRlbSIsImZpcnN0IiwiJGxhc3RJdGVtIiwibGFzdCIsIiRmb2xsb3dpbmdJdGVtIiwicHJldiIsIm5leHQiLCJPUEVOX01PREUiLCJ1cmxQcmVmaXhlcyIsImN1c3RvbWVycyIsImNhdGVnb3JpZXMiLCJvcmRlcnMiLCJwYXJhbSIsImRvIiwibnVtYmVyIiwidXJsIiwiZW5jb2RlVVJJQ29tcG9uZW50IiwiX3NldFVzZXJDZmdWYWx1ZSIsIm9wZW4iLCJpbml0IiwibGlicyIsInVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlIiwiU2VhcmNoIiwiaW5pdGlhbGl6ZSJdLCJtYXBwaW5ncyI6Ijs7Ozs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLFFBREQsRUFHQyxDQUFDLDRCQUFELENBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFSYzs7QUFhZCxLQUFNRCxTQUFTO0FBQ2RFLFlBQVU7QUFDVEMsVUFBT0MsRUFBRSxJQUFGLEVBQVFDLElBQVIsQ0FBYSxlQUFiO0FBREU7QUFESSxFQUFmOztBQU1BO0FBQ0E7QUFDQTs7QUFyQmMsS0F1QlJDLGdCQXZCUTtBQXdCYjs7Ozs7Ozs7O0FBU0EsNEJBQVlDLFVBQVosRUFBd0JDLFFBQXhCLEVBQWtDQyxjQUFsQyxFQUFrREMsT0FBbEQsRUFBMkRDLElBQTNELEVBQWlFO0FBQUE7O0FBQ2hFO0FBQ0EsUUFBS0MsVUFBTCxHQUFrQkosUUFBbEI7QUFDQSxRQUFLSyxNQUFMLEdBQWMsS0FBS0QsVUFBTCxDQUFnQlAsSUFBaEIsQ0FBcUIsZUFBckIsQ0FBZDtBQUNBLFFBQUtT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cyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBzZWFyY2guanMgMjAxNi0wNC0yM1xyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBHbG9iYWwgU2VhcmNoIENvbnRyb2xsZXIgTW9kdWxlXHJcbiAqL1xyXG5neC5jb250cm9sbGVycy5tb2R1bGUoXHJcblx0J3NlYXJjaCcsXHJcblx0XHJcblx0Wyd1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSddLFxyXG5cdFxyXG5cdGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcdFxyXG5cdFx0J3VzZSBzdHJpY3QnO1xyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gVkFSSUFCTEVTXHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0XHQgKi9cclxuXHRcdGNvbnN0IG1vZHVsZSA9IHtcclxuXHRcdFx0YmluZGluZ3M6IHtcclxuXHRcdFx0XHRpbnB1dDogJCh0aGlzKS5maW5kKCcuc2VhcmNoLWlucHV0JylcclxuXHRcdFx0fVxyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIEZVTkNUSU9OU1xyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdFxyXG5cdFx0Y2xhc3MgU2VhcmNoQ29udHJvbGxlciB7XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBDcmVhdGVzIGEgbmV3IHNlYXJjaCBjb250cm9sbGVyLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcGFyYW0ge051bWJlcn0gY3VzdG9tZXJJZCBDdXN0b21lciBJRC5cclxuXHRcdFx0ICogQHBhcmFtIHtqUXVlcnl9ICRlbGVtZW50IFNlYXJjaCBiYXIgY29udHJvbGxlciBlbGVtZW50LlxyXG5cdFx0XHQgKiBAcGFyYW0ge09iamVjdH0gVXNlckNmZ1NlcnZpY2UgVXNlciBjb25maWd1cmF0aW9uIGxpYnJhcnkuXHJcblx0XHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBCaW5kaW5nIERhdGEgYmluZGluZyBvYmplY3QuXHJcblx0XHRcdCAqIEBwYXJhbSB7RnVuY3Rpb259IGRvbmUgSlMtRW5naW5lIGZpbmlzaCBjYWxsYmFjayBmdW5jdGlvbi5cclxuXHRcdFx0ICovXHJcblx0XHRcdGNvbnN0cnVjdG9yKGN1c3RvbWVySWQsICRlbGVtZW50LCBVc2VyQ2ZnU2VydmljZSwgQmluZGluZywgZG9uZSkge1xyXG5cdFx0XHRcdC8vIFNldCBlbGVtZW50IHByb3BlcnRpZXMuXHJcblx0XHRcdFx0dGhpcy4kc2VhcmNoQmFyID0gJGVsZW1lbnQ7XHJcblx0XHRcdFx0dGhpcy4kaW5wdXQgPSB0aGlzLiRzZWFyY2hCYXIuZmluZCgnLnNlYXJjaC1pbnB1dCcpO1xyXG5cdFx0XHRcdHRoaXMuJGxpc3QgPSB0aGlzLiRzZWFyY2hCYXIuZmluZCgnLnNlYXJjaC1saXN0Jyk7XHJcblx0XHRcdFx0dGhpcy4kbGlzdEl0ZW1zID0gdGhpcy4kbGlzdC5maW5kKCcuc2VhcmNoLWxpc3QtaXRlbScpO1xyXG5cdFx0XHRcdHRoaXMuJGJ1dHRvbiA9ICQoJy5hY3Rpb25zIC5zZWFyY2gnKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBMaXN0IGl0ZW0gc2VhcmNoIHRlcm0gcGxhY2Vob2xkZXIgc2VsZWN0b3Igc3RyaW5nLlxyXG5cdFx0XHRcdHRoaXMubGlzdEl0ZW1TZWFyY2hUZXJtUGxhY2Vob2xkZXJTZWxlY3RvciA9ICcuc2VhcmNoLXRlcm0tcGxhY2Vob2xkZXInO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIFNldCBkZWZhdWx0IGVudGl0eS5cclxuXHRcdFx0XHR0aGlzLkRFRkFVTFRfRU5USVRZID0gJ29yZGVycyc7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gVHdvLXdheSBkYXRhIGJpbmRpbmcgb2JqZWN0LlxyXG5cdFx0XHRcdHRoaXMuYmluZGluZyA9IEJpbmRpbmc7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gU2V0IHVzZXIgY29uZmlndXJhdGlvbiBzZXJ2aWNlLlxyXG5cdFx0XHRcdHRoaXMudXNlckNmZ1NlcnZpY2UgPSBVc2VyQ2ZnU2VydmljZTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBDdXJyZW50IHNlYXJjaCBlbnRpdHkuXHJcblx0XHRcdFx0dGhpcy5lbnRpdHkgPSBudWxsO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIENhbGwgSlMtRW5naW5lIG1vZHVsZSBmaW5pc2ggY2FsbGJhY2suXHJcblx0XHRcdFx0ZG9uZSgpO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogQXR0YWNoZXMgdGhlIGV2ZW50IGxpc3RlbmVycyB0byB0aGUgd2lkZ2V0IGVsZW1lbnRzIGFuZCBzZXRzIHRoZSBzZWFyY2ggZW50aXR5LlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcmV0dXJuIHtTZWFyY2hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwdWJsaWNcclxuXHRcdFx0ICovXHJcblx0XHRcdGluaXRpYWxpemUoKSB7XHJcblx0XHRcdFx0Ly8gQmluZCBpbnB1dCBldmVudHMuXHJcblx0XHRcdFx0dGhpcy4kaW5wdXRcclxuXHRcdFx0XHRcdC5vbignY2xpY2snLCBldmVudCA9PiB0aGlzLl9vbklucHV0Q2xpY2soZXZlbnQpKVxyXG5cdFx0XHRcdFx0Lm9uKCdrZXl1cCcsIGV2ZW50ID0+IHRoaXMuX29uSW5wdXRLZXlVcChldmVudCkpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIEJpbmQgd2luZG93IGV2ZW50cy5cclxuXHRcdFx0XHQkKHdpbmRvdylcclxuXHRcdFx0XHRcdC5vbignY2xpY2snLCBldmVudCA9PiB0aGlzLl9vbk91dHNpZGVDbGljayhldmVudCkpXHJcblx0XHRcdFx0XHQub24oJ2JsdXInLCBldmVudCA9PiB0aGlzLl9vbldpbmRvd0JsdXIoZXZlbnQpKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBCaW5kIGxpc3QgaXRlbSBldmVudHMuXHJcblx0XHRcdFx0dGhpcy4kbGlzdEl0ZW1zLm9uKCdjbGljaycsIGV2ZW50ID0+IHRoaXMuX29uTGlzdENsaWNrKGV2ZW50KSk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gQmluZCBidXR0b24gZXZlbnQuXHJcblx0XHRcdFx0dGhpcy4kYnV0dG9uLm9uKCdjbGljaycsIGV2ZW50ID0+IHRoaXMuX29uQnV0dG9uQ2xpY2soZXZlbnQpKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBTZXQgc2VhcmNoIGVudGl0eS5cclxuXHRcdFx0XHR0aGlzLl9zZXRFbnRpdHkoZGF0YS5yZWNlbnRTZWFyY2hBcmVhIHx8IHRoaXMuREVGQVVMVF9FTlRJVFkpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdHJldHVybiB0aGlzO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogU2F2ZXMgdGhlIGVudGl0eSBhcyB1c2VyIGNvbmZpZ3VyYXRpb24gdmFsdWUuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwYXJhbSB7U3RyaW5nfSB2YWx1ZSBDb25maWd1cmF0aW9uIHZhbHVlLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcmV0dXJuIHtTZWFyY2hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwcml2YXRlXHJcblx0XHRcdCAqL1xyXG5cdFx0XHRfc2V0VXNlckNmZ1ZhbHVlKHZhbHVlKSB7XHJcblx0XHRcdFx0Ly8gVXNlciBjb25maWd1cmF0aW9uIHNlcnZpY2UgcmVxdWVzdCBvcHRpb25zLlxyXG5cdFx0XHRcdGNvbnN0IGRhdGEgPSAkLmV4dGVuZCh0cnVlLCB7fSwgdGhpcy51c2VyQ2ZnRGF0YSwge2NvbmZpZ3VyYXRpb25WYWx1ZTogdmFsdWV9KTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBTYXZlIGNvbmZpZ3VyYXRpb24gdmFsdWUgdG8gc2VydmVyLlxyXG5cdFx0XHRcdHRoaXMudXNlckNmZ1NlcnZpY2Uuc2V0KHtkYXRhfSk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0cmV0dXJuIHRoaXM7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBTZXRzIHRoZSBzZWFyY2ggZW50aXR5IGFuZCBhY3RpdmF0ZXMgdGhlIHJlc3BlY3RpdmUgbGlzdCBpdGVtIGVsZW1lbnQuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBlbnRpdHkgU2VhcmNoIGVudGl0eSBuYW1lLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcmV0dXJuIHtTZWFyY2hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwcml2YXRlXHJcblx0XHRcdCAqL1xyXG5cdFx0XHRfc2V0RW50aXR5KGVudGl0eSkge1xyXG5cdFx0XHRcdC8vIFNldCBpbnRlcm5hbCBlbnRpdHkgdmFsdWUuXHJcblx0XHRcdFx0dGhpcy5lbnRpdHkgPSBlbnRpdHk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gU2VsZWN0IGl0ZW0gaW4gc2VhcmNoIGVudGl0eSBsaXN0LlxyXG5cdFx0XHRcdHRoaXMuJGxpc3RcclxuXHRcdFx0XHRcdC5maW5kKGBsaVtkYXRhLXNlYXJjaC1lbnRpdHk9XCIke2VudGl0eX1cIl1gKVxyXG5cdFx0XHRcdFx0LmFkZENsYXNzKCdhY3RpdmUnKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXR1cm4gdGhpcztcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0LyoqXHJcblx0XHRcdCAqIEhhbmRsZXMgZXZlbnQgZm9yIHRoZSBjbGljayBhY3Rpb24gb24gdGhlIGlucHV0IGZpZWxkLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnQgRmlyZWQgZXZlbnQuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEByZXR1cm4ge1NlYXJjaENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHByaXZhdGVcclxuXHRcdFx0ICovXHJcblx0XHRcdF9vbklucHV0Q2xpY2soZXZlbnQpIHtcclxuXHRcdFx0XHR0aGlzXHJcblx0XHRcdFx0XHQuX3RvZ2dsZVBsYWNlaG9sZGVyKHRydWUpXHJcblx0XHRcdFx0XHQuX2ZpbGxUZXJtSW5MaXN0SXRlbXMoKVxyXG5cdFx0XHRcdFx0Ll90b2dnbGVEcm9wZG93bih0cnVlKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXR1cm4gdGhpcztcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0LyoqXHJcblx0XHRcdCAqIEhhbmRsZXMgZXZlbnQgZm9yIHRoZSBrZXkgdXAgcHJlc3MgYWN0aW9uIHdpdGhpbiB0aGUgaW5wdXQgZmllbGQuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwYXJhbSAge2pRdWVyeS5FdmVudH0gZXZlbnQgRXZlbnQgZmlyZWQuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEByZXR1cm4ge1NlYXJjaENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHByaXZhdGVcclxuXHRcdFx0ICovXHJcblx0XHRcdF9vbklucHV0S2V5VXAoZXZlbnQpIHtcclxuXHRcdFx0XHQvLyBLZXkgY29kZXMuXHJcblx0XHRcdFx0Y29uc3QgS0VZX0VTQyA9IDI3O1xyXG5cdFx0XHRcdGNvbnN0IEtFWV9BUlJPV19VUCA9IDM4O1xyXG5cdFx0XHRcdGNvbnN0IEtFWV9BUlJPV19ET1dOID0gNDA7XHJcblx0XHRcdFx0Y29uc3QgS0VZX0VOVEVSID0gMTM7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0c3dpdGNoIChldmVudC53aGljaCkge1xyXG5cdFx0XHRcdFx0Ly8gSGlkZSBzZWFyY2ggYmFyIG9uIGVzY2FwZSBrZXkuXHJcblx0XHRcdFx0XHRjYXNlIEtFWV9FU0M6XHJcblx0XHRcdFx0XHRcdHRoaXNcclxuXHRcdFx0XHRcdFx0XHQuX3RvZ2dsZURyb3Bkb3duKGZhbHNlKVxyXG5cdFx0XHRcdFx0XHRcdC5fdG9nZ2xlUGxhY2Vob2xkZXIoZmFsc2UpXHJcblx0XHRcdFx0XHRcdFx0LiRpbnB1dC50cmlnZ2VyKCdibHVyJyk7XHJcblx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdFx0XHJcblx0XHRcdFx0XHQvLyBTdGFydCB0aGUgc2VhcmNoIG9uIHJldHVybiBrZXkuXHJcblx0XHRcdFx0XHRjYXNlIEtFWV9FTlRFUjpcclxuXHRcdFx0XHRcdFx0dGhpcy5fcGVyZm9ybVNlYXJjaCgpO1xyXG5cdFx0XHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHRcdFxyXG5cdFx0XHRcdFx0Ly8gQ3ljbGUgc2VsZWN0aW9uIHRocm91Z2ggc2VhcmNoIGVudGl0eSBsaXN0IGl0ZW1zIG9uIHZlcnRpY2FsIGFycm93IGtleXMuXHJcblx0XHRcdFx0XHRjYXNlIEtFWV9BUlJPV19VUDpcclxuXHRcdFx0XHRcdGNhc2UgS0VZX0FSUk9XX0RPV046XHJcblx0XHRcdFx0XHRcdGNvbnN0IGRpcmVjdGlvbiA9IGV2ZW50LndoaWNoID09PSBLRVlfQVJST1dfVVAgPyAndXAnIDogJ2Rvd24nO1xyXG5cdFx0XHRcdFx0XHR0aGlzLl9jeWNsZURyb3Bkb3duU2VsZWN0aW9uKGRpcmVjdGlvbik7XHJcblx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdFx0XHJcblx0XHRcdFx0XHQvLyBGaWxsIHNlYXJjaCB0ZXJtIGludG8gZHJvcGRvd24gbGlzdCBpdGVtcyBvbiBsZXR0ZXIga2V5cHJlc3MuXHJcblx0XHRcdFx0XHRkZWZhdWx0OlxyXG5cdFx0XHRcdFx0XHR0aGlzLl9maWxsVGVybUluTGlzdEl0ZW1zKCk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdHJldHVybiB0aGlzO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogSGFuZGxlcyBldmVudCBmb3IgdGhlIGNsaWNrIGFjdGlvbiBvdXRzaWRlIG9mIHRoZSBjb250cm9sbGVyIGFyZWEuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwYXJhbSAge2pRdWVyeS5FdmVudH0gZXZlbnQgRXZlbnQgZmlyZWQuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEByZXR1cm4ge1NlYXJjaENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHByaXZhdGVcclxuXHRcdFx0ICovXHJcblx0XHRcdF9vbk91dHNpZGVDbGljayhldmVudCkge1xyXG5cdFx0XHRcdC8vIENsaWNrZWQgdGFyZ2V0IGVsZW1lbnQuXHJcblx0XHRcdFx0Y29uc3QgJHRhcmdldCA9IGV2ZW50LnRhcmdldDtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBUYXJnZXQgZWxlbWVudCB2ZXJpZmllcnMuXHJcblx0XHRcdFx0Y29uc3QgaXNOb3RUYXJnZXRTZWFyY2hBcmVhID0gIXRoaXMuJHNlYXJjaEJhci5oYXMoJHRhcmdldCkubGVuZ3RoO1xyXG5cdFx0XHRcdGNvbnN0IGlzTm90VGFyZ2V0U2VhcmNoQnV0dG9uID0gIXRoaXMuJGJ1dHRvbi5oYXMoJHRhcmdldCkubGVuZ3RoO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIENsZWFyIHRoZSBwbGFjZWhvbGRlciBhbmQgaGlkZSBkcm9wZG93bixcclxuXHRcdFx0XHQvLyBpZiBjbGlja2VkIHRhcmdldCBpcyBub3Qgd2l0aGluIHNlYXJjaCBhcmVhLlxyXG5cdFx0XHRcdGlmIChpc05vdFRhcmdldFNlYXJjaEFyZWEgJiYgaXNOb3RUYXJnZXRTZWFyY2hCdXR0b24pIHtcclxuXHRcdFx0XHRcdHRoaXNcclxuXHRcdFx0XHRcdFx0Ll90b2dnbGVEcm9wZG93bihmYWxzZSlcclxuXHRcdFx0XHRcdFx0Ll90b2dnbGVQbGFjZWhvbGRlcihmYWxzZSlcclxuXHRcdFx0XHRcdFx0LiRpbnB1dC50cmlnZ2VyKCdibHVyJyk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdHJldHVybiB0aGlzO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogSGFuZGxlcyBldmVudCBmb3IgdGhlIGNsaWNrIGFjdGlvbiBvbiBhIGRyb3Bkb3duIGxpc3QgaXRlbS5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHBhcmFtICB7alF1ZXJ5LkV2ZW50fSBldmVudCBFdmVudCBmaXJlZC5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHJldHVybiB7U2VhcmNoQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcHJpdmF0ZVxyXG5cdFx0XHQgKi9cclxuXHRcdFx0X29uTGlzdENsaWNrKGV2ZW50KSB7XHJcblx0XHRcdFx0Ly8gR2V0IGVudGl0eSBmcm9tIGxpc3QgaXRlbS5cclxuXHRcdFx0XHRjb25zdCBlbnRpdHkgPSAkKGV2ZW50LmN1cnJlbnRUYXJnZXQpLmRhdGEoJ3NlYXJjaEVudGl0eScpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdHRoaXNcclxuXHRcdFx0XHRcdC5fdG9nZ2xlUGxhY2Vob2xkZXIodHJ1ZSwgZW50aXR5KVxyXG5cdFx0XHRcdFx0Ll9zZXRFbnRpdHkoZW50aXR5KVxyXG5cdFx0XHRcdFx0Ll9wZXJmb3JtU2VhcmNoKCk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0cmV0dXJuIHRoaXM7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBIYW5kbGVzIGV2ZW50IGZvciB0aGUgYnV0dG9uIGNsaWNrIGFjdGlvbi5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IEZpcmVkIGV2ZW50LlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcmV0dXJuIHtTZWFyY2hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwcml2YXRlXHJcblx0XHRcdCAqL1xyXG5cdFx0XHRfb25CdXR0b25DbGljayhldmVudCkge1xyXG5cdFx0XHRcdC8vIFByb3h5IGNsaWNrIGFuZCBmb2N1cyB0byB0aGUgc2VhcmNoIGlucHV0IGZpZWxkLlxyXG5cdFx0XHRcdHRoaXMuJGlucHV0XHJcblx0XHRcdFx0XHQudHJpZ2dlcignY2xpY2snKVxyXG5cdFx0XHRcdFx0LnRyaWdnZXIoJ2ZvY3VzJyk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0cmV0dXJuIHRoaXM7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBIYW5kbGVzIGV2ZW50IGZvciB3aW5kb3cgaW5hY3RpdmF0aW9uLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnQgRmlyZWQgZXZlbnQuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEByZXR1cm4ge1NlYXJjaENvbnRyb2xsZXJ9IFNhbWUgaW5zdGFuY2UgZm9yIG1ldGhvZCBjaGFpbmluZy5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHByaXZhdGVcclxuXHRcdFx0ICovXHJcblx0XHRcdF9vbldpbmRvd0JsdXIoZXZlbnQpIHtcclxuXHRcdFx0XHR0aGlzXHJcblx0XHRcdFx0XHQuX3RvZ2dsZURyb3Bkb3duKGZhbHNlKVxyXG5cdFx0XHRcdFx0Ll90b2dnbGVQbGFjZWhvbGRlcihmYWxzZSlcclxuXHRcdFx0XHRcdC4kaW5wdXQudHJpZ2dlcignYmx1cicpO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogRmV0Y2hlcyB0aGUgdHJhbnNsYXRpb24gZm9yIHRoZSBlbnRpdHkuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBlbnRpdHkgU2VhcmNoIGVudGl0eS5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHJldHVybiB7U3RyaW5nfSBUcmFuc2xhdGVkIGVudGl0eSBsYWJlbCB0ZXh0LlxyXG5cdFx0XHQgKi9cclxuXHRcdFx0X2dldFRyYW5zbGF0aW9uRm9yRW50aXR5KGVudGl0eSkge1xyXG5cdFx0XHRcdC8vIExhbmd1YWdlIHNlY3Rpb24uXHJcblx0XHRcdFx0Y29uc3Qgc2VjdGlvbiA9ICdhZG1pbl9sYWJlbHMnO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIFRyYW5zbGF0aW9uIHRleHQga2V5LlxyXG5cdFx0XHRcdGNvbnN0IGtleSA9IGBhZG1pbl9zZWFyY2hfJHtlbnRpdHl9YDtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBSZXR1cm4gZmV0Y2hlZCB0cmFuc2xhdGlvbiB0ZXh0LlxyXG5cdFx0XHRcdHJldHVybiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZShrZXksIHNlY3Rpb24pO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogUHJlcGVuZHMgdGhlIGN1cnJlbnQgc2VhcmNoIHRlcm0gaW50byB0aGUgZHJvcGRvd24gbGlzdCBpdGVtcy5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHJldHVybiB7U2VhcmNoQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcHJpdmF0ZVxyXG5cdFx0XHQgKi9cclxuXHRcdFx0X2ZpbGxUZXJtSW5MaXN0SXRlbXMoKSB7XHJcblx0XHRcdFx0Ly8gR2V0IGVhY2ggZW50aXR5IHNlYXJjaCBsaXN0IGl0ZW1cclxuXHRcdFx0XHQvLyBhbmQgcHJlcGVuZCBjdXJyZW50IHNlYXJjaCB0ZXJtIG9uIHRvIHRoZSBlbGVtZW50cy5cclxuXHRcdFx0XHR0aGlzLiRsaXN0XHJcblx0XHRcdFx0XHQuZmluZCh0aGlzLmxpc3RJdGVtU2VhcmNoVGVybVBsYWNlaG9sZGVyU2VsZWN0b3IpXHJcblx0XHRcdFx0XHQuZWFjaCgoaW5kZXgsIGVsZW1lbnQpID0+ICQoZWxlbWVudCkudGV4dCh0aGlzLmJpbmRpbmcuZ2V0KCkpKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXR1cm4gdGhpcztcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0LyoqXHJcblx0XHRcdCAqIFNob3dzIGFuZCBoaWRlcyB0aGUgZHJvcGRvd24uXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwYXJhbSB7Qm9vbGVhbn0gZG9TaG93IFNob3cgdGhlIGRyb3Bkb3duP1xyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcmV0dXJuIHtTZWFyY2hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwcml2YXRlXHJcblx0XHRcdCAqL1xyXG5cdFx0XHRfdG9nZ2xlRHJvcGRvd24oZG9TaG93KSB7XHJcblx0XHRcdFx0Ly8gQ2xhc3MgZm9yIHZpc2libGUgZHJvcGRvd24uXHJcblx0XHRcdFx0Y29uc3QgQUNUSVZFX0NMQVNTID0gJ2FjdGl2ZSc7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gVG9nZ2xlIGRyb3Bkb3duIGRlcGVuZGVudCBvbiB0aGUgcHJvdmlkZWQgYm9vbGVhbiB2YWx1ZS5cclxuXHRcdFx0XHRpZiAoZG9TaG93KSB7XHJcblx0XHRcdFx0XHR0aGlzLiRsaXN0LmFkZENsYXNzKEFDVElWRV9DTEFTUyk7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdHRoaXMuJGxpc3QucmVtb3ZlQ2xhc3MoQUNUSVZFX0NMQVNTKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0cmV0dXJuIHRoaXM7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBTaG93cyBhbmQgaGlkZXMgdGhlIGlucHV0IHBsYWNlaG9sZGVyLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcGFyYW0ge0Jvb2xlYW59IGRvU2hvdyBTaG93IHRoZSBwbGFjZWhvbGRlcj9cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHBhcmFtIHtTdHJpbmd9IGVudGl0eSBFbnRpdHkgdG8gc2hvdyBhcyBwbGFjZWhvbGRlciAob3B0aW9uYWwpLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcmV0dXJuIHtTZWFyY2hDb250cm9sbGVyfSBTYW1lIGluc3RhbmNlIGZvciBtZXRob2QgY2hhaW5pbmcuXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEBwcml2YXRlXHJcblx0XHRcdCAqL1xyXG5cdFx0XHRfdG9nZ2xlUGxhY2Vob2xkZXIoZG9TaG93LCBlbnRpdHkpIHtcclxuXHRcdFx0XHRpZiAoZG9TaG93KSB7XHJcblx0XHRcdFx0XHRjb25zdCBlbnRpdHkgPSBlbnRpdHkgfHwgdGhpcy5fZ2V0VHJhbnNsYXRpb25Gb3JFbnRpdHkodGhpcy5lbnRpdHkpO1xyXG5cdFx0XHRcdFx0dGhpcy4kaW5wdXQucHJvcCgncGxhY2Vob2xkZXInLCBlbnRpdHkpO1xyXG5cdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHR0aGlzLiRpbnB1dC5wcm9wKCdwbGFjZWhvbGRlcicsICcnKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0cmV0dXJuIHRoaXM7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBDeWNsZXMgc2VsZWN0aW9uIHRocm91Z2ggdGhlIGRyb3Bkb3duLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcGFyYW0ge1N0cmluZ30gZGlyZWN0aW9uIEN5Y2xpbmcgZGlyZWN0aW9uIChlLmc6ICd1cCcgb3IgJ2Rvd24nKS5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHJldHVybiB7U2VhcmNoQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcHJpdmF0ZVxyXG5cdFx0XHQgKi9cclxuXHRcdFx0X2N5Y2xlRHJvcGRvd25TZWxlY3Rpb24oZGlyZWN0aW9uKSB7XHJcblx0XHRcdFx0Ly8gU2VhcmNoIGVudGl0eSBsaXN0IGl0ZW0gZWxlbWVudHMuXHJcblx0XHRcdFx0Y29uc3QgJGN1cnJlbnRJdGVtID0gdGhpcy4kbGlzdEl0ZW1zLmZpbHRlcignLmFjdGl2ZScpO1xyXG5cdFx0XHRcdGNvbnN0ICRmaXJzdEl0ZW0gPSB0aGlzLiRsaXN0SXRlbXMuZmlyc3QoKTtcclxuXHRcdFx0XHRjb25zdCAkbGFzdEl0ZW0gPSB0aGlzLiRsaXN0SXRlbXMubGFzdCgpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIERldGVybWluZSB0aGUgbmV4dCBzZWxlY3RlZCBlbGVtZW50IG9mIHRoZSBkaXJlY3Rpb24uXHJcblx0XHRcdFx0bGV0ICRmb2xsb3dpbmdJdGVtID0gZGlyZWN0aW9uID09PSAndXAnID9cclxuXHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAkY3VycmVudEl0ZW0ucHJldigpIDogJGN1cnJlbnRJdGVtLm5leHQoKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBJZiB0aGVyZSBpcyBubyBuZXh0IGVsZW1lbnQsIHRoZW4gdGhlIGZpcnN0L2xhc3QgZWxlbWVudCBpcyBzZWxlY3RlZC5cclxuXHRcdFx0XHRpZiAoISRmb2xsb3dpbmdJdGVtLmxlbmd0aCkge1xyXG5cdFx0XHRcdFx0JGZvbGxvd2luZ0l0ZW0gPSBkaXJlY3Rpb24gPT09ICd1cCcgPyAkbGFzdEl0ZW0gOiAkZmlyc3RJdGVtO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBGZXRjaCBzZWFyY2ggZW50aXR5IGZyb20gbmV4dCBsaXN0IGl0ZW0uXHJcblx0XHRcdFx0Y29uc3QgZW50aXR5ID0gJGZvbGxvd2luZ0l0ZW0uZGF0YSgnc2VhcmNoRW50aXR5Jyk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gUmVtb3ZlIHNlbGVjdGlvbiBzdHlsZSBmcm9tIGN1cnJlbnQgbGlzdCBpdGVtLlxyXG5cdFx0XHRcdCRjdXJyZW50SXRlbS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gU2V0IGVudGl0eSB2YWx1ZSBhbmQgc2VsZWN0IGVudGl0eSBvbiB0aGUgbGlzdCBpdGVtIGFuZCBzZXQgcGxhY2Vob2xkZXIuXHJcblx0XHRcdFx0dGhpc1xyXG5cdFx0XHRcdFx0Ll9zZXRFbnRpdHkoZW50aXR5KVxyXG5cdFx0XHRcdFx0Ll90b2dnbGVQbGFjZWhvbGRlcih0cnVlLCBlbnRpdHkpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdHJldHVybiB0aGlzO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogU2F2ZXMgdGhlIHNlYXJjaCBlbnRpdHkgdG8gdGhlIHVzZXIgY29uZmlndXJhdGlvbiBhbmQgcGVyZm9ybXMgdGhlIHNlYXJjaC5cclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHJldHVybiB7U2VhcmNoQ29udHJvbGxlcn0gU2FtZSBpbnN0YW5jZSBmb3IgbWV0aG9kIGNoYWluaW5nLlxyXG5cdFx0XHQgKlxyXG5cdFx0XHQgKiBAcHJpdmF0ZVxyXG5cdFx0XHQgKi9cclxuXHRcdFx0X3BlcmZvcm1TZWFyY2goKSB7XHJcblx0XHRcdFx0Ly8gRGVmYXVsdCBoeXBlcmxpbmsgb3BlbiBtb2RlOiBPcGVuIGluIHNhbWUgdGFiLlxyXG5cdFx0XHRcdGNvbnN0IE9QRU5fTU9ERSA9ICdfc2VsZic7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gVXJsIHByZWZpeCBjb2xsZWN0aW9uLlxyXG5cdFx0XHRcdGNvbnN0IHVybFByZWZpeGVzID0ge1xyXG5cdFx0XHRcdFx0Y3VzdG9tZXJzOiAnY3VzdG9tZXJzLnBocD9zZWFyY2g9JyxcclxuXHRcdFx0XHRcdGNhdGVnb3JpZXM6ICdjYXRlZ29yaWVzLnBocD9zZWFyY2g9JyxcclxuXHRcdFx0XHRcdG9yZGVyczogJ2FkbWluLnBocD8nICsgJC5wYXJhbSh7XHJcblx0XHRcdFx0XHRcdGRvOiAnT3JkZXJzT3ZlcnZpZXcnLCBcclxuXHRcdFx0XHRcdFx0ZmlsdGVyOiB7XHJcblx0XHRcdFx0XHRcdFx0bnVtYmVyOiAnJ1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9KVxyXG5cdFx0XHRcdH07XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gQ29tcG9zZSBzZWFyY2ggVVJMIHdpdGggc2VhcmNoIHRlcm0uXHJcblx0XHRcdFx0Y29uc3QgdXJsID0gdXJsUHJlZml4ZXNbdGhpcy5lbnRpdHldICsgZW5jb2RlVVJJQ29tcG9uZW50KHRoaXMuYmluZGluZy5nZXQoKSk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gU2F2ZSBzZWxlY3RlZCBlbnRpdHkgdG8gc2VydmVyIHZpYSB1c2VyIGNvbmZpZ3VyYXRpb24gc2VydmljZS5cclxuXHRcdFx0XHR0aGlzLl9zZXRVc2VyQ2ZnVmFsdWUodGhpcy5lbnRpdHkpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIE9wZW4gY29tcG9zZWQgVVJMLlxyXG5cdFx0XHRcdHdpbmRvdy5vcGVuKHVybCwgT1BFTl9NT0RFKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdG1vZHVsZS5pbml0ID0gKGRvbmUpID0+IHtcclxuXHRcdFx0Y29uc3QgY3VzdG9tZXJJZCA9IGRhdGEuY3VzdG9tZXJJZDtcclxuXHRcdFx0Y29uc3QgJGVsZW1lbnQgPSAkKHRoaXMpO1xyXG5cdFx0XHRjb25zdCBVc2VyQ2ZnU2VydmljZSA9IGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlO1xyXG5cdFx0XHRjb25zdCBCaW5kaW5nID0gbW9kdWxlLmJpbmRpbmdzLmlucHV0O1xyXG5cdFx0XHRcclxuXHRcdFx0Y29uc3QgU2VhcmNoID0gbmV3IFNlYXJjaENvbnRyb2xsZXIoY3VzdG9tZXJJZCwgJGVsZW1lbnQsIFVzZXJDZmdTZXJ2aWNlLCBCaW5kaW5nLCBkb25lKTtcclxuXHRcdFx0U2VhcmNoLmluaXRpYWxpemUoKTtcclxuXHRcdH07XHJcblx0XHRcclxuXHRcdHJldHVybiBtb2R1bGU7XHJcblx0fSk7XHJcbiJdfQ==
