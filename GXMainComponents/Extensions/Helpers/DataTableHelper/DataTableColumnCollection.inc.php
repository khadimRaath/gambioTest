<?php

/* --------------------------------------------------------------
   DataTableColumnCollection.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class DataTableColumnCollection
 * 
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
class DataTableColumnCollection extends AbstractCollection
{
	/**
	 * Find column definition by name. 
	 * 
	 * @param StringType $name The column slug name to be found. 
	 *
	 * @return DataTableColumn|null Returns the column instance of null if not found. 
	 */
	public function findByName(StringType $name)
	{
		/** @var DataTableColumn $dataTableColumn */
		foreach($this->collectionContentArray as $dataTableColumn)
		{
			if($dataTableColumn->getName() === $name->asString())
			{
				return $dataTableColumn;
			}
		}
		
		return null; 
	}
	
	
	/**
	 * Get valid type.
	 *
	 * This method must be implemented in the child-collection classes.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'DataTableColumn';
	}
}