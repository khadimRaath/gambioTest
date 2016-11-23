<?php
/* --------------------------------------------------------------
  GMStat.php  2014-06-21 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
  --------------------------------------------------------------
 */

require_once(DIR_FS_ADMIN . DIR_WS_CLASSES . 'phplot.php');

class GMStat_ORIGIN
{
	var $subpage;
	var $objPlot;
	var $date_ini;
	var $date_now;

	function __construct()
	{
		if(file_exists(DIR_FS_ADMIN . 'html/assets/images/legacy/graphs/gm_graph.png'))
		{
			@unlink(DIR_FS_ADMIN . 'html/assets/images/legacy/graphs/gm_graph.png');
		}

		if(file_exists(DIR_FS_CATALOG . 'cache/graph-' . LogControl::get_secure_token() . '.png'))
		{
			@unlink(DIR_FS_CATALOG . 'cache/graph-' . LogControl::get_secure_token() . '.png');
		}

		$this->date_ini = $this->initialDate();
		$this->date_now = date('Y-m-d 23:59:59');

		$this->subpage = $_GET['subpage'];
		$this->objPlot = new PHPlot(gm_get_conf('GM_STAT_PLOT_WIDTH'), gm_get_conf('GM_STAT_PLOT_HEIGHT'));
		return;
	}

	function setGraph()
	{
		$t_graph_file = DIR_FS_CATALOG . 'cache/graph-' . LogControl::get_secure_token() . '.png';

		$this->objPlot->SetIsInline(true);
		$this->objPlot->SetBackgroundColor('#F7F7F7');
		$this->objPlot->SetOutputFile($t_graph_file);
		$this->objPlot->SetPrecisionY(0);

		# Turn off X tick labels and ticks because they don't apply here:
		$this->objPlot->SetXTickLabelPos('none');
		$this->objPlot->SetXTickPos('none');
		$this->objPlot->SetShading(6);

		$this->objPlot->SetXLabelAngle(0);

		$t_plotData = $this->getPlotData();
		$this->objPlot->SetDataValues($t_plotData);
		if(!empty($t_plotData))
		{
			$this->objPlot->DrawGraph();

			if(file_exists($t_graph_file))
			{
				@chmod($t_graph_file, 0777);
			}
		}
		else
		{
			return false;
		}

		return true;
	}

	/*
	 * 	-> get PlotData
	 */
	function getPlotData()
	{
		switch($_GET['action'])
		{

			case 'gm_counter_visitor':
				$this->objPlot->SetPlotType('stackedbars');
				$this->objPlot->SetMarginsPixels(60, 60, 40, 80);
				$this->objPlot->SetDataType('text-data');
				$this->objPlot->SetDataColors('#3A83D0');
				$this->objPlot->SetFont('x_label', 0);
				$this->objPlot->SetFont('y_label', 0);
				$this->objPlot->SetFont('x_title', 0);
				$this->objPlot->SetFont('y_title', 0);
				$this->objPlot->SetXTitle(constant(strtoupper($_GET['action'] . '_X')));
				$this->objPlot->SetYTitle(constant(strtoupper($_GET['action'] . '_Y')));
				$plot_data = $this->getVisits();

				if(count($plot_data) > 10)
				{
					$this->objPlot->SetXLabelAngle(90);
				}

				if(empty($plot_data))
				{
					echo TITLE_PLOT_EMPTY;
				}
				break;

			case 'gm_counter_pages':
				$this->objPlot->SetPlotType('pie');
				$this->objPlot->SetLegendPixels(0, 0);
				$this->objPlot->SetMarginsPixels(100, 10, 10, 10);
				$this->objPlot->SetDataType('text-data-single');
				$this->objPlot->SetDataColors(array('#ffcc00', 'green', 'blue', 'yellow', 'cyan',
					'magenta', 'brown', 'lavender', 'pink',
					'gray', 'orange'));
				$plot_data = $this->getPages();
				$this->objPlot->SetLegendStyle('right', 'left');
				if(empty($plot_data))
				{
					echo TITLE_PLOT_EMPTY;
				}
				else
				{
					foreach($plot_data as $row)
					{
						$this->objPlot->SetLegend(implode(': ', $row));
					}
				}
				break;

			case 'gm_counter_user':
				$this->objPlot->SetPlotType('pie');
				$this->objPlot->SetLegendPixels(0, 0);
				$this->objPlot->SetMarginsPixels(100, 10, 10, 10);
				$this->objPlot->SetDataType('text-data-single');
				$this->objPlot->SetDataColors(array('#ffcc00', 'green', 'blue', 'yellow', 'cyan',
					'magenta', 'brown', 'lavender', 'pink',
					'gray', 'orange'));
				$plot_data = $this->getUserInfo();
				$this->objPlot->SetLegendStyle('right', 'left');
				if(empty($plot_data))
				{
					echo TITLE_PLOT_EMPTY;
				}
				else
				{
					foreach($plot_data as $row)
					{
						$this->objPlot->SetLegend(implode(': ', $row));
					}
				}
				break;

			case 'gm_counter_search':
				$this->objPlot->SetPlotType('pie');
				$this->objPlot->SetLegendPixels(0, 0);
				$this->objPlot->SetMarginsPixels(100, 10, 10, 10);
				$this->objPlot->SetDataType('text-data-single');
				$this->objPlot->SetDataColors(array('#ffcc00', 'green', 'blue', 'yellow', 'cyan',
					'magenta', 'brown', 'lavender', 'pink',
					'gray', 'orange'));
				$plot_data = $this->getSearch();
				$this->objPlot->SetLegendStyle('right', 'left');
				if(empty($plot_data))
				{
					echo TITLE_PLOT_EMPTY;
				}
				else
				{
					foreach($plot_data as $row)
					{
						$this->objPlot->SetLegend(implode(': ', $row));
					}
				}
				break;
		}

		return $plot_data;
	}

