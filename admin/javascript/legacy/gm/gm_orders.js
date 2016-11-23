/* 
	--------------------------------------------------------------
	gm_order.js 2015-09-21 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
   
    IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
    MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
    NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
*/
	
	function gm_mail_close(box) {
		$("#GM_" + box + "_BOX").dialog('close');	
        
	}

	function gm_mail_send(file, param, box) {
		
		var gm_subject	= $("#gm_subject").val();
		var gm_mail		= $("#gm_mail").val();
		$("#GM_" + box + "_BOX").html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');
		
		jQuery.ajax({data: 		"gm_mail=" + gm_mail + "&gm_subject=" + gm_subject,
					url: 		file + '?oID=' + oID + param + "&XTCsid=" + session_id,
					type: 		"POST",
					async:		true,
					success:	function(t_html){
							$("#GM_" + box + "_BOX").html(t_html)
                        
                            if (t_html == '') // Hide modal if there is nothing to display.
                            {
                                $("#GM_" + box + "_BOX").fadeOut();    
                            }
						}
					}).html;
	}

	function gm_cancel(file, param, box) {
		
		var gm_subject	= $("#gm_subject").val();
		var gm_mail		= $("#gm_mail").val();
		var gm_comments = $("#gm_comment").val();
		var gm_cancel_id = $("#gm_cancel_id").val();
		if( $("#gm_notify_comments").prop('checked') == true){
			var gm_notify_comments	= "on";
		}

		if( $("#gm_notify").prop('checked') == true){
			var gm_notify	= "on";
		}
		
		if( $("#gm_restock").prop('checked') == true){
			var gm_restock	= "on";
		}
        
        if( $("#gm_reactivateArticle").prop('checked') == true){
			var gm_reactivateArticle	= "on";
		}
		
		// BOF GM_MOD products_shippingtime:
		if( $("#gm_reshipp").prop('checked') == true){
			var gm_reshipp	= "on";
		}
		// BOF GM_MOD products_shippingtime:

		$("#GM_" + box + "_BOX").html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');
        var data = {'gm_comments':gm_comments, 'gm_mail': gm_mail,'gm_subject': gm_subject};
        $("#GM_" + box + "_BOX").load(file + '?oID=' + oID + "&gm_notify=" + escape(gm_notify) + "&gm_notify_comments=" + escape(gm_notify_comments) + "&gm_reactivateArticle=" + escape(gm_reactivateArticle) + "&gm_restock=" + escape(gm_restock) + "&gm_reshipp=" + escape(gm_reshipp) + param + "&XTCsid=" + session_id,data);
    }

	function gm_get_position(event, box) {

		position				= new Array(2);	
		var left				= 0;
		var top					= 0;
		var element_width		= $('#GM_' + box + '_BOX').outerWidth();
		var element_height		= $('#GM_' + box + '_BOX').outerHeight();

		var browser_width		= $(document).width();

		var browser_scroll_top	= $(document).scrollTop();
		var browser_scroll_left	= $(document).scrollLeft();

		
		var browser_height =  $(window).height();
			
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

	$(document).ready(function() {
        
		$("#gm_check").click(function() {
			if($("#gm_check").prop("checked") == true) {
				$('input.checkbox').parent().addClass('checked');
				$("input.checkbox").prop("checked", true);
			} else {
				$('input.checkbox').parent().removeClass('checked');
				$("input.checkbox").prop('checked', false);
			}
		});

		$(".GM_INVOICE_MAIL").click(function(event) {
			oID = $('#gm_order_id').val();
            
			$("#GM_INVOICE_MAIL_BOX").load('gm_order_menu.php?' + 'oID=' + oID + '&XTCsid=' + session_id, function() {
                $(this).dialog({
                    width: 'auto',
                    height: 'auto',
                    modal: true,
                    dialogClass: 'gx-container',
	                close: function() { $('.ui-dialog-title').empty() }
                });

				$('.ui-dialog-title').append($('#GM_INVOICE_MAIL_BOX').find('strong').text());				
				$('#GM_INVOICE_MAIL_BOX').find('strong').parent().css('display', 'none');
            });
        });
		
		$(".GM_SEND_ORDER").click(function(event) {
			oID = $('#gm_order_id').val();
            
			$("#GM_ORDERS_MAIL_BOX").load('gm_order_menu.php?' + 'oID=' + oID + '&type=order&XTCsid=' + session_id, function() {
                $(this).dialog({
                    width: 'auto', 
                    height: 'auto',
                    modal: true,  
                    dialogClass: 'gx-container',
	                close: function() { $('.ui-dialog-title').empty() }
                });
				
				$('.ui-dialog-title').append($('#GM_ORDERS_MAIL_BOX').find('strong').text());
				$('#GM_ORDERS_MAIL_BOX').find('strong').parent().css('display', 'none');
            });			
		});

		$(".GM_CANCEL").click(function(event) {
			oID = $('#gm_order_id').val();

			$("#GM_CANCEL_BOX").load('gm_order_menu.php?' + 'oID=' + oID + '&type=cancel&XTCsid=' + session_id, function() {
                $(this).dialog({
                    width: 'auto',
                    height: 'auto',
                    modal: true,
                    dialogClass: 'gx-container',
	                close: function() { $('.ui-dialog-title').empty() }
                });
				
				$('.ui-dialog-title').append($('#GM_CANCEL_BOX').find('strong').text());
				$('#GM_CANCEL_BOX').find('strong').parent().css('display', 'none');
            });			
		});
	});