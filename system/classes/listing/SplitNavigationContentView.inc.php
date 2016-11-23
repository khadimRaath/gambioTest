<?php
/* --------------------------------------------------------------
   SplitNavigationContentView.inc.php 2016-06-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com
   (c) 2003	 nextcommerce www.nextcommerce.org

   XTC-NEWSLETTER_RECIPIENTS RC1 - Contribution for XT-Commerce http://www.xt-commerce.com
   by Matthias Hinsche http://www.gamesempire.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class SplitNavigationContentView extends ContentView
{
	protected $coo_split_page_results;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/gm_navigation.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_split_page_results'));
		if(empty($t_uninitialized_array))
		{
			$this->content_array['LEFT']  = $this->coo_split_page_results->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_NEW);
			$this->content_array['RIGHT'] = TEXT_RESULT_PAGE . ' '
			                                . $this->coo_split_page_results->display_links(MAX_DISPLAY_PAGE_LINKS, 
			                                                                              xtc_get_all_get_params(
				                                                                              array ('page', 
				                                                                                     'info', 
				                                                                                     'x', 
				                                                                                     'y', 
				                                                                                     'language', 
				                                                                                     'currency', 
				                                                                                     'gm_boosted_category',
				                                                                                     'gm_boosted_content',
				                                                                                     'gm_boosted_product')
			                                                                              ));
			$this->coo_split_page_results->setPrevNextUrls();
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
	
	protected function set_validation_rules()
	{
		// SET VALIDATION RULES
		$this->validation_rules_array['coo_split_page_results']	= array('type' => 'object',
																		'object_type' => 'splitPageResults');
	}
}