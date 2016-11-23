<?php

/* --------------------------------------------------------------
  JSGlobalExtenderComponent.inc.php 2016-03-08
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

MainFactory::load_class('ExtenderComponent');

/**
 * Class JSGlobalExtenderComponent
 * 
 * @deprecated Since v2.7.2.0
 */
class JSGlobalExtenderComponent extends ExtenderComponent
{
	function get_permission_status($p_customers_id = NULL)
	{
		return true;
	}
}
?>