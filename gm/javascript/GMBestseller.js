/* GMBestseller.js <?php
#   --------------------------------------------------------------
#   GMBestseller.js 2011-01-24 gambio
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
var config={sensitivity:1,interval:100,over:gm_open,timeout:100,out:gm_close};$('#menubox_best_sellers_body .box_load_bestseller').ready(function(){$(".box_load_bestseller").hoverIntent(config);});function gm_open(){$(this).find(".box_head").addClass('box_head_hover');$(this).find(".box_left").show('fast');}function gm_close(){$(this).find(".box_head_hover").removeClass('box_head_hover');$(this).find(".box_left").hide('fast');}
/*<?php
}
else
{
?>*/
var config = {
     sensitivity: 1, // number = sensitivity threshold (must be 1 or higher)
     interval: 100, // number = milliseconds for onMouseOver polling interval
     over: gm_open, // function = onMouseOver callback (REQUIRED)
     timeout: 100, // number = milliseconds delay before onMouseOut
     out: gm_close // function = onMouseOut callback (REQUIRED)
};
	$('#menubox_best_sellers_body .box_load_bestseller').ready(function() {
		$(".box_load_bestseller").hoverIntent( config )
	});

function gm_open(){
	$(this).find(".box_head").addClass('box_head_hover');
	$(this).find(".box_left").show('fast');
}
function gm_close(){
	$(this).find(".box_head_hover").removeClass('box_head_hover');
	$(this).find(".box_left").hide('fast');

}
/*<?php
}
?>*/



