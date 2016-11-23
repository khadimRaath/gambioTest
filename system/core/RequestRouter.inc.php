<?php
/* --------------------------------------------------------------
   RequestRouter.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of RequestRouter
 *
 * @author ncapuno
 */
class RequestRouter
{
	var $v_data_array = NULL;
	var $v_output_buffer = '';
	var $v_class_name_suffix = '';

	function RequestRouter($p_class_name_suffix)
	{
		$this->set_class_name_suffix($p_class_name_suffix);
	}

	function set_data($p_key, $p_value)
	{
		$c_key = trim((string) $p_key);
		if($c_key == '') {
			trigger_error('empty key given', E_USER_WARNING);
		}
		$this->v_data_array[$c_key] = $p_value;
	}

	function set_class_name_suffix($p_suffix)
	{
		$this->v_class_name_suffix = (string)$p_suffix;
	}

	function get_class_name_suffix()
	{
		return $this->v_class_name_suffix;
	}

	function create_module_object($p_module_name)
	{
		$coo_output_object = NULL;
		$t_class_name_suffix = $this->get_class_name_suffix();

		#class name for factory
		$t_class_name = $p_module_name . $t_class_name_suffix;

		if(MainFactory::load_class($t_class_name))
		{
			#class file found, build object in factory
			$coo_output_object = MainFactory::create_object($t_class_name);
		}
		return $coo_output_object;
	}

	function proceed($p_module_name)
	{
		#clean module name (path injections)
		$c_module_name = htmlentities_wrapper($p_module_name);
		$c_module_name = str_replace('/', '', $c_module_name);
		$c_module_name = str_replace('.', '', $c_module_name);

		#find and build module object
		$coo_module = $this->create_module_object($c_module_name);
		if($coo_module == NULL)
		{
			#could not build module object
			return false;
		}

		#transfer given request data to module_object
		foreach($this->v_data_array as $t_key => $t_value)
		{
			$coo_module->set_data($t_key, $t_value);
		}

		if($coo_module->get_permission_status() == false)
		{
			#permission check failed
			trigger_error('using this module ['.$c_module_name.'] is not permitted in this context', E_USER_WARNING);
			return false;
		}

		# proceed module and write response to buffer
		ob_start();
		$success = $coo_module->proceed();
		$content = ob_get_clean();
		
		if($success === false)
		{
			$content = htmlspecialchars($content);
		}
		
		echo $content;
		
		$this->v_output_buffer = $coo_module->get_response();
		
		return true;
	}
	
	function get_response()
	{
		$t_output = $this->v_output_buffer;
		return $t_output;
	}
}