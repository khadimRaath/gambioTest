<?php
/* --------------------------------------------------------------
   WishListContentView.inc.php 2016-05-24
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

require_once(DIR_FS_INC . 'xtc_array_to_string.inc.php');
require_once(DIR_FS_INC . 'xtc_image_button.inc.php');
require_once(DIR_FS_INC . 'xtc_image_submit.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_form.inc.php');
require_once(DIR_FS_INC . 'xtc_recalculate_price.inc.php');


/**
 * Class WishListContentView
 */
class WishListContentView extends ContentView
{
	protected $products = array();
	protected $hiddenOptions;

	/** @var wishList_ORIGIN $coo_whishlist */
	protected $coo_whishlist;
	protected $infoMessage = null;
	protected $gmHistory = array();
	protected $anyOutOfStock;
	protected $allowCheckout;
	protected $orderDetailsWishListContentView = null;


	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('module/wish_list.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		$this->_assignFilledCart();
		
		if($this->coo_whishlist->count_contents() <= 0)
		{
			$this->_assignEmptyCart();
		}

		$gmHistory = $this->gmHistory;

		$this->set_content_data('BUTTON_BACK_URL', $gmHistory[count($gmHistory) - 1]['CLOSE']);
	}

	
	protected function _assignFilledCart()
	{
		$this->anyOutOfStock = 0;

		$this->products = $this->coo_whishlist->get_products();
		$this->_buildAttributesArray();
		
		$this->set_content_data('FORM_ACTION', xtc_draw_form('cart_quantity',
		                                                     xtc_href_link('wish_list.php', 'action=update_product',
		                                                                   'NONSSL', true, true, true), 'post',
		                                                     'name="cart_quantity"'));
		$this->set_content_data('FORM_END', '</form>');
		$this->set_content_data('HIDDEN_OPTIONS', $this->hiddenOptions);
		$this->_orderDetails();
		
		if($this->infoMessage !== null)
		{
			$this->set_content_data('info_message', str_replace('+', ' ', htmlentities_wrapper($this->infoMessage)));
		}
		
		$this->_bofGmModGXCustomizer();
	}


