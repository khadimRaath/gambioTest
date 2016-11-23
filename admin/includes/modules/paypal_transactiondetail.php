<?php
/* --------------------------------------------------------------
   paypal_transactiondetail.php 2009-12-16 gambio
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

$show = true;
if ($_GET['view']=='detail' && isset($_GET['paypal_ipn_id'])) {
	
	$ipn_id = (int)$_GET['paypal_ipn_id'];
	
	$query = "SELECT * FROM paypal WHERE paypal_ipn_id = '".$ipn_id."'";
	$query = xtc_db_query($query);
	$ipn_data = xtc_db_fetch_array($query);	
}

if ($_GET['view']=='detail' && isset($_GET['txn_id'])) {
	$response = $paypal->GetTransactionDetails($_GET['txn_id']);
//	echo '<pre>';
//	print_r ($response);
//	echo '</pre>';
	// error ?
	if ($response['ACK']!='Success') {
	$error = $paypal->getErrorDescription($response['L_ERRORCODE0']);
	$messageStack->add($error,'error');
    $error = $messageStack->output();
    $show = false;
	} else {
		$ipn_data = $paypal->mapResponse($response);	
	}
}

if (isset($error)) {
	?>
	<table style="margin-bottom: 1px;" border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr><td class="dataTableContent" style="font-size: 12px; background-color: green; color: white; padding: 3px;"><?php echo $error; ?></td></tr>
	</table>
	<?php
}

if ($show) {
?>

 <div>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo TEXT_PAYPAL_TRANSACTION_DETAIL; ?>											  
			</td>
		</tr>
	</table>
	<table width="100%" border="0" class="gm_border dataTableRow">
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_TXN_ID; ?></td>
      <td class="main" width="90%"><?php echo $ipn_data['txn_type'].' (Code: '.$ipn_data['txn_id'].')'; ?></td>
   </tr>
    <?php if ($ipn_data['payer_business_name']!='') { ?>
	<tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_COMPANY; ?></td>
      <td class="main" width="90%"><?php echo $ipn_data['payer_business_name']; ?></td>
   </tr>
   <?php } ?>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_PAYER_EMAIL; ?></td>
      <td class="main" width="90%" valign="middle"><?php echo $ipn_data['payer_email'];?></td>
   </tr>
    <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_PAYER_EMAIL_STATUS; ?></td>
      <td class="main" width="90%"><?php echo $paypal->getStatusSymbol($ipn_data['payer_status']).$ipn_data['payer_status']; ?>
      </td>
   </tr>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_RECEIVER_EMAIL; ?></td>
      <td class="main" width="90%"><?php echo $ipn_data['receiver_email']; ?></td>
   </tr>
   <tr> 
      <td class="main" colspan="2"><hr noshade></td>
   </tr>
   <?php if ($ipn_data['pending_reason']=='authorization') { ?>
   	<tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_TOTAL; ?></td>
      <td class="main" width="90%"><?php echo number_format((double)$ipn_data['mc_authorization'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
   </tr>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_CAPTURED; ?></td>
      <td class="main" width="90%"><?php echo number_format((double)$ipn_data['mc_captured'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
   </tr>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_OPEN; ?></td>
      <td class="main" width="90%"><?php echo number_format((double)$ipn_data['mc_authorization']-$ipn_data['mc_captured'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
   </tr>
   <?php } else { ?>
   	<tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_TOTAL; ?></td>
      <td class="main" width="90%"><?php echo number_format((double)$ipn_data['mc_gross'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
   </tr>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_FEE; ?></td>
      <td class="main" width="90%"><?php echo number_format((double)$ipn_data['mc_fee'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
   </tr>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_NETTO; ?></td>
      <td class="main" width="90%"><?php echo number_format((double)$ipn_data['mc_gross']-$ipn_data['mc_fee'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
   </tr>
   <?php }  ?>
   <tr> 
      <td class="main" colspan="2"><hr noshade></td>
   </tr>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_ORDER_ID; ?></td>
      <td class="main" width="90%"><?php echo $ipn_data['xtc_order_id']; ?></td>
   </tr>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_PAYMENT_STATUS; ?></td>
      <td class="main" width="90%"><?php echo $paypal->getStatusName($ipn_data['payment_status'],$ipn_data['txn_type']); ?></td>
   </tr>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_PAYMENT_DATE; ?></td>
      <td class="main" width="90%"><?php echo $ipn_data['payment_date']; ?></td>
   </tr>
   <tr> 
      <td class="main" width="10%" valign="top"><?php echo TEXT_PAYPAL_ADRESS; ?></td>
      <td class="main" width="90%"><?php echo $ipn_data['address_name'].'<br>'.$ipn_data['first_name'].' '.$ipn_data['last_name'].'<br>'.$ipn_data['address_street'].'<br>'.$ipn_data['address_zip'].' '.$ipn_data['address_city'].'<br>'.$ipn_data['address_country']; ?></td>
   </tr>
   <?php if ($ipn_data['address_status']!='' and $ipn_data['address_status']!='None') { ?>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_ADRESS_STATUS; ?></td>
      <td class="main" width="90%"><?php echo $paypal->getStatusSymbol($ipn_data['address_status']).$ipn_data['address_status']; ?></td>
   </tr>
   <?php } ?>
   <tr> 
      <td class="main" width="10%"><?php echo TEXT_PAYPAL_PAYMENT_TYPE; ?></td>
      <td class="main" width="90%"><?php echo $paypal->getPaymentType($ipn_data['payment_type']); ?></td>
   </tr>
   </table>
   <?php
   if($_GET['back'] == 'order') {
	   echo '<a class="button" href="'.xtc_href_link(FILENAME_ORDERS, 'page='.$_GET['page'].'&oID='.$_GET['oID'].'&action='.$_GET['action']).'">'.BUTTON_BACK.'</a>';
   } elseif($_GET['back'] == 'paypal') {
	   echo '<a class="button" href="'.xtc_href_link(FILENAME_PAYPAL, 'view=detail&paypal_ipn_id='.$_GET['ipn_back']).'">'.BUTTON_BACK.'</a>';
   } else {
	   echo '<a class="button" href="'.xtc_href_link(FILENAME_PAYPAL).'">'.BUTTON_BACK.'</a>';
   }
   ?>
   </div>
  
   <?php if (isset($ipn_id)) { ?>
         <br />
         <div class="highlightbox">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo TEXT_PAYPAL_OPTIONS; ?>											  
			</td>
		</tr>
	</table>
	<table width="100%" border="0" class="gm_border dataTableRow">
		<tr>
		  <td class="main" width="10%"><?php echo  xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_refund.gif'); ?></td>
		  <td class="main" width="90%"><a href="<?php echo xtc_href_link(FILENAME_PAYPAL,'view=refund&paypal_ipn_id='.$ipn_id); ?>"><?php echo TEXT_PAYPAL_ACTION_REFUND; ?></a></td>
	   </tr>
   <?php if ($ipn_data['pending_reason']=='authorization' || $ipn_data['pending_reason']=='order') { ?>
		   <tr>
			  <td class="main" width="10%"><?php echo  xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/icon_capture.gif'); ?></td>
			  <td class="main" width="90%"><a href="<?php echo xtc_href_link(FILENAME_PAYPAL,'view=capture&paypal_ipn_id='.$ipn_id); ?>"><?php echo TEXT_PAYPAL_ACTION_CAPTURE; ?></a></td>
		   </tr>
		   <tr>
			  <td class="main" width="10%"><?php echo  xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/exclamation.png'); ?></td>
			  <td class="main" width="90%"><a href="<?php echo xtc_href_link(FILENAME_PAYPAL,'view=void&paypal_ipn_id='.$ipn_id); ?>"><?php echo TEXT_PAYPAL_ACTION_AUTHORIZATION; ?></a></td>
		   </tr>
   <?php } ?>
   </table>
   </div>
   <?php } ?>
 
<?php
//}


if ($ipn_data['parent_txn_id'] != '') {
	
	// get original transaction 
	$_orig_query ="SELECT * FROM paypal WHERE txn_id = '".$ipn_data['parent_txn_id']."'";
	$_orig_query = xtc_db_query($_orig_query);
	if (xtc_db_num_rows($_orig_query)>0) {	
?>
   <br />
      <div class="highlightbox">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo TEXT_PAYPAL_TRANSACTION_ORIGINAL; ?>										  
			</td>
		</tr>
	</table>
	<table width="100%" border="0" class="gm_border dataTableRow">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="10">&nbsp;</td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PAYMENT_DATE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYPAL_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_TYPE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PAYMENT_STATUS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_DETAIL; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_TOTAL; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_FEE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_NETTO; ?></td>
              </tr>
 <?php
 while ($conn_data = xtc_db_fetch_array($_orig_query)) {
 	?>
 
 <tr class="dataTableRow">
<td class="dataTableContent" nowrap><?php echo $paypal->getStatusSymbol($conn_data['payment_status'],$conn_data['txn_type']); ?></td>
<td class="dataTableContent" nowrap><?php echo xtc_datetime_short($conn_data['payment_date']); ?></td>
<td class="dataTableContent" nowrap><?php echo $conn_data['txn_id']; ?></td>
<td class="dataTableContent" nowrap><?php echo $paypal->getStatusName($conn_data['payment_status'],$conn_data['txn_type']); ?></td>
<td class="dataTableContent" nowrap><?php echo $conn_data['payment_type']; ?></td>
<td class="dataTableContent" nowrap><?php echo '<a href="'.xtc_href_link(FILENAME_PAYPAL,'view=detail&paypal_ipn_id='.$conn_data['paypal_ipn_id']).'">'.TEXT_PAYPAL_DETAIL.'</a>'; ?></td>
<td class="dataTableContent" nowrap><?php echo $conn_data['mc_gross'].' '.$conn_data['mc_currency']; ?></td>
<td class="dataTableContent" nowrap><?php echo $conn_data['mc_fee'].' '.$conn_data['mc_currency']; ?></td>
<td class="dataTableContent" nowrap><?php echo round($conn_data['mc_gross']-$conn_data['mc_fee'],2).' '.$conn_data['mc_currency']; ?></td>
</tr>
 <?php
		}
		?>

	</table>	
	
      </div>
      
      <?php
} 
}?>


<?php
// show transaction History

	$hist_query = "SELECT * FROM paypal_status_history WHERE paypal_ipn_id='".$ipn_id."'";
	$hist_query = xtc_db_query($hist_query);
	if (xtc_db_num_rows($hist_query)>0) {
?>
   <br />
      <div class="main">
      <div class="highlightbox">
 	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo TEXT_PAYPAL_TRANSACTION_HISTORY; ?>
			</td>
		</tr>
	</table>

	<table width="100%" border="0" cellspacing="0" cellpadding="2" class="gm_border dataTableRow">
             <tr class="dataTableHeadingRow">
              
              <td class="dataTableHeadingContent" width="10">&nbsp;</td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PAYMENT_DATE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PAYMENT_STATUS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PENDING_REASON; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENT_AMOUNT; ?></td>
              </tr>
              
              <?php             
              
		while ($hist_data = xtc_db_fetch_array($hist_query)) {
	
?>
<tr class="dataTableRow">
<td class="dataTableContent" nowrap><?php echo $paypal->getStatusSymbol($hist_data['payment_status'],'',$hist_data['pending_reason']); ?></td>
<td class="dataTableContent" nowrap><?php echo xtc_datetime_short($hist_data['date_added']); ?></td>
<td class="dataTableContent" nowrap><?php echo $paypal->getStatusName($hist_data['payment_status']); ?></td>
<td class="dataTableContent" nowrap><?php echo $hist_data['pending_reason']; ?></td>
<td class="dataTableContent" nowrap><?php echo $hist_data['mc_amount']; ?></td>
</tr>
 <?php
		}
		?>

	</table>	
	
      </div>
      </div>
      
      <?php
} 


// get connected transactions

	// get original transaction 
	$conn_query ="SELECT * FROM paypal WHERE parent_txn_id = '".$ipn_data['txn_id']."' or (txn_id='".$ipn_data['txn_id']."' and paypal_ipn_id != '".$ipn_data['paypal_ipn_id']."') ORDER BY payment_date";
	$conn_query = xtc_db_query($conn_query);
	if (xtc_db_num_rows($conn_query)>0) {
?>
   <br />
      <div class="highlightbox">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo TEXT_PAYPAL_TRANSACTION_CONNECTED; ?>									  
			</td>
		</tr>
	</table>
	<table width="100%" border="0" cellspacing="0" cellpadding="2" class="gm_border dataTableRow">
              <tr class="dataTableHeadingRow">
              	<td class="dataTableHeadingContent" width="10">&nbsp;</td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PAYMENT_DATE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYPAL_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_TYPE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PAYMENT_STATUS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_DETAIL; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_TOTAL; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_FEE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_NETTO; ?></td>
              </tr>
<?php

		while ($conn_data = xtc_db_fetch_array($conn_query)) {
	
?>
<tr class="dataTableRow">
<td class="dataTableContent" nowrap><?php echo $paypal->getStatusSymbol($conn_data['payment_status'],$conn_data['txn_type']); ?></td>
<td class="dataTableContent" nowrap><?php echo xtc_datetime_short($conn_data['payment_date']); ?></td>
<td class="dataTableContent" nowrap><?php echo $conn_data['txn_id']; ?></td>
<td class="dataTableContent" nowrap><?php echo ($conn_data['payment_type'] != '' ? $conn_data['payment_type'] : '&nbsp;'); ?></td>
<td class="dataTableContent" nowrap><?php echo $paypal->getStatusName($conn_data['payment_status'],$conn_data['txn_type']); ?></td>
<td class="dataTableContent" nowrap><?php echo '<a href="'.xtc_href_link(FILENAME_PAYPAL,'view=detail&paypal_ipn_id='.$conn_data['paypal_ipn_id']).'&back=paypal&ipn_back='.$_GET['paypal_ipn_id'].'">'.TEXT_PAYPAL_DETAIL.'</a>'; ?></td>
<td class="dataTableContent" nowrap><?php echo $conn_data['mc_gross'].' '.$conn_data['mc_currency']; ?></td>
<td class="dataTableContent" nowrap><?php echo $conn_data['mc_fee'].' '.$conn_data['mc_currency']; ?></td>
<td class="dataTableContent" nowrap><?php echo round($conn_data['mc_gross']-$conn_data['mc_fee'],2).' '.$conn_data['mc_currency']; ?></td>
</tr>
 <?php
		}
		?>

	</table>	
	
      </div>
      
      <?php
} 
}
?>