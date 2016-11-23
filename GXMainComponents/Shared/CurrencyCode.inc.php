<?php

/* --------------------------------------------------------------
   CurrencyCode.php 2015-11-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * CurrencyCode
 *
 * @category System
 * @package  Shared
 */
class CurrencyCode
{
	/**
	 * Holds the currency code as a string.
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * Holds the currency value as a decimal type.
	 *
	 * @var float
	 */
	protected $currencyValue;


	/**
	 * Constructor
	 *
	 * Validates the parameter and saves it to the property in uppercase format.
	 *
	 * @throws InvalidArgumentException if Argument is not exactly 3 letters
	 *
	 * @param \StringType $code          The currency Code (e.g. EUR)
	 * @param \DecimalType        $currencyValue Value of the currency
	 */
	public function __construct(StringType $code, DecimalType $currencyValue = null)
	{
		// Validate string length.
		if(strlen($code->asString()) !== 3)
		{
			throw new InvalidArgumentException('Argument must have exactly 3 letters');
		}
		
		$this->code          = strtoupper($code->asString());
		$this->currencyValue = (null !== $currencyValue) ? $currencyValue->asDecimal() : 1.00;
	}

	/**
	 * Get Code
	 *
	 * Returns the currency code as a string.
	 *
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}


	/**
	 * Get Currency Value
	 *
	 * Returns the currency value as a decimal type.
	 *
	 * @return DecimalType
	 */
	public function getCurrencyValue()
	{
		return $this->currencyValue;
	}
}