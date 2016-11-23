/* export_scheme_field_delete.js <?php
#   --------------------------------------------------------------
#   export_scheme_field_delete.js 2014-01-03 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2014 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$( t_lightbox_package ).delegate( ".delete", "click", function()
{
	$( document ).trigger( "export_scheme_field_delete", [t_lightbox_data.field_index] );
	$.lightbox_plugin( "close", t_lightbox_identifier );
	return false;
});