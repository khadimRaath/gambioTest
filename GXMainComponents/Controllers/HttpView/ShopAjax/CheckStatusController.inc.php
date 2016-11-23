<?php
/* --------------------------------------------------------------
   CheckStatusController.inc.php 2016-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CheckStatusController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class CheckStatusController extends HttpViewController
{
	/**
	 * @todo get rid of old AjaxHandler
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionDefault()
	{
		$ajaxHandler = MainFactory::create('PropertiesCombisAjaxHandler');
		
		$ajaxHandler->set_data('GET', array('action' => 'get_selection_template'));
		$ajaxHandler->set_data('POST', array(
			'properties_values_ids' => $this->_getQueryParameter('properties_values_ids'),
			'quantity'              => $this->_getQueryParameter('products_qty'),
			'products_id'           => $this->_getQueryParameter('products_id')
		));
		$ajaxHandler->proceed();
		$selectionTemplate = json_decode($ajaxHandler->get_response());
		
		$propertiesView = MainFactory::create_object('PropertiesView');
		$combiStatus    = $propertiesView->get_combis_status_json($this->_getQueryParameter('products_id'),
		                                                          $this->_getQueryParameter('properties_values_ids'),
		                                                          $this->_getQueryParameter('products_qty'));
		$combiStatus    = json_decode($combiStatus);
		
		/** @var ProductReadService $productReadService */
		$productReadService = StaticGXCoreLoader::getService('ProductRead');
		$quantityChecker    = '';
		$product            = $productReadService->getProductById(new IdType((int)$this->_getQueryParameter('products_id')));
		
		if($product->getSettings()->getPriceStatus() === 0)
		{
			$ajaxHandler = MainFactory::create('OrderAjaxHandler');
			$ajaxHandler->set_data('GET', array(
				'action' => 'quantity_checker',
				'qty'    => $this->_getQueryParameter('products_qty'),
				'id'     => $this->_getQueryParameter('products_id')
			));
			$ajaxHandler->proceed();
			$quantityChecker = $ajaxHandler->get_response();
		}
		
		$result = $this->_getPropertiesResponseArray($selectionTemplate, $combiStatus, $quantityChecker);
		
		if(!is_null($this->_getQueryParameter('id')))
		{
			$ajaxHandler = MainFactory::create('AttributesAjaxHandler');
			
			$_POST['properties_values_ids'] = $this->_getQueryParameter('properties_values_ids');
			$_POST['products_id']           = (int)$this->_getQueryParameter('products_id');
			
			$getArray = array(
				'action'       => 'calculate_price',
				'products_qty' => $this->_getQueryParameter('products_qty'),
				'products_id'  => $this->_getQueryParameter('products_id'),
				'id'           => $this->_getQueryParameter('id')
			);
			
			$ajaxHandler->set_data('GET', $getArray);
			$ajaxHandler->set_data('POST', $this->_getQueryParametersCollection()->getArray());
			$ajaxHandler->proceed();
			$result['content']['price']['value'] = $ajaxHandler->get_response();
			
			$result['attrImages'] = $this->_getAttributesImagesData();
			$result['content']['images'] = array(
				'selector' => 'attributeImages',
				'type'     => 'html',
				'value'    => $this->_getAttributesImagesHtml($ajaxHandler)
			);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $result);
	}
	
	
	/**
	 * @todo get rid of old AjaxHandler
	 * @todo use GET and POST REST-API like
	 *
	 * @return HttpControllerResponse
	 */
	public function actionAttributes()
	{
		$ajaxHandler = MainFactory::create('AttributesAjaxHandler');
		
		$weight          = $this->_getAttributesWeight($ajaxHandler);
		$price           = $this->_getAttributesPrice($ajaxHandler);
		$images          = array(
			'html'            => $this->_getAttributesImagesHtml($ajaxHandler),
			'attributes_data' => $this->_getAttributesImagesData()
		);
		$quantityChecker = $this->_getQuantityChecker();
		
		$result = $this->_getAttributesResponseArray($weight, $price, $images, $quantityChecker);
		
		return MainFactory::create('JsonHttpControllerResponse', $result);
	}
	
	
	/**
	 * @param mixed  $selectionTemplate
	 * @param mixed  $combiStatus
	 * @param string $p_quantityChecker
	 *
	 * @return array
	 */
	protected function _getPropertiesResponseArray($selectionTemplate, $combiStatus, $p_quantityChecker)
	{
		$discount = $this->_getDiscount();
		
		$result = array(
			'success'     => ($combiStatus->STATUS_CODE === 1 || $combiStatus->STATUS_CODE === 2)
			                 && ($selectionTemplate->status === 'stock_allowed'
			                     || $selectionTemplate->status === 'valid_quantity')
			                 && empty($p_quantityChecker),
			'status_code' => $combiStatus->STATUS_CODE,
			'content'     => array(
				'price'                  => array(
					'selector' => 'price',
					'type'     => 'html',
					'value'    => $selectionTemplate->price
				),
				'qty'                    => array(
					'selector' => 'quantity',
					'type'     => 'text',
					'value'    => $selectionTemplate->quantity
				),
				'shipping'               => array(
					'selector' => 'shippingTime',
					'type'     => 'text',
					'value'    => $selectionTemplate->shipping_status_name
				),
				'shippingIcon'           => array(
					'selector' => 'shippingTimeImage',
					'type'     => 'attribute',
					'key'      => 'src',
					'value'    => 'admin/html/assets/images/legacy/icons/' . $selectionTemplate->shipping_status_image
				),
				'weight'                 => array(
					'selector' => 'weight',
					'type'     => 'text',
					'value'    => $selectionTemplate->weight
				),
				'model'                  => array(
					'selector' => 'modelNumber',
					'type'     => 'html',
					'value'    => $selectionTemplate->model
				),
				'message'                => array(
					'selector' => 'messageCart',
					'type'     => 'html',
					'value'    => (!empty($selectionTemplate->message))
						? $selectionTemplate->message
						: $p_quantityChecker
				),
				'messageNoCombiSelected' => array(
					'selector' => 'messageCart',
					'type'     => 'html',
					'value'    => $combiStatus->STATUS_CODE === -1
						? $combiStatus->STATUS_TEXT
						: ''
				),
				'filter'                 => array(
					'selector' => 'propertiesForm',
					'type'     => 'replace',
					'value'    => $selectionTemplate->html
				),
				'ribbon'                 => array(
					'selector' => 'ribbonSpecial',
					'type'     => 'html',
					'value'    => $discount
				)
			)
		);
		
		if(!empty($selectionTemplate->message))
		{
			$result['content']['help'] = array(
				'selector' => 'messageHelp',
				'type'     => 'replace',
				'value'    => ''
			);
		}
		
		return $result;
	}
	
	
	/**
	 * @param string $p_weight
	 * @param string $p_price
	 * @param string $p_images
	 * @param string $p_quantityChecker
	 *
	 * @return array
	 */
	protected function _getAttributesResponseArray($p_weight, $p_price, $p_images, $p_quantityChecker)
	{
		$discount = $this->_getDiscount();
		
		$result = array(
			'success'     => empty($p_quantityChecker),
			'status_code' => 1,
			'attrImages'  => $p_images['attributes_data'],
			'content'     => array(
				'weight'  => array(
					'selector' => 'weight',
					'type'     => 'text',
					'value'    => $p_weight
				),
				'price'   => array(
					'selector' => 'price',
					'type'     => 'html',
					'value'    => $p_price
				),
				'images'  => array(
					'selector' => 'attributeImages',
					'type'     => 'html',
					'value'    => $p_images['html']
				),
				'message' => array(
					'selector' => 'messageCart',
					'type'     => 'html',
					'value'    => $p_quantityChecker
				),
				'ribbon'  => array(
					'selector' => 'ribbonSpecial',
					'type'     => 'html',
					'value'    => $discount
				)
			)
		);
		
		if(!empty($p_quantityChecker))
		{
			$result['content']['help'] = array(
				'selector' => 'messageHelp',
				'type'     => 'replace',
				'value'    => ''
			);
		}
		
		return $result;
	}
	
	
	/**
	 * @return string
	 */
	protected function _getDiscount()
	{
		require_once DIR_FS_INC . 'xtc_get_tax_class_id.inc.php';
		
		$combiPrice = 0;
		$discount   = '';
		$xtcPrice   = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		
		if(!is_null($this->_getQueryParameter('properties_values_ids')))
		{
			$propertiesControl = MainFactory::create_object('PropertiesControl');
			$combiId           = $propertiesControl->get_combis_id_by_value_ids_array(xtc_get_prid($this->_getQueryParameter('products_id')),
			                                                                          $this->_getQueryParameter('properties_values_ids'));
			$combiPrice        = $xtcPrice->get_properties_combi_price($combiId);
		}
		
		$specialPrice = $xtcPrice->xtcCheckSpecial($this->_getQueryParameter('products_id')) + $combiPrice;
		$normalPrice  = $xtcPrice->getPprice($this->_getQueryParameter('products_id')) + $combiPrice;
		
		if(is_array($this->_getQueryParameter('id')))
		{
			foreach($this->_getQueryParameter('id') as $optionId => $valueId)
			{
				$optionPrice = $xtcPrice->xtcGetOptionPrice($this->_getQueryParameter('products_id'), $optionId,
				                                            $valueId);
				$specialPrice += $optionPrice['price'];
				$normalPrice += $optionPrice['price'];
			}
		}
		
		$isSpecial = false;
		
		if($specialPrice < $normalPrice && $specialPrice > 0)
		{
			$discount  = ceil(round((1 - ($specialPrice / $normalPrice)) * -100, 1));
			$isSpecial = true;
		}
		
		if($isSpecial)
		{
			$discount = '<div class="ribbon-special"><span>' . $discount . '%</span></div>';
			
			return $discount;
		}
		
		return $discount;
	}
	
	
	protected function _getAttributesWeight(AttributesAjaxHandler $ajaxHandler)
	{
		$getArray = array(
			'action'       => 'calculate_weight',
			'products_qty' => $this->_getQueryParameter('products_qty'),
			'products_id'  => $this->_getQueryParameter('products_id')
		);
		
		if(!is_null($this->_getQueryParameter('id')))
		{
			$getArray['id'] = $this->_getQueryParameter('id');
		}
		
		$ajaxHandler->set_data('GET', $getArray);
		$ajaxHandler->set_data('POST', $this->_getQueryParametersCollection()->getArray());
		$ajaxHandler->proceed();
		
		$weight = $ajaxHandler->get_response();
		
		$ajaxHandler->v_output_buffer = null;
		
		return $weight;
	}
	
	
	protected function _getAttributesPrice(AttributesAjaxHandler $ajaxHandler)
	{
		$getArray = array(
			'action'       => 'calculate_price',
			'products_qty' => $this->_getQueryParameter('products_qty'),
			'products_id'  => $this->_getQueryParameter('products_id')
		);
		
		if(!is_null($this->_getQueryParameter('id')))
		{
			$getArray['id'] = $this->_getQueryParameter('id');
		}
		
		if(is_null($this->_getPostData('properties_values_ids')))
		{
			$propertiesControl = MainFactory::create_object('PropertiesControl');
			if((int)$propertiesControl->count_properties_to_product((int)$this->_getQueryParameter('products_id')) > 0)
			{
				$_POST['properties_values_ids'] = array();
				$_POST['products_id']           = (int)$this->_getQueryParameter('products_id');
			}
		}
		
		$ajaxHandler->set_data('GET', $getArray);
		$ajaxHandler->proceed();
		$price = $ajaxHandler->get_response();
		
		$ajaxHandler->v_output_buffer = null;
		
		return $price;
	}
	
	
	protected function _getAttributesImagesHtml(AttributesAjaxHandler $ajaxHandler)
	{
		$getArray = array(
			'action' => 'attribute_images'
		);
		
		if(!is_null($this->_getQueryParameter('id')))
		{
			$getArray['id'] = $this->_getQueryParameter('id');
		}
		
		$ajaxHandler->set_data('GET', $getArray);
		$ajaxHandler->proceed();
		
		return $ajaxHandler->get_response();
	}
	
	
	protected function _getAttributesImagesData()
	{
		$optionsIds = '';
		$valuesIds  = '';
		
		if(is_array($this->_getQueryParameter('id')))
		{
			foreach($this->_getQueryParameter('id') as $optionId => $valueId)
			{
				$optionsIds .= 'id[' . (int)$optionId . '],';
				$valuesIds .= (int)$valueId . ',';
			}
		}
		elseif(!is_null($this->_getQueryParameter('options_ids'))
		       && !is_null($this->_getQueryParameter('values_ids'))
		)
		{
			$optionsIds = $this->_getQueryParameter('options_ids');
			$valuesIds  = $this->_getQueryParameter('values_ids');
		}
		
		$attributes      = array();
		$optionsIdsArray = explode(',', substr($optionsIds, 0, -1));
		$valuesIdsArray  = explode(',', substr($valuesIds, 0, -1));
		$db              = StaticGXCoreLoader::getDatabaseQueryBuilder();
		
		foreach($optionsIdsArray as $key => $value)
		{
			$from             = strpos($value, '[');
			$productOptionsId = (int)substr($value, $from + 1, -1);
			
			$result = $db->select(array(
				                      'po.products_options_name',
				                      'pov.products_options_values_name',
				                      'pov.gm_filename'
			                      ))
			             ->from(array('products_options AS po', 'products_options_values AS pov'))
			             ->where(array(
				                     'po.products_options_id'         => $productOptionsId,
				                     'po.language_id'                 => $_SESSION['languages_id'],
				                     'pov.language_id'                => $_SESSION['languages_id'],
				                     'pov.products_options_values_id' => (int)$valuesIdsArray[$key]
			                     ))
			             ->limit(1)
			             ->get();
			if($result->num_rows() === 1)
			{
				$result = $result->row_array();
				if(!empty($result['gm_filename']))
				{
					$attributes[] = array(
						'src'   => DIR_WS_CATALOG . DIR_WS_IMAGES . 'product_images/attribute_images/'
						           . $result['gm_filename'],
						'title' => $result['products_options_name'] . ': ' . $result['products_options_values_name']
					);
				}
			}
		}
		
		return $attributes;
	}
	
	
	protected function _getQuantityChecker()
	{
		$productReadService = StaticGXCoreLoader::getService('ProductRead');
		$quantityChecker    = '';
		$product            = $productReadService->getProductById(new IdType((int)$this->_getQueryParameter('products_id')));
		
		if($product->getSettings()->getPriceStatus() === 0)
		{
			$ajaxHandler = MainFactory::create('OrderAjaxHandler');
			
			$getArray = array(
				'action' => 'quantity_checker',
				'qty'    => $this->_getQueryParameter('products_qty'),
				'id'     => $this->_getQueryParameter('products_id')
			);
			
			$ajaxHandler->set_data('GET', $getArray);
			$ajaxHandler->proceed();
			$quantityChecker = $ajaxHandler->get_response();
		}
		
		return $quantityChecker;
	}
}