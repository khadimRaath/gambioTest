/* combis_defaults.js <?php
#   --------------------------------------------------------------
#   combis_defaults.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$(document).ready(function()
{
	var t_products_id = t_lightbox_parameters["_" + t_lightbox_identifier]["products_id"];
	
    $("#combi_price_type", t_lightbox_package ).unbind("change");
    $("#combi_price_type", t_lightbox_package).bind("change", function(){
        if($(this).val() == "calc")
        {
            $("#combi_price", t_lightbox_package).attr("disabled", "disabled");
        }
        else
        {
            $("#combi_price", t_lightbox_package).removeAttr("disabled");
        }
    });
	
	$(".save", t_lightbox_package).bind('click', save_defaults);
	
	function save_defaults()
	{
		if( $( this ).hasClass( "active" ) ) return false;
		$( this ).addClass( "active" );
		
		var t_select_values = new Array();
        $('.lightbox_combis_defaults_content select', t_lightbox_package).each(function() 
        {
            t_select_values.push(this.name + '=' + this.value);
        });
        $('.lightbox_combis_defaults_content input', t_lightbox_package).each(function() 
        {
            t_select_values.push(this.name + '=' + this.value);
        });
        t_select_values.push("products_id=" + t_products_id);
        
        $.ajax(
        {
            data: 		t_select_values.join('&'),
            url: 		"request_port.php?module=PropertiesCombisAdmin&action=save&type=combis_defaults",
            type: 		"POST",
            timeout: 	2000,
            dataType:	"json",
			context:	this,
            error: 		function( p_jqXHR, p_exception )
            { 
                $.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception ); 
            },
            success: 	function( p_response )
            { 
                if($.isEmptyObject(p_response))
                {
                    $.lightbox_plugin( "error", t_lightbox_identifier, "empty_object" );
                }
                else
                {
                    $.lightbox_plugin( "close", t_lightbox_identifier );
                }
            }
        });
        return false;
	}
});