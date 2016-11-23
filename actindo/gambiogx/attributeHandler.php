<?php

/**
* Class attributeHandler
* @package actindo
* @author  Patrick Prasse <pprasse@actindo.de>
* @author  Chris Westerfield <westerfield@actindo.de>
* @version $Revision: 511 $
* @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
* @license http://opensource.org/licenses/GPL-2.0 GNU Public License
*/
class attributeHandler
{
    //Quantity Modes
    const QUANTITY_STANDARD = 0;
    const QUANTITY_STOCK = 1;
    const QUANTITY_COMBINED = 2;
    const QUANTITY_NOVALIDATION = 3;
    //Weight
    const WEIGHT_ADD = 0;
    const WEIGHT_SET = 1;
    //Shipping Time
    const SHIPPING_TIME_ARTICLE = 0;
    const SHIPPING_TIME_COMBINED = 1;
    /**
     * Languages
     * @var array
     */
    protected static $languages;
    /**
     * contains the last error number
     * @var string
     */
    protected $errorNumber;
    /**
     * contains the last error message
     * @var string
     */
    protected $errorMessage;
    /**
     * this variable is set to true if an error appears during the process call
     * @var boolean
     */
    protected $error = false;
    /**
     * this variable declares the direction in which the export or import works
     * @var boolean
     */
    protected $toActindo;
    /**
     * Article Id
     * @var integer
     */
    protected $articleId;
    /**
     * Mode of Shipping
     * @var int
     */
    protected $weightMode;
    /**
     * Quantity Mode
     * @var int
     */
    protected $quantityMode;
    /**
     * contains the Advanced Combination
     * @var array
     */
    protected $combinationAdvanced;
    /**
     * contains the Simple Combination
     * @var array
     */
    protected $combinationSimple;
    /**
     * contains the Names of the Attributes
     * @var array
     */
    protected $names;
    /**
     * contains the Attribute Values
     * @var array
     */
    protected $values;
    /**
     * contains the Stock Advanced
     * @obsolete
     * @var array
     */
    protected $bestandAdvanced;
    /**
     * contains the simple stock
     * @obsolete
     * @var array
     */
    protected $bestandSimple;
    /**
     * contains the Advanced Combination
     * @var array
     */
    protected $product;
    /**
     * Attribute Set
     * @var array
     */
    protected $attributeSet=array();
    /**
     * Article Number
     * @var string
     */
    protected $articleNumber;
    /**
     * Validation for Article Settings
     * @var array
     */
    protected $validation = array(
        'quantity'=>array(0,1,2,3),
        'weight'=>array(0,1),
        'shipping'=>array(0,1)
    );
    /**
     * Product Calculated Stock
     * @var float
     */
    protected $stock=0.00;
    protected $vpe=false;
    protected $parentArtNr = '';
    //PUBLIC METHODS
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * setter for Getting Actindo Data into this Class
     * @param array $productArray Actindo ERP Product Array
     * @return void
     */
    public function setProductData($productArray)
    {
        $this->product = $productArray;
        $this->articleNumber = $this->product['art_nr'];
        if((int)$this->product['shop']['art']['products_vpe_status'] == 1)
        {
            $this->vpe = array(
                $this->articleNumber=>array(
                    'value'=>$this->product['shop']['art']['products_vpe_value'],
                    'type'=>$this->product['shop']['art']['products_vpe']
                ),
            );
        }
        else
        {
            $this->vpe = array();
        }
        foreach($this->product['shop']['attributes']['combination_advanced'] as $artId=>$values)
        {
            if(isset($values['shop']) && !empty($values['shop']) && isset($values['shop']['art']) && !empty($values['shop']['art']) && isset($values['shop']['art']['products_vpe_status']) && (int)$values['shop']['art']['products_vpe_status']===1)
            {
                $this->vpe[$artId] = array(
                    'value'=>$values['shop']['art']['products_vpe_value'],
                    'type'=>$values['shop']['art']['products_vpe'],
                );
            }
        }
        $this->names = &$this->product['shop']['attributes']['names'];
        $this->values = &$this->product['shop']['attributes']['values'];
        $this->combinationAdvanced = &$this->product['shop']['attributes']['combination_advanced'];
        $this->bestandAdvanced = &$this->product['shop']['attributes']['l_bestand_advanced'];
        $this->bestandSimple = &$this->product['shop']['attributes']['l_bestand_simple'];
        $this->combinationSimple = &$this->product['shop']['attributes']['combination_simple'];
        return $this;
    }

