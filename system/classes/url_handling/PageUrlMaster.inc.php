<?php
/* --------------------------------------------------------------
   PageUrlMaster.inc.php 2012-06-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
require_once(DIR_FS_INC . 'xtc_category_link.inc.php');
require_once(DIR_FS_INC . 'xtc_cleanName.inc.php');
require_once(DIR_FS_INC . 'xtc_get_prid.inc.php');

class PageUrlMaster
{
	var $v_coo_seo_boost = null;
	
	function PageUrlMaster()
	{
		$this->v_coo_seo_boost = MainFactory::create_object('GMSEOBoost', array(), true);
	}
	
	
	function get_product_url($p_products_id, $p_name = '', $p_languages_id = false, $p_parameters = '', $p_connection = 'NONSSL', $p_add_session_id = true, $p_search_engine_safe = true)
	{
		$t_url = '';
		
		$c_type = (string)$p_type;
		$c_products_id = (string)$p_products_id;
		$c_name = (string)$p_name;
		$c_languages_id = (int)$_SESSION['languages_id'];
		if($p_languages_id !== false)
		{
			$c_languages_id = (int)$p_languages_id;
		}
		
		if($c_name == '')
		{
			$t_sql = "SELECT products_name
						FROM " . TABLE_PRODUCTS_DESCRIPTION . "
							WHERE
							products_id = '" . (int)xtc_get_prid($c_products_id) . "' AND
							language_id = '" . $c_languages_id . "'";
			$t_result = xtc_db_query($t_sql);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				$c_name = $t_result_array['products_name'];
			}
		}

		if($this->v_coo_seo_boost->boost_products && $p_search_engine_safe)
		{
			$t_url = xtc_href_link($this->v_coo_seo_boost->get_boosted_product_url((int)xtc_get_prid($c_products_id), $c_name, $c_languages_id), $p_parameters, $p_connection, $p_add_session_id, $p_search_engine_safe);
		}
		else
		{
			$t_parameters = (string)$p_parameters;
			if(strlen($t_parameters) > 0 && substr($t_parameters, 0, 1) != '&')
			{
				$t_parameters = '&' . $t_parameters;
			}

			$t_url = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($c_products_id, $c_name) . $t_parameters, $p_connection, $p_add_session_id, $p_search_engine_safe);
		}
		
		return $t_url;
	}
	
	
	function get_category_url($p_categories_id, $p_name = '', $p_languages_id = false, $p_parameters = '', $p_connection = 'NONSSL', $p_add_session_id = true, $p_search_engine_safe = true, $p_keywords_given = false)
	{
		$t_url = '';
		
		$c_type = (string)$p_type;
		$c_categories_id = (int)$p_categories_id;
		$c_name = (string)$p_name;
		$c_keywords_given = (bool)$p_keywords_given;
		$c_languages_id = (int)$_SESSION['languages_id'];
		if($p_languages_id !== false)
		{
			$c_languages_id = (int)$p_languages_id;
		}
		
		if($this->v_coo_seo_boost->boost_categories && $p_search_engine_safe)
		{
			$t_url = xtc_href_link($this->v_coo_seo_boost->get_boosted_category_url($c_categories_id, $c_languages_id), $p_parameters, $p_connection, $p_add_session_id);
		}
		else
		{
			if($c_name == '')
			{
				$t_sql = "SELECT categories_name
							FROM " . TABLE_CATEGORIES_DESCRIPTION . "
							WHERE
								categories_id = '" . $c_categories_id . "' AND
								language_id = '" . $c_languages_id . "'";
				$t_result = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_result_array = xtc_db_fetch_array($t_result);
					$c_name = $t_result_array['categories_name'];
				}
			}

			$t_parameters = (string)$p_parameters;
			if(strlen($t_parameters) > 0 && substr($t_parameters, 0, 1) != '&')
			{
				$t_parameters = '&' . $t_parameters;
			}

			$t_url = xtc_href_link(FILENAME_DEFAULT, xtc_category_link($c_categories_id, $c_name, $c_keywords_given, $c_languages_id) . $t_parameters, $p_connection, $p_add_session_id, $p_search_engine_safe);
		}
		
		return $t_url;
	}
	
	
	function get_content_url($p_content_group, $p_content_title = '', $p_languages_id = false, $p_parameters = '', $p_connection = 'NONSSL', $p_add_session_id = true, $p_search_engine_safe = true)
	{
		$t_url = '';
		
		$c_type = (string)$p_type;
		$c_content_group = (int)$p_content_group;
		$c_content_title = (string)$p_content_title;
		$c_languages_id = (int)$_SESSION['languages_id'];
		if($p_languages_id !== false)
		{
			$c_languages_id = (int)$p_languages_id;
		}
		
		if($this->v_coo_seo_boost->boost_content && $p_search_engine_safe)
		{				
			$t_content_id = $this->v_coo_seo_boost->get_content_id_by_content_group($c_content_group, $c_languages_id);
			$t_url = xtc_href_link($this->v_coo_seo_boost->get_boosted_content_url($t_content_id, $c_languages_id), $p_parameters, $p_connection, $p_add_session_id);
		}
		else
		{
			$t_parameter = (string)$p_parameters;
			if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			{
				$t_sql = "SELECT content_title
							FROM " . TABLE_CONTENT_MANAGER . "
							WHERE
								content_group = '" . $c_content_group . "' AND
								languages_id = '" . $c_languages_id . "'
							LIMIT 1";
				$t_result = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_result_array = xtc_db_fetch_array($t_result);
					$c_content_title = $t_result_array['content_title'];
				}

				$t_parameter = '&content=' . xtc_cleanName($c_content_title);
			}				

			$t_url = xtc_href_link(FILENAME_CONTENT, 'coID=' . $c_content_group . $t_parameter, $p_connection, $p_add_session_id, $p_search_engine_safe);
		}
		
		return $t_url;
	}
	
}

?>