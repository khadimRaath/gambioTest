<?php

/* --------------------------------------------------------------
   ProductListProviderFactoryInterface.inc.php 2015-12-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductListProviderFactoryInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductListProviderFactoryInterface
{
	/**
	 * Creates a product list provider.
	 *
	 * @param LanguageCode $languageCode Language code.
	 * @param array        $conditions   Request conditions.
	 *
	 * @return ProductListProviderInterface
	 */
	public function createProductListProvider(LanguageCode $languageCode, array $conditions = array());
}