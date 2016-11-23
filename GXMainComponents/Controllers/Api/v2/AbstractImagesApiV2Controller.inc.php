<?php

/* --------------------------------------------------------------
   AbstractImagesApiV2Controller.inc.php 2016-01-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class AbstractImagesApiV2Controller
 *
 * Provides a listing of image files.
 *
 * @category System
 * @package  ApiV2Controllers
 */
abstract class AbstractImagesApiV2Controller extends HttpApiV2Controller
{
	/**
	 * @var ProductWriteService|CategoryWriteService
	 */
	protected $writeService;
	
	
	/**
	 * Initializes API Controller
	 */
	abstract protected function __initialize();
	
	
	/**
	 * Returns the absolute path where the image files are located.
	 *
	 * @return string
	 */
	abstract protected function _getImageFolderName();
	
	
	/**
	 * Returns a list of all image files which exists on the server's filesystem.
	 *
	 * This function searches for image files ('gif', 'jpeg', 'jpg', 'png') in the path which is specified in the child
	 * classes _getImageFolderName method.
	 */
	public function get()
	{
		$allowedFileTypes = array('gif', 'jpeg', 'jpg', 'png');
		$response         = array();
		
		$files = glob($this->_getImageFolderName() . '*');
		{
			foreach($files as $file)
			{
				if(in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowedFileTypes))
				{
					$response[] = basename($file);
				}
			}
		}
		
		$this->_writeResponse($response);
	}
}