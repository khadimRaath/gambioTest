<?php
/* --------------------------------------------------------------
   payone_logs.php 2015-09-28 gm
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

function getPageURL($params) {
	$default_params = array(
		'mode' => isset($_GET['mode']) ? $_GET['mode'] : 'api',
		'page' => isset($_GET['page']) ? (int)$_GET['page'] : 1,
		'start_date' => isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('30 days ago')),
		'end_date' => isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'),
	);
	$params = array_merge($default_params, $params);
	$params_string = http_build_query($params);
	$url = PAGE_URL.'?'.$params_string;
	return $url;
}

if(!isset($_GET['page'])) {
	xtc_redirect(getPageURL(array()));
}

$payone = new GMPayOne();

$messages_ns = 'messages_'.basename(__FILE__);
if(!isset($_SESSION[$messages_ns])) {
	$_SESSION[$messages_ns] = array();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	// do something
	$_SESSION[$messages_ns][] = 'done';

	xtc_redirect(PAGE_URL);
}

$messages = $_SESSION[$messages_ns];
$_SESSION[$messages_ns] = array();

$mode = (isset($_GET['mode']) && in_array($_GET['mode'], array('api', 'transactions'))) ? $_GET['mode'] : 'api';
$entries_per_page = 100;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_date = date('Y-m-d', strtotime($_GET['start_date']));
$end_date = date('Y-m-d', strtotime($_GET['end_date']));
$total_logs = $payone->getLogsCount($mode, $start_date, $end_date);
$total_pages = max(1, ceil($total_logs / $entries_per_page));
$limit = $entries_per_page;
$offset = ($page - 1) * $entries_per_page;
$logs = $payone->getLogs($mode, $limit, $offset, $start_date, $end_date);

if(isset($_GET['event_id'])) {
	$event_data = $payone->getLogData($mode, (int)$_GET['event_id']);
}

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
			p.message {
				margin: .5ex auto;
				background: rgb(240, 230, 140);
				border: 1px solid rgb(255, 0, 0);
				padding: 1em;
			}

			dl.adminform {
				position: relative;
				overflow: auto;
			}

			dl.adminform dd, dl.adminform dt {
				float: left;
			}

			dl.adminform dt {
				clear: left;
				width: 15em;
			}

			input[type="submit"].btn_wide {
				width: auto;
			}

			#start_date, #end_date { width: 8em; }

			#logsform { display: block; width: 60%; margin: auto; }
			p.nologs { width: 60%; margin: 1.5em auto; }
			table.payone_logs {
				width: 60%;
				margin: 1.5em auto;
				border-collapse: collapse;
				background: #eee;
			}
			table.payone_logs th {
				background: #ddd;
			}
			table.payone_logs th, table.payone_logs td { padding: .2em .3em; }

			div.event {
				overflow: auto;
				background: #eee;
				width: 90%;
				margin: 0 auto 2em;
			}

			div.event_id {
				background: #ddd;
				font-size: 1.3em;
				padding: .4em .5em;
			}

			table.event_log {
				width: calc(50% - 2em);
				float: left;
				margin: 1em;
				background: #ddd;
			}

			table.event_log td.label { width: 40%; }
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
										<td class="pageHeading" style="padding-left: 0px"><span class="add-margin-left-24">##payone_logs_title</span></td>
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

								<?php if(isset($_GET['event_id']) && !empty($event_data)): ?>
								<div class="event">
									<div class="event_id">
										##event_id <?php echo (int)$_GET['event_id'] ?>
										<?php if($mode !== 'api'): ?>
											##txid <?php echo $event_log['message'][0]['txid'] ?>
										<?php endif ?>
									</div>
									<?php foreach($event_data as $event_log): ?>
										<table class="event_log">
											<tr>
												<td class="label">##event_log_count</td>
												<td class="value">
													<?php echo $event_log['log_count'] ?>
													<?php if($event_log['log_count'] == 1) echo '(##request)'; ?>
													<?php if($event_log['log_count'] == 2) echo '(##response)'; ?>
													<?php if($event_log['log_count'] > 2) echo '(##additional_event)'; ?>
												</td>
											</tr>
											<tr>
												<td class="label">##datetime</td>
												<td class="value">
													<?php echo $event_log['date_created'] ?>
												</td>
											</tr>
											<?php foreach($event_log['message'] as $name => $value): ?>
												<tr>
													<td class="label"><?php echo $name ?></td>
													<td class="value"><?php echo $value ?></td>
												</tr>
											<?php endforeach ?>
										</table>
									<?php endforeach ?>
								</div>
								<?php endif ?>

								<form action="<?php echo PAGE_URL ?>" method="GET" id="logsform">
									<select name="mode">
										<option value="api" <?php echo $_GET['mode'] == 'api' ? 'selected="selected"' : '' ?>>##api</option>
										<option value="transactions" <?php echo $_GET['mode'] == 'transactions' ? 'selected="selected"' : '' ?>>##transactions</option>
									</select>
									<label for="start_date">##start_date</label>
									<input id="start_date" name="start_date" type="text" value="<?php echo htmlspecialchars($_GET['start_date']); ?>">
									<label for="end_date">##end_date</label>
									<input id="end_date" name="end_date" type="text" value="<?php echo htmlspecialchars($_GET['end_date']); ?>">
									<label for="pageselect">##page</label>
									<select name="page" id="pageselect">
										<?php for($pageno = 1; $pageno <= $total_pages; $pageno++): ?>
											<option value="<?php echo $pageno?>"><?php echo $pageno ?></option>
										<?php endfor ?>
									</select>
									<input type="submit" value="##show">
								</form>

								<?php if(empty($logs)): ?>
									<p class="nologs">##no_logs</p>
								<?php else: ?>
									<table class="payone_logs">
										<tr>
											<?php if($mode == 'api'): ?>
												<th>##request</th>
												<th>##status</th>
											<?php else: ?>
												<th>##txid_seqno</th>
												<th>##clearingtype</th>
												<th>##txaction</th>
											<?php endif ?>
											<th>##datetime</th>
											<th>##customer</th>
											<th>&nbsp;<!-- Details --></th>
										</tr>
										<?php foreach($logs as $log): ?>
											<?php
											$customers_name = !empty($log['customers_lastname']) ? $log['customers_lastname'].", ".$log['customers_firstname'] : '';
											$logData = $payone->getLogData($mode, (int)$log['event_id']);
											?>
											<tr>
												<?php if($mode == 'api'): ?>
													<td><?php echo $logData[0]['message']['request'] ?></td>
													<td><?php echo $logData[1]['message']['status'] ?></td>
												<?php else: ?>
													<td><?php echo $logData[0]['message']['txid'].' ('.$logData[0]['message']['sequencenumber'].')' ?></td>
													<td><?php echo $logData[0]['message']['clearingtype'] ?></td>
													<td><?php echo $logData[0]['message']['txaction'] ?></td>
												<?php endif ?>
												<td><?php echo $log['date_created'] ?></td>
												<td><?php echo $customers_name ?>
												<!--
												<?php print_r($logData) ?>
												-->
												</td>
												<td>
													<a href="<?php echo getPageURL(array('event_id' => $log['event_id'])) ?>">Details</a>
												</td>
											</tr>
										<?php endforeach ?>
									</table>
								<?php endif?>
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
echo $payone->replaceTextPlaceholders(ob_get_clean());

require DIR_WS_INCLUDES . 'application_bottom.php';