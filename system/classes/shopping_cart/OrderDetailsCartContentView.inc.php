<?php
/* --------------------------------------------------------------
  OrderDetailsCartContentView.inc.php 2016-09-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(order_details.php,v 1.8 2003/05/03); www.oscommerce.com
  (c) 2003	 nextcommerce (order_details.php,v 1.16 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: order_details_cart.php 1281 2005-10-03 09:30:17Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once (DIR_FS_INC . 'xtc_check_stock.inc.php');
require_once (DIR_FS_INC . 'xtc_get_products_stock.inc.php');
require_once (DIR_FS_INC . 'xtc_remove_non_numeric.inc.php');
require_once (DIR_FS_INC . 'xtc_get_short_description.inc.php');
require_once (DIR_FS_INC . 'xtc_format_price.inc.php');
require_once (DIR_FS_INC . 'xtc_get_attributes_model.inc.php');

require_once(DIR_FS_INC . 'get_products_vpe_array.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');

class OrderDetailsCartContentView extends ContentView
{
	protected $langFileMaster;
	protected $propertiesControl;
	protected $propertiesView;
	protected $giftCartContentView;
	protected $cartShippingCostControl;
	protected $cartShippingCostsContentView;
	protected $gprintContentManager;
	protected $products;
	protected $productsCopy;
	protected $main;
	protected $xtcPrice;
	protected $moduleContent = array();
	protected $total = 0;
	protected $totalContent = '';
	protected $discount = 0;
	protected $productsQuantityArray = array();
	protected $coo_products;
	protected $sessionAnyOutOfStock = 0;
	protected $cart;
	protected $language;
	protected $gprintCart;
	protected $customerStatus;
	protected $shippingNumBoxes;
	protected $shippingWeight;
	protected $subtotal = 0;
	protected $buttonBackUrl;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/order_details.html');
	}

	public function setXtcPrice(xtcPrice $p_xtPrice)
	{
		$this->xtcPrice = $p_xtPrice;
	}

	public function setMain(main $p_main)
	{
		$this->main = $p_main;
	}

	public function setProducts(array $p_products)
	{
		$this->products = $p_products;
	}

	public function setLangFileMaster($p_langFileMaster)
	{
		$this->langFileMaster = $p_langFileMaster;
	}

	public function setPropertiesControl($p_propertiesControl)
	{
		$this->propertiesControl = $p_propertiesControl;
	}

	public function setPropertiesView($p_propertiesView)
	{
		$this->propertiesView = $p_propertiesView;
	}
	
	public function setGiftCartContentView($p_giftCart)
	{
		$this->giftCartContentView = $p_giftCart;
	}
	
	public function setCartShippingCostsControl($p_cartShippingCostsControl)
	{
		$this->cartShippingCostControl = $p_cartShippingCostsControl;
	}
	
	public function setCartShippingCostsContentView($p_cartShippingCostsContentView)
	{
		$this->cartShippingCostsContentView = $p_cartShippingCostsContentView;
	}
	
	public function setGprintContentManager($p_gprintContentManager)
	{
		$this->gprintContentManager = $p_gprintContentManager;
	}

	public function prepare_data()
	{
		$this->_getSessionVariablesGlobals();
		$this->_setProductsQuantityArray();
		
		$this->_checkProductsQuantity();

		$this->set_content_data('customer_status_allow_checkout', $_SESSION['customers_status']['customers_status_show_price']);
		
		$this->productsCopy = $this->products;

		for($i = 0, $n = sizeof($this->products); $i < $n; $i ++)
		{
			$t_price_num = $this->products[$i]['price'] * $this->products[$i]['quantity'];
			$t_price = $this->xtcPrice->xtcFormat($t_price_num, true);
			$t_price_single_num = $this->products[$i]['price'];
			$t_price_single = $this->xtcPrice->xtcFormat($t_price_single_num, true);
			if($_SESSION['customers_status']['customers_status_show_price'] != '1')
			{
				$t_price = '--';
				$t_price_single = '--';
				$this->set_content_data('customer_status_allow_checkout', 0);
			}

			$combisId		= $this->_getCombisId($this->products[$i]);
			$markStock		= $this->_getCheckMarkStock($this->products[$i], $combisId);
			$image			= $this->_getImageThumbnailPath($this->products[$i]);
			$productId		= $this->_getProductId($this->products[$i]);
			$productLink	= $this->_getProductLink($this->products[$i]);
			$shippingTime	= $this->_getShippingTime($this->products[$i], $combisId);
			$productsWeight	= $this->_getProductsWeight($this->products[$i], $combisId);
			$productsModel	= $this->_getProductsModel($this->products[$i], $combisId);
			$propertiesHtml	= $this->_getPropertiesHtml($combisId);

			$this->moduleContent[$i] = array(
				'PRODUCTS_NAME'					=> $this->products[$i]['name'] . $markStock,
				'PRODUCTS_QTY'					=> xtc_draw_input_field('cart_quantity[]', gm_convert_qty($this->products[$i]['quantity'], false), ' size="2" onblur="gm_qty_is_changed(' . $this->products[$i]['quantity'] . ', this.value, \'' . GM_QTY_CHANGED_MESSAGE . '\')"', 'text', true, "gm_cart_data gm_class_input") . xtc_draw_hidden_field('products_id[]', $this->products[$i]['id'], 'class="gm_cart_data"') . xtc_draw_hidden_field('old_qty[]', $this->products[$i]['quantity']),
				'PRODUCTS_OLDQTY_INPUT_NAME'	=> 'old_qty[]',
				'PRODUCTS_QTY_INPUT_NAME'		=> 'cart_quantity[]',
				'PRODUCTS_QTY_VALUE'			=> gm_convert_qty($this->products[$i]['quantity'], false),
				'PRODUCTS_ID_INPUT_NAME'		=> 'products_id[]',
				'PRODUCTS_ID_EXTENDED'			=> $this->products[$i]['id'],
				'PRODUCTS_MODEL'				=> $productsModel,
				'SHOW_PRODUCTS_MODEL'			=> SHOW_PRODUCTS_MODEL,
				'PRODUCTS_SHIPPING_TIME'		=> $shippingTime,
				'PRODUCTS_TAX'					=> (double)$this->products[$i]['tax'],
				'PRODUCTS_IMAGE'				=> $image,
				'IMAGE_ALT'						=> $this->products[$i]['name'],
				'BOX_DELETE'					=> xtc_draw_checkbox_field('cart_delete[]', $this->products[$i]['id'], false, 'id="gm_delete_product_' . $productId . '"'),
				'PRODUCTS_LINK'					=> $productLink,
				'PRODUCTS_PRICE'				=> $t_price,
				'PRODUCTS_SINGLE_PRICE'			=> $t_price_single,
				'PRODUCTS_SHORT_DESCRIPTION'	=> xtc_get_short_description($this->products[$i]['id']),
				'ATTRIBUTES'					=> [],
				'PROPERTIES'					=> $propertiesHtml,
				'GM_WEIGHT'						=> $productsWeight,
				'PRODUCTS_ID'					=> $productId,
				'UNIT'							=> $this->products[$i]['unit_name'],
				'PRODUCTS_VPE_ARRAY'			=> ''
			);
			$this->moduleContent[$i]['PRODUCTS_VPE_ARRAY'] = $this->_getProductAttributes($this->products[$i], $combisId, $markStock, $this->moduleContent[$i]);
		}
		
		$this->total = $this->cart->show_total();
		$this->subtotal = $this->total;

		$this->_setCustomerDiscount();

		$this->_setTaxText();
		$this->_setTaxData();

		$this->_setContentDataSubTotal();
		$this->_setContentDataTotal();
		$this->_setContentDataLanguage();
		$this->_setContentDataModuleContent();
		
		$this->_setShippingInfo();
		$this->_setShippingWeightInfo();
		$this->_setContentDataGiftCartContentView();
		
		$this->_setSessionVariables();
		
		$this->_setButtonBackUrl();
		$this->_setContentDataButtonsBackUrl();
	}


	/*
	 * Method for collect all session-variables an globals
	 */
	
	protected function _getSessionVariablesGlobals()
	{
		$this->cart = $_SESSION['cart'];
		$this->language = $_SESSION['language'];
		$this->gprintCart = $_SESSION['coo_gprint_cart'];
		$this->customerStatus = $_SESSION['customers_status'];
		$this->shippingNumBoxes = $GLOBALS['shipping_num_boxes'];
		$this->shippingWeight = $GLOBALS['shipping_weight'];
	}

	
	/*
	 * Method for setting all session-variables
	 */
	
	protected function _setSessionVariables()
	{
		$_SESSION['any_out_of_stock'] = $this->sessionAnyOutOfStock;
	}


	/*
	 * Methods for setting global values
	 */

	protected function _setProductsQuantityArray()
	{
		for($i = 0, $n = sizeof($this->products); $i < $n; $i ++)
		{
			$extractedProductsId = xtc_get_prid($this->products[$i]['id']);
			$cooProducts = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $extractedProductsId)));
			$usePropertiesCombisQuantity = $cooProducts->get_data_value('use_properties_combis_quantity');
			if($usePropertiesCombisQuantity == 1 || ($usePropertiesCombisQuantity == 0 && ATTRIBUTE_STOCK_CHECK == 'false' && STOCK_CHECK == 'true'))
			{
				$this->productsQuantityArray[$extractedProductsId] += $this->products[$i]['quantity'];
			}
		}
	}
	
	protected function _setDiscount($p_price)
	{
		$this->discount = round($this->xtcPrice->xtcGetDC($p_price, $this->customerStatus['customers_status_ot_discount']), 2);
		$priceSpecial = 1; 
		$calculateCurrencies = false;
		$this->totalContent = $this->customerStatus['customers_status_ot_discount'] . ' % ' . SUB_TITLE_OT_DISCOUNT . ' -' . xtc_format_price($this->discount, $priceSpecial, $calculateCurrencies) . '<br />';
		$this->_setContentDataDiscountValue();
		$this->_setContentDataDiscountText();
		if($this->customerStatus['customers_status_show_price'] == '1')
		{
			if($this->customerStatus['customers_status_show_price_tax'] == 0 && $this->customerStatus['customers_status_add_tax_ot'] == 0)
			{
				$this->total -= $this->discount;
			}
			if($this->customerStatus['customers_status_show_price_tax'] == 0 && $this->customerStatus['customers_status_add_tax_ot'] == 1)
			{
				$this->total -= $this->discount;
			}
			if($this->customerStatus['customers_status_show_price_tax'] == 1)
			{
				$this->total -= $this->discount;
			}
			$this->totalContent .= SUB_TITLE_SUB_TOTAL . $this->xtcPrice->xtcFormat($this->total, true) . '<br />';
			$this->subtotal = $this->total + $this->discount;
		}
		else
		{
			$this->totalContent .= NOT_ALLOWED_TO_SEE_PRICES . '<br />';
		}
	}
	
	protected function _setTaxText()
	{
		if($this->customerStatus['customers_status_show_price'] == '1')
		{
			if(gm_get_conf('TAX_INFO_TAX_FREE') == 'true')
			{
				$this->_setContentDataTaxFreeText();
			}
			else
			{
				$cartTaxInfo = $this->cart->show_tax();
				if(!empty($cartTaxInfo) && $this->customerStatus['customers_status_show_price_tax'] == '0' && $this->customerStatus['customers_status_add_tax_ot'] == '1')
				{
					if(!defined(MODULE_ORDER_TOTAL_SUBTOTAL_TITLE_NO_TAX))
					{
						$this->langFileMaster->init_from_lang_file('lang/' . $this->language . '/modules/order_total/ot_subtotal.php');
					}

					$tax = 0;
					foreach($this->cart->tax as $keyTax => $valueTax)
					{
						$tax += $valueTax['value'];
					}

					$this->subtotal = (double)$this->total - (double)$tax + $this->discount;
				}
			}
		}
	}
	
	protected function _setShippingWeightInfo()
	{
		if(SHOW_CART_SHIPPING_WEIGHT == 'true' && SHOW_CART_SHIPPING_COSTS == 'false')
		{
			if(isset($this->shippingNumBoxes) === false && isset($this->shippingWeight) === false)
			{
				$this->cartShippingCostControl->get_shipping_modules();
				$this->shippingNumBoxes = $GLOBALS['shipping_num_boxes'];
				$this->shippingWeight = $GLOBALS['shipping_weight'];
			}
			$this->_setContentDataShowShippingWeight(1);
			$this->_setContentDataShippingWeight(gm_prepare_number($this->shippingNumBoxes * $this->shippingWeight, $this->xtcPrice->currencies[$this->xtcPrice->actualCurr]['decimal_point']));
			
			$showShippingWeightInfo = 0;
			if((double)SHIPPING_BOX_WEIGHT > 0 || (double)SHIPPING_BOX_PADDING > 0)
			{
				$showShippingWeightInfo = 1;
			}
			$this->_setContentDataShowShippingWeightInfo($showShippingWeightInfo);
		}
		else
		{
			$this->_setContentDataShowShippingWeight(0);
		}
	}
	
	protected function _setTaxData()
	{
		$taxesDataArray = explode('<br />', $this->cart->show_tax(true));
		$taxArray = array();
		for($i = 0; $i < count($taxesDataArray); $i++)
		{
			if(!empty($taxesDataArray[$i]))
			{
				$taxDataArray = explode(':', $taxesDataArray[$i]);
				$taxArray[] = array(
					'TEXT'	=> $taxDataArray[0],
					'VALUE'	=> $taxDataArray[1]
				);
			}
		}
		$this->_setContentDataTaxData($taxArray);
	}
	
	protected function _setShippingInfo()
	{
		if(SHOW_CART_SHIPPING_COSTS == 'true')
		{
			$this->_setContentDataShippingInfoExcl(SHIPPING_EXCL);
			$this->_setContentDataShippingInfoShippingLink($this->main->gm_get_shipping_link(true));
			$this->_setContentDataShippingInfoShippingCosts(SHIPPING_COSTS);
			
			$this->_setContentDataShippingCostsSelection($this->cartShippingCostsContentView->get_html());
			
			$cartShippingCostsValue = $this->cartShippingCostControl->get_shipping_costs();
			if($this->cartShippingCostControl->is_shipping_free() === true)
			{
				$cartShippingCostsValue = $this->xtcPrice->xtcFormat(0, true);
			}
			$shippingInfo = ' ' . SHIPPING_EXCL . '<span class="cart_shipping_costs_value">' . $cartShippingCostsValue . '</span> <a href="' . $this->main->gm_get_shipping_link(true) . '" target="_blank" class="lightbox_iframe"> ' . SHIPPING_COSTS . '</a>';
			$shippingInfo .= $this->cartShippingCostControl->get_ot_gambioultra_info_html();
			
			$this->_setContentDataShippingInfoGabmioultra($this->cartShippingCostControl->get_ot_gambioultra_costs());
			$this->_setContentDataShippingInfoShippingCostsValue($cartShippingCostsValue);
			
			$this->_setContentDataShippingInfoDeprecated($shippingInfo);
		}
		elseif(SHOW_SHIPPING == 'true')
		{
			$this->_setContentDataShippingInfoExcl(SHIPPING_EXCL);
			$this->_setContentDataShippingInfoShippingLink($this->main->gm_get_shipping_link(true));
			$this->_setContentDataShippingInfoShippingCosts(SHIPPING_COSTS);
			
			$shippingInfo = ' ' . SHIPPING_EXCL . '<a href="' . $this->main->gm_get_shipping_link(true) . '" target="_blank" class="lightbox_iframe"> ' . SHIPPING_COSTS . '</a>';
			
			$this->_setContentDataShippingInfoDeprecated($shippingInfo);
		}
	}
	
	protected function _setCustomerDiscount()
	{
		if($this->customerStatus['customers_status_ot_discount_flag'] == '1' && $this->customerStatus['customers_status_ot_discount'] != '0.00')
		{
			$this->_setDiscount($this->_getPrice());
		}
	}
	
	protected function _deleteEmptyAttributes(&$p_moduleContent, $p_product, $p_value)
	{
		// delete empty attributes (random id)
		foreach($p_moduleContent['ATTRIBUTES'] AS $keyAttribute => $valAttribute)
		{
			if(empty($p_moduleContent['ATTRIBUTES'][$keyAttribute]['NAME']))
			{
				unset($p_moduleContent['ATTRIBUTES'][$keyAttribute]);
			}
		}

		if(isset($this->gprintCart->v_elements[$p_product['id']]) && $p_value == 0)
		{
			$gprintData = $this->gprintContentManager->get_content($p_product['id'], 'cart');
			for($j = 0; $j < count($gprintData); $j++)
			{
				$p_moduleContent['ATTRIBUTES'][] = array('ID' => 0,
														'MODEL' => '',
														'NAME' => $gprintData[$j]['NAME'],
														'VALUE_NAME' => $gprintData[$j]['VALUE']);
			}
		}
	}
	
	protected function _checkProductsQuantity()
	{
		foreach($this->productsQuantityArray as $productId => $productQuantity)
		{
			// check article quantity
			$markStock = xtc_check_stock($productId, $productQuantity);
			if($markStock)
			{
				$this->productsQuantityArray[$productId] = $markStock;
				$this->sessionAnyOutOfStock = 1;
			}
			else
			{
				unset($this->productsQuantityArray[$productId]);
			}
		}
	}


	/*
	 * helper-Methods with return-values
	 */
	
	protected function _getCheckMarkStock(array $p_product, $p_combisId)
	{
		$markStock = '';

		if($p_combisId == '')
		{
			// combis_id is empty = article without properties
			if(STOCK_CHECK == 'true')
			{
				$markStock = xtc_check_stock($p_product['id'], $p_product['quantity']);
				if($markStock)
				{
					$this->sessionAnyOutOfStock = 1;
				}
			}
		}
		else
		{
			$this->coo_products = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $p_product['id'])));
			$usePropertiesCombisQuantity = $this->coo_products->get_data_value('use_properties_combis_quantity');

			if($usePropertiesCombisQuantity == 1 || ($usePropertiesCombisQuantity == 0 && ATTRIBUTE_STOCK_CHECK == 'false' && STOCK_CHECK == 'true'))
			{
				// check article quantity
				$markStock = xtc_check_stock($p_product['id'], $p_product['quantity']);
				if($markStock)
				{
					$this->sessionAnyOutOfStock = 1;
				}
			}
			else if(($usePropertiesCombisQuantity == 0 && ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true') || $usePropertiesCombisQuantity == 2)
			{
				// check combis quantity
				$propertiesStock = $this->propertiesControl->get_properties_combis_quantity($p_combisId);
				if($propertiesStock < $p_product['quantity'])
				{
					$this->sessionAnyOutOfStock = 1;
					$markStock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
				}
			}

			$extractedProductsId = xtc_get_prid($p_product['id']);

			if(array_key_exists($extractedProductsId, $this->productsQuantityArray))
			{
				$markStock = $this->productsQuantityArray[$extractedProductsId];
			}
		}
		return $markStock;
	}
	
	protected function _getProductId($p_productId)
	{
		$productId = $p_productId['id'];
		$productId = str_replace('{', '_', $productId);
		$productId = str_replace('}', '_', $productId);
		return $productId;
	}
	
	protected function _getImageThumbnailPath(array $p_product)
	{
		$image = '';
		if(isset($p_product['image']) && $p_product['image'] != '')
		{
			$image = DIR_WS_THUMBNAIL_IMAGES . $p_product['image'];
		}
		return $image;
	}
	
	protected function _getCombisId(array $p_product)
	{
		$combisId = $this->propertiesControl->extract_combis_id($p_product['id']);
		return $combisId;
	}
	
	protected function _getShippingTime($p_product, $p_combisId)
	{
		$shippingTime = $p_product['shipping_time'];
		if($p_combisId != '')
		{
			if($this->coo_products->get_data_value('use_properties_combis_shipping_time') == 1)
			{
				$shippingTime = $this->propertiesControl->get_properties_combis_shipping_time($p_combisId);
			}
		}
		if(ACTIVATE_SHIPPING_STATUS == "false")
		{
			$shippingTime = '';
		}
		return $shippingTime;
	}
	
	protected function _getProductsModel($p_product, $p_combisId)
	{
		$productsModel = $p_product['model'];
		if($p_combisId != '')
		{
			$productsModel = $this->_getProductModelAddCombi($p_combisId, $productsModel);
		}
		return $productsModel;
	}
	
	protected function _getPropertiesHtml($p_combisId)
	{
		$propertiesHtml = '';
		if($p_combisId != '')
		{
			$propertiesHtml = $this->propertiesView->get_order_details_by_combis_id($p_combisId, 'cart');
		}
		return $propertiesHtml;
	}
	
	protected function _getProductLink($p_product)
	{
		$productLink = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($p_product['id'], $p_product['name']) . '&no_boost=1');
		// todo: bedingung: war vorher $products statt $this->products - dürfte nie gelaufen sein
		if(strpos($p_product['id'], '{') !== false)
		{
			//$productLink .= '?info=' . $p_product['id'];
		}
		return $productLink;
	}
	
	protected function _getProductsWeight($p_product, $p_combisId)
	{
		$productsWeight = $p_product['weight'];
		if($p_combisId != '')
		{
			if($this->coo_products->get_data_value('use_properties_combis_weight') == 1)
			{
				$productsWeight = $this->propertiesControl->get_properties_combis_weight($p_combisId);
			}
		}
		if(isset($p_product['attributes']))
		{
			foreach($p_product['attributes'] as $option => $value)
			{
				if($p_product[$option]['weight_prefix'] == '+')
				{
					$productsWeight += $p_product[$option]['options_values_weight'];
				}
				else if($p_product[$option]['weight_prefix'] == '-')
				{
					$productsWeight -= $p_product[$option]['options_values_weight'];
				}
			}
		}

		$productsWeight = gm_prepare_number($productsWeight, $this->xtcPrice->currencies[$this->xtcPrice->actualCurr]['decimal_point']);

		$query = xtc_db_query("SELECT gm_show_weight FROM products WHERE products_id='" . (int)$p_product['id'] . "'");
		$weightArray = xtc_db_fetch_array($query);

		if(empty($weightArray['gm_show_weight']))
		{
			$productsWeight = 0;
		}
		return $productsWeight;
	}
	
	protected function _getPrice()
	{
		$price = $this->total;
		if($this->customerStatus['customers_status_show_price_tax'] == 0 && $this->customerStatus['customers_status_add_tax_ot'] == 1)
		{
			$price = $this->total - $this->cart->show_tax(false);
		}
		return $price;
	}
	
	protected function _getProductModelAddCombi($p_combisId, $p_productsModel)
	{
		$combisId = (string)$p_combisId;
		$productsModel = (string)$p_productsModel;
		$combiModel = $this->propertiesControl->get_properties_combis_model($combisId);

		if(APPEND_PROPERTIES_MODEL == "true")
		{
			// Artikelnummer (Kombi) an Artikelnummer (Artikel) anhängen
			if($productsModel != '' && $combiModel != '')
			{
				$productsModel = $productsModel . '-' . $combiModel;
			}
			else if($combiModel != '')
			{
				$productsModel = $combiModel;
			}
		}
		else
		{
			// Artikelnummer (Artikel) durch Artikelnummer (Kombi) ersetzen
			if($combiModel != '')
			{
				$productsModel = $combiModel;
			}
		}
		return $productsModel;
	}
	
	protected function _getProductAttributes($p_product, $p_combisId, $p_markStock, &$p_moduleContent)
	{
		$attributesExist = ((isset($p_product['attributes'])) ? true : false);

		if($attributesExist === true)
		{
			foreach($p_product['attributes'] as $option => $value)
			{
				if(ATTRIBUTE_STOCK_CHECK == 'true' && STOCK_CHECK == 'true' && $value != 0)
				{
					$attributeStockCheck = xtc_check_stock_attributes($p_product[$option]['products_attributes_id'], $p_product['quantity']);
					if($attributeStockCheck)
					{
						$this->sessionAnyOutOfStock = 1;
					}
				}
				// combine all customizer products for checking stock
				elseif(STOCK_CHECK == 'true' && $value == 0 && $p_markStock == '')
				{
					preg_match('/(.*)\{[\d]+\}0$/', $p_product['id'], $matchesArray);

					if(isset($matchesArray[1]))
					{
						$productIdentifier = $matchesArray[1];
					}

					$quantities = 0;

					foreach($this->productsCopy as $productDataArray)
					{
						preg_match('/(.*)\{[\d]+\}0$/', $productDataArray['id'], $matchesArray);

						if(isset($matchesArray[1]) && $matchesArray[1] == $productIdentifier)
						{
							$quantities += $productDataArray['quantity'];
						}
					}

					$markStock = xtc_check_stock($p_product['id'], $quantities);

					if($markStock !== '')
					{
						$this->sessionAnyOutOfStock = 1;
						$p_moduleContent['PRODUCTS_NAME'] .= $markStock;
					}
				}

				$p_moduleContent['ATTRIBUTES'][] = array(
					'ID'			=> $p_product[$option]['products_attributes_id'],
					'MODEL'			=> xtc_get_attributes_model(xtc_get_prid($p_product['id']), $p_product[$option]['products_options_values_name'], $p_product[$option]['products_options_name']),
					'NAME'			=> $p_product[$option]['products_options_name'],
					'VALUE_NAME'	=> $p_product[$option]['products_options_values_name'] . $attributeStockCheck
				);

				// BOF GM_MOD GX-Customizer:
				$this->_deleteEmptyAttributes($p_moduleContent, $p_product, $value);
			}
		}
		return get_products_vpe_array($p_product['id'], $p_product['price'], array(), $p_combisId);
	}
	
	protected function _setButtonBackUrl()
	{
		$this->buttonBackUrl = xtc_href_link(FILENAME_DEFAULT);
		if(!empty($_SESSION['gm_history'][count($_SESSION['gm_history'])-1]['CLOSE']))
		{
			$this->buttonBackUrl = GM_HTTP_SERVER . $_SESSION['gm_history'][count($_SESSION['gm_history'])-1]['CLOSE'];
		}
	}

	
	/*
	 * Methods for setting smarty-variables
	 */
	
	protected function _setContentDataTaxData(array $p_taxArray)
	{
		$this->set_content_data('tax_data', $p_taxArray);
	}
	
	protected function _setContentDataTaxFreeText()
	{
		$this->set_content_data('TAX_FREE_TEXT', GM_TAX_FREE);
	}
	
	protected function _setContentDataShowShippingWeight($p_show)
	{
		$this->set_content_data('SHOW_SHIPPING_WEIGHT', $p_show);
	}
	
	protected function _setContentDataShowShippingWeightInfo($p_showShippingWeightInfo)
	{
		$this->set_content_data('SHOW_SHIPPING_WEIGHT_INFO', $p_showShippingWeightInfo);
	}
	
	protected function _setContentDataShippingWeight($p_shippingWeight)
	{
		$this->set_content_data('SHIPPING_WEIGHT', $p_shippingWeight);
	}
	
	protected function _setContentDataShippingCostsSelection($p_html)
	{
		$this->set_content_data('cart_shipping_costs_selection', $p_html);
	}
	
	protected function _setContentDataShippingInfoDeprecated($p_shippingInfo)
	{
		$this->set_content_data('SHIPPING_INFO', $p_shippingInfo, 2);
	}
	
	protected function _setContentDataShippingInfoGabmioultra($p_shippingGambioultra)
	{
		$this->set_content_data('SHIPPING_INFO_GAMBIOULTRA', $p_shippingGambioultra);
	}
	
	protected function _setContentDataShippingInfoShippingCostsValue($p_shippingCostsValue)
	{
		$this->set_content_data('SHIPPING_INFO_SHIPPING_COSTS_VALUE', $p_shippingCostsValue);
	}
	
	protected function _setContentDataShippingInfoExcl($p_shippingExcl)
	{
		$this->set_content_data('SHIPPING_INFO_EXCL', $p_shippingExcl);
	}
	
	protected function _setContentDataShippingInfoShippingLink($p_shippingLink)
	{
		$this->set_content_data('SHIPPING_INFO_SHIPPING_LINK', $p_shippingLink);
	}
	
	protected function _setContentDataShippingInfoShippingCosts($p_shippingCosts)
	{
		$this->set_content_data('SHIPPING_INFO_SHIPPING_COSTS', $p_shippingCosts);
	}
	
	protected function _setContentDataDiscountText()
	{
		$this->set_content_data('DISCOUNT_TEXT', round((double)$this->customerStatus['customers_status_ot_discount'], 2) . '% ' . SUB_TITLE_OT_DISCOUNT);
	}
	
	protected function _setContentDataSubTotal()
	{
		$this->set_content_data('SUBTOTAL', $this->xtcPrice->xtcFormat($this->subtotal, true));
	}
	
	protected function _setContentDataTotal()
	{
		$this->set_content_data('TOTAL', $this->xtcPrice->xtcFormat($this->total, true));
	}
	
	protected function _setContentDataLanguage()
	{
		$this->set_content_data('language', $this->language);
	}
	
	protected function _setContentDataModuleContent()
	{
		$this->set_content_data('module_content', $this->moduleContent);
	}
	
	protected function _setContentDataDiscountValue()
	{
		$priceSpecial = 1;
		$calculateCurrencies = false;
		$this->set_content_data('DISCOUNT_VALUE', '-' . xtc_format_price($this->discount, $priceSpecial, $calculateCurrencies));
	}
	
	protected function _setContentDataGiftCartContentView()
	{
		$viewHtml = $this->giftCartContentView->get_html();
		$this->set_content_data('MODULE_gift_cart', $viewHtml);
	}
	
	protected function _setContentDataButtonsBackUrl()
	{
		$this->set_content_data('BUTTON_BACK_URL', $this->buttonBackUrl);
	}
}