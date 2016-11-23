<?php

/* --------------------------------------------------------------
   Category.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

MainFactory::load_class('CategoryInterface');

/**
 * Class Category
 * 
 * This class represents a shop category which is not persisted yet.
 *
 * @category   System
 * @package    Category
 * @subpackage Entities
 */
class Category implements CategoryInterface
{
	/**
	 * Contains if category is active or not.
	 *
	 * @var bool
	 */
	protected $active = false;
	
	
	/**
	 * Parent ID of the Category.
	 *
	 * @var int
	 */
	protected $parentId = 0;
	
	
	/**
	 * Sort order value.
	 *
	 * @var int
	 */
	protected $sortOrder = 0;
	
	
	/**
	 * Date time when the category was added.
	 *
	 * @var DateTime
	 */
	protected $dateAdded;
	
	
	/**
	 * Date time when the category was modified last.
	 *
	 * @var DateTime
	 */
	protected $lastModified;
	
	
	/**
	 * Category settings.
	 *
	 * @var CategorySettingsInterface
	 */
	protected $settings;
	
	
	/**
	 * Names of the category.
	 *
	 * @var array
	 */
	protected $names = array();
	
	
	/**
	 * Heading Titles of the category.
	 *
	 * @var array
	 */
	protected $headingTitles = array();
	
	
	/**
	 * Description of the category.
	 *
	 * @var array
	 */
	protected $descriptions = array();
	
	
	/**
	 * Meta title of the category.
	 *
	 * @var array
	 */
	protected $metaTitles = array();
	
	
	/**
	 * Meta description of the category.
	 *
	 * @var array
	 */
	protected $metaDescriptions = array();
	
	
	/**
	 * Meta keywords of the category.
	 *
	 * @var array
	 */
	protected $metaKeywords = array();
	
	
	/**
	 * Url keywords of the category.
	 *
	 * @var array
	 */
	protected $urlKeywords = array();
	
	
	/**
	 * URL rewrites.
	 *
	 * @var UrlRewriteCollection
	 */
	protected $urlRewrites;
	
	
	/**
	 * Path to an image file.
	 *
	 * @var string
	 */
	protected $image = '';
	
	
	/**
	 * Alt texts of the image.
	 *
	 * @var array
	 */
	protected $altTexts = array();
	
	
	/**
	 * Path to an icon file.
	 *
	 * @var string
	 */
	protected $icon = '';
	
	
	/**
	 * Addon values.
	 *
	 * @var EditableKeyValueCollection
	 */
	protected $addonValues;
	

	/**
	 * Category constructor.
	 *
	 * @param CategorySettingsInterface $settings The category settings.
	 */
	public function __construct(CategorySettingsInterface $settings)
	{
		$this->settings     = $settings;
		$this->dateAdded    = new DateTime();
		$this->lastModified = new DateTime();
		$this->addonValues  = MainFactory::create('EditableKeyValueCollection', array());
		$this->urlRewrites  = MainFactory::create('UrlRewriteCollection', array());
	}
	
	
	/**
	 * Checks if the category is active or not.
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return $this->active;
	}
	

	/**
	 * Sets whether category is active or not.
	 *
	 * @param BoolType $status Category active or not?
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setActive(BoolType $status)
	{
		$this->active = $status->asBool();

		return $this;
	}


	/**
	 * Returns the ID of the parent category.
	 *
	 * @return int The ID of the parent category
	 */
	public function getParentId()
	{
		return $this->parentId;
	}


	/**
	 * Sets the parent ID of the category.
	 *
	 * @param IdType $categoryId The parent ID.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setParentId(IdType $categoryId)
	{
		$this->parentId = $categoryId->asInt();

		return $this;
	}


	/**
	 * Returns the sort order value.
	 *
	 * @return int Sort order value.
	 */
	public function getSortOrder()
	{
		return $this->sortOrder;
	}


