/* properties_main.js <?php
#   --------------------------------------------------------------
#   properties_main.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$(document).ready(function()
{
    $(".button_display_container").die("click");
    $(".button_display_container").live("click", function()
    {
        var self = this;	
        if($(self).closest(".properties_table_container").hasClass("active"))
        {
            $(self).closest(".properties_table_container").removeClass("active");
        }
        else
        {
            $(".properties_table_container").removeClass("active");
            $(self).closest(".properties_table_container").addClass("active");
        }
        return false;
    });
});