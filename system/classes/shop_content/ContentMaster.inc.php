<?php
/* --------------------------------------------------------------
   ContentMaster.inc.php 2012-06-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ContentMaster 
{
	function get_content($p_content_group, $p_languages_id = false, $p_ignore_status = false, $p_customers_status_id = false)
	{
		$t_result_array = array();
		
		$c_content_group = (int)$p_content_group;		
		
		$c_languages_id = (int)$_SESSION['languages_id'];
		if($p_languages_id !== false)
		{
			$c_languages_id = (int)$p_languages_id;
		}
		
		$c_customers_status_id = (int)$_SESSION['customers_status']['customers_status_id'];
		if($p_customers_status_id !== false)
		{
			$c_customers_status_id = (int)$p_customers_status_id;
		}
		
		if(GROUP_CHECK == 'true')
		{
			$t_group_check = "AND group_ids LIKE '%c_" . $c_customers_status_id . "_group%'";
		}

		$t_ignore_status = " AND content_status = '1' ";
		if($p_ignore_status)
		{
			$t_ignore_status = '';
		}
		
		$t_sql = "SELECT
						content_id,
						content_title,
						content_heading,
						content_text,
						content_file,
						languages_id,
						file_flag,
						content_status,
						gm_link,
						gm_link_target
					FROM " . TABLE_CONTENT_MANAGER . "
					WHERE 
						content_group = '" . $c_content_group . "'						
						AND languages_id = '" . $c_languages_id . "' 
						" . $t_group_check
						  . $t_ignore_status;
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
		}

		return $t_result_array;
	}
}

?>