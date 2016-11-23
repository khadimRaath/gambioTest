<?php
/* --------------------------------------------------------------
   ContactTypeInterface.inc.php 2015-02-03 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ContactTypeInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface ContactTypeInterface
{
	/**
	 * Returns the contact type as a string.
	 *
	 * @return string Equivalent string.
	 */
	public function __toString();
}