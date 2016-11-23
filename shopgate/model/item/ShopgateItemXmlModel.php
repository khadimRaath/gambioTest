<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */
class ShopgateItemXmlModel extends ShopgateItemModel
{
    /**
     * cache for product data
     *
     * @var array
     */
    private $cache = array(
        'options'              => '',
        'variationCount'       => '',
        'currentChild'         => '',
        'inputs'               => '',
        'taxRate'              => '',
        'propertyCombinations' => '',
        'productHasTierPrices' => '',
    );
    
    /**
     * @var null|ShopgateItemXmlModel
     */
    private $parent = null;
    
    /**
     * @var array
     */
    private $orderInfo;
    
    /**
     * @var array
     */
    protected $item;
    
    /**
     * @var array
     */
    private $xtPricesByCustomerGroups;
    
    
    public function setLastUpdate()
    {
        if (!empty($this->item['products_last_modified'])) {
            parent::setLastUpdate($this->item['products_last_modified']);
        }
    }
    
    public function setCustomerGroupPrices($customerGroupPrices)
    {
        $this->xtPricesByCustomerGroups = $customerGroupPrices;
    }
    
    public function setUid()
    {
        if ($this->getIsChild()) {
            $hash = '';
            if ($this->checkAttributeIsProperty()) {
                
                for ($i = 1; $i <= count($this->cache['currentChild']['group_data']); $i++) {
                    $hash .= $this->cache['currentChild']["attribute_{$i}"];
                }
                
                $hash .= '_' . $this->cache['currentChild']['itemNumber'];
                $ids = array(
                    $this->item['products_id'],
                    $this->cache['currentChild']['order_info']['products_properties_combis_id'],
                );
                $uid = $this->generateChildItemHash($hash, $ids);
                
            } else {
                foreach ($this->cache['currentChild'] as $variation) {
                    $hash .= $variation['products_options_values_name'];
                }
                $uid = $this->generateChildItemHash($hash, array($this->item['products_id']));
            }
            $this->shopgateLogger->log("uid: {$uid}, child \n", ShopgateLogger::LOGTYPE_DEBUG);
        } else {
            $uid = $this->item['products_id'];
            $this->shopgateLogger->log("uid: {$uid}, parent \n", ShopgateLogger::LOGTYPE_DEBUG);
        }
        
        parent::setUid($uid);
    }
    
    public function setName()
    {
        $productName = $this->item['products_name'];
        $this->shopgateLogger->log("name: {$productName} \n", ShopgateLogger::LOGTYPE_DEBUG);
        parent::setName($this->generateItemName($productName));
    }
    
    public function setTaxPercent()
    {
        $taxRate = $this->calculateTaxRate();
        $this->shopgateLogger->log("Tax rate: {$taxRate}, parent \n", ShopgateLogger::LOGTYPE_DEBUG);
        parent::setTaxPercent($taxRate);
    }
    
    public function setTaxClass()
    {
        $locationModel = new ShopgateLocationModel($this->config);
        parent::setTaxClass($locationModel->getTaxClassById($this->item['products_tax_class_id']));
    }
    
    public function setCurrency()
    {
        if (!empty($this->shopCurrency['code'])) {
            $currency = $this->shopCurrency['code'];
            parent::setCurrency($currency);
        }
    }
    
    public function setDescription()
    {
        $stringHelper = $this->getHelper(ShopgateObject::HELPER_STRING);
        $description  = $this->getProductDescription(
            $this->item,
            $stringHelper->removeTagsFromString($this->item["products_description"], array(), array('IFRAME')),
            $stringHelper->removeTagsFromString($this->item["products_short_description"]),
            $this->config->getExportProductsContentManagedFiles(),
            $this->config->getExportDescriptionType()
        );
        
        parent::setDescription($description);
    }
    
    public function setDeeplink()
    {
        if (!$this->getIsChild()) {
            $this->setSeoBoost($gmSeoBoost, $gmSeoBoostProductsEnabled);
            $deepLink = $this->generateDeepLink(
                $this->item['products_id'], $this->item['products_name'], $gmSeoBoostProductsEnabled, $gmSeoBoost
            );
            
            parent::setDeeplink($deepLink);
        }
    }
    
    public function setAgeRating()
    {
        parent::setAgeRating($this->item["products_fsk18"] == 1 ? '18' : '');
    }
    
    public function setWeight()
    {
        $weight = 0;
        if ($this->getIsChild()) {
            
            if ($this->checkAttributeIsProperty()) {
                if (!empty($this->cache['currentChild']['offset_weight'])) {
                    $weight = $this->cache['currentChild']['offset_weight'];
                }
            } else {
                foreach ($this->cache['currentChild'] as $variation) {
                    $weight += ($variation['weight_prefix'] == '-')
                        ? $variation['options_values_weight'] * (-1)
                        : $variation['options_values_weight'];
                }
            }
            $weight = $this->calculateWeight($this->item) + $weight;
            $this->shopgateLogger->log("attribute weight: {$weight}, child \n", ShopgateLogger::LOGTYPE_DEBUG);
        } else {
            $weight = $this->calculateWeight($this->item);
            $this->shopgateLogger->log("attribute weight: {$weight}, child \n", ShopgateLogger::LOGTYPE_DEBUG);
        }
        
        parent::setWeight($weight);
    }
    
