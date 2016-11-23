<?php
/* --------------------------------------------------------------
   paypal_listtransactions.php 2009-12-16 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

*
 * Project:   	xt:Commerce - eCommerce Engine
 * @version $Id   
 *
 * xt:Commerce - Shopsoftware
 * (c) 2003-2007 xt:Commerce (Winger/Zanier), http://www.xt-commerce.com
 *
 * xt:Commerce ist eine gesch�tzte Handelsmarke und wird vertreten durch die xt:Commerce GmbH (Austria)
 * xt:Commerce is a protected trademark and represented by the xt:Commerce GmbH (Austria)
 *
 * @copyright Copyright 2003-2007 xt:Commerce (Winger/Zanier), www.xt-commerce.com
 * @copyright based on Copyright 2002-2003 osCommerce; www.oscommerce.com
 * @copyright Porttions Copyright 2003-2007 Zen Cart Development Team
 * @copyright Porttions Copyright 2004 DevosC.com
 * @license http://www.xt-commerce.com.com/license/2_0.txt GNU Public License V2.0
 * 
 * For questions, help, comments, discussion, etc., please join the
 * xt:Commerce Support Forums at www.xt-commerce.com
 * 
 */
if (isset($error)) {
	?>
	<table style="margin-bottom: 1px;" border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr><td class="dataTableContent" style="font-size: 12px; background-color: green; color: white; padding: 3px;"><?php echo $error; ?></td></tr>
	</table>
	<?php
}
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="10">&nbsp;</td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_STATUS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYPAL_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TXN_TYPE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENT_TYPE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENT_STATUS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PENDING_REASON; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENT_AMOUNT; ?></td>
                <td class="dataTableHeadingContent" align="right">&nbsp;</td>
              </tr>
<?php


  if (xtc_not_null($selected_status)) {
    $ipn_search = "and p.payment_status = '" . xtc_db_prepare_input($selected_status) . "'";
    switch ($selected_status) {
      case 'Pending':
      case 'Completed':
      default:
//     	$order_by = ' ORDER BY payment_date DESC';
      	$order_by = ' ORDER BY xtc_order_id DESC';
        $ipn_query_raw = "select p.xtc_order_id,p.mc_authorization,p.mc_captured, p.paypal_ipn_id, p.txn_type, p.payment_type, p.payment_status, p.pending_reason, p.payer_status, p.mc_currency, p.date_added, p.mc_gross, p.first_name, p.last_name, p.payer_business_name, p.parent_txn_id, p.txn_id,o.orders_status from " . TABLE_PAYPAL . " as p, " .TABLE_ORDERS . " as o  where o.orders_id = p.xtc_order_id AND p.txn_type = 'expresscheckout' " . $ipn_search . $order_by;
        break;
    }
  } else {
// 		$order_by = ' ORDER BY payment_date DESC';
  		$order_by = ' ORDER BY xtc_order_id DESC';
			$ipn_query_raw = "select p.xtc_order_id,p.mc_authorization,p.mc_captured, p.paypal_ipn_id, p.txn_type, p.payment_type, p.payment_status, p.pending_reason, p.payer_status, p.mc_currency, p.date_added, p.mc_gross, p.first_name, p.last_name, p.payer_business_name, p.parent_txn_id, p.txn_id,o.orders_status from " . TABLE_PAYPAL . " as p left join " .TABLE_ORDERS . " as o on o.orders_id = p.xtc_order_id WHERE p.txn_type = 'expresscheckout'" . $order_by;
  }
  $ipn_split = new splitPageResults($_GET['page'], '20', $ipn_query_raw, $ipn_query_numrows);

  $ipn_query_raw = xtc_db_query($ipn_query_raw);
  while ($ipn_data = xtc_db_fetch_array($ipn_query_raw)) {
    
    if ($ipn_data['txn_id']!='') {
    
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xtc_href_link(FILENAME_PAYPAL, 'view=detail&paypal_ipn_id=' . $ipn_data['paypal_ipn_id']) . '\'">' . "\n";
   
?>
                <td class="dataTableContent" nowrap><?php echo $paypal->getStatusSymbol($ipn_data['payment_status'],$ipn_data['txn_type'],$ipn_data['pending_reason']); ?></td>
                <td class="dataTableContent" nowrap><?php echo $ipn_data['xtc_order_id']; ?></td>
                <td class="dataTableContent" nowrap><?php echo xtc_get_orders_status_name($ipn_data['orders_status']); ?></td>
                
                <td class="dataTableContent" nowrap><?php echo $ipn_data['txn_id']; ?></td>
                <td class="dataTableContent" nowrap><?php echo $ipn_data['first_name'] . ' ' . $ipn_data['last_name'] . ($ipn_data['payer_business_name'] != '' ? '<br />' . $ipn_data['payer_business_name'] : ''); ?>&nbsp;</td>
                <td class="dataTableContent" nowrap><?php echo $ipn_data['txn_type'] . '<br />'; ?>
                <td class="dataTableContent" nowrap><?php echo $ipn_data['payment_type']; ?>&nbsp;</td>
                <td class="dataTableContent" nowrap><?php echo $paypal->getStatusName($ipn_data['payment_status'],$ipn_data['txn_type']);

                ?></td>
                <td class="dataTableContent" nowrap><?php echo $ipn_data['pending_reason']; ?>&nbsp;</td>
                <td class="dataTableContent" nowrap><?php 
                
                if ($ipn_data['pending_reason']=='authorization') {
                	echo $ipn_data['mc_authorization'] . ' / ('.$ipn_data['mc_captured'].') '.$ipn_data['mc_currency']; 
                } else {
                	echo $ipn_data['mc_currency'] . ' '.number_format((double)$ipn_data['mc_gross'], 2); 
                }
                
                ?></td>
                
                <td class="dataTableContent" align="right"><?php echo '<a href="' . xtc_href_link(FILENAME_PAYPAL, 'view=detail'.'&paypal_ipn_id=' . $ipn_data['paypal_ipn_id']) . '">' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/page_find.gif', IMAGE_ICON_INFO) . '</a>'; ?>&nbsp;</td>
              </tr>
<?php
    }
  }
?>
              <tr>
                <td colspan="8"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $ipn_split->display_count($ipn_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PAYPAL_TRANSACTIONS); ?></td>
                    <td class="smallText" align="right"><?php echo $ipn_split->display_links($ipn_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>

            </table></td>

          </tr>
        </table>