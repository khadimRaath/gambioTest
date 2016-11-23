<?php
/* --------------------------------------------------------------
   CSVFunctionLibrary.inc.php 2016-08-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'admin/includes/gm/inc/no_html.inc.php');
require_once(DIR_FS_INC . 'xtc_get_tax_rate.inc.php');

/**
 * Description of CSVFunctionLibrary
 */
class CSVFunctionLibrary extends BaseClass
{	
	protected $coo_currencies;
	protected $coo_shipping_status;
	protected $coo_vpe_source;
	protected $coo_manufacturers;
	protected $coo_google_export_availability;
	protected $coo_seo_boost = false;
			
	protected $v_language_array = array();
	protected $v_language_by_code_array = array();
	protected $v_product_ids_array = array();
	protected $v_image_data_array = array();
	protected $v_additional_field_data_array = array();
	protected $v_personal_offers_array = array();
	protected $v_product_categories_array = array();
	protected $v_category_parents_array = array();
	protected $v_category_names_array = array();
	protected $v_google_categories_array = array();
	protected $v_properties_array = array();
	protected $v_attributes_array = array();
	protected $v_additional_fields_array = array();
	protected $coo_xtc_price;
	protected $coo_scheme;
	protected $combi_value_names_array = array();
	protected $property_names_array = array();
	
	public function __construct( $p_coo_scheme, $p_properties_array = array(), $p_attributes_array = array(), $p_additional_fields_array = array() )
	{
		$this->coo_scheme = $p_coo_scheme;
		$this->coo_currencies = MainFactory::create_object( 'CurrenciesSource' );
		$this->coo_shipping_status = MainFactory::create_object( 'ProductsShippingStatusSource' );
		$this->coo_vpe_source = MainFactory::create_object('ProductsVPESource');
		$this->coo_manufacturers = MainFactory::create_object( 'ManufacturersSource' );
		$this->coo_google_export_availability = MainFactory::create_object( 'GoogleExportAvailabilitySource' );
		$this->v_properties_array = $p_properties_array;
		$this->v_attributes_array = $p_attributes_array;
		$this->v_additional_fields_array = $p_additional_fields_array;
		$this->_init_languages_by_code();
		$this->_init_property_names();
		
		if( gm_get_conf('GM_SEO_BOOST_PRODUCTS') == 'true' )
		{
			$this->coo_seo_boost = new GMSEOBoost();
		}
		
		$t_currency = $this->coo_currencies->get_currencies( $this->coo_scheme->v_data_array[ 'currencies_id' ] );
		$this->coo_xtc_price = new xtcPrice( $t_currency[ 'code' ], $this->coo_scheme->v_data_array[ 'customers_status_id' ] );
	}
	
	public function get_data( $p_data_array, $p_field_content, $p_default_value = '', $p_preview_content = false )
	{
		$t_field_content = $this->_get_field_content($p_data_array, $p_field_content, $p_default_value);
		
		if($p_preview_content === true)
		{
			$t_field_content = htmlentities_wrapper( $t_field_content );
			if(strlen_wrapper($t_field_content) > 30)
			{
				$t_field_content = '<div class="preview_content_full" style="display: none">' . $t_field_content . ' <a href="#" style="color: red" class="toogle_content_size">[weniger]</a></div></div>' . 
								'<div class="preview_content_sub">' . substr_wrapper($t_field_content, 0, 30) . ' <a href="#" style="color: red" class="toogle_content_size">[mehr]</a></div>';
			}
		}
		
		return $t_field_content;
	}
	
	protected function _get_field_content($p_data_array, $p_field_content, $p_default_field_content = '')
	{
		preg_match_all( '/{([^{]+)}/', $p_field_content, $t_matches );
		
		foreach( $t_matches[1] AS $t_variable_name_data )
		{
			$t_language_code = '';
			$t_index = '';
			$t_variable_name = $t_variable_name_data;
			$t_variable_key_name = $t_variable_name_data;
			
			if(strpos_wrapper(trim($t_variable_name), 'collective_field') === 0)
			{
				$t_collective_variable = explode('||', $t_variable_name);
				$t_collective_source_names = explode(';', $t_collective_variable[1]);
				$t_collective_sources = explode(';', $t_collective_variable[2]);
				
				$t_variable_name_formatted = $this->collective_field($p_data_array, $t_collective_source_names, $t_collective_sources);
			}
			else
			{
				$t_filter_name = '';
				if(strpos_wrapper($t_variable_name, '|') !== false)
				{
					$t_variable_array = explode('|', $t_variable_name);
					$t_variable_name = $t_variable_array[0];
					$t_variable_key_name = $t_variable_array[0];
					$t_filter_data_array = explode(':', $t_variable_array[1]);
					$t_filter_name = $t_filter_data_array[0];
					$filter_params_array = array();
					if(isset($t_filter_data_array[1]))
					{
						foreach($t_filter_data_array as $t_filter_key => $t_filter_value)
						{
							if($t_filter_key > 0)
							{
								$filter_params_array[] = $t_filter_value;
							}
						}
					}
				}

				if(strpos_wrapper($t_variable_name, '.') !== false)
				{
					$t_variable_array = explode('.', $t_variable_name);

					$t_variable_name = $t_variable_array[0];
					$t_language_code = $t_variable_array[1];
				}

				if(strpos_wrapper($t_variable_name, '#') !== false)
				{
					$t_variable_array = explode('#', $t_variable_name);
					$t_variable_name = $t_variable_array[0];
					$t_index = $t_variable_array[1];
				}

				if( method_exists( $this, $t_variable_name ) )
				{
					$t_language_id = empty($t_language_code) ? $this->coo_scheme->v_data_array['languages_id'] : $this->_get_language_id_by_code($t_language_code);
					$this->_set_language_id($t_language_id);
					$this->_set_language_code($t_language_code);

					if($t_index === '')
					{
						$t_variable_name_formatted = call_user_func( array( $this, $t_variable_name ), $p_data_array );
					}
					else
					{
						$t_variable_name_formatted = call_user_func( array( $this, $t_variable_name ), $p_data_array, $t_index );
					}

					if($t_filter_name != '' && method_exists($this, $t_filter_name))
					{
						$t_variable_name_formatted = call_user_func( array( $this, $t_filter_name ), $t_variable_name_formatted, $filter_params_array );
					}
				}
				else if (array_key_exists($t_variable_key_name, $p_data_array))
				{
					$t_variable_name_formatted = $p_data_array[ $t_variable_key_name ];

					if($t_filter_name != '' && method_exists($this, $t_filter_name))
					{
						$t_variable_name_formatted = call_user_func( array( $this, $t_filter_name ), $t_variable_name_formatted, $filter_params_array );
					}
				}
				else
				{
					$t_variable_name_formatted = '{' . $t_variable_name_data . '}';
				}
			}
			$p_field_content = str_replace( '{' . $t_variable_name_data . '}', $t_variable_name_formatted, $p_field_content );
			
			if (trim($p_field_content) == '')
			{
				$p_field_content = $this->_get_field_content( $p_data_array, $p_default_field_content );
			}
		}
		
		return $p_field_content;
	}
	
