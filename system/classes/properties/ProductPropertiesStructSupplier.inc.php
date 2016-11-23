<?php
/* --------------------------------------------------------------
   ProductPropertiesStructSupplier.inc.php 2015-01-13 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ProductPropertiesStructSupplier
{
    protected $v_combis_array = array();
    protected $v_properties_array = array();
    protected $v_max_get_values = 1000;
    
    public function __construct() {}
  
    public function get_all_combis($p_products_id, $p_language_id, $p_combis_values_type = 'no', $p_offset = 0, $p_limit = 300)
    {
        $t_use_limit = true;
        
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_all_combis(): typeof($p_products_id) != integer', E_USER_ERROR);

        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) trigger_error('get_all_combis(): typeof($p_language_id) != integer', E_USER_ERROR);

        $c_offset = (int)$p_offset;

        $c_limit = (int)$p_limit;
        if(empty($c_limit)) $t_use_limit = false;

        // allowed values: full, ids, no
        $c_combis_values_type = (string)$p_combis_values_type;
        if($c_combis_values_type != 'full' && 
           $c_combis_values_type != 'ids')
        {
            $c_combis_values_type = 'no';
        }
        
        // reset combis_array
        $this->v_combis_array = array();
        
        // get products_tax_class_id
        $coo_product = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $c_products_id)));
        $t_products_tax_class_id = $coo_product->get_data_value('products_tax_class_id');
        
        $coo_xtc_price = new xtcPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);
        
        $t_sql = '
            SELECT
                products_properties_combis_id,
                sort_order,
                combi_model,
                combi_quantity,
                combi_ean,
                combi_weight,
                combi_price_type,
                combi_price,
                combi_image,
                combi_shipping_status_id,
                products_vpe_id,
                vpe_value
            FROM
                products_properties_combis
            WHERE
                products_id = "'.$c_products_id.'"
            ORDER BY
                sort_order
        ';
       
        if($t_use_limit)
        {
            $t_sql .= '
                LIMIT '.$c_offset.', '.$c_limit;
        }
        
        $t_result = xtc_db_query($t_sql);

        while($t_row = xtc_db_fetch_array($t_result))
        {
			$t_row['combi_quantity'] = (double)$t_row['combi_quantity'];
			
            if(PRICE_IS_BRUTTO == 'true')
            {
                $t_row['combi_price'] = $coo_xtc_price->xtcAddTax($t_row['combi_price'], $coo_xtc_price->TAX[$t_products_tax_class_id]);
            }
            $this->v_combis_array[$t_row['products_properties_combis_id']] = $t_row;
            $this->v_combis_array[$t_row['products_properties_combis_id']]['combi_price_formatted'] = $coo_xtc_price->xtcFormat($t_row['combi_price'], true);
			}
        
        if(count($this->v_combis_array) > 0){
            $this->get_combis_values($c_language_id);
        }
        
        return $this->v_combis_array;
    }
    
    public function get_combis($p_products_id, $p_combis_id, $p_language_id, $p_combis_values_type = 'no')
    {
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_combis(): typeof($p_products_id) != integer', E_USER_ERROR);
		
        $c_combis_id = (int)$p_combis_id;
        if(empty($c_combis_id)) trigger_error('get_combis(): typeof($p_combis_id) != integer', E_USER_ERROR);

        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) trigger_error('get_combis(): typeof($p_language_id) != integer', E_USER_ERROR);
        
        // allowed values: full, ids, no
        $c_combis_values_type = (string)$p_combis_values_type;
        if($c_combis_values_type != 'full' && 
           $c_combis_values_type != 'ids')
        {
            $c_combis_values_type = 'no';
        }
        
        // reset combis_array
        $this->v_combis_array = array();
        
        // get products_tax_class_id
        $coo_product = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $c_products_id)));
        $t_products_tax_class_id = $coo_product->get_data_value('products_tax_class_id');
        
        $coo_xtc_price = new xtcPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);
        
        $t_sql = '
            SELECT
                products_properties_combis_id,
                sort_order,
                combi_model,
                combi_quantity,
                combi_ean,
                combi_weight,
                combi_price_type,
                combi_price,
                combi_image,
                combi_shipping_status_id,
                products_vpe_id,
                vpe_value
            FROM
                products_properties_combis
            WHERE
                products_properties_combis_id = "'.$c_combis_id.'"
        ';
        
        $t_result = xtc_db_query($t_sql);
        
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $this->v_combis_array[$t_row['products_properties_combis_id']] = $t_row;
            if(PRICE_IS_BRUTTO == 'true')
            {
                $t_combi_price_total = $coo_xtc_price->xtcAddTax($t_row['combi_price'], $coo_xtc_price->TAX[$t_products_tax_class_id]);
            }
            else
			{
                $t_combi_price_total = $t_row['combi_price'];
			}
            $this->v_combis_array[$t_row['products_properties_combis_id']]['combi_price_total'] = $t_combi_price_total;
            $this->v_combis_array[$t_row['products_properties_combis_id']]['combi_price_formatted'] = $coo_xtc_price->xtcFormat($t_combi_price_total, true);
        }
        
        if(count($this->v_combis_array) > 0){
            $this->get_combis_values($c_language_id);
        }
        
        return $this->v_combis_array[$c_combis_id];
    }
    
    protected function get_combis_values($p_language_id)
    {
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) trigger_error('get_combis_values(): typeof($p_language_id) != integer', E_USER_ERROR);
        
        $t_array_length = count($this->v_combis_array);
        
        for($i = 1, $total = ceil($t_array_length / $this->v_max_get_values); $i <= $total; $i++)
        {
            if($i * $this->v_max_get_values > $t_array_length)
            {
                $t_limit = $t_array_length - (($i - 1) * $this->v_max_get_values);
            }
            else
            {
                $t_limit = $this->v_max_get_values;
            }
            $t_tmp_array = array_slice($this->v_combis_array, ($i - 1) * $this->v_max_get_values, $t_limit, true);
            
            $t_sql = '
                SELECT 
                    products_properties_combis_id,
                    properties_values_id,
                    properties_name,        
                    properties_admin_name,        
                    values_name     
                FROM 
                    products_properties_index
                WHERE
                    products_properties_combis_id IN ('.implode(',', array_keys($t_tmp_array)).') AND
                    language_id = "'.$c_language_id.'"
                ORDER BY
                    properties_sort_order,
                    properties_id,
                    value_sort_order,
                    properties_values_id
            ';

            $t_result = xtc_db_query($t_sql);

            # init properties_values and group in properties
            while($t_row = xtc_db_fetch_array($t_result))
            {
                $this->v_combis_array[$t_row['products_properties_combis_id']]['combis_values'][$t_row['properties_values_id']] = $t_row;
            }
        }
    }
    
    public function get_all_properties()
    {
        // reset properties array
        $this->v_properties_array = array();
        
        $t_sql = '
            SELECT
                properties_id,
                sort_order
            FROM 
                properties
            ORDER BY
                sort_order,
                properties_id
        ';
        $t_result = xtc_db_query($t_sql);

        # init properties_names
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $this->v_properties_array[$t_row['properties_id']] = $t_row;
        }
        
        if(count($this->v_properties_array) > 0){
            $this->get_properties_names();
            $this->get_properties_values();
        }
        
        return $this->v_properties_array;
    }
    
    public function get_all_properties_by_products_id($p_products_id)
    {
        $c_products_id = (int)$p_products_id;
        if(empty($c_products_id)) trigger_error('get_all_properties_by_products_id(): typeof($p_products_id) != integer', E_USER_ERROR);
        
        // reset properties array
        $this->v_properties_array = array();

        $t_sql = '
            SELECT
                properties_id,
                properties_sort_order
            FROM 
                products_properties_index
            WHERE
                products_id = "'.$c_products_id.'"
            GROUP BY 
                properties_id
            ORDER BY
                properties_sort_order,
                properties_id
        ';
        $t_result = xtc_db_query($t_sql);

        # init properties_names
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $this->v_properties_array[$t_row['properties_id']] = $t_row;
        }
        
        if(count($this->v_properties_array) > 0){
            $this->get_properties_names();
            $this->get_properties_values($c_products_id);
        }  
        
        return $this->v_properties_array;
    }
    
    public function get_properties($p_properties_id)
    {
        $c_properties_id = (int)$p_properties_id;
        if(empty($c_properties_id)) trigger_error('get_properties(): typeof($p_properties_id) != integer', E_USER_ERROR);
        
        // reset properties array
        $this->v_properties_array = array();
        
        $t_sql = '
        SELECT
            p.properties_id AS properties_id,
            p.sort_order AS sort_order
        FROM 
            properties AS p
            LEFT JOIN properties_description AS pd USING (properties_id)
        WHERE
            properties_id = "'.$c_properties_id.'"
        GROUP BY
            p.properties_id
        ';
        $t_result = xtc_db_query($t_sql);

        # init properties_names
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $this->v_properties_array[$t_row['properties_id']] = $t_row;
        }
        
        if(count($this->v_properties_array) > 0){
            $this->get_properties_names();
            $this->get_properties_values();
        } 
        
        return $this->v_properties_array[$c_properties_id];
    }
    
    public function get_properties_values($p_products_id = 0)
    {
		$t_products_tax_class_id = 0;
		
		$c_products_id = (int)$p_products_id;
		if(!empty($c_products_id) && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
		{
			// get products_tax_class_id
			$coo_product = MainFactory::create_object('GMDataObject', array('products', array('products_id' => $c_products_id)));
			$t_products_tax_class_id = $coo_product->get_data_value('products_tax_class_id');
		}
		
        $t_properties_values_ids_array = array();
//        print_r($_SESSION);
        $coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
        
        $t_sql = '
            SELECT 
                properties_values_id,
                properties_id,
                sort_order,
                value_model,
                value_price
            FROM 
                properties_values
            WHERE
                properties_id IN ('.implode(',', array_keys($this->v_properties_array)).')
            ORDER BY
                sort_order,
                properties_values_id
        ';
        $t_result = xtc_db_query($t_sql);

        # init properties_values and group in properties
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $this->v_properties_array[$t_row['properties_id']]['properties_values'][$t_row['properties_values_id']] = $t_row;     
			$t_value_price = '';
			if((double)$t_row['value_price'] != 0)
			{
				$t_value_price = (double)$t_row['value_price'];
				$t_value_price = $coo_xtc_price->xtcCalculateCurr($t_value_price);
				$t_discount = $coo_xtc_price->xtcCheckDiscount($p_products_id);
				if(!empty($t_discount) && $_SESSION['customers_status']['customers_status_discount_attributes'] == 1)
				{
					$t_value_price = $coo_xtc_price->xtcFormatSpecialDiscount($p_products_id, $t_discount, $t_value_price, false);
				}
				if($_SESSION['customers_status']['customers_status_show_price_tax'] == 0)
				{
					$t_value_price = $coo_xtc_price->xtcRemoveTax($t_value_price, $coo_xtc_price->TAX[$t_products_tax_class_id]);
				}
				$t_value_price = $coo_xtc_price->xtcFormat($t_value_price, true);
			}
            $this->v_properties_array[$t_row['properties_id']]['properties_values'][$t_row['properties_values_id']]['value_price_formatted'] = trim($t_value_price);
            $t_properties_values_ids_array[$t_row['properties_values_id']] = $t_row['properties_id'];
        }
        
        if(count($t_properties_values_ids_array) > 0)
        {
            $this->get_properties_values_names($t_properties_values_ids_array);        
        }        
    }
    
    public function get_properties_values_by_properties_values_id($p_properties_values_id)
    {
        $c_properties_values_id = (int)$p_properties_values_id;
        if(empty($c_properties_values_id)) trigger_error('get_properties_values_by_properties_values_id(): typeof($p_properties_values_id) != integer', E_USER_ERROR);
        
        $t_properties_values_ids_array = array();
        
        //reset properties array
        $this->v_properties_array = array();
        
        $coo_xtc_price = new xtcPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);
        
        $t_sql = '
            SELECT 
                properties_values_id,
                properties_id,
                sort_order,
                value_model,
                value_price
            FROM 
                properties_values
            WHERE
                properties_values_id = '.$c_properties_values_id.'
        ';
        $t_result = xtc_db_query($t_sql);

        # init properties_values and group in properties
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $t_properties_id = $t_row['properties_id'];
            $t_properties_values_id = $t_row['properties_values_id'];
            $this->v_properties_array[$t_properties_id]['properties_values'][$t_properties_values_id] = $t_row;          
            $this->v_properties_array[$t_properties_id]['properties_values'][$t_properties_values_id]['value_price_formatted'] = trim($coo_xtc_price->xtcFormat((double)$t_row['value_price'], true));
            $t_properties_values_ids_array[$t_properties_values_id] = $t_properties_id;
        }
        
        if(count($t_properties_values_ids_array) > 0)
        {
            $this->get_properties_values_names($t_properties_values_ids_array);   
            return $this->v_properties_array[$t_properties_id]['properties_values'][$t_properties_values_id];
        }
        else
        {
            return array();
        }
    }
    
    protected function get_properties_names()
    {        
        $t_sql = '
            SELECT
                properties_id,
                language_id,
                properties_name,
                properties_admin_name
            FROM
                properties_description AS pd
                LEFT JOIN languages AS lang ON (pd.language_id = lang.languages_id) 
            WHERE
                properties_id IN ('.implode(',', array_keys($this->v_properties_array)).')       
            ORDER BY
                lang.sort_order
        ';
        $t_result = xtc_db_query($t_sql);
		
		$languages_query = xtc_db_query("select languages_id, name, code, image, directory, status from ".TABLE_LANGUAGES." order by sort_order");
		while($languages = xtc_db_fetch_array($languages_query)) 
		{
			$t_languages_array[$languages['languages_id']] = array ('id' => $languages['languages_id'], 'name' => $languages['name'], 'code' => $languages['code'], 'image' => $languages['image'], 'directory' => $languages['directory'], 'status' => $languages['status']);
		}
        
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $this->v_properties_array[$t_row['properties_id']]['properties_names'][$t_row['language_id']] = $t_languages_array[$t_row['language_id']];
            $this->v_properties_array[$t_row['properties_id']]['properties_names'][$t_row['language_id']]['properties_name'] = $t_row['properties_name'];
            $this->v_properties_array[$t_row['properties_id']]['properties_names'][$t_row['language_id']]['properties_admin_name'] = $t_row['properties_admin_name'];
        }        
    }
    
    protected function get_properties_values_names($p_properties_values_ids_array)
    {       
        $t_sql = '
            SELECT
                properties_values_id,
                language_id,
                values_name
            FROM
                properties_values_description AS pvd
                LEFT JOIN languages AS lang ON (pvd.language_id = lang.languages_id) 
            WHERE
                properties_values_id IN ('.implode(',', array_keys($p_properties_values_ids_array)).')         
            ORDER BY
                sort_order
        ';
        $t_result = xtc_db_query($t_sql);
        
        $languages_query = xtc_db_query("select languages_id, name, code, image, directory, status from ".TABLE_LANGUAGES." order by sort_order");
		while($languages = xtc_db_fetch_array($languages_query)) 
		{
			$t_languages_array[$languages['languages_id']] = array ('id' => $languages['languages_id'], 'name' => $languages['name'], 'code' => $languages['code'], 'image' => $languages['image'], 'directory' => $languages['directory'], 'status' => $languages['status']);
		}
        
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $this->v_properties_array[$p_properties_values_ids_array[$t_row['properties_values_id']]]['properties_values'][$t_row['properties_values_id']]['values_names'][$t_row['language_id']] = $t_languages_array[$t_row['language_id']];
            $this->v_properties_array[$p_properties_values_ids_array[$t_row['properties_values_id']]]['properties_values'][$t_row['properties_values_id']]['values_names'][$t_row['language_id']]['values_name'] = $t_row['values_name'];
        }        
    }
}