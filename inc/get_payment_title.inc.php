<?php
/* --------------------------------------------------------------
   get_payment_title.inc.php 2015-07-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @param string $p_methodName name of the payment class
 * @param string $p_languageDirectoryName
 * @param bool   $p_stripTags remove HTML-tags like images
 *
 * @throws InvalidArgumentException if $p_methodName is not a string or is empty
 * @throws InvalidArgumentException if $p_languageDirectoryName is not a string
 *
 * @return string name of the payment method
 */
function get_payment_title($p_methodName, $p_languageDirectoryName = '', $p_stripTags = true)
{
	if(!is_string($p_methodName) || trim($p_methodName) === '')
	{
		throw new InvalidArgumentException('$p_methodName (' . gettype($p_methodName)
		                                   . ') is not a string or is empty');
	}

	if(!is_string($p_languageDirectoryName))
	{
		throw new InvalidArgumentException('$p_languageDirectoryName (' . gettype($p_languageDirectoryName)
		                                   . ') is not a string');
	}

	$methodName = trim($p_methodName);

	$languageDirectoryName = basename(trim($p_languageDirectoryName));
	if($languageDirectoryName === '')
	{
		$languageDirectoryName = $_SESSION['language'];
	}

	/* @var LanguageTextManager $languageTextManager */
	$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
	$languageTextManager->init_from_lang_file('lang/' . basename($languageDirectoryName) . '/modules/payment/'
	                                          . basename($methodName) . '.php');

	$paymentTitle      = $methodName;
	$titleConstantName = 'MODULE_PAYMENT_' . strtoupper($methodName) . '_TEXT_TITLE';

	if(defined($titleConstantName))
	{
		$paymentTitle = constant($titleConstantName);
	}
	elseif(file_exists(DIR_FS_CATALOG . 'includes/modules/payment/' . basename($methodName) . '.php'))
	{
		include_once DIR_FS_CATALOG . 'includes/modules/payment/' . basename($methodName) . '.php';

		if(defined($titleConstantName))
		{
			$paymentTitle = constant($titleConstantName);
		}
		elseif(class_exists($methodName))
		{
			$payment = MainFactory::create($methodName);

			if(isset($payment->title) && trim($payment->title) !== '')
			{
				$paymentTitle = trim($payment->title);
			}
		}
	}

	if($p_stripTags)
	{
		$paymentTitle = trim(strip_tags($paymentTitle));
		
		if($paymentTitle === '')
		{
			$paymentTitle = $methodName;
		}
	}

	return $paymentTitle;
}