<?php
/* --------------------------------------------------------------
   gm_gprint_admin_new_category.php 2009-11-25 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 
if($_GET['action'] == 'edit_category')
{
	$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
	$coo_lang_file_master->init_from_lang_file('lang/' . basename($_SESSION['language']) . '/admin/gm_gprint.php');

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
	
	?>		
	<tr>
	      	<td class="main strong" valign="top" align="left" width="150">GX-Customizer Set</td>
	   		<td class="main" valign="top" align="left">
	   		<?php 
	   			echo xtc_draw_pull_down_menu('gm_gprint_surfaces_groups_id', $t_gm_gprint_pull_down);
	   		?>
	   		</td>
	</tr>
	<tr>
	      	<td class="main strong" valign="top" align="left" width="150"></td>
	   		<td class="main" valign="top" align="left">
	   		<?php 
	   			echo xtc_draw_checkbox_field('gm_gprint_subcategories', '1', false);
	   			echo '&nbsp;';
	   			echo GM_GPRINT_SUBCATEGORIES;
	   		?>
	   		</td>
	</tr>
	<tr>
	      	<td class="main strong" valign="top" align="left" width="150"></td>
	   		<td class="main" valign="top" align="left">
	   		<?php 
	   			echo xtc_draw_checkbox_field('gm_gprint_delete_assignment', '1', false);
	   			echo '&nbsp;';
	   			echo GM_GPRINT_DELETE_ASSIGNMENT;
	   		?>
	   		</td>
	</tr>
<?php 
}
?>