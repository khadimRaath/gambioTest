<?php
/* --------------------------------------------------------------
   OrderItemDownloadInformation.inc.php 2015-12-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class OrderItemDownloadInformation
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderItemDownloadInformation
{
	/**
	 * Filename
	 *
	 * @var string
	 */
	protected $filename = '';
	
	/**
	 * Maximal number of days for download.
	 *
	 * @var int
	 */
	protected $maxDaysAllowed = 0;
	
	/**
	 * Maximal number of possible downloads.
	 *
	 * @var int
	 */
	protected $countAvailable = 0;
	
	
	/**
	 * OrderItemDownloadInformation constructor.
	 *
	 * @param FilenameStringType $filename
	 * @param IntType            $maxDaysAllowed
	 * @param IntType            $countAvailable
	 */
	public function __construct(FilenameStringType $filename, IntType $maxDaysAllowed, IntType $countAvailable)
	{
		$this->filename       = $filename->asString();
		$this->maxDaysAllowed = $maxDaysAllowed->asInt();
		$this->countAvailable = $countAvailable->asInt();
	}
	
	
	/**
	 * Returns the Filename of the download.
	 * 
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}
	
	
	/**
	 * Returns the number of days where downloads are possible.
	 * 
	 * @return int
	 */
	public function getMaxDaysAllowed()
	{
		return $this->maxDaysAllowed;
	}
	
	
	/**
	 * Returns the number of possible downloads.
	 * 
	 * @return int
	 */
	public function getCountAvailable()
	{
		return $this->countAvailable;
	}
}