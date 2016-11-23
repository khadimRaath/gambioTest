/* LiveSearchHandler.js <?php
#   --------------------------------------------------------------
#   LiveSearchHandler.js 2015-07-22 gm
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2015 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('8 C(){4(x)B.z(\'C 1A\');g f=5,q=1z();5.A=8(){4(x)B.z(\'C A\');4($(\'#3\').y>0){$(\'.v\').1y(\'<R 1x="7" H="T:Y"></R>\');$(\'#7\').1(\'9-O\',$(\'#3\').1(\'9-j-O\'));$(\'#7\').1(\'9-H\',$(\'#3\').1(\'9-j-H\'));$(\'#7\').1(\'9-p\',$(\'#3\').1(\'9-j-p\'));$(\'#7\').1(\'h-j\',$(\'#3\').1(\'h-j\'));$(\'#7\').1(\'h-10\',$(\'#3\').1(\'h-10\'));$(\'#7 1w 1v\').t(\'u\',8(){Q.1u.S=$(5).1t(\'a\').Z(\'S\');1s J});$(\'#3\').t(\'K\',8(b){4(x)B.z(\'3 K\');g c=1r($(\'#3\').Z(\'1q\'));4(c.y>2){1m.1B({13:\'\',1g:\'1f.1e?1d=1c&1b=\'+c+\'&1a=\'+17,16:"15",1l:12,14:8(a){4(a!=\'\'){f.11(a)}I{f.m()}}}).G}I{f.m()}});$(\'.v\').18(\'u\',f.m);$(\'.v\').t(\'u\',f.m)}};5.m=8(){$(\'#7\').G(\'\');$(\'#7\').19();4(X.W.V(/U [0-6]\\./)){f.F(J)}};5.11=8(a){f.E();4(X.W.V(/U [0-6]\\./)){f.F(12)}$(\'#7\').G(a);$(\'#7\').1h();Q.1i=8(){f.E()}};5.F=8(a){4(a){$(\'1j\').1k(8(){4($(5).1(\'D\')!=\'P\'&&$(5).1(\'T\')!=\'Y\'){q.1n(5);$(5).1({D:\'P\'})}})}I{1o(g i=0;i<q.y;i++){$(q[i]).1({D:\'1p\'})}}};5.E=8(){g a=l($(\'#3\').N().k),w=0;4(s($(\'#3\').1(\'M\'))!=\'o\'){w=l($(\'#3\').1(\'M\').r(\'n\',\'\'))}g b=0;4(s($(\'#3\').1(\'9-k-p\'))!=\'o\'){b=l($(\'#3\').1(\'9-k-p\').r(\'n\',\'\'))}g c=0;4(s($(\'#3\').1(\'h-k\'))!=\'o\'){c=l($(\'#3\').1(\'h-k\').r(\'n\',\'\'))}g d=0;4(s($(\'#3\').1(\'h-L\'))!=\'o\'){d=l($(\'#3\').1(\'h-L\').r(\'n\',\'\'))}g e=a+w+b+c+d;$(\'#7\').1({j:$(\'#3\').N().j,k:e+\'n\'})};5.A()}',62,100,'|css||search_field|if|this||live_search_container|function|border|||||||var|padding||left|top|Number|hide_result|px|undefined|width|t_ie6_elements_array|replace|typeof|live|click|wrap_shop|t_height|fb|length|log|init_binds|console|LiveSearchHandler|visibility|fix_position|ie6_fix|html|style|else|false|keyup|bottom|height|offset|color|hidden|window|div|href|display|MSIE|match|appVersion|navigator|none|attr|right|show_result|true|data|success|POST|type|gm_session_id|die|hide|XTCsid|needle|LiveSearch|module|php|request_port|url|show|onresize|select|each|async|jQuery|push|for|visible|value|encodeURIComponent|return|find|location|li|ul|id|prepend|Array|ready|ajax'.split('|'),0,{}))
/*<?php
}
else
{
?>*/
function LiveSearchHandler()
{
	if(fb)console.log('LiveSearchHandler ready');

	var coo_this = this;
	var t_ie6_elements_array = Array();

	this.init_binds = function()
	{
		if(fb)console.log('LiveSearchHandler init_binds');

		if($('#search_field').length > 0)
		{
			$('.wrap_shop').prepend('<div id="live_search_container" style="display:none"></div>');

			$('#live_search_container').css('border-color', $('#search_field').css('border-left-color'));
			$('#live_search_container').css('border-style', $('#search_field').css('border-left-style'));
			$('#live_search_container').css('border-width', $('#search_field').css('border-left-width'));
			$('#live_search_container').css('padding-left', $('#search_field').css('padding-left'));
			$('#live_search_container').css('padding-right', $('#search_field').css('padding-right'));

			// IE-Fix:
			$('#live_search_container ul li').live('click', function()
			{
				window.location.href = $(this).find('a').attr('href');

				return false;
			});

			$('#search_field').live('keyup', function(event)
			{
				if(fb)console.log('search_field keyup');

				var t_needle = encodeURIComponent( $('#search_field').attr('value') );

				if(t_needle.length > 2)
				{
					jQuery.ajax(
					{
						data:		'',
						url: 		'request_port.php?module=LiveSearch&needle=' + t_needle + '&XTCsid=' + gm_session_id,
						type: 		"POST",
						async:		true,
						success:	function(t_search_result_html)
						{
							if(t_search_result_html != '')
							{
								coo_this.show_result(t_search_result_html);
							}
							else
							{
								coo_this.hide_result();
							}
						}
					}).html;
				}
				else
				{
					coo_this.hide_result();
				}
			});

			$('.wrap_shop').die('click', coo_this.hide_result);
			$('.wrap_shop').live('click', coo_this.hide_result);

		}
	}

	this.hide_result = function()
	{
		$('#live_search_container').html('');
		$('#live_search_container').hide();
		if(navigator.appVersion.match(/MSIE [0-6]\./))
		{
			coo_this.ie6_fix(false);
		}
	}

	this.show_result = function(p_html_content)
	{

		coo_this.fix_position();

		if(navigator.appVersion.match(/MSIE [0-6]\./))
		{
			coo_this.ie6_fix(true);
		}

		$('#live_search_container').html(p_html_content);
		$('#live_search_container').show();

		window.onresize = function() {
			coo_this.fix_position();
		};

	}

	this.ie6_fix = function(p_hide)
	{
		if(p_hide)
		{
			$('select').each(function()
			{
				if($(this).css('visibility') != 'hidden' && $(this).css('display') != 'none')
				{
					t_ie6_elements_array.push(this);
					$(this).css(
					{
						visibility: 'hidden'
					});
				}
			});
		}
		else
		{
			for(var i = 0; i < t_ie6_elements_array.length; i++)
			{
				$(t_ie6_elements_array[i]).css(
				{
					visibility: 'visible'
				});
			}
		}
	}

	this.fix_position = function()
	{
		var t_offset_top = Number($('#search_field').offset().top);
		var t_height = 0;
		if(typeof($('#search_field').css('height')) != 'undefined')
		{
			t_height = Number($('#search_field').css('height').replace('px', ''));
		}
		var t_border_top_width = 0;
		if(typeof($('#search_field').css('border-top-width')) != 'undefined')
		{
			t_border_top_width = Number($('#search_field').css('border-top-width').replace('px', ''));
		}
		var t_padding_top = 0;
		if(typeof($('#search_field').css('padding-top')) != 'undefined')
		{
			t_padding_top = Number($('#search_field').css('padding-top').replace('px', ''));
		}
		var t_padding_bottom = 0;
		if(typeof($('#search_field').css('padding-bottom')) != 'undefined')
		{
			t_padding_bottom = Number($('#search_field').css('padding-bottom').replace('px', ''));
		}

		var t_top = t_offset_top +
		            t_height +
		            t_border_top_width +
		            t_padding_top +
		            t_padding_bottom;

		$('#live_search_container').css({
			left:			$('#search_field').offset().left,
			top:			t_top + 'px'
		});
	}

	this.init_binds();
}
/*<?php
}
?>*/