	protected function _init_languages_by_code()
	{
		$t_sql = '
				SELECT languages_id, code
				FROM ' . TABLE_LANGUAGES;
		$t_result = xtc_db_query($t_sql);
		
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$this->v_language_by_code_array[$t_row['code']] = $t_row['languages_id'];
		}
	}
	
	protected function _get_additional_field_value($p_additional_field_id, $p_item_id, $p_language_id = 0)
	{
		$c_additional_field_id = (int)$p_additional_field_id;
		$c_item_id = (int)$p_item_id;
		$c_language_id = (int)$p_language_id;
		$t_sql = 'SELECT afvd.value '
				. 'FROM additional_field_values afv, additional_field_value_descriptions afvd '
				. 'WHERE afv.additional_field_id = "' . $c_additional_field_id . '" AND '
				. 'afv.item_id = "' . $c_item_id . '" AND '
				. 'afvd.language_id = "' . $c_language_id . '"';
		$t_result = xtc_db_query($t_sql);
		
		if($t_row = xtc_db_fetch_array($t_result))
		{
			return $t_row['value'];
		}
		
		return '';
	}
	
	protected function _replace_whitespaces($p_text)
	{
		$t_text = $p_text;

		$t_text = str_replace(chr(9), '', $t_text);
		$t_text = str_replace(chr(10), '', $t_text);
		$t_text = str_replace(chr(13), '', $t_text);
		
		return $t_text;
	}
	
	protected function p_id( $p_data_array )
	{
		$t_return = $p_data_array[ 'products_id' ];
		
		if( $this->coo_scheme->v_data_array['export_properties'] == 1 && isset( $p_data_array[ 'products_properties_combis_id' ] ) && $p_data_array[ 'products_properties_combis_id' ] > 0 )
		{
			// contains properties
			$t_return .= 'p' . $p_data_array[ 'products_properties_combis_id' ];
		}

		// contains attributes
		if( $this->coo_scheme->v_data_array[ 'export_attributes' ] == 1 && isset( $p_data_array[ 'products_attributes_id' ] ) )
		{
			if(isset($p_data_array['attributes_combis']))
			{
				$t_attributes_ids_array = array();
				
				foreach($p_data_array['attributes_combis'] as $t_combi_array)
				{
					$t_attributes_ids_array[] = $t_combi_array['products_attributes_id'];
				}

				if(empty($t_attributes_ids_array) === false)
				{
					$t_return .= 'a' . implode('-', $t_attributes_ids_array);
				}
			}
			else
			{
				$t_return .= 'a' . $p_data_array[ 'products_attributes_id' ];
			}
		}
		
		return $t_return;
	}

	protected function p_name( $p_data_array )
	{
		$t_return = isset($p_data_array['products_name.' . $this->v_language_array['code']]) ? 
						$p_data_array['products_name.' . $this->v_language_array['code']] : 
						$p_data_array['products_name'];
		
		if($this->_use_properties($p_data_array))
		{
			// contains properties
			$t_properties_names_array = array();
			
			foreach($this->combi_value_names_array as $t_property_id => $t_combi_array)
			{
				$t_properties_names_array[] = $this->property_names_array[$t_property_id][$this->_get_language_id()] . ': ' . $t_combi_array[$this->_get_language_id()]['name'];
			}
			
			if(empty($t_properties_names_array) === false)
			{
				$t_return .= ' (';
				$t_return .= implode(' / ', $t_properties_names_array);
				$t_return .= ')';
			}
		}

		// contains attributes
		if( $this->coo_scheme->v_data_array[ 'export_attributes' ] == 1 && isset( $p_data_array[ 'products_attributes_id' ] ) )
		{
			if(isset($p_data_array['attributes_combis']))
			{
				$t_attributes_names_array = array();
				
				foreach($p_data_array['attributes_combis'] as $t_combi_array)
				{
					$t_attributes_names_array[] = $t_combi_array['products_options_name'] . ': ' . $t_combi_array['products_options_values_name'];
				}

				if(empty($t_attributes_names_array) === false)
				{
					$t_return .= ' (';
					$t_return .= implode(' / ', $t_attributes_names_array);
					$t_return .= ')';
				}
			}
			else
			{
				$t_return .= ' (' . $p_data_array[ 'products_options_name' ] . ': ' . $p_data_array[ 'products_options_values_name' ] . ')';
			}
		}
		
		$t_return = str_replace( "\t", ' ', $t_return );
		
		return $t_return;
	}
	
	protected function c_name( $p_data_array )
	{
		$t_return = '';
		$t_category_id = (int)$this->_get_category_id( $p_data_array );
		$t_category = $this->_get_category( $t_category_id );
		if( isset( $t_category[ 'categories_name' ] ) )
		{
			$t_return = $t_category[ 'categories_name' ];
		}
		return $t_return;
	}
	
	protected function c_path( $p_data_array )
	{
		$t_category_id = (int)$this->_get_category_id( $p_data_array );
		$t_return = $this->_build_category_path( $t_category_id );
		
		return $t_return;
	}
	
	protected function p_link( $p_data_array )
	{
		$t_return = '';
		
		$t_parameter_string = '';
		if( isset( $this->coo_scheme->v_data_array[ 'campaign_id' ] ) && !empty($this->coo_scheme->v_data_array[ 'campaign_id' ]) )
		{
			$t_parameter_string .= 'refID=' . $this->coo_scheme->v_data_array[ 'campaign_id' ] . '&';
		}
		
		if($this->coo_scheme->v_data_array['export_properties'] == 1 && isset( $p_data_array[ 'products_properties_combis_id' ] ) && $p_data_array[ 'products_properties_combis_id' ] > 0)
		{
			$t_parameter_string .= 'combi_id=' . $p_data_array[ 'products_properties_combis_id' ] . '&';
		}
		
		if( $this->coo_seo_boost == false )
		{
			$t_return = $this->_generate_link( 'product_info.php', $t_parameter_string . xtc_product_link( $p_data_array[ 'products_id' ], $p_data_array[ 'products_name' ] ) );
		}
		else
		{
			$t_return = $this->_generate_link( $this->coo_seo_boost->get_boosted_product_url( $p_data_array[ 'products_id' ], $p_data_array[ 'products_name' ] ), $t_parameter_string);			
		}
		
		return $t_return;
	}
	
	protected function _generate_link($p_page = '', $p_parameters = '')
	{
		$t_link = HTTP_SERVER . DIR_WS_CATALOG;
    
		if (xtc_not_null($p_parameters)) {
		  $t_link .= $p_page . '?' . $p_parameters;
		  $t_separator = '&';
		} else {
		  $t_link .= $p_page;
		  $t_separator = '?';
		}

		while ( (substr_wrapper($t_link, -1) == '&') || (substr_wrapper($t_link, -1) == '?') ) $t_link = substr_wrapper($t_link, 0, -1);
		
		return $t_link;
  }
	
	protected function products_description( $p_data_array )
	{
		$t_products_description = isset($p_data_array['products_description.' . $this->v_language_array['code']]) ? 
									$p_data_array['products_description.' . $this->v_language_array['code']] : 
									$p_data_array['products_description'];

		$t_products_description = $this->_replace_whitespaces($t_products_description);
		
		return $t_products_description;
	}

	protected function p_description( $p_data_array )
	{
		$t_return = '';
		$t_products_description = isset($p_data_array['products_description.' . $this->v_language_array['code']]) ? 
									$p_data_array['products_description.' . $this->v_language_array['code']] : 
									$p_data_array['products_description'];

		if( isset( $t_products_description ) )
		{
			$t_products_description = preg_replace('!(.*?)\[TAB:(.*?)\](.*?)!is', "$1$3", $t_products_description);
			$t_products_description = $this->_replace_whitespaces($t_products_description);
			$t_products_description = no_html($t_products_description);
			$t_return = $t_products_description;
		}

		return $t_return;
	}
	
	protected function p_short_description($p_data_array)
	{
		$t_products_short_description = isset($p_data_array['products_short_description.' . $this->v_language_array['code']]) ? 
											$p_data_array['products_short_description.' . $this->v_language_array['code']] : 
											$p_data_array['products_short_description'];

		$t_products_short_description = $this->_replace_whitespaces($t_products_short_description);
		$t_products_short_description = no_html($t_products_short_description);
		$t_return = $t_products_short_description;
		
		return $t_return;
	}
	
		
	protected function products_short_description( $p_data_array )
	{
		$t_return = '';
		
		$t_products_short_description = isset($p_data_array['products_short_description.' . $this->v_language_array['code']]) ? 
											$p_data_array['products_short_description.' . $this->v_language_array['code']] : 
											$p_data_array['products_short_description'];

		$t_return = $this->_replace_whitespaces($t_products_short_description);
		
		return $t_return;
	}
	
	protected function checkout_information($p_data_array)
	{
		$t_checkout_information = isset($p_data_array['checkout_information.' . $this->v_language_array['code']]) ?
									$p_data_array['checkout_information.' . $this->v_language_array['code']] :
									$p_data_array['checkout_information'];

		$t_checkout_information = $this->_replace_whitespaces($t_checkout_information);

		return $t_checkout_information;
	}
	
	protected function p_checkout_information($p_data_array)
	{
		$t_checkout_information = isset($p_data_array['checkout_information.' . $this->v_language_array['code']]) ?
									$p_data_array['checkout_information.' . $this->v_language_array['code']] :
									$p_data_array['checkout_information'];

		$t_checkout_information = $this->_replace_whitespaces($t_checkout_information);
		$t_checkout_information = no_html($t_checkout_information);

		return $t_checkout_information;
	}
	
	protected function p_gm_alt_text( $p_data_array, $p_index = 0 )
	{
		$t_return = '';
		if(empty($p_index))
		{
			$t_return = isset($p_data_array['gm_alt_text.' . $this->v_language_array['code']]) ?
							$p_data_array['gm_alt_text.' . $this->v_language_array['code']] :
							$p_data_array['gm_alt_text'];
		}
		else
		{
			$this->_build_image_data_array();
			if(isset($this->v_image_data_array[$p_data_array['products_id']][$p_index][$this->v_language_array['id']]['gm_alt_text']))
			{
				$t_return = $this->v_image_data_array[$p_data_array['products_id']][$p_index][$this->v_language_array['id']]['gm_alt_text'];
			}
		}
		
		return $t_return;
	}
	
	protected function p_additional_field($p_data_array, $p_additional_field_id)
	{
		$t_return = '';
		$c_additional_field_id = (int)$p_additional_field_id;
		
		$this->_build_additional_field_data_array();
		if(isset($this->v_additional_field_data_array[$p_data_array['products_id']][$c_additional_field_id][$this->v_language_array['id']]))
		{
			$t_return = $this->v_additional_field_data_array[$p_data_array['products_id']][$c_additional_field_id][$this->v_language_array['id']];
		}
		else if(isset($this->v_additional_field_data_array[$p_data_array['products_id']][$c_additional_field_id][0]))
		{
			$t_return = $this->v_additional_field_data_array[$p_data_array['products_id']][$c_additional_field_id][0];
		}
		
		return $t_return;
	}
	
	protected function collective_field($p_data_array, $p_source_field_names, $p_collective_sources)
	{
		$t_return = '';
		
		if(in_array('properties', $p_collective_sources))
		{
			foreach($p_source_field_names as $t_source_field_name)
			{
				if(isset($this->v_properties_array[$t_source_field_name])
				   && isset($this->combi_value_names_array[$this->v_properties_array[$t_source_field_name]])
				)
				{
					$t_return = $this->combi_value_names_array[$this->v_properties_array[$t_source_field_name]][$this->_get_language_id()]['name'];
					break;
				}
			}
		}
		
		if(empty($t_return) && in_array('attributes', $p_collective_sources))
		{
			foreach($p_source_field_names as $t_source_field_name)
			{
				if(	isset($this->v_attributes_array[$t_source_field_name]) &&
					isset($p_data_array['options_id']) &&
					empty($p_data_array['options_id']) == false &&
					$this->v_attributes_array[$t_source_field_name] == $p_data_array['options_id'])
				{
					$t_return = $p_data_array['products_options_values_name'];
					break;
				}
			}
		}
		
		if(empty($t_return) && in_array('additional_fields', $p_collective_sources))
		{
			foreach($p_source_field_names as $t_source_field_name)
			{
				if(isset($this->v_additional_fields_array[$t_source_field_name]))
				{
					$t_return = $p_data_array['additional_field_value_' . $this->v_additional_fields_array[$t_source_field_name]];
					break;
				}
			}
		}
		
		return $t_return;
	}
	
	protected function p_availability( $p_data_array )
	{
		$t_return = '1000-01-01 00:00:00';
		
		if (!empty($p_data_array['products_date_available']))
		{
			$t_return = $p_data_array['products_date_available'];
		}
		
		// Sprachabhaengige Konstanten erlaubt?
		// War im alten Export-Modul umgedreht (Ja => Nein, Nein => Ja) Nochmal klaeren!!!
		$t_return = $t_return >= date("Y-m-d H:i:s", time()) ? 'Nein' : 'Ja';
		
		return $t_return;
	}
	
	protected function products_image( $p_data_array, $p_index = 0 )
	{
		$t_return = $p_data_array[ 'products_image' ];
		
		if( isset( $p_index ) && (int)$p_index != 0 )
		{
			$this->_build_image_data_array();
			$t_return = '';
			if(isset($this->v_image_data_array[$p_data_array['products_id']][$p_index]['image_name']))
			{
				$t_return = $this->v_image_data_array[$p_data_array['products_id']][$p_index]['image_name'];
			}
		}
		
		return $t_return;
	}
	
	protected function p_image( $p_data_array, $p_index = 0 )
	{
		$t_return = $this->_get_image_path('popup') . $p_data_array[ 'products_image' ];
		
		if( isset( $p_index ) && (int)$p_index != 0 )
		{
			$this->_build_image_data_array();
			$t_return = '';
			if(isset($this->v_image_data_array[$p_data_array['products_id']][$p_index]['image_name']))
			{
				$t_return = $this->_get_image_path('popup') . $this->v_image_data_array[$p_data_array['products_id']][$p_index]['image_name'];
			}
		}
		
		return $t_return;
	}
	
	protected function p_thumb_image( $p_data_array )
	{
		$t_return = '';
		
		if( isset( $p_data_array[ 'products_image' ] ) && !empty( $p_data_array[ 'products_image' ] ) )
		{
			$t_image_dir = $this->_get_image_path('thumb');
			$t_return = $t_image_dir . $p_data_array[ 'products_image' ];
		}
		
		return $t_return;
	}
	
	protected function p_info_image( $p_data_array )
	{
		$t_return = '';
		
		if( isset( $p_data_array[ 'products_image' ] ) && !empty( $p_data_array[ 'products_image' ] ) )
		{
			$t_image_dir = $this->_get_image_path('info');
			$t_return = $t_image_dir . $p_data_array[ 'products_image' ];
		}
		
		return $t_return;
	}
	
	protected function p_popup_image( $p_data_array )
	{
		$t_return = '';
		
		if( isset( $p_data_array[ 'products_image' ] ) && !empty( $p_data_array[ 'products_image' ] ) )
		{
			$t_image_dir = $this->_get_image_path('popup');
			$t_return = $t_image_dir . $p_data_array[ 'products_image' ];
		}
		
		return $t_return;
	}
	
	protected function p_thumb_images( $p_data_array )
	{
		$t_image_dir = $this->_get_image_path('thumb');
		return $this->_get_images_for_product($t_image_dir, $p_data_array);
	}
	
	protected function p_info_images( $p_data_array )
	{
		$t_image_dir = $this->_get_image_path('info');
		return $this->_get_images_for_product($t_image_dir, $p_data_array);
	}
	
	protected function p_popup_images( $p_data_array )
	{
		$t_image_dir = $this->_get_image_path('popup');
		return $this->_get_images_for_product($t_image_dir, $p_data_array);
	}
	
	protected function p_price_comma( $p_data_array )
	{
		$t_return = $this->_calculate_price($p_data_array);
		$t_return = number_format( $t_return, 2, ',', '' );
		return $t_return;
	}
	
	protected function p_price_point( $p_data_array )
	{
		$t_return = $this->_calculate_price($p_data_array);
		$t_return = number_format( $t_return, 2, '.', '' );
		return $t_return;
	}
	
	protected function p_price_net_point( $p_data_array )
	{
		$t_return = $this->_calculate_price_net($p_data_array);
		$t_return = number_format( $t_return, 2, '.', '' );
		return $t_return;
	}
	
	protected function p_price_net_comma( $p_data_array )
	{
		$t_return = $this->_calculate_price_net($p_data_array);
		$t_return = number_format( $t_return, 2, ',', '' );
		return $t_return;
	}

	protected function p_google_special_price( $p_data_array )
	{
		$t_return = '';
		
		if($this->coo_xtc_price->xtcCheckSpecial($p_data_array['products_id']))
		{
			if($this->coo_scheme->v_data_array['export_properties'] == 1
			   && isset($p_data_array['products_properties_combis_id'])
			   && $p_data_array['products_properties_combis_id'] > 0
			)
			{
				$t_return = $this->coo_xtc_price->xtcGetPrice($p_data_array['products_id'], false, 1,
				                                              $p_data_array['products_tax_class_id'], '', 0, 0, true,
				                                              true, $p_data_array['products_properties_combis_id']);
			}
			else
			{
				$t_return = $this->coo_xtc_price->xtcGetPrice($p_data_array['products_id'], false, 1,
				                                              $p_data_array['products_tax_class_id'], '', 0, 0, true);
			}
			
			$t_return = number_format($t_return, 2, ',', '') . " " . $this->p_currency();
		}
		
		return $t_return;
	}

	protected function p_google_price( $p_data_array )
	{
		$t_return = $this->_calculate_price($p_data_array, false);
		$t_return = number_format( $t_return, 2, ',', '' ) . " " . $this->p_currency();
		return $t_return;
	}
	
	protected function p_old_price_point( $p_data_array )
	{
		$t_return = $this->_get_old_price( $p_data_array );
		if( trim($t_return) != '' )
		{
			$t_return = number_format( $t_return, 2, '.', '' );
		}
		return $t_return;
	}
	
	protected function p_old_price_comma( $p_data_array )
	{
		$t_return = $this->_get_old_price( $p_data_array );
		if( trim($t_return) != '' )
		{
			$t_return = number_format( $t_return, 2, ',', '' );
		}
		return $t_return;
	}
	
	protected function p_old_price_point_currency( $p_data_array )
	{
		$t_return = $this->p_old_price_point( $p_data_array );
		if( trim($t_return) != '' )
		{
			$t_return = $this->p_currency() . " " . $t_return;
		}
		return $t_return;
	}
	
	protected function p_old_price_comma_currency( $p_data_array )
	{
		$t_return = $this->p_old_price_comma( $p_data_array );
		if( trim($t_return) != '' )
		{
			$t_return = $this->p_currency() . " " . $t_return;
		}
		return $t_return;
	}
	
	protected function _get_old_price( $p_data_array )
	{
		$t_return = $p_data_array['products_price'];
		if( $this->coo_xtc_price->xtcCheckSpecial($p_data_array[ 'products_id' ]) )
		{
			$t_return = $this->_calculate_price($p_data_array, false);
		}
		return $t_return;
	}
    
	protected function p_baseprice_point( $p_data_array )
	{
		$t_return = '';
		$t_baseprice = (double)$this->p_baseprice($p_data_array);
		if($t_baseprice > 0)
		{
			$t_currency = $this->coo_currencies->get_currencies($this->coo_scheme->v_data_array['currencies_id']);
			$t_return .= number_format($t_baseprice, 2, '.', '') . ' ' . $t_currency['code'] . ' / ' . $this->vpe_name($p_data_array);
		}
		
		return $t_return;
	}
	
	protected function p_baseprice($p_data_array)
	{
		$t_return = '';
		$t_vpe_value = $this->vpe_value($p_data_array);
		
		if(empty($t_vpe_value) == false)
		{			
			$t_price = $this->p_price_point($p_data_array);
			$t_return = $t_price / (double)$t_vpe_value;
		}
		
		return $t_return;
	}
	
	protected function p_baseprice_comma($p_data_array)
	{
		$t_return = '';
		$t_baseprice = (double)$this->p_baseprice($p_data_array);
		if($t_baseprice > 0)
		{
			$t_currency = $this->coo_currencies->get_currencies($this->coo_scheme->v_data_array['currencies_id']);
			$t_return = number_format($t_baseprice, 2, ',', '') . ' ' . $t_currency['code'] . ' / ' . $this->vpe_name($p_data_array);
		}
		
		return $t_return;
	}
	
	protected function _calculate_price($p_data_array, $p_include_special = true)
	{
		$t_combi_price = 0;
		$t_attributes_price = 0;
		$t_consider_properties = false;
		$t_combi_id = 0;

		if($this->coo_scheme->v_data_array['export_properties'] == 1 && isset( $p_data_array[ 'products_properties_combis_id' ] ) && $p_data_array[ 'products_properties_combis_id' ] > 0)
		{
			$t_consider_properties = true;
			$t_combi_id = $p_data_array['products_properties_combis_id'];
		}

		$t_product_price = $this->coo_xtc_price->xtcGetPrice($p_data_array['products_id'], false, 1, $p_data_array['products_tax_class_id'], '', 0, 0, $p_include_special, $t_consider_properties, $t_combi_id);

		if( $this->coo_scheme->v_data_array[ 'export_attributes' ] == 1 && isset( $p_data_array[ 'products_attributes_id' ] ) )
		{
			if(isset($p_data_array['attributes_combis']))
			{
				foreach($p_data_array['attributes_combis'] as $t_combi_array)
				{
					$t_attributes_price_array = $this->coo_xtc_price->xtcGetOptionPrice($p_data_array['products_id'], $t_combi_array['options_id'], $t_combi_array['options_values_id']);
					$t_attributes_price += $t_attributes_price_array['price'];
				}
			}
			else
			{
				$t_attributes_price_array = $this->coo_xtc_price->xtcGetOptionPrice($p_data_array['products_id'], $p_data_array['options_id'], $p_data_array['options_values_id']);
				$t_attributes_price += $t_attributes_price_array['price'];
			}
		}
		
		$t_return = $t_product_price + $t_combi_price + $t_attributes_price;
		
		return $t_return;
	}
	
	protected function _calculate_price_net($p_data_array)
	{
		$t_consider_properties = false;
		$t_combi_id = 0;
		
		if($this->coo_scheme->v_data_array['export_properties'] == 1 && isset( $p_data_array[ 'products_properties_combis_id' ] ) && $p_data_array[ 'products_properties_combis_id' ] > 0)
		{
			$t_consider_properties = true;
			$t_combi_id = $p_data_array['products_properties_combis_id'];
		}

		$t_product_price = $this->coo_xtc_price->xtcGetPrice($p_data_array['products_id'], false, 1, 0, '',  0, 0, true, $t_consider_properties, $t_combi_id);
		
		return $t_product_price;
	}

	protected function become_baseprice_value($p_data_array)
	{
		$t_return = '';
		if(preg_match('/(\d+\s+)?(\w+)/', $this->vpe_name($p_data_array), $matches) == 1)
		{
			$t_return = 1;
			$t_number = trim($matches[1]);
			if(is_numeric($t_number))
			{
				$t_return = $t_number;
			}
		}
		
		return $t_return;
	}
	
	protected function become_baseprice_amount($p_data_array)
	{
		$t_return = '';
		$t_baseprice = (double)$this->p_baseprice($p_data_array);
		if( $t_baseprice > 0 )
		{
			$t_return = number_format($t_baseprice, 2, '.', '');
		}
		
		return $t_return;
	}
	
	protected function become_baseprice_unit($p_data_array)
	{
		$t_return = '';
		if(preg_match('/(\d+\s+)?(\w+)/', $p_data_array['products_vpe_name'], $matches) == 1)
		{
			$t_return = $matches[2];
		}
		return $t_return;
	}
    
    protected function p_currency()
    {
        $t_currency = $this->coo_currencies->get_currencies($this->coo_scheme->v_data_array['currencies_id']);
        $t_return = $t_currency['code'];
        
        return $t_return;
    }
	
	protected function _shipping_costs( $p_data_array, $p_shipping_free_minimum = false )
	{
		$t_shipping_costs = '';
		
		if((double)$p_data_array['nc_ultra_shipping_costs'] > 0)
		{
			$t_shipping_costs = (double)$p_data_array['nc_ultra_shipping_costs'];
			if(strpos_wrapper(MODULE_SHIPPING_INSTALLED, 'gambioultra') !== false)
			{
				$t_tax_rate = xtc_get_tax_rate(MODULE_SHIPPING_GAMBIOULTRA_TAX_CLASS);
				$t_shipping_costs = $this->coo_xtc_price->xtcAddTax($t_shipping_costs, $t_tax_rate);
			}
			else
			{
				$t_shipping_costs = $this->coo_xtc_price->xtcCalculateCurr($t_shipping_costs);
			}
		}
		if($p_shipping_free_minimum
		   && (double)$this->p_price_point($p_data_array)
		      >= (double)$this->coo_scheme->v_data_array['shipping_free_minimum']
		)
		{
			$t_shipping_costs = 0.00;
		}
		
		return $t_shipping_costs;
	}
	
	protected function p_shipping_costs_point( $p_data_array )
	{
		$t_return = $this->_shipping_costs($p_data_array);
		
		if($t_return !== '')
		{
			$t_return = number_format((double)$t_return, 2, '.', '');
		}
		
		return $t_return;
	}
	
	protected function p_shipping_costs_comma( $p_data_array )
	{
		$t_return = $this->_shipping_costs($p_data_array);
		
		if($t_return !== '')
		{
			$t_return = number_format((double)$t_return, 2, ',', '');
		}
		
		return $t_return;
	}

	protected function p_shipping_costs_point_with_shipping_free_minimum( $p_data_array )
	{
		$t_return = $this->_shipping_costs($p_data_array, true);

		if($t_return !== '')
		{
			$t_return = number_format((double)$t_return, 2, '.', '');
		}

		return $t_return;
	}

	protected function p_shipping_costs_comma_with_shipping_free_minimum( $p_data_array )
	{
		$t_return = $this->_shipping_costs($p_data_array, true);

		if($t_return !== '')
		{
			$t_return = number_format((double)$t_return, 2, ',', '');
		}

		return $t_return;
	}
	
	protected function p_weight_point( $p_data_array )
	{
		$t_return = number_format($this->_calculate_weight($p_data_array), 3, '.', '');
	
		return $t_return;
	}
	
	protected function p_weight_comma( $p_data_array )
	{
		$t_return = number_format($this->_calculate_weight($p_data_array), 3, ',', '');
		
		return $t_return;
	}
	
	protected function _calculate_weight($p_data_array)
	{
		$t_return = (double)$p_data_array['products_weight'];

		if( $this->coo_scheme->v_data_array[ 'export_attributes' ] == 1 && isset( $p_data_array[ 'products_attributes_id' ] ) )
		{
			if(isset($p_data_array['attributes_combis']))
			{
				foreach($p_data_array['attributes_combis'] as $t_combi_array)
				{
					$t_attributes_weight_array = $this->coo_xtc_price->xtcGetOptionPrice($p_data_array['products_id'], $t_combi_array['options_id'], $t_combi_array['options_values_id']);
					$t_return += (double)$t_attributes_weight_array['weight'];
				}
			}
			else
			{
				$t_attributes_weight_array = $this->coo_xtc_price->xtcGetOptionPrice($p_data_array['products_id'], $p_data_array['options_id'], $p_data_array['options_values_id']);
				$t_return += (double)$t_attributes_weight_array['weight'];
			}
		}
		
		if($this->coo_scheme->v_data_array['export_properties'] == 1 && isset( $p_data_array[ 'products_properties_combis_id' ] ) && $p_data_array[ 'products_properties_combis_id' ] > 0)
		{
			if($p_data_array['use_properties_combis_weight'] == 0)
			{
				$t_return += (double)$p_data_array['combi_weight'];
			}
			else
			{
				$t_return = (double)$p_data_array['combi_weight'];
			}
		}
		
		return $t_return;
	}
	
	protected function p_special_period($p_data_array)
	{
		if( (isset($p_data_array['specials_id']) && !empty($p_data_array['specials_id']) ) &&  ( isset( $p_data_array['status']) && $p_data_array['status'] == 1) )
		{
			$t_start_timestamp = strtotime($p_data_array['specials_date_added']);
			
			if(isset($p_data_array['specials_last_modified']) && !empty($p_data_array['specials_last_modified']))
			{
				$t_start_timestamp = strtotime($p_data_array['specials_last_modified']);
			}
			
			$t_end_timestamp = strtotime($p_data_array['expires_date']);
			
			if(empty($p_data_array['expires_date']) || $p_data_array['expires_date'] === '1000-01-01 00:00:00' || $p_data_array['expires_date'] === '0000-00-00 00:00:00')
			{
				$t_end_timestamp = strtotime('+1 year', time());
			}
			
			$t_return = date('c', $t_start_timestamp) . '/' . date('c', $t_end_timestamp);
		}
		else
		{
			$t_return = '';
		}
		
		return $t_return;
	}

	/*
 * GOOGLE-SHOPPING FUNKTIONEN
 */
	
	protected function p_google_name_vpe_prefix( $p_data_array )
	{
		$t_return = '';
		
		$t_baseprice = $this->p_baseprice_comma($p_data_array);
		$t_name = $this->p_name($p_data_array);
		
		if($t_baseprice > 0)
		{
			$t_return = '(' . $t_baseprice . ') ';
		}
		$t_return .= $t_name;
		
		return $t_return;
	}
	
	protected function p_google_name_vpe_suffix( $p_data_array )
	{
		$t_return = '';
		
		$t_baseprice = $this->p_baseprice_comma($p_data_array);
		$t_name = $this->p_name($p_data_array);
		$t_return = $t_name;
		if($t_baseprice > 0)
		{
			$t_return .= ' (' . $t_baseprice . ')';
		}
		
		return $t_return;
	}
	
	protected function p_google_shipping_costs( $p_data_array )
	{
		$t_return = '';
		$t_shipping_costs = $this->p_shipping_costs_point($p_data_array);
		if(empty($t_shipping_costs) == false)
		{
			$t_return = ':::' . $t_shipping_costs . ' ' . $this->p_currency();
		}
		
		return $t_return;
	}

	protected function p_google_export_availability( $p_data_array )
	{
		$t_return = 'auf lager';
		
		if(isset($p_data_array['availability']))
		{
			$t_return = $p_data_array['availability'];
		}
		else
		{
			$t_google_export_availability = $this->coo_google_export_availability->get_google_export_availability( $p_data_array[ 'google_export_availability_id' ] );
			if(!empty($t_google_export_availability[ 'availability' ]))
			{
				$t_return = $t_google_export_availability[ 'availability' ];
			}			
		}
		
		return $t_return;
	}
	
	protected function p_google_export_condition( $p_data_array )
	{
		$t_return = 'neu';
		
		if( isset( $p_data_array['google_export_condition'] ) && !empty( $p_data_array['google_export_condition'] ) )
		{
			$t_return = $p_data_array['google_export_condition'];
		}
		
		return $t_return;
	}
	
	protected function p_google_category( $p_data_array )
	{
		if (empty($this->v_google_categories_array))
		{
			$t_sql = 'SELECT google_category, products_id FROM products_google_categories WHERE products_id IN (' . implode(',', $this->v_product_ids_array) . ') GROUP BY products_id';
			$t_result = xtc_db_query($t_sql);
			while ($t_row = xtc_db_fetch_array($t_result))
			{
				$this->v_google_categories_array[$t_row['products_id']] = $t_row['google_category'];
			}
		}
		
		return $this->v_google_categories_array[$p_data_array['products_id']];
	}
	
