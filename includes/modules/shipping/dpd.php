<?php
/* --------------------------------------------------------------
   zones.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(zones.php,v 1.19 2003/02/05); www.oscommerce.com
   (c) 2003	 nextcommerce (zones.php,v 1.7 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: zones.php,v 1.1 2003/09/06 22:13:54 fanta2k Exp $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


/*
 * USAGE
 * By default, the module comes with support for 1 zone.  This can be
 * easily changed by editing the line below in the zones constructor
 * that defines $this->num_zones.
 *
 * Next, you will want to activate the module by going to the Admin screen,
 * clicking on Modules, then clicking on Shipping.  A list of all shipping
 * modules should appear.  Click on the green dot next to the one labeled
 * zones.php.  A list of settings will appear to the right.  Click on the
 * Edit button.
 *
 * PLEASE NOTE THAT YOU WILL LOSE YOUR CURRENT SHIPPING RATES AND OTHER
 * SETTINGS IF YOU TURN OFF THIS SHIPPING METHOD.  Make sure you keep a
 * backup of your shipping settings somewhere at all times.
 *
 * If you want an additional handling charge applied to orders that use this
 * method, set the Handling Fee field.
 *
 * Next, you will need to define which countries are in each zone.  Determining
 * this might take some time and effort.  You should group a set of countries
 * that has similar shipping charges for the same weight.  For instance, when
 * shipping from the US, the countries of Japan, Australia, New Zealand, and
 * Singapore have similar shipping rates.  As an example, one of my customers
 * is using this set of zones:
 *   1: USA
 *   2: Canada
 *   3: Austria, Belgium, Great Britain, France, Germany, Greenland, Iceland,
 *      Ireland, Italy, Norway, Holland/Netherlands, Denmark, Poland, Spain,
 *      Sweden, Switzerland, Finland, Portugal, Israel, Greece
 *   4: Japan, Australia, New Zealand, Singapore
 *   5: Taiwan, China, Hong Kong
 *
 * When you enter these country lists, enter them into the Zone X Countries
 * fields, where "X" is the number of the zone.  They should be entered as
 * two character ISO country codes in all capital letters.  They should be
 * separated by commas with no spaces or other punctuation. For example:
 *   1: US
 *   2: CA
 *   3: AT,BE,GB,FR,DE,GL,IS,IE,IT,NO,NL,DK,PL,ES,SE,CH,FI,PT,IL,GR
 *   4: JP,AU,NZ,SG
 *   5: TW,CN,HK
 *
 * Now you need to set up the shipping rate tables for each zone.  Again,
 * some time and effort will go into setting the appropriate rates.  You
 * will define a set of weight ranges and the shipping price for each
 * range.  For instance, you might want an order than weighs more than 0
 * and less than or equal to 3 to cost 5.50 to ship to a certain zone.
 * This would be defined by this:  3:5.5
 *
 * You should combine a bunch of these rates together in a comma delimited
 * list and enter them into the "Zone X Shipping Table" fields where "X"
 * is the zone number.  For example, this might be used for Zone 1:
 *   1:3.5,2:3.95,3:5.2,4:6.45,5:7.7,6:10.4,7:11.85, 8:13.3,9:14.75,10:16.2,11:17.65,
 *   12:19.1,13:20.55,14:22,15:23.45
 *
 * The above example includes weights over 0 and up to 15.  Note that
 * units are not specified in this explanation since they should be
 * specific to your locale.
 *
 * CAVEATS
 * At this time, it does not deal with weights that are above the highest amount
 * defined.  This will probably be the next area to be improved with the
 * module.  For now, you could have one last very high range with a very
 * high shipping rate to discourage orders of that magnitude.  For
 * instance:  999:1000
 *
 * If you want to be able to ship to any country in the world, you will
 * need to enter every country code into the Country fields. For most
 * shops, you will not want to enter every country.  This is often
 * because of too much fraud from certain places. If a country is not
 * listed, then the module will add a $0.00 shipping charge and will
 * indicate that shipping is not available to that destination.
 * PLEASE NOTE THAT THE ORDER CAN STILL BE COMPLETED AND PROCESSED!
 *
 * It appears that the osC shipping system automatically rounds the
 * shipping weight up to the nearest whole unit.  This makes it more
 * difficult to design precise shipping tables.  If you want to, you
 * can hack the shipping.php file to get rid of the rounding.
 *
 * Lastly, there is a limit of 255 characters on each of the Zone
 * Shipping Tables and Zone Countries.
 *
 *  Released under the GNU General Public License
 *
 */

  class dpd_ORIGIN {
    var $code, $title, $description, $enabled, $num_zones;

/**
 * class constructor
 */
    public function __construct() {
      $this->code = 'dpd';
      $this->title = MODULE_SHIPPING_DPD_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_DPD_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_DPD_SORT_ORDER;
      $this->icon = '';
      $this->tax_class = MODULE_SHIPPING_DPD_TAX_CLASS;
      $this->enabled = ((MODULE_SHIPPING_DPD_STATUS == 'True') ? true : false);

/**
 * CUSTOMIZE THIS SETTING FOR THE NUMBER OF ZONES NEEDED
 */
      $this->num_zones = 10;
    }

/**
 * class methods
 */
    function quote($method = '') {
      // BOF GM_MOD:
      global $order, $shipping_weight, $shipping_num_boxes;

      $dest_country = $order->delivery['country']['iso_code_2'];
      $dest_zone = 0;
      $error = false;

      for ($i=1; $i<=$this->num_zones; $i++) {
        $countries_table = constant('MODULE_SHIPPING_DPD_COUNTRIES_' . $i);
        $country_zones = explode(',', $countries_table);
        if (in_array($dest_country, $country_zones)) {
          $dest_zone = $i;
          break;
        }
      }

      if ($dest_zone == 0) {
        $error = true;
      } else {
        $shipping = -1;
        $zones_cost = constant('MODULE_SHIPPING_DPD_COST_' . $dest_zone);

        $zones_table = preg_split('/[:,]/', $zones_cost);
        $size = sizeof($zones_table);
        for ($i=0; $i<$size; $i+=2) {
          if ($shipping_weight <= $zones_table[$i]) {
            $shipping = $zones_table[$i+1];
            // BOF GM_MOD:
            $shipping_method = MODULE_SHIPPING_DPD_TEXT_WAY . ' ' . $dest_country . ': (' . $shipping_num_boxes . ' x ' . $shipping_weight . ' ' . MODULE_SHIPPING_DPD_TEXT_UNITS . ')';
            break;
          }
        }

        if ($shipping == -1) {
          $error = true;
          $shipping_cost = 0;
          $shipping_method = MODULE_SHIPPING_DPD_UNDEFINED_RATE;
        } else {
          $shipping_cost = ($shipping + constant('MODULE_SHIPPING_DPD_HANDLING_' . $dest_zone));
        }
      }

      // BOF GM_MOD:
      $this->quotes = array('id' => $this->code,
                            'module' => MODULE_SHIPPING_DPD_TEXT_TITLE,
                            'methods' => array(array('id' => $this->code,
                                                     'title' => $shipping_method,
                                                     'cost' => $shipping_cost * $shipping_num_boxes)));

      if ($this->tax_class > 0) {
        $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
      }

      if (xtc_not_null($this->icon)) $this->quotes['icon'] = xtc_image($this->icon, $this->title);

      if ($error == true) $this->quotes['error'] = MODULE_SHIPPING_DPD_UNDEFINED_RATE;

      return $this->quotes;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_DPD_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_SHIPPING_DPD_STATUS', 'True', '6', '0', 'gm_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_ALLOWED', '', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_SHIPPING_DPD_TAX_CLASS', '0', '6', '0', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_SORT_ORDER', '0', '6', '0', now())");
      for ($i = 1; $i <= $this->num_zones; $i++) {
        $default_countries = '';
        if ($i == 1) {
          $default_countries = 'US,CA';
        }
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_COUNTRIES_" . $i ."', '" . $default_countries . "', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_COST_" . $i ."', '3:8.50,7:10.50,99:20.00', '6', '0', now())");
        xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_SHIPPING_DPD_HANDLING_" . $i."', '0', '6', '0', now())");
      }
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      $keys = array('MODULE_SHIPPING_DPD_STATUS','MODULE_SHIPPING_DPD_ALLOWED', 'MODULE_SHIPPING_DPD_TAX_CLASS', 'MODULE_SHIPPING_DPD_SORT_ORDER');

      for ($i=1; $i<=$this->num_zones; $i++) {
        $keys[] = 'MODULE_SHIPPING_DPD_COUNTRIES_' . $i;
        $keys[] = 'MODULE_SHIPPING_DPD_COST_' . $i;
        $keys[] = 'MODULE_SHIPPING_DPD_HANDLING_' . $i;
      }

      return $keys;
    }
  }
  
MainFactory::load_origin_class('dpd');