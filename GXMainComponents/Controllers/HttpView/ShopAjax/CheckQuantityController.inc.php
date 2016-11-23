<?php
/* --------------------------------------------------------------
   CartDropdownController.inc.php 2016-05-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CheckQuantityController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class CheckQuantityController extends HttpViewController
{
	/**
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionDefault()
	{
		$quantity                     = str_replace(',', '.', $this->_getPostData('cart_quantity'));
		$quantity                     = new DecimalType((float)$quantity);
		$quantityErrorMessage         = $this->_getQuantityErrorMessage(new IdType((int)$this->_getPostData('products_id')),
		                                                                $quantity);
		$combinationStatusInformation = array('STATUS_CODE' => 0, 'STATUS_TEXT' => '');
		if(!is_null($this->_getPostData('combination')))
		{
			$combinationStatusInformation = $this->_getCombinationStatusInformation(new IdType($this->_getPostData('combination')),
			                                                                        $quantity);
		}
		
		$result = $this->_getResponseArray($quantityErrorMessage, $combinationStatusInformation);
		
		return MainFactory::create('JsonHttpControllerResponse', $result);
	}
	
	
	/**
	 * @todo use decimal point from current currency instead of , for all currencies
	 *
	 * @param IdType $productId
	 *
	 * @return string
	 */
	protected function _getQuantityErrorMessage(IdType $productId, DecimalType $quantity)
	{
		$errorMessage        = '';
		$languageTextManager = MainFactory::create('LanguageTextManager', 'general');
		
		/** @var ProductReadService $productReadService */
		$productReadService = StaticGXCoreLoader::getService('ProductRead');
		$product            = $productReadService->getProductById($productId);
		
		$minOrder          = $product->getSettings()->getMinOrder();
		$graduatedQuantity = $product->getSettings()->getGraduatedQuantity();
		
		if($quantity->asDecimal() < $minOrder)
		{
			$errorMessage .= $languageTextManager->get_text('GM_ORDER_QUANTITY_CHECKER_MIN_ERROR_1')
			                 . str_replace('.', ',', (string)$minOrder)
			                 . $languageTextManager->get_text('GM_ORDER_QUANTITY_CHECKER_MIN_ERROR_2');
		}
		
		if($quantity->asDecimal() > MAX_PRODUCTS_QTY)
		{
			$errorMessage .= $languageTextManager->get_text('GM_ORDER_QUANTITY_CHECKER_MAX_ERROR_1')
			                 . (string)MAX_PRODUCTS_QTY
			                 . $languageTextManager->get_text('GM_ORDER_QUANTITY_CHECKER_MAX_ERROR_2');
		}
		
		$graduatedQuantityCheckResult = $quantity->asDecimal() / $graduatedQuantity;
		
		// workaround for next if-case to avoid calculating failure
		$graduatedQuantityCheckResult = round($graduatedQuantityCheckResult, 4);
		if((int)$graduatedQuantityCheckResult != $graduatedQuantityCheckResult)
		{
			$errorMessage .= $languageTextManager->get_text('GM_ORDER_QUANTITY_CHECKER_GRADUATED_ERROR_1')
			                 . str_replace('.', ',', (string)$graduatedQuantity)
			                 . $languageTextManager->get_text('GM_ORDER_QUANTITY_CHECKER_GRADUATED_ERROR_2');
		}
		
		return $errorMessage;
	}
	
	
	/**
	 * @param IdType      $combiId
	 * @param DecimalType $quantity
	 *
	 * @return array
	 */
	protected function _getCombinationStatusInformation(IdType $combiId, DecimalType $quantity)
	{
		$propertiesView = MainFactory::create_object('PropertiesView');
		$statusInfo     = $propertiesView->get_combis_status_by_combis_id_json($combiId->asInt(),
		                                                                       $quantity->asDecimal());
		$statusInfo     = json_decode($statusInfo, true);
		
		return $statusInfo;
	}
	
	
	/**
	 * @param string $errorMessage
	 * @param array  $combinationStatusInformation
	 *
	 * @return array
	 */
	protected function _getResponseArray($errorMessage, array $combinationStatusInformation)
	{
		$result = array(
			'success'     => $errorMessage === '',
			'status_code' => $combinationStatusInformation['STATUS_CODE'],
			'combination' => array(
				'message' => array(
					'selector' => 'errorMsg',
					'type'     => 'html',
					'value'    => $combinationStatusInformation['STATUS_TEXT']
				)
			),
			'quantity'    => array(
				'message' => array(
					'selector' => 'errorMsg',
					'type'     => 'html',
					'value'    => $errorMessage
				)
			)
		);
		
		return $result;
	}
}
