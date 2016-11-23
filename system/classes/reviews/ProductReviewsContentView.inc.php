<?php
/* --------------------------------------------------------------
   ProductReviewsContentView.inc.php 2014-11-10 gambio
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
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_reviews.php 1243 2005-09-25 09:33:02Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once (DIR_FS_INC.'xtc_row_number_format.inc.php');
require_once (DIR_FS_INC.'xtc_date_short.inc.php');

/**
 * Class ProductReviewsContentView
 */
class ProductReviewsContentView extends ContentView
{
	protected $product;
	protected $reviewsArray = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/products_reviews.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		$this->_assignUrls();
		
		if($this->product->getReviewsCount() > 0)
		{
			$this->_assignReviews();
			$this->_assignSnippet();

			$this->_assignDeprecated();
		}
	}


	/**
	 * @return array
	 */
	protected function _buildSnippetArray()
	{
		$snippetArray = array();

		/* @var GoogleRichSnippetContentView $view */
		$view = MainFactory::create_object('GoogleRichSnippetContentView');
		
		foreach($this->reviewsArray as $t_review)
		{
			$view->set_fsk18((boolean)$this->product->data['products_fsk18']);
			$view->set_price_status($this->product->data['gm_price_status']);
			$view->set_quantity($this->product->data['products_quantity']);
			$view->set_review_date_created($t_review['DATE_CLEAN']);
			$view->set_rating($t_review['RATING_CLEAN']);
			$view->set_products_name($this->product->data['products_name']);

			$snippetArray[] = $view->get_review_snippet();
		}

		return $snippetArray;
	}


	protected function _assignDeprecated()
	{
		$this->set_content_data('BUTTON_WRITE', '<a href="' . xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,
																			xtc_product_link($this->product->data['products_id'],
																							 $this->product->data['products_name'])) .
												'">' .
												xtc_image_button('button_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) .
												'</a>', 2);
	}


	protected function _assignUrls()
	{
		$this->set_content_data('BUTTON_LINK', xtc_href_link(FILENAME_PRODUCT_REVIEWS_WRITE,
															 xtc_product_link($this->product->data['products_id'],
																			  $this->product->data['products_name'])));
	}


	protected function _assignReviews()
	{
		$this->reviewsArray = $this->product->getReviews(PRODUCT_REVIEWS_VIEW);
		$this->set_content_data('module_content', $this->reviewsArray);
	}


	protected function _assignSnippet()
	{
		$t_rich_snippet_array = $this->_buildSnippetArray();
		$this->set_content_data('rich_snippet_content', $t_rich_snippet_array);
	}


	/**
	 * @param product $product
	 */
	public function setProduct(product $product)
	{
		$this->product = $product;
	}


	/**
	 * @return product
	 */
	public function getProduct()
	{
		return $this->product;
	}
}