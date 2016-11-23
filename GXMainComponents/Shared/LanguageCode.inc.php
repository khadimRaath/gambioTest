<?php

/* --------------------------------------------------------------
   LanguageCode.php 2015-12-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * LanguageCode
 *
 * @category System
 * @package  Shared
 */
class LanguageCode
{
	/**
	 * Language code.
	 * @var string
	 */
	protected $languageCode = '';
	
	
	/**
	 * Class Constructor
	 *
	 * Validates the parameter and saves it to the property in uppercase format.
	 *
	 * @param StringType $code
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct(StringType $code)
	{
		if(strlen($code->asString()) !== 2) // Argument must have 2 letters.
		{
			throw new InvalidArgumentException('$code must have exactly 2 letters');
		}
		
		$this->languageCode = strtoupper($code->asString());
	}
	
	
	/**
	 * Returns the language code.
	 * @return string
	 */
	public function __toString()
	{
		return $this->languageCode;
	}
	
	
	/**
	 * As String
	 *
	 * Returns the language code as a string.
	 *
	 * @return string
	 */
	public function asString()
	{
		return $this->languageCode;
	}
}