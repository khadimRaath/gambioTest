<?php
/* --------------------------------------------------------------
  product.php 2016-09-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com
  (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product.php 1316 2005-10-21 15:30:58Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// BOF GM_MOD:
require_once(DIR_FS_INC . 'xtc_get_products_stock.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');

class product_ORIGIN
{
	/**
	 *
	 * Constructor
	 *
	 */
	public function __construct($pID = 0, $p_language_id = false)
	{
		$this->pID = (int)$pID;
		$this->useStandardImage = false;
		$this->standardImage = 'noimage.gif';
		if($this->pID == 0)
		{
			$this->isProduct = false;
			return;
		}
		
		if($p_language_id === false)
		{
			$t_language_id = (int)$_SESSION['languages_id'];
		}
		else
		{
			$t_language_id = (int)$p_language_id;
		}
		// query for Product
		$group_check = "";
		if(GROUP_CHECK == 'true')
		{
			$group_check = " and p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
		}

		$fsk_lock = "";
		if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
		{
			$fsk_lock = ' and p.products_fsk18!=1';
		}

		$t_query = "SELECT
						p.*,
						pd.*,
						qud.quantity_unit_id,
						qud.unit_name
					FROM 
						" . TABLE_PRODUCTS . " p
					LEFT JOIN 
						" . TABLE_PRODUCTS_DESCRIPTION . " pd USING (products_id)
					LEFT JOIN 
						products_quantity_unit pqu USING (products_id)
					LEFT JOIN 
						quantity_unit_description qud ON (pqu.quantity_unit_id = qud.quantity_unit_id AND qud.language_id = '" . $t_language_id . "')
					WHERE
						p.products_id = '" . (int)$this->pID . "' AND
						p.products_status = '1'
						" . $group_check . $fsk_lock . " AND
						pd.language_id = '" . $t_language_id . "'";

		$t_query = xtc_db_query($t_query);

		if(!xtc_db_num_rows($t_query, true))
		{
			$this->isProduct = false;
		}
		else
		{
			$this->isProduct = true;
			$this->data = xtc_db_fetch_array($t_query, true);
		}
	}

	/**
	 *
	 *  Query for attributes count
	 *
	 */
	function getAttributesCount()
	{
		$t_query = 'SELECT
						count(*) as total 
					FROM
						' . TABLE_PRODUCTS_OPTIONS . ' popt, 
						' . TABLE_PRODUCTS_ATTRIBUTES . ' patrib
					WHERE
						patrib.products_id = "' . $this->pID . '"
						AND patrib.options_id = popt.products_options_id
						AND popt.language_id = "' . (int)$_SESSION['languages_id'] . '"';
		
		$products_attributes_query = xtc_db_query($t_query);
		$products_attributes = xtc_db_fetch_array($products_attributes_query, true);
		return $products_attributes['total'];
	}

	/**
	 *
	 * Query for reviews count
	 *
	 */
	function getReviewsCount()
	{
		$t_query = 'SELECT
						count(*) as total
					FROM 
						' . TABLE_REVIEWS . ' r,
						' . TABLE_REVIEWS_DESCRIPTION . ' rd
					WHERE
						r.products_id = "' . $this->pID . '"
						AND r.reviews_id = rd.reviews_id
						AND rd.languages_id = "' . (int)$_SESSION['languages_id'] . '"
						AND rd.reviews_text != ""';
		$reviews_query = xtc_db_query($t_query);
		$reviews = xtc_db_fetch_array($reviews_query, true);
		return $reviews['total'];
	}
	
	
	public function getAggregateRatingData()
	{
		$roundedAverageRating = 0;
		
		$queryString = 'SELECT
							`products_id`,
							AVG(`reviews_rating`) `rating`,
							count(`products_id`) `qty`
						FROM
							' . TABLE_REVIEWS . '
						WHERE
							`products_id` = ' . $this->pID . '
						GROUP BY
							`products_id`';
		
		$query = xtc_db_query($queryString);
		$count = xtc_db_num_rows($query);
		
		if($count !== 0)
		{
			$result               = xtc_db_fetch_array($query);
			$averageRating        = round($result['rating'] * 100) / 100;
			$roundedAverageRating = round($averageRating * 2) / 2;
			$count                = (int)$result['qty'];
		}
		
		$aggregateReviewData = array(
			'count'         => $count,
			'averageRating' => $roundedAverageRating
		);
		
		return $aggregateReviewData;
	}   
	

	/**
	 *
	 * select reviews
	 *
	 */
	function getReviews($p_limit = false)
	{
		$data_reviews = array();

		if($p_limit !== false && (int)$p_limit == 0)
		{
			return $data_reviews;
		}

		$t_limit = '';
		if((int)$p_limit > 0)
		{
			$t_limit = ' LIMIT ' . (int)$p_limit;
		}
		$t_query = 'SELECT
						r.reviews_rating,
						r.reviews_id,
						r.customers_name,
						r.date_added,
						r.last_modified,
						r.reviews_read,
						rd.reviews_text
					FROM
						' . TABLE_REVIEWS . ' r,
						' . TABLE_REVIEWS_DESCRIPTION . ' rd
					WHERE
						r.products_id = "' . $this->pID . '"
						AND r.reviews_id = rd.reviews_id
						AND rd.languages_id = "' . (int)$_SESSION['languages_id'] . '"
					ORDER BY
						reviews_id DESC'
					. $t_limit;
		$reviews_query = xtc_db_query($t_query);
		if(xtc_db_num_rows($reviews_query, true))
		{
			$row = 0;
			$data_reviews = array();
			while($reviews = xtc_db_fetch_array($reviews_query, true))
			{
				$row ++;
				$data_reviews[] = array(
					'AUTHOR' => $reviews['customers_name'],
					'DATE' => xtc_date_short($reviews['date_added']),
					'RATING' => xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $reviews['reviews_rating'] . '.gif', sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])),
					'TEXT' => $reviews['reviews_text'],
					'LINK' => xtc_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $this->pID . '&reviews_id=' . $reviews['reviews_id']),
					'DATE_CLEAN' => date('Y-m-d', strtotime($reviews['date_added'])),
					'RATING_CLEAN' => $reviews['reviews_rating']
				);
				if($row == PRODUCT_REVIEWS_VIEW)
				{
					break;
				}
			}
		}
		return $data_reviews;
	}

	/**
	 *
	 * return model if set, else return name
	 *
	 */
	function getBreadcrumbModel()
	{
		if($this->data['products_model'] != "")
		{
			return $this->data['products_model'];
		}
		return $this->data['products_name'];
	}

	/**
	 *
	 * get also purchased products related to current
	 *
	 */
	function getAlsoPurchased()
	{
		// BOF YOOCHOOSE
		if(defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)
		{
			require_once (DIR_FS_CATALOG . 'includes/yoochoose/recommendations.php');
			require_once (DIR_FS_CATALOG . 'includes/yoochoose/functions.php');
			return recommendData(getAlsoPurchasedStrategy(), $this->pID, MAX_DISPLAY_ALSO_PURCHASED);
		}
		// EOF YOOCHOOSE

		global $xtPrice;

		$module_content = array();

		$fsk_lock = "";
		if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
		{
			$fsk_lock = ' and p.products_fsk18!=1';
		}
		$group_check = "";
		if(GROUP_CHECK == 'true')
		{
			$group_check = " and p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
		}

		// BOF GM_MOD:
		$t_query = "SELECT
						p.products_fsk18,
						p.products_id,
						p.products_price,
						p.products_tax_class_id,
						p.products_image,
						pd.gm_alt_text,
						pd.products_name,
						pd.products_meta_description,
						p.products_vpe,
						p.products_vpe_status,
						p.products_vpe_value,
						pd.products_short_description
					FROM 
						" . TABLE_ORDERS_PRODUCTS . " opa,
						" . TABLE_ORDERS_PRODUCTS . " opb,
						" . TABLE_ORDERS . " o,
						" . TABLE_PRODUCTS . " p,
						" . TABLE_PRODUCTS_DESCRIPTION . " pd
					WHERE
						opa.products_id = '" . $this->pID . "'
						AND opa.orders_id = opb.orders_id
						AND opb.products_id != '" . $this->pID . "'
						AND opb.products_id = p.products_id
						AND opb.orders_id = o.orders_id
						AND p.products_status = '1'
						AND pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
						AND opb.products_id = pd.products_id
						AND o.date_purchased > DATE_SUB(NOW(),INTERVAL " . MAX_DISPLAY_ALSO_PURCHASED_DAYS . " DAY)
						" . $group_check . "
						" . $fsk_lock . "
					GROUP BY
						p.products_id
					ORDER BY
						o.date_purchased desc
					LIMIT
						" . MAX_DISPLAY_ALSO_PURCHASED;
		$t_query = xtc_db_query($t_query);
		while($orders = xtc_db_fetch_array($t_query, true))
		{
			$module_content[] = $this->buildDataArray($orders);
		}

		return $module_content;
	}

	/**
	 *
	 *
	 *  Get Cross sells
	 *
	 *
	 */
	function getCrossSells()
	{
		global $xtPrice;

		$t_query = "SELECT
						products_xsell_grp_name_id 
					FROM 
						" . TABLE_PRODUCTS_XSELL . "
					WHERE 
						products_id = '" . $this->pID . "' 
					GROUP BY 
						products_xsell_grp_name_id";
		$cs_groups = xtc_db_query($t_query);
		$cross_sell_data = array();
		if(xtc_db_num_rows($cs_groups, true) > 0)
		{
			while($cross_sells = xtc_db_fetch_array($cs_groups, true))
			{

				$fsk_lock = '';
				if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
				{
					$fsk_lock = ' AND p.products_fsk18!=1';
				}
				$group_check = "";
				if(GROUP_CHECK == 'true')
				{
					$group_check = " AND p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
				}

				// BOF GM_MOD:
				$cross_query = "
								SELECT
									p.products_fsk18,
									p.products_tax_class_id,
									p.products_id,
									p.products_image,
									pd.products_name,
									pd.products_short_description,
									pd.products_meta_description,
									p.products_fsk18,
									p.products_price,
									pd.gm_alt_text,
									p.products_vpe,
									p.products_vpe_status,
									p.products_vpe_value,
									xp.sort_order 
								FROM 
									" . TABLE_PRODUCTS_XSELL . " xp,
									" . TABLE_PRODUCTS . " p,
									" . TABLE_PRODUCTS_DESCRIPTION . " pd
								WHERE
									xp.products_id = '" . $this->pID . "' 
								AND
									xp.xsell_id = p.products_id " .
									$fsk_lock .
									$group_check . "
								AND 
									p.products_id = pd.products_id 
								AND 
									xp.products_xsell_grp_name_id='" . $cross_sells['products_xsell_grp_name_id'] . "'							
								AND 
									pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
								AND 
									p.products_status = '1'
								ORDER BY 
									xp.sort_order ASC";

				$cross_query = xtDBquery($cross_query);
				if(xtc_db_num_rows($cross_query, true) > 0)
				{
					$cross_sell_data[$cross_sells['products_xsell_grp_name_id']] = array('GROUP' => xtc_get_cross_sell_name($cross_sells['products_xsell_grp_name_id']), 'PRODUCTS' => array());
				}

				while($xsell = xtc_db_fetch_array($cross_query, true))
				{
					$cross_sell_data[$cross_sells['products_xsell_grp_name_id']]['PRODUCTS'][] = $this->buildDataArray($xsell);
				}
			}
			return $cross_sell_data;
		}
	}

	/**
	 *
	 * get reverse cross sells
	 *
	 */
	function getReverseCrossSells()
	{
		global $xtPrice;


		$fsk_lock = '';
		if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
		{
			$fsk_lock = ' and p.products_fsk18!=1';
		}
		$group_check = "";
		if(GROUP_CHECK == 'true')
		{
			$group_check = " and p.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
		}

		// BOF GM_MOD:
		$t_query = "SELECT
						p.products_fsk18,
						p.products_tax_class_id,
						p.products_id,
						p.products_image,
						pd.products_name,
						pd.products_short_description,
						pd.products_meta_description,
						p.products_fsk18,
						p.products_price,
						pd.gm_alt_text,
						p.products_vpe,
						p.products_vpe_status,
						p.products_vpe_value,
						xp.sort_order 
					FROM 
						" . TABLE_PRODUCTS_XSELL . " xp,
						" . TABLE_PRODUCTS . " p,
						" . TABLE_PRODUCTS_DESCRIPTION . " pd
					WHERE
						xp.xsell_id = '" . $this->pID . "' 
					AND 
						xp.products_id = p.products_id " .
					$fsk_lock .
					$group_check . "
					AND 
						p.products_id = pd.products_id 
					AND 
						pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
					AND 
						p.products_status = '1'
					ORDER BY
						xp.sort_order ASC";
		
		$cross_query = xtc_db_query($t_query);

		while($xsell = xtc_db_fetch_array($cross_query, true))
		{
			$cross_sell_data[] = $this->buildDataArray($xsell);
		}

		return $cross_sell_data;
	}

	function getGraduated()
	{
		global $xtPrice;

		$staffel_data = array();

		if($xtPrice->xtcCheckSpecial($this->pID) == 0)
		{
			$staffel_query = xtDBquery("SELECT
														 quantity,
														 personal_offer
														 FROM
														 " . TABLE_PERSONAL_OFFERS_BY . (int)$_SESSION['customers_status']['customers_status_id'] . "
														 WHERE
														 products_id = '" . $this->pID . "'
														 ORDER BY quantity ASC");

			$staffel = array();
			while($staffel_values = xtc_db_fetch_array($staffel_query, true))
			{
				$staffel[] = array('stk' => (double)$staffel_values['quantity'], 'price' => (double)$staffel_values['personal_offer']);
			}

			for($i = 0, $n = sizeof($staffel); $i < $n; $i ++)
			{
				// BOF GM_MOD
				$quantity_output = '';

				$quantity = (double)$staffel[$i]['stk'];

				if($quantity < (double)$this->data['gm_min_order'])
				{
					$quantity = (double)$this->data['gm_min_order'];
				}

				if(isset($staffel[$i + 1]['stk']))
				{
					if((double)$staffel[$i + 1]['stk'] - (double)$this->data['gm_graduated_qty'] > $quantity)
					{
						$quantity_output = gm_prepare_number($quantity, $xtPrice->currencies[$_SESSION['currency']]['decimal_point']) . '-' . gm_prepare_number(($staffel[$i + 1]['stk'] - (double)$this->data['gm_graduated_qty']), $xtPrice->currencies[$_SESSION['currency']]['decimal_point']);
					}
					elseif((double)$staffel[$i + 1]['stk'] - (double)$this->data['gm_graduated_qty'] == $quantity)
					{
						$quantity_output = gm_prepare_number($quantity, $xtPrice->currencies[$_SESSION['currency']]['decimal_point']);
					}
				}
				else
				{
					$quantity -= (double)$this->data['gm_graduated_qty'];
					$quantity_output = '> ' . gm_prepare_number($quantity, $xtPrice->currencies[$_SESSION['currency']]['decimal_point']);
				}

				$vpe = '';

				if($this->data['products_vpe_status'] == 1 && $this->data['products_vpe_value'] != 0.0 && $staffel[$i]['price'] > 0)
				{
					$vpe = $staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount;
					$vpe = $vpe * (1 / $this->data['products_vpe_value']);
				}

				require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');

				if($quantity_output != '')
				{
					if($vpe)
					{
						$staffel_data[$i] = array('QUANTITY' => $quantity_output,
							'VPE' => trim($xtPrice->xtcFormat($vpe, true, $this->data['products_tax_class_id']) . TXT_PER . xtc_get_vpe_name($this->data['products_vpe'])),
							'PRICE' => $xtPrice->xtcFormat($staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount, true, $this->data['products_tax_class_id']),
							'UNIT' => $this->data['unit_name']);
					}
					else
					{
						$staffel_data[$i] = array('QUANTITY' => $quantity_output,
							'VPE' => $vpe,
							'PRICE' => $xtPrice->xtcFormat($staffel[$i]['price'] - $staffel[$i]['price'] / 100 * $discount, true, $this->data['products_tax_class_id']),
							'UNIT' => $this->data['unit_name']);
					}
				}
				// EOF GM_MOD
			}
		}



		return $staffel_data;
	}

	/**
	 *
	 * valid flag
	 *
	 */
	function isProduct()
	{
		return $this->isProduct;
	}

	// beta
	function getBuyNowButton($id, $name)
	{
		global $PHP_SELF;
		return '<a href="' . xtc_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id=' . $id . '&' . xtc_get_all_get_params(array('action')), 'NONSSL') . '">' . xtc_image_button('button_buy_now.gif', TEXT_BUY . $name . TEXT_NOW) . '</a>';
	}

	function getVPEtext($product, $price)
	{
		global $xtPrice;

		require_once (DIR_FS_INC . 'xtc_get_vpe_name.inc.php');

		if(!is_array($product))
		{
			$product = $this->data;
		}

		if($product['products_vpe_status'] == 1 && $product['products_vpe_value'] != 0.0 && $price > 0)
		{
			return $xtPrice->xtcFormat($price * (1 / $product['products_vpe_value']), true) . TXT_PER . xtc_get_vpe_name($product['products_vpe']);
		}

		return null;
	}

	function gm_min_order($pID)
	{
		static $t_min_order_array;

		if($t_min_order_array !== null && isset($t_min_order_array[$pID]))
		{
			return $t_min_order_array[$pID];
		}
		elseif(is_array($t_min_order_array) === false)
		{
			$t_min_order_array = array();
		}

		$min_order = 1;
		$gm_get_min_order = xtc_db_query("SELECT gm_min_order, gm_graduated_qty FROM products WHERE products_id = '" . $pID . "'");
		if(xtc_db_num_rows($gm_get_min_order) == 1)
		{
			$qty = xtc_db_fetch_array($gm_get_min_order);
			if($qty['gm_min_order'] >= $qty['gm_graduated_qty'])
			{
				$min_order = $qty['gm_min_order'];
			}
			else
			{
				$min_order = $qty['gm_graduated_qty'];
			}
			if($min_order <= 0)
			{
				$min_order = 1;
			}
		}

		$min_order = (double)$min_order;
		$min_order = gm_convert_qty($min_order, false);

		$t_min_order_array[$pID] = $min_order;

		return $min_order;
	}

	function buildDataArray(&$array, $image = 'thumbnail')
	{
		global $xtPrice, $main;
		// BOF GM_MOD
		global $PHP_SELF, $gmSEOBoost;

		if(isset($array['cat_url']) == false)
		{
			$array['cat_url'] = '';
		}
		if(isset($array['expires_date']) == false)
		{
			$array['expires_date'] = '';
		}
		if(isset($array['gm_alt_text']) == false)
		{
			$array['gm_alt_text'] = '';
		}
		if(isset($array['gm_show_weight']) == false)
		{
			$array['gm_show_weight'] = '';
		}
		if(isset($array['ID']) == false)
		{
			$array['ID'] = '';
		}
		if(isset($array['products_description']) == false)
		{
			$array['products_description'] = '';
		}
		if(isset($array['products_fsk18']) == false)
		{
			$array['products_fsk18'] = '';
		}
		if(isset($array['products_id']) == false)
		{
			$array['products_id'] = '';
		}
		if(isset($array['products_image_h']) == false)
		{
			$array['products_image_h'] = '';
		}
		if(isset($array['products_image_w']) == false)
		{
			$array['products_image_w'] = '';
		}
		if(isset($array['products_image']) == false)
		{
			$array['products_image'] = '';
		}
		if(isset($array['products_meta_description']) == false)
		{
			$array['products_meta_description'] = '';
		}
		if(isset($array['products_name']) == false)
		{
			$array['products_name'] = '';
		}
		if(isset($array['products_price']) == false)
		{
			$array['products_price'] = '';
		}
		if(isset($array['products_shippingtime']) == false)
		{
			$array['products_shippingtime'] = '';
		}
		if(isset($array['products_short_description']) == false)
		{
			$array['products_short_description'] = '';
		}
		if(isset($array['products_tax_class_id']) == false)
		{
			$array['products_tax_class_id'] = '';
		}
		if(isset($array['products_weight']) == false)
		{
			$array['products_weight'] = '';
		}

		$tax_rate = $xtPrice->TAX[$array['products_tax_class_id']];

		$coo_properties_control = MainFactory::create_object('PropertiesControl');
		$t_combi = $coo_properties_control->get_cheapest_combi($array['products_id'], $_SESSION['languages_id']);
		$products_price = $xtPrice->xtcGetPrice($array['products_id'], true, 1, $array['products_tax_class_id'], $array['products_price'], 1, 0, true, true, $t_combi['products_properties_combis_id']);

		if($t_combi != false && !empty($t_combi['products_vpe_id']))
		{
			$array['products_vpe_value'] = $t_combi['vpe_value'];
			$array['products_vpe'] = $t_combi['products_vpe_id'];
		}

		// BOF GM_MOD
		$buy_now = '';
		$gm_buy_now_url = '';
		$gm_qty = '';
		$t_qty_array = array();
		$gm_buy_now = xtc_draw_hidden_field('products_id', $array['products_id'], 'class="gm_products_id"');
		if($_SESSION['customers_status']['customers_status_show_price'] != '0' && $xtPrice->gm_check_price_status($array['products_id']) == 0)
		{
			if($_SESSION['customers_status']['customers_fsk18'] == '1')
			{
				if($array['products_fsk18'] == '0')
				{
					$buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
					$gm_buy_now_url = xtc_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id=' . $array['products_id'] . '&' . xtc_get_all_get_params(array('action')), 'NONSSL');
					$gm_qty = xtc_draw_input_field('products_qty', $this->gm_min_order($array['products_id']), 'size="3" id="gm_attr_calc_qty_' . $array['products_id'] . '" onkeyup="gm_calc_prices_listing(\'' . $array['products_id'] . '\')"', 'text', true, "gm_listing_form gm_class_input");
					$gm_buy_now .= xtc_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, 'class="gm_image_button"');
					$t_qty_array = array('NAME' => 'products_qty',
						'VALUE' => $this->gm_min_order($array['products_id']),
						'SIZE' => '3',
						'ID' => 'gm_attr_calc_qty_' . $array['products_id'],
						'ONKEYUP' => 'gm_calc_prices_listing(\'' . $array['products_id'] . '\')',
						'CLASS' => 'gm_listing_form gm_class_input',
						'TYPE' => 'text');
				}
			}
			else
			{
				$buy_now = $this->getBuyNowButton($array['products_id'], $array['products_name']);
				$gm_buy_now_url = xtc_href_link(basename($PHP_SELF), 'action=buy_now&BUYproducts_id=' . $array['products_id'] . '&' . xtc_get_all_get_params(array('action')), 'NONSSL');
				$gm_qty = xtc_draw_input_field('products_qty', $this->gm_min_order($array['products_id']), 'size="3" id="gm_attr_calc_qty_' . $array['products_id'] . '" onkeyup="gm_calc_prices_listing(\'' . $array['products_id'] . '\')"', 'text', true, "gm_listing_form gm_class_input");
				$gm_buy_now .= xtc_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, 'class="gm_image_button"');
				$t_qty_array = array('NAME' => 'products_qty',
					'VALUE' => $this->gm_min_order($array['products_id']),
					'SIZE' => '3',
					'ID' => 'gm_attr_calc_qty_' . $array['products_id'],
					'ONKEYUP' => 'gm_calc_prices_listing(\'' . $array['products_id'] . '\')',
					'CLASS' => 'gm_listing_form gm_class_input',
					'TYPE' => 'text');
			}
		}

		$t_shipping_status_id = $array['products_shippingtime'];
		if($xtPrice->gm_check_price_status($array['products_id']) == 1 || $xtPrice->gm_check_price_status($array['products_id']) == 2)
		{
			if($array['products_price'] > 0 && $xtPrice->gm_check_price_status($array['products_id']) == 2)
			{
				$gm_tax_info = $main->getTaxInfo($tax_rate);
			}
			else
			{
				$gm_tax_info = '';
			}
			$gm_shipping_link = '';
			$t_shipping_info_link_active = '';
			$shipping_status_name = '';
			$shipping_status_image = '';
		}
		else
		{
			$gm_tax_info = $main->getTaxInfo($tax_rate);
			$gm_shipping_link = $main->getShippingLink(true, $array['products_id']);

			if(ACTIVATE_SHIPPING_STATUS == 'true')
			{
				$shipping_status_name = $main->getShippingStatusName($t_shipping_status_id);
				$shipping_status_image = $main->getShippingStatusImage($t_shipping_status_id);
				$t_shipping_info_link_active = $main->getShippingStatusInfoLinkActive($t_shipping_status_id);
			}
			else
			{
				$shipping_status_name = '';
				$shipping_status_image = '';
				$t_shipping_info_link_active = '';
			}
		}

		if($gmSEOBoost->boost_products)
		{
			$gm_product_link = xtc_href_link($gmSEOBoost->get_boosted_product_url($array['products_id'], $array['products_name']));
		}
		else
		{
			$gm_product_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($array['products_id'], $array['products_name']));
		}

		$gm_products_stock = gm_convert_qty(xtc_get_products_stock($array['products_id']), false);

		// set image size once a time if !exist
		if(isset($array['products_image_w']) && empty($array['products_image_w']) && xtc_not_null($array['products_image']))
		{
			$gm_imagesize = $this->productImageSize($array['products_id'], $array['products_image']);
			$array['products_image_w'] = $gm_imagesize[0];
			$array['products_image_h'] = $gm_imagesize[1];
		}

		$gm_cat_search = '';
		if(isset($_GET['cat']))
		{
			$gm_cat_search = '&cat=' . $_GET['cat'];
		}
		if(isset($_GET['keywords']))
		{
			$gm_cat_search = '&keywords=' . $_GET['keywords'];
			if(isset($_GET['page']))
			{
				$gm_cat_search .= '&page=' . $_GET['page'];
			}
		}

		$t_form_array = array();
		$t_form_array = array('ID' => 'gm_add_to_cart_' . $array['products_id'],
			'ACTION_URL' => xtc_href_link('index.php', 'action=buy_now&BUYproducts_id=' . $array['products_id'] . $gm_cat_search, 'NONSSL', true, true, true),
			'METHOD' => 'post',
			'ONSUBMIT' => 'return gm_quantity_check_listing(\'' . $array['products_id'] . '\')'
		);

		$t_data_array = array('PRODUCTS_NAME' => htmlspecialchars_wrapper($array['products_name']),
			'COUNT' => $array['ID'],
			'PRODUCTS_ID' => $array['products_id'],
			'PRODUCTS_VPE' => $this->getVPEtext($array, $products_price['plain']),
			'PRODUCTS_IMAGE' => $this->productImage($array['products_image'], $image),
			'PRODUCTS_IMAGE_W' => $array['products_image_w'],
			'PRODUCTS_IMAGE_H' => $array['products_image_h'],
			'PRODUCTS_IMAGE_WIDTH' => PRODUCT_IMAGE_THUMBNAIL_WIDTH,
			'PRODUCTS_IMAGE_PADDING' => ((PRODUCT_IMAGE_THUMBNAIL_HEIGHT + 8) - $array['products_image_h']) / 2,
			'PRODUCTS_IMAGE_ALT' => $array['gm_alt_text'],
			'PRODUCTS_LINK' => $gm_product_link,
			'PRODUCTS_PRICE' => $products_price['formated'],
			'PRODUCTS_TAX_INFO' => $gm_tax_info,
			'PRODUCTS_SHIPPING_LINK' => $gm_shipping_link,
			'PRODUCTS_BUTTON_BUY_NOW' => $buy_now,
			'GM_PRODUCTS_BUTTON_BUY_NOW_URL' => $gm_buy_now_url,
			'GM_PRODUCTS_BUTTON_BUY_NOW' => $gm_buy_now,
			'PRODUCTS_SHIPPING_NAME' => $shipping_status_name,
			'PRODUCTS_SHIPPING_IMAGE' => $shipping_status_image,
			'PRODUCTS_SHIPPING_LINK_ACTIVE' => $t_shipping_info_link_active,
			'PRODUCTS_DESCRIPTION' => $array['products_description'],
			'PRODUCTS_EXPIRES' => $array['expires_date'],
			'PRODUCTS_CATEGORY_URL' => $array['cat_url'],
			'PRODUCTS_SHORT_DESCRIPTION' => $array['products_short_description'],
			'PRODUCTS_FSK18' => $array['products_fsk18'],
			'GM_FORM_ACTION' => xtc_draw_form('gm_add_to_cart_' . $array['products_id'], xtc_href_link('index.php', 'action=buy_now&BUYproducts_id=' . $array['products_id'] . $gm_cat_search, 'NONSSL', true, true, true), 'post', 'onsubmit="return gm_quantity_check_listing(\'' . $array['products_id'] . '\')"'),
			'FORM_DATA' => $t_form_array,
			'QTY_DATA' => $t_qty_array,
			'GM_FORM_END' => '</form>',
			'GM_PRODUCTS_QTY' => $gm_qty,
			'GM_PRODUCTS_STOCK' => $gm_products_stock,
			'PRODUCTS_META_DESCRIPTION' => $array['products_meta_description'],
			'PRODUCTS_WEIGHT' => gm_prepare_number((double)$array['products_weight'], $xtPrice->currencies[$xtPrice->actualCurr]['decimal_point']),
			'SHOW_PRODUCTS_WEIGHT' => $array['gm_show_weight']);

		return $t_data_array;
		// EOF GM_MOD
	}

	function productImage($name, $type)
	{
		switch($type)
		{
			case 'info' :
				$path = DIR_WS_INFO_IMAGES;
				break;
			case 'thumbnail' :
				$path = DIR_WS_THUMBNAIL_IMAGES;
				break;
			case 'popup' :
				$path = DIR_WS_POPUP_IMAGES;
				break;
		}

		if($name == '')
		{
			if($this->useStandardImage == 'true' && $this->standardImage != '')
			{
				return $path . $this->standardImage;
			}
		}
		else
		{
			// check if image exists
			if(!file_exists($path . $name))
			{
				if($this->useStandardImage == 'true' && $this->standardImage != '')
				{
					$name = $this->standardImage;
				}
			}
			return $path . $name;
		}
	}

	function productImageSize($pid, $image)
	{
		$gm_imagesize = @getimagesize(DIR_WS_THUMBNAIL_IMAGES . $image);
		$gm_query = xtc_db_query("
									UPDATE " .
				TABLE_PRODUCTS . "
									SET
										products_image_w = '" . $gm_imagesize[0] . "',
										products_image_h = '" . $gm_imagesize[1] . "'
									WHERE
										products_id = '" . $pid . "'						
									");
		return $gm_imagesize;
	}
}