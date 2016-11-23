<?php
/* --------------------------------------------------------------
   AttachmentPath.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AttachmentPathInterface');

/**
 * Class AttachmentPath
 *
 * Important: This value object is not going to check if the attachment file exists in the
 * server because that would cause problems with the service usage (e.g. if an attachment
 * file is missing an exception would halt all the email service operations).
 *
 * @category   System
 * @package    Email
 * @subpackage ValueObjects
 */
class AttachmentPath implements AttachmentPathInterface
{
	/**
	 * Email attachment path.
	 * @var string
	 */
	protected $path;


	/**
	 * Constructor
	 *
	 * Executes the validation checks for the email attachment.
	 *
	 * @throws InvalidArgumentException If the provided argument is not a string or empty.
	 *
	 * @param string $p_path E-Mail attachment path.
	 */
	public function __construct($p_path)
	{
		if(!is_string($p_path) || empty($p_path))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected string path) $p_emailAttachment: '
			                                   . print_r($p_path, true));
		}

		$this->path = $p_path;
	}


	/**
	 * Returns attachment path.
	 *
	 * @return string Equivalent string.
	 */
	public function __toString()
	{
		return $this->path;
	}
}