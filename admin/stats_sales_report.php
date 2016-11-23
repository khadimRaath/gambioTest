<?php
/* --------------------------------------------------------------
   stats_sales_report.php 2015-09-28 gm
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
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: stats_sales_report.php 1311 2005-10-18 12:30:40Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:

   stats_sales_report (c) Charly Wilhelm  charly@yoshi.ch

   possible views (srView):
  1 yearly
  2 monthly
  3 weekly
  4 daily

  possible options (srDetail):
  0 no detail
  1 show details (products)
  2 show details only (products)

  export
  0 normal view
  1 html view without left and right
  2 csv

  sort
  0 no sorting
  1 product description asc
  2 product description desc
  3 #product asc, product descr asc
  4 #product desc, product descr desc
  5 revenue asc, product descr asc
  6 revenue desc, product descr des

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

// default detail no detail
$srDefaultDetail = 0;
// default view (daily)
$srDefaultView = 2;
// default export
$srDefaultExp = 0;
// default sort
$srDefaultSort = 4;

// report views (1: yearly 2: monthly 3: weekly 4: daily)
if(($_GET['report']) && (xtc_not_null($_GET['report'])))
{
	$srView = $_GET['report'];
}
if($srView < 1 || $srView > 4)
{
	$srView = $srDefaultView;
}

// detail
if(($_GET['detail']) && (xtc_not_null($_GET['detail'])))
{
	$srDetail = $_GET['detail'];
}
if($srDetail < 0 || $srDetail > 2)
{
	$srDetail = $srDefaultDetail;
}

// report views (1: yearly 2: monthly 3: weekly 4: daily)
if(($_GET['export']) && (xtc_not_null($_GET['export'])))
{
	$srExp = $_GET['export'];
}
if($srExp < 0 || $srExp > 2)
{
	$srExp = $srDefaultExp;
}

// item_level
if(($_GET['max']) && (xtc_not_null($_GET['max'])))
{
	$srMax = $_GET['max'];
}
if(!is_numeric($srMax))
{
	$srMax = 0;
}

// order status
$srStatus = array();
if(isset($_GET['orders_status']) && is_array($_GET['orders_status']))
{
	$srStatus = $_GET['orders_status'];
}

// paymenttype
if(($_GET['payment']) && (xtc_not_null($_GET['payment'])))
{
	$srPayment = $_GET['payment'];
}
else
{
	$srPayment = 0;
}

// sort
if(($_GET['sort']) && (xtc_not_null($_GET['sort'])))
{
	$srSort = $_GET['sort'];
}
if($srSort < 1 || $srSort > 6)
{
	$srSort = $srDefaultSort;
}

// check start and end Date
$startDate  = "";
$startDateG = 0;
if(($_GET['startD']) && (xtc_not_null($_GET['startD'])))
{
	$sDay       = $_GET['startD'];
	$startDateG = 1;
}
else
{
	$sDay = 1;
}
if(($_GET['startM']) && (xtc_not_null($_GET['startM'])))
{
	$sMon       = $_GET['startM'];
	$startDateG = 1;
}
else
{
	$sMon = 1;
}
if(($_GET['startY']) && (xtc_not_null($_GET['startY'])))
{
	$sYear      = $_GET['startY'];
	$startDateG = 1;
}
else
{
	$sYear = date("Y");
}
if($startDateG)
{
	$startDate = mktime(0, 0, 0, $sMon, $sDay, $sYear);
}
else
{
	$startDate = mktime(0, 0, 0, date("m"), 1, date("Y"));
}

$endDate  = "";
$endDateG = 0;
if(($_GET['endD']) && (xtc_not_null($_GET['endD'])))
{
	$eDay     = $_GET['endD'];
	$endDateG = 1;
}
else
{
	$eDay = 1;
}
if(($_GET['endM']) && (xtc_not_null($_GET['endM'])))
{
	$eMon     = $_GET['endM'];
	$endDateG = 1;
}
else
{
	$eMon = 1;
}
if(($_GET['endY']) && (xtc_not_null($_GET['endY'])))
{
	$eYear    = $_GET['endY'];
	$endDateG = 1;
}
else
{
	$eYear = date("Y");
}
if($endDateG)
{
	$endDate = mktime(0, 0, 0, $eMon, $eDay + 1, $eYear);
}
else
{
	$endDate = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
}

require(DIR_WS_CLASSES . 'sales_report.php');
$sr = new sales_report($srView, $startDate, $endDate, $srSort, $srStatus, $srFilter, $srPayment);

$startDate = $sr->startDate;
$endDate   = $sr->endDate;

if($srExp < 2)
{
	// not for csv export
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
	if($srExp < 1)
	{
		require(DIR_WS_INCLUDES . 'header.php');
	}
	?>
	<!-- header_eof //-->

	<!-- body //-->
	<form action="" method="get">
	<table border="0" width="100%" cellspacing="2" cellpadding="0">
	<tr>
	<?php
	if($srExp < 1)
	{
		?>
		<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
				<!-- left_navigation //-->
				<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
				<!-- left_navigation_eof //-->
			</table>
		</td>
		<?php
	}
	?>
	<td class="boxCenter" width="100%" valign="top">
	<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/statistik.png)">
		&nbsp;<?php echo HEADING_TITLE; ?>
	</div>
	<table class="breakpoint-large gx-container" border="0" width="100%" cellspacing="0" cellpadding="0">
	<?php
	if($srExp < 1)
	{
		?>
		<tr>
			<td colspan="2">
				<?php include DIR_FS_ADMIN . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'compatibility'
				              . DIRECTORY_SEPARATOR . 'sales_statistics.php'; ?>
			</td>
		</tr>
		<?php
	} // end of ($srExp < 1)
	?>
	<tr>
	<td width=100% valign=top>
	<!--<table border="0" width="100%" cellspacing="0" cellpadding="0">-->
	<!--<tr>-->
	<!--<td valign="top">-->
	<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-compatibility">
	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContent" style="width: 150px"><?php echo TABLE_HEADING_DATE; ?></td>
		<td class="dataTableHeadingContent" style="width: 100px; padding-left: 100px"><?php echo TABLE_HEADING_ORDERS; ?></td>
		<td class="dataTableHeadingContent" style="width: 85px; padding-left: 165px"><?php echo TABLE_HEADING_ITEMS; ?></td>
		<td class="dataTableHeadingContent" style="width: 105px; padding-left: 310px"><?php echo TABLE_HEADING_REVENUE; ?></td>
		<td class="dataTableHeadingContent" style="padding-left: 175px"><?php echo TABLE_HEADING_SHIPPING; ?></td>
	</tr>
	<?php
} // end of if $srExp < 2 csv export

/* ******************************** TaxSumLister start ****************************************************/

