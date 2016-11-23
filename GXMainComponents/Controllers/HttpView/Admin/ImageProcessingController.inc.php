<?php
/* --------------------------------------------------------------
  ImageProcessingController.inc.php 2015-11-12
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class ImageProcessingController
 * @extends    AdminHttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class ImageProcessingController extends AdminHttpViewController
{
	/**
	 * @return AdminPageHttpControllerResponse
	 */
	public function actionDefault()
	{
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/');

		$html = $this->_render('image_processing.html', array(
			'image_options_page_link' => xtc_href_link(FILENAME_CONFIGURATION, 'gID=4', 'NONSSL')
		));

		$JavaScriptEngineLanguages = array(
			'image_processing'
		);

		return MainFactory::create('AdminPageHttpControllerResponse', 'Image Processing', $html, null, $JavaScriptEngineLanguages);
	}


	/**
	 * Runs the image Processing
	 *
	 * @return RedirectHttpControllerResponse
	 */
	public function actionProcess()
	{
		@xtc_set_time_limit(0);
		require_once DIR_FS_CATALOG . 'admin/includes/classes/' . FILENAME_IMAGEMANIPULATOR;
		
		$logger = LogControl::get_instance();
		$imageNumber = (int)$this->_getPostData('image_number');
		$files = $this->_getImageFiles();
		$responseMessage = '';
		
		// do not rename this variables, because included files need them
		$products_image_name = $files[$imageNumber - 1]['text'];
		$image_error = false;

		$filesCount = count($files);

		if($imageNumber <= $filesCount && $imageNumber > 0)
		{
			include(DIR_WS_INCLUDES . 'product_popup_images.php');
			include(DIR_WS_INCLUDES . 'product_info_images.php');
			include(DIR_WS_INCLUDES . 'product_thumbnail_images.php');
			include(DIR_WS_INCLUDES . 'product_gallery_images.php');

			// image processing failed, log the error
			if($image_error)
			{
				$responseMessage = 'Image ' . $imageNumber . ' "' . $products_image_name . '" could not be processed.';
				$logger->notice($responseMessage, 'widgets', 'image_processing', 'notice', $p_level_type = 'DEBUG NOTICE', E_USER_NOTICE);
			}
			elseif($imageNumber === $filesCount)
			{
				$logger->notice('Image processing DONE', 'widgets', 'image_processing', 'notice', $p_level_type = 'DEBUG NOTICE', E_USER_NOTICE);
			}

			$finished = $imageNumber === $filesCount;
		}
		else
		{
			$finished = true;
		}

		$payload = array(
			'imagesCount' => $filesCount,
		    'finished' => $finished
		);
		
		$response = array('success' => !$image_error, 'msg' => $responseMessage, 'payload' => $payload);

		return MainFactory::create('JsonHttpControllerResponse', $response);
	}

	
	/**
	 * @return array
	 */
	protected function _getImageFiles()
	{
		$files = array();

		if($dir = opendir(DIR_FS_CATALOG_ORIGINAL_IMAGES))
		{
			$i = 0;

			while($file = readdir($dir))
			{
				if(is_file(DIR_FS_CATALOG_ORIGINAL_IMAGES . $file)
				   && (strrpos(strtolower($file), '.jpg') !== false
				       || strrpos(strtolower($file), '.jpeg') !== false
				       || strrpos(strtolower($file), '.gif') !== false
				       || strrpos(strtolower($file), '.png') !== false)
				)
				{
					$files[] = array(
						'id'   => $file,
						'text' => $file,
						'nr'   => $i++
					);
				}
			}
			closedir($dir);

			array_multisort($files);
		}

		return $files;
	}
}