/* db_backup_restore.js <?php
#   --------------------------------------------------------------
#   db_backup_restore.js 2014-01-08 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$( ".confirm_db_backup_restore", t_lightbox_package ).bind( "click", function()
{
	if( $( this ).hasClass( "active" ) ) return false;
	$( this ).addClass( "active" ).hide();
	$( ".button_left_container .lightbox_close", t_lightbox_package ).hide();
	$( ".db_backup_lightbox_backup_select", t_lightbox_package ).hide();
	$( ".db_backup_lightbox_process", t_lightbox_package ).css( "visibility", "visible" );	
	reset();
});

function restore_backup( p_file_index, p_file_position )
{
	var intRegex = /^\d+$/;
	var c_file_index = 0;
	if( p_file_index && intRegex.test( p_file_index ) )
	{
		c_file_index = p_file_index;
	}
	var c_file_position = 0;
	if( p_file_position && intRegex.test( p_file_position ) )
	{
		c_file_position = p_file_position;
	}
	var t_filename = $( "select", t_lightbox_package ).val();
	$.ajax({
		type:		"GET",
		url:		"request_port.php?module=DBBackup&action=restore_db_backup&filename=" + t_filename 
                        + "&file_position=" + c_file_position + "&file_index=" + c_file_index 
                        + "&page_token=" + jse.core.config.get('pageToken'),
		timeout:	30000,
		dataType:	"json",
		context:	this,
		success:	function( p_response )
					{
						if( $.type( p_response ) == "object" && ( p_response.status == "continue_restore" || p_response.status == "restore_done" ) )
						{
							$( ".progress_text", t_lightbox_package ).html( p_response.progress + "%" );
							$( ".progress_job", t_lightbox_package ).html( p_response.job );
							$( ".progress_marker", t_lightbox_package ).width( p_response.progress + "%" );
							if( p_response.status == "continue_restore" )
							{
								restore_backup( p_response.file_index, p_response.file_position );
							}
							else if( p_response.status == "restore_done" )
							{
								reset_backup();
							}	
						}
						else
						{
							if( $.type( p_response ) == "object" )
							{
								reset_backup( p_response.error_message );
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

function reset()
{
	$.ajax({
		type:		"GET",
		url:		"request_port.php?module=DBBackup&action=reset_db_backup&page_token=" + jse.core.config.get('pageToken'),
		timeout:	30000,
		context:	this,
		success:	function( p_response )
					{
						restore_backup( 0 );
					},
		error:		function( p_jqXHR, p_exception )
					{
						reset();
					}
	});
}