<?php
/* --------------------------------------------------------------
   ClassRegistry.inc.php 2013-09-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ClassRegistry extends Registry
{
  /*
   * pattern for "which one is a class file"
   */
  var $v_file_pattern = ".php";
  var $v_samples_dir_pattern = "_samples";


  /*
   * constructor
   */
  function ClassRegistry()
  {
	if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('ClassRegistry() by '.gm_get_env_info('REQUEST_URI'), 'ClassRegistry');
  }

  static function &get_instance()
  {
	  static $s_instance;

	  if($s_instance === NULL)   {
		  $s_instance = new ClassRegistry();
	  }
	  return $s_instance;
  }


  /*
   * scan given dir recursively or not for classes ('.inc.php') and
   * set class name and path.
   * @param string $p_path  path for scan
   * @param bool $p_recursively  do it with or without
   * @return bool true:ok | false:error
   */
	function scan_dir($p_path, $p_recursively = false)
	{
		$t_coo_cached_directory = new CachedDirectory($p_path);
		#print_r($t_coo_handle);

		#var_dump($p_path);echo '<br>';
		if($t_coo_cached_directory->is_dir($p_path) == false)
		{
			# p_path not a directory
			return false;
		}
		elseif(substr($p_path, strlen($this->v_samples_dir_pattern) * -1) == $this->v_samples_dir_pattern)
		{
			# p_path is samples-directory
			return false;
		}
		
		while (false !== ($t_entry = $t_coo_cached_directory->read() ))
		{
			if (substr($t_entry, 0, 1)==".") continue;
		#	echo $v_entry.'<br>';

			$t_part = '/';
			if(substr($p_path, -1, 1) == $t_part) $t_part = '';

			if($t_coo_cached_directory->is_dir($p_path.'/'.$t_entry) && $p_recursively)
			{
				$t_result = $this->scan_dir($p_path.$t_part.$t_entry, $p_recursively);
			}
			elseif(strpos($t_entry, $this->v_file_pattern, strlen($t_entry) - strlen($this->v_file_pattern)) > 0)
			{
				$t_class_name = strtok($t_entry, ".");
				$this->set($t_class_name, $p_path .'/'. $t_entry);
			}
		}
		#print_r($this->get_all_data());
		return true;
	}
}
?>