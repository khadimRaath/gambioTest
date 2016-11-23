<?php
/* --------------------------------------------------------------
   CSVControl.inc.php 2016-04-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of CSVControl
 */
class CSVControl extends BaseClass
{
	static protected $coo_instance = null;
	static protected $get_instance_called = false;
	protected $v_coo_csv_source = null;
	protected $v_export_file_handle = false;
	protected $coo_properties_data_agent = null;
    protected $v_timeout = 20;

	static public function get_instance()
	{
		if(self::$coo_instance === null)
		{
			self::$get_instance_called = true;
			self::$coo_instance = MainFactory::create_object('CSVControl');
		}

		return self::$coo_instance;
	}

	public function __construct()
	{
		if(self::$get_instance_called === false || self::$coo_instance !== null)
		{
			trigger_error('CSVSource is a singleton. Use CSVControl::get_instance() instead of CSVControl::__construct().', E_USER_ERROR);
		}

		$this->init();
	}

	private function __clone() {}

	protected function init()
	{
		$this->v_coo_csv_source = MainFactory::create_object('CSVSource', array(), true);
		$this->coo_properties_data_agent = MainFactory::create_object('PropertiesDataAgent', array());
	}

	public function get_schemes($p_reload = false)
	{
		return $this->v_coo_csv_source->get_schemes($p_reload);
	}

	public function get_schemes_by_type( $p_type )
	{
		$t_schemes_array = $this->v_coo_csv_source->get_schemes_by_type( $p_type );

		return $t_schemes_array;
	}

	public function get_scheme( $p_scheme_id )
	{
		return $this->v_coo_csv_source->get_scheme($p_scheme_id);
	}

	public function get_collective_fields($p_scheme_id)
	{
		$coo_scheme = $this->v_coo_csv_source->get_scheme($p_scheme_id);
		$coo_scheme->load_fields();
		$t_collective_fields_array = array();

		foreach($coo_scheme->v_fields_array as $t_field_id => $coo_field)
		{
			if($coo_field->is_collective_field())
			{
				$t_collective_fields_array[$t_field_id] = $coo_field;
			}
		}

		return $t_collective_fields_array;
	}


	public function save_scheme( $p_data_array )
	{
		$coo_scheme_model = MainFactory::create_object('CSVSchemeModel');
		$t_scheme_id = $coo_scheme_model->save($p_data_array);

		if($p_data_array['scheme_id'] == 0 && $p_data_array['type_id'] == 1)
		{
			$p_field_data = array();
			$p_field_data['field_id'] = 0;
			$p_field_data['field_name'] = 'XTSOL';
			$p_field_data['field_content'] = 'XTSOL';
			$p_field_data['scheme_id'] = $t_scheme_id;
			$p_field_data['created_by'] = 'custom';
			$this->save_field($p_field_data);
		}

		$this->v_coo_csv_source->reset_schemes();

		return $t_scheme_id;
	}


	public function save_scheme_properties( $p_scheme_id, $p_properties_data_string )
	{
		$c_scheme_id = (int)$p_scheme_id;

		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_scheme_properties'));
		$coo_data_object->set_keys( array( 'scheme_id' => $c_scheme_id ) );
		$coo_data_object->delete();
		$coo_data_object->set_keys(array());

		if( strpos_wrapper( $p_properties_data_string, ',' ) === false ) return false;

		$p_properties_data_array = explode( ',', $p_properties_data_string );
		$t_count_properties_data_array = count( $p_properties_data_array );

		$coo_data_object->set_data_value( 'scheme_id', $c_scheme_id );
		for( $i = 0; $i < $t_count_properties_data_array; $i++ )
		{
			$coo_data_object->set_data_value( 'properties_column', $p_properties_data_array[ $i ] );
			$coo_data_object->set_data_value( 'sort_order', $i );
			$coo_data_object->save_body_data();
		}

		return true;
	}


	public function delete_scheme( $p_scheme_id )
	{
		$coo_data_object = $this->v_coo_csv_source->get_scheme($p_scheme_id);
		$t_success = $coo_data_object->delete();
		$this->v_coo_csv_source->reset_schemes();

		return $t_success;
	}


