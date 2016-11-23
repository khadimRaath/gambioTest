/* ButtonWishListToCartHandler.js <?php
#   --------------------------------------------------------------
#   ButtonWishListToCartHandler.js 2014-11-12 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') != false)
{
?>*/
function ButtonWishListToCartHandler(){this.init_binds=function(){if(fb)console.log('ButtonWishListToCartHandler init_binds');$('.button_wish_list_to_cart').die('click');$('.button_wish_list_to_cart').live('click',function(){if(fb)console.log('.button_wish_list_to_cart click');var coo_quantity_checker=new GMOrderQuantityChecker(),t_no_error=coo_quantity_checker.check_wishlist();if(t_no_error){var t_no_error2=false;$('.wishlist_checkbox').each(function(){if($(this).prop('checked')==true){t_no_error2=true}});if(t_no_error2==false){alert('<?php echo GM_WISHLIST_NOTHING_CHECKED; ?>')}}if(t_no_error&&t_no_error2){if(typeof(coo_cart_wishlist_manager)=='object'){coo_cart_wishlist_manager.wishlist_to_cart()}document.cart_quantity.submit_target.value="wishlist";var t_target=document.cart_quantity.action;t_target=t_target.replace(/update_product/,"wishlist_to_cart");document.cart_quantity.action=t_target;document.cart_quantity.submit()}return false})};this.init_binds()}
/*<?php
}
else
{
?>*/
function ButtonWishListToCartHandler()
{
	this.init_binds = function()
	{
		if(fb)console.log('ButtonWishListToCartHandler init_binds');

		$('.button_wish_list_to_cart').die('click');
		$('.button_wish_list_to_cart').live('click', function()
		{
			if(fb)console.log('.button_wish_list_to_cart click');

			var coo_quantity_checker = new GMOrderQuantityChecker();
			var t_no_error = coo_quantity_checker.check_wishlist();
			if(t_no_error)
			{
				var t_no_error2 = false;
				$('.wishlist_checkbox').each(function()
				{
					if($(this).prop('checked') == true)
					{
						t_no_error2 = true;
					}
				});
				if(t_no_error2 == false){
					alert('<?php echo GM_WISHLIST_NOTHING_CHECKED; ?>');
				}
			}

			if(t_no_error && t_no_error2)
			{
				if(typeof(coo_cart_wishlist_manager) == 'object')
				{
					coo_cart_wishlist_manager.wishlist_to_cart();
				}

				document.cart_quantity.submit_target.value = "wishlist";
				var t_target = document.cart_quantity.action;
				t_target = t_target.replace(/update_product/, "wishlist_to_cart");
				document.cart_quantity.action = t_target;
				document.cart_quantity.submit();
			}

			return false;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/

