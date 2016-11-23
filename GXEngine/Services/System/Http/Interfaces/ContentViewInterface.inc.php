<?php
/* --------------------------------------------------------------
   ContentViewInterface.inc.php 2015-02-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface ContentViewInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface ContentViewInterface
{
	/**
	 * Sets the content template file.
	 *
	 * @param $templateFile
	 *
	 * @return void
	 */
	public function set_content_template($templateFile);


	/**
	 * Inject the content data to the template file that they are accessible in template with variables.
	 *
	 * @param string $key   Variable name in the template file.
	 * @param mixed  $value Value in the template which can get accessed by the $key inside the set template file.
	 *
	 * @return void
	 */
	public function set_content_data($key, $value);


	/**
	 * Sets the template directory path.
	 *
	 * @param string $templateDirectoryPath Absolute path to the template directory.
	 *
	 * @return void
	 */
	public function set_template_dir($templateDirectoryPath);
}