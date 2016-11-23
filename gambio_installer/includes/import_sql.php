<?php
/* --------------------------------------------------------------
  import_sql.php 2015-04-04 mb
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(install_3.php,v 1.6 2002/08/15); www.oscommerce.com
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: install_step3.php 899 2005-04-29 02:40:57Z hhgag $)

  Released under the GNU General Public License
  -------------------------------------------------------------- */

$t_output = array();

if(!empty($_GET['sql_part']) && xtc_in_array('database', $_POST['install']))
{
	$t_db_array = array();

	$t_db_array['DB_SERVER'] = trim(gm_prepare_string($_POST['DB_SERVER'], true));
	$t_db_array['DB_SERVER_USERNAME'] = trim(gm_prepare_string($_POST['DB_SERVER_USERNAME'], true));
	$t_db_array['DB_SERVER_PASSWORD'] = trim(gm_prepare_string($_POST['DB_SERVER_PASSWORD'], true));
	$t_db_array['DB_DATABASE'] = trim(gm_prepare_string($_POST['DB_DATABASE'], true));

	xtc_db_connect_installer($t_db_array['DB_SERVER'], $t_db_array['DB_SERVER_USERNAME'], $t_db_array['DB_SERVER_PASSWORD']);

	$db_error = false;

	$t_files_array = glob(DIR_FS_CATALOG . 'gambio_installer/sql/*.sql');
	sort($t_files_array);

	$t_sql_file = '';
	$t_next_sql = '';

	$t_files_sorted_array = array();
	$t_files_sorted_array['gambio'] = DIR_FS_CATALOG . 'gambio_installer/sql/gambio.sql';
	$t_last_exists = false;
	foreach($t_files_array AS $t_filepath)
	{
		if(substr($t_filepath, -10) != 'gambio.sql' && substr($t_filepath, -8) != 'last.sql')
		{
			$t_files_sorted_array[substr(basename($t_filepath), 0, -4)] = $t_filepath;
		}
		elseif(substr($t_filepath, -8) == 'last.sql')
		{
			$t_last_exists = true;
		}
	}

	if($t_last_exists)
	{
		$t_files_sorted_array['last'] = DIR_FS_CATALOG . 'gambio_installer/sql/last.sql';
	}

	$GLOBALS['total_executed_queries'] = 0;
	$t_max_queries_per_request = 2000;
	$t_next_sql = $_GET['sql_part'];
	
	while($GLOBALS['total_executed_queries'] < $t_max_queries_per_request && $t_next_sql !== '')
	{
		while(key($t_files_sorted_array) !== $t_next_sql)
		{
			next($t_files_sorted_array);
		}

		$t_sql_file = current($t_files_sorted_array);
		if($t_sql_file)
		{
			xtc_db_install($t_db_array['DB_DATABASE'], $t_sql_file);

			$t_next = next($t_files_sorted_array);
			if($t_next === false)
			{
				$t_next_sql = '';
			}
			else
			{
				$t_next_sql = key($t_files_sorted_array);
			}
		}
	}
}

if(!$db_error)
{
	$t_output['success'] = true;

	if($t_next_sql == '' && $t_sql_file != '')
	{
		$t_output['progress'] = 100;
	}
	elseif($t_next_sql != '' && $t_sql_file != '')
	{
		$t_output['progress'] = floor(100 / count($t_files_sorted_array) * ($i + 1));
		$t_output['next_sql'] = $t_next_sql;
	}
	else
	{
		$t_output['success'] = false;
		$t_output['error'] = 'no sql file';
	}
}
else
{
	$t_output['success'] = false;
	$t_output['error'] = $db_error;
}
