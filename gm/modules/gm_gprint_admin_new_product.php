<?php
/* --------------------------------------------------------------
   gm_gprint_admin_new_product.php 2009-11-13 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

require_once('../gm/modules/gm_gprint_tables.php');
require_once('../gm/classes/GMGPrintProductManager.php');

$coo_gm_gprint_product_manager = new GMGPrintProductManager();

$t_gm_gprint_surfaces_groups = $coo_gm_gprint_product_manager->get_surfaces_groups();

$t_gm_gprint_pull_down = array();
$t_gm_gprint_pull_down[] = array('id' => '', 'text' => '');

foreach($t_gm_gprint_surfaces_groups AS $t_gm_gprint_key => $t_gm_gprint_value)
{
	$t_gm_gprint_pull_down[] = array('id' => $t_gm_gprint_surfaces_groups[$t_gm_gprint_key]['ID'], 'text' => $t_gm_gprint_surfaces_groups[$t_gm_gprint_key]['NAME']);
}

$t_gm_gprint_surfaces_groups_id = $coo_gm_gprint_product_manager->get_surfaces_groups_id($_GET['pID']);		

?>		
<tr>
      	<td>GX-Customizer Set</td>
   		<td><?php echo xtc_draw_pull_down_menu('gm_gprint_surfaces_groups_id', $t_gm_gprint_pull_down, $t_gm_gprint_surfaces_groups_id); ?></td>
</tr>