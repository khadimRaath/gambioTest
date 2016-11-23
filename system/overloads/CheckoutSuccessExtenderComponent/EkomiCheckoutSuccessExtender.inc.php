<?php
/* --------------------------------------------------------------
   EkomiCheckoutSuccessExtender.inc.php 2012-01-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class EkomiCheckoutSuccessExtender extends EkomiCheckoutSuccessExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		if(gm_get_conf('EKOMI_STATUS') == '1')
		{
			$coo_ekomi_manager = MainFactory::create_object('EkomiManager', array(gm_get_conf('EKOMI_API_ID'), gm_get_conf('EKOMI_API_PASSWORD')));
						
			if(isset($this->v_data_array['orders_id']) && !empty($this->v_data_array['orders_id']))
			{
				$coo_ekomi_manager->load_settings();
				$coo_ekomi_manager->put_order($this->v_data_array);
			}

			$t_success = $coo_ekomi_manager->send_mails();
		}		
	}
}
?>