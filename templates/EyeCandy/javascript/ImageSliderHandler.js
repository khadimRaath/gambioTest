/* ImageSliderHandler.js <?php
#   --------------------------------------------------------------
#   ImageSliderHandler.js 2011-01-28 gambio
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
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('3 y(){5 e=0;$(z).A(3(){$(\'#j\').k(\'B\',\'C\');D(l.m,E)});2.m=3(){5 a;$(\'#n o\').F(3(){a=$(2).8(\'.9\').f(\'p\');$(2).8(\'.9\').f(\'G\',a);$(2).8(\'.9\').H(\'p\')});5 b=$(\'#n\').I();$(\'#q\').J(b);l.r()};2.r=3(){e=$(\'#q o\').K;5 a=$(\'#L\').g();$("#j").4({s:1,h:2.h,M:t,N:t,O:\'P\',Q:R(a),S:\'T\',U:2.u});$(\'.4-V-W .4-X-Y\').k({\'Z\':$(\'#10\').g(),\'11\':$(\'#12\').g()})};2.h=3(b){$(\'.4-i a\').13(\'14\',3(){5 a=$(2).f(\'15\').16(\'v\',\'\');b.s(17.4.18(a));$(\'.4-i a\').w(\'6\');$(2).x(\'6\');19 1a})};2.u=3(a,b,c,d){7=(c%e);1b(7==0){7=e}$(\'.4-i a\').w(\'6\');$(\'#v\'+7).x(\'6\')}}',62,74,'||this|function|jcarousel|var|jcarousel_control_a_active|actindex|find|slideImgControl||||||attr|val|initCallback|control|jcarousel_box|css|coo_image_slider_handler|swap_images|jcarousel_image_box_hidden|li|title|jcarousel_image_box|init_binds|scroll|null|initHighlight|slideImg_|removeClass|addClass|ImageSliderHandler|document|ready|visibility|visible|setTimeout|1000|each|src|removeAttr|html|append|length|jcarousel_interval|buttonNextHTML|buttonPrevHTML|animation|slow|auto|parseInt|wrap|circular|itemVisibleInCallback|skin|tango|clip|horizontal|width|jcarousel_width|height|jcarousel_height|bind|click|id|replace|jQuery|intval|return|false|if'.split('|'),0,{}));
/*<?php
}
else
{
?>*/
function ImageSliderHandler()
{
	var v_slider_elements = 0;

	$(document).ready(
		function()
		{
			$('#jcarousel_box').css('visibility', 'visible');

			setTimeout(coo_image_slider_handler.swap_images, 1000);
		}
	);

	this.swap_images = function()
	{
		var t_img_src;

		$('#jcarousel_image_box_hidden li').each(
			function()
			{
				t_img_src = $(this).find('.slideImgControl').attr('title');
				$(this).find('.slideImgControl').attr('src', t_img_src);
				$(this).find('.slideImgControl').removeAttr('title');
			}
		);


		var t_image_list = $('#jcarousel_image_box_hidden').html();

		$('#jcarousel_image_box').append(t_image_list);

		coo_image_slider_handler.init_binds();
	}

	this.init_binds = function()
	{
		v_slider_elements = $('#jcarousel_image_box li').length;

		var t_interval = $('#jcarousel_interval').val();

		$("#jcarousel_box").jcarousel(
			{
				scroll:					1,
				initCallback:			this.initCallback,
				buttonNextHTML:			null,
				buttonPrevHTML:			null,
				animation:				'slow',
				auto:					parseInt(t_interval),
				wrap:					'circular',
				itemVisibleInCallback:	this.initHighlight
			}
		);

		$('.jcarousel-skin-tango .jcarousel-clip-horizontal').css(
			{
				'width':	$('#jcarousel_width').val(),
				'height':	$('#jcarousel_height').val()
			}
		);
	}

	this.initCallback = function(carousel)
	{
		$('.jcarousel-control a').bind('click', function()
			{
				var t_id = $(this).attr('id').replace('slideImg_', '');
				carousel.scroll(jQuery.jcarousel.intval(t_id));
				$('.jcarousel-control a').removeClass('jcarousel_control_a_active');
				$(this).addClass('jcarousel_control_a_active');
				return false;
			}
		);
	}

	this.initHighlight = function(carousel,objectli,liindex,listate)
	{
		actindex = (liindex % v_slider_elements);
		if(actindex == 0)
		{
			actindex = v_slider_elements;
		}

		$('.jcarousel-control a').removeClass('jcarousel_control_a_active');
		$('#slideImg_' + actindex).addClass('jcarousel_control_a_active');
	}
}
/*<?php
}
?>*/
