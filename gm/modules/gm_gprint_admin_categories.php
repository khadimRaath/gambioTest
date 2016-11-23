<?php
/* --------------------------------------------------------------
   gm_gprint_admin_categories.php 2009-12-15 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

$coo_gm_gprint_product_manager = new GMGPrintProductManager();
$t_gm_gprint_surfaces_groups_id = $coo_gm_gprint_product_manager->get_surfaces_groups_id($src_products_id);

if($t_gm_gprint_surfaces_groups_id !== false)
{
	$coo_gm_gprint_product_manager->add($t_gm_gprint_surfaces_groups_id, $dup_products_id);
}

?>