<?php
/* --------------------------------------------------------------
   tax_rates.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(tax_rates.php,v 1.28 2003/03/12); www.oscommerce.com
   (c) 2003	 nextcommerce (tax_rates.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: tax_rates.php 1123 2005-07-27 09:00:31Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

AdminMenuControl::connect_with_page('zones.php');

if ($_GET['action']) {
	switch ($_GET['action']) {
		case 'insert':
			$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
			$tax_zone_id = xtc_db_prepare_input($_POST['tax_zone_id']);
			$tax_class_id = xtc_db_prepare_input($_POST['tax_class_id']);
			$tax_rate = xtc_db_prepare_input($_POST['tax_rate']);
			$tax_description = xtc_db_prepare_input($_POST['tax_description']);
			$tax_priority = xtc_db_prepare_input($_POST['tax_priority']);
			$date_added = xtc_db_prepare_input($_POST['date_added']);

			xtc_db_query("insert into " . TABLE_TAX_RATES . " (tax_zone_id, tax_class_id, tax_rate, tax_description, tax_priority, date_added) values ('" . xtc_db_input($tax_zone_id) . "', '" . xtc_db_input($tax_class_id) . "', '" . xtc_db_input($tax_rate) . "', '" . xtc_db_input($tax_description) . "', '" . xtc_db_input($tax_priority) . "', now())");
			xtc_redirect(xtc_href_link(FILENAME_TAX_RATES));
			break;

		case 'save':
			$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
			$tax_rates_id = xtc_db_prepare_input($_GET['tID']);
			$tax_zone_id = xtc_db_prepare_input($_POST['tax_zone_id']);
			$tax_class_id = xtc_db_prepare_input($_POST['tax_class_id']);
			$tax_rate = xtc_db_prepare_input($_POST['tax_rate']);
			$tax_description = xtc_db_prepare_input($_POST['tax_description']);
			$tax_priority = xtc_db_prepare_input($_POST['tax_priority']);
			$last_modified = xtc_db_prepare_input($_POST['last_modified']);

			xtc_db_query("update " . TABLE_TAX_RATES . " set tax_rates_id = '" . xtc_db_input($tax_rates_id) . "', tax_zone_id = '" . xtc_db_input($tax_zone_id) . "', tax_class_id = '" . xtc_db_input($tax_class_id) . "', tax_rate = '" . xtc_db_input($tax_rate) . "', tax_description = '" . xtc_db_input($tax_description) . "', tax_priority = '" . xtc_db_input($tax_priority) . "', last_modified = now() where tax_rates_id = '" . xtc_db_input($tax_rates_id) . "'");
			xtc_redirect(xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $tax_rates_id));
			break;

		case 'deleteconfirm':
			$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
			$tax_rates_id = xtc_db_prepare_input($_GET['tID']);

			xtc_db_query("delete from " . TABLE_TAX_RATES . " where tax_rates_id = '" . xtc_db_input($tax_rates_id) . "'");
			xtc_redirect(xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page']));
			break;
	}
}

$messageStack->add(HEADING_WARNING, 'warning');

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html <?php echo HTML_PARAMS; ?>>
		<head>
			<meta http-equiv="x-ua-compatible" content="IE=edge">
			<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
			<title><?php echo TITLE; ?></title>
			<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
			<script type="text/javascript" src="html/assets/javascript/legacy/gm/general.js"></script>
		</head>
		<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
			<!-- header //-->
			<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
			<!-- header_eof //-->

			<!-- body //-->
			<table border="0" width="100%" cellspacing="2" cellpadding="2">
				<tr>
					<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
							<!-- left_navigation //-->
							<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
							<!-- left_navigation_eof //-->
						</table></td>
					<!-- body_text //-->
					<td class="boxCenter" width="100%" valign="top">

						<div class="gx-container create-new-wrapper left-table">
							<div class="create-new-container pull-right">
								<a href="<?php echo xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&action=new') ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
							</div>
							<br /><br />
						</div>

						<table border="0" width="100%" cellspacing="0" cellpadding="2">
							<tr>
								<td>
									<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/land.png)">
										<?php echo $adminMenuLang->get_text('BOX_HEADING_ZONE', 'admin_menu'); ?>
									</div>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td class="dataTableHeadingContent">
												<a href="zones.php">
													<?php echo $adminMenuLang->get_text('BOX_ZONES'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="countries.php">
													<?php echo $adminMenuLang->get_text('BOX_COUNTRIES'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="languages.php">
													<?php echo $adminMenuLang->get_text('BOX_LANGUAGES'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="tax_classes.php">
													<?php echo $adminMenuLang->get_text('BOX_TAX_CLASSES'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<?php echo $adminMenuLang->get_text('BOX_TAX_RATES'); ?>
											</td>
											<td class="dataTableHeadingContent">
												<a href="configuration.php?gID=18">
													<?php echo $adminMenuLang->get_text('BOX_CONFIGURATION_18'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="geo_zones.php">
													<?php echo $adminMenuLang->get_text('BOX_GEO_ZONES'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<a href="currencies.php">
													<?php echo $adminMenuLang->get_text('BOX_CURRENCIES'); ?>
												</a>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td><table border="0" width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
													<tr class="dataTableHeadingRow">
														<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_RATE_PRIORITY; ?></td>
														<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_CLASS_TITLE; ?></td>
														<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ZONE; ?></td>
														<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_RATE; ?></td>
														<td class="dataTableHeadingContent"></td>
													</tr>
													<?php
													$rates_query_raw = "select r.tax_rates_id, z.geo_zone_id, z.geo_zone_name, tc.tax_class_title, tc.tax_class_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified from " . TABLE_TAX_CLASS . " tc, " . TABLE_TAX_RATES . " r left join " . TABLE_GEO_ZONES . " z on r.tax_zone_id = z.geo_zone_id where r.tax_class_id = tc.tax_class_id";
													$rates_split = new splitPageResults($_GET['page'], '20', $rates_query_raw, $rates_query_numrows);
													$rates_query = xtc_db_query($rates_query_raw);
													while ($rates = xtc_db_fetch_array($rates_query)) {
													  if (((!$_GET['tID']) || (@$_GET['tID'] == $rates['tax_rates_id'])) && (!$trInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
														$trInfo = new objectInfo($rates);
													  }

													  if ( (is_object($trInfo)) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id) ) {
														echo '              <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '">' . "\n";
													  } else {
														echo '              <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '">' . "\n";
													  }
												  ?>
																  <td class="dataTableContent"><?php echo $rates['tax_priority']; ?></td>
																  <td class="dataTableContent"><?php echo htmlspecialchars($rates['tax_class_title'], ENT_QUOTES); ?></td>
																  <td class="dataTableContent"><?php echo $rates['geo_zone_name']; ?></td>
																  <td class="dataTableContent"><?php echo xtc_display_tax_value($rates['tax_rate']); ?>%</td>
																  <td class="dataTableContent"></td>
																</tr>
												  <?php
													}
													?>
												</table>

												<table class="gx-container paginator left-table table-paginator">
												    <tr>
												        <td class="pagination-control">
															<?php echo $rates_split->display_count($rates_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_RATES); ?>
												    		<span class="page-number-information">
																<?php echo $rates_split->display_links($rates_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
												    		</span>
												    	</td>
												    </tr>
												</table>
											</td>
										</tr>
									</table></td>
							</tr>
						</table></td>
					<!-- body_text_eof //-->
				</tr>
			</table>
			<!-- body_eof //-->

			<!-- footer //-->
			<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			<!-- footer_eof //-->
			<div class="hidden">
				<?php

				$heading = array();
				$contents = array();
				$buttons = '';
				$formIsEditable = false;
				$formAction = '';
				$formMethod = 'post';
				$formAttributes = array();

				switch ($_GET['action']) {
					case 'new':
						$formAction = xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&action=insert');
						$formIsEditable = true;

						$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
						$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>';

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_TAX_RATE . '</b>');

						$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CLASS_TITLE . '</span>' . xtc_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"'));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_ZONE_NAME . '</span>' . xtc_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"'));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_TAX_RATE . '</span>' . xtc_draw_input_field('tax_rate'));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_RATE_DESCRIPTION . '</span>' . xtc_draw_input_field('tax_description'));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_TAX_RATE_PRIORITY . '</span>' . xtc_draw_input_field('tax_priority'));
						break;

					case 'edit':

						$formAction = xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=save');
						$formIsEditable = true;

						$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>';
						$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>';

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_TAX_RATE . '</b>');

						$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CLASS_TITLE . '</span>' . xtc_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"', $trInfo->tax_class_id));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_ZONE_NAME . '</span>' . xtc_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"', $trInfo->geo_zone_id));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_TAX_RATE . '</span>' . xtc_draw_input_field('tax_rate', $trInfo->tax_rate));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_RATE_DESCRIPTION . '</span>' . xtc_draw_input_field('tax_description', $trInfo->tax_description));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_TAX_RATE_PRIORITY . '</span>' . xtc_draw_input_field('tax_priority', $trInfo->tax_priority));
						break;

					case 'delete':

						$formAction = xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=deleteconfirm');
						$formIsEditable = true;

						$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
						$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id) . '">' . BUTTON_CANCEL . '</a>';

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_TAX_RATE . '</b>');

						$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
						$contents[] = array('text' => '<br /><b>' . htmlspecialchars($trInfo->tax_class_title,ENT_QUOTES) . ' ' . (double)$trInfo->tax_rate . '%</b>');
						break;

					default:
						if (is_object($trInfo)) {
							$editButton = '<a class="pull-right btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
							$deleteButton = '<a class="pull-right btn btn-default btn-delete" href="' . xtc_href_link(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';

							$buttons = $deleteButton . $editButton;

							$heading[] = array('text' => '<b>' . htmlspecialchars($trInfo->tax_class_title, ENT_QUOTES) . '</b>');
							$contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($trInfo->date_added));
							$contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($trInfo->last_modified));
							$contents[] = array('text' => '<br />' . TEXT_INFO_RATE_DESCRIPTION . '<br />' . htmlspecialchars($trInfo->tax_description, ENT_QUOTES));
						}
						break;
				}

				$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
				$configurationBoxContentView->setOldSchoolHeading($heading);
				$configurationBoxContentView->setOldSchoolContents($contents);
				$configurationBoxContentView->set_content_data('buttons', $buttons);
				$configurationBoxContentView->setFormAttributes($formAttributes);
				$configurationBoxContentView->setFormEditable($formIsEditable);
				$configurationBoxContentView->setFormAction($formAction);
				echo $configurationBoxContentView->get_html();
				?>
			</div>
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
