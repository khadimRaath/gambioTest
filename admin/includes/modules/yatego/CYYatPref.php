<?php
/* --------------------------------------------------------------
   * $Id: CYYatPref.php,v 1.4 2008/02/17 10:40:47 tobias Exp $
   * Konfiguration des Exports
   --------------------------------------------------------------*/

class CYYatPref {
	var $currency; // Währung, die exportiert wird
	var $customer_status; // Kundengruppe zur Preisberechnung
	var $language; // Sprache der Beschreibungen
	var $username; // Benutzername bei Yatego
	var $password; // Passwort bei Yatego
	var $quantities; // Export der Lagerbestände
	var $exportall; // Export aller Artikel
	var $h2longdesc; // Artikelname in <h2> vor Langbeschreibung
	var $genshortdesc; // Erstellen der Kurzbeschreibung
	var $genpackagesize; // Erstellen der Grundpreis
	
	function CYYatPref() {
		// Initialisierung mit den Werten aus der Datenbank
		$this->currency = YATEGO_CURRENCY;
		$this->customer_status = YATEGO_CUSTOMER_STATUS;
		$this->language = YATEGO_LANGUAGE;
		$this->username = YATEGO_USERNAME;
		$this->password = YATEGO_PASSWORD;
		$this->quantities = YATEGO_QUANTITIES;
		$this->exportall = YATEGO_EXPORTALL;
		$this->h2longdesc = YATEGO_H2LONGDESC;
		$this->genshortdesc = YATEGO_GENSHORTDESC;
		$this->genpackagesize = YATEGO_GENPACKAGESIZE;
	}

/* --------------------------------------------------------------
   * Ändern der Währung
   --------------------------------------------------------------*/
	function setCurrency($curr) {
		if($this->currency != $curr) {
			$this->currency = $curr;
			xtc_db_query("UPDATE configuration SET configuration_value='" . $this->currency . "' WHERE configuration_key='YATEGO_CURRENCY'");
			return true;
		}
		return false;
	}

/* --------------------------------------------------------------
   * Ändern der Kundengruppe
   --------------------------------------------------------------*/
	function setCustomerStatus($cust) {
		if($this->customer_status != $cust) {
			$this->customer_status = $cust;
			xtc_db_query("UPDATE configuration SET configuration_value='" . $this->customer_status . "' WHERE configuration_key='YATEGO_CUSTOMER_STATUS'");
			return true;
		}
		return false;
	}

/* --------------------------------------------------------------
   * Ändern der Sprache
   --------------------------------------------------------------*/
	function setLanguage($lang) {
		if($this->language != $lang) {
			$this->language = $lang;
			xtc_db_query("UPDATE configuration SET configuration_value='" . $this->language . "' WHERE configuration_key='YATEGO_LANGUAGE'");
			return true;
		}
		return false;
	}

/* --------------------------------------------------------------
   * Ändern des Yatego Benutzernamens
   --------------------------------------------------------------*/
	function setUsername($user) {
		if($this->username != $user) {
			$this->username = $user;
			xtc_db_query("UPDATE configuration SET configuration_value='" . xtc_db_input($this->username) . "' WHERE configuration_key='YATEGO_USERNAME'");
			return true;
		}
		return false;
	}

/* --------------------------------------------------------------
   * Ändern des Yatego Passworts
   --------------------------------------------------------------*/
	function setPassword($pass) {
		if($this->password != $pass) {
			$this->password = $pass;
			xtc_db_query("UPDATE configuration SET configuration_value='" . $this->password . "' WHERE configuration_key='YATEGO_PASSWORD'");
			return true;
		}
		return false;
	}

/* --------------------------------------------------------------
   * Ändern der Lagerbestände
   --------------------------------------------------------------*/
	function setQuantities($quan) {
		if($this->quantities != $quan) {
			$this->quantities = $quan;
			if(xtc_db_query("UPDATE configuration SET configuration_value='" . $this->quantities . "' WHERE configuration_key='YATEGO_QUANTITIES'")) {
				return true;
			}
		}
		return false;
	}

/* --------------------------------------------------------------
   * Ändern der Option zum Exportieren aller Artikel
   --------------------------------------------------------------*/
	function setExportAll($exp) {
		if($this->exportall != $exp) {
			$this->exportall = $exp;
			if(xtc_db_query("UPDATE configuration SET configuration_value='" . $this->exportall . "' WHERE configuration_key='YATEGO_EXPORTALL'")) {
				return true;
			}
			xtc_db_query("TRUNCATE TABLE yatego_articles");
		}
		return false;
	}

/* --------------------------------------------------------------
   * Ändern des Artikelnamens vor Langbeschreibung
   --------------------------------------------------------------*/
	function setH2longdesc($h2) {
		if($this->h2longdesc != $h2) {
			$this->h2longdesc = $h2;
			if(xtc_db_query("UPDATE configuration SET configuration_value='" . $this->h2longdesc . "' WHERE configuration_key='YATEGO_H2LONGDESC'")) {
				return true;
			}
		}
		return false;
	}
	
/* --------------------------------------------------------------
   * Ändern der Erstellung der Kurzbeschreibung
   --------------------------------------------------------------*/
	function setGenshortdesc($gen) {
		if($this->genshortdesc != $gen) {
			$this->genshortdesc = $gen;
			if(xtc_db_query("UPDATE configuration SET configuration_value='" . $this->genshortdesc . "' WHERE configuration_key='YATEGO_GENSHORTDESC'")) {
				return true;
			}
		}
		return false;
	}
	
	
/* --------------------------------------------------------------
   * Ändern der Erstellung der Grundpreis
   --------------------------------------------------------------*/
   function setGenpackagesize($gen) {
		if($this->genpackagesize != $gen) {
			$this->genpackagesize = $gen;
			if(xtc_db_query("UPDATE configuration SET configuration_value='".$this->genpackagesize."' WHERE configuration_key='YATEGO_GENPACKAGESIZE'")) {
				return true;
			}
		}
		return false;
	}	
	
/* --------------------------------------------------------------
   * Anzeige der Einstellungen
   * Auswahl wird per POST an yatego.php geschickt
   --------------------------------------------------------------*/
	function display() {
		
		$link_yatego = xtc_href_link('yatego.php');
		   if (strpos($link_yatego, '?') !== false)
		   {
		     	$link_yatego .= '&';
		   }
		   else
		   {
		   	    $link_yatego .= '?';
		   }
		
		echo '<p><form action="' . $link_yatego . 'module=yatego&amp;section=preferences" method="post" accept-charset="' . $_SESSION['language_charset'] . '">';
?>
			<fieldset>
			<legend>Export Einstellungen</legend>
			<ol>
			<li>
			<fieldset>
			<legend>Sprache</legend>
<?php
		// Es werden nur die Sprachen angezeigt, die auch installiert sind.
		// Hierfür wird die Klasse des XT:Commerce verwendet
		if (!isset($lng) && !is_object($lng)) {
			include(DIR_WS_CLASSES . 'language.php');
			$lng = new language;
		}
		reset($lng->catalog_languages);
		while(list($key, $value) = each($lng->catalog_languages)) {
			echo xtc_draw_radio_field('yatego_language', $value['id'], $value['id']==$this->language?true:false).$value['name'];
		}
?>
		</fieldset>
			</li>
			<li>
			<fieldset>
			<legend>W&auml;hrung</legend>
<?php
		$currencies=xtc_db_query("SELECT title, code FROM ".TABLE_CURRENCIES);
		while($currencies_data=xtc_db_fetch_array($currencies)) {
			echo xtc_draw_radio_field('yatego_currency', $currencies_data['code'], $currencies_data['code']==$this->currency?true:false).$currencies_data['title'];
		}
?>
		</fieldset>
			</li>
			<li>
			<label for="yatego_customer_status">Kundengruppe</label>
<?php
		$customers_statuses_array = xtc_get_customers_statuses();
		echo xtc_draw_pull_down_menu('yatego_customer_status',$customers_statuses_array, $this->customer_status, 'id="yatego_customer_status"');
		echo '</li>
			<li><label for="yatego_username">Yatego Benutzername</label><input type="text" name="yatego_username" id="yatego_username" value="' . htmlspecialchars($this->username) . '" /></li>
			<li><label for="yatego_password">Yatego Passwort</label><input type="text" name="yatego_password" id="yatego_password" value="********" /></li>
			<li><label for="yatego_quantities">Lagerbest&auml;nde exportieren</label>' . xtc_draw_checkbox_field('yatego_quantities', 'true', $this->quantities=='false'?false:true, 'id=yatego_quantities') . '</li>
			<li><label for="yatego_exportall">Alle Artikel exportieren</label>' . xtc_draw_checkbox_field('yatego_exportall', 'true', $this->exportall=='false'?false:true, 'id=yatego_exportall') . '</li>
			<li><label for="yatego_h2longdesc">Artikelname in &lt;h2&gt; vor Langbeschreibung</label>' . xtc_draw_checkbox_field('yatego_h2longdesc', 'true', $this->h2longdesc=='false'?false:true, 'id=yatego_h2longdesc') . '</li>
			<li><label for="yatego_genshortdesc">Kurzbeschreibung generieren</label>' . xtc_draw_checkbox_field('yatego_genshortdesc', 'true', $this->genshortdesc=='false'?false:true, 'id=yatego_genshortdesc') . '</li>
			<li><label for="yatego_genpackagesize">Grundpreis generieren</label>' . xtc_draw_checkbox_field('yatego_genpackagesize', 'true', $this->genpackagesize=='false'?false:true, 'id=yatego_genpackagesize') . '</li>
			<li><input type="submit" class="button" style="width:auto" /></li>
			</ol>
			</fieldset>
			</form>
			</p>';
	}
}