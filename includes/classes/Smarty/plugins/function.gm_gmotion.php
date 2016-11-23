<?php
/* --------------------------------------------------------------
   function.gm_motion.php 2012-02-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

function smarty_function_gm_gmotion($params, &$smarty)
{
	$t_output = '';
	$t_products_id = 0;
	
	if(isset($_GET['info']))
	{
		$t_site = explode('_', $_GET['info']);
		$t_products_id = (int) str_replace('p', '', $t_site[0]);
	}
	elseif(isset($_GET['products_id']))
	{
		$t_products_id = (int)xtc_get_prid($_GET['products_id']);
	}
		
	$coo_gm_gmotion = new GMGMotion();
	
	$t_data_array = $coo_gm_gmotion->load($t_products_id);
	$coo_product = new product($t_products_id);

	if(!empty($t_data_array))
	{
		$coo_gm_gprint_smarty = new Smarty;
		
		$coo_gm_gprint_smarty->assign('PRODUCTS_ID', $t_products_id);
		$coo_gm_gprint_smarty->assign('WIDTH', PRODUCT_IMAGE_INFO_WIDTH);
		$coo_gm_gprint_smarty->assign('HEIGHT', PRODUCT_IMAGE_INFO_HEIGHT);
	
		$t_output = $coo_gm_gprint_smarty->fetch(CURRENT_TEMPLATE . '/module/gm_gmotion_small.html');
	}	
	
	return $t_output;
}

?>