	public function get_fields( $p_scheme_id )
	{
		$coo_scheme_model = $this->v_coo_csv_source->get_scheme($p_scheme_id);

		return $coo_scheme_model->v_fields_array;
	}


	public function get_field( $p_field_id )
	{
		$coo_field_model = MainFactory::create_object('CSVFieldModel', array($p_field_id));

		return $coo_field_model->v_data_array;
	}


	public function save_field( $p_data_array )
	{
		$coo_field_model = MainFactory::create_object('CSVFieldModel');
		$t_field_id = $coo_field_model->save($p_data_array);

		return $t_field_id;
	}


	public function set_collective_variable($p_source_name_array, $p_sources, $p_field_id = 0)
	{
		$c_field_id = (int)$p_field_id;
		$t_field_content = '';
		$t_sources = '';
		foreach($p_sources as $t_source => $t_value)
		{
			if($t_value == '1')
			{
				$t_sources .= ';' . $t_source;
			}
		}
		$t_sources = substr_wrapper($t_sources, 1);

		$t_collective_variable = '{collective_field||' . implode(';', $p_source_name_array) . '||' . $t_sources . '}';

		if($c_field_id > 0)
		{
			$t_sql = 'SELECT field_content FROM export_scheme_fields WHERE field_id = ' . $c_field_id;
			$t_result = xtc_db_query($t_sql);
			$t_field_data = xtc_db_fetch_array($t_result);

			if(empty($t_field_data))
			{
				$c_field_id = 0;
			}
			else
			{
				$t_field_content = $t_field_data['field_content'];
			}
		}

		if(empty($t_field_content))
		{
			$t_field_content = $t_collective_variable;
		}
		else
		{
			$t_field_content = preg_replace('/(.*?)\{collective_field\|\|.*?\}(.*)/', '$1' . $t_collective_variable . '$2', $t_field_content, 1);
		}
		return $t_field_content;
	}


	public function delete_fields_by_fields_array( $p_scheme_id, $p_field_array, $p_invert = false )
	{
		$this->v_coo_csv_source->delete_fields_by_fields_array( $p_scheme_id, $p_field_array, $p_invert );

		return true;
	}


	public function copy_scheme($p_scheme_id)
	{
		$coo_scheme_model = $this->v_coo_csv_source->get_scheme($p_scheme_id);
		$t_new_scheme_id = $coo_scheme_model->copy();
		$this->v_coo_csv_source->reset_schemes();

		return $t_new_scheme_id;
	}


	public function save_field_sort_order( $p_scheme_id, $p_field_ids_array )
	{
		$coo_scheme_model = $this->v_coo_csv_source->get_scheme($p_scheme_id);
		$t_success = $coo_scheme_model->save_field_sort_order($p_field_ids_array);

		return $t_success;
	}


	public function save_categories( $p_scheme_id, $p_save_all = true, $p_selected_categories = array(), $p_bequeathing_categories = array() )
	{
		$t_selected_categories = array();
		$t_by_parents = array();
		$t_by_children = array();
		$coo_scheme_model = $this->v_coo_csv_source->get_scheme($p_scheme_id);
		$coo_scheme_model->load_categories();

		if ($p_save_all)
		{
			$t_success = $coo_scheme_model->add_categories();
			$t_success &= $coo_scheme_model->save_categories();
		}
		else
		{
			$coo_data_object = MainFactory::create_object('GMDataObjectGroup', array('export_scheme_categories', array('scheme_id' => $coo_scheme_model->get_scheme_id())));
			$coo_categories = $coo_data_object->get_data_objects_array();

			foreach ($coo_categories as $coo_category)
			{
				$t_selected_categories[$coo_category->get_data_value('categories_id')] = $coo_category->get_data_value('selection_state');
			}

			if (!empty($p_bequeathing_categories))
			{
				$coo_data_object = MainFactory::create_object('GMDataObjectGroup', array('categories', array()));
				$coo_categories = $coo_data_object->get_data_objects_array();

				foreach ($coo_categories as $coo_category)
				{
					if (!isset($t_by_parents[$coo_category->get_data_value('parent_id')]) || !is_array($t_by_parents[$coo_category->get_data_value('parent_id')]))
					{
						$t_by_parents[$coo_category->get_data_value('parent_id')] = array();
					}
					$t_by_parents[$coo_category->get_data_value('parent_id')][] = $coo_category->get_data_value('categories_id');
					$t_by_children[$coo_category->get_data_value('categories_id')] = $coo_category->get_data_value('parent_id');
				}

				$this->bequeath_categories_state($t_selected_categories, $p_bequeathing_categories, $t_by_parents, 0);
			}

			foreach ($p_selected_categories as $t_category_id => $t_state)
			{
				$t_selected_categories[$t_category_id] = $t_state;
			}

			$t_success = $coo_scheme_model->save_categories($t_selected_categories);
		}

		return $t_success;
	}

