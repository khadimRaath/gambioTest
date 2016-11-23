<?php

/* --------------------------------------------------------------
   AdminStatusOnlyInterface.inc.php 2016-02-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminStatusOnlyInterface
 *
 * @category System
 * @package Shared
 * @subpackage Interfaces
 */
interface AdminStatusOnlyInterface
{
	/**
	 * Makes sure that the admin status is currently given
	 *
	 * @throws LogicException
	 */
	public function validateCurrentAdminStatus();
}
