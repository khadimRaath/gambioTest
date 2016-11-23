<?php
/* --------------------------------------------------------------
   PropertiesAdminContentView.inc.php 2015-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PropertiesAdminContentView extends LightboxContentView
{
    protected $v_properties_admin_control;
    
    function __construct()
    {
		parent::__construct();
        $this->v_properties_admin_control = MainFactory::create_object('PropertiesAdminControl');
        
        $this->set_template_dir(DIR_FS_CATALOG.'admin/html/content/properties/');
        
        $this->v_caching_enabled = false;
    }
    
    public function get_html_array( $p_get_array = array(), $p_post_array = array() )
    {
        if( !is_array( $p_get_array ) || count( $p_get_array ) == 0 ) trigger_error( 'PropertiesAdminContentView: $p_get_array is empty', E_USER_ERROR );
        $p_get_array['template'] = trim($p_get_array['template']);
        if( empty( $p_get_array['template'] ) ) trigger_error( 'PropertiesAdminContentView: $p_get_array["template"] is empty', E_USER_ERROR );
        
        switch($p_get_array['template'])
        {
            case 'properties_main':
                $t_html_output['html'] = $this->get_properties_main();
                break;
            case 'properties_table':
                $t_html_output['html'] = $this->get_properties_table($p_get_array['properties_id']);
                break;
            case 'properties_edit.html':                
            case 'properties_delete.html':
                $t_html_output['html'] = $this->get_properties_edit($p_get_array);
                break;
            case 'properties_values_edit.html':                
            case 'properties_values_delete.html':
                $t_html_output['html'] = $this->get_properties_values_edit($p_get_array);
                break;
            default:
                break;
        }
        
        return $t_html_output;
    }
    
    public function get_properties_main()
    {          
        $properties = $this->v_properties_admin_control->get_all_properties();
		        
        $this->set_content_data('properties_tables', $properties);
        $this->set_content_template('properties_main.html');
		
		$t_return = $this->build_html();
        
        return $t_return;
    }
    
    public function get_properties_table($p_properties_id)
    {
        $c_properties_id = (int)$p_properties_id;
        if(empty($c_properties_id)) trigger_error('get_properties_table: typeof($p_properties_id) != integer', E_USER_ERROR);
        
        $this->set_content_data('properties_table', $this->v_properties_admin_control->get_properties($c_properties_id));  
        $this->set_content_template('properties_table.html');
		
		$t_return = $this->build_html();
        
        return $t_return;
    }
    
    public function get_properties_edit($p_param)
    {        
        $c_properties_id = (int)$p_param['properties_id'];
        if(empty($c_properties_id)) $c_properties_id = 0;

        if($c_properties_id > 0)
        {
        # load properties data by optional given properties_id
            $t_properties = $this->v_properties_admin_control->get_properties($c_properties_id);
            $t_combis_count = $this->v_properties_admin_control->get_properties_in_combis_count($c_properties_id);
            $t_properties['properties_in_combis_count'] = $t_combis_count['combis_count'];
        }
        else
        {
        # no properties_id given. use defaults and empty array for empty fields
            $t_properties['properties_id'] = '';
            $t_properties['sort_order'] = '1';
            $languages_array = xtc_get_languages();
            for($i = 0, $total = count($languages_array); $i < $total; $i++)
            {
                $t_properties['properties_names'][$languages_array[$i]['id']] = $languages_array[$i];
                $t_properties['properties_names'][$languages_array[$i]['id']]['properties_name'] = '';
                $t_properties['properties_names'][$languages_array[$i]['id']]['properties_admin_name'] = '';
            }
            $t_properties['properties_in_combis_count'] = 0;
        }
		
        $this->set_content_data("properties", $t_properties);
		
		$this->set_lightbox_button('left', 'cancel', array('close', 'lightbox_close'));
		
		if( $p_param['template'] == 'properties_edit.html' )
		{
			$this->set_lightbox_button('right', 'save', array('save', 'green'));
			$this->set_lightbox_button('right', 'save_close', array('save_close', 'green'));
		}
		else if($p_param['template'] == 'properties_delete.html')
		{
			$this->set_lightbox_button('right', 'delete', array('delete', 'red'));
		}
		
		$t_return = $this->build_html();
        
        return $t_return;
    }
    
    public function get_properties_values_edit($p_param)
    {        
        $c_properties_id = (int)$p_param['properties_id'];
        if(empty($c_properties_id)) trigger_error('get_properties_values_edit: typeof($p_param["properties_id"]) != integer', E_USER_ERROR);
        
        $c_properties_values_id = (int)$p_param['properties_values_id'];
        if(empty($c_properties_values_id)) $c_properties_values_id = 0;

        if($c_properties_values_id > 0)
        {
        # load properties data by optional given properties_id
            $t_properties_values = $this->v_properties_admin_control->get_properties_values_by_properties_values_id($c_properties_values_id);
            $t_combis_count = $this->v_properties_admin_control->get_properties_values_in_combis_count($c_properties_values_id);
            $t_properties_values['properties_values_in_combis_count'] = $t_combis_count['combis_count'];
        }
        else
        {
        # no properties_id given. use defaults and empty array for empty fields
            $t_properties_values['properties_id'] = $c_properties_id;
            $t_properties_values['properties_values_id'] = '';
            $t_properties_values['sort_order'] = '';
            $t_properties_values['value_price'] = '0.00';
            $languages_array = xtc_get_languages();
            for($i = 0, $total = count($languages_array); $i < $total; $i++)
            {
                $t_properties_values['values_names'][$languages_array[$i]['id']] = $languages_array[$i];
                $t_properties_values['values_names'][$languages_array[$i]['id']]['values_name'] = '';
            }
            $t_properties_values['properties_values_in_combis_count'] = 0;
        }

        $this->set_content_data("properties_values", $t_properties_values);
		
		$this->set_lightbox_button('left', 'cancel', array('close', 'lightbox_close'));
		
		if( $p_param['template'] == 'properties_values_edit.html' )
		{
			$this->set_lightbox_button('right', 'save', array('save', 'green'));
			$this->set_lightbox_button('right', 'save_close', array('save_close', 'green'));
		}
		else if($p_param['template'] == 'properties_values_delete.html')
		{
			$this->set_lightbox_button('right', 'delete', array('delete', 'red'));
		}
		
		$t_return = $this->build_html();
        
        return $t_return;
    }
}