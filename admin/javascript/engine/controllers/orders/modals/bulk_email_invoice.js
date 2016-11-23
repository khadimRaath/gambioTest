/* --------------------------------------------------------------
 bulk_email_invoice.js 2016-06-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Bulk Email Invoice Modal Controller
 */
gx.controllers.module('bulk_email_invoice', ['modal', 'loading_spinner'], function(data) {
	
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
	 * Key for placeholder map values that need to access to the response data returned from subject data request.
	 *
	 * @type {string}
	 */
	const placeholderValueKey = 'requestKey';
	
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
	 * Placeholder Map
	 *
	 * Used to replace the placeholder with the respective variables.
	 *
	 * @type {Object}
	 */
	const placeholderMap = {
		'{INVOICE_NUM}': {[placeholderValueKey]: 'invoiceId'},
		'{INVOICE_DATE}': {[placeholderValueKey]: 'date'},
		'{ORDER_ID}': 'id'
	};
	
	/**
	 * Request URL
	 * 
	 * @type {String}
	 */
	const requestUrl = jse.core.config.get('appUrl') + '/admin/gm_pdf_order.php';
	
	/**
	 * Request URL for retrieving subject data.
	 *
	 * @type {String}
	 */
	const subjectDataRequestUrl = jse.core.config.get('appUrl') + '/admin/admin.php';
	
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
	 * @param {Object} subjectData Subject data.
	 *
	 * @return {String}
	 */
	function _getParsedSubject(orderData, subjectData) {
		// Subject.
		let subject = module.bindings.subject.get();
		
		// Placeholder iterator function.
		const placeholderIterator = placeholder => {
			// Value from placeholder map.
			const placeholderMapValue = placeholderMap[placeholder];
			
			// Get data from response of subject data request?
			const doesNeedAccessToSubjectData = typeof placeholderMap[placeholder] !== 'string';
			
			// Replaced value.
			const replacedValue = doesNeedAccessToSubjectData ?
			                      subjectData[placeholderMapValue[placeholderValueKey]] :
			                      orderData[placeholderMapValue];
			
			subject = subject.replace(placeholder, replacedValue);
		};
		
		// Iterate over the placeholders and replace the values.
		Object
			.keys(placeholderMap)
			.forEach(placeholderIterator);
		
		return subject;
	}
	
	/**
	 * Handles the successful delivery of all messages.
	 */
	function _handleDeliverySuccess() {
		const message = jse.core.lang.translate('BULK_MAIL_SUCCESS', 'gm_send_order');
		
		// Show success message in the admin info box.
		jse.libs.info_box.service.addSuccessMessage(message);
		
		$('.orders .table-main').DataTable().ajax.reload();
		$('.orders .table-main').orders_overview_filter('reload');
		
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
		
		// Hide modal and the loading spinner and re-enable the send button.
		_toggleSpinner(false);
		$this.modal('hide');
	}
	
	/**
	 * Send the modal data to the form through an AJAX call.
	 */
	function _onSendClick() {
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
			const orderData = $(element).data('invoice');
			
			// Email address entered in input field.
			const enteredEmail = $(element).find(emailListItemEmailSelector).val();
			
			// Promise wrapper for subject data AJAX request.
			const getSubjectPromise = new Promise((resolve, reject) => {
				// Request options.
				const options = {
					url: subjectDataRequestUrl,
					method: 'GET',
					data: {
						do: 'OrdersModalsAjax/GetEmailInvoiceSubjectData',
						id: orderData.id,
						date: orderData.purchaseDate.date,
						pageToken: jse.core.config.get('pageToken')
					},
					dataType: 'json'
				};
				
				// Create AJAX request.
				const request = $.ajax(options);
				
				// Resolve promise on success.
				request.done(response => resolve(response));
				
				// Reject promise on fail.
				request.fail(() => reject());
			});
			
			// Promise wrapper for AJAX requests.
			const promise = new Promise((resolve, reject) => {
				// Get subject data.
				getSubjectPromise
					.then(subjectData => {
						// Request GET parameters to send.
						const getParameters = {
							oID: orderData.id,
							type: 'invoice',
							mail: '1',
							gm_quick_mail: '1'
						};
						
						// Composed request URL.
						const url = requestUrl + '?' + $.param(getParameters);
						
						// Data to send.
						const data = {
							gm_mail: enteredEmail,
							gm_subject: _getParsedSubject(orderData, subjectData)
						};
						
						// Create AJAX request.
						const request = $.ajax({method: 'POST', url, data});
						
						// Resolve promise on success.
						request.done(response => resolve(response));
						
						// Reject promise on fail.
						request.fail(() => reject());
					})
					.catch(reject);
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