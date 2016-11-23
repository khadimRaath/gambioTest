'use strict';

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
gx.controllers.module('emails_table', [gx.source + '/libs/emails', 'modal', 'datatable', 'normalize'],

/** @lends module:Controllers/emails_table */

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
		emailsTableActions: function emailsTableActions() {
			return '<div class="row-actions">' + '<span class="send-email action-item" title="' + jse.core.lang.translate('send', 'buttons') + '">' + '<i class="fa fa-envelope-o"></i>' + '</span>' + '<span class="forward-email action-item" title="' + jse.core.lang.translate('forward', 'buttons') + '">' + '<i class="fa fa-share"></i>' + '</span>' + '<span class="delete-email action-item" title="' + jse.core.lang.translate('delete', 'buttons') + '">' + '<i class="fa fa-trash-o"></i>' + '</span>' + '<span class="preview-email action-item" title="' + jse.core.lang.translate('preview', 'buttons') + '">' + '<i class="fa fa-eye"></i>' + '</span>' + '</div>';
		},

		convertPendingToString: function convertPendingToString(data, type, row, meta) {
			return data === true ? jse.core.lang.translate('email_pending', 'emails') : jse.core.lang.translate('email_sent', 'emails');
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
	var _onSelectAllRows = function _onSelectAllRows(event) {
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
	var _onSendEmail = function _onSendEmail(event) {
		var $row = $(this).parents('tr');

		jse.libs.modal.message({
			title: jse.core.lang.translate('send', 'buttons'),
			content: jse.core.lang.translate('prompt_send_email', 'emails'),
			buttons: [{
				text: jse.core.lang.translate('no', 'lightbox_buttons'),
				click: function click() {
					$(this).dialog('close');
				}
			}, {
				text: jse.core.lang.translate('yes', 'lightbox_buttons'),
				click: function click() {
					var email = $row.data();
					jse.libs.emails.sendCollection([email]).done(function (response) {
						$this.DataTable().ajax.reload();
						jse.libs.emails.getAttachmentsSize($('#attachments-size'));
					}).fail(function (response) {
						var title = jse.core.lang.translate('error', 'messages');

						jse.libs.modal.message({
							title: title,
							content: response.message
						});
					});
					$(this).dialog('close');
				}
			}]
		});
	};

	/**
  * Display modal with email information but without contacts.
  *
  * The user will be able to set new contacts and send the email (kind of "duplicate" method).
  *
  * @param {object} event Contains event information.
  */
	var _onForwardEmail = function _onForwardEmail(event) {
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
	var _onDeleteEmail = function _onDeleteEmail(event) {
		var $row = $(this).parents('tr'),
		    email = $row.data();

		jse.libs.modal.message({
			title: jse.core.lang.translate('delete', 'buttons'),
			content: jse.core.lang.translate('prompt_delete_email', 'emails'),
			buttons: [{
				text: jse.core.lang.translate('no', 'lightbox_buttons'),
				click: function click() {
					$(this).dialog('close');
				}
			}, {
				text: jse.core.lang.translate('yes', 'lightbox_buttons'),
				click: function click() {
					jse.libs.emails.deleteCollection([email]).done(function (response) {
						$this.DataTable().ajax.reload();
						jse.libs.emails.getAttachmentsSize($('#attachments-size'));
					}).fail(function (response) {
						var title = jse.core.lang.translate('error', 'messages');
						jse.libs.modal.message({
							title: title,
							content: response.message
						});
					});
					$(this).dialog('close');
				}
			}]
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
	var _onPreviewEmail = function _onPreviewEmail(event) {
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
	module.init = function (done) {
		// Create a DataTable instance for the email records.
		jse.libs.datatable.create($this, {
			processing: false,
			serverSide: true,
			dom: 'rtip',
			autoWidth: false,
			language: jse.core.config.get('languageCode') === 'de' ? jse.libs.datatable.getGermanTranslation() : null,
			ajax: {
				url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/DataTable',
				type: 'POST'
			},
			order: [[2, 'desc']],
			pageLength: 20,
			columns: [{
				data: null,
				orderable: false,
				defaultContent: '<input type="checkbox" data-single_checkbox />',
				width: '2%',
				className: 'dt-head-center dt-body-center'
			}, {
				data: 'row_count',
				orderable: false,
				width: '3%',
				className: 'dt-head-center dt-body-center'
			}, {
				data: 'creation_date',
				width: '12%'
			}, {
				data: 'sent_date',
				width: '12%'
			}, {
				data: 'sender',
				width: '12%'
			}, {
				data: 'recipient',
				width: '12%'
			}, {
				data: 'subject',
				width: '27%'
			}, {
				data: 'is_pending',
				width: '8%',
				render: options.convertPendingToString
			}, {
				data: null,
				orderable: false,
				defaultContent: '',
				render: options.emailsTableActions,
				width: '12%'
			}]
		});

		// Add table error handler.
		jse.libs.datatable.error($this, function (event, settings, techNote, message) {
			jse.libs.modal.message({
				title: 'DataTables ' + jse.core.lang.translate('error', 'messages'),
				content: message
			});
		});

		// Add ajax error handler.
		jse.libs.datatable.ajaxComplete($this, function (event, settings, json) {
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
		$this.on('init.dt', function (e, settings, json) {
			$('.paginator').appendTo($('#emails-table_wrapper'));
			$('#emails-table_info').appendTo($('.paginator .datatable-components')).css('clear', 'none');
			$('#emails-table_paginate').appendTo($('.paginator .datatable-components')).css('clear', 'none');
		});

		// Recreate the checkbox widgets.
		$this.on('draw.dt', function () {
			$this.find('tbody').attr('data-gx-widget', 'checkbox');
			gx.widgets.init($this); // Initialize the checkbox widget.
		});

		// Add spinner to table loading actions.
		var $spinner;
		$this.on('preXhr.dt', function (e, settings, json) {
			$spinner = jse.libs.loading_spinner.show($this);
		});
		$this.on('xhr.dt', function (e, settings, json) {
			if ($spinner) {
				jse.libs.loading_spinner.hide($spinner);
			}
		});

		// Bind event handlers of the emails table.
		$this.on('click', '#select-all-rows', _onSelectAllRows).on('click', '.send-email', _onSendEmail).on('click', '.forward-email', _onForwardEmail).on('click', '.delete-email', _onDeleteEmail).on('click', '.preview-email', _onPreviewEmail);

		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImVtYWlscy9lbWFpbHNfdGFibGUuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkdG9vbGJhciIsIiRtb2RhbCIsImRlZmF1bHRzIiwiZW1haWxzVGFibGVBY3Rpb25zIiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJjb252ZXJ0UGVuZGluZ1RvU3RyaW5nIiwidHlwZSIsInJvdyIsIm1ldGEiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX29uU2VsZWN0QWxsUm93cyIsImV2ZW50IiwicHJvcCIsImZpbmQiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwiX29uU2VuZEVtYWlsIiwiJHJvdyIsInBhcmVudHMiLCJsaWJzIiwibW9kYWwiLCJtZXNzYWdlIiwidGl0bGUiLCJjb250ZW50IiwiYnV0dG9ucyIsInRleHQiLCJjbGljayIsImRpYWxvZyIsImVtYWlsIiwiZW1haWxzIiwic2VuZENvbGxlY3Rpb24iLCJkb25lIiwicmVzcG9uc2UiLCJEYXRhVGFibGUiLCJhamF4IiwicmVsb2FkIiwiZ2V0QXR0YWNobWVudHNTaXplIiwiZmFpbCIsIl9vbkZvcndhcmRFbWFpbCIsInJlc2V0TW9kYWwiLCJsb2FkRW1haWxPbk1vZGFsIiwidmFsIiwiY2xlYXIiLCJkcmF3Iiwid2lkdGgiLCJoZWlnaHQiLCJkaWFsb2dDbGFzcyIsImNsb3NlT25Fc2NhcGUiLCJnZXREZWZhdWx0TW9kYWxCdXR0b25zIiwib3BlbiIsImNvbG9yaXplQnV0dG9uc0ZvckVkaXRNb2RlIiwiX29uRGVsZXRlRW1haWwiLCJkZWxldGVDb2xsZWN0aW9uIiwiX29uUHJldmlld0VtYWlsIiwiZ2V0UHJldmlld01vZGFsQnV0dG9ucyIsImNvbG9yaXplQnV0dG9uc0ZvclByZXZpZXdNb2RlIiwiaW5pdCIsImRhdGF0YWJsZSIsImNyZWF0ZSIsInByb2Nlc3NpbmciLCJzZXJ2ZXJTaWRlIiwiZG9tIiwiYXV0b1dpZHRoIiwibGFuZ3VhZ2UiLCJjb25maWciLCJnZXQiLCJnZXRHZXJtYW5UcmFuc2xhdGlvbiIsInVybCIsIm9yZGVyIiwicGFnZUxlbmd0aCIsImNvbHVtbnMiLCJvcmRlcmFibGUiLCJkZWZhdWx0Q29udGVudCIsImNsYXNzTmFtZSIsInJlbmRlciIsImVycm9yIiwic2V0dGluZ3MiLCJ0ZWNoTm90ZSIsImFqYXhDb21wbGV0ZSIsImpzb24iLCJleGNlcHRpb24iLCJkZWJ1ZyIsIm9uIiwiZSIsImFwcGVuZFRvIiwiY3NzIiwiYXR0ciIsIndpZGdldHMiLCIkc3Bpbm5lciIsImxvYWRpbmdfc3Bpbm5lciIsInNob3ciLCJoaWRlIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQ0MsY0FERCxFQUdDLENBQ0NGLEdBQUdHLE1BQUgsR0FBWSxjQURiLEVBRUMsT0FGRCxFQUdDLFdBSEQsRUFJQyxXQUpELENBSEQ7O0FBVUM7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVdELEVBQUUsaUJBQUYsQ0FiWjs7O0FBZUM7Ozs7O0FBS0FFLFVBQVNGLEVBQUUsZUFBRixDQXBCVjs7O0FBc0JDOzs7OztBQUtBRyxZQUFXO0FBQ1ZDLHNCQUFvQiw4QkFBVztBQUM5QixVQUFPLDhCQUE4Qiw4Q0FBOUIsR0FDSkMsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FDRCxNQURDLEVBRUQsU0FGQyxDQURJLEdBR1EsSUFIUixHQUdlLGtDQUhmLEdBR29ELFNBSHBELEdBSU4saURBSk0sR0FLSkgsSUFBSUMsSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsU0FBeEIsRUFBbUMsU0FBbkMsQ0FMSSxHQU1OLElBTk0sR0FPTiw2QkFQTSxHQU8wQixTQVAxQixHQVFOLGdE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RixFQUFOLENBQVMsV0FBVCxFQUFzQixVQUFTQyxDQUFULEVBQVlQLFFBQVosRUFBc0JHLElBQXRCLEVBQTRCO0FBQ2pEUyxjQUFXeEYsSUFBSW1CLElBQUosQ0FBU3NFLGVBQVQsQ0FBeUJDLElBQXpCLENBQThCaEcsS0FBOUIsQ0FBWDtBQUNBLEdBRkQ7QUFHQUEsUUFBTXdGLEVBQU4sQ0FBUyxRQUFULEVBQW1CLFVBQVNDLENBQVQsRUFBWVAsUUFBWixFQUFzQkcsSUFBdEIsRUFBNEI7QUFDOUMsT0FBSVMsUUFBSixFQUFjO0FBQ2J4RixRQUFJbUIsSUFBSixDQUFTc0UsZUFBVCxDQUF5QkUsSUFBekIsQ0FBOEJILFFBQTlCO0FBQ0E7QUFDRCxHQUpEOztBQU1BO0FBQ0E5RixRQUNFd0YsRUFERixDQUNLLE9BREwsRUFDYyxrQkFEZCxFQUNrQ3hFLGdCQURsQyxFQUVFd0UsRUFGRixDQUVLLE9BRkwsRUFFYyxhQUZkLEVBRTZCbEUsWUFGN0IsRUFHRWtFLEVBSEYsQ0FHSyxPQUhMLEVBR2MsZ0JBSGQsRUFHZ0M1QyxlQUhoQyxFQUlFNEMsRUFKRixDQUlLLE9BSkwsRUFJYyxlQUpkLEVBSStCL0IsY0FKL0IsRUFLRStCLEVBTEYsQ0FLSyxPQUxMLEVBS2MsZ0JBTGQsRUFLZ0M3QixlQUxoQzs7QUFPQXRCO0FBQ0EsRUF6SEQ7O0FBMkhBO0FBQ0EsUUFBT3hDLE1BQVA7QUFDQSxDQWhZRiIsImZpbGUiOiJlbWFpbHMvZW1haWxzX3RhYmxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBlbWFpbHNfdGFibGUuanMgMjAxNS0xMC0xNSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRW1haWxzIFRhYmxlIENvbnRyb2xsZXJcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgd2lsbCBoYW5kbGUgdGhlIG1haW4gdGFibGUgb3BlcmF0aW9ucyBvZiB0aGUgYWRtaW4vZW1haWxzIHBhZ2UuXG4gKlxuICogQG1vZHVsZSBDb250cm9sbGVycy9lbWFpbHNfdGFibGVcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQnZW1haWxzX3RhYmxlJyxcblx0XG5cdFtcblx0XHRneC5zb3VyY2UgKyAnL2xpYnMvZW1haWxzJyxcblx0XHQnbW9kYWwnLFxuXHRcdCdkYXRhdGFibGUnLFxuXHRcdCdub3JtYWxpemUnXG5cdF0sXG5cdFxuXHQvKiogQGxlbmRzIG1vZHVsZTpDb250cm9sbGVycy9lbWFpbHNfdGFibGUgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFRvb2xiYXIgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdG9vbGJhciA9ICQoJyNlbWFpbHMtdG9vbGJhcicpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZGFsIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JG1vZGFsID0gJCgnI2VtYWlscy1tb2RhbCcpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgTW9kdWxlIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0ZW1haWxzVGFibGVBY3Rpb25zOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRyZXR1cm4gJzxkaXYgY2xhc3M9XCJyb3ctYWN0aW9uc1wiPicgKyAnPHNwYW4gY2xhc3M9XCJzZW5kLWVtYWlsIGFjdGlvbi1pdGVtXCIgdGl0bGU9XCInXG5cdFx0XHRcdFx0XHQrIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKFxuXHRcdFx0XHRcdFx0XHQnc2VuZCcsXG5cdFx0XHRcdFx0XHRcdCdidXR0b25zJykgKyAnXCI+JyArICc8aSBjbGFzcz1cImZhIGZhLWVudmVsb3BlLW9cIj48L2k+JyArICc8L3NwYW4+JyArXG5cdFx0XHRcdFx0XHQnPHNwYW4gY2xhc3M9XCJmb3J3YXJkLWVtYWlsIGFjdGlvbi1pdGVtXCIgdGl0bGU9XCInXG5cdFx0XHRcdFx0XHQrIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdmb3J3YXJkJywgJ2J1dHRvbnMnKSArXG5cdFx0XHRcdFx0XHQnXCI+JyArXG5cdFx0XHRcdFx0XHQnPGkgY2xhc3M9XCJmYSBmYS1zaGFyZVwiPjwvaT4nICsgJzwvc3Bhbj4nICtcblx0XHRcdFx0XHRcdCc8c3BhbiBjbGFzcz1cImRlbGV0ZS1lbWFpbCBhY3Rpb24taXRlbVwiIHRpdGxlPVwiJyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKFxuXHRcdFx0XHRcdFx0XHQnZGVsZXRlJywgJ2J1dHRvbnMnKSArICdcIj4nICsgJzxpIGNsYXNzPVwiZmEgZmEtdHJhc2gtb1wiPjwvaT4nICsgJzwvc3Bhbj4nICtcblx0XHRcdFx0XHRcdCc8c3BhbiBjbGFzcz1cInByZXZpZXctZW1haWwgYWN0aW9uLWl0ZW1cIiB0aXRsZT1cIidcblx0XHRcdFx0XHRcdCsganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3ByZXZpZXcnLCAnYnV0dG9ucycpICtcblx0XHRcdFx0XHRcdCdcIj4nICtcblx0XHRcdFx0XHRcdCc8aSBjbGFzcz1cImZhIGZhLWV5ZVwiPjwvaT4nICsgJzwvc3Bhbj4nICsgJzwvZGl2Pic7XG5cdFx0XHRcdH0sXG5cdFx0XHRcdFxuXHRcdFx0XHRjb252ZXJ0UGVuZGluZ1RvU3RyaW5nOiBmdW5jdGlvbihkYXRhLCB0eXBlLCByb3csIG1ldGEpIHtcblx0XHRcdFx0XHRyZXR1cm4gKGRhdGFcblx0XHRcdFx0XHQ9PT0gdHJ1ZSkgPyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZW1haWxfcGVuZGluZycsICdlbWFpbHMnKSA6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKFxuXHRcdFx0XHRcdFx0J2VtYWlsX3NlbnQnLCAnZW1haWxzJyk7XG5cdFx0XHRcdH1cblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgTW9kdWxlIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBUb2dnbGUgcm93IHNlbGVjdGlvbiBmb3IgbWFpbiBwYWdlIHRhYmxlLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IENvbnRhaW5zIGV2ZW50IGluZm9ybWF0aW9uLlxuXHRcdCAqL1xuXHRcdHZhciBfb25TZWxlY3RBbGxSb3dzID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdGlmICgkKHRoaXMpLnByb3AoJ2NoZWNrZWQnKSkge1xuXHRcdFx0XHQkdGhpcy5maW5kKCd0Ym9keSAuc2luZ2xlLWNoZWNrYm94JykuYWRkQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHRcdFx0JHRoaXMuZmluZCgndGJvZHkgaW5wdXQ6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQkdGhpcy5maW5kKCd0Ym9keSAuc2luZ2xlLWNoZWNrYm94JykucmVtb3ZlQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHRcdFx0JHRoaXMuZmluZCgndGJvZHkgaW5wdXQ6Y2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgZmFsc2UpO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogV2lsbCBzZW5kIHRoZSBlbWFpbCB0byBpdHMgY29udGFjdHMgKGV2ZW4gaWYgaXRzIHN0YXR1cyBpcyBcInNlbnRcIikuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vblNlbmRFbWFpbCA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgJHJvdyA9ICQodGhpcykucGFyZW50cygndHInKTtcblx0XHRcdFxuXHRcdFx0anNlLmxpYnMubW9kYWwubWVzc2FnZSh7XG5cdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc2VuZCcsICdidXR0b25zJyksXG5cdFx0XHRcdGNvbnRlbnQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdwcm9tcHRfc2VuZF9lbWFpbCcsICdlbWFpbHMnKSxcblx0XHRcdFx0YnV0dG9uczogW1xuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdubycsICdsaWdodGJveF9idXR0b25zJyksXG5cdFx0XHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3llcycsICdsaWdodGJveF9idXR0b25zJyksXG5cdFx0XHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdHZhciBlbWFpbCA9ICRyb3cuZGF0YSgpO1xuXHRcdFx0XHRcdFx0XHRqc2UubGlicy5lbWFpbHMuc2VuZENvbGxlY3Rpb24oW2VtYWlsXSlcblx0XHRcdFx0XHRcdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0XHRcdFx0JHRoaXMuRGF0YVRhYmxlKCkuYWpheC5yZWxvYWQoKTtcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5nZXRBdHRhY2htZW50c1NpemUoJCgnI2F0dGFjaG1lbnRzLXNpemUnKSk7XG5cdFx0XHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHRcdFx0XHQuZmFpbChmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0XHRcdFx0dmFyIHRpdGxlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyk7XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHRcdFx0XHRcdFx0XHR0aXRsZTogdGl0bGUsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdGNvbnRlbnQ6IHJlc3BvbnNlLm1lc3NhZ2Vcblx0XHRcdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdF1cblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGlzcGxheSBtb2RhbCB3aXRoIGVtYWlsIGluZm9ybWF0aW9uIGJ1dCB3aXRob3V0IGNvbnRhY3RzLlxuXHRcdCAqXG5cdFx0ICogVGhlIHVzZXIgd2lsbCBiZSBhYmxlIHRvIHNldCBuZXcgY29udGFjdHMgYW5kIHNlbmQgdGhlIGVtYWlsIChraW5kIG9mIFwiZHVwbGljYXRlXCIgbWV0aG9kKS5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBDb250YWlucyBldmVudCBpbmZvcm1hdGlvbi5cblx0XHQgKi9cblx0XHR2YXIgX29uRm9yd2FyZEVtYWlsID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBlbWFpbCA9ICQodGhpcykucGFyZW50cygndHInKS5kYXRhKCk7XG5cdFx0XHRcblx0XHRcdGpzZS5saWJzLmVtYWlscy5yZXNldE1vZGFsKCRtb2RhbCk7XG5cdFx0XHRqc2UubGlicy5lbWFpbHMubG9hZEVtYWlsT25Nb2RhbChlbWFpbCwgJG1vZGFsKTtcblx0XHRcdFxuXHRcdFx0Ly8gQ2xlYXIgY29udGFjdCBmaWVsZHMgYnV0IGxldCB0aGUgcmVzdCBvZiB0aGUgZGF0YSB1bnRvdWNoZWQuXG5cdFx0XHQkbW9kYWwuZmluZCgnI2VtYWlsLWlkJykudmFsKCcnKTtcblx0XHRcdCRtb2RhbC5maW5kKCcjc2VuZGVyLWVtYWlsLCAjc2VuZGVyLW5hbWUnKS52YWwoJycpO1xuXHRcdFx0JG1vZGFsLmZpbmQoJyNyZXBseS10by1lbWFpbCwgI3JlcGx5LXRvLW5hbWUnKS52YWwoJycpO1xuXHRcdFx0JG1vZGFsLmZpbmQoJyNyZWNpcGllbnQtZW1haWwsICNyZWNpcGllbnQtbmFtZScpLnZhbCgnJyk7XG5cdFx0XHQkbW9kYWwuZmluZCgnI2NvbnRhY3RzLXRhYmxlJykuRGF0YVRhYmxlKCkuY2xlYXIoKS5kcmF3KCk7XG5cdFx0XHRcblx0XHRcdCRtb2RhbC5kaWFsb2coe1xuXHRcdFx0XHR0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2ZvcndhcmQnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHR3aWR0aDogMTAwMCxcblx0XHRcdFx0aGVpZ2h0OiA3NDAsXG5cdFx0XHRcdG1vZGFsOiB0cnVlLFxuXHRcdFx0XHRkaWFsb2dDbGFzczogJ2d4LWNvbnRhaW5lcicsXG5cdFx0XHRcdGNsb3NlT25Fc2NhcGU6IGZhbHNlLFxuXHRcdFx0XHRidXR0b25zOiBqc2UubGlicy5lbWFpbHMuZ2V0RGVmYXVsdE1vZGFsQnV0dG9ucygkbW9kYWwsICR0aGlzKSxcblx0XHRcdFx0b3BlbjoganNlLmxpYnMuZW1haWxzLmNvbG9yaXplQnV0dG9uc0ZvckVkaXRNb2RlXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIERlbGV0ZSBzZWxlY3RlZCByb3cgZW1haWwuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vbkRlbGV0ZUVtYWlsID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciAkcm93ID0gJCh0aGlzKS5wYXJlbnRzKCd0cicpLFxuXHRcdFx0XHRlbWFpbCA9ICRyb3cuZGF0YSgpO1xuXHRcdFx0XG5cdFx0XHRqc2UubGlicy5tb2RhbC5tZXNzYWdlKHtcblx0XHRcdFx0dGl0bGU6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdkZWxldGUnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHRjb250ZW50OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgncHJvbXB0X2RlbGV0ZV9lbWFpbCcsICdlbWFpbHMnKSxcblx0XHRcdFx0YnV0dG9uczogW1xuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdubycsICdsaWdodGJveF9idXR0b25zJyksXG5cdFx0XHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3llcycsICdsaWdodGJveF9idXR0b25zJyksXG5cdFx0XHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5kZWxldGVDb2xsZWN0aW9uKFtlbWFpbF0pXG5cdFx0XHRcdFx0XHRcdFx0LmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHRcdFx0XHRcdCR0aGlzLkRhdGFUYWJsZSgpLmFqYXgucmVsb2FkKCk7XG5cdFx0XHRcdFx0XHRcdFx0XHRqc2UubGlicy5lbWFpbHMuZ2V0QXR0YWNobWVudHNTaXplKCQoJyNhdHRhY2htZW50cy1zaXplJykpO1xuXHRcdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHRcdFx0LmZhaWwoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHRcdFx0XHRcdHZhciB0aXRsZSA9IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdlcnJvcicsICdtZXNzYWdlcycpO1xuXHRcdFx0XHRcdFx0XHRcdFx0anNlLmxpYnMubW9kYWwubWVzc2FnZSh7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdHRpdGxlOiB0aXRsZSxcblx0XHRcdFx0XHRcdFx0XHRcdFx0Y29udGVudDogcmVzcG9uc2UubWVzc2FnZVxuXHRcdFx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH1cblx0XHRcdFx0XVxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBEaXNwbGF5IG1vZGFsIHdpdGggZW1haWwgaW5mb3JtYXRpb25cblx0XHQgKlxuXHRcdCAqIFRoZSB1c2VyIGNhbiBzZWxlY3QgYW4gYWN0aW9uIHRvIHBlcmZvcm0gdXBvbiB0aGUgcHJldmlld2VkIGVtYWlsIChTZW5kLCBGb3J3YXJkLFxuXHRcdCAqIERlbGV0ZSwgQ2xvc2UpLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtICB7b2JqZWN0fSBldmVudCBDb250YWlucyBldmVudCBpbmZvcm1hdGlvbi5cblx0XHQgKi9cblx0XHR2YXIgX29uUHJldmlld0VtYWlsID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdHZhciBlbWFpbCA9ICQodGhpcykucGFyZW50cygndHInKS5kYXRhKCk7XG5cdFx0XHRcblx0XHRcdGpzZS5saWJzLmVtYWlscy5yZXNldE1vZGFsKCRtb2RhbCk7XG5cdFx0XHRqc2UubGlicy5lbWFpbHMubG9hZEVtYWlsT25Nb2RhbChlbWFpbCwgJG1vZGFsKTtcblx0XHRcdFxuXHRcdFx0JG1vZGFsLmRpYWxvZyh7XG5cdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgncHJldmlldycsICdidXR0b25zJyksXG5cdFx0XHRcdHdpZHRoOiAxMDAwLFxuXHRcdFx0XHRoZWlnaHQ6IDc0MCxcblx0XHRcdFx0bW9kYWw6IGZhbHNlLFxuXHRcdFx0XHRkaWFsb2dDbGFzczogJ2d4LWNvbnRhaW5lcicsXG5cdFx0XHRcdGNsb3NlT25Fc2NhcGU6IGZhbHNlLFxuXHRcdFx0XHRidXR0b25zOiBqc2UubGlicy5lbWFpbHMuZ2V0UHJldmlld01vZGFsQnV0dG9ucygkbW9kYWwsICR0aGlzKSxcblx0XHRcdFx0b3BlbjoganNlLmxpYnMuZW1haWxzLmNvbG9yaXplQnV0dG9uc0ZvclByZXZpZXdNb2RlXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIG1vZHVsZSwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICpcblx0XHQgKiBUaGUgZW1haWxzIHRhYmxlIG9wZXJhdGVzIHdpdGggc2VydmVyIHByb2Nlc3NpbmcgYmVjYXVzZSBpdCBpcyBtdWNoIGZhc3RlciBhbmQgZWZmaWNpZW50IHRoYW4gcHJlcGFyaW5nXG5cdFx0ICogYW5kIHNlbmRpbmcgYWxsIHRoZSByZWNvcmRzIGluIGV2ZXJ5IEFKQVggcmVxdWVzdC4gQ2hlY2sgdGhlIEVtYWlscy9EYXRhVGFibGUgY29udHJvbGxlciBtZXRob2QgZm9yXG5cdFx0ICogcmVxdWVzdGVkIGRhdGEgYW5kIHRoZSBmb2xsb3dpbmcgbGluayBmb3IgbW9yZSBpbmZvIGFib3V0IHNlcnZlciBwcm9jZXNzaW5nIGluIERhdGFUYWJsZXMuXG5cdFx0ICpcblx0XHQgKiB7QGxpbmsgaHR0cDovL3d3dy5kYXRhdGFibGVzLm5ldC9tYW51YWwvc2VydmVyLXNpZGV9XG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBDcmVhdGUgYSBEYXRhVGFibGUgaW5zdGFuY2UgZm9yIHRoZSBlbWFpbCByZWNvcmRzLlxuXHRcdFx0anNlLmxpYnMuZGF0YXRhYmxlLmNyZWF0ZSgkdGhpcywge1xuXHRcdFx0XHRwcm9jZXNzaW5nOiBmYWxzZSxcblx0XHRcdFx0c2VydmVyU2lkZTogdHJ1ZSxcblx0XHRcdFx0ZG9tOiAncnRpcCcsXG5cdFx0XHRcdGF1dG9XaWR0aDogZmFsc2UsXG5cdFx0XHRcdGxhbmd1YWdlOiAoanNlLmNvcmUuY29uZmlnLmdldCgnbGFuZ3VhZ2VDb2RlJylcblx0XHRcdFx0PT09ICdkZScpID8ganNlLmxpYnMuZGF0YXRhYmxlLmdldEdlcm1hblRyYW5zbGF0aW9uKCkgOiBudWxsLFxuXHRcdFx0XHRhamF4OiB7XG5cdFx0XHRcdFx0dXJsOiBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPUVtYWlscy9EYXRhVGFibGUnLFxuXHRcdFx0XHRcdHR5cGU6ICdQT1NUJ1xuXHRcdFx0XHR9LFxuXHRcdFx0XHRvcmRlcjogW1syLCAnZGVzYyddXSxcblx0XHRcdFx0cGFnZUxlbmd0aDogMjAsXG5cdFx0XHRcdGNvbHVtbnM6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRkYXRhOiBudWxsLFxuXHRcdFx0XHRcdFx0b3JkZXJhYmxlOiBmYWxzZSxcblx0XHRcdFx0XHRcdGRlZmF1bHRDb250ZW50OiAnPGlucHV0IHR5cGU9XCJjaGVja2JveFwiIGRhdGEtc2luZ2xlX2NoZWNrYm94IC8+Jyxcblx0XHRcdFx0XHRcdHdpZHRoOiAnMiUnLFxuXHRcdFx0XHRcdFx0Y2xhc3NOYW1lOiAnZHQtaGVhZC1jZW50ZXIgZHQtYm9keS1jZW50ZXInXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRkYXRhOiAncm93X2NvdW50Jyxcblx0XHRcdFx0XHRcdG9yZGVyYWJsZTogZmFsc2UsXG5cdFx0XHRcdFx0XHR3aWR0aDogJzMlJyxcblx0XHRcdFx0XHRcdGNsYXNzTmFtZTogJ2R0LWhlYWQtY2VudGVyIGR0LWJvZHktY2VudGVyJ1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0ZGF0YTogJ2NyZWF0aW9uX2RhdGUnLFxuXHRcdFx0XHRcdFx0d2lkdGg6ICcxMiUnXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRkYXRhOiAnc2VudF9kYXRlJyxcblx0XHRcdFx0XHRcdHdpZHRoOiAnMTIlJ1xuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0ZGF0YTogJ3NlbmRlcicsXG5cdFx0XHRcdFx0XHR3aWR0aDogJzEyJSdcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGRhdGE6ICdyZWNpcGllbnQnLFxuXHRcdFx0XHRcdFx0d2lkdGg6ICcxMiUnXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRkYXRhOiAnc3ViamVjdCcsXG5cdFx0XHRcdFx0XHR3aWR0aDogJzI3JSdcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGRhdGE6ICdpc19wZW5kaW5nJyxcblx0XHRcdFx0XHRcdHdpZHRoOiAnOCUnLFxuXHRcdFx0XHRcdFx0cmVuZGVyOiBvcHRpb25zLmNvbnZlcnRQZW5kaW5nVG9TdHJpbmdcblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGRhdGE6IG51bGwsXG5cdFx0XHRcdFx0XHRvcmRlcmFibGU6IGZhbHNlLFxuXHRcdFx0XHRcdFx0ZGVmYXVsdENvbnRlbnQ6ICcnLFxuXHRcdFx0XHRcdFx0cmVuZGVyOiBvcHRpb25zLmVtYWlsc1RhYmxlQWN0aW9ucyxcblx0XHRcdFx0XHRcdHdpZHRoOiAnMTIlJ1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIEFkZCB0YWJsZSBlcnJvciBoYW5kbGVyLlxuXHRcdFx0anNlLmxpYnMuZGF0YXRhYmxlLmVycm9yKCR0aGlzLCBmdW5jdGlvbihldmVudCwgc2V0dGluZ3MsIHRlY2hOb3RlLCBtZXNzYWdlKSB7XG5cdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHRcdHRpdGxlOiAnRGF0YVRhYmxlcyAnICsganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyksXG5cdFx0XHRcdFx0Y29udGVudDogbWVzc2FnZVxuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBBZGQgYWpheCBlcnJvciBoYW5kbGVyLlxuXHRcdFx0anNlLmxpYnMuZGF0YXRhYmxlLmFqYXhDb21wbGV0ZSgkdGhpcywgZnVuY3Rpb24oZXZlbnQsIHNldHRpbmdzLCBqc29uKSB7XG5cdFx0XHRcdGlmIChqc29uLmV4Y2VwdGlvbiA9PT0gdHJ1ZSkge1xuXHRcdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdEYXRhVGFibGVzIFByb2Nlc3NpbmcgRXJyb3InLCAkdGhpcy5nZXQoMCksIGpzb24pO1xuXHRcdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHRcdFx0dGl0bGU6ICdBSkFYICcgKyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLCAnbWVzc2FnZXMnKSxcblx0XHRcdFx0XHRcdGNvbnRlbnQ6IGpzb24ubWVzc2FnZVxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gQ29tYmluZSBcIi5wYWdpbmF0b3JcIiB3aXRoIHRoZSBEYXRhVGFibGUgSFRNTCBvdXRwdXQgaW4gb3JkZXIgdG8gY3JlYXRlIGEgdW5pcXVlIHBhZ2luYXRpb25cblx0XHRcdC8vIGZyYW1lIGF0IHRoZSBib3R0b20gb2YgdGhlIHRhYmxlIChleGVjdXRlZCBhZnRlciB0YWJsZSBpbml0aWFsaXphdGlvbikuXG5cdFx0XHQkdGhpcy5vbignaW5pdC5kdCcsIGZ1bmN0aW9uKGUsIHNldHRpbmdzLCBqc29uKSB7XG5cdFx0XHRcdCQoJy5wYWdpbmF0b3InKS5hcHBlbmRUbygkKCcjZW1haWxzLXRhYmxlX3dyYXBwZXInKSk7XG5cdFx0XHRcdCQoJyNlbWFpbHMtdGFibGVfaW5mbycpXG5cdFx0XHRcdFx0LmFwcGVuZFRvKCQoJy5wYWdpbmF0b3IgLmRhdGF0YWJsZS1jb21wb25lbnRzJykpXG5cdFx0XHRcdFx0LmNzcygnY2xlYXInLCAnbm9uZScpO1xuXHRcdFx0XHQkKCcjZW1haWxzLXRhYmxlX3BhZ2luYXRlJylcblx0XHRcdFx0XHQuYXBwZW5kVG8oJCgnLnBhZ2luYXRvciAuZGF0YXRhYmxlLWNvbXBvbmVudHMnKSlcblx0XHRcdFx0XHQuY3NzKCdjbGVhcicsICdub25lJyk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gUmVjcmVhdGUgdGhlIGNoZWNrYm94IHdpZGdldHMuXG5cdFx0XHQkdGhpcy5vbignZHJhdy5kdCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkdGhpcy5maW5kKCd0Ym9keScpLmF0dHIoJ2RhdGEtZ3gtd2lkZ2V0JywgJ2NoZWNrYm94Jyk7XG5cdFx0XHRcdGd4LndpZGdldHMuaW5pdCgkdGhpcyk7IC8vIEluaXRpYWxpemUgdGhlIGNoZWNrYm94IHdpZGdldC5cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBBZGQgc3Bpbm5lciB0byB0YWJsZSBsb2FkaW5nIGFjdGlvbnMuXG5cdFx0XHR2YXIgJHNwaW5uZXI7XG5cdFx0XHQkdGhpcy5vbigncHJlWGhyLmR0JywgZnVuY3Rpb24oZSwgc2V0dGluZ3MsIGpzb24pIHtcblx0XHRcdFx0JHNwaW5uZXIgPSBqc2UubGlicy5sb2FkaW5nX3NwaW5uZXIuc2hvdygkdGhpcyk7XG5cdFx0XHR9KTtcblx0XHRcdCR0aGlzLm9uKCd4aHIuZHQnLCBmdW5jdGlvbihlLCBzZXR0aW5ncywganNvbikge1xuXHRcdFx0XHRpZiAoJHNwaW5uZXIpIHtcblx0XHRcdFx0XHRqc2UubGlicy5sb2FkaW5nX3NwaW5uZXIuaGlkZSgkc3Bpbm5lcik7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBCaW5kIGV2ZW50IGhhbmRsZXJzIG9mIHRoZSBlbWFpbHMgdGFibGUuXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ2NsaWNrJywgJyNzZWxlY3QtYWxsLXJvd3MnLCBfb25TZWxlY3RBbGxSb3dzKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5zZW5kLWVtYWlsJywgX29uU2VuZEVtYWlsKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5mb3J3YXJkLWVtYWlsJywgX29uRm9yd2FyZEVtYWlsKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgJy5kZWxldGUtZW1haWwnLCBfb25EZWxldGVFbWFpbClcblx0XHRcdFx0Lm9uKCdjbGljaycsICcucHJldmlldy1lbWFpbCcsIF9vblByZXZpZXdFbWFpbCk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
