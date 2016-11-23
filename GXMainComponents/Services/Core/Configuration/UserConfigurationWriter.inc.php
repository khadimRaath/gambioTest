<?php
/* --------------------------------------------------------------
   UserConfigurationWriter.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UserConfigurationWriter
 *
 * @category   System
 * @package    UserConfiguration
 * @subpackage Repository
 */
class UserConfigurationWriter implements UserConfigurationWriterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;


	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * @override
	 */
	public function setUserConfiguration(IdType $userId, $configurationKey, $configurationValue)
	{
		$this->db->replace('user_configuration', array(
				'customer_id'         => (string)$userId,
				'configuration_key'   => $configurationKey,
				'configuration_value' => $configurationValue
		));
	}
}