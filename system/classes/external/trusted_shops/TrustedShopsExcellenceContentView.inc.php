<?php
/* --------------------------------------------------------------
  TrustedShopsExcellenceContentView.inc.php 2016-07-29
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

class TrustedShopsExcellenceContentView extends ContentView
{
	protected $order;

	function TrustedShopsExcellenceContentView($order)
	{
		parent::__construct();
		$this->order = $order;
		$this->set_content_template('module/ts_excellence.html');
		$this->set_flat_assigns(true);
	}

	function get_html()
	{
		$order = $this->order;
		$service = new GMTSService();
		$tsid = $service->findExcellenceID($_SESSION['language_code']);
		if($tsid !== false)
		{
			$trusted_amount = round($order->info['total'], 2);
			if(isset($_SESSION['ts_excellence']))
			{
				$this->set_content_data('has_protection', '1');
				unset($_SESSION['ts_excellence']['from_protection']);
			}
			$product = $service->findProtectionProduct($tsid, $trusted_amount, $order->info['currency']);
			if($product === false)
			{
				$t_html_output = '<!-- TS Buyer Protection Excellence product not found ' . $tsid . ' ' . $trusted_amount . ' ' . $order->info['currency'] . ' -->';
			}
			else
			{
				$this->set_content_data('TSID', $tsid);
				$this->set_content_data('total', round($order->info['total'], 2));
				$this->set_content_data('add_protection_action', GM_HTTP_SERVER.DIR_WS_CATALOG.'trusted_shops_protection.php');
				$this->set_content_data('add_protection_checked', isset($_SESSION['ts_excellence']['application_number']));
				$this->set_content_data('trusted_amount', number_format($trusted_amount, 2, ',', '') . ' ' . $order->info['currency']);
				$this->set_content_data('protected_amount', number_format($product['protectedamount'], 2, ',', '') . ' ' . $order->info['currency']);
				$this->set_content_data('protection_fee', number_format($product['grossfee'], 2, ',', '') . ' ' . $order->info['currency']);
				$this->set_content_data('incl_tax', $_SESSION['customers_status']['customers_status_show_price_tax'] == 1);
				$t_html_output = $this->build_html();
				$t_html_output = strtr($t_html_output, array('{TSID}' => $tsid));
			}
		}
		else
		{
			$t_html_output = '<!-- TS Buyer Protection Excellence unavailable -->';
		}
		return $t_html_output;
	}

}
