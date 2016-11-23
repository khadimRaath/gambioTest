<?php
/* --------------------------------------------------------------
   FooterContentView.inc.php 2016-09-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(add_a_quickie.php,v 1.10 2001/12/19); www.oscommerce.com
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: add_a_quickie.php,v 1.1 2004/04/26 20:26:42 fanta2k Exp $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Add A Quickie v1.0 Autor  Harald Ponce de Leon

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/


class FooterContentView extends ContentView
{
	protected $language_id;
	protected $customer_status_id;
	protected $content_data_array = array();
	
	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/footer.html');
	}
	
	protected function set_validtaion_rules()
	{
		$this->validation_rules_array['language_id']		= array('type' => 'int');
		$this->validation_rules_array['customer_status_id']	= array('type' => 'int');
		$this->validation_rules_array['content_data_array']	= array('type' => 'array');
	}

	public function prepare_data()
	{
		$t_uninitialized_array = $this->get_uninitialized_variables(array('customer_status_id',
																		  'language_id'))
		;

		if(empty($t_uninitialized_array))
		{
			$this->get_data();
			$this->set_data();
		}
		else
		{
			trigger_error("Variable(s) "
						  . implode(', ', $t_uninitialized_array)
						  . " do(es) not exist in class "
						  . get_class($this)
						  . " or is/are null"
				, E_USER_ERROR
			);
		}
	}
	
	
	public function assign_menu_boxes(array $smartyVars)
	{
		foreach($smartyVars as $key => $value)
		{
			if(strpos($key, 'gm_box_pos_') !== false)
			{
				$this->set_content_data($key, $value);
			}
		}
	}
	

	protected function get_data()
	{
		$this->build_html = false;
		
		if(gm_get_conf('SHOW_FOOTER', 'ASSOC', true) == 'true')
		{
			$t_query = $this->get_sql_query();
			$t_content_query = xtc_db_query($t_query);
			if(xtc_db_num_rows($t_content_query) == 1)
			{
				$this->content_data_array = xtc_db_fetch_array($t_content_query);
			}
			$this->build_html = true;
		}
	}

	protected function set_data()
	{
		if(is_array($this->content_data_array) && empty($this->content_data_array) == false)
		{
			$t_content = '';

			if($this->content_data_array['content_file'] != '')
			{
				ob_start();

				if(strpos($this->content_data_array['content_file'], '.txt'))
				{
					echo '<pre>';
				}

				include(DIR_FS_CATALOG . 'media/content/' . $this->content_data_array['content_file']);

				if(strpos($this->content_data_array['content_file'], '.txt'))
				{
					echo '</pre>';
				}

				$t_content = ob_get_contents();
				ob_end_clean();
			}
			else
			{
				$t_content = $this->content_data_array['content_text'];
			}

			$this->content_array['HTML'] = $t_content;
		}
		
		if($this->build_html == true)
		{
			$t_footer_html = '';

			// TEMPLATE SWITCHER
			if(defined('TEMPLATE_SWITCHER') && trim(TEMPLATE_SWITCHER) != '')
			{
				$t_footer_html .= TEMPLATE_SWITCHER . '<br /><br />';
			}
			$footer = gm_get_content('GM_FOOTER', $_SESSION['languages_id']) ? : gm_get_conf('GM_FOOTER');
			$t_footer_html .= $footer;
			

			// COPYRIGHT
			$this->content_array['COPYRIGHT_FOOTER'] = $t_footer_html;
		}
	}

	protected function get_sql_query()
	{
		if(GROUP_CHECK == 'true')
		{
			$t_group_check = ' AND group_ids LIKE "%c_' . $this->customer_status_id . '_group%"';
		}

		$t_query = 'SELECT
						*
					FROM
						' . TABLE_CONTENT_MANAGER . '
					WHERE
						content_group = "199"
					AND
						languages_id = "' . $this->language_id . '"
						' . $t_group_check
		;

		return $t_query;
	}
}