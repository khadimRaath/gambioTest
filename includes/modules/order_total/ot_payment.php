<?php
/* --------------------------------------------------------------
   ot_payment.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

$Id: ot_payment.php,v 1.2.3 (3.0.4) 2005/10/27 13:55:50 Anotherone Exp $

  André Estel / Estelco http://www.estelco.de

  Copyright (C) 2005 Estelco

  based on:
  Andreas Zimmermann / IT eSolutions http://www.it-esolutions.de

  Copyright (C) 2004 IT eSolutions
  -----------------------------------------------------------------------------------------

  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com 

  Released under the GNU General Public License

  ---------------------------------------------------------------------------------------*/

  class ot_payment_ORIGIN {
    var $title, $output;

    public function __construct() {
      $this->code = 'ot_payment';
      $this->title = MODULE_ORDER_TOTAL_PAYMENT_TITLE;
      $this->description = MODULE_ORDER_TOTAL_PAYMENT_DESCRIPTION;
      $this->enabled = MODULE_ORDER_TOTAL_PAYMENT_STATUS=='true'?true:false;
      $this->sort_order = MODULE_ORDER_TOTAL_PAYMENT_SORT_ORDER;
      $this->include_shipping = MODULE_ORDER_TOTAL_PAYMENT_INC_SHIPPING;
      $this->include_tax = MODULE_ORDER_TOTAL_PAYMENT_INC_TAX;
      $this->percentage = MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE;
      $this->percentage2 = MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE2; //neu
//    $this->minimum = MODULE_ORDER_TOTAL_PAYMENT_MINIMUM;
//    $this->minimum2 = MODULE_ORDER_TOTAL_PAYMENT_MINIMUM2; //neu
      $this->calculate_tax = MODULE_ORDER_TOTAL_PAYMENT_CALC_TAX;
      $this->howto_calc = MODULE_ORDER_TOTAL_PAYMENT_HOWTO_CALC;
      $this->amount1_desc = MODULE_ORDER_TOTAL_PAYMENT_AMOUNT1;
      $this->amount2_desc = MODULE_ORDER_TOTAL_PAYMENT_AMOUNT2;
//    $this->credit_class = true;
//    $this->Price=$price;
      $this->output = array();
    }

    function process() {
      global $order, $currencies, $xtPrice;

      $allowed_zones = explode(',', MODULE_ORDER_TOTAL_PAYMENT_ALLOWED);

      if ($this->enabled && (in_array($_SESSION['delivery_zone'], $allowed_zones) == true || MODULE_ORDER_TOTAL_PAYMENT_ALLOWED == '')) {
        $discount = $this->calculate_credit($this->xtc_order_total());
        if ($discount['sum']!=0) {
           $this->deduction = $discount['sum'];
           if ($discount['amount1']!=0) {
               $this->output[] = array('title' => abs($discount['pro1']) . "% " . $this->amount1_desc . ':',
                                       'text' => $discount['amount1']>0 ? $xtPrice->xtcFormat($discount['sum'], true) : $xtPrice->xtcFormat($discount['sum'], true),
                                       'value' => $discount['sum']);
           } elseif ($discount['amount2']!=0) {
               $this->output[] = array('title' => abs($discount['pro2']) . "% " . $this->amount2_desc . ':',
                                       'text' => $discount['amount2']>0 ? $xtPrice->xtcFormat($discount['sum'], true) : $xtPrice->xtcFormat($discount['sum'], true),
                                       'value' => $discount['sum']);
           }
           // BOF GM_MOD:
           $order->info['subtotal'] = $order->info['subtotal'] + $discount['sum'];
           $order->info['total'] = $order->info['total'] + $discount['sum'];
        }
      }
    }


  function calculate_credit($amount) {
    global $order, $customer_id, $payment;
    $od_amount=0;
    $od_amount2=0; //neu
    $discount = array();
    $tod_amount = 0;
    $tod_amount2 = 0;
    $do = false;

    $discount_table = (preg_split('/[:,]/' , $this->percentage));

    for ($i=0; $i<sizeof($discount_table); $i+=2) {
      if ($amount >= $discount_table[$i]) {
        $od_pc = $discount_table[$i+1];
        $minimum = $discount_table[$i];
      } else {
        break;
      }
    }

    $discount_table = (preg_split('/[:,]/' , $this->percentage2));

    for ($i=0; $i<sizeof($discount_table); $i+=2) {
      if ($amount >= $discount_table[$i]) {
        $od_pc2 = $discount_table[$i+1];
        $minimum2 = $discount_table[$i];
      } else {
        break;
      }
    }

    if ($amount >= $minimum) {
    $table = explode(',' , MODULE_ORDER_TOTAL_PAYMENT_TYPE);
    for ($i = 0; $i < count($table); $i++) {
          if ($_SESSION['payment'] == $table[$i]) $do = true;
        }
    if ($do) {
    // Calculate tax reduction if necessary
    if($this->calculate_tax == 'true') {
    // Calculate main tax reduction
      // BOF GM_MOD:
      $tod_amount = round($order->info['tax']*100)/100*$od_pc/100;
      $order->info['tax'] = $order->info['tax'] - $tod_amount;
      // Calculate tax group deductions
      reset($order->info['tax_groups']);
      while (list($key, $value) = each($order->info['tax_groups'])) {
        // BOF GM_MOD:
      	$god_amount = round($value*100)/100*$od_pc/100;
        $order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
      }
    }
    // BOF GM_MOD:
    $od_amount = round($amount*100)/100*$od_pc/100;
    //    $od_amount = $od_amount + $tod_amount; //auskommentieren, da sonst die Steuer 2x rabattiert wird
    }
    }

//Zweiten Rabatt berechnen...

    $amount2 = $amount - $od_amount; //diese zeile anpassen um Prozente gleichzeitig ("$amount2 = $amount;") oder nacheinander ("$amount2 = $amount - $od_amount;") zu berechnen
    $do = false;
    if ($amount2 >= $minimum2) {
    $table = explode(',' , MODULE_ORDER_TOTAL_PAYMENT_TYPE2);
    for ($i = 0; $i < count($table); $i++) {
          if ($_SESSION['payment'] == $table[$i]) $do = true;
        }
    if ($do) {
    // Calculate tax reduction if necessary
    if($this->calculate_tax == 'true') {
    // Calculate main tax reduction
      // BOF GM_MOD:
      $tod_amount2 = round($order->info['tax']*100)/100*$od_pc2/100;
      $order->info['tax'] = $order->info['tax'] - $tod_amount2; //diese Zeile auskommentieren, wenn beide Prozente zusammen berechnet werden sollen
      // Calculate tax group deductions
      reset($order->info['tax_groups']);
      while (list($key, $value) = each($order->info['tax_groups'])) {
        // BOF GM_MOD:
      	$god_amount2 = round($value*100)/100*$od_pc2/100;
        $order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount2;
      }
    }
    // BOF GM_MOD:
    $od_amount2 = round($amount2*100)/100*$od_pc2/100;
    //    $od_amount2 = $od_amount2 + $tod_amount2; //auskommentieren, da sonst die Steuer 2x rabattiert wird
    }
    }
    //  $order->info['tax'] = $order->info['tax'] - $tod_amount - $tod_amount2; //diese Zeile auskommentieren, wenn die Prozente nacheinander berechnet werden sollen
    $discount['sum'] = -($od_amount + $od_amount2);
    $discount['amount1'] = $od_amount;
    $discount['amount2'] = $od_amount2;
    $discount['pro1'] = $od_pc;
    $discount['pro2'] = $od_pc2;
    return $discount;
  }


  function xtc_order_total() {
    global  $order, $cart;
    $order_total = $order->info['total'];
// Check if gift voucher is in cart and adjust total
    $products = $_SESSION['cart']->get_products();
    for ($i=0; $i<sizeof($products); $i++) {
      $t_prid = xtc_get_prid($products[$i]['id']);
      $gv_query = xtc_db_query("select products_price, products_tax_class_id, products_model from " . TABLE_PRODUCTS . " where products_id = '" . $t_prid . "'");
		// bof gm
		$gv_result = xtc_db_fetch_array($gv_query);
		if(!is_object($cart)) {
			$cart = $_SESSION['cart'];
		}
		// eof gm	
      if (preg_match('/^GIFT/', addslashes($gv_result['products_model']))) {
        $qty = $cart->get_quantity($t_prid);
        $products_tax = xtc_get_tax_rate($gv_result['products_tax_class_id']);
        if ($this->include_tax =='false') {
           $gv_amount = $gv_result['products_price'] * $qty;
        } else {
          $gv_amount = ($gv_result['products_price'] + xtc_calculate_tax($gv_result['products_price'],$products_tax)) * $qty;
        }
        $order_total=$order_total - $gv_amount;
      }
    }
    if ($this->include_shipping == 'false') $order_total=$order_total-$order->info['shipping_cost'];
    if ($this->include_tax == 'false') if($_SESSION['customers_status']['customers_status_add_tax_ot']=='0') $order_total=$order_total-$order->info['tax'];
    return $order_total;
  }


    function check() {
      if (!isset($this->check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_PAYMENT_STATUS'");
        $this->check = xtc_db_num_rows($check_query);
      }

      return $this->check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_PAYMENT_STATUS', 'MODULE_ORDER_TOTAL_PAYMENT_SORT_ORDER', 'MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE', 'MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE2', 'MODULE_ORDER_TOTAL_PAYMENT_TYPE', 'MODULE_ORDER_TOTAL_PAYMENT_TYPE2', 'MODULE_ORDER_TOTAL_PAYMENT_INC_SHIPPING', 'MODULE_ORDER_TOTAL_PAYMENT_INC_TAX', 'MODULE_ORDER_TOTAL_PAYMENT_CALC_TAX', 'MODULE_ORDER_TOTAL_PAYMENT_ALLOWED', 'MODULE_ORDER_TOTAL_PAYMENT_TAX_CLASS'); //, 'MODULE_ORDER_TOTAL_PAYMENT_HOWTO_CALC', 'MODULE_ORDER_TOTAL_PAYMENT_MINIMUM', 'MODULE_ORDER_TOTAL_PAYMENT_MINIMUM2'
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_SORT_ORDER', '49', '6', '2', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_INC_SHIPPING', 'false', '6', '5', 'xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_INC_TAX', 'true', '6', '6','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE', '100:4', '6', '4', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE2', '100:2', '6', '4', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_CALC_TAX', 'true', '6', '5','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
//    xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function ,date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_HOWTO_CALC', 'Zusammen', '6', '2','xtc_cfg_select_option(array(\'Zusammen\', \'Einzeln\'), ', now())");
//    xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_MINIMUM', '100', '6', '5', now())");
//    xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_MINIMUM2', '100', '6', '5', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_TYPE', 'moneyorder', '6', '3', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_TYPE2', 'cod', '6', '3', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_ALLOWED', '',   '6', '2', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_PAYMENT_TAX_CLASS', '0','6', '7', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      $keys = '';
      $keys_array = $this->keys();
      for ($i=0; $i<sizeof($keys_array); $i++) {
        $keys .= "'" . $keys_array[$i] . "',";
      }
      $keys = substr($keys, 0, -1);

      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in (" . $keys . ")");
    }

  }

MainFactory::load_origin_class('ot_payment');