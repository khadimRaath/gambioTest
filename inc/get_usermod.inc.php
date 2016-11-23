<?php
/* --------------------------------------------------------------
   get_usermod.inc.php 2015-09-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function get_usermod($p_file_path, $p_debug_output=false)
{
	if(!is_string($p_file_path) || strpos($p_file_path, 'string:') !== false || strpos($p_file_path, 'eval:') !== false)
	{
		return $p_file_path;
	}
	
	$t_file_path = trim($p_file_path);
	$t_coo_cached_directory = new CachedDirectory('');

	# extract filename
	$t_file_name = basename($t_file_path);

	# extend filename
	$t_file_parts = explode('.', $t_file_name);
	$t_file_parts[0] .= '-USERMOD';

	# rebuild filename
	$t_file_name = implode('.', $t_file_parts);

	# rebuild possible filepath to usermod-version
	$t_usermod_file_path = dirname($t_file_path) .'/'. $t_file_name;

	# check if -USERMOD-file exists
	if($t_coo_cached_directory->file_exists($t_usermod_file_path))
	{
		$t_file_path = $t_usermod_file_path;
	}

	if($p_debug_output)
	{
		echo "input: $p_file_path <br/>\n";
		echo "tried: $t_usermod_file_path <br/>\n";
		echo "result: $t_file_path <br/>\n";
	}
	return $t_file_path;
}
