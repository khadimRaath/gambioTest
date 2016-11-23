<?php

/* -----------------------------------------------------------------------------------------
   * $Id: CYExportYatego.php,v 1.1 2008/03/16 15:31:31 tobias Exp $
   * Erstellen der CSV-Datei für den Import bei Yatego
   ---------------------------------------------------------------------------------------*/

class CYExportYatego {
	var $language; // Sprache des Exports (Artikelnamen, Beschreibungen, ...)
	var $customerGroup; // Kundengruppe, die zur Preisberechnung verwendet werden soll
	var $currency; // Währung der Preise, die exportiert werden
	var $exportQuantities; // Konfigruationseinstellung, ob Lagerbestände exportiert werden
	var $exportAllProducts; // Konfigruationseinstellung, ob alle Artikel exportiert werden
	var $h2longdesc; // Konfigruationseinstellung, Artikelname in <h2> vor Langbeschreibung
	var $genshortdesc; // Konfigruationseinstellung, ob Kurzbeschreibung generiert wird
	var $genpackagesize; // Konfigruationseinstellung, ob Packungsgröße generiert wird
	var $articleSelect; // SQL-Select für Artikeldatei
	var $variantSetSelect; // SQL-Select für Variantensatzdatei
	var $variantSelect; // SQL-Select für Variantendatei
	var $categoriesSelect; // SQL-Select für Kategoriendatei
	var $stockSelect; // SQL-Select für Lagerdatei
	var $discountSetSelect; // SQL-Select für Rabattsatzdatei
	var $discountSelect; // SQL-Select für Rabattdatei
	var $exportMode; // Erstellungsart der CSV-Datei [download|zip]
	var $fileHandle; // Handle auf die gzip-Datei

