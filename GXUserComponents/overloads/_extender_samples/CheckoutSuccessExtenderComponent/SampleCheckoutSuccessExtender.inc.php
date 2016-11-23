<?php
/* --------------------------------------------------------------
   SampleCheckoutSuccessExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleCheckoutSuccessExtender
 *
 * This is a sample overload for the CheckoutSuccessExtenderComponent.
 *
 * @see CheckoutSuccessExtenderComponent
 */
class SampleCheckoutSuccessExtender extends SampleCheckoutSuccessExtender_parent
{
    /**
     * Overloaded "proceed" method.
     */
    public function proceed()
    {
        parent::proceed();

        $this->_someLogic(new IdType($this->v_data_array['orders_id']));

        $this->html_output_array[] = '<b>My own success message.</b>';
    }
    
    private function _someLogic(IdType $orderId)
    {
        // Some logic
    }
}