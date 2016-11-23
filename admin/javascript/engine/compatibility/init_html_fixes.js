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
gx.compatibility.module(
	'init_html_fixes',
	
	['url_arguments'],
	
	/**  @lends module:Compatibility/init_html_fixes */
	
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
		fixes.push(function() {
			$('.pageHeading').eq(1).removeClass('pageHeading');
			$('.boxCenter:not(.no-wrap)').wrapInner('<div class="boxCenterWrapper"></div>');
			$('.pageHeading:first-child').prependTo('.boxCenter');
			$('.pageHeading').css('float', 'none');
			
			var $firstChild = $($('.boxCenterWrapper').children()[0]);
			
			if ($firstChild.is('br')) {
				$firstChild.remove();
			}
			
			if ($('div.gx-configuration-box').length) {
				$('.boxCenterWrapper')
					.wrap('<div class="boxCenterAndConfigurationWrapper" style="overflow: auto"></div>');
				$('.boxCenterAndConfigurationWrapper').append($('div.gx-configuration-box'));
			}
		});
		
		/**
		 * Remove unnecessary <br> tag after page wrapper element.
		 */
		fixes.push(function() {
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
		fixes.push(function() {
			var $columnLeft2 = $('.main-left-menu').parent('td');
			if (!$columnLeft2.hasClass('columnLeft2')) {
				$columnLeft2.addClass('columnLeft2');
			}
		});
		
		/**
		 * Remove width attribute from ".columnLeft2" element.
		 */
		fixes.push(function() {
			$('.columnLeft2').removeAttr('width');
		});
		
		/**
		 * Move message stack container to correct place.
		 */
		fixes.push(function() {
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
		fixes.push(function() {
			// Checks if current page is order and return immediately if its not the case
			var isCurrentPage = (window.location.href.indexOf('orders.php') > -1);
			
			if (!isCurrentPage) {
				return;
			}
			
			// Prepare customer link
			var customerLinkPrefix = window.location.href
				.replace(window.location.search, '')
				.replace('orders.php', 'customers.php?action=edit&cID=');
			
			// Do the modifications on the table rows
			var rowsSelectors = [
				'tr.gx-container.dataTableRowSelected',
				'tr.gx-container.dataTableRow'
			].join(', ');
			var $rows = $this.find(rowsSelectors);
			
			// Remove the on click event on the entire row add special events
			$rows.each(function(index, element) {
				// Extract order link from element
				var orderLink = $(element)
					.find('td[onclick]:first')
					.attr('onclick');
				if (typeof orderLink !== 'undefined') {
					orderLink
						.replace('document.location.href="', '')
						.replace('&action=edit', '')
						.slice(0, -1);
				}
				
				// Customer ID
				var customerId = $(this)
					.find('a[data-customer-id]')
					.data('customerId');
				
				// Remove onclick attributes from elements
				$(element)
					.find('[onclick]')
					.removeAttr('onclick');
			});
		});
		
		/**
		 * Remove inline class javascript changes.
		 */
		fixes.push(function() {
			var selectors = [
				'.dataTableRow',
				'.dataTableRowSelected'
			];
			
			$.each(selectors, function() {
				$(this).each(function() {
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
		fixes.push(function() {
			$('#old-category-table').remove();
		});
		
		/**
		 * Orders form fix.
		 */
		fixes.push(function() {
			var $headingBoxContainer = $('.orders_form');
			$.each($headingBoxContainer.children(), function(index, element) {
				$(element).addClass('hidden');
			});
		});
		
		/**
		 * Fix margins and cell spacing of left menu
		 */
		fixes.push(function() {
			$('.columnLeft2')
				.parents('table:first')
				.css({
					'border-spacing': 0
				});
		});
		
		fixes.push(function() {
			var urlHelper = jse.libs.url_arguments;
			
			if (urlHelper.getCurrentFile() === 'categories.php') {
				$('.columnLeft2')
					.parents('table:first')
					.css('width', '');
			}
		});
		
		fixes.push(function() {
			var urlHelper = jse.libs.url_arguments;
			var file = urlHelper.getCurrentFile(),
				doParameter = urlHelper.getUrlParameters().do || '',
				largePages = [
					'gm_emails.php'
				],
				smallPages = [
					'gm_seo_boost.php',
					'parcel_services.php'
				];
			
			if ($.inArray(file, largePages) > -1
				|| (file === 'admin.php' && $.inArray(doParameter, largePages) > -1)) {
				$('.boxCenterWrapper')
					.addClass('breakpoint-large');
			}
			
			if ($.inArray(file, smallPages) > -1
				|| (file === 'admin.php' && $.inArray(doParameter, smallPages) > -1)) {
				$('.boxCenterWrapper')
					.addClass('breakpoint-small');
			}
		});
		
		/**
		 * Helper to add css breakpoint classes to pages which use the controller mechanism.
		 * Extend whether the array 'largePages' or 'smallPages' to add the breakpoint class.
		 * Add as element the controller name (like in the url behind do=) and the action with trailing slash.
		 * (the action is the string in the 'do' argument behind the slash)
		 */
		fixes.push(function() {
			var urlHelper = jse.libs.url_arguments,
				currentFile = urlHelper.getCurrentFile(),
				controllerAction = urlHelper.getUrlParameters().do,
				largePages = [],
				smallPages = ['JanolawModuleCenterModule/Config'];
			
			if (currentFile === 'admin.php') {
				
				if ($.inArray(controllerAction, largePages) > -1) {
					$('#container')
						.addClass('breakpoint-large');
				}
				
				if ($.inArray(controllerAction, smallPages) > -1) {
					$('#container')
						.addClass('breakpoint-small');
				}
			}
		});
		
		/**
		 * Cleans the header of the configuration box from tables
		 */
		fixes.push(function() {
			var $contents = $('div.configuration-box-header h2 table.contentTable tr td > *');
			$contents.each(function(index, elem) {
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
		fixes.push(function() {
			var selectors = [
				'table .categories_view_data input:checkbox',
				'table .dataTableHeadingRow td input:checkbox',
				'table thead tr th:first input:checkbox',
				'table.gx-orders-table tr:not(.dataTableHeadingRow) td:first-child input:checkbox',
				'form[name="quantity_units"] input:checkbox',
				'form[name="sliderset"] input:checkbox',
				'form[name="featurecontrol"] input:checkbox:not(.checkbox-switcher)',
				'.feature-table tr td:last-child input:checkbox'
			];
			
			if ($(selectors).length > 120) {
				return;
			}
			
			$.each(selectors, function() {
				$(this).each(function() {
					if (!$(this).parent().hasClass('single-checkbox')) {
						$(this)
							.attr('data-single_checkbox', '')
							.parent().attr('data-gx-widget', 'checkbox');
						gx.widgets.init($(this).parent());
					}
				});
			});
		});
		
		/**
		 * Make the top header bar clickable to activate the search bar
		 */
		fixes.push(function() {
			var $topHeader = $('.top-header'),
				$searchInput = $('input[name="admin_search"]');
			
			$topHeader.on('click', function(event) {
				if ($topHeader.is(event.target)) {
					$searchInput.trigger('click');
				}
			});
			
		});
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			// Execute all the existing fixes.
			$.each(fixes, function() {
				this();
			});
			
			done();
		};
		
		return module;
	});
