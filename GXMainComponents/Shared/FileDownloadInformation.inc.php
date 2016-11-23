<?php

/* --------------------------------------------------------------
   FileDownloadInformation.inc.php 2016-07-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class FileDownloadInformation
 *
 * @category   System
 * @package    Shared
 */
class FileDownloadInformation
{
	/**
	 * @var \ExistingFile
	 */
	protected $downloadFile;

	/**
	 * @var \FilenameStringType
	 */
	protected $filename;
 

	/**
	 * FileDownloadInformation constructor.
	 *
	 * @param \ExistingFile       $downloadFile
	 * @param \FilenameStringType $filename
	 */
	public function __construct(ExistingFile $downloadFile, FilenameStringType $filename)
	{
		$this->downloadFile = $downloadFile->getFilePath();
		$this->filename     = $filename->asString();
	}
	

	/**
	 * Returns the path if the download file.
	 *
	 * @return \StringType
	 */
	public function getPath()
	{
		return $this->downloadFile;
	}
	

	/**
	 * Returns the filename of the download file.
	 *
	 * @return \StringType
	 */
	public function getFilename()
	{
		return $this->filename;
	}
}