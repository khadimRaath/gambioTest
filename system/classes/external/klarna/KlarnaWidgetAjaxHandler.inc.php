<?php
/* --------------------------------------------------------------
	KlarnaWidgetAjaxHandler.inc.php 2013-05-13 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2013 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class KlarnaWidgetAjaxHandler extends AjaxHandler {
	function get_permission_status($p_customers_id=NULL) {
		return true;
	}

	function proceed() {
		$klarna = new GMKlarna;
		$amount = (double)$_REQUEST['amount'];
		$amount = is_numeric($amount) ? $amount : 100;
		$widget = $klarna->getWidgetCode($amount, true);
		$this->v_output_buffer = $widget;
		return true;
	}
}