<?php
/* --------------------------------------------------------------
   ShippingAndPaymentMatrixAssistentSource.inc.php 2014-05-31 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShippingAndPaymentMatrixAssistentSource
{
	public function save_shipping_and_payment_matrix(array $p_shipping_info_array, array $p_payment_info_array, array $p_shipping_time_array)
	{
		foreach($p_shipping_info_array as $t_language_id => $t_country_array)
		{
			foreach($t_country_array as $t_country_code => $t_shipping_info)
			{
				$t_sql = 'REPLACE INTO `shipping_and_payment_matrix` (country_code, language_id, shipping_info, payment_info, shipping_time) VALUES("' . xtc_db_input(xtc_db_prepare_input($t_country_code)) . '", "' . (int)$t_language_id . '", "' . xtc_db_input(xtc_db_prepare_input($this->decode($t_shipping_info))) . '", "' . xtc_db_input(xtc_db_prepare_input($this->decode($p_payment_info_array[$t_language_id][$t_country_code]))) . '", "' . xtc_db_input(xtc_db_prepare_input($this->decode($p_shipping_time_array[$t_language_id][$t_country_code]))) . '")';
				xtc_db_query($t_sql);
			}
		}
	}
	
	public function delete_matrix()
	{
		$t_sql = 'TRUNCATE shipping_and_payment_matrix';
		xtc_db_query($t_sql);
	}
	
	public function decode($p_string)
	{
		$t_string = $p_string;
		
		if(preg_match('/(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+/xs', $p_string)
			&& strtoupper($_SESSION['language_charset']) != 'UTF-8')
		{
			$t_string = utf8_decode($p_string);
		}
		
		return $t_string;
	}
}