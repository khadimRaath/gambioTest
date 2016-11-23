<?php
/* --------------------------------------------------------------
   gm_save_order.inc.php 2008-07-21 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

gm_save_order.inc.php 10.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	/*
		-> function to get content values
	*/
	
	function gm_save_order($oID, $order_html, $order_txt, $gm_send_order_status) {
		
		xtc_db_query("
						UPDATE
							" . TABLE_ORDERS . " 
						SET
							gm_order_html				= '" . addslashes($order_html)				.  "',
							gm_order_txt				= '" . addslashes($order_txt)				.  "',
							gm_send_order_status		= '" . addslashes($gm_send_order_status)	.  "',
							gm_order_send_date			= NOW()
						WHERE 
							orders_id= '" . (int)$oID . "'
					");
		return;
	}
?>