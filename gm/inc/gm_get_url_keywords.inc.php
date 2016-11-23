<?php
/* --------------------------------------------------------------
   gm_get_url_keywords.inc.php 2011-04-28 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

	

function gm_get_categories_url_keywords($category_id, $language_id)
{
	if((int)$category_id === 0)
	{
		return '';
	}

	$category_query = xtc_db_query("select gm_url_keywords from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".(int)$category_id."' and language_id = '".(int)$language_id."'");
	$category = xtc_db_fetch_array($category_query);

	return $category['gm_url_keywords'];
}


function gm_get_products_url_keywords($products_id, $language_id)
{
	if((int)$products_id === 0)
	{
		return '';
	}

	$product_query = xtc_db_query("select gm_url_keywords from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".(int)$products_id."' and language_id = '".(int)$language_id."'");
	$product = xtc_db_fetch_array($product_query);

	return $product['gm_url_keywords'];
}
?>