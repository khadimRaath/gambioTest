<?php
/* --------------------------------------------------------------
   UserConfigurationService.inc.php 2015-10-05 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class UserConfigurationService
 *
 * This class is used to persist user specific configurations
 *
 * @category   System
 * @package    UserConfiguration
 */
class UserConfigurationService implements UserConfigurationServiceInterface
{
	protected $reader;
	protected $writer;


	/**
	 * Constructor
	 *
	 * @param UserConfigurationReader $reader
	 * @param UserConfigurationWriter $writer
	 */
	public function __construct(UserConfigurationReader $reader, UserConfigurationWriter $writer)
	{
		$this->reader = $reader;
		$this->writer = $writer;
	}


	/**
	 * @override
	 */
	public function setUserConfiguration(IdType $userId, $configurationKey, $configurationValue)
	{
		return $this->writer->setUserConfiguration($userId, $configurationKey, $configurationValue);
	}


	/**
	 * @override
	 */
	public function getUserConfiguration(IdType $userId, $configurationKey)
	{
		return $this->reader->getUserConfiguration($userId, $configurationKey);
	}
}