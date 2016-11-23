<?php

/* --------------------------------------------------------------
   InfoBoxMessageInterface.php 2016-04-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface InfoBoxMessageInterface
 *
 * @category   System
 * @package    InfoBox
 * @subpackage Interfaces
 */
interface InfoBoxMessageInterface
{
	/**
	 * Sets the message's source.
	 *
	 * @param StringType $source Source of the message (e.g. 'internal').
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setSource(StringType $source);


	/**
	 * Returns the message's source.
	 * @return string Source of the message.
	 */
	public function getSource();
	

	/**
	 * Sets the ID of the message.
	 *
	 * @param IdType $id Identifier of the message.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setId(IdType $id);


	/**
	 * Returns the ID of the message.
	 * @return int ID of the message.
	 */
	public function getId();


	/**
	 * Sets the identifier string of the message
	 *
	 * @param StringType $identifier Message identifier.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setIdentifier(StringType $identifier);


	/**
	 * Returns the message identifier.
	 * @return string Message identifier.
	 */
	public function getIdentifier();


	/**
	 * Sets the status of the message.
	 *
	 * @param StringType $status Status of the message.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setStatus(StringType $status);


	/**
	 * Returns the status of the message.
	 * @return string Status of the message.
	 */
	public function getStatus();
	

	/**
	 * Sets the type of the message.
	 *
	 * @param StringType $type Type of the message.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setType(StringType $type);


	/**
	 * Returns the type of the message.
	 * @return string
	 */
	public function getType();
	

	/**
	 * Sets the visibility of the message.
	 *
	 * @param StringType $visibility Visibility of the message.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setVisibility(StringType $visibility);


	/**
	 * Returns the visibility of the message.
	 * @return string Visibility of the message.
	 */
	public function getVisibility();
	

	/**
	 * Sets the link of the message button.
	 *
	 * @param StringType $buttonLink Link of the message button.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setButtonLink(StringType $buttonLink);


	/**
	 * Returns the link of the message button.
	 * @return string Link of the message button.
	 */
	public function getButtonLink();
	

	/**
	 * Sets the customer ID of the message.
	 *
	 * @param IdType $customerId Customer ID of the message.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setCustomerId(IdType $customerId);


	/**
	 * Returns the customer ID of the message.
	 * @return int
	 */
	public function getCustomerId();
	

	/**
	 * Sets the message creation date time.
	 *
	 * @param DateTime $dateTime Message added date time value.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setAddedDateTime(DateTime $dateTime);


	/**
	 * Returns the message creation date time.
	 * @return DateTime The added date time.
	 */
	public function getAddedDateTime();
	

	/**
	 * Sets the message modification date time.
	 *
	 * @param DateTime $dateTime Message modification date time.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setModifiedDateTime(DateTime $dateTime);


	/**
	 * Returns the message modification date time.
	 * @return DateTime The modified date time.
	 */
	public function getModifiedDateTime();


	/**
	 * Sets the message headline.
	 *
	 * @param StringType   $text         Message headline.
	 * @param LanguageCode $languageCode Language code for message headline.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setHeadLine(StringType $text, LanguageCode $languageCode);


	/**
	 * Returns the message headline.
	 *
	 * @param LanguageCode $languageCode Language code of the message headline.
	 *
	 * @return string The message headline.
	 */
	public function getHeadLine(LanguageCode $languageCode);


	/**
	 * Sets the message.
	 *
	 * @param StringType   $text         Message.
	 * @param LanguageCode $languageCode Language code of the message.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setMessage(StringType $text, LanguageCode $languageCode);


	/**
	 * Returns the message.
	 *
	 * @param LanguageCode $languageCode Language code of the message.
	 *
	 * @return string The message.
	 */
	public function getMessage(LanguageCode $languageCode);


	/**
	 * Sets the message button label.
	 *
	 * @param StringType   $text         Button label text.
	 * @param LanguageCode $languageCode Button label text language code.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setButtonLabel(StringType $text, LanguageCode $languageCode);


	/**
	 * Returns the message button label.
	 *
	 * @param LanguageCode $languageCode Button label language code.
	 *
	 * @return string The message button label.
	 */
	public function getButtonLabel(LanguageCode $languageCode);


	/**
	 * Sets the messages by adding a collection.
	 *
	 * @param KeyValueCollection $messages Messages.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setMessageCollection(KeyValueCollection $messages);


	/**
	 * Sets the message headlines.
	 *
	 * @param KeyValueCollection $headlines Message headlines.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setHeadLineCollection(KeyValueCollection $headlines);


	/**
	 * Sets the button labels.
	 *
	 * @param KeyValueCollection $buttonLabels Message button labels.
	 *
	 * @return InfoBoxMessageInterface Same instance for method chaining.
	 */
	public function setButtonLabelCollection(KeyValueCollection $buttonLabels);


	/**
	 * Returns the message collection.
	 * @return EditableKeyValueCollection
	 */
	public function getMessageCollection();


	/**
	 * Returns the headline collection.
	 * @return EditableKeyValueCollection
	 */
	public function getHeadLineCollection();


	/**
	 * Returns the button label collection.
	 * @return EditableKeyValueCollection
	 */
	public function getButtonLabelCollection();
}