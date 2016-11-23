<?php
/* --------------------------------------------------------------
   hermes_order.php 2016-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

require 'includes/application_top.php';
require DIR_FS_CATALOG .'/includes/classes/hermes.php';
require DIR_FS_CATALOG .'/admin/includes/classes/order.php';
require DIR_FS_CATALOG .'/admin/includes/classes/messages.php';

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);

function makeLink($options = null, $hrefmode = false) {
	$orders_id = $GLOBALS['orders_id'];
	$orderno = $GLOBALS['orderno'];
	$options = is_array($options) ? $options : array();
	$urloptions = array_merge(array('orders_id' => $orders_id, 'orderno' => $orderno), $options);
	$url = HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__).'?';
	$oparts = array();
	foreach($urloptions as $key => $value) {
		$oparts[] = $key.'='.$value;
	}
	$amp = $hrefmode ? '&amp;' : '&';
	$url .= implode($amp, $oparts);
	return $url;
}

function setOrdersStatus($orders_id, $orders_status_id, $orders_status_history_comment = '') {
	$orders_query = "UPDATE orders SET orders_status = :orders_status, last_modified = now() WHERE orders_id = :orders_id";
	$orders_query = strtr($orders_query, array(':orders_id' => $orders_id, ':orders_status' => $orders_status_id));
	xtc_db_query($orders_query);

	$orders_sh_query = "INSERT INTO orders_status_history (orders_id, orders_status_id, date_added, comments) ".
			"VALUES (:orders_id, :orders_status_id, now(), ':comments')";
	$orders_sh_query = strtr($orders_sh_query, array(':orders_id' => $orders_id, ':orders_status_id' => $orders_status_id,
			':comments' => xtc_db_input($orders_status_history_comment)));
	xtc_db_query($orders_sh_query);
}

ob_start();

$hermes = new Hermes();
$messages = new Messages('hermes_messages');

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 'checkavailability') {
	ob_clean();
	if($hermes->checkAvailability() == true) {
		echo '<span class="available">'.$hermes->get_text('webservice_available').'</span>';
	}
	else {
		echo '<span class="unavailable">'.$hermes->get_text('webservice_not_available').'</span>';
	}
	xtc_db_close();
	exit;
}

if(isset($_POST['orderprintlabel'])) {
	$orders_id = $_POST['orders_id'];
	$orderno = $_POST['orderno'];
	$printpos = $_POST['printpos'];
	$order = new HermesOrder($orderno);
	$hermes->orderPrintLabel($order, $printpos);
	$labelfile = $hermes->makeLabelFileName($order, $printpos);
	header('Content-Type: application/pdf');
	header('Content-Disposition: attachment; filename='.basename($labelfile));
	readfile($labelfile);
	gm_set_conf('HERMES_LASTPRINTPOS', $printpos - 1);
	$os_afterlabel = $hermes->getOrdersStatusAfterLabel();
	if($os_afterlabel !== '-1') {
		setOrdersStatus($orders_id, $os_afterlabel, $hermes->get_text('hermes_label_retrieved'));
	}
	xtc_db_close();
	exit;
}

if(!isset($_GET['orders_id'])) {
	xtc_redirect(FILENAME_ORDERS);
}
$orders_id = (int)$_GET['orders_id'];
$gm_order = new order($orders_id);

if(empty($_GET['orderno']) || $_GET['orderno'] == 'new') {
	$orderno = false;
}
else {
	$orderno = $_GET['orderno'];
}


if(isset($_POST['ordersave'])) {
	$order = new HermesOrder();
	$order->fillFromArray($_POST);
	try {
		$saveresult = $hermes->orderSave($order);
		if($saveresult !== true) {
			$messages->addMessage('FEHLER: '. $saveresult['code'] .' '. $saveresult['message']);
		}
		$os_aftersave = $hermes->getOrdersStatusAfterSave();
		if($os_aftersave !== '-1') {
			setOrdersStatus($orders_id, $os_aftersave, $hermes->get_text('hermes_order_saved'));
		}
	}
	catch(Exception $e) {
		$messages->addMessage('FEHLER: '. $e->getMessage());
	}
	xtc_redirect(makeLink(array('orders_id' => $order->orders_id, 'orderno' => $order->orderno)));
}

if(isset($_POST['ordercancel'])) {
	$order = new HermesOrder($_POST['orderno']);
	$cancelresult = $hermes->orderCancel($order);
	if($cancelresult !== true) {
		$messages->addMessage($cancelresult['code'] .' '. $cancelresult['message']);
	}
	xtc_redirect(makeLink(array('orders_id' => $order->orders_id, 'orderno' => '', 'debug' => '1')));
}

$shipper = $hermes->getPripsShipper();

if(isset($_POST['pripsprint'])) {
	if(isset($_POST['acceptanceliabilitylimit']) === false || isset($_POST['acceptancetac']) === false) {
		$messages->addMessage($hermes->get_text('acceptance_required'));
		xtc_redirect(makeLink(array('orders_id' => $_POST['orders_id'])));
	}

	$newshipper = $_POST['shipper'];
	$hermes->setPripsShipper($newshipper);
	$shipper = $hermes->getPripsShipper();

	$orderno = $_POST['orderno'];
	try {
		$order = new HermesOrder($orderno);
	}
	catch(Exception $e) {
		// not found, i.e. new
		$order = new HermesOrder();
	}
	$order->order_type = 'prips';
	$order->fillFromArray($_POST);
	$order->saveToDb();

	$t_raw_date = date('Y-m-d', strtotime($_POST['collection_desired_date']));
	$t_collection_desired_date = date('c', strtotime($t_raw_date));

	if($hermes->getService() == 'PriPS')
	{
		if($_POST['parcelclass'] == 'XS')
		{
			$_POST['parcelclass'] = 'HP';
		}
	}

	$labeldata = array(
			'shipper' => $shipper,
			'receiver' => $order->getReceiver(),
			'orderDetails' => array(
					'acceptanceLiabilityLimit' => 'YES',
					'acceptanceTermsAndConditions' => 'YES',
					'numberOfParcels' => '1',
					'parcelClass' => array($_POST['parcelclass']),
					'handOverMode' => $_POST['hand_over_mode'],
					'collectionDesiredDate' => $t_collection_desired_date,
					'bulkgoods' => array(false),
				),
		);
	try {
		$orderno = $hermes->pripsMakeLabel($labeldata);
		if($orderno !== false)
		{
			$order->deleteFromDb();
			$order->orderno = $orderno;
			$order->state = 'printed';
			$order->saveToDb();
			$hermes->storeTrackingNumber($order->orders_id, $orderno);
			xtc_redirect(makeLink(array('orders_id' => $order->orders_id, 'orderno' => $orderno)));
		}
		else
		{
			# save as mutable data for correction
			$order->saveToDb();
			$messages->addMessage($hermes->get_text('error_creating_prips_label'));
			xtc_redirect(makeLink(array('orders_id' => $order->orders_id, 'orderno' => $orderno)));
		}
	}
	catch(Exception $e) {
		# save as mutable data for correction
		$order->saveToDb();
		$messages->addMessage($hermes->get_text('error_creating_prips_label').': '.$e->getMessage());
		xtc_redirect(makeLink(array('orders_id' => $order->orders_id, 'orderno' => $orderno)));
	}
}

$hermesorders = HermesOrder::getOrders((int)$_GET['orders_id']);
if(count($hermesorders) == 1 && $orderno === false && !($_GET['orderno'] == 'new')) {
	xtc_redirect(makeLink(array('orderno' => $hermesorders[0]->orderno)));
}

if($orderno !== false) {
	$order = new HermesOrder($orderno);
}
else {
	$order = new HermesOrder();
	if(is_numeric($orders_id)) {
		$order->fillFromOrder($orders_id);
	}
}

$barcode = (!empty($order->shipping_id)) ? $order->shipping_id : false;

if($hermes->getService() == 'PriPS')
{
	$label_liabilitylimit = $hermes->getLabelAcceptanceLiabilityLimit();
	$label_tac = $hermes->getLabelAcceptanceTermsAndConditions();
	$url_tac = $hermes->getUrlTermsAndConditions();
}

$pclasses = $hermes->getPackageClasses();

$countries = Hermes::getCountries();
$scquery = xtc_db_query("SELECT * FROM countries WHERE countries_iso_code_3 IN ('". implode("','", $countries) ."')");
$shopcountries = array();
while($scrow = xtc_db_fetch_array($scquery)) {
	$shopcountries[$scrow['countries_iso_code_3']] = $scrow;
}

if($order->isMutable()) {
	$romode = '';
}
else {
	$romode = 'readonly';
}

$printpos = gm_get_conf('HERMES_LASTPRINTPOS');
if(is_numeric($printpos) === false) {
	$printpos = 0;
}
else {
	$printpos = ($printpos + 1) % 4;
}


/* ------- ProPSOrders -------- */

