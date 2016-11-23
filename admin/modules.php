<?php
/* --------------------------------------------------------------
   modules.php 2016-07-14
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
   (c) 2002-2003 osCommerce(modules.php,v 1.45 2003/05/28); www.oscommerce.com 
   (c) 2003	 nextcommerce (modules.php,v 1.23 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: modules.php 1060 2005-07-21 18:32:58Z mz $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

require('includes/application_top.php');

// include needed functions (for modules)

//Eingefügt um Fehler in CC Modul zu unterdrücken. 
require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');
$xtPrice = new xtcPrice($_SESSION['currency'],'');

switch ($_GET['set']) {
	case 'shipping':
		$module_type = 'shipping';
		$module_directory = DIR_FS_CATALOG_MODULES . 'shipping/';
		$module_key = 'MODULE_SHIPPING_INSTALLED';
		define('HEADING_TITLE', HEADING_TITLE_MODULES_SHIPPING);
		break;

	case 'ordertotal':
	case 'order_total':
		$module_type = 'order_total';
		$module_directory = DIR_FS_CATALOG_MODULES . 'order_total/';
		$module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
		define('HEADING_TITLE', HEADING_TITLE_MODULES_ORDER_TOTAL);
		break;

	case 'payment':
	default:
		$module_type = 'payment';
		$module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
		$module_key = 'MODULE_PAYMENT_INSTALLED';
		define('HEADING_TITLE', HEADING_TITLE_MODULES_PAYMENT);
		if (isset($_GET['error'])) {
			$messageStack->add($_GET['error'], 'error');
		}
		PayPalDeprecatedCheck::ppDeprecatedCheck($messageStack);
		break;
}

// BOF GM_MOD
require_once(DIR_FS_ADMIN . 'includes/gm/classes/GMModulesManager.php');
require_once(DIR_FS_ADMIN . 'includes/gm/gm_modules/gm_modules_structure.php');
$coo_module_manager = new GMModuleManager($module_type, $t_show_installed_modules_menu, $t_display_installed_modules, $t_show_missing_modules_menu, $t_display_missing_modules_menu, $t_ignore_files_array);
// EOF GM_MOD		

switch ($_GET['action']) {
	case 'save':
		if(isset($_POST['configuration']) && is_array($_POST['configuration']))
		{
			while (list($key, $value) = each($_POST['configuration'])) {
				if(preg_match('/(MODULE_)\w*(_ALLOWED|_COUNTRIES_\d+)/i', $key)){
					$value = preg_replace('/[^A-Za-z,]/', '', $value);
					$value = strtoupper($value);
					$value = trim($value, ',');
				}
				
				if(preg_match('/MODULE_PAYMENT_COD_UPPER_LIMIT/', $key) && !empty($value))
				{
					$value = preg_replace('/[a-zA-Z]/', '', $value);
					$value = number_format((double)str_replace(',', '.', $value), 2, '.', '');
				}

				if(is_array($value))
				{
					$value = implode('|', $value);
				}

				$key = xtc_db_input(addslashes($key));
				$value = xtc_db_input(addslashes($value));

				switch($key)
				{
					case 'MODULE_ORDER_TOTAL_GV_INC_SHIPPING' :
					case 'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING' :
						xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value
						             . "' where configuration_key IN ('MODULE_ORDER_TOTAL_GV_INC_SHIPPING', 'MODULE_ORDER_TOTAL_COUPON_INC_SHIPPING')");
						break;
				}
				if(preg_match('/MODULE_[A-Z]*_[A-Z0-9_]*_ALIAS/', $key))
				{
					if(trim($value) === '')
					{
						xtc_db_query('DELETE FROM `configuration` WHERE `configuration_key` = "' . $key . '"');
					}
					else
					{
						$query =
							'REPLACE INTO configuration (`configuration_key`, `configuration_value`) VALUES ("' . $key
							. '", "' . xtc_db_prepare_input($value) . '")';
						xtc_db_query($query);
					}
				}
				else
				{
					xtc_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value
					             . "' where configuration_key = '" . $key . "'");
				}
			}
			// BOF GM_MOD:
			$coo_module_manager->save_sort_order($coo_module_manager->get_modules_installed());
		}

		xtc_redirect(xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']));
		break;

	case 'install':
	case 'remove':
		$file_extension = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '.'));
		$class = basename($_GET['module']);
		if (file_exists($module_directory . $class . $file_extension)) {
			include($module_directory . $class . $file_extension);
			$module = new $class(0);
			if ($_GET['action'] == 'install') {
				// clean up:
				$module->remove();
				$module->install();
			} elseif ($_GET['action'] == 'remove') {
				$module->remove();
			}
		}

		xtc_redirect(xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class));
		break;
}
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html <?php echo HTML_PARAMS; ?>>
		<head>
			<meta http-equiv="x-ua-compatible" content="IE=edge">
			<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
			<title><?php echo TITLE; ?></title>
			<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		</head>
		<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
			<!-- header //-->
			<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
			<script type="text/javascript" src="html/assets/javascript/legacy/gm/gm_modules.js"></script>
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
					<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
							<tr class="hidden">
								<td>
									<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/module.png); float: left;"><?php echo HEADING_TITLE; ?></div>

									<?php
									if($_GET['set']=='shipping')
										//echo '
										//	<div align="right">
										//		<a class="button" onClick="this.blur();" href="' . xtc_href_link(FILENAME_CONFIGURATION, 'gID=7') . '">
										//			' . BOX_CONFIGURATION_7 . '
										//		</a>
										//	</div>';
									
										echo '
											<table>
												<tr>
													<td class="dataTableHeadingContent">
														' . BOX_SHIPPING . '
													</td>
													<td class="dataTableHeadingContent">
														<a href="configuration.php?gID=7">
															' . BOX_CONFIGURATION_7 . '
														</a>
													</td>
												</tr>
											</table>
										';
									?>
									<br />
								</td>
							</tr>
							<tr>
								<td>
									<table border="0" width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top">
												<?php
												// BOF GM_MOD
												?>
												<div class="gx-container">
													<table data-gx-compatibility="modules/modules_overview" class="gx-modules-table left-table <?php echo htmlentities_wrapper($_GET['set']); ?>" cellpadding="0" cellspacing="0" width="100%">
														<tr class="dataTableHeadingRow">
															<td class="dataTableHeadingContent" style="width: 12px"></td>
															<td class="dataTableHeadingContent" style="width: 300px"><?php echo TABLE_HEADING_MODULES ?></td>
															<td class="dataTableHeadingContent" style="width: 130px"></td><!-- Module logo -->
															<td class="dataTableHeadingContent" style="width: 200px"><?php echo TABLE_HEADING_FILENAME ?></td>
															<td class="dataTableHeadingContent" style="width: 72px"><?php echo TABLE_HEADING_STATUS ?></td>
															<td class="dataTableHeadingContent" style="width: 96px"><?php echo TABLE_HEADING_SORT_ORDER ?></td>
															<td class="dataTableHeadingContent"></td>
														</tr>
	
														<?php
														$coo_module_manager->repair();
														$coo_module_manager->show_modules($t_gm_structure_array);
	
														if(!empty($_GET['module']))
														{
															$mInfo = new objectInfo($coo_module_manager->get_module_data_by_name($_GET['module']));
														}
														?>
	
													</table>
												</div>
												<?php
												// EOF GM_MOD
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
				if(isset($_GET['module']) && !empty($_GET['module']))
				{
					$heading = array();
					$contents = array();
	
					$languageTextManager = MainFactory::create_object('LanguageTextManager', true);
					$formIsEditable = false;
	
					switch ($_GET['action']) {
						case 'edit':
							// the code below handles the modules alias.
							if($_GET['set'] === 'payment' || $_GET['set'] === 'shipping')
							{
								$moduleType       = $_GET['set'];
								$moduleName       = $_GET['module'];
								$configurationKey =
									'MODULE_' . strtoupper($moduleType) . '_' . strtoupper($moduleName) . '_ALIAS';

								$query =
									'SELECT `configuration_value` FROM `configuration` WHERE `configuration_key` = "'
									. $configurationKey . '"';
								$result = xtc_db_query($query);

								$oldAlias = xtc_db_num_rows($result) > 0 ? xtc_db_fetch_array($result)['configuration_value'] : '';

								$keys = '<span class="options-title">' . ORDERS_OVERVIEW_ALIAS_TITLE . '</span>';
								$keys .= '<input type="text" name="configuration[' . $configurationKey . ']" value="'
								         . $oldAlias . '" />';
							}
							else
							{
								$keys = '';
							}

							$formIsEditable = true;
							reset($mInfo->keys);

							while (list($key, $value) = each($mInfo->keys)) {
								if(preg_match('/_ALIAS$/', $key))
								{
									continue;
								}
								$keys .= '<span class="options-title">' . $value['title'] . '</span>' .  $value['description'];
								if ($value['set_function']) {
									eval('$keys .= ' . $value['set_function'] . "'" . addslashes($value['value']) . "', '" . addslashes($key) . "');");
								} else {
									$keys .= xtc_draw_input_field('configuration[' . $key . ']', $value['value']);
								}
							}
	
							$heading[] = array('text' => strip_tags($mInfo->title));
	
							$contents[] = array('text' => $keys);
							if($_GET['module'] == 'moneyorder')
							{
								$buttons = '<button id="moneyorder_submit" class="btn btn-primary">' . BUTTON_UPDATE . '</button>';
								$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']) . '">' . BUTTON_CANCEL . '</a>';
							}
							else
							{
								$buttons = '<button class="btn btn-primary" onClick="this.blur();">' . BUTTON_UPDATE . '</button>';
								$buttons .= '<a class="button btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']) . '">' . BUTTON_CANCEL . '</a>';
							}
							break;
	
						default:
							if($mInfo->status == '1')
							{
								$buttons = '<a class="btn btn-edit btn-primary" href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=edit') . '">' . BUTTON_EDIT . '</a>';
								$buttons .= '<a href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=remove') . '" class="btn">' . htmlspecialchars_wrapper($languageTextManager->get_text('uninstall', 'buttons')) . '</a>';
							}
							else
							{
								$buttons = '<a href="' . xtc_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $mInfo->code . '&action=install') . '" class="btn btn-primary">' . htmlspecialchars_wrapper($languageTextManager->get_text('install', 'buttons')) . '</a>';
							}
	
							$heading[] = array('text' => '<b>' . strip_tags($mInfo->title) . '</b><br/>');
	
							if ($mInfo->status == '1') {
								$keys = '';
								reset($mInfo->keys);
								while (list(, $value) = each($mInfo->keys)) {
									$keys .= '<b>' . $value['title'] . '</b><br />';
									if ($value['use_function']) {
										$use_function = $value['use_function'];
										if (strpos($use_function, '->') !== false) {
											$class_method = explode('->', $use_function);
											if (!is_object(${$class_method[0]})) {
												include(DIR_WS_CLASSES . $class_method[0] . '.php');
												${$class_method[0]} = new $class_method[0]();
											}
											$keys .= xtc_call_function($class_method[1], $value['value'], ${$class_method[0]});
										} else {
											$keys .= xtc_call_function($use_function, $value['value']);
										}
									} else {
										if(strlen_wrapper($value['value']) > 30) {
											$keys .=  substr($value['value'],0,30) . ' ...';
										} else {
											$keys .=  $value['value'];
										}
									}
									$keys .= '<br/><br/>';
								}
								// handles display of alias names in the module configuration
								if($_GET['set'] === 'payment' || $_GET['set'] === 'shipping')
								{
									$moduleType       = $_GET['set'];
									$moduleName       = $_GET['module'];
									$aliasConfigurationKey =
										'MODULE_' . strtoupper($moduleType) . '_' . strtoupper($moduleName) . '_ALIAS';

									$query =
										'SELECT `configuration_value` FROM `configuration` WHERE `configuration_key` = "'
										. $aliasConfigurationKey . '"';
									$result = xtc_db_query($query);

									$oldAlias =
										xtc_db_num_rows($result)
										> 0 ? xtc_db_fetch_array($result)['configuration_value'] : TEXT_NONE;

									$content = '<b>' . ORDERS_OVERVIEW_ALIAS_TITLE . '</b>';
									$content .= '<br/><span>' . $oldAlias . '</span>';
									$contents[] = array('text' => $content);
									$contents[] = array('text' => ''); // added empty text area to increase the margin
								}
								$contents[] = array('text' => '' . $mInfo->description);
								$contents[] = array('text' => $keys);
							} else {
								$contents[] = array('text'  => $mInfo->description);
							}
							break;
					}
	
					$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
					$configurationBoxContentView->setOldSchoolHeading($heading);
					$configurationBoxContentView->setOldSchoolContents($contents);
					$configurationBoxContentView->set_content_data('buttons', $buttons);
					$configurationBoxContentView->setFormEditable($formIsEditable);
					$configurationBoxContentView->setFormAction(xtc_href_link(FILENAME_MODULES . '?set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=save'));
					echo $configurationBoxContentView->get_html();
				}
				?>
			</div>
		</body>
	</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>