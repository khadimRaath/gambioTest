<?php

/* --------------------------------------------------------------
   CategoryListProvider.inc.php 2015-11-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryListProvider
 *
 * This class provides methods for creating a list of flattened categories with just its essential data.
 * The list of categories is filtered by its parent category ID and customer status permissions and for simplicity
 * it contains language specific data only in one language.
 *
 * @category   System
 * @package    Category
 * @subpackage Providers
 */
class CategoryListProvider implements CategoryListProviderInterface
{
	/**
	 * Language code.
	 * 
	 * @var LanguageCode
	 */
	protected $languageCode;

	/**
	 * Array of conditions.
	 * 
	 * @var array
	 */
	protected $conditions;

	/**
	 * Database query builder.
	 * 
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * Category Repository.
	 * 
	 * @var CategoryRepositoryInterface
	 */
	protected $categoryRepo;


	/**
	 * CategoryListProvider constructor.
	 *
	 * @param LanguageCode                $languageCode Two letter language code.
	 * @param array                       $conditions   Additional data request conditions.
	 * @param CI_DB_query_builder         $db           Database connector.
	 * @param CategoryRepositoryInterface $categoryRepo Category repository.
	 */
	public function __construct(LanguageCode $languageCode,
	                            array $conditions = array(),
	                            CategoryRepositoryInterface $categoryRepo,
	                            CI_DB_query_builder $db)
	{
		$this->languageCode = $languageCode;
		$this->conditions   = $conditions;
		$this->db           = $db;
		$this->categoryRepo = $categoryRepo;
	}


	/**
	 * Returns a category list based the parent ID provided.
	 *
	 * @param IdType $parentId Category parent ID.
	 *
	 * @return CategoryListItemCollection
	 */
	public function getByParentId(IdType $parentId)
	{
		// Build select part of query.
		$this->_select();

		// Add where condition: Select by ID.
		$this->db->where('categories.parent_id', $parentId->asInt());

		// Get query result and pass to the method which processes the result.
		$result = $this->db->get()->result_array();

		return $this->_prepareCollection($result);
	}


	/**
	 * Build the select part of the query build.
	 *
	 * @return CategoryListProvider Same instance for chained method calls. 
	 */
	protected function _select()
	{
		// Build query.
		$this->db->select('categories.*, categories_description.*')
		         ->from('categories, categories_description')
		         ->join('languages', 'languages.languages_id = categories_description.language_id', 'inner')
		         ->where('categories_description.categories_id = categories.categories_id')
		         ->where('languages.code', (string)$this->languageCode);

		// Check for additional conditions to be appended to query.
		if(count($this->conditions) > 0)
		{
			$this->db->where($this->conditions);
		}

		return $this;
	}


	/**
	 * Prepares the CategoryListItemCollection object.
	 *
	 * @param array $result Query result.
	 *
	 * @return CategoryListItemCollection
	 */
	protected function _prepareCollection(array $result)
	{
		$listItems = array();

		// Iterate over each query result row and
		// create a CategoryListItem for each row which
		// will be pushed into $listItems array.
		foreach($result as $row)
		{
			$categoryRepo         = $this->categoryRepo;
			$categoryListProvider = $this;
			$categoryId           = new IdType((int)$row['categories_id']);
			$parentId             = new IdType((int)$row['parent_id']);
			$isActive             = new BoolType((bool)$row['categories_status']);
			$name                 = new StringType((string)$row['categories_name']);
			$headingTitle         = new StringType((string)$row['categories_heading_title']);
			$description          = new StringType((string)$row['categories_description']);
			$urlKeywords          = new StringType((string)$row['categories_meta_keywords']);
			$image                = new StringType((string)$row['categories_image']);
			$imageAltTest         = new StringType((string)$row['gm_alt_text']);
			$icon                 = new StringType((string)$row['categories_icon']);

			$categoryListItem = MainFactory::create('CategoryListItem', $categoryRepo, $categoryListProvider,
			                                        $categoryId, $parentId, $isActive, $name, $headingTitle,
			                                        $description, $urlKeywords, $image, $imageAltTest, $icon);

			$listItems[] = $categoryListItem;
		}

		$collection = MainFactory::create('CategoryListItemCollection', $listItems);

		return $collection;
	}
}