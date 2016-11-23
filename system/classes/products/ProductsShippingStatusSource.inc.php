<?php
/* --------------------------------------------------------------
   ProductsShippingStatusSource.inc.php 2014-07-14 tb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class ProductsShippingStatusSource {
    protected $v_products_shipping_status_data;
    
    public function __construct()
    {
        $t_sql = 'SELECT * FROM shipping_status';
        $result = xtc_db_query($t_sql);
        
        while($t_row = mysqli_fetch_array($result))
        {
            $this->v_products_shipping_status_data[$t_row['language_id']][$t_row['shipping_status_id']] = $t_row;
        }
    }
    
    public function get_all_shipping_status($p_language_id = 0)
    {
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id'];   
        
        return $this->v_products_shipping_status_data[$c_language_id];        
    }
    
    public function get_shipping_status($p_shipping_status_id, $p_language_id = 0)
    {
        $c_shipping_status_id = (int)$p_shipping_status_id;
        if(empty($c_shipping_status_id)) trigger_error('ProductsShippingStatusSource - get_shipping_status: typeof($p_shipping_status_id) is not integer', E_USER_ERROR); 
        
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id'];   
        
        return $this->v_products_shipping_status_data[$c_language_id][$c_shipping_status_id];
    }
    
    public function get_shipping_status_name($p_shipping_status_id, $p_language_id = 0)
    {
        $c_shipping_status_id = (int)$p_shipping_status_id;
        if(empty($c_shipping_status_id)) trigger_error('ProductsShippingStatusSource - get_shipping_status_name: typeof($p_shipping_status_id) is not integer', E_USER_ERROR); 
        
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id'];   
        
        return $this->v_products_shipping_status_data[$c_language_id][$c_shipping_status_id]['shipping_status_name'];
    }
    
    public function get_shipping_status_image($p_shipping_status_id, $p_language_id = 0)
    {
        $c_shipping_status_id = (int)$p_shipping_status_id;
        if(empty($c_shipping_status_id)) trigger_error('ProductsShippingStatusSource - get_shipping_status_image: typeof($p_shipping_status_id) is not integer', E_USER_ERROR); 
        
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id'];   
        
        return $this->v_products_shipping_status_data[$c_language_id][$c_shipping_status_id]['shipping_status_image'];
    }
}