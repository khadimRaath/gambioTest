<?php

/* --------------------------------------------------------------
   EmptyLanguageCode.php 2015-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * EmptyLanguageCode
 *
 * The purpose of this class is to return an empty string
 * as representation of a empty language code value.
 *
 * @category System
 * @package  Shared
 */
class EmptyLanguageCode extends LanguageCode
{
	/**
	 * Override constructor, as no argument are passed, and therefore
	 * no validation is needed in this class.
	 */
	public function __construct()
	{
	}
	
	
	public function __toString()
	{
		return '';
	}
}