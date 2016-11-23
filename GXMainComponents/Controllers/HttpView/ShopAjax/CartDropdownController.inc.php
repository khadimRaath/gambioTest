<?php
/* --------------------------------------------------------------
   CartDropdownController.inc.php 2016-10-27
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CartDropdownController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class CartDropdownController extends HttpViewController
{
	/** @var shoppingCart_ORIGIN */
	protected $shoppingCart;
	
	/** @var xtcPrice_ORIGIN */
	protected $xtcPrice;
	
	/** @var int */
	protected $customersStatusOtDiscountFlag;
	
	/** @var float */
	protected $customersStatusOtDiscount;
	
	/** @var int */
	protected $customersStatusShowPriceTax;
	
	/** @var int */
	protected $customersStatusAddTaxOt;
	
	/** @var int */
	protected $customersStatusShowPrice;
	
	
	/**
	 * @param HttpContextReaderInterface     $httpContextReader
	 * @param HttpResponseProcessorInterface $httpResponseProcessor
	 * @param ContentViewInterface           $defaultContentView
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $defaultContentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $defaultContentView);
		
		$this->shoppingCart = $_SESSION['cart'];
		
		$this->xtcPrice = $GLOBALS['xtPrice'];
		
		$this->customersStatusOtDiscountFlag = (int)(boolean)$_SESSION['customers_status']['customers_status_ot_discount_flag'];
		$this->customersStatusOtDiscount     = (float)$_SESSION['customers_status']['customers_status_ot_discount'];
		$this->customersStatusShowPriceTax   = (int)(boolean)$_SESSION['customers_status']['customers_status_show_price_tax'];
		$this->customersStatusAddTaxOt       = (int)(boolean)$_SESSION['customers_status']['customers_status_add_tax_ot'];
		$this->customersStatusShowPrice      = (int)(boolean)$_SESSION['customers_status']['customers_status_show_price'];
	}
	
	
	/**
	 * @todo get rid of old AjaxHandler
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionDefault()
	{
		$cartSum      = trim($this->xtcPrice->xtcFormat($this->_getCartTotal(), true));
		$productCount   = $this->shoppingCart->count_products();
		$cartDropdown = $this->_getCartDropdown();
		
		$result = $this->_getResponseArray(new StringType($cartSum), new DecimalType($productCount),
		                                   new StringType($cartDropdown));
		
		return MainFactory::create('JsonHttpControllerResponse', $result);
	}
	
	
	/**
	 * @return float
	 */
	protected function _getCartTotal()
	{
		$total    = $this->shoppingCart->show_total();
		$discount = 0.0;
		
		if($this->customersStatusOtDiscountFlag === 1 && $this->customersStatusOtDiscount !== 0)
		{
			if($this->customersStatusShowPriceTax === 0 && $this->customersStatusAddTaxOt === 1)
			{
				$price = $total - $this->shoppingCart->show_tax(false);
			}
			else
			{
				$price = $total;
			}
			$discount = $this->xtcPrice->xtcGetDC($price, $this->customersStatusOtDiscount);
		}
		
		if($this->customersStatusShowPrice === 1)
		{
			if($this->customersStatusShowPriceTax === 0 && $this->customersStatusAddTaxOt === 0)
			{
				$total -= $discount;
			}
			if($this->customersStatusShowPriceTax === 0 && $this->customersStatusAddTaxOt === 1)
			{
				$total = $total - $this->shoppingCart->show_tax(false) - $discount;
			}
			if($this->customersStatusShowPriceTax === 1)
			{
				$total -= $discount;
			}
		}
		
		return (float)$total;
	}
	
	
	/**
	 * @return mixed|string
	 */
	protected function _getCartDropdown()
	{
		$cartDropdownContentView = MainFactory::create_object('ShoppingCartDropdownBoxContentView');
		$cartDropdownContentView->set_('coo_cart', $this->shoppingCart);
		$cartDropdownContentView->set_('language_id', $_SESSION['languages_id']);
		$cartDropdownContentView->set_('language_code', $_SESSION['language_code']);
		$cartDropdownContentView->set_('customers_status_ot_discount_flag', $this->customersStatusOtDiscountFlag);
		$cartDropdownContentView->set_('customers_status_ot_discount', $this->customersStatusOtDiscount);
		$cartDropdownContentView->set_('customers_status_show_price_tax', $this->customersStatusShowPriceTax);
		$cartDropdownContentView->set_('customers_status_add_tax_ot', $this->customersStatusAddTaxOt);
		$cartDropdownContentView->set_('customers_status_show_price', $this->customersStatusShowPrice);
		$cartDropdownContentView->set_('customers_status_payment_unallowed',
		                               $_SESSION['customers_status']['customers_status_payment_unallowed']);
		
		return $cartDropdownContentView->get_html();
	}
	
	
	protected function _getResponseArray(StringType $cartSum, DecimalType $productsCount, StringType $cartDropdown)
	{
		$result = array(
			'success' => true,
			'content' => array(
				'price'    => array(
					'selector' => 'cartDropdownProducts',
					'type'     => 'text',
					'value'    => $cartSum->asString()
				),
				'count'    => array(
					'selector' => 'cartDropdownProductsCount',
					'type'     => 'text',
					'value'    => $productsCount->asDecimal()
				),
				'dropdown' => array(
					'selector' => 'cartDropdown',
					'type'     => 'replace',
					'value'    => $cartDropdown->asString()
				),
			)
		);
		
		return $result;
	}
}