	/*
	 * 	-> get Search
	 */
	function getSearch()
	{
		// initial defaults
		if(empty($_GET['gm_count']) || $_GET['gm_count'] == 'undefined')
		{
			$gm_count = 10;
		}
		else
		{
			$gm_count = (int)$_GET['gm_count'];
		}

		if($_GET['subpage'] == 'intern')
		{
			$gm_query = xtc_db_query("
										SELECT
											gm_counter_intern_search_name,
											gm_counter_intern_search_hits

										FROM
											gm_counter_intern_search
										ORDER BY
											gm_counter_intern_search_hits DESC
										LIMIT " . $gm_count . "
										");

			if(xtc_db_num_rows($gm_query) > 0)
			{
				while($gm_row = xtc_db_fetch_array($gm_query))
				{
					$plot_data[] = array($gm_row['gm_counter_intern_search_name'], $gm_row['gm_counter_intern_search_hits']);
				}
			}
		}
		else
		{
			$gm_query = xtc_db_query("
										SELECT
											gm_counter_extern_search_engine,
											gm_counter_extern_search_name,
											gm_counter_extern_search_hits

										FROM
											gm_counter_extern_search
										ORDER BY
											gm_counter_extern_search_hits DESC
										LIMIT " . $gm_count . "
										");

			if(xtc_db_num_rows($gm_query) > 0)
			{
				while($gm_row = xtc_db_fetch_array($gm_query))
				{
					$plot_data[] = array($gm_row['gm_counter_extern_search_name'] . ' [' . $gm_row['gm_counter_extern_search_engine'] . ']', $gm_row['gm_counter_extern_search_hits']);
				}
			}
		}
		return $plot_data;
	}

	/*
	 * 	-> get UserInfo
	 */
	function getUserInfo()
	{
		// initial defaults
		if(empty($_GET['gm_count']) || $_GET['gm_count'] == 'undefined')
		{
			$gm_count = 10;
		}
		else
		{
			$gm_count = (int)$_GET['gm_count'];
		}
		$gm_query = xtc_db_query("
									SELECT
										gm_counter_info_hits,
										gm_counter_info_name
									FROM
										gm_counter_info
									WHERE
										gm_counter_info_type_id = '" . xtc_db_input($_GET['subpage']) . "'
									ORDER BY
										gm_counter_info_hits DESC
									LIMIT " . $gm_count . "

									");

		if(xtc_db_num_rows($gm_query) > 0)
		{
			while($gm_row = xtc_db_fetch_array($gm_query))
			{
				if($_GET['subpage'] == '5')
				{
					$plot_data[] = array($this->getUnknown($this->getCountry($gm_row['gm_counter_info_name'])), $gm_row['gm_counter_info_hits']);
				}
				else
				{
					$plot_data[] = array($this->getUnknown($gm_row['gm_counter_info_name']), $gm_row['gm_counter_info_hits']);
				}
			}
		}
		return $plot_data;
	}

	/*
	 * 	-> get Country
	 */
	function getCountry($country)
	{
		if(strlen($country) > 2 && strstr($country, '-'))
		{
			$array_country = explode('-', $country);
			$country = array_pop($array_country);
		}

		$gm_query = xtc_db_query("
									SELECT
										countries_name
									FROM
										countries
									WHERE
										countries_iso_code_2 = '" . $country . "'
									");

		if(xtc_db_num_rows($gm_query) > 0)
		{
			$gm_row = xtc_db_fetch_array($gm_query);
			return $gm_row['countries_name'];
		}
		else
		{
			return $country;
		}
	}

	/*
	 * 	-> get Pages
	 */
	function getPages()
	{
		// initial defaults
		if(empty($_GET['gm_count']) || $_GET['gm_count'] == 'undefined')
		{
			$gm_count = 10;
		}
		else
		{
			$gm_count = (int)$_GET['gm_count'];
		}

		if(empty($_GET['gm_type']) || $_GET['gm_type'] == 'undefined')
		{
			$gm_type = 'all';
		}
		else
		{
			$gm_type = $_GET['gm_type'];
		}

		if($_GET['subpage'] == 'today')
		{

			$this->objPlot->SetTitle(MENU_TITLE_TODAY);

			// build query - where - types
			if($gm_type != 'all')
			{
				$where_type = "WHERE gm_counter_page_type = '" . $gm_type . "' ";
			}
			if($gm_type == 'content')
			{
				$where_type = "WHERE gm_counter_page_type = 'coid' OR gm_counter_page_type='content' ";
			}


			$gm_query = xtc_db_query("
										SELECT
											gm_counter_page_name,
											gm_counter_page_type
										FROM
											gm_counter_page
											" . $where_type . "
										GROUP BY gm_counter_page_name, gm_counter_page_type
										LIMIT
											" . $gm_count . "
										");

			if(xtc_db_num_rows($gm_query) > 0)
			{
				while($gm_row = xtc_db_fetch_array($gm_query))
				{

					$gm_count_query = xtc_db_query("
													SELECT
														count(*)
													AS
														count
													FROM
														gm_counter_page
													WHERE
														gm_counter_page_name = '" . $gm_row['gm_counter_page_name'] . "'
													");
					$gm_count_row = xtc_db_fetch_array($gm_count_query);

					$plot_data[] = array($this->getPageName($gm_row['gm_counter_page_name'], $gm_row['gm_counter_page_type']), $gm_count_row['count']);
				}
			}
		}
		else
		{

			$this->objPlot->SetTitle(MENU_TITLE_ALL);

			// build query - where - types
			if($gm_type != 'all')
			{
				$where_type = "WHERE gm_counter_page_history_type = '" . $gm_type . "' ";
			}
			if($gm_type == 'content')
			{
				$where_type = "WHERE gm_counter_page_history_type = 'coid' OR gm_counter_page_history_type='content' ";
			}
			$gm_query = xtc_db_query("
										SELECT
											gm_counter_page_history_name,
											gm_counter_page_history_type,
											gm_counter_page_history_hits AS count
										FROM
											gm_counter_page_history
											" . $where_type . "
										GROUP BY
											gm_counter_page_history_name
										ORDER BY
											gm_counter_page_history_hits DESC
										LIMIT
											" . $gm_count . "
										");

			if(xtc_db_num_rows($gm_query) > 0)
			{
				while($gm_row = xtc_db_fetch_array($gm_query))
				{
					$plot_data[] = array($this->getPageName($gm_row['gm_counter_page_history_name'], $gm_row['gm_counter_page_history_type']), $gm_row['count']);
				}
			}
		}
		return $plot_data;
	}

	/*
	 * 	-> get Visits
	 */
	function getPageName($id, $type)
	{

		if($type == 'cat')
		{

			$gm_query = xtc_db_query("
										SELECT
											categories_name
										AS
											name
										FROM
											categories_description
										WHERE
											categories_id = '" . $id . "'
										AND
											language_id = '" . $_SESSION['languages_id'] . "'
										");
		}
		elseif($type == 'prd')
		{

			$gm_query = xtc_db_query("
										SELECT
											products_name
										AS
											name
										FROM
											products_description
										WHERE
											products_id = '" . $id . "'
										AND
											language_id = '" . $_SESSION['languages_id'] . "'
										");
		}
		elseif($type == 'coid')
		{
			$gm_query = xtc_db_query("
										SELECT
											content_title
										AS
											name
										FROM
											content_manager
										WHERE
											content_id = '" . $id . "'
										AND
											languages_id = '" . $_SESSION['languages_id'] . "'
										");
		}
		if(!empty($gm_query))
		{
			if(xtc_db_num_rows($gm_query) == 1)
			{
				$gm_row = xtc_db_fetch_array($gm_query);
				$name = $gm_row['name'];
			}
			else
			{
				$name = $id;
			}

			return $name;
		}

		return $id;
	}

	/*
	 * 	-> get Visits
	 */
	function initialDate()
	{
		// initial date
		if(
				$_GET['gm_start'] == 'undefined' ||
				$_GET['gm_end'] == 'undefined' ||
				empty($_GET['gm_start']) ||
				empty($_GET['gm_end'])
		)
		{
			$end_date = date('Y-m-d 23:59:59');

			if($_GET['subpage'] == 'yearly')
			{
				$start_date = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d'), date('Y') - 1));
			}
			elseif($_GET['subpage'] == 'monthly')
			{
				$start_date = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')));
			}
			else
			{
				$start_date = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
			}
		}
		else
		{
			$start_date = $_GET['gm_start'] . ' 00:00:00';
			$end_date = $_GET['gm_end'] . ' 23:59:59';
		}

		return array('start_date' => $start_date, 'end_date' => $end_date);
	}

	/*
	 * 	-> get Visits
	 */
	function getVisits($type = '')
	{
		$array_start_date = $this->formatDate($this->date_ini['start_date']);
		$array_end_date = $this->formatDate($this->date_ini['end_date']);

		// stat - yearly
		if($_GET['subpage'] == 'yearly')
		{

			$start_date = $array_start_date['year'] . '-01-01 00:00:00';
			$end_date = $array_end_date['year'] . '-12-31 23:59:59';

			$mysql_where = "WHERE gm_counter_id != '1' AND gm_counter_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
			$mysql_group = "GROUP BY DATE_FORMAT(`gm_counter_date`,'%Y')";
			$mysql_col = "sum(gm_counter_visits_total) AS gm_counter_visits_total";

			$plot_data = $this->getQuery($mysql_where, $mysql_group, $mysql_col, "Y");

			$this->objPlot->SetTitle(MENU_TITLE_YEARLY . " " . MENU_TITLE_FROM_YEAR . " " . $array_start_date['year'] . " " . MENU_TITLE_TO_YEAR . " " . $array_end_date['year']);

			// stat - monthly
		}
		else if($_GET['subpage'] == 'monthly')
		{
			$start_date = $array_start_date['year'] . '-' . $array_start_date['month'] . '-01 00:00:00';
			$end_date = $array_end_date['year'] . '-' . $array_end_date['month'] . '-' . $this->formatDate($this->date_ini['end_date'], "t") . ' 23:59:59';

			$mysql_where = "WHERE gm_counter_id != '1' AND gm_counter_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
			$mysql_group = "GROUP BY DATE_FORMAT(`gm_counter_date`,'%Y-%m')";
			$mysql_col = "sum(gm_counter_visits_total) AS gm_counter_visits_total";

			$plot_data = $this->getQuery($mysql_where, $mysql_group, $mysql_col, "F y");

			$this->objPlot->SetTitle(MENU_TITLE_MONTHLY . " " . MENU_TITLE_FROM . " " . $this->formatDate($this->date_ini['start_date'], "F y") . " " . MENU_TITLE_TO . " " . $this->formatDate($this->date_ini['end_date'], "F y"));

			// stat - daily
		}
		else
		{

			$mysql_where = "WHERE gm_counter_id != '1' AND gm_counter_date BETWEEN '" . $this->date_ini['start_date'] . "' AND '" . $this->date_ini['end_date'] . "'";
			$mysql_col = "gm_counter_visits_total";

			$plot_data = $this->getQuery($mysql_where, '', $mysql_col, "d.m.y");
			$this->objPlot->SetTitle(MENU_TITLE_DAILY . " " . MENU_TITLE_FROM . " " . $this->formatDate($this->date_ini['start_date'], "d.m.Y") . " " . MENU_TITLE_TO . " " . $this->formatDate($this->date_ini['end_date'], "d.m.Y"));
		}

		return $plot_data;
	}

	/*
	 * 	-> get Data
	 */
	function getQuery($mysql_where, $mysql_group, $mysql_col, $format)
	{
		$gm_query = xtc_db_query("
										SELECT
											gm_counter_date,
											" . $mysql_col . "
										FROM
											gm_counter_visits
										" . $mysql_where . "
										" . $mysql_group . "
										ORDER by
											gm_counter_date ASC
									");
		if(xtc_db_num_rows($gm_query) > 0)
		{
			while($gm_row = xtc_db_fetch_array($gm_query))
			{
				$plot_data[] = array($this->formatDate($gm_row['gm_counter_date'], $format), $gm_row['gm_counter_visits_total']);
			}
		}

		return $plot_data;
	}

	/*
	 * 	-> format date
	 */
	function formatDate($raw_date, $format = '')
	{
		$year = substr($raw_date, 0, 4);
		$month = substr($raw_date, 5, 2);
		$day = (int)substr($raw_date, 8, 2);
		$hour = (int)substr($raw_date, 11, 2);
		$minute = (int)substr($raw_date, 14, 2);
		$second = (int)substr($raw_date, 17, 2);

		if(empty($format))
		{

			return(
					array(
						'year' => $year,
						'month' => $month
					)
					);
		}
		else
		{
			if(@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year)
			{
				return date($format, mktime($hour, $minute, $second, $month, $day, $year));
			}
			else
			{
				return preg_replace('/2037' . '$/', $year, date($format, mktime($hour, $minute, $second, $month, $day, 2037)));
			}
		}
	}

	/*
	 * 	-> get/set
	 */
	function update()
	{
		if($_GET['action'] == 'gm_counter_update')
		{

			if(!empty($_GET['GM_COUNTER_IP_BARRIER']))
			{
				gm_set_conf('GM_COUNTER_IP_BARRIER', $_GET['GM_COUNTER_IP_BARRIER']);
			}


			$gm_query = xtc_db_query("
										SELECT
											sum(gm_counter_visits_total)
										AS
											hits
										FROM
											gm_counter_visits
										WHERE
											gm_counter_id != '1'
				");

			if(xtc_db_num_rows($gm_query) > 0)
			{
				$gm_hits = xtc_db_fetch_array($gm_query);
			}

			$new_hits = $_GET['gm_counter_visits_total'] - $gm_hits['hits'];

			$gm_id_query = xtc_db_query("
												SELECT
													gm_counter_id
												FROM
													gm_counter_visits
												WHERE
													gm_counter_id = '1'
				");
			if(xtc_db_num_rows($gm_id_query) > 0)
			{
				xtc_db_query("
												UPDATE
														gm_counter_visits
													SET
														gm_counter_visits_total	= '" . $new_hits . "',
														gm_counter_date			= '" . xtc_db_input($_GET['gm_counter_date']) . ' 00:00:00' . "'
													WHERE
														gm_counter_id = '1'
													LIMIT 1
					");
			}
			else
			{
				xtc_db_query("
												UPDATE
														gm_counter_visits
													SET
														gm_counter_visits_total	= '" . $new_hits . "',
														gm_counter_date			= '" . xtc_db_input($_GET['gm_counter_date']) . ' 00:00:00' . "'
													ORDER BY
														gm_counter_date
													ASC
													LIMIT 1
					");
			}
			echo '<b style="color:#339900">' . PROCEED . '</b> <b>' . $error . '</b>';
		}

		if($_GET['action'] == 'gm_counter_conf')
		{
			$gm_query = xtc_db_query("
												SELECT
													sum(gm_counter_visits_total)
												AS
													hits
												FROM
													gm_counter_visits
				");

			if(xtc_db_num_rows($gm_query) == 1)
			{

				$gm_conf = xtc_db_fetch_array($gm_query);
			}


			$gm_date_query = xtc_db_query("
												SELECT
													unix_timestamp(gm_counter_date) AS date
												FROM
													gm_counter_visits
												WHERE
													gm_counter_id = '1'
				");

			if((int)xtc_db_num_rows($gm_date_query) > 0)
			{

				$gm_date = xtc_db_fetch_array($gm_date_query);
			}
			else
			{
				$gm_dates_query = xtc_db_query("
												SELECT
													unix_timestamp(gm_counter_date) AS date
												FROM
													gm_counter_visits
												ORDER BY
													gm_counter_date
												ASC
												LIMIT 1
												");
				if((int)xtc_db_num_rows($gm_dates_query) > 0)
				{
					$gm_date = xtc_db_fetch_array($gm_dates_query);
				}
			}

			include(DIR_FS_ADMIN . 'includes/gm/gm_counter/gm_counter_conf.php');
		}

		return;
	}

	/*
	 * 	-> get Country
	 */
	function getUnknown($name)
	{
		if($name == 'UNKNOWN' || $name == '' || empty($name))
		{
			return TITLE_UNKNOWN;
		}
		else
		{
			return $name;
		}
	}

}
MainFactory::load_origin_class('GMStat');