	function CYExportYatego($mode) {
		xtc_db_query("SET SQL_BIG_SELECTS=1");
		$this->exportMode = $mode;
		switch($this->exportMode) {
			case 'download':
			header("Cache-Control: no-cache, must-revalidate");
			header('Content-type: application/csv');
			header('Content-Disposition: attachment; filename="yatego.csv"');
			case 'zip':
			if (!$this->fileHandle = gzopen(DIR_FS_DOCUMENT_ROOT.'export/' . 'yatego.gz', "w9")) {
					print "<p>Kann die Datei $this->filename nicht &ouml;ffnen!</p>";
					exit;
			}
			break;
		}
		// allgemeine Initalisierungen
		$this->language = YATEGO_LANGUAGE;
		$this->customerGroup = YATEGO_CUSTOMER_STATUS;
		$this->currency = YATEGO_CURRENCY;
		$this->exportQuantities = YATEGO_QUANTITIES=='false'?false:true;
		$this->exportAllProducts = YATEGO_EXPORTALL=='false'?false:true;
		$this->h2longdesc = YATEGO_H2LONGDESC=='false'?false:true;
		$this->genshortdesc = YATEGO_GENSHORTDESC=='false'?false:true;
		$this->genpackagesize = YATEGO_GENPACKAGESIZE=='false'?false:true;
		$this->articleSelect = "SELECT DISTINCT
		products.products_id AS foreign_id,
		products.products_model AS article_nr,";

		if(mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SHOW COLUMNS FROM products LIKE 'products_ean'")) > 0)
		{
			$this->articleSelect .= "products.products_ean AS ean,";
		}

		$this->articleSelect .= "
		products_description.products_name AS title,
		products.products_tax_class_id as tax,
		products.products_id AS price,
		IF(specials.specials_new_products_price, REPLACE(`products_price`*(1+IF(`tax_rate`,(`tax_rate`/100),0)), '.', ','), '') AS price_uvp,
		products_description.products_short_description AS short_desc,
		products_description.products_description AS long_desc,
		REPLACE(products.products_weight, '.', ',') AS units,
		products.products_id AS url,
		products.products_image AS picture,
		image2.pic AS picture2,
		image3.pic AS picture3,
		image4.pic AS picture4,
		image5.pic AS picture5,
		cats.categories AS categories,
		atts.vars AS variants,
		products.products_quantity AS stock,
		products.products_status AS status,
		shipping_status.shipping_status_name as delivery_date,
		products_vpe.products_vpe_name AS quantity_unit,
		REPLACE(products.products_vpe_value, '.', ',') AS package_size,
		manufacturers.manufacturers_name as manufacturer,
		xsells.xsells AS cross_selling
		FROM products
		INNER JOIN yatego_articles
		ON products.products_id = yatego_articles.product_id
		LEFT JOIN manufacturers
		ON products.manufacturers_id = manufacturers.manufacturers_id
		LEFT JOIN products_vpe
		ON products.products_vpe = products_vpe.products_vpe_id
		AND products_vpe.language_id = $this->language
		LEFT JOIN shipping_status
		ON products.products_shippingtime = shipping_status.shipping_status_id
		AND shipping_status.language_id = $this->language
		LEFT JOIN tax_rates
		ON products.products_tax_class_id = tax_rates.tax_rates_id
		LEFT JOIN products_description
		ON products.products_id=products_description.products_id
		LEFT JOIN products_to_categories
		ON products.products_id=products_to_categories.products_id
		LEFT JOIN specials ON products.products_id = specials.products_id
		LEFT JOIN (
			SELECT products_id AS picid, image_name AS pic
			FROM products_images
			WHERE products_images.image_nr=1) AS image2
		ON image2.picid=products.products_id
		LEFT JOIN (
			SELECT products_id AS picid, image_name AS pic
			FROM products_images
			WHERE products_images.image_nr=2) AS image3
		ON image3.picid=products.products_id
		LEFT JOIN (
			SELECT products_id AS picid, image_name AS pic
			FROM products_images
			WHERE products_images.image_nr=3) AS image4
		ON image4.picid=products.products_id
		LEFT JOIN (
			SELECT products_id AS picid, image_name AS pic
			FROM products_images
			WHERE products_images.image_nr=4) AS image5
		ON image5.picid=products.products_id
		LEFT JOIN (
			SELECT products_to_categories.products_id AS catids,
			CONCAT_WS(',', GROUP_CONCAT( products_to_categories.categories_id ), GROUP_CONCAT(yatego_category_mapping.yatego_category))
			AS categories
			FROM products_to_categories
			LEFT JOIN yatego_category_mapping
			ON products_to_categories.categories_id=yatego_category_mapping.shop_category
			GROUP BY products_to_categories.products_id
		) AS cats
		ON cats.catids = products.products_id
		LEFT JOIN
			(SELECT
			products_attributes.products_id AS prodids,
			GROUP_CONCAT(DISTINCT products_attributes.products_id,'_',products_attributes.options_id) AS vars
			FROM products_attributes
			GROUP BY products_attributes.products_id) AS atts
		ON atts.prodids = products.products_id
		LEFT JOIN (
			SELECT products_xsell.products_id AS xids, GROUP_CONCAT( products_xsell.xsell_id ) AS xsells
			FROM products_xsell
			GROUP BY products_xsell.products_id
		) AS xsells
		ON xsells.xids = products.products_id
		WHERE products_description.language_id=$this->language
		";
		if($this->exportAllProducts==false) {
			$this->articleSelect .= " AND yatego_articles.export_yatego = 1";
		}
		else
		{
			xtc_db_query("
				INSERT INTO yatego_articles (product_id)
				SELECT products.products_id FROM products
				WHERE products.products_id NOT IN (select yatego_articles.product_id from yatego_articles)");
		}
		$this->variantSetSelect = "SELECT DISTINCT
		CONCAT( products_attributes.products_id, '_', products_attributes.options_id ) AS foreign_id,
		products_options.products_options_name AS vs_title,
		NULL AS variant_set_name, NULL AS delitem
		FROM products_attributes
		INNER JOIN products_options
		ON products_options.products_options_id=products_attributes.options_id
		WHERE products_options.language_id = $this->language
		";
		$this->variantSelect = "SELECT DISTINCT CONCAT( products_attributes.products_id, '_', products_attributes.options_id ) AS variant_set_id,
		CONCAT( products_attributes.products_id, '_', products_attributes.options_id, '_', products_attributes.options_values_id ) AS foreign_id,
		products_options_values.products_options_values_name AS description,
		REPLACE (
		IF (
		products_attributes.price_prefix = '-',
		concat( '-', products_attributes.options_values_price ) ,
		products_attributes.options_values_price
		),
		'.',
		','
		) AS price,
		NULL AS delitem
		FROM products_attributes
		INNER JOIN products_options_values ON products_attributes.options_values_id = products_attributes.options_values_id
		WHERE products_options_values_id = options_values_id
		AND products_options_values.language_id = $this->language
		";
		$this->categorySelect = "SELECT DISTINCT cat.categories_id AS foreign_id_h, NULL AS foreign_id_m, NULL as foreign_id_l, cd.categories_name AS title_h, NULL AS title_m, NULL AS title_l, NULL AS sorting
		FROM categories cat
		JOIN categories_description cd ON cat.categories_id = cd.categories_id
		WHERE cat.parent_id = 0 AND cd.language_id = $this->language
		UNION SELECT DISTINCT cat.categories_id AS foreign_id_h, cat1.categories_id AS foreign_id_m, NULL as foreign_id_l, cd.categories_name AS title_h, cd1.categories_name AS title_m, NULL as title_l, NULL AS sorting
		FROM categories cat
		INNER JOIN categories cat1 ON cat.categories_id = cat1.parent_id
		JOIN categories_description cd ON cat.categories_id = cd.categories_id
		JOIN categories_description cd1 ON cat1.categories_id = cd1.categories_id
		WHERE cat.parent_id = 0 and cd.language_id = $this->language AND cd1.language_id = $this->language
		UNION SELECT DISTINCT cat.categories_id AS foreign_id_h, cat1.categories_id AS foreign_id_m, cat2.categories_id as foreign_id_l, cd.categories_name AS title_h, cd1.categories_name AS title_m, cd2.categories_name as title_l, NULL AS sorting
		FROM categories cat
		INNER JOIN categories cat1 ON cat.categories_id = cat1.parent_id
		INNER JOIN categories cat2 ON cat1.categories_id = cat2.parent_id
		JOIN categories_description cd ON cat.categories_id = cd.categories_id
		JOIN categories_description cd1 ON cat1.categories_id = cd1.categories_id
		JOIN categories_description cd2 ON cat2.categories_id = cd2.categories_id
		WHERE cat.parent_id = 0 and cd1.language_id = $this->language AND cd2.language_id = $this->language
		";

		// select all options (sowas wie variantensätze)
		$letters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','a1','b1','c1','d1','e1','f1','g1','h1','i1','j1','k1','l1','m1','n1','o1','p1','q1','r1','s1','t1','u1','v1','w1','x1','y1','z1');
		$result = xtc_db_query('SELECT DISTINCT products_options_id FROM products_options');
		$products_options_ids = array();
		while($row = mysqli_fetch_assoc($result))
		{
			$products_options_ids[] = $row['products_options_id'];
		}

		if (false && count($products_options_ids) > 1 && count($products_options_ids) <= 50)
		{
			// Fehler: wenn Artikel mehr wie einen Variantensatz zugeordnet hatte, wurden stocks falsch zusammengesetzt
			// es werden nur die stocks exportiert, wo weniger wie 1000 variantenkombinationen haben

			$query1 = "SELECT products_id, count(*) anz FROM products_attributes WHERE options_id = ..options_id.. group by products_id";
			$query2 = "SELECT products_attributes_id,products_id,concat(products_id, '_',options_id, '_',options_values_id) variant_ids,attributes_stock,attributes_model
						FROM products_attributes WHERE options_id = ..options_id..";

			$query = "SELECT md5(concat(a.products_attributes_id";
			for ($i = 1; $i < count($products_options_ids); $i++) $query .= ",ifnull(".$letters[$i].".products_attributes_id,'')";
			$query .= ")) foreign_id,a.products_id article_id, replace(replace(replace(replace(replace(replace(replace(replace(replace(trim(trailing ',' from concat(a.variant_ids";
			for ($i = 1; $i < count($products_options_ids); $i++) $query .= ",',',ifnull(".$letters[$i].".variant_ids,'')";
			$query .= ")), ',,', ','), ',,,,,,,,,', ','), ',,,,,,,,', ','), ',,,,,,,', ','), ',,,,,,', ','), ',,,,,', ','), ',,,,', ','), ',,,', ','), ',,', ',')  variant_ids, least(a.attributes_stock";
			for ($i = 1; $i < count($products_options_ids); $i++) $query .= ",ifnull(".$letters[$i].".attributes_stock,0)";
			$query .= ") stock_value,";

			if(mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SHOW COLUMNS FROM products LIKE 'products_ean'")) > 0)
			{
				$query .= "products.products_ean AS ean,";
			}

			$query .= "'' delivery_date, if(least(a.attributes_stock";
			for ($i = 1; $i < count($products_options_ids); $i++) $query .= ",ifnull(".$letters[$i].".attributes_stock,0)";
			$query .= ") > 0, 1, 0) active, a.attributes_model article_nr, '0' price";

			$query .= " FROM (".str_replace('..options_id..',$products_options_ids[0],$query2)." AND products_attributes.products_id IN (";
			$query .= "SELECT a.products_id FROM (";
			$query .= str_replace('..options_id..',$products_options_ids[0],$query1).") a ";
			for ($i = 1; $i < count($products_options_ids); $i++) $query .= 'left join ('.str_replace('..options_id..',$products_options_ids[$i],$query1).") ".$letters[$i]." ON a.products_id = ".$letters[$i].".products_id ";
			$query .= "WHERE ((a.anz";
			for ($i = 1; $i < count($products_options_ids); $i++) $query .= " * ifnull(".$letters[$i].".anz,1)";
			$query .= ") < 1000))) a INNER JOIN yatego_articles ON a.products_id = yatego_articles.product_id";
			for ($i = 1; $i < count($products_options_ids); $i++) $query .= " left join (".str_replace('..options_id..',$products_options_ids[$i],$query2).") ".$letters[$i]." ON a.products_id = ".$letters[$i].".products_id";
			$query .= " INNER JOIN products ON a.products_id = products.products_id WHERE yatego_articles.export_yatego = 1";
			$this->stockSelect = $query;
		}
		else
		{
			$this->stockSelect = "SELECT DISTINCT products_attributes.products_attributes_id AS foreign_id,
			products_attributes.products_id AS article_id,
			CONCAT( products_attributes.products_id, '_', products_attributes.options_id, '_', products_attributes.options_values_id ) AS variant_ids,
			attributes_stock AS stock_value,";

			if(mysqli_num_rows(mysqli_query($GLOBALS["___mysqli_ston"], "SHOW COLUMNS FROM products LIKE 'products_ean'")) > 0)
			{
				$this->stockSelect .= "products.products_ean AS ean,";
			}

			$this->stockSelect .= "
			products_vpe.products_vpe_name AS quantity_unit,
			manufacturers.manufacturers_name as manufacturer,
			REPLACE(products_attributes.options_values_weight, '.', ',') AS package_size,
			'' AS delivery_date,
			IF(attributes_stock > 0, 1, 0) AS active,
			attributes_model AS article_nr,
			0 AS price
			FROM products_attributes INNER JOIN products
			LEFT JOIN manufacturers
			ON products.manufacturers_id = manufacturers.manufacturers_id
			LEFT JOIN products_vpe
			ON products.products_vpe = products_vpe.products_vpe_id
			AND products_vpe.language_id = $this->language
			WHERE products.products_id = products_attributes.products_id
			";
		}

		$this->discountSetSelect = "SELECT DISTINCT
		products_id AS 'foreign_id',
		1 AS 'active',
		'Rabatt' AS 'title',
		'a' AS 'type'
		FROM personal_offers_by_customers_status_$this->customerGroup
		GROUP BY products_id
		";
		$this->discountSelect = "SELECT DISTINCT personal_offers_by_customers_status_$this->customerGroup.products_id AS 'discount_set_id',
		personal_offers_by_customers_status_$this->customerGroup.price_id AS 'foreign_id',
		personal_offers_by_customers_status_$this->customerGroup.quantity AS 'condition',
		REPLACE(products.products_price - personal_offers_by_customers_status_$this->customerGroup.personal_offer*(1+IF(tax_rate,(tax_rate/100),0)), '.', ',') AS 'value',
		'eur' AS 'value_type'
		FROM personal_offers_by_customers_status_$this->customerGroup
		LEFT JOIN products ON personal_offers_by_customers_status_$this->customerGroup.products_id = products.products_id
		LEFT JOIN tax_rates	ON products.products_tax_class_id = tax_rates.tax_rates_id
		";
	}

/* --------------------------------------------------------------
   * Erstellung der Artikel-Datei
   * Die Felder der CSV-Datei werden über die SQL-Selects
   * bestimmt. Auch die Reihenfolger der Felder und deren
   * Bezeichner wird dort festgelegt.
   * Feldtrenner ;
   * Zeilenumbruch \r\n
   --------------------------------------------------------------*/
	function exportArticles() {
		$xtPrice = new xtcPrice($this->currency, $this->customerGroup);
		$result = xtc_db_query($this->articleSelect);
		// Ausgabe der Feldbezeichner
		while($field = xtc_db_fetch_fields($result)){
			$this->dataOutput($field->name . ';');
		}
		$this->dataOutput("\r\n");
		// Ausgabe der Artikeldaten
		while($articleLine = mysqli_fetch_assoc($result)) {
			foreach($articleLine as $key => $field) {
				// Preis wird über Klasse des XT:Commerce bestimmt
				if($key=='price') {
					$field = $xtPrice->xtcGetPrice($field,
                                        false,
                                        1,
                                        $articleLine['tax'],
                                        '');
					$field = str_replace('.', ',', $field);
				}
				// Mwst wird über Klasse des XT:Commerce bestimmt
				if($key=='tax') {
					$field = $xtPrice->TAX[$articleLine['tax']];
					$field = str_replace('.', ',', $field);
				}
				// HTML-Tags werden für Kurzbeschreibung entfernt. Begrenzung auf 130 Zeichen
				if($key=='short_desc') {
					if($this->genshortdesc) {
						$field = strip_tags($articleLine['long_desc']);
						$field = $articleLine['title'] . " - " . substr($articleLine['long_desc'], 0, 130-strlen($articleLine['title'])-6) . "...";
					}
					else {
						$field = substr(strip_tags($field), 0, 130-3) . "...";
					}
				}
				if($key=='long_desc') {
					if($this->h2longdesc)
						$field = "<h2>" . $articleLine['title'] . "</h2>" . $field;
				}
				// In Textfeldern werden Umbrüche entfernt, <html> und <head>-Tags gefiltert,
				// Anführungszeichen durch doppelte ersetzt
				// und das ganze Feld in Anführungszeichen eingeschlossen
				if($key=='title' || $key=='short_desc' || $key=='long_desc') {
					$field = str_replace("\r", '', $field);
					$field = str_replace("\n", '', $field);
					$field = preg_replace('/<\s*html[^>]*>/', '', $field);
					$field = preg_replace('/<\s*head[^>]*>/', '', $field);
					$field = preg_replace('/<\s*\/\s*html[^>]*>/', '', $field);
					$field = preg_replace('/<\s*\/\s*head[^>]*>/', '', $field);
					$field = str_replace('"', '""', $field);
					$field = '"' . $field . '"';
				}
				if($key=='url') {
					$field = '"' . xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($field, $articleLine['title'])) . '"';
				}
				if($key=='picture' || $key=='picture2' || $key=='picture3' || $key=='picture4' || $key=='picture5') {
					if($field != '')
						$field = xtc_href_link(DIR_WS_POPUP_IMAGES . $field);
				}
				if($key=='stock' && $this->exportQuantities == false) {
					$field = "-1";
				}

				if($key=='stock') {
				$field = str_replace('.', ',', $field);
				}

				if($key=='quantity_unit') {
					if($this->genpackagesize)
					{
						$field = $articleLine['quantity_unit'];
					}
					else
					{
						$field = '';
					}
				}
				if($key=='package_size') {
					if($this->genpackagesize)
					{
						$field = $articleLine['package_size'];
					}
					else
					{
						$field = '0.00';
					}
				}

				$this->dataOutput($field . ';');
			}
			$this->dataOutput("\r\n");
		}
	}

/* --------------------------------------------------------------
   * Erstellung der Variantensatz-Datei
   * Die Felder der CSV-Datei werden über die SQL-Selects
   * bestimmt. Auch die Reihenfolger der Felder und deren
   * Bezeichner wird dort festgelegt.
   * Feldtrenner ;
   * Zeilenumbruch \r\n
   --------------------------------------------------------------*/
	function exportVariantSets() {
		$result = xtc_db_query($this->variantSetSelect);
		// Ausgabe der Feldbezeichner
		while($field = xtc_db_fetch_fields($result)){
			$this->dataOutput($field->name . ';');
		}
		$this->dataOutput("\r\n");
		// Ausgabe der Variantensatzdaten
		while($variantSetsLine = mysqli_fetch_assoc($result)) {
			foreach($variantSetsLine as $key => $field) {
				// Im Titel werden Anführungszeichen durch doppelte ersetzt
				// und das ganze Feld in Anführungszeichen eingeschlossen
				if($key=='vs_title') {
					$field = str_replace('"', '""', $field);
					$field = '"' . $field . '"';
				}
				$this->dataOutput($field . ';');
			}
			$this->dataOutput("\r\n");
		}
	}

/* --------------------------------------------------------------
   * Erstellung der Varianten-Datei
   * Die Felder der CSV-Datei werden über die SQL-Selects
   * bestimmt. Auch die Reihenfolger der Felder und deren
   * Bezeichner wird dort festgelegt.
   * Feldtrenner ;
   * Zeilenumbruch \r\n
   --------------------------------------------------------------*/
	function exportVariants() {
		$result = xtc_db_query($this->variantSelect);
		while($field = xtc_db_fetch_fields($result)){
			$this->dataOutput($field->name . ';');
		}
		$this->dataOutput("\r\n");
		while($variantsLine = mysqli_fetch_assoc($result)) {
			foreach($variantsLine as $key => $field) {
				// In der Bezeichnung werden Anführungszeichen durch doppelte ersetzt
				// und das ganze Feld in Anführungszeichen eingeschlossen
				if($key=='description') {
					$field = str_replace('"', '""', $field);
					$field = '"' . $field . '"';
				}
				$this->dataOutput($field . ';');
			}
			$this->dataOutput("\r\n");
		}
	}

/* --------------------------------------------------------------
   * Erstellung der Kategorien-Datei
   * Die Felder der CSV-Datei werden über die SQL-Selects
   * bestimmt. Auch die Reihenfolger der Felder und deren
   * Bezeichner wird dort festgelegt.
   * Feldtrenner ;
   * Zeilenumbruch \r\n
   --------------------------------------------------------------*/
	function exportCategories() {
		$result = xtc_db_query($this->categorySelect);
		while($field = xtc_db_fetch_fields($result)){
			$this->dataOutput($field->name . ';');
		}
		$this->dataOutput("\r\n");
		while($categoriesLine = mysqli_fetch_assoc($result)) {
			foreach($categoriesLine as $key => $field) {
				if($key=='title_h' || $key=='title_m' || $key=='title_l') {
					$field = str_replace('"', '""', $field);
					$field = '"' . $field . '"';
				}
				$this->dataOutput($field . ';');
			}
			$this->dataOutput("\r\n");
		}
	}

/* --------------------------------------------------------------
   * Erstellung der Lager-Datei
   * Die Felder der CSV-Datei werden über die SQL-Selects
   * bestimmt. Auch die Reihenfolger der Felder und deren
   * Bezeichner wird dort festgelegt.
   * Feldtrenner ;
   * Zeilenumbruch \r\n
   --------------------------------------------------------------*/
	function exportStocks() {

		if ($this->exportQuantities == false) return;

		$result = xtc_db_query($this->stockSelect);
		while($field = xtc_db_fetch_fields($result)){
			$this->dataOutput($field->name . ';');
		}
		$this->dataOutput("\r\n");
		while($stocksLine = mysqli_fetch_assoc($result)) {
			foreach($stocksLine as $key => $field) {
					if($key=='quantity_unit') {
					if($this->genpackagesize)
					{
						$field = $stocksLine['quantity_unit'];
					}
					else
					{
						$field = '';
					}
					}
					if($key=='package_size') {
					if($this->genpackagesize)
					{
						$field = $stocksLine['package_size'];
					}
					else
					{
						$field = '0.00';
					}
				}

				if($key=='stock_value') {
				$field = str_replace('.', ',', $field);
				}

				$this->dataOutput($field . ';');
			}
			$this->dataOutput("\r\n");
		}

	}

/* --------------------------------------------------------------
   * Erstellung der Rabattsatz-Datei
   * Die Felder der CSV-Datei werden über die SQL-Selects
   * bestimmt. Auch die Reihenfolger der Felder und deren
   * Bezeichner wird dort festgelegt.
   * Feldtrenner ;
   * Zeilenumbruch \r\n
   --------------------------------------------------------------*/
	function exportDiscountSets() {
		$result = xtc_db_query($this->discountSetSelect);
		while($field = xtc_db_fetch_fields($result)){
			$this->dataOutput($field->name . ';');
		}
		$this->dataOutput("delitem\r\n");
		while($discountSetsLine = mysqli_fetch_assoc($result)) {
			foreach($discountSetsLine as $key => $field) {
				$this->dataOutput($field . ';');
			}
			$this->dataOutput("\r\n");
		}
	}

/* --------------------------------------------------------------
   * Erstellung der Rabatt-Datei
   * Die Felder der CSV-Datei werden über die SQL-Selects
   * bestimmt. Auch die Reihenfolger der Felder und deren
   * Bezeichner wird dort festgelegt.
   * Feldtrenner ;
   * Zeilenumbruch \r\n
   --------------------------------------------------------------*/
	function exportDiscounts() {
		$result = xtc_db_query($this->discountSelect);
		while($field = xtc_db_fetch_fields($result)){
			$this->dataOutput($field->name . ';');
		}
		$this->dataOutput("delitem\r\n");
		while($discountsLine = mysqli_fetch_assoc($result)) {
			foreach($discountsLine as $key => $field) {
				$this->dataOutput($field . ';');
			}
			$this->dataOutput("\r\n");
		}
	}

/* --------------------------------------------------------------
   * Methode zur Ausgabe der Daten
   * Abhängig vom exportMode werden die Daten an den Browser
   * übergeben, oder in eine gzip-Datei geschrieben.
   --------------------------------------------------------------*/
	function dataOutput($data) {
		switch($this->exportMode) {
			case 'download':
			echo $data;
			break;
			case 'zip':
			if (!gzwrite($this->fileHandle, $data)) {
				print "Kann in die Datei $this->filename nicht schreiben";
				exit;
			}
			break;
		}
	}
	function PostToHost($host, $port, $path, $referer, $data_to_send)
	{
		$dc = 0;
		$bo="-----------------------------305242850528394";

		$fp = fsockopen($host, $port, $errno, $errstr);
		if (!$fp) {
			echo "errno: $errno \n";
			echo "errstr: $errstr \n";
			return $result;
		}
		if(fputs($fp, "POST $path HTTP/1.0\n") === FALSE) {
			echo "Cannot write to $path \n";
		}
		fputs($fp, "Host: $host\n");
		fputs($fp, "Referer: $referer\n");
		fputs($fp, "User-Agent: Mozilla/4.05C-SGI [en] (X11; I; IRIX 6.5 IP22)\n");
		fputs($fp, "Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, image/png, */*\n");
		fputs($fp, "Accept-Charset: iso-8859-1,*,utf-8\n");
		fputs($fp, "Content-type: multipart/form-data; boundary=$bo\n");
		foreach($data_to_send as $key=>$val) {
			$ds =sprintf("--%s\nContent-Disposition: form-data; name=\"%s\"\n\n%s\n", $bo, $key, $val);
			$dc += strlen($ds);
		}
		$dc += strlen($bo)+3;
		fputs($fp, "Content-length: $dc \n");
		fputs($fp, "\n");
		foreach($data_to_send as $key=>$val) {
			$ds =sprintf("--%s\nContent-Disposition: form-data; name=\"%s\"\n\n%s\n", $bo, $key, $val);
			fputs($fp, $ds );
		}
		$ds = "--".$bo."--\n";
		fputs($fp, $ds);
		$res = fread($fp, 15);
		fclose($fp);

		return $res;
	}
	function sendData() {
		$fa = file(DIR_FS_DOCUMENT_ROOT.'export/' . 'yatego.gz');

		$xf = implode("", $fa);

		$data["user"]			= YATEGO_USERNAME; // Yatego-Login (bzw. Yatego-Domain inkl. .yatego.com
		$data["passwd"]     	= YATEGO_PASSWORD; // Passwort f?r Login
		$data["action"]     	= "import_csv"; // Aktion (F?r den Import von Excel-Offline-Tools "import", f?r den Import von CSV "import_csv
		$data["delall"]			= "0"; // L?schen aller Daten vor Import (1) (Artikel-Reset), keine L?schaktion vor Import (0)
		$data["import_mode"]	= "1"; // L?schen aller Daten die nicht in der Import-Datei vorhanden sind (1), nicht L?schen (0)
		$data["import_pic"]		= "0"; // Alle (max. 5) Bilder aus der Langbeschreibung als Bilder verwenden (1), nicht verwenden (0)
		$data["import_file\"; filename=\"yatego.gz"]        = $xf; // Dateiname des Zip-Archivs

		$x = $this->PostToHost("www1.yatego.com",80,"/admin/modules/yatego/importexport.php","www1.yatego.com/admin/modules/yatego/importexport.php",$data);

		if (strpos($x, "HTTP/1.1 200 OK") !== false)
			print "<h2>Ihre Daten wurden zum Import angenommen.</h2>";
		else
			print "<h2>Fehler beim Import.</h2>";
	}
}