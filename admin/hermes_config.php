<?php
/* --------------------------------------------------------------
   hermes_config.php 2015-09-28 gm
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

require('includes/application_top.php');
require DIR_FS_CATALOG .'/admin/includes/classes/messages.php';
require DIR_FS_CATALOG .'/includes/classes/hermes.php';

defined('GM_HTTP_SERVER') OR define('GM_HTTP_SERVER', HTTP_SERVER);
define('PAGE_URL', GM_HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

$hermes = new Hermes();
$messages = new Messages('hermes_messages');

$service = $hermes->getService();
$username = $hermes->getUsername();
$password = $hermes->getPassword();
$sandboxmode = $hermes->getSandboxmode();
$os_aftersave = $hermes->getOrdersStatusAfterSave();
$os_afterlabel = $hermes->getOrdersStatusAfterLabel();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$hermes->setService($_POST['service']);
	$hermes->setUsername($_POST['username']);
	$hermes->setPassword($_POST['password']);
	$hermes->setSandboxmode(isset($_POST['sandboxmode']));
	$hermes->setOrdersStatusAfterSave($_POST['os_aftersave']);
	$hermes->setOrdersStatusAfterLabel($_POST['os_afterlabel']);
	$hermes->setParcelServiceId($_POST['parcelservice']);
	$messages->addMessage($hermes->get_text('configuration_saved'));
	xtc_redirect(PAGE_URL);
}

$orders_status = array();
$os_query = "SELECT * FROM orders_status WHERE language_id = :language_id ORDER BY orders_status_id";
$os_query = strtr($os_query, array(':language_id' => $_SESSION['languages_id']));
$os_result = xtc_db_query($os_query);
while($os_row = xtc_db_fetch_array($os_result)) {
	$orders_status[$os_row['orders_status_id']] = $os_row['orders_status_name'];
}

$service_selected = array();
if($service == 'PriPS') {
	$service_selected['PriPS'] = 'checked="checked"';
	$service_selected['ProPS'] = '';
}
else {
	$service_selected['ProPS'] = 'checked="checked"';
	$service_selected['PriPS'] = '';
}

$parcelServiceReader = MainFactory::create('ParcelServiceReader');
$parcelServices = $parcelServiceReader->getAllParcelServices();
$parcelServiceId = $hermes->getParcelServiceId();

/* messages */
$session_messages = $messages->getMessages();
$messages->reset();

