<?php
/* --------------------------------------------------------------
  categories.php 2015-09-28
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
  (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
  (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: categories.php 1318 2005-10-21 19:40:59Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------
  Third Party contribution:
  Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
  New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
  Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
  Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

  Released under the GNU General Public License
  -------------------------------------------------------------- */

// ----------------------------------------------------------------------------------------------------- //
// holds functions for manipulating products & categories
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class categories_ORIGIN
{
	protected $redirect_url = null;

	// ----------------------------------------------------------------------------------------------------- //

	public function set_redirect_url($p_url)
	{
		$this->redirect_url = $p_url;
	}

	public function get_redirect_url()
	{
		$t_output = $this->redirect_url;
		return $t_output;
	}

	// deletes an array of categories, with products
	// makes use of remove_category, remove_product

	function remove_categories($category_id)
	{

		$categories = xtc_get_category_tree($category_id, '', '0', '', true);
		$products = array();
		$products_delete = array();

		for($i = 0, $n = sizeof($categories); $i < $n; $i ++)
		{
			$product_ids_query = xtc_db_query("SELECT products_id
												FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
												WHERE categories_id = '" . $categories[$i]['id'] . "'");
			while($product_ids = xtc_db_fetch_array($product_ids_query))
			{
				$products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
			}
		}

		reset($products);
		while(list ($key, $value) = each($products))
		{
			$category_ids = '';
			for($i = 0, $n = sizeof($value['categories']); $i < $n; $i ++)
			{
				$category_ids .= '\'' . $value['categories'][$i] . '\', ';
			}
			$category_ids = substr($category_ids, 0, -2);

			$check_query = xtc_db_query("SELECT COUNT(*) AS total
						    	                               FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
						    	                               WHERE products_id = '" . $key . "'
						    	                               AND categories_id NOT IN (" . $category_ids . ")");
			$check = xtc_db_fetch_array($check_query);
			if($check['total'] < '1')
			{
				$products_delete[$key] = $key;
			}
		}

		// Removing categories can be a lengthy process
		@xtc_set_time_limit(0);
		for($i = 0, $n = sizeof($categories); $i < $n; $i ++)
		{
			$this->remove_category($categories[$i]['id']);
		}

		reset($products_delete);
		while(list ($key) = each($products_delete))
		{
			$this->remove_product($key);
		}
	}

// remove_categories ends
	// ----------------------------------------------------------------------------------------------------- //
	// deletes a single category, without products

	function remove_category($category_id)
	{
		$category_image_query = xtc_db_query("SELECT categories_image FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . xtc_db_input($category_id) . "'");
		$category_image = xtc_db_fetch_array($category_image_query);

		$duplicate_image_query = xtc_db_query("SELECT count(*) AS total FROM " . TABLE_CATEGORIES . " WHERE categories_image = '" . xtc_db_input($category_image['categories_image']) . "'");
		$duplicate_image = xtc_db_fetch_array($duplicate_image_query);

		if($duplicate_image['total'] < 2)
		{
			if(file_exists(DIR_FS_CATALOG_IMAGES . 'categories/' . $category_image['categories_image']))
			{
				@unlink(DIR_FS_CATALOG_IMAGES . 'categories/' . $category_image['categories_image']);
			}
		}

		xtc_db_query("DELETE FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . xtc_db_input($category_id) . "'");
		xtc_db_query("DELETE FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . xtc_db_input($category_id) . "'");
		xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = '" . xtc_db_input($category_id) . "'");

		if(USE_CACHE == 'true')
		{
			xtc_reset_cache_block('categories');
			xtc_reset_cache_block('also_purchased');
		}
	}

// remove_category ends
	// ----------------------------------------------------------------------------------------------------- //
	// inserts / updates a category from given $categories_data array
	// Needed fields: id, sort_order, status, array(groups), products_sorting, products_sorting2, category_template,
	// listing_template, previous_image, array[name][lang_id], array[heading_title][lang_id], array[description][lang_id],
	// array[meta_title][lang_id], array[meta_description][lang_id], array[meta_keywords][lang_id]

	function insert_category($categories_data, $dest_category_id, $action = 'insert')
	{
		$categories_id = (int)$categories_data['categories_id'];
		$sort_order = xtc_db_prepare_input($categories_data['sort_order']);
		$categories_status = xtc_db_prepare_input($categories_data['status']);
		$customers_statuses_array = xtc_get_customers_statuses();
		$permission = array();

		foreach($customers_statuses_array as $t_gm_key => $t_gm_value)
		{
			if(isset($t_gm_value['id']))
			{
				$permission[$t_gm_value['id']] = 0;
			}
		}

		if(isset($categories_data['groups']))
		{
			foreach($categories_data['groups'] as $dummy => $b)
			{
				$permission[$b] = 1;
			}
		}

		// build array
		if($permission['all'] == 1)
		{
			$permission = array();

			reset($customers_statuses_array);
			foreach($customers_statuses_array AS $t_gm_key => $t_gm_value)
			{
				if(isset($t_gm_value['id']))
				{
					$permission[$t_gm_value['id']] = 1;
				}
			}
		}

		$permission_array = array();

		reset($customers_statuses_array);
		foreach($customers_statuses_array AS $t_gm_key => $t_gm_value)
		{
			if(isset($t_gm_value['id']))
			{
				$permission_array = array_merge($permission_array, array('group_permission_' . $t_gm_value['id'] => $permission[$t_gm_value['id']]));
			}
		}

		$sql_data_array = array('sort_order' => $sort_order,
			'categories_status' => $categories_status,
			'products_sorting' => xtc_db_prepare_input($categories_data['products_sorting']),
			'products_sorting2' => xtc_db_prepare_input($categories_data['products_sorting2']),
			'categories_template' => xtc_db_prepare_input($categories_data['categories_template']),
			'listing_template' => xtc_db_prepare_input($categories_data['listing_template']),
			'gm_show_attributes' => xtc_db_prepare_input($categories_data['gm_show_attributes']),
			'gm_show_graduated_prices' => xtc_db_prepare_input($categories_data['gm_show_graduated_prices']),
			'gm_priority' => xtc_db_prepare_input($categories_data['gm_priority']),
			'gm_sitemap_entry' => xtc_db_prepare_input($categories_data['gm_sitemap_entry']),
			'gm_changefreq' => xtc_db_prepare_input($categories_data['gm_changefreq']),
			'gm_show_qty' => xtc_db_prepare_input($categories_data['gm_show_qty']),
			'gm_show_qty_info' => xtc_db_prepare_input($categories_data['gm_show_qty_info']),
			'show_sub_categories' => xtc_db_prepare_input($categories_data['show_sub_categories']),
			'show_sub_categories_images' => xtc_db_prepare_input($categories_data['show_sub_categories_images']),
			'show_sub_categories_names' => xtc_db_prepare_input($categories_data['show_sub_categories_names']),
			'show_sub_products' => xtc_db_prepare_input($categories_data['show_sub_products']),
			'view_mode_tiled' => xtc_db_prepare_input($categories_data['view_mode_tiled']),
			'show_category_filter' => xtc_db_prepare_input($categories_data['show_category_filter']));

		$sql_data_array = array_merge($sql_data_array, $permission_array);

		if($action == 'insert')
		{
			$insert_sql_data = array('parent_id' => $dest_category_id, 'date_added' => 'now()');
			$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES, $sql_data_array);
			$categories_id = xtc_db_insert_id();
		}
		elseif($action == 'update')
		{
			$update_sql_data = array('last_modified' => 'now()');
			$sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . (int)$categories_id . '\'');
		}

		$this->save_cat_slider($categories_id);

		if(isset($_POST['set_groups_recursive']) && $_POST['set_groups_recursive'] == '1')
		{
			xtc_set_groups($categories_id, $permission_array);
		}

		$languages = xtc_get_languages();
		foreach($languages as $lang)
		{
			$gm_url_keywords = xtc_db_prepare_input($categories_data['gm_url_keywords'][$lang['id']]);
			$gm_url_keywords = xtc_cleanName($gm_url_keywords);

			$sql_data_array = array('categories_name' => xtc_db_prepare_input($categories_data['categories_name'][$lang['id']]),
				'categories_heading_title' => xtc_db_prepare_input($categories_data['categories_heading_title'][$lang['id']]),
				'categories_description' => xtc_db_prepare_input($categories_data['categories_description'][$lang['id']]),
				'categories_meta_title' => xtc_db_prepare_input($categories_data['categories_meta_title'][$lang['id']]),
				'categories_meta_description' => xtc_db_prepare_input($categories_data['categories_meta_description'][$lang['id']]),
				'categories_meta_keywords' => xtc_db_prepare_input($categories_data['categories_meta_keywords'][$lang['id']]),
				'gm_url_keywords' => $gm_url_keywords,
				'gm_alt_text' => xtc_db_prepare_input($categories_data['gm_categories_image_alt_text_' . $lang['id']]));

			if($action == 'insert')
			{
				// sometimes entry already exists - for whatever reason
				$t_gm_check = xtc_db_query("SELECT categories_id
											FROM " . TABLE_CATEGORIES_DESCRIPTION . "
											WHERE
												categories_id = '" . (int)$categories_id . "' AND
												language_id = '" . (int)$lang['id'] . "'");
				if(xtc_db_num_rows($t_gm_check) == 0)
				{
					$insert_sql_data = array('categories_id' => $categories_id, 'language_id' => $lang['id']);
					$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
				}
			}
			elseif($action == 'update')
			{
				$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \'' . (int)$categories_id . '\' and language_id = \'' . $lang['id'] . '\'');

				if(xtc_db_num_rows(xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . (int)$categories_id . "' and language_id = '" . $lang['id'] . "'")) == 0)
				{
					$insert_sql_data = array('categories_id' => (int)$categories_id, 'language_id' => $lang['id']);
					$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
					$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
				}
			}
		}

		$sql_data_array = array();

		// bof gm category upload
		if(!empty($_FILES['categories_image']['tmp_name']))
		{
			$gmCat = new GMCatUpload($_FILES['categories_image'], $_POST['gm_categories_image_name'], DIR_FS_CATALOG_IMAGES . 'categories/', $_POST['categories_previous_image'], $categories_id);
			$categories_image_name = $gmCat->upload_file();
				
			if($categories_image_name && $categories_image_name[1] !== false)
			{
				// unlink old file
				if(!empty($_POST['categories_previous_image']) && $_POST['categories_previous_image'] != $categories_image_name[1])
				{
					unlink(DIR_FS_CATALOG_IMAGES . 'categories/' . $_POST['categories_previous_image']);
				}

				$sql_data_array = array('categories_image' => xtc_db_input($categories_image_name[1]));
			}
			else
			{
				$error_message = sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, 'images/categories');
				$GLOBALS['messageStack']->add_session($error_message, 'error');
			}

			unset($gmCat);
		}
		// perform rename
		else if(empty($_FILES['categories_image']['tmp_name']) && !empty($_POST['gm_categories_image_name']))
		{
			$gmCat = new GMCatUpload('', $_POST['gm_categories_image_name'], DIR_FS_CATALOG_IMAGES . 'categories/', $_POST['categories_previous_image'], $categories_id);

			if($categories_image_name = $gmCat->rename_file($_POST['categories_previous_image']))
			{
				$sql_data_array = array('categories_image' => xtc_db_input($categories_image_name[1]));
			}
			unset($gmCat);
		}

		// deleting files
		if($categories_data['del_cat_pic'] == 'yes')
		{
			@unlink(DIR_FS_CATALOG_IMAGES . 'categories/' . $categories_data['categories_previous_image']);
			$sql_data_array = array_merge($sql_data_array, array('categories_image' => ''));
		}

		if($categories_image_name[0])
		{
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . (int)$categories_id . '\'');
			$this->set_redirect_url(xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&cID=' . $_GET['cID'] . '&action=edit_category&gm_redirect=1#gm_anchor'));
			return $categories_id;
		}
		// eof gm category upload
		// bof gm category icon upload
		if(!empty($_FILES['categories_icon']['tmp_name']))
		{
			$gmCatIco = new GMCatUpload($_FILES['categories_icon'], $_POST['gm_categories_icon_name'], DIR_FS_CATALOG_IMAGES . 'categories/icons/', $_POST['categories_previous_icon'], $categories_id);
			$categories_icon_name = $gmCatIco->upload_file();
			
			if($categories_icon_name && $categories_icon_name[1] !== false && file_exists(DIR_FS_CATALOG_IMAGES . 'categories/icons/' . $categories_icon_name[1]))
			{
				// unlink old file
				if(!empty($_POST['categories_previous_icon']) && $_POST['categories_previous_icon'] != $categories_icon_name[1])
				{
					unlink(DIR_FS_CATALOG_IMAGES . 'categories/icons/' . $_POST['categories_previous_icon']);
				}

				$gm_icon_size = @getimagesize(DIR_FS_CATALOG_IMAGES . 'categories/icons/' . $categories_icon_name[1]);

				$sql_data_array = array_merge($sql_data_array, array('categories_icon' => xtc_db_input($categories_icon_name[1]),
					'categories_icon_w' => $gm_icon_size[0],
					'categories_icon_h' => $gm_icon_size[1]));
			}
			else
			{
				$error_message = sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, 'images/categories/icons');
				$GLOBALS['messageStack']->add_session($error_message, 'error');
			}

			unset($gmCatIco);
		}
		// perform rename
		else if(empty($_FILES['categories_icon']['tmp_name']) && !empty($_POST['gm_categories_icon_name']))
		{
			$gmCatIco = new GMCatUpload('', $_POST['gm_categories_icon_name'], DIR_FS_CATALOG_IMAGES . 'categories/icons/', $_POST['categories_previous_icon'], $categories_id);

			if($categories_icon_name = $gmCatIco->rename_file($_POST['categories_previous_icon']))
			{
				$sql_data_array = array_merge($sql_data_array, array('categories_icon' => xtc_db_input($categories_icon_name[1])));
			}

			unset($gmCatIco);
		}

		// deleting files
		if($categories_data['del_cat_ico'] == 'yes')
		{
			@unlink(DIR_FS_CATALOG_IMAGES . 'categories/icons/' . $categories_data['categories_previous_icon']);
			$sql_data_array = array_merge($sql_data_array, array('categories_icon' => ''));
		}

		if(empty($sql_data_array) == false)
		{
			$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . (int)$categories_id . '\'');
		}

		// eof gm category icon upload

		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$coo_seo_boost->repair('categories');

		return $categories_id;
	}

