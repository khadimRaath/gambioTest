<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */
class ShopgateCouponModel extends ShopgateObject
{
    /**
     * @var ShopgateConfigGambioGx $config
     */
    private $config;

    /**
     * @var int $languageId
     */
    private $languageId;

    /**
     * @var string $language
     */
    private $language;

    /**
     * @var string $currencyCode
     */
    private $currencyCode;

    /**
     * @var int $countryId
     */
    private $countryId;

    const SG_COUPON_TYPE_GIFT          = 'G';
    const SG_COUPON_TYPE_PERCENTAGE    = 'P';
    const SG_COUPON_TYPE_FIX           = 'F';
    const SG_COUPON_TYPE_FREE_SHIPPING = 'S';
    const SG_COUPON_ACTIVE             = 'Y';

    /**
     * @param ShopgateConfigGambioGx $config
     * @param int                    $languageId
     * @param string                 $language
     * @param array                  $currency
     * @param int                    $countryId
     */
    public function __construct(ShopgateConfigGambioGx $config, $languageId, $language, $currency, $countryId)
    {
        $this->config       = $config;
        $this->languageId   = $languageId;
        $this->currencyCode = $currency['code'];
        $this->language     = $language;
        $this->countryId    = $countryId;
        $this->initializeCouponModule();
    }

    /**
     * check if a coupon is valid
     * there are different types of coupons
     * G = gift, this coupon will be created if a customer registered a new account in the shop
     * S = shipping, gives free shipping
     * F = fixed, the coupon value is a fixed value, which needs to be subtracted from item(s) / the whole cart,
     *     depending on the coupon's setting
     * P = Percentage, the coupon amount is a percentage value which needs to be used to calculate the amount to
     *     subtract for item(s) / the whole cart, depending on the coupon's setting
     *
     * @param ShopgateCart          $cart
     * @param ShopgateItemCartModel $cartItemModel
     * @param array                 $coupon
     * @param float|string          $orderAmount
     *
     * @return string
     */
    public function validateCoupon(
        ShopgateCart $cart, ShopgateItemCartModel $cartItemModel, array $coupon, $orderAmount
    ) {
        if ($coupon['coupon_type'] == self::SG_COUPON_TYPE_GIFT) {
            // coupons of type gift are special. After you redeemed a gift coupon the amount of the coupon is
            // credited to your account. You can use this credit to reduce the amount you have to pay by placing an order
            // As a workaround we set the coupon as invalid.
            return ERROR_NO_INVALID_REDEEM_COUPON . "\n";
        }
    
        $msg = "";
        
        if ($coupon['coupon_minimum_order'] > $orderAmount) {
            $xtPrice      = new xtcPrice($this->currencyCode, "");
            $msg = (defined("ERROR_MINIMUM_ORDER_COUPON_1")
                    ? ERROR_MINIMUM_ORDER_COUPON_1
                    : SHOPGATE_COUPON_ERROR_MINIMUM_ORDER_AMOUNT_NOT_REACHED)
                . " (" . trim($xtPrice->xtcFormat($coupon['coupon_minimum_order'], true)) . ")\n";
        }
        
        if (!$this->checkCouponRedeemAmount($coupon)) {
            $msg = ERROR_INVALID_USES_COUPON . $coupon['uses_per_coupon'] . TIMES . "\n";
        }

        if (!$this->checkCouponRedeemAmountToCustomer($coupon, $cart->getExternalCustomerId())) {
            $msg = ERROR_INVALID_USES_USER_COUPON . $coupon['uses_per_user'] . TIMES . "\n";
        }
        
        if ($coupon['restrict_to_products']
            && !$this->cartHasRestrictedProduct(
                $coupon, $cart->getItems(), $cartItemModel
            )
        ) {
            $msg = SHOPGATE_COUPON_ERROR_RESTRICTED_PRODUCTS . "\n";
        }
        
        if ($coupon['restrict_to_categories']
            && !$this->cartHasRestrictedProductToCategory(
                $coupon, $cart->getItems()
            )
        ) {
            $msg = SHOPGATE_COUPON_ERROR_RESTRICTED_CATEGORIES . "\n";
        }
        
        $currentDate = date("Y-m-d H:i:s");
        if ($coupon['coupon_start_date'] >= $currentDate) {
            $msg = ERROR_INVALID_STARTDATE_COUPON . "\n";
        }
        
        if ($coupon['coupon_expire_date'] <= $currentDate) {
            $msg = ERROR_INVALID_FINISDATE_COUPON . "\n";
        }
        
        if (empty($coupon) || $coupon['coupon_active'] !== self::SG_COUPON_ACTIVE) {
            $msg = ShopgateLibraryException::COUPON_NOT_VALID . "\n";
        }
        
        return $msg;
    }

