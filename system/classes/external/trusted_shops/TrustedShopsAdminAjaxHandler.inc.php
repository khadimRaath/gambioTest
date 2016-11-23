<?php
/* --------------------------------------------------------------
   TrustedShopsAdminAjaxHandler.inc.php 2013-11-14 mab@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class TrustedShopsAdminAjaxHandler extends AjaxHandler {
	function get_permission_status($p_customers_id=NULL)	{
		return true;
	}

	function proceed()	{
		$output = array();
		$service = new GMTSService();
		if($service === false) {
			$output['error'] = 'service unavailable';
			return true;
		}
		$cmd = $this->v_data_array['GET']['cmd'];
		switch($cmd) {
			case 'new_id':
				$cert = $service->checkCertificate($this->v_data_array['POST']['new_id']);
				$rating_state = $service->updateRatingWidgetState($this->v_data_array['POST']['new_id']);
				if($cert->stateEnum == 'INVALID_TS_ID' && $rating_state != 'OK') {
					$output['error'] = 'TS_INVALID_ID';
					$output['reload'] = false;
				}
				else {
					$is_double = $service->numCertsByLanguageURL($cert->certificationLanguage, $cert->url) > 0;
					if($is_double) {
						$output['error'] = 'TS_DOUBLE_ID';
						$output['reload'] = false;
					}
					else {
						$cert->rating_ok = $rating_state == 'OK' ? 1 : 0;
						$service->storeCertificate($cert);
						$output['reload'] = true;
					}
				}
				break;
			case 'check_login':
				$result = $service->checkLogin($this->v_data_array['POST']['tsid'], $this->v_data_array['POST']['user'], $this->v_data_array['POST']['password']);
				$output['state'] = $result;
				$output['valid'] = $result >= 0;
				break;
			default:
				$output['message'] = 'not implemented';
		}
		$this->v_output_buffer = json_encode($output);
		return true;
	}
}

