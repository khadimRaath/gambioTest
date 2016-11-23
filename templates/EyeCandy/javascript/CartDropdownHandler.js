/* CartDropdownHandler.js <?php
#   --------------------------------------------------------------
#   CartDropdownHandler.js 2013-06-10 gambio
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
function CartDropdownHandler(){if(fb)console.log('CartDropdownHandler ready');this.init_binds=function(){if(fb)console.log('CartDropdownHandler init_binds');t_close_timeout=false;$('#head_shopping_cart').die('click');$('#head_shopping_cart').live('click',function(){if(fb)console.log('#head_shopping_cart click');if($(this).hasClass('active')==false){coo_cart_control.position_dropdown();coo_cart_control.open_dropdown();t_close_timeout=setTimeout(coo_cart_control.close_dropdown,10000)}else{coo_cart_control.close_dropdown()}return true});$(document).bind("cart_shipping_costs_info_active",function(){if(t_close_timeout!=false){clearTimeout(t_close_timeout);t_close_timeout=false}})};this.init_binds()}
/*<?php
}
else
{
?>*/
function CartDropdownHandler()
{
	if(fb)console.log('CartDropdownHandler ready');
	
	this.init_binds = function()
	{
		if(fb)console.log('CartDropdownHandler init_binds');

		//var coo_this = this;
		t_close_timeout = false;

		$('#head_shopping_cart').die('click');
		$('#head_shopping_cart').live('click', function()
		{
			if(fb)console.log('#head_shopping_cart click');

			if($(this).hasClass('active') == false)
			{
				coo_cart_control.position_dropdown();
				coo_cart_control.open_dropdown();
				t_close_timeout = setTimeout(coo_cart_control.close_dropdown, 10000);
			}
			else
			{
				coo_cart_control.close_dropdown()
			}
			return true;
		});
		
		$( document ).bind( "cart_shipping_costs_info_active", function()
		{
			if( t_close_timeout != false )
			{
				clearTimeout( t_close_timeout );
				t_close_timeout = false;
			}
		});

	}
	this.init_binds();
}
/*<?php
}
?>*/