<?php
/* --------------------------------------------------------------
   Yoochoose GmbH
   http://www.yoochoose.com
   Copyright (c) 2011 Yoochoose GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */ 


define('YOOCHOOSE_MODULE_NAME', 'YOOCHOOSE Recommendations');

// ADMIN-BEREICH Menu

define('YOOCHOOSE_MENU_STATISTIC', 'Statistic');
define('YOOCHOOSE_MENU_MODELS',    'Recommendations');
define('YOOCHOOSE_MENU_UPLOAD',    'Upload');
define('YOOCHOOSE_MENU_CONFIG',    'Configuration');
define('YOOCHOOSE_MENU_INFO',      'Description');
define('YOOCHOOSE_MENU_FAQ',       'FAQ');
define('YOOCHOOSE_MENU_CHECK',     'Test');
define('YOOCHOOSE_MENU_REGISTER',  'Register');
define('YOOCHOOSE_MENU_CONTACT',   'Contact');



// ADMIN-BEREICH Einstellungen

define('YOOCHOOSE_ID_TITLE', 'Customer ID');
define('YOOCHOOSE_ID_DESC', 
    'Fill in your personal Yoochoose customer id we sent you after your registration<br/>
    <a target="_blank" href="http://config.yoochoose.net/registration/registration.jsp?role=gambio">New customer? It only takes two minutes to get started!</a>');
define('YOOCHOOSE_ACTIVE_TITLE', 'Active');
define('YOOCHOOSE_ACTIVE_DESC',
    'If deactivated, YOOCHOOSE Recommendation won\'t be shown and purchase history won\'t be collected.');
define('YOOCHOOSE_SECRET_TITLE', 'License key');
define('YOOCHOOSE_SECRET_DESC', 'The key to protect your settings from unauthorized access. Make sure your key is never passed on to third parties!');
define('YOOCHOOSE_LICENSE_TITLE', 'License type');
define('YOOCHOOSE_LICENSE_DESC', 'Your current license type. To change your license, please visit <a href="%1$s">our service site</a>');

define('YOOCHOOSE_REG_SERVER_TITLE', 'Configuration Service URL');
define('YOOCHOOSE_REG_SERVER_DESC', 
    'Full URL for YOOCHOOSE Configuration Service. 
     Deafult value: %1$s');

define('YOOCHOOSE_EVENT_SERVER_TITLE', 'Event Service URL');
define('YOOCHOOSE_EVENT_SERVER_DESC', 
    'Full URL for YOOCHOOSE Event Service 
     Deafult value: %1$s');

define('YOOCHOOSE_RECO_SERVER_TITLE', 'Recommendation Service URL');
define('YOOCHOOSE_RECO_SERVER_DESC', 
    'Full URL for YOOCHOOSE Recommendation Service. 
     Deafult value: %1$s');


define('YOOCHOOSE_PREF_BTN', 'Sumbit');

// ADMIN-BEREICH Modell

define('YOOCHOOSE_MODELS_LANDING_PAGE', 'Landing Page');
define('YOOCHOOSE_MODELS_SPECIAL_OFFER', 'Specials');
define('YOOCHOOSE_MODELS_CATEGORY_TITLE', 'Category Title');
define('YOOCHOOSE_MODELS_NEW_IN_SHOP', 'New Products');
define('YOOCHOOSE_MODELS_CATEGORY_PAGE', 'Category Page');
define('YOOCHOOSE_MODELS_MENU', 'Menu');
define('YOOCHOOSE_MODELS_PRODUCTS', "Products");
define('YOOCHOOSE_MODELS_PRODUCT', "Product");

define('YOOCHOOSE_MODELS_SHOPPING_CART', "Shopping Cart");


define('YOOCHOOSE_MODELS_MAIN_MENU', 'Main Menu');
define('YOOCHOOSE_MODELS_LOGIN', 'Login');

define('YOOCHOOSE_STATISTIC_HEADER', 'Recommendation Service Statistic');
define('YOOCHOOSE_STATISTIC_ADV', 'Advanced Statistic');
define('YOOCHOOSE_STATISTIC_ADV_TEXT', 'Additional statistic is avaiable on the <a href="http://admin.yoochoose.net" target="_blank">YOOCHOOSE Administrator Dashboard</a>.');

define('YOOCHOOSE_UPLOADER_HEADER', 'Upload Shop Usage Data');

define('YOOCHOOSE_JSON_MISSING', 'Function <code>json_decode()</code> is missing. Please update your php version (5.2 or higher) or install JSON extension manually (see http://pecl.php.net/package/json)');
define('YOOCHOOSE_CURL_MISSING', 'Your php installation is missing the <code>curl</code> extension. Please read http://www.php.net/manual/en/curl.installation.php for details about installing it.');
define('YOOCHOOSE_CONNECTION_SUCCESS', 'Yoochoose recommendation service is activated.');
define('YOOCHOOSE_CONNECTION_ERROR', 'Yoochoose service is currently not available from your network.<br>Please check the credentials you received upon your registration with the Yoochoose service.<br>If the error persists, please contact your system or network administrator.<br>HTTP Status retriving the statistic data: %1$s');
define('YOOCHOOSE_STATISTIC_COLLECTED', 'The following statistics have been collected so far:');
define('YOOCHOOSE_STATISTIC_EMPTY', 'It will take several minutes after your activation for the first events to arrive.');

define('YOOCHOOSE_ADVANCED_SETTINGS', 'advanced settings');

define('YOOCHOOSE_ERROR_LOADING_STRATEGIES', 
        'Unable to load avaliable recommendation scenarios. 
         Please check you customer ID and license key!<br> Error message: %1$s');

define('YOOCHOOSE_ERROR_STRATEGY_NOT_FOUND',
		'Recommendation scenario with reference code [%1$s] is not found.');
		
define('YOOCHOOSE_STRATEGY_NOT_FOUND_TIP',
		'Create missing scenarios using <a href="http://admin.yoochoose.net" target="_blank">Yoochoose Adminstrator Dashboard</a>.');

define('YOOCHOOSE_BOX_NOT_CONFIGURED_TIP',
	    'Missed menubox must be configured in [/templates/<yourtemplate>/template_settings.php].');

define('YOOCHOOSE_ERROR_TEMPLATE_NOT_PREPARED',
		'Template file %2$s of the current template %1$s does not contain recommendation variable. Please add the follwing code to the template:<br> %3$s');

// ADMIN-BEREICH Recommendation strategies

define('YOOCHOOSE_EVENT_1', 'events "click on item"');
define('YOOCHOOSE_EVENT_2', 'events "purchase item"');
define('YOOCHOOSE_EVENT_3', 'events "consumed item"');
define('YOOCHOOSE_EVENT_4', 'events "rated item"');
define('YOOCHOOSE_EVENT_5', 'events "followed recommended item"');
define('YOOCHOOSE_DELIVERED_RECO_ALSO_PURCHASED', 'recommendations "also purchased"');
define('YOOCHOOSE_DELIVERED_RECO_TOP_SELLING', 'recommendations "top-selling"');
define('YOOCHOOSE_DELIVERED_RECO_ALSO_CLICKED', 'recommendations "also clicked"');
define('YOOCHOOSE_DELIVERED_RECO_ULTIMATELY_PURCHASED', 'recommendations "ultimately purchased"'); // ultimately_purchased

define('YOOCHOOSE_NOT_AVAILABLE', 'not available');

define('YOOCHOOSE_CROSS_SELL_RECOMMENDATION', 'product recommendations');
define('YOOCHOOSE_RECOMMENDATION_EMPTY', 'There are currently no recommendations for this product');
define('YOOCHOOSE_REQUEST_RECOMMENDATION', 'recommend!');


define('YOOCHOOSE_UNABLE_PARSE_AS_DATE', 'Unable to parse a text "%1$s" as a date using "%2$s" format.');
define('YOOCHOOSE_UNABLE_PARSE_EMPTY_AS_DATE', 'Empty string cannot be parsed to date.');
define('YOOCHOOSE_VALIDATING_DDD', 'Validating...');

define('YOOCHOOSE_GENERATING_USAGE_DATA', 'Generating usage data. Step %1$s of 10.');
define('YOOCHOOSE_GENERATING_USAGE_DATA_FINISHED', 'Generating usage data. Step %1$s of 10 finished.');

define('YOOCHOOSE_GENERATING_FINISHED', 'Export finished <a href="%1$s">(link)</a>.');
define('YOOCHOOSE_MD5_SCHEDULING', 'Generating MD5 hash and scheduling upload.');
define('YOOCHOOSE_MD5_FINISHED', 'MD5 hash generated <a href="%1$s">(link)</a>.');
define('YOOCHOOSE_UPLOAD_SCHEDULED', 'Upload to Recomendation Engine scheduled.');
define('YOOCHOOSE_UPL_FROM', 'From (%1$s)');
define('YOOCHOOSE_UPL_TO', 'To (%1$s).');
define('YOOCHOOSE_UPL_BTN', 'Upload');
define('YOOCHOOSE_VALIDATING_DDD', 'Validating...');
define('YOOCHOOSE_USAGE_DATA_UPLOAD_INFO', 'Usually you need to upload the data manually only once after the recommendation module was activated and redistered.');
define('YOOCHOOSE_INTERVAL_NEGATIVE_ERR', 'Time interval cannot be negative');

define('YOOCHOOSE_ESTIMATED_OVERSIZE', 'Estimated output file size is %1$s. Uploading over %2$s Mb is not recomended. Please decrease the date interval.');
define('YOOCHOOSE_PRODUCTS_TO_UPLOAD', 'Products to upload: %1$s. Estimated output file size: %2$s.');

define('YOOCHOOSE_UPLOAD_FAILED', 'Error scheduling the upload in Recomendation Engine. %1$s');



define('YOOCHOOSE_ADMIN_HEADER', 'Yoochoose Recommedation Service');
define('YOOCHOOSE_CONFIG_HEADER', 'Configuration');

define('YOOCHOOSE_REGISTER_BTN_12', 'Register for free');

define('YOOCHOOSE_REGISTER_BTN', 'Register for free');

define('YOOCHOOSE_ACTIVATE_BTN', 'Activate');
define('YOOCHOOSE_ACTIVATE_CONTENT', 'I\'m already YOOCHOOSE customer.');


define('YOOCHOOSE_TOO_OLD_PHP_VERSION',
    'The PHP version you are using is too old. YOOCHOOSE module needs at least the PHP version %2$s. Your version is %1$s.');

define('YOOCHOOSE_DATETIME_MISSING',
     'YOOCHOOSE module needs the Class DateTime, which was not found in your PHP installation. Please check your configuration!');



define('YOOCHOOSE_LOG_LEVEL_TITLE',  'Log Level');
define('YOOCHOOSE_LOG_LEVEL_DESC',   'Spezified the log level for the log files /export/recommendations-*.log');


define('YOOCHOOSE_CONF_SERVER_NOT_ACCESSIBLE', 'Unable to connect to YOOCHOOSE Configuration Server. Please check the Web Server Firewall settings.');
define('YOOCHOOSE_CONF_UNAUTHORIZED', 'Unauthorized. Please recheck the Customer ID and the License Key. HTTP Error: %1$s');
define('YOOCHOOSE_CONF_FORBIDDEN', 'Forbidden. Please recheck the Customer ID and the License Key. HTTP Error: %1$s');
define('YOOCHOOSE_CONF_NOT_FOUND', 'Resource not found. Please check the Configuration Server URL. HTTP Error: %1$s');
define('YOOCHOOSE_CONF_NOT_UNKNOWN', 'Unable to load the license type. Unknown cause (%2$d). Please try again later. HTTP Error: %1$s');
define('YOOCHOOSE_JSON_ERROR', 'Unexpected error. Server responce cannot be parsed. %1$s');

define('YOOCHOOSE_STRATEGY', 'Recommendation scenario');
define('YOOCHOOSE_MAX_RECOMMENDATIONS', 'Products to display:');
define('YOOCHOOSE_BOX_ENABLED', 'enabled');

define('YOOCHOOSE_HAVE_BOUGHT', 'have bought'); // 40% have bought it.

?>