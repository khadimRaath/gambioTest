/* --------------------------------------------------------------
 customers_table_controller.js.js 2016-03-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Customers Table Controller
 *
 * This controller contains the mapping logic of the customers table.
 *
 * @module Compatibility/customers_table_controller
 */
gx.compatibility.module(
	'customers_table_controller',
	
	[
		gx.source + '/libs/button_dropdown'
	],
	
	/**  @lends module:Compatibility/customers_table_controller */
	
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
			module = {},
			
			/**
			 * Reference to the actual file
			 *
			 * @var {string}
			 */
			srcPath = window.location.origin + window.location.pathname,
		
			/**
			 * Query parameter string
			 * 
			 * @type {string}
			 */
			queryString = '?' + (window.location.search
								.replace(/\?/, '')
								.replace(/cID=[\d]+/g, '')
								.replace(/action=[\w]+/g, '')
								.concat('&')
								.replace(/&[&]+/g, '&')
								.replace(/^&/g, ''));
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		/**
		 * Map actions for every row in the table.
		 *
		 * This method will map the actions for each
		 * row of the table.
		 *
		 * @private
		 */
		var _mapRowActions = function() {
			// Iterate over table rows, except the header row
			$('.gx-customer-overview tr').not('.dataTableHeadingRow').each(function() {
				
				/**
				 * Save that "this" scope here
				 * @var {object | jQuery}
				 */
				var $that = $(this);
				
				/**
				 * Data attributes of current row
				 * @var {object}
				 */
				var data = $that.data();
				
				/**
				 * Reference to the row action dropdown
				 * @var {object | jQuery}
				 */
				var $dropdown = $that.find('.js-button-dropdown');
				
				if ($dropdown.length) {
					
					// Add click event to the table row and open the
					// customer detail view
					$that
						.find('.btn-edit').closest('tr')
						.css({
							cursor: 'pointer'
						})
						.on('click', function(event) {
							// Compose the URL and open it
							var url = [
								srcPath,
								'?cID=' + data.rowId,
								'&action=edit'
							].join('');
							if ($(event.target).prop('tagName') === 'TD') {
								window.open(url, '_self');
							}
						});
					
					// Icon behavior - Edit
					$that
						.find('.btn-edit')
						.css({
							cursor: 'pointer'
						})
						.prop('title', jse.core.lang.translate('edit', 'buttons'))
						.on('click', function() {
							// Compose the URL and open it
							var url = [
								srcPath,
								'?cID=' + data.rowId,
								'&action=edit'
							].join('');
							window.open(url, '_self');
						});
					
					// Icon behavior - Delete
					if (data.rowId !== 1) {
						$that
							.find('.btn-delete')
							.css({
								cursor: 'pointer'
							})
							.prop('title', jse.core.lang.translate('delete', 'buttons'))
							.on('click', function() {
								// Compose the URL and open it
								var url = [
									srcPath,
									queryString,
									'cID=' + data.rowId,
									'&action=confirm'
								].join('');
								window.open(url, '_self');
							});
					}
					
					if (data.rowId === 1) {
						$that.find('.btn-delete').css({
							opacity: '0.2'
						});
					}
					
					// Icon behavior - Orders
					$that
						.find('.btn-order')
						.css({
							cursor: 'pointer'
						})
						.prop('title', jse.core.lang.translate('BUTTON_ORDERS', 'admin_buttons'))
						.on('click', function() {
							// Compose the URL and open it
							var url = [
								srcPath.replace('customers.php', 'admin.php'),
								'?' + $.param({
									do: 'OrdersOverview', 
									filter: {
										customer: '#' + data.rowId
									}
								})
							].join('');
							window.open(url, '_self');
						});
					
					_mapButtonDropdown($that, $dropdown, data);
				}
			});
		};
		
		var _mapButtonDropdown = function($that, $dropdown, data) {
			var actions = ['edit'];
			
			// Bind dropdown option - Delete
			if (data.rowId !== 1) {
				actions.push('delete');
			}
			
			actions = actions.concat([
				'BUTTON_STATUS',
				'BUTTON_ORDERS',
				'BUTTON_EMAIL',
				'BUTTON_IPLOG',
				'BUTTON_NEW_ORDER'
			]);
			
			// Admin rights button
			if ($that.find('[data-cust-group]').data('custGroup') === 0 &&
				$that.find('[data-cust-id]').data('custId') !== 1) {
				actions.push('BUTTON_ACCOUNTING');
			}
			
			// Bind MailBeez dropdown options.
			var mailBeezConversationsSelector =
				'.contentTable .infoBoxContent a.context_view_button.btn_right';
			if ($(mailBeezConversationsSelector).length) {
				actions.push('MAILBEEZ_OVERVIEW');
				actions.push('MAILBEEZ_NOTIFICATIONS');
				actions.push('MAILBEEZ_CONVERSATIONS');
			}
			
			// Bind Mediafinanz dropdown options.
			var $mediafinanzAction = $('.mediafinanz-creditworthiness'); 
			if ($mediafinanzAction.length) {
				actions.push('BUTTON_MEDIAFINANZ_CREDITWORTHINESS'); 
			}
			
			for (var index in actions) {
				_mapCustomerAction($dropdown, actions[index], data);
			}
		};
		
		var _mapCustomerAction = function($dropdown, action, data) {
			jse.libs.button_dropdown.mapAction($dropdown, action, _sectionMapping[action], function(event) {
				_executeActionCallback(action, data);
			});
		};
		
		var _sectionMapping = {
			edit: 'buttons',
			delete: 'buttons',
			BUTTON_STATUS: 'admin_buttons',
			BUTTON_ORDERS: 'admin_buttons',
			BUTTON_EMAIL: 'admin_buttons',
			BUTTON_IPLOG: 'admin_buttons',
			MAILBEEZ_OVERVIEW: 'admin_customers',
			MAILBEEZ_NOTIFICATIONS: 'admin_customers',
			MAILBEEZ_CONVERSATIONS: 'admin_customers',
			BUTTON_MEDIAFINANZ_CREDITWORTHINESS: 'admin_buttons',
			BUTTON_NEW_ORDER: 'admin_buttons',
			BUTTON_ACCOUNTING: 'admin_buttons'
		};
		
		/**
		 * Get the corresponding callback
		 *
		 * @param action
		 * @private
		 */
		var _executeActionCallback = function(action, data) {
			switch (action) {
				case 'edit':
					_editCallback(data);
					break;
				case 'delete':
					_deleteCallback(data);
					break;
				case 'BUTTON_STATUS':
					_customerGroupCallBack(data);
					break;
				case 'BUTTON_ORDERS':
					_ordersCallback(data);
					break;
				case 'BUTTON_EMAIL':
					_emailCallback(data);
					break;
				case 'BUTTON_IPLOG':
					_ipLogCallback(data);
					break;
				case 'MAILBEEZ_OVERVIEW':
					_mailBeezOverviewCallback(data);
					break;
				case 'MAILBEEZ_NOTIFICATIONS':
					_mailBeezNotificationsCallback(data);
					break;
				case 'MAILBEEZ_CONVERSATIONS':
					_mailBeezConversationsCallback(data);
					break;
				case 'BUTTON_MEDIAFINANZ_CREDITWORTHINESS':
					_mediafinanzCreditworthinessCallback(data);
					break;
				case 'BUTTON_NEW_ORDER':
					_newOrderCallback(data);
					break;
				case 'BUTTON_ACCOUNTING':
					_adminRightsCallback(data);
					break;
				default:
					throw new Error('Callback not found.');
			}
		};
		
		var _editCallback = function(data) {
			// Compose the URL and open it
			var url = [
				srcPath,
				'?cID=' + data.rowId,
				'&action=edit'
			].join('');
			window.open(url, '_self');
		};
		
		var _deleteCallback = function(data) {
			// Compose the URL and open it
			var url = [
				srcPath,
				queryString,
				'cID=' + data.rowId,
				'&action=confirm'
			].join('');
			window.open(url, '_self');
		};
		
		var _customerGroupCallBack = function(data) {
			// Compose the URL and open it
			var url = [
				srcPath,
				queryString,
				'cID=' + data.rowId,
				'&action=editstatus'
			].join('');
			window.open(url, '_self');
		};
		
		var _ordersCallback = function(data) {
			// Compose the URL and open it
			var url = [
				srcPath.replace('customers.php', 'admin.php'),
				'?' + $.param({
					do: 'OrdersOverview',
					filter: {
						customer: '#' + data.rowId
					}
				})
			].join('');
			window.open(url, '_self');
		};
		
		var _emailCallback = function(data) {
			// Compose the URL and open it
			var url = [
				srcPath.replace('customers.php', 'mail.php'),
				'?selected_box=tools',
				'&customer=' + data.custEmail,
			].join('');
			window.open(url, '_self');
		};
		
		var _ipLogCallback = function(data) {
			// Compose the URL and open it
			var url = [
				srcPath,
				queryString,
				'cID=' + data.rowId,
				'&action=iplog'
			].join('');
			window.open(url, '_self');
		};
		
		var _mailBeezOverviewCallback = function(data) {
			var $target = $('.contentTable .infoBoxContent a.context_view_button.btn_left');
			var url = $('.contentTable .infoBoxContent a.context_view_button.btn_left').attr(
				'onclick');
			url = url.replace(/cID=(.*)&/, 'cID=' + data.rowId + '&');
			$('.contentTable .infoBoxContent a.context_view_button.btn_left').attr('onclick', url);
			$target.get(0).click();
		};
		
		var _mailBeezNotificationsCallback = function(data) {
			var $target = $('.contentTable .infoBoxContent a.context_view_button.btn_middle');
			var url = $('.contentTable .infoBoxContent a.context_view_button.btn_middle').attr(
				'onclick');
			url = url.replace(/cID=(.*)&/, 'cID=' + data.rowId + '&');
			$('.contentTable .infoBoxContent a.context_view_button.btn_middle').attr('onclick',
				url);
			$target.get(0).click();
		};
		
		var _mailBeezConversationsCallback = function(data) {
			var $target = $('.contentTable .infoBoxContent a.context_view_button.btn_right');
			var url = $('.contentTable .infoBoxContent a.context_view_button.btn_right').attr(
				'onclick');
			url = url.replace(/cID=(.*)&/, 'cID=' + data.rowId + '&');
			$('.contentTable .infoBoxContent a.context_view_button.btn_right').attr('onclick',
				url);
			$target.get(0).click();
		};
		
		var _mediafinanzCreditworthinessCallback = function(data) {
			var $target = $('.mediafinanz-creditworthiness');
			var onclickAttribute = $target.attr('onclick');
			// Replace the customer number in the onclick attribute. 
			onclickAttribute = onclickAttribute.replace(/cID=(.*', 'popup')/, 'cID=' + data.rowId + '\', \'popup\''); 
			$target.attr('onclick', onclickAttribute);
			$target.trigger('click'); // Trigger the click event in the <a> element. 
		};
		
		var _newOrderCallback = function(data) {
			// Compose the URL and open it
			var url = [
				srcPath,
				'?cID=' + data.rowId,
				'&action=new_order'
			].join('');
			window.open(url, '_self');
		};
		
		var _adminRightsCallback = function(data) {
			// Compose the URL and open it
			var url = [
				srcPath.replace('customers.php', 'accounting.php'),
				'?cID=' + data.rowId
			].join('');
			window.open(url, '_self');
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			// Wait until the buttons are converted to dropdown for every row.
			var interval = setInterval(function() {
				if ($('.js-button-dropdown').length) {
					clearInterval(interval);
					_mapRowActions();
				}
			}, 500);
			
			done();
		};
		
		return module;
	});
