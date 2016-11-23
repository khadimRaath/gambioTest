<?php
/* --------------------------------------------------------------
  languages.php 2016-08-29
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
  (c) 2002-2003 osCommerce(languages.php,v 1.33 2003/05/07); www.oscommerce.com
  (c) 2003	 nextcommerce (languages.php,v 1.10 2003/08/18); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: languages.php 1180 2005-08-26 08:44:53Z novalis $)

  Released under the GNU General Public License
  -------------------------------------------------------------- */

require('includes/application_top.php');

AdminMenuControl::connect_with_page('zones.php');

if(gm_get_conf('GM_CHECK_BROWSER_LANGUAGE') === '1')
{
	$t_gm_browser_language_checked = ' checked="checked"';
}
else
{
	$t_gm_browser_language_checked = '';
}

if(isset($_POST['gm_save']))
{
	if(isset($_POST['gm_check_browser_language']))
	{
		gm_set_conf('GM_CHECK_BROWSER_LANGUAGE', 1);
		$t_gm_browser_language_checked = ' checked="checked"';
	}
	else
	{
		gm_set_conf('GM_CHECK_BROWSER_LANGUAGE', 0);
		$t_gm_browser_language_checked = '';
	}
}

switch($_GET['action'])
{
	case 'setlflag':
		if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
		{
			if(($_GET['flag'] == '0') || ($_GET['flag'] == '1'))
			{
				if($_GET['lID'])
				{
					$c_languages_id = (int)$_GET['lID'];
					$c_new_status = (int)$_GET['flag'];
					$t_sql = "UPDATE `" . TABLE_LANGUAGES . "` SET `status` = " . $c_new_status . " WHERE `languages_id` = " . $c_languages_id;
					xtc_db_query($t_sql);
				}
			}
			xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'lID=' . $_GET['lID']));
		}
		break;

	case 'insert':
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			$name = xtc_db_prepare_input($_POST['name']);
			$code = xtc_db_prepare_input($_POST['code']);
			$image = xtc_db_prepare_input($_POST['image']);
			$directory = xtc_db_prepare_input($_POST['directory']);
			$sort_order = xtc_db_prepare_input($_POST['sort_order']);
			$charset = xtc_db_prepare_input($_POST['charset']);

			$c_source_language_id = (int)$_POST['ref_language_id'];

			xtc_db_query("insert into " . TABLE_LANGUAGES . " (name, code, image, directory, sort_order,language_charset) values ('" . xtc_db_input($name) . "', '" . xtc_db_input($code) . "', '" . xtc_db_input($image) . "', '" . xtc_db_input($directory) . "', '" . xtc_db_input($sort_order) . "', '" . xtc_db_input($charset) . "')");
			$insert_id = xtc_db_insert_id();

			// create additional categories_description records
			$categories_query = xtc_db_query("select categories_id, categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . $c_source_language_id . "'");
			while($categories = xtc_db_fetch_array($categories_query))
			{
				xtc_db_query("insert into " . TABLE_CATEGORIES_DESCRIPTION . " (categories_id, language_id, categories_name) values ('" . $categories['categories_id'] . "', '" . $insert_id . "', '" . xtc_db_input($categories['categories_name']) . "')");
			}

			// create additional products_description records
			$products_query = xtc_db_query("select products_id, products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . $c_source_language_id . "'");
			while($products = xtc_db_fetch_array($products_query))
			{
				xtc_db_query("insert into " . TABLE_PRODUCTS_DESCRIPTION . " (products_id, language_id, products_name) values ('" . $products['products_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products['products_name']) . "')");
			}

			// create additional products_options records
			$products_options_query = xtc_db_query("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $c_source_language_id . "'");
			while($products_options = xtc_db_fetch_array($products_options_query))
			{
				xtc_db_query("insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, language_id, products_options_name) values ('" . $products_options['products_options_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products_options['products_options_name']) . "')");
			}

			// create additional products_options_values records
			$products_options_values_query = xtc_db_query("select products_options_values_id, products_options_values_name, gm_filename from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id = '" . $c_source_language_id . "'");
			while($products_options_values = xtc_db_fetch_array($products_options_values_query))
			{
				xtc_db_query("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES . " (products_options_values_id, language_id, products_options_values_name, gm_filename) values ('" . $products_options_values['products_options_values_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products_options_values['products_options_values_name']) . "', '" . xtc_db_input($products_options_values['gm_filename']) . "')");
			}

			// create additional manufacturers_info records
			$manufacturers_query = xtc_db_query("select m.manufacturers_id, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on m.manufacturers_id = mi.manufacturers_id where mi.languages_id = '" . $c_source_language_id . "'");
			while($manufacturers = xtc_db_fetch_array($manufacturers_query))
			{
				xtc_db_query("insert into " . TABLE_MANUFACTURERS_INFO . " (manufacturers_id, languages_id, manufacturers_url) values ('" . $manufacturers['manufacturers_id'] . "', '" . $insert_id . "', '" . xtc_db_input($manufacturers['manufacturers_url']) . "')");
			}

			// create additional orders_status records
			$orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $c_source_language_id . "'");
			while($orders_status = xtc_db_fetch_array($orders_status_query))
			{
				xtc_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $orders_status['orders_status_id'] . "', '" . $insert_id . "', '" . xtc_db_input($orders_status['orders_status_name']) . "')");
			}

			// create additional content_manager records
			$copy_cm_query = "INSERT INTO " . TABLE_CONTENT_MANAGER . " (categories_id,
								parent_id,
								group_ids,
								languages_id,
								content_title,
								content_heading,
								content_text,
								sort_order,
								file_flag,
								content_file,
								content_status,
								content_group,
								content_delete,
								gm_link,
								gm_link_target,
								gm_priority,
								gm_changefreq,
								gm_last_modified,
								gm_sitemap_entry,
								gm_url_keywords,
								contents_meta_title,
								contents_meta_description,
								contents_meta_keywords)
								SELECT categories_id,
									parent_id,
									group_ids,
									" . $insert_id . ",
									content_title,
									content_heading,
									content_text,
									sort_order,
									file_flag,
									content_file,
									content_status,
									content_group,
									content_delete,
									gm_link,
									gm_link_target,
									gm_priority,
									gm_changefreq,
									now(),
									gm_sitemap_entry,
									gm_url_keywords,
									contents_meta_title,
									contents_meta_description,
									contents_meta_keywords
								FROM " . TABLE_CONTENT_MANAGER . "
								WHERE languages_id = " . $c_source_language_id;
			xtc_db_query($copy_cm_query);


			// create additional gm_contents records
			$gm_contents_query = xtc_db_query("SELECT
												  gm_key,
												  gm_value,
												  gm_group_id,
												  gm_sort_order
											  FROM gm_contents
											  WHERE languages_id='" . $c_source_language_id . "'");
			while($gm_contents = xtc_db_fetch_array($gm_contents_query))
			{
				xtc_db_query("INSERT INTO gm_contents
								SET
									languages_id = '" . $insert_id . "',
									gm_key = '" . $gm_contents['gm_key'] . "',
									gm_value = '" . $gm_contents['gm_value'] . "',
									gm_group_id = '" . $gm_contents['gm_group_id'] . "',
									gm_sort_order = '" . $gm_contents['gm_sort_order'] . "'");
			}

			// create additional orders_status records
			$xsell_grp_query = xtc_db_query("select products_xsell_grp_name_id,xsell_sort_order, groupname from " . TABLE_PRODUCTS_XSELL_GROUPS . " where language_id = '" . $c_source_language_id . "'");
			while($xsell_grp = xtc_db_fetch_array($xsell_grp_query))
			{
				xtc_db_query("insert into " . TABLE_PRODUCTS_XSELL_GROUPS . " (products_xsell_grp_name_id,xsell_sort_order, language_id, groupname) values ('" . $xsell_grp['products_xsell_grp_name_id'] . "','" . $xsell_grp['xsell_sort_order'] . "', '" . $insert_id . "', '" . xtc_db_input($xsell_grp['groupname']) . "')");
			}

			// create additional customers status
			$customers_status_query = xtc_db_query("SELECT DISTINCT customers_status_id
								FROM " . TABLE_CUSTOMERS_STATUS);
			while($data = xtc_db_fetch_array($customers_status_query))
			{

				$customers_status_data_query = xtc_db_query("SELECT *
								FROM " . TABLE_CUSTOMERS_STATUS . "
								WHERE customers_status_id='" . $data['customers_status_id'] . "'");

				$group_data = xtc_db_fetch_array($customers_status_data_query);
				$c_data = array(
					'customers_status_id' => $data['customers_status_id'],
					'language_id' => $insert_id,
					'customers_status_name' => $group_data['customers_status_name'],
					'customers_status_public' => $group_data['customers_status_public'],
					'customers_status_min_order' => $group_data['customers_status_min_order'],
					'customers_status_max_order' => $group_data['customers_status_max_order'],
					'customers_status_image' => $group_data['customers_status_image'],
					'customers_status_discount' => $group_data['customers_status_discount'],
					'customers_status_ot_discount_flag' => $group_data['customers_status_ot_discount_flag'],
					'customers_status_ot_discount' => $group_data['customers_status_ot_discount'],
					'customers_status_graduated_prices' => $group_data['customers_status_graduated_prices'],
					'customers_status_show_price' => $group_data['customers_status_show_price'],
					'customers_status_show_price_tax' => $group_data['customers_status_show_price_tax'],
					'customers_status_add_tax_ot' => $group_data['customers_status_add_tax_ot'],
					'customers_status_payment_unallowed' => $group_data['customers_status_payment_unallowed'],
					'customers_status_shipping_unallowed' => $group_data['customers_status_shipping_unallowed'],
					'customers_status_discount_attributes' => $group_data['customers_status_discount_attributes'],
					'customers_fsk18' => $group_data['customers_fsk18'],
					'customers_fsk18_display' => $group_data['customers_fsk18_display'],
					'customers_status_write_reviews' => $group_data['customers_status_write_reviews'],
					'customers_status_read_reviews' => $group_data['customers_status_read_reviews']);

				xtc_db_perform(TABLE_CUSTOMERS_STATUS, $c_data);
			}

			// create additional shipping_status records
			$shipping_status_query = xtc_db_query("select shipping_status_id, shipping_status_name from " . TABLE_SHIPPING_STATUS . " where language_id = '" . $c_source_language_id . "'");
			while($shipping_status = xtc_db_fetch_array($shipping_status_query))
			{
				xtc_db_query("replace into " . TABLE_SHIPPING_STATUS . " (shipping_status_id, language_id, shipping_status_name) values ('" . $shipping_status['shipping_status_id'] . "', '" . $insert_id . "', '" . xtc_db_input($shipping_status['shipping_status_name']) . "')");
			}

			// coupons description
			$t_sql = 'SELECT
							coupon_id,
							coupon_name
						FROM ' . TABLE_COUPONS_DESCRIPTION . '
						WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_sql = 'INSERT INTO ' . TABLE_COUPONS_DESCRIPTION . '
							SET
								coupon_id = "' . $t_result_array['coupon_id'] . '",
								coupon_name = "' . xtc_db_input($t_result_array['coupon_name']) . '",
								language_id = "' . $insert_id . '"';
				xtc_db_query($t_sql);
			}

			// feature description
			$t_sql = 'SELECT
							feature_id,
							feature_name,
							feature_admin_name
						FROM feature_description
						WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_sql = 'INSERT INTO feature_description
							SET
								feature_id = "' . $t_result_array['feature_id'] . '",
								feature_name = "' . xtc_db_input($t_result_array['feature_name']) . '",
								feature_admin_name = "' . xtc_db_input($t_result_array['feature_admin_name']) . '",
								language_id = "' . $insert_id . '"';
				xtc_db_query($t_sql);
			}

			// feature value description
			$t_sql = 'SELECT
							feature_value_id,
							feature_value_text
						FROM feature_value_description
						WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_sql = 'INSERT INTO feature_value_description
							SET
								feature_value_id = "' . $t_result_array['feature_value_id'] . '",
								feature_value_text = "' . xtc_db_input($t_result_array['feature_value_text']) . '",
								language_id = "' . $insert_id . '"';
				xtc_db_query($t_sql);
			}

			// GX-Customizer elements values
			$t_sql = 'SELECT
							gm_gprint_elements_groups_id,
							name,
							elements_value
						FROM gm_gprint_elements_values
						WHERE languages_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_sql = 'INSERT INTO gm_gprint_elements_values
							SET
								gm_gprint_elements_groups_id = "' . $t_result_array['gm_gprint_elements_groups_id'] . '",
								name = "' . xtc_db_input($t_result_array['name']) . '",
								elements_value = "' . xtc_db_input($t_result_array['elements_value']) . '",
								languages_id = "' . $insert_id . '"';
				xtc_db_query($t_sql);
			}

			// GX-Customizer surfaces description
			$t_sql = 'SELECT
							gm_gprint_surfaces_id,
							name
						FROM gm_gprint_surfaces_description
						WHERE languages_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_sql = 'INSERT INTO gm_gprint_surfaces_description
							SET
								gm_gprint_surfaces_id = "' . $t_result_array['gm_gprint_surfaces_id'] . '",
								name = "' . xtc_db_input($t_result_array['name']) . '",
								languages_id = "' . $insert_id . '"';
				xtc_db_query($t_sql);
			}

			// products vpe
			$t_sql = 'SELECT
							products_vpe_id,
							products_vpe_name
						FROM ' . TABLE_PRODUCTS_VPE . '
						WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_sql = 'INSERT INTO ' . TABLE_PRODUCTS_VPE . '
							SET
								products_vpe_id = "' . $t_result_array['products_vpe_id'] . '",
								products_vpe_name = "' . xtc_db_input($t_result_array['products_vpe_name']) . '",
								language_id = "' . $insert_id . '"';
				xtc_db_query($t_sql);
			}

			// properties description
			$t_sql = 'SELECT
							properties_id,
							properties_name,
							properties_admin_name
						FROM properties_description
						WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_sql = 'INSERT INTO properties_description
							SET
								properties_id = "' . $t_result_array['properties_id'] . '",
								properties_name = "' . xtc_db_input($t_result_array['properties_name']) . '",
								properties_admin_name = "' . xtc_db_input($t_result_array['properties_admin_name']) . '",
								language_id = "' . $insert_id . '"';
				xtc_db_query($t_sql);
			}

			// properties values description
			$t_sql = 'SELECT
							properties_values_id,
							values_name
						FROM properties_values_description
						WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_sql = 'INSERT INTO properties_values_description
							SET
								properties_values_id = "' . $t_result_array['properties_values_id'] . '",
								values_name = "' . xtc_db_input($t_result_array['values_name']) . '",
								language_id = "' . $insert_id . '"';
				xtc_db_query($t_sql);
			}

			// export types
			$t_sql = 'SELECT * FROM export_types WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('export_types', $t_result_array);
			}

			// shop notices
			$t_sql = 'SELECT * FROM shop_notice_contents WHERE language_id = ' . $c_source_language_id;
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;
				$t_result_array['content'] = '';

				xtc_db_perform('shop_notice_contents', $t_result_array);
			}

			if($_POST['default'] == 'on')
			{
				xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($code) . "' where configuration_key = 'DEFAULT_LANGUAGE'");
			}

			// EXTENDER START
			$coo_admin_language_extender_component = MainFactory::create_object('AdminLanguageExtenderComponent');
			$coo_admin_language_extender_component->set_data('GET', $_GET);
			$coo_admin_language_extender_component->set_data('POST', $_POST);
			if(isset($insert_id) && empty($insert_id) == false)
			{
				$coo_admin_language_extender_component->set_data('insert_id', $insert_id);
			}
			$coo_admin_language_extender_component->proceed();
			// EXTENDER END

			$coo_cache_control = MainFactory::create_object('CacheControl');
			$coo_cache_control->rebuild_products_properties_index();
			$coo_cache_control->clear_data_cache();

			xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $insert_id));
		}
		break;

	case 'copy':
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			$name = xtc_db_prepare_input($_POST['name']);
			$code = xtc_db_prepare_input($_POST['code']);
			$image = xtc_db_prepare_input($_POST['image']);
			$directory = xtc_db_prepare_input($_POST['directory']);
			$sort_order = xtc_db_prepare_input($_POST['sort_order']);
			$charset = xtc_db_prepare_input($_POST['charset']);
			$ref_language_id = (int)$_POST['ref_language_id'];
			$c_source_language_id = $ref_language_id;


			xtc_db_query("insert into " . TABLE_LANGUAGES . " (name, code, image, directory, sort_order,language_charset) values ('" . xtc_db_input($name) . "', '" . xtc_db_input($code) . "', '" . xtc_db_input($image) . "', '" . xtc_db_input($directory) . "', '" . xtc_db_input($sort_order) . "', '" . xtc_db_input($charset) . "')");
			$insert_id = xtc_db_insert_id();

			$t_result = xtc_db_query("SELECT * FROM " . TABLE_LANGUAGES . " WHERE languages_id = " . $ref_language_id);
			$t_language = xtc_db_fetch_array($t_result);

			xtc_db_query("INSERT INTO `language_phrases_edited` (`language_id`, `section_name`, `phrase_name`, `phrase_text`) SELECT " . $insert_id . ", `section_name`, `phrase_name`, `phrase_text` FROM `language_phrases_edited` WHERE `language_id` = " . $ref_language_id);
			$reader = MainFactory::create_object('PhraseCacheBuilder');
			$reader->build();

			// create additional categories_description records
			$t_sql = 'SELECT * FROM ' . TABLE_CATEGORIES_DESCRIPTION . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $t_result_array);
			}

			// create additional products_description records
			$t_sql = 'SELECT * FROM ' . TABLE_PRODUCTS_DESCRIPTION . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $t_result_array);
			}

			// create additional products_options records
			$t_sql = 'SELECT * FROM ' . TABLE_PRODUCTS_OPTIONS . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_PRODUCTS_OPTIONS, $t_result_array);
			}

			// create additional products_options_values records
			$t_sql = 'SELECT * FROM ' . TABLE_PRODUCTS_OPTIONS_VALUES . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_PRODUCTS_OPTIONS_VALUES, $t_result_array);
			}

			// create additional manufacturers_info records
			$t_sql = 'SELECT * FROM ' . TABLE_MANUFACTURERS_INFO . ' WHERE languages_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['languages_id'] = $insert_id;

				xtc_db_perform(TABLE_MANUFACTURERS_INFO, $t_result_array);
			}

			// create additional orders_status records
			$t_sql = 'SELECT * FROM ' . TABLE_ORDERS_STATUS . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_ORDERS_STATUS, $t_result_array);
			}

			// create additional content_manager records
			$t_sql = 'SELECT * FROM ' . TABLE_CONTENT_MANAGER . ' WHERE languages_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				unset($t_result_array['content_id']);
				$t_result_array['languages_id'] = $insert_id;
				$t_result_array['gm_last_modified'] = 'now()';

				xtc_db_perform(TABLE_CONTENT_MANAGER, $t_result_array);
			}

			// create additional gm_contents records
			$t_sql = 'SELECT * FROM gm_contents WHERE languages_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				unset($t_result_array['gm_contents_id']);
				$t_result_array['languages_id'] = $insert_id;

				xtc_db_perform('gm_contents', $t_result_array);
			}

			// create additional gm_emails records
			$query = 'INSERT INTO `email_templates_edited` (`name`, `language_id`, `type`, `content`, `backup`)
						SELECT `name`, ' . $insert_id . ', `type`, `content`, `backup` FROM `email_templates_edited`
						WHERE `language_id` = ' . $c_source_language_id;
			xtc_db_query($query);


			// create additional xsell groups records
			$t_sql = 'SELECT * FROM ' . TABLE_PRODUCTS_XSELL_GROUPS . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_PRODUCTS_XSELL_GROUPS, $t_result_array);
			}

			// create additional customers status
			$t_sql = 'SELECT * FROM ' . TABLE_CUSTOMERS_STATUS . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_CUSTOMERS_STATUS, $t_result_array);
			}

			// create additional shipping_status records
			$t_sql = 'SELECT * FROM ' . TABLE_SHIPPING_STATUS . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_SHIPPING_STATUS, $t_result_array);
			}

			// coupons description
			$t_sql = 'SELECT * FROM ' . TABLE_COUPONS_DESCRIPTION . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_COUPONS_DESCRIPTION, $t_result_array);
			}

			// feature description
			$t_sql = 'SELECT * FROM feature_description WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('feature_description', $t_result_array);
			}

			// feature value description
			$t_sql = 'SELECT * FROM feature_value_description WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('feature_value_description', $t_result_array);
			}

			// GX-Customizer elements values
			$t_sql = 'SELECT * FROM gm_gprint_elements_values WHERE languages_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				unset($t_result_array['gm_gprint_elements_values_id']);
				$t_result_array['languages_id'] = $insert_id;

				xtc_db_perform('gm_gprint_elements_values', $t_result_array);
			}

			// GX-Customizer surfaces description
			$t_sql = 'SELECT * FROM gm_gprint_surfaces_description WHERE languages_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['languages_id'] = $insert_id;

				xtc_db_perform('gm_gprint_surfaces_description', $t_result_array);
			}

			// products vpe
			$t_sql = 'SELECT * FROM ' . TABLE_PRODUCTS_VPE . ' WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform(TABLE_PRODUCTS_VPE, $t_result_array);
			}

			// properties description
			$t_sql = 'SELECT * FROM properties_description WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				unset($t_result_array['properties_description_id']);
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('properties_description', $t_result_array);
			}

			// properties values description
			$t_sql = 'SELECT * FROM properties_values_description WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				unset($t_result_array['properties_values_description_id']);
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('properties_values_description', $t_result_array);
			}

			// quantity unit description
			$t_sql = 'SELECT * FROM quantity_unit_description WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('quantity_unit_description', $t_result_array);
			}

			// additional field descriptions
			$t_sql = 'SELECT * FROM additional_field_descriptions WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('additional_field_descriptions', $t_result_array);
			}

			// additional field value descriptions
			$t_sql = 'SELECT * FROM additional_field_value_descriptions WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('additional_field_value_descriptions', $t_result_array);
			}

			// export types
			$t_sql = 'SELECT * FROM export_types WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('export_types', $t_result_array);
			}

			// product's image alt-texts
			$t_sql = 'SELECT * FROM gm_prd_img_alt WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				unset($t_result_array['img_alt_id']);
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('gm_prd_img_alt', $t_result_array);
			}

			// product's content
			$t_sql = 'SELECT * FROM ' . TABLE_PRODUCTS_CONTENT . ' WHERE languages_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				unset($t_result_array['content_id']);
				$t_result_array['languages_id'] = $insert_id;

				xtc_db_perform(TABLE_PRODUCTS_CONTENT, $t_result_array);
			}

			// slider image description
			$t_sql = 'SELECT * FROM slider_image_description WHERE language_id = "' . $c_source_language_id . '"';
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('slider_image_description', $t_result_array);
			}

			// shop notices
			$t_sql = 'SELECT * FROM shop_notice_contents WHERE language_id = ' . $c_source_language_id;
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$t_result_array['language_id'] = $insert_id;

				xtc_db_perform('shop_notice_contents', $t_result_array);
			}

			// EXTENDER START
			$coo_admin_language_extender_component = MainFactory::create_object('AdminLanguageExtenderComponent');
			$coo_admin_language_extender_component->set_data('GET', $_GET);
			$coo_admin_language_extender_component->set_data('POST', $_POST);
			if(isset($insert_id) && empty($insert_id) == false)
			{
				$coo_admin_language_extender_component->set_data('insert_id', $insert_id);
			}
			$coo_admin_language_extender_component->proceed();
			// EXTENDER END

			$coo_cache_control = MainFactory::create_object('CacheControl');
			$coo_cache_control->rebuild_products_properties_index();
			$coo_cache_control->clear_data_cache();

			xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $insert_id));
		}
		break;

	case 'save':
		if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
		{
			$lID = xtc_db_prepare_input($_GET['lID']);
			$name = xtc_db_prepare_input($_POST['name']);
			$code = xtc_db_prepare_input($_POST['code']);
			$image = xtc_db_prepare_input($_POST['image']);
			$directory = xtc_db_prepare_input($_POST['directory']);
			$sort_order = xtc_db_prepare_input($_POST['sort_order']);
			$charset = xtc_db_prepare_input($_POST['charset']);

			xtc_db_query("update " . TABLE_LANGUAGES . " set name = '" . xtc_db_input($name) . "', code = '" . xtc_db_input($code) . "', image = '" . xtc_db_input($image) . "', directory = '" . xtc_db_input($directory) . "', sort_order = '" . xtc_db_input($sort_order) . "', language_charset = '" . xtc_db_input($charset) . "' where languages_id = '" . xtc_db_input($lID) . "'");

			if($_POST['default'] == 'on')
			{
				xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($code) . "' where configuration_key = 'DEFAULT_LANGUAGE'");
			}

			// EXTENDER START
			$coo_admin_language_extender_component = MainFactory::create_object('AdminLanguageExtenderComponent');
			$coo_admin_language_extender_component->set_data('GET', $_GET);
			$coo_admin_language_extender_component->set_data('POST', $_POST);
			$coo_admin_language_extender_component->proceed();
			// EXTENDER END

			xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $_GET['lID']));
		}
		break;

	case 'deleteconfirm':
		if($_SESSION['coo_page_token']->is_valid($_GET['page_token']))
		{
			$lID = xtc_db_prepare_input($_GET['lID']);

			$lng_query = xtc_db_query("select languages_id from " . TABLE_LANGUAGES . " where code = '" . DEFAULT_CURRENCY . "'");
			$lng = xtc_db_fetch_array($lng_query);
			if($lng['languages_id'] == $lID)
			{
				xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_CURRENCY'");
			}

			xtc_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where language_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where languages_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_ORDERS_STATUS . " where language_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_LANGUAGES . " where languages_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_CONTENT_MANAGER . " where languages_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_PRODUCTS_CONTENT . " where languages_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query("delete from gm_contents where languages_id = '" . xtc_db_input($lID) . "'");
			xtc_db_query('DELETE FROM additional_field_descriptions WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM additional_field_value_descriptions WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM ' . TABLE_COUPONS_DESCRIPTION . ' WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM export_types WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM feature_description WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM feature_value_description WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM gm_gprint_elements_values WHERE languages_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM gm_gprint_surfaces_description WHERE languages_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM infobox_messages_description WHERE languages_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM products_properties_index WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM ' . TABLE_PRODUCTS_VPE . ' WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM ' . TABLE_PRODUCTS_XSELL_GROUPS . ' WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM properties_description WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM properties_values_description WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM quantity_unit_description WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM ' . TABLE_REVIEWS_DESCRIPTION . ' WHERE languages_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM ' . TABLE_SHIPPING_STATUS . ' WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM slider_image_description WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM language_phrases_cache WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM language_phrases_edited WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM email_templates_cache WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM email_templates_edited WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM shop_notice_contents WHERE language_id = "' . (int)$lID . '"');
			xtc_db_query('DELETE FROM shop_notice_job_contents WHERE language_id = "' . (int)$lID . '"');

			// EXTENDER START
			$coo_admin_language_extender_component = MainFactory::create_object('AdminLanguageExtenderComponent');
			$coo_admin_language_extender_component->set_data('GET', $_GET);
			$coo_admin_language_extender_component->set_data('POST', $_POST);
			$coo_admin_language_extender_component->proceed();
			// EXTENDER END

			$coo_cache_control = MainFactory::create_object('CacheControl');
			$coo_cache_control->rebuild_products_properties_index();
			$coo_cache_control->clear_data_cache();

			xtc_redirect(xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page']));
		}
		break;

	case 'delete':
		$lID = xtc_db_prepare_input($_GET['lID']);

		$lng_query = xtc_db_query("select code from " . TABLE_LANGUAGES . " where languages_id = '" . xtc_db_input($lID) . "'");
		$lng = xtc_db_fetch_array($lng_query);

		$remove_language = true;
		if($lng['code'] == DEFAULT_LANGUAGE)
		{
			$remove_language = false;
			$GLOBALS['messageStack']->add(ERROR_REMOVE_DEFAULT_LANGUAGE, 'error');
		}
		break;
}

