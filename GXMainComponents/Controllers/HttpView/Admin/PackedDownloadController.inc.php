<?php

/* --------------------------------------------------------------
	PackedDownloadController.inc.php 2016-02-05
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class PackedDownloadController
 * 
 * This class implements a mass downloader. It can be used to have the shop retrieve a list of resources (denoted by their URLs)
 * and pack them in a ZIP file. The ZIP file is stored in the cache folder and can then be downloaded.
 * 
 * @extends    AdminHttpViewController
 * @category   System
 * @package    AdminHttpViewControllers
 */
class PackedDownloadController extends AdminHttpViewController
{
	/**
	 * initializes the controller
	 * @param HttpContextReaderInterface
	 * @param HttpResponseProcessorInterface
	 * @param ContentViewInterface
	 */
	public function __construct(HttpContextReaderInterface $httpContextReader,
	                            HttpResponseProcessorInterface $httpResponseProcessor,
	                            ContentViewInterface $contentView)
	{
		parent::__construct($httpContextReader, $httpResponseProcessor, $contentView);
	}
	
	
	/**
	 * Returns an error (JSON-encoded) as this controller does not have a default action
	 * @return JsonHttpControllerResponse
	 */
	public function defaultAction()
	{
		return MainFactory::create('JsonHttpControllerResponse', array('error' => 'invalid action'));
	}
	
	
	/**
	 * Retrieves a number of resources and puts them in a ZIP for later retrieval by the user.
	 *
	 * Input is a JSON-encoded list (array) of URLs. Response is a JSON object with a downloadKey property (cf. actionDownloadPackage())
	 * or an error message.
	 *
	 * @return JsonHttpControllerResponse
	 */
	public function actionDownloadByJson()
	{
		$response = array();
		$rawInput = file_get_contents('php://input');
		$json     = json_decode($rawInput);
		
		try
		{
			$tmpDir = $this->makeTmpDir();
			$files  = array();
			foreach($json->urls as $url)
			{
				$files[] = $this->downloadFile($url, $tmpDir);
			}
			$downloadKey             = $this->packForDownload($tmpDir, $files);
			$response['input']       = $json;
			$response['result']      = 'OK';
			$response['downloadKey'] = $downloadKey;
		}
		catch(Exception $e)
		{
			$response['result']        = 'ERROR';
			$response['error_message'] = $e->getMessage();
		}
		if(!empty($tmpDir)
		   && is_dir($tmpDir)
		) // to be refactored into a finally block when system requirements are raised to PHP 5.5
		{
			$this->cleanUp($tmpDir);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Download a pack of resources retrieved with actionDownloadByJson() identified by a downloadKey
	 * @return HttpControllerResponse (empty; download is initiated prior to standard response handling)
	 */
	public function actionDownloadPackage()
	{
		$downloadKey  = preg_replace('_[^[:alnum:]]_', '', (string)$this->_getQueryParameter('key'));
		$downloadFile = DIR_FS_CATALOG . '/cache/PDC_' . $downloadKey . '.zip';
		if(!(is_file($downloadFile) && is_readable($downloadFile)))
		{
			return MainFactory::create('HttpControllerResponse', 'invalid key');
		}
		ob_clean();
		header('Content-Description: Bundled files');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="PackedDownload_' . date('YmdHis') . '.zip"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($downloadFile));
		readfile($downloadFile);
		unlink($downloadFile);
		
		return MainFactory::create('HttpControllerResponse', '');
	}
	
	
	/**
	 * Retrieve a file/resource and store in $tmpDir
	 * File name is determined from URL.
	 * @param  string $url
	 * @param  string $tmpDir
	 * @return string name of retrieved file
	 */
	protected function downloadFile($url, $tmpDir)
	{
		$targetFile = basename($url);
		if(empty($targetFile))
		{
			throw new Exception('ERROR - could not derive file name from URL');
		}
		$out_fp      = fopen(realpath($tmpDir) . '/' . $targetFile, 'w');
		$followLocation = ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' || ini_get('safe_mode') == false);
		$curlOptions = array(
			CURLOPT_URL            => $url,
			CURLOPT_FOLLOWLOCATION => $followLocation,
			CURLOPT_USERAGENT      => 'Gambio GX2 PackedDownload',
			CURLOPT_FILE           => $out_fp,
		);
		$ch          = curl_init();
		curl_setopt_array($ch, $curlOptions);
		$curl_success = curl_exec($ch);
		$curl_errno   = curl_errno($ch);
		$curl_error   = curl_error($ch);
		$curl_info    = curl_getinfo($ch);
		curl_close($ch);
		fclose($out_fp);
		if($curl_success === false)
		{
			throw new Exception(sprintf("%s - %s", $curl_errno, $curl_error));
		}
		
		return $targetFile;
	}
	
	
	/**
	 * Packs up files in $tmpDir into a ZIP file
	 * @param  string
	 * @param  array
	 * @return downloadKey
	 */
	protected function packForDownload($tmpDir, $files)
	{
		$downloadKey = sha1(uniqid() . (string)microtime(true));
		$zip         = new ZipArchive();
		$zipFileName = DIR_FS_CATALOG . '/cache/PDC_' . $downloadKey . '.zip';
		if($zip->open($zipFileName, ZipArchive::CREATE) !== true)
		{
			throw new Exception('ERROR cannot create archive file');
		}
		$filesOK = true;
		foreach($files as $filename)
		{
			$filesOK = $filesOK && $zip->addFile($tmpDir . '/' . $filename, $filename);
			if($filesOK !== true)
			{
				break;
			}
		}
		$zip->close();
		if($filesOK !== true)
		{
			unlink($zipFileName);
			throw new Exception('ERROR cannot add file to archive');
		}
		
		return $downloadKey;
	}
	
	
	/**
	 * Clean up $tmpDir
	 * @param  $tmpDir
	 * @return bool
	 */
	protected function cleanUp($tmpDir)
	{
		$tmpDirIterator = new DirectoryIterator('glob://' . $tmpDir . '/*');
		foreach($tmpDirIterator as $directoryEntry)
		{
			if($directoryEntry->isFile())
			{
				unlink($directoryEntry->getPathname());
			}
		}
		rmdir($tmpDir);
		
		return true;
	}
	
	
	/**
	 * Makes a new tmpDir location
	 * @return string full path of tmpDir
	 */
	protected function makeTmpDir()
	{
		$baseDir    = DIR_FS_CATALOG . '/cache/';
		$tmpDirName = sha1(uniqid() . (string)microtime(true));
		$tmpDir     = $baseDir . $tmpDirName;
		$success    = mkdir($tmpDir, 0700);
		if($success === false)
		{
			throw new Exception('ERROR creating temporary directory');
		}
		
		return $tmpDir;
	}
}