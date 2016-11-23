<?php
/* --------------------------------------------------------------
   intraship.php 2016-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
   (C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once 'includes/application_top.php';

AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');

define('PAGE_URL', HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

$intraship = new GMIntraship();

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	foreach($_POST['intraship'] as $cfg_key => $cfg_value) {
		$intraship->$cfg_key = $cfg_value;
	}
	$intraship->saveConfig();
	$_SESSION[$messages_ns][] = $intraship->get_text('configuration_saved');
	xtc_redirect(PAGE_URL);
}

$parcelServiceReader = MainFactory::create('ParcelServiceReader');
$parcelServices = $parcelServiceReader->getAllParcelServices();

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

ob_start();
?>
<!doctype HTML>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<style>
			p.message {margin: .5ex auto; background: rgb(240, 230, 140); border: 1px solid rgb(255, 0, 0); padding: 1em; }
			dl.adminform {margin: 1em; position: relative; overflow: auto; }
			dl.adminform dd, dl.adminform dt {float: left; margin: 1px 0; }
			dl.adminform dt {clear: left; width: 30%; }
			dl.adminform dd {width: 65%; margin-left: 2%; }
			input[type="submit"].btn_wide {width: auto; }
			dl.adminform input[type="text"] {width: 95%; }
			dl.adminform input[type="radio"] {vertical-align: bottom; }
			form.bluegray {font-size: 0.9em; }
			form.bluegray fieldset {border: none; padding: 0; margin: 1ex 0 0 0; }
			form.bluegray legend {font-weight: bolder; font-size: 1.4em; background: #585858; color: #FFFFFF; padding: .2ex 0.5%; width: 99%; }
			form.bluegray dl.adminform {margin: 0; }
			form.bluegray dl.adminform dt, form.bluegray dl.adminform dd {line-height: 20px; padding: 3px 0; margin: 0; }
			form.bluegray dl.adminform dt {width: 25%; float: left; font-weight: bold; padding: 2px;}
			form.bluegray dl.adminform dd {border-bottom: 1px dotted rgb(90, 90, 90); width: 70%; float: none; padding-left: 29%; background-color: #F7F7F7; }
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
										<td class="pageHeading" style="padding-left: 0px">##TITLE_CONFIG</td>
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

								<form action="<?php echo PAGE_URL ?>" method="POST" class="bluegray">
									<fieldset class="left">
										<legend>##basic_data</legend>
										<dl class="adminform">
											<dt>##intraship_active</dt>
											<dd>
												<input type="radio" name="intraship[active]" value="1" id="intraship_active_yes" <?php echo $intraship->active == '1' ? 'checked="checked"' : '' ?>>
												<label for="intraship_active_yes">##yes</label><br>
												<input type="radio" name="intraship[active]" value="0" id="intraship_active_no" <?php echo $intraship->active == '0' ? 'checked="checked"' : '' ?>>
												<label for="intraship_active_no">##no</label><br>
											</dd>
											<?php if($intraship->debug == true || $_GET['debug'] == true): ?>
												<dt>##debug_mode</dt>
												<dd>
													<input type="radio" name="intraship[debug]" value="1" id="intraship_debug_yes" <?php echo $intraship->debug == '1' ? 'checked="checked"' : '' ?>>
													<label for="intraship_debug_yes">##yes</label><br>
													<input type="radio" name="intraship[debug]" value="0" id="intraship_debug_no" <?php echo $intraship->debug == '0' ? 'checked="checked"' : '' ?>>
													<label for="intraship_debug_no">##no</label><br>
												</dd>
											<?php endif ?>
											<dt>##send_email</dt>
											<dd>
												<input type="radio" name="intraship[send_email]" value="1" id="intraship_send_email_yes" <?php echo $intraship->send_email == '1' ? 'checked="checked"' : '' ?>>
												<label for="intraship_send_email_yes">##yes</label><br>
												<input type="radio" name="intraship[send_email]" value="0" id="intraship_send_email_no" <?php echo $intraship->send_email == '0' ? 'checked="checked"' : '' ?>>
												<label for="intraship_send_email_no">##no</label><br>
											</dd>
											<dt>##send_announcement</dt>
											<dd>
												<input type="radio" name="intraship[send_announcement]" value="1" id="intraship_send_announcement_yes" <?php echo $intraship->send_announcement == '1' ? 'checked="checked"' : '' ?>>
												<label for="intraship_send_announcement_yes">##yes</label><br>
												<input type="radio" name="intraship[send_announcement]" value="0" id="intraship_send_announcement_no" <?php echo $intraship->send_announcement == '0' ? 'checked="checked"' : '' ?>>
												<label for="intraship_send_announcement_no">##no</label><br>
											</dd>
											<dt><label for="intraship_ekp">##ekp</label></dt>
											<dd>
												<input type="text" name="intraship[ekp]" value="<?php echo $intraship->ekp ?>" id="intraship_ekp">
											</dd>
											<dt><label for="intraship_user">##user</label></dt>
											<dd>
												<input type="text" name="intraship[user]" value="<?php echo $intraship->user ?>" id="intraship_user">
											</dd>
											<dt><label for="intraship_password">##password</label></dt>
											<dd>
												<input type="text" name="intraship[password]" value="<?php echo $intraship->password ?>" id="intraship_password">
											</dd>
											<!-- ORDER STATUS -->
											<dt><label for="intraship_status_id_sent">##status_id_sent</label></dt>
											<dd>
												<select id="intraship_status_id_sent" name="intraship[status_id_sent]">
													<?php foreach(xtc_get_orders_status() as $os): ?>
														<option value="<?php echo $os['id'] ?>" <?php echo $os['id'] == $intraship->status_id_sent ? 'selected="selected"' : ''?>>
															<?php echo $os['text'] ?>
														</option>
													<?php endforeach ?>
												</select>
											</dd>
											<dt><label for="intraship_status_id_storno">##status_id_storno</label></dt>
											<dd>
												<select id="intraship_status_id_storno" name="intraship[status_id_storno]">
													<?php foreach(xtc_get_orders_status() as $os): ?>
														<option value="<?php echo $os['id'] ?>" <?php echo $os['id'] == $intraship->status_id_storno ? 'selected="selected"' : ''?>>
															<?php echo $os['text'] ?>
														</option>
													<?php endforeach ?>
												</select>
											</dd>
											<dt><label for="intraship_parcelservice_id">##parcelservice_id</label></dt>
											<dd>
												<select id="intraship_parcelservice_id" name="intraship[parcelservice_id]">
													<option value="0" <?php if($intraship->parcelservice_id == 0) echo 'selected="selected"' ?>>##parcelservice_none</option>
													<?php foreach($parcelServices as $parcelService): ?>
														<option value="<?php echo $parcelService->GetId() ?>" <?php if($intraship->parcelservice_id == $parcelService->getId()) echo 'selected="selected"' ?>>
															<?php echo $parcelService->getName() ?>
														</option>
													<?php endforeach ?>
												</select>
											</dd>
											<dt>##bpi_service</dt>
											<dd>
												<input type="radio" name="intraship[bpi_use_premium]" value="1" id="intraship_bpi_use_premium_yes" <?php echo $intraship->bpi_use_premium == '1' ? 'checked="checked"' : '' ?>>
												<label for="intraship_bpi_use_premium_yes">##premium</label><br>
												<input type="radio" name="intraship[bpi_use_premium]" value="0" id="intraship_bpi_use_premium_no" <?php echo $intraship->bpi_use_premium == '0' ? 'checked="checked"' : '' ?>>
												<label for="intraship_bpi_use_premium_no">##economy</label><br>
											</dd>
										</dl>
									</fieldset>

									<!-- ZONES -->
									<fieldset class="left">
										<legend>##zones_data</legend>
										<dl class="adminform">
											<?php for($zone = 1; $zone <= GMIntraship::NUM_ZONES; $zone++): ?>
												<dt><label for="intraship_zone_<?php echo $zone ?>_countries">##zone <?php echo $zone ?> ##zone_countries</label></dt>
												<dd>
													<input type="text" name="intraship[zone_<?php echo $zone ?>_countries]"
														value="<?php $v = 'zone_'.$zone.'_countries'; echo $intraship->$v; ?>" id="intraship_zone_<?php echo $zone ?>_countries">
												</dd>
												<dt><label for="intraship_zone_<?php echo $zone ?>_product">##zone <?php echo $zone ?> ##zone_product</label></dt>
												<dd>
													<input type="text" name="intraship[zone_<?php echo $zone ?>_product]"
														value="<?php $v = 'zone_'.$zone.'_product'; echo $intraship->$v; ?>" id="intraship_zone_<?php echo $zone ?>_product">
												</dd>
												<dt><label for="intraship_zone_<?php echo $zone ?>_partner_id">##zone <?php echo $zone ?> ##zone_partner_id</label></dt>
												<dd>
													<input type="text" name="intraship[zone_<?php echo $zone ?>_partner_id]"
														value="<?php $v = 'zone_'.$zone.'_partner_id'; echo $intraship->$v; ?>" id="intraship_zone_<?php echo $zone ?>_partner_id">
												</dd>
											<?php endfor ?>
										</dl>
									</fieldset>

									<!-- SHIPPER -->
									<fieldset class="left" style="clear: left;">
										<legend>##shipper_data</legend>
										<dl class="adminform">
											<dt><label for="intraship_shipper_name">##shipper_name</label></dt>
											<dd>
												<input type="text" name="intraship[shipper_name]" value="<?php echo $intraship->shipper_name ?>" id="intraship_shipper_name">
											</dd>
											<dt><label for="intraship_shipper_street">##shipper_street</label></dt>
											<dd>
												<input type="text" name="intraship[shipper_street]" value="<?php echo $intraship->shipper_street ?>" id="intraship_shipper_street">
											</dd>
											<dt><label for="intraship_shipper_house">##shipper_house</label></dt>
											<dd>
												<input type="text" name="intraship[shipper_house]" value="<?php echo $intraship->shipper_house ?>" id="intraship_shipper_house">
											</dd>
											<dt><label for="intraship_shipper_postcode">##shipper_postcode</label></dt>
											<dd>
												<input type="text" name="intraship[shipper_postcode]" value="<?php echo $intraship->shipper_postcode ?>" id="intraship_shipper_postcode">
											</dd>
											<dt><label for="intraship_shipper_city">##shipper_city</label></dt>
											<dd>
												<input type="text" name="intraship[shipper_city]" value="<?php echo $intraship->shipper_city ?>" id="intraship_shipper_city">
											</dd>
											<dt><label for="intraship_shipper_contact">##shipper_contact</label></dt>
											<dd>
												<input type="text" name="intraship[shipper_contact]" value="<?php echo $intraship->shipper_contact ?>" id="intraship_shipper_contact">
											</dd>
											<dt><label for="intraship_shipper_email">##shipper_email</label></dt>
											<dd>
												<input type="text" name="intraship[shipper_email]" value="<?php echo $intraship->shipper_email ?>" id="intraship_shipper_email">
											</dd>
											<dt><label for="intraship_shipper_phone">##shipper_phone</label></dt>
											<dd>
												<input type="text" name="intraship[shipper_phone]" value="<?php echo $intraship->shipper_phone ?>" id="intraship_shipper_phone">
											</dd>
										</dl>
									</fieldset>

									<!-- COD -->
									<fieldset class="left">
										<legend>##cod_data</legend>
										<dl class="adminform">
											<dt><label for="intraship_cod_account_holder">##cod_account_holder</label></dt>
											<dd>
												<input type="text" name="intraship[cod_account_holder]" value="<?php echo $intraship->cod_account_holder ?>" id="intraship_cod_account_holder">
											</dd>
											<dt><label for="intraship_cod_account_number">##cod_account_number</label></dt>
											<dd>
												<input type="text" name="intraship[cod_account_number]" value="<?php echo $intraship->cod_account_number ?>" id="intraship_cod_account_number">
											</dd>
											<dt><label for="intraship_cod_bank_number">##cod_bank_number</label></dt>
											<dd>
												<input type="text" name="intraship[cod_bank_number]" value="<?php echo $intraship->cod_bank_number ?>" id="intraship_cod_bank_number">
											</dd>
											<dt><label for="intraship_cod_bank_name">##cod_bank_name</label></dt>
											<dd>
												<input type="text" name="intraship[cod_bank_name]" value="<?php echo $intraship->cod_bank_name ?>" id="intraship_cod_bank_name">
											</dd>
											<dt><label for="intraship_cod_iban">##cod_iban</label></dt>
											<dd>
												<input type="text" name="intraship[cod_iban]" value="<?php echo $intraship->cod_iban ?>" id="intraship_cod_iban">
											</dd>
											<dt><label for="intraship_cod_bic">##cod_bic</label></dt>
											<dd>
												<input type="text" name="intraship[cod_bic]" value="<?php echo $intraship->cod_bic ?>" id="intraship_cod_bic">
											</dd>
										</dl>
									</fieldset>

									<p style="clear: left; padding: 1em;">
										##note_required_fields
									</p>
									<p style="clear: left; padding: 1em;">
										<input class="button btn_wide" type="submit" value="##save">
									</p>
								</form>

								<p style="font-size: 0.8em; text-align: right; color: #888;"><?php echo $intraship->module_version ?></p>
							</td>
						</tr>
					</table>
				</td>

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
	$replacement = $intraship->get_text($matches[1]);
	if(empty($replacement)) {
		$replacement = $matches[1];
	}
	$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
}
echo $content;
require DIR_WS_INCLUDES . 'application_bottom.php';