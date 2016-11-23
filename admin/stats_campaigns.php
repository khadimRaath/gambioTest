<?php
/* --------------------------------------------------------------
   stats_campaigns.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce coding standards; www.oscommerce.com
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: stats_campaigns.php 1179 2005-08-25 12:37:13Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:

   stats_sales_report (c) Charly Wilhelm  charly@yoshi.ch

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');

require (DIR_WS_CLASSES.'currencies.php');
$currencies = new currencies();

require (DIR_WS_CLASSES.'campaigns.php');
$campaign = new campaigns($_GET);

$orders_statuses = array ();
$orders_status_array = array ();
$orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from ".TABLE_ORDERS_STATUS." where language_id = '".$_SESSION['languages_id']."'");
while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
	$orders_statuses[] = array ('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
	$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}

$campaigns = array ();
$campaign_query = "SELECT * FROM ".TABLE_CAMPAIGNS;
$campaign_query = xtc_db_query($campaign_query);
while ($campaign_data = xtc_db_fetch_array($campaign_query)) {
	$campaigns[] = array ('id' => $campaign_data['campaigns_refID'], 'text' => $campaign_data['campaigns_name']);
}

// report views (1: yearly 2: monthly 3: weekly 4: daily)
if (($_GET['report']) && (xtc_not_null($_GET['report']))) {
	$srView = $_GET['report'];
}
if ($srView < 1 || $srView > 4) {
	$srView = $srDefaultView;
}

// check start and end Date
$startDate = "";
$startDateG = 0;
if (($_GET['startD']) && (xtc_not_null($_GET['startD']))) {
	$sDay = $_GET['startD'];
	$startDateG = 1;
} else {
	$sDay = 1;
}
if (($_GET['startM']) && (xtc_not_null($_GET['startM']))) {
	$sMon = $_GET['startM'];
	$startDateG = 1;
} else {
	$sMon = 1;
}
if (($_GET['startY']) && (xtc_not_null($_GET['startY']))) {
	$sYear = $_GET['startY'];
	$startDateG = 1;
} else {
	$sYear = date("Y");
}
if ($startDateG) {
	$startDate = mktime(0, 0, 0, $sMon, $sDay, $sYear);
} else {
	$startDate = mktime(0, 0, 0, date("m"), 1, date("Y"));
}

$endDate = "";
$endDateG = 0;
if (($_GET['endD']) && (xtc_not_null($_GET['endD']))) {
	$eDay = $_GET['endD'];
	$endDateG = 1;
} else {
	$eDay = 1;
}
if (($_GET['endM']) && (xtc_not_null($_GET['endM']))) {
	$eMon = $_GET['endM'];
	$endDateG = 1;
} else {
	$eMon = 1;
}
if (($_GET['endY']) && (xtc_not_null($_GET['endY']))) {
	$eYear = $_GET['endY'];
	$endDateG = 1;
} else {
	$eYear = date("Y");
}
if ($endDateG) {
	$endDate = mktime(0, 0, 0, $eMon, $eDay +1, $eYear);
} else {
	$endDate = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php


require (DIR_WS_INCLUDES.'header.php');
?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="0">
  <tr>

<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->

    <td class="boxCenter" width="100%" valign="top">
	    <div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/statistik.png)">&nbsp;<?php echo HEADING_TITLE; ?></div>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-container breakpoint-large">
<?php

if ($srExp < 1) {
?>
        <tr>
          <td colspan="2">
            <form action="" method="get" style="margin: 0">
              <?php include DIR_FS_ADMIN . 'html' . DIRECTORY_SEPARATOR . 'compatibility' . DIRECTORY_SEPARATOR . 'campaigns_statistics.php'; ?>
            </form>
          </td>
        </tr>
<?php

} // end of ($srExp < 1)
?>
        <tr>
          <td width=100% valign=top>
 <?php

if (count($campaign->result)) {
?>


	<table border="0" width="100%" cellspacing="0" cellpadding="0" class="add-padding-top-24">
		<tr class="dataTableHeadingRow">
			<td class="dataTableHeadingContent" width="25%"><?php echo HEADING_TOTAL; ?></td>
			<td></td>
			<td class="dataTableHeadingContent" width="10%"><?php echo HEADING_HITS; ?></td>
			<td class="dataTableHeadingContent" width="15%"><?php echo HEADING_LEADS; ?></td>
			<td class="dataTableHeadingContent" width="15%"><?php echo HEADING_SELLS; ?></td>
			<td class="dataTableHeadingContent" width="15%"><?php echo HEADING_LATESELLS; ?></td>
			<td class="dataTableHeadingContent" width="20%"><?php echo HEADING_SUM; ?></td>
		</tr>
		<tr class="dataTableRow">
			<td class="dataTableContent bold" colspan="2" width="25%"><?php echo HEADING_SUM; ?></td>
			<td class="dataTableContent bold" width="10%">&nbsp;</td>
			<td class="dataTableContent bold" width="15%"><?php echo $campaign->total['leads']; ?></td>
			<td class="dataTableContent bold" colspan="2" width="30%"><?php echo $campaign->total['sells']; ?></td>
			<td class="dataTableContent bold" width="20%"><?php echo $campaign->total['sum']; ?></td>
		</tr>
	</table>
 <?php

	// show campaigns

	for ($n = 0; $n < count($campaign->result); $n ++) {
?>
  
  
  
  <table class="add-padding-top-24">
		<tr class="dataTableHeadingRow">
			<td class="dataTableHeadingContent" width="25%"><?php echo $campaign->result[$n]['text'] . ' ' . TEXT_REFERER . ' ('
			                                        . $campaign->result[$n]['id'] . ')'; ?></td>
			<td class="dataTableHeadingContent">&nbsp;</td>
			<td class="dataTableHeadingContent" width="10%"><?php echo HEADING_HITS; ?></td>
			<td class="dataTableHeadingContent" width="15%"><?php echo HEADING_LEADS; ?></td>
			<td class="dataTableHeadingContent" width="15%"><?php echo HEADING_SELLS; ?></td>
			<td class="dataTableHeadingContent" width="15%"><?php echo HEADING_LATESELLS; ?></td>
			<td class="dataTableHeadingContent" width="20%"><?php echo HEADING_SUM; ?></td>
		</tr>
  
  <?php
		// show values
		for ($nn = 0; $nn < count($campaign->result[$n]['result']); $nn ++) {
?>
  
  <tr class="dataTableRow"> 
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['range']; ?></td>
	<td class="dataTableContent">&nbsp;</td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['hits']; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['leads'].' ('.$campaign->result[$n]['result'][$nn]['leads_p'].'%)'; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['sells'].' ('.$campaign->result[$n]['result'][$nn]['sells_p'].'%)'; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['late_sells'].' ('.$campaign->result[$n]['result'][$nn]['late_sells_p'].'%)'; ?></td>
    <td class="dataTableContent"><?php echo $campaign->result[$n]['result'][$nn]['sum'].' ('.$campaign->result[$n]['result'][$nn]['sum_p'].'%)'; ?></td>
 </tr>
  
  <?php

		}
?>
  
  
    <tr class="dataTableRow"> 
    <td class="dataTableContent"><b><?php echo HEADING_SUM; ?></b></td>
    <td class="dataTableContent">&nbsp;</td>
    <td class="dataTableContent"><b><?php echo $campaign->result[$n]['hits_s']; ?></b>&nbsp;</td>
    <td class="dataTableContent"><b><?php echo $campaign->result[$n]['leads_s'].' ('.($campaign->total['leads']> 0 ? ($campaign->result[$n]['leads_s']/$campaign->total['leads']*100):'0').'%)'; ?></b></td>
    <td class="dataTableContent"><b><?php echo $campaign->result[$n]['sells_s'].' ('.($campaign->total['sells']> 0 ? ($campaign->result[$n]['sells_s']/$campaign->total['sells']*100):'0').'%)'; ?></b></td>
    <td class="dataTableContent"><b><?php echo $campaign->result[$n]['late_sells_s'].' ('.($campaign->total['sells']> 0 ? ($campaign->result[$n]['late_sells_s']/$campaign->total['sells']*100):'0').'%)'; ?></b></td>
    <td class="dataTableContent"><b><?php echo $campaign->result[$n]['sum_s'].' ('.($campaign->total['sum_plain']> 0 ? round(($campaign->result[$n]['sum_s']/$campaign->total['sum_plain']*100),0):'0').'%)'; ?></b></td>
  </tr>
  
  
  <?php


	}
?>
</table>
<?php } ?>
          </td>
        </tr>
      </table>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php


	require (DIR_WS_INCLUDES.'footer.php');
?>
<!-- footer_eof //-->
</body>
</html>
<?php

	require (DIR_WS_INCLUDES.'application_bottom.php');
?>
