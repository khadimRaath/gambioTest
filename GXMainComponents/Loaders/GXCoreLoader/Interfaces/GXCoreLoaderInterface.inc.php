<?php
/* --------------------------------------------------------------
   GXCoreLoaderInterface.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Interface GXCoreLoaderInterface
 *
 * @category   System
 * @package    Loaders
 * @subpackage Interfaces
 */
interface GXCoreLoaderInterface
{
	/**
	 * Get Service Object
	 *
	 * @param string $serviceName
	 *
	 * @return AddressBookService|CountryService|CustomerService
	 * @throws DomainException
	 */
	public function getService($serviceName);
	
	
	/**
	 * Get a CodeIgniter Query Builder Object
	 *
	 * @return CI_DB_query_builder
	 */
	public function getDatabaseQueryBuilder();
} 