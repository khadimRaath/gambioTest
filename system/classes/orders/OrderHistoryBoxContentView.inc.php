<?php
/* --------------------------------------------------------------
  OrderHistoryBoxContentView.inc.php 2014-07-17 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(order_history.php,v 1.4 2003/02/10); www.oscommerce.com
  (c) 2003	 nextcommerce (order_history.php,v 1.9 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: order_history.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');

class OrderHistoryBoxContentView extends ContentView
{
	protected $coo_seo_boost;
	protected $customer_id;
	protected $language_id = 2;
	protected $product_ids_array = array();

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_order_history.html');
		$this->set_caching_enabled(false);
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['coo_seo_boost']		= array('type' => 'object',
																	'object_type' => 'GMSEOBoost');
		$this->validation_rules_array['customer_id']		= array('type' => 'int');
		$this->validation_rules_array['language_id']		= array('type' => 'int');
		$this->validation_rules_array['product_ids_array']	= array('type' => 'array');
	}

	public function prepare_data()
	{
		$this->coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		
		if($_SESSION['style_edit_mode'] == 'edit')
		{
			$this->content_array['CONTENT'] = ' ';
		}
		
		if(isset($this->customer_id))
		{
			$this->get_product_ids_array();
			if(empty($this->product_ids_array) == false)
			{
				$this->content_array['CONTENT'] = '';
				$this->add_product_data();
			}
		}
	}
	
	protected function get_product_ids_array()
	{
		$t_query = 'SELECT DISTINCT
						op.products_id
					FROM
						' . TABLE_ORDERS . ' o,
						' . TABLE_ORDERS_PRODUCTS . ' op,
						' . TABLE_PRODUCTS . ' p
					WHERE
						o.customers_id = "' . $this->customer_id . '"
						AND	o.orders_id = op.orders_id
						AND	op.products_id = p.products_id
						AND	p.products_status = "1"
					GROUP BY
						products_id
					ORDER BY
						o.date_purchased DESC
					LIMIT
						' . MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX;
		$t_result = xtc_db_query($t_query);
		
		if(xtc_db_num_rows($t_result) > 0)
		{
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$this->product_ids_array[] = $t_row['products_id'];
			}
		}
	}
	
	protected function add_product_data()
	{
		$t_query = 'SELECT
						products_id,
						products_name,
						products_meta_description
					FROM
						' . TABLE_PRODUCTS_DESCRIPTION . '
					WHERE
						products_id in (' . implode(',', $this->product_ids_array) . ')
						AND language_id = "' . $this->language_id . '"
					ORDER BY
						products_name';
		$t_result = xtc_db_query($t_query);
		
		while($t_product_row = xtc_db_fetch_array($t_result))
		{
			if($this->coo_seo_boost->boost_products)
			{
				$t_product_link = xtc_href_link($this->coo_seo_boost->get_boosted_product_url($t_product_row['products_id'], $t_product_row['products_name']));
			}
			else
			{
				$t_product_link = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($t_product_row['products_id'], $t_product_row['products_name']));
			}
			$t_title = '';
			if($t_product_row['products_meta_description'] != '')
			{
				if(strlen_wrapper($t_product_row['products_meta_description']) > 80)
				{
					$t_title = ' title="' . htmlspecialchars_wrapper(substr_wrapper($t_product_row['products_meta_description'], 0, 80)) . '"';
				}
				else
				{
					$t_title = ' title="' . htmlspecialchars_wrapper($t_product_row['products_meta_description']) . '"';
				}
			}
			$this->content_array['CONTENT'] .= '<a href="' . $t_product_link . '"' . $t_title . '>' . $this->truncate($t_product_row['products_name'], gm_get_conf('TRUNCATE_PRODUCTS_HISTORY')) . '</a><br />';
		}
	}

	protected function truncate($p_string, $t_limit = 24)
	{
		if(strlen_wrapper($p_string) <= $t_limit)
		{
			return $p_string;
		}
		else
		{
			return substr_wrapper($p_string, 0, $t_limit) . '...';
		}
	}
}