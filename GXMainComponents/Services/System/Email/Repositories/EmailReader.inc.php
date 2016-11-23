<?php
/* --------------------------------------------------------------
   EmailReader.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailReaderInterface');

/**
 * Class EmailReader
 *
 * Reads email records from the database. This class provides a customizable interface
 * for reading operations so that it is possible to build different variations in the
 * EmailRepository class (e.g. "getPending", "findById", "getAll").
 *
 * @category   System
 * @package    Email
 * @subpackage Repository
 */
class EmailReader implements EmailReaderInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * E-Mail factory.
	 * @var EmailFactory
	 */
	protected $factory;


	/**
	 * Class Constructor
	 *
	 * @param CI_DB_query_builder   $db      Will be used for database operations.
	 * @param EmailFactoryInterface $factory Will be used for the creation of returned objects.
	 */
	public function __construct(CI_DB_query_builder $db, EmailFactoryInterface $factory)
	{
		$this->db      = $db;
		$this->factory = $factory;
	}


	/**
	 * Get email records filtered by conditions.
	 *
	 * Example:
	 *      $reader->get(array('email_id' => $customerId), 10, array( array('email_id', 'asc') ));
	 *
	 * @param array $conditions (optional) Contains conditions with column => value pairs.
	 * @param array $limit      (optional) Array that contains LIMIT and OFFSET value
	 *                          e.g. array( 'limit' => 10, 'offset' => 5 )
	 * @param array $order      (optional) Contains arrays with column, direction pairs
	 *                          e.g. array( 'column' => 'direction' )
	 *
	 * @return EmailCollection Returns a collection containing the email records.
	 */
	public function get(array $conditions = array(), array $limit = array(), array $order = array())
	{
		$this->_limit($limit);
		$this->_order($order);

		$results    = $this->db->get_where('emails', $conditions)->result_array();
		$collection = MainFactory::create('EmailCollection');

		foreach($results as &$item)
		{
			$item['contacts']    = $this->db->get_where('email_contacts', array('email_id' => $item['email_id']))
			                                ->result_array();
			$item['attachments'] = $this->db->get_where('email_attachments', array('email_id' => $item['email_id']))
			                                ->result_array();
			$collection->add($this->_createEmailByArray($item));
		}

		return $collection;
	}


	/**
	 * Filter email records with provided keyword string.
	 *
	 * @param string $p_keyword String to be used for filtering the email records.
	 * @param array  $limit     (optional) Array that contains LIMIT and OFFSET value
	 *                          e.g. array( 'limit' => 10, 'offset' => 5 )
	 * @param array  $order     (optional) Contains arrays with column, direction pairs
	 *                          e.g. array( 'column' => 'direction' )
	 *
	 * @return EmailCollection Returns a collection containing the email records.
	 */
	public function filter($p_keyword, array $limit = array(), array $order = array())
	{
		$this->_filter($p_keyword);
		$this->_limit($limit);
		$this->_order($order);

		$results    = $this->db->get()->result_array();
		$collection = MainFactory::create('EmailCollection');

		foreach($results as &$item)
		{
			$item['contacts']    = $this->db->get_where('email_contacts', array('email_id' => $item['email_id']))
			                                ->result_array();
			$item['attachments'] = $this->db->get_where('email_attachments', array('email_id' => $item['email_id']))
			                                ->result_array();
			$collection->add($this->_createEmailByArray($item));
		}

		return $collection;
	}


	/**
	 * Get the current count of the email records in the database.
	 *
	 * This method will quickly return the record count of the "emails" table. It must
	 * be used when we just need the number and not the data, because the "get" or "find"
	 * methods need more time to load and parse the records.
	 *
	 * @param string $p_filterKeyword (optional) If provided the records will be filtered.
	 *
	 * @throws InvalidArgumentException If the provided argument is not a string.
	 *
	 * @return int Returns the row number of the email table.
	 */
	public function getRecordCount($p_filterKeyword = '')
	{
		if(!is_string($p_filterKeyword))
		{
			throw new InvalidArgumentException('Invalid argument provided (string expected): '
			                                   . gettype($p_filterKeyword));
		}

		if(!empty($p_filterKeyword))
		{
			$this->_filter($p_filterKeyword);
			$count = $this->db->count_all_results();
		}
		else
		{
			$count = $this->db->count_all('emails');
		}

		return (int)$count;
	}


	/**
	 * Creates an email object out of an array.
	 *
	 * This method expects the following values to be present in the array: 'email_id', 'subject',
	 * 'content', 'is_pending', 'contacts', 'attachments'. It uses the EmailFactory for creating
	 * email objects.
	 *
	 * @param array $emailDataArray Contains the database record information.
	 *
	 * @throws  UnexpectedValueException If the 'creation_date' of the email is empty.
	 *
	 * @return Email Returns an object that represents the database record.
	 */
	protected function _createEmailByArray(array $emailDataArray)
	{
		// Required email fields must always have a value. 
		$emailId   = new IdType((int)$emailDataArray['email_id']);
		$subject   = (!empty($emailDataArray['subject'])) ? MainFactory::create('EmailSubject',
		                                                                        $emailDataArray['subject']) : null;
		$isPending = (bool)$emailDataArray['is_pending'];

		// Optional email fields might be empty. In that case we simply set a NULL value.
		$contentHtml  = (!empty($emailDataArray['content_html'])) ? MainFactory::create('EmailContent',
		                                                                                html_entity_decode_wrapper($emailDataArray['content_html'])) : null;
		$contentPlain = (!empty($emailDataArray['content_plain'])) ? MainFactory::create('EmailContent',
		                                                                                 $emailDataArray['content_plain']) : null;

		$contacts = MainFactory::create('ContactCollection');
		foreach($emailDataArray['contacts'] as $contactDataArray)
		{
			// Required Fields
			$emailAddress = MainFactory::create('EmailAddress', $contactDataArray['email_address']);
			$contactType  = MainFactory::create('ContactType', $contactDataArray['contact_type']);

			// Optional Field
			$contactName = (!empty($contactDataArray['contact_name'])) ? MainFactory::create('ContactName',
			                                                                                 $contactDataArray['contact_name']) : null;

			$contacts->add($this->factory->createContact($emailAddress, $contactType, $contactName));
		}

		$attachments = MainFactory::create('AttachmentCollection');
		foreach($emailDataArray['attachments'] as $attachmentDataArray)
		{
			// Required Field
			$path = MainFactory::create('AttachmentPath', $attachmentDataArray['path']);

			// Optional Field
			$name = (!empty($attachmentDataArray['name'])) ? MainFactory::create('AttachmentName',
			                                                                     $attachmentDataArray['name']) : null;

			$attachments->add(MainFactory::create('EmailAttachment', $path, $name));
		}

		$email = $this->factory->createEmail($emailId, $subject, $contentHtml, $contentPlain, $isPending, $contacts,
		                                     $attachments);

		// All registered emails must have a creation date. If there is a record with no 
		// creation date then something wrong happened during the "send" or "queue" operations.
		if($emailDataArray['creation_date'] === null)
		{
			throw new UnexpectedValueException('Email "creation_date" field must not be null.');
		}

		$email->setCreationDate(MainFactory::create('DateTime', $emailDataArray['creation_date']));

		if($emailDataArray['sent_date'] !== null)
		{
			$email->setSentDate(MainFactory::create('DateTime', $emailDataArray['sent_date']));
		}

		return $email;
	}


	/**
	 * Apply filter rules to email records.
	 *
	 * This method will set the SQL filters depending the provided keyword so that one
	 * can "get" the filtered records.
	 *
	 * @param string $p_keyword Filtering keyword to be applied in the query.
	 *
	 * @throws InvalidArgumentException If the $keyword argument is not a string.
	 */
	protected function _filter($p_keyword)
	{
		if(!is_string($p_keyword))
		{
			throw new InvalidArgumentException('Invalid argument provided (string expected) $keyword: ' . $p_keyword);
		}

		$this->db->select('emails.*')
		         ->distinct()
		         ->from('emails')
		         ->join('email_contacts', 'email_contacts.email_id = emails.email_id', 'left')
		         ->join('email_attachments', 'email_attachments.email_id = emails.email_id', 'left')
		         ->like('emails.subject', $p_keyword)
		         ->or_like('emails.content_html', $p_keyword)
		         ->or_like('emails.content_plain', $p_keyword)
		         ->or_like('emails.creation_date', $p_keyword)
		         ->or_like('emails.sent_date', $p_keyword)
		         ->or_like('email_contacts.email_address', $p_keyword)
		         ->or_like('email_contacts.contact_name', $p_keyword)
		         ->or_like('email_attachments.path', $p_keyword)
		         ->or_like('email_attachments.name', $p_keyword);
	}


	/**
	 * Apply LIMIT clause to query.
	 *
	 * Example: $this->_limit( array( 'limit' => 10, 'offset' => 0 ) );
	 *
	 * @link http://www.codeigniter.com/userguide3/database/query_builder.html#limiting-or-counting-results
	 *
	 * @param array $rule Must be an array that contains 'limit' and 'offset' values.
	 */
	protected function _limit(array $rule)
	{
		if(!empty($rule))
		{
			$this->db->limit((int)$rule['limit'], (int)$rule['offset']);
		}
	}


	/**
	 * Apply ORDER BY clause to query.
	 *
	 * Example: $this->_order( array( 'email_id' => 'desc' ) );
	 *
	 * @link http://www.codeigniter.com/userguide3/database/query_builder.html#ordering-results
	 *
	 * @param array $rule Contains column, direction arrays for ordering results.
	 */
	protected function _order(array $rule)
	{
		foreach($rule as $column => $direction)
		{
			$this->db->order_by($column, $direction);
		}
	}
}