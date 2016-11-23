<?php
/* --------------------------------------------------------------
   AdminMenuSource.inc.php 2016-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   Copyright (c) 2011 Avenger, entwicklung@powertemplate.de
   --------------------------------------------------------------
*/

class AdminMenuSource
{
	var $v_menu_structure_array = array();
	var $v_system_xml_path;
	var $v_user_xml_path;

	function AdminMenuSource()
	{
		// system xml directory
		$this->v_system_xml_path = DIR_FS_CATALOG.'system/conf/admin_menu/';
		// user xml directory
		$this->v_user_xml_path = DIR_FS_CATALOG.'GXUserComponents/conf/admin_menu/';
	}

	function init_structure_array( )
	{
		// load first gambio menu xml
		$t_menu_files[] = $this->v_system_xml_path."gambio_menu.xml";
		
		// load xml plugin files from system
		$t_plugin_menu_files_system = glob($this->v_system_xml_path.'menu_*.xml');
		if (is_array($t_plugin_menu_files_system))
		{
			$t_menu_files = array_merge($t_menu_files, $t_plugin_menu_files_system);
		}
		
		// load xml plugin files from user
		$t_plugin_menu_files_user = glob($this->v_user_xml_path.'menu_*.xml');
		if (is_array($t_plugin_menu_files_user))
		{
			$t_menu_files = array_merge($t_menu_files, $t_plugin_menu_files_user);
		}
		
		foreach ($t_menu_files as $t_menu_file)
		{
			// get xml content from ContentView
			$coo_view = MainFactory::create_object('ContentView');
			$t_menu_file_parts = explode('/', $t_menu_file);
			$t_menu_file_name = $t_menu_file_parts[count($t_menu_file_parts)-1];
			$t_menu_file_path = str_replace($t_menu_file_parts[count($t_menu_file_parts)-1], '', $t_menu_file);
			$coo_view->set_template_dir($t_menu_file_path);
			$coo_view->set_content_template($t_menu_file_name);

			// OLD Module-Center
			if($t_menu_file_name == 'gambio_menu.xml')
			{
				$displayOldModuleCenter = false;
				$oldModuleFiles = glob(DIR_FS_CATALOG . 'admin/includes/modules/export/*.php');
				if(is_array($oldModuleFiles) && count($oldModuleFiles))
				{
					$displayOldModuleCenter = true;
				}
				$coo_view->set_content_data('display_old_module_center', $displayOldModuleCenter);
			}
			
			$t_xml_content = $coo_view->get_html();
			$t_menu_groups= $this->unserialize_xml($t_xml_content);
			if (is_array($t_menu_groups))
			{
				$t_menu_groups = $t_menu_groups['menugroup'];
				if (!$t_menu_groups[0])
				{
					$t_menu_groups = array(0 => $t_menu_groups);
				}
				foreach ($t_menu_groups as $t_menu_group)
				{
					// get attributes from xml
					$t_attributes = $t_menu_group['@attributes'];
					$t_group_id = $t_attributes['id'];
					
					if(trim($t_group_id) == '')
					{
						$t_group_id = 'UNKNOWN';
					}
					
					// add group if not exists
					if (!$this->v_menu_structure_array[$t_group_id])
					{
						if(trim($t_attributes['id']) != '' && trim($t_attributes["title"]) == '')
						{
							continue;
						}
						// add default group values
						$this->v_menu_structure_array[$t_group_id] = array('id' => $t_group_id,
																		   'title' => '',
																		   'sort' => 1000,
																		   'background' => 'module.png',
																		   'class' => 'fa fa-folder-open',
																		   'menuitems' => array());
						
						if(trim($t_attributes['id']) != '')
						{
							// set sort order
							if(trim($t_attributes["title"]) != '')
							{
								$this->v_menu_structure_array[$t_group_id]['title'] = trim($t_attributes["title"]);
							}
							
							// set sort order
							if(trim($t_attributes["sort"]) != '')
							{
								$this->v_menu_structure_array[$t_group_id]['sort'] = trim($t_attributes["sort"]);
							}

							// set category image
							if(trim($t_attributes["background"]) != '')
							{
								$this->v_menu_structure_array[$t_group_id]['background'] = trim($t_attributes["background"]);
							}

							// set category fontawesome class
							if(trim($t_attributes["class"]) != '')
							{
								$this->v_menu_structure_array[$t_group_id]['class'] = trim($t_attributes["class"]);
							}
						}	
					}
					
					// get menu items from xml
					$t_menuitems = $t_menu_group['menuitem'];
					if (is_array($t_menuitems))
					{
						if (!$t_menuitems[0])
						{
							$t_menuitems = array(0 => $t_menuitems);
						}
						foreach ($t_menuitems as $t_menuitem)
						{
							if ($t_menuitem)
							{
								// get attributes from xml
								$t_attributes = $t_menuitem['@attributes'];
								
								if(empty($t_attributes['title']) || trim($t_attributes['title']) == '')
								{
									continue;
								}
								
								// get menu item link
								$t_link = $t_attributes["link"];
								if (defined($t_link))
								{
									$t_link = constant($t_link);
								}
								// get sort order
								$t_sort = $t_attributes["sort"];
								if ($t_sort == "")
								{
									$t_sort = 1000;
								}
								// add menu item
								$t_menu_item = array(
									"title" => htmlspecialchars_decode($t_attributes['title']),
									"link" => $t_link,
									"link_param" => $t_attributes["link_param"],
									"sort" => isset($t_sort) ? $t_sort : '1000'
									);
								//Check if element with "sort" already exists...
								$t_menu_item_exists = false;
								$t_current_menu_key = false;
								foreach($this->v_menu_structure_array[$t_group_id]["menuitems"] as $key => $menu_item)
								{
									if($menu_item['link'] == $t_link && $menu_item['link_param'] == $t_attributes["link_param"])
									{
										$t_menu_item_exists = true;
										$t_current_menu_key = $key;
										break;
									}
								}
								if(isset($t_attributes["delete"]) && $t_attributes["delete"] == 'true')
								{
									if($t_menu_item_exists)
									{
										unset($this->v_menu_structure_array[$t_group_id]["menuitems"][$t_current_menu_key]);
									}
								}
								else
								{
									if($t_menu_item_exists)
									{
										$this->v_menu_structure_array[$t_group_id]["menuitems"][$t_current_menu_key] = $t_menu_item;
									}
									else
									{
										$this->v_menu_structure_array[$t_group_id]["menuitems"][] = $t_menu_item;
									}
								}
							}
						}
					}
					// sort menu items according to sort order
					usort($this->v_menu_structure_array[$t_group_id]["menuitems"], array($this, "compare_sort_order"));
				}
			}
		}
		// sort group items according to sort order
		usort($this->v_menu_structure_array, array($this, "compare_sort_order"));
	}

