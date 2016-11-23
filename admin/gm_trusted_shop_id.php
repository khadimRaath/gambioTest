<?php
/* --------------------------------------------------------------
  gm_trusted_shop_id.php 2016-04-15
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

AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');

if(isset($_GET['reviewcollector']))
{
	$filename = 'tsrc_'.xtc_cleanName(STORE_NAME).'_'.date('YmdHis').'.csv';
	header('Content-Type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename='.$filename);
	$_SESSION['coo_page_token']->is_valid($_GET['page_token']) or die('CSRF protection triggered');
	(isset($_GET['timeframe']) && isset($_GET['orders_status'])) or die('parameters missing');

	$num_days = (int)$_GET['timeframe'];
	$orders_status = (int)$_GET['orders_status'];

	$query =
		'SELECT
			orders_id, date_purchased, customers_email_address, customers_firstname, customers_lastname
		FROM
			orders
		WHERE
			date_purchased BETWEEN NOW() - INTERVAL :num_days DAY AND NOW() AND
			orders_status = \':orders_status\'';
	$query = strtr($query, array(':num_days' => $num_days, ':orders_status' => $orders_status));
	$result = xtc_db_query($query);
	while($row = xtc_db_fetch_array($result))
	{
		echo $row['customers_email_address'].','.$row['orders_id'].','.$row['customers_firstname'].','.$row['customers_lastname']."\n";
	}

	xtc_db_close();
	exit;
}

$languages = MainFactory::create_object('language');
$service = MainFactory::create_object('GMTSService');
$certs = $service->retrieveCertificates();
$selected_cert = false;

foreach($certs as $cert)
{
	unset($languages->catalog_languages[$cert['language']]);
}

if(!empty($certs))
{
	xtc_db_query(
		'UPDATE
			content_manager
		SET
			content_status = "1"
		WHERE
			content_group = "15"
	');

	foreach($certs as $cert)
	{
		if(!empty($_GET['ts_id']) && $cert['tsid'] == $_GET['ts_id'])
		{
			$selected_cert = $cert['tsid'];
		}
	}
}
else
{
	xtc_db_query(
		'UPDATE
			content_manager
		SET
			content_status = "0"
		WHERE
			content_group = "15"
	');
}

if(isset($_GET['delete_aib']))
{
	$service->deleteAdminInfoboxMessage($_GET['delete_aib']);
}

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if(!$_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		// this will never be reached b/c is_valid() will abort execution
		die('CSRF protection triggered');
	}

	if(isset($_POST['new_id']))
	{
		if(isset($_POST['use_for_excellence']))
		{
			$login_check = $service->checkLogin($_POST['new_id'], $_POST['excellence_user'], $_POST['excellence_password']);
			if($login_check >= 0)
			{
				$certCheckResult = $service->checkCertificate($_POST['new_id']);
				if($certCheckResult !== false)
				{
					if(in_array($certCheckResult->certificationLanguage, array_keys($languages->catalog_languages)))
					{
						$service->storeTSID($_POST['new_id'], $certCheckResult->certificationLanguage, true, $_POST['excellence_user'], $_POST['excellence_password']);
					}
					else
					{
						$_SESSION['ts_flash'] = $service->get_text('double_tsid_for_language');
					}
				}
			}
			else
			{
				$_SESSION['ts_flash'] = $service->get_text('TS_LOGIN_INVALID');
			}
		}
		else
		{
			$service->storeTSID($_POST['new_id'], $_POST['language']);
		}
	}

	if(isset($_POST['del_id'])) {
		$service->deleteCertificate($_POST['del_id']);
		$service->setBadgeSnippet($_POST['del_id'], false, '');
		$_SESSION['ts_flash'] = $service->get_text('ts_id_deleted');
	}

	if(isset($_POST['cred_id'])) {
		$service->changeCredentials($_POST['cred_id'], $_POST['user'], $_POST['password']);
		$_SESSION['ts_flash'] = $service->get_text('credentials_saved');
	}

	if(isset($_POST['check_all_certs'])) {
		$service->checkAllCertificates();
		$service->reloadProtectionItems();
		$_SESSION['ts_flash'] = $service->get_text('certificates_checked');
	}

	if(isset($_POST['application_number'])) {
		$state = $service->getRequestState($_POST['application_number']);
		if($state === false) {
			$_SESSION['ts_flash'] = $service->get_text('error_retrieving_state');
		}
		else if($state < 0) {
			$_SESSION['ts_flash'] = $service->errorText($state);
		}
		else {
			$_SESSION['ts_flash'] = $service->get_text('state_retrieved');
		}
	}

	if(isset($_POST['save_rating_cfg']))
	{
		$service->rating_enabled = isset($_POST['activate_rating']) ? '1' : '0';
		$service->rating_cosuccess = isset($_POST['rating_cosuccess']) ? '1' : '0';
		$service->rating_email = isset($_POST['rating_email']) ? '1' : '0';
	}

	if(isset($_POST['save_tsid_cfg']))
	{
		// Trust Badge
		$badge_enabled = isset($_POST['badge_enabled']) ? '1' : '0';
		$service->setBadgeSnippet($_POST['tsid'], $badge_enabled, $_POST['badge_snippet']);
		$redirect_url = xtc_href_link(basename(__FILE__), 'ts_id='.$_POST['tsid']);

		// Review Sticker
		$review_sticker_enabled = isset($_POST['review_sticker_enabled']) ? '1' : '0';
		$service->setReviewStickerSnippet($_POST['tsid'], $review_sticker_enabled, $_POST['review_sticker_snippet']);
		$redirect_url = xtc_href_link(basename(__FILE__), 'ts_id='.$_POST['tsid']);
	}

	if(isset($_POST['seal_cfg']))
	{
		$service->seal_enabled                    = isset($_POST['enable_seal'])                    ? '1' : '0';
		$service->richsnippets_enabled            = isset($_POST['enable_richsnippets'])            ? '1' : '0';
		$service->richsnippets_enabled_categories = isset($_POST['enable_richsnippets_categories']) ? '1' : '0';
		$service->richsnippets_enabled_products   = isset($_POST['enable_richsnippets_products'])   ? '1' : '0';
		$service->richsnippets_enabled_other      = isset($_POST['enable_richsnippets_other'])      ? '1' : '0';
		$service->productreviews_enabled          = isset($_POST['enable_productreviews'])          ? '1' : '0';
		$service->productreviews_summary_enabled  = isset($_POST['enable_productreviews_summary'])  ? '1' : '0';
	}

	if(empty($redirect_url))
	{
		$redirect_url = xtc_href_link(basename(__FILE__));
	}
	xtc_redirect($redirect_url);
}

$app_query = xtc_db_query(
	'SELECT
		tsp.*, orders.customers_name, orders.date_purchased
	FROM
		ts_protection tsp,
		orders
	WHERE
		tsp.orders_id = orders.orders_id AND
		orders.date_purchased > DATE_SUB(NOW(), INTERVAL 31 DAY)
	ORDER BY
		orders.date_purchased DESC
	');
$applications = array();
while($app_row = xtc_db_fetch_array($app_query)) {
	$applications[] = $app_row;
}

$seal_enabled         = $service->seal_enabled;
$richsnippets_enabled = $service->richsnippets_enabled;
$rating_enabled       = $service->rating_enabled;
$rating_cosuccess     = $service->rating_cosuccess;
$rating_email         = $service->rating_email;
$badge_enabled        = $service->badge_enabled;
$badge_yoffset        = $service->badge_yoffset;
$badge_variant        = $service->badge_variant;

if(isset($_SESSION['ts_flash'])) {
	$flash = $_SESSION['ts_flash'];
	unset($_SESSION['ts_flash']);
}
else {
	$flash = '';
}

$cronjob_url = HTTP_SERVER.DIR_WS_CATALOG.'trusted_shops_cron.php?key='.$service->getKey();

ob_start();
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>html/assets/styles/legacy/stylesheet.css">
		<style type="text/css">
			input[type="checkbox"] { vertical-align: middle; }
			.padded { padding: 1ex 1em; }
			form.padded { display: block; }
			.warning { color: #CC0000; font-weight: bold; }
			.tsmain { background: #f7f7f7; border: 1px solid #CCCCCC; font: 0.8em sans-serif; padding: 1ex; }
			.tsinfo .showhidecontent {margin-bottom: 1em; overflow: auto; }
			.showhide {cursor: pointer; }
			.subHeading {background-color: #585858; padding: .3ex 0.5ex; margin: 1em 0 .5ex 0; color: #ffffff; font-size: 1.1em; font-weight: bold; }
			#message {color: red; font-weight: bold; }
			p.flash {border: 1px solid #CC0000; background: #fffacd; padding: 1ex 1em; font-weight: bold; }
			input.invalid {border-color: #CC0000; }
			.add_ts_id {background: #dddddd; margin: auto; padding: 1ex 1em; width: 80%; line-height: 1.8; }
			.add_ts_id label { display: inline-block; width: 25%; }
			table.tsids, table.applications {margin: 1em auto; width: 90%; }
			table.tsids thead, table.applications thead {background: #888888; color: #ffffff; }
			table.tsids th, table.tsids td, table.applications th, table.applications td {padding: .3ex .5ex; }
			table.tsids tbody, table.applications tbody {background: #dddddd; }
			.creds label {display: inline-block; width: 8em; margin-left: 2em; }
			.tsbadge label, .reviewsticker label { display: inline-block; width: 15em; vertical-align: top; }
			#badge_snippet, #review_sticker_snippet { width: 700px; min-height: 10em; }
			tr.selected_cert td { background: #FFFCB2; font-weight: bold; }
			.tsidcfg { margin-left: 3em; }
			.rcexport form { line-height: 1.8; }
			.rcexport label { display: inline-block; width: 10em; }
			.rcexport input[type="submit"].button { display: inline-block; width: auto; }
			div.instructions { margin: 2em auto; }
			div.instructions a { font-size: 0.9em; font-weight: bold; }
			div.instructions li { margin: 1ex 0; }
		</style>
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/LoadUrl.js"></script>
	</head>
	<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF">

		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
				</td>
				<td class="boxCenter" width="100%" valign="top">
					<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)">##HEADING_TITLE</div>

					<div class="tsmain">
						<div class="tsinfo">
							<!-- <div class="showhide">##SHOWHIDEINFO</div> -->
							<div class="subHeading showhide">
								##HEADING_INFO
							</div>
							<div class="showhidecontent">
								<!-- Trusted Shops Info -->
								<?php
								$info_image_file = sprintf('images/trusted-info-%s.png', strtolower($_SESSION['language_code']));
								if(!file_exists(DIR_FS_CATALOG.$info_image_file))
								{
									$info_image_file = 'images/trusted-info-de.png';
								}
								?>
								<a style="float: right; margin-left: 3em;" href="http://www.trustedshops.de/shopbetreiber/mitgliedschaft_partner.html?shopsw=GAMBIO&amp;et_cid=14&amp;et_lid=58032" target="_blank">
									<img src="<?php echo xtc_catalog_href_link($info_image_file) ?>">
								</a>
								<div class="instructions">
									<ol>
										<li>Geben Sie Ihre Trusted Shops ID ein, wählen Sie die Ausgabesprache und speichern Sie Ihre Änderungen über „TSID hinzufügen“.</li>
										<li>Klicken Sie auf „bearbeiten“, folgen Sie dem Link ins Trusted Shops Integration Center und konfigurieren Sie dort Ihren individuellen
										Trustbadge Code. Kopieren Sie diesen, kehren Sie ins Gambio Backend zurück und fügen Sie das Snippet in die Trustbadge Code Box ein.
										Aktivieren Sie das Trustbadge über die Checkbox und speichern Sie ihre Einstellungen.</li>
										<li>Anschließend können Sie weitere Features wie den Review Sticker oder die Rich Snippets aktivieren.
										Wir empfehlen, direkt nach Einbindung des Trustbadges den Review Collector zu nutzen, um Bestandskunden um eine Bewertung zu bitten.
										Exportieren Sie einfach mit dem Tool unten etliche Bestellungen der letzten Monate und laden Sie die exportierte Datei in  unserem
										<a href="https://www.trustedshops.com/tsb2b/sa/ratings/batchRatingRequest.seam?prefLang=de" target="_blank">Review Collector</a> hoch.
										Damit sammeln Sie in kürzester Zeit zahlreiche Bewertungen.</li>
									</ol>
								<!-- END Trusted Shops Info -->
							</div>
						</div>
						<div class="tscfg">
							<div class="subHeading">##HEADING_CONFIGURATION</div>
							<?php if(!empty($flash)): ?>
							<p class="flash">
								<?php echo $flash ?>
							</p>
							<?php endif ?>
							<?php if(!empty($languages->catalog_languages)): ?>
								<div class="add_ts_id">
									<form action="<?php echo xtc_href_link(basename(__FILE__)) ?>" method="post" class="add_tsid">
										<?php  echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
										<label for="new_id">##ADD_TS_ID</label>
										<input type="text" name="new_id" id="new_id" size="40" maxlength="33">
										<br>
										<label for="new_id_language">##language</label>
										<select name="language" id="new_id_language">
											<?php foreach($languages->catalog_languages as $lang_code => $lang_data): ?>
												<option value="<?php echo $lang_code ?>"><?php echo $lang_data['name'] ?></option>
											<?php endforeach ?>
										</select>
										<br>
										<label for="use_for_excellence">##use_for_excellence</label>
										<input type="checkbox" id="use_for_excellence" name="use_for_excellence" value="1"><label for="use_for_excellence">##yes_use_for_excellence</label>
										<br>
										<label for="excellence_user">##username_for_excellence</label>
										<input type="text" name="excellence_user" id="excellence_user">
										<span class="note">##excellence_only</span>
										<br>
										<label for="excellence_password">##password_for_excellence</label>
										<input type="text" name="excellence_password" id="excellence_password">
										<span class="note">##excellence_only</span>
										<br>
										<label>&nbsp;</label>
										<input type="submit" value="##ADD_TSID" id="add_id">
										<span id="message"></span>
									</form>
								</div>
							<?php endif ?>

							<?php if(empty($certs)): ?>
							<p><em>##NO_TS_YET</em></p>
							<?php else: ?>
							<table class="tsids">
								<thead>
									<tr>
										<th>##TH_TS_ID</th>
										<th>##TH_TYPE</th>
										<!-- <th>##TH_URL</th> -->
										<th>##TH_LANGUAGE</th>
										<!-- <th>##TH_STATE</th>
										<th>##TH_FOR_RATING</th> -->
										<th>&nbsp;</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($certs as $cert): ?>
									<tr class="<?php echo $cert['tsid'] === $selected_cert ? 'selected_cert' : '' ?>">
										<td><?php echo $cert['tsid'] ?></td>
										<td><?php echo $cert['type'] ?></td>
										<!-- <td><?php echo $cert['url'] ?></td> -->
										<td><?php echo $cert['language'] ?></td>
										<!-- <td><?php echo $service->get_text('CERTSTATE_'.$cert['state']) ?></td>
										<td><?php echo $cert['rating_ok'] == true ? YES : NO ?></td> -->
										<td>
											<form action="" method="POST">
												<input type="hidden" name="del_id" value="<?php echo $cert['tsid'] ?>">
												<input type="submit" value="##TS_DELETE">
												<?php  echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
											</form>
											<form action="" method="GET">
												<input type="hidden" name="ts_id" value="<?php echo $cert['tsid'] ?>">
												<input type="submit" value="##edit_tsid">
											</form>
										</td>
									</tr>
									<?php if($cert['type'] == 'EXCELLENCE'): ?>
									<tr>
										<td colspan="4">
											<form action="" method="POST" class="creds">
												<input type="hidden" name="cred_id" value="<?php echo $cert['tsid'] ?>">
												<label for="user_<?php echo $cert['tsid'] ?>">##TS_USERNAME</label>
												<input type="text" name="user" value="<?php echo $cert['user'] ?>" id="user_<?php echo $cert['tsid'] ?>" size="25" maxlength="200">
												<?php if($cert['login_ok'] != true): ?>
												<span class="warning">##TS_LOGIN_INVALID</span>
												<?php endif ?>
												<br>
												<label for="password_<?php echo $cert['tsid'] ?>">##TS_PASSWORD</label>
												<input type="text" name="password" value="<?php echo $cert['password'] ?>" id="password_<?php echo $cert['tsid'] ?>" size="25" maxlength="200">
												<input type="submit" value="##TS_CHECKSAVE">
												<?php  echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
											</form>
										</td>
									</tr>
									<?php endif ?>
									<?php endforeach ?>
								</tbody>
							</table>

							<?php /*
							<form action="" method="post" class="padded">
								<input type="hidden" name="check_all_certs" value="1">
								<input type="submit" value="##TS_CHECKALLIDS">
								<?php  echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
							</form>

							<p>
								##TS_CRONJOB_URL<a href="<?php echo $cronjob_url ?>"><?php echo $cronjob_url ?></a>
							</p>
							*/ ?>
							<?php endif // empty($certs) ?>
						</div>

						<?php if($selected_cert !== false): ?>
							<?php
								$badge_snippet = $service->getBadgeSnippet($selected_cert);
								$review_sticker_snippet = $service->getReviewStickerSnippet($selected_cert);
							?>
							<form action="<?php echo xtc_href_link(basename(__FILE__), 'ts_id='.$selected_cert) ?>" method="post" class="">
								<input type="hidden" name="save_tsid_cfg" value="1">
								<input type="hidden" name="tsid" value="<?php echo $selected_cert ?>">
								<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
								<div class="subHeading">##ts_id_specific_settings_for <?php echo $selected_cert ?></div>
								<div class="tscfg tsidcfg tsbadge">
									<div class="subHeading">##TS_HEADING_TRUSTBADGE</div>
									<p class="integration_help">
										##trustbadge_integration_info
										<a target="_blank" href="https://www.trustedshops.com/integration/?shop_id=<?php echo $selected_cert ?>&amp;backend_language=<?php echo $_SESSION['language_code'] ?>&amp;shopsw=GAMBIO&amp;shopsw_version=Gambio%20GX2&amp;plugin_version=2.1.6.0&amp;context=trustbadge">
											##trustbadge_integration_info_click_here
										</a>
									</p>
									<label for="badge_snippet">##badge_snippet</label>
									<textarea name="badge_snippet" id="badge_snippet"><?php echo $badge_snippet['snippet_code'] ?></textarea>
									<br>
									<label for="activate_badge">##TS_ENABLE_BADGE</label>
									<input type="checkbox" id="activate_badge" name="badge_enabled" value="1" <?php echo $badge_snippet['enabled'] ? 'checked="checked"' : ''?>>
									<label for="activate_badge">##TS_YES_ACTIVATE</label>
									<br>
								</div>

								<div class="tscfg tsidcfg reviewsticker">
									<div class="subHeading">##TS_HEADING_REVIEWSTICKER</div>
									<label for="review_sticker_snippet">##review_sticker_snippet</label>
									<textarea name="review_sticker_snippet" id="review_sticker_snippet"><?php echo $review_sticker_snippet['snippet_code'] ?></textarea>
									<br>
									<label for="activate_review_sticker">##TS_ENABLE_review_sticker</label>
									<input type="checkbox" id="activate_review_sticker" name="review_sticker_enabled" value="1" <?php echo $review_sticker_snippet['enabled'] ? 'checked="checked"' : ''?>>
									<label for="activate_review_sticker">##TS_YES_ACTIVATE</label>
									<br>
									<input class="button" type="submit" value="##TS_SAVE">
								</div>
							</form>
						<?php endif?>

						<?php if(!empty($certs)): ?>
							<div class="tscfg tsintegration">
								<div class="subHeading">##TS_HEADING_INTEGRATION</div>
								<form action="" method="post" class="padded">
									<input type="hidden" name="seal_cfg" value="1">
									<input type="checkbox" name="enable_richsnippets_categories" value="1" id="enable_richsnippets_categories" <?php echo $service->richsnippets_enabled_categories ? 'checked="checked"' : '' ?>>
									<label for="enable_richsnippets_categories">##TS_ENABLE_RICH_SNIPPETS_CATEGORIES</label>
									<br>
									<input type="checkbox" name="enable_richsnippets_products" value="1" id="enable_richsnippets_products" <?php echo $service->richsnippets_enabled_products ? 'checked="checked"' : '' ?>>
									<label for="enable_richsnippets_products">##TS_ENABLE_RICH_SNIPPETS_PRODUCTS</label>
									<br>
									<input type="checkbox" name="enable_richsnippets_other" value="1" id="enable_richsnippets_other" <?php echo $service->richsnippets_enabled_other ? 'checked="checked"' : '' ?>>
									<label for="enable_richsnippets_other">##TS_ENABLE_RICH_SNIPPETS_OTHER</label>
									<br>
									<input type="checkbox" name="enable_productreviews" value="1" id="enable_productreviews" <?php echo $service->productreviews_enabled ? 'checked="checked"' : '' ?>>
									<label for="enable_productreviews">##enable_productreviews</label>
									<br>
									<input type="checkbox" name="enable_productreviews_summary" value="1" id="enable_productreviews_summary" <?php echo $service->productreviews_summary_enabled ? 'checked="checked"' : '' ?>>
									<label for="enable_productreviews_summary">##enable_productreviews_summary</label>

									<br><br>
									<input type="submit" value="##TS_SAVE" class="button">
									<?php  echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
								</form>
							</div>
						<?php endif ?>

						<?php if(!empty($applications)): ?>
						<div class="tscfg">
							<div class="subHeading">##TS_BP_APPLICATIONS</div>
							<table class="applications">
								<thead>
									<tr>
										<th>##TH_ORDER</th>
										<th>##TH_DATE</th>
										<th>##TH_CUSTOMER</th>
										<th>##TH_APPNO</th>
										<th>##TH_GUARANTEENO</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($applications as $app): ?>
									<tr>
										<td><?php echo $app['orders_id'] ?></td>
										<td><?php echo $app['date_purchased'] ?></td>
										<td><?php echo $app['customers_name'] ?></td>
										<td><?php echo $app['application_number'] ?></td>
										<td>
											<?php if($app['result'] == '' || $app['result'] == '0'): ?>
											<?php if($app['result'] == '0'): ?>
											##TS_PROCESSING
											<?php endif ?>
											<form action="" method="post">
												<input type="hidden" name="application_number" value="<?php echo $app['application_number'] ?>">
												<input type="submit" value="##TS_CHECKUPDATE">
												<?php  echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
											</form>
											<?php else: ?>
											<?php echo $app['result'] ?>
											<?php endif ?>
										</td>
									</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
						<?php endif; // !empty($applications) ?>

						<div class="subHeading">##export_for_review_collector</div>
						<div class="tscfg rcexport">
							<p>##review_collector_info</p>
							<form action="<?php echo xtc_href_link(basename(__FILE__)) ?>" method="GET">
								<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
								<input type="hidden" name="reviewcollector" value="csv">
								<label for="export_timeframe">##export_interval</label>
								<select name="timeframe" id="export_timeframe">
									<option value="30">30 ##days</option>
									<option value="60">60 ##days</option>
									<option value="90">90 ##days</option>
								</select>
								<br>
								<label for="export_orders_status">##export_orders_status</label>
								<select name="orders_status">
									<?php foreach(xtc_get_orders_status() as $orders_status): ?>
										<option value="<?php echo $orders_status['id'] ?>"><?php echo $orders_status['text'] ?></option>
									<?php endforeach?>
								</select>
								<br>
								<label>&nbsp;</label>
								<input type="submit" class="button" value="##download_rc_list">
							</form>
						</div>
					</div>

				</td>
			</tr>
		</table>
		<script>
			$(function() {
				var error_txt = {
					'TS_INVALID_ID': '##TS_INVALID_ID',
					'TS_DOUBLE_ID': '##TS_DOUBLE_ID',
					'TS_LOGIN_INVALID': '##TS_LOGIN_INVALID'
				};
				$(".showhide").on('click', function(e) {
					var content = $(".showhidecontent", $(this).parent());
					content.toggle('fast');
				});

				<?php if(!empty($certs)): ?>
				$(".tsinfo .showhidecontent").hide();
				<?php endif ?>

				$("form.pseudoform").submit(function(e) {
					return false;
				});

				/*
				$("#add_id").click(function(e) {
					var new_id = $("input[name=new_id]", $(this).closest("form")).val();
					var rpdata = {
						'new_id': new_id
					};
					$.ajax({
						type: "POST",
						url: "request_port.php?module=TrustedShopsAdmin&cmd=new_id",
						data: rpdata,
						dataType: 'json'
					}).done(function(result){
						if(result.error) {
							//$('#message').html(error_txt[result.error]);
							alert(error_txt[result.error]);
						}
						if(result.reload == true) {
							//location.reload();
							location = "<?php echo HTTP_SERVER.DIR_WS_ADMIN.'gm_trusted_shop_id.php?ts_id=' ?>" + new_id;
						}
					});
				});
				*/

				$("form.creds").submit(function(e) {
					var the_form = $(this);
					var user_input = $('input[name="user"]', this);
					var password_input = $('input[name="password"]', this);
					var tsid = $('input[name="cred_id"]', this).val();
					var user = user_input.val();
					var password = password_input.val();
					var rpdata = {
						'tsid': tsid,
						'user': user,
						'password': password
					};
					var valid = false;
					$.ajax({
						type: "POST",
						url: "request_port.php?module=TrustedShopsAdmin&cmd=check_login",
						data: rpdata,
						dataType: 'json',
						async: false
					}).done(function(result) {
						console.log(result);
						if(result.valid == true) {
							valid = true;
						}
						else {
							alert(error_txt['TS_LOGIN_INVALID']);
							user_input.addClass('invalid');
							password_input.addClass('invalid');
							valid = false;
						}
					});
					return valid;
				});
			});
		</script>
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	</body>
</html>
<?php
echo $service->replaceTextPlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');
