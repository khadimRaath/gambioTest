<?php
/* --------------------------------------------------------------
   GoogleTaxonomySource.inc.php 2014-02-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GoogleTaxonomySource
{
	// url to the google file
	var $v_taxonomy_file_url;

	// file refresh time in days
	var $v_taxonomy_file_refresh_time = 1;

	// filepath to the cache file
	var $v_taxonomy_local_file_path;

	/**
	 * constructor set the local taxonomy path
	 * 
	 * @return bool true
	 */
	function GoogleTaxonomySource()
	{
		$this->v_taxonomy_local_file_path = DIR_FS_CATALOG.'cache/google_taxonomy.de-DE.txt';

		return true;
	}

	/*
	 * set the URL to the Google categorie taxonomie file
	 * 
	 * @param string $p_taxonomy_file_url URL to the file
	 * @return bool true
	 */
	function set_taxonomy_file_url($p_taxonomy_file_url)
	{
		$this->v_taxonomy_file_url = $p_taxonomy_file_url;
		return true;
	}

	/*
	 * refresh the categorie taxonomie file if the cached file date is to old
	 *
	 * @return bool OK:true | ERROR:false
	 */
	function refresh_local_taxonomy_file( )
	{
		if(file_exists($this->v_taxonomy_local_file_path) && filesize($this->v_taxonomy_local_file_path) > 0) {
			$t_filetime = filectime($this->v_taxonomy_local_file_path);
			$t_now = time();
			$t_tomorrow  = mktime(
					date('h', $t_filetime),
					date('i', $t_filetime),
					date('s', $t_filetime),
					date('m', $t_filetime),
					date('d', $t_filetime)+$this->v_taxonomy_file_refresh_time,
					date('Y', $t_filetime)
					);
			if($t_tomorrow > $t_now) {
				return true;
			}
		}

		if(empty($this->v_taxonomy_file_url)) {
			return false;
		}

		// get data from google
		if(function_exists('curl_init')) {
			$t_get_result = $this->get_data_by_curl();
		} else {
			$t_get_result = @file_get_contents($this->v_taxonomy_file_url);
			if(!$t_get_result) {
				$t_get_result = $this->get_data_by_fsockopen();
			}
		}

		// if bad result
		if($t_get_result === false || empty($t_get_result) || strlen_wrapper($t_get_result) < 100000) {
			return false;
		}

		// write new local taxonomy file
		$t_handle = fopen($this->v_taxonomy_local_file_path, 'w');
		$t_put_result = fwrite($t_handle, $t_get_result);
		fclose($t_handle);
		if($t_put_result === false) {
			return false;
		}

		return true;
	}

	/**
	 * get Google taxanomy data by cURL
	 *
	 * @return string Google taxanomy data
	 */
	function get_data_by_curl()
	{
		$t_ch = curl_init();
		curl_setopt($t_ch, CURLOPT_URL, $this->v_taxonomy_file_url);
		curl_setopt($t_ch, CURLOPT_RETURNTRANSFER, 1);
		$t_response = curl_exec($t_ch);

		return $t_response;
	}

	/**
	 * get Google taxanomy data by fsockopen
	 *
	 * @return string Google taxanomy data
	 */
	function get_data_by_fsockopen()
	{
		$t_parsed_url = parse_url($this->v_taxonomy_file_url);

		$t_sock = fsockopen($t_parsed_url['host'], 80, $errno, $errstr, 5);
		fputs($t_sock, "GET " . $t_parsed_url['path'] . " HTTP/1.1\r\n");
		fputs($t_sock, "Host: " . $t_parsed_url['host'] . "\r\n");
		fputs($t_sock, "Connection: close\r\n\r\n");

		$header = '';
		$t_data = '';
		do {
			$header .= fgets($t_sock, 4096);
		} while(strpos($header, "\r\n\r\n") === false);

		while(!feof($t_sock)) {
			$t_data .= fgets($t_sock, 4096);
		}

		fclose($t_sock);

		return $t_data;
	}

	/*
	 * get the categories after the given categorie
	 *
	 * @param string $p_parent parent categorie
	 * @return array $t_categorie_array array with the following categories
	 */
	function get_categories_array($p_parent = '')
	{
		// decode umlauts
		$t_parent = $p_parent;
		// get the file as array without empty lines
		$handle = fopen($this->v_taxonomy_local_file_path, 'r');

		$t_result_array = array();
		while (!feof($handle)) {
		    $t_line = fgets($handle, 4096);
			$t_line = trim($t_line);
			$t_line = $t_line;
			// exclude comments begins with #
			if(strstr($t_line, $t_parent.'#')) {
				continue;
			}
			// if no parent given, get all categories without an child
			if(empty($t_parent) && !strstr($t_line, '>')) {
				$t_result_array[] = $t_line;
			} elseif(!empty($t_parent) && strstr($t_line, $t_parent.' >')) {
				// cut the parent categorie
				$t_value = substr_wrapper($t_line, strlen_wrapper($t_parent));
				// make an array from line
				$t_temp_array = explode(' > ', $t_value);
				// get the child from given categorie
				$t_value = trim($t_temp_array[1]);
				// if not empty and if not in array, save categorie
				if(!empty($t_value) && !isset($t_temp_array[2])) {
					$t_result_array[] = $t_value;
				}
			}
		}
		fclose ($handle);

		return $t_result_array;
	}
}
?>