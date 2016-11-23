<?php
/* --------------------------------------------------------------
   CategoriesBox.inc.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include_once DIR_FS_CATALOG . 'gm/inc/gm_get_categories_icon.inc.php';

/**
 * class to get the category navigation
 *
 * Class CategoriesBox
 */
class CategoriesBox
{

	/**
	 * @var GMSEOBoost $gmSEOBoost
	 */
	var $gmSEOBoost;

	/*
	*	-> $cPath
	*/
	var $gmcPath;

	/*
	*	-> use standard icon ?
	*/
	var $icon_cat_use;
	var $icon_w;
	var $icon_h;

	protected $categoriesParentsArray = array();

	/**
	 * @param $cPath
	 */
	function CategoriesBox($cPath)
	{
		$this->gmSEOBoost   = MainFactory::create_object('GMSEOBoost');
		$this->cPath        = $cPath;
		$this->icon_cat_use = gm_get_conf('GM_LOGO_CAT_USE');
		$this->icon_w       = gm_get_conf('GM_LOGO_CAT_SIZE_W');
		$this->icon_h       = gm_get_conf('GM_LOGO_CAT_SIZE_H');

		return;
	}


	/**
	 * main function to get the sitemap
	 *
	 * @return string
	 */
	function get()
	{
		$language_id = (int)$_SESSION['languages_id'];
		$catUppperId = 0;

		$idArray = array();

		if(!empty($this->cPath))
		{
			$idArray = explode('_', $this->cPath);
			reset($idArray);
		}



		if(!empty($idArray))
		{
			$categorieID = end($idArray);
			reset($idArray);
			if(isset($idArray[count($idArray)-2]))
			{
				$catUppperId = $idArray[count($idArray)-2];
			}

		}
		else
		{
			$categorieID = 0;
		}

		/*
		* get cats
		*/

		if($this->getSplitMenueStatus() === true)
		{
			if(!empty($catUppperId) && $categorieID !== 0)
			{
				$result = $this->getCategoryQuery($catUppperId, $language_id);
				$row = xtc_db_fetch_array($result);
				
				$linkToUpperCat = $this->get_cat_link($catUppperId, $row['name'], false, true);
			}

			if(empty($catUppperId) && $categorieID !== 0)
			{
				/** @var LanguageTextManager $languageTextManager */
				$languageTextManager = MainFactory::create_object('LanguageTextManager', array('box_categories', $language_id), true);
				$linkToUpperCat = $this->get_cat_link(0, $languageTextManager->get_text('cat_menu_go_up'), false, true);
			}
		}
		else
		{
			$categorieID    = 0;
			$linkToUpperCat = '';
		}

		$gm_query = $this->getQueryString($categorieID, $language_id);

		$gm_tree = '';
		while($gm_array = xtc_db_fetch_array($gm_query))
		{
			if($idArray[0] == $gm_array['id'])
			{
				array_splice($idArray, 0, 1);
				reset($idArray);
				$gm_sub_tree = $this->get_tree($gm_array['id'], $idArray);
			}
			else
			{
				if(gm_get_conf('GM_SHOW_CAT') == 'all' || gm_get_conf('GM_SHOW_CAT') == 'child')
				{
					$gm_sub_tree = $this->get_tree($gm_array['id'], $idArray);
				}
				else
				{
					$gm_sub_tree = '';
				}
			}

			if(!empty($gm_sub_tree))
			{
				$gm_help = '<div class="categories">' . $this->get_cat_link($gm_array['id'], $gm_array['name'], true) .
						   $gm_sub_tree . '</div></div>';
			}
			else
			{
				$gm_help = '<div class="categories">' . $this->get_cat_link($gm_array['id'], $gm_array['name'], true) .
						   '</div></div>';
			}
			$gm_tree .= $gm_help;
		}

		/** @var CategoriesAgent $coo_categories_agent */
		$coo_categories_agent =& MainFactory::create_object('CategoriesAgent', array(), true);
		if(isset($_GET['cPath']))
		{
			$this->categoriesParentsArray = $coo_categories_agent->getPartentsIds($_GET['cPath']);
		}

		$gm_tree .= $this->_generateParentsIds();

		return $linkToUpperCat . $gm_tree;
	}


	/**
	 * read cats
	 *
	 * @param       $id
	 * @param array $subtree
	 *
	 * @return string
	 */
	function get_tree($id, $subtree = array())
	{
		$group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$group_check = " AND c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
		}

