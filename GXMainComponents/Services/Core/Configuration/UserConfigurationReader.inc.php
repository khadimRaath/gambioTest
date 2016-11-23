<?php
/* --------------------------------------------------------------
   UserConfigurationReader.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UserConfigurationReader
 *
 * @category   System
 * @package    UserConfiguration
 * @subpackage Repository
 */
class UserConfigurationReader implements UserConfigurationReaderInterface
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
	public function getUserConfiguration(IdType $userId, $configurationKey)
	{
		$result = $this->db->select('configuration_value')
		                   ->from('user_configuration')
		                   ->where(array('customer_id' => (string)$userId, 'configuration_key' => $configurationKey))
		                   ->get()
		                   ->row_array(0);

		return !is_null($result) ? $result['configuration_value'] : null;
	}
}