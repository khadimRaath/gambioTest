/* ButtonCheckoutModuleHandler.js <?php
#   --------------------------------------------------------------
#   ButtonCheckoutModuleHandler.js 2014-08-08 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('3 e(){4(5)6.7(\'e q\');r a=1;1.f=3(){4(5)6.7(\'e f\');$(\'.0 9[b=c]:8\').m(\'n.0\').d(\'2\');$(\'.0\').g(\'h\');$(\'.0\').i(\'h\',3(){4(5)6.7(\'.0 h\');$(1).d(\'2\')});$(\'.0\').g(\'j\');$(\'.0\').i(\'j\',3(){4(5)6.7(\'.0 j\');$(\'.0\').o(\'2\');$(\'.0 9[b=c]:8\').m(\'n.0\').d(\'2\')});$(\'.0\').g(\'k\',a.l);$(\'.0\').i(\'k\',a.l)};1.l=3(){4(5)6.7(\'.0 k\');$(\'.0\').o(\'2\');$(\'.0 9[b=c]:8\').p(\'8\',s);$(1).t(\'9[b=c]\').p(\'8\',u);$(1).d(\'2\')};1.f()}',31,31,'button_checkout_module|this|module_option_checked|function|if|fb|console|log|checked|input||type|radio|addClass|ButtonCheckoutModuleHandler|init_binds|die|mouseover|live|mouseout|click|check_module|closest|div|removeClass|prop|ready|var|false|find|true'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function ButtonCheckoutModuleHandler()
{
	if(fb)console.log('ButtonCheckoutModuleHandler ready');

	var coo_this = this;

	this.init_binds = function()
	{
		if(fb)console.log('ButtonCheckoutModuleHandler init_binds');

		$('.button_checkout_module input[type=radio]:checked').closest('div.button_checkout_module').addClass('module_option_checked');

		$('.button_checkout_module').die('mouseover');
		$('.button_checkout_module').live('mouseover', function()
		{
			if(fb)console.log('.button_checkout_module mouseover');

			$(this).addClass('module_option_checked');

		});

		$('.button_checkout_module').die('mouseout');
		$('.button_checkout_module').live('mouseout', function()
		{
			if(fb)console.log('.button_checkout_module mouseout');

			$('.button_checkout_module').removeClass('module_option_checked');
			$('.button_checkout_module input[type=radio]:checked').closest('div.button_checkout_module').addClass('module_option_checked');

		});

		$('.button_checkout_module').die('click', coo_this.check_module);
		$('.button_checkout_module').live('click', coo_this.check_module);
	}

	this.check_module = function()
	{
		if(fb)console.log('.button_checkout_module click');
		
		$('.button_checkout_module').removeClass('module_option_checked');
		$('.button_checkout_module input[type=radio]:checked').prop('checked', false);
		$(this).find('input[type=radio]').prop('checked', true);
		$(this).addClass('module_option_checked');
	}

	this.init_binds();
}
/*<?php
}
?>*/

