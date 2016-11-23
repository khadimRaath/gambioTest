<?php
/* --------------------------------------------------------------
   CSVSchemeModel.inc.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of CSVSchemeModel
 */
class CSVSchemeModel extends BaseClass
{	
	public $v_scheme_id = 0;
	public $v_data_array = array();
	public $v_fields_array = array();
	public $v_properties_array = array();
	protected $v_categories_array = array();
	protected $v_base_path = '';
	protected $v_sorted_field_names = array();
	
	public function __construct( $p_scheme_id = 0 )
	{
		$this->set_scheme_id($p_scheme_id);
		if($this->get_scheme_id() > 0)
		{
			$this->load_data();
		}
		if (file_exists(DIR_FS_CATALOG . 'export/'))
		{
			$this->set_base_path(DIR_FS_CATALOG . 'export/');
		}
	}
	
	protected function load_data()
	{
		$this->v_data_array = array();
		
		$t_sql = "SELECT * 
					FROM export_schemes 
					WHERE scheme_id = '" . $this->get_scheme_id() . "'
					ORDER BY scheme_name ASC";
		$t_query = xtc_db_query($t_sql, 'db_link', false);
		if(xtc_db_num_rows($t_query) == 1)
		{
			$t_query_result = xtc_db_fetch_array($t_query);
			$this->v_data_array = $t_query_result;
		}
		
		return true;
	}
	
	public function load_fields()
	{
		$this->v_fields_array = array();
		
		$t_sql = "SELECT field_id
					FROM export_scheme_fields 
					WHERE scheme_id = '" . $this->get_scheme_id() .  "'
					ORDER BY sort_order ASC, field_id ASC";
		$t_query = xtc_db_query($t_sql, 'db_link', false);
		while($t_query_result = xtc_db_fetch_array($t_query))
		{
			$coo_field_model = MainFactory::create_object('CSVFieldModel', array($t_query_result['field_id']));
			$this->v_fields_array[$t_query_result['field_id']] = $coo_field_model;
		}
		
		return true;
	}
	
	public function load_properties()
	{
		if(isset($this->v_data_array['export_properties']) && $this->v_data_array['export_properties'] == '1')
		{
			$this->v_properties_array = array();

			$t_sql = 
					"
					SELECT
						properties_column
					FROM 
						export_scheme_properties
					WHERE
						scheme_id = '" . $this->get_scheme_id() . "'
					ORDER BY
						sort_order";

			$t_result = xtc_db_query($t_sql, 'db_link', false);

			while($t_row = xtc_db_fetch_array($t_result))
			{
				$this->v_properties_array[] = $t_row['properties_column'];
			}
		}
		
		return true;
	}
	
	public function load_categories()
	{
		$this->v_categories_array = array();
		
		$t_sql = "SELECT * 
					FROM export_scheme_categories 
					WHERE scheme_id = '" . $this->get_scheme_id() . "'
					ORDER BY categories_id ASC";
		$t_query = xtc_db_query($t_sql, 'db_link', false);
		while($t_query_result = xtc_db_fetch_array($t_query))
		{
			$this->v_categories_array[$t_query_result['categories_id']] = $t_query_result['selection_state'];
		}
		
		return true;
	}
	
	public function save($p_data_array=array())
	{
		if (empty($p_data_array))
		{
			$p_data_array = $this->v_data_array;
		}
		
		$c_scheme_id = false;
		
		if(isset($p_data_array['scheme_id']) && (int)$p_data_array['scheme_id'] > 0)
		{
			$c_scheme_id = (int)$p_data_array['scheme_id'];
			$t_date_modified = date('Y-m-d H:i:s');
		}
		else
		{
			$c_date_created = date('Y-m-d H:i:s');
		}
		
		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_schemes'));		
		$coo_data_object->set_keys(array('scheme_id' => $c_scheme_id));
		unset( $p_data_array['scheme_id'] );

		foreach( $p_data_array AS $t_data_key => $t_data_value )
		{
			if(is_array($t_data_value))
			{
				$t_data_value = implode('|', $t_data_value);
			}
			
			$coo_data_object->set_data_value( $t_data_key, $t_data_value );
		}
		
		if($c_scheme_id === false)
		{
			$coo_data_object->set_data_value('date_created', $c_date_created);			
		}
		else
		{
			$coo_data_object->set_data_value('date_modified', $t_date_modified);
		}

		$t_scheme_id = $coo_data_object->save_body_data();
		
		if($c_scheme_id !== false)
		{
			$t_scheme_id = $c_scheme_id;
		}
		
		$this->set_scheme_id($t_scheme_id);
		$this->load_data();
		
		if($this->v_data_array['cronjob_allowed'] == 1)
		{
			$this->save_next_due_date();
		}
		
		return $t_scheme_id;
	}
	
