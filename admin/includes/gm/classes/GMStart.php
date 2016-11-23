<?php
/* --------------------------------------------------------------
  GMStart.php  2014-10-20 gm
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
require_once(DIR_FS_ADMIN . 'includes/gm/classes/GMStat.php');
require_once(DIR_FS_CATALOG . 'includes/classes/xtcPrice.php');
require_once(DIR_FS_CATALOG . 'gm/inc/gm_xtc_href_link.inc.php');
require_once(DIR_FS_INC . 'xtc_product_link.inc.php');

MainFactory::load_class('GMStat');

class GMStart_ORIGIN extends GMStat
{
	var $gm_rates;
	var $objPlot;
	var $date_ini;
	var $date_now;
	var $date_today;
	var $plot_data;
	var $price;
	var $gmSEOBoost;
	var $rates;

	/*
	 * 	-> constructor
	 */

	function __construct()
	{
		if(file_exists(DIR_FS_ADMIN . 'html/assets/images/legacy/graphs/gm_graph.png'))
		{
			@unlink(DIR_FS_ADMIN . 'html/assets/images/legacy/graphs/gm_graph.png');
		}

		$this->date_ini = parent::initialDate();
		$this->date_now = date('Y-m-d 23:59:59');
		$this->date_today = date('Y-m-d 00:00:00');
		$this->objPlot = new PHPlot(270, 260);
		$this->plot_data = parent::getVisits();
		$this->plot_data = parent::getVisits();
		$this->gmSEOBoost = MainFactory::create_object('GMSEOBoost');
		$this->rates = $this->getRates();

		return;
	}

	/*
	 * 	-> statistic
	 */

	function getStatistic()
	{
		$t_graph_file = DIR_FS_CATALOG . 'cache/graph-' . LogControl::get_secure_token() . '.png';

		// defaults
		$this->objPlot->SetIsInline(true);
		$this->objPlot->SetYLabelType("data");
		$this->objPlot->SetPrecisionY(0);
		$this->objPlot->SetBackgroundColor('#d6e6f3');
		$this->objPlot->SetDataColors('#3A83D0');
		$this->objPlot->SetOutputFile($t_graph_file);
		$this->objPlot->SetXTickLabelPos('none');
		$this->objPlot->SetXTickPos('none');
		$this->objPlot->SetXLabelAngle(0);
		$this->objPlot->SetPlotType('stackedbars');
		$this->objPlot->SetMarginsPixels(60, 0, 30, 30);
		$this->objPlot->SetDataType('text-data');
		$this->objPlot->SetFont('y_label', 0, 8);
		$this->objPlot->SetFont('x_label', 0, 8);
		$this->objPlot->SetFont('y_title', 0);
		$this->objPlot->SetFont('title', 0);

		// data
		if($_GET['do'] == 'visits')
		{
			$this->objPlot->SetYTitle(constant("GM_START_TITLE_Y_" . strtoupper($_GET['do'])));
			$this->objPlot->SetTitle(constant("GM_START_TITLE_" . strtoupper($_GET['do'])));
			$this->objPlot->SetDataValues($this->plot_data);
		}
		else
		{
			$data_array = array(
				0 => array(0 => parent::formatDate($this->date_ini['start_date'], "d.m.Y"), 1 => $this->rates[strtoupper($_GET['do'])]['YESTERDAY']),
				1 => array(0 => parent::formatDate($this->date_today, "d.m.Y"), 1 => $this->rates[strtoupper($_GET['do'])]['TODAY'])
			);

			$this->objPlot->SetYTitle(constant("GM_START_TITLE_Y_" . strtoupper($_GET['do'])));
			$this->objPlot->SetTitle(constant("GM_START_TITLE_" . strtoupper($_GET['do'])));
			$this->objPlot->SetDataValues($data_array);
		}


		// output			
		$this->objPlot->DrawGraph();
		if(file_exists($t_graph_file))
		{
			@chmod($t_graph_file, 0777);
		}
		return '<img src="' . DIR_WS_CATALOG . 'cache/graph-' . LogControl::get_secure_token() . '.png' . '?img_id=' . time() . '">';
	}

	/*
	 * 	-> get top listing
	 */
	function getTopListing()
	{
		// get top search intern
		$gm_array = array();
		$gm_query = xtc_db_query("
										SELECT 
											gm_counter_intern_search_name AS name,
											gm_counter_intern_search_hits AS hits
										FROM
											gm_counter_intern_search
										ORDER by
											gm_counter_intern_search_hits DESC
										LIMIT 5
									");

		while($gm_row = xtc_db_fetch_array($gm_query))
		{
			$gm_listing['search_intern'][] = '<a href="' . gm_xtc_href_link('advanced_search_result.php', 'keywords=' . $gm_row['name']) . '">' . htmlspecialchars_wrapper($this->truncate($gm_row['name'], 15, $gm_row['hits'])) . '</a>' . ' (' . $gm_row['hits'] . ')';
		}

		// get top search extern
		$gm_array = array();
		$gm_query = xtc_db_query("
										SELECT 
											gm_counter_extern_search_name AS name,
											gm_counter_extern_search_hits AS hits
										FROM
											gm_counter_extern_search
										ORDER by
											gm_counter_extern_search_hits DESC
										LIMIT 5
									");

		while($gm_row = xtc_db_fetch_array($gm_query))
		{
			$gm_listing['search_extern'][] = htmlspecialchars_wrapper($this->truncate($gm_row['name'], 15, $gm_row['hits'])) . ' (' . $gm_row['hits'] . ')';
		}


		// get top article sold
		$gm_array = array();
		$gm_query = xtc_db_query("
										SELECT 
											p.products_id, 
											p.products_ordered, 
											pd.products_name
										FROM
											" . TABLE_PRODUCTS_DESCRIPTION . " pd,
											" . TABLE_PRODUCTS . " p
										WHERE
											pd.products_id = p.products_id 
										AND 
											pd.language_id = '" . $_SESSION['languages_id'] . "' 
										AND 
											p.products_ordered > 0 
										GROUP BY
											pd.products_id 
										ORDER BY 
											p.products_ordered DESC, pd.products_name
										LIMIT 5
									");

		while($gm_row = xtc_db_fetch_array($gm_query))
		{

			if($this->gmSEOBoost->boost_active())
			{
				//$gm_product_link = xtc_href_link($gmSEOBoost->get_boosted_product_url($array['products_id'], $array['products_name']) );
				$gm_listing['article_sold'][] = '<a href="' . gm_xtc_href_link($this->gmSEOBoost->get_boosted_product_url($gm_row['products_id'], $gm_row['products_name'])) . '">' . $this->truncate($gm_row['products_name'], 15, (int)$gm_row['products_ordered']) . '</a>' . ' (' . (double)$gm_row['products_ordered'] . ')';
			}
			else
			{
				$gm_listing['article_sold'][] = '<a href="' . gm_xtc_href_link('product_info.php', xtc_product_link($gm_row['products_id'], $gm_row['products_name'])) . '">' . $this->truncate($gm_row['products_name'], 15, (int)$gm_row['products_ordered']) . '</a>' . ' (' . (double)$gm_row['products_ordered'] . ')';
			}
		}
		return $gm_listing;
	}

	/*
	 * 	-> truncate
	 */
	function truncate($str, $limit, $info)
	{
		if($_SESSION['screen_width'] > 980)
		{
			$limit = 25;
		}
		if((strlen_wrapper($str) + 3 + strlen_wrapper($info)) <= $limit)
		{
			return $str;
		}
		else
		{
			return substr_wrapper($str, 0, $limit, $_SESSION['language_charset']) . '...';
		}
	}

	/*
	 * 	-> get rates
	 */
	function getRates()
	{
		// get orders
		$gm_array = array();
		$gm_query = xtc_db_query("
										SELECT 
											count(*) 
										AS
											count
										FROM
											orders
										WHERE
											date_purchased >= '" . $this->date_today . "'
										AND 
											orders_status != '" . (int)gm_get_conf('GM_ORDER_STATUS_CANCEL_ID') . "'

									");

		$gm_array = xtc_db_fetch_array($gm_query);

		$this->gm_rates['ORDERS']['TODAY'] = $gm_array['count'];

		$gm_array = array();
		$gm_query = xtc_db_query("
										SELECT 
											count(*)											
										AS
											count
										FROM
											orders
										WHERE
											date_purchased 
										BETWEEN '" . $this->date_ini['start_date'] . "' 
										AND '" . $this->date_today . "'	
										AND 
											orders_status != '" . (int)gm_get_conf('GM_ORDER_STATUS_CANCEL_ID') . "'
										
									");

		$gm_array = xtc_db_fetch_array($gm_query);

		$this->gm_rates['ORDERS']['YESTERDAY'] = $gm_array['count'];
		$this->gm_rates['ORDERS']['DIFFERENCE'] = $this->calc('ORDERS');


		//get visitors			
		$this->gm_rates['VISITORS']['TODAY'] = $this->plot_data[1][1];
		$this->gm_rates['VISITORS']['YESTERDAY'] = $this->plot_data[0][1];
		$this->gm_rates['VISITORS']['DIFFERENCE'] = $this->calc('VISITORS');


		// get page hits
		$gm_array = array();
		$gm_query = xtc_db_query("
										SELECT 
											count(*) 
										AS
											count
										FROM
											gm_counter_page
										WHERE
											gm_counter_page_date >= '" . $this->date_today . "'										
									");

		$gm_array = xtc_db_fetch_array($gm_query);

		$this->gm_rates['HITS']['TODAY'] = $gm_array['count'];
		$gm_array = array();
		$gm_query = xtc_db_query("
										SELECT 
											count(*) 
										AS
											count
										FROM
											gm_counter_page
										WHERE
											gm_counter_page_date < '" . $this->date_today . "'	
										AND
											gm_counter_page_date >= '" . $this->date_ini['start_date'] . "'	
									");

		$gm_array = xtc_db_fetch_array($gm_query);

		$this->gm_rates['HITS']['YESTERDAY'] = $gm_array['count'];
		$this->gm_rates['HITS']['DIFFERENCE'] = $this->calc('HITS');


		// sales report

		$this->gm_rates['SALES']['TODAY'] = $this->sales("date_purchased >= '" . $this->date_today . "'");
		$this->gm_rates['SALES']['YESTERDAY'] = $this->sales("date_purchased BETWEEN '" . $this->date_ini['start_date'] . "' AND '" . $this->date_today . "'");
		$this->gm_rates['SALES']['DIFFERENCE'] = $this->calc('SALES');

		return $this->gm_rates;
	}

	/*
	 * 	-> calc +/- and defaults
	 */
	function sales($where)
	{
		$gm_query = xtc_db_query("
							SELECT 
								orders_id
							AS
								id
							FROM
								orders
							WHERE
								" . $where . "

						");

		while($gm_row = xtc_db_fetch_array($gm_query))
		{
			$gm_orders[] = $gm_row;
		}

		if(!empty($gm_orders))
		{
			foreach($gm_orders as $order)
			{

				$gm_query = xtc_db_query("
									SELECT 										
										ot.value,
										o.currency,
										o.customers_status
									FROM
										orders_total ot,									
										orders o
									WHERE
										ot.orders_id	= '" . $order['id'] . "'
									AND
										o.orders_id		= '" . $order['id'] . "'
									AND 
										class			= 'ot_total'
									AND 
										o.orders_status != '" . (int)gm_get_conf('GM_ORDER_STATUS_CANCEL_ID') . "'
								");

				while($gm_row = xtc_db_fetch_array($gm_query))
				{


					$gm_squery = xtc_db_query("
										SELECT 										
											ot.value
										FROM
											orders_total ot
										WHERE
											ot.orders_id	= '" . $order['id'] . "'
										AND 
											class			= 'ot_shipping'
									");

					$shipping = xtc_db_fetch_array($gm_squery);

					$this->price = new xtcPrice($gm_row['currency'], $gm_row['customers_status']);
					$brutto = $this->price->xtcFormat($this->price->xtcRemoveCurr($gm_row['value']), 0);
					$shipping = $this->price->xtcFormat($this->price->xtcRemoveCurr($shipping['value']), 0);

					$sum_total += $brutto - $shipping;
					$shipping = 0;
					$brutto = 0;
					unset($this->price);
				}
			}
		}
		if(empty($sum_total) || $sum_total <= 0)
		{
			return 0;
		}
		else
		{
			return $sum_total;
		}
	}

	/*
	 * 	-> calc +/- and defaults
	 */
	function calc($type)
	{
		if(empty($this->gm_rates[$type]['YESTERDAY']) || empty($this->gm_rates[$type]['TODAY']))
		{

			return '-';
		}
		else
		{

			$erg = $this->gm_rates[$type]['TODAY'] - $this->gm_rates[$type]['YESTERDAY'];
			$percent = ($erg / $this->gm_rates[$type]['YESTERDAY']) * 100;

			if($this->gm_rates[$type]['TODAY'] > $this->gm_rates[$type]['YESTERDAY'])
			{
				return '+' . round($percent) . '%';
			}
			else
			{
				return round($percent) . '%';
			}
		}
	}

}
MainFactory::load_origin_class('GMStart');
