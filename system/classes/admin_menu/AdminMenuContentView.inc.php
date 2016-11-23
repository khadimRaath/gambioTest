<?php
/* --------------------------------------------------------------
  AdminMenuContentView.inc.php 2015-10-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class AdminMenuContentView extends ContentView
{
	protected $customerId = 0;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/');
		$this->set_content_template('admin_menu.html');

		$this->init_smarty();
		$this->set_flat_assigns(false);
	}

	public function prepare_data()
	{
		$this->build_html = false;
		$data_output_array = array();
		if($this->customerId > 0)
		{
			$coo_menu_control = MainFactory::create_object('AdminMenuControl', array(false));
			$t_set_data_array = $coo_menu_control->get_menu_array($this->customerId);

			if(sizeof($t_set_data_array) > 0)
			{
				foreach($t_set_data_array AS $key1 => $t_data_group)
				{
					if(sizeof($t_data_group['menuitems']) > 0 || $t_data_group['id'] == "BOX_HEADING_FAVORITES")
					{
						$data_output_array[$key1] = $t_data_group;
						$data_output_array[$key1]['active_class'] = '';
						
						foreach($t_data_group['menuitems'] AS $key2 => $t_data_item)
						{
							$t_data_item['class'] = '';
							
							$requestUri = gm_get_env_info('REQUEST_URI');

							$linkParts = parse_url($t_data_item['link']);
							parse_str($linkParts['query'], $linkGetParams);
							
							$urlParts = parse_url($requestUri);
							parse_str($urlParts['query'], $urlGetParams);
							
							// workaround for interfaces pages
							if(isset($urlGetParams['gID']))
							{
								switch($urlGetParams['gID'])
								{
									case '21':
									case '24':
									case '25':
									case '26':
									case '32':
										$urlGetParams['gID'] = '19';
										break;
								}
							}
							
							// workaround for orders edit pages
							$urlParts['path'] = str_replace('/orders_edit.php', '/orders.php', $urlParts['path']);
							
							// The admin menu connection must take into concern the configuration pages. 
							$connectedPagePath = $linkParts['path']; 
							if(count($linkGetParams) > 0)
							{
								$connectedPagePath .= '?' . http_build_query($linkGetParams);
							}
							
							if(($linkParts['path'] === $urlParts['path'] 
							    && count($linkGetParams) == count(array_intersect($linkGetParams, $urlGetParams)))
								|| (AdminMenuControl::get_connected_page() == $connectedPagePath))
							{
								$t_data_item['class'] = 'current';
								$data_output_array[$key1]['active_class'] = 'current';
								if($data_output_array[$key1]['id'] !== 'BOX_HEADING_FAVORITES') 
								{
									AdminMenuControl::reset_connected_page();	
								}
							}

							$data_output_array[$key1]['menuitems'][$key2] = $t_data_item;
						}
					}
				}
				$this->set_content_data('DATA', $data_output_array);
				$this->build_html = true;
			}
		}
		
		return $data_output_array;
	}
	
	public function setCustomerId($customerId)
	{
		$this->customerId = (int)$customerId;
	}
	
	public function getCustomerId()
	{
		return $this->customerId;
	}
}