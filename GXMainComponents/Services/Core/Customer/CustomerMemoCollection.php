<?php

/* --------------------------------------------------------------
   CustomerMemoCollection.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerMemoCollection
 * 
 * @category   System
 * @package    Customer
 * @subpackage Validation
 */
class CustomerMemoCollection extends AbstractCollection
{
	/**
	 * Get valid type (CustomerMemo).
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'CustomerMemo';
	}
	

	/**
	 * Returns a serialized array of the customers collection.
	 * The structure is:
	 *  $memosArray = [
	 *      [
	 *          'customerId' => $customerId,
	 *          'memoTitle'  => $memoTitle,
	 *          'memoText'   => $memoText,
	 *          'memoDate'   => $memoDate',
	 *          'posterId'   => $posterId
	 *      ], |…|…|
	 *  ]
	 *
	 * @return array Array with the structure above.
	 */
	public function getSerializedArray()
	{
		$memosArray = [];
		foreach($this->collectionContentArray as $item)
		{
			/** @var CustomerMemo $item */
			$memo               = [];
			$memo['customerId'] = $item->getCustomerId();
			$memo['memoTitle']  = $item->getTitle();
			$memo['memoText']   = $item->getText();
			$memo['memoDate']   = $item->getCreationDate()->format('Y-m-d H:i:s');
			$memo['posterId']   = $item->getPosterId();

			$memosArray[] = $memo;
		}

		return $memosArray;
	}
}