<?php
/* --------------------------------------------------------------
   GMIdStarts.php 2014-06-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

	
	class GMIdStarts_ORIGIN {
		
		function __construct() {
			return;
		}
		
		
		function get_last_orders_id(){
			
			$orders_id = array();
			
			$get_orders_id = xtc_db_query("SELECT orders_id FROM orders ORDER BY orders_id DESC LIMIT 1");
			if(mysqli_num_rows($get_orders_id) == 1){
				$orders_id = xtc_db_fetch_array($get_orders_id);
			}
			else $orders_id['orders_id'] = 1;			
			
			return $orders_id['orders_id'];
		}
		
		
		function get_last_customers_id(){
			
			$customers_id = array();
			
			$get_customers_id = xtc_db_query("SELECT customers_id FROM customers ORDER BY customers_id DESC LIMIT 1");
			if(mysqli_num_rows($get_customers_id) == 1){
				$customers_id = xtc_db_fetch_array($get_customers_id);
			}
			else $customers_id['customers_id'] = 1;
			
			return $customers_id['customers_id'];
		}
		
		
		function get_orders_autoindex(){
			
			$orders_autoindex = 1;
			
			$get_current_autoindex = xtc_db_query("SHOW TABLE STATUS LIKE 'orders'");
			if(mysqli_num_rows($get_current_autoindex) == 1){
				$row = xtc_db_fetch_array($get_current_autoindex);
				$orders_autoindex = $row['Auto_increment'];
			}	
			
			return $orders_autoindex;
		}
		
		
		function get_customers_autoindex(){
		
			$customers_autoindex = 1;
			
			$get_current_autoindex = xtc_db_query("SHOW TABLE STATUS LIKE 'customers'");
			if(mysqli_num_rows($get_current_autoindex) == 1){
				$row = xtc_db_fetch_array($get_current_autoindex);
				$customers_autoindex = $row['Auto_increment'];
			}	
			
			return $customers_autoindex;		
		}
		
		
		function set_next_orders_id($next_id){
				
			$success = false;
				
			if(is_numeric($next_id) && $next_id >= $this->get_last_orders_id()){
				xtc_db_query("ALTER TABLE orders AUTO_INCREMENT = " . (int)$next_id . "");
				$success = true;
			}
			
			return $success;
		}
		
		
		function set_next_customers_id($next_id){
			
			$success = false;
				
			if(is_numeric($next_id) && $next_id >= $this->get_last_customers_id()){
				xtc_db_query("ALTER TABLE customers AUTO_INCREMENT = " . (int)$next_id . "");
				$success = true;
			}
			
			return $success;
		}
		
	}

MainFactory::load_origin_class('GMIdStarts');
