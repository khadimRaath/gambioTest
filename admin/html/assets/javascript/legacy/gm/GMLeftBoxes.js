/* GMLeftBoxes.js <?php
#   --------------------------------------------------------------
#   GMLeftBoxes.js 2016-07-14
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2016 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/

function GMLeftBoxes()
{
	var current_box_id = '';
	
	$(document).ready(function()
	{
		if(fb)console.log('GMLeftBoxes document ready');
		
		
		$('.leftmenu_collapse').click(function(e) 
		{
			if(fb)console.log('leftmenu_collapse click');
			
			var box_key = $(this).next('.leftmenu_box').attr('id');
			
			if($(this).hasClass('leftmenu_collapse_closed')) {
				gmLeftBoxes.open_leftbox(box_key);
				gmLeftBoxes.save_box_status(box_key, 1);
			}	else {
				gmLeftBoxes.close_leftbox(box_key);
				gmLeftBoxes.save_box_status(box_key, 0);
			}
		});
		
	});

	// Notice: Since admin facelift this request is not required. 
	this.init = function() 
	{
		$.ajax({
		  url: 			'request_port.php?module=AdminMenu&action=get_closed_boxes',
		  dataType: 'json',
		  cache: 		false,
		  success: 	function(cresult)
		  					{
		  						if(fb)console.log('keys:' + cresult.box_keys.length);
		  						var curkey		= '';
		    					for(var i=0; i<cresult.box_keys.length; i++) {
		    						curkey = cresult.box_keys[i];
		    						if(fb)console.log('box_key closed:' + curkey);
		    						gmLeftBoxes.close_leftbox(curkey);
		    					}
		  					}
		});
	}
	
	
	this.open_leftbox = function(box_key) {
		var collapse_container = $('#'+box_key).prev('.leftmenu_collapse');
		
		$(collapse_container).removeClass('leftmenu_collapse_closed');
		$(collapse_container).addClass('leftmenu_collapse_opened');
		
		$('#'+box_key).show();
	}
	
	this.close_leftbox = function(box_key) {
		var collapse_container = $('#'+box_key).prev('.leftmenu_collapse');

		$(collapse_container).removeClass('leftmenu_collapse_opened');
		$(collapse_container).addClass('leftmenu_collapse_closed');
		
		$('#'+box_key).hide();
	}
	
	
	this.save_box_status = function(box_key, box_status) {
		$.ajax({url: 'request_port.php?module=AdminMenu&action=save_box_status&box_key='+box_key+'&box_status='+box_status});
	}
	
}