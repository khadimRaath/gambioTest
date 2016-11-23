<?php
/* --------------------------------------------------------------
   IndexFeatureProductFinder.inc.php 2015-06-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once (DIR_FS_INC.'xtc_get_currencies_values.inc.php');

/**
 * class IndexFeatureProductFinder
 *
 */
//class IndexFeatureProductFinder extends FeatureProductFinder
class IndexFeatureProductFinder
{
	var $v_categories_id_array = array();
	var $v_feature_value_id_array = array();

	var $v_feature_value_group_array = array();

	var $v_price_range_start = false;
	var $v_price_range_end = false;

	function IndexFeatureProductFinder()
	{
	}

	/**
	 * ids for inclusive-OR operations
	 *
	 * @param int p_categories_id
	 * @param bool p_recursively
	 * @return
	 * @access public
	 */
	function add_categories_id($p_categories_id, $p_recursively=true)
	{
		$c_categories_id = (int)$p_categories_id;
		$this->v_categories_id_array[] = $c_categories_id;

		/*
		# add sub-categories?
		if($p_recursively == true)
		{
			$t_search_array = array(
									'parent_id'	=> $c_categories_id,
									'categories_status' => '1'
								);
			# look for sub-categories
			$coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('categories', $t_search_array));
			$t_data_object_array = $coo_data_object_group->get_data_objects_array();

			for($i=0; $i<sizeof($t_data_object_array); $i++)
			{
				# add found sub-categories
				$t_sub_categories_id = $t_data_object_array[$i]->get_data_value('categories_id');
				$this->add_categories_id($t_sub_categories_id, true);
			}
		}
		*/
	} // end of member function add_categories_id



	function add_feature_value_group($p_feature_value_group)
	{
		$this->v_feature_value_group_array[] = $p_feature_value_group;
	}

	function get_feature_value_group_array()
	{
		return $this->v_feature_value_group_array;
	}


	/**
	 * ids for AND operations
	 *
	 * @param int p_feature_value_id
	 * @return
	 * @access public
	 */
	function add_feature_value_id($p_feature_value_id)
	{
		$c_feature_value_id = (int)$p_feature_value_id;
		$this->v_feature_value_id_array[] = $c_feature_value_id;
	}
	// end of member function add_feature_value_id



