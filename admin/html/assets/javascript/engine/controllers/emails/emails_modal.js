'use strict';

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
gx.controllers.module('emails_modal', [gx.source + '/libs/emails', 'modal', 'datatable', 'normalize'],

/** @lends module:Controllers/emails_modal */

function (data) {

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
		contactsTableActions: function contactsTableActions(data, type, row, meta) {
			return '<div class="row-actions">' + '<span class="delete-contact action-item" title="' + jse.core.lang.translate('delete', 'buttons') + '">' + '<i class="fa fa-trash-o"></i>' + '</span>' + '</div>';
		},

		attachmentsTableActions: function attachmentsTableActions(data, type, row, meta) {
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
			return '<div class="row-actions">' + '<span class="delete-attachment action-item" title="' + jse.core.lang.translate('delete', 'buttons') + '">' + '<i class="fa fa-trash-o"></i>' + '</span>' + '<span class="download-attachment action-item" title="' + title + '" ' + disabled + '>' + '<i class="fa fa-download"></i>' + '</span>' + '</div>';
		},

		convertUpperCase: function convertUpperCase(data, type, row, meta) {
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
	var _onAddContact = function _onAddContact(event) {
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
	var _onDeleteContact = function _onDeleteContact(event) {
		var row = $(this).parents('tr');
		$this.find('#contacts-table').DataTable().row(row).remove().draw();
		jse.libs.emails.updateTabCounters($this);
	};

	/**
  * Called after the attachment is uploaded
  *
  * @param {object} event Contains event information.
  */
	var _onUploadAttachment = function _onUploadAttachment(event, response) {
		if (response.exception) {
			jse.libs.modal.message({
				title: jse.core.lang.translate('error', 'messages'),
				content: jse.core.lang.translate('message_upload_attachment_error', 'emails') + response.message
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
	var _onDeleteAttachment = function _onDeleteAttachment(event) {
		var row = $(this).parents('tr').get(0),
		    url = jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/DeleteAttachment',
		    data = {
			pageToken: jse.core.config.get('pageToken'),
			attachments: [$attachmentsTable.DataTable().row(row).data().path]
		};

		$.post(url, data, function (response) {
			jse.core.debug.info('AJAX File Remove Response', response);

			if (response.exception) {
				jse.libs.modal.message({
					title: jse.core.lang.translate('error', 'messages'),
					content: jse.core.lang.translate('message_remove_attachment_error') + response.message
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
	var _onDownloadAttachment = function _onDownloadAttachment(event) {
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
	var _onEmailDetailsValidation = function _onEmailDetailsValidation(event) {
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
	module.init = function (done) {
		// Contacts DataTable
		jse.libs.datatable.create($contactsTable, {
			autoWidth: false,
			order: [[0, 'asc'] // Email ASC
			],
			language: jse.core.config.get('languageCode') === 'de' ? jse.libs.datatable.getGermanTranslation() : null,
			lengthMenu: options.lengthMenu,
			pageLength: 5,
			columns: [{
				data: 'email',
				width: '45%'
			}, {
				data: 'name',
				width: '35%'
			}, {
				data: 'type',
				render: options.convertUpperCase,
				width: '10%'
			}, {
				data: null,
				orderable: false,
				defaultContent: '',
				render: options.contactsTableActions,
				width: '10%',
				className: 'dt-head-center dt-body-center'
			}]
		});

		// Attachments DataTable
		jse.libs.datatable.create($attachmentsTable, {
			autoWidth: false,
			order: [[0, 'asc'] // Path ASC
			],
			language: jse.core.config.get('languageCode') === 'de' ? jse.libs.datatable.getGermanTranslation() : null,
			lengthMenu: options.lengthMenu,
			pageLength: 5,
			columns: [{
				data: 'path',
				width: '90%'
			}, {
				data: null,
				orderable: false,
				defaultContent: '',
				render: options.attachmentsTableActions,
				width: '10%',
				className: 'dt-head-center dt-body-center'
			}]
		});

		jse.libs.emails.updateTabCounters($this);

		// Bind event handlers of the modal.
		$this.on('click', '#add-contact', _onAddContact).on('click', '.delete-contact', _onDeleteContact).on('upload', '#upload-attachment', _onUploadAttachment).on('click', '.delete-attachment', _onDeleteAttachment).on('click', '.download-attachment', _onDownloadAttachment).on('validator.validate', _onEmailDetailsValidation);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImVtYWlscy9lbWFpbHNfbW9kYWwuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkdGFibGUiLCIkdG9vbGJhciIsIiRjb250YWN0c1RhYmxlIiwiZmluZCIsIiRhdHRhY2htZW50c1RhYmxlIiwiZGVmYXVsdHMiLCJjb250YWN0c1RhYmxlQWN0aW9ucyIsInR5cGUiLCJyb3ciLCJtZXRhIiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJhdHRhY2htZW50c1RhYmxlQWN0aW9ucyIsImRpc2FibGVkIiwidGl0bGUiLCJmaWxlX2V4aXN0cyIsImNvbnZlcnRVcHBlckNhc2UiLCJ0b1VwcGVyQ2FzZSIsImxlbmd0aE1lbnUiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX29uQWRkQ29udGFjdCIsImV2ZW50IiwidHJpZ2dlciIsImxlbmd0aCIsImNvbnRhY3QiLCJuYW1lIiwibGlicyIsIm5vcm1hbGl6ZSIsImVzY2FwZUh0bWwiLCJ2YWwiLCJlbWFpbCIsIkRhdGFUYWJsZSIsImFkZCIsImRyYXciLCJyZW1vdmVDbGFzcyIsInByb3AiLCJlbWFpbHMiLCJ1cGRhdGVUYWJDb3VudGVycyIsIl9vbkRlbGV0ZUNvbnRhY3QiLCJwYXJlbnRzIiwicmVtb3ZlIiwiX29uVXBsb2FkQXR0YWNobWVudCIsInJlc3BvbnNlIiwiZXhjZXB0aW9uIiwibW9kYWwiLCJtZXNzYWdlIiwiY29udGVudCIsInBhdGgiLCJfb25EZWxldGVBdHRhY2htZW50IiwiZ2V0IiwidXJsIiwiY29uZmlnIiwicGFnZVRva2VuIiwiYXR0YWNobWVudHMiLCJwb3N0IiwiZGVidWciLCJpbmZvIiwiX29uRG93bmxvYWRBdHRhY2htZW50IiwiYXR0ciIsIndpbmRvdyIsIm9wZW4iLCJfb25FbWFpbERldGFpbHNWYWxpZGF0aW9uIiwiY3NzIiwiaW5pdCIsImRvbmUiLCJkYXRhdGFibGUiLCJjcmVhdGUiLCJhdXRvV2lkdGgiLCJvcmRlciIsImxhbmd1YWdlIiwiZ2V0R2VybWFuVHJhbnNsYXRpb24iLCJwYWdlTGVuZ3RoIiwiY29sdW1ucyIsIndpZHRoIiwicmVuZGVyIiwib3JkZXJhYmxlIiwiZGVmYXVsdENvbnRlbnQiLCJjbGFzc05hbWUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLGNBREQsRUFHQyxDQUNDRixHQUFHRyxNQUFILEdBQVksY0FEYixFQUVDLE9BRkQsRUFHQyxXQUhELEVBSUMsV0FKRCxDQUhEOztBQVVDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxVQUFTRCxFQUFFLGVBQUYsQ0FiVjs7O0FBZUM7Ozs7O0FBS0FFLFlBQVdGLEVBQUUsaUJBQUYsQ0FwQlo7OztBQXNCQzs7Ozs7QUFLQUcsa0JBQWlCSixNQUFNSyxJQUFOLENBQVcsaUJBQVgsQ0EzQmxCOzs7QUE2QkM7Ozs7O0FBS0FDLHFCQUFvQk4sTUFBTUssSUFBTixDQUFXLG9CQUFYLENBbENyQjs7O0FBb0NDOzs7OztBQUtBRSxZQUFXO0FBQ1ZDLHdCQUFzQiw4QkFBU1QsSUFBVCxFQUFlVSxJQUFmLEVBQXFCQyxHQUFyQixFQUEwQkMsSUFBMUIsRUFBZ0M7QUFDckQsVUFBTyw4QkFBOEIsa0RBQTlCLEdBQ05DLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQ0MsUUFERCxFQUNXLFNBRFgsQ0FETSxHQUVrQixJQUZsQixHQUV5QiwrQkFGekIsR0FFMkQsU0FGM0QsR0FFdUUsUUFGOUU7QUFHQSxHQUxTOztBQU9WQywyQkFBeUIsaUNBQVNqQixJQUFULEVBQWVVLElBQWYsRUFBcUJDLEdBQXJCLEVBQTBCQyxJQUExQixFQUFnQztBQUN4RDtBQUNBLE9BQUlNLFFBQUosRUFBY0MsS0FB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QUNSO0FBQ0M5RSxVQUFNLE1BRFA7QUFFQytFLFdBQU87QUFGUixJQURRLEVBS1I7QUFDQy9FLFVBQU0sSUFEUDtBQUVDaUYsZUFBVyxLQUZaO0FBR0NDLG9CQUFnQixFQUhqQjtBQUlDRixZQUFReEQsUUFBUVAsdUJBSmpCO0FBS0M4RCxXQUFPLEtBTFI7QUFNQ0ksZUFBVztBQU5aLElBTFE7QUFWbUMsR0FBN0M7O0FBMEJBdEUsTUFBSW1CLElBQUosQ0FBU1UsTUFBVCxDQUFnQkMsaUJBQWhCLENBQWtDMUMsS0FBbEM7O0FBRUE7QUFDQUEsUUFDRW1GLEVBREYsQ0FDSyxPQURMLEVBQ2MsY0FEZCxFQUM4QjFELGFBRDlCLEVBRUUwRCxFQUZGLENBRUssT0FGTCxFQUVjLGlCQUZkLEVBRWlDeEMsZ0JBRmpDLEVBR0V3QyxFQUhGLENBR0ssUUFITCxFQUdlLG9CQUhmLEVBR3FDckMsbUJBSHJDLEVBSUVxQyxFQUpGLENBSUssT0FKTCxFQUljLG9CQUpkLEVBSW9DOUIsbUJBSnBDLEVBS0U4QixFQUxGLENBS0ssT0FMTCxFQUtjLHNCQUxkLEVBS3NDckIscUJBTHRDLEVBTUVxQixFQU5GLENBTUssb0JBTkwsRUFNMkJqQix5QkFOM0I7O0FBUUFHO0FBQ0EsRUE1RUQ7O0FBOEVBLFFBQU94RSxNQUFQO0FBQ0EsQ0FyVUYiLCJmaWxlIjoiZW1haWxzL2VtYWlsc19tb2RhbC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZW1haWxzX21vZGFsLmpzIDIwMTUtMTAtMTUgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRW1haWxzIE1vZGFsIENvbnRyb2xsZXJcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgd2lsbCBoYW5kbGUgdGhlIG1vZGFsIGRpYWxvZyBvcGVyYXRpb25zIG9mIHRoZSBhZG1pbi9lbWFpbHMgcGFnZS5cbiAqXG4gKiBAbW9kdWxlIENvbnRyb2xsZXJzL2VtYWlsc19tb2RhbFxuICovXG5neC5jb250cm9sbGVycy5tb2R1bGUoXG5cdCdlbWFpbHNfbW9kYWwnLFxuXHRcblx0W1xuXHRcdGd4LnNvdXJjZSArICcvbGlicy9lbWFpbHMnLFxuXHRcdCdtb2RhbCcsXG5cdFx0J2RhdGF0YWJsZScsXG5cdFx0J25vcm1hbGl6ZSdcblx0XSxcblx0XG5cdC8qKiBAbGVuZHMgbW9kdWxlOkNvbnRyb2xsZXJzL2VtYWlsc19tb2RhbCAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVGFibGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGFibGUgPSAkKCcjZW1haWxzLXRhYmxlJyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVG9vbGJhciBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0b29sYmFyID0gJCgnI2VtYWlscy10b29sYmFyJyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogQ29udGFjdHMgVGFibGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkY29udGFjdHNUYWJsZSA9ICR0aGlzLmZpbmQoJyNjb250YWN0cy10YWJsZScpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEF0dGFjaG1lbnRzIFRhYmxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JGF0dGFjaG1lbnRzVGFibGUgPSAkdGhpcy5maW5kKCcjYXR0YWNobWVudHMtdGFibGUnKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE1vZHVsZSBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdGNvbnRhY3RzVGFibGVBY3Rpb25zOiBmdW5jdGlvbihkYXRhLCB0eXBlLCByb3csIG1ldGEpIHtcblx0XHRcdFx0XHRyZXR1cm4gJzxkaXYgY2xhc3M9XCJyb3ctYWN0aW9uc1wiPicgKyAnPHNwYW4gY2xhc3M9XCJkZWxldGUtY29udGFjdCBhY3Rpb24taXRlbVwiIHRpdGxlPVwiJyArXG5cdFx0XHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZShcblx0XHRcdFx0XHRcdFx0J2RlbGV0ZScsICdidXR0b25zJykgKyAnXCI+JyArICc8aSBjbGFzcz1cImZhIGZhLXRyYXNoLW9cIj48L2k+JyArICc8L3NwYW4+JyArICc8L2Rpdj4nO1xuXHRcdFx0XHR9LFxuXHRcdFx0XHRcblx0XHRcdFx0YXR0YWNobWVudHNUYWJsZUFjdGlvbnM6IGZ1bmN0aW9uKGRhdGEsIHR5cGUsIHJvdywgbWV0YSkge1xuXHRcdFx0XHRcdC8vIENoZWNrIGlmIGF0dGFjaG1lbnQgZmlsZSBleGlzdHMgaW4gdGhlIHNlcnZlciBhbmQgdGh1cyBjYW4gYmUgZG93bmxvYWRlZC5cblx0XHRcdFx0XHR2YXIgZGlzYWJsZWQsIHRpdGxlO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGlmIChkYXRhLmZpbGVfZXhpc3RzKSB7XG5cdFx0XHRcdFx0XHRkaXNhYmxlZCA9ICcnO1xuXHRcdFx0XHRcdFx0dGl0bGUgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZG93bmxvYWQnLCAnYnV0dG9ucycpO1xuXHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRkaXNhYmxlZCA9ICdkaXNhYmxlZD1cImRpc2FibGVkXCInO1xuXHRcdFx0XHRcdFx0dGl0bGUgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnbWVzc2FnZV9kb3dubG9hZF9hdHRhY2htZW50X2Vycm9yJywgJ2VtYWlscycpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBHZW5lcmF0ZSB0YWJsZSBhY3Rpb25zIGh0bWwgZm9yIHRhYmxlIHJvdy5cblx0XHRcdFx0XHRyZXR1cm4gJzxkaXYgY2xhc3M9XCJyb3ctYWN0aW9uc1wiPicgKyAnPHNwYW4gY2xhc3M9XCJkZWxldGUtYXR0YWNobWVudCBhY3Rpb24taXRlbVwiIHRpdGxlPVwiJyArXG5cdFx0XHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZShcblx0XHRcdFx0XHRcdFx0J2RlbGV0ZScsICdidXR0b25zJykgKyAnXCI+JyArICc8aSBjbGFzcz1cImZhIGZhLXRyYXNoLW9cIj48L2k+JyArICc8L3NwYW4+JyArXG5cdFx0XHRcdFx0XHQnPHNwYW4gY2xhc3M9XCJkb3dubG9hZC1hdHRhY2htZW50IGFjdGlvbi1pdGVtXCIgdGl0bGU9XCInICsgdGl0bGUgKyAnXCIgJyArIGRpc2FibGVkICsgJz4nICtcblx0XHRcdFx0XHRcdCc8aSBjbGFzcz1cImZhIGZhLWRvd25sb2FkXCI+PC9pPicgKyAnPC9zcGFuPicgKyAnPC9kaXY+Jztcblx0XHRcdFx0fSxcblx0XHRcdFx0XG5cdFx0XHRcdGNvbnZlcnRVcHBlckNhc2U6IGZ1bmN0aW9uKGRhdGEsIHR5cGUsIHJvdywgbWV0YSkge1xuXHRcdFx0XHRcdHJldHVybiBkYXRhLnRvVXBwZXJDYXNlKCk7XG5cdFx0XHRcdH0sXG5cdFx0XHRcdFxuXHRcdFx0XHRsZW5ndGhNZW51OiBbWzUsIDEwXSwgWzUsIDEwXV1cblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgTW9kdWxlIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBBZGQgYSBjb250YWN0IHdpdGggdGhlIHByb3ZpZGVkIGRhdGEgaW50byB0aGUgY29udGFjdHMgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vbkFkZENvbnRhY3QgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0Ly8gVmFsaWRhdGUgQ29udGFjdCBGb3JtXG5cdFx0XHQkdGhpcy5maW5kKCcudGFiLWNvbnRlbnQuYmNjLWNjJykudHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJyk7IC8vIFRyaWdnZXIgZm9ybSB2YWxpZGF0aW9uXG5cdFx0XHRpZiAoJHRoaXMuZmluZCgnLnRhYi1jb250ZW50LmJjYy1jYyAuZXJyb3InKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gQWRkIGNvbnRhY3QgdG8gdGFibGUuXG5cdFx0XHR2YXIgY29udGFjdCA9IHtcblx0XHRcdFx0bmFtZToganNlLmxpYnMubm9ybWFsaXplLmVzY2FwZUh0bWwoJHRoaXMuZmluZCgnI2NvbnRhY3QtbmFtZScpLnZhbCgpKSxcblx0XHRcdFx0ZW1haWw6IGpzZS5saWJzLm5vcm1hbGl6ZS5lc2NhcGVIdG1sKCR0aGlzLmZpbmQoJyNjb250YWN0LWVtYWlsJykudmFsKCkpLFxuXHRcdFx0XHR0eXBlOiBqc2UubGlicy5ub3JtYWxpemUuZXNjYXBlSHRtbCgkdGhpcy5maW5kKCcjY29udGFjdC10eXBlJykudmFsKCkpXG5cdFx0XHR9O1xuXHRcdFx0JHRoaXMuZmluZCgnI2NvbnRhY3RzLXRhYmxlJykuRGF0YVRhYmxlKCkucm93LmFkZChjb250YWN0KS5kcmF3KCk7XG5cdFx0XHQkdGhpcy5maW5kKCcjY29udGFjdC1uYW1lLCAjY29udGFjdC1lbWFpbCwgI2NvbnRhY3QtdHlwZScpLnJlbW92ZUNsYXNzKCd2YWxpZCBlcnJvcicpO1xuXHRcdFx0JHRoaXMuZmluZCgnI2NvbnRhY3QtbmFtZSwgI2NvbnRhY3QtZW1haWwnKS52YWwoJycpO1xuXHRcdFx0JHRoaXMuZmluZCgnI2NvbnRhY3QtdHlwZSBvcHRpb246Zmlyc3QnKS5wcm9wKCdzZWxlY3RlZCcsIHRydWUpO1xuXHRcdFx0anNlLmxpYnMuZW1haWxzLnVwZGF0ZVRhYkNvdW50ZXJzKCR0aGlzKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFJlbW92ZSBjb250YWN0IGZyb20gY29udGFjdHMgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgY29udGFpbnMgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vbkRlbGV0ZUNvbnRhY3QgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0dmFyIHJvdyA9ICQodGhpcykucGFyZW50cygndHInKTtcblx0XHRcdCR0aGlzLmZpbmQoJyNjb250YWN0cy10YWJsZScpLkRhdGFUYWJsZSgpLnJvdyhyb3cpLnJlbW92ZSgpLmRyYXcoKTtcblx0XHRcdGpzZS5saWJzLmVtYWlscy51cGRhdGVUYWJDb3VudGVycygkdGhpcyk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDYWxsZWQgYWZ0ZXIgdGhlIGF0dGFjaG1lbnQgaXMgdXBsb2FkZWRcblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBDb250YWlucyBldmVudCBpbmZvcm1hdGlvbi5cblx0XHQgKi9cblx0XHR2YXIgX29uVXBsb2FkQXR0YWNobWVudCA9IGZ1bmN0aW9uKGV2ZW50LCByZXNwb25zZSkge1xuXHRcdFx0aWYgKHJlc3BvbnNlLmV4Y2VwdGlvbikge1xuXHRcdFx0XHRqc2UubGlicy5tb2RhbC5tZXNzYWdlKHtcblx0XHRcdFx0XHR0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyksXG5cdFx0XHRcdFx0Y29udGVudDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ21lc3NhZ2VfdXBsb2FkX2F0dGFjaG1lbnRfZXJyb3InLCAnZW1haWxzJylcblx0XHRcdFx0XHQrIHJlc3BvbnNlLm1lc3NhZ2Vcblx0XHRcdFx0fSk7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0JHRoaXMuZmluZCgnI2F0dGFjaG1lbnRzLXRhYmxlJykuRGF0YVRhYmxlKCkucm93LmFkZCh7XG5cdFx0XHRcdHBhdGg6IGpzZS5saWJzLm5vcm1hbGl6ZS5lc2NhcGVIdG1sKHJlc3BvbnNlLnBhdGgpLFxuXHRcdFx0XHRmaWxlX2V4aXN0czogdHJ1ZVxuXHRcdFx0fSkuZHJhdygpO1xuXHRcdFx0XG5cdFx0XHQkdGhpcy5maW5kKCcjdXBsb2FkLWF0dGFjaG1lbnQnKS52YWwoJycpO1xuXHRcdFx0anNlLmxpYnMuZW1haWxzLnVwZGF0ZVRhYkNvdW50ZXJzKCR0aGlzKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFJlbW92ZSBzZWxlY3RlZCBhdHRhY2htZW50IGZyb20gZW1haWwgYW5kIGZyb20gc2VydmVyLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IENvbnRhaW5zIGV2ZW50IGluZm9ybWF0aW9uLlxuXHRcdCAqL1xuXHRcdHZhciBfb25EZWxldGVBdHRhY2htZW50ID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciByb3cgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZ2V0KDApLFxuXHRcdFx0XHR1cmwgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPUVtYWlscy9EZWxldGVBdHRhY2htZW50Jyxcblx0XHRcdFx0ZGF0YSA9IHtcblx0XHRcdFx0XHRwYWdlVG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpLFxuXHRcdFx0XHRcdGF0dGFjaG1lbnRzOiBbJGF0dGFjaG1lbnRzVGFibGUuRGF0YVRhYmxlKCkucm93KHJvdykuZGF0YSgpLnBhdGhdXG5cdFx0XHRcdH07XG5cdFx0XHRcblx0XHRcdCQucG9zdCh1cmwsIGRhdGEsIGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmluZm8oJ0FKQVggRmlsZSBSZW1vdmUgUmVzcG9uc2UnLCByZXNwb25zZSk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAocmVzcG9uc2UuZXhjZXB0aW9uKSB7XG5cdFx0XHRcdFx0anNlLmxpYnMubW9kYWwubWVzc2FnZSh7XG5cdFx0XHRcdFx0XHR0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyksXG5cdFx0XHRcdFx0XHRjb250ZW50OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnbWVzc2FnZV9yZW1vdmVfYXR0YWNobWVudF9lcnJvcicpXG5cdFx0XHRcdFx0XHQrIHJlc3BvbnNlLm1lc3NhZ2Vcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdCR0aGlzLmZpbmQoJyNhdHRhY2htZW50cy10YWJsZScpLkRhdGFUYWJsZSgpLnJvdyhyb3cpLnJlbW92ZSgpLmRyYXcoKTtcblx0XHRcdFx0anNlLmxpYnMuZW1haWxzLnVwZGF0ZVRhYkNvdW50ZXJzKCR0aGlzKTtcblx0XHRcdH0sICdqc29uJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBEb3dubG9hZCBzZWxlY3RlZCBhdHRhY2htZW50LlxuXHRcdCAqXG5cdFx0ICogQSBuZXcgd2luZG93IHRhYiB3aWxsIGJlIG9wZW5lZCBhbmQgdGhlIGZpbGUgZG93bmxvYWQgd2lsbCBzdGFydCBpbW1lZGlhdGVseS4gSWZcblx0XHQgKiB0aGVyZSBhcmUgYW55IGVycm9ycyBmcm9tIHRoZSBQSFAgY29kZSB0aGV5IHdpbGwgYmUgZGlzcGxheWVkIGluIHRoZSBuZXcgdGFiIGFuZFxuXHRcdCAqIHRoZXkgd2lsbCBub3QgYWZmZWN0IHRoZSBjdXJyZW50IHBhZ2UuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vbkRvd25sb2FkQXR0YWNobWVudCA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRpZiAoJCh0aGlzKS5hdHRyKCdkaXNhYmxlZCcpID09PSAnZGlzYWJsZWQnKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblx0XHRcdHZhciByb3cgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZ2V0KDApLFxuXHRcdFx0XHRwYXRoID0gJGF0dGFjaG1lbnRzVGFibGUuRGF0YVRhYmxlKCkucm93KHJvdykuZGF0YSgpLnBhdGgsXG5cdFx0XHRcdHVybCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89RW1haWxzL0Rvd25sb2FkQXR0YWNobWVudCZwYXRoPScgKyBwYXRoO1xuXHRcdFx0d2luZG93Lm9wZW4odXJsLCAnX2JsYW5rJyk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBDYWxsYmFjayB0byB0aGUgdmFsaWRhdGlvbiBvZiB0aGUgZmlyc3QgdGFiIG9mIHRoZSBtb2RhbC5cblx0XHQgKlxuXHRcdCAqIE1ha2UgdGhlIHRhYiBoZWFkbGluZSBsaW5rIHJlZCBzbyB0aGF0IHRoZSB1c2VyIGNhbiBzZWUgdGhhdCB0aGVyZSBpcyBhbiBlcnJvclxuXHRcdCAqIGluc2lkZSB0aGUgZWxlbWVudHMgb2YgdGhpcyB0YWIuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vbkVtYWlsRGV0YWlsc1ZhbGlkYXRpb24gPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0Ly8gUGFpbnQgdGhlIHBhcmVudCB0YWIgc28gdGhhdCB0aGUgdXNlciBrbm93cyB0aGF0IHRoZXJlIGlzIGEgcHJvYmxlbSBpbiB0aGUgZm9ybS5cblx0XHRcdGlmICgkdGhpcy5maW5kKCcudGFiLWNvbnRlbnQuZGV0YWlscyAuZXJyb3InKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdCR0aGlzLmZpbmQoJy50YWItaGVhZGxpbmUuZGV0YWlscycpLmNzcygnY29sb3InLCAncmVkJyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQkdGhpcy5maW5kKCcudGFiLWhlYWRsaW5lLmRldGFpbHMnKS5jc3MoJ2NvbG9yJywgJycpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgbW9kdWxlLCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdC8vIENvbnRhY3RzIERhdGFUYWJsZVxuXHRcdFx0anNlLmxpYnMuZGF0YXRhYmxlLmNyZWF0ZSgkY29udGFjdHNUYWJsZSwge1xuXHRcdFx0XHRhdXRvV2lkdGg6IGZhbHNlLFxuXHRcdFx0XHRvcmRlcjogW1xuXHRcdFx0XHRcdFswLCAnYXNjJ10gLy8gRW1haWwgQVNDXG5cdFx0XHRcdF0sXG5cdFx0XHRcdGxhbmd1YWdlOiAoIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2xhbmd1YWdlQ29kZScpID09PSAnZGUnKVxuXHRcdFx0XHRcdD8ganNlLmxpYnMuZGF0YXRhYmxlLmdldEdlcm1hblRyYW5zbGF0aW9uKClcblx0XHRcdFx0XHQ6IG51bGwsXG5cdFx0XHRcdGxlbmd0aE1lbnU6IG9wdGlvbnMubGVuZ3RoTWVudSxcblx0XHRcdFx0cGFnZUxlbmd0aDogNSxcblx0XHRcdFx0Y29sdW1uczogW1xuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGRhdGE6ICdlbWFpbCcsXG5cdFx0XHRcdFx0XHR3aWR0aDogJzQ1JSdcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGRhdGE6ICduYW1lJyxcblx0XHRcdFx0XHRcdHdpZHRoOiAnMzUlJ1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0ZGF0YTogJ3R5cGUnLFxuXHRcdFx0XHRcdFx0cmVuZGVyOiBvcHRpb25zLmNvbnZlcnRVcHBlckNhc2UsXG5cdFx0XHRcdFx0XHR3aWR0aDogJzEwJSdcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGRhdGE6IG51bGwsXG5cdFx0XHRcdFx0XHRvcmRlcmFibGU6IGZhbHNlLFxuXHRcdFx0XHRcdFx0ZGVmYXVsdENvbnRlbnQ6ICcnLFxuXHRcdFx0XHRcdFx0cmVuZGVyOiBvcHRpb25zLmNvbnRhY3RzVGFibGVBY3Rpb25zLFxuXHRcdFx0XHRcdFx0d2lkdGg6ICcxMCUnLFxuXHRcdFx0XHRcdFx0Y2xhc3NOYW1lOiAnZHQtaGVhZC1jZW50ZXIgZHQtYm9keS1jZW50ZXInXG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRdXG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gQXR0YWNobWVudHMgRGF0YVRhYmxlXG5cdFx0XHRqc2UubGlicy5kYXRhdGFibGUuY3JlYXRlKCRhdHRhY2htZW50c1RhYmxlLCB7XG5cdFx0XHRcdGF1dG9XaWR0aDogZmFsc2UsXG5cdFx0XHRcdG9yZGVyOiBbXG5cdFx0XHRcdFx0WzAsICdhc2MnXSAvLyBQYXRoIEFTQ1xuXHRcdFx0XHRdLFxuXHRcdFx0XHRsYW5ndWFnZTogKCBqc2UuY29yZS5jb25maWcuZ2V0KCdsYW5ndWFnZUNvZGUnKSA9PT0gJ2RlJylcblx0XHRcdFx0XHQ/IGpzZS5saWJzLmRhdGF0YWJsZS5nZXRHZXJtYW5UcmFuc2xhdGlvbigpXG5cdFx0XHRcdFx0OiBudWxsLFxuXHRcdFx0XHRsZW5ndGhNZW51OiBvcHRpb25zLmxlbmd0aE1lbnUsXG5cdFx0XHRcdHBhZ2VMZW5ndGg6IDUsXG5cdFx0XHRcdGNvbHVtbnM6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRkYXRhOiAncGF0aCcsXG5cdFx0XHRcdFx0XHR3aWR0aDogJzkwJSdcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGRhdGE6IG51bGwsXG5cdFx0XHRcdFx0XHRvcmRlcmFibGU6IGZhbHNlLFxuXHRcdFx0XHRcdFx0ZGVmYXVsdENvbnRlbnQ6ICcnLFxuXHRcdFx0XHRcdFx0cmVuZGVyOiBvcHRpb25zLmF0dGFjaG1lbnRzVGFibGVBY3Rpb25zLFxuXHRcdFx0XHRcdFx0d2lkdGg6ICcxMCUnLFxuXHRcdFx0XHRcdFx0Y2xhc3NOYW1lOiAnZHQtaGVhZC1jZW50ZXIgZHQtYm9keS1jZW50ZXInXG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRdXG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0anNlLmxpYnMuZW1haWxzLnVwZGF0ZVRhYkNvdW50ZXJzKCR0aGlzKTtcblx0XHRcdFxuXHRcdFx0Ly8gQmluZCBldmVudCBoYW5kbGVycyBvZiB0aGUgbW9kYWwuXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ2NsaWNrJywgJyNhZGQtY29udGFjdCcsIF9vbkFkZENvbnRhY3QpXG5cdFx0XHRcdC5vbignY2xpY2snLCAnLmRlbGV0ZS1jb250YWN0JywgX29uRGVsZXRlQ29udGFjdClcblx0XHRcdFx0Lm9uKCd1cGxvYWQnLCAnI3VwbG9hZC1hdHRhY2htZW50JywgX29uVXBsb2FkQXR0YWNobWVudClcblx0XHRcdFx0Lm9uKCdjbGljaycsICcuZGVsZXRlLWF0dGFjaG1lbnQnLCBfb25EZWxldGVBdHRhY2htZW50KVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5kb3dubG9hZC1hdHRhY2htZW50JywgX29uRG93bmxvYWRBdHRhY2htZW50KVxuXHRcdFx0XHQub24oJ3ZhbGlkYXRvci52YWxpZGF0ZScsIF9vbkVtYWlsRGV0YWlsc1ZhbGlkYXRpb24pO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
