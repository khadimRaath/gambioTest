<?php

/* --------------------------------------------------------------
   AdminInfoBoxAjaxController.php 2016-04-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminInfoBoxAjaxController
 *
 * This class handles incoming ajax requests for the admin info box.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class AdminInfoBoxAjaxController extends AdminHttpViewController
{
	/**
	 * Customer ID.
	 * @var int
	 */
	protected $customerId;

	/**
	 * Language code.
	 * @var LanguageCode
	 */
	protected $languageCode;

	/**
	 * Language ID.
	 * @var int
	 */
	protected $languageId;

	/**
	 * Language text manager.
	 * @var LanguageTextManager
	 */
	protected $languageTextManager;

	/**
	 * Formatting pattern for date time values.
	 * @var string
	 */
	protected $dateTimeFormat = 'Y-m-d H:i:s';

	/**
	 * Admin Info Box Service.
	 * @var InfoBoxService
	 */
	protected $service;


	/**
	 * Initializes the controller.
	 */
	public function init()
	{
		$languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());

		$this->customerId          = $_SESSION['customer_id'];
		$this->service             = StaticGXCoreLoader::getService('InfoBox');
		$this->languageId          = (int)$_SESSION['languages_id'];
		$this->languageTextManager = MainFactory::create('LanguageTextManager', 'messages', $this->languageId);
		$this->languageCode        = $languageProvider->getCodeById(new IdType((int)$this->languageId));
	}


	/**
	 * Checks if the customer is the admin.
	 *
	 * @return bool Is the customer the admin?
	 */
	protected function _isAdmin()
	{
		try
		{
			$this->validateCurrentAdminStatus();

			return true;
		}
		catch(LogicException $exception)
		{
			return false;
		}
	}


	/**
	 * Callback method for the default action.
	 * @return HttpControllerResponse
	 */
	public function actionDefault()
	{
		return MainFactory::create('HttpControllerResponse', array());
	}


	/**
	 * Returns all messages.
	 * @throws AuthenticationException If the customer has no admin privileges.
	 * @return JsonHttpControllerResponse
	 */
	public function actionGetAllMessages()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		$collection = $this->service->getAllMessages()->getArray();

		$messages = array();

		/**
		 * @var InfoBoxMessage $item
		 */
		foreach($collection as $item)
		{
			$message = array(
				'id'               => $item->getId(),
				'source'           => $item->getSource(),
				'identifier'       => $item->getIdentifier(),
				'status'           => $item->getStatus(),
				'type'             => $item->getType(),
				'visibility'       => $item->getVisibility(),
				'buttonLink'       => $item->getButtonLink(),
				'customerId'       => $item->getCustomerId(),
				'addedDateTime'    => $item->getAddedDateTime()->format($this->dateTimeFormat),
				'modifiedDateTime' => $item->getModifiedDateTime()->format($this->dateTimeFormat),
				'headline'         => $item->getHeadLine($this->languageCode),
				'message'          => $item->getMessage($this->languageCode),
				'buttonLabel'      => $item->getButtonLabel($this->languageCode)
			);

			$messages[] = $message;
		}

		return new JsonHttpControllerResponse($messages);
	}


	/**
	 * Adds a new message.
	 * @throws AuthenticationException If the customer has no admin privileges.
	 * @return HttpControllerResponse
	 */
	public function actionAddMessage()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		$message = MainFactory::create('InfoBoxMessage');

		try
		{
			$message->setSource(new StringType($this->_getQueryParameter('source')))
			        ->setIdentifier(new StringType($this->_getQueryParameter('identifier')))
			        ->setStatus(new StringType($this->_getQueryParameter('status')))
			        ->setType(new StringType($this->_getQueryParameter('type')))
			        ->setVisibility(new StringType($this->_getQueryParameter('visibility')))
			        ->setButtonLink(new StringType($this->_getQueryParameter('buttonLink')))
			        ->setCustomerId(new IdType((int)$this->_getQueryParameter('customerId')))
			        ->setMessage(new StringType($this->_getQueryParameter('message')), $this->languageCode)
			        ->setHeadLine(new StringType($this->_getQueryParameter('headline')), $this->languageCode)
			        ->setButtonLabel(new StringType($this->_getQueryParameter('buttonLabel')), $this->languageCode);

			$this->service->addMessage($message);

			return MainFactory::create('HttpControllerResponse', 'success');
		}
		catch(Exception $exception)
		{
			return MainFactory::create('HttpControllerResponse', 'error');
		}
	}


	/**
	 * Adds a new success message.
	 * @throws AuthenticationException If the customer has no admin privileges.
	 * @return HttpControllerResponse
	 */
	public function actionAddSuccessMessage()
	{
		$messageSource      = 'adminAction';
		$messageIdentifier  = uniqid('adminActionSuccess-', true);
		$messageStatus      = 'new';
		$messageType        = 'success';
		$messageVisibility  = 'removable';
		$messageButtonLink  = '';
		$messageText        = $this->_getQueryParameter('message') ? : $this->languageTextManager->get_text('GM_LANGUAGE_CONFIGURATION_SUCCESS',
		                                                                                                    'languages');
		$messageHeadLine    = $this->languageTextManager->get_text('success');
		$messageButtonLabel = '';

		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		$message = MainFactory::create('InfoBoxMessage');

		try
		{
			$message->setSource(new StringType($messageSource))
			        ->setIdentifier(new StringType($messageIdentifier))
			        ->setStatus(new StringType($messageStatus))
			        ->setType(new StringType($messageType))
			        ->setVisibility(new StringType($messageVisibility))
			        ->setButtonLink(new StringType($messageButtonLink))
			        ->setCustomerId(new IdType((int)$this->customerId))
			        ->setMessage(new StringType($messageText), $this->languageCode)
			        ->setHeadLine(new StringType($messageHeadLine), $this->languageCode)
			        ->setButtonLabel(new StringType($messageButtonLabel), $this->languageCode);

			$this->service->addMessage($message);

			return MainFactory::create('HttpControllerResponse', 'success');
		}
		catch(Exception $exception)
		{
			return MainFactory::create('HttpControllerResponse', 'error');
		}
	}


	/**
	 * Reactivates the messages.
	 * @throws AuthenticationException If the customer has no admin privileges.
	 * @return HttpControllerResponse
	 */
	public function actionReactivateMessages()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		try
		{
			$this->service->reactivateMessages();

			return MainFactory::create('HttpControllerResponse', 'success');
		}
		catch(Exception $exception)
		{
			return MainFactory::create('HttpControllerResponse', 'error');
		}
	}


	/**
	 * Deletes messages by their sources.
	 * @throws AuthenticationException If the customer has no admin privileges.
	 * @return HttpControllerResponse
	 */
	public function actionDeleteBySource()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		try
		{
			$source = new StringType($this->_getQueryParameter('source'));
			$this->service->deleteMessageBySource($source);

			return MainFactory::create('HttpControllerResponse', 'success');
		}
		catch(Exception $exception)
		{
			return MainFactory::create('HttpControllerResponse', 'error');
		}
	}


	/**
	 * Deletes messages by their identifiers.
	 * @throws AuthenticationException If the customer has no admin privileges.
	 * @return HttpControllerResponse
	 */
	public function actionDeleteByIdentifier()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		try
		{
			$identifier = new StringType($this->_getQueryParameter('identifier'));
			$this->service->deleteMessageByIdentifier($identifier);

			return MainFactory::create('HttpControllerResponse', 'success');
		}
		catch(Exception $exception)
		{
			return MainFactory::create('HttpControllerResponse', 'error');
		}
	}


	/**
	 * Delete a message by its ID.
	 * @throws AuthenticationException If the customer has no admin privileges.
	 * @return HttpControllerResponse
	 */
	public function actionDeleteById()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		try
		{
			$id = new IdType($this->_getQueryParameter('id'));
			$this->service->deleteMessageById($id);

			return MainFactory::create('HttpControllerResponse', 'success');
		}
		catch(Exception $exception)
		{
			return MainFactory::create('HttpControllerResponse', 'error');
		}
	}


	/**
	 * Sets a message status.
	 * @throws AuthenticationException If the customer has no admin privileges.
	 * @return HttpControllerResponse
	 */
	public function actionSetMessageStatus()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		try
		{
			$id     = new IdType($this->_getQueryParameter('id'));
			$status = new StringType($this->_getQueryParameter('status'));

			$this->service->setMessageStatus($id, $status);

			return MainFactory::create('HttpControllerResponse', 'success');
		}
		catch(Exception $exception)
		{
			return MainFactory::create('HttpControllerResponse', 'error');
		}
	}
}