    public function setWeightUnit()
    {
        parent::setWeightUnit(self::DEFAULT_WEIGHT_UNIT_KG);
    }
    
    public function setPrice()
    {
        // Hint: it is not possible to set a vpe depending on tier prices
        $price                    = $oldPrice = $discount = 0;
        $priceHelper              = $this->getHelper(ShopgateObject::HELPER_PRICING);
        $priceModel               = new ShopgatePriceModel($this->config, $this->languageId, $this->exchangeRate);
        $exportPriceModel         = new Shopgate_Model_Catalog_Price();
        $_SESSION['languages_id'] = $this->languageId;
        $priceType                = Shopgate_Model_Catalog_Price::DEFAULT_PRICE_TYPE_NET;
        $taxRate                  = $this->calculateTaxRate();
        $xtPrice                  = new xtcPrice_ORIGIN($this->shopCurrency['code'], DEFAULT_CUSTOMERS_STATUS_ID);
        $priceOnRequest           = $xtPrice->xtcGetPrice(
            $this->item['products_id'],
            false,
            1,
            $this->item['products_tax_class_id'],
            $this->item['products_price'],
            1
        );
        
        $priceModel->getPriceToItem($this->item, $taxRate, $price, $oldPrice, $discount, true, false);
        $statuses = $this->generatePriceStatus($this->item, $oldPrice);
        
        if ($this->getIsChild()) {
            $additionalPrice = 0;
            
            if ($this->checkAttributeIsProperty()) {
                if (!empty($this->cache['currentChild']['offset_amount_with_tax'])) {
                    $additionalPrice = $this->cache['currentChild']['offset_amount_with_tax'] / (1 + ($taxRate / 100));
                } elseif (!empty($this->cache['currentChild']['offset_amount'])) {
                    $additionalPrice = $this->cache['currentChild']['offset_amount'];
                }
            } else {
                $additionalPrice = $this->calculateChildPrice($this->cache['currentChild']);
            }
            
            $additionalPrice = ($additionalPrice * $this->exchangeRate);
            $salesPrice      = $price + $additionalPrice;
            
            if ($oldPrice > 0) {
                $oldPrice += $additionalPrice;
            }
            
            $exportPriceModel->setSalePrice($salesPrice);
            
            // attribute vpe price will be checked and set in the database query
            // so no need to regard this here
            if ($oldPrice) {
                $exportPriceModel->setPrice($oldPrice);
            }
            
            $currentChildProductsVpeValue = (float) $this->cache['currentChild']['products_vpe_value'];
            if ($currentChildProductsVpeValue > 0.0
                && !empty($this->cache['currentChild']['products_vpe_name'])
            ) {
                $combinationPrice = 0;
                if ($this->cache['currentChild']['combi_price_type'] == 'calc') {
                    $combinationPrice = $price * (1 / $currentChildProductsVpeValue);
                } elseif ($this->cache['currentChild']['combi_price_type'] == 'fix') {
                    $combinationPrice = $salesPrice * (1 / $currentChildProductsVpeValue);
                }
                $combinationPrice = $this->addTaxes($combinationPrice, $taxRate);
                $vpe              = $xtPrice->xtcFormat($combinationPrice, true)
                    . TXT_PER . $this->cache['currentChild']['products_vpe_name'];
                
            } else {
                $salesPrice = $this->addTaxes($salesPrice, $taxRate);
                $vpe        = $this->getProductVPE($this->item, $salesPrice, $this->cache['currentChild']);
            }
            
            if (!empty($vpe)) {
                $exportPriceModel->setBasePrice($vpe);
            }elseif(!empty($priceOnRequest['formated']) && $priceOnRequest['formated'] == GM_SHOW_PRICE_ON_REQUEST) {
                $exportPriceModel->setBasePrice(GM_SHOW_PRICE_ON_REQUEST);
            }
            
            $this->shopgateLogger->log(
                "price: {$price}, old price: {$oldPrice}, child \n", ShopgateLogger::LOGTYPE_DEBUG
            );
        } else {
            $vpe = "";
            $productsVpeValue = (float) $this->item['products_vpe_value'];
            if (!empty($this->item['products_vpe_name']) && $productsVpeValue > 0.0) {
                $vpePrice = $this->addTaxes($price, $taxRate);
                $vpePrice = $vpePrice * (1 / $productsVpeValue);
                $vpe      = $xtPrice->xtcFormat($vpePrice, true)
                    . TXT_PER . $this->item['products_vpe_name'];
            }
            
            if (!empty($statuses['basic_price'])) {
                $exportPriceModel->setSalePrice($statuses['basic_price']);
            } else {
                $exportPriceModel->setSalePrice($price);
                if (!empty($vpe)) {
                    $exportPriceModel->setBasePrice($vpe);
                }elseif(!empty($priceOnRequest['formated']) && $priceOnRequest['formated'] == GM_SHOW_PRICE_ON_REQUEST) {
                    $exportPriceModel->setBasePrice(GM_SHOW_PRICE_ON_REQUEST);
                }
                if ($oldPrice) {
                    $exportPriceModel->setPrice($oldPrice);
                }
            }
            
            $this->shopgateLogger->log(
                "price: {$price}, old price: {$oldPrice}, vpe: {$vpe}, parent \n", ShopgateLogger::LOGTYPE_DEBUG
            );
        }
        
        $this->addTierPricesTo($exportPriceModel, $priceModel, $priceHelper, $taxRate);
        $exportPriceModel->setType($priceType);
        
        parent::setPrice($exportPriceModel);
    }
    
