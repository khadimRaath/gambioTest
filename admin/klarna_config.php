<?php
/* --------------------------------------------------------------
   klarna_config.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

require_once 'includes/application_top.php';
define('PAGE_URL', HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

$coo_text_mgr = new LanguageTextManager('klarna', $_SESSION['languages_id']);
$klarna = new GMKlarna();
$config = $klarna->getConfig();

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	// do something
	if(isset($_POST['config_save'])) {
		$checkbox_names = array('activate_country_AT', 'activate_country_DK', 'activate_country_FI',
			'activate_country_DE', 'activate_country_NL', 'activate_country_NO', 'activate_country_SE');
		foreach($checkbox_names as $cb_name) {
			$_POST[$cb_name] = isset($_POST[$cb_name]) ? $_POST[$cb_name] : '0';
		}
		$klarna->saveConfig($_POST);
		$_SESSION[$messages_ns][] = $coo_text_mgr->get_text('configuration_saved');
	}
	if(isset($_POST['clear_pclasses'])) {
		$result = $klarna->clearPClasses();
		if($result === false) {
			$_SESSION[$messages_ns][] = $coo_text_mgr->get_text('error_clearing_pclasses');
		}
		else {
			$_SESSION[$messages_ns][] = $coo_text_mgr->get_text('pclasses_cleared');
		}
	}

	xtc_redirect(PAGE_URL);
}

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

ob_start();
?>
<!doctype HTML>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title>##CONFIG_TITLE - <?php echo TITLE ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<style>
			p.message {	margin: .5ex auto; background: rgb(240, 230, 140); border: 1px solid rgb(255, 0, 0); padding: 1em; }
			dl.adminform { position: relative; overflow: auto; }
			dl.adminform dd, dl.adminform dt { float: left; margin: 1px 0; }
			dl.adminform dt { clear: left; width: 15em; }
			dl.adminform dt label:after { content: ':';}
			input[type="submit"].btn_wide { width: auto; }
			dl.adminform select { width: 12em; }
			dl.adminform input[type="checkbox"] { vertical-align: middle; }
			dl.countrydata { display: none; }

			form.bluegray {font-size: 0.9em; }
			form.bluegray fieldset {border: none; padding: 0; margin: 1ex 0 0 0; }
			form.bluegray legend {font-weight: bolder; font-size: 1.4em; background: #585858; color: #FFFFFF; padding: .2ex 0.5%; width: 99%; }
			form.bluegray dl.adminform {margin: 0; }
			form.bluegray dl.adminform dt, form.bluegray dl.adminform dd {line-height: 1.3; padding: 3px 0; margin: 0; }
			form.bluegray dl.adminform dt {width: 30%; float: left; font-weight: bold; padding: 2px;}
			form.bluegray dl.adminform dd {border-bottom: 1px dotted rgb(90, 90, 90); width: 68%; float: none; padding-left: 32%; background-color: #F7F7F7; min-height: 2.5em; }
			form.bluegray dl.adminform dd:nth-child(4n) {background: #D6E6F3; }
		</style>
	</head>
	<body>
		<!-- header //-->
		<?php require DIR_WS_INCLUDES . 'header.php'; ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">

						<!-- left_navigation //-->
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						<!-- left_navigation_eof //-->

					</table>
				</td>

				<!-- body_text //-->

				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading" style="padding-left: 0px">##KLARNA_CONFIGURATION</td>
										<td width="80" rowspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td class="main" valign="top">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="main">
								<?php foreach($messages as $msg): ?>
								<p class="message"><?php echo $msg ?></p>
								<?php endforeach; ?>

								<form class="bluegray" action="<?php echo PAGE_URL ?>" method="POST">
									<dl class="adminform">
										<dt><label for="server">##SERVER_MODE</label></dt>
										<dd>
											<select name="server" id="server">
												<option value="<?php echo Klarna::BETA ?>" <?php echo $config['server'] == Klarna::BETA ? 'selected="selected"' : '' ?>>BETA</option>
												<option value="<?php echo Klarna::LIVE ?>" <?php echo $config['server'] == Klarna::LIVE ? 'selected="selected"' : '' ?>>LIVE</opfion>
											</select>
										</dd>
										<?php foreach($klarna->getCountries() as $country => $klarna_country_id): ?>
										<dt>##ACTIVATE_IN ##COUNTRY_<?php echo $country ?></dt>
										<dd>
											<input id="activate_country_<?php echo $country ?>" name="activate_country_<?php echo $country ?>" type="checkbox" value="1" class="countrycb" <?php echo $config['activate_country_'.$country] ? 'checked="checked"' : ''?>>
											<label for="activate_country_<?php echo $country ?>">##USE_FOR_COUNTRY&nbsp;##COUNTRY_<?php echo $country ?></label>
											<dl class="countrydata">
												<dt><label for="merchant_id_<?php echo $country ?>">##MERCHANT_ID ##COUNTRY_<?php echo $country ?></label></dt>
												<dd>
													<input id="merchant_id_<?php echo $country ?>" name="merchant_id_<?php echo $country ?>" type="text" value="<?php echo $config['merchant_id_'.$country] ?>">
												</dd>
												<dt><label for="shared_secret_<?php echo $country ?>">##SHARED_SECRET ##COUNTRY_<?php echo $country ?></label></dt>
												<dd>
													<input id="shared_secret_<?php echo $country ?>" name="shared_secret_<?php echo $country ?>" type="text" value="<?php echo $config['shared_secret_'.$country] ?>">
												</dd>
												<dt><label for="invoice_fee_<?php echo $country ?>">##INVOICE_FEE</label></dt>
												<dd>
													<input type="text" name="invoice_fee_<?php echo $country ?>" value="<?php echo $config['invoice_fee_'.$country] ?>" placeholder="1.95">
												</dd>
											</dl>
										</dd>
										<?php endforeach ?>
										<dt><label for="show_checkout_partpay">##SHOW_CHECKOUT_PARTPAY</label></dt>
										<dd>
											<select id="show_checkout_partpay" name="show_checkout_partpay">
												<option value="1" <?php echo $config['show_checkout_partpay'] == true ? 'selected="selected"' : ''?>>##YES</option>
												<option value="0" <?php echo $config['show_checkout_partpay'] == false ? 'selected="selected"' : ''?>>##NO</option>
											</select>
										</dd>
										<dt><label for="show_product_partpay">##SHOW_PRODUCT_PARTPAY</label></dt>
										<dd>
											<select id="show_product_partpay" name="show_product_partpay">
												<option value="1" <?php echo $config['show_product_partpay'] == true ? 'selected="selected"' : ''?>>##YES</option>
												<option value="0" <?php echo $config['show_product_partpay'] == false ? 'selected="selected"' : ''?>>##NO</option>
											</select>
										</dd>
									</dl>
									<input class="button btn_wide" type="submit" value="##CONFIG_SAVE" name="config_save">
									<input class="button btn_wide" type="submit" value="##CLEAR_PCLASSES" name="clear_pclasses">
								</form>

								<div style="font-size: 0.8em; float: right; color: #888;"><?php echo $klarna->module_version ?></div>
							</td>
						</tr>
					</table>
				</td>

				<script>
				$(function() {
					$('input.countrycb').click(function() {
						$('dl.countrydata', $(this).parent()).slideToggle($(this).prop('checked'));
					});
					$('dl.countrydata', $('input.countrycb:checked').parent()).show();
				});
				</script>

				<!-- body_text_eof //-->

			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
		<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php
$content = ob_get_clean();

while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
	$replacement = $coo_text_mgr->get_text($matches[1]);
	if(empty($replacement)) {
		$replacement = $matches[1];
	}
	$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
}
echo $content;
require DIR_WS_INCLUDES . 'application_bottom.php';

