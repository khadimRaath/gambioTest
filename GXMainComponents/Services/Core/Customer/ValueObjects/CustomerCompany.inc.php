<?php
/* --------------------------------------------------------------
   CustomerCompany.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerCompanyInterface');

/**
 * Value Object
 *
 * Class CustomerCompany
 *
 * Represents a customer company
 *
 * @category   System
 * @package    Customer
 * @subpackage ValueObjects
 * @implements CustomerCompanyInterface
 */
class CustomerCompany implements CustomerCompanyInterface
{
	/**
	 * Customer's company.
	 * @var string
	 */
	protected $company;


	/**
	 * Constructor of the class CustomerCompany.
	 *
	 * Validates the length and data type of the customer company.
	 *
	 * @param string $p_company Customer's company.
	 *
	 * @throws InvalidArgumentException If $p_company is not a string.
	 * @throws LengthException If $p_company contains more characters than 255.
	 */
	public function __construct($p_company)
	{
		if(!is_string($p_company))
		{
			throw new InvalidArgumentException('$p_company is not a string');
		}

		$dbFieldLength = 255;
		$company       = trim($p_company);

		if(strlen_wrapper($company) > $dbFieldLength)
		{
			throw new LengthException('$company is longer than ' . $dbFieldLength . ' characters VARCHAR(255)');
		}

		$this->company = $company;
	}


	/**
	 * Returns the equivalent string value.
	 * @return string Equivalent string value.
	 */
	public function __toString()
	{
		return $this->company;
	}
} 