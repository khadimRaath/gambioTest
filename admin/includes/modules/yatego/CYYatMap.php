<?php
/* --------------------------------------------------------------
   * $Id: CYYatMap.php,v 1.4 2008/02/17 10:40:47 tobias Exp $
   * Zuordnung der Shopkategorien zu den Yatego Kategorien
   --------------------------------------------------------------*/

class CYYatMap {
	var $language; // Sprache der Shopkateogrien

	function CYYatMap() {
		$this->language = YATEGO_LANGUAGE;
	}

/* --------------------------------------------------------------
   * Anzeige der Zuordnung
   --------------------------------------------------------------*/
	function display() {
		if(!empty($_POST)) {
				$result = xtc_db_query("TRUNCATE TABLE yatego_category_mapping");

				foreach(array_keys($_POST) as $key) {
					$result = xtc_db_query("
					INSERT INTO yatego_category_mapping
					( `category_mapping_id` , `shop_category` , `yatego_category` )
					VALUES (NULL , '$key', '{$_POST[$key]}')");
				}
		}
		mysqli_query($GLOBALS["___mysqli_ston"], "SET OPTION SQL_Max_JOIN=500000000000"); 
		$result = xtc_db_query("
SELECT DISTINCT cat.categories_id, cd.categories_name,yatego_category_mapping.yatego_category
		FROM categories cat
		INNER JOIN categories_description cd ON cat.categories_id = cd.categories_id
LEFT JOIN yatego_category_mapping
		ON cat.categories_id = yatego_category_mapping.shop_category
		WHERE cat.parent_id =0 AND cd.language_id = $this->language
		UNION SELECT DISTINCT cat1.categories_id, concat_ws(' -> ',cd.categories_name, cd1.categories_name),yatego_category_mapping.yatego_category
		FROM categories cat
		INNER JOIN categories cat1 ON cat.categories_id = cat1.parent_id
		JOIN categories_description cd ON cat.categories_id = cd.categories_id
		JOIN categories_description cd1 ON cat1.categories_id = cd1.categories_id
LEFT JOIN yatego_category_mapping
		ON cat1.categories_id = yatego_category_mapping.shop_category
		WHERE cat.parent_id = 0 and cd.language_id = $this->language AND cd1.language_id = $this->language
		UNION SELECT DISTINCT cat2.categories_id, concat_ws(' -> ',cd.categories_name, cd1.categories_name, cd2.categories_name),yatego_category_mapping.yatego_category
		FROM categories cat
		INNER JOIN categories cat1 ON cat.categories_id = cat1.parent_id
		INNER JOIN categories cat2 ON cat1.categories_id = cat2.parent_id
		JOIN categories_description cd ON cat.categories_id = cd.categories_id
		JOIN categories_description cd1 ON cat1.categories_id = cd1.categories_id
		JOIN categories_description cd2 ON cat2.categories_id = cd2.categories_id
LEFT JOIN yatego_category_mapping
		ON cat2.categories_id = yatego_category_mapping.shop_category
		WHERE cat.parent_id = 0 and cd1.language_id = $this->language AND cd2.language_id = $this->language
		UNION SELECT DISTINCT cat3.categories_id, concat_ws(' -> ',cd.categories_name, cd1.categories_name, cd2.categories_name, cd3.categories_name),yatego_category_mapping.yatego_category
		FROM categories cat
		INNER JOIN categories cat1 ON cat.categories_id = cat1.parent_id
		INNER JOIN categories cat2 ON cat1.categories_id = cat2.parent_id
		INNER JOIN categories cat3 ON cat2.categories_id = cat3.parent_id
		JOIN categories_description cd ON cat.categories_id = cd.categories_id
		JOIN categories_description cd1 ON cat1.categories_id = cd1.categories_id
		JOIN categories_description cd2 ON cat2.categories_id = cd2.categories_id
		JOIN categories_description cd3 ON cat3.categories_id = cd3.categories_id
LEFT JOIN yatego_category_mapping
		ON cat3.categories_id = yatego_category_mapping.shop_category
		WHERE cat.parent_id = 0 and cd1.language_id = $this->language AND cd2.language_id = $this->language AND cd3.language_id = $this->language");

		while ($row = mysqli_fetch_row($result)) {
    		$line[] = $row;
		}
		
		$link_yatego = xtc_href_link('yatego.php');
		   if (strpos($link_yatego, '?') !== false)
		   {
		     	$link_yatego .= '&';
		   }
		   else
		   {
		   	    $link_yatego .= '?';
		   }
		   
		echo '<form action="' . $link_yatego.'section=categorymapping' . '" method="post">';
		echo '<br>Über das unten aufgeführte Kategorien-Mapping können Sie Ihre Shopkategorien mit geeigneten Yatego-Kategorien verknüpfen.<p>Über das Mapping wird quasi ausgedrückt: "Artikel, die sich in dieser Shopkategorie befinden, sollen auch in jene Yatego-Kategorie eingeordnet werden". Dies vereinfacht die Zuordnung Ihrer Artikel zu Yatego-Kategorien, weil Sie die Zuordnung nicht bei jedem einzelnen Artikel vornehmen müssen.<p>Die Auflistung aller Yatego-Kategorien in Form einer Excel-Tabelle können Sie im Yatego-Administrationsbereich unter "Sortiment" > "Import/Export" > "Vorlagen" > "Download aller Yatego Kategorien-IDs" oder als CSV-Datei unter diesem <a href="http://www.yatego.com/index.htm?cl=mall&fnc=export_ycategories&noOutput=true">Link</a> abrufen.<p>Geben Sie einfach in den leeren Feldern rechts neben Ihren Shopkategorien die Identifikationsnummern der zu den Shop-Kategorien passenden Yatego-Kategorien an. Wenn Sie mehrere Kategorien zuordnen wollen, trennen Sie die IDs über ein Komma und ohne Leerzeichen (Schema: ##-##-##,##-##-##).<p><p>Die Zuweisung der Artikel zu Yatego-Kategorien ist aus konzeptioneller Sicht außerordentlich wichtig.<p>';
		echo '<table>';
		foreach($line as $v) {
			echo "<tr><td>$v[1]</td><td><input type=\"text\" value=\"$v[2]\" name=\"$v[0]\" /></td></tr>\r\n";
		}
		echo '</table>';
		echo '<p><input type="submit" value="Mapping einspielen" class="button" style="width:auto" /></p>';
		echo '</form>';
	}
}
?>