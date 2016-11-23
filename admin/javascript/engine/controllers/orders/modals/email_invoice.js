/* --------------------------------------------------------------
 email_invoice.js 2016-05-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Email Invoice Modal Controller
 *
 * Handles the functionality of the Email Invoice modal.
 */
gx.controllers.module('email_invoice', ['modal'], function(data) {
	
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
			type: 'invoice',
			mail: '1',
			gm_quick_mail: '1'
		};
		const url = jse.core.config.get('appUrl') + '/admin/gm_pdf_order.php?' + $.param(getParams);
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
				
				$('.orders .table-main').DataTable().ajax.reload();
				$('.orders .table-main').orders_overview_filter('reload');
				
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