'use strict';

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
gx.extensions.module('admin_search', ['user_configuration_service', 'url_arguments'], function (data) {

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
	var _initializeSearchArea = function _initializeSearchArea() {
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

					onSuccess: function onSuccess(response) {
						if (response.success && response.configurationValue) {
							searchArea = response.configurationValue;
							$dropdown.trigger('select:item');
						} else {
							searchArea = 'categories';
							$dropdown.trigger('select:item');
						}
					},

					onError: function onError() {
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
	var _refreshSearchArea = function _refreshSearchArea() {
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

	var _initializeInput = function _initializeInput() {

		// Click event
		$this.on('click', function () {
			$this.trigger('refresh:search-area');
			if ($this.val() === '') {
				$this.val(recentSearch);
			}
			$dropdown.trigger('show:dropdown');
			$this.trigger('focus');
		});

		// Keyboard events
		$this.on('keyup', function (event) {
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
		$this.on('perform:search', function () {
			var inputValue = encodeURIComponent($this.val()),
			    openMode = '_self',
			    url;

			switch (searchArea) {
				case 'customers':
					url = ['customers.php', '?search=', inputValue].join('');
					break;
				case 'categories':
					url = ['categories.php', '?search=', inputValue].join('');
					break;
				case 'orders':
					url = ['admin.php', '?', $.param({
						do: 'OrdersOverview',
						filter: {
							number: inputValue
						}
					})].join('');
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
		$this.on('refresh:search-area', function () {
			$this.prop('placeholder', labels[searchArea]);
		});

		// Remove placeholder when input is inactive
		$this.on('blur', function () {
			$this.prop('placeholder', '');
			$dropdown.trigger('hide:dropdown');
		});
	};

	var _initializeButton = function _initializeButton() {
		$button.on('click', function () {
			$this.trigger('refresh:search-area');
			$this.val(recentSearch);
			$dropdown.trigger('show:dropdown');
			$this.trigger('focus');
		});
	};

	var _initializeDropdown = function _initializeDropdown() {
		// Select item
		$dropdown.on('select:item', function () {
			$dropdown.find('li[data-search-area=' + searchArea + ']').addClass('active');
		});

		// Show event
		$dropdown.on('show:dropdown', function () {
			$dropdown.fadeIn();
			$dropdown.trigger('select:item');
			$dropdown.trigger('refresh:search-item');
		});

		// Select first item
		$dropdown.on('select:item:first', function () {
			var $activeListItem = $dropdown.find('li.search-item.active');
			var $firstListItem = $dropdown.find('li.search-item:first');
			$activeListItem.removeClass('active');
			$firstListItem.addClass('active');
			_refreshSearchArea();
			$dropdown.trigger('select:item');
		});

		$dropdown.on('select:item:last', function () {
			var $activeListItem = $dropdown.find('li.search-item.active');
			var $lastListItem = $dropdown.find('li.search-item:last');
			$activeListItem.removeClass('active');
			$lastListItem.addClass('active');
			_refreshSearchArea();
			$dropdown.trigger('select:item');
		});

		// Select previous item event
		$dropdown.on('select:item:previous', function () {
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
		$dropdown.on('select:item:next', function () {
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
		$dropdown.on('hide:dropdown', function () {
			$dropdown.fadeOut();
		});

		// Item click event
		$dropdown.on('click', function (event) {
			event.stopPropagation();

			$dropdown.find('li').removeClass('active');

			var $elementToActivate = $(event.target).is('span') ? $(event.target).parents('li:first') : $(event.target);

			$elementToActivate.addClass('active');

			_refreshSearchArea();
			$dropdown.trigger('hide:dropdown');
			$this.trigger('perform:search');
		});

		// Item search event
		$dropdown.on('refresh:search-item', function () {
			$('.search-item').each(function () {
				// Update search query
				$(this).find('.search-query-item').text($this.val());

				// Update search description
				var searchAreaText = [labels.searchIn, labels[$(this).data('searchArea')]].join(' ');

				$(this).find('.search-query-description').text(searchAreaText);
			});
		});
	};

	var _initializeRecentSearch = function _initializeRecentSearch() {
		$(document).on('JSENGINE_INIT_FINISHED', function () {
			if (recentSearch !== '') {
				$this.prop('value', recentSearch);
				$this.focus();
			}
		});
	};

	/**
  * Initialize method of the extension, called by the engine.
  */
	module.init = function (done) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluX3NlYXJjaC5qcyJdLCJuYW1lcyI6WyJneCIsImV4dGVuc2lvbnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJ1dHRvbiIsImJ1dHRvbiIsIiRkcm9wZG93biIsInJlY2VudFNlYXJjaCIsImRlY29kZVVSSSIsImpzZSIsImxpYnMiLCJ1cmxfYXJndW1lbnRzIiwiZ2V0VXJsUGFyYW1ldGVycyIsImxvY2F0aW9uIiwiaHJlZiIsInNlYXJjaCIsInNlYXJjaEFyZWEiLCJsYWJlbHMiLCJzZWFyY2hJbiIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwib3JkZXJzIiwiY3VzdG9tZXJzIiwiY2F0ZWdvcmllcyIsImtleU1hcCIsIkVTQyIsIkFSUk9XX1VQIiwiQVJST1dfRE9XTiIsIkVOVEVSIiwidXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlIiwidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UiLCJ1cmxBcmd1bWVudHMiLCJjb25maWd1cmF0aW9uQ29udGFpbmVyIiwidXNlcklkIiwiY3VzdG9tZXJfaWQiLCJjb25maWd1cmF0aW9uS2V5IiwiX2luaXRpYWxpemVTZWFyY2hBcmVhIiwiZ2V0Q3VycmVudEZpbGUiLCJ0cmlnZ2VyIiwiZ2V0Iiwib25TdWNjZXNzIiwicmVzcG9uc2UiLCJzdWNjZXNzIiwiY29uZmlndXJhdGlvblZhbHVlIiwib25FcnJvciIsIl9yZWZyZXNoU2VhcmNoQXJlYSIsImxlbmd0aCIsImNvbnNvbGUiLCJlcnJvciIsIl9pbml0aWFsaXplSW5wdXQiLCJvbiIsInZhbCIsImV2ZW50Iiwid2hpY2giLCJpbnB1dFZhbHVlIiwiZW5jb2RlVVJJQ29tcG9uZW50Iiwib3Blbk1vZGUiLCJ1cmwiLCJqb2luIiwicGFyYW0iLCJkbyIsImZpbHRlciIsIm51bWJlciIsInNldCIsImV4dGVuZCIsIndpbmRvdyIsIm9wZW4iLCJwcm9wIiwiX2luaXRpYWxpemVCdXR0b24iLCJfaW5pdGlhbGl6ZURyb3Bkb3duIiwiZmluZCIsImFkZENsYXNzIiwiZmFkZUluIiwiJGFjdGl2ZUxpc3RJdGVtIiwiJGZpcnN0TGlzdEl0ZW0iLCJyZW1vdmVDbGFzcyIsIiRsYXN0TGlzdEl0ZW0iLCIkcHJldiIsInByZXYiLCIkbmV4dCIsIm5leHQiLCJmYWRlT3V0Iiwic3RvcFByb3BhZ2F0aW9uIiwiJGVsZW1lbnRUb0FjdGl2YXRlIiwidGFyZ2V0IiwiaXMiLCJwYXJlbnRzIiwiZWFjaCIsInRleHQiLCJzZWFyY2hBcmVhVGV4dCIsIl9pbml0aWFsaXplUmVjZW50U2VhcmNoIiwiZG9jdW1lbnQiLCJmb2N1cyIsImluaXQiLCJkb25lIiwicmVjZW50U2VhcmNoQXJlYSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7QUFTQUEsR0FBR0MsVUFBSCxDQUFjQyxNQUFkLENBQ0MsY0FERCxFQUdDLENBQUMsNEJBQUQsRUFBK0IsZUFBL0IsQ0FIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7O0FBQ0EsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxVQUFVRCxFQUFFRixLQUFLSSxNQUFQLENBRFg7QUFBQSxLQUVDQyxZQUFZSCxFQUFFLHFCQUFGLENBRmI7QUFBQSxLQUdDSSxlQUFlQyxVQUFVQyxJQUFJQyxJQUFKLENBQVNDLGFBQVQsQ0FBdUJDLGdCQUF2QixDQUF3Q0MsU0FBU0MsSUFBakQsRUFBdURDLE1BQXZELElBQWlFLEVBQTNFLENBSGhCOztBQUtBO0FBQ0EsS0FBSUMsVUFBSjs7QUFFQTtBQUNBLEtBQUlDLFNBQVM7QUFDWkMsWUFBVVQsSUFBSVUsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsdUJBQXhCLEVBQWlELGNBQWpELENBREU7QUFFWkMsVUFBUWIsSUFBSVUsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IscUJBQXhCLEVBQStDLGNBQS9DLENBRkk7QUFHWkUsYUFBV2QsSUFBSVUsSUFBSixDQUFTQyxJQUFU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UCxFQUVFa0IsSUFGRixDQUVPQyxjQUZQO0FBR0EsSUFmRDtBQWdCQSxHQWpCRDtBQWtCQSxFQTVHRDs7QUE4R0EsS0FBSUMsMEJBQTBCLFNBQTFCQSx1QkFBMEIsR0FBVztBQUN4Q3hGLElBQUV5RixRQUFGLEVBQVl6QyxFQUFaLENBQWUsd0JBQWYsRUFBeUMsWUFBVztBQUNuRCxPQUFJNUMsaUJBQWlCLEVBQXJCLEVBQXlCO0FBQ3hCTCxVQUFNa0UsSUFBTixDQUFXLE9BQVgsRUFBb0I3RCxZQUFwQjtBQUNBTCxVQUFNMkYsS0FBTjtBQUNBO0FBQ0QsR0FMRDtBQU1BLEVBUEQ7O0FBU0E7OztBQUdBN0YsUUFBTzhGLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUI3QztBQUNBb0I7QUFDQUQ7QUFDQXNCOztBQUVBM0UsZUFBYWYsS0FBSytGLGdCQUFMLElBQXlCLFlBQXRDO0FBQ0ExRixZQUFVaUMsT0FBVixDQUFrQixhQUFsQjs7QUFFQXdEO0FBQ0EsRUFWRDs7QUFZQTtBQUNBLFFBQU8vRixNQUFQO0FBQ0EsQ0FuV0YiLCJmaWxlIjoiYWRtaW5fc2VhcmNoLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBhZG1pbl9zZWFyY2guanMgMjAxNi0wOS0wOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQWRtaW4gU2VhcmNoIEV4dGVuc2lvblxuICpcbiAqIEV4dGVuc2lvbiBmb3Igc2VhcmNoIGluIG9yZGVycywgY3VzdG9tZXJzIGFuZCBjYXRlZ29yaWVzIGluIHRoZSBhZG1pbiBwYW5lbFxuICpcbiAqIEBtb2R1bGUgQWRtaW4vRXh0ZW5zaW9uL2FkbWluX3NlYXJjaFxuICogQHJlcXVpcmVzIGpRdWVyeVVJXG4gKiBAaWdub3JlXG4gKi9cbmd4LmV4dGVuc2lvbnMubW9kdWxlKFxuXHQnYWRtaW5fc2VhcmNoJyxcblx0XG5cdFsndXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UnLCAndXJsX2FyZ3VtZW50cyddLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8vIEVsZW1lbnRzLlxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHQkYnV0dG9uID0gJChkYXRhLmJ1dHRvbiksXG5cdFx0XHQkZHJvcGRvd24gPSAkKCd1bC5zZWFyY2hhYmxlOmZpcnN0JyksXG5cdFx0XHRyZWNlbnRTZWFyY2ggPSBkZWNvZGVVUkkoanNlLmxpYnMudXJsX2FyZ3VtZW50cy5nZXRVcmxQYXJhbWV0ZXJzKGxvY2F0aW9uLmhyZWYpLnNlYXJjaCB8fCAnJyk7XG5cdFx0XG5cdFx0Ly8gQ3VycmVudCBzZWFyY2ggYXJlYS5cblx0XHR2YXIgc2VhcmNoQXJlYTtcblx0XHRcblx0XHQvLyBUZXh0IGxhYmVscy5cblx0XHR2YXIgbGFiZWxzID0ge1xuXHRcdFx0c2VhcmNoSW46IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdhZG1pbl9zZWFyY2hfaW5fbGFiZWwnLCAnYWRtaW5fbGFiZWxzJyksXG5cdFx0XHRvcmRlcnM6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdhZG1pbl9zZWFyY2hfb3JkZXJzJywgJ2FkbWluX2xhYmVscycpLFxuXHRcdFx0Y3VzdG9tZXJzOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnYWRtaW5fc2VhcmNoX2N1c3RvbWVycycsICdhZG1pbl9sYWJlbHMnKSxcblx0XHRcdGNhdGVnb3JpZXM6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdhZG1pbl9zZWFyY2hfY2F0ZWdvcmllcycsICdhZG1pbl9sYWJlbHMnKVxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gS2V5IGNvZGUgbWFwLlxuXHRcdHZhciBrZXlNYXAgPSB7XG5cdFx0XHRFU0M6IDI3LFxuXHRcdFx0QVJST1dfVVA6IDM4LFxuXHRcdFx0QVJST1dfRE9XTjogNDAsXG5cdFx0XHRFTlRFUjogMTNcblx0XHR9O1xuXHRcdFxuXHRcdC8vIExpYnJhcnkgYWNjZXNzIHNob3J0Y3V0cy5cblx0XHR2YXIgdXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlID0ganNlLmxpYnMudXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UsXG5cdFx0XHR1cmxBcmd1bWVudHMgPSBqc2UubGlicy51cmxfYXJndW1lbnRzO1xuXHRcdFxuXHRcdC8vIENvbmZpZ3VyYXRpb24gc2V0dGluZ3MgZm9yIFVzZXJDb25maWd1cmF0aW9uU2VydmljZS5cblx0XHR2YXIgY29uZmlndXJhdGlvbkNvbnRhaW5lciA9IHtcblx0XHRcdHVzZXJJZDogZGF0YS5jdXN0b21lcl9pZCxcblx0XHRcdGNvbmZpZ3VyYXRpb25LZXk6ICdyZWNlbnRTZWFyY2hBcmVhJ1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gTW9kdWxlIG9iamVjdCAoSlNFbmdpbmUpLlxuXHRcdHZhciBtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBNRVRIT0RTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGV0ZXJtaW5lcyB0aGUgYWN0dWFsIHBhZ2UgdG8gc2V0IHRoZSBzZWFyY2ggYXJlYSB2YXJpYWJsZVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9pbml0aWFsaXplU2VhcmNoQXJlYSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0c3dpdGNoICh1cmxBcmd1bWVudHMuZ2V0Q3VycmVudEZpbGUoKSkge1xuXHRcdFx0XHRjYXNlICdvcmRlcnMucGhwJzpcblx0XHRcdFx0XHRzZWFyY2hBcmVhID0gJ29yZGVycyc7XG5cdFx0XHRcdFx0JGRyb3Bkb3duLnRyaWdnZXIoJ3NlbGVjdDppdGVtJyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ2N1c3RvbWVycy5waHAnOlxuXHRcdFx0XHRcdHNlYXJjaEFyZWEgPSAnY3VzdG9tZXJzJztcblx0XHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcignc2VsZWN0Oml0ZW0nKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnY2F0ZWdvcmllcy5waHAnOlxuXHRcdFx0XHRcdHNlYXJjaEFyZWEgPSAnY2F0ZWdvcmllcyc7XG5cdFx0XHRcdFx0JGRyb3Bkb3duLnRyaWdnZXIoJ3NlbGVjdDppdGVtJyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0dXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlLmdldCh7XG5cdFx0XHRcdFx0XHRkYXRhOiBjb25maWd1cmF0aW9uQ29udGFpbmVyLFxuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRvblN1Y2Nlc3M6IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdGlmIChyZXNwb25zZS5zdWNjZXNzICYmIHJlc3BvbnNlLmNvbmZpZ3VyYXRpb25WYWx1ZSkge1xuXHRcdFx0XHRcdFx0XHRcdHNlYXJjaEFyZWEgPSByZXNwb25zZS5jb25maWd1cmF0aW9uVmFsdWU7XG5cdFx0XHRcdFx0XHRcdFx0JGRyb3Bkb3duLnRyaWdnZXIoJ3NlbGVjdDppdGVtJyk7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0ZWxzZSB7XG5cdFx0XHRcdFx0XHRcdFx0c2VhcmNoQXJlYSA9ICdjYXRlZ29yaWVzJztcblx0XHRcdFx0XHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcignc2VsZWN0Oml0ZW0nKTtcblx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0b25FcnJvcjogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdHNlYXJjaEFyZWEgPSAnY2F0ZWdvcmllcyc7XG5cdFx0XHRcdFx0XHRcdCRkcm9wZG93bi50cmlnZ2VyKCdzZWxlY3Q6aXRlbScpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUmVmcmVzaGVzIHRoZSBzZWFyY2ggYXJlYSB2YXJpYWJsZVxuXHRcdCAqXG5cdFx0ICogU2hvd3MgdGhlIG5ldyBzZWFyY2ggYXJlYSBpbiB0aGUgYnV0dG9uXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3JlZnJlc2hTZWFyY2hBcmVhID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQvLyBBYm9ydCBpZiBubyBuZXcgc2VhcmNoIGFyZWEgaXMgcHJvdmlkZWRcblx0XHRcdGlmICghJCgnLnNlYXJjaC1pdGVtLmFjdGl2ZScpLmxlbmd0aCkge1xuXHRcdFx0XHRjb25zb2xlLmVycm9yKCdObyBhY3RpdmUgbGlzdCBpdGVtIScpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBBc3NpZ24gbmV3IHNlYXJjaCBhcmVhXG5cdFx0XHRzZWFyY2hBcmVhID0gJCgnLnNlYXJjaC1pdGVtLmFjdGl2ZScpLmRhdGEoJ3NlYXJjaEFyZWEnKTtcblx0XHRcdCR0aGlzLnRyaWdnZXIoJ3JlZnJlc2g6c2VhcmNoLWFyZWEnKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9pbml0aWFsaXplSW5wdXQgPSBmdW5jdGlvbigpIHtcblx0XHRcdFxuXHRcdFx0Ly8gQ2xpY2sgZXZlbnRcblx0XHRcdCR0aGlzLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkdGhpcy50cmlnZ2VyKCdyZWZyZXNoOnNlYXJjaC1hcmVhJyk7XG5cdFx0XHRcdGlmKCR0aGlzLnZhbCgpID09PSAnJylcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCR0aGlzLnZhbChyZWNlbnRTZWFyY2gpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdCRkcm9wZG93bi50cmlnZ2VyKCdzaG93OmRyb3Bkb3duJyk7XG5cdFx0XHRcdCR0aGlzLnRyaWdnZXIoJ2ZvY3VzJyk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gS2V5Ym9hcmQgZXZlbnRzXG5cdFx0XHQkdGhpcy5vbigna2V5dXAnLCBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0XHRzd2l0Y2ggKGV2ZW50LndoaWNoKSB7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gUGVyZm9ybSBzZWFyY2ggaWYgZW50ZXIga2V5IGlzIHByZXNzZWRcblx0XHRcdFx0XHRjYXNlIGtleU1hcC5FTlRFUjpcblx0XHRcdFx0XHRcdCR0aGlzLnRyaWdnZXIoJ3BlcmZvcm06c2VhcmNoJyk7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBDbG9zZSBkcm9wZG93biBpZiBlc2NhcGUga2V5IGlzIHByZXNzZWRcblx0XHRcdFx0XHRjYXNlIGtleU1hcC5FU0M6XG5cdFx0XHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcignaGlkZTpkcm9wZG93bicpO1xuXHRcdFx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIE5hdmlnYXRlIHVwIGluIGRyb3Bkb3duXG5cdFx0XHRcdFx0Y2FzZSBrZXlNYXAuQVJST1dfVVA6XG5cdFx0XHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcignc2VsZWN0Oml0ZW06cHJldmlvdXMnKTtcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdGNhc2Uga2V5TWFwLkFSUk9XX0RPV046XG5cdFx0XHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcignc2VsZWN0Oml0ZW06bmV4dCcpO1xuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdH1cblx0XHRcdFx0JGRyb3Bkb3duLnRyaWdnZXIoJ3JlZnJlc2g6c2VhcmNoLWl0ZW0nKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBTZWFyY2ggZXZlbnRzXG5cdFx0XHQkdGhpcy5vbigncGVyZm9ybTpzZWFyY2gnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyIGlucHV0VmFsdWUgPSBlbmNvZGVVUklDb21wb25lbnQoJHRoaXMudmFsKCkpLFxuXHRcdFx0XHRcdG9wZW5Nb2RlID0gJ19zZWxmJyxcblx0XHRcdFx0XHR1cmw7XG5cdFx0XHRcdFxuXHRcdFx0XHRzd2l0Y2ggKHNlYXJjaEFyZWEpIHtcblx0XHRcdFx0XHRjYXNlICdjdXN0b21lcnMnOlxuXHRcdFx0XHRcdFx0dXJsID0gW1xuXHRcdFx0XHRcdFx0XHQnY3VzdG9tZXJzLnBocCcsXG5cdFx0XHRcdFx0XHRcdCc/c2VhcmNoPScsXG5cdFx0XHRcdFx0XHRcdGlucHV0VmFsdWVcblx0XHRcdFx0XHRcdF0uam9pbignJyk7XG5cdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRjYXNlICdjYXRlZ29yaWVzJzpcblx0XHRcdFx0XHRcdHVybCA9IFtcblx0XHRcdFx0XHRcdFx0J2NhdGVnb3JpZXMucGhwJyxcblx0XHRcdFx0XHRcdFx0Jz9zZWFyY2g9Jyxcblx0XHRcdFx0XHRcdFx0aW5wdXRWYWx1ZVxuXHRcdFx0XHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdGNhc2UgJ29yZGVycyc6XG5cdFx0XHRcdFx0XHR1cmwgPSBbXG5cdFx0XHRcdFx0XHRcdCdhZG1pbi5waHAnLFxuXHRcdFx0XHRcdFx0XHQnPycsXG5cdFx0XHRcdFx0XHRcdCQucGFyYW0oe1xuXHRcdFx0XHRcdFx0XHRcdGRvOiAnT3JkZXJzT3ZlcnZpZXcnLFxuXHRcdFx0XHRcdFx0XHRcdGZpbHRlcjoge1xuXHRcdFx0XHRcdFx0XHRcdFx0bnVtYmVyOiBpbnB1dFZhbHVlXG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHR1c2VyQ29uZmlndXJhdGlvblNlcnZpY2Uuc2V0KHtcblx0XHRcdFx0XHRkYXRhOiAkLmV4dGVuZChjb25maWd1cmF0aW9uQ29udGFpbmVyLCB7XG5cdFx0XHRcdFx0XHRjb25maWd1cmF0aW9uVmFsdWU6IHNlYXJjaEFyZWFcblx0XHRcdFx0XHR9KVxuXHRcdFx0XHR9KTtcblx0XHRcdFx0d2luZG93Lm9wZW4odXJsLCBvcGVuTW9kZSk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gQ2hhbmdlIHNlYXJjaCBhcmVhIGV2ZW50XG5cdFx0XHQkdGhpcy5vbigncmVmcmVzaDpzZWFyY2gtYXJlYScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkdGhpcy5wcm9wKCdwbGFjZWhvbGRlcicsIGxhYmVsc1tzZWFyY2hBcmVhXSk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gUmVtb3ZlIHBsYWNlaG9sZGVyIHdoZW4gaW5wdXQgaXMgaW5hY3RpdmVcblx0XHRcdCR0aGlzLm9uKCdibHVyJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCR0aGlzLnByb3AoJ3BsYWNlaG9sZGVyJywgJycpO1xuXHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcignaGlkZTpkcm9wZG93bicpO1xuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2luaXRpYWxpemVCdXR0b24gPSBmdW5jdGlvbigpIHtcblx0XHRcdCRidXR0b24ub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCR0aGlzLnRyaWdnZXIoJ3JlZnJlc2g6c2VhcmNoLWFyZWEnKTtcblx0XHRcdFx0JHRoaXMudmFsKHJlY2VudFNlYXJjaCk7XG5cdFx0XHRcdCRkcm9wZG93bi50cmlnZ2VyKCdzaG93OmRyb3Bkb3duJyk7XG5cdFx0XHRcdCR0aGlzLnRyaWdnZXIoJ2ZvY3VzJyk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfaW5pdGlhbGl6ZURyb3Bkb3duID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQvLyBTZWxlY3QgaXRlbVxuXHRcdFx0JGRyb3Bkb3duLm9uKCdzZWxlY3Q6aXRlbScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkZHJvcGRvd25cblx0XHRcdFx0XHQuZmluZCgnbGlbZGF0YS1zZWFyY2gtYXJlYT0nICsgc2VhcmNoQXJlYSArICddJylcblx0XHRcdFx0XHQuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIFNob3cgZXZlbnRcblx0XHRcdCRkcm9wZG93bi5vbignc2hvdzpkcm9wZG93bicsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkZHJvcGRvd24uZmFkZUluKCk7XG5cdFx0XHRcdCRkcm9wZG93bi50cmlnZ2VyKCdzZWxlY3Q6aXRlbScpO1xuXHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcigncmVmcmVzaDpzZWFyY2gtaXRlbScpO1xuXHRcdFx0XHRcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBTZWxlY3QgZmlyc3QgaXRlbVxuXHRcdFx0JGRyb3Bkb3duLm9uKCdzZWxlY3Q6aXRlbTpmaXJzdCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgJGFjdGl2ZUxpc3RJdGVtID0gJGRyb3Bkb3duLmZpbmQoJ2xpLnNlYXJjaC1pdGVtLmFjdGl2ZScpO1xuXHRcdFx0XHR2YXIgJGZpcnN0TGlzdEl0ZW0gPSAkZHJvcGRvd24uZmluZCgnbGkuc2VhcmNoLWl0ZW06Zmlyc3QnKTtcblx0XHRcdFx0JGFjdGl2ZUxpc3RJdGVtLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFx0JGZpcnN0TGlzdEl0ZW0uYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0XHRfcmVmcmVzaFNlYXJjaEFyZWEoKTtcblx0XHRcdFx0JGRyb3Bkb3duLnRyaWdnZXIoJ3NlbGVjdDppdGVtJyk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0JGRyb3Bkb3duLm9uKCdzZWxlY3Q6aXRlbTpsYXN0JywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciAkYWN0aXZlTGlzdEl0ZW0gPSAkZHJvcGRvd24uZmluZCgnbGkuc2VhcmNoLWl0ZW0uYWN0aXZlJyk7XG5cdFx0XHRcdHZhciAkbGFzdExpc3RJdGVtID0gJGRyb3Bkb3duLmZpbmQoJ2xpLnNlYXJjaC1pdGVtOmxhc3QnKTtcblx0XHRcdFx0JGFjdGl2ZUxpc3RJdGVtLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFx0JGxhc3RMaXN0SXRlbS5hZGRDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHRcdF9yZWZyZXNoU2VhcmNoQXJlYSgpO1xuXHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcignc2VsZWN0Oml0ZW0nKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBTZWxlY3QgcHJldmlvdXMgaXRlbSBldmVudFxuXHRcdFx0JGRyb3Bkb3duLm9uKCdzZWxlY3Q6aXRlbTpwcmV2aW91cycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgJGFjdGl2ZUxpc3RJdGVtID0gJGRyb3Bkb3duLmZpbmQoJ2xpLnNlYXJjaC1pdGVtLmFjdGl2ZScpO1xuXHRcdFx0XHR2YXIgJHByZXYgPSAkYWN0aXZlTGlzdEl0ZW0ucHJldigpO1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKCRwcmV2Lmxlbmd0aCkge1xuXHRcdFx0XHRcdCRhY3RpdmVMaXN0SXRlbS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHRcdFx0JHByZXYuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xuXHRcdFx0XHRcdF9yZWZyZXNoU2VhcmNoQXJlYSgpO1xuXHRcdFx0XHRcdCRkcm9wZG93bi50cmlnZ2VyKCdzZWxlY3Q6aXRlbScpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCRkcm9wZG93bi50cmlnZ2VyKCdzZWxlY3Q6aXRlbTpsYXN0Jyk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBTZWxlY3QgcHJldmlvdXMgaXRlbSBldmVudFxuXHRcdFx0JGRyb3Bkb3duLm9uKCdzZWxlY3Q6aXRlbTpuZXh0JywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciAkYWN0aXZlTGlzdEl0ZW0gPSAkZHJvcGRvd24uZmluZCgnbGkuc2VhcmNoLWl0ZW0uYWN0aXZlJyk7XG5cdFx0XHRcdHZhciAkbmV4dCA9ICRhY3RpdmVMaXN0SXRlbS5uZXh0KCk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoJG5leHQubGVuZ3RoKSB7XG5cdFx0XHRcdFx0JGFjdGl2ZUxpc3RJdGVtLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFx0XHQkbmV4dC5hZGRDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHRcdFx0X3JlZnJlc2hTZWFyY2hBcmVhKCk7XG5cdFx0XHRcdFx0JGRyb3Bkb3duLnRyaWdnZXIoJ3NlbGVjdDppdGVtJyk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0JGRyb3Bkb3duLnRyaWdnZXIoJ3NlbGVjdDppdGVtOmZpcnN0Jyk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBIaWRlIGV2ZW50XG5cdFx0XHQkZHJvcGRvd24ub24oJ2hpZGU6ZHJvcGRvd24nLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JGRyb3Bkb3duLmZhZGVPdXQoKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBJdGVtIGNsaWNrIGV2ZW50XG5cdFx0XHQkZHJvcGRvd24ub24oJ2NsaWNrJywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkZHJvcGRvd25cblx0XHRcdFx0XHQuZmluZCgnbGknKVxuXHRcdFx0XHRcdC5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHR2YXIgJGVsZW1lbnRUb0FjdGl2YXRlID0gJChldmVudC50YXJnZXQpLmlzKCdzcGFuJykgP1xuXHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgJChldmVudC50YXJnZXQpLnBhcmVudHMoJ2xpOmZpcnN0JykgOlxuXHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgJChldmVudC50YXJnZXQpO1xuXHRcdFx0XHRcblx0XHRcdFx0JGVsZW1lbnRUb0FjdGl2YXRlLmFkZENsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFx0XG5cdFx0XHRcdF9yZWZyZXNoU2VhcmNoQXJlYSgpO1xuXHRcdFx0XHQkZHJvcGRvd24udHJpZ2dlcignaGlkZTpkcm9wZG93bicpO1xuXHRcdFx0XHQkdGhpcy50cmlnZ2VyKCdwZXJmb3JtOnNlYXJjaCcpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIEl0ZW0gc2VhcmNoIGV2ZW50XG5cdFx0XHQkZHJvcGRvd24ub24oJ3JlZnJlc2g6c2VhcmNoLWl0ZW0nLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JCgnLnNlYXJjaC1pdGVtJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQvLyBVcGRhdGUgc2VhcmNoIHF1ZXJ5XG5cdFx0XHRcdFx0JCh0aGlzKVxuXHRcdFx0XHRcdFx0LmZpbmQoJy5zZWFyY2gtcXVlcnktaXRlbScpXG5cdFx0XHRcdFx0XHQudGV4dCgkdGhpcy52YWwoKSk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gVXBkYXRlIHNlYXJjaCBkZXNjcmlwdGlvblxuXHRcdFx0XHRcdHZhciBzZWFyY2hBcmVhVGV4dCA9IFtcblx0XHRcdFx0XHRcdGxhYmVscy5zZWFyY2hJbixcblx0XHRcdFx0XHRcdGxhYmVsc1skKHRoaXMpLmRhdGEoJ3NlYXJjaEFyZWEnKV1cblx0XHRcdFx0XHRdLmpvaW4oJyAnKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkKHRoaXMpXG5cdFx0XHRcdFx0XHQuZmluZCgnLnNlYXJjaC1xdWVyeS1kZXNjcmlwdGlvbicpXG5cdFx0XHRcdFx0XHQudGV4dChzZWFyY2hBcmVhVGV4dCk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2luaXRpYWxpemVSZWNlbnRTZWFyY2ggPSBmdW5jdGlvbigpIHtcblx0XHRcdCQoZG9jdW1lbnQpLm9uKCdKU0VOR0lORV9JTklUX0ZJTklTSEVEJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmIChyZWNlbnRTZWFyY2ggIT09ICcnKSB7XG5cdFx0XHRcdFx0JHRoaXMucHJvcCgndmFsdWUnLCByZWNlbnRTZWFyY2gpO1xuXHRcdFx0XHRcdCR0aGlzLmZvY3VzKCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIGV4dGVuc2lvbiwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRfaW5pdGlhbGl6ZUlucHV0KCk7XG5cdFx0XHRfaW5pdGlhbGl6ZURyb3Bkb3duKCk7XG5cdFx0XHRfaW5pdGlhbGl6ZUJ1dHRvbigpO1xuXHRcdFx0X2luaXRpYWxpemVSZWNlbnRTZWFyY2goKTtcblx0XHRcdFxuXHRcdFx0c2VhcmNoQXJlYSA9IGRhdGEucmVjZW50U2VhcmNoQXJlYSB8fCAnY2F0ZWdvcmllcyc7XG5cdFx0XHQkZHJvcGRvd24udHJpZ2dlcignc2VsZWN0Oml0ZW0nKTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
