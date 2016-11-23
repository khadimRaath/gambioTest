<?php
/* --------------------------------------------------------------
   CSVSource.inc.php 2016-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'admin/includes/classes/pclzip.lib.php');

/**
 * Description of CSVSource
 */
class CSVSource extends BaseClass
{
	static protected $coo_instance = null;
	static protected $get_instance_called = false;
	protected $v_limit_row_count = 300;
	protected $v_stop_cronjob_filename = 'stop_cronjob';
	protected $v_pause_cronjob_filename = 'pause_cronjob';
	protected $v_cache_data_array = array();
	protected $v_product_ids_array = array();
	protected $v_num_rows = 0;
	protected $v_cache_key = 'export_progress';
	public $v_passes_array = array();

	protected $v_coo_csv_function_lib = null;
	protected $v_coo_csv_import_function_lib = null;
	protected $v_handle = null;
	protected $v_keyword_array = array();
	protected $v_entry_exists_array = array();
	protected $v_properties_array = array();
	protected $v_additional_fields_array = array();
	protected $v_current_import_product_id = 0;
	protected $v_current_combi_property_ids_array = array();
	protected $v_img_nrs = array();
	public $v_scheme_model_array = array();
	public $v_export_file_handle = false;
	public $v_import_quote = '';

	static public function get_instance()
	{
		if(self::$coo_instance === null)
		{
			self::$get_instance_called = true;
			self::$coo_instance = MainFactory::create_object('CSVSource');
		}

		return self::$coo_instance;
	}

	public function __construct()
	{
		if(self::$get_instance_called === false || self::$coo_instance !== null)
		{
			trigger_error('CSVSource is a singleton. Use CSVSource::get_instance() instead of CSVSource::__construct().', E_USER_ERROR);
		}

		$this->init();
	}

	private function __clone() {}

	protected function init()
	{
		$this->v_passes_array['main']['pass'] = 0;
		$this->v_passes_array['main']['rows'] = 0;
		$this->v_passes_array['attributes']['pass'] = 0;
		$this->v_passes_array['attributes']['rows'] = 0;

		$this->v_keyword_array = array('XTSOL');
	}

	public function set_function_library($p_scheme_id)
	{
		$t_cache_data = $this->get_cache_data();
		$this->v_coo_csv_function_lib = MainFactory::create_object('CSVFunctionLibrary', array($this->get_scheme($p_scheme_id), $t_cache_data['properties'], $t_cache_data['attributes'], $t_cache_data['additional_fields']));
	}


	public function reset_schemes()
	{
		$this->v_scheme_model_array = array();
	}


	public function get_schemes($p_reload = false)
	{
		// get all schemes from table
		// foreach row = new SchemeModel(row)
		// return array
		if($p_reload)
		{
			$this->reset_schemes();
		}

		if(empty($this->v_scheme_model_array))
		{
			$t_sql = "SELECT scheme_id FROM export_schemes ORDER BY scheme_name";
			$t_query = xtc_db_query($t_sql);
			while($t_query_result = xtc_db_fetch_array($t_query))
			{
				$coo_scheme_model = MainFactory::create_object('CSVSchemeModel', array($t_query_result['scheme_id']));
				$this->v_scheme_model_array[$t_query_result['scheme_id']] = $coo_scheme_model;
			}
		}

		return $this->v_scheme_model_array;
	}

	public function get_scheme($p_scheme_id)
	{
		if(empty($this->v_scheme_model_array))
		{
			$this->get_schemes();
		}
		return $this->v_scheme_model_array[(int) $p_scheme_id];
	}

	public function get_schemes_by_type( $p_type )
	{
		// get all schemes from table
		// foreach row = new SchemeModel(row)
		// return array
		$t_schemes_array = array();

		$t_sql = "SELECT scheme_id FROM export_schemes WHERE type_id = '" . (int)$p_type . "' ORDER BY scheme_name";
		$t_query = xtc_db_query($t_sql);
		while($t_query_result = xtc_db_fetch_array($t_query))
		{
			$coo_scheme_model = MainFactory::create_object('CSVSchemeModel', array($t_query_result['scheme_id']));
			$t_schemes_array[$t_query_result['scheme_id']] = $coo_scheme_model;
		}

		return $t_schemes_array;
	}

	public function delete_fields_by_fields_array( $p_scheme_id, $p_field_array, $p_invert = false)
	{
		$c_scheme_id = (int)$p_scheme_id;
		if(is_array( $p_field_array ) && count( $p_field_array ) > 0 )
		{
			$t_sql_not_string = '';
			if($p_invert == false)
			{
				$t_sql_not_string = 'NOT';
			}
			$t_sql = "DELETE FROM export_scheme_fields WHERE scheme_id = '" . $c_scheme_id . "' AND field_id " . $t_sql_not_string . " IN (" . implode( $p_field_array, "," ) . ")";
		}
		else
		{
			$t_sql = "DELETE FROM export_scheme_fields WHERE scheme_id = '" . $c_scheme_id . "'";
		}
		xtc_db_query($t_sql);
		return true;
	}


	public function get_secure_token()
	{
		$t_token = LogControl::get_secure_token();
		$t_token = md5($t_token);

		return $t_token;
	}


	public function get_export_data($p_scheme_id, $p_products_count = false)
	{
		$t_return = false;

		switch($this->v_scheme_model_array[$p_scheme_id]->v_data_array['type_id'])
		{
			case 1:
				$t_return = $this->get_products_export_data($p_scheme_id, $p_products_count);
				break;
			case 2:
				$t_return = $this->get_portal_export_data($p_scheme_id, $p_products_count);
				break;
		}

		return $t_return;
	}


	protected function get_portal_export_data($p_scheme_id, $p_products_count = false)
	{
		static $t_manufacturers_array;
		static $t_google_export_availability_array;
		static $t_shipping_status_array;
		static $t_vpe_array;
		static $t_products_count_array;

		if(!empty($p_products_count))
		{
			$this->v_limit_row_count = (int)$p_products_count;
		}

		$t_limit_part = '';
		if($this->v_limit_row_count > 0)
		{
			$t_limit_part = ' LIMIT ' . $this->get_limit_offset($p_scheme_id) . ', ' . $this->v_limit_row_count;
		}

		// manufacturer data
		if($t_manufacturers_array === null)
		{
			$t_manufacturers_array = array();
			$t_sql = "SELECT * FROM " . TABLE_MANUFACTURERS;
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_manufacturers_array[$t_result_array['manufacturers_id']] = $t_result_array;
			}
		}

