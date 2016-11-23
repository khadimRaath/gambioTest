/*  --------------------------------------------------------------
 *  GMCounter.js 2011-01-24 gambio
 *  Gambio GmbH
 *  http://www.gambio.de
 *  Copyright (c) 2011 Gambio GmbH
 *  Released under the GNU General Public License (Version 2)
 *  [http://www.gnu.org/licenses/gpl-2.0.html]
 *  --------------------------------------------------------------
 */

$(document).ready(function() {
	$.get("request_port.php?module=SetScreen", {
		screen_resolution: screen.width + "x" + screen.height,
		color_depth: screen.colorDepth,
		gm_action: "gmc_user_screen"
		}
	);
});
