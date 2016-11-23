<?php
/* --------------------------------------------------------------
   GMModuleManager.php 2015-09-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
?><?php

class GMModuleManager_ORIGIN
{
	var $v_module_type = '';
	var $v_modules_directory = '';
	var $v_modules_lang_directory = '';
	var $v_module_link = '';
	var $v_show_installed_modules_menu = false;
	var $v_display_installed_modules = false;
	var $v_show_missing_modules_menu = true;
	var $v_display_missing_modules = true;
	var $v_coo_lang_file_master = null;
	
	function __construct($p_module_type, $p_show_installed_modules_menu = false, $p_display_installed_modules = false, $p_show_missing_modules_menu = true, $p_display_missing_modules = true, $p_ignore_files_array = array())
	{
		$this->v_module_type = basename($p_module_type);
		if($p_module_type == 'ordertotal')
		{
			$this->v_module_type = 'order_total';
		}
		$this->v_modules_directory = DIR_FS_CATALOG_MODULES . $this->v_module_type . '/';
		$this->v_modules_lang_directory = DIR_FS_LANGUAGES . $_SESSION['language'] . '/modules/' . $this->v_module_type . '/';
		$this->v_module_link = FILENAME_MODULES . '?set=' . $this->v_module_type . '&module=';
		$this->v_show_installed_modules_menu = $p_show_installed_modules_menu;
		$this->v_display_installed_modules = $p_display_installed_modules;
		$this->v_show_missing_modules_menu = $p_show_missing_modules_menu;
		$this->v_display_missing_modules = $p_display_missing_modules;
		$this->v_ignore_files_array = $p_ignore_files_array;
		$this->v_coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
	}
	
	
	function get_modules_installed()
	{
		$t_modules_installed = array();
		$t_get_modules_installed = xtc_db_query("SELECT configuration_value 
													FROM " . TABLE_CONFIGURATION . "
													WHERE configuration_key = 'MODULE_" . strtoupper($this->v_module_type) . "_INSTALLED'
													LIMIT 1");
		if(xtc_db_num_rows($t_get_modules_installed) == 1)
		{
			$t_modules_installed_result = xtc_db_fetch_array($t_get_modules_installed);
			$t_modules_installed_result['configuration_value'] = str_replace('.php', '', $t_modules_installed_result['configuration_value']);
			$t_modules_installed = explode(';', $t_modules_installed_result['configuration_value']);
		}

		return $t_modules_installed;
	}
	
	
	function get_missing_modules($p_structure_array)
	{
		$t_missing_modules = array();
		
		if($t_dir = opendir($this->v_modules_directory))
		{
			while($t_file = readdir($t_dir))
			{
				if(substr($t_file, -4) == '.php')
				{
					if(strpos(serialize($p_structure_array), '"' . substr($t_file, 0, -4) . '"') === false && !in_array($t_file, $this->v_ignore_files_array))
					{
						$t_missing_modules[] = substr($t_file, 0, -4);
					}
				}
			}
			
			sort($t_missing_modules);
			closedir($t_dir);
		}
		
		return $t_missing_modules;
	}
	
	
	function repair()
	{
		$t_modules_installed_array = $this->get_modules_installed();
		
		if($t_dir = opendir($this->v_modules_directory))
		{
			while($t_file = readdir($t_dir))
			{
				if(substr($t_file, -4) == '.php')
				{
					$this->v_coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/' . $this->v_module_type . '/' . $t_file);
	   				include_once($this->v_modules_directory . $t_file);
					
	   				$t_module_name = substr($t_file, 0, -4);

					if(xtc_class_exists($t_module_name))
					{
						$coo_module = new $t_module_name();
						
						if($coo_module->check() && !in_array($coo_module->code, $t_modules_installed_array))
						{
							$t_modules_installed_array[] = $coo_module->code;
							
							foreach($t_modules_installed_array AS $t_key => $t_value)
							{
								if(substr($t_value, -4) != '.php')
								{
									$t_modules_installed_array[$t_key] .= '.php';
								}
								if($t_modules_installed_array[$t_key] == '.php')
								{
									unset($t_modules_installed_array[$t_key]);
								}
							}
							
							$t_modules_installed_array = array_unique($this->sort_modules($t_modules_installed_array));
							
							xtc_db_query("UPDATE " . TABLE_CONFIGURATION . "
										 SET configuration_value = '" . xtc_db_input(implode(';', $t_modules_installed_array)) . "'
										 WHERE configuration_key = 'MODULE_" . strtoupper($this->v_module_type) . "_INSTALLED'");
						}
						elseif(!$coo_module->check() && in_array($coo_module->code, $t_modules_installed_array))
						{
							foreach($t_modules_installed_array AS $t_key => $t_value)
							{
								if($t_value == $coo_module->code)
								{
									unset($t_modules_installed_array[$t_key]);
								}
								if(substr($t_value, -4) != '.php')
								{
									$t_modules_installed_array[$t_key] .= '.php';
								}
								if($t_modules_installed_array[$t_key] == '.php')
								{
									unset($t_modules_installed_array[$t_key]);
								}
							}
							
							$t_modules_installed_array = array_unique($this->sort_modules($t_modules_installed_array));
							
							xtc_db_query("UPDATE " . TABLE_CONFIGURATION . "
										 SET configuration_value = '" . xtc_db_input(implode(';', $t_modules_installed_array)) . "'
										 WHERE configuration_key = 'MODULE_" . strtoupper($this->v_module_type) . "_INSTALLED'");
						}
					}
				}

			}
			
			closedir($t_dir);
		}
	}
	
	
	function sort_modules($p_modules_array)
	{
		$t_sorted_modules_array = array();
		$t_modules_files_array = array();
		$t_modules_sort_number_array = array();
		
		foreach($p_modules_array AS $t_key => $t_file)
		{
			if(substr($t_file, -4) != '.php')
			{
				$t_file .= '.php';
			}
			
			if(is_file($this->v_modules_directory . $t_file))
			{
				$this->v_coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/' . $this->v_module_type . '/' . $t_file);
	   			include_once($this->v_modules_directory . $t_file);

				$t_module_name = substr($t_file, 0, -4);
	   			if(xtc_class_exists($t_module_name))
				{
					$coo_module = new $t_module_name();
					
					if($coo_module->check())
					{
						if(isset($_POST['configuration']['MODULE_' . strtoupper($this->v_module_type) . '_' . strtoupper($t_module_name) . '_SORT_ORDER']))
						{
							$t_modules_sort_number_array[] = $_POST['configuration']['MODULE_' . strtoupper($this->v_module_type) . '_' . strtoupper($t_module_name) . '_SORT_ORDER'];
						}
						else
						{
							$t_modules_sort_number_array[] = $coo_module->sort_order;
						}
						$t_modules_files_array[] = $t_file;
					}
				}
			}
		}
		
		asort($t_modules_sort_number_array);
		reset($t_modules_sort_number_array);
		
		foreach($t_modules_sort_number_array AS $t_key => $t_value)
		{
			$t_sorted_modules_array[] = $t_modules_files_array[$t_key];
		}
		
		return $t_sorted_modules_array;
	}
	
	
	function save_sort_order($p_modules_array)
	{
		$t_modules_array = array_unique($this->sort_modules($p_modules_array));
		xtc_db_query("UPDATE " . TABLE_CONFIGURATION . "
							 SET configuration_value = '" . xtc_db_input(implode(';', $t_modules_array)) . "'
							 WHERE configuration_key = 'MODULE_" . strtoupper($this->v_module_type) . "_INSTALLED'");
	}
	
	
	function get_module_data_by_name($p_module_name)
	{
		$t_module_data = array();
		
		if(file_exists($this->v_modules_directory . basename($p_module_name) . '.php'))
		{
			$this->v_coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/' . $this->v_module_type . '/' . basename($p_module_name) . '.php');
	   		include_once($this->v_modules_directory . basename($p_module_name) . '.php');

	   		if(xtc_class_exists($p_module_name))
			{
				$coo_module = new $p_module_name();

				$t_module_keys_array = array();
				$t_keys_array = $coo_module->keys();
				
				for($i = 0; $i < count($t_keys_array); $i++)
				{
					$t_get_key_data = xtc_db_query("SELECT
														configuration_key,
														configuration_value, 
														use_function, 
														set_function
													FROM " . TABLE_CONFIGURATION . "
													WHERE
														configuration_key = '" . $t_keys_array[$i] . "'
													LIMIT 1");
					if(xtc_db_num_rows($t_get_key_data) == 1)
					{
						$t_key_data = xtc_db_fetch_array($t_get_key_data);
						$t_module_keys_array[$t_keys_array[$i]]['title'] =
							(defined(strtoupper($t_keys_array[$i] . '_TITLE'))) ? constant(strtoupper($t_keys_array[$i]
							                                                                          . '_TITLE')) : '';
						$t_module_keys_array[$t_keys_array[$i]]['value'] = $t_key_data['configuration_value'];
						$t_module_keys_array[$t_keys_array[$i]]['description'] =
							(defined(strtoupper($t_keys_array[$i] . '_DESC'))) ? constant(strtoupper($t_keys_array[$i]
							                                                                         . '_DESC')) : '';
						$t_module_keys_array[$t_keys_array[$i]]['use_function'] = $t_key_data['use_function'];
						$t_module_keys_array[$t_keys_array[$i]]['set_function'] = $t_key_data['set_function'];
					}
				}
				
				$t_module_data = array('code' => $coo_module->code,
										'title' => $coo_module->title,
										'description' => $coo_module->description,
										'show_install' => $coo_module->show_install,
										'sort_order' => ($coo_module->check() && is_numeric($coo_module->sort_order)) ? $coo_module->sort_order : '',
										'arrow' => ($_GET['module'] != $coo_module->code) ? '<a href="' . $this->v_module_link . $coo_module->code . '" id="gm_module_arrow_inactive_' . $coo_module->code . '"><img src="' . DIR_WS_ADMIN . 'html/assets/images/legacy/icon_info.gif" title="' . htmlspecialchars_wrapper($coo_module->title) . '" /></a>' : '<img src="' . DIR_WS_ADMIN . 'html/assets/images/legacy/icon_arrow_right.gif" title="' . htmlspecialchars_wrapper($coo_module->title) . '" id="gm_module_arrow_active_' . $coo_module->code . '" />',
								        'status' => $coo_module->check(),
										'keys' => $t_module_keys_array
										);
			}
		}

		return $t_module_data;
	}
	
	
	function show_modules($p_structure_array)
	{
		$t_structure_array = $p_structure_array;
		
		if($this->v_show_missing_modules_menu)
		{
			$t_missing_modules_array = array(
												array(
														'TITLE' => GM_MODULES_MISSING_TITLE,
														'MODULES' => $this->get_missing_modules($t_structure_array),
														'DISPLAY' => $this->v_display_missing_modules
												)
											);
			if(!empty($t_missing_modules_array[0]['MODULES'][0]))
			{
				$t_structure_array = array_merge($t_structure_array, $t_missing_modules_array);
			}
		}
		
		if(isset($_GET['module']))
		{
			foreach($t_structure_array AS $t_key => $t_value)
			{
				$t_structure_array[$t_key] = $this->expand_menu($t_value, $_GET['module']);
			}
		}

		$this->draw_output($t_structure_array);
	}
	

	function expand_menu($p_structure_array, $p_module)
	{
		if(isset($p_structure_array['MODULES']) && is_array($p_structure_array['MODULES']))
		{
			foreach($p_structure_array['MODULES'] AS $t_index => $t_module)
			{
				if($t_module == $p_module)
				{
					$p_structure_array['DISPLAY'] = 1;

					return $p_structure_array;
				}
				elseif(is_array($t_module))
				{
					$t_result = $this->expand_menu($t_module, $p_module);
					if(is_array($t_result))
					{
						$p_structure_array['MODULES'][$t_index] = $t_result;
						$p_structure_array['DISPLAY'] = 1;
					}
				}
			}
		}

		return $p_structure_array;
	}


	function draw_output($structure)
	{
		foreach($structure as $module)
		{
			if(!is_array($module))
			{
				$this->draw_module($module);
			}
			else
			{
				if(isset($module['GHOST']) && $module['GHOST'] == true && !isset($_GET['showghosts'])) {
					continue;
				}
				
				if(isset($module['MODULES']))
				{
					if($this->is_empty($module['MODULES']))
					{
						continue;
					}
					
					if(isset($module['TITLE']) && (!isset($module['DISPLAY']) || $module['DISPLAY']))
					{
						$this->draw_head_row($module['MODULES'], $module['TITLE']);
					}
					

					$hidden = !$this->is_any_module_installed_or_selected($module['MODULES']);
					
					foreach($module['MODULES'] as $singleModule)
					{
						$this->draw_module($singleModule, true, $hidden, ' id_' . md5(serialize($module['MODULES'])));
					}
				}
			}
		}
	}
	
	function draw_module($p_module, $nested = false, $hidden = false, $p_class = '')
	{
		$moduleData = $this->get_module_data_by_name($p_module);

		if(!empty($moduleData))
		{
			$classes = '';
			if($_GET['module'] == $moduleData['code'])
			{
				$classes = 'active';
			}

			if($nested)
			{
				$classes .= ' nested';
			}
			
			if(isset($moduleData['sort_order']) && $moduleData['sort_order'] !== '')
			{
				$classes .= ' installed';
			}
			
			if($hidden)
			{
				$classes .= ' hidden';
			}

			if($p_class !== '')
			{
				$classes .= ' ' . $p_class;
			}
			
			preg_match('/(<img[^>]+?>)/', $moduleData['title'], $matches);
			$logo = isset($matches[1]) ? $matches[1] : '';
			$logo = preg_replace('/(style="[^"]*")/', '', $logo);
			$logo = preg_replace('/(class="[^"]*")/', '', $logo);
			$name = trim(preg_replace('/(<[^>]+?>)/', '', $moduleData['title']));
			
			echo $this->draw_row($name, $logo, $moduleData['code'], $moduleData['sort_order'], $classes);
		}
	}
	
	function draw_row($p_name, $p_logo, $p_moduleName, $p_sort, $p_class = '', $p_id = '')
	{
		$id = '';
		$icon = '';
		if($p_id !== '')
		{
			$id = ' id="' . $p_id . '"';
			$type = strpos($p_class, 'closed') !== false ? 'plus' : 'minus';
			$icon = '<span class="collapse-icon">
						<i class="fa fa-' . $type . '-square-o"></i>
					</span>';
		}
		
		$installedBadge = '';
		if($p_sort !== '')
		{
			$installedBadge = '<span class="badge badge-success">' . $this->v_coo_lang_file_master->get_text('installed', 'buttons') . '</span> ';
		}
		
		if(strpos($p_class, 'nested') !== false)
		{
			$p_name = '<i class="fa fa-angle-right"></i> ' . $p_name;
		}
		
		$linkComponent = '';
		if($p_moduleName !== '')
		{
			$linkComponent = ' data-gx-extension="link" data-link-url="' .
			                 xtc_href_link(FILENAME_MODULES, xtc_get_all_get_params(array('module', 'action')) .
			                 'module=' . $p_moduleName) . '"';
		}
		
		echo '<tr class="dataTableRow ' . $p_class . '"' . $id . $linkComponent . '>
					<td class="dataTableContent">' . $icon . '</td>
					<td class="dataTableContent">' . $p_name . '</td>
					<td class="dataTableContent">' . $p_logo . '</td>
					<td class="dataTableContent">' . $p_moduleName . '</td>
					<td class="dataTableContent">' . $installedBadge . '</td>
					<td class="dataTableContent numeric_cell">' . $p_sort . '</td>
					<td class="dataTableContent"></td>
				</tr>';
	}
	
	function draw_head_row(array $modules, $p_title, $p_logo = '')
	{
		$class = 'module-head';
		if(!$this->is_any_module_installed_or_selected($modules))
		{
			$class = 'module-head closed';
		}
		
		$this->draw_row($p_title, $p_logo, '', '', $class, 'id_' . md5(serialize($modules)));
	}
	
	function is_any_module_installed_or_selected(array $modules)
	{
		foreach($modules as $module)
		{
			$moduleData = $this->get_module_data_by_name($module);

			if((isset($moduleData['sort_order']) && $moduleData['sort_order'] !== '') || $_GET['module'] === $module)
			{
				return true;
			}
		}
		
		return false;
	}
	
	function is_empty(array $modules)
	{
		foreach($modules as $module)
		{
			$moduleData = $this->get_module_data_by_name($module);
			
			if(!empty($moduleData))
			{
				return false;
			}
		}
		
		return true;
	}
}

MainFactory::load_origin_class('GMModuleManager');
