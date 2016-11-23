<?php
/* --------------------------------------------------------------
   GoogleRichSnippetContentView.inc.php 2016-07-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GoogleRichSnippetContentView
 */
class GoogleRichSnippetContentView extends ContentView
{
	protected $active;
	protected $fsk18;
	protected $price_status;
	protected $quantity;
	protected $date_available;
	protected $price;
	protected $currency;
	protected $products_name;
	protected $breadcrumb_array;
	protected $breadcrumb_separator;
	protected $review_date_created;
	protected $rating;
	protected $rating_count;

	
	public function __construct()
	{
		parent::__construct();
		
		$this->active = true;
	}


	/**
	 * @return bool
	 */
	protected function is_rich_snippet_active()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('fsk18', 'quantity', 'price_status', 'active'));
		
		if(empty($t_uninitialized_array))
		{
			if($this->fsk18 == false 
					&& (STOCK_ALLOW_CHECKOUT == 'true' || ($this->quantity > 0) && STOCK_ALLOW_CHECKOUT == 'false') 
					&& $this->price_status == '0'
					&& $this->active == true)
			{
				return true;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
		
		return false;
	}


	/**
	 * @return array
	 */
	public function get_product_snippet()
	{
		$t_rich_snippet_array = array();
		$t_rich_snippet_array['product_itemscope'] = '';
		$t_rich_snippet_array['product_itemprop_image'] = '';
		$t_rich_snippet_array['product_itemprop_name_start'] = '';
		$t_rich_snippet_array['product_itemprop_model_start'] = '';
		$t_rich_snippet_array['product_itemprop_model_end'] = '';
		$t_rich_snippet_array['product_itemprop_model_end'] = '';
		$t_rich_snippet_array['product_itemprop_description_start'] = '';
		$t_rich_snippet_array['product_itemprop_description_end'] = '';
		$t_rich_snippet_array['product_itemprop_offers'] = '';
		
		$t_rich_snippet_array['aggregate_rating_itemscope'] = '';
		$t_rich_snippet_array['aggregate_rating_itemprop_ratingValue'] = '';
		$t_rich_snippet_array['aggregate_rating_itemprop_ratingCount'] = '';

		$t_rich_snippet_array['offer_itemscope'] = '';
		$t_rich_snippet_array['offer_itemprop_price_start'] = '';
		$t_rich_snippet_array['offer_itemprop_price_end'] = '';
		$t_rich_snippet_array['offer_itemprop_price_currency'] = '';

		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('price', 'currency'));
		
		if(empty($t_uninitialized_array))
		{
			if($this->is_rich_snippet_active())
			{
	//			if(empty($this->date_available) == false && $this->date_available > date('Y-m-d H:i:s'))
	//			{
	//				$t_rich_snippet_array['availability'] = 'preorder';
	//			}
	//			elseif($this->quantity > 0)
	//			{
	//				$t_rich_snippet_array['availability'] = 'in_stock';
	//			}
	//			elseif($this->quantity <= 0 )
	//			{
	//				$t_rich_snippet_array['availability'] = 'out_of_stock';
	//			}


				$t_rich_snippet_array['product_itemscope'] = ' itemscope="itemscope" itemtype="http://schema.org/Product"';
				$t_rich_snippet_array['product_itemprop_image'] = ' itemprop="image"';
				$t_rich_snippet_array['product_itemprop_name_start'] = '<span itemprop="name">';
				$t_rich_snippet_array['product_itemprop_name_end'] = '</span>';
				$t_rich_snippet_array['product_itemprop_model_start'] = '<span itemprop="model">';
				$t_rich_snippet_array['product_itemprop_model_end'] = '</span>';
				$t_rich_snippet_array['product_itemprop_description_start'] = '<div itemprop="description">';
				$t_rich_snippet_array['product_itemprop_description_end'] = '</div>';
				$t_rich_snippet_array['product_itemprop_offers'] = ' itemprop="offers"';
				
				if($this->rating_count > 1)
				{
					$t_rich_snippet_array['product_itemprop_aggregate_rating'] = ' itemprop="aggregateRating"';
					$t_rich_snippet_array['aggregate_rating_itemscope'] = ' itemscope="itemscope" itemtype="http://schema.org/AggregateRating"';
					$t_rich_snippet_array['aggregate_rating_itemprop_ratingValue'] = ' itemprop="ratingValue"';
					$t_rich_snippet_array['aggregate_rating_itemprop_ratingCount'] = ' itemprop="ratingCount"';
				}

				$t_rich_snippet_array['offer_itemscope'] = ' itemscope="itemscope" itemtype="http://schema.org/Offer"';
				$t_rich_snippet_array['offer_itemprop_price_start'] = '<span itemprop="price" content="' .  number_format($this->price, 2, '.', '') . '">';
				$t_rich_snippet_array['offer_itemprop_price_end'] = '</span>';
				$t_rich_snippet_array['offer_itemprop_price_currency'] = '<meta itemprop="priceCurrency" content="' . $this->currency .'" />';
				$t_rich_snippet_array['offer_itemprop_in_stock'] = '<meta itemprop="availability" content="http://schema.org/InStock" />';
				$t_rich_snippet_array['offer_itemprop_out_of_stock'] = '<meta itemprop="availability" content="http://schema.org/OutOfStock" />';
				$t_rich_snippet_array['offer_itemprop_item_condition'] = '<meta itemprop="itemCondition" content="http://schema.org/NewCondition" />';
			}
			
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
		
		return $t_rich_snippet_array;
	}


	/**
	 * @return string
	 */
	function get_breadcrumb_snippet()
	{
		$t_output_html = '';
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('breadcrumb_array', 'breadcrumb_separator', 'active'));
		
		if(empty($t_uninitialized_array))
		{
			$t_items_array = array();
			$t_breadcrumb_array = $this->breadcrumb_array;

			if($this->active == true)
			{
				$t_output_html .= '<div id="breadcrumb_navi" itemscope itemtype="http://schema.org/BreadcrumbList">';

				foreach($t_breadcrumb_array as $key => $t_value_array)
				{
					if(isset($t_value_array['link']) && xtc_not_null($t_value_array['link']))
					{
						$t_items_array[] = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
												<a href="' . $t_value_array['link'] . '" class="headerNavigation" itemprop="item">
													<span itemprop="name">' . $t_value_array['title'] . '</span>
												</a>
												<meta itemprop="position" content="' . ($key + 1) . '" />
											</span>';
					}
					else
					{
						$t_items_array[] = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
													<span itemprop="name">' . $t_value_array['title'] . '</span>
													<meta itemprop="position" content="' . ($key + 1) . '" />
											</span>';
					}
				}

				$t_output_html .= implode($this->breadcrumb_separator, $t_items_array);

				$t_output_html .= '</div>';
			}
			else
			{
				$t_output_html .= '<div id="breadcrumb_navi">';

				foreach($t_breadcrumb_array as $t_value_array)
				{
					if(isset($t_value_array['link']) && xtc_not_null($t_value_array['link']))
					{
						$t_items_array[] = '<a href="' . $t_value_array['link'] . '" class="headerNavigation">
												<span>' . $t_value_array['title'] . '</span>
											</a>';
					}
					else
					{
						$t_items_array[] = '<span>' . $t_value_array['title'] . '</span>';
					}
				}

				$t_output_html .= implode($this->breadcrumb_separator, $t_items_array);

				$t_output_html .= '</div>';
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
		
		return $t_output_html;
	}



	/**
	 * @return array
	 * @link https://developers.google.com/structured-data/rich-snippets/reviews
	 */
	public function get_review_snippet()
	{
		$t_rich_snippet_array = array();
		$t_rich_snippet_array['product_itemprop_reviews'] = '';
		$t_rich_snippet_array['review_itemscope'] = '';
		$t_rich_snippet_array['review_itemprop_about'] = '';
		$t_rich_snippet_array['review_itemprop_reviewBody'] = '';
		$t_rich_snippet_array['review_itemprop_author'] = '';
		$t_rich_snippet_array['author_itemscope'] = '';
		$t_rich_snippet_array['author_itemprop_name'] = '';
		$t_rich_snippet_array['review_itemprop_datePublished'] = '';
		$t_rich_snippet_array['review_itemprop_reviewRating'] = '';
		$t_rich_snippet_array['rating_itemscope'] = '';
		$t_rich_snippet_array['rating_itemprop_ratingValue'] = '';
		
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('products_name', 'review_date_created', 'rating'));
		
		if(empty($t_uninitialized_array))
		{
			if($this->is_rich_snippet_active())
			{   
				$t_rich_snippet_array['product_itemprop_reviews'] = ' itemprop="review"';

				$t_rich_snippet_array['review_itemscope'] = ' itemscope="itemscope" itemtype="http://schema.org/Review"';
				$t_rich_snippet_array['review_itemprop_about'] = ' <meta itemprop="about" content="' . $this->products_name . '">';
				$t_rich_snippet_array['review_itemprop_reviewBody'] = ' itemprop="reviewBody"';
				$t_rich_snippet_array['review_itemprop_author'] = ' itemprop="author"';
				
				$t_rich_snippet_array['author_itemscope'] = ' itemscope="itemscope" itemtype="http://schema.org/Person"';
				$t_rich_snippet_array['author_itemprop_name'] = ' itemprop="name"';

				$t_rich_snippet_array['review_itemprop_datePublished'] = '<meta itemprop="datePublished" content="' . $this->review_date_created . '">';
				$t_rich_snippet_array['review_itemprop_reviewRating'] = ' itemprop="reviewRating"';

				$t_rich_snippet_array['rating_itemscope'] = ' itemscope="itemscope" itemtype="http://schema.org/Rating"';
				$t_rich_snippet_array['rating_itemprop_ratingValue'] = '<meta itemprop="ratingValue" content="' . $this->rating . '">';
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
		
		return $t_rich_snippet_array;
	}


	/**
	 * @param boolean $p_active
	 */
	public function set_active($p_active)
	{
		$this->active = (bool)$p_active;
	}


	/**
	 * @return boolean
	 */
	public function get_active()
	{
		return $this->active;
	}


	/**
	 * @param array $p_breadcrumb_array
	 */
	public function set_breadcrumb_array(array $p_breadcrumb_array)
	{
		$this->breadcrumb_array = $p_breadcrumb_array;
	}


	/**
	 * @return array
	 */
	public function get_breadcrumb_array()
	{
		return $this->breadcrumb_array;
	}


	/**
	 * @param string $p_breadcrumb_separator
	 */
	public function set_breadcrumb_separator($p_breadcrumb_separator)
	{
		$this->breadcrumb_separator = (string)$p_breadcrumb_separator;
	}


	/**
	 * @return string
	 */
	public function get_breadcrumb_separator()
	{
		return $this->breadcrumb_separator;
	}


	/**
	 * @param string $p_currency
	 */
	public function set_currency($p_currency)
	{
		$this->currency = (string)$p_currency;
	}


	/**
	 * @return string
	 */
	public function get_currency()
	{
		return $this->currency;
	}


	/**
	 * @param string $p_date_available
	 */
	public function set_date_available($p_date_available)
	{
		$this->date_available = (string)$p_date_available;
	}


	/**
	 * @return string
	 */
	public function get_date_available()
	{
		return $this->date_available;
	}


	/**
	 * @param boolean $p_fsk18
	 */
	public function set_fsk18($p_fsk18)
	{
		$this->fsk18 = (bool)$p_fsk18;
	}


	/**
	 * @return boolean
	 */
	public function get_fsk18()
	{
		return $this->fsk18;
	}


	/**
	 * @param double $p_price
	 */
	public function set_price($p_price)
	{
		$this->price = (double)$p_price;
	}


	/**
	 * @return double
	 */
	public function get_price()
	{
		return $this->price;
	}


	/**
	 * @param int $p_price_status
	 */
	public function set_price_status($p_price_status)
	{
		$this->price_status = (int)$p_price_status;
	}


	/**
	 * @return int
	 */
	public function get_price_status()
	{
		return $this->price_status;
	}


	/**
	 * @param string $p_products_name
	 */
	public function set_products_name($p_products_name)
	{
		$this->products_name = (string)$p_products_name;
	}


	/**
	 * @return string
	 */
	public function get_products_name()
	{
		return $this->products_name;
	}


	/**
	 * @param double $p_quantity
	 */
	public function set_quantity($p_quantity)
	{
		$this->quantity = (double)$p_quantity;
	}


	/**
	 * @return double
	 */
	public function get_quantity()
	{
		return $this->quantity;
	}


	/**
	 * @param int $p_rating
	 */
	public function set_rating($p_rating)
	{
		$this->rating = (int)$p_rating;
	}


	/**
	 * @return int
	 */
	public function get_rating()
	{
		return $this->rating;
	}
	
	
	/**
	 * @param int $p_rating_count
	 */
	public function set_rating_count($p_rating_count)
	{
		$this->rating_count = (int)$p_rating_count;
	}
	
	
	/**
	 * @return int
	 */
	public function get_rating_count()
	{
		return $this->rating_count;
	}


	/**
	 * @param string $p_review_date_created
	 */
	public function set_review_date_created($p_review_date_created)
	{
		$this->review_date_created = (string)$p_review_date_created;
	}


	/**
	 * @return string
	 */
	public function get_review_date_created()
	{
		return $this->review_date_created;
	}
}