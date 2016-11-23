/* GMAttributeImages.js <?php
#   --------------------------------------------------------------
#   GMAttributeImages.js 2015-06-09 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2015 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
$(document).ready(function(){var attr_img=new GMAttributeImages();attr_img.get_attribute_images();$(".gm_attr_calc_input").bind("change",function(){if(!$(this).hasClass("properties_values_select_field")){var attr_img=new GMAttributeImages();attr_img.get_attribute_images()}})});function GMAttributeImages(){this.get_attribute_images=function(){var gm_options_ids='',gm_values_ids='';$('.gm_attr_calc_input').each(function(){if($(this).prop('checked')==true){gm_options_ids+=$(this).attr('name')+',';gm_values_ids+=$(this).val()+','}});if(gm_options_ids==''){$('.gm_attr_calc_input').each(function(){gm_options_ids+=$(this).attr('name')+',';gm_values_ids+=$(this).val()+','})}$('#gm_attribute_images').load('request_port.php?module=Attributes&action=attribute_images&options_ids='+gm_options_ids+'&values_ids='+gm_values_ids,function(attribute_images){$('#gm_attribute_images').html(attribute_images)})};}
/*<?php
}
else
{
?>*/
$(document).ready(function(){

	var attr_img = new GMAttributeImages();
	attr_img.get_attribute_images();

	$(".gm_attr_calc_input").bind("change", function(){
		if(!$(this).hasClass("properties_values_select_field")){
			var attr_img = new GMAttributeImages();
			attr_img.get_attribute_images();
		}
	});
});


function GMAttributeImages()
{
	this.get_attribute_images = function()
	{
		var gm_options_ids = '';
		var gm_values_ids = '';

		$('.gm_attr_calc_input').each(function()
		{
			if($(this).prop('checked') == true)
			{
				gm_options_ids += $(this).attr('name') + ',';
				gm_values_ids += $(this).val() + ',';
			}
		});

		if(gm_options_ids == '')
		{
			$('.gm_attr_calc_input').each(function()
			{
				gm_options_ids += $(this).attr('name') + ',';
				gm_values_ids += $(this).val() + ',';
			});
		}

		$('#gm_attribute_images').load('request_port.php?module=Attributes&action=attribute_images&options_ids=' + gm_options_ids + '&values_ids=' + gm_values_ids,
				function(attribute_images)
				{
					$('#gm_attribute_images').html(attribute_images);
				}
		);
	}
}
/*<?php
}
?>*/
