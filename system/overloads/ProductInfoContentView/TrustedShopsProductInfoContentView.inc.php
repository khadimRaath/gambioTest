<?php
/* --------------------------------------------------------------
	TrustedShopsProductInfoContentView.inc.php 2016-09-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class TrustedShopsProductInfoContentView extends TrustedShopsProductInfoContentView_parent
{
	protected function _assignReviews()
	{
		parent::_assignReviews();
		$service = MainFactory::create_object('GMTSService');
		if($service->productreviews_summary_enabled == true)
		{
			$tsid = $service->findRatingID($_SESSION['language_code']);
			$snippet =
				"<div id=\"ts_product_widget\"></div>\n".
				"<script type=\"text/javascript\" src=\"//widgets.trustedshops.com/reviews/tsSticker/tsProductStickerSummary.js\"></script>\n".
				"<script>\n".
				"var summaryBadge = new productStickerSummary();\n".
				"summaryBadge.showSummary(\n".
				"	{\n".
				"		'tsId':              ':tsid',\n".
				"		'sku':               [':sku'],\n".
				"		'element':           '#ts_product_widget',\n".
				"		'starColor':         '#FFDC0F',\n".
				"		'starSize':          '14px',\n".
				"		'fontSize':          '12px',\n".
				"		'scrollToReviews':   false,\n".
				"		'showRating':        true,\n".
				"       'enablePlaceholder': true\n".
				"	}\n".
				");\n".
				"</script>\n";
			$snippet = strtr($snippet, array(
				':tsid' => $tsid,
				':sku' => $this->_getSKUForTrustedShops(),
			));
			$this->set_content_data('TS_PRODUCT_RATING', $snippet);
		}
	}

	protected function _assignDescription()
	{
		if(gm_get_conf('MODULE_CENTER_TRUSTEDSHOPS_INSTALLED') == true)
		{
			$service = MainFactory::create_object('GMTSService');
			if($service->productreviews_enabled == true)
			{
				$tsid = $service->findRatingID($_SESSION['language_code']);
				$this->product->data['products_description'] .= sprintf("\n[TAB:%s]\n%s\n", $service->get_text('product_reviews_tab_title'), $this->_getReviewSnippet($tsid));
			}
		}
		parent::_assignDescription();
	}

	protected function _getReviewSnippet($tsid)
	{
		$snippet = "<script type=\"text/javascript\">\n" .
		           "  _tsProductReviewsConfig = {\n" .
		           "	tsid: ':tsid',\n" .
		           "	sku: [':sku'],\n" .
		           "	variant: 'productreviews',\n" .
		           "	borderColor: '#0DBEDC',\n" .
		           "        locale: ':locale',\n" .
		           "        starColor: '#FFDC0F',\n" .
		           "        starSize: '15px',\n" .
		           "        ratingSummary: 'false',\n" .
		           "        maxHeight: '1200px',\n" .
		           "	introtext: 'What our customers say about us:'\n" .
		           "  };\n" .
		           "  var scripts = document.getElementsByTagName('SCRIPT'),\n" .
		           "  me = scripts[scripts.length - 1];\n" .
		           "  var _ts = document.createElement('SCRIPT');\n" .
		           "  _ts.type = 'text/javascript';\n" .
		           "  _ts.async = true;\n" .
		           "  _ts.charset = 'utf-8';\n" .
		           "  _ts.src ='//widgets.trustedshops.com/reviews/tsSticker/tsProductSticker.js';\n" .
		           "  me.parentNode.insertBefore(_ts, me);\n" .
		           "  _tsProductReviewsConfig.script = _ts;\n" .
		           "</script>\n";
		$snippet = strtr($snippet, array(
			':tsid' => $tsid,
			':sku' => $this->_getSKUForTrustedShops(),
			':locale' => $this->_getTrustedLocale(),
		));
		return $snippet;
	}

	protected function _getTrustedLocale()
	{
		switch($_SESSION['language_code'])
		{
			case 'en':
				$locale = 'en_GB';
				break;
			case 'es':
				$locale = 'es_ES';
				break;
			case 'fr':
				$locale = 'fr_FR';
				break;
			case 'pl':
				$locale = 'pl_PL';
				break;
			default:
				$locale = 'de_DE';
		}
		return $locale;
	}

	protected function _getSKUForTrustedShops()
	{
		$sku = $this->product->pID;
		if(!empty($this->product->data['products_model']))
		{
			$sku = $this->product->data['products_model'];
		}
		else if(!empty($this->product->data['products_ean']))
		{
			$sku = $this->product->data['products_ean'];
		}

		return $sku;
	}
}