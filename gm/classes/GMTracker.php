<?php
/* --------------------------------------------------------------
   GMTracker.php 2015-05-20 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

GMTracker.php 09.04.2008 pt
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/
	
	class GMTracker {

		var $gm_current_ip;
		var $type;
		var $timeout;
		var $tryout;
		var $timeline;

		function __construct($type="login") {
			
			$this->type				= $type;
			$conf					= gm_get_conf(array('GM_' . strtoupper($this->type) . '_TIMEOUT','GM_' . strtoupper($this->type) . '_TRYOUT', 'GM_' . strtoupper($this->type) . '_TIMELINE'));			
			$this->gm_current_ip	= $_SERVER['REMOTE_ADDR'];
			$this->timeout			= $conf['GM_' . strtoupper($this->type) . '_TIMEOUT'];
			$this->tryout			= $conf['GM_' . strtoupper($this->type) . '_TRYOUT'];
			$this->timeline			= $conf['GM_' . strtoupper($this->type) . '_TIMELINE'];			
			return;		
		}

	
		/*
		*	-> log failed logins
		*/
		function gm_track() {
			xtc_db_query("
						INSERT INTO
							gm_" . $this->type . "_history
						SET 
							gm_" . $this->type . "_ip		= '" . $_SERVER['REMOTE_ADDR'] . "',
							gm_" . $this->type . "_date		= NOW()
						");		

			return;
		}


		/*
		*	-> delete ips
		*/
		function gm_delete_ip() {
			
			xtc_db_query("
						DELETE
						FROM 
							gm_" . $this->type . "_history
						WHERE
							gm_" . $this->type . "_ip	= '" . $this->gm_current_ip . "' 
						");
		}


		/*
		*	-> get date
		*/
		function gm_date($sort) {
			
			$gm_query = xtc_db_query("
										SELECT
											unix_timestamp(gm_" . $this->type . "_date)
										AS
											gm_" . $this->type . "_date
										FROM
											gm_" . $this->type . "_history
										WHERE
											gm_" . $this->type . "_ip	= '" . $this->gm_current_ip . "' 
										ORDER BY 
											gm_" . $this->type . "_date " . $sort . "
										LIMIT 1
									"); 

			$gm_row = xtc_db_fetch_array($gm_query);

			return $gm_row['gm_' . $this->type . '_date'];
		}
		
		
		/*
		*	-> count ip
		*/
		function gm_count() {
			$first_date = $this->gm_date("ASC");
			if(!empty($first_date)) {
				$gm_query = xtc_db_query("
										SELECT
											count(gm_" . $this->type . "_ip) 
										AS 
											count
										FROM
											gm_" . $this->type . "_history
										WHERE			
											gm_" . $this->type . "_ip = '" . $this->gm_current_ip . "'
										AND
											unix_timestamp(gm_" . $this->type . "_date) BETWEEN " . $first_date . " AND " . ($first_date + $this->timeline) . "
										"); 

				$gm_row = xtc_db_fetch_array($gm_query);
			}
			return $gm_row['count'];
		}


		/*
		*	-> get blocked ips
		*/
		function gm_ban() {

			if($this->gm_count() >= $this->tryout) {
				return true;
			} else {
				return false;
			}
		}		


		/*
		*	-> delete old ips
		*/
		function gm_delete($success = false) {
			
			if($success == false) {
				if(($this->gm_date('DESC')) + $this->timeout <= time()) {
					$this->gm_delete_ip();				
				} 		
			} else { 
				$this->gm_delete_ip();				
			}
			return;
		}
	}
MainFactory::load_origin_class('ErrorHandler');