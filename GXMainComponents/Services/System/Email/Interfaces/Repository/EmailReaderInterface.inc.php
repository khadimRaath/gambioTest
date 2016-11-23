<?php
/* --------------------------------------------------------------
   EmailReaderInterface.inc.php 2015-01-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface EmailReaderInterface
 *
 * @category   System
 * @package    Email
 * @subpackage Interfaces
 */
interface EmailReaderInterface
{
	/**
	 * Get email records filtered by conditions.
	 *
	 * @param array $conditions (optional) Contains conditions with column => value pairs.
	 * @param array $limit      (optional) Array that contains LIMIT and OFFSET value
	 *                          e.g. array( 'limit' => 10, 'offset' => 5 )
	 * @param array $order      (optional) Contains arrays with column, direction pairs
	 *                          e.g. array( 'column' => 'direction' )
	 *
	 * @return EmailCollection Returns a collection containing the email records.
	 */
	public function get(array $conditions = array(), array $limit = array(), array $order = array());


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
	public function getRecordCount($p_filterKeyword = '');
}