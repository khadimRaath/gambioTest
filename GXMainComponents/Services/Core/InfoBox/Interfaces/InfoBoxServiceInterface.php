<?php

/* --------------------------------------------------------------
   InfoBoxServiceInterface.php 2016-04-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InfoBoxServiceInterface
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Interfaces
 */
interface InfoBoxServiceInterface
{
	/**
	 * Returns all info box messages.
	 * @return InfoBoxMessageCollection
	 */
	public function getAllMessages();
}