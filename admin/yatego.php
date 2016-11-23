<?php
/* --------------------------------------------------------------
   yatego.php 2015-09-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   * $Id: yatego.php,v 1.1 2008/03/16 15:32:52 tobias Exp $
   * aufrufbare Seite im Adminbereich des Shops
   * Einstellungen, etc. werden hier vorgenommen
   * verfügbare GET-Parameter:
   * section := [export|preferences|categorymapping|
   * selectArticles] Auswahl der Seite
   --------------------------------------------------------------*/  

	require('includes/application_top.php');

	AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');
	
	if(!isset($_GET['section']))	
	{
		$_GET['section'] = 'export';
	}
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<html <?php echo HTML_PARAMS; ?>>
		<head>
			<meta http-equiv="x-ua-compatible" content="IE=edge">   
			<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>"> 
			<title>Yatego-Export</title>
			<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>html/assets/styles/legacy/stylesheet.css">		
		</head>
		<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF">

			<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

			<table border="0" width="100%" cellspacing="2" cellpadding="2">
				<tr>
					<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
						<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
							<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						</table>
					</td>
					<td class="boxCenter" width="100%" valign="top">
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)">Yatego-Export</div>
									<br />
									<table border="0" width="100%" cellspacing="0" cellpadding="0">
										<tr>
											<td width="120" class="dataTableHeadingContent">
												<a href="<?php echo xtc_href_link('yatego.php').'?section=export'; ?>">Export</a>
											</td>
											<td width="120" class="dataTableHeadingContent">
												<a href="<?php echo xtc_href_link('yatego.php').'?section=preferences'; ?>">Einstellungen</a>
											</td>
											<td width="120" class="dataTableHeadingContent">
												<a href="<?php echo xtc_href_link('yatego.php').'?section=categorymapping'; ?>">Kategorien-Mapping</a>
											</td>
											<td class="dataTableHeadingContent"  style="border-right: 0px;">
												<a href="<?php echo xtc_href_link('yatego.php').'?section=selectArticles'; ?>">Artikel ausw&auml;hlen</a>
											</td>
										</tr>
									</table>
									<table border="0" width="100%" cellspacing="0" cellpadding="0" class="gm_border dataTableRow">
										<tr>
											<td valign="top" class="main">												
												<div id="gm_box_content">
													<?php
													/* --------------------------------------------------------------
													   * Installation der benötigten Einstellungen und Tabellen
													   * auf der Datenbank
													   * configuration ist die Standard-Tabelle im XT:Commerce für
													   * globale Einstellungen
													   * yatego_category_mapping speichert die Zuordnung von den
													   * XT:Commerce Kategorien zu den Yatego Kategorien
													   * yatego_articles speichert die Artikel, die zum Export
													   * zu Yatego ausgewählt wurden
													   --------------------------------------------------------------*/

													$result = xtc_db_query("SELECT * FROM configuration WHERE configuration_key = 'YATEGO_LANGUAGE'");
													$row = mysqli_fetch_row($result);
													if ($row === false) $doConfiguration = true;
													else $doConfiguration = false;

													$yatego_tables = array();
													$result = xtc_db_query("SHOW TABLES LIKE 'yatego%'");
													while ($row = mysqli_fetch_row($result)) {
														$yatego_tables[] = $row[0];
													}

													if(!in_array('yatego_articles', $yatego_tables) || !in_array('yatego_category_mapping', $yatego_tables) || $doConfiguration) {
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_CURRENCY','EUR',6,1,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_CUSTOMER_STATUS','0',6,2,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_LANGUAGE','2',6,3,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_USERNAME','',6,4,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_PASSWORD','',6,5,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_QUANTITIES','false',6,6,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_EXPORTALL','false',6,6,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_H2LONGDESC','false',6,6,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_GENSHORTDESC','false',6,6,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_GENPACKAGESIZE','false',6,6,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_IMPORTMODE','1',6,6,NOW(),NOW())");
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_TOPSELLER','50',6,6,NOW(),NOW())");	
														$result = xtc_db_query('DROP TABLE IF EXISTS `yatego_category_mapping`');
														$result = xtc_db_query('DROP TABLE IF EXISTS `yatego_articles`');	
														
														$result = xtc_db_query('CREATE TABLE `yatego_category_mapping` (
															`category_mapping_id` INT NOT NULL AUTO_INCREMENT ,
															`shop_category` TEXT  ,
															`yatego_category` TEXT  ,
															PRIMARY KEY (`category_mapping_id`)
															)');

														$result = xtc_db_query('CREATE TABLE `yatego_articles` (
															`product_id` INT NOT NULL,
															`export_yatego` TINYINT,
															`yatego_top` TINYINT,
															PRIMARY KEY (`product_id`)
															)');
													}

													$result2 = xtc_db_query("SELECT * FROM configuration WHERE configuration_key Like 'YATEGO_%'");
													while ($row2 = mysqli_fetch_row($result2)) 
													{
														$configuration_key[] = $row2[1];
													}
													if(!in_array('YATEGO_TOPSELLER', $configuration_key))
													{
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_TOPSELLER','50',6,6,NOW(),NOW())");		
													}
													if(!in_array('YATEGO_GENPACKAGESIZE', $configuration_key))
													{
														$result = xtc_db_query("INSERT INTO configuration (configuration_key,configuration_value,configuration_group_id,sort_order,last_modified,date_added) VALUES ('YATEGO_GENPACKAGESIZE','false',6,6,NOW(),NOW())");
													}

													/* --------------------------------------------------------------
													   * Anzeige der Einstellungen
													   * gewählte Optionen werden per POST übertragen
													   * Klasse CYYatPref wird eingebunden
													   * Einstellungen werden dort über set-Methoden gespeichert
													   --------------------------------------------------------------*/
													if($_GET['section']=="preferences") {
														include(DIR_WS_INCLUDES . 'modules/yatego/CYYatPref.php');
														$pref = new CYYatPref();
														if(isset($_POST['yatego_currency'])) {
															if($pref->setCurrency($_POST['yatego_currency'])) {
																echo "<p>W&auml;hrung ge&auml;ndert</p>";
															}
														}
														if(isset($_POST['yatego_customer_status'])) {
															if($pref->setCustomerStatus($_POST['yatego_customer_status'])) {
																echo "<p>Kundengruppe ge&auml;ndert</p>";
															}
														}
														if(isset($_POST['yatego_language'])) {
															if($pref->setLanguage($_POST['yatego_language'])) {
																echo "<p>Sprache ge&auml;ndert</p>";
															}
														}
														if(isset($_POST['yatego_username'])) {
															if($pref->setUsername($_POST['yatego_username'])) {
																echo "<p>Yatego Benutzername ge&auml;ndert</p>";
															}
														}
														if(isset($_POST['yatego_password']) && $_POST['yatego_password'] != '********') {
															if($pref->setPassword($_POST['yatego_password'])) {
																echo "<p>Yatego Passwort ge&auml;ndert</p>";
															}
														}
														if(!empty($_POST)) {
															if($pref->setQuantities($_POST['yatego_quantities']=='true'?'true':'false')) {
																echo "<p>Exportieren der Lagerbest&auml;nde ge&auml;ndert</p>";
															}
														}
														if(!empty($_POST)) {
															if($pref->setExportAll($_POST['yatego_exportall']=='true'?'true':'false')) {
																echo "<p>Exportieren der Artikel ge&auml;ndert</p>";
															}
														}
														if(!empty($_POST)) {
															if($pref->setH2longdesc($_POST['yatego_h2longdesc']=='true'?'true':'false')) {
																echo "<p>Setzen des Artikelnamens ge&auml;ndert</p>";
															}
														}
														if(!empty($_POST)) {
															if($pref->setGenshortdesc($_POST['yatego_genshortdesc']=='true'?'true':'false')) {
																echo "<p>Generieren der Kurzbeschreibung ge&auml;ndert</p>";
															}
														}
														if(!empty($_POST)) {
															if($pref->setGenpackagesize($_POST['yatego_genpackagesize']=='true'?'true':'false')) {
																echo "<p>Generieren der Grundpreis ge&auml;ndert</p>";
															}
														}
														$pref->display();
													}
													?>
													<?php
													/* --------------------------------------------------------------
													   * Anzeige der Exportdateien
													   * verfügbare GET-Parameter:
													   * action := [send] direktes übertragen der Datei zu Yatego
													   --------------------------------------------------------------*/

													if($_GET['section']=="export") {
													?>
													<ul>
													<li><a href="../yatego.php?action=export&amp;mode=download">Yatego CSV-Datei herunterladen</a></li>
													<li><a href="../yatego.php?action=send&amp;mode=zip">Yatego CSV-Datei &uuml;bertragen</a></li>
													</ul>
													<?php
													if($_GET['action']=="send") {
														require(DIR_FS_CATALOG.DIR_WS_CLASSES.'xtcPrice.php');
														require(DIR_FS_CATALOG.DIR_WS_MODULES . 'yatego/CYExportYatego.php');
														define('DIR_WS_POPUP_IMAGES', DIR_WS_CATALOG_POPUP_IMAGES);
														define('FILENAME_PRODUCT_INFO', 'product_info.php');
														$yatego = new CYExportYatego('zip');
														$yatego->exportCategories();
														$yatego->exportVariantSets();
														$yatego->exportVariants();
														$yatego->exportArticles();
														$yatego->exportStocks();
														gzclose($yatego->fileHandle);
														$yatego->sendData();
													}
													}
													?>
													<?php
													/* --------------------------------------------------------------
													   * Anzeige des Kategorien-Mappings
													   * Klasse CYYatMap wird eingebunden. Weitere Verarbeitung
													   * erfolgt dort
													   --------------------------------------------------------------*/

														if($_GET['section']=="categorymapping") {
															include(DIR_WS_INCLUDES . 'modules/yatego/CYYatMap.php');
															$mapping = new CYYatMap();
															$mapping->display();
														}

													/* --------------------------------------------------------------
													   * Anzeige der Artikelauswahl
													   * Klasse CYYatArtSel wird eingebunden. Weitere Verarbeitung
													   * erfolgt dort
													   *    * verfügbare GET-Parameter:
													   * selectArticles := [all|none] Auswahl aller / keines Artikel
													   * page := [\d*] pro Seite werden zehn Artikel angezeigt
													   * \d ist die anzuzeigende Seite
													   * category := [ID der XT:Commerce Kategorie|all] Anzeige auf
													   * ID der XT:Commerce Kategorie begrenzen
													   --------------------------------------------------------------*/
														if($_GET['section']=="selectArticles") 
														{
															include(DIR_WS_INCLUDES . 'modules/yatego/CYYatArtSel.php');
															$artsel = new CYYatArtSel();				
															if(isset($_GET['selectCategoryArticles']) && isset($_GET['category']) && $_GET['category'] != 'all')
															{
																if($_GET['selectCategoryArticles']=='yes' ) 
																{
																	$artsel->display(isset($_GET['page'])?$_GET['page']:0, (int)$_GET['category'], 1);
																}
																if($_GET['selectCategoryArticles']=='no' ) 
																{
																	$artsel->display(isset($_GET['page'])?$_GET['page']:0, (int)$_GET['category'], 2);
																}
															}
															else
															{
																if($_GET['selectArticles']=='all') {
																	$artsel->selectAllArticles();
																}
																if($_GET['selectArticles']=='none') {
																	$artsel->selectNoArticles();
																}
																if($_GET['selectArticles']=='topseller') 
																{
																	if(!isset($_GET['topseller'])) 
																	{
																		
																		$_GET['topseller'] = YATEGO_TOPSELLER;
																	}
																	else
																	{
																		updateTopSeller($_GET['topseller']);
																	}
																	
																	$artsel->display(isset($_GET['page'])?$_GET['page']:0, 'all', '', $_GET['topseller'], $_GET['selectall']);
																}
																else
																{
																	$artsel->display(isset($_GET['page'])?$_GET['page']:0, isset($_GET['category'])?(int)$_GET['category']:'all');
																}
															}		
														}
														
														function updateTopSeller($amount) 
														{
															xtc_db_query("UPDATE configuration SET configuration_value='".xtc_db_input($amount). "' WHERE configuration_key='YATEGO_TOPSELLER'");	
															return true;
														}
													?>												
												</div>
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
	<?php require(DIR_WS_INCLUDES . 'application_bottom.php');