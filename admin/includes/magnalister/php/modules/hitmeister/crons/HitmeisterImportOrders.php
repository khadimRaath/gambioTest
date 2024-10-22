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
 * $Id$
 *
 * (c) 2010 - 2011 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleImportOrders.php');

class HitmeisterImportOrders extends MagnaCompatibleImportOrders {
	
	public function __construct($mpID, $marketplace) {
		parent::__construct($mpID, $marketplace);
	}
	
	protected function getConfigKeys() {
		$keys = parent::getConfigKeys();
		$keys['OrderStatusOpen'] = array (
			'key' => 'orderstatus.open',
			'default' => '2',
		);
		return $keys;
	}
	
	protected function getOrdersStatus() {
		return $this->config['OrderStatusOpen'];
	}
	
	protected function additionalProductsIdentification() {
		$ean = $this->p['products_ean'];
		unset($this->p['products_ean']);
		if ($this->p['products_id'] == 0) {
			$pim = MagnaDB::gi()->fetchRow('
				SELECT products_id, products_model FROM '.TABLE_PRODUCTS.'
				 WHERE products_ean = "'.$ean.'"
			');
			if (false !== $pim) {
				$this->p['products_id'] = $pim['products_id'];
				$this->p['products_model'] = $pim['products_model'];
			}
		}
		if ((!isset($this->p['products_name']) || empty($this->p['products_name'])) && ($this->p['products_id'] != 0)) {
			$this->p['products_name'] = MagnaDB::gi()->fetchOne('
				SELECT pd.products_name
				  FROM '.TABLE_PRODUCTS_DESCRIPTION.'pd, '.TABLE_LANGUAGES.' l
				 WHERE pd.products_id = "'.$this->p['products_id'].'"
					   AND pd.language_id = l.languages_id
					   AND l.code = "'.strtolower($this->o['orderInfo']['BuyerCountryISO']).'"
			');
			if ($this->p['products_name'] == false) {
				# Fallback for default language
				$languageId = MagnaDB::gi()->fetchOne('
					SELECT languages_id
					  FROM '.TABLE_LANGUAGES.' l, '.TABLE_CONFIGURATION.' c
					 WHERE c.configuration_key = "DEFAULT_LANGUAGE"
						   AND c.configuration_value = l.code
				');
				$this->p['products_name'] = MagnaDB::gi()->fetchOne('
					SELECT products_name
					  FROM '.TABLE_PRODUCTS_DESCRIPTION.'
					 WHERE pd.products_id = "'.$this->p['products_id'].'"
						   AND language_id = '.$languageId
				);
			}
		}
		if (empty($this->p['products_name'])) {
			$this->p['products_name'] = $ean;
		}
	}
	
}
