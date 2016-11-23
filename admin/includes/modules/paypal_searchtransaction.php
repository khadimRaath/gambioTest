<?php
/* --------------------------------------------------------------
   paypal_searchtransaction.php 2008-06-04 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

paypal_transactiondetail.php 04.06.2008 pt@gambio
	Gambio OHG
	http://www.gambio.de
	Copyright (c) 2007 Gambio OHG
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

 echo xtc_draw_form('transaction_search', FILENAME_PAYPAL, xtc_get_all_get_params(array('action')) . 'action=perform');
?>
<div class="highlightbox">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="dataTableHeadingContent" style="border-right: 0px;">
				<?php echo TEXT_PAYPAL_SEARCH_TRANSACTION; ?>												  
			</td>
		</tr>
	</table>
	<table width="100%" border="0" class="gm_border dataTableRow">
		<tr>
			<td class="main" width="120" valign="top"><?php echo TEXT_PAYPAL_SEARCH_FOR; ?></td>
			<td class="main"><input type="text" name="search_type" value=""></td>
		</tr>
		<tr>
			<td class="main" width="120" valign="top"><?php echo TEXT_PAYPAL_SEARCH_IN; ?></td>
			<td class="main">
				<select name="search_first_type"><option value="email_alias"><?php echo TEXT_PAYPAL_SEARCH_SELECT_MAIL; ?></option>
					<option value="trans_id"><?php echo TEXT_PAYPAL_SEARCH_SELECT_ID; ?></option>
					<option value="last_name_only"><?php echo TEXT_PAYPAL_SEARCH_SELECT_NAME; ?></option>
					<option value="last_name"><?php echo TEXT_PAYPAL_SEARCH_SELECT_FULLNAME; ?></option>
					<option value="invoice_id"><?php echo TEXT_PAYPAL_SEARCH_SELECT_INVOICE_NO; ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="main" width="120" valign="top"><input type="radio" name="span" value="broad"><?php echo TEXT_PAYPAL_SEARCH_TIME; ?></td>
			<td class="main">		
				<select name="for" onChange="javascript:CheckMe('0',this.form);"><option value="1"><?php echo TEXT_PAYPAL_SEARCH_SELECT_LASTDAY; ?></option>
					<option value="2"><?php echo TEXT_PAYPAL_SEARCH_SELECT_LASTWEEK; ?></option>
					<option value="3"><?php echo TEXT_PAYPAL_SEARCH_SELECT_LASTMONTH; ?></option>
					<option value="4"><?php echo TEXT_PAYPAL_SEARCH_SELECT_LASTYEAR; ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="main" width="120" valign="top"><input type="radio" checked name="span" value="narrow"><?php echo TEXT_PAYPAL_SEARCH_TIME_FROM; ?></td>
			<td class="main">
				<table align="left" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><input type="text" size="2" maxlength="2" onFocus="javascript:CheckMe('1',this.form);" name="from_t" value="<?php echo $date['last_month']['tt']; ?>"></td>
						<td class="main"> / </td>
						<td><input type="text" size="2" maxlength="2" onFocus="javascript:CheckMe('1',this.form);" name="from_m" value="<?php echo $date['last_month']['mm']; ?>"></td>
						<td class="main"> / </td>
						<td><input type="text" size="4" maxlength="4" onFocus="javascript:CheckMe('1',this.form);" name="from_y" value="<?php echo $date['last_month']['yyyy']; ?>"></td>
					</tr>
					<tr>
						<td class="smallText" colspan="2"><?php echo TEXT_PAYPAL_SEARCH_FORMAT_DAY; ?></td>
						<td class="smallText" colspan="2"><?php echo TEXT_PAYPAL_SEARCH_FORMAT_MONTH; ?></td>
						<td class="smallText" colspan="2"><?php echo TEXT_PAYPAL_SEARCH_FORMAT_YEAR; ?></td>
					</tr>
				</table>		
			</td>
		</tr>
		<tr>
			<td class="main" width="120" valign="top"><?php echo TEXT_PAYPAL_SEARCH_TIME_TO; ?></td>
			<td class="main">		
				<table align="left" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><input type="text" size="2" maxlength="2" onFocus="javascript:CheckMe('1',this.form);" name="to_t" value="<?php echo $date['actual']['tt']; ?>"></td>
						<td class="main"> / </td>
						<td><input type="text" size="2" maxlength="2" onFocus="javascript:CheckMe('1',this.form);" name="to_m" value="<?php echo $date['actual']['mm']; ?>"></td>
						<td class="main"> / </td>
						<td><input type="text" size="4" maxlength="4" onFocus="javascript:CheckMe('1',this.form);" name="to_y" value="<?php echo $date['actual']['yyyy']; ?>"></td>
					</tr>
					<tr>
						<td class="smallText" colspan="2"><?php echo TEXT_PAYPAL_SEARCH_FORMAT_DAY; ?></td>
						<td class="smallText" colspan="2"><?php echo TEXT_PAYPAL_SEARCH_FORMAT_MONTH; ?></td>
						<td class="smallText" colspan="2"><?php echo TEXT_PAYPAL_SEARCH_FORMAT_YEAR; ?></td>
					</tr>
				</table>		
			</td>
		</tr>
		<tr>
			<td colspan="2" class="main" width="120" valign="top">
				&nbsp;
			</td>
		</tr>			 
		<tr>
			<td colspan="2" class="main" valign="top">
				<input type="submit" class="button float_left" value="<?php echo BUTTON_SEARCH; ?>">          
				<?php echo '<a class="button float_left" href="'.xtc_href_link(FILENAME_PAYPAL).'">Zur&uuml;ck</a>'; ?>
			</td>
		</tr>			 
	</table>	
	</form>
</div>

<br />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            
<div class="main">
      <strong><?php echo TEXT_PAYPAL_FOUND_TRANSACTION; ?></strong><br />
       <?php 
       
      
     	if (isset($paypal->SearchError['code'])) {     	
       	$messageStack->add($paypal->SearchError['longmessage'],'warning');
    	echo $messageStack->output();
       	
       } ?>
            
            
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent" width="10">&nbsp;</td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_PAYMENT_DATE; ?></td>
                <td class="dataTableHeadingContent""><?php echo TABLE_HEADING_NAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYPAL_ID; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TXN_TYPE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENT_STATUS; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PAYMENT_AMOUNT; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_FEE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TEXT_PAYPAL_NETTO; ?></td>
                <td class="dataTableHeadingContent" align="right">&nbsp;</td>
              </tr>
              
<?php 
if (!is_array($response)) { 
	echo '<tr><td class="main" colspan="9">' . TEXT_PAYPAL_SEARCH_EMPTY_RESULT . '</td></tr>';
} else {

foreach ($response as $arr) { ?>

<tr>
<td class="dataTableContent" nowrap><?php echo $paypal->getStatusSymbol($arr['TYPE'],$arr['STATUS']); ?>&nbsp;</td>
<td class="dataTableContent" nowrap><?php echo xtc_date_short($arr['TIMESTAMP']); ?></td>
<td class="dataTableContent"><?php echo $arr['NAME']; ?></td>
<td class="dataTableContent" nowrap><?php echo $arr['TXNID']; ?></td>
<td class="dataTableContent" nowrap><?php echo $arr['TYPE']; ?></td>
<td class="dataTableContent" nowrap><?php echo $arr['STATUS']; ?></td>
<td class="dataTableContent" nowrap><?php echo $arr['AMT']; ?></td>
<td class="dataTableContent" nowrap><?php echo $arr['FEEAMT']; ?></td>
<td class="dataTableContent" nowrap><?php echo $arr['NETAMT']; ?></td>
<td class="dataTableContent" nowrap><?php echo '<a href="' . xtc_href_link(FILENAME_PAYPAL, 'view=detail&txn_id='.$arr['TXNID']) . '">' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/page_find.gif', IMAGE_ICON_INFO) . '</a>'; ?></td>
</tr>

<?php } }?>
</table>
</div>
</td></tr></table>            