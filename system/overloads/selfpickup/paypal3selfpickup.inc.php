<?php
/* --------------------------------------------------------------
	paypal3selfpickup.inc.php 2015-07-02
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class paypal3selfpickup extends paypal3selfpickup_parent
{
	function quote($method = '')
	{
		$this->quotes = parent::quote($method);

		if(isset($_SESSION['paypal_payment']) && $_SESSION['paypal_payment']['type'] == 'ecs' && $_SESSION['paypal_payment']['state'] == 'approved')
		{
			$paypalConfig = MainFactory::create('PayPalConfigurationStorage');
			if($paypalConfig->get('allow_selfpickup') == false)
			{
				$this->quotes = false;
			}
		}

		return $this->quotes;
	}
}
