<?php
/* --------------------------------------------------------------
   resource.get_usermod.php 2016-01-08 rn
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

	Resource overload for the smarty include statements. It enables
	USERMOD-files for templates.
	USAGE:
	{include file="overloads:PATH/TO/TPL.FILE"} 
*/
class Smarty_Resource_Get_Usermod extends Smarty_Resource_Custom
{

	/**
	 * Fetch a template and its modification time
	 *
	 * @param string $name template name
	 * @param string $source template source
	 * @param integer $mtime template modification timestamp (epoch)
	 * @return void
	 */
	protected function fetch($name, &$source, &$mtime)
	{
		$filename   = get_usermod(DIR_FS_CATALOG . $name);
		$source     = file_get_contents($filename);
		$mtime      = filemtime($filename);
	}

	/**
	 * Fetch a template's modification time
	 *
	 * @note implementing this method is optional. Only implement it if modification times can be accessed faster than loading the complete template source.
	 * @param string $name template name
	 * @return integer timestamp (epoch) the template was modified
	 */
	protected function fetchTimestamp($name) {
		$filename   = get_usermod(DIR_FS_CATALOG . $name);
		return filemtime($filename);
	}

}