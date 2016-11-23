<?php
/* --------------------------------------------------------------
  message_stack.php 2015-08-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
  --------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(message_stack.php,v 1.5 2002/11/22); www.oscommerce.com
  (c) 2003	 nextcommerce (message_stack.php,v 1.6 2003/08/18); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: message_stack.php 950 2005-05-14 16:45:21Z mz $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Example usage:

  $messageStack = new messageStack();
  $messageStack->add('Error: Error 1', 'error');
  $messageStack->add('Error: Error 2', 'warning');
  if ($messageStack->size > 0) echo $messageStack->output();
  --------------------------------------------------------------------------------------- */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class messageStack_ORIGIN
{
	protected $messages = array('danger' => array(), 'warning' => array(), 'success' => array(), 'info' => array());
	protected $additionalClasses = array();
	public $size = 0;

	public function __construct()
	{
		if(isset($_SESSION['messageToStack']))
		{
			for($i = 0, $n = count($_SESSION['messageToStack']); $i < $n; $i++)
			{
				$this->add($_SESSION['messageToStack'][$i]['text'], $_SESSION['messageToStack'][$i]['type']);
			}
			
			unset($_SESSION['messageToStack']);
		}
	}

	public function add($p_message, $p_type = 'error', $userConfigKey = '', $userConfigValue = '')
	{
		switch ($p_type) {
			case 'error':
				$type = 'danger';
				break;
			case 'warning':
				$type = 'warning';
				break;
			case 'success':
				$type = 'success';
				break;
			default:
				$type = 'info';
				break;
		}

		$this->messages[$type][] = array(
			'message' => (string)$p_message,
			'user_config_key' => (string)$userConfigKey,
			'user_config_value' => (string)$userConfigValue
		);
		$this->size++;
	}

	public function add_session($p_message, $p_type = 'error')
	{
		if (!isset($_SESSION['messageToStack'])) {
			$_SESSION['messageToStack'] = array();
		}

		$_SESSION['messageToStack'][] = array('text' => $p_message, 'type' => $p_type);
	}
	
	public function add_additional_class($additional_class)
	{
		$this->additionalClasses[] = $additional_class;
	}

	public function reset()
	{
		$this->messages = array('danger' => array(), 'warning' => array(), 'success' => array(), 'info' => array());
		$this->size = 0;
	}

	public function output()
	{
		foreach($this->messages as $type => $messages)
		{
			foreach($messages as $message)
			{
				$userConfig = '';
				if(trim($message['user_config_key']) !== '' && trim($message['user_config_value']) !== '')
				{
					$userConfig = 'data-close_alert_box-user_config_key="'
						. $message['user_config_key']
						. '" data-close_alert_box-user_config_value="'
						. $message['user_config_value']
						. '" data-close_alert_box-user_id="'
						. (int)$_SESSION['customer_id'] . '"';
				}

				echo '<div class="alert alert-' . $type . ' ' . implode(' ', $this->additionalClasses)
				     . '" data-gx-compatibility="close_alert_box" ' . $userConfig . '>
							<button type="button" class="close" data-dismiss="alert">×</button>' . $message['message'] . '
						</div>';
			}
		}
	}
	
	
	public function get_messages()
	{
		return $this->messages; 
	}
}

MainFactory::load_origin_class('messageStack');
