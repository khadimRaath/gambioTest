<?php
/* --------------------------------------------------------------
   AttachmentsApiV2Controller.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class AttachmentsApiV2Controller
 *
 * This controller enables the API consumers to handle email attachments which can be later
 * used in emails. The most common scenario is that if an email has attachments, they must
 * be already uploaded before the email is sent.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class AttachmentsApiV2Controller extends HttpApiV2Controller
{
	/**
	 * AttachmentsHandler
	 * 
	 * @var AttachmentsHandler
	 */
	protected $attachmentsHandler;


	/**
	 * Initialize API Controller
	 */
	public function __initialize()
	{
		$this->attachmentsHandler = MainFactory::create('AttachmentsHandler', DIR_FS_CATALOG . 'uploads');
	}


	/**
	 * @api        {post} /attachments Upload Attachment
	 * @apiVersion 2.1.0
	 * @apiName    UploadAttachments
	 * @apiGroup   Emails
	 *
	 * @apiDescription
	 * If an email contains an attachment this must be uploaded before the email is sent. This
	 * method accepts the upload of one file at a time. It will return its temporary path which can
	 * be used as the attachment path in the email JSON data. The name of the file form field is not
	 * taken into concern (can be whatever). The important rule is that only one file will be uploaded
	 * at a time.
	 *
	 * @apiExample {curl} Upload Attachment
	 * curl --user admin@shop.de:12345 -F name=test -F filedata=@localfile.jpg http://shop.de/api.php/v2/attachments
	 *
	 * @apiSuccessExample {json} Success-Response
	 * {
	 *   "code": 201,
	 *   "status": "success",
	 *   "action": "upload",
	 *   "path": "/var/www/html/uploads/tmp/myfilename.txt"
	 * }
	 */
	public function post()
	{
		if(!isset($_FILES) || empty($_FILES))
		{
			throw new HttpApiV2Exception('No attachment file was provided.', 400);
		}

		// Get the first item of $_FILES array.
		$file = array_shift($_FILES);
		$tmpAttachmentPath = MainFactory::create('AttachmentPath', $file['tmp_name']);
		$tmpAttachmentName = MainFactory::create('AttachmentName', $file['name']);
		$tmpEmailAttachment = MainFactory::create('EmailAttachment', $tmpAttachmentPath, $tmpAttachmentName);
		$newEmailAttachment = $this->attachmentsHandler->uploadAttachment($tmpEmailAttachment);

		// Return success response to client. 
		$response = array(
				'code'   => 201,
				'status' => 'success',
				'action' => 'upload',
				'path'   => (string)$newEmailAttachment->getPath()
		);

		$this->_writeResponse($response, 201);
	}
}