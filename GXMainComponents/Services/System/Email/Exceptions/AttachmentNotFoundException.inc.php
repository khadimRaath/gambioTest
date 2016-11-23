<?php
/* --------------------------------------------------------------
   AttachmentNotFoundException.inc.php 2015-06-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AttachmentNotFoundException
 *
 * Is thrown whenever an email attachment file could not be found on the server.
 *
 * @category   System
 * @package    Email
 * @subpackage Exceptions
 */
class AttachmentNotFoundException extends Exception
{
	/**
	 * Attachment path.
	 * @var string
	 */
	protected $attachmentPath;


	/**
	 * Class Constructor
	 *
	 * @param string $message        (optional) Message of the exception instance.
	 * @param string $attachmentPath (optional) The attachment path that could not be found.
	 */
	public function __construct($message = '', $attachmentPath = '')
	{
		parent::__construct($message);
		$this->attachmentPath = $attachmentPath;
	}


	/**
	 * Get attachment path that could not be found.
	 *
	 * @return string Attachment path.
	 */
	public function getAttachmentPath()
	{
		return (string)$this->attachmentPath;
	}
}