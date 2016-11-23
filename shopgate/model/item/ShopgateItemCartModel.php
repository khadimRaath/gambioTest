<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */
class ShopgateItemCartModel extends ShopgateObject
{

    private $languageId;

    /**
     * ShopgateItemCartModel constructor.
     *
     * @param $languageId
     */
    public function __construct($languageId)
    {
        $this->languageId = $languageId;
    }

    /**
     * if the current order item (product) is an child product the item number is
     * generated in the schema <productId>_<attributeId>
     *
     * this function returns the id, the product has in the shop system
     *
     * @param ShopgateOrderItem $product
     *
     * @return mixed
     */
    public function getProductsIdFromCartItem($product)
    {
        $id = $product->getParentItemNumber();
        if (empty($id)) {
            $info = json_decode($product->getInternalOrderInfo(), true);
            if (!empty($info) && isset($info["base_item_number"])) {
                $id = $info["base_item_number"];
            }
        }

        return !empty($id) ? $id : $product->getItemNumber();
    }

    /**
     * gather all uids from options to an product
     *
     * @param ShopgateOrderItem $product
     *
     * @return array
     */
    public function getCartItemOptionIds($product)
    {
        $optionIdArray = array();
        $options       = $product->getOptions();
        if (!empty($options)) {
            foreach ($options AS $option) {
                $optionIdArray[] = $option->getValueNumber();
            }
        }

        return $optionIdArray;
    }

    /**
     * gather all uids from attributes to an product
     *
     * @param ShopgateOrderItem $product
     *
     * @return array
     */
    public function getCartItemAttributeIds($product)
    {
        $attributeIdArray = array();
        $orderInfos       = json_decode($product->getInternalOrderInfo(), true);

        if (empty($orderInfos)) {
            return $attributeIdArray;
        }

        foreach ($orderInfos as $info) {
            if (is_array($info)) {
                foreach ($info AS $key => $value) {
                    $attributeIdArray[] = $key;
                }
            }
        }

        return $attributeIdArray;
    }

    /**
     * read product data from database
     *
     * @param ShopgateOrderItem $item
     *
     * @return array|bool|mixed
     * @throws ShopgateLibraryException
     */
    public function getCartItemFromDatabase(ShopgateOrderItem $item)
    {
        $query = sprintf(
            "SELECT
                                p.*,
                                sp.specials_new_products_price
                                FROM %s AS p
                                LEFT JOIN %s AS sp ON sp.products_id = p.products_id
                                WHERE p.products_id = %s",
            TABLE_PRODUCTS,
            TABLE_SPECIALS,
            $this->getProductsIdFromCartItem($item)
        );

        $result = xtc_db_query($query);

        if (!$result) {
            throw new ShopgateLibraryException(
                ShopgateLibraryException::PLUGIN_DATABASE_ERROR,
                "Shopgate Plugin - Error checking for table \"" . TABLE_PRODUCTS . "\".",
                true
            );
        }

        return xtc_db_fetch_array($result);
    }