	protected function bequeath_categories_state(&$p_selected_categories, $p_bequeathing_categories, &$p_by_parents, $p_parent_id, $p_inherited_state = false)
	{
		if ($p_inherited_state !== false)
		{
			$p_selected_categories[$p_parent_id] = $p_inherited_state;
		}

		if (isset($p_by_parents[$p_parent_id]) && is_array($p_by_parents[$p_parent_id]))
		{
			if (isset($p_bequeathing_categories[$p_parent_id]))
			{
				$p_inherited_state = $p_bequeathing_categories[$p_parent_id];
			}

			foreach ($p_by_parents[$p_parent_id] as $t_child_id)
			{
				$this->bequeath_categories_state($p_selected_categories, $p_bequeathing_categories, $p_by_parents, $t_child_id, $p_inherited_state);
			}
		}
	}


	public function get_variables_array($p_type_id)
	{
		$t_variables_array = $this->v_coo_csv_source->get_variables_array($p_type_id);

		return $t_variables_array;
	}


	public function get_properties_array( $p_get_values )
	{
		return $this->v_coo_csv_source->get_properties_array( $p_get_values );
	}

	public function get_selected_properties_by_products_id($p_products_id)
	{
		return $this->v_coo_csv_source->get_selected_properties_by_products_id($p_products_id);
	}

	public function get_selected_properties_for_products()
	{
		return $this->v_coo_csv_source->get_selected_properties_for_products();
	}


