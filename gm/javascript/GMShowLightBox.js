/* GMShowLightBox.js <?php
#   --------------------------------------------------------------
#   GMShowLightBox.js 2011-01-24 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function GMShowLightBox(){var previous_string='<?php echo $_SESSION["lightbox"]->previous; ?>';var previous=false;if(previous_string=='true'){previous=true;}this.open_box=function(){$('body').append('<div id="popup_box"></div>');$('#popup_box').css({position:'absolute',left:'0px',top:'60px',width:'100%'});$('#lightbox_content').show();var height=$('body').attr('offsetHeight');if(typeof height=='undefined')var height=$(document).attr('height');if(typeof height=='undefined')var height=$(document).height();if(navigator.appVersion.match(/MSIE [0-6]\./)){$('.lightbox_visibility_hidden').css({visibility:'hidden'});}var html=$('#lightbox_content').html();$('#lightbox_content').html('<?php echo GM_LIGHTBOX_PLEASE_WAIT; ?>');if(previous)gmLightBox.load_box('#popup_box',0,false,height+200);else gmLightBox.load_box('#popup_box',100,true,height+200);if($('#pre_black_container').attr('id')=='pre_black_container')$('#pre_black_container').html('');$('#popup_box').html(html);$('#menubox_gm_scroller').css({display:'none'});if($(document).height()>$('#popup_box').height()){var pt_height=$(document).height();}else{var pt_height=$('#popup_box').height()+200;}$('#__dimScreen').css({height:pt_height+'px'});};}$(document).ready(function(){gmShowLightBox=new GMShowLightBox();gmShowLightBox.open_box();activate_form_styles();});
/*<?php
}
else
{
?>*/
function GMShowLightBox()
{
	var previous_string = '<?php echo $_SESSION["lightbox"]->previous; ?>';
	var previous = false;
	if(previous_string == 'true')
	{
		previous = true;
	}

	this.open_box = function()
	{

//		$('.wrap_site').append('<div id="popup_box"></div>');
		$('body').append('<div id="popup_box"></div>');
		$('#popup_box').css(
		{
			position: 	'absolute',
			left: 			'0px',
			top: 				'60px',
			width: 			'100%'
		});

		$('#lightbox_content').show();

		var height = $('body').attr('offsetHeight'); //IE
		if(typeof height == 'undefined') var height = $(document).attr('height');	//firefox
		if(typeof height == 'undefined') var height = $(document).height(); //Opera

		if (navigator.appVersion.match(/MSIE [0-6]\./)) {
			$('.lightbox_visibility_hidden').css(
			{
				visibility: 	'hidden'
			});
		}

		var html = $('#lightbox_content').html();
		$('#lightbox_content').html('<?php echo GM_LIGHTBOX_PLEASE_WAIT; ?>');

		if(previous) gmLightBox.load_box('#popup_box', 0, false, height+200);
		else gmLightBox.load_box('#popup_box', 100, true, height+200);

		if($('#pre_black_container').attr('id') == 'pre_black_container') $('#pre_black_container').html('');

		$('#popup_box').html(html);

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

}

$(document).ready(function() {
	gmShowLightBox = new GMShowLightBox();
	gmShowLightBox.open_box();

	activate_form_styles();
});
/*<?php
}
?>*/

/*<?php $_SESSION['lightbox']->set_actual('true'); ?>*/