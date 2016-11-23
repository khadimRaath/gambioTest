/* properties_values_edit.js <?php
#   --------------------------------------------------------------
#   properties_values_edit.js 2014-01-03 tb@gambio
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
    var t_properties_values_id = t_lightbox_parameters["_" + t_lightbox_identifier]["properties_values_id"];
	
	set_sort_order();
	
	$( ".save", t_lightbox_package ).bind( "click" , save_propertie_value);
    $( ".save_close", t_lightbox_package ).bind( "click" , save_propertie_value);
	
	function set_sort_order()
	{
		if( $( "#properties_values_sort_order", t_lightbox_package ).val() == "" )    
		{
			if( $( "#properties_table_container_" + t_properties_id ).find( "tr" ).length > 1 )
			{
				$( "#properties_values_sort_order", t_lightbox_package ).val( parseInt( $( "#properties_table_container_" + t_properties_id ).find( "tr:last .properties_table_sortnr" ).html() ) + 1 );
			}
			else
			{
				$( "#properties_values_sort_order", t_lightbox_package ).val( 1 );
			}
		}
	}
	
	function save_propertie_value()
	{

        var numberOfInputsWithContent_array = $( "input[name^='values_name']", t_lightbox_package ).filter(function(index)
        {
            return $(this).val() !== '';
        }).length;
               
		if( !numberOfInputsWithContent_array )
		{
			$.lightbox_plugin( 'error', t_lightbox_identifier, 'properties_values_name_empty' );
			return false;
		}
        
		if( $( this ).hasClass( "active" ) ) return false; 
        $( this ).addClass( "active" );
		
        var inputs = [];
        $('.lightbox_properties_values_edit_content input', t_lightbox_package).each(function() 
        {
	        inputs.push(this.name + '=' + encodeURIComponent(this.value));
        });
        $('.lightbox_properties_values_edit_content select', t_lightbox_package).each(function() 
        {
	        inputs.push(this.name + '=' + encodeURIComponent(this.value));
        });
        inputs.push('properties_id=' + t_properties_id);
        inputs.push('properties_values_id=' + t_properties_values_id);

        $.ajax(
        {
            data: 		inputs.join('&'),
            url: 		'request_port.php?module=PropertiesAdmin&action=save&type=properties_values',
            type: 		'POST',
            timeout: 	2000,
            dataType:	'json',
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
					$('#properties_table_container_' + p_response.properties_id ).html( p_response.html );

					$( this ).removeClass( "active" );

					if( $( this ).hasClass( "save_close" ) )
					{
						$.lightbox_plugin( "close", t_lightbox_identifier );
					}
					else
					{
						if( t_properties_values_id == 0 )
						{
							$( ".lightbox_content_error", t_lightbox_package ).empty();
							$( ".lightbox_properties_values_edit_content input", t_lightbox_package ).val( "" );
							$( ".lightbox_properties_values_edit_content input[name='value_price']", t_lightbox_package ).val( "0.00" );
							set_sort_order();
							$( ".lightbox_properties_values_edit_content input:eq(0)" ).focus();
						}
					}
				}
            }
        });
        return false;
	}
});