	/**
	 * Sets the sort order to the given value.
	 *
	 * @param IntType $sortOrder Order value.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setSortOrder(IntType $sortOrder)
	{
		$this->sortOrder = $sortOrder->asInt();

		return $this;
	}


	/**
	 * Returns the datetime when the category was added.
	 *
	 * @return DateTime
	 */
	public function getAddedDateTime()
	{
		return $this->dateAdded;
	}


	/**
	 * Sets the datetime when the category was added.
	 *
	 * @param DateTime $added Datetime of when the category has been added.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setAddedDateTime(DateTime $added)
	{
		$this->dateAdded = $added;

		return $this;
	}


	/**
	 * Returns the datetime when the category was modified last.
	 *
	 * @return DateTime
	 */
	public function getLastModifiedDateTime()
	{
		return $this->lastModified;
	}


	/**
	 * Sets the date time when the category was modified last.
	 *
	 * @param DateTime $modified Datetime of when the category has been lastly modified.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setLastModifiedDateTime(DateTime $modified)
	{
		$this->lastModified = $modified;

		return $this;
	}


	/**
	 * Returns the settings of the category.
	 *
	 * @return CategorySettingsInterface
	 */
	public function getSettings()
	{
		return $this->settings;
	}


	/**
	 * Returns the name of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string
	 */
	public function getName(LanguageCode $language)
	{
		return (string)$this->names[(string)$language];
	}


	/**
	 * Sets the name of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Category name.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setName(StringType $text, LanguageCode $language)
	{
		$this->names[(string)$language] = $text->asString();

		return $this;
	}


	/**
	 * Returns the heading title of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string
	 */
	public function getHeadingTitle(LanguageCode $language)
	{
		return (string)$this->headingTitles[(string)$language];
	}


	/**
	 * Sets the heading title of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Heading title of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setHeadingTitle(StringType $text, LanguageCode $language)
	{
		$this->headingTitles[(string)$language] = $text->asString();

		return $this;
	}


	/**
	 * Returns the description of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string
	 */
	public function getDescription(LanguageCode $language)
	{
		return (string)$this->descriptions[(string)$language];
	}


	/**
	 * Sets the description of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Description of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setDescription(StringType $text, LanguageCode $language)
	{
		$this->descriptions[(string)$language] = $text->asString();

		return $this;
	}


	/**
	 * Returns the meta title of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string
	 */
	public function getMetaTitle(LanguageCode $language)
	{
		return (string)$this->metaTitles[(string)$language];
	}


	/**
	 * Sets the meta title of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Meta title for the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setMetaTitle(StringType $text, LanguageCode $language)
	{
		$this->metaTitles[(string)$language] = $text->asString();

		return $this;
	}


	/**
	 * Returns the meta title of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string
	 */
	public function getMetaDescription(LanguageCode $language)
	{
		return (string)$this->metaDescriptions[(string)$language];
	}


	/**
	 * Sets the meta title of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Meta title of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setMetaDescription(StringType $text, LanguageCode $language)
	{
		$this->metaDescriptions[(string)$language] = $text->asString();

		return $this;
	}


	/**
	 * Returns the meta keywords of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string
	 */
	public function getMetaKeywords(LanguageCode $language)
	{
		return (string)$this->metaKeywords[(string)$language];
	}


	/**
	 * Sets the meta keywords of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Meta keywords of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setMetaKeywords(StringType $text, LanguageCode $language)
	{
		$this->metaKeywords[(string)$language] = $text->asString();

		return $this;
	}


	/**
	 * Returns the url keywords of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string
	 */
	public function getUrlKeywords(LanguageCode $language)
	{
		return (string)$this->urlKeywords[(string)$language];
	}


