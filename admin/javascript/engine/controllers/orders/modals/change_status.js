/* --------------------------------------------------------------
 change_status.js 2016-05-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Change Order Status Modal Controller
 */
gx.controllers.module('change_status', ['modal'], function(data) {
	
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
			status: $this.find('#status-dropdown'),
			notifyCustomer: $this.find('#notify-customer'),
			sendParcelTrackingCode: $this.find('#send-parcel-tracking-code'),
			sendComment: $this.find('#send-comment'),
			comment: $this.find('#comment')
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
	function _changeStatus(event) {
		event.stopPropagation();
		
		if (module.bindings.status.get() === '') {
			return;
		}
		
		const url = jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersModalsAjax/ChangeOrderStatus';
		const data = {
				selectedOrders: module.bindings.selectedOrders.get().split(', '),
				statusId: module.bindings.status.get(),
				notifyCustomer: module.bindings.notifyCustomer.get(),
				sendParcelTrackingCode: module.bindings.sendParcelTrackingCode.get(),
				sendComment: module.bindings.sendComment.get(),
				comment: module.bindings.comment.get(),
				pageToken: jse.core.config.get('pageToken')
			};
		const $saveButton = $(event.target);
		
		$saveButton.addClass('disabled').attr('disabled', true);
		
		$.ajax({
			url,
			data,
			method: 'POST'
		})
			.done(function(response) {
				const content = data.notifyCustomer ?
				                jse.core.lang.translate('MAIL_SUCCESS', 'gm_send_order') :
				                jse.core.lang.translate('SUCCESS_ORDER_UPDATED', 'orders');
				
				$('.orders .table-main').DataTable().ajax.reload(null, false);
				$('.orders .table-main').orders_overview_filter('reload'); 
				
				// Show success message in the admin info box.
				jse.libs.info_box.service.addSuccessMessage(content);
			})
			.always(function() {
				$this.modal('hide');
				$saveButton.removeClass('disabled').attr('disabled', false);
			});
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.on('click', '.btn.save', _changeStatus);
		done();
	};
	
	return module;
});