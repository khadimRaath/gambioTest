'use strict';

/* --------------------------------------------------------------
 withdrawals_main_controller.js 2015-09-16 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Withdrawals Controller
 *
 * @module Compatibility/withdrawals_main_controller
 */
gx.controllers.module(
// Module name
'withdrawals_main_controller',

// Module Dependencies
[gx.source + '/libs/info_messages'],

/**  @lends module:Compatibility/withdrawals_main_controller */

function () {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	// Element reference

	var $this = $(this);

	// Libraries reference
	var messages = jse.libs.info_messages;

	// Meta object
	var module = {};

	// ------------------------------------------------------------------------
	// ELEMENTS DEFINITION
	// ------------------------------------------------------------------------

	// Save Order Button
	var $saveOrderIdButton = $this.find('.js-save-order-id'),
	    $orderIdInput = $this.find('input[name="withdrawal_order_id"]'),
	    $withdrawalIdText = $('#withdrawal_id'),
	    $pageTokenInput = $this.find('input[name="page_token"]');

	// ------------------------------------------------------------------------
	// METHODS
	// ------------------------------------------------------------------------

	// Save Order ID
	var _saveOrderId = function _saveOrderId() {

		$saveOrderIdButton.animate({
			opacity: 0.2
		}, 250);

		var url = ['request_port.php?', 'module=Withdrawal&', 'action=save_withdrawal_order_id'].join('');

		var request = $.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: {
				withdrawal_id: $withdrawalIdText.text(),
				order_id: $orderIdInput.val(),
				page_token: $pageTokenInput.val()
			}
		});

		request.done(function (response) {
			$saveOrderIdButton.animate({
				opacity: 1
			}, 250);

			if (response.status === 'success') {
				messages.addSuccess(jse.core.lang.translate('TXT_SAVE_SUCCESS', 'admin_general'));
			} else {
				messages.addError(jse.core.lang.translate('TXT_SAVE_ERROR', 'admin_general'));
			}
		});

		request.fail(function () {
			$saveOrderIdButton.animate({
				opacity: 1
			}, 250);

			messages.addError(jse.core.lang.translate('TXT_SAVE_ERROR', 'admin_general'));
		});
	};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	var _onClick = function _onClick(event) {
		// Save Order Button
		if ($saveOrderIdButton.is(event.target)) {
			_saveOrderId();
		}
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', _onClick);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpdGhkcmF3YWxzL3dpdGhkcmF3YWxzX21haW5fY29udHJvbGxlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwic291cmNlIiwiJHRoaXMiLCIkIiwibWVzc2FnZXMiLCJqc2UiLCJsaWJzIiwiaW5mb19tZXNzYWdlcyIsIiRzYXZlT3JkZXJJZEJ1dHRvbiIsImZpbmQiLCIkb3JkZXJJZElucHV0IiwiJHdpdGhkcmF3YWxJZFRleHQiLCIkcGFnZVRva2VuSW5wdXQiLCJfc2F2ZU9yZGVySWQiLCJhbmltYXRlIiwib3BhY2l0eSIsInVybCIsImpvaW4iLCJyZXF1ZXN0IiwiYWpheCIsInR5cGUiLCJkYXRhVHlwZSIsImRhdGEiLCJ3aXRoZHJhd2FsX2lkIiwidGV4dCIsIm9yZGVyX2lkIiwidmFsIiwicGFnZV90b2tlbiIsImRvbmUiLCJyZXNwb25zZSIsInN0YXR1cyIsImFkZFN1Y2Nlc3MiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsImFkZEVycm9yIiwiZmFpbCIsIl9vbkNsaWNrIiwiZXZlbnQiLCJpcyIsInRhcmdldCIsImluaXQiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxHQUFHQyxXQUFILENBQWVDLE1BQWY7QUFDQztBQUNBLDZCQUZEOztBQUlDO0FBQ0EsQ0FDQ0YsR0FBR0csTUFBSCxHQUFZLHFCQURiLENBTEQ7O0FBU0M7O0FBRUEsWUFBVzs7QUFFVjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7O0FBQ0EsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7O0FBRUE7QUFDQSxLQUFJQyxXQUFXQyxJQUFJQyxJQUFKLENBQVNDLGFBQXhCOztBQUVBO0FBQ0EsS0FBSVAsU0FBUyxFQUFiOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLEtBQUlRLHFCQUFxQk4sTUFBTU8sSUFBTixDQUFXLG1CQUFYLENBQXpCO0FBQUEsS0FDQ0MsZ0JBQWdCUixNQUFNTyxJQUFOLENBQVcsbUNBQVgsQ0FEakI7QUFBQSxLQUVDRSxvQkFBb0JSLEVBQUUsZ0JBQUYsQ0FGckI7QUFBQSxLQUdDUyxrQkFBa0JWLE1BQU1PLElBQU4sQ0FBVywwQkFBWCxDQUhuQjs7QUFLQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQSxLQUFJSSxlQUFlLFNBQWZBLFlBQWUsR0FBVzs7QUFFN0JMLHFCQUFtQk0sT0FBbkIsQ0FBMkI7QUFDMUJDLFlBQVM7QUFEaUIsR0FBM0IsRUFFRyxHQUZIOztBQUlBLE1BQUlDLE1BQU0sQ0FDVCxtQkFEUyxFQUVULG9CQUZTLEVBR1QsaUNBSFMsRUFJUkMsSUFKUSxDQUlILEVBSkcsQ0FBVjs7QUFNQSxNQUFJQyxVQUFVZixFQUFFZ0IsSUFBRixDQUFPO0FBQ3BCSCxRQUFLQSxHQURlO0FBRXBCSSxTQUFNLE1BRmM7QUFHcEJDLGFBQVUsTUFIVTtBQUlwQkMsU0FBTTtBQUNMQyxtQkFBZVosa0JBQWtCYSxJQUFsQixFQURWO0FBRUxDLGNBQVVmLGNBQWNnQixHQUFkLEVBRkw7QUFHTEMsZ0JBQVlmLGdCQUFnQmMsR0FBaEI7QUFIUDtBQUpjLEdBQVAsQ0FBZDs7QUFXQVIsVUFBUVUsSUFBUixDQUFhLFVBQVNDLFFBQVQsRUFBbUI7QUFDL0JyQixzQkFBbUJNLE9BQW5CLENBQTJCO0FBQzFCQyxhQUFTO0FBRGlCLElBQTNCLEVBRUcsR0FGSDs7QUFJQSxPQUFJYyxTQUFTQyxNQUFULEtBQW9CLFNBQXhCLEVBQW1DO0FBQ2xDMUIsYUFBUzJCLFVBQVQsQ0FDQzFCLElBQUkyQixJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixrQkFBeEIsRUFBNEMsZUFBNUMsQ0FERDtBQUdBLElBSkQsTUFJTztBQUNOOUIsYUFBUytCLFFBQVQsQ0FDQzlCLElBQUkyQixJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixnQkFBeEIsRUFBMEMsZUFBMUMsQ0FERDtBQUdBO0FBQ0QsR0FkRDs7QUFnQkFoQixVQUFRa0IsSUFBUixDQUFhLFlBQVc7QUFDdkI1QixzQkFBbUJNLE9BQW5CLENBQTJCO0FBQzFCQyxhQUFTO0FBRGlCLElBQTNCLEVBRUcsR0FGSDs7QUFJQVgsWUFBUytCLFFBQVQsQ0FDQzlCLElBQUkyQixJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixnQkFBeEIsRUFBMEMsZUFBMUMsQ0FERDtBQUdBLEdBUkQ7QUFTQSxFQWhERDs7QUFrREE7QUFDQTtBQUNBOztBQUVBLEtBQUlHLFdBQVcsU0FBWEEsUUFBVyxDQUFTQyxLQUFULEVBQWdCO0FBQzlCO0FBQ0EsTUFBSTlCLG1CQUFtQitCLEVBQW5CLENBQXNCRCxNQUFNRSxNQUE1QixDQUFKLEVBQXlDO0FBQ3hDM0I7QUFDQTtBQUNELEVBTEQ7O0FBT0E7QUFDQTtBQUNBOztBQUVBYixRQUFPeUMsSUFBUCxHQUFjLFVBQVNiLElBQVQsRUFBZTtBQUM1QjFCLFFBQU13QyxFQUFOLENBQVMsT0FBVCxFQUFrQkwsUUFBbEI7QUFDQVQ7QUFDQSxFQUhEOztBQUtBLFFBQU81QixNQUFQO0FBRUEsQ0FuSEYiLCJmaWxlIjoid2l0aGRyYXdhbHMvd2l0aGRyYXdhbHNfbWFpbl9jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiB3aXRoZHJhd2Fsc19tYWluX2NvbnRyb2xsZXIuanMgMjAxNS0wOS0xNiBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgV2l0aGRyYXdhbHMgQ29udHJvbGxlclxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS93aXRoZHJhd2Fsc19tYWluX2NvbnRyb2xsZXJcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKFxuXHQvLyBNb2R1bGUgbmFtZVxuXHQnd2l0aGRyYXdhbHNfbWFpbl9jb250cm9sbGVyJyxcblx0XG5cdC8vIE1vZHVsZSBEZXBlbmRlbmNpZXNcblx0W1xuXHRcdGd4LnNvdXJjZSArICcvbGlicy9pbmZvX21lc3NhZ2VzJ1xuXHRdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvd2l0aGRyYXdhbHNfbWFpbl9jb250cm9sbGVyICovXG5cdFxuXHRmdW5jdGlvbigpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvLyBFbGVtZW50IHJlZmVyZW5jZVxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyk7XG5cdFx0XG5cdFx0Ly8gTGlicmFyaWVzIHJlZmVyZW5jZVxuXHRcdHZhciBtZXNzYWdlcyA9IGpzZS5saWJzLmluZm9fbWVzc2FnZXM7XG5cdFx0XG5cdFx0Ly8gTWV0YSBvYmplY3Rcblx0XHR2YXIgbW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRUxFTUVOVFMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8vIFNhdmUgT3JkZXIgQnV0dG9uXG5cdFx0dmFyICRzYXZlT3JkZXJJZEJ1dHRvbiA9ICR0aGlzLmZpbmQoJy5qcy1zYXZlLW9yZGVyLWlkJyksXG5cdFx0XHQkb3JkZXJJZElucHV0ID0gJHRoaXMuZmluZCgnaW5wdXRbbmFtZT1cIndpdGhkcmF3YWxfb3JkZXJfaWRcIl0nKSxcblx0XHRcdCR3aXRoZHJhd2FsSWRUZXh0ID0gJCgnI3dpdGhkcmF3YWxfaWQnKSxcblx0XHRcdCRwYWdlVG9rZW5JbnB1dCA9ICR0aGlzLmZpbmQoJ2lucHV0W25hbWU9XCJwYWdlX3Rva2VuXCJdJyk7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gTUVUSE9EU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8vIFNhdmUgT3JkZXIgSURcblx0XHR2YXIgX3NhdmVPcmRlcklkID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRcblx0XHRcdCRzYXZlT3JkZXJJZEJ1dHRvbi5hbmltYXRlKHtcblx0XHRcdFx0b3BhY2l0eTogMC4yXG5cdFx0XHR9LCAyNTApO1xuXHRcdFx0XG5cdFx0XHR2YXIgdXJsID0gW1xuXHRcdFx0XHQncmVxdWVzdF9wb3J0LnBocD8nLFxuXHRcdFx0XHQnbW9kdWxlPVdpdGhkcmF3YWwmJyxcblx0XHRcdFx0J2FjdGlvbj1zYXZlX3dpdGhkcmF3YWxfb3JkZXJfaWQnXG5cdFx0XHRdLmpvaW4oJycpO1xuXHRcdFx0XG5cdFx0XHR2YXIgcmVxdWVzdCA9ICQuYWpheCh7XG5cdFx0XHRcdHVybDogdXJsLFxuXHRcdFx0XHR0eXBlOiAnUE9TVCcsXG5cdFx0XHRcdGRhdGFUeXBlOiAnanNvbicsXG5cdFx0XHRcdGRhdGE6IHtcblx0XHRcdFx0XHR3aXRoZHJhd2FsX2lkOiAkd2l0aGRyYXdhbElkVGV4dC50ZXh0KCksXG5cdFx0XHRcdFx0b3JkZXJfaWQ6ICRvcmRlcklkSW5wdXQudmFsKCksXG5cdFx0XHRcdFx0cGFnZV90b2tlbjogJHBhZ2VUb2tlbklucHV0LnZhbCgpXG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRyZXF1ZXN0LmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0JHNhdmVPcmRlcklkQnV0dG9uLmFuaW1hdGUoe1xuXHRcdFx0XHRcdG9wYWNpdHk6IDFcblx0XHRcdFx0fSwgMjUwKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmIChyZXNwb25zZS5zdGF0dXMgPT09ICdzdWNjZXNzJykge1xuXHRcdFx0XHRcdG1lc3NhZ2VzLmFkZFN1Y2Nlc3MoXG5cdFx0XHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVFhUX1NBVkVfU1VDQ0VTUycsICdhZG1pbl9nZW5lcmFsJylcblx0XHRcdFx0XHQpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdG1lc3NhZ2VzLmFkZEVycm9yKFxuXHRcdFx0XHRcdFx0anNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RYVF9TQVZFX0VSUk9SJywgJ2FkbWluX2dlbmVyYWwnKVxuXHRcdFx0XHRcdCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRyZXF1ZXN0LmZhaWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCRzYXZlT3JkZXJJZEJ1dHRvbi5hbmltYXRlKHtcblx0XHRcdFx0XHRvcGFjaXR5OiAxXG5cdFx0XHRcdH0sIDI1MCk7XG5cdFx0XHRcdFxuXHRcdFx0XHRtZXNzYWdlcy5hZGRFcnJvcihcblx0XHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVFhUX1NBVkVfRVJST1InLCAnYWRtaW5fZ2VuZXJhbCcpXG5cdFx0XHRcdCk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9vbkNsaWNrID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdC8vIFNhdmUgT3JkZXIgQnV0dG9uXG5cdFx0XHRpZiAoJHNhdmVPcmRlcklkQnV0dG9uLmlzKGV2ZW50LnRhcmdldCkpIHtcblx0XHRcdFx0X3NhdmVPcmRlcklkKCk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRoaXMub24oJ2NsaWNrJywgX29uQ2xpY2spO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0XHRcblx0fSk7XG4iXX0=
