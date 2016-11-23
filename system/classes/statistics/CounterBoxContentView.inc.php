<?php
/* --------------------------------------------------------------
   CounterBoxContentView.inc.php 2014-07-17 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(languages.php,v 1.14 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (languages.php,v 1.8 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: languages.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_date_short.inc.php');

class CounterBoxContentView extends ContentView
{
	public function CounterBoxContentView()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_gm_counter.html');
		$this->set_caching_enabled(false);
	}

	public function prepare_data()
	{
		$t_result = xtc_db_query("
								SELECT
									sum(gm_counter_visits_total) AS hits
								FROM
									gm_counter_visits
								");

		if(xtc_db_num_rows($t_result) > 0) {
			$t_hits_array = xtc_db_fetch_array($t_result);		
		}

		$t_result = xtc_db_query("
								SELECT
									gm_counter_date	AS date
								FROM
									gm_counter_visits
								WHERE
									gm_counter_id = '1'											
								");

		if((int)xtc_db_num_rows($t_result) > 0) 
		{
			$t_date_array = xtc_db_fetch_array($t_result);		
		}
		else
		{
			$t_date_arrays_query = xtc_db_query("
									SELECT
										gm_counter_date	AS date
									FROM
										gm_counter_visits
									ORDER BY
										gm_counter_date ASC
									LIMIT 1
									");
			if((int)xtc_db_num_rows($t_date_arrays_query) > 0) 
			{
				$t_date_array = xtc_db_fetch_array($t_date_arrays_query);		
			}
		}

		$t_result = xtc_db_query("
								SELECT
									count(*) AS online
								FROM
									whos_online
								");

		if(xtc_db_num_rows($t_result) > 0)
		{
			$t_online_array = xtc_db_fetch_array($t_result);		
		}

		$this->content_array['GM_HITS'] = $t_hits_array['hits'];
		$this->content_array['GM_DATE'] = xtc_date_short($t_date_array['date']);
		$this->content_array['GM_ONLINE'] = $t_online_array['online'];
	}
}