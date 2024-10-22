<?php
/* --------------------------------------------------------------
  general.php 2016-08-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(general.php,v 1.156 2003/05/29); www.oscommerce.com
  (c) 2003	 nextcommerce (general.php,v 1.35 2003/08/1); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: general.php 1316 2005-10-21 15:30:58Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------
  Third Party contributions:

  Customers Status v3.x (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Enable_Disable_Categories 1.3                Autor: Mikel Williams | mikel@ladykatcostumes.com

  Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  -------------------------------------------------------------- */
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

function clear_string($value)
{

	$html = str_replace("'", '', $value);
	$html = str_replace(')', '', $html);
	$html = str_replace('(', '', $html);
	$array = explode(',', $html);
	foreach($array as $key => $value)
	{
		$array[$key] = trim($value);
	}
	return $array;
}

function check_stock($products_id)
{
	$products_id = (int)$products_id;
	unset($stock_flag);
	$stock_query = xtc_db_query("SELECT products_quantity FROM " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
	$stock_values = xtc_db_fetch_array($stock_query);
	// BOF GM_MOD
	if($stock_values['products_quantity'] <= '0')
	{
		$stock_flag = 'true';
		$stock_warn = TEXT_WARN_MAIN;
	}
	$attribute_stock_query = xtc_db_query("SELECT DISTINCT attributes_stock, options_values_id FROM " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . $products_id . "' and attributes_stock <= 0");
	while($attribute_stock_values = xtc_db_fetch_array($attribute_stock_query))
	{
		$stock_flag = 'true';
		$which_attribute_query = xtDBquery("SELECT products_options_values_name FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE products_options_values_id = '" . $attribute_stock_values['options_values_id'] . "' AND language_id = '" . $_SESSION['languages_id'] . "'");
		$which_attribute = xtc_db_fetch_array($which_attribute_query);
		if(!isset($stock_warn))
			$stock_warn .= $which_attribute['products_options_values_name'];
		else
			$stock_warn .= ', ' . $which_attribute['products_options_values_name'];
	}
	
	$stock_warn = rtrim($stock_warn, ','); // Make sure there's no trailing comma.
	
	// EOF GM_MOD
	if($stock_flag == 'true' && $products_id != '')
	{		
		$displayed_stock_warn = (!empty($attribute_stock_query) && xtc_db_num_rows($attribute_stock_query) > 3) 
			? implode(',', array_slice(explode(',', $stock_warn), 0, 3)) . '...' 
			: $stock_warn; 
		
		return '<div class="stock_warn" data-complete-stock-string="' . $stock_warn . '">' . $displayed_stock_warn . '</div>';
	}
	else
	{
		return (int)$stock_values['products_quantity'];
	}
}

// Set Categorie Status
function xtc_set_categories_status($categories_id, $status)
{
	$categories_id = (int)$categories_id;
	
	if($status == '1')
	{
		return xtc_db_query("update " . TABLE_CATEGORIES . " set categories_status = '1' where categories_id = '" . $categories_id . "'");
	}
	elseif($status == '0')
	{
		return xtc_db_query("update " . TABLE_CATEGORIES . " set categories_status = '0' where categories_id = '" . $categories_id . "'");
	}
	else
	{
		return -1;
	}
}

function xtc_set_groups($categories_id, $permission_array)
{
	$categories_id = (int)$categories_id;
	
	// BOF GM_MOD
	if(!empty($permission_array))
	{
		// EOF GM_MOD
		// get products in categorie
		$products_query = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id='" . $categories_id . "'");
		while($products = xtc_db_fetch_array($products_query))
		{
			xtc_db_perform(TABLE_PRODUCTS, $permission_array, 'update', 'products_id = \'' . $products['products_id'] . '\'');
		}
		// set status of categorie
		xtc_db_perform(TABLE_CATEGORIES, $permission_array, 'update', 'categories_id = \'' . $categories_id . '\'');
		// look for deeper categories and go rekursiv
		$categories_query = xtc_db_query("SELECT categories_id FROM " . TABLE_CATEGORIES . " where parent_id='" . $categories_id . "'");
		while($categories = xtc_db_fetch_array($categories_query))
		{
			xtc_set_groups($categories['categories_id'], $permission_array);
		}
		// BOF GM_MOD:
	}
}

// Set Admin Access Rights
function xtc_set_admin_access($fieldname, $status, $cID)
{
	$cID = (int)$cID;
	
	if($status == '1')
	{
		return xtc_db_query("update " . TABLE_ADMIN_ACCESS . " set " . $fieldname . " = '1' where customers_id = '" . $cID . "'");
	}
	else
	{
		return xtc_db_query("update " . TABLE_ADMIN_ACCESS . " set " . $fieldname . " = '0' where customers_id = '" . $cID . "'");
	}
}

// Check whether a referer has enough permission to open an admin page
function xtc_check_permission($pagename)
{
	if($pagename != 'index')
	{
		$access_permission_query = xtc_db_query("select " . $pagename . " from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_SESSION['customer_id'] . "'");
		$access_permission = xtc_db_fetch_array($access_permission_query);

		if(($_SESSION['customers_status']['customers_status_id'] == '0') && ($access_permission[$pagename] == '1'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		$t_message = 'Permission denied.';
		LogControl::get_instance()->notice($t_message, 'security', 'security');
		xtc_redirect(xtc_href_link(FILENAME_LOGIN));
	}
}

////
// Redirect to another page or site
function xtc_redirect($url)
{
	header('Location: ' . $url);

	LogControl::get_instance()->get_stop_watch()->stop();
	LogControl::get_instance()->write_time_log();

	exit;
}

function xtc_customers_name($customers_id)
{
	$customers_id = (int)$customers_id;
	$customers = xtc_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $customers_id . "'");
	$customers_values = xtc_db_fetch_array($customers);

	return $customers_values['customers_firstname'] . ' ' . $customers_values['customers_lastname'];
}

function xtc_get_path($current_category_id = '')
{
	global $cPath_array;

	if($current_category_id == '')
	{
		$cPath_new = implode('_', $cPath_array);
	}
	else
	{
		if(sizeof($cPath_array) == 0)
		{
			$cPath_new = $current_category_id;
		}
		else
		{
			$cPath_new = '';
			$last_category_query = xtc_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_array[(sizeof($cPath_array) - 1)] . "'");
			$last_category = xtc_db_fetch_array($last_category_query);
			$current_category_query = xtc_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
			$current_category = xtc_db_fetch_array($current_category_query);
			if($last_category['parent_id'] == $current_category['parent_id'])
			{
				for($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++)
				{
					$cPath_new .= '_' . $cPath_array[$i];
				}
			}
			else
			{
				for($i = 0, $n = sizeof($cPath_array); $i < $n; $i++)
				{
					$cPath_new .= '_' . $cPath_array[$i];
				}
			}
			$cPath_new .= '_' . $current_category_id;
			if(substr($cPath_new, 0, 1) == '_')
			{
				$cPath_new = substr($cPath_new, 1);
			}
		}
	}

	return 'cPath=' . $cPath_new;
}

function xtc_get_all_get_params($exclude_array = '')
{

	if($exclude_array == '')
		$exclude_array = array();

	$get_url = '';

	reset($_GET);

	foreach($_GET as $key => $value)
	{
		if(($key != session_name()) && ($key != 'error') && (!xtc_in_array($key, $exclude_array)))
			$get_url .= strip_slashes_from_url($value, htmlspecialchars_wrapper($key));
	}

	return $get_url;
}

function strip_slashes_from_url($p_value, $p_key)
{
	$t_return = '';

	if(is_string($p_value))
	{
		$t_return .= $p_key . '=' . stripslashes(htmlspecialchars_wrapper($p_value)) . '&';
	}
	elseif(is_array($p_value))
	{
		foreach($p_value as $t_key => $t_value)
		{
			$t_return .= $p_key . '[' . $t_key . ']' . strip_slashes_from_url($t_value, '');
		}
	}
	else
	{
		$t_return .= $p_key . '=' . htmlspecialchars_wrapper((string)$p_value) . '&';
	}

	return $t_return;
}

function xtc_date_long($raw_date)
{
	if($raw_date === '0000-00-00 00:00:00' || $raw_date === '1000-01-01 00:00:00' || $raw_date == '')
		return false;

	$year = (int)substr($raw_date, 0, 4);
	$month = (int)substr($raw_date, 5, 2);
	$day = (int)substr($raw_date, 8, 2);
	$hour = (int)substr($raw_date, 11, 2);
	$minute = (int)substr($raw_date, 14, 2);
	$second = (int)substr($raw_date, 17, 2);

	return utf8_encode_wrapper(strftime(DATE_FORMAT_LONG, mktime($hour, $minute, $second, $month, $day, $year)));
}

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
function xtc_date_short($raw_date)
{
	if($raw_date === '0000-00-00 00:00:00' || $raw_date === '1000-01-01 00:00:00' || $raw_date == '')
		return false;

	$year = substr($raw_date, 0, 4);
	$month = (int)substr($raw_date, 5, 2);
	$day = (int)substr($raw_date, 8, 2);
	$hour = (int)substr($raw_date, 11, 2);
	$minute = (int)substr($raw_date, 14, 2);
	$second = (int)substr($raw_date, 17, 2);

	if(@ date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year)
	{
		return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
	}
	else
	{
		return preg_replace('/2037' . '$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));
	}
}

function xtc_datetime_short($raw_datetime)
{
	if($raw_datetime === '0000-00-00 00:00:00' || $raw_datetime === '1000-01-01 00:00:00' || $raw_datetime == '')
		return false;

	$year = (int)substr($raw_datetime, 0, 4);
	$month = (int)substr($raw_datetime, 5, 2);
	$day = (int)substr($raw_datetime, 8, 2);
	$hour = (int)substr($raw_datetime, 11, 2);
	$minute = (int)substr($raw_datetime, 14, 2);
	$second = (int)substr($raw_datetime, 17, 2);

	return strftime(DATE_TIME_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
}

function xtc_array_merge($array1, $array2, $array3 = '')
{
	if(!is_array($array1))
	{
		$array1 = array();
	}
	if(!is_array($array2))
	{
		$array2 = array();
	}
	if(!is_array($array3))
	{
		$array3 = array();
	}
	if(function_exists('array_merge'))
	{
		$array_merged = array_merge($array1, $array2, $array3);
	}
	else
	{
		while(list ($key, $val) = each($array1))
			$array_merged[$key] = $val;
		while(list ($key, $val) = each($array2))
			$array_merged[$key] = $val;
		if(sizeof($array3) > 0)
			while(list ($key, $val) = each($array3))
				$array_merged[$key] = $val;
	}

	return (array)$array_merged;
}

function xtc_in_array($lookup_value, $lookup_array)
{
	if(function_exists('in_array'))
	{
		if(in_array($lookup_value, $lookup_array))
			return true;
	} else
	{
		reset($lookup_array);
		while(list ($key, $value) = each($lookup_array))
		{
			if($value == $lookup_value)
				return true;
		}
	}

	return false;
}

function xtc_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false)
{

	if(!is_array($category_tree_array))
		$category_tree_array = array();
	if((sizeof($category_tree_array) < 1) && ($exclude != '0'))
		$category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

	if($include_itself)
	{
		$category_query = xtc_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . (int)$_SESSION['languages_id'] . "' and cd.categories_id = '" . (int)$parent_id . "'");
		$category = xtc_db_fetch_array($category_query);
		$category_tree_array[] = array('id' => $parent_id, 'text' => htmlspecialchars_wrapper($category['categories_name']));
	}

	$categories_query = xtc_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
	while($categories = xtc_db_fetch_array($categories_query))
	{
		if($exclude != $categories['categories_id'])
			$category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . htmlspecialchars_wrapper($categories['categories_name']));
		$category_tree_array = xtc_get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
	}

	return $category_tree_array;
}

function xtc_draw_products_pull_down($name, $parameters = '', $exclude = '')
{
	global $currencies, $xtPrice;

	if($exclude == '')
	{
		$exclude = array();
	}
	$select_string = '<select name="' . $name . '"';
	if($parameters)
	{
		$select_string .= ' ' . $parameters;
	}
	$select_string .= '>';
	$products_query = xtc_db_query("select p.products_id, pd.products_name,p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' order by products_name");
	while($products = xtc_db_fetch_array($products_query))
	{
		if(!xtc_in_array($products['products_id'], $exclude))
		{
			//brutto admin:
			if(PRICE_IS_BRUTTO == 'true')
			{
				$products['products_price'] = xtc_round($products['products_price'] * ((100 + xtc_get_tax_rate($products['products_tax_class_id'])) / 100), PRICE_PRECISION);
			}
			$select_string .= '<option value="' . $products['products_id'] . '">' . $products['products_name'] . ' (' . trim($xtPrice->xtcFormat(xtc_round($products['products_price'], PRICE_PRECISION), true)) . ')</option>';
		}
	}
	$select_string .= '</select>';

	return $select_string;
}

function xtc_options_name($options_id)
{

	$options = xtc_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . (int)$options_id . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'");
	$options_values = xtc_db_fetch_array($options);

	return $options_values['products_options_name'];
}

function xtc_values_name($values_id)
{

	$values = xtc_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . (int)$values_id . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'");
	$values_values = xtc_db_fetch_array($values);

	return $values_values['products_options_values_name'];
}

function xtc_info_image($image, $alt, $width = '', $height = '', $params = '')
{
	if(($image) && (file_exists(DIR_FS_CATALOG_IMAGES . $image)))
	{
		$image = xtc_image(DIR_WS_CATALOG_IMAGES . $image, $alt, $width, $height, $params);
	}
	else
	{
		$image = TEXT_IMAGE_NONEXISTENT;
	}

	return $image;
}

function xtc_info_image_c($image, $alt, $width = '', $height = '', $params = '')
{
	if(($image) && (file_exists(DIR_FS_CATALOG_IMAGES . 'categories/' . $image)))
	{
		$image = xtc_image(DIR_WS_CATALOG_IMAGES . 'categories/' . $image, $alt, $width, $height, $params);
	}
	else
	{
		$image = TEXT_IMAGE_NONEXISTENT;
	}

	return $image;
}

function xtc_product_thumb_image($image, $alt, $width = '', $height = '', $params = '')
{
	if(($image) && (file_exists(DIR_FS_CATALOG_THUMBNAIL_IMAGES . $image)))
	{
		$image = xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES . $image, $alt, $width, $height, $params);
	}
	else
	{
		$image = TEXT_IMAGE_NONEXISTENT;
	}

	return $image;
}

