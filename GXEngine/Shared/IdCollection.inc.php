<?php
/* --------------------------------------------------------------
   IdCollection.inc.php 2016-07-06 
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');


/**
 * Class IdCollection
 * 
 * @category System
 * @package Shared
 */
class IdCollection extends AbstractCollection
{
	/**
	 * Get valid item type.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'IdType';
	}
	
	
	/**
	 * Convert the items to int and return all items as an array.
	 *
	 * @return array Collection content
	 */
	public function getIntArray()
	{
		$collectionContentAsIntArray = array();
		
		foreach($this->collectionContentArray as $item)
		{
			$collectionContentAsIntArray[] = $item->asInt();
		}
		
		return $collectionContentAsIntArray;
	}
}
 