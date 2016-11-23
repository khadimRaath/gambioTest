'use strict';

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
gx.controllers.module('emails_paginator', [gx.source + '/libs/emails', gx.source + '/libs/button_dropdown', 'loading_spinner', 'modal'],

/** @lends module:Controllers/emails_paginator */

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
	var _onRefreshData = function _onRefreshData(event) {
		$table.DataTable().ajax.reload();
		jse.libs.emails.getAttachmentsSize($attachmentsSize);
	};

	/**
  * Change current page length.
  *
  * @param {object} event Contains the event data.
  */
	var _onTableLengthChange = function _onTableLengthChange(event) {
		var length = $this.find('#display-records').val();
		$table.DataTable().page.len(length).draw();
	};

	/**
  * Open handle attachments modal window.
  *
  * @param {object} event Contains event information.
  */
	var _onHandleAttachments = function _onHandleAttachments(event) {
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
	var _onBulkDelete = function _onBulkDelete(event) {
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
			buttons: [{
				text: jse.core.lang.translate('no', 'lightbox_buttons'),
				click: function click() {
					$(this).dialog('close');
				}
			}, {
				text: jse.core.lang.translate('yes', 'lightbox_buttons'),
				click: function click() {
					jse.libs.emails.deleteCollection(collection).done(function (response) {
						$table.DataTable().ajax.reload();
						jse.libs.emails.getAttachmentsSize($attachmentsSize);
					}).fail(function (response) {
						var title = jse.core.lang.translate('error', 'messages');

						jse.libs.modal.message({
							title: title,
							content: response.message
						});
					});

					$(this).dialog('close');
					$table.find('input[type=checkbox]').prop('checked', false);
				}
			}]
		});
	};

	/**
  * Execute the send operation for the selected email records.
  *
  * @param {object} event Contains the event information.
  */
	var _onBulkSend = function _onBulkSend(event) {
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
			buttons: [{
				text: jse.core.lang.translate('no', 'lightbox_buttons'),
				click: function click() {
					$(this).dialog('close');
				}
			}, {
				text: jse.core.lang.translate('yes', 'lightbox_buttons'),
				click: function click() {
					jse.libs.emails.sendCollection(collection).done(function (response) {
						$table.DataTable().ajax.reload();
						jse.libs.emails.getAttachmentsSize($attachmentsSize);
					}).fail(function (response) {
						var title = jse.core.lang.translate('error', 'messages');

						jse.libs.modal.message({
							title: title,
							content: response.message
						});
					});

					$(this).dialog('close');
					$table.find('input[type=checkbox]').prop('checked', false);
				}
			}]
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the module, called by the engine.
  */
	module.init = function (done) {
		// Bind paginator event handlers.
		$this.on('click', '#refresh-table', _onRefreshData).on('click', '#handle-attachments', _onHandleAttachments).on('change', '#display-records', _onTableLengthChange);

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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImVtYWlscy9lbWFpbHNfcGFnaW5hdG9yLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJHRhYmxlIiwiJGF0dGFjaG1lbnRzU2l6ZSIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vblJlZnJlc2hEYXRhIiwiZXZlbnQiLCJEYXRhVGFibGUiLCJhamF4IiwicmVsb2FkIiwianNlIiwibGlicyIsImVtYWlscyIsImdldEF0dGFjaG1lbnRzU2l6ZSIsIl9vblRhYmxlTGVuZ3RoQ2hhbmdlIiwibGVuZ3RoIiwiZmluZCIsInZhbCIsInBhZ2UiLCJsZW4iLCJkcmF3IiwiX29uSGFuZGxlQXR0YWNobWVudHMiLCIkYXR0YWNobWVudHNNb2RhbCIsImRhdGVwaWNrZXIiLCJtYXhEYXRlIiwiRGF0ZSIsImRvY3VtZW50Iiwibm90IiwiYWRkQ2xhc3MiLCJkaWFsb2ciLCJ0aXRsZSIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwid2lkdGgiLCJtb2RhbCIsImRpYWxvZ0NsYXNzIiwiY2xvc2VPbkVzY2FwZSIsIl9vbkJ1bGtEZWxldGUiLCJjb2xsZWN0aW9uIiwiZ2V0U2VsZWN0ZWRFbWFpbHMiLCJtZXNzYWdlIiwiY29udGVudCIsImJ1dHRvbnMiLCJ0ZXh0IiwiY2xpY2siLCJkZWxldGVDb2xsZWN0aW9uIiwiZG9uZSIsInJlc3BvbnNlIiwiZmFpbCIsInByb3AiLCJfb25CdWxrU2VuZCIsInNlbmRDb2xsZWN0aW9uIiwiaW5pdCIsIm9uIiwiJGRyb3Bkb3duIiwiYnV0dG9uX2Ryb3Bkb3duIiwibWFwQWN0aW9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQ0Msa0JBREQsRUFHQyxDQUNDRixHQUFHRyxNQUFILEdBQVksY0FEYixFQUVDSCxHQUFHRyxNQUFILEdBQVksdUJBRmIsRUFH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dGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGVtYWlsc19wYWdpbmF0b3IuanMgMjAxNS0xMC0xNSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgRW1haWxzIFBhZ2luYXRvciBDb250cm9sbGVyXG4gKlxuICogVGhpcyBjb250cm9sbGVyIHdpbGwgaGFuZGxlIHRoZSBtYWluIHRhYmxlIHBhZ2luYXRvciBvcGVyYXRpb25zIG9mIHRoZSBhZG1pbi9lbWFpbHMgcGFnZS5cbiAqXG4gKiBAbW9kdWxlIENvbnRyb2xsZXJzL2VtYWlsc19wYWdpbmF0b3JcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQnZW1haWxzX3BhZ2luYXRvcicsXG5cdFxuXHRbXG5cdFx0Z3guc291cmNlICsgJy9saWJzL2VtYWlscycsXG5cdFx0Z3guc291cmNlICsgJy9saWJzL2J1dHRvbl9kcm9wZG93bicsXG5cdFx0J2xvYWRpbmdfc3Bpbm5lcicsXG5cdFx0J21vZGFsJ1xuXHRdLFxuXHRcblx0LyoqIEBsZW5kcyBtb2R1bGU6Q29udHJvbGxlcnMvZW1haWxzX3BhZ2luYXRvciAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogVGFibGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGFibGUgPSAkKCcjZW1haWxzLXRhYmxlJyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogQXR0YWNobWVudHMgU2l6ZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRhdHRhY2htZW50c1NpemUgPSAkKCcjYXR0YWNobWVudHMtc2l6ZScpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgTW9kdWxlIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE1vZHVsZSBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUmVmcmVzaCBwYWdlIGRhdGEuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vblJlZnJlc2hEYXRhID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdCR0YWJsZS5EYXRhVGFibGUoKS5hamF4LnJlbG9hZCgpO1xuXHRcdFx0anNlLmxpYnMuZW1haWxzLmdldEF0dGFjaG1lbnRzU2l6ZSgkYXR0YWNobWVudHNTaXplKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIENoYW5nZSBjdXJyZW50IHBhZ2UgbGVuZ3RoLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IENvbnRhaW5zIHRoZSBldmVudCBkYXRhLlxuXHRcdCAqL1xuXHRcdHZhciBfb25UYWJsZUxlbmd0aENoYW5nZSA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgbGVuZ3RoID0gJHRoaXMuZmluZCgnI2Rpc3BsYXktcmVjb3JkcycpLnZhbCgpO1xuXHRcdFx0JHRhYmxlLkRhdGFUYWJsZSgpLnBhZ2UubGVuKGxlbmd0aCkuZHJhdygpO1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogT3BlbiBoYW5kbGUgYXR0YWNobWVudHMgbW9kYWwgd2luZG93LlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IENvbnRhaW5zIGV2ZW50IGluZm9ybWF0aW9uLlxuXHRcdCAqL1xuXHRcdHZhciBfb25IYW5kbGVBdHRhY2htZW50cyA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgJGF0dGFjaG1lbnRzTW9kYWwgPSAkKCcjYXR0YWNobWVudHMtbW9kYWwnKTtcblx0XHRcdFxuXHRcdFx0Ly8gUmVzZXQgbW9kYWwgc3RhdGUuXG5cdFx0XHQkYXR0YWNobWVudHNNb2RhbC5maW5kKCcjcmVtb3ZhbC1kYXRlJykudmFsKCcnKS5kYXRlcGlja2VyKHtcblx0XHRcdFx0bWF4RGF0ZTogbmV3IERhdGUoKVxuXHRcdFx0fSk7XG5cdFx0XHQkKGRvY3VtZW50KS5maW5kKCcudWktZGF0ZXBpY2tlcicpLm5vdCgnLmd4LWNvbnRhaW5lcicpLmFkZENsYXNzKCdneC1jb250YWluZXInKTtcblx0XHRcdFxuXHRcdFx0Ly8gRGlzcGxheSBtb2RhbCB0byB0aGUgdXNlci5cblx0XHRcdCRhdHRhY2htZW50c01vZGFsLmRpYWxvZyh7XG5cdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnaGFuZGxlX2F0dGFjaG1lbnRzJywgJ2VtYWlscycpLFxuXHRcdFx0XHR3aWR0aDogNDAwLFxuXHRcdFx0XHRtb2RhbDogdHJ1ZSxcblx0XHRcdFx0ZGlhbG9nQ2xhc3M6ICdneC1jb250YWluZXInLFxuXHRcdFx0XHRjbG9zZU9uRXNjYXBlOiBmYWxzZVxuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBFeGVjdXRlIHRoZSBkZWxldGUgb3BlcmF0aW9uIGZvciB0aGUgc2VsZWN0ZWQgZW1haWwgcmVjb3Jkcy5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBDb250YWlucyB0aGUgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vbkJ1bGtEZWxldGUgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0Ly8gQ2hlY2sgaWYgdGhlcmUgYXJlIHRhYmxlIHJvd3Mgc2VsZWN0ZWQuXG5cdFx0XHRpZiAoJHRhYmxlLmZpbmQoJ3RyIHRkIGlucHV0OmNoZWNrZWQnKS5sZW5ndGggPT09IDAgfHwgJCgnI2J1bGstYWN0aW9uJykudmFsKCkgPT09ICcnKSB7XG5cdFx0XHRcdHJldHVybjsgLy8gTm8gc2VsZWN0ZWQgcmVjb3JkcywgZXhpdCBtZXRob2QuXG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIEdldCBzZWxlY3RlZCByb3dzIGRhdGEgLSBjcmVhdGUgYSBuZXcgZW1haWwgY29sbGVjdGlvbi5cblx0XHRcdHZhciBjb2xsZWN0aW9uID0ganNlLmxpYnMuZW1haWxzLmdldFNlbGVjdGVkRW1haWxzKCR0YWJsZSk7XG5cdFx0XHRcblx0XHRcdC8vIERpc3BsYXkgY29uZmlybWF0aW9uIG1vZGFsIHRvIHRoZSB1c2VyLlxuXHRcdFx0anNlLmxpYnMubW9kYWwubWVzc2FnZSh7XG5cdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnYnVsa19hY3Rpb24nLCAnYWRtaW5fbGFiZWxzJyksXG5cdFx0XHRcdGNvbnRlbnQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdwcm9tcHRfZGVsZXRlX2NvbGxlY3Rpb24nLCAnZW1haWxzJyksXG5cdFx0XHRcdGJ1dHRvbnM6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnbm8nLCAnbGlnaHRib3hfYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCd5ZXMnLCAnbGlnaHRib3hfYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRqc2UubGlicy5lbWFpbHMuZGVsZXRlQ29sbGVjdGlvbihjb2xsZWN0aW9uKVxuXHRcdFx0XHRcdFx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHQkdGFibGUuRGF0YVRhYmxlKCkuYWpheC5yZWxvYWQoKTtcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5nZXRBdHRhY2htZW50c1NpemUoJGF0dGFjaG1lbnRzU2l6ZSk7XG5cdFx0XHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHRcdFx0XHQuZmFpbChmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0XHRcdFx0dmFyIHRpdGxlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyk7XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHRcdFx0XHRcdFx0XHR0aXRsZTogdGl0bGUsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdGNvbnRlbnQ6IHJlc3BvbnNlLm1lc3NhZ2Vcblx0XHRcdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHRcdCR0YWJsZS5maW5kKCdpbnB1dFt0eXBlPWNoZWNrYm94XScpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRdXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEV4ZWN1dGUgdGhlIHNlbmQgb3BlcmF0aW9uIGZvciB0aGUgc2VsZWN0ZWQgZW1haWwgcmVjb3Jkcy5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBDb250YWlucyB0aGUgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vbkJ1bGtTZW5kID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdC8vIENoZWNrIGlmIHRoZXJlIGFyZSB0YWJsZSByb3dzIHNlbGVjdGVkLlxuXHRcdFx0aWYgKCR0YWJsZS5maW5kKCd0ciB0ZCBpbnB1dDpjaGVja2VkJykubGVuZ3RoID09PSAwIHx8ICQoJyNidWxrLWFjdGlvbicpLnZhbCgpID09PSAnJykge1xuXHRcdFx0XHRyZXR1cm47IC8vIE5vIHNlbGVjdGVkIHJlY29yZHMsIGV4aXQgbWV0aG9kLlxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBHZXQgc2VsZWN0ZWQgcm93cyBkYXRhIC0gY3JlYXRlIGEgbmV3IGVtYWlsIGNvbGxlY3Rpb24uXG5cdFx0XHR2YXIgY29sbGVjdGlvbiA9IGpzZS5saWJzLmVtYWlscy5nZXRTZWxlY3RlZEVtYWlscygkdGFibGUpO1xuXHRcdFx0XG5cdFx0XHQvLyBEaXNwbGF5IGNvbmZpcm1hdGlvbiBtb2RhbCB0byB0aGUgdXNlci5cblx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHR0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2J1bGtfYWN0aW9uJywgJ2FkbWluX2xhYmVscycpLFxuXHRcdFx0XHRjb250ZW50OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgncHJvbXB0X3NlbmRfY29sbGVjdGlvbicsICdlbWFpbHMnKSxcblx0XHRcdFx0YnV0dG9uczogW1xuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdubycsICdsaWdodGJveF9idXR0b25zJyksXG5cdFx0XHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0dGV4dDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3llcycsICdsaWdodGJveF9idXR0b25zJyksXG5cdFx0XHRcdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5zZW5kQ29sbGVjdGlvbihjb2xsZWN0aW9uKVxuXHRcdFx0XHRcdFx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHQkdGFibGUuRGF0YVRhYmxlKCkuYWpheC5yZWxvYWQoKTtcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5nZXRBdHRhY2htZW50c1NpemUoJGF0dGFjaG1lbnRzU2l6ZSk7XG5cdFx0XHRcdFx0XHRcdFx0fSlcblx0XHRcdFx0XHRcdFx0XHQuZmFpbChmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0XHRcdFx0dmFyIHRpdGxlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyk7XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xuXHRcdFx0XHRcdFx0XHRcdFx0XHR0aXRsZTogdGl0bGUsXG5cdFx0XHRcdFx0XHRcdFx0XHRcdGNvbnRlbnQ6IHJlc3BvbnNlLm1lc3NhZ2Vcblx0XHRcdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHRcdCR0YWJsZS5maW5kKCdpbnB1dFt0eXBlPWNoZWNrYm94XScpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRdXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIG1vZHVsZSwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBCaW5kIHBhZ2luYXRvciBldmVudCBoYW5kbGVycy5cblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5vbignY2xpY2snLCAnI3JlZnJlc2gtdGFibGUnLCBfb25SZWZyZXNoRGF0YSlcblx0XHRcdFx0Lm9uKCdjbGljaycsICcjaGFuZGxlLWF0dGFjaG1lbnRzJywgX29uSGFuZGxlQXR0YWNobWVudHMpXG5cdFx0XHRcdC5vbignY2hhbmdlJywgJyNkaXNwbGF5LXJlY29yZHMnLCBfb25UYWJsZUxlbmd0aENoYW5nZSk7XG5cdFx0XHRcblx0XHRcdHZhciAkZHJvcGRvd24gPSAkdGhpcy5maW5kKCcuYnVsay1hY3Rpb24nKTtcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5tYXBBY3Rpb24oJGRyb3Bkb3duLCAnYnVsa19zZW5kX3NlbGVjdGVkJywgJ2VtYWlscycsIF9vbkJ1bGtTZW5kKTtcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5tYXBBY3Rpb24oJGRyb3Bkb3duLCAnYnVsa19kZWxldGVfc2VsZWN0ZWQnLCAnZW1haWxzJywgX29uQnVsa0RlbGV0ZSk7XG5cdFx0XHRcblx0XHRcdC8vIEdldCBjdXJyZW50IGF0dGFjaG1lbnRzIHNpemUuXG5cdFx0XHRqc2UubGlicy5lbWFpbHMuZ2V0QXR0YWNobWVudHNTaXplKCRhdHRhY2htZW50c1NpemUpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gbW9kdWxlIG9iamVjdCB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
