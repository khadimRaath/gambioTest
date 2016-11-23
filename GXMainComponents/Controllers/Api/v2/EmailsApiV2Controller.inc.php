<?php
/* --------------------------------------------------------------
   EmailsApiV2Controller.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class EmailsApiV2Controller
 *
 * This controller provides a gateway to the EmailService of the system. The user is able to get, send, queue
 * and delete email records through his shop. Email forwarding is not directly supported but can be easily
 * implemented by getting an existing email and then sending it with the updated contact addresses.
 *
 * API consumers can also upload attachments through the AttachmentsApiV2Controller and later use them within
 * their emails. The upload operation must be executed before sending/queuing the email because it is not possible
 * to send the email JSON data and upload a file at the same time. For more info see the AttachmentsV2Controller.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class EmailsApiV2Controller extends HttpApiV2Controller
{
	/**
	 * E-Mail service.
	 * 
	 * @var EmailService
	 */
	protected $emailService;

	/**
	 * E-Mail serializer.
	 * 
	 * @var EmailJsonSerializer
	 */
	protected $emailSerializer;


	/**
	 * Initialize controller components.
	 */
	protected function __initialize()
	{
		$this->emailService    = StaticGXCoreLoader::getService('Email');
		$this->emailSerializer = MainFactory::create('EmailJsonSerializer');
	}


	/**
	 * @api             {post} /emails/:id Send Email
	 * @apiVersion      2.1.0
	 * @apiName         SendEmail
	 * @apiGroup        Emails
	 *
	 * @apiDescription
	 * This method will send and save a new or an existing email to the system. If you include mail attachments
	 * then they must already exist in the server. You will need to provide the full path to the file. To see an
	 * example usage take a look at
	 * `docs/REST/samples/email-service/send_email.php`
	 *
	 * @apiParamExample {json} Request-Body
	 * {
	 *   "subject": "Test Subject",
	 *   "sender": {
	 *     "emailAddress": "sender@email.de",
	 *     "contactName": "John Doe"
	 *   },
	 *   "recipient": {
	 *     "emailAddress": "recipient@email.de",
	 *     "contactName": "Jane Doe"
	 *   },
	 *   "replyTo": {
	 *     "emailAddress": "reply_to@email.de",
	 *     "contactName": "John Doe (Reply To)"
	 *   },
	 *   "contentHtml": "<strong>HTML Content</content>",
	 *   "contentPlain": "Plain Content",
	 *   "bcc": [
	 *     {
	 *       "emailAddress": "bcc@email.de",
	 *       "contactName": "Chris Doe"
	 *     }
	 *   ],
	 *   "cc": [
	 *     {
	 *       "emailAddress": "cc@email.de",
	 *       "contactName": "Chloe Doe"
	 *     }
	 *   ],
	 *   "attachments": [
	 *     {
	 *       "path": "/var/www/html/shop/uploads/attachments/1434614398/myfile.txt",
	 *       "name": "Display For MyFile.txt"
	 *     }
	 *   ]
	 * }
	 *
	 * @apiParam {Number} [id] If provided then an existing email will be resend (only applies to URL).
	 * @apiParam {String} [subject] Email subject to be sent.
	 * @apiParam {Object} sender Contains the sender contact data.
	 * @apiParam {String} sender.emailAddress Sender's email address.
	 * @apiParam {String} [sender.contactName] Sender display name.
	 * @apiParam {Object} recipient Contains the recipient contact data.
	 * @apiParam {String} recipient.emailAddress Recipient's email address.
	 * @apiParam {String} [recipient.contactName] Recipient's display name.
	 * @apiParam {Object} replyTo Contains the reply to contact data.
	 * @apiParam {String} replyTo.emailAddress Email address of the 'Reply-To' contact.
	 * @apiParam {String} replyTo.contactName Name of the 'Reply-To' contact.
	 * @apiParam {String} [contentHtml] Email plain content.
	 * @apiParam {String} [contentPlain] Email HTML content.
	 * @apiParam {Array} [bcc] Contains the BCC contacts of the email.
	 * @apiParam {Array} [cc] Contains the CC contacts of the email.
	 * @apiParam {Array} [attachments] Contains the attachment data.
	 * @apiParam {String} attachments[].path The path to the attachments (the file must already exist in the server).
	 * @apiParam {String} [attachments[].name] Set a display name for the attachment file (must also contain
	 *           the file extension).
	 *
	 * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete email resource in the
	 * response body.
	 *
	 * @apiError 400-BadRequest Email data were not provided.
	 * @apiErrorExample Error-Response (400)
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Email data were not provided."
	 * }
	 *
	 * @apiError 404-NotFound Email record was not found.
	 * @apiErrorExample Error-Response (404)
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "Email record was not found."
	 * }
	 */
	public function post()
	{
		$emailJsonString = $this->api->request->getBody();

		// Resend existing email without any changes.
		if(isset($this->uri[1]) && is_numeric($this->uri[1]) && empty($emailJsonString))
		{
			$email = $this->emailService->findById(new IdType((int)$this->uri[1]));

			if($email === null)
			{
				throw new HttpApiV2Exception('Email record was not found.', 404);
			}
		}
		// Email json data were provided and they will be used to update the $baseObject.
		else
		{
			if(empty($emailJsonString))
			{
				throw new HttpApiV2Exception('Email data were not provided.', 400);
			}

			$baseObject = null;

			if(isset($this->uri[1]) && is_numeric($this->uri[1]))
			{
				$baseObject = $this->emailService->findById(new IdType((int)$this->uri[1]));

				if($baseObject === null) // base object could not be found
				{
					throw new HttpApiV2Exception('Email record was not found.', 404);
				}

				// Ensure that the email has the correct email id of the request url
				$emailJsonString = $this->_setJsonValue($emailJsonString, 'id', (int)$this->uri[1]);
			}

			$email = $this->emailSerializer->deserialize($emailJsonString, $baseObject);
		}

		// Send the email through the service.
		$this->emailService->send($email);

		// Return response to the client.
		$this->_locateResource('emails', (string)$email->getId());
		$this->_writeResponse($this->emailSerializer->serialize($email, false), 201);
	}


	/**
	 * @api             {put} /emails Queue Email
	 * @apiVersion      2.1.0
	 * @apiName         QueueEmail
	 * @apiGroup        Emails
	 *
	 * @apiDescription
	 * This method will queue a new email so that it can be send later (with the POST method). See
	 * the "post" method for parameter description. To see an example usage take a look at
	 * `docs/REST/samples/email-service/queue_email.php`
	 *
	 * @apiSuccess (Success 200 - Email Was Queued) {String} Response-Body If successful, this
	 * method returns a complete email resource in the response body.
	 *
	 * @apiError 400-BadRequest Email data were not provided.
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 400 Bad Request
	 * {
	 *   "code": 400,
	 *   "status": "error",
	 *   "message": "Email data were not provided."
	 * }
	 */
	public function put()
	{
		$emailJsonString = $this->api->request->getBody();

		if(empty($emailJsonString))
		{
			throw new HttpApiV2Exception('Email data were not provided.', 400);
		}

		// Parse the email.
		$email = $this->emailSerializer->deserialize($emailJsonString);

		// Queue the email to the database.
		$this->emailService->queue($email);

		// Return response to the client.
		$this->_writeResponse($this->emailSerializer->serialize($email, false));
	}


	/**
	 * @todo Do not throw an error if a record does not exist. Because this is what other controllers do.
	 *
	 * @api             {delete} /emails/:id Delete Email
	 * @apiVersion      2.1.0
	 * @apiName         DeleteEmail
	 * @apiGroup        Emails
	 *
	 * @apiDescription
	 * Delete an email record from database. To see an example usage take a look at
	 * `docs/REST/samples/email-service/remove_email.php`.
	 *
	 * @apiSuccess Response-Body If successful this method returns information about the deleted record.
	 *
	 * @apiExample {curl} Delete Email with ID = 572
	 * curl -X DELETE --user admin@shop.de:12345 http://shop.de/api.php/v2/emails/572
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 200,
	 *   "status": "success",
	 *   "action": "delete",
	 *   "emailId": 73
	 * }
	 *
	 * @apiError 400-BadRequest The email ID parameter is missing or is not valid.
	 */
	public function delete()
	{
		if(!isset($this->uri[1]) || !is_numeric($this->uri[1]))
		{
			throw new HttpApiV2Exception('Email record ID was not provided or is not valid in the requested URI.', 400);
		}

		$id    = (int)$this->uri[1];
		$email = $this->emailService->getById(new IdType($id));
		$this->emailService->delete($email);

		// Return response JSON.
		$response = array(
			'code'    => 200,
			'status'  => 'success',
			'action'  => 'delete',
			'emailId' => $id
		);

		$this->_writeResponse($response);
	}


	/**
	 * @api             {get} /emails/:id Get Emails
	 * @apiVersion      2.1.0
	 * @apiName         GetEmails
	 * @apiGroup        Emails
	 *
	 * @apiDescription
	 * Get multiple or a single email record through the GET method. This resource supports
	 * the following GET parameters as described in the first section of documentation: sorting
	 * minimization, search, pagination. Additionally you can filter emails by providing the
	 * GET parameter "state=pending" or "state=sent". These filter parameters do not apply when
	 * a single emails record is selected (e.g. api.php/v2/emails/84) or when the emails are searched
	 * by the "q" parameter. To see an example usage take a look at
	 * `docs/REST/samples/email-service/fetch_email.php`
	 *
	 * @apiExample {curl} Get All Emails
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/emails
	 *
	 * @apiExample {curl} Get Email With ID = 527
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/emails/527
	 *
	 * @apiExample {curl} Get Pending Emails
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/emails?state=pending
	 *
	 * @apiExample {curl} Search Emails
	 *             curl -i --user admin@shop.de:12345 http://shop.de/api.php/v2/emails?q=admin@shop.de
	 *
	 * @apiError 404-NotFound Email record not found.
	 *           
	 * @apiErrorExample Error-Response
	 * HTTP/1.1 404 Not Found
	 * {
	 *   "code": 404,
	 *   "status": "error",
	 *   "message": "Email record could not be found."
	 * }
	 */
	public function get()
	{
		$emails = MainFactory::create('EmailCollection');

		// Get specific email record (email id was provided in the URL).
		if(isset($this->uri[1]) && is_numeric($this->uri[1]))
		{
			$email = $this->emailService->findById(new IdType((int)$this->uri[1]));

			if($email === null)
			{
				throw new HttpApiV2Exception('Email record could not be found.', 404);
			}

			$emails->add($email);
		}
		// Search email records (state filter cannot be applied).
		else
		{
			if($this->api->request->get('q') !== null)
			{
				$emails = $this->emailService->filter($this->api->request->get('q'));
			}
			// Filter results by state ("pending" or "sent").
			else
			{
				if($this->api->request->get('state') !== null)
				{
					$emails = ($this->api->request->get('state')
					           === 'pending') ? $this->emailService->getPending() : $this->emailService->getSent();
				}
				// Get all the records without applying filters.
				else
				{
					$emails = $this->emailService->getAll();
				}
			}
		}

		// Serialize email records to be returned with response.
		$response = array();

		foreach($emails->getArray() as $email)
		{
			$response[] = $this->emailSerializer->serialize($email, false);
		}

		// Apply common response filters.
		$this->_sortResponse($response);
		$this->_paginateResponse($response);
		$this->_minimizeResponse($response);

		// Check if a single resource was requested.
		if(isset($this->uri[1]) && is_numeric($this->uri[1]) && count($response) > 0)
		{
			$response = $response[0];
		}

		$this->_writeResponse($response);
	}
}
