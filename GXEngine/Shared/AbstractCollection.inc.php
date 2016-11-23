<?php
/* --------------------------------------------------------------
   AbstractCollection.inc.php 2015-01-24 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class AbstractCollection
 *
 * @category System
 * @package  Shared
 */
abstract class AbstractCollection implements IteratorAggregate, \Countable
{
	/**
	 * Content Collection
	 *
	 * @var array
	 */
	protected $collectionContentArray = array();


	/**
	 * Initialize the collection instance.
	 *
	 * @throws InvalidArgumentException
	 *
	 * @param array|mixed|null $argumentsArray
	 */
	public function __construct($argumentsArray = null)
	{
		$argsArray = func_get_args();

		if(is_array($argumentsArray) && count($argsArray) === 1)
		{
			$argsArray = $argumentsArray;
		}

		foreach($argsArray as $argsItem)
		{
			try
			{
				$this->_add($argsItem);
			}
			catch(InvalidArgumentException $e)
			{
				throw $e;
			}
		}
	}


	/**
	 * Get collection item count.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->collectionContentArray);
	}


	/**
	 * Get the collection as an array.
	 *
	 * @return array
	 */
	public function getArray()
	{
		return $this->collectionContentArray;
	}


	/**
	 * Get specific collection item by index.
	 *
	 * @throws InvalidArgumentException if index is not numeric | OutOfBoundsException if index is out of bounds
	 *
	 * @param $p_index
	 *
	 * @return mixed
	 */
	public function getItem($p_index)
	{
		if(is_numeric($p_index) == false)
		{
			throw new InvalidArgumentException('Given $p_index not numeric');
		}

		if($p_index < 0 || $p_index >= sizeof($this->collectionContentArray))
		{
			throw new OutOfBoundsException('$p_index is out of bounds');
		}

		return $this->collectionContentArray[$p_index];
	}


	/**
	 * Determine whether the collection is empty or not.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return 0 === count($this->collectionContentArray);
	}


	/**
	 * Add a new item.
	 * This method must be used by child-collection classes.
	 *
	 * @param mixed $item Item which should add to the collection
	 *
	 * @throws \InvalidArgumentException When $item has an invalid type.
	 */
	protected function _add($item)
	{
		if($this->_itemIsValid($item) === false)
		{
			$exceptionText = $this->_getExceptionText();
			throw new InvalidArgumentException($exceptionText);
		}
		else
		{
			$this->collectionContentArray[] = $item;
		}
	}


	/**
	 * Check if a new item has the valid collection type.
	 *
	 * @param mixed $dataItem
	 *
	 * @return bool
	 */
	protected function _itemIsValid($dataItem)
	{
		// The first condition checks if the $dataItem is a string AND the valid type is equal to string.
		// The second checks if $dataItem is an object and match the expected type,
		// If one condition is true, the method return true, otherwise false.
		return ((is_string($dataItem) && strtolower($this->_getValidType()) === 'string')
		        || (is_object($dataItem)
		            && is_a($dataItem,
		                    $this->_getValidType())));
	}


	/**
	 * Get exception text.
	 *
	 * @return string
	 */
	protected function _getExceptionText()
	{
		return 'Given item has invalid type ('. $this->_getValidType() .' needed)';
	}
	
	
	public function getIterator()
	{
		return new ArrayIterator($this->collectionContentArray);
	}


	/**
	 * Get valid type.
	 *
	 * This method must be implemented in the child-collection classes.
	 *
	 * @return string
	 */
	abstract protected function _getValidType();
}
 