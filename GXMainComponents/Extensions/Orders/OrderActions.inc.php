<?php
/* --------------------------------------------------------------
   OrderActions.inc.php 2016-09-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderActions
 *
 * @category   System
 * @package    Extensions
 * @subpackage Orders
 */
class OrderActions
{
	/**
	 * Cancels an order.
	 * The method use the old logic of the gm_send_orders.php file and will be refactored soon.
	 *
	 * @param array           $orderIds
	 * @param BoolType        $restockQuantity
	 * @param BoolType        $recalculateShippingStatus
	 * @param BoolType        $resetArticleStatus
	 * @param BoolType        $notifyCustomer
	 * @param BoolType        $sendComment
	 * @param StringType|null $comment
	 */
	public function cancelOrder(array $orderIds,
	                            BoolType $restockQuantity,
	                            BoolType $recalculateShippingStatus,
	                            BoolType $resetArticleStatus,
	                            BoolType $notifyCustomer,
	                            BoolType $sendComment,
	                            StringType $comment = null)
	{
		require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
		require_once(DIR_FS_INC . 'xtc_get_order_data.inc.php');
		require_once(DIR_FS_INC . 'xtc_get_attributes_model.inc.php');
		require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
		require_once(DIR_FS_INC . 'xtc_format_price_order.inc.php');
		require_once(DIR_WS_CLASSES . 'order.php');
		require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');
		require_once(DIR_FS_CATALOG . 'gm/inc/gm_save_order.inc.php');
		$smarty = MainFactory::create('Smarty');
		
		foreach($orderIds as $oID)
		{
			$order       = MainFactory::create('order', $oID);
			$gm_status   = gm_get_conf('GM_ORDER_STATUS_CANCEL_ID');
			$gm_comments = $comment ? xtc_db_prepare_input($comment->asString()) : '';
			
			$check_status_query = xtc_db_query("
											SELECT
												o.customers_name,
												o.customers_email_address,
												o.orders_status,
												o.language,
												o.date_purchased,
												l.languages_id
											FROM
												" . TABLE_ORDERS . " o
												LEFT JOIN languages l ON (o.language = l.directory)
											WHERE
												o.orders_id = '" . xtc_db_input($oID) . "'
											LIMIT 1
											");
			
			$check_status = xtc_db_fetch_array($check_status_query);
			
			if($check_status['orders_status'] != $gm_status)
			{
				
				xtc_db_query("
						UPDATE " . TABLE_ORDERS . "
						SET
							orders_status	= '" . $gm_status . "',
							last_modified	= now(),
							gm_cancel_date	= now()
						WHERE
							orders_id = '" . xtc_db_input($oID) . "'
						");
				
				if($notifyCustomer->asBool())
				{
					if($sendComment->asBool())
					{
						$notify_comments = $gm_comments;
					}
					else
					{
						$notify_comments = '';
					}
					
					// assign language to template for caching
					$smarty->assign('language', $_SESSION['language']);
					$smarty->caching = false;
					
					// set dirs manual
					$smarty->template_dir = DIR_FS_CATALOG . 'templates';
					$smarty->compile_dir  = DIR_FS_CATALOG . 'templates_c';
					$smarty->config_dir   = DIR_FS_CATALOG . 'lang';
					
					$smarty->assign('tpl_path', 'templates/' . CURRENT_TEMPLATE . '/');
					$smarty->assign('logo_path',
					                HTTP_SERVER . DIR_WS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/img/');
					
					$smarty->assign('NAME', $check_status['customers_name']);
					$smarty->assign('GENDER', $order->customer['gender']);
					$smarty->assign('ORDER_NR', $oID);
					$smarty->assign('ORDER_LINK',
					                xtc_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID,
					                                      'SSL'));
					$smarty->assign('ORDER_DATE', xtc_date_long($check_status['date_purchased']));
					$smarty->assign('NOTIFY_COMMENTS', $notify_comments);
					$smarty->assign('ORDER_STATUS',
					                xtc_get_orders_status_name($gm_status, $check_status['languages_id']));
					
					if(defined('EMAIL_SIGNATURE'))
					{
						$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
						$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
					}
					
					$html_mail = fetch_email_template($smarty, 'change_order_mail', 'html', '',
					                                  $check_status['languages_id']);
					$txt_mail  = fetch_email_template($smarty, 'change_order_mail', 'txt', '',
					                                  $check_status['languages_id']);
					
					$languageTextManager = MainFactory::create('LanguageTextManager', 'gm_order_menu',
					                                           $check_status['languages_id']);
					$subject             = $languageTextManager->get_text('TITLE_GM_CANCEL_SUBJECT_1') . $oID
					                       . $languageTextManager->get_text('TITLE_GM_CANCEL_SUBJECT_2')
					                       . xtc_date_short(date('Y-m-d'))
					                       . $languageTextManager->get_text('TITLE_GM_CANCEL_SUBJECT_3');
					
					xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $check_status['customers_email_address'],
					             $check_status['customers_name'], '', EMAIL_BILLING_REPLY_ADDRESS,
					             EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $html_mail, $txt_mail);
					
					$customer_notified = '1';
					
					xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY
					             . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('"
					             . xtc_db_input($oID) . "', '" . xtc_db_input($gm_status) . "', now(), '"
					             . $customer_notified . "', '" . xtc_db_input($gm_comments) . "')");
					
					$gm_reactivateArticle = $resetArticleStatus->asBool();
					$gm_reshipp           = $recalculateShippingStatus->asBool();
					$restockQuantity->asBool() ? xtc_remove_order($oID, true, true, $gm_reshipp,
					                                              $gm_reactivateArticle) : null;
				}
				else
				{
					$customer_notified = '0';
					
					xtc_db_query("INSERT INTO " . TABLE_ORDERS_STATUS_HISTORY
					             . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('"
					             . xtc_db_input($oID) . "', '" . xtc_db_input($gm_status) . "', now(), '"
					             . $customer_notified . "', '" . xtc_db_input($gm_comments) . "')");
					
					$gm_reactivateArticle = $resetArticleStatus->asBool();
					$gm_reshipp           = $recalculateShippingStatus->asBool();
					$restockQuantity->asBool() ? xtc_remove_order($oID, true, true, $gm_reshipp,
					                                              $gm_reactivateArticle) : null;
				}
			}
		}
	}
	
	
	/**
	 * Removes an order.
	 * The method use the old logic of the xtc_remove_order function and will be refactored soon.
	 *
	 * @param IdType        $orderId
	 * @param BoolType|null $restockQuantity
	 * @param BoolType|null $recalculateShippingStatus
	 * @param BoolType|null $resetProductStatus
	 */
	public function removeOrderById(IdType $orderId,
	                                BoolType $restockQuantity = null,
	                                BoolType $recalculateShippingStatus = null,
	                                BoolType $resetProductStatus = null)
	{
		
		xtc_remove_order($orderId->asInt(), $restockQuantity->asBool(), false, $recalculateShippingStatus->asBool(),
		                 $resetProductStatus->asBool());
	}
	
	
	/**
	 * Outputs the concatenated invoice PDFs.
	 *
	 * @param array $orderIds The order IDs to be included in the concatenated file.
	 *
	 * @throws InvalidArgumentException
	 */
	public function bulkPdfInvoices(array $orderIds)
	{
		$this->_createBulkPdf($orderIds, 'invoice');
	}
	
	
	/**
	 * Outputs the concatenated packing-slip PDFs.
	 *
	 * @param array $orderIds The order IDs to be included in the concatenated file.
	 *
	 * @throws InvalidArgumentException
	 */
	public function bulkPdfPackingSlips(array $orderIds)
	{
		$this->_createBulkPdf($orderIds, 'packingslip');
	}
	
	
	/**
	 * Outputs a bulk PDF file through the use of mPDF.
	 *
	 * This method will parse the latest generated PDFs of the provided orders and will concatenate them into
	 * a single file. This file will be then outputted directly so that browsers download it immediately.
	 *
	 * @param array  $orderIds The selected order IDs.
	 * @param string $type     Bulk PDF type ('invoice' or 'packingslip').
	 *
	 * @throws InvalidArgumentException If $type argument is not valid, and
	 *
	 * @see mPDF
	 */
	protected function _createBulkPdf(array $orderIds, $type)
	{
		if($type !== 'invoice' && $type !== 'packingslip')
		{
			throw new InvalidArgumentException('Invalid bulk PDF type provided (expected "invoice" or "packingslip",'
			                                   . ' got ' . $type . ').');
		}
		
		$mPDF = new mPDF();
		$mPDF->SetImportUse();
		
		$basePath = DIR_FS_CATALOG . 'export/' . $type . '/';
		
		foreach($orderIds as $index => $orderId)
		{
			$filePath  = array_pop(glob($basePath . $orderId . '*'));
			$pageCount = $mPDF->SetSourceFile($filePath);
			
			for($i = 1; $i <= $pageCount; $i++)
			{
				$currentPageNumber = $mPDF->ImportPage($i);
				$mPDF->UseTemplate($currentPageNumber);
				
				$onLastPage = ($index + 1) === count($orderIds);
				
				if(($onLastPage && $i < $pageCount) || (!$onLastPage && $i <=$pageCount))
				{
						$mPDF->AddPage();
				}
			}
		}
		
		// Set PDF permissions depending the database settings. 
		$permissions = $this->_getPdfPermissions();
		$mPDF->setProtection($permissions);
		
		// Output the PDF file for browser download.
		$newFilename = $type === 'invoice' ? 'Invoices' : 'Packing-Slips';
		$mPDF->Output($newFilename . '-' . time() . '.pdf', 'D');
	}
	
	
	/**
	 * @param IdType     $orderId
	 * @param IdType     $statusId
	 * @param StringType $comment
	 * @param BoolType   $notifyCustomer
	 * @param BoolType   $sendParcelTrackingCode
	 * @param BoolType   $sendComment
	 */
	public function changeOrderStatus(IdType $orderId,
	                                  IdType $statusId,
	                                  StringType $comment,
	                                  BoolType $notifyCustomer,
	                                  BoolType $sendParcelTrackingCode,
	                                  BoolType $sendComment)
	{
		require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
		
		$orderReadService  = StaticGXCoreLoader::getService('OrderRead');
		$orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
		$cidb              = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$languageProvider  = MainFactory::create('LanguageProvider', $cidb);
		
		$orderWriteService->updateOrderStatus($orderId, $statusId, $comment, $notifyCustomer);
		
		$order = $orderReadService->getOrderById($orderId);
		
		if($notifyCustomer->asBool() === true)
		{
			$smarty               = MainFactory::create('Smarty');
			$smarty->caching      = false;
			$smarty->template_dir = DIR_FS_CATALOG . 'templates';
			$smarty->compile_dir  = DIR_FS_CATALOG . 'templates_c';
			$smarty->config_dir   = DIR_FS_CATALOG . 'lang';
			
			$mailLogo                 = MainFactory::create_object('GMLogoManager', array('gm_logo_mail'));
			$parcelTrackingCodeItem   = MainFactory::create_object('ParcelTrackingCode');
			$parcelTrackingCodeReader = MainFactory::create_object('ParcelTrackingCodeReader');
			$languageId               = (int)$languageProvider->getIdByCode($order->getLanguageCode());
			$purchaseDate             = $order->getPurchaseDateTime()->format('d.m.Y');
			$fullCustomerName         = $order->getCustomerAddress()->getFirstname() . ' '
			                            . $order->getCustomerAddress()->getLastname();
			$data                     = [
				'GENDER'                      => (string)$order->getCustomerAddress()->getGender(),
				'NAME'                        => $fullCustomerName,
				'ORDER_DATE'                  => $purchaseDate,
				'ORDER_NR'                    => $orderId->asInt(),
				'NOTIFY_COMMENTS'             => $sendComment->asBool() === true ? $comment->asString() : '',
				'ORDER_STATUS'                => $this->_getOrderStatusName(new IdType((int)array_pop($order->getStatusHistory()
				                                                                                            ->getArray())->getOrderStatusId()),
				                                                            new IdType($languageId)),
				'PARCEL_TRACKING_CODES'       => $sendParcelTrackingCode->asBool(),
				'PARCEL_TRACKING_CODES_ARRAY' => $parcelTrackingCodeReader->getTackingCodeItemsByOrderId($parcelTrackingCodeItem,
				                                                                                         $orderId->asInt())
			];
			if($mailLogo->logo_use === '1')
			{
				$data['gm_logo_mail'] = $mailLogo->get_logo();
			}
			if(defined('EMAIL_SIGNATURE'))
			{
				$data['EMAIL_SIGNATURE_HTML'] = nl2br(EMAIL_SIGNATURE);
				$data['EMAIL_SIGNATURE_TEXT'] = EMAIL_SIGNATURE;
			}
			//			@todo: integrate extender
			//			$orderStatusMailExtender = MainFactory::create_object('AdminOrderStatusMailExtenderComponent');
			//			$orderStatusMailExtender->set_data('GET', $this->_getQueryParametersCollection()->getArray());
			//			$orderStatusMailExtender->set_data('POST', $this->_getPostDataCollection()->getArray());
			//			$orderStatusMailExtender->set_data('action', $this->_getQueryParameter('action'));
			//			$orderStatusMailExtender->proceed();
			//			if(is_array($orderStatusMailExtender->v_output_buffer))
			//			{
			//				$data = array_merge($data, $orderStatusMailExtender->v_output_buffer);
			//			}
			
			foreach($data as $key => $value)
			{
				$smarty->assign($key, $value);
			}
			
			$txtMail = fetch_email_template($smarty, 'change_order_mail', 'txt', '', $languageId);
			$smarty->assign('NOTIFY_COMMENTS', nl2br($data['NOTIFY_COMMENTS']));
			$htmlMail = fetch_email_template($smarty, 'change_order_mail', 'html', '', $languageId);
			
			$languageTextManager = MainFactory::create_object('LanguageTextManager',
			                                                  array('gm_order_menu', $languageId), true);
			$subject             = $languageTextManager->get_text('CHANGE_ORDER_STATUS_SUBJECT') . $orderId . ', '
			                       . $purchaseDate . ', ' . $fullCustomerName;
			
			xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME, $order->getCustomerEmail(), $fullCustomerName, '',
			             EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $subject, $htmlMail,
			             $txtMail);
		}
	}
	
	
	/**
	 * Gets the Name of an order status by status ID and language ID
	 *
	 * @param IdType $orderStatusId The status ID
	 * @param IdType $languageId    The language ID
	 *
	 * @return string The name of the status
	 */
	protected function _getOrderStatusName(IdType $orderStatusId, IdType $languageId)
	{
		$statusName = '';
		$cidb       = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$result     = $cidb->select('orders_status_name')->from('orders_status')->where([
			                                                                                'orders_status_id' => $orderStatusId->asInt(),
			                                                                                'language_id'      => $languageId->asInt()
		                                                                                ])->get()->result_array();
		if(count($result) > 0)
		{
			$statusName = $result[0]['orders_status_name'];
		}
		
		return $statusName;
	}
	
	
	/**
	 * Generate the PDF permissions depending the shop configuration.
	 * 
	 * @link https://mpdf.github.io/reference/mpdf-functions/setprotection.html
	 * 
	 * @return array
	 */
	protected function _getPdfPermissions()
	{
		$permissions = [
			'print',
			'fill-forms',
			'extract',
			'assemble',
			'print-highres'
		];
		
		if(filter_var(gm_get_conf('GM_PDF_ALLOW_COPYING'), FILTER_VALIDATE_BOOLEAN))
		{
			$permissions[] = 'copy';
		}
		
		if(filter_var(gm_get_conf('GM_PDF_ALLOW_NOTIFYING'), FILTER_VALIDATE_BOOLEAN))
		{
			$permissions[] = 'annot-forms';
		}
		
		if(filter_var(gm_get_conf('GM_PDF_ALLOW_MODIFYING'), FILTER_VALIDATE_BOOLEAN))
		{
			$permissions[] = 'modify';
		}
		
		return $permissions; 
	}
}
