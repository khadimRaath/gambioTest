/* --------------------------------------------------------------
 add_tracking_number.js 2016-09-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Add Tracking Number Modal Controller
 *
 * Handles the functionality of the "Add Tracking Number" modal.
 */
gx.controllers.module('add_tracking_number', ['modal', `${gx.source}/libs/info_box`], function(data) {
	
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
	const module = {};
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Stores the tracking number for a specific order.
	 *
	 * @param {jQuery.Event} event
	 */
	function _onStoreTrackingNumberClick(event) {
		event.preventDefault();
		
		const orderId = $this.data('orderId');
		const parcelServiceId = $('#delivery-service').find('option:selected').val();
		const trackingNumber = $('input:text[name="tracking-number"]').val();
		
		// Make an AJAX call to store the tracking number if one was provided.
		if (trackingNumber.length) {
			$.ajax({
				url: './admin.php?do=OrdersModalsAjax/StoreTrackingNumber',
				data: {
					orderId,
					trackingNumber,
					parcelServiceId,
					pageToken: jse.core.config.get('pageToken')
				},
				method: 'POST',
				dataType: 'JSON'
			})
				.done(function(response) {
					$this.modal('hide');
					jse.libs.info_box.service.addSuccessMessage(
						jse.core.lang.translate('ADD_TRACKING_NUMBER_SUCCESS', 'admin_orders'));
					$('.table-main').DataTable().ajax.reload();
				})
				.fail(function(jqXHR, textStatus, errorThrown) {
					jse.libs.modal.message({
						title: jse.core.lang.translate('error', 'messages'),
						content: jse.core.lang.translate('ADD_TRACKING_NUMBER_ERROR', 'admin_orders')
					});
					jse.core.debug.error('Store Tracking Number Error', jqXHR, textStatus, errorThrown);
				});
		} else {
			// Show an error message
			const $modalFooter = $this.find('.modal-footer');
			const errorMessage = jse.core.lang.translate('TXT_SAVE_ERROR', 'admin_general');
			
			// Remove error message
			$modalFooter.find('span').remove();
			$modalFooter.prepend(`<span class="text-danger">${errorMessage}</span>`);
		}
	}
	
	/**
	 * On Add Tracking Number Modal Hidden
	 *
	 * Reset the tracking number modal.
	 */
	function _onAddTrackingNumberModalHidden() {
		$(this).find('#tracking-number').val('');
		$(this).find('.modal-footer span').remove();
	}
	
	
	/**
	 * On Add Tracking Number Modal Show
	 *
	 * Handles the event for storing a a tracking number from the tracking number modal.
	 *
	 * @param {jQuery.Event} event
	 */
	function _onAddTrackingNumberModalShow(event) {
		event.stopPropagation();
		// Element which invoked the tracking number modal.
		$(this).data('orderId', $(event.relatedTarget).data('orderId'));
	}
	
	/**
	 * Checks if the enter key was pressed and delegates to
	 * the tracking number store method.
	 *
	 * @param {jQuery.Event} event
	 */
	function _saveOnPressedEnterKey(event) {
		const keyCode = event.keyCode ? event.keyCode : event.which;
		
		if (keyCode === 13) {
			_onStoreTrackingNumberClick(event);
		}
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this
			.on('show.bs.modal', _onAddTrackingNumberModalShow)
			.on('hidden.bs.modal', _onAddTrackingNumberModalHidden)
			.on('click', '#store-tracking-number', _onStoreTrackingNumberClick)
			.on('keypress', _saveOnPressedEnterKey);
		
		done();
	};
	
	return module;
}); 
