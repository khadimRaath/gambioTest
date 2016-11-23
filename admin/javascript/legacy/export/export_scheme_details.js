/* export_scheme_details.js <?php
#   --------------------------------------------------------------
#   export_scheme_details.js 2014-08-08 dwue@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

var t_field_change_timeout = false;
var t_preview_request = false;

function load_details_content( p_event, p_confirmed )
{
	if( $( this ).hasClass( "active" )) return false;
		
	var t_form_changed = $( "form", t_lightbox_package ).form_changes_checker();
	if ($( "#csv_categories", t_lightbox_package ).length == 1 && window.csv_form_change == true )
	{
		t_form_changed.push("csv_categories");
	}
	
	if( t_form_changed.length > 0 && p_confirmed != true )
	{
		var t_date = new Date();
		var t_identifier = t_date.getTime();
		
		$( this ).addClass( "changes_confirm_" + t_identifier );
		show_confirm_lightbox( t_identifier, "click" );
	}
	else
	{
		$( ".export_scheme_details_navigation a", t_lightbox_package ).removeClass("active");
		$( this ).addClass( "active" );
		
		var t_scheme_id = $( "#input_scheme_id", t_lightbox_package ).val();
		var t_template = $( this ).attr("href");

		$.ajax({
			type:		"GET",
			url:		"request_port.php?module=CSV&action=get_template",
			timeout:	30000,
			dataType:	"json",
			context:	this,
			data:		{
							"scheme_id":	t_scheme_id,
							"template":		t_template
						},
			success:	function( p_response )
						{
							$.destroy_tooltip_plugin();
							$( ".export_scheme_details_content", t_lightbox_package ).html( p_response.html );
							$.initialize_tooltip_plugin();
							$( ".save", t_lightbox_package ).removeClass( "active" );
							if( p_response.html_preview )
							{
								$( "table tr", t_lightbox_package ).sortable({
										items: "td:not(.disabled)",
										axis: "x",
										containment: "parent",
										handle: ".field_column_move:not(.active)",
										tolerance: "pointer",
										placeholder: {
											element: function(currentItem) {
												return $("<td style='width: 36px; height: 360px;'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>")[0];
											},
											update: function(container, p) {
												return;
											}
										},
										update: function(event, ui)
												{
													load_preview();
												}
									});
								if( t_field_change_timeout != false )
								{
									clearTimeout(t_field_change_timeout);
									t_field_change_timeout = false;
								}
								$( ".export_scheme_preview", t_lightbox_package ).html( p_response.html_preview );
								$( ".export_scheme_preview > div" ).scrollLeft( 0 );
								$( ".export_scheme_field_container_wrapper" ).scrollLeft( 0 );
								$( "#field_details", t_lightbox_package ).form_changes_checker( 'initialize' );	
								$( "td#properties", t_lightbox_package ).form_changes_checker( 'initialize', false );	
							}
							if(t_template == "export_scheme_collective_fields.html")
							{
								init_parent_checkboxes();
							}
							$.lightbox_plugin( "update_view", t_lightbox_identifier );
							
							$( "form", t_lightbox_package ).form_changes_checker( 'initialize', false );	
							window.csv_form_change = false;
						},
			error:		function( p_jqXHR, p_exception )
						{
							$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
						}
		});
	}
	return false;
}

function check_scheme_data( p_event )
{
	var t_actual_element_id = $(this).attr("id");
	
	var t_new_scheme_id = $( "#input_scheme_id", t_lightbox_package ).val();
	var t_scheme_list = $( ".export_scheme_container", "#exportSchemes" );
	
	var t_scheme_name_exist = false;
	var t_new_scheme_name = $( "#conf_scheme_name", t_lightbox_package ).val();
	
	var t_scheme_filename_exist = false;
	var t_new_scheme_filename = $( "#conf_filename", t_lightbox_package ).val();
	
	$.each( t_scheme_list, function( key, value )
	{
		if( $( value ).attr( "id" ) != "export_scheme_container_" + t_new_scheme_id )
		{
			var t_existing_scheme_name = $( ".scheme_name", value ).html();
			if( $.trim( t_existing_scheme_name.toLowerCase() ) == $.trim( t_new_scheme_name.toLowerCase() ) )
			{
				t_scheme_name_exist = true;
			}
			var t_existing_scheme_filename = $( ".scheme_filename a", value ).html();
			if( $.trim( t_existing_scheme_filename.toLowerCase() ) == $.trim( t_new_scheme_filename.toLowerCase() ) )
			{
				t_scheme_filename_exist = true;
			}
		}
	});	
	
	if( t_actual_element_id != "conf_filename" )
	{
		var t_scheme_name_regex = new RegExp( js_options.validation['csv-scheme_name']['pattern'] );
		if( t_scheme_name_exist == true )
		{
			$( "#conf_scheme_name", t_lightbox_package ).closest( ".row" ).addClass( "error" );
			$( "#conf_scheme_name", t_lightbox_package ).tooltip_plugin( "update", js_options.error_handling.csv.duplicate_scheme_name );
		}
		else if( $.trim( t_new_scheme_name ) == "" )
		{
			$( "#conf_scheme_name", t_lightbox_package ).closest( ".row" ).addClass( "error" );
			$( "#conf_scheme_name", t_lightbox_package ).tooltip_plugin( "update", js_options.error_handling.csv.empty_scheme_name );
		}
		else if( t_new_scheme_name.match( t_scheme_name_regex ) && $( "#conf_scheme_name", t_lightbox_package ).attr( "disabled" ) == false )
		{
			$( "#conf_scheme_name", t_lightbox_package ).closest( ".row" ).addClass( "error" );
			$( "#conf_scheme_name", t_lightbox_package ).tooltip_plugin( "update", js_options.error_handling.csv.profile_name_allowed_chars );
		}
		else if( t_new_scheme_name.toLowerCase().indexOf("[gambio]") >= 0 && $( "#conf_scheme_name", t_lightbox_package ).attr( "disabled" ) == false )
		{
			$( "#conf_scheme_name", t_lightbox_package ).closest( ".row" ).addClass( "error" );
			$( "#conf_scheme_name", t_lightbox_package ).tooltip_plugin( "update", js_options.error_handling.csv.reserved_scheme_name );
		}
		else
		{
			$( "#conf_scheme_name", t_lightbox_package ).closest( ".row" ).removeClass( "error" );
			$( "#conf_scheme_name", t_lightbox_package ).tooltip_plugin( "reset_content" );
		}
	}

	if( t_actual_element_id != "conf_scheme_name" )
	{
		var t_scheme_filename_regex = new RegExp( js_options.validation['csv-scheme_filename']['pattern'] );
		if( t_scheme_filename_exist == true )
		{
			$( "#conf_filename", t_lightbox_package ).closest( ".row" ).addClass( "error" );
			$( "#conf_filename", t_lightbox_package ).tooltip_plugin( "update", js_options.error_handling.duplicate_filename );
		}
		else if( t_new_scheme_filename.match( t_scheme_filename_regex ) )
		{
			$( "#conf_filename", t_lightbox_package ).closest( ".row" ).addClass( "error" );
			$( "#conf_filename", t_lightbox_package ).tooltip_plugin( "update", js_options.error_handling.csv.filename_allowed_chars );
		}
		else if( $.trim( t_new_scheme_filename ) == "" )
		{
			$( "#conf_filename", t_lightbox_package ).closest( ".row" ).addClass( "error" );
			$( "#conf_filename", t_lightbox_package ).tooltip_plugin( "update", js_options.error_handling.empty_filename );
		}
		else 
		{
			$( "#conf_filename", t_lightbox_package ).closest( ".row" ).removeClass( "error" );
			$( "#conf_filename", t_lightbox_package ).tooltip_plugin( "reset_content" );
		}
	}
}

function toggle_cronjob()
{
	if( !$( this ).attr( "checked" ) )
	{
		$( ".conf_export_path", t_lightbox_package ).css( "visibility", "hidden" );
		$( ".conf_cronjob_time", t_lightbox_package ).css( "visibility", "hidden" );
		$( "#conf_cronjob_allowed_input", t_lightbox_package ).val( 0 );
	}
	else
	{
		$( ".conf_export_path", t_lightbox_package ).css( "visibility", "visible" );
		$( ".conf_cronjob_time", t_lightbox_package ).css( "visibility", "visible" );
		$( "#conf_cronjob_allowed_input", t_lightbox_package ).val( 1 );
	}
}

function toggle_attributes()
{
	if( $( this ).attr( "checked" ) )
	{
		$( "#conf_export_attributes_input", t_lightbox_package ).val( 1 );
	}
	else
	{
		$( "#conf_export_attributes_input", t_lightbox_package ).val( 0 );
	}
}

function toggle_properties()
{
	if( $( this ).attr( "checked" ) )
	{
		$( "#conf_export_properties_input", t_lightbox_package ).val( 1 );
	}
	else
	{
		$( "#conf_export_properties_input", t_lightbox_package ).val( 0 );
	}
}

function open_field( p_event, p_confirmed )
{	
	var t_changes = $( "#field_details", t_lightbox_package ).form_changes_checker();	
	var t_changes_properties = $( "td#properties", t_lightbox_package ).form_changes_checker();

	if( ( t_changes.length > 0 || t_changes_properties.length > 0)  && p_confirmed != true )
	{
		var t_date = new Date();
		var t_identifier = t_date.getTime();
		
		$( this ).addClass( "changes_confirm_" + t_identifier );
		show_confirm_lightbox( t_identifier, "click" );
	}
	else
	{
		reset_view();
		reset_properties();
		
		$( ".add_field", t_lightbox_package ).removeClass( "active" );
		$( ".save", t_lightbox_package ).addClass( "active" );
		
		if( t_field_change_timeout != false )
		{
			clearTimeout(t_field_change_timeout);
			t_field_change_timeout = false;
		}

		var t_field_container = $( this, t_lightbox_package ).closest(".field_container");
		
		$( "#field_details", t_lightbox_package ).appendTo( $( ".field_edit_container", t_field_container ) );
		$( ".field_column", t_field_container ).hide();
		
		$( ".export_scheme_field_container_wrapper td", t_lightbox_package ).width( "36px" );
		$( "#new_field", t_lightbox_package ).width( "0px" );
		$( t_field_container ).closest( "td" ).width( "360px" );

		var t_container_position = $( t_field_container ).closest( "td" ).position().left;
		$( ".export_scheme_field_container_wrapper" ).scrollLeft( t_container_position + 360 - 640 );

		$( "#field_details .field_headline" ).text( $( ".field_name", t_field_container ).val() );
		$( "#field_details #edit_field_name" ).val( $( ".field_name", t_field_container ).val() );
		$( "#field_details #edit_field_content" ).val( $( ".field_content", t_field_container ).val() );
		$( "#field_details #edit_field_content_default" ).val( $( ".field_content_default", t_field_container ).val() );

		if( $( ".field_disabled", t_field_container ).val() == 1 )
		{
			$( "#field_details input" ).attr( "disabled", "disabled" );
			$( "#field_details select" ).attr( "disabled", "disabled" );
			$( "#field_details .add_field_variable" ).addClass( "disable" );
			$( "#field_details .save_field" ).addClass( "active" );
		}
		$.initialize_tooltip_plugin();
		$( "#field_details", t_lightbox_package ).form_changes_checker( 'initialize', false );
		
		preview_scroll_to_field();
	}
	
	return false;
}

function create_field( p_event, p_confirmed )
{
	var t_changes = $( "#field_details", t_lightbox_package ).form_changes_checker();
	var t_changes_properties = $( "td#properties", t_lightbox_package ).form_changes_checker();

	if( ( t_changes.length > 0 || t_changes_properties.length > 0 ) && p_confirmed != true )
	{
		var t_date = new Date();
		var t_identifier = t_date.getTime();
		
		$( this ).addClass( "changes_confirm_" + t_identifier );
		show_confirm_lightbox( t_identifier, "click" );
	}
	else
	{
		if( $( this ).hasClass( "active" ) ) return false;
		$( this ).addClass( "active" );
		
		reset_view();
		reset_properties();
		
		$( ".save", t_lightbox_package ).addClass( "active" );
		
		$( "#field_edit_container", t_lightbox_package ).show();
		
		var t_position = ($( ".export_scheme_field_container_wrapper td" ).length - 1) * 36;
		$( ".export_scheme_field_container_wrapper" ).scrollLeft( t_position );
		$.initialize_tooltip_plugin();
		$( "#field_details", t_lightbox_package ).form_changes_checker( 'initialize', false );
	}
	
	return false;
}

function change_field_variable_select()
{
	var t_field_container = $( this ).closest( "#field_details" );
	var t_select_value = $( this ).val();
	var t_variable_description = $( "option[value='" + t_select_value + "']", $( this ) ).attr( "title" );

	$( ".field_variable_description", t_field_container ).text( t_variable_description );
	$( this ).tooltip_plugin( "hide" );
	
	if( t_select_value == 0 )
	{
		$( ".add_field_variable", t_field_container ).addClass( "disable" );
	}
	else
	{
		$( ".add_field_variable", t_field_container ).removeClass( "disable" );		
	}

	return false;
}

function add_variable_to_field_content()
{
	if( $( this ).hasClass( "disable" ) ) return false;
	$( this ).addClass( "disable" );
	
	var t_field_container = $( this ).closest( ".field_container" );
	var t_scheme_id = $( "#input_scheme_id", t_lightbox_package ).val();
	var t_field_id = $( "input.field_id", t_field_container ).val();
	var t_select_value = $( ".field_variable", t_field_container ).val();
	
	if( t_select_value != 0 )
	{
		t_select_value = "{" + t_select_value + "}";
		$( "#edit_field_content", t_lightbox_package ).val( $( "#edit_field_content", t_lightbox_package ).val() + t_select_value );

		$( ".field_variable", t_field_container ).val(0);
		$( ".field_variable_description", t_field_container ).empty();
		
		load_preview();
	}
	
	return false;
}

function change_field_content( p_reload_preview )
{
	var t_field_container = $( "#field_details", t_lightbox_package ).closest( ".field_container" );
	var t_field_id = $( "input.field_id", t_field_container ).val();
	var t_field_name = $( "#edit_field_name", t_field_container ).val();
	var t_duplicate_field_name = false;
	
	$.each( $( ".export_scheme_field_container_wrapper td" ), function( key, value )
	{
		if($( "input.field_id", value ).length == 1 && $( "input.field_name", value ).length == 1)
		{
			var t_existing_field_id = $( "input.field_id", value ).val();
			if( t_existing_field_id != t_field_id && t_existing_field_id != -1 )
			{
				var t_existing_field_name = $( "input.field_name", value ).val();
				if( t_existing_field_name.toLowerCase() == t_field_name.toLowerCase() )
				{
					t_duplicate_field_name = true;
				}	
			}
		}
	});
	
	if( $.trim( t_field_name ) == "" )
	{
		$( "#edit_field_name", t_field_container ).closest( ".row" ).addClass( "error" );		
		$( "#edit_field_name", t_field_container ).tooltip_plugin( "update", js_options.error_handling.csv.empty_field_name );	
		$( ".save_field", t_field_container ).addClass( "active" );
	}
	else if( t_duplicate_field_name == true )
	{
		$( "#edit_field_name", t_field_container ).closest( ".row" ).addClass( "error" );	
		$( "#edit_field_name", t_field_container ).tooltip_plugin( "update", js_options.error_handling.csv.duplicate_field_name );		
		$( ".save_field", t_field_container ).addClass( "active" );
	}
	else
	{
		$( "#edit_field_name", t_field_container ).closest( ".row" ).removeClass( "error" );	
		$( "#edit_field_name", t_field_container ).tooltip_plugin( "reset_content" );		
		$( ".save_field", t_field_container ).removeClass( "active" );
		$( ".field_headline", t_field_container ).text( t_field_name );
		
		if( p_reload_preview != false )
		{
			if( t_field_change_timeout != false )
			{
				clearTimeout(t_field_change_timeout);
			}
			
			t_field_change_timeout = setTimeout($.proxy(function()
			{
				load_preview();
			}, this), 1000);			
		}
		else
		{
			clearTimeout(t_field_change_timeout);
			load_preview();
		}
	}
	return true;
}

function save_field()
{
	change_field_content( false );
	
	if( $(this).hasClass( "active" ) ) return false;			
	$(this).addClass( "active" );

	var t_field_container = $( "#field_details", t_lightbox_package ).closest( ".field_container" );

	var t_scheme_id = $( "#input_scheme_id" ).val();
	var t_field_id = $( "input.field_id", t_field_container ).val();
	
	if( t_field_id == -1 )
	{
		$.ajax({
			type:       "GET",
			url:        "request_port.php?module=CSV&action=get_template",
			timeout:    30000,
			dataType:	"json",
			context:	this,
			data:		{
							"scheme_id":				t_scheme_id,
							"field_id":					0,
							"template":					"export_scheme_fields.html"
						},
			success:    function( p_response )
						{			
							$( "#new_field", t_lightbox_package ).before( p_response.html );
							var t_field_container = $( "#new_field", t_lightbox_package ).prev();
							
							var t_field_name = $( "#edit_field_name", t_lightbox_package ).val();
							var t_field_content = $( "#edit_field_content", t_lightbox_package ).val();
							var t_field_content_default = $( "#edit_field_content_default", t_lightbox_package ).val();
							$( ".field_column_headline", t_field_container ).text( t_field_name );
							$( "input.field_id", t_field_container ).val( "0" );
							$( "input.field_name", t_field_container ).val( t_field_name );
							$( "input.field_content", t_field_container ).val( t_field_content );
							$( "input.field_content_default", t_field_container ).val( t_field_content_default );
							
							var t_position = ($( ".export_scheme_field_container_wrapper td", t_lightbox_package ).length - 1) * 36;
							$( ".export_scheme_field_container_wrapper" ,t_lightbox_package ).scrollLeft( t_position );
							
							reset_view();
							
							$( "#field_details", t_lightbox_package ).form_changes_checker( 'initialize', false );
						},
			error:      function( p_jqXHR, p_exception )
						{	
							$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
						}
		});
	}
	else
	{
		var t_field_name = $( "#edit_field_name", t_lightbox_package ).val();
		var t_field_content = $( "#edit_field_content", t_lightbox_package ).val();
		var t_field_content_default = $( "#edit_field_content_default", t_lightbox_package ).val();
		$( ".field_column_headline", t_field_container ).text( t_field_name );
		$( ".field_name", t_field_container ).val( t_field_name );
		$( ".field_content", t_field_container ).val( t_field_content );
		$( ".field_content_default", t_field_container ).val( t_field_content_default );
		
		reset_view();
		
		$( "#field_details", t_lightbox_package ).form_changes_checker( 'initialize', false );
	}

	return false;	
}

function save_properties_field()
{
	//change_field_content( false );
	
	if( $(this).hasClass( "active" ) ) return false;			
	$(this).addClass( "active" );

	var t_field_container = $( "td#properties .field_container" );
	
	var t_properties_data = "";
	var t_properties_data_array = new Array();
	
	$.each( $( "#properties .properties_data_select option:selected", t_lightbox_package ), function( key, value )
	{
		t_properties_data_array.push( $( value ).val() );
	});
	t_properties_data = t_properties_data_array.join( "," );
	$( ".field_properties_data", t_field_container ).val( t_properties_data );
	$( ".properties_language", t_field_container ).val( $( ".select_properties_language", t_lightbox_package ).val() );

	reset_view();
	
	$( "td#properties", t_lightbox_package ).form_changes_checker( 'initialize', false );
			
	$(this).removeClass( "active" );
	return false;	
}

function save_collective_fields(p_target)
{
	if( $( p_target ).hasClass( "active" ) ) return false;	
	$( p_target ).addClass( "active" );
	$('input[type="checkbox"]:not(:checked)').each(function()
	{
		$(this).after('<input type="hidden" value="0" name="' + $(this).attr('name') + '"/>');
	});
	
	$.ajax({
		type:		"POST",
		url:		"request_port.php?module=CSV&action=save_collective_fields",
		timeout:	30000,
		dataType:	"json",
		context:	p_target,
		data:		{
						"scheme_id":	$("#input_scheme_id").val(),
						"field_data":	$( "#collective_fields_form *", t_lightbox_package ).serialize()
					},
		success:	function( p_response )
					{
						$( p_target ).removeClass( "active" );

						$( "#export_scheme_collective_fields" ).replaceWith( p_response.html );	
						init_parent_checkboxes();
						$( "form", t_lightbox_package ).form_changes_checker( 'initialize' );
					},
		error:      function( p_jqXHR, p_exception )
					{	
						$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
					}
	});
	return false;	
}

function reset_view()
{
	var t_field_container = $( "#field_details", t_lightbox_package );
	$( ".field_headline", t_field_container ).text( "" );
	$( "#edit_field_name", t_field_container ).val( "" ).removeAttr( "disabled" );
	$( "#edit_field_content", t_field_container ).val( "" ).removeAttr( "disabled" );
	$( "#edit_field_content_default", t_field_container ).val( "" ).removeAttr( "disabled" );
	$( ".row.error", t_field_container ).removeClass( "error" );
	$( ".add_field", t_lightbox_package ).removeClass( "active" );
	$( ".field_variable", t_field_container ).val( 0 ).removeAttr( "disabled" );
	$( ".add_field_variable", t_field_container ).addClass( "disable" );
	$( ".field_variable_description", t_field_container ).empty();
	$( ".save_field", t_field_container ).removeClass( "active" );
	$( ".save_field_properties", t_field_container ).removeClass( "active" );
	$( ".save", t_lightbox_package ).removeClass( "active" );

	$( ".export_scheme_field_container_wrapper td", t_lightbox_package ).width( "36px" );
	$( "#new_field", t_lightbox_package ).width( "0px" );
	$( ".field_column", t_lightbox_package ).show();

	$( ".export_scheme_preview th").removeClass( "active" );
	$( ".export_scheme_preview td").removeClass( "active" );						
	$( ".export_scheme_preview > div" ).scrollLeft( 0 );

	$( "#field_edit_container", t_lightbox_package ).hide();
	$( "#field_details", t_lightbox_package ).appendTo( $( "#field_edit_container", t_lightbox_package ) );

	$( "#properties .field_column", t_lightbox_package ).show();
	$( "#properties .field_edit_container", t_lightbox_package ).hide();
}

function reset_properties()
{
	if( $( "#properties", t_lightbox_package ).length == 1 )
	{
		var t_properties_data_string = $( ".field_properties_data", t_lightbox_package ).val();
		var t_properties_data_array = t_properties_data_string.split( "," );
		$( "#properties option:selected", t_lightbox_package ).removeAttr( "selected" );
		$.each( $( "#properties .properties_data_select option", t_lightbox_package ), function( key, value )
		{
			if( $.inArray( $(value).val(), t_properties_data_array ) != -1 )
			{
				$(value).attr( "selected", "selected" );
			}
		});
		
		$( "#properties .select_properties_language" ).val( $( "input.properties_language", t_lightbox_package ).val() );
	}
}

function delete_field()
{
	var t_field_index = $( this ).closest( "td" ).index();
	
	var t_href = $( this ).attr( "href" );
	t_href = t_href + "&field_index=" + t_field_index;
	$( this ).attr( "href", t_href );
	
	if( $( this ).hasClass( "active" ) ) return false;
	$( this ).lightbox_plugin( 
	{
		'background_color': '#EFEFEF',
		'lightbox_width': '360px',
		'shadow_close_onclick': false
	});
	return false;
}

function remove_field( p_event, p_index )
{
	$( ".export_scheme_field_container_wrapper td", t_lightbox_package ).eq( p_index ).remove();
	load_preview();
}

function close_field()
{	
	reset_view();
	reset_properties();
	
	$( "#field_details", t_lightbox_package ).form_changes_checker( 'initialize', false );

	$( ".export_scheme_preview th", t_lightbox_package ).removeClass( "active" );
	$( ".export_scheme_preview td", t_lightbox_package ).removeClass( "active" );
	$( ".add_field", t_lightbox_package ).removeClass( "active" );
	
	if( t_field_change_timeout != false )
	{
		clearTimeout(t_field_change_timeout);
		t_field_change_timeout = false;
	}
	load_preview();
	
	return false;
}

function close_properties_field()
{	
	reset_view();
	reset_properties();
	
	$( "#field_details", t_lightbox_package ).form_changes_checker( 'initialize', false );
	$( "td#properties", t_lightbox_package ).form_changes_checker( 'initialize', false );

	$( ".export_scheme_preview th", t_lightbox_package ).removeClass( "active" );
	$( ".export_scheme_preview td", t_lightbox_package ).removeClass( "active" );
	$( ".add_field", t_lightbox_package ).removeClass( "active" );
		
	if( t_field_change_timeout != false )
	{
		clearTimeout(t_field_change_timeout);
		t_field_change_timeout = false;
	}
	load_preview();
	
	return false;
}

function preview_scroll_to_field()
{
	$( "th", ".export_scheme_preview" ).removeClass( "active" );
	$( "td", ".export_scheme_preview" ).removeClass( "active" );
	
	var t_container = $( ".field_edit_container:not(:empty):not(:hidden)", t_lightbox_package );
	
	if( t_container.length > 0 )
	{
		var t_column_index = $( t_container ).closest( "td" ).index();
		
		if( $( t_container ).closest( "#properties" ).length == 1 )
		{
			t_column_index--;
		}
		if( t_column_index < $( "th", ".export_scheme_preview" ).length  )
		{
			$( "th", ".export_scheme_preview" ).eq(t_column_index).addClass( "active" );
			$.each( $( "tr", ".export_scheme_preview" ), function( t_tr_key, t_tr_value)
			{
				$( "td", t_tr_value ).eq(t_column_index).addClass( "active" );
			});

			var t_scroll_position = $( ".export_scheme_preview th").eq(t_column_index).position().left - 450 + ($( ".export_scheme_preview th").eq(t_column_index).width() / 2);
			$( ".export_scheme_preview > div" ).scrollLeft( t_scroll_position-1 );
		}
		else
		{
			$( ".export_scheme_preview > div" ).scrollLeft( 0 );
		}
	}
	
	return false;
}

function load_preview()
{
	$( ".export_scheme_preview", t_lightbox_package ).html( $( "<img></img>" ).attr( "src", "html/assets/images/legacy/ajax-loader.gif" ) );

	if( t_preview_request != false )
	{
		t_preview_request.abort();
	}
	
	t_preview_request = $.ajax({
		type:       "POST",
		url:        "request_port.php?module=CSV&action=get_template&template=export_scheme_preview.html&scheme_id=" + $(" #input_scheme_id", t_lightbox_package).val(),
		timeout:    30000,
		dataType:	"json",
		context:	this,
		data:		{
						"edit_field_index":		$( "#field_details", t_lightbox_package ).closest( "td" ).index(),
						"field_data":			$( "form", t_lightbox_package ).serialize()
					},
		success:    function( p_response )
					{			
						t_preview_request = false;
						$( ".export_scheme_preview", t_lightbox_package ).html( p_response.html );
						
						var t_scroll_position = $( ".export_scheme_field_container_wrapper").scrollLeft();		
						$.lightbox_plugin( "update_view", t_lightbox_identifier );
						$( ".export_scheme_field_container_wrapper", t_lightbox_package).scrollLeft( t_scroll_position );
												
						preview_scroll_to_field();
					},
		error:      function( p_jqXHR, p_exception )
					{	
						if(p_exception != 'abort')
						{
							load_preview();
						}
					}
	});
	return false;
}

function save()
{
	if( $( ".export_scheme_configuration", t_lightbox_package ).length == 1 )
	{
		if( $(this).hasClass( "active" ) ) return false;
		
		check_scheme_data();
		
		$( "form", t_lightbox_package ).validation_plugin();
		
		if( $( ".export_scheme_configuration .row.error", t_lightbox_package ).length > 0 ) return false;

		save_configuration( this );
	}
	else if( $( ".export_scheme_fields", t_lightbox_package ).length == 1 )
	{
		if( $(this).hasClass( "active" ) ) return false;	

		var t_error_contents = $( "form", t_lightbox_package ).validation_plugin();

		if( t_error_contents.length > 0 )
		{
			$(this).addClass( "active" );
			return false;
		}
		save_fields( this );
	}
	else if( $( "#export_scheme_collective_fields", t_lightbox_package ).length == 1 )
	{
		if( $(this).hasClass( "active" ) ) return false;	

		save_collective_fields( this );
	}
	
	return false;
}

function save_configuration( p_target )
{
	if( $( p_target ).hasClass( "active" ) ) return false;	
	$( p_target ).addClass( "active" );	
	
	$.ajax({
		type:		"POST",
		url:		"request_port.php?module=CSV&action=save_scheme",
		timeout:	30000,
		dataType:	"json",
		context:	p_target,
		data:		{
						"configuration"	:	$( "form", t_lightbox_package ).serialize()
					},
		success:	function( p_response )
					{
						$( "#input_scheme_id", t_lightbox_package ).val( p_response.scheme_id );
						$( p_target ).removeClass( "active" );
						$( ".export_scheme_details_navigation a", t_lightbox_package ).show();

						$( "#tab_container_" + p_response.export_type ).html( p_response.html );	
						
						$( "form", t_lightbox_package ).form_changes_checker( 'initialize' );
						
						$( ".export_scheme_details_navigation span" ).text( $( "#conf_scheme_name" ).val() );
					},
		error:      function( p_jqXHR, p_exception )
					{	
						$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
					}
	});
	return false;
}

function save_fields( p_target )
{
	$( p_target ).addClass( "active" );
	$.ajax({
		type:		"POST",
		url:		"request_port.php?module=CSV&action=save_fields",
		timeout:	30000,
		dataType:	"json",
		context:	p_target,
		data:		{
						"field_data"	:	$( "form", t_lightbox_package ).serialize()
					},
		success:	function( p_response )
					{
						$( ".export_scheme_details_content", t_lightbox_package ).html( p_response.html );
						$( "form", t_lightbox_package ).form_changes_checker( 'initialize', false );
						$( this ).removeClass( "active" );
					},
		error:      function( p_jqXHR, p_exception )
					{	
						$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
					}
	});
	return false;
}

function close_details( p_event, p_confirmed )
{
	if( $(this).hasClass( "active" ) ) return false;			
	$(this).addClass( "active" );
	
	// check if changes
	var t_changes = $( "form", t_lightbox_package ).form_changes_checker();
	
	if( window.csv_form_change == true )
	{
		t_changes.push( "csv_categories" );
	}
	
	if( t_changes.length > 0 && p_confirmed != true )
	{
		$(this).removeClass( "active" );
		var t_date = new Date();
		var t_identifier = t_date.getTime();
		
		$( this ).addClass( "changes_confirm_" + t_identifier );
		show_confirm_lightbox( t_identifier, "click" );
	}
	else
	{
		$.lightbox_plugin( 'close', t_lightbox_identifier );
		$(document).trigger( "resume_actualize_cronjob_status" );
		$.initialize_tooltip_plugin();
	}
	
	return false;
}

function toogle_preview_content()
{
	$(this).parent().parent().children('.preview_content_sub').toggle();
	$(this).parent().parent().children('.preview_content_full').toggle();
	
	return false;
}

function show_confirm_lightbox( p_event_selector, event_type, p_form_id )
{
	var t_a_tag = $( "<a href='export/export_scheme_changes_confirm.html?event_selector=" + p_event_selector + "&amp;event_type=" + event_type + "&amp;form_id=" + p_form_id + "&amp;buttons=cancel-discard'></a>" )
	$( t_a_tag ).lightbox_plugin( 
	{
		'background_color': '#EFEFEF',
		'lightbox_width': '360px',
		'shadow_close_onclick': false
	});
	return false;
}

function toggle_include_all_properties()
{
	toggle_checkboxes_by_name('include_properties', $(this).prop('checked'));
}

function toggle_include_all_attributes()
{
	toggle_checkboxes_by_name('include_attributes', $(this).prop('checked'));
}

function toggle_include_all_additional_fields()
{
	toggle_checkboxes_by_name('include_additional_fields', $(this).prop('checked'));
}

function toggle_checkboxes_by_name(p_name, p_check_state)
{
	$('#collective_fields_form input[name^="' + p_name + '"]').each(function()
	{
		$(this).prop('checked', p_check_state);
	});
}

function init_parent_checkboxes()
{
	var t_properties_checked = true;
	var t_attributes_checked = true;
	var t_additional_fields_checked = true;
	
	$('#collective_fields_form input[name^="include_properties"]').each(function()
	{
		t_properties_checked &= $(this).prop('checked');
	});
	
	$('#collective_fields_form input[name^="include_attributes"]').each(function()
	{
		t_attributes_checked &= $(this).prop('checked');
	});
	
	$('#collective_fields_form input[name^="include_additional_fields"]').each(function()
	{
		t_additional_fields_checked &= $(this).prop('checked');
	});
	
	$('#include_all_properties').prop('checked', t_properties_checked);
	$('#include_all_attributes').prop('checked', t_attributes_checked);
	$('#include_all_additional_fields').prop('checked', t_additional_fields_checked);
}

function toggle_properties_parent_checkbox()
{
	toggle_parent_checkbox_by_id('include_properties', 'include_all_properties', $(this).prop('checked'));
}

function toggle_attributes_parent_checkbox()
{
	toggle_parent_checkbox_by_id('include_attributes', 'include_all_attributes', $(this).prop('checked'));
}

function toggle_additional_fields_parent_checkbox()
{
	toggle_parent_checkbox_by_id('include_additional_fields', 'include_all_additional_fields', $(this).prop('checked'));
}

function toggle_parent_checkbox_by_id(p_child_name, p_parent_name, p_check_state)
{
	var t_toggle = true;
	$('#collective_fields_form input[name^="' + p_child_name + '"]').each(function()
	{
		if(p_check_state)
		{
			t_toggle &= $(this).prop('checked') == p_check_state;
		}
		else
		{
			t_toggle |= $(this).prop('checked') == p_check_state;
		}
	});
	
	if(t_toggle)
	{
		$('#' + p_parent_name).prop('checked', p_check_state)
	}
}

function add_collective_field()
{
	var t_form_pattern = $(".form_pattern table tr").clone();
	$("#collective_fields_form table").append(t_form_pattern);
	init_parent_checkboxes();
	
	return false;
}

function delete_collective_field()
{	
	var t_field_id = $(this).closest("tr").find(".id_input").val();
	if(t_field_id != 0)
	{
		if($.trim($("#collective_fields_form #delete_collective_fields").val()) == "")
		{
			$("#collective_fields_form #delete_collective_fields").val(t_field_id);
		}
		else
		{
			$("#collective_fields_form #delete_collective_fields").val($.trim($("#collective_fields_form #delete_collective_fields").val()) + "," + t_field_id);
		}
	}
	$(this).closest("tr").remove();
	init_parent_checkboxes();
	
	return false;
}

$.initialize_tooltip_plugin();

$( t_lightbox_package ).delegate( "input[class^='validate_'][type='text']", "keyup", function()
{
	$( this ).parent().validation_plugin();
});

$( t_lightbox_package ).delegate( "input[class^='validate_'][type='text']", "change", function()
{
	$( this ).parent().validation_plugin();
});

$( t_lightbox_package ).delegate( '#export_scheme_collective_fields #include_all_properties', 'change', toggle_include_all_properties);
$( t_lightbox_package ).delegate( '#export_scheme_collective_fields #include_all_attributes', 'change', toggle_include_all_attributes);
$( t_lightbox_package ).delegate( '#export_scheme_collective_fields #include_all_additional_fields', 'change', toggle_include_all_additional_fields);

$( t_lightbox_package ).delegate( '#export_scheme_collective_fields input[name^="include_properties"]', 'change', toggle_properties_parent_checkbox);
$( t_lightbox_package ).delegate( '#export_scheme_collective_fields input[name^="include_attributes"]', 'change', toggle_attributes_parent_checkbox);
$( t_lightbox_package ).delegate( '#export_scheme_collective_fields input[name^="include_additional_fields"]', "change", toggle_additional_fields_parent_checkbox);

$( t_lightbox_package ).delegate( '#export_scheme_collective_fields .add_collective_field', "click", add_collective_field);
$( t_lightbox_package ).delegate( '#export_scheme_collective_fields .collective_field_delete img', "click", delete_collective_field);

$( t_lightbox_package ).delegate( ".toogle_content_size", "click", toogle_preview_content);

$( t_lightbox_package ).delegate( ".export_scheme_details_navigation a", "click", load_details_content);

$( t_lightbox_package ).delegate( "#conf_scheme_name", "keyup", check_scheme_data );

$( t_lightbox_package ).delegate( "#conf_scheme_name", "change", check_scheme_data );

$( t_lightbox_package ).delegate( "#conf_filename", "keyup", check_scheme_data );

$( t_lightbox_package ).delegate( "#conf_filename", "change", check_scheme_data );

$( t_lightbox_package ).delegate( "#conf_cronjob_allowed_checkbox", "change", toggle_cronjob );

$( t_lightbox_package ).delegate( "#conf_export_attributes_checkbox", "change", toggle_attributes );

$( t_lightbox_package ).delegate( "#conf_export_properties_checkbox", "change", toggle_properties );

$( t_lightbox_package ).delegate( ".field_column_headline", "click", open_field );

$( t_lightbox_package ).delegate( ".field_column_headline_button a", "click", open_field );

$( t_lightbox_package ).delegate( ".field_column_delete a", "click", delete_field );

$( t_lightbox_package ).delegate( ".add_field", "click", create_field );

$( t_lightbox_package ).delegate( ".field_variable", "change keypress keyup click", change_field_variable_select );

$( t_lightbox_package ).delegate( ".add_field_variable", "click", add_variable_to_field_content );
				
$( t_lightbox_package ).delegate( "#edit_field_name", "keyup", change_field_content );

$( t_lightbox_package ).delegate( "#edit_field_name", "change", change_field_content );

$( t_lightbox_package ).delegate( "#edit_field_content", "keyup", change_field_content );

$( t_lightbox_package ).delegate( "#edit_field_content", "change", change_field_content );

$( t_lightbox_package ).delegate( "#edit_field_content_default", "keyup", change_field_content );

$( t_lightbox_package ).delegate( "#edit_field_content_default", "change", change_field_content );

$( t_lightbox_package ).delegate( "input[name='field_content_default']", "keyup", change_field_content );

$( t_lightbox_package ).delegate( "input[name='field_content_default']", "change", change_field_content );

$( t_lightbox_package ).delegate( ".save_field", "click", save_field );

$( t_lightbox_package ).delegate( ".save_field_properties", "click", save_properties_field );

$( ".cancel_field", t_lightbox_package ).live( "click", close_field );

$( ".cancel_field_properties", t_lightbox_package ).live( "click", close_properties_field );

$( t_lightbox_package ).delegate( ".save", "click", save );

$( t_lightbox_package ).delegate( ".close", "click", close_details );

$( document ).unbind( "export_scheme_field_delete" );
$( document ).bind( "export_scheme_field_delete", remove_field );

$( "form", t_lightbox_package ).form_changes_checker( 'initialize' );

// Confirm Events

$( document ).unbind( "export_scheme_changes_confirmed" );
$( document ).bind( "export_scheme_changes_confirmed", function( event, p_event_selector, p_event_type)
{
	$( "." + p_event_selector, t_lightbox_package ).trigger( p_event_type, [true] ).removeClass( p_event_selector );
	
});

$( document ).unbind( "export_scheme_changes_canceled" );
$( document ).bind( "export_scheme_changes_canceled", function( event, p_event_selector)
{
	$( "." + p_event_selector, t_lightbox_package ).removeClass( p_event_selector );
	
});

$( t_lightbox_package ).delegate( ".properties_column_headline", "click", function(p_event, p_confirmed)
{
	var t_changes = $( "#field_details", t_lightbox_package ).form_changes_checker();

	if( t_changes.length > 0 && p_confirmed != true )
	{
		var t_date = new Date();
		var t_identifier = t_date.getTime();
		
		$( this ).addClass( "changes_confirm_" + t_identifier );
		show_confirm_lightbox( t_identifier, "click" );
	}
	else
	{
		reset_view();	
		
		$( ".add_field", t_lightbox_package ).removeClass( "active" );
		$( ".save", t_lightbox_package ).addClass( "active" );
		
		if( t_field_change_timeout != false )
		{
			clearTimeout(t_field_change_timeout);
			t_field_change_timeout = false;
		}
		var t_field_container = $( this, t_lightbox_package ).closest(".field_container");
		
		$( ".export_scheme_field_container_wrapper td", t_lightbox_package ).width( "36px" );
		$( "#new_field", t_lightbox_package ).width( "0px" );
		$( t_field_container ).closest( "td" ).width( "360px" );

		var t_container_position = $( t_field_container ).closest( "td" ).position().left;
		$( ".export_scheme_field_container_wrapper" ).scrollLeft( t_container_position + 360 - 640 );
		
		$( "#properties .field_column", t_lightbox_package ).hide();
		$( "#properties .field_edit_container", t_lightbox_package ).show();
		
		preview_scroll_to_field();
	}
	
	return false;
});

$( t_lightbox_package ).delegate( ".properties_column_headline_button a", "click", function(p_event, p_confirmed)
{
	var t_changes = $( "#field_details", t_lightbox_package ).form_changes_checker();

	if( t_changes.length > 0 && p_confirmed != true )
	{
		var t_date = new Date();
		var t_identifier = t_date.getTime();
		
		$( this ).addClass( "changes_confirm_" + t_identifier );
		show_confirm_lightbox( t_identifier, "click" );
	}
	else
	{
		reset_view();	
		
		$( ".add_field", t_lightbox_package ).removeClass( "active" );
		$( ".save", t_lightbox_package ).addClass( "active" );
		
		if( t_field_change_timeout != false )
		{
			clearTimeout(t_field_change_timeout);
			t_field_change_timeout = false;
		}
		var t_field_container = $( this, t_lightbox_package ).closest(".field_container");
		
		$( ".export_scheme_field_container_wrapper td", t_lightbox_package ).width( "36px" );
		$( "#new_field", t_lightbox_package ).width( "0px" );
		$( t_field_container ).closest( "td" ).width( "360px" );
		
		$( "#properties .field_column", t_lightbox_package ).hide();
		$( "#properties .field_edit_container", t_lightbox_package ).show();

		var t_container_position = $( t_field_container ).closest( "td" ).position().left;
		$( ".export_scheme_field_container_wrapper" ).scrollLeft( t_container_position + 360 - 640 );
		
		$( "#field_details", t_lightbox_package ).form_changes_checker( 'initialize', false );
		
		preview_scroll_to_field();
	}
	
	return false;
});



$( t_lightbox_package ).delegate( ".properties_data_select", "keyup", load_preview );
$( t_lightbox_package ).delegate( ".properties_data_select", "change", load_preview );

$( t_lightbox_package ).delegate( ".select_properties_language", "keyup", load_preview );
$( t_lightbox_package ).delegate( ".select_properties_language", "change", load_preview );