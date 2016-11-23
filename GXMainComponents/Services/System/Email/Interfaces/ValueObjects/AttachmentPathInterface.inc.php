<?php
/* --------------------------------------------------------------
   AttachmentPathInterface.inc.php 2015-01-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AttachmentPathInterface
 *
 * @category   System
 * @package    Email
 * @subpackage ValueObjects
 */
interface AttachmentPathInterface
{
	/**
	 * Returns attachment path.
	 *
	 * @return string Equivalent string.
	 */
	public function __toString();
}