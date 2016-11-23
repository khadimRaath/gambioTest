<?php

/**
 * Class EkomiIntegrationOrder
 *
 * Handles the order related functionalities.
 * 
 * @category   System
 * @package    Modules
 * @author     Sandor Barics <sbarics@ekomi.de>
 * @copyright  2015-2016 
 */
class EkomiIntegrationOrder {

    var $order = null;

    /**
     * Initializes the order object
     * 
     * @param int $orderId The order id
     */
    function EkomiIntegrationOrder($orderId) {
        $this->order = $this->_fetchOrder($orderId);
    }

    /**
     * Gets the order status
     * 
     * @return int order status id Otherwise null
     */
    function getOrderStatus() {
        if (isset($this->order['orders_status'])) {
            return $this->order['orders_status'];
        } else {
            NULL;
        }
    }

    /**
     * Sets the order status
     * 
     * @param int $orderStatus The order status
     * 
     */
    function setOrderStatus($orderStatus) {
        if (isset($this->order['orders_status'])) {
            $this->order['orders_status'] = $orderStatus;
        }
    }

    /**
     * Gets the order data
     * 
     * @param int    $shopId       The shop Id
     * @param string $shopPassword The shop password
     * 
     * @return string The parameters
     */
    function getOrderData($shopId, $shopPassword) {

        $apiMode = $this->getRecipientType($this->order['customers_telephone']);
        $scheduleTime = date('d-m-Y H:i:s', strtotime($this->getOrdertimestamp()));

        $senderName = $this->getStoreName();

        if ($apiMode == 'sms' && strlen($senderName) > 11)
            $senderName = substr($senderName, 0, 11);

        $fields = array(
            'shop_id' => $shopId,
            'password' => $shopPassword,
            'recipient_type' => $apiMode,
            'salutation' => '',
            'first_name' => $this->order['billing_firstname'],
            'last_name' => $this->order['billing_lastname'],
            'email' => $this->order['customers_email_address'],
            'transaction_id' => $this->order['orders_id'],
            'transaction_time' => $scheduleTime,
            'telephone' => $this->order['customers_telephone'],
            'sender_name' => $senderName,
            'sender_email' => $this->getStoreEmail()
        );
        if ($this->order['customers_id'] > 0) {
            $fields['client_id'] = $this->order['customers_id'];
            $fields['screen_name'] = $this->order['customers_name'];
        } else {
            $fields['client_id'] = $this->order['customers_id'];
            $fields['screen_name'] = $this->order['customers_name'];
        }
        if (gm_get_conf('EKOMIINTEGRATION_PRODUCT_BASE') == '1') {
            $fields['has_products'] = 1;
            $productsData = $this->getProductsData();
            $fields['products_info'] = json_encode($productsData['product_info']);
            $fields['products_other'] = json_encode($productsData['other']);
        }
        $postVars = '';
        $counter = 1;
        foreach ($fields as $key => $value) {
            if ($counter > 1)
                $postVars .= "&";
            $postVars .= $key . "=" . $value;
            $counter++;
        }

        return $postVars;
    }

    /**
     * Gets the products data
     * 
     * @return array The products array
     * 
     * @access protected
     */
    protected function getProductsData() {

        $orderProducts = $this->_fetchOrderProducts($this->order['orders_id']);

        $products = array();

        require_once DIR_FS_DOCUMENT_ROOT . 'includes/configure.php';

        $basePath = HTTP_SERVER . DIR_WS_CATALOG;

        foreach ($orderProducts as $product) {

            $product_id = $product['products_id'];

            $products['product_info'][$product_id] = $product['products_name'];

            //make the product url
            $canonicalUrl = $basePath . 'product_info.php?info=';
            $canonicalUrl .= $this->_getProductUrl($product_id, $_SESSION['languages_id']);

            $productOther = array();

            $imageUrl = $basePath . DIR_WS_ORIGINAL_IMAGES . $this->_getImage($product_id);

            $productOther['image_url'] = utf8_decode($imageUrl);

            $productOther['brand_name'] = '';

            $productOther['product_ids'] = array(
                'gbase' => utf8_decode($product_id)
            );

            $productOther['links'] = array(
                array('rel' => 'canonical', 'type' => 'text/html',
                    'href' => utf8_decode($canonicalUrl))
            );

            $products['other'][$product['id']]['product_other'] = $productOther;
        }

        return $products;
    }

