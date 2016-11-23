<?php
/* --------------------------------------------------------------
   ProductReviewsWriteContentView.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_reviews_write.php,v 1.51 2003/02/13); www.oscommerce.com
   (c) 2003	 nextcommerce (product_reviews_write.php,v 1.13 2003/08/1); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_reviews_write.php 1101 2005-07-24 14:51:13Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class ProductReviewsWriteContentView extends ContentView
{
	protected $coo_product;
	protected $customer_info;
	protected $review_message;
	protected $rating = 3;
	protected $captcha_html;
	protected $error_text;
	protected $reviews_min_length;
	protected $privacy_accepted = '0';

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/product_reviews_write.html');
		$this->set_flat_assigns(true);
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['coo_product']        = array(
			'type'        => 'object',
			'object_type' => 'product'
		);
		$this->validation_rules_array['customer_info']      = array('type' => 'array');
		$this->validation_rules_array['review_message']     = array('type' => 'string');
		$this->validation_rules_array['rating']             = array('type' => 'int');
		$this->validation_rules_array['captcha_html']       = array('type' => 'string');
		$this->validation_rules_array['error_text']         = array('type' => 'string');
		$this->validation_rules_array['reviews_min_length'] = array('type' => 'int');
	}
	
	public function prepare_data()
	{
		$this->content_array['error'] = '';
		if(isset($this->coo_product) == false || $this->coo_product->isProduct() == false)
		{
			$this->content_array['error'] = ERROR_INVALID_PRODUCT;
		}
		else
		{
			if(trim($this->customer_info['customers_firstname']) == '' && trim($this->customer_info['customers_lastname']) == '')
			{
				$this->customer_info['customers_lastname'] = TEXT_GUEST;
			}
			$this->add_data();
		}

		$this->set_content_data('reviews_min_length', $this->reviews_min_length);
	}
	
	protected function add_data()
	{
		$this->content_array['PRODUCTS_NAME'] = $this->coo_product->data['products_name'];
		$this->content_array['AUTHOR'] = $this->customer_info['customers_firstname'].' '.$this->customer_info['customers_lastname'];
		$this->content_array['TEXTAREA_NAME'] = 'review';
		$this->content_array['TEXTAREA_VALUE'] = htmlentities_wrapper($this->review_message, true);
		$this->content_array['INPUT_RATING_NAME'] = 'rating';
		$this->content_array['INPUT_RATING_VALUE'] = $this->rating;

		$this->content_array['GM_VALIDATION_ACTIVE'] = gm_get_conf('GM_REVIEWS_VVCODE');
		$this->content_array['GM_CAPTCHA'] = $this->captcha_html;

		$this->content_array['GM_ERROR'] = $this->error_text;

		$this->content_array['FORM_ID'] = 'product_reviews_write';
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&' . xtc_product_link($this->coo_product->data['products_id'], $this->coo_product->data['products_name']), 'NONSSL', true, true, true);
		$this->content_array['FORM_METHOD'] = 'post';

		$this->content_array['BUTTON_BACK_LINK'] = 'javascript:history.back(1)';

		$this->content_array['GM_PRIVACY_LINK'] = gm_get_privacy_link('GM_CHECK_PRIVACY_REVIEWS');
		$this->content_array['show_privacy_checkbox'] = gm_get_conf('PRIVACY_CHECKBOX_REVIEWS'); 
		$this->content_array['privacy_accepted'] = (int)$this->privacy_accepted; 
	}
}
