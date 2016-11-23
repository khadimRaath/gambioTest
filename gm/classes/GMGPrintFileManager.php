<?php
/* --------------------------------------------------------------
   GMGPrintFileManager.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class GMGPrintFileManager_ORIGIN
{
	function __construct()
	{
		//
	}
	
	// important, that $p_file_permissions is octal!!!
	function upload($p_files_id, $p_target_dir, $p_allowed_extensions = array(), $p_new_filename = '', $p_file_permissions = 0777, $p_minimum_filesize = 0, $p_maximum_filesize = 0)
	{
		$t_success = false;
		
		if(file_exists($_FILES[$p_files_id]['tmp_name']) && (int)$_FILES[$p_files_id]['error'] == 0)
		{
			$t_new_filename = '';
			$t_allowed = false;
			if(!empty($p_new_filename))
			{
				$t_new_filename = basename($p_new_filename);
			}
			else
			{
				$t_new_filename = basename($_FILES[$p_files_id]['name']);
			}
			
			$t_allowed = $this->no_spam();
			if(!$t_allowed)
			{
				$t_success = 'spam';
			}	
			
			$t_allowed = $this->check_extension($_FILES[$p_files_id]['name'], $p_allowed_extensions);
			if(!$t_allowed && $t_success === false)
			{
				$t_success = 'wrong_type';
			}		
			
			$t_allowed = $this->check_minimum_filesize($_FILES[$p_files_id]['size'], $p_minimum_filesize);
			if(!$t_allowed && $t_success === false)
			{
				$t_success = 'filesize_below_limit';
			}

			
			$t_allowed = $this->check_maximum_filesize($_FILES[$p_files_id]['size'], $p_maximum_filesize);
			if(!$t_allowed && $t_success === false)
			{
				$t_success = 'filesize_limit_exceeded';
			}
			
			if($t_allowed && $t_success === false)
			{
				move_uploaded_file($_FILES[$p_files_id]['tmp_name'], $p_target_dir . $t_new_filename);
					
				if(is_file($p_target_dir . $t_new_filename))
				{
					$this->update_file_permissions($p_target_dir . $t_new_filename, $p_file_permissions);
					$t_success = true;
				}	
				else
				{
					$t_success = 'no_permission_to_save_file';
				}	
			}	
		}	
		elseif((int)$_FILES[$p_files_id]['error'] == 3)
		{
			$t_success = 'only_partially_uploaded';
		}
		else
		{
			$t_success = 'no_file';
		}
		
		return $t_success;
	}
	
	function check_extension($p_filename, $p_extensions)
	{
		$t_allowed = false;
		
		for($i = 0; $i < count($p_extensions); $i++)
		{
			if(strtolower($p_extensions[$i]) == substr(strrchr(strtolower($p_filename), '.'), 1))
			{
				$t_allowed = true;
			}
		}
		
		return $t_allowed;
	}
	
	// $p_size in bytes
	function check_minimum_filesize($p_size, $p_limit)
	{
		$t_success = true;
		
		// in megabyte
		$c_limit = (double)$p_limit;
		
		// in bytes
		$c_limit *= 1024 * 1024;

		if($c_limit > 0 && (int)$p_size < $c_limit)
		{
			$t_success = false;
		}
		
		return $t_success;
	}
	
	// $p_size in bytes, $p_limit in megabytes
	function check_maximum_filesize($p_size, $p_limit)
	{
		$t_success = true;
		
		// in megabyte
		$c_limit = (double)$p_limit;
		
		// in bytes
		$c_limit *= 1024 * 1024;

		if($c_limit > 0 && (int)$p_size > $c_limit)
		{
			$t_success = false;
		}
		
		return $t_success;
	}
	
	function no_spam()
	{
		$t_uploads_per_ip = (int)gm_get_conf('GM_GPRINT_UPLOADS_PER_IP');
		$t_no_spam = true;
		
		if($t_uploads_per_ip > 0)
		{
			$t_interval = (int)gm_get_conf('GM_GPRINT_UPLOADS_PER_IP_INTERVAL');
			$t_interval *= 60;
			$count_files = 0;
			
			
			if($_SERVER['HTTP_X_FORWARDED_FOR'])
			{
				$t_customers_ip_hash = md5($_SERVER['HTTP_X_FORWARDED_FOR']);
			}
			else
			{
				$t_customers_ip_hash = md5($_SERVER['REMOTE_ADDR']);
			}
			
			$t_get_filenames = xtc_db_query("SELECT encrypted_filename
												FROM " . TABLE_GM_GPRINT_UPLOADS . "
												WHERE 
													UNIX_TIMESTAMP(datetime) > (UNIX_TIMESTAMP(NOW()) - " . $t_interval . ")
													AND ip_hash = '" . $t_customers_ip_hash . "'");
			while($t_files = xtc_db_fetch_array($t_get_filenames))
			{
				if(is_file(DIR_FS_CATALOG . 'gm/customers_uploads/gprint/' . basename($t_files['encrypted_filename'])))
				{
					$count_files++;
				}
			}
			
			if($count_files >= $t_uploads_per_ip)
			{
				$t_no_spam = false;
			}
		}
		
		return $t_no_spam;
	}
	
	function update_file_permissions($p_file, $p_permissions)
	{
		@chmod($p_file, $p_permissions);
	}
	
	function rename_file($p_dir, $p_old_filename, $p_new_filename)
	{		
		$t_success = false;
		
		if(is_file($p_dir . $p_old_filename))
		{
			@rename($p_dir . $p_old_filename, $p_dir . $p_new_filename);
			
			if(is_file($p_dir . $p_new_filename))
			{
				$t_success = true;
			}
		}	
		
		return $t_success;
	}
	
	function delete_file($p_file)
	{
		$t_success = false;
		
		if(is_file($p_file))
		{
			@unlink($p_file);
			
			if(!is_file($p_file))
			{
				$t_success = true;
			}
		}	
		
		return $t_success;
	}
	
	function copy_file($p_source_filename, $p_new_filename, $p_source_dir, $p_new_dir)
	{
		$c_source_filename = basename($p_source_filename);
		$c_new_filename = basename($p_new_filename);
		
		$t_success = false;
		
		if(is_file($p_source_dir . $c_source_filename) && is_dir($p_new_dir) && !empty($c_new_filename))
		{
			@copy($p_source_dir . $p_source_filename, $p_new_dir . $c_new_filename);
			
			if(is_file($p_new_dir . $c_new_filename))
			{
				$t_success = true;
			}
		}	
		
		return $t_success;
	}
	
	function get_image_size($p_file)
	{
		$t_image_size = @getimagesize($p_file);
		
		return $t_image_size;
	}
	
	function get_next_filename_id()
	{
		$t_next_filename_id = '';
		
		$t_get_table_data = xtc_db_query("SHOW TABLE STATUS LIKE '" . TABLE_GM_GPRINT_ELEMENTS . "'");
		if(xtc_db_num_rows($t_get_table_data) == 1)
		{
			$t_table_data = xtc_db_fetch_array($t_get_table_data);
			$t_next_filename_id = $t_table_data['Auto_increment'];
		}
	
		return $t_next_filename_id;
	}	
	
	function get_error($p_files_id)
	{
		return $_FILES[$p_files_id]['error'];
	}
}
MainFactory::load_origin_class('GMGPrintFileManager');