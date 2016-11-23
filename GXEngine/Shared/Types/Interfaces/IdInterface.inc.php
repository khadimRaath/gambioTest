<?php

/* --------------------------------------------------------------
   IdInterface.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class IdInterface
 *
 * @category   System
 * @package    Shared
 * @subpackage Interfaces
 */
interface IdInterface
{
	/**
	 * Returns the instance value as integer.
	 * @return int
	 */
	public function asInt();
}