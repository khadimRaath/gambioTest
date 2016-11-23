<?php

/**
 * Class EkomiIntegrationAdminOrderOverviewExtender
 *
 * This is a EkomiIntegration overload for the AdminOrderOverviewExtenderComponent.
 * 
 * @see AdminOrderOverviewExtenderComponent
 * 
 * @extends    EkomiIntegrationAdminOrderOverviewExtender_parent
 * @category   System
 * @package    Modules
 * @author     Sandor Barics <sbarics@ekomi.de>
 */
class EkomiIntegrationAdminOrderOverviewExtender extends EkomiIntegrationAdminOrderOverviewExtender_parent {

    /**
     * Overloaded "proceed" method. 
     */
    public function proceed() {
        parent::proceed();

        if ($this->v_data_array['GET']['action'] == 'gm_multi_status') {
            if (gm_get_conf('MODULE_CENTER_EKOMIINTEGRATION_INSTALLED') == '1') {
                $newOrderStatus = $this->v_data_array['POST']['gm_status'];
                $orderIDs = $this->v_data_array['POST']['gm_multi_status'];

                if (!empty($orderIDs)) {
                    foreach ($orderIDs as $key => $orderId) {
                        $ekomiIntegrationManager = MainFactory::create_object('EkomiIntegrationManager', array(gm_get_conf('EKOMIINTEGRATION_SHOPID'), gm_get_conf('EKOMIINTEGRATION_SHOPPWD')));

                        $ekomiIntegrationManager->addRecipient($orderId, $newOrderStatus);
                    }
                }
            }
        }
    }

}
