<?php
/* --------------------------------------------------------------
   countries.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(countries.php,v 1.26 2003/05/17); www.oscommerce.com
   (c) 2003	 nextcommerce (countries.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: countries.php 1123 2005-07-27 09:00:31Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

AdminMenuControl::connect_with_page('zones.php');

if ($_GET['action']) {
	switch ($_GET['action']) {
		case 'insert':
			$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
			$countries_name = xtc_db_prepare_input($_POST['countries_name']);
			$countries_iso_code_2 = xtc_db_prepare_input($_POST['countries_iso_code_2']);
			$countries_iso_code_3 = xtc_db_prepare_input($_POST['countries_iso_code_3']);
			$address_format_id = xtc_db_prepare_input($_POST['address_format_id']);

			xtc_db_query("insert into " . TABLE_COUNTRIES . " (countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id) values ('" . xtc_db_input($countries_name) . "', '" . xtc_db_input($countries_iso_code_2) . "', '" . xtc_db_input($countries_iso_code_3) . "', '" . xtc_db_input($address_format_id) . "')");
			xtc_redirect(xtc_href_link(FILENAME_COUNTRIES));
			break;
		case 'save':
			$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
			$countries_id = xtc_db_prepare_input($_GET['cID']);
			$countries_name = xtc_db_prepare_input($_POST['countries_name']);
			$countries_iso_code_2 = xtc_db_prepare_input($_POST['countries_iso_code_2']);
			$countries_iso_code_3 = xtc_db_prepare_input($_POST['countries_iso_code_3']);
			$address_format_id = xtc_db_prepare_input($_POST['address_format_id']);

			xtc_db_query("update " . TABLE_COUNTRIES . " set countries_name = '" . xtc_db_input($countries_name) . "', countries_iso_code_2 = '" . xtc_db_input($countries_iso_code_2) . "', countries_iso_code_3 = '" . xtc_db_input($countries_iso_code_3) . "', address_format_id = '" . xtc_db_input($address_format_id) . "' where countries_id = '" . xtc_db_input($countries_id) . "'");
			xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));
			break;
		case 'deleteconfirm':
			$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
			$countries_id = xtc_db_prepare_input($_GET['cID']);

			xtc_db_query("delete from " . TABLE_COUNTRIES . " where countries_id = '" . xtc_db_input($countries_id) . "'");
			xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page']));
			break;
		case 'setlflag':
			$countries_id = xtc_db_prepare_input($_GET['cID']);
			$status = xtc_db_prepare_input($_GET['flag']);
			xtc_db_query("update " . TABLE_COUNTRIES . " set status = '" . xtc_db_input($status) . "' where countries_id = '" . xtc_db_input($countries_id) . "'");
			xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));
			break;
		/* bof gm */
		case 'setstatus':
			$status = xtc_db_prepare_input($_GET['flag']);
			xtc_db_query("update " . TABLE_COUNTRIES . " set status = '" . xtc_db_input($status) . "'");
			xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));
			break;
		case 'gm_set_dach_active':
			xtc_db_query("update " . TABLE_COUNTRIES . " set status = '0'");
			xtc_db_query("update " . TABLE_COUNTRIES . " set status = '1' WHERE countries_iso_code_2 = 'DE' OR countries_iso_code_2 = 'AT' OR countries_iso_code_2 = 'CH'");
			xtc_redirect(xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));
			break;
		/* eof gm */

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
								<a href="<?php echo xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&action=new') ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
							</div>
							<br /><br />
						</div>

						<table border="0" width="100%" cellspacing="0" cellpadding="2">
							<tr>
								<td>
									<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/land.png)">
										<?php echo $adminMenuLang->get_text('BOX_HEADING_ZONE', 'admin_menu'); ?>
									</div>
								</td>
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
												<?php echo $adminMenuLang->get_text('BOX_COUNTRIES'); ?>
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
												<a href="tax_rates.php">
													<?php echo $adminMenuLang->get_text('BOX_TAX_RATES'); ?>
												</a>
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
														<td class="dataTableHeadingContent" style="min-width: 200px;"><?php echo TABLE_HEADING_COUNTRY_NAME; ?></td>
														<td class="dataTableHeadingContent" style="min-width: 200px;" align="center" colspan="2"><?php echo TABLE_HEADING_COUNTRY_CODES; ?></td>
														<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
														<td class="dataTableHeadingContent"></td>
													</tr>
													<?php
													$countries_query_raw = "select countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, status, address_format_id from " . TABLE_COUNTRIES . " order by countries_name";
													$countries_split = new splitPageResults($_GET['page'], '20', $countries_query_raw, $countries_query_numrows);
													$countries_query = xtc_db_query($countries_query_raw);
													while ($countries = xtc_db_fetch_array($countries_query)) {
													  if (((!$_GET['cID']) || (@$_GET['cID'] == $countries['countries_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
														$cInfo = new objectInfo($countries);
													  }

													  if ( (is_object($cInfo)) && ($countries['countries_id'] == $cInfo->countries_id) ) {
														echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit') . '">' . "\n";
													  } else {
														echo '                  <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries['countries_id']) . '">' . "\n";
													  }
												  ?>
																  <td class="dataTableContent"><?php echo htmlspecialchars($countries['countries_name'], ENT_QUOTES); ?></td>
																  <td class="dataTableContent" align="center" width="40"><?php echo $countries['countries_iso_code_2']; ?></td>
																  <td class="dataTableContent" align="center" width="40"><?php echo $countries['countries_iso_code_3']; ?></td>
																  <td class="dataTableContent" align="center">
												  <?php
													  echo '<div data-gx-widget="checkbox"
																		  data-checkbox-checked="' . (($countries['status'] == '1') ? 'true' : 'false') . '"
																		  data-checkbox-on_url="' . xtc_href_link(FILENAME_COUNTRIES, xtc_get_all_get_params(array('page', 'action', 'cID')) . 'action=setlflag&flag=1&cID=' . $countries['countries_id'] . '&page='.$_GET['page']) . '"
																		  data-checkbox-off_url="' . xtc_href_link(FILENAME_COUNTRIES, xtc_get_all_get_params(array('page', 'action', 'cID')) . 'action=setlflag&flag=0&cID=' . $countries['countries_id'] . '&page='.$_GET['page']) . '"></div>';
												  ?>
																  </td>
																  <td class="dataTableContent"></td>
																</tr>
												  <?php
													}
													?>
												</table>

												<table class="gx-container paginator left-table table-paginator">
												    <tr>
														<td>
															<?php echo '<a class="button pull-left" href="' . xtc_href_link(FILENAME_COUNTRIES, 'action=setstatus&flag=1&cID=' . $countries['countries_id'] . '&page='.$_GET['page']) . '">' . GM_BUTTON_ACTIVE		. '</a>'; ?>
															<?php echo '<a class="button pull-left" href="' . xtc_href_link(FILENAME_COUNTRIES, 'action=setstatus&flag=0&cID=' . $countries['countries_id'] . '&page='.$_GET['page']) . '">' . GM_BUTTON_INACTIVE		. '</a>'; ?>
															<?php echo '<a class="button pull-left" href="' . xtc_href_link(FILENAME_COUNTRIES, 'action=gm_set_dach_active&cID=' . $countries['countries_id'] . '&page='.$_GET['page']) . '">' . GM_BUTTON_DACH		. '</a>'; ?>
														</td>
												        <td class="pagination-control">
															<?php echo $countries_split->display_count($countries_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?>
												    		<span class="page-number-information">
																<?php echo $countries_split->display_links($countries_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
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

						$formAction = xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&action=insert');
						$formIsEditable = true;

						$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
						$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>';

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_COUNTRY . '</b>');

						$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY_NAME . '</span>' . xtc_draw_input_field('countries_name'));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY_CODE_2 . '</span>' . xtc_draw_input_field('countries_iso_code_2'));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY_CODE_3 . '</span>' . xtc_draw_input_field('countries_iso_code_3'));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_ADDRESS_FORMAT . '</span>' . xtc_draw_pull_down_menu('address_format_id', xtc_get_address_formats()));
						break;
					case 'edit':

						$formAction = xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=save');
						$formIsEditable = true;

						$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>';
						$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id) . '">' . BUTTON_CANCEL . '</a>';

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_COUNTRY . '</b>');

						$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY_NAME . '</span>' . xtc_draw_input_field('countries_name', $cInfo->countries_name));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY_CODE_2 . '</span>' . xtc_draw_input_field('countries_iso_code_2', $cInfo->countries_iso_code_2));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY_CODE_3 . '</span>' . xtc_draw_input_field('countries_iso_code_3', $cInfo->countries_iso_code_3));
						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_ADDRESS_FORMAT . '</span>' . xtc_draw_pull_down_menu('address_format_id', xtc_get_address_formats(), $cInfo->address_format_id));
						break;
					case 'delete':
						$formAction = xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=deleteconfirm');
						$formIsEditable = true;

						$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
						$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id) . '">' . BUTTON_CANCEL . '</a>';

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_COUNTRY . '</b>');

						$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
						$contents[] = array('text' => '<br /><b>' . htmlspecialchars($cInfo->countries_name, ENT_QUOTES) . '</b>');
						break;
					default:
						if (is_object($cInfo)) {
							$editButton = '<a class="pull-right btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
							$deleteButton = '<a class="pull-right btn btn-default btn-delete" href="' . xtc_href_link(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';
							$buttons =  $deleteButton . $editButton;

							$heading[] = array('text' => '<b>' . htmlspecialchars($cInfo->countries_name, ENT_QUOTES) . '</b>');

							$contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_NAME . '<br />' . htmlspecialchars($cInfo->countries_name, ENT_QUOTES));
							$contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_2 . ' ' . $cInfo->countries_iso_code_2);
							$contents[] = array('text' => '<br />' . TEXT_INFO_COUNTRY_CODE_3 . ' ' . $cInfo->countries_iso_code_3);
							$contents[] = array('text' => '<br />' . TEXT_INFO_ADDRESS_FORMAT . ' ' . $cInfo->address_format_id);
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
