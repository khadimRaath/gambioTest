'use strict';

/* --------------------------------------------------------------
 email_order.js 2016-05-05
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Email Order Modal Controller
 */
gx.controllers.module('email_order', ['modal'], function (data) {

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
			type: 'send_order'
		};
		var url = jse.core.config.get('appUrl') + '/admin/gm_send_order.php?' + $.param(getParams);
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
			var $tableRow = $('tbody tr#' + getParams.oID);

			// Remove the e-mail symbol
			$tableRow.find('td.actions i.tooltip-confirmation-not-sent').remove();

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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvZW1haWxfb3JkZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJiaW5kaW5ncyIsInN1YmplY3QiLCJmaW5kIiwiZW1haWxBZGRyZXNzIiwiX29uU2VuZENsaWNrIiwiZXZlbnQiLCJnZXRQYXJhbXMiLCJvSUQiLCJ0eXBlIiwidXJsIiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsInBhcmFtIiwiZ21fbWFpbCIsImdtX3N1YmplY3QiLCIkc2VuZEJ1dHRvbiIsInRhcmdldCIsImFkZENsYXNzIiwicHJvcCIsImFqYXgiLCJtZXRob2QiLCJkb25lIiwicmVzcG9uc2UiLCJtZXNzYWdlIiwibGFuZyIsInRyYW5zbGF0ZSIsIiR0YWJsZVJvdyIsInJlbW92ZSIsImxpYnMiLCJpbmZvX2JveCIsInNlcnZpY2UiLCJhZGRTdWNjZXNzTWVzc2FnZSIsImZhaWwiLCJqcXhociIsInRleHRTdGF0dXMiLCJlcnJvclRocm93biIsInRpdGxlIiwiY29udGVudCIsIm1vZGFsIiwiYWx3YXlzIiwicmVtb3ZlQ2xhc3MiLCJpbml0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUFzQixhQUF0QixFQUFxQyxDQUFDLE9BQUQsQ0FBckMsRUFBZ0QsVUFBU0MsSUFBVCxFQUFlOztBQUU5RDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVM7QUFDZEksWUFBVTtBQUNUQyxZQUFTSCxNQUFNSSxJQUFOLENBQVcsVUFBWCxDQURBO0FBRVRDLGlCQUFjTCxNQUFNSSxJQUFOLENBQVcsZ0JBQVg7QUFGTDtBQURJLEVBQWY7O0FBT0E7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNFLFlBQVQsQ0FBc0JDLEtBQXRCLEVBQTZCO0FBQzVCLE1BQU1DLFlBQVk7QUFDakJDLFFBQUtULE1BQU1ELElBQU4sQ0FBVyxTQUFYLENBRFk7QUFFakJXLFNBQU07QUFGVyxHQUFsQjtBQUlBLE1BQU1DLE1BQU1DLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsMkJBQWhDLEdBQThEZCxFQUFFZSxLQUFGLENBQVFSLFNBQVIsQ0FBMUU7QUFDQSxNQUFNVCxPQUFPO0FBQ1prQixZQUFTbkIsT0FBT0ksUUFBUCxDQUFnQkcsWUFBaEIsQ0FBNkJVLEdBQTdCLEVBREc7QUFFWkcsZUFBWXBCLE9BQU9JLFFBQVAsQ0FBZ0JDLE9BQWhCLENBQXdCWSxHQUF4QjtBQUZBLEdBQWI7QUFJQSxNQUFNSSxjQUFjbEIsRUFBRU0sTUFBTWEsTUFBUixDQUFwQjs7QUFFQUQsY0FBWUUsUUFBWixDQUFxQixVQUFyQixFQUFpQ0MsSUFBakMsQ0FBc0MsVUFBdEMsRUFBa0QsSUFBbEQ7O0FBRUFyQixJQUFFc0IsSUFBRixDQUFPO0FBQ05aLFdBRE07QUFFTlosYUFGTTtBQUdOeUIsV0FBUTtBQUhGLEdBQVAsRUFLRUMsSUFMRixDQUtPLFVBQVNDLFFBQVQsRUFBbUI7QUFDeEIsT0FBTUMsVUFBVWYsSUFBSUMsSUFBSixDQUFTZSxJQUFULENBQWNDLFNBQWQsQ0FBd0IsY0FBeEIsRUFBd0MsZUFBeEMsQ0FBaEI7QUFDQSxPQUFNQyxZQUFZN0IsZ0JBQWNPLFVBQVVDLEdBQXhCLENBQWxCOztBQUVBO0FBQ0FxQixhQUFVMUIsSUFBVixDQUFlLDRDQUFmLEVBQTZEMkIsTUFBN0Q7O0FBRUE7QUFDQW5CLE9BQUlvQixJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLE9BQWxCLENBQTBCQyxpQkFBMUIsQ0FBNENSLE9BQTVDO0FBQ0EsR0FkRixFQWVFUyxJQWZGLENBZU8sVUFBU0MsS0FBVCxFQUFnQkMsVUFBaEIsRUFBNEJDLFdBQTVCLEVBQXlDO0FBQzlDLE9BQU1DLFFBQVE1QixJQUFJQyxJQUFKLENBQVNlLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixPQUF4QixFQUFpQyxVQUFqQyxDQUFkO0FBQ0EsT0FBTVksVUFBVTdCLElBQUlDLElBQUosQ0FBU2UsSUFBVCxDQUFjQyxTQUFkLENBQXdCLGdCQUF4QixFQUEwQyxlQUExQyxDQUFoQjs7QUFFQTtBQUNBakIsT0FBSW9CLElBQUosQ0FBU1UsS0FBVCxDQUFlZixPQUFmLENBQXVCLEVBQUNhLFlBQUQsRUFBUUMsZ0JBQVIsRUFBdkI7QUFDQSxHQXJCRixFQXNCRUUsTUF0QkYsQ0FzQlMsWUFBVztBQUNsQjNDLFNBQU0wQyxLQUFOLENBQVksTUFBWjtBQUNBdkIsZUFBWXlCLFdBQVosQ0FBd0IsVUFBeEIsRUFBb0N0QixJQUFwQyxDQUF5QyxVQUF6QyxFQUFxRCxLQUFyRDtBQUNBLEdBekJGO0FBMEJBOztBQUVEO0FBQ0E7QUFDQTs7QUFFQXhCLFFBQU8rQyxJQUFQLEdBQWMsVUFBU3BCLElBQVQsRUFBZTtBQUM1QnpCLFFBQU04QyxFQUFOLENBQVMsT0FBVCxFQUFrQixXQUFsQixFQUErQnhDLFlBQS9CO0FBQ0FtQjtBQUNBLEVBSEQ7O0FBS0EsUUFBTzNCLE1BQVA7QUFDQSxDQXhGRCIsImZpbGUiOiJvcmRlcnMvbW9kYWxzL2VtYWlsX29yZGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBlbWFpbF9vcmRlci5qcyAyMDE2LTA1LTA1XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBFbWFpbCBPcmRlciBNb2RhbCBDb250cm9sbGVyXG4gKi9cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZSgnZW1haWxfb3JkZXInLCBbJ21vZGFsJ10sIGZ1bmN0aW9uKGRhdGEpIHtcblx0XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBWQVJJQUJMRVNcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFxuXHQvKipcblx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdCAqXG5cdCAqIEB0eXBlIHtqUXVlcnl9XG5cdCAqL1xuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XG5cdFxuXHQvKipcblx0ICogTW9kdWxlIEluc3RhbmNlXG5cdCAqXG5cdCAqIEB0eXBlIHtPYmplY3R9XG5cdCAqL1xuXHRjb25zdCBtb2R1bGUgPSB7XG5cdFx0YmluZGluZ3M6IHtcblx0XHRcdHN1YmplY3Q6ICR0aGlzLmZpbmQoJy5zdWJqZWN0JyksXG5cdFx0XHRlbWFpbEFkZHJlc3M6ICR0aGlzLmZpbmQoJy5lbWFpbC1hZGRyZXNzJylcblx0XHR9XG5cdH07XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gRlVOQ1RJT05TXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIFNlbmQgdGhlIG1vZGFsIGRhdGEgdG8gdGhlIGZvcm0gdGhyb3VnaCBhbiBBSkFYIGNhbGwuXG5cdCAqXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxuXHQgKi9cblx0ZnVuY3Rpb24gX29uU2VuZENsaWNrKGV2ZW50KSB7XG5cdFx0Y29uc3QgZ2V0UGFyYW1zID0ge1xuXHRcdFx0b0lEOiAkdGhpcy5kYXRhKCdvcmRlcklkJyksXG5cdFx0XHR0eXBlOiAnc2VuZF9vcmRlcidcblx0XHR9O1xuXHRcdGNvbnN0IHVybCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9nbV9zZW5kX29yZGVyLnBocD8nICsgJC5wYXJhbShnZXRQYXJhbXMpO1xuXHRcdGNvbnN0IGRhdGEgPSB7XG5cdFx0XHRnbV9tYWlsOiBtb2R1bGUuYmluZGluZ3MuZW1haWxBZGRyZXNzLmdldCgpLFxuXHRcdFx0Z21fc3ViamVjdDogbW9kdWxlLmJpbmRpbmdzLnN1YmplY3QuZ2V0KClcblx0XHR9O1xuXHRcdGNvbnN0ICRzZW5kQnV0dG9uID0gJChldmVudC50YXJnZXQpO1xuXHRcdFxuXHRcdCRzZW5kQnV0dG9uLmFkZENsYXNzKCdkaXNhYmxlZCcpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XG5cdFx0XG5cdFx0JC5hamF4KHtcblx0XHRcdHVybCxcblx0XHRcdGRhdGEsXG5cdFx0XHRtZXRob2Q6ICdQT1NUJ1xuXHRcdH0pXG5cdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRjb25zdCBtZXNzYWdlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ01BSUxfU1VDQ0VTUycsICdnbV9zZW5kX29yZGVyJyk7XG5cdFx0XHRcdGNvbnN0ICR0YWJsZVJvdyA9ICQoYHRib2R5IHRyIyR7Z2V0UGFyYW1zLm9JRH1gKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFJlbW92ZSB0aGUgZS1tYWlsIHN5bWJvbFxuXHRcdFx0XHQkdGFibGVSb3cuZmluZCgndGQuYWN0aW9ucyBpLnRvb2x0aXAtY29uZmlybWF0aW9uLW5vdC1zZW50JykucmVtb3ZlKCk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBTaG93IHN1Y2Nlc3MgbWVzc2FnZSBpbiB0aGUgYWRtaW4gaW5mbyBib3guXG5cdFx0XHRcdGpzZS5saWJzLmluZm9fYm94LnNlcnZpY2UuYWRkU3VjY2Vzc01lc3NhZ2UobWVzc2FnZSk7XG5cdFx0XHR9KVxuXHRcdFx0LmZhaWwoZnVuY3Rpb24oanF4aHIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duKSB7XG5cdFx0XHRcdGNvbnN0IHRpdGxlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyk7XG5cdFx0XHRcdGNvbnN0IGNvbnRlbnQgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnTUFJTF9VTlNVQ0NFU1MnLCAnZ21fc2VuZF9vcmRlcicpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gU2hvdyBlcnJvciBtZXNzYWdlIGluIGEgbW9kYWwuXG5cdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe3RpdGxlLCBjb250ZW50fSk7XG5cdFx0XHR9KVxuXHRcdFx0LmFsd2F5cyhmdW5jdGlvbigpIHtcblx0XHRcdFx0JHRoaXMubW9kYWwoJ2hpZGUnKTtcblx0XHRcdFx0JHNlbmRCdXR0b24ucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG5cdFx0XHR9KTtcblx0fVxuXHRcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIElOSVRJQUxJWkFUSU9OXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0JHRoaXMub24oJ2NsaWNrJywgJy5idG4uc2VuZCcsIF9vblNlbmRDbGljayk7XG5cdFx0ZG9uZSgpO1xuXHR9O1xuXHRcblx0cmV0dXJuIG1vZHVsZTtcbn0pOyJdfQ==
