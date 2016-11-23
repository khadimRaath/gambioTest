<?php
/* --------------------------------------------------------------
  InvoicePDFOrderExtender.inc.php 2016-03-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class InvoicePDFOrderExtender extends InvoicePDFOrderExtender_parent
{
	function proceed()
	{
		ob_end_clean();
		$t_order_id = (int)$_GET['oID'];

		$t_invoices_files_array = glob(DIR_FS_CATALOG . 'export/invoice/' . $t_order_id . '_*.pdf');
		$t_packing_slips_files_array = glob(DIR_FS_CATALOG . 'export/packingslip/' . $t_order_id . '_*.pdf');
		
		$query = 'SELECT gm_orders_code, gm_packings_code FROM orders WHERE orders_id = ' . $t_order_id;
		$result = xtc_db_query($query);
		$row = xtc_db_fetch_array($result);
		?>
		<div class="invoice-packingslip hidden">
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th>
							<?php echo INVOICE_CREATED; ?>
						</th>
						<th>
							<?php echo PACKINGSLIP_CREATED; ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-container">
						<td class="invoice-container" valign="top">
							<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTableRow invoice" 
								   data-gx-controller="orders/orders_pdf_delete"
								   data-orders_pdf_delete-type="invoice"
								   data-gx-extension="visibility_switcher"
								   data-visibility_switcher-selections=".action-icons">
								<thead>
									<tr>
										<th><?php echo TITLE_ORDERS_BILLING_CODE; ?></th>
										<th><?php echo TEXT_DATE; ?></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								<?php
								if(is_array($t_invoices_files_array) && count($t_invoices_files_array) > 0)
								{
									foreach($t_invoices_files_array as $t_file)
									{
										$t_file_parts = explode('__', basename($t_file));

										?>
										<tr class="invoice visibility_switcher">
											<td>
												<?php echo $row['gm_orders_code'] ?>
											</td>
											<td>
												<?php echo xtc_datetime_short($t_file_parts[2]); ?>
											</td>
											<td>
												<div class="action-icons">
													<a href="request_port.php?module=OrderAdmin&action=showPdf&type=invoice&file=<?php echo basename($t_file); ?>" target="_blank" class="icon-container"><i class="fa fa-eye"></i></a>
													<a href="request_port.php?module=OrderAdmin&action=downloadPdf&type=invoice&file=<?php echo basename($t_file); ?>" target="_blank" class="icon-container"><i class="fa fa-download"></i></a>
													<a href="#" rel="<?php echo basename($t_file); ?>" class="delete_pdf icon-container"><i class="fa fa-trash-o"></i></a>
												</div>
											</td>
										</tr>
										<?php
									}
									?>
									<tr style="display: none;" class="invoice">
										<td>
											<?php echo NO_INVOICE_CREATED; ?>
										</td>
									</tr>
									<?php
								}
								else
								{
									?>
									<tr class="invoice">
										<td>
											&mdash;
										</td>
									</tr>
									<?php
								}
								?>
								</tbody>
							</table>
						</td>
						<td class="packingslip-container" valign="top">
							<table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTableRow packingslip"
								   data-gx-controller="orders/orders_pdf_delete"
								   data-orders_pdf_delete-type="packingslip"
								   data-gx-extension="visibility_switcher"
								   data-visibility_switcher-selections=".action-icons">
								<thead>
									<tr>
										<th><?php echo TITLE_PACKINGS_BILLING_CODE; ?></th>
										<th><?php echo TEXT_DATE; ?></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								<?php
								if(is_array($t_packing_slips_files_array) && count($t_packing_slips_files_array) > 0)
								{
									foreach($t_packing_slips_files_array as $t_file)
									{
										$t_file_parts = explode('__', basename($t_file));

										?>
										<tr class="packingslip visibility_switcher">
											<td>
												<?php echo $row['gm_packings_code'] ?>
											</td>
											<td>
												<?php echo xtc_datetime_short($t_file_parts[1]); ?>
											</td>
											<td>
												<div class="action-icons">
													<a href="request_port.php?module=OrderAdmin&action=showPdf&type=packingslip&file=<?php echo basename($t_file); ?>" target="_blank" class="icon-container"><i class="fa fa-eye"></i></a>
													<a href="request_port.php?module=OrderAdmin&action=downloadPdf&type=packingslip&file=<?php echo basename($t_file); ?>" target="_blank" class="icon-container"><i class="fa fa-download"></i></a>
													<a href="#" rel="<?php echo basename($t_file); ?>" class="delete_pdf icon-container"><i class="fa fa-trash-o"></i></a>
												</div>
											</td>
										</tr>
										<?php
									}
									?>
									<tr style="display: none;" class="packingslip">
										<td>
											<?PHP echo NO_PACKINGSLIP_CREATED; ?>
										</td>
									</tr>
									<?php
								}
								else
								{
									?>
									<tr class="packingslip">
										<td>
											&mdash;
										</td>
									</tr>
									<?php
								}
								?>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		ob_start();
		$this->addContent();
		parent::proceed();
	}
}