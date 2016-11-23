<?php
/* --------------------------------------------------------------
  BillSafeContentView.inc.php 2014-04-04 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project (earlier name of osCommerce)
  (c) 2002-2003 osCommerce (account.php,v 1.59 2003/05/19); www.oscommerce.com
  (c) 2003      nextcommerce (account.php,v 1.12 2003/08/17); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: account.php 1124 2005-07-28 08:50:04Z mz $)

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

class BillSafeContentView extends ContentView
{
	protected $customer_id;
	protected $languages_id;
	protected $tracking_data_array;
	protected $coo_product;
	protected $coo_message_stack;
	protected $billsafe_token;
	protected $layerform_action;
	protected $lpg_close_url;
	protected $layerform_button;
	protected $sandbox_mode;
	protected $product;
	protected $main_content;
	protected $request_method;
	protected $current_payment;
	protected $layered_payment_gateway;
	protected $token;
	protected $process;
	protected $mode;

	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('/module/checkout_billsafe.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		$coo_bs = MainFactory::create_object('GMBillSafe', array($this->current_payment));
		$coo_langtext_bs = MainFactory::create_object('LanguageTextManager', array('billsafe', $this->languages_id));
		$this->product = $coo_bs->getSubmodule(); // invoice|installment

		$main_content = '<!-- no content -->';

		if($this->request_method == 'POST' && !empty($this->layered_payment_gateway))
		{
			echo '<html><div id="BillSAFE_Token">'.$this->billsafe_token.'</div></html>';
			xtc_db_close();
			exit;
		}

		if(isset($this->mode) && $this->mode == 'layer')
		{
			// pass
		}
		else if(isset($this->token) && $this->token == $this->billsafe_token)
		{
			$sid = session_name() . '=' . session_id();

			if(strtolower(constant('MODULE_PAYMENT_'.strtoupper($this->current_payment).'_LAYER')) == 'true' && !isset($this->process))
			{
				$process_url = GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_billsafe.php'.'?token='.$this->token.'&process=1&'.$sid;
				echo "<html>\n<body>\n<script>\nif(top.lpg) {\n ".$jslog." top.lpg.close('".$process_url."');\n }\n</script>\n</body>\n</html>\n";
				flush();
				xtc_db_close();
				exit;
			}

			$tres = $coo_bs->getTransactionResult($this->token);

			if(isset($tres['ack']) && $tres['ack'] == 'OK')
			{
				if(isset($tres['status']) && $tres['status'] == 'ACCEPTED')
				{
					// OK, finalize order
					$coo_bs->saveTransactionId($_SESSION['tmp_oID'], $tres['transactionId']);
					xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_process.php?'.$sid);
				}
				else if(isset($tres['status']) && $tres['status'] == 'DECLINED')
				{
					$coo_bs->_log("Transaction declined: ".$tres['declineReason']['code'].' '.$tres['declineReason']['message'] .' | token '.$this->billsafe_token.
						' orders_id '. $_SESSION['tmp_oID']);
					$_SESSION['billsafe_3_error'] = $tres['declineReason']['buyerMessage'];
					unset($_SESSION['payment']);
					$coo_bs->markOrderAsAbortedOrDeclined($_SESSION['tmp_oID'], $tres['declineReason']['code'].' '.$tres['declineReason']['message']);
					unset($_SESSION['tmp_oID']);
					xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.'checkout_payment.php?payment_error='.$this->current_payment.'&'.$sid);
				}
				else
				{
					$coo_bs->_log("ERROR: Unhandled transaction status", GMBillSafe::LOGLEVEL_ERROR);
					$main_content .= '<p class="error">'.$coo_langtext_bs->get_text('general_error').'</p>';
				}
			}
			else
			{
				$coo_bs->_log("ERROR: Invalid transaction status response or protocol error", GMBillSafe::LOGLEVEL_ERROR);
				$main_content .= '<p class="error">'.$coo_langtext_bs->get_text('general_error').'</p>';
			}
		}

		$this->content_array['main_content'] = $this->main_content;
		$this->content_array['billsafe_token'] = $this->billsafe_token;
		$this->content_array['layerform_action'] = $this->layerform_action;
		$this->content_array['lpg_close_url'] = $this->lpg_close_url;
		$this->content_array['layerform_button'] = $coo_bs->get_text('layerform_button');
		$this->content_array['sandbox_mode'] = $this->sandbox_mode;
		$this->content_array['product'] = $this->product;
		$this->content_array['main_content'] = $main_content;
	}
}