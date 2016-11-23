<?php

/* --------------------------------------------------------------
   PayPalDeprecatedCheck.php 16-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PayPalDeprecatedCheck
{
	/**
	 * Name of deprecated pp module file.
	 * @var string
	 */
	protected static $moduleDeprecatedName = 'paypalng.php';

	/**
	 * User configuration key.
	 * @var string
	 */
	protected static $userConfigurationKey = 'nextPaypalDeprecatedMessage';

	/**
	 * Query builder to perform database operation.
	 * @var CI_DB_query_builder
	 */
	protected static $db;

	/**
	 * User configuration service.
	 * @var UserConfigurationService
	 */
	protected static $userConfigurationService;


	/**
	 * Checks if the deprecated pay pal module is installed.
	 * When the deprecated module is installed and the message was disabled longer than one week ago,
	 * a message is append to the message stack.
	 *
	 * @param messageStack $messageStack
	 */
	public static function ppDeprecatedCheck(messageStack $messageStack)
	{
		// Customer ID.
		$customerId = (int)$_SESSION['customer_id'];

		// Check expiration of message output deactivation period.
		if(self::_isPaypalDeprecated())
		{
			// Message output deactivation expiration timestamp value.
			$expirationTime = (int)self::_getService()
			                           ->getUserConfiguration(new IdType($customerId), self::$userConfigurationKey);

			// Current timestamp value.
			$currentTime = time();

			// Add notice to message stack if expiration time has been exceeded.
			if($expirationTime < $currentTime)
			{
				$content     = self::_messageIcon() . TEXT_PAYPAL_MODULE_DEPRECATED
				               . self::_btnToNewPayPal();
				$class       = 'error';
				$configValue = strtotime('+1 week');
				$messageStack->add($content, $class, self::$userConfigurationKey, $configValue);
			}
		}
	}


	/**
	 * Returns a string containing an exclamation icon element.
	 * @return string
	 */
	protected static function _messageIcon()
	{
		return '<span class="fa fa-exclamation-circle" style="position: relative; font-size: 1.6em; top: 9px;"></span> ';
	}


	/**
	 * Returns a string containing a link element to the new paypal module.
	 * @return string
	 */
	protected static function _btnToNewPayPal()
	{
		return '<br/><br/><a class="btn btn-danger" style="margin: 0 0 0 12px" href="' . xtc_href_link(FILENAME_MODULES,
		                                                                                               'set=payment&module=paypal3')
		       . '">' . TEXT_PAYPAL_MODULE_ACTIVATE . '</a>';
	}


	/**
	 * Checks if the deprecated pay pal module is currently installed.
	 * @return bool Whether true if pay pal ng is installed or false.
	 */
	protected static function _isPaypalDeprecated()
	{
		$paymentConfigurationArray = self::_getDatabaseQueryBuilder()
		                                 ->get_where('configuration',
		                                             array('configuration_key' => 'MODULE_PAYMENT_INSTALLED'))
		                                 ->row_array();
		$installedPaymentsString   = $paymentConfigurationArray['configuration_value'];

		$installedPaymentsArray = explode(';', $installedPaymentsString);

		return in_array(self::$moduleDeprecatedName, $installedPaymentsArray);
	}


	/**
	 * Gets the database query builder instance from the static gx core loader.
	 * @return CI_DB_query_builder
	 */
	protected static function _getDatabaseQueryBuilder()
	{
		if(null === self::$db)
		{
			self::$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		}

		return self::$db;
	}


	/**
	 * Assigns the user configuration service as property and returns it.
	 * @return UserConfigurationService
	 */
	protected static function _getService()
	{
		if(null === self::$userConfigurationService)
		{
			self::$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
		}

		return self::$userConfigurationService;
	}


	/**
	 * Deactivates the alert box output by setting the new message output deactivation expiration timestamp to a value
	 * far, far, far in the future. Realized in the context of providing an URL as 'hack' to deactivate the alert box
	 * permanently.
	 */
	public static function deactivateOutputPermanently()
	{
		// Customer ID.
		$customerId = new IdType((int)$_SESSION['customer_id']);

		// New message output deactivation expiration timestamp value to set.
		$newExpirationTime = strtotime('+400 years');

		// Set value.
		self::_getService()->setUserConfiguration($customerId, self::$userConfigurationKey, $newExpirationTime);
	}
}
