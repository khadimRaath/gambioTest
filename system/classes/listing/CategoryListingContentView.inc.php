<?php
/* --------------------------------------------------------------
   CategoryListingContentView.inc.php 2016-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
  (c) 2003  nextcommerce (default.php,v 1.11 2003/08/22); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: default.php 1292 2005-10-07 16:10:55Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contributions:
  Enable_Disable_Categories 1.3        Autor: Mikel Williams | mikel@ladykatcostumes.com
  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs...by=date#dirlist

  Released under the GNU General Public License
  ---------------------------------------------------------------------------------------*/


class CategoryListingContentView extends ContentView
{
	protected $currentCategoryId;
	protected $customerStatusId;
	protected $filterManager;
	protected $languageId;
	
	protected $categoryArray = array();
	protected $subcategoriesArray = array();
	
	public function __construct($p_template = 'default')
	{
		parent::__construct();
		$filepath = DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/';

		// get default template
		$c_template = $this->get_default_template($filepath, $p_template);

		$this->set_content_template('module/categorie_listing/' . $c_template);
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		$this->_buildCategoryArray();

		$this->_assignFilter();

		if($this->categoryArray['show_sub_categories'] == 1 || MOBILE_ACTIVE == 'true')
		{
			$this->_buildSubcategoriesArray();
			$this->_defineSubcategoriesCountConstant();
			$this->_assignCategoryData();
			$this->_assignSubcategoriesData();
		}
	}
	
	
	public function getFilterSelectionHtml()
	{
		if(isset($this->categoryArray['show_sub_products']) && $this->categoryArray['show_sub_products'] == 1 &&
		   $this->filterManager->is_active() == false
		)
		{
			$this->filterManager->reset();
			$this->filterManager->set_categories_id((int)$this->currentCategoryId);
			$this->filterManager->set_price_range_start(0);
			$this->filterManager->set_active(true);
		}
		
		$t_feature_value_group_array = $this->filterManager->get_feature_value_group_array();
		
		# transfer feature_value_groups to product finder
		$coo_finder = MainFactory::create_object('IndexFeatureProductFinder');
		
		foreach($t_feature_value_group_array as $t_feature_value_group)
		{
			$coo_finder->add_feature_value_group($t_feature_value_group);
		}
		
		$coo_filter_selection_content_view = MainFactory::create_object('FilterSelectionContentView');
		$coo_filter_selection_content_view->set_('feature_value_group_array', $t_feature_value_group_array);
		$coo_filter_selection_content_view->set_('language_id', $this->languageId);
		
		return $coo_filter_selection_content_view->get_html();
	}
	

	protected function _buildQuery()
	{
		if(GROUP_CHECK == 'true')
		{
			$groupCheck = " AND c.group_permission_" . (int)$this->customerStatusId . " = 1 ";
		}
		
		$query = "SELECT
								cd.categories_description,
								cd.categories_name,
								cd.categories_heading_title,
								cd.gm_alt_text,
								c.show_sub_categories,
								c.show_sub_categories_images,
								c.show_sub_categories_names,
								c.show_sub_products,
								c.categories_template,
								c.view_mode_tiled,
								c.categories_image 
							FROM 
								" . TABLE_CATEGORIES . " c, 
								" . TABLE_CATEGORIES_DESCRIPTION . " cd
							WHERE 
								c.categories_id = '" . (int)$this->currentCategoryId . "' AND
								cd.categories_id = c.categories_id
								" . $groupCheck . " AND
								cd.language_id = '" . (int)$this->languageId . "'";
		
		return $query;
	}


	protected function _buildCategoryArray()
	{
		$result = xtc_db_query($this->_buildQuery());
		
		if(xtc_db_num_rows($result))
		{
			$this->categoryArray = xtc_db_fetch_array($result);
		}
	}


	protected function _assignFilter()
	{
		$this->set_content_data('FILTER_SELECTION', $this->getFilterSelectionHtml());
	}


	/**
	 * @return string
	 */
	protected function _buildSubcategoriesQuery()
	{
		if(GROUP_CHECK == 'true')
		{
			$groupCheck = " AND c.group_permission_" . (int)$this->customerStatusId . " = 1 ";
		}
		
		$query = "SELECT
						cd.categories_description,
						c.categories_id,
						cd.categories_name,
						cd.gm_alt_text,
						cd.categories_heading_title,
						c.categories_image,
						c.parent_id 
					FROM 
						" . TABLE_CATEGORIES . " c, 
						" . TABLE_CATEGORIES_DESCRIPTION . " cd
					WHERE 
						c.categories_status = '1' AND
						c.parent_id = '" . (int)$this->currentCategoryId . "' AND
						c.categories_id = cd.categories_id
						" . $groupCheck . " AND
						cd.language_id = '" . (int)$this->languageId . "'
					ORDER BY 
						sort_order, 
						cd.categories_name";

		return $query;
	}