    /**
     * redeem the coupon in the shop system
     *
     * @param ShopgateExternalCoupon $sgCoupon
     * @param int                    $customerId
     */
    public function redeemCoupon(ShopgateExternalCoupon $sgCoupon, $customerId)
    {
        $coupon = $this->getCouponByCode($sgCoupon->getCode());
        if ($coupon['coupon_type'] == self::SG_COUPON_TYPE_GIFT) {
            $this->proceedWelcomeVoucher($coupon, $customerId);
        } else {
            $this->insertRedeemInformation($coupon['coupon_id'], $customerId);
        }
    }

    /**
     * insert the order total value for coupons
     *
     * @param int                    $orderId
     * @param ShopgateExternalCoupon $sgCoupon
     * @param int                    $sortOrder
     *
     * @return float
     */
    public function insertOrderTotal($orderId, ShopgateExternalCoupon $sgCoupon, $sortOrder = 0)
    {
        $xtPrice      = new xtcPrice($this->currencyCode, "");
        $insertAmount = $xtPrice->xtcFormat($sgCoupon->getAmountGross() * (-1), true);

        $orderTotal = array(
            'orders_id'  => $orderId,
            'title'      => MODULE_ORDER_TOTAL_COUPON_TITLE,
            'text'       => '<strong><span style="color:#ff0000">' . $insertAmount . '</span></strong>',
            'value'      => $insertAmount,
            'class'      => 'ot_coupon',
            'sort_order' => $sortOrder,
        );

        xtc_db_perform(TABLE_ORDERS_TOTAL, $orderTotal);

        return $sgCoupon->getAmountGross();
    }

    /**
     * read the coupon data from the database by coupon code
     *
     * @param string $code
     *
     * @return array
     */
    public function getCouponByCode($code)
    {
        $code        = xtc_db_prepare_input($code);
        $couponQuery =
            "SELECT * FROM `" . TABLE_COUPONS . "` AS c " .
            "LEFT JOIN `" . TABLE_COUPONS_DESCRIPTION . "` AS cd ON cd.coupon_id=c.coupon_id " .
            "WHERE c.coupon_code='{$code}' AND cd.language_id={$this->languageId}";
        
        $coupon = xtc_db_fetch_array(xtc_db_query($couponQuery));
        // check if coupon is an gift voucher 
        if (empty($coupon)) {
            $couponQuery = "SELECT * FROM `" . TABLE_COUPONS . "` AS c WHERE c.coupon_code='{$code}'";
            $coupon      = xtc_db_fetch_array(xtc_db_query($couponQuery));
        }

        return $coupon;
    }

