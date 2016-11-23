<?php

/* --------------------------------------------------------------
   InfoBoxService.php 2016-04-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxService
 *
 * @category System
 * @package  InfoBox
 */
class InfoBoxService implements InfoBoxServiceInterface
{
	/**
	 * Reader.
	 * @var InfoBoxReader
	 */
	protected $reader;

	/**
	 * Writer.
	 * @var InfoBoxWriter
	 */
	protected $writer;

	/**
	 * Deleter.
	 * @var InfoBoxDeleter
	 */
	protected $deleter;


	/**
	 * InfoBoxService constructor.
	 *
	 * @param InfoBoxReader  $reader  Reader.
	 * @param InfoBoxWriter  $writer  Writer.
	 * @param InfoBoxDeleter $deleter Deleter.
	 */
	public function __construct(InfoBoxReader $reader, InfoBoxWriter $writer, InfoBoxDeleter $deleter)
	{
		$this->reader  = $reader;
		$this->writer  = $writer;
		$this->deleter = $deleter;
	}


	/**
	 * Returns all info box messages.
	 * @return InfoBoxMessageCollection
	 */
	public function getAllMessages()
	{
		return $this->reader->getAll();
	}


	/**
	 * Adds a new info box message.
	 *
	 * @param InfoBoxMessage $message Message to save.
	 */
	public function addMessage(InfoBoxMessage $message)
	{
		$this->writer->write($message);
	}


	/**
	 * Updates a info box message.
	 *
	 * @param InfoBoxMessage $message Message to update.
	 */
	public function updateMessage(InfoBoxMessage $message)
	{
		$this->writer->update($message);
	}


	/**
	 * Reactivates the messages
	 */
	public function reactivateMessages()
	{
		$this->writer->reactivate();
	}


	/**
	 * Deletes a message based on the source.
	 *
	 * @param StringType $source Message source.
	 */
	public function deleteMessageBySource(StringType $source)
	{
		$this->deleter->deleteBySource($source);
	}


	/**
	 * Deletes a message based on its identifier.
	 *
	 * @param StringType $identifier Message identifier.
	 */
	public function deleteMessageByIdentifier(StringType $identifier)
	{
		$this->deleter->deleteByIdentifier($identifier);
	}


	/**
	 * Deletes a message by its ID.
	 *
	 * @param IdType $id Message ID.
	 */
	public function deleteMessageById(IdType $id)
	{
		$this->deleter->deleteById($id);
	}


	/**
	 * Updates a message status.
	 *
	 * @param IdType     $id     Message ID.
	 * @param StringType $status Message status.
	 */
	public function setMessageStatus(IdType $id, StringType $status)
	{
		$this->writer->setStatus($id, $status);
	}
}