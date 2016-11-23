<?php
/* --------------------------------------------------------------
  GMSitemapXML.php  2016-08-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------
 */


/*
 * 	-> class to create a google sitemap
 */

class GMSitemapXML_ORIGIN
{
	/*
	 * 	-> category path
	 */
	var $cat_path;

	/*
	 * 	-> category sub path
	 */
	var $cat_sub_path;

	/*
	 * 	-> language_id
	 */
	var $language_id;

	/*
	 * 	-> default changefreq
	 */
	var $changefreq;

	/*
	 * 	-> default priority
	 */
	var $priority;

	/*
	 * 	-> to count generated links
	 */
	var $link_counter;

	/*
	 * 	-> request uri
	 */
	var $request_uri;

	/*
	 * 	-> object for SEF Urls
	 */
	var $coo_seo_boost;
	
	/*
	 * -> object for data caching
	 */
	var $coo_data_cache; 

	/*
	 * 	-> kind of xml encoding - e.g. 'UTF-8', 'ISO-8859-1', etc.
	 */
	var $xml_encoding = 'UTF-8';


	/*
	 * 	-> kind of the xml version
	 */
	var $xml_version = '1.0';


	/*
	 * 	-> references the current protocol standard
	 */
	var $path = DIR_FS_CATALOG;

	/*
	 * 	-> references the current protocol standard
	 */
	var $filename = 'sitemap1.xml';

	/*
	 * 	-> default constructor
	 */

	function __construct()
	{
		$this->language_id = gm_get_conf('GM_SITEMAP_GOOGLE_LANGUAGE_ID');
		$this->changefreq = gm_get_conf('GM_SITEMAP_GOOGLE_CHANGEFREQ');
		$this->priority = gm_get_conf('GM_SITEMAP_GOOGLE_PRIORITY');

		$this->request_uri = HTTP_SERVER . DIR_WS_CATALOG . $this->filename;
		$this->coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$this->coo_data_cache = DataCache::get_instance(); 

		return;
	}

	/*
	 * 	-> create sitemap
	 */

	function create($categories, $content)
	{
		$t_xml = '<?xml version="' . $this->xml_version . '" encoding="' . $this->xml_encoding . '"?>' . "\n\t";

		$t_xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n\n\t\t";

		$t_xml .= $categories;

		$t_xml .= $content;

		$t_xml .= "\n\t" . '</urlset>';

		$this->output($t_xml);

		$link_counter = substr_count($t_xml, '<url>'); 
		
		return TITLE_SITEMAP_CREATED . $link_counter . TITLE_SITEMAP_CREATED2 . '<a href="' . HTTP_SERVER . DIR_WS_CATALOG . $this->filename . '" target="_blank">' . $this->filename . '</a><br /><br />';
	}

	/*
	 * 	-> get categories
	 */

