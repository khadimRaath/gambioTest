/* export_scheme_export.js <?php
#   --------------------------------------------------------------
#   export_scheme_export.js 2016-04-05
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2016 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

var t_scheme_id = t_lightbox_data['scheme_id'];

var t_token = t_lightbox_data['token']; 

var t_timeout = false;

var t_request = false;

var t_progress_canceled = false;

var t_progress = 0;

function run_import(p_progress)
{
	var t_progress = typeof(p_progress) == "undefined" ? 0 : p_progress;
	
	t_request = $.ajax({
		type:       "POST",
		url:        "../request_port.php?module=CSV&action=import",
		dataType:	"json",
		context:	this,
		data:		$("#import_form_container form").serialize() + '&progress=' + t_progress,
		success:    function( p_response )
					{	
						if(p_response == null)
						{
							$(".export_message").html(js_options.error_handling.csv.response_error);
							$(".export_progress_text").html("-%");
							$(".export_progress_marker").width("0%");
							$(".export_job").html(js_options.error_handling.error + "!");
							$( ".cancel", t_lightbox_package ).hide();
							$( ".close", t_lightbox_package ).show();
							return;
						}
						
						var t_progress = p_response.progress;
						
						$( ".export_progress_text", t_lightbox_package ).html( t_progress + "%" );
						$( ".export_progress_marker", t_lightbox_package ).width( t_progress + "%" );
						$( ".export_job", t_lightbox_package ).html( p_response.job );
						
						if( p_response.repeat == true )
						{
							if( t_progress_canceled == false )
							{
								run_import(t_progress);
							}
							else
							{
								$( ".cancel", t_lightbox_package ).hide();
								$( ".close", t_lightbox_package ).show();
							}
						}
						else if( p_response.repeat == false )
						{
							if( p_response.error == true )
							{		
								$( ".export_message", t_lightbox_package ).html( p_response.message ).css( "color", "red" );
								$( ".export_progress_bar", t_lightbox_package ).css( "visibility", "hidden" );
								$( ".export_progress_text", t_lightbox_package ).css( "visibility", "hidden" );
							}
							else
							{
								$( ".export_message", t_lightbox_package ).html( p_response.message );
							}
							
							t_request = false;
							
							if( p_response.rebuild_index != false )
							{
								run_rebuild_properties_index(t_progress);
							}
							else
							{
								$( ".cancel", t_lightbox_package ).hide();
								$( ".close", t_lightbox_package ).show();
							}
						}
					},
		error:		function( p_jqXHR, p_exception )
					{
						$(".export_message").html(js_options.error_handling.csv.response_error);
						$(".export_progress_text").html("-%");
						$(".export_progress_marker").width("0%");
						$(".export_job").html(js_options.error_handling.error + "!");
						$( ".cancel", t_lightbox_package ).hide();
						$( ".close", t_lightbox_package ).show();
						$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
					}
	});
	return false;
}

function run_rebuild_properties_index(p_progress)
{
	var t_progress = typeof(p_progress) == "undefined" ? 0 : p_progress;
	
	t_request = $.ajax({
		type:       "POST",
		url:        "../request_port.php?module=CSV&action=rebuild_properties_index",
		dataType:	"json",
		context:	this,
		success:    function( p_response )
					{	
						if(p_response == null)
						{
							$(".export_message").html(js_options.error_handling.csv.response_error);
							$(".export_progress_text").html("-%");
							$(".export_progress_marker").width("0%");
							$(".export_job").html(js_options.error_handling.error + "!");
							$( ".cancel", t_lightbox_package ).hide();
							$( ".close", t_lightbox_package ).show();
							return;
						}
						
						var t_progress = p_response.progress;
						
						$( ".export_progress_text", t_lightbox_package ).html( t_progress + "%" );
						$( ".export_progress_marker", t_lightbox_package ).width( t_progress + "%" );
						$( ".export_job", t_lightbox_package ).html( p_response.job );
						
						if( p_response.repeat == true )
						{
							if( t_progress_canceled == false )
							{
								run_rebuild_properties_index(t_progress);
							}
							else
							{
								$( ".cancel", t_lightbox_package ).hide();
								$( ".close", t_lightbox_package ).show();
							}
						}
						else if( p_response.repeat == false )
						{
							if( p_response.error == true )
							{		
								$( ".export_message", t_lightbox_package ).html( p_response.message ).css( "color", "red" );
								$( ".export_progress_bar", t_lightbox_package ).css( "visibility", "hidden" );
								$( ".export_progress_text", t_lightbox_package ).css( "visibility", "hidden" );
							}
							else
							{
								$( ".export_message", t_lightbox_package ).html( p_response.message );
							}
							
							t_request = false;
							$( ".cancel", t_lightbox_package ).hide();
							$( ".close", t_lightbox_package ).show();
						}
					},
		error:		function( p_jqXHR, p_exception )
					{
						$(".export_message").html(js_options.error_handling.csv.response_error);
						$(".export_progress_text").html("-%");
						$(".export_progress_marker").width("0%");
						$(".export_job").html(js_options.error_handling.error + "!");
						$( ".cancel", t_lightbox_package ).hide();
						$( ".close", t_lightbox_package ).show();
						$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
					}
	});
	return false;
}

function run_export()
{
	// Request individualisieren, damit man kein gecachtes Ergebnis erhält.
	var t_rand = Math.round( Math.random() * 1000000 );
	
	t_request = $.ajax({
		type:       "GET",
		url:        "../request_port.php?module=CSV&action=export",
		dataType:	"json",
		context:	this,
		data:		{
						"scheme_id":	t_scheme_id,
						"token":		t_token,
						"rand":			t_rand
					},
		success:    function( p_response )
					{	
						if(p_response == null)
						{
							$(".export_message").html(js_options.error_handling.csv.response_error);
							$(".export_progress_text").html("-%");
							$(".export_progress_marker").width("0%");
							$(".export_job").html(js_options.error_handling.error + "!");
							$( ".cancel", t_lightbox_package ).hide();
							$( ".close", t_lightbox_package ).show();
							$( document ).trigger( "resume_actualize_cronjob_status" );
							return;
						}
						
						var t_progress = p_response.progress;
						
						$( ".export_progress_text", t_lightbox_package ).html( t_progress + "%" );
						$( ".export_progress_marker", t_lightbox_package ).width( t_progress + "%" );
						$( ".export_job", t_lightbox_package ).html( p_response.job );
						
						if( p_response.repeat == true )
						{
							if( t_progress_canceled == false )
							{
								run_export();
							}
							else
							{
								$.ajax({
									type:       "POST",
									url:        "request_port.php?module=CSV&action=clean_export",
									timeout:    10000,
									dataType:	"json",
									context:	this,
									data:		{
													"scheme_id":	t_scheme_id
												},
									success:	function()
												{
													$.lightbox_plugin( "close", t_lightbox_identifier );
												}
								});
							}
						}
						else if( p_response.repeat == false )
						{
							t_request = false;
							$( ".cancel", t_lightbox_package ).hide();
							$( ".close", t_lightbox_package ).show();

							$( "#tab_container_" + p_response.export_type ).html( p_response.html );
							$.initialize_tooltip_plugin();
							
							$( document ).trigger( "resume_actualize_cronjob_status" );

							if( t_lightbox_data['download'] == "true" )
							{
								$('.close').text(jse.core.lang.translate('download_and_close', 'csv'));
								
								// The onclick event is necessary in order to prevent the browsers pop-up blocks.
								// The user has to explicitly confirm the opening of a new window with a download.
								$( ".close", t_lightbox_package ).on("click", function() {
									window.open("../request_port.php?module=CSV&action=download_export_file&scheme_id=" + t_scheme_id, '_blank');
								});
							}
						}
					},
		error:		function( p_jqXHR, p_exception )
					{
						$(".export_message").html(js_options.error_handling.csv.response_error);
						$(".export_progress_text").html("-%");
						$(".export_progress_marker").width("0%");
						$(".export_job").html(js_options.error_handling.error + "!");
						$( ".cancel", t_lightbox_package ).hide();
						$( ".close", t_lightbox_package ).show();
						$( document ).trigger( "resume_actualize_cronjob_status" );
						$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
					}
	});
	return false;
}
	
$( t_lightbox_package ).delegate( ".cancel", "click", function(){
	t_progress_canceled = true;
	$( this ).addClass( "active" );
	$( ".export_message", t_lightbox_package ).css( "visibility", "visible" ).html( js_options.error_handling.csv['export_canceled'] );
});

$( t_lightbox_package ).delegate( ".close", "click", function(){
	if( t_timeout != false )
	{
		clearTimeout( t_timeout );
	}
	$.lightbox_plugin( "close", t_lightbox_identifier );
});


if(typeof t_lightbox_data['filename'] == 'undefined')
{
	run_export();
}
else
{
	run_import();
}