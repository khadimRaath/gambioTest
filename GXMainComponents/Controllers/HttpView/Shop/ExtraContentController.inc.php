<?php
/* --------------------------------------------------------------
	ExtraContentController.inc.php 2016-07-13
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class ExtraContentController
 *
 * This controller exists as a base for future extensions for cases in which content needs to be displayed in a
 * somewhat neutral way. cf. system/overloads/ExtraContentController for examples on how to use this.
 * 
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class ExtraContentController extends HttpViewController
{
	protected $timeout = 10;

	/**
	 * Initialize the Controller with required properties
	 *
	 * @param \HttpContextReaderInterface     $httpContextReader
	 * @param \HttpResponseProcessorInterface $httpResponseProcessor
	 * @param \ContentViewInterface           $contentView
	 *
	 * @inheritdoc
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $contentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $contentView);
	}

	public function actionDefault()
	{
		return MainFactory::create('RedirectHttpControllerResponse', GM_HTTP_SERVER.DIR_WS_CATALOG);
	}

	protected function _getExternalContent($url)
	{
		$curlOptions = [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => $this->timeout,
		];
		$ch = curl_init();
		curl_setopt_array($ch, $curlOptions);
		$content = curl_exec($ch);
		$curlErrno = curl_errno($ch);
		$curlError = curl_error($ch);
		curl_close($ch);
		if($curlErrno > 0)
		{
			throw new Exception(sprintf("Error retrieving external content: %d - %s", $curlErrno, $curlError));
		}
		return $content;
	}
}
