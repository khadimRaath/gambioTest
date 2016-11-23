<?php

/* --------------------------------------------------------------
   InfoBoxMessage.php 2016-04-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxMessage
 *
 * @category System
 * @package  InfoBox
 */
class InfoBoxMessage implements InfoBoxMessageInterface
{
	/**
	 * Message source.
	 * @var string
	 */
	protected $source;

	/**
	 * Message ID.
	 * @var string
	 */
	protected $id;

	/**
	 * Message identifier.
	 * @var string
	 */
	protected $identifier;

	/**
	 * Message status.
	 * @var string
	 */
	protected $status;

	/**
	 * Message type.
	 * @var string
	 */
	protected $type;

	/**
	 * Message visibility.
	 * @var string
	 */
	protected $visibility;

	/**
	 * Message button link.
	 * @var string
	 */
	protected $buttonLink;

	/**
	 * Customer ID.
	 * @var int
	 */
	protected $customerId;

	/**
	 * Added date time.
	 * @var DateTime
	 */
	protected $addedDateTime;

	/**
	 * Modified date time.
	 * @var DateTime
	 */
	protected $modifiedDateTime;

	/**
	 * Message headlines.
	 * @var EditableKeyValueCollection
	 */
	protected $headlines;

	/**
	 * Messages.
	 * @var EditableKeyValueCollection
	 */
	protected $messages;

	/**
	 * Message button labels.
	 * @var EditableKeyValueCollection
	 */
	protected $buttonLabels;


	/**
	 * InfoBoxMessage constructor.
	 */
	public function __construct()
	{
		$this->setSource(new StringType('intern'));
		$this->setStatus(new StringType('new'));
		$this->setType(new StringType('info'));
		$this->setVisibility(new StringType('hideable'));
		$this->setButtonLink(new StringType(''));
		$this->setAddedDateTime(new DateTime());
		$this->setModifiedDateTime(new DateTime());
		$this->headlines    = MainFactory::create('EditableKeyValueCollection', array());
		$this->messages     = MainFactory::create('EditableKeyValueCollection', array());
		$this->buttonLabels = MainFactory::create('EditableKeyValueCollection', array());
	}
	

	/**
	 * Sets the message's source.
	 *
	 * @param StringType $source Source of the message (e.g. 'internal').
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setSource(StringType $source)
	{
		$this->source = $source->asString();

		return $this;
	}


	/**
	 * Returns the message's source.
	 * @return string Source of the message.
	 */
	public function getSource()
	{
		return $this->source;
	}


	/**
	 * Sets the ID of the message.
	 *
	 * @param IdType $id Identifier of the message.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setId(IdType $id)
	{
		$this->id = $id->asInt();

		return $this;
	}


	/**
	 * Returns the ID of the message.
	 * @return int ID of the message.
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Sets the status of the message.
	 *
	 * @param StringType $status Status of the message.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setStatus(StringType $status)
	{
		$this->status = $status->asString();

		return $this;
	}


	/**
	 * Returns the status of the message.
	 * @return string Status of the message.
	 */
	public function getStatus()
	{
		return $this->status;
	}


	/**
	 * Sets the type of the message.
	 *
	 * @param StringType $type Type of the message.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setType(StringType $type)
	{
		$this->type = $type->asString();

		return $this;
	}


	/**
	 * Returns the type of the message.
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * Sets the visibility of the message.
	 *
	 * @param StringType $visibility Visibility of the message.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setVisibility(StringType $visibility)
	{
		$this->visibility = $visibility->asString();

		return $this;
	}


	/**
	 * Returns the visibility of the message.
	 * @return string Visibility of the message.
	 */
	public function getVisibility()
	{
		return $this->visibility;
	}


	/**
	 * Sets the link of the message button.
	 *
	 * @param StringType $buttonLink Link of the message button.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setButtonLink(StringType $buttonLink)
	{
		$this->buttonLink = $buttonLink->asString();

		return $this;
	}


	/**
	 * Returns the link of the message button.
	 * @return string Link of the message button.
	 */
	public function getButtonLink()
	{
		return $this->buttonLink;
	}


