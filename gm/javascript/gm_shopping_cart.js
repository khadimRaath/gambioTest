/* gm_shopping_cart.js <?php
#   --------------------------------------------------------------
#   gm_shopping_cart.js 2011-01-24 gambio
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
var gm_qty_changed=false;function gm_qty_is_changed(old_qty,new_qty,gm_message){if(new_qty!=old_qty){$("#gm_checkout").attr('href',"javascript:alert('"+gm_message+"')");}return;}
/*<?php
}
else
{
?>*/
var gm_qty_changed = false;

function gm_qty_is_changed(old_qty, new_qty, gm_message) {

	if(new_qty != old_qty) {
		$("#gm_checkout").attr('href', "javascript:alert('" + gm_message + "')");
	}
	return;
}
/*<?php
}
?>*/

