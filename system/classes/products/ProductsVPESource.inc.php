<?php
/* --------------------------------------------------------------
   ProductsVPESource.inc.php 2014-07-14 tb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php
class ProductsVPESource {
    protected $v_products_vpe_data;
    
    public function __construct() 
    {
        $t_sql = 'SELECT * FROM products_vpe';
        $t_result = xtc_db_query($t_sql);
        
        while($t_row = xtc_db_fetch_array($t_result))
        {
            $this->v_products_vpe_data[$t_row['language_id']][$t_row['products_vpe_id']] = $t_row;
        }
    }
    
    public function get_all_products_vpe($p_language_id = 0)
    {
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id'];   
        
        return $this->v_products_vpe_data[$c_language_id];
    }
    
    public function get_products_vpe($p_products_vpe_id, $p_language_id = 0)
    {
        $c_products_vpe_id = (int)$p_products_vpe_id;
        if(empty($c_products_vpe_id)) trigger_error('ProductsVPESource - get_products_vpe: typeof($p_products_vpe_id) is not integer', E_USER_ERROR); 
        
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id'];   
        
        return $this->v_products_vpe_data[$c_language_id][$c_products_vpe_id];
    }
    
    public function get_products_vpe_name($p_products_vpe_id, $p_language_id = 0)
    {
        $c_products_vpe_id = (int)$p_products_vpe_id;
        if(empty($c_products_vpe_id)) trigger_error('ProductsVPESource - get_products_vpe_name: typeof($p_products_vpe_id) is not integer', E_USER_ERROR);   
        
        $c_language_id = (int)$p_language_id;
        if(empty($c_language_id)) $c_language_id = $_SESSION['languages_id'];   
        
        return $this->v_products_vpe_data[$c_language_id][$c_products_vpe_id]['products_vpe_name'];
    }    
}