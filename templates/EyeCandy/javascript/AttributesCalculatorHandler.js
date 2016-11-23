/* AttributesCalculatorHandler.js <?php
#   --------------------------------------------------------------
#   AttributesCalculatorHandler.js 2011-01-24 gambio
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
function AttributesCalculatorHandler(){if(fb)console.log('AttributesCalculatorHandler ready');this.init_binds=function(){if(fb)console.log('AttributesCalculatorHandler init_binds');$('#product_listing input[name="products_qty"]').die('keyup');$('#product_listing input[name="products_qty"]').live('keyup',function(){if(fb)console.log('#product_listing input[name="products_qty"] keyup');if($(this).closest('form').attr('id').search('gm_add_to_cart_')!=-1){var t_id='0';t_id=$(this).closest('form').attr('id').replace('gm_add_to_cart_','');gm_calc_prices_listing(t_id,true);}return false;});$('#product_listing .gm_listing_form').die('click');$('#product_listing .gm_listing_form').live('click',function(){if(fb)console.log('#product_listing .gm_listing_form click');if($(this).closest('form').attr('id').search('gm_add_to_cart_')!=-1){var t_id='0';t_id=$(this).closest('form').attr('id').replace('gm_add_to_cart_','');gm_calc_prices_listing(t_id,true);}});};this.init_binds();}
/*<?php
}
else
{
?>*/
function AttributesCalculatorHandler()
{
	if(fb)console.log('AttributesCalculatorHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('AttributesCalculatorHandler init_binds');

		$('#product_listing input[name="products_qty"]').die('keyup');
		$('#product_listing input[name="products_qty"]').live('keyup', function()
		{
			if(fb)console.log('#product_listing input[name="products_qty"] keyup');

			if($(this).closest('form').attr('id').search('gm_add_to_cart_') != -1)
			{
				var t_id = '0';
				t_id =$(this).closest('form').attr('id').replace('gm_add_to_cart_', '');
				gm_calc_prices_listing(t_id, true);
			}

			return false;
		});

		$('#product_listing .gm_listing_form').die('click');
		$('#product_listing .gm_listing_form').live('click', function()
		{
			if(fb)console.log('#product_listing .gm_listing_form click');

			if($(this).closest('form').attr('id').search('gm_add_to_cart_') != -1)
			{
				var t_id = '0';
				t_id =$(this).closest('form').attr('id').replace('gm_add_to_cart_', '');
				gm_calc_prices_listing(t_id, true);
			}
		});

	}

	this.init_binds();
}
/*<?php
}
?>*/