    /**
     * fill the ShopgateExternalCoupon object with data e.g. coupon amount.
     *
     * @param array                  $coupon
     * @param ShopgateExternalCoupon $sgCoupon
     * @param ShopgateCart           $cart
     * @param ShopgateItemCartModel  $cartItemModel
     * @param int                    $customerGroupId
     */
    public function setCouponData(
        array $coupon, ShopgateExternalCoupon $sgCoupon, ShopgateCart $cart, ShopgateItemCartModel $cartItemModel,
        $customerGroupId
    ) {
        $creditAmount = 0;
        $sgCoupon->setIsFreeShipping(false);
        
        switch ($coupon['coupon_type']) {
            case self::SG_COUPON_TYPE_FREE_SHIPPING:
            case self::SG_COUPON_TYPE_FIX:
                $conditionRestrictedProducts   = $coupon['restrict_to_products']
                    && $this->cartHasRestrictedProduct(
                        $coupon, $cart->getItems(), $cartItemModel
                    ) || empty($coupon['restrict_to_products']);
                $conditionRestrictedCategories = $coupon['restrict_to_categories']
                    && $this->cartHasRestrictedProductToCategory(
                        $coupon, $cart->getItems(), $cartItemModel
                    ) || empty($coupon['restrict_to_categories']);
                
                if ($conditionRestrictedProducts && $conditionRestrictedCategories) {
                    $creditAmount = $coupon['coupon_amount'];
                    
                    if ($coupon['coupon_type'] == self::SG_COUPON_TYPE_FREE_SHIPPING) {
                        $sgCoupon->setIsFreeShipping(true);
                    }
                }
                break;
            case self::SG_COUPON_TYPE_PERCENTAGE:
                if ($coupon['restrict_to_products']) {
                    $creditAmount =
                        $this->calculateCreditToRestrictedProducts(
                            $coupon, $cart->getItems(), $cartItemModel, $customerGroupId
                        );
                } elseif ($coupon['restrict_to_categories']) {
                    $creditAmount =
                        $this->calculateCreditToRestrictedProductsToCategory(
                            $coupon, $cart->getItems(), $cartItemModel, $customerGroupId
                        );
                } else {
                    $creditAmount =
                        $this->calculateCreditToCart($coupon, $cart->getItems(), $cartItemModel, $customerGroupId);
                }
                break;
    
            // gift coupons are invalidated earlier
            case self::SG_COUPON_TYPE_GIFT:
            default :
                break;
        }
        
        $sgCoupon->setAmount(null);
        $sgCoupon->setAmountNet(null);
        $sgCoupon->setAmountGross($creditAmount);
        $sgCoupon->setName($coupon['coupon_name']);
        $sgCoupon->setCode($coupon['coupon_code']);
        $sgCoupon->setCurrency($this->currencyCode);
        $sgCoupon->setDescription($coupon['coupon_description']);
    }

    /**
     * include the language file to the shop module ot_coupon
     */
    private function initializeCouponModule()
    {
        if (!class_exists("ot_coupon")) {
            $couponModuleFile = DIR_FS_LANGUAGES . $this->language . '/modules/order_total/ot_coupon.php';
            if (file_exists($couponModuleFile)) {
                require_once($couponModuleFile);
            }
        }
    }

    /**
     * check redeem amount to a coupon
     *
     * @param array $coupon_result
     *
     * @return bool
     */
    private function checkCouponRedeemAmount($coupon_result)
    {
        $coupon_count = xtc_db_query(
            "SELECT coupon_id FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . $coupon_result['coupon_id']
            . "'"
        );

        return (xtc_db_num_rows($coupon_count) >= $coupon_result['uses_per_coupon']
            && $coupon_result['uses_per_coupon'] > 0) ? false : true;
    }

    /**
     * check redeem amount to customer for a coupon
     *
     * @param array $coupon_result
     * @param int   $customerId
     *
     * @return bool
     */
    private function checkCouponRedeemAmountToCustomer($coupon_result, $customerId)
    {
        $coupon_count_customer = xtc_db_query(
            "SELECT coupon_id FROM " . TABLE_COUPON_REDEEM_TRACK . " WHERE coupon_id = '" . $coupon_result['coupon_id']
            . "' AND customer_id = '" . (int)$customerId . "'"
        );

        return (xtc_db_num_rows($coupon_count_customer) >= $coupon_result['uses_per_user']
            && $coupon_result['uses_per_user'] > 0) ? false : true;
    }

    /**
     * check if a product is in the cart which a coupon points to
     *
     * @param array                 $coupon
     * @param ShopgateOrderItem[]   $items
     * @param ShopgateItemCartModel $cartItemModel
     *
     * @return bool
     */
    private function cartHasRestrictedProduct(array $coupon, $items, ShopgateItemCartModel $cartItemModel)
    {
        $ids = explode(",", $coupon['restrict_to_products']);
        foreach ($items as $cartItem) {
            $id = $cartItemModel->getProductIdFromCartItem($cartItem);
            if (in_array($id, $ids)) {
                return true;
            }
        }

        return false;
    }

