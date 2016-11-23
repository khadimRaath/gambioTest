<?php

/* --------------------------------------------------------------
   OrderTrackingInformationCollection.inc.php 2016-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderTrackingInformationCollection
 * 
 * @category   System
 * @package    Order
 * @subpackage Collections
 */
class OrderTrackingInformationCollection extends AbstractCollection
{
	/**
	 * Get valid type.
	 *
	 * This method must be implemented in the child-collection classes.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'OrderTrackingInformation';
	}
	
	
	/**
	 * Get collection as serialized array. 
	 * 
	 * Each entry contains the "company", "number" and "link" values.
	 * 
	 * @return array
	 */
	public function getSerializedArray()
	{
		$result = [];
		
		/** @var OrderTrackingInformation $orderTrackingInformation */
		foreach($this->collectionContentArray as $orderTrackingInformation)
		{
			$result[] = [
				'company' => $orderTrackingInformation->getCompany(),
				'number'  => $orderTrackingInformation->getNumber(),
				'link'    => $orderTrackingInformation->getLink()
			];
		}
		
		return $result; 
	}
}