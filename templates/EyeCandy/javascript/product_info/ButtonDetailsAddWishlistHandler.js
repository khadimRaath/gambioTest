/* ButtonDetailsAddWishlistHandler.js <?php
#   --------------------------------------------------------------
#   ButtonDetailsAddWishlistHandler.js 2013-11-04 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function ButtonDetailsAddWishlistHandler(){if(fb)console.log('ButtonDetailsAddWishlistHandler ready');this.init_binds=function(){if(fb)console.log('ButtonDetailsAddWishlistHandler init_binds');if($('#gm_gprint').length==0){$('.button_details_add_wishlist').die('click');$('.button_details_add_wishlist').live('click',function(event){if(fb)console.log('.button_details_add_wishlist click');coo_dropdowns_listener.check_combi_status();gm_qty_check=new GMOrderQuantityChecker();if(gm_qty_check.check()){document.cart_quantity.submit_target.value="wishlist";document.cart_quantity.submit()}return false})}};this.init_binds()};
/*<?php
}
else
{
?>*/
function ButtonDetailsAddWishlistHandler()
{
	if(fb)console.log('ButtonDetailsAddWishlistHandler ready');
	
	this.init_binds = function()
	{
		if(fb)console.log('ButtonDetailsAddWishlistHandler init_binds');

		if($('#gm_gprint').length == 0)
		{
			$('.button_details_add_wishlist').die('click');
			$('.button_details_add_wishlist').live('click', function(event)
			{
				if(fb)console.log('.button_details_add_wishlist click');
				
				coo_dropdowns_listener.check_combi_status();
			
				gm_qty_check = new GMOrderQuantityChecker();
				if(gm_qty_check.check())
				{
					document.cart_quantity.submit_target.value = "wishlist";
					document.cart_quantity.submit();
				}
				
				return false;
			});
		}
	}

	this.init_binds();
}
/*<?php
}
?>*/