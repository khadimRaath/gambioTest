<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/
	require_once 'includes/application_top.php';

	$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
	$languageTextManager->init_from_lang_file('hgwConf', $_SESSION['languages_id']);

	/* include class */
	if(file_exists(DIR_WS_CLASSES.'class.heidelpaygw.php')){
		include_once(DIR_WS_CLASSES.'class.heidelpaygw.php');
	}else{
		require_once(DIR_FS_CATALOG.DIR_WS_CLASSES.'class.heidelpaygw.php');
	}

	define('PAGE_URL', HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));
	$hgw = new heidelpayGW();
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		if(isset($_POST['reset'])){
			$setConf = $hgw->setConf($hgw->defConf);
		}elseif(isset($_POST['hgw'])){
			$setConf = $hgw->setConf($_POST['hgw']);
		}
		if($setConf){ $_SESSION[$messages_ns][] = HGW_CONFIG_SAVED; }
		xtc_redirect(PAGE_URL);
	}
	
	$getConf = $hgw->getConf;
	$messages = $_SESSION[$messages_ns];
	$_SESSION[$messages_ns] = array();
?>

<!doctype HTML>
<html <?php print HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php print $_SESSION['language_charset']; ?>">
		<title><?php print TITLE.' | '.HGW_CONFIG_TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css" />
		<style>
			.small{ font-size: 10px; color: #444; vertical-align: middle; }
			.right{ text-align: right; }
			form table { width: 900px; }
			form table tr td{ vertical-align: top; }
			form table tr td input,
			form table tr td select{ width: 305px; }
			form table tr td h2{ background-color: #2196F3; color: #fff; padding: 3px 0 3px 10px; margin: 10px 0; text-transform: uppercase; }
			form table tr td.close{ background-color: #2196F3; color: #fff; padding: 1px 5px;}
			p.message { margin: .5ex 0; background: #F0E68C; border: 1px solid #FF0000; padding: 10px; width: 875px; }
			input.button{ display: inherit; }
			input.button.reset{ background-color: gray; margin: 0 0 0 20px; width: 235px; }			
			.main .config a{ font-size: 12px; }
			.main .config table.support{ margin: 0; padding: 0; width: 300px; }
			.main .config table.support tr td{ margin: 0; padding: 0; }
		</style>
	</head>
	<body>
		<?php require DIR_WS_INCLUDES . 'header.php'; ?>

		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php print BOX_WIDTH; ?>" valign="top">
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
				</td>
				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr><td class="pageHeading" colspan="2"><?php print HGW_CONFIG_TITLE; ?></td></tr>
									<tr>
										<td class="main" valign="top">											
											<?php if(!empty($messages)): ?>											
												<?php foreach($messages as $msg): ?>
													<p class="message"><?php echo $msg ?></p>
												<?php endforeach; ?>
											<?php endif; ?>
											
											<form action="<?php print PAGE_URL; ?>" method="POST">
												<table>
													<colgroup>
														<col width="200">
														<col width="325">
														<col width="375">
													</colgroup>
													<tr><td></td><td></td><td class="small"></td></tr>
													<tr><td colspan='3'><h2><?php print HGW_ABOUT_HEAD;?></h2></td></tr>
													<tr><td colspan='3'><?php print HGW_ABOUT;?></td></tr>
													<tr><td colspan='3'><h2><?php print HGW_OPTIONS_HEAD;?></h2></td></tr>
													<tr><td><?php print HGW_SENDER;?></td><td><input type="text" name="hgw[senderId]" value="<?php print $getConf['senderId']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_LOGIN;?></td><td><input type="text" name="hgw[login]" value="<?php print $getConf['login']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_PW;?></td><td><input type="text" name="hgw[pw]" value="<?php print $getConf['pw']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td colspan='3'>&nbsp;</td></tr>
													<tr><td><?php print HGW_TRANSACTION_MODE;?></td>
														<td>
															<select name="hgw[transactionMode]">
																<option value="0" <?php $getConf['transactionMode'] == '0' ? print 'selected="selected"' : '';?>><?php print HGW_NO;?></option>
																<option value="1" <?php $getConf['transactionMode'] == '1' ? print 'selected="selected"' : '';?>><?php print HGW_YES;?></option>
															</select>
														</td>
														<td class="small"><?php print HGW_TRANSACTION_MODE_INFO;?></td>
													</tr>
													<tr><td colspan='3'><h2><?php print HGW_CHANNEL_HEAD;?></h2></td></tr>
													<tr><td><?php print HGW_CC;?></td><td><input type="text" name="hgw[cc_chan]" value="<?php print $getConf['cc_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_DC;?></td><td><input type="text" name="hgw[dc_chan]" value="<?php print $getConf['dc_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_DD;?></td><td><input type="text" name="hgw[dd_chan]" value="<?php print $getConf['dd_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_PP;?></td><td><input type="text" name="hgw[pp_chan]" value="<?php print $getConf['pp_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_IV;?></td><td><input type="text" name="hgw[iv_chan]" value="<?php print $getConf['iv_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_SU;?></td><td><input type="text" name="hgw[su_chan]" value="<?php print $getConf['su_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_PAY;?></td><td><input type="text" name="hgw[pay_chan]" value="<?php print $getConf['pay_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_BS;?></td><td><input type="text" name="hgw[bs_chan]" value="<?php print $getConf['bs_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<!--<tr><td><?php print HGW_EPS;?></td><td><input type="text" name="hgw[eps_chan]" value="<?php print $getConf['eps_chan']; ?>"></td><td class="small">&nbsp;</td></tr>-->
													<tr><td><?php print HGW_IDL;?></td><td><input type="text" name="hgw[idl_chan]" value="<?php print $getConf['idl_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_MK;?></td><td><input type="text" name="hgw[mk_chan]" value="<?php print $getConf['mk_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_PF;?></td><td><input type="text" name="hgw[pf_chan]" value="<?php print $getConf['pf_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<tr><td><?php print HGW_GP;?></td><td><input type="text" name="hgw[gp_chan]" value="<?php print $getConf['gp_chan']; ?>"></td><td class="small">&nbsp;</td></tr>
													<!--<tr><td><?php print HGW_BP;?></td><td><input type="text" name="hgw[bp_chan]" value="<?php print $getConf['bp_chan']; ?>"></td><td class="small">&nbsp;</td></tr>-->	
													
													<tr><td colspan='3'><h2><?php print HGW_BOOKING_MODE_HEAD;?></h2></td></tr>
													<tr><td><?php print HGW_BOOKING_MODE_CC;?></td>
														<td>
															<select name="hgw[cc_bookingMode]">
																<option value="1" <?php $getConf['cc_bookingMode'] == '1' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_DB;?></option>
																<option value="2" <?php $getConf['cc_bookingMode'] == '2' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_RES;?></option>
																<option value="3" <?php $getConf['cc_bookingMode'] == '3' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_REG_DB;?></option>
																<option value="4" <?php $getConf['cc_bookingMode'] == '4' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_REG_RES;?></option>
															</select>
														</td>
														<td class="small" rowspan='3'><?php print HGW_BOOKING_MODE_INFO; ?></td>
													</tr>
													<tr><td><?php print HGW_BOOKING_MODE_DC;?></td>
														<td>
															<select name="hgw[dc_bookingMode]">
																<option value="1" <?php $getConf['dc_bookingMode'] == '1' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_DB;?></option>
																<option value="2" <?php $getConf['dc_bookingMode'] == '2' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_RES;?></option>
																<option value="3" <?php $getConf['dc_bookingMode'] == '3' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_REG_DB;?></option>
																<option value="4" <?php $getConf['dc_bookingMode'] == '4' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_REG_RES;?></option>
															</select>
														</td>
													</tr>
													<tr><td><?php print HGW_BOOKING_MODE_DD;?></td>
														<td>
															<select name="hgw[dd_bookingMode]">
																<option value="1" <?php $getConf['dd_bookingMode'] == '1' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_DB;?></option>
																<option value="2" <?php $getConf['dd_bookingMode'] == '2' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_RES;?></option>
																<option value="3" <?php $getConf['dd_bookingMode'] == '3' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_REG_DB;?></option>
																<option value="4" <?php $getConf['dd_bookingMode'] == '4' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_REG_RES;?></option>
															</select>
														</td>
													</tr>
													<!-- Paypal in Registration-Mode doesn´t work since at least 01.04.2016  -->
													<tr><td><?php print HGW_BOOKING_MODE_PAY;?></td>
 														<td> 
 															<select name="hgw[pay_bookingMode]"> 
																<option value="1" <?php $getConf['pay_bookingMode'] == '1' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_DB;?></option>
  																<option value="2" <?php $getConf['pay_bookingMode'] == '2' ? print 'selected="selected"' : '';?>><?php print HGW_BOOKING_MODE_RES;?></option>		
<!--																<option value="3" <?php// $getConf['pay_bookingMode'] == '3' ? print 'selected="selected"' : '';?>><?php //print HGW_BOOKING_MODE_REG_DB;?></option>	-->
<!--																<option value="4" <?php// $getConf['pay_bookingMode'] == '4' ? print 'selected="selected"' : '';?>><?php // print HGW_BOOKING_MODE_REG_RES;?></option>	-->
 															</select> 
 														</td> 
 													</tr> 
													<tr><td colspan='3'><h2><?php print HGW_MISC_HEAD;?></h2></td></tr>
													<!--
													<tr><td><?php print HGW_DEBUG;?></td>
													<td>
														<select name="hgw[debug]">
															<option value="0" <?php $getConf['debug'] == '0' ? print 'selected="selected"' : '';?>><?php print HGW_NO;?></option>
															<option value="1" <?php $getConf['debug'] == '1' ? print 'selected="selected"' : '';?>><?php print HGW_YES;?></option>
														</select>
													</td>													
													<td class="small">&nbsp;</td></tr>
													-->
													<tr><td><?php print HGW_IBAN;?></td>
													<td>
														<select name="hgw[iban]">
															<option value="0" <?php $getConf['iban'] == '0' ? print 'selected="selected"' : '';?>><?php print HGW_NO;?></option>
															<option value="1" <?php $getConf['iban'] == '1' ? print 'selected="selected"' : '';?>><?php print HGW_YES;?></option>
															<option value="2" <?php $getConf['iban'] == '2' ? print 'selected="selected"' : '';?>><?php print HGW_BOTH;?></option>
														</select>
													</td>
													<td class="small"><?php print HGW_IBAN_INFO; ?></td></tr>													
													<tr><td><?php print HGW_SECRET;?></td><td><input type="text" name="hgw[secret]" value="<?php print $getConf['secret']; ?>"></td><td class="small"><?php print HGW_SECRET_INFO; ?></td></tr>
													<tr><td><?php print HGW_SHIPPINGHASH; ?></td>
													<td>
														<select name="hgw[shippinghash]">
															<option value="0" <?php $getConf['shippinghash'] == '0' ? print 'selected="selected"' : '';?>><?php print HGW_NO;?></option>
															<option value="1" <?php $getConf['shippinghash'] == '1' ? print 'selected="selected"' : '';?>><?php print HGW_YES;?></option>
														</select>
													</td>
													<td class="small"><?php print HGW_SHIPPINGHASH_INFO; ?></td></tr>
													<tr><td colspan="3">&nbsp;</td></tr>
													<tr><td class="close small right" colspan="3">Modul Version: <b><?php print $hgw->version; ?></b></td></tr>
													<tr><td colspan="3">&nbsp;</td></tr>
												</table>
												
												<input class="button btn_wide" type="submit" value="<?php print HGW_BTN_SAVE; ?>">
												<input class="button btn_wide reset" type="submit" value="<?php print HGW_BTN_RESET; ?>" name="reset">
											</form>
										</td>										
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>				
			</tr>
		</table>
		<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>