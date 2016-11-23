<?php
/* --------------------------------------------------------------
   ot_gambioultra.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_loworderfee.php,v 1.11 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (ot_loworderfee.php,v 1.7 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   

  class ot_gambioultra_ORIGIN {
    var $title, $output;

    public function __construct() {
    	global $xtPrice;
      $this->code = 'ot_gambioultra';
      $this->title = MODULE_ORDER_TOTAL_GAMBIOULTRA_TITLE;
      $this->description = MODULE_ORDER_TOTAL_GAMBIOULTRA_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_GAMBIOULTRA_SORT_ORDER;

      $this->output = array();
    }
    
		function nc_get_product_shipping_costs() 
		{
			global $xtPrice;
			
			$products = $_SESSION['cart']->get_products();
			$costs 		= 0;
			$infos		= array();
			
			for($i=0; $i<sizeof($products); $i++) {
				$result = mysqli_query($GLOBALS["___mysqli_ston"], '
					SELECT 
						p.nc_ultra_shipping_costs AS costs,
						pd.products_name					AS products_name
					FROM 	
						products p
					LEFT JOIN 
						products_description AS pd USING (products_id)
					WHERE
						p.nc_ultra_shipping_costs	 != 0																	AND
						p.products_id 							= "'. $products[$i]['id'] 			.'"	AND
						pd.language_id 							= "'. $_SESSION['languages_id'] .'"
				');

				if(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) == 0)  {
					while(($row = mysqli_fetch_array($result) )) {
						$costs 	+= $xtPrice->xtcFormat($row['costs'] * $products[$i]['quantity'], false, 0, true);

						
						$infos[] = array(
												'title' 			=> gm_prepare_number($products[$i]['quantity'], $xtPrice->currencies[$xtPrice->actualCurr]['decimal_point']) .'x '. $row['products_name'],
												'price' 			=> $xtPrice->xtcFormat($row['costs'] * $products[$i]['quantity'], true, MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS, true),
												'price_plain' => $xtPrice->xtcFormat($row['costs'] * $products[$i]['quantity'], false, 0, true)
											);
					}
				}
			}
			$output = array(
									'costs' => $costs,
									'infos' => $infos
							);
			return $output;
		}

    function process() {
      global $order, $xtPrice;
	  
	  if($order->info['shipping_class'] == 'selfpickup_selfpickup')
	  {
		  $this->output = array();
		  return;
	  }
	  
      //include needed functions
      require_once(DIR_FS_INC . 'xtc_calculate_tax.inc.php');
      
      if (MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS == 'true' && $order->info['shipping_class'] != 'selfpickup_selfpickup') 
      {
        switch (MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION) {
          case 'national':
            if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;
          case 'international':
            if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;
          case 'both':
            $pass = true; break;
          default:
            $pass = false; break;
        }

        if($pass == true) 
        {
			$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array( 'gambioultra', $_SESSION['languages_id']), false);
			
	      	$nc_ultra_data 		= $this->nc_get_product_shipping_costs();
        	$nc_ultra_details = '';
        	
          $tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
          $tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);

					if (MODULE_ORDER_TOTAL_GAMBIOULTRA_DETAILS == 'false') // without details...
					{
						if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
						  $order->info['tax'] += xtc_calculate_tax($nc_ultra_data['costs'], $tax);
		          $order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += xtc_calculate_tax($nc_ultra_data['costs'], $tax);
		          $order->info['total'] += $nc_ultra_data['costs'] + xtc_calculate_tax($nc_ultra_data['costs'], $tax);
		          $gambioultra_fee=xtc_add_tax($nc_ultra_data['costs'], $tax);
		        }
		        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
							$gambioultra_fee=$nc_ultra_data['costs'];
							$order->info['tax'] += xtc_calculate_tax($nc_ultra_data['costs'], $tax);
			        $order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += xtc_calculate_tax($nc_ultra_data['costs'], $tax);
							$order->info['subtotal'] += $gambioultra_fee;
			        $order->info['total'] += $gambioultra_fee;
		        }
		        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] != 1) {
							$gambioultra_fee=$nc_ultra_data['costs'];
							$order->info['subtotal'] += $gambioultra_fee;
		    	    $order->info['total'] += $gambioultra_fee;
		        }
		        $output_title  = $coo_text_mgr->get_text('name') . ':';
	          $this->output[] = array('title' => $output_title,
	                                  'text' 	=> $xtPrice->xtcFormat($gambioultra_fee, true),
	                                  'value' => $gambioultra_fee);
					}
					else //show details...
					{
	          foreach ($nc_ultra_data['infos'] as $info) {
							if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
								$order->info['tax'] += xtc_calculate_tax($info['price_plain'], $tax);
			          $order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += xtc_calculate_tax($info['price_plain'], $tax);
			          $order->info['total'] += $info['price_plain'] + xtc_calculate_tax($info['price_plain'], $tax);
			          $gambioultra_fee = xtc_add_tax($info['price_plain'], $tax);
			        }
			        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
								$gambioultra_fee = $info['price_plain'];
								$order->info['tax'] += xtc_calculate_tax($info['price_plain'], $tax);
				        $order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += xtc_calculate_tax($info['price_plain'], $tax);
								$order->info['subtotal'] += $gambioultra_fee;
				        $order->info['total'] += $gambioultra_fee;
			        }
			        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] != 1) {
								$gambioultra_fee = $info['price_plain'];
								$order->info['subtotal'] += $gambioultra_fee;
			    	    $order->info['total'] += $gambioultra_fee;
			        }
			        $output_title  	= $coo_text_mgr->get_text('name') . ': ';
			        $this->output[] = array('title' => $output_title .' '. $info['title'] . ': ',
		                                  'text' => $xtPrice->xtcFormat($gambioultra_fee, true),
		                                  'value' => $gambioultra_fee);

	          }//end foreach
					}
        }
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS', 'MODULE_ORDER_TOTAL_GAMBIOULTRA_SORT_ORDER', 'MODULE_ORDER_TOTAL_GAMBIOULTRA_OUTPUT_NAME', 'MODULE_ORDER_TOTAL_GAMBIOULTRA_DETAILS', 'MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION', 'MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS');
    }

    function install() {
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_GAMBIOULTRA_SORT_ORDER', '31', '6', '2', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added) values ('MODULE_ORDER_TOTAL_GAMBIOULTRA_OUTPUT_NAME', 'Sperrgutzuschlag', '6', '2', now())");
			xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_GAMBIOULTRA_DETAILS', 'true', '6', '1','xtc_cfg_select_option(array(\'true\', \'false\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION', 'both','6', '6', 'xtc_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
      xtc_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added) values ('MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS', '0','6', '7', 'xtc_get_tax_class_title', 'xtc_cfg_pull_down_tax_classes(', now())");
    }

    function remove() {
      xtc_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
  
MainFactory::load_origin_class('ot_gambioultra');