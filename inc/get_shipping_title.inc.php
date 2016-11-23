<?php
/* --------------------------------------------------------------
   get_shipping_title.inc.php 2015-07-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @param string $p_methodName name of the shipping class
 * @param string $p_languageDirectoryName
 * @param bool   $p_stripTags remove HTML-tags like images
 *
 * @throws InvalidArgumentException if $p_methodName is not a string or is empty
 * @throws InvalidArgumentException if $p_languageDirectoryName is not a string
 *
 * @return string name of the shipping method
 */
function get_shipping_title($p_methodName, $p_languageDirectoryName = '', $p_stripTags = true)
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
	$languageTextManager->init_from_lang_file('lang/' . basename($languageDirectoryName) . '/modules/shipping/'
	                                          . basename($methodName) . '.php');

	$shippingTitle      = $methodName;
	$titleConstantName = 'MODULE_SHIPPING_' . strtoupper($methodName) . '_TEXT_TITLE';

	if(defined($titleConstantName))
	{
		$shippingTitle = constant($titleConstantName);
	}
	elseif(file_exists(DIR_FS_CATALOG . 'includes/modules/shipping/' . basename($methodName) . '.php'))
	{
		include_once DIR_FS_CATALOG . 'includes/modules/shipping/' . basename($methodName) . '.php';

		if(defined($titleConstantName))
		{
			$shippingTitle = constant($titleConstantName);
		}
		elseif(class_exists($methodName))
		{
			$shipping = MainFactory::create($methodName);

			if(isset($shipping->title) && trim($shipping->title) !== '')
			{
				$shippingTitle = trim($shipping->title);
			}
		}
	}

	if($p_stripTags)
	{
		$shippingTitle = trim(strip_tags($shippingTitle));
		
		if($shippingTitle === '')
		{
			$shippingTitle = $methodName;
		}
	}

	return $shippingTitle;
}