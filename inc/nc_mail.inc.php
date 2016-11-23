<?php
/* --------------------------------------------------------------
   nc_mail.inc.php 2008-01-27 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_address_label.inc.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_address_label.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   // include needed functions
   
  function nc_mail($emailTo, $emailFrom, $subject, $body)
  {
		mail($emailTo, 
				 $subject, 
				 $body,
				 'From: '. $emailFrom ."\n".
				 'Content-Type: text/plain'
				);
				
		return true;
  }
?>