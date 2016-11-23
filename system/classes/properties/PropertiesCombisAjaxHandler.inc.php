<?php
/* --------------------------------------------------------------
   PropertiesCombisAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class PropertiesCombisAjaxHandler extends AjaxHandler
{
    function get_permission_status($p_customers_id=NULL)
    {
        return true;
    }

    function proceed()
    {
        $t_output_array = array();
        $t_enable_json_output = true;

        $t_action_request = $this->v_data_array['GET']['action'];
		
		$coo_properties_control = MainFactory::create_object('PropertiesControl');

        switch($t_action_request)
        {
            case 'get_selection_template':
                if(isset($this->v_data_array['POST']['products_id']))
                {
                    $c_products_id = (int)$this->v_data_array['POST']['products_id'];
                }
                else
                {
                    $c_products_id = (int)$this->v_data_array['GET']['products_id'];
                }
				
				if(isset($this->v_data_array['POST']['properties_values']) == false 
                   && isset($this->v_data_array['POST']['properties_values_ids']) == false)
				{
					trigger_error('properties_values not found: PropertiesCombisAjaxHandler->get_selection_template');
				}
				$t_properties_values = $this->_getPropertiesValuesString();
				$c_quantity = (int)$this->v_data_array['POST']['quantity'];
				$t_initial = ($this->v_data_array['POST']['initial'] === 'true') ? true : false;
				
				$t_output_array = $coo_properties_control->get_selection_data($c_products_id, $_SESSION['languages_id'], $c_quantity, $t_properties_values, $_SESSION['currency'], $_SESSION['customers_status']['customers_status_id'], $t_initial);				
                break;
				
			case 'check_quantity':
                if(isset($this->v_data_array['POST']['products_id']))
                {
                    $c_products_id = (int)$this->v_data_array['POST']['products_id'];
                }
                else
                {
                    $c_products_id = (int)$this->v_data_array['GET']['products_id'];
                }
                
				if(isset($this->v_data_array['POST']['properties_values']) == false
                   && isset($this->v_data_array['POST']['properties_values_ids']) == false)
				{
					trigger_error('properties_values not found: PropertiesCombisAjaxHandler->get_selection_template');
				}
				$t_properties_values = $this->_getPropertiesValuesString();
				$c_quantity = (int)$this->v_data_array['POST']['quantity'];
				
				$t_output_array = $coo_properties_control->check_combis_quantity($c_products_id, $_SESSION['languages_id'], $c_quantity, $t_properties_values);				
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


    /**
     * @return string
     */
    protected function _getPropertiesValuesString()
    {
        if(isset($this->v_data_array['POST']['properties_values']))
        {
            $propertiesValues = $this->v_data_array['POST']['properties_values'];
        }
        else
        {
            $propertiesValuesArray = array();

            foreach($this->v_data_array['POST']['properties_values_ids'] as $key => $property_value_id)
            {
                $propertiesValuesArray[] = $key . ':' . $property_value_id;
            }

            $propertiesValues = implode('&', $propertiesValuesArray);
        }
        
        return $propertiesValues;
    }
}