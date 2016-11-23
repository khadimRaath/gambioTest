<?php
/* --------------------------------------------------------------
   JsonHttpControllerResponse.inc.php 2015-03-12 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponse');

/**
 * Class JsonHttpControllerResponse
 *
 * @category   System
 * @package    Http
 * @subpackage ValueObjects
 * @extends    HttpControllerResponse
 */
class JsonHttpControllerResponse extends HttpControllerResponse
{
	/**
	 * Initializes the json http controller response.
	 *
	 * @param array $contentArray Array which will be encoded in json format.
	 */
	public function __construct(array $contentArray)
	{
		$this->httpBody = json_encode($contentArray);
	}
}