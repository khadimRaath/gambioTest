<?php

/* --------------------------------------------------------------
   CategoryRepositoryDeleter.inc.php 2015-11-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryRepositoryDeleter
 *
 * This class deletes category records from the database and is used in the category repository among the classes for
 * writing and reading category records.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategoryRepositoryDeleter implements CategoryRepositoryDeleterInterface
{
	/**
	 * Database connector.
	 * 
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * CategoryRepositoryDeleter constructor.
	 *
	 * @param CI_DB_query_builder $db Database connector.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Deletes a specific category entity.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return CategoryRepositoryDeleter Same instance for chained method calls. 
	 */
	public function deleteById(IdType $categoryId)
	{
		$this->db->delete(array('categories', 'categories_description'),
		                  array('categories_id' => $categoryId->asInt()));

		return $this;
	}
}