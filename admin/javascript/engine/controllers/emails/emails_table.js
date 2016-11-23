/* --------------------------------------------------------------
 emails_table.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Emails Table Controller
 *
 * This controller will handle the main table operations of the admin/emails page.
 *
 * @module Controllers/emails_table
 */
gx.controllers.module(
	'emails_table',
	
	[
		gx.source + '/libs/emails',
		'modal',
		'datatable',
		'normalize'
	],
	
	/** @lends module:Controllers/emails_table */
	
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
			 * Toolbar Selector
			 *
			 * @type {object}
			 */
			$toolbar = $('#emails-toolbar'),
			
			/**
			 * Modal Selector
			 *
			 * @type {object}
			 */
			$modal = $('#emails-modal'),
			
			/**
			 * Default Module Options
			 *
			 * @type {object}
			 */
			defaults = {
				emailsTableActions: function() {
					return '<div class="row-actions">' + '<span class="send-email action-item" title="'
						+ jse.core.lang.translate(
							'send',
							'buttons') + '">' + '<i class="fa fa-envelope-o"></i>' + '</span>' +
						'<span class="forward-email action-item" title="'
						+ jse.core.lang.translate('forward', 'buttons') +
						'">' +
						'<i class="fa fa-share"></i>' + '</span>' +
						'<span class="delete-email action-item" title="' + jse.core.lang.translate(
							'delete', 'buttons') + '">' + '<i class="fa fa-trash-o"></i>' + '</span>' +
						'<span class="preview-email action-item" title="'
						+ jse.core.lang.translate('preview', 'buttons') +
						'">' +
						'<i class="fa fa-eye"></i>' + '</span>' + '</div>';
				},
				
				convertPendingToString: function(data, type, row, meta) {
					return (data
					=== true) ? jse.core.lang.translate('email_pending', 'emails') : jse.core.lang.translate(
						'email_sent', 'emails');
				}
			},
			
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
		 * Toggle row selection for main page table.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onSelectAllRows = function(event) {
			if ($(this).prop('checked')) {
				$this.find('tbody .single-checkbox').addClass('checked');
				$this.find('tbody input:checkbox').prop('checked', true);
			} else {
				$this.find('tbody .single-checkbox').removeClass('checked');
				$this.find('tbody input:checkbox').prop('checked', false);
			}
		};
		
		/**
		 * Will send the email to its contacts (even if its status is "sent").
		 *
		 * @param {object} event Contains event information.
		 */
		var _onSendEmail = function(event) {
			var $row = $(this).parents('tr');
			
			jse.libs.modal.message({
				title: jse.core.lang.translate('send', 'buttons'),
				content: jse.core.lang.translate('prompt_send_email', 'emails'),
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
							var email = $row.data();
							jse.libs.emails.sendCollection([email])
								.done(function(response) {
									$this.DataTable().ajax.reload();
									jse.libs.emails.getAttachmentsSize($('#attachments-size'));
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
			});
		};
		
		/**
		 * Display modal with email information but without contacts.
		 *
		 * The user will be able to set new contacts and send the email (kind of "duplicate" method).
		 *
		 * @param {object} event Contains event information.
		 */
		var _onForwardEmail = function(event) {
			var email = $(this).parents('tr').data();
			
			jse.libs.emails.resetModal($modal);
			jse.libs.emails.loadEmailOnModal(email, $modal);
			
			// Clear contact fields but let the rest of the data untouched.
			$modal.find('#email-id').val('');
			$modal.find('#sender-email, #sender-name').val('');
			$modal.find('#reply-to-email, #reply-to-name').val('');
			$modal.find('#recipient-email, #recipient-name').val('');
			$modal.find('#contacts-table').DataTable().clear().draw();
			
			$modal.dialog({
				title: jse.core.lang.translate('forward', 'buttons'),
				width: 1000,
				height: 740,
				modal: true,
				dialogClass: 'gx-container',
				closeOnEscape: false,
				buttons: jse.libs.emails.getDefaultModalButtons($modal, $this),
				open: jse.libs.emails.colorizeButtonsForEditMode
			});
		};
		
		/**
		 * Delete selected row email.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onDeleteEmail = function(event) {
			var $row = $(this).parents('tr'),
				email = $row.data();
			
			jse.libs.modal.message({
				title: jse.core.lang.translate('delete', 'buttons'),
				content: jse.core.lang.translate('prompt_delete_email', 'emails'),
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
							jse.libs.emails.deleteCollection([email])
								.done(function(response) {
									$this.DataTable().ajax.reload();
									jse.libs.emails.getAttachmentsSize($('#attachments-size'));
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
			});
		};
		
		/**
		 * Display modal with email information
		 *
		 * The user can select an action to perform upon the previewed email (Send, Forward,
		 * Delete, Close).
		 *
		 * @param  {object} event Contains event information.
		 */
		var _onPreviewEmail = function(event) {
			var email = $(this).parents('tr').data();
			
			jse.libs.emails.resetModal($modal);
			jse.libs.emails.loadEmailOnModal(email, $modal);
			
			$modal.dialog({
				title: jse.core.lang.translate('preview', 'buttons'),
				width: 1000,
				height: 740,
				modal: false,
				dialogClass: 'gx-container',
				closeOnEscape: false,
				buttons: jse.libs.emails.getPreviewModalButtons($modal, $this),
				open: jse.libs.emails.colorizeButtonsForPreviewMode
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 *
		 * The emails table operates with server processing because it is much faster and efficient than preparing
		 * and sending all the records in every AJAX request. Check the Emails/DataTable controller method for
		 * requested data and the following link for more info about server processing in DataTables.
		 *
		 * {@link http://www.datatables.net/manual/server-side}
		 */
		module.init = function(done) {
			// Create a DataTable instance for the email records.
			jse.libs.datatable.create($this, {
				processing: false,
				serverSide: true,
				dom: 'rtip',
				autoWidth: false,
				language: (jse.core.config.get('languageCode')
				=== 'de') ? jse.libs.datatable.getGermanTranslation() : null,
				ajax: {
					url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/DataTable',
					type: 'POST'
				},
				order: [[2, 'desc']],
				pageLength: 20,
				columns: [
					{
						data: null,
						orderable: false,
						defaultContent: '<input type="checkbox" data-single_checkbox />',
						width: '2%',
						className: 'dt-head-center dt-body-center'
					},
					{
						data: 'row_count',
						orderable: false,
						width: '3%',
						className: 'dt-head-center dt-body-center'
					},
					{
						data: 'creation_date',
						width: '12%'
					},
					{
						data: 'sent_date',
						width: '12%'
					},
					{
						data: 'sender',
						width: '12%'
					},
					{
						data: 'recipient',
						width: '12%'
					},
					{
						data: 'subject',
						width: '27%'
					},
					{
						data: 'is_pending',
						width: '8%',
						render: options.convertPendingToString
					},
					{
						data: null,
						orderable: false,
						defaultContent: '',
						render: options.emailsTableActions,
						width: '12%'
					}
				]
			});
			
			// Add table error handler.
			jse.libs.datatable.error($this, function(event, settings, techNote, message) {
				jse.libs.modal.message({
					title: 'DataTables ' + jse.core.lang.translate('error', 'messages'),
					content: message
				});
			});
			
			// Add ajax error handler.
			jse.libs.datatable.ajaxComplete($this, function(event, settings, json) {
				if (json.exception === true) {
					jse.core.debug.error('DataTables Processing Error', $this.get(0), json);
					jse.libs.modal.message({
						title: 'AJAX ' + jse.core.lang.translate('error', 'messages'),
						content: json.message
					});
				}
			});
			
			// Combine ".paginator" with the DataTable HTML output in order to create a unique pagination
			// frame at the bottom of the table (executed after table initialization).
			$this.on('init.dt', function(e, settings, json) {
				$('.paginator').appendTo($('#emails-table_wrapper'));
				$('#emails-table_info')
					.appendTo($('.paginator .datatable-components'))
					.css('clear', 'none');
				$('#emails-table_paginate')
					.appendTo($('.paginator .datatable-components'))
					.css('clear', 'none');
			});
			
			// Recreate the checkbox widgets.
			$this.on('draw.dt', function() {
				$this.find('tbody').attr('data-gx-widget', 'checkbox');
				gx.widgets.init($this); // Initialize the checkbox widget.
			});
			
			// Add spinner to table loading actions.
			var $spinner;
			$this.on('preXhr.dt', function(e, settings, json) {
				$spinner = jse.libs.loading_spinner.show($this);
			});
			$this.on('xhr.dt', function(e, settings, json) {
				if ($spinner) {
					jse.libs.loading_spinner.hide($spinner);
				}
			});
			
			// Bind event handlers of the emails table.
			$this
				.on('click', '#select-all-rows', _onSelectAllRows)
				.on('click', '.send-email', _onSendEmail)
				.on('click', '.forward-email', _onForwardEmail)
				.on('click', '.delete-email', _onDeleteEmail)
				.on('click', '.preview-email', _onPreviewEmail);
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
