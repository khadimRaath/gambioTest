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
 * ## Attachments Modal Controller
 *
 * This controller will handle the attachments modal dialog operations of the admin/emails page.
 *
 * @module Controllers/attachments_modal
 */
gx.controllers.module('attachments_modal', ['modal', gx.source + '/libs/emails'],

/** @lends module:Controllers/attachments_modal */

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
	var _onDeleteOldAttachments = function _onDeleteOldAttachments(event) {
		// Validate selected date before making the request.
		if ($this.find('#removal-date').val() === '') {
			return; // do not proceed
		}

		// Display confirmation modal before proceeding.
		var modalOptions = {
			title: jse.core.lang.translate('delete', 'buttons') + ' - ' + Date.parse($this.find('#removal-date').datepicker('getDate')).toString('dd.MM.yyyy'),
			content: jse.core.lang.translate('prompt_delete_old_attachments', 'emails'),
			buttons: [{
				text: jse.core.lang.translate('no', 'lightbox_buttons'),
				click: function click() {
					$(this).dialog('close');
				}
			}, {
				text: jse.core.lang.translate('yes', 'lightbox_buttons'),
				click: function click() {
					jse.libs.emails.deleteOldAttachments($('#removal-date').datepicker('getDate')).done(function (response) {
						var size = response.size.megabytes !== 0 ? response.size.megabytes + ' Megabytes' : response.size.bytes + ' Bytes';

						var message = jse.core.lang.translate('message_delete_old_attachments_success', 'emails') + '<br/>' + jse.core.lang.translate('count', 'admin_labels') + ': ' + response.count + ', ' + jse.core.lang.translate('size', 'db_backup') + ': ' + size;

						jse.libs.modal.message({
							title: 'Info',
							content: message
						});

						jse.libs.emails.getAttachmentsSize($('#attachments-size'));
						$table.DataTable().ajax.reload();
						$this.dialog('close');
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
		};

		jse.libs.modal.message(modalOptions);
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the module, called by the engine.
  */
	module.init = function (done) {
		$this.on('click', '#delete-old-attachments', _onDeleteOldAttachments);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImVtYWlscy9hdHRhY2htZW50c19tb2RhbC5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR0YWJsZSIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vbkRlbGV0ZU9sZEF0dGFjaG1lbnRzIiwiZXZlbnQiLCJmaW5kIiwidmFsIiwibW9kYWxPcHRpb25zIiwidGl0bGUiLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsIkRhdGUiLCJwYXJzZSIsImRhdGVwaWNrZXIiLCJ0b1N0cmluZyIsImNvbnRlbnQiLCJidXR0b25zIiwidGV4dCIsImNsaWNrIiwiZGlhbG9nIiwibGlicyIsImVtYWlscyIsImRlbGV0ZU9sZEF0dGFjaG1lbnRzIiwiZG9uZSIsInJlc3BvbnNlIiwic2l6ZSIsIm1lZ2FieXRlcyIsImJ5dGVzIiwibWVzc2FnZSIsImNvdW50IiwibW9kYWwiLCJnZXRBdHRhY2htZW50c1NpemUiLCJEYXRhVGFibGUiLCJhamF4IiwicmVsb2FkIiwiZmFpbCIsImluaXQiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLG1CQURELEVBR0MsQ0FDQyxPQURELEVBRUNGLEdBQUdHLE1BQUgsR0FBWSxjQUZiLENBSEQ7O0FBUUM7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFVBQVNELEVBQUUsZUFBRixDQWJWOzs7QUFlQzs7Ozs7QUFLQUUsWUFBVyxFQXBCWjs7O0FBc0JDOzs7OztBQUtBQyxXQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSixJQUE3QixDQTNCWDs7O0FBNkJD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QW5DRixJQVBRO0FBSlMsR0FBbkI7O0FBbURBWixNQUFJYSxJQUFKLENBQVNVLEtBQVQsQ0FBZUYsT0FBZixDQUF1QnZCLFlBQXZCO0FBQ0EsRUEzREQ7O0FBNkRBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FiLFFBQU80QyxJQUFQLEdBQWMsVUFBU2IsSUFBVCxFQUFlO0FBQzVCNUIsUUFBTTBDLEVBQU4sQ0FBUyxPQUFULEVBQWtCLHlCQUFsQixFQUE2Q3BDLHVCQUE3QztBQUNBc0I7QUFDQSxFQUhEOztBQUtBLFFBQU8vQixNQUFQO0FBQ0EsQ0F6SUYiLCJmaWxlIjoiZW1haWxzL2F0dGFjaG1lbnRzX21vZGFsLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBlbWFpbHNfbW9kYWwuanMgMjAxNS0xMC0xNSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBBdHRhY2htZW50cyBNb2RhbCBDb250cm9sbGVyXG4gKlxuICogVGhpcyBjb250cm9sbGVyIHdpbGwgaGFuZGxlIHRoZSBhdHRhY2htZW50cyBtb2RhbCBkaWFsb2cgb3BlcmF0aW9ucyBvZiB0aGUgYWRtaW4vZW1haWxzIHBhZ2UuXG4gKlxuICogQG1vZHVsZSBDb250cm9sbGVycy9hdHRhY2htZW50c19tb2RhbFxuICovXG5neC5jb250cm9sbGVycy5tb2R1bGUoXG5cdCdhdHRhY2htZW50c19tb2RhbCcsXG5cdFxuXHRbXG5cdFx0J21vZGFsJyxcblx0XHRneC5zb3VyY2UgKyAnL2xpYnMvZW1haWxzJ1xuXHRdLFxuXHRcblx0LyoqIEBsZW5kcyBtb2R1bGU6Q29udHJvbGxlcnMvYXR0YWNobWVudHNfbW9kYWwgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEVtYWlscyBNYWluIFRhYmxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRhYmxlID0gJCgnI2VtYWlscy10YWJsZScpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgTW9kdWxlIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE1vZHVsZSBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogRGVsZXRlIG9sZCBhdHRhY2htZW50cyByZXF1ZXN0LlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9IGV2ZW50IENvbnRhaW5zIHRoZSBldmVudCBpbmZvcm1hdGlvbi5cblx0XHQgKi9cblx0XHR2YXIgX29uRGVsZXRlT2xkQXR0YWNobWVudHMgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0Ly8gVmFsaWRhdGUgc2VsZWN0ZWQgZGF0ZSBiZWZvcmUgbWFraW5nIHRoZSByZXF1ZXN0LlxuXHRcdFx0aWYgKCR0aGlzLmZpbmQoJyNyZW1vdmFsLWRhdGUnKS52YWwoKSA9PT0gJycpIHtcblx0XHRcdFx0cmV0dXJuOyAvLyBkbyBub3QgcHJvY2VlZFxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBEaXNwbGF5IGNvbmZpcm1hdGlvbiBtb2RhbCBiZWZvcmUgcHJvY2VlZGluZy5cblx0XHRcdHZhciBtb2RhbE9wdGlvbnMgPSB7XG5cdFx0XHRcdHRpdGxlOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZGVsZXRlJywgJ2J1dHRvbnMnKSArICcgLSAnXG5cdFx0XHRcdCsgRGF0ZS5wYXJzZSgkdGhpcy5maW5kKCcjcmVtb3ZhbC1kYXRlJykuZGF0ZXBpY2tlcignZ2V0RGF0ZScpKS50b1N0cmluZygnZGQuTU0ueXl5eScpLFxuXHRcdFx0XHRjb250ZW50OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgncHJvbXB0X2RlbGV0ZV9vbGRfYXR0YWNobWVudHMnLCAnZW1haWxzJyksXG5cdFx0XHRcdGJ1dHRvbnM6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnbm8nLCAnbGlnaHRib3hfYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCd5ZXMnLCAnbGlnaHRib3hfYnV0dG9ucycpLFxuXHRcdFx0XHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRqc2UubGlicy5lbWFpbHMuZGVsZXRlT2xkQXR0YWNobWVudHMoJCgnI3JlbW92YWwtZGF0ZScpXG5cdFx0XHRcdFx0XHRcdFx0LmRhdGVwaWNrZXIoJ2dldERhdGUnKSlcblx0XHRcdFx0XHRcdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdFx0XHRcdFx0dmFyIHNpemUgPSAocmVzcG9uc2Uuc2l6ZS5tZWdhYnl0ZXMgIT09IDApXG5cdFx0XHRcdFx0XHRcdFx0XHRcdD8gcmVzcG9uc2Uuc2l6ZS5tZWdhYnl0ZXMgKyAnIE1lZ2FieXRlcydcblx0XHRcdFx0XHRcdFx0XHRcdFx0OiByZXNwb25zZS5zaXplLmJ5dGVzICsgJyBCeXRlcyc7XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdHZhciBtZXNzYWdlID1cblx0XHRcdFx0XHRcdFx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ21lc3NhZ2VfZGVsZXRlX29sZF9hdHRhY2htZW50c19zdWNjZXNzJywgJ2VtYWlscycpXG5cdFx0XHRcdFx0XHRcdFx0XHRcdCsgJzxici8+JyArIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjb3VudCcsICdhZG1pbl9sYWJlbHMnKSArICc6ICdcblx0XHRcdFx0XHRcdFx0XHRcdFx0KyByZXNwb25zZS5jb3VudCArICcsICcgKyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc2l6ZScsICdkYl9iYWNrdXAnKSArICc6ICdcblx0XHRcdFx0XHRcdFx0XHRcdFx0KyBzaXplO1xuXHRcdFx0XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRcdFx0XHRqc2UubGlicy5tb2RhbC5tZXNzYWdlKHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0dGl0bGU6ICdJbmZvJyxcblx0XHRcdFx0XHRcdFx0XHRcdFx0Y29udGVudDogbWVzc2FnZVxuXHRcdFx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdGpzZS5saWJzLmVtYWlscy5nZXRBdHRhY2htZW50c1NpemUoJCgnI2F0dGFjaG1lbnRzLXNpemUnKSk7XG5cdFx0XHRcdFx0XHRcdFx0XHQkdGFibGUuRGF0YVRhYmxlKCkuYWpheC5yZWxvYWQoKTtcblx0XHRcdFx0XHRcdFx0XHRcdCR0aGlzLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHR2YXIgdGl0bGUgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLCAnbWVzc2FnZXMnKTtcblx0XHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdFx0anNlLmxpYnMubW9kYWwubWVzc2FnZSh7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdHRpdGxlOiB0aXRsZSxcblx0XHRcdFx0XHRcdFx0XHRcdFx0Y29udGVudDogcmVzcG9uc2UubWVzc2FnZVxuXHRcdFx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9XG5cdFx0XHRcdF1cblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2UobW9kYWxPcHRpb25zKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIG1vZHVsZSwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkdGhpcy5vbignY2xpY2snLCAnI2RlbGV0ZS1vbGQtYXR0YWNobWVudHMnLCBfb25EZWxldGVPbGRBdHRhY2htZW50cyk7XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
