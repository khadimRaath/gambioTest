<?php
/* --------------------------------------------------------------
   gm_gprint_admin_orders_2.php 2009-11-30 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintContentManager.php');
    	
$coo_gm_gprint_content_manager = new GMGPrintContentManager();
$coo_gm_gprint_order_data = $coo_gm_gprint_content_manager->get_orders_products_content($order->products[$i]['opid'], true);

$count_index = 0;

foreach($contents AS $t_key => $t_value)
{
	$count_index = $t_key;
	if($contents[$t_key]['text'] == '<small>&nbsp;<i> - : </i></small></nobr>')
	{
		// delete empty attributes (random id)
		unset($contents[$t_key]);
		$count_index--;
	}
}

for($m = 0; $m < count($coo_gm_gprint_order_data); $m++)
{
	$count_index++;
	if(empty($coo_gm_gprint_order_data[$m]['DOWNLOAD_KEY']))
	{
		$contents[$count_index] = array ('text' => '<small>&nbsp;<i> - '.$coo_gm_gprint_order_data[$m]['NAME'].': '.$coo_gm_gprint_order_data[$m]['VALUE'].'</i></small></nobr>');
	}
	else
	{
		$contents[$count_index] = array ('text' => '<small>&nbsp;<i> - '.$coo_gm_gprint_order_data[$m]['NAME'].': <a href="' . xtc_href_link('request_port.php', 'module=GPrintDownload&key=' . $coo_gm_gprint_order_data[$m]['DOWNLOAD_KEY']) . '"><u>'.$coo_gm_gprint_order_data[$m]['VALUE'].'</u></a></i></small></nobr>');
	}
}

?>