		// google product availability data
		if($t_google_export_availability_array === null)
		{
			$t_google_export_availability_array = array();
			$t_sql = "SELECT * FROM google_export_availability";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_google_export_availability_array[$t_result_array['google_export_availability_id']] = $t_result_array;
			}
		}

		// delivery status data
		if($t_shipping_status_array === null)
		{
			$t_shipping_status_array = array();
			$t_sql = "SELECT 
							sg.*,
							g.*,
							s.*
						FROM 
							" . TABLE_SHIPPING_STATUS . " s
						LEFT JOIN shipping_status_to_google_availability sg ON (s.shipping_status_id = sg.shipping_status_id)
						LEFT JOIN google_export_availability g ON (sg.google_export_availability_id = g.google_export_availability_id)
						WHERE s.language_id = '" . (int)$this->v_scheme_model_array[$p_scheme_id]->v_data_array['languages_id'] . "'";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_shipping_status_array[$t_result_array['shipping_status_id']] = $t_result_array;
			}
		}

		// VPE data
		if($t_vpe_array === null)
		{
			$t_vpe_array = array();
			$t_sql = "SELECT * FROM " . TABLE_PRODUCTS_VPE . " WHERE language_id = '" . (int)$this->v_scheme_model_array[$p_scheme_id]->v_data_array['languages_id'] . "'";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_vpe_array[$t_result_array['products_vpe_id']] = $t_result_array;
			}
		}

		$t_group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$t_group_check = " AND p.group_permission_" . (int)$this->v_scheme_model_array[$p_scheme_id]->v_data_array['customers_status_id'] . " = '1' ";
		}

		// product data
		if(!isset($t_products_count_array[$p_scheme_id]))
		{
			$t_sql = $this->get_main_sql_query(2, '', $p_scheme_id, true, $t_group_check);
			$t_result = xtc_db_query($t_sql);
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_products_count_array[$p_scheme_id] = $t_result_array['products_count'];
		}

		$t_sql = $this->get_main_sql_query(2, $t_limit_part, $p_scheme_id, false, $t_group_check);
		$this->build_product_ids_array($t_sql);
		$t_result = xtc_db_query($t_sql);
		$this->v_passes_array['main']['rows'] = xtc_db_num_rows($t_result);
		if($this->v_passes_array['main']['pass'] < $this->v_passes_array['main']['rows'])
		{
			mysqli_data_seek($t_result,  $this->v_passes_array['main']['pass']);
			$t_data_array = xtc_db_fetch_array($t_result);

			$t_google_export_availability_id = 0;
			if(!empty($t_data_array['google_export_availability_id']))
			{
				$t_google_export_availability_id = $t_data_array['google_export_availability_id'];
			}

			if(!empty($t_data_array['manufacturers_id']) && isset($t_manufacturers_array[$t_data_array['manufacturers_id']]))
			{
				$t_data_array = array_merge($t_data_array, $t_manufacturers_array[$t_data_array['manufacturers_id']]);
			}

			if(!empty($t_data_array['products_shippingtime']) && isset($t_shipping_status_array[$t_data_array['products_shippingtime']]))
			{
				$t_data_array = array_merge($t_data_array, $t_shipping_status_array[$t_data_array['products_shippingtime']]);
			}

			if(!empty($t_google_export_availability_id) && isset($t_google_export_availability_array[$t_google_export_availability_id]))
			{
				$t_data_array = array_merge($t_data_array, $t_google_export_availability_array[$t_google_export_availability_id]);
			}

			if(!empty($t_data_array['products_vpe']) && !empty($t_data_array['products_vpe_status']) && (double)$t_data_array['products_vpe_value'] > 0 && isset($t_vpe_array[$t_data_array['products_vpe']]))
			{
				$t_data_array = array_merge($t_data_array, $t_vpe_array[$t_data_array['products_vpe']]);
			}

			if(!empty($this->v_scheme_model_array[$p_scheme_id]->v_data_array['export_attributes']))
			{
				$t_attributes_sql = "SELECT 
											a.*,
											o.*,
											ov.*
										FROM 
											" . TABLE_PRODUCTS_ATTRIBUTES . " a,
											" . TABLE_PRODUCTS_OPTIONS . " o,
											" . TABLE_PRODUCTS_OPTIONS_VALUES . " ov
										WHERE 
											a.products_id = '" . (int)$t_data_array['products_id'] . "' AND
											a.options_id = o.products_options_id AND
											o.language_id = '" . (int)$this->v_scheme_model_array[$p_scheme_id]->v_data_array['languages_id'] . "' AND
											a.options_values_id = ov.products_options_values_id AND
											ov.language_id = '" . (int)$this->v_scheme_model_array[$p_scheme_id]->v_data_array['languages_id'] . "'";
				$t_attributes_result = xtc_db_query($t_attributes_sql);
				$this->v_passes_array['attributes']['rows'] = xtc_db_num_rows($t_attributes_result);
				if($this->v_passes_array['attributes']['pass'] < $this->v_passes_array['attributes']['rows'])
				{
					mysqli_data_seek($t_attributes_result,  $this->v_passes_array['attributes']['pass']);
					$t_attributes_result_array = xtc_db_fetch_array($t_attributes_result);
					$t_data_array = array_merge($t_data_array, $t_attributes_result_array);

					if(!empty($t_data_array['products_vpe_id']) && (double)$t_data_array['gm_vpe_value'] > 0 && isset($t_vpe_array[$t_data_array['products_vpe_id']]))
					{
						$t_data_array = array_merge($t_data_array, $t_vpe_array[$t_data_array['products_vpe_id']]);
					}

					$this->v_passes_array['attributes']['pass']++;
					if($this->v_passes_array['attributes']['pass'] == $this->v_passes_array['attributes']['rows'])
					{
						$this->v_passes_array['attributes']['pass'] = 0;
						$this->v_passes_array['main']['pass']++;
					}

					return $t_data_array;
				}
			}

			$this->v_passes_array['main']['pass']++;

			return $t_data_array;
		}
		elseif($this->v_passes_array['main']['rows'] < $this->v_limit_row_count || $p_products_count !== false)
		{
			$this->set_limit_offset($p_scheme_id, 0, $t_products_count_array[$p_scheme_id]);

			if($this->is_cronjob())
			{
				$t_next_scheme_id = $this->get_next_scheme_id($p_scheme_id);
				$this->save_cronjob_scheme_id($t_next_scheme_id);
			}
		}
		else
		{
			$t_offset = $this->get_limit_offset($p_scheme_id);
			$t_new_offset = $t_offset + $this->v_limit_row_count;
			$this->set_limit_offset($p_scheme_id, $t_new_offset, $t_products_count_array[$p_scheme_id]);
		}

		return false;
	}

	protected function get_products_export_data($p_scheme_id, $p_products_count = false)
	{
		static $t_manufacturers_array;
		static $t_google_export_availability_array;
		static $t_shipping_status_array;
		static $t_vpe_array;
		static $t_products_count_array;

		if(!empty($p_products_count))
		{
			$this->v_limit_row_count = (int)$p_products_count;
		}

		$t_limit_part = '';
		if($this->v_limit_row_count > 0)
		{
			$t_limit_part = ' LIMIT ' . $this->get_limit_offset($p_scheme_id) . ', ' . $this->v_limit_row_count;
		}

		// manufacturer data
		if($t_manufacturers_array === null)
		{
			$t_manufacturers_array = array();

			$t_sql_data_array = $this->build_multilingual_sql_data(TABLE_MANUFACTURERS_INFO, 'm.manufacturers_id = {manufacturers_id} AND {languages_id} = {$languages_id}');
			$t_sql = "SELECT 
						m.*,
						" . $t_sql_data_array['select'] . "
						FROM 
							" . TABLE_MANUFACTURERS . " m
						" . $t_sql_data_array['from'] . "
						GROUP BY m.manufacturers_id";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_manufacturers_array[$t_result_array['manufacturers_id']] = $t_result_array;
			}
		}

		// google product availability data
		if($t_google_export_availability_array === null)
		{
			$t_google_export_availability_array = array();
			$t_sql = "SELECT * FROM google_export_availability";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_google_export_availability_array[$t_result_array['google_export_availability_id']] = $t_result_array;
			}
		}

		// delivery status data
		if($t_shipping_status_array === null)
		{
			$t_shipping_status_array = array();
			$t_sql_data_array = $this->build_multilingual_sql_data(TABLE_SHIPPING_STATUS, 's.shipping_status_id = {shipping_status_id} AND {language_id} = {$languages_id}');
			$t_sql = "SELECT 
							sg.*,
							g.*,
							s.*,
							" . $t_sql_data_array['select'] . "
						FROM 
							" . TABLE_SHIPPING_STATUS . " s
						" . $t_sql_data_array['from'] . "
						LEFT JOIN shipping_status_to_google_availability sg ON (s.shipping_status_id = sg.shipping_status_id)
						LEFT JOIN google_export_availability g ON (sg.google_export_availability_id = g.google_export_availability_id)
						GROUP BY s.shipping_status_id";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_shipping_status_array[$t_result_array['shipping_status_id']] = $t_result_array;
			}
		}

		// VPE data
		if($t_vpe_array === null)
		{
			$t_vpe_array = array();
			$t_sql_data_array = $this->build_multilingual_sql_data(TABLE_PRODUCTS_VPE, 'v.products_vpe_id = {products_vpe_id} AND {language_id} = {$languages_id}');

			$t_sql = "SELECT 
							v.*,
							" . $t_sql_data_array['select'] . "
						FROM 
							" . TABLE_PRODUCTS_VPE . " v
						" . $t_sql_data_array['from'] . "
						GROUP BY v.products_vpe_id";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_vpe_array[$t_result_array['products_vpe_id']] = $t_result_array;
			}
		}

		// product data
		if(!isset($t_products_count_array[$p_scheme_id]))
		{
			$t_sql = $this->get_main_sql_query(1, '', $p_scheme_id, true);
			$t_result = xtc_db_query($t_sql);
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_products_count_array[$p_scheme_id] = $t_result_array['products_count'];
		}

		$t_sql = $this->get_main_sql_query(1, $t_limit_part, $p_scheme_id, false);
		$this->build_product_ids_array($t_sql);
		$t_result = xtc_db_query($t_sql);
		$this->v_passes_array['main']['rows'] = xtc_db_num_rows($t_result);
		if($this->v_passes_array['main']['pass'] < $this->v_passes_array['main']['rows'])
		{
			mysqli_data_seek($t_result,  $this->v_passes_array['main']['pass']);
			$t_data_array = xtc_db_fetch_array($t_result);
			
			$t_google_export_availability_id = 0;
			if(!empty($t_data_array['google_export_availability_id']))
			{
				$t_google_export_availability_id = $t_data_array['google_export_availability_id'];
			}

			if(!empty($t_data_array['manufacturers_id']) && isset($t_manufacturers_array[$t_data_array['manufacturers_id']]))
			{
				$t_data_array = array_merge($t_data_array, $t_manufacturers_array[$t_data_array['manufacturers_id']]);
			}

			if(!empty($t_data_array['products_shippingtime']) && isset($t_shipping_status_array[$t_data_array['products_shippingtime']]))
			{
				$t_data_array = array_merge($t_data_array, $t_shipping_status_array[$t_data_array['products_shippingtime']]);
			}

			if(!empty($t_google_export_availability_id) && isset($t_google_export_availability_array[$t_google_export_availability_id]))
			{
				$t_data_array = array_merge($t_data_array, $t_google_export_availability_array[$t_google_export_availability_id]);
			}

			if(!empty($t_data_array['products_vpe']) && !empty($t_data_array['products_vpe_status']) && (double)$t_data_array['products_vpe_value'] > 0 && isset($t_vpe_array[$t_data_array['products_vpe']]))
			{
				$t_data_array = array_merge($t_data_array, $t_vpe_array[$t_data_array['products_vpe']]);
			}

			$this->v_passes_array['main']['pass']++;

			return $t_data_array;
		}
		elseif($this->v_passes_array['main']['rows'] < $this->v_limit_row_count || $p_products_count !== false)
		{
			$this->set_limit_offset($p_scheme_id, 0, $t_products_count_array[$p_scheme_id]);

			if($this->is_cronjob())
			{
				$t_next_scheme_id = $this->get_next_scheme_id($p_scheme_id);
				$this->save_cronjob_scheme_id($t_next_scheme_id);
			}
		}
		else
		{
			$t_offset = $this->get_limit_offset($p_scheme_id);
			$t_new_offset = $t_offset + $this->v_limit_row_count;
			$this->set_limit_offset($p_scheme_id, $t_new_offset, $t_products_count_array[$p_scheme_id]);
		}

		return false;
	}

	protected function get_main_sql_query($p_type_id, $p_limit_part, $p_scheme_id, $p_count = false, $p_group_check = '')
	{
		$t_categories_ids = array_keys($this->v_scheme_model_array[$p_scheme_id]->get_categories_array());
		$t_language_id = $this->v_scheme_model_array[$p_scheme_id]->v_data_array['languages_id'];
		$t_quantity_minimum = $this->v_scheme_model_array[$p_scheme_id]->v_data_array['quantity_minimum'];
		$t_export_properties = (int)$this->v_scheme_model_array[$p_scheme_id]->v_data_array['export_properties'];

		$t_additional_field_ids = array();
		$t_sql = 'SELECT afd.additional_field_id '
				 . 'FROM additional_fields af, additional_field_descriptions afd '
				 . 'WHERE af.item_type LIKE "product" AND '
				 . 'af.additional_field_id = afd.additional_field_id AND '
				 . 'afd.language_id = ' . $t_language_id;
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_additional_field_ids[] = $t_row['additional_field_id'];
		}

		switch($p_type_id)
		{
			case 2:

				if($p_count)
				{
					$t_select = 'COUNT(*) AS products_count';
				}
				else
				{
					$t_select = 'pic.*,
								pqu.*,
								s.*,
								p.*,
								' . ($t_export_properties == 1 ? '
								ppc.products_properties_combis_id,
								ppc.sort_order AS combi_sort_order,
								ppc.combi_model,
								ppc.combi_ean,
								ppc.combi_quantity,
								ppc.combi_shipping_status_id,
								ppc.combi_weight,
								ppc.combi_price_type,
								ppc.combi_price,
								ppc.combi_image,
								ppc.products_vpe_id AS combi_vpe_id,
								ppc.vpe_value AS combi_vpe_value,' : '') . '
								pd.*';

					foreach($t_additional_field_ids as $t_additional_field_id)
					{
						$t_select .= ', afvd' . $t_additional_field_id . '.value AS additional_field_value_' . $t_additional_field_id;
					}
				}

				$t_additional_field_join = '';
				foreach($t_additional_field_ids as $t_additional_field_id)
				{
					$t_additional_field_join .= ' LEFT JOIN additional_field_values afv' . $t_additional_field_id . ' ON (afv' . $t_additional_field_id . '.item_id = p.products_id AND afv' . $t_additional_field_id . '.additional_field_id = ' . $t_additional_field_id . ') ';
					$t_additional_field_join .= ' LEFT JOIN additional_field_value_descriptions afvd' . $t_additional_field_id . ' ON (afvd' . $t_additional_field_id . '.additional_field_value_id = afv' . $t_additional_field_id . '.additional_field_value_id AND (afvd' . $t_additional_field_id . '.language_id = 0 OR afvd' . $t_additional_field_id . '.language_id = ' . $t_language_id . '))';
				}

				$t_sql = "SELECT DISTINCT
								" . $t_select . "
							FROM
								" . TABLE_PRODUCTS_TO_CATEGORIES . " ptc, 
								export_scheme_categories esc, 
								" . TABLE_PRODUCTS . " p 
							LEFT JOIN " . TABLE_SPECIALS . " s ON (p.products_id = s.products_id)
							" . $t_additional_field_join . "
							LEFT JOIN products_item_codes pic ON (p.products_id = pic.products_id)
							LEFT JOIN products_quantity_unit pqu ON (p.products_id = pqu.products_id)
							" . ($t_export_properties == 1 ? "LEFT JOIN products_properties_combis ppc ON (p.products_id = ppc.products_id)" : "") . ",
								" . TABLE_PRODUCTS_DESCRIPTION . " pd							
							WHERE
								p.products_id = pd.products_id AND 
								p.products_status = '1' AND 
								(" . ($t_export_properties == 1 ? " ppc.combi_quantity >= '".$t_quantity_minimum."' OR " : "") . "
								p.products_quantity >= '" . $t_quantity_minimum . "') AND
								p.products_id = ptc.products_id AND 
								" . (!empty($t_categories_ids) ? "ptc.categories_id IN ('" . implode("','", (array)$t_categories_ids) . "') AND " : "1 = 0 AND ") . "
								esc.scheme_id = " . $p_scheme_id . " AND
								esc.selection_state LIKE 'self_%' AND
								esc.categories_id = ptc.categories_id AND
								pd.language_id = '" . (int)$t_language_id . "' 						
							" . (string)$p_group_check . ' ORDER BY p.products_id '
						 . (string)$p_limit_part;
				break;
			case 1:

				$t_sql_data_array = $this->build_multilingual_sql_data(TABLE_PRODUCTS_DESCRIPTION, 'p.products_id = {products_id} AND {language_id} = {$languages_id}');
				$t_id_string = '';
				if($p_count)
				{
					$t_select = 'COUNT(*) AS products_count';
				}
				else
				{
					$t_query = 'SELECT DISTINCT
								p.products_id
								' . ($t_export_properties == 1 ? ",ppc.products_properties_combis_id" : "") . '
							FROM
								' . TABLE_PRODUCTS_TO_CATEGORIES . ' ptc, 
								export_scheme_categories esc, 
								' . TABLE_PRODUCTS . ' p
							' . ($t_export_properties == 1 ? "LEFT JOIN products_properties_combis ppc ON (p.products_id = ppc.products_id)" : "") . '
							WHERE
								p.products_id = ptc.products_id AND 
								' . (!empty($t_categories_ids) ? "ptc.categories_id IN ('" . implode("','", (array)$t_categories_ids) . "') AND " : "1 = 0 AND ") . '
								esc.scheme_id = ' . $p_scheme_id . ' AND
								esc.selection_state LIKE "self_%" AND
								esc.categories_id = ptc.categories_id
							ORDER BY p.products_id '
							   . (string)$p_limit_part;

					$t_result = xtc_db_query($t_query);
					if(xtc_db_num_rows($t_result) > 0)
					{
						$t_id_string_array = array();
						while($t_row = mysqli_fetch_array($t_result))
						{
							if(isset($t_row['products_properties_combis_id']) && (int)$t_row['products_properties_combis_id'] != 0)
							{
								$t_id_string_array[] = '(p.products_id = "' . $t_row['products_id'] . '" AND ppc.products_properties_combis_id = "' . (int)$t_row['products_properties_combis_id'] . '")';
							}
							else
							{
								$t_id_string_array[] = '(p.products_id = "' . $t_row['products_id'] . '")';
							}
						}
						$t_id_string = 'WHERE ' . implode(' OR ', $t_id_string_array);
					}
					else
					{
						$t_id_string = 'WHERE p.products_id="0"';
					}

					$t_select = 'pic.*,
								pqu.*,
								s.*,
								p.*,
								' . ($t_export_properties == 1 ? '
								ppc.products_properties_combis_id,
								ppc.sort_order AS combi_sort_order,
								ppc.combi_model,
								ppc.combi_ean,
								ppc.combi_quantity,
								ppc.combi_shipping_status_id,
								ppc.combi_weight,
								ppc.combi_price_type,
								ppc.combi_price,
								ppc.combi_image,
								ppc.products_vpe_id AS combi_vpe_id,
								ppc.vpe_value AS combi_vpe_value,' : '') . '
								' . $t_sql_data_array['select'];
				}

				$t_sql = "SELECT DISTINCT
								" . $t_select . "
							FROM
								" . TABLE_PRODUCTS . " p 
							LEFT JOIN " . TABLE_SPECIALS . " s ON (p.products_id = s.products_id)
							LEFT JOIN products_item_codes pic ON (p.products_id = pic.products_id)
							LEFT JOIN products_quantity_unit pqu ON (p.products_id = pqu.products_id)
							" . ($t_export_properties == 1 ? "LEFT JOIN products_properties_combis ppc ON (p.products_id = ppc.products_id)" : "") . "
							" . $t_sql_data_array['from'] . $t_id_string . ' ORDER BY p.products_id ';

				break;
		}

		return $t_sql;
	}

	/**
	 * Builds a formatted data array with all field values needed for the given scheme
	 * @param object $p_coo_scheme_model Configurations for the export
	 * @param string[][] $p_data_array All data
	 * @return string[][] The formatted data array
	 */
	public function build_scheme_data_array($p_coo_scheme_model_id, &$p_data_array, $p_preview_content = false)
	{
		$t_scheme_data = array();
		$t_fields = $this->v_scheme_model_array[$p_coo_scheme_model_id]->v_fields_array;
		$this->v_coo_csv_function_lib->set_product_ids_array($this->v_product_ids_array);
		foreach ($p_data_array as $t_data_set)
		{
			$t_scheme_data[] = array();
			$t_last_index = count($t_scheme_data) - 1;

			$t_products_properties_combis_id = 0;

			if(isset($t_data_set['products_properties_combis_id']))
			{
				$t_products_properties_combis_id = $t_data_set['products_properties_combis_id'];
			}

			$this->v_coo_csv_function_lib->_get_combi_value_names($t_products_properties_combis_id);

			foreach ($t_fields as $t_field_id => $coo_field)
			{
				$t_default_field_value = $coo_field->v_data_array['field_content_default'];
				$t_scheme_data[$t_last_index][$coo_field->v_data_array['field_name']] = $this->v_coo_csv_function_lib->get_data($t_data_set, $coo_field->v_data_array['field_content'], $t_default_field_value, $p_preview_content);
			}
		}

		// no data -> create empty data row
		if(empty($t_scheme_data))
		{
			$t_scheme_data[] = array();
			$t_last_index = count($t_scheme_data) - 1;
			foreach ($t_fields as $coo_field)
			{
				$t_scheme_data[$t_last_index][$coo_field->v_data_array['field_name']] = '';
			}
		}

		return $t_scheme_data;
	}

	public function load_properties_fields($p_coo_scheme_model_id)
	{
		$t_sql = 'SELECT languages_id, code
					FROM languages';
		$t_result = xtc_db_query($t_sql);
		$t_languages_array = array();
		while ($t_row = xtc_db_fetch_array($t_result))
		{
			$t_languages_array[$t_row['languages_id']] = $t_row['code'];
		}

		$t_scheme_properties_array = $this->v_scheme_model_array[$p_coo_scheme_model_id]->get_properties_array();

		if( count( $t_scheme_properties_array ) > 0 )
		{
			foreach($t_scheme_properties_array AS $t_key => $t_value)
			{
				$coo_field_model = MainFactory::create_object( 'CSVFieldModel' );

				$t_field_name = $t_value;
				$t_field_content = '{' . $t_value . '}';

				if(is_numeric($t_value))
				{
					$t_properties_array = $this->get_properties_array();
					$t_language_id = $this->v_scheme_model_array[$p_coo_scheme_model_id]->v_data_array['languages_id'];

					if( trim( $t_properties_array[$t_value]['names'][$t_language_id]['properties_admin_name'] ) != '' )
					{
						$properties_name = $t_properties_array[$t_value]['names'][$t_language_id]['properties_admin_name'] . ' (' . $t_properties_array[$t_value]['names'][$t_language_id]['properties_name'] . ')';
					}
					else
					{
						$properties_name = $t_properties_array[$t_value]['names'][$t_language_id]['properties_name'];
					}

					$t_field_name = 'Eigenschaft: ' . $properties_name . '.' . $t_languages_array[$t_language_id] . ' [' . $t_value . ']';
					$t_field_content = '{property#' . $t_value . '.' . $t_languages_array[$t_language_id] . '}';
				}

				$t_data_array = array();
				$t_data_array['field_id'] = 0;
				$t_data_array['scheme_id'] = $p_coo_scheme_model_id;
				$t_data_array['field_name'] = $t_field_name;
				$t_data_array['field_content'] = $t_field_content;
				$t_data_array['field_content_default'] = '';
				$t_data_array['created_by'] = '';
				$t_data_array['sort_order'] = count($this->v_scheme_model_array[$p_coo_scheme_model_id]->v_fields_array) + 1;

				$coo_field_model->set_data_array( $t_data_array );
				$this->v_scheme_model_array[$p_coo_scheme_model_id]->v_fields_array[] = $coo_field_model;
			}
		}

		return true;
	}

	public function open_export_file($p_scheme_id, $p_new_file)
	{
		$t_write_mode = 'a';

		if ($p_new_file)
		{
			$t_write_mode = 'w';
		}

		$this->v_export_file_handle = fopen($this->v_scheme_model_array[$p_scheme_id]->get_base_path() . 'tmp_' . basename($this->v_scheme_model_array[$p_scheme_id]->v_data_array['filename']), $t_write_mode);
	}

	public function close_export_file()
	{
		fclose($this->v_export_file_handle);
		$this->v_export_file_handle = false;
	}

	public function create_field_definition($p_output_type, $p_scheme_id)
	{
		switch ($p_output_type)
		{
			case 'csv':
				return $this->create_csv_field_definition($p_scheme_id);
			default:
				return false;
		}
	}

	protected function create_csv_field_definition($p_scheme_id)
	{
		$t_fields = $this->v_scheme_model_array[$p_scheme_id]->get_sorted_field_names();
		$t_separator = $this->v_scheme_model_array[$p_scheme_id]->v_data_array['field_separator'];
		$t_quotes = $this->v_scheme_model_array[$p_scheme_id]->v_data_array['field_quotes'];

		foreach($t_fields AS $t_key => $t_field)
		{
			$t_fields[$t_key] = $this->escape_enclosure_character($t_field, $t_quotes);
		}

		$t_separator = str_replace('\t', "\t", $t_separator);

		$t_csv = implode($t_quotes . $t_separator . $t_quotes, $t_fields);
		$t_csv = $t_quotes . $t_csv . $t_quotes . "\n";
		return $t_csv;
	}

	/**
	 *
	 * @param type $p_output_type
	 * @param type $p_coo_scheme_model
	 * @param type $p_data_array
	 * @param type $p_new_file
	 * @param type $p_filehandle
	 * @return boolean
	 */
	public function transform_to_export($p_output_type, $p_scheme_id, &$p_data_array, $p_new_file)
	{
		switch ($p_output_type)
		{
			case 'csv':
				return $this->transform_to_csv($p_scheme_id, $p_data_array, $p_new_file);
			default:
				return false;
		}
	}

	/**
	 *
	 * @param type $p_coo_scheme_model
	 * @param type $p_data_array
	 * @param type $p_new_file
	 * @param type $p_filehandle
	 * @return int
	 */
	protected function transform_to_csv($p_scheme_id, &$p_data_array, $p_new_file)
	{
		$t_return_value = '';
		$t_success = true;
		$t_field_names = $this->v_scheme_model_array[$p_scheme_id]->get_sorted_field_names();
		$t_quotes = $this->v_scheme_model_array[$p_scheme_id]->v_data_array['field_quotes'];
		$t_separator = $this->v_scheme_model_array[$p_scheme_id]->v_data_array['field_separator'];

		$t_separator = str_replace('\t', "\t", $t_separator);

		if ($p_new_file)
		{
			$t_data_array = array();
			$t_data_array[0] = array();

			foreach ($t_field_names as $t_field_name)
			{
				$t_data_array[0][$t_field_name] = $t_field_name;
			}

			$t_formatted = $this->build_csv_line($t_data_array, $t_field_names, $t_separator, $t_quotes);

			$t_success &= $this->write_data_set($t_formatted, $t_return_value);
		}

		$t_formatted = $this->build_csv_line($p_data_array, $t_field_names, $t_separator, $t_quotes);
		$t_success &= $this->write_data_set($t_formatted, $t_return_value);

		return $t_return_value;
	}

	protected function build_csv_line(&$p_data_array, &$p_field_names, &$p_delimiter, &$p_enclosure)
	{
		$t_field_names_count = count($p_field_names);
		$t_formatted = '';

		foreach ($p_data_array as $t_data_row)
		{
			foreach ($p_field_names as $t_field_name)
			{
				$t_formatted .= $p_enclosure . $this->escape_enclosure_character($t_data_row[$t_field_name], $p_enclosure) . $p_enclosure;

				if ($t_field_name != $p_field_names[$t_field_names_count - 1])
				{
					$t_formatted .= $p_delimiter;
				}
				else
				{
					$t_formatted .= "\n";
				}
			}
		}

		return $t_formatted;
	}

	protected function escape_enclosure_character(&$p_string, &$p_enclosure)
	{
		return str_replace($p_enclosure, $p_enclosure . $p_enclosure, $p_string);
	}

	protected function write_data_set(&$p_data_set, &$p_content)
	{
		$success = true;
		if ($this->v_export_file_handle !== false)
		{
			$success = fwrite($this->v_export_file_handle, $p_data_set);
		}
		else
		{
			$p_content .= $p_data_set;
		}
		return $success;
	}

	public function transform_to_import($p_input_type, $p_scheme_id, $p_data_array=array(), $p_filepath='')
	{
		switch ($p_input_type)
		{
			case 'csv':
				return $this->transform_from_csv($p_scheme_id, $p_data_array, $p_filepath);
			default:
				return false;
		}
	}

	protected function transform_from_csv($p_scheme_id, $p_data_array, $p_filepath)
	{
		$t_keys = array();
		$t_data_array = array();
		$t_separator = $this->v_scheme_model_array[$p_scheme_id]->v_data_array['field_separator'];
		$t_quotes = $this->v_scheme_model_array[$p_scheme_id]->v_data_array['field_quotes'];

		$t_separator = str_replace('\t', "\t", $t_separator);

		if ($p_filepath != '')
		{
			$p_data_array = file($p_filepath);
		}

		if (!empty($p_data_array))
		{
			$t_keys = $this->explode($p_data_array[0], $t_separator, $t_quotes);

			for ($i = 1; $i < count($p_data_array); $i++)
			{
				if (trim($p_data_array[$i]) == '' && !empty($t_data_array))
				{
					continue;
				}
				$t_values = $this->explode($p_data_array[$i], $t_separator, $t_quotes);

				for ($j = 0; $j < count($t_keys); $j++)
				{
					$t_data_array[$i - 1][$t_keys[$j]] = $t_values[$j];
				}
			}
		}

		return $t_data_array;
	}

	public function explode($p_string, $p_delimiter, $p_enclosure = '')
	{
		$t_result_array = array();
		$c_string = trim((string)$p_string);
		$c_delimiter = (string)$p_delimiter;
		if($c_delimiter == '')
		{
			$c_delimiter = '|';
		}
		$c_enclosure = (string)$p_enclosure;

		if(version_compare(PHP_VERSION, '5.3.0', '>='))
		{
			$t_result_array = str_getcsv($c_string, $c_delimiter, $c_enclosure);
		}
		else
		{
			$t_string_len = strlen_wrapper($c_string);

			if($t_string_len > 0 && $c_enclosure !== '')
			{
				$t_enclosure_len = strlen_wrapper($c_enclosure);
				$t_delimiter_len = strlen_wrapper($c_delimiter);
				$t_next_delimiter = $c_delimiter;
				$t_start_pos = 0;
				$t_result_found = false;

				while(!isset($t_split_pos) || $t_split_pos !== false)
				{
					$t_add_enclosure_len = 0;
					if(substr_wrapper($c_string, $t_start_pos, $t_enclosure_len) == $c_enclosure)
					{
						$t_next_delimiter = $c_enclosure . $c_delimiter;
						$t_add_enclosure_len = $t_enclosure_len;
					}

					if(substr_wrapper($c_string, $t_start_pos, $t_delimiter_len) == $c_delimiter) // column is empty, no enclosure
					{
						$t_split_pos = $t_start_pos;
					}
					elseif($t_string_len >= $t_start_pos + $t_delimiter_len) // column has value or empty column is wrapped by enclosure
					{
						$t_split_pos = strpos_wrapper($c_string, $t_next_delimiter, $t_start_pos + $t_delimiter_len);
					}
					else // last column is empty
					{
						$t_split_pos = false;
						$t_result_found = true;
						$t_result_array[] = '';
					}

					if($t_split_pos !== false)
					{
						$t_end_pos = $t_split_pos + $t_add_enclosure_len;
						$t_is_split_pos = $this->is_split_pos($c_string, $c_enclosure, $t_start_pos, $t_end_pos);

						while($t_is_split_pos === false && $t_split_pos !== false)
						{
							$t_split_pos = strpos_wrapper($c_string, $t_next_delimiter, $t_split_pos + $t_delimiter_len);
							if($t_split_pos !== false)
							{
								$t_end_pos = $t_split_pos + $t_add_enclosure_len;
								$t_is_split_pos = $this->is_split_pos($c_string, $c_enclosure, $t_start_pos, $t_end_pos);
							}
						}

						if($t_is_split_pos === true)
						{
							$t_result_array[] = $this->clean_column_value(substr_wrapper($c_string, $t_start_pos, $t_end_pos - $t_start_pos), $c_enclosure, $t_string_len, $t_enclosure_len);
							$t_result_found = true;
						}
					}

					// last column
					if($t_result_found == false && $t_start_pos < strlen_wrapper($c_string))
					{
						$t_result_array[] = $this->clean_column_value(substr_wrapper($c_string, $t_start_pos, strlen_wrapper($c_string) - $t_start_pos), $c_enclosure, $t_string_len, $t_enclosure_len);
					}

					$t_start_pos = $t_end_pos + $t_delimiter_len;
					$t_next_delimiter = $c_delimiter;
					$t_result_found = false;
				}
			}
			elseif(strpos_wrapper($c_string, $c_delimiter) !== false)
			{
				$t_result_array = explode($c_delimiter, $c_string);
			}
			elseif($t_string_len > 0)
			{
				$t_result_array[] = $c_string;
			}
		}

		return $t_result_array;
	}

	protected function is_split_pos(&$p_string, &$p_enclosure, &$p_start_pos, &$p_end_pos)
	{
		$t_string = substr_wrapper($p_string, $p_start_pos, $p_end_pos - $p_start_pos);

		$t_enclosure_count = substr_count_wrapper($t_string, $p_enclosure);

		return ($t_enclosure_count % 2 == 0);
	}

	protected function clean_column_value(&$p_string, &$p_enclosure, &$p_string_len, &$p_enclosure_len)
	{
		$t_cleaned_string = $p_string;

		if($p_string_len >= $p_enclosure_len * 2 && substr_wrapper($p_string, 0, $p_enclosure_len) == $p_enclosure && substr_wrapper($p_string, $p_enclosure_len * -1) == $p_enclosure)
		{
			$t_cleaned_string = substr_wrapper($p_string, $p_enclosure_len, $p_enclosure_len * -1);
			$t_cleaned_string = str_replace($p_enclosure.$p_enclosure, $p_enclosure, $t_cleaned_string);
		}

		return $t_cleaned_string;
	}

	public function get_export_status( $p_scheme_id )
	{
		$c_scheme_id = (int)$p_scheme_id;
		$t_export_status = array();

		$t_progress_array = $this->get_cache_data();

		$t_user = 'admin';
		// cronjob
		if($this->is_cronjob())
		{
			$t_user = 'cronjob';
		}

		if(isset($t_progress_array[$t_user][$c_scheme_id]['offset']))
		{
			$t_export_status = $t_progress_array[$t_user][$c_scheme_id];
		}

		return $t_export_status;
	}

	public function get_limit_offset($p_scheme_id)
	{
		$c_scheme_id = (int)$p_scheme_id;
		$t_offset = 0;

		$t_progress_array = $this->get_cache_data();

		$t_user = 'admin';
		// cronjob
		if($this->is_cronjob())
		{
			$t_user = 'cronjob';
		}

		if(isset($t_progress_array[$t_user][$c_scheme_id]['offset']))
		{
			$t_offset = (int)$t_progress_array[$t_user][$c_scheme_id]['offset'];
		}

		return $t_offset;
	}

	public function set_limit_offset($p_scheme_id, $p_offset, $p_products_count)
	{
		$c_scheme_id = (int)$p_scheme_id;
		$c_offset = (int)$p_offset;
		$c_products_count = (int)$p_products_count;

		$t_progress_array = array();

		$t_progress_array = $this->get_cache_data();

		$t_user = 'admin';
		// cronjob
		if($this->is_cronjob())
		{
			$t_user = 'cronjob';
		}

		$t_progress_array[$t_user][$c_scheme_id]['offset'] = $c_offset;
		$t_progress_array[$t_user][$c_scheme_id]['products_count'] = $c_products_count;

		$this->set_cache_data($t_progress_array);
	}

	protected function get_cache_key()
	{
		return basename($this->v_cache_key);
	}

	public function get_child_categories($p_parent_id, $p_scheme_id, $p_levels=1, $p_customers_group=0, $p_include_inactive=false)
	{
		$t_categories = array();

		if ($p_levels <= 0 || $p_parent_id == 0)
		{
			return $t_categories;
		}

		$t_sql = "SELECT *
				  FROM export_scheme_categories
				  WHERE scheme_id = " . $p_scheme_id .
				 " AND categories_id = 0";

		$t_top_result = xtc_db_query($t_sql);

		if ($p_parent_id == -1)
		{
			if ($t_category = xtc_db_fetch_array($t_top_result))
			{
				$t_top_state = $t_category['selection_state'];
			}
			else
			{
				$t_top_state = 'no_self_no_sub';
			}

			$t_categories[0] = array();
			$t_categories[0]['categories_id'] = 0;
			$t_categories[0]['categories_name'] = 'Top';
			$t_categories[0]['state'] = $t_top_state;
			$p_parent_id = 0;
		}

		$t_group_check = '';
		if(GROUP_CHECK == 'true')
		{
			$t_group_check = " AND c.group_permission_" . (int)$p_customers_group . " = 1 ";
		}

		$t_sql = "SELECT 
					c.*,
					esc.selection_state,
					cd.categories_name
				FROM
					categories c
						LEFT JOIN categories_description cd ON (c.categories_id = cd.categories_id)
						LEFT JOIN export_scheme_categories esc ON (c.categories_id = esc.categories_id AND esc.scheme_id = '" . $p_scheme_id . "')
				WHERE 
					c.parent_id = " . (int) $p_parent_id . " AND 
					cd.language_id = " . (int) $_SESSION['languages_id'] .
				 $t_group_check .
				 (!$p_include_inactive ? " AND c.categories_status = 1" : "") . "
				GROUP BY
					c.categories_id 
				ORDER BY 
					c.sort_order, 
					cd.categories_name";

		$t_result = xtc_db_query($t_sql);

		while ($t_category = xtc_db_fetch_array($t_result))
		{
			$t_cat_id = $t_category['categories_id'];
			$t_categories[] = array();
			$t_categories[count($t_categories) - 1]['categories_id'] = $t_cat_id;
			$t_categories[count($t_categories) - 1]['categories_name'] = $t_category['categories_name'];
			$t_categories[count($t_categories) - 1]['state'] = (empty($t_category['selection_state'])) ? 'no_self_no_sub' : $t_category['selection_state'];
			$t_categories[count($t_categories) - 1]['children'] = $this->get_child_categories($t_cat_id, $p_scheme_id, $p_levels - 1, $p_customers_group, $p_include_inactive);
		}

		return $t_categories;
	}

	public function get_cronjob_scheme_ids()
	{
		$t_scheme_ids_array = array();

		$t_sql = "SELECT
						s.scheme_id
					FROM 
						export_schemes s,
						export_cronjobs c
					WHERE 
						s.scheme_id = c.scheme_id AND
						s.cronjob_allowed = '1' AND						
						(s.date_last_export < c.due_date OR
							s.date_last_export = '1000-01-01 00:00:00') AND
						c.due_date <= NOW()
					GROUP BY s.scheme_id";
		$t_result = xtc_db_query( $t_sql, 'db_link', false );

		while( $t_row = xtc_db_fetch_array( $t_result ) )
		{
			$t_scheme_ids_array[] = $t_row[ 'scheme_id' ];
		}

		return $t_scheme_ids_array;
	}

	protected function get_next_scheme_id($p_current_scheme_id)
	{
		$t_next_scheme_id = 0;
		$t_scheme_ids_array = $this->get_cronjob_scheme_ids();
		if(isset($t_scheme_ids_array[0]))
		{
			$t_next_scheme_id = $t_scheme_ids_array[0];

			for($i = 0; $i < count($t_scheme_ids_array); $i++)
			{
				if($t_scheme_ids_array[$i] == $p_current_scheme_id && isset($t_scheme_ids_array[$i+1]))
				{
					$t_next_scheme_id = $t_scheme_ids_array[$i+1];
					break;
				}
			}
		}

		return $t_next_scheme_id;
	}

	protected function save_cronjob_scheme_id($p_scheme_id)
	{
		$c_scheme_id = (int)$p_scheme_id;

		if($this->is_cronjob())
		{
			$t_progress_array = array();

			$t_progress_array = $this->get_cache_data();

			$t_progress_array['cronjob']['current_scheme_id'] = $c_scheme_id;

			$this->set_cache_data($t_progress_array);
		}
	}

	public function get_current_scheme_id()
	{
		$t_scheme_id = 0;

		if($this->is_cronjob())
		{
			$t_progress_array = $this->get_cache_data();
			if(isset($t_progress_array['cronjob']['current_scheme_id']))
			{
				$t_scheme_id = $t_progress_array['cronjob']['current_scheme_id'];
			}
		}

		if($t_scheme_id == 0)
		{
			$t_scheme_ids_array = $this->get_cronjob_scheme_ids();
			if(isset($t_scheme_ids_array[0]))
			{
				$t_scheme_id = $t_scheme_ids_array[0];
			}
		}

		return $t_scheme_id;
	}

	public function is_cronjob()
	{
		if(empty($_SESSION['customer_id']))
		{
			return true;
		}

		return false;
	}

	public function reset_cache($p_user)
	{
		$c_user = (string)$p_user;

		$t_progress_array = $this->get_cache_data();
		unset($t_progress_array[$c_user]);
		$this->set_cache_data($t_progress_array);
	}

	protected function get_pause_cronjob_filename()
	{
		return basename($this->v_pause_cronjob_filename);
	}

	protected function get_pause_cronjob_filepath()
	{
		return DIR_FS_CATALOG . 'cache/' . $this->get_pause_cronjob_filename();
	}

	public function cronjob_paused()
	{
		if(file_exists($this->get_pause_cronjob_filepath()))
		{
			return true;
		}

		return false;
	}

	public function pause_cronjob($p_status)
	{
		if( $p_status == 'false' && file_exists($this->get_pause_cronjob_filepath()) )
		{
			@unlink( $this->get_pause_cronjob_filepath() );
		}
		else if( $p_status == 'true' )
		{
			file_put_contents( $this->get_pause_cronjob_filepath(), ' ' );
		}

		return true;
	}

	protected function get_stop_cronjob_filename()
	{
		return basename($this->v_stop_cronjob_filename);
	}

	protected function get_stop_cronjob_filepath()
	{
		return DIR_FS_CATALOG . 'cache/' . $this->get_stop_cronjob_filename();
	}

	public function cronjob_stopped()
	{
		if(file_exists($this->get_stop_cronjob_filepath()))
		{
			return true;
		}

		return false;
	}

	public function cronjob_allowed( $p_status )
	{
		if( $p_status == "true" && file_exists($this->get_stop_cronjob_filepath()))
		{
			@unlink( $this->get_stop_cronjob_filepath() );
		}
		else if( $p_status == "false" )
		{
			file_put_contents( $this->get_stop_cronjob_filepath(), ' ' );

			$this->reset_cache('cronjob');
		}
		return true;
	}

	protected function get_cache_filepath()
	{
		return DIR_FS_CATALOG . 'cache/' . $this->get_cache_key() . '-' . LogControl::get_secure_token();
	}

	protected function get_cache_data()
	{
		if(count($this->v_cache_data_array) == 0)
		{
			if(file_exists($this->get_cache_filepath()))
			{
				$t_cache_data = file_get_contents($this->get_cache_filepath());
				$t_cache_data_array = unserialize($t_cache_data);
				if(is_array($t_cache_data_array))
				{
					$this->v_cache_data_array = $t_cache_data_array;
				}
			}
		}

		return $this->v_cache_data_array;
	}

	protected function set_cache_data($p_data_array)
	{
		if(is_array($p_data_array))
		{
			$t_cache_data_array = array();

			$t_cache_data_array = array_merge($t_cache_data_array, $p_data_array);
			$t_cache_data = serialize($t_cache_data_array);
			if(!file_exists($this->get_cache_filepath()) || is_writable($this->get_cache_filepath()))
			{
				file_put_contents($this->get_cache_filepath(), $t_cache_data);
			}
			else
			{
				trigger_error('cannot write cache data, because cache file is not writable: ' . $this->get_cache_filepath(), E_USER_ERROR);
			}

			$this->v_cache_data_array = $t_cache_data_array;

			return true;
		}

		return false;
	}

	protected function add_cache_data($p_data_array)
	{
		if(is_array($p_data_array))
		{
			$t_cache_data_array = array_merge($this->get_cache_data(), $p_data_array);
			return $this->set_cache_data($t_cache_data_array);
		}

		return false;
	}

	public function get_cronjob_status_array($p_scheme_id)
	{
		$c_scheme_id = (int)$p_scheme_id;
		$t_cache_data = $this->get_cache_data();

		return $this->v_scheme_model_array[$c_scheme_id]->get_cronjob_status_array($t_cache_data);
	}

	public function get_properties_array( $p_get_values = true )
	{
		if (!empty($this->v_properties_array) && $p_get_values)
		{
			return $this->v_properties_array;
		}

		$c_get_values = (boolean)$p_get_values;
		$t_properties_array = array();

		$t_select = "
					SELECT
						properties_id, 
						properties_name, 
						properties_admin_name, 
						language_id
					FROM
						properties_description";

		$t_result = xtc_db_query($t_select);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			if(!is_array($t_properties_array[$t_row['properties_id']]))
			{
				$t_properties_array[$t_row['properties_id']] = array();
			}

			$t_properties_array[$t_row['properties_id']]['properties_id'] = $t_row['properties_id'];

			if(!is_array($t_properties_array[$t_row['properties_id']]['names']))
			{
				$t_properties_array[$t_row['properties_id']]['names'] = array();
			}

			$t_properties_array[$t_row['properties_id']]['names'][$t_row['language_id']] = array();
			$t_properties_array[$t_row['properties_id']]['names'][$t_row['language_id']]['properties_name'] = $t_row['properties_name'];
			$t_properties_array[$t_row['properties_id']]['names'][$t_row['language_id']]['properties_admin_name'] = $t_row['properties_admin_name'];
		}

		if( $c_get_values )
		{
			$t_select = "
						SELECT
							pv.properties_id,
							pv.properties_values_id,
							pvd.language_id,
							pvd.values_name
						FROM
							properties_description AS pd,
							properties_values AS pv
						LEFT JOIN
							properties_values_description AS pvd USING( properties_values_id )
						WHERE
							pd.properties_id = pv.properties_id AND
							pv.properties_values_id = pvd.properties_values_id OR
							pv.properties_values_id IS NULL";

			$t_result = xtc_db_query($t_select);

			while ($t_row = xtc_db_fetch_array($t_result))
			{
				if (!is_array($t_properties_array[$t_row['properties_id']]['properties_values']))
				{
					$t_properties_array[$t_row['properties_id']]['properties_values'] = array();
				}

				if (!is_array($t_properties_array[$t_row['properties_id']]['properties_values'][$t_row['properties_values_id']]))
				{
					$t_properties_array[$t_row['properties_id']]['properties_values'][$t_row['properties_values_id']] = array();
				}

				if (!is_array($t_properties_array[$t_row['properties_id']]['properties_values'][$t_row['properties_values_id']][$t_row['language_id']]))
				{
					$t_properties_array[$t_row['properties_id']]['properties_values'][$t_row['properties_values_id']][$t_row['language_id']] = array();
				}

				$t_properties_array[$t_row['properties_id']]['properties_values'][$t_row['properties_values_id']][$t_row['language_id']]['values_name'] = $t_row['values_name'];
			}
		}

		if ($p_get_values && empty($this->v_properties_array))
		{
			$this->v_properties_array = $t_properties_array;
		}

		return $t_properties_array;
	}

	public function get_additional_fields_array()
	{
		if (!empty($this->v_additional_fields_array))
		{
			return $this->v_additional_fields_array;
		}

		$t_additional_fields_array = array();

		$t_select = "
					SELECT
						*
					FROM
						additional_field_values AS afv
					LEFT JOIN
						additional_field_value_descriptions AS afvd USING (additional_field_value_id)";

		$t_result = xtc_db_query($t_select);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			if(!is_array($t_additional_fields_array[$t_row['additional_field_id']]))
			{
				$t_additional_fields_array[$t_row['additional_field_id']] = array();
			}

			if(!is_array($t_additional_fields_array[$t_row['additional_field_id']][$t_row['item_id']]))
			{
				$t_additional_fields_array[$t_row['additional_field_id']][$t_row['item_id']] = array();
			}

			if(!is_array($t_additional_fields_array[$t_row['additional_field_id']][$t_row['item_id']][$t_row['language_id']]))
			{
				$t_additional_fields_array[$t_row['additional_field_id']][$t_row['item_id']][$t_row['language_id']] = array();
			}

			$t_additional_fields_array[$t_row['additional_field_id']][$t_row['item_id']][$t_row['language_id']]['additional_field_value_id'] = $t_row['additional_field_value_id'];
			$t_additional_fields_array[$t_row['additional_field_id']][$t_row['item_id']][$t_row['language_id']]['value'] = $t_row['value'];
		}

		$this->v_additional_fields_array = $t_additional_fields_array;

		return $t_additional_fields_array;
	}

	public function get_selected_properties_for_products()
	{
		$t_properties_for_products_array = array();

		$t_select = "
					SELECT 
						products_id, properties_id 
					FROM
						products_properties_index 
					GROUP BY products_id, properties_id";

		$t_result = xtc_db_query($t_select);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			if(!is_array($t_properties_for_products_array[$t_row['products_id']]))
			{
				$t_properties_for_products_array[$t_row['products_id']] = array();
			}
			$t_properties_for_products_array[$t_row['products_id']][] = $t_row['properties_id'];
		}

		return $t_properties_for_products_array;
	}

	public function get_selected_properties_by_products_id($p_products_id)
	{
		$c_products_id = (int) $p_products_id;

		if($c_products_id == $this->v_current_import_product_id)
		{
			return $this->v_current_combi_property_ids_array;
		}
		else
		{
			$this->v_current_combi_property_ids_array = array();
		}

		$t_select = "
					SELECT 
						properties_id 
					FROM
						products_properties_index 
					WHERE
						products_id = " . $c_products_id . " 
					GROUP BY products_id, properties_id";

		$t_result = xtc_db_query($t_select);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$this->v_current_combi_property_ids_array[] = $t_row['properties_id'];
		}

		$this->v_current_import_product_id = $c_products_id;

		return $this->v_current_combi_property_ids_array;
	}

	// p_condition: 'p.products_id = {products_id} AND {language_id} = {$languages_id}'
	protected function build_multilingual_sql_data($p_table, $p_condition)
	{
		$c_table = (string)$p_table;
		$t_languages_array = array();
		$t_field_names_array = array();
		$t_sql_data_array = array();
		$t_multilingual_select = '';
		$t_multilingual_from = '';

		$t_sql = "SELECT languages_id, code	FROM " . TABLE_LANGUAGES;
		$t_result = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$t_languages_array[$t_result_array['languages_id']] = $t_result_array['code'];
		}

		$t_sql = "DESCRIBE `" . $c_table . "`";
		$t_result = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$t_field_names_array[] = $t_result_array['Field'];
		}

		$t_fields_array = array();

		foreach($t_languages_array AS $t_language_id => $t_code)
		{
			foreach($t_field_names_array AS $t_field_name)
			{
				$t_fields_array[] = $c_table . '_' . $t_code . '.' . $t_field_name . ' AS `' . $t_field_name . '.' . $t_code . '`';
			}

			$t_multilingual_from = ' LEFT JOIN ' . $c_table . ' AS ' . $c_table . '_' . $t_code . ' ON (';
			$t_condition = str_replace('{$languages_id}', $t_language_id, $p_condition);
			preg_match_all( '/{([^{]+)}/', $t_condition, $t_matches );
			foreach( $t_matches[1] AS $t_column )
			{
				$t_condition = str_replace('{' . $t_column. '}', $c_table . '_' . $t_code . '.' . $t_column, $t_condition);
			}

			$t_sql_data_array['from'] .= $t_multilingual_from . $t_condition . ") \n";
		}

		if(!empty($t_fields_array))
		{
			$t_multilingual_select = implode(",\n", $t_fields_array);
		}

		$t_sql_data_array['select'] = $t_multilingual_select;


		return $t_sql_data_array;
	}

	public function get_export_types()
	{
		$coo_language_manager = MainFactory::create_object('LanguageTextManager', array('export_schemes', $_SESSION['languages_id']));

		$t_export_types = array();
		$t_sql = 'SELECT * FROM export_types WHERE language_id = "' . $_SESSION['languages_id'] . '"';
		$t_result = xtc_db_query( $t_sql );
		while( $t_row = xtc_db_fetch_array( $t_result ) )
		{
			if($t_row['type_id'] == 1)
			{
				$t_export_types[ $t_row[ 'type_id' ] ][ 'name' ] = $coo_language_manager->get_text('product_export');
			}
			elseif($t_row['type_id'] == 2)
			{
				$t_export_types[ $t_row[ 'type_id' ] ][ 'name' ] = $coo_language_manager->get_text('price_comparison');
			}
			else
			{
				// DEPRECATED
				$t_export_types[ $t_row[ 'type_id' ] ][ 'name' ] = $t_row[ 'name' ];
			}
		}
		return $t_export_types;
	}

	protected function build_product_ids_array($p_sql)
	{
		if(empty($this->v_product_ids_array))
		{
			$t_result = xtc_db_query($p_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				if(!empty($t_result_array['products_id']))
				{
					$this->v_product_ids_array[] = $t_result_array['products_id'];
				}
			}

			$this->v_product_ids_array = array_unique($this->v_product_ids_array);
		}
	}

	public function reset_product_ids_array()
	{
		$this->v_product_ids_array = array();

		if(is_object($this->v_coo_csv_function_lib))
		{
			$t_empty_array = array();
			$this->v_coo_csv_function_lib->set_product_ids_array($t_empty_array);
		}
	}

	public function upload()
	{
		$t_success = false;

		if( isset($_FILES['upload_import_file']['name']) && !empty( $_FILES['upload_import_file']['name'] ) )
		{
			$t_file_parts = pathinfo($_FILES['upload_import_file']['name']);
			$t_file_extension = strtolower( $t_file_parts['extension'] );
			if(in_array($t_file_extension, array('csv', 'txt', 'zip')))
			{
				$t_filename = basename(gm_prepare_filename($_FILES['upload_import_file']['name']));

				if(strlen_wrapper($t_filename) > 0 && substr_wrapper($t_filename, 0, 1) != '.')
				{
					if( file_exists( DIR_FS_CATALOG . "import/" . $t_filename ) )
					{
						@unlink( DIR_FS_CATALOG . "import/" . $t_filename );
					}

					@move_uploaded_file( $_FILES['upload_import_file']['tmp_name'], DIR_FS_CATALOG . "import/" . $t_filename );
					@chmod( DIR_FS_CATALOG . "import/" . $t_filename, 0777 );

					if( file_exists( DIR_FS_CATALOG . "import/" . $t_filename ) )
					{
						// check if zipped
						if($t_file_extension == 'zip')
						{
							$coo_zip = new PclZip(DIR_FS_CATALOG . 'import/' . $t_filename);
							$t_zip_content_array = $coo_zip->listContent();

							if(is_array($t_zip_content_array))
							{
								foreach($t_zip_content_array as $t_file)
								{
									$t_extension = strtolower(strrchr($t_file['filename'], '.'));
									if(!in_array($t_extension, array('.csv', '.txt')))
									{
										$t_success = false;
										break;
									}
									else
									{
										$t_success = true;
									}
								}
								if($t_success)
								{
									$this->unzip(DIR_FS_CATALOG . "import/" . $t_filename);
								}
							}

							// delete uploaded zip-file
							@unlink(DIR_FS_CATALOG . "/import/" . $t_filename);
						}
						elseif(in_array($t_file_extension, array('csv', 'txt')))
						{
							$t_success = true;
						}
						else
						{
							@unlink(DIR_FS_CATALOG . "/import/" . $t_filename);
						}

					}
				}
			}
		}
		$t_response = array( 'status' => $t_success, 'filename' => $t_filename );
		return $t_response;
	}

	protected function unzip($p_filepath)
	{
		$coo_zip = new PclZip($p_filepath);
		$coo_zip->extract(PCLZIP_OPT_PATH, DIR_FS_CATALOG . 'import');
	}

	public function read_line($p_file, $p_pointer_position = -1)
	{
		$t_line_content = '';
		$c_pointer_position = (int)$p_pointer_position;
		$c_file = basename($p_file);
		if(strlen_wrapper($c_file) > 0 && substr_wrapper($c_file, 0, 1) != '.')
		{
			if($this->v_handle === null)
			{
				if(file_exists(DIR_FS_CATALOG . 'import/' . $c_file))
				{
					$this->v_handle = fopen(DIR_FS_CATALOG . 'import/' . $c_file, 'r');
				}
				else
				{
					return false;
				}
			}

			$t_fseek = 0;
			if($c_pointer_position >= 0)
			{
				// '0' success, '-1' failure
				$t_fseek = fseek($this->v_handle, $c_pointer_position);
			}
			elseif($this->is_line_end_reached() === false)
			{
				return false;
			}

			if($t_fseek == -1)
			{
				return false;
			}

			// fgets returns false, if end of file is reached
			$t_line_content = fgets($this->v_handle);

			// reset pointer position if end of file is reached
			if($t_line_content === false)
			{
				$this->set_pointer_position(0);
			}
			else
			{
				$t_line_content = trim($t_line_content);

				// cancel if keyword not found
				if($c_pointer_position == 0 && $this->get_pointer_position() == -1 && $this->keyword_found($t_line_content) === false)
				{
					return false;
				}

				if($this->get_pointer_position() == -1  || $c_pointer_position > 0)
				{
					$this->set_pointer_position(ftell($this->v_handle));
				}

				if($this->is_line_end_reached() === false)
				{
					$t_line_content .= $this->read_line($p_file, $this->get_pointer_position());
				}
			}
		}

		return $t_line_content;
	}

	protected function is_line_end_reached()
	{
		$t_line_end_is_reached = true;

		$t_fseek = fseek($this->v_handle, $this->get_pointer_position());
		if($t_fseek != -1)
		{
			$t_line_content = fgets($this->v_handle);

			if(is_string($t_line_content))
			{
				$t_line_end_is_reached = $this->keyword_found($t_line_content);
			}
		}

		// reset pointer position
		fseek($this->v_handle, $this->get_pointer_position());

		return $t_line_end_is_reached;
	}

	public function keyword_found($p_line)
	{
		$t_keyword_found = false;
		$t_keyword_start = strlen_wrapper($this->v_import_quote);

		$t_line_length = strlen_wrapper($p_line);

		foreach($this->v_keyword_array AS $t_keyword)
		{
			$t_keyword_length = strlen_wrapper($t_keyword);

			if($t_line_length > $t_keyword_length && substr_wrapper($p_line, $t_keyword_start, $t_keyword_length) === $t_keyword)
			{
				$t_keyword_found = true;
				break;
			}
		}

		if($t_keyword_found === false)
		{
			foreach($this->v_keyword_array AS $t_keyword)
			{
				$t_keyword_length = strlen_wrapper($t_keyword);

				if($t_line_length > $t_keyword_length && substr_wrapper($p_line, 0, $t_keyword_length) === $t_keyword)
				{
					$t_keyword_found = true;
					break;
				}
			}
		}

		return $t_keyword_found;
	}


	public function close()
	{
		if ($this->v_handle !== null)
		{
			fclose($this->v_handle);
		}
	}

	public function get_pointer_position()
	{
		$t_position = -1;
		if(isset($_SESSION['pointer_position']))
		{
			$t_position = $_SESSION['pointer_position'];
		}

		return $t_position;
	}

	public function set_pointer_position($p_position)
	{
		$_SESSION['pointer_position'] = (int)$p_position;
	}

	public function set_filesize($p_filename)
	{
		$c_filename = basename($p_filename);
		if(strlen_wrapper($c_filename) > 0 && substr_wrapper($c_filename, 0, 1) != '.')
		{
			if(file_exists(DIR_FS_CATALOG . 'import/' . $c_filename))
			{
				$_SESSION['import_filesize'] = filesize(DIR_FS_CATALOG . 'import/' . $c_filename);
			}
		}
	}

	public function get_filesize()
	{
		return $_SESSION['import_filesize'];
	}

	public function set_import_function_lib(&$p_import_data_array)
	{
		$this->v_coo_csv_import_function_lib = MainFactory::create_object('CSVImportFunctionLibrary', array($p_import_data_array));
	}

	public function import_data_set(&$p_data_array)
	{
		$this->v_coo_csv_import_function_lib->clean_data_array();

		foreach ($p_data_array as $t_key => $t_field_value)
		{
			$t_field_name = $this->v_coo_csv_import_function_lib->get_field_name($t_key);
			if (preg_match('/p_img_alt_text\.\d+\..+/', $t_field_name))
			{
				$t_tmp = explode('.', $t_field_name);
				$t_img_nr = $t_tmp[1];
				$this->v_img_nrs[] = $t_img_nr;
			}
			$this->v_coo_csv_import_function_lib->set_field_content($t_field_name, $t_field_value);
		}
		$this->v_img_nrs = array_unique($this->v_img_nrs);
		$t_import_data = $this->v_coo_csv_import_function_lib->get_import_data_array();
		$t_has_properties = false;
		
		if (!$this->validate_data_set($t_import_data, $t_has_properties))
		{
			return false;
		}

		$this->get_existing_entries($t_import_data);

		$this->import_categories($t_import_data);
		$this->import_categories_description($t_import_data);
		$this->import_products($t_import_data);
		$this->import_products_description($t_import_data);
		$this->import_products_item_codes($t_import_data);

		$this->import_products_to_categories($t_import_data);
		$this->import_products_google_categories($t_import_data);
		$this->import_specials($t_import_data);
		$this->import_products_images($t_import_data);

		$this->import_products_quantity_unit($t_import_data);
		$this->import_gm_prd_img_alt($t_import_data);
		$this->import_personal_offers_by_customers_status($t_import_data);

		if($t_has_properties)
		{
			$t_combi_exists = false;

			$p_values_ids_array = array();
			foreach($t_import_data['products_properties_combis_values'] as $t_values)
			{
				$p_values_ids_array[] = (int)$t_values['properties_values_id'];
			}

			$t_count_values = count($p_values_ids_array);

			$t_sql = '
				SELECT
					products_properties_combis_id
				FROM
					products_properties_index
				USE INDEX 
					(products_id_2)
				WHERE 
					products_id = ' . (int)$t_import_data['products']['products_id'] . ' AND
					language_id = ' . (int)$_SESSION['languages_id'] . ' AND
					properties_values_id IN ('.implode(',', $p_values_ids_array).')
				GROUP BY 
					products_properties_combis_id
				HAVING 
					COUNT(*) = '.$t_count_values.'
				ORDER BY
					NULL
				LIMIT
					1
			';

			$result = xtc_db_query($t_sql);
			if(xtc_db_num_rows($result) > 0)
			{
				$t_row = xtc_db_fetch_array($result);
				$t_old_combi_id = $t_row['products_properties_combis_id'];

				if($t_import_data['products_properties_combis']['products_properties_combis_id'] == '' || $t_import_data['products_properties_combis']['products_properties_combis_id'] != $t_old_combi_id )
				{
					$t_combi_exists = true;
				}
			}

			if($t_combi_exists == false)
			{
				$this->import_properties_values($t_import_data);
				$this->import_properties_values_description($t_import_data);
				$this->import_products_properties_combis($t_import_data);
				$this->import_products_properties_combis_values($t_import_data);
				$this->import_products_properties_admin_select($t_import_data);
			}
		}

		$this->import_additional_field_values($t_import_data);
		$this->import_additional_field_value_descriptions($t_import_data);

		return $t_import_data;
	}


	protected function validate_data_set($p_import_data, &$p_has_properties)
	{
		if ($this->product_has_properties($p_import_data) && isset($p_import_data['products']['products_id']))
		{
			$t_selected_properties = $this->get_selected_properties_by_products_id($p_import_data['products']['products_id']);

			// Abgleich: Index und uebergebene Properties
			if (count($t_selected_properties) != 0 &&
				(count($t_selected_properties) != count($p_import_data['products_properties_combis_values']) ||
				 !$this->combi_has_correct_property_values($p_import_data['products_properties_combis_values'])))
			{
				return false;
			}

			foreach ($t_selected_properties as $t_property_id)
			{
				if (!isset($p_import_data['products_properties_combis_values'][$t_property_id]) ||
					empty($p_import_data['products_properties_combis_values'][$t_property_id]['properties_values_id']))
				{
					return false;
				}
			}
		}

		$p_has_properties = $this->product_has_properties($p_import_data);

		return true;
	}

	protected function combi_has_correct_property_values($p_property_values_data)
	{
		$t_properties_array = $this->get_properties_array();

		foreach ($p_property_values_data as $t_property_id => $t_property_values_data)
		{
			if (!array_key_exists($t_property_values_data['properties_values_id'], $t_properties_array[$t_property_id]['properties_values']))
			{
				return false;
			}
		}
		return true;
	}

	public function product_has_properties(&$p_import_data)
	{
		return count($p_import_data['products_properties_combis_values']) > 0;
	}

	protected function get_existing_entries(&$p_import_data)
	{
		if (!empty($p_import_data['products']['products_id']))
		{
			$t_sql_select = "
						SELECT
							p.products_id AS products_exists,
							pic.products_id AS products_item_codes_exists,
							pgc.products_google_categories_id AS products_google_categories_exists,
							s.specials_id AS specials_exists,
							pqu.products_id AS products_quantity_unit_exists";
			$t_sql_from = "
						FROM
							products p
						LEFT JOIN
							products_item_codes AS pic
						ON
							(p.products_id = pic.products_id)
						LEFT JOIN
							products_google_categories AS pgc
						ON
							(p.products_id = pgc.products_id)
						LEFT JOIN
							specials AS s
						ON
							(p.products_id = s.products_id)
						LEFT JOIN
							products_quantity_unit AS pqu
						ON
							(p.products_id = pqu.products_id)";
			$t_sql_where = "
						WHERE
							p.products_id = " . $p_import_data['products']['products_id'];
			$t_sql_limit = "
						LIMIT
							1";

			$t_language_array = $this->v_coo_csv_import_function_lib->get_language_array();
			$t_image_count = count($p_import_data['products_images']);

			for ($i = 1; $i <= $t_image_count; $i++)
			{
				$t_sql_select .= ",
							pi__" . $i . ".image_id AS products_images__" . $i . "_exists";
				$t_sql_from .= "
						LEFT JOIN
							products_images AS pi__" . $i . "
						ON
							(p.products_id = pi__" . $i . ".products_id
						AND
							pi__" . $i . ".image_nr = " . $i . ")";
			}

			$t_table_names = array_keys($p_import_data);
			$t_customer_status_array = array();
			foreach ($t_table_names as $t_table_name)
			{
				if (strpos_wrapper($t_table_name, 'personal_offers_by_customers_status_') === 0)
				{
					$t_customer_status_array[] = substr_wrapper($t_table_name, strrpos($t_table_name, '_') + 1);
				}
			}

			foreach ($t_customer_status_array as $t_status)
			{
				foreach ($p_import_data['personal_offers_by_customers_status_' . $t_status] as $t_quantity => $t_val)
				{
					$t_quantity = (double) $t_quantity;
					$t_sql_select .= ",
							po_" . $t_status . "__" . str_replace('.', '_', $t_quantity) . ".price_id AS personal_offers_by_customers_status_" . $t_status . "__" . str_replace('.', '_', $t_quantity) . "_exists";
					$t_sql_from .= "
						LEFT JOIN
							personal_offers_by_customers_status_" . $t_status . " AS po_" . $t_status . "__" . str_replace('.', '_', $t_quantity) . "
						ON
							(p.products_id = po_" . $t_status . "__" . str_replace('.', '_', $t_quantity) . ".products_id
						AND
							po_" . $t_status . "__" . str_replace('.', '_', $t_quantity) . ".quantity = " . $t_quantity . ")";
				}
			}

			foreach ($t_language_array as $t_language)
			{
				$t_sql_select .= ",
							pd__" . $t_language . ".products_id AS products_description__" . $t_language . "_exists";
				$t_sql_from .= "
						LEFT JOIN
							products_description AS pd__" . $t_language . "
						ON
							(p.products_id = pd__" . $t_language . ".products_id)";

				for ($i = 1; $i <= $t_image_count; $i++)
				{
					$t_sql_select .= ",
							pia__" . $i . "__" . $t_language . ".img_alt_id AS gm_prd_img_alt__" . $i . "__" . $t_language . "_exists";
					$t_sql_from .= "
						LEFT JOIN
							gm_prd_img_alt AS pia__" . $i . "__" . $t_language . "
						ON
							(p.products_id = pia__" . $i . "__" . $t_language . ".products_id
						AND
							pia__" . $i . "__" . $t_language . ".image_id = pi__" . $i . ".image_id
						AND
							pia__" . $i . "__" . $t_language . ".language_id = " . $t_language . ")";
				}
			}

			$t_sql = $t_sql_select . $t_sql_from . $t_sql_where . $t_sql_limit;
			$t_exist_result = xtc_db_query($t_sql, 'db_link', false);

			$this->v_entry_exists_array = xtc_db_fetch_array($t_exist_result);
		}
		else
		{
			$this->v_entry_exists_array = array();
		}
	}


	protected function entry_exists($p_table_name, $p_params = array())
	{
		$t_table = $p_table_name . (!empty($p_params) ? '__' : '') . implode('__', $p_params) . '_exists';
		return isset($this->v_entry_exists_array[$t_table]) && !empty($this->v_entry_exists_array[$t_table]);
	}

	protected function import_categories(&$p_import_data)
	{
		$t_table_name = 'categories';
		if(isset($p_import_data[$t_table_name]) == false)
		{
			return;
		}
		$t_table_data = &$p_import_data[$t_table_name];

		for ($i = 1; $i <= count($t_table_data); $i++)
		{
			if (!empty($t_table_data[$i]['categories_id']))
			{
				$query = 'SELECT COUNT(*) AS categories_id_exists FROM categories WHERE categories_id = ' . (int)$t_table_data[$i]['categories_id'];
				$result = xtc_db_query($query, 'db_link', false);
				$row = xtc_db_fetch_array($result);
				
				if($row['categories_id_exists'] === '1')
				{
					$t_action = 'update';
					$t_where = 'categories_id = ' . (int)$t_table_data[$i]['categories_id'];
				}
				else
				{
					$t_action = 'insert';
					$t_where = '';
				}
			}
			else
			{
				$t_action = 'insert';
				$t_where = '';
				//				unset($t_table_data[$i][$t_table_name]);
			}

			if ($i == 1)
			{
				$t_table_data[$i]['parent_id'] = 0;
			}

			xtc_db_perform($t_table_name, $t_table_data[$i], $t_action, $t_where);
			$t_cache_array = $this->get_cache_data();
			if(isset($p_import_data['products']['products_id']) == false || empty($p_import_data['products']['products_id']) || (isset($t_cache_array['product_ids_array']) == false || isset($t_cache_array['product_ids_array']['product']) == false || in_array($p_import_data['products']['products_id'], $t_cache_array['product_ids_array']['product']) == false))
			{
				$this->update_statistic($t_table_name, $t_action);
			}

			if ($t_action == 'insert')
			{
				$t_table_data[$i]['categories_id'] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
				if ($i < count($t_table_data))
				{
					$t_table_data[$i + 1]['parent_id'] = $t_table_data[$i]['categories_id'];
				}
			}

			$p_import_data['products_to_categories']['categories_id'] = $t_table_data[$i]['categories_id'];
		}
	}


	protected function import_categories_description(&$p_import_data)
	{
		$t_table_name = 'categories_description';
		if(isset($p_import_data[$t_table_name]) == false)
		{
			return;
		}
		$t_table_data = &$p_import_data[$t_table_name];

		$t_language_array = $this->v_coo_csv_import_function_lib->get_language_array();
		for ($i = 1; $i <= count($t_table_data); $i++)
		{
			foreach ($t_language_array as $t_language_id)
			{
				if(empty($t_language_id))
				{
					continue;
				}
				
				if (empty($t_table_data[$i][$t_language_id]['categories_id']))
				{
					$t_action = 'replace';
					$t_table_data[$i][$t_language_id]['categories_id'] = $p_import_data['categories'][$i]['categories_id'];
				}
				else
				{
					$t_action = 'update';
				}
				$t_table_data[$i][$t_language_id]['language_id'] = $t_language_id;
				$t_where = 'categories_id = ' . $t_table_data[$i][$t_language_id]['categories_id'] . ' AND language_id = ' . $t_language_id;

				xtc_db_perform($t_table_name, $t_table_data[$i][$t_language_id], $t_action, $t_where);
			}
		}
	}


	protected function import_products(&$p_import_data)
	{
		$t_table_name = 'products';
		$t_table_data = &$p_import_data[$t_table_name];

		if ($this->entry_exists($t_table_name))
		{
			$t_action = 'update';
			$t_where = 'products_id = ' . $t_table_data['products_id'];
		}
		else
		{
			$t_action = 'insert';
			$t_where = '';
		}

		xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);

		$t_cache_array = $this->get_cache_data();
		if(isset($t_table_data['products_id']) == false || empty($t_table_data['products_id']) || (isset($t_cache_array['product_ids_array']) == false || isset($t_cache_array['product_ids_array']['product']) == false || in_array($p_import_data['products']['products_id'], $t_cache_array['product_ids_array']['product']) == false))
		{
			$this->update_statistic($t_table_name, $t_action);
		}

		if ($t_action == 'insert')
		{
			$t_table_data['products_id'] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		}
	}


	protected function import_products_description(&$p_import_data)
	{
		$t_table_name = 'products_description';
		$t_table_data = &$p_import_data[$t_table_name];
		$t_products_id = $p_import_data['products']['products_id'];

		$t_language_array = $this->v_coo_csv_import_function_lib->get_language_array();
		foreach ($t_language_array as $t_language_id)
		{
			if(empty($t_language_id))
			{
				continue;
			}
			
			$t_table_data[$t_language_id]['products_id'] = $t_products_id;
			$t_table_data[$t_language_id]['language_id'] = $t_language_id;
			if ($this->entry_exists($t_table_name, array($t_language_id)))
			{
				$t_action = 'update';
			}
			else
			{
				$t_action = 'replace';
			}
			$t_where = 'products_id = ' . $t_table_data[$t_language_id]['products_id'] . ' AND language_id = ' . $t_language_id;

			xtc_db_perform($t_table_name, $t_table_data[$t_language_id], $t_action, $t_where);
		}
	}


	protected function import_products_item_codes(&$p_import_data)
	{
		$t_table_name = 'products_item_codes';
		$t_table_data = &$p_import_data[$t_table_name];
		$t_table_data['products_id'] = $p_import_data['products']['products_id'];

		$t_action = 'replace';
		$t_where = 'products_id = ' . $t_table_data['products_id'];

		xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);
	}


	protected function import_products_to_categories(&$p_import_data)
	{
		$t_table_name = 'products_to_categories';
		$t_table_data = &$p_import_data[$t_table_name];
		if(isset($t_table_data['categories_id']) == false)
		{
			return;
		}
		$t_table_data['products_id'] = $p_import_data['products']['products_id'];

		$t_action = 'replace';
		$t_where = 'products_id = ' . $t_table_data['products_id'] . ' AND categories_id = ' . $t_table_data['categories_id'];

		xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);
	}


	protected function import_products_google_categories(&$p_import_data)
	{
		$t_table_name = 'products_google_categories';
		$t_table_data = &$p_import_data[$t_table_name];
		$t_table_data['products_id'] = $p_import_data['products']['products_id'];

		if (empty($t_table_data['google_category']))
		{
			return;
		}

		if ($this->entry_exists($t_table_name))
		{
			$t_action = 'update';
			$t_where = 'products_id = ' . $t_table_data['products_id'];
		}
		else
		{
			$t_action = 'insert';
			$t_where = '';
		}

		xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);
	}


	protected function import_specials(&$p_import_data)
	{
		$t_table_name = 'specials';
		$t_table_data = &$p_import_data[$t_table_name];
		$t_table_data['products_id'] = $p_import_data['products']['products_id'];

		if (empty($t_table_data['specials_qty']) &&
			empty($t_table_data['specials_new_products_price']) &&
			empty($t_table_data['expires_date']) &&
			empty($t_table_data['specials_status']))
		{
			return;
		}

		if ($this->entry_exists($t_table_name))
		{
			$t_action = 'update';
			$t_where = 'products_id = ' . $t_table_data['products_id'];
		}
		else
		{
			$t_action = 'insert';
			$t_where = '';
		}

		xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);
		$t_cache_array = $this->get_cache_data();
		if(isset($t_table_data['products_id']) == false || empty($t_table_data['products_id']) || (isset($t_cache_array['product_ids_array']) == false || isset($t_cache_array['product_ids_array']['product']) == false || in_array($p_import_data['products']['products_id'], $t_cache_array['product_ids_array']['product']) == false))
		{
			$this->update_statistic($t_table_name, $t_action);
		}
	}


	protected function import_products_images(&$p_import_data)
	{
		$t_table_name = 'products_images';
		$t_table_data = &$p_import_data[$t_table_name];

		if(empty($t_table_data))
		{
			return;
		}

		$t_language_array = $this->v_coo_csv_import_function_lib->get_language_array();

		foreach ($t_table_data as $i => $t_image_data)
		{
			$t_table_data[$i]['products_id'] = $p_import_data['products']['products_id'];
			$t_table_data[$i]['image_nr'] = $i;
			$t_action = '';

			if ($this->entry_exists($t_table_name, array($i)))
			{
				$t_where = 'products_id = ' . $t_table_data[$i]['products_id'] . ' AND image_nr = ' . $i;

				if (empty($t_table_data[$i]['image_name']))
				{
					$t_action = 'delete';
				}
				else
				{
					$t_action = 'update';
				}
			}
			else if (!empty($t_table_data[$i]['image_name']))
			{
				$t_action = 'insert';
				$t_where = '';
			}

			if ($t_action == 'delete')
			{
				$t_sql = "DELETE pia.* FROM " . $t_table_name . " pi, gm_prd_img_alt pia WHERE pi.image_id = pia.image_id AND pia.products_id = " . $t_table_data[$i]['products_id'] . " AND pi.products_id = " . $t_table_data[$i]['products_id'] . " AND pi.image_nr = " . $i;
				xtc_db_query($t_sql);

				$t_sql = "DELETE FROM " . $t_table_name . " WHERE products_id = " . $t_table_data[$i]['products_id'] . " AND image_nr = " . $i;
				xtc_db_query($t_sql);

				$this->v_entry_exists_array[$t_table_name . '__' . $i . '_exists'] = '';
				foreach ($t_language_array as $t_language_id)
				{
					$this->v_entry_exists_array['gm_prd_img_alt__' . $i . '__' . $t_language_id . '_exists'] = '';
				}
				continue;
			}
			else if (!empty($t_table_data[$i]['image_name']))
			{
				xtc_db_perform($t_table_name, $t_table_data[$i], $t_action, $t_where);
			}

			if ($t_action == 'insert')
			{
				$t_table_data[$i]['image_id'] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
				$this->v_entry_exists_array[$t_table_name . '__' . $i . '_exists'] = $t_table_data[$i]['image_id'];
			}
			else
			{
				$t_table_data[$i]['image_id'] = $this->v_entry_exists_array[$t_table_name . '__' . $i . '_exists'];
			}
		}
	}


	protected function import_products_quantity_unit(&$p_import_data)
	{
		$t_table_name = 'products_quantity_unit';
		$t_table_data = &$p_import_data[$t_table_name];
		$t_table_data['products_id'] = $p_import_data['products']['products_id'];

		if(array_key_exists('quantity_unit_id', $t_table_data) && empty($t_table_data['quantity_unit_id']))
		{
			$t_action = 'delete';
			$t_where = 'products_id = ' . $t_table_data['products_id'];
		}
		elseif ($this->entry_exists($t_table_name))
		{
			$t_action = 'update';
			$t_where = 'products_id = ' . $t_table_data['products_id'];
		}
		else
		{
			$t_action = 'insert';
			$t_where = '';
		}

		xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);
	}


	protected function import_gm_prd_img_alt(&$p_import_data)
	{
		$t_table_name = 'gm_prd_img_alt';
		$c_products_id = (int)$p_import_data['products']['products_id'];

		if (!isset($p_import_data[$t_table_name]))
		{
			$t_sql = "DELETE FROM " . $t_table_name . " WHERE products_id = " . $c_products_id;
			xtc_db_query($t_sql);
			return;
		}
		$t_table_data = &$p_import_data[$t_table_name];

		foreach ($this->v_img_nrs as $i)
		{
			if (!$this->entry_exists('products_images', array($i)))
			{
				continue;
			}

			if (isset($t_table_data[$i]) & !empty($t_table_data[$i]))
			{
				foreach ($t_table_data[$i] as $t_language_id => $t_alt_text)
				{
					$t_table_data[$i][$t_language_id]['products_id'] = $p_import_data['products']['products_id'];
					$t_table_data[$i][$t_language_id]['language_id'] = $t_language_id;
					$t_table_data[$i][$t_language_id]['image_id'] = $p_import_data['products_images'][$i]['image_id'];

					if ($this->entry_exists($t_table_name, array($i, $t_language_id)))
					{
						$t_where = 'products_id = ' . $t_table_data[$i][$t_language_id]['products_id'] . ' AND image_id = ' . $t_table_data[$i][$t_language_id]['image_id'] . ' AND language_id = ' . $t_language_id;
						$t_action = 'update';
					}
					else
					{
						$t_action = 'insert';
						$t_where = '';
					}

					if (!empty($t_table_data[$i][$t_language_id]['gm_alt_text']))
					{
						xtc_db_perform($t_table_name, $t_table_data[$i][$t_language_id], $t_action, $t_where);
					}
					elseif ($t_action == 'update')
					{
						$t_sql = "DELETE FROM " . $t_table_name . " 
									WHERE 
										products_id = " . $t_table_data[$i][$t_language_id]['products_id'] . " AND 
										image_id = " . $t_table_data[$i][$t_language_id]['image_id'] . " AND 
										language_id = " . $t_language_id;
						xtc_db_query($t_sql);
					}
				}
			}
			else
			{
				$t_sql = "DELETE pia.* FROM products_images pi, gm_prd_img_alt pia WHERE pi.image_id = pia.image_id AND pia.products_id = " . $c_products_id . " AND pi.products_id = " . $c_products_id . " AND pi.image_nr = " . $i;
				xtc_db_query($t_sql);
			}
		}
	}


	protected function import_personal_offers_by_customers_status(&$p_import_data)
	{
		$t_table_name_prefix = 'personal_offers_by_customers_status_';
		$t_tables = array();
		foreach ($p_import_data as $t_key => $t_val)
		{
			if (strpos_wrapper($t_key, $t_table_name_prefix) === 0)
			{
				$t_tables[] = $t_key;
			}
		}

		if (empty($t_tables))
		{
			return;
		}
		$t_action = 'replace';

		foreach ($t_tables as $t_table_name)
		{
			$t_table_data = &$p_import_data[$t_table_name];

			foreach ($t_table_data as $t_quantity => $t_personal_offer)
			{
				if (empty($t_quantity))
				{
					continue;
				}
				$t_table_data[$t_quantity]['products_id'] = $p_import_data['products']['products_id'];
				$t_table_data[$t_quantity]['quantity'] = $t_quantity;
				$t_where = 'products_id = ' . $t_table_data[$t_quantity]['products_id'] . ' AND quantity = ' . $t_quantity;
				xtc_db_perform($t_table_name, $t_table_data[$t_quantity], $t_action, $t_where);
			}
		}
	}


	protected function import_properties_values(&$p_import_data)
	{
		$t_table_name = 'properties_values';
		foreach ($p_import_data[$t_table_name] as &$t_table_data)
		{
			$t_properties_array = $this->get_properties_array();

			if (isset($t_properties_array[$t_table_data['properties_id']]['properties_values'][$t_table_data['properties_values_id']]) &&
				!empty($t_properties_array[$t_table_data['properties_id']]['properties_values'][$t_table_data['properties_values_id']]))
			{
				return;
			}

			$t_action = 'insert';

			xtc_db_perform($t_table_name, $t_table_data, $t_action);
			$t_table_data['properties_values_id'] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		}
	}


	protected function import_properties_values_description(&$p_import_data)
	{
		$t_table_name = 'properties_values_description';
		foreach ($p_import_data[$t_table_name] as &$t_table_data)
		{
			if (empty($t_table_data['properties_values_id']))
			{
				$t_table_data['properties_values_id'] = $p_import_data['properties_values']['properties_values_id'];
				$t_action = 'replace';
			}
			else
			{
				$t_action = 'update';
			}
			$t_where = 'properties_values_id = ' . $t_table_data['properties_values_id'] . ' AND language_id = ' . $t_table_data['language_id'];

			xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);
		}
	}


	protected function import_products_properties_combis(&$p_import_data)
	{
		$t_table_name = 'products_properties_combis';
		$t_table_data = &$p_import_data[$t_table_name];
		$t_table_data['products_id'] = $p_import_data['products']['products_id'];

		if (empty($t_table_data['products_properties_combis_id']))
		{
			$t_action = 'insert';
			$t_where = '';
		}
		else
		{
			$t_action = 'replace';
			$t_where = 'products_properties_combis_id = ' . $t_table_data['products_properties_combis_id'];
		}

		xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);

		$t_statistics_action = 'insert';
		if($t_action == 'replace')
		{
			$t_statistics_action = 'update';
		}
		$this->update_statistic($t_table_name, $t_statistics_action);

		if ($t_action == 'insert')
		{
			$t_table_data['products_properties_combis_id'] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
		}
	}


	protected function import_products_properties_combis_values(&$p_import_data)
	{
		$t_table_name = 'products_properties_combis_values';
		$t_table_data = &$p_import_data[$t_table_name];

		foreach ($t_table_data as &$t_property_value)
		{
			$t_property_value['products_properties_combis_id'] = $p_import_data['products_properties_combis']['products_properties_combis_id'];

			$t_action = 'replace';
			$t_where = 'products_properties_combis_id = ' . $t_property_value['products_properties_combis_id'] . ' AND properties_values_id = ' . $t_property_value['properties_values_id'];

			xtc_db_perform($t_table_name, $t_property_value, $t_action, $t_where);
		}
	}


	protected function import_products_properties_admin_select(&$p_import_data)
	{
		$t_table_name = 'products_properties_admin_select';
		foreach ($p_import_data[$t_table_name] as &$t_table_data)
		{
			$t_table_data['products_id'] = $p_import_data['products']['products_id'];

			$t_action = 'replace';
			$t_where = 'properties_id = ' . $t_table_data['properties_id'] . ' AND properties_values_id = ' . $t_table_data['properties_values_id'] . ' AND products_id = ' . $t_table_data['products_id'];

			xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);
		}
	}


	protected function import_additional_field_values(&$p_import_data)
	{
		$t_table_name = 'additional_field_values';
		$t_table_data_array = &$p_import_data[$t_table_name];

		if(empty($t_table_data_array) || is_array($t_table_data_array) == false)
		{
			return;
		}

		foreach ($t_table_data_array as $t_additional_field_id => &$t_table_data)
		{
			$t_table_data['item_id'] = $p_import_data['products']['products_id'];

			if (empty($t_table_data['additional_field_value_id']))
			{
				$t_action = 'insert';
				$t_where = '';
			}
			else
			{
				$t_action = 'replace';
				$t_where = 'additional_field_id = ' . $p_import_data[$t_table_name][$t_additional_field_id]['additional_field_id'] . ' AND item_id = ' . $t_table_data['products_id'];
			}

			xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);

			if ($t_action == 'insert')
			{
				$p_import_data[$t_table_name][$t_additional_field_id]['additional_field_value_id'] = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			}
		}
	}


	protected function import_additional_field_value_descriptions(&$p_import_data)
	{
		$t_table_name = 'additional_field_value_descriptions';
		if(is_array($p_import_data[$t_table_name]))
		{
			foreach($p_import_data[$t_table_name] as $t_additional_field_id => $t_value_descriptions_by_lang)
			{
				foreach($t_value_descriptions_by_lang as $t_language_id => $t_value_description_data)
				{
					$t_table_data = $t_value_description_data;
					$t_table_data['additional_field_value_id'] = $p_import_data['additional_field_values'][$t_additional_field_id]['additional_field_value_id'];

					$t_action = 'replace';
					$t_where = 'additional_field_value_id = ' . $t_table_data['additional_field_value_id'];

					xtc_db_perform($t_table_name, $t_table_data, $t_action, $t_where);
				}
			}
		}

		//$t_table_data = &$p_import_data[$t_table_name][$p_import_data[$t_table_name]['language_id']];
	}


	public function calc_import_progress()
	{
		$t_progress = 1;
		$t_pointer_position = (int)$this->get_pointer_position();
		$t_filesize = (int)$this->get_filesize();

		if($t_pointer_position > 0 && $t_filesize > 0)
		{
			$t_progress = round ( (100 / $t_filesize ) * $t_pointer_position);

			if($t_progress < 1)
			{
				$t_progress = 1;
			}
			elseif($t_progress >= 100 && $t_pointer_position < $t_filesize)
			{
				$t_progress = 99;
			}
			elseif($t_progress > 100)
			{
				$t_progress = 100;
			}
		}

		return $t_progress;
	}

	public function reset_import_data()
	{
		unset($_SESSION['pointer_position']);
		unset($_SESSION['import_filesize']);

		unset($_SESSION['import_statistics_array']);
	}

	public function get_variables_array($p_type_id)
	{
		$coo_export_variables = MainFactory::create_object('CSVExportVariables');

		$t_variables_array = $coo_export_variables->get_variables_array();

		$t_filtered_variables_array = array();

		foreach($t_variables_array AS $t_key => $t_variable)
		{
			if ($t_variable[$p_type_id])
			{
				$t_filtered_variables_array[$t_key] = $t_variable;
			}
		}

		return $t_filtered_variables_array;
	}

	public function get_import_files_array()
	{
		$t_import_file_list = array();
		$tmp_import_file_list = glob(DIR_FS_CATALOG . 'import/*');
		foreach( $tmp_import_file_list AS $t_file )
		{
			if( in_array(strtolower( substr_wrapper( $t_file, -4 ) ), array('.csv', '.txt')) )
			{
				$t_import_file_list[] = str_replace( DIR_FS_CATALOG . 'import/', '', $t_file);
			}
		}

		return $t_import_file_list;
	}

	public function build_import_statistics_message()
	{
		$t_products_inserted = (isset($_SESSION['import_statistics_array']['products']['insert'])) ? (int)$_SESSION['import_statistics_array']['products']['insert'] : 0;
		$t_products_updated = (isset($_SESSION['import_statistics_array']['products']['update'])) ? (int)$_SESSION['import_statistics_array']['products']['update'] : 0;
		$t_categories_inserted = (isset($_SESSION['import_statistics_array']['categories']['insert'])) ? (int)$_SESSION['import_statistics_array']['categories']['insert'] : 0;
		$t_categories_updated = (isset($_SESSION['import_statistics_array']['categories']['update'])) ? (int)$_SESSION['import_statistics_array']['categories']['update'] : 0;
		$t_specials_inserted = (isset($_SESSION['import_statistics_array']['specials']['insert'])) ? (int)$_SESSION['import_statistics_array']['specials']['insert'] : 0;
		$t_specials_updated = (isset($_SESSION['import_statistics_array']['specials']['update'])) ? (int)$_SESSION['import_statistics_array']['specials']['update'] : 0;
		$t_combis_inserted = (isset($_SESSION['import_statistics_array']['products_properties_combis']['insert'])) ? (int)$_SESSION['import_statistics_array']['products_properties_combis']['insert'] : 0;
		$t_combis_updated = (isset($_SESSION['import_statistics_array']['products_properties_combis']['update'])) ? (int)$_SESSION['import_statistics_array']['products_properties_combis']['update'] : 0;

		$coo_language_manager = MainFactory::create_object('LanguageTextManager', array('export_schemes', $_SESSION['languages_id']));
		$t_message = $coo_language_manager->get_text('import_statistics_message');
		$t_message = sprintf($t_message, $t_products_inserted, $t_products_updated, $t_categories_inserted, $t_categories_updated, $t_specials_inserted, $t_specials_updated, $t_combis_inserted, $t_combis_updated);

		return $t_message;
	}

	protected function update_statistic($p_table_name, $p_action)
	{
		if(!isset($_SESSION['import_statistics_array']))
		{
			$_SESSION['import_statistics_array'] = array();
		}
		if(!isset($_SESSION['import_statistics_array'][$p_table_name]))
		{
			$_SESSION['import_statistics_array'][$p_table_name] = array();
		}
		if(!isset($_SESSION['import_statistics_array'][$p_table_name][$p_action]))
		{
			$_SESSION['import_statistics_array'][$p_table_name][$p_action] = 1;
		}
		else if($p_action == 'replace')
		{
			$_SESSION['import_statistics_array'][$p_table_name]['insert'] += 1;
		}
		else
		{
			$_SESSION['import_statistics_array'][$p_table_name][$p_action] += 1;
		}
	}

	public function add_products_id_to_cache($p_products_id, $p_key = 'product')
	{
		$c_products_id = (int)$p_products_id;
		$t_cache_array = $this->get_cache_data();

		if(isset($t_cache_array['product_ids_array']) === false)
		{
			$t_cache_array['product_ids_array'] = array();
		}
		if(isset($t_cache_array['product_ids_array'][$p_key]) === false)
		{
			$t_cache_array['product_ids_array'][$p_key] = array();
		}

		$t_key = array_search($c_products_id, $t_cache_array['product_ids_array'][$p_key]);

		if($t_key === false)
		{
			$t_cache_array['product_ids_array'][$p_key][] = $c_products_id;
			$this->set_cache_data($t_cache_array);
		}
	}

	public function reset_products_id_cache()
	{
		$t_cache_array = array();
		$t_cache_array['product_ids_array'] = array();
		$t_cache_array['processed_product_ids_array'] = array();
		$this->set_cache_data($t_cache_array);
	}

	public function delete_products_id_from_cache($p_products_id, $p_key = 'product')
	{
		$c_products_id = (int)$p_products_id;
		$t_cache_array = $this->get_cache_data();
		if(isset($t_cache_array['product_ids_array']['index']) && empty($t_cache_array['product_ids_array']['index']) === false)
		{
			$t_key = array_search($c_products_id, $t_cache_array['product_ids_array']['index']);
			if($t_key !== false)
			{
				unset($t_cache_array['product_ids_array']['index'][$t_key]);

				if(isset($t_cache_array['processed_product_ids_array']) === false)
				{
					$t_cache_array['processed_product_ids_array'] = array();
				}

				$t_key = array_search($c_products_id, $t_cache_array['processed_product_ids_array']);

				if($t_key === false)
				{
					$t_cache_array['processed_product_ids_array'][] = $c_products_id;
				}

				$this->set_cache_data($t_cache_array);
			}
		}
	}

	public function get_products_id_from_cache()
	{
		$t_cache_array = $this->get_cache_data();

		if(isset($t_cache_array['product_ids_array']) && empty($t_cache_array['product_ids_array']) === false)
		{
			if(isset($t_cache_array['product_ids_array']['index']) && empty($t_cache_array['product_ids_array']['index']) === false)
			{
				return current($t_cache_array['product_ids_array']['index']);
			}
		}

		return false;
	}

	public function get_products_id_cache_progress()
	{
		$t_progress = 100;

		$t_cache_array = $this->get_cache_data();

		if(isset($t_cache_array['product_ids_array']) && empty($t_cache_array['product_ids_array']) === false)
		{
			if(isset($t_cache_array['product_ids_array']['index']) && empty($t_cache_array['product_ids_array']['index']) === false)
			{
				$t_progress = 0;

				if(isset($t_cache_array['processed_product_ids_array']) && empty($t_cache_array['processed_product_ids_array']) === false)
				{
					$t_not_processed_count = count($t_cache_array['product_ids_array']['index']);
					$t_processed_count = count($t_cache_array['processed_product_ids_array']);

					$t_progress = $t_processed_count / ($t_not_processed_count + $t_processed_count) * 100;
					$t_progress = floor($t_progress);
				}
			}
		}

		return $t_progress;
	}

	public function set_additional_fields_cache($p_language_id)
	{
		$c_language_id = (int)$p_language_id;
		$t_cache_array = array();
		$t_cache_array['additional_fields'] = array();
		$this->reset_additional_fields_cache();
		$t_sql = 'SELECT afd.additional_field_id, afd.name '
				 . 'FROM additional_fields af, additional_field_descriptions afd '
				 . 'WHERE af.item_type LIKE "product" AND '
				 . 'af.additional_field_id = afd.additional_field_id AND '
				 . 'afd.language_id = ' . $c_language_id;
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_cache_array['additional_fields'][$t_row['name']] = $t_row['additional_field_id'];
		}
		$this->add_cache_data($t_cache_array);
	}

	public function reset_additional_fields_cache()
	{
		$t_cache_array = array();
		$t_cache_array['additional_fields'] = array();
		$this->add_cache_data($t_cache_array);
	}

	public function set_properties_cache($p_language_id)
	{
		$c_language_id = (int)$p_language_id;
		$t_cache_array = array();
		$t_cache_array['properties'] = array();
		$this->reset_properties_cache();
		$t_sql = 'SELECT pd.properties_id, pd.properties_admin_name, pd.properties_name '
				 . 'FROM properties_description pd '
				 . 'WHERE pd.language_id = ' . $c_language_id;
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_cache_array['properties'][$t_row['properties_admin_name'] === '' ? $t_row['properties_name'] : $t_row['properties_admin_name']] = $t_row['properties_id'];
		}
		$this->add_cache_data($t_cache_array);
	}

	public function reset_properties_cache()
	{
		$t_cache_array = array();
		$t_cache_array['properties'] = array();
		$this->add_cache_data($t_cache_array);
	}

	public function set_attributes_cache($p_language_id)
	{
		$c_language_id = (int)$p_language_id;
		$t_cache_array = array();
		$t_cache_array['attributes'] = array();
		$this->reset_attributes_cache();
		$t_sql = 'SELECT po.products_options_id, po.products_options_name '
				 . 'FROM products_options po '
				 . 'WHERE po.language_id = ' . $c_language_id;
		$t_result = xtc_db_query($t_sql);

		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_cache_array['attributes'][$t_row['products_options_name']] = $t_row['products_options_id'];
		}
		$this->add_cache_data($t_cache_array);
	}

	public function reset_attributes_cache()
	{
		$t_cache_array = array();
		$t_cache_array['attributes'] = array();
		$this->add_cache_data($t_cache_array);
	}
}
