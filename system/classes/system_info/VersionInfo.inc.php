<?php
/* --------------------------------------------------------------
   VersionInfo.inc.php 2012-12-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class VersionInfo
{
	var $v_shop_versioninfo_array;
		
	function get_shop_versioninfo()
	{
		if(!isset($this->v_shop_versioninfo_array))
		{
			$this->set_shop_versioninfo();
		}

		if(!count($this->v_shop_versioninfo_array))
		{
			return '';
		}

		return $this->v_shop_versioninfo_array;		
	}
	
	
	function set_shop_versioninfo()
	{
		//$t_pattern = DIR_FS_CATALOG . 'version_info/_samples/*.php';
		$t_pattern = DIR_FS_CATALOG . 'version_info/*.php';
		$t_versioninfo_array = glob($t_pattern);
		
		if($t_versioninfo_array !== false && count($t_versioninfo_array) > 0)
		{
			foreach($t_versioninfo_array as $t_path)
			{
				$this->v_shop_versioninfo_array[basename($t_path, '.php')] = $this->get_versioninfo_content($t_path);
			}
		}
		else
		{
			$this->v_shop_versioninfo_array = array();
		}
	}

	
	function get_versioninfo_content($p_path)
	{
		$t_content = '';
		
		if(function_exists('file_get_contents'))
		{
			$t_content = file_get_contents($p_path);
		}
		else
		{
			$fp = fopen($p_path, 'r');
			if($fp !== false)
			{
				while(!feof($fp))
				{
					$t_content .= fread($fp, filesize($p_path));
				}
				fclose($fp);
			}
		}
		
		$t_content = trim(preg_replace('/<\?.*?\?>/s', '', $t_content));
		return $t_content;
	}
}

?>