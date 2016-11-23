<?php

/* --------------------------------------------------------------
   ProductInterface.inc.php 2016-04-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductInterface
{
	/**
	 * Is Active
	 *
	 * Checks if a product is active.
	 *
	 * @return bool
	 */
	public function isActive();
	
	
	/**
	 * Get Sort Order
	 *
	 * Returns an integer which represents a specific sort order.
	 *
	 * @return int The sort order.
	 */
	public function getSortOrder();
	
	
	/**
	 * Get Available Date Time
	 *
	 * Returns the available date time of the product.
	 *
	 * @return DateTime The available date time.
	 */
	public function getAvailableDateTime();
	
	
	/**
	 * Get Added Date Time
	 *
	 * Returns the added date time of the product.
	 *
	 * @return DateTime The added date time.
	 */
	public function getAddedDateTime();
	
	
	/**
	 * Get Last Modified Date Time
	 *
	 * Returns the last modified date time.
	 *
	 * @return DateTime The last modified date time.
	 */
	public function getLastModifiedDateTime();
	
	
	/**
	 * Get View Count
	 *
	 * Returns the current view count of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException
	 *
	 * @param LanguageCode $language The language code of the language to be returned.
	 *
	 * @return int The current view count.
	 */
	public function getViewedCount(LanguageCode $language);
	
	
	/**
	 * Get Ordered Count
	 *
	 * Returns the ordered count of the product.
	 *
	 * @return int The ordered count.
	 */
	public function getOrderedCount();
	
	
	/**
	 * Get Product Settings.
	 *
	 * Returns the product settings.
	 *
	 * @return ProductSettingsInterface
	 */
	public function getSettings();
	
	
	/**
	 * Get Name
	 *
	 * Returns the name of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The name of the product.
	 */
	public function getName(LanguageCode $language);
	
	
	/**
	 * Get Description
	 *
	 * Returns the description of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The description of the product.
	 */
	public function getDescription(LanguageCode $language);
	
	
	/**
	 * Get Short Description
	 *
	 * Returns the short description of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The short description of the product.
	 */
	public function getShortDescription(LanguageCode $language);
	
	
	/**
	 * Get Keywords
	 *
	 * Returns the keywords of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The keywords of the product.
	 */
	public function getKeywords(LanguageCode $language);
	
	
	/**
	 * Get Meta Title
	 *
	 * Returns the meta title of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The meta title of the product.
	 */
	public function getMetaTitle(LanguageCode $language);
	
	
	/**
	 * Get Meta Description
	 *
	 * Returns the meta description of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The meta description of the product.
	 */
	public function getMetaDescription(LanguageCode $language);
	
	
	/**
	 * Get Meta Keywords
	 *
	 * Returns the meta keywords of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The meta keywords of the product.
	 */
	public function getMetaKeywords(LanguageCode $language);
	
	
	/**
	 * Get Url
	 *
	 * Returns the URL of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string Product URL
	 */
	public function getUrl(LanguageCode $language);
	
	
	/**
	 * Get URL Keywords
	 *
	 * Returns the URL keywords of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The URL keywords of the product.
	 */
	public function getUrlKeywords(LanguageCode $language);
	
	
	/**
	 * Get URL rewrite
	 *
	 * Returns the URL rewrite of the product, depending on the provided language code.
	 *
	 * @throws InvalidArgumentException if the language code is not valid.
	 *
	 * @param LanguageCode $language The language code of the URL rewrite to be return.
	 *
	 * @return UrlRewrite The URL rewrite of the product.
	 */
	public function getUrlRewrite(LanguageCode $language);
	
	
	/**
	 * Get URL rewrites
	 *
	 * Returns the URL rewrites of the product.
	 *
	 * @return UrlRewriteCollection The URL rewrites of the product.
	 */
	public function getUrlRewrites();
	
	
	/**
	 * Get Checkout Information
	 *
	 * Returns the checkout information of the product, depending on the provided language code.
	 *
	 * @param LanguageCode $language The language code of the language to return.
	 *
	 * @return string The checkout information of the product.
	 */
	public function getCheckoutInformation(LanguageCode $language);
	
	
	/**
	 * Get Product Model
	 *
	 * Returns the product model.
	 *
	 * @return string The product model.
	 */
	public function getProductModel();
	
	
	/**
	 * Get EAN
	 *
	 * Returns the EAN of the product.
	 *
	 * @return string The EAN of the product.
	 */
	public function getEan();
	
	
	/**
	 * Get Price
	 *
	 * Returns the price of a product.
	 *
	 * @return float The price of the product.
	 */
	public function getPrice();
	
	
	/**
	 * Get Tax Class ID
	 *
	 * Returns the tax class ID of the product.
	 *
	 * @return int The tax class ID.
	 */
	public function getTaxClassId();
	
	
	/**
	 * Get Quantity
	 *
	 * Returns the quantity of the product.
	 *
	 * @return float The quantity of the product.
	 */
	public function getQuantity();
	
	
	/**
	 * Get Weight
	 *
	 * Returns the weight of the product.
	 *
	 * @return float The weight of the product.
	 */
	public function getWeight();
	
	
	/**
	 * Get Discount Allowed
	 *
	 * Returns the allowed discount.
	 *
	 * @return float The allowed discount.
	 */
	public function getDiscountAllowed();
	
	
	/**
	 * Get Shipping Costs
	 *
	 * Returns the shipping cost of the product.
	 *
	 * @return float The shipping costs of the product.
	 */
	public function getShippingCosts();
	
	
	/**
	 * Get Shipping Time ID
	 *
	 * Returns the shipping time ID of the product.
	 *
	 * @return int The shipping time ID.
	 */
	public function getShippingTimeId();
	
	
	/**
	 * Get Product Type ID.
	 *
	 * Returns the product type ID.
	 *
	 * @return int The product type ID.
	 */
	public function getProductTypeId();
	
	
	/**
	 * Get Manufacturer ID
	 *
	 * Returns the manufacturer ID.
	 *
	 * @return int The manufacturer ID.
	 */
	public function getManufacturerId();
	
	
	/**
	 * Is FSK 18
	 *
	 * Checks if the product is only available for FSK 18.
	 *
	 * @return bool Is the product FSK18?
	 */
	public function isFsk18();
	
	
	/**
	 * Is VPE Active
	 *
	 * Checks if VPE is active on the product.
	 *
	 * @return bool Is VPE active on the product?
	 */
	public function isVpeActive();
	
	
	/**
	 * Get VPE ID.
	 *
	 * Returns the VPE ID.
	 *
	 * @return int VPE ID.
	 */
	public function getVpeId();
	
	
	/**
	 * Get VPE Value
	 *
	 * Returns the VPE value.
	 *
	 * @return float The VPE value.
	 */
	public function getVpeValue();
	
	
	/**
	 * Get Addon Value
	 *
	 * Returns the addon value of a product, depending on the provided key.
	 *
	 * @param StringType $key The key of the addon value to return.
	 *
	 * @return string The addon value.
	 */
	public function getAddonValue(StringType $key);
	
	
	/**
	 * Get Addon Values
	 *
	 * Returns a key value collection of the product.
	 *
	 * @return KeyValueCollection The key value collection.
	 */
	public function getAddonValues();
	
	
	/**
	 * Set Active
	 *
	 * Activates or deactivates a product status.
	 *
	 * @param BoolType $status Should the product status be activated?
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setActive(BoolType $status);
	
	
	/**
	 * Set Sort Order
	 *
	 * Sets the sort order of the product.
	 *
	 * @param IntType $sortOrder The sort order.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setSortOrder(IntType $sortOrder);
	
	
	/**
	 * Set Available Date Time
	 *
	 * Sets an available date time.
	 *
	 * @param DateTime $date The date time to add.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setAvailableDateTime(DateTime $date);
	
	
	/**
	 * Set Last Modified Date Time
	 *
	 * Sets the last modified date time.
	 *
	 * @param DateTime $date The last modified date time.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setLastModifiedDateTime(DateTime $date);
	
	
	/**
	 * Set Viewed Count
	 *
	 * Sets the viewed count of a product.
	 *
	 * @param IntType      $count    The amount of views.
	 * @param LanguageCode $language The language code for the product name.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setViewedCount(IntType $count, LanguageCode $language);
	
	
	/**
	 * Set Ordered Count
	 *
	 * Sets the ordered count.
	 *
	 * @param IntType $count The ordered count.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setOrderedCount(IntType $count);
	
	
	/**
	 * Set Image Container
	 *
	 * Sets the image container of a product.
	 *
	 * @param ProductImageContainerInterface $images Product image container to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setImageContainer(ProductImageContainerInterface $images);
	
	
	/**
	 * Sets the settings of the product.
	 *
	 * @param ProductSettingsInterface $productSettings The settings of the product.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setSettings(ProductSettingsInterface $productSettings);
	
	
	/**
	 * Set Name
	 *
	 * Sets the products name.
	 *
	 * @param StringType   $text     The name of the product.
	 * @param LanguageCode $language The language code for the product name.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setName(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set Description
	 *
	 * Sets the products description.
	 *
	 * @param StringType   $text     The description.
	 * @param LanguageCode $language The language code for the description.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setDescription(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set Short Description
	 *
	 * Sets the products short description.
	 *
	 * @param StringType   $text     Short description to set.
	 * @param LanguageCode $language The language code for the short description.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setShortDescription(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set Keywords
	 *
	 * Sets the products keywords.
	 *
	 * @param StringType   $text     The keywords.
	 * @param LanguageCode $language The language code for the keywords.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setKeywords(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set Meta title.
	 *
	 * Sets the meta title.
	 *
	 * @param StringType   $text     Meta title to set.
	 * @param LanguageCode $language Language code of the meta title.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setMetaTitle(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set Meta description.
	 *
	 * Sets the products meta description.
	 *
	 * @param StringType   $text     Meta description to set.
	 * @param LanguageCode $language Language code for the meta description.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setMetaDescription(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set Meta Keywords
	 *
	 * Sets the products meta keywords.
	 *
	 * @param StringType   $text     The meta keywords.
	 * @param LanguageCode $language The language code for the meta keywords.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setMetaKeywords(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set URL
	 *
	 * Sets the products URL.
	 *
	 * @param StringType   $url      The URL to set.
	 * @param LanguageCode $language The language code for the URL keywords.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setUrl(StringType $url, LanguageCode $language);
	
	
	/**
	 * Set URL Keywords
	 *
	 * Sets the products URL Keywords.
	 *
	 * @param StringType   $text     The URL.
	 * @param LanguageCode $language The language code for the URL keywords.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setUrlKeywords(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set URL rewrite
	 *
	 * Sets the products URL rewrite for the provided language code.
	 *
	 * @param UrlRewrite   $urlRewrite The URL rewrite instance.
	 * @param LanguageCode $language   The language code for the URL keywords.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setUrlRewrite(UrlRewrite $urlRewrite, LanguageCode $language);
	
	
	/**
	 * Set URL rewrites
	 *
	 * Sets the products URL rewrites.
	 *
	 * @param UrlRewriteCollection $urlRewrites The URL rewrites.
	 *
	 * @return GXEngineProduct Same instance for chained method calls.
	 */
	public function setUrlRewrites(UrlRewriteCollection $urlRewrites);
	
	
	/**
	 * Set Checkout Information
	 *
	 * Sets the checkout information.
	 *
	 * @param StringType   $text     The checkout information.
	 * @param LanguageCode $language The language code for the checkout information.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setCheckoutInformation(StringType $text, LanguageCode $language);
	
	
	/**
	 * Set Product Model
	 *
	 * Set the product model.
	 *
	 * @param StringType $model The product model.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setProductModel(StringType $model);
	
	
	/**
	 * Set EAN
	 *
	 * Sets a EAN for the product.
	 *
	 * @param StringType $ean The EAN to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setEan(StringType $ean);
	
	
	/**
	 * Set Price
	 *
	 * Sets a price of the product.
	 *
	 * @param DecimalType $price The price to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setPrice(DecimalType $price);
	
	
	/**
	 * Set Tax Class ID
	 *
	 * Sets a tax class ID for the product.
	 *
	 * @param IdType $id The tax class ID to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setTaxClassId(IdType $id);
	
	
	/**
	 * Set Quantity
	 *
	 * Sets a quantity for the product.
	 *
	 * @param DecimalType $quantity The quantity to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setQuantity(DecimalType $quantity);
	
	
	/**
	 * Set Weight
	 *
	 * Sets the weight of a product.
	 *
	 * @param DecimalType $weight The weight to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setWeight(DecimalType $weight);
	
	
	/**
	 * Set Discount Allowed
	 *
	 * Sets the allowed discount of a product.
	 *
	 * @param DecimalType $discount The discount to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setDiscountAllowed(DecimalType $discount);
	
	
	/**
	 * Set Shipping Costs
	 *
	 * Sets the shipping costs of a product.
	 *
	 * @param DecimalType $price The shipping costs to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setShippingCosts(DecimalType $price);
	
	
	/**
	 * Set Shipping Time ID
	 *
	 * Sets the shipping time ID of a product.
	 *
	 * @param IdType $id The shipping time ID to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setShippingTimeId(IdType $id);
	
	
	/**
	 * Set Product Type ID.
	 *
	 * Sets the product type ID of the product.
	 *
	 * @param IdType $id Product type ID to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setProductTypeId(IdType $id);
	
	
	/**
	 * Set Manufacturer ID
	 *
	 * Sets the manufacturer ID of a product.
	 *
	 * @param IdType $id The manufacturer ID to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setManufacturerId(IdType $id);
	
	
	/**
	 * Set FSK 18
	 *
	 * Activates or deactivates FSK18 for a product.
	 *
	 * @param BoolType $status Should FSK be activated?
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setFsk18(BoolType $status);
	
	
	/**
	 * Set VPE Active
	 *
	 * Activates or deactivates VPE for a product.
	 *
	 * @param BoolType $status Should VPE be activated?
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setVpeActive(BoolType $status);
	
	
	/**
	 * Set VPE ID
	 *
	 * Sets the VPE ID of a product.
	 *
	 * @param IdType $id The VPE ID to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setVpeId(IdType $id);
	
	
	/**
	 * Set VPE Value
	 *
	 * Sets the VPE value of a product.
	 *
	 * @param DecimalType $vpeValue The VPE value to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setVpeValue(DecimalType $vpeValue);
	
	
	/**
	 * Set Addon Value
	 *
	 * Sets the addon value of a product.
	 *
	 * @param StringType $key   The key for the addon value.
	 * @param StringType $value The value for the addon.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setAddonValue(StringType $key, StringType $value);
	
	
	/**
	 * Sets the added date time.
	 *
	 * @param DateTime $date Added date time to set.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function setAddedDateTime(DateTime $date);
	
	
	/**
	 * Add Addon Values
	 *
	 * Adds a key value collection to a product.
	 *
	 * @param KeyValueCollection $keyValueCollection The key value collection to add.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function addAddonValues(KeyValueCollection $keyValueCollection);
	
	
	/**
	 * Delete Addon Value
	 *
	 * Deletes a addon value of a product.
	 *
	 * @param StringType $key The key of the addon value to delete.
	 *
	 * @return ProductInterface Same instance for chained method calls.
	 */
	public function deleteAddonValue(StringType $key);
	
	
	/**
	 * Returns the image container of the product.
	 *
	 * @return ProductImageContainer Product image container.
	 */
	public function getImageContainer();
	
	
	/**
	 * Returns a product's primary image.
	 *
	 * @return ProductImage
	 */
	public function getPrimaryImage();
	

	/**
	 * Returns a product's additional images.
	 *
	 * @return ProductImageCollection
	 */
	public function getAdditionalImages();
}