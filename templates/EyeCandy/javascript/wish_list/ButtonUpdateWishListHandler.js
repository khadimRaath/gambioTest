/* ButtonUpdateWishListHandler.js <?php
#   --------------------------------------------------------------
#   ButtonUpdateWishListHandler.js 2013-05-14 gm
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
function ButtonUpdateWishListHandler(){this.init_binds=function(){if(fb)console.log('ButtonUpdateWishListHandler init_binds');$('.button_update_wish_list').die('click');$('.button_update_wish_list').live('click',function(){if(fb)console.log('.button_update_wish_list click');document.cart_quantity.submit_target.value="wishlist";var t_target=document.cart_quantity.action;t_target=t_target.replace(/update_product/,"update_wishlist");document.cart_quantity.action=t_target;document.cart_quantity.submit();return false});$('input.gm_cart_data').live('keyup',function(event){var t_keycode=(event.keyCode?event.keyCode:(event.which?event.which:event.charCode));if(t_keycode==13){document.cart_quantity.submit_target.value="wishlist";var t_target=document.cart_quantity.action;t_target=t_target.replace(/update_product/,"update_wishlist");document.cart_quantity.action=t_target;document.cart_quantity.submit();return false}})};this.init_binds()}
/*<?php
}
else
{
?>*/
function ButtonUpdateWishListHandler()
{
	this.init_binds = function()
	{
		if(fb)console.log('ButtonUpdateWishListHandler init_binds');

		$('.button_update_wish_list').die('click');
		$('.button_update_wish_list').live('click', function()
		{
			if(fb)console.log('.button_update_wish_list click');

			document.cart_quantity.submit_target.value = "wishlist";
			var t_target = document.cart_quantity.action;
			t_target = t_target.replace(/update_product/, "update_wishlist");
			document.cart_quantity.action = t_target;
			document.cart_quantity.submit();

			return false;
		});
		
		$('input.gm_cart_data').live('keyup', function(event)
		{
			var t_keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));

			// track enter key
			if(t_keycode == 13) // 13 keycode for enter key
			{
				document.cart_quantity.submit_target.value = "wishlist";
				var t_target = document.cart_quantity.action;
				t_target = t_target.replace(/update_product/, "update_wishlist");
				document.cart_quantity.action = t_target;
				document.cart_quantity.submit();

				return false;
			}
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/
