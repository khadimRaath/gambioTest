/* gm_form_styles.js <?php
#   --------------------------------------------------------------
#   gm_form_styles.js 2014-08-18 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
$(document).ready(function(){activate_form_styles()});function activate_form_styles(){$(".cell_right").click(function(){var input_id=$(this).attr("id");$(".cell_right").each(function(){var input_id2=$(this).attr("id");if(input_id2!=input_id){$("#left_"+input_id2).removeClass("cell_left_bold");$("#"+input_id2+" .gm_mb_input").removeClass("active_input");$("#"+input_id2+" .gm_mb_input").addClass("inactive_input")}});$("#left_"+input_id).addClass("cell_left_bold");if($("#"+input_id+" .gm_mb_input").hasClass("inactive_input")){$("#"+input_id+" .gm_mb_input").removeClass("inactive_input");$("#"+input_id+" .gm_mb_input").addClass("active_input")}});$(".cell_right").keyup(function(){var input_id=$(this).attr("id");$(".cell_right").each(function(){var input_id2=$(this).attr("id");if(input_id2!=input_id){$("#left_"+input_id2).removeClass("cell_left_bold");$("#"+input_id2+" .gm_mb_input").removeClass("active_input");$("#"+input_id2+" .gm_mb_input").addClass("inactive_input")}});$("#left_"+input_id).addClass("cell_left_bold");if($("#"+input_id+" .gm_mb_input").hasClass("inactive_input")){$("#"+input_id+" .gm_mb_input").removeClass("inactive_input");$("#"+input_id+" .gm_mb_input").addClass("active_input")}});$(".module_option, .module_option_checked").click(function(){var input_id=$(this).attr("id");$(".module_option_checked").each(function(){$(this).removeClass("module_option_checked");$(this).addClass("module_option")});$('#'+input_id+'_radio > input[type=radio]').prop('checked',true);$(".module_option_price, .module_option_price_bold").each(function(){$(this).removeClass("module_option_price_bold");$(this).addClass("module_option_price")});$('#'+input_id+'_price').addClass("module_option_price_bold");$(this).addClass("module_option_checked")});$('#checkout_shipping #footer, #gm_checkout_payment #footer, #gm_checkout_confirmation #footer').click(function(){gm_confirm=confirm('<?php echo GM_CONFIRM_CLOSE_LIGHTBOX; ?>');if(gm_confirm==true){document.location.href="index.php?XTCsid="+gm_session_id}});$('#checkout_success #footer').click(function(){document.location.href="index.php?XTCsid="+gm_session_id});var gm_input_val=$('#gm_gift_input').val();$('#gm_gift_input').click(function(){if($('#gm_gift_input').val()==gm_input_val||$('#gm_gift_input').val()=='')$('#gm_gift_input').val('')});$('#gm_gift_input').blur(function(){if($('#gm_gift_input').val()==gm_input_val||$('#gm_gift_input').val()=='')$('#gm_gift_input').val(gm_input_val)});$('.icon_lightbox_close').die('click');$('.icon_lightbox_close').live('click',function(){if(fb)console.log('.icon_lightbox_close click');if(typeof(gmLightBox)!='undefined'&&($(this).attr('onclick').length==0||String($(this).attr('onclick')).search('close_iframe_box')==-1)){gmLightBox.close_box()}return false})};
/*<?php
}
else
{
?>*/
$(document).ready(function() {

	activate_form_styles();

});

function activate_form_styles(){

		$(".cell_right").click(function() {

		var input_id = $(this).attr("id");

		$(".cell_right").each(function() {
			var input_id2 = $(this).attr("id");

			if(input_id2 != input_id){
				$("#left_" + input_id2).removeClass("cell_left_bold");
				$("#" + input_id2 + " .gm_mb_input").removeClass("active_input");
				$("#" + input_id2 + " .gm_mb_input").addClass("inactive_input");
			}
		});

		$("#left_" + input_id).addClass("cell_left_bold");
		if($("#" + input_id + " .gm_mb_input").hasClass("inactive_input")){
			$("#" + input_id + " .gm_mb_input").removeClass("inactive_input");
			$("#" + input_id + " .gm_mb_input").addClass("active_input");
		}

	});


	$(".cell_right").keyup(function() {

		var input_id = $(this).attr("id");

		$(".cell_right").each(function() {
			var input_id2 = $(this).attr("id");

			if(input_id2 != input_id){
				$("#left_" + input_id2).removeClass("cell_left_bold");
				$("#" + input_id2 + " .gm_mb_input").removeClass("active_input");
				$("#" + input_id2 + " .gm_mb_input").addClass("inactive_input");
			}
		});

		$("#left_" + input_id).addClass("cell_left_bold");
		if($("#" + input_id + " .gm_mb_input").hasClass("inactive_input")){
			$("#" + input_id + " .gm_mb_input").removeClass("inactive_input");
			$("#" + input_id + " .gm_mb_input").addClass("active_input");
		}

	});


	$(".module_option, .module_option_checked").click(function() {

		var input_id = $(this).attr("id");

		$(".module_option_checked").each(function() {
			$(this).removeClass("module_option_checked");
			$(this).addClass("module_option");
		});

		$('#' + input_id + '_radio > input[type=radio]').prop('checked', true);

		$(".module_option_price, .module_option_price_bold").each(function() {
			$(this).removeClass("module_option_price_bold");
			$(this).addClass("module_option_price");
		});

		$('#' + input_id + '_price').addClass("module_option_price_bold");
		$(this).addClass("module_option_checked");

	});


	$('#checkout_shipping #footer, #gm_checkout_payment #footer, #gm_checkout_confirmation #footer').click(function() {
		gm_confirm = confirm('<?php echo GM_CONFIRM_CLOSE_LIGHTBOX; ?>');
		if(gm_confirm == true){
			document.location.href = "index.php?XTCsid=" + gm_session_id;
		}
	});

	$('#checkout_success #footer').click(function() {
		document.location.href = "index.php?XTCsid=" + gm_session_id;
	});

	var gm_input_val = $('#gm_gift_input').val();

		$('#gm_gift_input').click(function() {
		if($('#gm_gift_input').val() == gm_input_val || $('#gm_gift_input').val() == '' )
			$('#gm_gift_input').val('');
	});

	$('#gm_gift_input').blur(function() {
		if($('#gm_gift_input').val() == gm_input_val || $('#gm_gift_input').val() == '' )
		$('#gm_gift_input').val(gm_input_val);

	});

	$('.icon_lightbox_close').die('click');
	$('.icon_lightbox_close').live('click', function()
	{
		if(fb)console.log('.icon_lightbox_close click');
		
		if(typeof(gmLightBox) != 'undefined' && ($(this).attr('onclick').length == 0 || String($(this).attr('onclick')).search('close_iframe_box') == -1))
		{
			gmLightBox.close_box();
		}

		return false;
	});
}
/*<?php
}
?>*/
