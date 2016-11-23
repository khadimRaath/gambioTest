/* filter_set_edit.js <?php
#   --------------------------------------------------------------
#   filter_set_edit.js 2014-10-17 tb@gambio
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

$(document).ready(function()
{	
	$('.gambio_scrollbar').jScrollPane();
});

$(".delete_feature_box", t_lightbox_package).die("click");
$(".delete_feature_box", t_lightbox_package).live("click", function()
{
	var t_feature_id = $( this ).attr( "rel" );
	var feature_box = $('#feature_id_' + t_feature_id, t_lightbox_package);
	var headline = $(feature_box, t_lightbox_package).find(".feature_box_headline span").html();
	$(feature_box, t_lightbox_package).remove();
	var option = '<option value="' + t_feature_id + '">' + headline + '</option>';
	var select = $('#feature_set_add_feature_select select', t_lightbox_package);
	$(select).append(option);

	$(select).html($("option", $(select)).sort(function(a, b)
	{ 
		a = a.text.toLowerCase();
		b = b.text.toLowerCase();
		return a == b ? 0 : a < b ? -1 : 1 
	}));

	$(select).val(0);
	$("#feature_set_add_feature_select", t_lightbox_package ).css( "visibility", "visible" );
	return false;
});

$("select[name='add_feature']", t_lightbox_package).die("change");
$("select[name='add_feature']", t_lightbox_package).live("change", function()
{
	var t_feature_id = $( this ).val();
	if (t_feature_id == 0) return false;

	$.ajax(
	{
		data: 
		{
			feature_id: t_feature_id
		},
		url: 'request_port.php?module=FeatureSetAdmin&action=get_feature_box',
		type: 'POST',
		timeout: 10000,
		dataType: 'html',
		error: function()
		{
			$('.lightbox_error').html("Error: Feature box could not be added");
		},
		success: function(p_response)
		{
			if($.isEmptyObject(p_response))
			{
				$('.lightbox_error').html("Error: Feature box could not be added");
			} 
			else 
			{
				if($('.feature_box', t_lightbox_package ).length > 0)
				{
					$('.feature_box', t_lightbox_package ).last().after(p_response);
				}
				else
				{
					$('.feature_set_box', t_lightbox_package ).prepend(p_response);	
				}
					
				var t_feature_name = p_response.substring(p_response.indexOf('feature_id_') + 'feature_id_'.length);
				t_feature_name = t_feature_name.substring(0, t_feature_name.indexOf("\""));
				$("#feature_set_add_feature_select option[value='" + t_feature_name + "']", t_lightbox_package ).remove();
				
				if( $("#feature_set_add_feature_select option", t_lightbox_package ).length == 1 )
				{
					$("#feature_set_add_feature_select", t_lightbox_package ).css( "visibility", "hidden" );
				}
                $('.gambio_scrollbar').jScrollPane();
			}
			return false;
		}
	});
	return false;
});

$(".editable_values .feature_values a", t_lightbox_package).die("click");
$(".editable_values .feature_values a", t_lightbox_package).live("click", function()
{
	$(this).toggleClass("active");
	return false;
});

$(".save", t_lightbox_package).die("click");
$(".save", t_lightbox_package).live("click", function()
{
	var target = this;
	
	var feature_values = new Array();
	$.each($(".feature_values a.active"), function()
	{
		feature_values.push($(this).attr("rel"));
	});	

	if( feature_values.length == 0 )
	{
		$.lightbox_plugin( "error", t_lightbox_identifier, "feature_set_no_values_selected" );
		return false;
	}
	else
	{
		$( ".lightbox_content_error", t_lightbox_package ).html( "" );
	}

	$.ajax(
	{
		data: 		
		{ 
			categories_path: categories_path, 
			feature_set_id: feature_set_id, 
			products_id: products_id, 
			feature_value: feature_values.join('&')
		},
		url: 		'request_port.php?module=FeatureSetAdmin&action=save',
		type: 		'POST',
		timeout: 	5000,
		dataType:	'json',
		error: 		function(p_jqXHR, p_exception)
		{
			$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
		},
		success: 	function(p_response)
		{ 	
			if(p_response.status == "error")
			{
				$.lightbox_plugin( "error", t_lightbox_identifier, "feature_set_cant_save" );
				return;
			}
			else if(p_response.status == "success")			
			{
				if(feature_set_id != 0)
				{
					$("#feature_set_id_" + feature_set_id).replaceWith(p_response.html);
					$("#feature_set_id_" + feature_set_id).next().remove();
				}
				else
				{
					$("#feature_set_box_container").prepend(p_response.html);
				}
			}
			
			$('.lightbox_content_error').html('');
			if(p_response.message != '')
			{
				$('.lightbox_content_error').html(p_response.message);
				$('.lightbox_content_error').show();
			}
			
			if($(target).hasClass('close'))
			{
				$.lightbox_plugin('close', t_lightbox_identifier);
			}
			else
			{
				if(feature_set_id == 0 && p_response.status == 'success')
				{
					$(".feature_values a", t_lightbox_package).removeClass("active");
				}				
			}
			$.routeFeatureSets();
		}
	});

	return false;
});	