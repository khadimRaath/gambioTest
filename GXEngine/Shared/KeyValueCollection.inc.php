<?php
/* --------------------------------------------------------------
   KeyValueCollection.inc.php 2015-12-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');


/**
 * Class KeyValueCollection
 *
 * @category System
 * @package  Shared
 */
class KeyValueCollection extends AbstractCollection
{
	/**
	 * Class Constructor
	 *
	 * @param array $keyValueArray
	 */
	public function __construct(array $keyValueArray)
	{
		$this->_addToCollectionContentArray($keyValueArray);
	}
	
	
	/**
	 * Get the value that corresponds to the provided key.
	 *
	 * @param string $p_keyName
	 *
	 * @return mixed
	 * @throws InvalidArgumentException
	 */
	public function getValue($p_keyName)
	{
		if(!$this->keyExists($p_keyName))
		{
			throw new InvalidArgumentException('Given keyName not found: ' . htmlentities($p_keyName));
		}
		
		return $this->collectionContentArray[$p_keyName];
	}
	
	
	/**
	 * Check if a given key exists within the collection.
	 *
	 * @param string $p_keyName
	 *
	 * @return bool
	 */
	public function keyExists($p_keyName)
	{
		return array_key_exists($p_keyName, $this->collectionContentArray);
	}
	
	
	/**
	 * Get valid item type.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'string';
	}
	
	
	/**
	 * Add the passed key value array to the collection content array.
	 *
	 * @param array $keyValueArray
	 */
	protected function _addToCollectionContentArray(array $keyValueArray)
	{
		foreach($keyValueArray as $itemKey => $itemValue)
		{
			$this->collectionContentArray[$itemKey] = $itemValue;
		}
	}
}
 