	/**
	* A function to create a scheme on the base of this scheme.
	* 
	* @return int ID of the new scheme
	*/
	public function copy()
	{
		$t_copy_count = 0;
		$t_copy_count_text = '';
		$t_scheme_name = str_replace( '[Gambio] ', '', $this->v_data_array['scheme_name'] );

		$coo_schemes = MainFactory::create_object('GMDataObjectGroup', array('export_schemes', array()));
		$coo_scheme_array = $coo_schemes->get_data_objects_array();
		
		foreach ($coo_scheme_array as $coo_scheme)
		{
			$t_actual_scheme_name = $coo_scheme->get_data_value('scheme_name');
			if ($t_actual_scheme_name == $t_scheme_name . ' Kopie' || strpos_wrapper($t_actual_scheme_name, $t_scheme_name . ' Kopie (') === 0)
			{
				$t_copy_count++;
			}
		}
		
		if ($t_copy_count > 0)
		{
			$t_copy_count_text = ' (' . $t_copy_count . ')';
		}
		
		$coo_new_scheme = clone $this;
		$coo_new_scheme->v_data_array['scheme_id'] = 0;
		$coo_new_scheme->v_data_array['scheme_name'] = str_replace( '[Gambio] ', '', $coo_new_scheme->v_data_array['scheme_name'] ) . ' Kopie' . $t_copy_count_text;
		$coo_new_scheme->v_data_array['filename'] = substr_wrapper($coo_new_scheme->v_data_array['filename'], 0, strpos_wrapper($coo_new_scheme->v_data_array['filename'], '.')) . '_Kopie' . str_replace( ' ', '_', $t_copy_count_text ) . substr($coo_new_scheme->v_data_array['filename'], strpos_wrapper($coo_new_scheme->v_data_array['filename'], '.'));
		$coo_new_scheme->v_data_array['date_modified'] = '';
		$coo_new_scheme->v_data_array['date_last_export'] = '';
		$coo_new_scheme->v_data_array['created_by'] = 'custom';
		$coo_new_scheme->v_data_array['cronjob_allowed'] = 0;
		$t_scheme_id = $coo_new_scheme->save();
		
		if (empty($this->v_fields_array))
		{
			$this->load_fields();
		}
		if (empty($this->v_categories_array))
		{
			$this->load_categories();
		}
		
		if (empty($this->v_properties_array))
		{
			$this->load_properties();
		}
		
		$t_fields_array = $this->v_fields_array;
		$this->set_scheme_id($t_scheme_id);
		
		foreach ($t_fields_array as $coo_field)
		{
			$coo_field->v_data_array['field_id'] = 0;
			$coo_field->v_data_array['scheme_id'] = $t_scheme_id;
			$coo_field->v_data_array['created_by'] = 'custom';
			$coo_field->save();
		}
		
		$this->save_categories();
		$this->save_properties();
		
		return $t_scheme_id;
	}
	
	public function delete()
	{
		if (empty($this->v_fields_array) || !is_array($this->v_fields_array))
		{
			$this->load_fields();
		}
		
		foreach($this->v_fields_array AS $coo_field_model)
		{
			$coo_field_model->delete();
		}
		
		$coo_categories = MainFactory::create_object('GMDataObject', array('export_scheme_categories'));
		$coo_categories->set_keys(array('scheme_id' => $this->get_scheme_id()));
		$coo_categories->delete();
		
		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_schemes'));
		$coo_data_object->set_keys(array('scheme_id' => $this->get_scheme_id()));
		$coo_data_object->delete();
		
		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_cronjobs'));
		$coo_data_object->set_keys(array('scheme_id' => $this->get_scheme_id()));
		$coo_data_object->delete();
		
		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_scheme_properties'));
		$coo_data_object->set_keys(array('scheme_id' => $this->get_scheme_id()));
		$coo_data_object->delete();
		
		return true;
	}
	
