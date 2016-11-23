<?php
/* --------------------------------------------------------------
   DataCache.inc.php 2016-05-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class DataCache
{
	public $v_cache_content_array = array();
	public $v_coo_error_log = NULL;

	public $v_cache_file_prefix = 'persistent_data_cache-';
	
	protected $v_persistence_index_array = array();
	protected $v_persistence_tags_array = array();
	
	/*
	* constructor
	*/
	public function __construct()
	{
		if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('DataCache() by '.gm_get_env_info('REQUEST_URI'), 'DataCache');
		
		$this->init_persistence_tags_array();
		$this->init_persistence_index_array();
	}
	
	public static function &get_instance()
	{
		static $s_instance;

		if($s_instance === NULL)   {
			$s_instance = new DataCache();
		}
		return $s_instance;
	}
	
	protected function init_persistence_tags_array()
	{
		$this->v_persistence_tags_array = array(
			'CORE',
			'TEMPLATE',
			'CHECKOUT',
			'ADMIN'
		);
	}
	
	protected function init_persistence_index_array()
	{
		$t_index_file = $this->get_cache_dir() .'persistence_index';

		#cancel if cache file not found
		if(is_readable($t_index_file) == false) return NULL;

		#load cached object
		$t_data_serialized = file_get_contents($t_index_file);
		$coo_cached_data = unserialize($t_data_serialized);

		#cancel if unserialize was not successful
		if($coo_cached_data === false) return NULL;
		
		$this->v_persistence_index_array = $coo_cached_data;
	}
	
	public function persistence_tag_allowed($p_tag)
	{
		if(in_array($p_tag, $this->v_persistence_tags_array)) {
			# tag found + allowed
			return true;
		}
		# tag not found
		return false;
	}

	public function add_persistence_tag($p_key, $p_tags_array)
	{
		if(array_key_exists($p_key, $this->v_persistence_index_array) === false)
		{
			# init if key doesnt exist
			$this->v_persistence_index_array[$p_key] = array();
		}
		# set tag array
		$this->v_persistence_index_array[$p_key] = $p_tags_array;
		
		$this->write_persistence_index();
	}

	protected function write_persistence_index()
	{
		$t_index_file = $this->get_cache_dir() .'persistence_index';
		
		#serialize given data
		$t_data_serialized = serialize($this->v_persistence_index_array);

		if((file_exists($t_index_file) && is_writable($t_index_file)) || (!file_exists($t_index_file) && is_writable($this->get_cache_dir())))
		{
			#write data string to cache file
			file_put_contents($t_index_file, $t_data_serialized);
		}
		else
		{
			trigger_error($t_index_file . ' is not writable', E_USER_WARNING);
		}
	}




	public function get_cache_dir()
	{
		$t_cache_directory = DIR_FS_CATALOG . 'cache/';
		return $t_cache_directory;
	}
	
	protected function filter_key($p_key)
	{
		$c_key = preg_replace('/[^0-9a-zA-Z\._%-]/i', '', $p_key);
		return $c_key;
	}
	
	public function set_data($p_key, $p_value, $p_persistent=false, $p_persistence_tags_array=false)
	{
		$c_key = $this->filter_key($p_key);
		$this->v_cache_content_array[$c_key] = $p_value;
		if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log("new content with key ". $c_key, 'DataCache');

		if($p_persistent)
		{
			$this->write_persistent_data($c_key, $p_value);
			if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log("new persistent content with key ". $c_key, 'DataCache');
			
			if($p_persistence_tags_array)
			{
				# tags given? add them
				$this->add_persistence_tag($c_key, $p_persistence_tags_array);
			}
		}
	}

	public function get_data($p_key, $p_persistent=false)
	{
		$t_output = false;
		$c_key = $this->filter_key($p_key);

		if($this->key_exists($c_key, $p_persistent) == false)
		{
			# need existing key, so trigger error
			trigger_error('key not found in DataCache', E_USER_ERROR);
		}
		else {
			# key found, return cached data
			$t_output = $this->v_cache_content_array[$c_key];
			if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log("cached content returned with key ". $c_key, 'DataCache');
		}
		return $t_output;
	}

	public function key_exists($p_key, $p_persistent=false)
	{
		$c_key = $this->filter_key($p_key);
		if(is_object($GLOBALS['coo_debugger']) && $GLOBALS['coo_debugger']->is_enabled('DataCache_disable_cache')) return false;
		$t_output = false;

		if(array_key_exists($c_key, $this->v_cache_content_array))
		{
			# key found. return true
			$t_output = true;
		}

		#key not found in cache_content? try persistent?
		if($p_persistent == true && $t_output == false)
		{
			$t_data = $this->get_persistent_data($c_key);
			if($t_data !== NULL)
			{
				#found persistent data, write to cache_content
				$this->set_data($c_key, $t_data);
				# key found. return true
				$t_output = true;
			}
		}
		return $t_output;
	}

	public function build_key($p_data)
	{
		$t_output = '';
		$t_output = md5($p_data);

		return $t_output;
	}

	public function get_cache_file($p_key)
	{
		$t_cache_file = $this->get_cache_dir() . $p_key.'-'. $this->v_cache_file_prefix . LogControl::get_secure_token() . '.pdc';
		return $t_cache_file;
	}
	
	public function write_persistent_data($p_key, $p_data)
	{
		if(is_object($GLOBALS['coo_debugger']) && $GLOBALS['coo_debugger']->is_enabled('DataCache_disable_persistent')) return false;
        $t_cache_file = $this->get_cache_file($p_key);

		#serialize given data
		$t_data_serialized = serialize($p_data);

		if((file_exists($t_cache_file) && is_writable($t_cache_file)) || (!file_exists($t_cache_file) && is_writable($this->get_cache_dir())))
		{
			#write data string to cache file
			file_put_contents($t_cache_file, $t_data_serialized);
		}
		else
		{
			trigger_error($t_cache_file . ' is not writable', E_USER_WARNING);
		}
	}

	public function get_persistent_data($p_key)
	{
		$t_cache_file = $this->get_cache_file($p_key);
		
		#cancel if cache file not found
		if(is_readable($t_cache_file) == false) return NULL;

		#load cached object
		$t_data_serialized = file_get_contents($t_cache_file);
		$coo_cached_data = unserialize($t_data_serialized);

		#cancel if unserialize was not successful
		if($coo_cached_data === false) return NULL;

		return $coo_cached_data;
	}
	
	public function clear_cache_by_tag($p_cache_tag)
	{
		foreach($this->v_persistence_index_array as $t_cache_key => $t_cache_tags_array)
		{
			if(is_array($t_cache_tags_array) && in_array($p_cache_tag, $t_cache_tags_array))
			{
				$this->clear_cache($t_cache_key);
				$this->add_persistence_tag($t_cache_key, NULL);
			}
		}
	}

	public function clear_cache($p_key=NULL)
	{
		if($p_key === NULL) 
		{
			$p_key = '*';
		}
		$t_search_pattern = $this->get_cache_file($p_key);

		$t_files_and_dirs_array = glob($t_search_pattern);

		if(is_array($t_files_and_dirs_array))
		{
			foreach($t_files_and_dirs_array as $t_filename)
			{
				if(file_exists($t_filename))
				{
					#delete found cache files
					$t_unlink_result = @unlink($t_filename);
					if($t_unlink_result !== true)
					{
						trigger_error((string)$t_filename . ' cannot be deleted', E_USER_WARNING);
					}
				}
			}
		}
	}
}