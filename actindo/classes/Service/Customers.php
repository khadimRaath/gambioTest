<?php

/**
 * Class Actindo_Connector_Service_Customers
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
class Actindo_Connector_Service_Customers
{
    /**
     * count the number of customers and returns the highest shop and actindo deb kred id
     * @param string $params
     * @return array
     */
    public function count($params) {
        return export_customers_count(array('params'=>$params));
    }

    /**
     * This is where customers.list is handled (despite the different method name).
     * Exports the customer list or a customers details.
     * @param string $params
     * @param boolean $list if true, a customerlist is returned. if false, a single customers details are returned
     * @param struct $filters Search Filters
     * @return struct customers list
     */
    public function getList($params, $list=true, $filters=array()) {
        return export_customers_list(array('params'=>$params,'list'=>$list,'filters'=>$filters));
    }

    /**
     * sets the customernumber of a customer
     * @param string $params
     * @param int $userID userid whos customernumber should be set
     * @param int $customerNumber the customernumber to set
     * @return struct
     */
    public function set_deb_kred_id($params, $userID, $customerNumber) {
        return import_customer_set_deb_kred_id(array('params'=>$params,'userId'=>$userID,'customerNumber'=>$customerNumber));
    }
}
