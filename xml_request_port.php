<?php

// Debug-Logging
file_put_contents('logfiles/xml_unfiltered.log', str_repeat('-', 100) . "\nDatum: " . date('Y-m-d H:i:s') . "\nIP: " . $_SERVER['REMOTE_ADDR'] . "\n\nGET-" . print_r($_GET, true) . "\nPOST-" . print_r($_POST, true) . "\n", FILE_APPEND);
		
chdir('admin/');
if ($_GET['module'] == 'XMLConnect')
{
	define( 'SUPPRESS_REDIRECT', 1 ); 
	require_once('request_port.php');
}