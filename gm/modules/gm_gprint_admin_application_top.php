<?php
/* --------------------------------------------------------------
   gm_gprint_admin_application_top.php 2010-02-22 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

if($_SESSION['customers_status']['customers_status_id'] === '0')
{
	define('FILENAME_GM_GPRINT', 'gm_gprint.php');

	require_once('../gm/modules/gm_gprint_tables.php');
	require_once('../gm/classes/GMGPrintProductManager.php');
	
	require_once('../inc/xtc_get_categories.inc.php');

	// save product
	if(substr_count($_SERVER["SCRIPT_NAME"], 'categories.php') > 0 && $_GET['action'] == 'update_product' && isset($_POST['gm_gprint_surfaces_groups_id']))
	{
		$t_gm_gprint_products_id = (int)$_POST['products_id'];
		$t_gm_gprint_surfaces_groups_id = (int)$_POST['gm_gprint_surfaces_groups_id'];

		$coo_gm_gprint_product_manager = new GMGPrintProductManager();
		
		if($t_gm_gprint_surfaces_groups_id > 0)
		{
			$coo_gm_gprint_product_manager->add($t_gm_gprint_surfaces_groups_id, $t_gm_gprint_products_id);
		}
		else
		{
			$coo_gm_gprint_product_manager->remove($t_gm_gprint_products_id);
		}
	}
	elseif(substr_count($_SERVER["SCRIPT_NAME"], 'categories.php') > 0 && $_GET['action'] == 'insert_product' && isset($_POST['gm_gprint_surfaces_groups_id']))
	{
		$t_products_autoindex = 1;
			
		$t_get_products_autoindex = xtc_db_query("SHOW TABLE STATUS LIKE 'products'");
		if(mysqli_num_rows($t_get_products_autoindex) == 1){
			$t_autoindex = xtc_db_fetch_array($t_get_products_autoindex);
			$t_products_autoindex = $t_autoindex['Auto_increment'];
		}	

		$t_gm_gprint_products_id = (int)$_POST['products_id'];
		$t_gm_gprint_surfaces_groups_id = (int)$_POST['gm_gprint_surfaces_groups_id'];

		$coo_gm_gprint_product_manager = new GMGPrintProductManager();
		
		if($t_gm_gprint_surfaces_groups_id > 0)
		{
			$coo_gm_gprint_product_manager->add($t_gm_gprint_surfaces_groups_id, $t_products_autoindex);
		}
		else
		{
			$coo_gm_gprint_product_manager->remove($t_products_autoindex);
		}
	}
	// save category
	elseif(substr_count($_SERVER["SCRIPT_NAME"], 'categories.php') > 0 && $_GET['action'] == 'update_category' && isset($_POST['gm_gprint_surfaces_groups_id']))
	{
		$coo_gm_gprint_product_manager = new GMGPrintProductManager();
		
		$coo_gm_gprint_product_manager->save_category($_POST['categories_id'], $_POST['gm_gprint_surfaces_groups_id'], $_POST['gm_gprint_subcategories'], (int) $_POST['gm_gprint_delete_assignment']);
	}
}
?>