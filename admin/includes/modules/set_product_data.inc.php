<?php
/* --------------------------------------------------------------
   set_product_data.inc.php 2016-03-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * This file is included in admin/categories.php for insert_- and update_product action
 */

/** @var LanguageProvider $languageProvider */
$languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
$languages      = $languageProvider->getCodes();
$settings       = $product->getSettings();

$product->setAvailableDateTime(new EmptyDateTime(xtc_db_prepare_input($_POST['products_date_available'])));

$_POST['products_price'] = str_replace(',','.',$_POST['products_price']);

if(PRICE_IS_BRUTTO === 'true' && $_POST['products_price'])
{
	$price = round(((double)$_POST['products_price'] / (xtc_get_tax_rate($_POST['products_tax_class_id']) + 100) * 100),
		PRICE_PRECISION);
}
else
{
	$price = (double)$_POST['products_price'];
}

$customersStatusArray = xtc_get_customers_statuses();
$customerStatusIds    = array_keys($customersStatusArray);

foreach($customerStatusIds as $customerStatusId)
{
	if(isset($_POST['groups']) && is_array($_POST['groups'])
	   && (in_array($customerStatusId, $_POST['groups'])
	       || in_array('all', $_POST['groups']))
	)
	{
		$settings->setPermittedCustomerStatus(new IdType((int)$customerStatusId), new BoolType(true));
	}
	else
	{
		$settings->setPermittedCustomerStatus(new IdType((int)$customerStatusId), new BoolType(false));
	}
}

$graduatedQuantity = (double)$_POST['gm_graduated_qty'] > 0 ? (double)$_POST['gm_graduated_qty'] : 1;
$minOrder          = max((double)$_POST['gm_min_order'] > 0 ? (double)$_POST['gm_min_order'] : 1, $graduatedQuantity);

// Product data
$product->setQuantity(new DecimalType((double)$_POST['products_quantity']));
$product->setProductTypeId(new IdType((int)$_POST['product_type']));
$product->setProductModel(new StringType(xtc_db_prepare_input($_POST['products_model'])));
$product->setEan(new StringType(xtc_db_prepare_input($_POST['products_ean'])));
$product->setPrice(new DecimalType($price));
$product->setActive(isOptionChecked('products_status'));
$product->setSortOrder(new IntType((int)$_POST['products_sort']));
$product->setShippingTimeId(new IdType((int)$_POST['shipping_status']));
$product->setDiscountAllowed(new DecimalType((double)$_POST['products_discount_allowed']));
$product->setWeight(new DecimalType((double)$_POST['products_weight']));
$product->setTaxClassId(new IdType((int)$_POST['products_tax_class_id']));
$product->setManufacturerId(new IdType((int)$_POST['manufacturers_id']));
$product->setFsk18(isOptionChecked('fsk18'));
$product->setVpeValue(new DecimalType((double)$_POST['products_vpe_value']));
$product->setVpeActive(isOptionChecked('products_vpe_status'));
$product->setVpeId(new IdType((int)$_POST['products_vpe']));
$product->setShippingCosts(new DecimalType((double)$_POST['nc_ultra_shipping_costs']));

/** @var LanguageCode $languageCode */
foreach($languages as $languageCode)
{
	/** @var int $languageId */
	$languageId = $languageProvider->getIdByCode($languageCode);

	$description = $_POST['products_description_' . $languageId];
	$matches     = array();
	preg_match('/(.*)\[TAB:/isU', $description, $matches);
	
	if(count($matches) > 1)
	{
		$completeDescription = trim($matches[1]);
	}
	else
	{
		$completeDescription = trim($description);
	}
	
	if(trim($completeDescription) == '<br />')
	{
		$completeDescription = '';
	}
	
	if(count($_POST['products_tab_' . $languageId]) > 0)
	{
		foreach($_POST['products_tab_' . $languageId] as $key => $value)
		{
			$completeDescription .= "[TAB:" . $_POST['products_tab_headline_' . $languageId][$key] . "]" . $value;
		}
	}
	
	$urlKeywords = xtc_db_prepare_input($_POST['gm_url_keywords'][$languageId]);
	$urlKeywords = xtc_cleanName($urlKeywords);
	
	$product->setName(new StringType(xtc_db_prepare_input($_POST['products_name'][$languageId])), $languageCode);
	$product->setDescription(new StringType(xtc_db_prepare_input($completeDescription)), $languageCode);
	$product->setShortDescription(new StringType(xtc_db_prepare_input($_POST['products_short_description_'
	                                                                         . $languageId])), $languageCode);
	$product->setKeywords(new StringType(xtc_db_prepare_input($_POST['products_keywords'][$languageId])),
	                      $languageCode);
	$product->setUrl(new StringType(xtc_db_prepare_input($_POST['products_url'][$languageId])), $languageCode);
	$product->setMetaTitle(new StringType(xtc_db_prepare_input($_POST['products_meta_title'][$languageId])),
	                       $languageCode);
	$product->setMetaDescription(new StringType(xtc_db_prepare_input($_POST['products_meta_description'][$languageId])),
	                             $languageCode);
	$product->setMetaKeywords(new StringType(xtc_db_prepare_input($_POST['products_meta_keywords'][$languageId])),
	                          $languageCode);
	$product->setUrlKeywords(new StringType($urlKeywords), $languageCode);
	$product->setCheckoutInformation(new StringType(xtc_db_prepare_input($_POST['checkout_information_'
	                                                                            . $languageId])), $languageCode);
	$product->setViewedCount(new IntType(0), $languageCode);
}

// Product setting data
$settings->setShowOnStartpage(isOptionChecked('products_startpage'));
$settings->setStartpageSortOrder(new IntType((int)$_POST['products_startpage_sort']));
$settings->setDetailsTemplate(new StringType(xtc_db_prepare_input($_POST['info_template'])));
$settings->setOptionsDetailsTemplate(new StringType(xtc_db_prepare_input($_POST['options_template'])));
$settings->setOptionsListingTemplate(new StringType(xtc_db_prepare_input($_POST['gm_options_template'])));
$settings->setShowAddedDateTime(isOptionChecked('gm_show_date_added'));
$settings->setShowPriceOffer(isOptionChecked('gm_show_price_offer'));
$settings->setPriceStatus(new IntType((int)$_POST['gm_price_status']));
$settings->setShowQuantityInfo(isOptionChecked('gm_show_qty_info'));
$settings->setShowWeight(isOptionChecked('gm_show_weight'));
$settings->setMinOrder(new DecimalType($minOrder));
$settings->setGraduatedQuantity(new DecimalType($graduatedQuantity));
$settings->setSitemapPriority(new StringType(xtc_db_prepare_input($_POST['gm_priority'])));
$settings->setSitemapChangeFreq(new StringType(xtc_db_prepare_input($_POST['gm_changefreq'])));
$settings->setSitemapEntry(isOptionChecked('gm_sitemap_entry'));

$product->setSettings($settings);