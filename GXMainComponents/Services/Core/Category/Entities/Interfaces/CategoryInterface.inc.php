<?php

/* --------------------------------------------------------------
   CategoryInterface.inc.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/


/**
 * Interface CategoryInterface
 *
 * @category   System
 * @package    Category
 * @subpackage Interfaces
 */
interface CategoryInterface
{
	/**
	 * Is tax of the OrderItem allowed?
	 *
	 * @return bool Value if tax of the OrderItem allowed.
	 */
	public function isActive();


	/**
	 * Sets whether category is active or not.
	 *
	 * @param BoolType $status Category active or not?
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setActive(BoolType $status);


	/**
	 * Returns the ID of the parent category.
	 *
	 * @return int The ID of the parent category
	 */
	public function getParentId();


	/**
	 * Sets the parent ID of the category.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setParentId(IdType $categoryId);


	/**
	 * Returns the sort order value.
	 *
	 * @return int Sort order value.
	 */
	public function getSortOrder();


	/**
	 * Sets the sort order to the given value.
	 *
	 * @param IntType $sortOrder Sorting order.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setSortOrder(IntType $sortOrder);


	/**
	 * Returns the date time when the category was added.
	 *
	 * @return DateTime The date time when the category was added.
	 */
	public function getAddedDateTime();


	/**
	 * Sets the date time when the category was added.
	 *
	 * @param DateTime $added DateTime when the category has been added.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setAddedDateTime(DateTime $added);


	/**
	 * Returns the date time when the category was modified last.
	 *
	 * @return DateTime The date time when the category was modified last.
	 */
	public function getLastModifiedDateTime();


	/**
	 * Sets the date time when the category was modified last.
	 *
	 * @param DateTime $modified DateTime when the category has been lastly modified.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setLastModifiedDateTime(DateTime $modified);


	/**
	 * Returns the settings of the category.
	 *
	 * @return CategorySettingsInterface Settings of the category.
	 */
	public function getSettings();
	
	
	/**
	 * Sets the settings of the category.
	 * 
	 * @param CategorySettingsInterface $categorySettings The settings of the category.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setSettings(CategorySettingsInterface $categorySettings);


	/**
	 * Returns the name of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter Language code.
	 *
	 * @return string Name of the category from the given language code.
	 */
	public function getName(LanguageCode $language);


	/**
	 * Sets the name of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Name of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setName(StringType $text, LanguageCode $language);


	/**
	 * Returns the heading title of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string Heading title of the category from the given language code.
	 */
	public function getHeadingTitle(LanguageCode $language);


	/**
	 * Sets the heading title of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Heading title of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setHeadingTitle(StringType $text, LanguageCode $language);


	/**
	 * Returns the description of the category for the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string Description of the category from the given language code.
	 */
	public function getDescription(LanguageCode $language);


	/**
	 * Sets the description of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Description of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setDescription(StringType $text, LanguageCode $language);


	/**
	 * Returns the meta title of the category for the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string Meta title of the category from the given language code
	 */
	public function getMetaTitle(LanguageCode $language);


	/**
	 * Sets the meta title of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Meta title of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setMetaTitle(StringType $text, LanguageCode $language);


	/**
	 * Returns the meta description of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string Meta description of the category from the given language code.
	 */
	public function getMetaDescription(LanguageCode $language);


	/**
	 * Sets the meta description of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     Meta description of the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setMetaDescription(StringType $text, LanguageCode $language);


	/**
	 * Returns the meta keywords of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string Meta keywords of the category from the given language code.
	 */
	public function getMetaKeywords(LanguageCode $language);


	/**
	 * Sets the meta keywords of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     The meta keyword for the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setMetaKeywords(StringType $text, LanguageCode $language);


	/**
	 * Returns the url keywords of the category in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string URL keywords of the category from the given language code.
	 */
	public function getUrlKeywords(LanguageCode $language);


	/**
	 * Sets the url keywords of the category for the language with the provided language code.
	 *
	 * @param StringType   $text     URL Keyword for the category.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setUrlKeywords(StringType $text, LanguageCode $language);
	
	
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
	public function getUrlRewrite(LanguageCode $language);
	
	
	/**
	 * Get URL rewrites
	 *
	 * Returns the URL rewrites of the category.
	 *
	 * @return UrlRewriteCollection The URL rewrites of the category.
	 */
	public function getUrlRewrites();
	
	
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
	public function setUrlRewrite(UrlRewrite $urlRewrite, LanguageCode $language);
	
	
	/**
	 * Set URL rewrites
	 *
	 * Sets the URL rewrites of the category.
	 *
	 * @param UrlRewriteCollection $urlRewrites The URL rewrites.
	 *
	 * @return Category Same instance for chained method calls.
	 */
	public function setUrlRewrites(UrlRewriteCollection $urlRewrites);


	/**
	 * Returns the image filename of the category.
	 *
	 * @return string The image filename of the category.
	 */
	public function getImage();


	/**
	 * Sets the image filename of the category.
	 *
	 * @param StringType $imageFile Image filename.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setImage(StringType $imageFile);


	/**
	 * Returns the alternative text of the image in the language with the provided language code.
	 *
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return string The alt text of the image.
	 */
	public function getImageAltText(LanguageCode $language);


	/**
	 * Set the alternative text of the image for the language with the provided language code.
	 *
	 * @param StringType   $text     Alternative text for the image.
	 * @param LanguageCode $language Two letter language code.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setImageAltText(StringType $text, LanguageCode $language);


	/**
	 * Returns the icon filename of the category.
	 *
	 * @return string The icon filename of the category.
	 */
	public function getIcon();


	/**
	 * Sets the icon filename of the category.
	 *
	 * @param StringType $iconFile Icon filename.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setIcon(StringType $iconFile);


	/**
	 * Returns an addon value by a given key.
	 *
	 * @param StringType $key Identifier key.
	 *
	 * @return string Addon value by a given key.
	 */
	public function getAddonValue(StringType $key);


	/**
	 * Sets an addon key and value.
	 *
	 * @param StringType $key   Identifier key.
	 * @param StringType $value The value to be saved.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function setAddonValue(StringType $key, StringType $value);


	/**
	 * Returns a KeyValueCollection of addon values.
	 *
	 * @return KeyValueCollection A KeyValueCollection of addon values.
	 */
	public function getAddonValues();


	/**
	 * Adds a KeyValueCollection of addon values.
	 *
	 * @param KeyValueCollection $collection Key value collection.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function addAddonValues(KeyValueCollection $collection);


	/**
	 * Deletes an addon value with a given key from the KeyValueCollection.
	 *
	 * @param StringType $key Identifier key.
	 *
	 * @return CategoryInterface Same instance for chained method calls.
	 */
	public function deleteAddonValue(StringType $key);
}