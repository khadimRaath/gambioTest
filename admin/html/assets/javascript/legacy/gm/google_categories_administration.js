/* 
	--------------------------------------------------------------
	google_categories_administration.js 2014-07-11 tb@gambio
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
(function($){
	$.fn.google_categories_administration = function(options){      

		var self = this;  

		var settings = {
		};
		var loading_image = $("<img></img>").attr("src", "../images/loading.gif");
		var loading_image_box = $("<div></div>").addClass("loading_image_box").append(loading_image);
		
		var tmp = $(this).attr("id").split("_");
		
		if(tmp[0] == "product"){
			initialize();

			$(".google_category_list_add_button a").live("click", function(){
				$(".google_category_list_add_box").append(loading_image_box);
				$(".google_category_list_add_button a").hide();
				show_category_add_box("");
				return false;
			});

			$(".google_category_list .edit").live("click", function(){
				$(".google_category_list input").removeClass("edit");
				$(this).prev().addClass("edit");
				var t_value = $(this).prev().val();
				$(".google_category_list_add_box").append(loading_image_box);
				show_category_add_box(t_value);
				$(".google_category_list_add_button a").hide();
				$(".google_category_list_add_box div.category_select_container").remove();
				return false;
			});

			$(".google_category_list_add_box .add").live("click", function(){
				$(".google_category_list_add_button a").hide();
				var t_already_exists = false;
				$.each($(".google_category_list input"), function(key, value){
					if($(value).val() == $(".google_category_list_add_box .category_string").val()){
						t_already_exists = true;
					}
				});

				if($(".google_category_list_add_box .category_string").val() != "" && t_already_exists == false){
					if($(".google_category_list_add_box").hasClass("edit")){
						$(".google_category_list input.edit").val($(".google_category_list_add_box .category_string").val());
						$(".google_category_list input").removeClass("edit");
					}else{
						var t_input = $("<input></input>").attr("name", "category_list[][0]").addClass('category_list').attr("type", "text").attr("readonly", "readonly").val($(".google_category_list_add_box .category_string").val());
						var t_edit_icon = $('<i></i>').addClass('fa fa-pencil fa-lg fa-fw');
						var t_edit_button = $("<a></a>").attr("href", "#").addClass("edit btn-edit").append(t_edit_icon);
						$(".google_category_list").find('.delete-checkbox').val(0);
						$(".google_category_list").prepend(t_edit_button).prepend(t_input);
						$(".google_category_list span").show();
					}
					if ($('.google_category_list input[name^="category_list"]').length == 0)
					{
						$(".google_category_list_add_button a").show();
					}
					$(".google_category_list_add_box").hide();
					$(".google_category_list_add_box").removeClass("add edit");
					$(".google_category_list_add_box div.category_select_container").remove();
				}
                
				return false;
			});

			$(".google_category_list_add_box .cancel").live("click", function(){
				if ($('.google_category_list input[name^="category_list"]').length == 0)
				{
					$(".google_category_list_add_button a").show();
				}
				$(".google_category_list_add_box").hide();
				$(".google_category_list_add_box").removeClass("add edit"); 
				$(".google_category_list_add_box div.category_select_container").remove();
				$(".google_category_list input").removeClass("edit");
				return false;
			});

			$(".google_category_list_add_box select").live("change", function(){
				var next_elements = $(this).parent().nextAll().find('select');
				$.each(next_elements, function(key, value){
					$(value).remove();
				});
				update_category_string();
				if($(this).val() != ""){
					$(".google_category_list_add_box").append(loading_image_box);
					get_select_options($(".google_category_list_add_box .category_string").val());
				}
			});
		}
		
		function initialize(){
			$.ajax({
				type: "GET",
				url: "request_port.php?module=GoogleTaxonomy",
				data: {"action": "get_template", "template": "google_categories_administration.html"},
				success: function(template){
					$(".google_categories_administration").append(template);
					show_google_categories();
				}
			});
		}

		function show_google_categories(){
			var $t_category_id = $(".google_categories_administration").attr("id").replace("product_id_", "");
			$.ajax({
				type: "GET",
				url: "request_port.php?module=GoogleTaxonomy",
				data: {"action": "get_product_google_category_array", "product_id": $t_category_id},
				success: function(response){
					response = $.parseJSON(response);
					if(response.length > 0){
						$(".google_category_list_add_button a").hide();
					}
					$.each(response, function(key, value){
						$(".google_category_list span").show();
						var t_input = $("<input></input>").attr("name", "category_list[]["+value.v_products_google_categories_id+"]").addClass('category_list').attr("type", "text").attr("readonly", "readonly").val(value.v_google_category);
						var t_edit_icon = $('<i></i>').addClass('fa fa-pencil fa-lg fa-fw');
						var t_edit_button = $("<a></a>").attr("href", "#").addClass("edit btn-edit").append(t_edit_icon);
						$(".google_category_list").prepend(t_edit_button).prepend(t_input);
						$(".google_category_list").find('.delete-checkbox').val(value.v_products_google_categories_id); 
					});
				}
			});
		}

		function update_category_string(){
			$(".google_category_list_add_box .category_string").empty();
			$.each($(".google_category_list_add_box select"), function(key, value){
				if(key != 0){
					if($(value).val() != ""){
						var t_string = $(".google_category_list_add_box .category_string").val()
						t_string = t_string+" > "+$(value).val();
						$(".google_category_list_add_box .category_string").val(t_string);
					}
				}else{
					$(".google_category_list_add_box .category_string").val($(value).val());
				}
			});
		}

		function show_category_add_box(p_string){
			if(p_string == ""){
				$(".google_category_list_add_box").addClass("add");
			}else{
				$(".google_category_list_add_box").addClass("edit");
			}
			$(".google_category_list_add_box .category_string").val(p_string);
			get_select_options("", p_string);

			$(".google_category_list_add_box").show();
		}

		function show_category_select(p_string, options_array){
			if(options_array != false){
				var cat_array = new Array();
				if($.trim($(".google_category_list_add_box .category_string").val()) != ""){
					cat_array = $(".google_category_list_add_box .category_string").val().split(">");
					$.each(cat_array, function(key, value){						
						cat_array[key] = $.trim(value);
					});
				}

				var category_select = $("<select></select>");
				category_select.append($("<option></option>").attr("value", "").html($(".text.select").val()));
				$.each(options_array, function(key, value){
					value = $.trim(value);
					if(value != ""){
						if($.inArray(value, cat_array) != -1 && cat_array.length > 0 && $(".google_category_list_add_box").hasClass("edit")){
							p_string = $.trim(p_string);
							$(".google_category_list_add_box").append(loading_image_box);
							if(p_string != ""){
								get_select_options(p_string + " > " + value);
							}else{
								get_select_options(value);
							}
							category_select.append($("<option></option>").attr("value", value).html(value).attr("selected", "selected"));
						}else{
							category_select.append($("<option></option>").attr("value", value).html(value));
						}
					}
				});
				var category_select_wrapper = $('<div></div>').addClass('category_select_container').append(category_select);
				$(".google_category_select_boxes").append(category_select_wrapper);
			}
		}

		function get_select_options(p_string){
			$.ajax({
				type: "GET",
				url: "request_port.php?module=GoogleTaxonomy",
				data: {"action": "get_google_categories_array", "parent": p_string},
				success: function(response){
					response = $.parseJSON(response);
					show_category_select(p_string, response);
					$(loading_image_box).remove();
				},
				error: function(){
					$(loading_image_box).remove();
				}
			});

		}
		
		return false;      
	};  
})(jQuery);

$(document).ready(function(){
	if($("div.google_categories_administration").length == 1){
		$("div.google_categories_administration").google_categories_administration();
	}	
});
