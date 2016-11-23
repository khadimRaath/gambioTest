<?php
/* --------------------------------------------------------------
   ServerInfoMaster.inc.php 2012-02-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class ServerInfoMaster
{
	function ini_flatten($p_config_array)
	{
		$t_flat_array = array();
		foreach($p_config_array as $t_key => $t_info_array)
		{
			$t_flat_array[$t_key] = $t_info_array['local_value'];
		}

		return $t_flat_array;
	}


	function fix_on_off($p_var)
	{
		$t_output = !empty($p_var) ? $p_var : "Off";

		return $t_output == "1" ? "On" : $t_output;
	}


	function fix_no_value($p_var)
	{
		return !empty($p_var) ? $p_var : "no value set";
	}


	function check_ext_loaded($p_ext)
	{
		return extension_loaded($p_ext) ? $p_ext . " support is On" : $p_ext . " support is Off";
	}


	function get_server_info()
	{
		include(DIR_FS_CATALOG . 'release_info.php');

		$t_server_info_array  = array();
		$t_ini_data_array = $this->ini_flatten( ini_get_all() );

		$t_sql = "SELECT VERSION() AS mysql_version";
		$t_result = xtc_db_query($t_sql);
		$t_result_array = xtc_db_fetch_array($t_result);

		$t_server_info_array["gambio"]									 =       $gx_version;

		$t_server_info_array["phpversion"]                               =       phpversion();

		$t_server_info_array["mysqlversion"]                             =       $t_result_array['mysql_version'];

		$t_server_info_array["SERVER_SOFTWARE"]                          =       $_SERVER["SERVER_SOFTWARE"];
		$t_server_info_array["REQUEST_URI"]                              =       $_SERVER["REQUEST_URI"];
		$t_server_info_array["SCRIPT_NAME"]                              =       $_SERVER["SCRIPT_NAME"];
		$t_server_info_array["PHP_SELF"]                                 =       $_SERVER["PHP_SELF"];
		$t_server_info_array["host_ip"]                                  =       $_SERVER["SERVER_ADDR"];

		$t_server_info_array["allow_call_time_pass_reference"]           =       $this->fix_on_off($t_ini_data_array["allow_call_time_pass_reference"]);
		$t_server_info_array["allow_url_fopen"]                          =       $this->fix_on_off($t_ini_data_array["allow_url_fopen"]);
		$t_server_info_array["allow_url_include"]                        =       $this->fix_on_off($t_ini_data_array["allow_url_include"]);
		$t_server_info_array["default_charset"]                          =       $t_ini_data_array["default_charset"];

		// active disabled classes ?
		if( !empty( $t_ini_data_array["disable_classes"] ) )
		{
			$t_server_info_array["disable_classes"]                      =       $t_ini_data_array["disable_classes"];
		}
		else{
			$t_server_info_array["disable_classes"]                      =       "no disabled classes found";
		}

		// active disabled functions
		if( !empty( $t_ini_data_array["disable_classes"] ) )
		{
			$t_server_info_array["disable_functions"]                    =       $t_ini_data_array["disable_functions"];
		}
		else{
			$t_server_info_array["disable_functions"]                    =       "no disabled functions found";
		}

		$t_server_info_array["display_errors"]                           =       $this->fix_on_off($t_ini_data_array["display_errors"]);
		$t_server_info_array["error_reporting"]                          =       $t_ini_data_array["error_reporting"];
		$t_server_info_array["magic_quotes_gpc"]                         =       $this->fix_on_off($t_ini_data_array["magic_quotes_gpc"]);
		$t_server_info_array["max_execution_time"]                       =       $t_ini_data_array["max_execution_time"];
		$t_server_info_array["max_file_uploads"]                         =       $t_ini_data_array["max_file_uploads"];
		$t_server_info_array["memory_limit"]                             =       $t_ini_data_array["memory_limit"];
		$t_server_info_array["post_max_size"]                            =       $t_ini_data_array["post_max_size"];

		$t_register_globals												 =       ini_get("register_globals");

		$t_server_info_array["register_globals"]                         =       $this->fix_on_off($t_register_globals);

		if(isset($t_ini_data_array["safe_mode"]))
		{
			$t_server_info_array["safe_mode"]                            =		 $this->fix_on_off($t_ini_data_array["safe_mode"]);
		}
		elseif(version_compare( phpversion(), '5.3.0') >= 0)
		{
			$t_server_info_array["safe_mode"]                            =       "safe_mode is DEPRECATED since PHP 5.3.0!";
		}
		else
		{
			$t_server_info_array["max_input_vars"]                       =       "not present in this php version";
		}

		$t_server_info_array["safe_mode_gid"]                            =       $this->fix_on_off($t_ini_data_array["safe_mode_gid"]);
		$t_server_info_array["sql.safe_mode"]                            =       $this->fix_on_off($t_ini_data_array["sql.safe_mode"]);
		$t_server_info_array["short_open_tag"]                           =       $this->fix_on_off($t_ini_data_array["short_open_tag"]);
		$t_server_info_array["upload_max_filesize"]                      =       $t_ini_data_array["upload_max_filesize"];

		if(isset($t_ini_data_array["max_input_vars"]))
		{
			$t_server_info_array["max_input_vars"]                       =       $t_ini_data_array["max_input_vars"];
		}
		else
		{
			$t_server_info_array["max_input_vars"]                       =       "not present in this php version";
		}

		$t_server_info_array["max_input_nesting_level"]                  =       $t_ini_data_array["max_input_nesting_level"];
		$t_server_info_array["session.auto_start"]                       =       $t_ini_data_array["session.auto_start"];
		$t_server_info_array["session.bug_compat_42"]                    =       $this->fix_on_off($t_ini_data_array["session.bug_compat_42"]);
		$t_server_info_array["session.bug_compat_warn"]                  =       $this->fix_on_off($t_ini_data_array["session.bug_compat_warn"]);
		$t_server_info_array["session.cache_expire"]                     =       $t_ini_data_array["session.cache_expire"];
		$t_server_info_array["session.cache_limiter"]                    =       $t_ini_data_array["session.cache_limiter"];
		$t_server_info_array["session.cookie_domain"]                    =       $this->fix_no_value($t_ini_data_array["session.cookie_domain"]);
		$t_server_info_array["session.cookie_httponly"]                  =       $this->fix_on_off($t_ini_data_array["session.cookie_httponly"]);
		$t_server_info_array["session.cookie_lifetime"]                  =       $t_ini_data_array["session.cookie_lifetime"];
		$t_server_info_array["session.cookie_path"]                      =       $t_ini_data_array["session.cookie_path"];
		$t_server_info_array["session.cookie_secure"]                    =       $this->fix_on_off($t_ini_data_array["session.cookie_secure"]);
		$t_server_info_array["session.entropy_file"]                     =       $this->fix_no_value($t_ini_data_array["session.entropy_file"]);
		$t_server_info_array["session.entropy_length"]                   =       $t_ini_data_array["session.entropy_length"];
		$t_server_info_array["session.gc_divisor"]                       =       $t_ini_data_array["session.gc_divisor"];
		$t_server_info_array["session.gc_maxlifetime"]                   =       $t_ini_data_array["session.gc_maxlifetime"];
		$t_server_info_array["session.gc_probability"]                   =       $this->fix_on_off($t_ini_data_array["session.gc_probability"]);
		$t_server_info_array["session.hash_bits_per_character"]          =       $t_ini_data_array["session.hash_bits_per_character"];
		$t_server_info_array["session.hash_function"]                    =       $t_ini_data_array["session.hash_function"];
		$t_server_info_array["session.name"]                             =       $t_ini_data_array["session.name"];
		$t_server_info_array["session.referer_check"]                    =       $this->fix_no_value($t_ini_data_array["session.referer_check"]);
		$t_server_info_array["session.save_handler"]                     =       $t_ini_data_array["session.save_handler"];
		$t_server_info_array["session.save_path"]                        =       $this->fix_no_value($t_ini_data_array["session.save_path"]);
		$t_server_info_array["session.serialize_handler"]                =       $t_ini_data_array["session.serialize_handler"];
		$t_server_info_array["session.use_cookies"]                      =       $this->fix_on_off($t_ini_data_array["session.use_cookies"]);
		$t_server_info_array["session.use_only_cookies"]                 =       $this->fix_on_off($t_ini_data_array["session.use_only_cookies"]);
		$t_server_info_array["session.use_trans_sid"]                    =       $t_ini_data_array["session.use_trans_sid"];
		$t_server_info_array["soap.wsdl_cache"]                          =       $this->fix_on_off($t_ini_data_array["soap.wsdl_cache"]);
		$t_server_info_array["soap.wsdl_cache_enabled"]                  =       $this->fix_on_off($t_ini_data_array["soap.wsdl_cache_enabled"]);
		$t_server_info_array["soap.wsdl_cache_limit"]                    =       $t_ini_data_array["soap.wsdl_cache_limit"];
		$t_server_info_array["soap.wsdl_cache_ttl"]                      =       $t_ini_data_array["soap.wsdl_cache_ttl"];
		$t_server_info_array["url_rewriter.tags"]                        =       $t_ini_data_array["url_rewriter.tags"];

		$t_server_info_array["suhosin_support"]                          =       $this->check_ext_loaded('suhosin');
		$t_server_info_array["suhosin.post.max_array_depth"]             =       $t_ini_data_array["suhosin.post.max_array_depth"];
		$t_server_info_array["suhosin.post.max_array_index_length"]      =       $t_ini_data_array["suhosin.post.max_array_index_length"];
		$t_server_info_array["suhosin.post.max_vars"]                    =       $t_ini_data_array["suhosin.post.max_vars"];
		$t_server_info_array["suhosin.request.max_array_depth"]          =       $t_ini_data_array["suhosin.request.max_array_depth"];
		$t_server_info_array["suhosin.request.max_array_index_length"]   =       $t_ini_data_array["suhosin.request.max_array_index_length"];
		$t_server_info_array["suhosin.request.max_vars"]                 =       $t_ini_data_array["suhosin.request.max_vars"];
		$t_server_info_array["suhosin.executor.func.blacklist"]          =       $t_ini_data_array["suhosin.executor.func.blacklist"];

		$t_server_info_array["curlSupport"]                              =       $this->check_ext_loaded('curl');

		if( extension_loaded ('curl') )
		{
			$tmp = curl_version();
			$t_server_info_array["curlInformation"]                      =       "libcurl/" . $tmp["version"] . " " . $tmp["ssl_version"] .
																				  " zlib"     . $tmp["libz_version"];
		}

		$t_server_info_array["ftp_support"]                              =       $this->check_ext_loaded('ftp');
		$t_server_info_array["gd_support"]                               =       $this->check_ext_loaded('gd');

		if( extension_loaded('gd') )
		{
			$t_server_info_array["gd_information"]                       =       gd_info();
		}

		$t_server_info_array["json_support"]							 =       $this->check_ext_loaded('json');
		$t_server_info_array["mysql_client_api_version"]                 =       extension_loaded ('mysql') ? mysqli_get_client_info() : "mysql extension not loaded yet!";
		$t_server_info_array["openssl_version"]                          =       $tmp["ssl_version"];

		$t_server_info_array["installed_extensions"]                     =       get_loaded_extensions();

		return ( $t_server_info_array );
	}


	function format($p_data_array)
	{
		$t_json = '';
		$coo_json =  new Services_JSON();
		$t_json = $coo_json->encodeUnsafe($p_data_array);

		$t_json = str_replace('",', "\",\n", $t_json);
		$t_json = str_replace(',"', ",\n\"", $t_json);
		$t_json = str_replace('{', "\n{\n", $t_json);
		$t_json = str_replace('}', "\n}", $t_json);
		$t_json = str_replace('[', "\n[\n", $t_json);
		$t_json = str_replace(']', "\n]", $t_json);
		$t_json = str_replace('\/', "/", $t_json);


		return $t_json;
	}


	function send($p_server_info, $p_comment)
	{
		$t_url = 'https://www.gambio-support.de/misc/serverinfo/';
		$t_post_data = 'server_info_array=' . urlencode($p_server_info) . '&comment=' . urlencode($p_comment);

		$t_success = false;

		if(function_exists('curl_init'))
		{
			$ch = curl_init($t_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $t_post_data);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_URL, $t_url);
			$t_response = curl_exec($ch);
			$t_response = trim($t_response);
			curl_close($ch);

			if($t_response == 'success')
			{
				$t_success = true;
			}
		}

		return $t_success;
	}
}
?>