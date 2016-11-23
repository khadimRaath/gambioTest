<?php
/* --------------------------------------------------------------
  file_list_creator.php 2015-04-23 gm jow
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

define('FILELIST_NAME','gambio_filelist_to_delete.txt');
define('FILELIST_LINE_ENDING', "\r\n");


/*  ******************** START ********************************** */
define('SEND_NO_HEADER', true);

//include_once 'application.inc.php';

error_reporting(E_STRICT);
ini_set('html_errors', 'on');

if(array_key_exists('file_list', $_POST) && $_POST['file_list'] !== '')
{
	$fileListJsonString = urldecode($_POST['file_list']);
}
else
{
	die('request_error');
}

/* Build File-Content */

$filesArray = json_decode($fileListJsonString);

$content = '';

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . FILELIST_NAME . '"');
header('Content-Transfer-Encoding: binary');

// Send Headers: Prevent Caching of File
header('Cache-Control: no-store');

echo implode(FILELIST_LINE_ENDING, $filesArray);