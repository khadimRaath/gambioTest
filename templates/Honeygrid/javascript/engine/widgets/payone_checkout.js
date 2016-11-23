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
gambio.widgets.module(
	'payone_checkout',

	[],

	function(data) {

		'use strict';

		// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			p1_debug = true,
			module = {};

		// ########## PAYONE FUNCTIONS ##########

		var _p1_payment_submit_handler = function (e) {
			var payment_item = $('.payment_item input[type="radio"]:checked').closest('div.payment_item');
			if($('.payone_paydata_nobtn', payment_item).length > 0)
			{
				if(p1_debug) { console.log('payone cc check triggered'); }
				e.preventDefault();
				p1cc_check();
			}
		};

		var _initOnlineTransfer = function()
		{
			$('select#otrans_type').on('change', function(e) {
				var selected_type = $(this).val();
				var $pd_table = $(this).closest('table.payone_otrans_data');
				var $datarows = $('tr.datarow', $pd_table);
				$datarows.hide();
				$('.for_'+selected_type).show();
				if(selected_type == 'pfefinance' || selected_type == 'pfcard')
				{
					$(this).closest('div.payment_item').addClass('data_valid');
					$(this).closest('div.payment_item').click();
				}
			});
			$('select#otrans_type').trigger('change');

			var otrans_input_handler = function(e) {
				var any_empty = false;
				$('.payone_otrans_data input[type="text"]:visible').each(function() {
					if($(this).val() === '') { any_empty = true;	}
				});
				if(any_empty === true) {
					$('table.payone_otrans_data').addClass('payone_data_missing');
				}
				else {
					$('table.payone_otrans_data').removeClass('payone_data_missing');
				}
				$(this).closest('div.payment_item').removeClass('data_valid');
			};

			$('.payone_otrans_data input[type="text"]').keyup(otrans_input_handler);
			$('.payone_otrans_data input[type="text"]').change(otrans_input_handler);

			var pg_callback_otrans = function(response) {
				if(p1_debug) { console.log(response); }
				//var $current_block = $('div.module_option_checked');
				var $current_block = $('li.active');
				if(!response || typeof response != 'object' || response.status != 'VALID') {
					// error occurred
					var errormessage = 'ERROR';
					if(typeof response.customermessage == 'string') {
						errormessage = response.customermessage;
					}
					$('p.p1_error', $current_block).html(errormessage);
					$('p.p1_error', $current_block).show();
					$('div.payment_item', $current_block).removeClass('data_valid');
					$current_block.get(0).scrollIntoView();
				}
				else {
					$('p.p1_error', $current_block).hide();
					$('table.payone_otrans_data').hide();
					$('div.p1_finaldata_otrans').show();
					$('td.final_otrans_type').html($('select#otrans_type option').filter(':selected').html());
					$('td.final_otrans_accowner').html($('input#otrans_accowner').val());
					$('td.final_otrans_bankcode').html($('input#otrans_bankcode').val());
					$('td.final_otrans_accnum').html($('input#otrans_accnum').val());
					$('td.final_otrans_bankgroup').html($('select#otrans_bankgroup_eps option, select#otrans_bankgroup_ideal option').filter(':selected').html());
					var selected_type = $('select#otrans_type').val();
					$('.p1_finaldata_otrans tr').each(function() {
						$(this).toggle($(this).hasClass('for_'+selected_type));
					});
					$('div.payment_item', $current_block).addClass('data_valid');
					$('form#checkout_payment').trigger('submit');
				}
			};

			var payone_otrans_checkdata = function(e)
			{
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
				if(p1_debug) { console.log(data); }
				pg.call(data);
			};

			$('form#checkout_payment').on('submit', function(e) {
				var $checked_payment = $('input[name="payment"]:checked');
				if($checked_payment.val() === 'payone_otrans')
				{
					var otrans_type = $('select[name="otrans_type"]', $checked_payment.closest('li')).val();
					if(otrans_type !== 'eps' && otrans_type !== 'ideal')
					{
						if($checked_payment.closest('div.payment_item').hasClass('data_valid') === false)
						{
							e.preventDefault();
							payone_otrans_checkdata(e);
						}
					}
				}
			});
		};

		var _initELV = function()
		{
			$('table.payone_elv_data select[name="p1_elv_country"]').on('change', function(e) {
				var selected_iso_2 = $(this).val();
				var only_de_rows = $('tr.only_de', $(this).closest('table'));
				if(selected_iso_2 == 'DE') {
					only_de_rows.show('fast');
				}
				else {
					only_de_rows.hide('fast');
				}
			});
			$('table.payone_elv_data select[name="p1_elv_country"]').trigger('change');

			$('.sepadata input').on('change', function(e) {
				var sepadata = '';
				$('.sepadata input').each(function() { sepadata += $(this).val(); });
				if(sepadata.length === 0)
				{
					$('tr.only_de input').removeAttr('disabled');
				}
				else
				{
					$('tr.only_de input').attr('disabled', 'disabled');
				}
			});

			$('.only_de input').on('change', function(e) {
				var accountdata = '';
				$('.only_de input').each(function() { accountdata += $(this).val(); });
				if(accountdata.length === 0)
				{
					$('tr.sepadata input').removeAttr('disabled');
				}
				else
				{
					$('tr.sepadata input').attr('disabled', 'disabled');
				}
			});

			var pg_callback_elv = function(response) {
				if(p1_debug) { console.log(response); }
				var current_block = $('div.module_option_checked');
				if(!response || typeof response != 'object' || response.status != 'VALID') {
					// error occurred
					var errormessage = p1_payment_error;
					if(typeof response.customermessage == 'string') {
						errormessage = response.customermessage;
					}
					$('p.p1_error', current_block).html(errormessage);
					$('p.p1_error', current_block).show();
					current_block.closest('div.payment_item').removeClass('data_valid');
					current_block.get(0).scrollIntoView();
				}
				else {
					pg_callback_elv_none();
					$('form#checkout_payment').trigger('submit');
				}
			};

			var pg_callback_elv_none = function() {
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

			var payone_elv_checkdata = function(e) {
				var input_bankcountry = $('select[name="p1_elv_country"] option').filter(':selected').val();
				var input_accountnumber = $('input[name="p1_elv_accountnumber"]', $this).val();
				var input_bankcode = $('input[name="p1_elv_bankcode"]', $this).val();
				var input_iban = $('input[name="p1_elv_iban"]', $this).val();
				var input_bic = $('input[name="p1_elv_bic"]', $this).val();


				if(p1_elv_checkmode == 'none')
				{
					pg_callback_elv_none();
				}
				else
				{
					e.preventDefault(); // prevent submit
					var pg_config = p1_elv_config;
					var pg = new PAYONE.Gateway(pg_config, pg_callback_elv);
					var data = {};
					if(input_iban.length > 0) {
						data = {
							iban: input_iban,
							bic: input_bic,
							bankcountry: input_bankcountry,
						};
					}
					else {
						data = {
							bankaccount: input_accountnumber,
							bankcode: input_bankcode,
							bankcountry: input_bankcountry,
						};
					}

					if(p1_debug) { console.log(data); }
					pg.call(data);
				}
			};

			$('form#checkout_payment').on('submit', function(e) {
				var $checked_payment = $('input[name="payment"]:checked');
				if($checked_payment.val() === 'payone_elv')
				{
					if($checked_payment.closest('div.payment_item').hasClass('data_valid') === false)
					{
						payone_elv_checkdata(e);
					}
				}
			});
		};

		var _initSafeInv = function()
		{
			var _safeInvDisplayAgreement = function()
			{
				var safeInvType = $('#p1_safeinv_type').val();
				$('tr.p1-safeinv-agreement').not('.p1-show-for-' + safeInvType).hide();
				$('tr.p1-show-for-' + safeInvType).show();
			}
			$('select[name="safeinv_type"]').on('change', _safeInvDisplayAgreement);
			_safeInvDisplayAgreement();
		}

		// ########## INITIALIZATION ##########

		/**
		 * Initialize Module
		 * @constructor
		 */
		module.init = function(done) {
			if(p1_debug) { console.log('payone_checkout module initializing, submodule ' + options.module); }
			if(options.module == 'cc')
			{
				$('form#checkout_payment').on('submit', _p1_payment_submit_handler);
			}
			if(options.module == 'otrans')
			{
				_initOnlineTransfer();
			}
			if(options.module == 'elv')
			{
				_initELV();
			}
			if(options.module == 'safeinv')
			{
				_initSafeInv();
			}
			done();
		};

		return module;
	}
);
