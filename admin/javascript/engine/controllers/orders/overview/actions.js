/* --------------------------------------------------------------
 actions.js 2016-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Main Table Actions
 *
 * This module creates the bulk and row actions for the table.
 */
gx.controllers.module(
	'actions',
	
	['user_configuration_service', `${gx.source}/libs/button_dropdown`],
	
	function() {
		
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
		 * Create Bulk Actions
		 *
		 * This callback can be called once during the initialization of this module.
		 */
		function _createBulkActions() {
			// Add actions to the bulk-action dropdown.
			const $bulkActions = $('.bulk-action');
			const defaultBulkAction = $this.data('defaultBulkAction') || 'change-status';
			
			jse.libs.button_dropdown.bindDefaultAction($bulkActions, jse.core.registry.get('userId'),
				'ordersOverviewBulkAction', jse.libs.user_configuration_service);
			
			// Change status
			jse.libs.button_dropdown.addAction($bulkActions, {
				text: jse.core.lang.translate('BUTTON_MULTI_CHANGE_ORDER_STATUS', 'orders'),
				class: 'change-status',
				data: {configurationValue: 'change-status'},
				isDefault: defaultBulkAction === 'change-status',
				callback: e => e.preventDefault()
			});
			
			// Delete
			jse.libs.button_dropdown.addAction($bulkActions, {
				text: jse.core.lang.translate('BUTTON_MULTI_DELETE', 'orders'),
				class: 'delete',
				data: {configurationValue: 'delete'},
				isDefault: defaultBulkAction === 'delete',
				callback: e => e.preventDefault()
			});
			
			// Cancel
			jse.libs.button_dropdown.addAction($bulkActions, {
				text: jse.core.lang.translate('BUTTON_MULTI_CANCEL', 'orders'),
				class: 'cancel',
				data: {configurationValue: 'cancel'},
				isDefault: defaultBulkAction === 'cancel',
				callback: e => e.preventDefault()
			});
			
			// Send order confirmation.
			jse.libs.button_dropdown.addAction($bulkActions, {
				text: jse.core.lang.translate('BUTTON_MULTI_SEND_ORDER', 'orders'),
				class: 'bulk-email-order',
				data: {configurationValue: 'bulk-email-order'},
				isDefault: defaultBulkAction === 'bulk-email-order',
				callback: e => e.preventDefault()
			});
			
			// Send invoice.
			jse.libs.button_dropdown.addAction($bulkActions, {
				text: jse.core.lang.translate('BUTTON_MULTI_SEND_INVOICE', 'orders'),
				class: 'bulk-email-invoice',
				data: {configurationValue: 'bulk-email-invoice'},
				isDefault: defaultBulkAction === 'bulk-email-invoice',
				callback: e => e.preventDefault()
			});
			
			// Download invoices.
			jse.libs.button_dropdown.addAction($bulkActions, {
				text: jse.core.lang.translate('TITLE_INVOICE', 'orders'),
				class: 'bulk-download-invoice',
				data: {configurationValue: 'bulk-download-invoice'},
				isDefault: defaultBulkAction === 'bulk-download-invoice',
				callback: e => e.preventDefault()
			});
			
			// Download packing slips.
			jse.libs.button_dropdown.addAction($bulkActions, {
				text: jse.core.lang.translate('TITLE_PACKINGSLIP', 'orders'),
				class: 'bulk-download-packing-slip',
				data: {configurationValue: 'bulk-download-packing-slip'},
				isDefault: defaultBulkAction === 'bulk-download-packing-slip',
				callback: e => e.preventDefault()
			});
			
			$this.datatable_default_actions('ensure', 'bulk');
		}
		
		/**
		 * Create Table Row Actions
		 *
		 * This function must be call with every table draw.dt event.
		 */
		function _createRowActions() {
			// Re-create the checkbox widgets and the row actions. 
			const defaultRowAction = $this.data('defaultRowAction') || 'edit';
			
			jse.libs.button_dropdown.bindDefaultAction($this.find('.btn-group.dropdown'), 
				jse.core.registry.get('userId'), 'ordersOverviewRowAction', jse.libs.user_configuration_service);
			
			$this.find('.btn-group.dropdown').each(function() {
				const orderId = $(this).parents('tr').data('id');
				
				// Edit
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TEXT_SHOW', 'orders'),
					href: `orders.php?oID=${orderId}&action=edit`,
					class: 'edit',
					data: {configurationValue: 'edit'},
					isDefault: defaultRowAction === 'edit',
					callback: e => e.preventDefault()
				});
				
				// Change Status
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TEXT_GM_STATUS', 'orders'),
					class: 'change-status',
					data: {configurationValue: 'change-status'},
					isDefault: defaultRowAction === 'change-status',
					callback: e => e.preventDefault()
				});
				
				// Delete
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('BUTTON_MULTI_DELETE', 'orders'),
					class: 'delete',
					data: {configurationValue: 'delete'},
					isDefault: defaultRowAction === 'delete',
					callback: e => e.preventDefault()
				});
				
				// Cancel
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('BUTTON_GM_CANCEL', 'orders'),
					class: 'cancel',
					data: {configurationValue: 'cancel'},
					isDefault: defaultRowAction === 'cancel',
					callback: e => e.preventDefault()
				});
				
				// Invoice
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TITLE_INVOICE', 'orders'),
					href: `gm_pdf_order.php?oID=${orderId}&type=invoice`,
					target: '_blank',
					class: 'invoice',
					data: {configurationValue: 'invoice'},
					isDefault: defaultRowAction === 'invoice',
					callback: e => e.preventDefault()
				});
				
				// Email Invoice
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TITLE_INVOICE_MAIL', 'orders'),
					class: 'email-invoice',
					data: {configurationValue: 'email-invoice'},
					isDefault: defaultRowAction === 'email-invoice',
					callback: e => e.preventDefault()
				});
				
				// Packing Slip
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TITLE_PACKINGSLIP', 'orders'),
					href: `gm_pdf_order.php?oID=${orderId}&type=packingslip`,
					target: '_blank',
					class: 'packing-slip',
					data: {configurationValue: 'packing-slip'},
					isDefault: defaultRowAction === 'packing-slip'
				});
				
				// Show Order Acceptance
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TITLE_ORDER', 'orders'),
					href: `gm_send_order.php?oID=${orderId}&type=order`,
					target: '_blank',
					class: 'show-acceptance',
					data: {configurationValue: 'show-acceptance'},
					isDefault: defaultRowAction === 'show-acceptance'
				});
				
				// Recreate Order Acceptance
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TITLE_RECREATE_ORDER', 'orders'),
					href: `gm_send_order.php?oID=${orderId}&type=recreate_order`,
					target: '_blank',
					class: 'recreate-order-acceptance',
					data: {configurationValue: 'recreate-order-acceptance'},
					isDefault: defaultRowAction === 'recreate-order-acceptance'
				});
				
				// Email Order
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TITLE_SEND_ORDER', 'orders'),
					class: 'email-order',
					data: {configurationValue: 'email-order'},
					isDefault: defaultRowAction === 'email-order',
					callback: e => e.preventDefault()
				});
				
				// Create Withdrawal
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TEXT_CREATE_WITHDRAWAL', 'orders'),
					href: `../withdrawal.php?order_id=${orderId}`,
					target: '_blank',
					class: 'create-withdrawal',
					data: {configurationValue: 'create-withdrawal'},
					isDefault: defaultRowAction === 'create-withdrawal'
				});
				
				// Add Tracking Code
				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('TXT_PARCEL_TRACKING_SENDBUTTON_TITLE', 'parcel_services'),
					class: 'add-tracking-number',
					data: {configurationValue: 'add-tracking-number'},
					isDefault: defaultRowAction === 'add-tracking-number',
					callback: e => e.preventDefault()
				});
				
				$this.datatable_default_actions('ensure', 'row');
			});
		}
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$(window).on('JSENGINE_INIT_FINISHED', () => {
				$this.on('draw.dt', _createRowActions);
				_createRowActions();
				_createBulkActions();
			});
			
			done();
		};
		
		return module;
		
	}); 