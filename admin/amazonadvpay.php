<?php
/* --------------------------------------------------------------
   amazonadvpay.php 2015-09-28 gm
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
ob_start();

if(isset($_SESSION['coo_page_token']))
{
	$t_page_token = $_SESSION['coo_page_token']->generate_token();
}
else
{
	$t_page_token = '';
}

$coo_aap = MainFactory::create_object('AmazonAdvancedPayment', array());

if(isset($_REQUEST['check_connect']))
{
	$t_url = $_REQUEST['url'];
	$t_timeout = 5;
	$cc = MainFactory::create_object('ConnectChecker', array());
	try {
		if($_REQUEST['check_connect'] == 1) # check by GET
		{
			$connectinfo = $cc->check_connect($t_url, $t_timeout);
		}
		if($_REQUEST['check_connect'] == 2) # check by POST
		{
			$connectinfo = $cc->check_connect($t_url, $t_timeout, true, 'CONNECTION_TEST');
		}
		echo '<div class="connection_ok">'.$coo_aap->get_text('connection_ok').'</div>';
	}
	catch(Exception $e)
	{
		echo '<div class="connection_error">'.$coo_aap->get_text('connection_error').': '.$e->getMessage().'</div>';
	}

	xtc_db_close();
	exit;
}

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['use_default_orders_status']))
	{
		$coo_aap->useDefaultOrdersStatusConfiguration();
		$_SESSION[$messages_ns][] = $coo_aap->get_text('using_default_orders_status_configuration');
	}
	else if(is_array($_POST['config']))
	{
		$old_credentials = array($coo_aap->seller_id, $coo_aap->aws_access_key, $coo_aap->secret_key);
		$new_credentials = array($_POST['config']['seller_id'], $_POST['config']['aws_access_key'], $_POST['config']['secret_key']);
		if(implode('', $old_credentials) != implode('', $new_credentials))
		{
			$order_reference = $coo_aap->check_credentials($_POST['config']['seller_id'], $_POST['config']['aws_access_key'], $_POST['config']['secret_key']);
			if(isset($order_reference->Error->Code))
			{
				switch((string)$order_reference->Error->Code)
				{
					case 'InvalidOrderReferenceId':
						$_SESSION[$messages_ns][] = $coo_aap->get_text('credentials_changed');
						break;
					case 'InvalidAccessKeyId':
						$_SESSION[$messages_ns][] = $coo_aap->get_text('credentials_invalid_access_key');
						break;
					case 'SignatureDoesNotMatch':
						$_SESSION[$messages_ns][] = $coo_aap->get_text('credentials_invalid_signature');
						break;
					case 'InvalidParameterValue':
						$_SESSION[$messages_ns][] = $coo_aap->get_text('credentials_invalid_merchant_id');
						break;
					default:
						$_SESSION[$messages_ns][] = $coo_aap->get_text('credentials_check_failed');
				}
			}
		}
		foreach($_POST['config'] as $t_key => $t_value)
		{
			$coo_aap->$t_key = $t_value;
		}
		$_SESSION[$messages_ns][] = $coo_aap->get_text('configuration_saved');
	}

	xtc_redirect(PAGE_URL);
}

$t_orders_status = xtc_get_orders_status();

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

$t_ipn_url = HTTPS_CATALOG_SERVER.DIR_WS_CATALOG.'request_port.php?module=AmazonIPN&key='.LogControl::get_secure_token();

?>
<!doctype HTML>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<style>
			p.message {margin: .5ex auto; background: rgb(240, 230, 140); border: 1px solid rgb(255, 0, 0); padding: 1em; }
			dl.adminform {position: relative; overflow: auto; }
			dl.adminform dd, dl.adminform dt {float: left; }
			dl.adminform dt {clear: left; width: 15em; }
			input[type="submit"].btn_wide {width: auto; display: inline-block; margin-top: 2px; }
			dl.adminform input[type="text"].long { width: 99%; }
			dl.adminform select { width: 20em; }
			button#start_connectcheck { display: block; width: 20em; margin: 2em auto; }
			table#connectcheck { background: #EEF2D0; display: none; width: 60%; margin: 2em auto; }
			div.connection_ok, div.connection_error { width: 100%; height: 100%; padding: .3ex; }
			div.connection_ok { background: #80C980; }
			div.connection_error { background: #FF9191; }

			form.bluegray {font-size: 0.9em; }
			form.bluegray fieldset {border: none; padding: 0; margin: 1ex 0 0 0; }
			form.bluegray legend {font-weight: bolder; font-size: 1.4em; background: #585858; color: #FFFFFF; padding: .2ex 0.5%; width: 99%; }
			form.bluegray dl.adminform {margin: 0; }
			form.bluegray dl.adminform dt, form.bluegray dl.adminform dd {line-height: 1.3; padding: 3px 0; margin: 0; }
			form.bluegray dl.adminform dt {width: 20%; float: left; font-weight: bold; padding: 2px;}
			form.bluegray dl.adminform dd {border-bottom: 1px dotted rgb(90, 90, 90); width: 78%; float: none; padding-left: 22%; background-color: #F7F7F7; }
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
					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</td>

				<!-- body_text //-->

				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading">##amazon_advanced_payment_configuration</td>
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

								<button id="start_connectcheck">##check_connection</button>

								<table id="connectcheck">
									<tr class="check">
										<td class="host"><?php echo $coo_aap::EP_OAP_DE_SANDBOX ?></td>
										<td class="result">##checking_connectivity</td>
									</tr>
								</table>

								<form class="bluegray" action="<?php echo PAGE_URL ?>" method="POST">
									<fieldset>
										<legend>##credentials</legend>
										<dl class="adminform">
											<dt><label for="config_seller_id">##seller_id</label></dt>
											<dd><input id="config_seller_id" name="config[seller_id]" type="text" value="<?php echo $coo_aap->seller_id ?>"></dd>
											<dt><label for="config_aws_access_key">##aws_access_key</label></dt>
											<dd><input id="config_aws_access_key" name="config[aws_access_key]" type="text" value="<?php echo $coo_aap->aws_access_key ?>"></dd>
											<dt><label for="config_secret_key">##secret_key</label></dt>
											<dd><input id="config_secret_key" name="config[secret_key]" type="text" class="long" value="<?php echo $coo_aap->secret_key ?>"></dd>
											<dt><label for="config_mode">##mode</label></dt>
											<dd>
												<select name="config[mode]">
													<option value="sandbox" <?php echo $coo_aap->mode == 'sandbox' ? 'selected="selected"' : '' ?>>
														##mode_sandbox
													</option>
													<option value="production" <?php echo $coo_aap->mode == 'production' ? 'selected="selected"' : '' ?>>
														##mode_production
													</option>
												</select>
											</dd>
											<dt><label for="config_location">##location</label></dt>
											<dd>
												<select name="config[location]">
													<option value="de" <?php echo $coo_aap->location == 'de' ? 'selected="selected"' : '' ?>>
														##location_de
													</option>
													<option value="uk" <?php echo $coo_aap->location == 'uk' ? 'selected="selected"' : '' ?>>
														##location_uk
													</option>
													<option value="us" <?php echo $coo_aap->location == 'us' ? 'selected="selected"' : '' ?>>
														##location_us
													</option>
												</select>
											</dd>
										</dl>
									</fieldset>
									<fieldset>
										<legend>##appearance</legend>
										<dl class="adminform">
											<dt><label for="config_button_color">##button_color</label></dt>
											<dd>
												<select name="config[button_color]">
													<option value="orange" <?php echo $coo_aap->button_color == 'orange' ? 'selected="selected"' : '' ?>>
														##button_color_orange
													</option>
													<option value="tan" <?php echo $coo_aap->button_color == 'tan' ? 'selected="selected"' : '' ?>>
														##button_color_tan
													</option>
												</select>
											</dd>
											<dt><label for="config_button_size">##button_size</label></dt>
											<dd>
												<select name="config[button_size]">
													<option value="medium" <?php echo $coo_aap->button_size == 'medium' ? 'selected="selected"' : '' ?>>
														##button_size_medium
													</option>
													<option value="large" <?php echo $coo_aap->button_size == 'large' ? 'selected="selected"' : '' ?>>
														##button_size_large
													</option>
													<option value="x-large" <?php echo $coo_aap->button_size == 'x-large' ? 'selected="selected"' : '' ?>>
														##button_size_x_large
													</option>
												</select>
											</dd>
											<dt>##hidden_button_mode</dt>
											<dd>
												<input id="hidden_button_on" name="config[hidden_button]" type="radio" value="1" <?php echo $coo_aap->hidden_button == true ? 'checked="checked"' : '' ?>>
												<label for="hidden_button_on">##button_hidden</label><br>
												<input id="hidden_button_off" name="config[hidden_button]" type="radio" value="0" <?php echo $coo_aap->hidden_button != true ? 'checked="checked"' : '' ?>>
												<label for="hidden_button_off">##button_visible</label>
											</dd>
										</dl>
									</fieldset>
									<fieldset>
										<legend>##settings</legend>
										<dl class="adminform">
											<dt><label for="auth_mode">##authorization_mode</label></dt>
											<dd>
												<select id="auth_mode" name="config[authorization_mode]">
													<option value="auto-sync" <?php echo $coo_aap->authorization_mode == 'auto-sync' ? 'selected="selected"' : '' ?>>
														##auth_mode_auto_sync
													</option>
													<option value="auto-async" <?php echo $coo_aap->authorization_mode == 'auto-async' ? 'selected="selected"' : '' ?>>
														##auth_mode_auto_async
													</option>
													<option value="manual" <?php echo $coo_aap->authorization_mode == 'manual' ? 'selected="selected"' : '' ?>>
														##auth_mode_manual
													</option>
												</select>
											</dd>
											<dt><label for="capture_mode">##capture_mode</label></dt>
											<dd>
												<select id="capture_mode" name="config[capture_mode]">
													<option value="manual" <?php echo $coo_aap->capture_mode == 'manual' ? 'selected="selected"' : '' ?>>
														##capture_mode_manual
													</option>
													<option value="immediate" <?php echo $coo_aap->capture_mode == 'immediate' ? 'selected="selected"' : '' ?>>
														##capture_mode_immediate
													</option>
												</select>
												<div class="note">##note_immediate_capture</div>
											</dd>
											<dt>##erp_mode</dt>
											<dd>
												<input id="erp_mode_on" name="config[erp_mode]" type="radio" value="1" <?php echo $coo_aap->erp_mode == true ? 'checked="checked"' : '' ?>>
												<label for="erp_mode_on">##erp_mode_on</label><br>
												<input id="erp_mode_off" name="config[erp_mode]" type="radio" value="0" <?php echo $coo_aap->erp_mode != true ? 'checked="checked"' : '' ?>>
												<label for="erp_mode_off">##erp_mode_off</label>
											</dd>
											<dt>##ipn_enabled</dt>
											<dd>
												<input id="ipn_enabled_on" name="config[ipn_enabled]" type="radio" value="1" <?php echo $coo_aap->ipn_enabled == true ? 'checked="checked"' : '' ?>>
												<label for="ipn_enabled_on">##ipn_enabled_on</label><br>
												<input id="ipn_enabled_off" name="config[ipn_enabled]" type="radio" value="0" <?php echo $coo_aap->ipn_enabled != true ? 'checked="checked"' : '' ?>>
												<label for="ipn_enabled_off">##ipn_enabled_off</label><br>
												<br>
												IPN-URL: <input class="long" type="text" readonly="readonly" value="<?php echo $t_ipn_url ?>">
											</dd>
											<dt>##orders_status_auth_open</dt>
											<dd style="min-height: 3em;">
												<select name="config[orders_status_auth_open]">
													<?php foreach($t_orders_status as $t_os): ?>
														<option value="<?php echo $t_os['id'] ?>" <?php echo $coo_aap->orders_status_auth_open == $t_os['id'] ? 'selected="selected"' : '' ?>>
															<?php echo $t_os['text'] ?>
														</option>
													<?php endforeach ?>
												</select>
												<span class="orders_status_default_name">
													##orders_status_default: ##orders_status_name_payment_authorized
												</span>
											</dd>
											<dt>##orders_status_auth_declined</dt>
											<dd style="min-height: 3em;">
												<select name="config[orders_status_auth_declined]">
													<?php foreach($t_orders_status as $t_os): ?>
														<option value="<?php echo $t_os['id'] ?>" <?php echo $coo_aap->orders_status_auth_declined == $t_os['id'] ? 'selected="selected"' : '' ?>>
															<?php echo $t_os['text'] ?>
														</option>
													<?php endforeach ?>
												</select>
												<span class="orders_status_default_name">
													##orders_status_default: ##orders_status_name_authorization_declined
												</span>
											</dd>
											<dt>##orders_status_auth_declined_hard</dt>
											<dd style="min-height: 3em;">
												<select name="config[orders_status_auth_declined_hard]">
													<?php foreach($t_orders_status as $t_os): ?>
														<option value="<?php echo $t_os['id'] ?>" <?php echo $coo_aap->orders_status_auth_declined_hard == $t_os['id'] ? 'selected="selected"' : '' ?>>
															<?php echo $t_os['text'] ?>
														</option>
													<?php endforeach ?>
												</select>
												<span class="orders_status_default_name">
													##orders_status_default: ##orders_status_name_authorization_declined_hard
												</span>
											</dd>
											<dt>##orders_status_captured</dt>
											<dd style="min-height: 3em;">
												<select name="config[orders_status_captured]">
													<?php foreach($t_orders_status as $t_os): ?>
														<option value="<?php echo $t_os['id'] ?>" <?php echo $coo_aap->orders_status_captured == $t_os['id'] ? 'selected="selected"' : '' ?>>
															<?php echo $t_os['text'] ?>
														</option>
													<?php endforeach ?>
												</select>
												<span class="orders_status_default_name">
													##orders_status_default: ##orders_status_name_payment_captured
												</span>
											</dd>
											<dt>##orders_status_capture_failed</dt>
											<dd style="min-height: 3em;">
												<select name="config[orders_status_capture_failed]">
													<?php foreach($t_orders_status as $t_os): ?>
														<option value="<?php echo $t_os['id'] ?>" <?php echo $coo_aap->orders_status_capture_failed == $t_os['id'] ? 'selected="selected"' : '' ?>>
															<?php echo $t_os['text'] ?>
														</option>
													<?php endforeach ?>
												</select>
												<span class="orders_status_default_name">
													##orders_status_default: ##orders_status_name_capture_failed
												</span>
											</dd>
											<dt>
												##use_default_orders_status
											</dt>
											<dd>
												<input class="button btn_wide" type="submit" value="##use_default_orders_status_btn" name="use_default_orders_status">
												##use_default_orders_status_note
											</dd>
										</dl>
									</fieldset>
									<br>
									<input class="button btn_wide" type="submit" value="##config_save">
								</form>
							</td>
						</tr>
					</table>
				</td>

				<!-- body_text_eof //-->

			</tr>
		</table>
		<!-- body_eof //-->

		<script>
		$(function() {
			$('#start_connectcheck').on('click', function() {
				$(this).hide();
				$('#connectcheck').show();
				$('#connectcheck tr.check').each(function() {
					var url = $('td.host', this).text();
					var post_url = $('td.post_host', this).text();
					if(url) {
						$('td.result', this).load('<?php echo PAGE_URL ?>?check_connect=1&url='+url);
					}
					if(post_url) {
						$('td.result', this).load('<?php echo PAGE_URL ?>?check_connect=2&url='+url);
					}
				});
			});
		});
		</script>

		<!-- footer //-->
		<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php
echo $coo_aap->replaceLanguagePlaceholders(ob_get_clean());
require DIR_WS_INCLUDES . 'application_bottom.php';
