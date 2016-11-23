/* InputEnterKeyHandler.js <?php
#   --------------------------------------------------------------
#   InputEnterKeyHandler.js 2013-03-06 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('h p(){2(5)6.7(\'p G\');i d=0,q=0,j=3,r=0,k=3,l=3;4.s=h(){2(5)6.7(\'p s\');$(\'x\').y(\'H\',h(a){i b=(a.m?a.m:(a.n?a.n:a.z));2($(4).o(\'f\').I(\'.A\').t>0){2(b==u&&((J.K($(4).B().t-r)<=1||j==8)||d==0)){2(k||l==3){g 3}2(L($(4).o(\'f\').C(\'e\'))==\'M\'){2(5)6.7(\'D e N f-O!\');i c=$(4).o(\'f\').C(\'e\').P(\'g \',\'\'),v=Q(c);2(5)6.7(\'e R\');2(v==3){2(5)6.7(\'e E: 3\');g 3}w 2(v==8){2(5)6.7(\'e E: 8\')}}$(4).o(\'f\').F("S",["F"]);g 3}w 2(b==u||d==T||d==U||d==9){l=3;k=3}d=b;r=$(4).B().t}w{2(5)6.7(\'V ".A" W D\')}g 8});$(\'x\').y(\'X\',h(a){i b=(a.m?a.m:(a.n?a.n:a.z));2(b!=u){j=3}2(q==Y){2(b==Z){j=8}}2(d==10||d==11){k=8}q=b;l=8})};4.s()}',62,64,'||if|false|this|fb|console|log|true||||||onsubmit|form|return|function|var|v_ctrl_v|v_arrow_key_pressed|v_keydown|keyCode|which|closest|InputEnterKeyHandler|v_last_key_down|v_last_char_count|init_binds|length|13|t_onsubmit_return|else|input|live|charCode|action_submit|val|attr|found|returns|trigger|ready|keyup|find|Math|abs|typeof|string|in|tag|replace|eval|evaluated|submit|37|39|no|button|keydown|17|86|38|40'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function InputEnterKeyHandler()
{
	if(fb)console.log('InputEnterKeyHandler ready');

	var v_last_key = 0;
	var v_last_key_down = 0;
	var v_ctrl_v = false;
	var v_last_char_count = 0;
	var v_arrow_key_pressed = false;
	var v_keydown = false;

	this.init_binds = function()
	{
		if(fb)console.log('InputEnterKeyHandler init_binds');

		$('input').live('keyup', function(event)
		{
			var t_keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));

			if($(this).closest('form').find('.action_submit').length > 0)
			{
				// track enter key
				if(t_keycode == 13 && ((Math.abs($(this).val().length - v_last_char_count) <= 1 || v_ctrl_v == true) || v_last_key == 0)) // 13 keycode for enter key
				{
					// abort submit because input-field browser-suggestion was confirmed and no form submit was executed
					if(v_arrow_key_pressed || v_keydown == false)
					{
						return false;
					}

					if(typeof($(this).closest('form').attr('onsubmit')) == 'string')
					{
						if(fb)console.log('found onsubmit in form-tag!');

						var t_onsubmit = $(this).closest('form').attr('onsubmit').replace('return ', '');
						var t_onsubmit_return = eval(t_onsubmit);

						if(fb)console.log('onsubmit evaluated');

						if(t_onsubmit_return == false)
						{
							if(fb)console.log('onsubmit returns: false');

							return false;
						}
						else if(t_onsubmit_return == true)
						{
							if(fb)console.log('onsubmit returns: true');
						}

					}

					$(this).closest('form').trigger( "submit", ["trigger"] );
					return false;
				}
				else if(t_keycode == 13 || v_last_key == 37 || v_last_key == 39 || v_last_key == 9) // 13 = Enter, 37 = Arrow left, 39 Arrow right, 9 Tab
				{
					v_keydown = false;
					v_arrow_key_pressed = false;
				}

				v_last_key = t_keycode;
				v_last_char_count = $(this).val().length;
			}
			else
			{
				if(fb)console.log('no ".action_submit" button found');
			}

			return true;
		});

		$('input').live('keydown', function(event)
		{
			var t_keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));

			if(t_keycode != 13)
			{
				v_ctrl_v = false;
			}

			if(v_last_key_down == 17) // key: CTRL
			{
				if(t_keycode == 86) // key: v
				{
					v_ctrl_v = true;
				}
			}

			if(v_last_key == 38 || v_last_key == 40) // 38, 40 keycode for arrow-keys (up, down)
			{
				v_arrow_key_pressed = true;
			}

			v_last_key_down = t_keycode;

			v_keydown = true;
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/