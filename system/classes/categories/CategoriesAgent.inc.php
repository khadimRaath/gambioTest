<?php
/* --------------------------------------------------------------
   CategoriesAgent.inc.php 2014-11-12 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CategoriesAgent
{
	var $v_default_icon	= '';
	var $v_default_icon_w = 0;
	var $v_default_icon_h = 0;
	
	static function get_instance()
	{
		static $s_instance;

		if($s_instance === NULL)   {
			$s_instance = MainFactory::create_object('CategoriesAgent');
		}
		return $s_instance;
	}
	
	function CategoriesAgent()
	{
		$this->init_default_icon();
	}

	function init_default_icon()
	{
		$t_icon_path_web	= DIR_WS_IMAGES.'logos/'.gm_get_conf('GM_LOGO_CAT');
		$t_icon_path_server	= DIR_FS_CATALOG.$t_icon_path_web;
		
		#$this->v_default_icon = HTTP_SERVER.DIR_WS_CATALOG.DIR_WS_IMAGES.'logos/'.gm_get_conf('GM_LOGO_CAT');

		if(is_file($t_icon_path_server))
		{
			$this->v_default_icon = $t_icon_path_web;
			
			$t_img_sizes = getimagesize($t_icon_path_server);
			$this->v_default_icon_w = $t_img_sizes[0];
			$this->v_default_icon_h = $t_img_sizes[1];
		}
		else {
//			trigger_error('default_icon not found', E_USER_WARNING);
		}
	}

	#TODO: move to data
	function get_categories_raw_tree($p_parent_id, $p_language_id, $p_depth)
	{
		$coo_stop_watch = LogControl::get_instance()->get_stop_watch();
		$coo_stop_watch->start('get_categories_raw_tree');

		$p_output_tree_array = array();
		
		$c_parent_id	= (int)$p_parent_id;
		$c_language_id	= (int)$p_language_id;

		$t_group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$t_group_check = ' AND c.group_permission_'.$_SESSION['customers_status']['customers_status_id'].'=1 ';
		}

		$t_sql = '
			SELECT *
			FROM
				categories AS c LEFT JOIN categories_description AS cd USING (categories_id)
			WHERE
				c.categories_status = 1 AND
				c.parent_id			= "'. $c_parent_id	 .'" AND
				cd.language_id		= "'. $c_language_id .'" '.
				$t_group_check .'
			ORDER BY
				c.sort_order ASC,
				cd.categories_name ASC
		';
		$t_result = xtc_db_query($t_sql);

		while(($t_row = xtc_db_fetch_array($t_result) ))
		{
			if(SHOW_COUNTS == 'true')
			{
				$t_group_check = '';
				if(GROUP_CHECK == 'true')
				{
					$t_group_check = ' AND p.group_permission_'.$_SESSION['customers_status']['customers_status_id'].' = 1 ';
				}
				
				$fsk_lock = '';
				if($_SESSION['customers_status']['customers_fsk18_display'] == '0')
				{
					$fsk_lock = ' AND p.products_fsk18 != 1 ';
				}
				
				$t_sql = "SELECT count(*) AS products_count
							FROM 
								categories_index ci,
								products p
							WHERE 
								ci.categories_index LIKE '%-" . (int)$t_row['categories_id'] . "-%' AND
								ci.products_id = p.products_id AND
								p.products_status = 1
								" . $t_group_check . " 
								" . $fsk_lock ;
				$t_count_result = xtc_db_query($t_sql);
				$t_result_array = xtc_db_fetch_array($t_count_result);
				$t_row['products_count'] = $t_result_array['products_count'];
			}

			$t_category_children_array = array();
			if($p_depth > 0)
			{
				$t_next_level_depth = $p_depth - 1;
				$t_category_children_array = $this->get_categories_raw_tree($t_row['categories_id'], $c_language_id, $t_next_level_depth);
			}

			$p_output_tree_array[] = array(
										'data' => $t_row,
										'children' => $t_category_children_array
									);
		}
		$coo_stop_watch->stop('get_categories_raw_tree');
		return $p_output_tree_array;
	}

	public function getPartentsIds($p_cPath)
	{
		$arrParents = explode('_', trim($p_cPath));
		return $arrParents;
	}
	
	function get_categories_info_tree($p_parent_id, $p_language_id, $p_depth)
	{
		$coo_stop_watch = LogControl::get_instance()->get_stop_watch();
		$coo_stop_watch->start('get_categories_info_tree');

		$c_parent_id	= (int)$p_parent_id;
		$c_language_id	= (int)$p_language_id;
		$c_depth		= (int)$p_depth;
		$c_customers_status_id = (int)$_SESSION['customers_status']['customers_status_id'];

		if(xtc_not_null(SID)) {
			$t_use_sid = 'sid_TRUE';
		} else {
			$t_use_sid = 'sid_FALSE';
		}

		# parameter list for cache matching
		$t_cache_key_source = $c_parent_id.'-'.$c_language_id.'-'.$c_depth.'-'.$c_customers_status_id.'-'.$t_use_sid;
		
		$coo_cache = DataCache::get_instance();
		$t_cache_key = $coo_cache->build_key($t_cache_key_source);

		$t_cached = '';

		if($coo_cache->key_exists($t_cache_key, true))
		{
			# use cached result
			$t_info_tree_array = $coo_cache->get_data($t_cache_key);
			$t_cached = 'cached';
			//if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log("AAA-DEBUG: cache used ");
		}
		else
		{
			# no cache content found
			$t_raw_tree_array = $this->get_categories_raw_tree($c_parent_id, $c_language_id, $c_depth);
			$t_info_tree_array = $this->prepare_raw_tree($t_raw_tree_array);

			# write to cache (only if there are data to write - fix #23354)
			if(count($t_info_tree_array) > 0)
			{
				$coo_cache->set_data($t_cache_key, $t_info_tree_array, true);
				//if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log("AAA-DEBUG: cache NOT used ");	
			}
		}
		
		$coo_stop_watch->stop('get_categories_info_tree');

		return $t_info_tree_array;
	}

	function prepare_raw_tree($p_raw_tree_array)
	{
		$coo_stop_watch = LogControl::get_instance()->get_stop_watch();
		$coo_stop_watch->start('prepare_raw_tree');

		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');


		$t_output_tree_array = array();

		# scan tree recursively
		for($i=0; $i<sizeof($p_raw_tree_array); $i++)
		{
			$t_cat_id = $p_raw_tree_array[$i]['data']['categories_id'];
			$t_cat_name = $p_raw_tree_array[$i]['data']['categories_name'];
			$t_cat_url_keywords = $p_raw_tree_array[$i]['data']['gm_url_keywords'];

			# build url
			if($coo_seo_boost->boost_categories)
			{
				$t_boosted = $coo_seo_boost->get_boosted_category_url($t_cat_id, false, $t_cat_url_keywords);
				$t_cat_url = xtc_href_link($t_boosted);
				$t_cat_url = str_replace(HTTP_SERVER.DIR_WS_ADMIN, HTTP_SERVER.DIR_WS_CATALOG, $t_cat_url);
				
			}
			else {
				$t_category_link = xtc_category_link($t_cat_id, $t_cat_name.'_'.$t_cat_url_keywords, true);
				$t_cat_url = xtc_href_link(FILENAME_DEFAULT, $t_category_link);
				$t_cat_url = str_replace(HTTP_SERVER.DIR_WS_ADMIN.'start.php', HTTP_SERVER.DIR_WS_CATALOG.'index.php', $t_cat_url);
			}

			
			# build icon
			$t_icon_path_web	= DIR_WS_IMAGES.'categories/icons/'.$p_raw_tree_array[$i]['data']['categories_icon'];
			$t_icon_path_server	= DIR_FS_CATALOG.$t_icon_path_web;
			
			if(is_file($t_icon_path_server))
			{
				# use category icon
				$t_cat_icon	  = $t_icon_path_web;
				$t_cat_icon_w = $p_raw_tree_array[$i]['data']['categories_icon_w'];
				$t_cat_icon_h = $p_raw_tree_array[$i]['data']['categories_icon_h'];
			}
			else
			{
				# use default icon
				$t_cat_icon	  = $this->v_default_icon;
				$t_cat_icon_w = $this->v_default_icon_w;
				$t_cat_icon_h = $this->v_default_icon_h;
			}


			# build image
			$t_image_path_web	 = DIR_WS_IMAGES.'categories/'.$p_raw_tree_array[$i]['data']['categories_image'];
			$t_image_path_server = DIR_FS_CATALOG.$t_image_path_web;

			$t_cat_image = '';
			if(is_file($t_image_path_server))
			{
				$t_cat_image = $t_image_path_web;
			}
			
			# copy from raw to prepared
			$t_category_info_array = array(
										'id'		=> $t_cat_id,
										'name'		=> $t_cat_name,
										'url'		=> $t_cat_url,
										'icon'		=> $t_cat_icon,
										'icon_w'	=> $t_cat_icon_w,
										'icon_h'	=> $t_cat_icon_h,
										'image'		=> $t_cat_image,
										'image_alt'	=> $p_raw_tree_array[$i]['data']['gm_alt_text'],
										'meta_description'	=> $p_raw_tree_array[$i]['data']['categories_meta_description']
									);

			if(isset($p_raw_tree_array[$i]['data']['products_count']))
			{
				$t_category_info_array = array_merge($t_category_info_array, array('products_count' => $p_raw_tree_array[$i]['data']['products_count']));
			}

			# look for children
			$t_category_children_array = array();
			if(sizeof($p_raw_tree_array[$i]['children']) > 0)
			{
				$t_category_children_array = $this->prepare_raw_tree($p_raw_tree_array[$i]['children']);
			}

			# collect body data and children
			$t_output_tree_array[] = array(
										'data' => $t_category_info_array,
										'children' => $t_category_children_array
									);
		}
		$coo_stop_watch->stop('prepare_raw_tree');

		return $t_output_tree_array;
	}

	function get_products_ids_array($p_categories_id, $p_recursively=false)
	{
		$t_output_array = array();
		$c_categories_id = (int)$p_categories_id;

		# get categories products
		$t_query = '
			SELECT
				products_id
			FROM
				products_to_categories AS p2c
			WHERE
				p2c.categories_id = "'. $c_categories_id .'"
		';
		$t_result = xtc_db_query($t_query);

		while(($t_row = xtc_db_fetch_array($t_result) ))
		{
			$t_output_array[] = $t_row['products_id'];
		}

		# get sub-categories
		if($p_recursively == true)
		{
			$t_query = '
				SELECT
					categories_id
				FROM
					categories AS c
				WHERE
					c.parent_id= "'. $c_categories_id .'"
			';
			$t_result = xtc_db_query($t_query);

			while(($t_row = xtc_db_fetch_array($t_result) ))
			{
				$t_sub_products_ids_array = $this->get_products_ids_array($t_row['categories_id'], true);
				$t_output_array = array_merge($t_output_array, $t_sub_products_ids_array);
			}
		}

		return $t_output_array;
	}

}