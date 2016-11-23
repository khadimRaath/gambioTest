'use strict';

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
gx.compatibility.module('orders_table_controller', [gx.source + '/libs/action_mapper', gx.source + '/libs/button_dropdown'],

/**  @lends module:Compatibility/orders_table_controller */

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
	var _toggleMultiActionButton = function _toggleMultiActionButton() {
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
	var _mapRowAction = function _mapRowAction($that) {
		/**
   * Reference to the row action dropdown
   * @var {object | jQuery}
   */
		var $dropdown = $that.find('.js-button-dropdown');

		if ($dropdown.length) {
			_mapRowButtonDropdown($dropdown);
		}
	};

	var _mapRowButtonDropdown = function _mapRowButtonDropdown($dropdown) {
		var actions = ['TEXT_SHOW', 'TEXT_GM_STATUS', 'delete', 'BUTTON_GM_CANCEL', 'TITLE_INVOICE', 'TITLE_INVOICE_MAIL', 'TITLE_PACKINGSLIP', 'TITLE_ORDER', 'TITLE_RECREATE_ORDER', 'TITLE_SEND_ORDER', 'TEXT_CREATE_WITHDRAWAL', 'TXT_PARCEL_TRACKING_SENDBUTTON_TITLE', 'BUTTON_DHL_LABEL', 'MAILBEEZ_OVERVIEW', 'MAILBEEZ_NOTIFICATIONS', 'MAILBEEZ_CONVERSATIONS', 'BUTTON_HERMES'];

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

	var _getActionCallback = function _getActionCallback(action) {
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

	var _showOrderCallback = function _showOrderCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var url = $(_selectorMapping.TEXT_SHOW).attr('href');
		window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_self');
	};

	var _changeOrderStatusCallback = function _changeOrderStatusCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		$('#gm_order_id').val(orderId);
		$('.gx-orders-table .single-checkbox').removeClass('checked');
		$('.gx-orders-table input:checkbox').prop('checked', false);
		$(event.target).parents('tr').eq(0).find('.single-checkbox').addClass('checked');
		$(event.target).parents('tr').eq(0).find('input:checkbox').prop('checked', true);
		$(_selectorMapping.TEXT_GM_STATUS).click();
	};

	var _deleteCallback = function _deleteCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var $delete = $(_selectorMapping.delete);
		$delete.data('order_id', orderId);
		$delete.get(0).click();
	};

	var _cancelCallback = function _cancelCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		$('#gm_order_id').val(orderId);
		$('.gx-orders-table .single-checkbox').removeClass('checked');
		$('.gx-orders-table input:checkbox').prop('checked', false);
		$(event.target).parents('tr').eq(0).find('.single-checkbox').addClass('checked');
		$(event.target).parents('tr').eq(0).find('input:checkbox').prop('checked', true);
		$(_selectorMapping.BUTTON_MULTI_CANCEL).click();
	};

	var _invoiceCallback = function _invoiceCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var url = $(_selectorMapping.TITLE_INVOICE).attr('href');
		window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_blank');
	};

	var _emailInvoiceCallback = function _emailInvoiceCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		$('#gm_order_id').val(orderId);
		$('.GM_INVOICE_MAIL').click();
	};

	var _packingSlipCallback = function _packingSlipCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var url = $(_selectorMapping.TITLE_PACKINGSLIP).attr('href');
		window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_blank');
	};

	var _orderConfirmationCallback = function _orderConfirmationCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var url = $(_selectorMapping.TITLE_ORDER).attr('href');
		window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_blank');
	};

	var _recreateOrderConfirmationCallback = function _recreateOrderConfirmationCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var url = $(_selectorMapping.TITLE_RECREATE_ORDER).attr('href');
		window.open(url.replace(/oID=(.*)&/, 'oID=' + orderId + '&'), '_blank');
	};

	var _sendOrderConfirmationCallback = function _sendOrderConfirmationCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		$('#gm_order_id').val(orderId);
		$('.GM_SEND_ORDER').click();
	};

	var _withdrawalCallback = function _withdrawalCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var url = $(_selectorMapping.TEXT_CREATE_WITHDRAWAL).attr('href');
		window.open(url.replace(/order=[^&]*/, 'order_id=' + orderId), '_blank');
	};

	var _addTrackingCodeCallback = function _addTrackingCodeCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var $target = $(_selectorMapping.TXT_PARCEL_TRACKING_SENDBUTTON_TITLE);
		$target.data('order_id', orderId);
		$target.get(0).click();
	};

	var _dhlLabelCallback = function _dhlLabelCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var url = $(_selectorMapping.BUTTON_DHL_LABEL).attr('href');
		window.open(url.replace(/oID=(.*)/, 'oID=' + orderId), '_blank');
	};

	var _mailBeezOverviewCallback = function _mailBeezOverviewCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var $target = $(_selectorMapping.MAILBEEZ_OVERVIEW);
		var url = $target.attr('onclick');
		url = url.replace(/oID=(.*)&/, 'oID=' + orderId + '&');
		$target.attr('onclick', url);
		$target.get(0).click();
	};

	var _mailBeezNotificationsCallback = function _mailBeezNotificationsCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var $target = $(_selectorMapping.MAILBEEZ_NOTIFICATIONS);
		var url = $target.attr('onclick');
		url = url.replace(/oID=(.*)&/, 'oID=' + orderId + '&');
		$target.attr('onclick', url);
		$target.get(0).click();
	};

	var _mailBeezConversationsCallback = function _mailBeezConversationsCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var $target = $(_selectorMapping.MAILBEEZ_CONVERSATIONS);
		var url = $target.attr('onclick');
		url = url.replace(/oID=(.*)&/, 'oID=' + orderId + '&');
		$target.attr('onclick', url);
		$target.get(0).click();
	};

	var _hermesCallback = function _hermesCallback(event) {
		var orderId = $(event.target).parents('tr').data('row-id');
		var $target = $(_selectorMapping.BUTTON_HERMES);
		var url = $target.attr('href');
		url = url.replace(/orders_id=(.*)/, 'orders_id=' + orderId);
		$target.attr('href', url);
		$target.get(0).click();
	};

	var _iloxxCallback = function _iloxxCallback(event) {
		var $target = $(_selectorMapping.get_labels);
		$target.click();
	};

	var _multiChangeOrderStatusCallback = function _multiChangeOrderStatusCallback(event) {
		$(_selectorMapping.BUTTON_MULTI_CHANGE_ORDER_STATUS).get(0).click();
	};

	var _multiDeleteCallback = function _multiDeleteCallback(event) {
		$(_selectorMapping.BUTTON_MULTI_DELETE).get(0).click();
	};

	var _multiCancelCallback = function _multiCancelCallback(event) {
		$(_selectorMapping.BUTTON_MULTI_CANCEL).get(0).click();
	};

	/**
  * Map table actions to bottom dropdown button.
  *
  * @private
  */
	var _mapTableActions = function _mapTableActions() {
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
	var _mapUnmappedRowActions = function _mapUnmappedRowActions($this) {
		var unmappedRowActions = [];
		$('.action_buttons .extended_single_actions a,' + '.action_buttons .extended_single_actions button,' + '.action_buttons .extended_single_actions input[type="button"],' + '.action_buttons .extended_single_actions input[type="submit"]').each(function () {
			if (!_alreadyMapped($(this))) {
				unmappedRowActions.push($(this));
			}
		});

		var orderId = $this.data('row-id'),
		    $dropdown = $this.find('.js-button-dropdown');

		$.each(unmappedRowActions, function () {
			var $button = $(this);
			var callback = function callback() {
				if ($button.prop('href') !== undefined) {
					$button.prop('href', $button.prop('href').replace(/oID=(.*)\d(?=&)?/, 'oID=' + orderId));
				}
				$button.get(0).click();
			};

			jse.libs.button_dropdown.mapAction($dropdown, $button.text(), '', callback);
			mappedButtons.push($button);
		});
	};

	var _mapUnmappedMultiActions = function _mapUnmappedMultiActions() {
		var unmappedMultiActions = [];
		$('.action_buttons .extended_multi_actions a,' + '.action_buttons .extended_multi_actions button,' + '.action_buttons .extended_multi_actions input[type="button"],' + '.action_buttons .extended_multi_actions input[type="submit"]').each(function () {
			if (!_alreadyMapped($(this))) {
				unmappedMultiActions.push($(this));
			}
		});

		var $dropdown = $('#orders-table-dropdown');
		$.each(unmappedMultiActions, function () {
			var $button = $(this);
			var callback = function callback() {
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
	var _alreadyMapped = function _alreadyMapped($button) {
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
	var _addButtonToMappedArray = function _addButtonToMappedArray(buttonSelector) {
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
	var _bindEventHandler = function _bindEventHandler($dropdown, action, customRecentButtonSelector) {
		var targetSelector = _selectorMapping[action],
		    section = _sectionMapping[action],
		    callback = _getActionCallback(action),
		    customElement = $(customRecentButtonSelector).length ? $(customRecentButtonSelector) : $dropdown;
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
	var _fixRowSelectionForControlElements = function _fixRowSelectionForControlElements() {
		$('input.checkbox[name="gm_multi_status[]"]').add('.single-checkbox').add('a.action-icon').add('.js-button-dropdown').add('tr.dataTableRow a').on('click', function (event) {
			event.stopPropagation();
			_toggleMultiActionButton();
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Wait until the buttons are converted to dropdown for every row.
		var interval = setInterval(function () {
			if ($('.js-button-dropdown').length) {
				clearInterval(interval);

				_mapTableActions();
				_mapUnmappedMultiActions();

				var tableActions = mappedButtons;

				// Remove Mailbeez conversations badge.
				_addButtonToMappedArray('.contentTable .infoBoxContent a.context_view_button.btn_right');

				$('.gx-orders-table tr').not('.dataTableHeadingRow').each(function () {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcnNfdGFibGVfY29udHJvbGxlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwibWFwcGVkQnV0dG9ucyIsIm1hcHBlciIsImpzZSIsImxpYnMiLCJhY3Rpb25fbWFwcGVyIiwiX3RvZ2dsZU11bHRpQWN0aW9uQnV0dG9uIiwiJGNoZWNrZWQiLCJwcm9wIiwibGVuZ3RoIiwiX21hcFJvd0FjdGlvbiIsIiR0aGF0IiwiJGRyb3Bkb3duIiwiZmluZCIsIl9tYXBSb3dCdXR0b25Ecm9wZG93biIsImFjdGlvbnMiLCJpbmRleCIsIl9iaW5kRXZlbnRIYW5kbGVyIiwiX3NlY3Rpb25NYXBwaW5nIiwiX3NlbGVjdG9yTWFwcGluZyIsIl9nZXRBY3Rpb25DYWxsYmFjayIsImFjdGlvbiIsIl9zaG93T3JkZXJDYWxsYmFjayIsIl9jaGFuZ2VPcmRlclN0YXR1c0NhbGxiYWNrIiwiX2RlbGV0ZUNhbGxiYWNrIiwiX2NhbmNlbENhbGxiYWNrIiwiX2ludm9pY2VDYWxsYmFjayIsIl9lbWFpbEludm9pY2VDYWxsYmFjayIsIl9wYWNraW5nU2xpcENhbGxiYWNrIiwiX29yZGVyQ29uZmlybWF0aW9uQ2FsbGJhY2siLCJfcmVjcmVhdGVPcmRlckNvbmZpcm1hdGlvbkNhbGxiYWNrIiwiX3NlbmRPcmRlckNvbmZpcm1hdGlvbkNhbGxiYWNrIiwiX3dpdGhkcmF3YWxDYWxsYmFjayIsIl9hZGRUcmFja2luZ0NvZGVDYWxsYmFjayIsIl9kaGxMYWJlbENhbGxiYWNrIiwiX21haWxCZWV6T3ZlcnZpZXdDYWxsYmFjayIsIl9tYWlsQmVlek5vdGlmaWNhdGlvbnNDYWxsYmFjayIsIl9tYWlsQmVlekNvbnZlcnNhdGlvbnNDYWxsYmFjayIsIl9tdWx0aUNhbmNlbENhbGxiYWNrIiwiX211bHRpQ2hhbmdlT3JkZXJTdGF0dXNDYWxsYmFjayIsIl9tdWx0aURlbGV0ZUNhbGxiYWNrIiwiX2hlcm1lc0NhbGxiYWNrIiwiX2lsb3h4Q2FsbGJhY2siLCJldmVudCIsIm9yZGVySWQiLCJ0YXJnZXQiLCJwYXJlbnRzIiwidXJsIiwiVEVYVF9TSE9XIiwiYXR0ciIsIndpbmRvdyIsIm9wZW4iLCJyZXBsYWNlIiwidmFsIiwicmVtb3ZlQ2xhc3MiLCJlcSIsImFkZENsYXNzIiwiVEVYVF9HTV9TVEFUVVMiLCJjbGljayIsIiRkZWxldGUiLCJkZWxldGUiLCJnZXQiLCJCVVRUT05fTVVMVElfQ0FOQ0VMIiwiVElUTEVfSU5WT0lDRSIsIlRJVExFX1BBQ0tJTkdTTElQIiwiVElUTEVfT1JERVIiLCJUSVRMRV9SRUNSRUFURV9PUkRFUiIsIlRFWFRfQ1JFQVRFX1dJVEhEUkFXQUwiLCIkdGFyZ2V0IiwiVFhUX1BBUkNFTF9UUkFDS0lOR19TRU5EQlVUVE9OX1RJVExFIiwiQlVUVE9OX0RITF9MQUJFTCIsIk1BSUxCRUVaX09WRVJWSUVXIiwiTUFJTEJFRVpfTk9USUZJQ0FUSU9OUyIsIk1BSUxCRUVaX0NPTlZFUlNBVElPTlMiLCJCVVRUT05fSEVSTUVTIiwiZ2V0X2xhYmVscyIsIkJVVFRPTl9NVUxUSV9DSEFOR0VfT1JERVJfU1RBVFVTIiwiQlVUVE9OX01VTFRJX0RFTEVURSIsIl9tYXBUYWJsZUFjdGlvbnMiLCJfbWFwVW5tYXBwZWRSb3dBY3Rpb25zIiwidW5tYXBwZWRSb3dBY3Rpb25zIiwiZWFjaCIsIl9hbHJlYWR5TWFwcGVkIiwicHVzaCIsIiRidXR0b24iLCJjYWxsYmFjayIsInVuZGVmaW5lZCIsImJ1dHRvbl9kcm9wZG93biIsIm1hcEFjdGlvbiIsInRleHQiLCJfbWFwVW5tYXBwZWRNdWx0aUFjdGlvbnMiLCJ1bm1hcHBlZE11bHRpQWN0aW9ucyIsImlzIiwiX2FkZEJ1dHRvblRvTWFwcGVkQXJyYXkiLCJidXR0b25TZWxlY3RvciIsImN1c3RvbVJlY2VudEJ1dHRvblNlbGVjdG9yIiwidGFyZ2V0U2VsZWN0b3IiLCJzZWN0aW9uIiwiY3VzdG9tRWxlbWVudCIsIl9maXhSb3dTZWxlY3Rpb25Gb3JDb250cm9sRWxlbWVudHMiLCJhZGQiLCJvbiIsInN0b3BQcm9wYWdhdGlvbiIsImluaXQiLCJkb25lIiwiaW50ZXJ2YWwiLCJzZXRJbnRlcnZhbCIsImNsZWFySW50ZXJ2YWwiLCJ0YWJsZUFjdGlvbnMiLCJub3QiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLHlCQURELEVBR0MsQ0FDQ0YsR0FBR0csTUFBSCxHQUFZLHFCQURiLEVBRUNILEdBQUdHLE1BQUgsR0FBWSx1QkFGYixDQUhEOztBQVFDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7OztBQUtBTSxpQkFBZ0IsRUEzQmpCOzs7QUE2QkM7Ozs7O0FBS0FDLFVBQVNDLElBQUlDLElBQUosQ0FBU0MsYUFsQ25COzs7QUFvQ0M7Ozs7O0FBS0FaLFVBQVMsRUF6Q1Y7O0FBMkNBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7QUFLQSxLQUFJYSwyQkFBMkIsU0FBM0JBLHdCQUEyQixHQUFXO0FBQ3pDLE1BQUlDLFdBQVdWLEVBQUUsZ0RBQUYsQ0FBZjtBQUNBQSxJQUFFLDRCQUFGLEVBQWdDVyxJQUFoQyxDQUFxQyxVQUFyQyxFQUFpRCxDQUFDRCxTQUFTRSxNQUEzRDtBQUNBLEVBSEQ7O0FBS0E7Ozs7Ozs7O0FBUUEsS0FBSUMsZ0JBQWdCLFNBQWhCQSxhQUFnQixDQUFTQyxLQUFULEVBQWdCO0FBQ25DOzs7O0FBSUEsTUFBSUMsWUFBWUQsTUFBTUUsSUFBTixDQUFX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LEdBQXpCLENBQTZCLHNCQUE3QixFQUFxRDVCLElBQXJELENBQTBELFlBQVc7QUFDcEVoRixxQkFBZ0IsRUFBaEI7O0FBRUEsVUFBSyxJQUFJZSxLQUFULElBQWtCNEYsWUFBbEIsRUFBZ0M7QUFDL0IzRyxvQkFBY2UsS0FBZCxJQUF1QjRGLGFBQWE1RixLQUFiLENBQXZCO0FBQ0E7O0FBRUROLG1CQUFjYixFQUFFLElBQUYsQ0FBZDtBQUNBa0YsNEJBQXVCbEYsRUFBRSxJQUFGLENBQXZCO0FBQ0EsS0FURDs7QUFXQXNHOztBQUVBO0FBQ0E3RjtBQUNBO0FBQ0QsR0E1QmMsRUE0QlosR0E1QlksQ0FBZjs7QUE4QkE7QUFDQTtBQUNBQTs7QUFFQWtHO0FBQ0EsRUFyQ0Q7O0FBdUNBLFFBQU8vRyxNQUFQO0FBQ0EsQ0E5aUJGIiwiZmlsZSI6Im9yZGVycy9vcmRlcnNfdGFibGVfY29udHJvbGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gb3JkZXJzX3RhYmxlX2NvbnRyb2xsZXIuanMgMjAxNi0xMC0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgT3JkZXJzIFRhYmxlIENvbnRyb2xsZXJcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgY29udGFpbnMgdGhlIG1hcHBpbmcgbG9naWMgb2YgdGhlIG9yZGVycyB0YWJsZS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvb3JkZXJzX3RhYmxlX2NvbnRyb2xsZXJcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdvcmRlcnNfdGFibGVfY29udHJvbGxlcicsXG5cdFxuXHRbXG5cdFx0Z3guc291cmNlICsgJy9saWJzL2FjdGlvbl9tYXBwZXInLFxuXHRcdGd4LnNvdXJjZSArICcvbGlicy9idXR0b25fZHJvcGRvd24nXG5cdF0sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9vcmRlcnNfdGFibGVfY29udHJvbGxlciAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEFycmF5IG9mIG1hcHBlZCBidXR0b25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciBBcnJheVxuXHRcdFx0ICovXG5cdFx0XHRtYXBwZWRCdXR0b25zID0gW10sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVGhlIG1hcHBlciBsaWJyYXJ5XG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtYXBwZXIgPSBqc2UubGlicy5hY3Rpb25fbWFwcGVyLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBQUklWQVRFIE1FVEhPRFNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBEaXNhYmxlL0VuYWJsZSB0aGUgYnV0dG9ucyBvbiB0aGUgYm90dG9tIGJ1dHRvbi1kcm9wZG93blxuXHRcdCAqIGRlcGVuZGVudCBvbiB0aGUgY2hlY2tib3hlcyBzZWxlY3Rpb25cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfdG9nZ2xlTXVsdGlBY3Rpb25CdXR0b24gPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkY2hlY2tlZCA9ICQoJ3RyW2RhdGEtcm93LWlkXSBpbnB1dFt0eXBlPVwiY2hlY2tib3hcIl06Y2hlY2tlZCcpO1xuXHRcdFx0JCgnLmpzLWJvdHRvbS1kcm9wZG93biBidXR0b24nKS5wcm9wKCdkaXNhYmxlZCcsICEkY2hlY2tlZC5sZW5ndGgpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTWFwIGFjdGlvbnMgZm9yIGV2ZXJ5IHJvdyBpbiB0aGUgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBUaGlzIG1ldGhvZCB3aWxsIG1hcCB0aGUgYWN0aW9ucyBmb3IgZWFjaFxuXHRcdCAqIHJvdyBvZiB0aGUgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbWFwUm93QWN0aW9uID0gZnVuY3Rpb24oJHRoYXQpIHtcblx0XHRcdC8qKlxuXHRcdFx0ICogUmVmZXJlbmNlIHRvIHRoZSByb3cgYWN0aW9uIGRyb3Bkb3duXG5cdFx0XHQgKiBAdmFyIHtvYmplY3QgfCBqUXVlcnl9XG5cdFx0XHQgKi9cblx0XHRcdHZhciAkZHJvcGRvd24gPSAkdGhhdC5maW5kKCcuanMtYnV0dG9uLWRyb3Bkb3duJyk7XG5cdFx0XHRcblx0XHRcdGlmICgkZHJvcGRvd24ubGVuZ3RoKSB7XG5cdFx0XHRcdF9tYXBSb3dCdXR0b25Ecm9wZG93bigkZHJvcGRvd24pO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9tYXBSb3dCdXR0b25Ecm9wZG93biA9IGZ1bmN0aW9uKCRkcm9wZG93bikge1xuXHRcdFx0dmFyIGFjdGlvbnMgPSBbXG5cdFx0XHRcdCdURVhUX1NIT1cnLFxuXHRcdFx0XHQnVEVYVF9HTV9TVEFUVVMnLFxuXHRcdFx0XHQnZGVsZXRlJyxcblx0XHRcdFx0J0JVVFRPTl9HTV9DQU5DRUwnLFxuXHRcdFx0XHQnVElUTEVfSU5WT0lDRScsXG5cdFx0XHRcdCdUSVRMRV9JTlZPSUNFX01BSUwnLFxuXHRcdFx0XHQnVElUTEVfUEFDS0lOR1NMSVAnLFxuXHRcdFx0XHQnVElUTEVfT1JERVInLFxuXHRcdFx0XHQnVElUTEVfUkVDUkVBVEVfT1JERVInLFxuXHRcdFx0XHQnVElUTEVfU0VORF9PUkRFUicsXG5cdFx0XHRcdCdURVhUX0NSRUFURV9XSVRIRFJBV0FMJyxcblx0XHRcdFx0J1RYVF9QQVJDRUxfVFJBQ0tJTkdfU0VOREJVVFRPTl9USVRMRScsXG5cdFx0XHRcdCdCVVRUT05fREhMX0xBQkVMJyxcblx0XHRcdFx0J01BSUxCRUVaX09WRVJWSUVXJyxcblx0XHRcdFx0J01BSUxCRUVaX05PVElGSUNBVElPTlMnLFxuXHRcdFx0XHQnTUFJTEJFRVpfQ09OVkVSU0FUSU9OUycsXG5cdFx0XHRcdCdCVVRUT05fSEVSTUVTJ1xuXHRcdFx0XTtcblx0XHRcdFxuXHRcdFx0Zm9yICh2YXIgaW5kZXggaW4gYWN0aW9ucykge1xuXHRcdFx0XHRfYmluZEV2ZW50SGFuZGxlcigkZHJvcGRvd24sIGFjdGlvbnNbaW5kZXhdLCAnLnNpbmdsZS1vcmRlci1kcm9wZG93bicpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGVmaW5lcyB0aGUgbGFuZ3VhZ2Ugc2VjdGlvbiBmb3IgZWFjaCB0ZXh0IHRpbGVcblx0XHQgKlxuXHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3NlY3Rpb25NYXBwaW5nID0ge1xuXHRcdFx0J1RFWFRfU0hPVyc6ICdvcmRlcnMnLFxuXHRcdFx0J1RFWFRfR01fU1RBVFVTJzogJ29yZGVycycsXG5cdFx0XHQnZGVsZXRlJzogJ2J1dHRvbnMnLFxuXHRcdFx0J0JVVFRPTl9HTV9DQU5DRUwnOiAnb3JkZXJzJyxcblx0XHRcdCdUSVRMRV9JTlZPSUNFJzogJ29yZGVycycsXG5cdFx0XHQnVElUTEVfSU5WT0lDRV9NQUlMJzogJ29yZGVycycsXG5cdFx0XHQnVElUTEVfUEFDS0lOR1NMSVAnOiAnb3JkZXJzJyxcblx0XHRcdCdUSVRMRV9PUkRFUic6ICdvcmRlcnMnLFxuXHRcdFx0J1RJVExFX1JFQ1JFQVRFX09SREVSJzogJ29yZGVycycsXG5cdFx0XHQnVElUTEVfU0VORF9PUkRFUic6ICdvcmRlcnMnLFxuXHRcdFx0J1RFWFRfQ1JFQVRFX1dJVEhEUkFXQUwnOiAnb3JkZXJzJyxcblx0XHRcdCdUWFRfUEFSQ0VMX1RSQUNLSU5HX1NFTkRCVVRUT05fVElUTEUnOiAncGFyY2VsX3NlcnZpY2VzJyxcblx0XHRcdCdCVVRUT05fREhMX0xBQkVMJzogJ29yZGVycycsXG5cdFx0XHQnTUFJTEJFRVpfT1ZFUlZJRVcnOiAnb3JkZXJzJyxcblx0XHRcdCdNQUlMQkVFWl9OT1RJRklDQVRJT05TJzogJ29yZGVycycsXG5cdFx0XHQnTUFJTEJFRVpfQ09OVkVSU0FUSU9OUyc6ICdvcmRlcnMnLFxuXHRcdFx0J0JVVFRPTl9NVUxUSV9DQU5DRUwnOiAnb3JkZXJzJyxcblx0XHRcdCdCVVRUT05fTVVMVElfQ0hBTkdFX09SREVSX1NUQVRVUyc6ICdvcmRlcnMnLFxuXHRcdFx0J0JVVFRPTl9NVUxUSV9ERUxFVEUnOiAnb3JkZXJzJyxcblx0XHRcdCdCVVRUT05fSEVSTUVTJzogJ29yZGVycycsXG5cdFx0XHQnZ2V0X2xhYmVscyc6ICdpbG94eCdcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIERlZmluZXMgdGFyZ2V0IHNlbGVjdG9yc1xuXHRcdCAqXG5cdFx0ICogQHR5cGUge29iamVjdH1cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2VsZWN0b3JNYXBwaW5nID0ge1xuXHRcdFx0J1RFWFRfU0hPVyc6ICcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmJ0bi1kZXRhaWxzJyxcblx0XHRcdCdURVhUX0dNX1NUQVRVUyc6ICcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmJ0bi11cGRhdGVfb3JkZXJfc3RhdHVzJyxcblx0XHRcdCdkZWxldGUnOiAnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgYS5idG4tZGVsZXRlJyxcblx0XHRcdCdCVVRUT05fR01fQ0FOQ0VMJzogJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IC5HTV9DQU5DRUwnLFxuXHRcdFx0J1RJVExFX0lOVk9JQ0UnOiAnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgYS5idG4taW52b2ljZScsXG5cdFx0XHQnVElUTEVfSU5WT0lDRV9NQUlMJzogJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IC5HTV9JTlZPSUNFX01BSUwnLFxuXHRcdFx0J1RJVExFX1BBQ0tJTkdTTElQJzogJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuYnRuLXBhY2tpbmdfc2xpcCcsXG5cdFx0XHQnVElUTEVfT1JERVInOiAnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgYS5idG4tb3JkZXJfY29uZmlybWF0aW9uJyxcblx0XHRcdCdUSVRMRV9SRUNSRUFURV9PUkRFUic6ICcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmJ0bi1yZWNyZWF0ZV9vcmRlcl9jb25maXJtYXRpb24nLFxuXHRcdFx0J1RJVExFX1NFTkRfT1JERVInOiAnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgLkdNX1NFTkRfT1JERVInLFxuXHRcdFx0J1RFWFRfQ1JFQVRFX1dJVEhEUkFXQUwnOiAnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgYS5idG4tY3JlYXRlX3dpdGhkcmF3YWwnLFxuXHRcdFx0J1RYVF9QQVJDRUxfVFJBQ0tJTkdfU0VOREJVVFRPTl9USVRMRSc6ICcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmJ0bi1hZGRfdHJhY2tpbmdfY29kZScsXG5cdFx0XHQnQlVUVE9OX0RITF9MQUJFTCc6ICcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmJ0bi1kaGxfbGFiZWwnLFxuXHRcdFx0J01BSUxCRUVaX09WRVJWSUVXJzogJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuY29udGV4dF92aWV3X2J1dHRvbi5idG5fbGVmdCcsXG5cdFx0XHQnTUFJTEJFRVpfTk9USUZJQ0FUSU9OUyc6ICcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmNvbnRleHRfdmlld19idXR0b24uYnRuX21pZGRsZScsXG5cdFx0XHQnTUFJTEJFRVpfQ09OVkVSU0FUSU9OUyc6ICcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmNvbnRleHRfdmlld19idXR0b24uYnRuX3JpZ2h0Jyxcblx0XHRcdCdCVVRUT05fTVVMVElfQ0FOQ0VMJzogJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuYnRuLW11bHRpX2NhbmNlbCcsXG5cdFx0XHQnQlVUVE9OX01VTFRJX0NIQU5HRV9PUkRFUl9TVEFUVVMnOiAnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgYS5idG4tdXBkYXRlX29yZGVyX3N0YXR1cycsXG5cdFx0XHQnQlVUVE9OX01VTFRJX0RFTEVURSc6ICcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmJ0bi1tdWx0aV9kZWxldGUnLFxuXHRcdFx0J0JVVFRPTl9IRVJNRVMnOiAnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgYS5idG4taGVybWVzJyxcblx0XHRcdCdnZXRfbGFiZWxzJzogJyNpbG94eF9vcmRlcnMnXG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2dldEFjdGlvbkNhbGxiYWNrID0gZnVuY3Rpb24oYWN0aW9uKSB7XG5cdFx0XHRzd2l0Y2ggKGFjdGlvbikge1xuXHRcdFx0XHRjYXNlICdURVhUX1NIT1cnOlxuXHRcdFx0XHRcdHJldHVybiBfc2hvd09yZGVyQ2FsbGJhY2s7XG5cdFx0XHRcdGNhc2UgJ1RFWFRfR01fU1RBVFVTJzpcblx0XHRcdFx0XHRyZXR1cm4gX2NoYW5nZU9yZGVyU3RhdHVzQ2FsbGJhY2s7XG5cdFx0XHRcdGNhc2UgJ2RlbGV0ZSc6XG5cdFx0XHRcdFx0cmV0dXJuIF9kZWxldGVDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnQlVUVE9OX0dNX0NBTkNFTCc6XG5cdFx0XHRcdFx0cmV0dXJuIF9jYW5jZWxDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnVElUTEVfSU5WT0lDRSc6XG5cdFx0XHRcdFx0cmV0dXJuIF9pbnZvaWNlQ2FsbGJhY2s7XG5cdFx0XHRcdGNhc2UgJ1RJVExFX0lOVk9JQ0VfTUFJTCc6XG5cdFx0XHRcdFx0cmV0dXJuIF9lbWFpbEludm9pY2VDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnVElUTEVfUEFDS0lOR1NMSVAnOlxuXHRcdFx0XHRcdHJldHVybiBfcGFja2luZ1NsaXBDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnVElUTEVfT1JERVInOlxuXHRcdFx0XHRcdHJldHVybiBfb3JkZXJDb25maXJtYXRpb25DYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnVElUTEVfUkVDUkVBVEVfT1JERVInOlxuXHRcdFx0XHRcdHJldHVybiBfcmVjcmVhdGVPcmRlckNvbmZpcm1hdGlvbkNhbGxiYWNrO1xuXHRcdFx0XHRjYXNlICdUSVRMRV9TRU5EX09SREVSJzpcblx0XHRcdFx0XHRyZXR1cm4gX3NlbmRPcmRlckNvbmZpcm1hdGlvbkNhbGxiYWNrO1xuXHRcdFx0XHRjYXNlICdURVhUX0NSRUFURV9XSVRIRFJBV0FMJzpcblx0XHRcdFx0XHRyZXR1cm4gX3dpdGhkcmF3YWxDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnVFhUX1BBUkNFTF9UUkFDS0lOR19TRU5EQlVUVE9OX1RJVExFJzpcblx0XHRcdFx0XHRyZXR1cm4gX2FkZFRyYWNraW5nQ29kZUNhbGxiYWNrO1xuXHRcdFx0XHRjYXNlICdCVVRUT05fREhMX0xBQkVMJzpcblx0XHRcdFx0XHRyZXR1cm4gX2RobExhYmVsQ2FsbGJhY2s7XG5cdFx0XHRcdGNhc2UgJ01BSUxCRUVaX09WRVJWSUVXJzpcblx0XHRcdFx0XHRyZXR1cm4gX21haWxCZWV6T3ZlcnZpZXdDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnTUFJTEJFRVpfTk9USUZJQ0FUSU9OUyc6XG5cdFx0XHRcdFx0cmV0dXJuIF9tYWlsQmVlek5vdGlmaWNhdGlvbnNDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnTUFJTEJFRVpfQ09OVkVSU0FUSU9OUyc6XG5cdFx0XHRcdFx0cmV0dXJuIF9tYWlsQmVlekNvbnZlcnNhdGlvbnNDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnQlVUVE9OX01VTFRJX0NBTkNFTCc6XG5cdFx0XHRcdFx0cmV0dXJuIF9tdWx0aUNhbmNlbENhbGxiYWNrO1xuXHRcdFx0XHRjYXNlICdCVVRUT05fTVVMVElfQ0hBTkdFX09SREVSX1NUQVRVUyc6XG5cdFx0XHRcdFx0cmV0dXJuIF9tdWx0aUNoYW5nZU9yZGVyU3RhdHVzQ2FsbGJhY2s7XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9NVUxUSV9ERUxFVEUnOlxuXHRcdFx0XHRcdHJldHVybiBfbXVsdGlEZWxldGVDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnQlVUVE9OX0hFUk1FUyc6XG5cdFx0XHRcdFx0cmV0dXJuIF9oZXJtZXNDYWxsYmFjaztcblx0XHRcdFx0Y2FzZSAnZ2V0X2xhYmVscyc6XG5cdFx0XHRcdFx0cmV0dXJuIF9pbG94eENhbGxiYWNrO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9zaG93T3JkZXJDYWxsYmFjayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgb3JkZXJJZCA9ICQoZXZlbnQudGFyZ2V0KS5wYXJlbnRzKCd0cicpLmRhdGEoJ3Jvdy1pZCcpO1xuXHRcdFx0dmFyIHVybCA9ICQoX3NlbGVjdG9yTWFwcGluZy5URVhUX1NIT1cpLmF0dHIoJ2hyZWYnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybC5yZXBsYWNlKC9vSUQ9KC4qKSYvLCAnb0lEPScgKyBvcmRlcklkICsgJyYnKSwgJ19zZWxmJyk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2NoYW5nZU9yZGVyU3RhdHVzQ2FsbGJhY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyIG9yZGVySWQgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5kYXRhKCdyb3ctaWQnKTtcblx0XHRcdCQoJyNnbV9vcmRlcl9pZCcpLnZhbChvcmRlcklkKTtcblx0XHRcdCQoJy5neC1vcmRlcnMtdGFibGUgLnNpbmdsZS1jaGVja2JveCcpLnJlbW92ZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHQkKCcuZ3gtb3JkZXJzLXRhYmxlIGlucHV0OmNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcblx0XHRcdCQoZXZlbnQudGFyZ2V0KS5wYXJlbnRzKCd0cicpLmVxKDApLmZpbmQoJy5zaW5nbGUtY2hlY2tib3gnKS5hZGRDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0JChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZXEoMCkuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG5cdFx0XHQkKF9zZWxlY3Rvck1hcHBpbmcuVEVYVF9HTV9TVEFUVVMpLmNsaWNrKCk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2RlbGV0ZUNhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBvcmRlcklkID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZGF0YSgncm93LWlkJyk7XG5cdFx0XHR2YXIgJGRlbGV0ZSA9ICQoX3NlbGVjdG9yTWFwcGluZy5kZWxldGUpO1xuXHRcdFx0JGRlbGV0ZS5kYXRhKCdvcmRlcl9pZCcsIG9yZGVySWQpO1xuXHRcdFx0JGRlbGV0ZS5nZXQoMCkuY2xpY2soKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfY2FuY2VsQ2FsbGJhY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyIG9yZGVySWQgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5kYXRhKCdyb3ctaWQnKTtcblx0XHRcdCQoJyNnbV9vcmRlcl9pZCcpLnZhbChvcmRlcklkKTtcblx0XHRcdCQoJy5neC1vcmRlcnMtdGFibGUgLnNpbmdsZS1jaGVja2JveCcpLnJlbW92ZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHQkKCcuZ3gtb3JkZXJzLXRhYmxlIGlucHV0OmNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcblx0XHRcdCQoZXZlbnQudGFyZ2V0KS5wYXJlbnRzKCd0cicpLmVxKDApLmZpbmQoJy5zaW5nbGUtY2hlY2tib3gnKS5hZGRDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0JChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZXEoMCkuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG5cdFx0XHQkKF9zZWxlY3Rvck1hcHBpbmcuQlVUVE9OX01VTFRJX0NBTkNFTCkuY2xpY2soKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfaW52b2ljZUNhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBvcmRlcklkID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZGF0YSgncm93LWlkJyk7XG5cdFx0XHR2YXIgdXJsID0gJChfc2VsZWN0b3JNYXBwaW5nLlRJVExFX0lOVk9JQ0UpLmF0dHIoJ2hyZWYnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybC5yZXBsYWNlKC9vSUQ9KC4qKSYvLCAnb0lEPScgKyBvcmRlcklkICsgJyYnKSwgJ19ibGFuaycpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9lbWFpbEludm9pY2VDYWxsYmFjayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgb3JkZXJJZCA9ICQoZXZlbnQudGFyZ2V0KS5wYXJlbnRzKCd0cicpLmRhdGEoJ3Jvdy1pZCcpO1xuXHRcdFx0JCgnI2dtX29yZGVyX2lkJykudmFsKG9yZGVySWQpO1xuXHRcdFx0JCgnLkdNX0lOVk9JQ0VfTUFJTCcpLmNsaWNrKCk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX3BhY2tpbmdTbGlwQ2FsbGJhY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyIG9yZGVySWQgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5kYXRhKCdyb3ctaWQnKTtcblx0XHRcdHZhciB1cmwgPSAkKF9zZWxlY3Rvck1hcHBpbmcuVElUTEVfUEFDS0lOR1NMSVApLmF0dHIoJ2hyZWYnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybC5yZXBsYWNlKC9vSUQ9KC4qKSYvLCAnb0lEPScgKyBvcmRlcklkICsgJyYnKSwgJ19ibGFuaycpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9vcmRlckNvbmZpcm1hdGlvbkNhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBvcmRlcklkID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZGF0YSgncm93LWlkJyk7XG5cdFx0XHR2YXIgdXJsID0gJChfc2VsZWN0b3JNYXBwaW5nLlRJVExFX09SREVSKS5hdHRyKCdocmVmJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwucmVwbGFjZSgvb0lEPSguKikmLywgJ29JRD0nICsgb3JkZXJJZCArICcmJyksICdfYmxhbmsnKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfcmVjcmVhdGVPcmRlckNvbmZpcm1hdGlvbkNhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBvcmRlcklkID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZGF0YSgncm93LWlkJyk7XG5cdFx0XHR2YXIgdXJsID0gJChfc2VsZWN0b3JNYXBwaW5nLlRJVExFX1JFQ1JFQVRFX09SREVSKS5hdHRyKCdocmVmJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwucmVwbGFjZSgvb0lEPSguKikmLywgJ29JRD0nICsgb3JkZXJJZCArICcmJyksICdfYmxhbmsnKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfc2VuZE9yZGVyQ29uZmlybWF0aW9uQ2FsbGJhY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyIG9yZGVySWQgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5kYXRhKCdyb3ctaWQnKTtcblx0XHRcdCQoJyNnbV9vcmRlcl9pZCcpLnZhbChvcmRlcklkKTtcblx0XHRcdCQoJy5HTV9TRU5EX09SREVSJykuY2xpY2soKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfd2l0aGRyYXdhbENhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBvcmRlcklkID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZGF0YSgncm93LWlkJyk7XG5cdFx0XHR2YXIgdXJsID0gJChfc2VsZWN0b3JNYXBwaW5nLlRFWFRfQ1JFQVRFX1dJVEhEUkFXQUwpLmF0dHIoJ2hyZWYnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybC5yZXBsYWNlKC9vcmRlcj1bXiZdKi8sICdvcmRlcl9pZD0nICsgb3JkZXJJZCksICdfYmxhbmsnKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfYWRkVHJhY2tpbmdDb2RlQ2FsbGJhY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyIG9yZGVySWQgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5kYXRhKCdyb3ctaWQnKTtcblx0XHRcdHZhciAkdGFyZ2V0ID0gJChfc2VsZWN0b3JNYXBwaW5nLlRYVF9QQVJDRUxfVFJBQ0tJTkdfU0VOREJVVFRPTl9USVRMRSk7XG5cdFx0XHQkdGFyZ2V0LmRhdGEoJ29yZGVyX2lkJywgb3JkZXJJZCk7XG5cdFx0XHQkdGFyZ2V0LmdldCgwKS5jbGljaygpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9kaGxMYWJlbENhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBvcmRlcklkID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZGF0YSgncm93LWlkJyk7XG5cdFx0XHR2YXIgdXJsID0gJChfc2VsZWN0b3JNYXBwaW5nLkJVVFRPTl9ESExfTEFCRUwpLmF0dHIoJ2hyZWYnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybC5yZXBsYWNlKC9vSUQ9KC4qKS8sICdvSUQ9JyArIG9yZGVySWQpLCAnX2JsYW5rJyk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX21haWxCZWV6T3ZlcnZpZXdDYWxsYmFjayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgb3JkZXJJZCA9ICQoZXZlbnQudGFyZ2V0KS5wYXJlbnRzKCd0cicpLmRhdGEoJ3Jvdy1pZCcpO1xuXHRcdFx0dmFyICR0YXJnZXQgPSAkKF9zZWxlY3Rvck1hcHBpbmcuTUFJTEJFRVpfT1ZFUlZJRVcpO1xuXHRcdFx0dmFyIHVybCA9ICR0YXJnZXQuYXR0cignb25jbGljaycpO1xuXHRcdFx0dXJsID0gdXJsLnJlcGxhY2UoL29JRD0oLiopJi8sICdvSUQ9JyArIG9yZGVySWQgKyAnJicpO1xuXHRcdFx0JHRhcmdldC5hdHRyKCdvbmNsaWNrJywgdXJsKTtcblx0XHRcdCR0YXJnZXQuZ2V0KDApLmNsaWNrKCk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX21haWxCZWV6Tm90aWZpY2F0aW9uc0NhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBvcmRlcklkID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuZGF0YSgncm93LWlkJyk7XG5cdFx0XHR2YXIgJHRhcmdldCA9ICQoX3NlbGVjdG9yTWFwcGluZy5NQUlMQkVFWl9OT1RJRklDQVRJT05TKTtcblx0XHRcdHZhciB1cmwgPSAkdGFyZ2V0LmF0dHIoJ29uY2xpY2snKTtcblx0XHRcdHVybCA9IHVybC5yZXBsYWNlKC9vSUQ9KC4qKSYvLCAnb0lEPScgKyBvcmRlcklkICsgJyYnKTtcblx0XHRcdCR0YXJnZXQuYXR0cignb25jbGljaycsIHVybCk7XG5cdFx0XHQkdGFyZ2V0LmdldCgwKS5jbGljaygpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9tYWlsQmVlekNvbnZlcnNhdGlvbnNDYWxsYmFjayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgb3JkZXJJZCA9ICQoZXZlbnQudGFyZ2V0KS5wYXJlbnRzKCd0cicpLmRhdGEoJ3Jvdy1pZCcpO1xuXHRcdFx0dmFyICR0YXJnZXQgPSAkKF9zZWxlY3Rvck1hcHBpbmcuTUFJTEJFRVpfQ09OVkVSU0FUSU9OUyk7XG5cdFx0XHR2YXIgdXJsID0gJHRhcmdldC5hdHRyKCdvbmNsaWNrJyk7XG5cdFx0XHR1cmwgPSB1cmwucmVwbGFjZSgvb0lEPSguKikmLywgJ29JRD0nICsgb3JkZXJJZCArICcmJyk7XG5cdFx0XHQkdGFyZ2V0LmF0dHIoJ29uY2xpY2snLCB1cmwpO1xuXHRcdFx0JHRhcmdldC5nZXQoMCkuY2xpY2soKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfaGVybWVzQ2FsbGJhY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyIG9yZGVySWQgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5kYXRhKCdyb3ctaWQnKTtcblx0XHRcdHZhciAkdGFyZ2V0ID0gJChfc2VsZWN0b3JNYXBwaW5nLkJVVFRPTl9IRVJNRVMpO1xuXHRcdFx0dmFyIHVybCA9ICR0YXJnZXQuYXR0cignaHJlZicpO1xuXHRcdFx0dXJsID0gdXJsLnJlcGxhY2UoL29yZGVyc19pZD0oLiopLywgJ29yZGVyc19pZD0nICsgb3JkZXJJZCk7XG5cdFx0XHQkdGFyZ2V0LmF0dHIoJ2hyZWYnLCB1cmwpO1xuXHRcdFx0JHRhcmdldC5nZXQoMCkuY2xpY2soKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfaWxveHhDYWxsYmFjayA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgJHRhcmdldCA9ICQoX3NlbGVjdG9yTWFwcGluZy5nZXRfbGFiZWxzKTtcblx0XHRcdCR0YXJnZXQuY2xpY2soKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfbXVsdGlDaGFuZ2VPcmRlclN0YXR1c0NhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdCQoX3NlbGVjdG9yTWFwcGluZy5CVVRUT05fTVVMVElfQ0hBTkdFX09SREVSX1NUQVRVUykuZ2V0KDApLmNsaWNrKCk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX211bHRpRGVsZXRlQ2FsbGJhY2sgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0JChfc2VsZWN0b3JNYXBwaW5nLkJVVFRPTl9NVUxUSV9ERUxFVEUpLmdldCgwKS5jbGljaygpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9tdWx0aUNhbmNlbENhbGxiYWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdCQoX3NlbGVjdG9yTWFwcGluZy5CVVRUT05fTVVMVElfQ0FOQ0VMKS5nZXQoMCkuY2xpY2soKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE1hcCB0YWJsZSBhY3Rpb25zIHRvIGJvdHRvbSBkcm9wZG93biBidXR0b24uXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbWFwVGFibGVBY3Rpb25zID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJGRyb3Bkb3duID0gJCgnI29yZGVycy10YWJsZS1kcm9wZG93bicpO1xuXHRcdFx0XG5cdFx0XHRfYmluZEV2ZW50SGFuZGxlcigkZHJvcGRvd24sICdCVVRUT05fTVVMVElfQ0hBTkdFX09SREVSX1NUQVRVUycpO1xuXHRcdFx0XG5cdFx0XHRpZiAoJChfc2VsZWN0b3JNYXBwaW5nLmdldF9sYWJlbHMpLmxlbmd0aCkge1xuXHRcdFx0XHRfYmluZEV2ZW50SGFuZGxlcigkZHJvcGRvd24sICdnZXRfbGFiZWxzJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdF9iaW5kRXZlbnRIYW5kbGVyKCRkcm9wZG93biwgJ0JVVFRPTl9NVUxUSV9ERUxFVEUnKTtcblx0XHRcdF9iaW5kRXZlbnRIYW5kbGVyKCRkcm9wZG93biwgJ0JVVFRPTl9NVUxUSV9DQU5DRUwnKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE1hcCBhY3Rpb25zIGZvciBldmVyeSByb3cgaW4gdGhlIHRhYmxlIGdlbmVyaWNhbGx5LlxuXHRcdCAqXG5cdFx0ICogVGhpcyBtZXRob2Qgd2lsbCB1c2UgdGhlIGFjdGlvbl9tYXBwZXIgbGlicmFyeSB0byBtYXAgdGhlIGFjdGlvbnMgZm9yIGVhY2hcblx0XHQgKiByb3cgb2YgdGhlIHRhYmxlLiBJdCBtYXBzIG9ubHkgdGhvc2UgYnV0dG9ucywgdGhhdCBoYXZlbid0IGFscmVhZHkgZXhwbGljaXRseVxuXHRcdCAqIG1hcHBlZCBieSB0aGUgX21hcFJvd0FjdGlvbnMgZnVuY3Rpb24uXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbWFwVW5tYXBwZWRSb3dBY3Rpb25zID0gZnVuY3Rpb24oJHRoaXMpIHtcblx0XHRcdHZhciB1bm1hcHBlZFJvd0FjdGlvbnMgPSBbXTtcblx0XHRcdCQoJy5hY3Rpb25fYnV0dG9ucyAuZXh0ZW5kZWRfc2luZ2xlX2FjdGlvbnMgYSwnICtcblx0XHRcdFx0Jy5hY3Rpb25fYnV0dG9ucyAuZXh0ZW5kZWRfc2luZ2xlX2FjdGlvbnMgYnV0dG9uLCcgK1xuXHRcdFx0XHQnLmFjdGlvbl9idXR0b25zIC5leHRlbmRlZF9zaW5nbGVfYWN0aW9ucyBpbnB1dFt0eXBlPVwiYnV0dG9uXCJdLCcgK1xuXHRcdFx0XHQnLmFjdGlvbl9idXR0b25zIC5leHRlbmRlZF9zaW5nbGVfYWN0aW9ucyBpbnB1dFt0eXBlPVwic3VibWl0XCJdJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKCFfYWxyZWFkeU1hcHBlZCgkKHRoaXMpKSkge1xuXHRcdFx0XHRcdHVubWFwcGVkUm93QWN0aW9ucy5wdXNoKCQodGhpcykpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0dmFyIG9yZGVySWQgPSAkdGhpcy5kYXRhKCdyb3ctaWQnKSxcblx0XHRcdFx0JGRyb3Bkb3duID0gJHRoaXMuZmluZCgnLmpzLWJ1dHRvbi1kcm9wZG93bicpO1xuXHRcdFx0XG5cdFx0XHQkLmVhY2godW5tYXBwZWRSb3dBY3Rpb25zLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyICRidXR0b24gPSAkKHRoaXMpO1xuXHRcdFx0XHR2YXIgY2FsbGJhY2sgPSBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRpZiAoJGJ1dHRvbi5wcm9wKCdocmVmJykgIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRcdFx0JGJ1dHRvbi5wcm9wKCdocmVmJywgJGJ1dHRvbi5wcm9wKCdocmVmJykucmVwbGFjZSgvb0lEPSguKilcXGQoPz0mKT8vLCAnb0lEPScgKyBvcmRlcklkKSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdCRidXR0b24uZ2V0KDApLmNsaWNrKCk7XG5cdFx0XHRcdH07XG5cdFx0XHRcdFxuXHRcdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKCRkcm9wZG93biwgJGJ1dHRvbi50ZXh0KCksICcnLCBjYWxsYmFjayk7XG5cdFx0XHRcdG1hcHBlZEJ1dHRvbnMucHVzaCgkYnV0dG9uKTtcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9tYXBVbm1hcHBlZE11bHRpQWN0aW9ucyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHVubWFwcGVkTXVsdGlBY3Rpb25zID0gW107XG5cdFx0XHQkKCcuYWN0aW9uX2J1dHRvbnMgLmV4dGVuZGVkX211bHRpX2FjdGlvbnMgYSwnICtcblx0XHRcdFx0Jy5hY3Rpb25fYnV0dG9ucyAuZXh0ZW5kZWRfbXVsdGlfYWN0aW9ucyBidXR0b24sJyArXG5cdFx0XHRcdCcuYWN0aW9uX2J1dHRvbnMgLmV4dGVuZGVkX211bHRpX2FjdGlvbnMgaW5wdXRbdHlwZT1cImJ1dHRvblwiXSwnICtcblx0XHRcdFx0Jy5hY3Rpb25fYnV0dG9ucyAuZXh0ZW5kZWRfbXVsdGlfYWN0aW9ucyBpbnB1dFt0eXBlPVwic3VibWl0XCJdJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKCFfYWxyZWFkeU1hcHBlZCgkKHRoaXMpKSkge1xuXHRcdFx0XHRcdHVubWFwcGVkTXVsdGlBY3Rpb25zLnB1c2goJCh0aGlzKSk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHR2YXIgJGRyb3Bkb3duID0gJCgnI29yZGVycy10YWJsZS1kcm9wZG93bicpO1xuXHRcdFx0JC5lYWNoKHVubWFwcGVkTXVsdGlBY3Rpb25zLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyICRidXR0b24gPSAkKHRoaXMpO1xuXHRcdFx0XHR2YXIgY2FsbGJhY2sgPSBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQkYnV0dG9uLmdldCgwKS5jbGljaygpO1xuXHRcdFx0XHR9O1xuXHRcdFx0XHRcblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLm1hcEFjdGlvbigkZHJvcGRvd24sICRidXR0b24udGV4dCgpLCAnJywgY2FsbGJhY2spO1xuXHRcdFx0XHRtYXBwZWRCdXR0b25zLnB1c2goJGJ1dHRvbik7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENoZWNrcyBpZiB0aGUgYnV0dG9uIHdhcyBhbHJlYWR5IG1hcHBlZFxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2FscmVhZHlNYXBwZWQgPSBmdW5jdGlvbigkYnV0dG9uKSB7XG5cdFx0XHRmb3IgKHZhciBpbmRleCBpbiBtYXBwZWRCdXR0b25zKSB7XG5cdFx0XHRcdGlmICgkYnV0dG9uLmlzKG1hcHBlZEJ1dHRvbnNbaW5kZXhdKSkge1xuXHRcdFx0XHRcdHJldHVybiB0cnVlO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBBZGQgQnV0dG9uIHRvIE1hcHBlZCBBcnJheVxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIGJ1dHRvblNlbGVjdG9yXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59XG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYWRkQnV0dG9uVG9NYXBwZWRBcnJheSA9IGZ1bmN0aW9uKGJ1dHRvblNlbGVjdG9yKSB7XG5cdFx0XHRpZiAobWFwcGVkQnV0dG9uc1tidXR0b25TZWxlY3Rvcl0gIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRyZXR1cm4gdHJ1ZTtcblx0XHRcdH1cblx0XHRcdG1hcHBlZEJ1dHRvbnNbYnV0dG9uU2VsZWN0b3JdID0gJChidXR0b25TZWxlY3Rvcik7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBCaW5kIEV2ZW50IGhhbmRsZXJcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSAkZHJvcGRvd25cblx0XHQgKiBAcGFyYW0gYWN0aW9uXG5cdFx0ICogQHBhcmFtIGN1c3RvbVJlY2VudEJ1dHRvblNlbGVjdG9yXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYmluZEV2ZW50SGFuZGxlciA9IGZ1bmN0aW9uKCRkcm9wZG93biwgYWN0aW9uLCBjdXN0b21SZWNlbnRCdXR0b25TZWxlY3Rvcikge1xuXHRcdFx0dmFyIHRhcmdldFNlbGVjdG9yID0gX3NlbGVjdG9yTWFwcGluZ1thY3Rpb25dLFxuXHRcdFx0XHRzZWN0aW9uID0gX3NlY3Rpb25NYXBwaW5nW2FjdGlvbl0sXG5cdFx0XHRcdGNhbGxiYWNrID0gX2dldEFjdGlvbkNhbGxiYWNrKGFjdGlvbiksXG5cdFx0XHRcdGN1c3RvbUVsZW1lbnQgPSAkKGN1c3RvbVJlY2VudEJ1dHRvblNlbGVjdG9yKS5sZW5ndGggPyAkKGN1c3RvbVJlY2VudEJ1dHRvblNlbGVjdG9yKSA6XG5cdFx0XHRcdCAgICAgICAgICAgICAgICAkZHJvcGRvd247XG5cdFx0XHRpZiAoJCh0YXJnZXRTZWxlY3RvcikubGVuZ3RoKSB7XG5cdFx0XHRcdF9hZGRCdXR0b25Ub01hcHBlZEFycmF5KHRhcmdldFNlbGVjdG9yKTtcblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLm1hcEFjdGlvbigkZHJvcGRvd24sIGFjdGlvbiwgc2VjdGlvbiwgY2FsbGJhY2ssIGN1c3RvbUVsZW1lbnQpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRml4IGZvciByb3cgc2VsZWN0aW9uIGNvbnRyb2xzLlxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2ZpeFJvd1NlbGVjdGlvbkZvckNvbnRyb2xFbGVtZW50cyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnaW5wdXQuY2hlY2tib3hbbmFtZT1cImdtX211bHRpX3N0YXR1c1tdXCJdJylcblx0XHRcdFx0LmFkZCgnLnNpbmdsZS1jaGVja2JveCcpXG5cdFx0XHRcdC5hZGQoJ2EuYWN0aW9uLWljb24nKVxuXHRcdFx0XHQuYWRkKCcuanMtYnV0dG9uLWRyb3Bkb3duJylcblx0XHRcdFx0LmFkZCgndHIuZGF0YVRhYmxlUm93IGEnKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdFx0XHRfdG9nZ2xlTXVsdGlBY3Rpb25CdXR0b24oKTtcblx0XHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Ly8gV2FpdCB1bnRpbCB0aGUgYnV0dG9ucyBhcmUgY29udmVydGVkIHRvIGRyb3Bkb3duIGZvciBldmVyeSByb3cuXG5cdFx0XHR2YXIgaW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKCQoJy5qcy1idXR0b24tZHJvcGRvd24nKS5sZW5ndGgpIHtcblx0XHRcdFx0XHRjbGVhckludGVydmFsKGludGVydmFsKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRfbWFwVGFibGVBY3Rpb25zKCk7XG5cdFx0XHRcdFx0X21hcFVubWFwcGVkTXVsdGlBY3Rpb25zKCk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0dmFyIHRhYmxlQWN0aW9ucyA9IG1hcHBlZEJ1dHRvbnM7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gUmVtb3ZlIE1haWxiZWV6IGNvbnZlcnNhdGlvbnMgYmFkZ2UuXG5cdFx0XHRcdFx0X2FkZEJ1dHRvblRvTWFwcGVkQXJyYXkoJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuY29udGV4dF92aWV3X2J1dHRvbi5idG5fcmlnaHQnKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQkKCcuZ3gtb3JkZXJzLXRhYmxlIHRyJykubm90KCcuZGF0YVRhYmxlSGVhZGluZ1JvdycpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRtYXBwZWRCdXR0b25zID0gW107XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdGZvciAodmFyIGluZGV4IGluIHRhYmxlQWN0aW9ucykge1xuXHRcdFx0XHRcdFx0XHRtYXBwZWRCdXR0b25zW2luZGV4XSA9IHRhYmxlQWN0aW9uc1tpbmRleF07XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdF9tYXBSb3dBY3Rpb24oJCh0aGlzKSk7XG5cdFx0XHRcdFx0XHRfbWFwVW5tYXBwZWRSb3dBY3Rpb25zKCQodGhpcykpO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdF9maXhSb3dTZWxlY3Rpb25Gb3JDb250cm9sRWxlbWVudHMoKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBJbml0aWFsaXplIGNoZWNrYm94ZXNcblx0XHRcdFx0XHRfdG9nZ2xlTXVsdGlBY3Rpb25CdXR0b24oKTtcblx0XHRcdFx0fVxuXHRcdFx0fSwgMzAwKTtcblx0XHRcdFxuXHRcdFx0Ly8gQ2hlY2sgZm9yIHNlbGVjdGVkIGNoZWNrYm94ZXMgYWxzb1xuXHRcdFx0Ly8gYmVmb3JlIGFsbCByb3dzIGFuZCB0aGVpciBkcm9wZG93biB3aWRnZXRzIGhhdmUgYmVlbiBpbml0aWFsaXplZC5cblx0XHRcdF90b2dnbGVNdWx0aUFjdGlvbkJ1dHRvbigpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
