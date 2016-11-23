<?php
/* --------------------------------------------------------------
   orders_iloxx.php 2016-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
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

require('includes/application_top.php');
defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);
define('PAGE_URL', GM_HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

if(!is_array($_SESSION[basename(__FILE__)])) {
	$_SESSION[basename(__FILE__)] = array();
}
if(!is_array($_SESSION[basename(__FILE__)]['messages'])) {
	$_SESSION[basename(__FILE__)]['messages'] = array();
}

function getOrdersProducts($orders_id) {
	$query = "SELECT op.orders_products_id, op.products_quantity, op.products_name, p.products_weight FROM `orders_products` op
				left join products p on p.products_id = op.products_id
				where orders_id = :orders_id";
	$query = strtr($query, array(':orders_id' => (int)$orders_id));
	$result = xtc_db_query($query);
	$opdata = array();
	while($row = xtc_db_fetch_array($result)) {
		$opdata[] = $row;
	}
	return $opdata;
}

function getOrderData($orders_ids, $use_weight_options = false) {
	$orders = array();
	if(!empty($orders_ids)) {
		$orders_ids = array_slice($orders_ids, 0, 100); // web service will accept up to 100 entries
		$oquery = "SELECT o.*, ot.value FROM `orders` o ".
			"left join orders_total ot on ot.orders_id = o.orders_id and ot.class = 'ot_total' ".
			"WHERE o.orders_id IN (".implode(',', $orders_ids).") ";
		$oresult = xtc_db_query($oquery, 'db_link', false);
		while($orow = xtc_db_fetch_array($oresult)) {
			$opdata = getOrdersProducts($orow['orders_id']);
			$orders_weight = 0;
			$content = array();
			foreach($opdata as $od) {
				$orders_weight += $od['products_quantity'] * $od['products_weight'];
				$content[] = $od['products_quantity'] .' '. $od['products_name'];
			}

			// begin (adapted) copy from includes/classes/shipping.php
			$shipping_num_boxes = 1;
			if($use_weight_options) {
				$shipping_weight = $orders_weight;

				if(SHIPPING_BOX_WEIGHT >= $shipping_weight*SHIPPING_BOX_PADDING/100) {
					$shipping_weight = $shipping_weight+SHIPPING_BOX_WEIGHT;
				}
				else {
					$shipping_weight = $shipping_weight + ($shipping_weight*SHIPPING_BOX_PADDING/100);
				}

				if($shipping_weight > SHIPPING_MAX_WEIGHT) { // Split into many boxes
					$shipping_num_boxes = ceil($shipping_weight/SHIPPING_MAX_WEIGHT);
					$shipping_weight = $shipping_weight/$shipping_num_boxes;
				}
				// end copy from includes/classes/shipping.php
				$orders_weight = $shipping_weight;
			}
			$orow['orders_weight'] = $orders_weight;
			$orow['shipping_num_boxes'] = $shipping_num_boxes;
			$orow['content'] = implode(', ', $content);
			$orders[] = $orow;
		}
	}
	else {
	}
	return $orders;
}

$iloxx = new GMIloxx();

if(isset($_GET['dl_labels'])) {
	header('Content-Type: application/pdf');
	header('Content-Disposition: attachment; filename=iloxx_labels.pdf');
	readfile($iloxx->getLabelsFileName());
	xtc_db_close();
	exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
	switch($_POST['cmd']) {
		case 'select_orders':
			$_SESSION['iloxx_debug'] = print_r($_POST, true);
			$_SESSION['iloxx']['return_uri'] = $_POST['return_uri'];
			$_SESSION['iloxx']['orders_ids'] = explode('_', rtrim($_POST['checked_ids'], '_'));
			break;
		case 'add_orders':
			$orders_ids = $_SESSION['iloxx']['orders_ids'];
			if(isset($_POST['remove_selected']) && !empty($_POST['selected'])) {
				$selected_ids = array_keys($_POST['selected']);
				$orders_ids = array_diff($orders_ids, $selected_ids);
				$_SESSION['iloxx']['orders_ids'] = $orders_ids;
			}
			$orders = getOrderData($orders_ids);
			foreach($orders as $idx => $order) {
				$orders[$idx]['orders_weight'] = (double)$_POST['orders_weight'][$order['orders_id']];
				$orders[$idx]['iloxx_service'] = $_POST['ordertype'][$order['orders_id']];
				$iloxxdata = $iloxx->getOrderIloxxData($order['orders_id']);
				$iloxxdata['weight'] = (double)$_POST['orders_weight'][$order['orders_id']];
				$iloxxdata['service'] = $_POST['ordertype'][$order['orders_id']];
				$iloxxdata['shipdate'] = $_POST['shipdate'];
				$iloxx->setOrderIloxxData($iloxxdata['orders_id'], $iloxxdata['parcelnumber'], $iloxxdata['service'], $iloxxdata['weight'], $iloxxdata['shipdate']);
			}
			if(isset($_POST['check'])) {
				$result = $iloxx->addOrder($orders, $_POST['shipdate'], $_POST['labelsize'], $_POST['labelpos'], 'check');
				if($result === false) {
					foreach($iloxx->_service_errors as $service_error) {
						$_SESSION[basename(__FILE__)]['messages'][] = $service_error;
					}
				}
				else {
					$_SESSION[basename(__FILE__)]['messages'][] = $iloxx->get_text('all_addresses_ok');
				}
			}
			else if(isset($_POST['addorders'])) {
				$result = $iloxx->addOrder($orders, $_POST['shipdate'], $_POST['labelsize'], $_POST['labelpos'], 'addOrder');
				if($result !== false) {
					$_SESSION[basename(__FILE__)]['messages'][] = $iloxx->get_text('labels_created');
					$_SESSION[basename(__FILE__)]['labels_ready'] = true;
				}
				else
				{
					$_SESSION[basename(__FILE__)]['messages'][] = $iloxx->get_text('error_creating_labels_check_addresses');
				}
			}
			break;
		default:
			die("Just what do you think you're doing, Dave?");
	}
	xtc_redirect(PAGE_URL);
}

if(isset($_GET['oID']))
{
	$_SESSION['iloxx']['orders_ids'] = [(int)$_GET['oID']];
}
else if(isset($_GET['orders_id']) && is_array($_GET['orders_id']))
{
	$_SESSION['iloxx']['orders_ids'] = $_GET['orders_id'];
}
$return_uri = $_SESSION['iloxx']['return_uri'];
$orders_ids = $_SESSION['iloxx']['orders_ids'];
$orders = getOrderData($orders_ids, $iloxx->use_weight_options);

$messages = $_SESSION[basename(__FILE__)]['messages'];
$_SESSION[basename(__FILE__)]['messages'] = array();

if($_SESSION[basename(__FILE__)]['labels_ready'] === true) {
	$_SESSION[basename(__FILE__)]['labels_ready'] = false;
	header('Refresh: 1; url='.PAGE_URL.'?dl_labels=1');
}

ob_start();

?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<link rel="stylesheet" type="text/css" href="includes/stylesheet_iloxx.css">
		<style>
		input.small { width: 5em; }
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

				<td class="boxCenter" width="100%" valign="top">
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="credits">
						<tr>
							<td>
								<table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td class="pageHeading" style="padding-left: 0px">##order_preparation</td>
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
								<p class="message"><?php echo htmlspecialchars_wrapper($msg) ?></p>
								<?php endforeach ?>

								<form action="<?php echo PAGE_URL ?>" method="POST" name="iloxx_orders">
									<input type="hidden" name="cmd" value="add_orders">
									<table class="orders">
										<thead>
											<tr>
												<th><input id="selectall" type="checkbox"></th>
												<th>##orders_id</th>
												<th>##recipient_name</th>
												<th>##recipient_address</th>
												<th>##contents</th>
												<th>##shipping_type</th>
												<th>##value</th>
												<th>##weight</th>
												<th>##parcel_no</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach($orders as $o): ?>
											<?php $idata = $iloxx->getOrderIloxxData($o['orders_id'], $o['payment_method'] == 'cod'); ?>
											<?php $weight = $idata['weight'] > 0 ? $idata['weight'] : $o['orders_weight']; ?>
											<tr>
												<td>
													<input type="checkbox" name="selected[<?php echo $o['orders_id'] ?>]" value="1">
												</td>
												<td><?php echo $o['orders_id'] ?></td>
												<td><?php echo $o['customers_name'] ?></td>
												<td><?php
													echo $o['delivery_name'] .'<br>'.
														$o['delivery_company'] .'<br>'.
														$o['delivery_street_address'] . ' ' . $o['delivery_house_number'] . '<br>'.
														$o['delivery_postcode'] .' '. $o['delivery_city'] .'<br>'.
														$iloxx->getISO3fromISO2($o['delivery_country_iso_code_2']);
													?>
												</td>
												<td><?php echo $o['content'] ?></td>
												<td>
													<select name="ordertype[<?php echo $o['orders_id'] ?>]">
														<?php foreach(GMIloxx::getShipServices() as $key => $name): ?>
														<option value="<?php echo $key ?>"<?php echo $key == $idata['service'] ? ' selected="selected"' : '' ?>><?php echo $name ?></option>
														<?php endforeach ?>
													</select>
												</td>
												<td>
													<input class="small" type="text" name="orders_codamount[<?php echo $o['orders_id'] ?>" value="<?php echo number_format($o['value'], 2, '.', '') ?>">
													<?php echo $o['currency'] ?>
												</td>
												<td>
													<input class="small" type="text" name="orders_weight[<?php echo $o['orders_id'] ?>]" value="<?php echo $weight ?>"> kg
													<?php if($iloxx->use_weight_options): ?>
														<br>
															(<?php echo $o['shipping_num_boxes'] ?> ##packets)
													<?php endif ?>
												</td>
												<td>
														<?php echo empty($idata['parcelnumber']) ? '<em>##none_yet</em>' : $idata['parcelnumber'] ?>
												</td>
											</tr>
											<?php endforeach ?>
										</tbody>
									</table>
									<br>
									<label for="labelsize">##label_format:</label>
									<select name="labelsize">
										<option value="a4">A4 (##multiple_labels_per_page)</option>
										<option value="a6">A6 (##one_label_per_page)</option>
									</select>
									<br>
									<label for="labelpos">##afour_starting_position:</label>
									<select name="labelpos">
										<option value="ul">##top_left</option>
										<option value="ur">##top_right</option>
										<option value="ll">##bottom_left</option>
										<option value="lr">##bottom_right</option>
									</select>
									<br>
									<label for="shipdate">##shipping_date:</label>
									<input
									  id="shipdate"
									  name="shipdate"
									  type="text"
									  placeholder="##.##.####"
									  value="<?= date('Y-m-d'); ?>"
									  data-gx-widget="datetimepicker"
			  						  data-datetimepicker-lang="de"
									  data-datetimepicker-format="Y-m-d"
									  data-datetimepicker-day-of-week-start="1"
									  data-datetimepicker-timepicker="false"
									  >
									<br>
									<input type="submit" name="check" value="##check_addresses" class="button button_wide">
									<input type="submit" name="addorders" value="##retrieve_labels" class="button button_wide"><br>
									<input type="submit" name="remove_selected" value="##remove_selected" class="button button_wide">
								</form>

							</td><!--  main  -->
						</tr>
					</table>
				</td>

				<!-- body_text_eof //-->

			</tr>
		</table>
		<!-- body_eof //-->

		<script>
		$(function() {
			$('form[name="iloxx_orders"]').delegate('#selectall', 'click', function(e) {
				var checked = $(this).get(0).checked;
				$('form[name="iloxx_orders"] input[type="checkbox"]').not(this).each(function() {
					$(this).get(0).checked = checked;
				});
			});
		});
		</script>

		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php
echo $iloxx->replaceTextPlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');
