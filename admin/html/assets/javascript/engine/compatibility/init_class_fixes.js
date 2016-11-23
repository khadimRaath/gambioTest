'use strict';

/* --------------------------------------------------------------
 init_class_fixes.js 2016-02-03 
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Initialize Class Fixes
 *
 * This module must set as many compatibility classes as possible. Wherever it is
 * certain that an HTML class will be present it must be automatically set by this
 * module.
 *
 * @module Compatibility/init_class_fixes
 */
gx.compatibility.module('init_class_fixes', ['url_arguments'],

/**  @lends module:Compatibility/init_class_fixes */

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
  * Callbacks for checking common patterns.
  *
  * @var {array}
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
	// OPERATIONS
	// ------------------------------------------------------------------------

	/**
  * Add gx-compatibility class to body element.
  */
	fixes.push(function () {
		if (!$('body').hasClass('gx-compatibility')) {
			$('body').addClass('gx-compatibility');
		}
	});

	/**
  * Add the gx-container custom predefined selectors.
  */
	fixes.push(function () {
		// Append the following array with extra custom selectors.
		var customSelectors = ['.dataTableRow', '.dataTableHeadingRow', '.dataTableRowSelected', '.pdf_menu', '#log_content', '.contentTable', '.infoBoxHeading'];

		$.each(customSelectors, function () {
			if (!$(this).hasClass('gx-container')) {
				$(this).addClass('gx-container');
			}
		});
	});

	/**
  * Normalize tables by custom selectors.
  */
	fixes.push(function () {
		// Append the following array with extra custom selectors.
		var normalizeTables = ['#gm_box_content > table', '#gm_box_content > form > table'];

		$.each(normalizeTables, function () {
			if (!$(this).hasClass('normalize-table')) {
				$(this).addClass('normalize-table');
			}
		});
	});

	/**
  * Add extra classes to the table structure of configuration.php pages.
  */
	fixes.push(function () {
		var tablesArray = $('form[name="configuration"]').children();

		// set $saveBtn only if there is exactly one input[type="submit"]-Button
		if ($('input[type="submit"]').length === 1) {
			var $saveBtn = $('input[type="submit"]');
			$saveBtn.removeClass('button');
			if (!$saveBtn.hasClass('btn')) {
				$saveBtn.addClass('btn');
				$saveBtn.addClass('btn-primary');
			}
		}

		$.each(tablesArray, function (index, element) {
			var labelText = $(element).find('.dataTableContent_gm').first().children().first().text(),
			    $elementObj = $(element),
			    rightDataTableContent = $($elementObj.find('.dataTableContent_gm')[1]);
			$elementObj.find('tr[bgcolor]').removeAttr('bgcolor');
			$elementObj.find('.dataTableContent_gm').first().addClass('configuration-label');
			$elementObj.find('.dataTableContent_gm').first().children().first().replaceWith(labelText);

			rightDataTableContent.find('br').remove();

			$elementObj.addClass('main-table');

			if (index % 2) {
				$elementObj.addClass('even');
			} else {
				$elementObj.addClass('odd');
			}
		});
		$('.error-logging-select').removeClass('pull-left');
	});

	/**
  * Fixes for the orders table.
  *
  * Some columns swapped or hide, classes was added and some elements will be removed.
  */
	fixes.push(function () {
		var $headingBoxContainer = $('.orders_form'),
		    $orderInfoBox = $('#gm_orders');

		$.each($headingBoxContainer.children(), function (index, element) {
			$(element).addClass('hidden');
		});

		$orderInfoBox.addClass('hidden');
	});

	/**
  * Fixes for customer overview page.
  */
	fixes.push(function () {
		var $compatibilityTable = $('.gx-compatibility-table.gx-customer-overview'),
		    $pagerRow = $compatibilityTable.find('tr').last();

		$('.info-box').addClass('hidden');
		$('.customer-sort-links').addClass('hidden');
		$pagerRow.find('td').first().parent().parent().parent().appendTo($compatibilityTable.parent());
		$compatibilityTable.find('.arrow-icon').addClass('hidden');
		$compatibilityTable.find('tr').last().remove();

		// Delete guest accounts
		$('#delete-guest-accounts').on('click', function () {
			// Create confirmation dialog
			var $confirmation = $('<div>');
			var $content = $('<span>');

			$content.text(jse.core.lang.translate('CONFIRM_DELETE_GUEST_ACCOUNTS', 'admin_customers'));

			$confirmation.appendTo('body').append($content).addClass('gx-container').dialog({
				'title': jse.core.lang.translate('BUTTON_DELETE_GUEST_ACCOUNTS', 'admin_customers'),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': [{
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function click() {
						$(this).dialog('close');
					}
				}, {
					'text': jse.core.lang.translate('delete', 'buttons'),
					'class': 'btn-primary',
					'click': function click() {
						$.ajax({
							url: [window.location.origin + window.location.pathname.replace('customers.php', ''), 'request_port.php', '?module=DeleteGuestAccounts', '&token=' + $('#delete-guest-accounts').data('token')].join(''),
							type: 'GET',
							dataType: 'json',
							data: '',
							success: function success(p_result_json) {
								var t_url = window.location.href;
								if (window.location.search.search('cID=') !== -1) {
									t_url = window.location.href.replace(/[&]?cID=[\d]+/g, '');
								}

								window.location.href = t_url;

								return false;
							}
						});
					}
				}],
				'width': 420,
				'closeOnEscape': false,
				'open': function open() {
					$('.ui-dialog-titlebar-close').hide();
				}
			});
		});
	});

	/**
  * Class fixes for the products and categories overview page.
  */
	fixes.push(function () {
		var $infoBox = $('.gx-categories').find('.info-box'),
		    $sortBarRow = $('.dataTableHeadingRow_sortbar'),
		    $createNewContainer = $('.create-new-container'),
		    pageHeadingElementsArray = $('.pageSubHeading').children(),
		    tableCellArray = $('.categories_view_data'),
		    $pagerContainer = $('.articles-pager');
		$infoBox.addClass('hidden');
		$sortBarRow.addClass('hidden');
		$.each(tableCellArray, function (index, element) {
			// Replace double '-' with single one.
			var cellObj = $(element);
			if (cellObj.text() === '--') {
				cellObj.text('-');
			}
		});
		$.each(pageHeadingElementsArray, function (index, element) {
			// Page heading actions.
			$(element).addClass('hidden');
		});
		$createNewContainer.removeClass('hidden');

		$.each($pagerContainer.find('.button'), function (index, element) {
			var elementObj = $(element);
			elementObj.addClass('hidden');
			elementObj.removeClass('button');
		});
	});

	/**
  * Add Pagination styles
  */
	fixes.push(function () {
		// Define pagination area where all the pagination stuff is
		var $paginationArea = $this.find('.pagination-control').parents('table:first');

		// Add compatibility classes
		$paginationArea.addClass('gx-container paginator');
	});

	/**
  * Add extra classes to the table structure of configuration.php pages.
  */
	fixes.push(function () {
		var tablesArray = $('form[name="configuration"]').children();
		$.each(tablesArray, function (index, element) {
			var labelText = $(element).find('.dataTableContent_gm').first().children().first().text(),
			    $elementObj = $(element),
			    rightDataTableContent = $($elementObj.find('.dataTableContent_gm')[1]);
			$elementObj.find('tr[bgcolor]').removeAttr('bgcolor');
			$elementObj.find('.dataTableContent_gm').first().addClass('configuration-label');
			$elementObj.find('.dataTableContent_gm').first().children().first().replaceWith(labelText);

			rightDataTableContent.find('br').remove();

			$elementObj.addClass('main-table');

			if (index % 2) {
				$elementObj.addClass('even');
			} else {
				$elementObj.addClass('odd');
			}
		});
		$('.error-logging-select').removeClass('pull-left');
	});

	/**
  * Change class of all buttons from "button" and "admin_button_green" to "btn"
  */
	fixes.push(function () {
		var selectors = ['.button', '.admin_button_green'];

		$.each(selectors, function () {
			$(this).each(function () {
				if (!$(this).hasClass('btn')) {
					$(this).addClass('btn');
				}

				$(this).removeClass('button');
				$(this).removeClass('admin_button_green');
			});
		});
	});

	/**
  * Remove img in anchor tags with class btn
  */
	fixes.push(function () {
		$('a.btn').each(function (index, element) {
			if ($(element).find('img').length) {
				$(element).find('img').remove();
			}
		});
	});

	/**
  * Hides an empty container, that takes up space
  */
	fixes.push(function () {
		if (!$('div.orders_form :visible').text().trim().length) {
			$('div.orders_form').parents('table:first').removeProp('cellpadding');
			$('div.orders_form').parents('tr:first').find('br').remove();
			$('div.orders_form').parents('td:first').css('padding', '0');
		}
	});

	/**
  *
  */
	fixes.push(function () {
		$('table.paginator').removeProp('cellspacing').removeProp('cellpadding');
	});

	/**
  * Fix the name alignment of the customer groups
  */
	fixes.push(function () {
		$('.group_icon').find('img').css('display', 'inline-block').css('vertical-align', 'middle').css('margin-right', '8px');
	});

	/**
  * Add extra class for the modal box when a customer group should edit.
  */
	fixes.push(function () {
		var urlHelper = jse.libs.url_arguments,
		    // alias
		$form = $('form[name="customers"]');

		if (urlHelper.getCurrentFile() === 'customers.php' && urlHelper.getUrlParameters().action === 'editstatus') {
			$form.find('table').addClass('edit-customer-group-table').attr('cellpadding', '0');
		}
	});

	/**
  * Fix the warning icon element in case a checkbox is next to it
  */
	fixes.push(function () {
		var warningIcon = $('.tooltip_icon.warning');
		if ($(warningIcon).parent().parent().prev('.checkbox-switch-wrapper').length) {
			warningIcon.css('margin-left', '12px');
		}
	});

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Execute the registered fixes.
		$.each(fixes, function () {
			this();
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluaXRfY2xhc3NfZml4ZXMuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImZpeGVzIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwicHVzaCIsImhhc0NsYXNzIiwiYWRkQ2xhc3MiLCJjdXN0b21TZWxlY3RvcnMiLCJlYWNoIiwibm9ybWFsaXplVGFibGVzIiwidGFibGVzQXJyYXkiLCJjaGlsZHJlbiIsImxlbmd0aCIsIiRzYXZlQnRuIiwicmVtb3ZlQ2xhc3MiLCJpbmRleCIsImVsZW1lbnQiLCJsYWJlbFRleHQiLCJmaW5kIiwiZmlyc3QiLCJ0ZXh0IiwiJGVsZW1lbnRPYmoiLCJyaWdodERhdGFUYWJsZUNvbnRlbnQiLCJyZW1vdmVBdHRyIiwicmVwbGFjZVdpdGgiLCJyZW1vdmUiLCIkaGVhZGluZ0JveENvbnRhaW5lciIsIiRvcmRlckluZm9Cb3giLCIkY29tcGF0aWJpbGl0eVRhYmxlIiwiJHBhZ2VyUm93IiwibGFzdCIsInBhcmVudCIsImFwcGVuZFRvIiwib24iLCIkY29uZmlybWF0aW9uIiwiJGNvbnRlbnQiLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsImFwcGVuZCIsImRpYWxvZyIsImFqYXgiLCJ1cmwiLCJ3aW5kb3ciLCJsb2NhdGlvbiIsIm9yaWdpbiIsInBhdGhuYW1lIiwicmVwbGFjZSIsImpvaW4iLCJ0eXBlIiwiZGF0YVR5cGUiLCJzdWNjZXNzIiwicF9yZXN1bHRfanNvbiIsInRfdXJsIiwiaHJlZiIsInNlYXJjaCIsImhpZGUiLCIkaW5mb0JveCIsIiRzb3J0QmFyUm93IiwiJGNyZWF0ZU5ld0NvbnRhaW5lciIsInBhZ2VIZWFkaW5nRWxlbWVudHNBcnJheSIsInRhYmxlQ2VsbEFycmF5IiwiJHBhZ2VyQ29udGFpbmVyIiwiY2VsbE9iaiIsImVsZW1lbnRPYmoiLCIkcGFnaW5hdGlvbkFyZWEiLCJwYXJlbnRzIiwic2VsZWN0b3JzIiwidHJpbSIsInJlbW92ZVByb3AiLCJjc3MiLCJ1cmxIZWxwZXIiLCJsaWJzIiwidXJsX2FyZ3VtZW50cyIsIiRmb3JtIiwiZ2V0Q3VycmVudEZpbGUiLCJnZXRVcmxQYXJhbWV0ZXJzIiwiYWN0aW9uIiwiYXR0ciIsIndhcm5pbmdJY29uIiwicHJldiIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7OztBQVNBQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLGtCQURELEVBR0MsQ0FBQyxlQUFELENBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFNBQVEsRUFiVDs7O0FBZUM7Ozs7O0FBS0FDLFlBQVcsRUFwQlo7OztBQXNCQzs7Ozs7QUFLQUMsV0FBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkosSUFBN0IsQ0EzQlg7OztBQTZCQzs7Ozs7QUFLQUQsVUFBUyxFQWxDVjs7QUFvQ0E7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUksT0FBTUksSUFBTixDQUFXLFlBQVc7QUFDckIsTUFBSSxDQUFDTCxFQUFFLE1BQUYsRUFBVU0sUUFBVixDQUFtQixrQkFBbkIsQ0FBTCxFQUE2QztBQUM1Q04sS0FBRSxNQUFGLEVBQVVPLFFBQVYsQ0FBbUIsa0JBQW5CO0FBQ0E7QUFDRCxFQUpEOztBQU1BOzs7QUFHQU4sT0FBTUksSUFBTixDQUFXLFlBQVc7QUFDckI7QUFDQSxNQUFJRyxrQkFBa0IsQ0FDckIsZUFEcUIsRUFFckIsc0JBRnFCLEVBR3JCLHVCQUhxQixFQUlyQixXQUpxQixFQUtyQixjQUxxQixFQU1yQixlQU5xQixFQU9yQixpQkFQcUIsQ0FBdEI7O0FBVUFSLElBQUVTLElBQUYsQ0FBT0QsZUFBUCxFQUF3QixZQUFXO0FBQ2xDLE9BQUksQ0FBQ1Is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QU1JLElBQU4sQ0FBVyxZQUFXO0FBQ3JCLE1BQUk0RSxjQUFjakYsRUFBRSx1QkFBRixDQUFsQjtBQUNBLE1BQUlBLEVBQUVpRixXQUFGLEVBQWVqRCxNQUFmLEdBQXdCQSxNQUF4QixHQUFpQ2tELElBQWpDLENBQXNDLDBCQUF0QyxFQUFrRXJFLE1BQXRFLEVBQThFO0FBQzdFb0UsZUFBWVQsR0FBWixDQUFnQixhQUFoQixFQUErQixNQUEvQjtBQUNBO0FBQ0QsRUFMRDs7QUFPQTtBQUNBO0FBQ0E7O0FBRUEzRSxRQUFPc0YsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QjtBQUNBcEYsSUFBRVMsSUFBRixDQUFPUixLQUFQLEVBQWMsWUFBVztBQUN4QjtBQUNBLEdBRkQ7O0FBSUFtRjtBQUNBLEVBUEQ7O0FBU0EsUUFBT3ZGLE1BQVA7QUFDQSxDQW5aRiIsImZpbGUiOiJpbml0X2NsYXNzX2ZpeGVzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbml0X2NsYXNzX2ZpeGVzLmpzIDIwMTYtMDItMDMgXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBJbml0aWFsaXplIENsYXNzIEZpeGVzXG4gKlxuICogVGhpcyBtb2R1bGUgbXVzdCBzZXQgYXMgbWFueSBjb21wYXRpYmlsaXR5IGNsYXNzZXMgYXMgcG9zc2libGUuIFdoZXJldmVyIGl0IGlzXG4gKiBjZXJ0YWluIHRoYXQgYW4gSFRNTCBjbGFzcyB3aWxsIGJlIHByZXNlbnQgaXQgbXVzdCBiZSBhdXRvbWF0aWNhbGx5IHNldCBieSB0aGlzXG4gKiBtb2R1bGUuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L2luaXRfY2xhc3NfZml4ZXNcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdpbml0X2NsYXNzX2ZpeGVzJyxcblx0XG5cdFsndXJsX2FyZ3VtZW50cyddLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvaW5pdF9jbGFzc19maXhlcyAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBDYWxsYmFja3MgZm9yIGNoZWNraW5nIGNvbW1vbiBwYXR0ZXJucy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHthcnJheX1cblx0XHRcdCAqL1xuXHRcdFx0Zml4ZXMgPSBbXSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBPUEVSQVRJT05TXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQWRkIGd4LWNvbXBhdGliaWxpdHkgY2xhc3MgdG8gYm9keSBlbGVtZW50LlxuXHRcdCAqL1xuXHRcdGZpeGVzLnB1c2goZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoISQoJ2JvZHknKS5oYXNDbGFzcygnZ3gtY29tcGF0aWJpbGl0eScpKSB7XG5cdFx0XHRcdCQoJ2JvZHknKS5hZGRDbGFzcygnZ3gtY29tcGF0aWJpbGl0eScpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEFkZCB0aGUgZ3gtY29udGFpbmVyIGN1c3RvbSBwcmVkZWZpbmVkIHNlbGVjdG9ycy5cblx0XHQgKi9cblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0Ly8gQXBwZW5kIHRoZSBmb2xsb3dpbmcgYXJyYXkgd2l0aCBleHRyYSBjdXN0b20gc2VsZWN0b3JzLlxuXHRcdFx0dmFyIGN1c3RvbVNlbGVjdG9ycyA9IFtcblx0XHRcdFx0Jy5kYXRhVGFibGVSb3cnLFxuXHRcdFx0XHQnLmRhdGFUYWJsZUhlYWRpbmdSb3cnLFxuXHRcdFx0XHQnLmRhdGFUYWJsZVJvd1NlbGVjdGVkJyxcblx0XHRcdFx0Jy5wZGZfbWVudScsXG5cdFx0XHRcdCcjbG9nX2NvbnRlbnQnLFxuXHRcdFx0XHQnLmNvbnRlbnRUYWJsZScsXG5cdFx0XHRcdCcuaW5mb0JveEhlYWRpbmcnXG5cdFx0XHRdO1xuXHRcdFx0XG5cdFx0XHQkLmVhY2goY3VzdG9tU2VsZWN0b3JzLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKCEkKHRoaXMpLmhhc0NsYXNzKCdneC1jb250YWluZXInKSkge1xuXHRcdFx0XHRcdCQodGhpcykuYWRkQ2xhc3MoJ2d4LWNvbnRhaW5lcicpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBOb3JtYWxpemUgdGFibGVzIGJ5IGN1c3RvbSBzZWxlY3RvcnMuXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdC8vIEFwcGVuZCB0aGUgZm9sbG93aW5nIGFycmF5IHdpdGggZXh0cmEgY3VzdG9tIHNlbGVjdG9ycy5cblx0XHRcdHZhciBub3JtYWxpemVUYWJsZXMgPSBbXG5cdFx0XHRcdCcjZ21fYm94X2NvbnRlbnQgPiB0YWJsZScsXG5cdFx0XHRcdCcjZ21fYm94X2NvbnRlbnQgPiBmb3JtID4gdGFibGUnXG5cdFx0XHRdO1xuXHRcdFx0XG5cdFx0XHQkLmVhY2gobm9ybWFsaXplVGFibGVzLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKCEkKHRoaXMpLmhhc0NsYXNzKCdub3JtYWxpemUtdGFibGUnKSkge1xuXHRcdFx0XHRcdCQodGhpcykuYWRkQ2xhc3MoJ25vcm1hbGl6ZS10YWJsZScpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBBZGQgZXh0cmEgY2xhc3NlcyB0byB0aGUgdGFibGUgc3RydWN0dXJlIG9mIGNvbmZpZ3VyYXRpb24ucGhwIHBhZ2VzLlxuXHRcdCAqL1xuXHRcdGZpeGVzLnB1c2goZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgdGFibGVzQXJyYXkgPSAkKCdmb3JtW25hbWU9XCJjb25maWd1cmF0aW9uXCJdJykuY2hpbGRyZW4oKTtcblx0XHRcdFxuXHRcdFx0Ly8gc2V0ICRzYXZlQnRuIG9ubHkgaWYgdGhlcmUgaXMgZXhhY3RseSBvbmUgaW5wdXRbdHlwZT1cInN1Ym1pdFwiXS1CdXR0b25cblx0XHRcdGlmICgkKCdpbnB1dFt0eXBlPVwic3VibWl0XCJdJykubGVuZ3RoID09PSAxKSB7XG5cdFx0XHRcdHZhciAkc2F2ZUJ0biA9ICQoJ2lucHV0W3R5cGU9XCJzdWJtaXRcIl0nKTtcblx0XHRcdFx0JHNhdmVCdG4ucmVtb3ZlQ2xhc3MoJ2J1dHRvbicpO1xuXHRcdFx0XHRpZiAoISRzYXZlQnRuLmhhc0NsYXNzKCdidG4nKSkge1xuXHRcdFx0XHRcdCRzYXZlQnRuLmFkZENsYXNzKCdidG4nKTtcblx0XHRcdFx0XHQkc2F2ZUJ0bi5hZGRDbGFzcygnYnRuLXByaW1hcnknKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkLmVhY2godGFibGVzQXJyYXksIGZ1bmN0aW9uKGluZGV4LCBlbGVtZW50KSB7XG5cdFx0XHRcdHZhciBsYWJlbFRleHQgPSAkKGVsZW1lbnQpLmZpbmQoJy5kYXRhVGFibGVDb250ZW50X2dtJykuZmlyc3QoKS5jaGlsZHJlbigpLmZpcnN0KCkudGV4dCgpLFxuXHRcdFx0XHRcdCRlbGVtZW50T2JqID0gJChlbGVtZW50KSxcblx0XHRcdFx0XHRyaWdodERhdGFUYWJsZUNvbnRlbnQgPSAkKCRlbGVtZW50T2JqLmZpbmQoJy5kYXRhVGFibGVDb250ZW50X2dtJylbMV0pO1xuXHRcdFx0XHQkZWxlbWVudE9iai5maW5kKCd0cltiZ2NvbG9yXScpLnJlbW92ZUF0dHIoJ2JnY29sb3InKTtcblx0XHRcdFx0JGVsZW1lbnRPYmouZmluZCgnLmRhdGFUYWJsZUNvbnRlbnRfZ20nKS5maXJzdCgpLmFkZENsYXNzKCdjb25maWd1cmF0aW9uLWxhYmVsJyk7XG5cdFx0XHRcdCRlbGVtZW50T2JqLmZpbmQoJy5kYXRhVGFibGVDb250ZW50X2dtJykuZmlyc3QoKS5jaGlsZHJlbigpLmZpcnN0KCkucmVwbGFjZVdpdGgobGFiZWxUZXh0KTtcblx0XHRcdFx0XG5cdFx0XHRcdHJpZ2h0RGF0YVRhYmxlQ29udGVudC5maW5kKCdicicpLnJlbW92ZSgpO1xuXHRcdFx0XHRcblx0XHRcdFx0JGVsZW1lbnRPYmouYWRkQ2xhc3MoJ21haW4tdGFibGUnKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmIChpbmRleCAlIDIpIHtcblx0XHRcdFx0XHQkZWxlbWVudE9iai5hZGRDbGFzcygnZXZlbicpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCRlbGVtZW50T2JqLmFkZENsYXNzKCdvZGQnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHQkKCcuZXJyb3ItbG9nZ2luZy1zZWxlY3QnKS5yZW1vdmVDbGFzcygncHVsbC1sZWZ0Jyk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRml4ZXMgZm9yIHRoZSBvcmRlcnMgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBTb21lIGNvbHVtbnMgc3dhcHBlZCBvciBoaWRlLCBjbGFzc2VzIHdhcyBhZGRlZCBhbmQgc29tZSBlbGVtZW50cyB3aWxsIGJlIHJlbW92ZWQuXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkaGVhZGluZ0JveENvbnRhaW5lciA9ICQoJy5vcmRlcnNfZm9ybScpLFxuXHRcdFx0XHQkb3JkZXJJbmZvQm94ID0gJCgnI2dtX29yZGVycycpO1xuXHRcdFx0XG5cdFx0XHQkLmVhY2goJGhlYWRpbmdCb3hDb250YWluZXIuY2hpbGRyZW4oKSwgZnVuY3Rpb24oaW5kZXgsIGVsZW1lbnQpIHtcblx0XHRcdFx0JChlbGVtZW50KS5hZGRDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0JG9yZGVySW5mb0JveC5hZGRDbGFzcygnaGlkZGVuJyk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRml4ZXMgZm9yIGN1c3RvbWVyIG92ZXJ2aWV3IHBhZ2UuXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkY29tcGF0aWJpbGl0eVRhYmxlID0gJCgnLmd4LWNvbXBhdGliaWxpdHktdGFibGUuZ3gtY3VzdG9tZXItb3ZlcnZpZXcnKSxcblx0XHRcdFx0JHBhZ2VyUm93ID0gJGNvbXBhdGliaWxpdHlUYWJsZS5maW5kKCd0cicpLmxhc3QoKTtcblx0XHRcdFxuXHRcdFx0JCgnLmluZm8tYm94JykuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0JCgnLmN1c3RvbWVyLXNvcnQtbGlua3MnKS5hZGRDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHQkcGFnZXJSb3cuZmluZCgndGQnKS5maXJzdCgpLnBhcmVudCgpLnBhcmVudCgpLnBhcmVudCgpLmFwcGVuZFRvKCRjb21wYXRpYmlsaXR5VGFibGUucGFyZW50KCkpO1xuXHRcdFx0JGNvbXBhdGliaWxpdHlUYWJsZS5maW5kKCcuYXJyb3ctaWNvbicpLmFkZENsYXNzKCdoaWRkZW4nKTtcblx0XHRcdCRjb21wYXRpYmlsaXR5VGFibGUuZmluZCgndHInKS5sYXN0KCkucmVtb3ZlKCk7XG5cdFx0XHRcblx0XHRcdC8vIERlbGV0ZSBndWVzdCBhY2NvdW50c1xuXHRcdFx0JCgnI2RlbGV0ZS1ndWVzdC1hY2NvdW50cycpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQvLyBDcmVhdGUgY29uZmlybWF0aW9uIGRpYWxvZ1xuXHRcdFx0XHR2YXIgJGNvbmZpcm1hdGlvbiA9ICQoJzxkaXY+Jyk7XG5cdFx0XHRcdHZhciAkY29udGVudCA9ICQoJzxzcGFuPicpO1xuXHRcdFx0XHRcblx0XHRcdFx0JGNvbnRlbnRcblx0XHRcdFx0XHQudGV4dChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQ09ORklSTV9ERUxFVEVfR1VFU1RfQUNDT1VOVFMnLCAnYWRtaW5fY3VzdG9tZXJzJykpO1xuXHRcdFx0XHRcblx0XHRcdFx0JGNvbmZpcm1hdGlvblxuXHRcdFx0XHRcdC5hcHBlbmRUbygnYm9keScpXG5cdFx0XHRcdFx0LmFwcGVuZCgkY29udGVudClcblx0XHRcdFx0XHQuYWRkQ2xhc3MoJ2d4LWNvbnRhaW5lcicpXG5cdFx0XHRcdFx0LmRpYWxvZyh7XG5cdFx0XHRcdFx0XHQndGl0bGUnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQlVUVE9OX0RFTEVURV9HVUVTVF9BQ0NPVU5UUycsICdhZG1pbl9jdXN0b21lcnMnKSxcblx0XHRcdFx0XHRcdCdtb2RhbCc6IHRydWUsXG5cdFx0XHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0XHRcdCdidXR0b25zJzogW1xuXHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZGVsZXRlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuLXByaW1hcnknLFxuXHRcdFx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0JC5hamF4KHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0dXJsOiBbXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0KHdpbmRvdy5sb2NhdGlvbi5vcmlnaW5cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHQrIHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZS5yZXBsYWNlKCdjdXN0b21lcnMucGhwJyxcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdCcnKSksXG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0J3JlcXVlc3RfcG9ydC5waHAnLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCc/bW9kdWxlPURlbGV0ZUd1ZXN0QWNjb3VudHMnLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdCcmdG9rZW49JyArICQoJyNkZWxldGUtZ3Vlc3QtYWNjb3VudHMnKS5kYXRhKCd0b2tlbicpLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRdLmpvaW4oJycpLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHR0eXBlOiAnR0VUJyxcblx0XHRcdFx0XHRcdFx0XHRcdFx0ZGF0YVR5cGU6ICdqc29uJyxcblx0XHRcdFx0XHRcdFx0XHRcdFx0ZGF0YTogJycsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdHN1Y2Nlc3M6IGZ1bmN0aW9uKHBfcmVzdWx0X2pzb24pIHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHR2YXIgdF91cmwgPSB3aW5kb3cubG9jYXRpb24uaHJlZjtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRpZiAod2luZG93LmxvY2F0aW9uLnNlYXJjaC5zZWFyY2goJ2NJRD0nKSAhPT0gLTEpIHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdHRfdXJsID1cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcdFx0d2luZG93LmxvY2F0aW9uLmhyZWYucmVwbGFjZSgvWyZdP2NJRD1bXFxkXSsvZywgJycpO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdFx0XHR3aW5kb3cubG9jYXRpb24uaHJlZiA9IHRfdXJsO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRdLFxuXHRcdFx0XHRcdFx0J3dpZHRoJzogNDIwLFxuXHRcdFx0XHRcdFx0J2Nsb3NlT25Fc2NhcGUnOiBmYWxzZSxcblx0XHRcdFx0XHRcdCdvcGVuJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdCQoJy51aS1kaWFsb2ctdGl0bGViYXItY2xvc2UnKS5oaWRlKCk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDbGFzcyBmaXhlcyBmb3IgdGhlIHByb2R1Y3RzIGFuZCBjYXRlZ29yaWVzIG92ZXJ2aWV3IHBhZ2UuXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkaW5mb0JveCA9ICQoJy5neC1jYXRlZ29yaWVzJykuZmluZCgnLmluZm8tYm94JyksXG5cdFx0XHRcdCRzb3J0QmFyUm93ID0gJCgnLmRhdGFUYWJsZUhlYWRpbmdSb3dfc29ydGJhcicpLFxuXHRcdFx0XHQkY3JlYXRlTmV3Q29udGFpbmVyID0gJCgnLmNyZWF0ZS1uZXctY29udGFpbmVyJyksXG5cdFx0XHRcdHBhZ2VIZWFkaW5nRWxlbWVudHNBcnJheSA9ICQoJy5wYWdlU3ViSGVhZGluZycpLmNoaWxkcmVuKCksXG5cdFx0XHRcdHRhYmxlQ2VsbEFycmF5ID0gJCgnLmNhdGVnb3JpZXNfdmlld19kYXRhJyksXG5cdFx0XHRcdCRwYWdlckNvbnRhaW5lciA9ICQoJy5hcnRpY2xlcy1wYWdlcicpO1xuXHRcdFx0JGluZm9Cb3guYWRkQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0JHNvcnRCYXJSb3cuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0JC5lYWNoKHRhYmxlQ2VsbEFycmF5LCBmdW5jdGlvbihpbmRleCwgZWxlbWVudCkgeyAvLyBSZXBsYWNlIGRvdWJsZSAnLScgd2l0aCBzaW5nbGUgb25lLlxuXHRcdFx0XHR2YXIgY2VsbE9iaiA9ICQoZWxlbWVudCk7XG5cdFx0XHRcdGlmIChjZWxsT2JqLnRleHQoKSA9PT0gJy0tJykge1xuXHRcdFx0XHRcdGNlbGxPYmoudGV4dCgnLScpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdCQuZWFjaChwYWdlSGVhZGluZ0VsZW1lbnRzQXJyYXksIGZ1bmN0aW9uKGluZGV4LCBlbGVtZW50KSB7IC8vIFBhZ2UgaGVhZGluZyBhY3Rpb25zLlxuXHRcdFx0XHQkKGVsZW1lbnQpLmFkZENsYXNzKCdoaWRkZW4nKTtcblx0XHRcdH0pO1xuXHRcdFx0JGNyZWF0ZU5ld0NvbnRhaW5lci5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHRcblx0XHRcdCQuZWFjaCgkcGFnZXJDb250YWluZXIuZmluZCgnLmJ1dHRvbicpLCBmdW5jdGlvbihpbmRleCwgZWxlbWVudCkge1xuXHRcdFx0XHR2YXIgZWxlbWVudE9iaiA9ICQoZWxlbWVudCk7XG5cdFx0XHRcdGVsZW1lbnRPYmouYWRkQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0XHRlbGVtZW50T2JqLnJlbW92ZUNsYXNzKCdidXR0b24nKTtcblx0XHRcdH0pO1xuXHRcdH0pO1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEFkZCBQYWdpbmF0aW9uIHN0eWxlc1xuXHRcdCAqL1xuXHRcdGZpeGVzLnB1c2goZnVuY3Rpb24oKSB7XG5cdFx0XHQvLyBEZWZpbmUgcGFnaW5hdGlvbiBhcmVhIHdoZXJlIGFsbCB0aGUgcGFnaW5hdGlvbiBzdHVmZiBpc1xuXHRcdFx0dmFyICRwYWdpbmF0aW9uQXJlYSA9ICR0aGlzXG5cdFx0XHRcdC5maW5kKCcucGFnaW5hdGlvbi1jb250cm9sJylcblx0XHRcdFx0LnBhcmVudHMoJ3RhYmxlOmZpcnN0Jyk7XG5cdFx0XHRcblx0XHRcdC8vIEFkZCBjb21wYXRpYmlsaXR5IGNsYXNzZXNcblx0XHRcdCRwYWdpbmF0aW9uQXJlYS5hZGRDbGFzcygnZ3gtY29udGFpbmVyIHBhZ2luYXRvcicpO1xuXHRcdFx0XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQWRkIGV4dHJhIGNsYXNzZXMgdG8gdGhlIHRhYmxlIHN0cnVjdHVyZSBvZiBjb25maWd1cmF0aW9uLnBocCBwYWdlcy5cblx0XHQgKi9cblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHRhYmxlc0FycmF5ID0gJCgnZm9ybVtuYW1lPVwiY29uZmlndXJhdGlvblwiXScpLmNoaWxkcmVuKCk7XG5cdFx0XHQkLmVhY2godGFibGVzQXJyYXksIGZ1bmN0aW9uKGluZGV4LCBlbGVtZW50KSB7XG5cdFx0XHRcdHZhciBsYWJlbFRleHQgPSAkKGVsZW1lbnQpLmZpbmQoJy5kYXRhVGFibGVDb250ZW50X2dtJykuZmlyc3QoKS5jaGlsZHJlbigpLmZpcnN0KCkudGV4dCgpLFxuXHRcdFx0XHRcdCRlbGVtZW50T2JqID0gJChlbGVtZW50KSxcblx0XHRcdFx0XHRyaWdodERhdGFUYWJsZUNvbnRlbnQgPSAkKCRlbGVtZW50T2JqLmZpbmQoJy5kYXRhVGFibGVDb250ZW50X2dtJylbMV0pO1xuXHRcdFx0XHQkZWxlbWVudE9iai5maW5kKCd0cltiZ2NvbG9yXScpLnJlbW92ZUF0dHIoJ2JnY29sb3InKTtcblx0XHRcdFx0JGVsZW1lbnRPYmouZmluZCgnLmRhdGFUYWJsZUNvbnRlbnRfZ20nKS5maXJzdCgpLmFkZENsYXNzKCdjb25maWd1cmF0aW9uLWxhYmVsJyk7XG5cdFx0XHRcdCRlbGVtZW50T2JqLmZpbmQoJy5kYXRhVGFibGVDb250ZW50X2dtJykuZmlyc3QoKS5jaGlsZHJlbigpLmZpcnN0KCkucmVwbGFjZVdpdGgobGFiZWxUZXh0KTtcblx0XHRcdFx0XG5cdFx0XHRcdHJpZ2h0RGF0YVRhYmxlQ29udGVudC5maW5kKCdicicpLnJlbW92ZSgpO1xuXHRcdFx0XHRcblx0XHRcdFx0JGVsZW1lbnRPYmouYWRkQ2xhc3MoJ21haW4tdGFibGUnKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmIChpbmRleCAlIDIpIHtcblx0XHRcdFx0XHQkZWxlbWVudE9iai5hZGRDbGFzcygnZXZlbicpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCRlbGVtZW50T2JqLmFkZENsYXNzKCdvZGQnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHQkKCcuZXJyb3ItbG9nZ2luZy1zZWxlY3QnKS5yZW1vdmVDbGFzcygncHVsbC1sZWZ0Jyk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ2hhbmdlIGNsYXNzIG9mIGFsbCBidXR0b25zIGZyb20gXCJidXR0b25cIiBhbmQgXCJhZG1pbl9idXR0b25fZ3JlZW5cIiB0byBcImJ0blwiXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciBzZWxlY3RvcnMgPSBbXG5cdFx0XHRcdCcuYnV0dG9uJyxcblx0XHRcdFx0Jy5hZG1pbl9idXR0b25fZ3JlZW4nXG5cdFx0XHRdO1xuXHRcdFx0XG5cdFx0XHQkLmVhY2goc2VsZWN0b3JzLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JCh0aGlzKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdGlmICghJCh0aGlzKS5oYXNDbGFzcygnYnRuJykpIHtcblx0XHRcdFx0XHRcdCQodGhpcykuYWRkQ2xhc3MoJ2J0bicpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkKHRoaXMpLnJlbW92ZUNsYXNzKCdidXR0b24nKTtcblx0XHRcdFx0XHQkKHRoaXMpLnJlbW92ZUNsYXNzKCdhZG1pbl9idXR0b25fZ3JlZW4nKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBSZW1vdmUgaW1nIGluIGFuY2hvciB0YWdzIHdpdGggY2xhc3MgYnRuXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdCQoJ2EuYnRuJykuZWFjaChmdW5jdGlvbihpbmRleCwgZWxlbWVudCkge1xuXHRcdFx0XHRpZiAoJChlbGVtZW50KS5maW5kKCdpbWcnKS5sZW5ndGgpIHtcblx0XHRcdFx0XHQkKGVsZW1lbnQpXG5cdFx0XHRcdFx0XHQuZmluZCgnaW1nJylcblx0XHRcdFx0XHRcdC5yZW1vdmUoKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSGlkZXMgYW4gZW1wdHkgY29udGFpbmVyLCB0aGF0IHRha2VzIHVwIHNwYWNlXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdGlmICghJCgnZGl2Lm9yZGVyc19mb3JtIDp2aXNpYmxlJykudGV4dCgpLnRyaW0oKS5sZW5ndGgpIHtcblx0XHRcdFx0JCgnZGl2Lm9yZGVyc19mb3JtJykucGFyZW50cygndGFibGU6Zmlyc3QnKS5yZW1vdmVQcm9wKCdjZWxscGFkZGluZycpO1xuXHRcdFx0XHQkKCdkaXYub3JkZXJzX2Zvcm0nKS5wYXJlbnRzKCd0cjpmaXJzdCcpLmZpbmQoJ2JyJykucmVtb3ZlKCk7XG5cdFx0XHRcdCQoJ2Rpdi5vcmRlcnNfZm9ybScpLnBhcmVudHMoJ3RkOmZpcnN0JykuY3NzKCdwYWRkaW5nJywgJzAnKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKlxuXHRcdCAqL1xuXHRcdGZpeGVzLnB1c2goZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCd0YWJsZS5wYWdpbmF0b3InKS5yZW1vdmVQcm9wKCdjZWxsc3BhY2luZycpLnJlbW92ZVByb3AoJ2NlbGxwYWRkaW5nJyk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRml4IHRoZSBuYW1lIGFsaWdubWVudCBvZiB0aGUgY3VzdG9tZXIgZ3JvdXBzXG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdCQoJy5ncm91cF9pY29uJykuZmluZCgnaW1nJylcblx0XHRcdFx0LmNzcygnZGlzcGxheScsICdpbmxpbmUtYmxvY2snKVxuXHRcdFx0XHQuY3NzKCd2ZXJ0aWNhbC1hbGlnbicsICdtaWRkbGUnKVxuXHRcdFx0XHQuY3NzKCdtYXJnaW4tcmlnaHQnLCAnOHB4Jyk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQWRkIGV4dHJhIGNsYXNzIGZvciB0aGUgbW9kYWwgYm94IHdoZW4gYSBjdXN0b21lciBncm91cCBzaG91bGQgZWRpdC5cblx0XHQgKi9cblx0XHRmaXhlcy5wdXNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHVybEhlbHBlciA9IGpzZS5saWJzLnVybF9hcmd1bWVudHMsIC8vIGFsaWFzXG5cdFx0XHRcdCRmb3JtID0gJCgnZm9ybVtuYW1lPVwiY3VzdG9tZXJzXCJdJyk7XG5cdFx0XHRcblx0XHRcdGlmICh1cmxIZWxwZXIuZ2V0Q3VycmVudEZpbGUoKSA9PT0gJ2N1c3RvbWVycy5waHAnICYmIHVybEhlbHBlci5nZXRVcmxQYXJhbWV0ZXJzKCkuYWN0aW9uID09PVxuXHRcdFx0XHQnZWRpdHN0YXR1cycpIHtcblx0XHRcdFx0JGZvcm0uZmluZCgndGFibGUnKS5hZGRDbGFzcygnZWRpdC1jdXN0b21lci1ncm91cC10YWJsZScpLmF0dHIoJ2NlbGxwYWRkaW5nJywgJzAnKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBGaXggdGhlIHdhcm5pbmcgaWNvbiBlbGVtZW50IGluIGNhc2UgYSBjaGVja2JveCBpcyBuZXh0IHRvIGl0XG5cdFx0ICovXG5cdFx0Zml4ZXMucHVzaChmdW5jdGlvbigpIHtcblx0XHRcdHZhciB3YXJuaW5nSWNvbiA9ICQoJy50b29sdGlwX2ljb24ud2FybmluZycpO1xuXHRcdFx0aWYgKCQod2FybmluZ0ljb24pLnBhcmVudCgpLnBhcmVudCgpLnByZXYoJy5jaGVja2JveC1zd2l0Y2gtd3JhcHBlcicpLmxlbmd0aCkge1xuXHRcdFx0XHR3YXJuaW5nSWNvbi5jc3MoJ21hcmdpbi1sZWZ0JywgJzEycHgnKTtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Ly8gRXhlY3V0ZSB0aGUgcmVnaXN0ZXJlZCBmaXhlcy5cblx0XHRcdCQuZWFjaChmaXhlcywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHRoaXMoKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
