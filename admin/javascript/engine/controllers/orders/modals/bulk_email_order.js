/* --------------------------------------------------------------
 bulk_email_order.js 2016-05-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Bulk Email Order Modal Controller
 */
gx.controllers.module('bulk_email_order', ['modal', 'loading_spinner'], function(data) {
	
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
		bindings: {subject: $this.find('.subject')}
	};
	
	/**
	 * Selector for the email list item.
	 * 
	 * @type {String}
	 */
	const emailListItemSelector = '.email-list-item';
	
	/**
	 * Selector for the email list item ID.
	 * 
	 * @type {String}
	 */
	const emailListItemEmailSelector = '.email-input';
	
	/**
	 * Selector for the modal content body layer.
	 * 
	 * @type {String}
	 */
	const modalContentSelector = '.modal-content';
	
	/**
	 * Placeholder map.
	 * Used to replace the placeholder with the respective variables.
	 * 
	 * Format: '{Placeholder}' : 'Attribute'
	 * 
	 * @type {Object}
	 */
	const placeholderMap = {
		'{ORDER_ID}': 'id',
		'{ORDER_DATE}': 'purchaseDate'
	};
	
	/**
	 * Loading spinner instance.
	 * 
	 * @type {jQuery|null}
	 */
	let $spinner = null;
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Show/hide loading spinner.
	 * 
	 * @param {Boolean} doShow Show the loading spinner?
	 */
	function _toggleSpinner(doShow) {
		if (doShow) {
			$spinner = jse.libs.loading_spinner.show($this.find(modalContentSelector), $this.css('z-index'));
		} else {
			jse.libs.loading_spinner.hide($spinner);
		}
	}
	
	/**
	 * Parse subject and replace the placeholders with the variables.
	 * 
	 * @param {Object} orderData Order data.
	 * 
	 * @return {String}
	 */
	function _getParsedSubject(orderData) {
		// Subject.
		let subject = module.bindings.subject.get();
		
		// Iterate over the placeholders and replace the values.
		Object
			.keys(placeholderMap)
			.forEach(placeholder => subject = subject.replace(placeholder, orderData[placeholderMap[placeholder]]));
		
		return subject;
	}
	
	/**
	 * Handles the successful delivery of all messages.
	 */
	function _handleDeliverySuccess() {
		const message = jse.core.lang.translate('BULK_MAIL_SUCCESS', 'gm_send_order');
		
		// Show success message in the admin info box.
		jse.libs.info_box.service.addSuccessMessage(message);
		
		// Hide modal and loading spinner.
		_toggleSpinner(false);
		$this.modal('hide');
	}
	
	/**
	 * Handles the failure of the message delivery.
	 */
	function _handleDeliveryFail() {
		const title = jse.core.lang.translate('error', 'messages');
		const content = jse.core.lang.translate('BULK_MAIL_UNSUCCESS', 'gm_send_order');
		
		// Show error message in a modal.
		jse.libs.modal.message({title, content});
		
		// Hide modal and the loading spinner and reenable the send button.
		_toggleSpinner(false);
		$this.modal('hide');
	}
	
	/**
	 * Send the modal data to the form through an AJAX call.
	 */
	function _onSendClick() {
		// Send type.
		const REQUEST_SEND_TYPE = 'send_order';
		
		// Request base URL.
		const REQUEST_URL = jse.core.config.get('appUrl') + '/admin/gm_send_order.php';
		
		// Collection of requests in promise format.
		const promises = [];
		
		// Email list item elements.
		const $emailListItems = $this.find(emailListItemSelector);
		
		// Abort and hide modal on empty email list entries.
		if (!$emailListItems.length) {
			$this.modal('hide');
			return;
		}
		
		// Show loading spinner.
		_toggleSpinner(true);
		
		// Fill orders array with data.
		$emailListItems.each((index, element) => {
			// Order data.
			const orderData = $(element).data('order');
			
			// Format the purchase date.
			const dateFormat = jse.core.config.get('languageCode') === 'de' ? 'DD.MM.YY' : 'MM.DD.YY';
			orderData.purchaseDate = moment(orderData.purchaseDate.date).format(dateFormat);
			
			// Email address entered in input field.
			const enteredEmail = $(element).find(emailListItemEmailSelector).val();
			
			// Request GET parameters to send.
			const getParameters = {
				oID: orderData.id,
				type: REQUEST_SEND_TYPE
			};
			
			// Composed request URL.
			const url = REQUEST_URL + '?' + $.param(getParameters);
			
			// Data to send.
			const data = {
				gm_mail: enteredEmail,
				gm_subject: _getParsedSubject(orderData)
			};
			
			// Promise wrapper for AJAX response.
			const promise = new Promise((resolve, reject) => {
				// Create AJAX request.
				const request = $.ajax({method: 'POST', url, data});
				
				request.success(() => {
					const orderId = getParameters.oID;
					const $tableRow = $(`tbody tr#${orderId}`);
					
					// Remove the e-mail symbol
					$tableRow.find('td.actions i.tooltip-confirmation-not-sent').remove();
				});
				
				// Resolve promise on success.
				request.done(response => resolve(response));
				
				// Reject promise on fail.
				request.fail(() => reject());
			});
			
			// Add promise to array.
			promises.push(promise);
		});
		
		// Wait for all promise to respond and handle success/error.
		Promise.all(promises)
			.then(_handleDeliverySuccess)
			.catch(_handleDeliveryFail);
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