	public function export( $p_scheme_id, $p_preview_rows = 0, $p_field_data_array = array(), $p_properties_data_array = array(), $p_format = 'csv' )
	{
		$t_run_export = true;
		$t_export_start = microtime(true);

		$t_export_unfinished = true;
		$t_scheme_ids_array = array();
		$t_scheme_ids_array[] = (int)$p_scheme_id;
		$t_scheme_id = (int)$p_scheme_id;

		if(empty($p_scheme_id))
		{
			$t_scheme_ids_array = $this->v_coo_csv_source->get_cronjob_scheme_ids();
			$t_scheme_id = $this->v_coo_csv_source->get_current_scheme_id();

			// stop export, if cronjob-function is paused
			if($this->v_coo_csv_source->cronjob_stopped() || $this->v_coo_csv_source->cronjob_paused())
			{
				$t_run_export = false;
			}
		}

		while( $t_run_export == true && count( $t_scheme_ids_array ) > 0 )
		{
			$coo_scheme_model = $this->v_coo_csv_source->get_scheme($t_scheme_id);
			$this->v_coo_csv_source->set_properties_cache($coo_scheme_model->v_data_array['languages_id']);
			$this->v_coo_csv_source->set_attributes_cache($coo_scheme_model->v_data_array['languages_id']);
			$this->v_coo_csv_source->set_additional_fields_cache($coo_scheme_model->v_data_array['languages_id']);
			$this->v_coo_csv_source->set_function_library($t_scheme_id);
			$this->v_coo_csv_source->reset_product_ids_array();
			$coo_scheme_model->load_categories();

			if( count( $p_field_data_array ) == 0 )
			{
				$coo_scheme_model->load_fields();
			}
			else
			{
				$this->v_coo_csv_source->v_scheme_model_array[$t_scheme_id]->v_fields_array = array();
				foreach($p_field_data_array AS $t_field_data_array)
				{
					$coo_scheme_model->add_field($t_field_data_array, false);
				}
			}

			if(empty($p_properties_data_array) === false)
			{
				$this->v_coo_csv_source->v_scheme_model_array[$t_scheme_id]->v_properties_array = $p_properties_data_array;
			}

			$this->v_coo_csv_source->load_properties_fields($t_scheme_id);

			$t_result = array();

			if ($p_preview_rows == 0)
			{
				$t_new_file = true;
				$t_export_limit_offset = $this->v_coo_csv_source->get_limit_offset( $t_scheme_id );

				if( $t_export_limit_offset != 0 )
				{
					$t_new_file = false;
				}

				$this->v_coo_csv_source->open_export_file($t_scheme_id, $t_new_file);
				$t_export_data = '';


				while(($t_data_array = $this->v_coo_csv_source->get_export_data($t_scheme_id)))
				{
					$t_data_array = array($t_data_array);
					$t_preview_content = false;

					$t_export_data = $this->v_coo_csv_source->build_scheme_data_array($t_scheme_id, $t_data_array, $t_preview_content);
					$this->v_coo_csv_source->transform_to_export($p_format, $t_scheme_id, $t_export_data, $t_new_file);

					if ($t_new_file)
					{
						$t_new_file = false;
					}
				}

				// no data to export -> create headline separately
				if($t_export_data == '')
				{
					$t_data_array = array();
					$t_preview_content = false;
					$t_export_data = $this->v_coo_csv_source->build_scheme_data_array($t_scheme_id, $t_data_array, $t_preview_content);
					$this->v_coo_csv_source->transform_to_export($p_format, $t_scheme_id, $t_export_data, $t_new_file);

					if ($t_new_file)
					{
						$t_new_file = false;
					}
				}

				$this->v_coo_csv_source->close_export_file();
			}
			else
			{
				$t_preview_content = true;
				$this->v_coo_csv_source->v_export_file_handle = false;
				while($t_data_array = $this->v_coo_csv_source->get_export_data($t_scheme_id, $p_preview_rows))
				{
					$t_data_array = array($t_data_array);
					$t_preview_data = $this->v_coo_csv_source->build_scheme_data_array($t_scheme_id, $t_data_array, $t_preview_content);
					$t_csv_string = $this->v_coo_csv_source->transform_to_export($p_format, $t_scheme_id, $t_preview_data, $t_new_file);
					$t_csv_string = $this->v_coo_csv_source->create_field_definition($p_format, $t_scheme_id) . $t_csv_string;
					$t_tmp = $this->v_coo_csv_source->transform_to_import($p_format, $t_scheme_id, explode("\n", $t_csv_string));
					$t_result = array_merge($t_result, $t_tmp);
				}

				// no data to show -> create headline separately
				if(empty($t_result))
				{
					$t_data_array = array();
					$t_preview_data = $this->v_coo_csv_source->build_scheme_data_array($t_scheme_id, $t_data_array, $t_preview_content);
					$t_csv_string = $this->v_coo_csv_source->transform_to_export($p_format, $t_scheme_id, $t_preview_data, $t_new_file);
					$t_csv_string = $this->v_coo_csv_source->create_field_definition($p_format, $t_scheme_id) . $t_csv_string;
					$t_tmp = $this->v_coo_csv_source->transform_to_import($p_format, $t_scheme_id, explode("\n", $t_csv_string));
					$t_result = array_merge($t_result, $t_tmp);
				}

				return $t_result;
			}

			if($this->v_coo_csv_source->get_limit_offset($t_scheme_id) == 0)
			{
				$t_export_unfinished = false;
				if( $this->v_coo_csv_source->is_cronjob() == false )
				{
					$t_run_export = false;
				}

				if (file_exists($coo_scheme_model->get_base_path() . 'tmp_' . basename($coo_scheme_model->v_data_array['filename'])))
				{
					rename($coo_scheme_model->get_base_path() . 'tmp_' . basename($coo_scheme_model->v_data_array['filename']), $coo_scheme_model->get_base_path() . basename($coo_scheme_model->v_data_array['filename']));
				}
				$coo_scheme_model->v_data_array[ 'date_last_export' ] = date( 'Y-m-d H:i:00' );
				$coo_scheme_model->save();
				$coo_scheme_model->save_next_due_date();
			}

			$this->v_coo_csv_source->v_passes_array['main']['pass'] = 0;
			$this->v_coo_csv_source->v_passes_array['main']['rows'] = 0;
			$this->v_coo_csv_source->v_passes_array['attributes']['pass'] = 0;
			$this->v_coo_csv_source->v_passes_array['attributes']['rows'] = 0;

			if(empty($p_scheme_id))
			{
				$t_scheme_ids_array = $this->v_coo_csv_source->get_cronjob_scheme_ids();
				$t_scheme_id = $this->v_coo_csv_source->get_current_scheme_id();
			}

			$t_actual_time = microtime(true);
			if( (int)($t_actual_time - $t_export_start) > $this->v_timeout )
			{
				$t_run_export = false;
			}
		}

		return $t_export_unfinished;
	}

