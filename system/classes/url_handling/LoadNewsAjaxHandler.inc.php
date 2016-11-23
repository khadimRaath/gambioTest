<?php
/* --------------------------------------------------------------
   LoadNewsAjaxHandler.inc.php 2015-10-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class LoadNewsAjaxHandler extends AjaxHandler
{
	public function get_permission_status($p_customers_id=NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}

	public function proceed()
	{
		$this->v_output_buffer = 'Timeout';
		$coo_load_url = MainFactory::create_object('LoadUrl');

		$t_result = $coo_load_url->load_url($this->v_data_array['GET']['link']);
		
		if($t_result)
		{
			/**
			 * @var UserConfigurationService $userConfiguration
			 */
			$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
			$userId                   = new IdType((int)$_SESSION['customer_id']);

			preg_match('/<!--\s+news_content_stamp:\s*([\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2})\s+-->/', $t_result, $matches);
			
			if(isset($matches[1]))
			{
				if($userConfigurationService->getUserConfiguration($userId, 'news_content_stamp') !== $matches[1])
				{
					$userConfigurationService->setUserConfiguration($userId, 'news_content_stamp', $matches[1]);
					$userConfigurationService->setUserConfiguration($userId, 'dashboard_chart_collapse', 'true');
				}
			}
			
			$this->v_output_buffer = $t_result;
		}

		return true;
	}
}