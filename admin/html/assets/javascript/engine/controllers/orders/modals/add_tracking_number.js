'use strict';

/* --------------------------------------------------------------
 add_tracking_number.js 2016-09-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Add Tracking Number Modal Controller
 *
 * Handles the functionality of the "Add Tracking Number" modal.
 */
gx.controllers.module('add_tracking_number', ['modal', gx.source + '/libs/info_box'], function (data) {

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
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Stores the tracking number for a specific order.
  *
  * @param {jQuery.Event} event
  */
	function _onStoreTrackingNumberClick(event) {
		event.preventDefault();

		var orderId = $this.data('orderId');
		var parcelServiceId = $('#delivery-service').find('option:selected').val();
		var trackingNumber = $('input:text[name="tracking-number"]').val();

		// Make an AJAX call to store the tracking number if one was provided.
		if (trackingNumber.length) {
			$.ajax({
				url: './admin.php?do=OrdersModalsAjax/StoreTrackingNumber',
				data: {
					orderId: orderId,
					trackingNumber: trackingNumber,
					parcelServiceId: parcelServiceId,
					pageToken: jse.core.config.get('pageToken')
				},
				method: 'POST',
				dataType: 'JSON'
			}).done(function (response) {
				$this.modal('hide');
				jse.libs.info_box.service.addSuccessMessage(jse.core.lang.translate('ADD_TRACKING_NUMBER_SUCCESS', 'admin_orders'));
				$('.table-main').DataTable().ajax.reload();
			}).fail(function (jqXHR, textStatus, errorThrown) {
				jse.libs.modal.message({
					title: jse.core.lang.translate('error', 'messages'),
					content: jse.core.lang.translate('ADD_TRACKING_NUMBER_ERROR', 'admin_orders')
				});
				jse.core.debug.error('Store Tracking Number Error', jqXHR, textStatus, errorThrown);
			});
		} else {
			// Show an error message
			var $modalFooter = $this.find('.modal-footer');
			var errorMessage = jse.core.lang.translate('TXT_SAVE_ERROR', 'admin_general');

			// Remove error message
			$modalFooter.find('span').remove();
			$modalFooter.prepend('<span class="text-danger">' + errorMessage + '</span>');
		}
	}

	/**
  * On Add Tracking Number Modal Hidden
  *
  * Reset the tracking number modal.
  */
	function _onAddTrackingNumberModalHidden() {
		$(this).find('#tracking-number').val('');
		$(this).find('.modal-footer span').remove();
	}

	/**
  * On Add Tracking Number Modal Show
  *
  * Handles the event for storing a a tracking number from the tracking number modal.
  *
  * @param {jQuery.Event} event
  */
	function _onAddTrackingNumberModalShow(event) {
		event.stopPropagation();
		// Element which invoked the tracking number modal.
		$(this).data('orderId', $(event.relatedTarget).data('orderId'));
	}

	/**
  * Checks if the enter key was pressed and delegates to
  * the tracking number store method.
  *
  * @param {jQuery.Event} event
  */
	function _saveOnPressedEnterKey(event) {
		var keyCode = event.keyCode ? event.keyCode : event.which;

		if (keyCode === 13) {
			_onStoreTrackingNumberClick(event);
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('show.bs.modal', _onAddTrackingNumberModalShow).on('hidden.bs.modal', _onAddTrackingNumberModalHidden).on('click', '#store-tracking-number', _onStoreTrackingNumberClick).on('keypress', _saveOnPressedEnterKey);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvYWRkX3RyYWNraW5nX251bWJlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIl9vblN0b3JlVHJhY2tpbmdOdW1iZXJDbGljayIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJvcmRlcklkIiwicGFyY2VsU2VydmljZUlkIiwiZmluZCIsInZhbCIsInRyYWNraW5nTnVtYmVyIiwibGVuZ3RoIiwiYWpheCIsInVybCIsInBhZ2VUb2tlbiIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJtZXRob2QiLCJkYXRhVHlwZSIsImRvbmUiLCJyZXNwb25zZSIsIm1vZGFsIiwibGlicyIsImluZm9fYm94Iiwic2VydmljZSIsImFkZFN1Y2Nlc3NNZXNzYWdlIiwibGFuZyIsInRyYW5zbGF0ZSIsIkRhdGFUYWJsZSIsInJlbG9hZCIsImZhaWwiLCJqcVhIUiIsInRleHRTdGF0dXMiLCJlcnJvclRocm93biIsIm1lc3NhZ2UiLCJ0aXRsZSIsImNvbnRlbnQiLCJkZWJ1ZyIsImVycm9yIiwiJG1vZGFsRm9vdGVyIiwiZXJyb3JNZXNzYWdlIiwicmVtb3ZlIiwicHJlcGVuZCIsIl9vbkFkZFRyYWNraW5nTnVtYmVyTW9kYWxIaWRkZW4iLCJfb25BZGRUcmFja2luZ051bWJlck1vZGFsU2hvdyIsInN0b3BQcm9wYWdhdGlvbiIsInJlbGF0ZWRUYXJnZXQiLCJfc2F2ZU9uUHJlc3NlZEVudGVyS2V5Iiwia2V5Q29kZSIsIndoaWNoIiwiaW5pdCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUFzQixxQkFBdEIsRUFBNkMsQ0FBQyxPQUFELEVBQWFGLEdBQUdHLE1BQWhCLG9CQUE3QyxFQUFzRixVQUFTQyxJQUFULEVBQWU7O0FBRXBHOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7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TUFBTThDLFVBQVU5QyxNQUFNOEMsT0FBTixHQUFnQjlDLE1BQU04QyxPQUF0QixHQUFnQzlDLE1BQU0rQyxLQUF0RDs7QUFFQSxNQUFJRCxZQUFZLEVBQWhCLEVBQW9CO0FBQ25CL0MsK0JBQTRCQyxLQUE1QjtBQUNBO0FBQ0Q7O0FBRUQ7QUFDQTtBQUNBOztBQUVBTixRQUFPc0QsSUFBUCxHQUFjLFVBQVMvQixJQUFULEVBQWU7QUFDNUJwQixRQUNFb0QsRUFERixDQUNLLGVBREwsRUFDc0JQLDZCQUR0QixFQUVFTyxFQUZGLENBRUssaUJBRkwsRUFFd0JSLCtCQUZ4QixFQUdFUSxFQUhGLENBR0ssT0FITCxFQUdjLHdCQUhkLEVBR3dDbEQsMkJBSHhDLEVBSUVrRCxFQUpGLENBSUssVUFKTCxFQUlpQkosc0JBSmpCOztBQU1BNUI7QUFDQSxFQVJEOztBQVVBLFFBQU92QixNQUFQO0FBQ0EsQ0FoSUQiLCJmaWxlIjoib3JkZXJzL21vZGFscy9hZGRfdHJhY2tpbmdfbnVtYmVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGFkZF90cmFja2luZ19udW1iZXIuanMgMjAxNi0wOS0xMlxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBBZGQgVHJhY2tpbmcgTnVtYmVyIE1vZGFsIENvbnRyb2xsZXJcclxuICpcclxuICogSGFuZGxlcyB0aGUgZnVuY3Rpb25hbGl0eSBvZiB0aGUgXCJBZGQgVHJhY2tpbmcgTnVtYmVyXCIgbW9kYWwuXHJcbiAqL1xyXG5neC5jb250cm9sbGVycy5tb2R1bGUoJ2FkZF90cmFja2luZ19udW1iZXInLCBbJ21vZGFsJywgYCR7Z3guc291cmNlfS9saWJzL2luZm9fYm94YF0sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgbW9kdWxlID0ge307XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogU3RvcmVzIHRoZSB0cmFja2luZyBudW1iZXIgZm9yIGEgc3BlY2lmaWMgb3JkZXIuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25TdG9yZVRyYWNraW5nTnVtYmVyQ2xpY2soZXZlbnQpIHtcclxuXHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcclxuXHRcdGNvbnN0IG9yZGVySWQgPSAkdGhpcy5kYXRhKCdvcmRlcklkJyk7XHJcblx0XHRjb25zdCBwYXJjZWxTZXJ2aWNlSWQgPSAkKCcjZGVsaXZlcnktc2VydmljZScpLmZpbmQoJ29wdGlvbjpzZWxlY3RlZCcpLnZhbCgpO1xyXG5cdFx0Y29uc3QgdHJhY2tpbmdOdW1iZXIgPSAkKCdpbnB1dDp0ZXh0W25hbWU9XCJ0cmFja2luZy1udW1iZXJcIl0nKS52YWwoKTtcclxuXHRcdFxyXG5cdFx0Ly8gTWFrZSBhbiBBSkFYIGNhbGwgdG8gc3RvcmUgdGhlIHRyYWNraW5nIG51bWJlciBpZiBvbmUgd2FzIHByb3ZpZGVkLlxyXG5cdFx0aWYgKHRyYWNraW5nTnVtYmVyLmxlbmd0aCkge1xyXG5cdFx0XHQkLmFqYXgoe1xyXG5cdFx0XHRcdHVybDogJy4vYWRtaW4ucGhwP2RvPU9yZGVyc01vZGFsc0FqYXgvU3RvcmVUcmFja2luZ051bWJlcicsXHJcblx0XHRcdFx0ZGF0YToge1xyXG5cdFx0XHRcdFx0b3JkZXJJZCxcclxuXHRcdFx0XHRcdHRyYWNraW5nTnVtYmVyLFxyXG5cdFx0XHRcdFx0cGFyY2VsU2VydmljZUlkLFxyXG5cdFx0XHRcdFx0cGFnZVRva2VuOiBqc2UuY29yZS5jb25maWcuZ2V0KCdwYWdlVG9rZW4nKVxyXG5cdFx0XHRcdH0sXHJcblx0XHRcdFx0bWV0aG9kOiAnUE9TVCcsXHJcblx0XHRcdFx0ZGF0YVR5cGU6ICdKU09OJ1xyXG5cdFx0XHR9KVxyXG5cdFx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0XHQkdGhpcy5tb2RhbCgnaGlkZScpO1xyXG5cdFx0XHRcdFx0anNlLmxpYnMuaW5mb19ib3guc2VydmljZS5hZGRTdWNjZXNzTWVzc2FnZShcclxuXHRcdFx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0FERF9UUkFDS0lOR19OVU1CRVJfU1VDQ0VTUycsICdhZG1pbl9vcmRlcnMnKSk7XHJcblx0XHRcdFx0XHQkKCcudGFibGUtbWFpbicpLkRhdGFUYWJsZSgpLmFqYXgucmVsb2FkKCk7XHJcblx0XHRcdFx0fSlcclxuXHRcdFx0XHQuZmFpbChmdW5jdGlvbihqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pIHtcclxuXHRcdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xyXG5cdFx0XHRcdFx0XHR0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyksXHJcblx0XHRcdFx0XHRcdGNvbnRlbnQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdBRERfVFJBQ0tJTkdfTlVNQkVSX0VSUk9SJywgJ2FkbWluX29yZGVycycpXHJcblx0XHRcdFx0XHR9KTtcclxuXHRcdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdTdG9yZSBUcmFja2luZyBOdW1iZXIgRXJyb3InLCBqcVhIUiwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pO1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0Ly8gU2hvdyBhbiBlcnJvciBtZXNzYWdlXHJcblx0XHRcdGNvbnN0ICRtb2RhbEZvb3RlciA9ICR0aGlzLmZpbmQoJy5tb2RhbC1mb290ZXInKTtcclxuXHRcdFx0Y29uc3QgZXJyb3JNZXNzYWdlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RYVF9TQVZFX0VSUk9SJywgJ2FkbWluX2dlbmVyYWwnKTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIFJlbW92ZSBlcnJvciBtZXNzYWdlXHJcblx0XHRcdCRtb2RhbEZvb3Rlci5maW5kKCdzcGFuJykucmVtb3ZlKCk7XHJcblx0XHRcdCRtb2RhbEZvb3Rlci5wcmVwZW5kKGA8c3BhbiBjbGFzcz1cInRleHQtZGFuZ2VyXCI+JHtlcnJvck1lc3NhZ2V9PC9zcGFuPmApO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBBZGQgVHJhY2tpbmcgTnVtYmVyIE1vZGFsIEhpZGRlblxyXG5cdCAqXHJcblx0ICogUmVzZXQgdGhlIHRyYWNraW5nIG51bWJlciBtb2RhbC5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25BZGRUcmFja2luZ051bWJlck1vZGFsSGlkZGVuKCkge1xyXG5cdFx0JCh0aGlzKS5maW5kKCcjdHJhY2tpbmctbnVtYmVyJykudmFsKCcnKTtcclxuXHRcdCQodGhpcykuZmluZCgnLm1vZGFsLWZvb3RlciBzcGFuJykucmVtb3ZlKCk7XHJcblx0fVxyXG5cdFxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIEFkZCBUcmFja2luZyBOdW1iZXIgTW9kYWwgU2hvd1xyXG5cdCAqXHJcblx0ICogSGFuZGxlcyB0aGUgZXZlbnQgZm9yIHN0b3JpbmcgYSBhIHRyYWNraW5nIG51bWJlciBmcm9tIHRoZSB0cmFja2luZyBudW1iZXIgbW9kYWwuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25BZGRUcmFja2luZ051bWJlck1vZGFsU2hvdyhldmVudCkge1xyXG5cdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XHJcblx0XHQvLyBFbGVtZW50IHdoaWNoIGludm9rZWQgdGhlIHRyYWNraW5nIG51bWJlciBtb2RhbC5cclxuXHRcdCQodGhpcykuZGF0YSgnb3JkZXJJZCcsICQoZXZlbnQucmVsYXRlZFRhcmdldCkuZGF0YSgnb3JkZXJJZCcpKTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogQ2hlY2tzIGlmIHRoZSBlbnRlciBrZXkgd2FzIHByZXNzZWQgYW5kIGRlbGVnYXRlcyB0b1xyXG5cdCAqIHRoZSB0cmFja2luZyBudW1iZXIgc3RvcmUgbWV0aG9kLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX3NhdmVPblByZXNzZWRFbnRlcktleShldmVudCkge1xyXG5cdFx0Y29uc3Qga2V5Q29kZSA9IGV2ZW50LmtleUNvZGUgPyBldmVudC5rZXlDb2RlIDogZXZlbnQud2hpY2g7XHJcblx0XHRcclxuXHRcdGlmIChrZXlDb2RlID09PSAxMykge1xyXG5cdFx0XHRfb25TdG9yZVRyYWNraW5nTnVtYmVyQ2xpY2soZXZlbnQpO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0JHRoaXNcclxuXHRcdFx0Lm9uKCdzaG93LmJzLm1vZGFsJywgX29uQWRkVHJhY2tpbmdOdW1iZXJNb2RhbFNob3cpXHJcblx0XHRcdC5vbignaGlkZGVuLmJzLm1vZGFsJywgX29uQWRkVHJhY2tpbmdOdW1iZXJNb2RhbEhpZGRlbilcclxuXHRcdFx0Lm9uKCdjbGljaycsICcjc3RvcmUtdHJhY2tpbmctbnVtYmVyJywgX29uU3RvcmVUcmFja2luZ051bWJlckNsaWNrKVxyXG5cdFx0XHQub24oJ2tleXByZXNzJywgX3NhdmVPblByZXNzZWRFbnRlcktleSk7XHJcblx0XHRcclxuXHRcdGRvbmUoKTtcclxuXHR9O1xyXG5cdFxyXG5cdHJldHVybiBtb2R1bGU7XHJcbn0pOyBcclxuIl19
