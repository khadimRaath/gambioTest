<?php
/* --------------------------------------------------------------
   EmailDeleter.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('EmailDeleterInterface');

/**
 * Class EmailDeleter
 *
 * Deletes email records from the database.
 *
 * @category   System
 * @package    Email
 * @subpackage Repository
 */
class EmailDeleter implements EmailDeleterInterface
{
	/**
	 * Query builder.
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * Class Constructor
	 *
	 * @param CI_DB_query_builder $db Query builder.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Removes a record from the database.
	 *
	 * This method will delete all the email relevant entities from the database. It will
	 * not throw an exception if the given record is not found.
	 *
	 * @param EmailInterface $email E-Mail.
	 */
	public function delete(EmailInterface $email)
	{
		$this->db->delete(array('emails', 'email_contacts', 'email_attachments'),
		                  array('email_id' => (int)(string)$email->getId()));
	}
}