	function get_categories()
	{
		$t_max_execution_time = @ini_get('max_execution_time') ?: 20;
		$t_start_time = time();
		
		$t_cache_data = array(
			'limit' => 0,
			'tree' => ''
		);
		
		$t_tree = '';
		
		if($this->coo_data_cache->key_exists('sitemap_cache_data', true))
		{
			$t_cache_data = $this->coo_data_cache->get_data('sitemap_cache_data', true);
			$t_tree = $t_cache_data['tree'];
		}
		
		/*
		 * 	-> get cats 
		 */
		$t_sql = "SELECT
					c.categories_id						AS id, 
					c.gm_priority						AS priority, 
					c.gm_changefreq						AS changefreq, 
					UNIX_TIMESTAMP(c.last_modified)		AS last_mod,
					UNIX_TIMESTAMP(c.date_added)		AS date_added,
					cd.categories_name					AS name,
					cd.gm_url_keywords					AS keyword
				FROM " . TABLE_CATEGORIES . " c 
				LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON c.categories_id = cd.categories_id 
				WHERE 
					cd.language_id = '" . (int)$this->language_id . "' AND 
					c.parent_id = '0' AND 
					c.categories_status = '1' AND 
					c.gm_sitemap_entry = '1' 
					" . $this->get_group_check('c') . "
				ORDER BY c.sort_order, cd.categories_name"; 
		
		if($t_cache_data['limit'] !== 0)
		{
			$t_sql .= ' LIMIT ' . $t_cache_data['limit'] . ', 100000000000000'; 
		}
		
		$t_query = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_query))
		{
			if($this->coo_seo_boost->boost_categories)
			{
				$this->cat_path = $this->coo_seo_boost->get_boosted_category_url($t_result_array['id'], (int)$this->language_id);
				$t_result_array['link'] = $this->cat_path;
			}

			$t_sub_tree = $this->get_categories_tree($t_result_array['id']);
			$t_prd = $this->get_products($t_result_array['id']);

			$t_help .= $this->add_url($t_result_array);

			if(!empty($t_sub_tree))
			{
				$t_help .= $t_sub_tree;
			}

			if(!empty($t_prd))
			{
				$t_help .= $t_prd;
			}

			$t_tree .= $t_help;
			$t_help = '';
			$t_cache_data['tree'] = $t_tree;
			$t_cache_data['limit']++;
			
			if($t_max_execution_time - (time() - $t_start_time) < 5) // Stop the execution 1 seconds before the max_execution_time is reached.
			{
				$this->coo_data_cache->set_data('sitemap_cache_data', $t_cache_data, true);
				return false;
			}
		}

		$this->coo_data_cache->clear_cache('sitemap_cache_data'); 
		
		return $t_tree;
	}

	/*
	 * 	-> get categories tree recursive
	 */

	function get_categories_tree($p_id)
	{
		$t_query = xtc_db_query("SELECT
										c.categories_id						AS id, 
										c.gm_priority						AS priority, 
										c.gm_changefreq						AS changefreq, 
										UNIX_TIMESTAMP(c.last_modified)		AS last_mod,
										UNIX_TIMESTAMP(c.date_added)		AS date_added,
										cd.categories_name					AS name,
										cd.gm_url_keywords					AS keyword
									FROM " . TABLE_CATEGORIES . " c 
									LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON c.categories_id = cd.categories_id 
									WHERE 
										cd.language_id = '" . (int)$this->language_id . "' AND 
										c.parent_id = '" . (int)$p_id . "' AND 
										c.categories_status = '1' AND 
										c.gm_sitemap_entry = '1' 
										" .	$this->get_group_check('c') . "
									ORDER BY c.sort_order, cd.categories_name");

		while($t_result_array = xtc_db_fetch_array($t_query))
		{
			if($this->coo_seo_boost->boost_categories)
			{
				$t_result_array['link'] = $this->coo_seo_boost->get_boosted_category_url($t_result_array['id'], (int)$this->language_id);
			}

			$t_sub_tree = $this->get_categories_tree($t_result_array['id']);
			$t_prd = $this->get_products($t_result_array['id']);

			$t_help .= $this->add_url($t_result_array);

			if(!empty($t_sub_tree))
			{
				$t_help .= $t_sub_tree;
			}

			if(!empty($t_prd))
			{
				$t_help .= $t_prd;
			}

			$t_tree .= $t_help;
			$t_help = '';
			$this->cat_sub_path = '';
		}
		
		return $t_tree;
	}

	/*
	 * 	-> get articles
	 */

	function get_products($p_id)
	{
		$t_query = xtc_db_query("SELECT
										p.products_id									AS id, 
										p.gm_priority									AS priority, 
										p.gm_changefreq									AS changefreq, 
										UNIX_TIMESTAMP(p.products_last_modified)		AS last_mod, 
										UNIX_TIMESTAMP(p.products_date_added)			AS date_added, 
										pd.products_name								AS name,
										pd.gm_url_keywords								AS keyword	
									FROM " . TABLE_PRODUCTS . " p 
									LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id = pd.products_id 
									LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON p.products_id = ptc.products_id
									WHERE 
										pd.language_id = '" . (int)$this->language_id . "' AND 
										p.products_status = '1' AND 
										ptc.categories_id = '" . (int)$p_id . "' AND 
										p.gm_sitemap_entry = '1' 
										" .	$this->get_group_check('p') . "
									ORDER BY p.products_sort, pd.products_name");

		if(xtc_db_num_rows($t_query) > 0)
		{
			while($t_result_array = xtc_db_fetch_array($t_query))
			{
				if($this->coo_seo_boost->boost_products)
				{
					$t_result_array['link'] = $this->coo_seo_boost->get_boosted_product_url($t_result_array['id'], $t_result_array['name'], (int)$this->language_id, $t_result_array['gm_url_keywords']);
				}
				
				$t_products .= $this->add_url($t_result_array, 'prd');
			}
		}
		else
		{
			$t_products = '';
		}

		return $t_products;
	}

	/*
	 * 	-> get content
	 */

	function get_content()
	{
		$t_tree = '';
		
		if($this->coo_seo_boost->boost_content)
		{
			$t_group_query = "content_id AS id,";
		}
		else
		{
			$t_group_query = "content_group AS id,";
		}

		$t_query = xtc_db_query("SELECT
									gm_priority							AS priority, 
									gm_changefreq						AS changefreq, 
									" . $t_group_query . "
									content_title						AS heading,
									content_heading						AS name,
									gm_url_keywords						AS keyword,
									gm_link								AS link,
									UNIX_TIMESTAMP(gm_last_modified)	AS last_mod
								FROM " . TABLE_CONTENT_MANAGER . "
								WHERE
									languages_id = '" . (int)$this->language_id . "' AND 
									file_flag != 4 " . $this->get_group_check('content') . " AND 
									content_status = 1 AND
									gm_sitemap_entry = 1 
								ORDER BY sort_order");

		while($t_result_array = xtc_db_fetch_array($t_query))
		{
			if(empty($t_result_array['link']))
			{
				if($this->coo_seo_boost->boost_content)
				{
					$t_result_array['link'] = $this->coo_seo_boost->get_boosted_content_url($t_result_array['id'], (int)$this->language_id);
				}
				
				$t_tree .= $this->add_url($t_result_array, 'content');
			}
		}
		
		return $t_tree;
	}

	/*
	 * 	-> create output
	 */

	function output($p_content)
	{
		$fp = fopen($this->path . $this->filename, 'w');
		fwrite($fp, $p_content);
		fclose($fp);
		
		return;
	}

	/*
	 * add url block
	 */

	function add_url($p_array, $p_type = 'cat')
	{
		$t_url = '<url>' . "\n\t\t\t";

		$t_url .= '<loc>' . htmlspecialchars_wrapper($this->get_link($p_array['id'], $p_array['name'], $p_type, $p_array['link'])) . '</loc>' . "\n\t\t\t";

		if(xtc_not_null($p_array['last_mod']) || xtc_not_null($p_array['date_added']))
		{
			$t_url .= '<lastmod>' . $this->get_date($p_array['last_mod'], $p_array['date_added']) . '</lastmod>' . "\n\t\t\t";
		}

		$t_url .= '<changefreq>' . $this->get_changefreq($p_array['changefreq']) . '</changefreq>' . "\n\t\t\t";

		$t_url .= '<priority>' . $this->get_priority($p_array['priority']) . '</priority>' . "\n\t\t";

		$t_url .= '</url>' . "\n\t\t";

		$this->link_counter++;

		return $t_url;
	}

	/*
	 * 	-> generate links
	 */

	function get_link($p_id, $p_name, $p_type, $p_link = '')
	{
		// -> handle cats
		if($p_type == 'cat')
		{
			if($this->coo_seo_boost->boost_categories)
			{
				return gm_xtc_href_link($p_link);
			}
			else
			{
				return gm_xtc_href_link('index.php', xtc_category_link($p_id, $p_name));
			}

			// -> handle prds
		}
		elseif($p_type == 'prd')
		{
			if($this->coo_seo_boost->boost_products)
			{
				return gm_xtc_href_link($p_link);
			}
			else
			{
				return gm_xtc_href_link('product_info.php', xtc_product_link($p_id, $p_name));
			}

			// -> handle content
		}
		elseif($p_type == 'content')
		{
			if($this->coo_seo_boost->boost_content)
			{
				return gm_xtc_href_link($p_link);
			}
			else
			{
				if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
				{
					$t_SEF_parameter = '&content=' . xtc_cleanName($p_name);
				}
				
				return gm_xtc_href_link('shop_content.php', 'coID=' . $p_id . $t_SEF_parameter);
			}
		}
	}

	/*
	 * 	-> get default changefreq, if standard is empty
	 */

	function get_changefreq($p_changefreq)
	{
		if(empty($p_changefreq))
		{
			$p_changefreq = $this->changefreq;
		}

		return $p_changefreq;
	}

	/*
	 * 	-> get default priority, if standard is empty
	 */

	function get_priority($p_priority)
	{
		if(empty($p_priority))
		{
			$p_priority = $this->priority;
		}
		
		return $p_priority;
	}

	/*
	 * 	-> add group check
	 */

	function get_group_check($p_type)
	{
		if(GROUP_CHECK == 'true')
		{
			if($p_type == "content")
			{
				$t_group_check = " AND group_ids LIKE '%c_1_group%'";
			}
			else
			{
				$t_group_check = " AND " . $p_type . ".group_permission_1=1 ";
			}
		}
		else
		{
			$t_group_check = "";
		}
		
		return $t_group_check;
	}

	/*
	 * 	-> format date
	 */

	function get_date($p_last_mod, $p_date_added)
	{
		if(!empty($p_last_mod))
		{
			return date("Y-m-d", $p_last_mod);
		}
		elseif(!empty($p_date_added))
		{
			return date("Y-m-d", $p_date_added);
		}
		else
		{
			return date("Y-m-d");
		}
	}

}

MainFactory::load_origin_class('GMSitemapXML');
