<?php
/* --------------------------------------------------------------
   admin_info_box.js.php 2016-07-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   -------------------------------------------------------------- 
*/
	
// @todo The file will be removed in GX v3.5
	
$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('admin_info_boxes', $_SESSION['languages_id']));
?>

var session_id = '<?php if(isset($_GET["XTCsid"]) && !empty($_GET["XTCsid"]) && preg_replace("/[^a-zA-Z0-9,-]/", "", $_GET["XTCsid"]) === $_GET["XTCsid"]) echo $_GET["XTCsid"]; ?>';

(function($)
{
	$.fn.admin_info_box = function()
	{      
		var self = this;  
		var timeout_close;
		var settings = {
			'auto_close': false
		};
		
		$(document).click(function(e) {
			if($("#admin_info_wrapper:visible").length == 1){
				if(e.target != $(self) && $(e.target).parents("#admin_info_wrapper").length == 0 && $(e.target).hasClass("admin_info_box_button") == false){
					$(self).removeClass("active");
				}
			}
		});

		function info_box_open( )
		{			
			update_position();
			check_visible();

			$(self).show().addClass('active');

			$('.admin_info_box:visible').each(function()
			                                  {
				                                  if($(this).hasClass('read') == false && $(this).hasClass('hidden') == false)
				                                  {
					                                  $.ajax({	data:		'module=AdminInfobox&action=set_status_read&id=' + $(this).find('.admin_info_box_id').text() + '&XTCsid=' + session_id,
						                                  url: 		'request_port.php',
						                                  type: 		"GET",
						                                  async:		true,
						                                  success:	function(t_response)
								                                               {
									                                               //
								                                               }
					                                  }).html;
				                                  }
			                                  });

			if(settings.auto_close == true)
			{
				timeout_close = setTimeout(function()
				                           {
					                           info_box_close();
				                           }, 5000);
			}

			return false;
		}

		function info_box_close( )
		{
			$(self).removeClass('active');

			return false;
		}
		
		function hide_all()
		{			
			$(self).removeClass('active');
		}
		
		function update_position(){		
			$(self).css("top", $(self).height() * -1);
		}
		
		function check_visible(){
			var show_all = false;
			if($(".admin_info_box.hidden").length > 0){
				$(".show_all").css("display", "block");
				show_all = true;
			}else{
				$(".show_all").css("display", "none");
			}
			if($(".admin_info_box").not(".hidden").length == 0){
				$(self).find(".no_messages").show();
			}
		}

		$('.admin_info_box').each(function()
		{
			if($(this).not(".hidden").length > 0)
			{
				$(".admin_info_box_button").addClass("active");
			}	
			if($(this).hasClass('read') == false && $(this).hasClass('hidden') == false)
			{
				settings.auto_close = true;				
				info_box_open();
				return false;
			}
		});

		// set infobox item count
		$('.notification-count').text( $('.admin_info_box').length ); 
		
		if ($('.admin_info_box').length > 0) {
			$('.notification-count').removeClass('hidden');
		}

		$(".admin_info_box_button").die("click");
		$(".admin_info_box_button").live("click", function()
		{
			clearTimeout(timeout_close);
			if($(self).hasClass('active'))
			{
				settings.auto_close = false;
				info_box_close();
			}
			else
			{
				settings.auto_close = false;
				info_box_open();
			}
			return false;
		});

		$(self).die('mousemove');		
		$(self).live('mousemove', function(){
			clearTimeout(timeout_close);
		});

		$(self).die('mouseenter');		
		$(self).live('mouseenter', function(){
			clearTimeout(timeout_close);
		});

		$(self).die('mouseleave');
		$(self).live('mouseleave', function(){
			if(settings.auto_close == true)
			{
				timeout_close = setTimeout(function()
				{
					info_box_close();
				}, 1000);
			}					
		});
		
		$(window).unbind("scroll");
		$(window).bind("scroll", function(){
			update_position();	
		});

		$(".show_all_info_boxes").die('mouseleave');
		$(".show_all_info_boxes").live('click', function(){
			if($(".show_all_info_boxes").prop("checked"))
			{
				$(".no_messages").hide();
				$(".admin_info_box.hidden").show();
			}
			else
			{
				if($(".admin_info_box").not(".hidden").length == 0){
					$(".no_messages").show();
				}
				
				$(".admin_info_box.hidden").hide();
			}				
		});

		$(self).find("a").live("click", function(){
			if($(this).hasClass("ajax"))
			{
				settings.auto_close = false;
				var info_box = $(this).closest(".admin_info_box");
				var url = $(this).attr('href');
				var rel = $(this).attr('rel');

				if(rel != "hide_info_box")
				{
					$(info_box).addClass("progress");
				}

				$.ajax(
				{
					type:       "GET",
					url:        url,
					timeout:    3000,
					success:    function(response)
								{
									switch(rel)
									{
										case 'clear_cache':
											$(info_box).removeClass("info").removeClass("warning").removeClass("progress");
											
											if(response.length < 500)
											{
												$(info_box).find(".info_text").html(response);
												$(info_box).find("a[rel='clear_cache']").hide();
												$(info_box).addClass("success");

												setTimeout(function(){
													if($(".admin_info_box").not(".hidden").length == 0)
													{
														$(".admin_info_box_button").removeClass("active");
													}
													
													hide_all();

													setTimeout(function(){
														$(info_box).addClass("hidden");
														$(info_box).remove();
													}, 500);
												}, 2000);	

												$('.notification-count').text( parseInt($('.notification-count').text()) - 1 );
												
												if ($('.notification-count').text() == '0') {
													$('.notification-count').addClass('hidden');
												}

											}
											else
											{
												$(info_box).find(".info_text").html('<?php echo $coo_text_mgr->get_text('ERROR_SESSION_EXPIRED'); ?>');
												$(info_box).addClass("error");											
											}
											
											break;
										case 'hide_info_box':
											$(".show_all_info_boxes").prop("checked", false);
											$(info_box).slideUp(500, function(){
												$(this).addClass('hidden');
												check_visible();
												if($(".admin_info_box").not(".hidden").length == 0)
												{
													$(".admin_info_box_button").removeClass("active");
												}
											});
											if($(".admin_info_box:visible").length == 1)
											{
												info_box_close();												
											}

											break;
										case 'remove_info_box':
											$(info_box).slideUp(500, function(){
												$(this).addClass('hidden');
												if($(".admin_info_box").not(".hidden").length == 0)
												{
													$(".admin_info_box_button").removeClass("active");
												}
												$(info_box).remove();
											});
											if($(".admin_info_box:visible").length == 1)
											{
												info_box_close();												
											}
											
											break;
									}																					
								},
					error:      function(response)
								{
									$(info_box).removeClass("info").removeClass("warning").removeClass("progress").addClass("error");
									$(info_box).find(".info_text").html(response);
								}
				});
				return false;
			}
			else if($(this).hasClass("target_blank"))
			{
				var myWindow;
				myWindow = window.open($(this).attr('href'));
				myWindow.focus();
				return false;
			}
			return true;
		});
		
		return false;      
	};
})(jQuery);

$(document).ready(function(){
	
	// load info boxes
	$.ajax({	data: 'module=LoadAdminInfoBoxes&XTCsid=' + session_id,
					url: 		'request_port.php',
					type: 		"GET",
					async:		true,
					timeout:	10000,
					success:	function(t_admin_info_boxes)
					{
						$('body').append(t_admin_info_boxes);
						
						var self = $('#admin_info_wrapper');
						if($(self).length == 1)
						{
							$(self).hide();
							$(self).admin_info_box();
						}	
					}
	}).html;	
	
});