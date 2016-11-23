<?php
/* --------------------------------------------------------------
   GMGPrintLoginExtender.inc.php 2011-12-01 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMGPrintLoginExtender extends GMGPrintLoginExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		if(is_object($_SESSION['coo_gprint_cart']))
		{
			$_SESSION['coo_gprint_cart']->restore();
		}
		if(is_object($_SESSION['coo_gprint_wishlist']))
		{
			$_SESSION['coo_gprint_wishlist']->restore();
		}		
	}
}
?>