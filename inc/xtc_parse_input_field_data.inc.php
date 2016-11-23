<?php
/* --------------------------------------------------------------
   xtc_parse_input_field_data.inc.php 2011-05-19 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2011 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(html_output.php,v 1.52 2003/03/19); www.oscommerce.com
   (c) 2003	 nextcommerce (xtc_parse_input_field_data.inc.php,v 1.3 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: xtc_parse_input_field_data.inc.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// Parse the data used in the html tags to ensure the tags will not break
function xtc_parse_input_field_data($data, $parse) {
	if(!is_array($data))
	{
		return strtr(trim($data), $parse);
	}

	return '';
 }
 ?>