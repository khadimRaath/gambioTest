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
gx.compatibility.module(
	'orders_modal_layer',
	
	['xhr', 'fallback'],
	
	/**  @lends module:Compatibility/orders_modal_layer */
	
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
		
		var _openDeleteDialog = function(event) {
			
			var $form = $('#delete_confirm_form');
			$form.attr('action', $form.attr('action') + '&oID=' + $this.data('order_id'));
			
			event.preventDefault();
			
			var title = jse.core.lang.translate('TEXT_INFO_HEADING_DELETE_ORDER', 'orders')
				.replace('%s', $this.data('order_id'));
			
			$form.dialog({
				'title': title,
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': _getModalButtons($form),
				'width': 420
			});
			
		};
		
		var _openTrackingCodeDeleteDialog = function(event) {
			var $form = $('#delete_tracking_code_confirm_form');
			var data_set = jse.libs.fallback._data($(this), 'orders_modal_layer');
			$form.dialog({
				'title': jse.core.lang.translate('TXT_PARCEL_TRACKING_DELETE_BUTTON', 'parcel_services')
					.replace(
						'%s', data_set.tracking_code),
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
						'class': 'btn btn-primary',
						'click': function() {
							$(this).dialog('close');
							
							var url = 'request_port.php?module=ParcelServices&action=delete_tracking_code';
							
							jse.libs.xhr.post({
								'url': url,
								'data': {
									'tracking_code_id': data_set.tracking_code_id,
									'order_id': data_set.order_id,
									'page_token': data_set.page_token
								}
							}).done(function(response) {
								$('#tracking_code_wrapper > .frame-content > table').html(response.html);
							});
						}
					}
				],
				'width': 420
			});
			
		};
		
		var _openMultiDeleteDialog = function(event) {
			
			var $form = $('#multi_delete_confirm_form'),
				orderId = 0;
			
			event.preventDefault();
			
			if ($checkboxes.filter(':checked').length === 0) {
				return false;
			}
			
			_readSelectedOrders($form);
			
			$form.attr('action', $form.attr('action') + '&oID=' + $this.data('order_id'));
			
			$form.dialog({
				'title': jse.core.lang.translate('TEXT_INFO_HEADING_MULTI_DELETE_ORDER', 'orders')
					.replace('%s',
						$this.data('order_id')),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': _getModalButtons($form),
				'width': 420
			});
		};
		
		var _openMultiCancelDialog = function(event) {
			var $form = $('#multi_cancel_confirm_form');
			event.preventDefault();
			
			if (options.detail_page) {
				// Orders detail page
				$form.append('<input type="hidden" name="gm_multi_status[]" value="' + options.order_id +
					'" />');
				$form.find('.selected_orders').text(options.order_id);
				$form.find('textarea[name="gm_comments"]').html(options.comment);
			}
			else {
				// Orders page
				if ($checkboxes.filter(':checked').length === 0) {
					return false;
				}
				_readSelectedOrders($form);
			}
			
			$form.attr('action', $form.attr('action') + '?oID=' + $this.data('order_id'));
			
			$form.dialog({
				'title': jse.core.lang.translate('TEXT_INFO_HEADING_MULTI_CANCEL_ORDER', 'orders')
					.replace('%s',
						$this.data('order_id')),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': _getModalButtons($form),
				'width': 420
			});
		};
		
		var _openUpdateOrdersStatusDialog = function(event) {
			var $form = $('#update_orders_status_form');
			
			event.preventDefault();
			
			if (options.detail_page) {
				// Orders detail page
				$form.append('<input type="hidden" name="gm_multi_status[]" value="' + options.order_id +
					'" />');
				$form.find('.selected_orders').text(options.order_id);
				$form.find('textarea[name="gm_comments"]').html(options.comment);
			}
			else {
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
		
		var _openTrackingCodeDialog = function(event) {
			
			var $form = $('#add_tracking_code_form');
			
			event.preventDefault();
			$form.dialog({
				'title': jse.core.lang.translate('TXT_PARCEL_TRACKING_HEADING', 'parcel_services')
					.replace('%s', $this.data('order_id')),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': _getModalButtons($form),
				'width': 420
			});
			
		};
		
		var _getModalButtons = function($form) {
			var buttons = [
				{
					'text': jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function() {
						$(this).dialog('close');
					}
				}
			];
			switch (options.action) {
				case 'delete':
				case 'multi_delete':
					buttons.push({
						'text': jse.core.lang.translate('delete', 'buttons'),
						'class': 'btn btn-primary',
						'click': function() {
							$form.submit();
						}
					});
					break;
				case 'add_tracking_code':
					buttons.push({
						'text': jse.core.lang.translate('add', 'buttons'),
						'class': 'btn btn-primary',
						'click': function(event) {
							_addTrackingCodeFromOverview(event);
						}
					});
					break;
				case 'update_orders_status':
					buttons.push({
						'text': jse.core.lang.translate('execute', 'buttons'),
						'class': 'btn btn-primary',
						'click': function(event) {
							$form.submit();
						}
					});
					break;
				case 'multi_cancel':
					buttons.push({
						'text': jse.core.lang.translate('send', 'buttons'),
						'class': 'btn btn-primary',
						'click': function(event) {
							//event.preventDefault();
							//gm_cancel('gm_send_order.php', '&type=cancel', 'CANCEL');
							$form.submit();
						}
					});
					break;
			}
			
			return buttons;
		};
		
		var _addTrackingCodeFromOverview = function(event) {
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
				success: function() {
					document.location.reload();
				}
			});
			
			return false;
		};
		
		var _readSelectedOrders = function($form) {
			var orderIds = [];
			
			$checkboxes.filter(':checked').each(function() {
				$form.append('<input type="hidden" name="gm_multi_status[]" value="' + $(this).val() +
					'" />');
				
				orderIds.push($(this).val());
			});
			
			$form.find('.selected_orders').text(orderIds.join(', '));
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
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
