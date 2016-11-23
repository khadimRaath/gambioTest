/* --------------------------------------------------------------
 withdrawals_main_controller.js 2015-09-16 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Withdrawals Controller
 *
 * @module Compatibility/withdrawals_main_controller
 */
gx.controllers.module(
	// Module name
	'withdrawals_main_controller',
	
	// Module Dependencies
	[
		gx.source + '/libs/info_messages'
	],
	
	/**  @lends module:Compatibility/withdrawals_main_controller */
	
	function() {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		// Element reference
		var $this = $(this);
		
		// Libraries reference
		var messages = jse.libs.info_messages;
		
		// Meta object
		var module = {};
		
		// ------------------------------------------------------------------------
		// ELEMENTS DEFINITION
		// ------------------------------------------------------------------------
		
		// Save Order Button
		var $saveOrderIdButton = $this.find('.js-save-order-id'),
			$orderIdInput = $this.find('input[name="withdrawal_order_id"]'),
			$withdrawalIdText = $('#withdrawal_id'),
			$pageTokenInput = $this.find('input[name="page_token"]');
		
		// ------------------------------------------------------------------------
		// METHODS
		// ------------------------------------------------------------------------
		
		// Save Order ID
		var _saveOrderId = function() {
			
			$saveOrderIdButton.animate({
				opacity: 0.2
			}, 250);
			
			var url = [
				'request_port.php?',
				'module=Withdrawal&',
				'action=save_withdrawal_order_id'
			].join('');
			
			var request = $.ajax({
				url: url,
				type: 'POST',
				dataType: 'json',
				data: {
					withdrawal_id: $withdrawalIdText.text(),
					order_id: $orderIdInput.val(),
					page_token: $pageTokenInput.val()
				}
			});
			
			request.done(function(response) {
				$saveOrderIdButton.animate({
					opacity: 1
				}, 250);
				
				if (response.status === 'success') {
					messages.addSuccess(
						jse.core.lang.translate('TXT_SAVE_SUCCESS', 'admin_general')
					);
				} else {
					messages.addError(
						jse.core.lang.translate('TXT_SAVE_ERROR', 'admin_general')
					);
				}
			});
			
			request.fail(function() {
				$saveOrderIdButton.animate({
					opacity: 1
				}, 250);
				
				messages.addError(
					jse.core.lang.translate('TXT_SAVE_ERROR', 'admin_general')
				);
			});
		};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _onClick = function(event) {
			// Save Order Button
			if ($saveOrderIdButton.is(event.target)) {
				_saveOrderId();
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this.on('click', _onClick);
			done();
		};
		
		return module;
		
	});
