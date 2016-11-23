<?php

/* --------------------------------------------------------------
   PHPConfigurationController.php 2016-06-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AdminHttpViewController');

/**
 * Class MaxFileSizeController
 * @extends    AdminHttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class MaxFileSizeController extends AdminHttpViewController
{
	/**
	 * @var PHP ini settings
	 */
	protected $maxFileSize = array();


	public function actionDefault()
	{
		$this->maxFileSize['maxFileSize'] = (int)ini_get('upload_max_filesize');

		return MainFactory::create('JsonHttpControllerResponse', $this->maxFileSize);
	}
}