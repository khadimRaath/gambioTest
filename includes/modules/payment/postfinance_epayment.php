<?php
/* -----------------------------------------------------------------------------------------
   $Id: postfinance_epayment.php,v1.4 2012/02/14
   

   Changelog:
   v1.4
   - Fix error without phonenumber,
   - Payment icons
   
   v1.3 
   - backlinks URL = $catalogurl instead of HTTP_SERVER /sna
   
   v1.2
   - Real order_id, function payment_action, GET request

   v1.1
   - New SHA-1 security implementation

   Copyright (c) 2009 swisswebXperts Näf www.swisswebxperts.ch
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -----------------------------------------------------------------------------------------
*/
  class postfinance_epayment_ORIGIN {
    var $code, $title, $description, $enabled, $orderid, $productive;


    public function __construct() {
      global $order;

      $this->code = 'postfinance_epayment';
      $this->title = MODULE_PAYMENT_POSTFINANCE_EPAYMENT_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_POSTFINANCE_EPAYMENT_TEXT_DESCRIPTION;
      $this->info = MODULE_PAYMENT_POSTFINANCE_EPAYMENT_TEXT_INFO;
      $this->sort_order = MODULE_PAYMENT_POSTFINANCE_EPAYMENT_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_POSTFINANCE_EPAYMENT_STATUS == 'True') ? true : false);
      $this->orderid = '';
      $this->productive = MODULE_PAYMENT_POSTFINANCE_EPAYMENT_PROD;
      $this->images = explode(',', MODULE_PAYMENT_POSTFINANCE_EPAYMENT_IMAGES);

      if ((int)MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
			
		if ($this->productive == 'True') // PRODUKTIV LINK
			$this->form_action_url = 'https://e-payment.postfinance.ch/ncol/prod/orderstandard.asp';
		else  								 // TEST LINK
			$this->form_action_url = 'https://e-payment.postfinance.ch/ncol/test/orderstandard.asp';
			
		$this->tmpOrders = true;
		$this->tmpStatus = 1;
	}


    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ZONE > 0) ) {
        $check_flag = false;
        $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = xtc_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      
      $images = '';
      foreach ($this->images as $image) {
        $images .= '<img src="includes/modules/payment/images/postfinance_' . $image . '.gif" />&nbsp;';
      }  
                
      if ($images != '') {          
        $this->info .= '<br />' . trim($images);
      }

      return array('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return false;
    }

    function process_button() {

	  return false;
    }

	function payment_action() {
        global $order, $xtPrice, $currencies, $customer_id, $insert_id, $tmp;		
		
		if (MODULE_PAYMENT_POSTFINANCE_EPAYMENT_CURRENCY == 'Selected Currency') {
			$currency = $_SESSION['currency'];
		} else {
			$currency = MODULE_PAYMENT_POSTFINANCE_EPAYMENT_CURRENCY;
		}

		if (MODULE_PAYMENT_POSTFINANCE_EPAYMENT_LANGUAGE == 'Selected language') {
			$language = $_SESSION['language_code'];
		
			switch($language) {
				case 'en': $language = 'en_US';
					break;
				case 'fr': $language = 'fr_FR';
					break;
				case 'nl': $language = 'nl_NL';
					break;
				case 'be': $language = 'nl_BE';
					break;
				case 'it': $language = 'it_IT';
					break;
				case 'de': $language = 'de_DE';
					break;
				case 'es': $language = 'es_ES';
					break;
				case 'no': $language = 'no_NO';
					break;
				case 'tr': $language = 'tr_TR';
					break;
				default : $language = 'en_US';
					break;
			}				
		} else {
			$language = MODULE_PAYMENT_POSTFINANCE_EPAYMENT_LANGUAGE;
		}
			
		$this->orderid = $this->getorderid();

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			$amount = round($order->info['total'] + $order->info['tax'], $xtPrice->get_decimal_places($currency));
		} else {
			$amount = round($order->info['total'], $xtPrice->get_decimal_places($currency));
		}
		$amount = $amount * 100;

		if (ENABLE_SSL == true) {
			$homeurl = HTTPS_SERVER;
		} else {
			$homeurl = HTTP_SERVER;
		}
		
		$catalogurl = $homeurl . DIR_WS_CATALOG;

		// Alphabetisch sortiert
		$arrParams = array(
			'amount' 		=> $amount,
			'currency' 		=> $currency,
			'language' 		=> $language,
			'homeurl'		=> 'none',
			'orderID' 		=> $insert_id,
			'PSPID' 		=> MODULE_PAYMENT_POSTFINANCE_EPAYMENT_PSPID,
			'CN' 			=> $order->customer['firstname'] . ' ' . $order->customer['lastname'],
			'EMAIL' 		=> $order->customer['email_address'],
			'owneraddress' 	=> $order->customer['street_address'],
			'ownerZIP' 		=> $order->customer['postcode'],
			'ownertown'		=> $order->customer['city'],
			'ownercty'		=> $order->customer['country']['iso_code_2'],
			'TITLE'			=> STORE_NAME,
			'accepturl'		=> $catalogurl . '/checkout_process.php',
			'declineurl'	=> $catalogurl . '/checkout_payment.php',
			'exceptionurl'	=> $catalogurl . '/checkout_payment.php',
			'cancelurl'     => $catalogurl . '/checkout_payment.php',
			'backurl'		=> $catalogurl . '/checkout_payment.php'
		);
        
        if (strlen($order->customer['telephone']) > 0) {
            $arrParams['ownertelno'] = $order->customer['telephone'];
        }
        
		// Alphabetisch sortieren
		function my_sort($a, $b) {
			$a = strtolower($a);
			$b = strtolower($b);
		
			if ($a == $b) return 0;
			return ($a < $b) ? -1 : 1;
		}
		
		uksort($arrParams, "my_sort");
			
		$query = '';			
		$shaStr = '';
		
		foreach($arrParams as $key => $value) {
			$query  .= $key . '=' . urlencode($value) . '&';
			$shaStr .= strtoupper($key) . '=' . $value . MODULE_PAYMENT_POSTFINANCE_EPAYMENT_SHA1_SIGNATURE;
		}
		
		$shasign = sha1($shaStr);
		$query .= 'SHASign=' .  strtoupper($shasign);

		xtc_redirect($this->form_action_url . '?' . $query);
		exit;
	}

    function before_process() {
      return false;
    }

    function after_process() {
		global $insert_id;
		if ($this->order_status)
			xtc_db_query("UPDATE ".TABLE_ORDERS." SET orders_status='".$this->order_status."' WHERE orders_id='".$insert_id."'");
    }

    function output_error() {
        $error = array('title' => MODULE_PAYMENT_POSTFINANCE_EPAYMENT_TEXT_ERROR,
                 'error' => MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ERROR);
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
  	  xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values 
				('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_IMAGES', 'postcard', '6', '0', now())");     
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values
	  		    ('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_PROD', 'False', '6', '2', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
	  xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values
                ('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_STATUS', 'True', '6', '3', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values 
				('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_PSPID', '',  '6', '4', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values 
				('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_SHA1_SIGNATURE', '',  '6', '4', now())");
	  xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values
  		        ('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_LANGUAGE', 'Selected language', '6', '1', 'xtc_cfg_select_option(array(\'Selected language\', \'de_DE\', \'fr_FR\', \'it_IT\', \'en_US\', \'es_ES\', \'nl_NL\', \'nl_BE\', \'no_NO\', \'tr_TR\'), ', now())");
  	  xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values 
				('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ALLOWED', 'CH,LI', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) values
				('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_CURRENCY', 'Selected Currency',  '6', '6', 'xtc_cfg_select_option(array(\'Selected Currency\',\'CHF\',\'EUR\',\'USD\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) values
				('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_SORT_ORDER', '0', '6', '0', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) values 
				('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ZONE', '0', '6', '2', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) values
				('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ORDER_STATUS_ID', '0',  '6', '0', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_POSTFINANCE_EPAYMENT_STATUS','MODULE_PAYMENT_POSTFINANCE_EPAYMENT_PROD', 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_IMAGES','MODULE_PAYMENT_POSTFINANCE_EPAYMENT_LANGUAGE', 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ALLOWED', 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_PSPID', 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_CURRENCY', 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ORDER_STATUS_ID', 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_SORT_ORDER', 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_ZONE', 'MODULE_PAYMENT_POSTFINANCE_EPAYMENT_SHA1_SIGNATURE');
    }

    function getorderid() {
      return xtc_create_random_value(20, "digits");
    }
  }

MainFactory::load_origin_class('postfinance_epayment');