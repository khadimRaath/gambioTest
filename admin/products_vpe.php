<?php
/* --------------------------------------------------------------
   products_vpe.php 2015-09-23 gm
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
   (c) 2002-2003 osCommerce(order_status.php,v 1.19 2003/02/06); www.oscommerce.com
   (c) 2003	 nextcommerce (order_status.php,v 1.9 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: products_vpe.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

switch ($_GET['action']) {
	case 'insert':
	case 'save':
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$products_vpe_id = xtc_db_prepare_input($_GET['oID']);

		$languages = xtc_get_languages();
		for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
			$products_vpe_name_array = $_POST['products_vpe_name'];
			$language_id = $languages[$i]['id'];

			$sql_data_array = array('products_vpe_name' => xtc_db_prepare_input($products_vpe_name_array[$language_id]));

			if ($_GET['action'] == 'insert') {
				if (!xtc_not_null($products_vpe_id)) {
					$next_id_query = xtc_db_query("select max(products_vpe_id) as products_vpe_id from " . TABLE_PRODUCTS_VPE . "");
					$next_id = xtc_db_fetch_array($next_id_query);
					$products_vpe_id = $next_id['products_vpe_id'] + 1;
				}

				$insert_sql_data = array('products_vpe_id' => $products_vpe_id,
				                         'language_id' => $language_id);
				$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
				xtc_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array);
			} elseif ($_GET['action'] == 'save') {
				$exists_query = xtc_db_query("select products_vpe_id from " . TABLE_PRODUCTS_VPE . " WHERE language_id = '" . $language_id . "' and products_vpe_id = '" . xtc_db_input($products_vpe_id) . "'");
				if(xtc_db_num_rows($exists_query) >0) xtc_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array, 'update', "products_vpe_id = '" . xtc_db_input($products_vpe_id) . "' and language_id = '" . $language_id . "'");
				else {
					if (!xtc_not_null($products_vpe_id)) {
						$next_id_query = xtc_db_query("select max(products_vpe_id) as products_vpe_id from " . TABLE_PRODUCTS_VPE . "");
						$next_id = xtc_db_fetch_array($next_id_query);
						$products_vpe_id = $next_id['products_vpe_id'] + 1;
					}

					$insert_sql_data = array('products_vpe_id' => $products_vpe_id,
					                         'language_id' => $language_id);
					$sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
					xtc_db_perform(TABLE_PRODUCTS_VPE, $sql_data_array);

				}
			}
		}

		if ($_POST['default'] == 'on') {
			xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($products_vpe_id) . "' where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
		}

		xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe_id));
		break;

	case 'deleteconfirm':
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);

		$oID = xtc_db_prepare_input($_GET['oID']);

		$products_vpe_query = xtc_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
		$products_vpe = xtc_db_fetch_array($products_vpe_query);
		if ($products_vpe['configuration_value'] == $oID) {
			xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_PRODUCTS_VPE_ID'");
		}

		xtc_db_query("delete from " . TABLE_PRODUCTS_VPE . " where products_vpe_id = '" . xtc_db_input($oID) . "'");

		xtc_db_query('UPDATE `products` SET `products_vpe` = 0, `products_vpe_status` = 0 WHERE `products_vpe` = ' . (int)$oID);
		xtc_db_query('UPDATE `products_attributes` SET `products_vpe_id` = 0 WHERE `products_vpe_id` = ' . (int)$oID);
		xtc_db_query('UPDATE `products_properties_combis` SET `products_vpe_id` = 0 WHERE `products_vpe_id` = ' . (int)$oID);
		xtc_db_query('UPDATE `products_properties_combis_defaults` SET `products_vpe_id` = 0 WHERE `products_vpe_id` = ' . (int)$oID);

		xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page']));
		break;

	case 'delete':
		$oID = xtc_db_prepare_input($_GET['oID']);
		$remove_status = true;

		// In the past it was not allowed to delete the default vpe. There is no reason for doing that.
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
							<a href="<?php echo xtc_href_link(FILENAME_PRODUCTS_VPE,
							                                  'page=' . $_GET['page'] . '&action=new'); ?>"
							   class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;<?php echo $GLOBALS['languageTextManager']->get_text('create',
							                                                                                                                'buttons'); ?>
							</a>
						</div>
					</div>

					<table border="0" width="100%" cellspacing="0" cellpadding="2">
						<tr>
							<td>
								<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/meinshop.png)"><?php echo BOX_PRODUCTS_VPE; ?></div>
							</td>
						</tr>
						<tr>
							<td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
									<tr>
										<td valign="top"><table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="2">
												<tr class="dataTableHeadingRow">
													<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_VPE; ?></td>
													<td class="dataTableHeadingContent" align="right">&nbsp;</td>
													<td class="dataTableHeadingContent"></td>
												</tr>
												<?php
												$products_vpe_query_raw = "select products_vpe_id, products_vpe_name from " . TABLE_PRODUCTS_VPE . " where language_id = '" . $_SESSION['languages_id'] . "' order by products_vpe_id";
												$products_vpe_split = new splitPageResults($_GET['page'], '20', $products_vpe_query_raw, $products_vpe_query_numrows);
												$products_vpe_query = xtc_db_query($products_vpe_query_raw);

												if(xtc_db_num_rows($products_vpe_query) == 0)
												{
													$gmLangEditTextManager = MainFactory::create('LanguageTextManager', 'gm_lang_edit', $_SESSION['language_id']);
													echo '
												          <tr class="gx-container no-hover">
												              <td colspan="10" class="text-center">' . $gmLangEditTextManager->get_text('TEXT_NO_RESULT') . '</td>
												          </tr>
												      ';
												}

												while ($products_vpe = xtc_db_fetch_array($products_vpe_query)) {
													if (((!$_GET['oID']) || ($_GET['oID'] == $products_vpe['products_vpe_id'])) && (!$oInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
														$oInfo = new objectInfo($products_vpe);
													}

													if ( (is_object($oInfo)) && ($products_vpe['products_vpe_id'] == $oInfo->products_vpe_id) ) {
														echo '                  <tr class="dataTableRowSelected active" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=edit') . '">' . "\n";
													} else {
														echo '                  <tr class="dataTableRow" data-gx-extension="link" data-link-url="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $products_vpe['products_vpe_id']) . '">' . "\n";
													}

													if (DEFAULT_PRODUCTS_VPE_ID == $products_vpe['products_vpe_id']) {
														echo '                <td class="dataTableContent">' . $products_vpe['products_vpe_name'] . ' (' . TEXT_DEFAULT . ')</td>' . "\n";
													} else {
														echo '                <td class="dataTableContent">' . $products_vpe['products_vpe_name'] . '</td>' . "\n";
													}
													?>
            <td class="dataTableContent" align="right"></td>
            <td></td>
          </tr>
<?php
												}
												?>
											</table>
											<table class="gx-container paginator left-table table-paginator">
												<tr>
													<td class="pagination-control">
														<?php echo $products_vpe_split->display_count($products_vpe_query_numrows,
														                                              '20',
														                                              $_GET['page'],
														                                              TEXT_DISPLAY_NUMBER_OF_PRODUCTS_VPE); ?>
														<span class="page-number-information">
															<?php echo $products_vpe_split->display_links($products_vpe_query_numrows,
															                                              '20',
															                                              MAX_DISPLAY_PAGE_LINKS,
															                                              $_GET['page']); ?>
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
						$formAction = xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&action=insert');
						$formIsEditable = true;

						$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_INSERT . '"/>';
						$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page']) . '">' . BUTTON_CANCEL . '</a>';

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_PRODUCTS_VPE . '</b>');
						$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);

						$products_vpe_inputs_string = '';
						$languages = xtc_get_languages();
						for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
							$products_vpe_inputs_string .= xtc_draw_input_field('products_vpe_name[' . $languages[$i]['id'] . ']', '', 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
						}

						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_PRODUCTS_VPE_NAME . '</span>' . $products_vpe_inputs_string);
						$contents[] = array('text' => '<span class="options-title">' . TEXT_SET_DEFAULT . '</span><div class="control-group" data-gx-widget="checkbox">' . xtc_draw_checkbox_field('default', 'on') . '</div>');
						break;
					case 'edit':
						$formAction = xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id  . '&action=save');
						$formIsEditable = true;

						$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_UPDATE . '"/>';
						$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id) . '">' . BUTTON_CANCEL . '</a>';

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_PRODUCTS_VPE . '</b>');
						$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

						$products_vpe_inputs_string = '';
						$languages = xtc_get_languages();
						for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
							$products_vpe_inputs_string .= xtc_draw_input_field('products_vpe_name[' . $languages[$i]['id'] . ']', '', 'class="icon-input" data-gx-widget="icon_input" data-icon="'. DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image'] .'"');
						}

						$contents[] = array('text' => '<span class="options-title">' . TEXT_INFO_PRODUCTS_VPE_NAME . '</span>' . $products_vpe_inputs_string);
						if (DEFAULT_PRODUCTS_VPE_ID != $oInfo->products_vpe_id) $contents[] = array('text' => '<div class="control-group" data-gx-widget="checkbox">' . xtc_draw_checkbox_field('default', 'on') . '</div>' . TEXT_SET_DEFAULT);
						break;

					case 'delete':
						$formAction = xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id  . '&action=deleteconfirm');
						$formIsEditable = true;

						if($remove_status)
						{
							$buttons = '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
							$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id) . '">' . BUTTON_CANCEL . '</a>';
						}

						$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCTS_VPE . '</b>');

						$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
						$contents[] = array('text' => '<br /><b>' . $oInfo->products_vpe_name . '</b>');
						break;

					default:
						if (is_object($oInfo)) {
							$editButton = '<a class="btn btn-primary btn-edit" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
							$deleteButton = '<a class="btn btn-delete" href="' . xtc_href_link(FILENAME_PRODUCTS_VPE, 'page=' . $_GET['page'] . '&oID=' . $oInfo->products_vpe_id . '&action=delete') . '">' . BUTTON_DELETE . '</a>';
							$buttons = $editButton . $deleteButton;

							$heading[] = array('text' => '<b>' . $oInfo->products_vpe_name . '</b>');

							$products_vpe_inputs_string = '';
							$languages = xtc_get_languages();
							for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
								$products_vpe_inputs_string .= '<br />' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . ' ' . xtc_get_products_vpe_name($oInfo->products_vpe_id, $languages[$i]['id']);
							}

							$contents[] = array('text' => $products_vpe_inputs_string);
						}
						break;
				}

			$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
			$configurationBoxContentView->setOldSchoolHeading($heading);
			$configurationBoxContentView->setOldSchoolContents($contents);
			$configurationBoxContentView->setFormAttributes($formAttributes);
			$configurationBoxContentView->set_content_data('buttons', $buttons);
			$configurationBoxContentView->setFormEditable($formIsEditable);
			$configurationBoxContentView->setFormAction($formAction);
			echo $configurationBoxContentView->get_html();
			?>
		</div>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
