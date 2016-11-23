<?php
/* --------------------------------------------------------------
   EmailCollection.inc.php 2015-02-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');
MainFactory::load_class('EmailCollectionInterface');

/**
 * Class EmailCollection
 *
 * Used by operations that consider multiple email objects.
 *
 * @category   System
 * @package    Email
 * @subpackage Collections
 */
class EmailCollection extends AbstractCollection implements EmailCollectionInterface
{
	/**
	 * Adds a new email to the collection.
	 *
	 * @param EmailInterface $email E-Mail.
	 */
	public function add(EmailInterface $email)
	{
		$this->_add($email);
	}


	/**
	 * Removes an email from collection.
	 *
	 * @param EmailInterface $email E-Mail.
	 *
	 * @throws Exception If email cannot be found.
	 */
	public function remove(EmailInterface $email)
	{
		$index = array_search($email, $this->collectionContentArray);

		if($index === false)
		{
			throw new Exception('Could not remove email because it does not exist in collection.');
		}

		unset($this->collectionContentArray[$index]);
	}


	/**
	 * Removes all emails of collection.
	 */
	public function clear()
	{
		$this->collectionContentArray = array();
	}


	/**
	 * Returns the type of te collection items.
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'EmailInterface';
	}
}