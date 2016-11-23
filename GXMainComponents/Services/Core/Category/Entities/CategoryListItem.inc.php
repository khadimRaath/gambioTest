<?php

/* --------------------------------------------------------------
   CategoryListItem.inc.php 2015-11-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryListItem
 *
 * This class represents a flattened category with just its essential data.
 * The object stores language specific data only in one language and is mainly used inside a CategoryListItemCollection
 * for displaying among other CategoryListItems in a list.
 * The class provides only read access and can not use to manipulate and persist categories data.
 *
 * @category   System
 * @package    Category
 * @subpackage Entities
 */
class CategoryListItem
{
	/**
	 * Category repository.
	 * 
	 * @var CategoryRepositoryInterface
	 */
	protected $categoryRepo;
	
	/**
	 * Category list provider.
	 * 
	 * @var CategoryListProviderInterface
	 */
	protected $categoryListProvider;
	
	/**
	 * Category ID.
	 * 
	 * @var int
	 */
	protected $categoryId;
	
	/**
	 * Category parent ID.
	 * 
	 * @var int
	 */
	protected $parentId;
	
	/**
	 * Value whether category is active?
	 * 
	 * @var bool
	 */
	protected $active;
	
	/**
	 * Category name.
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * Category heading title.
	 * 
	 * @var string
	 */
	protected $headingTitle;
	
	/**
	 * Category description.
	 * 
	 * @var string
	 */
	protected $description;
	
	/**
	 * Category URL keywords.
	 * 
	 * @var string
	 */
	protected $urlKeywords;
	
	/**
	 * Category image.
	 * 
	 * @var string
	 */
	protected $image;
	
	/**
	 * Image alternative text.
	 * 
	 * @var string
	 */
	protected $imageAltText;
	
	/**
	 * Category icon file.
	 * 
	 * @var string
	 */
	protected $icon;
	
	
	/**
	 * CategoryListItem constructor.
	 *
	 * @param CategoryRepositoryInterface   $categoryRepo         Category repository.
	 * @param CategoryListProviderInterface $categoryListProvider Category list provider.
	 * @param IdType                        $categoryId           Category ID.
	 * @param IdType                        $parentId             Category parent ID.
	 * @param BoolType                      $active               Is category active?
	 * @param StringType                    $name                 Category name.
	 * @param StringType                    $headingTitle         Category heading title.
	 * @param StringType                    $description          Category description.
	 * @param StringType                    $urlKeywords          URL keywords.
	 * @param StringType                    $image                Category image.
	 * @param StringType                    $imageAltText         Image alternative text.
	 * @param StringType                    $icon                 Category icon.
	 */
	public function __construct(CategoryRepositoryInterface $categoryRepo,
	                            CategoryListProviderInterface $categoryListProvider,
	                            IdType $categoryId,
	                            IdType $parentId,
	                            BoolType $active,
	                            StringType $name,
	                            StringType $headingTitle,
	                            StringType $description,
	                            StringType $urlKeywords,
	                            StringType $image,
	                            StringType $imageAltText,
	                            StringType $icon)
	{
		
		$this->categoryRepo         = $categoryRepo;
		$this->categoryListProvider = $categoryListProvider;
		$this->categoryId           = $categoryId->asInt();
		$this->parentId             = $parentId->asInt();
		$this->active               = $active->asBool();
		$this->name                 = $name->asString();
		$this->headingTitle         = $headingTitle->asString();
		$this->description          = $description->asString();
		$this->urlKeywords          = $urlKeywords->asString();
		$this->image                = $image->asString();
		$this->imageAltText         = $imageAltText->asString();
		$this->icon                 = $icon->asString();
	}
	
	
	/**
	 * Returns the category ID.
	 * 
	 * @return int
	 */
	public function getCategoryId()
	{
		return $this->categoryId;
	}
	
	
	/**
	 * Returns the category parent ID.
	 * 
	 * @return int
	 */
	public function getParentId()
	{
		return $this->parentId;
	}
	
	
	/**
	 * Returns the value whether the category is active.
	 * 
	 * @return boolean
	 */
	public function isActive()
	{
		return $this->active;
	}
	
	
	/**
	 * Returns the category name.
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	
	/**
	 * Returns the category heading title.
	 * 
	 * @return string
	 */
	public function getHeadingTitle()
	{
		return $this->headingTitle;
	}
	
	
	/**
	 * Returns the category description.
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
	
	/**
	 * Returns the URL keywords.
	 * 
	 * @return string
	 */
	public function getUrlKeywords()
	{
		return $this->urlKeywords;
	}
	
	
	/**
	 * Returns the category image.
	 * 
	 * @return string
	 */
	public function getImage()
	{
		return $this->image;
	}
	
	
	/**
	 * Returns the image alternative text.
	 * 
	 * @return string
	 */
	public function getImageAltText()
	{
		return $this->imageAltText;
	}
	
	
	/**
	 * Returns the category icon.
	 * 
	 * @return string
	 */
	public function getIcon()
	{
		return $this->icon;
	}
	
	
	/**
	 * Returns the category object.
	 * 
	 * @return CategoryInterface
	 */
	public function getCategoryObject()
	{
		$id = new IdType($this->getCategoryId());

		return $this->categoryRepo->getCategoryById($id);
	}
	
	
	/**
	 * Returns the subcategory collection.
	 * 
	 * @return CategoryListItemCollection
	 */
	public function getSubcategoryList()
	{
		$id = new IdType($this->getCategoryId());

		return $this->categoryListProvider->getByParentId($id);
	}
}