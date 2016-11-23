<?php
/* --------------------------------------------------------------
   JobManagerAjaxHandlerinc.php 2016-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

/**
 * Class JobManagerAjaxHandler
 */
class JobManagerAjaxHandler extends AjaxHandler
{
	/**
	 * @param null|int $p_customersId
	 *
	 * @return bool
	 */
	public function get_permission_status($p_customersId = null)
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
		$responseArray = array();
		$action = $this->v_data_array['GET']['action'];

		switch($action)
		{
			case 'save_shop_notice_job':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$responseArray['id'] = $this->_saveShopNoticeJob($this->v_data_array['POST']);
					$responseArray['page_token'] = $_SESSION['coo_page_token']->generate_token();
					$responseArray['success'] = true;
				}
				break;
			case 'delete_shop_notice_job':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$this->_deleteShopNoticeJob($this->v_data_array['POST']['id']);
					$responseArray['page_token'] = $_SESSION['coo_page_token']->generate_token();
					$responseArray['success'] = true;
				}
				break;
			case 'hide_shop_notice_job':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$this->_hideShopNoticeJob(key($this->v_data_array['POST']['date']));
					$responseArray['page_token'] = $_SESSION['coo_page_token']->generate_token();
					$responseArray['success'] = true;
				}
				break;
			case 'save_field_replace_job':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$responseArray['id'] = $this->_saveFieldReplaceJob($this->v_data_array['POST']);
					$responseArray['page_token'] = $_SESSION['coo_page_token']->generate_token();
					$responseArray['success'] = true;
				}
				break;
			case 'delete_field_replace_job':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$this->_deleteFieldReplaceJob($this->v_data_array['POST']['id']);
					$responseArray['page_token'] = $_SESSION['coo_page_token']->generate_token();
					$responseArray['success'] = true;
				}
				break;
			case 'hide_field_replace_job':
				if($_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']))
				{
					$this->_hideFieldReplaceJob(key($this->v_data_array['POST']['id']));
					$responseArray['page_token'] = $_SESSION['coo_page_token']->generate_token();
					$responseArray['success'] = true;
				}
				break;
			case 'get_shipping_status_options':
				$responseArray['data'] = $this->_getShippingStatusOptions();
				$responseArray['success'] = true;
				break;
			case 'get_price_status_options':
				$responseArray['data'] = $this->_getPriceStatusOptions();
				$responseArray['success'] = true;
				break;
			default:

				if($this->_proceedOverloadAction($action) === false)
				{
					trigger_error('t_action_request not found: '. htmlentities_wrapper( $action ), E_USER_WARNING);
					return false;
				}
		}

		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$outputJson = $json->encode($responseArray);

		$this->v_output_buffer = $outputJson;

		return true;
	}


	/**
	 * @param array $formDataArray
	 *
	 * @return array
	 */
	protected function _prepareShopNoticeData(array $formDataArray)
	{
		$shopNoticeDataArray = array();

		$id = (int)key($formDataArray['date']);
		
		$shopNoticeDataArray['id'] = $id;
		
		// Quick Fix: GX-Bug #41801
		//
		// The problem lies in the "window.gx.lib.form.getData()" function of the "common.js" file, which is 
		// part of the JS Engine. The shop online/offline page uses the "table_inline_edit.js" component to grab the 
		// data from the form and send them to the server. The above function will not parse the form correctly due
		// to a change made for another page (check the "checkbox" case of the "switch" statement). 
		//
		// Involved Files: 
		// shop_notice_jobs.html, common.js, table_inline_edit.js, JobManagerAjaxHandler.inc.php
		//
		// Commit link (check "common.js" file):
		// http://sources.gambio-server.net/gambio/gxdev/commit/99ce655a35ba359a50108c970f6439544623987b
		//
		// @todo Find a more permanent solution for the problem #41801 (this solution was only due to lack of time).
		//
		// The quick fix will grab the value from the $formDataArray without checking for the $id value, 
		// like the other field does. This is why we use the array_shift() function to get the first array
		// element.
		
		$offline = (isset($formDataArray['offline'])) ? array_shift($formDataArray['offline']) : null; 
		$topbar = (isset($formDataArray['topbar'])) ? array_shift($formDataArray['topbar']) : null;
		$popup = (isset($formDataArray['popup'])) ? array_shift($formDataArray['popup']) : null;  
		
		
		$shopNoticeDataArray['shop_active'] = ($offline == 'true' || $offline == 'checked') ? false : true;
		$shopNoticeDataArray['offline_content'] = xtc_db_prepare_input($formDataArray['offline_msg'][$id]);

		$shopNoticeDataArray['topbar_active'] = ($topbar == 'true' || $topbar == 'checked') ? true : false;
		$shopNoticeDataArray['topbar_color'] = $formDataArray['topbar_color'][$id];
		$shopNoticeDataArray['topbar_mode'] = $formDataArray['topbar_mode'][$id];
		$shopNoticeDataArray['topbar_content'] = array();
		
		$shopNoticeDataArray['popup_active'] = ($popup == 'true' || $popup == 'checked') ? true : false;
		$shopNoticeDataArray['popup_content'] = array();
		
		$time = strtotime($formDataArray['date'][$id] . ' ' .
						  $formDataArray['hours'][$id] . ':' . $formDataArray['minutes'][$id]);
		$date = date('Y-m-d H:i:s', $time);

		$shopNoticeDataArray['date'] = $date;
		$shopNoticeDataArray['name'] = xtc_db_prepare_input($formDataArray['name'][$id]);
		
		/* @var ShopLanguageReader $languageReader */
		$languageReader = MainFactory::create_object('ShopLanguageReader');
		$shopLanguageArray = $languageReader->getAll();

		foreach($shopLanguageArray as $shopLanguage)
		{
			$languageId   = $shopLanguage->getLanguageId();
			$languageCode = $shopLanguage->getLanguageCode();

			if(isset($formDataArray['popup_msg'][$id][$languageId]))
			{
				$shopNoticeDataArray['popup_content'][$languageCode] = xtc_db_prepare_input($formDataArray['popup_msg'][$id][$languageId]);
			}

			if(isset($formDataArray['topbar_msg'][$id][$languageId]))
			{
				$shopNoticeDataArray['topbar_content'][$languageCode] = xtc_db_prepare_input($formDataArray['topbar_msg'][$id][$languageId]);
			}
		}
		
		return $shopNoticeDataArray;
	}


	/**
	 * @param ShopLanguageReader $languageReader
	 * @param array              $shopNoticeDataArray
	 *
	 * @return ShopNoticeJob
	 */
	protected function _buildNoticeJob(ShopLanguageReader $languageReader, array $shopNoticeDataArray)
	{
		/* @var ShopNoticeJob $noticeJob */
		$noticeJob = MainFactory::create_object('ShopNoticeJob', array($languageReader));
		$noticeJob->setShopActive($shopNoticeDataArray['shop_active']);
		$noticeJob->setShopOfflineContent($shopNoticeDataArray['offline_content']);

		$noticeJob->setTopbarActive($shopNoticeDataArray['topbar_active']);
		$noticeJob->setTopbarColor($shopNoticeDataArray['topbar_color']);
		$noticeJob->setTopbarMode($shopNoticeDataArray['topbar_mode']);

		$noticeJob->setPopupActive($shopNoticeDataArray['popup_active']);

		foreach($shopNoticeDataArray['popup_content'] as $languageCode => $content)
		{
			$noticeJob->setPopupContent($languageCode, $content);
		}

		foreach($shopNoticeDataArray['topbar_content'] as $languageCode => $content)
		{
			$noticeJob->setTopbarContent($languageCode, $content);
		}

		if($shopNoticeDataArray['id'] > 0)
		{
			$noticeJob->setShopNoticeJobId($shopNoticeDataArray['id']);
		}
		
		return $noticeJob;
	}


	/**
	 * @param array $formDataArray
	 *
	 * @return int
	 */
	protected function _saveShopNoticeJob(array $formDataArray)
	{
		$shopNoticeDataArray = $this->_prepareShopNoticeData($formDataArray);
		
		/* @var ShopLanguageReader $languageReader */
		$languageReader = MainFactory::create_object('ShopLanguageReader');

		$noticeJob = $this->_buildNoticeJob($languageReader, $shopNoticeDataArray);
		
		$dueDate = new DateTime($shopNoticeDataArray['date']);
		$callback = 'ShopNotice';
		$subject = $shopNoticeDataArray['name'];
		
		/* @var JobQueueReception $jobQueueReception */
		$jobQueueReception = MainFactory::create_object('JobQueueReception');

		/* @var WaitingTicket $waitingTicket */
		$waitingTicket = $jobQueueReception->createWaitingTicket($dueDate, $callback, $subject);

		$noticeJob->setWaitingNumber($waitingTicket->getWaitingNumber() );

		/* @var ShopNoticeJobWriter $noticeJobWriter */
		$noticeJobWriter = MainFactory::create_object('ShopNoticeJobWriter', array($languageReader));
		$noticeJobWriter->write($noticeJob);
		
		return $noticeJob->getShopNoticeJobId();
	}


	/**
	 * @param int $p_shopNoticeJobId
	 *
	 * @return bool
	 */
	protected function _deleteShopNoticeJob($p_shopNoticeJobId)
	{
		$shopNoticeJobId = (int)$p_shopNoticeJobId;
		
		if($shopNoticeJobId > 0)
		{
			$shopLanguageReader = MainFactory::create_object('ShopLanguageReader');
			// find job
			/* @var ShopNoticeJobReader $jobReader */
			$jobReader = MainFactory::create_object('ShopNoticeJobReader', array($shopLanguageReader));
			
			/* @var ShopNoticeJob $noticeJob */
			$noticeJob = $jobReader->getById($shopNoticeJobId);

			// cancel due date
			/* @var JobQueueReception $jobQueueReception */
			$jobQueueReception = MainFactory::create_object('JobQueueReception');
			$jobQueueReception->cancelWaitingTicket($noticeJob->getWaitingNumber() );

			// delete job
			/* @var ShopNoticeJobDeleter $noticeJobDeleter */
			$noticeJobDeleter = MainFactory::create_object('ShopNoticeJobDeleter');
			$noticeJobDeleter->delete($noticeJob);
			
			return true;
		}
		
		return false;
	}


	/**
	 * @param int $p_shopNoticeJobId
	 *
	 * @return bool
	 */
	protected function _hideShopNoticeJob($p_shopNoticeJobId)
	{
		$shopNoticeJobId = (int)$p_shopNoticeJobId;

		if($shopNoticeJobId > 0)
		{
			/* @var ShopLanguageReader $languageReader */
			$languageReader = MainFactory::create_object('ShopLanguageReader');

			// find job
			/* @var ShopNoticeJobReader $jobReader */
			$jobReader = MainFactory::create_object('ShopNoticeJobReader', array($languageReader));

			/* @var ShopNoticeJob $noticeJob */
			$noticeJob = $jobReader->getById($shopNoticeJobId);
			$noticeJob->setHidden(true);

			/* @var ShopNoticeJobWriter $noticeJobWriter */
			$noticeJobWriter = MainFactory::create_object('ShopNoticeJobWriter', array($languageReader));
			$noticeJobWriter->write($noticeJob);

			return true;
		}

		return false;
	}


	/**
	 * @param array $formDataArray
	 *
	 * @return int
	 */
	protected function _saveFieldReplaceJob(array $formDataArray)
	{
		$fieldReplaceDataArray = $this->_prepareFieldReplaceData($formDataArray);

		if($fieldReplaceDataArray['type'] == 'shipping_status')
		{
			$fieldName = 'products_shippingtime';
		}
		else
		{
			$fieldName = 'gm_price_status';
		}
		
		$oldShippingTimeId = $fieldReplaceDataArray['old_value'];
		$newShippingTimeId = $fieldReplaceDataArray['new_value'];

		$dueDate = new DateTime($fieldReplaceDataArray['date']);
		$callback = 'ProductsFieldReplace';
		$subject = $fieldReplaceDataArray['name'];

		$fieldReplaceJobId = null;
		if($fieldReplaceDataArray['id'] > 0)
		{
			$fieldReplaceJobId = $fieldReplaceDataArray['id'];
		}
		
		/* @var ProductsFieldReplaceJob $replaceJob */
		$replaceJob = MainFactory::create_object('ProductsFieldReplaceJob', array($fieldName, $oldShippingTimeId, $newShippingTimeId, $fieldReplaceJobId) );

		/* @var JobQueueReception $jobQueueReception */
		$jobQueueReception = MainFactory::create_object('JobQueueReception');
		
		/* @var WaitingTicket $waitingTicket */
		$waitingTicket = $jobQueueReception->createWaitingTicket($dueDate, $callback, $subject);
		
		$replaceJob->setWaitingNumber($waitingTicket->getWaitingNumber() );

		/* @var FieldReplaceJobWriter $replaceJobWriter */
		$replaceJobWriter = MainFactory::create_object('FieldReplaceJobWriter');
		$replaceJobWriter->write($replaceJob);
		
		return $replaceJob->getFieldReplaceJobId();
	}


	protected function _prepareFieldReplaceData(array $formDataArray)
	{
		$fieldReplaceDataArray = array();

		$id = (int)key($formDataArray['date']);

		$fieldReplaceDataArray['id'] = $id;

		$time = strtotime($formDataArray['date'][$id] . ' ' .
						  $formDataArray['hours'][$id] . ':' . $formDataArray['minutes'][$id]);
		$date = date('Y-m-d H:i:s', $time);

		$fieldReplaceDataArray['date'] = $date;
		$fieldReplaceDataArray['name'] = xtc_db_prepare_input($formDataArray['name'][$id]);

		$fieldReplaceDataArray['old_value'] = $formDataArray['old_value'][$id];
		$fieldReplaceDataArray['new_value'] = $formDataArray['new_value'][$id];

		$fieldReplaceDataArray['type'] = $formDataArray['type'][$id];
		
		return $fieldReplaceDataArray;
	}


	/**
	 * @param int $p_fieldReplaceJobId
	 *
	 * @return bool
	 */
	protected function _deleteFieldReplaceJob($p_fieldReplaceJobId)
	{
		$fieldReplaceJobId = (int)$p_fieldReplaceJobId;

		if($fieldReplaceJobId > 0)
		{
			// find job
			/* @var FieldReplaceJobReader $jobReader */
			$jobReader = MainFactory::create_object('FieldReplaceJobReader');

			/* @var FieldReplaceJob $fieldReplaceJob */
			$fieldReplaceJob = $jobReader->getById($fieldReplaceJobId);

			// cancel due date
			/* @var JobQueueReception $jobQueueReception */
			$jobQueueReception = MainFactory::create_object('JobQueueReception');
			$jobQueueReception->cancelWaitingTicket($fieldReplaceJob->getWaitingNumber() );

			// delete job
			/* @var FieldReplaceJobDeleter $fieldReplaceJobDeleter */
			$fieldReplaceJobDeleter = MainFactory::create_object('FieldReplaceJobDeleter');
			$fieldReplaceJobDeleter->delete($fieldReplaceJob);

			return true;
		}

		return false;
	}

	protected function _hideFieldReplaceJob($p_fieldReplaceJobId)
	{
		$fieldReplaceJobId = (int)$p_fieldReplaceJobId;

		if($fieldReplaceJobId > 0)
		{
			// find job
			/* @var FieldReplaceJobReader $jobReader */
			$jobReader = MainFactory::create_object('FieldReplaceJobReader');

			/* @var FieldReplaceJob $fieldReplaceJob */
			$fieldReplaceJob = $jobReader->getById($fieldReplaceJobId);
			$fieldReplaceJob->setHidden(true);
			
			/* @var FieldReplaceJobWriter $replaceJobWriter */
			$replaceJobWriter = MainFactory::create_object('FieldReplaceJobWriter');
			$replaceJobWriter->write($fieldReplaceJob);

			return true;
		}

		return false;
	}


	/**
	 * @return array
	 */
	protected function _getShippingStatusOptions()
	{
		return $this->_buildOptionsArray(xtc_get_shipping_status());
	}
	
	
	/**
	 * @return array
	 */
	protected function _getPriceStatusOptions()
	{
		/** @var LanguageTextManager $langFileMaster */
		$langFileMaster = MainFactory::create_object('LanguageTextManager', array(), true);
		$langFileMaster->init_from_lang_file('lang/' . basename($_SESSION['language']) . '/admin/categories.php');
		
		$statusArray = array();
		$statusArray[] = array('id' => 0, 'text' => GM_PRICE_STATUS_0);
		$statusArray[] = array('id' => 1, 'text' => GM_PRICE_STATUS_1);
		$statusArray[] = array('id' => 2, 'text' => GM_PRICE_STATUS_2);

		return $this->_buildOptionsArray($statusArray);
	}


	protected function _buildOptionsArray($dataArray)
	{
		$optionsArray = array();
		$optionsArray['.target_1'] = array();
		$optionsArray['.target_2'] = array();

		foreach($dataArray as $valueArray)
		{
			$optionsArray['.target_1'][] = array('value' => $valueArray['id'], 'name' => $valueArray['text']);
			$optionsArray['.target_2'][] = array('value' => $valueArray['id'], 'name' => $valueArray['text']);
		}

		return $optionsArray;
	}
	

	/**
	 * use this method for adding new action-case
	 * @param $p_action
	 *
	 * @return bool
	 */
	protected function _proceedOverloadAction($p_action)
	{
		return false;
	}
} 