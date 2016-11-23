<?php
/* --------------------------------------------------------------
  AdminOrderOverviewExtenderComponent.inc.php 2015-10-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

class AdminOrderOverviewExtenderComponent extends ExtenderComponent
{
    public function __construct() {}

    public function proceed()
    {
        parent::proceed();

        if(is_array($this->v_output_buffer) == false)
        {
            $this->v_output_buffer = array();
        }

        $this->v_output_buffer['single_action'] = '';
        $this->v_output_buffer['multi_action'] = '';
    }
}