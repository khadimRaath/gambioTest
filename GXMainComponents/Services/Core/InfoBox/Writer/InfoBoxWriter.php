<?php

/* --------------------------------------------------------------
   InfoBoxWriter.php 2016-04-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxWriter
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Interfaces
 */
class InfoBoxWriter implements InfoBoxWriterInterface
{
	/**
	 * Database connection.
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * Name of the table for the info box messages entries.
	 * @var string
	 */
	protected $infoBoxTable = 'infobox_messages';

	/**
	 * Name of the table for the info box description entries.
	 * @var string
	 */
	protected $infoBoxTableDescription = 'infobox_messages_description';

	/**
	 * Formatting pattern for date time values.
	 * @var string
	 */
	protected $dateTimeFormat = 'Y-m-d H:i:s';

	/**
	 * Language provider.
	 * @var LanguageProvider
	 */
	protected $languageProvider;

	/**
	 * Name of the ID column.
	 * @var string
	 */
	protected $id_column = 'infobox_messages_id';

	/**
	 * Info box item reactivation time limit.
	 * Default value is 604800 which equals 1 week.
	 * @var int
	 */
	protected $reactivationTimeLimit = 604800;


	/**
	 * Constructor of the class InfoBoxWriter.
	 *
	 * @param CI_DB_query_builder $db Query builder.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db               = $db;
		$this->languageProvider = MainFactory::create('LanguageProvider', $db);
	}


	/**
	 * Adds a new message.
	 *
	 * @param InfoBoxMessage $message Info box message to save.
	 */
	public function write(InfoBoxMessage $message)
	{
		$messageData = array(
			'source'        => $message->getSource(),
			'identifier'    => $message->getIdentifier(),
			'status'        => $message->getStatus(),
			'type'          => $message->getType(),
			'visibility'    => $message->getVisibility(),
			'button_link'   => $message->getButtonLink(),
			'customers_id'  => $message->getCustomerId(),
			'date_added'    => (string)$message->getAddedDateTime()->format($this->dateTimeFormat),
			'date_modified' => (string)$message->getModifiedDateTime()->format($this->dateTimeFormat)
		);

		$this->db->insert($this->infoBoxTable, $messageData);
		$message->setId(new IdType($this->db->insert_id()));

		$activeLanguageCodes = $this->languageProvider->getActiveCodes()->getArray();

		foreach($activeLanguageCodes as $code)
		{
			$messageDescriptionData = array(
				'infobox_messages_id' => $message->getId(),
				'languages_id'        => $this->languageProvider->getIdByCode($code),
				'headline'            => $message->getHeadLineCollection()
				                                 ->keyExists($code->asString()) ? $message->getHeadLine($code) : '',
				'message'             => $message->getMessageCollection()
				                                 ->keyExists($code->asString()) ? $message->getMessage($code) : '',
				'button_label'        => $message->getButtonLabelCollection()
				                                 ->keyExists($code->asString()) ? $message->getButtonLabel($code) : ''
			);

			$this->db->insert($this->infoBoxTableDescription, $messageDescriptionData);
		}
	}


	/**
	 * Reactivates all messages.
	 */
	public function reactivate()
	{
		$limit = date($this->dateTimeFormat, time() - $this->reactivationTimeLimit);

		$data = array(
			'status'        => 'new',
			'date_modified' => date($this->dateTimeFormat)
		);

		$where = array(
			'date_added <' => $limit,
			'status'       => 'hidden'
		);

		$this->db->update($this->infoBoxTable, $data, $where);
	}


	/**
	 * Updates a message status.
	 *
	 * @param IdType     $id     Message ID.
	 * @param StringType $status Message status to set.
	 */
	public function setStatus(IdType $id, StringType $status)
	{
		$updateData = array(
			'status' 				=> $status->asString(),
			'date_modified' => date($this->dateTimeFormat)
		);

		$where = array(
			$this->id_column => $id->asInt()
		);

		$this->db->update($this->infoBoxTable, $updateData, $where);
	}
}