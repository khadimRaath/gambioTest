<?php
/* --------------------------------------------------------------
   FTPManager.inc.php 2016-07-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FTPManager
{
	static protected $instance = null;
	public $connect_id = null;
	public $error = '';
	public $data_array = array();
	public $wrong_chmod_array = array();
	protected $host;
	protected $user;
	protected $password;
	protected $pasv;
	
	static public function get_instance($p_connect = false, $p_host = '', $p_user = '', $p_password = '', $p_pasv = true)
	{
		$t_is_same_connection = isset(self::$instance->host)
								&& isset(self::$instance->user)
								&& isset(self::$instance->password)
								&& isset(self::$instance->pasv)
								&& $p_host == self::$instance->host
								&& $p_user == self::$instance->user
								&& $p_password == self::$instance->password
								&& $p_pasv == self::$instance->pasv;
		
		if(self::$instance === null || $t_is_same_connection == false)
		{
			self::$instance = new self($p_connect, $p_host, $p_user, $p_password, $p_pasv);
		}
		
		return self::$instance;
	}
	
	protected function __construct($p_connect, $p_host, $p_user, $p_password, $p_pasv = true)
	{
		$this->host = $p_host;
		$this->user = $p_user;
		$this->password = $p_password;
		$this->pasv = $p_pasv;
		
		if($p_connect == true)
		{
			$this->connect();
		}
	}


	function connect()
	{
		if(function_exists('ftp_connect'))
		{
			$t_connect_id = @ftp_connect($this->host) or $this->error .= sprintf(ERROR_FTP_CONNECTION, $this->host);
			if($t_connect_id !== false)
			{
				$t_login = @ftp_login($t_connect_id, $this->user, $this->password) or $this->error .= sprintf(ERROR_FTP_DATA, $this->user);
				ftp_pasv($t_connect_id, $this->pasv);
	
				$this->connect_id = $t_connect_id;
				
				if($t_login == false)
				{
					debug_notice(sprintf(ERROR_FTP_DATA, $this->user));
				}
				else
				{
					debug_notice('FTP connection successful');

					$this->check_ftp_listing();
				}
			}
			else
			{
				debug_notice(sprintf(ERROR_FTP_CONNECTION, $this->host));
			}			
		}
		else
		{
			debug_notice('The server does not support FTP');
			$this->error .= ERROR_FTP_NOT_INSTALLED;
		}
	}
	

	function disconnect()
	{
		ftp_close($this->connect_id);
		$this->connect_id = null;
	}
	
	
	function check_ftp_listing()
	{
		if(ftp_nlist($this->connect_id, '/') === false)
		{
			debug_notice('The server does not support the FTP-function ftp_nlist');
			$this->error = ERROR_FTP_NO_LISTING;
		}
	}


	function isdir($p_dir)
	{
		if(function_exists('ftp_size'))
		{
			$t_size = @ftp_size($this->connect_id, $p_dir);
		}

		if(!isset($t_size) || $t_size === -1 || $t_size === 0)
		{
			if(@ftp_chdir($this->connect_id, $p_dir))
			{
				@ftp_cdup($this->connect_id);
				return true;
			}
		}
	}


	function get_directories($p_dir, $p_no_parent_dirs = false)
	{
		$t_parent = '';
		if($p_dir !== '/')
		{
			$t_parent = $p_dir;
		}
		elseif(array_key_exists('dir', $_POST))
		{
			$t_parent = $_POST['dir'];
		}
		$t_parent .= '/';

		$t_list_array = $this->get_dir_content($p_dir);

		$t_final_list = array();

		$list_count = count($t_list_array);
		for($i = 0; $i < $list_count; $i++)
		{
			if(substr($t_list_array[$i], -3) === '/.')
			{
				continue;
			}

			if(strpos($t_list_array[$i], '/') !== 0)
			{
				$t_list_array[$i] = $t_parent . $t_list_array[$i];
			}

			if(substr($t_list_array[$i], -3) !== '/..' && substr($t_list_array[$i], -2) !== '/.' && $this->isdir($t_list_array[$i]))
			{
				if($p_no_parent_dirs)
				{
					$t_dir = substr(strrchr($t_list_array[$i], '/'), 1);
				}
				else
				{
					$t_dir = $t_list_array[$i];
				}

				if(strlen($t_dir) > 1 && substr($t_dir, 0, 2) === '//')
				{
					$t_dir = substr($t_dir, 1);
				}

				$t_final_list[] = $t_dir;
			}
		}

		sort($t_final_list);

		return $t_final_list;
	}


	function get_dir_content($p_dir)
	{
		return @ftp_nlist($this->connect_id, $p_dir);
	}


	function quit()
	{
		$t_success = false;

		if($this->connect_id !== false && $this->connect_id !== null)
		{
			$t_success = ftp_quit($this->connect_id);
		}

		return $t_success;
	}


	function is_shop($p_dir)
	{
		$t_found_shop = false;

		if($this->connect_id !== false)
		{
			$t_includes_dir_content_array = $this->get_dir_content($p_dir . '/includes');
			$file_count = count($t_includes_dir_content_array);
			for($i = 0; $i < $file_count; $i++)
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


	function chmod_777($p_dir, $p_chmod_array = array())
	{
		foreach($p_chmod_array as $t_line) {
			$t_line['PATH'] = trim($t_line['PATH']);
			if(strlen($t_line['PATH']) > 0)
			{
				$t_line['PATH'] = str_replace('\\', '/', $t_line['PATH']);
				if(substr($t_line['PATH'], 0, 1) != '/')
				{
					$t_line['PATH'] = '/' . $t_line['PATH'];
				}

				if(substr($p_dir, -1) == '/')
				{
					$t_line['PATH'] = substr($p_dir, 0, -1) . $t_line['PATH'];
				}
				else
				{
					$t_line['PATH'] = $p_dir . $t_line['PATH'];
				}

				if ($t_line['IS_DIR'])
				{
					$this->data_array = array();
					$this->recursive_ftpn_list($t_line['PATH'], true, true, array('.htaccess', '.', '..'));

					$t_mode = @ftp_chmod($this->connect_id, 0777, trim($t_line['PATH']));
					foreach($this->data_array as $t_file)
					{
						$t_mode = @ftp_chmod($this->connect_id, 0777, trim($t_file));
					}
				}
				else
				{
					$t_mode = @ftp_chmod($this->connect_id, 0777, trim($t_line['PATH']));
				}
			}
		}
	}
	
	function put_file($p_dir, $p_source_file_handle , $p_target_file_path)
	{
		$t_success = false;
		$t_target_file_path = trim($p_target_file_path);
		if(strlen($t_target_file_path) > 0)
		{
			$t_target_file_path = str_replace('\\', '/', $t_target_file_path);
			if(substr($t_target_file_path, 0, 1) != '/')
			{
				$t_target_file_path = '/' . $t_target_file_path;
			}
			if(substr($p_dir, -1) == '/')
			{
				$t_target_file_path = substr($p_dir, 0, -1) . $t_target_file_path;
			}
			else
			{
				$t_target_file_path = $p_dir . $t_target_file_path;
			}

			$t_success = ftp_fput($this->connect_id, $t_target_file_path, $p_source_file_handle, FTP_ASCII);
		}
		
		return $t_success;
	}
	
	function delete_file($p_dir, $p_file_path)
	{
		$t_success = false;
		$t_file = trim($p_file_path);
		if(strlen($t_file) > 0)
		{
			$t_file = str_replace('\\', '/', $t_file);
			if(substr($t_file, 0, 1) != '/')
			{
				$t_file = '/' . $t_file;
			}
			if(substr($p_dir, -1) == '/')
			{
				$t_file = substr($p_dir, 0, -1) . $t_file;
			}
			else
			{
				$t_file = $p_dir . $t_file;
			}
			$t_success = ftp_delete($this->connect_id, $t_file);
		}

		return $t_success;
	}
	
	function delete_files($p_dir, $p_file_array = array())
	{
		$t_dirs_to_delete = array();

		foreach($p_file_array as $t_file)
		{
			if(is_string($t_file))
			{
				$t_file = trim($t_file);
				if(strlen($t_file) > 0)
				{
					$t_file = str_replace('\\', '/', $t_file);
					if(substr($t_file, 0, 1) != '/')
					{
						$t_file = '/' . $t_file;
					}

					$t_recursive = false;
					if(substr($t_file, -2) == '/*')
					{
						$t_recursive = true;
						$t_file = substr($t_file, 0, -2);
					}

					if(substr($p_dir, -1) == '/')
					{
						$t_file = substr($p_dir, 0, -1) . $t_file;
					}
					else
					{
						$t_file = $p_dir . $t_file;
					}

					if($t_recursive && $this->isdir($t_file))
					{
						@ftp_chdir($this->connect_id, $t_file);

						$this->data_array = array();
						$this->recursive_ftpn_list($t_file);
						sort($this->data_array);
						$this->data_array = array_reverse($this->data_array);
						$t_mode = true;

						foreach($this->data_array as $t_child_file)
						{
							if($this->isdir($t_child_file))
							{
								@ftp_cdup($this->connect_id);
								$t_mode = @ftp_rmdir($this->connect_id, $t_child_file);
							}
							else
							{
								$t_mode &= @ftp_delete($this->connect_id, trim($t_child_file));
							}
						}

						if($t_mode)
						{
							$t_mode = @ftp_rmdir($this->connect_id, $t_file);
						}

						@ftp_cdup($this->connect_id);
					}
					elseif($this->isdir($t_file))
					{
						@ftp_chdir($this->connect_id, $t_file);
						$t_delete_success = @ftp_rmdir($this->connect_id, $t_file);

						if($t_delete_success === false)
						{
							$t_dirs_to_delete[] = $t_file;
						}

						@ftp_cdup($this->connect_id);
					}
					else
					{
						@ftp_delete($this->connect_id, $t_file);
					}
				}
			}
		}

		rsort($t_dirs_to_delete);

		foreach($t_dirs_to_delete as $t_dir)
		{
			@ftp_rmdir($this->connect_id, $t_dir);
		}
	}

	function recursive_ftpn_list($p_dir, $p_directories = true, $p_files = true, $p_exclude = array('.', '..'))
	{
		$t_parent = '';
		if(isset($_POST['dir']))
		{
			$t_parent = $_POST['dir'];
		}
		$t_parent .= '/';
		
		if(strpos($p_dir, '/') !== 0)
		{
			$p_dir = $t_parent . $p_dir;
		}
		
		$t_modifier = '';
		if(strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
		{
			$t_modifier = '-a ';
		}
		$t_list_array = $this->get_dir_content($t_modifier . $p_dir);
		foreach($t_list_array as $t_file)
		{
			if(strpos($t_file, '/') !== 0)
			{
				$t_file = $p_dir . '/' . $t_file;
			}

			if(strrchr($t_file, '/') !== false)
			{
				$t_name = substr(strrchr($t_file, '/'), 1);
			}
			else
			{
				$t_name = $t_file;
			}
			if(!in_array($t_name, $p_exclude))
			{
				if($this->isdir($t_file) && $p_directories)
				{
					$this->data_array[] = $t_file;
					$this->recursive_ftpn_list($t_file, $p_directories, $p_files, $p_exclude);
				}

				if(!$this->isdir($t_file) && $p_files)
				{
					$this->data_array[] = $t_file;
				}
			}
		}

		return $this->data_array;
	}


	function chmod_444($p_dir)
	{
		@chmod(DIR_FS_CATALOG . 'admin/includes/configure.org.php', 0444);
		@chmod(DIR_FS_CATALOG . 'admin/includes/configure.php', 0444);
		@chmod(DIR_FS_CATALOG . 'includes/configure.org.php', 0444);
		@chmod(DIR_FS_CATALOG . 'includes/configure.php', 0444);

		$t_mode = @ftp_chmod($this->connect_id, 0444, $p_dir . '/admin/includes/configure.org.php');
		$t_mode = @ftp_chmod($this->connect_id, 0444, $p_dir . '/admin/includes/configure.php');
		$t_mode = @ftp_chmod($this->connect_id, 0444, $p_dir . '/includes/configure.org.php');
		$t_mode = @ftp_chmod($this->connect_id, 0444, $p_dir . '/includes/configure.php');
	}


	function check_chmod($p_chmod_array = array())
	{
		$this->wrong_chmod_array = array();

		foreach($p_chmod_array as $t_line) {
			$t_line = trim($t_line);
			if(strlen($t_line) > 0)
			{
				$t_line = str_replace('\\', '/', $t_line);
				if(substr($t_line, 0, 1) == '/')
				{
					$t_line = substr($t_line, 1);
				}

				@chmod(DIR_FS_CATALOG . $t_line, 0777);
				if(@!is_writeable(DIR_FS_CATALOG . $t_line) && @file_exists(DIR_FS_CATALOG . $t_line))
				{
					$this->wrong_chmod_array[] = DIR_FS_CATALOG . $t_line;
				}
			}
		}

		return $this->wrong_chmod_array;
	}


	function recursive_check_chmod($p_dir, $p_exclude = array('.htaccess', '.', '..'))
	{
		if(substr($p_dir, -1) != '/')
		{
			$p_dir .= '/';
		}

		if(is_dir($p_dir))
		{
			if($t_dh = opendir($p_dir))
			{
				while(($t_file = readdir($t_dh)) !== false)
				{
					if(in_array($t_file, $p_exclude) === false)
					{
						@chmod($p_dir . $t_file, 0777);
						if(is_writeable($p_dir . $t_file) === false)
						{
							$this->wrong_chmod_array[] = $p_dir . $t_file;
						}
						
						if(is_dir($p_dir . $t_file))
						{
							$this->recursive_check_chmod($p_dir . $t_file, $p_exclude);
						}
					}
				}
				closedir($t_dh);
			}
		}

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

			$t_filesize = @ftp_size($this->connect_id, $t_dir . '/robots.txt');
			if((int)$t_filesize == -1)
			{
				@ftp_delete($this->connect_id, $t_dir . '/robots.txt');
				$t_success = ftp_fput($this->connect_id, $t_dir . '/robots.txt', $t_handle, FTP_ASCII, 0);
				$t_mode = @ftp_chmod($this->connect_id, 0644, $t_dir . '/robots.txt');
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
	
	public function move($p_dir, $p_file_array = array())
	{
		$t_success = true;
		
		foreach($p_file_array as $t_path_array)
		{
			if(isset($t_path_array['old']) && $t_path_array['new'])
			{
				$t_path_array['old'] = trim($t_path_array['old']);
				$t_path_array['new'] = trim($t_path_array['new']);
				
				if(strlen($t_path_array['old']) > 0 && strlen($t_path_array['new']))
				{
					$t_path_array['old'] = str_replace('\\', '/', $t_path_array['old']);
					if(substr($t_path_array['old'], 0, 1) != '/')
					{
						$t_path_array['old'] = '/' . $t_path_array['old'];
					}

					if(substr($p_dir, -1) == '/')
					{
						$t_path_array['old'] = substr($p_dir, 0, -1) . $t_path_array['old'];
					}
					else
					{
						$t_path_array['old'] = $p_dir . $t_path_array['old'];
					}

					$t_path_array['new'] = str_replace('\\', '/', $t_path_array['new']);
					if(substr($t_path_array['new'], 0, 1) != '/')
					{
						$t_path_array['new'] = '/' . $t_path_array['new'];
					}

					if(substr($p_dir, -1) == '/')
					{
						$t_path_array['new'] = substr($p_dir, 0, -1) . $t_path_array['new'];
					}
					else
					{
						$t_path_array['new'] = $p_dir . $t_path_array['new'];
					}

					// Windows file system cannot differentiate between upper- und lowercase -> do intermediate step
					if($t_path_array['old'] === $t_path_array['new'])
					{
						$t_temp_target_path = $t_path_array['new'] . '_temp';
						$this->ftp_move($p_dir, $t_path_array['old'], $t_temp_target_path);
						$t_path_array['old'] = $t_temp_target_path;
					}

					$t_success = $this->ftp_move($p_dir, $t_path_array['old'], $t_path_array['new']);
				}
			}
		}
		clearstatcache();
		return $t_success;
	}
	
	public function file_exists($p_path)
	{
		if($this->isdir($p_path))
		{
			return true;
		}
		
		$t_path = $p_path;
		
		if(substr($p_path, -1) == '/')
		{
			$t_path = substr($t_path, 0, -1);
		}
		
		$t_dir = dirname($t_path);
		
		if(empty($t_dir) === false)
		{
			$t_list_array = $this->get_dir_content('-a ' . $t_dir);
			
			foreach($t_list_array as $t_file)
			{
				if(basename($t_file) === basename($p_path))
				{
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function create_directories($p_dir, $p_path)
	{
		$t_success = true;
		
		ftp_chdir($this->connect_id, $p_dir);
		
		$t_path = $p_path;
		if(strpos($p_path, $p_dir) === 0)
		{
			$t_path = substr($p_path, strlen($p_dir));
		}
				
		$t_dirs_array = explode('/', $t_path);
		
		foreach($t_dirs_array as $t_folder)
		{
			if($t_folder != '' && $this->isdir($t_folder) === false)
			{
				$t_new_dir = ftp_mkdir($this->connect_id, $t_folder);
				
				if($t_new_dir !== false)
				{
					ftp_chdir($this->connect_id, $t_new_dir);
					@ftp_chmod($this->connect_id, 0755, $t_new_dir);
					$t_success &= true;
				}
				else
				{
					$t_success = false;
				}
			}
		}
				
		ftp_chdir($this->connect_id, $p_dir);
		
		return $t_success;
	}
	
	// Moving files or folders if target does not exist. Otherwise source will be kept.
	public function ftp_move($p_dir, $p_source_path, $p_target_path)
	{
		$t_success = true;
		
		ftp_chdir($this->connect_id, $p_dir);
		
		$t_rename_success = @ftp_rename($this->connect_id, $p_source_path, $p_target_path);
					
		if($t_rename_success === false)
		{
			if($this->file_exists(dirname($p_target_path)) === false)
			{
				$t_success &= $this->create_directories($p_dir, dirname($p_target_path));

				if($t_success)
				{
					$t_success = ftp_rename($this->connect_id, $p_source_path, $p_target_path);
				}
			}
			elseif($this->isdir($p_source_path))
			{
				$t_list_array = $this->get_dir_content('-a ' . $p_source_path);
				$t_delete_source = true;
				
				foreach($t_list_array as $t_file)
				{
					$t_file = basename($t_file);

					if($t_file != '.' && $t_file != '..')
					{
						if($this->isdir($p_target_path . '/' . $t_file))
						{
							$t_success &= $this->ftp_move($p_dir, $p_source_path . '/' . $t_file, $p_target_path . '/' . $t_file);
						}
						elseif($this->file_exists($p_target_path . '/' . $t_file) == false)
						{
							$t_success &= ftp_rename($this->connect_id, $p_source_path . '/' . $t_file, $p_target_path . '/' . $t_file);
						}
						else
						{
							$t_delete_source = false;
						}
					}
				}

				if($t_success && $t_delete_source)
				{
					ftp_rmdir($this->connect_id, $p_source_path);
				}
			}
		}
		clearstatcache();
		return $t_success;
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

		$pathArray = preg_split('![/\\\]!', substr(DIR_FS_CATALOG, 0, -1));
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