    public function setShipping()
    {
        $shipping    = new Shopgate_Model_Catalog_Shipping();
        $priceHelper = $this->getHelper(ShopgateObject::HELPER_PRICING);
        
        if (!empty($this->item['nc_ultra_shipping_costs'])) {
            $shipping->setAdditionalCostsPerUnit(
                $this->item['nc_ultra_shipping_costs']
            );
        } else {
            $shipping->setAdditionalCostsPerUnit(0.0);
        }
        $shipping->setCostsPerOrder(0.0);
        $shipping->setIsFree(false);
        
        parent::setShipping($shipping);
    }
    
    public function setManufacturer()
    {
        $title = $this->item['manufacturers_name'];
        if (!empty($title)) {
            $manufacturerModel = new Shopgate_Model_Catalog_Manufacturer();
            $manufacturerModel->setUid(bin2hex($title));
            $manufacturerModel->setTitle($title);
            $manufacturerModel->setItemNumber(false);
            parent::setManufacturer($manufacturerModel);
        }
    }
    
    public function setVisibility()
    {
        $visibility = new Shopgate_Model_Catalog_Visibility();
        $visibility->setLevel(Shopgate_Model_Catalog_Visibility::DEFAULT_VISIBILITY_CATALOG_AND_SEARCH);
        $visibility->setMarketplace(true);
        
        parent::setVisibility($visibility);
    }
    
    public function setStock()
    {
        $oldPrice = "";
        $stock    = new Shopgate_Model_Catalog_Stock();
        $statuses = $this->generatePriceStatus($this->item, $oldPrice);
        
        if ($this->checkAttributeIsProperty() && !empty($this->cache['currentChild']['shipping_status_name'])) {
            $stock->setAvailabilityText($this->cache['currentChild']['shipping_status_name']);
        } elseif (!empty($statuses['available_text'])) {
            $stock->setAvailabilityText($statuses['available_text']);
        } else {
            $stock->setAvailabilityText($this->getAvailableText($this->item));
        }
        
        $itemStock = $this->item['products_quantity'];
        
        // The stocks of all variations are iterated and in the end the lowest is set.
        // Because there is no "real" stock management for every product in modified
        if ($this->getIsChild()) {
            if ($this->checkAttributeIsProperty()) {
                
                if (!empty($this->cache['currentChild']['stock_quantity'])
                    && (int)$this->cache['currentChild']['use_stock'] == 1
                ) {
                    if ((int)$this->cache['currentChild']['use_parents_stock_quantity'] == 0) {
                        $itemStock = $this->cache['currentChild']['stock_quantity'];
                    }
                }
            } elseif (ATTRIBUTE_STOCK_CHECK == 'true') {
                foreach ($this->cache['currentChild'] as $variation) {
                    if ($variation['attributes_stock'] < $itemStock) {
                        $itemStock = $variation['attributes_stock'];
                    }
                }
            }
        }
        
        $stock->setStockQuantity((int)$itemStock);
        
        // packaging unit not supported yet (xml/csv)
        // workaround:
        //  - export the products price * gm_graduated_quantity
        //
        // add information text into description
        // on order import:
        //  - price / gm_graduated_quantity
        //  - quantity * gm_graduated_quantity
        //
        //$this->item['gm_graduated_quantity'];
        
        if (!empty($statuses) && count($statuses) > 0) {
            if ((bool)$statuses['use_stock'] === true) {
                $stock->setUseStock(true);
            } else {
                $stock->setUseStock(false);
            }
            $stock->setIsSaleable(false);
            $stock->setStockQuantity(0);
        } else {
            $stock->setMinimumOrderQuantity($this->getMinimumOrderQuantity($this->item));
            
            if ($this->useStock()) {
                $stock->setIsSaleable($itemStock > 0 ? true : false);
                $stock->setUseStock(true);
            } else {
                $stock->setIsSaleable(true);
                $stock->setUseStock(false);
            }
        }
        
        parent::setStock($stock);
    }

    public function setImages()
    {
        $images = array();
        if ($this->getIsChild()) {
            if (!$this->checkAttributeIsProperty()) {
                foreach ($this->cache['currentChild'] as $variation) {
                    if (!empty($variation['gm_filename'])) {
                        $imageModel = new Shopgate_Model_Media_Image();
                        $imageModel->setUrl(
                            $this->getFilePath($variation['gm_filename'], 'images/product_images/attribute_images/')
                        );
                        $images[] = $imageModel;
                    }
                }
                
                if (!empty($images)) {
                    $images = array_merge($images, $this->parent->getImages());
                }
            }
        } else {
            foreach ($this->getProductsImages($this->item, true) as $image) {
                $imageModel = new Shopgate_Model_Media_Image();
                $imageModel->setUrl($image);
                $images[] = $imageModel;
            }
        }

        parent::setImages($images);
    }
    
