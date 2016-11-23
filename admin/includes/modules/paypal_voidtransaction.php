<?php
/**
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
	$err = substr($_GET['err'], 6);
	$papayl_error = $paypal->getPayPalErrorDescription($err);
	if(!$papayl_error) {
		$papayl_error = $error;
	}
	?>
	<table style="margin-bottom: 1px;" border="0" width="100%" cellspacing="0" cellpadding="2">
		<tr><td class="dataTableContent" style="font-size: 12px; background-color: darkred; color: white; padding: 3px;"><?php echo $papayl_error; ?></td></tr>
	</table>
	<?php
}

?>
<?php echo xtc_draw_form('void_transaction', FILENAME_PAYPAL, xtc_get_all_get_params(array('action')) . 'action=perform'); 
echo xtc_draw_hidden_field('txn_id', $ipn_data['txn_id']);
echo xtc_draw_hidden_field('amount', $ipn_data['mc_gross']);
echo xtc_draw_hidden_field('ipn_id', (int)$_GET['paypal_ipn_id']);
?>
       
<div class="highlightbox">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo TEXT_PAYPAL_VOID_AUTHORIZATION; ?>
			</td>
		</tr>
	</table>
      
	<table width="100%" border="0" class="gm_border dataTableRow">
		<tr>
			<td class="main" width="15%" nowrap="nowrap"><?php echo TEXT_PAYPAL_TXN_ID; ?></td>
			<td class="main" width="90%"><?php echo $ipn_data['txn_id']; ?></td>
		</tr>
		<tr>
			<td class="main" valign="top"><?php echo TEXT_PAYPAL_ADRESS; ?></td>
			<td class="main"><?php echo $ipn_data['address_name']; ?></td>
		</tr>
		<tr>
			<td class="main" nowrap="nowrap"><?php echo TEXT_PAYPAL_PAYER_EMAIL; ?></td>
			<td class="main" valign="middle"><?php echo $ipn_data['payer_email'];?></td>
		</tr>
		<tr>
			<td class="main" nowrap="nowrap"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_TOTAL; ?></td>
			<td class="main"><?php echo number_format((double)$ipn_data['mc_authorization'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_CAPTURED; ?></td>
			<td class="main"><?php echo number_format((double)$ipn_data['mc_captured'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
		</tr>
		<tr>
			<td class="main"><?php echo TEXT_PAYPAL_TRANSACTION_AUTH_OPEN; ?></td>
			<td class="main"><?php echo number_format((double)$ipn_data['mc_authorization']-$ipn_data['mc_captured'], 2, '.', '').' '.$ipn_data['mc_currency']; ?></td>
		</tr>
		<tr>
			<td class="main" valign="top"><?php echo TEXT_PAYPAL_REFUND_NOTE; ?></td>
			<td class="main"><?php echo xtc_draw_textarea_field('refund_info', '', '50', '5', ''); ?></td>
		</tr>
	</table>

	<input type="submit" class="button float_left" value="<?php echo TEXT_PAYPAL_BUTTON_VOID_AUTHORIZATION; ?>">
	<?php echo '<a class="button float_left" href="'.xtc_href_link(FILENAME_PAYPAL, 'view=detail&paypal_ipn_id='.(int)$_GET['paypal_ipn_id']).'">'.BUTTON_BACK.'</a>'; ?>
</div>
   
 </form>