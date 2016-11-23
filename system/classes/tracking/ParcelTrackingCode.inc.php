<?php
/* --------------------------------------------------------------
  ParcelTrackingCode.php 2014-10-08 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/


/**
 * Class ParcelTrackingCode
 */
class ParcelTrackingCode
{
	protected $trackingCodeId;
	protected $serviceId;
	protected $trackingCode;
	protected $orderId;
	protected $creationDate;
	protected $serviceUrl;
	protected $serviceComment;
	protected $serviceName;
	protected $serviceIsDefault;


	/**
	 * @return bool
	 */
	public function getServiceIsDefault()
	{
		return $this->serviceIsDefault;
	}


	/**
	 * @param bool $p_serviceIsDefault
	 */
	public function setServiceIsDefault($p_serviceIsDefault)
	{
		$this->serviceIsDefault = (bool)$p_serviceIsDefault;
	}


	/**
	 * @return int
	 */
	public function getTrackingCodeId()
	{
		return $this->trackingCodeId;
	}


	/**
	 * @param int $p_trackingCodeId
	 */
	public function setTrackingCodeId($p_trackingCodeId)
	{
		$this->trackingCodeId = (int)$p_trackingCodeId;
	}


	/**
	 * @return int
	 */
	public function getServiceId()
	{
		return $this->serviceId;
	}


	/**
	 * @param int $p_serviceId
	 */
	public function setServiceId($p_serviceId)
	{
		$this->serviceId = (int)$p_serviceId;
	}


	/**
	 * @return string
	 */
	public function getTrackingCode()
	{
		return $this->trackingCode;
	}


	/**
	 * @param string $p_trackingCode
	 */
	public function setTrackingCode($p_trackingCode)
	{
		$this->trackingCode = (string)$p_trackingCode;
	}


	/**
	 * @return int
	 */
	public function getOrderId()
	{
		return $this->orderId;
	}


	/**
	 * @param int $p_orderId
	 */
	public function setOrderId($p_orderId)
	{
		$this->orderId = (int)$p_orderId;
	}


	/**
	 * @return string
	 */
	public function getCreationDate()
	{
		return $this->creationDate;
	}


	/**
	 * @param string $p_creationDate
	 */
	public function setCreationDate($p_creationDate)
	{
		$this->creationDate = (string)$p_creationDate;
	}


	/**
	 * @return string
	 */
	public function getServiceUrl()
	{
		return $this->serviceUrl;
	}


	/**
	 * @param string $p_serviceUrl
	 */
	public function setServiceUrl($p_serviceUrl)
	{
		$this->serviceUrl = (string)$p_serviceUrl;
	}


	/**
	 * @return string
	 */
	public function getServiceComment()
	{
		return $this->serviceComment;
	}


	/**
	 * @param string $p_serviceComment
	 */
	public function setServiceComment($p_serviceComment)
	{
		$this->serviceComment = (string)$p_serviceComment;
	}


	/**
	 * @return string
	 */
	public function getServiceName()
	{
		return $this->serviceName;
	}


	/**
	 * @param string $p_serviceName
	 */
	public function setServiceName($p_serviceName)
	{
		$this->serviceName = (string)$p_serviceName;
	}


	/**
	 * @return string
	 */
	public function getFormatedCreationDate()
	{
		$date = xtc_date_short($this->getCreationDate()) . ' ' . date("H:i", strtotime($this->getCreationDate()));
		
		return $date;
	}
}