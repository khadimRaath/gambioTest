'use strict';

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
gx.controllers.module('email_invoice', ['modal'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {
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
		var getParams = {
			oID: $this.data('orderId'),
			type: 'invoice',
			mail: '1',
			gm_quick_mail: '1'
		};
		var url = jse.core.config.get('appUrl') + '/admin/gm_pdf_order.php?' + $.param(getParams);
		var data = {
			gm_mail: module.bindings.emailAddress.get(),
			gm_subject: module.bindings.subject.get()
		};
		var $sendButton = $(event.target);

		$sendButton.addClass('disabled').prop('disabled', true);

		$.ajax({
			url: url,
			data: data,
			method: 'POST'
		}).done(function (response) {
			var message = jse.core.lang.translate('MAIL_SUCCESS', 'gm_send_order');

			$('.orders .table-main').DataTable().ajax.reload();
			$('.orders .table-main').orders_overview_filter('reload');

			// Show success message in the admin info box.
			jse.libs.info_box.service.addSuccessMessage(message);
		}).fail(function (jqxhr, textStatus, errorThrown) {
			var title = jse.core.lang.translate('error', 'messages');
			var content = jse.core.lang.translate('MAIL_UNSUCCESS', 'gm_send_order');

			// Show error message in a modal.
			jse.libs.modal.message({ title: title, content: content });
		}).always(function () {
			$this.modal('hide');
			$sendButton.removeClass('disabled').prop('disabled', false);
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', '.btn.send', _onSendClick);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvZW1haWxfaW52b2ljZS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImJpbmRpbmdzIiwic3ViamVjdCIsImZpbmQiLCJlbWFpbEFkZHJlc3MiLCJfb25TZW5kQ2xpY2siLCJldmVudCIsImdldFBhcmFtcyIsIm9JRCIsInR5cGUiLCJtYWlsIiwiZ21fcXVpY2tfbWFpbCIsInVybCIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJwYXJhbSIsImdtX21haWwiLCJnbV9zdWJqZWN0IiwiJHNlbmRCdXR0b24iLCJ0YXJnZXQiLCJhZGRDbGFzcyIsInByb3AiLCJhamF4IiwibWV0aG9kIiwiZG9uZSIsInJlc3BvbnNlIiwibWVzc2FnZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJEYXRhVGFibGUiLCJyZWxvYWQiLCJvcmRlcnNfb3ZlcnZpZXdfZmlsdGVyIiwibGlicyIsImluZm9fYm94Iiwic2VydmljZSIsImFkZFN1Y2Nlc3NNZXNzYWdlIiwiZmFpbCIsImpxeGhyIiwidGV4dFN0YXR1cyIsImVycm9yVGhyb3duIiwidGl0bGUiLCJjb250ZW50IiwibW9kYWwiLCJhbHdheXMiLCJyZW1vdmVDbGFzcyIsImluaXQiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsZUFBdEIsRUFBdUMsQ0FBQyxPQUFELENBQXZDLEVBQWtELFVBQVNDLElBQVQsRUFBZTs7QUFFaEU7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNSCxTQUFTO0FBQ2RJLFlBQVU7QUFDVEMsWUFBU0gsTUFBTUksSUFBTixDQUFXLFVBQVgsQ0FEQTtBQUVUQyxpQkFBY0wsTUFBTUksSUFBTixDQUFXLGdCQUFYO0FBRkw7QUFESSxFQUFmOztBQU9BO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7QUFLQSxVQUFTRSxZQUFULENBQXNCQyxLQUF0QixFQUE2QjtBQUM1QixNQUFNQyxZQUFZO0FBQ2pCQyxRQUFLVCxNQUFNRCxJQUFOLENBQVcsU0FBWCxDQURZO0FBRWpCVyxTQUFNLFNBRlc7QUFHakJDLFNBQU0sR0FIVztBQUlqQkMsa0JBQWU7QUFKRSxHQUFsQjtBQU1BLE1BQU1DLE1BQU1DLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsMEJBQWhDLEdBQTZEaEIsRUFBRWlCLEtBQUYsQ0FBUVYsU0FBUixDQUF6RTtBQUNBLE1BQU1ULE9BQU87QUFDWm9CLFlBQVNyQixPQUFPSSxRQUFQLENBQWdCRyxZQUFoQixDQUE2QlksR0FBN0IsRUFERztBQUVaRyxlQUFZdEIsT0FBT0ksUUFBUCxDQUFnQkMsT0FBaEIsQ0FBd0JjLEdBQXhCO0FBRkEsR0FBYjtBQUlBLE1BQU1JLGNBQWNwQixFQUFFTSxNQUFNZSxNQUFSLENBQXBCOztBQUVBRCxjQUFZRSxRQUFaLENBQXFCLFVBQXJCLEVBQWlDQyxJQUFqQyxDQUFzQyxVQUF0QyxFQUFrRCxJQUFsRDs7QUFFQXZCLElBQUV3QixJQUFGLENBQU87QUFDTlosV0FETTtBQUVOZCxhQUZNO0FBR04yQixXQUFRO0FBSEYsR0FBUCxFQUtFQyxJQUxGLENBS08sVUFBU0MsUUFBVCxFQUFtQjtBQUN4QixPQUFNQyxVQUFVZixJQUFJQyxJQUFKLENBQVNlLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixjQUF4QixFQUF3QyxlQUF4QyxDQUFoQjs7QUFFQTlCLEtBQUUscUJBQUYsRUFBeUIrQixTQUF6QixHQUFxQ1AsSUFBckMsQ0FBMENRLE1BQTFDO0FBQ0FoQyxLQUFFLHFCQUFGLEVBQXlCaUMsc0JBQXpCLENBQWdELFFBQWhEOztBQUVBO0FBQ0FwQixPQUFJcUIsSUFBSixDQUFTQyxRQUFULENBQWtCQyxPQUFsQixDQUEwQkMsaUJBQTFCLENBQTRDVCxPQUE1QztBQUNBLEdBYkYsRUFjRVUsSUFkRixDQWNPLFVBQVNDLEtBQVQsRUFBZ0JDLFVBQWhCLEVBQTRCQyxXQUE1QixFQUF5QztBQUM5QyxPQUFNQyxRQUFRN0IsSUFBSUMsSUFBSixDQUFTZSxJQUFULENBQWNDLFNBQWQsQ0FBd0IsT0FBeEIsRUFBaUMsVUFBakMsQ0FBZDtBQUNBLE9BQU1hLFVBQVU5QixJQUFJQyxJQUFKLENBQVNlLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixnQkFBeEIsRUFBMEMsZUFBMUMsQ0FBaEI7O0FBRUE7QUFDQWpCLE9BQUlxQixJQUFKLENBQVNVLEtBQVQsQ0FBZWhCLE9BQWYsQ0FBdUIsRUFBQ2MsWUFBRCxFQUFRQyxnQkFBUixFQUF2QjtBQUNBLEdBcEJGLEVBcUJFRSxNQXJCRixDQXFCUyxZQUFXO0FBQ2xCOUMsU0FBTTZDLEtBQU4sQ0FBWSxNQUFaO0FBQ0F4QixlQUFZMEIsV0FBWixDQUF3QixVQUF4QixFQUFvQ3ZCLElBQXBDLENBQXlDLFVBQXpDLEVBQXFELEtBQXJEO0FBQ0EsR0F4QkY7QUF5QkE7O0FBRUQ7QUFDQTtBQUNBOztBQUVBMUIsUUFBT2tELElBQVAsR0FBYyxVQUFTckIsSUFBVCxFQUFlO0FBQzVCM0IsUUFBTWlELEVBQU4sQ0FBUyxPQUFULEVBQWtCLFdBQWxCLEVBQStCM0MsWUFBL0I7QUFDQXFCO0FBQ0EsRUFIRDs7QUFLQSxRQUFPN0IsTUFBUDtBQUNBLENBekZEIiwiZmlsZSI6Im9yZGVycy9tb2RhbHMvZW1haWxfaW52b2ljZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZW1haWxfaW52b2ljZS5qcyAyMDE2LTA1LTA1XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBFbWFpbCBJbnZvaWNlIE1vZGFsIENvbnRyb2xsZXJcbiAqXG4gKiBIYW5kbGVzIHRoZSBmdW5jdGlvbmFsaXR5IG9mIHRoZSBFbWFpbCBJbnZvaWNlIG1vZGFsLlxuICovXG5neC5jb250cm9sbGVycy5tb2R1bGUoJ2VtYWlsX2ludm9pY2UnLCBbJ21vZGFsJ10sIGZ1bmN0aW9uKGRhdGEpIHtcblx0XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBWQVJJQUJMRVNcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFxuXHQvKipcblx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdCAqXG5cdCAqIEB0eXBlIHtqUXVlcnl9XG5cdCAqL1xuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XG5cdFxuXHQvKipcblx0ICogTW9kdWxlIEluc3RhbmNlXG5cdCAqXG5cdCAqIEB0eXBlIHtPYmplY3R9XG5cdCAqL1xuXHRjb25zdCBtb2R1bGUgPSB7XG5cdFx0YmluZGluZ3M6IHtcblx0XHRcdHN1YmplY3Q6ICR0aGlzLmZpbmQoJy5zdWJqZWN0JyksXG5cdFx0XHRlbWFpbEFkZHJlc3M6ICR0aGlzLmZpbmQoJy5lbWFpbC1hZGRyZXNzJylcblx0XHR9XG5cdH07XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gRlVOQ1RJT05TXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIFNlbmQgdGhlIG1vZGFsIGRhdGEgdG8gdGhlIGZvcm0gdGhyb3VnaCBhbiBBSkFYIGNhbGwuXG5cdCAqXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxuXHQgKi9cblx0ZnVuY3Rpb24gX29uU2VuZENsaWNrKGV2ZW50KSB7XG5cdFx0Y29uc3QgZ2V0UGFyYW1zID0ge1xuXHRcdFx0b0lEOiAkdGhpcy5kYXRhKCdvcmRlcklkJyksXG5cdFx0XHR0eXBlOiAnaW52b2ljZScsXG5cdFx0XHRtYWlsOiAnMScsXG5cdFx0XHRnbV9xdWlja19tYWlsOiAnMSdcblx0XHR9O1xuXHRcdGNvbnN0IHVybCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9nbV9wZGZfb3JkZXIucGhwPycgKyAkLnBhcmFtKGdldFBhcmFtcyk7XG5cdFx0Y29uc3QgZGF0YSA9IHtcblx0XHRcdGdtX21haWw6IG1vZHVsZS5iaW5kaW5ncy5lbWFpbEFkZHJlc3MuZ2V0KCksXG5cdFx0XHRnbV9zdWJqZWN0OiBtb2R1bGUuYmluZGluZ3Muc3ViamVjdC5nZXQoKVxuXHRcdH07XG5cdFx0Y29uc3QgJHNlbmRCdXR0b24gPSAkKGV2ZW50LnRhcmdldCk7XG5cdFx0XG5cdFx0JHNlbmRCdXR0b24uYWRkQ2xhc3MoJ2Rpc2FibGVkJykucHJvcCgnZGlzYWJsZWQnLCB0cnVlKTtcblx0XHRcblx0XHQkLmFqYXgoe1xuXHRcdFx0dXJsLFxuXHRcdFx0ZGF0YSxcblx0XHRcdG1ldGhvZDogJ1BPU1QnXG5cdFx0fSlcblx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdGNvbnN0IG1lc3NhZ2UgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnTUFJTF9TVUNDRVNTJywgJ2dtX3NlbmRfb3JkZXInKTtcblx0XHRcdFx0XG5cdFx0XHRcdCQoJy5vcmRlcnMgLnRhYmxlLW1haW4nKS5EYXRhVGFibGUoKS5hamF4LnJlbG9hZCgpO1xuXHRcdFx0XHQkKCcub3JkZXJzIC50YWJsZS1tYWluJykub3JkZXJzX292ZXJ2aWV3X2ZpbHRlcigncmVsb2FkJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBTaG93IHN1Y2Nlc3MgbWVzc2FnZSBpbiB0aGUgYWRtaW4gaW5mbyBib3guXG5cdFx0XHRcdGpzZS5saWJzLmluZm9fYm94LnNlcnZpY2UuYWRkU3VjY2Vzc01lc3NhZ2UobWVzc2FnZSk7XG5cdFx0XHR9KVxuXHRcdFx0LmZhaWwoZnVuY3Rpb24oanF4aHIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duKSB7XG5cdFx0XHRcdGNvbnN0IHRpdGxlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyk7XG5cdFx0XHRcdGNvbnN0IGNvbnRlbnQgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnTUFJTF9VTlNVQ0NFU1MnLCAnZ21fc2VuZF9vcmRlcicpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gU2hvdyBlcnJvciBtZXNzYWdlIGluIGEgbW9kYWwuXG5cdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe3RpdGxlLCBjb250ZW50fSk7XG5cdFx0XHR9KVxuXHRcdFx0LmFsd2F5cyhmdW5jdGlvbigpIHtcblx0XHRcdFx0JHRoaXMubW9kYWwoJ2hpZGUnKTtcblx0XHRcdFx0JHNlbmRCdXR0b24ucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG5cdFx0XHR9KTtcblx0fVxuXHRcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIElOSVRJQUxJWkFUSU9OXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0JHRoaXMub24oJ2NsaWNrJywgJy5idG4uc2VuZCcsIF9vblNlbmRDbGljayk7XG5cdFx0ZG9uZSgpO1xuXHR9O1xuXHRcblx0cmV0dXJuIG1vZHVsZTtcbn0pOyJdfQ==
