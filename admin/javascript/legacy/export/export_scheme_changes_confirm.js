/* SchemeExportChangesConfirm.js <?php
#   --------------------------------------------------------------
#   SchemeExportChangesConfirm.js 2013-01-31 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

$( t_lightbox_package ).delegate( ".discard", "click", function()
{	
	$( document ).trigger( "export_scheme_changes_confirmed", ["changes_confirm_" + t_lightbox_data.event_selector, t_lightbox_data.event_type, t_lightbox_data.form_id] );
	$.lightbox_plugin( "close", t_lightbox_identifier );
	
	return false;
});

$( t_lightbox_package ).delegate( ".cancel", "click", function()
{	
	$( document ).trigger( "export_scheme_changes_canceled", ["changes_confirm_" + t_lightbox_data.event_selector] );
	
	return false;
});