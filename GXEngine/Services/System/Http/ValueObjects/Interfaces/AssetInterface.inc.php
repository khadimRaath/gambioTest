<?php
/* --------------------------------------------------------------
   AssetInterface.inc.php 2015-07-22 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AssetInterface
 *
 * @category   System
 * @package    Http
 * @subpackage Interfaces
 */
interface AssetInterface
{
	/**
	 * Returns the assets HTML markup.
	 *
	 * @return string Returns the HTML markup that will load the file when the page is loaded.
	 */
	public function __toString();
}