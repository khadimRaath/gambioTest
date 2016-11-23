<?php
/* --------------------------------------------------------------
   ShowLogs.php 2014-11-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

class ShowLogs_ORIGIN
{
    /*
     * path to the logfiles
     */
    var $v_path = '';
	var $v_log_prefix_array = array();
    
    /*
     * constructor
     */
    function __construct()
    {
		$this->v_path = DIR_FS_CATALOG . 'logfiles/';
    }

    /*
     * determined number of pages of logfiles
     *
     * @param string $p_file Filename with path
     * @param int $p_num_value Offset value
     * @return array Array with page numbers and ID
     */
    function get_page_number($p_file, $p_num_value = 150, $p_full_log = false)
    {
        $t_result_array[0]['id'] = 1;
        $t_result_array[0]['text'] = 1;
		
        $t_is_file = is_file($this->v_path.$p_file);
        if(!$t_is_file) {
            return $t_result_array;
        }
		
		if($p_full_log)
		{
			return $t_result_array;
		}

        $p_filesize = filesize($this->v_path.$p_file);
        if(!$p_filesize) {
            return $t_result_array;
        }
        
        $t_offset = ($p_num_value * 120);
        $t_page_number = ceil(($p_filesize / $t_offset));

        $t_result_array = array();
        $b = $t_page_number;
        for($i = 1; $i <= $t_page_number; $i++) {
            $t_result_array[$i]['id'] = $b;
            $t_result_array[$i]['text'] = $i;
            $b--;
        }
        return ($t_result_array);
    }

    /*
     * read part of the selected logfile
     *
     * @param string $p_file Filename with path
     * @param int $p_page Page number
     * @param int $p_num_value Offset number
     * @return string Part of the logfile
     */
    function get_log($p_file, $p_page, $p_num_value = 150, $p_full_log = false)
    {
		$t_buffer = '';

        $t_is_file = is_file($this->v_path.$p_file);
        if(!$t_is_file) {
            return $t_buffer;
        }
		
		$t_file = $p_file;
		$t_gz_content = '';
		
		if(strpos($t_file, '.log.gz') !== false || strpos($t_file, '.html.gz') !== false)
		{
			$t_handle = gzopen($this->v_path . $t_file, 'r');
			
			while(!feof($t_handle))
			{
			  $t_gz_content .= gzread($t_handle, 10000);			 			  
			}
			gzclose($t_handle);
				
			$t_file .= '.temp';
			
			$t_handle = fopen($this->v_path . $t_file, 'w+');
			fwrite($t_handle, $t_gz_content);
			fclose($t_handle);
			
		}

        $t_handle = fopen($this->v_path.$t_file, "r");
        if(!$t_handle)
		{
            return $t_buffer;
        }

		if($p_full_log == false)
		{
			$t_offset = (-$p_num_value * 120) * $p_page;
			$t_offset_plus = (($p_num_value+2) * 120);

			// go to end of file an return to t_offset
			fseek($t_handle, $t_offset, SEEK_END);
			// get the position
			$t_position = ftell($t_handle);
			// read the file
			$t_buffer = fread($t_handle, $t_offset_plus);

			// cut the first line if position not the first line
			if($t_position > 0)
			{
				$t_buffer = substr_wrapper($t_buffer, (strpos_wrapper($t_buffer, "\n")+1));
			}
			
			// cut the last line
			if(strrpos_wrapper($t_buffer, "\n") !== false)
			{
				$t_buffer = substr_wrapper($t_buffer, 0, strrpos_wrapper($t_buffer, "\n"));
			}
		}
		elseif(empty($t_gz_content) == false)
		{
			$t_buffer = $t_gz_content;
		}
		else
		{
			$t_buffer = file_get_contents($this->v_path . $p_file);
		}
        
        fclose($t_handle);

		// search for UTF-8 characters and convert to UTF-8
		if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $t_buffer) == false
				&& isset($_SESSION['language_charset'])
				&& strtolower(trim((string)$_SESSION['language_charset'])) == 'utf-8')
		{
			$t_buffer = utf8_encode($t_buffer);
		}
		
		// filter log message for security reasons
		if(strstr($p_file, '.html') == false)
		{
			$t_buffer = htmlspecialchars_wrapper($t_buffer);
		}
	    
 		// Image Processing
		if(strstr($t_file, 'debug-image_processing')) {
			$string = '#ERROR#';
			$t_buffer = str_replace($string, '<span style="color: red;">'.$string.'</span>', $t_buffer);
		}

		if(substr_wrapper($t_file, -5) == '.temp')
		{
			unlink($this->v_path.$t_file);
		}

        return $t_buffer;
    }

    /*
     * scan directory for logfiles
     *
     * @result array Array with logfiles
     */
    function scan_dir()
    {
        $t_result_array[0]['id'] = '';
        $t_result_array[0]['text'] = TEXT_INFO_NO_FILES;

        $t_is_dir = is_dir($this->v_path);
        if(!$t_is_dir) {
            return $t_result_array;
        }
        $t_dh = opendir($this->v_path);
        if(!$t_dh) {
            return $t_result_array;
        }

        $t_result_array = array();
        $i = 0;
        while (($t_file = readdir($t_dh)) !== false) {
            // just get *.log files
            if ($t_file != "." && $t_file != ".." && !is_dir($t_file) && (strstr($t_file, ".log") || strstr($t_file, ".html") && $t_file != 'index.html')) {
                $t_result_array[$i]['id'] = urlencode($t_file);
                $t_result_array[$i]['text'] = '['.date ("d.m.Y", filemtime($this->v_path.$t_file)).'] '.$t_file;
                $i++;
				clearstatcache();
            }
        }
		asort($t_result_array);
        return $t_result_array;
    }

    /*
     * check the filename
     *
     * @param string $p_file File from GET
     * @param array $p_file_array Array with logfiles from the directory
     * @return bool true:ok | false:if incorect filename
     */
    function check_file_name($p_file, $p_file_array)
    {
        foreach($p_file_array as $file) {
            if($file['id'] == $p_file) {
                return true;
            }
        }
        return false;
    }

	function clear_log($p_filename)
	{
		$t_file = $this->v_path.$p_filename;
		if(file_exists($t_file)) {
			$t_handle = fopen($t_file, 'w+');
			ftruncate($t_handle, 0);
			fclose($t_handle);
			
			$this->mark_as_read($_SESSION['customer_id'], $p_filename, true);
			
			echo TEXT_LOG_CLEAR_SUCCESS;
			return;
		}
		echo TEXT_LOG_NOT_EXISTS;
	}

	function delete_log($p_filename)
	{
		$t_file = $this->v_path.$p_filename;
		if(file_exists($t_file)) {
			
			$this->mark_as_read($_SESSION['customer_id'], $p_filename, true);
			
			unlink($t_file);
			
			if(strpos_wrapper($p_filename, 'security-') === 0)
			{
				$adminInfoboxControl = MainFactory::create_object('AdminInfoboxControl');
				$adminInfoboxControl->delete_by_identifier($p_filename);
			}
			
			echo TEXT_LOG_DELETED;
			return;
		}
		echo TEXT_LOG_NOT_DELETED;
	}
	
	function download_log($p_filename)
	{
		$t_file = $this->v_path . $p_filename;
		
		if(file_exists($t_file))
		{
			header('Content-Description: File Transfer');
			header("Content-Type: application/octet-stream");
			header('Content-Disposition: attachment; filename="' . basename($t_file) . '"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($t_file));
			ob_clean();
			flush();

			readfile($t_file);
		}	
		else
		{
			$this->v_output_buffer = 'Error: File does not exist!';
		}
		return;
	}
	
	function get_file_date($p_logfile)
	{
		$t_file_date = false;
		
		$t_logfile_path = $this->v_path . basename((string)$p_logfile);
		if(file_exists($t_logfile_path))
		{
			$t_file_date = filemtime($t_logfile_path);
		}		
		
		return $t_file_date;
	}
	
	function mark_as_read($p_customers_id, $p_logfile, $p_ignore_customers_id = false)
	{
		$c_customers_id = (int)$p_customers_id;
		$t_confirmation_date = $this->get_file_date($p_logfile);
		$c_logfile = xtc_db_input($p_logfile);
		
		if($t_confirmation_date !== false)
		{
			if($p_ignore_customers_id === true)
			{
				$t_sql = "SELECT	
								a.customers_id,
								c.customers_logs_history_id 
							FROM 
								" . TABLE_ADMIN_ACCESS . " a
							LEFT JOIN " . TABLE_CUSTOMERS_LOGS_HISTORY . " as c ON (a.customers_id = c.customers_id)
							WHERE
								a.customers_id != 'groups' AND
								a.show_logs = 1 AND
								(
									(c.logfile = '" . $c_logfile . "' AND 
									c.customers_logs_history_id IS NOT NULL)
										OR
									c.customers_logs_history_id IS NULL
								)";
				$t_result = xtc_db_query($t_sql, 'db_link', false);
				while($t_result_array = xtc_db_fetch_array($t_result))
				{
					if(!empty($t_result_array['customers_logs_history_id']))
					{
						$t_sql = "UPDATE " . TABLE_CUSTOMERS_LOGS_HISTORY . " 
									SET confirmation_date = '" . xtc_db_input($t_confirmation_date) . "'
									WHERE
										customers_id = '" . (int)$t_result_array['customers_id'] . "' AND
										logfile = '" . $c_logfile . "'";
					}
					else
					{
						$t_sql = "INSERT INTO " . TABLE_CUSTOMERS_LOGS_HISTORY . " 
									SET 
										confirmation_date = '" . $t_confirmation_date . "',
										customers_id = '" . (int)$t_result_array['customers_id'] . "',
										logfile = '" . $c_logfile . "'";
					}
					
					// mark as read
					$t_update_result = xtc_db_query($t_sql);
				}
			}
			else
			{
				$t_sql = "SELECT customers_logs_history_id 
							FROM " . TABLE_CUSTOMERS_LOGS_HISTORY . "
							WHERE
								customers_id = '" . $c_customers_id . "' AND
								logfile = '" . $c_logfile . "'";
				$t_result = xtc_db_query($t_sql, 'db_link', false);
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_sql = "UPDATE " . TABLE_CUSTOMERS_LOGS_HISTORY . " 
								SET confirmation_date = '" . xtc_db_input($t_confirmation_date) . "'
								WHERE
									customers_id = '" . $c_customers_id . "' AND
									logfile = '" . $c_logfile . "'";
				}
				else
				{
					$t_sql = "INSERT INTO " . TABLE_CUSTOMERS_LOGS_HISTORY . " 
								SET 
									confirmation_date = '" . $t_confirmation_date . "',
									customers_id = '" . $c_customers_id . "',
									logfile = '" . $c_logfile . "'";
				}

				// mark as read
				$t_result = xtc_db_query($t_sql);
			}
			
			
			// delete info box message
			$coo_admin_infobox_control = MainFactory::create_object('AdminInfoboxControl');
			$coo_admin_infobox_control->delete_by_identifier($c_logfile, $p_ignore_customers_id);
		}
	}
	
	function check_for_change($p_customers_id, $p_logfile)
	{
		$t_log_has_changed = true;
		
		$c_customers_id = (int)$p_customers_id;
		$t_confirmation_date = $this->get_file_date($p_logfile);
		
		if($t_confirmation_date !== false)
		{
			$c_logfile = xtc_db_input($p_logfile);
		
			$t_sql = "SELECT customers_logs_history_id 
						FROM " . TABLE_CUSTOMERS_LOGS_HISTORY . "
						WHERE
							customers_id = '" . $c_customers_id . "' AND
							logfile = '" . $c_logfile . "' AND
							confirmation_date = '" . xtc_db_input($t_confirmation_date) . "'";
			$t_result = xtc_db_query($t_sql, 'db_link', false);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_log_has_changed = false;
			}
		}
		else
		{
			$t_log_has_changed = false;
		}
		
		return $t_log_has_changed;
	}
	
	function create_info_boxes()
	{
		$t_infobox_messages_id_array = array();
		
		$t_log_prefix_array = $this->get_log_prefix_array();
		foreach($t_log_prefix_array as $t_log_prefix)
		{
			$t_infobox_messages_id_array = array_merge($t_infobox_messages_id_array, $this->create_info_box($t_log_prefix));
		}
		
		return $t_infobox_messages_id_array;
	}
	
	function create_info_box($p_log_prefix)
	{
		$t_html_output = '';
		$c_log_prefix = basename($p_log_prefix);
		$t_infobox_messages_id_array = array();
		
		$c_logfile = '';	
		
		$t_logfiles_array = glob(DIR_FS_CATALOG . '/logfiles/' . $c_log_prefix . '-*.log');
		if(is_array($t_logfiles_array))
		{
			foreach($t_logfiles_array as $t_file)
			{
				if(preg_match('/' . $c_log_prefix . '-[a-f0-9]{32}\.log/', basename($t_file)))
				{
					$c_logfile = basename($t_file);
					if($c_logfile != '' && $this->check_for_change($_SESSION['customer_id'], $c_logfile) == true)
					{
						$t_logfile_output_name = ucfirst($c_log_prefix) . '-Log';

						$t_messages_array = array();
						$t_headline_array = array();
						$t_button_label_array = array();
						$coo_languages = xtc_get_languages();

						for($i = 0; $i < count($coo_languages); $i++)
						{
							$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('admin_info_boxes', $coo_languages[$i]['id']));
							$t_headline_array[$coo_languages[$i]['id']] = $coo_text_mgr->get_text('HEADLINE_NEW_LOG_ENTRIES');
							$t_messages_array[$coo_languages[$i]['id']] = sprintf($coo_text_mgr->get_text('TEXT_NEW_LOG_ENTRIES'), $c_logfile, $t_logfile_output_name);
							$t_button_label_array[$coo_languages[$i]['id']] = $coo_text_mgr->get_text('BUTTON_SHOW_LOG');
						}			

						$coo_admin_infobox_control = MainFactory::create_object('AdminInfoboxControl');
						$t_infobox_messages_id_array[] = $coo_admin_infobox_control->add_message($t_messages_array, 'warning', $t_headline_array, $t_button_label_array, 'show_logs.php?file=' . $c_logfile, 'alwayson', 'new', $c_logfile, 'intern', false, false);
					}
				}
			}
		}
		
		return $t_infobox_messages_id_array;
	}
	
	function set_log_prefixes($p_log_prefix_array)
	{
		if(is_array($p_log_prefix_array))
		{
			foreach($p_log_prefix_array AS $t_log_prefix)
			{
				$c_log_prefix = basename($t_log_prefix);

				if(!in_array($c_log_prefix, $this->v_log_prefix_array))
				{
					$this->v_log_prefix_array[] = $c_log_prefix;
				}		
			}
			
			return true;
		}
		
		return false;
	}
	
	function get_log_prefix_array()
	{
		return $this->v_log_prefix_array;
	}
}

MainFactory::load_origin_class('ShowLogs');
