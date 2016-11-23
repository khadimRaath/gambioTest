<?php
/* --------------------------------------------------------------
   GMCat.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include_once DIR_FS_CATALOG . 'gm/inc/gm_get_categories_icon.inc.php';

/*
* class to get the category navigation
*/

class GMCat_ORIGIN
{
	/*
	*	-> object for SEF Urls
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


	/*
	* default & empty constructor
	*/
	function __construct($cPath)
	{
		$this->gmSEOBoost   = MainFactory::create_object('GMSEOBoost');
		$this->cPath        = $cPath;
		$this->icon_cat_use = gm_get_conf('GM_LOGO_CAT_USE');
		$this->icon_w       = gm_get_conf('GM_LOGO_CAT_SIZE_W');
		$this->icon_h       = gm_get_conf('GM_LOGO_CAT_SIZE_H');
	}


	/*
	* main function to get the sitemap
	*/
	function get()
	{
		if(!empty($this->cPath))
		{
			$id = explode('_', $this->cPath);
			reset($id);
		}

		/*
		* get cats 
		*/
		if(GROUP_CHECK == 'true')
		{
			$group_check = " AND c.group_permission_" . $_SESSION['customers_status']['customers_status_id'] . "=1 ";
		}

		$gm_query = xtc_db_query("
										SELECT
											c.categories_id AS id, 
											cd.categories_name AS name,
											c.parent_id 
										FROM " . TABLE_CATEGORIES . " c 
										LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd 
										ON 
											c.categories_id = cd.categories_id 
										WHERE 
											cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
										AND 
											c.parent_id = '0' 
										AND 
											c.categories_status = '1' " . $group_check . "
										ORDER BY 
											c.sort_order, cd.categories_name
										");

		while($gm_array = xtc_db_fetch_array($gm_query))
		{
			if($id[0] == $gm_array['id'])
			{
				array_splice($id, 0, 1);
				reset($id);
				$gm_sub_tree = $this->get_tree($gm_array['id'], $id);
			}
			else
			{
				$gm_help = '';
				if(gm_get_conf('GM_SHOW_CAT') == 'all' || gm_get_conf('GM_SHOW_CAT') == 'child')
				{
					$gm_sub_tree = $this->get_tree($gm_array['id'], $id);
				}
				else
				{
					$gm_sub_tree = '';
				}
			}

			if(!empty($gm_sub_tree))
			{
				$gm_help = '<div class="categories">' . $this->get_cat_link($gm_array['id'], $gm_array['name'], true)
				           . $gm_sub_tree . '</div></div>';
			}
			else
			{
				$gm_help = '<div class="categories">' . $this->get_cat_link($gm_array['id'], $gm_array['name'], true)
				           . '</div></div>';
			}
			$gm_tree .= $gm_help;
			$gm_help = '';
		}

		return $gm_tree;
	}


	/*
	* read cats
	*/
	function get_tree($id, $subtree = '')
	{
		$gm_tree = '';
		
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
				$gm_sub_tree = '';
				
				if(gm_get_conf('GM_SHOW_CAT') == 'all')
				{
					$gm_sub_tree = $this->get_tree($gm_array['id'], $id);
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


	/*
	* create sef-cat-link
	*/
	function get_cat_link($id, $name, $is_top)
	{
		if($this->gmSEOBoost->boost_categories)
		{
			$gm_cat_link = xtc_href_link($this->gmSEOBoost->get_boosted_category_url($id, $_SESSION['languages_id']));
			if($this->is_cat_active($id))
			{
				return
					$this->gm_get_categories_icon($id, $name, $is_top, $gm_cat_link) . '<div><a href="' . $gm_cat_link
					. '"><span class="cat_active">' . htmlspecialchars_wrapper($name) . '</span></a>'
					. gm_count_products_in_category($id) . "</div>";
			}
			else
			{
				return
					$this->gm_get_categories_icon($id, $name, $is_top, $gm_cat_link) . '<div><a href="' . $gm_cat_link
					. '">' . htmlspecialchars_wrapper($name) . '</a>' . gm_count_products_in_category($id) . "</div>";
			}
		}
		else
		{
			$gm_cat_link = xtc_href_link(FILENAME_DEFAULT, xtc_category_link($id, $name));
			if($this->is_cat_active($id))
			{
				return
					$this->gm_get_categories_icon($id, $name, $is_top, $gm_cat_link) . '<div><a href="' . $gm_cat_link
					. '"><span class="cat_active">' . htmlspecialchars_wrapper($name) . '</span></a>'
					. gm_count_products_in_category($id) . "</div>";
			}
			else
			{
				return
					$this->gm_get_categories_icon($id, $name, $is_top, $gm_cat_link) . '<div><a href="' . $gm_cat_link
					. '">' . htmlspecialchars_wrapper($name) . '</a>' . gm_count_products_in_category($id) . "</div>";
			}
		}
	}


	/*
	* create check if element is active
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


	/*
	* create cat icon
	*/
	function gm_get_categories_icon($cid, $cname, $is_top, $gm_cat_link)
	{
		$gm_use_logo = false;

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
				return '<div class="cat_icon"><a href="' . $gm_cat_link . '"><img src="' . $icon . '" width="' . $_w
				       . '" height="' . $_h . '" alt="' . htmlspecialchars_wrapper($cname) . '" title="'
				       . htmlspecialchars_wrapper($cname) . '" /></a></div><div class="cat_link" style="padding-left:'
				       . ($_w + 3) . 'px;">';
			}
			else
			{
				return '<div class="cat_icon"><a href="' . $gm_cat_link . '"><img src="' . $icon . '" width="' . $_w
				       . '" height="' . $_h . '" alt="' . htmlspecialchars_wrapper($cname) . '" title="'
				       . htmlspecialchars_wrapper($cname)
				       . '" /></a></div><div class="cat_sub_link" style="padding-left:' . ($_w + 3) . 'px;">';
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


	/*
	* create counts
	*/
	function gm_count_products_in_category($cid)
	{
		@require_once(DIR_FS_INC . 'xtc_count_products_in_category.inc.php');

		if(SHOW_COUNTS == 'true')
		{
			$products_in_category = xtc_count_products_in_category($cid);
			if($products_in_category > 0)
			{
				return $categories_string .= ' (' . $products_in_category . ')';
			}
		}
	}


	/*
	* create icon size once a time
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
}

MainFactory::load_origin_class('GMCat');