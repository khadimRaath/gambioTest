/* ButtonCheckoutOrderConfirmHandler.js <?php
#   --------------------------------------------------------------
#   ButtonCheckoutOrderConfirmHandler.js 2014-08-08 gm
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
(function($,jQuery){if(fb)console.log('ButtonCheckoutOrderConfirmHandler ready');function deactivate_confirm_button(){if(($('input[name="withdrawal"]').length>0&&$('input[name="withdrawal"]').prop('checked')!=true)||$('input[name="conditions"]').length>0&&$('input[name="conditions"]').prop('checked')!=true){return true}if($(this).hasClass("order_confirmed")){return false}$(this).addClass("order_confirmed");return true}$(".order_confirm").delegate(".checkout_button a","click",deactivate_confirm_button)})(jQuery,jQuery);
/*<?php
}
else
{
?>*/
(function($, jQuery)
{
	if(fb)console.log('ButtonCheckoutOrderConfirmHandler ready');

	function deactivate_confirm_button()
	{
		if(($('input[name="withdrawal"]').length > 0 && $('input[name="withdrawal"]').prop('checked') != true)
			|| $('input[name="conditions"]').length > 0 && $('input[name="conditions"]').prop('checked') != true)
		{
			return true;
		}
		
		if( $( this ).hasClass( "order_confirmed" ) )
		{
			return false;
		}
		$( this ).addClass( "order_confirmed" )
		return true;
	}
	
	$( ".order_confirm" ).delegate( ".checkout_button a", "click", deactivate_confirm_button);
	
})(jQuery, jQuery);
/*<?php
}
?>*/