<?php
/* --------------------------------------------------------------
	TrustBadgeCheckoutSuccessExtender.inc.php 2015-02-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class TrustBadgeCheckoutSuccessExtender extends TrustBadgeCheckoutSuccessExtender_parent
{
	public function proceed()
	{
		parent::proceed();
		$service = MainFactory::create_object('GMTSService');
		$tsid = $service->findRatingID($_SESSION['language_code']);
		$badge_snippet = $service->getBadgeSnippet($tsid);
		if($badge_snippet['enabled'] == true)
		{
			$this->html_output_array['TRUSTBADGE_CONFIRMATION_SNIPPET'] = $this->getConfirmationSnippet();
		}
	}

	protected function getConfirmationSnippet()
	{
		$max_days = $this->getMaxDeliveryDays($this->v_data_array['orders_id']);
		$estDeliveryDate = date('Y-m-d', strtotime('+ '.$max_days.' days'));
		$snippet =
			'<div id="trustedShopsCheckout" style="display: none;">'.PHP_EOL.
			'	<span id="tsCheckoutOrderNr">'.$this->v_data_array['orders_id'].'</span>'.PHP_EOL.
			'	<span id="tsCheckoutBuyerEmail">'.$this->v_data_array['coo_order']->customer['email_address'].'</span>'.PHP_EOL.
			'	<span id="tsCheckoutOrderAmount">'.number_format($this->v_data_array['coo_order']->info['pp_total'], 2, '.', '').'</span>'.PHP_EOL.
			'	<span id="tsCheckoutOrderCurrency">'.$this->v_data_array['coo_order']->info['currency'].'</span>'.PHP_EOL.
			'	<span id="tsCheckoutOrderPaymentType">'.$this->v_data_array['coo_order']->info['payment_method'].'</span>'.PHP_EOL;
		if($max_days !== false)
		{
			$snippet .= '	<span id="tsCheckoutOrderEstDeliveryDate">'.$estDeliveryDate.'</span>'.PHP_EOL;
		}
		foreach($this->v_data_array['coo_order']->products as $product)
		{
			//$snippet .= sprintf("\n<!--\n%s\n\n-->\n", print_r($product, true));
			$snippet .= $this->productSnippet($product);
		}
		$snippet .= '</div>';
		return $snippet;
	}

	protected function productSnippet($productArray)
	{
		$products_id        = $productArray['id'];
		$seoBoost           = $GLOBALS['gmSEOBoost']; // global instance from application_top
		$languageCode       = MainFactory::create('LanguageCode', MainFactory::create('StringType', $_SESSION['language_code']));
		$productReadService = StaticGXCoreLoader::getService('ProductRead');
		$product            = $productReadService->getProductById(MainFactory::create('IdType', $products_id));
		$productName        = $product->getName($languageCode);
		if($seoBoost->boost_products === true)
		{
			$productUrl = xtc_href_link($seoBoost->get_boosted_product_url($product->getProductId(), $productName, $_SESSION['languages_id']));
		}
		else
		{
			$productUrl = xtc_href_link('product_info.php', xtc_product_link($products_id, $productName));
		}

		$imageUrl = '';
		if($product->getPrimaryImage()->isVisible() === true)
		{
			$imageUrl = xtc_href_link('images/product_images/popup_images/' . $product->getPrimaryImage()->getFilename());
		}

		$mpn       = $product->getAddonValues()->keyExists('codeMpn')   ? $product->getAddonValues()->getValue('codeMpn')   : '';
		$brandName = $product->getAddonValues()->keyExists('brandName') ? $product->getAddonValues()->getValue('brandName') : '';

		$snippet = "    <span class=\"tsCheckoutProductItem\">\n".
		           "        <span class=\"tsCheckoutProductUrl\">%PRODUCT_URL%</span>\n".
		           "        <span class=\"tsCheckoutProductImageUrl\">%PRODUCT_IMAGE_URL%</span>\n".
		           "        <span class=\"tsCheckoutProductName\">%PRODUCT_NAME%</span>\n".
		           "        <span class=\"tsCheckoutProductSKU\">%PRODUCT_SKU%</span>\n".
		           "        <span class=\"tsCheckoutProductGTIN\">%PRODUCT_GTIN%</span>\n".
		           "        <span class=\"tsCheckoutProductMPN\">%PRODUCT_MPN%</span>\n".
		           "        <span class=\"tsCheckoutProductBrand\">%PRODUCT_BRAND%</span>\n".
		           "    </span>\n";

		$productsModel = $product->getProductModel();
		//$productsModel = $productArray['model'];
		$productsEan   = $product->getEan();

		$sku = (int)$products_id;
		if(!empty($productsModel))
		{
			$sku = $productsModel;
		}
		else if(!empty($productsEan))
		{
			$sku = $productsEan;
		}

		if(!empty($productArray['properties']))
		{
			$productsCombiEan = $this->getPropertiesCombiEan($productArray['orders_products_id']);
			if(!empty($productsCombiEan))
			{
				$productsEan = $productsCombiEan;
			}
		}

		$data = array(
			'%PRODUCT_URL%'       => $productUrl,
			'%PRODUCT_IMAGE_URL%' => $imageUrl,
			'%PRODUCT_NAME%'      => $productName,
			'%PRODUCT_SKU%'       => $sku,
			'%PRODUCT_GTIN%'      => $productsEan,
			'%PRODUCT_MPN%'       => $mpn,
			'%PRODUCT_BRAND%'     => $brandName,
		);

		$snippet = strtr($snippet, $data);

		return $snippet;
	}

	protected function getPropertiesCombiEan($orders_products_id)
	{
		$db       = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$oppRow   = $db->get_where('orders_products_properties', array('orders_products_id' => (int)$orders_products_id))->row();
		$combiId  = $oppRow->products_properties_combis_id;
		$combiRow = $db->get_where('products_properties_combis', array('products_properties_combis_id' => (int)$combiId))->row();
		$combiEan = $combiRow->combi_ean;
		return $combiEan;
	}

	protected function getMaxDeliveryDays($orders_id)
	{
		$query =
			'SELECT
				MAX(ss.number_of_days) AS max_days
			FROM
				products p
			LEFT JOIN
				`shipping_status` ss ON ss.shipping_status_id = p.products_shippingtime AND ss.language_id = \':language_id\'
			WHERE
				p.products_id IN (SELECT products_id FROM orders_products WHERE orders_id = \':orders_id\')';
		$query = strtr($query, array(':language_id' => $_SESSION['languages_id'], ':orders_id' => (int)$orders_id));
		$max_days = false;
		$result = xtc_db_query($query);
		while($row = xtc_db_fetch_array($result))
		{
			$max_days = (int)$row['max_days'];
		}
		return $max_days;
	}
}
