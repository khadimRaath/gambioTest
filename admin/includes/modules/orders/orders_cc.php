<?php
/* --------------------------------------------------------------
   orders_cc.php 2016-01-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?>
<!-- ORDERS - CC PAYMENT -->
<?php
if((($order->info['cc_type']) || ($order->info['cc_owner']) || ($order->info['cc_number'])))
{
	?>

	<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
		<tr>
			<td class="" style="border-right: 0px;">
				<?php echo TITLE_CC_INFO; ?>
			</td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="0" class="cc">

		<?php
		// BMC CC Mod Start
		if($order->info['cc_number'] != '0000000000000000')
		{
			if(strtolower(CC_ENC) == 'true')
			{
				$cipher_data = $order->info['cc_number'];
				$order->info['cc_number'] = changedataout($cipher_data, CC_KEYCHAIN);
			}
		}
		// BMC CC Mod End
		?>
		<tr>
			<td width="80" class="main gm_strong" valign="top">
				<?php echo ENTRY_CREDIT_CARD_NUMBER; ?>
			</td>
			<td colspan="5" class="main" valign="top">
				<?php echo $order->info['cc_number']; ?>
			</td>
		</tr>
		<tr>
			<td width="80" class="main gm_strong" valign="top">
				<?php echo ENTRY_CREDIT_CARD_CVV; ?>
			</td>
			<td colspan="5" class="main" valign="top">
				<?php echo $order->info['cc_cvv']; ?>
			</td>
		</tr>
		<tr>
			<td width="80" class="main gm_strong" valign="top">
				<?php echo ENTRY_CREDIT_CARD_EXPIRES; ?>
			</td>
			<td colspan="5" class="main" valign="top">
				<?php echo $order->info['cc_expires']; ?>
			</td>
		</tr>
	</table>
	<?php
}

// begin modification for banktransfer
$banktransfer_query = xtc_db_query("select banktransfer_prz, banktransfer_status, banktransfer_owner, banktransfer_number, banktransfer_bankname, banktransfer_blz, banktransfer_fax from banktransfer where orders_id = '"
                                   . xtc_db_input($_GET['oID']) . "'");
$banktransfer       = xtc_db_fetch_array($banktransfer_query);
if(($banktransfer['banktransfer_bankname']) || ($banktransfer['banktransfer_blz'])
   || ($banktransfer['banktransfer_number']))
{

?>

<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
	<tr>
		<td class="" style="border-right: 0px;">
			<?php echo TITLE_BANK_INFO; ?>
		</td>
	</tr>
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
	<tr>
		<td width="80" class="main gm_strong" valign="top">
			<?php echo TEXT_BANK_NAME; ?>
		</td>
		<td colspan="5" class="main" valign="top">
			<?php echo $banktransfer['banktransfer_bankname']; ?>
		</td>
	</tr>
	<tr>
		<td width="80" class="main gm_strong" valign="top">
			<?php echo TEXT_BANK_BLZ; ?>
		</td>
		<td colspan="5" class="main" valign="top">
			<?php echo $banktransfer['banktransfer_blz']; ?>
		</td>
	</tr>
	<tr>
		<td width="80" class="main gm_strong" valign="top">
			<?php echo TEXT_BANK_NUMBER; ?>
		</td>
		<td colspan="5" class="main" valign="top">
			<?php echo $banktransfer['banktransfer_number']; ?>
		</td>
	</tr>
	<tr>
		<td width="80" class="main gm_strong" valign="top">
			<?php echo TEXT_BANK_OWNER; ?>
		</td>
		<td colspan="5" class="main" valign="top">
			<?php echo $banktransfer['banktransfer_owner']; ?>
		</td>
	</tr>

	<?php
	if($banktransfer['banktransfer_status'] == 0)
	{
		?>
		<tr>
			<td width="80" class="main gm_strong" valign="top">
				<?php echo TEXT_BANK_STATUS; ?>
			</td>
			<td colspan="5" class="main" valign="top">
				<?php echo "OK"; ?>
			</td>
		</tr>
		<?php
	}
	else
	{
		?>
		<tr>
			<td width="80" class="main gm_strong" valign="top">
				<?php echo TEXT_BANK_STATUS; ?>
			</td>
			<td colspan="5" class="main" valign="top">
				<?php echo $banktransfer['banktransfer_status']; ?>
			</td>
		</tr>
		<?php
		switch($banktransfer['banktransfer_status'])
		{
			case 1 :
				$error_val = TEXT_BANK_ERROR_1;
				break;
			case 2 :
				$error_val = TEXT_BANK_ERROR_2;
				break;
			case 3 :
				$error_val = TEXT_BANK_ERROR_3;
				break;
			case 4 :
				$error_val = TEXT_BANK_ERROR_4;
				break;
			case 5 :
				$error_val = TEXT_BANK_ERROR_5;
				break;
			case 8 :
				$error_val = TEXT_BANK_ERROR_8;
				break;
			case 9 :
				$error_val = TEXT_BANK_ERROR_9;
				break;
		}
		?>
		<tr>
			<td width="80" class="main gm_strong" valign="top">
				<?php echo TEXT_BANK_ERRORCODE; ?>
			</td>
			<td colspan="5" class="main" valign="top">
				<?php echo $error_val; ?>
			</td>
		</tr>
		<tr>
			<td width="80" class="main gm_strong" valign="top">
				<?php echo TEXT_BANK_PRZ; ?>
			</td>
			<td colspan="5" class="main" valign="top">
				<?php echo $banktransfer['banktransfer_prz']; ?>
			</td>
		</tr>
		<?php
	}
	}
	elseif($banktransfer['banktransfer_fax'])
	{
	?>
	<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
		<?php
		}
		if($banktransfer['banktransfer_fax'])
		{
			?>
			<tr>
				<td width="80" class="main gm_strong" valign="top">
					<?php echo TEXT_BANK_FAX; ?>
				</td>
				<td colspan="5" class="main" valign="top">
					<?php echo $banktransfer['banktransfer_fax']; ?>
				</td>
			</tr>
			<?php
			echo "</table>";
		}
		// end modification for banktransfer
		?>
