<?php
/* --------------------------------------------------------------
   AttributesAjaxHandler.inc.php 2016-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');
require_once(DIR_FS_CATALOG . 'gm/classes/GMAttributesCalculator.php');
require_once(DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');
		
class AttributesAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		return true;
	}

	function proceed()
	{
		$t_output_array = array();
		$t_enable_json_output = false;
		
		$t_action_request = $this->v_data_array['GET']['action'];

		$coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
		
		switch($t_action_request)
		{
			case 'calculate_price':
				$t_attributes = array();
				$t_tax_class_id = 0;
				$t_gm_vpe_data = array();

				$coo_xtc_price->showFrom_Attributes = false;

				if(xtc_has_product_attributes((int)$this->v_data_array['POST']['products_id']) 
				   && empty($this->v_data_array['POST']['id'])
				   && !isset($this->v_data_array['POST']['properties_values_ids']))
				{
					$coo_xtc_price->showFrom_Attributes = true;
				}

				if($coo_xtc_price->cStatus['customers_status_show_price'] == '0'
						&& $coo_xtc_price->gm_check_price_status((int)$this->v_data_array['POST']['products_id']) != 1)
				{
					$this->v_output_buffer .= $coo_xtc_price->xtcShowNote(0); // Kundengruppe kann keine Preise sehen
				}
				elseif(empty($this->v_data_array['POST']['products_id']) == false
								&& ($coo_xtc_price->gm_check_price_status((int)$this->v_data_array['POST']['products_id']) == 0
									|| ($coo_xtc_price->gm_check_price_status((int)$this->v_data_array['POST']['products_id']) == 2
										&& $coo_xtc_price->getPprice((int)$this->v_data_array['POST']['products_id']) > 0
										)
									)
						)
				{
					if(!empty($this->v_data_array['POST']['id']))
					{
						foreach($this->v_data_array['POST']['id'] as $key => $unit)
						{
							$t_attributes[] = array('option' => $key,
													'value' => $unit);

							$gm_get_vpe_data = xtc_db_query("SELECT
																products_vpe_id,
																gm_vpe_value
															FROM
																products_attributes
															WHERE
																products_id = '" . (int)$this->v_data_array['POST']['products_id'] . "'
																AND options_id = '" . (int)$key . "'
																AND options_values_id = '" . (int)$unit . "'
																AND products_vpe_id > 0
															AND gm_vpe_value > 0");
							if(xtc_db_num_rows($gm_get_vpe_data) == 1)
							{
								$t_gm_vpe_data = xtc_db_fetch_array($gm_get_vpe_data);
							}
						}
					}

					if($_SESSION['customers_status']['customers_status_show_price_tax'] != 0)
					{
						$get_tax_class_id = xtc_db_query("SELECT products_tax_class_id FROM products WHERE products_id = '" . (int)$this->v_data_array['POST']['products_id'] . "'");
						if(xtc_db_num_rows($get_tax_class_id) == 1)
						{
							$row = xtc_db_fetch_array($get_tax_class_id);
							$t_tax_class_id = $row['products_tax_class_id'];
						}
					}

					$GLOBALS['xtPrice']->showFrom_Attributes = $coo_xtc_price->showFrom_Attributes;
					$t_details_array = array();
					if(isset($_POST['properties_values_ids']))
					{
						$coo_properties_control = MainFactory::create_object('PropertiesControl');
						$t_products_properties_combis_id = $coo_properties_control->get_combis_id_by_value_ids_array($_POST['products_id'], $_POST['properties_values_ids']);
						
						if(empty($t_products_properties_combis_id))
						{
							$GLOBALS['xtPrice']->showFrom_Attributes = true;
							$t_cheapest_combi_array = $coo_properties_control->get_cheapest_combi($_POST['products_id'], $_SESSION['languages_id']);
							$t_products_properties_combis_id = $t_cheapest_combi_array['products_properties_combis_id'];
						}
						
						$coo_properties_data_agent = MainFactory::create_object('PropertiesDataAgent');
						$t_details_array = $coo_properties_data_agent->get_properties_combis_vpe_details($t_products_properties_combis_id, $_SESSION['languages_id']);
					}

					$gmAttrCalc = new GMAttributesCalculator($this->v_data_array['POST']['products_id'], $t_attributes, $t_tax_class_id, $t_products_properties_combis_id);

					$this->v_output_buffer .= $gmAttrCalc->calculate($this->v_data_array['POST']['products_qty'], true);

					if(isset($t_details_array['products_vpe_id']) && $t_details_array['products_vpe_id'] > 0)
					{
						$gm_vpe_price = $gmAttrCalc->calculate($this->v_data_array['POST']['products_qty'], false) / (double)$t_details_array['vpe_value'];
						$this->v_output_buffer .= '<br /><span class="gm_products_vpe products-vpe">' . $coo_xtc_price->xtcFormat($gm_vpe_price, true).TXT_PER.$t_details_array['products_vpe_name'] . '</span>';
					}
					elseif(empty($t_gm_vpe_data) == false)
					{
						$gm_vpe_price = $gmAttrCalc->calculate($this->v_data_array['POST']['products_qty'], false) / (double)$t_gm_vpe_data['gm_vpe_value'];
						$this->v_output_buffer .= '<br /><span class="gm_products_vpe products-vpe">' . $coo_xtc_price->xtcFormat($gm_vpe_price, true).TXT_PER.xtc_get_vpe_name($t_gm_vpe_data['products_vpe_id']) . '</span>';
					}
					else
					{
						$gm_get_vpe_data = xtc_db_query("SELECT
															products_vpe,
															products_vpe_value
														FROM
															products
														WHERE
															products_id = '" . (int)$this->v_data_array['POST']['products_id'] . "'
															AND products_vpe > 0
															AND products_vpe_value > 0
															AND products_vpe_status = '1'");
						if(xtc_db_num_rows($gm_get_vpe_data) == 1)
						{
							$t_gm_vpe_data = xtc_db_fetch_array($gm_get_vpe_data);
						}

						if(empty($t_gm_vpe_data) == false)
						{
							$gm_vpe_price = $gmAttrCalc->calculate($this->v_data_array['POST']['products_qty'], false) / (double)$t_gm_vpe_data['products_vpe_value'];
							$this->v_output_buffer .= '<br /><span class="gm_products_vpe products-vpe">' . $coo_xtc_price->xtcFormat($gm_vpe_price, true).TXT_PER.xtc_get_vpe_name($t_gm_vpe_data['products_vpe']) . '</span>';
						}
					}
				}
				else
				{
					// Send HMTL for link back to client (#refs 41576)
					$price_status = array();
					$price_status = $coo_xtc_price->gm_show_price_status($coo_xtc_price->gm_check_price_status((int)$this->v_data_array['POST']['products_id']), 1);
					
					if($price_status['formated'] == GM_SHOW_PRICE_ON_REQUEST) 
					{
						$t_contact_url = xtc_href_link(FILENAME_CONTENT, 'coID=7'); // Link to contact form. 
						$this->v_output_buffer .=  '<a href="' . $t_contact_url . '" class="price-on-request">' . GM_SHOW_PRICE_ON_REQUEST . '</a>'; 
					}
					else 
					{
						$this->v_output_buffer .= $price_status['formated']; 
					}
				}
				
				break;
				
			case 'attribute_images':
				$coo_content_view = MainFactory::create_object('AttributeImagesContentView');

				$options_ids = '';
				$values_ids  = '';
				
				if(isset($this->v_data_array['GET']['id']) && is_array($this->v_data_array['GET']['id']))
				{
					foreach($this->v_data_array['GET']['id'] as $optionId => $valueId)
					{
						$options_ids .= 'id[' . (int)$optionId . '],';
						$values_ids .= (int)$valueId . ',';
					}
				}
				elseif(isset($this->v_data_array['GET']['options_ids']) 
				       && isset($this->v_data_array['GET']['values_ids']))
				{
					$options_ids = $this->v_data_array['GET']['options_ids'];
					$values_ids  = $this->v_data_array['GET']['values_ids'];
				}

				$coo_content_view->set_('options_ids', $options_ids);
				$coo_content_view->set_('values_ids', $values_ids);
				$coo_content_view->set_('language_id', $_SESSION['languages_id']);
				$this->v_output_buffer = $coo_content_view->get_html();
				
				break;
			
			case 'calculate_weight':
				$gm_query = xtc_db_query("
										SELECT
											products_weight		AS weight
										FROM
											products
										WHERE
											products_id			= '" . (int)$this->v_data_array['POST']['products_id']	. "'
										");

				$gm_array = xtc_db_fetch_array($gm_query);
				if(!empty($this->v_data_array['POST']['id'])) {
					foreach($this->v_data_array['POST']['id'] as $key => $unit) {
						$gm_query = xtc_db_query("
												SELECT
													options_values_weight	AS weight,
													weight_prefix			AS prefix
												FROM
													products_attributes
												WHERE
													products_id				= '" . (int)$this->v_data_array['POST']['products_id']	. "'
												AND
													options_id				= '" . (int)$key								. "'
												AND
													options_values_id		= '" . (int)$unit							. "'
												");
						$row = xtc_db_fetch_array($gm_query);
						if($row['prefix'] == '-') {
							$gm_array['weight'] -= $row['weight'];
						} else {
							$gm_array['weight'] += $row['weight'];
						}
					}
				}

				$this->v_output_buffer = gm_prepare_number($gm_array['weight'], $coo_xtc_price->currencies[$coo_xtc_price->actualCurr]['decimal_point']);
				
				break;
				
			default:
				trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
				return false;
		}

		if($t_enable_json_output)
		{
			$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			$t_output_json = $coo_json->encode($t_output_array);

			$this->v_output_buffer = $t_output_json;
		}
		
		return true;
	}
}