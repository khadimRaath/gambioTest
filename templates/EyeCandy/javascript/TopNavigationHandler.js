/* TopNavigationHandler.js <?php
#   --------------------------------------------------------------
#   TopNavigationHandler.js 2015-06-17 gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('6 k(){1(r)s.t(\'k J\');2.l=6(){1(r)s.t(\'k l\');1(u v!=\'w\'){K.x=$.L([],v);2.y();M a=\'z\';$(\'#5 3 4 a\').c(\'A\');$(\'#5 3 4 a\').d(\'A\',6(){1($(2).m(\'n-o\')!=\'z\'){a=$(2).m(\'n-o\')}});$(\'#5 3 4\').c(\'B\');$(\'#5 3 4\').d(\'B\',6(){$(\'#5 3 4 a\').N(\'O\')});$(\'#5 3 4 f\').c(\'C\');$(\'#5 3 4 f\').d(\'C\',6(){1(a!=\'\'){$(2).8(\'a\').m(\'n-o\',a)}});$(\'#5 3 4 f\').c(\'p\');$(\'#5 3 4 f\').d(\'p\',6(){1(u($(2).8(\'a\').q(\'D\'))==\'w\'||$(2).8(\'a\').q(\'D\').b==0){P.Q.E=$(2).8(\'a\').q(\'E\');R S}g{$(2).8(\'a\').T(\'p\')}})}};2.y=6(){$.U(x,6(i,e){1(V(e)!==W.X.Y){1($(\'.F\').b<=0){$(\'#G\'+e+\'\').7(\'h\')}$(\'#H\'+e+\'\').7(\'h\');1($(\'#9\'+e+\'\').8(\'3\').b>0){$(\'#9\'+e+\'\').7(\'h\')}g{$(\'#9\'+e+\'\').I(\'4\').7(\'h\')}}g{1($(\'.F\').b<=0){$(\'#G\'+e+\'\').7(\'j\')}$(\'#H\'+e+\'\').7(\'j\');1($(\'#9\'+e+\'\').8(\'3\').b>0){$(\'#9\'+e+\'\').7(\'j\')}g{$(\'#9\'+e+\'\').I(\'4\').7(\'j\')}}})};2.l()}',61,61,'|if|this|ul|li|top_navi_inner|function|addClass|next|megadropdown_top_link_id_||length|die|live||div|else|parentOfCurrent||current|TopNavigationHandler|init_binds|css|background|color|click|attr|fb|console|log|typeof|parentsIds|undefined|parentsIdsBak|setCurrentsClasses|transparent|hover|mouseleave|mouseenter|rel|href|cat_go_up_button|menu_cat_id_|megadropdown_|closest|ready|window|extend|var|removeAttr|style|document|location|return|false|trigger|each|parseInt|js_options|global|categories_id'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function TopNavigationHandler()
{
    if(fb)console.log('TopNavigationHandler ready');

	this.init_binds = function()
	{
		if(fb)console.log('TopNavigationHandler init_binds');

		if(typeof parentsIds != 'undefined')
		{
			window.parentsIdsBak = $.extend([], parentsIds);
			this.setCurrentsClasses();

			var t_background_color_hover = 'transparent';

			$('#top_navi_inner ul li a').die('hover');
			$('#top_navi_inner ul li a').live('hover', function()
			{
				if($(this).css('background-color') != 'transparent')
				{
					t_background_color_hover = $(this).css('background-color');
				}
			});

			$('#top_navi_inner ul li').die('mouseleave');
			$('#top_navi_inner ul li').live('mouseleave', function()
			{
				$('#top_navi_inner ul li a').removeAttr('style');
			});

			$('#top_navi_inner ul li div').die('mouseenter');
			$('#top_navi_inner ul li div').live('mouseenter', function()
			{
				if(t_background_color_hover != '')
				{
					$(this).next('a').css('background-color', t_background_color_hover);
				}
			});

			$('#top_navi_inner ul li div').die('click');
			$('#top_navi_inner ul li div').live('click', function()
			{
				if(typeof($(this).next('a').attr('rel')) == 'undefined' || $(this).next('a').attr('rel').length == 0)
				{
					document.location.href = $(this).next('a').attr('href');
					return false;
				}
				else
				{
					// open pull down menu
					$(this).next('a').trigger('click');
				}
			});
		}
	}

    this.setCurrentsClasses = function()
    {
        $.each(parentsIdsBak, function(i, e){
            if(parseInt(e) !== js_options.global.categories_id)
            {
                if($('.cat_go_up_button').length <= 0)
                {
                    $('#menu_cat_id_' + e + '').addClass('parentOfCurrent');
                }
                $('#megadropdown_' + e + '').addClass('parentOfCurrent');
                if($('#megadropdown_top_link_id_' + e + '').next('ul').length > 0)
                {
                    $('#megadropdown_top_link_id_' + e + '').addClass('parentOfCurrent');
                }
                else
                {
                    $('#megadropdown_top_link_id_' + e + '').closest('li').addClass('parentOfCurrent');
                }
            }
            else
            {
                if($('.cat_go_up_button').length <= 0)
                {
                    $('#menu_cat_id_' + e + '').addClass('current');
                }
                $('#megadropdown_' + e + '').addClass('current');
                if($('#megadropdown_top_link_id_' + e + '').next('ul').length > 0)
                {
                    $('#megadropdown_top_link_id_' + e + '').addClass('current');
                }
                else
                {
                    $('#megadropdown_top_link_id_' + e + '').closest('li').addClass('current');
                }
            }
        });
    }


    this.init_binds();
}
/*<?php
}
?>*/