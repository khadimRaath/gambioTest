<?php
/* --------------------------------------------------------------
   GMAltText.php 2015-06-29 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMAltText_ORIGIN
{
	function get_cat_alt($cat_id, $lang_id)
	{
		$gm_query = xtc_db_query("
									SELECT
										gm_alt_text
									FROM " . TABLE_CATEGORIES_DESCRIPTION . "
									WHERE
										categories_id = '" . (int)$cat_id . "'
									AND
										language_id = '" . (int)$lang_id . "'
								");
		$gm       = xtc_db_fetch_array($gm_query);

		return $gm['gm_alt_text'];
	}


	function get_form($image_id, $no, $pID)
	{
		$lang = gm_get_language();
		$alt_text = '';
		
		for($i = 0; $i < count($lang); $i++)
		{
			if($no == 0)
			{
				$gm_query = xtc_db_query("
											SELECT
												gm_alt_text
											FROM " . TABLE_PRODUCTS_DESCRIPTION . "
											WHERE
												products_id = '" . (int)$pID . "'
											AND
												language_id = '" . (int)$lang[$i]['id'] . "'
										");
				$gm       = xtc_db_fetch_array($gm_query);
			}
			else
			{
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
				$gm       = xtc_db_fetch_array($gm_query);
				$alt_text .= '' . '<input value="' . $gm['img_alt_id'] . '" type="hidden" name="gm_alt_id[' . $no . ']['
				             . $lang[$i]['id'] . ']" />';
			}
			
			$alt_text .= '<tr><td height="20" class="main" valign="top" align="left"><div class="gm_image_style">'
			             . GM_PRODUCTS_ALT_TEXT . '</div>' . '<input value="' . $gm['gm_alt_text']
			             . '" type="text" name="gm_alt_text[' . $no . '][' . $lang[$i]['id'] . ']" />&nbsp;'
			             . xtc_image(DIR_WS_LANGUAGES . $lang[$i]['directory'] . '/admin/images/' . $lang[$i]['image'])
			             . '</tr>';
		}

		return $alt_text;
	}


	function get_alt($image_id, $no, $pID)
	{
		if($no == 0)
		{
			$gm_query = xtc_db_query("
										SELECT
											gm_alt_text
										FROM " . TABLE_PRODUCTS_DESCRIPTION . "
										WHERE
											products_id = '" . (int)$pID . "'
										AND
											language_id = '" . (int)$_SESSION['languages_id'] . "'
									");
			$gm       = xtc_db_fetch_array($gm_query);
		}
		else
		{
			$gm_query = xtc_db_query("
										SELECT
											gm_alt_text,
											img_alt_id
										FROM
											gm_prd_img_alt
										WHERE
											image_id	= '" . (int)$image_id . "'
										AND
											language_id = '" . (int)$_SESSION['languages_id'] . "'
									");
			$gm       = xtc_db_fetch_array($gm_query);
		}

		return $gm['gm_alt_text'];
	}
}

MainFactory::load_origin_class('GMAltText');