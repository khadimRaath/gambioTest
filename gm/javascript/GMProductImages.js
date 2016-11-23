/* GMProductImages.js <?php
#   --------------------------------------------------------------
#   GMProductImages.js 2014-09-09 gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('3 K(){h.S=3(a,b){$(\'T\').18(\'<o v="g"></o>\');$(\'#g\').f({1f:\'z\',A:\'B\',D:\'O\',Q:\'p%\',6:\'p%\'});4 c=11,12=0,8=19.1b.1d();l(8.n("s")>-1&&8.n("C")==-1){4 d=8.n("s")+5;l(8.E(d,1)<7){$(\'.F\').f({G:\'H\'})}}$(\'#g\').I(\'J.1p?L=M&N=q&P=\'+a+\'&9=\'+b,\'\',3(){k.t();k.m(b);U.V(1,1)});W.X(\'#g\');$(\'#Y\').f({Z:\'10\'});l($(r).6()>$(\'#i\').6()){4 e=$(r).6()}13{4 e=$(\'#i\').6()+14}$(\'#15\').f({6:e+\'16\'})};h.m=3(a){4 b=\'#17\'+a,2=$(b).u(\'a\').j(\'1a\');2=2.w(\'/\');2=2[2.1c-1];$(\'#i x\').1e(\'y\');$(b).1g(\'y\');$(\'#1h\').u(\'1i\').j(\'1j\',1k.1l.1m+\'q/1n/\'+2)};h.t=3(){$(\'#i x\').1o(3(e){4 a=$(h).j(\'v\'),9=a.w(\'R\');9=9[1];k.m(9)})}}',62,88,'||active_img_name|function|var||height||user_agent|image_nr||||||css|product_images_layer|this|product_images_box|attr|gmProductImages|if|activate_image|indexOf|div|100|product_images|document|msie|bind_fn|find|id|split|li|active|absolute|left|0px|opera|top|substr|lightbox_visibility_hidden|visibility|hidden|load|request_port|GMProductImages|module|Product|action|50px|pID|width|_|open_images|body|window|scrollTo|gmLightBox|load_box|menubox_gm_scroller|display|none|95|test_image_nr|else|200|__dimScreen|px|image_|append|navigator|href|userAgent|length|toLowerCase|removeClass|position|addClass|active_image|img|src|js_options|global|dir_ws_images|popup_images|click|php'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function GMProductImages()
{

	this.open_images = function(products_id, image_number)
	{
//		$('.wrap_site').append('<div id="product_images_layer"></div>');
		$('body').append('<div id="product_images_layer"></div>');
		$('#product_images_layer').css(
		{
			position: 	'absolute',
			left: 			'0px',
			top: 				'50px',
			width: 			'100%',
			height: 		'100%'
		});

		var test_products_id 	= 95;
		var test_image_nr 		= 0;

		var user_agent = navigator.userAgent.toLowerCase();
		if(user_agent.indexOf("msie") > -1 && user_agent.indexOf("opera") == -1){
			var msie_index = user_agent.indexOf("msie") + 5;
			if(user_agent.substr(msie_index,1) < 7){
				$('.lightbox_visibility_hidden').css(
				{
					visibility: 	'hidden'
				});
			}
		}

		$('#product_images_layer').load('request_port.php?module=Product&action=product_images&pID='+ products_id +'&image_nr='+ image_number,
																			'',
																			function(){
																				gmProductImages.bind_fn();
																				gmProductImages.activate_image(image_number);
																				window.scrollTo(1, 1);
																			});
		gmLightBox.load_box('#product_images_layer');

		// BOF MOD by PT
		$('#menubox_gm_scroller').css({
			display: 'none'
		});

		if($(document).height() > $('#product_images_box').height()) {
			var pt_height = $(document).height();
		} else {
			var pt_height = $('#product_images_box').height()+ 200;
		}
		$('#__dimScreen').css({ height: pt_height + 'px'});
		// EOF MOD by PT

	}


	this.activate_image = function(image_number)
	{
		var active_li 			= '#image_' + image_number;
		var active_img_name = $(active_li).find('a').attr('href');

		//IE6 patch:
		active_img_name = active_img_name.split('/');
		active_img_name = active_img_name[active_img_name.length - 1];

		$('#product_images_box li').removeClass('active');
		$(active_li).addClass('active');

		$('#active_image').find('img').attr('src', js_options.global.dir_ws_images + 'product_images/popup_images/' + active_img_name);
	}

	this.bind_fn = function()
	{
		$('#product_images_box li').click(function(e)
		{
			var li_id 		= $(this).attr('id');

			var image_nr	= li_id.split('_');
			image_nr			= image_nr[1];

			gmProductImages.activate_image(image_nr);
		});
	}

}
/*<?php
}
?>*/
