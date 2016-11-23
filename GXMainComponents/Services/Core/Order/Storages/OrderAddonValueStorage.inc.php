<?php

/* --------------------------------------------------------------
   OrderAddonValueStorage.inc.php 2015-12-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractAddonValueStorage');

/**
 * Class OrderAddonValueStorage
 *
 * @category   System
 * @package    Order
 * @subpackage Storages
 */
class OrderAddonValueStorage extends AbstractAddonValueStorage
{
	/**
	 * Get the container class type.
	 *
	 * @return string
	 */
	protected function _getContainerType()
	{
		return 'OrderInterface';
	}
	
	
	/**
	 * Returns a multidimensional array with the primary key of the orders table and the required column names with the
	 * corresponding key used in the KeyValueCollection.
	 *
	 * @return array
	 */
	protected function _getExternalFieldsArray()
	{
		$externalFields                          = array();
		$externalFields['orders']['primary_key'] = 'orders_id';
		$externalFields['orders']['fields']      = array(
			'customers_ip'         => 'customerIp',
			'abandonment_download' => 'downloadAbandonmentStatus',
			'abandonment_service'  => 'serviceAbandonmentStatus',
			'cc_type'              => 'ccType',
			'cc_owner'             => 'ccOwner',
			'cc_number'            => 'ccNumber',
			'cc_expires'           => 'ccExpires',
			'cc_start'             => 'ccStart',
			'cc_issue'             => 'ccIssue',
			'cc_cvv'               => 'ccCvv'
		);
		
		return $externalFields;
	}
}