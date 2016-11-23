'use strict';

/* --------------------------------------------------------------
 emails_toolbar.js 2016-02-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Emails Toolbar Controller
 *
 * This controller will handle the main toolbar operations of the admin/emails page.
 *
 * @module Controllers/emails_toolbar
 */
gx.controllers.module('emails_toolbar', [gx.source + '/libs/emails', 'url_arguments'],

/** @lends module:Controllers/emails_toolbar */

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
  * Modal Selector
  *
  * @type {object}
  */
	$modal = $('#emails-modal'),


	/**
  * Table Selector
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
	module = {
		model: {
			settings: jse.core.config.get('appUrl') + '/admin/admin.php?do=Emails/GetEmailSettings'
		}
	};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * Display create new email modal.
  *
  * @param {object} event Contains event information.
  */
	var _onCreateNewEmail = function _onCreateNewEmail(event) {
		// Reset modal elements to initial state.
		jse.libs.emails.resetModal($modal);

		// Apply Email Settings to the Email Modal
		if (typeof module.model.settings !== 'undefined' && module.model.settings !== null) {
			// Set the email signature (if not empty). We'll only set the signature to the CKEditor because
			// if the signature contains HTML markup it cannot be sanitized properly for the plain content.
			if (module.model.settings.signature !== null && module.model.settings.signature !== '') {
				var signatureHtml = '<br><p>-----<br>' + module.model.settings.signature.replace('\n/g', '<br>') + '</p>';
				CKEDITOR.instances['content-html'].setData(signatureHtml);
				var signaturePlain = '\n\n-----\n' + module.model.settings.signature.replace(/(<([^>]+)>)/gi, '');
				$modal.find('#content-plain').val(signaturePlain);
			}

			// Disable the HTML content if the shop uses only plain content for the emails.
			if (module.model.settings.useHtml === false) {
				$modal.find('.content').find('.tab-headline:eq(0), .tab-content:eq(0)').hide();
				$modal.find('.content').find('.tab-headline:eq(1)').trigger('click');
			}

			// Preload sender and reply to contact data if provided.
			if (typeof module.model.settings.replyAddress !== 'undefined' && module.model.settings.replyAddress !== '') {
				$modal.find('#sender-email, #reply-to-email').val(module.model.settings.replyAddress);
			}
			if (typeof module.model.settings.replyName !== 'undefined' && module.model.settings.replyName !== '') {
				$modal.find('#sender-name, #reply-to-name').val(module.model.settings.replyName);
			}
		}

		// Prepare and display new modal window.
		$modal.dialog({
			title: jse.core.lang.translate('new_mail', 'buttons'),
			width: 1000,
			height: 740,
			modal: false,
			dialogClass: 'gx-container',
			closeOnEscape: false,
			buttons: jse.libs.emails.getDefaultModalButtons($modal, $table),
			open: jse.libs.emails.colorizeButtonsForEditMode
		});
	};

	/**
  * Perform search request on the DataTable instance.
  *
  * @param {object} event Contains the event data.
  */
	var _onTableSearchSubmit = function _onTableSearchSubmit(event) {
		event.preventDefault();
		var keyword = $this.find('#search-keyword').val();
		$table.DataTable().search(keyword).draw();
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the module, called by the engine.
  */
	module.init = function (done) {
		// Set default "#bulk-action" value.
		$this.find('#bulk-action').val('');

		// Bind Event Handlers
		$this.on('click', '#create-new-email', _onCreateNewEmail).on('submit', '#quick-search', _onTableSearchSubmit);

		// Check if the "mail_to" parameter is present and process its value within the new email modal layer.
		var getParameters = jse.libs.url_arguments.getUrlParameters();
		if (typeof getParameters.mailto !== 'undefined') {
			_onCreateNewEmail({}); // Display the new email modal.
			$modal.find('#recipient-email').val(getParameters.mailto);
		}

		done();
	};

	// Return module object to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImVtYWlscy9lbWFpbHNfdG9vbGJhci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRtb2RhbCIsIiR0YWJsZSIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIm1vZGVsIiwic2V0dGluZ3MiLCJqc2UiLCJjb3JlIiwiY29uZmlnIiwiZ2V0IiwiX29uQ3JlYXRlTmV3RW1haWwiLCJldmVudCIsImxpYnMiLCJlbWFpbHMiLCJyZXNldE1vZGFsIiwic2lnbmF0dXJlIiwic2lnbmF0dXJlSHRtbCIsInJlcGxhY2UiLCJDS0VESVRPUiIsImluc3RhbmNlcyIsInNldERhdGEiLCJzaWduYXR1cmVQbGFpbiIsImZpbmQiLCJ2YWwiLCJ1c2VIdG1sIiwiaGlkZSIsInRyaWdnZXIiLCJyZXBseUFkZHJlc3MiLCJyZXBseU5hbWUiLCJkaWFsb2ciLCJ0aXRsZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJ3aWR0aCIsImhlaWdodCIsIm1vZGFsIiwiZGlhbG9nQ2xhc3MiLCJjbG9zZU9uRXNjYXBlIiwiYnV0dG9ucyIsImdldERlZmF1bHRNb2RhbEJ1dHRvbnMiLCJvcGVuIiwiY29sb3JpemVCdXR0b25zRm9yRWRpdE1vZGUiLCJfb25UYWJsZVNlYXJjaFN1Ym1pdCIsInByZXZlbnREZWZhdWx0Iiwia2V5d29yZCIsIkRhdGFUYWJsZSIsInNlYXJjaCIsImRyYXciLCJpbml0IiwiZG9uZSIsIm9uIiwiZ2V0UGFyYW1ldGVycyIsInVybF9hcmd1bWVudHMiLCJnZXRVcmxQYXJhbWV0ZXJzIiwibWFpbHRvIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQ0MsZ0JBREQsRUFHQyxDQUNDRixHQUFHRyxNQUFILEdBQVksY0FEYixFQUVDLGVBRkQsQ0FIRDs7QUFRQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FB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LEVBQWdDQyxHQUFoQyxDQUFvQzRCLGNBQWNHLE1BQWxEO0FBQ0E7O0FBRURMO0FBQ0EsRUFqQkQ7O0FBbUJBO0FBQ0EsUUFBT3ZELE1BQVA7QUFDQSxDQWhLRiIsImZpbGUiOiJlbWFpbHMvZW1haWxzX3Rvb2xiYXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGVtYWlsc190b29sYmFyLmpzIDIwMTYtMDItMDNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIEVtYWlscyBUb29sYmFyIENvbnRyb2xsZXJcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgd2lsbCBoYW5kbGUgdGhlIG1haW4gdG9vbGJhciBvcGVyYXRpb25zIG9mIHRoZSBhZG1pbi9lbWFpbHMgcGFnZS5cbiAqXG4gKiBAbW9kdWxlIENvbnRyb2xsZXJzL2VtYWlsc190b29sYmFyXG4gKi9cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcblx0J2VtYWlsc190b29sYmFyJyxcblx0XG5cdFtcblx0XHRneC5zb3VyY2UgKyAnL2xpYnMvZW1haWxzJyxcblx0XHQndXJsX2FyZ3VtZW50cydcblx0XSxcblx0XG5cdC8qKiBAbGVuZHMgbW9kdWxlOkNvbnRyb2xsZXJzL2VtYWlsc190b29sYmFyICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2RhbCBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRtb2RhbCA9ICQoJyNlbWFpbHMtbW9kYWwnKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBUYWJsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0YWJsZSA9ICQoJyNlbWFpbHMtdGFibGUnKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE1vZHVsZSBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBNb2R1bGUgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7XG5cdFx0XHRcdG1vZGVsOiB7XG5cdFx0XHRcdFx0c2V0dGluZ3M6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89RW1haWxzL0dldEVtYWlsU2V0dGluZ3MnXG5cdFx0XHRcdH1cblx0XHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBEaXNwbGF5IGNyZWF0ZSBuZXcgZW1haWwgbW9kYWwuXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge29iamVjdH0gZXZlbnQgQ29udGFpbnMgZXZlbnQgaW5mb3JtYXRpb24uXG5cdFx0ICovXG5cdFx0dmFyIF9vbkNyZWF0ZU5ld0VtYWlsID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdC8vIFJlc2V0IG1vZGFsIGVsZW1lbnRzIHRvIGluaXRpYWwgc3RhdGUuXG5cdFx0XHRqc2UubGlicy5lbWFpbHMucmVzZXRNb2RhbCgkbW9kYWwpO1xuXHRcdFx0XG5cdFx0XHQvLyBBcHBseSBFbWFpbCBTZXR0aW5ncyB0byB0aGUgRW1haWwgTW9kYWxcblx0XHRcdGlmICh0eXBlb2YgbW9kdWxlLm1vZGVsLnNldHRpbmdzICE9PSAndW5kZWZpbmVkJyAmJiBtb2R1bGUubW9kZWwuc2V0dGluZ3MgIT09IG51bGwpIHtcblx0XHRcdFx0Ly8gU2V0IHRoZSBlbWFpbCBzaWduYXR1cmUgKGlmIG5vdCBlbXB0eSkuIFdlJ2xsIG9ubHkgc2V0IHRoZSBzaWduYXR1cmUgdG8gdGhlIENLRWRpdG9yIGJlY2F1c2Vcblx0XHRcdFx0Ly8gaWYgdGhlIHNpZ25hdHVyZSBjb250YWlucyBIVE1MIG1hcmt1cCBpdCBjYW5ub3QgYmUgc2FuaXRpemVkIHByb3Blcmx5IGZvciB0aGUgcGxhaW4gY29udGVudC5cblx0XHRcdFx0aWYgKG1vZHVsZS5tb2RlbC5zZXR0aW5ncy5zaWduYXR1cmUgIT09IG51bGwgJiYgbW9kdWxlLm1vZGVsLnNldHRpbmdzLnNpZ25hdHVyZSAhPT0gJycpIHtcblx0XHRcdFx0XHR2YXIgc2lnbmF0dXJlSHRtbCA9ICc8YnI+PHA+LS0tLS08YnI+JyBcblx0XHRcdFx0XHRcdCsgbW9kdWxlLm1vZGVsLnNldHRpbmdzLnNpZ25hdHVyZS5yZXBsYWNlKCdcXG4vZycsICc8YnI+JykgKyAnPC9wPic7XG5cdFx0XHRcdFx0Q0tFRElUT1IuaW5zdGFuY2VzWydjb250ZW50LWh0bWwnXS5zZXREYXRhKHNpZ25hdHVyZUh0bWwpO1xuXHRcdFx0XHRcdHZhciBzaWduYXR1cmVQbGFpbiA9ICdcXG5cXG4tLS0tLVxcbicgKyBtb2R1bGUubW9kZWwuc2V0dGluZ3Muc2lnbmF0dXJlLnJlcGxhY2UoLyg8KFtePl0rKT4pL2dpLFxuXHRcdFx0XHRcdFx0XHQnJyk7XG5cdFx0XHRcdFx0JG1vZGFsLmZpbmQoJyNjb250ZW50LXBsYWluJykudmFsKHNpZ25hdHVyZVBsYWluKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0Ly8gRGlzYWJsZSB0aGUgSFRNTCBjb250ZW50IGlmIHRoZSBzaG9wIHVzZXMgb25seSBwbGFpbiBjb250ZW50IGZvciB0aGUgZW1haWxzLlxuXHRcdFx0XHRpZiAobW9kdWxlLm1vZGVsLnNldHRpbmdzLnVzZUh0bWwgPT09IGZhbHNlKSB7XG5cdFx0XHRcdFx0JG1vZGFsLmZpbmQoJy5jb250ZW50JykuZmluZCgnLnRhYi1oZWFkbGluZTplcSgwKSwgLnRhYi1jb250ZW50OmVxKDApJykuaGlkZSgpO1xuXHRcdFx0XHRcdCRtb2RhbC5maW5kKCcuY29udGVudCcpLmZpbmQoJy50YWItaGVhZGxpbmU6ZXEoMSknKS50cmlnZ2VyKCdjbGljaycpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBQcmVsb2FkIHNlbmRlciBhbmQgcmVwbHkgdG8gY29udGFjdCBkYXRhIGlmIHByb3ZpZGVkLlxuXHRcdFx0XHRpZiAodHlwZW9mIG1vZHVsZS5tb2RlbC5zZXR0aW5ncy5yZXBseUFkZHJlc3MgIT09ICd1bmRlZmluZWQnICYmIG1vZHVsZS5tb2RlbC5zZXR0aW5ncy5yZXBseUFkZHJlc3MgIT09XG5cdFx0XHRcdFx0JycpIHtcblx0XHRcdFx0XHQkbW9kYWwuZmluZCgnI3NlbmRlci1lbWFpbCwgI3JlcGx5LXRvLWVtYWlsJykudmFsKG1vZHVsZS5tb2RlbC5zZXR0aW5ncy5yZXBseUFkZHJlc3MpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGlmICh0eXBlb2YgbW9kdWxlLm1vZGVsLnNldHRpbmdzLnJlcGx5TmFtZSAhPT0gJ3VuZGVmaW5lZCcgJiYgbW9kdWxlLm1vZGVsLnNldHRpbmdzLnJlcGx5TmFtZSAhPT1cblx0XHRcdFx0XHQnJykge1xuXHRcdFx0XHRcdCRtb2RhbC5maW5kKCcjc2VuZGVyLW5hbWUsICNyZXBseS10by1uYW1lJykudmFsKG1vZHVsZS5tb2RlbC5zZXR0aW5ncy5yZXBseU5hbWUpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFByZXBhcmUgYW5kIGRpc3BsYXkgbmV3IG1vZGFsIHdpbmRvdy5cblx0XHRcdCRtb2RhbC5kaWFsb2coe1xuXHRcdFx0XHR0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ25ld19tYWlsJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0d2lkdGg6IDEwMDAsXG5cdFx0XHRcdGhlaWdodDogNzQwLFxuXHRcdFx0XHRtb2RhbDogZmFsc2UsXG5cdFx0XHRcdGRpYWxvZ0NsYXNzOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0Y2xvc2VPbkVzY2FwZTogZmFsc2UsXG5cdFx0XHRcdGJ1dHRvbnM6IGpzZS5saWJzLmVtYWlscy5nZXREZWZhdWx0TW9kYWxCdXR0b25zKCRtb2RhbCwgJHRhYmxlKSxcblx0XHRcdFx0b3BlbjoganNlLmxpYnMuZW1haWxzLmNvbG9yaXplQnV0dG9uc0ZvckVkaXRNb2RlXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFBlcmZvcm0gc2VhcmNoIHJlcXVlc3Qgb24gdGhlIERhdGFUYWJsZSBpbnN0YW5jZS5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBDb250YWlucyB0aGUgZXZlbnQgZGF0YS5cblx0XHQgKi9cblx0XHR2YXIgX29uVGFibGVTZWFyY2hTdWJtaXQgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdHZhciBrZXl3b3JkID0gJHRoaXMuZmluZCgnI3NlYXJjaC1rZXl3b3JkJykudmFsKCk7XG5cdFx0XHQkdGFibGUuRGF0YVRhYmxlKCkuc2VhcmNoKGtleXdvcmQpLmRyYXcoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIG1vZHVsZSwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBTZXQgZGVmYXVsdCBcIiNidWxrLWFjdGlvblwiIHZhbHVlLlxuXHRcdFx0JHRoaXMuZmluZCgnI2J1bGstYWN0aW9uJykudmFsKCcnKTtcblx0XHRcdFxuXHRcdFx0Ly8gQmluZCBFdmVudCBIYW5kbGVyc1xuXHRcdFx0JHRoaXNcblx0XHRcdFx0Lm9uKCdjbGljaycsICcjY3JlYXRlLW5ldy1lbWFpbCcsIF9vbkNyZWF0ZU5ld0VtYWlsKVxuXHRcdFx0XHQub24oJ3N1Ym1pdCcsICcjcXVpY2stc2VhcmNoJywgX29uVGFibGVTZWFyY2hTdWJtaXQpO1xuXHRcdFx0XG5cdFx0XHQvLyBDaGVjayBpZiB0aGUgXCJtYWlsX3RvXCIgcGFyYW1ldGVyIGlzIHByZXNlbnQgYW5kIHByb2Nlc3MgaXRzIHZhbHVlIHdpdGhpbiB0aGUgbmV3IGVtYWlsIG1vZGFsIGxheWVyLlxuXHRcdFx0dmFyIGdldFBhcmFtZXRlcnMgPSBqc2UubGlicy51cmxfYXJndW1lbnRzLmdldFVybFBhcmFtZXRlcnMoKTtcblx0XHRcdGlmICh0eXBlb2YgZ2V0UGFyYW1ldGVycy5tYWlsdG8gIT09ICd1bmRlZmluZWQnKSB7XG5cdFx0XHRcdF9vbkNyZWF0ZU5ld0VtYWlsKHt9KTsgLy8gRGlzcGxheSB0aGUgbmV3IGVtYWlsIG1vZGFsLlxuXHRcdFx0XHQkbW9kYWwuZmluZCgnI3JlY2lwaWVudC1lbWFpbCcpLnZhbChnZXRQYXJhbWV0ZXJzLm1haWx0byk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBtb2R1bGUgb2JqZWN0IHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
