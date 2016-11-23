<?php
/* --------------------------------------------------------------
  CSVAjaxHandler.inc.php 2016-05-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
require(DIR_FS_CATALOG . 'gm/inc/gm_prepare_filename.inc.php');

class CSVAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id = NULL)
	{
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = true;

		if(isset($this->v_data_array['GET']['action']))
		{
			$t_action_request = $this->v_data_array['GET']['action'];

			$coo_csv_control = MainFactory::create_object('CSVControl', array(), true);

			if($t_action_request == 'export')
			{
				$t_token = $coo_csv_control->get_secure_token();

				if($t_token != $this->v_data_array['GET']['token'])
				{
					return false;
				}

				$t_scheme_id = 0;
				if(isset($this->v_data_array['GET']['scheme_id']))
				{
					$t_scheme_id = $this->v_data_array['GET']['scheme_id'];
				}

				$t_output_array['repeat'] = $coo_csv_control->export($t_scheme_id);

				if($t_scheme_id != 0)
				{
					$coo_csv_source = MainFactory::create_object('CSVSource', array(), true);
					$t_export_status = $coo_csv_source->get_export_status($t_scheme_id);
					$t_output_array['offset'] = $t_export_status['offset'];
					$t_output_array['products_count'] = $t_export_status['products_count'];
					$t_progress = 100;
					if($t_output_array['offset'] > 0)
					{
						$t_progress = (int)(($t_output_array['offset'] / $t_output_array['products_count']) * 100);
						if($t_progress > 100)
						{
							$t_progress = 100;
						}
						if($t_progress < 1)
						{
							$t_progress = 1;
						}
					}
					$coo_language_manager = MainFactory::create_object('LanguageTextManager', array('export_schemes', $_SESSION['languages_id']));
					$t_output_array['job'] = $coo_language_manager->get_text('process_completed');
					if($t_output_array['repeat'] == true)
					{
						$t_output_array['job'] = $coo_language_manager->get_text('export_is_running');
					}
					$t_output_array['progress'] = $t_progress;

					$coo_scheme = $coo_csv_control->get_scheme($t_scheme_id);
					$t_output_array['export_type'] = $coo_scheme->v_data_array['type_id'];

					$coo_csv_content_view = MainFactory::create_object('CSVContentView');
					$t_html = $coo_csv_content_view->get_html(array("template" => "export_scheme_overview.html", "export_type" => $coo_scheme->v_data_array['type_id']), array());
					$t_output_array['html'] = $t_html;
				}
				$t_output_array['scheme_id'] = $t_scheme_id;
			}
			else if($_SESSION['customers_status']['customers_status_id'] === '0')
			{
				switch($t_action_request)
				{
					case 'get_template':
						$t_output_array['html'] = $this->get_template();
						if($this->v_data_array['GET']['template'] == 'export_scheme_fields.html' && !isset($this->v_data_array['GET']['field_id']))
						{
							$this->v_data_array['GET']['template'] = 'export_scheme_preview.html';
							$t_output_array['html_preview'] = $this->get_template();
						}
						break;

					case 'save_scheme':
						$t_scheme_data = array();
						parse_str($this->v_data_array['POST']['configuration'], $t_scheme_data);
						foreach($t_scheme_data AS $t_data_key => $t_data_value)
						{
							if(is_array($t_data_value))
							{
								$t_clean_data = array();
								foreach($t_data_value as $t_elem)
								{
									$t_clean_data[] = str_replace("\'", "'", str_replace('\"', '"', str_replace('\\\\', '\\', str_replace('\\\\\\', '\\', $t_elem))));
								}
							}
							else
							{
								$t_clean_data = str_replace("\'", "'", str_replace('\"', '"', str_replace('\\\\', '\\', str_replace('\\\\\\', '\\', $t_data_value))));
							}

							$t_scheme_data[$t_data_key] = $t_clean_data;
						}
						$t_scheme_id = $coo_csv_control->save_scheme($t_scheme_data);
						$coo_scheme = $coo_csv_control->get_scheme($t_scheme_id);
						$this->v_data_array['GET']['template'] = 'export_scheme_overview.html';
						$this->v_data_array['GET']['export_type'] = $coo_scheme->v_data_array['type_id'];

						$t_output_array['html'] = $this->get_template();
						$t_output_array['scheme_id'] = $t_scheme_id;
						$t_output_array['export_type'] = $coo_scheme->v_data_array['type_id'];
						break;

					case 'delete_scheme':
						$t_output_array['scheme_id'] = $this->v_data_array['POST']['scheme_id'];
						$t_output_array['status'] = $coo_csv_control->delete_scheme($this->v_data_array['POST']['scheme_id']);
						break;

					case 'download_export_file':
						$coo_scheme = $coo_csv_control->get_scheme($this->v_data_array['GET']['scheme_id']);
						header("Content-Type: application/force-download");
						header("Content-Type: application/octet-stream");
						header('Content-Disposition: attachment; filename="' . basename($coo_scheme->v_data_array['filename']) . '"');
						header("Content-Transfer-Encoding: binary");
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Pragma: public');
						header('Content-Length: ' . filesize('export/' . basename($coo_scheme->v_data_array['filename'])));
						echo file_get_contents('export/' . basename($coo_scheme->v_data_array['filename']));
						exit(0);
						break;

					case 'save_fields':
						parse_str($this->v_data_array['POST']['field_data'], $t_content_data);
						$c_scheme_id = (int)$t_content_data['scheme_id'];
						$coo_csv_control->delete_fields_by_fields_array($c_scheme_id, $t_content_data['field_id']);

						$t_count_fields = count($t_content_data['field_id']);
						for($i = 0; $i < $t_count_fields; $i++)
						{
							$t_field_data = array();
							$t_field_data['field_id'] = $t_content_data['field_id'][$i];
							$t_field_data['field_name'] = $t_content_data['field_name'][$i];
							$t_field_data['field_content'] = $t_content_data['field_content'][$i];
							$t_field_data['field_content_default'] = $t_content_data['field_content_default'][$i];
							$t_field_data['sort_order'] = $i + 1;
							$t_field_data['scheme_id'] = $c_scheme_id;
							$t_field_data['created_by'] = 'custom';
							if($t_content_data['field_created_by'][$i] == 1)
							{
								$t_field_data['created_by'] = 'gambio';
							}
							$coo_csv_control->save_field($t_field_data);
						}

						if((int)$t_content_data['select_properties_language'] > 0)
						{
							$coo_scheme = $coo_csv_control->get_scheme($c_scheme_id);
							$coo_scheme->v_data_array['languages_id'] = (int)$t_content_data['select_properties_language'];
							$coo_scheme->save();
						}

						$coo_csv_control->save_scheme_properties($c_scheme_id, $t_content_data['field_properties_data']);

						$t_output_array['status'] = true;
						break;

					case 'save_collective_fields':
						parse_str($this->v_data_array['POST']['field_data'], $t_content_data);
						$c_scheme_id = (int)$this->v_data_array['POST']['scheme_id'];

						if(isset($t_content_data['delete_collective_fields']) && $t_content_data['delete_collective_fields'] != '')
						{
							$coo_csv_control->delete_fields_by_fields_array($c_scheme_id, explode(',', $t_content_data['delete_collective_fields']), true);
						}
						
						$t_count_fields = count($t_content_data['field_id']);
						for($i = 0; $i < $t_count_fields; $i++)
						{
							if(trim($t_content_data['field_name'][$i]) != '' || $t_content_data['collective_field_source'][$i] != '')
							{
								$t_field_data = array();
								$t_field_data['field_id'] = $t_content_data['field_id'][$i];
								$t_field_data['field_name'] = $t_content_data['field_name'][$i];
								$t_collective_content = $coo_csv_control->set_collective_variable(explode(';', $t_content_data['collective_field_source'][$i]), array('properties' => $t_content_data['include_properties'][$i], 'attributes' => $t_content_data['include_attributes'][$i], 'additional_fields' => $t_content_data['include_additional_fields'][$i]), $t_content_data['field_id'][$i]);
								$t_field_data['field_content'] = $t_collective_content;
								$t_field_data['field_content_default'] = $t_content_data['field_content_default'][$i];
								$t_field_data['scheme_id'] = $c_scheme_id;
								$t_field_data['created_by'] = 'custom';
								if($t_content_data['field_created_by'][$i] == 1)
								{
									$t_field_data['created_by'] = 'gambio';
								}
								$coo_csv_control->save_field($t_field_data);
							}
						}

						$t_output_array['status'] = true;
						$coo_csv_content_view = MainFactory::create_object('CSVContentView');
						$t_html = $coo_csv_content_view->get_html(array("template" => "export_scheme_collective_fields.html", "scheme_id" => $c_scheme_id), array());
						$t_output_array['html'] = $t_html;
						break;

					case 'save_categories':
						$t_scheme_id = $this->v_data_array['POST']['scheme_id'];
						$t_select_all = $this->v_data_array['POST']['select_all'];
						$t_selected_categories = array();
						$t_tmp_selected_categories = explode(',', $this->v_data_array['POST']['selected_categories']);
						$t_bequeathing_categories = array();
						$t_tmp_bequeathing_categories = explode(',', $this->v_data_array['POST']['bequeathing_categories']);

						if($t_select_all == 'false')
						{
							foreach($t_tmp_selected_categories as $t_category)
							{
								$t_tmp = explode('=', $t_category);
								$t_selected_categories[$t_tmp[0]] = $t_tmp[1];
							}

							foreach($t_tmp_bequeathing_categories as $t_category)
							{
								$t_tmp = explode('=', $t_category);
								$t_bequeathing_categories[$t_tmp[0]] = $t_tmp[1];
							}

							$t_success = $coo_csv_control->save_categories($t_scheme_id, false, $t_selected_categories, $t_bequeathing_categories);
						}
						else
						{
							$t_success = $coo_csv_control->save_categories($t_scheme_id);
						}

						$t_output_array['status'] = $t_success;
						break;

					case 'copy_scheme':
						$t_orig_scheme_id = (int)$this->v_data_array['POST']['scheme_id'];
						$t_scheme_id = $coo_csv_control->copy_scheme($t_orig_scheme_id);

						$coo_scheme = $coo_csv_control->get_scheme($t_scheme_id);

						$coo_csv_content_view = MainFactory::create_object('CSVContentView');
						$t_html = $coo_csv_content_view->get_html(array("template" => "export_scheme_overview.html", "export_type" => $coo_scheme->v_data_array['type_id']), array());

						$t_output_array['export_type'] = $coo_scheme->v_data_array['type_id'];
						$t_output_array['scheme_id'] = $t_orig_scheme_id;
						$t_output_array['html'] = $t_html;
						break;

					case 'stop_cronjob':
						$coo_csv_control->cronjob_allowed($this->v_data_array['POST']['status']);
						if($this->v_data_array['POST']['status'] == 'false')
						{
							$coo_csv_control->pause_cronjob('false');
						}
						$t_output_array['cronjob_status'] = $coo_csv_control->get_cronjob_status_array(false);
						break;

					case 'pause_cronjob':
						$coo_csv_control->pause_cronjob($this->v_data_array['POST']['status']);
						$t_output_array['cronjob_status'] = $coo_csv_control->get_cronjob_status_array(false);
						break;

					case 'clean_export':
						$coo_csv_control->clean_export($this->v_data_array['POST']['scheme_id']);
						$t_output_array['status'] = true;
						break;
					case 'get_cronjob_status':
						$t_scheme_id = false;
						if(isset($this->v_data_array['GET']['scheme_id']))
						{
							$t_scheme_id = $this->v_data_array['GET']['scheme_id'];
						}
						$t_output_array['cronjob_status'] = $coo_csv_control->get_cronjob_status_array($t_scheme_id);
						break;
					case 'upload_import_file':
						if(!empty($_FILES['upload_import_file']['name']))
						{
							$t_response = $coo_csv_control->upload();
							if($t_response['status'] === false)
							{
								$t_output_array['status'] = 'error';
								$t_output_array['error_code'] = 'import_zip_content';
							}
							else
							{
								$t_output_array['select_file'] = $t_response['filename'];
								$t_output_array['file_list'] = $coo_csv_control->get_import_files_array();
								$t_output_array['status'] = "uploaded";
							}
						}
						else
						{
							$t_output_array['status'] = "no_file_selected";
						}
						break;
					case 'import':
						if(isset($this->v_data_array['POST']['select_import_file']) && isset($this->v_data_array['POST']['import_field_separator']))
						{
							$t_filename = $this->v_data_array['POST']['select_import_file'];
							$t_separator = $this->v_data_array['POST']['import_field_separator'];
							$t_quote = $this->v_data_array['POST']['import_field_quotes'];
							$t_deletions = $this->v_data_array['POST']['deletions'];
							$t_quote = str_replace('\"', '"', $t_quote);
							$t_quote = str_replace("\'", "'", $t_quote);
							$t_progress = $this->v_data_array['POST']['progress'];
							$t_output_array = $coo_csv_control->import($t_filename, $t_separator, $t_quote, $t_deletions, $t_progress);
						}
						break;
					case 'rebuild_properties_index':
						$t_output_array = $coo_csv_control->rebuild_properties_index();
						break;

					default:
						trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
						return false;
				}
			}
			else
			{
				trigger_error('access denied');
			}
		}

		if($t_enable_json_output)
		{
			if(function_exists('json_encode'))
			{
				$t_output_json = json_encode($t_output_array);
			}
			else
			{
				require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');
				$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
				$t_output_json = $coo_json->encode($t_output_array);
			}

			$this->v_output_buffer = $t_output_json;
		}

		return true;
	}

	function get_template()
	{
		$t_html = '';
		$f_get_array = array();
		if(isset($this->v_data_array['GET']))
		{
			$f_get_array = $this->v_data_array['GET'];
		}
		$f_post_array = array();
		if(isset($this->v_data_array['POST']))
		{
			$f_post_array = $this->v_data_array['POST'];
		}

		$coo_csv_content_view = MainFactory::create_object('CSVContentView');
		$t_html = $coo_csv_content_view->get_html($f_get_array, $f_post_array);

		return $t_html;
	}
}