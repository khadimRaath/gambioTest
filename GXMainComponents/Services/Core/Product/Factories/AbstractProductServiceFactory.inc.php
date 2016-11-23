<?php

/* --------------------------------------------------------------
   AbstractProductServiceFactory.inc.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Abstract Class AbstractProductServiceFactory
 *
 * @category   System
 * @package    Product
 * @subpackage Factories
 */
abstract class AbstractProductServiceFactory
{
	/**
	 * Creates a product object service.
	 *
	 * @return ProductObjectServiceInterface
	 */
	abstract public function createProductObjectService();
	
	
	/**
	 * Creates a product read service.
	 *
	 * @return ProductReadServiceInterface
	 */
	abstract public function createProductReadService();
	

	/**
	 * Creates a product write service.
	 *
	 * @return ProductWriteServiceInterface
	 */
	abstract public function createProductWriteService();

}