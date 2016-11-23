<?php
/* --------------------------------------------------------------
   hermes_collection.php 2016-07-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

ob_start();
require('includes/application_top.php');
require DIR_FS_CATALOG .'/includes/classes/hermes.php';
require DIR_FS_CATALOG .'/admin/includes/classes/messages.php';

$hermes = new Hermes();
$messages = new Messages('hermes_messages');

if(isset($_REQUEST['load_collorders'])) {
	$corders = $hermes->getCollectionOrders();
	if(is_array($corders)) {
		echo '<table class="corders">';
		echo '<tr><th>##date</th><th>##period</th><th>Art</th><th>##number_of_parcels</th><th>##volume</th><th>##more_than_two_cub_m</th><th>##storno</th></tr>';
		foreach($corders as $co) {
			echo '<tr>';
			echo "<td>".date('Y-m-d (D)', strtotime($co->collectionDate))."</td>";
			echo "<td>".$co->timeframe."</td>";
			echo "<td>".$co->collectionType."</td>";
			echo "<td>".$co->numberOfParcels."</td>";
			echo "<td>".($co->volume > 0 ? $co->volume . " m³" : '')."</td>";
			echo "<td>".($co->moreThan2ccm == 'YES' ? 'ja' : 'nein')."</td>";
			echo '<td><form action="" method="POST"><input type="hidden" name="datetime" value="'.$co->collectionDate.'">';
			echo '<input type="submit" value="##cancel"></form></td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	else if(is_string($corders)) {
		echo '<p>'.$corders.'</p>';
	}
	else {
		echo '<p>##cannot_retrieve_data</p>';
	}
	echo $hermes->replaceTextPlaceholders(ob_get_clean());
	xtc_db_close();
	exit;
}

if(!empty($_POST)) {
	if(!empty($_POST['datetime'])) {
		$result = $hermes->collectionCancel($_POST['datetime']);
		if($result === true) {
			$messages->addMessage($hermes->get_text('order_cancelled'));
		}
		else {
			$messages->addMessage($hermes->get_text('order_not_cancelled'));
		}
	}

	if(!empty($_POST['date'])) {
		$_POST['time'] = '12:00';
		$datestring = $_POST['date'] .' '. $_POST['time'] .':00 CET';
		$timestamp = strtotime($datestring);
		$datetime = gmdate('c', $timestamp);
		try
		{
			$result = $hermes->addPropsCollectionRequest($datetime, $_POST['packets']);
			if($result !== true && is_array($result)) {
				$messages->addMessage($hermes->get_text('error').': '. $result['code'] .' '. $result['message']);
			}
			if(is_string($result)) {
				$messages->addMessage($hermes->get_text('order_saved_w_number').' '. $result);
				}
			}
		catch(SoapFault $e)
		{
			$messages->addMessage($hermes->get_text('error').': '.$e->getMessage());
		}
	}
	
	xtc_redirect(HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));
}

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
		<link rel="stylesheet" type="text/css" href="<?php echo HTTP_SERVER . DIR_WS_CATALOG; ?>gm/javascript/jquery/ui/datepicker/css/ui-lightness/jquery-ui-1.8.11.custom.css">			
		<style>
		.hermesorder { font-family: sans-serif; font-size: 0.8em; }
		.hermesorder h1 { padding: 0; }
		.hermesorder a:link { font-size: inherit; text-decoration: underline; }
		dl.form { overflow: auto; width: 50%;}
		dl.form dt, dl.form dd { float: left; margin: 1px 0; }
		dl.form dt { clear: left; font-weight: bold; width: 15em; }
		dl.form dd { }
		fieldset { border: none; background: #dddddd; margin: 1em 0; }
		legend { background: #C7E8F8; padding: 1ex 1em; box-shadow: 0 0 2px #000000; }
		.availability { float: right; width: 15em; border: 1px solid #555; background: #eee; padding: 1ex 1em; }
		.corders { width: 99%; margin: auto; }
		.corders th { background: #ccc; text-align: center; }
		.corders td { background: #f8f8f; }
		.corders tr:nth-child(even) td { background: #f0f0f0 }
		p.message { background: #ffa; border: 1px solid #faa; padding: 1ex 1em; }
		.cb { clear: both; }
		.overlay { position: absolute; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); color: #fff; text-align: center; padding-top: 15em; font-family: sans-serif; }
		div.info { width: 50%; float: right; }
		p.note_props_only { width: 80%; margin: auto; border: 1px solid #000; background: #EEEEEE; padding: 1em; font-size: 1.1em; }
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

				<td class="boxCenter hermesorder" width="100%" valign="top" data-gx-compatibility="dynamic_page_breakpoints" data-dynamic_page_breakpoints-large=".boxCenterWrapper">
				<!-- body_text //-->
					<div class="availability">
						##checking_availability
					</div>

					<h2>##collection_appointments</h2>

					<?php if($hermes->getService() == 'PriPS'): ?>
						<p class="note_props_only">##note_props_only</p>
					<?php else: # ProPS ?>
						<?php foreach($session_messages as $msg): ?>
							<p class="message"><?php echo $msg ?></p>
						<?php endforeach ?>
						
						<form action="" method="post">
							<fieldset>
								<legend>##new_appointment</legend>
								<div class="info">
									##constraints_info
								</div>
								<dl class="form">
									<dt><label for="date">##collection_date</label></dt>
									<dd>
										<input type="text" name="date" id="date">
									</dd>
									<?php foreach($hermes->getPackageClasses() as $pckey => $pclass): ?>
										<dt><label for="packets_<?php echo $pckey ?>">##number_of_parcels_in_class <?php echo $pclass['name'] ?></label></dt>
										<dd>
											<input type="text" name="packets[<?php echo $pckey ?>]" id="packets_<?php echo $pckey ?>" value="0">
										</dd>
									<?php endforeach ?>
								</dl>
								<input type="submit" class="button" value="##send_appointment">
							</fieldset>
						</form>
						
						<div id="collorders">
							##loading_list
						</div>
					<?php endif ?>
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
				
				$('.availability').load('hermes_order.php', { 'ajax': 'checkavailability' }, function() {
					if($('span.available').length > 0) {
						$('#collorders').load('hermes_collection.php', { 'load_collorders': 1 });
					}
					else {
						$('#collorders').html('##not_available');
					}
				});
				
				$('#date').datepicker({
					dateFormat: 'yy-mm-dd',
					minDate: +1,
					maxDate: +90
				});
			});
		</script>
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/ui/datepicker/jquery-ui-datepicker.js"></script>
	</body>
</html>
<?php 
echo $hermes->replaceTextPlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');