$messageStack->add(HEADING_WARNING, 'warning');

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
		<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
						<!-- left_navigation //-->
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						<!-- left_navigation_eof //-->
					</table>
				</td>
				<!-- body_text //-->
				<td class="boxCenter" width="100%" valign="top">

					<div class="gx-container create-new-wrapper left-table">
						<div class="create-new-container pull-right">
							<a href="<?php echo xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=new') ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
						</div>
					</div>

					<table border="0" width="100%" cellspacing="0" cellpadding="2">
						<tr>
							<td width="100%">
								<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/land.png)">
									<?php echo $adminMenuLang->get_text('BOX_HEADING_ZONE', 'admin_menu'); ?>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td class="dataTableHeadingContent">
											<a href="zones.php">
												<?php echo $adminMenuLang->get_text('BOX_ZONES'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<a href="countries.php">
												<?php echo $adminMenuLang->get_text('BOX_COUNTRIES'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<?php echo $adminMenuLang->get_text('BOX_LANGUAGES'); ?>
										</td>
										<td class="dataTableHeadingContent">
											<a href="tax_classes.php">
												<?php echo $adminMenuLang->get_text('BOX_TAX_CLASSES'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<a href="tax_rates.php">
												<?php echo $adminMenuLang->get_text('BOX_TAX_RATES'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<a href="configuration.php?gID=18">
												<?php echo $adminMenuLang->get_text('BOX_CONFIGURATION_18'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<a href="geo_zones.php">
												<?php echo $adminMenuLang->get_text('BOX_GEO_ZONES'); ?>
											</a>
										</td>
										<td class="dataTableHeadingContent">
											<a href="currencies.php">
												<?php echo $adminMenuLang->get_text('BOX_CURRENCIES'); ?>
											</a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<div class="gx-container left-table">
									<form action="<?php xtc_href_link('languages.php'); ?>" method="post">
										<table class="gx-configuration" data-gx-widget="checkbox">
											<tr>
												<th class="dataTableContent_gm"><?php echo GM_LANGUAGE_CONFIGURATION_TITLE; ?></th>
												<th class="dataTableContent_gm"></th>
											</tr>
											<tr>
												<td class="dataTableContent_gm configuration-label">
													<?php
													echo GM_LANGUAGE_CONFIGURATION_TEXT;
													?>
												</td>
												<td>
													<input type="checkbox" name="gm_check_browser_language" value="1" <?php echo $t_gm_browser_language_checked; ?> />
												</td>
											</tr>
										</table>
										<div class="add-margin-top-24">
											<input style="margin-left:1px" type="submit" name="gm_save" class="btn btn-primary pull-right" onClick="this.blur();" value="<?php echo BUTTON_SAVE; ?>" />
										</div>
									</form>
								</div>

								<?php
								$fileList = array();
								$fileList[] = 'lang/%DIR%/admin/init.inc.php';
								$fileList[] = 'lang/%DIR%/admin/images/icon.gif';
								$fileList[] = 'lang/%DIR%/flag.png';
								$fileList[] = 'lang/%DIR%/init.inc.php';
								$fileList[] = 'lang/%DIR%/%IMAGE%';
								$fileList[] = 'lang/%DIR%/original_mail_templates';
								$fileList[] = 'lang/%DIR%/original_sections';
								$fileList[] = 'lang/%DIR%/user_mail_templates';
								$fileList[] = 'lang/%DIR%/user_sections';

								$errorsArray = array();
								$languages_query_raw = "select languages_id, name, code, image, directory, sort_order,language_charset, status from " . TABLE_LANGUAGES . " order by sort_order";
								$languagesResult = xtc_db_query($languages_query_raw);
								while($row = xtc_db_fetch_array($languagesResult))
								{
									foreach($fileList as $file)
									{
										$file = str_replace('%DIR%', $row['directory'], $file);
										$file = str_replace('%IMAGE%', $row['image'], $file);
										if(file_exists(DIR_FS_CATALOG . $file) == false)
										{
											$errorsArray[$row['name']][] = $file;
										}
									}
								}
								if(count($errorsArray) > 0)
								{
								?>
									<div class="gx-container">
										<table class="gx-configuration" data-gx-widget="checkbox">
											<tr>
												<th class="dataTableContent_gm"><?php echo HEADLINE_INCOMPLETE_LANGUAGES_HINT; ?></th>
												<th class="dataTableContent_gm"></th>
											</tr>
											<tr>
												<td class="dataTableContent_gm configuration-label">
													<?php echo TEXT_INCOMPLETE_LANGUAGES_HINT; ?>
												</td>
												<td class="dataTableContent_gm">
													<?php
													foreach($errorsArray as $languageName => $errors)
													{
														echo '<b>' . $languageName . ':</b>';
														foreach($errors as $error)
														{
															echo '<br />' . $error;
														}
														echo '<br /><br />';
													}
													?>
												</td>
											</tr>
										</table>
										<br />
										<br />
									</div>
								<?php
								}
								?>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td valign="top">
											<table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
												<tr class="dataTableHeadingRow">
													<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LANGUAGE_NAME; ?></td>
													<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LANGUAGE_CODE; ?></td>
													<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_STATUS; ?></td>
													<td class="dataTableHeadingContent"></td>
												</tr>
												<?php
												$languages_split = new splitPageResults($_GET['page'], '20', $languages_query_raw, $languages_query_numrows);
												$languages_query = xtc_db_query($languages_query_raw);

												while($languages = xtc_db_fetch_array($languages_query))
												{
													if(((!$_GET['lID']) || (@$_GET['lID'] == $languages['languages_id'])) && (!$lInfo) && (substr($_GET['action'], 0, 3) != 'new'))
													{
														$lInfo = new objectInfo($languages);
													}

													if((is_object($lInfo)) && ($languages['languages_id'] == $lInfo->languages_id))
													{
														echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '">' . "\n";
													}
													else
													{
														echo '                  <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $languages['languages_id']) . '">' . "\n";
													}

													if(DEFAULT_LANGUAGE == $languages['code'])
													{
														echo '                <td class="dataTableContent">' . $languages['name'] . ' (' . TEXT_DEFAULT . ')</td>' . "\n";
													}
													else
													{
														echo '                <td class="dataTableContent">' . $languages['name'] . '</td>' . "\n";
													}
													?>
													<td class="dataTableContent"><?php echo $languages['code']; ?></td>
													<td class="dataTableContent">
													<?php
													if( array_key_exists($languages['name'], $errorsArray) == false
														&& DEFAULT_LANGUAGE != $languages['code'])
													{
														$checked = $languages['status'] == '1' ? 'true' : 'false';

													?>
														<div class="control-group" data-gx-widget="checkbox"
														     data-checkbox-checked="<?php echo $checked ?>"
														     data-checkbox-on_url="<?php echo xtc_href_link(FILENAME_LANGUAGES, xtc_get_all_get_params(array('page', 'lID', 'action')) . 'action=setlflag&flag=1&page_token=' . $_SESSION['coo_page_token']->generate_token() . '&lID=' . $languages['languages_id']); ?>"
														     data-checkbox-off_url="<?php echo xtc_href_link(FILENAME_LANGUAGES, xtc_get_all_get_params(array('page', 'lID', 'action')) . 'action=setlflag&flag=0&page_token=' . $_SESSION['coo_page_token']->generate_token() . '&lID=' . $languages['languages_id']) ?>">
														</div>
													<?php
													}
													else if(DEFAULT_LANGUAGE == $languages['code'] && $languages['status'] == '1' && array_key_exists($languages['name'], $errorsArray) == false)
													{
													?>
														<div class="control-group" data-gx-widget="checkbox">
															<input type="checkbox" checked disabled />
														</div>
													<?php
													}
													else
													{
													?>
														<div class="control-group" data-gx-widget="checkbox">
															<input type="checkbox" disabled />
														</div>
													<?php
													}
													?>
														&nbsp;
													</td>
													<td class="dataTableContent"></td>
												</tr>
												<?php
												}
												?>
											</table>

											<table class="gx-container paginator left-table table-paginator">
												<tr>
													<td class="pagination-control">
														<?php echo $languages_split->display_count($languages_query_numrows,
														                                           '20', $_GET['page'],
														                                           TEXT_DISPLAY_NUMBER_OF_LANGUAGES); ?>
														<span class="page-number-information">
															<?php echo $languages_split->display_links($languages_query_numrows,
															                                           '20', MAX_DISPLAY_PAGE_LINKS,
															                                           $_GET['page']); ?>
			                                            </span>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
				<!-- body_text_eof //-->
			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
		<div class="hidden">
			<?php
			$direction_options = array(array('id' => '', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_DEFAULT),
			                           array('id' => 'ltr', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_LEFT_TO_RIGHT),
			                           array('id' => 'rtl', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_RIGHT_TO_LEFT));

			$heading = array();
			$contents = array();
			$buttons = '';
			$formIsEditable = false;
			$formAction = '';
			$formMethod = 'post';
			$formAttributes = array();
			switch($_GET['action'])
			{
				case 'new':
					$formAction = xtc_href_link(FILENAME_LANGUAGES, 'action=copy');
					$formIsEditable = true;

					$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_CREATE . '"/>';
					$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $_GET['lID']) . '">' . BUTTON_CANCEL . '</a>';

					$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_LANGUAGE . '</b>');

					$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
					$contents[] = array('text' => '<span class="options-title">' . TEXT_MODE_LANGUAGE . '</span>');
					$t_mode_selection_html = xtc_draw_radio_field('mode', 'insert') . TEXT_MODE_NEW_LANGUAGE . '<br />' . xtc_draw_radio_field('mode', 'copy', true) . TEXT_MODE_COPY_LANGUAGE;
					$t_mode_selection_html .= '<script type="text/javascript">
																			$(document).ready(function()
																			{
																				$(\'#configuration-box-form input[type="radio"]\').change(function()
																				{
																					if($(this).val() == "insert" && $(this).prop("checked") == true)
																					{
																						$("#configuration-box-form").attr("action", $("#configuration-box-form").attr("action").replace("action=copy", "action=insert"));
																					}
																					else if($(this).val() == "copy" && $(this).prop("checked") == true)
																					{
																						$("#configuration-box-form").attr("action", $("#configuration-box-form").attr("action").replace("action=insert", "action=copy"));
																					}
																				});
																			});
																			</script>';
					$contents[] = array('text' => $t_mode_selection_html);
					$languages_query_raw = "select languages_id, name, code, image, directory, sort_order,language_charset, status from " . TABLE_LANGUAGES . " order by sort_order";
					$languages_query = xtc_db_query($languages_query_raw);
					$language_select_array = array();
					while($language = xtc_db_fetch_array($languages_query))
					{
						$language_select_array[] = array('id' => $language['languages_id'], 'text' => $language['name']);
					}
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_TEMPLATE . '</span>' . xtc_draw_pull_down_menu('ref_language_id', $language_select_array));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_NAME . '</span>' . xtc_draw_input_field('name'));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_CODE . '</span>' . xtc_draw_input_field('code'));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_CHARSET . '</span>' . xtc_draw_input_field('charset'));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_IMAGE . '</span>' . xtc_draw_input_field('image', 'icon.gif'));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_DIRECTORY . '</span>' . xtc_draw_input_field('directory'));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_SORT_ORDER . '</span>' . xtc_draw_input_field('sort_order'));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_SET_DEFAULT . '</span><div class="control-group" data-gx-widget="checkbox">' . xtc_draw_checkbox_field('default', 'on') . '</div>');
					break;

				case 'edit':
					$formAction = xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=save');
					$formIsEditable = true;

					$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>';
					$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id) . '">' . BUTTON_CANCEL . '</a>';

					$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_LANGUAGE . '</b>');

					$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_NAME . '</span>' . xtc_draw_input_field('name', $lInfo->name));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_CODE . '</span>' . xtc_draw_input_field('code', $lInfo->code));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_CHARSET . '</span>' . xtc_draw_input_field('charset', $lInfo->language_charset));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_IMAGE . '</span>' . xtc_draw_input_field('image', $lInfo->image));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_DIRECTORY . '</span>' . xtc_draw_input_field('directory', $lInfo->directory));
					$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_LANGUAGE_SORT_ORDER . '</span>' . xtc_draw_input_field('sort_order', $lInfo->sort_order));
					if(DEFAULT_LANGUAGE != $lInfo->code && $lInfo->status != '0')
						$contents[] = array('text' => '<span class="options-title">' . TEXT_SET_DEFAULT . '</span><div class="control-group" data-gx-widget="checkbox">' . xtc_draw_checkbox_field('default', 'on') . '</div>');
					break;

				case 'delete':

					$buttons = '';
					if($remove_language)
					{
						$pageToken = $_SESSION['coo_page_token']->generate_token();
						$buttons .= '<a class="btn btn-primary" onClick="this.blur();" href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=deleteconfirm&page_token=' . $pageToken) . '">' . BUTTON_DELETE . '</a>';
					}
					$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id) . '">' . BUTTON_CANCEL . '</a>';

					$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_LANGUAGE . '</b>');

					$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
					$contents[] = array('text' => '<br /><b>' . $lInfo->name . '</b>');
					break;

				default:
					if(is_object($lInfo))
					{
						$editButton = '<a class="btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
						$deleteButton = '<a class="btn btn-delete" href="' . xtc_href_link(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';
						$buttons = $editButton . $deleteButton;

						$heading[] = array('text' => '<b>' . $lInfo->name . '</b>');

						$contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_NAME . ' ' . $lInfo->name);
						$contents[] = array('text' => TEXT_INFO_LANGUAGE_CODE . ' ' . $lInfo->code);
						$contents[] = array('text' => TEXT_INFO_LANGUAGE_CHARSET_INFO . ' ' . $lInfo->language_charset);

						$contents[] = array('text' => '<br />' . xtc_image(DIR_WS_LANGUAGES . $lInfo->directory . '/' . $lInfo->image, $lInfo->name));
						$contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br />' . DIR_WS_LANGUAGES . '<b>' . $lInfo->directory . '</b>');
						$contents[] = array('text' => '<br />' . TEXT_INFO_LANGUAGE_SORT_ORDER . ' ' . $lInfo->sort_order);
					}
					break;
			}

			$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
			$configurationBoxContentView->setOldSchoolHeading($heading);
			$configurationBoxContentView->setOldSchoolContents($contents);
			$configurationBoxContentView->setFormAttributes($formAttributes);
			$configurationBoxContentView->set_content_data('buttons', $buttons);
			$configurationBoxContentView->setFormEditable($formIsEditable);
			$configurationBoxContentView->setFormAction($formAction);
			echo $configurationBoxContentView->get_html();
			?>
		</div>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
