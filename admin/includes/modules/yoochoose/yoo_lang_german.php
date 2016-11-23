<?php

/* --------------------------------------------------------------
   YOOCHOOSE GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 YOOCHOOSE GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */
 
define('YOOCHOOSE_MODULE_NAME', 'YOOCHOOSE Empfehlungen'); // for admin menu column on the left side. 


// ADMIN-BEREICH Menu

define('YOOCHOOSE_ADMIN_HEADER', 'YOOCHOOSE Empfehlungsdienst');

define('YOOCHOOSE_MENU_STATISTIC', 'Statistik');                                                 
define('YOOCHOOSE_MENU_UPLOAD',    'Upload');
define('YOOCHOOSE_MENU_MODELS',    'Empfehlungen');
define('YOOCHOOSE_MENU_CONFIG',    'Einstellungen');
define('YOOCHOOSE_MENU_INFO',      'Beschreibung');
define('YOOCHOOSE_MENU_FAQ',       'FAQ');
define('YOOCHOOSE_MENU_CHECK',     'Test');
define('YOOCHOOSE_MENU_REGISTER',  'Registrierung');
define('YOOCHOOSE_MENU_CONTACT',   'Kontakt');

// ADMIN-BEREICH Einstellungen

define('YOOCHOOSE_ID_TITLE', 'Kundennummer');
define('YOOCHOOSE_ID_DESC', 
    'Ihre YOOCHOOSE Kundennummer, die Sie per E-Mail nach der Registrierung erhalten haben. <br/>
     <a target="_blank" href="%1$s">Noch nicht registriert?</a>');

define('YOOCHOOSE_ACTIVE_TITLE', 'Aktiv');
define('YOOCHOOSE_ACTIVE_DESC', 
    'Es werden nur dann YOOCHOOSE Empfehlungen angezeigt und Statistiken gesammelt, wenn der Dienst aktiviert ist.');

define('YOOCHOOSE_SECRET_TITLE', 'Lizenzschlüssel');
define('YOOCHOOSE_SECRET_DESC', 
    'Mit dem Lizenzschlüssel weisen Sie Ihre Identität nach. Er verhindert das unbefugte Lesen und 
     Ändern Ihrer Einstellungen. Geben Sie Ihren Lizenzschlüssel daher niemals an Dritte weiter!');

define('YOOCHOOSE_LICENSE_TITLE', 'Lizenztyp');
define('YOOCHOOSE_LICENSE_DESC', 
    'Ihr aktueller Lizenztyp. Ändern Sie Ihre Lizenz auf <a href="%1$s">unserem Service-Portal</a>.');

define('YOOCHOOSE_LOG_LEVEL_TITLE',  'Log-Level');
define('YOOCHOOSE_LOG_LEVEL_DESC',   'Definiert das Log-Level für die Dateien /export/recommendations-*.log');

define('YOOCHOOSE_EVENT_SERVER_TITLE', 'Event Service');
define('YOOCHOOSE_EVENT_SERVER_DESC', 'Default Value: %1$s');

define('YOOCHOOSE_RECO_SERVER_TITLE', 'Recomendation Service');
define('YOOCHOOSE_RECO_SERVER_DESC', 'Default Value: %1$s');

define('YOOCHOOSE_REG_SERVER_TITLE', 'Configuration Service');
define('YOOCHOOSE_REG_SERVER_DESC', 'Default Value: %1$s');

define('YOOCHOOSE_PREF_BTN', 'Speichern'); // Submit button

// ADMIN-BEREICH Modell

define('YOOCHOOSE_MODELS_LANDING_PAGE', 'Startseite');
define('YOOCHOOSE_MODELS_SPECIAL_OFFER', 'Angebote');
define('YOOCHOOSE_MODELS_CATEGORY_TITLE', 'Kategoreübeschrift');
define('YOOCHOOSE_MODELS_NEW_IN_SHOP', 'Neue Artikel');
define('YOOCHOOSE_MODELS_CATEGORY_PAGE', 'Kategorie');
define('YOOCHOOSE_MODELS_MENU', 'Menü');
define('YOOCHOOSE_MODELS_PRODUCTS', "Artikelliste");
define('YOOCHOOSE_MODELS_PRODUCT', "Artikel");

define('YOOCHOOSE_MODELS_SHOPPING_CART', "Warenkorb");

define('YOOCHOOSE_MODELS_MAIN_MENU', 'Hauptmenü');
define('YOOCHOOSE_MODELS_LOGIN', 'Login');

define('YOOCHOOSE_STATISTIC_HEADER', 'Empfehlungsdienstsstatistik');
define('YOOCHOOSE_STATISTIC_ADV', 'Erweiterte Statistik');
define('YOOCHOOSE_STATISTIC_ADV_TEXT', 'Mehr information steht auf <a href="http://admin.yoochoose.net" target="_blank">YOOCHOOSE Administrator Dashboard</a> zur Verfügung.');


define('YOOCHOOSE_UPLOADER_HEADER', 'Verkaufsstatistik hochladen');

define('YOOCHOOSE_JSON_MISSING', 
    'Die Funktion <code>json_decode()</code> ist nicht installiert. Bitte aktualisieren Sie 
     die PHP-Version (mindestens 5.2) oder installieren Sie das JSON-Paket manuell (Details unter http://pecl.php.net/package/json).');

define('YOOCHOOSE_CURL_MISSING', 
    'Die <code>curl</code>-Erweiterung ist nicht installiert. Bitte folgen Sie der 
    Installationsanleitung unter http://www.php.net/manual/de/curl.installation.php');

define('YOOCHOOSE_CONNECTION_ERROR', 
'Der Empfehlungsdienst von YOOCHOOSE ist aus ihrem Netzwerk nicht erreichbar.
<br><br>
Bitte überprüfen Sie die Zugangsdaten, die Sie bei der Registrierung erhalten haben.
Falls dieses Problem dennoch bestehen bleibt, wenden Sie sich bitte an ihren Netzwerk- oder Systemadministrator.
<br><br>
HTTP Status: %1$s');

define('YOOCHOOSE_STATISTIC_EMPTY', 
    'Nach der Aktivierung kann es ca. 10 Minuten dauern, bis die ersten Ereignisse 
    vorliegen und der Dienst genutzt werden kann.');


define('YOOCHOOSE_ADVANCED_SETTINGS', 'Erweiterte Einstellungen');

define('YOOCHOOSE_ERROR_LOADING_STRATEGIES', 
        'Die verfügbaren Scenarien können nicht geladen werden. 
         Überprüfen Sie Ihre Kundennummer und Lizenzschlüssel.<br>Fehlermeldung: %1$s');

define('YOOCHOOSE_ERROR_STRATEGY_NOT_FOUND',
		'Empfehlungsscenario [%1$s] wurde nicht gefunden.');
		
define('YOOCHOOSE_STRATEGY_NOT_FOUND_TIP',
		'Nutze <a href="http://admin.yoochoose.net" target="_blank">Yoochoose Adminstrator Dashboard</a>, um fehlende Empfehlungsscenarios zu erstellen.');

define('YOOCHOOSE_BOX_NOT_CONFIGURED_TIP',
	    'Fehlende Menuboxen müssen in [/templates/<yourtemplate>/template_settings.php] configuriert werden.');

define('YOOCHOOSE_ERROR_TEMPLATE_NOT_PREPARED',
		'Empfehlung-Platzhalter wurde in der Templatedatei %2$s von dem Template %1$s nicht gefunden. Bitte fügen den folgenden Code hinzu:<br> %3$s');

// ADMIN-BEREICH Recommendation strategies

define('YOOCHOOSE_EVENT_1', 'Ereignisse "Artikel geklickt"');
define('YOOCHOOSE_EVENT_2', 'Ereignisse "Artikel gekauft"');
define('YOOCHOOSE_EVENT_3', 'Ereignisse "Artikel konsumiert"');
define('YOOCHOOSE_EVENT_4', 'Ereignisse "Artikel bewertet"');
define('YOOCHOOSE_EVENT_5', 'Ereignisse "empfohlenen Artikel geklickt"');
define('YOOCHOOSE_DELIVERED_RECO_ALSO_PURCHASED', 'Empfehlungen "Kunden kauften auch"');
define('YOOCHOOSE_DELIVERED_RECO_TOP_SELLING', 'Empfehlungen "häufig gekauft"');
define('YOOCHOOSE_DELIVERED_RECO_ALSO_CLICKED', 'Empfehlungen "Kunden klicken auch"');
define('YOOCHOOSE_DELIVERED_RECO_ULTIMATELY_PURCHASED', 'Empfehlungen "anschließend gekauft"'); // ultimately_purchased

define('YOOCHOOSE_NOT_AVAILABLE', 'nicht verfügbar');

define('YOOCHOOSE_CROSS_SELL_RECOMMENDATION', 'Produktempfehlungen');
define('YOOCHOOSE_RECOMMENDATION_EMPTY', 'Zu diesem Produkt gibt es bislang keine Empfehlungen');
define('YOOCHOOSE_REQUEST_RECOMMENDATION', 'Empfehlen!');


define('YOOCHOOSE_UNABLE_PARSE_AS_DATE', 'Der Text "%1$s" ist kein valides Datum nach dem "%2$s" Format.');
define('YOOCHOOSE_UNABLE_PARSE_EMPTY_AS_DATE', 'Leeres Feld ist kein valides Datum.');
define('YOOCHOOSE_VALIDATING_DDD', 'Validating...');

define('YOOCHOOSE_GENERATING_USAGE_DATA', 'Verkaufsstatistik wird generiert. Schritt %1$s von 10.');
define('YOOCHOOSE_GENERATING_USAGE_DATA_FINISHED', 'Verkaufsstatistik wird generiert. Schritt %1$s von 10 ist abgeschlossen.');

define('YOOCHOOSE_GENERATING_FINISHED',  'Export ist abgeschlossen <a href="%1$s">(Link)</a>.');
define('YOOCHOOSE_MD5_SCHEDULING', 'MD5 wird generiert, das Hochladen wird vorbereitet.');
define('YOOCHOOSE_MD5_FINISHED', 'MD5 Hash ist fertig <a href="%1$s">(Link)</a>.');
define('YOOCHOOSE_UPLOAD_SCHEDULED', 'Hochladen ist angestoßen.');
define('YOOCHOOSE_UPL_FROM', 'Von (%1$s)');
define('YOOCHOOSE_UPL_TO', 'Bis (%1$s)');
define('YOOCHOOSE_UPL_BTN', 'Hochladen');
define('YOOCHOOSE_VALIDATING_DDD', 'Validierung...');

define('YOOCHOOSE_USAGE_DATA_UPLOAD_INFO', 
'Die Verkaufsstatistik sollte unmittelbar nach der Aktivierung des Empfehlungsdienstes hochgeladen werden, damit zu Beginn genügend Daten zur Verfügung stehen. Normalerweise sollte dieser Vorgang nicht wiederholt werden..');
// Laden Sie Ihre Verkaufsstatistik hoch, nachdem Sie den Empfehlungsdienst aktiviert haben.

define('YOOCHOOSE_INTERVAL_NEGATIVE_ERR', 'Das Datum "bis" muss nach dem Datum "von" liegen.');

define('YOOCHOOSE_ESTIMATED_OVERSIZE', 'Voraussichtliche Dateigröße ist %1$s. Das Hochladen über %2$s Mb ist nicht empfohlen. Reduzieren Sie die Zeitspanne.');
define('YOOCHOOSE_PRODUCTS_TO_UPLOAD', 'Produkte zum Hochladen: %1$s. Vorausichtliche Dateiengröße ist: %2$s.');

define('YOOCHOOSE_UPLOAD_FAILED', 'Fehler während der Übemittlung an den Empfehlungsdienst. %1$s');

define('YOOCHOOSE_REST_SERVER_TITLE', 'Empfehlungsdienst-Server');
define('YOOCHOOSE_REST_SERVER_DESC', 'Servername mit dem Protokoll und der Portnummer (wenn notwendig). Standardwert: "https://test.yoochoose.net"');





define('YOOCHOOSE_CONFIG_HEADER', 'Einstellungen');

define('YOOCHOOSE_REGISTER_BTN', 'Kostenlos registrieren');
define('YOOCHOOSE_REGISTER_BTN_12', 'Kostenlos registrieren');

define('YOOCHOOSE_ACTIVATE_BTN', 'Aktivieren');
define('YOOCHOOSE_ACTIVATE_CONTENT',   
        'Ich habe bereits eine<br> YOOCHOOSE Kundennummer.');

define('YOOCHOOSE_TOO_OLD_PHP_VERSION',
    'Ihre PHP-Version ist zu alt für dieses Modul. Das YOOCHOOSE Modul benötigt mindestens PHP Version %2$s, ihre Version ist %1$s.');
    
define('YOOCHOOSE_DATETIME_MISSING',
    'Für das Modul wird die Klasse DateTime benötigt. Sie wurde in Ihrer PHP-Installation nicht gefunden. Prüfen Sie bitte Ihre Konfiguration.');




define('YOOCHOOSE_CONF_SERVER_NOT_ACCESSIBLE', 'YOOCHOOSE Configuration Server ist nicht erreichbar. Prüfen Sie Ihre Firewall Einstellungen.');
define('YOOCHOOSE_CONF_UNAUTHORIZED', 'Authentifizierungsfehler. Prüfen Sie Ihren Kundennummer und Lizenzschlüssel. Fehlermeldung: %1$s');
define('YOOCHOOSE_CONF_FORBIDDEN', 'Zugang verweigert. Prüfen Sie Ihren Kundennummer und Lizenzschlüssel. Fehlermeldung: %1$s');             
define('YOOCHOOSE_CONF_NOT_FOUND', 'Resource wurde nicht gefunden. Prüfen Sie die Adresse von Configuration Server. Fehlermeldung: %1$s');               
define('YOOCHOOSE_CONF_NOT_UNKNOWN', 'Die Lizenz kann nicht geladen werden. Ursache unbekannt (%2$d). Bitte versuchen Sie später noch einmal. %1$s');
define('YOOCHOOSE_JSON_ERROR', 'Unerwarteter Fehler. Server responce cannot be parsed. %1$s');

define('YOOCHOOSE_STRATEGY', 'Empfehlungsscenario');
define('YOOCHOOSE_MAX_RECOMMENDATIONS', 'Angezeigte Artikel:');
define('YOOCHOOSE_BOX_ENABLED', 'Aktiv');

define('YOOCHOOSE_HAVE_BOUGHT', 'haben gekauft'); // 40% haben gekauft.

?>