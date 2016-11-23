<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2012-09-06 13:49:09 +0200 (Thu, 06 Sep 2012) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofortOrderSynchronisation.php 5326 2012-09-06 11:49:09Z boehm $
 */

require_once(DIR_FS_CATALOG."callback/sofort/ressources/scripts/sofortOrderShopTools.php");
require_once(DIR_FS_CATALOG."callback/sofort/helperFunctions.php");

class sofortOrderSynchronisation {

	/**
	 * if cart is edited at shop, send new data to SOFORT
	 * @param string $transactionId
	 * @param array	 $articles
	 * @param string $comment
	 */
	public function editArticlesSofort($transactionId, $articles, $comment = '') {
		$PnagInvoice = new PnagInvoice(shopGetConfigKey(), $transactionId);
		$sofortArticles = array();
		$orderId = '';

		$taxLow = 0;
		$taxHigh = 0;
		$subtotal = 0;
		$shipping = 0;
		$discount = array();
		$agio = array();

		foreach($articles as $article) {
			if ($orderId == '') {
				$orderId = $article['articleOrdersId'];
			}

			if($article['articleQuantity'] != 'delete'){
				array_push($sofortArticles, array(
					'itemId'		=> substr($article['articleId'],0,31),
					'productNumber' => $article['articleNumber'],
					'productType'	=> $article['articleType'],
					'title'			=> HelperFunctions::convertEncoding($article['articleTitle'],3),
					'description'	=> HelperFunctions::convertEncoding($article['articleDescription'],3),
					'quantity'		=> $article['articleQuantity'],
					'unitPrice'		=> number_format($article['articlePrice'], 2, '.', ''),
					'tax'			=> number_format($article['articleTax'], 2, '.', ''))
				);
			}

			switch($article['articleClass']){
				case 'shipping': 	$shipping = $article['articlePrice'];
									break;
				case 'discount':	$splitItemId = explode('|', $article['articleId']);
									$discountClass = $splitItemId[1];
									array_push($discount, array(
											'class' => $discountClass,
											'value' => $article['articlePrice']));
									break;
				case 'agio':		$splitItemId = explode('|', $article['articleId']);
									$agioClass = $splitItemId[1];
									array_push($agio , array(
											'class' => $agioClass,
											'value' => $article['articlePrice']));
									break;
				case 'product':		$subtotal += ($article['articleQuantity'] * $article['articlePrice']);
									break;
			}

			switch ($article['articleTax']){
				case 7:		$taxLow  += ($article['articleQuantity']*$article['articlePrice']);
							break;
				case 19:	$taxHigh += ($article['articleQuantity']*$article['articlePrice']);
							break;
			}
		}

		$lastShopTotal = HelperFunctions::getLastFieldValueFromSofortTable($orderId, 'amount', true);
		$time = date("d.m.Y, G:i:s");

		$errors = $PnagInvoice->updateInvoice($transactionId, $sofortArticles, $comment);
		$warnings = $PnagInvoice->getWarnings();

		if($errors){
			return array(
					'errors'   => $errors,
					'warnings' => $warnings
			);
		}

		$PnagInvoice->refreshTransactionData();

		$orderStatus = shopDbQuery('SELECT orders_status FROM '.TABLE_ORDERS.' WHERE orders_id = "'.(int)$orderId.'"');
		$orderStatus = shopDbFetchArray($orderStatus);

		$invoiceArticles = $PnagInvoice->getItems();

		if(is_array($invoiceArticles)){
			$this->_insertNewTotalCommentToHistory($orderId, $orderStatus['orders_status'], $time, $PnagInvoice, $lastShopTotal);
			$this->editArticlesShop($PnagInvoice, $orderId);
		} else {
			return array(
					'errors'   => 'Undefined Error (probably unable to connect to the SOFORT-Server)'
			);
		}

		return false;
	}


