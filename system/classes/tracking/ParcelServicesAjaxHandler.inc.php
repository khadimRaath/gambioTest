<?php
/* --------------------------------------------------------------
   ParcelServicesAjaxHandler.inc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

/**
 * Class ParcelServicesAjaxHandler
 */
class ParcelServicesAjaxHandler extends AjaxHandler
{
	/**
	 * @param null $p_customers_id
	 *
	 * @return bool
	 */
	public function get_permission_status($p_customers_id = null)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}
		return false;
	}


	/**
	 * @return bool
	 */
	public function proceed()
	{
		$t_responseArray = array();
		$t_action = $this->v_data_array['GET']['action'];

		switch( $t_action )
		{
			case 'save_parcel_service':
				$parcelServiceDataArray = array();
				parse_str($this->v_data_array['POST']['form_data'], $parcelServiceDataArray);
				
				if($_SESSION['coo_page_token']->is_valid($parcelServiceDataArray['page_token']))
				{
					/** @var ParcelService $parcelService */
					$parcelService = MainFactory::create_object('ParcelService');
	
					/** @var ParcelServiceWriter $parcelServiceWriter */
					$parcelServiceWriter = MainFactory::create_object('ParcelServiceWriter');
	
					/** @var ParcelServicesOverviewContentView $parcelServicesOverviewView */
					$parcelServicesOverviewView = MainFactory::create_object('ParcelServicesOverviewContentView');
					$parcelServicesOverviewView->setPageToken($_SESSION['coo_page_token']->generate_token());
					
					$this->_saveParcelService($this->v_data_array['POST']['parcel_service_id'], $parcelServiceDataArray, $parcelService, $parcelServiceWriter);
					
					$t_responseArray['status'] = 'success';
					$t_responseArray['html'] = $parcelServicesOverviewView->get_html();
				}
				break;
			
			case 'delete_parcel_service':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					/** @var ParcelServiceWriter $parcelServiceWriter */
					$parcelServiceWriter = MainFactory::create_object('ParcelServiceWriter');
					
					$this->_deleteParcelService($this->v_data_array['POST']['parcel_service_id'], $parcelServiceWriter);
					
					$parcelServicesOverviewView = MainFactory::create_object( 'ParcelServicesOverviewContentView' );
					$parcelServicesOverviewView->setPageToken($_SESSION['coo_page_token']->generate_token());
					
					$t_responseArray['status'] = 'success';
					$t_responseArray['html'] = $parcelServicesOverviewView->get_html();
				}
				break;

			case 'add_tracking_code':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$trackingCode = xtc_db_prepare_input($this->v_data_array['POST']['tracking_code']);
	
					/** @var ParcelServiceReader $parcelServiceReader */
					$parcelServiceReader = MainFactory::create_object('ParcelServiceReader');
					
					/** @var ParcelTrackingCodeWriter $parcelTrackingCodeWriter */
					$parcelTrackingCodeWriter = MainFactory::create_object('ParcelTrackingCodeWriter');
					
					$this->_addTrackingCode($this->v_data_array['POST']['order_id'], $trackingCode, $this->v_data_array['POST']['service_id'],
											$parcelServiceReader, $parcelTrackingCodeWriter);
					
					/** @var TrackingCodesContentView $view */
					$view = MainFactory::create_object('TrackingCodesContentView');
	
					$view->setOrderId($this->v_data_array['POST']['order_id']);
					$view->setPageToken($_SESSION['coo_page_token']->generate_token());
	
					$t_responseArray['status'] = 'success';
					$t_responseArray['html'] = $view->get_html();
				}
				break;

			case 'delete_tracking_code':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					/** @var ParcelTrackingCodeWriter $parcelTrackingCodeWriter */
					$parcelTrackingCodeWriter = MainFactory::create_object('ParcelTrackingCodeWriter');
					
					$this->_deleteTrackingCode($this->v_data_array['POST']['tracking_code_id'], $parcelTrackingCodeWriter);
					
					$orderId = (int)$this->v_data_array['POST']['order_id'];
					
					/** @var TrackingCodesContentView $view */
					$view = MainFactory::create_object('TrackingCodesContentView');
	
					$view->setOrderId($orderId);
					$view->setPageToken($_SESSION['coo_page_token']->generate_token());
					
					$t_responseArray['status'] = 'success';
					$t_responseArray['html'] = $view->get_html();
				}
				break;

			default:
				trigger_error('t_action_request not found: '. htmlentities($t_action_request), E_USER_WARNING);
				return false;
		}

		$coo_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$t_output_json = $coo_json->encode($t_responseArray);

		$this->v_output_buffer = $t_output_json;
		
		return true;
	}


	/**
	 * @param int                 $p_parcelServiceId
	 * @param array               $parcelServiceDataArray
	 * @param ParcelService       $parcelService
	 * @param ParcelServiceWriter $parcelServiceWriter
	 *
	 * @return bool
	 */
	protected function _saveParcelService($p_parcelServiceId, array $parcelServiceDataArray, ParcelService $parcelService, ParcelServiceWriter $parcelServiceWriter)
	{
		$parcelServiceId = (int)$p_parcelServiceId;

		$parcelService->setName($parcelServiceDataArray['parcel_service']['name']);
		$default = 0;
		if(isset($parcelServiceDataArray['parcel_service']['default']) && (int)$parcelServiceDataArray['parcel_service']['default'] != 0)
		{
			$default = 1;
		}
		$parcelService->setDefault($default);
		$parcelService->setUrlArray($parcelServiceDataArray['parcel_service']['url']);
		$parcelService->setCommentArray($parcelServiceDataArray['parcel_service']['comment']);

		if($parcelServiceId == 0)
		{
			$parcelServiceWriter->insertParcelService($parcelService);
		}
		else
		{
			$parcelService->setId($parcelServiceId);
			$parcelServiceWriter->updateParcelService($parcelService);
		}

		return true;
	}


	/**
	 * @param int                 $p_parcelServiceId
	 * @param ParcelServiceWriter $parcelServiceWriter
	 *
	 * @return bool
	 */
	protected function _deleteParcelService($p_parcelServiceId, ParcelServiceWriter $parcelServiceWriter)
	{
		$parcelServiceId = (int)$p_parcelServiceId;

		if($parcelServiceId > 0)
		{
			$parcelServiceWriter->deleteParcelService($parcelServiceId);
			
			return true;
		}
		
		return false;
	}


	/**
	 * @param int                      $p_orderId
	 * @param string                   $p_trackingCode
	 * @param int                      $p_parcelServiceId
	 * @param ParcelServiceReader      $parcelServiceReader
	 * @param ParcelTrackingCodeWriter $parcelServiceWriter
	 *
	 * @return bool
	 */
	protected function _addTrackingCode($p_orderId, $p_trackingCode, $p_parcelServiceId,
										ParcelServiceReader $parcelServiceReader,
										ParcelTrackingCodeWriter $parcelServiceWriter)
	{
		$parcelServiceWriter->insertTrackingCode((int)$p_orderId, $p_trackingCode, (int)$p_parcelServiceId,
															 $parcelServiceReader);
		
		return true;
	}


	/**
	 * @param int                      $p_trackingCodeId
	 * @param ParcelTrackingCodeWriter $parcelTrackingCodeWriter
	 *
	 * @return bool
	 */
	protected function _deleteTrackingCode($p_trackingCodeId, ParcelTrackingCodeWriter $parcelTrackingCodeWriter)
	{
		$parcelTrackingCodeWriter->deleteTrackingCode($p_trackingCodeId);
		
		return true;
	}
}