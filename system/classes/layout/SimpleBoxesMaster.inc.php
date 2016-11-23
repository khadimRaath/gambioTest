<?php
/* --------------------------------------------------------------
   SimpleBoxesMaster.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of SimpleBoxesMaster
 *
 * @author ncapuno
 */
class SimpleBoxesMaster
{
	public function get_status($p_menubox)
	{
		$coo_template_control = MainFactory::create_object('TemplateControl', array(), true);

		if(isset($coo_template_control->v_optional_template_settings_array['MENUBOXES'][$p_menubox]) == false)
		{
			trigger_error('menubox not found in settings-array: ' . $p_menubox);
			$t_output = 0;
		}
		else
		{
			$t_output = $coo_template_control->v_optional_template_settings_array['MENUBOXES'][$p_menubox]['STATUS'];
		}

		return $t_output;
	}


	public function get_position($p_menubox)
	{
		$coo_template_control = MainFactory::create_object('TemplateControl', array(), true);

		if(isset($coo_template_control->v_optional_template_settings_array['MENUBOXES'][$p_menubox]) == false)
		{
			trigger_error('menubox not found in settings-array: ' . $p_menubox);
			$t_output = 0;
		}
		else
		{
			$t_output = $coo_template_control->v_optional_template_settings_array['MENUBOXES'][$p_menubox]['POSITION'];
		}
		
		return $t_output;
	}


	public function findSettingValueByName($p_settingName)
	{
		$settingValue = null;

		$coo_template_control = MainFactory::create_object('TemplateControl', array(), true);

		if(isset($coo_template_control->v_optional_template_settings_array['SETTINGS'][$p_settingName]))
		{
			$settingValue = $coo_template_control->v_optional_template_settings_array['SETTINGS'][$p_settingName];
		}

		return $settingValue;
	}
}