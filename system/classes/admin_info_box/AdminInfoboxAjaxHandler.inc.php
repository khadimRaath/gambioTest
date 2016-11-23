<?php
/* --------------------------------------------------------------
   AdminInfoboxAjaxHandler.inc.php 2014-07-31 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AdminInfoboxAjaxHandler extends AjaxHandler
{
	function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	function proceed()
	{
		$t_success = false;
		
		$c_infobox_message_id = (int)$this->v_data_array['GET']['id'];	
		$coo_admin_infobox_control = MainFactory::create_object('AdminInfoboxControl');
		
		switch($this->v_data_array['GET']['action'])
		{
			case 'hide_info_box':
				$t_success = $coo_admin_infobox_control->set_status($c_infobox_message_id, 'hidden');
				break;
			case 'remove_info_box':
				$t_success = $coo_admin_infobox_control->set_status($c_infobox_message_id, 'deleted');
				break;
			case 'set_status_read':
				$t_success = $coo_admin_infobox_control->set_status($c_infobox_message_id, 'read');
				break;
		}		
		
		return $t_success;
	}
}