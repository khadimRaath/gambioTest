<?php
/* --------------------------------------------------------------
   EditableKeyValueCollection.inc.php 2015-12-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('KeyValueCollection');

/**
 * Class EditableKeyValueCollection
 *
 * @category System
 * @package  Shared
 */
class EditableKeyValueCollection extends KeyValueCollection
{
	/**
	 * Set new key-value pair.
	 *
	 * @param string $p_keyName
	 * @param mixed  $p_value
	 */
	public function setValue($p_keyName, $p_value)
	{
		$this->collectionContentArray[$p_keyName] = $p_value;
	}


	/**
	 * Add another collection to this one.
	 *
	 * @param \KeyValueCollection $collection Collection to add.
	 *
	 * @return $this Same instance to make chained method calls possible.
	 */
	public function addCollection(KeyValueCollection $collection)
	{
		$keyValueArray = $collection->getArray();

		$this->_addToCollectionContentArray($keyValueArray);

		return $this;
	}


	/**
	 * Deletes an value from the collection by the given key.
	 *
	 * @param string $p_key Key of the value that should gets delete.
	 *
	 * @return $this Same instance to make chained method calls possible.
	 */
	public function deleteValue($p_key)
	{
		if($this->keyExists($p_key))
		{
			unset($this->collectionContentArray[$p_key]);
		}
	}


	/**
	 * Return a clone of the current editable key value collection instance.
	 */
	public function getClone()
	{
		return clone $this;
	}
}