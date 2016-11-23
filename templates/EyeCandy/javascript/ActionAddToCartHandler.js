/* ActionAddToCartHandler.js <?php
#   --------------------------------------------------------------
#   ActionAddToCartHandler.js 2011-05-24 gambio
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
function ActionAddToCartHandler(){if(fb)console.log('ActionAddToCartHandler ready');this.init_binds=function(){if(fb)console.log('ActionAddToCartHandler init_binds');$('.action_add_to_cart').die('click');$('.action_add_to_cart').live('click',function(){if(fb)console.log('.action_add_to_cart click');if($(this).closest('form').length>0){var t_quantity_success=true;if($(this).closest('form').find('.gm_products_id:first').length>0){var t_products_id=$(this).closest('form').find('.gm_products_id:first').val();if(typeof(GMOrderQuantityChecker)=='function'){var coo_quantity_checker=new GMOrderQuantityChecker();t_quantity_success=coo_quantity_checker.check_listing(t_products_id)}}if(t_quantity_success){var t_operation_form=$(this).closest('form');coo_cart_control.submit_buy_now_form(t_operation_form)}}return false});$('.action_add_to_cart').closest('form').submit(function(){var t_quantity_success=true;if($(this).find('.gm_products_id:first').length>0){var t_products_id=$(this).find('.gm_products_id:first').val();if(typeof(GMOrderQuantityChecker)=='function'){var coo_quantity_checker=new GMOrderQuantityChecker();t_quantity_success=coo_quantity_checker.check_listing(t_products_id)}}if(t_quantity_success){coo_cart_control.submit_buy_now_form(this)}return false})};this.init_binds()}
/*<?php
}
else
{
?>*/
function ActionAddToCartHandler()
{
	if(fb)console.log('ActionAddToCartHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('ActionAddToCartHandler init_binds');

		$('.action_add_to_cart').die('click');
		$('.action_add_to_cart').live('click', function()
		{
			if(fb)console.log('.action_add_to_cart click');

			if($(this).closest('form').length > 0)
			{
				var t_quantity_success = true;

				if($(this).closest('form').find('.gm_products_id:first').length > 0)
				{
					var t_products_id = $(this).closest('form').find('.gm_products_id:first').val();
					if(typeof(GMOrderQuantityChecker) == 'function')
					{
						var coo_quantity_checker = new GMOrderQuantityChecker();
						t_quantity_success = coo_quantity_checker.check_listing(t_products_id);
					}
				}

				if(t_quantity_success)
				{
					var t_operation_form = $(this).closest('form');
					coo_cart_control.submit_buy_now_form(t_operation_form);
				}
			}
			
			return false;
		});

		$('.action_add_to_cart').closest('form').submit(function()
		{
			var t_quantity_success = true;

			if($(this).find('.gm_products_id:first').length > 0)
			{
				var t_products_id = $(this).find('.gm_products_id:first').val();
				if(typeof(GMOrderQuantityChecker) == 'function')
				{
					var coo_quantity_checker = new GMOrderQuantityChecker();
					t_quantity_success = coo_quantity_checker.check_listing(t_products_id);
				}
			}

			if(t_quantity_success)
			{
				coo_cart_control.submit_buy_now_form(this);
			}

			return false;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/