<?php
/* --------------------------------------------------------------
   german.inc.php 2016-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

define('BUTTON_CONTINUE', 'Fortfahren');
define('BUTTON_INSTALL', 'Updates durchf&uuml;hren');
define('BUTTON_GAMBIO_PORTAL', 'Zum Gambio Kundenportal');
define('BUTTON_LOGIN', 'Anmelden');
define('BUTTON_CONFIGURE', 'Zur Updates-Konfiguration');
define('BUTTON_SHOW_UPDATES', 'Zur Updates-&Uuml;bersicht');
define('BUTTON_SHOP', 'Zum Shop');
define('BUTTON_CHECK_PERMISSIONS', 'Rechte erneut &uuml;berpr&uuml;fen');
define('BUTTON_CONNECT', 'Verbinden');
define('BUTTON_CONNECT_NEW', 'Neu verbinden');
define('BUTTON_SET_PERMISSIONS', 'Rechte setzen');
define('BUTTON_CHECK_DELETE_FILES', 'Erneut pr&uuml;fen');
define('BUTTON_CHECK_MOVE', 'Erneut pr&uuml;fen');
define('BUTTON_CREATE_BACKUP', 'Dateien downloaden');
define('BUTTON_DELETE_FILES', 'Veraltete Dateien l&ouml;schen');
define('BUTTON_MOVE', 'Durchf&uuml;hren');
define('BUTTON_SKIP', 'Installationsfortsetzung erzwingen');
define('BUTTON_DOWNLOAD_FILELIST_TO_DELETE', 'Download der Löschliste');

define('HEADING_INSTALLATION_SERVICE', 'Gambio Installations-Service');
define('HEADING_INSTALLATION', 'Zur Installation');
define('HEADING_LOGIN', 'Login');
define('HEADING_UPDATES', 'Updates');
define('HEADING_FTP_DATA', 'FTP-Daten');
define('HEADING_REMOTE_CONSOLE', 'Remote-Konsole');
define('HEADING_WHICH_VERSION', 'Shopversion ausw&auml;hlen');
define('HEADING_INSTALLATION_SUCCESS', 'Installation abgeschlossen');
define('HEADING_PROGRESS', 'Installationsfortschritt');
define('HEADING_WRONG_PERMISSIONS', 'Folgende Dateien oder Ordner haben keine vollen Schreibrechte (777):');
define('HEADING_NEED_TO_DELETE', 'Folgende Dateien oder Ordner m&uuml;ssen gel&ouml;scht werden:');
define('HEADING_MOVE', 'Folgende Dateien oder Ordner m&uuml;ssen verschoben werden:');
define('HEADING_RENAME', 'Folgende Dateien oder Ordner m&uuml;ssen umbenannt werden:');
define('HEADING_INSTALLATION_CLEAR_CACHE', 'Shop-Caches');
define('HEADING_DELETED_FILES', 'Gelöschte Dateien und Verzeichnisse');
define('HEADING_MOVED', 'Folgende Dateien oder Ordner wurden verschoben:');
define('HEADING_RENAMED', 'Folgende Dateien oder Ordner wurden umbenannt:');
define('HEADING_PERMISSIONS_SET', 'Folgende Dateien oder Ordner haben nun volle Schreibrechte (777):');

define('LABEL_VERSION', 'Version:');
define('LABEL_EMAIL', 'E-Mail:');
define('LABEL_PASSWORD', 'Passwort:');
define('LABEL_FTP_SERVER', 'FTP-Server:');
define('LABEL_FTP_USER', 'FTP-Benutzer:');
define('LABEL_FTP_PASSWORD', 'FTP-Passwort:');
define('LABEL_FTP_PASV', 'passiv:');
define('LABEL_DIR_UP', 'Verzeichnis nach oben');
define('LABEL_FORCE_VERSION_SELECTION', 'Versionsauswahl erzwingen');
define('DESCRIPTION_FORCE_VERSION_SELECTION', 'Wenn ein Update-Vorgang nach der Installation abgebrochen wurde oder fehlgeschlagen ist, kann mit der Option "Versionsauswahl erzwingen" ein erneuter Update-Vorgang gestartet werden. Bitte wählen Sie dann die Shopversion, die der Shop VOR dem Update hatte.');

define('TEXT_INSTALLATION_SERVICE', 'Sie m&ouml;chten die Installation nicht selbst durchf&uuml;hren? Nutzen Sie unseren Installations-Service!');
define('TEXT_INSTALLATION', 'W&auml;hlen sie die gew&uuml;nschte Sprache f&uuml;r Ihre Installation.');
define('TEXT_LOGIN', 'Melden Sie sich bitte mit der E-Mail-Adresse und dem Passwort Ihres Adminstrator-Shop-Kontos an.');
define('TEXT_LOGIN_ERROR', 'Die E-Mail-Adresse oder das Passwort ist fehlerhaft.');
define('TEXT_UPDATES', 'Folgende Updates wurden gefunden:');
define('TEXT_WHICH_VERSION', 'Welche Shopversion haben Sie aktuell?<br />');
define('TEXT_LANGUAGE', 'Sprache');
define('TEXT_SECTION_NAME', 'section-Name');
define('TEXT_PHRASE_NAME', 'Phrasen-Name');
define('TEXT_ERRORS', 'Es ist ein Fehler aufgetreten. Bitte spielen Sie Ihre Sicherung wieder ein.');
define('TEXT_ERROR_TIMEOUT', 'Maximale Ausf&uuml;hrungszeit des Servers erreicht: Update konnte nicht vollst&auml;ndig ausgef&uuml;hrt werden.');
define('TEXT_ERROR_PARSERERROR', 'Falscher R&uuml;ckgabewert:<br />');
define('TEXT_ERROR_NO_RESPONSE', 'Unbekannter R&uuml;ckgabewert: Update wurde aus unbekannten Gr&uuml;nden abgebrochen.');
define('TEXT_ERROR_500', 'Interner Server Error: Update wurde aus unbekannten Gr&uuml;nden abgebrochen.');
define('TEXT_ERROR_UNKNOWN', 'Unbekannter Fehler.');
define('TEXT_SQL_ERRORS', 'Das Update wurde nicht vollst&auml;ndig ausgef&uuml;hrt. Bitte spielen Sie Ihre Datenbanksicherung wieder ein. Folgende SQL-Fehler sind aufgetreten:');
define('TEXT_SECTION_CONFLICT_REPORT', 'Folgende Phrasen werden durch individuell angelegte section-Sprachdateien &uuml;berladen, so dass momentan die von Ihnen soeben gew&auml;hlten neuen Phrasen-Texte nicht im Shop angezeigt werden. Wenden Sie sich daher mit dieser Information an Ihren Programmierer.<br />');
define('TEXT_DELETE_LIST', 'Bitte l&ouml;schen Sie nun folgende Dateien bzw. Verzeichnisse von Ihrem Server:');
define('TEXT_INSTALLATION_SUCCESS_WARNING', '<br /><span class="warning" style="display:inline-block">Wenn Sie das Modul StyleEdit verwenden, muss dieses im Rahmen des Service Packs aktualisiert werden.<br /><br /><u><b>Findet die Aktualisierung des StyleEdits nicht statt, treten Fehler im Shop auf</u></b>. Bitte beachten Sie hierzu den Abschnitt &quot;Installation Bearbeitungsmodus StyleEdit&quot; der Installationsanleitung.<br /><br /><br /><u><b>Nach Abschluss des Updates müssen die Originalvorlagen für die E-Mail-Vorlagen &quot;Bestellbestätigung&quot; und &quot;Admin: Änderung Bestellstatus&quot; wiederhergestellt werden, da es sonst zu Fehlern im Shop kommen kann.</u></b><br /><br />Gehen Sie hierzu im Gambio Admin unter <b>Shop-Einstellungen &gt; E-Mail-Optionen &gt; E-Mail-Vorlagen</b> und wählen für die genannten Vorlagen &quot;Original wiederherstellen&quot; aus und klicken auf OK. Führen Sie dies bitte sowohl für die HTML- als auch die Textvorlagen in beiden Sprachen (Deutsch und Englisch) durch. Leeren Sie abschließend unter <b>Toolbox &gt; Cache leeren</b> den Cache für die E-Mail-Vorlagen. Bitte beachten Sie, dass individuelle Änderungen hierdurch verloren gehen und ggf. erneut vorgenommen werden müssen.<br /><br /><br />Leeren Sie abschließend den Cache Ihres Browsers, um Darstellungsfehler zu vermeiden.</span>');
define('TEXT_INSTALLATION_SUCCESS', 'Alle Updates wurden erfolgreich installiert.');
define('TEXT_INSTALLATION_SUCCESS_CACHE_REBUILD_ERROR', 'Alle Updates wurden erfolgreich installiert.<p style="color: red;" >Achtung: Die Caches konnten nicht geleert werden.<br />Bitte leeren Sie die Caches im Gambio Admin!</p>');
define('TEXT_PROGRESS', 'Bitte haben Sie ein wenig Geduld. Folgendes Update wird gerade installiert: ');
define('TEXT_SET_PERMISSIONS', 'Sie k&ouml;nnen die Rechte entweder selbst mit einem FTP-Programm oder &uuml;ber die FTP-Funktion des Updaters setzen. F&uuml;r Letzteres geben Sie bitte im folgenden Formular Ihre FTP-Daten ein und klicken auf &quot;Verbinden&quot;.<br />
Anschlie&szlig;end navigieren Sie zum Verzeichnis, in dem sich der Shop befindet und starten die Rechtevergabe, indem Sie auf den Button &quot;Rechte setzen&quot; klicken. ');
define('TEXT_DELETE_FILES', 'Sie k&ouml;nnen die Dateien und Ordner entweder selbst mit einem FTP-Programm oder &uuml;ber die FTP-Funktion des Updaters l&ouml;schen. F&uuml;r Letzteres geben Sie bitte im folgenden Formular Ihre FTP-Daten ein und klicken auf &quot;Verbinden&quot;.<br />
Anschlie&szlig;end navigieren Sie zum Verzeichnis, in dem sich der Shop befindet und starten den L&ouml;schvorgang, indem Sie auf den Button &quot;Veraltete Dateien l&ouml;schen&quot; klicken. ');
define('TEXT_MOVE', 'Sie k&ouml;nnen die &Auml;nderungen mit einem FTP-Programm oder &uuml;ber die FTP-Funktion des Updaters durchf&uuml;hren. F&uuml;r Letzteres geben Sie bitte im folgenden Formular Ihre FTP-Daten ein und klicken auf &quot;Verbinden&quot;.<br />
Anschlie&szlig;end navigieren Sie zum Verzeichnis, in dem sich der Shop befindet und starten den Vorgang, indem Sie auf den Button &quot;Durchf&uuml;hren&quot; klicken. ');
define('TEXT_NO_CONFIGURATION', 'Die zu installierenden Updates haben keine Konfiguration. Fahren Sie mit Klick auf &quot;' . BUTTON_INSTALL . '&quot; fort.');
define('TEXT_NO_UPDATES', 'Es wurden keine installierbaren Updates für Ihre aktuelle Shopversion gefunden.');
define('TEXT_PERMISSIONS_OK', 'Die Dateirechte sind korrekt gesetzt.');
define('TEXT_DELETE_FILES_OK', 'Veraltete Dateien und Ordner wurden gel&ouml;scht.');
define('TEXT_MOVE_OK', 'Das Umbenennen bzw. Verschieben wurde erfolgreich durchgef&uuml;hrt.');
define('TEXT_CURRENT_DIR', 'aktuelles Verzeichnis: ');
define('TEXT_TEMPLATE_NOTIFICATION', 'Ihr aktuelles Template scheint ohne Anpassungen nicht kompatibel mit der neuen Architektur zu sein. Daher wird vorerst das EyeCandy-Template aktiviert.');
define('TEXT_SKIP', 'Sie können die Installation fortsetzen, wenn Sie sicher sind, dass die Rechte bereits korrekt gesetzt sind und deren Erkennung aus technischen Gründen fehlschlägt.');
define('TEXT_INSTALLATION_CLEAR_CACHE', 'Bitte haben Sie ein wenig Geduld, bis die Caches des Shops neu aufgebaut wurden...');
define('TEXT_DELETED_FILES', 'Die gelisteten Dateien bzw. Verzeichnisse wurden gelöscht. Sie können sich eine Sicherung über den Button "Dateien downloaden" herunterladen.<br /><br />Klicken Sie auf den Button "Fortfahren", um die Installation fortzusetzen.');
define('TEXT_PERMISSIONS_SET', 'Die gelisteten Dateien bzw. Verzeichnisse haben nun die korrekten Schreibrechte.<br /><br />Klicken Sie auf den Button "Fortfahren", um die Installation fortzusetzen.');
define('TEXT_NOT_ALL_FILES_UPLOADED', 'Das Service Pack wurde nicht vollständig hochgeladen. Bitte laden Sie das gesamte Service Pack noch einmal auf Ihren Webserver');
define('TEXT_NOT_ALL_SE_V2_FILES_UPLOADED', 'Das StyleEdit Modul wurde nicht vollständig hochgeladen. Bitte laden Sie das gesamte StyleEdit Verzeichnis noch einmal auf Ihren Webserver');
define('TEXT_NOT_ALL_SE_V3_FILES_UPLOADED', 'Das StyleEdit3 Modul wurde nicht vollständig hochgeladen. Bitte laden Sie das gesamte StyleEdit3 Verzeichnis noch einmal auf Ihren Webserver');

define('TEXTCONFLICTS_LABEL', 'Textkonflikte');
define('TEXTCONFLICTS_TEXT', 'Welche Textversion m&ouml;chten Sie &uuml;bernehmen?');
define('TEXTCONFLICTS_OLD', 'alt');
define('TEXTCONFLICTS_NEW', 'neu');

define('ERROR_FTP_CONNECTION', 'Es konnte keine FTP-Verbindung zu \'%s\' hergestellt werden. &Uuml;berpr&uuml;fen Sie die FTP-Adresse!');
define('ERROR_FTP_DATA', 'Der FTP-Benutzer \'%s\' oder das FTP-Passwort ist falsch!');
define('ERROR_FTP_NOT_INSTALLED', 'Der Server unterst&uuml;tzt leider kein FTP. Bitte wenden Sie sich an Ihren Provider mit der Bitte die FTP-Funktionen f&uuml;r PHP freizuschalten.<br />Alternativ k&ouml;nnen Sie jetzt die Anpassungen mit einem FTP-Programm durchf&uuml;hren. Klicken Sie anschlie&szlig;end auf &quot;Erneut pr&uuml;fen&quot;, um den Update-Prozess fortzuf&uuml;hren.');
define('ERROR_FTP_NO_LISTING', 'Der Server kann leider keine Verzeichnisse per FTP auslesen. Ursache k&ouml;nnte z. B. eine serverseitige Firewall sein. Bitte wenden Sie sich an Ihren Provider mit der Bitte die PHP-Funktion &quot;ftp_nlist&quot; zu &uuml;berpr&uuml;fen.<br />Alternativ k&ouml;nnen Sie jetzt die Anpassungen mit einem FTP-Programm durchf&uuml;hren. Klicken Sie anschlie&szlig;end auf &quot;Erneut pr&uuml;fen&quot;, um den Update-Prozess fortzuf&uuml;hren.');

define('ERROR_SQL_UNKNOWN', 'Ein unbekannter Fehler ist während des Updates "x.x.x.x" aufgetreten.');

define('LABEL_FORCE_VERSION_SELECTION_BTN', 'Versionsauswahl erzwingen');

define('REQUIREMENT_WARNING', '<p>Für den Gambio-Shops wird mindestens <strong>PHP ###minPHPVersion### </strong> und <strong>MySQL ###minMySQLVersion###</strong> benötigt.</p><p>Ihre PHP Version: <strong>###yourPHPVersion###</strong><br />Ihre MySQL Version: <strong>###yourMySQLVersion###</strong></p><p>Bitte kontaktieren Sie Ihren Provider, um die entsprechenden Versionen anzupassen.</p>');

define('TEXT_PAYPAL_NOTIFICATION', '<span class="warning" style="display:inline-block"><strong>ACHTUNG: Sie nutzen ein veraltetes PayPal-Modul!</strong><br /><p>Wir empfehlen Ihnen, ab sofort das aktuelle PayPal-Modul einzusetzen. Sie können es jetzt im Admin unter Module -> Zahlungsweisen aktivieren.</p><p>Weitere Informationen: <a href="https://www.gambio.de/7pM9Y" target="_blank">https://www.gambio.de/7pM9Y</a></p></span>');