'use strict';

/* --------------------------------------------------------------
 orders_modal_layer.js 2016-03-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Modal Layer Module
 *
 * This module will open a modal layer for order actions like deleting or changing the oder status.
 *
 * @module Compatibility/orders_modal_layer
 */
gx.compatibility.module('orders_modal_layer', ['xhr', 'fallback'],

/**  @lends module:Compatibility/orders_modal_layer */

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
  * Modal Selector
  *
  * @type {object}
  */
	$modal = $('#modal_layer_container'),


	/**
  * Checkboxes Selector
  *
  * @type {object}
  */
	$checkboxes = $('.gx-orders-table tr:not(.dataTableHeadingRow) input'),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {
		detail_page: false,
		comment: ''
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
	module = {};

	// ------------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	var _openDeleteDialog = function _openDeleteDialog(event) {

		var $form = $('#delete_confirm_form');
		$form.attr('action', $form.attr('action') + '&oID=' + $this.data('order_id'));

		event.preventDefault();

		var title = jse.core.lang.translate('TEXT_INFO_HEADING_DELETE_ORDER', 'orders').replace('%s', $this.data('order_id'));

		$form.dialog({
			'title': title,
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($form),
			'width': 420
		});
	};

	var _openTrackingCodeDeleteDialog = function _openTrackingCodeDeleteDialog(event) {
		var $form = $('#delete_tracking_code_confirm_form');
		var data_set = jse.libs.fallback._data($(this), 'orders_modal_layer');
		$form.dialog({
			'title': jse.core.lang.translate('TXT_PARCEL_TRACKING_DELETE_BUTTON', 'parcel_services').replace('%s', data_set.tracking_code),
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
				'class': 'btn btn-primary',
				'click': function click() {
					$(this).dialog('close');

					var url = 'request_port.php?module=ParcelServices&action=delete_tracking_code';

					jse.libs.xhr.post({
						'url': url,
						'data': {
							'tracking_code_id': data_set.tracking_code_id,
							'order_id': data_set.order_id,
							'page_token': data_set.page_token
						}
					}).done(function (response) {
						$('#tracking_code_wrapper > .frame-content > table').html(response.html);
					});
				}
			}],
			'width': 420
		});
	};

	var _openMultiDeleteDialog = function _openMultiDeleteDialog(event) {

		var $form = $('#multi_delete_confirm_form'),
		    orderId = 0;

		event.preventDefault();

		if ($checkboxes.filter(':checked').length === 0) {
			return false;
		}

		_readSelectedOrders($form);

		$form.attr('action', $form.attr('action') + '&oID=' + $this.data('order_id'));

		$form.dialog({
			'title': jse.core.lang.translate('TEXT_INFO_HEADING_MULTI_DELETE_ORDER', 'orders').replace('%s', $this.data('order_id')),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($form),
			'width': 420
		});
	};

	var _openMultiCancelDialog = function _openMultiCancelDialog(event) {
		var $form = $('#multi_cancel_confirm_form');
		event.preventDefault();

		if (options.detail_page) {
			// Orders detail page
			$form.append('<input type="hidden" name="gm_multi_status[]" value="' + options.order_id + '" />');
			$form.find('.selected_orders').text(options.order_id);
			$form.find('textarea[name="gm_comments"]').html(options.comment);
		} else {
			// Orders page
			if ($checkboxes.filter(':checked').length === 0) {
				return false;
			}
			_readSelectedOrders($form);
		}

		$form.attr('action', $form.attr('action') + '?oID=' + $this.data('order_id'));

		$form.dialog({
			'title': jse.core.lang.translate('TEXT_INFO_HEADING_MULTI_CANCEL_ORDER', 'orders').replace('%s', $this.data('order_id')),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($form),
			'width': 420
		});
	};

	var _openUpdateOrdersStatusDialog = function _openUpdateOrdersStatusDialog(event) {
		var $form = $('#update_orders_status_form');

		event.preventDefault();

		if (options.detail_page) {
			// Orders detail page
			$form.append('<input type="hidden" name="gm_multi_status[]" value="' + options.order_id + '" />');
			$form.find('.selected_orders').text(options.order_id);
			$form.find('textarea[name="gm_comments"]').html(options.comment);
		} else {
			// Orders page
			if ($checkboxes.filter(':checked').length === 0) {
				return false;
			}
			_readSelectedOrders($form);
		}

		$form.dialog({
			'title': jse.core.lang.translate('HEADING_GM_STATUS', 'orders'),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($form),
			'width': 580
		});
	};

	var _openTrackingCodeDialog = function _openTrackingCodeDialog(event) {

		var $form = $('#add_tracking_code_form');

		event.preventDefault();
		$form.dialog({
			'title': jse.core.lang.translate('TXT_PARCEL_TRACKING_HEADING', 'parcel_services').replace('%s', $this.data('order_id')),
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': _getModalButtons($form),
			'width': 420
		});
	};

	var _getModalButtons = function _getModalButtons($form) {
		var buttons = [{
			'text': jse.core.lang.translate('close', 'buttons'),
			'class': 'btn',
			'click': function click() {
				$(this).dialog('close');
			}
		}];
		switch (options.action) {
			case 'delete':
			case 'multi_delete':
				buttons.push({
					'text': jse.core.lang.translate('delete', 'buttons'),
					'class': 'btn btn-primary',
					'click': function click() {
						$form.submit();
					}
				});
				break;
			case 'add_tracking_code':
				buttons.push({
					'text': jse.core.lang.translate('add', 'buttons'),
					'class': 'btn btn-primary',
					'click': function click(event) {
						_addTrackingCodeFromOverview(event);
					}
				});
				break;
			case 'update_orders_status':
				buttons.push({
					'text': jse.core.lang.translate('execute', 'buttons'),
					'class': 'btn btn-primary',
					'click': function click(event) {
						$form.submit();
					}
				});
				break;
			case 'multi_cancel':
				buttons.push({
					'text': jse.core.lang.translate('send', 'buttons'),
					'class': 'btn btn-primary',
					'click': function click(event) {
						//event.preventDefault();
						//gm_cancel('gm_send_order.php', '&type=cancel', 'CANCEL');
						$form.submit();
					}
				});
				break;
		}

		return buttons;
	};

	var _addTrackingCodeFromOverview = function _addTrackingCodeFromOverview(event) {
		event.stopPropagation();

		var tracking_code = $('#parcel_service_tracking_code').val();
		if (tracking_code === '') {
			return false;
		}

		$.ajax({
			'type': 'POST',
			'url': 'request_port.php?module=ParcelServices&action=add_tracking_code',
			'timeout': 30000,
			'dataType': 'json',
			'context': this,
			'data': {

				'tracking_code': tracking_code,
				'service_id': $('#parcel_services_dropdown option:selected').val(),
				'order_id': $this.data('order_id'),
				'page_token': $('.page_token').val()
			},
			success: function success() {
				document.location.reload();
			}
		});

		return false;
	};

	var _readSelectedOrders = function _readSelectedOrders($form) {
		var orderIds = [];

		$checkboxes.filter(':checked').each(function () {
			$form.append('<input type="hidden" name="gm_multi_status[]" value="' + $(this).val() + '" />');

			orderIds.push($(this).val());
		});

		$form.find('.selected_orders').text(orderIds.join(', '));
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		switch (options.action) {
			case 'delete':
				$this.on('click', _openDeleteDialog);
				break;
			case 'multi_delete':
				$this.on('click', _openMultiDeleteDialog);
				break;
			case 'add_tracking_code':
				$this.on('click', _openTrackingCodeDialog);
				break;
			case 'update_orders_status':
				$this.on('click', _openUpdateOrdersStatusDialog);
				break;
			case 'multi_cancel':
				$this.on('click', _openMultiCancelDialog);
				break;
		}

		if (options.container === 'tracking_code_wrapper') {
			$this.on('click', '.btn-delete', _openTrackingCodeDeleteDialog);
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcnNfbW9kYWxfbGF5ZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRtb2RhbCIsIiRjaGVja2JveGVzIiwiZGVmYXVsdHMiLCJkZXRhaWxfcGFnZSIsImNvbW1lbnQiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX29wZW5EZWxldGVEaWFsb2ciLCJldmVudCIsIiRmb3JtIiwiYXR0ciIsInByZXZlbnREZWZhdWx0IiwidGl0bGUiLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsInJlcGxhY2UiLCJkaWFsb2ciLCJfZ2V0TW9kYWxCdXR0b25zIiwiX29wZW5UcmFja2luZ0NvZGVEZWxldGVEaWFsb2ciLCJkYXRhX3NldCIsImxpYnMiLCJmYWxsYmFjayIsIl9kYXRhIiwidHJhY2tpbmdfY29kZSIsInVybCIsInhociIsInBvc3QiLCJ0cmFja2luZ19jb2RlX2lkIiwib3JkZXJfaWQiLCJwYWdlX3Rva2VuIiwiZG9uZSIsInJlc3BvbnNlIiwiaHRtbCIsIl9vcGVuTXVsdGlEZWxldGVEaWFsb2ciLCJvcmRlcklkIiwiZmlsdGVyIiwibGVuZ3RoIiwiX3JlYWRTZWxlY3RlZE9yZGVycyIsIl9vcGVuTXVsdGlDYW5jZWxEaWFsb2ciLCJhcHBlbmQiLCJmaW5kIiwidGV4dCIsIl9vcGVuVXBkYXRlT3JkZXJzU3RhdHVzRGlhbG9nIiwiX29wZW5UcmFja2luZ0NvZGVEaWFsb2ciLCJidXR0b25zIiwiYWN0aW9uIiwicHVzaCIsInN1Ym1pdCIsIl9hZGRUcmFja2luZ0NvZGVGcm9tT3ZlcnZpZXciLCJzdG9wUHJvcGFnYXRpb24iLCJ2YWwiLCJhamF4Iiwic3VjY2VzcyIsImRvY3VtZW50IiwibG9jYXRpb24iLCJyZWxvYWQiLCJvcmRlcklkcyIsImVhY2giLCJqb2luIiwiaW5pdCIsIm9uIiwiY29udGFpbmVyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxvQkFERCxFQUdDLENBQUMsS0FBRCxFQUFRLFVBQVIsQ0FIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsVUFBU0QsRUFBRSx3QkFBRixDQWJWOzs7QUFlQzs7Ozs7QUFLQUUsZUFBY0YsRUFBRSxxREFBRixDQXBCZjs7O0FBc0JDOzs7OztBQUtBRyxZQUFXO0FBQ1ZDLGVBQWEsS0FESDtBQUVWQyxXQUFTO0FBRkMsRUEzQlo7OztBQWdDQzs7Ozs7QUFLQUMsV0FBVU4sRUFBRU8sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkwsSUFBN0IsQ0FyQ1g7OztBQXVDQzs7Ozs7QUFLQUQsVUFBUyxFQTVDVjs7QUE4Q0E7QUFDQTtBQUNBOztBQUVBLEtBQUlXLG9CQUFvQixTQUFwQkEsaUJBQW9CLENBQVNDLEtBQVQsRUFBZ0I7O0FBRXZDLE1BQUlDLFFBQVFWLEVBQUUsc0JBQUYsQ0FBWjtBQUNBVSxRQUFNQyxJQUFOLENBQVcsUUFBWCxFQUFxQkQsTUFBTUMsSUFBTixDQUFXLFFBQVgsSUFBdUIsT0FBdkIsR0FBaUNaLE1BQU1ELElBQU4sQ0FBVyxVQUFYLENBQXREOztBQUVBVyxRQUFNRyxjQUFOOztBQUVBLE1BQUlDLFFBQVFDLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLGdDQUF4QixFQUEwRCxRQUExRCxFQUNWQyxPQURVLENBQ0YsSUFERSxFQUNJbkIsTUFBTUQsSUFBTixDQUFXLFVBQVgsQ0FESixDQUFaOztBQUdBWSxRQUFNUyxNQUFOLENBQWE7QUFDWixZQUFTTixLQURHO0FBRVosWUFBUyxJQUZHO0FBR1osa0JBQWUsY0FISDtBQUlaLGNBQVdPLGlCQUFpQlYsS0FBakIsQ0FKQztBQUtaLFlBQVM7QUFMRyxHQUFiO0FBUUEsRUFsQkQ7O0FBb0JBLEtBQUlXLGdDQUFnQyxTQUFoQ0EsNkJBQWdDLENBQVNaLEtBQVQsRUFBZ0I7QUFDbkQsTUFBSUMsUUFBUVYsRUFBRSxvQ0FBRixDQUFaO0FBQ0EsTUFBSXNCLFdBQVdSLElBQUlTLElBQUosQ0FBU0MsUUFBVCxDQUFrQkMsS0FBbEIsQ0FBd0J6QixFQUFFLElBQUYsQ0FBeEIsRUFBaUMsb0JBQWpDLENBQWY7QUFDQVUsUUFBTVMsTUFBTixDQUFhO0FBQ1osWUFBU0wsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsbUNBQXhCLEVBQTZELGlCQUE3RCxFQUNQQyxPQURPLENBRVAsSUFGTyxFQUVESSxTQUFTSSxhQUZSLENBREc7QUFJWixZQUFTLElBSkc7QUFLWixrQkFBZSxjQUxIO0FBTVosY0FBVyxDQUNWO0FBQ0MsWUFBUVosSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsT0FBeEIsRUFBaUMsU0FBakMsQ0FEVDtBQUVDLGFBQVMsS0FGVjtBQUdDLGFBQVMsaUJBQVc7QUFDbkJqQixPQUFFLElBQUYsRUFBUW1CLE1BQVIsQ0FBZSxPQUFmO0FBQ0E7QUFMRixJQURVLEVBUVY7QUFDQyxZQUFRTCxJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixRQUF4QixFQUFrQyxTQUFsQyxDQURUO0FBRUMsYUFBUyxpQkFGVjtBQUdDLGFBQVMsaUJBQVc7QUFDbkJqQixPQUFFLElBQUYsRUFBUW1CLE1BQVIsQ0FBZSxPQUFmOztBQUVBLFNBQUlRLE1BQU0sb0VBQVY7O0FBRUFiLFNBQUlTLElBQUosQ0FBU0ssR0FBVCxDQUFhQyxJQUFiLENBQWtCO0FBQ2pCLGFBQU9GLEdBRFU7QUFFakIsY0FBUTtBQUNQLDJCQUFvQkwsU0FBU1EsZ0JBRHRCO0FBRVAsbUJBQVlSLFNBQVNTLFFBRmQ7QUFHUCxxQkFBY1QsU0FBU1U7QUFIaEI7QUFGUyxNQUFsQixFQU9HQyxJQVBILENBT1EsVUFBU0MsUUFBVCxFQUFtQjtBQUMxQmxDLFFBQUUsaURBQUYsRUFBcURtQyxJQUFyRCxDQUEwREQsU0FBU0MsSUFBbkU7QUFDQSxNQVREO0FBVUE7QUFsQkYsSUFSVSxDQU5DO0FBbUNaLFlBQVM7QUFuQ0csR0FBYjtBQXNDQSxFQXpDRDs7QUEyQ0EsS0FBSUMseUJBQXlCLFNBQXpCQSxzQkFBeUIsQ0FBUzNCLEtBQVQsRUFBZ0I7O0FBRTVDLE1BQUlDLFFBQVFWLEVBQUUsNEJBQUYsQ0FBWjtBQUFBLE1BQ0NxQyxVQUFVLENBRFg7O0FBR0E1QixRQUFNRyxjQUFOOztBQUVBLE1BQUlWLFlBQVlvQyxNQUFaLENBQW1CLFVBQW5CLEVBQStCQyxNQUEvQixLQUEwQyxDQUE5QyxFQUFpRDtBQUNoRCxVQUFPLEtBQVA7QUFDQTs7QUFFREMsc0JBQW9COUIsS0FBcEI7O0FBRUFBLFFBQU1DLElBQU4sQ0FBVyxRQUFYLEVBQXFCRCxNQUFNQyxJQUFOLENBQVcsUUFBWCxJQUF1QixPQUF2QixHQUFpQ1osTUFBTUQsSUFBTixDQUFXLFVBQVgsQ0FBdEQ7O0FBRUFZLFFBQU1TLE1BQU4sQ0FBYTtBQUNaLFlBQVNMLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLHNDQUF4QixFQUFnRSxRQUFoRSxFQUNQQyxPQURPLENBQ0MsSUFERCxFQUVQbkIsTUFBTUQsSUFBTixDQUFXLFVBQVgsQ0FGTyxDQURHO0FBSVosWUFBUyxJQUpHO0FBS1osa0JBQWUsY0FMSDtBQU1aLGNBQVdzQixpQkFBaUJWLEtBQWpCLENBTkM7QUFPWixZQUFTO0FBUEcsR0FBYjtBQVNBLEVBeEJEOztBQTBCQSxLQUFJK0IseUJBQXlCLFNBQXpCQSxzQkFBeUIsQ0FBU2hDLEtBQVQsRUFBZ0I7QUFDNUMsTUFBSUMsUUFBUVYsRUFBRSw0QkFBRixDQUFaO0FBQ0FTLFFBQU1HLGNBQU47O0FBRUEsTUFBSU4sUUFBUUYsV0FBWixFQUF5QjtBQUN4QjtBQUNBTSxTQUFNZ0MsTUFBTixDQUFhLDBEQUEwRHBDLFFBQVF5QixRQUFsRSxHQUNaLE1BREQ7QUFFQXJCLFNBQU1pQyxJQUFOLENBQVcsa0JBQVgsRUFBK0JDLElBQS9CLENBQW9DdEMsUUFBUXlCLFFBQTVDO0FBQ0FyQixTQUFNaUMsSUFBTixDQUFXLDhCQUFYLEVBQTJDUixJQUEzQyxDQUFnRDdCLFFBQVFELE9BQXhEO0FBQ0EsR0FORCxNQU9LO0FBQ0o7QUFDQSxPQUFJSCxZQUFZb0MsTUFBWixDQUFtQixVQUFuQixFQUErQkMsTUFBL0IsS0FBMEMsQ0FBOUMsRUFBaUQ7QUFDaEQsV0FBTyxLQUFQO0FBQ0E7QUFDREMsdUJBQW9COUIsS0FBcEI7QUFDQTs7QUFFREEsUUFBTUMsSUFBTixDQUFXLFFBQVgsRUFBcUJELE1BQU1DLElBQU4sQ0FBVyxRQUFYLElBQXVCLE9BQXZCLEdBQWlDWixNQUFNRCxJQUFOLENBQVcsVUFBWCxDQUF0RDs7QUFFQVksUUFBTVMsTUFBTixDQUFhO0FBQ1osWUFBU0wsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0Isc0NBQXhCLEVBQWdFLFFBQWhFLEVBQ1BDLE9BRE8sQ0FDQyxJQURELEVBRVBuQixNQUFNRCxJQUFOLENBQVcsVUFBWCxDQUZPLENBREc7QUFJWixZQUFTLElBSkc7QUFLWixrQkFBZSxjQUxIO0FBTVosY0FBV3NCLGlCQUFpQlYsS0FBakIsQ0FOQztBQU9aLFlBQVM7QUFQRyxHQUFiO0FBU0EsRUE5QkQ7O0FBZ0NBLEtBQUltQyxnQ0FBZ0MsU0FBaENBLDZCQUFnQyxDQUFTcEMsS0FBVCxFQUFnQjtBQUNuRCxNQUFJQyxRQUFRVixFQUFFLDRCQUFGLENBQVo7O0FBRUFTLFFBQU1HLGNBQU47O0FBRUEsTUFBSU4sUUFBUUYsV0FBWixFQUF5QjtBQUN4QjtBQUNBTSxTQUFNZ0MsTUFBTixDQUFhLDBEQUEwRHBDLFFBQVF5QixRQUFsRSxHQUNaLE1BREQ7QUFFQXJCLFNBQU1pQyxJQUFOLENBQVcsa0JBQVgsRUFBK0JDLElBQS9CLENBQW9DdEMsUUFBUXlCLFFBQTVDO0FBQ0FyQixTQUFNaUMsSUFBTixDQUFXLDhCQUFYLEVBQTJDUixJQUEzQyxDQUFnRDdCLFFBQVFELE9BQXhEO0FBQ0EsR0FORCxNQU9LO0FBQ0o7QUFDQSxPQUFJSCxZQUFZb0MsTUFBWixDQUFtQixVQUFuQixFQUErQkMsTUFBL0IsS0FBMEMsQ0FBOUMsRUFBaUQ7QUFDaEQsV0FBTyxLQUFQO0FBQ0E7QUFDREMsdUJBQW9COUIsS0FBcEI7QUFDQTs7QUFFREEsUUFBTVMsTUFBTixDQUFhO0FBQ1osWUFBU0wsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsbUJBQXhCLEVBQTZDLFFBQTdDLENBREc7QUFFWixZQUFTLElBRkc7QUFHWixrQkFBZSxjQUhIO0FBSVosY0FBV0csaUJBQWlCVixLQUFqQixDQUpDO0FBS1osWUFBUztBQUxHLEdBQWI7QUFPQSxFQTNCRDs7QUE2QkEsS0FBSW9DLDBCQUEwQixTQUExQkEsdUJBQTBCLENBQVNyQyxLQUFULEVBQWdCOztBQUU3QyxNQUFJQyxRQUFRVixFQUFFLHlCQUFGLENBQVo7O0FBRUFTLFFBQU1HLGNBQU47QUFDQUYsUUFBTVMsTUFBTixDQUFhO0FBQ1osWUFBU0wsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsNkJBQXhCLEVBQXVELGlCQUF2RCxFQUNQQyxPQURPLENBQ0MsSUFERCxFQUNPbkIsTUFBTUQsSUFBTixDQUFXLFVBQVgsQ0FEUCxDQURHO0FBR1osWUFBUyxJQUhHO0FBSVosa0JBQWUsY0FKSDtBQUtaLGNBQVdzQixpQkFBaUJWLEtBQWpCLENBTEM7QUFNWixZQUFTO0FBTkcsR0FBYjtBQVNBLEVBZEQ7O0FBZ0JBLEtBQUlVLG1CQUFtQixTQUFuQkEsZ0JBQW1CLENBQVNWLEtBQVQsRUFBZ0I7QUFDdEMsTUFBSXFDLFVBQVUsQ0FDYjtBQUNDLFdBQVFqQyxJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixPQUF4QixFQUFpQyxTQUFqQyxDQURUO0FBRUMsWUFBUyxLQUZWO0FBR0MsWUFBUyxpQkFBVztBQUNuQmpCLE1BQUUsSUFBRixFQUFRbUIsTUFBUixDQUFlLE9BQWY7QUFDQTtBQUxGLEdBRGEsQ0FBZDtBQVNBLFVBQVFiLFFBQVEwQyxNQUFoQjtBQUNDLFFBQUssUUFBTDtBQUNBLFFBQUssY0FBTDtBQUNDRCxZQUFRRSxJQUFSLENBQWE7QUFDWixhQUFRbkMsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsUUFBeEIsRUFBa0MsU0FBbEMsQ0FESTtBQUVaLGNBQVMsaUJBRkc7QUFHWixjQUFTLGlCQUFXO0FBQ25CUCxZQUFNd0MsTUFBTjtBQUNBO0FBTFcsS0FBYjtBQU9BO0FBQ0QsUUFBSyxtQkFBTDtBQUNDSCxZQUFRRSxJQUFSLENBQWE7QUFDWixhQUFRbkMsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsS0FBeEIsRUFBK0IsU0FBL0IsQ0FESTtBQUVaLGNBQVMsaUJBRkc7QUFHWixjQUFTLGVBQVNSLEtBQVQsRUFBZ0I7QUFDeEIwQyxtQ0FBNkIxQyxLQUE3QjtBQUNBO0FBTFcsS0FBYjtBQU9BO0FBQ0QsUUFBSyxzQkFBTDtBQUNDc0MsWUFBUUUsSUFBUixDQUFhO0FBQ1osYUFBUW5DLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLFNBQXhCLEVBQW1DLFNBQW5DLENBREk7QUFFWixjQUFTLGlCQUZHO0FBR1osY0FBUyxlQUFTUixLQUFULEVBQWdCO0FBQ3hCQyxZQUFNd0MsTUFBTjtBQUNBO0FBTFcsS0FBYjtBQU9BO0FBQ0QsUUFBSyxjQUFMO0FBQ0NILFlBQVFFLElBQVIsQ0FBYTtBQUNaLGFBQVFuQyxJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixNQUF4QixFQUFnQyxTQUFoQyxDQURJO0FBRVosY0FBUyxpQkFGRztBQUdaLGNBQVMsZUFBU1IsS0FBVCxFQUFnQjtBQUN4QjtBQUNBO0FBQ0FDLFlBQU13QyxNQUFOO0FBQ0E7QUFQVyxLQUFiO0FBU0E7QUF2Q0Y7O0FBMENBLFNBQU9ILE9BQVA7QUFDQSxFQXJERDs7QUF1REEsS0FBSUksK0JBQStCLFNBQS9CQSw0QkFBK0IsQ0FBUzFDLEtBQVQsRUFBZ0I7QUFDbERBLFFBQU0yQyxlQUFOOztBQUVBLE1BQUkxQixnQkFBZ0IxQixFQUFFLCtCQUFGLEVBQW1DcUQsR0FBbkMsRUFBcEI7QUFDQSxNQUFJM0Isa0JBQWtCLEVBQXRCLEVBQTBCO0FBQ3pCLFVBQU8sS0FBUDtBQUNBOztBQUVEMUIsSUFBRXNELElBQUYsQ0FBTztBQUNOLFdBQVEsTUFERjtBQUVOLFVBQU8saUVBRkQ7QUFHTixjQUFXLEtBSEw7QUFJTixlQUFZLE1BSk47QUFLTixjQUFXLElBTEw7QUFNTixXQUFROztBQUVQLHFCQUFpQjVCLGFBRlY7QUFHUCxrQkFBYzFCLEVBQUUsMkNBQUYsRUFBK0NxRCxHQUEvQyxFQUhQO0FBSVAsZ0JBQVl0RCxNQUFNRCxJQUFOLENBQVcsVUFBWCxDQUpMO0FBS1Asa0JBQWNFLEVBQUUsYUFBRixFQUFpQnFELEdBQWpCO0FBTFAsSUFORjtBQWFORSxZQUFTLG1CQUFXO0FBQ25CQyxhQUFTQyxRQUFULENBQWtCQyxNQUFsQjtBQUNBO0FBZkssR0FBUDs7QUFrQkEsU0FBTyxLQUFQO0FBQ0EsRUEzQkQ7O0FBNkJBLEtBQUlsQixzQkFBc0IsU0FBdEJBLG1CQUFzQixDQUFTOUIsS0FBVCxFQUFnQjtBQUN6QyxNQUFJaUQsV0FBVyxFQUFmOztBQUVBekQsY0FBWW9DLE1BQVosQ0FBbUIsVUFBbkIsRUFBK0JzQixJQUEvQixDQUFvQyxZQUFXO0FBQzlDbEQsU0FBTWdDLE1BQU4sQ0FBYSwwREFBMEQxQyxFQUFFLElBQUYsRUFBUXFELEdBQVIsRUFBMUQsR0FDWixNQUREOztBQUdBTSxZQUFTVixJQUFULENBQWNqRCxFQUFFLElBQUYsRUFBUXFELEdBQVIsRUFBZDtBQUNBLEdBTEQ7O0FBT0EzQyxRQUFNaUMsSUFBTixDQUFXLGtCQUFYLEVBQStCQyxJQUEvQixDQUFvQ2UsU0FBU0UsSUFBVCxDQUFjLElBQWQsQ0FBcEM7QUFDQSxFQVhEOztBQWFBO0FBQ0E7QUFDQTs7QUFFQWhFLFFBQU9pRSxJQUFQLEdBQWMsVUFBUzdCLElBQVQsRUFBZTtBQUM1QixVQUFRM0IsUUFBUTBDLE1BQWhCO0FBQ0MsUUFBSyxRQUFMO0FBQ0NqRCxVQUFNZ0UsRUFBTixDQUFTLE9BQVQsRUFBa0J2RCxpQkFBbEI7QUFDQTtBQUNELFFBQUssY0FBTDtBQUNDVCxVQUFNZ0UsRUFBTixDQUFTLE9BQVQsRUFBa0IzQixzQkFBbEI7QUFDQTtBQUNELFFBQUssbUJBQUw7QUFDQ3JDLFVBQU1nRSxFQUFOLENBQVMsT0FBVCxFQUFrQmpCLHVCQUFsQjtBQUNBO0FBQ0QsUUFBSyxzQkFBTDtBQUNDL0MsVUFBTWdFLEVBQU4sQ0FBUyxPQUFULEVBQWtCbEIsNkJBQWxCO0FBQ0E7QUFDRCxRQUFLLGNBQUw7QUFDQzlDLFVBQU1nRSxFQUFOLENBQVMsT0FBVCxFQUFrQnRCLHNCQUFsQjtBQUNBO0FBZkY7O0FBa0JBLE1BQUluQyxRQUFRMEQsU0FBUixLQUFzQix1QkFBMUIsRUFBbUQ7QUFDbERqRSxTQUFNZ0UsRUFBTixDQUFTLE9BQVQsRUFBa0IsYUFBbEIsRUFBaUMxQyw2QkFBakM7QUFDQTs7QUFFRFk7QUFDQSxFQXhCRDs7QUEwQkEsUUFBT3BDLE1BQVA7QUFDQSxDQXZXRiIsImZpbGUiOiJvcmRlcnMvb3JkZXJzX21vZGFsX2xheWVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBvcmRlcnNfbW9kYWxfbGF5ZXIuanMgMjAxNi0wMy0xNlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgT3JkZXJzIE1vZGFsIExheWVyIE1vZHVsZVxuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgb3BlbiBhIG1vZGFsIGxheWVyIGZvciBvcmRlciBhY3Rpb25zIGxpa2UgZGVsZXRpbmcgb3IgY2hhbmdpbmcgdGhlIG9kZXIgc3RhdHVzLlxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9vcmRlcnNfbW9kYWxfbGF5ZXJcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdvcmRlcnNfbW9kYWxfbGF5ZXInLFxuXHRcblx0Wyd4aHInLCAnZmFsbGJhY2snXSxcblx0XG5cdC8qKiAgQGxlbmRzIG1vZHVsZTpDb21wYXRpYmlsaXR5L29yZGVyc19tb2RhbF9sYXllciAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2RhbCBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRtb2RhbCA9ICQoJyNtb2RhbF9sYXllcl9jb250YWluZXInKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBDaGVja2JveGVzIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JGNoZWNrYm94ZXMgPSAkKCcuZ3gtb3JkZXJzLXRhYmxlIHRyOm5vdCguZGF0YVRhYmxlSGVhZGluZ1JvdykgaW5wdXQnKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0ZGV0YWlsX3BhZ2U6IGZhbHNlLFxuXHRcdFx0XHRjb21tZW50OiAnJ1xuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX29wZW5EZWxldGVEaWFsb2cgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0XG5cdFx0XHR2YXIgJGZvcm0gPSAkKCcjZGVsZXRlX2NvbmZpcm1fZm9ybScpO1xuXHRcdFx0JGZvcm0uYXR0cignYWN0aW9uJywgJGZvcm0uYXR0cignYWN0aW9uJykgKyAnJm9JRD0nICsgJHRoaXMuZGF0YSgnb3JkZXJfaWQnKSk7XG5cdFx0XHRcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcblx0XHRcdHZhciB0aXRsZSA9IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdURVhUX0lORk9fSEVBRElOR19ERUxFVEVfT1JERVInLCAnb3JkZXJzJylcblx0XHRcdFx0LnJlcGxhY2UoJyVzJywgJHRoaXMuZGF0YSgnb3JkZXJfaWQnKSk7XG5cdFx0XHRcblx0XHRcdCRmb3JtLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IHRpdGxlLFxuXHRcdFx0XHQnbW9kYWwnOiB0cnVlLFxuXHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0J2J1dHRvbnMnOiBfZ2V0TW9kYWxCdXR0b25zKCRmb3JtKSxcblx0XHRcdFx0J3dpZHRoJzogNDIwXG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9vcGVuVHJhY2tpbmdDb2RlRGVsZXRlRGlhbG9nID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciAkZm9ybSA9ICQoJyNkZWxldGVfdHJhY2tpbmdfY29kZV9jb25maXJtX2Zvcm0nKTtcblx0XHRcdHZhciBkYXRhX3NldCA9IGpzZS5saWJzLmZhbGxiYWNrLl9kYXRhKCQodGhpcyksICdvcmRlcnNfbW9kYWxfbGF5ZXInKTtcblx0XHRcdCRmb3JtLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdUWFRfUEFSQ0VMX1RSQUNLSU5HX0RFTEVURV9CVVRUT04nLCAncGFyY2VsX3NlcnZpY2VzJylcblx0XHRcdFx0XHQucmVwbGFjZShcblx0XHRcdFx0XHRcdCclcycsIGRhdGFfc2V0LnRyYWNraW5nX2NvZGUpLFxuXHRcdFx0XHQnbW9kYWwnOiB0cnVlLFxuXHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0J2J1dHRvbnMnOiBbXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0J2NsYXNzJzogJ2J0bicsXG5cdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdkZWxldGUnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0J2NsYXNzJzogJ2J0biBidG4tcHJpbWFyeScsXG5cdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHR2YXIgdXJsID0gJ3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPVBhcmNlbFNlcnZpY2VzJmFjdGlvbj1kZWxldGVfdHJhY2tpbmdfY29kZSc7XG5cdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRqc2UubGlicy54aHIucG9zdCh7XG5cdFx0XHRcdFx0XHRcdFx0J3VybCc6IHVybCxcblx0XHRcdFx0XHRcdFx0XHQnZGF0YSc6IHtcblx0XHRcdFx0XHRcdFx0XHRcdCd0cmFja2luZ19jb2RlX2lkJzogZGF0YV9zZXQudHJhY2tpbmdfY29kZV9pZCxcblx0XHRcdFx0XHRcdFx0XHRcdCdvcmRlcl9pZCc6IGRhdGFfc2V0Lm9yZGVyX2lkLFxuXHRcdFx0XHRcdFx0XHRcdFx0J3BhZ2VfdG9rZW4nOiBkYXRhX3NldC5wYWdlX3Rva2VuXG5cdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHR9KS5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdFx0JCgnI3RyYWNraW5nX2NvZGVfd3JhcHBlciA+IC5mcmFtZS1jb250ZW50ID4gdGFibGUnKS5odG1sKHJlc3BvbnNlLmh0bWwpO1xuXHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdF0sXG5cdFx0XHRcdCd3aWR0aCc6IDQyMFxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfb3Blbk11bHRpRGVsZXRlRGlhbG9nID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFxuXHRcdFx0dmFyICRmb3JtID0gJCgnI211bHRpX2RlbGV0ZV9jb25maXJtX2Zvcm0nKSxcblx0XHRcdFx0b3JkZXJJZCA9IDA7XG5cdFx0XHRcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcblx0XHRcdGlmICgkY2hlY2tib3hlcy5maWx0ZXIoJzpjaGVja2VkJykubGVuZ3RoID09PSAwKSB7XG5cdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0X3JlYWRTZWxlY3RlZE9yZGVycygkZm9ybSk7XG5cdFx0XHRcblx0XHRcdCRmb3JtLmF0dHIoJ2FjdGlvbicsICRmb3JtLmF0dHIoJ2FjdGlvbicpICsgJyZvSUQ9JyArICR0aGlzLmRhdGEoJ29yZGVyX2lkJykpO1xuXHRcdFx0XG5cdFx0XHQkZm9ybS5kaWFsb2coe1xuXHRcdFx0XHQndGl0bGUnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVEVYVF9JTkZPX0hFQURJTkdfTVVMVElfREVMRVRFX09SREVSJywgJ29yZGVycycpXG5cdFx0XHRcdFx0LnJlcGxhY2UoJyVzJyxcblx0XHRcdFx0XHRcdCR0aGlzLmRhdGEoJ29yZGVyX2lkJykpLFxuXHRcdFx0XHQnbW9kYWwnOiB0cnVlLFxuXHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0J2J1dHRvbnMnOiBfZ2V0TW9kYWxCdXR0b25zKCRmb3JtKSxcblx0XHRcdFx0J3dpZHRoJzogNDIwXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfb3Blbk11bHRpQ2FuY2VsRGlhbG9nID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciAkZm9ybSA9ICQoJyNtdWx0aV9jYW5jZWxfY29uZmlybV9mb3JtJyk7XG5cdFx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XG5cdFx0XHRpZiAob3B0aW9ucy5kZXRhaWxfcGFnZSkge1xuXHRcdFx0XHQvLyBPcmRlcnMgZGV0YWlsIHBhZ2Vcblx0XHRcdFx0JGZvcm0uYXBwZW5kKCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCJnbV9tdWx0aV9zdGF0dXNbXVwiIHZhbHVlPVwiJyArIG9wdGlvbnMub3JkZXJfaWQgK1xuXHRcdFx0XHRcdCdcIiAvPicpO1xuXHRcdFx0XHQkZm9ybS5maW5kKCcuc2VsZWN0ZWRfb3JkZXJzJykudGV4dChvcHRpb25zLm9yZGVyX2lkKTtcblx0XHRcdFx0JGZvcm0uZmluZCgndGV4dGFyZWFbbmFtZT1cImdtX2NvbW1lbnRzXCJdJykuaHRtbChvcHRpb25zLmNvbW1lbnQpO1xuXHRcdFx0fVxuXHRcdFx0ZWxzZSB7XG5cdFx0XHRcdC8vIE9yZGVycyBwYWdlXG5cdFx0XHRcdGlmICgkY2hlY2tib3hlcy5maWx0ZXIoJzpjaGVja2VkJykubGVuZ3RoID09PSAwKSB7XG5cdFx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0XHR9XG5cdFx0XHRcdF9yZWFkU2VsZWN0ZWRPcmRlcnMoJGZvcm0pO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkZm9ybS5hdHRyKCdhY3Rpb24nLCAkZm9ybS5hdHRyKCdhY3Rpb24nKSArICc/b0lEPScgKyAkdGhpcy5kYXRhKCdvcmRlcl9pZCcpKTtcblx0XHRcdFxuXHRcdFx0JGZvcm0uZGlhbG9nKHtcblx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RFWFRfSU5GT19IRUFESU5HX01VTFRJX0NBTkNFTF9PUkRFUicsICdvcmRlcnMnKVxuXHRcdFx0XHRcdC5yZXBsYWNlKCclcycsXG5cdFx0XHRcdFx0XHQkdGhpcy5kYXRhKCdvcmRlcl9pZCcpKSxcblx0XHRcdFx0J21vZGFsJzogdHJ1ZSxcblx0XHRcdFx0J2RpYWxvZ0NsYXNzJzogJ2d4LWNvbnRhaW5lcicsXG5cdFx0XHRcdCdidXR0b25zJzogX2dldE1vZGFsQnV0dG9ucygkZm9ybSksXG5cdFx0XHRcdCd3aWR0aCc6IDQyMFxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX29wZW5VcGRhdGVPcmRlcnNTdGF0dXNEaWFsb2cgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyICRmb3JtID0gJCgnI3VwZGF0ZV9vcmRlcnNfc3RhdHVzX2Zvcm0nKTtcblx0XHRcdFxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFxuXHRcdFx0aWYgKG9wdGlvbnMuZGV0YWlsX3BhZ2UpIHtcblx0XHRcdFx0Ly8gT3JkZXJzIGRldGFpbCBwYWdlXG5cdFx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiZ21fbXVsdGlfc3RhdHVzW11cIiB2YWx1ZT1cIicgKyBvcHRpb25zLm9yZGVyX2lkICtcblx0XHRcdFx0XHQnXCIgLz4nKTtcblx0XHRcdFx0JGZvcm0uZmluZCgnLnNlbGVjdGVkX29yZGVycycpLnRleHQob3B0aW9ucy5vcmRlcl9pZCk7XG5cdFx0XHRcdCRmb3JtLmZpbmQoJ3RleHRhcmVhW25hbWU9XCJnbV9jb21tZW50c1wiXScpLmh0bWwob3B0aW9ucy5jb21tZW50KTtcblx0XHRcdH1cblx0XHRcdGVsc2Uge1xuXHRcdFx0XHQvLyBPcmRlcnMgcGFnZVxuXHRcdFx0XHRpZiAoJGNoZWNrYm94ZXMuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aCA9PT0gMCkge1xuXHRcdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdFx0fVxuXHRcdFx0XHRfcmVhZFNlbGVjdGVkT3JkZXJzKCRmb3JtKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JGZvcm0uZGlhbG9nKHtcblx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0hFQURJTkdfR01fU1RBVFVTJywgJ29yZGVycycpLFxuXHRcdFx0XHQnbW9kYWwnOiB0cnVlLFxuXHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0J2J1dHRvbnMnOiBfZ2V0TW9kYWxCdXR0b25zKCRmb3JtKSxcblx0XHRcdFx0J3dpZHRoJzogNTgwXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfb3BlblRyYWNraW5nQ29kZURpYWxvZyA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcblx0XHRcdHZhciAkZm9ybSA9ICQoJyNhZGRfdHJhY2tpbmdfY29kZV9mb3JtJyk7XG5cdFx0XHRcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHQkZm9ybS5kaWFsb2coe1xuXHRcdFx0XHQndGl0bGUnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVFhUX1BBUkNFTF9UUkFDS0lOR19IRUFESU5HJywgJ3BhcmNlbF9zZXJ2aWNlcycpXG5cdFx0XHRcdFx0LnJlcGxhY2UoJyVzJywgJHRoaXMuZGF0YSgnb3JkZXJfaWQnKSksXG5cdFx0XHRcdCdtb2RhbCc6IHRydWUsXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0XHQnYnV0dG9ucyc6IF9nZXRNb2RhbEJ1dHRvbnMoJGZvcm0pLFxuXHRcdFx0XHQnd2lkdGgnOiA0MjBcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2dldE1vZGFsQnV0dG9ucyA9IGZ1bmN0aW9uKCRmb3JtKSB7XG5cdFx0XHR2YXIgYnV0dG9ucyA9IFtcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Nsb3NlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0XTtcblx0XHRcdHN3aXRjaCAob3B0aW9ucy5hY3Rpb24pIHtcblx0XHRcdFx0Y2FzZSAnZGVsZXRlJzpcblx0XHRcdFx0Y2FzZSAnbXVsdGlfZGVsZXRlJzpcblx0XHRcdFx0XHRidXR0b25zLnB1c2goe1xuXHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZGVsZXRlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHRcdCdjbGFzcyc6ICdidG4gYnRuLXByaW1hcnknLFxuXHRcdFx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdhZGRfdHJhY2tpbmdfY29kZSc6XG5cdFx0XHRcdFx0YnV0dG9ucy5wdXNoKHtcblx0XHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2FkZCcsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdFx0XHRcdF9hZGRUcmFja2luZ0NvZGVGcm9tT3ZlcnZpZXcoZXZlbnQpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICd1cGRhdGVfb3JkZXJzX3N0YXR1cyc6XG5cdFx0XHRcdFx0YnV0dG9ucy5wdXNoKHtcblx0XHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2V4ZWN1dGUnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0J2NsYXNzJzogJ2J0biBidG4tcHJpbWFyeScsXG5cdFx0XHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0XHRcdFx0XHQkZm9ybS5zdWJtaXQoKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnbXVsdGlfY2FuY2VsJzpcblx0XHRcdFx0XHRidXR0b25zLnB1c2goe1xuXHRcdFx0XHRcdFx0J3RleHQnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc2VuZCcsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdFx0XHRcdC8vZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0XHRcdFx0Ly9nbV9jYW5jZWwoJ2dtX3NlbmRfb3JkZXIucGhwJywgJyZ0eXBlPWNhbmNlbCcsICdDQU5DRUwnKTtcblx0XHRcdFx0XHRcdFx0JGZvcm0uc3VibWl0KCk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHJldHVybiBidXR0b25zO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9hZGRUcmFja2luZ0NvZGVGcm9tT3ZlcnZpZXcgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcblx0XHRcdHZhciB0cmFja2luZ19jb2RlID0gJCgnI3BhcmNlbF9zZXJ2aWNlX3RyYWNraW5nX2NvZGUnKS52YWwoKTtcblx0XHRcdGlmICh0cmFja2luZ19jb2RlID09PSAnJykge1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdCd0eXBlJzogJ1BPU1QnLFxuXHRcdFx0XHQndXJsJzogJ3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPVBhcmNlbFNlcnZpY2VzJmFjdGlvbj1hZGRfdHJhY2tpbmdfY29kZScsXG5cdFx0XHRcdCd0aW1lb3V0JzogMzAwMDAsXG5cdFx0XHRcdCdkYXRhVHlwZSc6ICdqc29uJyxcblx0XHRcdFx0J2NvbnRleHQnOiB0aGlzLFxuXHRcdFx0XHQnZGF0YSc6IHtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQndHJhY2tpbmdfY29kZSc6IHRyYWNraW5nX2NvZGUsXG5cdFx0XHRcdFx0J3NlcnZpY2VfaWQnOiAkKCcjcGFyY2VsX3NlcnZpY2VzX2Ryb3Bkb3duIG9wdGlvbjpzZWxlY3RlZCcpLnZhbCgpLFxuXHRcdFx0XHRcdCdvcmRlcl9pZCc6ICR0aGlzLmRhdGEoJ29yZGVyX2lkJyksXG5cdFx0XHRcdFx0J3BhZ2VfdG9rZW4nOiAkKCcucGFnZV90b2tlbicpLnZhbCgpXG5cdFx0XHRcdH0sXG5cdFx0XHRcdHN1Y2Nlc3M6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdGRvY3VtZW50LmxvY2F0aW9uLnJlbG9hZCgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9yZWFkU2VsZWN0ZWRPcmRlcnMgPSBmdW5jdGlvbigkZm9ybSkge1xuXHRcdFx0dmFyIG9yZGVySWRzID0gW107XG5cdFx0XHRcblx0XHRcdCRjaGVja2JveGVzLmZpbHRlcignOmNoZWNrZWQnKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkZm9ybS5hcHBlbmQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cImdtX211bHRpX3N0YXR1c1tdXCIgdmFsdWU9XCInICsgJCh0aGlzKS52YWwoKSArXG5cdFx0XHRcdFx0J1wiIC8+Jyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRvcmRlcklkcy5wdXNoKCQodGhpcykudmFsKCkpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCRmb3JtLmZpbmQoJy5zZWxlY3RlZF9vcmRlcnMnKS50ZXh0KG9yZGVySWRzLmpvaW4oJywgJykpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdHN3aXRjaCAob3B0aW9ucy5hY3Rpb24pIHtcblx0XHRcdFx0Y2FzZSAnZGVsZXRlJzpcblx0XHRcdFx0XHQkdGhpcy5vbignY2xpY2snLCBfb3BlbkRlbGV0ZURpYWxvZyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ211bHRpX2RlbGV0ZSc6XG5cdFx0XHRcdFx0JHRoaXMub24oJ2NsaWNrJywgX29wZW5NdWx0aURlbGV0ZURpYWxvZyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ2FkZF90cmFja2luZ19jb2RlJzpcblx0XHRcdFx0XHQkdGhpcy5vbignY2xpY2snLCBfb3BlblRyYWNraW5nQ29kZURpYWxvZyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ3VwZGF0ZV9vcmRlcnNfc3RhdHVzJzpcblx0XHRcdFx0XHQkdGhpcy5vbignY2xpY2snLCBfb3BlblVwZGF0ZU9yZGVyc1N0YXR1c0RpYWxvZyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ211bHRpX2NhbmNlbCc6XG5cdFx0XHRcdFx0JHRoaXMub24oJ2NsaWNrJywgX29wZW5NdWx0aUNhbmNlbERpYWxvZyk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLmNvbnRhaW5lciA9PT0gJ3RyYWNraW5nX2NvZGVfd3JhcHBlcicpIHtcblx0XHRcdFx0JHRoaXMub24oJ2NsaWNrJywgJy5idG4tZGVsZXRlJywgX29wZW5UcmFja2luZ0NvZGVEZWxldGVEaWFsb2cpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
