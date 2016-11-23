<?php
/* --------------------------------------------------------------
   template_configuration.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

$customConfigPath   = 'templates/' . CURRENT_TEMPLATE . '/admin/configuration.php';
$fsCustomConfigPath = DIR_FS_DOCUMENT_ROOT . $customConfigPath;

if(is_file($fsCustomConfigPath))
{
	require_once($fsCustomConfigPath);
	exit;
}

class TemplateConfiguration
{
	function get_styles_by_attr($p_attr)
	{
		$t_styles_array = array();

		switch($p_attr)
		{
			case 'font-family':
				$t_styles_array = $this->get_styles('font-family', false);

				break;
			case 'color':
				$t_styles_array = $this->get_styles('color');

				break;
			case 'background-color':
				$t_styles_array = $this->get_styles('background-color');

				break;
			case 'border-color':
				$t_styles_array = $this->get_styles('border-%color');

				break;
		}

		return $t_styles_array;
	}


	function get_selectors($p_attr, $p_value)
	{
		$c_attr = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $p_attr) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$c_value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $p_value) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$t_selectors_array = array();

		$t_short_color = '';
		if(preg_match('/^#[0-9a-fA-F]{6}$/', $c_value) == 1 && substr($c_value, 1, 3) == substr($c_value, -3))
		{
			$t_short_color = " OR sc.style_value = '" . substr($c_value, 0, 4) . "' ";
		}

		$t_sql = "SELECT
						s.gm_css_style_id,
						s.style_name
					FROM
						gm_css_style s,
						gm_css_style_content sc
					WHERE
						s.template_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], CURRENT_TEMPLATE) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
						s.gm_css_style_id = sc.gm_css_style_id AND
						sc.style_attribute LIKE '" . $c_attr . "' AND
						(sc.style_value = '" . $c_value . "' " . $t_short_color . ")
					GROUP BY s.style_name
					ORDER BY s.style_name";
		$t_result = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$t_selectors_array[] = array('ID' => $t_result_array['gm_css_style_id'],
										 'NAME' => $t_result_array['style_name']);
		}

		return $t_selectors_array;
	}


	function get_styles($p_attr, $t_lowercase = true)
	{
		$c_attr = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $p_attr) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$t_styles_array = array();

		if($t_lowercase)
		{
			$t_sql = "SELECT
							LOWER(sc.style_value) as style_value
						FROM
							gm_css_style s,
							gm_css_style_content sc
						WHERE
							s.template_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], CURRENT_TEMPLATE) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
							s.gm_css_style_id = sc.gm_css_style_id AND
							sc.style_attribute LIKE '" . $c_attr . "'
						GROUP BY style_value
						ORDER BY style_value";
		}
		else
		{
			$t_sql = "SELECT
							style_value
						FROM
							gm_css_style s,
							gm_css_style_content sc
						WHERE
							s.template_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], CURRENT_TEMPLATE) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
							s.gm_css_style_id = sc.gm_css_style_id AND
							sc.style_attribute LIKE '" . $c_attr . "'
						GROUP BY style_value
						ORDER BY style_value";
		}
		$t_result = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$t_styles_array[] = $t_result_array['style_value'];
		}

		if($c_attr == 'color' || $c_attr == 'background-color' || $c_attr == 'border-%color')
		{
			for($i = 0; $i < count($t_styles_array); $i++)
			{
				if(strlen(trim($t_styles_array[$i])) == '4')
				{
					$t_styles_array[$i] .= substr($t_styles_array[$i], 1);
				}
			}
		}

		$t_styles_array = array_unique($t_styles_array);
		sort($t_styles_array); // convert to numeric array

		return $t_styles_array;
	}


	function set_style($p_attr, $p_old_value, $p_new_value, $p_style_ids_array)
	{
		$c_attr = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], trim($p_attr)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$c_old_value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], trim($p_old_value)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$c_new_value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], trim($p_new_value)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$c_style_ids_array = array();
		$t_success = false;

		if(is_array($p_style_ids_array))
		{
			foreach($p_style_ids_array AS $t_id)
			{
				$c_style_ids_array[] = (int)$t_id;
			}

			if(!empty($c_style_ids_array))
			{
				switch($c_attr)
				{
					case 'color':
					case 'background-color':
					case 'border-%color':

						if(preg_match('/^#[0-9a-fA-F]{3}$/', $c_new_value) == 1)
						{
							$c_new_value .= substr($c_new_value, 1);
						}

						if(preg_match('/^#[0-9a-fA-F]{6}$/', $c_new_value) == 1 || $c_new_value == 'transparent')
						{
							$t_short_color = '';
							if(substr($c_old_value, 1, 3) == substr($c_old_value, -3))
							{
								$t_short_color = " OR style_value = '" . substr($c_old_value, 0, 4) . "' ";
							}

							$t_sql = "UPDATE gm_css_style_content
											SET style_value = '" . $c_new_value . "'
											WHERE
												style_attribute LIKE '" . $c_attr . "' AND
												(style_value = '" . $c_old_value . "'
												" . $t_short_color . " ) AND
												gm_css_style_id IN (" . implode(',', $c_style_ids_array) . ")";

							xtc_db_query($t_sql);
							$t_success = true;
						}

						break;
					case 'font-family':
						$t_sql = "UPDATE gm_css_style_content
											SET style_value = '" . $c_new_value . "'
											WHERE
												style_attribute LIKE '" . $c_attr . "' AND
												style_value = '" . $c_old_value . "' AND
												gm_css_style_id IN (" . implode(',', $c_style_ids_array) . ")";
						xtc_db_query($t_sql);

						$t_sql = "SELECT gm_css_style_fonts_id
									FROM gm_css_style_fonts
									WHERE font = '" . $c_new_value . "'";
						$t_result = xtc_db_query($t_sql);
						if(xtc_db_num_rows($t_result) == 0)
						{
							$t_sql = "INSERT INTO gm_css_style_fonts
										SET font = '" . $c_new_value . "'";
							xtc_db_query($t_sql);
						}
						$t_success = true;

						break;
				}
			}
		}

		return $t_success;
	}
}

$coo_template_configuration = new TemplateConfiguration();

if(isset($_POST['save']))
{
	if(isset($_POST['shop_align']))
	{
		$t_margin_left = 'auto';
		$t_margin_right = 'auto';

		switch($_POST['shop_align'])
		{
			case 'left':
				$t_margin_left = '0';

				break;
			case 'right':
				$t_margin_right = '0';
				break;
		}

		$t_css_selectors_array = array();
		$t_css_selectors_array = array('#top_navi', '#container', '#footer');

		for($i = 0; $i < count($t_css_selectors_array); $i++)
		{
			$t_sql = "SELECT
							sc.gm_css_style_content_id,
							sc.style_value
						FROM
							gm_css_style s,
							gm_css_style_content sc
						WHERE
							s.template_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], CURRENT_TEMPLATE) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
							s.style_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_css_selectors_array[$i]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
							s.gm_css_style_id = sc.gm_css_style_id AND
							sc.style_attribute = 'margin'";
			$t_result = xtc_db_query($t_sql);
			if(xtc_db_num_rows($t_result) == 0)
			{
				$t_sql = "SELECT
								sc.gm_css_style_content_id,
								sc.style_value
							FROM
								gm_css_style s,
								gm_css_style_content sc
							WHERE
								s.template_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], CURRENT_TEMPLATE) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
								s.style_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_css_selectors_array[$i]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
								s.gm_css_style_id = sc.gm_css_style_id AND
								sc.style_attribute = 'margin-left'";
				$t_result = xtc_db_query($t_sql);
				if(xtc_db_num_rows($t_result) == 1)
				{
					$t_margin_left_array = xtc_db_fetch_array($t_result);
					$t_sql = "SELECT
									sc.gm_css_style_content_id,
									sc.style_value
								FROM
									gm_css_style s,
									gm_css_style_content sc
								WHERE
									s.template_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], CURRENT_TEMPLATE) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
									s.style_name = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_css_selectors_array[$i]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "' AND
									s.gm_css_style_id = sc.gm_css_style_id AND
									sc.style_attribute = 'margin-right'";
					$t_result = xtc_db_query($t_sql);
					if(xtc_db_num_rows($t_result) == 1)
					{
						$t_margin_right_array = xtc_db_fetch_array($t_result);

						$t_sql = "UPDATE gm_css_style_content
									SET style_value = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_margin_left) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "'
									WHERE gm_css_style_content_id = '" . (int)$t_margin_left_array['gm_css_style_content_id'] . "'";
						$t_result = xtc_db_query($t_sql);

						$t_sql = "UPDATE gm_css_style_content
									SET style_value = '" . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_margin_right) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . "'
									WHERE gm_css_style_content_id = '" . (int)$t_margin_right_array['gm_css_style_content_id'] . "'";
						$t_result = xtc_db_query($t_sql);
					}
				}
			}
			elseif(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				$t_sql = "UPDATE gm_css_style_content
									SET style_value = '0 " . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_margin_right) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . " 0 " .  ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_margin_left) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . " 0'
									WHERE gm_css_style_content_id = '" . (int)$t_result_array['gm_css_style_content_id'] . "'";
				$t_result = xtc_db_query($t_sql);
			}
		}
	}

	if(isset($_POST['main_show_qty_info']) && $_POST['main_show_qty_info'] == 'true')
	{
		gm_set_conf('MAIN_SHOW_QTY_INFO', 'true');
	}
	else
	{
		gm_set_conf('MAIN_SHOW_QTY_INFO', 'false');
	}

	if(isset($_POST['main_show_attributes']) && $_POST['main_show_attributes'] == 'true')
	{
		gm_set_conf('MAIN_SHOW_ATTRIBUTES', 'true');
	}
	else
	{
		gm_set_conf('MAIN_SHOW_ATTRIBUTES', 'false');
	}

	if(isset($_POST['main_show_graduated_prices']) && $_POST['main_show_graduated_prices'] == 'true')
	{
		gm_set_conf('MAIN_SHOW_GRADUATED_PRICES', 'true');
	}
	else
	{
		gm_set_conf('MAIN_SHOW_GRADUATED_PRICES', 'false');
	}

	if(isset($_POST['main_show_qty']) && $_POST['main_show_qty'] == 'true')
	{
		gm_set_conf('MAIN_SHOW_QTY', 'true');
	}
	else
	{
		gm_set_conf('MAIN_SHOW_QTY', 'false');
	}

	if(isset($_POST['main_view_mode_tiled']) && $_POST['main_view_mode_tiled'] == 'true')
	{
		gm_set_conf('MAIN_VIEW_MODE_TILED', 'true');
	}
	else
	{
		gm_set_conf('MAIN_VIEW_MODE_TILED', 'false');
	}

	if(isset($_POST['show_wishlist']) && $_POST['show_wishlist'] == 'true')
	{
		gm_set_conf('GM_SHOW_WISHLIST', 'true');
	}
	else
	{
		gm_set_conf('GM_SHOW_WISHLIST', 'false');
	}

	if(isset($_POST['quick_search']) && $_POST['quick_search'] == 'true')
	{
		gm_set_conf('GM_QUICK_SEARCH', 'true');
	}
	else
	{
		gm_set_conf('GM_QUICK_SEARCH', 'false');
	}

	if(isset($_POST['show_gallery']) && $_POST['show_gallery'] == 'true')
	{
		gm_set_conf('SHOW_GALLERY', 'true');
	}
	else
	{
		gm_set_conf('SHOW_GALLERY', 'false');
	}

	if(isset($_POST['show_zoom']) && $_POST['show_zoom'] == 'true')
	{
		gm_set_conf('SHOW_ZOOM', 'true');
	}
	else
	{
		gm_set_conf('SHOW_ZOOM', 'false');
	}

	if(isset($_POST['show_top_language_selection']) && $_POST['show_top_language_selection'] == 'true')
	{
		gm_set_conf('SHOW_TOP_LANGUAGE_SELECTION', 'true');
	}
	else
	{
		gm_set_conf('SHOW_TOP_LANGUAGE_SELECTION', 'false');
	}

	if(isset($_POST['show_top_currency_selection']) && $_POST['show_top_currency_selection'] == 'true')
	{
		gm_set_conf('SHOW_TOP_CURRENCY_SELECTION', 'true');
	}
	else
	{
		gm_set_conf('SHOW_TOP_CURRENCY_SELECTION', 'false');
	}

	if(isset($_POST['show_top_country_selection']) && $_POST['show_top_country_selection'] == 'true')
	{
		gm_set_conf('SHOW_TOP_COUNTRY_SELECTION', 'true');
	}
	else
	{
		gm_set_conf('SHOW_TOP_COUNTRY_SELECTION', 'false');
	}
	
	if(isset($_POST['cat_menu_top']) && $_POST['cat_menu_top'] == 'true')
	{
		gm_set_conf('CAT_MENU_TOP', 'true');
	}
	else
	{
		gm_set_conf('CAT_MENU_TOP', 'false');
	}

	if(isset($_POST['cat_menu_left']) && $_POST['cat_menu_left'] == 'true')
	{
		gm_set_conf('CAT_MENU_LEFT', 'true');
	}
	else
	{
		gm_set_conf('CAT_MENU_LEFT', 'false');
	}

	if(isset($_POST['cat_menu_classic']) && $_POST['cat_menu_classic'] == 'true')
	{
		gm_set_conf('CAT_MENU_CLASSIC', 'true');
	}
	else
	{
		gm_set_conf('CAT_MENU_CLASSIC', 'false');
	}

	gm_set_conf('GM_SHOW_CAT', $_POST['GM_SHOW_CAT']);

	if(isset($_POST['show_split_menu']) && $_POST['show_split_menu'] == 'true')
	{
		gm_set_conf('SHOW_SPLIT_MENU', 'true');
	}
	else
	{
		gm_set_conf('SHOW_SPLIT_MENU', 'false');
	}

	if(isset($_POST['show_flyover']) && $_POST['show_flyover'] == '1')
	{
		gm_set_conf('GM_SHOW_FLYOVER', '1');
	}
	else
	{
		gm_set_conf('GM_SHOW_FLYOVER', '0');
	}

	if(isset($_POST['show_footer']) && $_POST['show_footer'] == 'true')
	{
		gm_set_conf('SHOW_FOOTER', 'true');
	}
	else
	{
		gm_set_conf('SHOW_FOOTER', 'false');
	}

	if(isset($_POST['show_bookmarking']) && $_POST['show_bookmarking'] == 'true')
	{
		gm_set_conf('SHOW_BOOKMARKING', 'true');
	}
	else
	{
		gm_set_conf('SHOW_BOOKMARKING', 'false');
	}

	if(isset($_POST['show_facebook']) && $_POST['show_facebook'] == 'true')
	{
		gm_set_conf('SHOW_FACEBOOK', 'true');
	}
	else
	{
		gm_set_conf('SHOW_FACEBOOK', 'false');
	}

	if(isset($_POST['show_twitter']) && $_POST['show_twitter'] == 'true')
	{
		gm_set_conf('SHOW_TWITTER', 'true');
	}
	else
	{
		gm_set_conf('SHOW_TWITTER', 'false');
	}

	if(isset($_POST['show_googleplus']) && $_POST['show_googleplus'] == 'true')
	{
		gm_set_conf('SHOW_GOOGLEPLUS', 'true');
	}
	else
	{
		gm_set_conf('SHOW_GOOGLEPLUS', 'false');
	}

	if(isset($_POST['show_pinterest']) && $_POST['show_pinterest'] == 'true')
	{
		gm_set_conf('SHOW_PINTEREST', 'true');
	}
	else
	{
		gm_set_conf('SHOW_PINTEREST', 'false');
	}

	if(isset($_POST['show_print']) && $_POST['show_print'] == 'true')
	{
		gm_set_conf('SHOW_PRINT', 'true');
	}
	else
	{
		gm_set_conf('SHOW_PRINT', 'false');
	}

	if(isset($_POST['tell_a_friend']) && $_POST['tell_a_friend'] == 'true')
	{
		gm_set_conf('GM_TELL_A_FRIEND', 'true');
	}
	else
	{
		gm_set_conf('GM_TELL_A_FRIEND', 'false');
	}

	$t_specials_startpage = trim($_POST['specials_startpage']);
	$t_new_products_startpage = trim($_POST['new_products_startpage']);

	gm_set_conf('GM_SPECIALS_STARTPAGE', (int)$t_specials_startpage);
	gm_set_conf('GM_NEW_PRODUCTS_STARTPAGE', (int)$t_new_products_startpage);

	$customizer_position = trim($_POST['customizer_position']);

	gm_set_conf('CUSTOMIZER_POSITION', (int)$customizer_position);
}
elseif(isset($_POST['send']))
{
	$t_suhosin_error = false;

	if(isset($_POST['gm_css_style_ids']) && is_numeric($_POST['checked_fields_count']) && (int)$_POST['checked_fields_count'] == count($_POST['gm_css_style_ids']))
	{
		$t_success = $coo_template_configuration->set_style($_POST['type'], $_POST['old'], $_POST['new'], $_POST['gm_css_style_ids']);
		if($t_success)
		{
			@unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');
		}
	}
	elseif(is_numeric($_POST['checked_fields_count']) && (int)$_POST['checked_fields_count'] > count($_POST['gm_css_style_ids']))
	{
		$t_suhosin_error = true;
	}
}
?>
	<html>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<style type="text/css">
			body
			{
				background-color: #373D4D;
				margin: 0px;
				padding: 20px 0 0 20px;
				color: #fff;
				font-family: Arial,Tahoma,Verdana,sans-serif;
				font-size: 12px;
			}

			table tr td
			{
				background-color: #4B5163;
				font-size: 14px;
			}

			.new_value_input,
			.button,
			#gm_color_box input.button,
			.short_input_field
			{
				background-color: #414756;
				border: 1px solid #585E71;
				color: #fff;
				font-size: 14px;
				margin-right: 5px;
				height: 24px;
			}

			body .gm_click
			{
				width: 24px;
				height: 24px;
				border: 1px solid #585E71;
				cursor: pointer;
				margin-right: 5px;
				float: left;
			}

			.css_attribute
			{
				background-color: #4B5163;
				padding: 5px 0px 5px 5px;
				margin: 2px;
				width: 536px;
			}

			.css_attribute_title
			{
				background-color: #4B5163;
				padding: 5px 0px 5px 5px;
				margin: 2px;
				width: 536px;
				font-size: 14px;
			}

			form
			{
				margin: 0;
				padding: 0;
				overflow: hidden;
			}

			.show_selectors
			{
				text-decoration: underline;
				cursor: pointer;
			}

			#gm_color_box
			{
				padding: 5px;
				width:208px;
			}

			#gm_color_box input
			{
				border: 1px solid black;
				background-color: #eee;
			}

			#gm_color_box .save, #gm_color_box .close
			{
				width: 80px;
			}

			form input.font_input
			{
				width: 250px;
			}

			.selectors
			{
				display: none;
				clear: left;
			}
		</style>
		<link rel="stylesheet" href="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/farbtastic/farbtastic.css" type="text/css" />
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/jquery-migrate.min.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/hoverIntent/hoverIntent.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/jquery.dimensions.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/plugins/farbtastic/farbtastic.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/gm_style_edit.js"></script>
		<script type="text/javascript"><!--

			//--></script>
	</head>
	<body>
	<?php
	$coo_template_control =& MainFactory::create_object('TemplateControl', array(), true);
	if($coo_template_control->get_template_presentation_version() < FIRST_GX2_TEMPLATE_VERSION)
	{
		echo NO_TEMPLATE_CONFIGURATION;
	}
	else
	{
		?>
		<div id="gm_color_box" style="display:none;">
			<div id="colorpicker"></div>
			<div align="center">
				<input type="text" id="color" name="color" value="#123456" />
				<input type="hidden" id="actual" value="" /><br /><br />
				<input type="button" class="save button" style="cursor:pointer;width:90px;float:left" value="<?php echo BUTTON_SAVE; ?>">
				<input type="button" class="close button" style="cursor:pointer;width:90px;float:right" value="<?php echo BUTTON_CLOSE; ?>">
			</div>
		</div>

		<form id="template_configuration" name="template_configuration" action="<?php echo xtc_href_link('template_configuration.php'); ?>" method="post">
			<table border="0" width="545" cellspacing="2" cellpadding="5" >

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(isset($_POST['shop_align']) && $_POST['shop_align'] == 'left') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="radio" name="shop_align" value="left"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOP_ALIGN_LEFT_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(isset($_POST['shop_align']) && $_POST['shop_align'] == 'center') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="radio" name="shop_align" value="center"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOP_ALIGN_CENTER_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(isset($_POST['shop_align']) && $_POST['shop_align'] == 'right') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="radio" name="shop_align" value="right"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOP_ALIGN_RIGHT_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('MAIN_SHOW_QTY_INFO') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="main_show_qty_info" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo MAIN_SHOW_QTY_INFO_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('MAIN_SHOW_ATTRIBUTES') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="main_show_attributes" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo MAIN_SHOW_ATTRIBUTES_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('MAIN_SHOW_GRADUATED_PRICES') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="main_show_graduated_prices" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo MAIN_SHOW_GRADUATED_PRICES_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('MAIN_SHOW_QTY') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="main_show_qty" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo MAIN_SHOW_QTY_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('MAIN_VIEW_MODE_TILED') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="main_view_mode_tiled" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo MAIN_VIEW_MODE_TILED_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('GM_SHOW_WISHLIST') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_wishlist" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_WISHLIST_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('GM_QUICK_SEARCH') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="quick_search" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_QUICK_SEARCH_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('GM_SHOW_FLYOVER') == '1') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_flyover" value="1"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_FLYOVER_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_GALLERY') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_gallery" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_GALLERY_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_ZOOM') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_zoom" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_ZOOM_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('CAT_MENU_TOP') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="cat_menu_top" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo CAT_MENU_TOP_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('CAT_MENU_LEFT') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="cat_menu_left" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo CAT_MENU_LEFT_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('CAT_MENU_CLASSIC') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="cat_menu_classic" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo CAT_MENU_CLASSIC_TEXT; ?><br /><br />
						<select name="GM_SHOW_CAT" style="width:70px">
							<?php
							if(gm_get_conf('GM_SHOW_CAT') == 'none')
							{
								$t_none = 'selected';
							}
							?>
							<option <?php echo $t_none; ?> value="none">
								<?php echo GM_SHOW_CAT_NONE; ?>
							</option>

							<?php
							if(gm_get_conf('GM_SHOW_CAT') == 'child')
							{
								$t_child = 'selected';
							}
							?>
							<option <?php echo $t_child; ?> value="child">
								<?php echo GM_SHOW_CAT_CHILD; ?>
							</option>

							<?php
							if(gm_get_conf('GM_SHOW_CAT') == 'all')
							{
								$t_all = 'selected';
							}
							?>
							<option <?php echo $t_all; ?> value="all">
								<?php echo GM_SHOW_CAT_ALL; ?>
							</option>
						</select> <?php echo GM_SHOW_CAT_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_SPLIT_MENU') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_split_menu" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_SPLIT_MENU_SELECTION_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_TOP_LANGUAGE_SELECTION') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_top_language_selection" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_TOP_LANGUAGE_SELECTION_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_TOP_CURRENCY_SELECTION') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_top_currency_selection" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_TOP_CURRENCY_SELECTION_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_TOP_COUNTRY_SELECTION') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_top_country_selection" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_TOP_COUNTRY_SELECTION_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_FOOTER') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_footer" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_FOOTER_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_FACEBOOK') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_facebook" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_FACEBOOK_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_TWITTER') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_twitter" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_TWITTER_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_GOOGLEPLUS') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_googleplus" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_GOOGLEPLUS_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_PINTEREST') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_pinterest" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_PINTEREST_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('GM_TELL_A_FRIEND') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="tell_a_friend" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_TELL_A_FRIEND_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_PRINT') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_print" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_PRINT_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<?php if(gm_get_conf('SHOW_BOOKMARKING') == 'true') $t_checked = 'checked="checked"'; else $t_checked = '';	?>
						<input type="checkbox" name="show_bookmarking" value="true"<?php echo $t_checked; ?> />
					</td>
					<td class="main">
						<?php echo SHOW_BOOKMARKING_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<input style="width:30px" class="short_input_field" type="text" name="specials_startpage" value="<?php echo gm_get_conf('GM_SPECIALS_STARTPAGE'); ?>" size="3" />
					</td>
					<td class="main">
						<?php echo SPECIALS_STARTPAGE_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<input style="width:30px" class="short_input_field" type="text" name="new_products_startpage" value="<?php echo gm_get_conf('GM_NEW_PRODUCTS_STARTPAGE'); ?>" size="3" />
					</td>
					<td class="main">
						<?php echo NEW_PRODUCTS_STARTPAGE_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<input type="radio" name="customizer_position" value="1"<?php if(gm_get_conf('CUSTOMIZER_POSITION') == '1') echo ' checked="checked"'; ?> />
					</td>
					<td class="main">
						<?php echo CUSTOMIZER_POSITION_1_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<input type="radio" name="customizer_position" value="2"<?php if(gm_get_conf('CUSTOMIZER_POSITION') == '2') echo ' checked="checked"'; ?> />
					</td>
					<td class="main">
						<?php echo CUSTOMIZER_POSITION_2_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td width="40" class="main" valign="top" align="left">
						<input type="radio" name="customizer_position" value="3"<?php if(gm_get_conf('CUSTOMIZER_POSITION') == '3') echo ' checked="checked"'; ?> />
					</td>
					<td class="main">
						<?php echo CUSTOMIZER_POSITION_3_TEXT; ?>
					</td>
				</tr>

				<tr>
					<td colspan="2" class="main">
						<input type="submit" class="button" name="save" value="<?php echo SUBMIT_TEMPLATE_CONFIGURATION; ?>" />
					</td>
				</tr>
			</table>
		</form>

	<?php

	$t_color_array = $coo_template_configuration->get_styles_by_attr('color');

	echo '<br /><br />';
	echo '<div class="css_attribute_title">';
	echo CSS_COLORS_HEADING;
	echo '</div>';

	foreach($t_color_array AS $t_value)
	{
		$t_selectors_array = array();
		$t_selectors_array =  $coo_template_configuration->get_selectors('color', $t_value);

		echo '<div class="css_attribute">';
		echo '<form action="' . xtc_href_link('template_configuration.php') . '" method="post">';
		echo '<input type="button" style="background-color: ' . htmlspecialchars_wrapper($t_value) . '" id="color_colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" class="gm_click"><input type="text" class="new_value_input" id="colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" value="' . htmlspecialchars_wrapper($t_value) . '" name="colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" style="float: left" />';
		echo '<input type="hidden" name="old" value="' . htmlspecialchars_wrapper($t_value) . '" />';
		echo '<input type="hidden" name="new" value="' . htmlspecialchars_wrapper($t_value) . '" class="new_value" />';
		echo '<input type="hidden" name="type" value="color" />';
		echo '<input type="hidden" name="checked_fields_count" value="" />';
		echo '<input type="submit" class="button" name="send" value="' . SUBMIT_TEMPLATE_CONFIGURATION . '" style="float: left" />';

		echo '<span id="selectors_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" class="show_selectors">' . CSS_SHOW_TEXT . '</span>';
		echo '<div class="selectors_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . ' selectors">';
		echo '<input type="checkbox" name="ALL" value="1" checked="checked" class="check_all" /><strong>' . CSS_ALL_TEXT . '</strong><br />';
		foreach($t_selectors_array AS $t_key => $t_value_array)
		{
			echo '<input type="checkbox" name="gm_css_style_ids[]" value="' . $t_value_array['ID'] . '" checked="checked" />' . $t_value_array['NAME'] . '<br />';
		}
		echo '</div>';
		echo '</form>';
		echo '</div>';
	}

	echo '<br /><br />';

	$t_color_array = $coo_template_configuration->get_styles_by_attr('background-color');

	echo '<div class="css_attribute_title">';
	echo CSS_BACKGROUND_COLORS_HEADING;
	echo '</div>';

	foreach($t_color_array AS $t_value)
	{
		$t_selectors_array = array();
		$t_selectors_array =  $coo_template_configuration->get_selectors('background-color', $t_value);

		echo '<div class="css_attribute">';
		echo '<form action="' . xtc_href_link('template_configuration.php') . '" method="post">';
		echo '<input type="button" style="background-color: ' . htmlspecialchars_wrapper($t_value) . '" id="color_background_colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" class="gm_click"><input type="text" class="new_value_input" id="background_colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" value="' . htmlspecialchars_wrapper($t_value) . '" name="background_colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '">';
		echo '<input type="hidden" name="old" value="' . htmlspecialchars_wrapper($t_value) . '" />';
		echo '<input type="hidden" name="new" value="' . htmlspecialchars_wrapper($t_value) . '" class="new_value" />';
		echo '<input type="hidden" name="type" value="background-color" />';
		echo '<input type="hidden" name="checked_fields_count" value="" />';
		echo '<input type="submit" class="button" name="send" value="' . SUBMIT_TEMPLATE_CONFIGURATION . '" />';

		echo '<span id="selectors_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" class="show_selectors">' . CSS_SHOW_TEXT . '</span>';
		echo '<div class="selectors_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . ' selectors">';
		echo '<input type="checkbox" name="ALL" value="1" checked="checked" class="check_all" /><strong>' . CSS_ALL_TEXT . '</strong><br />';
		foreach($t_selectors_array AS $t_key => $t_value_array)
		{
			echo '<input type="checkbox" name="gm_css_style_ids[]" value="' . $t_value_array['ID'] . '" checked="checked" />' . $t_value_array['NAME'] . '<br />';
		}
		echo '</div>';
		echo '</form>';
		echo '</div>';
	}

	echo '<br /><br />';

	$t_color_array = $coo_template_configuration->get_styles_by_attr('border-color');

	echo '<div class="css_attribute_title">';
	echo CSS_BORDERS_COLORS_HEADING;
	echo '</div>';

	foreach($t_color_array AS $t_value)
	{
		$t_selectors_array = array();
		$t_selectors_array =  $coo_template_configuration->get_selectors('border-%color', $t_value);

		echo '<div class="css_attribute">';
		echo '<form action="' . xtc_href_link('template_configuration.php') . '" method="post">';
		echo '<input type="button" style="background-color: ' . htmlspecialchars_wrapper($t_value) . '" id="color_border_colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" class="gm_click"><input type="text" class="new_value_input" id="border_colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" value="' . htmlspecialchars_wrapper($t_value) . '" name="border_colour_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '">';
		echo '<input type="hidden" name="old" value="' . htmlspecialchars_wrapper($t_value) . '" />';
		echo '<input type="hidden" name="new" value="' . htmlspecialchars_wrapper($t_value) . '" class="new_value" />';
		echo '<input type="hidden" name="type" value="border-%color" />';
		echo '<input type="hidden" name="checked_fields_count" value="" />';
		echo '<input type="submit" class="button" name="send" value="' . SUBMIT_TEMPLATE_CONFIGURATION . '" />';

		echo '<span id="selectors_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" class="show_selectors">' . CSS_SHOW_TEXT . '</span>';
		echo '<div class="selectors_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . ' selectors">';
		echo '<input type="checkbox" name="ALL" value="1" checked="checked" class="check_all" /><strong>' . CSS_ALL_TEXT . '</strong><br />';
		foreach($t_selectors_array AS $t_key => $t_value_array)
		{
			echo '<input type="checkbox" name="gm_css_style_ids[]" value="' . $t_value_array['ID'] . '" checked="checked" />' . $t_value_array['NAME'] . '<br />';
		}
		echo '</div>';
		echo '</form>';
		echo '</div>';
	}

	$t_fonts_array = $coo_template_configuration->get_styles_by_attr('font-family');

	echo '<br /><br />';
	echo '<div class="css_attribute_title">';
	echo CSS_FONTS_HEADING;
	echo '</div>';

	foreach($t_fonts_array AS $t_value)
	{
		$t_selectors_array = array();
		$t_selectors_array =  $coo_template_configuration->get_selectors('font-family', $t_value);

		echo '<div class="css_attribute">';
		echo '<form action="' . xtc_href_link('template_configuration.php') . '" method="post">';
		echo '<input type="text" class="new_value_input font_input" id="font_family_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" value="' . htmlspecialchars_wrapper($t_value) . '" name="font_family_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '">';
		echo '<input type="hidden" name="old" value="' . htmlspecialchars_wrapper($t_value) . '" />';
		echo '<input type="hidden" name="new" value="' . htmlspecialchars_wrapper($t_value) . '" class="new_value" />';
		echo '<input type="hidden" name="type" value="font-family" />';
		echo '<input type="hidden" name="checked_fields_count" value="" />';
		echo '<input type="submit" class="button" name="send" value="' . SUBMIT_TEMPLATE_CONFIGURATION . '" />';

		echo '<span id="selectors_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . '" class="show_selectors">' . CSS_SHOW_TEXT . '</span>';
		echo '<div class="selectors_' . preg_replace('/[^0-9a-zA-Z]/', '-', $t_value) . ' selectors">';
		echo '<input type="checkbox" name="ALL" value="1" checked="checked" class="check_all" /><strong>' . CSS_ALL_TEXT . '</strong><br />';
		foreach($t_selectors_array AS $t_key => $t_value_array)
		{
			echo '<input type="checkbox" name="gm_css_style_ids[]" value="' . $t_value_array['ID'] . '" checked="checked" />' . $t_value_array['NAME'] . '<br />';
		}
		echo '</div>';
		echo '</form>';
		echo '</div>';
	}

	echo '<br /><br />';
	?>
	<?php
	if(isset($t_suhosin_error) && $t_suhosin_error === true)
	{
	?>
		<script>
			<!--
			alert('<?php echo sprintf(SUHOSIN_ERROR_TEXT,  count((array)$_POST['gm_css_style_ids']), (int)$_POST['checked_fields_count']); ?>');
			//-->
		</script>
	<?php
	}
	elseif(isset($_POST['save']) || isset($_POST['send']))
	{
	?>
		<script>
			<!--
			var t_reload = confirm('<?php echo RELOAD_PAGE_TEXT; ?>');
			if(t_reload)
			{
				self.parent.location.reload(true);
			}
			//-->
		</script>
	<?php
	}
	?>
		<script>
			<!--
			$(document).ready(function()
			{
				$('.check_all').click(function()
				{
					if($(this).prop('checked') == true)
					{
						$(this).closest('div').find('input[type="checkbox"]').prop('checked', true);
					}
					else
					{
						$(this).closest('div').find('input[type="checkbox"]').prop('checked', false);
					}
				});

				$('.show_selectors').click(function()
				{
					$('.' + $(this).attr('id')).toggle();
				});

				$('form').submit(function()
				{
					$(this).find('.new_value').val($(this).find('.new_value_input').val());
					$(this).find('input[name="checked_fields_count"]').val($(this).find('input[name="gm_css_style_ids[]"]:checked').length);

					return true;
				});
			});

			(function () {
				'use strict';

				var $splitMenu = $('input[name=show_split_menu]');
				var $catLeftClassic = $('input[name=cat_menu_classic]');
				var $catLeft= $('input[name=cat_menu_left]');


				var init = function () {

					var statusSplitMenu = false;
					var thiz = this;

					if ($splitMenu.attr('checked') === true) {
//						$splitMenu.addClass();
					}
				};

				var testAllowedToSplitMenu = function () {
					var checked;

					if ($catLeft.prop('checked')) {
						$catLeftClassic.removeAttr('checked');
					}

					if ($catLeftClassic.prop('checked')) {
						$catLeft.removeAttr('checked');
					}

					checked = $catLeftClassic.prop('checked');

					return checked;
				};

				var deactivateElement = function () {
					$splitMenu.attr('disabled', 'disabled');
					$splitMenu.removeAttr('checked');
				};

				var activateElement = function () {
					$splitMenu.removeAttr('disabled');

				};

				var startStatus = function(){
					if($catLeft.prop('checked')){
						deactivateElement();
					}
				};

				$catLeft.on('change', function () {

					if($catLeft.prop('checked')) {
						$catLeftClassic.removeAttr('checked');
					}

					if (testAllowedToSplitMenu() === true) {
						activateElement();
					} else {
						deactivateElement();
					}
				});

				$catLeftClassic.on('change', function () {

					if($catLeftClassic.prop('checked')) {
						$catLeft.removeAttr('checked');
					}

					if (testAllowedToSplitMenu() === true) {
						activateElement();
					} else {
						deactivateElement();
					}
				});

				startStatus();

			})();

			//-->
		</script>
	<?php
	}
	?>
	</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>