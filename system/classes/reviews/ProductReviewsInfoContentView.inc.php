<?php
/* --------------------------------------------------------------
   ProductReviewsInfoContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_reviews_info.php,v 1.47 2003/02/13); www.oscommerce.com
   (c) 2003	 nextcommerce (product_reviews_info.php,v 1.12 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_reviews_info.php 1238 2005-09-24 10:51:19Z mz $) 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once (DIR_FS_INC.'xtc_break_string.inc.php');
require_once (DIR_FS_INC.'xtc_date_long.inc.php');

/**
 * Class ProductReviewsInfoContentView
 */
class ProductReviewsInfoContentView extends ContentView
{
	protected $review_data_array;
	protected $coo_seo_boost;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('/module/product_reviews_info.html');
		$this->set_flat_assigns(true);
	}
	
	
	public function prepare_data()
	{
		$uninitializedArray = $this->get_uninitialized_variables(array('review_data_array'));
		if(empty($uninitializedArray))
		{
			$this->coo_seo_boost = MainFactory::create_object('GMSEOBoost');
			$this->add_data();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $uninitializedArray) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	
	protected function add_data()
	{
		$this->content_array['PRODUCTS_NAME'] = $this->review_data_array['products_name'];
		$this->content_array['AUTHOR'] = $this->review_data_array['customers_name'];
		$this->content_array['DATE'] = xtc_date_long($this->review_data_array['date_added']);
		
		$reviews_text = xtc_break_string(htmlspecialchars_wrapper($this->review_data_array['reviews_text']), 60, '-<br />');
		$this->content_array['REVIEWS_TEXT'] = nl2br($reviews_text);
		$this->content_array['RATING'] = xtc_image('templates/'.CURRENT_TEMPLATE.'/img/stars_'.$this->review_data_array['reviews_rating'].'.gif', sprintf(TEXT_OF_5_STARS, $this->review_data_array['reviews_rating']));
		
		if($this->coo_seo_boost->boost_products)
		{
			$productLink = xtc_href_link($this->coo_seo_boost->get_boosted_product_url($this->review_data_array['products_id'], $this->review_data_array['products_name']) );
		}
		else
		{
			$productLink = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($this->review_data_array['products_id'], $this->review_data_array['products_name']));
		}
		$this->content_array['PRODUCTS_LINK'] = $productLink;

		// lets retrieve all $HTTP_GET_VARS keys and values..
		$getParams = xtc_get_all_get_params(array ('reviews_id'));
		$getParams = substr_wrapper($getParams, 0, -1); //remove trailing &
		
		$this->content_array['BUTTON_BACK_LINK'] = xtc_href_link(FILENAME_PRODUCT_REVIEWS, $getParams);
		$this->content_array['BUTTON_BUY_NOW_URL'] = xtc_href_link(FILENAME_DEFAULT, 'action=buy_now&BUYproducts_id=' . $this->review_data_array['products_id']);
		$this->content_array['IMAGE_URL'] = DIR_WS_THUMBNAIL_IMAGES . basename($this->review_data_array['products_image']);
		
		$this->add_rich_snippet_data();
	}
	
	
	protected function add_rich_snippet_data()
	{
		/* @var GoogleRichSnippetContentView $richSnippetView */
		$richSnippetView = MainFactory::create_object('GoogleRichSnippetContentView');
		$richSnippetView->set_fsk18((boolean)$this->review_data_array['products_fsk18']);
		$richSnippetView->set_price_status($this->review_data_array['gm_price_status']);
		$richSnippetView->set_quantity($this->review_data_array['products_quantity']);
		$richSnippetView->set_review_date_created(date('Y-m-d', strtotime($this->review_data_array['date_added'])));
		$richSnippetView->set_rating($this->review_data_array['reviews_rating']);
		$richSnippetView->set_products_name($this->review_data_array['products_name']);

		$richSnippetArray = $richSnippetView->get_review_snippet();
		$this->content_array['rich_snippet_content'] = $richSnippetArray;
	}
}