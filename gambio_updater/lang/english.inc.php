<?php
/* --------------------------------------------------------------
   english.inc.php 2016-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

define('BUTTON_CONTINUE', 'Continue');
define('BUTTON_INSTALL', 'Install updates');
define('BUTTON_GAMBIO_PORTAL', 'Gambio Customer Portal');
define('BUTTON_LOGIN', 'Login');
define('BUTTON_CONFIGURE', 'Update configuration');
define('BUTTON_SHOW_UPDATES', 'Update overview');
define('BUTTON_SHOP', 'Open shop');
define('BUTTON_CHECK_PERMISSIONS', 'Check permissions again');
define('BUTTON_CONNECT', 'Connect');
define('BUTTON_CONNECT_NEW', 'Reconnect');
define('BUTTON_SET_PERMISSIONS', 'Set permissions');
define('BUTTON_CHECK_DELETE_FILES', 'Check again');
define('BUTTON_CHECK_MOVE', 'Check again');
define('BUTTON_CREATE_BACKUP', 'Download Files');
define('BUTTON_DELETE_FILES', 'Delete obsolete files');
define('BUTTON_MOVE', 'Execute');
define('BUTTON_SKIP', 'Force install continuation');
define('BUTTON_DOWNLOAD_FILELIST_TO_DELETE', 'Download of the delete list');

define('HEADING_INSTALLATION_SERVICE', 'Gambio Installation-Service');
define('HEADING_INSTALLATION', 'Start Installation');
define('HEADING_LOGIN', 'Login');
define('HEADING_UPDATES', 'Updates');
define('HEADING_FTP_DATA', 'FTP-Data');
define('HEADING_REMOTE_CONSOLE', 'Remote-Console');
define('HEADING_WHICH_VERSION', 'Choosing shop version');
define('HEADING_INSTALLATION_SUCCESS', 'Installation completed');
define('TEXT_INSTALLATION_SUCCESS_CACHE_REBUILD_ERROR', 'All updates were successfully installed.<br /><br />Please clear your browser\'s cache to avoid display errors.<p style="color: red;" >Attention: The update script wasn\'t able to delete the caches.<br />Please delete the caches at the admin panel manually.</p>');
define('HEADING_PROGRESS', 'Installation progress');
define('HEADING_WRONG_PERMISSIONS', 'The following files or folders do not have full write access (777):');
define('HEADING_NEED_TO_DELETE', 'The following files or folders need to be deleted:');
define('HEADING_MOVE', 'The following files or folders need to be moved:');
define('HEADING_RENAME', 'The following files or folders need to be renamed:');
define('HEADING_INSTALLATION_CLEAR_CACHE', 'Shop-Caches');
define('HEADING_DELETED_FILES', 'Deleted files and folders');
define('HEADING_MOVED', 'The following files or folders were moved:');
define('HEADING_RENAMED', 'The following files or folders were renamed:');
define('HEADING_PERMISSIONS_SET', 'The following files or folders now have full write access (777):');

define('LABEL_VERSION', 'Version:');
define('LABEL_EMAIL', 'E-Mail:');
define('LABEL_PASSWORD', 'Password:');
define('LABEL_FTP_SERVER', 'FTP-Server:');
define('LABEL_FTP_USER', 'FTP-User:');
define('LABEL_FTP_PASSWORD', 'FTP-Password:');
define('LABEL_FTP_PASV', 'passive:');
define('LABEL_DIR_UP', 'Directory up');
define('LABEL_FORCE_VERSION_SELECTION', 'Force version selection');
define('DESCRIPTION_FORCE_VERSION_SELECTION', 'If the update process was either cancelled or failed for any reason, a new process can be started using the option \'Force version selection\'. In that case, please choose the version the shop had BEFORE the update.');

define('TEXT_INSTALLATION_SERVICE', 'You do not want to perform the installation yourself? Take advantage of our installation service!');
define('TEXT_INSTALLATION', 'Select the desired language for your installation.');
define('TEXT_LOGIN', 'Please log in using your administrator-shop-account e-mail and password.');
define('TEXT_LOGIN_ERROR', 'Your email or password is incorrect.');
define('TEXT_UPDATES', 'The following updates were found:');
define('TEXT_WHICH_VERSION', 'What is your actual shop version?<br/>');
define('TEXT_LANGUAGE', 'Language');
define('TEXT_SECTION_NAME', 'section-name');
define('TEXT_PHRASE_NAME', 'Phrase-name');
define('TEXT_ERRORS', 'An error has occurred. Please restore your database from the backup.');
define('TEXT_ERROR_TIMEOUT', 'Maximum execution time reached: Update could not be completed.');
define('TEXT_ERROR_PARSERERROR', 'Incorrect return value:<br />');
define('TEXT_ERROR_NO_RESPONSE', 'Unknown return value: Update has been canceled for unknown reasons.');
define('TEXT_ERROR_500', 'Internal server error: Update has been canceled for unknown reasons.');
define('TEXT_ERROR_UNKNOWN', 'Unknown error.');
define('TEXT_SQL_ERRORS', 'There are SQL error occurred. Update could not be completed. Please restore your database from the backup.');
define('TEXT_SECTION_CONFLICT_REPORT', 'The following phrases are overloaded with individually applied section-language files, so that currently chosen new phrase texts are not displayed in the shop. You should therefore contact your programmer.<br />');
define('TEXT_DELETE_LIST', 'Please delete the following files or directories from your server:');
define('TEXT_INSTALLATION_SUCCESS', 'All updates were successfully installed.<br /><br />Please clear your browser\'s cache to avoid display errors.');
define('TEXT_PROGRESS', 'Please have a little patience. The following update is being installed: ');
define('TEXT_SET_PERMISSIONS', 'You can even put the permissions of either an FTP program or using the FTP feature of the updater. For the latter, please enter the following form with your FTP information and click &quot;Connect&quot;.<br/>
Then navigate to the directory where the store is run and set the permissions by clicking the button &quot;Set permissions&quot;.');
define('TEXT_DELETE_FILES', 'You can even delete the files and directories of either an FTP program or using the FTP feature of the updater. For the latter, please enter the following form with your FTP information and click &quot;Connect&quot;.<br/>
Then navigate to the directory where the store is run and set the permissions by clicking the button &quot;Delete obsolete files&quot;.');
define('TEXT_MOVE', 'You can perform the changes of either an FTP program or using the FTP feature of the updater. For the latter, please enter the following form with your FTP information and click &quot;Connect&quot;.<br/>
Then navigate to the directory where the store is run and perform the changes by clicking the button &quot;Execute&quot;.');
define('TEXT_NO_CONFIGURATION', 'The updates to be installed have no configuration. Continue by clicking &quot;' . BUTTON_INSTALL . '&quot;.');
define('TEXT_NO_UPDATES', 'There were no installable updates found for your current shop version.');
define('TEXT_PERMISSIONS_OK', 'The file permissions are correctly set.');
define('TEXT_DELETE_FILES_OK', 'Obsolete files and directories were deleted.');
define('TEXT_MOVE_OK', 'Renaming or moving has been carried out successfully.');
define('TEXT_CURRENT_DIR', 'current directory: ');
define('TEXT_TEMPLATE_NOTIFICATION', 'Your template seems to be incompatible with our new architecture. For now the EyeCandy template will be activated.');
define('TEXT_SKIP', 'You can continue the installation if you are certain that everything should already be set correctly and the detection fails due to technical reasons.');
define('TEXT_INSTALLATION_CLEAR_CACHE', 'Please have a little patience until the shop caches have been rebuilt.');
define('TEXT_DELETED_FILES', 'The listed files or folders were deleted. You can download a backup by clicking the button "Download Files".<br /><br />Please click the button "Continue", to proceed the installation.');
define('TEXT_PERMISSIONS_SET', 'The listed files or folders now have correct write permissions.<br /><br />Please click the button "Continue", to proceed the installation.');
define('TEXT_NOT_ALL_FILES_UPLOADED', 'The Service Pack was not uploaded completely. Please upload the complete Service Pack to your web server again.');
define('TEXT_NOT_ALL_SE_V2_FILES_UPLOADED', 'The StyleEdit Module was not uploaded completely. Please upload the complete StyleEdit directory to your web server again.');
define('TEXT_NOT_ALL_SE_V3_FILES_UPLOADED', 'The StyleEdit3 Module was not uploaded completely. Please upload the complete StyleEdit3 directory to your web server again.');

define('TEXTCONFLICTS_LABEL', 'Text conflicts');
define('TEXTCONFLICTS_TEXT', 'Which text version do you want to apply?');
define('TEXTCONFLICTS_OLD', 'old');
define('TEXTCONFLICTS_NEW', 'new');

define('ERROR_FTP_CONNECTION', 'Could not connect to \'%s\'. Check the FTP-Server address!');
define('ERROR_FTP_DATA', 'The FTP-User \'%s\' or the FTP-Password is invalid!');
define('ERROR_FTP_NOT_INSTALLED', 'Unfortunately, the server does not support FTP. Please contact your provider with a request to unlock the FTP functions of PHP.<br />Alternatively, you can now perform the adjustments with an FTP program. Then click the "Check again" button to continue the update process.');
define('ERROR_FTP_NO_LISTING', 'The server is not able to read any directories via FTP. This could be due to a server-side firewall. Please contact your provider with a request to check the PHP function &quot;ftp_nlist&quot;.<br />Alternatively, you can now perform the adjustments with an FTP program. Finally click the "Check again" button to continue the update process.');

define('ERROR_SQL_UNKNOWN', 'An unknown error occurred while processing the update "x.x.x.x".');

define('LABEL_FORCE_VERSION_SELECTION_BTN', 'Force shop-version');

define('REQUIREMENT_WARNING', '<p>The requirements for the Gambio shop are <strong>PHP ###minPHPVersion### </strong> and <strong>MySQL ###minMySQLVersion###</strong>.</p><p>Your PHP Version: <strong>###yourPHPVersion###</strong><br>Your MySQL Version: <strong>###yourMySQLVersion###</strong></p><p>Please contact your provider to update your server configuration.</p>');

define('TEXT_PAYPAL_NOTIFICATION', '<span class="warning" style="display:inline-block"><strong>ACHTUNG: Sie nutzen ein veraltetes PayPal-Modul!</strong><br /><p>Wir empfehlen Ihnen, ab sofort das aktuelle PayPal-Modul einzusetzen. Sie können es jetzt im Admin unter Module -> Zahlungsweisen aktivieren.</p><p>Weitere Informationen: <a href="https://www.gambio.de/7pM9Y" target="_blank">https://www.gambio.de/7pM9Y</a></p></span>');