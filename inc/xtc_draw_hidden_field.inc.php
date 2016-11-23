<?php
/* --------------------------------------------------------------
   xtc_draw_hidden_field.inc.php 2008-04-18 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_draw_hidden_field.inc.php,v 1.3 2003/08/1); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_draw_hidden_field.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
// Output a form hidden field
  function xtc_draw_hidden_field($name, $value = '', $parameters = '') {
    $field = '<input type="hidden" name="' . xtc_parse_input_field_data($name, array('"' => '&quot;')) . '" value="';

    if (xtc_not_null($value)) {
      $field .= xtc_parse_input_field_data($value, array('"' => '&quot;'));
    } else {
      $field .= xtc_parse_input_field_data($GLOBALS[$name], array('"' => '&quot;'));
    }

   $field .= '" ';

    if (xtc_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= ' />';

    return $field;
  }
 ?>