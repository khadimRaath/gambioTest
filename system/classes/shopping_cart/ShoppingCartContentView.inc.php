<?php
/* --------------------------------------------------------------
   ShoppingCartContentView.inc.php 2015-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(best_sellers.php,v 1.20 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (best_sellers.php,v 1.10 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: best_sellers.php 1292 2005-10-07 16:10:55Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3        	Autor: Mikel Williams | mikel@ladykatcostumes.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once (DIR_FS_INC.'xtc_array_to_string.inc.php');
require_once (DIR_FS_INC.'xtc_image_submit.inc.php');
require_once (DIR_FS_INC.'xtc_recalculate_price.inc.php');


class ShoppingCartContentView extends ContentView
{
	protected $paypal;
	protected $giftCartHtml;
	protected $sharedCartHtml;
	protected $klarna;
	protected $xtcPrice;
	protected $buttonBackUrl;
	protected $hiddenFormOptions = '';
	protected $seoBoost;
	protected $languagesId = '';
	protected $sessionCart;
	protected $cartCountContents;
	protected $customerStatusMinOrder;
	protected $customerStatusMaxOrder;
	protected $languageCode;
	protected $products;
	protected $orderDetailsCartContentView;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/shopping_cart.html');
		$this->set_flat_assigns(true);
	}
	
	public function setXtcPrice(xtcPrice $p_xtPrice)
	{
		$this->xtcPrice = $p_xtPrice;
	}
	public function setCustomerStatusMaxOrder($p_customerStatusMaxOrder)
	{
		$this->customerStatusMaxOrder = $p_customerStatusMaxOrder;
	}
	public function setCustomerStatusMinOrder($p_customerStatusMinOrder)
	{
		$this->customerStatusMinOrder = $p_customerStatusMinOrder;
	}
	public function setCartCountContents($p_countContents)
	{
		$this->cartCountContents = $p_countContents;
	}
	public function setCart($p_cart)
	{
		$this->sessionCart = $p_cart;
	}
	public function setLanguagesId($p_languageId)
	{
		$this->languagesId = $p_languageId;
	}
	public function setLanguageCode($p_languageCode)
	{
		$this->languageCode = $p_languageCode;
	}
	
	public function setProducts(array $products)
	{
		$this->products = $products;
	}
	
	public function getOrderDetailsCartContentView()
	{
		return $this->orderDetailsCartContentView;
	}

	public function prepare_data()
	{
		$this->_setSeoBoost();
		$this->_setKlarna();
		$this->_setPaypalButton();
		$this->_setAmazonadvpayButton();
		$this->_setGiftCart();
		$this->products = array();
		
		// Share cart feature is only available with Honeygrid
		if(gm_get_env_info('TEMPLATE_VERSION') >= 3)
		{
			$this->_setShareCart();
		}
		
		$products = array();
		$this->_setOrderDetailsCartContentView();
		$this->_setShippingCostsSelection();

		if ($this->cartCountContents > 0) {
			$this->_setContentDataForm();
			$this->_setSessionValueAnyOutOfStock(0);
			$this->products = $this->sessionCart->get_products();
			$this->_setProductsGmWeight();
			$this->_setContentDataHiddenOptions($this->hiddenFormOptions);
			
			// check if cart contents still exist
			if($this->cartCountContents > 0)
			{
				$this->_setOrderDetailsCartContentView();
				$this->_setInfoMessage();
				$this->_setMinMaxOrderValue();
				$this->_setButtonBackUrl();
				$this->_setContentDataButtonsBackUrl();
				$this->_setShippingAndPaymentInfoLink();
				$this->_setYoochoose();
				$this->_setCustomerStatusAllowCheckout();
			}
		}

		if($this->cartCountContents <= 0)
		{
			$this->_resetInfoMessage();
			$this->_setButtonBackUrl();
			$this->_setContentDataCartEmpty();
			$this->_setContentDataForm();
			$this->_setContentDataButtonsBackUrl();
		}

		$this->_setContentDataLightbox();
	}

	protected function _setSeoBoost()
	{
		$this->seoBoost = MainFactory::create_object('GMSEOBoost');
	}
	
	protected function _setGiftCart()
	{
		$giftCart = MainFactory::create_object('GiftCartContentView');
		$this->giftCartHtml = $giftCart->get_html();
		$this->_setContentDataGiftCart();
	}
	protected function _setShareCart()
	{
		$shareCart           = MainFactory::create_object('ShareCartContentView');
		$this->sharedCartHtml = $shareCart->get_html();
		$this->_setContentDataSharedCart();
	}
	
	protected function _setKlarna()
	{
		$showKlarnaWidget = gm_get_conf('KLARNA_SHOW_CHECKOUT_PARTPAY') == true;
		$klarnaWidget = '';
		if($showKlarnaWidget)
		{
			$this->klarna = new GMKlarna();
			$klarnaWidget = $this->klarna->getWidgetCode($this->sessionCart->show_total());
		}		
		$this->_setContentDataKlarnaWidget($klarnaWidget);
	}
	protected function _getProductAttributes(&$p_product)
	{
		$isValidProduct = true;
		$hiddenOptions = '';
		if(isset ($p_product['attributes'])) 
		{
			foreach($p_product['attributes'] as $option => $value)
			{
				$hiddenOptions .= xtc_draw_hidden_field('id[' . $p_product['id'] . '][' . $option . ']', $value);

				if((int)$value > 0)
				{
					$sql = "
					SELECT 
						popt.products_options_name, 
						poval.products_options_values_name, 
						pa.options_values_price, 
						pa.price_prefix,
						pa.attributes_stock,
						pa.products_attributes_id,
						pa.attributes_model,
						pa.weight_prefix,
						pa.options_values_weight
					FROM 
						" . TABLE_PRODUCTS_OPTIONS . " popt, 
						" . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, 
						" . TABLE_PRODUCTS_ATTRIBUTES . " pa
					WHERE 
						pa.products_id = '" . (int)$p_product['id'] . "' AND 
						pa.options_id = '" . (int)$option . "' AND 
						pa.options_id = popt.products_options_id AND 
						pa.options_values_id = '" . (int)$value . "' AND 
						pa.options_values_id = poval.products_options_values_id AND 
						popt.language_id = '" . $this->languagesId . "' AND 
						poval.language_id = '" . $this->languagesId . "'";

					$attributes       = xtc_db_query($sql);
					$attributesValues = xtc_db_fetch_array($attributes);

					if(empty($attributesValues))
					{
						$isValidProduct = false;
					}
					else
					{
						$p_product[$option]['products_options_name']        = $attributesValues['products_options_name'];
						$p_product[$option]['options_values_id']            = (int)$value;
						$p_product[$option]['products_options_values_name'] = $attributesValues['products_options_values_name'];
						$p_product[$option]['options_values_price']         = $attributesValues['options_values_price'];
						$p_product[$option]['price_prefix']                 = $attributesValues['price_prefix'];
						$p_product[$option]['weight_prefix']                = $attributesValues['weight_prefix'];
						$p_product[$option]['options_values_weight']        = $attributesValues['options_values_weight'];
						$p_product[$option]['attributes_stock']             = $attributesValues['attributes_stock'];
						$p_product[$option]['products_attributes_id']       = $attributesValues['products_attributes_id'];
						$p_product[$option]['products_attributes_model']    = $attributesValues['attributes_model'];
						
						/* bof gm weight*/
						if($attributesValues['weight_prefix'] == '-')
						{
							$p_product['gm_weight'] -= $attributesValues['options_values_weight'];
						}
						else
						{
							$p_product['gm_weight'] += $p_product[$option]['options_values_weight'];
						}
						/* eof gm weight*/
					}
				}
			}
		}

		if(!$isValidProduct)
		{
			$query = "DELETE FROM customers_basket WHERE products_id = '" . xtc_db_input($p_product['id']) . "'";
			xtc_db_query($query);

			$query = "DELETE FROM customers_basket_attributes WHERE products_id = '" . xtc_db_input($p_product['id']) . "'";
			xtc_db_query($query);
			
			unset($_SESSION['cart']->contents[$p_product['id']]);
			$p_product = null;
			$hiddenOptions = '';
			
			$this->cartCountContents = $_SESSION['cart']->count_contents();
		}
		
		$this->hiddenFormOptions .= $hiddenOptions;
	}
	protected function _setInfoMessage()
	{
		$this->_setSessionValueAllowCheckout('true');
		if (STOCK_CHECK == 'true') {
			if ($_SESSION['any_out_of_stock'] == 1) {
				$infoMessage = '';
				if (STOCK_ALLOW_CHECKOUT == 'true' || ($_SESSION['cart']->content_type === 'virtual' && DOWNLOAD_STOCK_CHECK == 'false')) {
					// write permission in session
					$this->_setSessionValueAllowCheckout('true');
					$infoMessage = sprintf(OUT_OF_STOCK_CAN_CHECKOUT, STOCK_MARK_PRODUCT_OUT_OF_STOCK);
				} else {
					$this->_setSessionValueAllowCheckout('false');
					$infoMessage = sprintf(OUT_OF_STOCK_CANT_CHECKOUT, STOCK_MARK_PRODUCT_OUT_OF_STOCK);
				}
				$this->_setContentDataInfoMessage($infoMessage);
			} else {
				$this->_setSessionValueAllowCheckout('true');
			}
		}
		$this->_resetInfoMessage();
	}
	protected function _resetInfoMessage()
	{
		if(isset($_SESSION['info_message']))
		{
			$this->_setContentDataInfoMessage($_SESSION['info_message']);
			$this->_unsetSessionValue('info_message');
		}
	}
	protected function _setMinMaxOrderValue()
	{
		if ((float) $this->sessionCart->show_total() < $this->customerStatusMinOrder) {
			$this->_setSessionValueAllowCheckout('false');
			$this->_setContentDataInfoMessage1(MINIMUM_ORDER_VALUE_NOT_REACHED_1);
			$this->_setContentDataInfoMessage2(MINIMUM_ORDER_VALUE_NOT_REACHED_2);

			$moreToBuy = $this->customerStatusMinOrder - $this->sessionCart->show_total();
			$orderAmount = $this->xtcPrice->xtcFormat($moreToBuy, true);
			$this->_setContentDataOrderAmount($orderAmount);

			$minOrder = $this->xtcPrice->xtcFormat($this->customerStatusMinOrder, true);
			$this->_setContentDataMinMaxOrder($minOrder);
		}
		if ($this->customerStatusMaxOrder != 0) {
			if ((float)$this->sessionCart->show_total() > $this->customerStatusMaxOrder) {
				$this->_setSessionValueAllowCheckout('false');
				$this->_setContentDataInfoMessage1(MAXIMUM_ORDER_VALUE_REACHED_1);
				$this->_setContentDataInfoMessage2(MAXIMUM_ORDER_VALUE_REACHED_2);

				$lessToBuy = $this->sessionCart->show_total() - $this->customerStatusMaxOrder;
				$orderAmount = $this->xtcPrice->xtcFormat($lessToBuy, true);
				$this->_setContentDataOrderAmount($orderAmount);

				$maxOrder = $this->xtcPrice->xtcFormat($this->customerStatusMaxOrder, true);
				$this->_setContentDataMinMaxOrder($maxOrder);
			}
		}
	}
	protected function _setButtonBackUrl()
	{
		$this->buttonBackUrl = xtc_href_link(FILENAME_DEFAULT);
		if(!empty($_SESSION['gm_history'][count($_SESSION['gm_history'])-1]['CLOSE']))
		{
			$this->buttonBackUrl = HTTP_SERVER . $_SESSION['gm_history'][count($_SESSION['gm_history'])-1]['CLOSE'];
		}
	}
	protected function _setShippingAndPaymentInfoLink()
	{
		$sql = '
			SELECT
				content_title
			FROM
				content_manager
			WHERE
				content_group = ' . (int)SHIPPING_INFOS . ' AND 
				languages_id = ' . $this->languagesId;
		
		$result = xtc_db_query($sql);
		$contentTitle = '';
		if($contentTitleArray = xtc_db_fetch_array($result))
		{
			$contentTitle = $contentTitleArray['content_title'];

			$sefParameter = '';
			if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			{
				$sefParameter .= '&content=' . xtc_cleanName($contentTitle);
			}
			
			$contentUrl = xtc_href_link(FILENAME_POPUP_CONTENT, 'coID=' . (int)SHIPPING_INFOS . '&lightbox_mode=1' . $sefParameter, 'SSL', false, true, true);
			$this->_setContentDataShippingAndPaymentInfoLink($contentUrl);
			
			if($this->seoBoost->boost_content)
			{
				$contentUrl = xtc_href_link($this->seoBoost->get_boosted_content_url($this->seoBoost->get_content_id_by_content_group((int)SHIPPING_INFOS), $this->languagesId));
			}
			else
			{
				$contentUrl = xtc_href_link(FILENAME_CONTENT, 'coID=' . (int)SHIPPING_INFOS . $sefParameter);
			}
			$this->_setContentDataShippingAndPaymentInfoLinkMobile($contentUrl);
		}
		$this->_setContentDataShippingAndPaymentContentTitle($contentTitle);
	}
	protected function _setYoochoose()
	{
		$alsoInterestingHtml = '';
		if(defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)
		{
			$yoochooseAlsoInteresting = MainFactory::create_object('YoochooseShoppingCartContentView');
			$alsoInterestingHtml = $yoochooseAlsoInteresting->get_html();
		}
		$this->_setContentDataYoochooseShoppingCart($alsoInterestingHtml);
	}
	protected function _setProductsGmWeight()
	{
		$productsCount = count($this->products);
		for ($i = 0, $n = $productsCount; $i < $n; $i ++) {
			$this->products[$i]['gm_weight'] = $this->products[$i]['weight'];
			// Push all attributes information in an array
			$this->_getProductAttributes($this->products[$i]);
			
			if($this->products[$i] === null)
			{
				unset($this->products[$i]);
			}
		}
	}
	protected function _setOrderDetailsCartContentView()
	{
		$this->orderDetailsCartContentView = MainFactory::create_object('OrderDetailsCartContentView');

		$giftCart = MainFactory::create_object('GiftCartContentView');
		$this->orderDetailsCartContentView->setGiftCartContentView($giftCart);

		$cartShippingCostsControl = MainFactory::create_object('CartShippingCostsControl', array(), true);
		$this->orderDetailsCartContentView->setCartShippingCostsControl($cartShippingCostsControl);

		$cartShippingCostsContentView = MainFactory::create_object('CartShippingCostsContentView');
		$this->orderDetailsCartContentView->setCartShippingCostsContentView($cartShippingCostsContentView);

		$gprintContentManager = MainFactory::create_object('GMGPrintContentManager');
		$this->orderDetailsCartContentView->setGprintContentManager($gprintContentManager);

		//$t_view_html = $giftCart->get_html();

		$langFileMaster = MainFactory::create_object('LanguageTextManager', array(), true);
		$this->orderDetailsCartContentView->setLangFileMaster($langFileMaster);

		$propertiesControl = MainFactory::create_object('PropertiesControl');
		$this->orderDetailsCartContentView->setPropertiesControl($propertiesControl);

		$propertiesView = MainFactory::create_object('PropertiesView');
		$this->orderDetailsCartContentView->setPropertiesView($propertiesView);
		$this->orderDetailsCartContentView->setProducts($this->products);

		$main = new main();
		$this->orderDetailsCartContentView->setMain($main);
		
		$this->orderDetailsCartContentView->setXtcPrice($this->xtcPrice);
		
		$orderDetailsHtml = $this->orderDetailsCartContentView->get_html();
		$this->_setContentDataOrderDetails($orderDetailsHtml);
	}


	/**
	 * PayPal-Buttons of deprecated module "paypalng". Deprecated since GX 2.4
	 */
	protected function _setPaypalButton()
	{
		if(class_exists('GMPayPal'))
		{
			$this->paypal = new GMPayPal();
			$ppConfig = $this->paypal->getConfigArray();
			$this->paypal->doQuickButtonRedirect();
			$usePaypalCheckout = $this->paypal->isExpressCheckoutActive();

			if($usePaypalCheckout)
			{
				$supportedLanguages = array('DE', 'EN', 'ES', 'FR', 'IT', 'NL');
				$langCode = strtoupper($this->languageCode);

				if(!in_array($langCode, $supportedLanguages))
				{
					$langCode = 'EN';
				}

				$buttonArray = array(
					'url' => GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_paypal_prepare.php',
					'img' => GM_HTTP_SERVER . DIR_WS_CATALOG . 'images/icons/paypal/' . $ppConfig['button_style'] . 'Btn_' . $langCode . '.png'
				);
				$this->_setCheckoutButton($buttonArray);
			}
		}
	}
	
	protected function _setAmazonadvpayButton()
	{
		$cartTotal = $this->sessionCart->show_total();
		$amazonAllowed = $cartTotal > 0;
		if($amazonAllowed === true)
		{
			$amazonAdvancedPayment = MainFactory::create_object('AmazonAdvancedPayment');
			if($amazonAdvancedPayment->is_enabled())
			{
				$this->_unsetSessionValue('amazonadvpay_order_ref_id');
				$this->_unsetSessionValue('billto');
				$this->_unsetSessionValue('sendto');
				$buttonArray = array(
					'script' => $amazonAdvancedPayment->get_button_element(),
				);
				$this->_setCheckoutButton($buttonArray);
			}
		}
	}
	protected function _setCheckoutButton(array $p_button)
	{
		$contentArray = $this->get_content_array();
		$cobArray = array();
		if(isset($contentArray['checkout_buttons']))
		{
			$cobArray = $contentArray['checkout_buttons'];
			$cobArray[] = $p_button;
		}
		else
		{
			$cobArray = array($p_button);
		}
		$this->_setContentDataCheckoutButtons($cobArray);
	}

	protected function _unsetSessionValue($p_value)
	{
		unset($_SESSION[$p_value]);
	}
	protected function _setSessionValueAnyOutOfStock($p_value)
	{
		$_SESSION['any_out_of_stock'] = $p_value;
	}
	protected function _setSessionValueAllowCheckout($p_allow)
	{
		$_SESSION['allow_checkout'] = $p_allow;
	}

	protected function _setContentDataCheckoutButtons(array $p_checkoutButtons)
	{
		$this->set_content_data('checkout_buttons', $p_checkoutButtons);
	}
	protected function _setContentDataLightbox()
	{
		$this->set_content_data('LIGHTBOX', gm_get_conf('GM_LIGHTBOX_CART'));
		$this->set_content_data('LIGHTBOX_CLOSE', xtc_href_link(FILENAME_DEFAULT, '', 'NONSSL'));
	}
	protected function _setContentDataKlarnaWidget($p_klarnaWidget)
	{
		$this->set_content_data('KLARNA_WIDGET', $p_klarnaWidget);
	}
	protected function _setContentDataCartEmpty()
	{
		// empty cart
		$this->set_content_data('cart_empty', true);
	}
	protected function _setContentDataButtonsBackUrl()
	{
		$this->_setContentsDataDeprecated();
		$this->set_content_data('BUTTON_BACK_URL', $this->buttonBackUrl);
	}
	protected function _setContentDataShippingAndPaymentContentTitle($p_contentTitle)
	{
		$this->set_content_data('SHIPPING_AND_PAYMENT_CONTENT_TITLE', $p_contentTitle);
	}
	protected function _setContentDataShippingAndPaymentInfoLinkMobile($p_contentUrl)
	{
		$this->set_content_data('SHIPPING_AND_PAYMENT_INFO_LINK_MOBILE', $p_contentUrl);
	}
	protected function _setContentDataShippingAndPaymentInfoLink($p_contentUrl)
	{
		$this->set_content_data('SHIPPING_AND_PAYMENT_INFO_LINK', $p_contentUrl);
	}
	protected function _setContentDataYoochooseShoppingCart($p_alsoInterestingHtml)
	{
		$this->set_content_data('MODULE_yoochoose_shopping_cart', $p_alsoInterestingHtml);
	}
	protected function _setContentDataInfoMessage1($p_orderValue)
	{
		$this->set_content_data('info_message_1', $p_orderValue);
	}
	protected function _setContentDataInfoMessage2($p_orderValue)
	{
		$this->set_content_data('info_message_2', $p_orderValue);
	}
	protected function _setContentDataOrderAmount($p_amount)
	{
		$this->set_content_data('order_amount', $p_amount);
	}
	protected function _setContentDataMinMaxOrder($p_minMax)
	{
		$this->set_content_data('min_order', $p_minMax);
	}
	protected function _setContentDataHiddenOptions($p_hiddenOptions)
	{
		$this->set_content_data('HIDDEN_OPTIONS', $p_hiddenOptions);
	}
	protected function _setContentDataOrderDetails($p_orderDetailsHtml)
	{
		$this->set_content_data('MODULE_order_details', $p_orderDetailsHtml);
	}
	protected function _setContentDataInfoMessage($p_infoMessage)
	{
		$this->set_content_data('info_message', $p_infoMessage);
	}
	protected function _setContentDataGiftCart()
	{
		$this->set_content_data('MODULE_gift_cart', $this->giftCartHtml);
		$this->set_content_data('CUSTOMER_STATUS', $_SESSION['customers_status']['customers_status_id']);
		$this->set_content_data('LINK_ACCOUNT', xtc_href_link('shop.php', 'do=CreateRegistree', 'SSL'));
	}
	protected function _setContentDataSharedCart()
	{
		$this->set_content_data('MODULE_shared_cart', $this->sharedCartHtml);
	}
	protected function _setContentDataForm()
	{
		$this->set_content_data('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_SHOPPING_CART, 'action=update_product', 'NONSSL', true, true, true)));
		$this->set_content_data('FORM_END', '</form>');
	}
	protected function _setCustomerStatusAllowCheckout()
	{
		$this->set_content_data('customer_status_allow_checkout', $_SESSION['customers_status']['customers_status_show_price']);
		$this->set_content_data('customer_status_allow_checkout_info', NOT_ALLOWED_TO_SEE_PRICES);
	}
	
	protected function _setShippingCostsSelection()
	{
		$cartShippingCostsContentView = MainFactory::create_object('CartShippingCostsContentView');
		$this->set_content_data('cart_shipping_costs_selection', $cartShippingCostsContentView->get_html());
	}
	
	protected function _setContentsDataDeprecated()
	{
		$this->set_content_data('BUTTON_RELOAD', xtc_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART, 'onclick="var gm_quantity_checker = new GMOrderQuantityChecker(); return gm_quantity_checker.check_cart();"'), 2);
		$this->set_content_data('BUTTON_BACK', '<a href="' . $this->buttonBackUrl . '"><img src="templates/' . CURRENT_TEMPLATE . '/buttons/' . $_SESSION['language'] . '/button_back.gif" alt="' . IMAGE_BUTTON_BACK . '" title="' . IMAGE_BUTTON_BACK . '" border="0" /></a>', 2);
		$this->set_content_data('BUTTON_CHECKOUT', '<a id="gm_checkout" onclick="var gm_quantity_checker = new GMOrderQuantityChecker(); return gm_quantity_checker.check_cart();" href="'.xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL').'">'.xtc_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT).'</a>', 2);
		$this->set_content_data('BUTTON_CONTINUE', '<a href="'.xtc_href_link(FILENAME_DEFAULT).'">'.xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE).'</a>', 2);
		$this->set_content_data('LINK_CLOSE', $this->buttonBackUrl, 2);
	}
}