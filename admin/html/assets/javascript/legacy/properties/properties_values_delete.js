/* properties_values_delete.js <?php
#   --------------------------------------------------------------
#   properties_values_delete.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$(document).ready(function()
{
    var t_properties_values_id = t_lightbox_parameters["_" + t_lightbox_identifier]["properties_values_id"];
	
    $('.delete', t_lightbox_package).bind('click', delete_propertie );
	
    function delete_propertie()
	{
        $.ajax(
        {
            url: 		'request_port.php?module=PropertiesAdmin&action=delete&type=properties_values&properties_values_id=' + t_properties_values_id,
            type: 		'GET',
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
					$( "tr.properties_values_id_" + p_response.properties_values_id ).remove();
					$.lightbox_plugin( "close", t_lightbox_identifier );
				}
            }
        });
        return false;
    }
});