<?php
/* --------------------------------------------------------------
   table.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(table.php,v 1.27 2003/02/05); www.oscommerce.com
   (c) 2003	 nextcommerce (table.php,v 1.8 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: table.php 1002 2005-07-10 16:11:37Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


  class table_ORIGIN {
    var $code, $title, $description, $icon, $enabled;


    public function __construct() {
      global $order;

      $this->code = 'table';
      $this->title = MODULE_SHIPPING_TABLE_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_TABLE_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_TABLE_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_TABLE_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_TABLE_STATUS == 'True') ? true : false);

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_TABLE_ZONE > 0) ) {
        $check_flag = false;
        $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_TABLE_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        while ($check = xtc_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
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


    function quote($method = '') {
      global $order, $shipping_weight, $shipping_num_boxes,$xtPrice;

      if (MODULE_SHIPPING_TABLE_MODE == 'price') {
        $order_total = $xtPrice->xtcRemoveCurr($_SESSION['cart']->show_total());
      } else {
        $order_total = $shipping_weight;
      }

      // BOF GM_MOD:
      $shipping = -1;

      $table_cost = preg_split('/[:,]/' , MODULE_SHIPPING_TABLE_COST);
      $size = sizeof($table_cost);
      for ($i=0, $n=$size; $i<$n; $i+=2) {
        if ($order_total <= $table_cost[$i]) {
          $shipping = $table_cost[$i+1];
          break;
        }
      }

      // BOF GM_MOD
      if(MODULE_SHIPPING_TABLE_MODE == 'weight' && $shipping != -1)
      {
      	$shipping = $shipping * $shipping_num_boxes + MODULE_SHIPPING_TABLE_HANDLING;
      }
      elseif($shipping == -1)
      {
      	$error = true;
      	$shipping = 0;
      	$shipping_method = MODULE_SHIPPING_DP_UNDEFINED_RATE;
      }

      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_TABLE_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => MODULE_SHIPPING_TABLE_TEXT_WAY,
                                                     'cost' => $shipping)));
	  // EOF GM_MOD

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (xtc_not_null($this->icon)) $this->quotes['icon'] = xtc_image($this->icon, $this->title);

      // BOF GM_MOD:
      if ($error == true) $this->quotes['error'] = MODULE_SHIPPING_TABLE_UNDEFINED_RATE;

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_TABLE_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SHIPPING_TABLE_STATUS', 'True', '6', '0', 'gm_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TABLE_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TABLE_COST', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_SHIPPING_TABLE_MODE', 'weight', '6', '0', 'gm_cfg_select_option(array(\'weight\', \'price\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TABLE_HANDLING', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_TABLE_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_TABLE_ZONE', '0', '6', '0', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_TABLE_SORT_ORDER', '0', '6', '0', now())");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_SHIPPING_TABLE_STATUS', 'MODULE_SHIPPING_TABLE_COST', 'MODULE_SHIPPING_TABLE_MODE', 'MODULE_SHIPPING_TABLE_HANDLING','MODULE_SHIPPING_TABLE_ALLOWED', 'MODULE_SHIPPING_TABLE_TAX_CLASS', 'MODULE_SHIPPING_TABLE_ZONE', 'MODULE_SHIPPING_TABLE_SORT_ORDER');
    }
  }
  
MainFactory::load_origin_class('table');