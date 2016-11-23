<?php
/* --------------------------------------------------------------
   shipping.php 2014-10-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shipping.php,v 1.22 2003/05/08); www.oscommerce.com
   (c) 2003	 nextcommerce (shipping.php,v 1.9 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: shipping.php 1305 2005-10-14 10:30:03Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  require_once(DIR_FS_INC . 'xtc_in_array.inc.php');
  if (!class_exists('shipping_ORIGIN', false))
  {
	class shipping_ORIGIN {
	  var $modules;

	  // class constructor
	  public function __construct($module = '') {
		global $PHP_SELF, $order;
		$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
	  		
	  if (empty($order))
	  {
		  if ($_SESSION['cart']->count_contents_non_virtual() > 0) { // cart contains products that are NOT virtual
			if (!isset ($_SESSION['sendto'])) {
				$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
			} else {
				// verify the selected shipping address
				$check_address_query = xtc_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_book_id = '".(int) $_SESSION['sendto']."'");
				$check_address = xtc_db_fetch_array($check_address_query);

				if ($check_address['total'] != '1') {
					$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
					if (isset ($_SESSION['shipping']))
						unset ($_SESSION['shipping']);
				}
			}
		  }
		  
		  $order = new order();
		  
		  $coo_cart_shipping_costs_control = MainFactory::create_object('CartShippingCostsControl', array(), true);
		  $t_country_array = $coo_cart_shipping_costs_control->get_selected_country();		 
		  $t_country = xtc_get_countriesList( key($t_country_array), true, true );
		  
		  $order->delivery['country']['id'] = key($t_country_array);
		  $order->delivery['country']['iso_code_2'] = $t_country['countries_iso_code_2'];
	  }

      if (defined('MODULE_SHIPPING_INSTALLED') && xtc_not_null(MODULE_SHIPPING_INSTALLED)) {
        $this->modules = explode(';', MODULE_SHIPPING_INSTALLED);

        $include_modules = array();

        if ( (xtc_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.php', $this->modules)) ) {
          $include_modules[] = array('class' => substr($module['id'], 0, strpos($module['id'], '_')), 'file' => substr($module['id'], 0, strpos($module['id'], '_')) . '.php');
        } else {
          reset($this->modules);
          while (list(, $value) = each($this->modules)) {
            $class = substr($value, 0, strrpos($value, '.'));
            $include_modules[] = array('class' => $class, 'file' => $value);
          }
        }
        // load unallowed modules into array
        $unallowed_modules = explode(',',$_SESSION['customers_status']['customers_status_shipping_unallowed'].','.$order->customer['shipping_unallowed']);
        for ($i = 0, $n = sizeof($include_modules); $i < $n; $i++) {
          if (xtc_in_array(str_replace('.php', '', $include_modules[$i]['file']), $unallowed_modules) != 'false') {
            // check if zone is alowed to see module
			$t_allowed = trim(constant(MODULE_SHIPPING_ . strtoupper(str_replace('.php', '', $include_modules[$i]['file'])) . _ALLOWED));
            if ($t_allowed != '') {
				
				if(strpos($t_allowed, ',') !== false)
				{
					$allowed_zones = explode(',', $t_allowed);
				}
				else
				{
					$allowed_zones = array($t_allowed);
				}
              
            } else {
              $allowed_zones = array();
            }
			
			if (in_array($_SESSION['delivery_zone'], $allowed_zones) == true || count($allowed_zones) == 0) {
			  $coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/shipping/' . $include_modules[$i]['file']);
              include_once(DIR_WS_MODULES . 'shipping/' . $include_modules[$i]['file']);

              $GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];
            }
          }
        }
      }
    }

	  function quote($method = '', $module = '') {
		global $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_boxes;

		$quotes_array = array();

		if (is_array($this->modules)) {
		  $shipping_quoted = '';
		  $shipping_num_boxes = 1;
		  $shipping_weight = $total_weight;

		  if (SHIPPING_BOX_WEIGHT >= $shipping_weight*SHIPPING_BOX_PADDING/100) {
			$shipping_weight = $shipping_weight+SHIPPING_BOX_WEIGHT;
		  } else {
			$shipping_weight = $shipping_weight + ($shipping_weight*SHIPPING_BOX_PADDING/100);
		  }

		  if ($shipping_weight > SHIPPING_MAX_WEIGHT) { // Split into many boxes
			$shipping_num_boxes = ceil($shipping_weight/SHIPPING_MAX_WEIGHT);
			$shipping_weight = $shipping_weight/$shipping_num_boxes;
		  }

		  $include_quotes = array();

		  reset($this->modules);
		  while (list(, $value) = each($this->modules)) {
			$class = substr($value, 0, strrpos($value, '.'));
			if (xtc_not_null($module)) {
			  if ( ($module == $class) && ($GLOBALS[$class]->enabled) ) {
				$include_quotes[] = $class;
			  }
			} elseif ($GLOBALS[$class]->enabled) {
			  $include_quotes[] = $class;
			}
		  }

		  $size = sizeof($include_quotes);
		  for ($i=0; $i<$size; $i++) {
			$quotes = $GLOBALS[$include_quotes[$i]]->quote($method);
			if (is_array($quotes)) $quotes_array[] = $quotes;
		  }
		}

		return $quotes_array;
	  }

	  function cheapest() {

		if (is_array($this->modules)) {
		  $rates = array();

		  reset($this->modules);
		  while (list(, $value) = each($this->modules)) {
			$class = substr($value, 0, strrpos($value, '.'));
			// BOF GM_MOD:
			if ($GLOBALS[$class]->enabled && $class != 'selfpickup') {
			  $quotes = $GLOBALS[$class]->quotes;
			  $size = sizeof($quotes['methods']);
			  for ($i=0; $i<$size; $i++) {
				  $title = $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')';
			  if(array_key_exists("cost",$quotes['methods'][$i])) {
				  $rates[] = array('id' => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
								   'title' => substr($title, 0, 255),
								   'cost' => $quotes['methods'][$i]['cost']);
								  // echo $quotes['methods'][$i]['cost'];

				}
			  }
			}
		  }

		  $cheapest = false;
		  $size = sizeof($rates);
		  for ($i=0; $i<$size; $i++) {
			if (is_array($cheapest)) {
			  if ($rates[$i]['cost'] < $cheapest['cost']) {
				$cheapest = $rates[$i];
			  }
			} else {
			  $cheapest = $rates[$i];
			}
		  }
		  return $cheapest;

		}

	  }
	  
	  function shopping_cart_cheapest()
		{
			$t_cheapest_shipping_module = false;
			global $PHP_SELF, $order;
			
			$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);

			if (empty($order))
			{
				if (!isset ($_SESSION['sendto'])) {
					$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
				} else {
					// verify the selected shipping address
					$check_address_query = xtc_db_query("select count(*) as total from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $_SESSION['customer_id']."' and address_book_id = '".(int) $_SESSION['sendto']."'");
					$check_address = xtc_db_fetch_array($check_address_query);

					if ($check_address['total'] != '1') {
						$_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
						if (isset ($_SESSION['shipping']))
							unset ($_SESSION['shipping']);
					}
				}

				if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight') || ($_SESSION['cart']->count_contents_non_virtual() == 0)) { // GV Code added
						$_SESSION['shipping'] = false;
						$_SESSION['sendto'] = false;

						return $t_cheapest_shipping_module;
				}

				$order = new order();
			}

			if($this->is_shipping_free() === true)
			{
				$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/order_total/ot_shipping.php');
				$coo_cart_shipping_costs_control = MainFactory::create_object('CartShippingCostsControl', array(), true);
				$_SESSION['shipping'] = array('id' => 'free' . $coo_cart_shipping_costs_control->v_module_method_separator . 'free',
												'title' => substr(FREE_SHIPPING_TITLE, 0, 255),
												'cost' => 0);
				$t_cheapest_shipping_module = array('id' => 'free' . $coo_cart_shipping_costs_control->v_module_method_separator . 'free',
															'module' => 'free',
															'method_id' => 'free',
															'method_title' => FREE_SHIPPING_TITLE,
															'cost' => 0);
				return $t_cheapest_shipping_module;
			}

			if (is_array($this->modules))
			{
				$unallowed_modules = explode(',',$_SESSION['customers_status']['customers_status_shipping_unallowed'].','.$order->customer['shipping_unallowed']);
				$t_shipping_costs_array = $this->quote();

				foreach ($t_shipping_costs_array as $t_module)
				{
					if (in_array($t_module['id'], $unallowed_modules))
					{
						continue;
					}

					if (constant(MODULE_SHIPPING_ . strtoupper($t_module['id']) . _ALLOWED) != '')
					{
						$t_allowed_zones = explode(',', constant(MODULE_SHIPPING_ . strtoupper($t_module['id']) . _ALLOWED));
					}
					else
					{
						$t_allowed_zones = array();
					}

					if (in_array($_SESSION['delivery_zone'], $t_allowed_zones) == false && count($t_allowed_zones) > 0 || isset($t_module['error']))
					{
						continue;
					}

					foreach ($t_module['methods'] as $t_method)
					{
						if ($t_cheapest_shipping_module === false 
								|| ((double)$t_method['cost'] < $t_cheapest_shipping_module['cost'] && $t_method['id'] != 'selfpickup') 
								|| $t_cheapest_shipping_module['id'] == 'selfpickup')
						{
							$t_cheapest_shipping_module = array('id' => $t_module['id'],
																'module' => $t_module['module'],
																'method_id' => $t_method['id'],
																'method_title' => substr($t_method['title'], 0, 255),
																'cost' => (double)$t_method['cost']);
						}
					}
				}
			}

			return $t_cheapest_shipping_module;
		}

		function is_shipping_free($p_selected_country = false)
		{
			if ($_SESSION['cart']->count_contents_non_virtual() == 0)
			{
				return true;
			}
			
			if($p_selected_country !== false)
			{
				$t_selected_country = $p_selected_country;
			}
			else
			{
				$coo_cart_shipping_costs_control = MainFactory::create_object('CartShippingCostsControl', array(), true);
				$t_selected_country = key($coo_cart_shipping_costs_control->get_selected_country());
			}		

			if ((MODULE_ORDER_TOTAL_SHIPPING_DESTINATION == 'national' && STORE_COUNTRY == $t_selected_country) ||
				(MODULE_ORDER_TOTAL_SHIPPING_DESTINATION == 'international' && STORE_COUNTRY != $t_selected_country) ||
				(MODULE_ORDER_TOTAL_SHIPPING_DESTINATION == 'both'))
			{
				if (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true' && $_SESSION['cart']->total >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)
				{
					return true;
				}
			}

			return false;
		}

		function module_is_allowed($p_country = false, $p_module = false)
		{
			if (empty($p_country) || empty($p_module) || $this->is_shipping_free() === true )
			{
				return false;
			}

			if (is_array($this->modules))
			{
				$t_shipping_modules_array = $this->quote();

				foreach ($t_shipping_modules_array as $t_module)
				{
					if ($t_module['id'] == $p_module)
					{
						if (constant(MODULE_SHIPPING_ . strtoupper($t_module['id']) . _ALLOWED) != '')
						{
							$t_allowed_zones = explode(',', constant(MODULE_SHIPPING_ . strtoupper($t_module['id']) . _ALLOWED));
						}
						else
						{
							$t_allowed_zones = array();
						}

						$t_country_data = xtc_get_countriesList( $p_country, true, true );

						if (!isset($t_module['error']) && (in_array($t_country_data['countries_iso_code_2'], $t_allowed_zones) == true || count($t_allowed_zones) == 0))
						{
							return true;
						}

						return false;
					}
				}
			}
			return false;
		}
	}
  }