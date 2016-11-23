<?php
/* --------------------------------------------------------------
   GMSitemap.php 2015-07-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include_once DIR_FS_CATALOG . 'gm/inc/gm_get_categories_icon.inc.php';

/*
* class to show visual sitemap
*/

class GMSitemap_ORIGIN
{
	/*
	*	-> object for SEF Urls
	*/
	var $gmSEOBoost;


	/*
	* default & empty constructor
	*/
	function __construct()
	{
		$this->gmSEOBoost = MainFactory::create_object('GMSEOBoost');
	}


	/*
	* main function to get the sitemap
	*/
	function get()
	{
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

		$gm_tree = '<ul>';
		while($gm_array = xtc_db_fetch_array($gm_query))
		{
			$gm_sub_tree = $this->get_tree($gm_array['id']);
			if(!empty($gm_sub_tree))
			{
				$gm_help = '<li class="parent"><h2>' . $this->get_cat_link($gm_array['id'], $gm_array['name']) . '</h2>'
				           . $gm_sub_tree . '</li>';
			}
			else
			{
				$gm_help = '<li class="parent"><h2>' . $this->get_cat_link($gm_array['id'], $gm_array['name'])
				           . '</h2></li>';
			}
			$gm_tree .= $gm_help;
		}

		/*
		* get contents 
		*/
		if(GROUP_CHECK == 'true')
		{
			$group_check = "and group_ids LIKE '%c_" . $_SESSION['customers_status']['customers_status_id']
			               . "_group%'";
		}

		if($this->gmSEOBoost->boost_content)
		{
			$group_query = "content_id AS id,";
		}
		else
		{
			$group_query = "content_group AS id,";
		}

		$gm_query = xtc_db_query("SELECT
										" . $group_query . "
										content_title,
										gm_link
									FROM " . TABLE_CONTENT_MANAGER . "
									WHERE
										languages_id='" . (int)$_SESSION['languages_id'] . "'
									AND 
										file_flag != 5
									AND
										file_flag!=4 " . $group_check . " 
									AND 
										content_status=1 
									ORDER BY 
										sort_order
									");

		while($content_data = xtc_db_fetch_array($gm_query))
		{
			$SEF_parameter = '';

			if($this->gmSEOBoost->boost_content)
			{
				$link = xtc_href_link($this->gmSEOBoost->get_boosted_content_url($content_data['id'],
				                                                                 $_SESSION['languages_id']));
			}
			else
			{
				if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
				{
					$SEF_parameter = '&content=' . xtc_cleanName($content_data['content_title']);
				}
				$link = xtc_href_link(FILENAME_CONTENT, 'coID=' . $content_data['id'] . $SEF_parameter);
			}

			if(empty($content_data['gm_link']))
			{
				$gm_tree .=
					'<li><h2><a href="' . $link . '">' . htmlspecialchars_wrapper($content_data['content_title'])
					. '</a></h2></li>';
			}
		}

		$gm_tree .= '</ul>';

		return $gm_tree;
	}


	/*
	* read cats
	*/
	function get_tree($id)
	{
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
										c.parent_id = '" . (int)$id . "'
									AND 
										c.categories_status = '1'" . $group_check . "
									ORDER BY 
										c.sort_order, cd.categories_name
									");
		$gm_tree = '';
		while($gm_array = xtc_db_fetch_array($gm_query))
		{
			$gm_sub_tree = $this->get_tree($gm_array['id']);

			if(!empty($gm_sub_tree))
			{
				$gm_help = '<ul><li>' . $this->get_cat_link($gm_array['id'], $gm_array['name']) . $gm_sub_tree
				           . '</li></ul>';
			}
			else
			{
				$gm_help = '<ul><li>' . $this->get_cat_link($gm_array['id'], $gm_array['name']) . '</li></ul>';
			}

			$gm_tree .= $gm_help;
		}

		return $gm_tree;
	}


	/*
	* create sef-cat-link
	*/
	function get_cat_link($id, $name)
	{
		if($this->gmSEOBoost->boost_categories)
		{
			return
				'<a href="' . xtc_href_link($this->gmSEOBoost->get_boosted_category_url($id, $_SESSION['languages_id']))
				. '">' . htmlspecialchars_wrapper($name) . '</a>' . gm_count_products_in_category($id);
		}
		else
		{
			return '<a href="' . xtc_href_link(FILENAME_DEFAULT, xtc_category_link($id, $name)) . '">'
			       . htmlspecialchars_wrapper($name) . '</a>' . gm_count_products_in_category($id);
		}
	}
}

MainFactory::load_origin_class('GMSitemap');