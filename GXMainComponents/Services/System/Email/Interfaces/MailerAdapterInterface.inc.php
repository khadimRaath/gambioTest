<?php
/* --------------------------------------------------------------
   MailerAdapterInterface.inc.php 2015-02-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface MailerAdapterInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface MailerAdapterInterface
{
	/**
	 * Sends a single email.
	 *
	 * @param EmailInterface $email Contains email information.
	 */
	public function send(EmailInterface $email);
}