	function get_products_listing_sql_string($p_where_part = '', $p_order_by = '')
	{
		$c_where_part = '';
		if(is_string($p_where_part))
		{
			$c_where_part = $p_where_part;
		}

		$c_order_by = '';
		$t_order_by_price = false;
		if(is_string($p_order_by))
		{
			$c_order_by = $p_order_by;

			if(strpos($c_order_by, 'p.products_price') !== false)
			{
				$c_order_by = str_replace('p.products_price', 'final_price', $c_order_by);
				$t_order_by_price = true;
			}
		}

		$t_categories_id_array = $this->get_categories_id_array();

		$t_category_id                  = (int)$_GET['feature_categories_id'];
		$t_show_cat_sub_products        = xtc_db_query('SELECT `show_sub_products` FROM `categories` WHERE `categories_id` = '
		                                               . $t_category_id);

		$t_show_cat_sub_products_result = 1;
		if(xtc_db_num_rows($t_show_cat_sub_products))
		{
			$t_row = xtc_db_fetch_array($t_show_cat_sub_products);
			$t_show_cat_sub_products_result = (int)$t_row['show_sub_products'];
		}
		
		$t_include_sub_categories       = ($t_show_cat_sub_products_result === 1) ? true : false;

		$t_feature_value_group_array = $this->get_feature_value_group_array();
		$t_feature_value_search = $this->get_feature_index_search_string($t_feature_value_group_array);

		$t_sql_price_parts_array = $this->get_sql_price_parts_array($t_order_by_price);

		$t_feature_sql_parts = array();
		if($t_include_sub_categories)
		{
			$t_categories_search = $this->get_categories_index_search_string($t_categories_id_array);

			$t_feature_sql_parts['FROM'] = ' LEFT JOIN categories_index AS ci ON (ci.products_id = p.products_id) ';
			$t_feature_sql_parts['WHERE'] = 'AND ci.categories_index LIKE "' . $t_categories_search . '"';
		}
		else
		{
			$t_feature_sql_parts['FROM'] = ' LEFT JOIN products_to_categories AS ptc ON (ptc.products_id = p.products_id) ';
			$t_feature_sql_parts['WHERE'] = ' AND ptc.categories_id = ' . $t_categories_id_array[0] . ' ';
		}
		
		
		$t_output_sql = '
						SELECT DISTINCT
							p.products_fsk18,
							p.products_shippingtime,
							p.products_model,
							p.products_ean,
							pd.products_name,
							m.manufacturers_name,
							p.products_quantity,
							p.products_image,
							p.products_weight,
							p.gm_show_weight,
							pd.products_short_description,
							pd.products_description,
							pd.gm_alt_text,
							pd.products_meta_description,
							p.products_id,
							p.manufacturers_id,
							p.products_price,
							p.products_vpe,
							p.products_vpe_status,
							p.products_vpe_value,
							p.products_discount_allowed,
							p.products_tax_class_id
							' . $t_sql_price_parts_array['SELECT'] . '
						FROM
							'.TABLE_PRODUCTS.' AS p
								LEFT JOIN '.TABLE_PRODUCTS_DESCRIPTION.' AS pd ON (pd.products_id = p.products_id)
								LEFT JOIN '.TABLE_MANUFACTURERS.' AS m ON (m.manufacturers_id = p.manufacturers_id)
								LEFT JOIN '.TABLE_SPECIALS.' AS s ON (s.products_id = p.products_id)
								LEFT JOIN feature_set_to_products AS fstp ON (fstp.products_id = p.products_id) 
								LEFT JOIN feature_index AS fi ON (fstp.feature_set_id = fi.feature_set_id) 
								' . $t_feature_sql_parts['FROM'] . 
								$t_sql_price_parts_array['FROM'].'
						WHERE
							p.products_status = 1
							AND pd.language_id = "'.(int)$_SESSION['languages_id'].'"
							' . $t_feature_sql_parts['WHERE'] . '
		                    ' . $t_feature_value_search . 
							$t_sql_price_parts_array['WHERE'].
							$c_where_part.
							$c_order_by
                        ;
		
		if(is_object($GLOBALS['coo_debugger'])) $GLOBALS['coo_debugger']->log('get_products_listing_sql_string() SQL: '. $t_output_sql, 'IndexFeatureProductFinder');
		return $t_output_sql;
	}

	function get_sql_price_parts_array($p_order_by_price = false)
	{
		$t_output_array = array('FROM' => '', 'WHERE' => '');

		$t_start = $this->get_price_range_start();
		$t_end = $this->get_price_range_end();

		$t_tax_factor = '';
		$t_tax_sql_select_part = '';
		$t_tax_sql_from_part = '';
		$t_tax_sql_where_part = '';

		if(($_SESSION['customers_status']['customers_status_show_price_tax'] != 0) && ($p_order_by_price || !empty($t_start) || !empty($t_end)))
		{
			$t_tax_sql_select_part = ',
										ROUND(IF(s.status = "1" AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price)  * (IF(p.products_tax_class_id = 0,0,tax_rate)/100+1), 2) AS final_price';

			if(!isset ($_SESSION['customer_country_id']))
			{
				$_SESSION['customer_country_id'] = STORE_COUNTRY;
				$_SESSION['customer_zone_id'] = STORE_ZONE;
			}
			$t_tax_factor = ' * (IF(p.products_tax_class_id = 0, 0, tax_rate)/100+1)';
			$t_tax_sql_from_part = '
										LEFT JOIN '.TABLE_TAX_RATES.' AS tr ON (p.products_tax_class_id = tr.tax_class_id OR p.products_tax_class_id = 0)
										LEFT JOIN '.TABLE_ZONES_TO_GEO_ZONES.' AS gz ON (tr.tax_zone_id = gz.geo_zone_id AND gz.zone_country_id = "'. (int)$_SESSION['customer_country_id'] .'")
									';
			$t_tax_sql_where_part = " AND (gz.zone_id = '0' OR gz.zone_id = '".(int) $_SESSION['customer_zone_id']."') ";
		}
		else 
		{
			$t_tax_sql_select_part = ',
										ROUND(IF(s.status = "1" AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price), 2) AS final_price';
		}
		$t_output_array['SELECT'] = $t_tax_sql_select_part;
		$t_output_array['FROM'] = $t_tax_sql_from_part;
		$t_output_array['WHERE'] .= $t_tax_sql_where_part;

		$t_rate = xtc_get_currencies_values($_SESSION['currency']);
		$t_rate = $t_rate['value'];

		if(!empty($t_start))
		{
			$t_start = $t_start / $t_rate;
			$t_output_array['WHERE'] .= " AND (ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) ".$t_tax_factor.", 2) >= ".xtc_db_input($t_start).") ";
		}
		if(!empty($t_end))
		{
			$t_end = $t_end / $t_rate;
			$t_output_array['WHERE'] .= " AND (ROUND(IF(s.status = '1' AND p.products_id = s.products_id, s.specials_new_products_price, p.products_price) ".$t_tax_factor.", 2) <= ".xtc_db_input($t_end).") ";
		}

		return $t_output_array;
	}


