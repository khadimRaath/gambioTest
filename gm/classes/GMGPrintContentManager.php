<?php
/* --------------------------------------------------------------
   GMGPrintContentManager.php 2015-05-20 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

class GMGPrintContentManager_ORIGIN
{
	function __construct()
	{
		//
	}


	function get_content($p_product, $p_source, $p_format = false)
	{
		$t_content = array();

		switch($p_source)
		{
			case 'cart':

				if(isset($_SESSION['coo_gprint_cart']->v_elements[$p_product]))
				{
					foreach($_SESSION['coo_gprint_cart']->v_elements[$p_product] AS $p_elements_id => $p_value)
					{
						$c_elements_id = (int)$p_elements_id;
						$t_content_name = '';
						$c_languages_id = (int)$_SESSION['languages_id'];
						$c_value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_value)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

						$t_get_name = xtc_db_query("SELECT
														v.name
													FROM
														" . TABLE_GM_GPRINT_ELEMENTS . " e,
														" . TABLE_GM_GPRINT_ELEMENTS_GROUPS . " g,
														" . TABLE_GM_GPRINT_ELEMENTS_VALUES. " v
													WHERE
														(e.gm_gprint_elements_id = '" . $c_elements_id . "'
														AND e.gm_gprint_elements_groups_id = v.gm_gprint_elements_groups_id
														AND e.gm_gprint_elements_groups_id = g.gm_gprint_elements_groups_id
														AND g.group_type != 'dropdown'
														AND languages_id = '" . $c_languages_id . "')
														OR
														(e.gm_gprint_elements_id = '" . $c_elements_id . "'
														AND e.gm_gprint_elements_groups_id = g.gm_gprint_elements_groups_id
														AND g.group_type = 'dropdown'
														AND e.gm_gprint_elements_groups_id = v.gm_gprint_elements_groups_id
														AND languages_id = '" . $c_languages_id . "'
														AND elements_value = '" . $c_value . "')
													GROUP BY e.gm_gprint_elements_id");
						if(xtc_db_num_rows($t_get_name) == 1)
						{
							$t_name = xtc_db_fetch_array($t_get_name);
							$t_content_name = $t_name['name'];

							$coo_configuration = new GMGPrintConfiguration($_SESSION['languages_id']);

							$t_character_length = (int)$coo_configuration->get_configuration('CHARACTER_LENGTH');

							if(strlen_wrapper($p_value) > $t_character_length && $t_character_length > 0)
							{
								$t_value = substr_wrapper($p_value, 0, $t_character_length) . '...';
							}
							else
							{
								$t_value = $p_value;
							}

							if(isset($_SESSION['coo_gprint_cart']->v_files[$p_product][$p_elements_id]))
							{
								$t_uploads_id = (int)$_SESSION['coo_gprint_cart']->v_files[$p_product][$p_elements_id];

								$t_get_key = xtc_db_query("SELECT download_key
															FROM " . TABLE_GM_GPRINT_UPLOADS . "
															WHERE gm_gprint_uploads_id = '" . $t_uploads_id . "'");
								if(xtc_db_num_rows($t_get_key) == 1)
								{
									$t_key = xtc_db_fetch_array($t_get_key);
									$t_value = '<a href="' . xtc_href_link('request_port.php', 'module=GPrintDownload&key=' . $t_key['download_key'], 'SSL') . '"><u>' . $t_value . '</u></a>';
								}
							}

							$t_content[] = array('NAME' => $t_content_name, 'VALUE' => $t_value);
						}
					}
				}

				break;
			case 'wishlist':

				if(isset($_SESSION['coo_gprint_wishlist']->v_elements[$p_product]))
				{
					foreach($_SESSION['coo_gprint_wishlist']->v_elements[$p_product] AS $p_elements_id => $p_value)
					{
						$c_elements_id = (int)$p_elements_id;
						$t_content_name = '';
						$c_languages_id = (int)$_SESSION['languages_id'];
						$c_value = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($p_value)) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

						$t_get_name = xtc_db_query("SELECT
														v.name
													FROM
														" . TABLE_GM_GPRINT_ELEMENTS . " e,
														" . TABLE_GM_GPRINT_ELEMENTS_GROUPS . " g,
														" . TABLE_GM_GPRINT_ELEMENTS_VALUES. " v
													WHERE
														(e.gm_gprint_elements_id = '" . $c_elements_id . "'
														AND e.gm_gprint_elements_groups_id = v.gm_gprint_elements_groups_id
														AND e.gm_gprint_elements_groups_id = g.gm_gprint_elements_groups_id
														AND g.group_type != 'dropdown'
														AND languages_id = '" . $c_languages_id . "')
														OR
														(e.gm_gprint_elements_id = '" . $c_elements_id . "'
														AND e.gm_gprint_elements_groups_id = g.gm_gprint_elements_groups_id
														AND g.group_type = 'dropdown'
														AND e.gm_gprint_elements_groups_id = v.gm_gprint_elements_groups_id
														AND languages_id = '" . $c_languages_id . "'
														AND elements_value = '" . $c_value . "')
													GROUP BY e.gm_gprint_elements_id");
						if(xtc_db_num_rows($t_get_name) == 1)
						{
							$t_name = xtc_db_fetch_array($t_get_name);
							$t_content_name = $t_name['name'];

							$coo_configuration = new GMGPrintConfiguration($_SESSION['languages_id']);

							$t_character_length = (int)$coo_configuration->get_configuration('CHARACTER_LENGTH');

							if(strlen_wrapper($p_value) > $t_character_length && $t_character_length > 0)
							{
								$t_value = substr_wrapper($p_value, 0, $t_character_length) . '...';
							}
							else
							{
								$t_value = $p_value;
							}

							if(isset($_SESSION['coo_gprint_wishlist']->v_files[$p_product][$p_elements_id]))
							{
								$t_uploads_id = (int)$_SESSION['coo_gprint_wishlist']->v_files[$p_product][$p_elements_id];

								$t_get_key = xtc_db_query("SELECT download_key
															FROM " . TABLE_GM_GPRINT_UPLOADS . "
															WHERE gm_gprint_uploads_id = '" . $t_uploads_id . "'");
								if(xtc_db_num_rows($t_get_key) == 1)
								{
									$t_key = xtc_db_fetch_array($t_get_key);
									$t_value = '<a href="' . xtc_href_link('request_port.php', 'module=GPrintDownload&key=' . $t_key['download_key'], 'SSL') . '"><u>' . $t_value . '</u></a>';
								}
							}

							$t_content[] = array('NAME' => $t_content_name, 'VALUE' => $t_value);
						}
					}
				}

				break;
		}

		if($p_format)
		{
			$t_content_html = '';

			for($i = 0; $i < count($t_content); $i++)
			{
				$t_content_html	.= '<strong>' . $t_content[$i]['NAME'] . '</strong>: ' . $t_content[$i]['VALUE'] . '<br />';
			}

			$t_content = $t_content_html;
		}

		return $t_content;
	}


	function get_orders_products_content($p_order_products_id, $p_shorten = false)
	{
		$c_order_products_id = (int)$p_order_products_id;
		$t_content = array();

		$t_get_elements_data = xtc_db_query("SELECT
													e.name,
													e.elements_value,
													e.gm_gprint_uploads_id,
													u.download_key
												FROM
													" . TABLE_GM_GPRINT_ORDERS_SURFACES_GROUPS . " g,
													" . TABLE_GM_GPRINT_ORDERS_SURFACES . " s,
													" . TABLE_GM_GPRINT_ORDERS_ELEMENTS . " e
												LEFT JOIN " . TABLE_GM_GPRINT_UPLOADS . " AS u ON (e.gm_gprint_uploads_id = u.gm_gprint_uploads_id)
												WHERE
													g.orders_products_id = '" . $c_order_products_id . "'
													AND g.gm_gprint_orders_surfaces_groups_id
													AND g.gm_gprint_orders_surfaces_groups_id = s.gm_gprint_orders_surfaces_groups_id
													AND s.gm_gprint_orders_surfaces_id = e.gm_gprint_orders_surfaces_id
													AND e.group_type IN ('text_input', 'textarea', 'file', 'dropdown')");

		while($t_elements_data = xtc_db_fetch_array($t_get_elements_data))
		{
			$t_value = $t_elements_data['elements_value'];

			if($p_shorten)
			{
				$coo_configuration = new GMGPrintConfiguration($_SESSION['languages_id']);

				$t_character_length = (int)$coo_configuration->get_configuration('CHARACTER_LENGTH');

				if(strlen_wrapper($t_value) > $t_character_length && $t_character_length > 0)
				{
					$t_value = substr_wrapper($t_value, 0, $t_character_length) . '...';
				}
			}

			$t_content[] = array('NAME' => $t_elements_data['name'],
									'VALUE' => $t_value,
									'UPLOADS_ID' => $t_elements_data['gm_gprint_uploads_id'],
									'DOWNLOAD_KEY' => $t_elements_data['download_key']);
		}

		return $t_content;
	}


	function get_content_by_orders_id($p_orders_id, $p_shorten = false)
	{
		$t_content = array();
		$c_orders_id = (int)$p_orders_id;

		$t_get_orders_products_ids = xtc_db_query("SELECT orders_products_id
													FROM " . TABLE_ORDERS_PRODUCTS . "
													WHERE orders_id = '" . $c_orders_id . "'");

		while($_orders_data = xtc_db_fetch_array($t_get_orders_products_ids))
		{
			$t_content_array = $this->get_orders_products_content($_orders_data['orders_products_id'], $p_shorten);
			if(!empty($t_content_array))
			{
				$t_content[$_orders_data['orders_products_id']] = $t_content_array;
			}
		}

		return $t_content;
	}
}
MainFactory::load_origin_class('GMGPrintContentManager');