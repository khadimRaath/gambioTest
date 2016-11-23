<?php
/* --------------------------------------------------------------
   EmailRepositoryInterface.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailRepositoryInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailRepositoryInterface
{
	/**
	 * Writes an email record into the database.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	public function write(EmailInterface $email);


	/**
	 * Returns an email record by ID.
	 *
	 * @param IdType $id Database ID of the  record to be returned.
	 *
	 * @return EmailInterface Returns the email object that matches the ID.
	 */
	public function getById(IdType $id);


	/**
	 * Find email by ID
	 *
	 * This method will try to find the email record that matches provided ID and
	 * will return NULL on when the record does not exist.
	 *
	 * @param IdType $id Email record id to be found.
	 *
	 * @return EmailInterface|null Returns email object or null on failure.
	 */
	public function findById(IdType $id);


	/**
	 * Returns a collection of pending emails.
	 *
	 * @return EmailCollection Returns the pending email objects.
	 */
	public function getPending();


	/**
	 * Returns a collection of sent emails.
	 *
	 * @return EmailCollection Returns the sent email objects.
	 */
	public function getSent();


	/**
	 * Returns all email records from the database.
	 *
	 * @return EmailCollection Returns all email objects.
	 */
	public function getAll();


	/**
	 * Removes all information of an email record from the database.
	 *
	 * This method will remove ALL the email information, from the tables that
	 * contain information about the specified email.
	 *
	 * @param EmailInterface $email Contains the email information.
	 */
	public function delete(EmailInterface $email);


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
	public function filter($p_keyword, array $limit = array(), array $order = array());


	/**
	 * Get the current count of the email records in the database.
	 *
	 * @param string $p_filterKeyword (optional) If provided the records will be filtered.
	 *
	 * @return int Returns the row number of the email table.
	 */
	public function getRecordCount($p_filterKeyword = '');
}