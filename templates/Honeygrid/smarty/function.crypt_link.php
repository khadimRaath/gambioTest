<?php
/* --------------------------------------------------------------
   function.crypt_link.php 2009-11-19 tw@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2009 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * @param $params
 * @param $smarty
 *
 * @return string
 */
function smarty_function_crypt_link($params, &$smarty)
{
	$cryptLetter = $params['crypt'];
	$planeLink = $params['link'];
	$cryptLink = chunk_split($planeLink, 2, $cryptLetter);
	$cryptLink = '2' . $cryptLink;
	$cryptLink = substr($cryptLink, 0, -1);
	return $cryptLink;
}