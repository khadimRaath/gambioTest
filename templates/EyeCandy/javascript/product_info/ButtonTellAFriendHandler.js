/* ButtonTellAFriendHandler.js <?php
#   --------------------------------------------------------------
#   ButtonTellAFriendHandler.js 2013-11-14 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') != false)
{
?>*/
function ButtonTellAFriendHandler(){if(fb)console.log('ButtonTellAFriendHandler ready');var coo_this=this;this.init_binds=function(){if(fb)console.log('ButtonTellAFriendHandler init_binds');$('.button_tell_a_friend').die('click');$('.button_tell_a_friend').live('click',function(){if(fb)console.log('.button_tell_a_friend click');coo_this.get_form();return false})};this.get_form=function(){$('.wrap_shop').append('<div id="gm_show_tell_a_friend"></div>');$('#gm_show_tell_a_friend').css({position:'absolute',left:'0px',top:'60px',width:'100%'});$('#gm_show_tell_a_friend').load('request_port.php?module=TellAFriend&action=get_form&id='+$("#gm_products_id").attr('value')+'&XTCsid='+gm_session_id,function(tell_a_friend_form){if(typeof(ButtonSendTellAFriendHandler)!='undefined'){var coo_button_send_tell_a_friend_handler=new ButtonSendTellAFriendHandler();if(typeof(window.showRecaptcha)=='function'){window.setTimeout("showRecaptcha('captcha_wrapper')",500)}}});if(navigator.appVersion.match(/MSIE [0-6]\./)){$('.lightbox_visibility_hidden').css({visibility:'hidden'})}gmLightBox.load_box('#gm_show_tell_a_friend');$('#menubox_gm_scroller').css({display:'none'});if($(document).height()>$('#popup_box').height()){var pt_height=$(document).height()}else{var pt_height=$('#popup_box').height()+200}$('#__dimScreen').css({height:pt_height+'px'})};this.init_binds()}
/*<?php
}
else
{
?>*/
function ButtonTellAFriendHandler()
{
	if(fb)console.log('ButtonTellAFriendHandler ready');

	var coo_this = this;

	this.init_binds = function()
	{
		if(fb)console.log('ButtonTellAFriendHandler init_binds');

		$('.button_tell_a_friend').die('click');
		$('.button_tell_a_friend').live('click', function()
		{
			if(fb)console.log('.button_tell_a_friend click');
			
			coo_this.get_form();
			
			return false;
		});
	}


	this.get_form = function(){

		$('.wrap_shop').append('<div id="gm_show_tell_a_friend"></div>');
		$('#gm_show_tell_a_friend').css(
		{
			position: 	'absolute',
			left: 			'0px',
			top: 				'60px',
			width: 			'100%'
		});

		$('#gm_show_tell_a_friend').load('request_port.php?module=TellAFriend&action=get_form&id=' + $("#gm_products_id").attr('value') + '&XTCsid=' + gm_session_id,
				function(tell_a_friend_form){
					if(typeof(ButtonSendTellAFriendHandler) != 'undefined')
					{
						var coo_button_send_tell_a_friend_handler = new ButtonSendTellAFriendHandler();
						if (typeof(window.showRecaptcha) == 'function')
						{
							window.setTimeout("showRecaptcha('captcha_wrapper')", 500);
						}
					}
				}
		);

		if (navigator.appVersion.match(/MSIE [0-6]\./)) {
			$('.lightbox_visibility_hidden').css(
			{
				visibility: 	'hidden'
			});
		}

		gmLightBox.load_box('#gm_show_tell_a_friend');
		// BOF MOD by PT
		$('#menubox_gm_scroller').css({
			display: 'none'
		});

		if($(document).height() > $('#popup_box').height()) {
			var pt_height = $(document).height();
		} else {
			var pt_height = $('#popup_box').height()+ 200;
		}
		$('#__dimScreen').css({ height: pt_height + 'px'});
		// EOF MOD by PT
	}
	
	this.init_binds();
}
/*<?php
}
?>*/