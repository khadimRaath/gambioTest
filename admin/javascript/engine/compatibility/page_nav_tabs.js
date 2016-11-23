/* --------------------------------------------------------------
 page_nav_tabs.js 2016-07-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Page Navigation Tabs
 *
 * This module will convert old table-style navigation to the new theme navigation tabs for
 * every page. It searches for specific HTML patterns and creates new markup for the page
 * navigation.
 *
 * **Important!** If you need to exclude an old navigation table from being converted you must add
 * the "exclude-page-nav" class to its table tag as in the following example.
 *
 * ```html
 * <table class="exclude-page-nav"> ... </table>
 * ```
 *
 * @module Compatibility/page_nav_tabs
 */
gx.compatibility.module(
	'page_nav_tabs',
	
	[],
	
	/**  @lends module:Compatibility/page_nav_tabs */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {
				EXCLUDE_CLASS: 'exclude-page-nav',
				CONVERT_CLASS: 'convert-to-tabs'
			},
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {},
			
			/**
			 * The original position of the tab navigation
			 *
			 * @type int
			 */
			originalPosition = 0,
			
			/**
			 * The last scroll position
			 *
			 * @type int
			 */
			scrollPosition = $(window).scrollTop(),
			
			/**
			 * The original left position of the pageHeading
			 *
			 * @type {number}
			 */
			originalLeft = 0,
			
			/**
			 * Tells if the tab navigation is within the view port
			 *
			 * @type boolean
			 */
			isOut = true;
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		/**
		 * Change the first .pageHeading HTML to contain the ".page-nav-title" class.
		 */
		var _fixPageHeading = function() {
			var $pageHeading = $('.pageHeading:first-child');
			$pageHeading.html('<div class="page-nav-title">' + $pageHeading.html() + '</div>');
			
			$pageHeading.wrap('<div class="pageHeadingWrapper"></div>');
			$('.pageHeadingWrapper').height($pageHeading.height() + 1);
			originalLeft = $('.pageHeading').length ? $('.pageHeading').offset().left : 0;
		};
		
		/**
		 * Checks if the page has the old table-style navigation system.
		 *
		 * @return {string} Returns Returns the selector that matches the string or null if none was found.
		 */
		var _detectHtmlPattern = function() {
			var patterns = [
					'.main table .dataTableHeadingRow .dataTableHeadingContentText a',
					'.pdf_menu tr td.dataTableHeadingContent',
					'.boxCenter table tr td.dataTableHeadingContent a'
				],
				selector = null;
			
			$.each(patterns, function() {
				if ($this.find(this).length > 0 && !$this.find(this).closest('table').hasClass(options.EXCLUDE_CLASS)) {
					selector = this;
					return false; // exit loop
				}
			});
			
			return selector;
		};
		
		/**
		 * Performs the conversion of the old style to the new navigation HTML.
		 *
		 * It will also hide the old navigation markup. Styling for the new HTML is located in the
		 * "_compatibility.scss".
		 *
		 * @param {string} selector The selector string to be used for selecting the old table td cells.
		 */
		var _convertNavigationTabs = function(selector) {
			var $selector = $this.find(selector),
				$table = $selector.closest('table'),
				$pageHeading = $('.pageHeading:first-child'),
				$pageNavTabs = $('<div class="page-nav-tabs"></div>');
			
			$table.find('tr td').each(function() {
				var $navTab = $('<div class="nav-tab">' + $(this).html() + '</div>');
				
				// Style current page tabs.
				if ($navTab.find('a').length === 0) {
					$navTab.addClass('no-link');
				}
				
				$navTab.appendTo($pageNavTabs);
			});
			
			$pageNavTabs.appendTo($pageHeading);
			
			$table.hide();
		};
		
		/**
		 * Quick Return Check
		 *
		 * Reset the page navigation frame to the original position if the user scrolls directly
		 * to top.
		 */
		var _quickReturn = function() {
			var newScrollPosition = $(window).scrollTop();
			var isScrollDown = scrollPosition - newScrollPosition < 0;
			var isScrollUp = scrollPosition - newScrollPosition > 0;
			originalPosition = $('.main-top-header').height();
			_setPageHeadingLeftAbsolute();
			
			var scrolledToTop = _checkScrolledToTop();
			
			if (!scrolledToTop) {
				if (isScrollDown && !isScrollUp && !isOut) {
					_rollOut();
				}
				else if (!isScrollDown && isScrollUp && isOut) {
					_rollIn();
				}
			}
			
			scrollPosition = newScrollPosition;
		};
		
		/**
		 * Roll-in Animation Function
		 */
		var _rollIn = function() {
			isOut = false;
			$('.pageHeading').css({
				top: '0px',
				position: 'fixed'
			});
			
			$('.pageHeading').animate(
				{
					top: originalPosition + 'px'
				},
				{
					complete: _checkScrolledToTop
				},
				'fast');
		};
		
		/**
		 * Sets the left position of the pageHeading absolute
		 */
		var _setPageHeadingLeftAbsolute = function() {
			var contentHeaderLeft = originalLeft - $(window).scrollLeft();
			var menuWidth = $('.columnLeft2').outerWidth(); 
			$('.pageHeading').css('left', (contentHeaderLeft < menuWidth ? menuWidth : contentHeaderLeft) + 'px');
		};
		
		/**
		 * Roll-out Animation Function
		 */
		var _rollOut = function() {
			isOut = true;
			$('.pageHeading').animate({
				top: '0px'
			}, 'fast', 'swing', function() {
				$('.pageHeading').css({
					top: originalPosition + 'px',
					position: 'static'
				});
			});
		};
		
		/**
		 * Check if user has scrolled to top of the page.
		 *
		 * @returns {bool} Returns the check result.
		 */
		var _checkScrolledToTop = function() {
			if ($(window).scrollTop() === 0) {
				$('.pageHeading').css({
					top: originalPosition + 'px',
					position: 'static'
				});
				
				return true;
			}
			
			return false;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			setTimeout(function() {
				_fixPageHeading(); // must be executed for every page
				
				// Convert only the pages that have a recognizable table navigation style.
				var selector = _detectHtmlPattern();
				
				if (selector !== null) {
					_convertNavigationTabs(selector);
				}
				
				$(window).on('scroll', _quickReturn);
				
				// Set height for parent element of the page heading bar to avoid that the main content moves up when
				// the heading bar switches into sticky mode
				$('.pageHeading').parent().height($('.pageHeading').parent().height());
				
				done();
			}, 300);
		};
		
		return module;
	});