/** @var MissingTaxSumImporter $missingTaxSumImporter */
$missingTaxSumImporter = MainFactory::create_object('MissingTaxSumImporter');
$missingTaxSumImporter->import();

/** @var currencies_ORIGIN $taxSumCurrency */
$taxSumCurrency = new currencies();

$txmStartDate = new DateTime(date('Y-m-d', $startDate));
$txmEndDate   = new DateTime(date('Y-m-d', $endDate));

/** @var TaxSumGroup $taxSumGroupToClone */
$taxSumGroupToClone = MainFactory::create_object('TaxSumGroup');

/** @var TaxSumManagerReader $taxSumManager */
$taxSumManager    = MainFactory::create_object('TaxSumManagerReader', array($taxSumGroupToClone));
$taxSumItemsArray = $taxSumManager->getAllTaxSumInfo($txmStartDate, $txmEndDate, $srStatus, $srPayment);

$tableContentTemplateDefaultCurrency = '
	<tr class="dataTableRow">
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent">%s</td>
	</tr>
';

$tableContentTemplateOrgCurrency = '
	<tr class="dataTableRow">
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent numeric_cell">%s</td>
		<td class="dataTableContent">%s</td>
		<td class="dataTableContent numeric_cell">%s</td>
		<td class="dataTableContent numeric_cell">%s</td>
		<td class="dataTableContent numeric_cell">%s</td>
	</tr>
';

$taxSumTableContent = '';
$taxSumCsvContent   = '<br><br><br>';

