/* --------------------------------------------------------------
	checkout_payone.js 2015-07-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

<?php
include DIR_FS_CATALOG.'/ext/payone/js/client_api.js';
?>

$(function() {
	var p1_debug = false;

	/* disable continue button if additional data is required */
	$('div.payment_item').click(function(e) {
		if($('.payone_paydata, .payone_paydata_nobtn', this).length > 0) {
			//$('div.continue_button').toggle($(this).hasClass('data_valid'));
			if($('div.p1_continue_button').length == 0) {
				var p1_continue_button = $('<div>').addClass('p1_continue_button');
				var button_text = $('div.continue_button').first().text();
				p1_continue_button.html('<img class="png-fix" src="templates/EyeCandy/img/icons/icon-white-shoppingcart.png" alt="" style="margin-right:10px; float:left"></img>&nbsp;' + button_text);
				$('div.continue_button').after(p1_continue_button);
				p1_continue_button.click(function(e) { p1_continue_click(e); });
			}
			$('div.continue_button').hide();

		}
		else {
			$('div.continue_button').show();
			$('div.p1_continue_button').remove();
		}
	});
	$('div.payment_item.module_option_checked').click();

	var p1_continue_click = function(e) {
		var payment_item = $('.payment_item input[type="radio"]:checked').closest('div.payment_item');
		if($('button.p1_checkdata', payment_item).length > 0)
		{
			var checkdata_button = $('button.p1_checkdata', payment_item);
			//checkdata_button = p1_get_checkdata_button();
			checkdata_button.click();
		}
		if($('.payone_paydata_nobtn', payment_item).length > 0)
		{
			p1cc_check();
		}
	}

	/*
	** credit cards
	*/
	// cf. checkout_payone_cc_form.html

	/*
	** onlinetransfer
	*/
	$('select#otrans_type').change(function(e) {
		var selected_type = $(this).val();
		var pd_table = $(this).closest('table.payone_otrans_data');
		var datarows = $('tr.datarow', pd_table);
		datarows.hide();
		$('.for_'+selected_type).show();
		if(selected_type == 'pfefinance' || selected_type == 'pfcard') {
			$(this).closest('div.payment_item').addClass('data_valid');
			$(this).closest('div.payment_item').click();
		}
	});
	$('select#otrans_type').change();

	var otrans_input_handler = function(e) {
		var any_empty = false;
		$('.payone_otrans_data input[type="text"]:visible').each(function() {
			if($(this).val() == '') { any_empty = true;	}
		});
		//$('div.continue_button').toggle(!any_empty);
		if(any_empty == true) {
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
		var current_block = $('div.module_option_checked');
		if(!response || typeof response != 'object' || response.status != 'VALID') {
			// error occurred
			var errormessage = 'ERROR';
			if(typeof response.customermessage == 'string') {
				errormessage = response.customermessage;
			}
			$('p.p1_error', current_block).html(errormessage);
			$('p.p1_error', current_block).show();
			current_block.closest('div.payment_item').removeClass('data_valid');
			current_block.get(0).scrollIntoView();
		}
		else {
			$('p.p1_error', current_block).hide();
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
			current_block.closest('div.payment_item').addClass('data_valid');
			$('div.p1_continue_button').remove();
			$('div.continue_button').show();
			$('div.continue_button a').click();
		}
	}

	$('.payone_otrans_data button.p1_checkdata').click(function(e) {
		e.preventDefault();
		var datablock = $(this).closest('.payone_paydata');
		var input_bankaccount = $('input[name="otrans_accnum"]', datablock).val();
		var input_bankcode = $('input[name="otrans_bankcode"]', datablock).val();
		var input_bankcountry = $('input[name="otrans_country"]').val();

		var pg_config = p1_otrans_config;
		var pg = new PAYONE.Gateway(pg_config, pg_callback_otrans);
		var data = {
			bankaccount: input_bankaccount,
			bankcode: input_bankcode,
			bankcountry: input_bankcountry,
		};
		if(p1_debug) { console.log(data); }
		pg.call(data);
	});



	/*
	** ELV
	*/

	$('table.payone_elv_data').delegate('select[name="p1_elv_country"]', 'change', function(e) {
		var selected_iso_2 = $(this).val();
		var only_de_rows = $('tr.only_de', $(this).closest('table'));
		if(selected_iso_2 == 'DE') {
			only_de_rows.show('fast');
		}
		else {
			only_de_rows.hide('fast');
		}
	});
	$('table.payone_elv_data select[name="p1_elv_country"]').change();

	$('table.payone_elv_data').delegate('tr.sepadata input[type="text"]', 'keyup', function(e) {
		var paydata_table = $(this).closest('table.payone_paydata');
		var iban = $('input[name="p1_elv_iban"]', paydata_table).val();
		var bic = $('input[name="p1_elv_bic"]', paydata_table).val();
		var sepa_length = iban.length + bic.length;
		if(sepa_length > 0) {
			$('tr.only_de input', paydata_table).attr('disabled', 'disabled');
		}
		else {
			$('tr.only_de input', paydata_table).removeAttr('disabled');
		}
	});

	$('table.payone_elv_data').delegate('tr.only_de input[type="text"]', 'keyup', function(e) {
		var paydata_table = $(this).closest('table.payone_paydata');
		var accountnumber = $('input[name="p1_elv_accountnumber"]', paydata_table).val();
		var bankcode = $('input[name="p1_elv_bankcode"]', paydata_table).val();
		var accbank_length = accountnumber.length + bankcode.length;
		if(accbank_length > 0) {
			$('tr.sepadata input', paydata_table).attr('disabled', 'disabled');
		}
		else {
			$('tr.sepadata input', paydata_table).removeAttr('disabled');
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
			$('p.p1_error', current_block).hide();
			$('table.payone_elv_data').hide();
			$('div.p1_finaldata_elv').show();
			// $('td.final_elv_accountholder').html($('input#p1_elv_accountholder').val());
			$('td.final_elv_country').html($('select#p1_elv_country option').filter(':selected').html());
			$('td.final_elv_accountnumber').html($('input#p1_elv_accountnumber').val());
			$('td.final_elv_bankcode').html($('input#p1_elv_bankcode').val());
			$('td.final_elv_iban').html($('input#p1_elv_iban').val());
			$('td.final_elv_bic').html($('input#p1_elv_bic').val());
			current_block.closest('div.payment_item').addClass('data_valid');
			$('div.p1_continue_button').remove();
			// $('div.continue_button').show();
			$('div.continue_button a').click();
		}
	}

	var pg_callback_elv_none = function(data) {
		var current_block = $('div.module_option_checked');
		$('p.p1_error', current_block).hide();
		$('table.payone_elv_data').hide();
		$('div.p1_finaldata_elv').show();
		$('td.final_elv_country').html($('select#p1_elv_country option').filter(':selected').html());
		$('td.final_elv_accountnumber').html($('input#p1_elv_accountnumber').val());
		$('td.final_elv_bankcode').html($('input#p1_elv_bankcode').val());
		$('td.final_elv_iban').html($('input#p1_elv_iban').val());
		$('td.final_elv_bic').html($('input#p1_elv_bic').val());
		current_block.closest('div.payment_item').addClass('data_valid');
		$('div.p1_continue_button').remove();
		$('div.continue_button a').click();
	}

	$('.payone_elv_data button.p1_checkdata').click(function(e) {
		e.preventDefault();
		var datablock = $(this).closest('.payone_paydata');
		// var input_accountholder = $('input[name="p1_elv_accountholder"]', datablock).val();
		var input_bankcountry = $('select[name="p1_elv_country"] option').filter(':selected').val();
		var input_accountnumber = $('input[name="p1_elv_accountnumber"]', datablock).val();
		var input_bankcode = $('input[name="p1_elv_bankcode"]', datablock).val();
		var input_iban = $('input[name="p1_elv_iban"]', datablock).val();
		var input_bic = $('input[name="p1_elv_bic"]', datablock).val();

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

		if(p1_elv_checkmode == 'none') {
			pg_callback_elv_none(data);
		}
		else {
			var pg_config = p1_elv_config;
			var pg = new PAYONE.Gateway(pg_config, pg_callback_elv);

			if(p1_debug) { console.log(data); }
			pg.call(data);
		}
	});

	/*
	** SafeInvoice
	 */
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

	if($('table.payone_safeinv_data').length > 0)
	{
		_initSafeInv();
	}

});