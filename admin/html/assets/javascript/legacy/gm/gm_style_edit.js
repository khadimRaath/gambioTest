/* 
	--------------------------------------------------------------
	gm_style_edit.js 10.07.2008 pt@gambio
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2007 Gambio OHG
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
   
    IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.    
    MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE   
    NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
*/

function gm_get_position(event) {

	var position			= new Array(2);	
	var left				= 0;
	var top					= 0;
	var element_width		= $("#gm_color_box").outerWidth();
	var element_height		= $("#gm_color_box").outerHeight();
	var browser_width		= $(document).width();
	var browser_scroll_top	= $(document).scrollTop();
	var browser_scroll_left	= $(document).scrollLeft();		
	var browser_height		= $(window).height();

	if(element_width + event.pageX > browser_width + browser_scroll_left) {
		position['left'] = browser_width + browser_scroll_left - element_width  - 30;	
	} else {
		position['left'] = event.pageX;
	}
	if(element_height + event.pageY > browser_height + browser_scroll_top) {
		position['top'] = browser_height + browser_scroll_top - element_height - 10;
	} else {
		position['top'] = event.pageY;
	}

	return position;
}	

$(document).ready(function(event) {

		$('#colorpicker').farbtastic('#color');
		 
		// -> manage escaping 
		$("#gm_color_box .close").click(function(){
			$("#gm_color_box").hide('normal');
		});	
		
		// -> manage saving
		$("#gm_color_box .save").click(function(){			
			var new_color = $("input#color").val();
			var input_ref = $("input#actual").val();
			
			$("#color_" + input_ref).css({
				"background-color" : new_color
			});
			$("input#gm_style_value_" + input_ref).val(new_color);
			$("input#" + input_ref).val(new_color);
			
			$("#gm_color_box").hide('normal');		
		});

	$(".gm_click").click(function(event) {

		var gm_actual_button_id = $(this).attr("id");		
		var gm_actual_id		= gm_actual_button_id.replace(/color_/g, "");	
		var gm_actual_color		= $("#" + gm_actual_id).val();							
		$.farbtastic('#colorpicker').setColor(gm_actual_color);

		var position = gm_get_position(event);
		$('#gm_color_box').css({
			zIndex: 		"300",
			position: 	"absolute",
			"top":  position['top'] + "px",
			"left":  position['left']  + "px",
			background: "white",
			border: 		"1px solid #ccc",
			background: "#ffffff"
		});
		$("#gm_color_box").show('fast');
		
		$('#color').val(gm_actual_color);
		$('#actual').val(gm_actual_id);	

	});
});