<?php
/* --------------------------------------------------------------
   xtc_get_prid.inc.php 2011-02-23 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   -----------------------------------------------------------------------------------------
   $Id: xtc_get_prid.inc.php 899 2005-04-29 02:40:57Z hhgag $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_get_prid.inc.php,v 1.3 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// Return a product ID from a product ID with attributes

 function xtc_get_prid($uprid) {
 	# gm_mod bof
 	$coo_properties_control	= MainFactory::create_object('PropertiesControl');
 	$uprid = $coo_properties_control->clear_baskets_products_id($uprid);
 	# gm_mod eof

	$pieces = explode('{', $uprid);

  if (is_numeric($pieces[0])) {
    return $pieces[0];
  } else {
    return false;
  }
}
?>