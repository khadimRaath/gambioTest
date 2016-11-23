<?php
/*	--------------------------------------------------------------
	export.php
	Digineo GmbH
	http://www.digineo.de
	Copyright (c) 2009 Digineo GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	Author: Tim Kretschmer
	Version 1.0
	--------------------------------------------------------------*/

require('../admin/includes/configure.php');
@set_time_limit(0);

$port   = isset(explode(':', DB_SERVER)[1]) && is_numeric(explode(':', DB_SERVER)[1]) ? (int)explode(':', DB_SERVER)[1] : null;
$socket = isset(explode(':', DB_SERVER)[1]) && !is_numeric(explode(':', DB_SERVER)[1]) ? explode(':', DB_SERVER)[1] : null;

($GLOBALS["___mysqli_ston"] = mysqli_connect(explode(':', DB_SERVER)[0],  DB_SERVER_USERNAME,  DB_SERVER_PASSWORD, DB_DATABASE, $port, $socket));
((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . constant('DB_DATABASE')));

// 2011-07-27 rp @ digineo: Passwort sind die ersten 15 Stellen des Lettr-API-Key
$sql = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT gm_value FROM gm_configuration WHERE gm_key='LETTR_API_KEY'"));
$password = substr($sql['gm_value'], 0, 15);

if($_SERVER['PHP_AUTH_PW'] != $password || $password == "" || $_SERVER['PHP_AUTH_USER'] != "newsletter_export") {
	header('WWW-Authenticate: Basic realm="Export"');
	header('HTTP/1.0 401 Unauthorized');
	die("not authorized");
}

class NewsletterAPI {

	function export(){
    // 2011-07-27 rp @ digineo: No-Spam wird gesetzt, sofern die Einstellung für LETTR_EXPORTCUSTOMER = false ist.
    $no_spam = mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "SELECT gm_value FROM gm_configuration WHERE gm_key='LETTR_EXPORTCUSTOMER'"));
		$no_spam = ($no_spam['gm_value'] == 'true') ? false : true;

		$sql = "	SELECT
					c.customers_id AS customers_id,
					c.customers_firstname,
					c.customers_lastname,
					c.customers_email_address,
					c.customers_gender,
					s.customers_status_name,
					a.entry_street_address,
					a.entry_postcode,
					c.customers_newsletter,
					a.entry_city
				FROM
					customers AS c,
					customers_status AS s, 
					address_book AS a
				WHERE
					a.customers_id = c.customers_id 
					AND
					c.customers_status= s.customers_status_id ";							
				if( $no_spam ) {
					$sql .= "
					AND 
					c.customers_newsletter=1 ";
				}		
		$sql.="	GROUP BY
					customers_id
				ORDER BY
					customers_id
				";
		$export_query = mysqli_query($GLOBALS["___mysqli_ston"], $sql, MYSQLI_USE_RESULT);

		header ("content-type: text/xml");
		echo "<?xml version='1.0' encoding='utf-8' ?>\n";
		echo "<recipients>\n";
		while($user = mysqli_fetch_assoc($export_query)) {
			$special_tags = "";
			if($user['customers_newsletter']==1)
			{
				$special_tags = ", newsletter";
			}
			echo "			  
				<recipient>
					<key>".$this->_encode_field($user['customers_id'])."</key>
					<email>".$this->_encode_field($user['customers_email_address'])."</email>
					<firstname>".$this->_encode_field($user['customers_firstname'])."</firstname>
					<lastname>".$this->_encode_field($user['customers_lastname'])."</lastname>
					<gender>".$this->_encode_field($user['customers_gender'])."</gender>					
					<city>".$this->_encode_field($user['entry_city'])."</city>
					<street>".$this->_encode_field($user['entry_street_address'])."</street>
					<pcode>".$this->_encode_field($user['entry_postcode'])."</pcode>
					<tag_list>".$this->_encode_field($user['customers_status_name']). $special_tags ."</tag_list>
					<only_text>0</only_text>
					<approved>1</approved>
				</recipient>				
			";	
		}
		echo "</recipients>";
	}
	
	function unsubscribe($recipient){
		$id = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $_POST['recipient']['key']) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));	
		$sql = "UPDATE customers SET customers_newsletter=0 WHERE customers_id=".$id;
		mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		header("HTTP/1.0 200 OK");
		die("Erfolgreich ausgetragen");
	}
	
	function _encode_field($field) {
		return htmlspecialchars(utf8_encode($field));
	}
}

$api = new NewsletterAPI();

switch($_SERVER['REQUEST_METHOD']){
	case "GET":
		$api->export();
		break;
	case "POST":
		$api->unsubscribe($_POST['recipient']);
		break;
		
	default:
		header("HTTP/1.0 405 Method Not Allowed");
		die("Method not allowed");
}