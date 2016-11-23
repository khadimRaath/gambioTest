/* export_overview.js <?php
#   --------------------------------------------------------------
#   export_overview.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

t_actualize_cronjob_status_timeout = false;
t_actualize_cronjob_status_request = false;

function actualize_cronjob_status()
{
	t_actualize_cronjob_status_request = $.ajax({
		type:       "GET",
		url:        "request_port.php?module=CSV&action=get_cronjob_status",
		timeout:    10000,
		dataType:	"json",
		context:	this,
		success:    function( p_response )
					{	
						$.each(p_response.cronjob_status, function( t_scheme_id, t_scheme ) {	
							if( t_scheme.status != "no_cronjob" )
							{
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).removeClass( "queueing" ).removeClass( "pending" ).removeClass( "running" );
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).addClass( t_scheme.status );
								$( "#export_scheme_container_" + t_scheme_id + " .scheme_last_export_date" ).html( t_scheme.date_last_export );
								if( t_scheme.date_last_export != "-" )
								{
									$( "#export_scheme_container_" + t_scheme_id + " .scheme_filename a" ).attr( "onclick", "" );
								}
								if( t_scheme.file_exists == "true" )
								{
									$( "#export_scheme_container_" + t_scheme_id + " .scheme_filename a" ).removeClass("no_file");
								}
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).tooltip_plugin('update', t_scheme.message );
							}
						});
						t_actualize_cronjob_status_timeout = setTimeout( actualize_cronjob_status, 20000);
					},
		error:      function( p_jqXHR, p_exception )
					{	
						if(fb){ console.log( p_jqXHR ) };
						if(fb){ console.log( p_exception ) };
						t_actualize_cronjob_status_timeout = setTimeout( actualize_cronjob_status, 5000);
					}
	});
}

t_actualize_cronjob_status_timeout = setTimeout( actualize_cronjob_status, 20000);

$(document).bind( "stop_actualize_cronjob_status", function(){
	if( t_actualize_cronjob_status_request != false )
	{
		t_actualize_cronjob_status_request.abort();
		t_actualize_cronjob_status_request = false;
	}
	if( t_actualize_cronjob_status_timeout != false )
	{
		clearTimeout(t_actualize_cronjob_status_timeout);
		t_actualize_cronjob_status_timeout = false;
	}	
});

$(document).bind( "resume_actualize_cronjob_status", function(){ t_actualize_cronjob_status_timeout = setTimeout( actualize_cronjob_status, 5000) });

$( "#exportSchemes" ).delegate( ".export_tab_headline", "click", function()
{
	if( $( this).hasClass( "active" ) ) return false;
	
	$(document).trigger( "stop_actualize_cronjob_status" );
	
	t_type_id = $( this ).attr( "rel" );
	if( t_type_id != "tab_container_999" )
	{
		$(document).trigger( "resume_actualize_cronjob_status" );
	}
	
	$( "#exportSchemes .export_tab_headline" ).removeClass( "active" );
	$( this ).addClass( "active" );
	$( "#exportSchemes .tab_container" ).hide();
	$( "#exportSchemes #" + t_type_id ).show();
	
	return false;
});

$( "#exportSchemes" ).delegate( ".export_big_lightbox", "click", function(){
	$(document).trigger( "stop_actualize_cronjob_status" );
	$( this ).lightbox_plugin( 
	{
		'background_color': '#EFEFEF',
        'lightbox_width': '900px',
		'shadow_close_onclick': false
	});
	return false;
});

$( "#exportSchemes" ).delegate( ".export_small_lightbox", "click", function(){
	$(document).trigger( "stop_actualize_cronjob_status" );
	$( this ).lightbox_plugin( 
	{
		'background_color': '#EFEFEF',
		'lightbox_width': '360px',
		'shadow_close_onclick': false
	});
	return false;
});

$( "#exportSchemes" ).delegate( ".start_import_button", "click", function()
{
	if($("#select_import_file").val() == 0)
	{
		$("#select_import_file").closest( ".row" ).addClass( "error" );
		$("#select_import_file").tooltip_plugin("update", "Bitte w&auml;hlen Sie eine Datei aus!")
		return false;
	}
	else
	{
		$( "#select_import_file" ).removeClass("error");
		$( "#select_import_file" ).tooltip_plugin('reset_content');
	}
	
	var t_filename = $("#select_import_file").val();
	var t_separator = $("#import_field_separator").val();
	var t_quote = $("#import_field_quotes").val();
	var t_href = $(this).attr("href");
	
	$(this).attr("href", t_href + "&filename=" + t_filename + "&separator=" + t_separator + "&quote=" + t_quote);
	
	$( this ).lightbox_plugin( 
	{
		'background_color': '#EFEFEF',
		'lightbox_width': '360px',
		'shadow_close_onclick': false
	});
	return false;
});

$( "#exportSchemes" ).delegate( ".export_copy_scheme", "click", function()
{
	if( $(this).hasClass( "active" ) ) return false;			
	$(this).addClass( "active" );
	
	var t_loading_wrapper = $( "<div class='loading_wrapper' style='background: #fff; opacity: 0.8; position: absolute; top: 0; left: 0; right: 0; bottom: 0;'> <!-- --> </div>" );
	$( ".export_scheme_overview" ).append( t_loading_wrapper );
	
	var t_scheme_id = $( this ).attr( "rel" );
	
	$.ajax({
		type:       "POST",
		url:        "request_port.php?module=CSV&action=copy_scheme",
		timeout:    10000,
		dataType:	"json",
		context:	this,
		data:		{
						"scheme_id":	t_scheme_id
					},
		success:    function( p_response )
					{	
						$( "#tab_container_" + p_response.export_type ).html( p_response.html );
						$(this).removeClass( "active" );
						$( ".loading_wrapper", ".export_scheme_overview" ).remove();
					},
		error:      function( p_jqXHR, p_exception )
					{	
						$( ".loading_wrapper", ".export_scheme_overview" ).remove();
						if(fb){ console.log( p_jqXHR ) };
						if(fb){ console.log( p_exception ) };
					}
	});
});

$( "#exportSchemes" ).delegate( ".deactivate_export", "click", function()
{
	$.ajax({
		type:       "POST",
		url:        "request_port.php?module=CSV&action=stop_cronjob",
		timeout:    10000,
		dataType:	"json",
		context:	this,
		data:		{
						"status":	"false"
					},
		success:    function( p_response )
					{	
						$( ".deactivate_export" ).hide();
						$( ".pause_export, .continue_export" ).hide();
						$( ".activate_export" ).show();
						$( "#exportSchemes .cronjob_status_icon" ).addClass( "inactive" );
						
						$.each(p_response.cronjob_status, function( t_scheme_id, t_scheme ) {	
							if( t_scheme.status != "no_cronjob" )
							{
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).attr( "title", t_scheme.message );
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).tooltip_plugin('update', t_scheme.message );								
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).removeClass( "queueing" ).removeClass( "pending" ).removeClass( "running" );
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).addClass( t_scheme.status );
							}
						});
					},
		error:      function( p_jqXHR, p_exception )
					{	
						if(fb){ console.log( p_jqXHR ) };
						if(fb){ console.log( p_exception ) };
					}
	});
});

$( "#exportSchemes" ).delegate( ".activate_export", "click", function()
{
	$.ajax({
		type:       "POST",
		url:        "request_port.php?module=CSV&action=stop_cronjob",
		timeout:    10000,
		dataType:	"json",
		context:	this,
		data:		{
						"status":	"true"
					},
		success:    function( p_response )
					{	
						$( ".activate_export" ).hide();
						$( ".deactivate_export, .pause_export" ).show();
						$( "#exportSchemes .cronjob_status_icon" ).removeClass( "inactive" );
						
						$.each(p_response.cronjob_status, function( t_scheme_id, t_scheme ) {	
							if( t_scheme.status != "no_cronjob" )
							{
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).attr( "title", t_scheme.message );
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).tooltip_plugin('update', t_scheme.message );
							}
						});
					},
		error:      function( p_jqXHR, p_exception )
					{	
						if(fb){ console.log( p_jqXHR ) };
						if(fb){ console.log( p_exception ) };
					}
	});
});

$( "#exportSchemes" ).delegate( ".pause_export", "click", function()
{
	$.ajax({
		type:       "POST",
		url:        "request_port.php?module=CSV&action=pause_cronjob",
		timeout:    10000,
		dataType:	"json",
		context:	this,
		data:		{
						"status":	"true"
					},
		success:    function( p_response )
					{	
						$( ".pause_export" ).hide();
						$( ".continue_export" ).show();
						$( "#exportSchemes .cronjob_status_icon" ).addClass( "inactive" );
						
						$.each(p_response.cronjob_status, function( t_scheme_id, t_scheme ) {	
							if( t_scheme.status != "no_cronjob" )
							{
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).attr( "title", t_scheme.message );
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).tooltip_plugin('update', t_scheme.message );
							}
						});
					},
		error:      function( p_jqXHR, p_exception )
					{	
						if(fb){ console.log( p_jqXHR ) };
						if(fb){ console.log( p_exception ) };
					}
	});
});

$( "#exportSchemes" ).delegate( ".continue_export", "click", function()
{
	$.ajax({
		type:       "POST",
		url:        "request_port.php?module=CSV&action=pause_cronjob",
		timeout:    10000,
		dataType:	"json",
		context:	this,
		data:		{
						"status":	"false"
					},
		success:    function( p_response )
					{	
						$( ".continue_export" ).hide();
						$( ".pause_export" ).show();
						$( "#exportSchemes .cronjob_status_icon" ).removeClass( "inactive" );
						
						$.each(p_response.cronjob_status, function( t_scheme_id, t_scheme ) {	
							if( t_scheme.status != "no_cronjob" )
							{
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).attr( "title", t_scheme.message );
								$( "#export_scheme_container_" + t_scheme_id + " .cronjob_status_icon" ).tooltip_plugin('update', t_scheme.message );
							}
						});
					},
		error:      function( p_jqXHR, p_exception )
					{	
						if(fb){ console.log( p_jqXHR ) };
						if(fb){ console.log( p_exception ) };
					}
	});
});

$( "#exportSchemes" ).delegate( ".upload_import_file_button", "click", function()
{
	if( $(this).hasClass( "active" ) ) return false;
	
	t_error_contents = $( "#upload_import_file_form" ).validation_plugin();	
	if( $.inArray( "upload_import_file" , t_error_contents ) > -1 ) return false;
		
	$(this).addClass( "active" );
	t_upload_button = $( this );
	
	$.ajaxFileUpload({
		url: 'request_port.php?module=CSV&action=upload_import_file',
		secureuri: false,
		fileElementId: "upload_import_file",
		dataType: 'json',
		success: function (data, status)
		{
			if( data.status == 'error' )
			{
				$( "#upload_import_file" ).closest( ".row" ).addClass( "error" );
				$( "#upload_import_file" ).attr("orig_title", $( "#upload_import_file" ).attr("title"));
				$( "#upload_import_file" ).attr("title", js_options.error_handling.csv[data.error_code]);
			}
			else
			{
				$( "#upload_import_file" ).closest( ".row" ).removeClass( "error" );
				if( $( "#upload_import_file" ).attr("orig_title") != "" )
				{
					$( "#upload_import_file" ).attr("title", $( "#upload_import_file" ).attr("orig_title"));
				}
				$( "#select_import_file option[value]" ).remove();
				$.each( data.file_list, function( key, value ){
					t_option = $( "<option></option>" ).val( value ).text( value );
					if( value == data.select_file )
					{
						t_option.attr( "selected", "selected" );
					}
					$( "#select_import_file" ).append( t_option );
				});
			
				if($("#select_import_file option").length > 0)
				{
					$( "#import_form_container" ).show();
				}
				$( "#upload_import_file" ).val( "" );
			}
			
			$( t_upload_button ).removeClass( "active" );
			
		},
		error: function (data, status, e)
		{
			$( t_upload_button ).removeClass( "active" );
			if( fb ) console.log( "error while image upload" );
		}

	});
	return false;
});

$( "#exportSchemes" ).delegate( ".show_cronjob_url", "click", function()
{
	$( this ).closest( ".tab_container" ).find( ".cronjob_url_container" ).show();
});