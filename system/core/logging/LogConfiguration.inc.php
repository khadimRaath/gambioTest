<?php
/* --------------------------------------------------------------
  LogConfiguration.inc.php 2016-03-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(SHOP_ROOT . 'system/core/logging/LogConfigurationPresets.inc.php');

class LogConfiguration
{
	protected $group_name;
	protected $configuration_array;
	protected $session_keys;
	
	protected $level_array;
	protected $output_type_array;
	protected $output_array;
	protected $default_session_keys;
	protected $excluded_files_array;
	protected $excluded_classes_array;
	
	public function __construct($p_group_name = '', $p_load_debug_configuration = true)
	{
		$this->group_name = $p_group_name;
		$this->init_default_configuration_arrays();
		$this->session_keys = $this->default_session_keys;
		$this->reset_configuration_array();
		
		if(empty($this->group_name))
		{
			$this->restore_default_preset();
		}
		else
		{
			$this->load_group_preset();
		}
		
		if($p_load_debug_configuration)
		{
			$this->load_debug_configuration();
		}
	}
	
	protected function init_default_configuration_arrays()
	{
		$this->level_array = array(
			'error',
			'warning',
			'notice'
		);
		
		$this->output_type_array = array(
			'screen',
			'file',
			'html_file',
			'mail'
		);
		
		$this->output_array = array(
			'output',
			'filepath',
			'backtrace',
			'request_data',
			'code_snippet',
			'class_data',
			'function_data',
			'session_data'
		);
		
		$this->default_session_keys = array(
			'tpl',
			'MOBILE_ACTIVE',
			'language',
			'languages_id',
			'language_charset',
			'language_code',
			'currency',
			'customers_status',
			'cart',
			'wishList',
			'customer_id',
			'payment',
			'shipping',
			'cartID',
			'sendto',
			'billto'
		);
		
		$this->excluded_files_array = array(
			'ErrorHandler.php',
			'LogEvent.inc.php',
			'LogControl.inc.php',
			'LogConfiguration.inc.php',
			'LogConfigurationPresets.inc.php',
			'GProtector.inc.php',
			'GXLogConnector.inc.php',
			'start.inc.php'
		);
		
		$this->excluded_classes_array = array(
			'ErrorHandler',
			'LogEvent',
			'LogControl',
			'LogConfiguration',
			'LogConfigurationPresets',
			'GProtector',
			'GXLogConnector'
		);
		
		return true;
	}
	
	public function reset_configuration_array()
	{
		$this->configuration_array = array();
		$this->init_default_configuration_levels(false, false);
		return true;
	}
	
	public function restore_default_preset()
	{
		$this->reset_configuration_array();
		LogConfigurationPresets::load_default_preset($this);
		return true;
	}
	
	public function load_group_preset($p_group_name = '', $p_replace_group_name = true, $p_suppress_db = false)
	{
		$t_group_name = $this->group_name;
		if(empty($p_group_name) == false)
		{
			$t_group_name = $p_group_name;
			
			if($p_replace_group_name)
			{
				$this->group_name = $t_group_name;
			}
		}
		
		if($this->is_shop_environment())
		{
			$t_sql = '	SELECT
							*
						FROM
							log_groups lg
						WHERE
							lg.name LIKE "' . $t_group_name . '"';
			$t_result = xtc_db_query($t_sql, 'db_link', true, false);
		}
		
		if($p_suppress_db == false && $this->load_db_configuration($t_group_name) && mysqli_num_rows($t_result) > 0)
		{
			return true;
		}
		
		if($this->preset_exists())
		{
			call_user_func(array('LogConfigurationPresets', 'load_group_' . $t_group_name . '_preset'), $this);
			return true;
		}
		
		$this->reset_configuration_array();
		
		return true;
	}
	
	public function load_db_configuration($p_group = '')
	{
		if($this->is_shop_environment())
		{
			$t_sql = '	SELECT
							ll.name AS level,
							lot.name AS output_type,
							lo.name AS output
						FROM
							log_groups lg,
							log_configuration lc
						LEFT JOIN
							log_levels ll USING (log_level_id)
						LEFT JOIN
							log_output_types lot USING (log_output_type_id)
						LEFT JOIN
							log_outputs lo USING (log_output_id)
						WHERE
							lc.log_group_id = lg.log_group_id AND
							lg.name LIKE "' . $p_group . '"';
			$t_result = xtc_db_query($t_sql, 'db_link', true, false);
			
			$this->set_for_all(false, false);
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$this->set($t_row['level'], $t_row['output_type'], $t_row['output']);
			}
			
			return true;
		}
		
		return false;
	}
	
	public function preset_exists($p_group_name = '')
	{
		$t_group_name = $this->group_name;
		
		if(empty($p_group_name) == false)
		{
			$t_group_name = $p_group_name;
		}
		
		$t_method_name = 'load_group_' . $t_group_name . '_preset';
		return method_exists('LogConfigurationPresets', $t_method_name);
	}
	
	public function load_debug_configuration()
	{
		if(isset($GLOBALS['coo_debugger']))
		{
			foreach($this->level_array as $t_level)
			{
				if($GLOBALS['coo_debugger']->is_enabled('log_' . $t_level))
				{
					foreach($this->output_type_array as $t_output_type)
					{
						foreach($this->output_array as $t_output)
						{
							if($GLOBALS['coo_debugger']->is_enabled('log_' . $t_output_type . '_' . $t_output))
							{
								$this->set_for_single_output($t_level, $t_output_type, $t_output);
							}
						}
					}
				}
			}
		}
	}
	
	protected function init_default_configuration_levels($p_default_value = true, $p_merge = true)
	{
		foreach($this->level_array as $t_level)
		{
			$this->set_for_single_level($t_level, $p_default_value, $p_merge);
		}
		return true;
	}
	
	protected function init_default_configuration_output_types($p_level, $p_default_value = true, $p_merge = true)
	{
		foreach($this->output_type_array as $t_output_type)
		{
			$this->set_for_single_output_type($p_level, $t_output_type, $p_default_value, $p_merge);
		}
		return true;
	}
	
	protected function init_default_configuration_outputs($p_level, $p_output_type, $p_default_value = true, $p_merge = true)
	{
		foreach($this->output_array as $t_output)
		{
			$this->set_for_single_output($p_level, $p_output_type, $t_output, $p_default_value, $p_merge);
		}
		return true;
	}
	
	public function set_for_single_level($p_level, $p_default_value = true, $p_merge = true)
	{
		if(isset($this->configuration_array[$p_level]) == false)
		{
			$this->configuration_array[$p_level] = array();
		}
		$this->init_default_configuration_output_types($p_level, $p_default_value, $p_merge);
		return true;
	}
	
	public function set_for_single_output_type($p_level, $p_output_type, $p_default_value = true, $p_merge = true)
	{
		if(isset($this->configuration_array[$p_level]) == false)
		{
			$this->set_for_single_level($p_level, $p_default_value, $p_merge);
		}
		if(isset($this->configuration_array[$p_level][$p_output_type]) == false)
		{
			$this->configuration_array[$p_level][$p_output_type] = array();
		}
		$this->init_default_configuration_outputs($p_level, $p_output_type, $p_default_value, $p_merge);
		return true;
	}
	
	public function set_for_single_output($p_level, $p_output_type, $p_output, $p_default_value = true, $p_merge = true)
	{
		if(isset($this->configuration_array[$p_level]) == false)
		{
			$this->set_for_single_level($p_level, $p_default_value, $p_merge);
		}
		if(isset($this->configuration_array[$p_level][$p_output_type]) == false)
		{
			$this->set_for_single_output_type($p_level, $p_output_type, $p_default_value, $p_merge);
		}
		if(isset($this->configuration_array[$p_level][$p_output_type][$p_output]) && $p_merge)
		{
			$this->configuration_array[$p_level][$p_output_type][$p_output] = $this->configuration_array[$p_level][$p_output_type][$p_output] || $p_default_value;
		}
		else
		{
			$this->configuration_array[$p_level][$p_output_type][$p_output] = $p_default_value;
		}
		return true;
	}
	
	public function set_for_all($p_new_value = true, $p_merge = true)
	{
		$this->init_default_configuration_levels($p_new_value, $p_merge);
	}
	
	public function set_for_level($p_level, $p_new_value = true, $p_merge = true)
	{
		$this->set_for_single_level($p_level, $p_new_value, $p_merge);
		return true;
	}
	
	public function set_for_output_type($p_output_type, $p_new_value = true, $p_merge = true)
	{
		foreach($this->configuration_array as $t_level => $t_output_type_array)
		{
			$this->set_for_single_output_type($t_level, $p_output_type, $p_new_value, $p_merge);
		}
		return true;
	}
	
	public function set_for_output($p_output, $p_new_value = true, $p_merge = true)
	{
		foreach($this->configuration_array as $t_level => &$t_output_type_array)
		{
			foreach($t_output_type_array as $t_output_type => $t_output_array)
			{
				$this->set_for_single_output($t_level, $t_output_type, $p_output, $p_new_value, $p_merge);
			}
		}
		return true;
	}
	
	public function set_for_level_and_output_type($p_level, $p_output_type, $p_new_value = true, $p_merge = true)
	{
		foreach($this->configuration_array[$p_level][$p_output_type] as $t_output => $t_old_value)
		{
			$this->set_for_single_output($p_level, $p_output_type, $t_output, $p_new_value, $p_merge);
		}
		return true;
	}
	
	public function set_for_level_and_output($p_level, $p_output, $p_new_value = true, $p_merge = true)
	{
		foreach($this->configuration_array[$p_level] as $t_output_type => $t_output_array)
		{
			$this->set_for_single_output($p_level, $t_output_type, $p_output, $p_new_value, $p_merge);
		}
		return true;
	}
	
	public function set_for_output_type_and_output($p_output_type, $p_output, $p_new_value = true, $p_merge = true)
	{
		foreach($this->configuration_array as $t_level => $t_output_type_array)
		{
			$this->set_for_single_output($t_level, $p_output_type, $p_output, $p_new_value, $p_merge);
		}
		return true;
	}
	
	public function is_active_output_type($p_level, $p_output_type)
	{
		return isset($this->configuration_array[$p_level])
			&& isset($this->configuration_array[$p_level][$p_output_type])
			&& isset($this->configuration_array[$p_level][$p_output_type]['output'])
			&& $this->configuration_array[$p_level][$p_output_type]['output'];
	}
	
	public function is_excluded_file($p_file)
	{
		$t_file = preg_replace('/(.+?)(\(.+)/', '$1', (string)$p_file); 
		$t_file = basename($t_file);
		return in_array($t_file, $this->excluded_files_array);
	}
	
	public function is_excluded_class($p_class)
	{
		return in_array($p_class, $this->excluded_classes_array);
	}
	
	public function merge(LogConfiguration $p_coo_merge_configuration)
	{
		$t_merge_configuration_array = $p_coo_merge_configuration->get_configuration_array();
		
		foreach($t_merge_configuration_array as $t_level => $t_output_type_array)
		{
			if(isset($this->configuration_array[$t_level]) == false)
			{
				$this->set_for_single_level($t_level, false);
			}
			
			foreach($t_output_type_array as $t_output_type => $t_output_array)
			{
				if(isset($this->configuration_array[$t_level][$t_output_type]) == false)
				{
					$this->set_for_single_output_type($t_level, $t_output_type, false);
				}
				
				foreach($t_output_array as $t_output => $t_value)
				{
					$this->set_for_single_output($t_level, $t_output_type, $t_output);
				}
			}
		}
	}
	
	public function get($p_level, $p_output_type, $p_output)
	{
		return isset($this->configuration_array[$p_level])
			&& isset($this->configuration_array[$p_level][$p_output_type])
			&& isset($this->configuration_array[$p_level][$p_output_type][$p_output])
			&& $this->configuration_array[$p_level][$p_output_type][$p_output];
	}
	
	public function set($p_level, $p_output_type, $p_output, $p_default_value = true, $p_merge = true)
	{
		$this->set_for_single_output($p_level, $p_output_type, $p_output, $p_default_value, $p_merge);
	}
	
	public function get_any_by_output_type($p_level, $p_output)
	{
		if(isset($this->configuration_array[$p_level]) == false)
		{
			return false;
		}
		
		foreach($this->configuration_array[$p_level] as $t_output_array)
		{
			if(isset($t_output_array[$p_output]) == false)
			{
				continue;
			}
			if($t_output_array[$p_output])
			{
				return true;
			}
		}
		return false;
	}
	
	public function __toString()
	{
		$t_return = '
			<style type="text/css">
				.log_configuration_visualization_table
				{
					border-collapse: collapse !important;
					color: #000000 !important;
				}
				
				.log_configuration_visualization_table th, .log_configuration_visualization_table td
				{
					border: solid 1px #000000 !important;
					padding: 3px 8px !important;
				}
				
				.log_configuration_visualization_table td.log_configuration_enabled
				{
					background-color: #66EE88 !important;
				}
				
				.log_configuration_visualization_table td.log_configuration_disabled
				{
					background-color: #EE6688 !important;
				}
			</style>
			
			<table class="log_configuration_visualization_table">';
		foreach($this->configuration_array as $t_level => $t_ouput_type_array)
		{
			$t_header_flag = false;
			foreach($t_ouput_type_array as $t_ouput_type => $t_ouput_array)
			{
				$t_return .= '<tr>';
				if($t_header_flag == false)
				{
					$t_return .= '<th>' . $t_level . '</th>';
					foreach($t_ouput_array as $t_ouput => $t_value)
					{
						$t_return .= '<td>' . $t_ouput. '</td>';
					}
					$t_return .= '</tr><tr>';
					$t_header_flag = true;
				}
				
				$t_return .= '<td>' . $t_ouput_type . '</td>';
				
				foreach($t_ouput_array as $t_ouput => $t_value)
				{
					$t_output_class = 'disabled';
					if($t_value)
					{
						$t_output_class = 'enabled';
					}
					$t_return .= '<td class="log_configuration_' . $t_output_class . '"></td>';
				}
				$t_return .= '</tr>';
			}
		}
		$t_return .= '</table><br/>';
		
		return $t_return;
	}


	protected function is_shop_environment()
	{
		$t_sql = 'SELECT * FROM log_groups';

		return defined('STORE_NAME') && function_exists('xtc_db_query') && isset($GLOBALS['db_link'])
		       && $GLOBALS['db_link'] instanceof mysqli
		       && @xtc_db_query($t_sql, 'db_link', true, false);
	}
	
	public function get_configuration_html_form()
	{
		$coo_text_manager = null;
		
		if($this->is_shop_environment())
		{
			$coo_text_manager = LanguageTextManager::get_instance();
			$t_section = 'lang/' . $_SESSION['language'] . '/admin/configuration.php';
			$t_language_id = (int)$_SESSION['languages_id'];
		}
		
		$t_level_text_array = array();
		foreach($this->level_array as $t_level)
		{
			if($this->is_shop_environment())
			{
				$t_level_text_array[$t_level] = $coo_text_manager->get_text('LOGGING_LEVEL_' . strtoupper($t_level), $t_section, $t_language_id);
			}
			else
			{
				$t_level_text_array[$t_level] = $t_level;
			}
		}
		
		$t_output_type_text_array = array();
		foreach($this->output_type_array as $t_output_type)
		{
			if($this->is_shop_environment())
			{
				$t_output_type_text_array[$t_output_type] = $coo_text_manager->get_text('LOGGING_OUTPUT_TYPE_' . strtoupper($t_output_type), $t_section, $t_language_id);
			}
			else
			{
				$t_output_type_text_array[$t_output_type] = $t_output_type;
			}
		}
		
		$t_output_text_array = array();
		foreach($this->output_array as $t_output)
		{
			if($this->is_shop_environment())
			{
				$t_output_text_array[$t_output] = $coo_text_manager->get_text('LOGGING_OUTPUT_' . strtoupper($t_output), $t_section, $t_language_id);
			}
			else
			{
				$t_output_text_array[$t_output] = $t_output;
			}
		}
		
		$t_form = '	<style>
						.log_configuration_table
						{
							width: 49%;
						}
						
						.log_configuration_table th
						{
							width: 50px;
						}

						.log_configuration_table td, .log_configuration_table th
						{
							text-align: center;
							vertical-align: middle;
							padding: 5px 8px;
						}
						
						.log_configuration_table tr
						{
							background-color: #D6E6F3;
						}
						
						.log_configuration_table tr.alt_row
						{
							background-color: #E2F2FF;
						}
						
						.log_configuration_table .level
						{
							background-color: #99BBDD;
						}
						
						.log_configuration_table td.output_type_header,
						.log_configuration_table td.output_header,
						.log_configuration_table td.total_checkbox
						{
							background-color: #AACAEE;
						}
						
						.log_configuration_table td.output_type_header
						{
							text-align: left;
						}
						
						.log_configuration_table td.output_header
						{
							width: 70px;
						}
					</style>';
		$t_form .= '<div class="grid">';
		
		foreach($this->level_array as $t_level)
		{
			$t_first_row = true;
			$t_form .= '<div class="span12">';
			$t_form .= '<table border="0" cellspacing="0" cellpadding="2" class="log_configuration_table dataTableContent_gm">';
			$t_alt_bg_counter = 0;
			foreach($this->output_type_array as $t_output_type)
			{
				if($t_alt_bg_counter++ % 2 == 0)
				{
					$t_form .= '<tr>';
				}
				else
				{
					$t_form .= '<tr class="alt_row">';
				}
				
				if($t_first_row)
				{
					
					$t_form .= '<th class="level">' . $t_level_text_array[$t_level] . '</th>';
					$t_form .= '<td class="level"></td>';
					
					foreach($this->output_array as $t_output)
					{
						$t_form .= '<td class="output_header">' . $t_output_text_array[$t_output] . '</td>';
					}
					
					$t_form .= '</tr><tr>';
					
					$t_form .= '<td class="level"></td>';
					$t_checkbox = '<input type="checkbox" class="total_level log_' . $t_level . '" onchange="set_all_checked(\'' . $t_level . '\', this.checked)" />';
					$t_form .= '<td class="level">' . $t_checkbox . '</td>';
					
					foreach($this->output_array as $t_output)
					{
						$t_checkbox = '<input type="checkbox" class="log_' . $t_level . ' total_output log_' . $t_output . '" onchange="set_all_outputs_checked(\'' . $t_level . '\', \'' . $t_output . '\', this.checked);" />';
						$t_form .= '<td class="total_checkbox">' . $t_checkbox . '</td>';
					}
					
					$t_form .= '</tr><tr>';
					$t_first_row = false;
				}
				
				$t_form .= '<td class="output_type_header">' . $t_output_type_text_array[$t_output_type] . '</td>';
				$t_checkbox = '<input type="checkbox" class="log_' . $t_level . ' total_output_type log_' . $t_output_type . '" onchange="set_all_output_types_checked(\'' . $t_level . '\', \'' . $t_output_type . '\', this.checked);" />';
				$t_form .= '<td class="total_checkbox">' . $t_checkbox . '</td>';
				
				foreach($this->output_array as $t_output)
				{
					$t_value = isset($this->configuration_array[$t_level])
							&& isset($this->configuration_array[$t_level][$t_output_type])
							&& isset($this->configuration_array[$t_level][$t_output_type][$t_output])
							&& $this->configuration_array[$t_level][$t_output_type][$t_output];
					$t_checked = '';
					if($t_value)
					{
						$t_checked = ' checked="checked"';
					}
					$t_checkbox = '<input type="checkbox" class="log_' . $t_level . ' log_' . $t_output_type . ' log_' . $t_output . '" name="log_configuration[' . $t_level . '][' . $t_output_type . '][' . $t_output . ']"' . $t_checked . ' onchange="init_log_configuration_checkboxes();" />';
					$t_form .= '<td>' . $t_checkbox . '</td>';
				}
				
				$t_form .= '</tr>';
			}
			
			$t_form .= '</table></div>';
		}
		
		$t_form .= '</div><script type="text/javascript">
						var t_levels = new Array(';
		foreach($this->level_array as $t_level)
		{
			$t_form .=	'"' . $t_level . '",';
		}
		$t_form = substr($t_form, 0, -1);
		$t_form .=		');
						var t_output_types = new Array(';
		foreach($this->output_type_array as $t_output_type)
		{
			$t_form .=	'"' . $t_output_type . '",';
		}
		$t_form = substr($t_form, 0, -1);
		$t_form .=		');
						var t_outputs = new Array(';
		foreach($this->output_array as $t_output)
		{
			$t_form .= '"' . $t_output . '",';
		}
		$t_form = substr($t_form, 0, -1);
		$t_form .=		');

						init_log_configuration_checkboxes();
						
						function init_log_configuration_checkboxes()
						{
							for(var i = 0; i < t_levels.length; i++)
							{
								for(var j = 0; j < t_output_types.length; j++)
								{
									check_all_output_types_checked(t_levels[i], t_output_types[j]);
								}
								
								for(var j = 0; j < t_outputs.length; j++)
								{
									check_all_outputs_checked(t_levels[i], t_outputs[j]);
								}
								
								check_all_checked(t_levels[i]);
							}
						}
						
						function check_all_checked(p_level)
						{
							var t_checked = true;
							
							for(var i = 0; i < t_output_types.length; i++)
							{
								t_checked = t_checked && document.getElementsByClassName("log_" + p_level + " total_output_type log_" + t_output_types[i])[0].checked;
							}
							
							for(var i = 0; i < t_outputs.length; i++)
							{
								t_checked = t_checked && document.getElementsByClassName("log_" + p_level + " total_output log_" + t_outputs[i])[0].checked;
							}
							
							set_total_checkbox("total_level", p_level, "", t_checked);
							
							return t_checked;
						}
						
						function set_all_checked(p_level, p_value)
						{
							var t_checked = p_value;
							
							for(var i = 0; i < t_output_types.length; i++)
							{
								for(var j = 0; j < t_outputs.length; j++)
								{
									set_all_outputs_checked(p_level, t_outputs[j], p_value)
								}
								
								set_all_output_types_checked(p_level, t_output_types[i], p_value);
							}
						}
						
						function check_all_output_types_checked(p_level, p_output_type)
						{
							var t_checked = true;
							
							for(var i = 0; i < t_outputs.length; i++)
							{
								t_checked = t_checked && document.getElementsByClassName("log_" + p_level + " log_" + p_output_type + " log_" + t_outputs[i])[0].checked;
							}
							
							set_total_checkbox(p_level, "total_output_type", p_output_type, t_checked);
							
							return t_checked;
						}
						
						function set_all_output_types_checked(p_level, p_output_type, p_value)
						{
							for(var i = 0; i < t_outputs.length; i++)
							{
								set_single_checkbox(p_level, p_output_type, t_outputs[i], p_value);
							}
							
							init_log_configuration_checkboxes();
						}
						
						function check_all_outputs_checked(p_level, p_output)
						{
							var t_checked = true;
							
							for(var i = 0; i < t_output_types.length; i++)
							{
								t_checked = t_checked && document.getElementsByClassName("log_" + p_level + " log_" + t_output_types[i] + " log_" + p_output)[0].checked;
							}
							
							set_total_checkbox(p_level, "total_output", p_output, t_checked);
							
							return t_checked;
						}
						
						function set_all_outputs_checked(p_level, p_output, p_value)
						{
							for(var i = 0; i < t_output_types.length; i++)
							{
								set_single_checkbox(p_level, t_output_types[i], p_output, p_value);
							}
							
							init_log_configuration_checkboxes();
						}
						
						function set_checkbox(p_classes, p_value)
						{
							if(p_value)
							{
								document.getElementsByClassName(p_classes)[0].checked = true;
							}
							else
							{
								document.getElementsByClassName(p_classes)[0].checked = false;
							}
						}
						
						function set_single_checkbox(p_level, p_output_type, p_output, p_value)
						{
							set_checkbox("log_" + p_level + " log_" + p_output_type + " log_" + p_output, p_value);
						}

						function set_total_checkbox(p_first_class, p_second_class, p_third_class, p_value)
						{
							var t_classes = "";
							
							if(p_first_class.indexOf("total") == 0 || p_first_class == "")
							{
								t_classes += p_first_class;
							}
							else
							{
								t_classes += "log_" + p_first_class;
							}
							
							if(p_second_class.indexOf("total") == 0 || p_second_class == "")
							{
								t_classes += " " + p_second_class;
							}
							else
							{
								t_classes += " log_" + p_second_class;
							}
							
							if(p_third_class.indexOf("total") == 0 || p_third_class == "")
							{
								t_classes += " " + p_third_class;
							}
							else
							{
								t_classes += " log_" + p_third_class;
							}
							
							set_checkbox(t_classes, p_value);
						}
					</script>';
		
		return $t_form;
	}
	
	/* ----- Getter / Setter ----- */
	
	public function get_group_name()
	{
		return $this->group_name;
	}

	public function get_configuration_array()
	{
		return $this->configuration_array;
	}

	public function get_level_array()
	{
		return $this->level_array;
	}

	public function get_output_type_array()
	{
		return $this->output_type_array;
	}

	public function get_output_array()
	{
		return $this->output_array;
	}
	
	public function get_session_keys()
	{
		return $this->session_keys;
	}

	public function get_default_session_keys()
	{
		return $this->default_session_keys;
	}
	
	public function get_excluded_files_array()
	{
		return $this->excluded_files_array;
	}

	public function get_excluded_classes_array()
	{
		return $this->excluded_classes_array;
	}

	public function set_group_name($p_group_name)
	{
		$this->group_name = $p_group_name;
	}

	public function set_configuration_array($p_configuration_array)
	{
		$this->configuration_array = $p_configuration_array;
	}

	public function set_level_array($p_level_array)
	{
		$this->level_array = $p_level_array;
	}

	public function set_output_type_array($p_output_type_array)
	{
		$this->output_type_array = $p_output_type_array;
	}

	public function set_output_array($p_output_array)
	{
		$this->output_array = $p_output_array;
	}
	
	public function set_session_keys($p_session_keys)
	{
		$this->session_keys = $p_session_keys;
	}

	public function set_default_session_keys($p_default_session_keys)
	{
		$this->default_session_keys = $p_default_session_keys;
	}
	
	public function set_excluded_files_array($p_excluded_files_array)
	{
		$this->excluded_files_array = $p_excluded_files_array;
	}

	public function set_excluded_classes_array($p_excluded_classes_array)
	{
		$this->excluded_classes_array = $p_excluded_classes_array;
	}
}