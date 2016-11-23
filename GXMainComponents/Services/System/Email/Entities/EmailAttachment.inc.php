<?php
/* --------------------------------------------------------------
   EmailAttachment.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailAttachmentInterface');

/**
 * Class EmailAttachment
 *
 * Email attachment will serve as the path to the file that needs to be attached
 * (located on the server).
 *
 * @category   System
 * @package    Email
 * @subpackage Entities
 */
class EmailAttachment implements EmailAttachmentInterface
{
	/**
	 * E-Mail attachment path.
	 * @var string
	 */
	protected $path;
	
	/**
	 * E-Mail attachment name.
	 * @var string
	 */
	protected $name;
	
	
	/**
	 * Constructor
	 *
	 * Entity representing an email attachment.
	 *
	 * @param AttachmentPathInterface $path (optional) Attachment path.
	 * @param AttachmentNameInterface $name (optional) Attachment name.
	 */
	public function __construct(AttachmentPathInterface $path = null, AttachmentNameInterface $name = null)
	{
		$this->path = $this->_convertToRelativePath($path);
		$this->name = (string)$name;
	}
	
	
	/**
	 * Sets the path of an email attachment.
	 *
	 * @param AttachmentPathInterface $path Attachment path.
	 */
	public function setPath(AttachmentPathInterface $path)
	{
		$this->path = $this->_convertToRelativePath($path);
	}
	
	
	/**
	 * Returns the path of an email attachment.
	 *
	 * @param bool $absolutePath (optional) Whether to return the absolute path or the relative one.
	 * 
	 * @return AttachmentPathInterface Attachment path.
	 */
	public function getPath($absolutePath = true)
	{
		$path = ($absolutePath) ? DIR_FS_CATALOG . $this->path : $this->path;
		
		return ($path !== DIR_FS_CATALOG) ? MainFactory::create('AttachmentPath', $path) : null;
	}
	
	
	/**
	 * Sets the name of an email attachment.
	 *
	 * @param AttachmentNameInterface $name Attachment name.
	 */
	public function setName(AttachmentNameInterface $name)
	{
		$this->name = (string)$name;
	}
	
	
	/**
	 * Returns the name of an email attachment.
	 *
	 * @return AttachmentNameInterface Attachment name.
	 */
	public function getName()
	{
		return MainFactory::create('AttachmentName', $this->name);
	}
	
	
	/**
	 * Convert a path to relative.
	 *
	 * Due to different server setups this process can be tedious and hard to foresee. The
	 * following method contains the conversion logic and must be used in any setter of the class.
	 *
	 * @param AttachmentPathInterface $path Attachment path.
	 *
	 * @return string Returns the converted path.
	 */
	protected function _convertToRelativePath(AttachmentPathInterface $path = null)
	{
		if(DIR_FS_CATALOG === '/' && substr((string)$path, 0, 1) === '/')
		{
			// Remove the initial slash.
			$relativePath = substr((string)$path, 1);
		}
		else if(DIR_FS_CATALOG !== '/')
		{
			// Remove the entire DIR_FS_CATALOG from the path.
			$relativePath = str_replace(DIR_FS_CATALOG, '', (string)$path);
		}
		else
		{
			// Path is already relative.
			$relativePath = (string)$path;
		}
		
		return $relativePath;
	}
}