    public function setCategoryPaths()
    {
        $categories   = $this->getCategoryNumbers($this->item, true);
        $categoryData = array();
        
        foreach ($categories as $category) {
            $categoryModel = new Shopgate_Model_Catalog_CategoryPath();
            $categoryModel->setUid($category['categories_id']);
            $categoryModel->setSortOrder($category['sortOrder']);
            $categoryData[] = $categoryModel;
        }
        
        parent::setCategoryPaths($categoryData);
    }
    
    public function setProperties()
    {
        $properties   = $this->buildProperties($this->item, true);
        $propertyData = array();
        foreach ($properties as $property) {
            $propertyModel = new Shopgate_Model_Catalog_Property();
            $propertyModel->setUid($property['feature_id']);
            $propertyModel->setLabel($property['feature_name']);
            $propertyModel->setValue($property['feature_value_text']);
            $propertyData[] = $propertyModel;
        }
        
        parent::setProperties($propertyData);
    }
    
    public function setIdentifiers()
    {
        $identifierData = array();
        if ($this->getIsChild()) {
            
            if ($this->checkAttributeIsProperty()) {
                if (!empty($this->cache['currentChild']['combi_ean'])) {
                    $identifierModel = new Shopgate_Model_Catalog_Identifier();
                    $identifierModel->setType("ean");
                    $identifierModel->setValue($this->cache['currentChild']['combi_ean']);
                    $identifierData[] = $identifierModel;
                }
                
                if (!empty($this->cache['currentChild']['item_number'])) {
                    $identifierModel = new Shopgate_Model_Catalog_Identifier();
                    $identifierModel->setType("sku");
                    $identifierModel->setValue(
                        (defined('APPEND_PROPERTIES_MODEL') && APPEND_PROPERTIES_MODEL == 'true'
                        && $this->item['products_model'] ? $this->item['products_model'] . "-" : "")
                        . $this->cache['currentChild']['item_number']
                    );
                    $identifierData[] = $identifierModel;
                }
                
            } else {
                foreach ($this->cache['currentChild'] as $child) {
                    $identifierModel = new Shopgate_Model_Catalog_Identifier();
                    $identifierModel->setType("ean");
                    $identifierModel->setValue($child['gm_ean']);
                    $identifierData[] = $identifierModel;

                    if (!empty($child['attributes_model'])) {
                        $identifierModel = new Shopgate_Model_Catalog_Identifier();
                        $identifierModel->setType('sku');
                        $identifierModel->setValue($child['attributes_model']);
                        $identifierData[] = $identifierModel;
                    }
                }
            }
        } else {
            $identifiers = $this->getProductCodes($this->item['products_id']);
            
            foreach ($identifiers as $identifier) {
                foreach ($identifier as $identifierField => $identifierValue) {
                    if (!empty($identifierValue)) {
                        if ($identifierField == 'upc' && !is_int($identifierValue)) {
                            continue;
                        }
                        $identifierModel = new Shopgate_Model_Catalog_Identifier();
                        $identifierModel->setType($identifierField);
                        $identifierModel->setValue($identifierValue);
                        $identifierData[] = $identifierModel;
                    }
                }
            }
            
            if (!empty($this->item['products_ean'])) {
                $identifierModel = new Shopgate_Model_Catalog_Identifier();
                $identifierModel->setType("ean");
                $identifierModel->setValue($this->item['products_ean']);
                $identifierData[] = $identifierModel;
            }
            
            if (!empty($this->item['products_model'])) {
                $identifierModel = new Shopgate_Model_Catalog_Identifier();
                $identifierModel->setType("sku");
                $identifierModel->setValue($this->item['products_model']);
                $identifierData[] = $identifierModel;
            }
        }
        
        parent::setIdentifiers($identifierData);
    }
    
    public function setTags()
    {
        $result = array();
        $tags   = explode(',', trim($this->item['products_keywords']));
        
        foreach ($tags as $tag) {
            if (!ctype_space($tag) && !empty($tag)) {
                $tagItemObject = new Shopgate_Model_Catalog_Tag();
                $tagItemObject->setValue(trim($tag));
                $result[] = $tagItemObject;
            }
        }
        
        parent::setTags($result);
    }
    
    public function setRelations()
    {
        $relatedItemUidList = $this->getRelatedShopItems($this->item['products_id'], true);
        $relationData       = array();
        
        if (!empty($relatedItemUidList)) {
            $productRelation = new Shopgate_Model_Catalog_Relation();
            $productRelation->setType(Shopgate_Model_Catalog_Relation::DEFAULT_RELATION_TYPE_UPSELL);
            $productRelation->setValues($relatedItemUidList);
            $relationData[] = $productRelation;
        }
        
        parent::setRelations($relationData);
    }
    
    public function setInputs()
    {
        $optionResult = $this->setInputOptions();
        $inputResult  = $this->setInputFields();
    
        parent::setInputs(array_merge($inputResult, $optionResult));
    }
    
