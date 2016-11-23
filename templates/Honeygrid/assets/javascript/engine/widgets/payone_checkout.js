'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
	payone_checkout.js 2016-08-30
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Payone Checkout
 *
 * @module Widgets/payone_checkout
 */
gambio.widgets.module('payone_checkout', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    p1_debug = true,
	    module = {};

	// ########## PAYONE FUNCTIONS ##########

	var _p1_payment_submit_handler = function _p1_payment_submit_handler(e) {
		var payment_item = $('.payment_item input[type="radio"]:checked').closest('div.payment_item');
		if ($('.payone_paydata_nobtn', payment_item).length > 0) {
			if (p1_debug) {
				console.log('payone cc check triggered');
			}
			e.preventDefault();
			p1cc_check();
		}
	};

	var _initOnlineTransfer = function _initOnlineTransfer() {
		$('select#otrans_type').on('change', function (e) {
			var selected_type = $(this).val();
			var $pd_table = $(this).closest('table.payone_otrans_data');
			var $datarows = $('tr.datarow', $pd_table);
			$datarows.hide();
			$('.for_' + selected_type).show();
			if (selected_type == 'pfefinance' || selected_type == 'pfcard') {
				$(this).closest('div.payment_item').addClass('data_valid');
				$(this).closest('div.payment_item').click();
			}
		});
		$('select#otrans_type').trigger('change');

		var otrans_input_handler = function otrans_input_handler(e) {
			var any_empty = false;
			$('.payone_otrans_data input[type="text"]:visible').each(function () {
				if ($(this).val() === '') {
					any_empty = true;
				}
			});
			if (any_empty === true) {
				$('table.payone_otrans_data').addClass('payone_data_missing');
			} else {
				$('table.payone_otrans_data').removeClass('payone_data_missing');
			}
			$(this).closest('div.payment_item').removeClass('data_valid');
		};

		$('.payone_otrans_data input[type="text"]').keyup(otrans_input_handler);
		$('.payone_otrans_data input[type="text"]').change(otrans_input_handler);

		var pg_callback_otrans = function pg_callback_otrans(response) {
			if (p1_debug) {
				console.log(response);
			}
			//var $current_block = $('div.module_option_checked');
			var $current_block = $('li.active');
			if (!response || (typeof response === 'undefined' ? 'undefined' : _typeof(response)) != 'object' || response.status != 'VALID') {
				// error occurred
				var errormessage = 'ERROR';
				if (typeof response.customermessage == 'string') {
					errormessage = response.customermessage;
				}
				$('p.p1_error', $current_block).html(errormessage);
				$('p.p1_error', $current_block).show();
				$('div.payment_item', $current_block).removeClass('data_valid');
				$current_block.get(0).scrollIntoView();
			} else {
				$('p.p1_error', $current_block).hide();
				$('table.payone_otrans_data').hide();
				$('div.p1_finaldata_otrans').show();
				$('td.final_otrans_type').html($('select#otrans_type option').filter(':selected').html());
				$('td.final_otrans_accowner').html($('input#otrans_accowner').val());
				$('td.final_otrans_bankcode').html($('input#otrans_bankcode').val());
				$('td.final_otrans_accnum').html($('input#otrans_accnum').val());
				$('td.final_otrans_bankgroup').html($('select#otrans_bankgroup_eps option, select#otrans_bankgroup_ideal option').filter(':selected').html());
				var selected_type = $('select#otrans_type').val();
				$('.p1_finaldata_otrans tr').each(function () {
					$(this).toggle($(this).hasClass('for_' + selected_type));
				});
				$('div.payment_item', $current_block).addClass('data_valid');
				$('form#checkout_payment').trigger('submit');
			}
		};

		var payone_otrans_checkdata = function payone_otrans_checkdata(e) {
			var input_iban = $('input[name="otrans_iban"]', $this).val();
			var input_bic = $('input[name="otrans_bic"]', $this).val();
			var input_bankcountry = $('input[name="otrans_country"]', $this.closest('div.payment_item')).val();

			var pg_config = p1_otrans_config;
			var pg = new PAYONE.Gateway(pg_config, pg_callback_otrans);
			var data = {
				iban: input_iban,
				bic: input_bic,
				bankcountry: input_bankcountry
			};
			if (p1_debug) {
				console.log(data);
			}
			pg.call(data);
		};

		$('form#checkout_payment').on('submit', function (e) {
			var $checked_payment = $('input[name="payment"]:checked');
			if ($checked_payment.val() === 'payone_otrans') {
				var otrans_type = $('select[name="otrans_type"]', $checked_payment.closest('li')).val();
				if (otrans_type !== 'eps' && otrans_type !== 'ideal') {
					if ($checked_payment.closest('div.payment_item').hasClass('data_valid') === false) {
						e.preventDefault();
						payone_otrans_checkdata(e);
					}
				}
			}
		});
	};

	var _initELV = function _initELV() {
		$('table.payone_elv_data select[name="p1_elv_country"]').on('change', function (e) {
			var selected_iso_2 = $(this).val();
			var only_de_rows = $('tr.only_de', $(this).closest('table'));
			if (selected_iso_2 == 'DE') {
				only_de_rows.show('fast');
			} else {
				only_de_rows.hide('fast');
			}
		});
		$('table.payone_elv_data select[name="p1_elv_country"]').trigger('change');

		$('.sepadata input').on('change', function (e) {
			var sepadata = '';
			$('.sepadata input').each(function () {
				sepadata += $(this).val();
			});
			if (sepadata.length === 0) {
				$('tr.only_de input').removeAttr('disabled');
			} else {
				$('tr.only_de input').attr('disabled', 'disabled');
			}
		});

		$('.only_de input').on('change', function (e) {
			var accountdata = '';
			$('.only_de input').each(function () {
				accountdata += $(this).val();
			});
			if (accountdata.length === 0) {
				$('tr.sepadata input').removeAttr('disabled');
			} else {
				$('tr.sepadata input').attr('disabled', 'disabled');
			}
		});

		var pg_callback_elv = function pg_callback_elv(response) {
			if (p1_debug) {
				console.log(response);
			}
			var current_block = $('div.module_option_checked');
			if (!response || (typeof response === 'undefined' ? 'undefined' : _typeof(response)) != 'object' || response.status != 'VALID') {
				// error occurred
				var errormessage = p1_payment_error;
				if (typeof response.customermessage == 'string') {
					errormessage = response.customermessage;
				}
				$('p.p1_error', current_block).html(errormessage);
				$('p.p1_error', current_block).show();
				current_block.closest('div.payment_item').removeClass('data_valid');
				current_block.get(0).scrollIntoView();
			} else {
				pg_callback_elv_none();
				$('form#checkout_payment').trigger('submit');
			}
		};

		var pg_callback_elv_none = function pg_callback_elv_none() {
			var $checked_payment = $('input[name="payment"]:checked');
			$('p.p1_error', $checked_payment.closest('div.payment_item')).hide();
			$('table.payone_elv_data').hide();
			$('div.p1_finaldata_elv').show();
			$('td.final_elv_country').html($('select#p1_elv_country option').filter(':selected').html());
			$('td.final_elv_accountnumber').html($('input#p1_elv_accountnumber').val());
			$('td.final_elv_bankcode').html($('input#p1_elv_bankcode').val());
			$('td.final_elv_iban').html($('input#p1_elv_iban').val());
			$('td.final_elv_bic').html($('input#p1_elv_bic').val());
			$checked_payment.closest('div.payment_item').addClass('data_valid');
			$('table.payone_elv_data').removeClass('payone_paydata');
		};

		var payone_elv_checkdata = function payone_elv_checkdata(e) {
			var input_bankcountry = $('select[name="p1_elv_country"] option').filter(':selected').val();
			var input_accountnumber = $('input[name="p1_elv_accountnumber"]', $this).val();
			var input_bankcode = $('input[name="p1_elv_bankcode"]', $this).val();
			var input_iban = $('input[name="p1_elv_iban"]', $this).val();
			var input_bic = $('input[name="p1_elv_bic"]', $this).val();

			if (p1_elv_checkmode == 'none') {
				pg_callback_elv_none();
			} else {
				e.preventDefault(); // prevent submit
				var pg_config = p1_elv_config;
				var pg = new PAYONE.Gateway(pg_config, pg_callback_elv);
				var data = {};
				if (input_iban.length > 0) {
					data = {
						iban: input_iban,
						bic: input_bic,
						bankcountry: input_bankcountry
					};
				} else {
					data = {
						bankaccount: input_accountnumber,
						bankcode: input_bankcode,
						bankcountry: input_bankcountry
					};
				}

				if (p1_debug) {
					console.log(data);
				}
				pg.call(data);
			}
		};

		$('form#checkout_payment').on('submit', function (e) {
			var $checked_payment = $('input[name="payment"]:checked');
			if ($checked_payment.val() === 'payone_elv') {
				if ($checked_payment.closest('div.payment_item').hasClass('data_valid') === false) {
					payone_elv_checkdata(e);
				}
			}
		});
	};

	var _initSafeInv = function _initSafeInv() {
		var _safeInvDisplayAgreement = function _safeInvDisplayAgreement() {
			var safeInvType = $('#p1_safeinv_type').val();
			$('tr.p1-safeinv-agreement').not('.p1-show-for-' + safeInvType).hide();
			$('tr.p1-show-for-' + safeInvType).show();
		};
		$('select[name="safeinv_type"]').on('change', _safeInvDisplayAgreement);
		_safeInvDisplayAgreement();
	};

	// ########## INITIALIZATION ##########

	/**
  * Initialize Module
  * @constructor
  */
	module.init = function (done) {
		if (p1_debug) {
			console.log('payone_checkout module initializing, submodule ' + options.module);
		}
		if (options.module == 'cc') {
			$('form#checkout_payment').on('submit', _p1_payment_submit_handler);
		}
		if (options.module == 'otrans') {
			_initOnlineTransfer();
		}
		if (options.module == 'elv') {
			_initELV();
		}
		if (options.module == 'safeinv') {
			_initSafeInv();
		}
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGF5b25lX2NoZWNrb3V0LmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwicDFfZGVidWciLCJfcDFfcGF5bWVudF9zdWJtaXRfaGFuZGxlciIsImUiLCJwYXltZW50X2l0ZW0iLCJjbG9zZXN0IiwibGVuZ3RoIiwiY29uc29sZSIsImxvZyIsInByZXZlbnREZWZhdWx0IiwicDFjY19jaGVjayIsIl9pbml0T25saW5lVHJhbnNmZXIiLCJvbiIsInNlbGVjdGVkX3R5cGUiLCJ2YWwiLCIkcGRfdGFibGUiLCIkZGF0YXJvd3MiLCJoaWRlIiwic2hvdyIsImFkZENsYXNzIiwiY2xpY2siLCJ0cmlnZ2VyIiwib3RyYW5zX2lucHV0X2hhbmRsZXIiLCJhbnlfZW1wdHkiLCJlYWNoIiwicmVtb3ZlQ2xhc3MiLCJrZXl1cCIsImNoYW5nZSIsInBnX2NhbGxiYWNrX290cmFucyIsInJlc3BvbnNlIiwiJGN1cnJlbnRfYmxvY2siLCJzdGF0dXMiLCJlcnJvcm1lc3NhZ2UiLCJjdXN0b21lcm1lc3NhZ2UiLCJodG1sIiwiZ2V0Iiwic2Nyb2xsSW50b1ZpZXciLCJmaWx0ZXIiLCJ0b2dnbGUiLCJoYXNDbGFzcyIsInBheW9uZV9vdHJhbnNfY2hlY2tkYXRhIiwiaW5wdXRfaWJhbiIsImlucHV0X2JpYyIsImlucHV0X2Jhbmtjb3VudHJ5IiwicGdfY29uZmlnIiwicDFfb3RyYW5zX2NvbmZpZyIsInBnIiwiUEFZT05FIiwiR2F0ZXdheSIsImliYW4iLCJiaWMiLCJiYW5rY291bnRyeSIsImNhbGwiLCIkY2hlY2tlZF9wYXltZW50Iiwib3RyYW5zX3R5cGUiLCJfaW5pdEVMViIsInNlbGVjdGVkX2lzb18yIiwib25seV9kZV9yb3dzIiwic2VwYWRhdGEiLCJyZW1vdmVBdHRyIiwiYXR0ciIsImFjY291bnRkYXRhIiwicGdfY2FsbGJhY2tfZWx2IiwiY3VycmVudF9ibG9jayIsInAxX3BheW1lbnRfZXJyb3IiLCJwZ19jYWxsYmFja19lbHZfbm9uZSIsInBheW9uZV9lbHZfY2hlY2tkYXRhIiwiaW5wdXRfYWNjb3VudG51bWJlciIsImlucHV0X2Jhbmtjb2RlIiwicDFfZWx2X2NoZWNrbW9kZSIsInAxX2Vsdl9jb25maWciLCJiYW5rYWNjb3VudCIsImJhbmtjb2RlIiwiX2luaXRTYWZlSW52IiwiX3NhZmVJbnZEaXNwbGF5QWdyZWVtZW50Iiwic2FmZUludlR5cGUiLCJub3QiLCJpbml0IiwiZG9uZSJdLCJtYXBwaW5ncyI6Ijs7OztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLGlCQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTs7QUFFQSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFdBQVcsRUFEWjtBQUFBLEtBRUNDLFVBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBRlg7QUFBQSxLQUdDTSxXQUFXLElBSFo7QUFBQSxLQUlDUCxTQUFTLEVBSlY7O0FBTUE7O0FBRUEsS0FBSVEsNkJBQTZCLFNBQTdCQSwwQkFBNkIsQ0FBVUMsQ0FBVixFQUFhO0FBQzdDLE1BQUlDLGVBQWVQLEVBQUUsMkNBQUYsRUFBK0NRLE9BQS9DLENBQXVELGtCQUF2RCxDQUFuQjtBQUNBLE1BQUdSLEVBQUUsdUJBQUYsRUFBMkJPLFlBQTNCLEVBQXlDRSxNQUF6QyxHQUFrRCxDQUFyRCxFQUNBO0FBQ0MsT0FBR0wsUUFBSCxFQUFhO0FBQUVNLFlBQVFDLEdBQVIsQ0FBWSwyQkFBWjtBQUEyQztBQUMxREwsS0FBRU0sY0FBRjtBQUNBQztBQUNBO0FBQ0QsRUFSRDs7QUFVQSxLQUFJQyxzQkFBc0IsU0FBdEJBLG1CQUFzQixHQUMxQjtBQUNDZCxJQUFFLG9CQUFGLEVBQXdCZSxFQUF4QixDQUEyQixRQUEzQixFQUFxQyxVQUFTVCxDQUFULEVBQVk7QUFDaEQsT0FBSVUsZ0JBQWdCaEIsRUFBRSxJQUFGLEVBQVFpQixHQUFSLEVBQXBCO0FBQ0EsT0FBSUMsWUFBWWxCLEVBQUUsSUFBRixFQUFRUSxPQUFSLENBQWdCLDBCQUFoQixDQUFoQjtBQUNBLE9BQUlXLFlBQVluQixFQUFFLFlBQUYsRUFBZ0JrQixTQUFoQixDQUFoQjtBQUNBQyxhQUFVQyxJQUFWO0FBQ0FwQixLQUFFLFVBQVFnQixhQUFWLEVBQXlCSyxJQUF6QjtBQUNBLE9BQUdMLGlCQUFpQixZQUFqQixJQUFpQ0EsaUJBQWlCLFFBQXJELEVBQ0E7QUFDQ2hCLE1BQUUsSUFBRixFQUFRUSxPQUFSLENBQWdCLGtCQUFoQixFQUFvQ2MsUUFBcEMsQ0FBNkMsWUFBN0M7QUFDQXRCLE1BQUUsSUFBRixFQUFRUSxPQUFSLENBQWdCLGtCQUFoQixFQUFvQ2UsS0FBcEM7QUFDQTtBQUNELEdBWEQ7QUFZQXZCLElBQUUsb0JBQUYsRUFBd0J3QixPQUF4QixDQUFnQyxRQUFoQzs7QUFFQSxNQUFJQyx1QkFBdUIsU0FBdkJBLG9CQUF1QixDQUFTbkIsQ0FBVCxFQUFZO0FBQ3RDLE9BQUlvQixZQUFZLEtBQWhCO0FBQ0ExQixLQUFFLGdEQUFGLEVBQW9EMkIsSUFBcEQsQ0FBeUQsWUFBVztBQUNuRSxRQUFHM0IsRUFBRSxJQUFGLEVBQVFpQixHQUFSLE9BQWtCLEVBQXJCLEVBQXlCO0FBQUVTLGlCQUFZLElBQVo7QUFBbUI7QUFDOUMsSUFGRDtBQUdBLE9BQUdBLGNBQWMsSUFBakIsRUFBdUI7QUFDdEIxQixNQUFFLDBCQUFGLEVBQThCc0IsUUFBOUIsQ0FBdUMscUJBQXZDO0FBQ0EsSUFGRCxNQUdLO0FBQ0p0QixNQUFFLDBCQUFGLEVBQThCNEIsV0FBOUIsQ0FBMEMscUJBQTFDO0FBQ0E7QUFDRDVCLEtBQUUsSUFBRixFQUFRUSxPQUFSLENBQWdCLGtCQUFoQixFQUFvQ29CLFdBQXBDLENBQWdELFlBQWhEO0FBQ0EsR0FaRDs7QUFjQTVCLElBQUUsd0NBQUYsRUFBNEM2QixLQUE1QyxDQUFrREosb0JBQWxEO0FBQ0F6QixJQUFFLHdDQUFGLEVBQTRDOEIsTUFBNUMsQ0FBbURMLG9CQUFuRDs7QUFFQSxNQUFJTSxxQkFBcUIsU0FBckJBLGtCQUFxQixDQUFTQyxRQUFULEVBQW1CO0FBQzNDLE9BQUc1QixRQUFILEVBQWE7QUFBRU0sWUFBUUMsR0FBUixDQUFZcUIsUUFBWjtBQUF3QjtBQUN2QztBQUNBLE9BQUlDLGlCQUFpQmpDLEVBQUUsV0FBRixDQUFyQjtBQUNBLE9BQUcsQ0FBQ2dDLFFBQUQsSUFBYSxRQUFPQSxRQUFQLHlDQUFPQSxRQUFQLE1BQW1CLFFBQWhDLElBQTRDQSxTQUFTRSxNQUFULElBQW1CLE9BQWxFLEVBQTJFO0FBQzFFO0FBQ0EsUUFBSUMsZUFBZSxPQUFuQjtBQUNBLFFBQUcsT0FBT0gsU0FBU0ksZUFBaEIsSUFBbUMsUUFBdEMsRUFBZ0Q7QUFDL0NELG9CQUFlSCxTQUFTSSxlQUF4QjtBQUNBO0FBQ0RwQyxNQUFFLFlBQUYsRUFBZ0JpQyxjQUFoQixFQUFnQ0ksSUFBaEMsQ0FBcUNGLFlBQXJDO0FBQ0FuQyxNQUFFLFlBQUYsRUFBZ0JpQyxjQUFoQixFQUFnQ1osSUFBaEM7QUFDQXJCLE1BQUUsa0JBQUYsRUFBc0JpQyxjQUF0QixFQUFzQ0wsV0FBdEMsQ0FBa0QsWUFBbEQ7QUFDQUssbUJBQWVLLEdBQWYsQ0FBbUIsQ0FBbkIsRUFBc0JDLGNBQXRCO0FBQ0EsSUFWRCxNQVdLO0FBQ0p2QyxNQUFFLFlBQUYsRUFBZ0JpQyxjQUFoQixFQUFnQ2IsSUFBaEM7QUFDQXBCLE1BQUUsMEJBQUYsRUFBOEJvQixJQUE5QjtBQUNBcEIsTUFBRSx5QkFBRixFQUE2QnFCLElBQTdCO0FBQ0FyQixNQUFFLHNCQUFGLEVBQTBCcUMsSUFBMUIsQ0FBK0JyQyxFQUFFLDJCQUFGLEVBQStCd0MsTUFBL0IsQ0FBc0MsV0FBdEMsRUFBbURILElBQW5ELEVBQS9CO0FBQ0FyQyxNQUFFLDBCQUFGLEVBQThCcUMsSUFBOUIsQ0FBbUNyQyxFQUFFLHVCQUFGLEVBQTJCaUIsR0FBM0IsRUFBbkM7QUFDQWpCLE1BQUUsMEJBQUYsRUFBOEJxQyxJQUE5QixDQUFtQ3JDLEVBQUUsdUJBQUYsRUFBMkJpQixHQUEzQixFQUFuQztBQUNBakIsTUFBRSx3QkFBRixFQUE0QnFDLElBQTVCLENBQWlDckMsRUFBRSxxQkFBRixFQUF5QmlCLEdBQXpCLEVBQWpDO0FBQ0FqQixNQUFFLDJCQUFGLEVBQStCcUMsSUFBL0IsQ0FBb0NyQyxFQUFFLDBFQUFGLEVBQThFd0MsTUFBOUUsQ0FBcUYsV0FBckYsRUFBa0dILElBQWxHLEVBQXBDO0FBQ0EsUUFBSXJCLGdCQUFnQmhCLEVBQUUsb0JBQUYsRUFBd0JpQixHQUF4QixFQUFwQjtBQUNBakIsTUFBRSx5QkFBRixFQUE2QjJCLElBQTdCLENBQWtDLFlBQVc7QUFDNUMzQixPQUFFLElBQUYsRUFBUXlDLE1BQVIsQ0FBZXpDLEVBQUUsSUFBRixFQUFRMEMsUUFBUixDQUFpQixTQUFPMUIsYUFBeEIsQ0FBZjtBQUNBLEtBRkQ7QUFHQWhCLE1BQUUsa0JBQUYsRUFBc0JpQyxjQUF0QixFQUFzQ1gsUUFBdEMsQ0FBK0MsWUFBL0M7QUFDQXRCLE1BQUUsdUJBQUYsRUFBMkJ3QixPQUEzQixDQUFtQyxRQUFuQztBQUNBO0FBQ0QsR0EvQkQ7O0FBaUNBLE1BQUltQiwwQkFBMEIsU0FBMUJBLHVCQUEwQixDQUFTckMsQ0FBVCxFQUM5QjtBQUNDLE9BQUlzQyxhQUFhNUMsRUFBRSwyQkFBRixFQUErQkQsS0FBL0IsRUFBc0NrQixHQUF0QyxFQUFqQjtBQUNBLE9BQUk0QixZQUFZN0MsRUFBRSwwQkFBRixFQUE4QkQsS0FBOUIsRUFBcUNrQixHQUFyQyxFQUFoQjtBQUNBLE9BQUk2QixvQkFBb0I5QyxFQUFFLDhCQUFGLEVBQWtDRCxNQUFNUyxPQUFOLENBQWMsa0JBQWQsQ0FBbEMsRUFBcUVTLEdBQXJFLEVBQXhCOztBQUVBLE9BQUk4QixZQUFZQyxnQkFBaEI7QUFDQSxPQUFJQyxLQUFLLElBQUlDLE9BQU9DLE9BQVgsQ0FBbUJKLFNBQW5CLEVBQThCaEIsa0JBQTlCLENBQVQ7QUFDQSxPQUFJakMsT0FBTztBQUNWc0QsVUFBTVIsVUFESTtBQUVWUyxTQUFLUixTQUZLO0FBR1ZTLGlCQUFhUjtBQUhILElBQVg7QUFLQSxPQUFHMUMsUUFBSCxFQUFhO0FBQUVNLFlBQVFDLEdBQVIsQ0FBWWIsSUFBWjtBQUFvQjtBQUNuQ21ELE1BQUdNLElBQUgsQ0FBUXpELElBQVI7QUFDQSxHQWZEOztBQWlCQUUsSUFBRSx1QkFBRixFQUEyQmUsRUFBM0IsQ0FBOEIsUUFBOUIsRUFBd0MsVUFBU1QsQ0FBVCxFQUFZO0FBQ25ELE9BQUlrRCxtQkFBbUJ4RCxFQUFFLCtCQUFGLENBQXZCO0FBQ0EsT0FBR3dELGlCQUFpQnZDLEdBQWpCLE9BQTJCLGVBQTlCLEVBQ0E7QUFDQyxRQUFJd0MsY0FBY3pELEVBQUUsNEJBQUYsRUFBZ0N3RCxpQkFBaUJoRCxPQUFqQixDQUF5QixJQUF6QixDQUFoQyxFQUFnRVMsR0FBaEUsRUFBbEI7QUFDQSxRQUFHd0MsZ0JBQWdCLEtBQWhCLElBQXlCQSxnQkFBZ0IsT0FBNUMsRUFDQTtBQUNDLFNBQUdELGlCQUFpQmhELE9BQWpCLENBQXlCLGtCQUF6QixFQUE2Q2tDLFFBQTdDLENBQXNELFlBQXRELE1BQXdFLEtBQTNFLEVBQ0E7QUFDQ3BDLFFBQUVNLGNBQUY7QUFDQStCLDhCQUF3QnJDLENBQXhCO0FBQ0E7QUFDRDtBQUNEO0FBQ0QsR0FkRDtBQWVBLEVBbEdEOztBQW9HQSxLQUFJb0QsV0FBVyxTQUFYQSxRQUFXLEdBQ2Y7QUFDQzFELElBQUUscURBQUYsRUFBeURlLEVBQXpELENBQTRELFFBQTVELEVBQXNFLFVBQVNULENBQVQsRUFBWTtBQUNqRixPQUFJcUQsaUJBQWlCM0QsRUFBRSxJQUFGLEVBQVFpQixHQUFSLEVBQXJCO0FBQ0EsT0FBSTJDLGVBQWU1RCxFQUFFLFlBQUYsRUFBZ0JBLEVBQUUsSUFBRixFQUFRUSxPQUFSLENBQWdCLE9BQWhCLENBQWhCLENBQW5CO0FBQ0EsT0FBR21ELGtCQUFrQixJQUFyQixFQUEyQjtBQUMxQkMsaUJBQWF2QyxJQUFiLENBQWtCLE1BQWxCO0FBQ0EsSUFGRCxNQUdLO0FBQ0p1QyxpQkFBYXhDLElBQWIsQ0FBa0IsTUFBbEI7QUFDQTtBQUNELEdBVEQ7QUFVQXBCLElBQUUscURBQUYsRUFBeUR3QixPQUF6RCxDQUFpRSxRQUFqRTs7QUFFQXhCLElBQUUsaUJBQUYsRUFBcUJlLEVBQXJCLENBQXdCLFFBQXhCLEVBQWtDLFVBQVNULENBQVQsRUFBWTtBQUM3QyxPQUFJdUQsV0FBVyxFQUFmO0FBQ0E3RCxLQUFFLGlCQUFGLEVBQXFCMkIsSUFBckIsQ0FBMEIsWUFBVztBQUFFa0MsZ0JBQVk3RCxFQUFFLElBQUYsRUFBUWlCLEdBQVIsRUFBWjtBQUE0QixJQUFuRTtBQUNBLE9BQUc0QyxTQUFTcEQsTUFBVCxLQUFvQixDQUF2QixFQUNBO0FBQ0NULE1BQUUsa0JBQUYsRUFBc0I4RCxVQUF0QixDQUFpQyxVQUFqQztBQUNBLElBSEQsTUFLQTtBQUNDOUQsTUFBRSxrQkFBRixFQUFzQitELElBQXRCLENBQTJCLFVBQTNCLEVBQXVDLFVBQXZDO0FBQ0E7QUFDRCxHQVhEOztBQWFBL0QsSUFBRSxnQkFBRixFQUFvQmUsRUFBcEIsQ0FBdUIsUUFBdkIsRUFBaUMsVUFBU1QsQ0FBVCxFQUFZO0FBQzVDLE9BQUkwRCxjQUFjLEVBQWxCO0FBQ0FoRSxLQUFFLGdCQUFGLEVBQW9CMkIsSUFBcEIsQ0FBeUIsWUFBVztBQUFFcUMsbUJBQWVoRSxFQUFFLElBQUYsRUFBUWlCLEdBQVIsRUFBZjtBQUErQixJQUFyRTtBQUNBLE9BQUcrQyxZQUFZdkQsTUFBWixLQUF1QixDQUExQixFQUNBO0FBQ0NULE1BQUUsbUJBQUYsRUFBdUI4RCxVQUF2QixDQUFrQyxVQUFsQztBQUNBLElBSEQsTUFLQTtBQUNDOUQsTUFBRSxtQkFBRixFQUF1QitELElBQXZCLENBQTRCLFVBQTVCLEVBQXdDLFVBQXhDO0FBQ0E7QUFDRCxHQVhEOztBQWFBLE1BQUlFLGtCQUFrQixTQUFsQkEsZUFBa0IsQ0FBU2pDLFFBQVQsRUFBbUI7QUFDeEMsT0FBRzVCLFFBQUgsRUFBYTtBQUFFTSxZQUFRQyxHQUFSLENBQVlxQixRQUFaO0FBQXdCO0FBQ3ZDLE9BQUlrQyxnQkFBZ0JsRSxFQUFFLDJCQUFGLENBQXBCO0FBQ0EsT0FBRyxDQUFDZ0MsUUFBRCxJQUFhLFFBQU9BLFFBQVAseUNBQU9BLFFBQVAsTUFBbUIsUUFBaEMsSUFBNENBLFNBQVNFLE1BQVQsSUFBbUIsT0FBbEUsRUFBMkU7QUFDMUU7QUFDQSxRQUFJQyxlQUFlZ0MsZ0JBQW5CO0FBQ0EsUUFBRyxPQUFPbkMsU0FBU0ksZUFBaEIsSUFBbUMsUUFBdEMsRUFBZ0Q7QUFDL0NELG9CQUFlSCxTQUFTSSxlQUF4QjtBQUNBO0FBQ0RwQyxNQUFFLFlBQUYsRUFBZ0JrRSxhQUFoQixFQUErQjdCLElBQS9CLENBQW9DRixZQUFwQztBQUNBbkMsTUFBRSxZQUFGLEVBQWdCa0UsYUFBaEIsRUFBK0I3QyxJQUEvQjtBQUNBNkMsa0JBQWMxRCxPQUFkLENBQXNCLGtCQUF0QixFQUEwQ29CLFdBQTFDLENBQXNELFlBQXREO0FBQ0FzQyxrQkFBYzVCLEdBQWQsQ0FBa0IsQ0FBbEIsRUFBcUJDLGNBQXJCO0FBQ0EsSUFWRCxNQVdLO0FBQ0o2QjtBQUNBcEUsTUFBRSx1QkFBRixFQUEyQndCLE9BQTNCLENBQW1DLFFBQW5DO0FBQ0E7QUFDRCxHQWxCRDs7QUFvQkEsTUFBSTRDLHVCQUF1QixTQUF2QkEsb0JBQXVCLEdBQVc7QUFDckMsT0FBSVosbUJBQW1CeEQsRUFBRSwrQkFBRixDQUF2QjtBQUNBQSxLQUFFLFlBQUYsRUFBZ0J3RCxpQkFBaUJoRCxPQUFqQixDQUF5QixrQkFBekIsQ0FBaEIsRUFBOERZLElBQTlEO0FBQ0FwQixLQUFFLHVCQUFGLEVBQTJCb0IsSUFBM0I7QUFDQXBCLEtBQUUsc0JBQUYsRUFBMEJxQixJQUExQjtBQUNBckIsS0FBRSxzQkFBRixFQUEwQnFDLElBQTFCLENBQStCckMsRUFBRSw4QkFBRixFQUFrQ3dDLE1BQWxDLENBQXlDLFdBQXpDLEVBQXNESCxJQUF0RCxFQUEvQjtBQUNBckMsS0FBRSw0QkFBRixFQUFnQ3FDLElBQWhDLENBQXFDckMsRUFBRSw0QkFBRixFQUFnQ2lCLEdBQWhDLEVBQXJDO0FBQ0FqQixLQUFFLHVCQUFGLEVBQTJCcUMsSUFBM0IsQ0FBZ0NyQyxFQUFFLHVCQUFGLEVBQTJCaUIsR0FBM0IsRUFBaEM7QUFDQWpCLEtBQUUsbUJBQUYsRUFBdUJxQyxJQUF2QixDQUE0QnJDLEVBQUUsbUJBQUYsRUFBdUJpQixHQUF2QixFQUE1QjtBQUNBakIsS0FBRSxrQkFBRixFQUFzQnFDLElBQXRCLENBQTJCckMsRUFBRSxrQkFBRixFQUFzQmlCLEdBQXRCLEVBQTNCO0FBQ0F1QyxvQkFBaUJoRCxPQUFqQixDQUF5QixrQkFBekIsRUFBNkNjLFFBQTdDLENBQXNELFlBQXREO0FBQ0F0QixLQUFFLHVCQUFGLEVBQTJCNEIsV0FBM0IsQ0FBdUMsZ0JBQXZDO0FBQ0EsR0FaRDs7QUFjQSxNQUFJeUMsdUJBQXVCLFNBQXZCQSxvQkFBdUIsQ0FBUy9ELENBQVQsRUFBWTtBQUN0QyxPQUFJd0Msb0JBQW9COUMsRUFBRSxzQ0FBRixFQUEwQ3dDLE1BQTFDLENBQWlELFdBQWpELEVBQThEdkIsR0FBOUQsRUFBeEI7QUFDQSxPQUFJcUQsc0JBQXNCdEUsRUFBRSxvQ0FBRixFQUF3Q0QsS0FBeEMsRUFBK0NrQixHQUEvQyxFQUExQjtBQUNBLE9BQUlzRCxpQkFBaUJ2RSxFQUFFLCtCQUFGLEVBQW1DRCxLQUFuQyxFQUEwQ2tCLEdBQTFDLEVBQXJCO0FBQ0EsT0FBSTJCLGFBQWE1QyxFQUFFLDJCQUFGLEVBQStCRCxLQUEvQixFQUFzQ2tCLEdBQXRDLEVBQWpCO0FBQ0EsT0FBSTRCLFlBQVk3QyxFQUFFLDBCQUFGLEVBQThCRCxLQUE5QixFQUFxQ2tCLEdBQXJDLEVBQWhCOztBQUdBLE9BQUd1RCxvQkFBb0IsTUFBdkIsRUFDQTtBQUNDSjtBQUNBLElBSEQsTUFLQTtBQUNDOUQsTUFBRU0sY0FBRixHQURELENBQ3FCO0FBQ3BCLFFBQUltQyxZQUFZMEIsYUFBaEI7QUFDQSxRQUFJeEIsS0FBSyxJQUFJQyxPQUFPQyxPQUFYLENBQW1CSixTQUFuQixFQUE4QmtCLGVBQTlCLENBQVQ7QUFDQSxRQUFJbkUsT0FBTyxFQUFYO0FBQ0EsUUFBRzhDLFdBQVduQyxNQUFYLEdBQW9CLENBQXZCLEVBQTBCO0FBQ3pCWCxZQUFPO0FBQ05zRCxZQUFNUixVQURBO0FBRU5TLFdBQUtSLFNBRkM7QUFHTlMsbUJBQWFSO0FBSFAsTUFBUDtBQUtBLEtBTkQsTUFPSztBQUNKaEQsWUFBTztBQUNONEUsbUJBQWFKLG1CQURQO0FBRU5LLGdCQUFVSixjQUZKO0FBR05qQixtQkFBYVI7QUFIUCxNQUFQO0FBS0E7O0FBRUQsUUFBRzFDLFFBQUgsRUFBYTtBQUFFTSxhQUFRQyxHQUFSLENBQVliLElBQVo7QUFBb0I7QUFDbkNtRCxPQUFHTSxJQUFILENBQVF6RCxJQUFSO0FBQ0E7QUFDRCxHQXBDRDs7QUFzQ0FFLElBQUUsdUJBQUYsRUFBMkJlLEVBQTNCLENBQThCLFFBQTlCLEVBQXdDLFVBQVNULENBQVQsRUFBWTtBQUNuRCxPQUFJa0QsbUJBQW1CeEQsRUFBRSwrQkFBRixDQUF2QjtBQUNBLE9BQUd3RCxpQkFBaUJ2QyxHQUFqQixPQUEyQixZQUE5QixFQUNBO0FBQ0MsUUFBR3VDLGlCQUFpQmhELE9BQWpCLENBQXlCLGtCQUF6QixFQUE2Q2tDLFFBQTdDLENBQXNELFlBQXRELE1BQXdFLEtBQTNFLEVBQ0E7QUFDQzJCLDBCQUFxQi9ELENBQXJCO0FBQ0E7QUFDRDtBQUNELEdBVEQ7QUFVQSxFQTFIRDs7QUE0SEEsS0FBSXNFLGVBQWUsU0FBZkEsWUFBZSxHQUNuQjtBQUNDLE1BQUlDLDJCQUEyQixTQUEzQkEsd0JBQTJCLEdBQy9CO0FBQ0MsT0FBSUMsY0FBYzlFLEVBQUUsa0JBQUYsRUFBc0JpQixHQUF0QixFQUFsQjtBQUNBakIsS0FBRSx5QkFBRixFQUE2QitFLEdBQTdCLENBQWlDLGtCQUFrQkQsV0FBbkQsRUFBZ0UxRCxJQUFoRTtBQUNBcEIsS0FBRSxvQkFBb0I4RSxXQUF0QixFQUFtQ3pELElBQW5DO0FBQ0EsR0FMRDtBQU1BckIsSUFBRSw2QkFBRixFQUFpQ2UsRUFBakMsQ0FBb0MsUUFBcEMsRUFBOEM4RCx3QkFBOUM7QUFDQUE7QUFDQSxFQVZEOztBQVlBOztBQUVBOzs7O0FBSUFoRixRQUFPbUYsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QixNQUFHN0UsUUFBSCxFQUFhO0FBQUVNLFdBQVFDLEdBQVIsQ0FBWSxvREFBb0RULFFBQVFMLE1BQXhFO0FBQWtGO0FBQ2pHLE1BQUdLLFFBQVFMLE1BQVIsSUFBa0IsSUFBckIsRUFDQTtBQUNDRyxLQUFFLHVCQUFGLEVBQTJCZSxFQUEzQixDQUE4QixRQUE5QixFQUF3Q1YsMEJBQXhDO0FBQ0E7QUFDRCxNQUFHSCxRQUFRTCxNQUFSLElBQWtCLFFBQXJCLEVBQ0E7QUFDQ2lCO0FBQ0E7QUFDRCxNQUFHWixRQUFRTCxNQUFSLElBQWtCLEtBQXJCLEVBQ0E7QUFDQzZEO0FBQ0E7QUFDRCxNQUFHeEQsUUFBUUwsTUFBUixJQUFrQixTQUFyQixFQUNBO0FBQ0MrRTtBQUNBO0FBQ0RLO0FBQ0EsRUFuQkQ7O0FBcUJBLFFBQU9wRixNQUFQO0FBQ0EsQ0FyU0YiLCJmaWxlIjoid2lkZ2V0cy9wYXlvbmVfY2hlY2tvdXQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRwYXlvbmVfY2hlY2tvdXQuanMgMjAxNi0wOC0zMFxuXHRHYW1iaW8gR21iSFxuXHRodHRwOi8vd3d3LmdhbWJpby5kZVxuXHRDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcblx0UmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG5cdFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuLyoqXG4gKiBQYXlvbmUgQ2hlY2tvdXRcbiAqXG4gKiBAbW9kdWxlIFdpZGdldHMvcGF5b25lX2NoZWNrb3V0XG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J3BheW9uZV9jaGVja291dCcsXG5cblx0W10sXG5cblx0ZnVuY3Rpb24oZGF0YSkge1xuXG5cdFx0J3VzZSBzdHJpY3QnO1xuXG5cdFx0Ly8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0cDFfZGVidWcgPSB0cnVlLFxuXHRcdFx0bW9kdWxlID0ge307XG5cblx0XHQvLyAjIyMjIyMjIyMjIFBBWU9ORSBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG5cdFx0dmFyIF9wMV9wYXltZW50X3N1Ym1pdF9oYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcblx0XHRcdHZhciBwYXltZW50X2l0ZW0gPSAkKCcucGF5bWVudF9pdGVtIGlucHV0W3R5cGU9XCJyYWRpb1wiXTpjaGVja2VkJykuY2xvc2VzdCgnZGl2LnBheW1lbnRfaXRlbScpO1xuXHRcdFx0aWYoJCgnLnBheW9uZV9wYXlkYXRhX25vYnRuJywgcGF5bWVudF9pdGVtKS5sZW5ndGggPiAwKVxuXHRcdFx0e1xuXHRcdFx0XHRpZihwMV9kZWJ1ZykgeyBjb25zb2xlLmxvZygncGF5b25lIGNjIGNoZWNrIHRyaWdnZXJlZCcpOyB9XG5cdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0cDFjY19jaGVjaygpO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHR2YXIgX2luaXRPbmxpbmVUcmFuc2ZlciA9IGZ1bmN0aW9uKClcblx0XHR7XG5cdFx0XHQkKCdzZWxlY3Qjb3RyYW5zX3R5cGUnKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0XHR2YXIgc2VsZWN0ZWRfdHlwZSA9ICQodGhpcykudmFsKCk7XG5cdFx0XHRcdHZhciAkcGRfdGFibGUgPSAkKHRoaXMpLmNsb3Nlc3QoJ3RhYmxlLnBheW9uZV9vdHJhbnNfZGF0YScpO1xuXHRcdFx0XHR2YXIgJGRhdGFyb3dzID0gJCgndHIuZGF0YXJvdycsICRwZF90YWJsZSk7XG5cdFx0XHRcdCRkYXRhcm93cy5oaWRlKCk7XG5cdFx0XHRcdCQoJy5mb3JfJytzZWxlY3RlZF90eXBlKS5zaG93KCk7XG5cdFx0XHRcdGlmKHNlbGVjdGVkX3R5cGUgPT0gJ3BmZWZpbmFuY2UnIHx8IHNlbGVjdGVkX3R5cGUgPT0gJ3BmY2FyZCcpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHQkKHRoaXMpLmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKS5hZGRDbGFzcygnZGF0YV92YWxpZCcpO1xuXHRcdFx0XHRcdCQodGhpcykuY2xvc2VzdCgnZGl2LnBheW1lbnRfaXRlbScpLmNsaWNrKCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0JCgnc2VsZWN0I290cmFuc190eXBlJykudHJpZ2dlcignY2hhbmdlJyk7XG5cblx0XHRcdHZhciBvdHJhbnNfaW5wdXRfaGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0dmFyIGFueV9lbXB0eSA9IGZhbHNlO1xuXHRcdFx0XHQkKCcucGF5b25lX290cmFuc19kYXRhIGlucHV0W3R5cGU9XCJ0ZXh0XCJdOnZpc2libGUnKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdGlmKCQodGhpcykudmFsKCkgPT09ICcnKSB7IGFueV9lbXB0eSA9IHRydWU7XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRpZihhbnlfZW1wdHkgPT09IHRydWUpIHtcblx0XHRcdFx0XHQkKCd0YWJsZS5wYXlvbmVfb3RyYW5zX2RhdGEnKS5hZGRDbGFzcygncGF5b25lX2RhdGFfbWlzc2luZycpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Uge1xuXHRcdFx0XHRcdCQoJ3RhYmxlLnBheW9uZV9vdHJhbnNfZGF0YScpLnJlbW92ZUNsYXNzKCdwYXlvbmVfZGF0YV9taXNzaW5nJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0JCh0aGlzKS5jbG9zZXN0KCdkaXYucGF5bWVudF9pdGVtJykucmVtb3ZlQ2xhc3MoJ2RhdGFfdmFsaWQnKTtcblx0XHRcdH07XG5cblx0XHRcdCQoJy5wYXlvbmVfb3RyYW5zX2RhdGEgaW5wdXRbdHlwZT1cInRleHRcIl0nKS5rZXl1cChvdHJhbnNfaW5wdXRfaGFuZGxlcik7XG5cdFx0XHQkKCcucGF5b25lX290cmFuc19kYXRhIGlucHV0W3R5cGU9XCJ0ZXh0XCJdJykuY2hhbmdlKG90cmFuc19pbnB1dF9oYW5kbGVyKTtcblxuXHRcdFx0dmFyIHBnX2NhbGxiYWNrX290cmFucyA9IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdGlmKHAxX2RlYnVnKSB7IGNvbnNvbGUubG9nKHJlc3BvbnNlKTsgfVxuXHRcdFx0XHQvL3ZhciAkY3VycmVudF9ibG9jayA9ICQoJ2Rpdi5tb2R1bGVfb3B0aW9uX2NoZWNrZWQnKTtcblx0XHRcdFx0dmFyICRjdXJyZW50X2Jsb2NrID0gJCgnbGkuYWN0aXZlJyk7XG5cdFx0XHRcdGlmKCFyZXNwb25zZSB8fCB0eXBlb2YgcmVzcG9uc2UgIT0gJ29iamVjdCcgfHwgcmVzcG9uc2Uuc3RhdHVzICE9ICdWQUxJRCcpIHtcblx0XHRcdFx0XHQvLyBlcnJvciBvY2N1cnJlZFxuXHRcdFx0XHRcdHZhciBlcnJvcm1lc3NhZ2UgPSAnRVJST1InO1xuXHRcdFx0XHRcdGlmKHR5cGVvZiByZXNwb25zZS5jdXN0b21lcm1lc3NhZ2UgPT0gJ3N0cmluZycpIHtcblx0XHRcdFx0XHRcdGVycm9ybWVzc2FnZSA9IHJlc3BvbnNlLmN1c3RvbWVybWVzc2FnZTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0JCgncC5wMV9lcnJvcicsICRjdXJyZW50X2Jsb2NrKS5odG1sKGVycm9ybWVzc2FnZSk7XG5cdFx0XHRcdFx0JCgncC5wMV9lcnJvcicsICRjdXJyZW50X2Jsb2NrKS5zaG93KCk7XG5cdFx0XHRcdFx0JCgnZGl2LnBheW1lbnRfaXRlbScsICRjdXJyZW50X2Jsb2NrKS5yZW1vdmVDbGFzcygnZGF0YV92YWxpZCcpO1xuXHRcdFx0XHRcdCRjdXJyZW50X2Jsb2NrLmdldCgwKS5zY3JvbGxJbnRvVmlldygpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Uge1xuXHRcdFx0XHRcdCQoJ3AucDFfZXJyb3InLCAkY3VycmVudF9ibG9jaykuaGlkZSgpO1xuXHRcdFx0XHRcdCQoJ3RhYmxlLnBheW9uZV9vdHJhbnNfZGF0YScpLmhpZGUoKTtcblx0XHRcdFx0XHQkKCdkaXYucDFfZmluYWxkYXRhX290cmFucycpLnNob3coKTtcblx0XHRcdFx0XHQkKCd0ZC5maW5hbF9vdHJhbnNfdHlwZScpLmh0bWwoJCgnc2VsZWN0I290cmFuc190eXBlIG9wdGlvbicpLmZpbHRlcignOnNlbGVjdGVkJykuaHRtbCgpKTtcblx0XHRcdFx0XHQkKCd0ZC5maW5hbF9vdHJhbnNfYWNjb3duZXInKS5odG1sKCQoJ2lucHV0I290cmFuc19hY2Nvd25lcicpLnZhbCgpKTtcblx0XHRcdFx0XHQkKCd0ZC5maW5hbF9vdHJhbnNfYmFua2NvZGUnKS5odG1sKCQoJ2lucHV0I290cmFuc19iYW5rY29kZScpLnZhbCgpKTtcblx0XHRcdFx0XHQkKCd0ZC5maW5hbF9vdHJhbnNfYWNjbnVtJykuaHRtbCgkKCdpbnB1dCNvdHJhbnNfYWNjbnVtJykudmFsKCkpO1xuXHRcdFx0XHRcdCQoJ3RkLmZpbmFsX290cmFuc19iYW5rZ3JvdXAnKS5odG1sKCQoJ3NlbGVjdCNvdHJhbnNfYmFua2dyb3VwX2VwcyBvcHRpb24sIHNlbGVjdCNvdHJhbnNfYmFua2dyb3VwX2lkZWFsIG9wdGlvbicpLmZpbHRlcignOnNlbGVjdGVkJykuaHRtbCgpKTtcblx0XHRcdFx0XHR2YXIgc2VsZWN0ZWRfdHlwZSA9ICQoJ3NlbGVjdCNvdHJhbnNfdHlwZScpLnZhbCgpO1xuXHRcdFx0XHRcdCQoJy5wMV9maW5hbGRhdGFfb3RyYW5zIHRyJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCQodGhpcykudG9nZ2xlKCQodGhpcykuaGFzQ2xhc3MoJ2Zvcl8nK3NlbGVjdGVkX3R5cGUpKTtcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHQkKCdkaXYucGF5bWVudF9pdGVtJywgJGN1cnJlbnRfYmxvY2spLmFkZENsYXNzKCdkYXRhX3ZhbGlkJyk7XG5cdFx0XHRcdFx0JCgnZm9ybSNjaGVja291dF9wYXltZW50JykudHJpZ2dlcignc3VibWl0Jyk7XG5cdFx0XHRcdH1cblx0XHRcdH07XG5cblx0XHRcdHZhciBwYXlvbmVfb3RyYW5zX2NoZWNrZGF0YSA9IGZ1bmN0aW9uKGUpXG5cdFx0XHR7XG5cdFx0XHRcdHZhciBpbnB1dF9pYmFuID0gJCgnaW5wdXRbbmFtZT1cIm90cmFuc19pYmFuXCJdJywgJHRoaXMpLnZhbCgpO1xuXHRcdFx0XHR2YXIgaW5wdXRfYmljID0gJCgnaW5wdXRbbmFtZT1cIm90cmFuc19iaWNcIl0nLCAkdGhpcykudmFsKCk7XG5cdFx0XHRcdHZhciBpbnB1dF9iYW5rY291bnRyeSA9ICQoJ2lucHV0W25hbWU9XCJvdHJhbnNfY291bnRyeVwiXScsICR0aGlzLmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKSkudmFsKCk7XG5cblx0XHRcdFx0dmFyIHBnX2NvbmZpZyA9IHAxX290cmFuc19jb25maWc7XG5cdFx0XHRcdHZhciBwZyA9IG5ldyBQQVlPTkUuR2F0ZXdheShwZ19jb25maWcsIHBnX2NhbGxiYWNrX290cmFucyk7XG5cdFx0XHRcdHZhciBkYXRhID0ge1xuXHRcdFx0XHRcdGliYW46IGlucHV0X2liYW4sXG5cdFx0XHRcdFx0YmljOiBpbnB1dF9iaWMsXG5cdFx0XHRcdFx0YmFua2NvdW50cnk6IGlucHV0X2Jhbmtjb3VudHJ5XG5cdFx0XHRcdH07XG5cdFx0XHRcdGlmKHAxX2RlYnVnKSB7IGNvbnNvbGUubG9nKGRhdGEpOyB9XG5cdFx0XHRcdHBnLmNhbGwoZGF0YSk7XG5cdFx0XHR9O1xuXG5cdFx0XHQkKCdmb3JtI2NoZWNrb3V0X3BheW1lbnQnKS5vbignc3VibWl0JywgZnVuY3Rpb24oZSkge1xuXHRcdFx0XHR2YXIgJGNoZWNrZWRfcGF5bWVudCA9ICQoJ2lucHV0W25hbWU9XCJwYXltZW50XCJdOmNoZWNrZWQnKTtcblx0XHRcdFx0aWYoJGNoZWNrZWRfcGF5bWVudC52YWwoKSA9PT0gJ3BheW9uZV9vdHJhbnMnKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0dmFyIG90cmFuc190eXBlID0gJCgnc2VsZWN0W25hbWU9XCJvdHJhbnNfdHlwZVwiXScsICRjaGVja2VkX3BheW1lbnQuY2xvc2VzdCgnbGknKSkudmFsKCk7XG5cdFx0XHRcdFx0aWYob3RyYW5zX3R5cGUgIT09ICdlcHMnICYmIG90cmFuc190eXBlICE9PSAnaWRlYWwnKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdGlmKCRjaGVja2VkX3BheW1lbnQuY2xvc2VzdCgnZGl2LnBheW1lbnRfaXRlbScpLmhhc0NsYXNzKCdkYXRhX3ZhbGlkJykgPT09IGZhbHNlKVxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdFx0XHRcdHBheW9uZV9vdHJhbnNfY2hlY2tkYXRhKGUpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fTtcblxuXHRcdHZhciBfaW5pdEVMViA9IGZ1bmN0aW9uKClcblx0XHR7XG5cdFx0XHQkKCd0YWJsZS5wYXlvbmVfZWx2X2RhdGEgc2VsZWN0W25hbWU9XCJwMV9lbHZfY291bnRyeVwiXScpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdHZhciBzZWxlY3RlZF9pc29fMiA9ICQodGhpcykudmFsKCk7XG5cdFx0XHRcdHZhciBvbmx5X2RlX3Jvd3MgPSAkKCd0ci5vbmx5X2RlJywgJCh0aGlzKS5jbG9zZXN0KCd0YWJsZScpKTtcblx0XHRcdFx0aWYoc2VsZWN0ZWRfaXNvXzIgPT0gJ0RFJykge1xuXHRcdFx0XHRcdG9ubHlfZGVfcm93cy5zaG93KCdmYXN0Jyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZSB7XG5cdFx0XHRcdFx0b25seV9kZV9yb3dzLmhpZGUoJ2Zhc3QnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHQkKCd0YWJsZS5wYXlvbmVfZWx2X2RhdGEgc2VsZWN0W25hbWU9XCJwMV9lbHZfY291bnRyeVwiXScpLnRyaWdnZXIoJ2NoYW5nZScpO1xuXG5cdFx0XHQkKCcuc2VwYWRhdGEgaW5wdXQnKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0XHR2YXIgc2VwYWRhdGEgPSAnJztcblx0XHRcdFx0JCgnLnNlcGFkYXRhIGlucHV0JykuZWFjaChmdW5jdGlvbigpIHsgc2VwYWRhdGEgKz0gJCh0aGlzKS52YWwoKTsgfSk7XG5cdFx0XHRcdGlmKHNlcGFkYXRhLmxlbmd0aCA9PT0gMClcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCQoJ3RyLm9ubHlfZGUgaW5wdXQnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Vcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCQoJ3RyLm9ubHlfZGUgaW5wdXQnKS5hdHRyKCdkaXNhYmxlZCcsICdkaXNhYmxlZCcpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblxuXHRcdFx0JCgnLm9ubHlfZGUgaW5wdXQnKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0XHR2YXIgYWNjb3VudGRhdGEgPSAnJztcblx0XHRcdFx0JCgnLm9ubHlfZGUgaW5wdXQnKS5lYWNoKGZ1bmN0aW9uKCkgeyBhY2NvdW50ZGF0YSArPSAkKHRoaXMpLnZhbCgpOyB9KTtcblx0XHRcdFx0aWYoYWNjb3VudGRhdGEubGVuZ3RoID09PSAwKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgndHIuc2VwYWRhdGEgaW5wdXQnKS5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Vcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCQoJ3RyLnNlcGFkYXRhIGlucHV0JykuYXR0cignZGlzYWJsZWQnLCAnZGlzYWJsZWQnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cblx0XHRcdHZhciBwZ19jYWxsYmFja19lbHYgPSBmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRpZihwMV9kZWJ1ZykgeyBjb25zb2xlLmxvZyhyZXNwb25zZSk7IH1cblx0XHRcdFx0dmFyIGN1cnJlbnRfYmxvY2sgPSAkKCdkaXYubW9kdWxlX29wdGlvbl9jaGVja2VkJyk7XG5cdFx0XHRcdGlmKCFyZXNwb25zZSB8fCB0eXBlb2YgcmVzcG9uc2UgIT0gJ29iamVjdCcgfHwgcmVzcG9uc2Uuc3RhdHVzICE9ICdWQUxJRCcpIHtcblx0XHRcdFx0XHQvLyBlcnJvciBvY2N1cnJlZFxuXHRcdFx0XHRcdHZhciBlcnJvcm1lc3NhZ2UgPSBwMV9wYXltZW50X2Vycm9yO1xuXHRcdFx0XHRcdGlmKHR5cGVvZiByZXNwb25zZS5jdXN0b21lcm1lc3NhZ2UgPT0gJ3N0cmluZycpIHtcblx0XHRcdFx0XHRcdGVycm9ybWVzc2FnZSA9IHJlc3BvbnNlLmN1c3RvbWVybWVzc2FnZTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0JCgncC5wMV9lcnJvcicsIGN1cnJlbnRfYmxvY2spLmh0bWwoZXJyb3JtZXNzYWdlKTtcblx0XHRcdFx0XHQkKCdwLnAxX2Vycm9yJywgY3VycmVudF9ibG9jaykuc2hvdygpO1xuXHRcdFx0XHRcdGN1cnJlbnRfYmxvY2suY2xvc2VzdCgnZGl2LnBheW1lbnRfaXRlbScpLnJlbW92ZUNsYXNzKCdkYXRhX3ZhbGlkJyk7XG5cdFx0XHRcdFx0Y3VycmVudF9ibG9jay5nZXQoMCkuc2Nyb2xsSW50b1ZpZXcoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlIHtcblx0XHRcdFx0XHRwZ19jYWxsYmFja19lbHZfbm9uZSgpO1xuXHRcdFx0XHRcdCQoJ2Zvcm0jY2hlY2tvdXRfcGF5bWVudCcpLnRyaWdnZXIoJ3N1Ym1pdCcpO1xuXHRcdFx0XHR9XG5cdFx0XHR9O1xuXG5cdFx0XHR2YXIgcGdfY2FsbGJhY2tfZWx2X25vbmUgPSBmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyICRjaGVja2VkX3BheW1lbnQgPSAkKCdpbnB1dFtuYW1lPVwicGF5bWVudFwiXTpjaGVja2VkJyk7XG5cdFx0XHRcdCQoJ3AucDFfZXJyb3InLCAkY2hlY2tlZF9wYXltZW50LmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKSkuaGlkZSgpO1xuXHRcdFx0XHQkKCd0YWJsZS5wYXlvbmVfZWx2X2RhdGEnKS5oaWRlKCk7XG5cdFx0XHRcdCQoJ2Rpdi5wMV9maW5hbGRhdGFfZWx2Jykuc2hvdygpO1xuXHRcdFx0XHQkKCd0ZC5maW5hbF9lbHZfY291bnRyeScpLmh0bWwoJCgnc2VsZWN0I3AxX2Vsdl9jb3VudHJ5IG9wdGlvbicpLmZpbHRlcignOnNlbGVjdGVkJykuaHRtbCgpKTtcblx0XHRcdFx0JCgndGQuZmluYWxfZWx2X2FjY291bnRudW1iZXInKS5odG1sKCQoJ2lucHV0I3AxX2Vsdl9hY2NvdW50bnVtYmVyJykudmFsKCkpO1xuXHRcdFx0XHQkKCd0ZC5maW5hbF9lbHZfYmFua2NvZGUnKS5odG1sKCQoJ2lucHV0I3AxX2Vsdl9iYW5rY29kZScpLnZhbCgpKTtcblx0XHRcdFx0JCgndGQuZmluYWxfZWx2X2liYW4nKS5odG1sKCQoJ2lucHV0I3AxX2Vsdl9pYmFuJykudmFsKCkpO1xuXHRcdFx0XHQkKCd0ZC5maW5hbF9lbHZfYmljJykuaHRtbCgkKCdpbnB1dCNwMV9lbHZfYmljJykudmFsKCkpO1xuXHRcdFx0XHQkY2hlY2tlZF9wYXltZW50LmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKS5hZGRDbGFzcygnZGF0YV92YWxpZCcpO1xuXHRcdFx0XHQkKCd0YWJsZS5wYXlvbmVfZWx2X2RhdGEnKS5yZW1vdmVDbGFzcygncGF5b25lX3BheWRhdGEnKTtcblx0XHRcdH07XG5cblx0XHRcdHZhciBwYXlvbmVfZWx2X2NoZWNrZGF0YSA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0dmFyIGlucHV0X2Jhbmtjb3VudHJ5ID0gJCgnc2VsZWN0W25hbWU9XCJwMV9lbHZfY291bnRyeVwiXSBvcHRpb24nKS5maWx0ZXIoJzpzZWxlY3RlZCcpLnZhbCgpO1xuXHRcdFx0XHR2YXIgaW5wdXRfYWNjb3VudG51bWJlciA9ICQoJ2lucHV0W25hbWU9XCJwMV9lbHZfYWNjb3VudG51bWJlclwiXScsICR0aGlzKS52YWwoKTtcblx0XHRcdFx0dmFyIGlucHV0X2Jhbmtjb2RlID0gJCgnaW5wdXRbbmFtZT1cInAxX2Vsdl9iYW5rY29kZVwiXScsICR0aGlzKS52YWwoKTtcblx0XHRcdFx0dmFyIGlucHV0X2liYW4gPSAkKCdpbnB1dFtuYW1lPVwicDFfZWx2X2liYW5cIl0nLCAkdGhpcykudmFsKCk7XG5cdFx0XHRcdHZhciBpbnB1dF9iaWMgPSAkKCdpbnB1dFtuYW1lPVwicDFfZWx2X2JpY1wiXScsICR0aGlzKS52YWwoKTtcblxuXG5cdFx0XHRcdGlmKHAxX2Vsdl9jaGVja21vZGUgPT0gJ25vbmUnKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0cGdfY2FsbGJhY2tfZWx2X25vbmUoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7IC8vIHByZXZlbnQgc3VibWl0XG5cdFx0XHRcdFx0dmFyIHBnX2NvbmZpZyA9IHAxX2Vsdl9jb25maWc7XG5cdFx0XHRcdFx0dmFyIHBnID0gbmV3IFBBWU9ORS5HYXRld2F5KHBnX2NvbmZpZywgcGdfY2FsbGJhY2tfZWx2KTtcblx0XHRcdFx0XHR2YXIgZGF0YSA9IHt9O1xuXHRcdFx0XHRcdGlmKGlucHV0X2liYW4ubGVuZ3RoID4gMCkge1xuXHRcdFx0XHRcdFx0ZGF0YSA9IHtcblx0XHRcdFx0XHRcdFx0aWJhbjogaW5wdXRfaWJhbixcblx0XHRcdFx0XHRcdFx0YmljOiBpbnB1dF9iaWMsXG5cdFx0XHRcdFx0XHRcdGJhbmtjb3VudHJ5OiBpbnB1dF9iYW5rY291bnRyeSxcblx0XHRcdFx0XHRcdH07XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGVsc2Uge1xuXHRcdFx0XHRcdFx0ZGF0YSA9IHtcblx0XHRcdFx0XHRcdFx0YmFua2FjY291bnQ6IGlucHV0X2FjY291bnRudW1iZXIsXG5cdFx0XHRcdFx0XHRcdGJhbmtjb2RlOiBpbnB1dF9iYW5rY29kZSxcblx0XHRcdFx0XHRcdFx0YmFua2NvdW50cnk6IGlucHV0X2Jhbmtjb3VudHJ5LFxuXHRcdFx0XHRcdFx0fTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0XHRpZihwMV9kZWJ1ZykgeyBjb25zb2xlLmxvZyhkYXRhKTsgfVxuXHRcdFx0XHRcdHBnLmNhbGwoZGF0YSk7XG5cdFx0XHRcdH1cblx0XHRcdH07XG5cblx0XHRcdCQoJ2Zvcm0jY2hlY2tvdXRfcGF5bWVudCcpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdHZhciAkY2hlY2tlZF9wYXltZW50ID0gJCgnaW5wdXRbbmFtZT1cInBheW1lbnRcIl06Y2hlY2tlZCcpO1xuXHRcdFx0XHRpZigkY2hlY2tlZF9wYXltZW50LnZhbCgpID09PSAncGF5b25lX2VsdicpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRpZigkY2hlY2tlZF9wYXltZW50LmNsb3Nlc3QoJ2Rpdi5wYXltZW50X2l0ZW0nKS5oYXNDbGFzcygnZGF0YV92YWxpZCcpID09PSBmYWxzZSlcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRwYXlvbmVfZWx2X2NoZWNrZGF0YShlKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdH07XG5cblx0XHR2YXIgX2luaXRTYWZlSW52ID0gZnVuY3Rpb24oKVxuXHRcdHtcblx0XHRcdHZhciBfc2FmZUludkRpc3BsYXlBZ3JlZW1lbnQgPSBmdW5jdGlvbigpXG5cdFx0XHR7XG5cdFx0XHRcdHZhciBzYWZlSW52VHlwZSA9ICQoJyNwMV9zYWZlaW52X3R5cGUnKS52YWwoKTtcblx0XHRcdFx0JCgndHIucDEtc2FmZWludi1hZ3JlZW1lbnQnKS5ub3QoJy5wMS1zaG93LWZvci0nICsgc2FmZUludlR5cGUpLmhpZGUoKTtcblx0XHRcdFx0JCgndHIucDEtc2hvdy1mb3ItJyArIHNhZmVJbnZUeXBlKS5zaG93KCk7XG5cdFx0XHR9XG5cdFx0XHQkKCdzZWxlY3RbbmFtZT1cInNhZmVpbnZfdHlwZVwiXScpLm9uKCdjaGFuZ2UnLCBfc2FmZUludkRpc3BsYXlBZ3JlZW1lbnQpO1xuXHRcdFx0X3NhZmVJbnZEaXNwbGF5QWdyZWVtZW50KCk7XG5cdFx0fVxuXG5cdFx0Ly8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIE1vZHVsZVxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0aWYocDFfZGVidWcpIHsgY29uc29sZS5sb2coJ3BheW9uZV9jaGVja291dCBtb2R1bGUgaW5pdGlhbGl6aW5nLCBzdWJtb2R1bGUgJyArIG9wdGlvbnMubW9kdWxlKTsgfVxuXHRcdFx0aWYob3B0aW9ucy5tb2R1bGUgPT0gJ2NjJylcblx0XHRcdHtcblx0XHRcdFx0JCgnZm9ybSNjaGVja291dF9wYXltZW50Jykub24oJ3N1Ym1pdCcsIF9wMV9wYXltZW50X3N1Ym1pdF9oYW5kbGVyKTtcblx0XHRcdH1cblx0XHRcdGlmKG9wdGlvbnMubW9kdWxlID09ICdvdHJhbnMnKVxuXHRcdFx0e1xuXHRcdFx0XHRfaW5pdE9ubGluZVRyYW5zZmVyKCk7XG5cdFx0XHR9XG5cdFx0XHRpZihvcHRpb25zLm1vZHVsZSA9PSAnZWx2Jylcblx0XHRcdHtcblx0XHRcdFx0X2luaXRFTFYoKTtcblx0XHRcdH1cblx0XHRcdGlmKG9wdGlvbnMubW9kdWxlID09ICdzYWZlaW52Jylcblx0XHRcdHtcblx0XHRcdFx0X2luaXRTYWZlSW52KCk7XG5cdFx0XHR9XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH1cbik7XG4iXX0=