	protected function _assignEmptyCart()
	{
		$cart_empty = true;
		if($this->infoMessage !== null)
		{
			$this->set_content_data('info_message', str_replace('+', ' ', htmlentities_wrapper($this->infoMessage)));
		}
		$this->set_content_data('cart_empty', $cart_empty);
		$this->set_content_data('BUTTON_CONTINUE', '<a href="' . xtc_href_link(FILENAME_DEFAULT) . '">' .
												   xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) .
												   '</a>');
	}


	protected function _buildAttributesArray()
	{
		$hiddenOptionsArray = array();
		
		for($i = 0; $i < count($this->products); $i++)
		{
			$isValidProduct = true;
			
			// Push all attributes information in an array
			if(isset($this->products[$i]['attributes']))
			{
				while(list($option, $value) = each($this->products[$i]['attributes']))
				{
					$hiddenOptionsArray[$i] .= xtc_draw_hidden_field('id[' . $this->products[$i]['id'] . '][' . $option . ']', $value);

					if((int)$value > 0)
					{
						$query = $this->getQueryForAttributes($this->products[$i], $option, $value);

						$attributes        = xtc_db_query($query);
						$attributesValues = xtc_db_fetch_array($attributes);

						if(empty($attributesValues))
						{
							$isValidProduct = false;
						}
						else
						{
							$this->products[$i][$option]['products_options_name']        = $attributesValues['products_options_name'];
							$this->products[$i][$option]['options_values_id']            = $value;
							$this->products[$i][$option]['products_options_values_name'] = $attributesValues['products_options_values_name'];
							$this->products[$i][$option]['options_values_price']         = $attributesValues['options_values_price'];
							$this->products[$i][$option]['price_prefix']                 = $attributesValues['price_prefix'];
							$this->products[$i][$option]['weight_prefix']                = $attributesValues['weight_prefix'];
							$this->products[$i][$option]['options_values_weight']        = $attributesValues['options_values_weight'];
							$this->products[$i][$option]['attributes_stock']             = $attributesValues['attributes_stock'];
							$this->products[$i][$option]['products_attributes_id']       = $attributesValues['products_attributes_id'];
							$this->products[$i][$option]['products_attributes_model']    = $attributesValues['attributes_model'];
						}
					}
				}
			}

			if(!$isValidProduct)
			{
				$query = "DELETE FROM customers_wishlist WHERE products_id = '" . xtc_db_input($this->products[$i]['id']) . "'";
				xtc_db_query($query);

				$query = "DELETE FROM customers_wishlist_attributes WHERE products_id = '" . xtc_db_input($this->products[$i]['id']) . "'";
				xtc_db_query($query);

				unset($_SESSION['wishList']->contents[$products[$i]['id']]);
				unset($this->products[$i]);
				unset($hiddenOptionsArray[$i]);
			}
		}

		$this->hiddenOptions = implode('', $hiddenOptionsArray);
	}


	/**
	 * @param array $product
	 * @param       $option
	 * @param       $value
	 *
	 * @return string
	 */
	protected function getQueryForAttributes(array $product, $option, $value)
	{
		$query = "SELECT `popt`.`products_options_name`, `poval`.`products_options_values_name`, `pa`.`options_values_price`, `pa`.`price_prefix`, `pa`.`weight_prefix`, `pa`.`options_values_weight`, `pa`.`attributes_stock`,`pa`.`products_attributes_id`,`pa`.`attributes_model`
													  FROM `" . TABLE_PRODUCTS_OPTIONS . "` popt, `" .
		TABLE_PRODUCTS_OPTIONS_VALUES . "` poval, `" .
		TABLE_PRODUCTS_ATTRIBUTES . "` pa
													  WHERE pa.products_id = '" . (int)$product['id'] . "'
													   AND pa.options_id = '" . (int)$option . "'
													   AND pa.options_id = popt.products_options_id
													   AND pa.options_values_id = '" . (int)$value . "'
													   AND pa.options_values_id = poval.products_options_values_id
													   AND popt.language_id = '" . (int)$_SESSION['languages_id'] . "'
													   AND poval.language_id = '" . (int)$_SESSION['languages_id'] .
		"'";

		return $query;
	}


	protected function _orderDetails()
	{
		# order details
		/** @var OrderDetailsWishListContentView $orderDetailsWishListContentView */
		$this->orderDetailsWishListContentView = MainFactory::create_object('OrderDetailsWishListContentView');
		
		$this->orderDetailsWishListContentView->setProductsArray($this->products);
		$t_view_html = $this->orderDetailsWishListContentView->prepare_data();

		$this->set_content_data('MODULE_order_details', $t_view_html);

		if(STOCK_CHECK == 'true')
		{
			if($this->anyOutOfStock == 1)
			{
				if(STOCK_ALLOW_CHECKOUT == 'true')
				{
					// write permission in session
					$this->allowCheckout = 'true';
					$this->set_content_data('info_message',
											sprintf(OUT_OF_STOCK_CAN_CHECKOUT, STOCK_MARK_PRODUCT_OUT_OF_STOCK));
				}
				else
				{
					$this->allowCheckout = 'false';
					$this->set_content_data('info_message',
											sprintf(OUT_OF_STOCK_CANT_CHECKOUT, STOCK_MARK_PRODUCT_OUT_OF_STOCK));
				}
			}
			else
			{
				$this->allowCheckout = 'true';
			}
		}
	}


	protected function _bofGmModGXCustomizer()
	{
		// BOF GM_MOD GX-Customizer:
		$this->set_content_data('BUTTON_RELOAD', '<a id="gm_update_wishlist" href="JavaScript:submit_to_wishlist()">' .
												 xtc_image_button('button_delete.gif', NC_WISHLIST) . '</a>');
		$this->set_content_data('BUTTON_CART',
								'<a id="gm_wishlist_to_cart" href="JavaScript:submit_wishlist_to_cart()">' .
								xtc_image_button('button_buy_now.gif', IMAGE_BUTTON_CHECKOUT) . '</a>');
		// EOF GM_MOD GX-Customizer
		$this->set_content_data('BUTTON_UPDATE', '<a href="JavaScript:update_wishlist()">' .
												 xtc_image_button('button_update_cart.gif', NC_WISHLIST) . '</a>');
		$this->set_content_data('BUTTON_CHECKOUT',
								'<a href="' . xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">' .
								xtc_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT) . '</a>');
	}


	/**
	 * @return wishList_ORIGIN
	 */
	public function getCooWhishlist()
	{
		return $this->coo_whishlist;
	}


	/**
	 * @param wishList_ORIGIN $coo_whishlist
	 */
	public function setCooWhishlist(wishList_ORIGIN $coo_whishlist)
	{
		$this->coo_whishlist = $coo_whishlist;
	}


	/**
	 * @return null
	 */
	public function getInfoMessage()
	{
		return $this->infoMessage;
	}


	/**
	 * @param $infoMessage
	 */
	public function setInfoMessage($infoMessage)
	{
			$this->infoMessage = $infoMessage;
	}


	/**
	 * @return mixed
	 */
	public function getGmHistory()
	{
		return $this->gmHistory;
	}


	/**
	 * @param array $gmHistory
	 */
	public function setGmHistory(array $gmHistory)
	{
			$this->gmHistory = $gmHistory;
	}


	/**
	 * @return mixed
	 */
	public function getAnyOutOfStock()
	{
		return $this->anyOutOfStock;
	}


	/**
	 * @param mixed $anyOutOfStock
	 */
	public function setAnyOutOfStock(&$anyOutOfStock)
	{
		$this->anyOutOfStock = &$anyOutOfStock;
	}

	/**
	 * @return mixed
	 */
	public function getHiddenOptions()
	{
		return $this->hiddenOptions;
	}


	/**
	 * @param mixed $hiddenOptions
	 */
	public function setHiddenOptions($hiddenOptions)
	{
		$this->hiddenOptions = $hiddenOptions;
	}


	/**
	 * @return mixed
	 */
	public function getAllowCheckout()
	{
		return $this->allowCheckout;
	}


	/**
	 * @param mixed $allowCheckout
	 */
	public function setAllowCheckout(&$allowCheckout)
	{
		$this->allowCheckout = &$allowCheckout;
	}


	/**
	 * @return array
	 */
	public function getProducts()
	{
		return $this->products;
	}


	/**
	 * @param array $products
	 */
	public function setProducts(array $products)
	{
		$this->products = $products;
	}
	
		
	/**
	 * @return null|OrderDetailsWishListContentView
	 */
	public function getOrderDetailsWishListContentView()
	{
		return $this->orderDetailsWishListContentView;
	}
}
