/* ShowLightBox.js <?php
#   --------------------------------------------------------------
#   ShowLightBox.js 2014-11-12 gambio
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
function ShowLightBox(){var v_previous_string='<?php echo $_SESSION["lightbox"]->previous; ?>',v_previous=false;if(v_previous_string=='true'){v_previous=true}this.open_box=function(p_coo_lightbox){if(typeof(p_coo_lightbox)=='undefined'){var p_coo_lightbox=gmLightBox}$('#main_inside').append('<div id="popup_box_container"><div id="popup_box"></div></div>');$('#popup_box_container').css({position:'absolute',left:'0px',top:'30px',width:'100%',zIndex:'1000'});$('#popup_box').css({width:'980px',marginLeft:'auto',marginRight:'auto'});$('#lightbox_content').show();var t_height=$('body').attr('offsetHeight');if(typeof t_height=='undefined')var t_height=$(document).attr('height');if(typeof t_height=='undefined')var t_height=$(document).height();var t_html=$('#lightbox_content').html();$('#lightbox_content').html('<?php echo GM_LIGHTBOX_PLEASE_WAIT; ?>');$('#lightbox_content').show();if(v_previous)p_coo_lightbox.load_box('#popup_box',0,false,t_height+200);else p_coo_lightbox.load_box('#popup_box',100,true,t_height+200);if($('#pre_black_container').attr('id')=='pre_black_container'){$('#pre_black_container').html('')}$('#popup_box').html(t_html);$('#menubox_gm_scroller').css({display:'none'});if($(document).height()>$('#popup_box').height()){var t_pt_height=$(document).height()}else{var t_pt_height=$('#popup_box').height()+200}$('#__dimScreen').css({height:t_pt_height+'px'});$('body').trigger('lightbox.loaded',[$('#lightbox_content')])};}$(document).ready(function(){coo_show_lightbox=new ShowLightBox();coo_show_lightbox.open_box()});
/*<?php
}
else
{
?>*/
function ShowLightBox()
{
	var v_previous_string = '<?php echo $_SESSION["lightbox"]->previous; ?>';
	var v_previous = false;
	if(v_previous_string == 'true')
	{
		v_previous = true;
	}

	this.open_box = function(p_coo_lightbox)
	{
		if(typeof(p_coo_lightbox) == 'undefined')
		{
			var p_coo_lightbox = gmLightBox;
		}

		$('#main_inside').append('<div id="popup_box_container"><div id="popup_box"></div></div>');
		$('#popup_box_container').css(
		{
			position: 	'absolute',
			left: 			'0px',
			top: 				'30px',
			width: 			'100%',
			zIndex:		'1000'
		});
		$('#popup_box').css(
		{
			width: 			'980px',
			marginLeft:		'auto',
			marginRight:	'auto'
		});

		$('#lightbox_content').show();

		var t_height = $('body').attr('offsetHeight'); //IE
		if(typeof t_height == 'undefined') var t_height = $(document).attr('height');	//firefox
		if(typeof t_height == 'undefined') var t_height = $(document).height(); //Opera

		var t_html = $('#lightbox_content').html();
		$('#lightbox_content').html('<?php echo GM_LIGHTBOX_PLEASE_WAIT; ?>');
		$('#lightbox_content').show();

		if(v_previous) p_coo_lightbox.load_box('#popup_box', 0, false, t_height + 200);
		else p_coo_lightbox.load_box('#popup_box', 100, true, t_height + 200);

		if($('#pre_black_container').attr('id') == 'pre_black_container')
		{
			$('#pre_black_container').html('');
		}

		$('#popup_box').html(t_html);

		// BOF MOD by PT
		$('#menubox_gm_scroller').css({
			display: 'none'
		});

		if($(document).height() > $('#popup_box').height()) {
			var t_pt_height = $(document).height();
		} else {
			var t_pt_height = $('#popup_box').height() + 200;
		}
		$('#__dimScreen').css({ height: t_pt_height + 'px'});
		// EOF MOD by PT

		$('body').trigger('lightbox.loaded', [$('#lightbox_content')]);
	}

}

$(document).ready(function()
{
	coo_show_lightbox = new ShowLightBox();
	coo_show_lightbox.open_box();
});
/*<?php
}
?>*/

/*<?php $_SESSION['lightbox']->set_actual('true'); ?>*/