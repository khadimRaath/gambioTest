<?php
/* --------------------------------------------------------------
   gm_gprint_gm_pdf_order.php 2013-03-06 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintContentManager.php');
    	
$coo_gm_gprint_content_manager = new GMGPrintContentManager();
$coo_gm_gprint_order_data = $coo_gm_gprint_content_manager->get_orders_products_content($order_data_values['orders_products_id']);

$count_index = 0;

reset($attributes_data);
foreach($attributes_data AS $t_key => $t_value)
{
	$count_index = $t_key;
	if($attributes_data[$t_key][1] == ': ')
	{
		// delete empty attributes (random id)
		unset($attributes_data[$t_key]);
		$count_index--;
	}
}

for($m = 0; $m < count($coo_gm_gprint_order_data); $m++)
{
	$count_index++;
	$attributes_data[$count_index] = array ('', str_replace('&euro;', chr(128), $coo_gm_gprint_order_data[$m]['NAME']).': '.str_replace('&euro;', chr(128), $coo_gm_gprint_order_data[$m]['VALUE']));
}

?>