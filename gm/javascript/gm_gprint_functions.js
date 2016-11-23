/* gm_gprint_functions.js <?php
#   --------------------------------------------------------------
#   gm_gprint_functions.js 2013-03-07 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2012 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(a)};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('7 p(a){2 b=\'\'+a,3=\'\',e=\'q\';8(2 i=0;i<b.r;i++){4(b.9(i)=="."){s}4(e.t(b.9(i))!=-1){3+=b.9(i)}}3=u(3);d 3}7 v(a){2 b;a=a+\'\';4(f(a)==\'g\'){b=a;8(2 c h a){b[c]=a[c].5(\'+\').6(\'%j\');b[c]=b[c].5(\'%k\').6(\'&l;\');b[c]=m(b[c])}}n{a=a.5(\'%k\').6(\'&l;\');b=m(a.5(\'+\').6(\'%j\'))}d b}7 w(a){2 b;4(f(a)==\'g\'){b=a;8(2 c h a){b[c]=o(a[c])}}n{b=o(a[c])}d b}',33,33,'||var|c_number|if|split|join|function|for|charAt||||return|t_numbers|typeof|object|in||20|80|euro|unescape|else|encodeURIComponent|gm_gprint_clear_number|0123456789|length|break|indexOf|Number|gm_unescape|gm_encodeURIComponent'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function gm_gprint_clear_number(p_number)
{
	var t_number = '' + p_number; // convert into string
	var c_number = '';
	var t_numbers = '0123456789';

	for(var i = 0; i < t_number.length; i++)
	{		
		if( t_number.charAt(i) == "." )
		{
			break;
		}
		if (t_numbers.indexOf(t_number.charAt(i)) != -1)
		{
			c_number += t_number.charAt(i);
		}
	}

	c_number = Number(c_number);

	return c_number;
}

function gm_unescape(p_value)
{
	var t_value;
	p_value = p_value + '';

	if(typeof(p_value) == 'object')
	{
		t_value = p_value;

		for(var t_key in p_value)
		{
			t_value[t_key] = p_value[t_key].split('+').join('%20');
			t_value[t_key] = t_value[t_key].split('%80').join('&euro;');
			t_value[t_key] = unescape(t_value[t_key]);
		}
	}
	else
	{
		p_value = p_value.split('%80').join('&euro;');
		t_value = unescape(p_value.split('+').join('%20'));
	}

	return t_value;
}

function gm_encodeURIComponent(p_value)
{
	var t_value;

	if(typeof(p_value) == 'object')
	{
		t_value = p_value;

		for(var t_key in p_value)
		{
			t_value[t_key] = encodeURIComponent(p_value[t_key]);
		}
	}
	else
	{
		t_value = encodeURIComponent(p_value[t_key]);
	}

	return t_value;
}

/*<?php
}
?>*/
