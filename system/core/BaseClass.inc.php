<?php
/* --------------------------------------------------------------
   BaseClass.inc.php 2014-03-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class BaseClass
{
	protected $validation_rules_array = array();
	
	/*
	 ********************************************************************************************************************************************************
	 * no constructor allowed, because singleton classes which extend the BaseClass have a non-public __construct-method which in turn causes a fatal error *
	 ********************************************************************************************************************************************************
	 */
	
	public function get_($p_variable_name)
	{
		if (property_exists($this, $p_variable_name) == false)
		{
			trigger_error("Variable '" . $p_variable_name . "' doesn't exist in class '" . get_class($this) . "'", E_USER_ERROR);
		}
		
		$t_function_name = 'get_' . trim($p_variable_name);
		if (method_exists($this, $t_function_name))
		{
			return call_user_func(array($this, $t_function_name));
		}
		
		$this->check_private($p_variable_name);
		
		return $this->{$p_variable_name};
	}
	
	public function set_($p_variable_name, $p_variable_value)
	{
		if (property_exists($this, $p_variable_name) == false)
		{
			trigger_error("Variable '" . $p_variable_name . "' doesn't exist in class '" . get_class($this) . "'", E_USER_ERROR);
		}
		
		$t_function_name = 'set_' . trim($p_variable_name);
		if (method_exists($this, $t_function_name))
		{
			call_user_func(array($this, $t_function_name), $p_variable_value);
			return;
		}

		if(array_key_exists($p_variable_name, $this->validation_rules_array))
		{
			if($this->check_data_type($p_variable_name, $p_variable_value) == false)
			{
				return false;
			}
			
			if((isset($this->validation_rules_array[$p_variable_name]['strict']) 
				&& $this->validation_rules_array[$p_variable_name]['strict'] == false)
				|| isset($this->validation_rules_array[$p_variable_name]['strict']) == false)
			{
				if($this->validation_rules_array[$p_variable_name]['type'] == 'int')
				{
					$p_variable_value = (int)$p_variable_value;
				}
				elseif($this->validation_rules_array[$p_variable_name]['type'] == 'double')
				{
					$p_variable_value = (double)$p_variable_value;
				}
			}
		}
		
		$this->check_private($p_variable_name);
		
		$this->{$p_variable_name} = $p_variable_value;
	}
	
	public function reference_set_($p_variable_name, &$p_variable_value)
	{
		if (!property_exists($this, $p_variable_name))
		{
			trigger_error("Variable '" . $p_variable_name . "' doesn't exist in class '" . get_class($this) . "'", E_USER_ERROR);
		}
		
		$t_function_name = 'set_' . trim($p_variable_name);
		if (method_exists($this, $t_function_name))
		{
			call_user_func_array(array($this, $t_function_name), array(&$p_variable_value));
			return;
		}

		if(array_key_exists($p_variable_name, $this->validation_rules_array))
		{
			if($this->check_data_type($p_variable_name, $p_variable_value) == false)
			{
				return false;
			}
		}
		
		$this->check_private($p_variable_name);
		
		$this->{$p_variable_name} = &$p_variable_value;
	}
	
	protected function check_private($p_variable_name)
	{
		$coo_class_reflector = new ReflectionClass(get_class($this));
		$coo_property_reflector = $coo_class_reflector->getProperty($p_variable_name);
		
		if($coo_property_reflector->isPrivate())
		{
			trigger_error("Variable '" . $p_variable_name . "' in class '" . get_class($this) . "' is private and can't be accessed", E_USER_ERROR);
		}
	}
	
	protected function check_data_type($p_variable_name, $p_variable_value)
	{
		$t_strict = false;
		$t_error_level = E_USER_ERROR;
		$t_type = '';
		$t_class_name = '';

		if(isset($this->validation_rules_array[$p_variable_name]['type']) && empty($this->validation_rules_array[$p_variable_name]['type']) == false)
		{
			$t_type = $this->validation_rules_array[$p_variable_name]['type'];
		}
		
		if(isset($this->validation_rules_array[$p_variable_name]['strict']) && empty($this->validation_rules_array[$p_variable_name]['strict']) == false)
		{
			$t_strict = $this->validation_rules_array[$p_variable_name]['strict'];
		}
		
		if(isset($this->validation_rules_array[$p_variable_name]['error_level']) && empty($this->validation_rules_array[$p_variable_name]['error_level']) == false)
		{
			$t_error_level = $this->validation_rules_array[$p_variable_name]['error_level'];
		}

		if(isset($this->validation_rules_array[$p_variable_name]['object_type']) && empty($this->validation_rules_array[$p_variable_name]['object_type']) == false)
		{
			$t_class_name = $this->validation_rules_array[$p_variable_name]['object_type'];
		}

		$t_valid = check_data_type($p_variable_value, $t_type, $t_strict, $t_error_level, $t_class_name);
		
		return $t_valid;
	}
	
	protected function set_validation_rules(){}
	
	public function get_uninitialized_variables($p_variable_names_array)
	{
		$t_uninitialized_variables = array();
		
		if(is_array($p_variable_names_array))
		{
			foreach($p_variable_names_array as $t_variable_name)
			{
				if(property_exists($this, $t_variable_name) == false || $this->{$t_variable_name} === null)
				{
					$t_uninitialized_variables[] = $t_variable_name;
				}
			}
		}
		
		return $t_uninitialized_variables;
	}
}