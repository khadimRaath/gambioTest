<?php

/* --------------------------------------------------------------
   ConfigurationBoxContentView.inc.php 2015-09-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class ConfigurationBoxContentView
 *
 * This ContentView can be used to create a configuration box
 */
class ConfigurationBoxContentView extends ContentView
{
	/**
	 * LEGACY
	 * This variable is should just be used to infuse content in the style of the old box-class
	 * @var array
	 */
	private $oldSchoolHeading = array();

	/**
	 * LEGACY
	 * This variable is should just be used to infuse content in the style of the old box-class
	 * @var array
	 */
	private $oldSchoolContents = array();

	/**
	 * Indicates, if the editable class is applied to the form
	 * @var bool
	 */
	private $formIsEditable = false;

	/**
	 * Request method (get | post)
	 * @var string
	 */
	private $formMethod = 'post';

	/**
	 * Request action
	 * @var string
	 */
	private $formAction = '.';

	/**
	 * Additional HTML attributes of the form
	 * @var array
	 */
	private $formAttributes = array();

	/**
	 * A flag that indicates whether the checkbox widget should be used in the form
	 * @var bool
	 */
	private $useCheckboxWidget = false;


	/**
	 * Initializes the template engine Smarty
	 */
	public function init_smarty()
	{
		parent::init_smarty();
		$this->set_flat_assigns(false);
	}


	/**
	 * Sets the Template and the content
	 */
	public function prepare_data()
	{
		$this->set_template_dir(DIR_FS_ADMIN . 'html/content/');
		$this->set_content_template('configuration_box.html');

		$this->_convertOldSchoolHeading();
		$this->_convertOldSchoolContents();

		$this->content_array['heading']           = strip_tags($this->content_array['heading']);
		$this->content_array['formIsEditable']    = $this->formIsEditable;
		$this->content_array['formMethod']        = $this->formMethod;
		$this->content_array['formAction']        = $this->formAction;
		$this->content_array['formAttributes']    = implode(' ', $this->formAttributes);
		$this->content_array['pageToken']         = $_SESSION['coo_page_token']->generate_token();
		$this->content_array['useCheckboxWidget'] = $this->useCheckboxWidget;
	}


	/**
	 * LEGACY
	 * Converts an array of old fashioned heading entries (like it is used in the box class) into the structure needed
	 * by the configuration box template
	 */
	private function _convertOldSchoolHeading()
	{
		if(count($this->oldSchoolHeading) > 0)
		{
			$this->content_array['heading'] = $this->_buildOldSchoolContent($this->oldSchoolHeading);
		}
	}


	/**
	 * LEGACY
	 * Converts an array of old fashioned content entries (like it is used in the box class) into the structure needed
	 * by the configuration box template
	 */
	private function _convertOldSchoolContents()
	{
		if(count($this->oldSchoolContents) > 0)
		{
			$this->content_array['form'] = $this->_buildOldSchoolContent($this->oldSchoolContents);
		}
	}


	/**
	 * LEGACY
	 * Builds content out of an old fashioned array (used by the box class)
	 *
	 * @param array $contentArray
	 *
	 * @return string The built content
	 */
	private function _buildOldSchoolContent(array $contentArray)
	{
		$tableBlock                    = MainFactory::create('tableBlock', array());
		$tableBlock->table_cellpadding = '0';

		return $tableBlock->tableBlock($contentArray);
	}


	/**
	 * LEGACY
	 * Sets a heading array like in the box class
	 *
	 * @param array $oldSchoolHeading
	 */
	public function setOldSchoolHeading(array $oldSchoolHeading)
	{
		$this->oldSchoolHeading = $oldSchoolHeading;
	}


	/**
	 * LEGACY
	 * Sets a content array like in the box class
	 *
	 * @param array $oldSchoolContents
	 */
	public function setOldSchoolContents(array $oldSchoolContents)
	{
		$this->oldSchoolContents = $oldSchoolContents;
	}


	/**
	 * Sets the form editable or not
	 *
	 * @param $formIsEditable
	 */
	public function setFormEditable($formIsEditable)
	{
		$this->formIsEditable = (bool)$formIsEditable;
	}


	/**
	 * @param string $formMethod
	 */
	public function setFormMethod($formMethod)
	{
		$this->formMethod = $formMethod;
	}


	/**
	 * @param string $formAction
	 */
	public function setFormAction($formAction)
	{
		$this->formAction = $formAction;
	}


	/**
	 * @param array $formAttributes
	 */
	public function setFormAttributes($formAttributes)
	{
		$this->formAttributes = $formAttributes;
	}


	/**
	 * @param boolean $useCheckboxWidget
	 */
	public function setUseCheckboxWidget($useCheckboxWidget)
	{
		$this->useCheckboxWidget = $useCheckboxWidget;
	}
}