	/**
	 * cart was edited at SOFORT-backend, apply changes in shop
	 * @param PnagInvoice $PnagInvoice
	 */
	public function editArticlesShop(PnagInvoice $PnagInvoice, $orderNumber) {
		$lng = $PnagInvoice->getLanguageCode();
		$newAmount = $PnagInvoice->getAmount();
		$invoiceArticles = $PnagInvoice->getItems();
		$sofortIdArray = array();

		foreach ($invoiceArticles as $article) {
			$getTotalItems = explode('|', $article->itemId);

			//discount/agio/shipping/total-modules
			if(count($getTotalItems) > '1'){
				if($getTotalItems[0]=='discount' || $getTotalItems[0]=='agio'){
					$sofortIdArray[$article->itemId] = $article->itemId;
					$sofortArticleArray[$article->itemId] = $article;
				} else {
					$sofortIdArray[$getTotalItems[0]] = $getTotalItems[0];
					$sofortArticleArray[$getTotalItems[0]] = $article;
				}
			//normal product
			} else {
				$ordersProductsId = $this->_getOrderProductsId($article->itemId, $orderNumber);
				if (!$ordersProductsId) {

					if (!HelperFunctions::textExistsInOrderHistory(MODULE_PAYMENT_SOFORT_SR_SYNC_FAILED_SELLER, $orderNumber)) {
						$comment = MODULE_PAYMENT_SOFORT_SR_SYNC_FAILED_SELLER;

						if (HelperFunctions::isGambio() && HelperFunctions::orderHasGxCustomizerArticles($orderNumber)) {
							$comment .= ' '.MODULE_PAYMENT_SOFORT_MULTIPAY_GX_CUSTOMIZER_AFFECTED;
						}

						HelperFunctions::insertHistoryEntry($orderNumber, -1, strtoupper($comment));
					}

					$PnagInvoice->logError("Critical Error! At least one OrdersProductsId for orderID '$orderNumber' was not found in shop. Article with
						item-ID '".$article->itemId."' does not exist in shop. Syncronisation will be aborted. Compare this order and prices with the order
						at SOFORT for differences. Check also comments in order-history and status of this order in Shop. Data at SOFORT are normally
						not affected and correct. Affected invoice-article: ".print_r($article, true));
					return false; //prevent sync
				}
				$sofortIdArray[$ordersProductsId] = $ordersProductsId;
				$sofortArticleArray[$ordersProductsId] = $article;
			}
		}

		$shopProductsQuery = shopDbQuery("SELECT orders_products_id FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id = '".(int)$orderNumber."'");

		while ($shopProductsResult = shopDbFetchArray($shopProductsQuery)) {
			$shopArticleArray[] = $shopProductsResult['orders_products_id'];
		}

		$taxLow = 0;
		$taxHigh = 0;
		$subtotal = 0;

		foreach ($shopArticleArray as $shopArticle){
			if (!in_array($shopArticle,$sofortIdArray)){
				$this->_sofortRestock($this->_getItemId($shopArticle, $orderNumber), $orderNumber, 0);
				$this->_deleteShopOrderArticle($shopArticle, $PnagInvoice->getStatusReason());
			} else {
				$qty = $sofortArticleArray[$shopArticle]->quantity;
				$price = $sofortArticleArray[$shopArticle]->unitPrice;
				$itemId = $sofortArticleArray[$shopArticle]->itemId;

				$this->_sofortRestock($itemId, $orderNumber, $qty);
				$this->_updateShopOrderArticle($shopArticle, $qty, $price, $PnagInvoice->getStatusReason());

				if ($sofortArticleArray[$shopArticle]->tax == '7.00'){
					$taxLow  += ($sofortArticleArray[$shopArticle]->quantity * $sofortArticleArray[$shopArticle]->unitPrice);
				} elseif ($sofortArticleArray[$shopArticle]->tax == '19.00'){
					$taxHigh += ($sofortArticleArray[$shopArticle]->quantity * $sofortArticleArray[$shopArticle]->unitPrice);
				}

				$subtotal += ($sofortArticleArray[$shopArticle]->quantity * $sofortArticleArray[$shopArticle]->unitPrice);
			}
		}

		$shipping = 0;
		$discount = array();
		$agio = array();

		foreach ($sofortIdArray as $sofortId){
			if (!in_array($sofortId, $shopArticleArray)){
				$splitItemId = explode('|', $sofortId);

				switch($splitItemId[0]){
					case 'shipping': 	$shipping = $sofortArticleArray[$sofortId]->unitPrice;
										break;
					case 'discount':	$discountClass = $splitItemId[1];
										array_push($discount, array(
												'class' => $discountClass,
												'value' => $sofortArticleArray[$sofortId]->unitPrice
												)
										);
										break;
					case 'agio':		$agioClass = $splitItemId[1];
										array_push($agio , array(
												'class' => $agioClass,
												'value' => $sofortArticleArray[$sofortId]->unitPrice
												)
										);
										break;
					default:			$this->_sofortRestock($sofortArticleArray[$sofortId]->itemId, $orderNumber, $sofortArticleArray[$sofortId]->quantity);
										$this->_insertShopOrderArticle($sofortArticleArray[$sofortId], $orderNumber, $lng);
										$subtotal += ($sofortArticleArray[$sofortId]->quantity * $sofortArticleArray[$sofortId]->unitPrice);
										break;
				}

				if ($sofortArticleArray[$sofortId]->tax == '7.00'){
					$taxLow += ($sofortArticleArray[$sofortId]->unitPrice*$sofortArticleArray[$sofortId]->quantity);
				} elseif ($sofortArticleArray[$sofortId]->tax == '19.00'){
					$taxHigh += ($sofortArticleArray[$sofortId]->unitPrice*$sofortArticleArray[$sofortId]->quantity);
				}
			}
		}

		$status = $PnagInvoice->getStatusReason();
		$this->_updateShopTotals($taxLow, $taxHigh, $subtotal, $newAmount, $orderNumber, $shipping, $discount, $agio, $status);
	}


	/**
	 * get SOFORT-ItemID from table sofort_products
	 * @param int $ordersProductsId
	 * @param int $ordersId
	 */
	protected function _getItemId ($ordersProductsId, $ordersId) {
		$query = "SELECT item_id FROM sofort_products WHERE orders_products_id = ".HelperFunctions::escapeSql($ordersProductsId);
		$product = shopDbCheckAndFetchOne($query);
		return $product['item_id'];
	}


	/**
	 * get latest item-quantity
	 * @param int $itemId
	 * @param int $ordersId
	 */
	protected function _getLatestQuantity($itemId, $ordersId){
		$orderProductsId = $this->_getOrderProductsId($itemId, $ordersId);

		if (!$orderProductsId) return false;

		$qry = "SELECT products_quantity FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_products_id = '".HelperFunctions::escapeSql($orderProductsId)."'";
		$res = shopDbCheckAndFetchOne($qry);
		return $res['products_quantity'];
	}


	/**
	 * get number of different products
	 * @param int $ordersId
	 */
	protected function _getNumberOfOrderProducts ($ordersId) {
		$query = "SELECT order_products_id FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_id = ".(int)$ordersId;
		$number = shopDbGetNumRows($query);
		return $number;
	}


	/**
	 * get orders_products_id from table sofort_products
	 * @param int $itemId
	 * @param int $ordersId
	 */
	protected function _getOrderProductsId ($itemId, $ordersId) {
		$query = shopDbQuery("SELECT orders_products_id FROM sofort_products WHERE item_id = '".HelperFunctions::escapeSql($itemId)."' AND orders_id = '".(int)$ordersId."'");
		if (shopDbNumRows($query) != 1) {
			return false; //GX-Customizer-Bug may occur in more than one entry!
		}
		$product = shopDbFetchArray($query);
		return $product['orders_products_id'];
	}


	/**
	 * update quantity and price in shop order table
	 * @param int	 $ordersProductsId
	 * @param mixed	 $quantity
	 * @param float	 $unitPrice
	 * @param string $status
	 */
	protected function _updateShopOrderArticle($ordersProductsId, $quantity, $unitPrice, $status){
		if ($quantity == 'delete'){
			// article was marked for removal which was already handled in editArticlesSofort()
		} elseif ($quantity == 0){
			$this->_deleteShopOrderArticle($ordersProductsId, $status);
		} else {
			$finalPrice = $quantity * $unitPrice;
			$query = "UPDATE ".TABLE_ORDERS_PRODUCTS." SET products_quantity = '".HelperFunctions::escapeSql($quantity)."', products_price = '".HelperFunctions::escapeSql($unitPrice)."', final_price = '".HelperFunctions::escapeSql($finalPrice)."' WHERE orders_products_id ='".HelperFunctions::escapeSql($ordersProductsId)."'";
			shopDbQuery($query);
		}
	}


	/**
	 * update shop totals
	 * @param float	 $taxLow
	 * @param float	 $taxHigh
	 * @param float	 $subtotal
	 * @param float	 $newAmount
	 * @param int	 $ordersId
	 * @param array	 $shipping
	 * @param array	 $discount
	 * @param array	 $agio
	 * @param string $status
	 * @param string $currency
	 */
	protected function _updateShopTotals($taxLow, $taxHigh, $subtotal, $newAmount, $ordersId, $shipping, $discount, $agio, $status, $currency = 'EUR'){
		if ($status == 'canceled' || $status == 'confirmation_period_expired' || $status == 'refunded'){
			// in case of cancellation keep old data for replicability
			return;
		}

		shopDbQuery('UPDATE '.TABLE_ORDERS_TOTAL.' SET value = "'.HelperFunctions::escapeSql($newAmount).'", text = "<b>'.HelperFunctions::escapeSql(number_format($newAmount,2,",",".")).' '.HelperFunctions::escapeSql($currency).'</b>" WHERE orders_id = "'.(int)$ordersId.'" AND class = "ot_total"');
		shopDbQuery('UPDATE '.TABLE_ORDERS_TOTAL.' SET value = "'.HelperFunctions::escapeSql($subtotal).'" , text = "'.HelperFunctions::escapeSql(number_format($subtotal,2,",",".")).' '.HelperFunctions::escapeSql($currency).'" WHERE orders_id = "'.(int)$ordersId.'" AND class = "ot_subtotal"');

		$shippingText = number_format($shipping ,2,",",".").' '.$currency;
		$this->_checkAndUpdateTotal($ordersId, 'ot_shipping', 'Versandkosten:', $shippingText, $shipping, constant('MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER'));

		$taxLowValue = 0;
		if ($taxLow  != 0) $taxLowValue  = (($taxLow /107)* 7);
		$taxLowText = number_format($taxLowValue ,2,",",".").' '.$currency;
		$this->_checkAndUpdateTotal($ordersId, 'ot_tax', 'zzgl. 7% MwSt.:', $taxLowText, $taxLowValue, constant('MODULE_ORDER_TOTAL_TAX_SORT_ORDER'), ' AND title LIKE  "%7%"');

		$taxHighValue = 0;
		if ($taxHigh != 0) $taxHighValue = (($taxHigh/119)*19);
		$taxHighText = number_format($taxHighValue ,2,",",".").' '.$currency;
		$this->_checkAndUpdateTotal($ordersId, 'ot_tax', 'zzgl. 19% MwSt.:', $taxHighText, $taxHighValue, constant('MODULE_ORDER_TOTAL_TAX_SORT_ORDER'), ' AND title LIKE  "%19%"');

		$nettoValue = $newAmount - $taxLowValue - $taxHighValue;
		$nettoText = number_format($nettoValue ,2,",",".").' '.$currency;
		$this->_checkAndUpdateNetto($ordersId, $nettoValue, $newAmount, $nettoText);
		$this->_deleteDeletedDiscountsAndAgios($ordersId, array_merge($discount, $agio));

		if(count($discount)!=0){
			foreach ($discount as $position){
				$text = number_format($position['value'],2,',','.')." ".$currency;
				$this->_checkAndUpdateTotal($ordersId, $position['class'], 'Rabatt:', $text, $position['value'], constant('MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER'));
			}
		}

		if(count($agio)!=0){
			foreach ($agio as $position){
				$text = number_format($position['value'],2,",",".").' '.$currency;
				$this->_checkAndUpdateTotal($ordersId, $position['class'], 'Zuschlag:', $text, $position['value'], constant('MODULE_ORDER_TOTAL_DISCOUNT_SORT_ORDER'));
			}
		}
	}


	/**
	 * if discounts/agios have been deleted at SOFORT, this function will delete them in the shop
	 * unknown discount-/agio-modules will not be supported by this function!
	 * @param int $ordersId
	 * @param array $discountsAndAgios - all current discounts/agios of this order
	 */
	protected function _deleteDeletedDiscountsAndAgios($ordersId, $discountsAndAgios) {
		$allCurrentInCart = array();
		$allCurrentInShop = array();

		foreach ($discountsAndAgios as $oneEntry) {
			$allCurrentInCart[] = $oneEntry['class'];
		}

		$supportedModules = array('ot_sofort', 'ot_discount', 'ot_gv', 'ot_coupon', 'ot_loworderfee');
		$query = shopDbQuery("SELECT orders_total_id, class FROM ".TABLE_ORDERS_TOTAL." WHERE orders_id = '".(int)$ordersId."' AND class IN ('".implode("','", $supportedModules)."')");

		while ($oneEntry = shopDbFetchArray($query)) {
			$allCurrentInShop[$oneEntry['orders_total_id']] = $oneEntry['class'];
		}

		foreach ($allCurrentInShop as $ordersTotalId => $class) {
			if (!in_array($class, $allCurrentInCart) && in_array($class, $supportedModules)) {
				shopDbQuery('DELETE FROM '.TABLE_ORDERS_TOTAL.' WHERE orders_id = "'.(int)$ordersId.'" AND orders_total_id = '.(int)$ordersTotalId.' AND class = "'.HelperFunctions::escapeSql($class).'"');
			}
		}
	}


	/**
	 * Checks if total exists; updates or deletes if true; inserts if false
	 * @param int	 $ordersId
	 * @param string $class
	 * @param string $title
	 * @param string $text
	 * @param float	 $value
	 * @param string $addCond - has to be already escaped!
	 */
	protected function _checkAndUpdateTotal($ordersId, $class, $title, $text, $value, $sortOrder, $addCond = ''){
		if(shopDbNumRows(shopDbQuery('SELECT * FROM '.TABLE_ORDERS_TOTAL.' WHERE orders_id = "'.(int)$ordersId.'" AND class = "'.HelperFunctions::escapeSql($class).'"'.$addCond)) != 0) {
			if ($value != 0 || $class == 'ot_shipping'){
				shopDbQuery('UPDATE '.TABLE_ORDERS_TOTAL.' SET value = "'.HelperFunctions::escapeSql($value).'" , text = "'.HelperFunctions::escapeSql($text).'" WHERE orders_id = "'.(int)$ordersId.'" AND class = "'.HelperFunctions::escapeSql($class).'"'.$addCond);
			} else {
				shopDbQuery('DELETE FROM '.TABLE_ORDERS_TOTAL.' WHERE orders_id = "'.(int)$ordersId.'" AND class = "'.HelperFunctions::escapeSql($class).'"'.$addCond);
			}
		} else {
			if ($value != 0 || $class == 'ot_shipping'){
				$data = array(
						'value'		=> $value,
						'text'		=> $text,
						'orders_id' => $ordersId,
						'class'		=> $class,
						'title'		=> $title,
						'sort_order'=> $sortOrder
				);
				shopDbPerform(TABLE_ORDERS_TOTAL, $data);
			}
		}
	}

	/**
	 * Checks if netto-string exists; updates if true; inserts if false and bruttoAmount >= 150
	 * @param int $ordersId
	 * @param float $nettoAmount
	 * @param float $bruttoAmount
	 * @param string $text
	 */
	private function _checkAndUpdateNetto($ordersId, $nettoAmount, $bruttoAmount, $text) {
		if (shopDbNumRows(shopDbQuery('SELECT * FROM '.TABLE_ORDERS_TOTAL.' WHERE class = "ot_subtotal_no_tax" AND orders_id = "'.(int)$ordersId.'"'))) {
			shopDbQuery('UPDATE '.TABLE_ORDERS_TOTAL.' SET value = "'.HelperFunctions::escapeSql($nettoAmount).'" , text = "'.HelperFunctions::escapeSql($text).'" WHERE orders_id = "'.(int)$ordersId.'" AND class = "ot_total_netto"');
		} else if ($bruttoAmount >= 150) {
			$data = array(
						'value'		=> $nettoAmount,
						'text'		=> $text,
						'orders_id' => $ordersId,
						'class'		=> 'ot_subtotal_no_tax',
						'title'		=> 'Summe (netto):',
						'sort_order'=> constant('MODULE_ORDER_TOTAL_SUBTOTAL_NO_TAX_SORT_ORDER')
				);
			shopDbPerform(TABLE_ORDERS_TOTAL, $data);
		}
	}


	/**
	 * delete article from shop order
	 * @param int	 $ordersProductsId
	 * @param string $status
	 */
	protected function _deleteShopOrderArticle($ordersProductsId, $status){
		if ($status == 'canceled' || $status == 'refunded' || $status == 'confirmation_period_expired'){
			// in case of cancellation keep old data for replicability
		} else {
			$query = "DELETE FROM ".TABLE_ORDERS_PRODUCTS." WHERE orders_products_id = '".$ordersProductsId."'";
			shopDbQuery($query);
			$query = "DELETE FROM ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." WHERE orders_products_id = '".$ordersProductsId."'";
			shopDbQuery($query);
		}
	}


	/**
	 * insert article in shop order (e.g. during an undo operation)
	 * @param object $sofortItem
	 * @param int	 $ordersId
	 * @param string $lng
	 */
	protected function _insertShopOrderArticle($sofortItem, $ordersId, $lng){
		$itemId = $sofortItem->itemId;

		$splitItemId = explode('{',$itemId);
		$productId = $splitItemId[0];

		if(count($splitItemId) == '1'){
			$hasAttributes = false;
		} else {
			$hasAttributes = true;

			for ($i=1;$i<count($splitItemId);++$i){
				$attrId = explode('}',$splitItemId[$i]);
				$attributes[] = array(
						'optionsId'		  => $attrId[0],
						'optionsValuesId' => $attrId[1]
				);
			}
		}

		$data = array(
				'orders_id'			=> $ordersId,
				'products_id'		=> $productId,
				'products_model'	=> $sofortItem->productNumber,
				'products_name'		=> HelperFunctions::convertEncoding($sofortItem->title,2),
				'products_price'	=> $sofortItem->unitPrice,
				'final_price'		=> ($sofortItem->unitPrice * $sofortItem->quantity),
				'products_tax'		=> $sofortItem->tax,
				'products_quantity' => $sofortItem->quantity,
				'allow_tax'			=> '1',
		);
		shopDbPerform(TABLE_ORDERS_PRODUCTS, $data);
		$insertId = xtc_db_insert_id();

		shopDbQuery('UPDATE sofort_products SET orders_products_id ="'.(int)$insertId.'" WHERE orders_id = "'.(int)$ordersId.'" AND item_id = "'.HelperFunctions::escapeSql($itemId).'"');

		if($hasAttributes) {
			$lngId = shopDbFetchArray(shopDbQuery("SELECT languages_id FROM ".TABLE_LANGUAGES." WHERE code = '".HelperFunctions::escapeSql($lng)."'"));

			foreach($attributes as $attribute){
				$queryTpa = shopDbQuery("SELECT options_values_price, price_prefix FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id ='".HelperFunctions::escapeSql($productId)."' AND options_id = '".HelperFunctions::escapeSql($attribute['optionsId'])."' AND options_values_id ='".HelperFunctions::escapeSql($attribute['optionsValuesId'])."'");
				$resultTpa = shopDbFetchArray($queryTpa);

				$queryTpo = shopDbQuery("SELECT products_options_name FROM ".TABLE_PRODUCTS_OPTIONS." WHERE products_options_id = '".HelperFunctions::escapeSql($attribute['optionsId'])."' AND language_id = '".HelperFunctions::escapeSql($lngId['languages_id'])."'");
				$resultTpo = shopDbFetchArray($queryTpo);

				$queryTpov = shopDbQuery("SELECT products_options_values_name FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = '".HelperFunctions::escapeSql($attribute['optionsValuesId'])."' AND language_id = '".HelperFunctions::escapeSql($lngId['languages_id'])."'");
				$resultTpov = shopDbFetchArray($queryTpov);

				$data = array(
						'orders_id'				  => $ordersId,
						'orders_products_id'	  => $insertId,
						'products_options'		  => $resultTpo['products_options_name'],
						'products_options_values' => $resultTpov['products_options_values_name'],
						'options_values_price'	  => $resultTpa['options_values_price'],
						'price_prefix'			  => $resultTpa['price_prefix']
				);
				shopDbPerform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $data);
			}
		}
	}


