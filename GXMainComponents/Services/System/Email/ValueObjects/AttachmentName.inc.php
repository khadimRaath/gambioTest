<?php
/* --------------------------------------------------------------
   AttachmentName.php 2015-05-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AttachmentNameInterface');

/**
 * Class AttachmentName
 *
 * Important: This value object is not going to check if the attachment file exists in the
 * server because that would cause problems with the service usage (e.g. if an attachment
 * file is missing an exception would halt all the email service operations).
 *
 * @category   System
 * @package    Email
 * @subpackage ValueObjects
 */
class AttachmentName implements AttachmentNameInterface
{

	/**
	 * Email attachment name.
	 *
	 * @var string
	 */
	protected $name;


	/**
	 * Constructor
	 *
	 * Executes the validation checks for the email attachment.
	 *
	 * @param string $p_name E-Mail attachment name.
	 * @throws InvalidArgumentException On invalid argument.
	 */
	public function __construct($p_name)
	{
		if(!is_string($p_name))
		{
			throw new InvalidArgumentException('Invalid argument provided (expected string name) $p_emailAttachment: '
			                                   . print_r($p_name, true));
		}

		$this->name = $p_name;
	}


	/**
	 * Returns the attachment path.
	 *
	 * @return string Equivalent string.
	 */
	public function __toString()
	{
		return $this->name;
	}
}