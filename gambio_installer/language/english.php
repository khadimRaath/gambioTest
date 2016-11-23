<?php
/* --------------------------------------------------------------
   english.php 2015-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

# Button-Labels
define('BUTTON_BACK', 'Back');
define('BUTTON_CHECK_MISSING_FILES', 'Check again');
define('BUTTON_CHECK_PERMISSIONS', 'Check permissions again');
define('BUTTON_CONNECT', 'Connect');
define('BUTTON_CONNECT_NEW', 'Reconnect');
define('BUTTON_CONTINUE', 'Proceed installation');
define('BUTTON_DOWNLOAD', 'Download');
define('BUTTON_ENGLISH', 'English');
define('BUTTON_FINISH', 'Complete setup');
define('BUTTON_GAMBIO_PORTAL', 'Gambio Customer Portal');
define('BUTTON_GERMAN', 'Deutsch');
define('BUTTON_OPEN_SHOP', 'Open online store');
define('BUTTON_SET_PERMISSIONS', 'Set permissions');
define('BUTTON_START', 'Start setup');
define('BUTTON_SKIP', 'Force install continuation');


# Headlines
define('HEADING_INSTALLATION_SERVICE', 'Gambio Installation-Service');
define('HEADING_INSTALLATION', 'Start Installation');
define('HEADING_WRONG_PERMISSIONS', 'The following files or folders do not have full write access (777):');
define('HEADING_FTP_DATA', 'FTP-DATA');
define('HEADING_REMOTE_CONSOLE', 'Remote-Console');
define('HEADING_DATABASE', 'DATABASE INFORMATION');
define('HEADING_SHOP_INFORMATION', 'SHOP INFORMATION');
define('HEADING_ADMIN_DATA', 'SHOP OWNER DATA');
define('HEADLINE_ROBOTS', 'CREATE ROBOTS.TXT');
define('HEADING_SUCCESS', 'Shop setup is successful');
define('HEADING_REGISTER_GLOBALS', 'Security risk is identified');
define('HEADING_PROGRESS', 'Database is set up');


# Texts
define('TEXT_INSTALLATION_SERVICE', 'You do not want to perform the installation yourself? Take advantage of our installation service!');
define('TEXT_INSTALLATION', 'Select the desired language for your installation.');
define('TEXT_SET_PERMISSIONS', 'You can even put the rights of either an FTP program or using the FTP feature of the installer.
For the latter, please enter the following form with your FTP information and click &quot;Connect&quot;.<br />
Then navigate to the directory where the store is run and set the permissions by clicking the button &quot;Set permissions&quot;.');
define('TEXT_ROBOTS','Click &quot;Download&quot; to generate and download the robots.txt for your store.
Upload the file with an FTP program in the main directory of your web server.
The file must then be accessible at the following link: <a href="http://' . getenv('HTTP_HOST') . '/robots.txt" target="_blank">http://' . getenv('HTTP_HOST') . '/robots.txt</a>');
define('TEXT_SUCCESS','Congratulations on installing your new online store. We wish you success and good sales!<br /> Your Gambio.de Service-Team.');
define('TEXT_FINAL_SETTINGS', 'Final setup is running...please wait.');
define('TEXT_WRITE_ROBOTS_FILE', 'The robots.txt will automatically attempt to create...please wait.');
define('TEXT_TABLES_EXIST', 'The following tables marked in red will be irrevocably deleted in the next step! Included data is lost!');
define('TEXT_MISSING_FILES', 'The following files or folders are missing. Use a FTP program to upload them on your server and click the button &quot;Check again&quot; to ensure the completeness.');
define('TEXT_REGISTER_GLOBALS', '&quot;register_globals&quot; in the configuration of your server is enabled. This is a security risk. We recommend you to contact your provider, so that &quot;register_globals&quot; is disabled on your server.');
define('TEXT_PROGRESS', 'This process can take several minutes and should not be aborted.');
define('TEXT_SKIP', 'You can continue the installation if you are certain that everything should already be set correctly and the detection fails due to technical reasons.');


# Form-Labels
define('LABEL_FTP_SERVER', 'FTP-Server:');
define('LABEL_FTP_USER', 'FTP-User:');
define('LABEL_FTP_PASSWORD', 'FTP-Password:');
define('LABEL_FTP_PASV', 'passive:');
define('LABEL_DIR_UP', 'Directory up');
define('LABEL_DB_SERVER', 'Server:');
define('LABEL_DB_USER', 'User:');
define('LABEL_DB_PASSWORD', 'Password:');
define('LABEL_DB_DATABASE', 'Database:');
define('LABEL_HTTP_SERVER', 'HTTP-Server:');
define('LABEL_SSL', 'activate SSL:');
define('LABEL_HTTPS_SERVER', 'HTTPS-Server:');
define('LABEL_GENDER', 'Form of address:');
define('LABEL_MALE', 'Mr');
define('LABEL_FEMALE', 'Miss/Ms/Mrs');
define('LABEL_FIRSTNAME', 'First name:');
define('LABEL_LASTNAME', 'Last name:');
define('LABEL_EMAIL', 'E-Mail:');
define('LABEL_STREET', 'Street:');
define('LABEL_STREET_NUMBER', 'Street number:');
define('LABEL_POSTCODE', 'ZIP:');
define('LABEL_CITY', 'City:');
define('LABEL_STATE', 'State:');
define('LABEL_COUNTRY', 'Country:');
define('LABEL_TELEPHONE', 'Telephone:');
define('LABEL_PASSWORD', 'Password:');
define('LABEL_CONFIRMATION', 'Confirmation:');
define('LABEL_SHOP_NAME', 'Shop name:');
define('LABEL_COMPANY', 'Company:');
define('LABEL_EMAIL_FROM', 'E-Mail from:');
define('LABEL_FORCE_DB', 'Continue anyway!');


# Error messages
define('ERROR_SESSION_SAVE_PATH', 'The session could not be started. Please set the permissions of the %s directory to 777 (full read-write access).');
define('ERROR_SET_PERMISSIONS_FAILED', 'Setting the file permissions has failed. Try right now to do it manually.');
define('ERROR_TABLES_EXIST', 'The database already contains tables!');
define('ERROR_FTP_CONNECTION', 'Could not connect to \'%s\'. Check the FTP-Server address!');
define('ERROR_FTP_DATA', 'The FTP-User \'%s\' or the FTP-Password is invalid!');
define('ERROR_UNEXPECTED', 'An unexpected error has occurred. Start the installation again.');
define('ERROR_CONFIG_FILES', 'The configuration files could not be written because they do not have write access (777).');
define('ERROR_MISSING_FILES', 'Shop uploaded incomplete');
define('ERROR_DB_QUERY', '-queries can not be executed, because the MySQL-user has no privileges. Please contact your provider to correct the rights for the MySQL-user.');

define('ERROR_INPUT_DB_CONNECTION', 'Server, user or password is invalid');
define('ERROR_INPUT_DB_DATABASE', 'Database does not exist');
define('ERROR_INPUT_SERVER_URL', 'Shop-URL is invalid');
define('ERROR_INPUT_SERVER_HTTPS', 'Activating SSL without a valid license can lead to unexpected problems');
define('ERROR_INPUT_MIN_LENGTH_2', 'At least 2 characters are required');
define('ERROR_INPUT_EMAIL', 'E-mail-address is invalid');
define('ERROR_INPUT_MIN_LENGTH_3', 'At least 3 characters are required');
define('ERROR_INPUT_MIN_LENGTH_4', 'At least 4 characters are required');
define('ERROR_INPUT_MIN_LENGTH_5', 'At least 5 characters are required');
define('ERROR_INPUT_PASSWORD_CONFIRMATION', 'Confirmation and password are not identical');
define('ERROR_MEMORY_LIMIT', '&quot;memory_limit&quot; to low');
define('ERROR_TEXT_MEMORY_LIMIT', 'The &quot;memory_limit&quot; in the server configuration is set to low. We recommend at least %sM.<br />Please contact your provider to increase the &quot;memory_limit&quot;.');

define('REQUIREMENT_WARNING', '<p>The requirements for the Gambio shop are <strong>PHP ###minPHPVersion### </strong></p>
<p>Your PHP Version: <strong>###yourPHPVersion###</strong></p>
<p>Please contact your provider to update your server configuration.</p>');