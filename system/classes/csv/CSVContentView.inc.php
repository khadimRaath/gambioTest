<?php
/* --------------------------------------------------------------
   CSVContentView.inc.php 2015-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

class CSVContentView extends LightboxContentView
{
    protected $v_get_array = array();
    protected $v_post_array = array();
	protected $v_preview_data_length = 5;
	
	public function __construct()
    {
		parent::__construct();
		$this->set_template_dir( DIR_FS_CATALOG.'admin/html/content/export/' );
    }

    public function get_html_array( $p_get_array = array(), $p_post_array = array() )
    {
		$this->v_get_array = $p_get_array;
		$this->v_post_array = $p_post_array;

		$t_html_output = array();

		$c_template = (string)$this->v_get_array['template'];
		if( preg_match( '/[^\w\.\-]/', $c_template ) )
		{
			trigger_error( 'get_html: unexpected characters in template_name', E_USER_ERROR );
			return false;
		}

		$this->set_content_template( $c_template );

		switch( $c_template )
		{
			case 'export_overview.html': 
				$t_html_output[ 'html' ] = $this->get_overview();
				break;
			case 'export_scheme_overview.html': 
				$t_html_output[ 'html' ] = $this->get_scheme_overview();
				break;
			case 'export_scheme_details.html': 
				$this->set_lightbox_mode( false );
				$this->set_content_template( 'export_scheme_configuration.html' );
				$t_html_configuration = $this->get_configuration();
				
				$this->set_lightbox_mode( true );
				$this->set_content_template( 'export_scheme_details.html' );
				$t_html_output[ 'html' ] = $this->get_details( $t_html_configuration );
				break;
			case 'export_scheme_configuration.html':
				$t_html_output[ 'html' ] = $this->get_configuration();
				break;
			case 'export_scheme_collective_fields.html':
				$t_html_output[ 'html' ] = $this->get_collective_fields();
				break;
			case 'export_categories.html':
				$t_html_output[ 'html' ] = $this->get_categories();
				break;
			case 'export_child_categories.html':
				$t_html_output[ 'html' ] = $this->get_child_categories();
				break;
			case 'export_scheme_fields.html':
				$t_html_output[ 'html' ] =  $this->get_fields();
				break;
			case 'export_scheme_preview.html':
				$t_html_output[ 'html' ] = $this->get_preview();
				break;
			default: trigger_error( 'get_html: no template selected', E_USER_WARNING );
		}
		
		return $t_html_output;
    }
	
	protected function get_overview()
	{
		$coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
		$t_export_types = $coo_csv_control->get_export_types();
		
		foreach( $t_export_types AS $t_type_id => $t_type )
		{
			$this->set_content_template( 'export_scheme_overview.html' );
			$this->v_get_array[ 'export_type' ] = $t_type_id;
			$this->v_get_array[ 'export_type_name' ] = $t_type[ 'name' ];
			$t_export_types[ $t_type_id ][ 'content' ] = $this->get_scheme_overview();
		}
		$t_export_types[ 999 ][ 'name' ] = 'Import';
		$this->set_content_template( 'export_import.html' );
		$this->set_content_data( 'import_file_data', $coo_csv_control->get_import_files_array() );
		$t_export_types[ 999 ][ 'content' ] = $this->get_import();
		
		$this->set_content_template( 'export_overview.html' );
		
		$this->set_content_data( 'export_tabs' , $t_export_types );
		
		$t_html = $this->build_html();
		return $t_html;
	}

    protected function get_scheme_overview()
    {
		$t_export_type = (int)$this->v_get_array[ 'export_type' ];
		
		$coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
		$t_show_wrapper = true;
		if( isset( $this->v_get_array[ 'scheme_id' ] ) && (int)$this->v_get_array[ 'scheme_id' ] > 0 )
		{
			$coo_scheme = $coo_csv_control->get_scheme((int)$this->v_get_array[ 'scheme_id' ]);
			$t_csv_schemes[ (int)$this->v_get_array[ 'scheme_id' ] ] = $coo_scheme;
			$t_show_wrapper = false;
		}
		else
		{
			$t_csv_schemes = $coo_csv_control->get_schemes_by_type( $t_export_type );
		}
		
		$t_cronjob_status_array = $coo_csv_control->get_cronjob_status_array();
		
		// format date
		$t_date_format = 'Y-m-d H:i';
		
		if( $_SESSION[ 'languages_id' ] == 2 )
		{
			$t_date_format = 'd.m.Y H:i';
		}
		$t_cronjob_exists = false;
		foreach( $t_csv_schemes AS $t_scheme )
		{
			$t_unformatted_date = $t_scheme->v_data_array[ 'date_last_export' ];
			if( $t_unformatted_date != '1000-01-01 00:00:00' )
			{
				$t_scheme->v_data_array[ 'date_last_export' ] = date( $t_date_format, strtotime( $t_scheme->v_data_array[ 'date_last_export' ] ) );
			}
			else
			{
				$t_scheme->v_data_array[ 'date_last_export' ] = '-';
			}
			$t_scheme->v_data_array[ 'file_exists' ] = "false";
			if( file_exists( DIR_FS_CATALOG . 'export/' . basename( $t_scheme->v_data_array[ 'filename' ] ) ) )
			{
				$t_scheme->v_data_array[ 'file_exists' ] = "true";
			}
			
			$t_scheme->v_data_array[ 'cronjob_status' ] = $t_cronjob_status_array[ $t_scheme->v_scheme_id ][ 'status' ];
			$t_scheme->v_data_array[ 'cronjob_active' ] = $t_cronjob_status_array[ $t_scheme->v_scheme_id ][ 'active' ];
			$t_scheme->v_data_array[ 'cronjob_message' ] = $t_cronjob_status_array[ $t_scheme->v_scheme_id ][ 'message' ];
			
			if( $t_cronjob_status_array[ $t_scheme->v_scheme_id ][ 'status' ] != 'no_cronjob' )
			{
				$t_cronjob_exists = true;
			}
		}
		
		$t_token = $coo_csv_control->get_secure_token();
		
		$t_export_active = true;
		if( $coo_csv_control->cronjob_stopped() )
		{
			$t_export_active = false;
		}
		
		$t_export_paused = false;
		if( $coo_csv_control->cronjob_paused() )
		{
			$t_export_paused = true;
		}
		
		$this->set_content_data( 'token', $t_token );
		$this->set_content_data( 'type_id', $t_export_type );
		$this->set_content_data( 'schemes' , $t_csv_schemes );	
		$this->set_content_data( 'show_wrapper' , $t_show_wrapper );	
		$this->set_content_data( 'export_active' , $t_export_active );	
		$this->set_content_data( 'export_paused' , $t_export_paused );		
		$this->set_content_data( 'cronjob_exists' , $t_cronjob_exists );		
		$this->set_content_data( 'cronjob_url' , HTTP_SERVER . DIR_WS_CATALOG . 'request_port.php?module=CSV&amp;action=export&amp;token=' . $t_token );
		
		$t_html = $this->build_html();

        return $t_html;
    }

    protected function get_details( $p_html_output )
    {		
		$c_scheme_id = (int)$this->v_get_array['scheme_id'];
		
		$this->content_array = array();
		$this->set_content_data( 'content', $p_html_output );

        $coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
		$coo_scheme = $coo_csv_control->get_scheme($c_scheme_id);
		$this->set_content_data( 'scheme', $coo_scheme );
		$this->set_content_data( 'export_type', (int)$this->v_get_array['export_type'] );
		
		$this->set_lightbox_button( 'right', 'save', array( 'save', 'green' ));
		$this->set_lightbox_button( 'right', 'close', array( 'close' ));

		$t_html = $this->build_html();

        return $t_html;
    }

    protected function get_configuration()
    {
		$c_scheme_id = (int)$this->v_get_array['scheme_id'];
		
		$t_deactivate_cronjob_type = false;
		
        $coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
        $t_csv_scheme = $coo_csv_control->get_scheme( $c_scheme_id );	
		$t_csv_scheme->v_data_array[ 'shipping_free_minimum' ] = (double)$t_csv_scheme->v_data_array[ 'shipping_free_minimum' ];
		$t_csv_scheme->v_data_array[ 'quantity_minimum' ] = (double)$t_csv_scheme->v_data_array[ 'quantity_minimum' ];
		$t_export_type = (int)$t_csv_scheme->v_data_array[ 'type_id' ];
		
		if( $c_scheme_id == 0 )
		{
			$t_csv_scheme->v_data_array[ 'shipping_free_minimum' ] = "0";
			$t_csv_scheme->v_data_array[ 'quantity_minimum' ] = "0";
			$t_export_type = (int)$this->v_get_array['export_type'];
		}
		
		if( count($t_csv_scheme->v_fields_array) > 0 )
		{
			$t_deactivate_cronjob_type = true;
		}
		
		// get all customers status
		$t_customers_status_array = array();
		$t_customers_status_result = xtc_db_query('SELECT customers_status_id, customers_status_name FROM customers_status WHERE language_id = "' . $_SESSION['languages_id'] . '"');
		while ( $t_row = xtc_db_fetch_array( $t_customers_status_result ) ) 
		{
			$t_customers_status_array[$t_row['customers_status_id']]['customers_status_id'] = $t_row['customers_status_id'];
			$t_customers_status_array[$t_row['customers_status_id']]['customers_status_name'] = $t_row['customers_status_name'];
		}
		
		// get all currencies
		$t_currencies_array = array();
		$t_currencies_result = xtc_db_query('SELECT currencies_id, code FROM currencies');
		while ( $t_row = xtc_db_fetch_array( $t_currencies_result ) ) 
		{
			$t_currencies_array[$t_row['currencies_id']]['currencies_id'] = $t_row['currencies_id'];
			$t_currencies_array[$t_row['currencies_id']]['code'] = $t_row['code'];
		}

		// get all campaigns
		$t_campaigns_array = array();
		$t_campaigns_result = xtc_db_query('SELECT campaigns_id, campaigns_refID, campaigns_name FROM campaigns');
		while ( $t_row = xtc_db_fetch_array( $t_campaigns_result ) ) 
		{
			$t_campaigns_array[$t_row['campaigns_id']]['campaigns_id'] = $t_row['campaigns_id'];
			$t_campaigns_array[$t_row['campaigns_id']]['campaigns_refID'] = $t_row['campaigns_refID'];
			$t_campaigns_array[$t_row['campaigns_id']]['campaigns_name'] = $t_row['campaigns_name'];
		}
		
		// get all languages
		$t_languages_array = array();
		$t_languages_result = xtc_db_query("SELECT languages_id, name, code, image, directory FROM " . TABLE_LANGUAGES . " ORDER BY sort_order");
		while ($t_row = xtc_db_fetch_array($t_languages_result)) 
		{
			$t_languages_array[] = array ('id' => $t_row['languages_id'], 'name' => $t_row['name'], 'code' => $t_row['code'], 'image' => $t_row['image'], 'directory' => $t_row['directory']);
		}
		
		// get all export types
		$t_export_types_array = array();
		$t_export_types_result = xtc_db_query('SELECT type_id, name FROM export_types WHERE language_id = "' . $_SESSION['languages_id'] . '"');
		while ( $t_row = xtc_db_fetch_array($t_export_types_result))
		{
			$t_export_types_array[$t_row['type_id']]['type_id'] = $t_row['type_id'];
			$t_export_types_array[$t_row['type_id']]['name'] = $t_row['name'];
		}
	    
	    // get export file path
	    $t_export_file_path = HTTP_SERVER . DIR_WS_CATALOG .'export/' . $t_csv_scheme->v_data_array['filename'];
		
		$this->set_content_data( 'export_type' , $t_export_type );
		$this->set_content_data( 'customers_status' , $t_customers_status_array );
		$this->set_content_data( 'currencies' , $t_currencies_array );
		$this->set_content_data( 'languages' , $t_languages_array );
		$this->set_content_data( 'campaigns' , $t_campaigns_array );
		$this->set_content_data( 'scheme' , $t_csv_scheme );
		$this->set_content_data( 'scheme_types' , $t_export_types_array );
		$this->set_content_data( 'cronjob_days_array' , explode( "|", $t_csv_scheme->v_data_array['cronjob_days'] ) );
		$this->set_content_data( 'deactivate_cronjob_type' , $t_deactivate_cronjob_type );
	    $this->set_content_data( 'export_file_path' , $t_export_file_path );
		
        $t_html = $this->build_html();

        return $t_html;
    }

	protected function get_collective_fields()
	{
		$c_scheme_id = (int)$this->v_get_array['scheme_id'];

		$coo_csv_control = MainFactory::create_object('CSVControl', array(), true);
		$coo_scheme = $coo_csv_control->get_scheme($c_scheme_id);
		$t_collective_fields_array = $coo_csv_control->get_collective_fields($c_scheme_id);

		$this->set_content_data('scheme', $coo_scheme);
		$this->set_content_data('collective_fields' , $t_collective_fields_array);

		$t_html = $this->build_html();

		return $t_html;
	}

	protected function get_categories()
	{
		$c_scheme_id = (int)$this->v_get_array['scheme_id'];
		
        $coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
        $t_csv_scheme = $coo_csv_control->get_scheme( $c_scheme_id );
		//TODO: letzten Parameter aus dem Scheme auslesen
		$t_categories = $coo_csv_control->get_child_categories($this->v_get_array['scheme_id'], -1, true);
		
		$this->set_content_data( 'scheme' , $t_csv_scheme );
		$this->set_content_data( 'categories' , $t_categories);
		
		$t_html = $this->build_html();

        return $t_html;
	}
	
	protected function get_child_categories()
	{
		$c_scheme_id = (int)$this->v_get_array['scheme_id'];
		
        $coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
        $t_csv_scheme = $coo_csv_control->get_scheme( $c_scheme_id );
		//TODO: letzten Parameter aus dem Scheme auslesen
		$t_categories = $coo_csv_control->get_child_categories($this->v_get_array['scheme_id'], $this->v_get_array['categories_id'], true);
		
		$this->set_content_data( 'scheme' , $t_csv_scheme );
		$this->set_content_data( 'categories' , $t_categories);
		
		$t_html = $this->build_html();

        return $t_html;
	}

    protected function get_fields()
    {
		$c_scheme_id = (int)$this->v_get_array['scheme_id'];
		if( $c_scheme_id == 0 ) trigger_error('get_fields: scheme_id = 0', E_USER_ERROR);
		
		$c_field_id = false;
		if( isset( $this->v_get_array['field_id'] ) )
		{
			$c_field_id = (int)$this->v_get_array['field_id'];
		}
		
		
		$coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
        $t_csv_scheme = $coo_csv_control->get_scheme( $c_scheme_id );
		$t_csv_scheme->load_fields();
		
		$t_languages = xtc_get_languages();
        
		$t_csv_variables = $coo_csv_control->get_variables_array($t_csv_scheme->v_data_array['type_id']);
		
		if( $t_csv_scheme->v_data_array['export_properties'] == 1 )
		{
			$t_scheme_properties_array = $t_csv_scheme->get_properties_array();
			$t_scheme_properties_string = implode(',', $t_scheme_properties_array);
			$t_shop_properties = $coo_csv_control->get_properties_array(false);
			$t_shop_properties_columns = array( 'products_properties_combis_id', 
											'combi_sort_order', 
											'combi_model', 
											'combi_ean',
											'combi_quantity', 
											'combi_shipping_status_id', 
											'combi_weight', 
											'combi_price', 
											'combi_price_type', 
											'combi_image', 
											'combi_vpe_id', 
											'combi_vpe_value' );
			
			$t_properties_language = $t_csv_scheme->v_data_array['languages_id'];
			if( $t_properties_language == 0 )
			{
				$t_properties_language = $t_languages[0]['id'];
			}
			
			$this->set_content_data( 'properties', $t_shop_properties );
			$this->set_content_data( 'scheme_properties_string', $t_scheme_properties_string );
			$this->set_content_data( 'scheme_properties', $t_scheme_properties_array );
			$this->set_content_data( 'properties_columns', $t_shop_properties_columns );
			$this->set_content_data( 'properties_language', $t_properties_language );
		}
		
		$t_show_wrapper = true;
		$t_show_edit_mode = false;
		
		if( $c_field_id !== false )
		{
			$t_show_wrapper = false;
			if( $c_field_id == 0 )
			{
				$t_show_edit_mode = true;
				$coo_field_model = MainFactory::create_object( 'CSVFieldModel' );
				$coo_field_model->v_field_id = 0;
				$t_fields_array[] = $coo_field_model;
			}
			else
			{
				$t_fields_array[ $c_field_id ] = $t_csv_scheme->v_fields_array[ $c_field_id ];
			}			
		}
		else
		{
			$t_fields_array = $t_csv_scheme->v_fields_array;
		}
		
		$this->set_content_data( 'scheme', $t_csv_scheme );
		$this->set_content_data( 'fields_array', $t_fields_array );
		$this->set_content_data( 'field_sort_string', implode( "_", array_keys( $t_fields_array ) ) );
		$this->set_content_data( 'show_wrapper', $t_show_wrapper );
		$this->set_content_data( 'show_edit_mode', $t_show_edit_mode );
		$this->set_content_data( 'variables', $t_csv_variables );
		$this->set_content_data( 'properties_languages', $t_languages );
		
		$t_html = $this->build_html();

        return $t_html;
    }

    protected function get_field()
    {
		$c_scheme_id = (int)$this->v_get_array['scheme_id'];
		if( $c_scheme_id == 0 ) trigger_error('get_field: scheme_id = 0', E_USER_ERROR);
		
		$c_field_id = (int)$this->v_get_array['field_id'];
			
		$coo_field = MainFactory::create_object( 'CSVFieldModel', array( $c_field_id ) );
		
		$this->set_content_data( 'field', $coo_field );
		$this->set_content_data( 'scheme_id', $c_scheme_id );
				
		$t_html = $this->build_html();
		
        return $t_html;
    }

    protected function get_preview()
    {
		$t_field_data_array = array();
		
		$c_scheme_id = (int)$this->v_get_array['scheme_id'];
		if( $c_scheme_id == 0 ) trigger_error('get_preview: scheme_id = 0', E_USER_ERROR);		
		
		$c_edit_field_index = (int)$this->v_post_array['edit_field_index'];
		
		parse_str_wrapper($this->v_post_array['field_data'], $t_content_data);
		
		$t_count_fields = count($t_content_data[ 'field_name' ]);						
		for( $i = 0; $i < $t_count_fields; $i++ )
		{
			$t_field_data_array[$i]['field_id'] = ($t_content_data['field_id'][$i] == -1) ? 0 : $t_content_data['field_id'][$i];
			$t_field_data_array[$i]['scheme_id'] = $c_scheme_id;
			$t_field_data_array[$i]['field_name'] = $t_content_data['field_name'][$i];
			$t_field_data_array[$i]['field_content'] = $t_content_data['field_content'][$i];
			$t_field_data_array[$i]['field_content_default'] = $t_content_data['field_content_default'][$i];
			$t_field_data_array[$i]['sort_order'] = $i;
			
			if($i == $c_edit_field_index && $t_content_data['edit_field_name'] != '')
			{
				$t_field_data_array[$i]['field_name'] = $t_content_data['edit_field_name'];
				$t_field_data_array[$i]['field_content'] = $t_content_data['edit_field_content'];
				$t_field_data_array[$i]['field_content_default'] = $t_content_data['edit_field_content_default'];
			}
		}
		
		if($t_count_fields == $c_edit_field_index && $t_content_data['edit_field_name'] != '')
		{
			$t_field_data_array[$c_edit_field_index]['field_id'] = 0;
			$t_field_data_array[$c_edit_field_index]['scheme_id'] = $c_scheme_id;
			$t_field_data_array[$c_edit_field_index]['field_name'] = $t_content_data['edit_field_name'];
			$t_field_data_array[$c_edit_field_index]['field_content'] = $t_content_data['edit_field_content'];
			$t_field_data_array[$c_edit_field_index]['field_content_default'] = $t_content_data['edit_field_content_default'];
			$t_field_data_array[$c_edit_field_index]['sort_order'] = $c_edit_field_index;
		}		
		
		if( isset( $t_content_data['field_properties_select'] ) || isset( $t_content_data['field_properties_data_select'] ) )
		{			
			$coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
			$coo_scheme = $coo_csv_control->get_scheme( $c_scheme_id );
			$coo_scheme->v_data_array[ 'languages_id' ] = (int)$t_content_data[ 'select_properties_language' ];					
			$coo_csv_control->get_properties_array(false);
			
			if( $t_content_data['field_properties_select'] != '' && !is_array( $t_content_data['field_properties_select'] ) )
			{
				$t_content_data['field_properties_select'] = array( $t_content_data['field_properties_select'] );
			}
			
			if( $t_content_data['field_properties_data_select'] != '' && !is_array( $t_content_data['field_properties_data_select'] ) )
			{
				$t_content_data['field_properties_data_select'] = array( $t_content_data['field_properties_data_select'] );
			}
			
			if( is_array( $t_content_data['field_properties_select'] ) && is_array( $t_content_data['field_properties_data_select'] ) )
			{
				$c_properties_array = array_merge( $t_content_data['field_properties_select'], $t_content_data['field_properties_data_select'] );
			}
			else if( is_array( $t_content_data['field_properties_select'] ) )
			{
				$c_properties_array = $t_content_data['field_properties_select'];
			}
			else if( is_array( $t_content_data['field_properties_data_select'] ) )
			{
				$c_properties_array = $t_content_data['field_properties_data_select'];
			}
		}
		$coo_csv_control = MainFactory::create_object( 'CSVControl', array(), true );
		$t_export_data = $coo_csv_control->export( $c_scheme_id, $this->v_preview_data_length, $t_field_data_array, $c_properties_array );

		if( count( $t_export_data ) > 0 )
		{
			$t_headline_array = array_keys( $t_export_data[0] );
			$this->set_content_data( 'headline_data', $t_headline_array );
			$this->set_content_data( 'export_data', $t_export_data );
		}
		
        $t_html = $this->build_html();

        return $t_html;
    }

    protected function get_import()
    {
		$t_html = $this->build_html();
        return $t_html;
    }
}