    /**
     * check if a product is in the cart which a coupon points to the products category
     *
     * @param array                 $coupon
     * @param ShopgateOrderItem[]   $items
     *
     * @return bool
     */
    private function cartHasRestrictedProductToCategory(array $coupon, $items)
    {
        $categoryIds = explode(",", $coupon['restrict_to_categories']);
        foreach ($items AS $item) {
            $categoryPath            = xtc_get_product_path(xtc_get_prid($item->getItemNumber()));
            $productCategoryIdsArray = explode("_", $categoryPath);
            
            $intersectingSet = array_intersect($productCategoryIdsArray, $categoryIds);
            if (!empty($intersectingSet)) {
                return true;
            }
        }

        return false;
    }

    /**
     * proceed the welcome voucher. This voucher will be disabled after one use
     *
     * @param array $coupon
     * @param int   $customerId
     */
    private function proceedWelcomeVoucher(array $coupon, $customerId)
    {
        $couponAmount          = $coupon['coupon_amount'];
        $gvCustomerAmount      = $this->getCustomerGvAmount($customerId);
        $totalGvCustomerAmount = $couponAmount + $gvCustomerAmount;

        $this->setCouponInactive($coupon['coupon_id']);
        $this->insertRedeemInformation($coupon['coupon_id'], $customerId);

        if ($gvCustomerAmount > 0) {
            // already has gv_amount so update
            $this->updateCustomerGvAmount($totalGvCustomerAmount, $customerId);
        } else {
            // no gv_amount so insert
            $this->insertCustomersGvAmount($totalGvCustomerAmount, $customerId);
        }
    }

    /**
     * read existing customers gv amount from database by customer's id
     *
     * @param int $customerId
     *
     * @return float
     */
    private function getCustomerGvAmount($customerId)
    {
        $customerAmountResult = xtc_db_fetch_array(
            xtc_db_query(
                "SELECT amount FROM " . TABLE_COUPON_GV_CUSTOMER . " WHERE customer_id = '" . $customerId . "'"
            )
        );
    
        $customerAmount = 0.0;
        if (!empty($customerAmountResult['amount'])) {
            $customerAmount = $customerAmountResult['amount'];
        }

        return (float)$customerAmount;
    }

    /**
     * set coupon as inactive in the database
     *
     * @param int $couponId
     */
    private function setCouponInactive($couponId)
    {
        xtc_db_query("UPDATE " . TABLE_COUPONS . " SET coupon_active = 'N' WHERE coupon_id = '" . $couponId . "'");
    }

    /**
     * insert redeem information into the database
     *
     * @param int $couponId
     * @param int $customerId
     */
    private function insertRedeemInformation($couponId, $customerId)
    {
        global $REMOTE_ADDR;
        xtc_db_query(
            "INSERT INTO  " . TABLE_COUPON_REDEEM_TRACK
            . " (coupon_id, customer_id, redeem_date, redeem_ip) VALUES ('"
            . $couponId . "', '" . $customerId . "', now(),'" . $REMOTE_ADDR . "')"
        );
    }

    /**
     * updates a customers gift value amount into the database by customer's id
     *
     * @param float        $totalGvAmount
     * @param int          $customerId
     */
    private function updateCustomerGvAmount($totalGvAmount, $customerId)
    {
        xtc_db_query(
            "UPDATE " . TABLE_COUPON_GV_CUSTOMER . " SET amount = '" . $totalGvAmount
            . "' WHERE customer_id = '" . $customerId . "'"
        );
    }

    /**
     * stores a customers gift value amount into the database by customer's id
     *
     * @param float $totalGvAmount
     * @param int   $customerId
     */
    private function insertCustomersGvAmount($totalGvAmount, $customerId)
    {
        xtc_db_query(
            "INSERT INTO " . TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) VALUES ('"
            . $customerId . "', '" . $totalGvAmount . "')"
        );
    }

