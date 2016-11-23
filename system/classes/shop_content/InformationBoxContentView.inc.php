<?php
/* --------------------------------------------------------------
   InformationBoxContentView.inc.php 2015-06-03 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(information.php,v 1.6 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (information.php,v 1.8 2003/08/21); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: information.php 1302 2005-10-12 16:21:29Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class InformationBoxContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_information.html');
	}

	function prepare_data()
	{
		$this->build_html = false;
		$coo_seo_boost = MainFactory::create_object('GMSEOBoost');
		$t_menu_items_array = array();
		$t_content_string = '';
		
		if(GROUP_CHECK == 'true')
		{
			$t_group_check = "AND group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
		}

		$t_sql = "SELECT
						content_id,
						categories_id,
						parent_id,
						content_title,
						content_group,
						gm_link,
						gm_link_target
					FROM " . TABLE_CONTENT_MANAGER . "
					WHERE
						languages_id='" . (int) $_SESSION['languages_id'] . "' AND
						file_flag = 0
						".$t_group_check." AND
						content_status = 1
					ORDER BY sort_order";

		$t_result = xtDBquery($t_sql);

		while($t_content_array = xtc_db_fetch_array($t_result, true))
		{
			$t_sef_parameter = '';
			if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			{
				$t_sef_parameter = '&content=' . xtc_cleanName($t_content_array['content_title']);
			}
			if(empty($t_content_array['gm_link']))
			{
				if($coo_seo_boost->boost_content)
				{
					$t_content_url = xtc_href_link($coo_seo_boost->get_boosted_content_url($t_content_array['content_id'], $_SESSION['languages_id']));
				}
				else
				{
					$t_content_url = xtc_href_link(FILENAME_CONTENT, 'coID='.$t_content_array['content_group'].$t_sef_parameter);
				}

				$t_target = '';
				
				$t_content_string .= '<img src="templates/'.CURRENT_TEMPLATE.'/img/icon_arrow.gif" alt="" /> <a href="'.$t_content_url.'">'.htmlspecialchars_wrapper($t_content_array['content_title']).'</a><br />';
			}
			else
			{
				$t_content_url = $t_content_array['gm_link'];
				$t_target = $t_content_array['gm_link_target'];
				$t_content_string .= '<img src="templates/'.CURRENT_TEMPLATE.'/img/icon_arrow.gif" alt="" /> <a href="'.$t_content_url.'" target="'.$t_content_array['gm_link_target'].'">'.htmlspecialchars_wrapper($t_content_array['content_title']).'</a><br />';
			}

			$t_menu_items_array[] = array('URL' => $t_content_url, 'URL_TARGET' => $t_target, 'NAME' => $t_content_array['content_title']);
		}

		if(!empty($t_menu_items_array))
		{			
			$this->set_content_data('CONTENT_LINKS_DATA', $t_menu_items_array);
			$this->set_content_data('CONTENT', $t_content_string, 2);
			$this->build_html = true;
		}
	}
}