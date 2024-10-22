<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id: ComparisonShoppingCheckinSubmit.php 4331 2014-08-05 13:48:23Z tim.neumann $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/CheckinSubmit.php');

class ComparisonShoppingCheckinSubmit extends CheckinSubmit {
	protected $firstRun = false;

	public function __construct($settings = array()) {
		global $_MagnaSession, $_modules;

		$settings = array_merge(array(
			'language' => getDBConfigValue($settings['marketplace'].'.lang', $_MagnaSession['mpID']),
			'currency' => getCurrencyFromMarketplace($_MagnaSession['mpID']),
		), $settings);

		parent::__construct($settings);
	}
	
	private function initUploadInfo() {
		if (MagnaConnector::gi()->getSubsystem() == 'ComparisonShopping') {
			$path = '';
			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'GetCSInfo',
				));
				$path = $result['DATA']['CSVPath'];
				$this->initSession['upload'] = $result['DATA']['HasUpload'] != 'no';
			} catch (MagnaException $e) {
				$path = '';
			}
			$this->firstRun = empty($path);
		}
	}

	private function initRequiredFileds() {
		$this->initSession['RequiredFileds'] = array();
		try {
			$requiredFileds = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'GetRequiredKeys',
			));
			if (!empty($requiredFileds['DATA'])) {
				foreach ($requiredFileds['DATA'] as $key) {
					$this->initSession['RequiredFileds'][$key] = true;
				}
			}
		} catch (MagnaException $e) { }
	}
	
	public function init($mode, $items = -1) {
		parent::init($mode, $items);
		$this->initUploadInfo();
		$this->initRequiredFileds();
	}

	public function makeSelectionFromErrorLog() {
		$madeSelection = false;
		
		$sanitizedIDs = array();
		foreach ($_POST['errIDs'] as $errID) {
			if (ctype_digit($errID)) {
				$sanitizedIDs[] = $errID;
			}
		}
		$res = MagnaDB::gi()->fetchArray('
			SELECT id, products_id, products_model, product_details
			  FROM '.TABLE_MAGNA_CS_ERRORLOG.' 
			 WHERE id IN ('.implode(', ', $sanitizedIDs).')
		');	
	
		if (!empty($res)) {
			$pIDs = array();
			$errorIDs = array();
			$data = array();
			
			foreach ($res as $item) {
				if (getDBConfigValue('general.keytype', '0') == 'artNr') {
					$pID = MagnaDB::gi()->fetchOne('
						SELECT products_id FROM '.TABLE_PRODUCTS.' WHERE products_model=\''.MagnaDB::gi()->escape($item['products_model']).'\'
					');
					if ($pID !== false) {
						$pIDs[] = $pID;
					}
				} else {
					if (MagnaDB::gi()->recordExists(TABLE_PRODUCTS, array('products_id' => $item['products_id']))) {
						$pIDs[] = $pID = $item['products_id'];
					} else {
						$pID = false;
					}
				}
				if ($pID !== false) {
					$errorIDs[$item['id']] = $pID;
					$data[$pID] = $item['product_details'];
				}
			}

			$pIDs = array_unique($pIDs);
			$batch = array();
			foreach ($pIDs as $pID) {
				$selection[$pID] = $data[$pID];
				$batch[] = array(
					'pID' => $pID,
					'data' => $data[$pID],
					'mpID' => $this->_magnasession['mpID'],
					'selectionname' => $this->settings['selectionName'],
					'session_id' => session_id(),
					'expires' => gmdate('Y-m-d H:i:s')
				);
			}
			MagnaDB::gi()->batchinsert(TABLE_MAGNA_SELECTION, $batch, true);
			unset($data);
			unset($pIDs);
			unset($batch);
			
			$this->initSession['selectionFromErrorLog'] = $errorIDs;
			$madeSelection = true;
		}

		return $madeSelection;
	}

	protected function generateRequestHeader() {
		return array(
			'ACTION' => 'AddItems',
			'MODE' => $this->submitSession['mode']
		);
	}

	protected function appendAdditionalData($pID, $product, &$data) {
		$this->simpleprice->setPrice($product['products_price']);
		
		if (getDBConfigValue(array($this->settings['marketplace'].'.price.usespecialoffer', 'val'), $this->_magnasession['mpID'], false)) {
			$specialPrice = $this->simpleprice->getSpecialOffer($product['products_id']);
		} else {
			$specialPrice = 0;
		}
		if ($specialPrice > 0) {
			$this->simpleprice->setPrice($specialPrice);
		}
		$finalPrice = $this->simpleprice->addTaxByTaxID($product['products_tax_class_id'])->calculateCurr()->roundPrice()->getPrice();

/*
		if (MagnaDB::gi()->tableExists(TABLE_SHIPPING_STATUS)) {
			$shippingTime = parseShippingStatusName(
				MagnaDB::gi()->fetchOne(
					'SELECT shipping_status_name 
				       FROM '.TABLE_SHIPPING_STATUS.' 
				      WHERE shipping_status_id=\''.$product['products_shippingtime'].'\' LIMIT 1'
				),
				getDBConfigValue($this->settings['marketplace'].'.shipping.time', $this->_magnasession['mpID'])
			);
		} else {
			$shippingTime = (int)getDBConfigValue($this->settings['marketplace'].'.shipping.time', $this->_magnasession['mpID']);
		}
*/

		if (defined('TABLE_SHIPPING_STATUS')) {
			$shippingTime = MagnaDB::gi()->fetchOne(
				'SELECT shipping_status_name 
			       FROM '.TABLE_SHIPPING_STATUS.' 
			      WHERE shipping_status_id=\''.$product['products_shippingtime'].'\'
			      		AND language_id=\''.getDBConfigValue($this->settings['marketplace'].'.lang', $this->_magnasession['mpID']).'\'
			      LIMIT 1'
			);
		} else {
			$shippingTime = '';
		}

		if ($product['manufacturers_id'] > 0) {
			$manufacturerName = MagnaDB::gi()->fetchOne(
				'SELECT manufacturers_name FROM '.TABLE_MANUFACTURERS.' WHERE manufacturers_id=\''.$product['manufacturers_id'].'\''
			);
		} else {
			$manufacturerName = '';
		}

		$mfrmd = getDBConfigValue('comparisonshopping.checkin.manufacturerpartnumber.table', $this->mpID, false);
		
		if (is_array($mfrmd) && !empty($mfrmd['column']) && !empty($mfrmd['table'])) {
			$pIDAlias = getDBConfigValue('comparisonshopping.checkin.manufacturerpartnumber.alias', $this->mpID);
			if (empty($pIDAlias)) {
				$pIDAlias = 'products_id';
			}
			$data['submit']['ManufacturerPartNumber'] = MagnaDB::gi()->fetchOne('
				SELECT `'.$mfrmd['column'].'`
				  FROM `'.$mfrmd['table'].'`
				 WHERE `'.$pIDAlias.'`=\''.MagnaDB::gi()->escape($pID).'\'
				 LIMIT 1
			');
		}

		$itemUrl = HTTP_CATALOG_SERVER . DIR_WS_CATALOG . 'product_info.php?products_id='.$product['products_id'];
		if (($campaign = getDBConfigValue($this->settings['marketplace'].'.campaignlink', $this->_magnasession['mpID'], '')) != '') {
			$itemUrl .= '&mlcampaign='.trim($campaign);
		}

		$imagePath = getDBConfigValue('comparisonshopping.imagepath', $this->_magnasession['mpID'], SHOP_URL_POPUP_IMAGES);
		$imagePath = trim($imagePath, '/ ').'/';
		if (!empty($product['products_image'])) {
			$imageUrl = $imagePath.$product['products_image'];
		} else {
			$imageUrl = '';
		}

		$data['submit']['SKU']				= magnaPID2SKU($product['products_id']);
		$data['submit']['ItemTitle']		= $product['products_name'];
		$data['submit']['Price']			= $finalPrice;
		$data['submit']['Currency']			= $this->settings['currency'];
		$data['submit']['Description']		= sanitizeProductDescription($product['products_description']);
		$data['submit']['ItemUrl']			= $itemUrl;						# Vollstaendiger Link zum Artikel im Shop.
		$data['submit']['Manufacturer']	    = $manufacturerName;
		$data['submit']['Image']			= $imageUrl;					# Vollstaendiger Pfad zum Bild im Shop.
		$data['submit']['ShippingCost']		= (string)$data['shippingcost'];
		$data['submit']['ShippingTime']		= $shippingTime;				# in Tagen
		$data['submit']['ItemWeight']		= $product['products_weight'];	# in kg
		if (defined('MAGNA_FIELD_PRODUCTS_EAN') && array_key_exists(MAGNA_FIELD_PRODUCTS_EAN, $product)) {
			$data['submit']['EAN']			= $product[MAGNA_FIELD_PRODUCTS_EAN];
		}
		// BasePrice
		if (isset($product['products_vpe_name']) && (0 <> $product['products_vpe_value'])) {
			$data['submit']['BasePrice'] = array (
				'Unit' => $product['products_vpe_name'],
				'Value' => $product['products_vpe_value'],
			);
		}

	}
	
	protected function generateErrorSaveArray(&$data) {
		return array (
			'shippingcost' => $data['submit']['ShippingCost'],
		);
	}
	
	protected function filterItem($pID, $data) {
		return array();
	}

	protected function filterSelection() {
		if (!is_array($this->initSession['RequiredFileds'])) {
			$this->initRequiredFileds();
		}

		$shitHappend = false;
		$missingFields = array();
		foreach ($this->selection as $pID => &$data) {
			if ($data['submit']['Price'] <= 0) {
				// Loesche das Feld, um eine Fehlermeldung zu erhalten
				unset($data['submit']['Price']);
			}
			
			$mfC = array();
			
			#echo var_dump_pre($data['submit'], 'data[submit]');
			#echo var_dump_pre($this->initSession['RequiredFileds'], 'initSession[RequiredFileds]');
			
			$this->requirementsMet($data['submit'], $this->initSession['RequiredFileds'], $mfC);
			
			#echo var_dump_pre($mfC);
			
			$mfC = array_merge($mfC, $this->filterItem($pID, $data['submit']));

			if (!empty($mfC)) {
				foreach ($mfC as $key => $field) {
					$mfC[$key] = ltrim(strtoupper(preg_replace('/([A-Z][a-z])/', '_${1}', $field)), '_');
				}
				MagnaDB::gi()->insert(
					TABLE_MAGNA_CS_ERRORLOG,
					array (
						'mpID' => $this->_magnasession['mpID'],
						'products_id' => $pID,
						'products_model' => MagnaDB::gi()->fetchOne('SELECT products_model FROM '.TABLE_PRODUCTS.' WHERE products_id=\''.$pID.'\''),
						'product_details' => serialize($this->generateErrorSaveArray($data)),
						'errormessage' => json_encode($mfC),
						'timestamp' => gmdate('Y-m-d H:i:s')
					)
				);
				$shitHappend = true;
				$this->badItems[] = $pID;
				unset($this->selection[$pID]);
			}
		}
		return $shitHappend;
	}
	
	protected function processSubmitResult($result) {}
	
	protected function postSubmit() {
		#echo 'postSubmit';
		if (isset($this->initSession['selectionFromErrorLog']) && !empty($this->initSession['selectionFromErrorLog'])) {
			foreach ($this->initSession['selectionFromErrorLog'] as $errID => $pID) {
				MagnaDB::gi()->delete(
					TABLE_MAGNA_CS_ERRORLOG,
					array(
						'id' => (int)$errID
					)
				);
			}
		}
		if ($this->initSession['upload'] === null) {
			$this->initUploadInfo();
		}
		#echo var_dump_pre($this->initSession['upload']);
		if ($this->initSession['upload']) {
			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'UploadItems',
					'MODE' => $this->submitSession['initialmode']
				));
				#echo print_m($result, true);
			} catch (MagnaException $e) {
				#echo print_m($e, 'Exception', true);
				$this->submitSession['api']['exception'] = $e->getErrorArray();
			}
		}
	}

	protected function generateRedirectURL($state) {
		return toURL(array(
			'mp' => $this->realUrl['mp'],
			'mode'   => 'listings',
		    'view'   => ($state == 'fail') ? 'failed' : 'inventory'
		), true);
	}

	protected function getFinalDialogs() {
		global $_modules;
		if ($this->firstRun && ($this->submitSession['state']['success'] > 0)) {
			$path = '';
			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'GetCSInfo',
				));
				$path = $result['DATA']['CSVPath'];
			} catch (MagnaException $e) { }
			return array (
				array (
					'headline' => ML_LABEL_INFORMATION,
					'message' => sprintf(ML_CSHOPPING_TEXT_FIRST_CHECKIN, $_modules[$this->settings['marketplace']]['title'], $path)
				),
			);
		}
		return array();
	}
}
