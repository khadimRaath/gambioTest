<?php
/* --------------------------------------------------------------
   login_mobile.php 2011-12-22
   Gambio GmbH
   http://www.gambio.de
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

include ('includes/application_top.php');

require_once (DIR_FS_INC.'xtc_validate_password.inc.php');
require_once (DIR_FS_INC.'xtc_array_to_string.inc.php');
require_once (DIR_FS_INC.'xtc_write_user_info.inc.php');

$gm_log = MainFactory::create_object('GMTracker');
$gm_log->gm_delete();


$response = array ('state'=>false,'message'=>'ip blocked','additional'=>array());

if($gm_log->gm_ban() == false) {
	if (isset ($_GET['action']) && ($_GET['action'] == 'process')) {
			
		$email_address = xtc_db_prepare_input($_POST['email_address']);
		$password 		 = xtc_db_prepare_input($_POST['password']);
		
		// Check if email exists
		$check_customer_query = xtc_db_query("select customers_id, customers_vat_id, customers_firstname,customers_lastname, customers_gender, customers_password, customers_email_address, customers_default_address_id from ".TABLE_CUSTOMERS." where customers_email_address = '".xtc_db_input($email_address)."' and account_type = '0'");
		if (!xtc_db_num_rows($check_customer_query)) {
		  
			$gm_log->gm_track();
			
			// Handle Error Message
			//=====================
			$response = array ('state'=>3001);
			
		}else{
			$check_customer = xtc_db_fetch_array($check_customer_query);
				
			// Check that password is good
			if (!xtc_validate_password($password, $check_customer['customers_password'])) {
				
				$gm_log->gm_track();
				
			// Handle Error Message
			//=====================
			$response = array ('state'=>3001);
				
			}else{
				
				$gm_log->gm_delete(true);
	
				if (SESSION_RECREATE == 'True'){
					xtc_session_recreate();
				}
	
				$check_country_query = xtc_db_query("select entry_country_id, entry_zone_id from ".TABLE_ADDRESS_BOOK." where customers_id = '".(int) $check_customer['customers_id']."' and address_book_id = '".$check_customer['customers_default_address_id']."'");
				$check_country = xtc_db_fetch_array($check_country_query);
	
				$_SESSION['customer_gender'] 		= $check_customer['customers_gender'];
				$_SESSION['customer_first_name'] 	= $check_customer['customers_firstname'];
				$_SESSION['customer_last_name'] 	= $check_customer['customers_lastname'];
				$_SESSION['customer_id'] 			= $check_customer['customers_id'];
				$_SESSION['customer_country_id'] 	= $check_country['entry_country_id'];
				$_SESSION['customer_zone_id'] 		= $check_country['entry_zone_id'];
	
				$date_now = date('Ymd');
	
				xtc_db_query("update ".TABLE_CUSTOMERS_INFO." SET customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 WHERE customers_info_id = '".(int) $_SESSION['customer_id']."'");
				xtc_write_user_info((int) $_SESSION['customer_id']);
				
				
				// Handle Error Message
				//=====================
				$response = array ('state'=>3000,'additional'=>array('gender'	=> $_SESSION['customer_gender'],
                                                              'first_name'		=> $_SESSION['customer_first_name'],
                                                              'last_name'	  	=> $_SESSION['customer_last_name'], 
                                                              'customer_id' 	=> $_SESSION['customer_id'],
															  'country'			=> $_SESSION['customer_country_id'],
															  'zone'			=> $_SESSION['customer_zone_id']
                                                              ));
			}
		}
	}	
}


echo json_encode($response);

((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
?>