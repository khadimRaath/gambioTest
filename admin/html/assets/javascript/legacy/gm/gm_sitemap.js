/* gm_sitemap.js <?php
#   --------------------------------------------------------------
#   gm_sitemap.js 2011-01-24 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2011 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#
#   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
#   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
#   NEW GX-ENGINE LIBRARIES INSTEAD.
#   --------------------------------------------------------------
?>*/

function gm_create_sitemap(action) {
	$("#gm_box_sitemap").fadeIn('normal');
	$("#gm_box_google").html('<img src="../images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');
	$("#gm_box_google").load(action);

	return;
}