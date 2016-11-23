/* gm_gprint_order.js <?php
#   --------------------------------------------------------------
#   gm_gprint_order.js 2013-11-18 gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2013 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
?>*/

<?php
if($_SESSION['customers_status']['customers_status_id'] === '0' && $this->v_data_array['GET']['mode'] == 'order')
{
?>

if(typeof(gm_session_id) == 'undefined')
{
	var gm_session_id = '<?php if(isset($this->v_data_array['GET']["XTCsid"]) && !empty($this->v_data_array['GET']["XTCsid"]) && preg_replace("/[^a-zA-Z0-9,-]/", "", $this->v_data_array['GET']["XTCsid"]) === $this->v_data_array['GET']["XTCsid"]) echo $this->v_data_array['GET']["XTCsid"]; ?>';
}

gm_session_id = encodeURIComponent(gm_session_id);

var coo_order_surfaces_manager = null;

<?php
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/gm_gprint_functions.js'));
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintOrderSurfacesManager.js'));
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintOrderSurfaces.js'));
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/GMGPrintOrderElements.js'));
?>

$(document).ready(function()
{
	var t_order_surfaces_groups_id = '0';
	var t_order_sets = Object();

	$('.gm_gprint_order_set').each(function()
	{
		t_order_surfaces_groups_id = $(this).attr('id');
		t_order_surfaces_groups_id = t_order_surfaces_groups_id.replace(/order_surfaces_groups_id_/g, '');

		coo_order_surfaces_manager = new GMGPrintOrderSurfacesManager(t_order_surfaces_groups_id);
		coo_order_surfaces_manager.load_surfaces_group(t_order_surfaces_groups_id);

		t_order_sets[t_order_surfaces_groups_id] = coo_order_surfaces_manager;

	});
});

<?php
}
?>