function xtc_break_string($p_string, $p_length, $p_break_char = '-')
{
	$l = 0;
	$t_output = '';

	if(function_exists('mb_strlen'))
	{
		$t_string_length = mb_strlen($p_string, 'utf-8');
	}
	else
	{
		$t_string_length = strlen($p_string);
	}

	for($i = 0; $i < $t_string_length; $i++)
	{
		if(function_exists('mb_substr'))
		{
			$t_char = mb_substr($p_string, $i, 1, 'utf-8');
		}
		else
		{
			$t_char = substr($p_string, $i, 1);
		}

		if($t_char != ' ')
		{
			$l++;
		}
		else
		{
			$l = 0;
		}

		if($l > $p_length)
		{
			$l = 1;
			$t_output .= $p_break_char;
		}

		$t_output .= $t_char;
	}

	return $t_output;
}

function xtc_get_country_name($country_id)
{
	$country_query = xtc_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");

	if(!xtc_db_num_rows($country_query))
	{
		return (int)$country_id;
	}
	else
	{
		$country = xtc_db_fetch_array($country_query);
		return $country['countries_name'];
	}
}

function xtc_get_zone_name($country_id, $zone_id, $default_zone)
{
	$zone_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' and zone_id = '" . (int)$zone_id . "'");
	if(xtc_db_num_rows($zone_query))
	{
		$zone = xtc_db_fetch_array($zone_query);
		return $zone['zone_name'];
	}
	else
	{
		return $default_zone;
	}
}

function xtc_browser_detect($component)
{

	return stristr($_SERVER['HTTP_USER_AGENT'], $component);
}

function xtc_tax_classes_pull_down($parameters, $selected = '')
{
	$select_string = '<select ' . $parameters . '>';
	$classes_query = xtc_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
	while($classes = xtc_db_fetch_array($classes_query))
	{
		$select_string .= '<option value="' . $classes['tax_class_id'] . '"';
		if($selected == $classes['tax_class_id'])
			$select_string .= ' SELECTED';
		$select_string .= '>' . htmlspecialchars($classes['tax_class_title'],ENT_QUOTES) . '</option>';
	}
	$select_string .= '</select>';

	return $select_string;
}

function xtc_geo_zones_pull_down($parameters, $selected = '')
{
	$select_string = '<select ' . $parameters . '>';
	$zones_query = xtc_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " order by geo_zone_name");
	while($zones = xtc_db_fetch_array($zones_query))
	{
		$select_string .= '<option value="' . $zones['geo_zone_id'] . '"';
		if($selected == $zones['geo_zone_id'])
			$select_string .= ' SELECTED';
		$select_string .= '>' . htmlspecialchars($zones['geo_zone_name'], ENT_QUOTES) . '</option>';
	}
	$select_string .= '</select>';

	return $select_string;
}

function xtc_get_geo_zone_name($geo_zone_id)
{
	$zones_query = xtc_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . (int)$geo_zone_id . "'");

	if(!xtc_db_num_rows($zones_query))
	{
		$geo_zone_name = (int)$geo_zone_id;
	}
	else
	{
		$zones = xtc_db_fetch_array($zones_query);
		$geo_zone_name = $zones['geo_zone_name'];
	}

	return $geo_zone_name;
}

function xtc_address_format($address_format_id, $address, $html, $boln, $eoln)
{
	$address_format_query = xtc_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . (int)$address_format_id . "'");
	$address_format = xtc_db_fetch_array($address_format_query);

	$company = addslashes($address['company']);
	$firstname = addslashes($address['firstname']);
	$cid = addslashes($address['csID']);
	$lastname = addslashes($address['lastname']);
	$street = addslashes($address['street_address']);
	$house_number            = addslashes($address['house_number']);
	$additional_address_info = addslashes($address['additional_address_info']);
	$suburb = addslashes($address['suburb']);
	$city = addslashes($address['city']);
	$state = addslashes($address['state']);
	$country_id = $address['country_id'];
	$zone_id = $address['zone_id'];
	$postcode = addslashes($address['postcode']);
	$zip = $postcode;
	$country = xtc_get_country_name($country_id);
	$state = xtc_get_zone_code($country_id, $zone_id, $state);

	if($html)
	{
		// HTML Mode
		$HR = '<hr />';
		$hr = '<hr />';
		if(($boln == '') && ($eoln == "\n"))
		{ // Values not specified, use rational defaults
			$CR = '<br />';
			$cr = '<br />';
			$eoln = $cr;
		}
		else
		{ // Use values supplied
			$CR = $eoln . $boln;
			$cr = $CR;
		}
	}
	else
	{
		// Text Mode
		$CR = $eoln;
		$cr = $CR;
		$HR = '----------------------------------------';
		$hr = '----------------------------------------';
	}

	$statecomma = '';
	$streets = $street;
	if($house_number != '')
	{
		$streets = $street . ' ' . $house_number;
	}
	if($suburb != '')
	{
		$streets = $streets . $cr . $suburb;
	}
	if($additional_address_info != '')
	{
		$streets .= $cr . $additional_address_info;
	}
	if($country == '')
	{
		$country = addslashes((string)$address['country']);
	}
	if($state != '')
	{
		$statecomma = $state . ', ';
	}

	$fmt = $address_format['format'];
	eval("\$address = \"$fmt\";");

	if((ACCOUNT_COMPANY == 'true') && (xtc_not_null($company)))
	{
		$address = $company . $cr . $address;
	}

	$address = stripslashes($address);

	// remove double line breaks
	$addressSegments = array_filter(array_map('trim', explode($eoln, $address)));
	
	return implode($eoln, $addressSegments);
}

////////////////////////////////////////////////////////////////////////////////////////////////
//
// Function    : xtc_get_zone_code
//
// Arguments   : country           country code string
//               zone              state/province zone_id
//               def_state         default string if zone==0
//
// Return      : state_prov_code   state/province code
//
// Description : Function to retrieve the state/province code (as in FL for Florida etc)
//
////////////////////////////////////////////////////////////////////////////////////////////////
function xtc_get_zone_code($country, $zone, $def_state)
{

	$state_prov_query = xtc_db_query("select zone_code from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and zone_id = '" . (int)$zone . "'");

	if(!xtc_db_num_rows($state_prov_query))
	{
		$state_prov_code = $def_state;
	}
	else
	{
		$state_prov_values = xtc_db_fetch_array($state_prov_query);
		$state_prov_code = $state_prov_values['zone_code'];
	}

	return $state_prov_code;
}

