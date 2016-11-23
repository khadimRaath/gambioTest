<?php
/* --------------------------------------------------------------
   group_prices.php 2015-02-19 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(based on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35); www.oscommerce.com
   (c) 2003	 nextcommerce (group_prices.php,v 1.16 2003/08/21); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: group_prices.php 1307 2005-10-14 10:36:37Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------
   based on Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');

require (DIR_FS_CATALOG.DIR_WS_CLASSES.'xtcPrice.php');
$xtPrice = new xtcPrice(DEFAULT_CURRENCY, $_SESSION['customers_status']['customers_status_id']);

$i = 0;
$group_query = xtc_db_query("SELECT
                                   customers_status_image,
                                   customers_status_id,
                                   customers_status_name
                               FROM
                                   ".TABLE_CUSTOMERS_STATUS."
                               WHERE
                                   language_id = '".$_SESSION['languages_id']."' AND customers_status_id != '0'");
while ($group_values = xtc_db_fetch_array($group_query)) {
	// load data into array
	$i ++;
	$group_data[$i] = array ('STATUS_NAME' => $group_values['customers_status_name'], 'STATUS_IMAGE' => $group_values['customers_status_image'], 'STATUS_ID' => $group_values['customers_status_id']);
}
?>  
<table width="100%"><tr><td style="border-bottom: thin dashed Gray;">&nbsp;</td></tr></table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><td><span class="main" style="padding-left: 10px;"><?php echo HEADING_PRICES_OPTIONS; ?></span></td></tr>
</table>
<br>
<table width="100%" border="0" bgcolor="#f3f3f3" style="border: 1px solid; border-color: #cccccc;">
          <tr>
            <td width="50%" class="main"><?php echo TEXT_PRODUCTS_PRICE; ?></td>
<?php


// calculate brutto price for display

if (PRICE_IS_BRUTTO == 'true') {
	$products_price = xtc_round($pInfo->products_price * ((100 + xtc_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);

} else {
	$products_price = xtc_round($pInfo->products_price, PRICE_PRECISION);
}
?>
            <td width="50%" class="main"><?php echo xtc_draw_input_field('products_price', $products_price); ?>
<?php


if (PRICE_IS_BRUTTO == 'true') {
	// BOF GM_MOD:
	echo '&nbsp;'.TEXT_NETTO.'<b>'.$xtPrice->xtcFormat($pInfo->products_price, false).'</b>  ';
}
?>
</td>
          </tr>
<?php


for ($col = 0, $n = sizeof($group_data); $col < $n +1; $col ++) {
	if ($group_data[$col]['STATUS_NAME'] != '') {
?>
          <tr>
            <td style="border-top: 1px solid; border-color: #cccccc;" valign="top" class="main"><?php echo $group_data[$col]['STATUS_NAME']; ?></td>
<?php


		if (PRICE_IS_BRUTTO == 'true') {
			$products_price = xtc_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id) * ((100 + xtc_get_tax_rate($pInfo->products_tax_class_id)) / 100), PRICE_PRECISION);

		} else {
			$products_price = xtc_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id), PRICE_PRECISION);
		}
?>
            <td style="border-top: 1px solid; border-color: #cccccc;" class="main"><?php


		echo xtc_draw_input_field('products_price_'.$group_data[$col]['STATUS_ID'], $products_price);

		if (PRICE_IS_BRUTTO == 'true' && get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id) != '0') {

			echo TEXT_NETTO.'<b>'.$xtPrice->xtcFormat(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id), false).'</b>  ';

		}

		echo ' '.TXT_STAFFELPREIS;
?> <img onMouseOver="javascript:this.style.cursor='hand';" src="html/assets/images/legacy/arrow_down.gif" height="12" width="12" onClick="javascript:toggleBox('staffel_<?php echo $group_data[$col]['STATUS_ID']; ?>');">
		
		<div id="staffel_<?php echo $group_data[$col]['STATUS_ID']; ?>" class="longDescription"><br><?php

		// ok, lets check if there is already a staffelpreis
		$staffel_query = xtc_db_query("SELECT
				                                         products_id,
				                                         quantity,
				                                         personal_offer
				                                     FROM
				                                         personal_offers_by_customers_status_".$group_data[$col]['STATUS_ID']."
				                                     WHERE
				                                         products_id = '".$pInfo->products_id."' AND quantity != 1
				                                     ORDER BY quantity ASC");
		echo '<div class="old_personal_offers">';
		while ($staffel_values = xtc_db_fetch_array($staffel_query))
		{
			if (PRICE_IS_BRUTTO == 'true') {
				$tax = xtc_get_tax_rate($pInfo->products_tax_class_id);

				$products_price = xtc_round($staffel_values['personal_offer'] * ((100 + $tax) / 100), PRICE_PRECISION);

			} else {
				$products_price = xtc_round($staffel_values['personal_offer'], PRICE_PRECISION);
			}
			
			echo '<div class="old_personal_offer">';
			echo TXT_STK;
			echo xtc_draw_small_input_field('disabled_quantity', (double)$staffel_values['quantity'], 'disabled="disabled"');
			echo xtc_draw_hidden_field('products_quantity_staffel_'.$group_data[$col]['STATUS_ID'].'[]', (double)$staffel_values['quantity']);
			echo TXT_PRICE;
			echo xtc_draw_input_field('products_price_staffel_'.$group_data[$col]['STATUS_ID'].'[]', $products_price);
			
			if (PRICE_IS_BRUTTO == 'true') {
				echo '<span style="display:inline-block;min-width:130px"> ' . TEXT_NETTO.'<b>'.$xtPrice->xtcFormat($staffel_values['personal_offer'], false).'</b></span>';
			}			
			?>
			<a style="display:inline-block" class="button delete_personal_offer" href="#"><?php echo BUTTON_DELETE; ?></a>
			<?php
			echo '</div>';
		}
		echo '</div>';
		
		echo '<div class="new_personal_offer">';
			echo TXT_STK;
			echo xtc_draw_small_input_field('products_quantity_staffel_'.$group_data[$col]['STATUS_ID'].'[]', 0);
			echo TXT_PRICE;
			echo xtc_draw_input_field('products_price_staffel_'.$group_data[$col]['STATUS_ID'].'[]', 0);		
			echo xtc_draw_separator('pixel_trans.gif', '10', '10');
			echo '<br />';
		echo '</div>';
		?>			
			<div class="added_personal_offers"></div>
			<a href="#" class="button add_personal_offer"><?php echo BUTTON_ADD; ?></a>
			</td>
          </tr>
<?php


	}
}
?>
			<script type="text/javascript">
				$(document).ready(function()
				{
					$('.delete_personal_offer').live('click', function()
					{
						var t_quantity = $(this).closest('.old_personal_offer').find('input[name^="products_quantity_staffel_"]').val();
						var t_group_id = '' + $(this).closest('.longDescription').attr('id').replace('staffel_', '');
						
						$(this).closest('.longDescription').find('.added_personal_offers').append('<input type="hidden" name="delete_products_quantity_staffel_' + t_group_id + '[]" value="' + t_quantity + '" />');
						
						$(this).closest('.old_personal_offer').remove();

						return false;
					});

					$('.add_personal_offer').live('click', function()
					{
						$(this).closest('.longDescription').find('.added_personal_offers').append($(this).closest('.longDescription').find('.new_personal_offer').html());
						$(this).closest('.longDescription').find('.added_personal_offers input[name^="products_quantity_staffel_"]:last').val('');
						$(this).closest('.longDescription').find('.added_personal_offers input[name^="products_price_staffel_"]:last').val('0');

						return false;
					});

				});
			</script>
		</div>
          <tr>
            <td style="border-top: 1px solid; border-color: #cccccc;" class="main"><?php echo TEXT_PRODUCTS_DISCOUNT_ALLOWED; ?></td>
            <td style="border-top: 1px solid; border-color: #cccccc;" class="main"><?php echo xtc_draw_input_field('products_discount_allowed', $pInfo->products_discount_allowed); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></td>
            <td class="main"><?php echo xtc_draw_pull_down_menu('products_tax_class_id', $tax_class_array, (empty($_GET['pID'])) ? 1 : $pInfo->products_tax_class_id); ?></td>
          </tr>
        </table>


<script type="text/javascript">
	$(document).ready(function(){
		$("input[name=products_model]").bind("change", function(){
			if($(this).val().match(/GIFT_/g)){
				$("select[name=products_tax_class_id]").val(0);
				$("select[name=products_tax_class_id]").attr("disabled", "disabled");
				$("select[name=products_tax_class_id]").parent().append("<span style='display: inline-block; margin: 0 0 0 20px; color: red;'><?php echo TEXT_NO_TAX_RATE_BY_GIFT; ?></span>");
			}else if($("select[name=products_tax_class_id]").attr("disabled")){
				$("select[name=products_tax_class_id]").removeAttr("disabled");
				$("select[name=products_tax_class_id]").parent().find("span").remove();
			}			
		});
	});
</script>