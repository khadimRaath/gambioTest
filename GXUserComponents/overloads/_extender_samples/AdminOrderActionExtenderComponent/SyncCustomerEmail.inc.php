<?php
/* --------------------------------------------------------------
   SyncCustomerEmail.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SyncCustomerEmail
 *
 * This is a sample overload for the AdminOrderActionExtenderComponent.
 *
 * @see AdminOrderActionExtenderComponent
 */
class SyncCustomerEmail extends SyncCustomerEmail_parent
{
    /**
     * Overloaded "proceed" method.
     */
    public function proceed()
    {
        switch($this->v_data_array['action'])
        {
            case 'sync_email_address':
                $this->_syncEmailAddress(new IdType((int)$this->v_data_array['GET']['oID']));
                break;
        }

        parent::proceed();
    }

    /**
     * Replaces the email address that is stored in the order with the current email address of the customer
     * 
     * @param IdType $orderId The ID of the order
     */
    private function _syncEmailAddress(IdType $orderId)
    {
        $orderReadService = StaticGXCoreLoader::getService('OrderRead');
        $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
        $customerReadService = StaticGXCoreLoader::getService('CustomerRead');
        
        $order = $orderReadService->getOrderById($orderId);
        $customerId = new IdType($order->getCustomerId());
        $customer = $customerReadService->getCustomerById($customerId);
        $actualEmail = $customer->getEmail();

        $order->setCustomerEmail(new EmailStringType((string)$actualEmail));
        $orderWriteService->updateOrder($order);
    }
}