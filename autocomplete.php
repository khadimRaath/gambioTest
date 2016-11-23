<?php
/* --------------------------------------------------------------
   autocomplete.php 2015-05-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/*	File:		autocomplete.php
	*	Version:	4.1
	*	Date:		21.Jan.2010
	*       $Revision: 216 $
	*	FINDOLOGIC GmbH
	*/

require_once 'includes/application_top.php';
require_once('./findologic_config.inc.php');

function getUrl($url, $timeout = 5)
{
	$curl_opts = array(
		CURLOPT_URL => $url,
		CURLOPT_TIMEOUT => $timeout,
		CURLOPT_RETURNTRANSFER => true,
	);
	$ch = curl_init();
	curl_setopt_array($ch, $curl_opts);
	$response = curl_exec($ch);
	$errno = curl_errno($ch);
	$error = curl_error($ch);
	$info = curl_getinfo($ch);
	curl_close($ch);

	$result = array(
		'response' => $response,
		'info' => $info,
		'errno' => $errno,
		'error' => $error,
	);
	return $result;
}

/*
 *	do http-request
 */
$parameters = $_GET;
$parameters['shopkey'] = FL_SHOP_ID;
$parameters['revision_timestamp'] = FL_REVISION;

/* manually pass the arg_separator as '&' to avoid problems with different configurations */
if(strpos(FL_SERVICE_URL, 'http') === false)
{
	$scheme_prefix = "http://";
}
else {
	$scheme_prefix = '';
}
$url = $scheme_prefix.FL_SERVICE_URL."/autocomplete.php?" . http_build_query($parameters, '', '&');

$result = getUrl($url);
if($result['error'] > 0)
{
	die('Could not connect to search service, please check your shop config');
}
header('Content-Type: ' . $result['info']['content_type']);
$content = $result['response'];

echo $content;

xtc_db_close();
