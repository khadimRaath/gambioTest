<?php
/* --------------------------------------------------------------
   update_customer_b2b_status.inc.php 2014-12-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
 
function update_customer_b2b_status($p_new_b2b_status)
{
	if($p_new_b2b_status == '0' || $p_new_b2b_status == '1')
	{
		$_SESSION['customer_b2b_status'] = (string)(int)$p_new_b2b_status;
	}
}