/*
  checkmatchserver.js 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
*/

if(jQuery) {
    jQuery(document).ready(function() {
        var callbackurl = "@callbackurl@";
        var triggerelem = jQuery("[data-rsmartseparole='checkmatchserverbutton']");
        triggerelem.click(function(evt) {
            evt.stopPropagation();
            evt.preventDefault();
            //alert("CallbackURL=" + callbackurl); 
            
            var callParams = {
                action: "CHECKMATCHSERVER"
            };
            
            triggerelem.css({cursor: "wait"});
            jQuery.ajax({
                type: "POST",
                url: callbackurl,
                processData: false,
                async: true,
                dataType: 'json',
                data: "json=" + JSON.stringify(callParams),
                success: function(data, textStatus, jqXHR) {
                    triggerelem.css({cursor: "pointer"});
                    if(data.status == "ok") {
                        alert(data.result);
                    }
                    else {
                        alert("Error");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    triggerelem.css({cursor: "pointer"});
                    alert("Ajax Error");
                }
            });
            
        });
    });
}


