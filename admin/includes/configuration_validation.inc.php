<?php
/* --------------------------------------------------------------
   configuration_validation.inc.php 2014-03-21 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------
*/

function validate_configuration_value($p_key, $p_value, $p_return_error_message = true)
{
	$t_validity = true;
	$t_error_message = '';
	
	switch($p_key)
	{
		case 'IMAGE_QUALITY':
			if (!is_numeric($p_value) || $p_value < 0 || $p_value > 100)
			{
				$t_validity = false;
				$t_error_message = ERROR_IMAGE_QUALITY;
			}
			break;
		case 'MO_PICS':
			if (!is_numeric($p_value) || $p_value < 0)
			{
				$t_validity = false;
				$t_error_message = ERROR_MO_PICS;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_WIDTH':
			if (!is_numeric($p_value) || $p_value < 0)
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_WIDTH;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_HEIGHT':
			if (!is_numeric($p_value) || $p_value < 0)
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_HEIGHT;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_WIDTH':
			if (!is_numeric($p_value) || $p_value < 0)
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_WIDTH;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_HEIGHT':
			if (!is_numeric($p_value) || $p_value < 0)
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_HEIGHT;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_WIDTH':
			if (!is_numeric($p_value) || $p_value < 0)
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_WIDTH;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_HEIGHT':
			if (!is_numeric($p_value) || $p_value < 0)
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_HEIGHT;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_BEVEL':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_BEVEL;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_GREYSCALE':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]{1,3}\s*,\s*[0-9]{1,3}\s*,\s*[0-9]{1,3}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_GREYSCALE;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_ELLIPSE':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_ELLIPSE;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_ROUND_EDGES':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9]+\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_ROUND_EDGES;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_MERGE':
			if (!empty($p_value) && !preg_match('/^\(\s*.+\.[0-9a-zA-Z]+\s*,\s*-*[0-9]+\s*,\s*-*[0-9]+\s*,\s*-*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_MERGE;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_FRAME':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9]+\s*(,\s*[0-9a-fA-F]{6}\s*|)\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_FRAME;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_DROP_SHADDOW':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_DROP_SHADDOW;
			}
			break;
		case 'PRODUCT_IMAGE_THUMBNAIL_MOTION_BLUR':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_THUMBNAIL_MOTION_BLUR;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_BEVEL':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_BEVEL;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_GREYSCALE':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]{1,3}\s*,\s*[0-9]{1,3}\s*,\s*[0-9]{1,3}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_GREYSCALE;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_ELLIPSE':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_ELLIPSE;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_ROUND_EDGES':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9]+\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_ROUND_EDGES;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_MERGE':
			if (!empty($p_value) && !preg_match('/^\(\s*.+\.[0-9a-zA-Z]+\s*,\s*-*[0-9]+\s*,\s*-*[0-9]+\s*,\s*-*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_MERGE;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_FRAME':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9]+\s*(,\s*[0-9a-fA-F]{6}\s*|)\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_FRAME;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_DROP_SHADDOW':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_DROP_SHADDOW;
			}
			break;
		case 'PRODUCT_IMAGE_INFO_MOTION_BLUR':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_INFO_MOTION_BLUR;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_BEVEL':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_BEVEL;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_GREYSCALE':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]{1,3}\s*,\s*[0-9]{1,3}\s*,\s*[0-9]{1,3}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_GREYSCALE;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_ELLIPSE':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_ELLIPSE;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_ROUND_EDGES':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9]+\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_ROUND_EDGES;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_MERGE':
			if (!empty($p_value) && !preg_match('/^\(\s*.+\.[0-9a-zA-Z]+\s*,\s*-*[0-9]+\s*,\s*-*[0-9]+\s*,\s*-*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_MERGE;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_FRAME':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9]+\s*(,\s*[0-9a-fA-F]{6}\s*|)\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_FRAME;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_DROP_SHADDOW':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_DROP_SHADDOW;
			}
			break;
		case 'PRODUCT_IMAGE_POPUP_MOTION_BLUR':
			if (!empty($p_value) && !preg_match('/^\(\s*[0-9]+\s*,\s*[0-9a-fA-F]{6}\s*\)$/', $p_value))
			{
				$t_validity = false;
				$t_error_message = ERROR_PRODUCT_IMAGE_POPUP_MOTION_BLUR;
			}
			break;
		case 'SHIPPING_MAX_WEIGHT':
			if (!is_numeric($p_value) || $p_value < 0)
			{
				$t_validity = false;
				$t_error_message = ERROR_MO_PICS;
			}
			break;
		default:
			break;
	}
	
	if (!$t_validity)
	{
		$_SESSION['configuration_validation_error_values'][$p_key] = $p_value;
	}
	else
	{
		unset($_SESSION['configuration_validation_error_values'][$p_key]);
	}
	
	$t_error_message = ($p_return_error_message && !$t_validity) ? '<tr><td class="dataTableContent_gm error">' . $t_error_message . '</td></tr>' : '';
	
	return ($p_return_error_message) ? $t_error_message : $t_validity;
}