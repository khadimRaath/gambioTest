<?php
/* --------------------------------------------------------------
   ipayment_cc.php 2015-06-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once dirname(__FILE__).'/ipayment/ipayment.php';

class ipayment_cc extends ipayment {
	function ipayment_cc() {
		$this->code = 'ipayment_cc';
		parent::__construct();
		if(defined('MODULE_PAYMENT_IPAYMENT_CC_STATUS') && !defined('MODULE_PAYMENT_IPAYMENT_CC_CARDS_ENABLED'))
		{
			$query = "insert into ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) ".
		"values ('MODULE_PAYMENT_IPAYMENT_CC_CARDS_ENABLED', 'master,visa,amex,diners,jcb,solo,discover,maestro', '6', '20', '', '', now())";
			xtc_db_query($query);
		}
	}

   function _configuration()
   {
      $config = parent::_configuration();
      $config['CARDS_ENABLED'] = array(
            'configuration_value' => 'master,visa,amex,diners,jcb,solo,discover,maestro',
         );
      return $config;
   }
}