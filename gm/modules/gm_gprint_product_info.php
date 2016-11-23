<?php
/* --------------------------------------------------------------
   gm_gprint_product_info.php 2009-11-16 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

$coo_gm_gprint_product_manager = new GMGPrintProductManager();

if($coo_gm_gprint_product_manager->get_surfaces_groups_id($product->data['products_id']) !== false)
{
	$coo_gm_gprint_configuration = new GMGPrintConfiguration($_SESSION['languages_id']);
	
	$info_smarty->assign('GM_GPRINT_SHOW_PRODUCTS_DESCRIPTION', $coo_gm_gprint_configuration->get_configuration('SHOW_PRODUCTS_DESCRIPTION'));
	$info_smarty->assign('GM_GPRINT', 1);
}	

?>