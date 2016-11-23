/* AddAQuickieFormHandler.js <?php
#   --------------------------------------------------------------
#   AddAQuickieFormHandler.js 2013-03-06 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/
/*<?php
if($GLOBALS['coo_debugger']->is_enabled('uncompressed_js') == false)
{
?>*/
$("#menubox_add_a_quickie_body form").bind("submit",function(event,data){if(data=="undefined"||data!="trigger"){event.preventDefault();return;}});
/*<?php
}
else
{
?>*/
$( "#menubox_add_a_quickie_body form" ).bind( "submit", function( event, data ) 
{
	if( data == "undefined" || data != "trigger" )
	{
		event.preventDefault();
		return;
	}
});
/*<?php
}
?>*/