/* properties_combis_edit.js <?php
#   --------------------------------------------------------------
#   properties_combis_edit.js 2014-07-11 tb@gambio
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
	products_properties_combis_id = t_lightbox_parameters["_" + t_lightbox_identifier]["products_properties_combis_id"];
	
	$("#combi_price_type").unbind("change");
	$("#combi_price_type").bind("change", function()
	{
		if($(this).val() == "calc")
		{
			$("#combi_price").attr("disabled", "disabled");
		}
		else
		{
			$("#combi_price").removeAttr("disabled");
		}
	});
	
	if(products_properties_combis_id == 0)
	{
		$(".lightbox_content_button_container.navigation").css("visibility", "hidden");
		$(".lightbox_content_button_container .save").hide();
	}
	else
	{
		if($("#box_properties_combis_row_" + products_properties_combis_id).next().attr("id") == '')
		{
			$(".next").css("visibility", "hidden");
		}

		if($("#box_properties_combis_row_" + products_properties_combis_id).prev().attr("id") == '')
		{
			$(".previous").css("visibility", "hidden");
		}

		$(".previous").unbind("click");
		$(".previous").bind("click", function()
		{
			var prev_properties_id = $("#box_properties_combis_row_" + products_properties_combis_id).prev().attr("id").split("_")[4];

			var url_param = {"products_id": products_id,
				"products_properties_combis_id": prev_properties_id,
				"lightbox_identifier": t_lightbox_identifier};
			$.ajax({
				type: "GET",
				dataType:	"json",
				url: "request_port.php?module=LightboxPluginAdmin",
				data: {"action": "get_template", "template": "properties_combis_edit.html", "section": "admin", "param": url_param},
				success: function(template)
				{
					$( ".lightbox_content_container" ).html( template.html );			
				},
				error: function(){
					alert("Connection Error - Unable to connect to server");
				}
			});
			return false;
		});

		$(".next").unbind("click");
		$(".next").bind("click", function()
		{
			var next_properties_id = $("#box_properties_combis_row_" + products_properties_combis_id).next().attr("id").split("_")[4];

			var url_param = {"products_id": products_id,
				"products_properties_combis_id": next_properties_id,
				"lightbox_identifier": t_lightbox_identifier};
			$.ajax({
				type: "GET",
				dataType:	"json",
				url: "request_port.php?module=LightboxPluginAdmin",
				data: {"action": "get_template", "template": "properties_combis_edit.html", "section": "admin", "param": url_param},
				success: function(template)
				{
					$( ".lightbox_content_container" ).html( template.html );
				},
				error: function(){
					alert("Connection Error - Unable to connect to server");
				}
			});
			return false;
		});
	}
	
	$(".save").unbind("click");
	$(".save").bind("click", function()
	{			
		if($(this).hasClass("active") == true)
		{
			return false;
		}
		$(this).addClass("active");
	
		save_combis(false);

		return false;
	});	
	
	$(".save_close").unbind("click");
	$(".save_close").bind("click", function()
	{	
		if($(this).hasClass("active") == true)
		{
			return false;
		}
		$(this).addClass("active");
		
		save_combis(true);

		return false;
	});
	
});

function save_combis(p_close_lightbox)
{	
	var inputs = [];
	$(".lightbox_properties_combis_edit_content input[type=text]").each(function() 
	{
		inputs.push(this.name + "=" + encodeURIComponent(this.value));
	});
	$(".lightbox_properties_combis_edit_content input[type=checkbox]:checked").each(function() 
	{
		inputs.push(this.name + "=" + this.value);
	});
	$(".lightbox_properties_combis_edit_content select").each(function() 
	{
		inputs.push(this.name + "=" + this.value);
	});
	inputs.push("products_id=" + products_id);
	inputs.push("products_properties_combis_id=" + products_properties_combis_id);

	$.ajax(
	{
		url: "request_port.php?module=PropertiesCombisAdmin&action=save&type=combis",
		type: "POST",
		data: inputs.join("&"),
		dataType: "json",
		timeout: 30000,
		error: function( p_jqXHR, p_exception )
		{
			$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );       
		},
		success: function(p_response)
		{
			if($.isEmptyObject(p_response))
			{
				$('.lightbox_content_error').html(js_options.error_handling.lightbox_plugin.fatal_error);
			}
			else
			{
				if(p_response.combis_exists && p_response.action == 'abort'){
					$('.lightbox_content_error').html(p_response.message).show();
					$(".save").removeClass("active");
					$(".save_close").removeClass("active");
				}else{
					if(fb)console.log(p_response.action + ': success');
					
					upload_image(p_response.combis_id, p_close_lightbox);
				}
			}							
		}
	});		
}

function upload_image(p_products_properties_combis_id, p_close_lightbox)
{
	$.ajaxFileUpload(
	{
		url: "request_port.php?module=properties_combis_image_upload&combis_id="+p_products_properties_combis_id,
		secureuri: false,
		fileElementId: "combi_image",
		dataType: "text",
		error: function( p_jqXHR, p_exception )
		{
			$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );      
		},
		success: function(p_response)
		{
			$.ajax({
				url: 		"request_port.php?module=PropertiesCombisAdmin&action=load&template=combis_table&combis_id=" + p_products_properties_combis_id + "&products_id=" + products_id,
				timeout: 	2000,
				dataType:	"html",
				error: function( p_jqXHR, p_exception )
				{
					$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );      
				},
				success: function(p_response)
				{
					
					var products_properties_combis_id = p_products_properties_combis_id;
					var t_table_container = $("#box_properties_combis_row_" + products_properties_combis_id);
					t_table_container.remove();
					var t_added = false;	

					$.each($(".box_properties_combis_row"), function(){	
						if(parseInt($(".properties_table_sortnr" ,this).html()) > parseInt($("#properties_sort_order").val()) && t_added == false){
							$(this).before('<tr id="box_properties_combis_row_' + products_properties_combis_id + '"></tr>');
							t_added = true;
						}
					});		
					if(t_added == false){
						$(".properties_view2 table").append('<tr id="box_properties_combis_row_' + products_properties_combis_id + '"></tr>');
					}
					$(".save").removeClass("active");
					$("#box_properties_combis_row_" + products_properties_combis_id).replaceWith(p_response);
				}
			});
			if(p_close_lightbox)
			{
				$.lightbox_plugin('close', t_lightbox_identifier);
			}
		}
	});
}