<?php

/**
 * Class EkomiIntegrationCheckoutSuccessExtender
 *
 * This is a EkomiIntegration overload for the CheckoutSuccessExtenderComponent.
 * 
 * @see AdminOrderOverviewExtenderComponent
 * 
 * @extends    EkomiIntegrationCheckoutSuccessExtender_parent
 * @category   System
 * @package    Modules
 * @author     Sandor Barics <sbarics@ekomi.de>
 */
class EkomiIntegrationCheckoutSuccessExtender extends EkomiIntegrationCheckoutSuccessExtender_parent {

    /**
     * Overloaded "proceed" method. 
     */
    function proceed() {

        parent::proceed();

        if (gm_get_conf('MODULE_CENTER_EKOMIINTEGRATION_INSTALLED') == '1') {

            $ekomiIntegrationManager = MainFactory::create_object('EkomiIntegrationManager', array(gm_get_conf('EKOMIINTEGRATION_SHOPID'), gm_get_conf('EKOMIINTEGRATION_SHOPPWD')));

            if (isset($this->v_data_array['orders_id']) && !empty($this->v_data_array['orders_id'])) {
                $ekomiIntegrationManager->addRecipient($this->v_data_array['orders_id']);
            }
        }
    }

}
