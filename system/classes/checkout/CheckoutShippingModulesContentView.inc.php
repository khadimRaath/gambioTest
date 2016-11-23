<?php
/* --------------------------------------------------------------
  CheckoutShippingModulesContentView.inc.php 2014-10-28 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_shipping.php,v 1.15 2003/04/08); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_shipping.php,v 1.20 2003/08/20); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_shipping.php 1037 2005-07-17 15:25:32Z gwinger $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once (DIR_FS_INC.'xtc_count_shipping_modules.inc.php');

class CheckoutShippingModulesContentView extends ContentView
{
	protected $coo_xtc_price;
	protected $free_shipping;
	protected $shipping_free_over;
	protected $quotes_array; // shipping modules array
	protected $selected_shipping_method;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/checkout_shipping_block.html');
		$this->set_flat_assigns(true);
	}


	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('free_shipping',
																		  'shipping_free_over',
																		  'quotes_array',
																		  'coo_xtc_price'));
		if(empty($t_uninitialized_array))
		{
			if(xtc_count_shipping_modules() > 0)
			{
				$this->_assignShippingModules();
				$this->_assignFreeShippingData();
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}


	protected function _assignShippingModules()
	{
		$shippingModulesArray = array();
		
		if($this->free_shipping)
		{
			if($this->_getSelfpickupKey() !== false)
			{
				$shippingModulesArray = $this->_buildShippingMethodsArrayForFreeShipping($this->_getSelfpickupKey());
			}
		}
		else
		{
			$shippingModulesArray = $this->_buildShippingModulesArray();
		}

		// use setter to allow overloading (compatibility to GX2.1 overloads)
		$this->set_quotes_array($shippingModulesArray);
		
		$this->set_content_data('module_content', $this->quotes_array);
	}


	/**
	 * @return int|bool
	 */
	protected function _getSelfpickupKey()
	{
		foreach($this->quotes_array AS $key => $value)
		{
			if(strpos($this->quotes_array[$key]['id'], 'selfpickup') === 0)
			{
				return $key;
			}
		}
		
		return false;
	}


	/**
	 * @param int $p_selfpickupKey
	 *
	 * @return array
	 */
	protected function _buildShippingMethodsArrayForFreeShipping($p_selfpickupKey)
	{
		$shippingModulesArray = array();
		
		// add free shipping data
		$shippingModulesArray[] = array('id' => 'free_free',
							   'module' => FREE_SHIPPING_TITLE,
							   'methods' => array(array('id' => 'free_free',
														'title' => sprintf(FREE_SHIPPING_DESCRIPTION, $this->coo_xtc_price->xtcFormat($this->shipping_free_over, true, 0, true)),
														'cost' => 0,
														'radio_buttons' => 0,
														'checked' => 1,
														'price' => $this->coo_xtc_price->xtcFormat(0, true),
														'radio_field' => xtc_draw_radio_field('shipping', 'free_free', true))
							   ));
		
		// copy selfpickup data into shipping methods array
		$shippingModulesArray[1] = $this->quotes_array[$p_selfpickupKey];
		
		// uncheck selfpickup selection
		unset($shippingModulesArray[1]['methods'][0]['checked']);
		
		// add missing selfpickup data
		$shippingModulesArray[1]['id'] = 'selfpickup_selfpickup';
		$shippingModulesArray[1]['methods'][0]['id'] = 'selfpickup_selfpickup';
		$shippingModulesArray[1]['methods'][0]['radio_buttons'] = 1;
		$shippingModulesArray[1]['methods'][0]['price'] = $this->coo_xtc_price->xtcFormat(0, true);
		$shippingModulesArray[1]['methods'][0]['radio_field'] = xtc_draw_radio_field('shipping', 'selfpickup_selfpickup', false);
		
		return $shippingModulesArray;
	}


	/**
	 * @return array
	 */
	protected function _buildShippingModulesArray()
	{
		$shippingModulesArray = $this->quotes_array;
		
		$radioButtons = 0;
		$modulesCount = count($shippingModulesArray);

		// loop through shipping modules to add missing data
		foreach($shippingModulesArray as $key => $methodDataArray)
		{
			if(!isset($shippingModulesArray[$key]['error']))
			{
				for($j = 0, $methodsCount = sizeof($shippingModulesArray[$key]['methods']); $j < $methodsCount; $j++)
				{
					// set the radio button to be checked if it is the method chosen
					$shippingModulesArray[$key]['methods'][$j]['radioButtons'] = $radioButtons;
					$checked = (($shippingModulesArray[$key]['id'] . '_' . $shippingModulesArray[$key]['methods'][$j]['id'] == $this->selected_shipping_method) ? true : false);

					if(($checked == true) || ($modulesCount == 1 && $methodsCount == 1))
					{
						$shippingModulesArray[$key]['methods'][$j]['checked'] = 1;
					}

					if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
					{
						$shippingModulesArray[$key]['tax'] = 0;
					}

					if(($modulesCount > 1) || ($methodsCount > 1))
					{
						$shippingModulesArray[$key]['methods'][$j]['price'] = $this->coo_xtc_price->xtcFormat(xtc_add_tax($shippingModulesArray[$key]['methods'][$j]['cost'], $shippingModulesArray[$key]['tax']), true, 0, true);
						$shippingModulesArray[$key]['methods'][$j]['radio_field'] = xtc_draw_radio_field('shipping', $shippingModulesArray[$key]['id'] . '_' . $shippingModulesArray[$key]['methods'][$j]['id'], $checked);
					}
					else
					{
						$shippingModulesArray[$key]['methods'][$j]['price'] = $this->coo_xtc_price->xtcFormat(xtc_add_tax($shippingModulesArray[$key]['methods'][$j]['cost'], $shippingModulesArray[$key]['tax']), true, 0, true)
																  . xtc_draw_hidden_field('shipping', $shippingModulesArray[$key]['id'] . '_' . $shippingModulesArray[$key]['methods'][$j]['id']);
					}

					$radioButtons++;
				}
			}
		}
		
		return $shippingModulesArray;
	}


	protected function _assignFreeShippingData()
	{
		$this->set_content_data('FREE_SHIPPING', $this->free_shipping);

		if($this->free_shipping)
		{
			$this->set_content_data('FREE_SHIPPING_TITLE', FREE_SHIPPING_TITLE);
			$this->set_content_data('FREE_SHIPPING_DESCRIPTION', sprintf(FREE_SHIPPING_DESCRIPTION,
																		 $this->coo_xtc_price->xtcFormat($this->shipping_free_over,
																								   true, 0, true)) .
																 xtc_draw_hidden_field('shipping', 'free_free'));
			$this->set_content_data('FREE_SHIPPING_ICON', '');
		}
	}


	/**
	 * @param xtcPrice $xtcPrice
	 */
	public function set_coo_xtc_price(xtcPrice $xtcPrice)
	{
		$this->coo_xtc_price = $xtcPrice;
	}


	/**
	 * @return xtcPrice
	 */
	public function get_coo_xtc_price()
	{
		return $this->coo_xtc_price;
	}


	/**
	 * @param bool $p_isFreeShipping
	 */
	public function set_free_shipping($p_isFreeShipping)
	{
		$this->free_shipping = $p_isFreeShipping;
	}


	/**
	 * @return bool
	 */
	public function get_free_shipping()
	{
		return $this->free_shipping;
	}


	/**
	 * @param array $shippingModulesArray
	 */
	public function set_quotes_array(array $shippingModulesArray)
	{
		$this->quotes_array = $shippingModulesArray;
	}


	/**
	 * @return array
	 */
	public function get_quotes_array()
	{
		return $this->quotes_array;
	}


	/**
	 * @param string $p_shippingMethod
	 */
	public function set_selected_shipping_method($p_shippingMethod)
	{
		$this->selected_shipping_method = (string)$p_shippingMethod;
	}


	/**
	 * @return string
	 */
	public function get_selected_shipping_method()
	{
		return $this->selected_shipping_method;
	}


	/**
	 * @param double $p_shippingFreePriceLimit
	 */
	public function set_shipping_free_over($p_shippingFreePriceLimit)
	{
		$this->shipping_free_over = (double)$p_shippingFreePriceLimit;
	}


	/**
	 * @return double
	 */
	public function get_shipping_free_over()
	{
		return $this->shipping_free_over;
	}
}