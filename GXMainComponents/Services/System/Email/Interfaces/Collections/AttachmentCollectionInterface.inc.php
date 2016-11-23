<?php
/* --------------------------------------------------------------
   AttachmentCollectionInterface.inc.php 2015-02-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AttachmentCollectionInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface AttachmentCollectionInterface
{
	/**
	 * Adds a new attachment to the collection.
	 *
	 * @param EmailAttachmentInterface $attachment E-Mail attachment.
	 */
	public function add(EmailAttachmentInterface $attachment);


	/**
	 * Removes an attachment from collection.
	 *
	 * @param EmailAttachmentInterface $attachment E-Mail attachment.
	 */
	public function remove(EmailAttachmentInterface $attachment);


	/**
	 * Removes all attachments of collection.
	 */
	public function clear();
}