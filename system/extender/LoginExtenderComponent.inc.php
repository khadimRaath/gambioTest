<?php
/* --------------------------------------------------------------
  LoginExtenderComponent.inc.php 2011-12-01 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2011 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

class LoginExtenderComponent extends ExtenderComponent
{
	function get_customer()
	{
		$coo_output_object = false;
		if(isset($this->v_data_array['customers_id']) && !empty($this->v_data_array['customers_id']))
		{
			$coo_output_object = MainFactory::create_object('GMDataObject', array('customers', array('customers_id' => $this->v_data_array['customers_id'])));
		}
		return $coo_output_object;
	}

	function proceed()
	{
		parent::proceed();
		
		$coo_customers_data_object = $this->get_customer();
		$this->set_data('coo_customers_data_object', $coo_customers_data_object);		
	}
}
?>