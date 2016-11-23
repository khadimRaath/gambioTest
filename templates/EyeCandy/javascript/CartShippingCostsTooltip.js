/* CartShippingCostsTooltip.js <?php
#   --------------------------------------------------------------
#   CartShippingCostsTooltip.js 2013-06-12 tb@gambio
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
$(document).ready(function(){$(".cart_shipping_costs_info_icon").live("click",function(){var t_info_text=$(".cart_shipping_costs_info_text");if($(t_info_text).is(":visible")){$(t_info_text).css("display","none")}else{$(t_info_text).css("display","block");$(document).trigger('cart_shipping_costs_info_active')}})});
/*<?php
}
else
{
?>*/
$(document).ready(function()
{
	$(".cart_shipping_costs_info_icon").live( "click", function()
	{
		var t_info_text = $( ".cart_shipping_costs_info_text" );
		if( $( t_info_text ).is( ":visible" ) )
		{
			$( t_info_text ).css( "display", "none" );
		}
		else
		{
			$( t_info_text ).css( "display", "block" );
			$( document ).trigger( 'cart_shipping_costs_info_active' );			
		}
	});
});
/*<?php
}
?>*/