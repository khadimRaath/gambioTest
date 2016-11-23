<?php
/* --------------------------------------------------------------
   gm_pdf_bulk.php 25.05.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/
?>
<div class="gx-compatibility-table">
	<form id="gm_pdf_form" class="remove-margin remove-padding">
		<table border="0" width="100%" cellspacing="0" cellpadding="0" id="gm_table" class="remove-margin">

			<tr>
				<td>
					<label for="max_amount_invoices"><?php echo TITLE_MAX_AMOUNT_INVOICES_BULK_PDF; ?></label>
				</td>
				<td>
					<input type="text"
					       name="max_amount_invoices"
					       value="<?php echo gm_get_conf('GM_PDF_MAX_AMOUNT_INVOICES_BULK_PDF') ?>" id="max_amount_invoices">
				</td>
			</tr>
			<tr>
				<td>
					<label for="max_amount_packing_slips"><?php echo TITLE_MAX_AMOUNT_PACKING_SLIP_BULK_PDF; ?></label>
				</td>
				<td>
					<input type="text"
					       name="max_amount_packing_slips"
					       value="<?php echo gm_get_conf('GM_PDF_MAX_AMOUNT_PACKING_SLIPS_BULK_PDF') ?>"
					       id="max_amount_packing_slips">
				</td>
			</tr>

		</table>
		<div style="display: block; margin-top: 12px; height: 30px;">
			<input class="btn btn-primary pull-right remove-margin"
			       type="button"
			       value="<?php echo BUTTON_SAVE; ?>"
			       onClick="gm_fadeout_boxes('gm_status');gm_update_boxes('<?php echo xtc_href_link('gm_pdf_action.php',
			                                                                                        'action=gm_pdf_bulk_update'
			                                                                                        . '&page_token='
			                                                                                        . $_SESSION['coo_page_token']->generate_token()); ?>', 'gm_status')">
			<span id="gm_status" class="pull-right add-padding-10" style="height:20px"></span>
		</div>
	</form>
</div>

