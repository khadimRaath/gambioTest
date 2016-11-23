/* ButtonCartRefreshHandler.js <?php
#   --------------------------------------------------------------
#   ButtonCartRefreshHandler.js 2013-05-14 gm
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('2 d(){$(v).k(2(){1(4)5.6(\'d k\');w.e()});l.e=2(){1(4)5.6(\'d e\');$(\'.f\').m(\'g\');$(\'.f\').n(\'g\',2(a){1(4)5.6(\'.f g\');1($(l).x(\'o\')){1(4)5.6(\'-- o y\');z();7 3}h b=p q(),8=b.r();1(8==9){$(\'#i\').s("j",[9])}7 3});$(\'A.B\').n(\'C\',2(a){1($(\'.D\').E==0){h b=(a.t?a.t:(a.u?a.u:a.F));1(b==G){$(\'#i\').s("j");7 3}}});$(\'#i\').j(2(a,b){1(H b=="I")b=3;1(b!=9){h c=p q(),8=c.r();1(8!=9){7 3}J{$(\'*\').K();$(\'*\').m();$(\'*\').L()}}})}}',48,48,'|if|function|false|fb|console|log|return|t_result|true||||ButtonCartRefreshHandler|init_binds|button_cart_refresh|click|var|cart_quantity|submit|ready|this|die|live|wishlist_button|new|GMOrderQuantityChecker|check_cart|trigger|keyCode|which|document|coo_button_cart_refresh_handler|hasClass|found|update_wishlist|input|gm_cart_data|keyup|button_update_wish_list|length|charCode|13|typeof|undefined|else|unbind|undelegate'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function ButtonCartRefreshHandler()
{
	$(document).ready(
		function()
		{
			if(fb)console.log('ButtonCartRefreshHandler ready');

			coo_button_cart_refresh_handler.init_binds();
		}
	);


	this.init_binds = function()
	{
		if(fb)console.log('ButtonCartRefreshHandler init_binds');


		$('.button_cart_refresh').die('click');
		$('.button_cart_refresh').live('click', function(event)
		{
			if(fb)console.log('.button_cart_refresh click');

			if($(this).hasClass('wishlist_button'))
			{
				if(fb)console.log('-- wishlist_button found');
				update_wishlist();
				return false;
			}

            var coo_quantity_checker = new GMOrderQuantityChecker();
            var t_result = coo_quantity_checker.check_cart();

            if(t_result == true) {
				$('#cart_quantity').trigger( "submit", [true] );
            }

			return false;
		});

		$('input.gm_cart_data').live('keyup', function(event)
		{
			if($('.button_update_wish_list').length == 0)
			{
				var t_keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
				
				// track enter key
				if(t_keycode == 13) // 13 keycode for enter key
				{
					$('#cart_quantity').trigger( "submit" );

					return false;
				}
			}
		});

		$('#cart_quantity').submit(function( p_event, p_checked )
		{
			if( typeof p_checked == "undefined" ) p_checked = false;
			if( p_checked != true )
			{
				var coo_quantity_checker = new GMOrderQuantityChecker();
				var t_result = coo_quantity_checker.check_cart();
				
				if(t_result != true)
				{
					return false;
				}
				else
				{
					// kill all other events before submitting
					$('*').unbind();
					$('*').die();
					$('*').undelegate();
				}
			}
		});
	}
}
/*<?php
}
?>*/
