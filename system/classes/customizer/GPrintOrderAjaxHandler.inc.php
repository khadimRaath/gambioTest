<?php
/* --------------------------------------------------------------
   GPrintOrderAjaxHandler.inc.php 2013-11-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/GMJSON.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintConfiguration.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintFileManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintSurfacesManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintSurfaces.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintElements.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintSurfacesGroupsManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintCartManager.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMGPrintWishlistManager.php');
require_once(DIR_FS_CATALOG . 'gm/modules/gm_gprint_tables.php');

class GPrintOrderAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	function proceed()
	{
		$t_output = '';
		
		if($this->v_data_array['GET']['action'] == 'load_surfaces_group')
		{
			$c_surfaces_groups_id = 0;
			if(isset($this->v_data_array['GET']['surfaces_groups_id']))
			{
				$c_surfaces_groups_id = (int)$this->v_data_array['GET']['surfaces_groups_id'];
			}
			
			$coo_gprint_order_surfaces_manager = new GMGPrintOrderSurfacesManager($c_surfaces_groups_id);
			$t_output = $coo_gprint_order_surfaces_manager->load_surfaces_group($c_surfaces_groups_id);
		}
		
		$this->v_output_buffer = $t_output;
	}
}