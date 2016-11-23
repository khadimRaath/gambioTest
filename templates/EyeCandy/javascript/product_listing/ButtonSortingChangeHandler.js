/* ButtonSortingChangeHandler.js <?php
#   --------------------------------------------------------------
#   ButtonSortingChangeHandler.js 2011-01-24 gambio
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
function ButtonSortingChangeHandler(){if(fb)console.log('ButtonSortingChangeHandler ready');this.init_binds=function(){if(fb)console.log('ButtonSortingChangeHandler init_binds');$('#product_listing .panel select').die('change');$('#product_listing .panel select').live('change',function(){if(fb)console.log('#product_listing .panel select change');if($(this).closest('form').length>0){$(this).closest('form').submit();}return false;});};this.init_binds();}
/*<?php
}
else
{
?>*/
function ButtonSortingChangeHandler()
{
	if(fb)console.log('ButtonSortingChangeHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('ButtonSortingChangeHandler init_binds');

		$('#product_listing .panel select').die('change');
		$('#product_listing .panel select').live('change', function()
		{
			if(fb)console.log('#product_listing .panel select change');

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