	public function get_child_categories($p_scheme_id, $p_parent_id, $p_include_inactive=false)
	{
		$t_levels = 2;

		$coo_scheme_model = $this->v_coo_csv_source->get_scheme($p_scheme_id);
		$t_customers_group_id = $coo_scheme_model->v_data_array['customers_status_id'];

		$t_categories_array = $this->v_coo_csv_source->get_child_categories($p_parent_id, $p_scheme_id, $t_levels, $t_customers_group_id, $p_include_inactive);

		return $t_categories_array;
	}

	public function cronjob_allowed( $p_status )
	{
		$this->v_coo_csv_source->cronjob_allowed( $p_status );

		return true;
	}

	public function pause_cronjob( $p_status )
	{
		$this->v_coo_csv_source->pause_cronjob( $p_status );

		return true;
	}

	public function clean_export( $p_scheme_id )
	{
		$c_scheme_id = (int)$p_scheme_id;
		$coo_scheme_model = $this->v_coo_csv_source->get_scheme($p_scheme_id);
		$t_filepath = DIR_FS_CATALOG . 'export/tmp_' . urlencode($coo_scheme_model->v_data_array['filename']);
		if (file_exists($t_filepath))
		{
			unlink($t_filepath);
		}

		$this->v_coo_csv_source->reset_cache('admin');

		return true;
	}

	public function get_secure_token()
	{
		$t_token = $this->v_coo_csv_source->get_secure_token();

		return $t_token;
	}

	public function cronjob_stopped()
	{
		return $this->v_coo_csv_source->cronjob_stopped();
	}

	public function cronjob_paused()
	{
		return $this->v_coo_csv_source->cronjob_paused();
	}

	public function get_cronjob_status_array($p_scheme_id = false)
	{
		$t_status_array = array();
		if($p_scheme_id === false)
		{
			$t_schemes_array = $this->get_schemes();
			foreach($t_schemes_array AS $t_scheme_id => $coo_scheme)
			{
				$t_status_array[$t_scheme_id] = $this->v_coo_csv_source->get_cronjob_status_array($t_scheme_id);
			}
		}
		else
		{
			$c_scheme_id = (int)$p_scheme_id;
			$t_status_array[$c_scheme_id] = $this->v_coo_csv_source->get_cronjob_status_array($c_scheme_id);
		}

		return $t_status_array;
	}

	public function get_export_types()
	{
		$t_export_types = $this->v_coo_csv_source->get_export_types();
		return $t_export_types;
	}

	public function upload()
	{
		return $this->v_coo_csv_source->upload();
	}