		$gm_query = xtc_db_query("
									SELECT
										c.categories_id AS id, 
										cd.categories_name AS name
									FROM " . TABLE_CATEGORIES . " c
									LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd
									ON 
										c.categories_id = cd.categories_id 
									WHERE 
										cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
									AND 
										c.parent_id = '" . $id . "' 									
									AND
										c.categories_status = '1'" . $group_check . "
									ORDER BY 
										c.sort_order, cd.categories_name
									");

		$gm_tree = '';
		while($gm_array = xtc_db_fetch_array($gm_query))
		{
			if($subtree[0] == $gm_array['id'])
			{
				array_splice($subtree, 0, 1);
				reset($subtree);
				$gm_sub_tree = $this->get_tree($gm_array['id'], $subtree);
			}
			else
			{
				if(gm_get_conf('GM_SHOW_CAT') == 'all')
				{
					$gm_sub_tree = $this->get_tree($gm_array['id'], $subtree);
				}
				else
				{
					$gm_sub_tree = '';
				}
			}

			if(!empty($gm_sub_tree))
			{
				$gm_help = $this->get_cat_link($gm_array['id'], $gm_array['name'], false) . $gm_sub_tree . '</div>';
			}
			else
			{
				$gm_help = $this->get_cat_link($gm_array['id'], $gm_array['name'], false) . '</div>';
			}

			$gm_tree .= $gm_help;

		}

		return $gm_tree;
	}


	/**
	 * create sef-cat-link
	 *
	 * @param      $id
	 * @param      $name
	 * @param      $isTop
	 *
	 * @param bool $isButtonToGoToTop
	 *
	 * @return string
	 */
	function get_cat_link($id, $name, $isTop, $isButtonToGoToTop = false)
	{
		$shopLinkTemplate = '%s<div class="cat_link_container %s" id="menu_cat_id_%s"><a data-menu_id="menu_cat_id_%s" href="%s">%s</a>%s</div>';

		if($isButtonToGoToTop === true)
		{
			$goUpButtonClass = 'cat_go_up_button';
			$categoryClass = '';
		}
		else
		{
			$goUpButtonClass = '';
			$categoryClass = $this->_generateCategoryClass($id);
		}

		$showLink = '<span class="' . $goUpButtonClass . '">' . htmlspecialchars_wrapper($name) . '</span>';
		
		/** @noinspection PhpUndefinedFieldInspection */
		if($this->gmSEOBoost->boost_categories)
		{
			/** @noinspection PhpUndefinedMethodInspection */
			$categoryLink    = xtc_href_link($this->gmSEOBoost->get_boosted_category_url($id, $_SESSION['languages_id'], $name));

			if($this->is_cat_active($id) && $isButtonToGoToTop === false)
			{
				$showLink = '<span class="cat_active">' . htmlspecialchars_wrapper($name) . '</span>';
			}
		}
		else
		{
			$categoryLink    = xtc_href_link(FILENAME_DEFAULT, xtc_category_link($id, $name));

			if($this->is_cat_active($id) && $isButtonToGoToTop === false)
			{
				$showLink = '<span class="cat_active">' . htmlspecialchars_wrapper($name) . '</span>';
			}
		}

		$catIcon             = $this->gm_get_categories_icon($id, $name, $isTop, $categoryLink);
		$productsInCategory = gm_count_products_in_category($id);

		$shopLinkFull = sprintf($shopLinkTemplate, $catIcon, $categoryClass, $id, $id, $categoryLink, $showLink,
								$productsInCategory);

		return $shopLinkFull;
	}


	/**
	 * @param $categoryId
	 *
	 * @return string
	 */
	protected function _generateCategoryClass($categoryId)
	{
		$categoriesArray = explode("_", $this->cPath);

		if(end($categoriesArray) == $categoryId)
		{
			return 'current';
		}
		elseif(in_array($categoryId, $categoriesArray))
		{
			return 'parentOfCurrent';
		}
	
		return '';
	}
	

