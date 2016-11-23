/* ButtonBookmarkHandler.js <?php
#   --------------------------------------------------------------
#   ButtonBookmarkHandler.js 2011-01-24 gambio
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
function ButtonBookmarkHandler(){if(fb)console.log('ButtonBookmarkHandler ready');this.init_binds=function(){if(fb)console.log('ButtonBookmarkHandler init_binds');$('.button_bookmark').die('click');$('.button_bookmark').live('click',function(){if(fb)console.log('.button_bookmark click');return false;});};this.init_binds();}
/*<?php
}
else
{
?>*/
function ButtonBookmarkHandler()
{
	if(fb)console.log('ButtonBookmarkHandler ready');
	
	this.init_binds = function()
	{
		if(fb)console.log('ButtonBookmarkHandler init_binds');

		$('.button_bookmark').die('click');
		$('.button_bookmark').live('click', function()
		{
			if(fb)console.log('.button_bookmark click');
			
			
			return false;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/