<?php
/* --------------------------------------------------------------
  ParcelServicesOverviewContentView.inc.php 2015-10-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class ParcelServicesOverviewContentView extends ContentView
{
	protected $pageToken;
	
	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/tracking/');
		$this->set_content_template('parcel_services_overview.html');
	}
	
	public function prepare_data()
	{
		$parcelServiceReadService = MainFactory::create_object('ParcelServiceReader');
		$this->content_array['parcel_services_array'] = $parcelServiceReadService->getAllParcelServices();
		$this->content_array['parcel_services_array'] = $this->_cleanAllParcelServices($this->content_array['parcel_services_array']);
		$this->content_array['page_token'] = $this->pageToken;
	}


	/**
	 * @param array $parcelServiceArray
	 *
	 * @return array
	 */
	protected function _cleanAllParcelServices(array $parcelServiceArray)
	{
		foreach($parcelServiceArray as $key => $parcel_service)
		{
			$parcelServiceArray[$key] = $this->_cleanParcelService($parcel_service);
		}
		
		return $parcelServiceArray;
	}


	/**
	 * @param ParcelService $parcelService
	 *
	 * @return ParcelService
	 */
	protected function _cleanParcelService(ParcelService $parcelService)
	{
		$parcelService->setName(htmlspecialchars_wrapper($parcelService->getName()));
		
		$urlArray = $parcelService->getUrlArray();
		foreach ($urlArray as $languageId => $url)
		{
			$parcelService->setUrlByLanguageId($languageId, htmlspecialchars_wrapper($url));
		}

		$commentArray = $parcelService->getCommentArray();
		foreach ($commentArray as $languageId => $comment)
		{
			$parcelService->setCommentByLanguageId($languageId, htmlspecialchars_wrapper($comment));
		}
		
		return $parcelService;
	}


	/**
	 * @param string $p_pageToken
	 */
	public function setPageToken($p_pageToken)
	{
		$this->pageToken = (string)$p_pageToken;
	}
}