    /**
     * Generates and returns an array of option inputs and updates the internal order info of the product.
     * 
     * @return array
     * 
     * @post The internal order info of $this contains all information needed to import it as line item of an order later on.
     */
    private function setInputOptions()
    {
        $count        = 0;
        $optionResult = array();
        
        if (
            ($this->getVariationCombinationCount() <= $this->config->getMaxAttributes())
            || $this->isProductDeactivated($this->item, $orderInfo) // $orderInfo is unused but has to be passed by ref
        ) {
            return $optionResult;
        }
        
        $variations = $this->generateOptions();
        
        if ((count($variations) > 2) && !empty($variations[2]['order_info']['is_property_attribute'])) {
            return $optionResult;
        }
        
        // generate options and option values
        foreach ($variations as $variationGroup) {
            $firstItem = reset($variationGroup);
            $options   = array();
            $input     = new Shopgate_Model_Catalog_Input();
            $input->setUid($firstItem['products_options_id']);
            $input->setLabel($firstItem['products_options_name']);
            $count++;
            
            foreach ($variationGroup as $variation) {
                $price = $this->calculateOptionPrice($variation);
                $this->addDataToOrderInfo($variation, $count);
                $option = new Shopgate_Model_Catalog_Option();
                $option->setUid($variation['products_options_values_id']);
                $option->setAdditionalPrice($price);
                $option->setSortOrder($variation['sortorder']);
                $option->setLabel($variation['products_options_values_name']);
                $options[] = $option;
            }
            
            if (count($options) > 0) {
                $input->setType(Shopgate_Model_Catalog_Input::DEFAULT_INPUT_TYPE_SELECT);
                $input->setOptions($options);
                $optionResult[] = $input;
            }
        }
        
        // update internal order information
        if (count($optionResult) <= 0) {
            return $optionResult;
        }
        
        $parentOrderInfo = parent::getInternalOrderInfo();
        
        if (!empty($parentOrderInfo) && is_string($parentOrderInfo)) {
            $parentOrderInfo = $this->jsonDecode($parentOrderInfo);
        }
        
        if (!empty($parentOrderInfo) && !empty($this->orderInfo)) {
            parent::setInternalOrderInfo($this->jsonEncode(array_merge($parentOrderInfo, $this->orderInfo)));
        } elseif (!empty($this->orderInfo)) {
            parent::setInternalOrderInfo($this->jsonEncode($this->orderInfo));
        }
        
        return $optionResult;
    }
    
    /**
     * @return array
     */
    private function setInputFields()
    {
        $attributeCount  = count($this->generateOptions());
        $inputs          = $this->getInputFieldsToProduct();
        $inputResult     = array();
        $firstInputField = reset($inputs);
    
        if (!empty($inputs['has_input_fields'])) {
            // gx customizer field
            $sgInputObject = $this->generateInputModelForGxInputField($inputs);
            
            if (!empty($sgInputObject)) {
                $inputResult[] = $sgInputObject;
            }
        } elseif (!empty($firstInputField['has_input_fields'])) {
            foreach ($inputs as $gxInputField) {
                $sgInputObject = $this->generateInputModelForGxInputField($gxInputField);
                
                if (!empty($sgInputObject)) {
                    $inputResult[] = $sgInputObject;
                }
            }
        } else {
            foreach ($inputs as $inputField) {
                $price = $this->calculateOptionPrice($inputField);
                $this->addDataToOrderInfo($inputField, ++$attributeCount);
                
                $input = new Shopgate_Model_Catalog_Input();
                $input->setUid($inputField['products_options_id']);
                $input->setType(Shopgate_Model_Catalog_Input::DEFAULT_INPUT_TYPE_TEXT);
                $input->setAdditionalPrice($price);
                $input->setType("fixed");
                $input->setSortOrder($inputField['sortorder']);
                $input->setLabel($inputField['products_options_values_name']);
                $inputResult[] = $input;
            }
        }
        
        return $inputResult;
    }
    
    public function setInternalOrderInfo()
    {
        if ($this->getIsChild()) {
            $orderInfo = "";
            $i         = 0;
            
            if ($this->checkAttributeIsProperty()) {
                if (!empty($this->cache['currentChild']['order_info'])) {
                    $orderInfo                     = $this->cache['currentChild']['order_info'];
                    $orderInfo['base_item_number'] = $this->item['products_id'];
                }
            } else {
                foreach ($this->cache['currentChild'] as $variation) {
                    ++$i;
                    
                    $orderInfo['attribute_' . $i] = array(
                        $variation['products_attributes_id'] => array(
                            'options_id'        => $variation['products_options_id'],
                            'options_values_id' => $variation['products_options_values_id']
                        )
                    );
                }
                $orderInfo['base_item_number'] = $this->item['products_id'];
            }
        }
        
        if (!empty($orderInfo)) {
            parent::setInternalOrderInfo($this->jsonEncode($orderInfo));
            
            return;
        }
        
        if (!empty($this->orderInfo)) {
            parent::setInternalOrderInfo($this->jsonEncode($this->orderInfo));
        }
    }
    
