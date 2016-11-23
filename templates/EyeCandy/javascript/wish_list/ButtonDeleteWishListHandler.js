/* ButtonDeleteWishListHandler.js <?php
#   --------------------------------------------------------------
#   ButtonDeleteWishListHandler.js 2011-01-24 gambio
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
function ButtonDeleteWishListHandler(){this.init_binds=function(){if(fb)console.log('ButtonDeleteWishListHandler init_binds');$('.button_delete_wish_list').die('click');$('.button_delete_wish_list').live('click',function(){if(fb)console.log('.button_delete_wish_list click');if(typeof(coo_cart_wishlist_manager)=='object'){coo_cart_wishlist_manager.update_wishlist()}if($(".wishlist_checkbox:checked").length>0){document.cart_quantity.submit_target.value="wishlist";document.cart_quantity.submit()}else{alert($(this).attr("rel"))}return false})};this.init_binds()}
/*<?php
}
else
{
?>*/
function ButtonDeleteWishListHandler()
{
	this.init_binds = function()
	{
		if(fb)console.log('ButtonDeleteWishListHandler init_binds');

		$('.button_delete_wish_list').die('click');
		$('.button_delete_wish_list').live('click', function()
		{
			if(fb)console.log('.button_delete_wish_list click');

			if(typeof(coo_cart_wishlist_manager) == 'object')
			{
				coo_cart_wishlist_manager.update_wishlist();

			}
			
			if($(".wishlist_checkbox:checked").length > 0){
				document.cart_quantity.submit_target.value = "wishlist";
				document.cart_quantity.submit();
			}else{
				alert($(this).attr("rel"));
			}
			
			return false;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/