//	EAN (in Europa), ISBN (fuer Buecher), UPC (in Nordamerika), JAN (in Japan)
	protected function p_google_export_gtin( $p_data_array )
	{
		// init with EAN
		$t_return = $this->p_ean($p_data_array);
		
		if(empty($t_return) && empty($p_data_array['code_isbn']) == false)
		{
			$t_return = trim($p_data_array['code_isbn']);
		}
		elseif(empty($t_return) && !empty($p_data_array['code_upc']))
		{
			$t_return = trim($p_data_array['code_upc']);
		}
		elseif(empty($t_return) && !empty($p_data_array['code_jan']))
		{
			$t_return = trim($p_data_array['code_jan']);
		}
		
		return $t_return;
	}


	protected function p_google_identifier_exists($p_data_array)
	{

		$gtin         = $this->p_google_export_gtin($p_data_array);
		$p_data_array = is_array($p_data_array) ? $p_data_array : array();
		$brandName    = array_key_exists('brand_name', $p_data_array) ? $p_data_array['brand_name'] : false;
		$mpn          = array_key_exists('code_mpn', $p_data_array) ? $p_data_array['code_mpn'] : false;

		$identifierExist = 'FALSE';

		$identifierExist = $gtin ? 'TRUE' : $identifierExist;
		$identifierExist = ($mpn && $brandName) ? 'TRUE' : $identifierExist;

		return $identifierExist;
	}
	
	protected function p_google_fsk18($p_data_array)
	{
		$t_return = 'FALSE';
		
		if($p_data_array['products_fsk18'] == 1)
		{
			$t_return = 'TRUE';
		}
		
		return $t_return;
	}
	
	protected function p_google_unit_price_measure($p_data_array)
	{
		$t_google_unit_price_measure = '';
		$t_allowed_units_array = array('mg', 'g', 'kg', 'ml', 'cl', 'l', 'cm', 'm', 'm2', 'cbm');
		$t_allowed_unit_values_array = array(1, 10, 100, 75, 50, 1000);
		
		$t_vpe_name = $this->vpe_name($p_data_array);
		$t_vpe_value = $this->vpe_value($p_data_array);
		
		if($t_vpe_name != '' && empty($t_vpe_value) === false)
		{
			$t_vpe_name = trim($t_vpe_name);
			preg_match('/([0-9]*)(.+)/', $t_vpe_name, $t_matches_array);
			
			$t_unit_value = (double)$t_matches_array[1];
			if(empty($t_unit_value))
			{
				$t_unit_value = 1;
			}
			$t_unit_name = trim($t_matches_array[2]);
			
			$p_products_array['unit_pricing_base_measure'] = '';
			$p_products_array['unit_price_measure'] = '';
			
			if(in_array($t_unit_name, $t_allowed_units_array) && in_array($t_unit_value, $t_allowed_unit_values_array))
			{
				$t_continue = true;
				
				switch($t_unit_value)
				{
					case 50:
					case 1000:
						if($t_unit_name != 'kg')
						{
							$t_continue = false;
						}
						break;
					case 75:
						if($t_unit_name != 'cl')
						{
							$t_continue = false;
						}
				}			
				
				if($t_continue === true)
				{
					$t_unit_price_measure_value = (string)((double)$t_vpe_value * $t_unit_value);
					$t_google_unit_price_measure = $t_unit_price_measure_value . ' ' . $t_unit_name;
				}
			}		
		}
		
		return $t_google_unit_price_measure;
	}
	
	protected function p_google_unit_pricing_base_measure($p_data_array)
	{
		$t_google_unit_pricing_base_measure = '';
		$t_allowed_units_array = array('mg', 'g', 'kg', 'ml', 'cl', 'l', 'cm', 'm', 'm2', 'cbm');
		$t_allowed_unit_values_array = array(1, 10, 100, 75, 50, 1000);
		
		$t_vpe_name = $this->vpe_name($p_data_array);
		
		if($t_vpe_name != '')
		{
			$t_vpe_name = trim($t_vpe_name);
			preg_match('/([0-9]*)(.+)/', $t_vpe_name, $t_matches_array);
			
			$t_unit_value = (double)$t_matches_array[1];
			if(empty($t_unit_value))
			{
				$t_unit_value = 1;
			}
			$t_unit_name = trim($t_matches_array[2]);
			
			$p_products_array['unit_pricing_base_measure'] = '';
			$p_products_array['unit_price_measure'] = '';
			
			if(in_array($t_unit_name, $t_allowed_units_array) && in_array($t_unit_value, $t_allowed_unit_values_array))
			{
				$t_continue = true;
				
				switch($t_unit_value)
				{
					case 50:
					case 1000:
						if($t_unit_name != 'kg')
						{
							$t_continue = false;
						}
						break;
					case 75:
						if($t_unit_name != 'cl')
						{
							$t_continue = false;
						}
				}			
				
				if($t_continue === true)
				{
					$t_google_unit_pricing_base_measure = $t_unit_value . ' ' . $t_unit_name;
				}
			}		
		}
		
		return $t_google_unit_pricing_base_measure;
	}
	
	protected function p_google_product_group($p_data_array)
	{
		if((isset($p_data_array['products_attributes_id']) && empty($p_data_array['products_attributes_id']) === false)
		   || (isset($p_data_array['products_properties_combis_id'])
		       && empty($p_data_array['products_properties_combis_id']) === false)
		)
		{
			return md5($p_data_array['products_id']);
		}
		return '';
	}
	
