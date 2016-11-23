/* ButtonPrintOrderHandler.js <?php
#   --------------------------------------------------------------
#   ButtonPrintOrderHandler.js 2011-01-24 gambio
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
function ButtonPrintOrderHandler(){if(fb)console.log('ButtonPrintOrderHandler ready');this.init_binds=function(){if(fb)console.log('ButtonPrintOrderHandler init_binds');$('.button_print_order').die('click');$('.button_print_order').live('click',function(){if(fb)console.log('.button_print_order click');window.open($(this).attr('href'),'popup','toolbar=0, width=640, height=600');return false;});};this.init_binds();}
/*<?php
}
else
{
?>*/
function ButtonPrintOrderHandler()
{
	if(fb)console.log('ButtonPrintOrderHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('ButtonPrintOrderHandler init_binds');

		$('.button_print_order').die('click');
		$('.button_print_order').live('click', function()
		{
			if(fb)console.log('.button_print_order click');

			window.open($(this).attr('href'), 'popup', 'toolbar=0, width=640, height=600');

			return false;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/

