<?php
/* --------------------------------------------------------------
   GMGPrintApplicationBottomExtender.inc.php 2016-03-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMGPrintApplicationBottomExtender extends GMGPrintApplicationBottomExtender_parent
{
	function proceed()
	{
		if(gm_get_conf('GM_SHOP_OFFLINE') != 'checked' || $_SESSION['customers_status']['customers_status_id'] == 0)
		{
			$t_page = $this->get_page();

			if(!is_object($_SESSION['coo_gprint_cart']) && $_SESSION['customer_id'] > 0)
			{
				$_SESSION['coo_gprint_cart'] = new GMGPrintCartManager();
			}

			if(!is_object($_SESSION['coo_gprint_wishlist']) && $_SESSION['customer_id'] > 0)
			{
				$_SESSION['coo_gprint_wishlist'] = new GMGPrintWishlistManager();
			}

			if(strpos(gm_get_env_info ('SCRIPT_NAME'), FILENAME_SHOPPING_CART) !== false)
			{
				if(is_object($_SESSION['coo_gprint_cart']))
				{
					$_SESSION['coo_gprint_cart']->restore();
				}
			}
			elseif(strpos(gm_get_env_info ('SCRIPT_NAME'), FILENAME_WISHLIST) !== false)
			{
				if(is_object($_SESSION['coo_gprint_wishlist']))
				{
					$_SESSION['coo_gprint_wishlist']->restore();
				}
			}

			if($t_page == 'ProductInfo' || $t_page == 'Cart' || $t_page == 'Wishlist')
			{
				$t_gm_gprint_product = false;
				$t_products_id = '';
				$t_value = array();
				$t_no_js = false;

				if($t_page == 'ProductInfo')
				{
					if(is_array($_SESSION['coo_gprint_cart']->v_elements))
					{
						foreach($_SESSION['coo_gprint_cart']->v_elements AS $t_products_id => $t_value)
						{
							$t_new_products_id = $_SESSION['coo_gprint_cart']->check_cart($t_products_id, 'cart');

							if($t_new_products_id !== false)
							{
								$t_products_id = $t_new_products_id;
							}

							if(strpos($this->v_data_array['GET']['info'], $t_products_id) !== false && strpos($this->v_data_array['GET']['info'], '{') !== false && $t_gm_gprint_product === false)
							{
								$t_gm_gprint_product = 'cart_' . $t_products_id;
							}
						}
					}

					if($t_gm_gprint_product === false && is_array($_SESSION['coo_gprint_wishlist']->v_elements))
					{
						foreach($_SESSION['coo_gprint_wishlist']->v_elements AS $t_products_id => $t_value)
						{
							$t_new_products_id = $_SESSION['coo_gprint_wishlist']->check_wishlist($t_products_id, 'wishList');

							if($t_new_products_id !== false)
							{
								$t_products_id = $t_new_products_id;
							}

							if(strpos($this->v_data_array['GET']['products_id'], $t_products_id) !== false && strpos($this->v_data_array['GET']['products_id'], '{') !== false && $t_gm_gprint_product === false)
							{
								$t_gm_gprint_product = 'wishlist_' . $t_products_id;
							}
							elseif(strpos($this->v_data_array['GET']['info'], $t_products_id) !== false && strpos($this->v_data_array['GET']['info'], '{') !== false && $t_gm_gprint_product === false)
							{
								$t_gm_gprint_product = 'wishlist_' . $t_products_id;
							}
						}
					}

					if($t_gm_gprint_product !== false)
					{
						$t_gm_gprint_product = '&amp;product=' . $t_gm_gprint_product;
					}

					$coo_gm_gprint_product_manager = new GMGPrintProductManager();

					$t_gm_gprint_surfaces_groups_id = $coo_gm_gprint_product_manager->get_surfaces_groups_id((int)$this->v_data_array['products_id']);

					if($t_gm_gprint_surfaces_groups_id === false)
					{
						$t_no_js = true;
					}
					else
					{
						$t_gm_gprint_surfaces_groups_id_parameter = '&amp;id=' . $t_gm_gprint_surfaces_groups_id;
					}
				}
				elseif(($t_page == 'Cart' && empty($_SESSION['coo_gprint_cart']->v_elements))
						|| ($t_page == 'Wishlist' && empty($_SESSION['coo_gprint_wishlist']->v_elements)))
				{
					$t_no_js = true;
				}

				if($t_no_js === false)
				{
					$t_open_cart_dropdown = '';
					if(isset($this->v_data_array['GET']['open_cart_dropdown']) && $this->v_data_array['GET']['open_cart_dropdown'] == '1')
					{
						$t_open_cart_dropdown = '&amp;open_cart_dropdown=1';
					}

					$this->v_output_buffer['GPRINT_JAVASCRIPT_CODE'] = '<script type="text/javascript" src="gm_javascript.js.php?page=Section&amp;section=load_gprint&amp;globals=off&amp;current_page=' . $t_page . '&amp;mode=frontend' . $t_gm_gprint_product . $t_gm_gprint_surfaces_groups_id_parameter . $t_open_cart_dropdown . '"></script>';
				}
			}
		}		
		
		parent::proceed();
	}
}