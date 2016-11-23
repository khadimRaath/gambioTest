<?php

/* --------------------------------------------------------------
   InfoBoxWriterInterface.php 2016-04-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InfoBoxWriterInterface
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Interfaces
 */
interface InfoBoxWriterInterface
{
	/**
	 * Adds a new message.
	 *
	 * @param InfoBoxMessage $message Info box message to save.
	 */
	public function write(InfoBoxMessage $message);


	/**
	 * Updates a message status.
	 *
	 * @param IdType     $id     Message ID.
	 * @param StringType $status Message status to set.
	 */
	public function setStatus(IdType $id, StringType $status);


	/**
	 * Reactivates all messages.
	 */
	public function reactivate();

}