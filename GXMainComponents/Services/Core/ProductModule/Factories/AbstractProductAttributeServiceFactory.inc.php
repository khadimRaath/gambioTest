<?php

/* --------------------------------------------------------------
   AbstractProductAttributeServiceFactory.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractProductAttributeServiceFactory
 *
 * @category   System
 * @package    ProductModule
 * @subpackage Factories
 */
abstract class AbstractProductAttributeServiceFactory
{
	/**
	 * Creates a product attribute object service.
	 *
	 * @return ProductAttributeObjectService
	 */
	abstract public function createProductAttributeObjectService();


	/**
	 * Creates a product attribute service.
	 *
	 * @return ProductAttributeService
	 */
	abstract public function createProductAttributeService();
}