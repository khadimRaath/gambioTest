<?php
/* --------------------------------------------------------------
  ImageRequestController.inc.php 2016-04-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class ImageRequestController
 *
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class ImageRequestController extends HttpViewController
{
	public function actionDefault()
	{
		try
		{
			$requestedImagePath = trim($this->_getQueryParameter('requested_image'));
			
			if(empty($requestedImagePath))
			{
				throw new RuntimeException('Image path required.');
			}
			elseif(strpos($requestedImagePath, '..') !== false)
			{
				throw new RuntimeException('Relative image path is not allowed.');
			}
			
			$requestedImagePath = $this->_getRequestedImagePath(new NonEmptyStringType($requestedImagePath));
			
			/** @var ProductWriteServiceInterface $productWriteService */
			$productWriteService = StaticGXCoreLoader::getService('ProductWrite');
			$productWriteService->processProductImage(new FilenameStringType(basename($requestedImagePath)));
			
			return MainFactory::create('HttpControllerResponse', readfile($requestedImagePath), array(
				'Content-Type: image/' . $this->_getContentType(new NonEmptyStringType($requestedImagePath))
			));
		}
		catch(FileNotFoundException $e)
		{
			return MainFactory::create('HttpControllerResponse', $e->getMessage(),
			                           array('HTTP/1.1 404 File not found'));
		}
		catch(Exception $e)
		{
			return MainFactory::create('HttpControllerResponse', $e->getMessage(),
			                           array('HTTP/1.1 500 Internal Server Error'));
		}
	}
	
	
	/**
	 * Returns the absolute file path of the requested image.
	 *
	 * @param NonEmptyStringType $requestedImagePath
	 *
	 * @return string
	 */
	protected function _getRequestedImagePath(NonEmptyStringType $requestedImagePath)
	{
		if(DIR_WS_CATALOG == '/')
		{
			$requestedImagePath = substr($requestedImagePath->asString(), 1);
		}
		else
		{
			$requestedImagePath = str_replace(DIR_WS_CATALOG, '', $requestedImagePath->asString());
		}
		
		$requestedImagePath = DIR_FS_CATALOG . $requestedImagePath;
		
		return $requestedImagePath;
	}
	
	
	/**
	 * Returns the content type of the requested image
	 *
	 * @param NonEmptyStringType $requestedImagePath
	 *
	 * @return mixed|string
	 */
	protected function _getContentType(NonEmptyStringType $requestedImagePath)
	{
		$contentType = preg_replace('/.*\.(png|jp(e)?g|gif)$/', '\\1', $requestedImagePath->asString());
		$contentType = (strcmp($contentType, 'jpg') == 0) ? 'jpeg' : $contentType;
		
		return $contentType;
	}
}