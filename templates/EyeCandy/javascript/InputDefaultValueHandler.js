/* InputDefaultValueHandler.js <?php
#   --------------------------------------------------------------
#   InputDefaultValueHandler.js 2011-012-01 gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('6 f(){2(7)8.9(\'f u\');3 d=m v();3 e=1;1.g=6(){2(7)8.9(\'f g\');3 c=\'\';$(\'.4\').n(\'h\');$(\'.4\').o(\'h\',6(){2(7)8.9(\'.4 h\');3 a=e.j(d,1);3 b=a;2(p(a)==\'w\'&&a==q){b=d.r;d[b]=m x();d[b][\'t\']=1;d[b][\'k\']=$(1).5()}2($(1).5()!=\'\'){c=$(1).5()}2(c==d[b][\'k\']){$(1).5(\'\')}});$(\'.4\').n(\'l\');$(\'.4\').o(\'l\',6(){2(7)8.9(\'.4 l\');2($(1).5().y(/\\s+/,\'\')==\'\'){3 a=e.j(d,1);$(1).5(d[a][\'k\'])}})};1.j=6(a,b){3 c=q;2(p(a)==\'z\'){A(3 i=0;i<a.r;i++){2(a[i][\'t\']==b){c=i}}}B c};1.g()}',38,38,'|this|if|var|default_value|val|function|fb|console|log||||||InputDefaultValueHandler|init_binds|click||in_array|VALUE|blur|new|die|live|typeof|false|length||ELEMENT|ready|Array|boolean|Object|replace|object|for|return'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function InputDefaultValueHandler()
{
	if(fb)console.log('InputDefaultValueHandler ready');

	var t_input_array = new Array();
	var coo_this = this;

	this.init_binds = function()
	{
		if(fb)console.log('InputDefaultValueHandler init_binds');

		var t_search_field_value = '';

		$('.default_value').die('click');
		$('.default_value').live('click', function()
		{
			if(fb)console.log('.default_value click');

			var t_check = coo_this.in_array(t_input_array, this);
			var t_key = t_check;

			if(typeof(t_check) == 'boolean' && t_check == false)
			{
				t_key = t_input_array.length;
				t_input_array[t_key] = new Object();
				t_input_array[t_key]['ELEMENT'] = this;
				t_input_array[t_key]['VALUE'] = $(this).val();
			}

			if($(this).val() != '')
			{
				t_search_field_value = $(this).val();
			}

			if(t_search_field_value == t_input_array[t_key]['VALUE'])
			{
				$(this).val('');
			}
		});

		$('.default_value').die('blur');
		$('.default_value').live('blur', function()
		{
			if(fb)console.log('.default_value blur');
			if($(this).val().replace(/\s+/, '') == '')
			{
				var t_key = coo_this.in_array(t_input_array, this);
				$(this).val(t_input_array[t_key]['VALUE']);
			}
		});
	}

	this.in_array = function(p_array, p_needle)
	{
		var t_found = false;

		if(typeof(p_array) == 'object')
		{
			for(var i = 0; i < p_array.length; i++)
			{
				if(p_array[i]['ELEMENT'] == p_needle)
				{
					t_found	= i;
				}
			}
		}

		return t_found;
	}

	this.init_binds();
}
/*<?php
}
?>*/
