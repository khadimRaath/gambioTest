<?php
/* --------------------------------------------------------------
   stats_customers.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(stats_customers.php,v 1.29 2002/05/16); www.oscommerce.com 
   (c) 2003	 nextcommerce (stats_customers.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: stats_customers.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
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
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="0">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
        </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">
	    <div class="pageHeading"><?php echo HEADING_TITLE; ?></div>
	    <table border="0" width="100%" cellspacing="0" cellpadding="0" class="breakpoint-small">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" style="width: 70px"><?php echo TABLE_HEADING_NUMBER; ?></td>
                <td class="dataTableHeadingContent" style="width: 240px"><?php echo TABLE_HEADING_CUSTOMERS; ?></td>
                <td class="dataTableHeadingContent" style="width: 125px"><?php echo TABLE_HEADING_TOTAL_PURCHASED; ?></td>
              </tr>
<?php
  if ($_GET['page'] > 1) $rows = $_GET['page'] * '20' - '20';
  $customers_query_raw = "select c.customers_firstname, c.customers_lastname, sum(op.final_price) as ordersum from " . TABLE_CUSTOMERS . " c, " . TABLE_ORDERS_PRODUCTS . " op, " . TABLE_ORDERS . " o where c.customers_id = o.customers_id and o.orders_id = op.orders_id and o.orders_status!='".gm_get_conf('GM_ORDER_STATUS_CANCEL_ID')."' group by c.customers_firstname, c.customers_lastname order by ordersum DESC";
  $customers_split = new splitPageResults($_GET['page'], '20', $customers_query_raw, $customers_query_numrows);
  // fix counted customers
  $customers_query_numrows = xtc_db_query("select customers_id from " . TABLE_ORDERS . " group by customers_id");
  $customers_query_numrows = xtc_db_num_rows($customers_query_numrows);

  $customers_query = xtc_db_query($customers_query_raw);
  while ($customers = xtc_db_fetch_array($customers_query)) {
    $rows++;

    if (strlen($rows) < 2) {
      $rows = '0' . $rows;
    }
?>
              <tr class="dataTableRow" data-gx-extension="link" data-link-url="<?php echo xtc_href_link(FILENAME_CUSTOMERS, 'search=' . $customers['customers_lastname'], 'NONSSL'); ?>">
                <td class="dataTableContent numeric_cell"><?php echo $rows; ?>.</td>
                <td class="dataTableContent"><?php echo '<a href="' . xtc_href_link(FILENAME_CUSTOMERS, 'search=' . $customers['customers_lastname'], 'NONSSL') . '">' . $customers['customers_firstname'] . ' ' . $customers['customers_lastname'] . '</a>'; ?></td>
                <td class="dataTableContent numeric_cell"><?php echo $currencies->format($customers['ordersum']); ?></td>
              </tr>
<?php
  }
?>
            </table></td>
          </tr>
		  </table>
	        <table class="gx-container paginator" border="0" width="100%" cellspacing="0" cellpadding="0">
		        <tr>
			        <td class="pagination-control">
				        <?php echo $customers_split->display_count($customers_query_numrows, '20', $_GET['page'],
				                                                   TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?>
				        <span class="page-number-information"><?php echo $customers_split->display_links($customers_query_numrows,
				                                                                                         '20',
				                                                                                         MAX_DISPLAY_PAGE_LINKS,
				                                                                                         $_GET['page']); ?></span>
			        </td>
		        </tr>
	        </table>
        </td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>