    public function setChildren()
    {
        $this->setAttributeGroups(array());
        
        if (($this->getVariationCombinationCount() > $this->config->getMaxAttributes())) {
            return;
        }
        
        $children = $this->generateAttributes();
        $parentGroupData = "";
        $childData       = array();
        
        if (isset($children['has_options'])) {
            unset($children['has_options']);
        }
        
        if (isset($children[0]['attribute_1'])) {
            $parentGroupData = $children[0];
            unset($children[0]);
        }
        
        foreach ($children as $child) {
            if (!empty($parentGroupData)) {
                $child['group_data'] = $parentGroupData;
            }
            $childModel = clone $this;
            $childModel->setIsChild(true);
            $childModel->cache['currentChild'] = $child;
            $childModel->setParent($this);
            $childModel->setFireMethodsForChildren();
            $childModel->generateData();
            $childData[] = $childModel;
        }
        
        parent::setChildren($childData);
    }
    
    public function setAttributes()
    {
        if (!$this->getIsChild()) {
            return;
        }
        
        $inputFields     = array();
        $parentAttGroups = $this->parent != null
            ? $this->parent->getAttributeGroups()
            : array();
        
        if ($this->checkAttributeIsProperty()) {
            
            // Property attributes BOF
            if (empty($parentAttGroups)) {
                for ($i = 1; $i <= count($this->cache['currentChild']['group_data']); $i++) {
                    $attributeGroupItem = new Shopgate_Model_Catalog_AttributeGroup();
                    $attributeGroupItem->setUid(
                        bin2hex($this->cache['currentChild']['group_data']["attribute_{$i}"]) . '-' . $i
                    );
                    $attributeGroupItem->setLabel($this->cache['currentChild']['group_data']["attribute_{$i}"]);
                    $parentAttGroups[] = $attributeGroupItem;
                }
                
                if ($this->parent != null) {
                    $this->parent->setAttributeGroups($parentAttGroups);
                }
            }
            
            for ($i = 1; $i <= count($this->cache['currentChild']['group_data']); $i++) {
                $child       = $this->cache['currentChild'];
                $label       = !empty($child["attribute_{$i}"]) ? $child["attribute_{$i}"] : "";
                $inputObject = new Shopgate_Model_Catalog_Attribute();
                $inputObject->setGroupUid(
                    !empty($child['group_data']["attribute_{$i}"])
                        ? bin2hex($child['group_data']["attribute_{$i}"]) . '-' . $i
                        : ""
                );
                $inputObject->setLabel($label);
                $inputFields[] = $inputObject;
            }
            
        } elseif ($this->getVariationCombinationCount() <= $this->config->getMaxAttributes()) {
            
            foreach ($this->cache['currentChild'] as $attribute) {
                $inputObject = new Shopgate_Model_Catalog_Attribute();
                $inputObject->setGroupUid($attribute['products_options_id']);
                $inputObject->setLabel($attribute['products_options_values_name']);
                $inputFields[] = $inputObject;
                
                if (!isset($parentAttGroups[$attribute['products_options_id']])) {
                    /* @var $attribute Shopgate_Model_Catalog_AttributeGroup */
                    $attributeGroupItem = new Shopgate_Model_Catalog_AttributeGroup();
                    $attributeGroupItem->setUid($attribute['products_options_id']);
                    $attributeGroupItem->setLabel($attribute['products_options_name']);
                    $parentAttGroups[$attribute['products_options_id']] = $attributeGroupItem;
                }
            }
        }
        
        if (!empty($inputFields)) {
            parent::setAttributes($inputFields);
        }
        
        if ($this->parent != null) {
            $this->parent->setAttributeGroups($parentAttGroups);
        }
        
    }
    
    public function setDisplayType()
    {
        parent::setDisplayType(Shopgate_Model_Catalog_Product::DISPLAY_TYPE_DEFAULT);
    }
    
    /**
     * set the methods which need to be called for every child product
     */
    public function setFireMethodsForChildren()
    {
        $this->fireMethods = array(
            'setUid',
            'setAttributes',
            'setPrice',
            'setWeight',
            'setInternalOrderInfo',
            'setStock',
            'setIdentifiers',
            'setWeightUnit',
            'setImages'
        );
    }
    
    /**
     * add information about options and input fields to the order information
     *
     * @param array $option
     * @param int   $attributeCount
     */
    private function addDataToOrderInfo(array $option, $attributeCount)
    {
        $this->orderInfo["attribute_{$attributeCount}"][$option['products_attributes_id']] = array(
            'options_id'        => $option['products_options_id'],
            'options_values_id' => $option['products_options_values_id'],
        );
    }
    
    /**
     * calculates the quantity of option combinations (cross product)
     * uses a cache to prevent multiple calculation
     *
     * @return int
     */
    private function getVariationCombinationCount()
    {
        if (empty($this->cache['variationCount'])) {
            $this->cache['variationCount'] = $this->calculateVariationAmountByOptions($this->generateOptions());
        }
        
        return $this->cache['variationCount'];
    }
    
    /**
     * read the input field data to a product from the database
     * uses a cache to prevent multiple calls
     *
     * @return array
     */
    private function getInputFieldsToProduct()
    {
        $gxCustomizerSets            = $this->getGxCustomizerSets();
        $inputFieldsFromGxCustomizer = $this->getInputFieldsFromGxCustomizerSets($gxCustomizerSets);
        
        return (!empty($this->cache['inputs']))
            ? $this->cache['inputs']
            : $this->cache['inputs'] = $this->getInputFields($this->item, $inputFieldsFromGxCustomizer, true);
    }
    
