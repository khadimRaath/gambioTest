<?php
/* --------------------------------------------------------------
   EmailAttachmentInterface.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailAttachmentInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailAttachmentInterface
{
	/**
	 * Sets the path of an email attachment.
	 *
	 * @param AttachmentPathInterface $path Attachment path.
	 */
	public function setPath(AttachmentPathInterface $path);


	/**
	 * Returns the path of an email attachment.
	 *
	 * @param bool $absolutePath (optional) Whether to return the absolute path or the relative one.
	 *
	 * @return AttachmentPathInterface Attachment path.
	 */
	public function getPath($absolutePath = true);


	/**
	 * Sets the name of an email attachment.
	 *
	 * @param AttachmentNameInterface $name Attachment name.
	 */
	public function setName(AttachmentNameInterface $name);


	/**
	 * Returns the name of an email attachment.
	 *
	 * @return AttachmentNameInterface Attachment name.
	 */
	public function getName();
}