	public function add_field($p_data_array, $p_load_fields = true)
	{
		if( ( empty($this->v_fields_array) || !is_array($this->v_fields_array ) ) && $p_load_fields )
		{
			$this->load_fields();
			
			if( $p_data_array[ 'field_id' ] != 0 )
			{
				$coo_field_model = $this->v_fields_array[ $p_data_array[ 'field_id' ] ];
				unset( $p_data_array[ 'field_id' ] );
				$coo_field_model->v_data_array = $p_data_array;
			}
			else
			{
				$t_last_field_model = end( $this->v_fields_array );
				$coo_field_model = MainFactory::create_object( 'CSVFieldModel' );
				$coo_field_model->set_data_array( $p_data_array );
				$this->v_fields_array[] = $coo_field_model;
			}
		}
		else if( !$p_load_fields )
		{
			$coo_field_model = MainFactory::create_object( 'CSVFieldModel' );
			$coo_field_model->set_data_array( $p_data_array );
			$this->v_fields_array[] = $coo_field_model;
		}
		
		return true;
	}
	
	public function add_categories($p_category_ids = false)
	{
		if ($p_category_ids === false)
		{
			$this->v_categories_array = array(0 => 'self_all_sub');
			
			$coo_categories_group = MainFactory::create_object('GMDataObjectGroup', array('categories', array()));
			$t_data_object_array = $coo_categories_group->get_data_objects_array();
			
			foreach ($t_data_object_array as $t_data_object)
			{
				$this->v_categories_array[$t_data_object->get_data_value('categories_id')] = 'self_all_sub';
			}
		}
		else
		{
			foreach ($p_category_ids as $t_cat_id)
			{
				$this->v_categories_array[$t_cat_id] = self_all_sub;
			}
		}
		
		return true;
	}
	
	public function save_categories($p_category_ids = array())
	{
		if (!empty($p_category_ids))
		{
			$this->v_categories_array = $p_category_ids;
		}
		
		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_scheme_categories'));
		$coo_data_object->set_keys(array('scheme_id' => $this->get_scheme_id()));
		$coo_data_object->delete();
		$coo_data_object->set_keys(array());
		
		foreach ($this->v_categories_array as $t_category_id => $t_state)
		{
			if ($t_state != 'no_self_no_sub')
			{
				$coo_data_object->set_data_value('scheme_id', $this->get_scheme_id());
				$coo_data_object->set_data_value('categories_id', $t_category_id);
				$coo_data_object->set_data_value('selection_state', $t_state);
				$coo_data_object->save_body_data();
			}
		}
		
		$this->load_categories();
		
		return true;
	}
	
	public function save_properties($p_properties_array = array())
	{
		if (!empty($p_properties_array) && is_array($p_properties_array))
		{
			$this->v_properties_array = $p_properties_array;
		}
		
		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_scheme_properties'));
		$coo_data_object->set_keys(array('scheme_id' => $this->get_scheme_id()));
		$coo_data_object->delete();
		$coo_data_object->set_keys(array());
		
		foreach($this->v_properties_array as $t_property)
		{
			$coo_data_object->set_data_value('scheme_id', $this->get_scheme_id());
			$coo_data_object->set_data_value('properties_column', $t_property);
			$coo_data_object->save_body_data();
		}
		
		$this->load_properties();
		
		return true;
	}
	
	public function save_field_sort_order( $p_field_ids_array )
	{
		foreach($p_field_ids_array AS $t_sort_order => $t_field_id)
		{
			$coo_data_object = MainFactory::create_object('GMDataObject', array('export_scheme_fields'));		
			$coo_data_object->set_keys(array(
												'field_id' => $t_field_id,
												'scheme_id' => $this->get_scheme_id(),
											));
			$coo_data_object->set_data_value('sort_order', $t_sort_order);
			$coo_data_object->save_body_data();
		}
		
		$this->load_fields();
				
		return true;
	}
	
	public function get_sorted_field_names($p_reload = false)
	{
		if ($p_reload)
		{
			$this->v_sorted_field_names = array();
		}
		
		if (empty($this->v_sorted_field_names))
		{
			$t_sorted_fields = $this->v_fields_array;
			usort($t_sorted_fields, array($this, 'field_sort'));

			foreach ($t_sorted_fields as $t_field)
			{
				$this->v_sorted_field_names[] = $t_field->v_data_array['field_name'];
			}
		}
		
		return $this->v_sorted_field_names;
	}
	
