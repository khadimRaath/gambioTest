<?php
/* --------------------------------------------------------------
   CustomerDateOfBirth.inc.php 2016-03-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Value Object
 *
 * Class CustomerDateOfBirth
 *
 * Represents a customer birth date
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @extends    DateTime
 */
class CustomerDateOfBirth extends DateTime
{
	/**
	 * Is null date?
	 * 
	 * @var bool
	 */
	protected $isNullDate = false;


	/**
	 * Constructor of the class CustomerDateOfBirth.
	 *
	 * @param string $p_dateOfBirth Customer's date of birth.
	 */
	public function __construct($p_dateOfBirth = '1000-01-01 00:00:00')
	{
		$dateOfBirth = $p_dateOfBirth;
		
		if(strpos($dateOfBirth, '0000') === 0 || empty($dateOfBirth) || $dateOfBirth === '00.00.0000')
		{
			$dateOfBirth = '1000-01-01 00:00:00';
			$this->isNullDate = true;
		}

		parent::__construct($dateOfBirth);
	}


	/**
	 * Format DateTime
	 * 
	 * Formats a date by a given pattern and ensures that dates that represent empty data are formatted to
	 * '1000-01-01 00:00:00'
	 *
	 * @param string $p_format Date format.
	 *
	 * @return mixed|string Formatted date.
	 */
	public function format($p_format)
	{
		$formattedDate = parent::format($p_format);

		return $formattedDate;
	}
}