    /**
     * Processes the Attribute Creation with all its sub steps
     */
    public function process()
    {
        if($this->toActindo===null)
        {
            $this->error = true;
            $this->errorNumber = 2361;
            $this->errorMessage = 'No Operation Direction was set';
        }
        elseif($this->toActindo)
        {
            $this->export();
        }
        else
        {
            $this->articleNumber = $this->product['art_nr'];
            $this->import();
        }
        return $this;
    }

    /**
     * method to check if an error occured
     * @return bool
     */
    public function errorExists()
    {
        return (bool)$this->error;
    }

    /**
     * returns the error String that appeared within attribute creation
     * @return array
     */
    public function getErrorMessage()
    {
        return array(
            'ok'=>false,
            'errno'=>$this->errorNumber,
            'error'=>$this->errorMessage
        );
    }

    /**
     * sets the Direction of the Operation
     * to Actindo
     * @return $this
     */
    public function setToActindo()
    {
        $this->toActindo = true;
        return $this;
    }

    /**
     * sets the Direction of the Operation
     * to Shopsystem
     * @return $this
     */
    public function setToShop(){
        $this->toActindo = false;
        return $this;
    }

    /**
     * check to see if attributes exist!
     * @return bool
     */
    public function attributesExist()
    {
        if(count($this->attributeSet)===4)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /**
     * Returns Attributes to Actiondo
     * or Returns the set Product Array
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributeSet;
    }
    /**
     * Total Stock
     * @return float
     */
    public function getCummulativeStock()
    {
        return $this->stock;
    }
    public function setArticleId($id)
    {
        $this->articleId = $id;
    }
    //PROTECTED METHODS
    /**
     * Do Export
     */
    protected function export()
    {
        //get Article Settings
        $sql = '
            SELECT
               p.products_model as articleId,
               p.use_properties_combis_weight,
               p.use_properties_combis_quantity,
               p.use_properties_combis_shipping_time,
               p.products_price,
               p.products_tax_class_id taxClassId
            FROM
               products p
            WHERE
               p.products_id = '.(int)$this->articleId.'
           ;
        ';
        $query = act_db_query($sql);
        if(act_db_num_rows($query)>0)
        {
            $result = act_db_fetch_assoc($query);
            $this->quantityMode = (
            in_array($result['use_properties_combis_quantity'],$this->validation['quantity'])
                ?(int)$result['use_properties_combis_quantity']
                :0
            );
            $this->weightMode = (
            in_array($result['use_properties_combis_weight'],$this->validation['weight'])
                ?(int)$result['use_properties_combis_weight']
                :0
            );
            $this->shippingTimeMode = (
            in_array($result['use_properties_combis_shipping_time'],$this->validation['shipping'])
                ?(int)$result['use_properties_combis_shipping_time']
                :0
            );
            $this->articleNumber = $result['articleId'];
            $taxClassId = $result['taxClassId'];
            $price = $result['products_price'];
        }else{
            return;
        }
        /**
         *  Prepare Customer Price Groups and fill with prices
         */
        $sql = '
            SELECT
                cs.customers_status_id,
                cs.customers_status_show_price_tax
            FROM
                customers_status cs
            GROUP BY cs.customers_status_id
            ;
        ';
        $query = act_db_query($sql);
        $priceGroups = array();
        $run = 0;
        $grundpreis = 0;
        $taxed = 0;
        while($result = act_db_fetch_array($query))
        {
            $customerGroup = (int)$result['customers_status_id'];
            $factor = 100;
            $taxed = 0;
            if((int)$result['customers_status_show_price_tax']>0)
            {
                $factor += act_get_tax_rate($taxClassId);
                $taxed = 1;
            }
            $taxPrice = (float)$price*$factor/100;
            $taxPrice = round($taxPrice,4);
            $priceGroup = array(
                'is_brutto'=>$taxed,
                'grundpreis'=>$taxPrice
            );
            if($run == 0)
            {
                $grundpreis = $taxPrice;
                $brutto = $taxed;
            }
            $run++;
            $priceGroups[$customerGroup] = $priceGroup;
        }
        /**
         * Prepare Attribute Data Set
         */
        $this->attributeSet = array(
            'combination_advanced'=>array(),
            'names'=>array(),
            'values'=>array(),
            'combination_simple'=>array()
        );
        //prepare Names and Values
        $combination = array();
        $sql = '
            SELECT
                lng.code as language,
                ppi.products_properties_combis_id as combiId,
                ppi.properties_id as propertyId,
                ppi.properties_values_id as valueId,
                ppi.properties_name as property,
                ppi.values_name as value,
                pv.value_price as valuePrice,
                pv.value_model
            FROM
                products_properties_index ppi
            LEFT JOIN
                languages lng
                ON
                lng.languages_id=ppi.language_id
            LEFT JOIN
                properties_values pv
                ON
                pv.properties_values_id = ppi.properties_values_id
            WHERE
                products_id = '.(int)$this->articleId.';
        ';
        $query = act_db_query($sql);
        /**
         * get data sets from the index table as this is the most complete table with the smallest ammounts of
         * queries
         */
        if(act_db_num_rows($query) > 0)
        {
            while($result = act_db_fetch_assoc($query))
            {
                $combiId = $result['combiId'];
                $property = $result['property'];
                $propertyId = (int)$result['propertyId'];
                $value = $result['value'];
                $valueId = (int)$result['valueId'];
                $language = $result['language'];
                $valuePrice = $result['valuePrice'];
                $valueModel = $result['value_model'];
                if(!isset($this->attributeSet['names'][$propertyId]))
                {
                    $this->attributeSet['names'][$propertyId] = array();
                }
                $this->attributeSet['names'][$propertyId][$language] = $property;
                if(!isset($this->attributeSet['values'][$propertyId]))
                {
                    $this->attributeSet['values'][$propertyId] = array();
                }
                if(!isset($this->attributeSet['values'][$propertyId][$valueId]))
                {
                    $this->attributeSet['values'][$propertyId][$valueId] = array();
                }
                $this->attributeSet['values'][$propertyId][$valueId][$language] = $value;
                if(!isset($combination[$combiId])){
                    $combination[$combiId] = array(
                        'name'=>array(),
                        'value'=>array(),
                        'price'=>array(),
                        'model'=>array(),
                    );
                }
                if(!in_array($propertyId,$combination[$combiId]['name']))
                {
                    $id = count($combination[$combiId]['name']);
                    $combination[$combiId]['name'][$id] = $propertyId;
                    $combination[$combiId]['value'][$id] = $valueId;
                    $combination[$combiId]['price'][$id] = $valuePrice;
                    $combination[$combiId]['model'][$id] = $valueModel;
                }
            }
        }else{
            return;
        }
        /**
         * get Attribute Values
         */
        $sql = '
            SELECT
                ppc.products_properties_combis_id as combiId,
                ppc.products_id as productId,
                ppc.sort_order as sortOrder,
                ppc.combi_model as articleNumber,
                ppc.combi_quantity as quantity,
                ppc.combi_shipping_status_id as shippingStatusId,
                ppc.combi_weight as weight,
                ppc.combi_price_type as priceType,
                ppc.combi_price as price,
                ppc.combi_image as image,
                ppc.products_vpe_id,
                ppc.vpe_value
            FROM
                products_properties_combis ppc
            WHERE
              ppc.products_id='.(int)$this->articleId.';
        ';
        $query = act_db_query($sql);
        if(act_db_num_rows($query) > 0)
        {
            while($result = act_db_fetch_array($query))
            {
                $combiId = $result['combiId'];
                $productId = $result['productId'];
                $sortOrder = $result['sortOrder'];
                $articleNumber = $result['articleNumber'];
                $quantity = $result['quantity'];
                $shippingStatusId = $result['shippingStatusId'];
                $weight = $result['weight'];
                $priceType = $result['priceType'];
                $price = $result['price'];
                $image = $result['image'];
                $vpevalue = $result['vpe_value'];
                $vpeproductsvpe = $result['products_vpe_id'];
                $combiAdvanced = array();
                $articlePrices = null;
                /**
                 * if the attribute combination does not exist attach it
                 */
                if(!isset($combiAdvanced[$articleNumber]))#$this->articleNumber.
                {
                    $calcPrice = (float)0;
                    for($i=0;$i<count($combination[$combiId]['name']);$i++)
                    {
                        $propertyId = $combination[$combiId]['name'][$i];
                        $valueId = $combination[$combiId]['value'][$i];
                        $valuePrice = $combination[$combiId]['price'][$i];
                        $valueModel = $combination[$combiId]['model'][$i];
                        $combiSimple = array(
                            'options_values_price'=>(float)$valuePrice,
                            'attributes_model'=>$valueModel,
                            'options_values_weight'=>0,
                            'sortorder'=>$sortOrder
                        );
                        $calcPrice += (float)$valuePrice;
                        if(!isset($this->attributeSet['combination_simple'][$propertyId]))
                        {
                            $this->attributeSet['combination_simple'][$propertyId] = array();
                        }
                        $this->attributeSet['combination_simple'][$propertyId][$valueId] = $combiSimple;
                    }
                    $articlePrices = $priceGroups;
                    if((float)$price > (float)0)
                    {
                        if(is_array($articlePrices) && count($articlePrices) > 0)
                        {
                            foreach($articlePrices as $id => $value)
                            {
                                $articlePrices[$id]['grundpreis'] = (float)$price + (float)$articlePrices[$id]['grundpreis'];
                            }
                        }
                        $grundpreisArticle = $grundpreis + (float)$price;
                    }else{
                        if(is_array($articlePrices) && count($articlePrices) > 0)
                        {
                            foreach($articlePrices as $id => $value)
                            {
                                $articlePrices[$id]['grundpreis'] = (float)$calcPrice + (float)$articlePrices[$id]['grundpreis'];
                            }
                        }
                        $grundpreisArticle = (float)$grundpreis + $calcPrice;
                    }

                    if( strlen($file_name = $image) )
                    {
                        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
                        {
                            $subpath = 'product_images/properties_combis_images/'.$file_name;
                            $path = DIR_FS_CATALOG_IMAGES.$subpath;
                            if(file_exists(DIR_FS_CATALOG_IMAGES.'product_images/properties_combis_images/'.$file_name))
                            {
                                $imageContent = file_get_contents( $path );
                                $size = getimagesize( $path );
                                $imageType = image_type_to_mime_type( $size[2] );
                                $image = array(
                                    'image_size' => $size,
                                    'image_type' => $imageType,
                                    'image_name' => $subpath,
                                    'image_nr'   => 0,
                                    'image'      => $imageContent
                                );
                            }else{

                            }
                        }
                    }
                    /**
                     * build the combination
                    $vpevalue = $result['vpe_value';
                    $vpeproductsvpe = $result['products_vpe_id'];
                     */
                    $combiAdvanced = array(
                        'attribute_name_id'=>$combination[$combiId]['name'],
                        'attribute_value_id'=>$combination[$combiId]['value'],
                        data=>array(
                            'products_status'=>1,
                            'products_ean'=>''
                        ),
                        'shop'=>array(
                            'images'=>array(
                                $image
                            ),
                            'art'=>array(
                                'products_weight'=>$weight,
                            ),
                        ),
                        'is_brutto'=>$brutto,
                        'preisgruppen'=>$articlePrices,
                        'grundpreis'=>$grundpreisArticle,
                        'l_bestand'=>(float)$quantity,
                    );
                    if(!empty($vpevalue))
                    {
                        $combiAdvanced['shop']['art']['products_vpe_status'] = 1;
                        $combiAdvanced['shop']['art']['products_vpe_value'] = $vpevalue;
                        if(!empty($vpeproductsvpe) && (int)$vpeproductsvpe > 0)
                        {
                            $combiAdvanced['shop']['art']['products_vpe'] = $vpeproductsvpe;
                        }
                    }
                    /**
                     * attach it
                     */
                    $this->stock += (float)$quantity;
                    $this->attributeSet['combination_advanced'][$this->articleNumber.'-'.$articleNumber] = $combiAdvanced;
                }
            }
        }
    }

    /**
     * Method to do the export
     */
    protected function import()
    {
        /**
         * get Language Set and prepare mapping object
         */
        $mapping = array(
            'language'=>self::getLanguages(),
            'shop'=>array(
                'properties'=>array(),
                'values'=>array(),
            ),
        );
        /**
         * create variant sets
         */
        $this->importVariantSet($mapping);
        $this->checkAndDelete();
        /**
         * Add Variants
         */
        $this->importVariants($mapping);
    }

    public function checkAndDelete()
    {
        /**
         * Cleanup old Variant Sets
         */
        $sql = '
            SELECT
                products_properties_combis_id,
                combi_image
            FROM products_properties_combis
            WHERE
                products_id='.(int)$this->articleId.';
        ';
        $query = act_db_query($sql);
        if(act_db_num_rows($query)>0)
        {
            while($result = act_db_fetch_assoc($query))
            {
                $path = DIR_FS_CATALOG_IMAGES.'product_images/properties_combis_images/'.$result['combi_image'];
                if(!empty($result['combi_image']) && file_exists($path))
                {
                    @unlink($path);
                }
                $sql = '
                  DELETE FROM
                      products_properties_combis_values
                  WHERE
                      products_properties_combis_id = '.$result['products_properties_combis_id'].';
                  ';
                act_db_query($sql);
            }
        }
        $sql = 'DELETE FROM products_properties_admin_select WHERE products_id='.(int)$this->articleId.';';
        act_db_query($sql);
        $sql = 'DELETE FROM products_properties_combis WHERE products_id='.(int)$this->articleId.';';
        act_db_query($sql);
        $sql = 'DELETE FROM products_properties_index WHERE products_id='.(int)$this->articleId.';';
        act_db_query($sql);
    }

    /**
     * Import Variants
     * @param $mapping
     */
    protected function importVariantSet(&$mapping)
    {
        $search = array();
        $existing = array();
        /**
         * get existing properties and remove them from the existing array, which is filled from the
         * names variable
         */
        if(count($this->names) > 0)
        {
            foreach($this->names as $namesId=>$nameValueArray)
            {
                foreach($nameValueArray as $languageId=>$value)
                {
                    if(!empty($value))
                    {
                        $search[] = '\''.$value.'\'';
                        $existing[$value] = $namesId;
                    }
                }
            }
        }
        $search = implode(',',$search);
        $sql = '
            SELECT
                *
            FROM
                properties_description
            WHERE
                properties_name IN ('.$search.');
        ';
        $query = act_db_query($sql);
        if(act_db_num_rows($query) > 0)
        {
            while($result = act_db_fetch_assoc($query))
            {
                if(isset($existing[$result['properties_name']]))
                {
                    $property = $existing[$result['properties_name']];
                    if(!isset($mapping[$property])){
                        $mapping[$property] = array(
                            'id'=>$result['properties_id'],
                            'names'=>array(),
                        );
                    }
                    unset($existing[$result['properties_name']]);
                    $mapping[$property]['names'][$result['language_id']] = $result['properties_name'];
                    $mapping['shop']['properties'][$result['properties_id']] = $property;
                }
            }
        }
        /**
         * Create Properties
         */
        if(is_array($existing) && count($existing) > 0){
            $existing = array_values(array_unique($existing));
            foreach($existing as $valueID)
            {
                $names = $this->names[$valueID];
                $sql = 'INSERT INTO properties (sort_order) VALUES (1);';
                $query = act_db_query($sql);
                $propertiesId = act_db_insert_id($query);
                $mapping[$valueID] = array(
                    'id'=>$propertiesId
                );
                $mapping['shop']['properties'][$propertiesId] = $valueID;
                foreach($names as $languageCode=>$translation)
                {
                    $languageId = $mapping['language']['code'][$languageCode];
                    $sql = '
                        INSERT INTO
                        properties_description
                        (
                          properties_id,
                          language_id,
                          properties_name,
                          properties_admin_name
                        )
                        VALUES
                        (
                          '.(int)$propertiesId.',
                          '.(int)$languageId.',
                          \''.esc($translation).'\',
                          \''.esc($translation).'\'
                        );
                    ';
                    act_db_query($sql);
                    $mapping[$valueID]['names'][(int)$languageId] = $translation;
                }
            }
        }
        /**
         * build values and fill the existing variable with values from the actindo values
         * remove existing values
         */
        if(count($this->values) > 0)
        {
            $existing = array('counter'=>0);
            $searchArray = array();
            foreach($this->values as $actindoPropertyId=>$values)
            {
                $gambioPropertyId = $mapping[$actindoPropertyId]['id'];
                $search = array();
                foreach($values as $actindoValueId=>$valueNames)
                {
                    foreach($valueNames as $languageId => $value)
                    {
                    	if(empty($value))
                    	{
                    		continue;
                    	}
                        $search[] = '\''.$value.'\'';
                        if(!isset($existing[$actindoPropertyId][$value]))
                        {
                            $existing['counter']++;
                        }
                        $existing[$actindoPropertyId][$value] = $actindoValueId;
                    }
                }
                $search = implode(',',$search);
                $searchArray[] = ' (
                pvd.values_name IN ('.$search.')
                and
                pv.properties_id='.(int)$gambioPropertyId.'
                ) '."\n";
            }
        }
        $sql = '
            SELECT
                pvd.properties_values_description_id as propertiesValuesDescriptionId,
                pvd.properties_values_id as propertiesValuesId,
                pvd.values_name as valueName,
                pvd.language_id as LanguageId,
                pv.properties_id as propertiesId
            FROM
                properties_values_description pvd
            LEFT JOIN
                properties_values pv
                ON
                pv.properties_values_id = pvd.properties_values_id
            WHERE
                '.implode(' or ',$searchArray).'
            ;
        ';
        /**
         * pre process and remove existing properties
         */
        $query = act_db_query($sql);
        if(act_db_num_rows($query) > 0)
        {
            while($result = act_db_fetch_assoc($query))
            {
                $valueName = $result['valueName'];
                $actindoPropertyId = $mapping['shop']['properties'][$result['propertiesId']];
                if(isset($existing[$actindoPropertyId][$valueName]))
                {
                    $valueId = $existing[$actindoPropertyId][$valueName];
                    if(!empty($actindoPropertyId))
                    {
                        if(!isset($mapping[$actindoPropertyId][$valueId]))
                        {
                            $mapping[$actindoPropertyId][$valueId] = array(
                                'id'=>$result['propertiesValuesId'],
                                'names'=>array(),
                            );
                        }
                        $mapping[$actindoPropertyId][$valueId]['names'][$result['LanguageId']] = $valueName;
                        $existing['counter']--;
                        unset(
                            $existing[$actindoPropertyId][$valueName]
                        );
                        if(count($existing[$actindoPropertyId])<1)
                        {
                            unset($existing[$actindoPropertyId]);
                        }
                    }
                }
            }
        }
        /**
         * Create missing values
         */
        if($existing['counter']>0){
            $processIds = array();
            foreach($existing as $actindoPropertyId=>$propertyArray)
            {
                if(!is_array($propertyArray))
                {
                    continue;
                }
                $propertyArray = array_values(array_unique($propertyArray));
                foreach($propertyArray as $actindoValueId)
                {
                    $propertyId = $mapping[$actindoPropertyId]['id'];
                    $names = $this->values[$actindoPropertyId][$actindoValueId];
                    $model = $this->combinationSimple[$actindoPropertyId][$actindoValueId]['attributes_model'];
                    $price = $this->combinationSimple[$actindoPropertyId][$actindoValueId]['options_values_price'];
                    $sql = '
                        INSERT INTO
                        properties_values
                            (properties_id,sort_order,value_model,value_price)
                        VALUES
                            ('.(int)$propertyId.',1,\''.esc($model).'\','.(float)$price.')
                        ;
                    ';
                    $query = act_db_query($sql);
                    $propertyValueID = act_db_insert_id($query);
                    $mapping[$actindoPropertyId][$actindoValueId] =  array(
                        'id'=>$propertyValueID,
                        'names'=>array(),
                    );
                    $mapping['shop']['values'][$propertyValueID] = $actindoValueId;
                    foreach($names as $languageCode=>$translation)
                    {
                        $languageId = $mapping['language']['code'][$languageCode];
                        $sql = '
                        INSERT INTO
                        properties_values_description
                        (properties_values_id,language_id,values_name)
                        VALUES
                        ('.(int)$propertyValueID.','.(int)$languageId.',\''.$translation.'\');
                    ';
                        act_db_query($sql);
                        $mapping[$actindoPropertyId][$actindoValueId]['names'][(int)$languageId] = $translation;
                    }
                }
            }
        }
    }

    /**
     * Import Variants
     * @param $mapping
     */
    protected function importVariants($mapping)
    {
        /**
         * Fill Admin Select Table Data
         */
        foreach($mapping as $ident=>$map)
        {
            if($ident == 'language' || $ident == 'shop')
            {
                continue;
            }
            if(isset($map['id']))
            {
                $sql = '
                    SELECT
                        properties_values_id
                    FROM
                        properties_values
                    WHERE
                        properties_id='.(int)$map['id'].'
                    GROUP BY properties_values_id
                    ;';
                $query = act_db_query($sql);
                while($result = act_db_fetch_assoc($query))
                {
                    $sql = '
                        SELECT
                            products_id
                        FROM products_properties_admin_select
                        WHERE
                        products_id='.(int)$this->articleId.'
                        and
                        properties_id='.(int)$map['id'].'
                        and
                        properties_values_id = '.(int)$result['properties_values_id'].';
                        ';
                    $subQuery = act_db_query($sql);
                    if(!(act_db_num_rows($subQuery) > 0))
                    {
                        $sql = '
                        INSERT INTO
                        products_properties_admin_select
                            (
                              products_id,
                              properties_id,
                              properties_values_id
                            )
                        VALUE
                            (
                              '.(int)$this->articleId.',
                              '.(int)$map['id'].',
                              '.(int)$result['properties_values_id'].'
                            )
                        ;
                        ';
                        act_db_query($sql);;
                    }
                }
            }
        }
        /**
         * get Core Data
         */
        $sql = '
            SELECT
                products_price,
                products_tax_class_id
            FROM
              products
            WHERE
                products_id = '.(int)$this->articleId.'
            ;
        ';
        $query = act_db_query($sql);
        $result = act_db_fetch_assoc($query);
        /**
         * get Price Groups
         */
        $productPrice = (float)$result['products_price'];
        $taxClassId = (int)$result['products_tax_class_id'];
        $sql = '
            SELECT
                cs.customers_status_id,
                cs.customers_status_show_price_tax
            FROM
                customers_status cs
            GROUP BY cs.customers_status_id
            ;
        ';
        $query = act_db_query($sql);
        $priceGroups = array();
        while($result = act_db_fetch_array($query))
        {
            $customerGroup = (int)$result['customers_status_id'];
            $factor = 100;
            $taxed = 0;
            if((int)$result['customers_status_show_price_tax']>0)
            {
                $factor += act_get_tax_rate($taxClassId);
                $taxed = 1;
            }
            $taxPrice = (float)$productPrice * $factor / 100;
            $taxPrice = round($taxPrice,4);
            $priceGroup = array(
                'is_brutto'=>$taxed,
                'grundpreis'=>$taxPrice
            );
            $priceGroups[$customerGroup] = $priceGroup;
        }
        $adminSelect = array();
        $counter = 1;
        /**
         * Process Variants
         */
        foreach($this->combinationAdvanced as $articleId => $combination)
        {
            /**
             * Create Combis Table Data
             */
            $combiModel = $articleId;
	        //$combiModel = str_replace($this->articleNumber.'-','',$combiModel);
            $upprice = (float)$combination['preisgruppen'][0]['grundpreis'] - (float)$priceGroups[0]['grundpreis'];
            $insertDefinitions = array(
                'products_id',
                'sort_order',
                'combi_model',
                'combi_quantity',
                'combi_weight',
                'combi_price'
            );
            if(count($this->vpe) > 0)
            {
                $insertDefinitions[] = 'products_vpe_id';
                $insertDefinitions[] = 'vpe_value';
            }
            $insertValues = array(
                (int)$this->articleId,
                $counter,
                '\''.esc($combiModel).'\'',
                (int)$combination['l_bestand'],
                (float)$combination['shop']['art']['products_weight'],
                $upprice
            );
            $counter++;
            if(count($this->vpe) > 0)
            {
                if(isset($this->vpe[$articleId]) && is_array($this->vpe[$articleId]) && count($this->vpe[$articleId])==2)
                {
                    $insertValues[] = $this->vpe[$articleId]['type'];
                    $insertValues[] = $this->vpe[$articleId]['value'];
                }
                else
                {
                    $insertValues[] = $this->vpe[$this->articleNumber]['type'];
                    $insertValues[] = $this->vpe[$this->articleNumber]['value'];
                }
            }
            if(isset($combination['shop']['images'][1]))
            {
                $insertDefinitions[] = 'combi_image';
                $prefix = $articleId.'-';
                $imageName = $combination['shop']['images'][1]['image_name'];
                if(strpos($imageName,$prefix)===false)
                {
                    $imageName = $prefix.$imageName;
                }
                $insertValues[] = '\''.$imageName.'\'';
                file_put_contents(
                    DIR_FS_CATALOG_IMAGES.'product_images/properties_combis_images/'.$imageName,
                    $combination['shop']['images'][1]['image']
                );
            }
            $sql = '
                INSERT INTO
                products_properties_combis
                (
                  '.implode(',',$insertDefinitions).'
                )
                VALUES
                (
                    '.implode(',',$insertValues).'
                );
            ';
            $query = act_db_query($sql);
            $combisId = act_db_insert_id($query);
            $attributePropertiesKeys = array();
            //run Admin Select
            for($i=0;$i<count($combination['attribute_name_id']);$i++)
            {
                $combiAttributeId = $combination['attribute_name_id'][$i];
                $combiVariantValueId = $combination['attribute_value_id'][$i];
                $propertyId = (int)$mapping[$combiAttributeId]['id'];
                $valueId = (int)$mapping[$combination['attribute_name_id'][$i]][$combiVariantValueId]['id'];
                /**
                 * Fill Properties Combis Values Table
                 */
                $sql = '
                    INSERT INTO
                        products_properties_combis_values
                    (products_properties_combis_id,properties_values_id)
                    VALUES
                    ('.(int)$combisId.','.$valueId.');
                ';
                act_db_query($sql);
                foreach($mapping['language']['code'] as $languageCode => $languageId)
                {
                    /**
                     * Last but not least fill the properties index!
                     * This table is required by the exporter!
                     */
                    $sql = '
                        INSERT INTO
                            products_properties_index
                        (
                            products_id,
                            language_id,
                            properties_id,
                            products_properties_combis_id,
                            properties_values_id,
                            properties_name,
                            properties_admin_name,
                            properties_sort_order,
                            values_name,
                            value_sort_order
                        )
                        VALUES(
                          '.(int)$this->articleId.',
                          '.(int)$languageId.',
                          '.(int)$propertyId.',
                          '.(int)$combisId.',
                          '.(int)$valueId.',
                          \''.esc($mapping[$combiAttributeId]['names'][$languageId]).'\',
                          \''.esc($mapping[$combiAttributeId]['names'][$languageId]).'\',
                          1,
                          \''.esc($mapping[$combiAttributeId][$combiVariantValueId]['names'][$languageId]).'\',
                          1
                        );
                    ';
                    act_db_query($sql);
                }
            }
        }
    }
    //static Methods
    /**
     * get the Languages
     * @return array
     * @static
     */
    public static function getLanguages()
    {
        if(self::$languages===null)
        {
            self::$languages = array(
                'id'=>array(),
                'code'=>array(),
            );
            $sql = 'SELECT languages_id,code FROM languages;';
            $query = act_db_query($sql);
            if(act_db_num_rows($query) > 0)
            {
                while($result = act_db_fetch_array($query))
                {
                    self::$languages['id'][$result['languages_id']] = $result['code'];
                    self::$languages['code'][$result['code']] = $result['languages_id'];
                }
            }
        }
        return self::$languages;
    }

    public function updateStock($art)
    {
        if(count($art['attributes']['combination_advanced']) > 0)
        {
            foreach($art['attributes']['combination_advanced'] as $modelId=>$data)
            {
                $sql = 'update products_properties_combis SET combi_quantity='.(float)$data['l_bestand'].' WHERE combi_model=\''.esc($modelId).'\';';
                act_db_query($sql);
            }
        }
    }
}
