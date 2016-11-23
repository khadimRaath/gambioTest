<?php
/* --------------------------------------------------------------
   SampleAdminOrderOverviewExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleAdminOrderOverviewExtender
 * 
 * This is a sample overload for the AdminOrderOverviewExtenderComponent.
 * 
 * @see AdminOrderOverviewExtenderComponent
 */
class SampleAdminOrderOverviewExtender extends SampleAdminOrderOverviewExtender_parent
{
    /**
     * Overloaded "proceed" method. 
     */
    public function proceed()
    {
        parent::proceed();
    
        $href = xtc_href_link('gm_send_order.php', 'oID=0&type=recreate_order'); 
        $this->v_output_buffer['single_action'] = '<a href="' . $href . '" target="_blank">TEST</a>';
        
        $href = xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')) . 'oID=0&action=delete');
        $this->v_output_buffer['multi_action'] =
            '<a data-gx-compatibility="orders/orders_modal_layer" data-orders_modal_layer-action="multi_delete" 
                href="' . $href . '">MULTI-TEST</a>';
    }
}