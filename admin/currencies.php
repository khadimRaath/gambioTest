<?php
/* --------------------------------------------------------------
   currencies.php 2016-05-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(currencies.php,v 1.46 2003/05/02); www.oscommerce.com
   (c) 2003	 nextcommerce (currencies.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: currencies.php 1123 2005-07-27 09:00:31Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/
require('includes/application_top.php');

AdminMenuControl::connect_with_page('zones.php');

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

if ($_GET['action']) {
	switch ($_GET['action']) {
		case 'insert':
		case 'save':
			// check page token
			$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

			$currency_id = xtc_db_prepare_input($_GET['cID']);
			$title = xtc_db_prepare_input($_POST['title']);
			$code = xtc_db_prepare_input($_POST['code']);
			$symbol_left = xtc_db_prepare_input($_POST['symbol_left']);
			$symbol_right = xtc_db_prepare_input($_POST['symbol_right']);
			$decimal_point = xtc_db_prepare_input($_POST['decimal_point']);
			$thousands_point = xtc_db_prepare_input($_POST['thousands_point']);
			$decimal_places = xtc_db_prepare_input($_POST['decimal_places']);
			$value = xtc_db_prepare_input($_POST['value']);

			$sql_data_array = array('title' => $title,
			                        'code' => $code,
			                        'symbol_left' => $symbol_left,
			                        'symbol_right' => $symbol_right,
			                        'decimal_point' => $decimal_point,
			                        'thousands_point' => $thousands_point,
			                        'decimal_places' => $decimal_places,
			                        'value' => $value);

			if ($_GET['action'] == 'insert') {
				xtc_db_perform(TABLE_CURRENCIES, $sql_data_array);
				$currency_id = xtc_db_insert_id();
			} elseif ($_GET['action'] == 'save') {
				xtc_db_perform(TABLE_CURRENCIES, $sql_data_array, 'update', "currencies_id = '" . xtc_db_input($currency_id) . "'");
			}

			if ($_POST['default'] == 'on') {
				xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($code) . "' where configuration_key = 'DEFAULT_CURRENCY'");
			}
			xtc_redirect(xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency_id));
			break;

		case 'deleteconfirm':

			// check page token
			$_SESSION['coo_page_token']->is_valid($_GET['page_token']);

			$currencies_id = xtc_db_prepare_input($_GET['cID']);

			$currency_query = xtc_db_query("select currencies_id from " . TABLE_CURRENCIES . " where code = '" . DEFAULT_CURRENCY . "'");
			$currency = xtc_db_fetch_array($currency_query);
			if ($currency['currencies_id'] == $currencies_id) {
				xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_CURRENCY'");
			}

			xtc_db_query("delete from " . TABLE_CURRENCIES . " where currencies_id = '" . xtc_db_input($currencies_id) . "'");

			xtc_redirect(xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page']));
			break;

		case 'update':
			$currency_query = xtc_db_query("select currencies_id, code, title from " . TABLE_CURRENCIES);
			while ($currency = xtc_db_fetch_array($currency_query)) {
				$quote_function = 'quote_' . CURRENCY_SERVER_PRIMARY . '_currency';
				$rate = $quote_function($currency['code']);
				/*
				if ( (!$rate) && (CURRENCY_SERVER_BACKUP != '') ) {
					$quote_function = 'quote_' . CURRENCY_SERVER_BACKUP . '_currency';
					$rate = $quote_function($currency['code']);
				}
				*/
				if ($rate) {
					xtc_db_query("update " . TABLE_CURRENCIES . " set value = '" . $rate . "', last_updated = now() where currencies_id = '" . $currency['currencies_id'] . "'");
					$messageStack->add_session(sprintf(TEXT_INFO_CURRENCY_UPDATED, $currency['title'], $currency['code']), 'success');
				} else {
					$messageStack->add_session(sprintf(ERROR_CURRENCY_INVALID, $currency['title'], $currency['code']), 'error');
				}
			}
			xtc_redirect(xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']));
			break;

		case 'delete':
			$currencies_id = xtc_db_prepare_input($_GET['cID']);

			$currency_query = xtc_db_query("select code from " . TABLE_CURRENCIES . " where currencies_id = '" . xtc_db_input($currencies_id) . "'");
			$currency = xtc_db_fetch_array($currency_query);

			$remove_currency = true;
			if ($currency['code'] == DEFAULT_CURRENCY) {
				$remove_currency = false;
			}
			break;
	}
}

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
								<a href="<?php echo xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=new') ?>" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create', 'buttons'); ?></a>
							</div>
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
												<a href="geo_zones.php">
													<?php echo $adminMenuLang->get_text('BOX_GEO_ZONES'); ?>
												</a>
											</td>
											<td class="dataTableHeadingContent">
												<?php echo $adminMenuLang->get_text('BOX_CURRENCIES'); ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<br />
									<table border="0" width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
													<tr class="dataTableHeadingRow">
														<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENCY_NAME; ?></td>
														<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENCY_CODES; ?></td>
														<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_CURRENCY_VALUE; ?></td>
														<td class="dataTableHeadingContent"></td>
													</tr>
													<?php
													$currency_query_raw = "select currencies_id, title, code, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, last_updated, value from " . TABLE_CURRENCIES . " order by title";
													$currency_split = new splitPageResults($_GET['page'], '20', $currency_query_raw, $currency_query_numrows);
													$currency_query = xtc_db_query($currency_query_raw);
													while ($currency = xtc_db_fetch_array($currency_query)) {
													  if (((!$_GET['cID']) || (@$_GET['cID'] == $currency['currencies_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
														$cInfo = new objectInfo($currency);
													  }

													  if ( (is_object($cInfo)) && ($currency['currencies_id'] == $cInfo->currencies_id) ) {
														echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '">' . "\n";
													  } else {
														echo '                  <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency['currencies_id']) . '">' . "\n";
													  }

													  if (DEFAULT_CURRENCY == $currency['code']) {
														echo '                <td class="dataTableContent">' . htmlspecialchars($currency['title'], ENT_QUOTES) . ' (' . TEXT_DEFAULT . ')</td>' . "\n";
													  } else {
														echo '                <td class="dataTableContent">' .  htmlspecialchars($currency['title'], ENT_QUOTES) . '</td>' . "\n";
													  }
												  ?>
																  <td class="dataTableContent"><?php echo htmlspecialchars($currency['code'], ENT_QUOTES); ?></td>
																  <td class="dataTableContent" align="left"><?php echo number_format((double)$currency['value'], 8); ?></td>
																  <td class="dataTableContent"></td>
																</tr>
												  <?php
													}
													?>
												</table>

												<table class="gx-container paginator left-table table-paginator">
												    <tr>
														<td>
															<?php if (CURRENCY_SERVER_PRIMARY) { echo '<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=update') . '">' . BUTTON_UPDATE . '</a>'; } ?>
														</td>
												        <td class="pagination-control">
															<?php echo $currency_split->display_count($currency_query_numrows, '20', $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CURRENCIES); ?>
												    		<span class="page-number-information">
																<?php echo $currency_split->display_links($currency_query_numrows, '20', MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
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

							$formAction = xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=insert');
							$formIsEditable = true;

							$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
							$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CURRENCY . '</b>');

							$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_TITLE . '</span>' . xtc_draw_input_field('title'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_CODE . '</span>' . xtc_draw_input_field('code'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '</span>' . xtc_draw_input_field('symbol_left'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '</span>' . xtc_draw_input_field('symbol_right'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '</span>' . xtc_draw_input_field('decimal_point'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_THOUSANDS_POINT . '</span>' . xtc_draw_input_field('thousands_point'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '</span>' . xtc_draw_input_field('decimal_places'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_VALUE . '</span>' . xtc_draw_input_field('value'));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_SET_DEFAULT . '</span>' . TEXT_INFO_SET_AS_DEFAULT . '<br /><br />' . xtc_draw_checkbox_field('default'));
							break;

						case 'edit':

							$formAction = xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=save');
							$formIsEditable = true;

							$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>';
							$buttons .= '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CURRENCY . '</b>');

							$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_TITLE . '</span>' . xtc_draw_input_field('title', $cInfo->title));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_CODE . '</span>' . xtc_draw_input_field('code', $cInfo->code));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '</span>' . xtc_draw_input_field('symbol_left', $cInfo->symbol_left));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '</span>' . xtc_draw_input_field('symbol_right', $cInfo->symbol_right));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '</span>' . xtc_draw_input_field('decimal_point', $cInfo->decimal_point));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_THOUSANDS_POINT . '</span>' . xtc_draw_input_field('thousands_point', $cInfo->thousands_point));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '</span>' . xtc_draw_input_field('decimal_places', $cInfo->decimal_places));
							$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_CURRENCY_VALUE . '</span>' . xtc_draw_input_field('value', $cInfo->value));
							if (DEFAULT_CURRENCY != $cInfo->code) $contents[] = array('text' => '<br /><span class="options-title">' . TEXT_SET_DEFAULT . '</span>' . TEXT_INFO_SET_AS_DEFAULT . '<br /><br />' . xtc_draw_checkbox_field('default'));
							break;

						case 'delete':
							$buttons = '<a class="btn btn-default" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id) . '">' . BUTTON_CANCEL . '</a>';

							$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CURRENCY . '</b>');

							// Remove Default
							if (!$remove_currency)
							{
								$contents[] = array('text' => ERROR_REMOVE_DEFAULT_CURRENCY);
							} else {
								$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
								$contents[] = array('text' => '<br /><b>' . htmlspecialchars($cInfo->title, ENT_QUOTES) . '</b>');
								$buttons .= '<a class="btn btn-primary" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=deleteconfirm&page_token=' . $_SESSION['coo_page_token']->generate_token()) . '">' . BUTTON_DELETE . '</a>';
							}


							break;

						default:
							if (is_object($cInfo)) {

								$editButton = '<a class="pull-right btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
								$deleteButton = '<a class="pull-right btn btn-default btn-delete" href="' . xtc_href_link(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';

								$buttons = $deleteButton . $editButton;

								$heading[] = array('text' => '<b>' . htmlspecialchars($cInfo->title, ENT_QUOTES) . '</b>');

								$contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_TITLE . ' ' . htmlspecialchars($cInfo->title, ENT_QUOTES));
								$contents[] = array('text' => TEXT_INFO_CURRENCY_CODE . ' ' . htmlspecialchars($cInfo->code, ENT_QUOTES));
								$contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . ' ' . htmlspecialchars($cInfo->symbol_left, ENT_QUOTES));
								$contents[] = array('text' => TEXT_INFO_CURRENCY_SYMBOL_RIGHT . ' ' . htmlspecialchars($cInfo->symbol_right, ENT_QUOTES));
								$contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_DECIMAL_POINT . ' ' . htmlspecialchars($cInfo->decimal_point, ENT_QUOTES));
								$contents[] = array('text' => TEXT_INFO_CURRENCY_THOUSANDS_POINT . ' ' . htmlspecialchars($cInfo->thousands_point, ENT_QUOTES));
								$contents[] = array('text' => TEXT_INFO_CURRENCY_DECIMAL_PLACES . ' ' . htmlspecialchars($cInfo->decimal_places, ENT_QUOTES));
								$contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_LAST_UPDATED . ' ' . xtc_date_short($cInfo->last_updated));
								$contents[] = array('text' => TEXT_INFO_CURRENCY_VALUE . ' ' . number_format((double)$cInfo->value, 8));
								$contents[] = array('text' => '<br />' . TEXT_INFO_CURRENCY_EXAMPLE . '<br />' . $currencies->format('30', false, DEFAULT_CURRENCY) . ' = ' . htmlspecialchars($currencies->format('30', true, $cInfo->code), ENT_QUOTES));
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
					$configurationBoxContentView->setUseCheckboxWidget(true);
					echo $configurationBoxContentView->get_html();
				?>
			</div>
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
