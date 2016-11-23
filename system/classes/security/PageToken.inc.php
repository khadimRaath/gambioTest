<?php
/* --------------------------------------------------------------
   PageToken.inc.php 2016-01-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PageToken
{
	protected $v_page_token_array;
	protected $v_max_array_size = 200;
	
	public function __construct()
	{
		
	}
	
	public function generate_token()
	{
		$t_key =  md5(time() . rand() . LogControl::get_secure_token());
		$this->v_page_token_array[$t_key] = true;
		if(count($this->v_page_token_array) > $this->v_max_array_size)
		{
			$this->v_page_token_array = array_slice($this->v_page_token_array, $this->v_max_array_size * -1);
		}
		
		return $t_key;
	}
	
	public function is_valid($p_check)
	{
		if(ACTIVATE_PAGE_TOKEN != 'true')
		{
			return true;			
		}
		
		if(!isset($this->v_page_token_array[$p_check]) || $this->v_page_token_array[$p_check] !== true || !preg_match('/^[a-f0-9]{32}$/', $p_check))
		{
			$this->write_page_token_warning($p_check);
			return false;
		}
		
		return true;
	}
	
	protected function write_page_token_warning($p_check)
	{
		$coo_logger = LogControl::get_instance(true);
		
		$t_message = 'Unsecure page token ' . $p_check;
		$t_additional_info = 'HTTP-Referer: '. $_SERVER['HTTP_REFERER'] . "\r\n";
		$t_additional_info .= 'used token: ' . $p_check . "\r\n";
		$t_additional_info .= 'Generated token array:' . "\r\n" . print_r($this->v_page_token_array, true);
		
		$coo_logger->error($t_message, 'security', 'security', 'error', 'SECURITY ERROR', E_USER_ERROR, $t_additional_info);
		$coo_logger->write_stack(array('security'));
		
		xtc_db_close();
		die('Unsecure page token!');
		
	}
}