	/**
	 * Sets the url keywords of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     URL keywords of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setUrlKeywords(StringType $text, LanguageCode $language)
	{
		$this->urlKeywords[(string)$language] = $text->asString();

		return $this;
	}
	
	
	/**
	 * Get URL rewrite
	 *
	 * Returns the URL rewrite of the category, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the URL rewrite to be return.
	 *
	 * @return UrlRewrite The URL rewrite of the category.
	 */
	public function getUrlRewrite(LanguageCode $language)
	{
		return $this->urlRewrites->getValue($language->asString());
	}
	
	
	/**
	 * Get URL rewrites
	 *
	 * Returns the URL rewrites of the category.
	 *
	 * @return UrlRewriteCollection The URL rewrites of the category.
	 */
	public function getUrlRewrites()
	{
		return $this->urlRewrites;
	}
	
	
	/**
	 * Set URL rewrite
	 *
	 * Sets the URL rewrite of the category for the provided language code.
	 *
	 * @param UrlRewrite   $urlRewrite The URL rewrite instance.
	 * @param LanguageCode $language   The language code for the URL keywords.
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setUrlRewrite(UrlRewrite $urlRewrite, LanguageCode $language)
	{
		$this->urlRewrites->setValue($language->asString(), $urlRewrite);
		
		return $this;
	}
	
	
	/**
	 * Set URL rewrites
	 *
	 * Sets the URL rewrites of the category.
	 *
	 * @param UrlRewriteCollection $urlRewrites The URL rewrites.
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setUrlRewrites(UrlRewriteCollection $urlRewrites)
	{
		$this->urlRewrites = $urlRewrites;
		
		return $this;
	}


	/**
	 * Returns the image filename of the category.
	 *
	 * @return string
	 */
	public function getImage()
	{
		return $this->image;
	}


	/**
	 * Sets the image filename of the category.
	 *
	 * @param StringType $imageFile Category image file.
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setImage(StringType $imageFile)
	{
		$this->image = $imageFile->asString();

		return $this;
	}


	/**
	 * Returns the alternative text of the image in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string
	 */
	public function getImageAltText(LanguageCode $language)
	{
		return (string)$this->altTexts[(string)$language];
	}


	/**
	 * Set the alternative text of the image for the language with the provided language code.
	 *
	 * @param StringType   $text     Alternative text of the category image.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setImageAltText(StringType $text, LanguageCode $language)
	{
		$this->altTexts[(string)$language] = $text->asString();

		return $this;
	}


	/**
	 * Returns the icon filename of the category.
	 *
	 * @return string
	 */
	public function getIcon()
	{
		return $this->icon;
	}


	/**
	 * Sets the icon filename of the category.
	 *
	 * @param StringType $iconFile Category icon file.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setIcon(StringType $iconFile)
	{
		$this->icon = $iconFile->asString();

		return $this;
	}

	/*
	 * ====================================================
	 * TODO: Implement getIconWidth() and getIconHeight()
	 * ====================================================
	 */

	/**
	 * Returns an addon value by a given key.
	 *
	 * @param StringType $key Identifier key.
	 *
	 * @return string
	 */
	public function getAddonValue(StringType $key)
	{
		return $this->addonValues->getValue($key->asString());
	}


	/**
	 * Sets an addon key and value.
	 *
	 * @param StringType $key   Identifier key.
	 * @param StringType $value Value text.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function setAddonValue(StringType $key, StringType $value)
	{
		$this->addonValues->setValue($key->asString(), $value->asString());

		return $this;
	}


	/**
	 * Returns a KeyValueCollection of addon values.
	 *
	 * @return KeyValueCollection
	 */
	public function getAddonValues()
	{
		return $this->addonValues;
	}


	/**
	 * Adds a KeyValueCollection of addon values.
	 *
	 * @param KeyValueCollection $collection Key-Value collection.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function addAddonValues(KeyValueCollection $collection)
	{
		$this->addonValues->addCollection($collection);

		return $this;
	}


	/**
	 * Deletes an addon value with a given key from the KeyValueCollection.
	 *
	 * @param StringType $key Identifier key.
	 *
	 * @return Category Same instance for chained method calls. 
	 */
	public function deleteAddonValue(StringType $key)
	{
		$this->addonValues->deleteValue($key->asString());

		return $this;
	}
	
	
	/**
	 * Sets a category setting object
	 * 
	 * @param CategorySettingsInterface $settings
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setSettings(CategorySettingsInterface $settings)
	{
		$this->settings = $settings;
		
		return $this;
	}
}