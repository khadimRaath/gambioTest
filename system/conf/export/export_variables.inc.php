<?php
/* --------------------------------------------------------------
   export_variables.inc.php 2014-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
$query  = xtc_db_query('SELECT MAX(`image_nr`) AS `amount` FROM `products_images`');
$result = xtc_db_fetch_array($query);
$moPics = $result['amount'];

$this->add_variables(array(
	
	/* --- Multilingual --- */
	
	array(
		'name' => 'products_name',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_name',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_description',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_description',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_short_description',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_short_description',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_keywords',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'checkout_information',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_meta_title',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_meta_description',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_meta_keywords',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_url',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_gm_alt_text',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_gm_alt_text#',
		'has_lang' => true,
		'start_index' => 1,
		'max_index' => $moPics,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'gm_url_keywords',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_shipping_status_name',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'c_name',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'c_path',
		'has_lang' => true,
		'price_comparison_forbidden' => true
	),
	
	
	/* --- Monolingual --- */
	
	array(
		'name' => 'p_id'
	),
	
	array(
		'name' => 'products_name',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_name',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_type',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_description',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_description',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_short_description',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_short_description',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_quantity'
	),
	
	array(
		'name' => 'gm_show_qty_info',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_startpage',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'gm_show_price_offer',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_sort',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_startpage_sort',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_keywords',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_meta_title',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_meta_description',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_meta_keywords',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_url',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'gender'
	),
	
	array(
		'name' => 'age_group'
	),
	
	array(
		'name' => 'p_gm_alt_text',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_gm_alt_text#',
		'start_index' => 1,
		'max_index' => $moPics,
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'gm_url_keywords',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_shippingtime',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_shipping_status_name',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'nc_ultra_shipping_costs',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'c_name',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'c_path',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_ean'
	),
	
	array(
		'name' => 'code_isbn'
	),
	
	array(
		'name' => 'code_upc'
	),
	
	array(
		'name' => 'code_mpn'
	),
	
	array(
		'name' => 'code_jan'
	),
	
	array(
		'name' => 'brand_name'
	),
	
	array(
		'name' => 'p_expiration_date'
	),
	
	array(
		'name' => 'manufacturers_id',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_manufacturer_name'
	),
	
	array(
		'name' => 'p_link'
	),
	
	array(
		'name' => 'products_image',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_image#',
		'start_index' => 1,
		'max_index' => $moPics,
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_image#',
		'start_index' => 1,
		'max_index' => $moPics,
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_thumb_image',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_info_image',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_popup_image',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_thumb_images',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_info_images',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_popup_images',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'products_price',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_price_point'
	),
	
	array(
		'name' => 'p_price_comma'
	),
	
	array(
		'name' => 'p_price_net_point',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_price_net_comma',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_old_price_point'
	),
	
	array(
		'name' => 'p_old_price_comma'
	),
	
	array(
		'name' => 'p_baseprice_point'
	),
	
	array(
		'name' => 'p_baseprice_comma'
	),
	
	array(
		'name' => 'products_discount_allowed'
	),
	
	array(
		'name' => 'gm_price_status',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'gm_min_order'
	),
	
	array(
		'name' => 'gm_graduated_qty'
	),
	
	array(
		'name' => 'products_tax_class_id',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_shipping_costs_point'
	),
	
	array(
		'name' => 'p_shipping_costs_comma'
	),

	array(
		'name' => 'p_shipping_costs_point_with_shipping_free_minimum'
	),

	array(
		'name' => 'p_shipping_costs_comma_with_shipping_free_minimum'
	),
	
	array(
		'name' => 'p_weight_point'
	),
	
	array(
		'name' => 'p_weight_comma'
	),
	
	array(
		'name' => 'products_weight',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'gm_show_weight',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'gm_sitemap_entry',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'gm_priority',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'gm_changefreq',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_model'
	),
	
	array(
		'name' => 'products_model'
	),
	
	array(
		'name' => 'p_currency'
	),
	
	array(
		'name' => 'products_ean'
	),
	
	array(
		'name' => 'product_template',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'options_template',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'gm_options_template',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'quantity_unit_id',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_fsk18'
	),
	
	array(
		'name' => 'products_vpe_status',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_vpe_value',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'vpe_name'
	),
	
	array(
		'name' => 'products_status',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_date_added',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_last_modified',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_date_available'
	),
	
	array(
		'name' => 'p_availability'
	),
	
	array(
		'name' => 'gm_show_date_added',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'products_ordered',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'p_quantity'
	),
	
	array(
		'name' => 'p_quantity_floor'
	),
	
	array(
		'name' => 'p_attribute',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'specials_quantity',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'specials_new_products_price',
		'price_comparison_forbidden' => true
	),
	
	array(
		'name' => 'expires_date',
		'price_comparison_forbidden' => true
	),
	
	
	/* --- Google-Shopping-Variablen --- */
	
	array(
		'name' => 'p_google_name_vpe_prefix',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_google_name_vpe_suffix',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_google_category'
	),
	
	array(
		'name' => 'p_google_export_condition'
	),
	
	array(
		'name' => 'p_google_export_availability'
	),
	
	array(
		'name' => 'p_google_shipping_costs',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_google_export_gtin',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_google_identifier_exists',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_google_fsk18',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_google_unit_price_measure',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_google_unit_pricing_base_measure',
		'product_export_forbidden' => true
	),
	
	array(
		'name' => 'p_google_product_group',
		'product_export_forbidden' => true
	)
	
));