    /**
     * read all products data from database
     *
     * @param ShopgateCart $cart
     *
     * @return mixed
     * @throws ShopgateLibraryException
     */
    public function getCartItemsFromDatabase(ShopgateCart $cart)
    {
        $cartProducts = $cart->getItems();
        $itemIds      = array();
        $itemQuantityQuery
                      = "SELECT 
                                p.*,
                                sp.specials_new_products_price
                                FROM " . TABLE_PRODUCTS . " AS p
                                LEFT JOIN " . TABLE_SPECIALS . " AS sp ON sp.products_id = p.products_id
                                WHERE p.products_id IN (";

        foreach ($cartProducts as $product) {
            $itemIds[] = $this->getProductsIdFromCartItem($product);
        }

        $itemQuantityQuery .= implode(",", $itemIds) . ");";
        $result = xtc_db_query($itemQuantityQuery);

        if (!$result) {
            throw new ShopgateLibraryException(
                ShopgateLibraryException::PLUGIN_DATABASE_ERROR,
                "Shopgate Plugin - Error checking for table \"" . TABLE_PRODUCTS . "\".",
                true
            );
        }

        return $result;
    }

    /**
     * read all option data to an product from database and
     * create an ShopgateOrderItemOption object with the data
     *
     * @param $sgProduct
     * @param $orderInfos
     * @param $tax_rate
     *
     * @return array
     * @throws ShopgateLibraryException
     */
    public function getCartItemOptions(&$sgProduct, $orderInfos, $tax_rate)
    {
        $infos            = json_decode($orderInfos, true);
        $price            = 0;
        $weight           = 0;
        $resultAttributes = array();
        if (is_array($infos)) {
            foreach ($infos as $key => $attributes) {
                if (strpos($key, "attribute_") !== false) {
                    foreach ($attributes as $attributeId => $attribute) {
                        $attributeQuery
                            = "SELECT
                                    po.products_options_id AS 'option_number',
                                    po.products_options_name AS 'name',
                                    pov.products_options_values_id AS 'value_number',
                                    pov.products_options_values_name AS 'value',
                                    pa.weight_prefix,
                                    pa.options_values_weight,
                                    pa.options_values_price AS 'price',
                                    pa.price_prefix AS 'prefix'
                                FROM " . TABLE_PRODUCTS . " AS p
                                LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa ON (pa.products_id=p.products_id)
                                LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " AS pov ON (pov.products_options_values_id = pa.options_values_id AND pov.language_id={$this->languageId})
                                LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " AS po ON (po.products_options_id = pa.options_id AND po.language_id = {$this->languageId})
                                WHERE pov.products_options_values_name != 'TEXTFELD' 
                                AND po.products_options_id = {$attribute['options_id']} 
                                AND pov.products_options_values_id = {$attribute['options_values_id']} AND pa.products_attributes_id = {$attributeId};";

                        $attributeResult = xtc_db_query($attributeQuery);

                        if (!$attributeResult) {
                            throw new ShopgateLibraryException(
                                ShopgateLibraryException::PLUGIN_DATABASE_ERROR,
                                "Shopgate Plugin - Error checking for table \"" . TABLE_PRODUCTS . "\".", true
                            );
                        }


                        while ($attrRow = xtc_db_fetch_array($attributeResult)) {

                            $sgvariation = new ShopgateOrderItemOption();
                            $sgvariation->setName($attrRow["name"]);
                            $sgvariation->setValue($attrRow["value"]);
                            $sgvariation->setValueNumber($attrRow["value_number"]);
                            $sgvariation->setOptionNumber($attrRow["option_number"]);
                            $resultAttributes[] = $sgvariation;
                            $price += ($attrRow["prefix"] == "-") ? ($attrRow["price"] * (-1)) : $attrRow["price"];
                            $weight += ($attrRow["weight_prefix"] == "-") ? ($attrRow["options_values_weight"] * (-1))
                                : $attrRow["options_values_weight"];
                        }
                    }
                }
            }
        }
        if ($sgProduct instanceof ShopgateCartItem) {
            $sgProduct->setUnitAmount($sgProduct->getUnitAmount() + $price);
            $sgProduct->setUnitAmountWithTax($sgProduct->getUnitAmountWithTax() + ($price * (1 + ($tax_rate / 100))));
        }

        return $resultAttributes;
    }

    /**
     * read all option data to an product from database and
     * create an ShopgateOrderItemOption object with the data
     *
     * @param ShopgateOrderItem $product
     * @param                   $tax_rate
     *
     * @return array
     * @throws ShopgateLibraryException
     */
    public function getCartItemOptionsFromDb($product, $tax_rate)
    {


        $attributeQuery
            = "SELECT 
                                    po.products_options_id AS 'option_number',
                                    po.products_options_name AS 'name',
                                    pov.products_options_values_id AS 'value_number',
                                    pov.products_options_values_name AS 'value',
                                    pa.options_values_price AS 'price',
                                    pa.price_prefix AS 'prefix'
                                FROM " . TABLE_PRODUCTS . " AS p 
                                LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa ON (pa.products_id=p.products_id)
                                LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " AS pov ON (pov.products_options_values_id = pa.options_values_id AND pov.language_id={$this->languageId})
                                LEFT JOIN " . TABLE_PRODUCTS_OPTIONS
            . " AS po ON (po.products_options_id = pa.options_id AND po.language_id = {$this->languageId}) WHERE pov.products_options_values_name != 'TEXTFELD' ";

        $optionIds    = $this->getCartItemOptionIds($product);
        $attributeIds = $this->getCartItemAttributeIds($product);
        $pId          = $this->getProductsIdFromCartItem($product);
        if (count($optionIds) == 0 && count($attributeIds) == 0) {
            return array();
        }

        $optionQueryPart    =
            (count($optionIds) > 0) ? " AND (pa.products_id = {$pId} AND pa.options_values_id IN (" . implode(
                    ",", $optionIds
                ) . ")) " : "";
        $attributeQueryPart =
            (count($attributeIds) > 0) ? " AND (pa.products_id = {$pId} AND pa.products_attributes_id IN (" . implode(
                    ",", $attributeIds
                ) . ")) " : "";

        if (!empty($optionQueryPart) && !empty($attributeQueryPart)) {
            $attributeQuery .= $optionQueryPart . " OR " . $attributeQueryPart;
        } else {
            if (!empty($optionQueryPart)) {
                $attributeQuery .= $optionQueryPart;
            }
            if (!empty($attributeQueryPart)) {
                $attributeQuery .= $attributeQueryPart;
            }
        }

        $attributeResult = xtc_db_query($attributeQuery);

        if (!$attributeResult) {
            throw new ShopgateLibraryException(
                ShopgateLibraryException::PLUGIN_DATABASE_ERROR,
                "Shopgate Plugin - Error checking for table \"" . TABLE_PRODUCTS . "\".",
                true
            );
        }

        $resultAttributes = array();
        while ($attrRow = xtc_db_fetch_array($attributeResult)) {
            $sgvariation = new ShopgateOrderItemOption();
            $sgvariation->setName($attrRow["name"]);
            $sgvariation->setOptionNumber($attrRow["option_number"]);
            $sgvariation->setValue($attrRow["value"]);
            $sgvariation->setValueNumber($attrRow["value_number"]);
            if (!is_null($tax_rate)) {
                $attrRow["price"] *= 1 + ($tax_rate / 100);
            }
            $sgvariation->setAdditionalAmountWithTax(
                (($attrRow["prefix"] == "-") ? ($attrRow["price"] * (-1)) : $attrRow["price"])
            );
            $resultAttributes[] = $sgvariation;
        }

        return $resultAttributes;
    }

    /**
     * read all input field data to an product from database and
     * create an ShopgateOrderItemInput object with the data
     *
     * @param $productId
     * @param $languageId
     * @param $tax_rate
     *
     * @return array
     * @throws ShopgateLibraryException
     */
    public function getCartInputFields($productId, $languageId, $tax_rate)
    {
        $inputfieldQuery
                          = "
            SELECT
                po.products_options_id AS 'option_number',
                po.products_options_name AS 'name',
                pov.products_options_values_id AS 'value_number',
                pov.products_options_values_name AS 'value',
                pa.options_values_price AS 'price',
                pa.price_prefix AS 'prefix'
            FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa
            INNER JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON pa.options_id = po.products_options_id
            INNER JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON (pa.options_values_id = pov.products_options_values_id AND pov.language_id = $languageId)
            WHERE pa.products_id = '$productId'
                AND pov.products_options_values_name = 'TEXTFELD'
            ORDER BY po.products_options_id, pa.sortorder
        ";
        $inputfieldResult = xtc_db_query($inputfieldQuery);
        if (!$inputfieldResult) {
            throw new ShopgateLibraryException(
                ShopgateLibraryException::PLUGIN_DATABASE_ERROR,
                "Shopgate Plugin - Error checking for table \"" . TABLE_PRODUCTS . "\".",
                true
            );
        }

        $resultInputfields = array();
        while ($inputRow = xtc_db_fetch_array($inputfieldResult)) {
            $sgInputField = new ShopgateOrderItemInput();
            $sgInputField->setType('text');
            $sgInputField->setLabel($inputRow['name']);
            if (!is_null($tax_rate)) {
                $inputRow["price"] *= 1 + ($tax_rate / 100);
            }
            $sgInputField->setAdditionalAmountWithTax($inputRow["price"]);
            $sgInputField->setInfoText('');
            $resultInputfields[] = $sgInputField;
        }

        return $resultInputfields;
    }

    /**
     * calculate the weight to an product regarding the weight of options
     *
     * @param $products
     *
     * @return mixed
     */
    public function getProductsWeight($products)
    {
        $calculatedWeight = 0;
        foreach ($products as $product) {
            /**
             * @var $product ShopgateOrderItem
             */

            $weight       = 0;
            $optionIds    = $this->getCartItemOptionIds($product);
            $attributeIds = $this->getCartItemAttributeIds($product);
            $pId          = $this->getProductsIdFromCartItem($product);

            if (count($optionIds) != 0 || count($attributeIds) != 0) {
                // calculate the additional attribute/option  weight
                $query = "SELECT SUM(CONCAT(weight_prefix, options_values_weight)) AS weight FROM "
                    . TABLE_PRODUCTS_ATTRIBUTES . " AS pa WHERE ";

                $conditions = array();
                if (count($optionIds) > 0) {
                    $conditions[] =
                        " (pa.products_id = {$pId} AND pa.options_values_id IN (" . implode(",", $optionIds) . ")) ";
                }
                if (count($attributeIds) > 0) {
                    $conditions[] =
                        " (pa.products_id = {$pId} AND pa.products_attributes_id IN (" . implode(",", $attributeIds)
                        . ")) ";
                }

                $query .= implode(' OR ', $conditions);
                $fetchedQuery = xtc_db_query($query);
                $result = xtc_db_fetch_array($fetchedQuery);
                $weight += $result["weight"] * $product->getQuantity();
            }

            if (!empty($pId)) {
                // calculate the "base" product weight
                $query = xtc_db_query("select products_weight from " . TABLE_PRODUCTS . " AS p where p.products_id = {$pId}");
                $result = xtc_db_fetch_array($query);

                $weight += $result["products_weight"] * $product->getQuantity();
            }

            $calculatedWeight += $weight;
        }

        return $calculatedWeight;
    }

    /**
     * if the current order item (product) is a child product the item number was
     * generated in the schema <productId>_<attributeId>
     *
     * this function returns the shop system product id
     *
     * @param ShopgateOrderItem $sgOrderItem
     *
     * @return string
     */
    public function getProductIdFromCartItem(ShopgateOrderItem $sgOrderItem)
    {
        $parentId = $sgOrderItem->getParentItemNumber();
        if (empty($parentId)) {
            $id = $sgOrderItem->getItemNumber();
            if (strpos($id, "_") !== false) {
                $productIdArr = explode('_', $id);
                return $productIdArr[0];
            }
            return $id;
        }
        return $parentId;
    }

    /**
     * calculate the complete amount of all items in a specific shopping cart
     *
     * @param ShopgateCart $cart
     *
     * @return float|int
     */
    public function getCompleteAmount(ShopgateCart $cart)
    {
        $completeAmount = 0;
        foreach ($cart->getItems() as $item) {
            // It seems to happen that unit_amount_with_tax is not set in every case for method checkCart
            if ($item->getUnitAmountWithTax()) {
                $itemAmount = $item->getUnitAmountWithTax();
            } else if ($item->getTaxPercent() > 0) {
                $itemAmount = $item->getUnitAmount() * (1 + ($item->getTaxPercent()/100));
            } else {
                $itemAmount = $item->getUnitAmount();
            }

            $completeAmount += $itemAmount * $item->getQuantity();
        }

        return $completeAmount;
    }
}