	/**
	 * create check if element is active
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function is_cat_active($id)
	{
		$path = explode("_", $this->cPath);

		for($i = 0; $i < count($path); $i++)
		{
			if($path[$i] == $id)
			{
				return true;
			}
		}

		return false;
	}


	/**
	 * create cat icon
	 *
	 * @param $cid
	 * @param $cname
	 * @param $is_top
	 * @param $gm_cat_link
	 *
	 * @return string
	 */
	function gm_get_categories_icon($cid, $cname, $is_top, $gm_cat_link)
	{
		$gm_use_logo = false;
		$icon        = '';

		// fetch standard icon
		if($this->icon_cat_use == '1')
		{
			$icon        = DIR_WS_IMAGES . 'logos/' . gm_get_conf('GM_LOGO_CAT');
			$_w          = $this->icon_w;
			$_h          = $this->icon_h;
			$gm_use_logo = true;
		}


		// fetch individual icon
		$gm_query = xtc_db_query("
									SELECT
										categories_icon		AS icon, 
										categories_icon_w	AS w, 
										categories_icon_h	AS h 
									FROM
										categories
									WHERE
										categories_id = '" . (int)$cid . "'								
									");

		if(xtc_db_num_rows($gm_query) > 0)
		{
			$gm_icon = xtc_db_fetch_array($gm_query);

			if(!empty($gm_icon['icon']))
			{
				$icon = $gm_icon['icon'];

				// set icon_size if !exist once a time
				if(empty($gm_icon['h']) || empty($gm_icon['w']))
				{
					$imagesize    = @getimagesize(DIR_FS_CATALOG . 'images/categories/icons/' . $icon);
					$gm_icon['w'] = $imagesize[0];
					$gm_icon['h'] = $imagesize[1];
					$this->gm_set_cat_icon_size($cid, $imagesize);
				}

				$_w          = $gm_icon['w'];
				$_h          = $gm_icon['h'];
				$icon        = DIR_WS_IMAGES . 'categories/icons/' . $icon;
				$gm_use_logo = true;
			}
		}

		if($gm_use_logo)
		{
			if($is_top)
			{
				return '<div class="cat_icon"><a href="' . $gm_cat_link . '"><img src="' . $icon . '" width="' . $_w .
					   '" height="' . $_h . '" alt="' . htmlspecialchars_wrapper($cname) . '" title="' .
					   htmlspecialchars_wrapper($cname) . '" /></a></div><div class="cat_link" style="padding-left:' .
					   ($_w + 3) . 'px;">';
			}
			else
			{
				return '<div class="cat_icon"><a href="' . $gm_cat_link . '"><img src="' . $icon . '" width="' . $_w .
					   '" height="' . $_h . '" alt="' . htmlspecialchars_wrapper($cname) . '" title="' .
					   htmlspecialchars_wrapper($cname) .
					   '" /></a></div><div class="cat_sub_link" style="padding-left:' . ($_w + 3) . 'px;">';
			}
		}
		else
		{
			if($is_top)
			{
				return '<div class="cat_link">';
			}
			else
			{
				return '<div class="cat_sub_link">';
			}
		}
	}

	function getCategorieUpLink(){

	}


	/**
	 * create counts
	 *
	 * @param $cid
	 *
	 * @return string
	 */
	function gm_count_products_in_category($cid)
	{
		$categories_string = '';
		@require_once(DIR_FS_INC . 'xtc_count_products_in_category.inc.php');

		if(SHOW_COUNTS == 'true')
		{
			$products_in_category = xtc_count_products_in_category($cid);
			if($products_in_category > 0)
			{
				$ret =  $categories_string . ' (' . $products_in_category . ')';
				return $ret;
			}
		}
	}

	/*
	* create icon size once a time
	*/
	/**
	 * @param $cid
	 * @param $gm_icon_size
	 */
	function gm_set_cat_icon_size($cid, $gm_icon_size)
	{
		xtc_db_query("
							UPDATE " . TABLE_CATEGORIES . "
							SET 
								categories_icon_w	= '" . $gm_icon_size[0] . "',
								categories_icon_h	= '" . $gm_icon_size[1] . "'
							WHERE 
								categories_id		= '" . (int)$cid . "'
						");
	}


	/**
	 * @param $currentCategorieId
	 * @param $languageID
	 *
	 * @return bool|resource
	 */
	protected function getQueryString($currentCategorieId, $languageID)
	{

		$group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$group_check = " AND c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
		}

		$query = "
						SELECT
							c.categories_id AS id,
							cd.categories_name AS name,
							c.parent_id
						FROM " . TABLE_CATEGORIES . " c
						LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd
						ON
							c.categories_id = cd.categories_id
						WHERE
							cd.language_id = '" . $languageID . "'
						AND
							c.parent_id = $currentCategorieId
						AND
							c.categories_status = '1' " . $group_check . "
						ORDER BY
							c.sort_order, cd.categories_name
						";


		$gm_query = xtc_db_query($query);

		return $gm_query;

	}

	/**
	 * @param $currentCategorieId
	 * @param $languageID
	 *
	 * @return bool|resource
	 */
	protected function getCategoryQuery($currentCategorieId, $languageID)
	{

		$group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$group_check = " AND c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
		}

		$query = "
						SELECT
							c.categories_id AS id,
							cd.categories_name AS name,
							c.parent_id
						FROM " . TABLE_CATEGORIES . " c
						LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd
						ON
							(c.categories_id = $currentCategorieId
						AND	
							c.categories_id = cd.categories_id)
						WHERE
							cd.language_id = '" . $languageID . "'
						AND
							c.categories_status = '1' " . $group_check . "
						ORDER BY
							c.sort_order, cd.categories_name
						";


		$gm_query = xtc_db_query($query);

		return $gm_query;

	}


	/**
	 * @return bool
	 */
	protected function getSplitMenueStatus()
	{

		$splitMeneStatus = false;

		if(gm_get_conf('SHOW_SPLIT_MENU') === 'true')
		{
			$splitMeneStatus = true;
		}

		return $splitMeneStatus;
	}

	protected function _generateParentsIds()
	{
		$categoriesParents = '<script type="text/javascript">parentsIds = ' . json_encode($this->categoriesParentsArray) . ';</script>';
		return $categoriesParents;
	}
}
