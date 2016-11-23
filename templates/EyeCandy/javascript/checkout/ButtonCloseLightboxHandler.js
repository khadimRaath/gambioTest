/* ButtonCloseLightboxHandler.js <?php
#   --------------------------------------------------------------
#   ButtonCloseLightboxHandler.js 2011-01-28 gambio
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
function ButtonCloseLightboxHandler(){if(fb)console.log('ButtonCloseLightboxHandler ready');this.init_binds=function(){if(fb)console.log('ButtonCloseLightboxHandler init_binds');$('.button_close_lightbox').die('click');$('.button_close_lightbox').live('click',function(){var t_confirm=confirm('<?php echo GM_CONFIRM_CLOSE_LIGHTBOX; ?>');if(t_confirm==false){return false;}return true;});};this.init_binds();}
/*<?php
}
else
{
?>*/
function ButtonCloseLightboxHandler()
{
	if(fb)console.log('ButtonCloseLightboxHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('ButtonCloseLightboxHandler init_binds');

		$('.button_close_lightbox').die('click');
		$('.button_close_lightbox').live('click', function()
		{
			var t_confirm = confirm('<?php echo GM_CONFIRM_CLOSE_LIGHTBOX; ?>');
			if(t_confirm == false)
			{
				return false;
			}

			return true;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/

