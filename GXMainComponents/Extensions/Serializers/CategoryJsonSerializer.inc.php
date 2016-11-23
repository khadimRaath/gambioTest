<?php

/* --------------------------------------------------------------
  CategoryJsonSerializer.inc.php 2016-04-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class CategoryJsonSerializer
 *
 * This class will serialize and deserialize a Category entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class CategoryJsonSerializer extends AbstractJsonSerializer
{
	/**
	 * Serialize a Category object to a JSON string.
	 *
	 * @param StoredCategoryInterface $object  Object instance to be serialized.
	 * @param bool                    $encode  (optional) Whether to json_encode the result of the method (default
	 *                                         true).
	 *
	 * @return string|array Returns the json encoded category (string) or an array that can be easily encoded into a
	 *                      JSON string.
	 * @throws InvalidArgumentException If the provided object type is invalid.
	 * @throws RuntimeException Through the _serializeLanguageSpecificProperty method.
	 */
	public function serialize($object, $encode = true)
	{
		if(!is_a($object, 'CategoryInterface'))
		{
			throw new InvalidArgumentException('Invalid argument provided, StoredCategoryInterface object required: '
			                                   . get_class($object));
		}

		$category = array(
			'id'              => is_a($object, 'StoredCategoryInterface') ?  $object->getCategoryId() : null,
			'parentId'        => $object->getParentId(),
			'isActive'        => $object->isActive(),
			'sortOrder'       => $object->getSortOrder(),
			'dateAdded'       => $object->getAddedDateTime()->format('Y-m-d H:i:s'),
			'lastModified'    => $object->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
			'name'            => $this->_serializeLanguageSpecificProperty($object, 'name'),
			'headingTitle'    => $this->_serializeLanguageSpecificProperty($object, 'headingTitle'),
			'description'     => $this->_serializeLanguageSpecificProperty($object, 'description'),
			'metaTitle'       => $this->_serializeLanguageSpecificProperty($object, 'metaTitle'),
			'metaDescription' => $this->_serializeLanguageSpecificProperty($object, 'metaDescription'),
			'metaKeywords'    => $this->_serializeLanguageSpecificProperty($object, 'metaKeywords'),
			'urlKeywords'     => $this->_serializeLanguageSpecificProperty($object, 'urlKeywords'),
			'icon'            => $object->getIcon(),
			'image'           => $object->getImage(),
			'imageAltText'    => $this->_serializeLanguageSpecificProperty($object, 'imageAltText'),
			'settings'        => $this->_serializeSettings($object->getSettings()),
			'addonValues'     => $this->_serializeAddonValues($object->getAddonValues())
		);

		return ($encode) ? $this->jsonEncode($category) : $category;
	}


	/**
	 * Deserialize a Category JSON String.
	 *
	 * @param string $string     JSON string that contains the data of the category.
	 * @param object $baseObject (optional) If provided, this will be the base object to be updated
	 *                           and no new instance will be created.
	 *
	 * @return CategoryInterface Returns the deserialized Category object.
	 * @throws InvalidArgumentException If the argument is not a string or is empty.
	 */
	public function deserialize($string, $baseObject = null)
	{
		if(!is_string($string) || empty($string))
		{
			throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($string));
		}

		$json = json_decode($string); // error for malformed json strings

		if($json === null && json_last_error() > 0)
		{
			throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: ' . $string);
		}

		if(!$baseObject)
		{
			$categorySettings = MainFactory::create('CategorySettings');
			$category         = MainFactory::create('Category', $categorySettings);
		}
		else
		{
			$category = $baseObject;
		}

		// Deserialize JSON String 

		if($json->parentId !== null)
		{
			$category->setParentId(new IdType($json->parentId));
		}

		if($json->isActive !== null)
		{
			$category->setActive(new BoolType($json->isActive));
		}

		if($json->sortOrder !== null)
		{
			$category->setSortOrder(new IntType($json->sortOrder));
		}

		if($json->dateAdded !== null)
		{
			$category->setAddedDateTime(new EmptyDateTime($json->dateAdded));
		}

		if($json->lastModified !== null)
		{
			$category->setLastModifiedDateTime(new EmptyDateTime($json->lastModified));
		}

		if($json->settings !== null)
		{
			$category->setSettings($this->_deserializeSettings($category->getSettings(), $json->settings));
		}

		if($json->name !== null)
		{
			$this->_deserializeLanguageSpecificProperty($category, $json->name, 'name');
		}

		if($json->headingTitle !== null)
		{
			$this->_deserializeLanguageSpecificProperty($category, $json->headingTitle, 'headingTitle');
		}

		if($json->description !== null)
		{
			$this->_deserializeLanguageSpecificProperty($category, $json->description, 'description');
		}

		if($json->metaTitle !== null)
		{
			$this->_deserializeLanguageSpecificProperty($category, $json->metaTitle, 'metaTitle');
		}

		if($json->metaDescription !== null)
		{
			$this->_deserializeLanguageSpecificProperty($category, $json->metaDescription, 'metaDescription');
		}

		if($json->metaKeywords !== null)
		{
			$this->_deserializeLanguageSpecificProperty($category, $json->metaKeywords, 'metaKeywords');
		}

		if($json->urlKeywords !== null)
		{
			$this->_deserializeLanguageSpecificProperty($category, $json->urlKeywords, 'urlKeywords');
		}
		
		if($json->icon !== null)
		{
			$category->setIcon(new StringType($json->icon));
		}
		
		if($json->image !== null)
		{
			$category->setImage(new StringType($json->image));
		}

		if($json->imageAltText !== null)
		{
			$this->_deserializeLanguageSpecificProperty($category, $json->imageAltText, 'imageAltText');
		}

		if($json->addonValues !== null)
		{
			$categoryAddonValuesArray      = $this->_deserializeAddonValues($json->addonValues);
			$categoryAddonValuesCollection = MainFactory::create('EditableKeyValueCollection',
			                                                     $categoryAddonValuesArray);

			$category->addAddonValues($categoryAddonValuesCollection);
		}

		return $category;
	}


	protected function _serializeSettings(CategorySettingsInterface $settings)
	{
		$databaseQueryBuilder   = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$customerStatusProvider = MainFactory::create('CustomerStatusProvider', $databaseQueryBuilder);
		$customerStatusGroups   = $customerStatusProvider->getCustomerStatusIds();

		$permissions = array();
		foreach($customerStatusGroups as $groupId)
		{
			$permissions[] = array(
				'id'          => (int)$groupId,
				'isPermitted' => $settings->isPermittedCustomerStatus(new IdType($groupId))
			);
		}

		$serializedSettings = array(
			'categoryListingTemplate' => $settings->getCategoryListingTemplate(),
			'productListingTemplate'  => $settings->getProductListingTemplate(),
			'sortColumn'              => $settings->getProductSortColumn(),
			'sortDirection'           => $settings->getProductSortDirection(),
			'onSitemap'               => $settings->isSitemapEntry(),
			'sitemapPriority'         => $settings->getSitemapPriority(),
			'sitemapChangeFrequency'  => $settings->getSitemapChangeFreq(),
			'showAttributes'          => $settings->showAttributes(),
			'showGraduatedPrice'      => $settings->showGraduatedPrices(),
			'showQuantity'            => $settings->showQuantityInput(),
			'showQuantityInfo'        => $settings->showStock(),
			'showSubCategories'       => $settings->showSubcategories(),
			'showSubCategoryImages'   => $settings->showSubcategoryImages(),
			'showSubCategoryNames'    => $settings->showSubcategoryNames(),
			'showSubCategoryProducts' => $settings->showSubcategoryProducts(),
			'isViewModeTiled'         => $settings->isDefaultViewModeTiled(),
			'showCategoryFilter'      => $settings->showCategoryFilter(),
			'filterSelectionMode'     => $settings->getFilterSelectionMode(),
			'filterValueDeactivation' => $settings->getFilterValueDeactivation(),
			'groupPermissions'        => $permissions
		);

		return $serializedSettings;
	}


	protected function _deserializeSettings(CategorySettingsInterface $settings, $json)
	{
		if($json->categoryListingTemplate !== null)
		{
			$settings->setCategoryListingTemplate(new StringType($json->categoryListingTemplate));
		}

		if($json->productListingTemplate !== null)
		{
			$settings->setProductListingTemplate(new StringType($json->productListingTemplate));
		}

		if($json->sortColumn !== null)
		{
			$settings->setProductSortColumn(new StringType($json->sortColumn));
		}

		if($json->sortDirection !== null)
		{
			$settings->setProductSortDirection(new StringType($json->sortDirection));
		}

		if($json->onSitemap !== null)
		{
			$settings->setSitemapEntry(new BoolType($json->onSitemap));
		}

		if($json->sitemapPriority !== null)
		{
			$settings->setSitemapPriority(new StringType($json->sitemapPriority));
		}

		if($json->sitemapChangeFrequency !== null)
		{
			$settings->setSitemapChangeFreq(new StringType($json->sitemapChangeFrequency));
		}

		if($json->showAttributes !== null)
		{
			$settings->setShowAttributes(new BoolType($json->showAttributes));
		}

		if($json->showGraduatedPrice !== null)
		{
			$settings->setShowGraduatedPrices(new BoolType($json->showGraduatedPrice));
		}

		if($json->showQuantity !== null)
		{
			$settings->setShowQuantityInput(new BoolType($json->showQuantity));
		}

		if($json->showQuantityInfo !== null)
		{
			$settings->setShowStock(new BoolType($json->showQuantityInfo));
		}

		if($json->showSubCategories !== null)
		{
			$settings->setShowSubcategories(new BoolType($json->showSubCategories));
		}

		if($json->showSubCategoryImages !== null)
		{
			$settings->setShowSubcategoryImages(new BoolType($json->showSubCategoryImages));
		}

		if($json->showSubCategoryNames !== null)
		{
			$settings->setShowSubcategoryNames(new BoolType($json->showSubCategoryNames));
		}

		if($json->showSubCategoryProducts !== null)
		{
			$settings->setShowSubcategoryProducts(new BoolType($json->showSubCategoryProducts));
		}

		if($json->isViewModeTiled !== null)
		{
			$settings->setDefaultViewModeTiled(new BoolType($json->isViewModeTiled));
		}
		
		if($json->showCategoryFilter !== null)
		{
			$settings->setShowCategoryFilter(new BoolType($json->showCategoryFilter));
		}
		
		if($json->filterSelectionMode !== null)
		{
			$settings->setFilterSelectionMode(new IntType($json->filterSelectionMode));
		}
		
		if($json->filterValueDeactivation !== null)
		{
			$settings->setFilterValueDeactivation(new IntType($json->filterValueDeactivation));
		}

		if($json->groupPermissions !== null)
		{
			foreach($json->groupPermissions as $item)
			{
				$settings->setPermittedCustomerStatus(new IdType($item->id), new BoolType($item->isPermitted));
			}
		}

		return $settings;
	}
}