<?php
/* --------------------------------------------------------------
   WishListController.php 2016-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class WishListController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class WishListController extends HttpViewController
{
	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionDefault()
	{
		$json = $this->_getWishListJson();
		
		return MainFactory::create('JsonHttpControllerResponse', $json);
	}
	
	
	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionAdd()
	{
		# fake environment
		$_POST['submit_target'] = $_POST['target'];

		$this->_performAction('add_product');

		$result = array(
			'success'   => true,
			'type'      => 'url',
			'url'       => 'wish_list.php',
			'content'   => array()
		);

		return MainFactory::create('JsonHttpControllerResponse', $result);
	}


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionDelete()
	{
		$this->_performAction('update_product');
		
		return $this->actionDefault();
	}


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionUpdate()
	{
		$this->_performAction('update_wishlist');

		return $this->actionDefault();
	}


	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionAddToCart()
	{
		$this->_performAction('wishlist_to_cart');
		
		return $this->actionDefault();
	}


	/**
	 * @param string $p_action
	 */
	protected function _performAction($p_action)
	{
		$t_turbo_buy_now = true;    # flag used in cart_actions
		$t_show_cart     = false;        # will be changed in cart_actions
		$t_show_details  = false;    # will be changed in cart_actions

		$cartActionsProcess = MainFactory::create_object('CartActionsProcess');
		$cartActionsProcess->set_data('GET', $_GET);
		$cartActionsProcess->set_data('POST', $_POST);

		// Lokale
		if(isset($t_turbo_buy_now))
		{
			$cartActionsProcess->reference_set_('turbo_buy_now', $t_turbo_buy_now);
		}
		if(isset($t_show_cart))
		{
			$cartActionsProcess->reference_set_('show_cart', $t_show_cart);
		}
		if(isset($t_show_details))
		{
			$cartActionsProcess->reference_set_('show_details', $t_show_details);
		}

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
	}
	
	
	/**
	 * Builds a JSON array that contains the HTML snippets to build the current wish list
	 *
	 * @return array JSON array of the current wish list
	 */
	protected function _getWishListJson()
	{
		$json = [
			'success' => true
		];
		
		$wishListContentView = $this->_getWishListContentView();
		
		$wishListContentView->prepare_data();
		$json['products'] = $this->_getProducts($wishListContentView->getOrderDetailsWishListContentView());
		$json['content']  = $this->_getContents($wishListContentView);
		
		return $json;
	}
	
	
	/**
	 * Returns an initialized WishListContentView object
	 *
	 * @return WishListContentView
	 */
	protected function _getWishListContentView()
	{
		unset($_SESSION['any_out_of_stock']);
		
		/** @var WishListContentView $wishListContentView */
		$wishListContentView = MainFactory::create_object('WishListContentView');
		
		if(isset($_SESSION['wishList']) === false)
		{
			trigger_error('Session has no Object wishList', E_USER_ERROR);
		}
		$wishListContentView->setCooWhishlist($_SESSION['wishList']);
		
		if(isset($_GET['info_message']))
		{
			$wishListContentView->setInfoMessage($_GET['info_message']);
		}
		if(isset($_SESSION['gm_history']))
		{
			$wishListContentView->setGmHistory($_SESSION['gm_history']);
		}
		if(isset($_SESSION['any_out_of_stock']) === false)
		{
			$_SESSION['any_out_of_stock'] = null;
		}
		$wishListContentView->setAnyOutOfStock($_SESSION['any_out_of_stock']);
		
		if(isset($_SESSION['allow_checkout']) === false)
		{
			$_SESSION['allow_checkout'] = null;
		}
		$wishListContentView->setAnyOutOfStock($_SESSION['allow_checkout']);
		
		return $wishListContentView;
	}
	
	
	/**
	 * Gets a JSON array of HTML snippets to build the product listing of the current wish list content
	 *
	 * @param OrderDetailsWishListContentView $orderDetailsWishListContentView
	 *
	 * @return array JSON array of the wish list content
	 */
	protected function _getProducts(OrderDetailsWishListContentView $orderDetailsWishListContentView)
	{
		$lang = MainFactory::create('LanguageTextManager', 'order_details', $_SESSION['languages_id']);
		$productArray = [];
		
		$orderDetailsWishListContentView->set_content_template('snippets/order/order_item.html');
		$orderDetailsWishListContentView->set_flat_assigns(true);
		$contentArray = $orderDetailsWishListContentView->get_content_array();
		$i = 0;
		
		$orderDetailsWishListContentView->set_content_data('is_wishlist', true);
		$orderDetailsWishListContentView->set_content_data('is_confirmation', false);
		
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
			
			$orderDetailsWishListContentView->set_content_data('last', $i >= count($contentArray['module_content']));
			$orderDetailsWishListContentView->set_content_data('p_url', $productData['PRODUCTS_LINK']);
			$orderDetailsWishListContentView->set_content_data('p_name', $productData['PRODUCTS_NAME']);
			$orderDetailsWishListContentView->set_content_data('image_src', $imageSrc);
			$orderDetailsWishListContentView->set_content_data('image_alt', $imageAlt);
			$orderDetailsWishListContentView->set_content_data('image_title', $imageAlt);
			$orderDetailsWishListContentView->set_content_data('p_model', $model);
			$orderDetailsWishListContentView->set_content_data('show_p_model', $productData['SHOW_PRODUCTS_MODEL']);
			$orderDetailsWishListContentView->set_content_data('p_weight', $weight);
			$orderDetailsWishListContentView->set_content_data('p_shipping_time', $shippingTime);
			$orderDetailsWishListContentView->set_content_data('p_attributes', $attributes);
			$orderDetailsWishListContentView->set_content_data('p_price_single', $productData['PRODUCTS_SINGLE_PRICE']);
			$orderDetailsWishListContentView->set_content_data('p_price_vpe', $vpe);
			$orderDetailsWishListContentView->set_content_data('p_shipping_info', $unit);
			$orderDetailsWishListContentView->set_content_data('p_unit', $unit);
			$orderDetailsWishListContentView->set_content_data('p_qty_name', $productData['PRODUCTS_QTY_INPUT_NAME']);
			$orderDetailsWishListContentView->set_content_data('p_qty_value', $productData['PRODUCTS_QTY_VALUE']);
			$orderDetailsWishListContentView->set_content_data('p_price_final', $productData['PRODUCTS_PRICE']);
			$orderDetailsWishListContentView->set_content_data('p_hidden_name', $productData['PRODUCTS_ID_INPUT_NAME']);
			$orderDetailsWishListContentView->set_content_data('p_hidden_value', $productData['PRODUCTS_ID_EXTENDED']);
			$orderDetailsWishListContentView->set_content_data('p_hidden_qty_name', $productData['PRODUCTS_OLDQTY_INPUT_NAME']);
			$orderDetailsWishListContentView->set_content_data('p_hidden_qty_value', $productData['PRODUCTS_QTY_VALUE']);
			$orderDetailsWishListContentView->set_content_data('p_hidden_cart_delete_name', $productData['PRODUCTS_CART_DELETE_INPUT_NAME']);
			$orderDetailsWishListContentView->set_content_data('p_hidden_cart_delete_value', $productData['PRODUCTS_ID_EXTENDED']);
			$orderDetailsWishListContentView->set_content_data('p_error_id', $productData['PRODUCTS_ID']);
			$orderDetailsWishListContentView->set_content_data('tpl_properties', $productData['PROPERTIES']);
			$orderDetailsWishListContentView->set_content_data('tpl_box_delete', $productData['BOX_DELETE']);
			
			$productArray['product_' . $productData['PRODUCTS_ID_EXTENDED']] = $orderDetailsWishListContentView->build_html();
			$i++;
		}
		
		$orderDetailsWishListContentView->set_flat_assigns(false);
		
		return $productArray;
	}
	
	
	/**
	 * Gets a JSON array of HTML snippets to build the content of the current wish list apart from its products.
	 *
	 * @param WishListContentView $wishListContentView
	 *
	 * @return array JSON array of the informational content (without products) of the wish list
	 */
	protected function _getContents(WishListContentView $wishListContentView)
	{
		$contentArray = [];
		$contentViewContentArray = $wishListContentView->get_content_array();
		
		$contentViewContentArray['HIDDEN_OPTIONS'] .= '<input type="hidden" name="submit_target" value="wishlist" class="force" />';
		
		$contentArray['hidden']    = [
			'selector' => 'hiddenOptions',
			'type'     => 'html',
			'value'    => $contentViewContentArray['HIDDEN_OPTIONS']
		];
		
		return $contentArray;
	}
}