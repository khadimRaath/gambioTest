/* --------------------------------------------------------------
 admin_search.js 2016-09-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Admin Search Extension
 *
 * Extension for search in orders, customers and categories in the admin panel
 *
 * @module Admin/Extension/admin_search
 * @requires jQueryUI
 * @ignore
 */
gx.extensions.module(
	'admin_search',
	
	['user_configuration_service', 'url_arguments'],

	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		// Elements.
		var $this = $(this),
			$button = $(data.button),
			$dropdown = $('ul.searchable:first'),
			recentSearch = decodeURI(jse.libs.url_arguments.getUrlParameters(location.href).search || '');
		
		// Current search area.
		var searchArea;
		
		// Text labels.
		var labels = {
			searchIn: jse.core.lang.translate('admin_search_in_label', 'admin_labels'),
			orders: jse.core.lang.translate('admin_search_orders', 'admin_labels'),
			customers: jse.core.lang.translate('admin_search_customers', 'admin_labels'),
			categories: jse.core.lang.translate('admin_search_categories', 'admin_labels')
		};
		
		// Key code map.
		var keyMap = {
			ESC: 27,
			ARROW_UP: 38,
			ARROW_DOWN: 40,
			ENTER: 13
		};
		
		// Library access shortcuts.
		var userConfigurationService = jse.libs.user_configuration_service,
			urlArguments = jse.libs.url_arguments;
		
		// Configuration settings for UserConfigurationService.
		var configurationContainer = {
			userId: data.customer_id,
			configurationKey: 'recentSearchArea'
		};
		
		// Module object (JSEngine).
		var module = {};
		
		// ------------------------------------------------------------------------
		// METHODS
		// ------------------------------------------------------------------------
		
		/**
		 * Determines the actual page to set the search area variable
		 * @private
		 */
		var _initializeSearchArea = function() {
			switch (urlArguments.getCurrentFile()) {
				case 'orders.php':
					searchArea = 'orders';
					$dropdown.trigger('select:item');
					break;
				case 'customers.php':
					searchArea = 'customers';
					$dropdown.trigger('select:item');
					break;
				case 'categories.php':
					searchArea = 'categories';
					$dropdown.trigger('select:item');
					break;
				default:
					userConfigurationService.get({
						data: configurationContainer,
						
						onSuccess: function(response) {
							if (response.success && response.configurationValue) {
								searchArea = response.configurationValue;
								$dropdown.trigger('select:item');
							}
							else {
								searchArea = 'categories';
								$dropdown.trigger('select:item');
							}
						},
						
						onError: function() {
							searchArea = 'categories';
							$dropdown.trigger('select:item');
						}
					});
			}
		};
		
		/**
		 * Refreshes the search area variable
		 *
		 * Shows the new search area in the button
		 * @private
		 */
		var _refreshSearchArea = function() {
			// Abort if no new search area is provided
			if (!$('.search-item.active').length) {
				console.error('No active list item!');
			}
			
			// Assign new search area
			searchArea = $('.search-item.active').data('searchArea');
			$this.trigger('refresh:search-area');
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		var _initializeInput = function() {
			
			// Click event
			$this.on('click', function() {
				$this.trigger('refresh:search-area');
				if($this.val() === '')
				{
					$this.val(recentSearch);
				}
				$dropdown.trigger('show:dropdown');
				$this.trigger('focus');
			});
			
			// Keyboard events
			$this.on('keyup', function(event) {
				switch (event.which) {
					
					// Perform search if enter key is pressed
					case keyMap.ENTER:
						$this.trigger('perform:search');
						break;
					
					// Close dropdown if escape key is pressed
					case keyMap.ESC:
						$dropdown.trigger('hide:dropdown');
						return;
					
					// Navigate up in dropdown
					case keyMap.ARROW_UP:
						$dropdown.trigger('select:item:previous');
						break;
					case keyMap.ARROW_DOWN:
						$dropdown.trigger('select:item:next');
						break;
				}
				$dropdown.trigger('refresh:search-item');
			});
			
			// Search events
			$this.on('perform:search', function() {
				var inputValue = encodeURIComponent($this.val()),
					openMode = '_self',
					url;
				
				switch (searchArea) {
					case 'customers':
						url = [
							'customers.php',
							'?search=',
							inputValue
						].join('');
						break;
					case 'categories':
						url = [
							'categories.php',
							'?search=',
							inputValue
						].join('');
						break;
					case 'orders':
						url = [
							'admin.php',
							'?',
							$.param({
								do: 'OrdersOverview',
								filter: {
									number: inputValue
								}
							})
						].join('');
						break;
				}
				
				userConfigurationService.set({
					data: $.extend(configurationContainer, {
						configurationValue: searchArea
					})
				});
				window.open(url, openMode);
			});
			
			// Change search area event
			$this.on('refresh:search-area', function() {
				$this.prop('placeholder', labels[searchArea]);
			});
			
			// Remove placeholder when input is inactive
			$this.on('blur', function() {
				$this.prop('placeholder', '');
				$dropdown.trigger('hide:dropdown');
			});
		};
		
		var _initializeButton = function() {
			$button.on('click', function() {
				$this.trigger('refresh:search-area');
				$this.val(recentSearch);
				$dropdown.trigger('show:dropdown');
				$this.trigger('focus');
			});
		};
		
		var _initializeDropdown = function() {
			// Select item
			$dropdown.on('select:item', function() {
				$dropdown
					.find('li[data-search-area=' + searchArea + ']')
					.addClass('active');
			});
			
			// Show event
			$dropdown.on('show:dropdown', function() {
				$dropdown.fadeIn();
				$dropdown.trigger('select:item');
				$dropdown.trigger('refresh:search-item');
				
			});
			
			// Select first item
			$dropdown.on('select:item:first', function() {
				var $activeListItem = $dropdown.find('li.search-item.active');
				var $firstListItem = $dropdown.find('li.search-item:first');
				$activeListItem.removeClass('active');
				$firstListItem.addClass('active');
				_refreshSearchArea();
				$dropdown.trigger('select:item');
			});
			
			$dropdown.on('select:item:last', function() {
				var $activeListItem = $dropdown.find('li.search-item.active');
				var $lastListItem = $dropdown.find('li.search-item:last');
				$activeListItem.removeClass('active');
				$lastListItem.addClass('active');
				_refreshSearchArea();
				$dropdown.trigger('select:item');
			});
			
			// Select previous item event
			$dropdown.on('select:item:previous', function() {
				var $activeListItem = $dropdown.find('li.search-item.active');
				var $prev = $activeListItem.prev();
				
				if ($prev.length) {
					$activeListItem.removeClass('active');
					$prev.addClass('active');
					_refreshSearchArea();
					$dropdown.trigger('select:item');
				} else {
					$dropdown.trigger('select:item:last');
				}
			});
			
			// Select previous item event
			$dropdown.on('select:item:next', function() {
				var $activeListItem = $dropdown.find('li.search-item.active');
				var $next = $activeListItem.next();
				
				if ($next.length) {
					$activeListItem.removeClass('active');
					$next.addClass('active');
					_refreshSearchArea();
					$dropdown.trigger('select:item');
				} else {
					$dropdown.trigger('select:item:first');
				}
			});
			
			// Hide event
			$dropdown.on('hide:dropdown', function() {
				$dropdown.fadeOut();
			});
			
			// Item click event
			$dropdown.on('click', function(event) {
				event.stopPropagation();
				
				$dropdown
					.find('li')
					.removeClass('active');
				
				var $elementToActivate = $(event.target).is('span') ?
				                         $(event.target).parents('li:first') :
				                         $(event.target);
				
				$elementToActivate.addClass('active');
				
				_refreshSearchArea();
				$dropdown.trigger('hide:dropdown');
				$this.trigger('perform:search');
			});
			
			// Item search event
			$dropdown.on('refresh:search-item', function() {
				$('.search-item').each(function() {
					// Update search query
					$(this)
						.find('.search-query-item')
						.text($this.val());
					
					// Update search description
					var searchAreaText = [
						labels.searchIn,
						labels[$(this).data('searchArea')]
					].join(' ');
					
					$(this)
						.find('.search-query-description')
						.text(searchAreaText);
				});
			});
		};
		
		var _initializeRecentSearch = function() {
			$(document).on('JSENGINE_INIT_FINISHED', function() {
				if (recentSearch !== '') {
					$this.prop('value', recentSearch);
					$this.focus();
				}
			});
		};
		
		/**
		 * Initialize method of the extension, called by the engine.
		 */
		module.init = function(done) {
			_initializeInput();
			_initializeDropdown();
			_initializeButton();
			_initializeRecentSearch();
			
			searchArea = data.recentSearchArea || 'categories';
			$dropdown.trigger('select:item');
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
