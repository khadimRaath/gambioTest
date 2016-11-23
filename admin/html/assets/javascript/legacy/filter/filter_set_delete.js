/* filter_set_delete.js <?php
#   --------------------------------------------------------------
#   filter_set_delete.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$(".feature_values a", t_lightbox_package).die("mousedown");
$(".feature_values a", t_lightbox_package).live("mousedown", function()
{	
	return false;
});

var products_id = t_lightbox_parameters["_" + t_lightbox_identifier]["products_id"];
var categories_path = t_lightbox_parameters["_" + t_lightbox_identifier]["categories_path"];
var feature_set_id = t_lightbox_parameters["_" + t_lightbox_identifier]["feature_set_id"];

$("a.delete", t_lightbox_package).die("click");
$("a.delete", t_lightbox_package).live("click", function(){
	$.ajax(
	{
		data: 
		{
			feature_set_id: feature_set_id,
			products_id: products_id
		},
		url: 'request_port.php?module=FeatureSetAdmin&action=delete',
		type: 'POST',
		timeout: 2000,
		dataType: 'json',
		error: function()
		{
			$('.lightbox_error').html("Error: Set could not be deleted");
		},
		success: function(p_response)
		{
			if($.isEmptyObject(p_response))
			{
				$('.lightbox_error').html("Error: Set could not be deleted");
			} 
			else 
			{
				if(fb) console.log(p_response.action + ': ' + p_response.status);
				$('#feature_set_id_' + feature_set_id).next().remove();
				$('#feature_set_id_' + feature_set_id).remove();
			}
			$.lightbox_plugin('close', t_lightbox_identifier);
			$.routeFeatureSets();
		}
	});
	return false;
});

$(document).ready(function()
{	
	$('.gambio_scrollbar').jScrollPane();
});