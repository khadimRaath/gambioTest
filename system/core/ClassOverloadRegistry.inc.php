<?php
/* --------------------------------------------------------------
   ClassOverloadRegistry.inc.php 2016-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ClassOverloadRegistry extends Registry
{
	var $v_class_overload_system_dir = '';
	var $v_class_overload_user_dir = '';

	var $v_admin_prefixed_classes_array = array
	(
		'language',
		'messageStack',
		'order',
		'shoppingCart',
		'splitPageResults'
	);
	
	protected static $cachedDirectories = array();

  /*
   * constructor
   */
	function ClassOverloadRegistry()
	{
		if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('ClassOverloadRegistry() by '.gm_get_env_info('REQUEST_URI'), 'ClassOverloadRegistry');
	}

	static function &get_instance()
	{
		static $s_instance;

		if($s_instance === NULL)   {
			$s_instance = new ClassOverloadRegistry();
		}
		return $s_instance;
	}

	function set_class_overload_system_dir($p_path)
	{
		$this->v_class_overload_system_dir = $p_path;
	}

	function get_class_overload_system_dir()
	{
		$t_output = $this->v_class_overload_system_dir;
		return $t_output;
	}

	function set_class_overload_user_dir($p_path)
	{
		$this->v_class_overload_user_dir = $p_path;
	}

	function get_class_overload_user_dir()
	{
		$t_output = $this->v_class_overload_user_dir;
		return $t_output;
	}
	
	function get_directory_classes($p_class_overload_dir)
	{
		$t_found_classes_array = array();

		if (!empty(self::$cachedDirectories[$p_class_overload_dir]))
		{
			$t_coo_cached_directory = self::$cachedDirectories[$p_class_overload_dir];
		}
		else 
		{	
			$t_coo_cached_directory = new CachedDirectory($p_class_overload_dir);
			self::$cachedDirectories[$p_class_overload_dir] = $t_coo_cached_directory ;
		}

		if($t_coo_cached_directory->is_dir($p_class_overload_dir) == false)
		{
			# return empty array, if overload direcotory not found
			return $t_found_classes_array;
		}
		
		$t_file_pattern = '.php';

		while(($t_entry = $t_coo_cached_directory->read()) !== false)
		{
			if(substr($t_entry, 0, 1) == '.') continue;

			# entry is a file and ends with '.php'
			if($t_coo_cached_directory->is_file($p_class_overload_dir.'/'.$t_entry) 
			   && strpos($t_entry, $t_file_pattern, strlen($t_entry) - strlen($t_file_pattern)) > 0)
			{
				$t_system_class_path = $p_class_overload_dir.'/'.$t_entry;
				$t_user_class_path = DIR_FS_CATALOG.'GXUserComponents/'.$t_entry;

				if($t_coo_cached_directory->file_exists($t_user_class_path))	{
					$t_found_classes_array[] = $t_user_class_path;
				} else {
					$t_found_classes_array[] = $t_system_class_path;
				}
			}
		}
		return $t_found_classes_array;
	}

	function init_class_chain($p_base_class_name, $p_overload_subdir=false)
	{
		# set overload directory
		$t_system_base_dir = $this->get_class_overload_system_dir();
		$t_user_base_dir = $this->get_class_overload_user_dir();

		if($p_overload_subdir === false) {
			$t_class_overload_system_dir = $t_system_base_dir.$p_base_class_name;
			$t_class_overload_user_dir = $t_user_base_dir.$p_base_class_name;
			$t_class_overload_system_dir_admin = $t_system_base_dir.'Admin-'.$p_base_class_name;
			$t_class_overload_user_dir_admin = $t_user_base_dir.'Admin-'.$p_base_class_name;
		} else {
			$t_class_overload_system_dir = $t_system_base_dir.$p_overload_subdir;
			$t_class_overload_user_dir = $t_user_base_dir.$p_overload_subdir;
			$t_class_overload_system_dir_admin = $t_system_base_dir.'Admin-'.$p_overload_subdir;
			$t_class_overload_user_dir_admin = $t_user_base_dir.'Admin-'.$p_overload_subdir;
		}
		
		# search class files

		$t_admin_prefix_class = false;
		if(in_array($p_base_class_name, $this->v_admin_prefixed_classes_array) == true) $t_admin_prefix_class = true;
		if(in_array($p_overload_subdir, $this->v_admin_prefixed_classes_array) == true) $t_admin_prefix_class = true;

		if(APPLICATION_RUN_MODE == 'backend' && $t_admin_prefix_class == true)
		{
			$t_system_overload_classes = $this->get_directory_classes($t_class_overload_system_dir_admin);
			$t_user_overload_classes = $this->get_directory_classes($t_class_overload_user_dir_admin);
		}
		else
		{
			$t_system_overload_classes = $this->get_directory_classes($t_class_overload_system_dir);
			$t_user_overload_classes = $this->get_directory_classes($t_class_overload_user_dir);
		}

		$t_found_classes_array = array_merge($t_system_overload_classes, $t_user_overload_classes);
		sort($t_found_classes_array);

		# debug: show found chain
		if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('class chain '. $p_base_class_name .': '. print_r($t_found_classes_array, true), 'class_overloading');

		# create class-aliases
		$t_parent_class = $p_base_class_name;
		$t_current_class = '';

		for($i=0; $i<sizeof($t_found_classes_array); $i++)
		{
			$t_file_parts = explode('/', $t_found_classes_array[$i]);
			$t_file_name = $t_file_parts[sizeof($t_file_parts)-1];
			
			$t_current_class = strtok($t_file_name, ".");
			$t_new_class = $t_current_class.'_parent';

			if(class_exists($t_new_class) == false)
			{
				# prepare extended "_parent"
				$t_eval_code = 'class '.$t_new_class.' extends '.$t_parent_class.' {}';
				eval($t_eval_code);

				# include extending class
				include_once($t_found_classes_array[$i]);
			}

			# look for extension of extending class
			$this->init_class_chain($t_current_class);
			$t_found_extension_class = $this->get($t_current_class);

			if(empty($t_found_extension_class) == false)
			{
				$t_current_class = $t_found_extension_class;
			}

			# prepare parent-name for next round
			$t_parent_class = $t_current_class;
		}

		# save final class in registry
		if(sizeof($t_found_classes_array) > 0)
		{
			$t_last_chain_class = $t_current_class;
			$this->set($p_base_class_name, $t_last_chain_class);

			# final overloading class found
			return true;
		}
		
		# overload directory seems empty
		return false;
	}
}