	/**
	 * Sets the customer ID of the message.
	 *
	 * @param IdType $customerId Customer ID of the message.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setCustomerId(IdType $customerId)
	{
		$this->customerId = $customerId->asInt();

		return $this;
	}


	/**
	 * Returns the customer ID of the message.
	 * @return int
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}


	/**
	 * Sets the message creation date time.
	 *
	 * @param DateTime $dateTime Message added date time value.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setAddedDateTime(DateTime $dateTime)
	{
		$this->addedDateTime = $dateTime;

		return $this;
	}


	/**
	 * Returns the message creation date time.
	 * @return DateTime The added date time.
	 */
	public function getAddedDateTime()
	{
		return $this->addedDateTime;
	}


	/**
	 * Sets the message modification date time.
	 *
	 * @param DateTime $dateTime Message modification date time.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setModifiedDateTime(DateTime $dateTime)
	{
		$this->modifiedDateTime = $dateTime;

		return $this;
	}


	/**
	 * Returns the message modification date time.
	 * @return DateTime The modified date time.
	 */
	public function getModifiedDateTime()
	{
		return $this->modifiedDateTime;
	}


	/**
	 * Sets the message headline.
	 *
	 * @param StringType   $text         Message headline.
	 * @param LanguageCode $languageCode Language code for message headline.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setHeadLine(StringType $text, LanguageCode $languageCode)
	{
		$this->headlines->setValue($languageCode->asString(), $text->asString());

		return $this;
	}


	/**
	 * Returns the message headline.
	 *
	 * @param LanguageCode $languageCode Language code of the message headline.
	 *
	 * @return string The message headline.
	 */
	public function getHeadLine(LanguageCode $languageCode)
	{
		return $this->headlines->getValue($languageCode->asString());
	}


	/**
	 * Sets the message.
	 *
	 * @param StringType   $text         Message.
	 * @param LanguageCode $languageCode Language code of the message.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setMessage(StringType $text, LanguageCode $languageCode)
	{
		$this->messages->setValue($languageCode->asString(), $text->asString());

		return $this;
	}


	/**
	 * Returns the message.
	 *
	 * @param LanguageCode $languageCode Language code of the message.
	 *
	 * @return string The message.
	 */
	public function getMessage(LanguageCode $languageCode)
	{
		return $this->messages->getValue($languageCode->asString());
	}


	/**
	 * Sets the message button label.
	 *
	 * @param StringType   $text         Button label text.
	 * @param LanguageCode $languageCode Button label text language code.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setButtonLabel(StringType $text, LanguageCode $languageCode)
	{
		$this->buttonLabels->setValue($languageCode->asString(), $text->asString());

		return $this;
	}


	/**
	 * Returns the message button label.
	 *
	 * @param LanguageCode $languageCode Button label language code.
	 *
	 * @return string The message button label.
	 */
	public function getButtonLabel(LanguageCode $languageCode)
	{
		return $this->buttonLabels->getValue($languageCode->asString());
	}


	/**
	 * Sets the identifier string of the message
	 *
	 * @param StringType $identifier Message identifier.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setIdentifier(StringType $identifier)
	{
		$this->identifier = $identifier->asString();

		return $this;
	}


	/**
	 * Returns the message identifier.
	 * @return string Message identifier.
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}


	/**
	 * Sets the messages by adding a collection.
	 *
	 * @param KeyValueCollection $messages Messages.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setMessageCollection(KeyValueCollection $messages)
	{
		$this->messages->addCollection($messages);

		return $this;
	}


	/**
	 * Sets the message headlines.
	 *
	 * @param KeyValueCollection $headlines Message headlines.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setHeadLineCollection(KeyValueCollection $headlines)
	{
		$this->headlines->addCollection($headlines);

		return $this;
	}


	/**
	 * Sets the button labels.
	 *
	 * @param KeyValueCollection $buttonLabels Message button labels.
	 *
	 * @return InfoBoxMessage Same instance for method chaining.
	 */
	public function setButtonLabelCollection(KeyValueCollection $buttonLabels)
	{
		$this->buttonLabels->addCollection($buttonLabels);

		return $this;
	}


	/**
	 * Returns the message collection.
	 * @return EditableKeyValueCollection
	 */
	public function getMessageCollection()
	{
		return $this->messages;
	}


	/**
	 * Returns the headline collection.
	 * @return EditableKeyValueCollection
	 */
	public function getHeadLineCollection()
	{
		return $this->headlines;
	}


	/**
	 * Returns the button label collection.
	 * @return EditableKeyValueCollection
	 */
	public function getButtonLabelCollection()
	{
		return $this->buttonLabels;
	}
}