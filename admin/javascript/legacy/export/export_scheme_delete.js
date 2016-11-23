/* export_scheme_delete.js <?php
#   --------------------------------------------------------------
#   export_scheme_delete.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$( t_lightbox_package ).delegate( ".delete", "click", function()
{
	if( $(this).hasClass( "active" ) ) return false;			
	$(this).addClass( "active" );
	
	$.ajax({
		type:       "POST",
		url:        "request_port.php?module=CSV&action=delete_scheme",
		timeout:    10000,
		dataType:	"json",
		context:	this,
		data:		{
						"scheme_id":	t_lightbox_data.scheme_id
					},
		success:    function( p_response )
					{	
						$( "#export_scheme_container_" + p_response.scheme_id ).remove();
						$.lightbox_plugin( 'close', t_lightbox_identifier );
						t_actualize_cronjob_status = true;
					},
		error:      function( p_jqXHR, p_exception )
					{	
						$.lightbox_plugin( "error", t_lightbox_identifier, p_jqXHR, p_exception );
					}
	});
	return false;
});

$( t_lightbox_package ).delegate( ".cancel", "click", function()
{
	t_actualize_cronjob_status = true;
	return false;
});