<?php
/* --------------------------------------------------------------
   ProductQuestionController.inc.php 2016-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/**
 * Class ProductQuestionController
 * 
 * @extends    HttpViewController
 * @category   System
 * @package    HttpViewControllers
 */
class ProductQuestionController extends HttpViewController
{
	/**
	 * This ContentView was converted to the this->tellAFriendContentView functionality swince v3.1.
	 * 
	 * @var TellAFriendContentView
	 */
	protected $tellAFriendContentView;
	
	/**
	 * @var EmailService
	 */
	protected $emailService;
	
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db; 
	
	
	/**
	 * Initialize Controller
	 */
	public function init()
	{
		$this->tellAFriendContentView = MainFactory::create('TellAFriendContentView');
		$this->emailService = StaticGXCoreLoader::getService('Email');
		$this->db = StaticGXCoreLoader::getDatabaseQueryBuilder(); 
	}
	
	
	/**
	 * Display the modal form. 
	 * 
	 * @return JsonHttpControllerResponse
	 */
	public function actionDefault()
	{		
		$this->_setupContentView();
		
		$response = [
			'success' => true,
		    'content' => $this->tellAFriendContentView->get_html()
		];
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Send the question email. 
	 * 
	 * @return JsonHttpControllerResponse
	 */
	public function actionSend()
	{		 
		// Prepare success modal dialog. 
		$this->_setupContentView();
		$this->tellAFriendContentView->setPost($_POST);
		$this->tellAFriendContentView->setName($_POST['name']);
		$this->tellAFriendContentView->setEmail($_POST['email']);
		$this->tellAFriendContentView->setMessage($_POST['message']);
		$this->tellAFriendContentView->setPrivacyAccepted(isset($_POST['privacy_accepted']) ? 1 : 0);
		
		$contentHtml = $this->tellAFriendContentView->get_html();
		$contentArray = $this->tellAFriendContentView->get_content_array();
		
		$response = [
			'success' => !isset($contentArray['ERROR']),
			'content' => $contentHtml
		];
		
		return MainFactory::create('JsonHttpControllerResponse', $response);
	}
	
	
	/**
	 * Prepare the TellAFriendContentView instance.
	 */
	protected function _setupContentView()
	{
		$this->tellAFriendContentView->set_content_template('module/product_question.html');
		$this->tellAFriendContentView->set_flat_assigns(false);
		$this->tellAFriendContentView->setProductsId((int)$_GET['productId']);
		
		$captcha = MainFactory::create_object('Captcha');
		$this->tellAFriendContentView->setCaptchaObject($_SESSION['captcha_object'] = &$captcha);
		
		$this->tellAFriendContentView->setCustomerId((int)$_SESSION['customer_id']);
		$this->tellAFriendContentView->setCustomerFirstName($_SESSION['customer_first_name']);
		$this->tellAFriendContentView->setCustomerLastName($_SESSION['customer_last_name']);
		$this->tellAFriendContentView->setLanguagesId((int)$_SESSION['languages_id']);
	}
}