/* gm_product_details.js <?php
#   --------------------------------------------------------------
#   gm_product_details.js 2014-08-25 tb@gambio
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
var addthis_config={ui_delay:200,services_exclude:'print'};$(document).ready(function(){$("#product_info #print, #product_info #print_icon, .details .button_print").click(function(){window.open('<?php echo HTTP_SERVER . DIR_WS_CATALOG . "print_product_info.php"; ?>?products_id='+$("#gm_products_id").attr('value')+'&XTCsid='+gm_session_id,'popup','toolbar=0, width=640, height=600');return false});if($('#tabbed_description_part').length>0){if(fb)console.log('tabbed_description_part ready');$("#tabbed_description_part").tabs()}else{if($('#description-1').length>0){var t_description=$('#description-1').html();if(t_description.toLowerCase().replace(/\s/g,'')!='<p><br></p>'&&t_description.toLowerCase().replace(/\s/g,'')!='<p></p>'){$('#description-1').html('');$('#description-1').append('<div id="tabbed_description_part"><ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" style="overflow:hidden"><li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#tab_fragment_0"><span><?php echo PRODUCT_DESCRIPTION; ?></span></a></li></ul><div id="tab_fragment_0" class="ui-tabs-panel ui-widget-content ui-corner-bottom">'+t_description+'</div></div>')}}}$("#tabbed_description_part ul.ui-tabs-nav li a").live('click',function(){return false});$('#gm_show_tell_a_friend .icon_lightbox_close').die('click');$('#gm_show_tell_a_friend .icon_lightbox_close').live('click',function(){if(fb)console.log('.icon_lightbox_close click');if(typeof(gmLightBox)!='undefined'){gmLightBox.close_box(undefined,"#gm_show_tell_a_friend")}return false})});
/*<?php
}
else
{
?>*/
var addthis_config = {ui_delay: 200,services_exclude: 'print'};

$(document).ready(function()
{
	$("#product_info #print, #product_info #print_icon, .details .button_print").click(function()
	{
		window.open('<?php echo HTTP_SERVER . DIR_WS_CATALOG . "print_product_info.php"; ?>?products_id=' + $("#gm_products_id").attr('value') + '&XTCsid=' + gm_session_id, 'popup', 'toolbar=0, width=640, height=600');

		return false;
	});

	if($('#tabbed_description_part').length > 0)
	{
		if(fb)console.log('tabbed_description_part ready');
		$("#tabbed_description_part").tabs();
	}
	else
	{
		// TODO
		if($('#description-1').length > 0)
		{
			var t_description = $('#description-1').html();
			if(t_description.toLowerCase().replace(/\s/g, '') != '<p><br></p>' && t_description.toLowerCase().replace(/\s/g, '') != '<p></p>')
			{
				$('#description-1').html('');
				$('#description-1').append('<div id="tabbed_description_part"><ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" style="overflow:hidden"><li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#tab_fragment_0"><span><?php echo PRODUCT_DESCRIPTION; ?></span></a></li></ul><div id="tab_fragment_0" class="ui-tabs-panel ui-widget-content ui-corner-bottom">' + t_description + '</div></div>');
			}

			//$("#tabbed_description_part").tabs();
			//$("#tabbed_description_part").tabs('add', 'TEST');
		}
	}
	$("#tabbed_description_part ul.ui-tabs-nav li a").live('click', function()
	{
		return false;
	});

	$('#gm_show_tell_a_friend .icon_lightbox_close').die('click');
	$('#gm_show_tell_a_friend .icon_lightbox_close').live('click', function()
	{
		if(fb)console.log('.icon_lightbox_close click');

		if(typeof(gmLightBox) != 'undefined')
		{
			gmLightBox.close_box(undefined, "#gm_show_tell_a_friend");
		}

		return false;
	});
});

/*<?php
}
?>*/
