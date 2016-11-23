<?php

/**
 * Class Actindo_Connector_Service_Products
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
class Actindo_Connector_Service_Products
{
    /**
     * count the number of products
     * @param string $params
     * @param null $categoryID
     * @param null $ordernumber
     * @return array
     */
    public function count($params,$categoryID=null, $ordernumber=null)
    {
        return export_products_count(array('params'=>$params,'categoryId'=>$categoryID,'orderNumber'=>$ordernumber));
    }
    /**
     * Method used for creating the update
     * @param string $params
     * @param struct $product Actindo Array
     * @return mixed
     * @throws Actindo_Connector_Model_Exception_Error
     */
    public function create_update($params,$product)
    {
        return import_product(array('params'=>$params,'product'=>$product));
    }

    /**
     * exports either the article list or all the details of one specific article
     * the 2nd param, $ordernumber, is actually the shops article id if article details are exported!
     *
     * @api
     * @param string $params
     * @param string $categoryID the category to export the list from
     * @param string $ordernumber the articles ordernumber if the list is requested, otherwise its the shops article id
     * @param string $language not supported
     * @param int $justList is 1 if an article listing is requested, otherwise its 0
     * @param int $offset only for list: offset to start with
     * @param int $limit only for list: limits the number of articles
     * @param struct $filters only for list: an array of filters
     * @return array
     */
    public function get($params,$categoryID=null, $ordernumber=null, $language=null, $justList=null, $offset=null, $limit=null, $filters=null)
    {
        $par = array(
            'params'=>$params,
            'categoryId'=>$categoryID,
            'orderNumber'=>$ordernumber,
            'language'=>$language,
            'justList'=>$justList,
            'offset'=>$offset,
            'limit'=>$limit,
            'filters'=>$filters
        );
        $result = null;
        if($justList>0)
        {
            $result = export_products_list($par);
        }
        else
        {
            $result = export_products($par);
        }
        return $result;
    }

    /**
     * delete a product by it's ordernumber
     * will return true if product is found and deleted
     * will return false if product is not found
     * @param string $params
     * @param string $ordernumber
     */
    public function delete($params,$ordernumber)
    {
        return import_delete_product(array('params'=>$params,'orderNumber'=>$ordernumber));
    }

    /**
     * update product stock
     * method is used for live stock update by actindo
     * returns ok=true on success (on multiple products, each successfull or not
     * successful article is also added to the output result
     * @param string $params
     * @param array $product
     * @return array
     */
    public function update_stock($params,$product) {
        return import_product_stock(array('params'=>$params,'product'=>$product));
    }
}
