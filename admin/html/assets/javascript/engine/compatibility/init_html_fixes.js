'use strict';

/* --------------------------------------------------------------
 init_html_fixes.js 2016-02-10
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Admin HTML Fixes
 *
 * This module will be executed in page load and will perform minor HTML fixes for each pages
 * so that they don't have to be performed manually. Apply this module to the page wrapper element.
 *
 * @module Compatibility/init_html_fixes
 */
gx.compatibility.module('init_html_fixes', ['url_arguments'],

/**  @lends module:Compatibility/init_html_fixes */

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
  * Array of callbacks.
  *
  * @type {array}
  */
	fixes = [],


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


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
	module = {};

	// ------------------------------------------------------------------------
	// HTML FIXES
	// ------------------------------------------------------------------------

	/**
  * Wrap main page content into container.
  */
	fixes.push(function () {
		$('.pageHeading').eq(1).removeClass('pageHeading');
		$('.boxCenter:not(.no-wrap)').wrapInner('<div class="boxCenterWrapper"></div>');
		$('.pageHeading:first-child').prependTo('.boxCenter');
		$('.pageHeading').css('float', 'none');

		var $firstChild = $($('.boxCenterWrapper').children()[0]);

		if ($firstChild.is('br')) {
			$firstChild.remove();
		}

		if ($('div.gx-configuration-box').length) {
			$('.boxCenterWrapper').wrap('<div class="boxCenterAndConfigurationWrapper" style="overflow: auto"></div>');
			$('.boxCenterAndConfigurationWrapper').append($('div.gx-configuration-box'));
		}
	});

	/**
  * Remove unnecessary <br> tag after page wrapper element.
  */
	fixes.push(function () {
		var $nextElement = $this.next(),
		    tagName = $nextElement.prop('tagName');

		if (tagName === 'BR') {
			$nextElement.remove();
		}
	});

	/**
  * Ensure that the left menu parent has the columnLeft2 class because there
  * are some pages where this class is not defined and it will lead to styling issues.
  */
	fixes.push(function () {
		var $columnLeft2 = $('.main-left-menu').parent('td');
		if (!$columnLeft2.hasClass('columnLeft2')) {
			$columnLeft2.addClass('columnLeft2');
		}
	});

	/**
  * Remove width attribute from ".columnLeft2" element.
  */
	fixes.push(function () {
		$('.columnLeft2').removeAttr('width');
	});

	/**
  * Move message stack container to correct place.
  */
	fixes.push(function () {
		var $messageStackContainer = $('.message_stack_container'),
		    $message = $messageStackContainer.find('.alert'),
		    $createNewWrapper = $('.create-new-wrapper');

		if ($('.boxCenterWrapper').length > 0) {
			$messageStackContainer.prependTo('.boxCenterWrapper').show();
		} else {
			$messageStackContainer.prependTo('.order-edit-content').show();
			$messageStackContainer.prependTo('.dashboard-content').show();
		}

		/**
   * Fix if there are more than one message stack container classes.
   * This fix only work, if there are two containers.
   * Improve it if you recognize pages with more than two container classes.
   */
		if ($messageStackContainer.length > 1) {
			$($messageStackContainer[0]).remove();
		}

		if ($message.length > 0 && $createNewWrapper.length > 0) {
			$createNewWrapper.addClass('message-stack-active');
		}
	});

	/**
  * Changing behavior in the orders page.
  */
	fixes.push(function () {
		// Checks if current page is order and return immediately if its not the case
		var isCurrentPage = window.location.href.indexOf('orders.php') > -1;

		if (!isCurrentPage) {
			return;
		}

		// Prepare customer link
		var customerLinkPrefix = window.location.href.replace(window.location.search, '').replace('orders.php', 'customers.php?action=edit&cID=');

		// Do the modifications on the table rows
		var rowsSelectors = ['tr.gx-container.dataTableRowSelected', 'tr.gx-container.dataTableRow'].join(', ');
		var $rows = $this.find(rowsSelectors);

		// Remove the on click event on the entire row add special events
		$rows.each(function (index, element) {
			// Extract order link from element
			var orderLink = $(element).find('td[onclick]:first').attr('onclick');
			if (typeof orderLink !== 'undefined') {
				orderLink.replace('document.location.href="', '').replace('&action=edit', '').slice(0, -1);
			}

			// Customer ID
			var customerId = $(this).find('a[data-customer-id]').data('customerId');

			// Remove onclick attributes from elements
			$(element).find('[onclick]').removeAttr('onclick');
		});
	});

	/**
  * Remove inline class javascript changes.
  */
	fixes.push(function () {
		var selectors = ['.dataTableRow', '.dataTableRowSelected'];

		$.each(selectors, function () {
			$(this).each(function () {
				if ($(this).attr('onmouseover') && $(this).attr('onmouseover').indexOf('this.className') > -1) {
					$(this).removeAttr('onmouseover');
				}

				if ($(this).attr('onmouseout') && $(this).attr('onmouseout').indexOf('this.className') > -1) {
					$(this).removeAttr('onmouseout');
				}
			});
		});
	});

	/**
  * Remove the old markup for editing or creating a new category
  */
	fixes.push(function () {
		$('#old-category-table').remove();
	});

	/**
  * Orders form fix.
  */
	fixes.push(function () {
		var $headingBoxContainer = $('.orders_form');
		$.each($headingBoxContainer.children(), function (index, element) {
			$(element).addClass('hidden');
		});
	});

	/**
  * Fix margins and cell spacing of left menu
  */
	fixes.push(function () {
		$('.columnLeft2').parents('table:first').css({
			'border-spacing': 0
		});
	});

	fixes.push(function () {
		var urlHelper = jse.libs.url_arguments;

		if (urlHelper.getCurrentFile() === 'categories.php') {
			$('.columnLeft2').parents('table:first').css('width', '');
		}
	});

	fixes.push(function () {
		var urlHelper = jse.libs.url_arguments;
		var file = urlHelper.getCurrentFile(),
		    doParameter = urlHelper.getUrlParameters().do || '',
		    largePages = ['gm_emails.php'],
		    smallPages = ['gm_seo_boost.php', 'parcel_services.php'];

		if ($.inArray(file, largePages) > -1 || file === 'admin.php' && $.inArray(doParameter, largePages) > -1) {
			$('.boxCenterWrapper').addClass('breakpoint-large');
		}

		if ($.inArray(file, smallPages) > -1 || file === 'admin.php' && $.inArray(doParameter, smallPages) > -1) {
			$('.boxCenterWrapper').addClass('breakpoint-small');
		}
	});

	/**
  * Helper to add css breakpoint classes to pages which use the controller mechanism.
  * Extend whether the array 'largePages' or 'smallPages' to add the breakpoint class.
  * Add as element the controller name (like in the url behind do=) and the action with trailing slash.
  * (the action is the string in the 'do' argument behind the slash)
  */
	fixes.push(function () {
		var urlHelper = jse.libs.url_arguments,
		    currentFile = urlHelper.getCurrentFile(),
		    controllerAction = urlHelper.getUrlParameters().do,
		    largePages = [],
		    smallPages = ['JanolawModuleCenterModule/Config'];

		if (currentFile === 'admin.php') {

			if ($.inArray(controllerAction, largePages) > -1) {
				$('#container').addClass('breakpoint-large');
			}

			if ($.inArray(controllerAction, smallPages) > -1) {
				$('#container').addClass('breakpoint-small');
			}
		}
	});

	/**
  * Cleans the header of the configuration box from tables
  */
	fixes.push(function () {
		var $contents = $('div.configuration-box-header h2 table.contentTable tr td > *');
		$contents.each(function (index, elem) {
			$('div.configuration-box-header h2').append(elem);
			$('div.configuration-box-header h2').find('table.contentTable').remove();
		});
	});

	/**
  * Convert all the simple checkboxes to the JS Engine widget.
  *
  * This fix will fine-tune the html markup of the checkbox and then it will dynamically
  * initialize the checkbox widget.
  */
	fixes.push(function () {
		var selectors = ['table .categories_view_data input:checkbox', 'table .dataTableHeadingRow td input:checkbox', 'table thead tr th:first input:checkbox', 'table.gx-orders-table tr:not(.dataTableHeadingRow) td:first-child input:checkbox', 'form[name="quantity_units"] input:checkbox', 'form[name="sliderset"] input:checkbox', 'form[name="featurecontrol"] input:checkbox:not(.checkbox-switcher)', '.feature-table tr td:last-child input:checkbox'];

		if ($(selectors).length > 120) {
			return;
		}

		$.each(selectors, function () {
			$(this).each(function () {
				if (!$(this).parent().hasClass('single-checkbox')) {
					$(this).attr('data-single_checkbox', '').parent().attr('data-gx-widget', 'checkbox');
					gx.widgets.init($(this).parent());
				}
			});
		});
	});

	/**
  * Make the top header bar clickable to activate the search bar
  */
	fixes.push(function () {
		var $topHeader = $('.top-header'),
		    $searchInput = $('input[name="admin_search"]');

		$topHeader.on('click', function (event) {
			if ($topHeader.is(event.target)) {
				$searchInput.trigger('click');
			}
		});
	});

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Execute all the existing fixes.
		$.each(fixes, function () {
			this();
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluaXRfaHRtbF9maXhlcy5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZml4ZXMiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJwdXNoIiwiZXEiLCJyZW1vdmVDbGFzcyIsIndyYXBJbm5lciIsInByZXBlbmRUbyIsImNzcyIsIiRmaXJzdENoaWxkIiwiY2hpbGRyZW4iLCJpcyIsInJlbW92ZSIsImxlbmd0aCIsIndyYXAiLCJhcHBlbmQiLCIkbmV4dEVsZW1lbnQiLCJuZXh0IiwidGFnTmFtZSIsInByb3AiLCIkY29sdW1uTGVmdDIiLCJwYXJlbnQiLCJoYXNDbGFzcyIsImFkZENsYXNzIiwicmVtb3ZlQXR0ciIsIiRtZXNzYWdlU3RhY2tDb250YWluZXIiLCIkbWVzc2FnZSIsImZpbmQiLCIkY3JlYXRlTmV3V3JhcHBlciIsInNob3ciLCJpc0N1cnJlbnRQYWdlIiwid2luZG93IiwibG9jYXRpb24iLCJocmVmIiwiaW5kZXhPZiIsImN1c3RvbWVyTGlua1ByZWZpeCIsInJlcGxhY2UiLCJzZWFyY2giLCJyb3dzU2VsZWN0b3JzIiwiam9pbiIsIiRyb3dzIiwiZWFjaCIsImluZGV4IiwiZWxlbWVudCIsIm9yZGVyTGluayIsImF0dHIiLCJzbGljZSIsImN1c3RvbWVySWQiLCJzZWxlY3RvcnMiLCIkaGVhZGluZ0JveENvbnRhaW5lciIsInBhcmVudHMiLCJ1cmxIZWxwZXIiLCJqc2UiLCJsaWJzIiwidXJsX2FyZ3VtZW50cyIsImdldEN1cnJlbnRGaWxlIiwiZmlsZSIsImRvUGFyYW1ldGVyIiwiZ2V0VXJsUGFyYW1ldGVycyIsImRvIiwibGFyZ2VQYWdlcyIsInNtYWxsUGFnZXMiLCJpbkFycmF5IiwiY3VycmVudEZpbGUiLCJjb250cm9sbGVyQWN0aW9uIiwiJGNvbnRlbnRzIiwiZWxlbSIsIndpZGdldHMiLCJpbml0IiwiJHRvcEhlYWRlciIsIiRzZWFyY2hJbnB1dCIs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QWFJLE9BQWIsQ0FBcUIsT0FBckI7QUFDQTtBQUNELEdBSkQ7QUFNQSxFQVZEOztBQVlBO0FBQ0E7QUFDQTs7QUFFQS9FLFFBQU95RSxJQUFQLEdBQWMsVUFBU08sSUFBVCxFQUFlO0FBQzVCO0FBQ0E3RSxJQUFFMkMsSUFBRixDQUFPMUMsS0FBUCxFQUFjLFlBQVc7QUFDeEI7QUFDQSxHQUZEOztBQUlBNEU7QUFDQSxFQVBEOztBQVNBLFFBQU9oRixNQUFQO0FBQ0EsQ0FsWEYiLCJmaWxlIjoiaW5pdF9odG1sX2ZpeGVzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbml0X2h0bWxfZml4ZXMuanMgMjAxNi0wMi0xMFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQWRtaW4gSFRNTCBGaXhlc1xuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgYmUgZXhlY3V0ZWQgaW4gcGFnZSBsb2FkIGFuZCB3aWxsIHBlcmZvcm0gbWlub3IgSFRNTCBmaXhlcyBmb3IgZWFjaCBwYWdlc1xuICogc28gdGhhdCB0aGV5IGRvbid0IGhhdmUgdG8gYmUgcGVyZm9ybWVkIG1hbnVhbGx5LiBBcHBseSB0aGlzIG1vZHVsZSB0byB0aGUgcGFnZSB3cmFwcGVyIGVsZW1lbnQuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L2luaXRfaHRtbF9maXhlc1xuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2luaXRfaHRtbF9maXhlcycsXG5cdFxuXHRbJ3VybF9hcmd1bWVudHMnXSxcblx0XG5cdC8qKiAgQGxlbmRzIG1vZHVsZTpDb21wYXRpYmlsaXR5L2luaXRfaHRtbF9maXhlcyAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBBcnJheSBvZiBjYWxsYmFja3MuXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge2FycmF5fVxuXHRcdFx0ICovXG5cdFx0XHRmaXhlcyA9IFtdLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEhUTUwgRklYRVNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBXcmFwIG1haW4gcGFnZSBjb250ZW50IGludG8gY29udGFpbmVyLlxuXHRcdCAqL1xuXHRcdGZpeGVzLnB1c2goZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcucGFnZUhlYWRpbmcnKS5lcSgxKS5yZW1vdmVDbGFzcygncGFnZUhlYWRpbmcnKTtcblx0XHRcdCQoJy5ib3hDZW50ZXI6bm90KC5uby13cmFwKScpLndyYXBJbm5lcignPGRpdiBjbGFzcz1cImJveENlbnRlcldyYXBwZXJcIj48L2Rpdj4nKTtcblx0XHRcdCQoJy5wYWdlSGVhZGluZzpmaXJzdC1jaGlsZCcpLnByZXBlbmRUbygnLmJveENlbnRlcicpO1xuXHRcdFx0JCgnLnBhZ2VIZWFkaW5nJykuY3NzKCdmbG9hdCcsICdub25lJyk7XG5cdFx0XHRcblx0XHRcdHZhciAkZmlyc3RDaGlsZCA9ICQoJCgnLmJveENlbnRlcldyYXBwZXInKS5jaGlsZHJlbigpWzBdKTtcblx0XHRcdFxuXHRcdFx0aWYgKCRmaXJzdENoaWxkLmlzKCdicicpKSB7XG5cdFx0XHRcdCRmaXJzdENoaWxkLnJlbW92ZSgpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAoJCgnZGl2Lmd4LWNvbmZpZ3VyYXRpb24tYm94JykubGVuZ3RoKSB7XG5cdFx0XHRcdCQoJy5ib3hDZW50ZXJXcmFwcGVyJylcblx0XHRcdFx0XHQud3JhcCgnPGRpdiBjbGFzcz1cImJveENlbnRlckFuZENvbmZpZ3VyYXRpb25XcmFwcGVyXCIgc3R5bGU9XCJvdmVyZmxvdzogYXV0b1wiPjwvZGl2PicpO1xuXHRcdFx0XHQkKCcuYm94Q2VudGVyQW5kQ29uZmlndXJhdGlvbldyYXBwZXInKS5hcHBlbmQoJCgnZGl2Lmd4LWNvbmZpZ3VyYXRpb24tYm94JykpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFJlbW92ZSB1bm5lY2Vzc2FyeSA8YnI+IHRhZyBhZnRlciBwYWdlIHdyYXBwZXIgZWxlbWVudC5cblx0XHQgKi9cblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRuZXh0RWxlbWVudCA9ICR0aGlzLm5leHQoKSxcblx0XHRcdFx0dGFnTmFtZSA9ICRuZXh0RWxlbWVudC5wcm9wKCd0YWdOYW1lJyk7XG5cdFx0XHRcblx0XHRcdGlmICh0YWdOYW1lID09PSAnQlInKSB7XG5cdFx0XHRcdCRuZXh0RWxlbWVudC5yZW1vdmUoKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBFbnN1cmUgdGhhdCB0aGUgbGVmdCBtZW51IHBhcmVudCBoYXMgdGhlIGNvbHVtbkxlZnQyIGNsYXNzIGJlY2F1c2UgdGhlcmVcblx0XHQgKiBhcmUgc29tZSBwYWdlcyB3aGVyZSB0aGlzIGNsYXNzIGlzIG5vdCBkZWZpbmVkIGFuZCBpdCB3aWxsIGxlYWQgdG8gc3R5bGluZyBpc3N1ZXMuXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkY29sdW1uTGVmdDIgPSAkKCcubWFpbi1sZWZ0LW1lbnUnKS5wYXJlbnQoJ3RkJyk7XG5cdFx0XHRpZiAoISRjb2x1bW5MZWZ0Mi5oYXNDbGFzcygnY29sdW1uTGVmdDInKSkge1xuXHRcdFx0XHQkY29sdW1uTGVmdDIuYWRkQ2xhc3MoJ2NvbHVtbkxlZnQyJyk7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUmVtb3ZlIHdpZHRoIGF0dHJpYnV0ZSBmcm9tIFwiLmNvbHVtbkxlZnQyXCIgZWxlbWVudC5cblx0XHQgKi9cblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnLmNvbHVtbkxlZnQyJykucmVtb3ZlQXR0cignd2lkdGgnKTtcblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBNb3ZlIG1lc3NhZ2Ugc3RhY2sgY29udGFpbmVyIHRvIGNvcnJlY3QgcGxhY2UuXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkbWVzc2FnZVN0YWNrQ29udGFpbmVyID0gJCgnLm1lc3NhZ2Vfc3RhY2tfY29udGFpbmVyJyksXG5cdFx0XHRcdCRtZXNzYWdlID0gJG1lc3NhZ2VTdGFja0NvbnRhaW5lci5maW5kKCcuYWxlcnQnKSxcblx0XHRcdFx0JGNyZWF0ZU5ld1dyYXBwZXIgPSAkKCcuY3JlYXRlLW5ldy13cmFwcGVyJyk7XG5cdFx0XHRcblx0XHRcdGlmICgkKCcuYm94Q2VudGVyV3JhcHBlcicpLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0JG1lc3NhZ2VTdGFja0NvbnRhaW5lci5wcmVwZW5kVG8oJy5ib3hDZW50ZXJXcmFwcGVyJykuc2hvdygpO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0JG1lc3NhZ2VTdGFja0NvbnRhaW5lci5wcmVwZW5kVG8oJy5vcmRlci1lZGl0LWNvbnRlbnQnKS5zaG93KCk7XG5cdFx0XHRcdCRtZXNzYWdlU3RhY2tDb250YWluZXIucHJlcGVuZFRvKCcuZGFzaGJvYXJkLWNvbnRlbnQnKS5zaG93KCk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRml4IGlmIHRoZXJlIGFyZSBtb3JlIHRoYW4gb25lIG1lc3NhZ2Ugc3RhY2sgY29udGFpbmVyIGNsYXNzZXMuXG5cdFx0XHQgKiBUaGlzIGZpeCBvbmx5IHdvcmssIGlmIHRoZXJlIGFyZSB0d28gY29udGFpbmVycy5cblx0XHRcdCAqIEltcHJvdmUgaXQgaWYgeW91IHJlY29nbml6ZSBwYWdlcyB3aXRoIG1vcmUgdGhhbiB0d28gY29udGFpbmVyIGNsYXNzZXMuXG5cdFx0XHQgKi9cblx0XHRcdGlmICgkbWVzc2FnZVN0YWNrQ29udGFpbmVyLmxlbmd0aCA+IDEpIHtcblx0XHRcdFx0JCgkbWVzc2FnZVN0YWNrQ29udGFpbmVyWzBdKS5yZW1vdmUoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0aWYgKCRtZXNzYWdlLmxlbmd0aCA+IDAgJiYgJGNyZWF0ZU5ld1dyYXBwZXIubGVuZ3RoID4gMCkge1xuXHRcdFx0XHQkY3JlYXRlTmV3V3JhcHBlci5hZGRDbGFzcygnbWVzc2FnZS1zdGFjay1hY3RpdmUnKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDaGFuZ2luZyBiZWhhdmlvciBpbiB0aGUgb3JkZXJzIHBhZ2UuXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdC8vIENoZWNrcyBpZiBjdXJyZW50IHBhZ2UgaXMgb3JkZXIgYW5kIHJldHVybiBpbW1lZGlhdGVseSBpZiBpdHMgbm90IHRoZSBjYXNlXG5cdFx0XHR2YXIgaXNDdXJyZW50UGFnZSA9ICh3aW5kb3cubG9jYXRpb24uaHJlZi5pbmRleE9mKCdvcmRlcnMucGhwJykgPiAtMSk7XG5cdFx0XHRcblx0XHRcdGlmICghaXNDdXJyZW50UGFnZSkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFByZXBhcmUgY3VzdG9tZXIgbGlua1xuXHRcdFx0dmFyIGN1c3RvbWVyTGlua1ByZWZpeCA9IHdpbmRvdy5sb2NhdGlvbi5ocmVmXG5cdFx0XHRcdC5yZXBsYWNlKHdpbmRvdy5sb2NhdGlvbi5zZWFyY2gsICcnKVxuXHRcdFx0XHQucmVwbGFjZSgnb3JkZXJzLnBocCcsICdjdXN0b21lcnMucGhwP2FjdGlvbj1lZGl0JmNJRD0nKTtcblx0XHRcdFxuXHRcdFx0Ly8gRG8gdGhlIG1vZGlmaWNhdGlvbnMgb24gdGhlIHRhYmxlIHJvd3Ncblx0XHRcdHZhciByb3dzU2VsZWN0b3JzID0gW1xuXHRcdFx0XHQndHIuZ3gtY29udGFpbmVyLmRhdGFUYWJsZVJvd1NlbGVjdGVkJyxcblx0XHRcdFx0J3RyLmd4LWNvbnRhaW5lci5kYXRhVGFibGVSb3cnXG5cdFx0XHRdLmpvaW4oJywgJyk7XG5cdFx0XHR2YXIgJHJvd3MgPSAkdGhpcy5maW5kKHJvd3NTZWxlY3RvcnMpO1xuXHRcdFx0XG5cdFx0XHQvLyBSZW1vdmUgdGhlIG9uIGNsaWNrIGV2ZW50IG9uIHRoZSBlbnRpcmUgcm93IGFkZCBzcGVjaWFsIGV2ZW50c1xuXHRcdFx0JHJvd3MuZWFjaChmdW5jdGlvbihpbmRleCwgZWxlbWVudCkge1xuXHRcdFx0XHQvLyBFeHRyYWN0IG9yZGVyIGxpbmsgZnJvbSBlbGVtZW50XG5cdFx0XHRcdHZhciBvcmRlckxpbmsgPSAkKGVsZW1lbnQpXG5cdFx0XHRcdFx0LmZpbmQoJ3RkW29uY2xpY2tdOmZpcnN0Jylcblx0XHRcdFx0XHQuYXR0cignb25jbGljaycpO1xuXHRcdFx0XHRpZiAodHlwZW9mIG9yZGVyTGluayAhPT0gJ3VuZGVmaW5lZCcpIHtcblx0XHRcdFx0XHRvcmRlckxpbmtcblx0XHRcdFx0XHRcdC5yZXBsYWNlKCdkb2N1bWVudC5sb2NhdGlvbi5ocmVmPVwiJywgJycpXG5cdFx0XHRcdFx0XHQucmVwbGFjZSgnJmFjdGlvbj1lZGl0JywgJycpXG5cdFx0XHRcdFx0XHQuc2xpY2UoMCwgLTEpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBDdXN0b21lciBJRFxuXHRcdFx0XHR2YXIgY3VzdG9tZXJJZCA9ICQodGhpcylcblx0XHRcdFx0XHQuZmluZCgnYVtkYXRhLWN1c3RvbWVyLWlkXScpXG5cdFx0XHRcdFx0LmRhdGEoJ2N1c3RvbWVySWQnKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFJlbW92ZSBvbmNsaWNrIGF0dHJpYnV0ZXMgZnJvbSBlbGVtZW50c1xuXHRcdFx0XHQkKGVsZW1lbnQpXG5cdFx0XHRcdFx0LmZpbmQoJ1tvbmNsaWNrXScpXG5cdFx0XHRcdFx0LnJlbW92ZUF0dHIoJ29uY2xpY2snKTtcblx0XHRcdH0pO1xuXHRcdH0pO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFJlbW92ZSBpbmxpbmUgY2xhc3MgamF2YXNjcmlwdCBjaGFuZ2VzLlxuXHRcdCAqL1xuXHRcdGZpeGVzLnB1c2goZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgc2VsZWN0b3JzID0gW1xuXHRcdFx0XHQnLmRhdGFUYWJsZVJvdycsXG5cdFx0XHRcdCcuZGF0YVRhYmxlUm93U2VsZWN0ZWQnXG5cdFx0XHRdO1xuXHRcdFx0XG5cdFx0XHQkLmVhY2goc2VsZWN0b3JzLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JCh0aGlzKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdGlmICgkKHRoaXMpLmF0dHIoJ29ubW91c2VvdmVyJykgJiYgJCh0aGlzKS5hdHRyKCdvbm1vdXNlb3ZlcicpLmluZGV4T2YoJ3RoaXMuY2xhc3NOYW1lJykgPiAtMSkge1xuXHRcdFx0XHRcdFx0JCh0aGlzKS5yZW1vdmVBdHRyKCdvbm1vdXNlb3ZlcicpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdGlmICgkKHRoaXMpLmF0dHIoJ29ubW91c2VvdXQnKSAmJiAkKHRoaXMpLmF0dHIoJ29ubW91c2VvdXQnKS5pbmRleE9mKCd0aGlzLmNsYXNzTmFtZScpID4gLTEpIHtcblx0XHRcdFx0XHRcdCQodGhpcykucmVtb3ZlQXR0cignb25tb3VzZW91dCcpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBSZW1vdmUgdGhlIG9sZCBtYXJrdXAgZm9yIGVkaXRpbmcgb3IgY3JlYXRpbmcgYSBuZXcgY2F0ZWdvcnlcblx0XHQgKi9cblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnI29sZC1jYXRlZ29yeS10YWJsZScpLnJlbW92ZSgpO1xuXHRcdH0pO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE9yZGVycyBmb3JtIGZpeC5cblx0XHQgKi9cblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRoZWFkaW5nQm94Q29udGFpbmVyID0gJCgnLm9yZGVyc19mb3JtJyk7XG5cdFx0XHQkLmVhY2goJGhlYWRpbmdCb3hDb250YWluZXIuY2hpbGRyZW4oKSwgZnVuY3Rpb24oaW5kZXgsIGVsZW1lbnQpIHtcblx0XHRcdFx0JChlbGVtZW50KS5hZGRDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBGaXggbWFyZ2lucyBhbmQgY2VsbCBzcGFjaW5nIG9mIGxlZnQgbWVudVxuXHRcdCAqL1xuXHRcdGZpeGVzLnB1c2goZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcuY29sdW1uTGVmdDInKVxuXHRcdFx0XHQucGFyZW50cygndGFibGU6Zmlyc3QnKVxuXHRcdFx0XHQuY3NzKHtcblx0XHRcdFx0XHQnYm9yZGVyLXNwYWNpbmcnOiAwXG5cdFx0XHRcdH0pO1xuXHRcdH0pO1xuXHRcdFxuXHRcdGZpeGVzLnB1c2goZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgdXJsSGVscGVyID0ganNlLmxpYnMudXJsX2FyZ3VtZW50cztcblx0XHRcdFxuXHRcdFx0aWYgKHVybEhlbHBlci5nZXRDdXJyZW50RmlsZSgpID09PSAnY2F0ZWdvcmllcy5waHAnKSB7XG5cdFx0XHRcdCQoJy5jb2x1bW5MZWZ0MicpXG5cdFx0XHRcdFx0LnBhcmVudHMoJ3RhYmxlOmZpcnN0Jylcblx0XHRcdFx0XHQuY3NzKCd3aWR0aCcsICcnKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHVybEhlbHBlciA9IGpzZS5saWJzLnVybF9hcmd1bWVudHM7XG5cdFx0XHR2YXIgZmlsZSA9IHVybEhlbHBlci5nZXRDdXJyZW50RmlsZSgpLFxuXHRcdFx0XHRkb1BhcmFtZXRlciA9IHVybEhlbHBlci5nZXRVcmxQYXJhbWV0ZXJzKCkuZG8gfHwgJycsXG5cdFx0XHRcdGxhcmdlUGFnZXMgPSBbXG5cdFx0XHRcdFx0J2dtX2VtYWlscy5waHAnXG5cdFx0XHRcdF0sXG5cdFx0XHRcdHNtYWxsUGFnZXMgPSBbXG5cdFx0XHRcdFx0J2dtX3Nlb19ib29zdC5waHAnLFxuXHRcdFx0XHRcdCdwYXJjZWxfc2VydmljZXMucGhwJ1xuXHRcdFx0XHRdO1xuXHRcdFx0XG5cdFx0XHRpZiAoJC5pbkFycmF5KGZpbGUsIGxhcmdlUGFnZXMpID4gLTFcblx0XHRcdFx0fHwgKGZpbGUgPT09ICdhZG1pbi5waHAnICYmICQuaW5BcnJheShkb1BhcmFtZXRlciwgbGFyZ2VQYWdlcykgPiAtMSkpIHtcblx0XHRcdFx0JCgnLmJveENlbnRlcldyYXBwZXInKVxuXHRcdFx0XHRcdC5hZGRDbGFzcygnYnJlYWtwb2ludC1sYXJnZScpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAoJC5pbkFycmF5KGZpbGUsIHNtYWxsUGFnZXMpID4gLTFcblx0XHRcdFx0fHwgKGZpbGUgPT09ICdhZG1pbi5waHAnICYmICQuaW5BcnJheShkb1BhcmFtZXRlciwgc21hbGxQYWdlcykgPiAtMSkpIHtcblx0XHRcdFx0JCgnLmJveENlbnRlcldyYXBwZXInKVxuXHRcdFx0XHRcdC5hZGRDbGFzcygnYnJlYWtwb2ludC1zbWFsbCcpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciB0byBhZGQgY3NzIGJyZWFrcG9pbnQgY2xhc3NlcyB0byBwYWdlcyB3aGljaCB1c2UgdGhlIGNvbnRyb2xsZXIgbWVjaGFuaXNtLlxuXHRcdCAqIEV4dGVuZCB3aGV0aGVyIHRoZSBhcnJheSAnbGFyZ2VQYWdlcycgb3IgJ3NtYWxsUGFnZXMnIHRvIGFkZCB0aGUgYnJlYWtwb2ludCBjbGFzcy5cblx0XHQgKiBBZGQgYXMgZWxlbWVudCB0aGUgY29udHJvbGxlciBuYW1lIChsaWtlIGluIHRoZSB1cmwgYmVoaW5kIGRvPSkgYW5kIHRoZSBhY3Rpb24gd2l0aCB0cmFpbGluZyBzbGFzaC5cblx0XHQgKiAodGhlIGFjdGlvbiBpcyB0aGUgc3RyaW5nIGluIHRoZSAnZG8nIGFyZ3VtZW50IGJlaGluZCB0aGUgc2xhc2gpXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciB1cmxIZWxwZXIgPSBqc2UubGlicy51cmxfYXJndW1lbnRzLFxuXHRcdFx0XHRjdXJyZW50RmlsZSA9IHVybEhlbHBlci5nZXRDdXJyZW50RmlsZSgpLFxuXHRcdFx0XHRjb250cm9sbGVyQWN0aW9uID0gdXJsSGVscGVyLmdldFVybFBhcmFtZXRlcnMoKS5kbyxcblx0XHRcdFx0bGFyZ2VQYWdlcyA9IFtdLFxuXHRcdFx0XHRzbWFsbFBhZ2VzID0gWydKYW5vbGF3TW9kdWxlQ2VudGVyTW9kdWxlL0NvbmZpZyddO1xuXHRcdFx0XG5cdFx0XHRpZiAoY3VycmVudEZpbGUgPT09ICdhZG1pbi5waHAnKSB7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoJC5pbkFycmF5KGNvbnRyb2xsZXJBY3Rpb24sIGxhcmdlUGFnZXMpID4gLTEpIHtcblx0XHRcdFx0XHQkKCcjY29udGFpbmVyJylcblx0XHRcdFx0XHRcdC5hZGRDbGFzcygnYnJlYWtwb2ludC1sYXJnZScpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoJC5pbkFycmF5KGNvbnRyb2xsZXJBY3Rpb24sIHNtYWxsUGFnZXMpID4gLTEpIHtcblx0XHRcdFx0XHQkKCcjY29udGFpbmVyJylcblx0XHRcdFx0XHRcdC5hZGRDbGFzcygnYnJlYWtwb2ludC1zbWFsbCcpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ2xlYW5zIHRoZSBoZWFkZXIgb2YgdGhlIGNvbmZpZ3VyYXRpb24gYm94IGZyb20gdGFibGVzXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkY29udGVudHMgPSAkKCdkaXYuY29uZmlndXJhdGlvbi1ib3gtaGVhZGVyIGgyIHRhYmxlLmNvbnRlbnRUYWJsZSB0ciB0ZCA+IConKTtcblx0XHRcdCRjb250ZW50cy5lYWNoKGZ1bmN0aW9uKGluZGV4LCBlbGVtKSB7XG5cdFx0XHRcdCQoJ2Rpdi5jb25maWd1cmF0aW9uLWJveC1oZWFkZXIgaDInKS5hcHBlbmQoZWxlbSk7XG5cdFx0XHRcdCQoJ2Rpdi5jb25maWd1cmF0aW9uLWJveC1oZWFkZXIgaDInKS5maW5kKCd0YWJsZS5jb250ZW50VGFibGUnKS5yZW1vdmUoKTtcblx0XHRcdH0pO1xuXHRcdH0pO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENvbnZlcnQgYWxsIHRoZSBzaW1wbGUgY2hlY2tib3hlcyB0byB0aGUgSlMgRW5naW5lIHdpZGdldC5cblx0XHQgKlxuXHRcdCAqIFRoaXMgZml4IHdpbGwgZmluZS10dW5lIHRoZSBodG1sIG1hcmt1cCBvZiB0aGUgY2hlY2tib3ggYW5kIHRoZW4gaXQgd2lsbCBkeW5hbWljYWxseVxuXHRcdCAqIGluaXRpYWxpemUgdGhlIGNoZWNrYm94IHdpZGdldC5cblx0XHQgKi9cblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHNlbGVjdG9ycyA9IFtcblx0XHRcdFx0J3RhYmxlIC5jYXRlZ29yaWVzX3ZpZXdfZGF0YSBpbnB1dDpjaGVja2JveCcsXG5cdFx0XHRcdCd0YWJsZSAuZGF0YVRhYmxlSGVhZGluZ1JvdyB0ZCBpbnB1dDpjaGVja2JveCcsXG5cdFx0XHRcdCd0YWJsZSB0aGVhZCB0ciB0aDpmaXJzdCBpbnB1dDpjaGVja2JveCcsXG5cdFx0XHRcdCd0YWJsZS5neC1vcmRlcnMtdGFibGUgdHI6bm90KC5kYXRhVGFibGVIZWFkaW5nUm93KSB0ZDpmaXJzdC1jaGlsZCBpbnB1dDpjaGVja2JveCcsXG5cdFx0XHRcdCdmb3JtW25hbWU9XCJxdWFudGl0eV91bml0c1wiXSBpbnB1dDpjaGVja2JveCcsXG5cdFx0XHRcdCdmb3JtW25hbWU9XCJzbGlkZXJzZXRcIl0gaW5wdXQ6Y2hlY2tib3gnLFxuXHRcdFx0XHQnZm9ybVtuYW1lPVwiZmVhdHVyZWNvbnRyb2xcIl0gaW5wdXQ6Y2hlY2tib3g6bm90KC5jaGVja2JveC1zd2l0Y2hlciknLFxuXHRcdFx0XHQnLmZlYXR1cmUtdGFibGUgdHIgdGQ6bGFzdC1jaGlsZCBpbnB1dDpjaGVja2JveCdcblx0XHRcdF07XG5cdFx0XHRcblx0XHRcdGlmICgkKHNlbGVjdG9ycykubGVuZ3RoID4gMTIwKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JC5lYWNoKHNlbGVjdG9ycywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCQodGhpcykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRpZiAoISQodGhpcykucGFyZW50KCkuaGFzQ2xhc3MoJ3NpbmdsZS1jaGVja2JveCcpKSB7XG5cdFx0XHRcdFx0XHQkKHRoaXMpXG5cdFx0XHRcdFx0XHRcdC5hdHRyKCdkYXRhLXNpbmdsZV9jaGVja2JveCcsICcnKVxuXHRcdFx0XHRcdFx0XHQucGFyZW50KCkuYXR0cignZGF0YS1neC13aWRnZXQnLCAnY2hlY2tib3gnKTtcblx0XHRcdFx0XHRcdGd4LndpZGdldHMuaW5pdCgkKHRoaXMpLnBhcmVudCgpKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0fSk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTWFrZSB0aGUgdG9wIGhlYWRlciBiYXIgY2xpY2thYmxlIHRvIGFjdGl2YXRlIHRoZSBzZWFyY2ggYmFyXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkdG9wSGVhZGVyID0gJCgnLnRvcC1oZWFkZXInKSxcblx0XHRcdFx0JHNlYXJjaElucHV0ID0gJCgnaW5wdXRbbmFtZT1cImFkbWluX3NlYXJjaFwiXScpO1xuXHRcdFx0XG5cdFx0XHQkdG9wSGVhZGVyLm9uKCdjbGljaycsIGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdGlmICgkdG9wSGVhZGVyLmlzKGV2ZW50LnRhcmdldCkpIHtcblx0XHRcdFx0XHQkc2VhcmNoSW5wdXQudHJpZ2dlcignY2xpY2snKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHR9KTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Ly8gRXhlY3V0ZSBhbGwgdGhlIGV4aXN0aW5nIGZpeGVzLlxuXHRcdFx0JC5lYWNoKGZpeGVzLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0dGhpcygpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
