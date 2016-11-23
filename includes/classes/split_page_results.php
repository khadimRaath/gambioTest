<?php
/* --------------------------------------------------------------
   split_page_results.php 2016-07-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(split_page_results.php,v 1.14 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (split_page_results.php,v 1.6 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: split_page_results.php 1166 2005-08-21 00:52:02Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class splitPageResults_ORIGIN
{
	public $sql_query, $number_of_rows, $current_page_number, $number_of_pages, $number_of_rows_per_page;
	
	/**
	 * @var string $relPrevUrl needed for seo_tags-Smarty-Plugin (rel="prev")
	 */
	protected $relPrevUrl = '';
	
	/**
	 * @var string $relNextUrl needed for seo_tags-Smarty-Plugin (rel="next")
	 */
	protected $relNextUrl = '';
	
	// class constructor
	public function __construct($query, $page, $max_rows, $count_key = '*')
	{
		$this->sql_query = $query;
		
		if(empty($page) || (is_numeric($page) == false))
		{
			$page = 1;
		}
		$this->current_page_number = $page;
		
		$this->number_of_rows_per_page = $max_rows;
		
		$count_query = xtDBquery($query);
		$count       = xtc_db_num_rows($count_query, true);
		
		$this->number_of_rows = $count;
		@$this->number_of_pages = ceil($this->number_of_rows / $this->number_of_rows_per_page);
		
		if($this->current_page_number > $this->number_of_pages)
		{
			$this->current_page_number = $this->number_of_pages;
		}
		
		$offset = ($this->number_of_rows_per_page * ($this->current_page_number - 1));
		if($offset < 1)
		{
			$offset = 0;
		}
		
		$this->sql_query .= " LIMIT " . $offset . ", " . $this->number_of_rows_per_page;
	}
	
	// class functions
	
	// display split-page-number-links
	public function display_links($max_page_links, $parameters = '')
	{
		global $PHP_SELF, $request_type;
		
		$parameters    = str_replace('&amp;', '&', $parameters);
		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		
		if($coo_seo_boost->boost_categories && strpos_wrapper(gm_get_env_info('SCRIPT_NAME'), 'index.php') !== false)
		{
			# use boost url for splitting urls
			$t_href_link_base = $coo_seo_boost->get_current_boost_url();
			
			parse_str($parameters, $parametersArray);
			
			if(gm_get_env_info('TEMPLATE_VERSION') >= 3)
			{
				if(isset($parametersArray['cPath']))
				{
					unset($parametersArray['cPath']);
				}
				
				if(isset($parametersArray['cat']))
				{
					unset($parametersArray['cat']);
				}
			}
			
			$parameters = http_build_query($parametersArray, '', '&', PHP_QUERY_RFC3986);
		}
		elseif(strpos($PHP_SELF, 'shop.php') > -1)
		{
			$t_href_link_base = 'index.php'; // Use the default "index.php" file for the links instead of "shop.php". 
			
			parse_str($parameters, $parametersArray);
			
			// Remove the "do" GET parameter because it is not needed.
			if(isset($parametersArray['do']))
			{
				unset($parametersArray['do']);
			}
			
			if($coo_seo_boost->boost_categories && array_key_exists('filter_url', $_GET))
			{
				$t_href_link_base = $_GET['filter_url'];
				
				if(isset($parametersArray['filter_url']))
				{
					unset($parametersArray['filter_url']);
				}
				
				if(isset($parametersArray['cPath']))
				{
					unset($parametersArray['cPath']);
				}
				
				if(isset($parametersArray['cat']))
				{
					unset($parametersArray['cat']);
				}
			}
			
			$parameters = http_build_query($parametersArray, '', '&', PHP_QUERY_RFC3986);
		}
		else
		{
			# use default url for splitting urls
			$t_href_link_base = basename($PHP_SELF);
		}
		
		parse_str($parameters, $parametersArray);
		
		if(isset($parametersArray['open_cart_dropdown']))
		{
			unset($parametersArray['open_cart_dropdown']);
		}
		
		if(isset($parametersArray['no_boost']))
		{
			unset($parametersArray['no_boost']);
		}
		
		foreach($parametersArray as &$getValue)
		{
			if(is_string($getValue))
			{
				$getValue = stripslashes($getValue);
			}
		}
		
		$parameters = http_build_query($parametersArray, '', '&', PHP_QUERY_RFC3986);
		
		$display_links_string = '';
		
		if(xtc_not_null($parameters) && (substr_wrapper($parameters, -1) != '&'))
		{
			$parameters .= '&';
		}
		
		$pageParam = '';
		if($this->current_page_number - 1 !== 1)
		{
			$pageParam = 'page=' . ($this->current_page_number - 1);
		}
		
		$this->relPrevUrl = '';
		
		// previous button - not displayed on first page
		if($this->current_page_number > 1)
		{
			$this->relPrevUrl = xtc_href_link($t_href_link_base, $parameters . $pageParam, $request_type);
			$display_links_string .= '<a href="' . $this->relPrevUrl . '" class="pageResults" title=" '
			                         . PREVNEXT_TITLE_PREVIOUS_PAGE . ' ">' . PREVNEXT_BUTTON_PREV . '</a>&nbsp;&nbsp;';
		}
		
		$t_page_numbers_array = $this->get_page_numbers_array($this->current_page_number, $max_page_links,
		                                                      $this->number_of_pages);
		
		for($i = 0; $i < count($t_page_numbers_array); $i++)
		{
			$pageParam = '';
			if($t_page_numbers_array[$i]['PAGE'] != 1)
			{
				$pageParam = 'page=' . $t_page_numbers_array[$i]['PAGE'];
			}
			
			if((int)$t_page_numbers_array[$i]['PAGE'] == $this->current_page_number)
			{
				$display_links_string .= '&nbsp;<strong>' . $t_page_numbers_array[$i]['TEXT'] . '</strong>&nbsp;';
			}
			elseif($t_page_numbers_array[$i]['TEXT'] == '...')
			{
				if($i == 1)
				{
					$display_links_string .= '<a href="' . xtc_href_link($t_href_link_base, $parameters . $pageParam,
					                                                     $request_type)
					                         . '" class="pageResults" title=" '
					                         . sprintf(PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE, $max_page_links)
					                         . ' ">...</a>';
				}
				else
				{
					$display_links_string .= '<a href="' . xtc_href_link($t_href_link_base, $parameters . $pageParam,
					                                                     $request_type)
					                         . '" class="pageResults" title=" '
					                         . sprintf(PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE, $max_page_links)
					                         . ' ">...</a>&nbsp;';
				}
			}
			else
			{
				$display_links_string .= '&nbsp;<a href="' . xtc_href_link($t_href_link_base, $parameters . $pageParam,
				                                                           $request_type)
				                         . '" class="pageResults" title=" ' . sprintf(PREVNEXT_TITLE_PAGE_NO,
				                                                                      $t_page_numbers_array[$i]['PAGE'])
				                         . ' ">' . $t_page_numbers_array[$i]['TEXT'] . '</a>&nbsp;';
			}
		}
		
		$this->relNextUrl = '';
		
		// next button
		if(($this->current_page_number < $this->number_of_pages) && ($this->number_of_pages != 1))
		{
			$this->relNextUrl = xtc_href_link($t_href_link_base,
			                                  $parameters . 'page=' . ($this->current_page_number + 1), $request_type);
			
			$display_links_string .= '&nbsp;<a href="' . $this->relNextUrl . '" class="pageResults" title=" '
			                         . PREVNEXT_TITLE_NEXT_PAGE . ' ">' . PREVNEXT_BUTTON_NEXT . '</a>&nbsp;';
		}
		
		return $display_links_string;
	}
	
	
	public function get_page_numbers_array($p_current_page, $p_max_page_number, $p_max_page)
	{
		$t_pages_array = array();
		
		$t_check_sum = 1 + (($p_max_page_number - 1) * 2) + 2;
		$count_index = 0;
		
		if($t_check_sum >= $p_max_page)
		{
			for($i = 1; $i <= $p_max_page; $i++)
			{
				$t_pages_array[$i - 1]['PAGE'] = $i;
				$t_pages_array[$i - 1]['TEXT'] = $i;
			}
		}
		else
		{
			
			$t_pages_before_and_after = $p_max_page_number - 1;
			if($p_current_page - $t_pages_before_and_after > 1)
			{
				$t_pages_array[$count_index]['PAGE'] = 1;
				$t_pages_array[$count_index]['TEXT'] = 1;
			}
			if($p_current_page - $t_pages_before_and_after > 2)
			{
				$count_index                         = count($t_pages_array);
				$t_pages_array[$count_index]['PAGE'] = $p_current_page - $t_pages_before_and_after - 1;
				$t_pages_array[$count_index]['TEXT'] = '...';
			}
			for($i = 0; $i < $t_pages_before_and_after * 2 + 1; $i++)
			{
				if($p_current_page - $t_pages_before_and_after + $i < $p_max_page
				   && $p_current_page - $t_pages_before_and_after + $i > 0
				)
				{
					$count_index                         = count($t_pages_array);
					$t_pages_array[$count_index]['PAGE'] = $p_current_page - $t_pages_before_and_after + $i;
					$t_pages_array[$count_index]['TEXT'] = $p_current_page - $t_pages_before_and_after + $i;
				}
			}
			if($t_pages_array[count($t_pages_array) - 1]['PAGE'] == $p_max_page - 2)
			{
				$count_index                         = count($t_pages_array);
				$t_pages_array[$count_index]['PAGE'] = $p_max_page - 1;
				$t_pages_array[$count_index]['TEXT'] = $p_max_page - 1;
			}
			elseif($t_pages_array[count($t_pages_array) - 1]['PAGE'] < $p_max_page - 1)
			{
				$count_index                         = count($t_pages_array);
				$t_pages_array[$count_index]['PAGE'] = $p_current_page + $t_pages_before_and_after + 1;
				$t_pages_array[$count_index]['TEXT'] = '...';
			}
			$count_index                         = count($t_pages_array);
			$t_pages_array[$count_index]['PAGE'] = $p_max_page;
			$t_pages_array[$count_index]['TEXT'] = $p_max_page;
		}
		
		return $t_pages_array;
	}
	
	
	// display number of total products found
	public function display_count($text_output)
	{
		$to_num = ($this->number_of_rows_per_page * $this->current_page_number);
		if($to_num > $this->number_of_rows)
		{
			$to_num = $this->number_of_rows;
		}
		
		$from_num = ($this->number_of_rows_per_page * ($this->current_page_number - 1));
		
		if($to_num == 0)
		{
			$from_num = 0;
		}
		else
		{
			$from_num++;
		}
		
		return sprintf($text_output, $from_num, $to_num, $this->number_of_rows);
	}
	
	
	public function setPrevNextUrls()
	{
		$_SESSION['relPrevUrl'] = $this->relPrevUrl;
		$_SESSION['relNextUrl'] = $this->relNextUrl;
	}
}