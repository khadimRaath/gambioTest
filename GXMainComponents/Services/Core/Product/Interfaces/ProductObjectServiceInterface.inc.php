<?php

/* --------------------------------------------------------------
   ProductObjectServiceInterface.inc.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductObjectServiceInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductObjectServiceInterface
{
	/**
	 * Creates a product object.
	 *
	 * @return ProductInterface
	 */
	public function createProductObject();
}