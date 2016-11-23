<?php

/* --------------------------------------------------------------
   CategoryRepositoryWriter.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryRepositoryWriter
 * 
 * This class provides methods for creating and updating specific category records in the database and is used in the category
 * repository among the classes for reading and deleting category records.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategoryRepositoryWriter implements CategoryRepositoryWriterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $categoriesTable = 'categories';

	/**
	 * @var LanguageProviderInterface $languageProvider
	 */
	protected $languageProvider;


	/**
	 * CategoryRepositoryWriter constructor.
	 *
	 * @param CI_DB_query_builder       $db Database connector.
	 * @param LanguageProviderInterface $languageProvider
	 */
	public function __construct(CI_DB_query_builder $db, LanguageProviderInterface $languageProvider)
	{
		$this->db               = $db;
		$this->languageProvider = $languageProvider;
	}


	/**
	 * Inserts a category record into the database accordingly to the provided category object and returns the ID from
	 * the saved entity.
	 *
	 * @param CategoryInterface $category The category to insert.
	 *
	 * @return int Returns the ID of the new category.
	 *             
	 * @throws UnexpectedValueException When no language id was found by the given language code.
	 * @throws InvalidArgumentException
	 */
	public function insert(CategoryInterface $category)
	{
		// Insert the category. 
		$categoryDataArray = $this->_parseCategoryData($category);

		$this->db->insert($this->categoriesTable, $categoryDataArray);

		$categoryId = $this->db->insert_id();

		// Insert the category description. 
		foreach($this->languageProvider->getCodes() as $languageCode)
		{
			$languageId = $this->languageProvider->getIdByCode($languageCode);

			$categoryDescriptionDataArray                  = $this->_parseCategoryDescriptionData($category, $languageCode);
			$categoryDescriptionDataArray['language_id']   = $languageId;
			$categoryDescriptionDataArray['categories_id'] = $categoryId;

			$this->db->insert('categories_description', $categoryDescriptionDataArray);
		}

		return $categoryId;
	}


	/**
	 * Updates an existing category record accordingly to the provided category object.
	 *
	 * @param StoredCategoryInterface $category The category to update.
	 *
	 * @return CategoryRepositoryWriter Same instance for chained method calls.
	 *                                  
	 * @throws UnexpectedValueException When no language id was found by the given language code.
	 * @throws InvalidArgumentException
	 */
	public function update(StoredCategoryInterface $category)
	{
		// Update the category. 
		$categoryDataArray = $this->_parseCategoryData($category);
		$this->db->update($this->categoriesTable,
		                  $categoryDataArray,
		                  array('categories_id' => $category->getCategoryId()));

		// Update the category descriptions. 
		foreach($this->languageProvider->getCodes() as $languageCode)
		{
			$languageId = $this->languageProvider->getIdByCode($languageCode);

			$categoryDescriptionDataArray = array_merge(array(
				                                           'categories_id' => $category->getCategoryId(),
				                                           'language_id' => $languageId
			                                           ), $this->_parseCategoryDescriptionData($category, $languageCode));
			
			$this->db->replace('categories_description', $categoryDescriptionDataArray);
		}

		return $this;
	}


	protected function _parseCategoryData(CategoryInterface $category)
	{
		$categoryDataArray = array(
			'categories_image'     => $category->getImage(),
			'parent_id'            => $category->getParentId(),
			'categories_status'    => $category->isActive(),
			'sort_order'           => $category->getSortOrder(),
			'date_added'           => $category->getAddedDateTime()->format('Y-m-d H:i:s'),
			'last_modified'        => $category->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
			'categories_icon'      => $category->getIcon()
		);

		return $categoryDataArray;
	}


	protected function _parseCategoryDescriptionData(CategoryInterface $category, LanguageCode $languageCode)
	{
		$categoryDescriptionDataArray = array(
			'categories_name'             => $category->getName($languageCode),
			'categories_heading_title'    => $category->getHeadingTitle($languageCode),
			'categories_description'      => $category->getDescription($languageCode),
			'categories_meta_title'       => $category->getMetaTitle($languageCode),
			'categories_meta_description' => $category->getMetaDescription($languageCode),
			'categories_meta_keywords'    => $category->getMetaKeywords($languageCode),
			'gm_alt_text'                 => $category->getImageAltText($languageCode),
			'gm_url_keywords'             => $category->getUrlKeywords($languageCode),
		);

		return $categoryDescriptionDataArray;
	}
}