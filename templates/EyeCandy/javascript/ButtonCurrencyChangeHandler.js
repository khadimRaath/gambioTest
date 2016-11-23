/* ButtonCurrencyChangeHandler.js <?php
#   --------------------------------------------------------------
#   ButtonCurrencyChangeHandler.js 2011-01-24 gambio
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
function ButtonCurrencyChangeHandler(){if(fb)console.log('ButtonCurrencyChangeHandler ready');this.init_binds=function(){if(fb)console.log('ButtonCurrencyChangeHandler init_binds');$('.currencies_selection').die('change');$('.currencies_selection').live('change',function(){if(fb)console.log('.currencies_selection change');if($(this).closest('form').length>0){$(this).closest('form').submit();}return false;});};this.init_binds();}
/*<?php
}
else
{
?>*/
function ButtonCurrencyChangeHandler()
{
	if(fb)console.log('ButtonCurrencyChangeHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('ButtonCurrencyChangeHandler init_binds');

		$('.currencies_selection').die('change');
		$('.currencies_selection').live('change', function()
		{
			if(fb)console.log('.currencies_selection change');

			if($(this).closest('form').length > 0)
			{
				$(this).closest('form').submit();
			}

			return false;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/
