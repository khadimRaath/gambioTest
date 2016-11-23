<?php
/* --------------------------------------------------------------
  LogEvent.inc.php 2014-11-17 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class LogEvent
{
	protected $group;
	protected $level;
	protected $level_type;
	protected $error_code;
	protected $message;
	protected $additional_info;
	protected $log_file_name;
	protected $configuration;
	
	protected $file;
	protected $line;
	protected $backtrace_array;
	
	protected $request_type;
	protected $request_url;
	protected $request_duration; //ms
	protected $server;
	protected $server_address;
	protected $remote_address;
	protected $user_agent;
	protected $date;
	
	protected $session_data_array;
	protected $post_data_array;
	protected $get_data_array;


	public function __construct($p_message, $p_group = '', $p_log_file_name = 'debug', $p_level = 'notice', $p_level_type = 'USER NOTICE',
								$p_error_code = E_USER_NOTICE, $p_additional_info = '', $p_coo_configuration = null)
	{
		$this->date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$this->message = $p_message;
		$this->additional_info = $p_additional_info;
		$this->group = $p_group;
		$this->log_file_name = $p_log_file_name;
		$this->level = $p_level;
		$this->level_type = $p_level_type;
		$this->error_code = $p_error_code;
		
		$this->configuration = $p_coo_configuration;
		
		$this->backtrace_array = array();
		
		if($this->build_request_data_active())
		{
			$this->init_request_data();
			$this->init_session_data();
		}
		
		if($this->build_backtrace_active())
		{
			$this->init_backtrace_data();
		}

		if($this->build_function_data_active())
		{
			$this->init_function_data();
		}

		if($this->build_class_data_active())
		{
			$this->init_class_data();
		}
		
		if(count($this->backtrace_array) > 0)
		{
			$this->file = $this->determine_file();
			$this->line = $this->determine_line();
		}
		else
		{
			$this->file = '';
			$this->line = '';
		}
	}
	
	protected function determine_file()
	{
		$t_file = '';
		$t_backtrace_array = debug_backtrace();
		
		foreach($t_backtrace_array as $t_call)
		{
			if($this->is_excluded_call($t_call))
			{
				continue;
			}

			$t_file = $t_call['file'];
			
			break;
		}
		
		return $t_file;
	}

	protected function determine_line()
	{
		$t_line = '';
		$t_backtrace_array = debug_backtrace();
		
		foreach($t_backtrace_array as $t_call)
		{
			if($this->is_excluded_call($t_call))
			{
				continue;
			}

			$t_line = $t_call['line'];
			
			break;
		}

		return $t_line;
	}
	
	protected function init_request_data()
	{
		if(isset($_SERVER))
		{
			$this->request_type = $_SERVER['REQUEST_METHOD'];
			$this->request_url = $_SERVER['REQUEST_URI'];
			if(isset($_SERVER['REQUEST_TIME_FLOAT']))
			{
				$this->request_duration = round((int)(microtime(true) * 1000) - (int)($_SERVER['REQUEST_TIME_FLOAT'] * 1000));
			}
			else
			{
				$this->request_duration = round((int)microtime(true) - $_SERVER['REQUEST_TIME']);
			}
			$this->server = $_SERVER['SERVER_SOFTWARE'];
			$this->server_address = $_SERVER['SERVER_ADDR'];

			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$this->remote_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			elseif(isset($_SERVER['HTTP_CLIENT_IP']))
			{
				$this->remote_address = $_SERVER['HTTP_CLIENT_IP'];
			}
			else
			{
				$this->remote_address = $_SERVER['REMOTE_ADDR'];
			}
			
			$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		}
		
		if(isset($_POST))
		{
			$this->post_data_array = $this->censor($_POST);
		}
		else
		{
			$this->post_data_array = array();
		}
		
		if($_GET)
		{
			$this->get_data_array = $this->censor($_GET);
		}
		else
		{
			$this->get_data_array = array();
		}
	}
	
	protected function init_backtrace_data()
	{
		if($this->determine_file() == '')
		{
			return;
		}
		
		$t_index = 0;
		$t_raw_backtrace_array = debug_backtrace();
		
		foreach($t_raw_backtrace_array as $t_key => $t_call)
		{
			if($this->is_excluded_call($t_call))
			{
				continue;
			}
			
			$this->backtrace_array[$t_index] = $t_call;
			
			$t_index++;
		}
	}
	
	protected function init_function_data()
	{
		if($this->determine_file() == '')
		{
			return;
		}
		
		foreach($this->backtrace_array as $t_index => $t_call)
		{
			$this->backtrace_array[$t_index]['function_parameter_array'] = array();
			
			// method called
			if($t_call['function'] != '' && isset($t_call['class']))
			{
				$coo_reflector = new ReflectionClass($t_call['class']);
				$coo_method = $coo_reflector->getMethod($t_call['function']);
				$t_parameter_array = $coo_method->getParameters();

				for($i = 0; $i < count($t_parameter_array); $i++)
				{
					if(isset($t_call['args'][$i]) == false && $t_parameter_array[$i]->isOptional())
					{
						if($t_parameter_array[$i]->isPassedByReference())
						{
							$t_key = $t_parameter_array[$i]->name . ' (passed by reference)';
						}
						else
						{
							$t_key = $t_parameter_array[$i]->name;
						}

						if($t_parameter_array[$i]->isDefaultValueAvailable())
						{
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_key] = '### default value: ' . $this->var_export($t_parameter_array[$i]->getDefaultValue(), true);

							if(method_exists(get_class($t_parameter_array[$i]), 'isDefaultValueConstant') && $t_parameter_array[$i]->isDefaultValueConstant())
							{
								$this->backtrace_array[$t_index]['function_parameter_array'][$t_key] .= ' (value of constant)';
							}
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_key] .= ' ###';
						}
						else
						{
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_key] = '### indeterminate value ###';
						}
					}
					else
					{
						if($t_parameter_array[$i]->isPassedByReference())
						{
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_parameter_array[$i]->name . ' (passed by reference)'] = $this->var_export($t_call['args'][$i], true);
						}
						elseif(isset($t_call['args']))
						{
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_parameter_array[$i]->name] = $this->var_export($t_call['args'][$i], true);
						}
					}
				}
			}
			// function called
			elseif($t_call['function'] != '' && function_exists($t_call['function']))
			{
				$coo_reflector = new ReflectionFunction($t_call['function']);
				$t_parameter_array = $coo_reflector->getParameters();

				for($i = 0; $i < count($t_parameter_array); $i++)
				{
					if($t_parameter_array[$i]->isOptional())
					{
						if($t_parameter_array[$i]->isPassedByReference())
						{
							$t_key = $t_parameter_array[$i]->name . ' (passed by reference)';
						}
						else
						{
							$t_key = $t_parameter_array[$i]->name;
						}

						if($t_parameter_array[$i]->isDefaultValueAvailable())
						{
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_key] = '### indeterminate value, default value: ' . $this->var_export($t_parameter_array[$i]->getDefaultValue(), true);
	
							if(method_exists(get_class($t_parameter_array[$i]), 'isDefaultValueConstant') && $t_parameter_array[$i]->isDefaultValueConstant())
							{
								$this->backtrace_array[$t_index]['function_parameter_array'][$t_key] .= ' (value of constant)';
							}
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_key] .= ' ###';
						}
						else
						{
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_key] = '### indeterminate value ###';
						}
					}
					else
					{
						if($t_parameter_array[$i]->isPassedByReference())
						{
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_parameter_array[$i]->name . ' (passed by reference)'] = '### indeterminate value ###';
						}
						else
						{
							$this->backtrace_array[$t_index]['function_parameter_array'][$t_parameter_array[$i]->name] = '### indeterminate value ###';
						}
					}
					
				}
			}
			
			if($t_call['function'] != '' && isset($t_call['args']) && is_array($t_call['args']))
			{
				$t_args_array = array();

				foreach($t_call['args'] as $t_arg)
				{
					$t_args_array[] = $this->var_export($t_arg, true);
				}
				
				$this->backtrace_array[$t_index]['function'] .= '(' . implode(', ', $t_args_array) . ')';
			}

			if($this->configuration->get_any_by_output_type($this->level, 'backtrace') == false)
			{
				break;
			}
		}
	}
	
	protected function init_class_data()
	{
		if($this->determine_file() == '')
		{
			return;
		}
		
		foreach($this->backtrace_array as $t_index => $t_call)
		{
			if(isset($t_call['class']) == false || isset($t_call['file']) == false || in_array(basename($t_call['file']), $this->configuration->get_excluded_files_array()))
			{
				continue;
			}

			$this->backtrace_array[$t_index]['class_property_array'] = array();
			
			if($t_call['class'] != '' && isset($t_call['object']))
			{
				$t_class_property_array = &$this->backtrace_array[$t_index]['class_property_array'];
				$this->init_class_properties($t_call['class'], $t_call['object'], $t_class_property_array);
			}

			if($this->configuration->get_any_by_output_type($this->level, 'backtrace') == false)
			{
				break;
			}
		}
	}
	
	protected function init_class_properties($p_class, $p_coo_class_object, array &$p_class_property_array = array())
	{
		$coo_reflector = new ReflectionClass($p_class);
		$t_properties_array = $coo_reflector->getProperties();
		
		foreach($t_properties_array as $t_property)
		{
			$t_property_value = $this->var_export(null);
			
			if(isset($p_coo_class_object->{$t_property->name}))
			{
				$t_property_value = $this->var_export($p_coo_class_object->{$t_property->name});
			}
			elseif($t_property->isPublic())
			{
				$t_property_value = $this->var_export(null);
			}
			elseif(method_exists($p_coo_class_object, 'get_' . $t_property->name))
			{
				$t_property_value = $this->var_export(call_user_func(array($p_coo_class_object, 'get_' . $t_property->name)));
			}
			elseif(method_exists($p_coo_class_object, 'get_') && $t_property->isPrivate() == false)
			{
				$t_property_value = $this->var_export(call_user_func(array($p_coo_class_object, 'get_'), $t_property->name));
			}
			else
			{
				$t_modifier_names = Reflection::getModifierNames($t_property->getModifiers());
				$t_property_value = '### Cannot access ' . implode(' ', $t_modifier_names) . ' property ' . $p_class . '::$' . $t_property->name . ' ###';
			}
			
			$p_class_property_array[$t_property->name] = $t_property_value;
		}
	}
	
	protected function init_session_data()
	{
		if(isset($_SESSION) && $this->build_session_data_active())
		{
			$t_session_key_array = $this->configuration->get_session_keys();
			if(empty($t_session_key_array))
			{
				$this->session_data_array = $_SESSION;
			}
			else
			{
				foreach($t_session_key_array as $t_session_key)
				{
					if(isset($_SESSION[$t_session_key]))
					{
						$this->session_data_array[$t_session_key] = $_SESSION[$t_session_key];
					}
					else
					{
						$this->session_data_array[$t_session_key] = '### Session entry not present ###';
					}
				}
			}
		}
	}
	
	public function __toString()
	{
		$t_output = $this->get_output();
		return $t_output;
	}
	
	public function get_output($p_output_mode = 'file')
	{
		$t_output = '';
		
		switch($p_output_mode)
		{
			case 'screen':
			case 'html_file':
				$t_output = $this->get_html_output($p_output_mode);
				break;
			default:
				$t_output = $this->get_text_output($p_output_mode);
		}
		
		return $t_output;
	}
	
	protected function var_export($p_expression)
	{
		$t_print_r = print_r($p_expression, true);
		
		if(strpos($t_print_r, '*RECURSION*') !== false)
		{
			$t_var_export = str_replace("\n", "\n\t\t\t", print_r($p_expression, true));
		}
		else
		{
			$t_var_export = str_replace("\n", "\n\t\t\t", var_export($p_expression, true));
		}

		unset($t_print_r);
				
		return $t_var_export;
	}
	
	protected function get_html_output($p_output_mode = 'screen')
	{
		$t_output = $this->generate_style_for_html_output();
		$t_output .= '<pre style="margin: 0; padding: 0">';
		$t_output .= $this->generate_output('html_file', $p_output_mode);
		$t_output .= '</pre>';
		
		return $t_output;
	}
	
	protected function get_text_output($p_output_mode = 'file')
	{
		$t_output = str_repeat('=', 80) . "\r\n";
		$t_output .= $this->generate_output('text', $p_output_mode);
		
		return $t_output;
	}
	
	protected function generate_output($p_output_type = 'text', $p_output_mode = 'screen')
	{
		$t_div_id = $this->generate_div_id();
		$t_head = $this->generate_head_output($p_output_type, $p_output_mode, $t_div_id);
		$t_details_start_div = $this->generate_error_box_div_output($p_output_type, 'details', true, $t_div_id);
		$t_backtrace = $this->generate_backtrace_output($p_output_type, $p_output_mode);
		$t_request_data = $this->generate_request_data_output($p_output_type, $p_output_mode);
		$t_additional_info = $this->generate_additional_info_output($p_output_type);
		$t_details_end_div = $this->generate_error_box_div_output($p_output_type, 'details', false);
		
		$t_output = $t_head
					. $t_details_start_div
					. $t_backtrace
					. $t_request_data
					. $t_additional_info
					. $t_details_end_div;
		
		return $t_output;
	}
	
	protected function generate_head_output($p_output_type = 'text', $p_output_mode = 'screen', $p_div_id = 0)
	{
		$t_head_start_div = $this->generate_error_box_div_output($p_output_type, 'head');
		$t_date = $this->generate_date_output($p_output_type, $p_output_mode);
		$t_error_code = $this->generate_error_code_output($p_output_type);
		$t_message = '"' . $this->generate_text_output($p_output_type, $this->message) . '"';
		$t_filepath = '';
		$t_detail_link = '';
		$t_head_end_div = $this->generate_error_box_div_output($p_output_type, 'head', false);
		$t_div_id = $p_div_id;
		$t_backtrace_head = '';
		
		if(empty($this->backtrace_array) == false && $this->get_specific_output($p_output_mode, 'filepath'))
		{
			$t_filepath = $this->generate_filepath_output($p_output_type);
			
			if($this->get_specific_output($p_output_mode, 'backtrace') || $this->get_specific_output($p_output_mode, 'request_data'))
			{
				$t_detail_link = $this->generate_detail_link_output($p_output_type, $t_div_id);
			}
			
			if($this->get_specific_output($p_output_mode, 'backtrace'))
			{
				$t_backtrace_head .= $this->generate_backtrace_head_output($p_output_type);
			}
		}
		
		$t_head = $t_date
				. $t_error_code
				. $t_message
				. $t_filepath;
		
		$t_head = $this->generate_unfold_link_output($p_output_type, $t_div_id, $t_head, 'unfolder');
		
		$t_head = $t_head_start_div
				. $t_head
				. $t_detail_link
				. $t_head_end_div;
		
		return $t_head;
	}
	
	protected function generate_backtrace_output($p_output_type = 'text', $p_output_mode = 'screen')
	{
		$t_backtrace = '';
		
		if($this->get_specific_output($p_output_mode, 'filepath') && $this->get_specific_output($p_output_mode, 'backtrace'))
		{
			$t_count = count($this->backtrace_array);
			foreach($this->backtrace_array as $t_backtrace_array)
			{
				$t_count--;
				$t_filepath_data = $this->generate_filepath_data_output($t_count, $t_backtrace_array);
				$t_div_id = $this->generate_div_id();
				$t_subdetails_link = $this->generate_detail_link_output($p_output_type, $t_div_id);
				$t_subdetails_start_div = $this->generate_error_box_div_output($p_output_type, 'subdetails', true, $t_div_id);
				$t_class_data = $this->generate_class_data_output($p_output_type, $p_output_mode, $t_backtrace_array);
				$t_function_data = $this->generate_function_data_output($p_output_type, $p_output_mode, $t_backtrace_array);
				$t_code_snippet = $this->generate_code_snippet_output($p_output_type, $p_output_mode, $t_backtrace_array);
				$t_subdetails_end_div = $this->generate_error_box_div_output($p_output_type, 'subdetails', false);
				
				if($this->get_specific_output($p_output_type, 'code_snippet')
					|| $this->get_specific_output($p_output_type, 'class_data')
					|| $this->get_specific_output($p_output_type, 'function_data'))
				{
					$t_backtrace .= $this->generate_unfold_link_output($p_output_type, $t_div_id, $t_filepath_data, 'unfolder');
					$t_backtrace .= $t_subdetails_link;
				}
				else
				{
					$t_backtrace .= $this->generate_text_output($p_output_type, $t_filepath_data);
					$t_backtrace .= $this->generate_line_break_output($p_output_type);
				}
				
				$t_backtrace .= $t_subdetails_start_div
								. $t_class_data
								. $t_function_data
								. $t_code_snippet
								. $t_subdetails_end_div;
			}
		}
		
		return $t_backtrace;
	}
	
	protected function generate_request_data_output($p_output_type = 'text', $p_output_mode = 'screen')
	{
		$t_request_data = '';
		
		if($this->get_specific_output($p_output_mode, 'request_data'))
		{
			$t_request_data_base = $this->generate_request_data_base_output($p_output_type, $p_output_mode);
			$t_session_data = '';
			$t_post_data = '';
			$t_get_data = '';
			
			if($this->get_specific_output($p_output_mode, 'session_data') && empty($this->session_data_array) == false)
			{
				$t_session_data = $this->generate_request_data_array_output($p_output_type, 'Session', $this->session_data_array);
			}

			if(empty($this->post_data_array) == false)
			{
				$t_post_data = $this->generate_request_data_array_output($p_output_type, 'Post', $this->post_data_array);
			}

			if(empty($this->get_data_array) == false)
			{
				$t_get_data = $this->generate_request_data_array_output($p_output_type, 'Get', $this->get_data_array);
			}
			
			$t_request_data = $t_request_data_base
								. $t_session_data
								. $t_post_data
								. $t_get_data;
		}
		
		return $t_request_data;
	}
	
	protected function generate_additional_info_output($p_output_type = 'text')
	{
		$t_additional_info = '';
		
		if(empty($this->additional_info) == false)
		{
			if($p_output_type == 'html_file')
			{
				$t_additional_info .=  '<b>Information:</b>';
			}
			else
			{
				$t_additional_info .= 'Information:';
			}
			
			$t_additional_info .= $this->generate_line_break_output($p_output_type);
			$t_additional_info .= $this->generate_text_output($p_output_type, $this->additional_info);
		}
		
		return $t_additional_info;
	}
	
	protected function generate_filepath_data_output($p_position = 0, $p_backtrace_array = array())
	{
		$t_filepath = '#' . $p_position;
		
		if(isset($p_backtrace_array['file']))
		{
			$t_filepath .= '	File: ' . $p_backtrace_array['file'];
			if(isset($p_backtrace_array['line']))
			{
				$t_filepath .= ':' . $p_backtrace_array['line'];
			}
		}
		
		return $t_filepath;
	}
	
	protected function generate_class_data_output($p_output_type = 'text', $p_output_mode = 'screen', $p_backtrace_array = array())
	{
		$t_class_data = '';
		
		if($this->get_specific_output($p_output_mode, 'class_data') && isset($p_backtrace_array['class'])
			&& $p_backtrace_array['class'] != '' && $this->configuration->is_excluded_class($p_backtrace_array['class']) == false)
		{
			$t_div_id = $this->generate_div_id();

			$t_class = '	Class: ' . $p_backtrace_array['class'];
			$t_class_detail_link = '';
			$t_class_start_div = '';
			$t_class_properties = '';
			$t_class_end_div = '';
			
			if(empty($p_backtrace_array['class_property_array']) == false)
			{
				$t_class_detail_link = $this->generate_detail_link_output($p_output_type, $t_div_id);
				$t_class_start_div = $this->generate_error_box_div_output($p_output_type, 'subdetails', true, $t_div_id);
				foreach($p_backtrace_array['class_property_array'] as $t_class_property_name => $t_class_property_value)
				{
					$t_class_properties .= $this->generate_variable_output($p_output_type, $t_class_property_name, $t_class_property_value);
				}
				$t_class_end_div = $this->generate_error_box_div_output($p_output_type, 'subdetails', false);
				
				$t_class_data = $this->generate_unfold_link_output($p_output_type, $t_div_id, $t_class, 'unfolder');
				$t_class_data .= $t_class_detail_link
							. $t_class_start_div
							. $t_class_properties
							. $t_class_end_div;
			}
			else
			{
				$t_class_data = $t_class . $this->generate_line_break_output($p_output_type);
			}
		}
		
		return $t_class_data;
	}
	
	protected function generate_function_data_output($p_output_type = 'text', $p_output_mode = 'screen', $p_backtrace_array = array())
	{
		$t_function_data = '';
		
		if($this->get_specific_output($p_output_mode, 'function_data') && isset($p_backtrace_array['function']) && $p_backtrace_array['function'] != ''
			&& (isset($p_backtrace_array['class']) == false || $p_backtrace_array['class'] == '' || isset($p_backtrace_array['class'])
			&& $p_backtrace_array['class'] != '' && $this->configuration->is_excluded_class($p_backtrace_array['class']) == false))
		{
			$t_div_id = $this->generate_div_id();
			
			if(isset($p_backtrace_array['class']) && $p_backtrace_array['class'] != '')
			{
				$t_function = '	Method: ' . $p_backtrace_array['function'];
			}
			else
			{
				$t_function = '	Function: ' . $p_backtrace_array['function'];
			}

			if($p_output_type == 'html_file')
			{
				$t_function = $this->htmlentities_wrapper($t_function);
			}
			
			$t_function_detail_link = '';
			$t_function_start_div = '';
			$t_function_parameters = '';
			$t_function_end_div = '';
			if(empty($p_backtrace_array['function_parameter_array']) == false)
			{
				$t_function_detail_link = $this->generate_detail_link_output($p_output_type, $t_div_id);
				$t_function_start_div = $this->generate_error_box_div_output($p_output_type, 'subdetails', true, $t_div_id);
				foreach($p_backtrace_array['function_parameter_array'] as $t_function_parameter_name => $t_function_parameter_value)
				{
					$t_function_parameters .= $this->generate_variable_output($p_output_type, $t_function_parameter_name, $t_function_parameter_value);
				}
				$t_function_end_div = $this->generate_error_box_div_output($p_output_type, 'subdetails', false);
				
				$t_function_data = $this->generate_unfold_link_output($p_output_type, $t_div_id, $t_function, 'unfolder');
				$t_function_data .= $t_function_detail_link
								. $t_function_start_div
								. $t_function_parameters
								. $t_function_end_div;
			}
			else
			{
				$t_function_data = $t_function . $this->generate_line_break_output($p_output_type);
			}
		}
		
		return $t_function_data;
	}
	
	protected function generate_code_snippet_output($p_output_type = 'text', $p_output_mode = 'screen', $p_backtrace_array = array())
	{
		$t_code = '';
		
		if($this->get_specific_output($p_output_mode, 'code_snippet') && is_readable($p_backtrace_array['file']) && $p_backtrace_array['line'] > 0)
		{
			$t_lines_array = file($p_backtrace_array['file']);
			$t_code = '	Code:' . $this->generate_line_break_output($p_output_type);

			if(isset($t_lines_array[$p_backtrace_array['line'] - 3]))
			{
				$t_code .= $this->generate_code_line($p_output_type, $t_lines_array, $p_backtrace_array['line'] - 3);
			}
			if(isset($t_lines_array[$p_backtrace_array['line'] - 2]))
			{
				$t_code .= $this->generate_code_line($p_output_type, $t_lines_array, $p_backtrace_array['line'] - 2);
			}
			if(isset($t_lines_array[$p_backtrace_array['line'] - 1]))
			{
				$t_code .= $this->generate_code_line($p_output_type, $t_lines_array, $p_backtrace_array['line'] - 1, true);
			}
			if(isset($t_lines_array[$p_backtrace_array['line']]))
			{
				$t_code .= $this->generate_code_line($p_output_type, $t_lines_array, $p_backtrace_array['line']);
			}
			if(isset($t_lines_array[$p_backtrace_array['line'] + 1]))
			{
				$t_code .= $this->generate_code_line($p_output_type, $t_lines_array, $p_backtrace_array['line'] + 1);
			}

			unset($t_lines_array);
		}
		
		return $t_code;
	}
	
	protected function generate_request_data_base_output($p_output_type = 'text', $p_output_mode = 'screen')
	{
		$t_request_data = '';
		
		if($p_output_type == 'html_file')
		{
			$t_request_data .= '<b>Request:</b> ';
		}
		else
		{
			$t_request_data .= 'Request: ';
		}
		$t_request_data .= $this->request_type . ' ' . $this->generate_text_output($p_output_type, $this->request_url);
		$t_div_id = $this->generate_div_id();
		$t_request_data = $this->generate_unfold_link_output($p_output_type, $t_div_id, $t_request_data, 'unfolder');
		$t_request_data .= $this->generate_detail_link_output($p_output_type, $t_div_id);
		$t_request_data .= $this->generate_error_box_div_output($p_output_type, 'subdetails', true, $t_div_id);
		
		$t_request_data .= '	- duration: ~' . $this->request_duration . 'ms' . $this->generate_line_break_output($p_output_type);
		$t_request_data .= '	- server: ' . $this->generate_text_output($p_output_type, $this->server) . $this->generate_line_break_output($p_output_type);
		$t_request_data .= '	- server address: ' . $this->server_address . $this->generate_line_break_output($p_output_type);
		$t_request_data .= '	- user agent: ' . $this->generate_text_output($p_output_type, $this->user_agent) . $this->generate_line_break_output($p_output_type);
		if($p_output_mode != 'screen')
		{
			$t_request_data .= '	- remote address: ' . md5($this->remote_address) . $this->generate_line_break_output($p_output_type);
		}
		
		$t_request_data .= $this->generate_error_box_div_output($p_output_type, 'subdetails', false);
		
		return $t_request_data;
	}
	
	protected function generate_request_data_array_output($p_output_type = 'text', $p_request_data_type = 'Session', $p_request_data_array = array())
	{
		$t_data = '';
		
		if($p_output_type == 'html_file')
		{
			$t_data .= '<b>' . $p_request_data_type . ':</b> ';
		}
		else
		{
			$t_data .= $p_request_data_type . ': ';
		}
		
		$t_div_id = $this->generate_div_id();
		if(empty($p_request_data_array) == false)
		{
			$t_data = $this->generate_unfold_link_output($p_output_type, $t_div_id, $t_data, 'unfolder');
			$t_data .= $this->generate_detail_link_output($p_output_type, $t_div_id);
		}
		$t_data .= $this->generate_error_box_div_output($p_output_type, 'subdetails', true, $t_div_id);
		
		foreach($p_request_data_array as $t_key => $t_value)
		{
			$t_data .= $this->generate_variable_output($p_output_type, $t_key, str_replace("\n", "\n\t\t", print_r($t_value, true)), 1, '');
		}
		
		$t_data .= $this->generate_error_box_div_output($p_output_type, 'subdetails', false);
		
		return $t_data;
	}
	
	protected function generate_line_break_output($p_output_type = 'text')
	{
		switch($p_output_type)
		{
			case 'html_file':
				return '<br/>';
			case 'text':
			default:
				return "\r\n";
		}
	}
	
	protected function generate_error_box_div_output($p_output_type = 'text', $p_div_class = 'head', $p_opening = true, $p_div_id = 0)
	{
		$t_error_box_div = '';
		if($p_output_type != 'html_file')
		{
			return $this->generate_line_break_output($p_output_type);
		}
		
		$t_div_id = '';
		$t_div_style = '';
		if(empty($p_div_id) == false)
		{
			$t_div_id = ' id="error_' . $p_div_id . '"';
			$t_div_style = ' style="display: none;"';
		}
		
		if($p_opening)
		{
			$t_error_box_div = '<span' . $t_div_id . ' class="gambio_error_box ' . $p_div_class . ' ' . $this->level . '"' . $t_div_style . '>';
		}
		else
		{
			$t_error_box_div = '</span>';
		}
		
		return $t_error_box_div;
	}
	
	protected function generate_error_code_output($p_output_type = 'text')
	{
		$t_error_code = $this->level_type;
		
		if($this->error_code > 0)
		{
			$t_error_code .= '(' . $this->error_code . ')';
		}
		
		$t_error_code .= ': ';
		
		if($p_output_type == 'html_file')
		{
			$t_error_code = '<b>' . $t_error_code . '</b>';
		}
		
		return $t_error_code;
	}
	
	protected function generate_date_output($p_output_type = 'text', $p_output_mode = 'file')
	{
		$t_date = '';
		
		if($p_output_mode != 'screen')
		{
			$t_date .= $this->date;

			if(empty($this->remote_address) == false)
			{
				$t_date .= ' (' . md5($this->remote_address) . ')';
			}

			$t_date .= ' ';
		}
		
		return $t_date;
	}
	
	protected function generate_text_output($p_output_type = 'text', $p_text = '')
	{
		$t_text = $p_text;
		
		if($p_output_type == 'html_file')
		{
			$t_text = $this->htmlentities_wrapper($p_text);
		}
		
		return $t_text;
	}
	
	protected function generate_filepath_output($p_output_type = 'text')
	{
		$t_filepath = ' in ' . $this->backtrace_array[0]['file'] . ':' . $this->backtrace_array[0]['line'];
		
		return $t_filepath;
	}
	
	protected function generate_detail_link_output($p_output_type = 'text', $p_div_id = 0)
	{
		$t_detail_link = '';
		
		if($p_output_type != 'html_file' || empty($p_div_id))
		{
			return $t_detail_link;
		}
		
		$t_detail_link .= ' (' . $this->generate_unfold_link_output($p_output_type, $p_div_id, 'Details') . ')' . $this->generate_line_break_output($p_output_type);
		
		return $t_detail_link;
	}
	
	protected function generate_unfold_link_output($p_output_type = 'text', $p_div_id = 0, $p_link_text = '', $p_class = '')
	{
		$t_unfold_link = '';
		
		if($p_output_type != 'html_file' || empty($p_div_id))
		{
			return $p_link_text;
		}
		
		$t_unfold_link = '<a href="#" class="' . $p_class . '" onclick="if(document.getElementById(\'error_' . $p_div_id . '\').style.display == \'none\'){document.getElementById(\'error_' . $p_div_id . '\').style.display=\'block\';}else{document.getElementById(\'error_' . $p_div_id . '\').style.display=\'none\';}return false;">' . $p_link_text . '</a>';
		
		return $t_unfold_link;
	}
	
	protected function generate_backtrace_head_output($p_output_type = 'text')
	{
		switch($p_output_type)
		{
			case 'html_file':
				return '<b>Backtrace:</b>' . $this->generate_line_break_output($p_output_type);
			case 'text':
			default:
				return "Backtrace:" . $this->generate_line_break_output($p_output_type);
		}
	}
	
	protected function generate_code_line($p_output_type = 'text', $p_lines_array = array(), $p_line = 0, $p_is_actual_line = false)
	{
		$t_code = '';

		if(isset($p_lines_array[$p_line]))
		{
			$t_code = ($p_line + 1) . ': ' . $this->generate_text_output($p_output_type, $p_lines_array[$p_line]);
		}

		if($p_is_actual_line)
		{
			if($p_output_type == 'html_file')
			{
				$t_code = "├─\t<b>line " . $t_code . '</b>';
			}
			else
			{
				$t_code = "├─\tline " . $t_code;
			}
		}
		else
		{
			$t_code = "│\tline " . $t_code;
		}

		return $t_code;
	}
	
	protected function generate_variable_output($p_output_type = 'text', $p_variable_name = '', $p_variable_value = '', $p_indent_depth = 2, $p_variable_prefix = '$')
	{
		$t_variable = '';
		$t_variable_lines = substr_count($p_variable_value, "\n") + 1;
		$t_max_variable_lines = 3;
		$t_indent = '';
		
		if(empty($p_variable_name))
		{
			return $t_variable;
		}
		
		for($i = 0; $i < $p_indent_depth; $i++)
		{
			$t_indent .= '	';
		}
		
		$t_variable_value = $p_variable_value;
		if($t_variable_lines > $t_max_variable_lines && $p_output_type == 'html_file')
		{
			$t_variable_value = '[...]';
		}
		
		$t_variable .= $t_indent . '- ' . $p_variable_prefix . $p_variable_name . ': ' . $this->generate_text_output($p_output_type, $t_variable_value);
		
		if($t_variable_lines > $t_max_variable_lines && $p_output_type == 'html_file')
		{
			$t_div_id = $this->generate_div_id();
			$t_variable = $this->generate_unfold_link_output($p_output_type, $t_div_id, $t_variable, 'unfolder');
			$t_variable .= $this->generate_line_break_output($p_output_type);
			$t_variable .= $this->generate_error_box_div_output($p_output_type, 'subdetails', true, $t_div_id);
			$t_variable .= $t_indent . '	' . $this->generate_text_output($p_output_type, $p_variable_value);
			$t_variable .= $this->generate_error_box_div_output($p_output_type, 'subdetails', false);
		}
		else
		{
			$t_variable .= $this->generate_line_break_output($p_output_type);
		}
		
		return $t_variable;
	}
	
	protected function generate_style_for_html_output()
	{
		$t_style = '<style type="text/css">
						.gambio_error_box {
							font-size: 1em !important;
							color:  black !important;
							margin: 0 !important;
							padding: 0 !important;
							display: block;
						}
						
						.gambio_error_box a.unfolder {
							color: #000000 !important;
							text-decoration: none !important;
							outline: none !important;
							font-family: monospace !important;
							font-size: 12px !important;
						}
						
						.gambio_error_box a.unfolder:hover {
							color: #0000FF !important;
							text-decoration: underline !important;
						}
						
						.gambio_error_box.head.error {
							background-color: #ff9797 !important;
						}
						
						.gambio_error_box.details.error {
							background-color: #ffcaca !important;
						}
						
						.gambio_error_box.subdetails.error {
							background-color: #ffdddd !important;
						}
						
						.gambio_error_box.head.warning {
							background-color: #eecc88 !important;
						}
						
						.gambio_error_box.details.warning {
							background-color: #ffeeaa !important;
						}
						
						.gambio_error_box.subdetails.warning {
							background-color: #fff5cc !important;
						}
						
						.gambio_error_box.head.notice {
							background-color: #9797ff !important;
						}
						
						.gambio_error_box.details.notice {
							background-color: #cacaff !important;
						}
						
						.gambio_error_box.subdetails.notice {
							background-color: #ddddff !important;
						}
						
						.gambio_error_box a {
							color:  blue !important;
						}
					</style>';
		return $t_style;
	}
	
	protected function generate_div_id()
	{
		return rand(10000, 99999);
	}

	public function htmlentities_wrapper($p_string)
	{
		if(defined('ENT_HTML401'))
		{
			$t_flags = ENT_COMPAT | ENT_HTML401;
		}
		else
		{
			$t_flags = ENT_COMPAT;
		}

		// search for UTF-8 characters
		if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $p_string))
		{
			$t_encoding = 'UTF-8';
		}
		elseif(isset($_SESSION['language_charset']))
		{
			$t_allowed_charsets_array = array();
			$t_allowed_charsets_array[] = 'ISO-8859-1';
			$t_allowed_charsets_array[] = 'ISO8859-1';
			$t_allowed_charsets_array[] = 'ISO-8859-15';
			$t_allowed_charsets_array[] = 'ISO8859-15';
			$t_allowed_charsets_array[] = 'UTF-8';
			$t_allowed_charsets_array[] = 'cp866';
			$t_allowed_charsets_array[] = 'ibm866';
			$t_allowed_charsets_array[] = '866';
			$t_allowed_charsets_array[] = 'cp1251';
			$t_allowed_charsets_array[] = 'Windows-1251';
			$t_allowed_charsets_array[] = 'win-1251';
			$t_allowed_charsets_array[] = '1251';
			$t_allowed_charsets_array[] = 'cp1252';
			$t_allowed_charsets_array[] = 'Windows-1252';
			$t_allowed_charsets_array[] = '1252';
			$t_allowed_charsets_array[] = 'KOI8-R';
			$t_allowed_charsets_array[] = 'koi8-ru';
			$t_allowed_charsets_array[] = 'koi8r';
			$t_allowed_charsets_array[] = 'BIG5';
			$t_allowed_charsets_array[] = '950';
			$t_allowed_charsets_array[] = 'GB2312';
			$t_allowed_charsets_array[] = '936';
			$t_allowed_charsets_array[] = 'BIG5-HKSCS';
			$t_allowed_charsets_array[] = 'Shift_JIS';
			$t_allowed_charsets_array[] = 'SJIS';
			$t_allowed_charsets_array[] = '932';
			$t_allowed_charsets_array[] = 'EUC-JP';
			$t_allowed_charsets_array[] = 'EUCJP';

			$t_key = array_search(strtolower(trim((string)$_SESSION['language_charset'])), array_map('strtolower', $t_allowed_charsets_array));
			if($t_key !== false)
			{
				$t_encoding = $t_allowed_charsets_array[$t_key];
			}
		}
		else
		{
			$t_encoding = 'ISO-8859-1';
		}

		return htmlentities($p_string, $t_flags, $t_encoding);
	}
	
	public function build_request_data_active()
	{
		return $this->configuration->get_any_by_output_type($this->level, 'request_data');
	}

	public function build_backtrace_active()
	{
		return $this->configuration->get_any_by_output_type($this->level, 'backtrace');
	}

	public function build_code_snippet_active()
	{
		return $this->configuration->get_any_by_output_type($this->level, 'code_snippet');
	}

	public function build_function_data_active()
	{
		return $this->configuration->get_any_by_output_type($this->level, 'function_data');
	}

	public function build_class_data_active()
	{
		return $this->configuration->get_any_by_output_type($this->level, 'class_data');
	}

	public function build_session_data_active()
	{
		return $this->configuration->get_any_by_output_type($this->level, 'session_data');
	}
	
	protected function is_excluded_call($p_call_array)
	{
		return	isset($p_call_array['file']) && $this->configuration->is_excluded_file($p_call_array['file'])
				&& isset($p_call_array['class']) && $this->configuration->is_excluded_class($p_call_array['class'])
				|| isset($p_call_array['file']) == false && isset($p_call_array['class'])
				&& $this->configuration->is_excluded_class($p_call_array['class'])
				|| isset($p_call_array['file']) && isset($p_call_array['class']) == false
				&& $this->configuration->is_excluded_file($p_call_array['file']);
	}
	
	protected function get_specific_output($p_output_type = 'file', $p_output = 'output')
	{
		return $this->configuration->get($this->level, $p_output_type, $p_output);
	}
	
	protected function censor(array $p_array)
	{
		$t_array = $p_array;
		
		if(isset($t_array['password']))
		{
			$t_array['password'] = '*****';
		}

		if(isset($t_array['FTP_PASSWORD']))
		{
			$t_array['FTP_PASSWORD'] = '*****';
		}
			
		return $t_array;
	}
	
	public function get_group()
	{
		return $this->group;
	}

	public function get_level()
	{
		return $this->level;
	}
	
	public function get_level_type()
	{
		return $this->level_type;
	}

	public function get_configuration()
	{
		return $this->configuration;
	}

	public function get_error_code()
	{
		return $this->error_code;
	}

	public function get_message()
	{
		return $this->message;
	}

	public function get_additional_info()
	{
		return $this->additional_info;
	}
	
	public function get_log_file_name()
	{
		return $this->log_file_name;
	}

	public function get_backtrace_array()
	{
		return $this->backtrace_array;
	}

	public function get_file()
	{
		return $this->file;
	}

	public function get_line()
	{
		return $this->line;
	}

	public function get_request_type()
	{
		return $this->request_type;
	}

	public function get_request_url()
	{
		return $this->request_url;
	}

	public function get_request_duration()
	{
		return $this->request_duration;
	}

	public function get_server()
	{
		return $this->server;
	}

	public function get_server_address()
	{
		return $this->server_address;
	}

	public function get_remote_address()
	{
		return $this->remote_address;
	}

	public function get_user_agent()
	{
		return $this->user_agent;
	}

	public function get_date()
	{
		return $this->date;
	}

	public function get_session_data_array()
	{
		return $this->session_data_array;
	}

	public function get_post_data_array()
	{
		return $this->post_data_array;
	}

	public function get_get_data_array()
	{
		return $this->get_data_array;
	}
	
	public function get_output_array()
	{
		return $this->output_array;
	}

	public function set_group($p_group)
	{
		$this->group = $p_group;
	}

	public function set_level($p_level)
	{
		$this->level = $p_level;
	}
	
	public function set_level_type($p_level_type)
	{
		$this->level_type = $p_level_type;
	}

	public function set_configuration($p_configuration)
	{
		$this->configuration = $p_configuration;
	}

	
	public function set_error_code($p_error_code)
	{
		$this->error_code = $p_error_code;
	}

	public function set_message($p_message)
	{
		$this->message = $p_message;
	}

	public function set_additional_info($p_additional_info)
	{
		$this->additional_info = $p_additional_info;
	}
	
	public function set_log_file_name($p_log_file_name)
	{
		if(is_string($p_log_file_name))
		{
			$this->log_file_name = basename($p_log_file_name);
		}
	}

	public function set_backtrace_array(array $p_backtrace_array)
	{
		$this->backtrace_array = $p_backtrace_array;
	}

	public function set_file($p_file)
	{
		$this->file = $p_file;
	}

	public function set_line($p_line)
	{
		$this->line = $p_line;
	}

	public function set_request_type($p_request_type)
	{
		$this->request_type = $p_request_type;
	}

	public function set_request_url($p_request_url)
	{
		$this->request_url = $p_request_url;
	}

	public function set_request_duration($p_request_duration)
	{
		$this->request_duration = $p_request_duration;
	}

	public function set_server($p_server)
	{
		$this->server = $p_server;
	}

	public function set_server_address($p_server_address)
	{
		$this->server_address = $p_server_address;
	}

	public function set_remote_address($p_remote_address)
	{
		$this->remote_address = $p_remote_address;
	}

	public function set_user_agent($p_user_agent)
	{
		$this->user_agent = $p_user_agent;
	}

	public function set_date($p_date)
	{
		$this->date = $p_date;
	}

	public function set_session_data_array($p_session_data_array)
	{
		$this->session_data_array = $p_session_data_array;
	}

	public function set_post_data_array($p_post_data_array)
	{
		$this->post_data_array = $p_post_data_array;
	}

	public function set_get_data_array($p_get_data_array)
	{
		$this->get_data_array = $p_get_data_array;
	}
	
	public function set_output_array($p_output_array)
	{
		$this->output_array = $p_output_array;
	}
}