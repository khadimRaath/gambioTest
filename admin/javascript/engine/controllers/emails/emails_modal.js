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
 * ## Emails Modal Controller
 *
 * This controller will handle the modal dialog operations of the admin/emails page.
 *
 * @module Controllers/emails_modal
 */
gx.controllers.module(
	'emails_modal',
	
	[
		gx.source + '/libs/emails',
		'modal',
		'datatable',
		'normalize'
	],
	
	/** @lends module:Controllers/emails_modal */
	
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
			 * Toolbar Selector
			 *
			 * @type {object}
			 */
			$toolbar = $('#emails-toolbar'),
			
			/**
			 * Contacts Table Selector
			 *
			 * @type {object}
			 */
			$contactsTable = $this.find('#contacts-table'),
			
			/**
			 * Attachments Table Selector
			 *
			 * @type {object}
			 */
			$attachmentsTable = $this.find('#attachments-table'),
			
			/**
			 * Default Module Options
			 *
			 * @type {object}
			 */
			defaults = {
				contactsTableActions: function(data, type, row, meta) {
					return '<div class="row-actions">' + '<span class="delete-contact action-item" title="' +
						jse.core.lang.translate(
							'delete', 'buttons') + '">' + '<i class="fa fa-trash-o"></i>' + '</span>' + '</div>';
				},
				
				attachmentsTableActions: function(data, type, row, meta) {
					// Check if attachment file exists in the server and thus can be downloaded.
					var disabled, title;
					
					if (data.file_exists) {
						disabled = '';
						title = jse.core.lang.translate('download', 'buttons');
					} else {
						disabled = 'disabled="disabled"';
						title = jse.core.lang.translate('message_download_attachment_error', 'emails');
					}
					
					// Generate table actions html for table row.
					return '<div class="row-actions">' + '<span class="delete-attachment action-item" title="' +
						jse.core.lang.translate(
							'delete', 'buttons') + '">' + '<i class="fa fa-trash-o"></i>' + '</span>' +
						'<span class="download-attachment action-item" title="' + title + '" ' + disabled + '>' +
						'<i class="fa fa-download"></i>' + '</span>' + '</div>';
				},
				
				convertUpperCase: function(data, type, row, meta) {
					return data.toUpperCase();
				},
				
				lengthMenu: [[5, 10], [5, 10]]
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
		 * Add a contact with the provided data into the contacts table.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onAddContact = function(event) {
			// Validate Contact Form
			$this.find('.tab-content.bcc-cc').trigger('validator.validate'); // Trigger form validation
			if ($this.find('.tab-content.bcc-cc .error').length > 0) {
				return;
			}
			
			// Add contact to table.
			var contact = {
				name: jse.libs.normalize.escapeHtml($this.find('#contact-name').val()),
				email: jse.libs.normalize.escapeHtml($this.find('#contact-email').val()),
				type: jse.libs.normalize.escapeHtml($this.find('#contact-type').val())
			};
			$this.find('#contacts-table').DataTable().row.add(contact).draw();
			$this.find('#contact-name, #contact-email, #contact-type').removeClass('valid error');
			$this.find('#contact-name, #contact-email').val('');
			$this.find('#contact-type option:first').prop('selected', true);
			jse.libs.emails.updateTabCounters($this);
		};
		
		/**
		 * Remove contact from contacts table.
		 *
		 * @param {object} event contains event information.
		 */
		var _onDeleteContact = function(event) {
			var row = $(this).parents('tr');
			$this.find('#contacts-table').DataTable().row(row).remove().draw();
			jse.libs.emails.updateTabCounters($this);
		};
		
		/**
		 * Called after the attachment is uploaded
		 *
		 * @param {object} event Contains event information.
		 */
		var _onUploadAttachment = function(event, response) {
			if (response.exception) {
				jse.libs.modal.message({
					title: jse.core.lang.translate('error', 'messages'),
					content: jse.core.lang.translate('message_upload_attachment_error', 'emails')
					+ response.message
				});
				return;
			}
			
			$this.find('#attachments-table').DataTable().row.add({
				path: jse.libs.normalize.escapeHtml(response.path),
				file_exists: true
			}).draw();
			
			$this.find('#upload-attachment').val('');
			jse.libs.emails.updateTabCounters($this);
		};
		
		/**
		 * Remove selected attachment from email and from server.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onDeleteAttachment = function(event) {
			var row = $(this).parents('tr').get(0),
				url = jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/DeleteAttachment',
				data = {
					pageToken: jse.core.config.get('pageToken'),
					attachments: [$attachmentsTable.DataTable().row(row).data().path]
				};
			
			$.post(url, data, function(response) {
				jse.core.debug.info('AJAX File Remove Response', response);
				
				if (response.exception) {
					jse.libs.modal.message({
						title: jse.core.lang.translate('error', 'messages'),
						content: jse.core.lang.translate('message_remove_attachment_error')
						+ response.message
					});
					return;
				}
				
				$this.find('#attachments-table').DataTable().row(row).remove().draw();
				jse.libs.emails.updateTabCounters($this);
			}, 'json');
		};
		
		/**
		 * Download selected attachment.
		 *
		 * A new window tab will be opened and the file download will start immediately. If
		 * there are any errors from the PHP code they will be displayed in the new tab and
		 * they will not affect the current page.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onDownloadAttachment = function(event) {
			if ($(this).attr('disabled') === 'disabled') {
				return;
			}
			var row = $(this).parents('tr').get(0),
				path = $attachmentsTable.DataTable().row(row).data().path,
				url = jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/DownloadAttachment&path=' + path;
			window.open(url, '_blank');
		};
		
		/**
		 * Callback to the validation of the first tab of the modal.
		 *
		 * Make the tab headline link red so that the user can see that there is an error
		 * inside the elements of this tab.
		 *
		 * @param {object} event Contains event information.
		 */
		var _onEmailDetailsValidation = function(event) {
			// Paint the parent tab so that the user knows that there is a problem in the form.
			if ($this.find('.tab-content.details .error').length > 0) {
				$this.find('.tab-headline.details').css('color', 'red');
			} else {
				$this.find('.tab-headline.details').css('color', '');
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			// Contacts DataTable
			jse.libs.datatable.create($contactsTable, {
				autoWidth: false,
				order: [
					[0, 'asc'] // Email ASC
				],
				language: ( jse.core.config.get('languageCode') === 'de')
					? jse.libs.datatable.getGermanTranslation()
					: null,
				lengthMenu: options.lengthMenu,
				pageLength: 5,
				columns: [
					{
						data: 'email',
						width: '45%'
					},
					{
						data: 'name',
						width: '35%'
					},
					{
						data: 'type',
						render: options.convertUpperCase,
						width: '10%'
					},
					{
						data: null,
						orderable: false,
						defaultContent: '',
						render: options.contactsTableActions,
						width: '10%',
						className: 'dt-head-center dt-body-center'
					}
				]
			});
			
			// Attachments DataTable
			jse.libs.datatable.create($attachmentsTable, {
				autoWidth: false,
				order: [
					[0, 'asc'] // Path ASC
				],
				language: ( jse.core.config.get('languageCode') === 'de')
					? jse.libs.datatable.getGermanTranslation()
					: null,
				lengthMenu: options.lengthMenu,
				pageLength: 5,
				columns: [
					{
						data: 'path',
						width: '90%'
					},
					{
						data: null,
						orderable: false,
						defaultContent: '',
						render: options.attachmentsTableActions,
						width: '10%',
						className: 'dt-head-center dt-body-center'
					}
				]
			});
			
			jse.libs.emails.updateTabCounters($this);
			
			// Bind event handlers of the modal.
			$this
				.on('click', '#add-contact', _onAddContact)
				.on('click', '.delete-contact', _onDeleteContact)
				.on('upload', '#upload-attachment', _onUploadAttachment)
				.on('click', '.delete-attachment', _onDeleteAttachment)
				.on('click', '.download-attachment', _onDownloadAttachment)
				.on('validator.validate', _onEmailDetailsValidation);
			
			done();
		};
		
		return module;
	});
