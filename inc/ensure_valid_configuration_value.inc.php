<?php
/* --------------------------------------------------------------
   ensure_valid_configuration_value.inc.php 2013-05-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function ensure_valid_configuration_value($p_key, $p_value)
{
	switch($p_key)
	{
		case 'MAX_PRODUCTS_QTY':
			$t_valid_value = (int)$p_value;
			if($t_valid_value < 1) $t_valid_value = 1;
			break;
		default:
			$t_valid_value = $p_value;
	}
	
	return $t_valid_value;
}