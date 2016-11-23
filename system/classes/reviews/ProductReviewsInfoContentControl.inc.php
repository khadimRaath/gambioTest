<?php
/* --------------------------------------------------------------
   ProductReviewsInfoContentControl.inc.php 2014-02-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
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

MainFactory::load_class('DataProcessing');

class ProductReviewsInfoContentControl extends DataProcessing
{
	protected $review_id = 0;
	protected $language_id = 2;
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['review_id']		= array('type' => 'int');
		$this->validation_rules_array['language_id']	= array('type' => 'int');
	}
	
	public function proceed()
	{
		$t_query = "SELECT 
						rd.reviews_text, 
						r.reviews_rating, 
						r.reviews_id, 
						r.products_id, 
						r.customers_name, 
						r.date_added, 
						r.last_modified, 
						r.reviews_read, 
						p.products_id, 
						p.products_fsk18, 
						p.gm_price_status, 
						p.products_quantity, 
						pd.products_name, 
						p.products_image 
					FROM 
						".TABLE_REVIEWS." r
						LEFT JOIN ".TABLE_PRODUCTS." p on (r.products_id = p.products_id)
						LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd
							ON 
								(p.products_id = pd.products_id AND 
								pd.language_id = '" . $this->language_id . "'), 
						" . TABLE_REVIEWS_DESCRIPTION . " rd
					WHERE 
						r.reviews_id = '" . $this->review_id . "' AND 
						r.reviews_id = rd.reviews_id AND 
						p.products_status = '1'
		";
		
		$t_query = xtc_db_query($t_query);

		if(!xtc_db_num_rows($t_query))
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_REVIEWS));
			return true;
		}
		
		$t_review_data_array = xtc_db_fetch_array($t_query);

		$this->wrapped_db_perform(__FUNCTION__, TABLE_REVIEWS, array('reviews_read' => (int)$t_review_data_array['reviews_read'] + 1), 'update', 'reviews_id = ' . $t_review_data_array['reviews_id']);
		
		$coo_product_reviews_info_view = MainFactory::create_object('ProductReviewsInfoContentView');
		$coo_product_reviews_info_view->set_('review_data_array', $t_review_data_array);
		$this->v_output_buffer = $coo_product_reviews_info_view->get_html($t_review_data_array);		
		
		return true;
	}	
}
