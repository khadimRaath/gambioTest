<?php

/**
 * Class Actindo_Connector_Service_Orders
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
class Actindo_Connector_Service_Orders
{
    /**
     * count all available orders
     * @param string $params
     * @return array with status ok, array containing total order count and max order id
     */
    public function count($params) {
        return export_orders_count(array('params'=>$params));
    }

    /**
     * Actindo_Connector_Model_Service_Order::getList()
     * get's a list of all orders
     * limited by the $filters content
     * @param string $params
     * @param struct $filters array containing filter parameters
     * @return array of order
     */
    public function getList($params,$filters) {
        return export_orders_list(array('params'=>$params,'filters'=>$filters));
    }

    /**
     * list_positions
     * List of all Order Positions
     * @param string $params
     * @param int $orderID
     */
    public function list_positions($params,$orderID) {
        return export_orders_positions(array('params'=>$params,'orderId'=>$orderID));
    }
    /**
     * set order status
     * @param string $params
     * @param int $orderID
     * @param string $status
     * @param string $comment
     * @param int $notifyCustomer
     * @param int $sendComments
     * @return array
     */
    public function set_status($params,$orderID, $status, $comment, $notifyCustomer, $sendComments) {
        if( !parse_args($params,$ret) )
        {
            return $ret;
        }
        return import_orders_set_status($orderID,$status,$comment,$notifyCustomer,$sendComments);
    }
    /**
     * set tracking code
     * Also sets order to shipped
     * @param string $params
     * @param int $orderID
     * @param string $trackingCode
     * @return array
     */
    public function set_trackingcode($params,$orderID, $trackingCode) {
        return array('ok' => true);
    }
}
