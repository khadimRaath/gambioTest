<?php
/* --------------------------------------------------------------
   xtc_get_products_stock.inc.php 2012-09-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


 based on:
 (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
 (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
 (c) 2003	 nextcommerce (xtc_get_products_stock.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
 (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_get_products_stock.inc.php 1009 2005-07-11 16:19:29Z mz $)

 Released under the GNU General Public License
 ---------------------------------------------------------------------------------------*/
 
function xtc_get_products_stock($products_id)
{
	$products_id = xtc_get_prid($products_id);
	$c_products_id = (int)$products_id;
	$t_stock = 0;
	
	$t_sql = "SELECT products_quantity FROM " . TABLE_PRODUCTS . " p WHERE products_id = '" . $c_products_id . "'";
	$t_result = xtc_db_query($t_sql);
	if(xtc_db_num_rows($t_result) == 1)
	{
		$t_result_array = xtc_db_fetch_array($t_result);
		$t_stock = (double)$t_result_array['products_quantity'];
	}
	
	return $t_stock;
}

?>