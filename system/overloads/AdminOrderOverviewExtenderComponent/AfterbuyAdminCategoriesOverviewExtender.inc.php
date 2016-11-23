<?php
/* --------------------------------------------------------------
   AfterbuyAdminCategoriesOverviewExtender.inc.php 2016-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AfterbuyAdminCategoriesOverviewExtender
 *
 * @see AdminOrderOverviewExtenderComponent
 */
class AfterbuyAdminCategoriesOverviewExtender extends AfterbuyAdminCategoriesOverviewExtender_parent
{
    /**
     * Overloaded "proceed" method.
     */
    public function proceed()
    {
        parent::proceed();
        if(AFTERBUY_ACTIVATED == 'true') {
            $this->v_output_buffer['single_action'] = '<a href="'.
                xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array ('oID', 'action')).
                'oID=0&action=afterbuy_send').'">' . BUTTON_AFTERBUY_SEND . '</a>';
        }
    }
}