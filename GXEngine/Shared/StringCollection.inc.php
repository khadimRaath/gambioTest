<?php

/* --------------------------------------------------------------
   StringCollection.inc.php 2016-01-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class StringCollection
 *
 * @category System
 * @package  Shared
 */
class StringCollection extends AbstractCollection
{
	
	/**
	 * Get valid item type.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'StringType';
	}


	/**
	 * Convert the items to strings and return all items as an array.
	 *
	 * @return array Collection content
	 */
	public function getStringArray()
	{
		$collectionContentAsStringArray = array();

		foreach($this->collectionContentArray as $item)
		{
			$collectionContentAsStringArray[] = $item->asString();
		}

		return $collectionContentAsStringArray;
	}
}