ob_start();
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<style>
		.hermesorder { font-family: sans-serif; font-size: 0.8em; }
		.hermesorder h1 { padding: 0; }
		.hermesorder a:link { font-size: inherit; text-decoration: underline; }
		.hermesorder p.message { background: #ffa; border: 1px solid #faa; padding: 1ex 1em; }
		.hermesorder dl.form { overflow: auto; }
		.hermesorder dl.form dt, dl.form dd { float: left; margin: .5ex 0; }
		.hermesorder dl.form dt { clear: left; width: 15em; }
		.hermesorder dl.form dt label:after { content: ':';}
		.hermesorder dl.form dt { margin-right: 1.5em; }
		.hermesorder input { vertical-align: middle; }
		.hermesorder input[type="text"] { width: 25em; }

		form.bluegray {font-size: 0.9em; }
		form.bluegray fieldset {border: none; padding: 0; margin: 1ex 0 0 0; }
		form.bluegray legend {font-weight: bolder; font-size: 1.4em; background: #585858; color: #FFFFFF; padding: .2ex 0.5%; width: 99%; }
		form.bluegray dl.adminform {margin: 0; }
		form.bluegray dl.adminform dt, form.bluegray dl.adminform dd {line-height: 1.3; padding: 3px 0; margin: 0; }
		form.bluegray dl.adminform dt {width: 20%; float: left; font-weight: bold; padding: 2px;}
		form.bluegray dl.adminform dd {border-bottom: 1px dotted rgb(90, 90, 90); width: 78%; float: none; padding-left: 22%; background-color: #F7F7F7; }
		form.bluegray dl.adminform dd:nth-child(4n) {background: #D6E6F3; }
		dd.status_select { min-height: 3em; }
		</style>
	</head>
	<body>
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
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
				<td class="boxCenter hermesorder" width="100%" valign="top">
					<h1>##hermes_configuration</h1>

					<?php foreach($session_messages as $msg): ?>
					<p class="message"><?php echo $msg ?></p>
					<?php endforeach ?>

					<form action="<?php echo PAGE_URL ?>" method="POST" id="hermesconfig" class="bluegray">
						<dl class="form adminform">
							<dt>
								##service
							</dt>
							<dd>
								<input type="radio" name="service" value="ProPS" id="service_props" <?php echo $service_selected['ProPS'] ?>>
								<label for="service_props">##service_props</label><br>
								<input type="radio" name="service" value="PriPS" id="service_prips" <?php echo $service_selected['PriPS'] ?>>
								<label for="service_prips">##service_prips</label>
							</dd>
							<dt class="props_only">
								<label for="username">##username</label>
							</dt>
							<dd class="props_only">
								<input id="username" name="username" type="text" value="<?php echo $username ?>">
							</dd>
							<dt class="props_only">
								<label for="password">##password</label>
							</dt>
							<dd class="props_only">
								<input id="password" name="password" type="text" value="<?php echo $password ?>">
							</dd>
							<dt>
								<label for="sandboxmode">##sandbox_mode</label>
							</dt>
							<dd>
								<input type="checkbox" value="1" name="sandboxmode" id="sandboxmode" <?= $sandboxmode ? 'checked="checked"' : '' ?>>
								##activate_for_testing_only
							</dd>
							<dt class="props_only">
								<label for="os_aftersave">##orderstatus_after_save</label>
							</dt>
							<dd class="props_only status_select">
								<select id="os_aftersave" name="os_aftersave">
									<option value="-1" <?php echo $os_aftersave == '-1' ? 'selected="selected"' : '' ?>>##dont_change</option>
									<?php foreach($orders_status as $os_id => $os_name): ?>
										<option value="<?php echo $os_id ?>" <?php echo $os_aftersave == $os_id ? 'selected="selected"' : '' ?>><?php echo $os_name ?></option>
									<?php endforeach ?>
								</select>
							</dd>
							<dt>
								<label for="os_afterlabel">##orderstatus_after_label</label>
							</dt>
							<dd class="status_select">
								<select id="os_afterlabel" name="os_afterlabel">
									<option value="-1" <?php echo $os_afterlabel == '-1' ? 'selected="selected"' : '' ?>>##dont_change</option>
									<?php foreach($orders_status as $os_id => $os_name): ?>
										<option value="<?php echo $os_id ?>" <?php echo $os_afterlabel == $os_id ? 'selected="selected"' : '' ?>><?php echo $os_name ?></option>
									<?php endforeach ?>
								</select>
							</dd>
							<dt>
								<label for="parcelservice">##parcelservice</label>
							</dt>
							<dd>
								<select name="parcelservice">
									<option value="0" <?php if($parcelServiceId == 0) echo 'selected="selected"' ?>>##no_parcel_service</option>
									<?php foreach($parcelServices as $parcelService): ?>
										<option value="<?php echo $parcelService->getId() ?>" <?php if($parcelServiceId == $parcelService->getId()) echo 'selected="selected"' ?>>
											<?php echo $parcelService->getName(); ?>
										</option>
									<?php endforeach ?>
								</select>
							</dd>
						</dl>
						<input class="button" type="submit" value="##save">
					</form>
				</td>
				<!-- body_text_eof //-->

			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
		<script>
		$(function() {
			$('#hermesconfig').delegate('input[name="service"]', 'change', function(e) {
				var service = $(this).val();
				if(service != 'ProPS') {
					$('.props_only input, .props_only select').attr('disabled', 'disabled');
				}
				else {
					$('.props_only input, .props_only select').removeAttr('disabled');
				}
			});
			$('input[name="service"]:checked').change();
		});
		</script>
	</body>
</html>
<?php
echo $hermes->replaceTextPlaceholders(ob_get_clean());

require(DIR_WS_INCLUDES . 'application_bottom.php');