	function get_categories_index_search_string($p_id_array)
	{
		sort($p_id_array);

		# target: -4--5--8--13-
		# search: %-5-%-13-%
		for($i=0; $i<sizeof($p_id_array); $i++)
		{
			# prepare and clean value for search
			$p_id_array[$i] = '-'.addslashes($p_id_array[$i]).'-';
		}
		$t_index_search = implode('%', $p_id_array);
		$t_index_search = '%'.$t_index_search.'%';

		return $t_index_search;
	}

	function get_feature_index_search_string($p_feature_value_group_array)
	{
		$t_index_search_output = '';
		$t_index_search_parts_array = array();

		if (is_object($GLOBALS['coo_debugger']))
		{
			$GLOBALS['coo_debugger']->log('get_index_search_string() $p_feature_value_group_array: ' . print_r($p_feature_value_group_array, true), 'IndexFeatureProductFinder');
		}

		foreach($p_feature_value_group_array as $t_feature_value_group)
		{
			$t_id_array = $t_feature_value_group['FEATURE_VALUE_ID_ARRAY'];
			sort($t_id_array);

			# skip array, if the only one value is empty
			if(sizeof($t_id_array) == 1 && $t_id_array[0] == '') continue;

			for($i=0; $i<sizeof($t_id_array); $i++)
			{
				# prepare and clean value for search
				$t_id_array[$i] = '-'.addslashes($t_id_array[$i]).'-';
			}

			# will be filled with AND- or OR-operations
			$t_search_part = '';

			if($t_feature_value_group['VALUE_CONJUNCTION'] == true)
			{
				# AND-search
				# target: -4--5--8--13-
				# search: LIKE "%-5-%-13-%"
				$t_search_part = implode('%', $t_id_array);
				$t_search_part = ' fi.feature_value_index LIKE "%'.$t_search_part.'%" ';
			}
			else
			{
				# OR-search
				# target: -4--5--8--13-
				# search: REGEXP (".*(-5-|-7-).*");
				$t_search_part = implode('|', $t_id_array);
				$t_search_part = ' fi.feature_value_index REGEXP ("'.$t_search_part.'") ';
			}
			$t_index_search_parts_array[] = $t_search_part;
		}
		if(sizeof($t_index_search_parts_array) > 0)
		{
			$t_index_search_output = implode(' AND ', $t_index_search_parts_array);
			$t_index_search_output = ' AND '. $t_index_search_output;
		}
		return $t_index_search_output;
	}


	function get_categories_id_array()
	{
		return $this->v_categories_id_array;
	}

	function get_feature_value_id_array()
	{
		return $this->v_feature_value_id_array;
	}

	/**
	 *
	 *
	 * @param array p_fields_limiter_array
	 * @return assoc_array
	 * @access public
	 */
	function get_products_array( $p_fields_limiter_array = null )
	{

	} // end of member function get_products_array


	function set_price_range_start($p_price)
	{
		$this->v_price_range_start = (float)$p_price;
	}

	function get_price_range_start()
	{
		return $this->v_price_range_start;
	}

	function set_price_range_end($p_price)
	{
		$this->v_price_range_end = (float)$p_price;
	}

	function get_price_range_end()
	{
		return $this->v_price_range_end;
	}
}