	/**
	 * restock shop articles during cartsynchronization, cancelation etc.
	 * @param string $itemId
	 * @param int	 $ordersId
	 * @param int	 $newQty
	 * @param int	 $oldQty
	 */
	protected function _sofortRestock($itemId, $ordersId, $newQty, $oldQty = 'aaaa') {

		if(!is_numeric($oldQty)){
			$oldQty = $this->_getLatestQuantity($itemId, $ordersId);

			if ($oldQty === false) return false;
		}

		$splitItemId = explode('{',$itemId);
		$productId = $splitItemId[0];

		for ($i=1;$i<count($splitItemId);++$i){
			$attrId = explode('}',$splitItemId[$i]);
			$optionsId[] = $attrId[0];
			$optionsValuesId[] = $attrId[1];
		}

		$diff = $oldQty - $newQty;

		$updateTP = "UPDATE ".TABLE_PRODUCTS." SET products_quantity = products_quantity + ".(int)$diff." WHERE products_id = '".(int)$productId."'";
		shopDbQuery($updateTP);

		if(isset($optionsId) && isset($optionsValuesId)){
			for($i=0;$i<count($optionsId);++$i){
				$updateTPA = "UPDATE ".TABLE_PRODUCTS_ATTRIBUTES." SET attributes_stock = attributes_stock + ".(int)$diff." WHERE products_id = '".(int)$productId."' AND options_id = '".(int)$optionsId[$i]."' AND options_values_id ='".$optionsValuesId[$i]."'";
				shopDbQuery($updateTPA);
			}
		}
	}


