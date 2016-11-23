<?php
/* --------------------------------------------------------------
   GMAltText.php 2015-09-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

class GMAltText_ORIGIN
{

	function __construct() {
		return;
	}

	// gm_prd_img_alt

	/**
	 * Get product's mo pic's image alt texts for all languages
	 * @param {integer} $productId
	 * @param {integer} $imageId
	 */
	function getMoPicAltText($productId, $imageId)
	{
		// Prepare DB input value
		$productId  = (int) $productId;
		$imageId 	= (int) $imageId;

		// Output array which will be filled
		$output = array();

		// Execute query and append values to `output`
		$query = '
			SELECT
				gm_alt_text,
				language_id
			FROM
				gm_prd_img_alt
			WHERE
				products_id = '.$productId.'
			AND
				image_id = '.$imageId.'
		';

		$result = xtc_db_query($query);

		while ($row = xtc_db_fetch_array($result)) {
			$output[$row['language_id']] = $row['gm_alt_text'];
		}

		return $output;
	}

	/**
	 * Get product's primary image alt texts for all languages
	 * @param {integer} $productId
	 */
	function getPrimaryImageAltText($productId)
	{
		// Prepare DB input value
		$productId = (int) $productId;

		// Output array which will be filled
		$output = array();

		// Execute query and append values to `output`
		$query = '
			SELECT
				gm_alt_text,
				language_id
			FROM
				'.TABLE_PRODUCTS_DESCRIPTION.'
			WHERE
				products_id = '.$productId.'
		';

		$result = xtc_db_query($query);

		while ($row = xtc_db_fetch_array($result)) {
			$output[$row['language_id']] = $row['gm_alt_text'];
		}

		return $output;
	}

	// Get categories alt text
	function get_cat_alt($cat_id, $lang_id) {
		$gm_query = xtc_db_query("
			SELECT
				gm_alt_text
			FROM " .
				TABLE_CATEGORIES_DESCRIPTION . "
			WHERE
				categories_id = '" . (int)$cat_id . "'
			AND
				language_id = '" .(int)$lang_id . "'
		");
		$gm = xtc_db_fetch_array($gm_query);
		return $gm['gm_alt_text'];
	}

	// Create form
	function get_form($image_id, $no, $pID) {
		$lang = xtc_get_languages();

		for($i = 0; $i < count($lang); $i++) {
			if($image_id == '') {
				$gm = array();
				$gm['gm_alt_text'] = '';
			} elseif ($no == 0) {
				$gm_query = xtc_db_query("
					SELECT
						gm_alt_text
					FROM " .
						TABLE_PRODUCTS_DESCRIPTION . "
					WHERE
						products_id = '" . (int)$pID . "'
					AND
						language_id = '" . (int)$lang[$i]['id'] . "'
				");
				$gm = xtc_db_fetch_array($gm_query);
			} else {
				$gm_query = xtc_db_query("
					SELECT
						gm_alt_text,
						img_alt_id
					FROM
						gm_prd_img_alt
					WHERE
						image_id	= '" . (int)$image_id . "'
					AND
						language_id = '" . (int)$lang[$i]['id'] . "'
				");
				$gm = xtc_db_fetch_array($gm_query);
				$alt_text .= '' . '<input value="' . $gm['img_alt_id'] . '" type="hidden" name="gm_alt_id[' . $no . '][' . $lang[$i]['id'] . ']" />';
			}
			$alt_text .=  '<tr><td height="20" class="main" valign="top" align="left"><div class="gm_image_style">' . GM_PRODUCTS_ALT_TEXT . '</div>' . '<input value="' . $gm['gm_alt_text'] . '" type="text" name="gm_alt_text[' . $no . '][' . $lang[$i]['id'] . ']" />&nbsp;'. xtc_image(DIR_WS_LANGUAGES.$lang[$i]['directory'].'/admin/images/'.$lang[$i]['image']) .'</tr>';
		}
		return $alt_text;
	}
}

MainFactory::load_origin_class('GMAltText');