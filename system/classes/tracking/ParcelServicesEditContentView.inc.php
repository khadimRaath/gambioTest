<?php
/* --------------------------------------------------------------
  ParcelServicesEditContentView.inc.php 2015-10-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php');

/**
 * Class ParcelServicesEditContentView
 */
class ParcelServicesEditContentView extends LightboxContentView
{
	public function __construct()
	{
		parent::__construct();
		$this->set_template_dir(DIR_FS_CATALOG . 'admin/html/content/tracking/');
		$this->set_content_template('parcel_service_edit.html');
	}

	public function prepare_data()
	{
		if(!isset($this->v_parameters['parcel_service_id']))
		{
			trigger_error('Invalid parcel service id', E_USER_ERROR);
		}

		// load parcelService object
		$parcelServiceID = (int)$this->v_parameters['parcel_service_id'];
		
		if($parcelServiceID > 0)
		{
			$parcelServiceReadService = MainFactory::create_object('ParcelServiceReader');
			$this->set_content_data('parcel_service', $parcelServiceReadService->getParcelServiceById($parcelServiceID));
		}
		else
		{
			$parcelService = MainFactory::create_object('ParcelService');
			$this->set_content_data('parcel_service', $parcelService);
		}

		$this->set_content_data('parcel_service', $this->_cleanParcelService($this->content_array['parcel_service']));
		
		// set languages array
		$languagesArray = gm_get_language();
		$this->set_content_data('languages_array', $languagesArray);
		
		// Set lightbox buttons
		$this->set_lightbox_button('right', 'save', array('save', 'green'));
		$this->set_lightbox_button('right', 'close', array('close'));

		$this->set_content_data('page_token', $_SESSION['coo_page_token']->generate_token());
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
}