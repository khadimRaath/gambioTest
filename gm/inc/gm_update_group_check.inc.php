<?php
/* --------------------------------------------------------------
   gm_update_group_check.inc.php 2009-05-26 gm
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

	function gm_update_group_check($p_value_old, $p_value_new)
	{

		$t_gm_update_group_check		= false;

		/* activate group check */
		if(strtolower($p_value_old) == 'false' && strtolower($p_value_new) == 'true')
		{
			$t_gm_update_group_check	= true;
			$t_group_permission			= 1;
		}
		/* deactivate group check */
		elseif(strtolower($p_value_old) == 'true' && strtolower($p_value_new) == 'false')
		{
			$t_gm_update_group_check	= true;
			$t_group_permission			= 0;
		}


		if($t_gm_update_group_check)
		{
			$t_gm_customers_statuses_array = array();
			$t_gm_customers_statuses_array = xtc_get_customers_statuses();

			/* GROUP_CHECK content_manager */
			xtc_db_query("
						UPDATE " . 
							TABLE_CATEGORIES . " 
						SET 
							group_ids ='" . $t_gm_group_ids . "' 
					");						

			foreach($t_gm_customers_statuses_array as $t_key => $t_value)
			{
				/* UPDATE GROUP_CHECK CATEGORIES */
				xtc_db_query("
								UPDATE " . 
									TABLE_CATEGORIES . " 
								SET 
									group_permission_" . $t_key . " = '" . $t_group_permission . "' 
							");

				/* UPDATE GROUP_CHECK PRODUCTS */
				xtc_db_query("
								UPDATE " . 
									TABLE_PRODUCTS . " 
								SET 
									group_permission_" . $t_key . " = '" . $t_group_permission . "' 
							");


				$t_group_permission_ids .= 'c_' . $t_key . '_group,';
			}

			/* UPDATE GROUP_CHECK CONTENT_MANAGER */
			if(strtolower($p_value_old) == 'true' && strtolower($p_value_new) == 'false') 
			{
				$t_group_permission_ids	= '';

			}

			xtc_db_query("
						UPDATE " . 
							TABLE_CONTENT_MANAGER . " 
						SET 
							group_ids ='" . $t_group_permission_ids . "' 
					");
		}
	}
?>