	public function get_categories_array()
	{
		if(empty($this->v_categories_array))
		{
			$this->load_categories();
		}
		
		return $this->v_categories_array;
	}
	
	public function get_properties_array()
	{
		if(empty($this->v_properties_array))
		{
			$this->load_properties();
		}
		
		return $this->v_properties_array;
	}
	
	
	// FUNKTION WIRD MOMENTAN NIRGENDWO AUFGERUFEN
	// WIRD DIE FUNCKTION NOCH GEBRAUCHT?
	public function field_sort($a, $b)
	{
		if ($a->v_data_array['sort_order'] == $b->v_data_array['sort_order'])
		{
			return 0;
		}
		
		return ($a->v_data_array['sort_order'] < $b->v_data_array['sort_order']) ? -1 : 1;
	}
	
	public function get_scheme_id()
	{
		return (int)$this->v_scheme_id;
	}
	
	public function set_scheme_id( $p_scheme_id )
	{
		$this->v_scheme_id = (int)$p_scheme_id;
	}
	
	public function get_base_path()
	{
		return (string)$this->v_base_path;
	}
	
	public function set_base_path( $p_base_path )
	{
		$this->v_base_path = (string)$p_base_path;
	}
	
	protected function build_next_due_date()
	{
		$t_due_date = '1000-01-01 00:00:00';
		
		$t_week_days_array = array();
		$t_week_days = $this->v_data_array['cronjob_days'];
		$t_hour = (int)$this->v_data_array['cronjob_hour'];
		$t_interval = (int)$this->v_data_array['cronjob_interval'];		
		$t_week_days_array = explode('|', $t_week_days);		
		$t_hour_now = date('G');
		
		if($t_interval > 0)
		{
			$t_hour = $this->determine_hour($t_interval);
		}
				
		$t_today_allowed = ($t_hour_now < $t_hour);
		$t_week_day = $this->determine_week_day($t_week_days_array, $t_today_allowed);
		$t_due_date = date('Y-m-d H:i:s', strtotime('next ' . $t_week_day . ' ' . $t_hour . ':00'));
		if($t_today_allowed && $t_week_day == date('D'))
		{
			$t_due_date = date('Y-m-d H:i:s', strtotime($t_week_day . ' ' . $t_hour . ':00'));
		}
				
		return $t_due_date;
	}
	
	protected function determine_week_day($p_week_days_array, $t_today_allowed = true)
	{
		$t_times_array = array();
		foreach($p_week_days_array AS $t_week_day)
		{
			if($t_today_allowed === true && $t_week_day == date('D'))
			{
				$t_times_array[] = time();
			}
			else
			{
				$t_times_array[] = strtotime('next ' . $t_week_day);
			}			
		}
		
		return date('D', min($t_times_array));
	}
	
	protected function determine_hour($p_hour_interval)
	{
		$t_next_hour = $p_hour_interval;
		$t_hour = $p_hour_interval;
		
		for($t_hour; $t_hour <= 24; $t_hour+=$p_hour_interval)
		{
			if(date('G') < $t_hour)
			{
				if($t_hour == 24)
				{
					$t_next_hour = 0;
				}
				else
				{
					$t_next_hour = $t_hour;
				}
				
				break;
			}
		}
		
		return $t_next_hour;
	}
	
