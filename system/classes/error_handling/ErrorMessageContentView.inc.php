<?php
/* --------------------------------------------------------------
   ErrorMessageContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: error_handler.php 949 2005-05-14 16:44:33Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/**
 * Class ErrorMessageContentView
 */
class ErrorMessageContentView extends ContentView
{
	/**
	 * deprecated - not used anymore
	 */
	protected $error;

	public function __construct()
	{
		parent::__construct();
		
		$this->set_content_template('module/error_message.html');
		$this->set_flat_assigns(true);
	}

	
	public function prepare_data()
	{
		$t_feature_value_group_array = $_SESSION['coo_filter_manager']->get_feature_value_group_array();
		# transfer feature_value_groups to product finder
		$coo_finder = MainFactory::create_object('IndexFeatureProductFinder');
		
		foreach($t_feature_value_group_array as $t_feature_value_group)
		{
			$coo_finder->add_feature_value_group($t_feature_value_group);
		}
	
		$coo_filter_selection_content_view = MainFactory::create_object('FilterSelectionContentView');
		$coo_filter_selection_content_view->set_('feature_value_group_array', $t_feature_value_group_array);
		$coo_filter_selection_content_view->set_('language_id', $_SESSION['languages_id']);
		$t_filter_selection_html = $coo_filter_selection_content_view->get_html();
		$this->content_array['FILTER_SELECTION'] = $t_filter_selection_html;
		
		// search field
		$this->content_array['FORM_ACTION'] = xtc_draw_form('new_find', xtc_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false, true, true), 'get').xtc_hide_session_id();
		$this->content_array['INPUT_SEARCH_NAME'] = 'keywords';
		$this->content_array['FORM_END'] = '</form>';
	}


	/**
	 * @param string $p_error
	 */
	public function set_error($p_error)
	{
		$this->error = (string)$p_error;
	}


	/**
	 * @return string
	 */
	public function get_error()
	{
		return $this->error;
	}
}