    /**
     * calculate the coupon amount to every product the cart contains
     *
     * @param array                 $coupon
     * @param ShopgateOrderItem[]   $items
     * @param ShopgateItemCartModel $cartItemModel
     * @param int                   $customerGroupId
     *
     * @return float
     */
    private function calculateCreditToCart(array $coupon, $items, ShopgateItemCartModel $cartItemModel, $customerGroupId
    ) {
        $creditAmount = 0;
        foreach ($items as $item) {
            $id = $cartItemModel->getProductIdFromCartItem($item);
            $creditAmount += $this->calculateCouponAmount(
                $coupon, $this->getCartItemAmount($item, $id, $customerGroupId), $item->getQuantity()
            );
        }

        return (float)$creditAmount;
    }

    /**
     * @param array $coupon
     * @param float $itemAmount
     * @param int   $quantity
     *
     * @return float
     */
    private function calculateCouponAmount(array $coupon, $itemAmount, $quantity)
    {
        if ($coupon['coupon_type'] == self::SG_COUPON_TYPE_PERCENTAGE) {
            $itemAmount *= $quantity;
            return (float)$itemAmount * ($coupon['coupon_amount'] / 100);
        } else {
            return (float)$creditAmount = $coupon['coupon_amount'];
        }
    }

    /**
     * get the amount to a cart item depending on the tax rate
     *
     * @param ShopgateOrderItem $item
     * @param int               $itemUid
     * @param int               $customerGroupId
     *
     * @return float
     */
    private function getCartItemAmount(ShopgateOrderItem $item, $itemUid, $customerGroupId)
    {
        $orderItemTaxClassId = xtc_get_tax_class_id($itemUid);
        $xtcPrice            = new xtcPrice($this->currencyCode, $customerGroupId);
        $priceWithTax        = $xtcPrice->xtcGetPrice(
            $itemUid,
            false,
            $item->getQuantity(),
            $orderItemTaxClassId,
            $item->getUnitAmount(),
            1
        );

        return (float)$priceWithTax;
    }

    /**
     * calculate the coupon amount to all restricted products the cart contains
     *
     * @param array                 $coupon
     * @param ShopgateOrderItem[]   $items
     * @param ShopgateItemCartModel $cartItemModel
     * @param int                   $customerGroupId
     *
     * @return float
     */
    private function calculateCreditToRestrictedProducts(
        array $coupon, $items, ShopgateItemCartModel $cartItemModel, $customerGroupId
    ) {
        $creditAmount = 0;
        $productIds   = explode(",", $coupon['restrict_to_products']);
        foreach ($items as $item) {
            $pid = $cartItemModel->getProductIdFromCartItem($item);
            if (in_array($pid, $productIds)) {
                $creditAmount +=
                    $this->calculateCouponAmount(
                        $coupon, $this->getCartItemAmount($item, $pid, $customerGroupId), $item->getQuantity()
                    );
            }
        }
    
        return (float)$creditAmount;
    }

    /**
     * calculate the coupon amount to all restricted categories which point to products, the cart contains
     *
     * attention: need to have a look if a product has tax or not.
     *
     * @param array                 $coupon
     * @param ShopgateOrderItem[]   $items
     * @param ShopgateItemCartModel $cartItemModel
     * @param int                   $customerGroupId
     *
     * @return float
     */
    private function calculateCreditToRestrictedProductsToCategory(
        array $coupon, $items, ShopgateItemCartModel $cartItemModel, $customerGroupId
    ) {
        $creditAmount = 0;
        $categoryIds  = explode(",", $coupon['restrict_to_categories']);
        foreach ($items AS $item) {
            $id                      = $cartItemModel->getProductIdFromCartItem($item);
            $categoryPath            = xtc_get_product_path(xtc_get_prid($id));
            
            $productCategoryIdsArray = explode("_", $categoryPath);
            $intersectingSet = array_intersect($productCategoryIdsArray, $categoryIds);
            
            if (!empty($intersectingSet)) {
                $creditAmount +=
                    $this->calculateCouponAmount(
                        $coupon, $this->getCartItemAmount($item, $id, $customerGroupId),
                        $item->getQuantity()
                    );
            }
        }

        return (float)$creditAmount;
    }
}
