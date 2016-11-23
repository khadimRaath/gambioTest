<?php
/* --------------------------------------------------------------
   ContactName.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('ContactNameInterface');

/**
 * Class ContactName
 *
 * Contact name will be the display name for an EmailContact object.
 *
 * @category   System
 * @package    Email
 * @subpackage ValueObjects
 */
class ContactName implements ContactNameInterface
{
	/**
	 * Maximum name length constant.
	 * @var int
	 */
	const MAX_LENGTH = 128;

	/**
	 * Contact name.
	 *
	 * @var string
	 */
	protected $contactName;


	/**
	 * Constructor
	 *
	 * Executes the validation checks upon the contact name.
	 *
	 * @throws InvalidArgumentException If the provided argument is not a string.
	 *
	 * @param string $p_contactName Contact name.
	 */
	public function __construct($p_contactName)
	{
		if(!is_string($p_contactName)) // contact name CAN be empty string
		{
			throw new InvalidArgumentException('Invalid argument provided (expected string name) $p_contactName: '
			                                   . print_r($p_contactName, true));
		}

		if(strlen(trim($p_contactName)) > self::MAX_LENGTH)
		{
			$p_contactName = substr($p_contactName, 0, self::MAX_LENGTH - 3) . '...';
		}

		$this->contactName = $p_contactName;
	}


	/**
	 * Returns the contact name as a string.
	 *
	 * @return string Equivalent string.
	 */
	public function __toString()
	{
		return $this->contactName;
	}
}