/*
 * DAPARTO FUNKTIONEN
 */
	protected function p_daparto_shippingtime( $p_data_array )
	{
		if($p_data_array['products_shippingdays'] <= 2)
		{
			$t_return = 1;
		}
		else if($p_data_array['products_shippingdays'] <= 5)
		{
			$t_return = 2;
		}
		else
		{ // shippingdays > 5
			$t_return = 3;
		}
		
		return $t_return;
	}
	
	protected function _get_image_path( $p_image_type )
	{
		$t_image_dir = HTTP_SERVER;
		
		if(defined("DIR_WS_CATALOG_ORIGINAL_IMAGES"))
		{
			switch ($p_image_type)
			{
				case "thumb":
					$t_image_dir .= DIR_WS_CATALOG_THUMBNAIL_IMAGES;
					break;
				
				case "popup":
					$t_image_dir .= DIR_WS_CATALOG_POPUP_IMAGES;
					break;
				
				case "info":
					$t_image_dir .= DIR_WS_CATALOG_INFO_IMAGES;
					break;

				default:
					break;
			}
		}
		
		else
		{
			$t_image_dir .= DIR_WS_CATALOG;
			
			switch ($p_image_type)
			{
				case "thumb":
					$t_image_dir .= DIR_WS_THUMBNAIL_IMAGES;
					break;
				
				case "popup":
					$t_image_dir .= DIR_WS_POPUP_IMAGES;
					break;
				
				case "info":
					$t_image_dir .= DIR_WS_INFO_IMAGES;
					break;

				default:
					break;
			}
		}
		
		return $t_image_dir;
	}
		
	
	protected function _get_images_for_product($p_image_dir, $p_data_array)
	{
		$t_return = '';
		if( isset( $p_data_array[ 'products_image' ] ) && !empty( $p_data_array[ 'products_image' ] ) )
		{
			$t_return .= $p_image_dir . $p_data_array['products_image'] . ', ';
		}
		
		$this->_build_image_data_array();
		
		if(isset($this->v_image_data_array[$p_data_array['products_id']]))
		{
			foreach($this->v_image_data_array[$p_data_array['products_id']] AS $t_image_array)
			{
				if(!empty($t_image_array['image_name']))
				{
					$t_return .= $p_image_dir . $t_image_array['image_name'] . ', ';
				}
			}
		}
		
		if(!empty($t_return))
		{
			$t_return = substr_wrapper($t_return, 0, (strlen_wrapper($t_return) - 2 ));
		}
		
		
		return $t_return;
	}
	
	
	protected function _get_language_id_by_code($p_language_code)
	{
		return $this->v_language_by_code_array[$p_language_code];
	}
	
	
	protected function _set_language_code($p_language_code)
	{
		$this->v_language_array['code'] = (string)$p_language_code;
	}
	
	
	protected function _get_language_code()
	{
		$t_language_code = false;
		
		if(isset($this->v_language_array['code']))
		{
			$t_language_code = $this->v_language_array['code'];
		}
		
		return $t_language_code;
	}
	
	
	protected function _set_language_id($p_language_id)
	{
		$this->v_language_array['id'] = (int)$p_language_id;
	}
	
	
	protected function _get_language_id()
	{
		$t_language_id = false;
		
		if(isset($this->v_language_array['id']))
		{
			$t_language_id = $this->v_language_array['id'];
		}
		
		return $t_language_id;
	}
	
	
	protected function _get_category_id( $p_data_array )
	{
		if (array_key_exists($p_data_array['products_id'], $this->v_product_categories_array))
		{
			return $this->v_product_categories_array[$p_data_array['products_id']];
		}
		
		$t_category_id = 0;
		$t_sql = '
			SELECT categories_id
			FROM ' . TABLE_PRODUCTS_TO_CATEGORIES . '
			WHERE products_id = "' . $p_data_array['products_id'] . '"
			AND categories_id != "0"
			ORDER BY categories_id';
		$t_result = xtc_db_query( $t_sql );
		
		$t_row = xtc_db_fetch_array($t_result); // Take the first matching category. 
		
		if ($t_row)
		{
			$t_category_id = $t_row['categories_id'];
		}
		
		$this->v_product_categories_array[$p_data_array['products_id']] = $t_category_id;
		
		return $t_category_id;
	}
	
	protected function _build_category_path( $p_category )
	{
		$t_categories_names_array = array();
		$t_parent_categories_array = array();
		$t_actual_category_id = $p_category;
		
		while( $t_actual_category_id != 0 && !in_array($t_actual_category_id, $t_parent_categories_array) )
		{
			$t_parent_categories_array[] = $t_actual_category_id;
			$t_category = $this->_get_category( $t_actual_category_id );
			if( $this->coo_scheme->v_data_array['type_id'] == 1 )
			{
				$t_category[ 'categories_name' ] .= '[' . $t_actual_category_id . ']';
			}
			$t_categories_names_array[] = $t_category[ 'categories_name' ];
			$t_actual_category_id = $t_category[ 'parent_id' ];
		}
		
		$t_categories_names_array = array_reverse($t_categories_names_array);
		
		if (trim(implode('', $t_categories_names_array)) == '[]')
		{
			$t_categories_names_array = array();
		}
			
		return implode(' > ', $t_categories_names_array);
	}
	
	protected function _get_category( $p_category_id )
	{
		$t_return = false;
		$c_category_id = (int)$p_category_id;
		
		if( $c_category_id > 0 )
		{
			if (array_key_exists($c_category_id, $this->v_category_parents_array) && array_key_exists($c_category_id, $this->v_category_names_array) && array_key_exists($this->_get_language_id(), $this->v_category_names_array[$c_category_id]))
			{
				$t_return = array();
				$t_return['categories_name'] = $this->v_category_names_array[$c_category_id][$this->_get_language_id()];
				$t_return['parent_id'] = $this->v_category_parents_array[$c_category_id];
			}
			else
			{
				$t_sql = '
					SELECT cd.categories_name, c.parent_id
					FROM ' . TABLE_CATEGORIES . ' AS c
					LEFT JOIN ' . TABLE_CATEGORIES_DESCRIPTION . ' AS cd ON c.categories_id = cd.categories_id
					WHERE c.categories_id = "' . $c_category_id . '" 
					AND language_id="' . $this->_get_language_id() . '"';
				$t_result = xtc_db_query( $t_sql );
				$t_return = xtc_db_fetch_array($t_result);
				if (!array_key_exists($c_category_id, $this->v_category_names_array))
				{
					$this->v_category_names_array[$c_category_id] = array();
				}
				$this->v_category_names_array[$c_category_id][$this->_get_language_id()] = $t_return['categories_name'];
				$this->v_category_parents_array[$c_category_id] = $t_return['parent_id'];
			}
		}
		
		return $t_return;
	}
	
	protected function p_manufacturer_name( $p_data_array )
	{
		$c_manufacturer_id = (int)$p_data_array['manufacturers_id'];
		$t_manufacturer_name = '';
		if(!empty($c_manufacturer_id))
		{
			$t_manufacturer = $this->coo_manufacturers->get_manufacturer( $p_data_array['manufacturers_id'] );
			if(!empty($t_manufacturer))
			{
				$t_manufacturer_name = $t_manufacturer[ 'manufacturers_name' ];
			}
		}
		return $t_manufacturer_name;
	}
	
	protected function p_model( $p_data_array )
	{
		$t_return = trim($p_data_array[ 'products_model' ]);
		
		if( $this->coo_scheme->v_data_array['export_properties'] == 1 && isset( $p_data_array[ 'products_properties_combis_id' ] ) && $p_data_array[ 'products_properties_combis_id' ] > 0 )
		{
			// contains properties
			if( APPEND_PROPERTIES_MODEL == 'true' && $t_return != '' && $p_data_array[ 'combi_model' ] != '' )
			{
				$t_return .= '-';
			}
			else if( APPEND_PROPERTIES_MODEL == 'false' )
			{
				$t_return = '';
			}
			$t_return .= $p_data_array[ 'combi_model' ];
		}

		// contains attributes
		if( $this->coo_scheme->v_data_array['export_attributes'] == 1 && isset( $p_data_array['products_attributes_id'] ) )
		{
			if(isset($p_data_array['attributes_combis']))
			{
				$t_attributes_models_array = array();

				foreach($p_data_array['attributes_combis'] as $t_combi_array)
				{
					if(trim($t_combi_array['attributes_model']) !== '')
					{
						$t_attributes_models_array[] = trim($t_combi_array['attributes_model']);
					}
				}

				if(empty($t_attributes_models_array) === false)
				{
					if( $t_return != '')
					{
						$t_return .= '-';
					}
					
					$t_return .= implode('-', $t_attributes_models_array);
				}
			}
			else
			{
				if( $t_return != '' && $p_data_array[ 'attributes_model' ] != '' )
				{
					$t_return .= '-';
				}
				
				$t_return .= $p_data_array[ 'attributes_model' ];
			}
		}
		
		return $t_return;
	}
	
	protected function p_ean($p_data_array)
	{
		$t_return = trim($p_data_array['products_ean']);
		if($this->_use_properties($p_data_array) && trim($p_data_array['combi_ean']) != '')
		{
			$t_return = $p_data_array['combi_ean'];
		}

		if( $this->coo_scheme->v_data_array['export_attributes'] == 1 && isset( $p_data_array['products_attributes_id'] ) )
		{
			// contains attributes
			$t_return = $p_data_array[ 'gm_ean' ];
		}
		
		return $t_return;
	}
	
	protected function p_expiration_date($p_data_array)
	{
		$t_return = $p_data_array['expiration_date'];
		$t_return = str_replace('1000-01-01', '', $t_return);
		
		return $t_return;
	}
	
	protected function p_quantity( $p_data_array )
	{
		$t_return = $p_data_array[ 'products_quantity' ];

		if( $this->coo_scheme->v_data_array[ 'export_attributes' ] == 1 
			&& isset( $p_data_array[ 'products_attributes_id' ] ) 
			&& STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true')
		{
			if(isset($p_data_array['attributes_combis']))
			{
				$t_stock_array = array();
				
				foreach($p_data_array['attributes_combis'] as $t_combi_array)
				{
					$t_stock_array[] = (double)$t_combi_array['attributes_stock'];
				}

				$t_return = min($t_stock_array);
			}
			else
			{
				$t_return = (double)$p_data_array['attributes_stock'];
			}
		}
		
		if($this->_use_properties($p_data_array) 
				&& (($p_data_array['use_properties_combis_quantity'] == 0 && STOCK_CHECK == 'true' && ATTRIBUTE_STOCK_CHECK == 'true') 
				|| $p_data_array['use_properties_combis_quantity'] == 2))
		{
			$t_return = $p_data_array['combi_quantity'];
		}
		
		return $t_return;
	}
	
	protected function p_quantity_floor( $p_data_array )
	{
		$t_return = $this->p_quantity($p_data_array);
		
		if ($t_return < 0)
		{
			$t_return = 0;
		}
		else
		{
			$t_return = floor($t_return);
		}
		
		return $t_return;
	}
	
	protected function p_shipping_status_name( $p_data_array)
	{
		$t_return = '';
		if(isset($p_data_array['shipping_status_name.' . $this->v_language_array['code']]))
		{
			$t_return = $p_data_array['shipping_status_name.' . $this->v_language_array['code']];
		}
		else
		{
			$t_return = $p_data_array['shipping_status_name'];
			if($this->_use_properties($p_data_array) && $p_data_array['use_properties_combis_shipping_time'] == 1 && $p_data_array['combi_shipping_status_id'] != 0)
			{
				$t_return = $this->coo_shipping_status->get_shipping_status_name($p_data_array['combi_shipping_status_id'], $this->coo_scheme->v_data_array['languages_id']);
			}
		}
		
		return $t_return;
	}

	protected function vpe_name( $p_data_array )
	{
		$t_vpe_name = '';
		
		// eigenschaften
		if($this->coo_scheme->v_data_array['export_properties'] == 1 
				&& isset($p_data_array[ 'products_properties_combis_id' ]) 
				&& $p_data_array[ 'products_properties_combis_id' ] > 0
				&& $p_data_array['combi_vpe_value'] != 0 
				&& $p_data_array['combi_vpe_id'] != 0)
		{
			$t_vpe_name = $this->coo_vpe_source->get_products_vpe_name($p_data_array['combi_vpe_id'], $this->coo_scheme->v_data_array['languages_id']);
		}
		// attribute
		elseif($this->coo_scheme->v_data_array['export_attributes'] == 1 
				&& (int)$p_data_array['products_attributes_id'] > 0 
				&& (int)$p_data_array['products_vpe_id'] > 0 
				&& (double)$p_data_array['gm_vpe_value'] > 0)
		{
			$t_vpe_name = $this->coo_vpe_source->get_products_vpe_name($p_data_array['products_vpe_id'], $this->coo_scheme->v_data_array['languages_id']);
		}
		elseif($p_data_array['products_vpe_value'] != 0 && $p_data_array['products_vpe'] != 0)
		{
			$t_vpe_name = $this->coo_vpe_source->get_products_vpe_name($p_data_array['products_vpe'], $this->coo_scheme->v_data_array['languages_id']);
		}
		
		return $t_vpe_name;
	}

	protected function vpe_value($p_data_array)
	{
		$t_return = '';
		
		// eigenschaften
		if($this->coo_scheme->v_data_array['export_properties'] == 1 
				&& isset($p_data_array[ 'products_properties_combis_id' ]) 
				&& $p_data_array[ 'products_properties_combis_id' ] > 0
				&& $p_data_array['combi_vpe_value'] != 0 
				&& $p_data_array['combi_vpe_id'] != 0)
		{
			$t_return = $p_data_array['combi_vpe_value'];
		}
		// attribute
		elseif($this->coo_scheme->v_data_array['export_attributes'] == 1 
				&& (int)$p_data_array['products_attributes_id'] > 0 
				&& (int)$p_data_array['products_vpe_id'] > 0 
				&& (double)$p_data_array['gm_vpe_value'] > 0)
		{
			$t_return = $p_data_array['gm_vpe_value'];
		}
		elseif($p_data_array['products_vpe_value'] != 0 && $p_data_array['products_vpe'] != 0)
		{
			$t_return = $p_data_array['products_vpe_value'];
		}
		
		return $t_return;
	}


	protected function p_personal_offer($p_data_array, $p_customer_status_id)
	{
		$t_personal_offers = '';
		$c_customer_status_id = (int)$p_customer_status_id;
		$this->_build_personal_offers_array($p_customer_status_id);
		
		if(isset($this->v_personal_offers_array[$c_customer_status_id][(int)$p_data_array['products_id']]['string']))
		{
			$t_personal_offers = implode('::', $this->v_personal_offers_array[$c_customer_status_id][(int)$p_data_array['products_id']]['string']);
		}
		
		return $t_personal_offers;
	}
	
	protected function p_group_permission($p_data_array, $p_customer_status_id)
	{
		return isset($p_data_array['group_permission_' . $p_customer_status_id]) ? $p_data_array['group_permission_' . $p_customer_status_id] : '';
	}
	
	protected function p_tax($p_data_array)
	{
		return xtc_get_tax_rate($p_data_array['products_tax_class_id']);
	}
	
	protected function p_attribute($p_data_array)
	{
		$t_return = '';
		
		if( $this->coo_scheme->v_data_array[ 'export_attributes' ] == 1 && isset( $p_data_array[ 'products_attributes_id' ] ) )
		{
			if(isset($p_data_array['attributes_combis']))
			{
				$t_attributes_names_array = array();

				foreach($p_data_array['attributes_combis'] as $t_combi_array)
				{
					$t_attributes_names_array[] = $t_combi_array['products_options_name'] . ': ' . $t_combi_array['products_options_values_name'];
				}

				if(empty($t_attributes_names_array) === false)
				{
					$t_return .= implode(' / ', $t_attributes_names_array);
				}
			}
			else
			{
				$t_return .= $p_data_array[ 'products_options_name' ] . ': ' . $p_data_array[ 'products_options_values_name' ];
			}
		}
		
		return $t_return;
	}
	
	public function set_product_ids_array(&$p_product_ids_array)
	{
		$this->v_product_ids_array = $p_product_ids_array;
	}
	
	protected function _build_image_data_array()
	{
		if(empty($this->v_image_data_array))
		{
			$t_sql = "SELECT 
							i.products_id,
							a.language_id,
							a.gm_alt_text,
							i.image_nr,
							i.image_name						
						FROM 
							" . TABLE_PRODUCTS_IMAGES. " i
						LEFT JOIN gm_prd_img_alt AS a ON (a.image_id = i.image_id AND a.products_id IN (" . implode(',', $this->v_product_ids_array) . ") AND a.products_id = i.products_id)
						WHERE 
							i.products_id IN (" . implode(',', $this->v_product_ids_array) . ")";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$this->v_image_data_array[$t_result_array['products_id']][$t_result_array['image_nr']][$t_result_array['language_id']]['gm_alt_text'] = $t_result_array['gm_alt_text'];
				$this->v_image_data_array[$t_result_array['products_id']][$t_result_array['image_nr']]['image_name'] = $t_result_array['image_name'];
			}
		}			
	}
	
	protected function _build_additional_field_data_array()
	{
		if(empty($this->v_additional_field_data_array))
		{
			$t_sql = "SELECT
							af.additional_field_id,
							afv.item_id,
							afvd.language_id,
							afvd.value
						FROM 
							additional_fields af
						LEFT JOIN
							additional_field_values afv ON (afv.additional_field_id = af.additional_field_id)
						LEFT JOIN
							additional_field_value_descriptions afvd ON (afv.additional_field_value_id = afvd.additional_field_value_id)
						WHERE
							af.item_type = 'product' AND
							afv.item_id IN (" . implode(',', $this->v_product_ids_array) . ")";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$this->v_additional_field_data_array[$t_result_array['item_id']][$t_result_array['additional_field_id']][$t_result_array['language_id']] = $t_result_array['value'];
			}
		}			
	}
	
	protected function _build_personal_offers_array($p_customer_status_id)
	{
		$c_customer_status_id = (int)$p_customer_status_id;
		
		if(!isset($this->v_personal_offers_array[$c_customer_status_id]))
		{
			$t_sql = "SELECT 
							products_id,
							quantity, 
							personal_offer 
						FROM " . TABLE_PERSONAL_OFFERS_BY . $c_customer_status_id . " 
						WHERE 
							products_id IN (" . implode(',', $this->v_product_ids_array) . ") AND
							personal_offer > 0
						ORDER BY quantity";
			$t_result = xtc_db_query($t_sql);
			while($t_result_array = xtc_db_fetch_array($t_result))
			{
				$this->v_personal_offers_array[$c_customer_status_id][$t_result_array['products_id']]['price'][(double)$t_result_array['quantity']] = (double)$t_result_array['personal_offer'];
				$this->v_personal_offers_array[$c_customer_status_id][$t_result_array['products_id']]['string'][(double)$t_result_array['quantity']] = (double)$t_result_array['quantity'] . ':' . (double)$t_result_array['personal_offer'];
			}
		}		
	}
	
	protected function _use_properties($p_data_array)
	{
		if($this->coo_scheme->v_data_array['export_properties'] == 1 
				&& isset( $p_data_array['products_properties_combis_id'] ) 
				&& (int)$p_data_array['products_properties_combis_id'] > 0)
		{
			return true;
		}
		
		return false;
	}
	
	protected function _use_attributes($p_data_array)
	{
		if($this->coo_scheme->v_data_array['export_attributes'] == 1 
				&& isset($p_data_array['products_attributes_id']) 
				&& (int)$p_data_array['products_attributes_id'] > 0)
		{
			return true;
		}
		
		return false;
	}
	
	public function _get_combi_value_names($p_combi_id)
	{
		$c_combi_id = (int)$p_combi_id;
		
		if($c_combi_id === 0)
		{
			$this->combi_value_names_array = array();
			return;
		}
		
		$this->combi_value_names_array = array();
		
		$t_sql = "SELECT 
						properties_id,
						properties_values_id,
						values_name,
						language_id
					FROM products_properties_index
					WHERE 
						products_properties_combis_id = '" . $c_combi_id . "'";
		$t_result = xtc_db_query($t_sql);
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$this->combi_value_names_array[$t_result_array['properties_id']][$t_result_array['language_id']] = array();		
			$this->combi_value_names_array[$t_result_array['properties_id']][$t_result_array['language_id']]['id'] = $t_result_array['properties_values_id'];		
			$this->combi_value_names_array[$t_result_array['properties_id']][$t_result_array['language_id']]['name'] = $t_result_array['values_name'];		
		}
	}
	
	protected function property($p_data_array, $p_property_id)
	{
		$t_property = '';
		
		if(array_key_exists($p_property_id, $this->combi_value_names_array))
		{
			$t_property = $this->combi_value_names_array[$p_property_id][$this->_get_language_id()]['name'];
		}
		
		return $t_property;
	}
	
	protected function _init_property_names()
	{
		$t_sql = 'SELECT 
						properties_id,
						language_id,
						properties_name
					FROM properties_description';
		$t_result = xtc_db_query($t_sql);
		
		while($t_result_array = xtc_db_fetch_array($t_result))
		{
			$this->property_names_array[$t_result_array['properties_id']][$t_result_array['language_id']] = $t_result_array['properties_name'];
		}		
	}


	protected function truncate($p_string, array $p_params_array)
	{
		if(empty($p_params_array))
		{
			return $p_string;
		}
		
		$t_truncated_string = $p_string;
		
		if($p_params_array[0] > 0 && strlen_wrapper($p_string) > $p_params_array[0])
		{
			$etc = isset($p_params_array[1]) ? $p_params_array[1] : '';
			
			if(strlen($etc) >= 2 && substr($etc, 0, 1) == '"' && substr($etc, -1) == '"')
			{
				$etc = substr($etc, 1, -1);
			}
			
			$length = (int)$p_params_array[0];
			$length -= min($p_params_array[0], strlen_wrapper($etc));
			$t_truncated_string = preg_replace('/\s+?(\S+)?$/', '', substr_wrapper($t_truncated_string, 0, $length+1));
			
			return substr_wrapper($t_truncated_string, 0, $length) . $etc;
		}
		
		return $t_truncated_string;
	}
}