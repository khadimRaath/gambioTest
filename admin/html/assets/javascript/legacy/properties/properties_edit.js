/* properties_edit.js <?php
#   --------------------------------------------------------------
#   properties_edit.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$(document).ready(function()
{
	var t_properties_id = t_lightbox_parameters["_" + t_lightbox_identifier]["properties_id"];
	
    set_sort_order();
	
    $( ".save", t_lightbox_package ).bind( "click" , save_propertie);
    $( ".save_close", t_lightbox_package ).bind( "click" , save_propertie);
	
	function set_sort_order()
	{
		if( $( "#properties_sort_order", t_lightbox_package ).val() == "" )
		{
			if( $( ".properties_table_container" ).length > 0 )
			{
				$( "#properties_sort_order", t_lightbox_package ).val( parseInt( $( ".properties_table_container:last .properties_sort_order_value" ).html() ) + 1 );
			}
			else
			{
				$( "#properties_sort_order", t_lightbox_package ).val( 1 );
			}
		}
	}
	
	function save_propertie()
	{
        
        var numberOfInputsWithContent_array = $("input[name^='properties_name']", t_lightbox_package).filter(function(index)
        {
            return $(this).val() !== '';
        }).length;

        if(!numberOfInputsWithContent_array)
        {
            $.lightbox_plugin('error', t_lightbox_identifier, 'properties_name_empty');
            return false;
        }
                
		if( $( this ).hasClass( "active" ) ) return false;
        $( this ).addClass( "active" );
        var inputs = [];
        $( ".lightbox_properties_edit_content input", t_lightbox_package ).each( function() 
        {
            inputs.push( this.name + "=" + encodeURIComponent(this.value) );
        });
        inputs.push( "properties_id=" + t_properties_id );
		
        $.ajax(
        {
            data: 		inputs.join( "&" ),
            url: 		"request_port.php?module=PropertiesAdmin&action=save&type=properties",
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
                if( $.isEmptyObject( p_response ) )
                {
                    $.lightbox_plugin( "error", t_lightbox_identifier, "empty_object" );
                }
                else
                {
                    var t_table_container = $( "#properties_table_container_" + p_response.properties_id );
                    t_table_container.remove();
                    var t_added = false;	

                    $.each( $( ".properties_table_container" ), function()
                    {	
                        if( parseInt( $( ".properties_sort_order_value", this ).html() ) > parseInt( $( "#properties_sort_order", t_lightbox_package ).val() ) )
                        {
                            $( this ).before( "<div id='properties_table_container_" + p_response.properties_id + "'></div>" );
                            t_added = true;
                        }
                    });
                    if( t_added == false )
                    {
                        $( "#properties_area_content" ).append( "<div id='properties_table_container_" + p_response.properties_id + "'></div>" );
                    }
                    $( ".properties_table_container" ).removeClass( "active" );
                    $( "#properties_table_container_" + p_response.properties_id ).addClass( "properties_table_container active" ).html( p_response.html );
					
					$( this ).removeClass( "active" );
					
					if( $( this ).hasClass( "save_close" ) )
					{
						$.lightbox_plugin( "close", t_lightbox_identifier );
					}
					else
					{
						if( t_properties_id == 0 )
						{
							$( ".lightbox_content_error", t_lightbox_package ).empty();
							$( ".lightbox_properties_edit_content input", t_lightbox_package ).val("");
							set_sort_order();
							$( ".lightbox_properties_edit_content input:eq(0)" ).focus();
						}
					}
                }
            }
        });
        return false;
	}
});
