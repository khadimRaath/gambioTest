<?php
/* --------------------------------------------------------------
   OrderTaxCheckoutSuccessExtender.inc.php 2015-06-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * generate and save order's tax data for statistics
 * 
 * Class OrderTaxCheckoutSuccessExtender
 */
class OrderTaxCheckoutSuccessExtender extends OrderTaxCheckoutSuccessExtender_parent
{
	public function proceed()
	{
		parent::proceed();

		/* @var OrderTaxInformation $orderTaxInformation */
		$orderTaxInformation = MainFactory::create_object('OrderTaxInformation');
		$orderTaxInformation->saveUnsavedTaxInformation($this->v_data_array['orders_id']);
	}
} 