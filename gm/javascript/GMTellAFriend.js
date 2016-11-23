/* --------------------------------------------------------------
   GMTellAFriend.js 2013-11-14 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
$(document).ready(function(){$("#gm_tell_a_friend, #gm_tell_a_friend_icon").click(function(){var tell_a_friend=new GMTellAFriend();tell_a_friend.get_form()})});function GMTellAFriend(){this.get_form=function(){$('body').append('<div id="gm_show_tell_a_friend"></div>');$('#gm_show_tell_a_friend').css({position:'absolute',left:'0px',top:'60px',width:'100%'});$('#gm_show_tell_a_friend').load('request_port.php?module=TellAFriend&action=get_form&id='+$("#gm_products_id").attr('value')+'&XTCsid='+gm_session_id,function(tell_a_friend_form){$('#gm_show_tell_a_friend').html(tell_a_friend_form);activate_form_styles()});if(navigator.appVersion.match(/MSIE [0-6]\./)){$('.lightbox_visibility_hidden').css({visibility:'hidden'})}gmLightBox.load_box('#gm_show_tell_a_friend');$('#menubox_gm_scroller').css({display:'none'});if($(document).height()>$('#popup_box').height()){var pt_height=$(document).height()}else{var pt_height=$('#popup_box').height()+200}$('#__dimScreen').css({height:pt_height+'px'})};this.send_form=function(){var inputs=[];$('.tell_a_friend_fields').each(function(){inputs.push(this.name+'='+escape(this.value))});jQuery.ajax({data:inputs.join('&'),url:'request_port.php?module=TellAFriend&action=get_form&id='+$("#gm_products_id").attr('value')+'&XTCsid='+gm_session_id,type:"POST",success:function(sent_success){$("#gm_show_tell_a_friend").html(sent_success);activate_form_styles()}})};}
/*<?php
}
else
{
?>*/
$(document).ready(function(){

	$("#gm_tell_a_friend, #gm_tell_a_friend_icon").click(function()
	{
		var tell_a_friend = new GMTellAFriend();
		tell_a_friend.get_form();
	});
});

function GMTellAFriend(){

	this.get_form = function(){

//		$('.wrap_site').append('<div id="gm_show_tell_a_friend"></div>');
		$('body').append('<div id="gm_show_tell_a_friend"></div>');
		$('#gm_show_tell_a_friend').css(
		{
			position: 	'absolute',
			left: 			'0px',
			top: 				'60px',
			width: 			'100%'
		});

		$('#gm_show_tell_a_friend').load('request_port.php?module=TellAFriend&action=get_form&id=' + $("#gm_products_id").attr('value') + '&XTCsid=' + gm_session_id,
				function(tell_a_friend_form){
					$('#gm_show_tell_a_friend').html(tell_a_friend_form);
					activate_form_styles();
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

	this.send_form = function(){
		var inputs = [];

		$('.tell_a_friend_fields').each(function(){inputs.push(this.name + '=' + escape(this.value)); });

		jQuery.ajax({
			data: 		inputs.join('&'),
			url: 			'request_port.php?module=TellAFriend&action=get_form&id=' + $("#gm_products_id").attr('value') + '&XTCsid=' + gm_session_id,
			type: 		"POST",
			success: 	function(sent_success) {
    		$("#gm_show_tell_a_friend").html(sent_success);
				activate_form_styles();
  		}
		});
	}
}
/*<?php
}
?>*/