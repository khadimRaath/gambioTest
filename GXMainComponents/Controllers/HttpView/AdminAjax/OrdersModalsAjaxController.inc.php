<?php

/* --------------------------------------------------------------
   OrdersModalsAjaxController.inc.php 2016-07-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrdersModalsAjaxController
 *
 * AJAX controller for the orders modals.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class OrdersModalsAjaxController extends AdminHttpViewController
{
	/**
	 * Initialize Controller
	 *
	 * @throws Exception
	 */
	public function init()
	{
		$this->_validatePageToken();
	}
	
	
	/**
	 * Stores a tracking number for a specific order.
	 *
	 * @return JsonHttpControllerResponse
	 *
	 * @throws Exception
	 * @throws UnexpectedValueException
	 */
	public function actionStoreTrackingNumber()
	{
		$orderId         = $this->_getPostData('orderId');
		$trackingNumber  = $this->_getPostData('trackingNumber');
		$parcelServiceId = $this->_getPostData('parcelServiceId');
		
		if($parcelServiceId > 0)
		{
			$parcelServiceReader      = MainFactory::create('ParcelServiceReader');
			$parcelTrackingCodeWriter = MainFactory::create('ParcelTrackingCodeWriter');
			
			try
			{
				$parcelTrackingCodeWriter->insertTrackingCode($orderId, $trackingNumber, $parcelServiceId,
				                                              $parcelServiceReader);
				
				return MainFactory::create('JsonHttpControllerResponse', ['success']);
			}
			catch(Exception $e)
			{
				return MainFactory::create('JsonHttpControllerResponse', ['error']);
			}
		}
		
		return MainFactory::create('JsonHttpControllerResponse', ['error']);
	}
	
	
	/**
	 * Change order status.
	 *
	 * @return JsonHttpControllerResponse
	 *
	 * @throws InvalidArgumentException
	 */
	public function actionChangeOrderStatus()
	{
		$orderActions = MainFactory::create('OrderActions');
		
		$orderIds               = $this->_getPostData('selectedOrders');
		$statusId               = new IdType((int)$this->_getPostData('statusId'));
		$comment                = new StringType($this->_getPostData('comment'));
		$notifyCustomer         = new BoolType($this->_getPostData('notifyCustomer'));
		$sendParcelTrackingCode = new BoolType($this->_getPostData('sendParcelTrackingCode'));
		$sendComment            = new BoolType($this->_getPostData('sendComment'));
		
		try
		{
			foreach($orderIds as $orderId)
			{
				$orderActions->changeOrderStatus(new IdType($orderId), $statusId, $comment, $notifyCustomer,
				                                 $sendParcelTrackingCode, $sendComment);
			}
		}
		catch(Exception $e)
		{
			return MainFactory::create('JsonHttpControllerResponse', ['error']);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', ['success']);
	}
	
	
	/**
	 * Download Bulk Invoices PDF.
	 *
	 * This method will provide a concatenated file of invoice PDFs. Provide a GET parameter "o" that contain
	 * the selected order IDs.
	 *
	 * Notice: The "o" is used instead of "orderIds" because the final URL must be as small as possible (some
	 * browsers do not work with GET URL of 100 orders).
	 *
	 * @see OrderActions
	 *
	 * @throws InvalidArgumentException
	 */
	public function actionBulkPdfInvoices()
	{
		$orderActions = MainFactory::create('OrderActions');
		$orderIds     = $this->_getQueryParameter('o');
		$orderActions->bulkPdfInvoices($orderIds);
	}
	
	
	/**
	 * Download Bulk Packing Slips PDF.
	 *
	 * This method will provide a concatenated file of packing slip PDFs. Provide a GET parameter "o" that contain
	 * the selected order IDs.
	 *
	 * Notice: The "o" is used instead of "orderIds" because the final URL must be as small as possible (some
	 * browsers do not work with GET URL of 100 orders).
	 *
	 * @see OrderActions
	 *
	 * @throws InvalidArgumentException
	 */
	public function actionBulkPdfPackingSlips()
	{
		$orderActions = MainFactory::create('OrderActions');
		$orderIds     = $this->_getQueryParameter('o');
		$orderActions->bulkPdfPackingSlips($orderIds);
	}
	
	
	/**
	 * Cancel Order Callback
	 *
	 * This method uses the OrderActions class to cancel an order and fulfill the requirements of the cancellation
	 * (re-stock product, inform customer ...).
	 *
	 * @return JsonHttpControllerResponse
	 *
	 * @throws InvalidArgumentException
	 */
	public function actionCancelOrder()
	{
		$orderActions = MainFactory::create('OrderActions');
		
		$orderIds                  = $this->_getPostData('selectedOrders');
		$restockQuantity           = new BoolType($this->_getPostData('reStock') === 'true');
		$recalculateShippingStatus = new BoolType($this->_getPostData('reShip') === 'true');
		$resetArticleStatus        = new BoolType($this->_getPostData('reActivate') === 'true');
		$notifyCustomer            = new BoolType($this->_getPostData('notifyCustomer') === 'true');
		$sendComment               = new BoolType($this->_getPostData('sendComments') === 'true');
		$comment                   = new StringType($this->_getPostData('cancellationComments'));
		
		$orderActions->cancelOrder($orderIds, $restockQuantity, $recalculateShippingStatus, $resetArticleStatus,
		                           $notifyCustomer, $sendComment, $comment);
		
		return MainFactory::create('JsonHttpControllerResponse', []);
	}
	
	
	/**
	 * Delete Order Callback
	 *
	 * This method uses the OrderActions class to delete an order and fulfill the requirements of the removal
	 * (re-stock product, re-activate ...).
	 *
	 * @return JsonHttpControllerResponse
	 *
	 * @throws InvalidArgumentException
	 */
	public function actionDeleteOrder()
	{
		$orderActions = MainFactory::create('OrderActions');
		
		$orderIds                   = $this->_getPostData('selectedOrders');
		$restockQuantity            = new BoolType($this->_getPostData('reStock') === 'true');
		$recalculateShippingStatus  = new BoolType($this->_getPostData('reShip') === 'true');
		$resetProductShippingStatus = new BoolType($this->_getPostData('reActivate') === 'true');
		
		foreach($orderIds as $orderId)
		{
			$orderActions->removeOrderById(new IdType($orderId), $restockQuantity, $recalculateShippingStatus,
			                               $resetProductShippingStatus);
		}
		
		return MainFactory::create('JsonHttpControllerResponse', []);
	}
	
	
	/**
	 * Get Email-Invoice Subject
	 *
	 * Currently the invoice ID can only be found in by parsing the PDF filename in the /export/invoice directory.
	 */
	public function actionGetEmailInvoiceSubject()
	{
		$subject = gm_get_content('GM_PDF_EMAIL_SUBJECT', $_SESSION['languages_id']);
		$orderId = $this->_getQueryParameter('id');
		
		if(strstr($subject, '{ORDER_ID}'))
		{
			$subject = str_replace('{ORDER_ID}', $orderId, $subject);
		}
		
		$orderDate  = new DateTime($this->_getQueryParameter('date'));
		$dateFormat = $_SESSION['language_code'] === 'de' ? 'd.m.Y' : 'm.d.Y';
		
		if(strstr($subject, '{DATE}'))
		{
			$subject = str_replace('{DATE}', $orderDate->format($dateFormat), $subject);
		}
		
		// Find the last invoice PDF file of the provided order and parse the invoice ID.
		$invoiceFilePattern = glob(DIR_FS_CATALOG . 'export/invoice/' . $orderId . '*');

		$filename = $invoiceFilePattern ? basename(array_pop($invoiceFilePattern)) : '';
		$invoiceIdExists = false;

		if(strlen($filename) > 0 && strstr($subject, '{INVOICE_ID}'))
		{
			$invoiceId = explode('__', $filename)[1];
			$subject   = str_replace('{INVOICE_ID}', $invoiceId, $subject);
			$invoiceIdExists = true;
		}
		
		// Return the response back to the client. 
		return MainFactory::create('JsonHttpControllerResponse',
		                           ['subject' => $subject, 'invoiceIdExists' => $invoiceIdExists]);
	}
	
	
	/**
	 * Get Email-Invoice Subject (Raw Data)
	 *
	 * Currently the invoice ID can only be found in by parsing the PDF filename in the /export/invoice directory.
	 *
	 * This method will return the email subject data instead of the pre-made string.
	 */
	public function actionGetEmailInvoiceSubjectData()
	{
		$orderId = $this->_getQueryParameter('id');
		
		$filename = basename(array_pop(glob(DIR_FS_CATALOG . 'export/invoice/' . $orderId . '*')));
		
		$invoiceId = explode('__', $filename)[1];
		
		$dateFormat = $_SESSION['language_code'] === 'de' ? 'd.m.Y' : 'm.d.Y';
		$orderDate  = new DateTime($this->_getQueryParameter('date'));
		
		$subjectData = [
			'invoiceId' => $invoiceId,
			'date'      => $orderDate->format($dateFormat)
		];
		
		return MainFactory::create('JsonHttpControllerResponse', $subjectData);
	}
}