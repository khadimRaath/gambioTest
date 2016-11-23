<?php
/* --------------------------------------------------------------
   SharedShoppingCartController.inc.php 2016-07-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class SharedShoppingCartController
 *
 * @category System
 * @package HttpViewControllers
 */
class SharedShoppingCartController extends HttpViewController
{
    /**
     * Adds products of a shared shopping cart to the current shopping cart of the customer .
     * 
     * @return JsonHttpControllerResponse
     */
    public function actionDefault()
    {
        $propertiesControl = MainFactory::create_object('PropertiesControl');
        $products = $this->_getSharedCart();

        foreach($products as $product)
        {
            $this->_addProductToCart($propertiesControl, $product);
        }

        return new RedirectHttpControllerResponse(xtc_href_link('shopping_cart.php'));
    }

    /**
     * Stores the current shopping cart content and creates a hash to address the shopping cart.
     * 
     * @return JsonHttpControllerResponse
     */
    public function actionStoreShoppingCart()
    {
        $products = $this->_getCart();
        $hash = $this->_storeShoppingCart($products);

        return new JsonHttpControllerResponse(array('success' => true, 'link' => xtc_href_link('shop.php', 'do=SharedShoppingCart&cart=' . $hash)));
    }

    /**
     * Extracts product information from the current shopping cart.
     * The returned array is structured as follows:
     *  array(
     *      productId => <int>,
     *      quantity => <int>,
     *      [combiId => <int>],
     *      [attributes => array(
     *          attributeId => <int>,
     *          attributeValueId => <int>
     *      )]
     *  )
     * 
     * @return array Am array of product information
     */
    protected function _getCart()
    {
        $propertiesControl = MainFactory::create_object('PropertiesControl');
        $cart = $_SESSION['cart']->get_products();
        $products = array();

        if($cart === false)
        {
            return array();
        }

        foreach($cart as $productData)
        {
            $uniqueProductId = $productData['id'];
            $productId = (int)xtc_get_prid($uniqueProductId);
            $combiId = $propertiesControl->extract_combis_id($uniqueProductId);

            if($combiId !== '' && $propertiesControl->combi_exists($productId, $combiId) === false)
            {
                continue;
            }

            $product = array();
            $product['productId'] = $productId;
            $product['quantity'] = (double)$productData['quantity'];
            if($combiId !== '')
            {
                $product['combiId'] = (int)$combiId;
            }
            if(is_array($productData['attributes']))
            {
                foreach($productData['attributes'] as $attributeId => $attributeValueId)
                {
                    $attribute = array(
                        'attributeId' => (int)$attributeId,
                        'attributeValueId' => (int)$attributeValueId
                    );
                    $product['attributes'][] = $attribute;
                }
            }

            $products[] = $product;
        }
        
        return $products;
    }

    /**
     * Stores the given product information as a shopping cart.
     * The expected array parameter has to be structured as follows:
     *  array(
     *      productId => <int>,
     *      quantity => <int>,
     *      [combiId => <int>],
     *      [attributes => array(
     *          attributeId => <int>,
     *          attributeValueId => <int>
     *      )]
     *  )
     * 
     * @param array $products The product information to store
     * @return string The hash of the stored shopping cart
     */
    protected function _storeShoppingCart(array $products)
    {
        $sharedShoppingCartService = StaticGXCoreLoader::getService('SharedShoppingCart');
        try
        {
            return $sharedShoppingCartService->storeShoppingCart($products, new IdType((int)$_SESSION['customer_id']));
        }
        catch(InvalidArgumentException $exception)
        {
            //TODO: handle exception properly
            return '';
        }
    }

    /**
     * Gathers shopping cart information from a stored cart in JSON format.
     * 
     * @return array|mixed The fetched cart information
     */
    protected function _getSharedCart()
    {
        $sharedShoppingCartService = StaticGXCoreLoader::getService('SharedShoppingCart');
        $hash = $this->_getQueryParameter('cart');
        if($hash === null)
        {
            return array();
        }
        
        try
        {
            return $sharedShoppingCartService->getShoppingCart(new StringType($hash));
        }
        catch(InvalidArgumentException $exception)
        {
            //TODO: handle exception properly
            return array();
        }
    }

    /**
     * Adds a product from given product information to the current shopping cart.
     * 
     * @param PropertiesControl $propertiesControl An instance of the PropertiesControl
     * @param stdClass $product Cart product information
     */
    protected function _addProductToCart(PropertiesControl $propertiesControl, stdClass $product)
    {
        $productId = $product->productId;
        $combiId = 0;
        if(isset($product->combiId))
        {
            $combiId = $product->combiId;

            if($propertiesControl->combi_exists($productId, $combiId) === false)
            {
                return;
            }
        }

        $attributes = '';
        if(isset($product->attributes))
        {
            $attributes = array();
            foreach($product->attributes as $attributeData)
            {
                $attributes[$attributeData->attributeId] = $attributeData->attributeValueId;
            }
        }

        $_SESSION['cart']->add_cart($productId, $product->quantity, $attributes, true, $combiId);
    }
}