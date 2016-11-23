<?php
/* --------------------------------------------------------------
   xtc_draw_password_field.inc.php 2008-08-10 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_draw_password_field.inc.php,v 1.3 2003/08/1); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_draw_password_field.inc.php 899 2005-04-29 02:40:57Z hhgag $) 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
// Output a form password field
  // BOF GM_MOD
	function xtc_draw_password_field($name, $value = '', $parameters = 'maxlength="40"', $gm_css_class = 'gm_class_input') {
    return xtc_draw_input_field($name, $value, $parameters, 'password', false, $gm_css_class);
  }
  
    function xtc_draw_password_fieldNote($name, $value = '', $parameters = 'maxlength="40"', $gm_css_class = 'gm_class_input') {
    return xtc_draw_input_fieldNote($name, $value, $parameters, 'password', false, $gm_css_class);
  }
	// EOF GM_MOD
 ?>