<?php
/* --------------------------------------------------------------
   AttributeImagesContentView.inc.php 2014-02-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AttributeImagesContentView extends ContentView
{
	protected $options_ids;
	protected $values_ids;
	protected $language_id;
	
	function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/gm_attribute_images.html');
		$this->set_flat_assigns(true);
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['language_id']	= array('type' => 'int');
		$this->validation_rules_array['options_ids']	= array('type' => 'string');
		$this->validation_rules_array['values_ids']		= array('type' => 'string');
	}
	
	public function prepare_data()
	{
		$this->build_html = false;
		$t_uninitialized_array = $this->get_uninitialized_variables(array('language_id',
																		  'options_ids',
																		  'values_ids')
		);

		if(empty($t_uninitialized_array))
		{
			$t_options_ids_array	= explode(',', substr($this->options_ids, 0, -1));
			$t_values_ids_array		= explode(',', substr($this->values_ids, 0, -1));

			for($i = 0; $i < count($t_options_ids_array); $i++)
			{
				$t_from = strpos($t_options_ids_array[$i], '[');
				$c_products_options_id = (int)substr($t_options_ids_array[$i], $t_from + 1, -1);

				$t_sql = '
					SELECT 
						po.products_options_name, 
						pov.products_options_values_name, 
						pov.gm_filename
					FROM 
						products_options po,
						products_options_values pov 
					WHERE 
						po.products_options_id = "' . $c_products_options_id . '" 
						AND po.language_id = "' . $this->language_id . '" 
						AND pov.language_id = "' . $this->language_id . '" 
						AND pov.products_options_values_id = "' . (int)$t_values_ids_array[$i] . '"  
					LIMIT 1
			';
				$t_result = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_row = xtc_db_fetch_array($t_result);
					if(empty($t_row['gm_filename']) == false)
					{
						$this->content_array['attributes'][] = array(
							'NAME' 			=> $t_row['products_options_name'],
							'OPTIONS_NAME'	=> $t_row['products_options_values_name'],
							'IMAGE' 		=> $t_row['gm_filename']
						);
						$this->build_html = true;
					}
				}
			}
			$this->content_array['IMAGES_PATH'] = DIR_WS_CATALOG . DIR_WS_IMAGES . 'product_images/attribute_images/';
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
}