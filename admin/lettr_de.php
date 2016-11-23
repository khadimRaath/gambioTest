<?php
/*	--------------------------------------------------------------
	lettr_de.php 2015-09-28 gm
	Digineo GmbH
	http://www.digineo.de
	Copyright (c) 2015 Digineo GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	Author: Ronny Paschen
	Version 2.0

    IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
    MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
    NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------*/

require('includes/application_top.php');

AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');

if (empty($_GET['lang_id'])) {
	$lang_id = $_SESSION['languages_id'];
} else {
	$lang_id = $_GET['lang_id'];
}


if ($_GET['go'] == 'verify_apikey') {
	if (isset($_GET['key'])) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://lettr.de/setting');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'X-Lettr-API-Key: '. $_GET['key']));
		$data = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		echo '{ "status": "'. $code . '", "err": "' . $err . '", "domain": "' . HTTP_CATALOG_SERVER . '", "response": ' . (isset($data) ? $data : '{}') . '}';
	}
	exit;
}

if (isset($_POST['save'])) {
	// Konfigurationseinträge speichern
	$apikey = gm_get_conf('LETTR_API_KEY');
	gm_set_conf('LETTR_API_KEY', $_POST['apikey']);
	gm_set_conf('LETTR_ALLVIALETTR', $_POST['allvialettr']);
	gm_set_conf('LETTR_ATTACHVIALETTR', $_POST['attachvialettr']);
	gm_set_conf('LETTR_NEWSEXPORT', $_POST['newsexport']);
	gm_set_conf('LETTR_EXPORTCUSTOMER', $_POST['exportcust']);
	// Einstellungen an Lettr.de übermitteln, sofern sich der API-Key geändert hat
	if (($_POST['validapikey'] == '1') && ($apikey != $_POST['apikey'])) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://lettr.de/setting');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json', 'X-Lettr-API-Key: ' . $_POST['apikey']));
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('setting[import_url]' => $_POST['importurl']));
		$data = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($code == 200) {
			// alles gut...
		}
	}

}


?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html <?php echo HTML_PARAMS; ?>>
		<head>
			<meta http-equiv="x-ua-compatible" content="IE=edge">
			<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
			<title><?php echo TITLE; ?></title>
			<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
			<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
			<script type="text/javascript">
				//
			</script>
		</head>
		<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
			<!-- header //-->
			<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
			<!-- header_eof //-->

			<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/modules/lettr_de.js"></script>

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

						<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
						<br />

						<?php echo xtc_draw_form('lettr_de', FILENAME_LETTR_DE, 'page='.$_GET['page'], 'post', 'enctype="multipart/form-data"'); ?>
						<input type="hidden" name="validapikey" value="0">
						<input type="hidden" name="importurl" value="">

						<div style="float:none;width:95%;">

							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td width="150" valign="middle" class="dataTableHeadingContent">
										<?php echo MENU_TITLE_LETTRMAILEXCHANGE; ?>
									</td>
								</tr>
							</table>

							<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
								<tr>
									<td valign="top" class="main">
										<div id="gm_box_content">
											<?php echo TEXT_LETTR_NEWCUSTOMER; ?><br><hr>
											<?php echo TEXT_LETTR_APIKEY; ?><br><br>
											<?php
											echo LETTR_APIKEY . ' <input id="val_apikey" type="text" name="apikey" value="' . gm_get_conf('LETTR_API_KEY') . '" size="20" /> &nbsp; <span id="out_apikey"></span><br>';
											?>
											<br>
											<hr>
											<?php echo TEXT_LETTR_MAILVIALETTR ?><br><br>
											<?php
											$val = (gm_get_conf('LETTR_ALLVIALETTR') == "true");
											echo xtc_draw_radio_field('allvialettr', 'false', (($val) ? false : true)) . ALLVIALETTR_NO . '<br>';
											echo xtc_draw_radio_field('allvialettr', 'true', (($val) ? true : false)) . ALLVIALETTR_YES . '<br>';
											?>
											<br>
											<hr>
											<?php echo TEXT_LETTR_MAILWITHATTACH ?><br><br>
											<?php
											$val = (gm_get_conf('LETTR_ATTACHVIALETTR') == 'true');
											echo xtc_draw_radio_field('attachvialettr', 'false', (($val) ? false : true)) . ATTACHVIALETTR_NO . '<br>';
											echo xtc_draw_radio_field('attachvialettr', 'true', (($val) ? true : false)) . ATTACHVIALETTR_YES . '<br>';
											?>
										</div>
									</td>
								</tr>
							</table>

							<table border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td width="150" valign="middle" class="dataTableHeadingContent">
										<?php echo MENU_TITLE_NEWSLETTER_EXPORT; ?>
									</td>
								</tr>
							</table>

							<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
								<tr>
									<td valign="top" class="main">
										<div id="gm_box_content">
											<?php echo TEXT_NEWSLETTER_EXPORT; ?><br><br>
											<?php
											$val = (gm_get_conf('LETTR_NEWSEXPORT') == "true");
											echo xtc_draw_radio_field('newsexport', 'false', (($val) ? false : true)) . NEWSLETTER_EXPORT_NO . '<br>';
											echo xtc_draw_radio_field('newsexport', 'true', (($val) ? true : false)) . NEWSLETTER_EXPORT_YES . '<br>';
											?>
											<br>
											<hr>
											<?php echo TEXT_NEWSLETTER_EXPORTCUSTOMER ?><br><br>
											<?php
											$val = (gm_get_conf('LETTR_EXPORTCUSTOMER') == "true");
											echo xtc_draw_radio_field('exportcust', 'false', (($val) ? false : true)) . NEWSLETTER_EXPORTALL_NO . '<br>';
											echo xtc_draw_radio_field('exportcust', 'true', (($val) ? true : false)) . NEWSLETTER_EXPORTALL_YES . '<br>';
											?>
										</div>
									</td>
								</tr>
								<tr>
									<td valign="top" class="main">
										<br>
										<input class="button" onclick="this.blur();" value="<?php echo TEXT_BTN_SAVE; ?>" name="save" type="submit">
									</td>
								</tr>
							</table>
						</div>
						<!-- body_eof //-->
					</td>
				</tr>
			</table>
			<!-- footer //-->
			<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			<!-- footer_eof //-->
			<br />
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>