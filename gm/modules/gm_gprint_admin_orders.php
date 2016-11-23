<?php
/* --------------------------------------------------------------
   gm_gprint_admin_orders.php 2015-06-23 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMJSON.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintContentManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderElements.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderSurfaces.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderSurfacesManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintOrderManager.php');

$coo_gm_gprint_content_manager = new GMGPrintContentManager();
$coo_gm_gprint_order_data = $coo_gm_gprint_content_manager->get_orders_products_content($productInformation['opid'], true);

for($m = 0; $m < count($coo_gm_gprint_order_data); $m++)
{
	if(empty($coo_gm_gprint_order_data[$m]['DOWNLOAD_KEY']))
	{		
		echo '- ' . $coo_gm_gprint_order_data[$m]['NAME'] . ': ' . $coo_gm_gprint_order_data[$m]['VALUE'] . '<br />';
	}
	else
	{
		echo '- ' . $coo_gm_gprint_order_data[$m]['NAME'] . ': <a href="' . xtc_href_link('request_port.php', 'module=GPrintDownload&key=' . $coo_gm_gprint_order_data[$m]['DOWNLOAD_KEY']) . '" target="_blank">' . $coo_gm_gprint_order_data[$m]['VALUE'] . '</a><br />';
	}
}

$coo_grint_order_manager = new GMGPrintOrderManager();
$t_order_surfaces_groups_id = $coo_grint_order_manager->get_order_surfaces_groups_id($productInformation['opid']);

if($t_order_surfaces_groups_id > 0)
{
	echo '<div class="show-details" id="show_order_surfaces_groups_id_' . $t_order_surfaces_groups_id . '"><span data-gx-compatibility="orders/order_customizer" data-order_customizer-selector="#order_surfaces_groups_id_' . $t_order_surfaces_groups_id . '" style="cursor: pointer">GX-Customizer Details</span></div>';
	echo '<div style="overflow: hidden; display: none;" class="gm_gprint_order_set" id="order_surfaces_groups_id_' . $t_order_surfaces_groups_id . '">';
	echo '<ul id="gm_gprint_tabs_' . $t_order_surfaces_groups_id . '"></ul>';
	echo "\n";
	echo '<div id="gm_gprint_content_' . $t_order_surfaces_groups_id . '" class="gm_gprint_content"></div>';
	echo "\n";
	echo '</div>';
}
