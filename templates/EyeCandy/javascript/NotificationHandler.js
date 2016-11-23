/* NotificationHandler.js <?php
 #   --------------------------------------------------------------
 #   TopbarHandler.js 2014-10-08 gambio
 #   Gambio GmbH
 #   http://www.gambio.de
 #   Copyright (c) 2014 Gambio GmbH
 #   Released under the GNU General Public License (Version 2)
 #   [http://www.gnu.org/licenses/gpl-2.0.html]
 #   --------------------------------------------------------------
 ?>*/
/*<?php
 if(false && $GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
 {
 ?>*/
function hideTopbar(event){if(fb)console.log('.hide_topbar_notification clicked');event.stopPropagation();$.ajax({type:"POST",url:"request_port.php?module=Notification&action=hide_topbar&XTCsid="+gm_session_id,timeout:5000,dataType:"json",context:this,data:{},success:function(p_response){$(".topbar_notification").remove();if(fb)console.log('.topbar_notification removed')}});return false}$('.topbar_notification').on("click",'.hide_topbar_notification',hideTopbar);function hidePopupNotification(event){if(fb)console.log('.close_popup_notification clicked');event.stopPropagation();$.ajax({type:"POST",url:"request_port.php?module=Notification&action=hide_popup_notification&XTCsid="+gm_session_id,timeout:5000,dataType:"json",context:this,data:{},success:function(p_response){$(".popup_notification").remove();if(fb)console.log('.close_popup_notification removed')}});return false}$('.popup_notification').on("click",'.hide_popup_notification',hidePopupNotification);
/*<?php
 }
 else
 {
 ?>*/
function hideTopbar(event)
{
    if(fb)console.log('.hide_topbar_notification clicked');
    
    event.stopPropagation();

    $.ajax({
        type:       "POST",
        url:        "request_port.php?module=Notification&action=hide_topbar&XTCsid=" + gm_session_id,
        timeout:    5000,
        dataType:	"json",
        context:	this,
        data:		{},
        success:    function( p_response )
        {
            $(".topbar_notification").remove();
            if(fb)console.log('.topbar_notification removed');
        }
    });

    return false;
}

$('.topbar_notification').on("click", '.hide_topbar_notification', hideTopbar);


function hidePopupNotification(event)
{
    if(fb)console.log('.close_popup_notification clicked');

    event.stopPropagation();

    $.ajax({
        type:       "POST",
        url:        "request_port.php?module=Notification&action=hide_popup_notification&XTCsid=" + gm_session_id,
        timeout:    5000,
        dataType:	"json",
        context:	this,
        data:		{},
        success:    function( p_response )
        {
            $(".popup_notification").remove();
            if(fb)console.log('.close_popup_notification removed');
        }
    });

    return false;
}

$('.popup_notification').on("click", '.hide_popup_notification', hidePopupNotification);
/*<?php
 }
 ?>*/