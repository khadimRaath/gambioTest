/* properties_combis_delete.js <?php
#   --------------------------------------------------------------
#   properties_combis_delete.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$(document).ready(function()
{
	products_properties_combis_id = t_lightbox_parameters["_" + t_lightbox_identifier]["products_properties_combis_id"];
	
	$(".delete", t_lightbox_package ).unbind("click");
	$(".delete", t_lightbox_package ).bind("click", function()
	{
		var properties_combis_id_array = new Array();
		properties_combis_id_array.push(products_properties_combis_id);
		
		$.ajax(
		{
			url: "request_port.php?module=PropertiesCombisAdmin&action=delete&type=combis",
			type: "POST",
			data: {"properties_combis_id_array": properties_combis_id_array},
			dataType: "json",
			timeout: 2000,
			error: function( p_jqXHR, p_exception )
			{
				$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );      
			},
			success: function(p_response)
			{
				if($.isEmptyObject(p_response))
				{
					$.lightbox_plugin( "error", t_lightbox_identifier, "empty_object" );
				}
				else
				{
					if(fb)console.log(p_response.action + ': success');
					$("#box_properties_combis_row_" + products_properties_combis_id).remove();
					$.lightbox_plugin('close', t_lightbox_identifier);
				}							
			}
		});	
		return false;
	});
});