<?php
/* --------------------------------------------------------------
  CachedDirectory.inc.php 2016-03-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class CachedDirectory
{
	private static $s_config_cache_paths_array = array();
	var $v_directory_content_array = array();
	var $v_cache_key = '';
	var $v_count_index = 0;
	var $v_path = '';
	var $v_coo_cache;

	/*
	 * constructor
	 */
	function CachedDirectory($p_path)
	{
		$this->v_coo_cache = DataCache::get_instance();
		$currentTemplate = (defined('CURRENT_TEMPLATE')) ? CURRENT_TEMPLATE : 'template_blank';

		if(!isset(self::$s_config_cache_paths_array[$currentTemplate]))
		{
			$t_cache_paths_array = array(
				DIR_FS_CATALOG . 'admin/includes/classes',
				DIR_FS_CATALOG . 'admin/includes/gm/classes',
				DIR_FS_CATALOG . 'gm/classes',
				DIR_FS_CATALOG . 'gm/javascript',
				DIR_FS_CATALOG . 'gm/properties',
				DIR_FS_CATALOG . 'system/classes',
				DIR_FS_CATALOG . 'system/core',
				DIR_FS_CATALOG . 'system/extender',
				DIR_FS_CATALOG . 'system/overloads',
				DIR_FS_CATALOG . 'GXEngine',
				DIR_FS_CATALOG . 'GXMainComponents',
				DIR_FS_CATALOG . 'GXUserComponents'
			);

			foreach($t_cache_paths_array as $t_key => $t_dir)
			{
				if(is_dir($t_dir) === false)
				{
					unset($t_cache_paths_array[$t_key]);
				}
			}

			self::$s_config_cache_paths_array[$currentTemplate] = $t_cache_paths_array;

			$t_templates_paths_array = array(
				DIR_FS_CATALOG . 'templates/' . $currentTemplate . '/source/classes',
				DIR_FS_CATALOG . 'templates/' . $currentTemplate . '/javascript',
				DIR_FS_CATALOG . 'templates/' . $currentTemplate . '/usermod'
			);

			foreach($t_templates_paths_array AS $t_path)
			{
				if(is_dir($t_path))
				{
					self::$s_config_cache_paths_array[$currentTemplate][] = $t_path;
				}
			}
		}

		$this->set_cache_key('directory_cache');
		$this->set_path($p_path);
		$this->load_cache();
	}

	function set_path($p_path)
	{
		if(check_data_type($p_path, 'string') && is_readable($p_path))
		{
			$t_path = $p_path;

			if(substr($p_path, -1) == '/')
			{
				$t_path = substr($p_path, 0, -1);
			}

			$this->v_path = $t_path;

			$this->reset_count_index();

			return true;
		}

		return false;
	}

	function get_path()
	{
		return $this->v_path;
	}

	function set_cache_key($p_filename)
	{
		if(check_data_type($p_filename, 'string'))
		{
			$currentTemplate = (defined('CURRENT_TEMPLATE')) ? CURRENT_TEMPLATE : 'template_blank';
			$this->v_cache_key = basename($p_filename . '_' . $currentTemplate);

			return true;
		}

		return false;
	}

	function get_cache_key()
	{
		return basename((string)$this->v_cache_key);
	}

	function set_directory_content_array($p_directory_content_array)
	{
		if(check_data_type($p_directory_content_array, 'array'))
		{
			$this->v_directory_content_array = $p_directory_content_array;

			return true;
		}

		return false;
	}

	function read()
	{
		if(isset($this->v_directory_content_array[$this->get_path()][$this->v_count_index]))
		{
			$this->v_count_index++;
			return $this->v_directory_content_array[$this->get_path()][$this->v_count_index - 1];
		}

		return false;
	}

	function reset_count_index()
	{
		$this->v_count_index = 0;
	}

	function clear_cache()
	{

		$this->v_coo_cache->clear_cache($this->get_cache_key());

		return true;
	}

	function rebuild_cache()
	{
		$currentTemplate = (defined('CURRENT_TEMPLATE')) ? CURRENT_TEMPLATE : 'template_blank';
		foreach(self::$s_config_cache_paths_array[$currentTemplate] AS $t_path)
		{
			$this->scan_dir($t_path);
		}

		$this->v_coo_cache = DataCache::get_instance();
		$this->v_coo_cache->set_data($this->get_cache_key(), $this->v_directory_content_array, true);
	}

	function scan_dir($p_path)
	{
		if(check_data_type($p_path, 'string') && is_readable($p_path))
		{
			if(is_readable($p_path) == false)
			{
				return false;
			}

			$t_path_pattern = $p_path . '/*';

			$t_glob_data_array = glob($t_path_pattern);

			if(is_array($t_glob_data_array))
			{
				foreach($t_glob_data_array as $t_result)
				{
					$t_entry = basename($t_result);

					if(substr($t_entry, 0, 1) == '.')
					{
						continue;
					}

					$t_part = '/';
					if(substr($p_path, -1) == $t_part)
					{
						$t_part = '';
					}

					if(!isset($this->v_directory_content_array[$p_path]) || !is_array($this->v_directory_content_array[$p_path]))
					{
						$this->v_directory_content_array[$p_path] = array();
					}

					$this->v_directory_content_array[$p_path][] = $t_entry;

					if(is_dir($p_path . '/' . $t_entry))
					{
						$t_result = $this->scan_dir($p_path . $t_part . $t_entry);
					}
				}
			}

			return true;
		}
		else
		{
			trigger_error('CachedDirectory scan_dir failed, because p_path is not a valid absolute path: ' . (string)$p_path, E_USER_ERROR);
		}

		return false;
	}

	function load_cache()
	{
		$this->v_coo_cache = DataCache::get_instance();

		if($this->v_coo_cache->key_exists($this->get_cache_key(), true))
		{
			$t_serialized_cache_data_array = $this->v_coo_cache->get_data($this->get_cache_key());

			if(check_data_type($t_serialized_cache_data_array, 'array'))
			{
				return $this->set_directory_content_array($t_serialized_cache_data_array);
			}
		}
		else
		{
			$this->rebuild_cache();
		}

		return false;
	}

	function is_dir($p_path)
	{
		$c_path = (string)$p_path;

		if($p_path != '/' && substr($p_path, -1) == '/')
		{
			$c_path = substr($c_path, 0, -1);
		}

		if(isset($this->v_directory_content_array[$c_path]))
		{
			return true;
		}

		return false;
	}

	function is_file($p_path)
	{
		return !$this->is_dir($p_path);
	}

	function file_exists($p_file_path)
	{
		if(isset($this->v_directory_content_array[dirname($p_file_path)])
			&& in_array(basename($p_file_path), $this->v_directory_content_array[dirname($p_file_path)]))
		{
			return true;
		}
		else
		{
			$t_real_file_path = @realpath($p_file_path);

			if(!empty($t_real_file_path))
			{
				if(isset($this->v_directory_content_array[dirname(str_replace('\\', '/', $t_real_file_path))])
					&& in_array(basename($p_file_path), $this->v_directory_content_array[dirname(str_replace('\\', '/', $t_real_file_path))]))
				{
					return true;
				}

				if(file_exists($t_real_file_path))
				{
					return true;
				}

				return false;
			}
			elseif(file_exists($p_file_path))
			{
				return true;
			}

			return false;
		}
	}
}
