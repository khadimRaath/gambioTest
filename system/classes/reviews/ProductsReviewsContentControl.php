<?php
/* --------------------------------------------------------------
  ProductReviewsContenControl.inc.php 2014-02-26 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(product_reviews.php,v 1.47 2003/02/13); www.oscommerce.com
  (c) 2003	 nextcommerce (product_reviews.php,v 1.12 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_reviews.php 1238 2005-09-24 10:51:19Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class ProductsReviewsContentControl extends DataProcessing
{
	protected $products_id;
	protected $products_name;
	protected $language_id;
	protected $language;
	protected $get_params;
	protected $get_params_back;
	protected $coo_products_reviews_content_view;

	protected function set_validation_rules()
	{
		$this->validation_rules_array['products_id']						= array('type' => 'int');
		$this->validation_rules_array['products_name']						= array('type' => 'string');
		$this->validation_rules_array['language_id']						= array('type' => 'int');
		$this->validation_rules_array['language']							= array('type' => 'string');
		$this->validation_rules_array['get_params']							= array('type' => 'string');
		$this->validation_rules_array['get_params_back']					= array('type' => 'string');
		$this->validation_rules_array['coo_products_reviews_content_view']	= array('type' => 'object',
																					'object_type' => 'ProductsReviewsContentView');
	}

	public function proceed()
	{
		// lets retrieve all $HTTP_GET_VARS keys and values..
		$this->get_params = xtc_get_all_get_params();
		$this->get_params_back = xtc_get_all_get_params(array('reviews_id')); // for back button
		$this->get_params = substr_wrapper($this->get_params, 0, -1); //remove trailing &
		if(xtc_not_null($this->get_params_back))
		{
			$this->get_params_back = substr_wrapper($this->get_params_back, 0, -1); //remove trailing &
		}
		else
		{
			$this->get_params_back = $this->get_params;
		}

		$t_query = 'SELECT
						pd.products_name
					FROM
						' . TABLE_PRODUCTS_DESCRIPTION . ' pd
					LEFT JOIN 
						' . TABLE_PRODUCTS . ' p on pd.products_id = p.products_id
					WHERE
						pd.language_id = "' . $this->language_id . '"
						AND p.products_status = "1"
						AND pd.products_id = "' . $this->products_id . '"';
		$t_result = xtc_db_query($t_query);
		if(!xtc_db_num_rows($t_result))
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_REVIEWS));
			return true;
		}
		$t_product_info = xtc_db_fetch_array($t_result);
		$this->products_name = $t_product_info['products_name'];

		$this->coo_products_reviews_content_view = MainFactory::create_object('ProductsReviewsContentView');
		$this->assign_data();
		$this->v_output_buffer = $this->coo_products_reviews_content_view->get_html();

		return true;
	}

	protected function assign_data()
	{
		$this->coo_products_reviews_content_view->set_('products_id', $this->products_id);
		$this->coo_products_reviews_content_view->set_('products_name', $this->products_name);
		$this->coo_products_reviews_content_view->set_('language', $this->language);
		$this->coo_products_reviews_content_view->set_('get_params', $this->get_params);
		$this->coo_products_reviews_content_view->set_('get_params_back', $this->get_params_back);
	}
}