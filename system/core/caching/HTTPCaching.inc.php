<?php
/* --------------------------------------------------------------
   HTTPCaching.inc.php 2016-05-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class HTTPCaching
{
	var $v_etag = '';
	var $v_last_modified = '';
	var $v_activated = true;
	var $v_exclude_array = array();

	function HTTPCaching()
	{
		if(isset($_SERVER['HTTP_IF_NONE_MATCH']))
		{
			$this->v_etag = trim($_SERVER['HTTP_IF_NONE_MATCH']);
		}

		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		{
			$this->v_last_modified = @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
		}

		if(defined('HTTP_CACHING') && HTTP_CACHING == 'false')
		{
			$this->v_activated = false;
		}
		
		$this->v_exclude_array[] = 'display_vvcodes.php';
		$this->v_exclude_array[] = 'download.php';
		$this->v_exclude_array[] = 'ckeditor';
	}

	function send_header($p_unencrypted_etag_content = false, $p_md5_file = false, $p_last_modified_unixtime = false, $p_cache_control = false, $p_expires_unixtime = false, $p_pragma = false)
	{
		if(!empty($p_unencrypted_etag_content))
		{
			if($p_md5_file && is_file($p_unencrypted_etag_content))
			{
				$t_etag = md5_file($p_unencrypted_etag_content);
			}
			else
			{
				$t_etag = md5($p_unencrypted_etag_content);
			}
			header('Etag: "' . $t_etag . '"');
		}

		if(!empty($p_last_modified_unixtime))
		{
			header('Last-Modified: ' . gmdate("D, d M Y H:i:s", (int)$p_last_modified_unixtime) . ' GMT');
		}

		if(is_string($p_cache_control))
		{
			header('Cache-Control: ' . $p_cache_control);
		}

		if(!empty($p_expires_unixtime))
		{
			header('Expires: ' . gmdate("D, d M Y H:i:s", (int)$p_expires_unixtime) . ' GMT');
		}

		if(is_string($p_pragma))
		{
			header('Pragma: ' . $p_pragma);
		}
	}


	function check_cache($p_unencrypted_etag_content = false, $p_md5_file = false, $p_last_modified_unixtime = false)
	{
		if(!empty($p_unencrypted_etag_content))
		{
			if($p_md5_file)
			{
				$t_etag = '"' . md5_file($p_unencrypted_etag_content). '"';
			}
			else
			{
				$t_etag = '"' . md5($p_unencrypted_etag_content) . '"';
			}

			if($this->v_etag === $t_etag && $this->v_activated === true)
			{
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}

		if(!empty($p_last_modified_unixtime))
		{
			if($this->v_last_modified === gmdate("D, d M Y H:i:s", (int)$p_last_modified_unixtime) . ' GMT' && $this->v_activated === true)
			{
				header('HTTP/1.1 304 Not Modified');
				exit;
			}
		}
	}


	function start_gzip()
	{
		// if gzip_compression is enabled, start to buffer the output
		if(GZIP_COMPRESSION == 'true'
			&& $ext_zlib_loaded = extension_loaded('zlib')
			&& strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie 6.') === false
			&& $this->gzip_allowed() === true
			)
		{
			if(PREFER_GZHANDLER == 'false')
			{
				if(headers_sent() === false)
				{
					@ini_set('zlib.output_compression', 'On');
				}
			}
			
			if(strtolower((string)ini_get('zlib.output_compression') == 'off') || (string)ini_get('zlib.output_compression') == '0' || PREFER_GZHANDLER == 'true')
			{

				if(headers_sent() === false)
				{
					@ini_set('zlib.output_compression', 'Off');
				}

				$t_buffer = ob_start("ob_gzhandler");

				if($t_buffer === false)
				{
					ob_start();
				}
			}
			else
			{
				$t_output_compression_level = (int)GZIP_LEVEL;
				if($t_output_compression_level < 1 || $t_output_compression_level > 9)
				{
					$t_output_compression_level = 9;
				}
				if(headers_sent() === false)
				{
					@ini_set('zlib.output_compression_level', $t_output_compression_level);
				}
			}
		}
	}


	function start_output_buffer($p_callback = false)
	{
		if($p_callback)
		{
			ob_start($p_callback);
		}
		else
		{
			ob_start();
		}
	}


	function stop_output_buffer()
	{
		$t_content = ob_get_contents();
		ob_end_clean();

		return $t_content;
	}
	
	
	function gzip_allowed()
	{
		if(ob_get_contents() != '')
		{
			return false;
		}
		
		foreach($this->v_exclude_array AS $t_exclude)
		{
			if(strpos(gm_get_env_info('SCRIPT_NAME'), $t_exclude) !== false)
			{
				return false;
			}
		}
		
		return true;
	}
}