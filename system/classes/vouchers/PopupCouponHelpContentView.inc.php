<?php
/* --------------------------------------------------------------
  PopupCouponHelpContentView.inc.php 2014-09-02 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(popup_search_help.php,v 1.3 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (popup_search_help.php,v 1.6 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: http://dev1.gambio-shop.de/2008/shop/gambio/icons/persdaten.png 1238 2005-09-24 10:51:19Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once(DIR_FS_INC . 'xtc_date_short.inc.php');
require_once(DIR_FS_INC . 'xtc_get_currencies_values.inc.php');

class PopupCouponHelpContentView extends ContentView
{
	protected $coupon_id = 0;
	protected $language_id = 0;
	protected $coo_xtc_price;
	protected $coupon_data_array = array();


	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/popup_coupon_help.html');
		$this->set_flat_assigns(true);
	}


	public function prepare_data()
	{
		$this->build_html = false;
		if($this->coupon_id > 0 && $this->language_id > 0 && isset($this->coo_xtc_price))
		{
			$this->load_coupon();
			if(count($this->coupon_data_array) > 0)
			{
				$text_coupon_help = TEXT_COUPON_HELP_HEADER;
				$text_coupon_help .= sprintf(TEXT_COUPON_HELP_NAME,
											 $this->coupon_data_array['description']['coupon_name']);
				if(xtc_not_null($this->coupon_data_array['description']['coupon_description']))
				{
					$text_coupon_help .= sprintf(TEXT_COUPON_HELP_DESC,
												 $this->coupon_data_array['description']['coupon_description']);
				}
				$coupon_amount = $this->coupon_data_array['coupon_amount'];

				switch($this->coupon_data_array['coupon_type'])
				{
					case 'F' :
						$t_gm_currency_array = array();

						$t_gm_currency_array = xtc_get_currencies_values($_SESSION['currency']);

						if(!empty($t_gm_currency_array['value']))
						{
							$this->coupon_data_array['coupon_amount'] = (double)$this->coupon_data_array['coupon_amount'] * (double)$t_gm_currency_array['value'];

							$this->coupon_data_array['coupon_amount'] = round($this->coupon_data_array['coupon_amount'],
																			  2);
						}

						$text_coupon_help .= sprintf(TEXT_COUPON_HELP_FIXED,
													 $this->coo_xtc_price->xtcFormat($this->coupon_data_array['coupon_amount'],
																					 true));
						break;
					case 'P' :
						$text_coupon_help .= sprintf(TEXT_COUPON_HELP_FIXED,
													 number_format((double)$this->coupon_data_array['coupon_amount'],
																   2) . '%');
						break;
					case 'S' :
						$text_coupon_help .= TEXT_COUPON_HELP_FREESHIP;
						break;
					default :
				}

				$t_gm_currency_array = xtc_get_currencies_values($_SESSION['currency']);

				if(empty($t_gm_currency_array['value']) == false)
				{
					$this->coupon_data_array['coupon_minimum_order'] = (double)$this->coupon_data_array['coupon_minimum_order'] * (double)$t_gm_currency_array['value'];

					$this->coupon_data_array['coupon_minimum_order'] = round($this->coupon_data_array['coupon_minimum_order'],
																			 2);
				}

				$text_coupon_help .= sprintf(TEXT_COUPON_HELP_MINORDER,
											 $this->coo_xtc_price->xtcFormat($this->coupon_data_array['coupon_minimum_order'],
																			 true));
				$text_coupon_help .= sprintf(TEXT_COUPON_HELP_DATE,
											 xtc_date_short($this->coupon_data_array['coupon_start_date']),
											 xtc_date_short($this->coupon_data_array['coupon_expire_date']));

				$cats    = '';
				$t_query = 'SELECT
								restrict_to_categories
							FROM
								' . TABLE_COUPONS . '
							WHERE
								coupon_id = "' . (int)$this->coupon_id . '"';

				$coupon_get = xtc_db_query($t_query);
				$get_result = xtc_db_fetch_array($coupon_get);

				$cat_ids = explode(',', $get_result['restrict_to_categories']);
				for($i = 0; $i < count($cat_ids); $i++)
				{
					$t_query = 'SELECT
									*
								FROM
									' . TABLE_CATEGORIES . ' c,
									' . TABLE_CATEGORIES_DESCRIPTION . ' cd 
								WHERE
									c.categories_id = cd.categories_id 
									AND cd.language_id = "' . (int)$this->language_id . '"
									AND c.categories_id = "' . (int)$cat_ids[$i] . '"';
					$result  = xtc_db_query($t_query);
					if($row = xtc_db_fetch_array($result))
					{
						$cats .= '<br />' . $row["categories_name"];
					}
				}

				$prods   = '';
				$t_query = 'SELECT
								restrict_to_products
							FROM
								' . TABLE_COUPONS . '
							WHERE
								coupon_id = "' . (int)$this->coupon_id . '"';

				$coupon_get = xtc_db_query($t_query);
				$get_result = xtc_db_fetch_array($coupon_get);

				$pr_ids = explode(',', $get_result['restrict_to_products']);
				for($i = 0; $i < count($pr_ids); $i++)
				{
					$t_query = 'SELECT
									*
								FROM
									' . TABLE_PRODUCTS . ' p,
									' . TABLE_PRODUCTS_DESCRIPTION . ' pd
								WHERE
									p.products_id = pd.products_id
									AND pd.language_id = "' . (int)$this->language_id . '"
									AND p.products_id = "' . (int)$pr_ids[$i] . '"';

					$result = xtc_db_query($t_query);
					if($row = xtc_db_fetch_array($result))
					{
						$prods .= '<br />' . $row["products_name"];
					}
				}

				// BOF GM_MOD
				if($cats != '' || $prods != '')
				{
					$text_coupon_help .= '<b>' . TEXT_COUPON_HELP_RESTRICT . '</b>';

					if($cats != '')
					{
						$text_coupon_help .= '<br /><br />' . TEXT_COUPON_HELP_CATEGORIES . ':';
						$text_coupon_help .= $cats;
					}

					if($prods != '')
					{
						$text_coupon_help .= '<br /><br />' . TEXT_COUPON_HELP_PRODUCTS . ':';
						$text_coupon_help .= $prods;
					}
				}
				// EOF GM_MOD

				$this->set_content_data('TEXT_HELP', $text_coupon_help);
				$this->set_content_data('link_close', 'javascript:window.close()');
				$this->build_html = true;
			}
		}
	}


	protected function load_coupon()
	{
		$t_query  = 'SELECT 
						*
					FROM
						' . TABLE_COUPONS . '
					WHERE
						coupon_id = "' . (int)$this->coupon_id . '"';
		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$this->coupon_data_array = xtc_db_fetch_array($t_result);
			$this->load_coupon_description();
		}
	}


	protected function load_coupon_description()
	{
		$t_query  = 'SELECT 
						*
					FROM
						' . TABLE_COUPONS_DESCRIPTION . '
					WHERE
						coupon_id = "' . (int)$this->coupon_id . '"
						AND language_id = "' . (int)$this->language_id . '"';
		$t_result = xtc_db_query($t_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$this->coupon_data_array['description'] = xtc_db_fetch_array($t_result);
		}
	}
}