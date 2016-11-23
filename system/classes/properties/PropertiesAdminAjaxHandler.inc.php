<?php
/* --------------------------------------------------------------
   PropertiesAdminAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class PropertiesAdminAjaxHandler extends AjaxHandler
{
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
        $t_output_array = array();
        $t_enable_json_output = true;

        $t_action_request = $this->v_data_array['GET']['action'];

        switch($t_action_request)
        {
            case 'load':
                $t_enable_json_output = false;
                $coo_properties_admin_view = MainFactory::create_object('PropertiesAdminContentView');
                $this->v_output_buffer = $coo_properties_admin_view->get_html($this->v_data_array['GET']);
                break;

            case 'save':
                $coo_properties_admin_control = MainFactory::create_object('PropertiesAdminControl');
				$coo_properties_admin_view = MainFactory::create_object('PropertiesAdminContentView');
                switch($this->v_data_array['GET']['type'])
                {
                    case 'properties':
                        $t_output_array['properties_id'] = $coo_properties_admin_control->save_properties($this->v_data_array['POST']);					
						$t_output_array['html'] = $coo_properties_admin_view->get_html(array("template" => "properties_table", "properties_id" => $t_output_array['properties_id']));
                        break;

                    case 'properties_values':
                        $t_output_array['properties_id'] = (int)$this->v_data_array['POST']['properties_id'];
                        $t_output_array['properties_values_id'] = $coo_properties_admin_control->save_properties_values($this->v_data_array['POST']);
						$t_output_array['html'] = $coo_properties_admin_view->get_html(array("template" => "properties_table", "properties_id" => (int)$this->v_data_array['POST']['properties_id']));
                        break;

                    default:
                        $t_enable_json_output = false;
                        trigger_error('unknown save_type: '. $this->v_data_array['GET']['type'], E_USER_ERROR);
                }
				
                break;

            case 'delete': 
                $coo_properties_admin_control = MainFactory::create_object('PropertiesAdminControl');
                switch($this->v_data_array['GET']['type'])
                {
                    case 'properties':
                        $t_output_array = $coo_properties_admin_control->delete_properties($this->v_data_array['GET']['properties_id']);
                        break;

                    case 'properties_values':
                        $t_output_array = $coo_properties_admin_control->delete_properties_values($this->v_data_array['GET']['properties_values_id']);
                        break;

                    default:
                        $t_enable_json_output = false;
                        trigger_error('unknown delete_type: '. $this->v_data_array['GET']['type'], E_USER_ERROR);
                }
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
}