	function get_groups_array( )
	{
		// get groups without menu items
		$t_group_array = array();
		foreach($this->v_menu_structure_array as $key => $t_value){
			$t_group = array("id" => $t_value["id"], "title" => $t_value["title"], "background" => $t_value["background"], "class" => $t_value["class"]);
			$t_group_array[] = $t_group;
		}
		return $t_group_array;
	}

	function get_group_items_array( $p_group_id )
	{
		// get menu items of $p_group_id
		$t_item_array = array();
		foreach($this->v_menu_structure_array as $key => $t_group){
			if($t_group["id"] == $p_group_id){
				return $t_group["menuitems"];
			}
		}
		return false;
	}

	// sort groups according to sort_order
	function compare_sort_order($a, $b){
		return strnatcmp($a['sort'], $b['sort']);
	}

	function unserialize_xml($input, $callback = null, $recurse = false)
	{
		// Get input, loading an xml string with simplexml if its the top level of recursion
		$data = ((!$recurse) && is_string($input))? $this->xml_from_string($input): $input;
		// Convert SimpleXMLElements to array
		if ($data instanceof SimpleXMLElement)
		{
			$data = (array) $data;
		}
		// Recurse into arrays
		if (is_array($data))
		{
			foreach ($data as &$item)
			{
				$item = $this->unserialize_xml($item, $callback, true);
			}
		}
		// Run callback and return
		return (!is_array($data) && is_callable($callback))? call_user_func($callback, $data): $data;
	}

	function xml_from_string($xmlstr)
	{
		$t_xml_result=@simplexml_load_string ($xmlstr);
		if ($t_xml_result)
		{
			$errors = libxml_get_errors();
			if (is_array($errors) && count($errors)>0)
			{
				$xml = explode("\n", $xmlstr);
				foreach ($errors as $error)
				{
					echo $this->display_xml_error($error, $xml);
				}
				libxml_clear_errors();
				return false;
			}
			else
			{
				return $t_xml_result;
			}
		}
	}

	function display_xml_error($error, $xml)
	{
		$return  = $xml[$error->line - 1] . "\n";
		$return .= str_repeat('-', $error->column) . "^\n";

		switch ($error->level)
		{
			case LIBXML_ERR_WARNING:
				$return .= "Warning $error->code: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "Error $error->code: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "Fatal Error $error->code: ";
				break;
		}
		$return .= trim($error->message) .
			"\n  Line: $error->line" .
			"\n  Column: $error->column";
		if ($error->file)
		{
			$return .= "\n  File: $error->file";
		}
		return "$return\n\n--------------------------------------------\n\n";
	}

	// adding menu entries (old school) - hint: prefer xml-file in GXUserComponents/conf/AdminMenu directory
	function add_compatibility_entries($admin_access)
	{
		# SAMPLE ITEM:
		//if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['banner_manager'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_BANNER_MANAGER" href="' . xtc_href_link(FILENAME_BANNER_MANAGER) . '">' . BOX_BANNER_MANAGER . '</a></li>';
		//if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['content_manager'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_CONTENT" href="' . xtc_href_link(FILENAME_CONTENT_MANAGER) . '">' . BOX_CONTENT . '</a></li>';
		//if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['backup'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_BACKUP" href="' . xtc_href_link(FILENAME_BACKUP) . '">' . BOX_BACKUP . '</a></li>';
		//if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['blacklist'] == '1')) echo '<li class="leftmenu_body_item"><a class="fav_drag_item" id="BOX_TOOLS_BLACKLIST" href="' . xtc_href_link(FILENAME_BLACKLIST, '', 'NONSSL') . '">' . BOX_TOOLS_BLACKLIST . '</a></li>';
	}
}