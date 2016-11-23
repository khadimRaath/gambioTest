<?php

/* --------------------------------------------------------------
   CategoryListProviderFactory.inc.php 2015-11-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryListProviderFactory
 *
 * This class creates CategoryListProvider objects for a specific language and filter of customer status permissions
 * with its dependencies.
 *
 * @category   System
 * @package    Category
 * @subpackage Factories
 */
class CategoryListProviderFactory implements CategoryListProviderFactoryInterface
{
	/**
	 * Category repository.
	 * 
	 * @var CategoryRepositoryInterface
	 */
	protected $categoryRepo;

	/**
	 * Database connector.
	 * 
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * CategoryListProviderFactory constructor.
	 *
	 * @param CategoryRepositoryInterface $categoryRepo Category repository.
	 * @param CI_DB_query_builder         $db           Database connector.
	 */
	public function __construct(CategoryRepositoryInterface $categoryRepo, CI_DB_query_builder $db)
	{
		$this->categoryRepo = $categoryRepo;
		$this->db           = $db;
	}
	

	/**
	 * Creates a CategoryListProvider for retrieving lists.
	 *
	 * @param LanguageCode $languageCode Two letter language code.
	 * @param array        $conditions   Optional conditions for data request.
	 *
	 * @return CategoryListProviderInterface
	 */
	public function createCategoryListProvider(LanguageCode $languageCode, array $conditions = array())
	{
		return MainFactory::create('CategoryListProvider', $languageCode, $conditions, $this->categoryRepo, $this->db);
	}
}