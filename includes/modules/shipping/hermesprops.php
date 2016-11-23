<?php

/**
 * Hermes ProfiPaketService (ProPS)/PrivatPaketService (PriPS)
 */
class hermesprops {
	var $code, $title, $description, $icon, $enabled;
	
	public function __construct() {
		global $order;

		$this->code = 'hermesprops';
		$this->title = MODULE_SHIPPING_HERMESPROPS_TEXT_TITLE;
		$this->description = MODULE_SHIPPING_HERMESPROPS_TEXT_DESCRIPTION;
		$this->sort_order = MODULE_SHIPPING_HERMESPROPS_SORT_ORDER;
		$this->icon = '';
		$this->tax_class = MODULE_SHIPPING_HERMESPROPS_TAX_CLASS;
		$this->enabled = ((MODULE_SHIPPING_HERMESPROPS_STATUS == 'True') ? true : false);
		$this->icon = DIR_WS_ICONS.'hermes_logo.png';

		if(($this->enabled == true) && ((int)MODULE_SHIPPING_HERMESPROPS_ZONE > 0)) {
			$check_flag = false;
			$check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_HERMESPROPS_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
			while ($check = xtc_db_fetch_array($check_query)) {
				if($check['zone_id'] < 1) {
					$check_flag = true;
					break;
				} elseif ($check['zone_id'] == $order->delivery['zone_id']) {
					$check_flag = true;
					break;
				}
			}

			if ($check_flag == false) {
				$this->enabled = false;
			}
		}
	}
	
	function determinePacketClass($products) {
		require_once DIR_FS_INC.'/xtc_get_prid.inc.php';
		$classes = array('XS' => 0, 'S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4, 'XXL' => 5);
		$fclasses = array_flip($classes);
		$minclass = 0;
		foreach($products as $p) {
			$prid = xtc_get_prid($p['id']);
			$classquery = xtc_db_query("SELECT min_pclass FROM products_hermesoptions WHERE products_id = ". $prid);
			if(xtc_db_num_rows($classquery) == 0) {
				$min_pclass = 'XS';
			}
			else {
				$classrow = xtc_db_fetch_array($classquery);
				$min_pclass = $classrow['min_pclass'];
			}
			if($classes[$min_pclass] > $minclass) {
				$minclass = $classes[$min_pclass];
			}
		}
		return $fclasses[$minclass];
	}

	function quote($method = '') {
		global $order, $total_count;
		
		$packet_class = $this->determinePacketClass($order->products);
		
		$this->quotes = array('id' => $this->code,
													'module' => MODULE_SHIPPING_HERMESPROPS_TEXT_TITLE,
													'methods' => array(array(
																			'id' => $this->code,
																			'title' => MODULE_SHIPPING_HERMESPROPS_TEXT_WAY, // ." Paketklasse $packet_class",
																			'cost' => MODULE_SHIPPING_HERMESPROPS_HANDLING + constant('MODULE_SHIPPING_HERMESPROPS_COST_'.$packet_class))
																	  )
												 );

		if ($this->tax_class > 0) {
			$this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
		}

		if (xtc_not_null($this->icon)) $this->quotes['icon'] = xtc_image($this->icon, $this->title);

		return $this->quotes;
	}
	
	function check() {
		if(!isset($this->_check)) {
			$check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_HERMESPROPS_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function install() {
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_HERMESPROPS_STATUS', 'True', '6', '0', 'gm_cfg_select_option(array(\'True\', \'False\'), ', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_HERMESPROPS_MODE', 'True', '6', '0', 'gm_cfg_select_option(array(\'True\', \'False\'), ', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_HERMESPROPS_LABELPOS', '1', '6', '0', 'xtc_cfg_select_option(array(1, 2, 3, 4), ', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_HERMESPROPS_SERVICE', 'ProPS', '6', '0', 'xtc_cfg_select_option(array(\'ProPS\', \'PriPS\'), ', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_PARTNERID', '', '6', '1', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_APIPASSWORD', '', '6', '2', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_USERNAME', '', '6', '2', now())");
		//xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_PASSWORD', '', '6', '2', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_ALLOWED', 'DE,BE,DK,EE,FI,FR,GB,IE,IT,LV,LT,LU,MC,NL,AT,PL,PT,SE,SK,SI,ES,CZ,HU', '6', '3', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_COST_XS', '0.00', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_COST_S', '0.00', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_COST_M', '0.00', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_COST_L', '0.00', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_COST_XL', '0.00', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_COST_XXL', '0.00', '6', '4', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_HANDLING', '0', '6', '5', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_HERMESPROPS_TAX_CLASS', '0', '6', '6', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_HERMESPROPS_ZONE', '0', '6', '7', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_HERMESPROPS_SORT_ORDER', '0', '6', '8', now())");
	}

	function remove() {
		xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
	}

	function keys() {
		return array(
			'MODULE_SHIPPING_HERMESPROPS_STATUS',
			//'MODULE_SHIPPING_HERMESPROPS_MODE', 'MODULE_SHIPPING_HERMESPROPS_PARTNERID', 'MODULE_SHIPPING_HERMESPROPS_APIPASSWORD',
			//'MODULE_SHIPPING_HERMESPROPS_SERVICE',
			//'MODULE_SHIPPING_HERMESPROPS_LABELPOS',
			//'MODULE_SHIPPING_HERMESPROPS_USERNAME',
			//'MODULE_SHIPPING_HERMESPROPS_PASSWORD',
			'MODULE_SHIPPING_HERMESPROPS_COST_XS',
			'MODULE_SHIPPING_HERMESPROPS_COST_S',
			'MODULE_SHIPPING_HERMESPROPS_COST_M',
			'MODULE_SHIPPING_HERMESPROPS_COST_L',
			'MODULE_SHIPPING_HERMESPROPS_COST_XL',
			'MODULE_SHIPPING_HERMESPROPS_COST_XXL',
			'MODULE_SHIPPING_HERMESPROPS_HANDLING',
			'MODULE_SHIPPING_HERMESPROPS_ALLOWED', 'MODULE_SHIPPING_HERMESPROPS_TAX_CLASS', 'MODULE_SHIPPING_HERMESPROPS_ZONE',
			'MODULE_SHIPPING_HERMESPROPS_SORT_ORDER');
	}
}
MainFactory::load_origin_class('hermesprops');