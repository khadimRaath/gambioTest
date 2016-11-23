<?php
/* --------------------------------------------------------------
   GMCounter.php 2015-06-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GMC_ORIGIN
{
	protected $gmc_current_ip;
	protected $gmc_current_agent;

	
	public function __construct()
	{
		$this->gmc_current_ip = md5(xtc_get_ip_address());
		$this->gmc_current_agent = $_SERVER['HTTP_USER_AGENT'];
	}


	/*
	*	-> 	set user once a time for every user
	*/
	public function gmc_set_current_user($p_echo = true)
	{
		$t_output = '';

		if(!$this->gmc_get_ip())
		{
			// -> set ip
			$this->gmc_set_ip();

			// -> set visit
			$this->gmc_set_visit();

			// -> set browser
			$this->gmc_set_browser();

			// -> set platform
			$this->gmc_set_platform();

			// -> set screen
			$t_output = $this->gmc_get_user_screen($p_echo);

			// -> set country/language
			$this->gmc_set_origin();

			// -> set history
			$this->gmc_set_history();
		}

		return $t_output;
	}


	/*
	*	-> get pages
	*/
	public function gmc_record($products_id, $cpath_id)
	{
		// -> store as category
		if(!empty($cpath_id) && $products_id == '0')
		{
			if(strpos($cpath_id, '_') !== false)
			{
				$this->gmc_set_page(str_replace('_', '', strrchr($cpath_id, '_')), 'cat');
			}
			else
			{
				$this->gmc_set_page($cpath_id, 'cat');
			}
			// -> store as prd
		}
		elseif($products_id != '0')
		{
			$this->gmc_set_page($products_id, 'prd');
			// -> store coIDs
		}
		elseif(!empty($_GET['coID']))
		{
			$this->gmc_set_page((int)$_GET['coID'], 'coid');
			// -> store the rest
		}
		elseif(empty($cpath_id) && $products_id == '0')
		{
			$this->gmc_set_page(str_replace('/', '', strrchr($_SERVER['SCRIPT_NAME'], '/')), 'content');
		}

		// -> store referer
		if(@strstr($_SERVER['HTTP_REFERER'], HTTP_SERVER) == false
		   && @strstr($_SERVER['HTTP_REFERER'], HTTPS_SERVER) == false
		)
		{
			if(!empty($_SERVER['HTTP_REFERER']))
			{
				//	$this->gmc_set_page($_SERVER['HTTP_REFERER'], 'ref');
				// -> store extern keywords
				$this->gmc_set_extern_searchterms($_SERVER['HTTP_REFERER']);
			}
		}

		// -> store search intern keywords
		if(!empty($_GET['keywords']))
		{
			$gm_search_log = MainFactory::create_object('GMTracker');
			$gm_search_log->gm_delete();

			if($gm_search_log->gm_ban() == false)
			{
				$gm_search_log->gm_track();
				$this->gmc_set_intern_search(trim($_GET['keywords']));
			}
		}
	}


	/*
	*	-> set history
	*/
	protected function gmc_set_history()
	{
		// -> get actual and last day
		$actual_day = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
		$last_day   = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')));

		// -> get rows of the last day
		$gm_page_query = xtc_db_query("
											SELECT
												*
											FROM
												gm_counter_page
											WHERE
												gm_counter_page_date < '" . $actual_day . "'
											");

		// -> store rows in history table
		if(xtc_db_num_rows($gm_page_query) > 0)
		{
			while($row = xtc_db_fetch_array($gm_page_query))
			{
				$gm_page_history_query = xtc_db_query("
															SELECT
																gm_counter_page_history_id AS id
															FROM
																gm_counter_page_history
															WHERE
																gm_counter_page_history_name = '"
				                                      . xtc_db_input($row['gm_counter_page_name']) . "'
															AND
																gm_counter_page_history_type = '"
				                                      . xtc_db_input($row['gm_counter_page_type']) . "'
															LIMIT 1
															");

				if(xtc_db_num_rows($gm_page_history_query) == 1)
				{
					$row_history = xtc_db_fetch_array($gm_page_history_query);

					xtc_db_query("
									UPDATE
										gm_counter_page_history
									SET
										gm_counter_page_history_hits	= gm_counter_page_history_hits + 1
									WHERE
										gm_counter_page_history_id		= '" . (int)$row_history['id'] . "'
									LIMIT 1
									");
				}
				else
				{
					xtc_db_query("
									INSERT INTO
											gm_counter_page_history
										SET
											gm_counter_page_history_name = '"
					             . xtc_db_input($row['gm_counter_page_name']) . "',
											gm_counter_page_history_type = '"
					             . xtc_db_input($row['gm_counter_page_type']) . "',
											gm_counter_page_history_hits = 1,
											gm_counter_page_history_date = '" . $last_day . "'
									");
				}

				// -> delete rows of the last day
				xtc_db_query("
									DELETE
									FROM
										gm_counter_page
									WHERE
										gm_counter_page_date < '" . $actual_day . "'
									");
			}
		}
	}


	/*
	*	-> update visits
	*/
	protected function gmc_set_visit()
	{
		$gm_query = xtc_db_query("
									SELECT
										gm_counter_id
									AS
										id
									FROM
										gm_counter_visits
									WHERE
										gm_counter_date >= '" . date('Y-m-d 00:00:00') . "'
									LIMIT 1
									");

		if(xtc_db_num_rows($gm_query) == 1)
		{
			xtc_db_query("
							UPDATE
									gm_counter_visits
								SET
									gm_counter_visits_total = gm_counter_visits_total + 1
								WHERE
									gm_counter_date >= '" . date('Y-m-d 00:00:00') . "'
							");
		}
		else
		{
			xtc_db_query("
							INSERT
									gm_counter_visits
								SET
									gm_counter_visits_total = 1,
									gm_counter_date = NOW()
							");
		}
	}


	/*
	*	-> 	set the actual visited page
	*/
	protected function gmc_set_page($page_name, $page_type)
	{
		xtc_db_query("
						INSERT
							INTO
								gm_counter_page
							SET
								gm_counter_page_name = '" . xtc_db_input($page_name) . "',
								gm_counter_page_type = '" . xtc_db_input($page_type) . "',
								gm_counter_page_date = NOW()
						");
	}


	/*
	*	-> set current ip
	*/
	protected function gmc_set_ip()
	{
		xtc_db_query("
						INSERT
							INTO
								gm_counter_ip
							SET
								gm_ip_value = '" . xtc_db_input($this->gmc_current_ip) . "',
								gm_ip_date			= NOW()
						");
	}


	/*
	*	-> set current browser
	*/
	protected function gmc_set_browser()
	{
		$browser = $this->gmc_get_browser();
		$this->gmc_set_info_value($browser, 'browser');
	}


	/*
	*	-> set current platform
	*/
	protected function gmc_set_platform()
	{
		$useragent = strtolower_wrapper($this->gmc_current_agent);

		if(strpos($useragent, "windows nt 5.1") !== false)
		{
			$platform = "Windows XP";
		}
		elseif(strpos($useragent, "windows 98") !== false)
		{
			$platform = "Windows 98";
		}
		elseif(strpos($useragent, "windows nt 5.0") !== false)
		{
			$platform = "Windows 2000";
		}
		elseif(strpos($useragent, "windows nt 5.2") !== false)
		{
			$platform = "Windows 2003 server";
		}
		elseif(strpos($useragent, "windows nt 6.0") !== false)
		{
			$platform = "Windows Vista";
		}
		elseif(strpos($useragent, "windows nt") !== false)
		{
			$platform = "Windows NT";
		}
		elseif(strpos($useragent, "win 9x 4.90") !== false && strpos($useragent, "win me"))
		{
			$platform = "Windows ME";
		}
		elseif(strpos($useragent, "win ce") !== false)
		{
			$platform = "Windows CE";
		}
		elseif(strpos($useragent, "win 9x 4.90") !== false)
		{
			$platform = "Windows ME";
		}
		elseif(strpos($useragent, "mac os x") !== false)
		{
			$platform = "Mac OS X";
		}
		elseif(strpos($useragent, "macintosh") !== false)
		{
			$platform = "Macintosh";
		}
		elseif(strpos($useragent, "linux") !== false)
		{
			$platform = "Linux";
		}
		elseif(strpos($useragent, "freebsd") !== false)
		{
			$platform = "Free BSD";
		}
		elseif(strpos($useragent, "symbian") !== false)
		{
			$platform = "Symbian";
		}
		else
		{
			// not found
			$platform = 'UNKNOWN';
		}

		$this->gmc_set_info_value($platform, 'platform');
	}


	/*
	*	-> set value
	*/
	public function gmc_set_info_value($value, $type)
	{
		if(empty($value) && $value != 0 && $value != "0")
		{
			$value = "UNKNOWN";
		}

		// -> get the id of the type
		$gm_info_query = xtc_db_query("
									SELECT
										gm_counter_info_type_id AS type_id
									FROM
										gm_counter_info_type
									WHERE
										gm_counter_info_type_name = '" . xtc_db_input($type) . "'
									LIMIT 1
									");

		// -> proceed if id was found
		if(xtc_db_num_rows($gm_info_query) == 1)
		{
			$gm_info_array = xtc_db_fetch_array($gm_info_query);

			$gm_query = xtc_db_query("
										SELECT
											gm_counter_info_id
										FROM
											gm_counter_info
										WHERE
											gm_counter_info_type_id = '" . (int)$gm_info_array['type_id'] . "'
										AND
											gm_counter_info_name		= '" . xtc_db_input($value) . "'
										LIMIT 1
										");

			if(xtc_db_num_rows($gm_query) == 1)
			{
				$gm_array = xtc_db_fetch_array($gm_query);

				xtc_db_query("
									UPDATE
											gm_counter_info
										SET
											gm_counter_info_hits	= gm_counter_info_hits + 1
										WHERE
											gm_counter_info_id		= '" . (int)$gm_array['gm_counter_info_id'] . "'
										AND
											gm_counter_info_name		= '" . xtc_db_input($value) . "'
										LIMIT 1
								");
			}
			else
			{
				xtc_db_query("
									INSERT INTO
											gm_counter_info
										SET
											gm_counter_info_hits		= 1,
											gm_counter_info_type_id		= '" . (int)$gm_info_array['type_id'] . "',
											gm_counter_info_name		= '" . xtc_db_input($value) . "'

								");
			}
		}
	}


	/*
	*	-> set extern searchterms
	*/
	protected function gmc_set_intern_search($searchterm)
	{
		$gm_query = xtc_db_query("
									SELECT
										gm_counter_intern_search_id
									FROM
										gm_counter_intern_search
									WHERE
										gm_counter_intern_search_name = '"
		                         . xtc_db_input(strtolower_wrapper($searchterm)) . "'
									LIMIT 1
									");

		if(xtc_db_num_rows($gm_query) == 1)
		{
			$gm_searchterm = xtc_db_fetch_array($gm_query);

			xtc_db_query("
								UPDATE
									gm_counter_intern_search
								SET
									gm_counter_intern_search_hits = gm_counter_intern_search_hits + 1
								WHERE
									gm_counter_intern_search_id = '"
			             . (int)$gm_searchterm['gm_counter_intern_search_id'] . "'
								LIMIT 1
							");
		}
		else
		{
			xtc_db_query("
							INSERT
								INTO
									gm_counter_intern_search
								SET
									gm_counter_intern_search_name = '" . xtc_db_input(strtolower_wrapper($searchterm)) . "',
									gm_counter_intern_search_hits = 1
							");
		}
	}


	/*
	*	-> set intern searchterms
	*/
	protected function gmc_set_extern_search($searchterm, $searchengine)
	{
		$searchterm = $searchterm;

		$gm_query = xtc_db_query("
									SELECT
										gm_counter_extern_search_id
									FROM
										gm_counter_extern_search
									WHERE
										gm_counter_extern_search_name	= '"
		                         . xtc_db_input(strtolower_wrapper($searchterm)) . "'
									AND
										gm_counter_extern_search_engine = '"
		                         . xtc_db_input(strtolower_wrapper($searchengine)) . "'
									LIMIT 1
									");

		if(xtc_db_num_rows($gm_query) == 1)
		{
			$gm_searchterm = xtc_db_fetch_array($gm_query);

			xtc_db_query("
								UPDATE
									gm_counter_extern_search
								SET
									gm_counter_extern_search_hits	= gm_counter_extern_search_hits + 1
								WHERE
									gm_counter_extern_search_id		= '"
			             . (int)$gm_searchterm['gm_counter_extern_search_id'] . "'
								LIMIT 1
							");
		}
		else
		{
			xtc_db_query("
							INSERT
								INTO
									gm_counter_extern_search
								SET
									gm_counter_extern_search_name	= '"
			             . xtc_db_input(strtolower_wrapper($searchterm)) . "',
									gm_counter_extern_search_engine = '"
			             . xtc_db_input(strtolower_wrapper($searchengine)) . "',
									gm_counter_extern_search_hits	= 1
							");
		}
	}


	/*
	*	-> set extern searchterms
	*/
	protected function gmc_set_extern_searchterms($ref)
	{
		$url = parse_url($ref);
		if(preg_match("/google\./i", $url['host']))
		{
			parse_str($url['query'], $q);
			$searchterms = $q['q'];
		}
		else
		{
			if(preg_match("/yahoo\./i", $url['host']))
			{
				parse_str($url['query'], $q);
				$searchterms = $q['p'];
			}
			else
			{
				if(preg_match("/search\.msn\./i", $url['host']))
				{
					parse_str($url['query'], $q);
					$searchterms = $q['q'];
				}
				else
				{
					if(preg_match("/search\.aol\./i", $url['host']))
					{
						parse_str($url['query'], $q);
						$searchterms = $q['query'];
					}
					else
					{
						if(preg_match("/web\.ask\./i", $url['host']))
						{
							parse_str($url['query'], $q);
							$searchterms = $q['q'];
						}
						else
						{
							if(preg_match("/search\.looksmart\./i", $url['host']))
							{

								parse_str($url['query'], $q);
								$searchterms = $q['p'];
							}
							else
							{
								if(preg_match("/alltheweb\./i", $url['host']))
								{
									parse_str($url['query'], $q);
									$searchterms = $q['q'];
								}
								else
								{
									if(preg_match("/a9\./i", $url['host']))
									{
										parse_str($url['query'], $q);
										$searchterms = $q['q'];
									}
									else
									{
										if(preg_match("/gigablast\./i", $url['host']))
										{
											parse_str($url['query'], $q);
											$searchterms = $q['q'];
										}
										else
										{
											if(preg_match("/s\.teoma\./i", $url['host']))
											{
												parse_str($url['query'], $q);
												$searchterms = $q['q'];
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if(!empty($searchterms))
		{
			$this->gmc_set_extern_search($searchterms, $url['host']);
		}
	}


	/*
	*	-> get current ip
	*/
	protected function gmc_get_ip()
	{
		$gm_query = xtc_db_query("
									SELECT
										gm_ip_value
									FROM
										gm_counter_ip
									WHERE
										 gm_ip_value = '" . xtc_db_input($this->gmc_current_ip) . "'
									LIMIT 1
									");

		if(xtc_db_num_rows($gm_query) == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	/*
	*	-> get current browser
	*/
	protected function gmc_get_browser()
	{
		// -> MSIE
		if(strpos($this->gmc_current_agent, "MSIE") !== false && strpos($this->gmc_current_agent, "Opera") === false
		   && strpos($this->gmc_current_agent, "Netscape") === false
		)
		{
			$found = preg_match("/MSIE ([0-9]{1}\.[0-9]{1,2})/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Internet Explorer " . $matches[1];
			}
			// -> alternative Browser
		}
		elseif(strpos($this->gmc_current_agent, "Gecko"))
		{
			$found = preg_match("/Firefox\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Mozilla Firefox " . $matches[1];
			}

			$found = preg_match("/Netscape\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Netscape " . $matches[1];
			}

			$found = preg_match("/Safari\/([0-9]{2,3}(\.[0-9])?)/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Safari " . $matches[1];
			}

			$found = preg_match("/Galeon\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Galeon " . $matches[1];
			}

			$found = preg_match("/Konqueror\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Konqueror " . $matches[1];
			}

			return "Gecko based";
		}
		elseif(strpos($this->gmc_current_agent, "Opera") !== false)
		{
			$found = preg_match("/Opera[\/ ]([0-9]{1}\.[0-9]{1}([0-9])?)/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Opera " . $matches[1];
			}
		}
		elseif(strpos($this->gmc_current_agent, "Lynx") !== false)
		{
			$found = preg_match("/Lynx\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Lynx " . $matches[1];
			}
		}
		elseif(strpos($this->gmc_current_agent, "Netscape") !== false)
		{
			$found = preg_match("/Netscape\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/", $this->gmc_current_agent, $matches);
			if($found)
			{
				return "Netscape " . $matches[1];
			}
		}
		else
		{
			// not found
			return 'UNKNOWN';
		}
	}


	/*
	*	-> get user screen
	*/
	protected function gmc_get_user_screen($p_echo)
	{
		$t_output = '';

		if($p_echo)
		{
			echo '<script type="text/javascript" src="' . DIR_WS_CATALOG . 'gm/javascript/GMCounter.js"></script>';
		}
		else
		{
			$t_output = '<script type="text/javascript" src="' . DIR_WS_CATALOG
			            . 'gm/javascript/GMCounter.js"></script>';
		}

		return $t_output;
	}


	/*
	*	-> get user screen
	*/
	protected function gmc_set_origin()
	{
		$langs = array();

		if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
		{
			preg_match('/([^,;]*)/', $_SERVER["HTTP_ACCEPT_LANGUAGE"], $langs);
		}

		$array_country = explode("-", $langs[0]);

		if(strtolower_wrapper($array_country[0]) == strtolower_wrapper($array_country[1]))
		{
			$this->gmc_set_info_value($array_country[0], 'origin');
		}
		else
		{
			$this->gmc_set_info_value($langs[0], 'origin');
		}
	}


	/*
	*	-> delete old ips
	*/
	public function gmc_delete_old_ip()
	{
		xtc_db_query("
						DELETE
						FROM
							gm_counter_ip
						WHERE
							gm_ip_date + " . gm_get_conf('GM_COUNTER_IP_BARRIER') . " < NOW()
						");
	}
}

MainFactory::load_origin_class('GMC');