<?php
/* --------------------------------------------------------------
   TellAFriendContentView.inc.php 2016-08-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
   
   based on:
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_navigator.php 1292 2005-10-07 16:10:55Z mz $) 

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once (DIR_FS_INC.'xtc_validate_email.inc.php');
require_once (DIR_FS_INC.'xtc_random_charcode.inc.php');
require_once (DIR_FS_CATALOG . 'gm/inc/gm_get_privacy_link.inc.php');
require_once (DIR_FS_INC.'xtc_php_mail.inc.php');

class TellAFriendContentView extends ContentView
{
	protected $productsId;
	protected $languagesId;
	protected $customerId;
	protected $captchaObject;
	protected $customerFirstName;
	protected $customerLastName;
	
	protected $post;
	protected $name;
	protected $email;
	protected $message;

	protected $productName;
	
	protected $privacyAccepted = 0;
	
	// ######### CONSTRUCTOR #########
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/gm_tell_a_friend.html');
		$this->set_flat_assigns(true);
	}
	
	// ######### GETTER AND SETTER #########

	/**
	 * @return integer Products ID
	 */
	public function getProductsId()
	{
		return $this->productsId;
	}
	
	/**
	 * @param integer $p_productsId Products ID
	 */
	public function setProductsId($p_productsId)
	{
		$this->productsId = (int)$p_productsId;
	}
	
	/**
	 * @return Captcha Captcha Object
	 */
	public function getCaptchaObject()
	{
		return $this->captchaObject;
	}

	/**
	 * @param Captcha $captchaObject Captcha Object
	 */
	public function setCaptchaObject(Captcha $captchaObject)
	{
		$this->captchaObject = $captchaObject;
	}
	
	/**
	 * @return string Customer First Name
	 */
	public function getCustomerFirstName()
	{
		return $this->customerFirstName;
	}
	
	/**
	 * @param string $p_customerFirstName Customer First Name
	 */
	public function setCustomerFirstName($p_customerFirstName)
	{
		$this->customerFirstName = (string)$p_customerFirstName;
	}
	
	/**
	 * @return integer Customer ID
	 */
	public function getCustomerId()
	{
		return $this->customerId;
	}

	/**
	 * @param integer $p_customerId Customer ID
	 */
	public function setCustomerId($p_customerId)
	{
		$this->customerId = (int)$p_customerId;
	}
	
	/**
	 * @return string Customer Last Name
	 */
	public function getCustomerLastName()
	{
		return $this->customerLastName;
	}
	
	/**
	 * @param string $p_customerLastName Customer Last Name
	 */
	public function setCustomerLastName($p_customerLastName)
	{
		$this->customerLastName = (string)$p_customerLastName;
	}

	/**
	 * @return integer Languages ID
	 */
	public function getLanguagesId()
	{
		return $this->languagesId;
	}
	
	/**
	 * @param integer $p_languagesId Languages ID
	 */
	public function setLanguagesId($p_languagesId)
	{
		$this->languagesId = (int)$p_languagesId;
	}
	
	
	/**
	 * @return array $post
	 */
	public function getPost()
	{
		return $this->post;
	}

	/**
	 * @param array $p_post
	 */
	public function setPost(array $p_post)
	{
		$this->post = $p_post;
	}
	
	/**
	 * @return string POST of the E-Mail Address
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
	/**
	 * @param string $p_email POST of the E-Mail Address
	 */
	public function setEmail($p_email)
	{
		$this->email = (string)$p_email;
	}
	
	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * @param string $p_message POST of the Message
	 */
	public function setMessage($p_message)
	{
		$this->message = (string)$p_message;
	}
	
	/**
	 * @return string POST of the Name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * @param string $p_name POST of the Name
	 */
	public function setName($p_name)
	{
		$this->name = (string)$p_name;
	}
	
	
	/**
	 * @param string $p_privacyAccepted POST of privacy checkbox
	 */
	public function setPrivacyAccepted($p_privacyAccepted)
	{
		$this->privacyAccepted = (int)$p_privacyAccepted;
	}
	
	
	// ######### PUBLIC FUNCTIONS #########

	/**
	 * 
	 */
	public function prepare_data()
	{
		$getProduct = xtc_db_query($this->_getProductsNameSql());
		if(xtc_db_num_rows($getProduct) == 1)
		{
			$product = xtc_db_fetch_array($getProduct);
			$this->productName = $product['products_name'];
			$formDataArray = $this->_getFormData();
			$this->_prepareEmailForm($product);

			if(!empty($this->post))
			{
				$validate = $this->_validateInput($product);
				if($validate === false)
				{
					$this->captchaObject->reload_vv_code();
				}
				else
				{
					$formDataArray['email'] = $this->email;
					$this->_prepareMail($formDataArray['email'], $formDataArray['sender'], $validate);
				}
			}
		}
	}

	// ######### PROTECTED/PRIVATE METHODS #########

	/**
	 * @return string $sql
	 */
	protected function _getProductsNameSql()
	{
		$sql = "SELECT 
					products_name
				FROM 
					products_description
				WHERE
					products_id = '" . (int)$this->productsId . "'
					AND language_id = '" . (int)$this->languagesId . "'";

		return $sql;
	}
	
	/**
	 * @return string $sql
	 */
	protected function _getCustomerEmailSql()
	{
		$sql = "SELECT 
					customers_email_address
				FROM 
					customers
				WHERE 
					customers_id = '" . (int)$this->customerId . "'";

		return $sql;
	}
	
	/**
	 * @param string $product
	 */
	protected function _prepareEmailForm($product)
	{	
		$this->set_content_data('PRODUCTS_ID', $this->productsId);
		$this->set_content_data('PRODUCTS_NAME', $product['products_name']);
		$this->set_content_data('SEND', xtc_image_button('button_send.gif', GM_TELL_A_FRIEND_SEND, 'id="gm_send_tell_a_friend" class="cursor_pointer" onclick="var tell_a_friend = new GMTellAFriend(); tell_a_friend.send_form();"'));
		
		$this->set_content_data('IMG', 'templates/' . CURRENT_TEMPLATE . '/icons/anmerkungen.gif');
		$this->set_content_data('TELL_A_FRIEND_TITLE', GM_TELL_A_FRIEND_TITLE);
		$this->set_content_data('NAME', GM_TELL_A_FRIEND_SENDER);
		$this->set_content_data('EMAIL', GM_TELL_A_FRIEND_EMAIL);
		$this->set_content_data('MESSAGE', GM_TELL_A_FRIEND_MESSAGE);
		$this->set_content_data('INPUT_MESSAGE', GM_TELL_A_FRIEND_MESSAGE_INPUT);

		$this->set_content_data('VALIDATION', GM_TELL_A_FRIEND_VALIDATION);
		$this->set_content_data('VALIDATION_ACTIVE', gm_get_conf('GM_TELL_A_FRIEND_VVCODE'));
		$this->set_content_data('GM_CAPTCHA', $this->captchaObject->get_html());

		$this->set_content_data('GM_PRIVACY_LINK', gm_get_privacy_link('GM_CHECK_PRIVACY_TELL_A_FRIEND'));
		
		$this->set_content_data('show_privacy_checkbox', gm_get_conf('PRIVACY_CHECKBOX_ASK_PRODUCT_QUESTION'));
		$this->set_content_data('privacy_accepted', $this->privacyAccepted);
	}

	/**
	 * @return string $sender
	 */
	protected function _getSenderAddress()
	{
		if(!empty($this->name))
		{
			$sender = $this->name;
		}
		else
		{
			$sender = STORE_NAME;
		}

		return $sender;
	}
	
	/**
	 * @return array $formDataArray
	 */
	protected function _getFormData()
	{
		$formDataArray = array();
		if(!empty($this->customerId))
		{
			$getCustomersMail = xtc_db_query($this->_getCustomerEmailSql());
			if(xtc_db_num_rows($getCustomersMail) == 1)
			{
				$customer = xtc_db_fetch_array($getCustomersMail);

				$formDataArray['email'] = $customer['customers_email_address'];
				$formDataArray['sender'] = $this->_getSenderAddress();
				$this->set_content_data('INPUT_NAME', $this->customerFirstName . ' ' . $this->customerLastName);
				$this->set_content_data('OWN_MAIL', $formDataArray['email']);
			}
		}
		else
		{
			$formDataArray['email'] = STORE_OWNER_EMAIL_ADDRESS;
			$formDataArray['sender'] = $this->_getSenderAddress();
		}
		
		return $formDataArray;
	}
	
	/**
	 * @param string $product
	 *
	 * @return mixed|string|bool $productLink
	 */
	protected function _validateInput($product)
	{
		$seoBoost = MainFactory::create_object('GMSEOBoost');
		$captchaIsValid = $this->captchaObject->is_valid($this->post, 'GM_TELL_A_FRIEND_VVCODE');
		$error = '';
		
		if(gm_get_conf('GM_CHECK_PRIVACY_TELL_A_FRIEND') === '1'
		   && gm_get_conf('PRIVACY_CHECKBOX_ASK_PRODUCT_QUESTION') === '1'
		   && $this->privacyAccepted !== 1
		)
		{
			$error = ENTRY_PRIVACY_ERROR . ' ';
			$this->set_content_data('ERROR', ENTRY_PRIVACY_ERROR);
		}
		
		if (!$captchaIsValid || empty($this->email) || $error !== '')
		{
			if(empty($this->email))
			{
				$error .= GM_TELL_A_FRIEND_ERROR;
			}
			
			$this->set_content_data('ERROR', $error);

			if (!$captchaIsValid)
			{
				$this->set_content_data('VVCODE_ERROR', GM_TELL_A_FRIEND_WRONG_CODE);
			}
			
			return false;
		}
		else
		{
			if($seoBoost->boost_products)
			{
				$productLink = xtc_href_link($seoBoost->get_boosted_product_url($this->productsId, $product['products_name']));
			}
			else
			{
				$productLink = xtc_href_link(FILENAME_PRODUCT_INFO, xtc_product_link($this->productsId, $product['products_name']));
			}
			
			return $productLink;
		}
	}

	/**
	 * @param string $email
	 * @param string $sender
	 * @param string $productLink
	 */
	protected function _prepareMail($email, $sender, $productLink)
	{
		$message = gm_prepare_string($this->message);
		$message = str_replace('%u20AC', 'EUR', $message);
		$text = $sender . GM_TELL_A_FRIEND_RECOMMENDS_1.GM_TELL_A_FRIEND_RECOMMENDS_2
				. "\n\n". GM_TELL_A_FRIEND_SUBJECT_2 . $productLink
		        . "\n\n". GM_TELL_A_FRIEND_EMAIL .': '.$this->email
				. "\n\n" . GM_TELL_A_FRIEND_MESSAGE .': '. $message;

		$text_html = htmlentities_wrapper($sender). ' '. htmlentities_wrapper(GM_TELL_A_FRIEND_RECOMMENDS_1).htmlentities_wrapper(GM_TELL_A_FRIEND_RECOMMENDS_2)
					 . '<br><br>'.GM_TELL_A_FRIEND_SUBJECT_2 .'<a href="' . $productLink . '" target="_blank">' . htmlentities_wrapper($productLink) . '</a>'
		             . '<br><br>'. GM_TELL_A_FRIEND_EMAIL .': '.$this->email
		             . '<br><br>'.htmlentities_wrapper(GM_TELL_A_FRIEND_MESSAGE) . ': ' . htmlentities_wrapper($message);
		
		xtc_php_mail($email, $sender, STORE_OWNER_EMAIL_ADDRESS, $email, '', $email, $sender, '', '', GM_TELL_A_FRIEND_SUBJECT_1 . '"'. $this->productName . '"', $text_html, html_entity_decode_wrapper($text));
		$this->set_content_data('MAIL_OUT', GM_TELL_A_FRIEND_MAIL_OUT);
	}
}
