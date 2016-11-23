<?php
/* --------------------------------------------------------------
   LanguagesBoxContentView.inc.php 2015-03-23 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(languages.php,v 1.14 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (languages.php,v 1.8 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: languages.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class LanguagesBoxContentView extends ContentView
{
	protected $coo_language;
	protected $request_type = '';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('boxes/box_languages.html');
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['coo_language']	= array('type' => 'object',
																'object_type' => 'language');
		$this->validation_rules_array['languages_id']	= array('type' => 'string');
	}

	public function prepare_data()
	{
		$this->build_html = false;
		$t_languages_string = '';
		$t_languages_array = array();
		$t_link = '';
		$languages_count = 0;
		
		$t_uninitialized_array = $this->get_uninitialized_variables(array('coo_language'));
		if(empty($t_uninitialized_array))
		{
			reset($this->coo_language->catalog_languages);
			while(list($t_key, $t_value) = each($this->coo_language->catalog_languages))
			{
				if($t_value['status'] == 1)
				{
					$languages_count++;

					$t_params_check = xtc_get_all_get_params(array('language', 'currency'));
					if(!empty($t_params_check))
					{
						$t_languages_string .= ' <a href="' . xtc_href_link(basename(gm_get_env_info('PHP_SELF')), 'language=' . $t_key.'&'.xtc_get_all_get_params(array('language', 'currency')), $this->request_type) . '">' . xtc_image('lang/' .  $t_value['directory'] .'/' . $t_value['image'], $t_value['name']) . '</a> ';
						$t_link = xtc_href_link(basename(gm_get_env_info('PHP_SELF')), 'language=' . $t_key.'&'.xtc_get_all_get_params(array('language', 'currency')), $this->request_type);
					}
					else
					{
						if(strstr(basename(gm_get_env_info('PHP_SELF')), '.') !== false)
						{
							$t_basename = basename(gm_get_env_info('PHP_SELF'));
						}
						else
						{
							$t_basename = '';
						}
						$t_languages_string .= ' <a href="' . xtc_href_link($t_basename, 'language=' . $t_key, $this->request_type) . '">' . xtc_image('lang/' .  $t_value['directory'] .'/' . $t_value['image'], $t_value['name']) . '</a> ';
						$t_link = xtc_href_link($t_basename, 'language=' . $t_key, $this->request_type);
					}

					$t_languages_array[] = array('NAME' => $t_value['name'],
													'ID' => $t_value['id'],
													'ICON' => 'lang/' . basename($t_value['directory']) . '/' . $t_value['image'],
													'ICON_SMALL' => 'lang/' . basename($t_value['directory']) . '/flag.png',
													'CODE' => $t_value['code'],
													'DIRECTORY' => $t_value['directory'],
													'LINK' => $t_link);
				}
			}

			// dont show box if there's only 1 language
			if($languages_count > 1 )
			{
				$this->content_array['CONTENT'] = $t_languages_string;
				$this->content_array['CURRENT_LANGUAGES_ID'] = $_SESSION['languages_id'];
				$this->content_array['languages_data'] = $t_languages_array;
				$this->build_html = true;
			}
		}
		else
		{
			trigger_error("Variable(s) " . implode(', ', $t_uninitialized_array) . " do(es) not exist in class " . get_class($this) . " or is/are null", E_USER_ERROR);
		}
	}
}