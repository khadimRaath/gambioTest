<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

if (!defined('DS')) define('DS', '/');

if( file_exists(dirname(__FILE__).DS.'dev.php') )
    require_once(dirname(__FILE__).DS.'dev.php');

// core
require_once(dirname(__FILE__).DS.'classes'.DS.'core.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'apis.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'configuration.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'customers.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'orders.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'external_orders.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'items.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'redirect.php');

// models (global / abstract)
require_once(dirname(__FILE__).DS.'classes'.DS.'models/Abstract.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/AbstractExport.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/XmlEmptyObject.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/XmlResultObject.php');

// models (catalog)
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Review.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Product.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Price.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/TierPrice.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Category.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/CategoryPath.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Shipping.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Manufacturer.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Visibility.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Property.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Stock.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Identifier.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Tag.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Relation.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Attribute.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Input.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Validation.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Option.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/AttributeGroup.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/catalog/Attribute.php');

// models (media)
require_once(dirname(__FILE__).DS.'classes'.DS.'models/media/Image.php');
//require_once(dirname(__FILE__).DS.'classes'.DS.'models/media/Attachment.php');

// models (redirect)
require_once(dirname(__FILE__).DS.'classes'.DS.'models/redirect/DeeplinkSuffix.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/redirect/DeeplinkSuffixValue.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/redirect/DeeplinkSuffixValueUnset.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/redirect/DeeplinkSuffixValueDisabled.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/redirect/HtmlTag.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/redirect/HtmlTagAttribute.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'models/redirect/HtmlTagVariable.php');

// helpers
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/DataStructure.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/Pricing.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/String.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/KeywordsManagerInterface.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/KeywordsManager.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/LinkBuilderInterface.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/LinkBuilder.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/MobileRedirectInterface.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/MobileRedirect.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/RedirectorInterface.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/Redirector.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/SettingsManagerInterface.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/SettingsManager.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/TagsGeneratorInterface.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/TagsGenerator.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/TemplateParserInterface.php');
require_once(dirname(__FILE__).DS.'classes'.DS.'helper/redirect/TemplateParser.php');

// vendors
require_once(dirname(__FILE__).DS.'vendors'.DS.'2d_is.php');
include_once(dirname(__FILE__).DS.'vendors'.DS.'JSON.php');
