/* GMGMotion.js <?php
#   --------------------------------------------------------------
#   GMGMotion.js 2011-03-08 gambio
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
var t_gm_gmotion_json_data='<?php echo $coo_gm_gmotion->json_crossslide($_GET["products_id"]); ?>';var coo_gm_gmotion_data=new Object();coo_gm_gmotion_data=eval('('+t_gm_gmotion_json_data+')');$(document).ready(function(){$('#crossslide_'+$('#gm_products_id').val()).crossSlide({fade:1},coo_gm_gmotion_data['SMALL']);});
/*<?php
}
else
{
?>*/
var t_gm_gmotion_json_data = '<?php echo $coo_gm_gmotion->json_crossslide($_GET["products_id"]); ?>';
var coo_gm_gmotion_data = new Object();
coo_gm_gmotion_data = eval('(' + t_gm_gmotion_json_data + ')');

$(document).ready(function()
{
	$('#crossslide_' + $('#gm_products_id').val() ).crossSlide({ fade: 1 }, coo_gm_gmotion_data['SMALL']);
});
/*<?php
}
?>*/

