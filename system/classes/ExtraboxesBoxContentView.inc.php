<?php
/* --------------------------------------------------------------
   ExtraboxesBoxContentView.inc.php 2014-09-02 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercebased on original files FROM OSCommerce CVS 2.2 2002/08/28 02:14:35 www.oscommerce.com
   (c) 2003	 nextcommerce (admin.php,v 1.12 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: admin.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class ExtraboxesBoxContentView extends ContentView
{
	protected $extrabox_number = 0;
	protected $group_number = 60;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_extrabox.html');
	}

	protected function set_validation_rules()
	{
		$this->validation_rules_array['extrabox_number'] 	= array('type' 		=> 'int');
		$this->validation_rules_array['group_number'] 		= array('type' 		=> 'int');
	}
	
	public function reset_content_array()
	{
		$this->content_array = array();
	}

	public function prepare_data()
	{
		$this->build_html = false;
		
		if($this->extrabox_number > 0)
		{
			$t_group_number = $this->group_number + $this->extrabox_number;
			
			if(GROUP_CHECK=='true')
			{
				$group_check = "AND group_ids LIKE '%c_".$_SESSION['customers_status']['customers_status_id']."_group%'";
			}
			
			$t_query = 'SELECT
							*
						FROM
							' . TABLE_CONTENT_MANAGER . '
						WHERE
							content_group = "' . $t_group_number . '" 
							AND languages_id = "' . (int)$_SESSION['languages_id'] . '"
						' . $group_check;

			$t_result = xtc_db_query($t_query);
			$t_content_data_array = xtc_db_fetch_array($t_result);

			if((xtc_db_num_rows($t_result) && $t_content_data_array['content_status'] == '1') || $_SESSION['style_edit_mode'] == 'edit')
			{
				if($t_content_data_array['content_file'] != '')
				{
					ob_start();
					if(strpos($t_content_data_array['content_file'], '.txt'))
					{
						echo '<pre>';
					}

					include(DIR_FS_CATALOG . 'media/content/' . basename($t_content_data_array['content_file']));

					if(strpos($t_content_data_array['content_file'], '.txt'))
					{
						echo '</pre>';
					}

					$t_content_body = ob_get_contents();
					ob_end_clean();
				}
				else
				{
					$t_content_body = $t_content_data_array['content_text'];
				}

				$this->set_content_data('NUMBER', $this->extrabox_number);
				$this->set_content_data('HEADING', $t_content_data_array['content_heading']);

				if($_SESSION['style_edit_mode'] == 'edit')
				{
					$t_content_body = preg_replace('!(.*?)<script.*?</script>(.*?)!is', "$1$2", $t_content_body);
				}
				$this->set_content_data('CONTENT', $t_content_body);

				$this->build_html = true;
			}
		}
	}
}