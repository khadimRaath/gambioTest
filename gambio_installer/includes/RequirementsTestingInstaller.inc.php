<?php
/* --------------------------------------------------------------
   RequirementsTestingInstaller.inc.phpr.inc.php 2015-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

// This File should be in PHP4 Style!

/**
 * Class RequirementsTestingInstaller
 */
class RequirementsTestingInstaller
{

	/**
	 * @var array
	 */
	var $info = array('php' => '');


	/**
	 *
	 * @param $p_minPHPVersion
	 *
	 * @return bool
	 */
	function textPHPAndMySQLVersion($p_minPHPVersion)
	{
		$phpTestResult   = $this->testPHPVersoin($p_minPHPVersion);

		return $phpTestResult;
	}


	/**
	 * @param $p_minPHPVersion
	 *
	 * @return bool
	 */
	function testPHPVersoin($p_minPHPVersion)
	{
		$testResult            = false;
		$minPHPMeetRequirement = version_compare(PHP_VERSION, $p_minPHPVersion, '>=');

		if($minPHPMeetRequirement)
		{
			$testResult = true;
		}

		$this->info['php'] = PHP_VERSION;

		return $testResult;
	}

	/**
	 * @return array
	 */
	function getInfo()
	{
		return $this->info;
	}
}