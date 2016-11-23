/* --------------------------------------------------------------
 cancel_modal.js 2016-05-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Cancel Order Modal Controller
 */
gx.controllers.module('cancel', ['modal'], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {
		bindings: {
			selectedOrders: $this.find('.selected-orders'),
			reStock: $this.find('.re-stock'),
			reShip: $this.find('.re-ship'),
			reActivate: $this.find('.re-activate'),
			notifyCustomer: $this.find('.notify-customer'),
			sendComments: $this.find('.send-comments'),
			cancellationComments: $this.find('.cancellation-comments')
		}
	};
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Send the modal data to the form through an AJAX call.
	 *
	 * @param {jQuery.Event} event
	 */
	function _onSendClick(event) {
		const url = jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersModalsAjax/CancelOrder';
		const data = {
			selectedOrders: module.bindings.selectedOrders.get().split(', '),
			reStock: module.bindings.reStock.get(),
			reShip: module.bindings.reShip.get(),
			reActivate: module.bindings.reActivate.get(),
			notifyCustomer: module.bindings.notifyCustomer.get(),
			sendComments: module.bindings.sendComments.get(),
			cancellationComments: module.bindings.cancellationComments.get(),
			pageToken: jse.core.config.get('pageToken')
		};
		const $sendButton = $(event.target);
		
		$sendButton.addClass('disabled').prop('disabled', true);
		
		$.ajax({
			url,
			data,
			method: 'POST',
			dataType: 'json'
		})
			.done(function(response) {
				jse.libs.info_box.service.addSuccessMessage(
					jse.core.lang.translate('CANCEL_ORDERS_SUCCESS', 'admin_orders'));
				$('.orders .table-main').DataTable().ajax.reload();
				$('.orders .table-main').orders_overview_filter('reload');
			})
			.fail(function(jqxhr, textStatus, errorThrown) {
				jse.libs.modal.message({
					title: jse.core.lang.translate('error', 'messages'),
					content: jse.core.lang.translate('CANCEL_ORDERS_ERROR', 'admin_orders')
				});
			})
			.always(function() {
				$this.modal('hide');
				$sendButton.removeClass('disabled').prop('disabled', false);
			});
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.on('click', '.btn.send', _onSendClick);
		done();
	};
	
	return module;
});