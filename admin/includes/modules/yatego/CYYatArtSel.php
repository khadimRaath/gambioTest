<?php
/* --------------------------------------------------------------
   * $Id: CYYatArtSel.php,v 1.4 2008/02/17 10:40:47 tobias Exp $
   * Auswahl der Artikel, die exportiert werden sollen
   --------------------------------------------------------------*/

class CYYatArtSel {
	var $language; // verwendete Sprache in Darstellung
	var $categories; // Begrenzung auf Kategorie in Darstellung
	var $selArt; // Konfigruationseinstellung, ob Modul verwendet wird
	var $boxChecked;
	var $category;
	
	function CYYatArtSel() {
		// Initalisierung mit Werten aus der Konfiguration
		$this->language = YATEGO_LANGUAGE;
		$this->selArt = YATEGO_EXPORTALL=='true'?'false':'true';
		$this->getCategories();
		/*
		 * In der Tabelle yatego_articles existiert für jeden Artikel
		 * ein Eintrag. Sollte ein Artikel noch nicht in
		 * der Tabelle sein, wird er nun hinzugefügt.
		 */
		if($this->selArt == 'true') {
			xtc_db_query("
				INSERT INTO yatego_articles (product_id)
				SELECT products.products_id FROM products
				WHERE products.products_id NOT IN (select yatego_articles.product_id from yatego_articles)");
		}
	}

/* --------------------------------------------------------------
   * Auswahl aller Artikel auf der Datenbank
   --------------------------------------------------------------*/
	function selectAllArticles() {
		$result = xtc_db_query("
			UPDATE yatego_articles
			SET export_yatego = 1");
	}

/* --------------------------------------------------------------
   * Auswahl keines Artikels auf der Datenbank
   --------------------------------------------------------------*/
	function selectNoArticles() {
		$result = xtc_db_query("
			UPDATE yatego_articles
			SET export_yatego = 0");
	}

function displayTopSeller() 
{
	$this->topseller = YATEGO_TOPSELLER;	
	$link_yatego = xtc_href_link('yatego.php');
	if (strpos($link_yatego, '?') !== false)
   {
     	$link_yatego .= '&';
   }
   else
   {
   	    $link_yatego .= '?';
   }
?>	
<p>
<form name="topseller" action="<?php echo $link_yatego.'section=selectTopSeller'. "&amp;selectTopSeller=1"?>" method="POST">
<input type="radio" name="topselleramount" value="0" checked> Alle Artikel<br>
<input type="radio" name="topselleramount" value="50" <?php if($this->topseller == 50)echo 'checked'?>> 50 TopSeller<br>
<input type="radio" name="topselleramount" value="250" <?php if($this->topseller == 250)echo 'checked'?>> 250 TopSeller<br>
<input type="radio" name="topselleramount" value="1000" <?php if($this->topseller == 1000)echo 'checked'?>> 1000 TopSeller<br>
<input type="radio" name="topselleramount" value="alle" <?php if($this->topseller == alle)echo 'checked'?>> Alle TopSeller<br>
<?php

print("</p>\r\n");
print("<p><input value=\"&Auml;nderungen &uuml;bernehmen\" type=\"submit\" class=\"button\" style='width:auto' /></p>\r\n");
?>
</form>
</p>
<?php

}
	
		
/* --------------------------------------------------------------
   * Anzeige der Artikelauswahl
   --------------------------------------------------------------*/
	function display($page = 0, $category = 'all', $boxChecked = '', $topseller = '0', $selectall = '0') {
		
		if($this->selArt == 'true') {
			$link_yatego = xtc_href_link('yatego.php');
		   if (strpos($link_yatego, '?') !== false)
		   {
		     	$link_yatego .= '&';
		   }
		   else
		   {
		   	    $link_yatego .= '?';
		   }
   
			print("\t\t<form action=\"" . $link_yatego.'section=selectArticles' . "&amp;page={$page}&amp;category={$category}\" method=\"post\">");
?>
		<form action="<?php echo $link_yatego.'section=selectArticles' . "&amp;page={$page}&amp;category={$category}"; ?> method="post">			
			<div id="selectArticles">
				<p>
					<a style="font-weight:bold;font-size:10px" href="<?php echo $link_yatego.'section=selectArticles'; ?>&amp;selectArticles=all">alle Artikel exportieren</a> | 
					<a style="font-weight:bold;font-size:10px" href="<?php echo $link_yatego.'section=selectArticles'; ?>&amp;selectArticles=none">kein Artikel exportieren</a> | 
					<a style="font-weight:bold;font-size:10px" href="<?php echo $link_yatego.'section=selectArticles'; ?>&amp;selectCategoryArticles=yes&amp;category=<?php echo $category ?>">Diese Kategorie exportieren</a> | 
					<a style="font-weight:bold;font-size:10px" href="<?php echo $link_yatego.'section=selectArticles'; ?>&amp;selectCategoryArticles=no&amp;category=<?php echo $category ?>">Diese Kategorie nicht exportieren</a> | 
					<a style="font-weight:bold;font-size:10px" href="<?php echo $link_yatego.'section=selectArticles'; ?>&amp;selectArticles=topseller">Top-Seller-Artikel Auswählen</a>
				</p>
<?php
if($topseller != '0')
{
	print("<p><label>Menge:</label><select name=\"articletopseller\" onchange=\"location.href = '" . $link_yatego.'section=selectArticles&amp;selectArticles=topseller' . "&amp;topseller=' + this.options[this.selectedIndex].value;\">");
			print('<option value="">---</option>');
			print('<option value="50">50</option>');
			print('<option value="250">250</option>');
			print('<option value="1000">1000</option>');
			print('<option value="999999">Alle Top-SellerArtikel</option>');
			print("</select></p>\r\n");
			print("</p>\r\n");
		echo "<p><a href=\"yatego.php?section=selectArticles&selectArticles=topseller&topseller=$topseller&selectall=1\"/>Alle auswählen</a></p>\r\n";
	$productsSQL = "
			SELECT products.products_id, products.products_image, products_description.products_name, yatego_articles.export_yatego
			FROM products
			JOIN products_description ON products.products_id = products_description.products_id
			LEFT JOIN yatego_articles ON products.products_id = yatego_articles.product_id
			WHERE products_description.language_id = $this->language
			AND products.products_ordered != 0 
			ORDER BY products.products_ordered DESC 
			LIMIT 0," . (int)$topseller;
		
			$result = xtc_db_query($productsSQL);
			echo '<table>';
			while ($row = mysqli_fetch_row($result)) {
				if($selectall == '1')	
				{
					$box=' checked="checked"';
				}
				else
				{
					if($row[3]==1) {$box=' checked="checked"';} else {$box='';}
				}
				echo "<tr><td><input name=\"$row[0]\" type=\"hidden\" value=\"0\" />";
				echo xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES.$row[1], $row[2]);
				echo "</td><td><input name=\"$row[0]\" type=\"checkbox\" value=\"1\"" . $box . " />$row[2]</td></tr>\r\n";
			}
			echo '</table>';
			
		print("</p>\r\n");
		print("<p><input value=\"&Auml;nderungen &uuml;bernehmen\" type=\"submit\" class=\"button\" style='width:auto'  /></p>\r\n");	
}
else
{
			print("<p><label>Kategorie:</label><select name=\"articleCategory\" onchange=\"location.href = '" . $link_yatego.'section=selectArticles' . "&amp;category=' + this.options[this.selectedIndex].value;\">");
			print('<option value="all">Alle</option>');

			foreach($this->categories as $cat) {
				print("<option value=\"$cat[0]\"");
				if($category == $cat[0]){print(" selected=\"selected\"");}
				print(">$cat[1]</option>\r\n");
			}
			print("</select></p>\r\n");

			if(!empty($_POST)) {
				foreach($_POST as $key => $value) {
					$result = xtc_db_query("
					UPDATE yatego_articles SET
					`export_yatego` = '{$value}'
					WHERE `product_id` = '{$key}'
					");
				}
			}
			if(isset($boxChecked) && $boxChecked != '')
			{
				if($boxChecked == 2)
				{
					$boxChecked = 0;
				}
				$query = 'SELECT a.categories_id a, b.categories_id b, c.categories_id c, d.categories_id d, e.categories_id e, f.categories_id f, g.categories_id g
				FROM categories a 
				LEFT JOIN categories b ON a.categories_id = b.parent_id
				LEFT JOIN categories c ON b.categories_id = c.parent_id
				LEFT JOIN categories d ON c.categories_id = d.parent_id
				LEFT JOIN categories e ON d.categories_id = e.parent_id
				LEFT JOIN categories f ON e.categories_id = f.parent_id
				LEFT JOIN categories g ON f.categories_id = g.parent_id
				WHERE a.categories_id ='.xtc_db_input($category);
				$categories_ids = array();
				$result = xtc_db_query($query);
				while ($row = mysqli_fetch_assoc($result)) 
				{
					if ($row['a'] != '') $categories_ids[] = $row['a'];
					if ($row['b'] != '') $categories_ids[] = $row['b'];
					if ($row['c'] != '') $categories_ids[] = $row['c'];
					if ($row['d'] != '') $categories_ids[] = $row['d'];
					if ($row['e'] != '') $categories_ids[] = $row['e'];
					if ($row['f'] != '') $categories_ids[] = $row['f'];
					if ($row['g'] != '') $categories_ids[] = $row['g'];
				}
				$categories_ids = array_unique($categories_ids);
				
				$productsSQL = "SELECT * FROM products
				JOIN products_to_categories ON products.products_id=products_to_categories.products_id
				WHERE products_to_categories.categories_id in (".implode(',', $categories_ids).")
				";
				
				$result = xtc_db_query($productsSQL);
				while($row = mysqli_fetch_assoc($result)) 
				{
					$update = "UPDATE yatego_articles SET `export_yatego` = ".$boxChecked." WHERE `product_id` = ".$row['products_id'];
						xtc_db_query($update);
				}
			}
			
			
			$productsSQL = "SELECT COUNT(*) FROM products";
			if($category != 'all') {
				
				// SELECT ALL CATEGORIES-IDS
				$query = 'SELECT a.categories_id a, b.categories_id b, c.categories_id c, d.categories_id d, e.categories_id e, f.categories_id f, g.categories_id g
				FROM categories a 
				LEFT JOIN categories b ON a.categories_id = b.parent_id
				LEFT JOIN categories c ON b.categories_id = c.parent_id
				LEFT JOIN categories d ON c.categories_id = d.parent_id
				LEFT JOIN categories e ON d.categories_id = e.parent_id
				LEFT JOIN categories f ON e.categories_id = f.parent_id
				LEFT JOIN categories g ON f.categories_id = g.parent_id
				WHERE a.categories_id ='.xtc_db_input($category);
				
				$categories_ids = array();
				$result = xtc_db_query($query);
				while ($row = mysqli_fetch_assoc($result)) 
				{
					if ($row['a'] != '') $categories_ids[] = $row['a'];
					if ($row['b'] != '') $categories_ids[] = $row['b'];
					if ($row['c'] != '') $categories_ids[] = $row['c'];
					if ($row['d'] != '') $categories_ids[] = $row['d'];
					if ($row['e'] != '') $categories_ids[] = $row['e'];
					if ($row['f'] != '') $categories_ids[] = $row['f'];
					if ($row['g'] != '') $categories_ids[] = $row['g'];
				}
				$categories_ids = array_unique($categories_ids);
				
				$productsSQL = "SELECT COUNT(*) FROM products
			JOIN products_to_categories ON products.products_id=products_to_categories.products_id
			WHERE products_to_categories.categories_id in (".implode(',', $categories_ids).")
			";
			}
			$result = xtc_db_query($productsSQL);
			print("<p> Seite:");
			$total_pages = mysqli_fetch_row($result);
			$total_pages = ceil($total_pages[0] / 10);
			for($i = 0; $i < $total_pages; $i++) {
				$pages = $i + 1;
				echo "<a href=\"".$link_yatego.'section=selectArticles'."&amp;page=$i&amp;category=$category\">$pages</a>";
			}
			print("</p>\r\n");

			print("<p><input value=\"&Auml;nderungen &uuml;bernehmen\" type=\"submit\" class=\"button\" style='width:auto' /></p>\r\n");
			
			if($this->selArt == 'true') {
			if($page != 0) {
				$page *= 10;
			}

			$productsSQL = "
			SELECT products.products_id, products.products_image, products_description.products_name, yatego_articles.export_yatego
			FROM products
			JOIN products_description ON products.products_id = products_description.products_id
			LEFT JOIN yatego_articles ON products.products_id = yatego_articles.product_id
			WHERE products_description.language_id = $this->language
			LIMIT " . (int)$page . ",10
			";
			if($category != 'all') {
				$productsSQL = "SELECT products.products_id, products.products_image, products_description.products_name, yatego_articles.export_yatego
			FROM products
			JOIN products_description ON products.products_id = products_description.products_id
			LEFT JOIN yatego_articles ON products.products_id = yatego_articles.product_id
			join products_to_categories on products.products_id=products_to_categories.products_id
			WHERE products_description.language_id = $this->language AND products_to_categories.categories_id in (".implode(',', $categories_ids).") 
			LIMIT " . (int)$page . ",10
			";
			}

			$result = xtc_db_query($productsSQL);
			echo '<table>';
			while ($row = mysqli_fetch_row($result)) {
				if($row[3]==1) {$box=' checked="checked"';} else {$box='';}
				echo "<tr><td><input name=\"$row[0]\" type=\"hidden\" value=\"0\" />";
				echo xtc_image(DIR_WS_CATALOG_THUMBNAIL_IMAGES.$row[1], $row[2]);
				echo "</td><td><input name=\"$row[0]\" type=\"checkbox\" value=\"1\"" . $box . " />$row[2]</td></tr>\r\n";
			}
			echo '</table>';
		}
	}
}

?>
			</div>
		</form>
<?php
	}

	function getCategories() {
		$result = xtc_db_query("SELECT cat.categories_id, cd.categories_name
				FROM categories cat
				JOIN categories_description cd ON cat.categories_id = cd.categories_id
				WHERE cat.parent_id = 0 AND cd.language_id = $this->language
				UNION SELECT cat1.categories_id, concat_ws(' -> ',cd.categories_name, cd1.categories_name)
				FROM categories cat
				INNER JOIN categories cat1 ON cat.categories_id = cat1.parent_id
				JOIN categories_description cd ON cat.categories_id = cd.categories_id
				JOIN categories_description cd1 ON cat1.categories_id = cd1.categories_id
				WHERE cat.parent_id = 0 and cd.language_id = $this->language AND cd1.language_id = $this->language
				UNION SELECT cat2.categories_id, concat_ws(' -> ',cd.categories_name, cd1.categories_name, cd2.categories_name)
				FROM categories cat
				INNER JOIN categories cat1 ON cat.categories_id = cat1.parent_id
				INNER JOIN categories cat2 ON cat1.categories_id = cat2.parent_id
				JOIN categories_description cd ON cat.categories_id = cd.categories_id
				JOIN categories_description cd1 ON cat1.categories_id = cd1.categories_id
				JOIN categories_description cd2 ON cat2.categories_id = cd2.categories_id
				WHERE cat.parent_id = 0 and cd1.language_id = $this->language AND cd2.language_id = $this->language");
		while($row = mysqli_fetch_row($result)) {
			$this->categories[] = array($row[0], $row[1]);
		}
	}
}