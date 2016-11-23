/* --------------------------------------------------------------
 orders_table_controller.js 2016-10-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Table Controller
 *
 * This controller contains the mapping logic of the orders table.
 *
 * @module Compatibility/orders_table_controller
 */
gx.compatibility.module(
	'orders_table_controller',
	
	[
		gx.source + '/libs/action_mapper',
		gx.source + '/libs/button_dropdown'
	],
	
	/**  @lends module:Compatibility/orders_table_controller */
	
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
			 * Array of mapped buttons
			 *
			 * @var Array
			 */
			mappedButtons = [],
			
			/**
			 * The mapper library
			 *
			 * @var {object}
			 */
			mapper = jse.libs.action_mapper,
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		/**
		 * Disable/Enable the buttons on the bottom button-dropdown
		 * dependent on the checkboxes selection
		 * @private
		 */
		var _toggleMultiActionButton = function() {
			var $checked = $('tr[data-row-id] input[type="checkbox"]:checked');
			$('.js-bottom-dropdown button').prop('disabled', !$checked.length);
		};
		
		/**
		 * Map actions for every row in the table.
		 *
		 * This method will map the actions for each
		 * row of the table.
		 *
		 * @private
		 */
		var _mapRowAction = function($that) {
			/**
			 * Reference to the row action dropdown
			 * @var {object | jQuery}
			 */
			var $dropdown = $that.find('.js-button-dropdown');
			
			if ($dropdown.length) {
				_mapRowButtonDropdown($dropdown);
			}
		};
		
		var _mapRowButtonDropdown = function($dropdown) {
			var actions = [
				'TEXT_SHOW',
				'TEXT_GM_STATUS',
				'delete',
				'BUTTON_GM_CANCEL',
				'TITLE_INVOICE',
				'TITLE_INVOICE_MAIL',
				'TITLE_PACKINGSLIP',
				'TITLE_ORDER',
				'TITLE_RECREATE_ORDER',
				'TITLE_SEND_ORDER',
				'TEXT_CREATE_WITHDRAWAL',
				'TXT_PARCEL_TRACKING_SENDBUTTON_TITLE',
				'BUTTON_DHL_LABEL',
				'MAILBEEZ_OVERVIEW',
				'MAILBEEZ_NOTIFICATIONS',
				'MAILBEEZ_CONVERSATIONS',
				'BUTTON_HERMES'
			];
			
			for (var index in actions) {
				_bindEventHandler($dropdown, actions[index], '.single-order-dropdown');
			}
		};
		
		/**
		 * Defines the language section for each text tile
		 *
		 * @type {object}
		 * @private
		 */
		var _sectionMapping = {
			'TEXT_SHOW': 'orders',
			'TEXT_GM_STATUS': 'orders',
			'delete': 'buttons',
			'BUTTON_GM_CANCEL': 'orders',
			'TITLE_INVOICE': 'orders',
			'TITLE_INVOICE_MAIL': 'orders',
			'TITLE_PACKINGSLIP': 'orders',
			'TITLE_ORDER': 'orders',
			'TITLE_RECREATE_ORDER': 'orders',
			'TITLE_SEND_ORDER': 'orders',
			'TEXT_CREATE_WITHDRAWAL': 'orders',
			'TXT_PARCEL_TRACKING_SENDBUTTON_TITLE': 'parcel_services',
			'BUTTON_DHL_LABEL': 'orders',
			'MAILBEEZ_OVERVIEW': 'orders',
			'MAILBEEZ_NOTIFICATIONS': 'orders',
			'MAILBEEZ_CONVERSATIONS': 'orders',
			'BUTTON_MULTI_CANCEL': 'orders',
			'BUTTON_MULTI_CHANGE_ORDER_STATUS': 'orders',
			'BUTTON_MULTI_DELETE': 'orders',
			'BUTTON_HERMES': 'orders',
			'get_labels': 'iloxx'
		};
		
		/**
		 * Defines target selectors
		 *
		 * @type {object}
		 * @private
		 */
		var _selectorMapping = {
			'TEXT_SHOW': '.contentTable .infoBoxContent a.btn-details',
			'TEXT_GM_STATUS': '.contentTable .infoBoxContent a.btn-update_order_status',
			'delete': '.contentTable .infoBoxContent a.btn-delete',
			'BUTTON_GM_CANCEL': '.contentTable .infoBoxContent .GM_CANCEL',
			'TITLE_INVOICE': '.contentTable .infoBoxContent a.btn-invoice',
			'TITLE_INVOICE_MAIL': '.contentTable .infoBoxContent .GM_INVOICE_MAIL',
			'TITLE_PACKINGSLIP': '.contentTable .infoBoxContent a.btn-packing_slip',
			'TITLE_ORDER': '.contentTable .infoBoxContent a.btn-order_confirmation',
			'TITLE_RECREATE_ORDER': '.contentTable .infoBoxContent a.btn-recreate_order_confirmation',
			'TITLE_SEND_ORDER': '.contentTable .infoBoxContent .GM_SEND_ORDER',
			'TEXT_CREATE_WITHDRAWAL': '.contentTable .infoBoxContent a.btn-create_withdrawal',
			'TXT_PARCEL_TRACKING_SENDBUTTON_TITLE': '.contentTable .infoBoxContent a.btn-add_tracking_code',
			'BUTTON_DHL_LABEL': '.contentTable .infoBoxContent a.btn-dhl_label',
			'MAILBEEZ_OVERVIEW': '.contentTable .infoBoxContent a.context_view_button.btn_left',
			'MAILBEEZ_NOTIFICATIONS': '.contentTable .infoBoxContent a.context_view_button.btn_middle',
			'MAILBEEZ_CONVERSATIONS': '.contentTable .infoBoxContent a.context_view_button.btn_right',
			'BUTTON_MULTI_CANCEL': '.contentTable .infoBoxContent a.btn-multi_cancel',
			'BUTTON_MULTI_CHANGE_ORDER_STATUS': '.contentTable .infoBoxContent a.btn-update_order_status',
			'BUTTON_MULTI_DELETE': '.contentTable .infoBoxContent a.btn-multi_delete',
			'BUTTON_HERMES': '.contentTable .infoBoxContent a.btn-hermes',
			'get_labels': '#iloxx_orders'
		};
		
		var _getActionCallback = function(action) {
			switch (action) {
				case 'TEXT_SHOW':
					return _showOrderCallback;
				case 'TEXT_GM_STATUS':
					return _changeOrderStatusCallback;
				case 'delete':
					return _deleteCallback;
				case 'BUTTON_GM_CANCEL':
					return _cancelCallback;
				case 'TITLE_INVOICE':
					return _invoiceCallback;
				case 'TITLE_INVOICE_MAIL':
					return _emailInvoiceCallback;
				case 'TITLE_PACKINGSLIP':
					return _packingSlipCallback;
				case 'TITLE_ORDER':
					return _orderConfirmationCallback;
				case 'TITLE_RECREATE_ORDER':
					return _recreateOrderConfirmationCallback;
				case 'TITLE_SEND_ORDER':
					return _sendOrderConfirmationCallback;
				case 'TEXT_CREATE_WITHDRAWAL':
					return _withdrawalCallback;
				case 'TXT_PARCEL_TRACKING_SENDBUTTON_TITLE':
					return _addTrackingCodeCallback;
				case 'BUTTON_DHL_LABEL':
					return _dhlLabelCallback;
				case 'MAILBEEZ_OVERVIEW':
					return _mailBeezOverviewCallback;
				case 'MAILBEEZ_NOTIFICATIONS':
					return _mailBeezNotificationsCallback;
				case 'MAILBEEZ_CONVERSATIONS':
					return _mailBeezConversationsCallback;
				case 'BUTTON_MULTI_CANCEL':
					return _multiCancelCallback;
				case 'BUTTON_MULTI_CHANGE_ORDER_STATUS':
					return _multiChangeOrderStatusCallback;
				case 'BUTTON_MULTI_DELETE':
					return _multiDeleteCallback;
				case 'BUTTON_HERMES':
					return _hermesCallback;
				case 'get_labels':
					return _iloxxCallback;
			}
		};
		
		var _showOrderCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var url = $(_selectorMapping.TEXT_SHOW).attr('href');
			window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_self');
		};
		
		var _changeOrderStatusCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			$('#gm_order_id').val(orderId);
			$('.gx-orders-table .single-checkbox').removeClass('checked');
			$('.gx-orders-table input:checkbox').prop('checked', false);
			$(event.target).parents('tr').eq(0).find('.single-checkbox').addClass('checked');
			$(event.target).parents('tr').eq(0).find('input:checkbox').prop('checked', true);
			$(_selectorMapping.TEXT_GM_STATUS).click();
		};
		
		var _deleteCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var $delete = $(_selectorMapping.delete);
			$delete.data('order_id', orderId);
			$delete.get(0).click();
		};
		
		var _cancelCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			$('#gm_order_id').val(orderId);
			$('.gx-orders-table .single-checkbox').removeClass('checked');
			$('.gx-orders-table input:checkbox').prop('checked', false);
			$(event.target).parents('tr').eq(0).find('.single-checkbox').addClass('checked');
			$(event.target).parents('tr').eq(0).find('input:checkbox').prop('checked', true);
			$(_selectorMapping.BUTTON_MULTI_CANCEL).click();
		};
		
		var _invoiceCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var url = $(_selectorMapping.TITLE_INVOICE).attr('href');
			window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_blank');
		};
		
		var _emailInvoiceCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			$('#gm_order_id').val(orderId);
			$('.GM_INVOICE_MAIL').click();
		};
		
		var _packingSlipCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var url = $(_selectorMapping.TITLE_PACKINGSLIP).attr('href');
			window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_blank');
		};
		
		var _orderConfirmationCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var url = $(_selectorMapping.TITLE_ORDER).attr('href');
			window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_blank');
		};
		
		var _recreateOrderConfirmationCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var url = $(_selectorMapping.TITLE_RECREATE_ORDER).attr('href');
			window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_blank');
		};
		
		var _sendOrderConfirmationCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			$('#gm_order_id').val(orderId);
			$('.GM_SEND_ORDER').click();
		};
		
		var _withdrawalCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var url = $(_selectorMapping.TEXT_CREATE_WITHDRAWAL).attr('href');
			window.open(url.replace(/order=[^&]*/, 'order_id=' + orderId), '_blank');
		};
		
		var _addTrackingCodeCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var $target = $(_selectorMapping.TXT_PARCEL_TRACKING_SENDBUTTON_TITLE);
			$target.data('order_id', orderId);
			$target.get(0).click();
		};
		
		var _dhlLabelCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var url = $(_selectorMapping.BUTTON_DHL_LABEL).attr('href');
			window.open(url.replace(/oID=(.*)/, 'oID=' + orderId), '_blank');
		};
		
		var _mailBeezOverviewCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var $target = $(_selectorMapping.MAILBEEZ_OVERVIEW);
			var url = $target.attr('onclick');
			url = url.replace(/oID=(.*)&/, 'oID=' + orderId + '&');
			$target.attr('onclick', url);
			$target.get(0).click();
		};
		
		var _mailBeezNotificationsCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var $target = $(_selectorMapping.MAILBEEZ_NOTIFICATIONS);
			var url = $target.attr('onclick');
			url = url.replace(/oID=(.*)&/, 'oID=' + orderId + '&');
			$target.attr('onclick', url);
			$target.get(0).click();
		};
		
		var _mailBeezConversationsCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var $target = $(_selectorMapping.MAILBEEZ_CONVERSATIONS);
			var url = $target.attr('onclick');
			url = url.replace(/oID=(.*)&/, 'oID=' + orderId + '&');
			$target.attr('onclick', url);
			$target.get(0).click();
		};
		
		var _hermesCallback = function(event) {
			var orderId = $(event.target).parents('tr').data('row-id');
			var $target = $(_selectorMapping.BUTTON_HERMES);
			var url = $target.attr('href');
			url = url.replace(/orders_id=(.*)/, 'orders_id=' + orderId);
			$target.attr('href', url);
			$target.get(0).click();
		};
		
		var _iloxxCallback = function(event) {
			var $target = $(_selectorMapping.get_labels);
			$target.click();
		};
		
		var _multiChangeOrderStatusCallback = function(event) {
			$(_selectorMapping.BUTTON_MULTI_CHANGE_ORDER_STATUS).get(0).click();
		};
		
		var _multiDeleteCallback = function(event) {
			$(_selectorMapping.BUTTON_MULTI_DELETE).get(0).click();
		};
		
		var _multiCancelCallback = function(event) {
			$(_selectorMapping.BUTTON_MULTI_CANCEL).get(0).click();
		};
		
		/**
		 * Map table actions to bottom dropdown button.
		 *
		 * @private
		 */
		var _mapTableActions = function() {
			var $dropdown = $('#orders-table-dropdown');
			
			_bindEventHandler($dropdown, 'BUTTON_MULTI_CHANGE_ORDER_STATUS');
			
			if ($(_selectorMapping.get_labels).length) {
				_bindEventHandler($dropdown, 'get_labels');
			}
			
			_bindEventHandler($dropdown, 'BUTTON_MULTI_DELETE');
			_bindEventHandler($dropdown, 'BUTTON_MULTI_CANCEL');
		};
		
		/**
		 * Map actions for every row in the table generically.
		 *
		 * This method will use the action_mapper library to map the actions for each
		 * row of the table. It maps only those buttons, that haven't already explicitly
		 * mapped by the _mapRowActions function.
		 *
		 * @private
		 */
		var _mapUnmappedRowActions = function($this) {
			var unmappedRowActions = [];
			$('.action_buttons .extended_single_actions a,' +
				'.action_buttons .extended_single_actions button,' +
				'.action_buttons .extended_single_actions input[type="button"],' +
				'.action_buttons .extended_single_actions input[type="submit"]').each(function() {
				if (!_alreadyMapped($(this))) {
					unmappedRowActions.push($(this));
				}
			});
			
			var orderId = $this.data('row-id'),
				$dropdown = $this.find('.js-button-dropdown');
			
			$.each(unmappedRowActions, function() {
				var $button = $(this);
				var callback = function() {
					if ($button.prop('href') !== undefined) {
						$button.prop('href', $button.prop('href').replace(/oID=(.*)\d(?=&)?/, 'oID=' + orderId));
					}
					$button.get(0).click();
				};
				
				jse.libs.button_dropdown.mapAction($dropdown, $button.text(), '', callback);
				mappedButtons.push($button);
			});
		};
		
		var _mapUnmappedMultiActions = function() {
			var unmappedMultiActions = [];
			$('.action_buttons .extended_multi_actions a,' +
				'.action_buttons .extended_multi_actions button,' +
				'.action_buttons .extended_multi_actions input[type="button"],' +
				'.action_buttons .extended_multi_actions input[type="submit"]').each(function() {
				if (!_alreadyMapped($(this))) {
					unmappedMultiActions.push($(this));
				}
			});
			
			var $dropdown = $('#orders-table-dropdown');
			$.each(unmappedMultiActions, function() {
				var $button = $(this);
				var callback = function() {
					$button.get(0).click();
				};
				
				jse.libs.button_dropdown.mapAction($dropdown, $button.text(), '', callback);
				mappedButtons.push($button);
			});
		};
		
		/**
		 * Checks if the button was already mapped
		 *
		 * @private
		 */
		var _alreadyMapped = function($button) {
			for (var index in mappedButtons) {
				if ($button.is(mappedButtons[index])) {
					return true;
				}
			}
			return false;
		};
		
		/**
		 * Add Button to Mapped Array
		 *
		 * @param buttonSelector
		 * @returns {boolean}
		 *
		 * @private
		 */
		var _addButtonToMappedArray = function(buttonSelector) {
			if (mappedButtons[buttonSelector] !== undefined) {
				return true;
			}
			mappedButtons[buttonSelector] = $(buttonSelector);
		};
		
		/**
		 * Bind Event handler
		 *
		 * @param $dropdown
		 * @param action
		 * @param customRecentButtonSelector
		 *
		 * @private
		 */
		var _bindEventHandler = function($dropdown, action, customRecentButtonSelector) {
			var targetSelector = _selectorMapping[action],
				section = _sectionMapping[action],
				callback = _getActionCallback(action),
				customElement = $(customRecentButtonSelector).length ? $(customRecentButtonSelector) :
				                $dropdown;
			if ($(targetSelector).length) {
				_addButtonToMappedArray(targetSelector);
				jse.libs.button_dropdown.mapAction($dropdown, action, section, callback, customElement);
			}
		};
		
		/**
		 * Fix for row selection controls.
		 *
		 * @private
		 */
		var _fixRowSelectionForControlElements = function() {
			$('input.checkbox[name="gm_multi_status[]"]')
				.add('.single-checkbox')
				.add('a.action-icon')
				.add('.js-button-dropdown')
				.add('tr.dataTableRow a')
				.on('click', function(event) {
					event.stopPropagation();
					_toggleMultiActionButton();
				});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			// Wait until the buttons are converted to dropdown for every row.
			var interval = setInterval(function() {
				if ($('.js-button-dropdown').length) {
					clearInterval(interval);
					
					_mapTableActions();
					_mapUnmappedMultiActions();
					
					var tableActions = mappedButtons;
					
					// Remove Mailbeez conversations badge.
					_addButtonToMappedArray('.contentTable .infoBoxContent a.context_view_button.btn_right');
					
					$('.gx-orders-table tr').not('.dataTableHeadingRow').each(function() {
						mappedButtons = [];
						
						for (var index in tableActions) {
							mappedButtons[index] = tableActions[index];
						}
						
						_mapRowAction($(this));
						_mapUnmappedRowActions($(this));
					});
					
					_fixRowSelectionForControlElements();
					
					// Initialize checkboxes
					_toggleMultiActionButton();
				}
			}, 300);
			
			// Check for selected checkboxes also
			// before all rows and their dropdown widgets have been initialized.
			_toggleMultiActionButton();
			
			done();
		};
		
		return module;
	});
