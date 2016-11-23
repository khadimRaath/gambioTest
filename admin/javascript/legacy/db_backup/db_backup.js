/* db_backup.js <?php
#   --------------------------------------------------------------
#   db_backup.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$( document ).delegate( ".db_backup_restore", "click", function()
{
	$( this ).lightbox_plugin(
	{
		'background_color': '#EFEFEF',
		'lightbox_width': '460px',
		'shadow_close_onclick': false,
		'close_button_position': 'none'
	});
});

$( document ).delegate( ".db_backup_create", "click", function()
{
	$( this ).lightbox_plugin(
	{
		'background_color': '#EFEFEF',
		'lightbox_width': '460px',
		'shadow_close_onclick': false,
		'close_button_position': 'none'
	});
});

$( document ).delegate( ".db_backup_delete", "click", function()
{
	var t_target = this;
	var t_lightbox_identifier = $( this ).lightbox_plugin(
	{
		'background_color': '#EFEFEF',
		'lightbox_width': '460px'
	});

	$( '#lightbox_package_' + t_lightbox_identifier ).delegate(".delete", "click." + t_lightbox_identifier, function()
	{
		var t_filename = $( t_target ).attr( "rel" );
		$.ajax(
		{
			type:		"GET",
			url:		"request_port.php?module=DBBackup&action=delete_db_backup&filename=" + t_filename + "&page_token=" + jse.core.config.get('pageToken'),
			timeout:	30000,
			dataType:	"json",
			context:	t_target,
			success:	function( p_response )
						{
							if( p_response.status == 'success' )
							{
								$( "#db_backup_wrapper" ).html( p_response.html );
								$.lightbox_plugin( 'close', t_lightbox_identifier );
							}
							else
							{
								$.lightbox_plugin( 'error', t_lightbox_identifier, p_response.error_code );
							}

						},
			error:		function( p_jqXHR, p_exception )
						{
							$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
						}
		});
	});
});
