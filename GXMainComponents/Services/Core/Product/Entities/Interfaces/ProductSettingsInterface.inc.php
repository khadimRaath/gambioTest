<?php

/* --------------------------------------------------------------
   ProductSettingsInterface.inc.php 2016-01-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductSettingsInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductSettingsInterface
{
	/**
	 * Returns the details template name.
	 *
	 * @return string
	 */
	public function getDetailsTemplate();


	/**
	 * Returns the options details template.
	 *
	 * @return string
	 */
	public function getOptionsDetailsTemplate();


	/**
	 * Returns the options listing template.
	 *
	 * @return string
	 */
	public function getOptionsListingTemplate();


	/**
	 * Returns true when the product is displayed on the start page, false otherwise.
	 *
	 * @return bool
	 */
	public function showOnStartpage();


	/**
	 * Returns the sort position.
	 *
	 * @return int
	 */
	public function getStartpageSortOrder();


	/**
	 * Returns true when the added date time is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showAddedDateTime();


	/**
	 * Returns true when the quantity info is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showQuantityInfo();


	/**
	 * Returns true when the weight is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showWeight();


	/**
	 * Returns true when the price offer is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showPriceOffer();


	/**
	 * Returns the price status.
	 *
	 * @return int
	 */
	public function getPriceStatus();


	/**
	 * Returns the minimum order value.
	 *
	 * @return float
	 */
	public function getMinOrder();


	/**
	 * Returns the graduated quantity.
	 *
	 * @return float
	 */
	public function getGraduatedQuantity();


	/**
	 * Returns true when the product is to be displayed in the sitemap, false otherwise.
	 *
	 * @return bool
	 */
	public function isSitemapEntry();


	/**
	 * Returns the sitemap priority.
	 *
	 * @return string
	 */
	public function getSitemapPriority();


	/**
	 * Returns the sitemap change frequency.
	 *
	 * @return string
	 */
	public function getSitemapChangeFreq();


	/**
	 * Returns true when the properties price is to be displayed, false otherwise.
	 *
	 * @return bool
	 */
	public function showPropertiesPrice();


	/**
	 * Returns the properties dropdown mode.
	 *
	 * @return string
	 */
	public function getPropertiesDropdownMode();


	/**
	 * Returns true when the properties combis weight is to be used, false otherwise.
	 *
	 * @return bool
	 */
	public function usePropertiesCombisWeight();


	/**
	 * Returns the mode which is used for the quantity check.
	 * 
	 * 0 = Default (global stock options)
	 * 1 = Products quantity
	 * 2 = Combis quantity
	 * 3 = No check
	 *
	 * @return int
	 */
	public function getPropertiesCombisQuantityCheckMode();


	/**
	 * Returns true when the properties combis shipping time is to be used, false otherwise.
	 *
	 * @return bool
	 */
	public function usePropertiesCombisShippingTime();


	/**
	 * Sets the details listing template.
	 *
	 * @param \StringType $template Name of the template
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setDetailsTemplate(StringType $template);


	/**
	 * Sets the options details template.
	 *
	 * @param \StringType $template Name of the template.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setOptionsDetailsTemplate(StringType $template);


	/**
	 * Sets the options listing template.
	 *
	 * @param \StringType $template Name of the template.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setOptionsListingTemplate(StringType $template);


	/**
	 * Shows or hides  a product on the start page.
	 *
	 * @param \BoolType $status True when it should be displayed, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setShowOnStartpage(BoolType $status);


	/**
	 * Sets the start page sort order.
	 *
	 * @param \IntType $sortOrder Sort position.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setStartpageSortOrder(IntType $sortOrder);


	/**
	 * Shows or hides the added date time of a product.
	 *
	 * @param \BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setShowAddedDateTime(BoolType $status);


	/**
	 * Shows or hides the quantity info of a product.
	 *
	 * @param \BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setShowQuantityInfo(BoolType $status);


	/**
	 * Shows or hides the weight of a product.
	 *
	 * @param \BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setShowWeight(BoolType $status);


	/**
	 * Shows or hides the price offer of a product.
	 *
	 * @param \BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setShowPriceOffer(BoolType $status);


	/**
	 * Sets the price status.
	 *
	 * @param \IntType $status New price status.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setPriceStatus(IntType $status);


	/**
	 * Sets the min order value.
	 *
	 * @param \DecimalType $quantity New minimum order.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setMinOrder(DecimalType $quantity);


	/**
	 * Sets the graduated quantity.
	 *
	 * @param \DecimalType $quantity New graduated quantity.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setGraduatedQuantity(DecimalType $quantity);


	/**
	 * Shows or hides a product in the sitemap.
	 *
	 * @param \BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setSitemapEntry(BoolType $status);


	/**
	 * Sets the sitemap priority.
	 *
	 * @param \StringType $priority New sitemap priority.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setSitemapPriority(StringType $priority);


	/**
	 * Sets the sitemap change frequency.
	 *
	 * @param \StringType $freq New sitemap change frequency.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setSitemapChangeFreq(StringType $freq);


	/**
	 * Shows or hides the properties price of a product.
	 *
	 * @param \BoolType $status True when it is to be displayed, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setShowPropertiesPrice(BoolType $status);


	/**
	 * Sets the properties dropdown mode.
	 *
	 * @param \StringType $mode New properties dropdown mode.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setPropertiesDropdownMode(StringType $mode);


	/**
	 * Determine whether the properties combis weight is to be used or not.
	 *
	 * @param \BoolType $status True when it is to be used, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setUsePropertiesCombisWeight(BoolType $status);
	
	
	/**
	 * Determine which mode for the quantity check should be used.
	 *
	 * 0 = Default (global stock options)
	 * 1 = Products quantity
	 * 2 = Combis quantity
	 * 3 = No check
	 *
	 * @param \IntType $status
	 *
	 * @return ProductSettings|$this Same ProductSettings instance for chained method calls.
	 */
	public function setPropertiesCombisQuantityCheckMode(IntType $status);


	/**
	 * Determine whether the properties combis shipping time is to be used or not.
	 *
	 * @param \BoolType $status True when it is to be used, false otherwise.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setUsePropertiesCombisShippingTime(BoolType $status);


	/**
	 * Returns true when the customer status is permitted, false otherwise.
	 *
	 * @param \IdType $customerStatusId Id of customer status.
	 *
	 * @return bool
	 */
	public function isPermittedCustomerStatus(IdType $customerStatusId);


	/**
	 * Sets customer status permissions.
	 *
	 * @param \IdType   $customerStatusId Id of customer status.
	 * @param \BoolType $permitted        Is customer permitted or not.
	 *
	 * @return ProductSettingsInterface|$this Same ProductSettingsInterface instance for chained method calls.
	 */
	public function setPermittedCustomerStatus(IdType $customerStatusId, BoolType $permitted);
}