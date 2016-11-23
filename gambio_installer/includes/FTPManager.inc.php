<?php
/* --------------------------------------------------------------
   FTPManager.inc.php 2015-07-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_CATALOG . 'system/classes/security/SecurityCheck.inc.php';

class FTPManager
{
	var $v_connect_id = null;
	var $v_error = '';
	var $v_data_array = array();
	var $v_wrong_chmod_array = array();
	
	function FTPManager($p_connect = true, $p_host, $p_user, $p_password, $p_pasv = true)
	{
		if($p_connect == true)
		{
			$this->connect($p_host, $p_user, $p_password, $p_pasv);
		}
	}


	function connect($p_host, $p_user, $p_password, $p_pasv)
	{
		$t_connect_id = @ftp_connect($p_host) or $this->v_error .= sprintf(ERROR_FTP_CONNECTION, $p_host);
		if($t_connect_id !== false)
		{
			ftp_pasv($t_connect_id, $p_pasv);
			$t_login = @ftp_login($t_connect_id, $p_user, $p_password) or $this->v_error .= sprintf(ERROR_FTP_DATA, $p_user);
		}

		$this->v_connect_id = $t_connect_id;
	}


	function isdir($p_dir)
	{
		if(@ftp_chdir($this->v_connect_id, $p_dir))
		{
			@ftp_cdup($this->v_connect_id);
			return true;
		}
		else
		{
			return false;
		}
	}


	function get_directories($p_dir, $p_no_parent_dirs = false)
	{
		$t_list_array = ftp_nlist($this->v_connect_id, $p_dir);
		$t_final_list = array();
		for($i = 0; $i < count($t_list_array); $i++)
		{
			if($this->isdir($t_list_array[$i]))
			{
				if($p_no_parent_dirs)
				{
					$t_final_list[] = substr(strrchr($t_list_array[$i], '/'), 1);
				}
				else
				{
					$t_final_list[] = $t_list_array[$i];
				}
			}
		}

		sort($t_final_list);

		return $t_final_list;
	}


	function get_dir_content($p_dir)
	{
		return ftp_nlist($this->v_connect_id, $p_dir);
	}


	function quit()
	{
		$t_success = false;

		if($this->v_connect_id !== false && $this->v_connect_id !== null)
		{
			$t_success = ftp_quit($this->v_connect_id);
		}

		return $t_success;
	}


	function is_shop($p_dir)
	{
		$t_found_shop = false;

		if($this->v_connect_id !== false)
		{
			$t_includes_dir_content_array = $this->get_dir_content($p_dir . '/includes');

			for($i = 0; $i < count($t_includes_dir_content_array); $i++)
			{
				if(strpos($t_includes_dir_content_array[$i], 'application_top.php') !== false)
				{
					$t_found_shop = true;
					break;
				}
			}
		}

		return $t_found_shop;
	}


	function chmod_777($p_dir)
	{
		$fp = fopen(DIR_FS_CATALOG.  'gambio_installer/txt/chmod.txt', 'r');

		while($t_line = fgets($fp, 1024)) {
			$t_line = trim($t_line);
			if(strlen($t_line) > 0)
			{
				$t_line = str_replace('\\', '/', $t_line);
				if(substr($t_line, 0, 1) != '/')
				{
					$t_line = '/' . $t_line;
				}
				$t_mode = @ftp_chmod($this->v_connect_id, 0777, $p_dir . $t_line);
			}
		}
		fclose($fp);

		$fp = fopen(DIR_FS_CATALOG.  'gambio_installer/txt/chmod_all.txt', 'r');

		while($t_line = fgets($fp, 1024)) {
			$t_line = trim($t_line);
			if(strlen($t_line) > 0)
			{
				$t_line = str_replace('\\', '/', $t_line);
				if(substr($t_line, 0, 1) != '/')
				{
					$t_line = '/' . $t_line;
				}

				if(substr($p_dir, -1) == '/')
				{
					$t_line = substr($p_dir, 0, -1) . $t_line;
				}
				else
				{
					$t_line = $p_dir . $t_line;
				}

				$this->v_data_array = array();
				$this->recursive_ftpn_list($t_line);
				
				$t_mode = @ftp_chmod($this->v_connect_id, 0777, trim($t_line));
				for($i = 0; $i < count($this->v_data_array); $i++)
				{
					$t_mode = @ftp_chmod($this->v_connect_id, 0777, trim($this->v_data_array[$i]));
				}
			}
		}

		$_SESSION['FTP_PATH'] = $p_dir;
		
		fclose($fp);
	}


	function recursive_ftpn_list($p_dir, $p_directories = true, $p_files = true, $p_exclude = array('.htaccess', '.', '..'))
	{
		$t_list_array = ftp_nlist($this->v_connect_id, $p_dir);
		for($i = 0; $i < count($t_list_array); $i++)
		{
			if(strrchr($t_list_array[$i], '/') !== false)
			{
				$t_name = substr(strrchr($t_list_array[$i], '/'), 1);
			}
			else
			{
				$t_name = $t_list_array[$i];
			}
			if(!in_array($t_name, $p_exclude))
			{
				if($this->isdir($t_list_array[$i]) && $p_directories)
				{
					$this->v_data_array[] = $t_list_array[$i];
					$this->recursive_ftpn_list($t_list_array[$i], $p_directories, $p_files, $p_exclude);
				}

				if(!$this->isdir($t_list_array[$i]) && $p_files)
				{
					$this->v_data_array[] = $t_list_array[$i];
				}
			}
		}

		return $this->v_data_array;
	}


	function chmod_444($p_dir)
	{
		@chmod(DIR_FS_CATALOG . 'admin/includes/configure.org.php', 0444);
		@chmod(DIR_FS_CATALOG . 'admin/includes/configure.php', 0444);
		@chmod(DIR_FS_CATALOG . 'includes/configure.org.php', 0444);
		@chmod(DIR_FS_CATALOG . 'includes/configure.php', 0444);

		$t_mode = @ftp_chmod($this->v_connect_id, 0444, $p_dir . '/admin/includes/configure.org.php');
		$t_mode = @ftp_chmod($this->v_connect_id, 0444, $p_dir . '/admin/includes/configure.php');
		$t_mode = @ftp_chmod($this->v_connect_id, 0444, $p_dir . '/includes/configure.org.php');
		$t_mode = @ftp_chmod($this->v_connect_id, 0444, $p_dir . '/includes/configure.php');
	}


	function check_chmod()
	{
		$this->wrong_chmod_array = SecurityCheck::getWrongPermittedInstallerFiles();

		return $this->wrong_chmod_array;
	}


	function write_robots_file($p_dir, $p_relative_shop_path, $p_https_server = false)
	{
		$t_file_exists = @file_exists(str_replace($p_relative_shop_path, '/', DIR_FS_CATALOG) . 'robots.txt');

		if($t_file_exists === false)
		{
			$t_file = DIR_FS_CATALOG . 'gambio_installer/templates/robots.txt.tpl';

			$t_lines = file($t_file);

			$t_robots_content = '';
			foreach($t_lines AS $t_line)
			{
				$t_robots_content .= str_replace('{PATH}', $p_relative_shop_path, $t_line);
			}

			// check SSL
			if($p_https_server)
			{
				// check if ssl is in a subdirectory
				$t_http_parsed = parse_url($p_https_server);
				if(isset($t_http_parsed['path']))
				{
					$t_robots_content .= "\t\n\t\n";
					$t_path = substr($t_http_parsed['path'], 1);
					if(substr($t_path, -1, 1) != '/')
					{
						$t_path = $t_path.'/';
					}
					// again for ssl
					foreach($t_lines AS $t_line)
					{
						$t_robots_content .= str_replace('{PATH}', $p_shop_path.$t_path, $t_line);
					}
				}
			}
		}

		$t_handle = fopen(DIR_FS_CATALOG . 'cache/temp_robots.txt', 'w+');
		fwrite($t_handle, $t_robots_content);
		rewind($t_handle);

		if($p_relative_shop_path != '/' && strpos($p_dir, substr($p_relative_shop_path, 0, -1)) !== false && substr($p_dir, strlen(substr($p_relative_shop_path, 0, -1)) * -1) === substr($p_relative_shop_path, 0, -1))
		{
			$t_dir =  substr($p_dir, 0, strlen(substr($p_relative_shop_path, 0, -1)) * -1);

			$t_filesize = @ftp_size($this->v_connect_id, $t_dir . '/robots.txt');
			if((int)$t_filesize == -1)
			{
				@ftp_delete($this->v_connect_id, $t_dir . '/robots.txt');
				$t_success = ftp_fput($this->v_connect_id, $t_dir . '/robots.txt', $t_handle, FTP_ASCII, 0);
				$t_mode = @ftp_chmod($this->v_connect_id, 0644, $t_dir . '/robots.txt');
			}

			fclose($t_handle);

			if($t_success)
			{
				@unlink(DIR_FS_CATALOG . 'cache/temp_robots.txt');
				return true;
			}
		}
		
		return false;
	}


	/**
	 * returns path to shop if found else returns $p_dir
	 *
	 * @param string $p_dir
	 *
	 * @return string
	 */
	function find_shop_dir($p_dir)
	{
		if($this->is_shop($p_dir))
		{
			return $p_dir;
		}
		
		$dirContent = $this->get_directories($p_dir, true);

		// __FILE__ = .../gambio_installer/includes/FTPManager.inc.php
		$pathArray = preg_split('![/\\\]!', dirname(dirname(dirname(__FILE__)))); // PHP 5.2 compatibility
		$pathArray = array_reverse($pathArray);

		$shopPath = '';

		foreach($pathArray as $pathDir)
		{
			$shopPath = $pathDir . '/' . $shopPath;

			foreach($dirContent as $dir)
			{
				if($pathDir === $dir)
				{
					$shopDir = '/' . substr($shopPath, 0, -1);
					if($this->is_shop($shopDir))
					{
						return $shopDir;
					}
				}
			}
		}

		return $p_dir;
	}
}