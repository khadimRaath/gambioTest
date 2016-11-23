/* filter_set_main.js <?php
#   --------------------------------------------------------------
#   filter_set_main.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$.routeFeatureSets = function()
{	
	var visible_feature_sets = $(".boxCenter #feature_set_box_container .feature_set_box");
	var visible_feature_sets_length = visible_feature_sets.length;
	
	var invisible_feature_sets = $(".boxCenter #hidden_feature_set_box_container .feature_set_box");
	var invisible_feature_sets_length = invisible_feature_sets.length;
	
	if(visible_feature_sets_length > 2)
	{
		$(".boxCenter #feature_set_box_container > .clear_both").eq(2).prependTo($(".boxCenter #hidden_feature_set_box_container"));
		$(".boxCenter #feature_set_box_container > .feature_set_box").eq(2).prependTo($(".boxCenter #hidden_feature_set_box_container"));
	}
	if(visible_feature_sets_length < 2 && invisible_feature_sets_length > 0)
	{
		$(".boxCenter #hidden_feature_set_box_container > .feature_set_box").eq(0).appendTo($(".boxCenter #feature_set_box_container"));
		$(".boxCenter #hidden_feature_set_box_container > .clear_both").eq(0).appendTo($(".boxCenter #feature_set_box_container"));
	}
	if($(".boxCenter #hidden_feature_set_box_container .feature_set_box").length == 0)
	{
		$(".boxCenter #hidden_feature_set_box_container").hide();
		$(".boxCenter #feature_set_show_all").addClass("show").removeClass("hide").hide();
	}
	else
	{
		$(".boxCenter #feature_set_show_all").show();
	}
	
	var t_new_invisible_count = $(".boxCenter #hidden_feature_set_box_container .feature_set_box").length;
	$(".boxCenter #feature_set_show_all span span").html(t_new_invisible_count);
	$('.gambio_scrollbar').jScrollPane();
	return true;
};

$(document).ready(function(){	
	$('.gambio_scrollbar').jScrollPane();
	var active_mode = false;
	var show_hidden_boxes = false;
	if($("#feature_set_show_all").hasClass("hide"))
	{
		show_hidden_boxes = true;
	}
	
	$("#feature_set_show_all span").bind("click", function()
	{
		var target = this;
		if(!active_mode)
		{
			active_mode = true;
			$("#hidden_feature_set_box_container").fadeToggle("fast", function()
			{
				if(show_hidden_boxes)
				{
					$("#feature_set_show_all").addClass("show");
					$("#feature_set_show_all").removeClass("hide");
					show_hidden_boxes = false;
				}
				else
				{
					$("#feature_set_show_all").removeClass("show");
					$("#feature_set_show_all").addClass("hide");
					show_hidden_boxes = true;
				}
				
				if ($('#hidden_feature_set_box_container').css('display') == 'none')
				{
					$('html,body').animate(
					{
						scrollTop: $("#feature_set_top").offset().top-35
					}, 'slow');
				}
				active_mode = false;
			});
		}
	});
});


$(".feature_set_lightbox_open").die("click");
$(".feature_set_lightbox_open").live("click", function()
{
	$(this).lightbox_plugin('open', {
		"width": 925, 
		"background_color": "#EFEFEF"
	});
	return false;
});

$(".feature_values a").die("mousedown");
$(".feature_values a").live("mousedown", function()
{
	return false;
});