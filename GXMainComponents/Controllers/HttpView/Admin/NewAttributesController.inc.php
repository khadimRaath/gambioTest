<?php
/* --------------------------------------------------------------
   NewAttributesController.inc.php 2016-05-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');


/**
 * Class NewAttributesController
 *
 * @category System
 * @package  AdminHttpViewControllers
 */
class NewAttributesController extends AdminHttpViewController
{
	/**
	 * Load Attribute Values
	 *
	 * @return \HttpControllerResponse
	 */
	public function actionLoadAttributeValues()
	{
		$outputPlain               = '';
		$current_product_option_id = (int)$this->_getQueryParameter('atttributesId');
		$current_product_id        = (int)$this->_getQueryParameter('product_id');
		
		// Find all of the Current Option's Available Values
		$query2   = "SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS
		            . " WHERE products_options_id = '" . $current_product_option_id
		            . "' ORDER BY products_options_values_id DESC";
		$result2  = xtc_db_query($query2);
		$matches2 = xtc_db_num_rows($result2);
		
		// Get the product option data. 
		$query = "SELECT products_options_name FROM ".TABLE_PRODUCTS_OPTIONS." where products_options_id = " . $current_product_option_id . " AND language_id = '" . (int)$_SESSION['languages_id'] . "'";
		$productOptionResult = xtc_db_query($query);
		$productOptionData = xtc_db_fetch_array($productOptionResult);
		
		// Get required translations.
		$newAttributesLang = MainFactory::create('LanguageTextManager', 'new_attributes', $_SESSION['language_id']);
		
		if($matches2)
		{
			$i = 0;
			while($line = xtc_db_fetch_array($result2))
			{
				$i++;
				$current_value_id = $line['products_options_values_id'];
				$isSelected       = checkAttribute($current_value_id, $current_product_id, $current_product_option_id);
				$CHECKED          = '';
				if($isSelected)
				{
					$CHECKED = ' checked="checked"';
				}
				
				if(!$isSelected)
				{
					$gm_get_vpe  = xtc_db_query("SELECT products_vpe_id, products_vpe_name FROM products_vpe WHERE language_id = '"
					                            . (int)$_SESSION['languages_id'] . "'");
					$gm_vpe_data = array();
					while($gm_vpe = xtc_db_fetch_array($gm_get_vpe))
					{
						$gm_vpe_data[] = array(
							'ID'   => $gm_vpe['products_vpe_id'],
							'NAME' => $gm_vpe['products_vpe_name']
						);
					}
					
					$query3  = "SELECT * FROM " . TABLE_PRODUCTS_OPTIONS_VALUES
					           . " WHERE products_options_values_id = '" . $current_value_id . "' AND language_id = '"
					           . (int)$_SESSION['languages_id'] . "'";
					$result3 = xtc_db_query($query3);
					while($line = xtc_db_fetch_array($result3))
					{
						$current_value_name = $line['products_options_values_name'];
						// Print the Current Value Name
						$disable = '';
						if(empty($CHECKED))
						{
							$disable = "disabled='true'";
						}
						
						$outputPlain .= '<tr class="attributes-odd" style="display: none;">' . '<td class="main">'
						                . '<input type="checkbox" name="optionValues[]" value="' . $current_value_id
						                . '"' . $CHECKED . ' data-single_checkbox />' . '&nbsp;&nbsp;'
						                . $current_value_name . '&nbsp;&nbsp;' . '</td>'
						                . '<td class="main" align="left">' . '<input ' . $disable
						                . ' type="text" name="' . $current_value_id . '_sortorder" value="' . $sortorder
						                . '" size="4" />' . '</td>' . '<td class="main" align="left">' . '<input '
						                . $disable . ' type="text" name="' . $current_value_id . '_model" value="'
						                . $attribute_value_model . '" size="10" />' . '</td>'
						                . '<td class="main" align="left">' . '<input ' . $disable
						                . ' type="text" name="' . $current_value_id . '_gm_ean" value="'
						                . $gm_attribute_ean . '" size="10" />' . '</td>'
						                . '<td class="main" align="left">' . '<input ' . $disable
						                . ' type="text" name="' . $current_value_id . '_stock" value="'
						                . (double)$attribute_value_stock . '" size="4" />' . '</td>'
						                . '<td class="main" align="left">' . '<input ' . $disable
						                . ' type="text" name="' . $current_value_id . '_vpe_value" value="'
						                . (double)$gm_attribute_vpe_value . '" size="4" /> ' . '<select ' . $disable
						                . ' name="' . $current_value_id . '_vpe_id">';
						if(empty($gm_attribute_vpe_id))
						{
							$gm_selected = ' selected="selected"';
						}
						else
						{
							$gm_selected = '';
						}
						$outputPlain .= '<option value="0"' . $gm_selected . '></option>';
						for($j = 0; $j < count($gm_vpe_data); $j++)
						{
							if($gm_vpe_data[$j]['ID'] == $gm_attribute_vpe_id)
							{
								$gm_selected = ' selected="selected"';
							}
							else
							{
								$gm_selected = '';
							}
							$outputPlain .= '<option value="' . $gm_vpe_data[$j]['ID'] . '"' . $gm_selected . '>'
							                . $gm_vpe_data[$j]['NAME'] . '</option>';
						}
						
						$outputPlain .= '</select>' . '</td>' . '<td class="main" align="left">' . '<input ' . $disable
						                . ' type="text" name="' . $current_value_id . '_weight" value="'
						                . $attribute_value_weight . '" size="10" /> ' . '<select ' . $disable
						                . ' name="' . $current_value_id . '_weight_prefix">' . '<option value="+"'
						                . $posCheck_weight . '>+</option>' . '<option value="-"' . $negCheck_weight
						                . '>-</option>' . '</select>' . '</td>' . '<td class="main" align="left">'
						                . '<input ' . $disable . ' type="text" name="' . $current_value_id
						                . '_price" value="' . $attribute_value_price_calculate . '" size="10" />'
										. ' <select ' . $disable . ' name="' . $current_value_id . '_prefix">'
						                . '<option value="+"' . $posCheck . '>+</option>' . '<option value="-"'
						                . $negCheck . '>-</option>' . '</select>' . '</td>' . '</tr>';
						
						// Download function start
						if(strtoupper($productOptionData['products_options_name']) == 'DOWNLOADS') {
							$outputPlain .= '<tr>' .
							     '<td colspan="2">' . xtc_draw_pull_down_menu($current_value_id . '_download_file', xtc_getDownloads(), $attribute_value_download_filename, $disable) . '</td>' .
							     '<td class="main" colspan="3">' .
							     '&nbsp;' . $newAttributesLang->get_text('DL_COUNT') .
							     ' <input ' . $disable . ' type="text" name="' . $current_value_id . '_download_count" value="' . $attribute_value_download_count . '">' .
							     '</td>' .
							     '<td class="main" colspan="3">' .
							     '&nbsp;' . $newAttributesLang->get_text('DL_EXPIRE') .
							     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_download_expire" value="' . $attribute_value_download_expire . '">' .
							     '</td>' .
							     '</tr>';
						}
					}
					
					// Download function end
					if($i == $matches2)
					{
						$i = 0;
					}
				}
			}
		}
		
		return MainFactory::create('HttpControllerResponse', $outputPlain);
	}
}
