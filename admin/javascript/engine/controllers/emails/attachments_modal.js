/* --------------------------------------------------------------
 emails_modal.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 ----------------------------------------------------------------
 */

/**
 * ## Attachments Modal Controller
 *
 * This controller will handle the attachments modal dialog operations of the admin/emails page.
 *
 * @module Controllers/attachments_modal
 */
gx.controllers.module(
	'attachments_modal',
	
	[
		'modal',
		gx.source + '/libs/emails'
	],
	
	/** @lends module:Controllers/attachments_modal */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Reference
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Emails Main Table Selector
			 *
			 * @type {object}
			 */
			$table = $('#emails-table'),
			
			/**
			 * Default Module Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * Final Module Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * Delete old attachments request.
		 *
		 * @param {object} event Contains the event information.
		 */
		var _onDeleteOldAttachments = function(event) {
			// Validate selected date before making the request.
			if ($this.find('#removal-date').val() === '') {
				return; // do not proceed
			}
			
			// Display confirmation modal before proceeding.
			var modalOptions = {
				title: jse.core.lang.translate('delete', 'buttons') + ' - '
				+ Date.parse($this.find('#removal-date').datepicker('getDate')).toString('dd.MM.yyyy'),
				content: jse.core.lang.translate('prompt_delete_old_attachments', 'emails'),
				buttons: [
					{
						text: jse.core.lang.translate('no', 'lightbox_buttons'),
						click: function() {
							$(this).dialog('close');
						}
					},
					{
						text: jse.core.lang.translate('yes', 'lightbox_buttons'),
						click: function() {
							jse.libs.emails.deleteOldAttachments($('#removal-date')
								.datepicker('getDate'))
								.done(function(response) {
									var size = (response.size.megabytes !== 0)
										? response.size.megabytes + ' Megabytes'
										: response.size.bytes + ' Bytes';
									
									var message =
										jse.core.lang.translate('message_delete_old_attachments_success', 'emails')
										+ '<br/>' + jse.core.lang.translate('count', 'admin_labels') + ': '
										+ response.count + ', ' + jse.core.lang.translate('size', 'db_backup') + ': '
										+ size;
									
									jse.libs.modal.message({
										title: 'Info',
										content: message
									});
									
									jse.libs.emails.getAttachmentsSize($('#attachments-size'));
									$table.DataTable().ajax.reload();
									$this.dialog('close');
								})
								.fail(function(response) {
									var title = jse.core.lang.translate('error', 'messages');
									
									jse.libs.modal.message({
										title: title,
										content: response.message
									});
								});
							
							$(this).dialog('close');
						}
					}
				]
			};
			
			jse.libs.modal.message(modalOptions);
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			$this.on('click', '#delete-old-attachments', _onDeleteOldAttachments);
			done();
		};
		
		return module;
	});
