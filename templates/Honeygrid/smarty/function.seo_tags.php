<?php
/* --------------------------------------------------------------
   function.seo_tags.php 2016-06-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Smarty plugin
 * @package    Smarty
 * @subpackage plugins
 */

/**
 * @param $params
 * @param $smarty
 *
 * @return string
 */
function smarty_function_seo_tags($params, &$smarty)
{
	$seoBoost     = MainFactory::create('GMSEOBoost');
	$languageCode = new LanguageCode(new StringType(strtoupper($_SESSION['language_code'])));
	$url          = '';
	$html         = '';
	$robots       = 'index,follow';
	
	$requestUri = gm_get_env_info('REQUEST_URI');
	$getParams  = parse_url($requestUri, PHP_URL_QUERY);
	parse_str($getParams, $getArray);
	$getArray = array_keys($getArray);
	
	$languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
	$activeCodes      = $languageProvider->getActiveCodes();
	
	$noIndexKeys = array(
		'feature_categories_id',
		'filter_categories_id',
		'filter_fv_id',
		'filter_id',
		'filter_price_max',
		'filter_price_min',
		'keywords',
		'listing_count',
		'listing_sort',
		'page',
		'value_conjunction'
	);
	
	$noRelPrevNext = array(
		'feature_categories_id',
		'filter_categories_id',
		'filter_fv_id',
		'filter_id',
		'filter_price_max',
		'filter_price_min',
		'listing_count',
		'listing_sort',
		'value_conjunction'
	);
	
	foreach($noIndexKeys as $key)
	{
		if(in_array($key, $getArray))
		{
			$robots = 'noindex,follow';
			break;
		}
	}
	
	if(isset($GLOBALS['actual_products_id']) && !empty($GLOBALS['actual_products_id']))
	{
		/** @var ProductReadService $productReadService */
		$productReadService = StaticGXCoreLoader::getService('ProductRead');
		$product            = $productReadService->getProductById(new IdType((int)$GLOBALS['actual_products_id']));
		
		if($seoBoost->boost_products)
		{
			$url = xtc_href_link($seoBoost->get_boosted_product_url($GLOBALS['actual_products_id']));
		}
		else
		{
			try
			{
				$productLinkParams = xtc_product_link($GLOBALS['actual_products_id'], $product->getName($languageCode),
				                                      $product->getUrlKeywords($languageCode),
				                                      $_SESSION['languages_id']);
			}
			catch(InvalidArgumentException $e)
			{
				$productLinkParams = xtc_product_link($GLOBALS['actual_products_id'], '', '',
				                                      $_SESSION['languages_id']);
			}
			
			$url = xtc_href_link(FILENAME_DEFAULT, $productLinkParams);
		}
		
		if($robots === 'index,follow' && $activeCodes->count() > 1)
		{
			foreach($activeCodes as $code)
			{
				$langCode   = new LanguageCode(new StringType($code->asString()));
				$languageId = $languageProvider->getIdByCode($langCode);
				
				if($languageId != $_SESSION['languages_id'])
				{
					if($seoBoost->boost_products)
					{
						$altUrl = xtc_href_link($seoBoost->get_boosted_product_url($GLOBALS['actual_products_id'], '',
						                                                           $languageId));
					}
					else
					{
						try
						{
							$productLinkParams = xtc_product_link($GLOBALS['actual_products_id'],
							                                      $product->getName($langCode),
							                                      $product->getUrlKeywords($langCode), $languageId);
						}
						catch(InvalidArgumentException $e)
						{
							continue;
						}
						
						$altUrl = xtc_href_link(FILENAME_DEFAULT, $productLinkParams);
					}
					
					if($altUrl !== $url)
					{
						if($html === '')
						{
							$html .= '<link rel="alternate" hreflang="' . strtolower($languageCode) . '" href="'
							         . htmlspecialchars($url) . '" />' . "\n\t\t";
						}
						
						$html .= '<link rel="alternate" hreflang="' . strtolower($langCode) . '" href="'
						         . htmlspecialchars($altUrl) . '" />' . "\n\t\t";
					}
				}
			}
		}
	}
	elseif(isset($_GET['cat']) && isset($GLOBALS['current_category_id']) && !empty($GLOBALS['current_category_id']))
	{
		/** @var CategoryReadService $categoryReadService */
		$categoryReadService = StaticGXCoreLoader::getService('CategoryRead');
		$category            = $categoryReadService->getCategoryById(new IdType((int)$GLOBALS['current_category_id']));
		
		if($seoBoost->boost_categories)
		{
			$url = xtc_href_link($seoBoost->get_boosted_category_url($GLOBALS['current_category_id']));
		}
		else
		{
			try
			{
				$categoryLinkParams = xtc_category_link($GLOBALS['current_category_id'],
				                                        $category->getName($languageCode),
				                                        $category->getUrlKeywords($languageCode),
				                                        $_SESSION['languages_id']);
			}
			catch(InvalidArgumentException $e)
			{
				$categoryLinkParams = xtc_category_link($GLOBALS['current_category_id'], '', '', 
				                                        $_SESSION['languages_id']);
			}
			
			$url = xtc_href_link(FILENAME_DEFAULT, $categoryLinkParams);
		}
		
		if($robots === 'index,follow' && $activeCodes->count() > 1)
		{
			foreach($activeCodes as $code)
			{
				$langCode   = new LanguageCode(new StringType($code->asString()));
				$languageId = $languageProvider->getIdByCode($langCode);
				
				if($languageId != $_SESSION['languages_id'])
				{
					if($seoBoost->boost_categories)
					{
						$altUrl = xtc_href_link($seoBoost->get_boosted_category_url($GLOBALS['current_category_id'],
						                                                            $languageId));
					}
					else
					{
						try
						{
							$categoryLinkParams = xtc_category_link($GLOBALS['actual_products_id'],
							                                        $category->getName($langCode),
							                                        $category->getUrlKeywords($langCode), $languageId);
						}
						catch(InvalidArgumentException $e)
						{
							continue;
						}
						
						$altUrl = xtc_href_link(FILENAME_DEFAULT, $categoryLinkParams);
					}
					
					if($altUrl !== $url)
					{
						if($html === '')
						{
							$html .= '<link rel="alternate" hreflang="' . strtolower($languageCode) . '" href="'
							         . htmlspecialchars($url) . '" />' . "\n\t\t";
						}
						
						$html .= '<link rel="alternate" hreflang="' . strtolower($langCode) . '" href="'
						         . htmlspecialchars($altUrl) . '" />' . "\n\t\t";
					}
				}
			}
		}
		
		$relNextPrev = '';
		if(!empty($_SESSION['relPrevUrl']))
		{
			$relNextPrev .= '<link rel="prev" href="' . htmlspecialchars($_SESSION['relPrevUrl']) . '" />' . "\n\t\t";
		}
		
		if(!empty($_SESSION['relNextUrl']))
		{
			$relNextPrev .= '<link rel="next" href="' . htmlspecialchars($_SESSION['relNextUrl']) . '" />' . "\n\t\t";
		}
		
		foreach($noRelPrevNext as $key)
		{
			if(in_array($key, $getArray))
			{
				$relNextPrev = '';
				break;
			}
		}
		
		if($relNextPrev !== '' && $robots === 'noindex,follow')
		{
			$url  = '';
			$html = '';
		}
		
		$html .= $relNextPrev;
	}
	elseif(isset($_GET['coID']) && $activeCodes->count() > 1)
	{
		$coID = (int)$_GET['coID'];
		
		if($seoBoost->boost_content)
		{
			$contentId = $seoBoost->get_content_id_by_content_group($coID);
			$url       = xtc_href_link($seoBoost->get_boosted_content_url($contentId));
		}
		else
		{
			$url = xtc_href_link(FILENAME_CONTENT, 'coID=' . $coID);
		}
		
		if($robots === 'index,follow' && $activeCodes->count() > 1)
		{
			foreach($activeCodes as $code)
			{
				$langCode   = new LanguageCode(new StringType($code->asString()));
				$languageId = $languageProvider->getIdByCode($langCode);
				
				if($languageId != $_SESSION['languages_id'])
				{
					if($seoBoost->boost_content)
					{
						$contentId = $seoBoost->get_content_id_by_content_group($coID, $languageId);
						$altUrl    = xtc_href_link($seoBoost->get_boosted_content_url($contentId, $languageId));
					}
					else
					{
						$altUrl = xtc_href_link(FILENAME_CONTENT, 'coID=' . $coID);
					}
					
					if($altUrl !== $url)
					{
						if($html === '')
						{
							$html .= '<link rel="alternate" hreflang="' . strtolower($languageCode) . '" href="'
							         . htmlspecialchars($url) . '" />' . "\n\t\t";
						}
						
						$html .= '<link rel="alternate" hreflang="' . strtolower($langCode) . '" href="'
						         . htmlspecialchars($altUrl) . '" />' . "\n\t\t";
					}
				}
			}
		}
	}
	
	if($url !== '')
	{
		$html .= '<link rel="canonical" href="' . $url . '" />';
	}
	
	$html = '<meta name="robots" content="' . $robots . '" />' . "\n\t\t" . $html;
	
	return $html;
}