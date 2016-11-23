<?php
/* --------------------------------------------------------------
   CIDB.php 2016-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * CodeIgniter Database Library - Compatibility Layer
 *
 * This file tries to setup a CodeIgniter friendly environment so that the database
 * library works without problems, without the rest of the framework and without extra
 * modifications at the classes. Include this file and start using the database like
 * shown in the example below.
 *
 * IMPORTANT:
 *   This library does not support the caching and multiple drivers functionality of the
 *   original library because they are strongly coupled with the CodeIgniter ecosystem.
 *   They need more work in order to be available.
 *
 * EXAMPLE:
 *   require 'database/CIDB.php';
 *   $db = CIDB('mysqli://user:password@localhost/dbname?socket=/tmp/mysql.sock'); // Socket is optional
 *   $records = $db->get('test_table')->result();
 *
 * LIBRARY HISTORY:
 * v1.0.0 - 27.01.2015 Enables the core database functions (Cache, Forge and Utility classes missing).
 * v1.1.0 - 04.02.2016 Enabled functionality of the database utility class.
 * v1.2.0 - 04.07.2016 Enabled functionality of the database forge class.
 *
 * @link    http://www.codeigniter.com/user_guide/database/index.html
 * @version 1.2
 */

// ----------------------------------------------------------------------------
// INITIALIZE
// ----------------------------------------------------------------------------

// BASEPATH must be one dir up from the current directory.
define('BASEPATH', dirname(dirname(__FILE__)) . '/'); // no use of __DIR__ for PHP 5.2 compatibility

// Require the main database file.
require BASEPATH . 'codeigniter-db/DB.php';
require BASEPATH . 'codeigniter-db/DB_utility.php';
require BASEPATH . 'codeigniter-db/DB_forge.php';
require BASEPATH . 'codeigniter-db/drivers/mysqli/mysqli_utility.php';
require BASEPATH . 'codeigniter-db/drivers/mysqli/mysqli_forge.php';

// Create an instance of the CI mock object.
global $CI;
$CI = new CodeIgniter();

// ----------------------------------------------------------------------------
// REQUIRED FUNCTIONS
// ----------------------------------------------------------------------------

/**
 * Initialize and return database object.
 *
 * @param string $connectionString PDO-like connection string of the database.
 *
 * @return CI_DB_query_builder Returns a database driver that can be used for db operations.
 *
 * @throws InvalidArgumentException If the $connectionString parameter has an invalid value.
 */
function CIDB($connectionString)
{
	if(!is_string($connectionString) || empty($connectionString))
	{
		throw new InvalidArgumentException('Invalid $pdoConnectionString provided: ' . print_r($connectionString,
		                                                                                       true));
	}
	
	global $CI;
	$CI     = new CodeIgniter();
	$CI->db = DB($connectionString);
	
	return $CI->db;
}

/**
 * Initialize and return the database utilities object.
 *
 * @param string $connectionString PDO-like connection string of the database.
 *
 * @return CI_DB_utility Contains methods that serve various database operations.
 *
 * @throws InvalidArgumentException If the $connectionString parameter has an invalid value.
 */
function CIDBUtils($connectionString)
{
	if(!is_string($connectionString) || empty($connectionString))
	{
		throw new InvalidArgumentException('Invalid $pdoConnectionString provided: ' . print_r($connectionString,
		                                                                                       true));
	}
	
	$db = DB($connectionString);
	
	return new CI_DB_mysqli_utility($db);
}

/**
 * Initialize and return the database forge object.
 * 
 * @param $connectionString
 *
 * @return CI_DB_mysqli_forge
 */
function CIDBForge($connectionString)
{
	if(!is_string($connectionString) || empty($connectionString))
	{
		throw new InvalidArgumentException('Invalid $pdoConnectionString provided: ' . print_r($connectionString,
		                                                                                       true));
	}
	
	$db = DB($connectionString);
	
	return new CI_DB_mysqli_forge($db);
}

/**
 * Log DB Library message.
 *
 * This method is used by multiple sections of the CIDB library for logging error
 * messages or other important information. It is the best place to handle database
 * query errors of the GX code.
 *
 * @param string $type    Type of the log message ("debug", "error" etc).
 * @param string $message The message that concerns the log item.
 */
function log_message($type, $message)
{
	if($type === 'error')
	{
		echo $message;
		if(function_exists('xtc_db_error'))  // GX3 logging method 
		{
			xtc_db_error('CIDB Library Error', '', $message);
		}
	}
	
	return; // Do not log database messages.
}

/**
 * This method will throw an exception so that outside code can catch
 * it and react accordingly.
 *
 * @param string $message Error message.
 *
 * @throws Exception When DB class wants to show an error.
 */
function show_error($message)
{
	throw new Exception($message);
}

/**
 * Get instance of CodeIgniter mocking object.
 *
 * @return CodeIgniter
 */
function &get_instance()
{
	global $CI;
	
	return $CI;
}

// ----------------------------------------------------------------------------
// MOCK OBJECTS
// ----------------------------------------------------------------------------

/**
 * CodeIgniter Mock Class
 *
 * This mock class will trick the database classes in order to work without any
 * extra modifications.
 */
class CodeIgniter
{
	public $db;
}