	public function import($p_filename, $p_separator, $p_quote, $t_deletions = array(), $p_progress = 0)
	{
		$t_response_array = array();
		$t_response_array['progress'] = 100;
		$t_response_array['repeat'] = false;
		$t_response_array['error'] = false;
		
		if(empty($t_deletions) === false && (int)$p_progress == 0)
		{
			$this->processDeletions($t_deletions);
		}

		$coo_language_manager = MainFactory::create_object('LanguageTextManager', array('export_schemes', $_SESSION['languages_id']));

		$t_run_import = true;
		$t_import_start = microtime(true);

		$this->v_coo_csv_source->v_import_quote = $p_quote;

		if ($p_progress == 0)
		{
			$this->v_coo_csv_source->reset_import_data();
			$this->v_coo_csv_source->reset_products_id_cache();
		}

		if($this->v_coo_csv_source->get_pointer_position() == -1)
		{
			$this->v_coo_csv_source->set_filesize($p_filename);
		}

		$t_headline = $this->v_coo_csv_source->read_line($p_filename, 0);

		if($t_headline !== false)
		{
			$t_headline_data_array = $this->v_coo_csv_source->explode($t_headline, $p_separator, $p_quote);

			$this->v_coo_csv_source->set_import_function_lib($t_headline_data_array);
			$t_pointer_position = $this->v_coo_csv_source->get_pointer_position();

			$t_products_id = false;
			while($t_run_import === true && $t_line = $this->v_coo_csv_source->read_line($p_filename, $t_pointer_position))
			{
				$t_line_data_array = $this->v_coo_csv_source->explode($t_line, $p_separator, $p_quote);
				$t_import_data = $this->v_coo_csv_source->import_data_set($t_line_data_array);

				if ($t_import_data !== false && $t_import_data['products']['products_id'] != $t_products_id)
				{
					$this->v_coo_csv_source->add_products_id_to_cache($t_import_data['products']['products_id']);
					if(array_key_exists('products_properties_combis', $t_import_data) && array_key_exists('products_properties_combis_id', $t_import_data['products_properties_combis']) && $t_import_data['products_properties_combis']['products_properties_combis_id'] != '')
					{
						$this->v_coo_csv_source->add_products_id_to_cache($t_import_data['products']['products_id'], 'index');
					}
					$t_products_id = $t_import_data['products']['products_id'];
				}

				$t_pointer_position = $this->v_coo_csv_source->get_pointer_position();

				$t_actual_time = microtime(true);
				if( (int)($t_actual_time - $t_import_start) > 20 )
				{
					$t_run_import = false;
				}
			}

			if($t_line !== false)
			{
				$t_response_array['progress'] = $this->v_coo_csv_source->calc_import_progress();
				if($t_response_array['progress'] < 100)
				{
					$t_response_array['repeat'] = true;
				}
			}

			$t_response_array['job'] = $coo_language_manager->get_text('import_is_running');

			if($t_response_array['repeat'] === false)
			{
				if($this->v_coo_csv_source->get_products_id_from_cache() != false)
				{
					$t_response_array['job'] = $coo_language_manager->get_text('rebuild_properties_index');
					$t_response_array['progress'] = 0;
					$t_response_array['rebuild_index'] = true;
				}
				else
				{
					$t_response_array['job'] = $coo_language_manager->get_text('process_completed');
					$t_response_array['message'] = $this->v_coo_csv_source->build_import_statistics_message();
					$this->v_coo_csv_source->reset_import_data();
					$t_response_array['rebuild_index'] = false;
				}
			}
		}
		else
		{
			$t_response_array['error'] = true;
			$t_message = $coo_language_manager->get_text('import_error_wrong_format');
			$t_response_array['message'] = $t_message;
		}

		return $t_response_array;
	}
	
