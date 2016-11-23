<?php
/* --------------------------------------------------------------
   CSVImportFunctionLibrary.inc.php 2015-04-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CSVImportFunctionLibrary extends BaseClass
{
	protected $v_import_mapping_array = array();
	protected $v_import_data_array = array();
	protected $v_headline_array = array();
	protected $v_language_array = array();
	protected $v_google_export_availability_array = array();
	protected $v_csv_source;
	protected $v_properties;
	protected $v_additional_fields;
	protected $v_properties_by_name_array = array();
	protected $v_property_values_by_name_array = array();
		
	public function CSVImportFunctionLibrary(&$p_headline_array)
	{
		$this->set_headline_array($p_headline_array);
		$this->init_import();
	}
	
	public function clean_data_array()
	{
		$this->v_import_data_array = array();
	}
	
	protected function set_headline_array(&$p_headline_array)
	{
		$this->v_headline_array = $p_headline_array;
	}
	
	public function get_field_name($p_index)
	{
		$t_field_name = '';
		
		if(isset($this->v_headline_array[$p_index]))
		{
			$t_field_name = $this->v_headline_array[$p_index];
		}
		
		return $t_field_name;
	}
	
	public function get_import_data_array()
	{
		return $this->v_import_data_array;
	}
	
	public function init_import()
	{
		$this->build_language_array();
		$this->build_import_mapping_array();
	}
	
	protected function build_language_array()
	{
		$t_sql = '
			SELECT languages_id, code
			FROM languages';
		$t_result = xtc_db_query($t_sql);
		
		while ($t_row = xtc_db_fetch_array($t_result))
		{
			$this->v_language_array[$t_row['code']] = $t_row['languages_id'];
		}
		$this->v_language_array[''] = 0;
	}
	
	public function get_language_array()
	{
		return $this->v_language_array;
	}
	
	public function get_categories_array()
	{
		return $this->v_import_data_array['categories'];
	}
	
	protected function build_google_export_availability_array()
	{
		$t_sql = '
			SELECT *
			FROM google_export_availability';
		$t_result = xtc_db_query($t_sql);
		
		while ($t_row = xtc_db_fetch_array($t_result))
		{
			$this->v_google_export_availability_array[$t_row['availability']] = $t_row['google_export_availability_id'];
		}
	}
	
	protected function build_import_mapping_array()
	{
		$this->v_import_mapping_array['p_id'] = array();
		$this->v_import_mapping_array['p_id']['table'] = 'products';
		$this->v_import_mapping_array['p_id']['column'] = 'products_id';
		$this->v_import_mapping_array['p_id']['function'] = '';
		
		$this->v_import_mapping_array['p_model'] = array();
		$this->v_import_mapping_array['p_model']['table'] = 'products';
		$this->v_import_mapping_array['p_model']['column'] = 'products_model';
		$this->v_import_mapping_array['p_model']['function'] = 'products_id_by_model';
		
		$this->v_import_mapping_array['p_stock'] = array();
		$this->v_import_mapping_array['p_stock']['table'] = 'products';
		$this->v_import_mapping_array['p_stock']['column'] = 'products_quantity';
		$this->v_import_mapping_array['p_stock']['function'] = '';
		
		$this->v_import_mapping_array['p_sorting'] = array();
		$this->v_import_mapping_array['p_sorting']['table'] = 'products';
		$this->v_import_mapping_array['p_sorting']['column'] = 'products_sort';
		$this->v_import_mapping_array['p_sorting']['function'] = '';
		
		$this->v_import_mapping_array['p_startpage'] = array();
		$this->v_import_mapping_array['p_startpage']['table'] = 'products';
		$this->v_import_mapping_array['p_startpage']['column'] = 'products_startpage';
		$this->v_import_mapping_array['p_startpage']['function'] = '';
		
		$this->v_import_mapping_array['p_startpage_sort'] = array();
		$this->v_import_mapping_array['p_startpage_sort']['table'] = 'products';
		$this->v_import_mapping_array['p_startpage_sort']['column'] = 'products_startpage_sort';
		$this->v_import_mapping_array['p_startpage_sort']['function'] = '';
		
		$this->v_import_mapping_array['p_shipping'] = array();
		$this->v_import_mapping_array['p_shipping']['table'] = 'products';
		$this->v_import_mapping_array['p_shipping']['column'] = 'products_shippingtime';
		$this->v_import_mapping_array['p_shipping']['function'] = '';
		
		$this->v_import_mapping_array['p_tpl'] = array();
		$this->v_import_mapping_array['p_tpl']['table'] = 'products';
		$this->v_import_mapping_array['p_tpl']['column'] = 'product_template';
		$this->v_import_mapping_array['p_tpl']['function'] = '';
		
		$this->v_import_mapping_array['p_opttpl'] = array();
		$this->v_import_mapping_array['p_opttpl']['table'] = 'products';
		$this->v_import_mapping_array['p_opttpl']['column'] = 'options_template';
		$this->v_import_mapping_array['p_opttpl']['function'] = '';
		
		$this->v_import_mapping_array['p_manufacturer'] = array();
		$this->v_import_mapping_array['p_manufacturer']['table'] = 'products';
		$this->v_import_mapping_array['p_manufacturer']['column'] = 'manufacturers_id';
		$this->v_import_mapping_array['p_manufacturer']['function'] = '';
		
		$this->v_import_mapping_array['p_fsk18'] = array();
		$this->v_import_mapping_array['p_fsk18']['table'] = 'products';
		$this->v_import_mapping_array['p_fsk18']['column'] = 'products_fsk18';
		$this->v_import_mapping_array['p_fsk18']['function'] = '';
		
		$this->v_import_mapping_array['p_priceNoTax'] = array();
		$this->v_import_mapping_array['p_priceNoTax']['table'] = 'products';
		$this->v_import_mapping_array['p_priceNoTax']['column'] = 'products_price';
		$this->v_import_mapping_array['p_priceNoTax']['function'] = 'personal_offer';
		
		$this->v_import_mapping_array['quantity'] = array();
		$this->v_import_mapping_array['quantity']['table'] = 'personal_offers_by_customer_status_';
		$this->v_import_mapping_array['quantity']['column'] = 'quantity';
		$this->v_import_mapping_array['quantity']['function'] = '';
		
		$this->v_import_mapping_array['p_tax'] = array();
		$this->v_import_mapping_array['p_tax']['table'] = 'products';
		$this->v_import_mapping_array['p_tax']['column'] = 'products_tax_class_id';
		$this->v_import_mapping_array['p_tax']['function'] = '';
		
		$this->v_import_mapping_array['p_status'] = array();
		$this->v_import_mapping_array['p_status']['table'] = 'products';
		$this->v_import_mapping_array['p_status']['column'] = 'products_status';
		$this->v_import_mapping_array['p_status']['function'] = '';
		
		$this->v_import_mapping_array['p_weight'] = array();
		$this->v_import_mapping_array['p_weight']['table'] = 'products';
		$this->v_import_mapping_array['p_weight']['column'] = 'products_weight';
		$this->v_import_mapping_array['p_weight']['function'] = '';
		
		$this->v_import_mapping_array['p_ean'] = array();
		$this->v_import_mapping_array['p_ean']['table'] = 'products';
		$this->v_import_mapping_array['p_ean']['column'] = 'products_ean';
		$this->v_import_mapping_array['p_ean']['function'] = '';

        $this->v_import_mapping_array['p_type'] = array();
        $this->v_import_mapping_array['p_type']['table'] = 'products';
        $this->v_import_mapping_array['p_type']['column'] = 'product_type';
        $this->v_import_mapping_array['p_type']['function'] = '';

		$this->v_import_mapping_array['code_isbn'] = array();
		$this->v_import_mapping_array['code_isbn']['table'] = 'products_item_codes';
		$this->v_import_mapping_array['code_isbn']['column'] = 'code_isbn';
		$this->v_import_mapping_array['code_isbn']['function'] = '';
		
		$this->v_import_mapping_array['code_upc'] = array();
		$this->v_import_mapping_array['code_upc']['table'] = 'products_item_codes';
		$this->v_import_mapping_array['code_upc']['column'] = 'code_upc';
		$this->v_import_mapping_array['code_upc']['function'] = '';
		
		$this->v_import_mapping_array['code_mpn'] = array();
		$this->v_import_mapping_array['code_mpn']['table'] = 'products_item_codes';
		$this->v_import_mapping_array['code_mpn']['column'] = 'code_mpn';
		$this->v_import_mapping_array['code_mpn']['function'] = '';
		
		$this->v_import_mapping_array['code_jan'] = array();
		$this->v_import_mapping_array['code_jan']['table'] = 'products_item_codes';
		$this->v_import_mapping_array['code_jan']['column'] = 'code_jan';
		$this->v_import_mapping_array['code_jan']['function'] = '';
		
		$this->v_import_mapping_array['brand_name'] = array();
		$this->v_import_mapping_array['brand_name']['table'] = 'products_item_codes';
		$this->v_import_mapping_array['brand_name']['column'] = 'brand_name';
		$this->v_import_mapping_array['brand_name']['function'] = '';
		
		$this->v_import_mapping_array['p_disc'] = array();
		$this->v_import_mapping_array['p_disc']['table'] = 'products';
		$this->v_import_mapping_array['p_disc']['column'] = 'products_discount_allowed';
		$this->v_import_mapping_array['p_disc']['function'] = '';
		
		$this->v_import_mapping_array['p_date_added'] = array();
		$this->v_import_mapping_array['p_date_added']['table'] = 'products';
		$this->v_import_mapping_array['p_date_added']['column'] = 'products_date_added';
		$this->v_import_mapping_array['p_date_added']['function'] = '';
		
		$this->v_import_mapping_array['p_last_modified'] = array();
		$this->v_import_mapping_array['p_last_modified']['table'] = 'products';
		$this->v_import_mapping_array['p_last_modified']['column'] = 'products_last_modified';
		$this->v_import_mapping_array['p_last_modified']['function'] = '';
		
		$this->v_import_mapping_array['p_date_available'] = array();
		$this->v_import_mapping_array['p_date_available']['table'] = 'products';
		$this->v_import_mapping_array['p_date_available']['column'] = 'products_date_available';
		$this->v_import_mapping_array['p_date_available']['function'] = '';
		
		$this->v_import_mapping_array['p_ordered'] = array();
		$this->v_import_mapping_array['p_ordered']['table'] = 'products';
		$this->v_import_mapping_array['p_ordered']['column'] = 'products_ordered';
		$this->v_import_mapping_array['p_ordered']['function'] = '';
		
		$this->v_import_mapping_array['nc_ultra_shipping_costs'] = array();
		$this->v_import_mapping_array['nc_ultra_shipping_costs']['table'] = 'products';
		$this->v_import_mapping_array['nc_ultra_shipping_costs']['column'] = 'nc_ultra_shipping_costs';
		$this->v_import_mapping_array['nc_ultra_shipping_costs']['function'] = '';
		
		$this->v_import_mapping_array['gm_show_date_added'] = array();
		$this->v_import_mapping_array['gm_show_date_added']['table'] = 'products';
		$this->v_import_mapping_array['gm_show_date_added']['column'] = 'gm_show_date_added';
		$this->v_import_mapping_array['gm_show_date_added']['function'] = '';
		
		$this->v_import_mapping_array['gm_show_price_offer'] = array();
		$this->v_import_mapping_array['gm_show_price_offer']['table'] = 'products';
		$this->v_import_mapping_array['gm_show_price_offer']['column'] = 'gm_show_price_offer';
		$this->v_import_mapping_array['gm_show_price_offer']['function'] = '';
		
		$this->v_import_mapping_array['gm_show_weight'] = array();
		$this->v_import_mapping_array['gm_show_weight']['table'] = 'products';
		$this->v_import_mapping_array['gm_show_weight']['column'] = 'gm_show_weight';
		$this->v_import_mapping_array['gm_show_weight']['function'] = '';
		
		$this->v_import_mapping_array['gm_show_qty_info'] = array();
		$this->v_import_mapping_array['gm_show_qty_info']['table'] = 'products';
		$this->v_import_mapping_array['gm_show_qty_info']['column'] = 'gm_show_qty_info';
		$this->v_import_mapping_array['gm_show_qty_info']['function'] = '';
		
		$this->v_import_mapping_array['gm_price_status'] = array();
		$this->v_import_mapping_array['gm_price_status']['table'] = 'products';
		$this->v_import_mapping_array['gm_price_status']['column'] = 'gm_price_status';
		$this->v_import_mapping_array['gm_price_status']['function'] = '';
		
		$this->v_import_mapping_array['gm_min_order'] = array();
		$this->v_import_mapping_array['gm_min_order']['table'] = 'products';
		$this->v_import_mapping_array['gm_min_order']['column'] = 'gm_min_order';
		$this->v_import_mapping_array['gm_min_order']['function'] = '';
		
		$this->v_import_mapping_array['gm_graduated_qty'] = array();
		$this->v_import_mapping_array['gm_graduated_qty']['table'] = 'products';
		$this->v_import_mapping_array['gm_graduated_qty']['column'] = 'gm_graduated_qty';
		$this->v_import_mapping_array['gm_graduated_qty']['function'] = '';
		
		$this->v_import_mapping_array['gm_options_template'] = array();
		$this->v_import_mapping_array['gm_options_template']['table'] = 'products';
		$this->v_import_mapping_array['gm_options_template']['column'] = 'gm_options_template';
		$this->v_import_mapping_array['gm_options_template']['function'] = '';
		
		$this->v_import_mapping_array['p_vpe'] = array();
		$this->v_import_mapping_array['p_vpe']['table'] = 'products';
		$this->v_import_mapping_array['p_vpe']['column'] = 'products_vpe';
		$this->v_import_mapping_array['p_vpe']['function'] = '';
		
		$this->v_import_mapping_array['p_vpe_status'] = array();
		$this->v_import_mapping_array['p_vpe_status']['table'] = 'products';
		$this->v_import_mapping_array['p_vpe_status']['column'] = 'products_vpe_status';
		$this->v_import_mapping_array['p_vpe_status']['function'] = '';
		
		$this->v_import_mapping_array['p_vpe_value'] = array();
		$this->v_import_mapping_array['p_vpe_value']['table'] = 'products';
		$this->v_import_mapping_array['p_vpe_value']['column'] = 'products_vpe_value';
		$this->v_import_mapping_array['p_vpe_value']['function'] = '';
		
		$this->v_import_mapping_array['p_image'] = array();
		$this->v_import_mapping_array['p_image']['table'] = 'products';
		$this->v_import_mapping_array['p_image']['column'] = 'products_image';
		$this->v_import_mapping_array['p_image']['function'] = 'additional_image';
		
		$this->v_import_mapping_array['image_nr'] = array();
		$this->v_import_mapping_array['image_nr']['table'] = 'products_images';
		$this->v_import_mapping_array['image_nr']['column'] = 'image_nr';
		$this->v_import_mapping_array['image_nr']['function'] = '';
		
		$this->v_import_mapping_array['p_name'] = array();
		$this->v_import_mapping_array['p_name']['table'] = 'products_description';
		$this->v_import_mapping_array['p_name']['column'] = 'products_name';
		$this->v_import_mapping_array['p_name']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_desc'] = array();
		$this->v_import_mapping_array['p_desc']['table'] = 'products_description';
		$this->v_import_mapping_array['p_desc']['column'] = 'products_description';
		$this->v_import_mapping_array['p_desc']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_shortdesc'] = array();
		$this->v_import_mapping_array['p_shortdesc']['table'] = 'products_description';
		$this->v_import_mapping_array['p_shortdesc']['column'] = 'products_short_description';
		$this->v_import_mapping_array['p_shortdesc']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_meta_title'] = array();
		$this->v_import_mapping_array['p_meta_title']['table'] = 'products_description';
		$this->v_import_mapping_array['p_meta_title']['column'] = 'products_meta_title';
		$this->v_import_mapping_array['p_meta_title']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_meta_desc'] = array();
		$this->v_import_mapping_array['p_meta_desc']['table'] = 'products_description';
		$this->v_import_mapping_array['p_meta_desc']['column'] = 'products_meta_description';
		$this->v_import_mapping_array['p_meta_desc']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_meta_key'] = array();
		$this->v_import_mapping_array['p_meta_key']['table'] = 'products_description';
		$this->v_import_mapping_array['p_meta_key']['column'] = 'products_meta_keywords';
		$this->v_import_mapping_array['p_meta_key']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_keywords'] = array();
		$this->v_import_mapping_array['p_keywords']['table'] = 'products_description';
		$this->v_import_mapping_array['p_keywords']['column'] = 'products_keywords';
		$this->v_import_mapping_array['p_keywords']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_url'] = array();
		$this->v_import_mapping_array['p_url']['table'] = 'products_description';
		$this->v_import_mapping_array['p_url']['column'] = 'products_url';
		$this->v_import_mapping_array['p_url']['function'] = 'by_language';
		
		$this->v_import_mapping_array['gm_url_keywords'] = array();
		$this->v_import_mapping_array['gm_url_keywords']['table'] = 'products_description';
		$this->v_import_mapping_array['gm_url_keywords']['column'] = 'gm_url_keywords';
		$this->v_import_mapping_array['gm_url_keywords']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_checkout_information'] = array();
		$this->v_import_mapping_array['p_checkout_information']['table'] = 'products_description';
		$this->v_import_mapping_array['p_checkout_information']['column'] = 'checkout_information';
		$this->v_import_mapping_array['p_checkout_information']['function'] = 'by_language';
		
		$this->v_import_mapping_array['p_cat'] = array();
		$this->v_import_mapping_array['p_cat']['table'] = 'products_to_categories';
		$this->v_import_mapping_array['p_cat']['column'] = 'categories_id';
		$this->v_import_mapping_array['p_cat']['function'] = 'resolve_c_path';
		
		$this->v_import_mapping_array['google_export_availability'] = array();
		$this->v_import_mapping_array['google_export_availability']['table'] = 'products_item_codes';
		$this->v_import_mapping_array['google_export_availability']['column'] = 'google_export_availability_id';
		$this->v_import_mapping_array['google_export_availability']['function'] = 'convert_google_export_availability';
		
		$this->v_import_mapping_array['google_export_condition'] = array();
		$this->v_import_mapping_array['google_export_condition']['table'] = 'products_item_codes';
		$this->v_import_mapping_array['google_export_condition']['column'] = 'google_export_condition';
		$this->v_import_mapping_array['google_export_condition']['function'] = '';
		
		$this->v_import_mapping_array['google_category'] = array();
		$this->v_import_mapping_array['google_category']['table'] = 'products_google_categories';
		$this->v_import_mapping_array['google_category']['column'] = 'google_category';
		$this->v_import_mapping_array['google_category']['function'] = '';
		
		$this->v_import_mapping_array['p_img_alt_text'] = array();
		$this->v_import_mapping_array['p_img_alt_text']['table'] = 'products_description';
		$this->v_import_mapping_array['p_img_alt_text']['column'] = 'gm_alt_text';
		$this->v_import_mapping_array['p_img_alt_text']['function'] = 'by_language,additional_image_alt_text';
		
		$this->v_import_mapping_array['p_group_permission'] = array();
		$this->v_import_mapping_array['p_group_permission']['table'] = 'products';
		$this->v_import_mapping_array['p_group_permission']['column'] = 'group_permission_0';
		$this->v_import_mapping_array['p_group_permission']['function'] = 'group_permission';
		
		$this->v_import_mapping_array['specials_qty'] = array();
		$this->v_import_mapping_array['specials_qty']['table'] = 'specials';
		$this->v_import_mapping_array['specials_qty']['column'] = 'specials_quantity';
		$this->v_import_mapping_array['specials_qty']['function'] = '';
		
		$this->v_import_mapping_array['specials_new_products_price'] = array();
		$this->v_import_mapping_array['specials_new_products_price']['table'] = 'specials';
		$this->v_import_mapping_array['specials_new_products_price']['column'] = 'specials_new_products_price';
		$this->v_import_mapping_array['specials_new_products_price']['function'] = '';
		
		$this->v_import_mapping_array['expires_date'] = array();
		$this->v_import_mapping_array['expires_date']['table'] = 'specials';
		$this->v_import_mapping_array['expires_date']['column'] = 'expires_date';
		$this->v_import_mapping_array['expires_date']['function'] = '';
		
		$this->v_import_mapping_array['specials_status'] = array();
		$this->v_import_mapping_array['specials_status']['table'] = 'specials';
		$this->v_import_mapping_array['specials_status']['column'] = 'status';
		$this->v_import_mapping_array['specials_status']['function'] = '';
		
		$this->v_import_mapping_array['gm_priority'] = array();
		$this->v_import_mapping_array['gm_priority']['table'] = 'products';
		$this->v_import_mapping_array['gm_priority']['column'] = 'gm_priority';
		$this->v_import_mapping_array['gm_priority']['function'] = '';
		
		$this->v_import_mapping_array['gm_changefreq'] = array();
		$this->v_import_mapping_array['gm_changefreq']['table'] = 'products';
		$this->v_import_mapping_array['gm_changefreq']['column'] = 'gm_changefreq';
		$this->v_import_mapping_array['gm_changefreq']['function'] = '';
		
		$this->v_import_mapping_array['gm_sitemap_entry'] = array();
		$this->v_import_mapping_array['gm_sitemap_entry']['table'] = 'products';
		$this->v_import_mapping_array['gm_sitemap_entry']['column'] = 'gm_sitemap_entry';
		$this->v_import_mapping_array['gm_sitemap_entry']['function'] = '';
		
		$this->v_import_mapping_array['p_qty_unit_id'] = array();
		$this->v_import_mapping_array['p_qty_unit_id']['table'] = 'products_quantity_unit';
		$this->v_import_mapping_array['p_qty_unit_id']['column'] = 'quantity_unit_id';
		$this->v_import_mapping_array['p_qty_unit_id']['function'] = '';
		
		$this->v_import_mapping_array['categories_id'] = array();
		$this->v_import_mapping_array['categories_id']['table'] = 'categories';
		$this->v_import_mapping_array['categories_id']['column'] = 'categories_id';
		$this->v_import_mapping_array['categories_id']['function'] = 'additional_level';
		
		$this->v_import_mapping_array['parent_id'] = array();
		$this->v_import_mapping_array['parent_id']['table'] = 'categories';
		$this->v_import_mapping_array['parent_id']['column'] = 'parent_id';
		$this->v_import_mapping_array['parent_id']['function'] = 'additional_level';
		
		$this->v_import_mapping_array['categories_status'] = array();
		$this->v_import_mapping_array['categories_status']['table'] = 'categories';
		$this->v_import_mapping_array['categories_status']['column'] = 'categories_status';
		$this->v_import_mapping_array['categories_status']['function'] = 'additional_level';
		
		$this->v_import_mapping_array['date_added'] = array();
		$this->v_import_mapping_array['date_added']['table'] = 'categories';
		$this->v_import_mapping_array['date_added']['column'] = 'date_added';
		$this->v_import_mapping_array['date_added']['function'] = 'additional_level';
		
		$this->v_import_mapping_array['last_modified'] = array();
		$this->v_import_mapping_array['last_modified']['table'] = 'categories';
		$this->v_import_mapping_array['last_modified']['column'] = 'last_modified';
		$this->v_import_mapping_array['last_modified']['function'] = 'additional_level';
		
		$this->v_import_mapping_array['categories_name'] = array();
		$this->v_import_mapping_array['categories_name']['table'] = 'categories_description';
		$this->v_import_mapping_array['categories_name']['column'] = 'categories_name';
		$this->v_import_mapping_array['categories_name']['function'] = 'by_language,additional_level';
		
		$this->v_import_mapping_array['categories_description_id'] = array();
		$this->v_import_mapping_array['categories_description_id']['table'] = 'categories_description';
		$this->v_import_mapping_array['categories_description_id']['column'] = 'categories_id';
		$this->v_import_mapping_array['categories_description_id']['function'] = 'by_language,additional_level';
		
		$this->v_import_mapping_array['products_properties_combis_id'] = array();
		$this->v_import_mapping_array['products_properties_combis_id']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['products_properties_combis_id']['column'] = 'products_properties_combis_id';
		$this->v_import_mapping_array['products_properties_combis_id']['function'] = '';
		
		$this->v_import_mapping_array['combi_sort_order'] = array();
		$this->v_import_mapping_array['combi_sort_order']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_sort_order']['column'] = 'sort_order';
		$this->v_import_mapping_array['combi_sort_order']['function'] = '';
		
		$this->v_import_mapping_array['combi_model'] = array();
		$this->v_import_mapping_array['combi_model']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_model']['column'] = 'combi_model';
		$this->v_import_mapping_array['combi_model']['function'] = '';
		
		$this->v_import_mapping_array['combi_ean'] = array();
		$this->v_import_mapping_array['combi_ean']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_ean']['column'] = 'combi_ean';
		$this->v_import_mapping_array['combi_ean']['function'] = '';
		
		$this->v_import_mapping_array['combi_quantity'] = array();
		$this->v_import_mapping_array['combi_quantity']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_quantity']['column'] = 'combi_quantity';
		$this->v_import_mapping_array['combi_quantity']['function'] = '';
		
		$this->v_import_mapping_array['combi_shipping_status_id'] = array();
		$this->v_import_mapping_array['combi_shipping_status_id']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_shipping_status_id']['column'] = 'combi_shipping_status_id';
		$this->v_import_mapping_array['combi_shipping_status_id']['function'] = '';
		
		$this->v_import_mapping_array['combi_weight'] = array();
		$this->v_import_mapping_array['combi_weight']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_weight']['column'] = 'combi_weight';
		$this->v_import_mapping_array['combi_weight']['function'] = '';
		
		$this->v_import_mapping_array['combi_price_type'] = array();
		$this->v_import_mapping_array['combi_price_type']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_price_type']['column'] = 'combi_price_type';
		$this->v_import_mapping_array['combi_price_type']['function'] = '';
		
		$this->v_import_mapping_array['combi_price'] = array();
		$this->v_import_mapping_array['combi_price']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_price']['column'] = 'combi_price';
		$this->v_import_mapping_array['combi_price']['function'] = '';
		
		$this->v_import_mapping_array['combi_image'] = array();
		$this->v_import_mapping_array['combi_image']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_image']['column'] = 'combi_image';
		$this->v_import_mapping_array['combi_image']['function'] = '';
		
		$this->v_import_mapping_array['combi_vpe_id'] = array();
		$this->v_import_mapping_array['combi_vpe_id']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_vpe_id']['column'] = 'products_vpe_id';
		$this->v_import_mapping_array['combi_vpe_id']['function'] = '';
		
		$this->v_import_mapping_array['combi_vpe_value'] = array();
		$this->v_import_mapping_array['combi_vpe_value']['table'] = 'products_properties_combis';
		$this->v_import_mapping_array['combi_vpe_value']['column'] = 'vpe_value';
		$this->v_import_mapping_array['combi_vpe_value']['function'] = '';
		
		$this->v_import_mapping_array['property'] = array();
		$this->v_import_mapping_array['property']['table'] = '';
		$this->v_import_mapping_array['property']['column'] = '';
		$this->v_import_mapping_array['property']['function'] = 'property';
		
		$this->v_import_mapping_array['properties_values-properties_values_id'] = array();
		$this->v_import_mapping_array['properties_values-properties_values_id']['table'] = 'properties_values';
		$this->v_import_mapping_array['properties_values-properties_values_id']['column'] = 'properties_values_id';
		$this->v_import_mapping_array['properties_values-properties_values_id']['function'] = 'by_property';
		
		$this->v_import_mapping_array['properties_values-properties_id'] = array();
		$this->v_import_mapping_array['properties_values-properties_id']['table'] = 'properties_values';
		$this->v_import_mapping_array['properties_values-properties_id']['column'] = 'properties_id';
		$this->v_import_mapping_array['properties_values-properties_id']['function'] = 'by_property';
		
		$this->v_import_mapping_array['properties_values_description-properties_values_id'] = array();
		$this->v_import_mapping_array['properties_values_description-properties_values_id']['table'] = 'properties_values_description';
		$this->v_import_mapping_array['properties_values_description-properties_values_id']['column'] = 'properties_values_id';
		$this->v_import_mapping_array['properties_values_description-properties_values_id']['function'] = 'by_property';
		
		$this->v_import_mapping_array['properties_values_description-language_id'] = array();
		$this->v_import_mapping_array['properties_values_description-language_id']['table'] = 'properties_values_description';
		$this->v_import_mapping_array['properties_values_description-language_id']['column'] = 'language_id';
		$this->v_import_mapping_array['properties_values_description-language_id']['function'] = 'by_property';
		
		$this->v_import_mapping_array['properties_values_description-values_name'] = array();
		$this->v_import_mapping_array['properties_values_description-values_name']['table'] = 'properties_values_description';
		$this->v_import_mapping_array['properties_values_description-values_name']['column'] = 'values_name';
		$this->v_import_mapping_array['properties_values_description-values_name']['function'] = 'by_property';
		
		$this->v_import_mapping_array['products_properties_combis_values-properties_values_id'] = array();
		$this->v_import_mapping_array['products_properties_combis_values-properties_values_id']['table'] = 'products_properties_combis_values';
		$this->v_import_mapping_array['products_properties_combis_values-properties_values_id']['column'] = 'properties_values_id';
		$this->v_import_mapping_array['products_properties_combis_values-properties_values_id']['function'] = 'by_property';
		
		$this->v_import_mapping_array['products_properties_admin_select-properties_id'] = array();
		$this->v_import_mapping_array['products_properties_admin_select-properties_id']['table'] = 'products_properties_admin_select';
		$this->v_import_mapping_array['products_properties_admin_select-properties_id']['column'] = 'properties_id';
		$this->v_import_mapping_array['products_properties_admin_select-properties_id']['function'] = 'by_property';
		
		$this->v_import_mapping_array['products_properties_admin_select-properties_values_id'] = array();
		$this->v_import_mapping_array['products_properties_admin_select-properties_values_id']['table'] = 'products_properties_admin_select';
		$this->v_import_mapping_array['products_properties_admin_select-properties_values_id']['column'] = 'properties_values_id';
		$this->v_import_mapping_array['products_properties_admin_select-properties_values_id']['function'] = 'by_property';
		
		$this->v_import_mapping_array['additional_field'] = array();
		$this->v_import_mapping_array['additional_field']['table'] = '';
		$this->v_import_mapping_array['additional_field']['column'] = '';
		$this->v_import_mapping_array['additional_field']['function'] = 'additional_field';
		
		$this->v_import_mapping_array['additional_field_values-additional_field_value_id'] = array();
		$this->v_import_mapping_array['additional_field_values-additional_field_value_id']['table'] = 'additional_field_values';
		$this->v_import_mapping_array['additional_field_values-additional_field_value_id']['column'] = 'additional_field_value_id';
		$this->v_import_mapping_array['additional_field_values-additional_field_value_id']['function'] = 'additional_level';
		
		$this->v_import_mapping_array['additional_field_values-additional_field_id'] = array();
		$this->v_import_mapping_array['additional_field_values-additional_field_id']['table'] = 'additional_field_values';
		$this->v_import_mapping_array['additional_field_values-additional_field_id']['column'] = 'additional_field_id';
		$this->v_import_mapping_array['additional_field_values-additional_field_id']['function'] = 'additional_level';
		
		$this->v_import_mapping_array['additional_field_value_descriptions-additional_field_value_id'] = array();
		$this->v_import_mapping_array['additional_field_value_descriptions-additional_field_value_id']['table'] = 'additional_field_value_descriptions';
		$this->v_import_mapping_array['additional_field_value_descriptions-additional_field_value_id']['column'] = 'additional_field_value_id';
		$this->v_import_mapping_array['additional_field_value_descriptions-additional_field_value_id']['function'] = 'by_language,additional_level';
		
		$this->v_import_mapping_array['additional_field_value_descriptions-language_id'] = array();
		$this->v_import_mapping_array['additional_field_value_descriptions-language_id']['table'] = 'additional_field_value_descriptions';
		$this->v_import_mapping_array['additional_field_value_descriptions-language_id']['column'] = 'language_id';
		$this->v_import_mapping_array['additional_field_value_descriptions-language_id']['function'] = 'by_language,additional_level';
		
		$this->v_import_mapping_array['additional_field_value_descriptions-value'] = array();
		$this->v_import_mapping_array['additional_field_value_descriptions-value']['table'] = 'additional_field_value_descriptions';
		$this->v_import_mapping_array['additional_field_value_descriptions-value']['column'] = 'value';
		$this->v_import_mapping_array['additional_field_value_descriptions-value']['function'] = 'by_language,additional_level';
	}
	
	
	public function set_field_content($p_field_name, $p_field_value)
	{
		$c_field_name = trim((strpos_wrapper($p_field_name, '.') !== false) ? substr_wrapper($p_field_name, 0, strpos_wrapper($p_field_name, '.')) : $p_field_name);
		
		if (strpos_wrapper($c_field_name, 'Eigenschaft:') === 0)
		{
			$c_field_name = 'property';
		}
		
		if (strpos_wrapper($c_field_name, 'Zusatzfeld:') === 0)
		{
			$c_field_name = 'additional_field';
		}
		
		if (array_key_exists($c_field_name, $this->v_import_mapping_array))
		{
			$t_field_data = array();
			$t_field_data['table'] = $this->v_import_mapping_array[$c_field_name]['table'];
			$t_field_data['column'] = $this->v_import_mapping_array[$c_field_name]['column'];
			$t_field_data['value'] = $p_field_value;
			if ($c_field_name == 'property')
			{
				$t_field_data['property'] = $p_field_name;
			}
			if ($c_field_name == 'additional_field')
			{
				$t_field_data['additional_field'] = $p_field_name;
			}
			
			if (!empty($this->v_import_mapping_array[$c_field_name]['function']))
			{
				$t_functions = explode(',', $this->v_import_mapping_array[$c_field_name]['function']);
				$t_clean_field_name = substr_wrapper($p_field_name, 0, strpos_wrapper($p_field_name, '[') !== false ? strpos_wrapper($p_field_name, '[') : strlen_wrapper($p_field_name));
				$t_params = explode('.', $t_clean_field_name);
				$t_params[0] = &$t_field_data;
				
				foreach ($t_functions as $t_function)
				{
					eval('$this->' . trim($t_function) . '($t_params);');
				}
			}
			
			$this->add_import_field($t_field_data);
		}
	}
	
	protected function add_import_field($p_field_data)
	{
		if (empty($p_field_data['table']))
		{
			return;
		}
		
		if (!array_key_exists($p_field_data['table'], $this->v_import_data_array))
		{
			$this->v_import_data_array[$p_field_data['table']] = array();
		}
		
		$this->build_field_data_recursively($this->v_import_data_array[$p_field_data['table']], $p_field_data['value'], $p_field_data['column']);
	}
	
	protected function build_field_data_recursively(&$p_data_array, $p_field_data, $p_column_name)
	{
		if (!is_array($p_data_array))
		{
			$p_data_array = array();
		}
		
		if (is_array($p_field_data))
		{
			foreach ($p_field_data as $t_key => $t_val)
			{
				$this->build_field_data_recursively($p_data_array[$t_key], $t_val, $p_column_name);
			}
		}
		else
		{
			$p_data_array[$p_column_name] = trim($p_field_data);
		}
	}
	
	protected function products_id_by_model(&$p_field_params)
	{
		if(!empty($this->v_import_data_array['products']['products_id']) || trim($p_field_params[0]['value']) === '')
		{
			return;
		}
		
		$t_sql = "SELECT products_id 
					FROM " . TABLE_PRODUCTS . " 
					WHERE products_model = '" . xtc_db_input($p_field_params[0]['value']) . "' 
					LIMIT 1";
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$this->v_import_data_array['products']['products_id'] = $t_result_array['products_id'];
		}
	}
	
	protected function by_language(&$p_field_params, $p_param_position = false)
	{
		if($p_param_position === false)
		{
			$t_index = count($p_field_params) - 1;
		}
		else
		{
			$t_index = $p_param_position;
		}
		
		$t_lang_code = '';
		if(isset($this->v_language_array[$p_field_params[$t_index]]))
		{
			$t_lang_code = $p_field_params[$t_index];
		}
		$t_lang_id = $this->v_language_array[$t_lang_code];
		$t_temp = $p_field_params[0]['value'];
		$p_field_params[0]['value'] = array();
		$p_field_params[0]['value'][$t_lang_id] = $t_temp;
	}
	
	protected function by_property(&$p_field_params)
	{
		$t_temp = $p_field_params[0]['value'];
		$p_field_params[0]['value'] = array();
		$p_field_params[0]['value'][$p_field_params[1]] = $t_temp;
	}
	
	protected function personal_offer(&$p_field_params)
	{
		if (!isset($p_field_params[1]) || empty($p_field_params[1]))
		{
			return;
		}
		$c_customer_group = (int) $p_field_params[1];
		$p_field_params[0]['table'] = 'personal_offers_by_customers_status_' . $c_customer_group;
		$p_field_params[0]['column'] = 'personal_offer';
		$t_offers_array = explode('::', $p_field_params[0]['value']);		
		$p_field_params[0]['value'] = array();
		
		foreach ($t_offers_array as $t_offer)
		{
			$t_quantity_offers = explode(':', $t_offer);
			if (count($t_quantity_offers) == 1)
			{
				$t_quantity = '1';
				$t_offer_price = $t_offer;
			}
			else
			{
				$t_quantity = $t_quantity_offers[0];
				$t_offer_price = $t_quantity_offers[1];
			}
			$p_field_params[0]['value'][$t_quantity] = $t_offer_price;
		}
	}
	
	protected function group_permission(&$p_field_params)
	{
		$c_customer_group = (int) $p_field_params[1];
		$p_field_params[0]['column'] = 'group_permission_' . $c_customer_group;
	}
	
	protected function additional_image(&$p_field_params)
	{
		if (!isset($p_field_params[1]) || empty($p_field_params[1]))
		{
			return;
		}
		
		$c_image_index = (int) $p_field_params[1];
		$p_field_params[0]['table'] = 'products_images';
		$p_field_params[0]['column'] = 'image_name';
		$t_temp = $p_field_params[0]['value'];
		$p_field_params[0]['value'] = array();
		$p_field_params[0]['value'][$c_image_index] = $t_temp;
	}
	
	protected function additional_image_alt_text(&$p_field_params)
	{
		if(isset($p_field_params[1]) && !is_numeric($p_field_params[1]))
		{
			return;
		}
		elseif (!isset($p_field_params[1]) || empty($p_field_params[1]))
		{
			$p_field_params[0]['table'] = false;
			return;
		}
		
		$c_image_nr = (int) $p_field_params[1];
		$p_field_params[0]['table'] = 'gm_prd_img_alt';
		$t_temp = $p_field_params[0]['value'];
		$p_field_params[0]['value'] = array();
		$p_field_params[0]['value'][$c_image_nr] = $t_temp;
	}
	
	protected function additional_level(&$p_field_params, $p_param_position = 1)
	{
		if (!isset($p_field_params[$p_param_position]) || empty($p_field_params[$p_param_position]))
		{
			return;
		}
		$c_index = (int) $p_field_params[$p_param_position];
		$t_temp = $p_field_params[0]['value'];
		$p_field_params[0]['value'] = array();
		$p_field_params[0]['value'][$c_index] = $t_temp;
	}
	
	protected function resolve_c_path(&$p_field_params)
	{
		if (!isset($this->v_language_array[$p_field_params[1]]) || empty($this->v_language_array[$p_field_params[1]]))
		{
			$p_field_params[0]['table'] = false;
			return;
		}
		if (!isset($p_field_params[0]['table']) || empty($p_field_params[0]['table']) || empty($p_field_params[0]['value']))
		{
			return;
		}
		if (!array_key_exists($p_field_params[0]['table'], $this->v_import_data_array))
		{
			$this->v_import_data_array[$p_field_params[0]['table']] = array();
		}
		
//		//TODO: Optimierung (c_path nur für eine Sprache auflösen)
//		if (array_key_exists($p_field_params[0]['column'], $this->v_import_data_array[$p_field_params[0]['table']]))
//		{
//			$this->ignore_column($p_field_params);
//			return;
//		}
		
		$t_category_array = explode('>', $p_field_params[0]['value']);

		$i = 0;
		$t_sql = "";
		$t_sql_select = "SELECT";
		$t_sql_from = " FROM";
		$t_sql_where = "";
		$t_sql_limit = " LIMIT 1";
		
		for ($i = 1; $i <= count($t_category_array); $i++)
		{
			if (strpos_wrapper($t_category_array[$i - 1], '[') !== false && strpos_wrapper($t_category_array[$i - 1], ']') !== false)
			{
				$t_categories_id = substr_wrapper($t_category_array[$i - 1], strpos_wrapper($t_category_array[$i - 1], '[') + 1);
				$t_categories_id = trim(substr_wrapper($t_categories_id, 0, strpos_wrapper($t_categories_id, ']')));
				$t_identification_sql = "c.categories_id = " . $t_categories_id;
			}
			else
			{
				$t_identification_sql = "cd.categories_name LIKE '" . addslashes(trim($t_category_array[$i - 1])) . "'";
			}
			
			$t_sql_subselect = "SELECT
								c.categories_id,
								c.parent_id,
								cd.categories_name
							FROM
								categories c,
								categories_description cd
							WHERE
								c.categories_id = cd.categories_id
							AND
								" . $t_identification_sql . "
							AND
								cd.language_id = " . $this->v_language_array[$p_field_params[1]];
			
			if ($i > 1)
			{
				$t_sql_from .= " LEFT JOIN (" . $t_sql_subselect . ") as level" . $i . " ON (level" . $i . ".parent_id = level" . ($i - 1) . ".categories_id)";
				$t_sql_select .= ($i > 1 ? "," : "") . "
									level" . $i . ".categories_id as level" . $i . "_categories_id,
									level" . $i . ".categories_name as level" . $i . "_categories_name";
			}
			else
			{
				$t_sql_from .= " (" . $t_sql_subselect . ") as level" . $i;
				$t_sql_select .= "
									level" . $i . ".categories_id as level" . $i . "_categories_id,
									level" . $i . ".categories_name as level" . $i . "_categories_name";
				$t_sql_where .= " WHERE level" . $i . ".parent_id = 0";
			}
		}
		$t_sql = $t_sql_select . $t_sql_from . $t_sql_where . $t_sql_limit;
		
		$t_result = xtc_db_query($t_sql, 'db_link', false);
		$t_category_path = array();
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_category_path = xtc_db_fetch_array($t_result);
		}
		
		for ($i = 1; $i <= count($t_category_array); $i++)
		{
			$t_category_id = substr_wrapper($t_category_array[$i - 1], strpos_wrapper($t_category_array[$i - 1], '[') + 1);
			$t_category_id = substr_wrapper($t_category_id, 0, strpos_wrapper($t_category_id, ']'));

			if (strpos_wrapper($t_category_array[$i - 1], '[') !== false)
			{
				$t_category_array[$i - 1] = substr_wrapper($t_category_array[$i - 1], 0, strpos_wrapper($t_category_array[$i - 1], '['));
			}

			if (empty($t_category_id))
			{
				if (isset($t_category_path['level' . $i . '_categories_id']))
				{
					$t_category_id = $t_category_path['level' . $i . '_categories_id'];
				}
				else
				{
					$t_category_id = '';
				}
			}
			
			$this->set_field_content('categories_id.' . $i, $t_category_id);
			
			if((!empty($t_category_id) && $i > 1) || empty($t_category_id))
			{
				$this->set_field_content('parent_id.' . $i, (isset($t_category_path['level' . ($i - 1) . '_categories_id'])) ? $t_category_path['level' . ($i - 1) . '_categories_id'] : 0);
			}
			else
			{
				$this->set_field_content('parent_id.1', 0);
			}
			
			$this->set_field_content('categories_status.' . $i, 1);
			$this->set_field_content('date_added.' . $i, 'now()');
			$this->set_field_content('last_modified.' . $i, 'now()');
			$this->set_field_content('categories_name.' . $i . '.' . $p_field_params[1], $t_category_array[$i - 1]);
			$this->set_field_content('categories_description_id.' . $i . '.' . $p_field_params[1], $t_category_path['level' . $i . '_categories_id']);
		}
		$p_field_params[0]['value'] = $t_category_id;
	}
	
	protected function convert_google_export_availability(&$p_field_params)
	{
		if (empty($this->v_google_export_availability_array))
		{
			$this->build_google_export_availability_array();
		}
		if (!empty($p_field_params[0]['value']))
		{
			$p_field_params[0]['value'] = $this->v_google_export_availability_array[$p_field_params[0]['value']];
		}
	}
	
	protected function property(&$p_field_params)
	{
		if (empty($this->v_import_data_array['products']['products_id']) || empty($p_field_params[0]['value']))
		{
			$this->ignore_column($p_field_params);
			return;
		}
		
		if (empty($this->v_properties))
		{
			$this->v_csv_source = MainFactory::create_object('CSVSource', array(), true);
			$this->v_properties = $this->v_csv_source->get_properties_array();
		}
		
		$c_property_id = $this->get_property_id($p_field_params[0]['property']);
		
		if (empty($c_property_id))
		{
			$this->ignore_column($p_field_params);
			return;
		}
		
		if (empty($this->v_language_array))
		{
			$this->build_language_array();
		}
		
		$c_property_value_name = $this->get_property_value_name($p_field_params[0]['value']);
		$c_property_value_id = (int) $this->get_property_value_id($c_property_id, $p_field_params[0]['value'], $p_field_params[1]);
		
		$this->set_field_content('properties_values-properties_values_id.' . $c_property_id, $c_property_value_id);
		$this->set_field_content('properties_values-properties_id.' . $c_property_id, $c_property_id);
		
		$this->set_field_content('properties_values_description-properties_values_id.' . $c_property_id, $c_property_value_id);
		$this->set_field_content('properties_values_description-language_id.' . $c_property_id, $this->v_language_array[trim($p_field_params[1])]);
		$this->set_field_content('properties_values_description-values_name.' . $c_property_id, $c_property_value_name);
		
		$this->set_field_content('products_properties_combis_values-properties_values_id.' . $c_property_id, $c_property_value_id);
		
		$this->set_field_content('products_properties_admin_select-properties_id.' . $c_property_id, $c_property_id);
		$this->set_field_content('products_properties_admin_select-properties_values_id.' . $c_property_id, $c_property_value_id);
	}
	
	
	protected function get_property_id($p_property)
	{
		if (strpos_wrapper($p_property, '[') !== false && strpos_wrapper($p_property, ']') !== false)
		{
			$p_property = substr_wrapper($p_property, strpos_wrapper($p_property, '[') + 1);
			$p_property = trim(substr_wrapper($p_property, 0, strpos_wrapper($p_property, ']')));
			$c_property_id = (int) $p_property;
		}
		else if (isset($this->v_properties_by_name_array[$p_property]) && !empty($this->v_properties_by_name_array[$p_property]))
		{
			$c_property_id = $this->v_properties_by_name_array[$p_property];
		}
		else if (isset($this->v_properties_by_name_array[$p_property]) && empty($this->v_properties_by_name_array[$p_property]))
		{
			return 0;
		}
		else
		{
			$t_sql = "	SELECT
							properties_id
						FROM
							properties_description
						WHERE
							properties_name LIKE '" . $p_property . "'";
			$t_result = xtc_db_query($t_sql);

			if (xtc_db_num_rows($t_result) != 1)
			{
				$this->v_properties_by_name_array[$p_property] = 0;
				return 0;
			}

			$t_row = xtc_db_fetch_array($t_result);
			$c_property_id = (int) $t_row['properties_id'];
			$this->v_properties_by_name_array[$p_property] = $c_property_id;
		}

		return $c_property_id;
	}
	
	protected function get_property_value_id($p_property_id, $p_property_value, $p_language_code = 'de')
	{
		$t_language_code = trim($p_language_code);
		if (strpos_wrapper($p_property_value, '[') !== false && strpos_wrapper($p_property_value, ']') !== false)
		{
			$p_property_value = substr_wrapper($p_property_value, strpos_wrapper($p_property_value, '[') + 1);
			$p_property_value = trim(substr_wrapper($p_property_value, 0, strpos_wrapper($p_property_value, ']')));
			$c_property_value_id = (int) $p_property_value;
		}
		else if (isset($this->v_property_values_by_name_array[$p_property_id][$t_language_code][$p_property_value]) && !empty($this->v_property_values_by_name_array[$p_property_id][$t_language_code][$p_property_value]))
		{
			$c_property_value_id = $this->v_property_values_by_name_array[$p_property_id][$t_language_code][$p_property_value];
		}
		else if (isset($this->v_property_values_by_name_array[$p_property_id][$t_language_code][$p_property_value]) && empty($this->v_property_values_by_name_array[$p_property_id][$t_language_code][$p_property_value]))
		{
			return 0;
		}
		else
		{
			$c_property_value_name = $this->get_property_value_name($p_property_value);
			if (empty($this->v_language_array))
			{
				$this->build_language_array();
			}
			
			$t_sql = "	SELECT
							pvd.properties_values_id,
							pvd.language_id
						FROM
							properties_values pv,
							properties_values_description pvd
						WHERE
							pv.properties_id = " . $p_property_id . "
						AND
							pv.properties_values_id = pvd.properties_values_id
						AND
							pvd.values_name LIKE '" . $c_property_value_name . "'
						AND
							pvd.language_id = " . $this->v_language_array[$t_language_code];
			$t_result = xtc_db_query($t_sql);

			if (xtc_db_num_rows($t_result) != 1)
			{
				$this->v_property_values_by_name_array[$p_property_id][$t_language_code][$c_property_value_name] = 0;
				return 0;
			}

			$t_row = xtc_db_fetch_array($t_result);
			$c_property_value_id = (int) $t_row['properties_values_id'];
			$this->v_property_values_by_name_array[$p_property_id][$t_language_code][$c_property_value_name] = $c_property_value_id;
		}

		return $c_property_value_id;
	}
	
	protected function get_additional_field_id($p_additional_field_headline)
	{
		$c_additional_field_id = 0;
		
		if (strpos_wrapper($p_additional_field_headline, '[') !== false && strpos_wrapper($p_additional_field_headline, ']') !== false)
		{
			$p_additional_field_headline = substr_wrapper($p_additional_field_headline, strpos_wrapper($p_additional_field_headline, '[') + 1);
			$p_additional_field_headline = trim(substr_wrapper($p_additional_field_headline, 0, strpos_wrapper($p_additional_field_headline, ']')));
			$c_additional_field_id = (int) $p_additional_field_headline;
		}
		
		return $c_additional_field_id;
	}
	
	
	protected function get_additional_field_value_id($p_additional_field_id, $p_item_id, $p_language_id = 0)
	{
		$t_additional_field_value_id = 0;
		if(isset($this->v_additional_fields[$p_additional_field_id][$p_item_id][$p_language_id]['additional_field_value_id']))
		{
			$t_additional_field_value_id = $this->v_additional_fields[$p_additional_field_id][$p_item_id][$p_language_id]['additional_field_value_id'];
		}

		return $t_additional_field_value_id;
	}
	
	protected function get_property_value_name($p_property_value)
	{
		return trim(substr_wrapper($p_property_value, 0, strpos_wrapper($p_property_value, '[') ? strpos_wrapper($p_property_value, '[') : strlen_wrapper($p_property_value)));
	}
	
	protected function ignore_column(&$p_field_params)
	{
		$p_field_params[0]['table'] = '';
	}
	
	protected function additional_field(&$p_field_params)
	{
		if (empty($this->v_import_data_array['products']['products_id']) || empty($p_field_params[0]['value']))
		{
			$this->ignore_column($p_field_params);
			return;
		}
		
		if (empty($this->v_additional_fields))
		{
			$this->v_csv_source = MainFactory::create_object('CSVSource', array(), true);
			$this->v_additional_fields = $this->v_csv_source->get_additional_fields_array();
		}
		
		$c_additional_field_id = $this->get_additional_field_id($p_field_params[0]['additional_field']);
		
		if (empty($c_additional_field_id))
		{
			$this->ignore_column($p_field_params);
			return;
		}
		
		if (empty($this->v_language_array))
		{
			$this->build_language_array();
		}
		
		$t_language_id = 0;
		$t_language_code = '';
		if(isset($p_field_params[1]))
		{
			$t_language_id = $this->v_language_array[trim($p_field_params[1])];
			$t_language_code = '.' . trim($p_field_params[1]);
		}
			
		$c_additional_field_value_id = $this->get_additional_field_value_id($c_additional_field_id, $this->v_import_data_array['products']['products_id'], $t_language_id);
		
		$this->set_field_content('additional_field_values-additional_field_value_id.' . $c_additional_field_id, $c_additional_field_value_id);
		$this->set_field_content('additional_field_values-additional_field_id.' . $c_additional_field_id, $c_additional_field_id);

		$this->set_field_content('additional_field_value_descriptions-additional_field_value_id.' . $c_additional_field_id . $t_language_code, $c_additional_field_value_id);
		$this->set_field_content('additional_field_value_descriptions-language_id.' . $c_additional_field_id . $t_language_code, $t_language_id);
		$this->set_field_content('additional_field_value_descriptions-value.' . $c_additional_field_id . $t_language_code, $p_field_params[0]['value']);
	}
	
	protected function by_additional_field_and_language(&$p_field_params)
	{
		
	}
}
