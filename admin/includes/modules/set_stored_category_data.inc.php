<?php
/* --------------------------------------------------------------
   set_stored_category_data.inc.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * This file is included in admin/categories.php for insert_- and update_category action
 */

$urlRewriteContentType = new NonEmptyStringType('category');
$urlRewriteContentId   = new IdType($category->getCategoryId());
$category->setUrlRewrites(MainFactory::create('UrlRewriteCollection', array()));
foreach($_POST['url_rewrites'] as $languageId => $url_rewrite)
{
	if($url_rewrite === '')
	{
		continue;
	}
	
	$languageId      = new IdType($languageId);
	$languageCode    = $languageProvider->getCodeById($languageId);
	$rewriteUrl      = new NonEmptyStringType($url_rewrite);
	$targetUrlString = 'index.php?cat=' . $urlRewriteContentId->asInt() . '&language='
	                   . strtolower($languageCode->asString());
	$targetUrl       = new NonEmptyStringType($targetUrlString);
	
	$urlRewrite = MainFactory::create('UrlRewrite', $urlRewriteContentType, $urlRewriteContentId, $languageId,
	                                  $rewriteUrl, $targetUrl);
	
	$category->setUrlRewrite($urlRewrite, $languageCode);
}