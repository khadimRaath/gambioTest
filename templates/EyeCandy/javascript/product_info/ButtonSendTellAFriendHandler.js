/* ButtonSendTellAFriendHandler.js <?php
#   --------------------------------------------------------------
#   ButtonSendTellAFriendHandler.js 2016-08-26
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2016 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function ButtonSendTellAFriendHandler(){if(fb)console.log('ButtonSendTellAFriendHandler ready');var coo_this=this;this.init_binds=function(){if(fb)console.log('ButtonSendTellAFriendHandler init_binds');$('.button_send_tell_a_friend').die('click');$('.button_send_tell_a_friend').live('click',function(){if(fb)console.log('.button_send_tell_a_friend click');coo_this.send_form();return false})};this.send_form=function(){jQuery.ajax({data:$('#ask_product_question').serialize(),url:'request_port.php?module=TellAFriend&action=get_form&id='+$("#gm_products_id").attr('value')+'&XTCsid='+gm_session_id,type:"POST",success:function(sent_success){$("#gm_show_tell_a_friend").html(sent_success);if(typeof(window.showRecaptcha)=='function'){window.setTimeout("showRecaptcha('captcha_wrapper')",500)}if(typeof(ButtonSendTellAFriendHandler)!='undefined'){var coo_button_send_tell_a_friend_handler=new ButtonSendTellAFriendHandler()}}})};this.init_binds()}
/*<?php
}
else
{
?>*/
function ButtonSendTellAFriendHandler()
{
	if(fb)console.log('ButtonSendTellAFriendHandler ready');

	var coo_this = this;

	this.init_binds = function()
	{
		if(fb)console.log('ButtonSendTellAFriendHandler init_binds');

		$('.button_send_tell_a_friend').die('click');
		$('.button_send_tell_a_friend').live('click', function()
		{
			if(fb)console.log('.button_send_tell_a_friend click');
			
			coo_this.send_form();
			
			return false;
		});
	}

	this.send_form = function()
	{
		jQuery.ajax({
			data: 		$('#ask_product_question').serialize(),
			url: 		'request_port.php?module=TellAFriend&action=get_form&id=' + $("#gm_products_id").attr('value') + '&XTCsid=' + gm_session_id,
			type: 		"POST",
			success: 	function(sent_success) {
				$("#gm_show_tell_a_friend").html(sent_success);
				if (typeof(window.showRecaptcha) == 'function')
				{
					window.setTimeout("showRecaptcha('captcha_wrapper')", 500);
				}
				
				if(typeof(ButtonSendTellAFriendHandler) != 'undefined')
				{
					var coo_button_send_tell_a_friend_handler = new ButtonSendTellAFriendHandler();
				}
			}
		});
	};

	this.init_binds();
}
/*<?php
}
?>*/