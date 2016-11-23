'use strict';

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
gx.compatibility.module('page_nav_tabs', [],

/**  @lends module:Compatibility/page_nav_tabs */

function (data) {

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
	var _fixPageHeading = function _fixPageHeading() {
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
	var _detectHtmlPattern = function _detectHtmlPattern() {
		var patterns = ['.main table .dataTableHeadingRow .dataTableHeadingContentText a', '.pdf_menu tr td.dataTableHeadingContent', '.boxCenter table tr td.dataTableHeadingContent a'],
		    selector = null;

		$.each(patterns, function () {
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
	var _convertNavigationTabs = function _convertNavigationTabs(selector) {
		var $selector = $this.find(selector),
		    $table = $selector.closest('table'),
		    $pageHeading = $('.pageHeading:first-child'),
		    $pageNavTabs = $('<div class="page-nav-tabs"></div>');

		$table.find('tr td').each(function () {
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
	var _quickReturn = function _quickReturn() {
		var newScrollPosition = $(window).scrollTop();
		var isScrollDown = scrollPosition - newScrollPosition < 0;
		var isScrollUp = scrollPosition - newScrollPosition > 0;
		originalPosition = $('.main-top-header').height();
		_setPageHeadingLeftAbsolute();

		var scrolledToTop = _checkScrolledToTop();

		if (!scrolledToTop) {
			if (isScrollDown && !isScrollUp && !isOut) {
				_rollOut();
			} else if (!isScrollDown && isScrollUp && isOut) {
				_rollIn();
			}
		}

		scrollPosition = newScrollPosition;
	};

	/**
  * Roll-in Animation Function
  */
	var _rollIn = function _rollIn() {
		isOut = false;
		$('.pageHeading').css({
			top: '0px',
			position: 'fixed'
		});

		$('.pageHeading').animate({
			top: originalPosition + 'px'
		}, {
			complete: _checkScrolledToTop
		}, 'fast');
	};

	/**
  * Sets the left position of the pageHeading absolute
  */
	var _setPageHeadingLeftAbsolute = function _setPageHeadingLeftAbsolute() {
		var contentHeaderLeft = originalLeft - $(window).scrollLeft();
		var menuWidth = $('.columnLeft2').outerWidth();
		$('.pageHeading').css('left', (contentHeaderLeft < menuWidth ? menuWidth : contentHeaderLeft) + 'px');
	};

	/**
  * Roll-out Animation Function
  */
	var _rollOut = function _rollOut() {
		isOut = true;
		$('.pageHeading').animate({
			top: '0px'
		}, 'fast', 'swing', function () {
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
	var _checkScrolledToTop = function _checkScrolledToTop() {
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

	module.init = function (done) {
		setTimeout(function () {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInBhZ2VfbmF2X3RhYnMuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwiRVhDTFVERV9DTEFTUyIsIkNPTlZFUlRfQ0xBU1MiLCJvcHRpb25zIiwiZXh0ZW5kIiwib3JpZ2luYWxQb3NpdGlvbiIsInNjcm9sbFBvc2l0aW9uIiwid2luZG93Iiwic2Nyb2xsVG9wIiwib3JpZ2luYWxMZWZ0IiwiaXNPdXQiLCJfZml4UGFnZUhlYWRpbmciLCIkcGFnZUhlYWRpbmciLCJodG1sIiwid3JhcCIsImhlaWdodCIsImxlbmd0aCIsIm9mZnNldCIsImxlZnQiLCJfZGV0ZWN0SHRtbFBhdHRlcm4iLCJwYXR0ZXJucyIsInNlbGVjdG9yIiwiZWFjaCIsImZpbmQiLCJjbG9zZXN0IiwiaGFzQ2xhc3MiLCJfY29udmVydE5hdmlnYXRpb25UYWJzIiwiJHNlbGVjdG9yIiwiJHRhYmxlIiwiJHBhZ2VOYXZUYWJzIiwiJG5hdlRhYiIsImFkZENsYXNzIiwiYXBwZW5kVG8iLCJoaWRlIiwiX3F1aWNrUmV0dXJuIiwibmV3U2Nyb2xsUG9zaXRpb24iLCJpc1Njcm9sbERvd24iLCJpc1Njcm9sbFVwIiwiX3NldFBhZ2VIZWFkaW5nTGVmdEFic29sdXRlIiwic2Nyb2xsZWRUb1RvcCIsIl9jaGVja1Njcm9sbGVkVG9Ub3AiLCJfcm9sbE91dCIsIl9yb2xsSW4iLCJjc3MiLCJ0b3AiLCJwb3NpdGlvbiIsImFuaW1hdGUiLCJjb21wbGV0ZSIsImNvbnRlbnRIZWFkZXJMZWZ0Iiwic2Nyb2xsTGVmdCIsIm1lbnVXaWR0aCIsIm91dGVyV2lkdGgiLCJpbml0IiwiZG9uZSIsInNldFRpbWVvdXQiLCJvbiIsInBhcmVudCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7O0FBZ0JBQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLGVBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXO0FBQ1ZDLGlCQUFlLGtCQURMO0FBRVZDLGlCQUFlO0FBRkwsRUFiWjs7O0FBa0JDOzs7OztBQUtBQyxXQUFVSixFQUFFSyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJKLFFBQW5CLEVBQTZCSCxJQUE3QixDQXZCWDs7O0FBeUJDOzs7OztBQUtBRCxVQUFTLEVBOUJWOzs7QUFnQ0M7Ozs7O0FBS0FTLG9CQUFtQixDQXJDcEI7OztBQXVDQzs7Ozs7QUFLQUMsa0JBQWlCUCxFQUFFUSxNQUFGLEVBQVVDLFNBQVYsRUE1Q2xCOzs7QUE4Q0M7Ozs7O0FBS0FDLGdCQUFlLENBbkRoQjs7O0FBcURDOzs7OztBQUtBQyxTQUFRLElBMURUOztBQTREQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBLEtBQUlDLGtCQUFrQixTQUFsQkEsZUFBa0IsR0FBVztBQUNoQyxNQUFJQyxlQUFlYixFQUFFLDBCQUFGLENBQW5CO0FBQ0FhLGVBQWFDLElBQWIsQ0FBa0IsaUNBQWlDRCxhQUFhQyxJQUFiLEVBQWpDLEdBQXVELFFBQXpFOztBQUVBRCxlQUFhRSxJQUFiLENBQWtCLHdDQUFsQjtBQUNBZixJQUFFLHFCQUFGLEVBQXlCZ0IsTUFBekIsQ0FBZ0NILGFBQWFHLE1BQWIsS0FBd0IsQ0FBeEQ7QUFDQU4saUJBQWVWLEVBQUUsY0FBRixFQUFrQmlCLE1BQWxCLEdBQTJCakIsRUFBRSxjQUFGLEVBQWtCa0IsTUFBbEIsR0FBMkJDLElBQXRELEdBQTZELENBQTVFO0FBQ0EsRUFQRDs7QUFTQTs7Ozs7QUFLQSxLQUFJQyxxQkFBcUIsU0FBckJBLGtCQUFxQixHQUFXO0FBQ25DLE1BQUlDLFdBQVcsQ0FDYixpRUFEYSxFQUViLHlDQUZhLEVBR2Isa0RBSGEsQ0FBZjtBQUFBLE1BS0NDLFdBQVcsSUFMWjs7QUFPQXRCLElBQUV1QixJQUFGLENBQU9GLFFBQVAsRUFBaUIsWUFBVztBQUMzQixPQUFJdEIsTUFBTXlCLElBQU4sQ0FBVyxJQUFYLEVBQWlCUCxNQUFqQixHQUEwQixDQUExQixJQUErQixDQUFDbEIsTUFBTXlCLElBQU4sQ0FBVyxJQUFYLEVBQWlCQyxPQUFqQixDQUF5QixPQUF6QixFQUFrQ0MsUUFBbEMsQ0FBMkN0QixRQUFRRixhQUFuRCxDQUFwQyxFQUF1RztBQUN0R29CLGVBQVcsSUFBWDtBQUNBLFdBQU8sS0FBUCxDQUZzRyxDQUV4RjtBQUNkO0FBQ0QsR0FMRDs7QUFPQSxTQUFPQSxRQUFQO0FBQ0EsRUFoQkQ7O0FBa0JBOzs7Ozs7OztBQVFBLEtBQUlLLHlCQUF5QixTQUF6QkEsc0JBQXlCLENBQVNMLFFBQVQsRUFBbUI7QUFDL0MsTUFBSU0sWUFBWTdCLE1BQU15QixJQUFOLENBQVdGLFFBQVgsQ0FBaEI7QUFBQSxNQUNDTyxTQUFTRCxVQUFVSCxPQUFWLENBQWtCLE9BQWxCLENBRFY7QUFBQSxNQUVDWixlQUFlYixFQUFFLDBCQUFGLENBRmhCO0FBQUEsTUFHQzhCLGVBQWU5QixFQUFFLG1DQUFGLENBSGhCOztBQUtBNkIsU0FBT0wsSUFBUCxDQUFZLE9BQVosRUFBcUJELElBQXJCLENBQTBCLFlBQVc7QUFDcEMsT0FBSVEsVUFBVS9CLEVBQUUsMEJBQTBCQSxFQUFFLElBQUYsRUFBUWMsSUFBUixFQUExQixHQUEyQyxRQUE3QyxDQUFkOztBQUVBO0FBQ0EsT0FBSWlCLFFBQVFQLElBQVIsQ0FBYSxHQUFiLEVBQWtCUCxNQUFsQixLQUE2QixDQUFqQyxFQUFvQztBQUNuQ2MsWUFBUUMsUUFBUixDQUFpQixTQUFqQjtBQUNBOztBQUVERCxXQUFRRSxRQUFSLENBQWlCSCxZQUFqQjtBQUNBLEdBVEQ7O0FBV0FBLGVBQWFHLFFBQWIsQ0FBc0JwQixZQUF0Qjs7QUFFQWdCLFNBQU9LLElBQVA7QUFDQSxFQXBCRDs7QUFzQkE7Ozs7OztBQU1BLEtBQUlDLGVBQWUsU0FBZkEsWUFBZSxHQUFXO0FBQzdCLE1BQUlDLG9CQUFvQnBDLEVBQUVRLE1BQUYsRUFBVUMsU0FBVixFQUF4QjtBQUNBLE1BQUk0QixlQUFlOUIsaUJBQWlCNkIsaUJBQWpCLEdBQXFDLENBQXhEO0FBQ0EsTUFBSUUsYUFBYS9CLGlCQUFpQjZCLGlCQUFqQixHQUFxQyxDQUF0RDtBQUNBOUIscUJBQW1CTixFQUFFLGtCQUFGLEVBQXNCZ0IsTUFBdEIsRUFBbkI7QUFDQXVCOztBQUVBLE1BQUlDLGdCQUFnQkMscUJBQXBCOztBQUVBLE1BQUksQ0FBQ0QsYUFBTCxFQUFvQjtBQUNuQixPQUFJSCxnQkFBZ0IsQ0FBQ0MsVUFBakIsSUFBK0IsQ0FBQzNCLEtBQXBDLEVBQTJDO0FBQzFDK0I7QUFDQSxJQUZELE1BR0ssSUFBSSxDQUFDTCxZQUFELElBQWlCQyxVQUFqQixJQUErQjNCLEtBQW5DLEVBQTBDO0FBQzlDZ0M7QUFDQTtBQUNEOztBQUVEcEMsbUJBQWlCNkIsaUJBQWpCO0FBQ0EsRUFuQkQ7O0FBcUJBOzs7QUFHQSxLQUFJTyxVQUFVLFNBQVZBLE9BQVUsR0FBVztBQUN4QmhDLFVBQVEsS0FBUjtBQUNBWCxJQUFFLGNBQUYsRUFBa0I0QyxHQUFsQixDQUFzQjtBQUNyQkMsUUFBSyxLQURnQjtBQUVyQkMsYUFBVTtBQUZXLEdBQXRCOztBQUtBOUMsSUFBRSxjQUFGLEVBQWtCK0MsT0FBbEIsQ0FDQztBQUNDRixRQUFLdkMsbUJBQW1CO0FBRHpCLEdBREQsRUFJQztBQUNDMEMsYUFBVVA7QUFEWCxHQUpELEVBT0MsTUFQRDtBQVFBLEVBZkQ7O0FBaUJBOzs7QUFHQSxLQUFJRiw4QkFBOEIsU0FBOUJBLDJCQUE4QixHQUFXO0FBQzVDLE1BQUlVLG9CQUFvQnZDLGVBQWVWLEVBQUVRLE1BQUYsRUFBVTBDLFVBQVYsRUFBdkM7QUFDQSxNQUFJQyxZQUFZbkQsRUFBRSxjQUFGLEVBQWtCb0QsVUFBbEIsRUFBaEI7QUFDQXBELElBQUUsY0FBRixFQUFrQjRDLEdBQWxCLENBQXNCLE1BQXRCLEVBQThCLENBQUNLLG9CQUFvQkUsU0FBcEIsR0FBZ0NBLFNBQWhDLEdBQTRDRixpQkFBN0MsSUFBa0UsSUFBaEc7QUFDQSxFQUpEOztBQU1BOzs7QUFHQSxLQUFJUCxXQUFXLFNBQVhBLFFBQVcsR0FBVztBQUN6Qi9CLFVBQVEsSUFBUjtBQUNBWCxJQUFFLGNBQUYsRUFBa0IrQyxPQUFsQixDQUEwQjtBQUN6QkYsUUFBSztBQURvQixHQUExQixFQUVHLE1BRkgsRUFFVyxPQUZYLEVBRW9CLFlBQVc7QUFDOUI3QyxLQUFFLGNBQUYsRUFBa0I0QyxHQUFsQixDQUFzQjtBQUNyQkMsU0FBS3ZDLG1CQUFtQixJQURIO0FBRXJCd0MsY0FBVTtBQUZXLElBQXRCO0FBSUEsR0FQRDtBQVFBLEVBVkQ7O0FBWUE7Ozs7O0FBS0EsS0FBSUwsc0JBQXNCLFNBQXRCQSxtQkFBc0IsR0FBVztBQUNwQyxNQUFJekMsRUFBRVEsTUFBRixFQUFVQyxTQUFWLE9BQTBCLENBQTlCLEVBQWlDO0FBQ2hDVCxLQUFFLGNBQUYsRUFBa0I0QyxHQUFsQixDQUFzQjtBQUNyQkMsU0FBS3ZDLG1CQUFtQixJQURIO0FBRXJCd0MsY0FBVTtBQUZXLElBQXRCOztBQUtBLFVBQU8sSUFBUDtBQUNBOztBQUVELFNBQU8sS0FBUDtBQUNBLEVBWEQ7O0FBYUE7QUFDQTtBQUNBOztBQUVBakQsUUFBT3dELElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJDLGFBQVcsWUFBVztBQUNyQjNDLHFCQURxQixDQUNGOztBQUVuQjtBQUNBLE9BQUlVLFdBQVdGLG9CQUFmOztBQUVBLE9BQUlFLGFBQWEsSUFBakIsRUFBdUI7QUFDdEJLLDJCQUF1QkwsUUFBdkI7QUFDQTs7QUFFRHRCLEtBQUVRLE1BQUYsRUFBVWdELEVBQVYsQ0FBYSxRQUFiLEVBQXVCckIsWUFBdkI7O0FBRUE7QUFDQTtBQUNBbkMsS0FBRSxjQUFGLEVBQWtCeUQsTUFBbEIsR0FBMkJ6QyxNQUEzQixDQUFrQ2hCLEVBQUUsY0FBRixFQUFrQnlELE1BQWxCLEdBQTJCekMsTUFBM0IsRUFBbEM7O0FBRUFzQztBQUNBLEdBakJELEVBaUJHLEdBakJIO0FBa0JBLEVBbkJEOztBQXFCQSxRQUFPekQsTUFBUDtBQUNBLENBblFGIiwiZmlsZSI6InBhZ2VfbmF2X3RhYnMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHBhZ2VfbmF2X3RhYnMuanMgMjAxNi0wNy0xM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgUGFnZSBOYXZpZ2F0aW9uIFRhYnNcbiAqXG4gKiBUaGlzIG1vZHVsZSB3aWxsIGNvbnZlcnQgb2xkIHRhYmxlLXN0eWxlIG5hdmlnYXRpb24gdG8gdGhlIG5ldyB0aGVtZSBuYXZpZ2F0aW9uIHRhYnMgZm9yXG4gKiBldmVyeSBwYWdlLiBJdCBzZWFyY2hlcyBmb3Igc3BlY2lmaWMgSFRNTCBwYXR0ZXJucyBhbmQgY3JlYXRlcyBuZXcgbWFya3VwIGZvciB0aGUgcGFnZVxuICogbmF2aWdhdGlvbi5cbiAqXG4gKiAqKkltcG9ydGFudCEqKiBJZiB5b3UgbmVlZCB0byBleGNsdWRlIGFuIG9sZCBuYXZpZ2F0aW9uIHRhYmxlIGZyb20gYmVpbmcgY29udmVydGVkIHlvdSBtdXN0IGFkZFxuICogdGhlIFwiZXhjbHVkZS1wYWdlLW5hdlwiIGNsYXNzIHRvIGl0cyB0YWJsZSB0YWcgYXMgaW4gdGhlIGZvbGxvd2luZyBleGFtcGxlLlxuICpcbiAqIGBgYGh0bWxcbiAqIDx0YWJsZSBjbGFzcz1cImV4Y2x1ZGUtcGFnZS1uYXZcIj4gLi4uIDwvdGFibGU+XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvcGFnZV9uYXZfdGFic1xuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J3BhZ2VfbmF2X3RhYnMnLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9wYWdlX25hdl90YWJzICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHRFWENMVURFX0NMQVNTOiAnZXhjbHVkZS1wYWdlLW5hdicsXG5cdFx0XHRcdENPTlZFUlRfQ0xBU1M6ICdjb252ZXJ0LXRvLXRhYnMnXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBUaGUgb3JpZ2luYWwgcG9zaXRpb24gb2YgdGhlIHRhYiBuYXZpZ2F0aW9uXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUgaW50XG5cdFx0XHQgKi9cblx0XHRcdG9yaWdpbmFsUG9zaXRpb24gPSAwLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFRoZSBsYXN0IHNjcm9sbCBwb3NpdGlvblxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIGludFxuXHRcdFx0ICovXG5cdFx0XHRzY3JvbGxQb3NpdGlvbiA9ICQod2luZG93KS5zY3JvbGxUb3AoKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBUaGUgb3JpZ2luYWwgbGVmdCBwb3NpdGlvbiBvZiB0aGUgcGFnZUhlYWRpbmdcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7bnVtYmVyfVxuXHRcdFx0ICovXG5cdFx0XHRvcmlnaW5hbExlZnQgPSAwLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFRlbGxzIGlmIHRoZSB0YWIgbmF2aWdhdGlvbiBpcyB3aXRoaW4gdGhlIHZpZXcgcG9ydFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIGJvb2xlYW5cblx0XHRcdCAqL1xuXHRcdFx0aXNPdXQgPSB0cnVlO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFBSSVZBVEUgTUVUSE9EU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENoYW5nZSB0aGUgZmlyc3QgLnBhZ2VIZWFkaW5nIEhUTUwgdG8gY29udGFpbiB0aGUgXCIucGFnZS1uYXYtdGl0bGVcIiBjbGFzcy5cblx0XHQgKi9cblx0XHR2YXIgX2ZpeFBhZ2VIZWFkaW5nID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJHBhZ2VIZWFkaW5nID0gJCgnLnBhZ2VIZWFkaW5nOmZpcnN0LWNoaWxkJyk7XG5cdFx0XHQkcGFnZUhlYWRpbmcuaHRtbCgnPGRpdiBjbGFzcz1cInBhZ2UtbmF2LXRpdGxlXCI+JyArICRwYWdlSGVhZGluZy5odG1sKCkgKyAnPC9kaXY+Jyk7XG5cdFx0XHRcblx0XHRcdCRwYWdlSGVhZGluZy53cmFwKCc8ZGl2IGNsYXNzPVwicGFnZUhlYWRpbmdXcmFwcGVyXCI+PC9kaXY+Jyk7XG5cdFx0XHQkKCcucGFnZUhlYWRpbmdXcmFwcGVyJykuaGVpZ2h0KCRwYWdlSGVhZGluZy5oZWlnaHQoKSArIDEpO1xuXHRcdFx0b3JpZ2luYWxMZWZ0ID0gJCgnLnBhZ2VIZWFkaW5nJykubGVuZ3RoID8gJCgnLnBhZ2VIZWFkaW5nJykub2Zmc2V0KCkubGVmdCA6IDA7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDaGVja3MgaWYgdGhlIHBhZ2UgaGFzIHRoZSBvbGQgdGFibGUtc3R5bGUgbmF2aWdhdGlvbiBzeXN0ZW0uXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJuIHtzdHJpbmd9IFJldHVybnMgUmV0dXJucyB0aGUgc2VsZWN0b3IgdGhhdCBtYXRjaGVzIHRoZSBzdHJpbmcgb3IgbnVsbCBpZiBub25lIHdhcyBmb3VuZC5cblx0XHQgKi9cblx0XHR2YXIgX2RldGVjdEh0bWxQYXR0ZXJuID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgcGF0dGVybnMgPSBbXG5cdFx0XHRcdFx0Jy5tYWluIHRhYmxlIC5kYXRhVGFibGVIZWFkaW5nUm93IC5kYXRhVGFibGVIZWFkaW5nQ29udGVudFRleHQgYScsXG5cdFx0XHRcdFx0Jy5wZGZfbWVudSB0ciB0ZC5kYXRhVGFibGVIZWFkaW5nQ29udGVudCcsXG5cdFx0XHRcdFx0Jy5ib3hDZW50ZXIgdGFibGUgdHIgdGQuZGF0YVRhYmxlSGVhZGluZ0NvbnRlbnQgYSdcblx0XHRcdFx0XSxcblx0XHRcdFx0c2VsZWN0b3IgPSBudWxsO1xuXHRcdFx0XG5cdFx0XHQkLmVhY2gocGF0dGVybnMsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRpZiAoJHRoaXMuZmluZCh0aGlzKS5sZW5ndGggPiAwICYmICEkdGhpcy5maW5kKHRoaXMpLmNsb3Nlc3QoJ3RhYmxlJykuaGFzQ2xhc3Mob3B0aW9ucy5FWENMVURFX0NMQVNTKSkge1xuXHRcdFx0XHRcdHNlbGVjdG9yID0gdGhpcztcblx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7IC8vIGV4aXQgbG9vcFxuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0cmV0dXJuIHNlbGVjdG9yO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUGVyZm9ybXMgdGhlIGNvbnZlcnNpb24gb2YgdGhlIG9sZCBzdHlsZSB0byB0aGUgbmV3IG5hdmlnYXRpb24gSFRNTC5cblx0XHQgKlxuXHRcdCAqIEl0IHdpbGwgYWxzbyBoaWRlIHRoZSBvbGQgbmF2aWdhdGlvbiBtYXJrdXAuIFN0eWxpbmcgZm9yIHRoZSBuZXcgSFRNTCBpcyBsb2NhdGVkIGluIHRoZVxuXHRcdCAqIFwiX2NvbXBhdGliaWxpdHkuc2Nzc1wiLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9IHNlbGVjdG9yIFRoZSBzZWxlY3RvciBzdHJpbmcgdG8gYmUgdXNlZCBmb3Igc2VsZWN0aW5nIHRoZSBvbGQgdGFibGUgdGQgY2VsbHMuXG5cdFx0ICovXG5cdFx0dmFyIF9jb252ZXJ0TmF2aWdhdGlvblRhYnMgPSBmdW5jdGlvbihzZWxlY3Rvcikge1xuXHRcdFx0dmFyICRzZWxlY3RvciA9ICR0aGlzLmZpbmQoc2VsZWN0b3IpLFxuXHRcdFx0XHQkdGFibGUgPSAkc2VsZWN0b3IuY2xvc2VzdCgndGFibGUnKSxcblx0XHRcdFx0JHBhZ2VIZWFkaW5nID0gJCgnLnBhZ2VIZWFkaW5nOmZpcnN0LWNoaWxkJyksXG5cdFx0XHRcdCRwYWdlTmF2VGFicyA9ICQoJzxkaXYgY2xhc3M9XCJwYWdlLW5hdi10YWJzXCI+PC9kaXY+Jyk7XG5cdFx0XHRcblx0XHRcdCR0YWJsZS5maW5kKCd0ciB0ZCcpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciAkbmF2VGFiID0gJCgnPGRpdiBjbGFzcz1cIm5hdi10YWJcIj4nICsgJCh0aGlzKS5odG1sKCkgKyAnPC9kaXY+Jyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBTdHlsZSBjdXJyZW50IHBhZ2UgdGFicy5cblx0XHRcdFx0aWYgKCRuYXZUYWIuZmluZCgnYScpLmxlbmd0aCA9PT0gMCkge1xuXHRcdFx0XHRcdCRuYXZUYWIuYWRkQ2xhc3MoJ25vLWxpbmsnKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0JG5hdlRhYi5hcHBlbmRUbygkcGFnZU5hdlRhYnMpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCRwYWdlTmF2VGFicy5hcHBlbmRUbygkcGFnZUhlYWRpbmcpO1xuXHRcdFx0XG5cdFx0XHQkdGFibGUuaGlkZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUXVpY2sgUmV0dXJuIENoZWNrXG5cdFx0ICpcblx0XHQgKiBSZXNldCB0aGUgcGFnZSBuYXZpZ2F0aW9uIGZyYW1lIHRvIHRoZSBvcmlnaW5hbCBwb3NpdGlvbiBpZiB0aGUgdXNlciBzY3JvbGxzIGRpcmVjdGx5XG5cdFx0ICogdG8gdG9wLlxuXHRcdCAqL1xuXHRcdHZhciBfcXVpY2tSZXR1cm4gPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBuZXdTY3JvbGxQb3NpdGlvbiA9ICQod2luZG93KS5zY3JvbGxUb3AoKTtcblx0XHRcdHZhciBpc1Njcm9sbERvd24gPSBzY3JvbGxQb3NpdGlvbiAtIG5ld1Njcm9sbFBvc2l0aW9uIDwgMDtcblx0XHRcdHZhciBpc1Njcm9sbFVwID0gc2Nyb2xsUG9zaXRpb24gLSBuZXdTY3JvbGxQb3NpdGlvbiA+IDA7XG5cdFx0XHRvcmlnaW5hbFBvc2l0aW9uID0gJCgnLm1haW4tdG9wLWhlYWRlcicpLmhlaWdodCgpO1xuXHRcdFx0X3NldFBhZ2VIZWFkaW5nTGVmdEFic29sdXRlKCk7XG5cdFx0XHRcblx0XHRcdHZhciBzY3JvbGxlZFRvVG9wID0gX2NoZWNrU2Nyb2xsZWRUb1RvcCgpO1xuXHRcdFx0XG5cdFx0XHRpZiAoIXNjcm9sbGVkVG9Ub3ApIHtcblx0XHRcdFx0aWYgKGlzU2Nyb2xsRG93biAmJiAhaXNTY3JvbGxVcCAmJiAhaXNPdXQpIHtcblx0XHRcdFx0XHRfcm9sbE91dCgpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2UgaWYgKCFpc1Njcm9sbERvd24gJiYgaXNTY3JvbGxVcCAmJiBpc091dCkge1xuXHRcdFx0XHRcdF9yb2xsSW4oKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRzY3JvbGxQb3NpdGlvbiA9IG5ld1Njcm9sbFBvc2l0aW9uO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUm9sbC1pbiBBbmltYXRpb24gRnVuY3Rpb25cblx0XHQgKi9cblx0XHR2YXIgX3JvbGxJbiA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aXNPdXQgPSBmYWxzZTtcblx0XHRcdCQoJy5wYWdlSGVhZGluZycpLmNzcyh7XG5cdFx0XHRcdHRvcDogJzBweCcsXG5cdFx0XHRcdHBvc2l0aW9uOiAnZml4ZWQnXG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0JCgnLnBhZ2VIZWFkaW5nJykuYW5pbWF0ZShcblx0XHRcdFx0e1xuXHRcdFx0XHRcdHRvcDogb3JpZ2luYWxQb3NpdGlvbiArICdweCdcblx0XHRcdFx0fSxcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGNvbXBsZXRlOiBfY2hlY2tTY3JvbGxlZFRvVG9wXG5cdFx0XHRcdH0sXG5cdFx0XHRcdCdmYXN0Jyk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBTZXRzIHRoZSBsZWZ0IHBvc2l0aW9uIG9mIHRoZSBwYWdlSGVhZGluZyBhYnNvbHV0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0UGFnZUhlYWRpbmdMZWZ0QWJzb2x1dGUgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBjb250ZW50SGVhZGVyTGVmdCA9IG9yaWdpbmFsTGVmdCAtICQod2luZG93KS5zY3JvbGxMZWZ0KCk7XG5cdFx0XHR2YXIgbWVudVdpZHRoID0gJCgnLmNvbHVtbkxlZnQyJykub3V0ZXJXaWR0aCgpOyBcblx0XHRcdCQoJy5wYWdlSGVhZGluZycpLmNzcygnbGVmdCcsIChjb250ZW50SGVhZGVyTGVmdCA8IG1lbnVXaWR0aCA/IG1lbnVXaWR0aCA6IGNvbnRlbnRIZWFkZXJMZWZ0KSArICdweCcpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUm9sbC1vdXQgQW5pbWF0aW9uIEZ1bmN0aW9uXG5cdFx0ICovXG5cdFx0dmFyIF9yb2xsT3V0ID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpc091dCA9IHRydWU7XG5cdFx0XHQkKCcucGFnZUhlYWRpbmcnKS5hbmltYXRlKHtcblx0XHRcdFx0dG9wOiAnMHB4J1xuXHRcdFx0fSwgJ2Zhc3QnLCAnc3dpbmcnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JCgnLnBhZ2VIZWFkaW5nJykuY3NzKHtcblx0XHRcdFx0XHR0b3A6IG9yaWdpbmFsUG9zaXRpb24gKyAncHgnLFxuXHRcdFx0XHRcdHBvc2l0aW9uOiAnc3RhdGljJ1xuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ2hlY2sgaWYgdXNlciBoYXMgc2Nyb2xsZWQgdG8gdG9wIG9mIHRoZSBwYWdlLlxuXHRcdCAqXG5cdFx0ICogQHJldHVybnMge2Jvb2x9IFJldHVybnMgdGhlIGNoZWNrIHJlc3VsdC5cblx0XHQgKi9cblx0XHR2YXIgX2NoZWNrU2Nyb2xsZWRUb1RvcCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKCQod2luZG93KS5zY3JvbGxUb3AoKSA9PT0gMCkge1xuXHRcdFx0XHQkKCcucGFnZUhlYWRpbmcnKS5jc3Moe1xuXHRcdFx0XHRcdHRvcDogb3JpZ2luYWxQb3NpdGlvbiArICdweCcsXG5cdFx0XHRcdFx0cG9zaXRpb246ICdzdGF0aWMnXG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRfZml4UGFnZUhlYWRpbmcoKTsgLy8gbXVzdCBiZSBleGVjdXRlZCBmb3IgZXZlcnkgcGFnZVxuXHRcdFx0XHRcblx0XHRcdFx0Ly8gQ29udmVydCBvbmx5IHRoZSBwYWdlcyB0aGF0IGhhdmUgYSByZWNvZ25pemFibGUgdGFibGUgbmF2aWdhdGlvbiBzdHlsZS5cblx0XHRcdFx0dmFyIHNlbGVjdG9yID0gX2RldGVjdEh0bWxQYXR0ZXJuKCk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoc2VsZWN0b3IgIT09IG51bGwpIHtcblx0XHRcdFx0XHRfY29udmVydE5hdmlnYXRpb25UYWJzKHNlbGVjdG9yKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0JCh3aW5kb3cpLm9uKCdzY3JvbGwnLCBfcXVpY2tSZXR1cm4pO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gU2V0IGhlaWdodCBmb3IgcGFyZW50IGVsZW1lbnQgb2YgdGhlIHBhZ2UgaGVhZGluZyBiYXIgdG8gYXZvaWQgdGhhdCB0aGUgbWFpbiBjb250ZW50IG1vdmVzIHVwIHdoZW5cblx0XHRcdFx0Ly8gdGhlIGhlYWRpbmcgYmFyIHN3aXRjaGVzIGludG8gc3RpY2t5IG1vZGVcblx0XHRcdFx0JCgnLnBhZ2VIZWFkaW5nJykucGFyZW50KCkuaGVpZ2h0KCQoJy5wYWdlSGVhZGluZycpLnBhcmVudCgpLmhlaWdodCgpKTtcblx0XHRcdFx0XG5cdFx0XHRcdGRvbmUoKTtcblx0XHRcdH0sIDMwMCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
