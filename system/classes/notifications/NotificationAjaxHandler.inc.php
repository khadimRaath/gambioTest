<?php
/* --------------------------------------------------------------
   NotificationAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

/**
 * Class NotificationAjaxHandler
 */
class NotificationAjaxHandler extends AjaxHandler
{
	/**
	 * @param null $p_customers_id
	 *
	 * @return bool
	 */
	public function get_permission_status($p_customers_id = null)
	{
		return true;
	}

	/**
	 * @return bool
	 */
	public function proceed()
	{
		$responseArray = array();
		$action = $this->v_data_array['GET']['action'];

		switch($action)
		{
			case 'hide_topbar':
				$this->_hideTopbar();

				$responseArray['status'] = 'success';

				break;
			case 'hide_popup_notification':
				$this->_hidePopup();

				$responseArray['status'] = 'success';

				break;
			case '':
			default:
				
				if($this->_proceedOverloadAction($action) === false)
				{
					trigger_error('t_action_request not found: '. htmlentities_wrapper( $action ), E_USER_WARNING);
					return false;
				}
		}

		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$outputJson = $json->encode($responseArray);

		$this->v_output_buffer = $outputJson;

		return true;
	}

	
	protected function _hideTopbar()
	{
		$_SESSION['hide_topbar'] = true;
	}


	protected function _hidePopup()
	{
		$_SESSION['hide_popup_notification'] = true;
	}
	

	/**
	 * use this method for adding new action-case
	 * @param $p_action
	 *
	 * @return bool
	 */
	protected function _proceedOverloadAction($p_action)
	{
		return false;	
	}
} 