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
gx.compatibility.module(
	'init_class_fixes',
	
	['url_arguments'],
	
	/**  @lends module:Compatibility/init_class_fixes */
	
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
		fixes.push(function() {
			if (!$('body').hasClass('gx-compatibility')) {
				$('body').addClass('gx-compatibility');
			}
		});
		
		/**
		 * Add the gx-container custom predefined selectors.
		 */
		fixes.push(function() {
			// Append the following array with extra custom selectors.
			var customSelectors = [
				'.dataTableRow',
				'.dataTableHeadingRow',
				'.dataTableRowSelected',
				'.pdf_menu',
				'#log_content',
				'.contentTable',
				'.infoBoxHeading'
			];
			
			$.each(customSelectors, function() {
				if (!$(this).hasClass('gx-container')) {
					$(this).addClass('gx-container');
				}
			});
		});
		
		/**
		 * Normalize tables by custom selectors.
		 */
		fixes.push(function() {
			// Append the following array with extra custom selectors.
			var normalizeTables = [
				'#gm_box_content > table',
				'#gm_box_content > form > table'
			];
			
			$.each(normalizeTables, function() {
				if (!$(this).hasClass('normalize-table')) {
					$(this).addClass('normalize-table');
				}
			});
		});
		
		/**
		 * Add extra classes to the table structure of configuration.php pages.
		 */
		fixes.push(function() {
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
			
			$.each(tablesArray, function(index, element) {
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
		fixes.push(function() {
			var $headingBoxContainer = $('.orders_form'),
				$orderInfoBox = $('#gm_orders');
			
			$.each($headingBoxContainer.children(), function(index, element) {
				$(element).addClass('hidden');
			});
			
			$orderInfoBox.addClass('hidden');
		});
		
		/**
		 * Fixes for customer overview page.
		 */
		fixes.push(function() {
			var $compatibilityTable = $('.gx-compatibility-table.gx-customer-overview'),
				$pagerRow = $compatibilityTable.find('tr').last();
			
			$('.info-box').addClass('hidden');
			$('.customer-sort-links').addClass('hidden');
			$pagerRow.find('td').first().parent().parent().parent().appendTo($compatibilityTable.parent());
			$compatibilityTable.find('.arrow-icon').addClass('hidden');
			$compatibilityTable.find('tr').last().remove();
			
			// Delete guest accounts
			$('#delete-guest-accounts').on('click', function() {
				// Create confirmation dialog
				var $confirmation = $('<div>');
				var $content = $('<span>');
				
				$content
					.text(jse.core.lang.translate('CONFIRM_DELETE_GUEST_ACCOUNTS', 'admin_customers'));
				
				$confirmation
					.appendTo('body')
					.append($content)
					.addClass('gx-container')
					.dialog({
						'title': jse.core.lang.translate('BUTTON_DELETE_GUEST_ACCOUNTS', 'admin_customers'),
						'modal': true,
						'dialogClass': 'gx-container',
						'buttons': [
							{
								'text': jse.core.lang.translate('close', 'buttons'),
								'class': 'btn',
								'click': function() {
									$(this).dialog('close');
								}
							},
							{
								'text': jse.core.lang.translate('delete', 'buttons'),
								'class': 'btn-primary',
								'click': function() {
									$.ajax({
										url: [
											(window.location.origin
											+ window.location.pathname.replace('customers.php',
												'')),
											'request_port.php',
											'?module=DeleteGuestAccounts',
											'&token=' + $('#delete-guest-accounts').data('token'),
										].join(''),
										type: 'GET',
										dataType: 'json',
										data: '',
										success: function(p_result_json) {
											var t_url = window.location.href;
											if (window.location.search.search('cID=') !== -1) {
												t_url =
													window.location.href.replace(/[&]?cID=[\d]+/g, '');
											}
											
											window.location.href = t_url;
											
											return false;
										}
									});
								}
							}
						],
						'width': 420,
						'closeOnEscape': false,
						'open': function() {
							$('.ui-dialog-titlebar-close').hide();
						}
					});
			});
		});
		
		/**
		 * Class fixes for the products and categories overview page.
		 */
		fixes.push(function() {
			var $infoBox = $('.gx-categories').find('.info-box'),
				$sortBarRow = $('.dataTableHeadingRow_sortbar'),
				$createNewContainer = $('.create-new-container'),
				pageHeadingElementsArray = $('.pageSubHeading').children(),
				tableCellArray = $('.categories_view_data'),
				$pagerContainer = $('.articles-pager');
			$infoBox.addClass('hidden');
			$sortBarRow.addClass('hidden');
			$.each(tableCellArray, function(index, element) { // Replace double '-' with single one.
				var cellObj = $(element);
				if (cellObj.text() === '--') {
					cellObj.text('-');
				}
			});
			$.each(pageHeadingElementsArray, function(index, element) { // Page heading actions.
				$(element).addClass('hidden');
			});
			$createNewContainer.removeClass('hidden');
			
			$.each($pagerContainer.find('.button'), function(index, element) {
				var elementObj = $(element);
				elementObj.addClass('hidden');
				elementObj.removeClass('button');
			});
		});
		
		/**
		 * Add Pagination styles
		 */
		fixes.push(function() {
			// Define pagination area where all the pagination stuff is
			var $paginationArea = $this
				.find('.pagination-control')
				.parents('table:first');
			
			// Add compatibility classes
			$paginationArea.addClass('gx-container paginator');
			
		});
		
		/**
		 * Add extra classes to the table structure of configuration.php pages.
		 */
		fixes.push(function() {
			var tablesArray = $('form[name="configuration"]').children();
			$.each(tablesArray, function(index, element) {
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
		fixes.push(function() {
			var selectors = [
				'.button',
				'.admin_button_green'
			];
			
			$.each(selectors, function() {
				$(this).each(function() {
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
		fixes.push(function() {
			$('a.btn').each(function(index, element) {
				if ($(element).find('img').length) {
					$(element)
						.find('img')
						.remove();
				}
			});
		});
		
		/**
		 * Hides an empty container, that takes up space
		 */
		fixes.push(function() {
			if (!$('div.orders_form :visible').text().trim().length) {
				$('div.orders_form').parents('table:first').removeProp('cellpadding');
				$('div.orders_form').parents('tr:first').find('br').remove();
				$('div.orders_form').parents('td:first').css('padding', '0');
			}
		});
		
		/**
		 *
		 */
		fixes.push(function() {
			$('table.paginator').removeProp('cellspacing').removeProp('cellpadding');
		});
		
		/**
		 * Fix the name alignment of the customer groups
		 */
		fixes.push(function() {
			$('.group_icon').find('img')
				.css('display', 'inline-block')
				.css('vertical-align', 'middle')
				.css('margin-right', '8px');
		});
		
		/**
		 * Add extra class for the modal box when a customer group should edit.
		 */
		fixes.push(function() {
			var urlHelper = jse.libs.url_arguments, // alias
				$form = $('form[name="customers"]');
			
			if (urlHelper.getCurrentFile() === 'customers.php' && urlHelper.getUrlParameters().action ===
				'editstatus') {
				$form.find('table').addClass('edit-customer-group-table').attr('cellpadding', '0');
			}
		});
		
		/**
		 * Fix the warning icon element in case a checkbox is next to it
		 */
		fixes.push(function() {
			var warningIcon = $('.tooltip_icon.warning');
			if ($(warningIcon).parent().parent().prev('.checkbox-switch-wrapper').length) {
				warningIcon.css('margin-left', '12px');
			}
		});
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			// Execute the registered fixes.
			$.each(fixes, function() {
				this();
			});
			
			done();
		};
		
		return module;
	});
