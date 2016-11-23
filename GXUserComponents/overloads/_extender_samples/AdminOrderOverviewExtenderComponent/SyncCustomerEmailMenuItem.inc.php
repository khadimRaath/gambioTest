<?php
/* --------------------------------------------------------------
   SyncCustomerEmailMenuItem.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SyncCustomerEmailMenuItem
 *
 * This is a sample overload for the AdminOrderOverviewExtenderComponent.
 *
 * @see AdminOrderOverviewExtenderComponent
 */
class SyncCustomerEmailMenuItem extends SyncCustomerEmailMenuItem_parent
{
    /**
     * Overloaded "proceed" method.
     */
    public function proceed()
    {
        parent::proceed();

        $this->v_output_buffer['single_action'] = '<a href="' .
            xtc_href_link('orders.php', 'oID=0&action=sync_email_address') .
            '">Synchronize email address</a>';
    }
}