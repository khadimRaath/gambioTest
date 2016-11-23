<?php
/* --------------------------------------------------------------
	PayoneCheckoutSuccessExtender.inc.php 2016-03-03
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PayoneCheckoutSuccessExtender extends PayoneCheckoutSuccessExtender_parent {
	function proceed() {
		parent::proceed();
		$payone = new GMPayOne();
		$orders_id = $this->v_data_array['orders_id'];
		$order = $this->v_data_array['coo_order'];
		$clearing_data = $payone->getClearingData($orders_id);

		if(/* $order->info['payment_method'] == 'payone_prepay' && */ $clearing_data !== false) {
			ob_start();
			include DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/checkout_success_payone_clearing.html';
			$block = ob_get_clean();
			$block = $payone->replaceTextPlaceholders($block);
			$this->v_output_buffer['PAYONE_BLOCK'] = $block;
		}

		if($order->info['payment_method'] == 'payone_elv' && empty($_SESSION['payone_elv_sepa_mandate_id']) == false)
		{
			$show_pdf_link = isset($_SESSION['payone_elv_sepa_download_pdf']) && $_SESSION['payone_elv_sepa_download_pdf'] == 'true';
			if($show_pdf_link)
			{
				$mandate_file = $payone->retrieveSepaMandate($_SESSION['payone_elv_sepa_mandate_id']);
				if($mandate_file !== false)
				{
					$block = '<div class="p1_sepa_download">';
					$block .= $payone->get_text('download_mandate_here');
					$block .= '<br><a target="_blank" href="'.GM_HTTP_SERVER.DIR_WS_DOWNLOAD_PUBLIC.$mandate_file.'">'.$payone->get_text('mandate_pdf').'</a>';
					$block .= '</div>';
					$this->html_output_array[] = $block;
				}
				else
				{
					$this->html_output_array[] = '<!-- SEPA mandate download unavailable -->';
				}
			}
		}
	}
}