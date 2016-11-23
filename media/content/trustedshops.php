<?php
/* --------------------------------------------------------------
   trustedshops.php 2014-06-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
$ts = new GMTSService();
$seal_id = $ts->findSealID($_SESSION['language_code']);
if($seal_id === false) {
	echo 'Diese Seite kann nur durch Eingabe einer gültigen Trusted Shops Shop-ID im Adminbereich freigeschaltet werden.';
} else { ?>
	<font face="Arial" size="2">
	<strong>Bei uns kaufen Sie sicher ein:<br>
	Wir haben uns umfassend prüfen lassen und das bekannte 
	Trusted Shops Gütesiegel erhalten!</strong><br><br>
	
	<strong>Was ist Trusted Shops? </strong><br>
	Trusted Shops bietet Ihnen einen Rundum-Schutz beim 
	Online-Einkauf. Shops mit dem Trusted Shops Gütesiegel 
	stehen für sicheres Einkaufen im Internet, egal welches 
	Zahlungsmittel Sie wählen! Und wenn dennoch Probleme 
	auftauchen: Trusted Shops hilft Ihnen weiter.<br>
	<br>
	
	<strong>Ihre Vorteile im Überblick:</strong>
	<ul type="square">
		<li>Durch Internet-Experten geprüfte und kontrollierte Händler 
		<li><a href="http://www.trustedshops.de/guetesiegel/kaeuferschutz.html#fifth_aspect" target="_blank"><u>Geld-zurück-Garantie im Fall von Nichtlieferung oder Warenrückgabe</u></a>
		<li>Erstattung Ihrer Selbstbeteiligung bei Kreditkartenmissbrauch 
		<li>Schnelle, unkomplizierte Problemlösung und Streitschlichtung 
		<li>Alle Leistungen von Trusted Shops sind für Verbraucher kostenlos! 
	</ul>
	
	<strong>Das Gütesiegel mit Garantie für geprüfte und seriöse Anbieter</strong><br>
	Alle Händler mit dem Trusted Shops Gütesiegel wurden umfassenden 
	Sicherheitstests unterzogen. Diese Prüfung mit mehr als 100 Einzelkriterien 
	orientiert sich an den Forderungen der Verbraucherschützer sowie dem nationalen 
	und europäischen Recht. Sie umfasst Bonität, Sicherheitstechnik, Preistransparenz, 
	Informationspflichten, Kundenservice und Datenschutz. Diese Anforderungen werden 
	ständig weiterentwickelt und an neueste Entwicklungen im Bereich Rechtsprechung 
	und Verbraucherschutz angepasst.<br>
	<br>
	<div align="center">
		
		
<form name="formSiegel" method="post" action="https://www.trustedshops.com/shop/certificate.php" target="_blank">
	<input name="shop_id" type="hidden" value="<?php echo $seal_id ?>">

 <table border="0" cellpadding="0" cellspacing="4" style="border-collapse: collapse" width="250" bgcolor="#EFEFEF">
  <tr>
   	<td width="250" height="16">
   		<p align="center"><b>
   		<font face="Verdana, Arial, Helvetica, Geneva, sans-serif" size="1" color="#666666">
		<a target="_blank" href="http://www.trustedshops.de/" style="text-decoration: none">
		<font color="#666666"> Sicher Einkaufen</font></a></font></b>
	</td>
  </tr>
  <tr>
   	<td width="250" align="center">

   		<table border="1" cellpadding="0" cellspacing="0" style="border-collapse: collapse" width="100%" bordercolor="#c0c0c0" valign="center">
    	<tr>
     		<td width="100%">
     			<div align="center">

			      <table width="100%" border="0" cellpadding="3" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111">
			       <tr valign="middle" bgcolor="#ffffff">
			        <td bgcolor="#ffffff" height="70" width="75" align="center">
			        	<p align="center"><input type="image" src="images/trusted_siegel.gif" border="0" title="Trusted Shops G&uuml;tesiegel - Bitte hier G&uuml;ltigkeit pr&uuml;fen!" name="Seal" align="middle" width="70" height="70">
			        </td>
			        <td bgcolor="#ffffff" height="70" width="175">
			        <a target="_blank" 
			        href="http://www.trustedshops.de/" 
			        style="text-decoration: none">
			        <font 
			        face="Verdana, Arial, Helvetica, Geneva, sans-serif" 
			        size="1" color="#666666">Gepr&uuml;fter Online-Shop mit 
			        kostenloser <a href="http://www.trustedshops.de/guetesiegel/kaeuferschutz.html#fifth_aspect" target="_blank"><u>Geld-zurück-Garantie</u></a> von Trusted Shops.
			        Klicken Sie auf das G&uuml;tesiegel, um die
			        G&uuml;ltigkeit zu pr&uuml;fen.</font></a></td>
			       </tr>
			      </table>

		     	</div>
     		</td>
    	</tr>
   		</table>

   </td>
  </tr>
 </table>
</form>

	</div>
	<br>
	<strong>Falls es doch einmal Probleme geben sollte...</strong><br>
	Für die <a href="http://www.trustedshops.de/guetesiegel/kaeuferschutz.html#fifth_aspect" target="_blank"><u>Geld-zurück-Garantie</u></a> können Sie sich nach jedem Einkauf bei einem 
	zertifizierten Shop anmelden. Sollte es dann beim Einkauf oder der Lieferung 
	dennoch zu Problemen kommen, können Sie sich per Online-System, E-Mail oder 
	Telefon an unser erfahrenes, mehrsprachiges Service-Center wenden. Hier erhalten 
	Sie professionelle Hilfe z. B. bei der Rückabwicklung von Transaktionen. Das hier 
	erfolgreich eingesetzte Streitschlichtungs-Verfahren wird wegen seiner 
	Praktikabilität und Kosteneffizienz von der Europäischen Kommission gefördert.<br><br>
	
	<a href="http://www.trustedshops.de" target="_blank"><u>
	Weitere Informationen zum sicheren Online-Shopping erhalten Sie bei Trusted Shops</u></a>
	</font>
<?php
 } 
 ?>