//$propsorders = $hermes->getPropsOrders();

/* messages */
$session_messages = $messages->getMessages();
$messages->reset();


?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<style>
		hr { clear: both; margin: 1em 0; }
		.hermesorder { font-family: sans-serif; font-size: 0.8em; }
		.hermesorder h1 { padding: 0; }
		.hermesorder h2 { font-size: 1em; }
		.hermesorder ul a:link { font: inherit; }
		.hermesorder a.current { font-style: italic; }
		.hermesorder form { display: block; overflow: auto; }
		.hwfloat { float: left; width: 47%; margin: 1ex 2px; }
		.fright { float: right; margin: 0 2em; clear: right; }
		.cl { clear: left; }
		.prips #fsbuttons { margin-top: 1.7em; }
		span.neworder { margin: 0 1ex; padding: 0 1ex; background: #FFD6D9; border: 1px solid #D35B63; }
		dl.form { overflow: auto; }
		dl.form dt, dl.form dd { float: left; margin: 1px 0; }
		dl.form dt { clear: left; font-weight: bold; width: 10em; }
		dl.form dd { }
		fieldset { border: none; background: #dddddd; margin: 1em 0; }
		legend { background: #C7E8F8; padding: 1ex 1em; box-shadow: 0 0 2px #000000; }
		.hermesorder input.button { width: auto; display: inline; margin: 4px 2px; }
		.hermesorder input[readonly] { color: #555; border: none; background: #eee;}
		/* .hermesorder div.label { margin: 3em 1em 0; text-align: center; } */
		.hermesorder div.label a.button { display: inline; padding: 1em 2em; }
		.availability { float: right; width: 25em; border: 1px solid #888; background: #eee; padding: 0.5ex 0.5em; }
		p.message { background: #ffa; border: 1px solid #faa; padding: 1ex 1em; }
		.orderlabel * { vertical-align: middle; }
		.printpos { display: inline-block; margin-bottom: -4px; }
		.printpos input { vertical-align: middle; margin: 0; }
		.cb { clear: both; }
		.overlay { position: absolute; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); color: #fff; text-align: center; padding-top: 15em; font-family: sans-serif; }
		table.hermesorders { margin-bottom: 1em; }
		table.hermesorders th, table.hermesorders td { padding: .2ex .5ex; }
		table.hermesorders th { background: #DDDDDD; }
		table.hermesorders td { background: #EEEEEE; }
		table.hermesorders a { font-size: 1.0em; }
		table.hermesorders tr.current_order td { background: #C7E8F8; }
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
				<td class="boxCenter hermesorder <?php echo strtolower($hermes->getService()) ?>" width="100%" valign="top">
					<div class="availability">
						##checking_availability
					</div>
					<h1 class="pageHeading">##order_entry</h1>

					<?php foreach($session_messages as $msg): ?>
						<p class="message"><?php echo $msg ?></p>
					<?php endforeach ?>

					<p class="fright">
						<a class="button" href="<?php echo HTTP_SERVER.DIR_WS_ADMIN.'orders.php?action=edit&oID='.$orders_id ?>">##to_order</a>
					</p>

					<?php if(!empty($hermesorders)): ?>
						<h2>##shipments_for_this_order:</h2>
						<?php if($orderno !== false): ?>
							<p class="fright">
							<a class="button" href="<?php echo xtc_href_link(basename(__FILE__), 'orders_id='.$orders_id.'&orderno=new') ?>">##new_order</a>
							</p>
						<?php endif ?>
						<table class="hermesorders">
							<tr><th>##order_no</th><th>##status</th></tr>
							<?php foreach($hermesorders as $ho): ?>
								<tr class="<?php if($ho->orderno == $orderno) { echo 'current_order'; } ?>">
									<td>
										<a href="<?php echo xtc_href_link(basename(__FILE__), 'orders_id='.$ho->orders_id.'&orderno='.$ho->orderno) ?>" class="<?php echo $orderno == $ho->orderno ? 'current' : '' ?>">
											<?php echo $ho->orderno ?>
										</a>
									</td>
									<td>##orderstate_<?php echo $ho->state ?></td>
								</tr>
							<?php endforeach ?>
						</table>
					<?php endif ?>

					<form action="<?php echo makeLink(array('orderno' => $order->orderno), true) ?>" method="post" class="cb">
						<input type="hidden" name="orders_id" value="<?php echo $order->orders_id ?>">
						<?php if($hermes->getService() == 'PriPS'): ?>
						<fieldset class="hwfloat" id="fsshipper">
							<legend>##sender</legend>
							<dl id="shipper" class="form">
								<dt><label for="shippertype">##shippertype:</label></dt>
								<dd>
									<?php if($order->isMutable()): ?>
									<select id="shippertype" name="shipper[shipperType]">
										<option value="PRIVATE" <?php echo $shipper['shipperType'] == 'PRIVATE' ? 'selected' : '' ?>>##private</option>
										<option value="COMMERCIAL" <?php echo $shipper['shipperType'] == 'COMMERCIAL' ? 'selected' : '' ?>>Unternehmer</option>
									</select>
									<?php else: ?>
									<?php echo $shipper['shipperType'] ?>
									<?php endif ?>
								</dd>
								<dt><label for="shipper_firstname">##firstname:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[firstname]" id="shipper_firstname" value="<?php echo $shipper['firstname'] ?>" size="25" maxlength="25"></dd>
								<dt><label for="shipper_lastname">##lastname:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[lastname]" id="shipper_lastname" value="<?php echo $shipper['lastname'] ?>" size="25" maxlength="25"></dd>
								<dt><label for="shipper_addressadd">##address_add:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[addressAdd]" id="shipper_addressadd" value="<?php echo $shipper['addressAdd'] ?>" size="25" maxlength="25"></dd>
								<dt><label for="shipper_street">##street:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[street]" id="shipper_street" value="<?php echo $shipper['street'] ?>" size="27" maxlength="27"></dd>
								<dt><label for="shipper_housenumber">##houseno:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[houseNumber]" id="shipper_housenumber" value="<?php echo $shipper['houseNumber'] ?>" size="5" maxlength="5"></dd>
								<dt><label for="shipper_postcode">##postcode:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[postcode]" id="shipper_postcode" value="<?php echo $shipper['postcode'] ?>" size="25" maxlength="25"></dd>
								<dt><label for="shipper_city">##city:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[city]" id="shipper_city" value="<?php echo $shipper['city'] ?>" size="25" maxlength="25"></dd>
								<dt><label for="shipper_district">##district:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[district]" id="shipper_district" value="<?php echo $shipper['district'] ?>" size="25" maxlength="25"></dd>
								<dt><label for="shipper_countrycode">##country:</label></dt>
								<dd>
									<?php if($order->isMutable()): ?>
									<select <?php echo $romode ?> name="shipper[countryCode]" id="shipper_countrycode" size="1">
										<?php foreach($countries as $ccode): ?>
											<option value="<?php echo $ccode ?>" <?php echo $shipper['countryCode'] == $ccode ? 'selected' : ''?>><?php echo $shopcountries[$ccode]['countries_name'] ?></option>
										<?php endforeach ?>
									</select>
									<?php else: ?>
									<?php echo $shopcountries[$shipper['countryCode']]['countries_name'] ?>
									<?php endif ?>
								</dd>
								<dt><label for="shipper_telephonenumber">##phone:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[telephoneNumber]" id="shipper_telephonenumber" value="<?php echo $shipper['telephoneNumber'] ?>" size="25" maxlength="25"></dd>
								<dt><label for="shipper_telephoneprefix">##area_code:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[telephonePrefix]" id="shipper_telephoneprefix" value="<?php echo $shipper['telephonePrefix'] ?>" size="25" maxlength="25"></dd>
								<dt><label for="shipper_email">##email:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="shipper[email]" id="shipper_email" value="<?php echo $shipper['email'] ?>" size="25" maxlength="25"></dd>
							</dl>
						</fieldset>
						<?php endif ?>
						<fieldset class="hwfloat" id="fsreceiver">
							<legend>##receiver</legend>
							<dl id="neworder" class="form">
								<dt><label for="firstname">##firstname:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_firstname" id="firstname" value="<?php echo $order->receiver_firstname ?>" size="25" maxlength="25"></dd>
								<dt><label for="lastname">##lastname:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_lastname" id="lastname" value="<?php echo $order->receiver_lastname ?>" size="25" maxlength="25"></dd>
								<dt><label for="street">##street:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_street" id="street" value="<?php echo $order->receiver_street ?>" size="27" maxlength="27"></dd>
								<dt><label for="housenumber">##houseno:</label></dt>
								<dd>
									<input <?php echo $romode ?> type="text" name="receiver_housenumber" id="housenumber" value="<?php echo $order->receiver_housenumber ?>" size="5" maxlength="5">
									<em>##note_houseno</em>
								</dd>
								<dt><label for="addressadd">##address_add:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_addressadd" id="addressadd" value="<?php echo $order->receiver_addressadd ?>" size="25" maxlength="25"></dd>
								<dt><label for="postcode">##postcode:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_postcode" id="postcode" value="<?php echo $order->receiver_postcode ?>" size="25" maxlength="25"></dd>
								<dt><label for="city">##city:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_city" id="city" value="<?php echo $order->receiver_city ?>" size="30" maxlength="30"></dd>
								<dt><label for="district">##district:</label></dt>
								<dd>
									<input <?php echo $romode ?> type="text" name="receiver_district" id="district" value="<?php echo $order->receiver_district ?>" size="25" maxlength="25">
									<em>##for_ireland</em>
								</dd>
								<dt><label for="countrycode">Land:</label></dt>
								<dd>
									<?php if($order->isMutable()): ?>
									<select <?php echo $romode ?> name="receiver_countrycode" id="countrycode" size="1">
										<?php foreach($countries as $ccode): ?>
											<option value="<?php echo $ccode ?>" <?php echo $order->receiver_countrycode == $ccode ? 'selected' : ''?>><?php echo $shopcountries[$ccode]['countries_name'] ?></option>
										<?php endforeach ?>
									</select>
									<?php else: ?>
										<?php echo $shopcountries[$order->receiver_countrycode]['countries_name'] ?>
									<?php endif ?>
								</dd>
								<dt><label for="email">##email:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_email" id="email" value="<?php echo $order->receiver_email ?>" size="50" maxlength="250"></dd>
								<dt><label for="telephonenumber">##phone:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_telephonenumber" id="telephonenumber" value="<?php echo $order->receiver_telephonenumber ?>" size="32" maxlength="32"></dd>
								<dt><label for="telephoneprefix">##area_code:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="receiver_telephoneprefix" id="telephoneprefix" value="<?php echo $order->receiver_telephoneprefix ?>" size="25" maxlength="25"></dd>
								<dt><label for="paket_shop_id">##parcel_shop_id:</label></dt>
								<dd><input <?php echo $romode ?> type="text" name="paket_shop_id" id="paket_shop_id" value="<?php echo $order->paket_shop_id ?>"></dd>
							</dl>
						</fieldset>
						<fieldset class="hwfloat <?php echo $hermes->getService() == "PriPS" ? 'cl' : '' ?>" id="fsorderdata">
							<legend>##shipment_data</legend>
							<dl id="orderdata" class="form">
								<dt><label for="orderno">##order_no:</label></dt>
								<dd>
									<input type="text" name="orderno" id="orderno" value="<?php echo $order->orderno ?>" readonly>
									<?php if($orderno === false): ?>
										<span class="neworder">##new_order</span>
									<?php endif ?>
								</dd>
								<dt><label for="state">##status:</label></dt>
								<dd>
									<div style="display: none">
										<select name="state" size="1">
											<?php foreach(HermesOrder::getValidStates() as $vstate): ?>
												<option value="<?php echo $vstate ?>" <?php echo $vstate == $order->state ? 'selected' : '' ?>><?php echo $vstate ?></option>
											<?php endforeach ?>
										</select>
									</div>
									<?php #echo HermesOrder::getStateName($order->state) ?>
									##state_<?php echo $order->state ?>
								</dd>
								<?php if($hermes->getService() == 'ProPS'): ?>
									<dt><label for="clientreferencenumber">Referenznummer:</label></dt>
									<dd><input <?php echo $romode ?> type="text" name="clientreferencenumber" id="clientreferencenumber" value="<?php echo $order->clientreferencenumber ?>"></dd>
								<?php endif ?>
								<?php if(true || $hermes->getService() == "ProPS"): ?>
									<dt><label for="parcelclass">##parcel_class:</label></dt>
									<dd>
										<?php if($order->isMutable()): ?>
											<select name="parcelclass" id="parcelclass">
												<?php foreach($pclasses as $pclass => $pcinfo): ?>
													<option value="<?php echo $pclass ?>" <?php echo $pclass == $order->parcelclass ? 'selected' : '' ?>>
														<?php echo $pcinfo['name'] .' - '. $pcinfo['desc'] ?>
													</option>
												<?php endforeach ?>
											</select>
										<?php else: ?>
											<?php echo $pclasses[$order->parcelclass]['name'] ?>
										<?php endif ?>
									</dd>
								<?php else: // Service == PriPS ?>
									<dt><label for="parcelclass">##parcel_classes:</label></dt>
									<dd>
										##number_of_parcels:
										<table>
											<?php foreach($pclasses as $pclass => $pcinfo): ?>
											<tr>
												<td class="parcelclasslabel">
													<?php echo $pcinfo['name'] ?>
												</td>
												<td>
													<input <?php echo $romode ?> name="<?php echo 'parcelclasses['.$pclass.']' ?>" type="text" size="3" maxlength="3" value="<?php echo $order->getParcelclasses($pclass) ?>">
												</td>
											</tr>
											<?php endforeach ?>
										</table>
									</dd>
								<?php endif ?>
								<?php if($hermes->getService() == "ProPS"): ?>
									<dt><label for="amountcashondeliveryeurocent">##cod_amount:</label></dt>
									<dd><input <?php echo $romode ?> type="text" name="amountcashondeliveryeurocent" id="amountcashondeliveryeurocent" value="<?php echo number_format($order->amountcashondeliveryeurocent / 100, 2, '.', '') ?>"> EUR</dd>
								<?php endif ?>
								<?php if($hermes->getService() == "PriPS"): ?>
									<dt><label for="handovermode">##handover_mode:</label></dt>
									<dd>
										<?php if($order->isMutable() == true): ?>
											<select name="hand_over_mode" id="handovermode" size="1">
												<option value="PS" <?php echo $order->hand_over_mode == 'PS' ? 'selected' : '' ?>>##handover_ps</option>
												<option value="S2S" <?php echo $order->hand_over_mode == 'S2S' ? 'selected' : ''?>>##handover_s2s</option>
												<option value="COL" <?php echo $order->hand_over_mode == 'COL' ? 'selected' : '' ?>>##handover_col</option>
											</select>
										<?php else: ?>
											##handover_<?php echo strtolower($order->hand_over_mode) ?>
										<?php endif ?>
									</dd>
									<dt><label for="collection_desired_date">##collection_desired_date:</label></dt>
									<dd><input <?php echo $romode ?> type="text" name="collection_desired_date" id="collection_desired_date" value="<?php echo date('Y-m-d', strtotime('tomorrow')) ?>"></dd>
								<?php endif ?>
								<?php if($barcode !== false): ?>
									<dt><label for="shippingid">##barcode:</label></dt>
									<dd class="shippingid"><?php echo $barcode ?></dd>
									<dt><label for="shipmentstatus">##shipment_status:</label></dt>
									<dd id="shipmentstatus">##loading</dd>
								<?php endif ?>
							</dl>
						</fieldset>
						<fieldset class="hwfloat" id="fsbuttons">
							<?php if($hermes->getService() == 'ProPS'): ?>
								<?php if($order->isMutable()): ?>
									<input type="submit" name="ordersave" value="##save_and_send" class="button showwork">
								<?php endif ?>
								<?php if($orderno !== false && $order->isMutable()): ?>
									<input type="submit" name="ordercancel" value="##cancel_order" class="button confirm showwork">
								<?php endif ?>
								<?php if($order->state == 'sent' || $order->state == 'printed'): ?>
									<div class="orderlabel">
										<input type="submit" name="orderprintlabel" value="##print_label" class="button">
										<div class="printpos">
											<input type="radio" name="printpos" value="1" title="##position 1" <?php echo $printpos == 0 ? 'checked="checked"' : '' ?>>
											<input type="radio" name="printpos" value="2" title="##position 2" <?php echo $printpos == 1 ? 'checked="checked"' : '' ?>><br>
											<input type="radio" name="printpos" value="3" title="##position 3" <?php echo $printpos == 2 ? 'checked="checked"' : '' ?>>
											<input type="radio" name="printpos" value="4" title="##position 4" <?php echo $printpos == 3 ? 'checked="checked"' : '' ?>>
										</div>
									</div>
								<?php endif ?>
							<?php else: // PriPS ?>
								<?php if($order->isMutable() == true): ?>
									<input type="checkbox" name="acceptanceliabilitylimit" id="accliab" value="1">
									<label for="accliab"><?php echo $label_liabilitylimit ?></label><br>
									<input type="checkbox" name="acceptancetac" id="acctac" value="1">
									<label for="acctac"><?php echo $label_tac ?></label>
									<a class="newwindow" href="<?php echo $url_tac ?>">(##display)</a>
									<br>
									<input type="submit" name="pripsprint" value="##print_label" class="button showwork" id="pripsprint">
								<?php elseif($order->state == 'printed' && file_exists(DIR_FS_CATALOG .'/cache/hermes_labels/'. $order->orderno .'.pdf') == true): ?>
									<a class="label button" target="_new" href="<?php echo GM_HTTP_SERVER.DIR_WS_CATALOG .'/cache/hermes_labels/'. $order->orderno .'.pdf'?>">##get_label</a>
								<?php endif?>
							<?php endif ?>
						</fieldset>
					</form>

					<a class="button" href="<?php echo HTTP_SERVER.DIR_WS_ADMIN.'orders.php?action=edit&oID='.$orders_id ?>">##to_order</a>

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
				$('a.newwindow').click(function(e) {
					e.preventDefault();
					window.open($(this).attr('href'));
				});
				$('.confirm').click(function(e) {
					return window.confirm('##really_delete');
				});
				$('.showwork').click(function(e) {
					$('body').prepend($('<div class="overlay">##working</div>'));
				});

				$('#pripsprint').click(function(e) {
					if(!($('#accliab:checked').val() == '1' && $('#acctac:checked').val() == '1')) {
						e.preventDefault();
						alert("<?php echo str_replace("\n", '\n', $hermes->get_text('note_mustconfirm')) ?>");
					}
				});

				$('#shipmentstatus').load('hermes_list.php', { 'shipmentstatus': $('.shippingid').text() });

				$('.availability').load('hermes_order.php', { 'ajax': 'checkavailability' });
			});
		</script>
	</body>
</html>
<?php
echo $hermes->replaceTextPlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');