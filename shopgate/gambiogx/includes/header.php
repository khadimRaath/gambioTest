<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */



if (function_exists("sgIsHomepage") == false) {
    function sgIsHomepage()
    {
        $scriptName = explode('/', $_SERVER['SCRIPT_NAME']);
        $scriptName = end($scriptName);
        
        if ($scriptName != 'index.php') {
            return false;
        }
        
        return true;
    }
}

$shopgateMobileHeader = '';// compatibility to older versions
$shopgateJsHeader     = '';

if (defined('MODULE_PAYMENT_SHOPGATE_STATUS') && MODULE_PAYMENT_SHOPGATE_STATUS === 'True') {
	include_once DIR_FS_CATALOG . 'shopgate/shopgate_library/shopgate.php';
	include_once DIR_FS_CATALOG . 'shopgate/gambiogx/shopgate_config.php';

	try {
		$shopgateCurrentLanguage = isset($_SESSION['language_code']) ? strtolower($_SESSION['language_code']) : 'de';
		$shopgateHeaderConfig    = new ShopgateConfigGambioGx();
		$shopgateHeaderConfig->loadByLanguage($shopgateCurrentLanguage);

		if ($shopgateHeaderConfig->checkUseGlobalFor($shopgateCurrentLanguage)) {
			$shopgateRedirectThisLanguage = in_array($shopgateCurrentLanguage, $shopgateHeaderConfig->getRedirectLanguages());
		} else {
			$shopgateRedirectThisLanguage = true;
		}

		if ($shopgateRedirectThisLanguage) {
			// SEO modules fix (for Commerce:SEO and others): if session variable was set, SEO did a redirect and most likely cut off our GET parameter
			// => reconstruct here, then unset the session variable
			if (!empty($_SESSION['shopgate_redirect'])) {
				$_GET['shopgate_redirect'] = 1;
				unset($_SESSION['shopgate_redirect']);
			}

			// instantiate and set up redirect class
			$shopgateBuilder    = new ShopgateBuilder($shopgateHeaderConfig);
			$shopgateRedirector = $shopgateBuilder->buildMobileRedirect($_SERVER['HTTP_USER_AGENT'], $_GET, $_COOKIE);

			##################
			# redirect logic #
			##################
			//from Version 2.1 $product was changed into $this->coo_product
			$product = (!empty($product)) ? $product : $this->coo_product;
			
			if (($product instanceof product) && $product->isProduct && !empty($product->pID)) {
				$shopgateJsHeader = $shopgateRedirector->buildScriptItem($product->pID);
			} elseif (!empty($current_category_id) || !empty($GLOBALS['current_category_id'])) {
				if (empty($current_category_id) && !empty($GLOBALS['current_category_id'])) {
					// This works for Gambio Version 2.1.x and 2.2.x
					$current_category_id = $GLOBALS['current_category_id'];
				}

				if (is_array($shopgateHeaderConfig->getDisabledRedirectCategoryIds())
					&& in_array($current_category_id, $shopgateHeaderConfig->getDisabledRedirectCategoryIds())) {
					$shopgateJsHeader = '';
					$shopgateRedirector->supressRedirectTechniques(true, true);
				} else {
					$shopgateJsHeader = $shopgateRedirector->buildScriptCategory($current_category_id);
				}
			} elseif(isset($_GET['keywords'])) {
				$shopgateJsHeader = $shopgateRedirector->buildScriptSearch($_GET['keywords']);
			} elseif (isset($_GET['manu'], $_GET['manufacturers_id'])) {
				$manExistResult   =
					xtc_db_query(
						"SELECT manufacturers_name FROM " . TABLE_MANUFACTURERS
						. " WHERE manufacturers_id = {$_GET['manufacturers_id']};"
					);
				$manufacturer     = xtc_db_fetch_array($manExistResult);
				$shopgateJsHeader = $shopgateRedirector->buildScriptBrand($manufacturer['manufacturers_name']);
			} elseif (sgIsHomepage()) {
				$shopgateJsHeader = $shopgateRedirector->buildScriptShop();
			} else {
				$shopgateJsHeader = $shopgateRedirector->buildScriptDefault();
			}
		}
	} catch (ShopgateLibraryException $e) {
	}
}
