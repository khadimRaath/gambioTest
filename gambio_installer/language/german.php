<?php
/* --------------------------------------------------------------
   german.php 2015-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

# Button-Labels
define('BUTTON_BACK', 'Zur&uuml;ck');
define('BUTTON_CHECK_MISSING_FILES', 'Erneut &uuml;berpr&uuml;fen');
define('BUTTON_CHECK_PERMISSIONS', 'Rechte erneut &uuml;berpr&uuml;fen');
define('BUTTON_CONNECT', 'Verbinden');
define('BUTTON_CONNECT_NEW', 'Neu verbinden');
define('BUTTON_CONTINUE', 'Installation fortfahren');
define('BUTTON_DOWNLOAD', 'Download');
define('BUTTON_ENGLISH', 'English');
define('BUTTON_FINISH', 'Shopeinrichtung abschlie&szlig;en');
define('BUTTON_GAMBIO_PORTAL', 'Zum Gambio Kundenportal');
define('BUTTON_GERMAN', 'Deutsch');
define('BUTTON_OPEN_SHOP', 'Zum Shop');
define('BUTTON_SET_PERMISSIONS', 'Rechte setzen');
define('BUTTON_START', 'Shopeinrichtung starten');
define('BUTTON_SKIP', 'Installationsfortsetzung erzwingen');


# Headlines
define('HEADING_INSTALLATION_SERVICE', 'Gambio Installations-Service');
define('HEADING_INSTALLATION', 'Zur Installation');
define('HEADING_WRONG_PERMISSIONS', 'Folgende Dateien oder Ordner haben keine vollen Schreibrechte (777):');
define('HEADING_FTP_DATA', 'FTP-DATEN');
define('HEADING_REMOTE_CONSOLE', 'Remote-Konsole');
define('HEADING_DATABASE', 'DATENBANKINFORMATIONEN');
define('HEADING_SHOP_INFORMATION', 'SHOPINFORMATIONEN');
define('HEADING_ADMIN_DATA', 'SHOPBETREIBERDATEN');
define('HEADLINE_ROBOTS', 'ROBOTS.TXT ANLEGEN');
define('HEADING_SUCCESS', 'Shopeinrichtung war erfolgreich');
define('HEADING_REGISTER_GLOBALS', 'Sicherheitsrisiko festgestellt');
define('HEADING_PROGRESS', 'Shopdatenbank wird eingerichtet');


# Texts
define('TEXT_INSTALLATION_SERVICE', 'Sie m&ouml;chten die Installation nicht selbst durchf&uuml;hren? Nutzen Sie unseren Installations-Service!');
define('TEXT_INSTALLATION', 'W&auml;hlen sie die gew&uuml;nschte Sprache f&uuml;r Ihre Installation.');
define('TEXT_SET_PERMISSIONS', 'Sie k&ouml;nnen die Rechte entweder selbst mit einem FTP-Programm oder &uuml;ber die FTP-Funktion des Installers setzen.
F&uuml;r Letzteres geben Sie bitte im folgenden Formular Ihre FTP-Daten ein und klicken auf &quot;Verbinden&quot;.<br />
Anschlie&szlig;end navigieren Sie zum Verzeichnis, in dem sich der Shop befindet und starten die Rechtevergabe, indem Sie auf den Button &quot;Rechte setzen&quot; klicken.');
define('TEXT_ROBOTS','Klicken Sie auf &quot;Download&quot;, um die robots.txt f&uuml;r Ihren Shop zu generieren und herunterzuladen.
Laden Sie die Datei mit einem FTP-Programm in das Haupt-Verzeichnis Ihres Webservers.
Die Datei muss anschlie&szlig;end unter folgendem Link erreichbar sein: <a href="http://' . getenv('HTTP_HOST') . '/robots.txt" target="_blank">http://' . getenv('HTTP_HOST') . '/robots.txt</a>');
define('TEXT_SUCCESS','Wir gratulieren Ihnen zur Installation Ihres neuen Onlineshops und w&uuml;nschen Ihnen viel Erfolg und gute Ums&auml;tze!<br />Ihr Gambio.de Service-Team.');
define('TEXT_FINAL_SETTINGS', 'Finale Einrichtung l&auml;uft...bitte warten.');
define('TEXT_WRITE_ROBOTS_FILE', 'robots.txt wird versucht automatisch anzulegen...bitte warten.');
define('TEXT_TABLES_EXIST', 'In der folgenden Auflistung rot markierte Tabellen werden im n&auml;chsten Schritt unwiderruflich gel&ouml;scht! Enthaltene Daten gehen verloren!');
define('TEXT_MISSING_FILES', 'Folgende Dateien oder Ordner fehlen. Laden Sie diese mit einem FTP-Programm auf Ihren Server und klicken anschlie&szlig;en auf &quot;Erneut &uuml;berpr&uuml;fen&quot;, um die Vollst&auml;ndigkeit sicherzustellen.');
define('TEXT_REGISTER_GLOBALS', '&quot;register_globals&quot; ist in der Konfiguration Ihres Shopservers aktiviert. Dies stellt ein Sicherheitsrisiko dar. Wir empfehlen Ihnen sich an Ihren Provider zu wenden, damit dieser &quot;register_globals&quot; f&uuml;r Ihren Server deaktiviert.');
define('TEXT_PROGRESS', 'Dieser Vorgang kann mehrere Minuten dauern und sollte nicht abgebrochen werden.');
define('TEXT_SKIP', 'Sie können die Installation fortsetzen, wenn Sie sicher sind, dass die Rechte bereits korrekt gesetzt sind und deren Erkennung aus technischen Gründen fehlschlägt.');


# Form-Labels
define('LABEL_FTP_SERVER', 'FTP-Server:');
define('LABEL_FTP_USER', 'FTP-Benutzer:');
define('LABEL_FTP_PASSWORD', 'FTP-Passwort:');
define('LABEL_FTP_PASV', 'passiv:');
define('LABEL_DIR_UP', 'Verzeichnis nach oben');
define('LABEL_DB_SERVER', 'Server:');
define('LABEL_DB_USER', 'Benutzer:');
define('LABEL_DB_PASSWORD', 'Passwort:');
define('LABEL_DB_DATABASE', 'Datenbank:');
define('LABEL_HTTP_SERVER', 'HTTP-Server:');
define('LABEL_SSL', 'SSL aktivieren:');
define('LABEL_HTTPS_SERVER', 'HTTPS-Server:');
define('LABEL_GENDER', 'Anrede:');
define('LABEL_MALE', 'Herr');
define('LABEL_FEMALE', 'Frau');
define('LABEL_FIRSTNAME', 'Vorname:');
define('LABEL_LASTNAME', 'Nachname:');
define('LABEL_EMAIL', 'E-Mail:');
define('LABEL_STREET', 'Stra&szlig;e:');
define('LABEL_STREET_NUMBER', 'Hausnummer:');
define('LABEL_POSTCODE', 'PLZ:');
define('LABEL_CITY', 'Ort:');
define('LABEL_STATE', 'Bundesland:');
define('LABEL_COUNTRY', 'Land:');
define('LABEL_TELEPHONE', 'Telefon:');
define('LABEL_PASSWORD', 'Passwort:');
define('LABEL_CONFIRMATION', 'Wiederholung:');
define('LABEL_SHOP_NAME', 'Shopname:');
define('LABEL_COMPANY', 'Firma:');
define('LABEL_EMAIL_FROM', 'Absender-E-Mail:');
define('LABEL_FORCE_DB', 'Trotzdem fortfahren!');


# Error messages
define('ERROR_SESSION_SAVE_PATH', 'Die Session konnte nicht gestartet werden. Bitte setzen Sie die Dateirechte des Ordners %s auf 777 (volle Schreib- und Leserechte).');
define('ERROR_SET_PERMISSIONS_FAILED', 'Das Setzen der Rechte ist leider fehlgeschlagen. Versuchen Sie die Rechte nun manuell zu setzen.');
define('ERROR_TABLES_EXIST', 'Die Datenbank enth&auml;lt bereits Tabellen!');
define('ERROR_FTP_CONNECTION', 'Es konnte keine FTP-Verbindung zu \'%s\' hergestellt werden. &Uuml;berpr&uuml;fen Sie die FTP-Adresse!');
define('ERROR_FTP_DATA', 'Der FTP-Benutzer \'%s\' oder das FTP-Passwort ist falsch!');
define('ERROR_UNEXPECTED', 'Ein unerwarteter Fehler ist aufgetreten. Beginnen Sie die Installation nochmals.');
define('ERROR_CONFIG_FILES', 'Die Konfigurationsdateien konnten nicht geschrieben werden, da sie keine Schreibrechte (777) haben.');
define('ERROR_MISSING_FILES', 'Shop unvollst&auml;ndig hochgeladen');
define('ERROR_DB_QUERY', '-Befehle k&ouml;nnen aufgrund fehlender Rechte des MySQL-Benutzers nicht ausgef&uuml;hrt werden. Wenden Sie sich an Ihren Provider mit der Bitte die Rechte des MySQL-Benutzers entsprechend anzupassen.');

define('ERROR_INPUT_DB_CONNECTION', 'Server, Benutzer oder Passwort sind ung&uuml;ltig');
define('ERROR_INPUT_DB_DATABASE', 'Datenbank existiert nicht');
define('ERROR_INPUT_SERVER_URL', 'Shop unter dieser Adresse nicht erreichbar');
define('ERROR_INPUT_SERVER_HTTPS', 'SSL-Aktivierung ohne g&uuml;ltiges Zertifikat kann Probleme verursachen');
define('ERROR_INPUT_MIN_LENGTH_2', 'Mindestzeichenanzahl von 2 nicht erreicht');
define('ERROR_INPUT_EMAIL', 'E-Mail-Adresse ung&uuml;ltig');
define('ERROR_INPUT_MIN_LENGTH_3', 'Mindestzeichenanzahl von 3 nicht erreicht');
define('ERROR_INPUT_MIN_LENGTH_4', 'Mindestzeichenanzahl von 4 nicht erreicht');
define('ERROR_INPUT_MIN_LENGTH_5', 'Mindestzeichenanzahl von 5 nicht erreicht');
define('ERROR_INPUT_PASSWORD_CONFIRMATION', 'Wiederholung und Passwort nicht identisch');
define('ERROR_MEMORY_LIMIT', '&quot;memory_limit&quot; zu niedrig');
define('ERROR_TEXT_MEMORY_LIMIT', 'In der Serverkonfiguration ist f&uuml;r das &quot;memory_limit&quot; ein zu niedriger Wert gesetzt, um alle Funktionen des Shops nutzen zu k&ouml;nnen. Wir empfehlen als Wert mindestens %sM.<br />Wenden Sie sich an Ihren Provider mit der Bitte das &quot;memory_limit&quot; entsprechend zu erh&ouml;hen.');

define('REQUIREMENT_WARNING', '<p>Für den Gambio-Shops wird mindestens <strong>PHP ###minPHPVersion### </strong> benötigt.</p>
<p>Ihre PHP Version: <strong>###yourPHPVersion###</strong>
<p>Bitte kontaktieren Sie Ihren Provider, um die entsprechenden Versionen anzupassen.</p>');