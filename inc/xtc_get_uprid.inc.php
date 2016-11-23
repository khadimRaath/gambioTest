<?php
/* --------------------------------------------------------------
   xtc_get_uprid.inc.php 2011-02-23 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   -----------------------------------------------------------------------------------------
   $Id: xtc_get_uprid.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(general.php,v 1.225 2003/05/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_get_uprid.inc.php,v 1.3 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
// Return a product ID with attributes

  function xtc_get_uprid($prid, $params, $p_products_properties_combis_id=0) {
  if (is_numeric($prid)) {
    $uprid = $prid;

    if (is_array($params) && (sizeof($params) > 0)) {
      $attributes_check = true;
      $attributes_ids = '';

      reset($params);
      while (list($option, $value) = each($params)) {
        if (is_numeric($option) && is_numeric($value)) {
          $attributes_ids .= '{' . (int)$option . '}' . (int)$value;
        } else {
          $attributes_check = false;
          break;
        }
      }

      if ($attributes_check == true) {
        $uprid .= $attributes_ids;
      }
    }
  } else {
    $uprid = xtc_get_prid($prid);

    if (is_numeric($uprid)) {
      if (strpos($prid, '{') !== false) {
        $attributes_check = true;
        $attributes_ids = '';

        $attributes = explode('{', substr($prid, strpos($prid, '{')+1));

        for ($i=0, $n=sizeof($attributes); $i<$n; $i++) {
          $pair = explode('}', $attributes[$i]);

          if (is_numeric($pair[0]) && is_numeric($pair[1])) {
            $attributes_ids .= '{' . (int)$pair[0] . '}' . (int)$pair[1];
          } else {
            $attributes_check = false;
            break;
          }
        }

        if ($attributes_check == true) {
          $uprid .= $attributes_ids;
        }
      }
    } else {
      return false;
    }
  }

	# gm_mod bof
	$c_products_properties_combis_id = (int)$p_products_properties_combis_id;
	if($c_products_properties_combis_id > 0)
	{
		$coo_properties_control	= MainFactory::create_object('PropertiesControl');
		$uprid = $coo_properties_control->get_baskets_products_id($uprid, $c_products_properties_combis_id);
	}
	# gm_mod eof

  return $uprid;
}
 ?>