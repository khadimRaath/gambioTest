<?php

/* -----------------------------------------------------------------------------------------
   * $Id: yatego.php,v 1.1 2008/03/16 15:31:09 tobias Exp $
   * Export der CSV-Datei für Yatego
   * verfügbare GET-Parameter:
   * action := [export] Export der CSV-Datei
   * mode := [download|zip] Datei wird in Browser ausgegeben oder als gzip auf Server
   * gespeichert.
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');
include (DIR_WS_MODULES . 'yatego/CYExportYatego.php');

switch($_GET['action']) {
	case 'export':
	// Instanz von CYExportYatego
	$yatego = new CYExportYatego($_GET['mode']);
	// alles exportieren
	//$yatego->exportDiscountSets();
	//$yatego->exportDiscounts();
	$yatego->exportCategories();
	$yatego->exportVariantSets();
	$yatego->exportVariants();
	$yatego->exportArticles();
	$yatego->exportStocks();
	break;
	case 'send':
	$yatego = new CYExportYatego($_GET['mode']);
	// alles exportieren
	//$yatego->exportDiscountSets();
	//$yatego->exportDiscounts();
	$yatego->exportCategories();
	$yatego->exportVariantSets();
	$yatego->exportVariants();
	$yatego->exportArticles();
	$yatego->exportStocks();
	gzclose($yatego->fileHandle);
	$yatego->sendData();
	break;
	default:
	xtc_redirect(xtc_href_link(FILENAME_DEFAULT, '', 'SSL'));
}
?>