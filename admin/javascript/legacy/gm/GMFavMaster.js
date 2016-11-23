/* GMFavMaster.js <?php
#   --------------------------------------------------------------
#   GMFavMaster.js 2015-08-24
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2015 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/

function GMFavMaster()
{
	var current_box_id = '';

	$('#gm_box_favorites').ready(function()
	{
		if(fb)console.log('gm_box_favorites ready');


    $(".fav_drag_item").live("mouseover", function() {
      if (!$(this).data("init")) {
        $(this).data("init", true);
        $(this).draggable({
          helper: 'clone',
          start: function(ev, ui)
          {

            $(this).parent('li').prepend('<div id="gm_fav_dropzone" class="gm_fav_add"><i class="fa fa-heart"></i></div>');
            $("#gm_fav_dropzone").css({
                'opacity' : '0.9',
                'z-index' : '1000'
            });

            $("#gm_fav_dropzone").css({
                                    'left': '190px',
                                    'top' : '-30px'
                                  });

            $("#gm_fav_dropzone").droppable({
              accept: ".fav_drag_item",
              tolerance: 'pointer',
              over: 			function(ev, ui) {$("#gm_fav_dropzone").css('opacity', 		'1.0');	},
              out: 				function(ev, ui) {$("#gm_fav_dropzone").css('opacity', 		'0.9');	},
              drop: function(ev, ui)
              {
                var link_key = $(ui.draggable).attr('id');
                gmFavMaster.save_fav(link_key);
                if(fb)console.log('received:' + link_key);
              }
            });
          },
          stop: function(ev, ui)
          {
            $("#gm_fav_dropzone").remove();
          }
        });
      }
    });

    $(".fav_content_item").live("mouseover", function() {
      if (!$(this).data("init")) {
        $(this).data("init", true);
        $(this).draggable({
          helper: 'clone',
          start: function(ev, ui)
          {
            $(this).parent('li').prepend('<div id="gm_fav_dropzone" class="gm_fav_remove"><i class="fa fa-trash"></i></div>');
            $("#gm_fav_dropzone").css({
                'opacity' : '0.9',
                'z-index' : '1000'
            });

            $("#gm_fav_dropzone").css({
              'left': '190px',
              'top' : '-30px'
            });

            $("#gm_fav_dropzone").droppable({
              accept: ".fav_content_item",
              tolerance: 'pointer',
              over: 			function(ev, ui) {$("#gm_fav_dropzone").css('opacity', 		'1.0');	},
              out: 				function(ev, ui) {$("#gm_fav_dropzone").css('opacity', 		'0.9');	},
              drop: function(ev, ui)
              {
                var link_key = $(ui.draggable).attr('id');
                gmFavMaster.delete_fav(link_key);
                if(fb)console.log('received:' + link_key);
              }
            });
          },
          stop: function(ev, ui)
          {
            $("#gm_fav_dropzone").remove();
          }
        });
      }
    });

	});

	this.save_fav = function(link_key)
	{
		$.ajax({
			url: 'request_port.php?module=AdminMenu&action=save_fav&link_key=' + link_key,
			success: function(res)
			{
				list_link = $("<a></a>").addClass("fav_content_item ui-draggable").attr("id", $("#" + link_key).attr("id")).attr("href", $("#" + link_key).attr("href")).html($("#" + link_key).html());
				list_element = $("<li></li>").addClass("leftmenu_body_item").append(list_link);
				if ($("#BOX_HEADING_FAVORITES").find("#" + link_key).length == 0)
				{
					$("#BOX_HEADING_FAVORITES").append(list_element);
				}
			}
		});
	};

	this.delete_fav = function(link_key)
	{
		$.ajax({
			url: 'request_port.php?module=AdminMenu&action=delete_fav&link_key=' + link_key,
			success: function(res)
			{
				$("#" + link_key).parent().remove();
			}
		});
	};

}
