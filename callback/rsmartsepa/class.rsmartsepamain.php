<?php
/* --------------------------------------------------------------
  class.rsmartsepamain.php 2015-04-24 wem
  Payment Module Mainclass 
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'callback/rsmartsepa/RsmartsepaHelper.php');


class rsmartsepa_main {
    
    var $code, $title, $description, $transaction_id, $language, $enabled, $tmpStatus, $module;
    
    public function __construct() {
        $this->transaction_id = '';
    } // End constructor
    
    /*
     * Implementation of payment module method
     * Check if module is installed (Administration Tool)
     * TABLES: configuration
     */
    function check() {
        if (!isset($this->_check)) {
            $constantName = 'MODULE_PAYMENT_RSMARTSEPA_STATUS';
            $check_query = xtc_db_query("SELECT configuration_value FROM " . 
                                        RsmartsepaHelper::escapeSql(TABLE_CONFIGURATION) . 
                                        " WHERE configuration_key = '" .
                                        RsmartsepaHelper::escapeSql($constantName) .
                                        "'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        return $this->_check;
    } // End check
    
    /**
     * Implementation of payment module method
     * Return the configuration keys
     * @return array
     *   An array of configuration keys 
     */
    function keys() {
        $keys = array();
        
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_STATUS';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_SORT_ORDER';

        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_ZONE';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_ALLOWED';
        // $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_DELETE_ORDER';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_ERROR';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_OK';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_TEMP';

        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_URI';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SELLERID';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_KEY';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_PROVIDERID';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_COUNTRYID';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SALESPOINTID';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_DESCRIPTION';
        
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE';
        $keys[] = 'MODULE_PAYMENT_RSMARTSEPA_DEBUGMODE';
        
        return $keys; 
    } // End keys
    
    /**
     * Install the module (Administration Tool)
     * TABLES: configuration
     */
    function install() {
        
        $tableNAME = RsmartsepaHelper::escapeSql(TABLE_CONFIGURATION);
        
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_STATUS');
        $defaultValue = 'False';
        $sortOrder = '0';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())";
        xtc_db_query($sql);
        
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_SORT_ORDER');
        $defaultValue = '0';
        $sortOrder = '5';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);
                
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_ZONE');
        $defaultValue = '0';
        $sortOrder = '15';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, use_function, set_function, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())";
        xtc_db_query($sql);
        
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_ALLOWED');
        $defaultValue = '';
        $sortOrder = '20';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);
        
        
