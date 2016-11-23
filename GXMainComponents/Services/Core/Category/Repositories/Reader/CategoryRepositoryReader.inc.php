<?php
/* --------------------------------------------------------------
   CategoryRepositoryReader.inc.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryRepositoryReader
 *
 * This class provides methods for fetching specific category records from the database and is used in the category
 * repository among the classes for writing and deleting category records.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategoryRepositoryReader implements CategoryRepositoryReaderInterface
{
	/**
	 * Database connector.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	/**
	 * Category factory.
	 *
	 * @var CategoryFactoryInterface
	 */
	protected $categoryFactory;
	
	
	/**
	 * CategoryRepositoryReader constructor.
	 *
	 * @param CI_DB_query_builder      $db              Database connector.
	 * @param CategoryFactoryInterface $categoryFactory Category factory.
	 */
	public function __construct(CI_DB_query_builder $db, CategoryFactoryInterface $categoryFactory)
	{
		$this->db              = $db;
		$this->categoryFactory = $categoryFactory;
	}
	
	
	/**
	 * Returns a category.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @throws UnexpectedValueException if no category record for the provided category ID was found.
	 * 
	 * @return StoredCategoryInterface
	 */
	public function getById(IdType $categoryId)
	{
		// Query the categories table.
		$categoryData = $this->db->get_where('categories', array(
			'categories_id' => $categoryId->asInt()
		))->row_array();
		
		// Get language specific context.
		$categoryDescriptionQuery = $this->db->select('categories_description.*, languages.code AS language_code')
		                                     ->from('categories_description')
		                                     ->join('languages',
		                                            'languages.languages_id = categories_description.language_id',
		                                            'inner')
		                                     ->where('categories_description.categories_id', $categoryId->asInt());
		
		$categoryDescription = $categoryDescriptionQuery->get()->result_array();
		
		if($categoryData === null)
		{
			throw new UnexpectedValueException('The requested category was not found in database (ID:'
			                                   . $categoryId->asInt() . ')');
		}
		
		if($categoryDescription === null)
		{
			throw new UnexpectedValueException('The requested category description was not found in database (ID:'
			                                   . $categoryId->asInt() . ')');
		}
		
		$category = $this->_createCategoryByArray($categoryData, $categoryDescription);
		
		return $category;
	}
	
	
	/**
	 * Returns all Categories with the provided parent ID.
	 * 
	 * @param IdType $parentId
	 *
	 * @return IdCollection
	 */
	public function getByParentId(IdType $parentId)
	{
		$subCategories = array();
		$result = $this->db->select('categories_id')
		                   ->get_where('categories', array('parent_id' => $parentId->asInt()))
		                   ->result_array();
		
		foreach($result as $row)
		{
			$categoryId = new IdType($row['categories_id']);
			$subCategories[] = $categoryId;
		}
		
		$idCollection = new IdCollection($subCategories);
		
		return $idCollection;
	}
	
	
	/**
	 * Creates a category instance.
	 *
	 * @param array $categoryData            Category query result.
	 * @param array $categoryDescriptionData Category description query result.
	 *
	 * @return StoredCategory Returns the complete category object.
	 *                        
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	protected function _createCategoryByArray(array $categoryData, array $categoryDescriptionData)
	{
		$category = $this->categoryFactory->createStoredCategory(new IdType($categoryData['categories_id']));
		$category->setActive(new BoolType((boolean)$categoryData['categories_status']));
		$category->setParentId(new IdType($categoryData['parent_id']));
		$category->setSortOrder(new IntType((int)$categoryData['sort_order']));
		$category->setAddedDateTime(new EmptyDateTime($categoryData['date_added']));
		$category->setLastModifiedDateTime(new EmptyDateTime($categoryData['last_modified']));
		$category->setImage(new StringType((string)$categoryData['categories_image']));
		$category->setIcon(new StringType((string)$categoryData['categories_icon']));
		
		// Set language specific data.
		foreach($categoryDescriptionData as $row)
		{
			$languageCode = new LanguageCode(new NonEmptyStringType((string)$row['language_code']));
			
			$category->setName(new StringType((string)$row['categories_name']), $languageCode);
			$category->setHeadingTitle(new StringType((string)$row['categories_heading_title']), $languageCode);
			$category->setDescription(new StringType((string)$row['categories_description']), $languageCode);
			$category->setMetaTitle(new StringType((string)$row['categories_meta_title']), $languageCode);
			$category->setMetaDescription(new StringType((string)$row['categories_meta_description']), $languageCode);
			$category->setMetaKeywords(new StringType((string)$row['categories_meta_keywords']), $languageCode);
			$category->setUrlKeywords(new StringType((string)$row['gm_url_keywords']), $languageCode);
			$category->setImageAltText(new StringType((string)$row['gm_alt_text']), $languageCode);
		}
		
		return $category;
	}
}