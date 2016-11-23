/* --------------------------------------------------------------
 emails_paginator.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Emails Paginator Controller
 *
 * This controller will handle the main table paginator operations of the admin/emails page.
 *
 * @module Controllers/emails_paginator
 */
gx.controllers.module(
	'emails_paginator',
	
	[
		gx.source + '/libs/emails',
		gx.source + '/libs/button_dropdown',
		'loading_spinner',
		'modal'
	],
	
	/** @lends module:Controllers/emails_paginator */
	
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
			 * Table Selector
			 *
			 * @type {object}
			 */
			$table = $('#emails-table'),
			
			/**
			 * Attachments Size Selector
			 *
			 * @type {object}
			 */
			$attachmentsSize = $('#attachments-size'),
			
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
		 * Refresh page data.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onRefreshData = function(event) {
			$table.DataTable().ajax.reload();
			jse.libs.emails.getAttachmentsSize($attachmentsSize);
		};
		
		/**
		 * Change current page length.
		 *
		 * @param {object} event Contains the event data.
		 */
		var _onTableLengthChange = function(event) {
			var length = $this.find('#display-records').val();
			$table.DataTable().page.len(length).draw();
		};
		
		/**
		 * Open handle attachments modal window.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onHandleAttachments = function(event) {
			var $attachmentsModal = $('#attachments-modal');
			
			// Reset modal state.
			$attachmentsModal.find('#removal-date').val('').datepicker({
				maxDate: new Date()
			});
			$(document).find('.ui-datepicker').not('.gx-container').addClass('gx-container');
			
			// Display modal to the user.
			$attachmentsModal.dialog({
				title: jse.core.lang.translate('handle_attachments', 'emails'),
				width: 400,
				modal: true,
				dialogClass: 'gx-container',
				closeOnEscape: false
			});
		};
		
		/**
		 * Execute the delete operation for the selected email records.
		 *
		 * @param {object} event Contains the event information.
		 */
		var _onBulkDelete = function(event) {
			// Check if there are table rows selected.
			if ($table.find('tr td input:checked').length === 0 || $('#bulk-action').val() === '') {
				return; // No selected records, exit method.
			}
			
			// Get selected rows data - create a new email collection.
			var collection = jse.libs.emails.getSelectedEmails($table);
			
			// Display confirmation modal to the user.
			jse.libs.modal.message({
				title: jse.core.lang.translate('bulk_action', 'admin_labels'),
				content: jse.core.lang.translate('prompt_delete_collection', 'emails'),
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
							jse.libs.emails.deleteCollection(collection)
								.done(function(response) {
									$table.DataTable().ajax.reload();
									jse.libs.emails.getAttachmentsSize($attachmentsSize);
								})
								.fail(function(response) {
									var title = jse.core.lang.translate('error', 'messages');
									
									jse.libs.modal.message({
										title: title,
										content: response.message
									});
								});
							
							$(this).dialog('close');
							$table.find('input[type=checkbox]').prop('checked', false);
						}
					}
				]
			});
		};
		
		/**
		 * Execute the send operation for the selected email records.
		 *
		 * @param {object} event Contains the event information.
		 */
		var _onBulkSend = function(event) {
			// Check if there are table rows selected.
			if ($table.find('tr td input:checked').length === 0 || $('#bulk-action').val() === '') {
				return; // No selected records, exit method.
			}
			
			// Get selected rows data - create a new email collection.
			var collection = jse.libs.emails.getSelectedEmails($table);
			
			// Display confirmation modal to the user.
			jse.libs.modal.message({
				title: jse.core.lang.translate('bulk_action', 'admin_labels'),
				content: jse.core.lang.translate('prompt_send_collection', 'emails'),
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
							jse.libs.emails.sendCollection(collection)
								.done(function(response) {
									$table.DataTable().ajax.reload();
									jse.libs.emails.getAttachmentsSize($attachmentsSize);
								})
								.fail(function(response) {
									var title = jse.core.lang.translate('error', 'messages');
									
									jse.libs.modal.message({
										title: title,
										content: response.message
									});
								});
							
							$(this).dialog('close');
							$table.find('input[type=checkbox]').prop('checked', false);
						}
					}
				]
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			// Bind paginator event handlers.
			$this
				.on('click', '#refresh-table', _onRefreshData)
				.on('click', '#handle-attachments', _onHandleAttachments)
				.on('change', '#display-records', _onTableLengthChange);
			
			var $dropdown = $this.find('.bulk-action');
			jse.libs.button_dropdown.mapAction($dropdown, 'bulk_send_selected', 'emails', _onBulkSend);
			jse.libs.button_dropdown.mapAction($dropdown, 'bulk_delete_selected', 'emails', _onBulkDelete);
			
			// Get current attachments size.
			jse.libs.emails.getAttachmentsSize($attachmentsSize);
			
			done();
		};
		
		// Return module object to module engine.
		return module;
	});
