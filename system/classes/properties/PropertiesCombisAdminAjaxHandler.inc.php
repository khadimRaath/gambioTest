<?php
/* --------------------------------------------------------------
   PropertiesCombisAdminAjaxHandler.inc.php 2016-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class PropertiesCombisAdminAjaxHandler extends AjaxHandler
{
	protected $languageTextManager;
	
    function get_permission_status($p_customers_id=NULL)
    {
        if($_SESSION['customers_status']['customers_status_id'] === '0')
        {
            #admins only
            return true;
        }
        return false;
    }

    function proceed()
    {
		$this->languageTextManager = MainFactory::create_object('LanguageTextManager', array('combis', $_SESSION['languages_id']));
		
        $t_output_array = array();
        $t_enable_json_output = true;

        $t_action_request = $this->v_data_array['GET']['action'];

        switch($t_action_request)
        {
            case 'load':
                $t_enable_json_output = false;
                $coo_properties_combis_admin_view = MainFactory::create_object('PropertiesCombisAdminContentView');
                $this->v_output_buffer = $coo_properties_combis_admin_view->get_html($this->v_data_array['GET']);
                break;

            case 'save':
                $coo_properties_combis_admin_control = MainFactory::create_object('PropertiesCombisAdminControl');
                switch($this->v_data_array['GET']['type'])
                {
                    case 'combis':
						$t_output_array = $coo_properties_combis_admin_control->save_combis($this->v_data_array['POST'], true, PRICE_IS_BRUTTO === 'true');
					
						if(!$t_output_array['combis_exists'])
						{
							$coo_data_agent = MainFactory::create_object('PropertiesDataAgent');
							$coo_data_agent->rebuild_properties_index($this->v_data_array['POST']['products_id']);
						}
                        break;

                    case 'combis_settings':
                        $t_output_array['status'] = $coo_properties_combis_admin_control->save_combis_settings($this->v_data_array['POST']);
                        break;

                    case 'combis_defaults':
                        $t_output_array = $coo_properties_combis_admin_control->save_combis_defaults($this->v_data_array['POST']);
                        break;

                    case 'admin_select':
                        $t_output_array = $coo_properties_combis_admin_control->save_admin_select($this->v_data_array['POST']['products_id'], $this->v_data_array['POST']['properties_values_ids_array']);
                        break;

                    default:
                        $t_enable_json_output = false;
                        trigger_error('unknown delete_type: '. $this->v_data_array['GET']['type'], E_USER_ERROR);
                }
                break;

            case 'delete':
				$coo_properties_combis_admin_control = MainFactory::create_object('PropertiesCombisAdminControl');
                switch($this->v_data_array['GET']['type'])
                {
                    case 'selected':
					case 'combis':
						$t_output_array = $coo_properties_combis_admin_control->delete_combis($this->v_data_array['POST']['properties_combis_id_array']);
                        break;

                    case 'all':
                        $t_output_array = $coo_properties_combis_admin_control->delete_all_combis($this->v_data_array['POST']['products_id']);
                        break;

                    default:
                        $t_enable_json_output = false;
                        trigger_error('unknown delete_type: '. $this->v_data_array['GET']['type'], E_USER_ERROR);
                }
                break;

            case 'run_autobuild':
                $t_output_array = $this->run_autobuild($this->v_data_array['POST']);
                break;

            case 'rebuild_properties_index':
                $t_output_array = $this->rebuild_properties_index($this->v_data_array['POST']);
                break;

            default:
                $t_enable_json_output = false;
                trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
                return false;
        }

        if($t_enable_json_output)
        {
            $coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$t_output_json = $coo_json->encode($t_output_array);

            $this->v_output_buffer = $t_output_json;
        }
        return true;
    }

    public function run_autobuild($p_data_array)
    {   
        $t_return = array();
        $t_return['action'] = 'run_autobuild';

        $c_properties_values_ids_array = $p_data_array['properties_values_ids_array'];
        if(!is_array($c_properties_values_ids_array)) trigger_error('run_autobuild: typeof($p_data_array["properties_values_ids_array"]) != array', E_USER_ERROR);

        $coo_control = MainFactory::create_object('PropertiesCombisAdminControl');

        # run combi auto_build
        $t_last_index = $coo_control->autobuild_combis($p_data_array['products_id'], $_SESSION['languages_id'], $c_properties_values_ids_array, $p_data_array['actual_index']);

        $t_combis_count = 1;

        // get combis count
        foreach($c_properties_values_ids_array AS $properties_key => $properties_values)
        {
            $t_combis_count = $t_combis_count * count($properties_values);
        }

        if($t_last_index != 0 && ($t_last_index < $t_combis_count)){
            $t_return['job'] = $this->languageTextManager->get_text("generating_combis");
			$t_return['progress_text'] = number_format(($t_last_index-1)  / $t_combis_count * 100, 1) . '%';
            $t_return['progress'] = number_format(($t_last_index-1)  / $t_combis_count * 100, 1);
            $t_return['combis_last_index'] = $t_last_index;
            $t_return['status'] = 'progress';
        }
        else
        {
			$t_return['job'] = $this->languageTextManager->get_text("rebuild_properties_index");
			$t_return['progress_text'] = '100%';
			$t_return['progress'] = 100;
            $t_return['status'] = 'success';
        }
        return $t_return;
    }

    public function rebuild_properties_index($p_data_array)
    {
        $c_products_id = $p_data_array['products_id'];
        if($c_products_id != (int)$c_products_id) trigger_error('rebuild_properties_index: typeof($p_data_array["products_id"]) != array', E_USER_ERROR);
        
        $t_return = array();

        $coo_properties_data_agent = MainFactory::create_object('PropertiesDataAgent');

        if($c_products_id == 0)
        {
            $t_return['action'] = 'rebuild_properties_index (all)';
            
            $t_start_value = (int)$p_data_array['start_value'];
            $count_products = 0;
            if($t_start_value == 0)
            {
                $t_sql = 'TRUNCATE products_properties_index';
                xtc_db_query($t_sql);

                $t_sql = '
                    SELECT COUNT(*)
                    FROM products_properties_combis
                    GROUP BY products_id
                ';
                $t_result = xtc_db_query($t_sql);
                $count_products = (int)xtc_db_fetch_array($t_result);
            }

            $t_sql = '
                SELECT products_id
                FROM products_properties_combis
                GROUP BY products_id
                LIMIT '.$t_start_value.', 5
            ';
            $t_result = xtc_db_query($t_sql);

            while($t_row = xtc_db_fetch_array($t_result))
            {            
                $coo_properties_data_agent->rebuild_properties_index($t_row['products_id']);
            }
            
            if($t_start_value+5 <= $count_products)
            {
                $t_return['status'] = 'progress'; 
                $t_return['products_next_index'] = $t_start_value+5; 
                $t_return['count_products'] = $count_products; 
            }
            else
            {
                $t_return['status'] = 'success';
            }
        }
        else
        {
            $t_return['action'] = 'rebuild_properties_index (products_id: ' + $c_products_id + ')';
            
            $t_sql = 'DELETE FROM products_properties_index WHERE products_id = '.$c_products_id;
            xtc_db_query($t_sql);

            $coo_properties_data_agent->rebuild_properties_index($c_products_id);
            
			$t_return['job'] = $this->languageTextManager->get_text("combis_generated");
			$t_return['progress_text'] = "100%";
			$t_return['progress'] = 100;
			$t_return['status'] = 'success';
        }
        return $t_return;
    }
}