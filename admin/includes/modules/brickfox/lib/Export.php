<?php

/* --------------------------------------------------------------
  $Id: Export.php 0.1 2011-07-16 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2011 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

class Brickfox_Lib_Export
{
	var $_brickfoxConfiguration;
	var $_lastExport;
	var $_type;
	var $_xml;
	var $_optionsCounter;
	var $_overallCounter;
	var $_exportPath = 'export';
	var $_categoriesFilename = 'Categories_';
	var $_manufaturersFilename = 'Manufacturers_';
	var $_ordersUpdateFilename = 'OrdersUpdate_';
	var $_productsFilename = 'Products_';
	var $_productsUpdateFilename = 'ProductsUpdate_';
	var $_fileExtension = 'xml';
	
	/**
	* constructor
	* @param Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration
	* @param string $type
	* @param null|string $lastExport
	*/
	function Brickfox_Lib_Export(Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration, $type, $lastExport = null)
	{
		$this->setBrickfoxConfiguration($brickfoxConfiguration);
		$this->setType($type);
		$this->setLastExport($lastExport);
	}
	
	/**
	 * get brickfoxConfiguration
	 *
	 * @return Brickfox_Lib_BrickfoxConfiguration
	 */
	function getBrickfoxConfiguration()
	{
		return $this->_brickfoxConfiguration;
	}	
	
	/**
	 * set brickfoxConfiguration
	 *
	 * @param Brickfox_Lib_BrickfoxConfiguration
	 */
	function setBrickfoxConfiguration(Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration)
	{
		$this->_brickfoxConfiguration = $brickfoxConfiguration;
	}

	/**
	 * get lastExport
	 *
	 * @return string
	 */
	function getLastExport()
	{
		return $this->_lastExport;
	}	
	
	/**
	 * set lastExport
	 *
	 * @param string
	 */
	function setLastExport($lastExport)
	{
		$this->_lastExport = $lastExport;
	}
	
	/**
	 * get type
	 *
	 * @return string
	 */
	function getType()
	{
		return $this->_type;
	}	
	
	/**
	 * set lastExport
	 *
	 * @param string
	 */
	function setType($type)
	{
		$this->_type = $type;
	}	

	/**
	 * get info images path
	 *
	 * @return string
	 */
	function getInfoImagesPath()
	{
		return HTTP_SERVER . DIR_WS_CATALOG_INFO_IMAGES;
	}

	/**
	 * get popup images path
	 *
	 * @return string
	 */
	function getPopupImagesPath()
	{
		return HTTP_SERVER . DIR_WS_CATALOG_POPUP_IMAGES;
	}

	/**
	 * reset option counter
	 *
	 * @return void
	 */
	function resetOptionCounter()
	{
		$this->_optionsCounter = 1;
	}

	/**
	 * increment option counter
	 *
	 * @return void
	 */
	function incrementOptionCounter()
	{
		$this->_optionsCounter++;
	}

	/**
	 * get option counter
	 *
	 * @return int
	 */
	function getOptionCounter()
	{
		return $this->_optionsCounter;
	}

	/**
	 * reset overall counter
	 *
	 * @return void
	 */
	function resetOverallCounter()
	{
		$this->_overallCounter = 0;
	}

	/**
	 * increment option counter
	 *
	 * @return void
	 */
	function incrementOverallCounter()
	{
		$this->_overallCounter++;
	}

	/**
	 * get overall counter
	 *
	 * @return int
	 */
	function getOverallCounter()
	{
		return $this->_overallCounter;
	}

	/**
	 * get export path
	 *
	 * @return string
	 */
	function getExportPath()
	{
		return $this->_exportPath;
	}

	/**
	 * get categories filename
	 *
	 * @return string
	 */
	function getCategoriesFilename()
	{
		return $this->_categoriesFilename;
	}

	/**
	 * get manufacturers filename
	 *
	 * @return string
	 */
	function getManufacturersFilename()
	{
		return $this->_manufaturersFilename;
	}

	/**
	 * get orders update filename
	 *
	 * @return string
	 */
	function getOrdersUpdateFilename()
	{
		return $this->_ordersUpdateFilename;
	}

	/**
	 * get products filename
	 *
	 * @return string
	 */
	function getProductsFilename()
	{
		return $this->_productsFilename;
	}

	/**
	 * get products update filename
	 *
	 * @return string
	 */
	function getProductsUpdateFilename()
	{
		return $this->_productsUpdateFilename;
	}

	/**
	 * get filename extension
	 *
	 * @return string
	 */
	function getFileExtension()
	{
		return $this->_fileExtension;
	}

	/**
	 * compose root, export path and filename with current date and filename extension
	 *
	 * @param string $filename
	 * @return string
	 */
	function getFilePathAndName($filename)
	{
		$filePathAndName = DIR_FS_DOCUMENT_ROOT . $this->getExportPath() . '/' . $filename . '_' . date("Ymd_His") . '.' . $this->getFileExtension();
		return $filePathAndName;
	}

	/**
	 * calculate price gross
	 *
	 * @param float $priceGross
	 * @param float $taxRate
	 * @return float
	 */
	function getPriceGrossIsNotBrutto($priceGross, $taxRate)
	{
		$priceGross *= (1. + ($taxRate / 100));
		return $priceGross;
	}

	/**
	 * compose query with and
	 *
	 * @param string $queryFilter
	 * @return string
	 */
	function getQueryAnd($queryFilter)
	{
		if ($queryFilter != '') {
			$queryFilter .= ' AND ';
		}
		return $queryFilter;
	}

	/**
	 * compose query with where
	 *
	 * @param string $queryFilter
	 * @return string
	 */
	function getQueryWhere($queryFilter)
	{
		if ($queryFilter != '') {
			$queryFilter = ' WHERE ' . $queryFilter;
		}
		return $queryFilter;
	}

	/**
	 * compose where query with last export date
	 *
	 * @param string $queryFilter
	 * @param string $lastExport
	 * @return string
	 */
	function getDateAddedOrLastModifiedByLastExportQuery($queryFilter, $lastExport)
	{
		if ($lastExport !== null) {
			$queryFilter = $this->getQueryAnd($queryFilter);
			$queryFilter = '(date_added > "' . $lastExport . '"
			    OR last_modified > "' . $lastExport . '")';
		}
		return $queryFilter;
	}

	/**
	 * compose where query with last modified date
	 *
	 * @param string $queryFilter
	 * @param string $lastExport
	 * @return string
	 */
	function getLastModifiedByLastExportQuery($queryFilter, $lastExport)
	{
		if ($lastExport !== null) {
			$queryFilter = $this->getQueryAnd($queryFilter);
			$queryFilter = 'last_modified > "' . $lastExport . '"';
		}
		return $queryFilter;
	}

	/**
	 * compose where query with products_date_added or products_last_update
	 *
	 * @param string $queryFilter
	 * @param string $lastExport
	 * @return string
	 */
	function getProductsDateAddedOrProductsLastModifiedLastExportQuery($queryFilter, $lastExport)
	{
		if ($lastExport !== null) {
			$queryFilter = $this->getQueryAnd($queryFilter);
			$queryFilter = '(p.products_date_added > "' . $lastExport . '"
			    OR p.products_last_modified > "' . $lastExport . '")';
		}
		return $queryFilter;
	}

	/**
	 * compose where query with excluded categories
	 *
	 * @param string $queryFilter
	 * @param Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration
	 * @return string
	 */
	function getExcludeCategoriesQuery($queryFilter, Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration)
	{
		if ($brickfoxConfiguration->hasExcludeCategories() == true) {
			$queryFilter = $this->getQueryAnd($queryFilter);
			$queryFilter = 'categories_id NOT IN ("' . $brickfoxConfiguration->getExcludeCategoriesImplodeList() . '")';
		}
		return $queryFilter;
	}

	/**
	 * compose where query with excluded products
	 *
	 * @param string $queryFilter
	 * @param Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration
	 * @return string
	 */
	function getExcludeProductsQuery($queryFilter, Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration)
	{
		if ($brickfoxConfiguration->hasExcludeproducts() == true) {
			$queryFilter = $this->getQueryAnd($queryFilter);
			$queryFilter = 'p.products_model NOT IN ("' . $brickfoxConfiguration->getExcludeProductsImplodeList() . '")';
		}
		return $queryFilter;
	}

	/**
	 * compose where query with products are not in excluded categories
	 *
	 * @param string $queryFilter
	 * @param Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration
	 * @return string
	 */
	function getExcludeProductsToCategoriesQuery($queryFilter, Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration)
	{
		if ($brickfoxConfiguration->hasExcludeCategories() == true) {
			$queryFilter = $this->getQueryAnd($queryFilter);
			$queryFilter = 'p.products_id NOT IN (SELECT products_id
		FROM TABLE_PRODUCTS_TO_CATEGORIES
		WHERE categories_id IN ("' . $brickfoxConfiguration->getExcludeCategoriesImplodeList() . '")';
		}
		return $queryFilter;
	}

	/**
	 * get categories query where categories are in products_to_categories by products_id
	 *
	 * @param int $productsId
	 * @return string
	 */
	function getCategoriesIdByProductsIdQuery($productsId)
	{
		$query = '
	    SELECT categories_id
	    FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . '
	    WHERE products_id = ' . $productsId;

		return $query;
	}

	/**
	 * get products description query for products_id and default language
	 *
	 * @param int $productsId
	 * @return string
	 */
	function getProductsDescriptionByProductsIdQuery($productsId)
	{
		$query = '
	    SELECT l.code as language,
	    pd.products_name,
	    pd.products_description,
	    pd.products_short_description,
	    pd.products_keywords
	    FROM ' . TABLE_PRODUCTS_DESCRIPTION . ' pd
	    LEFT JOIN ' . TABLE_LANGUAGES . ' l ON l.languages_id = pd.language_id
	    WHERE products_id = ' . $productsId;

		return $query;
	}

	/**
	 * get image query by products_id
	 *
	 * @param int $productsId
	 * @return string
	 */
	function getProductsImageByProductsIdQuery($productsId)
	{
		$query = '
	    SELECT image_name
	    FROM ' . TABLE_PRODUCTS_IMAGES . '
	    WHERE products_id = ' . $productsId . '
	    ORDER BY image_nr';

		return $query;
	}

	/**
	 * get products attributes query by products_id
	 *
	 * @param int $productsId
	 * @return string
	 */
	function getOptionsByProductsIdQuery($productsId)
	{
		$query = '
	    SELECT DISTINCT options_id
	    FROM ' . TABLE_PRODUCTS_ATTRIBUTES . '
	    WHERE products_id = ' . $productsId;
		return $query;
	}

	/**
	 * get options descriptions query by options_id and options_values_id
	 *
	 * @param int $optionsId
	 * @param int $optionsValuesId
	 * @return string
	 */
	function getOptionsTranslationByProductsIdQuery($optionsId, $optionsValuesId)
	{
		$query = '
	    SELECT l.code as language, po.products_options_name, pov.products_options_values_name
	    FROM (' . TABLE_PRODUCTS_OPTIONS . ' po, ' . TABLE_PRODUCTS_OPTIONS_VALUES . ' pov)
	    JOIN ' . TABLE_LANGUAGES . ' l ON l.languages_id = po.language_id AND l.languages_id = pov.language_id
	    WHERE po.products_options_id = ' . $optionsId . '
	    AND pov.products_options_values_id = ' . $optionsValuesId;
		return $query;
	}

	/**
	 * get categories query with last_export filter
	 *
	 * @param string $lastExport
	 * @param Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration
	 * @return string
	 */
	function getCategoriesQuery($lastExport, Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration)
	{
		$queryFilter = '';
		$queryFilter = $this->getDateAddedOrLastModifiedByLastExportQuery($queryFilter, $lastExport);
		$queryFilter = $this->getExcludeCategoriesQuery($queryFilter, $brickfoxConfiguration);
		$queryFilter = $this->getQueryWhere($queryFilter);

		$query = '
	    SELECT
	    categories_id, parent_id
	    FROM ' . TABLE_CATEGORIES . $queryFilter .
				 ' ORDER BY GREATEST(date_added, last_modified)';
		return $query;
	}

	/**
	 * get manufacturers query with las_export filter
	 *
	 * @param string $lastExport
	 * @return string
	 */
	function getManufactorersQuery($lastExport)
	{
		$queryFilter = '';
		$queryFilter = $this->getDateAddedOrLastModifiedByLastExportQuery($queryFilter, $lastExport);
		$queryFilter = $this->getQueryWhere($queryFilter);

		$query = '
	    SELECT manufacturers_id,
	    manufacturers_name
	    FROM ' . TABLE_MANUFACTURERS . $queryFilter;

		return $query;
	}

	/**
	 * get orders update query with last_export filter
	 *
	 * @param string $lastExport
	 * @return string
	 */
	function getOrdersUpdateQuery($lastExport)
	{
		$queryFilter = '';
		$queryFilter = $this->getLastModifiedByLastExportQuery($queryFilter, $lastExport);
		$queryFilter = $this->getQueryWhere($queryFilter);

		$query = '
	    SELECT o.orders_id,	o.last_modified, o.orders_date_finished, o.orders_status, bfo.brickfox_orders_id
	    FROM ' . TABLE_ORDERS . ' o
	    INNER JOIN brickfox_orders bfo ON bfo.intern_orders_id = o.orders_id ' . $queryFilter;

		return $query;
	}

	/**
	 * get products query with last_export filter
	 *
	 * @param string $lastExport
	 * @param Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration
	 * @return string
	 */
	function getProductsQuery($lastExport, Brickfox_Lib_BrickfoxConfiguration $brickfoxConfiguration)
	{
		$queryFilter = '';
		$queryFilter = $this->getProductsDateAddedOrProductsLastModifiedLastExportQuery($queryFilter, $lastExport);
		$queryFilter = $this->getExcludeProductsToCategoriesQuery($queryFilter, $brickfoxConfiguration);
		$queryFilter = $this->getExcludeProductsQuery($queryFilter, $brickfoxConfiguration);
		$queryFilter = $this->getQueryWhere($queryFilter);

		$query = '
	    SELECT p.products_id,
	    p.products_shippingtime,
	    p.products_ean,
	    p.products_status,
	    p.products_quantity,
	    p.manufacturers_id,
	    p.products_image,
	    p.products_model,
	    p.products_price,
	    tr.tax_rates_id,
	    tr.tax_rate,
	    m.manufacturers_name
	    FROM ' . TABLE_PRODUCTS . ' p
	    LEFT JOIN ' . TABLE_TAX_RATES . ' tr ON tr.tax_class_id = p.products_tax_class_id
		AND tr.tax_zone_id = (
					SELECT ztgz.geo_zone_id FROM ' . TABLE_ZONES . ' z
					LEFT JOIN ' . TABLE_ZONES_TO_GEO_ZONES . ' ztgz ON ztgz.zone_country_id = z.zone_country_id
					WHERE z.zone_id = ' . STORE_ZONE . '
					)
	    LEFT JOIN ' . TABLE_MANUFACTURERS . ' m ON m.manufacturers_id = p.manufacturers_id ' . $queryFilter;

		return $query;
	}

	/**
	 * get categories description query for categories id
	 *
	 * @param int $categoriesId
	 * @return string
	 */
	function getCategoriesNodeTranslationQuery($categoriesId)
	{
		$query = 'SELECT l.code AS language,
	    cd.categories_name
	    FROM ' . TABLE_CATEGORIES_DESCRIPTION . ' cd
	    LEFT JOIN ' . TABLE_LANGUAGES . ' l
	    ON l.languages_id = cd.language_id
	    WHERE cd.categories_id = ' . $categoriesId;

		return $query;
	}

	/**
	 * get manufacturers description for manufacturers id and default language
	 *
	 * @param int $manufacturersId
	 * @return string
	 */
	function getManufacturersNodeTranslationQuery($manufacturersId)
	{
		$query = 'SELECT l.code as language
	    FROM ' . TABLE_MANUFACTURERS_INFO . ' mi
	    LEFT JOIN ' . TABLE_LANGUAGES . ' l
	    ON l.languages_id = mi.languages_id
	    WHERE mi.manufacturers_id = ' . $manufacturersId . '
	    AND l.code != "' . DEFAULT_LANGUAGE . '"';

		return $query;
	}

	/**
	 * get shipping status description for shipping_status_id
	 *
	 * @param int $shippingStatusId
	 * @return string
	 */
	function getShippingStatusTranslationQuery($shippingStatusId)
	{
		$query = 'SELECT shipping_status_name
	    FROM ' . TABLE_SHIPPING_STATUS . ' AS ss
	    INNER JOIN languages AS s ON ss.language_id = s.languages_id
	    WHERE shipping_status_id = ' . $shippingStatusId . '
	    AND code = "' . DEFAULT_LANGUAGE . '"';

		return $query;
	}

	/**
	 * get order line query by orders id
	 *
	 * @param int $ordersId
	 * @return string
	 */
	function getOrdersLineNodeQuery($ordersId)
	{
		$query = 'SELECT op.orders_products_id, op.products_id,	op.products_quantity, bfol.brickfox_orders_lines_id
	FROM ' . TABLE_ORDERS_PRODUCTS . ' op
	INNER JOIN brickfox_orders_lines bfol ON bfol.orders_products_id = op.orders_products_id
	WHERE op.orders_id = ' . $ordersId;

		return $query;
	}

	/**
	 * export categories
	 *
	 * @return string
	 */
	function exportCategory()
	{
		$file = $this->getFilePathAndName($this->getCategoriesFilename());

		$this->_xml = new Brickfox_Lib_DOMDocument('1.0', 'UTF-8');
		$this->_xml->formatOutput = true;

		$categoriesNode = $this->_xml->createElement('Categories');

		$query = $this->getCategoriesQuery($this->getLastExport(), $this->getBrickfoxConfiguration());
		$categoriesResult = xtc_db_query($query);

		$this->resetOverallCounter();
		while ($categoriesRow = xtc_db_fetch_array($categoriesResult)) {

			$this->incrementOverallCounter();

			$categoriesNode->appendChild($this->_getCategoryNode($categoriesRow));
		}

		$categoriesNode->setAttribute('count', $this->getOverallCounter());

		$this->_xml->appendChild($categoriesNode);

		$this->_xml->save($file);

		return $file;
	}

	/**
	 * _getCategoryNode()
	 *
	 * @param Array $categoriesRow
	 * @return Brickfox_Lib_DOMElement
	 */
	function _getCategoryNode($categoriesRow)
	{
		$categoryNode = $this->_xml->createElement('Category');
		$categoryNode->setAttribute('id', $this->getOverallCounter());
		$categoryNode->appendChild($this->_xml->createElement('CategoryId', $categoriesRow['categories_id']));
		$categoryNode->appendChild($this->_xml->createElement('ParentId', $categoriesRow['parent_id']));

		$translationsNode = $this->_xml->createElement('Translations');

		$query = $this->getCategoriesNodeTranslationQuery($categoriesRow['categories_id']);
		$translationsQuery = xtc_db_query($query);

		while ($translationsRow = xtc_db_fetch_array($translationsQuery)) {
			$translationNode = $this->_xml->createElement('Translation');
			$translationNode->setAttribute('lang', $translationsRow['language']);
			$translationNode->appendChild($this->_xml->createElement('Name', $translationsRow['categories_name']));

			$translationsNode->appendChild($translationNode);
		}

		$categoryNode->appendChild($translationsNode);

		return $categoryNode;
	}

	/**
	 * export manufacturers
	 *
	 * @return string
	 */
	function exportManufacturer()
	{
		$file = $this->getFilePathAndName($this->getManufacturersFilename());

		$this->_xml = new Brickfox_Lib_DOMDocument('1.0', 'UTF-8');
		$this->_xml->formatOutput = true;

		$manufacturersNode = $this->_xml->createElement('Manufacturers');

		$query = $this->getManufactorersQuery($this->getLastExport());
		$manufacturersQuery = xtc_db_query($query);

		$this->resetOverallCounter();
		while ($manufacturersRow = xtc_db_fetch_array($manufacturersQuery)) {
			$this->incrementOverallCounter();

			$manufacturersNode->appendChild($this->_getManufacturerNode($manufacturersRow));
		}
		$manufacturersNode->setAttribute('count', $this->getOverallCounter());

		$this->_xml->appendChild($manufacturersNode);
		$this->_xml->save($file);
		return $file;
	}

	/**
	 * _getManufacturerNode()
	 *
	 * @param Array $manufacturersRow
	 * @return Brickfox_Lib_DOMElement
	 */
	function _getManufacturerNode($manufacturersRow)
	{
		$manufacturerNode = $this->_xml->createElement('Manufacturer');
		$manufacturerNode->setAttribute('id', $this->getOverallCounter());
		$manufacturerNode->appendChild($this->_xml->createElement('ManufacturerId', $manufacturersRow['manufacturers_id']));

		$translationsNode = $this->_xml->createElement('Translations');

		$translationNode = $this->_xml->createElement('Translation');
		$translationNode->setAttribute('lang', DEFAULT_LANGUAGE);
		$translationNode->appendChild($this->_xml->createElement('Name', $manufacturersRow['manufacturers_name']));

		$translationsNode->appendChild($translationNode);

		$query = $this->getManufacturersNodeTranslationQuery($manufacturersRow['manufacturers_id']);
		$translationsQuery = xtc_db_query($query);

		while ($translationsRow = xtc_db_fetch_array($translationsQuery)) {
			$translationNode = $this->_xml->createElement('Translation');
			$translationNode->setAttribute('lang', $translationsRow['language']);
			$translationNode->appendChild($this->_xml->createElement('Name', $manufacturersRow['manufacturers_name']));

			$translationsNode->appendChild($translationNode);
		}

		$manufacturerNode->appendChild($translationsNode);

		return $manufacturerNode;
	}

	/**
	 * export orders
	 *
	 * @return string
	 */
	function exportOrder()
	{
		$file = $this->getFilePathAndName($this->getOrdersUpdateFilename());

		$this->_xml = new Brickfox_Lib_DOMDocument('1.0', 'UTF-8');
		$this->_xml->formatOutput = true;

		$ordersNode = $this->_xml->createElement('Orders');

		$query = $this->getOrdersUpdateQuery($this->getLastExport());

		$ordersQuery = xtc_db_query($query);

		$this->resetOverallCounter();
		while ($ordersRow = xtc_db_fetch_array($ordersQuery)) {
			$this->incrementOverallCounter();

			$ordersNode->appendChild($this->_getOrderNode($ordersRow));
		}
		$ordersNode->setAttribute('count', $this->getOverallCounter());

		$this->_xml->appendChild($ordersNode);
		$this->_xml->save($file);
		return $file;
	}

	/**
	 * _getOrderNode()
	 *
	 * @param Array $ordersRow
	 * @return Brickfox_Lib_DOMElement
	 */
	function _getOrderNode($ordersRow)
	{
		$orderNode = $this->_xml->createElement('Order');
		$orderNode->setAttribute('id', $this->getOverallCounter());
		$orderNode->appendChild($this->_xml->createElement('OrderId', $ordersRow['brickfox_orders_id']));
		$orderNode->appendChild($this->_xml->createElement('ExternOrderId', $ordersRow['orders_id']));
		$orderNode->appendChild($this->_xml->createElement('OrderStatusChanged', $ordersRow['last_modified']));
		$orderNode->appendChild($this->_xml->createElement('OrderStatus', $ordersRow['orders_status']));
		$orderNode->appendChild($this->_xml->createElement('ShippingTrackingId'));
		$orderNode->appendChild($this->_xml->createElement('TrackCode'));
		$orderNode->appendChild($this->_xml->createElement('SendDate', $ordersRow['orders_date_finished']));
		$orderNode->appendChild($this->_xml->createElement('SendLogistic'));

		$orderLinesNode = $this->_xml->createElement('OrderLines');

		$query = $this->getOrdersLineNodeQuery($ordersRow['orders_id']);
		$orderLinesQuery = xtc_db_query($query);

		$orderLinesCounter = 0;
		while ($orderLinesRow = xtc_db_fetch_array($orderLinesQuery)) {
			$orderLinesCounter++;

			$orderLineNode = $this->_xml->createElement('OrderLine');
			$orderLineNode->appendChild($this->_xml->createElement('OrderLineId', $orderLinesRow['brickfox_orders_lines_id']));
			$orderLineNode->appendChild($this->_xml->createElement('ExternProductId', $orderLinesRow['products_id']));
			$orderLineNode->appendChild($this->_xml->createElement('OrderLineStatus'));
			$orderLineNode->appendChild($this->_xml->createElement('QunatityCancelled'));
			$orderLineNode->appendChild($this->_xml->createElement('QunatityShipped'));
			$orderLineNode->appendChild($this->_xml->createElement('QunatityReturned'));
			$orderLineNode->appendChild($this->_xml->createElement('QuantityOrdered', $orderLinesRow['products_quantity']));

			$orderLinesNode->appendChild($orderLineNode);
		}
		$orderLinesNode->setAttribute('count', $orderLinesCounter);

		$orderNode->appendChild($orderLinesNode);

		return $orderNode;
	}
	
	/**
	 * _processExportProduct
	 *
	 * @param string $type
	 * @param string $lastExport
	 * @return null|string
	 */	
	function _processExportProduct($file, $lastExport = null)
	{
		$this->_xml = new Brickfox_Lib_DOMDocument('1.0', 'UTF-8');
		$this->_xml->formatOutput = true;

		$productsNode = $this->_xml->createElement('Products');

		$query = $this->getProductsQuery($lastExport, $this->getBrickfoxConfiguration());

		$productsQuery = xtc_db_query($query);
		$this->resetOverallCounter();
		
		$nodeMethod = '_get' . $this->getType() . 'Node';
		while ($productsRow = xtc_db_fetch_array($productsQuery)) {

			$this->incrementOverallCounter();
			$productsNode->appendChild($this->$nodeMethod($productsRow));
		}

		$productsNode->setAttribute('count', $this->getOverallCounter());
		$this->_xml->appendChild($productsNode);
		$this->_xml->save($file);

		return $file;
	}

	/**
	 * export product
	 *
	 * @param string $type
	 * @return null|string
	 */
	function exportProduct()
	{
		$file = $this->getFilePathAndName($this->getProductsFilename());

		return $this->_processExportProduct($file, '0000-00-00 00:00:00');
	}
	
	/**
	 * export product update large
	 *
	 * @param string $type
	 * @return null|string
	 */
	function exportProductUpdateLarge()
	{
		$file = $this->getFilePathAndName($this->getProductsFilename());

		return $this->_processExportProduct($file, $this->getLastExport());
	}	
	
	/**
	 * export product update
	 *
	 * @return null|string
	 */
	function exportProductUpdate()
	{
		$file = $this->getFilePathAndName($this->getProductsUpdateFilename());

		return $this->_processExportProduct($file, $this->getLastExport());
	}

	/**
	 * _getProductUpdateLargeNode()
	 *
	 * @param Array $productsRow
	 * @return Brickfox_Lib_DOMElement
	 */
	function _getProductUpdateLargeNode($productsRow)
	{
		return $this->_getProductNode($productsRow);
	}

	/**
	 * _getProductNode()
	 *
	 * @param Array $productsRow
	 * @return Brickfox_Lib_DOMElement
	 */
	function _getProductNode($productsRow)
	{
		$priceGross = $productsRow['products_price'];

		$priceGross = $this->getPriceGrossIsNotBrutto($priceGross, $productsRow['tax_rate']);

		$productNode = $this->_xml->createElement('Product');
		$productNode->setAttribute('id', $this->getOverallCounter());
		$productNode->appendChild($this->_xml->createElement('ProductExternId', $productsRow['products_id']));
		$productNode->appendChild($this->_xml->createElement('ItemNumber', $productsRow['products_model']));
		$productNode->appendChild($this->_xml->createElement('EAN', $productsRow['products_ean']));
		$productNode->appendChild($this->_xml->createElement('Active', $productsRow['products_status']));
		$productNode->appendChild($this->_xml->createElement('Available', $productsRow['products_status']));
		$productNode->appendChild($this->_xml->createElement('ThirdPartyStock', 0));

		$query = $this->getShippingStatusTranslationQuery($productsRow['products_shippingtime']);
		$shippingTimeQuery = xtc_db_query($query);
		$shippingTimeName = '';
		$shippingTime = xtc_db_fetch_array($shippingTimeQuery);
		if (isset($shippingTime['shipping_status_name'])) {
			$shippingTimeName = $shippingTime['shipping_status_name'];
		}
		$productNode->appendChild($this->_xml->createElement('DeliveryTime', $shippingTimeName));

		$productNode->appendChild($this->_xml->createElement('Stock', $productsRow['products_quantity']));
		$productNode->appendChild($this->_xml->createElement('TaxId', $productsRow['tax_rates_id']));
		$productNode->appendChild($this->_xml->createElement('TaxRate', $productsRow['tax_rate']));

		$priceGrossNode = $this->_xml->createElement('PriceGross', $priceGross);
		$priceGrossNode->setAttribute('cur', DEFAULT_CURRENCY);

		$productNode->appendChild($priceGrossNode);

		$rrpNode = $this->_xml->createElement('Rrp');
		$rrpNode->setAttribute('cur', DEFAULT_CURRENCY);

		$productNode->appendChild($rrpNode);

		$primeCostNode = $this->_xml->createElement('PrimeCost');
		$primeCostNode->setAttribute('cur', DEFAULT_CURRENCY);

		$productNode->appendChild($primeCostNode);

		$categoriesNode = $this->_xml->createElement('Categories');

		$query = $this->getCategoriesIdByProductsIdQuery($productsRow['products_id']);
		$categoriesQuery = xtc_db_query($query);

		$firstCategory = 1;
		while ($categoriesRow = xtc_db_fetch_array($categoriesQuery)) {
			$categoryNode = $this->_xml->createElement('Category');
			$categoryNode->setAttribute('main', $firstCategory);
			$firstCategory = 0;
			$categoryNode->appendChild($this->_xml->createElement('ExternCategoryId', $categoriesRow['categories_id']));

			$categoriesNode->appendChild($categoryNode);
		}

		$productNode->appendChild($categoriesNode);
		$descriptionsNode = $this->_xml->createElement('Descriptions');

		$query = $this->getProductsDescriptionByProductsIdQuery($productsRow['products_id']);
		$descriptionsQuery = xtc_db_query($query);

		while ($descriptionsRow = xtc_db_fetch_array($descriptionsQuery)) {
			if ($productsRow['manufacturers_name']) {
				$pos = stripos($descriptionsRow['products_name'], $productsRow['manufacturers_name']);
				if ($pos !== false) {
					$descriptionsRow['products_name'] = substr($descriptionsRow['products_name'], 0, $pos) .
														substr($descriptionsRow['products_name'], $pos + strlen($productsRow['manufacturers_name']) + 1);
				}
			}

			$descriptionsRow['products_name'] = $productsRow['manufacturers_name'] . ' ' . $descriptionsRow['products_name'];
			$descriptionNode = $this->_xml->createElement('Description');
			$descriptionNode->setAttribute('lang', $descriptionsRow['language']);
			$descriptionNode->appendChild($this->_xml->createElement('Title', $descriptionsRow['products_name']));

			$shortDescriptionNode = $this->_xml->createElement('ShortDescription');
			$shortDescriptionNode->appendChild($this->_xml->createCDATASection($descriptionsRow['products_short_description']));

			$descriptionNode->appendChild($shortDescriptionNode);

			$longDescriptionNode = $this->_xml->createElement('LongDescription');
			$longDescriptionNode->appendChild($this->_xml->createCDATASection($descriptionsRow['products_description']));

			$descriptionNode->appendChild($longDescriptionNode);
			$descriptionNode->appendChild($this->_xml->createElement('StockText'));
			$descriptionNode->appendChild($this->_xml->createElement('SearchKeys', $descriptionsRow['products_keywords']));

			$descriptionsNode->appendChild($descriptionNode);
		}

		$productNode->appendChild($descriptionsNode);
		$productNode->appendChild($this->_xml->createElement('ManufacturerExternId', $productsRow['manufacturers_id']));
		$productNode->appendChild($this->_xml->createElement('VendorExternId'));

		$imagesNode = $this->_xml->createElement('Images');

		if (!empty($productsRow['products_image'])) {
			$imageNode = $this->_xml->createElement('Image');
			$imageNode->setAttribute(main, '1');
			$imageNode->appendChild($this->_xml->createElement('Path', $this->getInfoImagesPath() . $productsRow['products_image']));
			$imageNode->appendChild($this->_xml->createElement('Name', $productsRow['products_image']));
			$imageNode->appendChild($this->_xml->createElement('PathBig', $this->getPopupImagesPath() . $productsRow['products_image']));
			$imageNode->appendChild($this->_xml->createElement('NameBig', $productsRow['products_image']));
			$imageNode->appendChild($this->_xml->createElement('Sort', '1'));

			$imagesNode->appendChild($imageNode);
		}

		$query = $this->getProductsImageByProductsIdQuery($productsRow['products_id']);
		$imagesQuery = xtc_db_query($query);

		$imagesCounter = 1;
		while ($imagesRow = xtc_db_fetch_array($imagesQuery)) {
			if (empty($imagesRow['image_name'])) {
				continue;
			}
			$imagesCounter++;
			$imageNode = $this->_xml->createElement('Image');
			$imageNode->setAttribute(main, '0');
			$imageNode->appendChild($this->_xml->createElement('Path', $this->getInfoImagesPath() . $imagesRow['image_name']));
			$imageNode->appendChild($this->_xml->createElement('Name', $imagesRow['image_name']));
			$imageNode->appendChild($this->_xml->createElement('PathBig', $this->getPopupImagesPath() . $imagesRow['image_name']));
			$imageNode->appendChild($this->_xml->createElement('NameBig', $imagesRow['image_name']));
			$imageNode->appendChild($this->_xml->createElement('Sort', $imagesCounter));
			$imagesNode->appendChild($imageNode);
		}

		$productNode->appendChild($imagesNode);

		$languagesNode = $this->_xml->createElement('Languages');
		$productNode->appendChild($languagesNode);

		$variationsNode = $this->_xml->createElement('Variations');

		$query = $this->getOptionsByProductsIdQuery($productsRow['products_id']);
		$optionsCountQuery = xtc_db_query($query);

		$variationsQuery = $this->getVariationsQuery($optionsCountQuery, $productsRow['products_id']);
		while ($variationsRow = xtc_db_fetch_array($variationsQuery)) {
			$variationNode = $this->_getVariationNode($productsRow, $variationsRow);
			$variationsNode->appendChild($variationNode);
		}

		$productNode->appendChild($variationsNode);
		return $productNode;
	}

	/**
	 * _getProductUpdateNode
	 *
	 * @param Array $productsRow
	 * @return Brickfox_Lib_DOMElement
	 */
	function _getProductUpdateNode($productsRow)
	{
		$priceGross = $productsRow['products_price'];

		$priceGross = $this->getPriceGrossIsNotBrutto($priceGross, $productsRow['tax_rate']);

		$productNode = $this->_xml->createElement('ProductUpdate');
		$productNode->setAttribute('id', $this->getOverallCounter());
		$productNode->appendChild($this->_xml->createElement('ProductExternId', $productsRow['products_id']));
		$productNode->appendChild($this->_xml->createElement('Active', $productsRow['products_status']));
		$productNode->appendChild($this->_xml->createElement('Available', $productsRow['products_status']));
		$productNode->appendChild($this->_xml->createElement('ThirdPartyStock', 0));

		$query = $this->getShippingStatusTranslationQuery($productsRow['products_shippingtime']);
		$shippingTimeQuery = xtc_db_query($query);
		$shippingTimeName = '';
		$shippingTime = xtc_db_fetch_array($shippingTimeQuery);
		if (isset($shippingTime['shipping_status_name'])) {
			$shippingTimeName = $shippingTime['shipping_status_name'];
		}
		$productNode->appendChild($this->_xml->createElement('DeliveryTime', $shippingTimeName));

		$productNode->appendChild($this->_xml->createElement('Stock', $productsRow['products_quantity']));

		$priceGrossNode = $this->_xml->createElement('PriceGross', $priceGross);
		$priceGrossNode->setAttribute('cur', DEFAULT_CURRENCY);
		$productNode->appendChild($priceGrossNode);


		$languagesNode = $this->_xml->createElement('Languages');
		$productNode->appendChild($languagesNode);

		$variationsNode = $this->_xml->createElement('Variations');

		$query = $this->getOptionsByProductsIdQuery($productsRow['products_id']);

		$optionsCountQuery = xtc_db_query($query);

		$variationsQuery = $this->getVariationsQuery($optionsCountQuery, $productsRow['products_id']);

		while ($variationsRow = xtc_db_fetch_array($variationsQuery)) {
			$variationsUpdateNode = $this->_getVariationUpdateNode($productsRow, $variationsRow);
			$variationsNode->appendChild($variationsUpdateNode);
		}

		$productNode->appendChild($variationsNode);

		return $productNode;
	}

	/**
	 * _getVariationNode()
	 *
	 * @param Array $productsRow
	 * @param Array $variationsRow
	 * @return Brickfox_Lib_DOMElement
	 */
	function _getVariationNode($productsRow, $variationsRow)
	{
		$priceGross = $productsRow['products_price'];

		for ($optionsCounter = 1; $optionsCounter <= $this->getOptionCounter(); $optionsCounter++) {
			if ($variationsRow['price_prefix_' . $optionsCounter] == '-') {
				$priceGross -= $variationsRow['options_values_price_' . $optionsCounter];
			} else {
				$priceGross += $variationsRow['options_values_price_' . $optionsCounter];
			}
		}

		$priceGross = $this->getPriceGrossIsNotBrutto($priceGross, $productsRow['tax_rate']);

		$variationNode = $this->_xml->createElement('Variation');
		$variationNode->appendChild($this->_xml->createElement('VariationExternId', $variationsRow['products_attributes_id']));

		if ($this->getOptionCounter() == 1) {
			$variationNode->appendChild($this->_xml->createElement('VariationItemNumber', $variationsRow['attributes_model']));
			$variationNode->appendChild($this->_xml->createElement('EAN', $variationsRow['gm_ean']));
		}
		else
		{
			$variationNode->appendChild($this->_xml->createElement('VariationItemNumber'));
			$variationNode->appendChild($this->_xml->createElement('EAN'));
		}

		$variationNode->appendChild($this->_xml->createElement('VariationActive', $productsRow['products_status']));
		$variationNode->appendChild($this->_xml->createElement('Available', $productsRow['products_status']));
		$variationNode->appendChild($this->_xml->createElement('ThirdPartyStock', 0));
		$variationNode->appendChild($this->_xml->createElement('Stock', $variationsRow['attributes_stock']));

		$priceGrossNode = $this->_xml->createElement('PriceGross', $priceGross);
		$priceGrossNode->setAttribute('cur', DEFAULT_CURRENCY);

		$variationNode->appendChild($priceGrossNode);

		$rrpNode = $this->_xml->createElement('Rrp');
		$rrpNode->setAttribute('cur', DEFAULT_CURRENCY);

		$variationNode->appendChild($rrpNode);

		$primeCostNode = $this->_xml->createElement('PrimeCost');
		$primeCostNode->setAttribute('cur', DEFAULT_CURRENCY);

		$variationNode->appendChild($primeCostNode);

		$optionsNode = $this->_xml->createElement('Options');

		$optionsValuesArray = array();
		$rowOptionsCounter = $this->getOptionCounter();
		for ($optionsCounter = 1; $optionsCounter <= $rowOptionsCounter; $optionsCounter++) {
			$optionNode = $this->_xml->createElement('Option');
			$optionNode->appendChild($this->_xml->createElement('OptionExternId'));
			$optionNode->appendChild($this->_xml->createElement('OptionValueExternId'));

			$translationsNode = $this->_xml->createElement('Translations');

			$query = $this->getOptionsTranslationByProductsIdQuery($variationsRow['options_id_' . $optionsCounter], $variationsRow['options_values_id_' . $optionsCounter]);
			$translationsQuery = xtc_db_query($query);

			while ($translationsRow = xtc_db_fetch_array($translationsQuery)) {
				$translationNode = $this->_xml->createElement('Translation');
				$translationNode->setAttribute('lang', $translationsRow['language']);
				$translationNode->appendChild($this->_xml->createElement('OptionName', $translationsRow['products_options_name']));
				$optionValue = '';
				$optionsValuesArray[$translationsRow['language']] .= ' ' . $translationsRow['products_options_values_name'];
				$optionValue = $translationsRow['products_options_values_name'];

				$translationNode->appendChild($this->_xml->createElement('OptionValue', $optionValue));
				$translationsNode->appendChild($translationNode);
			}

			$optionNode->appendChild($translationsNode);
			$optionsNode->appendChild($optionNode);
		}

		$variationNode->appendChild($optionsNode);

		$variationDescriptionsNode = $this->_xml->createElement('VariationDescriptions');

		$query = $this->getProductsDescriptionByProductsIdQuery($productsRow['products_id']);
		$descriptionsQuery = xtc_db_query($query);

		while ($descriptionsRow = xtc_db_fetch_array($descriptionsQuery)) {
			$variationDescriptionNode = $this->_xml->createElement('VariationDescription');
			$variationDescriptionNode->setAttribute('lang', $descriptionsRow['language']);
			$variationDescriptionNode->appendChild($this->_xml->createElement('VariationTitle', $descriptionsRow['products_name'] . $optionsValuesArray[$descriptionsRow['language']]));

			$shortDescriptionNode = $this->_xml->createElement('VariationShortDescription');
			$variationDescriptionNode->appendChild($shortDescriptionNode);
			$longDescriptionNode = $this->_xml->createElement('VariationLongDescription');
			$variationDescriptionNode->appendChild($longDescriptionNode);
			$variationDescriptionNode->appendChild($this->_xml->createElement('VariationStockText'));
			$variationDescriptionsNode->appendChild($variationDescriptionNode);
		}

		$variationNode->appendChild($variationDescriptionsNode);
		$variationNode->appendChild($this->_xml->createElement('VariationImages'));
		$variationNode->appendChild($this->_xml->createElement('VariationLanguages'));

		return $variationNode;
	}

	/**
	 * _getVariationUpdateNode
	 *
	 * @param Array $productsRow
	 * @param Array $variationsRow
	 * @return Brickfox_Lib_DOMElement
	 */
	function _getVariationUpdateNode($productsRow, $variationsRow)
	{

		$priceGross = $productsRow['products_price'];
		for ($optionsCounter = 1; $optionsCounter <= $this->getOptionCounter(); $optionsCounter++) {
			if ($variationsRow['price_prefix_' . $optionsCounter] == '-') {
				$priceGross -= $variationsRow['options_values_price_' . $optionsCounter];
			} else {
				$priceGross += $variationsRow['options_values_price_' . $optionsCounter];
			}
		}
		$priceGross = $this->getPriceGrossIsNotBrutto($priceGross, $productsRow['tax_rate']);

		$variationNode = $this->_xml->createElement('Variation');
		$variationNode->appendChild($this->_xml->createElement('VariationExternId', $variationsRow['products_attributes_id']));
		$variationNode->appendChild($this->_xml->createElement('VariationActive', $productsRow['products_status']));
		$variationNode->appendChild($this->_xml->createElement('Available', $productsRow['products_status']));
		$variationNode->appendChild($this->_xml->createElement('Stock', $variationsRow['attributes_stock']));
		$variationNode->appendChild($this->_xml->createElement('ThirdPartyStock', 0));

		$priceGrossNode = $this->_xml->createElement('PriceGross', $priceGross);
		$priceGrossNode->setAttribute('cur', DEFAULT_CURRENCY);

		$variationNode->appendChild($priceGrossNode);

		$variationNode->appendChild($this->_xml->createElement('VariationImages'));
		$variationNode->appendChild($this->_xml->createElement('VariationLanguages'));

		return $variationNode;
	}

	/**
	 * getVariationsQuery()
	 *
	 * @param xtc_db_query $optionsCountQuery
	 * @param int $productsId
	 * @return xtc_db_query
	 */
	function getVariationsQuery($optionsCountQuery, $productsId)
	{
		$optionsRow = xtc_db_fetch_array($optionsCountQuery);
		$variationsQuery = '';
		if ($optionsRow['options_id']) {
			$optionsCombinationsQueryStringFieldProductsAttributesId = 'pa1.products_attributes_id';

			$optionsCombinationsQueryStringFields = ',
				pa1.gm_ean,
				pa1.attributes_model,
				pa1.options_id as options_id_1,
				pa1.options_values_id as options_values_id_1,
				pa1.options_values_price as options_values_price_1,
				pa1.price_prefix as price_prefix_1';

			$optionsCombinationsQueryStringFrom = ' FROM ' . TABLE_PRODUCTS_ATTRIBUTES . ' pa1';

			$optionsCombinationsQueryStringWhere = ' WHERE pa1.products_id = ' . $productsId . ' AND pa1.options_id = ' . $optionsRow['options_id'];

			$this->resetOptionCounter();
			while ($optionsRow = xtc_db_fetch_array($optionsCountQuery)) {
				$this->incrementOptionCounter();
				$optionsCombinationsQueryStringFieldProductsAttributesId .= ',"-",pa' . $this->getOptionCounter() . '.products_attributes_id';

				$optionsCombinationsQueryStringFields .= ',pa' . $this->getOptionCounter() . '.options_id as options_id_' . $this->getOptionCounter() . ',
							  pa' . $this->getOptionCounter() . '.options_values_id as options_values_id_' . $this->getOptionCounter() . ',
							  pa' . $this->getOptionCounter() . '.options_values_price as options_values_price_' . $this->getOptionCounter() . ',
							  pa' . $this->getOptionCounter() . '.price_prefix as price_prefix_' . $this->getOptionCounter();

				$optionsCombinationsQueryStringFrom .= ' LEFT JOIN ' . TABLE_PRODUCTS_ATTRIBUTES . ' pa' . $this->getOptionCounter() . '
							ON pa' . $this->getOptionCounter() . '.products_id = pa1.products_id
							AND pa' . $this->getOptionCounter() . '.options_id = ' . $optionsRow['options_id'];
			}

			$query = 'SELECT CONCAT(' . $optionsCombinationsQueryStringFieldProductsAttributesId . ') as products_attributes_id' .
					 $optionsCombinationsQueryStringFields . ', pa1.attributes_stock' .
					 $optionsCombinationsQueryStringFrom .
					 $optionsCombinationsQueryStringWhere;

			$variationsQuery = xtc_db_query($query);
		} else {
			$variationsQuery = $optionsCountQuery;
		}

		return $variationsQuery;
	}

}

?>