    /**
     * read all options from the database and generate the cross product of them
     * uses a cache to prevent multiple calls
     *
     * @return array
     *
     * @throws ShopgateLibraryException
     */
    private function generateAttributes()
    {
        $options = $this->generateOptions();
        
        if (empty($options)
            || isset($options['has_options'])
        ) {
            return $options;
        }
        
        $helper   = $this->getHelper(self::HELPER_DATASTRUCTURE);
        $children = $helper->arrayCross($options);
        
        return $children;
    }
    
    /**
     * read all options from the database and generate an array containing them
     * uses a cache to prevent multiple calls
     * 
     * @return array
     */
    private function generateOptions()
    {
        $taxRate = parent::getTaxPercent();
        
        if (empty($taxRate)) {
            $taxRate = $this->calculateTaxRate();
        }
        
        $variations = $this->generatePropertyCombinations();
        
        if (!empty($variations)) {
            return $variations;
        }
        
        if (empty($variations)) {
            $variations = $this->getVariations($this->item, $taxRate, true);
        }
        
        $options = (!empty($this->cache['options']))
            ? $this->cache['options']
            : $this->cache['options'] = $variations;
        
        return $options;
    }
    
    /**
     * @param Shopgate_Model_Catalog_Price $priceModel
     * @param ShopgatePriceModel           $priceHelperModel
     * @param Shopgate_Helper_Pricing      $priceHelper
     * @param float|string                 $taxRate
     */
    private function addTierPricesTo(
        Shopgate_Model_Catalog_Price $priceModel, ShopgatePriceModel $priceHelperModel,
        Shopgate_Helper_Pricing $priceHelper, $taxRate
    ) {
        /**
         * @var int             $customerGroupId
         * @var xtcPrice_ORIGIN $xtPrice
         */
        foreach ($this->xtPricesByCustomerGroups as $customerGroupId => $xtPrice) {
            if ($xtPrice->cStatus['customers_status_show_price'] == '0') {
                continue;
            }
            
            $getDiscountsOnly = ($xtPrice->cStatus['customers_status_graduated_prices'] == '1')
                ? ''
                : ' AND `quantity` = 1 ';
            
            $quantitiesQuery = xtc_db_query(
                'SELECT `quantity` ' .
                'FROM `personal_offers_by_customers_status_' . ((int)$customerGroupId) . '` ' .
                'WHERE `products_id` = ' . ((int)$this->item['products_id']) . ' ' . $getDiscountsOnly .
                'ORDER BY `quantity`;'
            );
            
            while ($quantity = xtc_db_fetch_array($quantitiesQuery)) {
                $priceHelperModel->getPriceToItem($this->item, $taxRate, $price, $oldPrice, $discount, false, false);
                
                $graduatedPrice =
                    $this->removeTaxes($this->calculateGraduatedPriceGross($xtPrice, $quantity), $taxRate);
                
                if ($this->getIsChild() && !$this->checkAttributeIsProperty()) {
                    $addAmountNet = $this->calculateChildPrice($this->cache['currentChild']);
                    $graduatedPrice += $addAmountNet - (($xtPrice->cStatus['customers_status_discount_attributes'])
                            ? ($addAmountNet * ($xtPrice->xtcCheckDiscount($this->item['products_id']) / 100))
                            : 0
                        );
                }
                $reduction = $price - $graduatedPrice;
                
                $tierPriceModel = new Shopgate_Model_Catalog_TierPrice();
                $tierPriceModel->setFromQuantity($quantity['quantity']);
                $tierPriceModel->setReduction($priceHelper->formatPriceNumber($reduction, 6));
                $tierPriceModel->setReductionType(Shopgate_Model_Catalog_TierPrice::DEFAULT_TIER_PRICE_TYPE_FIXED);
                $tierPriceModel->setAggregateChildren(true); // tier prices are always aggregated in
                $tierPriceModel->setCustomerGroupUid($customerGroupId);
                
                if (round($reduction, 6) > 0) {
                    $priceModel->setSalePrice($price);
                    if (!empty($oldPrice) && $oldPrice > 0) {
                        $priceModel->setPrice($oldPrice);
                    }
                    $priceModel->addTierPriceGroup($tierPriceModel);
                }
            }
        }
    }
    
    /**
     * calculate the tier price to a product depending on the item type (normal product or child product)
     *
     * @param xtcPrice_ORIGIN $xtPrice
     * @param array           $quantity
     *
     * @return array|float|int|string
     */
    private function calculateGraduatedPriceGross(xtcPrice_ORIGIN $xtPrice, $quantity)
    {
        if ($this->getIsChild() && $this->checkAttributeIsProperty()) {
            return $xtPrice->xtcGetPrice(
                $this->item['products_id'],
                false,
                $quantity['quantity'],
                $this->item['products_tax_class_id'],
                $this->item['products_price'],
                1,
                0,
                true,
                true,
                $this->cache['currentChild']['order_info']['products_properties_combis_id']
            );
        }
        
        return $xtPrice->xtcGetPrice(
            $this->item['products_id'],
            false,
            $quantity['quantity'],
            $this->item['products_tax_class_id'],
            $this->item['products_price'],
            1
        );
    }
    
