<?php
/* --------------------------------------------------------------
   SampleAdminOrderStatusMailExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleAdminOrderStatusMailExtender
 *
 * This is a sample overload for the AdminOrderStatusMailExtenderComponent.
 *
 * @see AdminOrderStatusMailExtenderComponent
 */
class SampleAdminOrderStatusMailExtender extends SampleAdminOrderStatusMailExtender_parent
{
    /**
     * Overloaded "proceed" method.
     */
    public function proceed()
    {
        $this->v_output_buffer['lorem_ipsum'] = 'Lorem ipsum dolor sit amet.';
        
        parent::proceed();
    }
}