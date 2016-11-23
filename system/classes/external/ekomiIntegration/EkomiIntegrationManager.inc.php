<?php

/**
 * Class EkomiIntegrationAdminOrderActionExtender
 *
 * Handles the ekomiIntegration Module functionalities.
 * 
 * @category   System
 * @package    Modules
 * @author     Sandor Barics <sbarics@ekomi.de>
 * @copyright  2015-2016 
 */
class EkomiIntegrationManager {

    var $shopId = 0;
    var $shopPassword = '';

    /**
     * Initializes the class properties
     * 
     * @param int    $shopId       The shop id
     * @param string $shopPassword The shop password
     */
    function EkomiIntegrationManager($shopId, $shopPassword) {
        $this->set_shopId($shopId);
        $this->set_shopPassword($shopPassword);
    }

    /**
     * Validates the shop
     * 
     * @return boolean True if validated False otherwise
     */
    public function validateShop() {
        $ApiUrl = 'http://api.ekomi.de/v3/getSettings';

        $ApiUrl .= "?auth=" . $this->shopId . "|" . $this->shopPassword . "&version=cust-1.0.0&type=request&charset=iso";


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        if ($server_output == 'Access denied') {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Adds/Sends the order data to ekomi
     * 
     * @param int $orderId The order id
     */
    function addRecipient($orderId, $orderStatus = '') {

        if (gm_get_conf('EKOMIINTEGRATION_ENABLE') == '1') {
            $ekomiIntegrationOrder = MainFactory::create_object('EkomiIntegrationOrder', array($orderId));

            if (!empty($orderStatus)) {
                $ekomiIntegrationOrder->setOrderStatus($orderStatus);
            }

            $orderStatus = $ekomiIntegrationOrder->getOrderStatus();

            $ekomiIntegrationOrderStatuses = explode(',', gm_get_conf('EKOMIINTEGRATION_ORDER_STATUSES'));

            if (in_array($orderStatus, $ekomiIntegrationOrderStatuses)) {
                $postVars = $ekomiIntegrationOrder->getOrderData($this->shopId, $this->shopPassword);

                if ($postVars != '') {

                    /*
                     * The Api Url
                     */
                    $apiUrl = 'https://apps.ekomi.com/srr/add-recipient';

                    $boundary = md5(time());

                    /*
                     * Send the curl call
                     */
                    try {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $apiUrl);
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, array('ContentType:multipart/form-data;boundary=' . $boundary));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
                        $exec = curl_exec($ch);
                        curl_close($ch);
                    } catch (\Exception $e) {
                        $logger = LogControl::get_instance();
                        $logger->notice($e->getMessage(), 'ekomiIntegration', 'EkomiIntegration_addrecipient', 'notice', $p_level_type = 'DEBUG NOTICE', E_USER_NOTICE);
                    }
                }
            }
        }
    }

    /**
     * Sets the shop Id
     * 
     * @param int $shopId The shop Id
     */
    function set_shopId($shopId) {
        $this->shopId = $shopId;
    }

    /**
     * Sets the shop Password
     * 
     * @param string $shopPassword The shop Api Key
     */
    function set_shopPassword($shopPassword) {
        $this->shopPassword = $shopPassword;
    }

    /**
     * Gets the shop id
     * 
     * @return int The shop id
     */
    function get_shopId() {
        return $this->shopId;
    }

    /**
     * Gets the shop password
     * 
     * @return string The shop Api Key
     */
    function get_shopPassword() {
        return $this->shopPassword;
    }

}