    /**
     * calculates the tax rate to a product
     * uses a cache to prevent multiple calculation
     *
     * @return int|null
     */
    private function calculateTaxRate()
    {
        if (!empty($this->cache['taxRate'])) {
            return $this->cache['taxRate'];
        }
        
        $locationModel = new ShopgateLocationModel($this->config);
        
        $this->cache['taxRate'] = $locationModel->getTaxRateToProduct(
            $this->item['products_tax_class_id'],
            true,
            $this->ggxVersion,
            $this->countryId,
            $this->zoneId
        );
        
        return $this->cache['taxRate'];
    }
    
    /**
     * calculates the price to an option or input field
     *
     * @param array $option
     *
     * @return float|string
     * @throws ShopgateLibraryException
     */
    private function calculateOptionPrice(array $option)
    {
        $priceModel = new ShopgatePriceModel($this->config, $this->languageId, $this->exchangeRate);
        $taxRate    = $this->calculateTaxRate();
        
        $result                          = $priceModel->getCustomerGroupMaxPriceDiscount();
        $customerGroupDiscountAttributes = $result['customerGroupMaxDiscountAttributes'];
        
        $priceModel->getPriceToItem($this->item, $taxRate, $price, $oldPrice, $productDiscount, false, false);
        
        $price = $option['price_prefix'] == "-"
            ? $option['options_values_price'] * (-1)
            : $option['options_values_price'];
        
        if ($customerGroupDiscountAttributes) {
            $price = $priceModel->getDiscountPrice($price, $productDiscount);
        }
        
        return $price;
    }
    
    /**
     * sum the price of all child items
     *
     * @param array $children
     *
     * @return int
     */
    private function calculateChildPrice(array $children)
    {
        $additionalPrice = 0;
        foreach ($children as $variation) {
            $additionalVariantPrice = $variation['options_values_price'];
            if ($variation['price_prefix'] == '-') {
                $additionalPrice -= $additionalVariantPrice;
            } else {
                $additionalPrice += $additionalVariantPrice;
            }
        }
        
        return $additionalPrice;
    }
    
    /**
     * uses a cache for the combination data to prevent multiple calculation
     *
     * @return mixed
     */
    protected function generatePropertyCombinations()
    {
        if (!empty($this->cache['propertyCombinations'])) {
            return $this->cache['propertyCombinations'];
        }
        
        $this->cache['propertyCombinations'] =
            $this->generatePropertyCombos($this->item, $this->calculateTaxRate());
        
        return $this->cache['propertyCombinations'];
    }
    
    /**
     * @param null|ShopgateItemXmlModel $parent
     */
    private function setParent($parent)
    {
        $this->parent = $parent;
    }
    
    /**
     * check if the current child has property attributes or default attributes
     *
     * @param null|array $attributeRow
     *
     * @return bool
     */
    private function checkAttributeIsProperty($attributeRow = null)
    {
        if (!empty($attributeRow)) {
            return !empty($attributeRow['order_info']['is_property_attribute']);
        }
        
        return (
            !empty($this->cache['currentChild'])
            && !empty($this->cache['currentChild']['order_info']['is_property_attribute'])
        );
    }
    
    /**
     * generate the uid for child products
     *
     * @param string $hash
     * @param array  $ids
     *
     * @return string
     */
    private function generateChildItemHash($hash, array $ids)
    {
        $hash     = md5($hash);
        $hash     = substr($hash, 0, 5);
        $idString = "";
        
        foreach ($ids AS $id) {
            $idString .= $id . "_";
        }
        
        return $idString . $hash;
    }
    
    /**
     * add tax to a price
     *
     * @param float|string $price
     * @param null|float   $taxRate
     *
     * @return float|string
     */
    private function addTaxes($price, $taxRate = null)
    {
        $tax = empty($taxRate) ? $this->calculateTaxRate() : $taxRate;
        
        return $tax <= 0 ? $price : ($price * (1 + ($tax / 100)));
    }
    
    /**
     * removes tax from a price
     *
     * @param float|string $price
     * @param null|float   $taxRate
     *
     * @return float|string
     */
    private function removeTaxes($price, $taxRate = null)
    {
        if(is_array($price) && isset($price['plain'])) {
            $price = $price['plain'];
        }
        $tax = empty($taxRate) ? $this->calculateTaxRate() : $taxRate;
        
        return $tax <= 0 ? $price : ($price / (1 + ($tax / 100)));
    }
    
    /**
     * fill a Shopgate_Model_Catalog_Input with data
     * 
     * @param array $field
     * @return Shopgate_Model_Catalog_Input
     */
    private function generateInputModelForGxInputField(array $field)
    {
        for($i =1; $i <= 10; $i++) {
            if(!empty($field["input_field_{$i}_number"])) {
                $input = new Shopgate_Model_Catalog_Input();
                $input->setUid($field["input_field_{$i}_number"]);
                $input->setType(Shopgate_Model_Catalog_Input::DEFAULT_INPUT_TYPE_TEXT);
                $input->setAdditionalPrice($field["input_field_{$i}_add_amount"]);
                $input->setLabel($field["input_field_{$i}_label"]);
                return $input;
            }
        }
        return null;
    }
}
