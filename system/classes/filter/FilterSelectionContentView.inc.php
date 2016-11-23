<?php
/* --------------------------------------------------------------
   FilterSelectionContentView.inc.php 2014-07-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class FilterSelectionContentView extends ContentView
{
	protected $language_id;
	protected $feature_value_group_array;
	protected $coo_feature_set_source;
	
	
	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('module/filter_selection/filter_selection.html');
		$this->set_flat_assigns(true);
	}
	
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['language_id']				= array('type' => 'int');
		$this->validation_rules_array['feature_value_group_array']	= array('type' => 'array');
		$this->validation_rules_array['coo_feature_set_source']		= array('type' => 'object',
																			'object_type' => 'FeatureSetSource');
	}
	
	
	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('language_id', 'feature_value_group_array'));
		if(empty($t_uninitialized_array))
		{
			$this->coo_feature_set_source = MainFactory::create_object('FeatureSetSource');
			
			$this->add_filter_selection();
		}
		else
		{
			trigger_error("Variable(s) "
						  . implode(', ', $t_uninitialized_array)
						  . " do(es) not exist in class "
						  . get_class($this)
						  . " or is/are null"
				, E_USER_ERROR
			);
		}
	}
	
	
	protected function add_filter_selection()
	{
		$t_feature_values_array = $this->get_feature_values_array();

		$t_filter_selection_array = array();
		
		foreach($t_feature_values_array as $t_feature_id => $t_feature_values)
		{
			$t_filter_selection_array[$t_feature_id] = $this->get_feature_data($t_feature_id, $t_feature_values);
		}
		
		$this->content_array['FILTER_SELECTION_ARRAY'] = $t_filter_selection_array;
	}
	
	
	protected function get_feature_data($p_feature_id, $p_feature_values)
	{
		$t_feature_array = array();
		
		$t_feature_query = $this->get_feature_sql_query($p_feature_id);
		$t_result = xtc_db_query($t_feature_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_feature_row = xtc_db_fetch_array($t_result);
			$t_feature_array['feature_name'] = $t_feature_row['feature_name'];
			$t_feature_array['feature_values'] = array();
			
			foreach($p_feature_values as $t_feature_value_id)
			{
				$t_feature_array['feature_values'][$t_feature_value_id] = $this->get_feature_value_data($t_feature_value_id);
			}
		}
		
		return $t_feature_array;
	}
	
	
	protected function get_feature_value_data($p_feature_value_id)
	{
		$t_feature_value_array = array();
		
		$t_feature_value_query = $this->get_feature_value_sql_query($p_feature_value_id);
		$t_result = xtc_db_query($t_feature_value_query);
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_feature_value_row = xtc_db_fetch_array($t_result);
			$t_feature_value_array = $t_feature_value_row['feature_value_text'];
		}
		
		return $t_feature_value_array;
	}

	
	protected function get_feature_values_array()
	{
		$t_selected_feature_values_id_array = array();

		for($i = 0; $i < count($this->feature_value_group_array); $i++)
		{
			$t_selected_feature_values_id_array = array_merge($t_selected_feature_values_id_array,
															  $this->feature_value_group_array[$i]['FEATURE_VALUE_ID_ARRAY'])
			;
		}
		$t_feature_values_array = $this->coo_feature_set_source->convert_values_array_to_feature_values_array($t_selected_feature_values_id_array);

		return $t_feature_values_array;
	}
	
	
	protected function get_feature_sql_query($p_feature_id)
	{
		$t_feature_query = 'SELECT
								*
							FROM
								feature_description
							WHERE
								feature_id	= "' . (int)$p_feature_id . '"
							AND
								language_id	= "' . $this->language_id . '"'
		;
		
		return $t_feature_query;
	}
	
	
	protected function get_feature_value_sql_query($p_feature_value_id)
	{
		$t_feature_value_query = 'SELECT
									*
								FROM
									feature_value_description
								WHERE
									feature_value_id = "' . (int)$p_feature_value_id . '"
								AND
									language_id = "' . $this->language_id . '"'
		;
		
		return $t_feature_value_query;
	}
}