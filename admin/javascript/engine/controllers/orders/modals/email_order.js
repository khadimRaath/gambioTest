/* --------------------------------------------------------------
 email_order.js 2016-05-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Email Order Modal Controller
 */
gx.controllers.module('email_order', ['modal'], function(data) {
	
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
			subject: $this.find('.subject'),
			emailAddress: $this.find('.email-address')
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
		const getParams = {
			oID: $this.data('orderId'),
			type: 'send_order'
		};
		const url = jse.core.config.get('appUrl') + '/admin/gm_send_order.php?' + $.param(getParams);
		const data = {
			gm_mail: module.bindings.emailAddress.get(),
			gm_subject: module.bindings.subject.get()
		};
		const $sendButton = $(event.target);
		
		$sendButton.addClass('disabled').prop('disabled', true);
		
		$.ajax({
			url,
			data,
			method: 'POST'
		})
			.done(function(response) {
				const message = jse.core.lang.translate('MAIL_SUCCESS', 'gm_send_order');
				const $tableRow = $(`tbody tr#${getParams.oID}`);
				
				// Remove the e-mail symbol
				$tableRow.find('td.actions i.tooltip-confirmation-not-sent').remove();
				
				// Show success message in the admin info box.
				jse.libs.info_box.service.addSuccessMessage(message);
			})
			.fail(function(jqxhr, textStatus, errorThrown) {
				const title = jse.core.lang.translate('error', 'messages');
				const content = jse.core.lang.translate('MAIL_UNSUCCESS', 'gm_send_order');
				
				// Show error message in a modal.
				jse.libs.modal.message({title, content});
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