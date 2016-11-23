<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

/**
 * @class Shopgate_Model_Catalog_Price
 * @see http://developer.shopgate.com/file_formats/xml/products
 *
 *  @method                                      setType(string $value)
 *  @method string                               getType()
 *
 *  @method                                      setPrice(float $value)
 *  @method float                                getPrice()
 *
 *  @method                                      setCost(float $value)
 *  @method float                                getCost()
 *
 *  @method                                      setSalePrice(float $value)
 *  @method float                                getSalePrice()
 *
 *  @method                                      setMsrp(float $value)
 *  @method float                                getMsrp()
 *
 *  @method                                      setTierPricesGroup(array $value)
 *  @method Shopgate_Model_Catalog_TierPrice[]   getTierPricesGroup()
 *
 *  @method                                      setMinimumOrderAmount(int $value)
 *  @method int                                  getMinimumOrderAmount()
 *
 *  @method                                      setBasePrice(string $value)
 *  @method string                               getBasePrice()
 *
 */
class Shopgate_Model_Catalog_Price extends Shopgate_Model_AbstractExport {
	/**
	 * default price types
	 *
	 * gross
	 */
	const DEFAULT_PRICE_TYPE_GROSS = 'gross';

	/**
	 * net
	 */
	const DEFAULT_PRICE_TYPE_NET = 'net';

	/**
	 * define allowed methods
	 *
	 * @var array
	 */
	protected $allowedMethods = array(
		'Type',
		'Price',
		'Cost',
		'SalePrice',
		'Msrp',
		'TierPricesGroup',
		'MinimumOrderAmount',
		'BasePrice'
	);

	/**
	 * init default object
	 */
	public function __construct() {
		$this->setTierPricesGroup(array());
	}

	public function asXml(Shopgate_Model_XmlResultObject $itemNode) {
		/**
		 * @var Shopgate_Model_XmlResultObject $pricesNode
		 * @var Shopgate_Model_XmlResultObject $tierPricesNode
		 */
		$pricesNode = $itemNode->addChild('prices');
		$pricesNode->addAttribute('type', $this->getType());
		
		$pricesNode->addChild('price', $this->getPrice(), null, false);
		$pricesNode->addChild('cost', $this->getCost(), null, false);
		$pricesNode->addChild('sale_price', $this->getSalePrice(), null, false);
		$pricesNode->addChild('msrp', $this->getMsrp(), null, false);
		$pricesNode->addChild('minimum_order_amount', $this->getMinimumOrderAmount(), null, false);
		$pricesNode->addChildWithCDATA('base_price', $this->getBasePrice(), false);

		$tierPrices = $this->getTierPricesGroup();
		if (!empty($tierPrices)) {
			$tierPricesNode = $pricesNode->addChild('tier_prices');
			foreach ($tierPrices as $customerGroupItem) {
				$customerGroupItem->asXml($tierPricesNode);
			}
		}

		return $itemNode;
	}

	/**
	 * add tier price
	 *
	 * @param Shopgate_Model_Catalog_TierPrice $tierPrice
	 */
	public function addTierPriceGroup(Shopgate_Model_Catalog_TierPrice $tierPrice) {
		$tierPrices = $this->getTierPricesGroup();
		array_push($tierPrices, $tierPrice);
		$this->setTierPricesGroup($tierPrices);
	}
}