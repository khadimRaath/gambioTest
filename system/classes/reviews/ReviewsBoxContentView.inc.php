<?php
/* --------------------------------------------------------------
  ReviewsBoxContentView.inc.php 2015-05-29 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(reviews.php,v 1.36 2003/02/12); www.oscommerce.com
  (c) 2003	 nextcommerce (reviews.php,v 1.9 2003/08/17 22:40:08); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: reviews.php 1262 2005-09-30 10:00:32Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once(DIR_FS_INC . 'xtc_random_select.inc.php');
require_once(DIR_FS_INC . 'xtc_break_string.inc.php');

class ReviewsBoxContentView extends ContentView
{
	protected $coo_product;
	protected $random_review_result;
	protected $language_id;
	protected $style_edit_mode;
	protected $customers_fsk18_display;

	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('boxes/box_reviews.html');
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['coo_product']				= array('type' => 'object',
																			'object_type' => 'product');
		$this->validation_rules_array['random_review_result']		= array('type' => 'array');
		$this->validation_rules_array['language_id']				= array('type' => 'int');
		$this->validation_rules_array['style_edit_mode']			= array('type' => 'string');
		$this->validation_rules_array['customers_fsk18_display']	= array('type' => 'int');
	}
	
	public function prepare_data()
	{
		$this->build_html = false;

		//fsk18 lock
		$t_fsk_lock = '';
		if($this->customers_fsk18_display == 0)
		{
			$t_fsk_lock = ' AND p.products_fsk18 != 1';
		}
		$t_query = "SELECT 
						r.reviews_id, 
						r.reviews_rating, 
						p.products_id, 
						p.products_image, 
						pd.products_name 
					FROM 
						" . TABLE_REVIEWS . " r, 
						" . TABLE_REVIEWS_DESCRIPTION . " rd, 
						" . TABLE_PRODUCTS . " p, 
						" . TABLE_PRODUCTS_DESCRIPTION . " pd 
					WHERE
						p.products_status = '1' AND 
						p.products_id = r.products_id 
						" . $t_fsk_lock . " AND 
						r.reviews_id = rd.reviews_id AND
						rd.languages_id = '" . $this->language_id . "' AND
						p.products_id = pd.products_id AND
						pd.language_id = '" . $this->language_id . "'";
		if(isset($this->coo_product) && $this->coo_product->isProduct())
		{
			$t_query .= " AND p.products_id = '" . $this->coo_product->data['products_id'] . "'";
		}
		$t_query .= " ORDER BY r.reviews_id DESC LIMIT " . MAX_RANDOM_SELECT_REVIEWS;
		$this->random_review_result = xtc_random_select($t_query);

		if($this->random_review_result)
		{
			$this->add_review_data();
		}
		elseif(isset($this->coo_product) && $this->coo_product->isProduct())
		{
			$this->add_write_a_review_data();
		}

		if($this->style_edit_mode == 'edit')
		{
			$this->build_html = true;
		}
	}
	
	protected function add_review_data()
	{
		if(!isset($this->random_review_result['reviews_rating']))
		{
			$this->random_review_result['reviews_rating'] = 0;
		}
		
		$t_reviews_text = $this->get_reviews_text();

		$this->content_array['TEXT'] = $t_reviews_text;
		$this->content_array['REVIEW_LINK'] = xtc_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $this->random_review_result['products_id'] . '&amp;reviews_id=' . $this->random_review_result['reviews_id']);
		$this->content_array['IMAGE'] = xtc_image(DIR_WS_THUMBNAIL_IMAGES . $this->random_review_result['products_image'], $this->random_review_result['products_name']);
		$this->content_array['STARS'] = xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $this->random_review_result['reviews_rating'] . '.gif', sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $this->random_review_result['reviews_rating']));
		$this->content_array['REVIEWS_LINK'] = xtc_href_link(FILENAME_REVIEWS);

		$this->build_html = true;
	}
	
	protected function get_reviews_text()
	{
		$t_query = "SELECT 
						reviews_text 
					FROM
						" . TABLE_REVIEWS_DESCRIPTION . " 
					WHERE 
						reviews_id = '" . $this->random_review_result['reviews_id'] . "' AND 
						languages_id = '" . $this->language_id . "'";
		$t_result = xtDBquery($t_query);
		$t_review = xtc_db_fetch_array($t_result, true);

		return strip_tags($t_review['reviews_text']);
	}
	
	protected function add_write_a_review_data()
	{
		$t_content = '<a href="' . xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, xtc_product_link($this->coo_product->data['products_id'], $this->coo_product->data['products_name'])) . '">' . xtc_image('templates/' . CURRENT_TEMPLATE . '/img/box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a><br />
					<a href="' . xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, xtc_product_link($this->coo_product->data['products_id'], $this->coo_product->data['products_name'])) . '">' . BOX_REVIEWS_WRITE_REVIEW . '</a>';

		$this->content_array['WRITE_IMG'] = 'img/box_write_review.gif';
		$this->content_array['WRITE_IMG_ALT'] = IMAGE_BUTTON_WRITE_REVIEW;
		$this->content_array['WRITE_URL'] = xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, xtc_product_link($this->coo_product->data['products_id'], $this->coo_product->data['products_name']));
		$this->content_array['WRITE_LINK_TEXT'] = BOX_REVIEWS_WRITE_REVIEW;
		$this->content_array['REVIEWS_LINK'] = xtc_href_link(FILENAME_REVIEWS);

		$this->content_array['WRITE'] = $t_content;
		$this->build_html = true;
	}
}