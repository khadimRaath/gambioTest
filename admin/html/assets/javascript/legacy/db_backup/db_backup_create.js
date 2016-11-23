/* db_backup_create.js <?php
#   --------------------------------------------------------------
#   db_backup_create.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$( ".confirm_db_backup_create", t_lightbox_package ).bind( "click", function()
{
	if( $( this ).hasClass( "active" ) ) return false;
	$( this ).addClass( "active" ).hide();
	$( ".button_left_container .lightbox_close", t_lightbox_package ).hide();
	$( ".db_backup_lightbox_process", t_lightbox_package ).css( "visibility", "visible" );
	reset();
});

function reset()
{
	$.ajax({
		type:		"GET",
		url:		"request_port.php?module=DBBackup&action=reset_db_backup&page_token=" + jse.core.config.get('pageToken'),
		timeout:	30000,
		context:	this,
		success:	function( p_response )
					{
						create_backup();
					},
		error:		function( p_jqXHR, p_exception )
					{
						reset();
					}
	});
}

function create_backup()
{
	$.ajax({
		type:		"GET",
		url:		"request_port.php?module=DBBackup&action=create_db_backup&page_token=" + jse.core.config.get('pageToken'),
		timeout:	30000,
		context:	this,
		success:	function( p_response )
					{
						if( $.type( p_response ) == "object" && ( p_response.status == "continue_backup" || p_response.status == "backup_done" ) )
						{
							$( ".progress_text", t_lightbox_package ).html( p_response.progress + "%" );
							$( ".progress_job", t_lightbox_package ).html( p_response.job );
							$( ".progress_marker", t_lightbox_package ).width( p_response.progress + "%" );
							if( p_response.status == "continue_backup" )
							{
								create_backup();
							}
							else if( p_response.status == "backup_done" )
							{
								bundle_backup( 'zip' );
							}	
						}
						else
						{
							if( $.type( p_response ) == "object" )
							{
								var t_error = p_response.error_message;
								if( $.trim( t_error ) == "" )
								{
									t_error = "Unbekannter Fehler!";
								}
								reset_backup( t_error );
							}
							else
							{
								reset_backup( p_response );
							}
						}
										
					},
		error:		function( p_jqXHR, p_exception )
					{
						reset_backup( "Unbekannter Fehler!" );
					}
	});
}

function bundle_backup( p_bundle_type )
{
	var c_bundle_type = $.trim( p_bundle_type );
	$.ajax({
		type:		"GET",
		url:		"request_port.php?module=DBBackup&action=bundle_db_backup&bundle_type=" + c_bundle_type + "&page_token=" + jse.core.config.get('pageToken'),
		timeout:	30000,
		context:	this,
		success:	function( p_response )
					{
						if( $.type( p_response ) != "object" || p_response.status == "error" )
						{
							if( c_bundle_type == 'zip' )
							{
								$( ".progress_marker", t_lightbox_package ).width( "97%" );
								$( ".progress_text", t_lightbox_package ).html( "97%" );
								bundle_backup( 'gzip' );
							}
							else if( c_bundle_type == 'gzip' )
							{
								$( ".progress_marker", t_lightbox_package ).width( "98%" );
								$( ".progress_text", t_lightbox_package ).html( "98%" );
								bundle_backup( 'sql' );
							}
							else if( c_bundle_type == 'sql' && $.type( p_response ) != "object" )
							{
								reset_backup( p_response );
							}
							else if( c_bundle_type == 'sql' && $.type( p_response ) == "object" )
							{
								reset_backup( p_response.error_message );
							}
						}
						else if( p_response.status == "success" )
						{
							$( ".progress_marker", t_lightbox_package ).width( "99%" );
							$( ".progress_text", t_lightbox_package ).html( "99%" );
							$( ".progress_job", t_lightbox_package ).html( p_response.job );
							reset_backup();
						}					
					},
		error:		function( p_jqXHR, p_exception )
					{
						reset_backup( "Unbekannter Fehler!" );
					}
	});
}

function reset_backup( p_error )
{
	var c_error = false;
	if( $.trim( p_error ) != '' && $.trim( p_error ) != 'false' )
	{
		c_error = $.trim( p_error );
	}
	$.ajax({
		type:		"GET",
		url:		"request_port.php?module=DBBackup&action=reset_db_backup&page_token=" + jse.core.config.get('pageToken'),
		timeout:	30000,
		context:	this,
		success:	function( p_response )
					{
						if( c_error == false )
						{
							$( ".progress_job", t_lightbox_package ).html( p_response.job );
							$( "#db_backup_wrapper" ).html( p_response.html );
							$( ".progress_marker", t_lightbox_package ).width( "100%" );
							$( ".progress_text", t_lightbox_package ).html( "100%" );
						}
						else
						{
							$( ".db_backup_lightbox_error_message", t_lightbox_package ).html( c_error ).show();
							$( ".db_backup_lightbox_process", t_lightbox_package ).hide();
						}
						$( ".button_right_container .lightbox_close", t_lightbox_package ).show();	
					},
		error:		function( p_jqXHR, p_exception )
					{
						reset_backup( c_error );
					}
	});
}

$( ".lightbox_close", t_lightbox_package ).live( "click", function()
{
	$.lightbox_plugin( "close", t_lightbox_identifier );
	return false;
});