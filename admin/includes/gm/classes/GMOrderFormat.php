<?php
/* --------------------------------------------------------------
   GMOrderFormat.php 2014-08-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

*	-> class to create a google sitemap
	*/
	class GMOrderFormat_ORIGIN {
		
		/*
		*	-> type: invoice or packingslip
		*/
		var $type;


		/*
		*	-> constructor
		*/
		function __construct() {
		
		}

		/*
		*	-> get act id from table orders
		*/
		function get_act_id($type) {			
			
			if($type == 'GM_NEXT_INVOICE_ID') {
				$type = 'gm_orders_id'; 
			} else {
				$type = 'gm_packings_id'; 
			}	
			$gm_query = xtc_db_query("
										SELECT
											" . $type . "
										AS
											id
										FROM 
											orders
										ORDER BY
											" . $type . "
										DESC
										LIMIT 1
									");
			if(xtc_db_num_rows($gm_query) > 0) {
				$gm_row = xtc_db_fetch_array($gm_query);
				return $gm_row['id'];
			} else {
				return 0;
			}			
		}		
		
		function get_next_id($type) {
			
			return gm_get_conf($type);
		}
		
		function update_next_id($type, $value, $oID) {			
			
			if($type == 'GM_NEXT_INVOICE_ID') {
				$type = 'gm_orders_id'; 
			} else {
				$type = 'gm_packings_id'; 
			}	
			
			xtc_db_query("
									UPDATE
										orders
									SET
										" . $type . " = '" . $value . "'
									WHERE
										orders_id = '" . $oID . "'
									");

		}

		function update_next_code($type, $value, $oID) {			
			
			if($type == 'GM_NEXT_INVOICE_ID') {
				$type = 'gm_orders_code'; 
			} else {
				$type = 'gm_packings_code'; 
			}	
			
			xtc_db_query("
									UPDATE
										orders
									SET
										" . $type . " = '" . $value . "'
									WHERE
										orders_id = '" . $oID . "'
									");

		}
		
		function set_next_id($type, $next_id) {
			if(is_numeric($next_id) && $next_id > $this->get_act_id($type)) {
				gm_set_conf($type, $next_id);
				return true;
			} else {
				return false;			
			}		
		}

		/*
		*	function to change the order status
		*	@param int		$p_orders_id
		*	@param int		$p_order_status_id
		*	@param int		$p_customer_notified
		*	@param String	$p_comment
		*	@return void
		*/
		function update_orders_status($p_orders_id, $p_order_status_id, $p_customer_notified, $p_comment)
		{
			if(!empty($p_order_status_id))
			{
				xtc_db_query("
								UPDATE " .
									TABLE_ORDERS . "
								SET
									orders_status	= '" . (int)$p_order_status_id	. "',
									last_modified	= NOW()
								WHERE
									orders_id		= '" . (int)$p_orders_id		. "'
				");

				xtc_db_query("
								INSERT INTO " . 
									TABLE_ORDERS_STATUS_HISTORY . " 
								SET
									orders_id			= '" . (int)$p_orders_id		. "',
									orders_status_id	= '" . (int)$p_order_status_id	. "',
									customer_notified	= '" . (int)$p_customer_notified. "',
									comments			= '" . xtc_db_input($p_comment)	. "',
									date_added			= NOW()"
				);
			}
			return;
		}


		/*
		*	function to get the date of the invoice
		*	@param int		$p_orders_id
		*	@param int		$p_order_status_id
		*	@return String
		*/
		function get_invoice_date($p_orders_id, $p_order_status_id)
		{
			$t_invoice_date =  date('d.m.Y');
			
			if(!empty($p_order_status_id))
			{
				$t_query = xtc_db_query("
											SELECT
												date_added
											FROM " . 
												TABLE_ORDERS_STATUS_HISTORY . " 
											WHERE
												orders_id			= '" . (int)$p_orders_id		. "'
											AND
												orders_status_id	= '" . (int)$p_order_status_id	. "'
											ORDER BY 
												date_added ASC
											LIMIT 1
				");

				if((int)xtc_db_num_rows($t_query) > 0)
				{
					$t_row = xtc_db_fetch_array($t_query);
					
					$t_invoice_date = xtc_date_short($t_row['date_added']);
				}
			}

			return $t_invoice_date;
		}
	}

MainFactory::load_origin_class('GMOrderFormat');
