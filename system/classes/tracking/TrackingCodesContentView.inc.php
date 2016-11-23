<?php
/* --------------------------------------------------------------
  TrackingCodesContentView.inc.php 2015-10-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class TrackingCodesContentView
 */
class TrackingCodesContentView extends ContentView
{
	protected $orderId;

	/** @var ParcelTrackingCode $coo_parcelTrackingCodeItem */
	protected $parcelTrackingCodeItem;
	/** @var ParcelTrackingCodeReader $coo_parcelTrackingCodes */
	protected $parcelTrackingCodes;
	
	protected $pageToken;


	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/tracking/');
		$this->set_content_template('tracking_codes.html');
	}


	public function prepare_data()
	{
		/** @var ParcelTrackingCode $parcelTrackingCodeItem */
		$parcelTrackingCodeItem = MainFactory::create_object('ParcelTrackingCode');
		$this->parcelTrackingCodeItem = $parcelTrackingCodeItem;

		/** @var ParcelTrackingCodeReader $parcelTrackingCodeReader */
		$parcelTrackingCodeReader = MainFactory::create_object('ParcelTrackingCodeReader');
		$this->parcelTrackingCodes = $parcelTrackingCodeReader;

		$parcelTrackingCodesArray = $parcelTrackingCodeReader->getTackingCodeItemsByOrderId($parcelTrackingCodeItem,
																							  $this->orderId);

		$this->set_content_data('parcel_tracking_codes_array', $parcelTrackingCodesArray);
		$this->set_content_data('orders_id', $this->orderId);
		$this->set_content_data('page_token', $this->pageToken);
		
		/* Options */
		$this->_buildOptionsHTML();
	}


	protected function _buildOptionsHTML()
	{
		/** @var ParcelServiceReader $parcelServiceReadService */
		$parcelServiceReadService = MainFactory::create_object('ParcelServiceReader');
		$allParcelServicesArray = $parcelServiceReadService->getAllParcelServices();

		$parcelOptionsArray = '';

		$selected = array();

		/** @var ParcelService $parcelService */
		foreach($allParcelServicesArray as $parcelService)
		{
			$key = $parcelService->getId();
			$val = $parcelService->getName();
			$parcelOptionsArray[$key] = $val;

			$default = $parcelService->getDefault();
			if($default === 1)
			{
				$selected = $key;
			}

		}

		$this->set_content_data('parcel_tracking_service_options', $parcelOptionsArray);
		$this->set_content_data('parcel_tracking_service_options_selected', $selected);

	}

	/**
	 * @param mixed $p_orderId
	 */
	public function setOrderId($p_orderId)
	{
		$this->orderId = (int)$p_orderId;
	}


	/**
	 * @param string $p_pageToken
	 */
	public function setPageToken($p_pageToken)
	{
		$this->pageToken = (string)$p_pageToken;
	}
}