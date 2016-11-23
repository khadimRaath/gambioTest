<?php
/* --------------------------------------------------------------
   CartController.inc.php 2016-08-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CartController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class CartController extends HttpViewController
{
	/** @var bool $turboBuyNow */
	protected $turboBuyNow = true;

	/** @var bool $showCart */
	protected $showCart = false;

	/** @var bool $showDetails */
	protected $showDetails = false;


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionDefault()
	{
		$json = $this->_getCartJson();

		return MainFactory::create('JsonHttpControllerResponse', $json);
	}


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionBuyProduct()
	{
		# fake environment
		$_GET['BUYproducts_id'] = (int)$_POST['products_id'];
		$_POST['submit_target'] = $_POST['target'];

		$this->_performAction('buy_now');

		$result = array(
			'success' => true,
			'type'    => (!$this->showCart && !$this->showDetails) ? 'dropdown' : 'url',
			'url'     => ($this->showCart
			              && !$this->showDetails) ? 'shopping_cart.php' : xtc_href_link(FILENAME_PRODUCT_INFO,
			                                                                            'products_id='
			                                                                            . (int)$_GET['BUYproducts_id']),
			'content' => array()
		);

		return MainFactory::create('JsonHttpControllerResponse', $result);
	}


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionAdd()
	{
		# fake environment
		$_GET['action']         = 'add_product';
		$_POST['submit_target'] = $_POST['target'];

		$showCart = $this->_performAction('add_product');

		$productsId = (int)$_POST['products_id'];

		if(isset($_POST['id']))
		{
			foreach($_POST['id'] as $optionId => $valueId)
			{
				$productsId .= '{' . $optionId . '}' . $valueId;
			}
		}

		if(isset($_POST['properties_values_ids']))
		{
			$propertiesControl = MainFactory::create_object('PropertiesControl');
			$combiId           = $propertiesControl->get_combis_id_by_value_ids_array(xtc_get_prid($_POST['products_id']),
			                                                                          $_POST['properties_values_ids']);
			$productsId .= 'x' . (int)$combiId;
		}

		$url = xtc_href_link(FILENAME_SHOPPING_CART);
		if(!$showCart)
		{
			$url = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($productsId,
			                                                             xtc_get_products_name($_POST['products_id'])
			                                                             )) . '&no_boost=1&open_cart_dropdown=1';
		}

		$result = array(
			'success' => true,
			'type'    => 'url',
			'url'     => preg_replace('/\{[\d]+\}0/', '', $url),
			'content' => array()
		);

		return MainFactory::create('JsonHttpControllerResponse', $result);
	}


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionDelete()
	{
		$this->_performAction('update_product');

		return $this->actionDefault();
	}


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionUpdate()
	{
		$this->_performAction('update_product');

		return $this->actionDefault();
	}


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionRedeemGift()
	{
		$this->_performAction('check_gift');
		$json = $this->_getGiftJson();

		return MainFactory::create('JsonHttpControllerResponse', $json);
	}


	/**
	 * @param string $p_action
	 *
	 * @return bool
	 */
	protected function _performAction($p_action)
	{
		$cartActionsProcess = MainFactory::create_object('CartActionsProcess');
		$cartActionsProcess->set_data('GET', $_GET);
		$cartActionsProcess->set_data('POST', $_POST);

		// Lokale
		$cartActionsProcess->reference_set_('turbo_buy_now', $this->turboBuyNow); # flag used in cart_actions
		$cartActionsProcess->reference_set_('show_cart', $this->showCart); # will be changed in cart_actions
		$cartActionsProcess->reference_set_('show_details', $this->showDetails); # will be changed in cart_actions

		// Globale
		$cartActionsProcess->set_('php_self', $GLOBALS['PHP_SELF']);
		$cartActionsProcess->set_('coo_seo_boost', $GLOBALS['gmSEOBoost']);
		if(isset($GLOBALS['order']) && is_null($GLOBALS['order']) == false)
		{
			$cartActionsProcess->set_('coo_order', $GLOBALS['order']);
		}
		if($GLOBALS['REMOTE_ADDR'] == false)
		{
			$GLOBALS['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
		}
		$cartActionsProcess->reference_set_('remote_address', $GLOBALS['REMOTE_ADDR']);
		$cartActionsProcess->set_('coo_price', $GLOBALS['xtPrice']);

		// Session
		if(isset($_SESSION['customer_id']))
		{
			$cartActionsProcess->set_('customer_id', $_SESSION['customer_id']);
		}
		$cartActionsProcess->set_('coo_wish_list', $_SESSION['wishList']);
		$cartActionsProcess->set_('coo_cart', $_SESSION['cart']);
		if(isset($_SESSION['coo_gprint_wishlist']) && is_null($_SESSION['coo_gprint_wishlist']) == false)
		{
			$cartActionsProcess->set_('coo_gprint_wish_list', $_SESSION['coo_gprint_wishlist']);
		}
		if(isset($_SESSION['coo_gprint_cart']) && is_null($_SESSION['coo_gprint_cart']) == false)
		{
			$cartActionsProcess->set_('coo_gprint_cart', $_SESSION['coo_gprint_cart']);
		}
		if(isset($_SESSION['info_message']))
		{
			$cartActionsProcess->reference_set_('info_message', $_SESSION['info_message']);
		}
		$cartActionsProcess->set_('customers_status_id', $_SESSION['customers_status']['customers_status_id']);
		$cartActionsProcess->set_('customers_fsk18', $_SESSION['customers_status']['customers_fsk18']);
		$cartActionsProcess->set_('customers_fsk18_display', $_SESSION['customers_status']['customers_fsk18_display']);

		$cartActionsProcess->proceed($p_action);

		$infoMessage = $cartActionsProcess->get_('info_message');
		if(trim($infoMessage) != '')
		{
			$_SESSION['info_message'] = $infoMessage;
		}
		
		unset($_SESSION['actual_content']);
		xtc_count_cart();
		
		return $this->showCart;
	}
	
	
	/**
	 * Builds a JSON array that contains the HTML snippets to build the current shopping cart
	 * 
	 * @return array JSON array of the current shopping cart 
	 */
	protected function _getCartJson()
	{
		$json = [
			'success' => true
		];
		
		$shoppingCartContentView = $this->_getCartContentView();
		
		$shoppingCartContentView->prepare_data();
		$json['products'] = $this->_getProducts($shoppingCartContentView->getOrderDetailsCartContentView());
		$json['content']  = $this->_getContents($shoppingCartContentView);
		
		$this->_tearDownTemporarySessionData();

		return $json;
	}
	
	
	/**
	 * Builds a JSON array that contains the HTML snippets to build the voucher redeem modal
	 * 
	 * @return array JSON array of the contents
	 */
	protected function _getGiftJson()
	{
		$json = [
			'success' => true
		];
		
		$shoppingCartContentView = $this->_getCartContentView();
		
		$json['content'] = $this->_getGiftContents($shoppingCartContentView);
		
		$this->_tearDownTemporarySessionData();
		
		return $json;
	}
	
	
	/**
	 * Returns an initialized ShoppingCartContentView object
	 * 
	 * @return ShoppingCartContentView
	 */
	protected function _getCartContentView()
	{
		$shoppingCartContentView = MainFactory::create_object('ShoppingCartContentView');
		
		$xtcPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		$shoppingCartContentView->setXtcPrice($xtcPrice);
		
		$shoppingCartContentView->setLanguagesId((int)$_SESSION['languages_id']);
		$shoppingCartContentView->setLanguageCode($_SESSION['language_code']);
		$shoppingCartContentView->setCart($_SESSION['cart']);
		$shoppingCartContentView->setCartCountContents($_SESSION['cart']->count_contents());
		$shoppingCartContentView->setCustomerStatusMinOrder($_SESSION['customers_status']['customers_status_min_order']);
		$shoppingCartContentView->setCustomerStatusMaxOrder($_SESSION['customers_status']['customers_status_max_order']);
		
		return $shoppingCartContentView;
	}
	
	
	/**
	 * Resets some session data that is set within the build methods of the content views
	 */
	protected function _tearDownTemporarySessionData()
	{
		unset($_SESSION['nvpReqArray'],
			  $_SESSION['reshash']['FORMATED_ERRORS'],
			  $_SESSION['reshash'],
			  $_SESSION['tmp_oID']);
	}
	
	
	/**
	 * Gets a JSON array of HTML snippets to build the product listing of the current shopping cart content
	 * 
	 * @param OrderDetailsCartContentView $orderDetailsCartContentView
	 *
	 * @return array JSON array of the shopping cart content
	 */
	protected function _getProducts(OrderDetailsCartContentView $orderDetailsCartContentView)
	{
		$lang = MainFactory::create('LanguageTextManager', 'order_details', $_SESSION['languages_id']);
		$productArray = [];
		
		$orderDetailsCartContentView->set_content_template('snippets/order/order_item.html');
		$orderDetailsCartContentView->set_flat_assigns(true);
		$contentArray = $orderDetailsCartContentView->get_content_array();
		$i = 0;
		
		$orderDetailsCartContentView->set_content_data('is_wishlist', false);
		$orderDetailsCartContentView->set_content_data('is_confirmation', false);
		
		foreach($contentArray['module_content'] as $productData)
		{
			$imageSrc     = $productData['PRODUCTS_IMAGE'] &&
			                $productData['PRODUCTS_IMAGE'] !== '' ? $productData['PRODUCTS_IMAGE'] : '';
			$imageAlt     = $productData['IMAGE_ALT'] &&
			                $productData['IMAGE_ALT'] !== '' ? $productData['IMAGE_ALT'] : $productData['PRODUCTS_NAME'];
			$model        = $productData['IMAGE_ALT'] &&
			                $productData['IMAGE_ALT'] !== '' ? $lang->get_text('text_model') . ' ' .
			                                                   $productData['PRODUCTS_MODEL'] : '';
			$weight       = $productData['GM_WEIGHT'] &&
			                $productData['GM_WEIGHT'] !== '' &&
			                $productData['GM_WEIGHT'] !== '0' ? $lang->get_text('text_weight') . ' ' .
			                                                    $productData['GM_WEIGHT'] . ' ' .
			                                                    $lang->get_text('text_weight_unit') : '';
			$shippingTime = $productData['PRODUCTS_SHIPPING_TIME'] &&
			                $productData['PRODUCTS_SHIPPING_TIME'] !== '' ? $lang->get_text('text_shippingtime') . ' ' .
			                                                                $productData['PRODUCTS_SHIPPING_TIME'] : '';
			$vpe          = $productData['PRODUCTS_VPE_ARRAY']['vpe_text'] &&
			                $productData['PRODUCTS_VPE_ARRAY']['vpe_text'] !== '' ? $productData['PRODUCTS_VPE_ARRAY']['vpe_text'] : '';
			$unit         = $productData['UNIT'] &&
			                $productData['UNIT'] !== '' ? $productData['UNIT'] : '';
			$attributes   = '';
			if($productData['ATTRIBUTES'] && $productData['ATTRIBUTES'] !== '')
			{
				foreach($productData['ATTRIBUTES'] as $attribute)
				{
					$attributes .= $attribute['NAME'] . ': ' . $attribute['VALUE_NAME'] . '<br />';
				}
			}
			
			$orderDetailsCartContentView->set_content_data('last', $i >= count($contentArray['module_content']));
			$orderDetailsCartContentView->set_content_data('p_url', $productData['PRODUCTS_LINK']);
			$orderDetailsCartContentView->set_content_data('p_name', $productData['PRODUCTS_NAME']);
			$orderDetailsCartContentView->set_content_data('image_src', $imageSrc);
			$orderDetailsCartContentView->set_content_data('image_alt', $imageAlt);
			$orderDetailsCartContentView->set_content_data('image_title', $imageAlt);
			$orderDetailsCartContentView->set_content_data('p_model', $model);
			$orderDetailsCartContentView->set_content_data('show_p_model', $productData['SHOW_PRODUCTS_MODEL']);
			$orderDetailsCartContentView->set_content_data('p_weight', $weight);
			$orderDetailsCartContentView->set_content_data('p_shipping_time', $shippingTime);
			$orderDetailsCartContentView->set_content_data('p_attributes', $attributes);
			$orderDetailsCartContentView->set_content_data('p_price_single', $productData['PRODUCTS_SINGLE_PRICE']);
			$orderDetailsCartContentView->set_content_data('p_price_vpe', $vpe);
			$orderDetailsCartContentView->set_content_data('p_shipping_info', $unit);
			$orderDetailsCartContentView->set_content_data('p_unit', $unit);
			$orderDetailsCartContentView->set_content_data('p_qty_name', $productData['PRODUCTS_QTY_INPUT_NAME']);
			$orderDetailsCartContentView->set_content_data('p_qty_value', $productData['PRODUCTS_QTY_VALUE']);
			$orderDetailsCartContentView->set_content_data('p_price_final', $productData['PRODUCTS_PRICE']);
			$orderDetailsCartContentView->set_content_data('p_hidden_name', $productData['PRODUCTS_ID_INPUT_NAME']);
			$orderDetailsCartContentView->set_content_data('p_hidden_value', $productData['PRODUCTS_ID_EXTENDED']);
			$orderDetailsCartContentView->set_content_data('p_hidden_qty_name', $productData['PRODUCTS_OLDQTY_INPUT_NAME']);
			$orderDetailsCartContentView->set_content_data('p_hidden_qty_value', $productData['PRODUCTS_QTY_VALUE']);
			$orderDetailsCartContentView->set_content_data('p_error_id', $productData['PRODUCTS_ID']);
			$orderDetailsCartContentView->set_content_data('tpl_properties', $productData['PROPERTIES']);
			
			$productArray['product_' . $productData['PRODUCTS_ID_EXTENDED']] = $orderDetailsCartContentView->build_html();
			$i++;
		}
		
		$orderDetailsCartContentView->set_flat_assigns(false);
		
		return $productArray;
	}
	
	
	/**
	 * Gets a JSON array of HTML snippets to build the content of the current shopping cart apart from its products.
	 * 
	 * @param ShoppingCartContentView $shoppingCartContentView
	 *
	 * @return array JSON array of the informational content (without products) of the shopping cart
	 */
	protected function _getContents(ShoppingCartContentView $shoppingCartContentView)
	{
		$contentArray = [];
		$contentViewContentArray = $shoppingCartContentView->get_content_array();
		
		$contentArray['hidden']    = [
			'selector' => 'hiddenOptions',
			'type'     => 'html',
			'value'    => $contentViewContentArray['HIDDEN_OPTIONS']
		];
		$contentArray['total']     = [
			'selector' => 'totals',
			'type'     => 'html',
			'value'    => $this->_getTotals($shoppingCartContentView->getOrderDetailsCartContentView())
		];
		$contentArray['shipping']  = [
			'selector' => 'shippingInformation',
			'type'     => 'replace',
			'value'    => $this->_getShippingInformation()
		];
		$contentArray['gift']      = [
			'selector' => 'giftLayer',
			'type'     => 'replace',
			'value'    => $this->_getGiftLayer()
		];
		$contentArray['button']    = [
			'selector' => 'buttons',
			'type'     => 'html',
			'value'    => $this->_getShoppingCartButton($shoppingCartContentView)
		];
		$contentArray['message']   = [
			'selector' => 'message',
			'type'     => 'html',
			'value'    => $this->_getMessages($shoppingCartContentView)
		];
		$contentArray['info']   = [
			'selector' => 'infoMessage',
			'type'     => 'html',
			'value'    => $this->_getInfoMessages($shoppingCartContentView)
		];
		
		return $contentArray;
	}
	
	
	/**
	 * Gets a JSON array that contains the HTML snippet for the content of the voucher redeem modal
	 *
	 * @param ShoppingCartContentView $shoppingCartContentView
	 * 
	 * @return array JSON array of the contents
	 */
	protected function _getGiftContents(ShoppingCartContentView $shoppingCartContentView)
	{
		$contentArray = [];
		
		$contentArray['gift']    = [
			'selector' => 'giftContent',
			'type'     => 'html',
			'value'    => $this->_getGiftCartContent()
		];
		
		return $contentArray;
	}
	
	
	/**
	 * Gets the HTML for the totals block 
	 * 
	 * @param OrderDetailsCartContentView $orderDetailsCartContentView
	 *
	 * @return mixed|string
	 */
	protected function _getTotals(OrderDetailsCartContentView $orderDetailsCartContentView)
	{
		$orderDetailsCartContentView->set_content_template('snippets/order/order_total.html');
		return $orderDetailsCartContentView->build_html();
	}
	
	
	/**
	 * Gets the HTML for the shipping information
	 * 
	 * @return mixed|string
	 */
	protected function _getShippingInformation()
	{
		$cartShippingCostsContentView = MainFactory::create_object('CartShippingCostsContentView');
		return $cartShippingCostsContentView->get_html();
	}
	
	
	/**
	 * Gets th HTML for the voucher redeem modal
	 * 
	 * @return mixed|string
	 */
	protected function _getGiftLayer()
	{
		$giftCartContentView = MainFactory::create_object('GiftCartContentView');
		return $giftCartContentView->get_html();
	}
	
	
	/**
	 * Gets the HTML for all available checkout buttons
	 * 
	 * @param ShoppingCartContentView $shoppingCartContentView
	 *
	 * @return mixed|string
	 */
	protected function _getShoppingCartButton(ShoppingCartContentView $shoppingCartContentView)
	{
		$shoppingCartContentView->set_content_template('snippets/shopping_cart_button.html');
		
		return $shoppingCartContentView->build_html();
	}
	
	
	/**
	 * Gets the HTML for all messages/warnings
	 * 
	 * @param ShoppingCartContentView $shoppingCartContentView
	 *
	 * @return mixed|string
	 */
	protected function _getMessages(ShoppingCartContentView $shoppingCartContentView)
	{
		$shoppingCartContentView->set_content_template('snippets/shopping_cart_messages.html');
		return $shoppingCartContentView->build_html();
	}
	
	/**
	 * Gets the HTML for all info messages.
	 *
	 * @param ShoppingCartContentView $shoppingCartContentView
	 *
	 * @return string
	 */
	protected function _getInfoMessages(ShoppingCartContentView $shoppingCartContentView)
	{
		$contentArray = $shoppingCartContentView->get_content_array(); 
		
		$message = ''; 
		
		if(array_key_exists('info_message_1', $contentArray))
		{
			$message = $contentArray['info_message_1'] . $contentArray['min_order'] . ' ' 
			           . $contentArray['info_message_2'] . $contentArray['order_amount']; 
		}
		
		return $message;
	}
	
	
	/**
	 * Gets the HTML for the content of the voucher redeem modal
	 * 
	 * @return mixed|string
	 */
	protected function _getGiftCartContent()
	{
		$giftCartContentView = MainFactory::create_object('GiftCartContentView');
		$giftCartContentView->set_content_template('snippets/gift_cart_content.html');
		return $giftCartContentView->get_html();
	}
}