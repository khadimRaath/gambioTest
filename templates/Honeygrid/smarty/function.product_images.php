<?php
/* --------------------------------------------------------------
   function.product_images.php 2016-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function getImages($query, $name, $image)
{
	$images = array();
	$result = xtc_db_query($query);
	
	while($row = xtc_db_fetch_array($result))
	{
		$images[] = array(
			'IMAGE'         => 'images/product_images/thumbnail_images/' . $row['image_name'],
			'IMAGE_ALT'     => !empty($row['gm_alt_text']) ? $row['gm_alt_text'] : $name,
			'PRODUCTS_NAME' => $name
		);
	}
	
	if(count($images) > 0)
	{
		$images[] = array(
			'IMAGE'         => $image,
			'IMAGE_ALT'     => $name,
			'PRODUCTS_NAME' => $name
		);
	}
	
	return $images;
}

function smarty_function_product_images($params, &$smarty)
{
	$query  = 'SELECT 
					i.image_id, 
					i.image_name,
					a.gm_alt_text
				FROM ' . TABLE_PRODUCTS_IMAGES . ' i
				LEFT JOIN gm_prd_img_alt a ON (i.image_id = a.image_id AND 
												a.language_id = ' . (int)$_SESSION['languages_id'] . ')
				WHERE i.products_id = ' . (int)$params['product_id'] . ' 
				ORDER BY i.image_nr';
	$images = getImages($query, $params['p_name'], $params['p_image']);
	$smarty->assign($params['out'], $images);
}