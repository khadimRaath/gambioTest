<?php

/* --------------------------------------------------------------
   EmptyCurrencyCode.php 2015-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * EmptyCurrencyCode
 *
 * The purpose of this class is to return an empty string
 * as representation of a empty currency code value.
 *
 * @category System
 * @package Shared
 */
class EmptyCurrencyCode extends CurrencyCode
{
	/**
	 * Override constructor, as no argument are passed, and therefore
	 * no validation is needed in this class.
	 */
	public function __construct()
	{

	}
}