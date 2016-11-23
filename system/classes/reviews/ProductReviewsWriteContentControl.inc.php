<?php
/* --------------------------------------------------------------
   ProductReviewsWriteContentControl.inc.php 2016-08-25
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

MainFactory::load_class('DataProcessing');

class ProductReviewsWriteContentControl extends DataProcessing
{
	protected $gmSEOBoost;
	protected $product;
	protected $customerId;
	
	
	public function __construct($product, $customerId)
	{
		$this->product = $product;
		$this->customerId = $customerId;
	}

	public function proceed()
	{
		$languagesId = $_SESSION['languages_id'];

		$coo_captcha = MainFactory::create_object('Captcha');
		$t_error_text = '';
		$t_error = false;

		if (isset($this->v_data_array['GET']['action'])
				&& $this->v_data_array['GET']['action'] == 'process'
				&& $coo_captcha->is_valid($this->v_data_array['POST'], 'GM_REVIEWS_VVCODE'))
		{
			if (is_object($this->product) && $this->product->isProduct())
			{ // We got to the process but it is an illegal product, don't write
				
				if(gm_get_conf('GM_CHECK_PRIVACY_REVIEWS') === '1'
				   && gm_get_conf('PRIVACY_CHECKBOX_REVIEWS') === '1'
				   && (!isset($this->v_data_array['POST']['privacy_accepted'])
				       || $this->v_data_array['POST']['privacy_accepted'] !== '1')
				)
				{
					$t_error = true;
					$t_error_text = ENTRY_PRIVACY_ERROR;
				}
				else
				{
					if(strlen_wrapper($this->v_data_array['POST']['review']) >= REVIEW_TEXT_MIN_LENGTH)
					{
						$rating = (int)$this->v_data_array['POST']['rating'];
						if ($rating > 0 && $rating < 6) {
							$t_result = xtc_db_query("SELECT
													customers_firstname, 
													customers_lastname 
												FROM 
													" . TABLE_CUSTOMERS . " 
												WHERE 
													customers_id = '" . (int)$this->customerId . "'
						");
							$t_customer_values_array = xtc_db_fetch_array($t_result);
							
							if($t_customer_values_array['customers_lastname'] == '')
							{
								$t_customer_values_array['customers_lastname'] = TEXT_GUEST;
							}
							
							$t_reviews_array = array(
								'products_id' => $this->product->data['products_id'],
								'customers_id' => (int)$this->customerId,
								'customers_name' => gm_prepare_string($t_customer_values_array['customers_firstname']) . ' ' . gm_prepare_string($t_customer_values_array['customers_lastname']),
								'reviews_rating' => gm_prepare_string($rating),
								'date_added' => 'now()'
							);
							$this->wrapped_db_perform(__FUNCTION__, TABLE_REVIEWS, $t_reviews_array);
							
							$t_insert_id = xtc_db_insert_id();
							
							$t_reviews_description_array = array(
								'reviews_id' => $t_insert_id,
								'languages_id' => (int)$languagesId,
								'reviews_text' => gm_prepare_string($this->v_data_array['POST']['review'])
							);
							$this->wrapped_db_perform(__FUNCTION__, TABLE_REVIEWS_DESCRIPTION, $t_reviews_description_array);
						}
						else
						{
							die('Invalid rating value');
						}
					}
					else
					{
						$t_error = true;
						$t_error_text = sprintf(GM_REVIEWS_TOO_SHORT,REVIEW_TEXT_MIN_LENGTH);
					}
				}
			}

			if ($t_error != true) {
				if ($this->gmSEOBoost->boost_products)
				{
					$productLink = xtc_href_link(
						$this->gmSEOBoost->get_boosted_product_url(
							$this->product->data['products_id'],
							$this->product->data['products_name']
						)
					);
				}
				else
				{
					$productLink = xtc_href_link(
						FILENAME_PRODUCT_INFO,
						xtc_product_link(
							$this->product->data['products_id'],
							$this->product->data['products_name']
						)
					);
				}
				$this->set_redirect_url($productLink);
			}
		}
		elseif(isset($this->v_data_array['GET']['action']) && !$coo_captcha->is_valid($this->v_data_array['POST'], 'GM_REVIEWS_VVCODE'))
		{
			$t_error_text = GM_REVIEWS_WRONG_CODE;
		}

        $customer_info = array();

        $customer_info_query = xtc_db_query("SELECT
												customers_firstname,
												customers_lastname
											FROM
												" . TABLE_CUSTOMERS . "
											WHERE
												customers_id = '" . (int)$this->customerId . "'");
        if(xtc_db_num_rows($customer_info_query) > 0)
        {
            $customer_info = xtc_db_fetch_array($customer_info_query);
        }

		$t_captcha_html = $coo_captcha->get_html();

		$coo_product_reviews_write_view = MainFactory::create_object('ProductReviewsWriteContentView');
		$coo_product_reviews_write_view->set_('coo_product', $this->product);
		$coo_product_reviews_write_view->set_('customer_info', $customer_info);
		if(isset($this->v_data_array['POST']['review']))
		{
			$coo_product_reviews_write_view->set_('review_message', $this->v_data_array['POST']['review']);
		}
		if(isset($this->v_data_array['POST']['rating']))
		{
			$coo_product_reviews_write_view->set_('rating', $this->v_data_array['POST']['rating']);
		}
		$coo_product_reviews_write_view->set_('captcha_html', $t_captcha_html);
		$coo_product_reviews_write_view->set_('privacy_accepted', (isset($this->v_data_array['POST']['privacy_accepted']) ? '1' : '0'));
		$coo_product_reviews_write_view->set_('error_text', $t_error_text);
		
		if(REVIEW_TEXT_MIN_LENGTH > 0)
		{
			$coo_product_reviews_write_view->set_('reviews_min_length', REVIEW_TEXT_MIN_LENGTH);
		}
		
		$this->v_output_buffer = $coo_product_reviews_write_view->get_html();

		return true;
	}
}