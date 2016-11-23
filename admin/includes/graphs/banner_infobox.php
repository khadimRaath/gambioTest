<?php
/* --------------------------------------------------------------
  banner_infobox.php 2014-03-18 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(banner_infobox.php,v 1.2 2002/05/09); www.oscommerce.com
  (c) 2003	 nextcommerce (banner_infobox.php,v 1.5 2003/08/18); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: banner_infobox.php 899 2005-04-29 02:40:57Z hhgag $)

  Released under the GNU General Public License
  -------------------------------------------------------------- */

require_once(DIR_WS_CLASSES . 'phplot.php');

$stats = array();
$banner_stats_query = xtc_db_query("select dayofmonth(banners_history_date) as name, banners_shown as value, banners_clicked as dvalue from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' and to_days(now()) - to_days(banners_history_date) < " . $days . " order by banners_history_date");
while($banner_stats = xtc_db_fetch_array($banner_stats_query))
{
	$stats[] = array($banner_stats['name'], $banner_stats['value'], $banner_stats['dvalue']);
}

if(sizeof($stats) < 1)
{
	$stats = array(array(date('j'), 0, 0));
}

$graph = new PHPlot(200, 220, DIR_FS_CATALOG . 'cache/banner_infobox-' . $banner_id . '-' . LogControl::get_secure_token() . '.' . $banner_extension);

$graph->SetFileFormat($banner_extension);
$graph->SetIsInline(1);
$graph->SetPrintImage(0);

$graph->draw_vert_ticks = 0;
$graph->SetSkipBottomTick(1);
$graph->SetDrawXDataLabels(0);
$graph->SetDrawYGrid(0);
$graph->SetPlotType('bars');
$graph->SetLabelScalePosition(1);
$graph->SetMarginsPixels(15, 15, 15, 30);

$graph->SetTitle('3 Day Statistics');

$graph->SetDataValues($stats);
$graph->SetDataColors(array('blue', 'red'), array('blue', 'red'));

$graph->DrawGraph();

$graph->PrintImage();