	/**
	 * inserts a "new total" comment into shop order status history
	 * @param int		  $orderId
	 * @param string	  $status
	 * @param date		  $time (or false, if it should not be set)
	 * @param PnagInvoice $PnagInvoice
	 * @param float		  $lastShopTotal
	 */
	protected function _insertNewTotalCommentToHistory($orderId, $status, $time, PnagInvoice $PnagInvoice, $lastShopTotal){
		$newTotal = $PnagInvoice->getAmount();

		if ($newTotal > $lastShopTotal) {
			$comment = MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CART_RESET.' '.MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CURRENT_TOTAL.' '.$newTotal.' Euro';
		} else {
			$comment = MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CART_EDITED.' '.MODULE_PAYMENT_SOFORT_SR_TRANSLATE_CURRENT_TOTAL.' '.$newTotal.' Euro';
		}

		if ($time) {
			$comments .= ' '.MODULE_PAYMENT_SOFORT_TRANSLATE_TIME.': '.$time;
		}

		$customerNotified = 0;
		//if gambio: $customer_notified must be "1" to make information visibible for customer
		if (HelperFunctions::isGambio())
		{
			if ($status != '-1')
			{
				$customerNotified = '1';
			}
			else // $status == '-1'
			{
				$status = DEFAULT_ORDERS_STATUS_ID;
				$last_status_query = sprintf('SELECT orders_status_id FROM orders_status_history WHERE orders_id = %d ORDER BY date_added DESC LIMIT 1', $orderId);
				$result = xtc_db_query($last_status_query);
				while($row = xtc_db_fetch_array($result))
				{
					$status = $row['orders_status_id'];
				}
			}
		}

		$sqlDataArray = array(
				'orders_id'			=> (int)$orderId,
				'orders_status_id'	=> $status,
				'date_added'		=> 'now()',
				'customer_notified' => $customerNotified,
				'comments'			=> $comment,
		);
		shopDbPerform(TABLE_ORDERS_STATUS_HISTORY, $sqlDataArray);

		//save all relevant data in table sofort_notification
		//because we dont get a notification in this situation
		$dbEntries = array(
			'amount' => $newTotal,
			'status' => $PnagInvoice->getStatus(),
			'status_reason' => $PnagInvoice->getStatusReason(),
			'status_id' => $PnagInvoice->getState(),
		);
		HelperFunctions::insertSofortOrdersNotification($PnagInvoice->getTransactionId(), $dbEntries);
	}
}