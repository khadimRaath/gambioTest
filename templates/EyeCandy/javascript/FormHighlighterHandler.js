/* FormHighlighterHandler.js <?php
#   --------------------------------------------------------------
#   FormHighlighterHandler.js 2011-01-24 gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('6 e(){1(3)4.5(\'e q\');r b=2;2.f=6(){1(3)4.5(\'e f\');$(\'.0\').s(\'g\');$(\'.0\').t(\'g\',6(){1(3)4.5(\'.0 g\');b.d(2)});$(\'.0 7, .0 8, .0 9\').n(\'h\');$(\'.0 7, .0 8, .0 9\').h(6(){1(3)4.5(\'.0 7 h\');b.d(2)});$(\'.0 7, .0 8, .0 9\').n(\'i\');$(\'.0 7, .0 8, .0 9\').i(6(){1(3)4.5(\'.0 7 i\');b.j(2)})};2.d=6(a){1($(a).u(\'c\')==v){2.j(a);$(a).k(\'c\');$(a).o(\'.0\').k(\'c\');$(a).o(\'.d\').w(\'.l p\').k(\'c\');1(3)4.5(\'m x\')}y{1(3)4.5(\'m z A\')}};2.j=6(a){$(\'.l p, .0 7, .0 9, .0 8, .l, .0\').B(\'c\');1(3)4.5(\'m C\')};2.f()}',39,39,'form_highlight_right|if|this|fb|console|log|function|input|textarea|select|||highlight_form|form_highlight|FormHighlighterHandler|init_binds|click|focus|blur|remove_highlight|addClass|form_highlight_left|hightlight|unbind|closest|label|ready|var|die|live|hasClass|false|find|successful|else|already|applied|removeClass|removed'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function FormHighlighterHandler()
{
	if(fb)console.log('FormHighlighterHandler ready');

	var coo_this = this;

	this.init_binds = function()
	{
		if(fb)console.log('FormHighlighterHandler init_binds');

		$('.form_highlight_right').die('click');
		$('.form_highlight_right').live('click', function()
		{
			if(fb)console.log('.form_highlight_right click');

			coo_this.form_highlight(this);

		});

		$('.form_highlight_right input, .form_highlight_right textarea, .form_highlight_right select').unbind('focus');
		$('.form_highlight_right input, .form_highlight_right textarea, .form_highlight_right select').focus(function()
		{
			if(fb)console.log('.form_highlight_right input focus');

			coo_this.form_highlight(this);
		});

		$('.form_highlight_right input, .form_highlight_right textarea, .form_highlight_right select').unbind('blur');
		$('.form_highlight_right input, .form_highlight_right textarea, .form_highlight_right select').blur(function()
		{
			if(fb)console.log('.form_highlight_right input blur');

			coo_this.remove_highlight(this);
		});
	}

	this.form_highlight = function(p_element)
	{
		if($(p_element).hasClass('highlight_form') == false)
		{
			this.remove_highlight(p_element);

			$(p_element).addClass('highlight_form');
			$(p_element).closest('.form_highlight_right').addClass('highlight_form');
			$(p_element).closest('.form_highlight').find('.form_highlight_left label').addClass('highlight_form');

			if(fb)console.log('hightlight successful');
		}
		else
		{
			if(fb)console.log('hightlight already applied');
		}
	}

	this.remove_highlight = function(p_element)
	{
		$('.form_highlight_left label, .form_highlight_right input, .form_highlight_right select, .form_highlight_right textarea, .form_highlight_left, .form_highlight_right').removeClass('highlight_form');

		if(fb)console.log('hightlight removed');
	}

	this.init_binds();
}
/*<?php
}
?>*/
