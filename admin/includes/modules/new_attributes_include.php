<?php
/* --------------------------------------------------------------
   new_attributes_include.php 2016-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_attributes_functions); www.oscommerce.com 
   (c) 2003	 nextcommerce (new_attributes_include.php,v 1.11 2003/08/21); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: new_attributes_include.php 901 2005-04-29 10:32:14Z novalis $)

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contributions:
   New Attribute Manager v4b				Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
// include needed functions

require_once(DIR_FS_INC .'xtc_get_tax_rate.inc.php');
require_once(DIR_FS_INC .'xtc_get_tax_class_id.inc.php');
require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');
$xtPrice = new xtcPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);
?>

<tr>
	<td>
		<table>
			<tr>
				<td class="dataTableHeadingContent">
					<a href="products_attributes.php">
						<?php echo $adminMenuLang->get_text('BOX_PRODUCTS_ATTRIBUTES'); ?>
					</a>
				</td>
				<td class="dataTableHeadingContent">
					<?php echo $adminMenuLang->get_text('BOX_ATTRIBUTES_MANAGER'); ?>
				</td>
			</tr>
		</table>
	</td>
</tr>

<tr>
	<td colspan="8" style="padding: 0;">
		<input type="hidden" name="current_product_id" value="<?php echo $_POST['current_product_id']; ?>" />
		<input type="hidden" name="action" value="change" />
		<?php
		echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
		if ($cPath) {
			echo '<input type="hidden" name="cPathID" value="' . $cPath . '">';
		}
		?>
	</td>
</tr>

<?php if(!empty($_POST['copy_product_id'])){ ?>
	<tr>
		<td class="main" colspan="8">
			<div class="message_stack_container">
				<div class="alert alert-success"><?php echo GM_COPY_SUCCESSFUL; ?></div>
			</div>
		</td>
	</tr>
<?php } ?>
<tr>
	<td class="attributes-edit-wrapper multi-table-wrapper">
		
		<?php
		// Temp id for text input contribution.. I'll put them in a seperate array.
		$tempTextID = '1999043';
		
		// Lets get all of the possible options
		$query = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS." where products_options_id LIKE '%' AND language_id = '" . $_SESSION['languages_id'] . "'";
		$result = xtc_db_query($query);
		$matches = xtc_db_num_rows($result);
		
		$totalOptionCountQuery = 'SELECT * FROM ' . TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS;
		$totalOptionCountQueryResult = xtc_db_query($totalOptionCountQuery);
		$totalOptionCount = xtc_db_num_rows($totalOptionCountQueryResult);
		
		if ($matches) {
			while ($line = xtc_db_fetch_array($result)) {
				$current_product_option_name = $line['products_options_name'];
				$current_product_option_id = $line['products_options_id'];
				// Find all of the Current Option's Available Values
				$query2 = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." WHERE products_options_id = '" . $current_product_option_id . "' ORDER BY products_options_values_id DESC";
				$result2 = xtc_db_query($query2);
				$matches2 = xtc_db_num_rows($result2);
				
				if ($matches2) {
					
					// Print the Option Name
					echo '<table class="gx-compatibility-table">';
					echo '<tr class="dataTableHeadingRow">' .
					     '<td class="dataTableHeadingContent">' . $current_product_option_name . '</td>' .
					     '<td class="dataTableHeadingContent">' . SORT_ORDER . '</td>' .
					     '<td class="dataTableHeadingContent">' . ATTR_MODEL . '</td>' .
					     '<td class="dataTableHeadingContent">EAN</td>' .
					     '<td class="dataTableHeadingContent">' . ATTR_STOCK . '</td>' .
					     '<td class="dataTableHeadingContent">VPE</td>' .
					     '<td class="dataTableHeadingContent">' . ATTR_WEIGHT . ' - ' . ATTR_PREFIXWEIGHT . '</td>' .
					     '<td class="dataTableHeadingContent">' . ATTR_PRICE . ' - ' . ATTR_PREFIXPRICE . '</td>' .
					     '</tr>';
					
					
					$i = 0;
					while ($line = xtc_db_fetch_array($result2)) {
						$i++;
						$rowClass = rowClass($i);
						$current_value_id = $line['products_options_values_id'];
						$isSelected = checkAttribute($current_value_id, $_POST['current_product_id'], $current_product_option_id);
						
						$CHECKED = '';
						if ($matches2 < 0 || $isSelected)
						{
							if($isSelected) $CHECKED = ' checked="checked"';
							
							
							$gm_get_vpe = xtc_db_query("SELECT products_vpe_id, products_vpe_name FROM products_vpe WHERE language_id = '" . $_SESSION['languages_id'] . "'");
							$gm_vpe_data = array();
							while($gm_vpe = xtc_db_fetch_array($gm_get_vpe)) {
								$gm_vpe_data[] = array('ID' => $gm_vpe['products_vpe_id'], 'NAME' => $gm_vpe['products_vpe_name']);
							}
							
							$query3 = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = '" . $current_value_id . "' AND language_id = '" . $_SESSION['languages_id'] . "'";
							$result3 = xtc_db_query($query3);
							while($line = xtc_db_fetch_array($result3)) {
								$current_value_name = $line['products_options_values_name'];
								// Print the Current Value Name
								
								$disable = '';
								if(empty($CHECKED)){
									$disable = "disabled='true'";
								}
								
								echo '<tr  class="' . $rowClass . '">' .
								     '<td class="main">' .
								     '<input type="checkbox" name="optionValues[]" value="' . $current_value_id . '"' . $CHECKED . ' data-single_checkbox />' .
								     '&nbsp;&nbsp;' . $current_value_name . '&nbsp;&nbsp;' .
								     '</td>' .
								     '<td class="main" align="left">' .
								     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_sortorder" value="' . $sortorder . '" size="4" />' .
								     '</td>' .
								     '<td class="main" align="left">' .
								     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_model" value="' . $attribute_value_model . '" size="10" />' .
								     '</td>' .
								     '<td class="main" align="left">' .
								     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_gm_ean" value="' . $gm_attribute_ean . '" size="10" />' .
								     '</td>' .
								     '<td class="main" align="left">' .
								     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_stock" value="' . (double)$attribute_value_stock . '" size="4" />' .
								     '</td>' .
								     '<td class="main" align="left">' .
								     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_vpe_value" value="' . (double)$gm_attribute_vpe_value . '" size="4" /> ' .
								     '<select ' . $disable . ' name="' . $current_value_id . '_vpe_id">';
								if(empty($gm_attribute_vpe_id)){
									$gm_selected = ' selected="selected"';
								}else{
									$gm_selected = '';
								}
								echo '<option value="0"' . $gm_selected . '></option>';
								for($j = 0; $j < count($gm_vpe_data); $j++){
									if($gm_vpe_data[$j]['ID'] == $gm_attribute_vpe_id){
										$gm_selected = ' selected="selected"';
									}else{
										$gm_selected = '';
									}
									echo '<option value="' . $gm_vpe_data[$j]['ID'] .  '"' . $gm_selected . '>' . $gm_vpe_data[$j]['NAME'] . '</option>';
								}
								echo '</select>' .
								     '</td>' .
								     '<td class="main" align="left">' .
								     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_weight" value="' . $attribute_value_weight . '" size="10" /> ' .
								     '<select ' . $disable . ' name="' . $current_value_id . '_weight_prefix">' .
								     '<option value="+"' . $posCheck_weight . '>+</option>' .
								     '<option value="-"' . $negCheck_weight . '>-</option>' .
								     '</select>' .
								     '</td>';
								
								// brutto Admin
								if (PRICE_IS_BRUTTO=='true'){
									$attribute_value_price_calculate = $xtPrice->xtcFormat(xtc_round($attribute_value_price*((100+(xtc_get_tax_rate(xtc_get_tax_class_id($_POST['current_product_id']))))/100),PRICE_PRECISION), false);
								} else {
									$attribute_value_price_calculate = xtc_round($attribute_value_price, PRICE_PRECISION);
								}
								echo '<td class="main" align="left">' .
								     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_price" value="' . $attribute_value_price_calculate . '" size="10" />';
								// brutto Admin
								if (PRICE_IS_BRUTTO=='true'){
									echo TEXT_NETTO . '<strong>' . $xtPrice->xtcFormat(xtc_round($attribute_value_price,PRICE_PRECISION),true) . '</strong>';
								}
								
								echo ' <select ' . $disable . ' name="' . $current_value_id . '_prefix">' .
								     '<option value="+"' . $posCheck . '>+</option>' .
								     '<option value="-"' . $negCheck . '>-</option>' .
								     '</select>' .
								     '</td>' .
								     '</tr>';
								
								
								
								// Download function start
								if(strtoupper($current_product_option_name) == 'DOWNLOADS') {
									echo '<tr>' .
									     '<td colspan="2">' . xtc_draw_pull_down_menu($current_value_id . '_download_file', xtc_getDownloads(), $attribute_value_download_filename, $disable) . '</td>' .
									     '<td class="main" colspan="3">' .
									     '&nbsp;' . DL_COUNT .
									     ' <input ' . $disable . ' type="text" name="' . $current_value_id . '_download_count" value="' . $attribute_value_download_count . '">' .
									     '</td>' .
									     '<td class="main" colspan="3">' .
									     '&nbsp;' . DL_EXPIRE .
									     '<input ' . $disable . ' type="text" name="' . $current_value_id . '_download_expire" value="' . $attribute_value_download_expire . '">' .
									     '</td>' .
									     '</tr>';
								}
								
							}
							
							
							// Download function end
							if ($i == $matches2 ) {
								$i = 0;
							}
						}
					}
					
					if(!$isSelected || $matches2 > 0)
					{
						echo '<tr class="values">' .
						     '<td class="main gx-container" colspan="8">' .
						     '<div class="text-center add-padding-10"><a style="display:block; font-size: 20px;" class="addValues" data-product-id="' . $_POST['current_product_id'] . '" data-attributes-id="' . $current_product_option_id . '" href="" title="Load option values"><i class="fa fa-plus-square-o btn-icon"></i></a></div>' .
						     '<br>' .
						     '</td>' .
						     '</tr>';
					}
					echo '</table>';
					
				}
				
			}
		}
		?>
	</td>
</tr>
<tr>
	<td colspan="8" class="main">
		<div class="grid gx-container attributes-btn-wrapper">
			<div class="pull-right">
				<input type="submit"
				       class="btn btn-primary"
				       onClick="this.blur();"
				       value="<?php echo BUTTON_SAVE; ?>">
			</div>
			<a href="new_attributes.php" class="btn float_right add-margin-right-5" onClick="this.blur()"><?php echo BUTTON_CANCEL; ?></a>
		</div>
		<!--<br/>-->
		<?php
		//	echo xtc_button(BUTTON_SAVE, 'submit', 'style="float:right; margin:5px"');
		//	echo xtc_button_link(BUTTON_CANCEL, FILENAME_NEW_ATTRIBUTES, 'style="float:right; margin:5px; font-size:12px"');
		//?>
	</td>
</tr>



<script type="text/javascript">
	$(document).ready(function() {
		
		$('.addValues').on('click', function (e) {
			e.preventDefault();
			var current_value_id  = $(this).attr('data-attributes-id');
			var product_id = $(this).attr('data-product-id');
			
			var table = $(this).closest('table');
			var that = $(this);
			$(this).closest('tr.values').fadeToggle( "fast");
			
			
			$.get( 'admin.php?do=NewAttributes/LoadAttributeValues&product_id='+product_id+'&atttributesId='+current_value_id, function( data ) {
				table.append(data);
				$('tr.attributes-odd').fadeIn("fast");
			});
			
		});
		
		$('#SUBMIT_ATTRIBUTES').on('click', 'input:checkbox', function () {
			var $tr		= $(this).closest('tr'),
				$nextTr	= $tr.next('tr:not([class^=attributes])');
			$tr
				.find('input:not(:checkbox), select')
				.attr('disabled', !$(this).is(':checked'));
			$nextTr
				.find('input[type=text], select')
				.attr('disabled', !$(this).is(':checked'));
		});
	});
</script>
