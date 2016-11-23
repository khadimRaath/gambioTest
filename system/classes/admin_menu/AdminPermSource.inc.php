<?php
/* --------------------------------------------------------------
   AdminPermSource.inc.php 2014-07-17 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AdminPermSource {

  var $v_permission_structure_array = array();

  function init_structure_array( ){
    $this->v_permission_structure_array = array();
    $query = xtc_db_query("SELECT * FROM admin_access");
    while($row = xtc_db_fetch_array($query)){
      $this->v_permission_structure_array[] = $row;
    }
  }
  
  function get_permissions( $p_customers_id ){
    foreach($this->v_permission_structure_array AS $t_key => $t_customer){
      if($t_customer['customers_id'] == $p_customers_id){
        return $t_customer;
      }
    }
  }
  
  function is_permitted( $p_customers_id,  $p_admin_page ){
    $p_admin_page = str_replace(".php", "", $p_admin_page);
    foreach($this->v_permission_structure_array AS $t_key => $t_customer){
      if($t_customer['customers_id'] == $p_customers_id && $t_customer[$p_admin_page] == 1){
        return true;
      }
    }
    return false;
  } 
}