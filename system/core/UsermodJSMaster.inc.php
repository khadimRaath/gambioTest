<?php
/* --------------------------------------------------------------
   UsermodJSMaster.inc.php 2012-01-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class UsermodJSMaster
{
	var $v_page = '';

	function UsermodJSMaster($p_page = 'Global')
	{
		$this->set_page($p_page);
	}


	function set_page($p_page)
	{
		$this->v_page = basename($p_page);
	}


	function get_page()
	{
		return $this->v_page;
	}


	function get_files()
	{
		$t_files_array = array();

		$t_coo_cached_directory = new CachedDirectory(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/usermod/javascript/Global');

		while(false !== ($t_entry = $t_coo_cached_directory->read() ))
		{
			if (substr($t_entry, 0, 1) != '.' && substr($t_entry, -3) == '.js')
			{
				$t_files_array[] = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/usermod/javascript/Global/' . basename($t_entry);
			}
		}

		if($this->get_page() != 'Global')
		{
			$t_coo_cached_directory->set_path(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/usermod/javascript/' . $this->get_page());

			while(false !== ($t_entry = $t_coo_cached_directory->read() ))
			{
				if (substr($t_entry, 0, 1) != '.' && substr($t_entry, -3) == '.js')
				{
					$t_files_array[] = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/usermod/javascript/' . $this->get_page() . '/' . basename($t_entry);
				}				
			}
		}

		return $t_files_array;
	}
}
?>