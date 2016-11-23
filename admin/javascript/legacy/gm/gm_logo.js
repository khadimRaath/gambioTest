/* 
 --------------------------------------------------------------
 gm_logo.js 2014-04-23 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2014 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 
 IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
 MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
 NEW GX-ENGINE LIBRARIES INSTEAD.
 --------------------------------------------------------------
*/

function gm_get_position(event, box)
{
	var position = new Array(2);
	var left = 0;
	var top = 0;
	var element_width = $(box).outerWidth();
	var element_height = $(box).outerHeight();

	var browser_width = $(document).width();

	var browser_scroll_top = $(document).scrollTop();
	var browser_scroll_left = $(document).scrollLeft();


	var browser_height = $(window).height();

	if(element_width + event.pageX > browser_width + browser_scroll_left)
	{
		position['left'] = browser_width + browser_scroll_left - element_width - 30;
	} 
	else 
	{
		position['left'] = event.pageX;
	}
	if(element_height + event.pageY > browser_height + browser_scroll_top)
	{
		position['top'] = browser_height + browser_scroll_top - element_height - 10;
	}
	else
	{
		position['top'] = event.pageY;
	}

	return position;
}


function hide_logo()
{
	$("#gm_big_logo").hide('fast');
}

$(document).ready(function()
{
	var img_width = $("#gm_box_content img").outerWidth();
	var img_height = $("#gm_box_content img").outerHeight();
	var element_width = $(".content_width").outerWidth();

	if (img_width > element_width - 200)
	{
		var rel = img_width / img_height;
		var height = (element_width - 250) / rel;
		$("#gm_box_content img").attr("width", element_width - 250).attr("height", height);
		
		$("#gm_box_content img").click(function(event)
		{
			$("#imageviewer").html('<img onclick="hide_logo()" id="gm_big_logo" src="' + $("#gm_box_content img").attr("src") + '">');

			var position = gm_get_position(event, '#imageviewer');
			$("#imageviewer").show('fast');

			$("#imageviewer").css(
			{
				"position": "absolute",
				"top": position['top'] + "px",
				"left": position['left'] + "px"
			});
		});
	}
});