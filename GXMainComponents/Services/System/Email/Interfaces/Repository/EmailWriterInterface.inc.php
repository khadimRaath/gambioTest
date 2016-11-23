<?php
/* --------------------------------------------------------------
   EmailWriterInterface.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailWriterInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailWriterInterface
{
	/**
	 * Save (insert/update) an email record.
	 *
	 * @param EmailInterface $email E-Mail.
	 */
	public function write(EmailInterface $email);
}