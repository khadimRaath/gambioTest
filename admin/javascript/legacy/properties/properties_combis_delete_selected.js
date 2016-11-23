/* properties_combis_delete_selected.js <?php
#   --------------------------------------------------------------
#   properties_combis_delete_selected.js 2014-11-09 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
$(document).ready(function()
{
	products_id = t_lightbox_parameters["_" + t_lightbox_identifier]["products_id"];
	properties_combis_id_array = t_lightbox_parameters["_" + t_lightbox_identifier]["properties_combis_id_array"];
	
	$(".delete", t_lightbox_package).unbind("click");
	$(".delete", t_lightbox_package).bind("click", function()
	{
        if($(this).hasClass("active"))
        {
            return false;
        }
        $(this).addClass("active");
        
		var input_string = properties_combis_id_array;
		var inputs = input_string.split(",");
		
		var t_type = "selected";
		if($("#delete_all_products_combis:checked").length > 0)
		{
			t_type = "all";
		}
		
		$.ajax(
		{
			url: "request_port.php?module=PropertiesCombisAdmin&action=delete&type=" + t_type,
			type: "POST",
			data: {"properties_combis_id_array": inputs, "products_id": products_id},
			dataType: "json",
			error: function( p_jqXHR, p_exception )
			{
				$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
                location.reload(true);
			},
			success: function(p_response)
			{
				if($.isEmptyObject(p_response))
				{
					$.lightbox_plugin( "error", t_lightbox_identifier, "empty_object" );
				}
				else
				{					
					if(t_type == 'selected')
					{
						$.each(inputs, function(input_key, input_value){
							$('#box_properties_combis_row_'+input_value).remove();
						});
						$(".button.delete_selected").hide();
						$.lightbox_plugin('close', t_lightbox_identifier);
					}
					else
					{
						location.reload(true);
					}
				}							
			}
		});	
		
		return false;
	});
});