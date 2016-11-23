<?php
/* --------------------------------------------------------------
	FindologicControl.inc.php 2015-07-06 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

require_once DIR_FS_CATALOG.'/findologic_config.inc.php';

class FindologicControl
{
	public function curl_http_request($link, $timeout) {
		$http_response = '';
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $link);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_TIMEOUT, $timeout);
		$http_response = curl_exec($c);
		curl_close($c);
		return $http_response;
	}

	public function http_request($link) {
		$http_response = '';
		$handle = fopen($link, 'r');
		if (!handle) {
			return '';
		} else {
			while (!feof($handle)) {
				$http_response .= fread($handle, 512);
			}
		}
		return $http_response;
	}

	public function direct_http_request($link, $timeout) {
		$http_response = '';
		$url = parse_url($link);
		$handle = fsockopen($url['host'], 80, $err_num, $err_msg, $timeout);
		if (!$handle) {
			return "error: $err_msg($err_num)";
		} else {
			fputs($handle, 'GET ' . $url['path'] . '?' . $url['query']  . " HTTP/1.0\n");
			fputs($handle, 'Host: ' . $url['host'] . "\n");
			fputs($handle, "Connection: close\n\n");
			while (!feof($handle)) {
				$http_response .= fgets($handle, 512);
			}
			fclose($handle);
		}
		return $http_response;
	}

	public function async_request($link) {
		$timeout = 10;
		$url = parse_url($link);
		$handle = fsockopen($url['host'], 80, $err_num, $err_msg, $timeout);
		if (!$handle) {
			return "error: $err_msg($err_num)";
		} else {
			stream_set_timeout($handle, $timeout);
			fputs($handle, 'GET ' . $url['path'] . '?' . $url['query']  . " HTTP/1.0\n");
			fputs($handle, 'Host: ' . $url['host'] . "\n");
			fputs($handle, "Connection: close\n\n");
			fclose($handle);
		}
	}

	public function is_alive($url) {
		if (function_exists('curl_init')) {
			$status = $this->curl_http_request($url.'alivetest.php?shopkey=' . FL_SHOP_ID, FL_ALIVE_TEST_TIMEOUT);
			//die($url.'alivetest.php?shopkey=' . FL_SHOP_ID .' => '. $status);
			return trim($status) == "alive";
		} else {
			$status = $this->direct_http_request($url.'alivetest.php?shopkey=' . FL_SHOP_ID, FL_ALIVE_TEST_TIMEOUT);
			return substr(trim($status), -5) == 'alive';
		}
	}

	public function get_search_result($p_fl_get)
	{
		if(GROUP_CHECK == 'true')
		{
			$group_id = isset($_SESSION['customers_status']['customers_status_id']) ? $_SESSION['customers_status']['customers_status_id'] : DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
			$p_fl_get['group[]'] = $group_id;
		}

		$t_fl_url = FL_SERVICE_URL.'index.php?'.
			'shop='.FL_SHOP_ID.
			'&shopurl='.urlencode(FL_SHOP_URL).
			'&userip='.$_SERVER['REMOTE_ADDR'].
			'&referer='.urlencode($_SERVER['HTTP_REFERER']).
			'&revision='.urlencode(FL_REVISION).
			'&'.http_build_query($p_fl_get, '', '&');
		$t_fl_content = $this->curl_http_request($t_fl_url, FL_REQUEST_TIMEOUT);
		if(empty($t_fl_content) === true)
		{
			$t_search_result = array(
					'success' => false,
					'content_all' => '',
				);
		}
		else
		{
			$t_product_ids = $this->extract_product_ids($t_fl_content);
			$t_forward_url = $this->extract_forward_url($t_fl_content);
			$t_inactive_products = $this->find_inactive_products($t_product_ids);
			$t_bottom_content = '';
			if(empty($t_inactive_products) !== true)
			{
				$t_fl_text = MainFactory::create_object('LanguageTextManager', array('findologic', $_SESSION['languages_id']));
				$t_bottom_content = '<div class="fl_unavailable"><h2>'.$t_fl_text->get_text('unavailable_products').'</h2>';
				$t_bottom_content .= '<ul class="unavailable_products">';
				foreach($t_inactive_products as $t_iap)
				{
					$t_bottom_content .= '<li>'.$t_iap['products_name'].'</li>';
				}
				$t_bottom_content .= '</ul></div>';
			}

			$t_fl_content = str_replace('findologic.php', 'advanced_search_result.php', $t_fl_content);

			$t_search_result = array(
					'success' => true,
					'content_all' => $t_fl_content,
					'product_ids' => $t_product_ids,
					'bottom_content' => $t_bottom_content,
					'forward_url' => $t_forward_url,
				);
		}

		return $t_search_result;
	}

	public function extract_forward_url($p_fl_response)
	{
		$forward_part = preg_replace('/.*window.location.replace\(\'(.*?)\'\).*/si', '$1', $p_fl_response, -1, $count);
		if($count > 0) {
			return $forward_part;
		}
		else
		{
			return false;
		}
	}

	public function extract_product_ids($p_fl_response)
	{
		$t_flids_part = preg_replace('/.*<div id="flResults">(.*?)<\/div>.*/si', '$1', $p_fl_response);
		$t_num_flids = preg_match_all('_<flproductid>(\d+)</flproductid>_i', $t_flids_part, $t_flids_matches);
		$t_flids = array();
		if($t_num_flids > 0)
		{
			$t_flids = array_merge($t_flids, $t_flids_matches[1]);
		}
		return $t_flids;
	}

	public function find_inactive_products($p_candidate_ids)
	{
		$t_inactive_products = array();
		if(empty($p_candidate_ids) !== true)
		{
			if(GROUP_CHECK == 'true')
			{
				if(isset($_SESSION['customers_status']['customers_status_id']))
				{
					$t_group_id = $_SESSION['customers_status']['customers_status_id'];
				}
				else
				{
					$t_group_id = DEFAULT_CUSTOMERS_STATUS_ID_GUEST;
				}
				$t_where_condition = '(`p`.`products_status` = 0 OR `p`.`group_permission_'.(int)$t_group_id.'` = 0)';
			}
			else
			{
				$t_where_condition = '`p`.`products_status` = 0';
			}

			$t_query =
				'SELECT
					`p`.`products_id`,
					`pd`.`products_name`
				FROM
					`products` p
				LEFT JOIN
					`products_description` pd
					ON
						`pd`.`products_id` = `p`.`products_id` AND
						`pd`.`language_id` = :language_id
				WHERE
					`p`.`products_id` IN (:pids_list) AND
					:where_condition
				';
			$t_query  = strtr($t_query, array(
					':pids_list' => implode(',', $p_candidate_ids),
					':where_condition' => $t_where_condition,
					':language_id' => (int)$_SESSION['languages_id'],
				));

			$t_result = xtc_db_query($t_query);
			while($t_row = xtc_db_fetch_array($t_result))
			{
				$t_inactive_products[] = $t_row;
			}
		}
		return $t_inactive_products;
	}

}