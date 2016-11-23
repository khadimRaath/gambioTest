<?php
/* --------------------------------------------------------------
   localization.php 2016-05-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------
*/
/* --------------------------------------------------------------
   $Id: localization.php 950 2005-05-14 16:45:21Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(localization.php,v 1.12 2003/06/25); www.oscommerce.com
   (c) 2003	 nextcommerce (localization.php,v 1.4 2003/08/14); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

function quote_oanda_currency($code, $base = DEFAULT_CURRENCY)
{
	$t_url = 'https://www.oanda.com/convert/fxdaily?value=1&redirected=1&exch=' . $code .  '&format=CSV&dest=Get+Table&sel_list=' . $base;
	$page = quote_retrieve_url($t_url);

	$match = array();
	preg_match('/(.+),(\w{3}),([0-9.]+),([0-9.]+)/i', $page, $match);

	if(sizeof($match) > 0)
	{
		return $match[3];
	}
	else
	{
		return false;
	}
}

function quote_xe_currency($to, $from = DEFAULT_CURRENCY)
{
	$t_url = 'http://www.xe.net/ucc/convert.cgi?Amount=1&From=' . $from . '&To=' . $to;
	$page = quote_retrieve_url($t_url);

	$match = array();
	preg_match('/[0-9.]+\s*' . $from . '\s*=\s*([0-9.]+)\s*' . $to . '/', $page, $match);

	if(sizeof($match) > 0)
	{
		return $match[1];
	}
	else
	{
		return false;
	}
}

function quote_retrieve_url($p_url)
{
	$t_content = '';

	if(function_exists('curl_init'))
	{
		$t_curl_options = array(
				CURLOPT_URL => $p_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 5,
			);
		$t_ch = curl_init();
		curl_setopt_array($t_ch, $t_curl_options);
		$t_response = curl_exec($t_ch);
		$t_errno = curl_errno($t_ch);
		curl_close($t_ch);
		if($t_errno == 0)
		{
			$t_content = $t_response;
		}
	}
	else
	{
		$t_content = file($p_url);
		$t_content = implode('', $t_content);
	}

	return $t_content;
}