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
gx.controllers.module(
	'search',
	
	['user_configuration_service'],
	
	function(data) {
		
		'use strict';
		
		// --------------------------------------------------------------------
		// VARIABLES
		// --------------------------------------------------------------------
		
		/**
		 * Module Instance
		 *
		 * @type {Object}
		 */
		const module = {
			bindings: {
				input: $(this).find('.search-input')
			}
		};
		
		// --------------------------------------------------------------------
		// FUNCTIONS
		// --------------------------------------------------------------------
		
		class SearchController {
			/**
			 * Creates a new search controller.
			 *
			 * @param {Number} customerId Customer ID.
			 * @param {jQuery} $element Search bar controller element.
			 * @param {Object} UserCfgService User configuration library.
			 * @param {Object} Binding Data binding object.
			 * @param {Function} done JS-Engine finish callback function.
			 */
			constructor(customerId, $element, UserCfgService, Binding, done) {
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
			initialize() {
				// Bind input events.
				this.$input
					.on('click', event => this._onInputClick(event))
					.on('keyup', event => this._onInputKeyUp(event));
				
				// Bind window events.
				$(window)
					.on('click', event => this._onOutsideClick(event))
					.on('blur', event => this._onWindowBlur(event));
				
				// Bind list item events.
				this.$listItems.on('click', event => this._onListClick(event));
				
				// Bind button event.
				this.$button.on('click', event => this._onButtonClick(event));
				
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
			_setUserCfgValue(value) {
				// User configuration service request options.
				const data = $.extend(true, {}, this.userCfgData, {configurationValue: value});
				
				// Save configuration value to server.
				this.userCfgService.set({data});
				
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
			_setEntity(entity) {
				// Set internal entity value.
				this.entity = entity;
				
				// Select item in search entity list.
				this.$list
					.find(`li[data-search-entity="${entity}"]`)
					.addClass('active');
				
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
			_onInputClick(event) {
				this
					._togglePlaceholder(true)
					._fillTermInListItems()
					._toggleDropdown(true);
				
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
			_onInputKeyUp(event) {
				// Key codes.
				const KEY_ESC = 27;
				const KEY_ARROW_UP = 38;
				const KEY_ARROW_DOWN = 40;
				const KEY_ENTER = 13;
				
				switch (event.which) {
					// Hide search bar on escape key.
					case KEY_ESC:
						this
							._toggleDropdown(false)
							._togglePlaceholder(false)
							.$input.trigger('blur');
						break;
					
					// Start the search on return key.
					case KEY_ENTER:
						this._performSearch();
						break;
					
					// Cycle selection through search entity list items on vertical arrow keys.
					case KEY_ARROW_UP:
					case KEY_ARROW_DOWN:
						const direction = event.which === KEY_ARROW_UP ? 'up' : 'down';
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
			_onOutsideClick(event) {
				// Clicked target element.
				const $target = event.target;
				
				// Target element verifiers.
				const isNotTargetSearchArea = !this.$searchBar.has($target).length;
				const isNotTargetSearchButton = !this.$button.has($target).length;
				
				// Clear the placeholder and hide dropdown,
				// if clicked target is not within search area.
				if (isNotTargetSearchArea && isNotTargetSearchButton) {
					this
						._toggleDropdown(false)
						._togglePlaceholder(false)
						.$input.trigger('blur');
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
			_onListClick(event) {
				// Get entity from list item.
				const entity = $(event.currentTarget).data('searchEntity');
				
				this
					._togglePlaceholder(true, entity)
					._setEntity(entity)
					._performSearch();
				
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
			_onButtonClick(event) {
				// Proxy click and focus to the search input field.
				this.$input
					.trigger('click')
					.trigger('focus');
				
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
			_onWindowBlur(event) {
				this
					._toggleDropdown(false)
					._togglePlaceholder(false)
					.$input.trigger('blur');
			}
			
			/**
			 * Fetches the translation for the entity.
			 *
			 * @param {String} entity Search entity.
			 *
			 * @return {String} Translated entity label text.
			 */
			_getTranslationForEntity(entity) {
				// Language section.
				const section = 'admin_labels';
				
				// Translation text key.
				const key = `admin_search_${entity}`;
				
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
			_fillTermInListItems() {
				// Get each entity search list item
				// and prepend current search term on to the elements.
				this.$list
					.find(this.listItemSearchTermPlaceholderSelector)
					.each((index, element) => $(element).text(this.binding.get()));
				
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
			_toggleDropdown(doShow) {
				// Class for visible dropdown.
				const ACTIVE_CLASS = 'active';
				
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
			_togglePlaceholder(doShow, entity) {
				if (doShow) {
					const entity = entity || this._getTranslationForEntity(this.entity);
					this.$input.prop('placeholder', entity);
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
			_cycleDropdownSelection(direction) {
				// Search entity list item elements.
				const $currentItem = this.$listItems.filter('.active');
				const $firstItem = this.$listItems.first();
				const $lastItem = this.$listItems.last();
				
				// Determine the next selected element of the direction.
				let $followingItem = direction === 'up' ?
				                     $currentItem.prev() : $currentItem.next();
				
				// If there is no next element, then the first/last element is selected.
				if (!$followingItem.length) {
					$followingItem = direction === 'up' ? $lastItem : $firstItem;
				}
				
				// Fetch search entity from next list item.
				const entity = $followingItem.data('searchEntity');
				
				// Remove selection style from current list item.
				$currentItem.removeClass('active');
				
				// Set entity value and select entity on the list item and set placeholder.
				this
					._setEntity(entity)
					._togglePlaceholder(true, entity);
				
				return this;
			}
			
			/**
			 * Saves the search entity to the user configuration and performs the search.
			 *
			 * @return {SearchController} Same instance for method chaining.
			 *
			 * @private
			 */
			_performSearch() {
				// Default hyperlink open mode: Open in same tab.
				const OPEN_MODE = '_self';
				
				// Url prefix collection.
				const urlPrefixes = {
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
				const url = urlPrefixes[this.entity] + encodeURIComponent(this.binding.get());
				
				// Save selected entity to server via user configuration service.
				this._setUserCfgValue(this.entity);
				
				// Open composed URL.
				window.open(url, OPEN_MODE);
			}
		}
		
		// --------------------------------------------------------------------
		// INITIALIZATION
		// --------------------------------------------------------------------
		
		module.init = (done) => {
			const customerId = data.customerId;
			const $element = $(this);
			const UserCfgService = jse.libs.user_configuration_service;
			const Binding = module.bindings.input;
			
			const Search = new SearchController(customerId, $element, UserCfgService, Binding, done);
			Search.initialize();
		};
		
		return module;
	});
