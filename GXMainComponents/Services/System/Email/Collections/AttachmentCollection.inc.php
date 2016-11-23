<?php
/* --------------------------------------------------------------
   AttachmentCollection.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');
MainFactory::load_class('AttachmentCollectionInterface');

/**
 * Class AttachmentCollection
 *
 * Handles the attachment collections of an Email.
 *
 * @category   System
 * @package    Email
 * @subpackage Collections
 */
class AttachmentCollection extends AbstractCollection implements AttachmentCollectionInterface
{
	/**
	 * Adds a new attachment to the collection.
	 *
	 * @param EmailAttachmentInterface $attachment E-Mail attachment.
	 */
	public function add(EmailAttachmentInterface $attachment)
	{
		$this->_add($attachment);
	}


	/**
	 * Removes an attachment from collection.
	 *
	 * @param EmailAttachmentInterface $attachment E-Mail attachment.
	 *
	 * @throws Exception If attachment could not be found.
	 */
	public function remove(EmailAttachmentInterface $attachment)
	{
		$index = array_search($attachment, $this->collectionContentArray);

		if($index === false)
		{
			throw new Exception('Could not remove attachment because it does not exist in collection.');
		}

		unset($this->collectionContentArray[$index]);
	}


	/**
	 * Removes all attachments of collection.
	 */
	public function clear()
	{
		$this->collectionContentArray = array();
	}


	/**
	 * Returns the type of the collection items.
	 *
	 * @return string Valid type.
	 */
	protected function _getValidType()
	{
		return 'EmailAttachmentInterface';
	}
}