	/**
	 * @param $categoryArray
	 *
	 * @return string
	 */
	protected function _buildImageUrl(array $categoryArray)
	{
		$image = '';
		if($categoryArray['categories_image'] != '')
		{
			$image = DIR_WS_IMAGES . 'categories/' . $categoryArray['categories_image'];

			return $image;
		}

		return $image;
	}


	/**
	 * @param $categoryArray
	 *
	 * @return string
	 */
	protected function _buildCategoryUrl($categoryArray)
	{
		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		
		if($coo_seo_boost->boost_categories)
		{
			$url = $coo_seo_boost->get_boosted_category_url($categoryArray['categories_id']);
		}
		else
		{
			$categoryLinkParams = xtc_category_link($categoryArray['categories_id'], $categoryArray['categories_name']);
			$url                = xtc_href_link(FILENAME_DEFAULT, $categoryLinkParams);
		}

		return $url;
	}


	/**
	 * @return string
	 */
	protected function _buildSubcategoriesArray()
	{
		$categories_query = $this->_buildSubcategoriesQuery();
		$result           = xtc_db_query($categories_query);

		while($row = xtc_db_fetch_array($result))
		{
			$image = $this->_buildImageUrl($row);
			$url   = $this->_buildCategoryUrl($row);

			$this->subcategoriesArray[] = array('CATEGORIES_NAME'          => $row['categories_name'],
												'CATEGORIES_ALT_TEXT'      => $row['gm_alt_text'],
												'CATEGORIES_HEADING_TITLE' => $row['categories_heading_title'],
												'CATEGORIES_IMAGE'         => $image, 'CATEGORIES_LINK' => $url,
												'CATEGORIES_DESCRIPTION'   => $row['categories_description']
			);

		}

		return $image;
	}


	protected function _defineSubcategoriesCountConstant()
	{
		define('GM_CAT_COUNT', count($this->subcategoriesArray));
	}


	protected function _assignCategoryData()
	{
		if(count($this->subcategoriesArray) > 0)
		{
			if(MAX_DISPLAY_CATEGORIES_PER_ROW > count($this->subcategoriesArray))
			{
				$this->set_content_data('GM_LI_WIDTH', 100 / count($this->subcategoriesArray) - 2);
			}
			else
			{
				$this->set_content_data('GM_LI_WIDTH', 100 / MAX_DISPLAY_CATEGORIES_PER_ROW - 2);
			}

			$image = $this->_buildImageUrl($this->categoryArray);
			
			$this->set_content_data('CATEGORIES_NAME', $this->categoryArray['categories_name']);
			$this->set_content_data('CATEGORIES_HEADING_TITLE', $this->categoryArray['categories_heading_title']);

			$this->set_content_data('CATEGORIES_IMAGE', $image);
			$this->set_content_data('CATEGORIES_ALT_TEXT', $this->categoryArray['gm_alt_text']);
			$this->set_content_data('CATEGORIES_DESCRIPTION', $this->categoryArray['categories_description']);

			$this->set_content_data('SHOW_SUB_CATEGORIES_IMAGES', $this->categoryArray['show_sub_categories_images']);
			$this->set_content_data('SHOW_SUB_CATEGORIES_NAMES', $this->categoryArray['show_sub_categories_names']);
		}
	}


	protected function _assignSubcategoriesData()
	{
		$this->set_content_data('module_content', $this->subcategoriesArray);
	}


	/**
	 * @param int $p_currentCategoryId
	 */
	public function setCurrentCategoryId($p_currentCategoryId)
	{
		$this->currentCategoryId = (int)$p_currentCategoryId;
	}


	/**
	 * @return int
	 */
	public function getCurrentCategoryId()
	{
		return $this->currentCategoryId;
	}


	/**
	 * @param int $p_customerStatusId
	 */
	public function setCustomerStatusId($p_customerStatusId)
	{
		$this->customerStatusId = (int)$p_customerStatusId;
	}


	/**
	 * @return int
	 */
	public function getCustomerStatusId()
	{
		return $this->customerStatusId;
	}


	/**
	 * @param FilterManager $filterManager
	 */
	public function setFilterManager(FilterManager $filterManager)
	{
		$this->filterManager = $filterManager;
	}


	/**
	 * @return FilterManager
	 */
	public function getFilterManager()
	{
		return $this->filterManager;
	}


	/**
	 * @param int $p_languageId
	 */
	public function setLanguageId($p_languageId)
	{
		$this->languageId = (int)$p_languageId;
	}


	/**
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->languageId;
	}
}
