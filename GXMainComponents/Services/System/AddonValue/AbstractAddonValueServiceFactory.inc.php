<?php

/* --------------------------------------------------------------
   AbstractAddonValueServiceFactory.inc.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AbstractAddonValueServiceFactory
 *
 * @category System
 * @package  AddonValue
 */
abstract class AbstractAddonValueServiceFactory
{
	/**
	 * Creates an addon value service.
	 * @return AddonValueServiceInterface
	 */
	abstract public function createAddonValueService();
}