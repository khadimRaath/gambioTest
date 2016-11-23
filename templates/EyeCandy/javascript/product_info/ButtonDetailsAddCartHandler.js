/* ButtonDetailsAddCartHandler.js <?php
#   --------------------------------------------------------------
#   ButtonDetailsAddCartHandler.js 2012-10-24 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2012 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
function ButtonDetailsAddCartHandler(){if(fb)console.log('ButtonDetailsAddCartHandler ready');this.init_binds=function(){if(fb)console.log('ButtonDetailsAddCartHandler init_binds');$('.button_details_add_cart').die('click');$('.button_details_add_cart').live('click',function(event){if(fb)console.log('.button_details_add_cart click');coo_dropdowns_listener.check_combi_status();gm_qty_check=new GMOrderQuantityChecker();if(gm_qty_check.check()){var t_operation_form=$(this).closest('form');coo_cart_control.submit_buy_now_form(t_operation_form)}return false});$('.button_details_add_cart').closest('form').submit(function(){coo_dropdowns_listener.check_combi_status();gm_qty_check=new GMOrderQuantityChecker();if(gm_qty_check.check()){coo_cart_control.submit_buy_now_form(this)}return false})};this.init_binds()}
/*<?php
}
else
{
?>*/
function ButtonDetailsAddCartHandler()
{
	if(fb)console.log('ButtonDetailsAddCartHandler ready');
	
	this.init_binds = function()
	{
		if(fb)console.log('ButtonDetailsAddCartHandler init_binds');

		$('.button_details_add_cart').die('click');
		$('.button_details_add_cart').live('click', function(event)
		{
			if(fb)console.log('.button_details_add_cart click');

			coo_dropdowns_listener.check_combi_status();

			gm_qty_check = new GMOrderQuantityChecker();
			if(gm_qty_check.check())
			{
				var t_operation_form = $(this).closest('form');
				coo_cart_control.submit_buy_now_form(t_operation_form);
			}	

			return false;
		});

		$('.button_details_add_cart').closest('form').submit(function()
		{
			coo_dropdowns_listener.check_combi_status();
			
			gm_qty_check = new GMOrderQuantityChecker();
			if(gm_qty_check.check())
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