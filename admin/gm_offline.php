<?php
/* --------------------------------------------------------------
   gm_offline.php 2015-10-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------*/

require('includes/application_top.php');

// Language Switcher
// =================
$coo_language_switcher = MainFactory::create_object('LanguageSwitcher');
$languagesArray = $coo_language_switcher->getLanguages();

// Topbar
// ======

/* @var TopbarNotificationReader $coo_topbar_notification_reader */
$coo_topbar_notification_reader = MainFactory::create_object('TopbarNotificationReader');

/* @var TopbarNotification $coo_topbar_notification */
$coo_topbar_notification = $coo_topbar_notification_reader->getTopbarNotification();

// Popup
// =====

/* @var PopupNotificationReader $coo_popup_notification_reader */
$coo_popup_notification_reader = MainFactory::create_object('PopupNotificationReader');

/* @var PopupNotification $coo_popup_notification */
$coo_popup_notification = $coo_popup_notification_reader->getPopupNotification();

/**
 * Language Text Manager
 *
 * Gets the text for the on/off states
 */
$languageTextManager = MainFactory::create_object('LanguageTextManager', array('shop_offline', $_SESSION['languages_id']), true);

/**
 * Globals
 *
 * This array with the associative keys ['jsEngineLanguage']['shop_offline']
 * holds the text for the on/off states, depending on which language is activated
 */
$GLOBALS['jsEngineLanguage']['shop_offline'] = $languageTextManager->get_section_array('shop_offline');


if(isset($_POST) && !empty($_POST))
{
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		// Reverse the value of the $_POST['shop_offline'] parameter because since v2.5.1.0 the UI checkbox stands
		// for the "current shop state" while before it used to stand for the "enable shop offline mode". This is
		// a quick fix because it is not possible to change the GM_SHOP_OFFLINE config name due to dependencies in
		// other sections of the app.
		$_POST['shop_offline'] = ($_POST['shop_offline'] == 'checked') ? '' : 'checked';
		xtc_db_query("UPDATE gm_configuration SET gm_value = '" . xtc_db_input(xtc_db_prepare_input($_POST['shop_offline'])). "' WHERE gm_key = 'GM_SHOP_OFFLINE'");
		xtc_db_query("UPDATE gm_configuration SET gm_value = '" . xtc_db_input(xtc_db_prepare_input($_POST['offline_content'])) . "' WHERE gm_key = 'GM_SHOP_OFFLINE_MSG'");


		$coo_topbar_notification->setActive(isset($_POST['topbar_enabled']));
		$coo_topbar_notification->setColor(xtc_db_prepare_input($_POST['colorValueTopBar']));
		$topbar_mode = (isset($_POST['topbar_mode'])) ? $_POST['topbar_mode'][0] : 'hideable';
		$coo_topbar_notification->setMode($topbar_mode);
		foreach($languagesArray as $kLanguage => $vLanguage)
		{
			$coo_topbar_notification->setContentByLanguageId(xtc_db_prepare_input($_POST['topbar_msg_plain'][0][$vLanguage['languages_id']]), $vLanguage['languages_id']);

			/* @var TopbarNotificationWriter $coo_topbar_notification_writer */
			$coo_topbar_notification_writer = MainFactory::create_object('TopbarNotificationWriter');
			$coo_topbar_notification_writer->save($coo_topbar_notification);
		}

		$coo_popup_notification->setActive(isset($_POST['popup_enabled']));
		foreach($languagesArray as $kLanguage => $vLanguage)
		{
			$coo_popup_notification->setContentByLanguageId(xtc_db_prepare_input($_POST['popup_msg_plain'][0][$vLanguage['languages_id']]), $vLanguage['languages_id']);

			/* @var PopupNotificationWriter $coo_popup_notification_writer */
			$coo_popup_notification_writer = MainFactory::create_object('PopupNotificationWriter');
			$coo_popup_notification_writer->save($coo_popup_notification);
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

		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/global-colorpicker.css" />
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/jobs.css" />
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/lightbox.css" />
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/tooltip_plugin.css">
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/buttons.css" />

		<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>



	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();" class="page_gm_offline">
	<!-- header //-->
	<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
	<!-- header_eof //-->

	<script type="text/javascript" src="html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>
	<script type="text/javascript" src="html/assets/javascript/legacy/gm/tooltip_plugin.js"></script>

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
			<td id="shop-offline" class="boxCenter" width="100%" valign="top">
				<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)">
					<?php echo HEADING_TITLE; ?>
				</div>

				<!-- Tabs -->
				<?php
					/* @var NotificationsContentView $notificationsContentView */
					$notificationsContentView = MainFactory::create_object('NotificationsContentView');
					$notificationsContentView->setPageToken($_SESSION['coo_page_token']->generate_token());
					$notificationsContentView->setLanguageCode($_SESSION['language_code']);
					$notificationsContentView->setLanguagesArray($languagesArray);

					$topbarContent = $coo_topbar_notification->getContentArray();
					$notificationsContentView->setContentDataTopbar($topbarContent[$_SESSION['languages_id']]);
					$notificationsContentView->setContentDataTopbarArray($coo_topbar_notification->getContentArray());

					$popupContent = $coo_popup_notification->getContentArray();
					$notificationsContentView->setContentDataPopup($popupContent[$_SESSION['languages_id']]);
					$notificationsContentView->setContentDataPopupArray($coo_popup_notification->getContentArray());
					echo $notificationsContentView->get_html();
				?>

				<!-- Jobs list -->
				<div>
					<span class="key-title">Timer</span>
				</div>
				<div>
					<?php
						/* @var ShopNoticeJobContentView $jobContentView */
						$jobContentView = MainFactory::create_object('ShopNoticeJobContentView');
						echo $jobContentView->get_html();
					?>
				</div>
			</td>
			<!-- body_text_eof //-->
		</tr>
	</table>
	<!-- body_eof //-->

	<!-- footer //-->
	<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	<!-- footer_eof //-->
	</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
