<?php
/* --------------------------------------------------------------
   FileLog.php 2011-09-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class FileLog
{
	var $v_log_active = true;

	var $v_file_base 	= '';
	var $v_file_suffix 	= '';
	var $v_max_filesize = 0;

	function FileLog($p_file_base, $p_log_active=true)
	{
		$this->v_file_base = $p_file_base;
		$this->v_file_suffix = $this->get_secure_token();
		
		if(defined('SQL_LOG_MAX_FILESIZE') && (double)SQL_LOG_MAX_FILESIZE > 0)
		{
			$this->v_max_filesize = (double)SQL_LOG_MAX_FILESIZE * 1024 * 1024;
		}
		else
		{
			$this->v_max_filesize = 1 * 1024 * 1024;
		}
  	}

  	function get_file_path()
  	{
  		$t_path = 'logfiles/'.$this->v_file_base.'-'.$this->v_file_suffix.'.log';
  		return $t_path;
  	}

	function write($p_string)
	{
		if($this->v_log_active == false)
		{
			# log deactivated. cancel write.
			return true;
		}

		if((file_exists(DIR_FS_CATALOG . $this->get_file_path()) == false && 
				is_writeable(DIR_FS_CATALOG . 'logfiles/') == false)
			|| 
			(file_exists(DIR_FS_CATALOG . $this->get_file_path()) == true && 
				is_writeable(DIR_FS_CATALOG . $this->get_file_path()) == false))
		{
			return false;
		}

		if(@filesize(DIR_FS_CATALOG . $this->get_file_path()) >= $this->v_max_filesize && is_writeable(DIR_FS_CATALOG . 'logfiles/') == true)
		{
			$t_file_content = file_get_contents(DIR_FS_CATALOG . $this->get_file_path());			
			$t_gz_file_content = gzencode($t_file_content, 9);
			$t_gz_filename = $this->v_file_base . '-' . $this->v_file_suffix . '_' . date('Ymd_His') . '.log.gz';
			$fp = fopen(DIR_FS_CATALOG . 'logfiles/' . $t_gz_filename, 'w+');
			fwrite($fp, $t_gz_file_content);
			fclose($fp);
			unlink(DIR_FS_CATALOG . $this->get_file_path());
		}
		
		$fp = fopen(DIR_FS_CATALOG . $this->get_file_path(), 'a+');
		fwrite($fp, $p_string);
		fclose($fp);

		return true;
	}

	function set_log_active($p_status)
	{
		$this->v_log_active = $p_status;
	}

	public static function get_secure_token()
	{
		static $t_secure_token;
		
		if($t_secure_token === null && is_dir(DIR_FS_CATALOG . 'media'))
		{
			 $t_dh = opendir(DIR_FS_CATALOG . 'media');
			 if($t_dh !== false)
			 {
				while(($t_file = readdir($t_dh)) !== false)
				{
					// search for secure token file
					if(strpos($t_file, 'secure_token_') !== false)
					{
						$t_secure_token = str_replace('secure_token_', '', $t_file);
						break;
					}
				}

				if($t_secure_token === null)
				{
					$t_secure_token = md5(mt_rand());
					
					$fp = fopen(DIR_FS_CATALOG . 'media/secure_token_' . $t_secure_token, 'w');
					fwrite($fp, '.');
					fclose($fp);
				}
			 }
		}
		
		return $t_secure_token;
	}
}
?>