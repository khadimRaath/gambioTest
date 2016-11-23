<?php

/**
 * Class EkomiIntegrationOrderWriteService
 *
 * This is a EkomiIntegration overload for the OrderWriteService.
 * 
 * @see OrderWriteService
 * 
 * @extends    EkomiIntegrationOrderWriteService_parent
 * @category   System
 * @package    Modules
 * @author     Sandor Barics <sbarics@ekomi.de>
 */
class EkomiIntegrationOrderWriteService extends EkomiIntegrationOrderWriteService_parent {

    /**
     * Overloaded "updateOrderStatus" method. 
     */
    public function updateOrderStatus(IdType $orderId, IntType $newOrderStatusId, StringType $comment, BoolType $customerNotified) {

        if (gm_get_conf('MODULE_CENTER_EKOMIINTEGRATION_INSTALLED') == '1') {

            $ekomiIntegrationManager = MainFactory::create_object('EkomiIntegrationManager', array(gm_get_conf('EKOMIINTEGRATION_SHOPID'), gm_get_conf('EKOMIINTEGRATION_SHOPPWD')));

            $ekomiIntegrationManager->addRecipient($orderId);
        }
    }

}
