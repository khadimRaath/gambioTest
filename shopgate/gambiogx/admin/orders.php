<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */



(defined('_VALID_XTC') || defined('_GM_VALID_CALL')) or die('Direct Access to this location is not allowed.');

include_once DIR_FS_CATALOG . '/shopgate/shopgate_library/shopgate.php';
include_once DIR_FS_CATALOG . '/shopgate/plugin.php';

/**
 * Wrapper for setShopgateOrderlistStatus() with only one order.
 *
 * For compatibility reasons.
 *
 * @param int $orderId The ID of the order in the shop system.
 * @param int $status  The ID of the order status that has been set in the shopping system.
 */
function setShopgateOrderStatus($orderId, $status)
{
    if (empty($orderId)) {
        return;
    }
    
    setShopgateOrderlistStatus(array($orderId), $status);
}

/**
 * Wrapper for ShopgatePluginGambioGX::updateOrdersStatus(). Set the shipping status for a list of order IDs.
 *
 * @param int[] $orderIds The IDs of the orders in the shop system.
 * @param int   $status   The ID of the order status that has been set in the shopping system.
 */
function setShopgateOrderlistStatus($orderIds, $status)
{
    if (empty($orderIds) || !is_array($orderIds)) {
        return;
    }
    
    $plugin = new ShopgatePluginGambioGX();
    $plugin->updateOrdersStatus($orderIds, $status);
}