/** @var TaxSumGroup $taxSumGroup */
foreach($taxSumItemsArray as $taxSumGroup)
{

	$taxClass                = $taxSumGroup->getTaxClass();
	$taxZone                 = $taxSumGroup->getTaxZone();
	$taxRate                 = $taxSumGroup->getTaxRate();
	$sumNetOrgCurrency       = $taxSumGroup->getSumNetOrgCurrency();
	$sumGrossOrgCurrency     = $taxSumGroup->getSumGrossOrgCurrency();
	$sumTaxOrgCurrency       = $taxSumGroup->getSumTaxOrgCurrency();
	$sumNetDefaultCurrency   = $taxSumGroup->getSumNetDefaultCurrency();
	$sumGrossDefaultCurrency = $taxSumGroup->getSumGrossDefaultCurrency();
	$sumTaxDefaultCurrency   = $taxSumGroup->getSumTaxDefaultCurrency();
	$currency                = $taxSumGroup->getCurrency();

	/* Make Price */

	/** @var xtcPrice_ORIGIN $taxSumXtcPrice */
	$taxSumXtcPrice = new xtcPrice($currency, 0);

	$sumGrossOrgCurrencyDisplay     = number_format($sumGrossOrgCurrency, 2,
	                                                $taxSumXtcPrice->currencies[DEFAULT_CURRENCY]['decimal_point'],
	                                                $xtcPrice->currencies[DEFAULT_CURRENCY]['thousand_point']);
	$sumNetOrgCurrencyDisplay       = number_format($sumNetOrgCurrency, 2,
	                                                $taxSumXtcPrice->currencies[DEFAULT_CURRENCY]['decimal_point'],
	                                                $xtcPrice->currencies[DEFAULT_CURRENCY]['thousand_point']);
	$sumTaxOrgCurrencyDisplay       = number_format($sumTaxOrgCurrency, 2,
	                                                $taxSumXtcPrice->currencies[DEFAULT_CURRENCY]['decimal_point'],
	                                                $xtcPrice->currencies[DEFAULT_CURRENCY]['thousand_point']);
	$sumGrossDefaultCurrencyDisplay = number_format($sumGrossDefaultCurrency, 2,
	                                                $taxSumXtcPrice->currencies[DEFAULT_CURRENCY]['decimal_point'],
	                                                $xtcPrice->currencies[DEFDefaultCurrencyAULT_CURRENCY]['thousand_point']);
	$sumNetDefaultCurrencyDisplay   = number_format($sumNetDefaultCurrency, 2,
	                                                $taxSumXtcPrice->currencies[DEFAULT_CURRENCY]['decimal_point'],
	                                                $xtcPrice->currencies[DEFAULT_CURRENCY]['thousand_point']);
	$sumTaxDefaultCurrencyDisplay   = number_format($sumTaxDefaultCurrency, 2,
	                                                $taxSumXtcPrice->currencies[DEFAULT_CURRENCY]['decimal_point'],
	                                                $xtcPrice->currencies[DEFAULT_CURRENCY]['thousand_point']);
	$taxRateDisplay                 = str_replace('.', $taxSumXtcPrice->currencies[DEFAULT_CURRENCY]['decimal_point'],
	                                              (string)(double)$taxRate) . '%';

	$taxSumCsvContent .= "$taxClass;$taxZone;$taxRateDisplay;$currency;$sumNetOrgCurrencyDisplay;$sumGrossOrgCurrencyDisplay;$sumTaxOrgCurrencyDisplay<br>\n";

	$taxSumTableContentDefaultCurrency .= sprintf($tableContentTemplateDefaultCurrency, $taxClass, $taxZone,
	                                              $taxRateDisplay, $currency, $sumNetDefaultCurrencyDisplay,
	                                              $sumGrossDefaultCurrencyDisplay, $sumTaxDefaultCurrencyDisplay);

	$taxSumTableContentOrgCurrency .= sprintf($tableContentTemplateOrgCurrency, $taxClass, $taxZone, $taxRateDisplay,
	                                          $currency, $sumNetOrgCurrencyDisplay, $sumGrossOrgCurrencyDisplay,
	                                          $sumTaxOrgCurrencyDisplay);
}

/* ******************************** TaxSumLister end ****************************************************/

