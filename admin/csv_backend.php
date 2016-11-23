<?php
/* --------------------------------------------------------------
   csv_backend.php 2015-09-28 gm
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
   (c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: csv_backend.php 1030 2005-07-14 20:22:32Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

	require('includes/application_top.php');
	require(DIR_WS_CLASSES . 'import.php');
	require_once(DIR_FS_INC . 'xtc_format_filesize.inc.php');

	define('FILENAME_CSV_BACKEND','csv_backend.php');

	switch ($_GET['action']) {

		case 'upload':
			$upload_file = xtc_db_prepare_input($_POST['file_upload']);
			if (($upload_file = &xtc_try_upload('file_upload',DIR_FS_CATALOG.'import/') )) {
				$$upload_file_name = $upload_file->filename;
			}
		break;

		case 'import':
			if($_POST['gm_delete_products'] == '1'){
				// mysql_query() to avoid warning messages if table does not exist
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_1");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_2");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_3");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_4");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_5");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_6");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_7");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_8");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_9");
				@mysqli_query($GLOBALS["___mysqli_ston"], "TRUNCATE personal_offers_by_customers_status_10");
				@xtc_db_query("TRUNCATE products");
				@xtc_db_query("TRUNCATE products_description");
				@xtc_db_query("TRUNCATE products_graduated_prices");
				@xtc_db_query("TRUNCATE products_to_categories");
				@xtc_db_query("TRUNCATE products_google_categories");
				@xtc_db_query("TRUNCATE products_item_codes");
				@xtc_db_query("TRUNCATE additional_field_values");
				@xtc_db_query("TRUNCATE additional_field_value_descriptions");
			}
			if($_POST['gm_delete_images'] == '1'){
				@xtc_db_query("TRUNCATE products_images");				
			}
			if($_POST['gm_delete_categories'] == '1'){
				@xtc_db_query("TRUNCATE categories");
				@xtc_db_query("TRUNCATE categories_description");				
			}
			if($_POST['gm_delete_manufacturers'] == '1'){
				@xtc_db_query("TRUNCATE manufacturers");				
			}
			if($_POST['gm_delete_reviews'] == '1'){				
				@xtc_db_query("TRUNCATE reviews");			
				@xtc_db_query("TRUNCATE reviews_description");				
			}
			if($_POST['gm_delete_attributes'] == '1'){
				@xtc_db_query("TRUNCATE products_attributes");
				@xtc_db_query("TRUNCATE products_attributes_download");
			}
			if($_POST['gm_delete_xsell'] == '1'){
				@xtc_db_query("TRUNCATE products_xsell");
				@xtc_db_query("TRUNCATE products_xsell_grp_name");
			}
			if($_POST['gm_delete_specials'] == '1'){
				@xtc_db_query("TRUNCATE specials");
			}
			$handler = new xtcImport($_POST['select_file']);
			$mapping=$handler->map_file($handler->generate_map());
			$import=$handler->import($mapping);
				
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_1");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_2");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_3");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_4");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_5");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_6");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_7");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_8");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_9");
			@mysqli_query($GLOBALS["___mysqli_ston"], "OPTIMIZE TABLE personal_offers_by_customers_status_10");
			@xtc_db_query("OPTIMIZE TABLE products");
			@xtc_db_query("OPTIMIZE TABLE products_item_codes");
			@xtc_db_query("OPTIMIZE TABLE products_description");
			@xtc_db_query("OPTIMIZE TABLE products_graduated_prices");
			@xtc_db_query("OPTIMIZE TABLE products_to_categories");
			@xtc_db_query("OPTIMIZE TABLE products_images");				
			@xtc_db_query("OPTIMIZE TABLE categories");
			@xtc_db_query("OPTIMIZE TABLE categories_description");				
			@xtc_db_query("OPTIMIZE TABLE manufacturers");				
			@xtc_db_query("OPTIMIZE TABLE reviews");			
			@xtc_db_query("OPTIMIZE TABLE reviews_description");				
			@xtc_db_query("OPTIMIZE TABLE products_attributes");
			@xtc_db_query("OPTIMIZE TABLE products_attributes_download");
			@xtc_db_query("OPTIMIZE TABLE products_xsell");
			@xtc_db_query("OPTIMIZE TABLE products_xsell_grp_name");
			@xtc_db_query("OPTIMIZE TABLE specials");
			@xtc_db_query("OPTIMIZE TABLE additional_fields");
			@xtc_db_query("OPTIMIZE TABLE additional_field_descriptions");
			@xtc_db_query("OPTIMIZE TABLE additional_field_values");
			@xtc_db_query("OPTIMIZE TABLE additional_field_value_descriptions");
			
		break;

		case 'export':
			$handler = new xtcExport('export.csv');
			$import=$handler->exportProdFile();
		break;

		case 'save':
			$configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '20' order by sort_order");

			while ($configuration = xtc_db_fetch_array($configuration_query)) {
				xtc_db_query("UPDATE ".TABLE_CONFIGURATION." SET configuration_value='".$_POST[$configuration['configuration_key']]."' where configuration_key='".$configuration['configuration_key']."'");
			}
			xtc_redirect(FILENAME_CSV_BACKEND);
		break;
	}

	$cfg_group_query = xtc_db_query("select configuration_group_title from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_id = '20'");
	$cfg_group = xtc_db_fetch_array($cfg_group_query);
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
	
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
		
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
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<!-- gm_module //-->
								<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)"><?php echo BOX_IMPORT; ?></div>
								<br />									
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
									<tr>
										<td width="120" class="dataTableHeadingContent" style="border-right: 0px;">
											<a href="#" onClick="toggleBox('config');">
												<?php echo CSV_SETUP; ?>
											</a>
										</td>
									</tr>
								</table>
								<?php

								if ($import) {
									if ($import[0])	{
										echo '
											<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow  messageStackSuccess">
												<tr>
													<td colspan="2" class="main" valign="top">
														<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										';
										// BOF GM_MOD
										if (isset($import[0]['prod_new'])) echo 'Neue Artikel: '.$import[0]['prod_new'].'<br />';
										if (isset($import[0]['cat_new'])) echo 'Neue Kategorien: '.$import[0]['cat_new'].'<br />';
										if (isset($import[0]['prod_upd'])) echo 'Aktualisierte Artikel: '.$import[0]['prod_upd'].'<br />';
										if (isset($import[0]['cat_upd'])) echo 'Aktualisierte Kategorien: ' .$import[0]['cat_upd'].'<br />';
										if (isset($import[0]['cat_touched'])) echo 'Betroffene Kategorien: '.$import[0]['cat_touched'].'<br />';
										if (isset($import[0]['prod_exp'])) echo 'Exportierte Artikel: '.$import[0]['prod_exp'].'<br />';
										// BOF GM_MOD
										if (isset($import[2])) echo $import[2];
										
										echo '
														</font>
													</td>
												</tr>
											</table>
											';
									}

									if (isset($import[1]) && $import[1][0]!='') {
										echo '
											<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow messageStackError">
												<tr>
													<td colspan="2" class="main" valign="top">
														<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
										';
										for ($i=0;$i<count($import[1]);$i++) {
											echo $import[1][$i].'<br />';
										}


										echo '
														</font>
													</td>
												</tr>
											</table>
											';
									}
								}
								elseif(isset($handler) && empty($handler->message) == false)
								{
									echo '
											<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow messageStackError">
												<tr>
													<td colspan="2" class="main" valign="top" style="font-family: Verdana, Arial, Helvetica, sans-serif">
														' . $handler->message . '
													</td>
												</tr>
											</table>
											';
								}
								?>
								<div id="config" class="longDescription">
								<table style="padding:0px;" border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
								<!-- bof options -->
									<tr>
										<td colspan="2" class="main" valign="top">											
											<?php echo xtc_draw_form('configuration', FILENAME_CSV_BACKEND, 'gID=20&action=save'); ?>
												<table width="100%"  border="0" cellspacing="0" cellpadding="4">
											<?php
											  $configuration_query = xtc_db_query("select configuration_key,configuration_id, configuration_value, use_function,set_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '20' order by sort_order");
											  $gm_row_cnt = 0;
											  while ($configuration = xtc_db_fetch_array($configuration_query)) {
												// BOF GM_MOD
												if($configuration['configuration_key'] != 'COMPRESS_EXPORT'){
												// EOF GM_MOD
												if(($gm_row_cnt++ % 2) == 0) $gm_row_bg='#d6e6f3'; else $gm_row_bg='#f7f7f7';
												if ($_GET['gID'] == 6) {
												  switch ($configuration['configuration_key']) {
													case 'MODULE_PAYMENT_INSTALLED':
													  if ($configuration['configuration_value'] != '') {
														$payment_installed = explode(';', $configuration['configuration_value']);
														for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
														  include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $payment_installed[$i]);
														}
													  }
													  break;

													case 'MODULE_SHIPPING_INSTALLED':
													  if ($configuration['configuration_value'] != '') {
														$shipping_installed = explode(';', $configuration['configuration_value']);
														for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
														  include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $shipping_installed[$i]);
														}
													  }
													  break;

													case 'MODULE_ORDER_TOTAL_INSTALLED':
													  if ($configuration['configuration_value'] != '') {
														$ot_installed = explode(';', $configuration['configuration_value']);
														for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
														  include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $ot_installed[$i]);
														}
													  }
													  break;
												  }
												}
												if (xtc_not_null($configuration['use_function'])) {
												  $use_function = $configuration['use_function'];
												  if (strpos($use_function,'->') !== false) {
													$class_method = explode('->', $use_function);
													if (!is_object(${$class_method[0]})) {
													  include(DIR_WS_CLASSES . $class_method[0] . '.php');
													  ${$class_method[0]} = new $class_method[0]();
													}
													$cfgValue = xtc_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
												  } else {
													$cfgValue = xtc_call_function($use_function, $configuration['configuration_value']);
												  }
												} else {
												  $cfgValue = $configuration['configuration_value'];
												}

												if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
												  $cfg_extra_query = xtc_db_query("select configuration_key,configuration_value, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
												  $cfg_extra = xtc_db_fetch_array($cfg_extra_query);

												  $cInfo_array = xtc_array_merge($configuration, $cfg_extra);
												  $cInfo = new objectInfo($cInfo_array);
												}
												if ($configuration['set_function']) {
													eval('$value_field = ' . $configuration['set_function'] . '"' . htmlspecialchars_wrapper($configuration['configuration_value']) . '");');
												  } else {
													$value_field = xtc_draw_input_field($configuration['configuration_key'], $configuration['configuration_value'],'size=40');
												  }
											   // add

											   if (strpos_wrapper($value_field,'configuration_value') !== false) $value_field=str_replace('configuration_value',$configuration['configuration_key'],$value_field);

													echo '
													<table width="100%" border="0" cellspacing="0" cellpadding="4" style="border-bottom: 1px dotted #5a5a5a">
													<tr valign="top" bgcolor="'.$gm_row_bg.'">
													<td class="dataTableContent_gm" width="300"><b>'.constant(strtoupper_wrapper($configuration['configuration_key'].'_TITLE')).'</b></td>
													<td class="dataTableContent_gm">
													<table width="100%" border="0" cellspacing="0" cellpadding="2">
													<tr>
													<td class="dataTableContent_gm">'.$value_field.'</td>
													</tr>
													</table>
													<br />'.constant(strtoupper_wrapper( $configuration['configuration_key'].'_DESC')).'</td>
													</tr>
													</table>
													';
													// BOF GM_MOD
													}
													else{
														echo xtc_draw_hidden_field($configuration['configuration_key'], 'false');
													}
													// EOF GM_MOD
											  }
											?>
											<br />
											<div style="padding:5px;">
											<?php echo '<input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_SAVE . '"/>'; ?>
											</div>
											</form>											
										</td>
									</tr>
								</table>
								</div>
								<!-- eof options -->
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">						
									<tr>
										<td colspan="2" class="main gm_strong" valign="top">
											<?php echo IMPORT; ?>
										</td>
									</tr>
									<tr>
										<td colspan="2" class="main gm_strong" valign="top">
											&nbsp;
										</td>
									</tr>
									<tr>
										<td width="150" class="main" valign="top">
											<?php echo UPLOAD; ?>
										</td>
										<td class="main" valign="top">
											<?php
												echo xtc_draw_form('upload',FILENAME_CSV_BACKEND,'action=upload','POST','enctype="multipart/form-data"');
												echo xtc_draw_file_field('file_upload');
												echo '<br/><br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_UPLOAD . '"/>';
											?>
											</form>
										</td>
									</tr>

									<tr>
										<td colspan="2" class="main gm_strong" valign="top">
											&nbsp;
										</td>
									</tr>
									<tr>
										<td width="180" class="main" valign="top">
											<?php echo SELECT; ?>
										</td>
										<td class="main" valign="top">
											<?php
												$files=array();
												echo xtc_draw_form('import',FILENAME_CSV_BACKEND,'action=import','POST','enctype="multipart/form-data"');
												if ($dir= opendir(DIR_FS_CATALOG.'import/')){
												while  (($file = readdir($dir)) !==false) {
												// BOF GM_MOD:
												if ($file != '.htaccess' && $file != 'index.html' && $file != '.' && $file != '..' && is_file(DIR_FS_CATALOG.'import/'.$file))
												{
												$size=filesize(DIR_FS_CATALOG.'import/'.$file);
												$files[]=array(
												'id' => $file,
												'text' => $file.' | '.xtc_format_filesize($size));
												}
												}
												closedir($dir);
												}
												echo xtc_draw_pull_down_menu('select_file',$files,'');
												echo '<br/><br/><input type="checkbox" name="gm_delete_products" value="1" />'.GM_CSV_DELETE_PRODUCTS;
												echo '<br/><br/><input type="checkbox" name="gm_delete_images" value="1" />'.GM_CSV_DELETE_IMAGES;
												echo '<br/><br/><input type="checkbox" name="gm_delete_attributes" value="1" />'.GM_CSV_DELETE_ATTRIBUTES;
												echo '<br/><br/><input type="checkbox" name="gm_delete_specials" value="1" />'.GM_CSV_DELETE_SPECIALS;
												echo '<br/><br/><input type="checkbox" name="gm_delete_categories" value="1" />'.GM_CSV_DELETE_CATEGORIES;
												echo '<br/><br/><input type="checkbox" name="gm_delete_manufacturers" value="1" />'.GM_CSV_DELETE_MANUFACTURERS;
												echo '<br/><br/><input type="checkbox" name="gm_delete_reviews" value="1" />'.GM_CSV_DELETE_REVIEWS;
												echo '<br/><br/><input type="checkbox" name="gm_delete_xsell" value="1" />'.GM_CSV_DELETE_XSELL;
												echo '<br/><br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_IMPORT . '"/>';
											?>
											</form>
										</td>
									</tr>
								</table>
								<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
									<tr>
										<td colspan="2" class="main gm_strong" valign="top">
											<?php echo EXPORT; ?>
										</td>
									</tr>
									<tr>
										<td colspan="2" class="main gm_strong" valign="top">
											&nbsp;
										</td>
									</tr>
									<tr>
										<td width="180" class="main" valign="top">
											<?php echo TEXT_EXPORT; ?>
										</td>
										<td class="main" valign="top">
											<?php
											echo xtc_draw_form('export',FILENAME_CSV_BACKEND,'action=export','POST','enctype="multipart/form-data"');
											$content=array();
											$content[]=array('id'=>'products','text'=>TEXT_PRODUCTS);
											echo xtc_draw_pull_down_menu('select_content',$content,'products');
											echo '<br/><br/><input type="submit" class="button" onClick="this.blur();" value="' . BUTTON_EXPORT . '"/>';
											?>
											</form>
											<?php
												if($_GET['action'] == 'export') {
													$url = HTTP_SERVER.DIR_WS_CATALOG.'export/export.csv';
													echo '<br /><br />URL der Exportdatei: <a href="'.$url.'" target="_blank">'.$url.'</a>';
												}
											?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
