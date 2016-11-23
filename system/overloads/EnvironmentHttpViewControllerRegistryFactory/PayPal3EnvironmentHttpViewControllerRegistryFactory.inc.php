<?php
/* --------------------------------------------------------------
	PayPal3EnvironmentHttpViewControllerRegistryFactory.inc.php
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class PayPal3EnvironmentHttpViewControllerRegistryFactory extends PayPal3EnvironmentHttpViewControllerRegistryFactory_parent
{
	/**
	 * @return HttpViewControllerRegistryInterface
	 */
	public function create()
	{
		$registry = parent::create();
		$registry->set('PayPal', 'PayPalController');
		$registry->set('PayPalConfiguration', 'PayPalConfigurationController');
		return $registry;
	}
}