$sum = 0;
while($sr->actDate < $sr->endDate)
{
	$info = $sr->getNext();
	$last = sizeof($info) - 1;
	if($srExp < 2)
	{
		?>
		<tr class="dataTableRow"
		    onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'"
		    onmouseout="this.className='dataTableRow'">
			<?php
			switch($srView)
			{
				case '3':
					?>
					<td class="dataTableContent" align="left"><?php echo xtc_date_long(date("Y-m-d\ H:i:s",
					                                                                        $sr->showDate)) . " - "
					                                                     . xtc_date_short(date("Y-m-d\ H:i:s",
					                                                                           $sr->showDateEnd)); ?></td>
					<?php
					break;
				case '4':
					?>
					<td class="dataTableContent" align="left"><?php echo xtc_date_long(date("Y-m-d\ H:i:s",
					                                                                        $sr->showDate)); ?></td>
					<?php
					break;
				default;
					?>
					<td class="dataTableContent" align="left"><?php echo xtc_date_short(date("Y-m-d\ H:i:s",
					                                                                         $sr->showDate)) . " - "
					                                                     . xtc_date_short(date("Y-m-d\ H:i:s",
					                                                                           $sr->showDateEnd)); ?></td>
					<?php
			}
			?>
			<td class="dataTableContent numeric_cell"><?php echo (int)$info[0]['order']; ?></td>
			<td class="dataTableContent numeric_cell"><?php echo (int)$info[$last - 1]['totitem']; ?></td>
			<td class="dataTableContent numeric_cell"><?php echo $currencies->format($info[$last
			                                                                               - 1]['totsum']); ?></td>
			<td class="dataTableContent numeric_cell" align="left"><?php echo $currencies->format($info[0]['shipping']); ?></td>
		</tr>
		<?php
	}
	else
	{
		// csv export
		echo date(DATE_FORMAT, $sr->showDate) . SR_SEPARATOR1 . date(DATE_FORMAT, $sr->showDateEnd) . SR_SEPARATOR1;
		echo $info[0]['order'] . SR_SEPARATOR1;
		echo $info[$last - 1]['totitem'] . SR_SEPARATOR1;
		echo $currencies->format($info[$last - 1]['totsum']) . SR_SEPARATOR1;
		echo $currencies->format($info[0]['shipping']) . SR_NEWLINE;
		/* CSV TaxSumManager */
		echo($taxSumCsvContent);
	}
	if($srDetail)
	{
		for($i = 0; $i < $last; $i++)
		{
			if($srMax == 0 or $i < $srMax)
			{
				if($srExp < 2)
				{
					?>
					<tr class="dataTableRow"
					    onmouseover="this.className='dataTableRowOver';this.style.cursor='hand'"
					    onmouseout="this.className='dataTableRow'">
						<td class="dataTableContent">&nbsp;</td>
						<td class="dataTableContent" align="left">
							<a href="<?php echo xtc_catalog_href_link("product_info.php?products_id="
							                                          . $info[$i]['pid']) ?>"
							   target="_blank"><?php echo $info[$i]['pmodel'] . ' : ' . $info[$i]['pname']; ?></a>
							<?php
							if(is_array($info[$i]['attr']))
							{
								$attr_info = $info[$i]['attr'];
								foreach($attr_info as $attr)
								{
									echo '<div style="font-style:italic;">&nbsp;' . $attr['quant'] . 'x ';
									//  $attr['options'] . ': '
									$flag = 0;
									foreach($attr['options_values'] as $value)
									{
										if($flag > 0)
										{
											echo "," . $value;
										}
										else
										{
											echo $value;
											$flag = 1;
										}
									}
									$price = 0;
									foreach($attr['price'] as $value)
									{
										$price += $value;
									}
									if($price != 0)
									{
										echo ' (';
										if($price > 0)
										{
											echo "+";
										}
										echo $currencies->format($price) . ')';
									}
									echo '</div>';
								}
							}
							?>                    </td>
						<td class="dataTableContent" align="left"><?php echo $info[$i]['pquant']; ?></td>
						<?php
						if($srDetail == 2)
						{ ?>
							<td class="dataTableContent"
							    align="left"><?php echo $currencies->format($info[$i]['psum']); ?></td>
							<?php
						}
						else
						{ ?>
							<td class="dataTableContent">&nbsp;</td>
							<?php
						}
						?>
						<td class="dataTableContent">&nbsp;</td>
					</tr>
					<?php
				}
				else
				{
					// csv export
					if(is_array($info[$i]['attr']))
					{
						$attr_info = $info[$i]['attr'];
						foreach($attr_info as $attr)
						{
							echo $info[$i]['pname'] . "(";
							$flag = 0;
							foreach($attr['options_values'] as $value)
							{
								if($flag > 0)
								{
									echo ", " . $value;
								}
								else
								{
									echo $value;
									$flag = 1;
								}
							}
							$price = 0;
							foreach($attr['price'] as $value)
							{
								$price += $value;
							}
							if($price != 0)
							{
								echo ' (';
								if($price > 0)
								{
									echo "+";
								}
								else
								{
									echo " ";
								}
								echo $currencies->format($price) . ')';
							}
							echo ")" . SR_SEPARATOR2;
							if($srDetail == 2)
							{
								echo $attr['quant'] . SR_SEPARATOR2;
								echo $currencies->format($attr['quant'] * ($info[$i]['price'] + $price)) . SR_NEWLINE;
							}
							else
							{
								echo $attr['quant'] . SR_NEWLINE;
							}
							$info[$i]['pquant'] = $info[$i]['pquant'] - $attr['quant'];
						}
					}
					if($info[$i]['pquant'] > 0)
					{
						echo $info[$i]['pmodel'] . SR_SEPARATOR2 . $info[$i]['pname'] . SR_SEPARATOR2;
						if($srDetail == 2)
						{
							echo $info[$i]['pquant'] . SR_SEPARATOR2;
							echo $currencies->format($info[$i]['pquant'] * $info[$i]['price']) . SR_NEWLINE;
						}
						else
						{
							echo $info[$i]['pquant'] . SR_NEWLINE;
						}
					}
				}
			}
		}
	}
}
if($srExp < 2)
{
	?>
	</table>
	</td>
	</tr>
	<!--</table>-->
	<!--</td>-->
	<!--</tr>-->

	<tr>
		<td colspan="2">
			<?php
			/* ******************************** TaxSumLister HTML Output start ****************************************************/

			?>

			<div class="tax_sum_manager_wrapper">
							<div id="taxSalesOverviewOriginalCurrency">
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-compatibility">

									<tr class="dataTableHeadingRow">
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_CLASS_TITLE; ?></td>
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ZONE; ?></td>
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_RATE; ?></td>
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENCY; ?></td>
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SUM_NET; ?></td>
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SUM_GROSS; ?></td>
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SUM_TAX; ?></td>
									</tr>

									<?php echo($taxSumTableContentDefaultCurrency); ?>

								</table>
							</div>

							<div id="taxSalesOverviewDefaultCurrency">
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gx-compatibility">

									<tr class="dataTableHeadingRow">
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_CLASS_TITLE; ?></td>
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ZONE; ?></td>
										<td class="dataTableHeadingContent" style="width: 75px;"><?php echo TABLE_HEADING_TAX_RATE; ?></td>
										<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENCY; ?></td>
										<td class="dataTableHeadingContent" style="width: 75px"><?php echo TABLE_HEADING_SUM_NET; ?></td>
										<td class="dataTableHeadingContent" style="width: 85px"><?php echo TABLE_HEADING_SUM_GROSS; ?></td>
										<td class="dataTableHeadingContent" style="width: 80px"><?php echo TABLE_HEADING_SUM_TAX; ?></td>
									</tr>

									<?php echo($taxSumTableContentOrgCurrency); ?>

								</table>
							</div>


			</div>
			<?php

			/* ******************************** TaxSumLister HTML Output end ****************************************************/
			?>
		</td>
	</tr>

	</table>
	</td>
	<!-- body_text_eof //-->
	</tr>
	</table>
	</form>


	<!-- body_eof //-->

	<!-- footer //-->
	<?php
	if($srExp < 1)
	{
		require(DIR_WS_INCLUDES . 'footer.php');
	}
	?>
	<!-- footer_eof //-->
	</body>
	</html>
	<?php
	require(DIR_WS_INCLUDES . 'application_bottom.php');
} // end if $srExp < 2
?>