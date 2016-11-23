/* 
	--------------------------------------------------------------
	image_mapper.js 2014-07-11 tb@gambio
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
    $.fn.image_mapper = function(options){      
      
          var self = this;  
          
          var settings = {
            canvas_width: $(".sliderWidth").val(),
            canvas_height: $(".sliderHeight").val(),
            slider_image_id: $(this).parent().parent().find(".slider_set_id").val(),
            image_url: "../images/slider_images/"+$(this).parent().parent().find(".gx_slider_image_path").val(),
            interval_time: 20,
			lightbox_width: "960",
			lightbox_height: ""
          };
          
          var arr_areas = [];
          var resize_handles = [];
          var polygon_handles = [];  
          var canvas, canvas_context;
          var tmp_canvas, tmp_canvas_context;
          var element_is_drag = false, element_is_resize_drag = false;
          var actual_handling_num = -1, actual_area_num = -1;
          var mouse_pos_x, mouse_pos_y;
          var old_mouse_pos_x, old_mouse_pos_y;
          var canvas_is_valid = false;
          var actual_area_object = null;
          var tmp_actual_area_object = null;
          var actual_handling_line_color = '#ff0000';
          var actual_handling_line_size = 0.5;
          var actual_handling_box_color = '#ff0000'; 
          var actual_handling_box_size = 4; 
          var offset_x, offset_y;
          var poly_coords = [];
          var ckeditor = null;
          var flyover_content_changed = false;
          var interval;
          
		  
			var lightbox_package;	
			var lightbox_package_loading_image;
			var lightbox_package_shadow;      
			var lightbox_package_container;
			var lightbox_package_wrapper;  
			var lightbox_package_content;       
			var window_width;
			var window_height;
			var lightbox_width;
			var lightbox_height;
          
          return this.each(function() 
          {
            if(options)
            {
              $.extend(settings,options);
            }
			
			window_width = $(window).width();
			window_height = $(window).height();
            
            lightbox_package = $("<div></div>").addClass("lightbox_package image_mapper");
			lightbox_package_shadow = $("<div></div>").addClass("lightbox_package_shadow");
			lightbox_package.append(lightbox_package_shadow);
			lightbox_package_loading_image = $("<img></img>").attr("src", "../images/loading.gif");
			lightbox_package_loading_image.css("position", "absolute").css("top", window_height/2-8).css("left", window_width/2-8);
			lightbox_package.append(lightbox_package_loading_image);
			$("body").append(lightbox_package);

            if($.browser.msie){
              if($.browser.version<9){
                wrong_browser_message();
              }else{
                initialize();
              }
            }else{
              initialize();
            }
			
			$(document).bind("scroll", function(){
				update_window();
			});

			$(window).bind('resize', function() {
				update_window();
			});
			
			function update_window(){
				window_width = $(window).width();
				window_height = $(window).height();

				if(settings.lightbox_width != "auto" && settings.lightbox_width != ""){
					lightbox_width = settings.lightbox_width;
				}else if(settings.lightbox_width == "auto"){
					lightbox_width = window_width-60;
				}else{
					lightbox_width = lightbox_package_content.width()+40;
					alert(lightbox_package_content.width());
				}

				if(lightbox_width > window_width-60){
					lightbox_width = window_width-60;
				}

				lightbox_package_container.css("left", (window_width-lightbox_width)/2);

				if(settings.lightbox_height != "auto" && settings.lightbox_height != ""){
					lightbox_height = settings.lightbox_height;
				}else if(settings.lightbox_height == "auto"){
					lightbox_height = window_height-60;
				}

				if(lightbox_package_wrapper.height() > window_height-60 || lightbox_package_wrapper.height() < lightbox_package_content.height()+30){
					lightbox_package_wrapper.height(window_height-60);
					lightbox_height = window_height-60;
				}

				lightbox_package.width(window_width);
				lightbox_package.height(window_height);
				lightbox_package_shadow.width(window_width);
				lightbox_package_shadow.height(window_height);
				lightbox_package_container.width(lightbox_width);
				lightbox_package_container.height(lightbox_height);
				lightbox_package_wrapper.height(lightbox_height);
			}

            function wrong_browser_message(){
              fillTemplate(lightbox_package);
        
              function fillTemplate(target){
                
                $.ajax({
                  type: "POST",
                  url: "request_port.php?module=SliderAdmin",
                  data: {"action": "get_template", "template": "image_mapper_wrong_browser.html"},
                  success: function(template){
					if(template.match(/WARNING\(512\)/) || template == ""){
						lightbox_package.remove();
						alert("Smarty Error - Unable to find Template");
					}else if(template.match(/t_action_request not found/)){
						lightbox_package.remove();
						alert("Warning - Unable to call function");
					}else{
						lightbox_package_loading_image.hide();
						lightbox_package_shadow.show();
						$(target).append(template);
						lightbox_package_container = $(".lightbox_package_container");
						lightbox_package_wrapper = $(".lightbox_package_wrapper");
						lightbox_package_content = $(".lightbox_package_content");
						lightbox_package_container.show();
						update_window();
						$(".lightbox_package_close").bind("click", function(){
							lightbox_package.remove();
						});
					}
                  },
				  error: function(){
					  lightbox_package.remove();
					  alert("Connection Error - Unable to connect to server");
				  }
                });
              }
            }
            
            function initialize(){

              fillTemplate(lightbox_package);
        
              function fillTemplate(target){
                $.ajax({
                  type: "POST",
                  url: "request_port.php?module=SliderAdmin",
                  data: {"action": "get_template", "template": "image_mapper_standard.html"},
                  success: function(template){
					if(template.match(/WARNING\(512\)/) || template == ""){
						lightbox_package.remove();
						alert("Smarty Error - Unable to find Template");
					}else if(template.match(/t_action_request not found/)){
						lightbox_package.remove();
						alert("Warning - Unable to call function");
					}else{
						lightbox_package_loading_image.hide();
						lightbox_package_shadow.show();
						$(target).append(template);
						lightbox_package_container = $(".lightbox_package_container");
						lightbox_package_wrapper = $(".lightbox_package_wrapper");
						lightbox_package_content = $(".lightbox_package_content");
						lightbox_package_container.show();
						update_window();

						$(".gx_control .float_left").css("display", "none");
						$(".gx_control .float_right").css("display", "none");
						$(".gx_control .add").css("display", "block");
						$(".gx_control .fields input").attr("disabled", "disabled");
						$(".gx_control .fields select").attr("disabled", "disabled");
						$(".gx_control .editor").css("display", "none");
						$(".gx_control .fields").css("display", "none");
						$(".gx_image_container").width(settings.canvas_width).height(settings.canvas_height);
						$(".gx_image_container img").attr("src", settings.image_url);
						$(".gx_image_container canvas").attr("width", settings.canvas_width).attr("height", settings.canvas_height);

						CKEDITOR.replace('image_mapper_flyover', {
							toolbar: "ImageMapper",
							filebrowserBrowseUrl: "includes/ckeditor/filemanager/index.html",
							language: $("#ckeditor_language").val(),
							baseHref: $("#ckeditor_base_href").val(),
							width: "900px",
							height: "100px",
							resize_enabled: false
						});

						canvas = document.getElementById("image_canvas");
						canvas_context = canvas.getContext("2d");
						canvas_context.strokeStyle = '#ff0000';
						canvas_context.lineWidth = actual_handling_line_size;

						tmp_canvas = document.createElement('canvas');
						tmp_canvas.width = settings.canvas_width;
						tmp_canvas.height = settings.canvas_height;
						tmp_canvas_context = tmp_canvas.getContext('2d');

						canvas.onselectstart = function () {return false;}                    
						canvas.onmousedown = myDown;
						canvas.onmouseup = myUp;
						canvas.onmousemove = myMove;

						interval = setInterval(mainDraw, settings.interval_time);
						
						update_window();

						ajax_request({
						  action: "get_image_area_data"
						});

						$(".lightbox_package_close").bind("click", function(){
						  if($(".gx_control .buttons .add").css("display") == "none"){
							var window_confirm = confirm($("#alert_message_text").val());
							if (window_confirm){
							  clearInterval(interval);
							  lightbox_package.remove();
							}
						  }else{
							clearInterval(interval);
							lightbox_package.remove();
						  }
						});

						$(".buttons .add").bind("click", function(){
						  $(".gx_control .float_right").css("display", "block");
						  $(".gx_control .float_right.add").css("display", "none");
						  $(".gx_control .float_right.save").css("display", "none");
						  $(".gx_control .float_right.delete").css("display", "none");
						  $(".gx_control .fields #field_type").removeAttr("disabled");
						  $(".gx_control .fields input").removeAttr("disabled");
						  $(".gx_control .fields select").removeAttr("disabled");
						  $(".gx_control .float_left").css("display", "block");
						  $(".gx_control .division").css("display", "none");
						  $(".gx_control .fields").css("display", "block");
						  $(".gx_control .float_left.flyover").css("display", "none");
						  $("#field_id").val(0);
						  return false;
						});

						$("#field_type").bind("change", function(){
						  $(".gx_control .float_right.save").css("display", "none");
						  $(".gx_control .float_right.delete").css("display", "none");
						  $(".gx_control .float_left.flyover").css("display", "none");
						  if($(this).val() != ""){
							if(arr_areas.length > 0){
							  if(arr_areas[arr_areas.length-1].slider_image_area_id == 0){
								arr_areas.splice(arr_areas.length-1, 1);
							  }
							}

							clear(canvas_context);
							clear(tmp_canvas_context);
							actual_area_object = null;
							unbind_poly();

							switch($(this).val()){
							  case "rectangle":
							  case "circle":
								var area = new Rectangle_box;
								area.slider_image_id = settings.slider_image_id;
								area.slider_image_area_id = 0;
								area.shape = $(this).val();
								area.title = "";
								area.link_url = "";
								area.link_target = "";
								area.flyover_content = "";
								area.coords = "";
								area.width = 60;
								area.height = 60;
								area.pos_x = settings.canvas_width/2-30;
								area.pos_y = settings.canvas_height/2-30;
								arr_areas.push(area);
								break;
							  case "polygon":
								bind_poly();
								break;
							  default:
								break;
							}
							invalidate();
						  }
						});

						$(".buttons .save").bind("click", function(){
						  $(".gx_control .float_left").css("display", "block");
						  $(".gx_control .float_right").css("display", "none");
						  $(".gx_control .fields input").attr("disabled", "disabled");
						  $(".gx_control .fields select").attr("disabled", "disabled");

						  actual_area_object.title = $("#field_title").val();
						  actual_area_object.link_url = $("#field_href").val();
						  actual_area_object.link_target = $("#field_target").val();

						  if(flyover_content_changed == true){
							actual_area_object.flyover_content = CKEDITOR.instances["image_mapper_flyover"].getData();  
						  }

						  if(actual_area_object.slider_image_area_id == 0){
							ajax_request({
							  action: "create_area"
							});   
						  }else{
							ajax_request({
							  action: "save_area",
							  area: actual_area_object
							});
						  }

						  $(".gx_control .division").css("display", "block");
						  $(".gx_control .editor").css("display", "none");
						  $(".gx_control .fields").css("display", "none");
						  $(".gx_control .float_left").css("display", "none");
						  $(".gx_control .add").css("display", "block");
						  $(".gx_control .fields input").val("");
						  $(".gx_control .fields select").val("");
						  CKEDITOR.instances.image_mapper_flyover.setData("");
						  flyover_content_changed = false;
						  return false;
						});

						$(".buttons .cancle").bind("click", function(){
						  $(".gx_control .float_left").css("display", "none");
						  $(".gx_control .float_right").css("display", "none");
						  $(".gx_control .add").css("display", "block");
						  $(".gx_control .fields input").attr("disabled", "disabled");
						  $(".gx_control .fields select").attr("disabled", "disabled");
						  $(".gx_control .division").css("display", "block");
						  $(".gx_control .fields").css("display", "none");
						  $(".gx_control .editor").css("display", "none");

						  if($("#field_id").val() == 0){
							if($("#field_type").val() == "polygon"){
							  if(poly_coords.length == 0){
								arr_areas.splice(arr_areas.length-1, 1);
							  }
							  unbind_poly();
							  poly_coords = [];
							}if($("#field_type").val() == "rectangle" || $("#field_type").val() == "circle"){
							  actual_area_object = null;
							  tmp_actual_area_object = null;
							  arr_areas.splice(arr_areas.length-1, 1);
							}
						  }else{
							var selected_key = -1;
							$.each(arr_areas, function(key, value){
							  if(actual_area_object.slider_image_area_id == value.slider_image_area_id){
								selected_key = key;
							  }
							});
							if(actual_area_object.shape == "polygon"){
							  arr_areas[selected_key].polygon_coords = tmp_actual_area_object.polygon_coords;
							  unbind_poly();
							  poly_coords = [];
							  actual_area_object = null;
							  tmp_actual_area_object = null;
							}else{
							  arr_areas[selected_key].pos_x = tmp_actual_area_object.pos_x;
							  arr_areas[selected_key].pos_y = tmp_actual_area_object.pos_y;
							  arr_areas[selected_key].width = tmp_actual_area_object.width;
							  arr_areas[selected_key].height = tmp_actual_area_object.height;
							  actual_area_object = null;
							  tmp_actual_area_object = null;
							}
						  }     

						  $(".gx_control .fields input").val("");
						  $(".gx_control .fields select").val("");
						  CKEDITOR.instances.image_mapper_flyover.setData("");
						  flyover_content_changed = false;
						  invalidate();
						  return false;
						});

						$(".buttons .delete").bind("click", function(){
						  $(".gx_control .float_left").css("display", "none");
						  $(".gx_control .float_right").css("display", "none");
						  $(".gx_control .add").css("display", "block");
						  $(".gx_control .fields input").attr("disabled", "disabled");
						  $(".gx_control .fields select").attr("disabled", "disabled");
						  $(".gx_control .division").css("display", "block");
						  $(".gx_control .fields").css("display", "none");
						  $(".gx_control .editor").css("display", "none");

						  if(actual_area_object != null){
							var delete_key = -1;
							$.each(arr_areas, function(key, value){
							  if(actual_area_object.slider_image_area_id == value.slider_image_area_id){
								delete_key = key;
							  }
							});
							if(delete_key != -1){
							  ajax_request({
								action: "delete_area",
								area: arr_areas[delete_key]
							  });
							}
						  }
						  $(".gx_control .fields input").val("");
						  $(".gx_control .fields select").val("");
						  CKEDITOR.instances.image_mapper_flyover.setData("");
						  flyover_content_changed = false;
						  return false;
						});

						$(".buttons .general").bind("click", function(){
						  if(actual_area_object != null){
							  actual_area_object.flyover_content = CKEDITOR.instances["image_mapper_flyover"].getData();  
						  }
						  $(".gx_control .division").css("display", "none");
						  $(".gx_control .fields").css("display", "block");
						  $(".gx_control .editor").css("display", "none");
						  return false;
						});

						$(".buttons .flyover").bind("click", function(){
							CKEDITOR.instances.image_mapper_flyover.setData(actual_area_object.flyover_content);
                          $("iframe").css("position", "fixed");         
						  flyover_content_changed = true;
						  $(".gx_control .division").css("display", "none");
						  $(".gx_control .fields").css("display", "none");
						  $(".gx_control .editor").css("display", "block");
						  update_window();
						  return false;
						});
					}
                  },
				  error: function(){
					  lightbox_package.remove();
					  alert("Connection Error - Unable to connect to server");
				  }
                });
              }
            }
            
              
            function Rectangle_box() {
              this.pos_x = 0;
              this.pos_y = 0;
              this.width = 1; 
              this.height = 1;
            }

            Rectangle_box.prototype = {
              draw: function(context) {
                if (context === tmp_canvas_context) {
                  context.fillStyle = "#000000";
                  clear(context);
                } else {
                  context.fillStyle = "#ffffff";
                }
                
                if(this.shape == "rectangle"){
                  if (this.pos_x + this.width > settings.canvas_width) this.pos_x = settings.canvas_width - this.width; 
                  if (this.pos_y + this.height > settings.canvas_height) this.pos_y = settings.canvas_height - this.height; 
                  if(this.pos_x < 0) this.pos_x = 0;
                  if(this.pos_y < 0) this.pos_y = 0;
                  
                  context.globalAlpha=0.7;
                  context.fillRect(this.pos_x,this.pos_y,this.width,this.height);          
                  context.globalAlpha=1;        
                  context.strokeRect(this.pos_x,this.pos_y,this.width,this.height);
                }else if(this.shape == "circle"){
                  if (this.pos_x + this.width > settings.canvas_width) this.pos_x = settings.canvas_width - this.width; 
                  if (this.pos_y + this.height > settings.canvas_height) this.pos_y = settings.canvas_height - this.height; 
                  if(this.pos_x < 0) this.pos_x = 0;
                  if(this.pos_y < 0) this.pos_y = 0;  
                  
                  context.globalAlpha=0.7;
                  context.beginPath();
                  if(this.width > this.height){
                    context.arc(this.pos_x+this.width/2,this.pos_y+this.height/2,this.height/2,0,Math.PI*2,false);
                  }else{
                    context.arc(this.pos_x+this.width/2,this.pos_y+this.height/2,this.width/2,0,Math.PI*2,false);
                  }
                  context.globalAlpha=1;    
                  context.strokeRect(this.pos_x,this.pos_y,this.width,this.height);
                  context.closePath();
                  context.globalAlpha=0.7;
                  context.fillStyle = "#ffffff";
				  context.fill();
                  context.globalAlpha=1;
                }else if(this.shape == "polygon"){
                  var tmp_this_id = this.slider_image_area_id;
                  context.beginPath();
                  $.each(arr_areas, function(key1, value1){
                    if(tmp_this_id == value1.slider_image_area_id){
                      var tmp_start_pos_x = value1.polygon_coords[0].x;
                      var tmp_start_pos_y = value1.polygon_coords[0].y;
                      $.each(value1.polygon_coords, function(key2, value2){
                        if(key2 == 0){          
                          context.moveTo(value2.x, value2.y);
                        }else{
                          context.lineTo(value2.x, value2.y);
                          if(key2 == value1.polygon_coords.length-1){
                            context.lineTo(tmp_start_pos_x, tmp_start_pos_y);
                          }
                        }
                      });
                      context.globalAlpha=0.7;
                      context.fillStyle = "#ffffff";
					  context.fill();
                      context.globalAlpha=1;
                    }
                  });
                  context.stroke();
                  context.closePath();
                  $.each(arr_areas, function(key1, value1){
                    if(value1.shape == "polygon"){
                      $.each(value1.polygon_coords, function(key2, value2){
                        context.strokeRect(value2.x-2, value2.y-2,4,4);
                        if(actual_area_object != null){
                          if(actual_area_object.slider_image_area_id == value1.slider_image_area_id){
                            context.fillStyle = "#ff0000";
                            context.fillRect(value2.x-2, value2.y-2, 4,4);
                            context.fillStyle = "#ffffff";
                          }else{
                            context.fillStyle = "#ffffff";
                            context.fillRect(value2.x-2, value2.y-2, 4,4);
                          }
                        }else{
                          context.fillStyle = "#ffffff";
                          context.fillRect(value2.x-2, value2.y-2, 4,4);
                        }
                        polygon_handles[key1][key2].x = value2.x-2;
                        polygon_handles[key1][key2].y = value2.y-2;
                      });
                    }
                  });
                }

                if (actual_area_object === this && actual_area_object.shape != "polygon") {
                  context.strokeStyle = actual_handling_line_color;
                  context.lineWidth = actual_handling_line_size;
                  context.strokeRect(this.pos_x,this.pos_y,this.width,this.height);

                  var half = actual_handling_box_size / 2;

                  resize_handles[0].x = this.pos_x-half;
                  resize_handles[0].y = this.pos_y-half;
                  resize_handles[1].x = this.pos_x+this.width/2-half;
                  resize_handles[1].y = this.pos_y-half;
                  resize_handles[2].x = this.pos_x+this.width-half;
                  resize_handles[2].y = this.pos_y-half;
                  resize_handles[3].x = this.pos_x-half;
                  resize_handles[3].y = this.pos_y+this.height/2-half;
                  resize_handles[4].x = this.pos_x+this.width-half;
                  resize_handles[4].y = this.pos_y+this.height/2-half;
                  resize_handles[5].x = this.pos_x-half;
                  resize_handles[5].y = this.pos_y+this.height-half;
                  resize_handles[6].x = this.pos_x+this.width/2-half;
                  resize_handles[6].y = this.pos_y+this.height-half;
                  resize_handles[7].x = this.pos_x+this.width-half;
                  resize_handles[7].y = this.pos_y+this.height-half;

                  context.fillStyle = actual_handling_box_color;
                  for (var i = 0; i < 8; i ++) {
                    var cur = resize_handles[i];
                    context.fillRect(cur.x, cur.y, actual_handling_box_size, actual_handling_box_size);
                  }
                }
              }
            }
            
            function clear(c) {
              c.clearRect(0, 0, settings.canvas_width, settings.canvas_height);
            }

            function mainDraw() {
              if (canvas_is_valid == false) {
                clear(canvas_context);
                var l = arr_areas.length;
                for (var i = 0; i < l ; i++) {
                  arr_areas[i].draw(canvas_context);
                }
                canvas_is_valid = true;
              }
            }

            function myMove(e){
              if (element_is_drag) {
                getMouse(e);
                if(actual_area_object.shape == "rectangle" || actual_area_object.shape == "circle"){
                  getMouse(e);
                  actual_area_object.pos_x = mouse_pos_x - offset_x;
                  actual_area_object.pos_y = mouse_pos_y - offset_y;  
                }
                
                if(actual_area_object.shape == "polygon"){
                  $.each(arr_areas, function(key, value){
                    if(actual_area_object.slider_image_area_id == value.slider_image_area_id){
                      $.each(value.polygon_coords, function(key2, value2){
                        value2.x = parseInt(value2.x) + (mouse_pos_x - old_mouse_pos_x);
                        value2.y = parseInt(value2.y) + (mouse_pos_y - old_mouse_pos_y);  
                      });
                    }
                  });
                  getMouse(e);
                  actual_area_object.pos_x = mouse_pos_x - offset_x;
                  actual_area_object.pos_y = mouse_pos_y - offset_y; 
                }
                
                old_mouse_pos_x = mouse_pos_x;
                old_mouse_pos_y = mouse_pos_y;
                invalidate();
              } else if (element_is_resize_drag) {
                var oldx = actual_area_object.pos_x;
                var oldy = actual_area_object.pos_y;
                if(actual_area_object.shape == "rectangle" || actual_area_object.shape == "circle"){
                  switch (actual_handling_num) {
                    case 0:
                      actual_area_object.pos_x = mouse_pos_x;
                      actual_area_object.pos_y = mouse_pos_y;
                      actual_area_object.width += oldx - mouse_pos_x;
                      actual_area_object.height += oldy - mouse_pos_y;
                      break;
                    case 1:
                      actual_area_object.pos_y = mouse_pos_y;
                      actual_area_object.height += oldy - mouse_pos_y;
                      break;
                    case 2:
                      actual_area_object.pos_y = mouse_pos_y;
                      actual_area_object.width = mouse_pos_x - oldx;
                      actual_area_object.height += oldy - mouse_pos_y;
                      break;
                    case 3:
                      actual_area_object.pos_x = mouse_pos_x;
                      actual_area_object.width += oldx - mouse_pos_x;
                      break;
                    case 4:
                      actual_area_object.width = mouse_pos_x - oldx;
                      break;
                    case 5:
                      actual_area_object.pos_x = mouse_pos_x;
                      actual_area_object.width += oldx - mouse_pos_x;
                      actual_area_object.height = mouse_pos_y - oldy;
                      break;
                    case 6:
                      actual_area_object.height = mouse_pos_y - oldy;
                      break;
                    case 7:
                      actual_area_object.width = mouse_pos_x - oldx;
                      actual_area_object.height = mouse_pos_y - oldy;
                      break;
                  }
                }
                if(actual_area_object.shape == "polygon"){
                  arr_areas[actual_area_num].polygon_coords[actual_handling_num].x = mouse_pos_x;
                  arr_areas[actual_area_num].polygon_coords[actual_handling_num].y = mouse_pos_y;
                }
                invalidate();
              }
              getMouse(e);
              if (actual_area_object !== null && !element_is_resize_drag) {
                var self = this;
                var tmp_ready = false;
                if(actual_area_object.shape == "rectangle" || actual_area_object.shape == "circle"){
                  for (var i = 0; i < 8; i++) {
                    var cur = resize_handles[i];
                    if (mouse_pos_x >= cur.x && mouse_pos_x <= cur.x + actual_handling_box_size &&
                        mouse_pos_y >= cur.y && mouse_pos_y <= cur.y + actual_handling_box_size) {
                      actual_handling_num = i;
                      invalidate();
                      switch (i) {
                        case 0:
                          self.style.cursor='nw-resize';
                          break;
                        case 1:
                          self.style.cursor='n-resize';
                          break;
                        case 2:
                          self.style.cursor='ne-resize';
                          break;
                        case 3:
                          self.style.cursor='w-resize';
                          break;
                        case 4:
                          self.style.cursor='e-resize';
                          break;
                        case 5:
                          self.style.cursor='sw-resize';
                          break;
                        case 6:
                          self.style.cursor='s-resize';
                          break;
                        case 7:
                          self.style.cursor='se-resize';
                          break;
                      }
                      tmp_ready = true;
                    }
                  }
                }

                if(actual_area_object.shape == "polygon"){
                  $.each(arr_areas, function(key, value){
                    if(value.slider_image_area_id == actual_area_object.slider_image_area_id){
                      $.each(value.polygon_coords, function(key2, value2){
                        var cur = polygon_handles[key][key2];
                        if (mouse_pos_x >= cur.x && mouse_pos_x <= cur.x + actual_handling_box_size &&
                            mouse_pos_y >= cur.y && mouse_pos_y <= cur.y + actual_handling_box_size) {
                          actual_handling_num = key2;
                          actual_area_num = key;
                          invalidate();
                          self.style.cursor='crosshair';
                          tmp_ready = true;
                        }
                      });
                    }
                  });
                }

                if(tmp_ready == false ){
                  element_is_resize_drag = false;
                  actual_area_num = -1;
                  actual_handling_num = -1;
                  this.style.cursor='auto';
                }
              }
            }

            function myDown(e){
              getMouse(e);
              if (actual_handling_num !== -1) {
                element_is_resize_drag = true;
                return;
              }
              clear(tmp_canvas_context);

              var l = arr_areas.length;
              for (var i = l-1; i >= 0; i--) {
                arr_areas[i].draw(tmp_canvas_context);
                var imageData = tmp_canvas_context.getImageData(mouse_pos_x, mouse_pos_y, 1, 1);
                
                if (imageData.data[3] > 0) {
                  if($("#field_id").val() == "" || arr_areas[i].slider_image_area_id == $("#field_id").val())
                  {
                    actual_area_object = arr_areas[i];
                    update_control_container(actual_area_object);
                    if(actual_area_object.shape == "rectangle" || actual_area_object.shape == "circle"){
                      offset_x = mouse_pos_x - actual_area_object.pos_x;
                      offset_y = mouse_pos_y - actual_area_object.pos_y;
                      actual_area_object.pos_x = mouse_pos_x - offset_x;
                      actual_area_object.pos_y = mouse_pos_y - offset_y;
                    }else{
                      old_mouse_pos_x = mouse_pos_x;
                      old_mouse_pos_y = mouse_pos_y;
                    }
                    element_is_drag = true;
                  }
                  invalidate();
                  clear(tmp_canvas_context);                  
                  if(tmp_actual_area_object == null && actual_area_object != null){
                    if(actual_area_object.shape == "polygon"){
                      var tmp_coords = [];
                      $.each(actual_area_object.polygon_coords, function(key, value){
                        tmp_coords.push({x: value.x, y: value.y});
                      });                    
                      tmp_actual_area_object = {
                        polygon_coords: tmp_coords
                      };
                    }else{
                      tmp_actual_area_object = {
                        pos_x: actual_area_object.pos_x,
                        pos_y: actual_area_object.pos_y,
                        width: actual_area_object.width,
                        height: actual_area_object.height
                      };
                    }
                  }
                  return;             
                }
                 
              }
            }

            function myUp(){
              element_is_drag = false;
              element_is_resize_drag = false;
              actual_area_num = -1;
              actual_handling_num = -1;
            }

            function invalidate() {
              canvas_is_valid = false;
            }

            function getMouse(e) {
              var element = canvas, offsetX = 0, offsetY = 0;
              if (element.offsetParent) {
                do {
                  offsetX += element.offsetLeft;
                  offsetY += element.offsetTop;
                } while ((element = element.offsetParent));
              }
              offsetX += $(document).scrollLeft();
              offsetY += $(document).scrollTop();
              mouse_pos_x = e.pageX - offsetX + $(".gx_image_container").scrollLeft();
              mouse_pos_y = e.pageY - offsetY + $(".gx_image_container").scrollTop();
            }
            
            function ajax_request(para){
              switch(para.action){                
                case "create_area":
                  $.ajax({
                    type: "POST",
                    url: "request_port.php?module=SliderAdmin",
                    data: {"action": para.action, "slider_image_id": settings.slider_image_id},
                    dataType: "JSON",
                    success: function(data){
                      actual_area_object.slider_image_area_id = data.slider_image_area_id;
                      $.each(arr_areas, function(key, value){
                        if(value.slider_image_id == 0){
                          value.slider_image_id = data.slider_image_area_id;
                        }
                      });
                      ajax_request({
                        action: "save_area",
                        area: actual_area_object
                      });
                    }
                  });
                  break;
                case "save_area":
                  switch(para.area.shape){
                    case "rectangle":
                      var shape = "rect";
                      var coords = para.area.pos_x+","+para.area.pos_y+","+parseInt(para.area.pos_x+para.area.width)+","+parseInt(para.area.pos_y+para.area.height);
                      break;
                    case "circle":
                      var shape = "circle";
                      if(para.area.width > para.area.height){
                        var coords = parseInt(para.area.pos_x+para.area.width/2)+","+parseInt(para.area.pos_y+para.area.height/2)+","+parseInt(para.area.height/2);
                      }else{
                        var coords = parseInt(para.area.pos_x+para.area.width/2)+","+parseInt(para.area.pos_y+para.area.height/2)+","+parseInt(para.area.width/2);
                      }
                      break;
                    case "polygon":
                      var shape = "poly";
                      var coords
                      $.each(para.area.polygon_coords, function(key, value){
                        if(key == 0){
                          coords = value.x;
                          coords = coords+","+value.y;
                        }else{
                          coords = coords+","+value.x+","+value.y;
                        }
                      });
                      break;
                    default:
                      break;
                  }
				  para.area.flyover_content = para.area.flyover_content.replace(/\n+/g, ""); 
                  $.ajax({
                    type: "POST",
                    url: "request_port.php?module=SliderAdmin",
                    data: {"action": para.action, "slider_image_id": settings.slider_image_id, "slider_image_area_id": para.area.slider_image_area_id, "shape": shape, "coords": coords, "title": para.area.title, "link_url": para.area.link_url, "link_target": para.area.link_target, "flyover_content": para.area.flyover_content},
                    success: function(){
                      $(".gx_control .fields input").val("");
                      $("#field_type").val("");
                      unbind_poly();
                      actual_area_object = null;
                      tmp_actual_area_object = null;
                      invalidate();
                    }
                  });
                  break;
                case "delete_area":
                  $.ajax({
                    type: "POST",
                    url: "request_port.php?module=SliderAdmin",
                    data: {"action" : para.action, "slider_image_area_id": para.area.slider_image_area_id},
                    success: function(){
                      var delete_key = -1;
                      $.each(arr_areas, function(key, value){
                        if(para.area.slider_image_area_id == value.slider_image_area_id){
                          delete_key = key;
                        }
                      });
                      
                      arr_areas.splice(delete_key, 1);
                      
                      update_polygon_handles();
                      unbind_poly();
                      actual_area_object = null;
                      tmp_actual_area_object = null;
                      invalidate();
                    }
                  });
                  break;
                case "get_image_area_data":
                  $.ajax({
                    type: "POST",
					dataType: "JSON",
                    url: "request_port.php?module=SliderAdmin",
                    data: {"action": para.action, "slider_image_id": settings.slider_image_id},
                    success: function(data){
                      arr_areas = [];
                      $.each(data, function(key, value){                       
                        
                        var area = new Rectangle_box;
                        area.slider_image_id = parseInt(value.slider_image_id);
                        area.slider_image_area_id = parseInt(value.slider_image_area_id);
                        area.title = value.title;
                        area.link_url = value.link_url;
                        area.link_target = value.link_target;
                        area.flyover_content = value.flyover_content;
                        area.coords = value.coords;

                        var coord_elements = value.coords.split(",");

                        switch(value.shape){
                          case "rect":
                            area.width = parseInt(coord_elements[2])-parseInt(coord_elements[0]);
                            area.height = parseInt(coord_elements[3])-parseInt(coord_elements[1]);
                            area.pos_x = parseInt(coord_elements[0]);
                            area.pos_y = parseInt(coord_elements[1]);
                            area.shape = "rectangle";
                            break;
                          case "circle":
                            area.width = parseInt(coord_elements[2])*2;
                            area.height = parseInt(coord_elements[2])*2;
                            area.pos_x = parseInt(coord_elements[0])-parseInt(coord_elements[2]);
                            area.pos_y = parseInt(coord_elements[1])-parseInt(coord_elements[2]);
                            area.shape = "circle";
                            break;
                          case "poly":
                            area.shape = "polygon";
                            var polygon_coords = [];
                            $.each(coord_elements, function(key, value){
                              if((key+1)%2 == 0){
                                var coord = {
                                  x: parseInt(coord_elements[key-1]),
                                  y: parseInt(value)
                                };
                                polygon_coords.push(coord);
                              }
                            });
                            area.polygon_coords = polygon_coords;

                            polygon_handles[key]= [];
                             $.each(area.polygon_coords, function(key2, value2){
                              polygon_handles[key][key2] = [];
                              polygon_handles[key][key2][0] = value2.x;
                              polygon_handles[key][key2][1] = value2.y;
                            });
                            break;
                          default:
                            break;
                        }
                        arr_areas.push(area);
                      });
                      

                      for (var i = 0; i < 8; i ++) {
                        var rect = new Rectangle_box();
                        resize_handles.push(rect);
                      }
                      invalidate();
                    }
                  });
                  break;
                default:
                  break;
              }
            }
            
            function bind_poly(){
              $("#image_canvas").bind("click", function(e){
                getMouse(e);
                if(poly_coords.length == 0){
                  canvas_context.beginPath();
                  canvas_context.moveTo(mouse_pos_x, mouse_pos_y);
                  poly_coords.push({x: mouse_pos_x, y: mouse_pos_y});
                  $(".gx_image_container").append($("<div style='width: 6px; height: 6px; position: absolute; display: block;'></div>"));
                  $(".gx_image_container div").css("top", mouse_pos_y-4).css("left", mouse_pos_x-4);
                  
                  $(".gx_image_container div").bind("click", function(){
                    canvas_context.lineTo(poly_coords[0].x, poly_coords[0].y);
                    canvas_context.stroke();
                    canvas_context.closePath();
                    $(this).remove();
                    
                    var tmp_poly_coords = "";
                    $.each(poly_coords, function(key, value){                      
                      tmp_poly_coords += ""+value.x+","+value.y+"";
                      if(key != poly_coords.length-1){
                        tmp_poly_coords += ",";
                      }
                    });
                    var area = new Rectangle_box;
                    area.slider_image_id = settings.slider_image_id;
                    area.slider_image_area_id = 0;
                    area.shape = "polygon";
                    area.polygon_coords = poly_coords;
                    area.pos_x = 1;
                    area.pos_y = 1;
                    area.width = 1;
                    area.height = 1;
                    area.flyover_content = "";
                    arr_areas.push(area);
                    actual_area_object = area;
                    invalidate();
                    update_polygon_handles();
                    unbind_poly();
                    $(".gx_control .float_right.save").css("display", "block");
                    $(".gx_control .float_left.flyover").css("display", "block");
                  });
                }else{
                  canvas_context.lineTo(mouse_pos_x, mouse_pos_y);
                  poly_coords.push({x: mouse_pos_x, y: mouse_pos_y});
                }
                canvas_context.stroke();
              });

            }
            
            function unbind_poly(){
              $("#image_canvas").unbind("click");    
              $(".gx_image_container div").unbind("click");
              poly_coords = [];
              $(".gx_image_container div").remove();
            }
            
            function update_control_container(area){
              $("#field_type").val(area.shape);
              $("#field_id").val(area.slider_image_area_id);
              $("#field_href").val(area.link_url);
              $("#field_title").val(area.title);
              $("#field_target").val(area.link_target);   
              
              if($(".gx_control .division").css("display") == "block"){
                $(".gx_control .division").css("display", "none");
                $(".gx_control .editor").css("display", "none");
                $(".gx_control .fields").css("display", "block");
              }
              
              $(".gx_control .float_left").css("display", "block");
              $(".gx_control .float_right").css("display", "none");
              if($("#field_id").val() != 0){
                $(".gx_control .delete").css("display", "block");
              }
              $(".gx_control .save").css("display", "block");
              $(".gx_control .cancle").css("display", "block");
              $(".gx_control input").removeAttr("disabled");
              $(".gx_control #field_target").removeAttr("disabled");
            }
            
            function update_polygon_handles(){
              
              $.each(arr_areas, function(key, value){
                if(value.shape == "polygon"){
                  polygon_handles[key]= [];                
                  $.each(value.polygon_coords, function(key2, value2){   
                    polygon_handles[key][key2] = [];
                    polygon_handles[key][key2][0] = value2.x;
                    polygon_handles[key][key2][1] = value2.y;
                  }); 
                }
              });
            }
            return false;
          });        
    };  
})(jQuery);

$(document).ready(function()
{
  $(".gx_image_mapper_open").bind("click", function(){
    $(this).image_mapper();
    return false;
  });
});