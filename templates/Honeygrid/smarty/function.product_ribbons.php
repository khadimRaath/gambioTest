<?php
/* --------------------------------------------------------------
   function.gm_footer.php 2016-05-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once DIR_FS_INC . 'xtc_get_tax_class_id.inc.php';

function smarty_function_product_ribbons($params, &$smarty)
{
	static $results;
	$key = $params['product_id'];

	if(isset($results[$key]))
	{
		$smarty->assign($params['out'], $results[$key]);
		return;
	}

	$arrResult = array();
	
	$arrResult['manufacturer'] = getManufacturersData($params);
	$arrResult['ribbons']      = array();
	
	$coo_text_mgr    = MainFactory::create_object('LanguageTextManager', array('general', $_SESSION['languages_id']),
	                                              true);
	$sectionArray = $coo_text_mgr->get_section_array();
	
	if(count($arrTemp = getDateAvailable($params)) > 0)
	{
		$arrResult['ribbons'][] = array(
			'class' => $arrTemp['class'],
			'text'  => $sectionArray['RIBBON_UPCOMING']
		);
	}
	
	if(count($arrTemp = getNew($params)) > 0)
	{
		$arrResult['ribbons'][] = array(
			'class' => $arrTemp['class'],
			'text'  => $sectionArray['RIBBON_NEW'],
		);
	}
	
	if(count($arrTemp = getRecommendation($params)) > 0)
	{
		$arrResult['ribbons'][] = array(
			'class' => $arrTemp['class'],
			'text'  => $sectionArray['RIBBON_TOP']
		);
	}
	
	if(count($arrTemp = getSpecials($params)) > 0)
	{
		$arrResult['ribbons'][] = array(
			'class' => $arrTemp['class'],
			'text'  => $arrTemp['text'] // $sectionArray['RIBBON_SPECIAL']
		);
	}

	$results[$key] = $arrResult;
	$smarty->assign($params['out'], $arrResult);
}

function getSoldOut($p_params)
{
	$arrTemp = array();
	
	if($p_params['showProductRibbons'] !== 'true')
	{
		return $arrTemp;
	}
	
	$strSql = "select * from products 
		where products_id = " . (int)$p_params['product_id'] . " 
		and products_quantity = 0 
		and gm_price_status = 0";
	$result = xtc_db_query($strSql);
	while($item = xtc_db_fetch_array($result))
	{
		$arrTemp['class'] = 'sold-out';
		$arrTemp['text']  = 'PRODUCT_RIBBON_SOLD_OUT';
	};
	
	return $arrTemp;
}

function getRecommendation($p_params)
{
	$arrTemp = array();
	
	if($p_params['showProductRibbons'] !== 'true')
	{
		return $arrTemp;
	}
	
	$strSql = "select * from products 
		where products_id = " . (int)$p_params['product_id'] . " 
		and products_startpage = 1";
	$result = xtc_db_query($strSql);
	while($item = xtc_db_fetch_array($result))
	{
		$arrTemp['class'] = 'recommendation';
		$arrTemp['text']  = 'PRODUCT_RIBBON_RECOMMENDATION';
	};
	
	return $arrTemp;
}

function getNew($p_params)
{
	$arrTemp = array();
	
	if($p_params['showProductRibbons'] !== 'true')
	{
		return $arrTemp;
	}
	
	$strNow = date('Y-m-d', mktime(1, 1, 1, date(m), date(d) - MAX_DISPLAY_NEW_PRODUCTS_DAYS, date(Y)));
	$strSql = "select * from products 
		where products_id = " . (int)$p_params['product_id'] . " 
		and products_date_added > '" . $strNow . "'";
	$result = xtc_db_query($strSql);
	while($item = xtc_db_fetch_array($result))
	{
		$arrTemp['class'] = 'new';
		$arrTemp['text']  = 'PRODUCT_RIBBON_NEW';
	};
	
	return $arrTemp;
}

function getDateAvailable($p_params)
{
	$arrTemp = array();
	
	if($p_params['showProductRibbons'] !== 'true')
	{
		return $arrTemp;
	}
	
	$strSql = "select * from products 
		where products_id = " . (int)$p_params['product_id'] . " 
		and products_date_available is not null 
		and products_date_available > NOW()";
	$result = xtc_db_query($strSql);
	while($item = xtc_db_fetch_array($result))
	{
		$arrTemp['class'] = 'available';
		$arrTemp['text']  = sprintf(PRODUCT_RIBBON_AVAILABLE, date("d.m.Y", mktime($item['products_date_available'])));
	};
	
	return $arrTemp;
}

function getSpecials($p_params)
{
	$arrTemp = array();
	
	if($p_params['showProductRibbons'] !== 'true')
	{
		return $arrTemp;
	}

	$xtPrice = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);

	$specialPrice = $xtPrice->xtcCheckSpecial($p_params['product_id']);
	$normalPrice = $xtPrice->getPprice($p_params['product_id']);
	
	if($specialPrice < $normalPrice && $specialPrice > 0)
	{
		$arrTemp['class'] = 'special';
		$arrTemp['text'] = ceil(round((1 - ($specialPrice / $normalPrice)) * -100, 1)) . '%';
	}
	
	return $arrTemp;
}

function getManufacturersData($p_params)
{
	$manufacturersDataArray = array();
	
	if($p_params['showManufacturerImages'] !== 'true')
	{
		return $manufacturersDataArray;
	}
	
	$productsDataArray = getManufacturersId($p_params['product_id']);
	$manufacturersId   = $productsDataArray['manufacturers_id'];
	
	if($manufacturersId > 0)
	{
		$query = '
			SELECT m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, i.manufacturers_url
			FROM manufacturers AS m
			INNER JOIN manufacturers_info AS i
			    ON (i.manufacturers_id = m.manufacturers_id AND i.languages_id = ' . (int)$_SESSION['languages_id'] . ')  
			WHERE m.manufacturers_id = ' . (int)$manufacturersId . ' 
				AND m.manufacturers_image != "" 
				AND m.manufacturers_image IS NOT NULL 
			GROUP BY m.manufacturers_id';
		
		$result = xtc_db_query($query);
		
		while($item = xtc_db_fetch_array($result))
		{
			$manufacturersDataArray[] = array(
				'ID'        => $item['manufacturers_id'],
				'NAME'      => $item['manufacturers_name'],
				'IMAGE'     => 'images/' . $item['manufacturers_image'],
				'IMAGE_ALT' => $item['manufacturers_name'],
				'URL'       => $item['manufacturers_url']
			);
		}
	}
	
	return $manufacturersDataArray;
}

function getManufacturersId($p_productId)
{
	$arrTemp = array();
	$strSql  = "select * from products where products_id = " . (int)$p_productId;
	$result  = xtc_db_query($strSql);
	while($objProduct = xtc_db_fetch_array($result))
	{
		$arrTemp = array(
			'manufacturers_id' => $objProduct['manufacturers_id'],
			'date_available'   => $objProduct['products_date_available']
		);
	}
	
	return $arrTemp;
}