	public function processDeletions($p_deletions = array())
	{
		if(array_key_exists('delete_products', $p_deletions) && $p_deletions['delete_products'] == '1')
		{
			$this->_truncateProductsData();
			$this->_truncateImagesData();
			$this->_truncateAttributesData();
			$this->_truncatePropertiesData();
			$this->_truncateSpecialsData();
			$this->_truncateReviewsData();
			$this->_truncateXSellData(false);
		}
		
		if(array_key_exists('delete_images', $p_deletions) && $p_deletions['delete_images'] == '1')
		{
			$this->_truncateImagesData();
		}
		
		if(array_key_exists('delete_attributes', $p_deletions) && $p_deletions['delete_attributes'] == '1')
		{
			$this->_truncateAttributesData();
		}
		
		if(array_key_exists('delete_properties', $p_deletions) && $p_deletions['delete_properties'] == '1')
		{
			$this->_truncatePropertiesData();
		}
		
		if(array_key_exists('delete_specials', $p_deletions) && $p_deletions['delete_specials'] == '1')
		{
			$this->_truncateSpecialsData();
		}

		if(array_key_exists('delete_categories', $p_deletions) && $p_deletions['delete_categories'] == '1')
		{
			$this->_truncateCategoriesData();
		}
		
		if(array_key_exists('delete_manufacturers', $p_deletions) && $p_deletions['delete_manufacturers'] == '1')
		{
			$this->_truncateManufacturersData();
		}
		
		if(array_key_exists('delete_reviews', $p_deletions) && $p_deletions['delete_reviews'] == '1')
		{
			$this->_truncateReviewsData();
		}
		
		if(array_key_exists('delete_xsell', $p_deletions) && $p_deletions['delete_xsell'] == '1')
		{
			$this->_truncateXSellData();
		}
	}
	
	
	protected function _truncateProductsData()
	{
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_1');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_2');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_3');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_4');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_5');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_6');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_7');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_8');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_9');
		@xtc_db_query('TRUNCATE personal_offers_by_customers_status_10');
		@xtc_db_query('TRUNCATE products');
		@xtc_db_query('TRUNCATE products_quantity_unit');
		@xtc_db_query('TRUNCATE products_description');
		@xtc_db_query('TRUNCATE products_graduated_prices');
		@xtc_db_query('TRUNCATE products_to_categories');
		@xtc_db_query('TRUNCATE products_google_categories');
		@xtc_db_query('TRUNCATE products_item_codes');
		@xtc_db_query('TRUNCATE additional_field_values');
		@xtc_db_query('TRUNCATE additional_field_value_descriptions');
	}
	
	protected function _truncateImagesData()
	{
		$primaryImageQuery = 'UPDATE products 
									SET 
										products_image = "",
										products_image_w = 0,
										products_image_h = 0,
										gm_show_image = 0';
		@xtc_db_query($primaryImageQuery);
		$primaryImageQuery = 'UPDATE products_description SET gm_alt_text = ""';
		@xtc_db_query($primaryImageQuery);
		@xtc_db_query('TRUNCATE products_images');
		@xtc_db_query('TRUNCATE gm_prd_img_alt');
	}
	
	
	protected function _truncateAttributesData()
	{
		@xtc_db_query('TRUNCATE products_attributes');
		@xtc_db_query('TRUNCATE products_attributes_download');
	}
	
	protected function _truncatePropertiesData()
	{
		@xtc_db_query('TRUNCATE products_properties_combis');
		@xtc_db_query('TRUNCATE products_properties_combis_defaults');
		@xtc_db_query('TRUNCATE products_properties_combis_values');
		@xtc_db_query('TRUNCATE products_properties_index');
	}


	protected function _truncateSpecialsData()
	{
		@xtc_db_query('TRUNCATE specials');
	}
	
	
	protected function _truncateCategoriesData()
	{
		@xtc_db_query('TRUNCATE categories');
		@xtc_db_query('TRUNCATE categories_description');
	}
	
	
	protected function _truncateManufacturersData()
	{
		@xtc_db_query('TRUNCATE manufacturers');
	}
	
	
	protected function _truncateReviewsData()
	{
		@xtc_db_query('TRUNCATE reviews');
		@xtc_db_query('TRUNCATE reviews_description');
	}
	
	
	protected function _truncateXSellData($groups = true)
	{
		@xtc_db_query('TRUNCATE products_xsell');
		$groups ? @xtc_db_query('TRUNCATE products_xsell_grp_name') : null;
	}
	
	public function get_import_files_array()
	{
		return $this->v_coo_csv_source->get_import_files_array();
	}

	public function rebuild_properties_index()
	{
		$t_output_array = array();

		$t_products_id = $this->v_coo_csv_source->get_products_id_from_cache();
		$this->coo_properties_data_agent->rebuild_properties_index($t_products_id);
		$this->v_coo_csv_source->delete_products_id_from_cache($t_products_id, 'index');

		$t_new_products_id = $this->v_coo_csv_source->get_products_id_from_cache();
		if($t_new_products_id == false)
		{
			$coo_language_manager = MainFactory::create_object('LanguageTextManager', array('export_schemes', $_SESSION['languages_id']));
			$t_output_array['repeat'] = false;
			$t_output_array['job'] = $coo_language_manager->get_text('process_completed');
			$t_output_array['message'] = $this->v_coo_csv_source->build_import_statistics_message();
			$this->v_coo_csv_source->reset_import_data();
		}
		else
		{
			$t_output_array['repeat'] = true;
			$t_output_array['next_product_id'] = $t_new_products_id;
		}
		$t_output_array['progress'] = $this->v_coo_csv_source->get_products_id_cache_progress();
		return $t_output_array;
	}
}