function xtc_get_uprid($prid, $params)
{
	$uprid = $prid;
	if((is_array($params)) && (!strstr($prid, '{')))
	{
		while(list ($option, $value) = each($params))
		{
			$uprid = $uprid . '{' . $option . '}' . $value;
		}
	}

	return $uprid;
}

function xtc_get_prid($uprid)
{
	$pieces = explode('{', $uprid);

	return $pieces[0];
}

function xtc_get_languages()
{
	$languages_query = xtc_db_query("select languages_id, name, code, image, directory, status from " . TABLE_LANGUAGES . " order by sort_order");
	while($languages = xtc_db_fetch_array($languages_query))
	{
		$languages_array[] = array(
			'id' => $languages['languages_id'],
			'name' => $languages['name'],
			'code' => $languages['code'],
			'image' => $languages['image'],
			'directory' => $languages['directory'],
			'status' => (int)$languages['status']
		);
	}

	return $languages_array;
}

function xtc_get_categories_name($category_id, $language_id)
{
	$category_query = xtc_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_name'];
}

function xtc_get_categories_heading_title($category_id, $language_id)
{
	$category_query = xtc_db_query("select categories_heading_title from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = xtc_db_fetch_array($category_query);
	return $category['categories_heading_title'];
}

function xtc_get_categories_description($category_id, $language_id)
{
	$category_query = xtc_db_query("select categories_description from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_description'];
}

function xtc_get_categories_meta_title($category_id, $language_id)
{
	$category_query = xtc_db_query("select categories_meta_title from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_meta_title'];
}

function xtc_get_categories_meta_description($category_id, $language_id)
{
	$category_query = xtc_db_query("select categories_meta_description from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_meta_description'];
}

function xtc_get_categories_meta_keywords($category_id, $language_id)
{
	$category_query = xtc_db_query("select categories_meta_keywords from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
	$category = xtc_db_fetch_array($category_query);

	return $category['categories_meta_keywords'];
}

function xtc_get_orders_status_name($orders_status_id, $language_id = '')
{

	if(!$language_id)
		$language_id = $_SESSION['languages_id'];
	$orders_status_query = xtc_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . (int)$orders_status_id . "' and language_id = '" . (int)$language_id . "'");
	$orders_status = xtc_db_fetch_array($orders_status_query);

	return $orders_status['orders_status_name'];
}

function xtc_get_cross_sell_name($cross_sell_group, $language_id = '')
{

	if(!$language_id)
		$language_id = $_SESSION['languages_id'];
	$cross_sell_query = xtc_db_query("select groupname from " . TABLE_PRODUCTS_XSELL_GROUPS . " where products_xsell_grp_name_id = '" . (int)$cross_sell_group . "' and language_id = '" . (int)$language_id . "'");
	$cross_sell = xtc_db_fetch_array($cross_sell_query);

	return $cross_sell['groupname'];
}

function xtc_get_shipping_status_name($shipping_status_id, $language_id = '')
{

	if(!$language_id)
		$language_id = $_SESSION['languages_id'];
	$shipping_status_query = xtc_db_query("select shipping_status_name from " . TABLE_SHIPPING_STATUS . " where shipping_status_id = '" . (int)$shipping_status_id . "' and language_id = '" . (int)$language_id . "'");
	$shipping_status = xtc_db_fetch_array($shipping_status_query);

	return $shipping_status['shipping_status_name'];
}

function xtc_get_orders_status()
{

	$orders_status_array = array();
	$orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$_SESSION['languages_id'] . "' order by orders_status_id");
	while($orders_status = xtc_db_fetch_array($orders_status_query))
	{
		$orders_status_array[] = array('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	}

	return $orders_status_array;
}

function xtc_get_cross_sell_groups()
{

	$cross_sell_array = array();
	$cross_sell_query = xtc_db_query("select products_xsell_grp_name_id, groupname from " . TABLE_PRODUCTS_XSELL_GROUPS . " where language_id = '" . (int)$_SESSION['languages_id'] . "' order by products_xsell_grp_name_id");
	while($cross_sell = xtc_db_fetch_array($cross_sell_query))
	{
		$cross_sell_array[] = array('id' => $cross_sell['products_xsell_grp_name_id'], 'text' => $cross_sell['groupname']);
	}

	return $cross_sell_array;
}

function xtc_get_products_vpe_name($products_vpe_id, $language_id = '')
{

	if(!$language_id)
		$language_id = $_SESSION['languages_id'];
	$products_vpe_query = xtc_db_query("select products_vpe_name from " . TABLE_PRODUCTS_VPE . " where products_vpe_id = '" . (int)$products_vpe_id . "' and language_id = '" . (int)$language_id . "'");
	$products_vpe = xtc_db_fetch_array($products_vpe_query);

	return $products_vpe['products_vpe_name'];
}

function xtc_get_shipping_status()
{

	$shipping_status_array = array();
	$shipping_status_query = xtc_db_query("select shipping_status_id, shipping_status_name from " . TABLE_SHIPPING_STATUS . " where language_id = '" . (int)$_SESSION['languages_id'] . "' order by number_of_days, shipping_status_id");
	while($shipping_status = xtc_db_fetch_array($shipping_status_query))
	{
		$shipping_status_array[] = array('id' => $shipping_status['shipping_status_id'], 'text' => $shipping_status['shipping_status_name']);
	}

	return $shipping_status_array;
}

function xtc_get_products_name($product_id, $language_id = 0)
{

	if($language_id == 0)
		$language_id = $_SESSION['languages_id'];
	$product_query = xtc_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_name'];
}

function xtc_get_products_description($product_id, $language_id)
{
	$product_query = xtc_db_query("select products_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_description'];
}

function xtc_get_products_short_description($product_id, $language_id)
{
	$product_query = xtc_db_query("select products_short_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_short_description'];
}

function xtc_get_products_keywords($product_id, $language_id)
{
	$product_query = xtc_db_query("select products_keywords from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_keywords'];
}

function xtc_get_products_meta_title($product_id, $language_id)
{
	$product_query = xtc_db_query("select products_meta_title from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_meta_title'];
}

function xtc_get_products_meta_description($product_id, $language_id)
{
	$product_query = xtc_db_query("select products_meta_description from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_meta_description'];
}

function xtc_get_products_meta_keywords($product_id, $language_id)
{
	$product_query = xtc_db_query("select products_meta_keywords from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_meta_keywords'];
}

function xtc_get_products_url($product_id, $language_id)
{
	$product_query = xtc_db_query("select products_url from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language_id . "'");
	$product = xtc_db_fetch_array($product_query);

	return $product['products_url'];
}

////
// Return the manufacturers URL in the needed language
// TABLES: manufacturers_info
function xtc_get_manufacturer_url($manufacturer_id, $language_id)
{
	$manufacturer_query = xtc_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$manufacturer_id . "' and languages_id = '" . (int)$language_id . "'");
	$manufacturer = xtc_db_fetch_array($manufacturer_query);

	return $manufacturer['manufacturers_url'];
}

////
// Wrapper for class_exists() function
// This function is not available in all PHP versions so we test it before using it.
function xtc_class_exists($class_name)
{
	if(function_exists('class_exists'))
	{
		return class_exists($class_name);
	}
	else
	{
		return true;
	}
}

////
// Returns an array with countries
// TABLES: countries
function xtc_get_countries($default = '')
{
	$languageTextManager = MainFactory::create_object('LanguageTextManager', array('countries', $_SESSION['languages_id']));

	$countries_array = array();
	if($default)
	{
		$countries_array[] = array('id' => STORE_COUNTRY, 'text' => $default);
	}
	$countries_query = xtc_db_query("select countries_id, countries_iso_code_2 from " . TABLE_COUNTRIES);
	while($countries = xtc_db_fetch_array($countries_query))
	{
		$countries_array[] = array('id' => $countries['countries_id'], 'text' => $languageTextManager->get_text($countries['countries_iso_code_2']));
	}

	usort($countries_array, 'sortCountriesByText');

	return $countries_array;
}


/**
 * @param array $a
 * @param array $b
 * @return int
 */
function sortCountriesByText(array $a, array $b)
{
	if($a['text'] == $b['text'])
	{
		return 0;
	}
	$arr_search  = array("Ä","Ö","Ü");
	$arr_replace = array("A","O","U");
	$a['text']   = str_replace( $arr_search, $arr_replace, $a['text']);
	$b['text']   = str_replace( $arr_search, $arr_replace, $b['text']);
	$return = ($a['text'] < $b['text']) ? -1 : +1;
	$a['text']   = str_replace( $arr_replace, $arr_search, $a['text']);
	$b['text']   = str_replace( $arr_replace, $arr_search, $b['text']);
	return $return;
}

////
// return an array with country zones
function xtc_get_country_zones($country_id)
{
	$zones_array = array();
	$zones_query = xtc_db_query("select zone_id, zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' order by zone_name");
	while($zones = xtc_db_fetch_array($zones_query))
	{
		$zones_array[] = array('id' => $zones['zone_id'], 'text' => $zones['zone_name']);
	}

	return $zones_array;
}

function xtc_prepare_country_zones_pull_down($country_id = '')
{
	// preset the width of the drop-down for Netscape
	$pre = '';
	if((!xtc_browser_detect('MSIE')) && (xtc_browser_detect('Mozilla/4')))
	{
		for($i = 0; $i < 45; $i++)
			$pre .= '&nbsp;';
	}

	$zones = xtc_get_country_zones($country_id);

	if(sizeof($zones) > 0)
	{
		$zones_select = array(array('id' => '', 'text' => PLEASE_SELECT));
		$zones = xtc_array_merge($zones_select, $zones);
	}
	else
	{
		$zones = array(array('id' => '', 'text' => TYPE_BELOW));
		// create dummy options for Netscape to preset the height of the drop-down
		if((!xtc_browser_detect('MSIE')) && (xtc_browser_detect('Mozilla/4')))
		{
			for($i = 0; $i < 9; $i++)
			{
				$zones[] = array('id' => '', 'text' => $pre);
			}
		}
	}

	return $zones;
}

////
// Get list of address_format_id's
function xtc_get_address_formats()
{
	$address_format_query = xtc_db_query("select address_format_id from " . TABLE_ADDRESS_FORMAT . " order by address_format_id");
	$address_format_array = array();
	while($address_format_values = xtc_db_fetch_array($address_format_query))
	{
		$address_format_array[] = array('id' => $address_format_values['address_format_id'], 'text' => $address_format_values['address_format_id']);
	}
	return $address_format_array;
}

////
// Alias function for Store configuration values in the Administration Tool
function xtc_cfg_pull_down_country_list($country_id)
{
	return xtc_draw_pull_down_menu('configuration_value', xtc_get_countries(), $country_id);
}

function xtc_cfg_pull_down_zone_list($zone_id)
{
	return xtc_draw_pull_down_menu('configuration_value', xtc_get_country_zones(STORE_COUNTRY), $zone_id);
}

function xtc_cfg_pull_down_tax_classes($tax_class_id, $key = '')
{
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
	$tax_class_query = xtc_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
	while($tax_class = xtc_db_fetch_array($tax_class_query))
	{
		$tax_class_array[] = array('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
	}

	return xtc_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
}

////
// Function to read in text area in admin
function xtc_cfg_textarea($text)
{
	// Most of the times provided $text parameter will be "htmlentities" encoded. This is not necessary
	// though because the "xtc_draw_textarea_field" will perform the "htmlspecialchars" function in the $text.
	// So we decode the $text parameter before we provide it to the "xtc_draw_textarea_field" (refs #41603).
	return xtc_draw_textarea_field('configuration_value', false, 35, 5, html_entity_decode_wrapper($text));
}

// bof gm
function xtc_cfg_nc_textarea($name, $text)
{
	return xtc_draw_textarea_field($name, false, 24, 5, $text);
}

// eof gm

function xtc_cfg_get_zone_name($zone_id)
{
	$zone_query = xtc_db_query("select zone_name from " . TABLE_ZONES . " where zone_id = '" . (int)$zone_id . "'");

	if(!xtc_db_num_rows($zone_query))
	{
		return (int)$zone_id;
	}
	else
	{
		$zone = xtc_db_fetch_array($zone_query);
		return $zone['zone_name'];
	}
}

////
// Sets the status of a banner
function xtc_set_banner_status($banners_id, $status)
{
	if($status == '1')
	{
		return xtc_db_query("update " . TABLE_BANNERS . " set status = '1', expires_impressions = NULL, expires_date = NULL, date_status_change = NULL where banners_id = '" . (int)$banners_id . "'");
	}
	elseif($status == '0')
	{
		return xtc_db_query("update " . TABLE_BANNERS . " set status = '0', date_status_change = now() where banners_id = '" . (int)$banners_id . "'");
	}
	else
	{
		return -1;
	}
}

// Sets the status of a product on special
function xtc_set_specials_status($specials_id, $status)
{
	if($status == '1')
	{
		return xtc_db_query("update " . TABLE_SPECIALS . " set status = '1', date_status_change = now() where specials_id = '" . (int)$specials_id . "'");
	}
	elseif($status == '0')
	{
		return xtc_db_query("update " . TABLE_SPECIALS . " set status = '0', date_status_change = now() where specials_id = '" . (int)$specials_id . "'");
	}
	else
	{
		return -1;
	}
}

////
// Sets timeout for the current script.
// Cant be used in safe mode.
function xtc_set_time_limit($limit)
{
	if(!get_cfg_var('safe_mode'))
	{
		@ set_time_limit($limit);
	}
}

////
// Alias function for Store configuration values in the Administration Tool
function xtc_cfg_select_option($select_array, $key_value, $key = '')
{
	return gm_cfg_select_option($select_array, $key_value, $key);
}

////
// Alias function for module configuration keys
function xtc_mod_select_option($select_array, $key_name, $key_value)
{
	reset($select_array);
	while(list ($key, $value) = each($select_array))
	{
		if(is_int($key))
			$key = $value;
		$html .= '<br><input type="radio" name="configuration[' . $key_name . ']" value="' . $key . '"';
		if($key_value == $key)
			$html .= ' CHECKED';
		$html .= '> ' . $value;
	}

	return $html;
}

////
// Retreive server information
function xtc_get_system_information()
{

	$db_query = xtc_db_query("select now() as datetime");
	$db = xtc_db_fetch_array($db_query);

	list ($system, $host, $kernel) = preg_split('/[\s,]+/', @ exec('uname -a'), 5);

	return array('date' => xtc_datetime_short(date('Y-m-d H:i:s')), 'system' => $system, 'kernel' => $kernel, 'host' => $host, 'ip' => gethostbyname($host), 'uptime' => @ exec('uptime'), 'http_server' => $_SERVER['SERVER_SOFTWARE'], 'php' => PHP_VERSION, 'zend' => (function_exists('zend_version') ? zend_version() : ''), 'db_server' => DB_SERVER, 'db_ip' => gethostbyname(DB_SERVER), 'db_version' => 'MySQL ' . (function_exists('mysqli_get_server_info') ? ((is_null($___mysqli_res = mysqli_get_server_info($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res) : ''), 'db_date' => xtc_datetime_short($db['datetime']));
}

function xtc_array_shift(& $array)
{
	if(function_exists('array_shift'))
	{
		return array_shift($array);
	}
	else
	{
		$i = 0;
		$shifted_array = array();
		reset($array);
		while(list ($key, $value) = each($array))
		{
			if($i > 0)
			{
				$shifted_array[$key] = $value;
			}
			else
			{
				$return = $array[$key];
			}
			$i++;
		}
		$array = $shifted_array;

		return $return;
	}
}

function xtc_array_reverse($array)
{
	if(function_exists('array_reverse'))
	{
		return array_reverse($array);
	}
	else
	{
		$reversed_array = array();
		for($i = sizeof($array) - 1; $i >= 0; $i--)
		{
			$reversed_array[] = $array[$i];
		}
		return $reversed_array;
	}
}

function xtc_generate_category_path($id, $from = 'category', $categories_array = '', $index = 0)
{

	if(!is_array($categories_array))
		$categories_array = array();

	if($from == 'product')
	{
		$categories_query = xtc_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$id . "'");
		while($categories = xtc_db_fetch_array($categories_query))
		{
			if($categories['categories_id'] == '0')
			{
				$categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
			}
			else
			{
				$category_query = xtc_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
				$category = xtc_db_fetch_array($category_query);
				$categories_array[$index][] = array('id' => $categories['categories_id'], 'text' => $category['categories_name']);
				if((xtc_not_null($category['parent_id'])) && ($category['parent_id'] != '0'))
					$categories_array = xtc_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
				$categories_array[$index] = xtc_array_reverse($categories_array[$index]);
			}
			$index++;
		}
	}
	elseif($from == 'category')
	{
		$category_query = xtc_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . (int)$id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
		$category = xtc_db_fetch_array($category_query);
		$categories_array[$index][] = array('id' => $id, 'text' => $category['categories_name']);
		if((xtc_not_null($category['parent_id'])) && ($category['parent_id'] != '0'))
			$categories_array = xtc_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
	}

	return $categories_array;
}

function xtc_output_generated_category_path($id, $from = 'category')
{
	$calculated_category_path_string = '';
	$calculated_category_path = xtc_generate_category_path($id, $from);
	for($i = 0, $n = sizeof($calculated_category_path); $i < $n; $i++)
	{
		for($j = 0, $k = sizeof($calculated_category_path[$i]); $j < $k; $j++)
		{
			$calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
		}
		$calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br>';
	}
	$calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

	if(strlen($calculated_category_path_string) < 1)
		$calculated_category_path_string = TEXT_TOP;

	return $calculated_category_path_string;
}

//deletes all product image files by filename
function xtc_del_image_file($image)
{
	if(file_exists(DIR_FS_CATALOG_POPUP_IMAGES . $image))
	{
		@ unlink(DIR_FS_CATALOG_POPUP_IMAGES . $image);
	}
	if(file_exists(DIR_FS_CATALOG_ORIGINAL_IMAGES . $image))
	{
		@ unlink(DIR_FS_CATALOG_ORIGINAL_IMAGES . $image);
	}
	if(file_exists(DIR_FS_CATALOG_THUMBNAIL_IMAGES . $image))
	{
		@ unlink(DIR_FS_CATALOG_THUMBNAIL_IMAGES . $image);
	}
	if(file_exists(DIR_FS_CATALOG_INFO_IMAGES . $image))
	{
		@ unlink(DIR_FS_CATALOG_INFO_IMAGES . $image);
	}
	if(file_exists(DIR_FS_CATALOG_IMAGES . 'product_images/gallery_images/' . $image))
	{
		@ unlink(DIR_FS_CATALOG_IMAGES . 'product_images/gallery_images/' . $image);
	}

	// BOF GM_MOD
	if(file_exists(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . basename($image)))
	{
		@unlink(DIR_FS_CATALOG_IMAGES . 'product_images/gm_gmotion_images/' . basename($image));
	}
	// EOF GM_MOD
}

function xtc_remove_order($order_id, $restock = false, $canceled = false, $reshipp = false, $reactivateArticle = false)
{
	if($restock == 'on' || $reshipp == 'on')
	{
		// BOF GM_MOD:
		$order_query = xtc_db_query("
									SELECT DISTINCT
										op.orders_products_id,
										op.products_id,
										op.products_quantity,
										opp.products_properties_combis_id,
										o.date_purchased
									FROM " . TABLE_ORDERS_PRODUCTS . " op
										LEFT JOIN " . TABLE_ORDERS . " o ON op.orders_id = o.orders_id
										LEFT JOIN orders_products_properties opp ON opp.orders_products_id = op.orders_products_id
									WHERE
										op.orders_id = '" . xtc_db_input($order_id) . "'
		");

		while($order = xtc_db_fetch_array($order_query))
		{
			if($restock == 'on')
			{
				/* BOF SPECIALS RESTOCK */
				$t_query = xtc_db_query("
										SELECT
											specials_date_added
										AS
											date
										FROM " .
						TABLE_SPECIALS . "
										WHERE
											specials_date_added < '" . $order['date_purchased'] . "'
										AND
											products_id			= '" . $order['products_id'] . "'
				");

				if((int)xtc_db_num_rows($t_query) > 0)
				{
					xtc_db_query("
									UPDATE " .
							TABLE_SPECIALS . "
									SET
										specials_quantity = specials_quantity + " . $order['products_quantity'] . "
									WHERE
										products_id = '" . $order['products_id'] . "'
					");
				}
				/* EOF SPECIALS RESTOCK */

				// check if combis exists
				$t_combis_query = xtc_db_query("
								SELECT
                                    products_properties_combis_id
                                FROM
									products_properties_combis
								WHERE
									products_id = '" . $order['products_id'] . "'
				");
				$t_combis_array_length = xtc_db_num_rows($t_combis_query);

				if($t_combis_array_length > 0)
				{
					$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
					$t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity($order['products_id']);
				}
				else
				{
					$t_use_combis_quantity = 0;
				}

				if($t_combis_array_length == 0 || $t_use_combis_quantity == 1 || ($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK != 'true'))
				{
					xtc_db_query("
                                    UPDATE " .
							TABLE_PRODUCTS . "
                                    SET
                                        products_quantity = products_quantity + " . $order['products_quantity'] . "
                                    WHERE
                                        products_id = '" . $order['products_id'] . "'
                    ");
				}

				xtc_db_query("
                                UPDATE " .
						TABLE_PRODUCTS . "
                                SET
                                    products_ordered = products_ordered - " . $order['products_quantity'] . "
                                WHERE
                                    products_id = '" . $order['products_id'] . "'
                ");

				if($t_combis_array_length > 0 && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2))
				{
					xtc_db_query("
                                    UPDATE
                                        products_properties_combis
                                    SET
                                        combi_quantity = combi_quantity + " . $order['products_quantity'] . "
                                    WHERE
                                        products_properties_combis_id = '" . $order['products_properties_combis_id'] . "' AND
                                        products_id = '" . $order['products_id'] . "'
                    ");
				}


				// BOF GM_MOD
				if(ATTRIBUTE_STOCK_CHECK == 'true')
				{
					$gm_get_orders_attributes = xtc_db_query("
															SELECT
																products_options,
																products_options_values
															FROM
																orders_products_attributes
															WHERE
																orders_id = '" . xtc_db_input($order_id) . "'
															AND
																orders_products_id = '" . $order['orders_products_id'] . "'
					");

					while($gm_orders_attributes = xtc_db_fetch_array($gm_get_orders_attributes))
					{
						$gm_get_attributes_id = xtc_db_query("
															SELECT
																pa.products_attributes_id
															FROM
																products_options_values pov,
																products_options po,
																products_attributes pa
															WHERE
																po.products_options_name = '" . addslashes($gm_orders_attributes['products_options']) . "'
																AND po.products_options_id = pa.options_id
																AND pov.products_options_values_id = pa.options_values_id
																AND pov.products_options_values_name = '" . addslashes($gm_orders_attributes['products_options_values']) . "'
																AND pa.products_id = '" . $order['products_id'] . "'
															LIMIT 1
						");

						if(xtc_db_num_rows($gm_get_attributes_id) == 1)
						{
							$gm_attributes_id = xtc_db_fetch_array($gm_get_attributes_id);

							xtc_db_query("
											UPDATE
												products_attributes
											SET
												attributes_stock = attributes_stock + " . $order['products_quantity'] . "
											WHERE
												products_attributes_id = '" . $gm_attributes_id['products_attributes_id'] . "'
							");
						}
					}
				}
				if($reactivateArticle == 'on')
				{
					$t_reactivate_product = false;

					// check if combis exists
					$t_combis_query = xtc_db_query("
									SELECT
										products_properties_combis_id
									FROM
										products_properties_combis
									WHERE
										products_id = '" . $order['products_id'] . "'
					");
					$t_combis_array_length = xtc_db_num_rows($t_combis_query);

					if($t_combis_array_length > 0)
					{
						$coo_combis_admin_control = MainFactory::create_object("PropertiesCombisAdminControl");
						$t_use_combis_quantity = $coo_combis_admin_control->get_use_properties_combis_quantity($order['products_id']);
					}
					else
					{
						$t_use_combis_quantity = 0;
					}

					// CHECK PRODUCT QUANTITY
					if($t_combis_array_length == 0 || $t_use_combis_quantity == 1 || ($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK != 'true'))
					{
						$coo_get_product = new GMDataObject('products', array('products_id' => $order['products_id']));
						if($coo_get_product->get_data_value('products_quantity') > 0 && $coo_get_product->get_data_value('products_status') == 0)
						{
							$t_reactivate_product = true;
						}
					}

					// CHECK COMBI QUANTITY
					if($t_combis_array_length > 0 && (($t_use_combis_quantity == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') || $t_use_combis_quantity == 2))
					{
						$coo_properties_control = MainFactory::create_object('PropertiesControl');
						$t_reactivate_product = $coo_properties_control->available_combi_exists($order['products_id']);
					}

					if($t_reactivate_product)
					{
						$coo_set_product = new GMDataObject('products');
						$coo_set_product->set_keys(array('products_id' => $order['products_id']));
						$coo_set_product->set_data_value('products_status', 1);
						$coo_set_product->save_body_data();
					}
				}
				// EOF GM_MOD
			}

			// BOF GM_MOD products_shippingtime:
			if($reshipp == 'on')
			{
				require_once(DIR_FS_CATALOG . 'gm/inc/set_shipping_status.php');
				set_shipping_status($order['products_id'], $order['products_properties_combis_id']);
			}
			// BOF GM_MOD products_shippingtime:
		}
	}

	if(!$canceled)
	{
		xtc_db_query("DELETE from " . TABLE_ORDERS . " WHERE orders_id = '" . xtc_db_input($order_id) . "'");

		$t_orders_products_ids_sql = 'SELECT orders_products_id FROM ' . TABLE_ORDERS_PRODUCTS . ' WHERE orders_id = "' . xtc_db_input($order_id) . '"';
		$t_orders_products_ids_result = xtc_db_query($t_orders_products_ids_sql);
		while($t_orders_products_ids_array = xtc_db_fetch_array($t_orders_products_ids_result))
		{
			xtc_db_query("DELETE FROM orders_products_quantity_units WHERE orders_products_id = '" . (int)$t_orders_products_ids_array['orders_products_id'] . "'");
			xtc_db_query('DELETE FROM orders_products_properties WHERE orders_products_id = "' . (int)$t_orders_products_ids_array['orders_products_id'] . '"');
		}

		// DELETE from gm_gprint_orders_*, and gm_gprint_uploads
		$coo_gm_gprint_order_manager = MainFactory::create_object('GMGPrintOrderManager');
		$coo_gm_gprint_order_manager->delete_order((int)$order_id);

		xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS . " WHERE orders_id = '" . (int)$order_id . "'");
		xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " WHERE orders_id = '" . (int)$order_id . "'");
		xtc_db_query("DELETE FROM " . TABLE_ORDERS_PRODUCTS_DOWNLOAD . " WHERE orders_id = '" . (int)$order_id . "'");
		xtc_db_query("DELETE FROM " . TABLE_ORDERS_STATUS_HISTORY . " WHERE orders_id = '" . (int)$order_id . "'");
		xtc_db_query("DELETE FROM " . TABLE_ORDERS_TOTAL . " WHERE orders_id = '" . (int)$order_id . "'");
		xtc_db_query("DELETE FROM banktransfer WHERE orders_id = '" . (int)$order_id . "'");
		xtc_db_query("DELETE FROM sepa WHERE orders_id = '" . (int)$order_id . "'");
		xtc_db_query("DELETE FROM orders_parcel_tracking_codes WHERE order_id = '" . (int)$order_id . "'");
		xtc_db_query("DELETE FROM orders_tax_sum_items WHERE order_id = '" . (int)$order_id . "'");

		// BOF GM_MOD GX-Customizer:
		require_once('../gm/modules/gm_gprint_admin_general.php');

		/*		 * ******* PayPal ********** */
		require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'class.paypal.php');
		if(paypal_admin::is_installed())
		{
			$t_get_paypal_ipn_id = xtc_db_query("
												SELECT
													paypal_ipn_id
												FROM
													paypal
												WHERE
													xtc_order_id = '" . (int)$order_id . "'");

			while($t_paypal_ipn_id = xtc_db_fetch_array($t_get_paypal_ipn_id))
			{
				xtc_db_query("DELETE FROM paypal_status_history WHERE `paypal_ipn_id` = '" . (int)$t_paypal_ipn_id['paypal_ipn_id'] . "'");
			}

			xtc_db_query("DELETE FROM paypal WHERE `xtc_order_id` = '" . (int)$order_id . "'");
		}
		/*		 * ******* PayPal ********** */
	}
}

function xtc_reset_cache_block($cache_block)
{
	global $cache_blocks;

	for($i = 0, $n = sizeof($cache_blocks); $i < $n; $i++)
	{
		if($cache_blocks[$i]['code'] == $cache_block)
		{
			if($cache_blocks[$i]['multiple'])
			{
				if($dir = @ opendir(DIR_FS_CACHE))
				{
					while($cache_file = readdir($dir))
					{
						$cached_file = $cache_blocks[$i]['file'];
						$languages = xtc_get_languages();
						for($j = 0, $k = sizeof($languages); $j < $k; $j++)
						{
							$cached_file_unlink = preg_replace('/-language/', '-' . $languages[$j]['directory'], $cached_file);
							if(preg_match('!^' . $cached_file_unlink . "!", $cache_file))
							{
								@ unlink(DIR_FS_CACHE . $cache_file);
							}
						}
					}
					closedir($dir);
				}
			}
			else
			{
				$cached_file = $cache_blocks[$i]['file'];
				$languages = xtc_get_languages();
				for($i = 0, $n = sizeof($languages); $i < $n; $i++)
				{
					$cached_file = preg_replace('/-language/', '-' . $languages[$i]['directory'], $cached_file);
					@ unlink(DIR_FS_CACHE . $cached_file);
				}
			}
			break;
		}
	}
}

function xtc_get_file_permissions($mode)
{
	// determine type
	if(($mode & 0xC000) == 0xC000)
	{ // unix domain socket
		$type = 's';
	}
	elseif(($mode & 0x4000) == 0x4000)
	{ // directory
		$type = 'd';
	}
	elseif(($mode & 0xA000) == 0xA000)
	{ // symbolic link
		$type = 'l';
	}
	elseif(($mode & 0x8000) == 0x8000)
	{ // regular file
		$type = '-';
	}
	elseif(($mode & 0x6000) == 0x6000)
	{ //bBlock special file
		$type = 'b';
	}
	elseif(($mode & 0x2000) == 0x2000)
	{ // character special file
		$type = 'c';
	}
	elseif(($mode & 0x1000) == 0x1000)
	{ // named pipe
		$type = 'p';
	}
	else
	{ // unknown
		$type = '?';
	}

	// determine permissions
	$owner['read'] = ($mode & 00400) ? 'r' : '-';
	$owner['write'] = ($mode & 00200) ? 'w' : '-';
	$owner['execute'] = ($mode & 00100) ? 'x' : '-';
	$group['read'] = ($mode & 00040) ? 'r' : '-';
	$group['write'] = ($mode & 00020) ? 'w' : '-';
	$group['execute'] = ($mode & 00010) ? 'x' : '-';
	$world['read'] = ($mode & 00004) ? 'r' : '-';
	$world['write'] = ($mode & 00002) ? 'w' : '-';
	$world['execute'] = ($mode & 00001) ? 'x' : '-';

	// adjust for SUID, SGID and sticky bit
	if($mode & 0x800)
		$owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
	if($mode & 0x400)
		$group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
	if($mode & 0x200)
		$world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

	return $type . $owner['read'] . $owner['write'] . $owner['execute'] . $group['read'] . $group['write'] . $group['execute'] . $world['read'] . $world['write'] . $world['execute'];
}

function xtc_array_slice($array, $offset, $length = '0')
{
	if(function_exists('array_slice'))
	{
		return array_slice($array, $offset, $length);
	}
	else
	{
		$length = abs($length);
		if($length == 0)
		{
			$high = sizeof($array);
		}
		else
		{
			$high = $offset + $length;
		}

		for($i = $offset; $i < $high; $i++)
		{
			$new_array[$i - $offset] = $array[$i];
		}

		return $new_array;
	}
}

function xtc_remove($source)
{
	global $messageStack, $xtc_remove_error;

	if(isset($xtc_remove_error))
		$xtc_remove_error = false;

	if(is_dir($source))
	{
		$dir = dir($source);
		while($file = $dir->read())
		{
			if(($file != '.') && ($file != '..'))
			{
				if(is_writeable($source . '/' . $file))
				{
					xtc_remove($source . '/' . $file);
				}
				else
				{
					$messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source . '/' . $file), 'error');
					$xtc_remove_error = true;
				}
			}
		}
		$dir->close();

		if(is_writeable($source))
		{
			rmdir($source);
		}
		else
		{
			$messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
			$xtc_remove_error = true;
		}
	}
	else
	{
		if(is_writeable($source))
		{
			unlink($source);
		}
		else
		{
			$messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
			$xtc_remove_error = true;
		}
	}
}

////
// Wrapper for constant() function
// Needed because its only available in PHP 4.0.4 and higher.
function xtc_constant($constant)
{
	if(function_exists('constant'))
	{
		$temp = constant($constant);
	}
	else
	{
		eval("\$temp=$constant;");
	}
	return $temp;
}

////
// Output the tax percentage with optional padded decimals
function xtc_display_tax_value($value, $padding = TAX_DECIMAL_PLACES)
{
	if(strpos($value, '.'))
	{
		$loop = true;
		while($loop)
		{
			if(substr($value, -1) == '0')
			{
				$value = substr($value, 0, -1);
			}
			else
			{
				$loop = false;
				if(substr($value, -1) == '.')
				{
					$value = substr($value, 0, -1);
				}
			}
		}
	}

	if($padding > 0)
	{
		if($decimal_pos = strpos($value, '.'))
		{
			$decimals = strlen(substr($value, ($decimal_pos + 1)));
			for($i = $decimals; $i < $padding; $i++)
			{
				$value .= '0';
			}
		}
		else
		{
			$value .= '.';
			for($i = 0; $i < $padding; $i++)
			{
				$value .= '0';
			}
		}
	}

	return $value;
}

function xtc_get_tax_class_title($tax_class_id)
{
	if($tax_class_id == '0')
	{
		return TEXT_NONE;
	}
	else
	{
		$classes_query = xtc_db_query("select tax_class_title from " . TABLE_TAX_CLASS . " where tax_class_id = '" . (int)$tax_class_id . "'");
		$classes = xtc_db_fetch_array($classes_query);

		return $classes['tax_class_title'];
	}
}

function xtc_banner_image_extension()
{
	if(function_exists('imagetypes'))
	{
		if(imagetypes() & IMG_PNG)
		{
			return 'png';
		}
		elseif(imagetypes() & IMG_JPG)
		{
			return 'jpg';
		}
		elseif(imagetypes() & IMG_GIF)
		{
			return 'gif';
		}
	}
	elseif(function_exists('imagecreatefrompng') && function_exists('imagepng'))
	{
		return 'png';
	}
	elseif(function_exists('imagecreatefromjpeg') && function_exists('imagejpeg'))
	{
		return 'jpg';
	}
	elseif(function_exists('imagecreatefromgif') && function_exists('imagegif'))
	{
		return 'gif';
	}

	return false;
}

////
// Wrapper function for round()
function xtc_round($value, $precision)
{
	return round($value, $precision);
}

// Calculates Tax rounding the result
function xtc_calculate_tax($price, $tax)
{
	global $currencies;
	return xtc_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
}

function xtc_call_function($function, $parameter, $object = '')
{
	if($object == '')
	{
		return call_user_func($function, $parameter);
	}
	else
	{
		return call_user_func(array($object, $function), $parameter);
	}
}

function xtc_get_zone_class_title($zone_class_id)
{
	if($zone_class_id == '0')
	{
		return TEXT_NONE;
	}
	else
	{
		$classes_query = xtc_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . (int)$zone_class_id . "'");
		$classes = xtc_db_fetch_array($classes_query);

		return $classes['geo_zone_name'];
	}
}

function xtc_cfg_pull_down_template_sets()
{
	$templateBaseDir = DIR_FS_CATALOG . 'templates/';
	$dir             = opendir($templateBaseDir);
	$templatesArray  = array();

	if($dir)
	{
		while(($templateName = readdir($dir)) !== false)
		{
			if($templateName === CURRENT_TEMPLATE)
			{
				$templatesArray[] = array('id' => $templateName, 'text' => $templateName);
				continue;
			}

			$templateDir      = $templateBaseDir . '/' . $templateName;
			$fileSettingsPath = $templateDir . '/template_settings.php';

			if(!in_array($templateName, array('.', '..', 'CVS', 'MobileCandy'))
			   && is_dir($templateDir)
			   && file_exists($fileSettingsPath)
			)
			{
				// include $t_template_settings_array
				include($fileSettingsPath);

				if(isset($t_template_settings_array)
				   && is_array($t_template_settings_array)
				   && array_key_exists('TEMPLATE_PRESENTATION_VERSION', $t_template_settings_array)
				   && $t_template_settings_array['TEMPLATE_PRESENTATION_VERSION'] >= 2.0
				)
				{
					$templatesArray[] = array('id' => $templateName, 'text' => $templateName);
				}
			}
		}

		closedir($dir);
		sort($templatesArray);
	}

	return xtc_draw_pull_down_menu('configuration_value', $templatesArray, CURRENT_TEMPLATE);
}

function xtc_cfg_pull_down_zone_classes($zone_class_id, $key = '')
{
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$zone_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
	$zone_class_query = xtc_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " order by geo_zone_name");
	while($zone_class = xtc_db_fetch_array($zone_class_query))
	{
		$zone_class_array[] = array('id' => $zone_class['geo_zone_id'], 'text' => $zone_class['geo_zone_name']);
	}

	return xtc_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
}

function xtc_cfg_pull_down_order_statuses($order_status_id, $key = '')
{

	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

	$statuses_array = array(array('id' => '1', 'text' => TEXT_DEFAULT));
	$statuses_query = xtc_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . (int)$_SESSION['languages_id'] . "' order by orders_status_name");
	while($statuses = xtc_db_fetch_array($statuses_query))
	{
		$statuses_array[] = array('id' => $statuses['orders_status_id'], 'text' => $statuses['orders_status_name']);
	}

	return xtc_draw_pull_down_menu($name, $statuses_array, $order_status_id);
}

function xtc_get_order_status_name($order_status_id, $language_id = '')
{

	if($order_status_id < 1)
		return TEXT_DEFAULT;

	if(!is_numeric($language_id))
		$language_id = $_SESSION['languages_id'];

	$status_query = xtc_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . (int)$order_status_id . "' and language_id = '" . (int)$language_id . "'");
	$status = xtc_db_fetch_array($status_query);

	return $status['orders_status_name'];
}

////
// Return a random value
function xtc_rand($min = null, $max = null)
{
	static $seeded;

	if(!$seeded)
	{
		mt_srand((double)microtime() * 1000000);
		$seeded = true;
	}

	if(isset($min) && isset($max))
	{
		if($min >= $max)
		{
			return $min;
		}
		else
		{
			return mt_rand($min, $max);
		}
	}
	else
	{
		return mt_rand();
	}
}

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
function xtc_convert_linefeeds($from, $to, $html)
{
	if((PHP_VERSION < "4.0.5") && is_array($from))
	{
		return preg_replace('!(' . implode('|', str_replace('!', '\!', $from)) . ')!', $to, $html);
	}
	else
	{
		return str_replace($from, $to, $html);
	}
}

// Return all customers statuses for a specified language_id and return an array(array())
// Use it to make pull_down_menu, checkbox....
function xtc_get_customers_statuses()
{

	$customers_statuses_array = array(array());
	$customers_statuses_query = xtc_db_query("select customers_status_id, customers_status_name, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . (int)$_SESSION['languages_id'] . "' order by customers_status_id");
	$i = 1; // this is changed from 0 to 1 in cs v1.2
	while($customers_statuses = xtc_db_fetch_array($customers_statuses_query))
	{
		$i = $customers_statuses['customers_status_id'];
		$customers_statuses_array[$i] = array('id' => $customers_statuses['customers_status_id'], 'text' => $customers_statuses['customers_status_name'], 'csa_public' => $customers_statuses['customers_status_public'], 'csa_image' => $customers_statuses['customers_status_image'], 'csa_discount' => $customers_statuses['customers_status_discount'], 'csa_ot_discount_flag' => $customers_statuses['customers_status_ot_discount_flag'], 'csa_ot_discount' => $customers_statuses['customers_status_ot_discount'], 'csa_graduated_prices' => $customers_statuses['customers_status_graduated_prices']);
	}
	return $customers_statuses_array;
}

function xtc_get_customer_status($customers_id)
{

	$customer_status_array = array();
	$customer_status_query = xtc_db_query("select customers_status, member_flag, customers_status_name, customers_status_public, customers_status_image, customers_status_discount, customers_status_ot_discount_flag, customers_status_ot_discount, customers_status_graduated_prices  FROM " . TABLE_CUSTOMERS . " left join " . TABLE_CUSTOMERS_STATUS . " on customers_status = customers_status_id where customers_id='" . (int)$customers_id . "' and language_id = '" . (int)$_SESSION['languages_id'] . "'");
	$customer_status_array = xtc_db_fetch_array($customer_status_query);
	return $customer_status_array;
}

function xtc_get_customers_status_name($customers_status_id, $language_id = '')
{

	if(!$language_id)
		$language_id = $_SESSION['languages_id'];
	$customers_status_query = xtc_db_query("select customers_status_name from " . TABLE_CUSTOMERS_STATUS . " where customers_status_id = '" . (int)$customers_status_id . "' and language_id = '" . (int)$language_id . "'");
	$customers_status = xtc_db_fetch_array($customers_status_query);
	return $customers_status['customers_status_name'];
}

//to set customers status in admin for newsletter, guest...
function xtc_cfg_pull_down_customers_status_list($customers_status_id, $key = '')
{
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
	return xtc_draw_pull_down_menu($name, xtc_get_customers_statuses(), $customers_status_id);
}

//to set customers status in admin for default value
function xtc_cfg_pull_down_default_customers_status_list($customers_status_id, $key = '')
{
	$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
	$customer_statuses = xtc_get_customers_statuses();
	for($i = 0; $i < count($customer_statuses); $i++)
	{
		if($customer_statuses[$i]['id'] == 0 || $customer_statuses[$i]['id'] == 1)
		{
			unset($customer_statuses[$i]);
			$customer_statuses = array_values($customer_statuses);
			$i--;
		}
	}
	return xtc_draw_pull_down_menu($name, $customer_statuses, $customers_status_id);
}

// Function for collecting ip
// return all log info for a customer_id
function xtc_get_user_info($customer_id)
{
	$user_info_array = xtc_db_query("select customers_ip, customers_ip_date, customers_host, customers_advertiser, customers_referer_url FROM " . TABLE_CUSTOMERS_IP . " where customers_id = '" . (int)$customer_id . "'");
	return $user_info_array;
}

//---------------------------------------------------------------kommt wieder raus spaeter!!
function xtc_get_uploaded_file($filename)
{
	if(isset($_FILES[$filename]))
	{
		$uploaded_file = array('name' => $_FILES[$filename]['name'], 'type' => $_FILES[$filename]['type'], 'size' => $_FILES[$filename]['size'], 'tmp_name' => $_FILES[$filename]['tmp_name']);
	}
	elseif(isset($_FILES[$filename]))
	{
		$uploaded_file = array('name' => $_FILES[$filename]['name'], 'type' => $_FILES[$filename]['type'], 'size' => $_FILES[$filename]['size'], 'tmp_name' => $_FILES[$filename]['tmp_name']);
	}
	else
	{
		$uploaded_file = array('name' => $GLOBALS[$filename . '_name'], 'type' => $GLOBALS[$filename . '_type'], 'size' => $GLOBALS[$filename . '_size'], 'tmp_name' => $GLOBALS[$filename]);
	}

	return $uploaded_file;
}

function get_group_price($group_id, $product_id)
{
	$group_id   = (int)$group_id;
	$product_id = (int)$product_id;
	
	// well, first try to get group price from database
	$group_price_query = xtc_db_query("SELECT personal_offer FROM " . TABLE_PERSONAL_OFFERS_BY . $group_id . " WHERE products_id = '" . $product_id . "' and quantity=1");
	$group_price_data = xtc_db_fetch_array($group_price_query);
	// if we found a price, everything is ok if not, we will create new entry
	// if there is no entry, create one. if there are more entries. keep one, dropp rest.
	if(!xtc_db_num_rows($group_price_query))
	{
		xtc_db_query("INSERT INTO " . TABLE_PERSONAL_OFFERS_BY . $group_id . " (price_id, products_id, quantity, personal_offer) VALUES ('', '" . $product_id . "', '1', '0.00')");
		$group_price_query = xtc_db_query("SELECT personal_offer FROM " . TABLE_PERSONAL_OFFERS_BY . $group_id . " WHERE products_id = '" . $product_id . "' ORDER BY quantity ASC");
		$group_price_data = xtc_db_fetch_array($group_price_query);
	}
	else
	if(xtc_db_num_rows($group_price_query) > 1)
	{
		while($data = xtc_db_fetch_array($group_price_query))
		{
			$group_price_data['personal_offer'] = $data['personal_offer'];
		}
		xtc_db_query("DELETE FROM " . TABLE_PERSONAL_OFFERS_BY . $group_id . " WHERE products_id='" . $product_id . "' and quantity=1");
		xtc_db_query("INSERT INTO " . TABLE_PERSONAL_OFFERS_BY . $group_id . " (price_id, products_id, quantity, personal_offer) VALUES ('', '" . $product_id . "', '1', '" . $group_price_data['personal_offer'] . "')");
		$group_price_query = xtc_db_query("SELECT personal_offer FROM " . TABLE_PERSONAL_OFFERS_BY . $group_id . " WHERE products_id = '" . $product_id . "' ORDER BY quantity ASC");
		$group_price_data = xtc_db_fetch_array($group_price_query);
	}

	return $group_price_data['personal_offer'];
}

function format_price($price_string, $price_special, $currency, $allow_tax, $tax_rate)
{
	// calculate currencies
	$currencies_query = xtc_db_query("SELECT
	                                          symbol_left,
	                                          symbol_right,
	                                          decimal_places,
	                                          value
	                                      FROM
	                                          " . TABLE_CURRENCIES . "
	                                      WHERE
	                                          code = '" . xtc_db_input($currency) . "'");
	$currencies_value = xtc_db_fetch_array($currencies_query);
	$currencies_data = array();
	$currencies_data = array('SYMBOL_LEFT' => $currencies_value['symbol_left'], 'SYMBOL_RIGHT' => $currencies_value['symbol_right'], 'DECIMAL_PLACES' => $currencies_value['decimal_places'], 'VALUE' => $currencies_value['value']);

	// round price
	if($allow_tax == 1)
		$price_string = $price_string / ((100 + $tax_rate) / 100);
	$price_string = precision($price_string, $currencies_data['DECIMAL_PLACES']);
	if($price_special == '1')
	{
		$price_string = $currencies_data['SYMBOL_LEFT'] . ' ' . $price_string . ' ' . $currencies_data['SYMBOL_RIGHT'];
	}
	return $price_string;
}

function precision($number, $places)
{
	$number = number_format((double)$number, (double)$places, '.', '');
	return $number;
}

function xtc_get_lang_definition($search_lang, $lang_array, $modifier)
{
	$search_lang = $search_lang . $modifier;
	return $lang_array[$search_lang];
}

function xtc_CheckExt($filename, $ext)
{
	$passed = FALSE;
	$testExt = "/\." . $ext . "$/i";
	if(preg_match($testExt, $filename))
	{
		$passed = TRUE;
	}
	return $passed;
}

function xtc_get_status_users($status_id)
{
	$status_query = xtc_db_query("SELECT count(*) as count FROM " . TABLE_CUSTOMERS . " WHERE customers_status = '" . (int)$status_id . "'");
	$status_data = xtc_db_fetch_array($status_query);
	return $status_data['count'];
}

function xtc_mkdirs($path, $perm)
{

	if(is_dir($path))
	{
		return true;
	}
	else
	{

		//$path=dirname($path);
		if(!mkdir($path, $perm))
			return false;
		mkdir($path, $perm);
		return true;
	}
}

function xtc_spaceUsed($dir)
{
	if(is_dir($dir))
	{
		if($dh = opendir($dir))
		{
			while(($file = readdir($dh)) !== false)
			{
				if(is_dir($dir . $file) && $file != '.' && $file != '..')
				{
					xtc_spaceUsed($dir . $file . '/');
				}
				else
				{
					$GLOBALS['total'] += filesize($dir . $file);
				}
			}
			closedir($dh);
		}
	}
}

function create_coupon_code($salt = "secret", $length = SECURITY_CODE_LENGTH)
{
	$ccid = md5(uniqid("", "salt"));
	$ccid .= md5(uniqid("", "salt"));
	$ccid .= md5(uniqid("", "salt"));
	$ccid .= md5(uniqid("", "salt"));
	srand((double)microtime() * 1000000); // seed the random number generator
	$length = (int)$length;
	$good_result = 0;
	while($good_result == 0)
	{
		if($length == 0)
		{
			$good_result = 1;
		}
		$random_start = @ rand(0, (128 - $length));
		$id1 = substr($ccid, $random_start, $length);
		$query = xtc_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_code = '" . xtc_db_input($id1) . "'");
		if(xtc_db_num_rows($query) == 0)
			$good_result = 1;
	}
	return $id1;
}

// Update the Customers GV account
function xtc_gv_account_update($customer_id, $gv_id)
{
	$customer_id = (int)$customer_id;
	$gv_id       = (int)$gv_id;
	
	$customer_gv_query = xtc_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . $customer_id . "'");
	$coupon_gv_query = xtc_db_query("select coupon_amount from " . TABLE_COUPONS . " where coupon_id = '" . $gv_id . "'");
	$coupon_gv = xtc_db_fetch_array($coupon_gv_query);
	if(xtc_db_num_rows($customer_gv_query) > 0)
	{
		$customer_gv = xtc_db_fetch_array($customer_gv_query);
		$new_gv_amount = $customer_gv['amount'] + $coupon_gv['coupon_amount'];
		$gv_query = xtc_db_query("update " . TABLE_COUPON_GV_CUSTOMER . " set amount = '" . $new_gv_amount . "' where customer_id = '" . $customer_id . "'");
	}
	else
	{
		$gv_query = xtc_db_query("insert into " . TABLE_COUPON_GV_CUSTOMER . " (customer_id, amount) values ('" . $customer_id . "', '" . $coupon_gv['coupon_amount'] . "')");
	}
}

// Output a day/month/year dropdown selector
function xtc_draw_date_selector($prefix, $date = '')
{
	$month_array = array();
	$month_array[1] = _JANUARY;
	$month_array[2] = _FEBRUARY;
	$month_array[3] = _MARCH;
	$month_array[4] = _APRIL;
	$month_array[5] = _MAY;
	$month_array[6] = _JUNE;
	$month_array[7] = _JULY;
	$month_array[8] = _AUGUST;
	$month_array[9] = _SEPTEMBER;
	$month_array[10] = _OCTOBER;
	$month_array[11] = _NOVEMBER;
	$month_array[12] = _DECEMBER;
	$usedate = getdate($date);
	$day = $usedate['mday'];
	$month = $usedate['mon'];
	$year = $usedate['year'];
	$date_selector = '<select name="' . $prefix . '_day">';
	for($i = 1; $i < 32; $i++)
	{
		$date_selector .= '<option value="' . $i . '"';
		if($i == $day)
			$date_selector .= 'selected';
		$date_selector .= '>' . $i . '</option>';
	}
	$date_selector .= '</select>';
	$date_selector .= '<select name="' . $prefix . '_month">';
	for($i = 1; $i < 13; $i++)
	{
		$date_selector .= '<option value="' . $i . '"';
		if($i == $month)
			$date_selector .= 'selected';
		$date_selector .= '>' . $month_array[$i] . '</option>';
	}
	$date_selector .= '</select>';
	$date_selector .= '<select name="' . $prefix . '_year">';
	for($i = 2010; $i < 2029; $i++)
	{
		$date_selector .= '<option value="' . $i . '"';
		if($i == $year)
			$date_selector .= 'selected';
		$date_selector .= '>' . $i . '</option>';
	}
	$date_selector .= '</select>';
	return $date_selector;
}

function xtc_getDownloads()
{

	$files = array();

	$dir = DIR_FS_CATALOG . 'download/';
	if($fp = opendir($dir))
	{
		while($file = readdir($fp))
		{
			if(is_file($dir . $file) && $file != '.htaccess' && $file != 'index.html')
			{
				$size = filesize($dir . $file);
				$files[] = array('id' => $file, 'text' => $file . ' | ' . xtc_format_filesize($size), 'size' => $size, 'date' => date("F d Y H:i:s.", filemtime($dir . $file)));
			} //if
		} // while
		closedir($fp);
	}
	return $files;
}

function xtc_try_upload($file = '', $destination = '', $permissions = '777', $extensions = '')
{
	$file_object = new upload($file, $destination, $permissions, $extensions);
	if($file_object->filename != '')
		return $file_object;
	else
		return false;
}

function xtc_button($value, $type = 'submit', $parameter = '', $classes = '')
{
	return '<input type="' . $type . '" class="button ' . $classes . '" onClick="this.blur();" value="' . $value . '" ' . $parameter . ' >';
}

function xtc_button_link($value, $href = 'javascript:void(null)', $parameter = '', $classes = '')
{
	return '<a href="' . $href . '" class="button ' . $classes. '" onClick="this.blur()" ' . $parameter . ' >' . $value . '</a>';
}

// BOF GM_MOD
// function for configuration values
function gm_cfg_select_option($select_array, $key_value, $key = '')
{
	if(count($select_array) === 2 && strtolower($select_array[0]) === 'true')
	{
		$inputs = '';

		for($i = 0, $n = count($select_array); $i < $n; $i++)
		{
			//if($i === 0)
			//{
			//	$onText = htmlspecialchars_wrapper($select_array[$i]);
			//	if(defined('GM_CFG_' . strtoupper($select_array[$i])))
			//	{
			//		$onText = htmlspecialchars_wrapper(constant('GM_CFG_' . strtoupper($select_array[$i])));
			//	}
			//}
			//else
			//{
			//	$offText = htmlspecialchars_wrapper($select_array[$i]);
			//	if(defined('GM_CFG_' . strtoupper($select_array[$i])))
			//	{
			//		$offText = htmlspecialchars_wrapper(constant('GM_CFG_' . strtoupper($select_array[$i])));
			//	}
			//}
			
			$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

			$checked = '';
			if($key_value == $select_array[$i])
			{
				$checked .= ' checked';
			}
			
			$inputs .= '<input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"' . $checked . ' />';
		}
		
		//$html = '<div class="gx-container checkbox-switch-wrapper">
		//				<div class="control-group" data-gx-widget="checkbox" data-checkbox-on="' . $onText . '" data-checkbox-off="' . $offText . '">';

		$html = '<div class="gx-container checkbox-switch-wrapper">
						<div data-gx-widget="checkbox">';
		
		$html .= $inputs;
		
		$html .= '		</div>
				</div>';
	}
	else
	{
		$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
		$html = '<select name="' . $name . '">';
		
		for($i = 0, $n = count($select_array); $i < $n; $i++)
		{
			$label = htmlspecialchars_wrapper($select_array[$i]);

			if(defined('GM_CFG_' . strtoupper($select_array[$i])))
			{
				$label = htmlspecialchars_wrapper(constant('GM_CFG_' . strtoupper($select_array[$i])));
			}
			
			$selected = '';
			if($key_value == $select_array[$i])
			{
				$selected .= ' selected';
			}
			
			$html .= '<option value="' . $select_array[$i] . '"' . $selected . ' /> ' . $label . '</option>';
		}

		$html .= '</select>';
	}

	return $html;
}

// function for configuration values
function gm_cfg_get_orders_status($orders_id)
{
	return xtc_draw_pull_down_menu('configuration_value', xtc_get_orders_status(), $orders_id);
}

// EOF GM_MOD

function cfg_download_order_status_checkboxes($t_values_string)
{
	$t_values_array = array();
	$t_html_output = '<div class="gx-container checkbox-switch-list">
						';

	if(strpos($t_values_string, '|') !== false)
	{
		$t_values_array = explode('|', $t_values_string);
	}
	else
	{
		$t_values_array[] = $t_values_string;
	}

	$t_orders_status_array = xtc_get_orders_status();
	
	foreach($t_orders_status_array AS $t_status_array)
	{
		$t_checked = '';
		if(in_array($t_status_array['id'], $t_values_array))
		{
			$t_checked = ' checked="checked"';
		}

		// configuration_value will be replaced by configuration_key value in configuration.php
		$t_html_output .= '<div data-gx-widget="checkbox" class="checkbox-switch-list-row"><input type="checkbox" name="configuration_value[]" value="' . $t_status_array['id'] . '"' . $t_checked . ' /> ' . $t_status_array['text'] . '</div>';
	}

	$t_html_output .= '</div>';
	
	return $t_html_output;
}

function cfg_cod_fee_form($t_values_string)
{
	$t_installed_shipping_filenames_array = array();
	$t_values_array = array();
	$t_shipping_array = array();
	$t_html_output = '';

	$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);

	if(strpos($t_values_string, '|') !== false)
	{
		$t_values_array = explode('|', $t_values_string);
	}
	else
	{
		$t_values_array[] = $t_values_string;
	}

	if(strpos(MODULE_SHIPPING_INSTALLED, ';') !== false)
	{
		$t_installed_shipping_filenames_array = explode(';', MODULE_SHIPPING_INSTALLED);
	}
	elseif(trim(MODULE_SHIPPING_INSTALLED) != '')
	{
		$t_installed_shipping_filenames_array = array(trim(MODULE_SHIPPING_INSTALLED));
	}

	foreach($t_installed_shipping_filenames_array AS $t_filename)
	{
		$t_module_name = str_replace('.php', '', $t_filename);
		if($t_module_name !== 'selfpickup')
		{
			$t_shipping_array[$t_module_name] = '';
		}
	}

	if(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')
	{
		$t_shipping_array['free'] = '';
	}

	for($i = 0; $i < count($t_values_array); $i++)
	{
		if($i % 2 == 0)
		{
			$t_module_name = basename($t_values_array[$i]);
		}
		elseif(isset($t_shipping_array[$t_module_name]))
		{
			$t_shipping_array[$t_module_name] = $t_values_array[$i];
		}
	}

	foreach($t_shipping_array AS $t_module_name => $t_rules)
	{
		$t_html_output .= '<input type="hidden" name="configuration[MODULE_ORDER_TOTAL_COD_FEE_RULES][]" value="' . $t_module_name . '" />';

		if(!defined('MODULE_SHIPPING_' . strtoupper($t_module_name) . '_TEXT_TITLE'))
		{
			$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/shipping/' . $t_module_name . '.php');
		}

		$t_shipping_name = $t_module_name;

		if(defined('MODULE_SHIPPING_' . strtoupper($t_module_name) . '_TEXT_TITLE'))
		{
			$t_shipping_name = constant('MODULE_SHIPPING_' . strtoupper($t_module_name) . '_TEXT_TITLE') . ' (' . $t_module_name . ')';
		}

		$t_html_output .= '<strong>' . $t_shipping_name . '</strong><br /><input type="text" name="configuration[MODULE_ORDER_TOTAL_COD_FEE_RULES][]" value="' . htmlspecialchars_decode($t_rules) . '" /><br /><br />';
	}

	if(empty($t_shipping_array))
	{
		$t_html_output = TEXT_SHIPPING_ERROR;
	}

	return $t_html_output;
}

function cfg_pull_down_timezones($p_timezone)
{
	$t_timezones_array = timezone_identifiers_list();

	$t_pull_down_html = '<select name="DATE_TIMEZONE">';

	foreach($t_timezones_array as $t_timezone)
	{
		$t_selected = '';
		if($t_timezone === $p_timezone)
		{
			$t_selected = ' selected="selected"';
		}

		$t_pull_down_html .= '<option value="' . $t_timezone . '"' . $t_selected . '>' . $t_timezone . '</option>';
	}

	$t_pull_down_html .= '</select>';

	return $t_pull_down_html;
}