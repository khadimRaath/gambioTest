/* social_share_plugin.js <?php
#   --------------------------------------------------------------
#   social_share_plugin.js 2014-03-14 tb@gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(6($){$.F.q=6(){7 c=$(8),3=$(r).f(\'G\').4(/[&]*H(=|\\/)[a-I-J-9,-]+/g,\'\');3=3.4(/\\?&/g,\'?\');3=3.4(/\\?$/g,\'\');7 d=l(3),s=l(h.t.K+$("#i .L M").f(\'N\')),j=\'\';m($("#i .n o u").v==1){j=$.w($("#i .n o u").k())}x{j=$.w($("#i .n o").k())}7 e=l(h.t.O+\' - \'+j+\' \');$.P(c,6(){$(8).Q("R",6(){y(8)});S 8});6 y(a){7 b="",2="";m($(a).5().T("p")){$(a).5().U("p");b=\'h.z.\'+$(a).5().f(\'A\')+\'.V\';$(a).5().B(".C").k(D(b))}x{$(a).5().W("p");2=\'h.z.\'+$(a).5().f(\'A\')+\'.X\';2=D(2);2=2.4(\'#Y#\',e);2=2.4(\'#Z#\',s);2=2.4(\'#r#\',3);2=2.4(\'#10#\',d);$(a).5().B(".C").k(2)}}}})(11);$(12).13(6(){m($(".E").v>0){$(".E").q()}});',62,66,'||t_code|v_location|replace|parent|function|var|this|||||||attr||js_options|product_info|t_product_headline|html|encodeURIComponent|if|info|h1|switch_on|social_share_plugin|location|v_product_image|global|span|length|trim|else|update_view|social_share|id|find|social_share_content|eval|social_share_image|fn|href|XTCsid|zA|Z0|shop_root|info_image_box|img|src|shop_name|each|bind|click|return|hasClass|removeClass|image|addClass|code|text|product_image|location_encoded|jQuery|document|ready'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
(function($){
    $.fn.social_share_plugin = function(){         

        var self = $(this);
        var v_location = window.location.href.replace(/[&]*XTCsid(=|\/)[a-zA-Z0-9,-]+/g, '');
        v_location = v_location.replace(/\?&/g, '?');
        v_location = v_location.replace(/\?$/g, '');        
        var v_location_encoded = encodeURIComponent(v_location);
        var v_product_image = encodeURIComponent(js_options.global.shop_root + $("#product_info .info_image_box img").attr('src'));
		var t_product_headline = '';
		if($("#product_info .info h1 span").length == 1)
		{
			t_product_headline = $.trim($("#product_info .info h1 span").html());
		}
		else
		{
			t_product_headline = $.trim($("#product_info .info h1").html());
		}
		
        var v_product_text = encodeURIComponent(js_options.global.shop_name + ' - ' + t_product_headline + ' ');

        $.each(self, function(){
            $(this).bind("click", function(){
                update_view(this);
            });
            return this;
        });
        
        function update_view(p_element){
            var t_image = "";
            var t_code = "";

            if($(p_element).parent().hasClass("switch_on")){
                $(p_element).parent().removeClass("switch_on");

                t_image = 'js_options.social_share.'+$(p_element).parent().attr('id')+'.image';
                $(p_element).parent().find(".social_share_content").html(eval(t_image));
            }else{
                $(p_element).parent().addClass("switch_on");

                t_code = 'js_options.social_share.'+$(p_element).parent().attr('id')+'.code';
                t_code = eval(t_code);
                t_code = t_code.replace('#text#', v_product_text);
                t_code = t_code.replace('#product_image#', v_product_image);
                t_code = t_code.replace('#location#', v_location);
                t_code = t_code.replace('#location_encoded#', v_location_encoded);
                $(p_element).parent().find(".social_share_content").html(t_code);
            }
        }
         
    };  
})(jQuery);

$(document).ready(function()
{
	if($(".social_share_image").length > 0){
		$(".social_share_image").social_share_plugin();
	}
});
/*<?php
}
?>*/