//        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_DELETE_ORDER');
//        $defaultValue = 'True';
//        $sortOrder = '25';
//        $sql = "insert into $tableNAME" .
//               " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)" .
//               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())";
//        xtc_db_query($sql);
        
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_ERROR');
        $defaultValue = '1';
        $sortOrder = '30';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())";
        xtc_db_query($sql);
        
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_OK');
        $defaultValue = '1';
        $sortOrder = '35';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())";
        xtc_db_query($sql);
        
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_TEMP');
        $defaultValue = '1';
        $sortOrder = '40';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, use_function, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())";
        xtc_db_query($sql);
        
        
        // Terminaldata: URI
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_URI');
        $defaultValue = 'https://p2.raasys.de';
        $sortOrder = '42';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);
        
        // Terminaldata: sellerId
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SELLERID');
        $defaultValue = '';
        $sortOrder = '43';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);

        // Terminaldata: key
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_KEY');
        $defaultValue = '';
        $sortOrder = '44';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);
        
        // Terminaldata: providerId
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_PROVIDERID');
        $defaultValue = 'T002p2.raasys.de';
        $sortOrder = '45';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);
        
        // Terminaldata: countryId
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_COUNTRYID');
        $defaultValue = 'de';
        $sortOrder = '46';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);
        
        // Terminaldata: salesPointId
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SALESPOINTID');
        $defaultValue = '';
        $sortOrder = '47';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);

        // Terminaldata: description
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_DESCRIPTION');
        $defaultValue = '';
        $sortOrder = '48';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', now())";
        xtc_db_query($sql);
        
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE');
        $defaultValue = 'False';
        $sortOrder = '50';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())";
        xtc_db_query($sql);
        
        
        $constantName = RsmartsepaHelper::escapeSql('MODULE_PAYMENT_RSMARTSEPA_DEBUGMODE');
        $defaultValue = 'False';
        $sortOrder = '55';
        $sql = "insert into $tableNAME" .
               " (configuration_key, configuration_value, configuration_group_id, sort_order, set_function, date_added)" .
               "  values ('$constantName', '$defaultValue', '6', '$sortOrder', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())";
        xtc_db_query($sql);
        
        $this->_installRsmartSepaTable();
        
        
        // Add new order stati
        $statiExisting = FALSE;
        $tableORDERS_STATUS = RsmartsepaHelper::escapeSql(TABLE_ORDERS_STATUS);
        $sql = "SELECT COUNT(*) AS numstati FROM " . $tableORDERS_STATUS . 
               " WHERE orders_status_name IN ('" . 
                 'rsmartsepa Error' . "', '" . 
                 'rsmartsepa Fehler' . "', '" . 
                 'rsmartsepa Paid' . "', '" . 
                 'rsmartsepa Bezahlt' . "', '" . 
                 'rsmartsepa Pending' . "', '" . 
                 'rsmartsepa Offen' . "'" . 
                ")";
        $query = xtc_db_query($sql);
        $records = xtc_db_fetch_array($query);
        if(isset($records) && $records !== FALSE && is_array($records) && isset($records['numstati'])) {
            $count = intval($records['numstati']);
            if($count == 6) {
                $statiExisting = TRUE;
            }
        }
        
        if($statiExisting == FALSE) {
            $langIDs = array();
            $tableLANGUAGES = RsmartsepaHelper::escapeSql(TABLE_LANGUAGES);
            $sql = "SELECT  languages_id, code FROM " . $tableLANGUAGES;
            $query = xtc_db_query($sql);
            while($langRow = xtc_db_fetch_array($query)) {
                $currCode = $langRow['code']; 
                $langIDs[$currCode] = $langRow['languages_id'];
            }
            if(count($langIDs) > 0) {
                $sql = "SELECT MAX(orders_status_id) AS maxid FROM " . $tableORDERS_STATUS;
                $query = xtc_db_query($sql);
                $records = xtc_db_fetch_array($query);
                if(isset($records) && $records !== FALSE && is_array($records) && isset($records['maxid'])) {
                    $maxid = intval($records['maxid']);
                    
                    $maxid += 1;
                    if(isset($langIDs['en'])) {
                        $sql = "INSERT INTO " . $tableORDERS_STATUS . " (orders_status_id,language_id,orders_status_name) " .
                               "VALUES ('" . $maxid . "','" . $langIDs['en'] . "','" . 'rsmartsepa Pending' . "')";
                        xtc_db_query($sql);
                    }
                    if(isset($langIDs['de'])) {
                        $sql = "INSERT INTO " . $tableORDERS_STATUS . " (orders_status_id,language_id,orders_status_name) " .
                               "VALUES ('" . $maxid . "','" . $langIDs['de'] . "','" . 'rsmartsepa Offen' . "')";
                        xtc_db_query($sql);                        
                    }
                    
                    $maxid += 1;
                    if(isset($langIDs['en'])) {
                        $sql = "INSERT INTO " . $tableORDERS_STATUS . " (orders_status_id,language_id,orders_status_name) " .
                               "VALUES ('" . $maxid . "','" . $langIDs['en'] . "','" . 'rsmartsepa Error' . "')";
                        xtc_db_query($sql);
                    }
                    if(isset($langIDs['de'])) {
                        $sql = "INSERT INTO " . $tableORDERS_STATUS . " (orders_status_id,language_id,orders_status_name) " .
                               "VALUES ('" . $maxid . "','" . $langIDs['de'] . "','" . 'rsmartsepa Fehler' . "')";
                        xtc_db_query($sql);                        
                    }

                    $maxid += 1;
                    if(isset($langIDs['en'])) {
                        $sql = "INSERT INTO " . $tableORDERS_STATUS . " (orders_status_id,language_id,orders_status_name) " .
                               "VALUES ('" . $maxid . "','" . $langIDs['en'] . "','" . 'rsmartsepa Paid' . "')";
                        xtc_db_query($sql);
                    }
                    if(isset($langIDs['de'])) {
                        $sql = "INSERT INTO " . $tableORDERS_STATUS . " (orders_status_id,language_id,orders_status_name) " .
                               "VALUES ('" . $maxid . "','" . $langIDs['de'] . "','" . 'rsmartsepa Bezahlt' . "')";
                        xtc_db_query($sql);                        
                    }
                    
                }
            }
        }
    } // End install
    
    
    /**
     * Remove the module (Administration Tool)
     * TABLES: configuration
     */
    function remove() {
        $tableNAME = RsmartsepaHelper::escapeSql(TABLE_CONFIGURATION);
        $sql = "delete from $tableNAME where configuration_key in ('" . implode("', '", $this->keys()) . "')";
        xtc_db_query($sql); 
        
        $this->_removeRsmartSepaTable();
    } // End remove
    
    
    function _installRsmartSepaTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS `rsmartsepa` (
               `tid` varchar(128) NOT NULL,
               `action` varchar(20) NOT NULL DEFAULT '',
               `status` varchar(20) NOT NULL DEFAULT '',
               `created` int(11) NOT NULL DEFAULT '0',
               `changed` int(11) NOT NULL DEFAULT '0',
               `data` mediumtext NOT NULL DEFAULT '',
               PRIMARY KEY (`tid`)
            )
            ";
        xtc_db_query($sql);
    } // End _installRsmartSepaTable
    
    function _removeRsmartSepaTable() {
        $sql = "DROP TABLE IF EXISTS `rsmartsepa`";
        xtc_db_query($sql);
    } // End _removeRsmartSepaTable
    
    function update_status() {
        global $order;
        
        
        
        if(($this->enabled == true) && ((int) constant('MODULE_PAYMENT_RSMARTSEPA_ZONE') > 0 )) {
            $check_flag = FALSE;
            $sql = "select zone_id from " . RsmartsepaHelper::escapeSql(TABLE_ZONES_TO_GEO_ZONES) .
                   " where geo_zone_id = '" . constant('MODULE_PAYMENT_RSMARTSEPA_ZONE') .
                   "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id";
            $check_query = xtc_db_query($sql);
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                }
                else if($check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            } // end: while
            
            if(!RsmartsepaHelper::isPHPExtensionInstalled('curl') || !RsmartsepaHelper::isPHPExtensionInstalled('libgd')) {
                $check_flag = FALSE;
            }
            
            // Test Library
            try {
                RsmartsepaHelper::startLibrary();
            } catch(Exception $ex) {
                $check_flag = FALSE;
            }
            
            
//            // Check if total is 0. Then disable this module
//            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
//                $total = $order->info['total'] + $order->info['tax'];
//                if($total == 0.00) {
//                    $check_flag = FALSE;
//                }
//            } else {
//                $total = $order->info['total'];
//                if($total == 0.00) {
//                    $check_flag = FALSE;
//                }
//            }
            
            
            if ($check_flag == false) {
                $this->enabled = false;
            }
        } // end: if(($this->enabled == true) && ...
        
    } // End update_status
    
    function get_error() {
        // Find the error message in the query parameter 'error'.
        // This may be set in the function pre_confirmation_check()
        $errorMessage = RsmartsepaHelper::getRequestValue('error', '');
        $errorMessage = stripslashes(urldecode($errorMessage));
        $errorArray = array(
            'title'         => MODULE_PAYMENT_RSMARTSEPA_STR_CHECKOUT_MODULETITLE,
            'error'         => $errorMessage,
        );
        return $errorArray;
    } // End get_error
    
} // End class rsmartsepa_main

