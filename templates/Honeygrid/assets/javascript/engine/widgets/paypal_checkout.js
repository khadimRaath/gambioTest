'use strict';

/* --------------------------------------------------------------
 paypal_checkout.js 2016-01-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals ppp, initPPP */

/**
 * PayPal Checkout
 *
 * Loads and handles the actions of the PayPal payment wall
 *
 * @module Widgets/paypal_checkout
 */
gambio.widgets.module('paypal_checkout', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {
		thirdPartyPaymentsBlock: []
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {},
	    paypal3_checked = $('input[value="paypal3"]').get(0).checked,
	    continue_button_text = $('div.continue_button input').val(),
	    ppplus_continue = $('<div id="ppplus_continue" class="col-xs-6 col-sm-4 col-sm-offset-4 col-md-3 ' + ' col-md-offset-6 text-right paypal_continue_button"><input type="submit" ' + ' class="btn btn-primary btn-block" value="' + continue_button_text + '"></div>');

	// ########## EVENT HANDLERS ##########

	var _paymentItemOnClick = function _paymentItemOnClick(e) {
		$('.order_payment #checkout_payment div.items div.payment_item').removeClass('module_option_selected');

		if ($('#ppplus', this).length > 0) {
			$(this).css('background-image', 'none');
			$(this).css('background-color', 'transparent');
			$('div.paypal_continue_button').show();
			$('div.continue_button').hide();
			paypal3_checked = true;
		} else {
			if (paypal3_checked) {
				paypal3_checked = false;
				console.log('3rd party payment selected ...');
				if (ppp.deselectPaymentMethod) {
					console.log('... and deselectPaymentMethod() called.');
					ppp.deselectPaymentMethod();
				} else {
					console.log('... and pp+ widget re-initialized.');
					initPPP(options.thirdPartyPaymentsBlock);
				}
			}
			$('div.paypal_continue_button').hide();
			$('div.continue_button').show();
			$(this).addClass('module_option_selected');
		}
	};

	var _ppplusContinueOnClick = function _ppplusContinueOnClick(e) {
		ppp.doContinue();
		return false;
	};

	// ########## INITIALIZATION ##########

	/**
  * Initialize Module
  * @constructor
  */
	module.init = function (done) {

		if ($('#ppplus').length > 0) {
			$('div.continue_button:first').before(ppplus_continue);

			$('input[name="payment"]:checked').closest('div.payment_item').addClass('module_option_selected');
			$('#ppplus').closest('div.payment_item').addClass('ppplus_payment_item');

			if ($('body').on) {
				$('div.payment_item_container').on('click', _paymentItemOnClick);
				$('div.paypal_continue_button').on('click', _ppplusContinueOnClick);
			} else {
				$('body').delegate('div.payment_item_container', 'click', _paymentItemOnClick);
				$('body').delegate('#ppplus_continue', 'click', _ppplusContinueOnClick);
			}

			$('div.payment_item input[value="paypal3"]').closest('div.payment_item').css('border-bottom', 'none');

			$('iframe').ready(function () {
				$('.list-group-item').each(function () {
					$(this).css('display', 'block');
				});
			});

			if (initPPP) {
				initPPP(options.thirdPartyPaymentsBlock);
			}
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGF5cGFsX2NoZWNrb3V0LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJ0aGlyZFBhcnR5UGF5bWVudHNCbG9jayIsIm9wdGlvbnMiLCJleHRlbmQiLCJwYXlwYWwzX2NoZWNrZWQiLCJnZXQiLCJjaGVja2VkIiwiY29udGludWVfYnV0dG9uX3RleHQiLCJ2YWwiLCJwcHBsdXNfY29udGludWUiLCJfcGF5bWVudEl0ZW1PbkNsaWNrIiwiZSIsInJlbW92ZUNsYXNzIiwibGVuZ3RoIiwiY3NzIiwic2hvdyIsImhpZGUiLCJjb25zb2xlIiwibG9nIiwicHBwIiwiZGVzZWxlY3RQYXltZW50TWV0aG9kIiwiaW5pdFBQUCIsImFkZENsYXNzIiwiX3BwcGx1c0NvbnRpbnVlT25DbGljayIsImRvQ29udGludWUiLCJpbml0IiwiZG9uZSIsImJlZm9yZSIsImNsb3Nlc3QiLCJvbiIsImRlbGVnYXRlIiwicmVhZHkiLCJlYWNoIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7O0FBRUE7Ozs7Ozs7QUFPQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0MsaUJBREQsRUFHQyxFQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBOztBQUVBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsV0FBVztBQUNWQywyQkFBeUI7QUFEZixFQURaO0FBQUEsS0FJQ0MsVUFBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSCxRQUFuQixFQUE2QkgsSUFBN0IsQ0FKWDtBQUFBLEtBS0NELFNBQVMsRUFMVjtBQUFBLEtBT0NRLGtCQUFrQkwsRUFBRSx3QkFBRixFQUE0Qk0sR0FBNUIsQ0FBZ0MsQ0FBaEMsRUFBbUNDLE9BUHREO0FBQUEsS0FRQ0MsdUJBQXVCUixFQUFFLDJCQUFGLEVBQStCUyxHQUEvQixFQVJ4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RDs7QUFpQ0EsUUFBTzlCLE1BQVA7QUFDQSxDQXJHRiIsImZpbGUiOiJ3aWRnZXRzL3BheXBhbF9jaGVja291dC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcGF5cGFsX2NoZWNrb3V0LmpzIDIwMTYtMDEtMjVcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKiBnbG9iYWxzIHBwcCwgaW5pdFBQUCAqL1xuXG4vKipcbiAqIFBheVBhbCBDaGVja291dFxuICpcbiAqIExvYWRzIGFuZCBoYW5kbGVzIHRoZSBhY3Rpb25zIG9mIHRoZSBQYXlQYWwgcGF5bWVudCB3YWxsXG4gKlxuICogQG1vZHVsZSBXaWRnZXRzL3BheXBhbF9jaGVja291dFxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdwYXlwYWxfY2hlY2tvdXQnLFxuXG5cdFtdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuXHRcdC8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHR0aGlyZFBhcnR5UGF5bWVudHNCbG9jazogW11cblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9LFxuXG5cdFx0XHRwYXlwYWwzX2NoZWNrZWQgPSAkKCdpbnB1dFt2YWx1ZT1cInBheXBhbDNcIl0nKS5nZXQoMCkuY2hlY2tlZCxcblx0XHRcdGNvbnRpbnVlX2J1dHRvbl90ZXh0ID0gJCgnZGl2LmNvbnRpbnVlX2J1dHRvbiBpbnB1dCcpLnZhbCgpLFxuXHRcdFx0cHBwbHVzX2NvbnRpbnVlID0gJCgnPGRpdiBpZD1cInBwcGx1c19jb250aW51ZVwiIGNsYXNzPVwiY29sLXhzLTYgY29sLXNtLTQgY29sLXNtLW9mZnNldC00IGNvbC1tZC0zICdcblx0XHRcdCAgICAgICAgICAgICAgICAgICAgKyAnIGNvbC1tZC1vZmZzZXQtNiB0ZXh0LXJpZ2h0IHBheXBhbF9jb250aW51ZV9idXR0b25cIj48aW5wdXQgdHlwZT1cInN1Ym1pdFwiICdcblx0XHRcdCAgICAgICAgICAgICAgICAgICAgKyAnIGNsYXNzPVwiYnRuIGJ0bi1wcmltYXJ5IGJ0bi1ibG9ja1wiIHZhbHVlPVwiJyArIGNvbnRpbnVlX2J1dHRvbl90ZXh0ICsgJ1wiPjwvZGl2PicpO1xuXG5cblx0XHQvLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVJTICMjIyMjIyMjIyNcblxuXHRcdHZhciBfcGF5bWVudEl0ZW1PbkNsaWNrID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0JCgnLm9yZGVyX3BheW1lbnQgI2NoZWNrb3V0X3BheW1lbnQgZGl2Lml0ZW1zIGRpdi5wYXltZW50X2l0ZW0nKS5yZW1vdmVDbGFzcygnbW9kdWxlX29wdGlvbl9zZWxlY3RlZCcpO1xuXG5cdFx0XHRpZiAoJCgnI3BwcGx1cycsIHRoaXMpLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0JCh0aGlzKS5jc3MoJ2JhY2tncm91bmQtaW1hZ2UnLCAnbm9uZScpO1xuXHRcdFx0XHQkKHRoaXMpLmNzcygnYmFja2dyb3VuZC1jb2xvcicsICd0cmFuc3BhcmVudCcpO1xuXHRcdFx0XHQkKCdkaXYucGF5cGFsX2NvbnRpbnVlX2J1dHRvbicpLnNob3coKTtcblx0XHRcdFx0JCgnZGl2LmNvbnRpbnVlX2J1dHRvbicpLmhpZGUoKTtcblx0XHRcdFx0cGF5cGFsM19jaGVja2VkID0gdHJ1ZTtcblx0XHRcdH1cblx0XHRcdGVsc2Uge1xuXHRcdFx0XHRpZiAocGF5cGFsM19jaGVja2VkKSB7XG5cdFx0XHRcdFx0cGF5cGFsM19jaGVja2VkID0gZmFsc2U7XG5cdFx0XHRcdFx0Y29uc29sZS5sb2coJzNyZCBwYXJ0eSBwYXltZW50IHNlbGVjdGVkIC4uLicpO1xuXHRcdFx0XHRcdGlmIChwcHAuZGVzZWxlY3RQYXltZW50TWV0aG9kKSB7XG5cdFx0XHRcdFx0XHRjb25zb2xlLmxvZygnLi4uIGFuZCBkZXNlbGVjdFBheW1lbnRNZXRob2QoKSBjYWxsZWQuJyk7XG5cdFx0XHRcdFx0XHRwcHAuZGVzZWxlY3RQYXltZW50TWV0aG9kKCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGVsc2Uge1xuXHRcdFx0XHRcdFx0Y29uc29sZS5sb2coJy4uLiBhbmQgcHArIHdpZGdldCByZS1pbml0aWFsaXplZC4nKTtcblx0XHRcdFx0XHRcdGluaXRQUFAob3B0aW9ucy50aGlyZFBhcnR5UGF5bWVudHNCbG9jayk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHRcdCQoJ2Rpdi5wYXlwYWxfY29udGludWVfYnV0dG9uJykuaGlkZSgpO1xuXHRcdFx0XHQkKCdkaXYuY29udGludWVfYnV0dG9uJykuc2hvdygpO1xuXHRcdFx0XHQkKHRoaXMpLmFkZENsYXNzKCdtb2R1bGVfb3B0aW9uX3NlbGVjdGVkJyk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdHZhciBfcHBwbHVzQ29udGludWVPbkNsaWNrID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0cHBwLmRvQ29udGludWUoKTtcblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9O1xuXG5cdFx0Ly8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIE1vZHVsZVxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHRpZiAoJCgnI3BwcGx1cycpLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0JCgnZGl2LmNvbnRpbnVlX2J1dHRvbjpmaXJzdCcpLmJlZm9yZShwcHBsdXNfY29udGludWUpO1xuXG5cdFx0XHRcdCQoJ2lucHV0W25hbWU9XCJwYXltZW50XCJdOmNoZWNrZWQnKS5jbG9zZXN0KCdkaXYucGF5bWVudF9pdGVtJykuYWRkQ2xhc3MoJ21vZHVsZV9vcHRpb25fc2VsZWN0ZWQnKTtcblx0XHRcdFx0JCgnI3BwcGx1cycpLmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKS5hZGRDbGFzcygncHBwbHVzX3BheW1lbnRfaXRlbScpO1xuXG5cdFx0XHRcdGlmICgkKCdib2R5Jykub24pIHtcblx0XHRcdFx0XHQkKCdkaXYucGF5bWVudF9pdGVtX2NvbnRhaW5lcicpLm9uKCdjbGljaycsIF9wYXltZW50SXRlbU9uQ2xpY2spO1x0XHRcdFx0XHRcblx0XHRcdFx0XHQkKCdkaXYucGF5cGFsX2NvbnRpbnVlX2J1dHRvbicpLm9uKCdjbGljaycsIF9wcHBsdXNDb250aW51ZU9uQ2xpY2spO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Uge1xuXHRcdFx0XHRcdCQoJ2JvZHknKS5kZWxlZ2F0ZSgnZGl2LnBheW1lbnRfaXRlbV9jb250YWluZXInLCAnY2xpY2snLCBfcGF5bWVudEl0ZW1PbkNsaWNrKTtcblx0XHRcdFx0XHQkKCdib2R5JykuZGVsZWdhdGUoJyNwcHBsdXNfY29udGludWUnLCAnY2xpY2snLCBfcHBwbHVzQ29udGludWVPbkNsaWNrKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdCQoJ2Rpdi5wYXltZW50X2l0ZW0gaW5wdXRbdmFsdWU9XCJwYXlwYWwzXCJdJykuY2xvc2VzdCgnZGl2LnBheW1lbnRfaXRlbScpLmNzcygnYm9yZGVyLWJvdHRvbScsICdub25lJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkKCdpZnJhbWUnKS5yZWFkeShmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQkKCcubGlzdC1ncm91cC1pdGVtJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCQodGhpcykuY3NzKCdkaXNwbGF5JywgJ2Jsb2NrJyk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdH0pO1xuXG5cdFx0XHRcdGlmIChpbml0UFBQKSB7XG5cdFx0XHRcdFx0aW5pdFBQUChvcHRpb25zLnRoaXJkUGFydHlQYXltZW50c0Jsb2NrKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