    /**
     * Gets the recipient type
     * 
     * @param string $telephone The phone nu,ber
     * 
     * @return string Recipient type
     * 
     * @access protected
     */
    protected function getRecipientType($telephone) {

        $reviewMod = gm_get_conf('EKOMIINTEGRATION_REVIEW_MOD');
        $apiMode = 'email';
        switch ($reviewMod) {
            case 'sms':
                $apiMode = 'sms';
                break;
            case 'email':
                $apiMode = 'email';
                break;
            case 'fallback':
                if ($this->validateE164($telephone))
                    $apiMode = 'sms';
                else
                    $apiMode = 'email';
                break;
        }

        return $apiMode;
    }

    /**
     * Gets the order timestamp
     * 
     * @return string The timestamp
     * 
     * @access protected
     */
    protected function getOrdertimestamp() {
        $order_date = $this->order['date_purchased'];
        $order_date_array = explode(' ', $order_date);

        return $order_date_array[0];
    }

    function getStoreName() {
        return STORE_NAME;
    }

    function getStoreEmail() {
        return STORE_OWNER_EMAIL_ADDRESS;
    }

    /**
     * Validates E164 numbers
     * 
     * @param $phone The phone number
     *
     * @return bool Yes if matches False otherwise
     * 
     * @access protected
     */
    protected function validateE164($phone) {
        $pattern = '/^\+?[1-9]\d{1,14}$/';

        preg_match($pattern, $phone, $matches);

        if (!empty($matches)) {
            return true;
        }

        return false;
    }

    /**
     * Fetches the order data
     * 
     * @param int $order_id The order id
     * 
     * @return array The order data
     * 
     * @access protected
     */
    protected function _fetchOrder($order_id) {
        $query = xtc_db_query("SELECT * FROM " . TABLE_ORDERS . "
                                     WHERE orders_id='" . $order_id . "'");

        $data = xtc_db_fetch_array($query);

        return $data;
    }

    /**
     * Fetches the ordered products
     * 
     * @param type $order_id The order id
     * 
     * @return array  The products data
     * 
     * @access protected
     */
    protected function _fetchOrderProducts($order_id) {
        $query = "SELECT * FROM " . TABLE_ORDERS_PRODUCTS . "
                   WHERE orders_id={$order_id}";

        $order_product_data = xtc_db_query($query);

        $prodcuts = array();

        while ($prodcut = xtc_db_fetch_array($order_product_data)) {
            $prodcuts[] = $prodcut;
        }

        return $prodcuts;
    }

    /**
     * Gets the product image
     * 
     * @param int $product_id The product id
     * 
     * @return string The images name Otherwise empty
     * 
     * @access protected
     */
    protected function _getImage($product_id) {
        $query = xtc_db_query("SELECT products_image FROM " . TABLE_PRODUCTS . "
                                     WHERE products_id='" . $product_id . "'");

        $data = xtc_db_fetch_array($query);
        if (isset($data['products_image'])) {
            return $data['products_image'];
        }

        return '';
    }

    /**
     * Gets the product Url
     * 
     * @param int $product_id The product id
     * @param int $language_id The language Id
     * 
     * @return string The product url Otherwise empty
     * 
     * @access protected
     */
    protected function _getProductUrl($product_id, $language_id) {
        $query = xtc_db_query("select products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int) $product_id . "' and language_id = '" . (int) $language_id . "'");
        $product = xtc_db_fetch_array($query);

        return $product['products_url'];
    }

}
