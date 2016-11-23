<?php
/* --------------------------------------------------------------
   geo_zones.php 2015-09-28 gm
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
   (c) 2002-2003 osCommerce(geo_zones.php,v 1.27 2003/05/07); www.oscommerce.com
   (c) 2003	 nextcommerce (geo_zones.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: geo_zones.php 1123 2005-07-27 09:00:31Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

AdminMenuControl::connect_with_page('zones.php');

switch ($_GET['saction']) {
	case 'insert_sub':
		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$zID = xtc_db_prepare_input($_GET['zID']);
		$zone_country_id = xtc_db_prepare_input($_POST['zone_country_id']);
		$zone_id = xtc_db_prepare_input($_POST['zone_id']);

		xtc_db_query("insert into " . TABLE_ZONES_TO_GEO_ZONES . " (zone_country_id, zone_id, geo_zone_id, date_added) values ('" . xtc_db_input($zone_country_id) . "', '" . xtc_db_input($zone_id) . "', '" . xtc_db_input($zID) . "', now())");
		$new_subzone_id = xtc_db_insert_id();

		xtc_redirect(xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $new_subzone_id));
		break;

	case 'save_sub':
		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$sID = xtc_db_prepare_input($_GET['sID']);
		$zID = xtc_db_prepare_input($_GET['zID']);
		$zone_country_id = xtc_db_prepare_input($_POST['zone_country_id']);
		$zone_id = xtc_db_prepare_input($_POST['zone_id']);

		xtc_db_query("update " . TABLE_ZONES_TO_GEO_ZONES . " set geo_zone_id = '" . xtc_db_input($zID) . "', zone_country_id = '" . xtc_db_input($zone_country_id) . "', zone_id = " . ((xtc_db_input($zone_id)) ? "'" . xtc_db_input($zone_id) . "'" : 'null') . ", last_modified = now() where association_id = '" . xtc_db_input($sID) . "'");

		xtc_redirect(xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID']));
		break;

	case 'deleteconfirm_sub':
		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$sID = xtc_db_prepare_input($_GET['sID']);

		xtc_db_query("delete from " . TABLE_ZONES_TO_GEO_ZONES . " where association_id = '" . xtc_db_input($sID) . "'");

		xtc_redirect(xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage']));
		break;
}

switch ($_GET['action']) {
	case 'insert_zone':
		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$geo_zone_name = xtc_db_prepare_input($_POST['geo_zone_name']);
		$geo_zone_description = xtc_db_prepare_input($_POST['geo_zone_description']);

		xtc_db_query("insert into " . TABLE_GEO_ZONES . " (geo_zone_name, geo_zone_description, date_added) values ('" . xtc_db_input($geo_zone_name) . "', '" . xtc_db_input($geo_zone_description) . "', now())");
		$new_zone_id = xtc_db_insert_id();

		xtc_redirect(xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $new_zone_id));
		break;

	case 'save_zone':
		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$zID = xtc_db_prepare_input($_GET['zID']);
		$geo_zone_name = xtc_db_prepare_input($_POST['geo_zone_name']);
		$geo_zone_description = xtc_db_prepare_input($_POST['geo_zone_description']);

		xtc_db_query("update " . TABLE_GEO_ZONES . " set geo_zone_name = '" . xtc_db_input($geo_zone_name) . "', geo_zone_description = '" . xtc_db_input($geo_zone_description) . "', last_modified = now() where geo_zone_id = '" . xtc_db_input($zID) . "'");

		xtc_redirect(xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']));
		break;

	case 'deleteconfirm_zone':
		// check page token
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$zID = xtc_db_prepare_input($_GET['zID']);

		xtc_db_query("delete from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . xtc_db_input($zID) . "'");
		xtc_db_query("delete from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . xtc_db_input($zID) . "'");

		xtc_redirect(xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage']));
		break;
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
			<?php
			if ($_GET['zID']  && (($_GET['saction'] == 'edit') || ($_GET['saction'] == 'new'))) {
				?>
				<script type="text/javascript"><!--
					function resetZoneSelected(theForm) {
						if (theForm.state.value != '') {
							theForm.zone_id.selectedIndex = '0';
							if (theForm.zone_id.options.length > 0) {
								theForm.state.value = '<?php echo JS_STATE_SELECT; ?>';
							}
						}
					}

					function update_zone(theForm) {
						var NumState = theForm.zone_id.options.length;
						var SelectedCountry = "";

						while(NumState > 0) {
							NumState--;
							theForm.zone_id.options[NumState] = null;
						}

						SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;

						<?php echo xtc_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>

					}
					//--></script>
				<?php
			}
			?>
		</head>
		<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
			<!-- header //-->
			<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
			<!-- header_eof //-->

			<!-- body //-->
			<table border="0" width="100%" cellspacing="2" cellpadding="2" data-gx-extension="toolbar_icons" data-toolbar_icons-large="true" data-toolbar_icons-fixedwidth="true">
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
						<div class="gx-container create-new-wrapper left-table">
							<div class="create-new-container pull-right">
								<a href="<?php

								if(isset($_GET['action']) && $_GET['action'] === 'list')
								{
									echo xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=new');
								}
								else
								{
									echo xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=new_zone');
								}

								?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
							</div>
							<br /><br />
						</div>
						<table border="0" width="100%" cellspacing="0" cellpadding="2">
							<tr>
								<td width="100%">
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
												<?php echo $adminMenuLang->get_text('BOX_GEO_ZONES'); ?>
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
											<td valign="top">
												<?php
												if ($_GET['action'] == 'list') {
													?>
													<table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
														<tr class="dataTableHeadingRow">
															<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY; ?></td>
															<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COUNTRY_ZONE; ?></td>
															<td></td>
														</tr>
														<?php
														$rows = 0;
														$zones_query_raw = "select a.association_id, a.zone_country_id, c.countries_name, a.zone_id, a.geo_zone_id, a.last_modified, a.date_added, z.zone_name from " . TABLE_ZONES_TO_GEO_ZONES . " a left join " . TABLE_COUNTRIES . " c on a.zone_country_id = c.countries_id left join " . TABLE_ZONES . " z on a.zone_id = z.zone_id where a.geo_zone_id = " . xtc_db_input($_GET['zID']) . " order by association_id";
														$zones_split = new splitPageResults($_GET['spage'], '20', $zones_query_raw, $zones_query_numrows);
														$zones_query = xtc_db_query($zones_query_raw);
														while ($zones = xtc_db_fetch_array($zones_query)) {
														  $rows++;
														  if (((!$_GET['sID']) || (@$_GET['sID'] == $zones['association_id'])) && (!$sInfo) && (substr($_GET['saction'], 0, 3) != 'new')) {
															$sInfo = new objectInfo($zones);
														  }
														  if ( (is_object($sInfo)) && ($zones['association_id'] == $sInfo->association_id) ) {
															echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url=' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit') . '">' . "\n";
														  } else {
															echo '                  <tr class="dataTableRow active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $zones['association_id']) . '">' . "\n";
														  }
													?>
																	<td class="dataTableContent"><?php echo (($zones['countries_name']) ? htmlspecialchars($zones['countries_name'], ENT_QUOTES) : TEXT_ALL_COUNTRIES); ?></td>
																	<td class="dataTableContent"><?php echo (($zones['zone_id']) ? htmlspecialchars($zones['zone_name'], ENT_QUOTES) : PLEASE_SELECT); ?></td>
																	<td class="dataTableContent"></td>
																  </tr>
													<?php
														}
														?>
													</table>

													<table class="gx-container paginator left-table table-paginator">
													    <tr>
															<?php if (!$_GET['saction']) echo '<td><a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']) . '">' . BUTTON_BACK . '</a></td>'; ?>
													        <td class="pagination-control">
													    		<?php echo $zones_split->display_count($zones_query_numrows, '20', $_GET['spage'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?>
													    		<span class="page-number-information">
																	<?php echo $zones_split->display_links($zones_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['spage'], 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list', 'spage'); ?>
													    		</span>
													    	</td>
													    </tr>
													</table>
													<?php
												} else {
													?>
													<table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
														<tr class="dataTableHeadingRow">
															<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAX_ZONES; ?></td>
															<td></td>
														</tr>
														<?php
														$zones_query_raw = "select geo_zone_id, geo_zone_name, geo_zone_description, last_modified, date_added from " . TABLE_GEO_ZONES . " order by geo_zone_name";
														$zones_split = new splitPageResults($_GET['zpage'], '20', $zones_query_raw, $zones_query_numrows);
														$zones_query = xtc_db_query($zones_query_raw);
														while ($zones = xtc_db_fetch_array($zones_query)) {
														  if (((!$_GET['zID']) || (@$_GET['zID'] == $zones['geo_zone_id'])) && (!$zInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
															$num_zones_query = xtc_db_query("select count(*) as num_zones from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . $zones['geo_zone_id'] . "' group by geo_zone_id");
															if (xtc_db_num_rows($num_zones_query) > 0) {
															  $num_zones = xtc_db_fetch_array($num_zones_query);
															  $zones['num_zones'] = $num_zones['num_zones'];
															} else {
															  $zones['num_zones'] = 0;
															}
															$zInfo = new objectInfo($zones);
														  }
														  if ( (is_object($zInfo)) && ($zones['geo_zone_id'] == $zInfo->geo_zone_id) ) {
															echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=list') . '">' . "\n";
														  } else {
															echo '                  <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zones['geo_zone_id']) . '">' . "\n";
														  }
													?>
																	<td class="dataTableContent"><?php echo '<a class="btn-folder" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zones['geo_zone_id'] . '&action=list') . '">' . '</a>&nbsp;' . htmlspecialchars($zones['geo_zone_name'], ENT_QUOTES); ?></td>
																	<td class="dataTableContent"></td>
																  </tr>
													<?php
														}
														?>
													</table>
													<table class="gx-container paginator left-table table-paginator">
													    <tr>
													        <td class="pagination-control">
													    		<?php echo $zones_split->display_count($zones_query_numrows, '20', $_GET['zpage'], TEXT_DISPLAY_NUMBER_OF_TAX_ZONES); ?>
													    		<span class="page-number-information">
																	<?php echo $zones_split->display_links($zones_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['zpage'], '', 'zpage'); ?>
													    		</span>
													    	</td>
													    </tr>
													</table>
													<?php
												}
												?>
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

				if ($_GET['action'] == 'list') {
					switch ($_GET['saction']) {
						case 'new':

							$formAction = xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID'] . '&saction=insert_sub');
							$formIsEditable = true;

							$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
							$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $_GET['sID']) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_SUB_ZONE . '</b>');

							$contents[] = array('text' => TEXT_INFO_NEW_SUB_ZONE_INTRO);
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY . '</span>' . xtc_draw_pull_down_menu('zone_country_id', xtc_get_countries(TEXT_ALL_COUNTRIES), '', 'onChange="update_zone(this.form);"'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY_ZONE . '</span>' . xtc_draw_pull_down_menu('zone_id', xtc_prepare_country_zones_pull_down()));
							break;

						case 'edit':

							$formAction = xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=save_sub');
							$formIsEditable = true;

							$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>';
							$buttons .= '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_SUB_ZONE . '</b>');

							$contents[] = array('text' => TEXT_INFO_EDIT_SUB_ZONE_INTRO);
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY . '</span>' . xtc_draw_pull_down_menu('zone_country_id', xtc_get_countries(TEXT_ALL_COUNTRIES), $sInfo->zone_country_id, 'onChange="update_zone(this.form);"'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_COUNTRY_ZONE . '</span>' . xtc_draw_pull_down_menu('zone_id', xtc_prepare_country_zones_pull_down($sInfo->zone_country_id), $sInfo->zone_id));
							break;

						case 'delete':

							$formAction = xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=deleteconfirm_sub');
							$formIsEditable = true;

							$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
							$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SUB_ZONE . '</b>');

							$contents[] = array('text' => TEXT_INFO_DELETE_SUB_ZONE_INTRO);
							$contents[] = array('text' => '<br /><b>' . $sInfo->countries_name . '</b>');
							break;

						default:
							if (is_object($sInfo)) {
								$editButton = '<a class="pull-right btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=edit') . '">' . BUTTON_EDIT . '</a>';
								$deleteButton = '<a class="pull-right btn btn-default btn-delete" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=list&spage=' . $_GET['spage'] . '&sID=' . $sInfo->association_id . '&saction=delete') . '">' . BUTTON_DELETE . '</a>';

								$buttons = $deleteButton . $editButton;

								$heading[] = array('text' => '<b>' . htmlspecialchars($sInfo->countries_name, ENT_QUOTES) . '</b>');

								$contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($sInfo->date_added));
								if (xtc_not_null($sInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($sInfo->last_modified));
							}
							break;
					}
				} else {
					switch ($_GET['action']) {
						case 'new_zone':

							$formAction = xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID'] . '&action=insert_zone');
							$formIsEditable = true;

							$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
							$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $_GET['zID']) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ZONE . '</b>');

							$contents[] = array('text' => TEXT_INFO_NEW_ZONE_INTRO);
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_ZONE_NAME . '</span>' . xtc_draw_input_field('geo_zone_name'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_ZONE_DESCRIPTION . '</span>' . xtc_draw_input_field('geo_zone_description'));
							break;

						case 'edit_zone':

							$formAction = xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=save_zone');
							$formIsEditable = true;

							$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>';
							$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ZONE . '</b>');

							$contents[] = array('text' => TEXT_INFO_EDIT_ZONE_INTRO);
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_ZONE_NAME . '</span>' . xtc_draw_input_field('geo_zone_name', $zInfo->geo_zone_name));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_ZONE_DESCRIPTION . '</span>' . xtc_draw_input_field('geo_zone_description', $zInfo->geo_zone_description));
							break;

						case 'delete_zone':

							$formAction = xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=deleteconfirm_zone');
							$formIsEditable = true;

							$buttons = '<input type="submit" class="button btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
							$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ZONE . '</b>');

							$contents[] = array('text' => TEXT_INFO_DELETE_ZONE_INTRO);
							$contents[] = array('text' => '<br /><b>' . htmlspecialchars($zInfo->geo_zone_name, ENT_QUOTES) . '</b>');
							break;

						default:
							if (is_object($zInfo)) {
								$editButton = '<a class="pull-right btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=edit_zone') . '">' . BUTTON_EDIT . '</a>';
								$deleteButton = '<a class="pull-right btn btn-default btn-delete" href="' . xtc_href_link(FILENAME_GEO_ZONES, 'zpage=' . $_GET['zpage'] . '&zID=' . $zInfo->geo_zone_id . '&action=delete_zone') . '">' . BUTTON_DELETE . '</a>';

								$buttons = $deleteButton . $editButton;

								$heading[] = array('text' => '<b>' . htmlspecialchars($zInfo->geo_zone_name, ENT_QUOTES) . '</b>');

								$contents[] = array('text' => '<br />' . TEXT_INFO_NUMBER_ZONES . ' ' . $zInfo->num_zones);
								$contents[] = array('text' => '<br />' . TEXT_INFO_DATE_ADDED . ' ' . xtc_date_short($zInfo->date_added));
								if (xtc_not_null($zInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . xtc_date_short($zInfo->last_modified));
								$contents[] = array('text' => '<br />' . TEXT_INFO_ZONE_DESCRIPTION . '<br />' . htmlspecialchars($zInfo->geo_zone_description, ENT_QUOTES));
							}
							break;
					}
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