// insert_category ends
	// ----------------------------------------------------------------------------------------------------- //

	function set_category_recursive($categories_id, $status = "0")
	{

		// get products in category
		/* // don't set products status at the moment
		  $products_query=xtc_db_query("SELECT products_id FROM ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id='".$categories_id."'");
		  while ($products=xtc_db_fetch_array($products_query)) {
		  xtc_db_query("UPDATE ".TABLE_PRODUCTS." SET products_status='".$status."' where products_id='".$products['products_id']."'");
		  }
		 */
		// set status of category
		xtc_db_query("UPDATE " . TABLE_CATEGORIES . " SET categories_status = '" . (int)$status . "' WHERE categories_id = '" . (int)$categories_id . "'");
		// look for deeper categories and go rekursiv
		$categories_query = xtc_db_query("SELECT categories_id FROM " . TABLE_CATEGORIES . " WHERE parent_id='" . (int)$categories_id . "'");
		while($categories = xtc_db_fetch_array($categories_query))
		{
			$this->set_category_recursive($categories['categories_id'], $status);
		}
	}

	// ----------------------------------------------------------------------------------------------------- //
	// moves a category to new parent category

	function move_category($src_category_id, $dest_category_id)
	{
		$src_category_id = xtc_db_prepare_input($src_category_id);
		$dest_category_id = xtc_db_prepare_input($dest_category_id);
		xtc_db_query("UPDATE " . TABLE_CATEGORIES . "
				    	                 SET parent_id     = '" . xtc_db_input($dest_category_id) . "', last_modified = now()
				    	               WHERE categories_id = '" . xtc_db_input($src_category_id) . "'");

		$coo_categories_index = MainFactory::create_object('CategoriesIndex');
		$coo_feature_product_finder = MainFactory::create_object('IndexFeatureProductFinder');
		// Kategorie hinzugfügen
		$coo_feature_product_finder->add_categories_id($src_category_id);
		// SQL string zum Artikel finden ermitteln
		$products_listing_sql = $coo_feature_product_finder->get_products_listing_sql_string();
		// SQL String ausführen
		$product_ids_query = xtc_db_query($products_listing_sql);
		// Artikel neu zuweisen
		while($product_ids = xtc_db_fetch_array($product_ids_query))
		{
			$coo_categories_index->build_categories_index($product_ids['products_id']);
		}
	}

	// ----------------------------------------------------------------------------------------------------- //
	// copies a category to new parent category, takes argument to link or duplicate its products
	// arguments are "link" or "duplicate"
	// $copied is an array of ID's that were already newly created, and is used to prevent them from being
	// copied recursively again

	function copy_category($src_category_id, $dest_category_id, $ctype = "link")
	{

		//skip category if it is already a copied one
		if(!(in_array($src_category_id, $_SESSION['copied'])))
		{

			$src_category_id = (int)$src_category_id;
			$dest_category_id = (int)$dest_category_id;

			//get data
			$ccopy_query = xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES . " WHERE categories_id = '" . $src_category_id . "'");
			$ccopy_values = xtc_db_fetch_array($ccopy_query);

			//get descriptions
			$cdcopy_query = xtc_db_query("SELECT * FROM " . TABLE_CATEGORIES_DESCRIPTION . " WHERE categories_id = '" . $src_category_id . "'");

			//copy data
			// BOF GM_MOD
			$sql_data_array = array('parent_id' => xtc_db_input($dest_category_id),
				'date_added' => 'NOW()',
				'last_modified' => 'NOW()',
				'categories_image' => $ccopy_values['categories_image'],
				'categories_status' => $ccopy_values['categories_status'],
				'categories_template' => $ccopy_values['categories_template'],
				'listing_template' => $ccopy_values['listing_template'],
				'sort_order' => $ccopy_values['sort_order'],
				'products_sorting' => $ccopy_values['products_sorting'],
				'products_sorting2' => $ccopy_values['products_sorting2'],
				'gm_show_attributes' => $ccopy_values['gm_show_attributes'],
				'gm_show_graduated_prices' => $ccopy_values['gm_show_graduated_prices'],
				'gm_show_qty' => $ccopy_values['gm_show_qty'],
				'gm_priority' => $ccopy_values['gm_priority'],
				'gm_changefreq' => $ccopy_values['gm_changefreq'],
				'gm_sitemap_entry' => $ccopy_values['gm_sitemap_entry'],
				'gm_show_qty_info' => $ccopy_values['gm_show_qty_info'],
				'show_sub_categories' => $ccopy_values['show_sub_categories'],
				'show_sub_categories_images' => $ccopy_values['show_sub_categories_images'],
				'show_sub_categories_names' => $ccopy_values['show_sub_categories_names'],
				'show_sub_products' => $ccopy_values['show_sub_products'],
				'view_mode_tiled' => $ccopy_values['view_mode_tiled']);
			// EOF GM_MOD

			$customers_statuses_array = xtc_get_customers_statuses();

			// BOF GM_MOD
			foreach($customers_statuses_array as $t_gm_key => $t_gm_value)
			{
				if(isset($t_gm_value['id']))
				{
					$sql_data_array = array_merge($sql_data_array, array('group_permission_' . $t_gm_value['id'] => $ccopy_values['group_permission_' . $t_gm_value['id']]));
				}
			}
			// EOF GM_MOD

			$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES, $sql_data_array);

			$new_cat_id = xtc_db_insert_id();

			//store copied ids, because we don't want to go into an endless loop later
			$_SESSION['copied'][] = $new_cat_id;

			//copy / link products
			$get_prod_query = xtc_db_query("SELECT products_id FROM " . TABLE_PRODUCTS_TO_CATEGORIES . " WHERE categories_id = '" . $src_category_id . "'");
			while($product = xtc_db_fetch_array($get_prod_query))
			{
				if($ctype == 'link')
				{
					$this->link_product($product['products_id'], $new_cat_id);
				}
				elseif($ctype == 'duplicate')
				{
					$this->duplicate_product($product['products_id'], $new_cat_id);
				}
				else
				{
					die('Undefined copy type!');
				}
			}

			//copy+rename image
			$src_pic = DIR_FS_CATALOG_IMAGES . 'categories/' . $ccopy_values['categories_image'];
			$t_images_array = array();
			if(is_file($src_pic))
			{
				$get_suffix = explode('.', $ccopy_values['categories_image']);
				$suffix = array_pop($get_suffix);
				$t_categories_image = $new_cat_id . '.' . $suffix;
				@copy($src_pic, DIR_FS_CATALOG_IMAGES . 'categories/' . $t_categories_image);
				$t_images_array['categories_image'] = $t_categories_image;
			}

			//copy+rename icon
			$gm_src_pic = DIR_FS_CATALOG_IMAGES . 'categories/icons/' . $ccopy_values['categories_icon'];
			if(is_file($gm_src_pic))
			{
				$get_suffix = explode('.', $ccopy_values['categories_icon']);
				$suffix = array_pop($get_suffix);
				$t_categories_icon = $new_cat_id . '.' . $suffix;
				@copy($gm_src_pic, DIR_FS_CATALOG_IMAGES . 'categories/icons/' . $t_categories_icon);
				$t_images_array['categories_icon'] = $t_categories_icon;
			}

			if(count($t_images_array) > 0)
			{
				$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES, $t_images_array, 'update', 'categories_id = "' . $new_cat_id . '"');
			}

			//copy descriptions
			while($cdcopy_values = xtc_db_fetch_array($cdcopy_query))
			{
				// BOF GM_MOD
				// sometimes entry already exists - for whatever reason
				$t_gm_check = xtc_db_query("SELECT categories_id
														FROM " . TABLE_CATEGORIES_DESCRIPTION . "
														WHERE
															categories_id = '" . (int)$new_cat_id . "' AND
															language_id = '" . (int)$cdcopy_values['language_id'] . "'");
				if(xtc_db_num_rows($t_gm_check) == 0)
				{
					$t_sql_data = array();
					$t_sql_data['categories_id'] = $new_cat_id;
					$t_sql_data['language_id'] = $cdcopy_values['language_id'];
					$t_sql_data['categories_name'] = addslashes($cdcopy_values['categories_name'] . ' - ' . TEXT_COPY);
					$t_sql_data['categories_heading_title'] = addslashes($cdcopy_values['categories_heading_title']);
					$t_sql_data['categories_description'] = $cdcopy_values['categories_description'];
					$t_sql_data['categories_meta_title'] = addslashes($cdcopy_values['categories_meta_title']);
					$t_sql_data['categories_meta_description'] = addslashes($cdcopy_values['categories_meta_description']);
					$t_sql_data['categories_meta_keywords'] = addslashes($cdcopy_values['categories_meta_keywords']);
					$t_sql_data['gm_alt_text'] = addslashes($cdcopy_values['gm_alt_text']);
					$t_sql_data['gm_url_keywords'] = addslashes($cdcopy_values['gm_url_keywords']);

					$this->wrapped_db_perform(__FUNCTION__, TABLE_CATEGORIES_DESCRIPTION, $t_sql_data);
				}
				// EOF GM_MOD
			}

			//get child categories of current category
			$crcopy_query = xtc_db_query("SELECT categories_id FROM " . TABLE_CATEGORIES . " WHERE parent_id = '" . $src_category_id . "'");

			//and go recursive
			while($crcopy_values = xtc_db_fetch_array($crcopy_query))
			{
				$this->copy_category($crcopy_values['categories_id'], $new_cat_id, $ctype);
			}
		}

		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$coo_seo_boost->repair('categories');
	}

	// ----------------------------------------------------------------------------------------------------- //
	// removes a product + images + more images + content

	function remove_product($p_products_id)
	{
		$c_products_id = (int)$p_products_id;

		// get content of product
		$product_content_query = xtc_db_query("SELECT content_file
												FROM " . TABLE_PRODUCTS_CONTENT . "
												WHERE products_id = '" . $c_products_id . "'");
		// check if used elsewhere, delete db-entry + file if not
		while($product_content = xtc_db_fetch_array($product_content_query))
		{
			$duplicate_content_query = xtc_db_query("SELECT count(*) AS total
														FROM " . TABLE_PRODUCTS_CONTENT . "
														WHERE
															content_file = '" . xtc_db_input($product_content['content_file']) . "'
														AND
															products_id != '" . $c_products_id . "'");

			$duplicate_content = xtc_db_fetch_array($duplicate_content_query);

			if($duplicate_content['total'] == 0)
			{
				@unlink(DIR_FS_DOCUMENT_ROOT . 'media/products/' . $product_content['content_file']);
			}

			//delete DB-Entry
			xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_CONTENT . "
							WHERE
								products_id = '" . $c_products_id . "'
							AND
								(content_file = '" . $product_content['content_file'] . "' OR content_file = '')");
		}

		$product_image_query = xtc_db_query("SELECT products_image
												FROM " . TABLE_PRODUCTS . "
												WHERE products_id = '" . $c_products_id . "'");
		$product_image = xtc_db_fetch_array($product_image_query);

		$duplicate_image_query = xtc_db_query("SELECT count(*) AS total
												FROM " . TABLE_PRODUCTS . "
												WHERE products_image = '" . xtc_db_input($product_image['products_image']) . "'");
		$duplicate_image = xtc_db_fetch_array($duplicate_image_query);

		if($duplicate_image['total'] < 2)
		{
			xtc_del_image_file($product_image['products_image']);
		}

		$coo_unit_handler = MainFactory::create_object('ProductQuantityUnitHandler');
		$coo_unit_handler->remove_quantity_unit($c_products_id);
		unset($coo_unit_handler);

		//delete more images
		$mo_images_query = xtc_db_query("SELECT image_name
											FROM " . TABLE_PRODUCTS_IMAGES . "
											WHERE products_id = '" . $c_products_id . "'");
		while($mo_images_values = xtc_db_fetch_array($mo_images_query))
		{
			$duplicate_more_image_query = xtc_db_query("SELECT count(*) AS total
														FROM " . TABLE_PRODUCTS_IMAGES . "
														WHERE image_name = '" . $mo_images_values['image_name'] . "'");
			$duplicate_more_image = xtc_db_fetch_array($duplicate_more_image_query);
			if($duplicate_more_image['total'] < 2)
			{
				xtc_del_image_file($mo_images_values['image_name']);
			}
		}

		$this->wrapped_db_perform(__FUNCTION__, TABLE_SPECIALS, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_TO_CATEGORIES, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_DESCRIPTION, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_ATTRIBUTES, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, TABLE_CUSTOMERS_BASKET_ATTRIBUTES, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');

		// bof gm

		$this->wrapped_db_perform(__FUNCTION__, 'gm_prd_img_alt', array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, GM_TABLE_GM_GMOTION, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, GM_TABLE_GM_GMOTION_PRODUCTS, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, 'categories_index', array(), 'delete', 'products_id = \'' . $c_products_id . '\'');


		$coo_additional_field_control = MainFactory::create_object('AdditionalFieldControl');
		$t_additional_product_fields_array = $coo_additional_field_control->get_field_names_by_item_type('product');
		foreach($t_additional_product_fields_array as $coo_additional_field)
		{
			$coo_additional_field->delete_all_field_values_by_item_id($c_products_id);
		}

		// delete feature-sets and their values
		$t_result = xtc_db_query("SELECT feature_set_id
									FROM feature_set_to_products
									WHERE products_id = '" . $c_products_id . "'");
		if(xtc_db_num_rows($t_result) > 0)
		{
			$coo_feature_set_source = MainFactory::create_object('FeatureSetSource');
			$t_set_id_array = array();
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$t_set_id_array[] = $t_row['feature_set_id'];
			}
			$this->wrapped_db_perform(__FUNCTION__, 'feature_set_to_products', array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
			foreach($t_set_id_array as $t_feature_set_id)
			{
				if($coo_feature_set_source->feature_set_still_in_use($t_feature_set_id) == false)
				{
					$this->wrapped_db_perform(__FUNCTION__, 'feature_index', array(), 'delete', 'feature_set_id = \'' . (int)$t_feature_set_id . '\'');
					$this->wrapped_db_perform(__FUNCTION__, 'feature_set', array(), 'delete', 'feature_set_id = \'' . (int)$t_feature_set_id . '\'');
					$this->wrapped_db_perform(__FUNCTION__, 'feature_set_values', array(), 'delete', 'feature_set_id = \'' . (int)$t_feature_set_id . '\'');
				}
			}
		}

		$coo_properties_combis_control = MainFactory::create_object('PropertiesCombisAdminControl');
		$coo_properties_combis_control->delete_all_combis($c_products_id);

		$this->wrapped_db_perform(__FUNCTION__, 'products_properties_combis_defaults', array(), 'delete', 'products_id = \'' . $c_products_id . '\'');

		// eof gm

		$customers_statuses_array = xtc_get_customers_statuses();

		// BOF GM_MOD
		foreach($customers_statuses_array as $t_gm_key => $t_gm_value)
		{
			if(isset($t_gm_value['id']))
			{
				$this->wrapped_db_perform(__FUNCTION__, 'personal_offers_by_customers_status_' . $t_gm_value['id'], array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
			}
		}
		// EOF GM_MOD

		$product_reviews_query = xtc_db_query("SELECT reviews_id
												FROM " . TABLE_REVIEWS . "
												WHERE products_id = '" . $c_products_id . "'");
		while($product_reviews = xtc_db_fetch_array($product_reviews_query))
		{
			$this->wrapped_db_perform(__FUNCTION__, TABLE_REVIEWS_DESCRIPTION, array(), 'delete', 'reviews_id = \'' . (int)$product_reviews['reviews_id'] . '\'');
		}

		$this->wrapped_db_perform(__FUNCTION__, TABLE_REVIEWS, array(), 'delete', 'products_id = \'' . $c_products_id . '\'');

		if(USE_CACHE == 'true')
		{
			xtc_reset_cache_block('categories');
			xtc_reset_cache_block('also_purchased');
		}

		$this->wrapped_db_perform(__FUNCTION__, 'products_google_categories', array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
		$this->wrapped_db_perform(__FUNCTION__, 'products_item_codes', array(), 'delete', 'products_id = \'' . $c_products_id . '\'');
	}

// remove_product ends
	// ----------------------------------------------------------------------------------------------------- //
	// deletes given product from categories, removes it completely if no category is left

	function delete_product($product_id, $product_categories)
	{

		for($i = 0, $n = sizeof($product_categories); $i < $n; $i ++)
		{

			xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
											              WHERE products_id   = '" . xtc_db_input($product_id) . "'
											              AND categories_id = '" . xtc_db_input($product_categories[$i]) . "'");
			if(($product_categories[$i]) == 0)
			{
				$this->set_product_startpage($product_id, 0);
			}
		}

		$coo_categories_index = MainFactory::create_object('CategoriesIndex');
		$coo_categories_index->build_categories_index((int)$product_id);


		$product_categories_query = xtc_db_query("SELECT COUNT(*) AS total
								                                            FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
								                                           WHERE products_id = '" . xtc_db_input($product_id) . "'");

		$product_categories = xtc_db_fetch_array($product_categories_query);

		if($product_categories['total'] == '0')
		{
			$this->remove_product($product_id);
		}

		// BOF GM_MOD
		// manage teaser slider for product
		$coo_product_slider_handler = MainFactory::create_object('ProductSliderHandler');
		$coo_product_slider_handler->remove_product_slider($product_id);
		// EOF GM_MOD
	}

// delete_product ends

	// ----------------------------------------------------------------------------------------------------- //
	// remove all links between a specific product and all categories

	function remove_all_product_links($productId)
	{
		$query = 'DELETE FROM products_to_categories WHERE products_id = ' . (int)$productId;
		xtc_db_query($query);
	}

	// ----------------------------------------------------------------------------------------------------- //
	// inserts / updates a product from given data

	function insert_product($products_data, $dest_category_id, $action = 'insert')
	{
		$_SESSION['gm_redirect'] = 0;

		$products_id = (int)$products_data['products_id'];
		$products_date_available = xtc_db_prepare_input($products_data['products_date_available']);

		$products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

		$products_status = xtc_db_prepare_input($products_data['products_status']);

		$this->remove_all_product_links($products_data['products_id']);
		foreach($products_data['categories'] as $category_id)
		{
			if($category_id != -1)
			{
				$this->link_product($products_data['products_id'], $category_id);
			}
		}

		if($products_data['products_startpage'] == 1)
		{
			$this->link_product($products_data['products_id'], 0);
		}

		if(PRICE_IS_BRUTTO == 'true' && $products_data['products_price'])
		{
			$products_data['products_price'] = round(($products_data['products_price'] / (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100), PRICE_PRECISION);
		}

		$customers_statuses_array = xtc_get_customers_statuses();

		$permission = array();

		foreach($customers_statuses_array AS $t_gm_key => $t_gm_value)
		{
			if(isset($t_gm_value['id']))
			{
				$permission[$t_gm_value['id']] = 0;
			}
		}

		if(isset($products_data['groups']))
		{
			foreach($products_data['groups'] AS $dummy => $b)
			{
				$permission[$b] = 1;
			}
		}
		// build array
		if($permission['all'] == 1)
		{
			$permission = array();
			reset($customers_statuses_array);

			foreach($customers_statuses_array AS $t_gm_key => $t_gm_value)
			{
				if(isset($t_gm_value['id']))
				{
					$permission[$t_gm_value['id']] = 1;
				}
			}
		}

		$permission_array = array();

		reset($customers_statuses_array);
		foreach($customers_statuses_array AS $t_gm_key => $t_gm_value)
		{
			if(isset($t_gm_value['id']))
			{
				$permission_array = array_merge($permission_array, array('group_permission_' . $t_gm_value['id'] => $permission[$t_gm_value['id']]));
			}
		}

		if((double)$products_data['gm_min_order'] > 0)
		{
			$gm_min_order = (double)$products_data['gm_min_order'];
		}
		else
		{
			$gm_min_order = 1;
		}

		if((double)$products_data['gm_graduated_qty'] > 0)
		{
			$gm_graduated_qty = (double)$products_data['gm_graduated_qty'];
		}
		else
		{
			$gm_graduated_qty = 1;
		}

		if($gm_min_order < $gm_graduated_qty)
		{
			$gm_min_order = $gm_graduated_qty;
		}

		$sql_data_array = array('products_quantity' => xtc_db_prepare_input($products_data['products_quantity']),
			'product_type' => xtc_db_prepare_input($products_data['product_type']),
			'products_model' => xtc_db_prepare_input($products_data['products_model']),
			'products_ean' => xtc_db_prepare_input($products_data['products_ean']),
			'products_price' => xtc_db_prepare_input($products_data['products_price']),
			'products_sort' => xtc_db_prepare_input($products_data['products_sort']),
			'products_shippingtime' => xtc_db_prepare_input($products_data['shipping_status']),
			'products_discount_allowed' => xtc_db_prepare_input($products_data['products_discount_allowed']),
			'products_date_available' => $products_date_available,
			'products_weight' => xtc_db_prepare_input($products_data['products_weight']),
			'products_status' => $products_status,
			'products_startpage' => xtc_db_prepare_input($products_data['products_startpage']),
			'products_startpage_sort' => xtc_db_prepare_input($products_data['products_startpage_sort']),
			'products_tax_class_id' => xtc_db_prepare_input($products_data['products_tax_class_id']),
			'product_template' => xtc_db_prepare_input($products_data['info_template']),
			'options_template' => xtc_db_prepare_input($products_data['options_template']),
			'manufacturers_id' => xtc_db_prepare_input($products_data['manufacturers_id']),
			'products_fsk18' => xtc_db_prepare_input($products_data['fsk18']),
			'products_vpe_value' => xtc_db_prepare_input($products_data['products_vpe_value']),
			'products_vpe_status' => xtc_db_prepare_input($products_data['products_vpe_status']),
			'products_vpe' => xtc_db_prepare_input($products_data['products_vpe']),
			'gm_show_date_added' => xtc_db_prepare_input($products_data['gm_show_date_added']),
			'gm_show_price_offer' => xtc_db_prepare_input($products_data['gm_show_price_offer']),
			'gm_price_status' => xtc_db_prepare_input($products_data['gm_price_status']),
			'gm_show_qty_info' => xtc_db_prepare_input($products_data['gm_show_qty_info']),
			'gm_min_order' => $gm_min_order,
			'gm_show_weight' => xtc_db_prepare_input($products_data['gm_show_weight']),
			'gm_graduated_qty' => $gm_graduated_qty,
			'gm_priority' => xtc_db_prepare_input($products_data['gm_priority']),
			'gm_changefreq' => xtc_db_prepare_input($products_data['gm_changefreq']),
			'gm_sitemap_entry' => xtc_db_prepare_input($products_data['gm_sitemap_entry']),
			'gm_options_template' => xtc_db_prepare_input($products_data['gm_options_template']),
			'nc_ultra_shipping_costs' => xtc_db_prepare_input($products_data['nc_ultra_shipping_costs']));

		$sql_data_array = array_merge($sql_data_array, $permission_array);

		//get the next auto_increment-value from table products if no products_id is set
		if(!$products_id || $products_id == '')
		{
			$new_pid_query = xtc_db_query("SHOW TABLE STATUS LIKE '" . TABLE_PRODUCTS . "'");
			$new_pid_query_values = xtc_db_fetch_array($new_pid_query);
			$products_id = $new_pid_query_values['Auto_increment'];
		}

		$this->save_quantity_unit($products_id);

		// BOF additional_fields

		$t_sql = 'SELECT scheme_id FROM export_schemes WHERE type_id = 1 AND scheme_name LIKE "[Gambio] Artikelexport"';
		$t_result = xtc_db_query($t_sql);
		$t_export_scheme_data = xtc_db_fetch_array($t_result);
		$t_export_scheme_id = (int)$t_export_scheme_data['scheme_id'];

		if(isset($products_data['additional_field_delete_array']))
		{
			foreach($products_data['additional_field_delete_array'] as $t_delete_field_id)
			{
				$coo_delete_additional_field = MainFactory::create_object('AdditionalField', array($t_delete_field_id));
				$coo_delete_additional_field->delete();

				//Delete CSV scheme fields
				$t_sql = 'DELETE FROM export_scheme_fields WHERE scheme_id = ' . $t_export_scheme_id . ' AND created_by LIKE "gambio" AND field_name LIKE "Zusatzfeld:%[' . (int)$t_delete_field_id . ']"';
				xtc_db_query($t_sql);
			}
		}

		$t_sql = 'SELECT languages_id, code FROM ' . TABLE_LANGUAGES;
		$t_language_result = xtc_db_query($t_sql);
		$t_language_code_array = array();
		while($t_row = xtc_db_fetch_array($t_language_result))
		{
			$t_language_code_array[$t_row['languages_id']] = $t_row['code'];
		}

		if(isset($products_data['additional_field_names_array']))
		{
			foreach($products_data['additional_field_names_array'] as $t_key => $t_field_name_array)
			{
				if(strpos($t_key, 'new') !== false)
				{
					$coo_additional_field = MainFactory::create_object('AdditionalField');
					$coo_additional_field->set_field_key('product-' . md5(time() . rand()));
					$coo_additional_field->set_item_type('product');

					$coo_additional_field->set_multilingual($products_data['additional_field_multilingual'][$t_key]);
				}
				else
				{
					$coo_additional_field = MainFactory::create_object('AdditionalField', array($t_key));
				}
				$coo_additional_field->set_name_array($t_field_name_array);
				$coo_additional_field->set_field_value_array();

				$coo_additional_field_value = MainFactory::create_object('AdditionalFieldValue');
				$coo_additional_field_value->set_item_id($products_id);
				$coo_additional_field_value->set_value_array($products_data['additional_field_values_array'][$t_key]);

				$coo_additional_field->add_field_value($coo_additional_field_value);

				if(strpos($t_key, 'new') !== false)
				{
					$coo_additional_field->save(false);

					//New CSV scheme fields
					$t_csv_scheme_field_data_array = array();
					$t_csv_scheme_field_data_array['scheme_id'] = $t_export_scheme_id;
					$t_csv_scheme_field_data_array['field_content_default'] = '';
					$t_csv_scheme_field_data_array['created_by'] = 'gambio';

					if($coo_additional_field->get_multilingual())
					{
						foreach($t_field_name_array as $t_language_id => $t_additional_field_name)
						{
							$coo_csv_field_model = MainFactory::create_object('CSVFieldModel');
							$t_csv_scheme_field_data_array['field_name'] = 'Zusatzfeld: ' . $t_field_name_array[$_SESSION['languages_id']] . '.' . $t_language_code_array[$t_language_id] . ' [' . $coo_additional_field->get_additional_field_id() . ']';
							$t_csv_scheme_field_data_array['field_content'] = '{p_additional_field#' . $coo_additional_field->get_additional_field_id() . '.' . $t_language_code_array[$t_language_id] . '}';
							$coo_csv_field_model->save($t_csv_scheme_field_data_array);
						}
					}
					else
					{
						$coo_csv_field_model = MainFactory::create_object('CSVFieldModel');
						$t_csv_scheme_field_data_array['field_name'] = 'Zusatzfeld: ' . $t_field_name_array[$_SESSION['languages_id']] . ' [' . $coo_additional_field->get_additional_field_id() . ']';
						$t_csv_scheme_field_data_array['field_content'] = '{p_additional_field#' . $coo_additional_field->get_additional_field_id() . '}';
						$coo_csv_field_model->save($t_csv_scheme_field_data_array);
					}
				}
				else
				{
					$coo_additional_field->save();
				}
			}
		}
		// EOF additional_fields

		/*
		 * bof gambio product image upload
		 */
		$gmUpload = new GMProductUpload($_FILES['products_image'], $_POST['gm_prd_img_name'], $products_id);

		if($products_image_name = $gmUpload->upload())
		{
			if($products_data['products_previous_image_0'] != $products_image_name)
			{
				$dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
												FROM " . TABLE_PRODUCTS_IMAGES . "
												WHERE image_name = '" . xtc_db_input($products_data['products_previous_image_0']) . "'");
				$dup_check = xtc_db_fetch_array($dup_check_query);
				if($dup_check['total'] === '0')
				{
					$dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
												FROM " . TABLE_PRODUCTS . "
												WHERE products_image = '" . xtc_db_input($products_data['products_previous_image_0']) . "'");
					$dup_check = xtc_db_fetch_array($dup_check_query);
					if($dup_check['total'] === '1')
					{
						@xtc_del_image_file($products_data['products_previous_image_0']);
					}
				}
			}
			/*
			 * eof gambio product image upload
			 */

			require (DIR_WS_INCLUDES . 'product_gallery_images.php');
			require (DIR_WS_INCLUDES . 'product_thumbnail_images.php');
			require (DIR_WS_INCLUDES . 'product_info_images.php');
			require (DIR_WS_INCLUDES . 'product_popup_images.php');

			$gm_imagesize = getimagesize(DIR_FS_CATALOG_THUMBNAIL_IMAGES . $products_image_name);

			$sql_data_array['products_image'] = xtc_db_prepare_input($products_image_name);
			$sql_data_array['products_image_w'] = $gm_imagesize[0];
			$sql_data_array['products_image_h'] = $gm_imagesize[1];
            $gm_show_image = isset($_POST['gm_gmotion_product_image_0']) ? 1 : 0;
			$sql_data_array['gm_show_image'] = $gm_show_image;
		}
		else
		{
            $gm_show_image = isset($_POST['gm_gmotion_product_image_0']) ? 1 : 0;
            $sql_data_array['gm_show_image'] = $gm_show_image;
			if(!empty($_POST['gm_prd_img_name']) && !empty($products_data['products_previous_image_0']))
			{
				$gmRename = new GMProductUpload('', $_POST['gm_prd_img_name'], $products_id);

				if($products_image_name = $gmRename->re_name($products_data['products_previous_image_0']))
				{
					$sql_data_array['products_image'] = xtc_db_prepare_input($products_image_name);
				}
				unset($gmRename);
			}
			else
			{
				$products_image_name = $products_data['products_previous_image_0'];
			}
			
			if(!is_writable(DIR_FS_CATALOG_IMAGES . 'product_images/original_images') && isset($_FILES['products_image']))
			{
				$error_message = sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, 'images/product_images/original_images');
				$GLOBALS['messageStack']->add_session($error_message, 'error');
			}
		}

		$gm_first_image = $products_image_name;


		//are we asked to delete some pics?
		if($products_data['del_pic'] != '')
		{
			$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_DESCRIPTION, array('gm_alt_text' => ''), 'update', 'products_id = \'' . (int)$products_id . '\'');
			$dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
												FROM " . TABLE_PRODUCTS . "
												WHERE products_image = '" . $products_data['del_pic'] . "'");
			$dup_check = xtc_db_fetch_array($dup_check_query);
			if($dup_check['total'] < 2)
			{
				@xtc_del_image_file($products_data['del_pic']);
			}

			$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS, array('products_image' => ''), 'update', 'products_id = \'' . (int)$products_id . '\'');
		}
		if($products_data['del_mo_pic'] != '')
		{
			foreach($products_data['del_mo_pic'] AS $dummy => $val)
			{
				$dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
												FROM " . TABLE_PRODUCTS_IMAGES . "
												WHERE image_name = '" . $val . "'");
				$dup_check = xtc_db_fetch_array($dup_check_query);
				if($dup_check['total'] < 2)
				{
					$dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
												FROM " . TABLE_PRODUCTS . "
												WHERE products_image = '" . $val . "'");
					$dup_check = xtc_db_fetch_array($dup_check_query);
					if($dup_check['total'] === '0')
					{
						@xtc_del_image_file($val);
					}
				}

				$_gm_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_IMAGES . " WHERE image_name = '" . $val . "'");
				$_gm_img = xtc_db_fetch_array($_gm_query);
				$_gm_del_img[] = $_gm_img['image_id'];

				$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, array(), 'delete', 'products_id = \'' . (int)$products_id . '\' and image_name = \'' . $val . '\'');
			}
		}
		//bof gambio product more image upload

		// unset($_FILES['products_image']);

		$i = 0;
		if(xtc_not_null($_FILES))
		{
			foreach($_FILES as $key => $file)
			{
				if($key == 'products_image')
				{
					continue;
				}
				preg_match("/mo_pics_(\d+)/i", $key, $match);

				$i = $match[1];

				$gm_prev = $products_data['products_previous_image_' . ($i + 1)];

				if(empty($gm_prev))
				{
					$gm_prev = 'more';
				}

				$gmUpload = new GMProductUpload($file, $_POST['gm_prd_img_name_' . $i . ''], $products_id, $products_data['products_previous_image_' . ($i + 1)], $i, $gm_first_image, true);

				if($products_image_name = $gmUpload->upload())
				{
					if($_POST['products_previous_image_' . ($i + 1)] != $products_image_name)
					{
						$dup_check_query = xtc_db_query('SELECT COUNT(*) as total
															FROM ' . TABLE_PRODUCTS_IMAGES . '
															WHERE
																products_id != ' . $products_id . ' AND
																image_name = "' . xtc_db_input($products_data['products_previous_image_' . ($i + 1)]) . '"');
						$dup_check = xtc_db_fetch_array($dup_check_query);
						if($dup_check['total'] === '0')
						{
							$dup_check_query = xtc_db_query("SELECT COUNT(*) AS total
												FROM " . TABLE_PRODUCTS . "
												WHERE products_image = '" .  xtc_db_input($products_data['products_previous_image_' . ($i + 1)]) . "'");
							$dup_check = xtc_db_fetch_array($dup_check_query);
							if($dup_check['total'] === '0')
							{
								@xtc_del_image_file($_POST['products_previous_image_' . ($i + 1)]);
							}
						}
					}

					// prepare input
                    $gm_show_image = isset($_POST['gm_gmotion_product_image_' . ($i + 1)]) ? 1 : 0;
					$mo_img = array('products_id' => xtc_db_prepare_input($products_id), 'image_nr' => xtc_db_prepare_input($i + 1), 'image_name' => xtc_db_prepare_input($products_image_name), 'gm_show_image' => $gm_show_image);

					if($action == 'insert')
					{
						// sometimes entry already exists - for whatever reason
						$t_gm_image_nr = xtc_db_prepare_input($i + 1);
						$t_gm_check = xtc_db_query("SELECT products_id
													FROM " . TABLE_PRODUCTS_IMAGES . "
													WHERE
														products_id = '" . (int)$products_id . "' AND
														image_nr = '" . $t_gm_image_nr . "'");
						unset($t_gm_image_nr);
						if(xtc_db_num_rows($t_gm_check) == 0)
						{
							$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, $mo_img);
						}
					}
					elseif($action == 'update' && !empty($products_data['products_previous_image_' . ($i + 1)]))
					{
						if(!empty($products_data['del_mo_pic']))
						{
							foreach($products_data['del_mo_pic'] AS $dummy => $val)
							{
								if($val == $products_data['products_previous_image_' . ($i + 1)])
								{
									$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, $mo_img);
								}
								break;
							}
						}

						$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, $mo_img, 'update', 'products_id = ' . $products_id . ' AND image_name = \'' . xtc_db_input($products_data['products_previous_image_' . ($i + 1)]) . '\'');
					}
					elseif(!$products_data['products_previous_image_' . ($i + 1)])
					{
						$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, $mo_img);
					}

					//image processing
					require (DIR_WS_INCLUDES . 'product_gallery_images.php');
					require (DIR_WS_INCLUDES . 'product_thumbnail_images.php');
					require (DIR_WS_INCLUDES . 'product_info_images.php');
					require (DIR_WS_INCLUDES . 'product_popup_images.php');
				}
				elseif(!is_writable(DIR_FS_CATALOG_IMAGES . 'product_images/original_images'))
				{
					$error_message = sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, 'images/product_images/original_images');
					$GLOBALS['messageStack']->add_session($error_message, 'error');
				}

				unset($gmUpload);
				//$i++;
			}
		}
		// eof gambio product more image upload

		for($i = 0; $i < (int)MO_PICS; $i++)
		{
            $gm_show_image = isset($_POST['gm_gmotion_product_image_' . ($i + 1)]) ? 1 : 0;
			if(isset($_POST['gm_prd_img_name_' . $i . '']) && isset($_POST['products_previous_image_' . ($i + 1)]) && !empty($_POST['gm_prd_img_name_' . $i . '']) && !empty($_POST['products_previous_image_' . ($i + 1)]))
			{
				$gmRename = new GMProductUpload('', $_POST['gm_prd_img_name_' . $i . ''], $products_id, $_POST['products_previous_image_' . ($i + 1)], $i, $gm_first_image, true);

				if($products_image_name = $gmRename->re_name($_POST['products_previous_image_' . ($i + 1)]))
				{
					$mo_img = array('products_id' => xtc_db_prepare_input($products_id),
						'image_nr' => xtc_db_prepare_input($i + 1),
						'image_name' => xtc_db_prepare_input($products_image_name));

					$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, $mo_img, 'update', 'image_name = \'' . xtc_db_input($products_data['products_previous_image_' . ($i + 1)]) . '\'');
				}

				unset($gmRename);
            }
			if(!empty($products_data['products_previous_image_' . ($i + 1)]))
			{
				$mo_img = array('gm_show_image' => $gm_show_image);
				$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, $mo_img, 'update', 'image_name = \'' . xtc_db_input($products_data['products_previous_image_' . ($i + 1)]) . '\'');
			}
		}

		if(isset($products_data['products_image']) && xtc_not_null($products_data['products_image']) && ($products_data['products_image'] != 'none'))
		{
			$sql_data_array['products_image'] = xtc_db_prepare_input($products_data['products_image']);
		}

		if($action == 'insert')
		{
			$sql_data_array['products_date_added'] = 'now()';
			$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS, $sql_data_array);
			$products_id = xtc_db_insert_id();

			if(is_array($dest_category_id))
			{
				foreach($dest_category_id as $categoryId)
				{
					// sometimes entry already exists - for whatever reason
					$t_gm_check = xtc_db_query("SELECT categories_id
										FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
										WHERE
											categories_id = '" . (int)$categoryId . "' AND
											products_id = '" . (int)$products_id . "'");
					if(xtc_db_num_rows($t_gm_check) == 0 && $products_id > 0)
					{
						$this->wrapped_db_perform(__FUNCTION__,
						                          TABLE_PRODUCTS_TO_CATEGORIES,
						                          array(
							                          'products_id'   => (int)$products_id,
							                          'categories_id' => (int)$categoryId
						                          ));
					}
				}
			}
			else
			{
				// sometimes entry already exists - for whatever reason
				$t_gm_check = xtc_db_query("SELECT categories_id
										FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
										WHERE
											categories_id = '" . (int)$dest_category_id . "' AND
											products_id = '" . (int)$products_id . "'");
				if(xtc_db_num_rows($t_gm_check) == 0 && $products_id > 0)
				{
					$this->wrapped_db_perform(__FUNCTION__,
					                          TABLE_PRODUCTS_TO_CATEGORIES,
					                          array(
						                          'products_id'   => (int)$products_id,
						                          'categories_id' => (int)$dest_category_id
					                          ));
				}
			}

			// manage teaser slider for product
			$product_slider_id = (int)$products_data['product_slider'];
			if(!empty($product_slider_id))
			{
				$coo_product_slider_handler = MainFactory::create_object('ProductSliderHandler');
				$coo_product_slider_handler->set_product_slider($products_id, $product_slider_id);
			}
		}
		elseif($action == 'update')
		{
			$sql_data_array['products_last_modified'] = 'now()';
			$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . (int)$products_id . '\'');

			// manage teaser slider for product
			$product_slider_id = (int)$products_data['product_slider'];
			$coo_product_slider_handler = MainFactory::create_object('ProductSliderHandler');
			$coo_product_slider_handler->remove_product_slider($products_id);

			if(!empty($product_slider_id))
			{
				$coo_product_slider_handler->set_product_slider($products_id, $product_slider_id);
			}
		}

		# insert/update item_codes BOF
		$t_item_codes_sql_data_array['products_id'] = $products_id;
		$t_item_codes_sql_data_array['code_isbn'] = $products_data['code_isbn'];
		$t_item_codes_sql_data_array['code_upc'] = $products_data['code_upc'];
		$t_item_codes_sql_data_array['code_mpn'] = $products_data['code_mpn'];
		$t_item_codes_sql_data_array['code_jan'] = $products_data['code_jan'];
		$t_item_codes_sql_data_array['brand_name'] = $products_data['brand_name'];
		$t_item_codes_sql_data_array['google_export_availability_id'] = $products_data['google_export_availability_id'];
		$t_item_codes_sql_data_array['google_export_condition'] = $products_data['google_export_condition'];
		$t_item_codes_sql_data_array['identifier_exists'] = $products_data['identifier_exists'];
		$t_item_codes_sql_data_array['gender'] = $products_data['gender'];
		$t_item_codes_sql_data_array['age_group'] = $products_data['age_group'];
		$t_item_codes_sql_data_array['expiration_date'] = $products_data['expiration_date'];

		$this->wrapped_db_perform(__FUNCTION__, 'products_item_codes', $t_item_codes_sql_data_array, 'replace');
		# insert/update item_codes EOF

		$languages = xtc_get_languages();
		// Here we go, lets write Group prices into db
		// start
		$i = 0;
		$group_query = xtc_db_query("SELECT customers_status_id
										FROM " . TABLE_CUSTOMERS_STATUS . "
										WHERE
											language_id = '" . (int)$_SESSION['languages_id'] . "' AND
											customers_status_id != '0'");
		while($group_values = xtc_db_fetch_array($group_query))
		{
			// load data into array
			$i ++;
			$group_data[$i] = array('STATUS_ID' => $group_values['customers_status_id']);
		}
		for($col = 0, $n = sizeof($group_data); $col < $n + 1; $col ++)
		{
			if($group_data[$col]['STATUS_ID'] != '')
			{
				$personal_price = xtc_db_prepare_input($products_data['products_price_' . $group_data[$col]['STATUS_ID']]);
				if($personal_price == '' || $personal_price == '0.0000')
				{
					$personal_price = '0.00';
				}
				else
				{
					if(PRICE_IS_BRUTTO == 'true')
					{
						$personal_price = ($personal_price / (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100);
					}
					$personal_price = xtc_round($personal_price, PRICE_PRECISION);
				}

				if($action == 'insert')
				{
					$this->wrapped_db_perform(__FUNCTION__, 'personal_offers_by_customers_status_' . $group_data[$col]['STATUS_ID'], array(), 'delete', 'products_id = \'' . (int)$products_id . '\'and quantity = \'1\'');

					$insert_array = array();
					$insert_array = array('personal_offer' => $personal_price,
						'quantity' => '1',
						'products_id' => $products_id);
					$this->wrapped_db_perform(__FUNCTION__, "personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'], $insert_array);
				}
				else
				{

					$this->wrapped_db_perform(__FUNCTION__, 'personal_offers_by_customers_status_' . $group_data[$col]['STATUS_ID'], array('personal_offer' => $personal_price), 'update', 'products_id = \'' . (int)$products_id . '\'and quantity = \'1\'');
				}
			}
		}
		// end
		// ok, lets check write new staffelpreis into db (if there is one)
		$i = 0;
		$group_query = xtc_db_query("SELECT customers_status_id
										FROM " . TABLE_CUSTOMERS_STATUS . "
										WHERE
											language_id = '" . (int)$_SESSION['languages_id'] . "' AND
											customers_status_id != '0'");
		while($group_values = xtc_db_fetch_array($group_query))
		{
			// load data into array
			$i ++;
			$group_data[$i] = array('STATUS_ID' => $group_values['customers_status_id']);
		}
		for($col = 0, $n = sizeof($group_data); $col < $n + 1; $col ++)
		{
			if($group_data[$col]['STATUS_ID'] != '')
			{
				// delete
				if(isset($products_data['delete_products_quantity_staffel_' . $group_data[$col]['STATUS_ID']]) && is_array($products_data['delete_products_quantity_staffel_' . $group_data[$col]['STATUS_ID']]))
				{
					foreach($products_data['delete_products_quantity_staffel_' . $group_data[$col]['STATUS_ID']] as $t_quantity)
					{
						$this->wrapped_db_perform(__FUNCTION__, 'personal_offers_by_customers_status_' . $group_data[$col]['STATUS_ID'], array(), 'delete', 'products_id = \'' . (int)$products_id . '\'and quantity = \'' . (double)$t_quantity . '\'');
					}
				}

				// insert/update
				if(isset($products_data['products_quantity_staffel_' . $group_data[$col]['STATUS_ID']]) && is_array($products_data['products_quantity_staffel_' . $group_data[$col]['STATUS_ID']]))
				{
					foreach($products_data['products_quantity_staffel_' . $group_data[$col]['STATUS_ID']] as $t_key => $t_quantity)
					{
						$c_quantity = (double)$t_quantity;
						$c_personal_offer = (double)($products_data['products_price_staffel_' . $group_data[$col]['STATUS_ID']][$t_key]);
						if(PRICE_IS_BRUTTO == 'true')
						{
							$c_personal_offer = ($c_personal_offer / (xtc_get_tax_rate($products_data['products_tax_class_id']) + 100) * 100);
						}
						$c_personal_offer = xtc_round($c_personal_offer, PRICE_PRECISION);

						if($c_quantity > 0 && $c_quantity != 1)
						{
							$check_query = xtc_db_query("SELECT quantity
															FROM personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . "
															WHERE
																products_id = '" . $products_id . "' AND
																quantity    = '" . $c_quantity . "'");
							// dont insert if same qty!
							if(xtc_db_num_rows($check_query) == 0)
							{
								$insert_array = array('price_id' => '',
									'products_id' => (int)$products_id,
									'quantity' => $c_quantity,
									'personal_offer' => $c_personal_offer);

								$this->wrapped_db_perform(__FUNCTION__, 'personal_offers_by_customers_status_' . $group_data[$col]['STATUS_ID'], $insert_array);
							}
							else
							{
								$this->wrapped_db_perform(__FUNCTION__, 'personal_offers_by_customers_status_' . $group_data[$col]['STATUS_ID'], array('personal_offer' => $c_personal_offer), 'update', 'products_id = \'' . (int)$products_id . '\'and quantity = \'' . $c_quantity . '\'');
							}
						}
					}
				}
			}
		}

		foreach($languages AS $lang)
		{
			$language_id = $lang['id'];

			$t_desc = $products_data['products_description_' . $language_id];
			$t_matches = array();
			preg_match('/(.*)\[TAB:/isU', $t_desc, $t_matches);

			if(count($t_matches) > 1)
			{
				$t_complete_description = trim($t_matches[1]);
			}
			else
			{
				$t_complete_description = trim($t_desc);
			}

			if(trim($t_complete_description) == '<br />')
			{
				$t_complete_description = '';
			}

			if(count($products_data['products_tab_' . $language_id]) > 0)
			{
				foreach($products_data['products_tab_' . $language_id] AS $key => $value)
				{
					$t_complete_description .= "[TAB:" . $products_data['products_tab_headline_' . $language_id][$key] . "]" . $value;
				}
			}

			$gm_url_keywords = xtc_db_prepare_input($products_data['gm_url_keywords'][$language_id]);
			$gm_url_keywords = xtc_cleanName($gm_url_keywords);
			$sql_data_array = array('products_name' => xtc_db_prepare_input($products_data['products_name'][$language_id]),
				'products_description' => xtc_db_prepare_input($t_complete_description),
				'products_short_description' => xtc_db_prepare_input($products_data['products_short_description_' . $language_id]),
				'products_keywords' => xtc_db_prepare_input($products_data['products_keywords'][$language_id]),
				'products_url' => xtc_db_prepare_input($products_data['products_url'][$language_id]),
				'products_meta_title' => xtc_db_prepare_input($products_data['products_meta_title'][$language_id]),
				'products_meta_description' => xtc_db_prepare_input($products_data['products_meta_description'][$language_id]),
				'products_meta_keywords' => xtc_db_prepare_input($products_data['products_meta_keywords'][$language_id]),
				'gm_url_keywords' => $gm_url_keywords,
				'gm_alt_text' => xtc_db_prepare_input($products_data['gm_alt_text'][0][$language_id]),
				'checkout_information' => xtc_db_prepare_input($products_data['checkout_information_' . $language_id]));

			if($products_data['del_pic'] != '')
			{
				$sql_data_array['gm_alt_text'] = '';
			}

			if($action == 'insert')
			{
				// sometimes entry already exists - for whatever reason
				$t_gm_check = xtc_db_query("SELECT products_id
											FROM " . TABLE_PRODUCTS_DESCRIPTION . "
											WHERE
												products_id = '" . (int)$products_id . "' AND
												language_id = '" . (int)$language_id . "'");
				if(xtc_db_num_rows($t_gm_check) == 0)
				{
					$sql_data_array['products_id'] = $products_id;
					$sql_data_array['language_id'] = $language_id;
					$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
				}
			}
			elseif($action == 'update')
			{
				$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . xtc_db_input($products_id) . '\' and language_id = \'' . $language_id . '\'');

				if(xtc_db_num_rows(xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_DESCRIPTION . " WHERE products_id = '" . xtc_db_input($products_id) . "' and language_id = '" . $language_id . "'")) == 0)
				{
					$sql_data_array['products_id'] = $products_id;
					$sql_data_array['language_id'] = $language_id;
					$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
				}
			}
		}

		unset($products_data['gm_alt_text'][0]);

		foreach($products_data['gm_alt_text'] as $imageNr => $value)
		{
			$gm_query = xtc_db_query("
									SELECT
										image_id
									FROM
										" . TABLE_PRODUCTS_IMAGES . "
									WHERE
										image_nr = '" . $imageNr . "' AND
										products_id = '" . $products_id . "'
									");


			$gm = xtc_db_fetch_array($gm_query);

			if(!empty($gm['image_id']))
			{
				$languages = xtc_get_languages();
				foreach($languages AS $lang)
				{
					$language_id = $lang['id'];
					$this->wrapped_db_perform(__FUNCTION__, 'gm_prd_img_alt', array(), 'delete', 'image_id = \'' . $gm['image_id'] . '\' AND products_id	= \'' . $products_id . '\' AND language_id = \'' . $language_id . '\'');

					if(xtc_not_null($products_data['gm_alt_text'][$imageNr][$language_id]))
					{
						$insert_array = array('language_id' => $language_id,
							'products_id' => $products_id,
							'image_id' => $gm['image_id'],
							'gm_alt_text' => gm_prepare_string($products_data['gm_alt_text'][$imageNr][$language_id]));
						$this->wrapped_db_perform(__FUNCTION__, 'gm_prd_img_alt', $insert_array);
					}
				}
			}
		}

		/* delete more images alttext */
		if($products_data['del_mo_pic'] != '')
		{
			for($i = 0; $i < count($_gm_del_img); $i++)
			{
				$this->wrapped_db_perform(__FUNCTION__, 'gm_prd_img_alt', array(), 'delete', 'image_id = \'' . $_gm_del_img[$i] . '\'');
			}
		}

		require_once(DIR_FS_CATALOG . 'gm/classes/GMGMotion.php');
		$coo_gm_gmotion = new GMGMotion();
		$coo_gm_gmotion->save();

		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$coo_seo_boost->repair('products');

		$coo_categories_index = MainFactory::create_object('CategoriesIndex');
		$coo_categories_index->build_categories_index($products_id);

		// BEGIN Hermes
		if(isset($products_data['hermes_minpclass']))
		{
			require_once DIR_FS_CATALOG . 'includes/classes/hermes.php';
			$hermes = new Hermes();
			$hermes_data = array('products_id' => (int)$products_id,
				'min_pclass' => xtc_db_prepare_input($products_data['hermes_minpclass']));
			$hermes->setProductsOptions($hermes_data);
		}
		// END Hermes
		// BOF Google
		// delete google categoriy from products
		if(count($_POST['delete_list']) > 0)
		{
			$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyViewAjaxHandler');
			$coo_taxonomy_control->set_data('action', 'delete_products_google_category');
			$coo_taxonomy_control->set_data('POST', $_POST);
			if(empty($coo_taxonomy_control->v_data_array['POST']['products_id']))
			{
				$coo_taxonomy_control->v_data_array['POST']['products_id'] = (int)$products_id;
			}
			$coo_taxonomy_control->proceed();
		}

		// add google category to a product
		if(count($_POST['category_list']) > 0)
		{
			$coo_taxonomy_control = MainFactory::create_object('GoogleTaxonomyViewAjaxHandler');
			$coo_taxonomy_control->set_data('action', 'add_products_google_category');
			$coo_taxonomy_control->set_data('POST', $_POST);
			if(empty($coo_taxonomy_control->v_data_array['POST']['products_id']))
			{
				$coo_taxonomy_control->v_data_array['POST']['products_id'] = (int)$products_id;
			}
			$coo_taxonomy_control->proceed();
		}
		// EOF Google

		if(!isset($_GET['pID']) || empty($_GET['pID']) || $_GET['pID'] < 1)
		{
			$_GET['pID'] = $products_id;
		}
        
        $categoryId = end(explode('_', $_GET['cPath']));
        $cPath = 'cPath=' . $_GET['cPath'];
        
        if(!in_array($categoryId, $products_data['categories']))
        {
            $cPath = xtc_get_path(end($products_data['categories']));
        }
        
		if($_SESSION['gm_redirect'] > 0)
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CATEGORIES, $cPath . '&action=new_product&pID=' . (int)$_GET['pID'] . '&gm_redirect=' . $_SESSION['gm_redirect'] . '#gm_anchor'));
		}
        
		if(isset($products_data['gm_update']))
		{
			$this->set_redirect_url(xtc_href_link(FILENAME_CATEGORIES, $cPath . '&action=new_product&pID=' . $products_id));
		}

		return $products_id;
	}

// insert_product ends
	// ----------------------------------------------------------------------------------------------------- //
	// duplicates a product by id into specified category by id

	function duplicate_product($src_products_id, $dest_categories_id)
	{

		$product_query = xtc_db_query("SELECT *
										FROM " . TABLE_PRODUCTS . "
									   WHERE products_id = '" . xtc_db_input($src_products_id) . "'");

		$product = xtc_db_fetch_array($product_query);
		if($dest_categories_id == 0)
		{
			$startpage = 1;
		}
		else
		{
			$startpage = 0;
		}

		// BOF GM_MOD
		if((double)$product['gm_min_order'] > 0)
			$gm_min_order = (double)$product['gm_min_order'];
		else
			$gm_min_order = 1;
		if((double)$product['gm_graduated_qty'] > 0)
			$gm_graduated_qty = (double)$product['gm_graduated_qty'];
		else
			$gm_graduated_qty = 1;
		if($gm_min_order < $gm_graduated_qty)
			$gm_min_order = $gm_graduated_qty;

		$sql_data_array = array(
			'products_ean' => $product['products_ean'],
			'use_properties_combis_quantity' => $product['use_properties_combis_quantity'],
			'products_quantity' => $product['products_quantity'],
			'use_properties_combis_shipping_time' => $product['use_properties_combis_shipping_time'],
			'products_shippingtime' => $product['products_shippingtime'],
			'products_model' => $product['products_model'],
			'products_sort' => $product['products_sort'],
			'products_price' => $product['products_price'],
			'products_discount_allowed' => $product['products_discount_allowed'],
			'products_date_added' => 'now()',
			'products_last_modified' => $product['products_last_modified'],
			'products_date_available' => $product['products_date_available'],
			'use_properties_combis_weight' => $product['use_properties_combis_weight'],
			'products_weight' => $product['products_weight'],
			'products_status' => 0,
			'products_tax_class_id' => $product['products_tax_class_id'],
			'product_template' => $product['product_template'],
			'options_template' => $product['options_template'],
			'manufacturers_id' => $product['manufacturers_id'],
			//'products_ordered'					=>$product['products_ordered'],
			'products_fsk18' => $product['products_fsk18'],
			'products_vpe' => $product['products_vpe'],
			'products_vpe_status' => $product['products_vpe_status'],
			'products_vpe_value' => $product['products_vpe_value'],
			'products_startpage' => $startpage,
			'products_startpage_sort' => $product['products_startpage_sort'],
			'group_ids' => $product['group_ids'],
			'nc_ultra_shipping_costs' => $product['nc_ultra_shipping_costs'],
			'gm_show_date_added' => $product['gm_show_date_added'],
			'gm_show_price_offer' => $product['gm_show_price_offer'],
			'gm_show_weight' => $product['gm_show_weight'],
			'gm_price_status' => $product['gm_price_status'],
			'gm_min_order' => $product['gm_min_order'],
			'gm_graduated_qty' => $product['gm_graduated_qty'],
			'gm_options_template' => $product['gm_options_template'],
			'gm_priority' => $product['gm_priority'],
			'gm_changefreq' => $product['gm_changefreq'],
			'gm_show_qty_info' => $product['gm_show_qty_info'],
			'gm_sitemap_entry' => $product['gm_sitemap_entry'],
			'products_image_w' => $product['products_image_w'],
			'products_image_h' => $product['products_image_h'],
			'properties_dropdown_mode' => $product['properties_dropdown_mode'],
			'properties_show_price' => $product['properties_show_price'],
			'product_type' => $product['product_type']
		);
		// EOF GM_MOD

		$customers_statuses_array = xtc_get_customers_statuses();

		// BOF GM_MOD
		foreach($customers_statuses_array as $t_gm_key => $t_gm_value)
		{
			if(isset($t_gm_value['id']))
			{
				$sql_data_array = array_merge($sql_data_array, array('group_permission_' . $t_gm_value['id'] => $product['group_permission_' . $t_gm_value['id']]));
			}
		}
		// EOF GM_MOD

		$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS, $sql_data_array);

		//get duplicate id
		$dup_products_id = xtc_db_insert_id();
		$this->duplicate_product_content($src_products_id, $dup_products_id);

		/* GX-Customizer assignment */
		$this->duplicate_gxcustumizer_set_assignment($src_products_id, $dup_products_id);

		$coo_unit_handler = MainFactory::create_object('ProductQuantityUnitHandler');
		$t_src_unit_id = $coo_unit_handler->get_quantity_unit_id($src_products_id);
		$coo_unit_handler->set_quantity_unit($dup_products_id, $t_src_unit_id);
		unset($coo_unit_handler);

		//duplicate image if there is one
		if($product['products_image'] != '')
		{

			//build new image_name for duplicate
			$pname_arr = explode('.', $product['products_image']);
			$nsuffix = array_pop($pname_arr);
			$dup_products_image_name = $pname_arr[0] . '_' . $dup_products_id . '_0' . '.' . $nsuffix;

			//write to DB
			$t_products_image_data_array = array('products_image' => $dup_products_image_name);
			$t_products_image_where_part = 'products_id = "' . $dup_products_id . '"';
			$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS, $t_products_image_data_array, 'update', $t_products_image_where_part);

			@copy(DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $dup_products_image_name);
			@copy(DIR_FS_CATALOG_INFO_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_INFO_IMAGES . '/' . $dup_products_image_name);
			@copy(DIR_FS_CATALOG_THUMBNAIL_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_THUMBNAIL_IMAGES . '/' . $dup_products_image_name);
			@copy(DIR_FS_CATALOG_POPUP_IMAGES . '/' . $product['products_image'], DIR_FS_CATALOG_POPUP_IMAGES . '/' . $dup_products_image_name);
			@copy(DIR_FS_CATALOG_IMAGES . 'product_images/gallery_images/' . $product['products_image'], DIR_FS_CATALOG_IMAGES . 'product_images/gallery_images/' . $dup_products_image_name);
		}
		else
		{
			unset($dup_products_image_name);
		}

		$description_query = xtc_db_query("SELECT *
											FROM " . TABLE_PRODUCTS_DESCRIPTION . "
										   WHERE products_id = '" . xtc_db_input($src_products_id) . "'");

		$old_products_id = xtc_db_input($src_products_id);
		while($description = xtc_db_fetch_array($description_query))
		{
			// BOF GM_MOD
			// sometimes entry already exists - for whatever reason
			$t_gm_check = xtc_db_query("SELECT products_id
													FROM " . TABLE_PRODUCTS_DESCRIPTION . "
													WHERE
														products_id = '" . (int)$dup_products_id . "' AND
														language_id = '" . (int)$description['language_id'] . "'");
			if(xtc_db_num_rows($t_gm_check) == 0)
			{
				$t_products_description_data_array = array(
					'products_id' => $dup_products_id,
					'language_id' => $description['language_id'],
					'products_name' => $description['products_name'],
					'products_description' => $description['products_description'],
					'products_keywords' => $description['products_keywords'],
					'products_short_description' => $description['products_short_description'],
					'products_meta_title' => $description['products_meta_title'],
					'products_meta_description' => $description['products_meta_description'],
					'products_meta_keywords' => $description['products_meta_keywords'],
					'products_url' => $description['products_url'],
					'products_viewed' => 0,
					'gm_alt_text' => $description['gm_alt_text'],
					'gm_url_keywords' => $description['gm_url_keywords'] . "-" . $dup_products_id,
					'checkout_information' => $description['checkout_information']
				);
				$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_DESCRIPTION, $t_products_description_data_array);
			}
			// EOF GM_MOD
		}

		// BOF GM_MOD sometimes entry already exists - for whatever reason
		$t_gm_check = xtc_db_query("SELECT products_id
												FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
												WHERE
													products_id = '" . (int)$dup_products_id . "' AND
													categories_id = '" . (int)$dest_categories_id . "'");
		if(xtc_db_num_rows($t_gm_check) == 0 && $dup_products_id > 0)
		{
			$t_products_to_categories_data_array = array(
				'products_id' => $dup_products_id,
				'categories_id' => xtc_db_input($dest_categories_id)
			);
			$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_TO_CATEGORIES, $t_products_to_categories_data_array);
		}
		// EOF GM_MOD
		// Renaming the product with additional "copy" when it is copied into the same category
		$query = "
			SELECT products_id
			FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
			WHERE
				products_id = '" . (int)$old_products_id . "' AND
				categories_id = '" . (int)$dest_categories_id . "'";
		$t_gm_check = xtc_db_query($query);
		if(xtc_db_num_rows($t_gm_check) > 0 && $dup_products_id > 0)
		{
			$t_products_description_data_array = array('products_name' => 'CONCAT(products_name, " - ' . stripslashes(TEXT_COPY) . '")');
			$t_products_desctription_where_part = 'products_id	= "' . $dup_products_id . '"';
			$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_DESCRIPTION, $t_products_description_data_array, 'update', $t_products_desctription_where_part, 'db_link', false);
		}

		//mo_images by Novalis@eXanto.de
		// BOF GM_MOD:
		$mo_images = xtc_get_products_mo_images($src_products_id, true);
		if(is_array($mo_images))
		{
			foreach($mo_images AS $dummy => $mo_img)
			{

				//build new image_name for duplicate
				$pname_arr = explode('.', $mo_img['image_name']);
				$nsuffix = array_pop($pname_arr);
				$dup_products_image_name = $pname_arr[0] . '_' . $dup_products_id . '_' . $mo_img['image_nr'] . '.' . $nsuffix;

				//copy org images to duplicate
				@copy(DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_ORIGINAL_IMAGES . '/' . $dup_products_image_name);
				@copy(DIR_FS_CATALOG_INFO_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_INFO_IMAGES . '/' . $dup_products_image_name);
				@copy(DIR_FS_CATALOG_THUMBNAIL_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_THUMBNAIL_IMAGES . '/' . $dup_products_image_name);
				@copy(DIR_FS_CATALOG_POPUP_IMAGES . '/' . $mo_img['image_name'], DIR_FS_CATALOG_POPUP_IMAGES . '/' . $dup_products_image_name);
				@copy(DIR_FS_CATALOG_IMAGES . 'product_images/gallery_images/' . $mo_img['image_name'], DIR_FS_CATALOG_IMAGES . 'product_images/gallery_images/' . $dup_products_image_name);

				// BOF GM_MOD sometimes entry already exists - for whatever reason
				$t_gm_check = xtc_db_query("SELECT products_id
														FROM " . TABLE_PRODUCTS_IMAGES . "
														WHERE
															products_id = '" . (int)$dup_products_id . "' AND
															image_nr = '" . (int)$mo_img['image_nr'] . "'");
				if(xtc_db_num_rows($t_gm_check) == 0)
				{
					$t_products_images_data_array = array(
						'products_id' => $dup_products_id,
						'image_nr' => $mo_img['image_nr'],
						'image_name' => $dup_products_image_name
					);
					$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_IMAGES, $t_products_images_data_array);
				}
				// EOF GM_MOD

				/* bof gm_prd_img_alt */
				$gm_mo_img_id = xtc_db_insert_id();
				$gm_query = xtc_db_query("
										SELECT
											*
										FROM
											gm_prd_img_alt
										WHERE
											products_id = '" . (int)$src_products_id . "' AND
											image_id    = '" . (int)$mo_img['image_id'] . "'
										");
				if(xtc_db_num_rows($gm_query) > 0)
				{

					while($row = xtc_db_fetch_array($gm_query))
					{
						if(xtc_not_null($row['gm_alt_text']))
						{
							$t_img_alt_data_array = array(
								'language_id' => $row['language_id'],
								'products_id' => $dup_products_id,
								'image_id' => $gm_mo_img_id,
								'gm_alt_text' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $row['gm_alt_text']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))
							);
							$this->wrapped_db_perform(__FUNCTION__, 'gm_prd_img_alt', $t_img_alt_data_array);
						}
					}
				}

				/* eof gm_prd_img_alt */
			}
		}
		//mo_images EOF
		//additional_fields
		$coo_additional_field_control = MainFactory::create_object('AdditionalFieldControl');
		$coo_additional_field_control->copy_field_values((int)$old_products_id, (int)$dup_products_id);

		// START: duplicate feature-set-to-products
		$t_feature_set_query = xtc_db_query("SELECT feature_set_id
											FROM feature_set_to_products
											WHERE products_id = '" . xtc_db_input($src_products_id) . "'");

		if(xtc_db_num_rows($t_feature_set_query) > 0)
		{
			while($t_feature_set = xtc_db_fetch_array($t_feature_set_query))
			{
				$t_feature_set_id = $t_feature_set['feature_set_id'];
				$t_feature_set_to_products = array(
					'feature_set_id' => $t_feature_set_id,
					'products_id' => $dup_products_id
				);
				$this->wrapped_db_perform(__FUNCTION__, 'feature_set_to_products', $t_feature_set_to_products);
			}
		}

		// END: duplicate feature-set-to-products


		$products_id = $dup_products_id;

		$i = 0;
		$group_query = xtc_db_query("SELECT customers_status_id
				    	                               FROM " . TABLE_CUSTOMERS_STATUS . "
				    	                              WHERE language_id = '" . (int)$_SESSION['languages_id'] . "'
				    	                                AND customers_status_id != '0'");

		while($group_values = xtc_db_fetch_array($group_query))
		{
			// load data into array
			$i ++;
			$group_data[$i] = array('STATUS_ID' => $group_values['customers_status_id']);
		}

		for($col = 0, $n = sizeof($group_data); $col < $n + 1; $col ++)
		{
			if($group_data[$col]['STATUS_ID'] != '')
			{

				$copy_query = xtc_db_query("SELECT quantity,
								    			                              personal_offer
								    			                              FROM personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . "
								    			                             WHERE products_id = '" . $old_products_id . "'");

				while($copy_data = xtc_db_fetch_array($copy_query))
				{
					$t_personal_offer_data = array(
						'products_id' => $products_id,
						'quantity' => $copy_data['quantity'],
						'personal_offer' => $copy_data['personal_offer']
					);
					$this->wrapped_db_perform(__FUNCTION__, 'personal_offers_by_customers_status_' . $group_data[$col]['STATUS_ID'], $t_personal_offer_data);
				}
			}
		}

		/* bof duplicate attributes */
		if((int)$_POST['gm_copy_attributes'] == 1)
		{
			// DUPLICATE PROPERTIES COMBIS
			$t_sql = '
				SELECT
					*
				FROM
					products_properties_combis
				WHERE
					products_id = "' . (int)$src_products_id . '"
				ORDER BY
					products_properties_combis_id
			';
			$t_result = xtc_db_query($t_sql);

			while(($t_row = xtc_db_fetch_array($t_result)))
			{
				$t_combi_data_array = array(
					'products_id' => $dup_products_id,
					'sort_order' => $t_row['sort_order'],
					'combi_model' => $t_row['combi_model'],
					'combi_ean' => $t_row['combi_ean'],
					'combi_quantity' => $t_row['combi_quantity'],
					'combi_shipping_status_id' => $t_row['combi_shipping_status_id'],
					'combi_weight' => $t_row['combi_weight'],
					'combi_price_type' => $t_row['combi_price_type'],
					'combi_price' => $t_row['combi_price'],
					'combi_image' => $t_row['combi_image'],
					'products_vpe_id' => $t_row['products_vpe_id'],
					'vpe_value' => $t_row['vpe_value']
				);
				$this->wrapped_db_perform(__FUNCTION__, 'products_properties_combis', $t_combi_data_array);

				$t_new_combi_id = xtc_db_insert_id();

				// COPY PROPERTIES COMBIS IMAGE
				if($t_row['combi_image'] != "")
				{
					$t_new_combi_image = str_replace($t_row['products_properties_combis_id'], $t_new_combi_id, $t_row['combi_image']);
					$t_combi_image_data_array = array('combi_image' => $t_new_combi_image);
					$t_combi_image_where_part = 'products_properties_combis_id = "' . $t_new_combi_id . '"';
					$this->wrapped_db_perform(__FUNCTION__, 'products_properties_combis', $t_combi_image_data_array, 'update', $t_combi_image_where_part);
					@copy(DIR_FS_CATALOG_IMAGES . 'product_images/properties_combis_images/' . $t_row['combi_image'], DIR_FS_CATALOG_IMAGES . 'product_images/properties_combis_images/' . $t_new_combi_image);
				}

				// DUPLICATE PROPERTIES COMBIS VALUES
				$t_sql = '
					SELECT
						*
					FROM
						products_properties_combis_values
					WHERE
						products_properties_combis_id = "' . (int)$t_row['products_properties_combis_id'] . '"
					ORDER BY
						products_properties_combis_values_id
				';
				$t_result_combi_values = xtc_db_query($t_sql);

				while(($t_row_combi_values = xtc_db_fetch_array($t_result_combi_values)))
				{
					$t_combi_values_data_array = array(
						'products_properties_combis_id' => $t_new_combi_id,
						'properties_values_id' => $t_row_combi_values['properties_values_id']
					);
					$this->wrapped_db_perform(__FUNCTION__, 'products_properties_combis_values', $t_combi_values_data_array);
				}
			}

			// REBUILD PROPERTIES INDEX
			$coo_properties_data_agent = MainFactory::create_object('PropertiesDataAgent');
			$coo_properties_data_agent->rebuild_properties_index($dup_products_id);

			// DUPLICATE PROPERTIES ADMIN SELECT
			$t_sql_admin_select = '
				SELECT
					*
				FROM
					products_properties_admin_select
				WHERE
					products_id = "' . (int)$src_products_id . '"
				ORDER BY
					products_properties_admin_select_id
			';
			$t_result_admin_select = xtc_db_query($t_sql_admin_select);

			while(($t_row = xtc_db_fetch_array($t_result_admin_select)))
			{
				$t_properties_admin_select_data_array = array(
					'products_id' => $dup_products_id,
					'properties_id' => $t_row['properties_id'],
					'properties_values_id' => $t_row['properties_values_id']
				);
				$this->wrapped_db_perform(__FUNCTION__, 'products_properties_admin_select', $t_properties_admin_select_data_array);
			}

			// DUPLICATE PROPERTIES COMBIS DEFAULTS
			$t_sql_combis_defaults = '
				SELECT
					*
				FROM
					products_properties_combis_defaults
				WHERE
					products_id = "' . (int)$src_products_id . '"
				ORDER BY
					products_properties_combis_defaults_id
			';
			$t_result_combis_defaults = xtc_db_query($t_sql_combis_defaults);

			while(($t_row = xtc_db_fetch_array($t_result_combis_defaults)))
			{
				$t_combis_defaults_data_array = array(
					'products_id' => $dup_products_id,
					'combi_ean' => $t_row['combi_ean'],
					'combi_quantity' => $t_row['combi_quantity'],
					'combi_shipping_status_id' => $t_row['combi_shipping_status_id'],
					'combi_weight' => $t_row['combi_weight'],
					'combi_price_type' => $t_row['combi_price_type'],
					'combi_price' => $t_row['combi_price'],
					'products_vpe_id' => $t_row['products_vpe_id'],
					'vpe_value' => $t_row['vpe_value']
				);
				$this->wrapped_db_perform(__FUNCTION__, 'products_properties_combis_defaults', $t_combis_defaults_data_array);
			}

			// DUPLICATE ATTRIBUTES
			$attrib_query = xtc_db_query(
					"SELECT
											products_id,
											options_id,
											options_values_id,
											options_values_price,
											price_prefix,
											attributes_model,
											attributes_stock,
											options_values_weight,
											weight_prefix,
											sortorder,
											products_vpe_id,
											gm_vpe_value,
											gm_ean
										FROM " .
					TABLE_PRODUCTS_ATTRIBUTES . "
										WHERE
											products_id = " . $src_products_id
			);

			if(xtc_db_num_rows($attrib_query) > 0)
			{
				while($attrib_res = xtc_db_fetch_array($attrib_query))
				{
					$t_attributes_data_array = array(
						'products_id' => $dup_products_id,
						'options_id' => $attrib_res['options_id'],
						'options_values_id' => $attrib_res['options_values_id'],
						'options_values_price' => $attrib_res['options_values_price'],
						'price_prefix' => $attrib_res['price_prefix'],
						'attributes_model' => $attrib_res['attributes_model'],
						'attributes_stock' => $attrib_res['attributes_stock'],
						'options_values_weight' => $attrib_res['options_values_weight'],
						'weight_prefix' => $attrib_res['weight_prefix'],
						'sortorder' => $attrib_res['sortorder'],
						'products_vpe_id' => $attrib_res['products_vpe_id'],
						'gm_vpe_value' => $attrib_res['gm_vpe_value'],
						'gm_ean' => $attrib_res['gm_ean']
					);
					$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_ATTRIBUTES, $t_attributes_data_array);
				}
			}
		}
		/* eof duplicate attributes */

		/* bof duplicate specials */
		if((int)$_POST['gm_copy_specials'] == 1)
		{
			$specials_query = xtc_db_query(
					"SELECT
											specials_quantity,
											specials_new_products_price,
											specials_date_added,
											specials_last_modified,
											expires_date,
											date_status_change,
											status
										FROM " .
					TABLE_SPECIALS . "
										WHERE
											products_id = " . $src_products_id
					. " LIMIT 1"
			);

			$specials_res = xtc_db_fetch_array($specials_query);

			if(xtc_db_num_rows($specials_query) == 1)
			{
				$t_specials_data_array = array(
					'products_id' => $dup_products_id,
					'specials_quantity' => $specials_res['specials_quantity'],
					'specials_new_products_price' => $specials_res['specials_new_products_price'],
					'specials_date_added' => $specials_res['specials_date_added'],
					'specials_last_modified' => $specials_res['specials_last_modified'],
					'expires_date' => $specials_res['expires_date'],
					'date_status_change' => $specials_res['date_status_change'],
					'status' => $specials_res['status']
				);
				$this->wrapped_db_perform(__FUNCTION__, TABLE_SPECIALS, $t_specials_data_array);
			}
		}
		/* eof duplicate specials */


		/* bof duplicate cross sells */
		if((int)$_POST['gm_copy_cross_sells'] == 1)
		{
			$cross_query = xtc_db_query(
					"SELECT
											products_xsell_grp_name_id,
											xsell_id,
											sort_order
										FROM " .
					TABLE_PRODUCTS_XSELL . "
										WHERE
											products_id = " . $src_products_id
			);

			if(xtc_db_num_rows($cross_query) > 0)
			{
				while($cross_res = xtc_db_fetch_array($cross_query))
				{
					$t_xsell_data_array = array(
						'products_id' => $dup_products_id,
						'products_xsell_grp_name_id' => $cross_res['products_xsell_grp_name_id'],
						'xsell_id' => $cross_res['xsell_id'],
						'sort_order' => $cross_res['sort_order']
					);
					$this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_XSELL, $t_xsell_data_array);
				}
			}
		}
		/* eof duplicate cross sells */

		// BOF GM_MOD
		require_once(DIR_FS_CATALOG . 'gm/classes/GMGMotion.php');
		$coo_gm_gmotion = new GMGMotion();
		$coo_gm_gmotion->copy($src_products_id, $dup_products_id);
		// EOF GM_MOD

		$t_google_category_sql = "SELECT google_category FROM products_google_categories WHERE products_id = '" . (int)$src_products_id . "'";
		$t_google_category_result = xtc_db_query($t_google_category_sql);
		while($t_google_category_result_array = xtc_db_fetch_array($t_google_category_result))
		{
			$t_google_category_data_array = array(
				'google_category' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_google_category_result_array['google_category']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'products_id' => (int)$dup_products_id
			);
			$this->wrapped_db_perform(__FUNCTION__, 'products_google_categories', $t_google_category_data_array);
		}

		$t_item_codes_sql = "SELECT
									google_export_condition,
									google_export_availability_id,
									brand_name,
									code_isbn,
									code_upc,
									code_mpn,
									code_jan,
									identifier_exists,
									gender,
									age_group,
									expiration_date
								FROM products_item_codes
								WHERE products_id = '" . (int)$src_products_id . "'";
		$t_item_codes_result = xtc_db_query($t_item_codes_sql);
		while($t_item_codes_result_array = xtc_db_fetch_array($t_item_codes_result))
		{
			$t_item_codes_where_part = 'products_id = "' . (int)$dup_products_id . '"';
			$this->wrapped_db_perform(__FUNCTION__, 'products_item_codes', array(), 'delete', $t_item_codes_where_part);

			$t_item_codes_data_array = array(
				'google_export_condition' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['google_export_condition']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'google_export_availability_id' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['google_export_availability_id']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'brand_name' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['brand_name']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'code_isbn' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['code_isbn']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'code_upc' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['code_upc']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'code_mpn' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['code_mpn']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'code_jan' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['code_jan']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'identifier_exists' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['identifier_exists']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'gender' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['gender']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'age_group' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['age_group']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'expiration_date' => ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_item_codes_result_array['expiration_date']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")),
				'products_id' => (int)$dup_products_id
			);
			$this->wrapped_db_perform(__FUNCTION__, 'products_item_codes', $t_item_codes_data_array);
		}


		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$coo_seo_boost->repair('products');

		$coo_categories_index = MainFactory::create_object('CategoriesIndex');
		$coo_categories_index->build_categories_index($dup_products_id);

		return $dup_products_id;
	}


	private function duplicate_product_content($srcId, $dupId)
	{
		$t_query = 'SELECT * FROM `products_content` WHERE `products_id` = \'' . $srcId . '\'';
		$t_result_resource = xtc_db_query($t_query);
		$t_result_array = xtc_db_fetch_array($t_result_resource);

		if(is_array($t_result_array) && count($t_result_array) > 0)
		{
			if(array_key_exists('content_id', $t_result_array))
			{
				unset($t_result_array['content_id']);
			}
			$t_result_array['products_id'] = (array_key_exists('products_id', $t_result_array)) ? $dupId : null;
			$t_result = $this->wrapped_db_perform(__FUNCTION__, TABLE_PRODUCTS_CONTENT, $t_result_array);
			if(!$t_result)
			{
				trigger_error('The duplicated article content could not be saved', E_USER_ERROR);
			}
		}
	}


	/**
	 * @param $p_productIdSrc
	 * @param $p_productIdTarget
	 *
	 * @return bool
	 */
	private function duplicate_gxcustumizer_set_assignment($p_productIdSrc, $p_productIdTarget)
	{
		$productIdSrc    = (int)$p_productIdSrc;
		$productIdTarget = (int)$p_productIdTarget;

		$return = false;

		if($productIdSrc > 0 && $productIdTarget > 0)
		{
			$query = 'SELECT * FROM ' . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . " WHERE products_id = '"
			         . $productIdSrc . "'";

			$result = xtc_db_query($query);

			$queryDelete = 'DELETE FROM `' . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . "` WHERE 'products_id' = '"
			               . $productIdTarget . "'";

			xtc_db_query($queryDelete);

			while($data = xtc_db_fetch_array($result))
			{
				$gxsetId = $data['gm_gprint_surfaces_groups_id'];

				$query = 'INSERT INTO `' . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS
				         . "` (`gm_gprint_surfaces_groups_id`, `products_id`) VALUES (" . "'$gxsetId', '$productIdTarget')";

				$return = xtc_db_query($query);
			}
		}

		return $return;
	}

	//duplicate_product ends
	// ----------------------------------------------------------------------------------------------------- //
	// links a product into specified category by id

	function link_product($src_products_id, $dest_categories_id)
	{
		global $messageStack;
		$check_query = xtc_db_query("SELECT COUNT(*) AS total
				                                     FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
				                                     WHERE products_id   = '" . xtc_db_input($src_products_id) . "'
				                                     AND   categories_id = '" . xtc_db_input($dest_categories_id) . "'");
		$check = xtc_db_fetch_array($check_query);

		if($check['total'] < '1')
		{
			// BOF GM_MOD sometimes entry already exists - for whatever reason
			$t_gm_check = xtc_db_query("SELECT products_id
													FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
													WHERE
														products_id = '" . (int)$src_products_id . "' AND
														categories_id = '" . (int)$dest_categories_id . "'");
			if(xtc_db_num_rows($t_gm_check) == 0 && $src_products_id > 0)
			{
				xtc_db_query("INSERT INTO " . TABLE_PRODUCTS_TO_CATEGORIES . "
						                          SET products_id   = '" . xtc_db_input($src_products_id) . "',
						                          categories_id = '" . xtc_db_input($dest_categories_id) . "'");
			}
			// EOF GM_MOD
			if($dest_categories_id == 0)
			{
				$this->set_product_startpage($src_products_id, 1);
			}
		}
		else
		{
			// BOF GM_MOD:
			if($dest_categories_id != 0)
				$messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
		}

		$coo_categories_index = MainFactory::create_object('CategoriesIndex');
		$coo_categories_index->build_categories_index($src_products_id);
	}

// link_product ends
	// ----------------------------------------------------------------------------------------------------- //
	// moves a product from category into specified category

	function move_product($src_products_id, $src_category_id, $dest_category_id)
	{
		$duplicate_check_query = xtc_db_query("SELECT COUNT(*) AS total
				    	                                         FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
				    	                                        WHERE products_id   = '" . xtc_db_input($src_products_id) . "'
				    	                                          AND categories_id = '" . xtc_db_input($dest_category_id) . "'");
		$duplicate_check = xtc_db_fetch_array($duplicate_check_query);

		if($duplicate_check['total'] == 0)
		{
			xtc_db_query("UPDATE " . TABLE_PRODUCTS_TO_CATEGORIES . "
								SET categories_id = '" . xtc_db_input($dest_category_id) . "'
							WHERE
								products_id   = '" . xtc_db_input($src_products_id) . "' AND
								categories_id = '" . xtc_db_input($src_category_id) . "'");

			if($dest_category_id == 0)
			{
				$this->set_product_startpage($src_products_id, 1);
			}

			if($src_category_id == 0)
			{
				$this->set_product_startpage($src_products_id, 0);
			}
		}

		$coo_categories_index = MainFactory::create_object('CategoriesIndex');
		$coo_categories_index->build_categories_index($src_products_id);
	}

	// ----------------------------------------------------------------------------------------------------- //
	// Sets the status of a product
	function set_product_status($products_id, $status)
	{
		if($status == '1')
		{
			return xtc_db_query("update " . TABLE_PRODUCTS . " set products_status = '1', products_last_modified = now() where products_id = '" . $products_id . "'");
		}
		elseif($status == '0')
		{
			return xtc_db_query("update " . TABLE_PRODUCTS . " set products_status = '0', products_last_modified = now() where products_id = '" . $products_id . "'");
		}
		else
		{
			return -1;
		}
	}

	// ----------------------------------------------------------------------------------------------------- //
	// Sets a product active on startpage
	function set_product_startpage($products_id, $status)
	{
		if($status == '1')
		{
			return xtc_db_query("update " . TABLE_PRODUCTS . " set products_startpage = '1', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
		}
		elseif($status == '0')
		{
			return xtc_db_query("update " . TABLE_PRODUCTS . " set products_startpage = '0', products_last_modified = now() where products_id = '" . (int)$products_id . "'");
		}
		else
		{
			return -1;
		}
	}

	// ----------------------------------------------------------------------------------------------------- //
	// Counts how many products exist in a category
	function count_category_products($category_id, $include_deactivated = false)
	{
		$products_count = 0;
		if($include_deactivated)
		{
			$products_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . $category_id . "'");
		}
		else
		{
			$products_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . $category_id . "'");
		}

		$products = xtc_db_fetch_array($products_query);

		$products_count += $products['total'];

		$childs_query = xtc_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $category_id . "'");
		if(xtc_db_num_rows($childs_query))
		{
			while($childs = xtc_db_fetch_array($childs_query))
			{
				$products_count += $this->count_category_products($childs['categories_id'], $include_deactivated);
			}
		}
		return $products_count;
	}

	// ----------------------------------------------------------------------------------------------------- //
	// Counts how many subcategories exist in a category
	function count_category_childs($category_id)
	{
		$categories_count = 0;
		$categories_query = xtc_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $category_id . "'");
		while($categories = xtc_db_fetch_array($categories_query))
		{
			$categories_count ++;
			$categories_count += $this->count_category_childs($categories['categories_id']);
		}
		return $categories_count;
	}

	function edit_cross_sell($cross_data)
	{

		if($cross_data['special'] == 'add_entries')
		{

			if(isset($cross_data['ids']))
			{
				foreach($cross_data['ids'] AS $pID)
				{

					$sql_data_array = array('products_id' => $cross_data['current_product_id'], 'xsell_id' => $pID, 'products_xsell_grp_name_id' => $cross_data['group_name'][$pID]);

					// check if product is already linked
					$check_query = xtc_db_query("SELECT * FROM " . TABLE_PRODUCTS_XSELL . " WHERE products_id='" . $cross_data['current_product_id'] . "' and xsell_id='" . $pID . "'");
					if(!xtc_db_num_rows($check_query))
						xtc_db_perform(TABLE_PRODUCTS_XSELL, $sql_data_array);
				}
			}
		}
		if($cross_data['special'] == 'edit')
		{

			if(isset($cross_data['ids']))
			{
				// delete
				foreach($cross_data['ids'] AS $pID)
				{
					xtc_db_query("DELETE FROM " . TABLE_PRODUCTS_XSELL . " WHERE ID='" . $pID . "'");
				}
			}
			if(isset($cross_data['sort']))
			{
				// edit sorting
				foreach($cross_data['sort'] AS $ID => $sort)
				{
					xtc_db_query("UPDATE " . TABLE_PRODUCTS_XSELL . " SET sort_order='" . $sort . "',products_xsell_grp_name_id='" . $cross_data['group_name'][$ID] . "' WHERE ID='" . $ID . "'");
				}
			}
		}
	}

	// save 'feature setup' for product_id
	function save_feature_values($p_products_id)
	{
		if(!isset($_POST['featureValue']) || !is_array($_POST['featureValue']))
		{
			return false;
		}
		$feat_value_array = array();
		$prod_id = (int)$p_products_id;
		$coo_control = MainFactory::create_object('ProductFeatureHandler');
		$coo_control->clear_feature_value($prod_id);
		foreach($_POST['featureValue'] as $feat_id => $value_array)
		{
			foreach($value_array as $key => $value_id)
			{
				$coo_control->add_feature_value($value_id, $prod_id);
			}
		}
		$coo_control->build_feature_index($prod_id);
		$coo_control = NULL;
		return true;
	}

	// save/delete 'category slider" for cat_id
	function save_cat_slider($p_categories_id)
	{
		$cat_slider_id = (int)$_POST['cat_slider'];
		$cat_id = (int)$p_categories_id;
		$coo_cat_slider_handler = MainFactory::create_object('CategorySliderHandler');
		$coo_cat_slider_handler->remove_category_slider($cat_id);
		if(!empty($cat_slider_id))
		{
			$coo_cat_slider_handler->set_category_slider($cat_id, $cat_slider_id);
		}
		return true;
	}

	// save/delete 'quantity unit" for product_id
	function save_quantity_unit($p_products_id)
	{
		$unit_id = (int)$_POST['quantityunit'];
		$prod_id = (int)$p_products_id;
		$coo_unit_handler = MainFactory::create_object('ProductQuantityUnitHandler');
		$coo_unit_handler->remove_quantity_unit($prod_id);
		if(!empty($unit_id))
		{
			$coo_unit_handler->set_quantity_unit($prod_id, $unit_id);
		}
		return true;
	}

	function wrapped_db_perform($p_called_from, $p_table, $p_data_array = array(), $p_action = 'insert', $p_parameters = '', $p_link = 'db_link', $p_quoted_values = true)
	{
		return xtc_db_perform($p_table, $p_data_array, $p_action, $p_parameters, $p_link, $p_quoted_values);
	}

	// ----------------------------------------------------------------------------------------------------- //
}

MainFactory::load_origin_class('categories');
