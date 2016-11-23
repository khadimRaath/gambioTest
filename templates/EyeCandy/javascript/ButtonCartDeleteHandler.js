/* ButtonCartDeleteHandler.js <?php
#   --------------------------------------------------------------
#   ButtonCartDeleteHandler.js 2012-02-09 gambio
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
function ButtonCartDeleteHandler(){$(document).ready(function(){if(fb)console.log('ButtonCartDeleteHandler ready');coo_button_cart_delete_handler.init_binds()});this.init_binds=function(){if(fb)console.log('ButtonCartDeleteHandler init_binds');$('.button_cart_delete').die('click');$('.button_cart_delete').live('click',function(event){if(fb)console.log('.button_cart_delete click');var t_delete_products_id=$(this).attr('rel');$('#field_cart_delete_products_id').attr('value',t_delete_products_id);if($(this).hasClass('wishlist_button')){if(fb)console.log('-- wishlist_button found');update_wishlist()}else{$('#cart_quantity').unbind('submit');$('#cart_quantity').submit()}return false})};}
/*<?php
}
else
{
?>*/
function ButtonCartDeleteHandler()
{
	$(document).ready(
		function()
		{
			if(fb)console.log('ButtonCartDeleteHandler ready');

			coo_button_cart_delete_handler.init_binds();
		}
	);


	this.init_binds = function()
	{
		if(fb)console.log('ButtonCartDeleteHandler init_binds');


		$('.button_cart_delete').die('click');
		$('.button_cart_delete').live('click', function(event)
		{
			if(fb)console.log('.button_cart_delete click');

			var t_delete_products_id = $(this).attr('rel');
			$('#field_cart_delete_products_id').attr('value', t_delete_products_id);

			if($(this).hasClass('wishlist_button'))
			{
				if(fb)console.log('-- wishlist_button found');
				update_wishlist();
			}
			else
			{
                                $('#cart_quantity').unbind('submit');
				$('#cart_quantity').submit();
			}
			return false;
		});
	}
}

/*<?php
}
?>*/