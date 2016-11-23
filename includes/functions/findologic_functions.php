<?php
/* --------------------------------------------------------------
   findologic_functions.php 2014-07-04 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function curl_http_request($link, $timeout) {
	$http_response = '';
	$c = curl_init();
	curl_setopt($c, CURLOPT_URL, $link);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_TIMEOUT, $timeout);
	$http_response = curl_exec($c);
	curl_close($c);
	return $http_response;
}

function http_request($link) {
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

function direct_http_request($link, $timeout) {
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

function async_request($link) {
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

function is_alive($url) {
	if (function_exists('curl_init')) {
		$status = curl_http_request($url.'alivetest.php?shopkey=' . FL_SHOP_ID, FL_ALIVE_TEST_TIMEOUT);
		//die($url.'alivetest.php?shopkey=' . FL_SHOP_ID .' => '. $status);
		return trim($status) == "alive";
	} else {
		$status = direct_http_request($url.'alivetest.php?shopkey=' . FL_SHOP_ID, FL_ALIVE_TEST_TIMEOUT);
		return substr(trim($status), -5) == 'alive';
	}
}

