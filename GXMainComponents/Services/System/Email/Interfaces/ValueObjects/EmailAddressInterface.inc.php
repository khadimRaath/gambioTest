<?php
/* --------------------------------------------------------------
   EmailAddressInterface.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailAddressInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailAddressInterface
{
	/**
	 * Returns the email address as a string.
	 *
	 * @return string Equivalent string.
	 */
	public function __toString();
}