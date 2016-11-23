<?php
/* --------------------------------------------------------------
   gm_pdf_protection.php 2015-09-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>
<div class="gx-compatibility-table">
	<!-- <table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
		<tr>
			<td valign="top" align="left" class="main">
				<h2><?php echo MENU_TITLE_INVOICING; ?></h2>
			</td>
		</tr>
	</table> -->
	<br />
	<form id="gm_pdf_form" class="remove-margin remove-padding">
		<table border="0" width="100%" cellspacing="0" cellpadding="2" id="gm_table" class="remove-margin" data-gx-widget="checkbox">
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_ORDER_STATUS_INVOICE; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<?php echo xtc_draw_pull_down_menu('GM_PDF_ORDER_STATUS_INVOICE', $t_order_status_list, gm_get_conf('GM_PDF_ORDER_STATUS_INVOICE', 'ASSOC', true), 'id="GM_PDF_ORDER_STATUS_INVOICE" style="width:200px;"'); ?>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_ORDER_STATUS_INVOICE_MAIL; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<?php echo xtc_draw_pull_down_menu('GM_PDF_ORDER_STATUS_INVOICE_MAIL', $t_order_status_list, gm_get_conf('GM_PDF_ORDER_STATUS_INVOICE_MAIL', 'ASSOC', true), 'id="GM_PDF_ORDER_STATUS_INVOICE_MAIL" style="width:200px;"'); ?>
				</td>
			</tr>
			<tr class="dataTableRow">
				<td valign="top" align="left" class="main dataTableContent configuration-label" style="width: 50%;">
					<?php echo GM_PDF_ORDER_STATUS_INVOICE_DATE; ?>
				</td>
				<td valign="top" align="left" class="main dataTableContent" style="width: 50%;">
					<?php
					echo xtc_draw_pull_down_menu('GM_PDF_ORDER_STATUS_INVOICE_DATE', $t_order_status_date, gm_get_conf('GM_PDF_ORDER_STATUS_INVOICE_DATE', 'ASSOC', true), 'id="GM_PDF_ORDER_STATUS_INVOICE_DATE" style="width:200px;"');
					?>
				</td>
			</tr>
		</table>
		<div style="display: block; margin-top: 12px; height: 30px;">
			<input class="btn btn-primary pull-right remove-margin" type="button" value="<?php echo BUTTON_SAVE;?>" onClick="gm_hide_boxes('gm_color_box');gm_fadeout_boxes('gm_status');gm_update_boxes('<?php echo xtc_href_link('gm_pdf_action.php', 'action=gm_pdf_update&page_token=' . $_SESSION['coo_page_token']->generate_token()); ?>', 'gm_status')">
			<span id="gm_status" class="pull-right add-padding-10" style="height:20px"></span>
		</div>
	</form>
</div>
