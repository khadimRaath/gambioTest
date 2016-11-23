<?php

/* --------------------------------------------------------------
   AddonValueServiceFactory.inc.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AddonValueServiceFactory
 *
 * @category System
 * @package  AddonValue
 */
class AddonValueServiceFactory extends AbstractAddonValueServiceFactory
{
	/**
	 * Database connection.
	 * @var CI_DB_query_builder
	 */
	protected $db;


	/**
	 * AddonValueServiceFactory constructor.
	 *
	 * @param CI_DB_query_builder $db Database connection.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	

	/**
	 * Creates an addon value service.
	 * @return AddonValueServiceInterface
	 */
	public function createAddonValueService()
	{
		$addonValueStorageFactory = MainFactory::create('AddonValueStorageFactory', $this->db);

		return MainFactory::create('AddonValueService', $addonValueStorageFactory);
	}
}