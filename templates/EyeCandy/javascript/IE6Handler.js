/* IE6Handler.js <?php
#   --------------------------------------------------------------
#   IE6Handler.js 2011-09-09 gambio
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
function IE6Handler(){if(fb)console.log('IE6Handler ready');var t_ie6=false;if(navigator.appVersion.match(/MSIE [0-6]\./)){t_ie6=true}this.init_binds=function(){if(fb)console.log('IE6Handler init_binds');if(t_ie6&&typeof(DD_belatedPNG)!='undefined'){this.fix_png()}};this.fix_png=function(){DD_belatedPNG.fix('.png-fix, a.button_blue span, a.button_blue, a.button_blue img, a.button_green span, a.button_green, a.button_green img, a.button_grey span, a.button_grey, a.button_grey img, a.button_red span, a.button_red, a.button_red img, .tabs ul .ui-tabs-selected a, .tabs ul li, #container_inner #header, .megadropdown-shadow, #top_navi a img, #top_navi li, #dropdown_shopping_cart, #dropdown_shopping_cart_inner, #head_navi, #head_navi li, #head_navi li a:hover, #head_toolbox, #head_toolbox img, a.button span, a.button, a.button img, #head_toolbox div, input.input-text, .process_bar ul li a, .process_bar ul li, .input-textarea')};this.init_binds()}
/*<?php
}
else
{
?>*/
function IE6Handler()
{
	if(fb)console.log('IE6Handler ready');

	var t_ie6 = false;
	if(navigator.appVersion.match(/MSIE [0-6]\./))
	{
		t_ie6 = true;
	}

	this.init_binds = function()
	{
		if(fb)console.log('IE6Handler init_binds');

		if(t_ie6 && typeof(DD_belatedPNG) != 'undefined')
		{
			this.fix_png();
		}
	}

	this.fix_png = function()
	{
		DD_belatedPNG.fix('.png-fix, a.button_blue span, a.button_blue, a.button_blue img, a.button_green span, a.button_green, a.button_green img, a.button_grey span, a.button_grey, a.button_grey img, a.button_red span, a.button_red, a.button_red img, .tabs ul .ui-tabs-selected a, .tabs ul li, #container_inner #header, .megadropdown-shadow, #top_navi a img, #top_navi li, #dropdown_shopping_cart, #dropdown_shopping_cart_inner, #head_navi, #head_navi li, #head_navi li a:hover, #head_toolbox, #head_toolbox img, a.button span, a.button, a.button img, #head_toolbox div, input.input-text, .process_bar ul li a, .process_bar ul li, .input-textarea');
	}

	this.init_binds();
}
/*<?php
}
?>*/

