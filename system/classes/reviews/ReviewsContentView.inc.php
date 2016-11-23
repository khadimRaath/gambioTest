<?php
/* --------------------------------------------------------------
  ReviewsContentView.inc.php 2014-07-17 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(reviews.php,v 1.48 2003/05/27); www.oscommerce.com
  (c) 2003	 nextcommerce (reviews.php,v 1.12 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: reviews.php 1238 2005-09-24 10:51:19Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

// include needed functions
require_once (DIR_FS_INC . 'xtc_date_long.inc.php');
require_once (DIR_FS_INC . 'xtc_word_count.inc.php');

class ReviewsContentView extends ContentView
{
	protected $page = 1;
	protected $language_id = 2;
	protected $coo_split_page_results;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/reviews.html');
		$this->set_flat_assigns(true);
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['page']					= array('type' => 'int');
		$this->validation_rules_array['language_id']			= array('type' => 'int');
		$this->validation_rules_array['coo_split_page_results']	= array('type' => 'object',
																		'object_type' => 'splitPageResults');
	}
			
	public function prepare_data()
	{
		$t_query = "SELECT 
						r.reviews_id,
						left(rd.reviews_text, 250) as reviews_text,
						r.reviews_rating,
						r.date_added,
						p.products_id,
						pd.products_name,
						p.products_image,
						r.customers_name
					FROM
						" . TABLE_REVIEWS . " r,
						" . TABLE_REVIEWS_DESCRIPTION . " rd,
						" . TABLE_PRODUCTS . " p,
						" . TABLE_PRODUCTS_DESCRIPTION . " pd
					WHERE 
						p.products_status = '1' AND 
						p.products_id = r.products_id AND 
						r.reviews_id = rd.reviews_id AND 
						p.products_id = pd.products_id AND
						pd.language_id = '" . $this->language_id . "' AND 
						rd.languages_id = '" . $this->language_id . "'
					ORDER BY r.reviews_id DESC";
		
		$this->coo_split_page_results = new splitPageResults($t_query, $this->page, MAX_DISPLAY_NEW_REVIEWS);

		if($this->coo_split_page_results->number_of_rows > 0)
		{
			$this->add_data();
		}
	}
	
	protected function add_data()
	{
		$this->add_navbar();
		$this->add_module_content();
	}
	
	protected function add_navbar()
	{
		$t_params = xtc_get_all_get_params(array('page', 'info', 'x', 'y'));
		$t_navbar = '
			<div>
				<div class="box_left">
					' . $this->coo_split_page_results->display_count(TEXT_DISPLAY_NUMBER_OF_REVIEWS) . '
				</div>
				<div class="box_right align_right">
					' . TEXT_RESULT_PAGE . ' ' . $this->coo_split_page_results->display_links(MAX_DISPLAY_PAGE_LINKS, $t_params) . '
				</div>
			</div>';
		$this->content_array['NAVBAR'] = $t_navbar;
	}
	
	protected function add_module_content()
	{
		$reviews_query = xtc_db_query($this->coo_split_page_results->sql_query);
		while($reviews = xtc_db_fetch_array($reviews_query))
		{
			$this->content_array['module_content'][] = array(
				'PRODUCTS_IMAGE' => DIR_WS_THUMBNAIL_IMAGES . $reviews['products_image'],
				'PRODUCTS_LINK' => xtc_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $reviews['products_id'] . '&reviews_id=' . $reviews['reviews_id']),
				'PRODUCTS_NAME' => $reviews['products_name'],
				'AUTHOR' => $reviews['customers_name'],
				'TEXT' => '(' . sprintf(TEXT_REVIEW_WORD_COUNT, xtc_word_count($reviews['reviews_text'], ' ')) . ')<br />' . htmlspecialchars_wrapper($reviews['reviews_text']) . '..',
				'RATING' => xtc_image('templates/' . CURRENT_TEMPLATE . '/img/stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])),
				'REVIEW_TEXT' => htmlspecialchars_wrapper($reviews['reviews_text']),
			);
		}
	}
}