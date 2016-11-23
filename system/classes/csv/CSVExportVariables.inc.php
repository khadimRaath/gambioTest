<?php
/* --------------------------------------------------------------
   CSVFieldModel.inc.php 2014-08-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

define('CSV_EXPORT_TYPE_PRODUCT_EXPORT', 1);
define('CSV_EXPORT_TYPE_PRICE_COMPARISON', 2);

/**
 * Description of CSVExportVariables
 *
 * @author wu
 */
class CSVExportVariables
{
	protected $v_variables_path = '';
	protected $v_variables_array = array();
	protected $v_languages = array();
	protected $v_coo_text_mgr = null;
	
	public function __construct($p_variables_path = false)
	{
		$this->v_variables_path = $p_variables_path !== false && strpos($p_variables_path, DIR_FS_CATALOG) === 0 ? $p_variables_path : DIR_FS_CATALOG . 'system/conf/export/export_variables.inc.php';
		$this->v_coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('export_schemes_variables', $_SESSION['languages_id']));
		$this->load_languages();
		$this->load_variables($this->v_variables_path);
		$this->load_additional_field_variables();
	}
	
	protected function load_variables($p_variables_path)
	{
		include($p_variables_path);
	}
	
	protected function load_additional_field_variables()
	{
		$t_sql = 'SELECT
						af.additional_field_id,
						af.multilingual
					FROM
						additional_fields af';
		$t_result = xtc_db_query($t_sql);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			if($t_row['multilingual'] == '1')
			{
				foreach ($this->v_languages as $t_language_id => $t_language)
				{
					$this->add_variable(array('name' => 'p_additional_field#',
											  'has_lang' => true,
											  'product_export_forbidden' => false,
											  'price_comparison_forbidden' => false
										),
										$t_language_id,
										$t_row['additional_field_id']
					);
				}
			}
			else
			{
				$this->add_variable(array('name' => 'p_additional_field#',
										  'has_lang' => false,
										  'product_export_forbidden' => false,
										  'price_comparison_forbidden' => false
									),
									false,
									$t_row['additional_field_id']
				);
			}
		}
	}
	
	protected function load_languages()
	{
		$t_sql = "SELECT code, name, languages_id FROM " . TABLE_LANGUAGES;
		$t_result = xtc_db_query($t_sql);
		$this->v_languages = array();
		while ($t_row = xtc_db_fetch_array($t_result))
		{
			$this->v_languages[$t_row['languages_id']] = array('name' => $t_row['name'], 'code' => $t_row['code']);
		}
	}
	
	protected function add_variables($p_variables_array = array())
	{
		foreach ($p_variables_array as $p_variable)
		{
			if (!empty($p_variable['has_lang']))
			{
				foreach ($this->v_languages as $t_language_id => $t_language)
				{
					if (!empty($p_variable['start_index']) && !empty($p_variable['max_index']))
					{
						for ($i = (int) $p_variable['start_index']; $i < (int) $p_variable['max_index'] + 1; $i++)
						{
							$this->add_variable($p_variable, $t_language_id, $i);
						}
					}
					else
					{
						$this->add_variable($p_variable, $t_language_id);
					}
				}
			}
			else if (!empty($p_variable['start_index']) && !empty($p_variable['max_index']))
			{
				for ($i = (int) $p_variable['start_index']; $i < (int) $p_variable['max_index'] + 1; $i++)
				{
					$this->add_variable($p_variable, false, $i);
				}
			}
			else
			{
				$this->add_variable($p_variable);
			}
		}
		$this->add_variable_exceptions();
	}
	
	function add_variable_exceptions()
	{
		$t_sql = "SELECT customers_status_id, customers_status_name FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = " . $_SESSION['languages_id'] . " ORDER BY customers_status_id";
		$t_result = xtc_db_query($t_sql);
		while ($t_row = xtc_db_fetch_array($t_result))
		{
			if ($t_row['customers_status_id'] != 0)
			{
				$t_key = 'p_personal_offer#' . $t_row['customers_status_id'];
				$this->v_variables_array[$t_key] = array();
				$this->v_variables_array[$t_key]['title'] = sprintf($this->v_coo_text_mgr->get_text('p_personal_offer#_title'), htmlentities_wrapper($t_row['customers_status_name']));
				$this->v_variables_array[$t_key]['description'] = sprintf($this->v_coo_text_mgr->get_text('p_personal_offer#_description'), htmlentities_wrapper($t_row['customers_status_name']));
				$this->v_variables_array[$t_key][CSV_EXPORT_TYPE_PRICE_COMPARISON] = false;
				$this->v_variables_array[$t_key][CSV_EXPORT_TYPE_PRODUCT_EXPORT] = true;
			}
			
			$t_key = 'p_group_permission#' . $t_row['customers_status_id'];
			$this->v_variables_array[$t_key] = array();
			$this->v_variables_array[$t_key]['title'] = sprintf($this->v_coo_text_mgr->get_text('p_group_permission#_title'), htmlentities_wrapper($t_row['customers_status_name']));
			$this->v_variables_array[$t_key]['description'] = $this->v_coo_text_mgr->get_text('p_group_permission#_description');
			$this->v_variables_array[$t_key][CSV_EXPORT_TYPE_PRICE_COMPARISON] = false;
			$this->v_variables_array[$t_key][CSV_EXPORT_TYPE_PRODUCT_EXPORT] = true;
		}
	}
	
	function add_variable($p_variable_data, $p_lang_id = false, $p_index = false)
	{
		$t_field_name = $p_variable_data['name'];
		$t_price_comparison_forbidden = isset($p_variable_data['price_comparison_forbidden']) && !empty($p_variable_data['price_comparison_forbidden']);
		$t_product_export_forbidden = isset($p_variable_data['product_export_forbidden']) && !empty($p_variable_data['product_export_forbidden']);
		
		$t_lang_code = false;
		if($p_lang_id !== false)
		{
			$t_lang_code = $this->v_languages[$p_lang_id]['code'];
		}
		
		$t_key = $t_field_name;
		if ($p_index !== false)
		{
			$t_key .= (int) $p_index;
		}
		if ($t_lang_code !== false)
		{
			$t_key .= '.' . $t_lang_code;
		}

		$this->v_variables_array[$t_key] = array();
		if($t_field_name == 'p_additional_field#')
		{
			if($p_lang_id === false)
			{
				$p_lang_id = $_SESSION['languages_id'];
			}
			$coo_additional_field = MainFactory::create_object('AdditionalField', array($p_index));
			$t_additional_field_name_array = $coo_additional_field->get_name_array();
			
			$this->v_variables_array[$t_key]['title'] = '[' . $this->v_coo_text_mgr->get_text('ADDITIONAL_FIELD') . '] ' . $t_additional_field_name_array[$p_lang_id] . ($t_lang_code !== false ? ' (' . $t_lang_code . ')' : '');
			$this->v_variables_array[$t_key]['description'] = $this->v_coo_text_mgr->get_text('ADDITIONAL_FIELD') . ': ' . ($t_lang_code !== false ? '(' . $t_lang_code . ') ' : '') . $t_additional_field_name_array[$p_lang_id];
		}
		else if($p_index !== false)
		{
			$this->v_variables_array[$t_key]['title'] = sprintf($this->v_coo_text_mgr->get_text($t_field_name . '_title'), $p_index) . ($t_lang_code !== false ? ' (' . $t_lang_code . ')' : '');
			$this->v_variables_array[$t_key]['description'] = ($t_lang_code !== false ? '(' . $t_lang_code . ') ' : '') . sprintf($this->v_coo_text_mgr->get_text($t_field_name . '_description'), $p_index);
		}
		else
		{
			$this->v_variables_array[$t_key]['title'] = $this->v_coo_text_mgr->get_text($t_field_name . '_title') . ($t_lang_code !== false ? ' (' . $t_lang_code . ')' : '');
			$this->v_variables_array[$t_key]['description'] = ($t_lang_code !== false ? '(' . $t_lang_code . ') ' : '') . $this->v_coo_text_mgr->get_text($t_field_name . '_description');
		}
		$this->v_variables_array[$t_key][CSV_EXPORT_TYPE_PRICE_COMPARISON] = !$t_price_comparison_forbidden;
		$this->v_variables_array[$t_key][CSV_EXPORT_TYPE_PRODUCT_EXPORT] = !$t_product_export_forbidden;
	}
	
	function sort_by_variable_title($a, $b)
	{
		if ($a['title'] < $b['title'])
		{
			return -1;
		}
		return 1;
	}
	
	public function get_variables_array()
	{
		uasort($this->v_variables_array, array(get_class(), 'sort_by_variable_title'));
		return $this->v_variables_array;
	}
}