	public function save_next_due_date()
	{
		$t_cronjob_id = false;
		
		if($this->v_data_array['cronjob_allowed'] == 1)
		{
			$t_sql = "DELETE FROM export_cronjobs WHERE scheme_id = '" . $this->get_scheme_id() . "' AND due_date > NOW()";
			xtc_db_query($t_sql);
			
			$coo_data_object = MainFactory::create_object('GMDataObject', array('export_cronjobs'));		
			$coo_data_object->set_keys(array('cronjobs_id' => false));

			$coo_data_object->set_data_value('scheme_id', $this->get_scheme_id());
			$coo_data_object->set_data_value('due_date', $this->build_next_due_date());
			$t_cronjob_id = $coo_data_object->save_body_data();
		}
		
		return $t_cronjob_id;
	}
	
	
	/**
	* @return array('status' => ['no_cronjob', 'running', 'queueing', 'pending'], 'active' => boolean)
	*/
	public function get_cronjob_status_array($p_cache_data_array)
	{
		// format date
		$t_date_format = 'Y-m-d H:i';
		
		if( $_SESSION[ 'languages_id' ] == 2 )
		{
			$t_date_format = 'd.m.Y H:i';
		}		
		
		$t_cronjob_status_array = array();
		$t_cronjob_status_array['status'] = 'no_cronjob';
		$t_cronjob_status_array['active'] = true;
		if( $this->v_data_array['date_last_export'] != "1000-01-01 00:00:00" )
		{
			$t_cronjob_status_array['date_last_export'] = date($t_date_format, strtotime($this->v_data_array['date_last_export']));
		}
		else
		{
			$t_cronjob_status_array['date_last_export'] = '-';
		}
		
		$t_cronjob_status_array[ 'file_exists' ] = "false";
		if( file_exists( DIR_FS_CATALOG . 'export/' . basename( $this->v_data_array[ 'filename' ] ) ) )
		{
			$t_cronjob_status_array[ 'file_exists' ] = "true";
		}
			
		$coo_csv_contol = MainFactory::create_object('CSVControl', array(), true);
		
		if($coo_csv_contol->cronjob_paused() || $coo_csv_contol->cronjob_stopped())
		{
			$t_cronjob_status_array['active'] = false;
		}
			
		if($this->v_data_array['cronjob_allowed'] == 1)
		{
			$t_sql = "SELECT 
							UNIX_TIMESTAMP(s.date_last_export) date_last_export,
							UNIX_TIMESTAMP(c.due_date) AS due_date
						FROM 
							export_schemes s
						LEFT JOIN export_cronjobs AS c ON (s.scheme_id = c.scheme_id)
						WHERE 
							s.scheme_id = '" . $this->get_scheme_id() . "'
						ORDER BY c.due_date DESC LIMIT 1";
			$t_result = xtc_db_query( $t_sql );
			if(xtc_db_num_rows($t_result))
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				if( $this->v_data_array['date_last_export'] != "1000-01-01 00:00:00" )
				{
					$t_cronjob_status_array['date_last_export'] = date($t_date_format, $t_result_array['date_last_export']);
				}
				else
				{
					$t_cronjob_status_array['date_last_export'] = '-';
				}
				
				if(empty($t_result_array['due_date']) || time() > $t_result_array['due_date'])
				{
					if(isset($p_cache_data_array['cronjob'][$this->get_scheme_id()]['offset']) && !empty($p_cache_data_array['cronjob'][$this->get_scheme_id()]['offset']))
					{
						$t_cronjob_status_array['status'] = 'running';
					}
					else
					{
						$t_cronjob_status_array['status'] = 'queueing';
					}
				}
				else
				{
					$t_cronjob_status_array['due_date'] = date($t_date_format, $t_result_array['due_date']);
					$t_cronjob_status_array['status'] = 'pending';
				}
			}
			
			$coo_language_manager = MainFactory::create_object('LanguageTextManager', array('export_schemes', $_SESSION['languages_id']));
		
			switch($t_cronjob_status_array['status'])
			{
				case 'running':
					$t_progress = (int)(($p_cache_data_array['cronjob'][$this->get_scheme_id()]['offset'] / $p_cache_data_array['cronjob'][$this->get_scheme_id()]['products_count'])*100);
					if($t_progress > 100)
					{
						$t_progress = 100;
					}
					if($t_progress < 1)
					{
						$t_progress = 1;
					}

					$t_cronjob_status_array['message'] = sprintf($coo_language_manager->get_text('tooltip_conf_cronjob_running'), $t_progress);
					break;
				case 'queueing':
					$t_cronjob_status_array['message'] = $coo_language_manager->get_text('tooltip_conf_cronjob_queueing');
					break;
				case 'pending':
					$t_cronjob_status_array['message'] = sprintf($coo_language_manager->get_text('tooltip_conf_cronjob_pending'), $t_cronjob_status_array['due_date']);
					break;
			}
			
			if($coo_csv_contol->cronjob_paused() || $coo_csv_contol->cronjob_stopped())
			{
				$t_cronjob_status_array['message'] = $coo_language_manager->get_text('tooltip_conf_cronjob_paused');
			}
		}
		
		
		
		return $t_cronjob_status_array;
	}
}
