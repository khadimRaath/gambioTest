<?php
/* --------------------------------------------------------------
	PayoneCheckoutCreditRiskContentView.inc.php 2013-10-04 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PayoneCheckoutCreditRiskContentView extends ContentView {
	protected $_payone;

	public function PayoneCheckoutCreditRiskContentView() {
		$this->set_content_template('module/checkout_payone_cr.html');
		$this->_payone = new GMPayOne();
	}

	function get_html() {
		$config = $this->_payone->getConfig();

		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			header('Content-Type: text/plain');

			if(isset($_POST['confirm'])) {
				// A/B testing: only perform scoring every n-th time
				$do_score = true;
				if($config['credit_risk']['abtest']['active'] == 'true') {
					$ab_value = max(1, (int)$config['credit_risk']['abtest']['value']);
					$score_count = (int)gm_get_conf('PAYONE_CONSUMERSCORE_ABCOUNTER');
					$do_score = ($score_count % $ab_value) == 0;
					gm_set_conf('PAYONE_CONSUMERSCORE_ABCOUNTER', $score_count + 1);
				}

				if($do_score) {
					$score = $this->_payone->scoreCustomer($_SESSION['billto']);
				}
				else {
					$score = false;
				}

				if($score instanceof Payone_Api_Response_Consumerscore_Valid) {
					switch((string)$score->getScore()) {
						case 'G':
							$_SESSION['payone_cr_result'] = 'green';
							break;
						case 'Y':
							$_SESSION['payone_cr_result'] = 'yellow';
							break;
						case 'R':
							$_SESSION['payone_cr_result'] = 'red';
							break;
						default:
							$_SESSION['payone_cr_result'] = $config['credit_risk']['newclientdefault'];
					}
					$_SESSION['payone_cr_hash']  = $this->_payone->getAddressHash($_SESSION['billto']);
				}
				else {
					// could not get a score value
					$_SESSION['payone_cr_result'] = $config['credit_risk']['newclientdefault'];
					$_SESSION['payone_cr_hash']  = $this->_payone->getAddressHash($_SESSION['billto']);
				}

				if($config['credit_risk']['timeofcheck'] == 'before') {
					xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT);
				}
				else {
					xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_CONFIRMATION);
				}
			}
			else if(isset($_POST['noconfirm'])) {
				if($config['credit_risk']['timeofcheck'] == 'before') {
					xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_PAYMENT.'?p1crskip=1');
				}
				else {
					xtc_redirect(GM_HTTP_SERVER.DIR_WS_CATALOG.FILENAME_CHECKOUT_CONFIRMATION.'?p1crskip=1');
				}
			}
		}

		$this->set_content_data('notice', $config['credit_risk']['notice']['text']);
		$this->set_content_data('confirmation', $config['credit_risk']['confirmation']['text']);
		$t_html_output = $this->build_html();
		return $t_html_output;
	}
}
