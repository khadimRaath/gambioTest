<?php
/* --------------------------------------------------------------
   ProductInfoContentView.inc.php 2016-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com
   (c) 2003      nextcommerce (product_info.php,v 1.46 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: product_info.php 1320 2005-10-25 14:21:11Z matthias $)


   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
   New Attribute Manager v4b                            Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Cross-Sell (X-Sell) Admin 1                          Autor: Joshua Dechant (dreamscape)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include needed functions
require_once(DIR_FS_INC . 'xtc_get_download.inc.php');
require_once(DIR_FS_INC . 'xtc_delete_file.inc.php');
require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');
require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_hidden_field.inc.php');
require_once(DIR_FS_INC . 'xtc_image_button.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_form.inc.php');
require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
require_once(DIR_FS_INC . 'xtc_image_submit.inc.php');

require_once(DIR_FS_INC . 'xtc_check_categories_status.inc.php');
require_once(DIR_FS_INC . 'xtc_get_products_mo_images.inc.php');
require_once(DIR_FS_INC . 'xtc_get_vpe_name.inc.php');
require_once(DIR_FS_INC . 'get_cross_sell_name.inc.php');

require_once(DIR_FS_INC . 'xtc_get_products_stock.inc.php');

require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');

/**
 * Class ProductInfoContentView
 */
class ProductInfoContentView extends ContentView
{
	protected $getArray  = array(); // $_GET
	protected $postArray = array(); // $_POST

	protected $cheapestCombiArray   = array();
	protected $combiId              = 0;
	protected $currency             = '';
	protected $currentCategoryId    = 0;
	protected $currentCombiArray    = array();
	protected $customerDiscount     = 0.0;
	protected $fsk18DisplayAllowed  = true;
	protected $fsk18PurchaseAllowed = true;
	protected $customerStatusId     = -1;
	protected $hasProperties        = false;
	protected $languageId           = 0;
	protected $language             = '';
	protected $lastListingSql       = '';
	protected $main;
	protected $product;
	protected $productPriceArray    = array();
	protected $showGraduatedPrices  = false;
	protected $showPrice            = true;
	protected $xtcPrice;
	protected $maxImageHeight = 0;
	protected $additionalFields		= array();


	function __construct($p_template = 'default')
	{
		parent::__construct();
		$filepath = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_info/';

		// get default template
		$c_template = $this->get_default_template($filepath, $p_template);

		$this->set_content_template('module/product_info/' . $c_template);
		$this->set_flat_assigns(true);
	}


	function prepare_data()
	{
		if($this->product instanceof product && $this->product->isProduct())
		{
			$this->_setPriceData();

			$this->_assignProductData();

			$this->_assignProductNavigator();
			$this->_assignFormTagData();
			$this->_assignWidgets();
			$this->_assignMedia();
			$this->_assignReviews();
			$this->_assignProductLists();
			$this->_assignRichSnippetData();

			// TODO move to control
			$this->_updateProductViewsStatistic();
			$this->_updateTracking();
		}
	}


	public function get_html()
	{
		if(($this->product instanceof product) === false || !$this->product->isProduct())
		{
			// product not found in database
			$error = TEXT_PRODUCT_NOT_FOUND;

			/* @var ErrorMessageContentView $coo_error_message */
			$errorView = MainFactory::create_object('ErrorMessageContentView');
			$errorView->set_error($error);
			$htmlOutput = $errorView->get_html();
		}
		else
		{
			$this->prepare_data();
			$htmlOutput = $this->build_html();
		}

		return $htmlOutput;
	}


	protected function _assignProductData()
	{
		// assign properties and set $this->hasProperties flag
		$this->_setPropertiesData();

		$this->_assignAttributes();
		$this->_assignDate();
		$this->_assignDeactivatedButtonFlag();
		$this->_assignGPrint();
		$this->_assignDescription();
		$this->_assignDiscount();
		$this->_assignEan();
		$this->_assignGraduatedPrices();
		$this->_assignId();
		$this->_assignImageData();
		$this->_assignImageMaxHeight();
		$this->_assignLegalAgeFlag();
		$this->_assignModelNumber();
		$this->_assignName();
		$this->_assignNumberOfOrders();
		$this->_assignPrice();
		$this->_assignProductUrl();
		$this->_assignQuantity();
		$this->_assignShippingTime();
		$this->_assignStatus();
		$this->_assignVpe();
		$this->_assignWeight();
		$this->_assignAdditionalFields();

		if($this->_showPrice())
		{
			$this->_assignShippingLink();
			$this->_assignTaxInfo();
		}

		if($this->_productIsForSale())
		{
			$this->_assignInputFieldQuantity();
		}
		else
		{
			$this->_assignDeprecatedIdHiddenField();
		}

		$this->set_content_data('showManufacturerImages', gm_get_conf('SHOW_MANUFACTURER_IMAGE_PRODUCT_DETAILS'));
		$this->set_content_data('showProductRibbons', gm_get_conf('SHOW_PRODUCT_RIBBONS'));

		$showRating = false;
		if(gm_get_conf('ENABLE_RATING') === 'true')
		{
			$showRating = true;
		}
		$this->content_array['showRating'] = $showRating;
	}


	protected function _assignProductLists()
	{
		$this->_assignAlsoPurchased();
		$this->_assignCrossSelling();
		$this->_assignReverseCrossSelling();
		$this->_assignYoochoose();
	}


	protected function _assignWidgets()
	{
		$this->_assignKlarna();
		$this->_assignWishlist();

		$this->_assignSocialServices();
		$this->_assignTellAFriend();
		$this->_assignPriceOffer();
		$this->_assignPrintLink();
	}


	protected function _assignProductNavigator()
	{
		if(ACTIVATE_NAVIGATOR == 'true')
		{
			/* @var ProductNavigatorContentView $view */
			$view = MainFactory::create_object('ProductNavigatorContentView');
			$view->setProduct($this->product);
			$view->setCategoryId($this->currentCategoryId);
			$view->setLastListingSql($this->lastListingSql);
			$view->setFSK18DisplayAllowed((int)$this->fsk18DisplayAllowed);
			$view->setCustomerStatusId($this->customerStatusId);
			$view->setLanguageId($this->languageId);
			$html = $view->get_html();
			$this->set_content_data('PRODUCT_NAVIGATOR', $html);
		}
	}


	protected function _updateProductViewsStatistic()
	{
		$query = 'UPDATE ' . TABLE_PRODUCTS_DESCRIPTION . '
					SET products_viewed = products_viewed+1
					WHERE
						products_id = ' . (int)$this->product->data['products_id'] . ' AND
						language_id = ' . (int)$this->languageId;

		xtc_db_query($query);
	}


	protected function _assignKlarna()
	{
		if(gm_get_conf('KLARNA_SHOW_PRODUCT_PARTPAY'))
		{
			$klarna = new GMKlarna();
			$this->set_content_data('KLARNA_WIDGET',
			                        $klarna->getWidgetCode($this->productPriceArray['plain'], 'product'));
		}
		else
		{
			$this->set_content_data('KLARNA_WIDGET', '');
		}
	}


	protected function _setPriceData()
	{
		$query  = 'SELECT products_properties_combis_id
					FROM products_properties_combis
					WHERE products_id = ' . (int)$this->product->data['products_id'];
		$result = xtc_db_query($query);

		if(xtc_db_num_rows($result) >= 1)
		{
			if(xtc_db_num_rows($result) == 1)
			{
				$row           = xtc_db_fetch_array($result);
				$this->combiId = $row['products_properties_combis_id'];
			}

			$coo_properties_control = MainFactory::create_object('PropertiesControl');
			if($this->combiId > 0)
			{
				// GET selected combi (GET)
				$this->currentCombiArray = $coo_properties_control->get_combis_full_struct($this->combiId,
				                                                                           $this->languageId);
			}
			if($this->currentCombiArray == false)
			{
				// GET CHEAPEST COMBI
				$this->cheapestCombiArray            = $coo_properties_control->get_cheapest_combi($this->product->data['products_id'],
				                                                                                   $this->languageId);
				$this->xtcPrice->showFrom_Attributes = true;
			}
		}

		$this->productPriceArray = $this->xtcPrice->xtcGetPrice($this->product->data['products_id'], true, 1,
		                                                        $this->product->data['products_tax_class_id'],
		                                                        $this->product->data['products_price'], 1);

		if(!empty($this->cheapestCombiArray) && $this->cheapestCombiArray['combi_price'] != 0)
		{
			$this->productPriceArray = $this->xtcPrice->xtcGetPrice($this->product->data['products_id'], true, 1,
			                                                        $this->product->data['products_tax_class_id'],
			                                                        $this->product->data['products_price'], 1, 0, true,
			                                                        true,
			                                                        $this->cheapestCombiArray['products_properties_combis_id']);
		}

		if(!empty($this->currentCombiArray) && $this->currentCombiArray['combi_price'] != 0)
		{
			$this->productPriceArray = $this->xtcPrice->xtcGetPrice($this->product->data['products_id'], true, 1,
			                                                        $this->product->data['products_tax_class_id'],
			                                                        $this->product->data['products_price'], 1, 0, true,
			                                                        true,
			                                                        $this->currentCombiArray['products_properties_combis_id']);
		}
	}


	protected function _assignMedia()
	{
		$html = $this->_getMediaContentHtml();

		if(trim($html) !== '')
		{
			$this->set_content_data('MODULE_products_media', $html);
		}
	}


	protected function _assignAlsoPurchased()
	{
		/* @var AlsoPurchasedContentView $view */
		$view = MainFactory::create_object('AlsoPurchasedContentView');
		$view->set_coo_product($this->product);
		$html = $view->get_html();
		$this->set_content_data('MODULE_also_purchased', $html);
	}


	protected function _assignYoochoose()
	{
		if(defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE)
		{
			include_once(DIR_WS_INCLUDES . 'yoochoose/recommendations.php');
			include_once(DIR_WS_INCLUDES . 'yoochoose/functions.php');

			/* @var YoochooseAlsoInterestingContentView $view */
			$view = MainFactory::create_object('YoochooseAlsoInterestingContentView');
			$view->setProduct($this->product);
			$html = $view->get_html();
			$this->set_content_data('MODULE_yoochoose_also_interesting', $html);

			$yooHtml = '<img src="' . getTrackingURL('click', $this->product) . '" width="0" height="0" alt="">';

			if(array_key_exists('ycr', $this->getArray))
			{
				$yooHtml .= '<img src="' . getTrackingURL('follow', $this->product) . '" width="0" height="0" alt="">';
			}

			$this->set_content_data('MODULE_yoochoose_product_tracking', $yooHtml . "\n");
		}
	}


	protected function _assignAttributes()
	{
		// CREATE ProductAttributesContentView OBJECT
		/* @var ProductAttributesContentView $view */
		$view = MainFactory::create_object('ProductAttributesContentView');

		// SET TEMPLATE
		$filepath   = DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/module/product_options/';
		$c_template = $view->get_default_template($filepath, $this->product->data['options_template']);
		$view->set_content_template('module/product_options/' . $c_template);

		// SET DATA
		$view->set_coo_product($this->product);
		$view->set_language_id($this->languageId);

		// GET HTML
		$html = $view->get_html();
		$this->set_content_data('MODULE_product_options', $html);
	}


	protected function _assignCrossSelling()
	{
		/* @var CrossSellingContentView $view */
		$view = MainFactory::create_object('CrossSellingContentView');
		$view->set_type('cross_selling');
		$view->set_coo_product($this->product);
		$html = $view->get_html();
		$this->set_content_data('MODULE_cross_selling', $html);
	}


	protected function _assignReverseCrossSelling()
	{
		if(ACTIVATE_REVERSE_CROSS_SELLING == 'true')
		{
			/* @var CrossSellingContentView $view */
			$view = MainFactory::create_object('CrossSellingContentView');
			$view->set_type('reverse_cross_selling');
			$view->set_coo_product($this->product);
			$html = $view->get_html();
			$this->set_content_data('MODULE_reverse_cross_selling', $html);
		}
	}


	protected function _assignRichSnippetData()
	{
		/* @var GoogleRichSnippetContentView $view */
		$view = MainFactory::create_object('GoogleRichSnippetContentView');
		$view->set_fsk18((boolean)$this->product->data['products_fsk18']);
		$view->set_price_status($this->product->data['gm_price_status']);
		$view->set_quantity($this->product->data['products_quantity']);
		$view->set_date_available((string)$this->product->data['products_date_available']);
		$view->set_price($this->productPriceArray['plain']);
		$view->set_currency($this->currency);
		$view->set_rating_count($this->product->getReviewsCount());

		$snippetArray = $view->get_product_snippet();
		$this->set_content_data('RICH_SNIPPET_ARRAY', $snippetArray);
	}


	protected function _assignReviews()
	{
		// Aggregate review data
		$this->set_content_data('AGGREGATE_REVIEW_DATA', $this->product->getAggregateRatingData());

		/* @var ProductReviewsContentView $view */
		$view = MainFactory::create_object('ProductReviewsContentView');
		$view->setProduct($this->product);
		$html = $view->get_html();
		if(trim($html) !== '')
		{
			$this->set_content_data('MODULE_products_reviews', $html);
		}
	}


	protected function _assignProductUrl()
	{
		if(xtc_not_null($this->product->data['products_url']))
		{
			$this->set_content_data('PRODUCTS_URL', sprintf(TEXT_MORE_INFORMATION, xtc_href_link(FILENAME_REDIRECT,
			                                                                                     'action=product&id='
			                                                                                     . $this->product->data['products_id'],
			                                                                                     'NONSSL', true)));
		}
	}


	protected function _assignDate()
	{
		if($this->product->data['products_date_available'] > date('Y-m-d H:i:s'))
		{
			$this->set_content_data('PRODUCTS_DATE_AVIABLE', sprintf(TEXT_DATE_AVAILABLE,
			                                                         xtc_date_long($this->product->data['products_date_available'])));
		}
		else
		{
			if($this->product->data['products_date_added'] != '1000-01-01 00:00:00'
			   && $this->product->data['gm_show_date_added'] == 1
			)
			{
				$this->set_content_data('PRODUCTS_ADDED', sprintf(TEXT_DATE_ADDED,
				                                                  xtc_date_long($this->product->data['products_date_added'])));
			}
		}
	}


	protected function _assignGraduatedPrices()
	{
		/* @var GraduatedPricesContentView $view */
		$view = MainFactory::create_object('GraduatedPricesContentView');
		$view->set_coo_product($this->product);
		$view->set_customers_status_graduated_prices((int)$this->showGraduatedPrices);
		$html = $view->get_html();
		$this->set_content_data('MODULE_graduated_price', $html);
	}


	// TODO move out of view into control
	protected function _updateTracking()
	{
		$i = count($_SESSION['tracking']['products_history']);
		if($i > 6)
		{
			array_shift($_SESSION['tracking']['products_history']);
			$_SESSION['tracking']['products_history'][6] = $this->product->data['products_id'];
			$_SESSION['tracking']['products_history']    = array_unique($_SESSION['tracking']['products_history']);
		}
		else
		{
			$_SESSION['tracking']['products_history'][$i] = $this->product->data['products_id'];
			$_SESSION['tracking']['products_history']     = array_unique($_SESSION['tracking']['products_history']);
		}
	}


	protected function _assignPrintLink()
	{
		if(gm_get_conf('SHOW_PRINT') == 'true')
		{
			$this->set_content_data('SHOW_PRINT', 1);
		}

		$this->_assignDeprecatedPrintLink();
	}


	protected function _assignSocialServices()
	{
		$this->_assignFacebook();
		$this->_assignTwitter();
		$this->_assignGooglePlus();
		$this->_assignPinterest();
		$this->_assignBookmarking();
	}


	protected function _assignFacebook()
	{
		if(gm_get_conf('SHOW_FACEBOOK') == 'true')
		{
			$this->set_content_data('SHOW_FACEBOOK', 1);
		}
	}


	protected function _assignTwitter()
	{
		if(gm_get_conf('SHOW_TWITTER') == 'true')
		{
			$this->set_content_data('SHOW_TWITTER', 1);
		}
	}


	protected function _assignGooglePlus()
	{
		if(gm_get_conf('SHOW_GOOGLEPLUS') == 'true')
		{
			$this->set_content_data('SHOW_GOOGLEPLUS', 1);
		}
	}


	protected function _assignPinterest()
	{
		if(gm_get_conf('SHOW_PINTEREST') == 'true')
		{
			$this->set_content_data('SHOW_PINTEREST', 1);
		}
	}


	protected function _assignBookmarking()
	{
		if(gm_get_conf('SHOW_BOOKMARKING') == 'true')
		{
			$this->set_content_data('SHOW_BOOKMARKING', 1);
		}
	}

	protected function _assignImageMaxHeight()
	{
		$this->set_content_data('IMAGE_MAX_HEIGHT', $this->maxImageHeight);
	}


	protected function _assignImageData()
	{
		$imagesDataArray = array();

		/* @var GMAltText $altTextManager */
		$altTextManager = MainFactory::create_object('GMAltText');

		if($this->product->data['products_image'] != '' && $this->product->data['gm_show_image'] == '1')
		{
			$imageArray = array(
					'image_name' => $this->product->data['products_image'],
					'image_id'   => 0,
					'image_nr'   => 0
			);

			$imagesDataArray[]     = $this->_buildImageArray($imageArray, $altTextManager);
			$thumbnailsDataArray[] = $this->_buildThumbnailArray($imageArray, $altTextManager);
		}

		$additionalImagesArray = xtc_get_products_mo_images($this->product->data['products_id']);

		if(is_array($additionalImagesArray) && !empty($additionalImagesArray))
		{
			foreach($additionalImagesArray as $imageArray)
			{
				$imagesDataArray[]     = $this->_buildImageArray($imageArray, $altTextManager);
				$thumbnailsDataArray[] = $this->_buildThumbnailArray($imageArray, $altTextManager);
			}
		}

		$this->set_content_data('images', $imagesDataArray);
		$this->set_content_data('thumbnails', $thumbnailsDataArray);

		$this->_assignGMotion();

		$this->_assignDeprecatedDimensionValues();
	}


	protected function _assignGMotion()
	{
		/* @var GMGMotion $gMotion */
		$gMotion = MainFactory::create_object('GMGMotion');
		$this->set_content_data('GMOTION', $gMotion->check_status($this->product->data['products_id']));
	}


	/**
	 * GX-Customizer
	 */
	protected function _assignGPrint()
	{
		$gPrintProductManager = new GMGPrintProductManager();

		if($gPrintProductManager->get_surfaces_groups_id($this->product->data['products_id']) !== false)
		{
			$gPrintConfiguration = new GMGPrintConfiguration($this->languageId);

			$this->set_content_data('GM_GPRINT_SHOW_PRODUCTS_DESCRIPTION',
			                        $gPrintConfiguration->get_configuration('SHOW_PRODUCTS_DESCRIPTION'));
			$this->set_content_data('GM_GPRINT', 1);

			if(gm_get_conf('CUSTOMIZER_POSITION') == '2' && gm_get_env_info('TEMPLATE_VERSION') < 3)
			{
				$customizerTabHtml = '[TAB:Customize]<div id="customizer_tab_container"></div>';
				$this->product->data['products_description'] .= $customizerTabHtml;
			}
		}
	}


	/**
	 * assign formated price or link to contact form if price status is "Preis auf Anfrage"
	 */
	protected function _assignPrice()
	{
		$this->set_content_data('PRODUCTS_PRICE', $this->productPriceArray['formated']);

		if($this->product->data['gm_price_status'] == 1)
		{
			$seoBoost     = MainFactory::create_object('GMSEOBoost');
			$sefParameter = '';

			$query  = "SELECT
							content_id,
							content_title
						FROM " . TABLE_CONTENT_MANAGER . "
						WHERE
							languages_id = '" . (int)$this->languageId . "' AND
							content_group = '7'";
			$result = xtc_db_query($query);
			if(xtc_db_num_rows($result))
			{
				$row                 = xtc_db_fetch_array($result);
				$contactContentId    = $row['content_id'];
				$contactContentTitle = $row['content_title'];

				if(SEARCH_ENGINE_FRIENDLY_URLS == 'false')
				{
					$sefParameter = '&content=' . xtc_cleanName($contactContentTitle);
				}
			}
			if($seoBoost->boost_content)
			{
				$contactUrl = xtc_href_link($seoBoost->get_boosted_content_url($contactContentId, $this->languageId)
				                            . '?subject=' . rawurlencode(GM_SHOW_PRICE_ON_REQUEST . ': '
				                                                         . $this->product->data['products_name']));
			}
			else
			{
				$contactUrl = xtc_href_link(FILENAME_CONTENT,
				                            'coID=7&subject=' . rawurlencode(GM_SHOW_PRICE_ON_REQUEST . ': '
				                                                             . $this->product->data['products_name'])
				                            . $sefParameter);
			}

			$contactLinkHtml = '<a href="' . $contactUrl . '" class="price-on-request">' . GM_SHOW_PRICE_ON_REQUEST
			                   . '</a>';

			$this->set_content_data('PRODUCTS_PRICE', $contactLinkHtml);
		}
	}


	protected function _assignDiscount()
	{
		if($this->customerDiscount != 0)
		{
			$discount = $this->customerDiscount;

			if($this->product->data['products_discount_allowed'] < $this->customerDiscount)
			{
				$discount = (double)$this->product->data['products_discount_allowed'];
			}

			if($discount != 0)
			{
				$this->set_content_data('PRODUCTS_DISCOUNT', $discount . '%');
			}
		}
	}


	protected function _assignDescription()
	{
		/* @var GMTabTokenizer $tabTokenizer */
		$tabTokenizer = MainFactory::create_object('GMTabTokenizer',
		                                           array(stripslashes($this->product->data['products_description'])));
		$description  = $tabTokenizer->get_prepared_output();

		$this->set_content_data('PRODUCTS_DESCRIPTION', $description);
		$this->set_content_data('description', $tabTokenizer->head_content);

		$tabs = array();
		foreach($tabTokenizer->tab_content as $key => $value)
		{
			$tabs[] = array('title' => $value, 'content' => $tabTokenizer->panel_content[$key]);
		}

		$mediaContent = $this->_getMediaContentHtml();
		if(trim($this->_getMediaContentHtml()) !== '')
		{
			$languageTextManager = MainFactory::create_object('LanguageTextManager',
			                                                  array('products_media', $this->languageId));
			$tabs[]              = array(
					'title'   => $languageTextManager->get_text('text_media_content_tab'),
					'content' => $mediaContent
			);
		}

		$this->set_content_data('tabs', $tabs);
	}


	/**
	 * @return bool
	 */
	protected function _productIsForSale()
	{
		return ($this->showPrice
		        && $this->xtcPrice->gm_check_price_status($this->product->data['products_id']) == 0)
		       && ($this->product->data['products_fsk18'] == '0' || $this->fsk18PurchaseAllowed);
	}


	/**
	 * @param array     $imageArray
	 * @param GMAltText $altTextManager
	 *
	 * @return array
	 */
	protected function _buildImageArray(array $imageArray, GMAltText $altTextManager)
	{
		$imageMaxWidth  = 369;
		$imageMaxHeight = 279;

		$infoImageSizeArray = @getimagesize(DIR_WS_INFO_IMAGES . $imageArray['image_name']);

		$imagePaddingLeft = 0;
		$imagePaddingTop  = 0;

		if(isset($infoImageSizeArray[0]) && $infoImageSizeArray[0] < $imageMaxWidth)
		{
			$imagePaddingLeft = round(($imageMaxWidth - $infoImageSizeArray[0]) / 2);
		}

		if(isset($infoImageSizeArray[1]) && $infoImageSizeArray[1] < $imageMaxHeight)
		{
			$imagePaddingTop = round(($imageMaxHeight - $infoImageSizeArray[1]) / 2);
		}

		if($this->maxImageHeight < $infoImageSizeArray[1]){
			$this->maxImageHeight = $infoImageSizeArray[1];
		}

		$zoomImageFilepath = DIR_WS_POPUP_IMAGES . $imageArray['image_name'];

		if(file_exists(DIR_WS_ORIGINAL_IMAGES . $imageArray['image_name']))
		{
			$zoomImageFilepath = DIR_WS_ORIGINAL_IMAGES . $imageArray['image_name'];
		}

		$imageDataArray = array(
				'IMAGE'           => DIR_WS_INFO_IMAGES . $imageArray['image_name'],
				'IMAGE_ALT'       => $altTextManager->get_alt($imageArray["image_id"], $imageArray['image_nr'],
				                                              $this->product->data['products_id']),
				'IMAGE_NR'        => $imageArray['image_nr'],
				'ZOOM_IMAGE'      => $zoomImageFilepath,
				'PRODUCTS_NAME'   => $this->product->data['products_name'],
				'PADDING_LEFT'    => $imagePaddingLeft,
				'PADDING_TOP'     => $imagePaddingTop,
				'IMAGE_POPUP_URL' => DIR_WS_POPUP_IMAGES . $imageArray['image_name'],
				'WIDTH'           => $infoImageSizeArray[0],
				'HEIGHT'          => $infoImageSizeArray[1]

		);

		return $imageDataArray;
	}


	/**
	 * @param array     $imageArray
	 * @param GMAltText $altTextManager
	 *
	 * @return array
	 */
	protected function _buildThumbnailArray(array $imageArray, GMAltText $altTextManager)
	{
		$thumbnailMaxWidth  = 86;
		$thumbnailMaxHeight = 86;

		$thumbnailImageSizeArray = @getimagesize(DIR_WS_IMAGES . 'product_images/gallery_images/'
		                                         . $imageArray['image_name']);

		$thumbnailPaddingLeft = 0;
		$thumbnailPaddingTop  = 0;

		if(isset($thumbnailImageSizeArray[0]) && $thumbnailImageSizeArray[0] < $thumbnailMaxWidth)
		{
			$thumbnailPaddingLeft = round(($thumbnailMaxWidth - $thumbnailImageSizeArray[0]) / 2);
		}

		if(isset($thumbnailImageSizeArray[1]) && $thumbnailImageSizeArray[1] < $thumbnailMaxHeight)
		{
			$thumbnailPaddingTop = round(($thumbnailMaxHeight - $thumbnailImageSizeArray[1]) / 2);
		}

		$zoomImageFilepath = DIR_WS_POPUP_IMAGES . $imageArray['image_name'];

		if(file_exists(DIR_WS_ORIGINAL_IMAGES . $imageArray['image_name']))
		{
			$zoomImageFilepath = DIR_WS_ORIGINAL_IMAGES . $imageArray['image_name'];
		}

		$thumbnailDataArray = array(
				'IMAGE'         => DIR_WS_IMAGES . 'product_images/gallery_images/' . $imageArray['image_name'],
				'IMAGE_ALT'     => $altTextManager->get_alt($imageArray["image_id"], $imageArray['image_nr'],
				                                            $this->product->data['products_id']),
				'IMAGE_NR'      => $imageArray['image_nr'],
				'ZOOM_IMAGE'    => $zoomImageFilepath,
				'INFO_IMAGE'    => DIR_WS_INFO_IMAGES . $imageArray['image_name'],
				'PRODUCTS_NAME' => $this->product->data['products_name'],
				'PADDING_LEFT'  => $thumbnailPaddingLeft,
				'PADDING_TOP'   => $thumbnailPaddingTop
		);

		return $thumbnailDataArray;
	}


	protected function _assignInputFieldQuantity()
	{
		$this->set_content_data('QUANTITY', gm_convert_qty($this->product->data['gm_min_order'], false));
		$this->set_content_data('DISABLED_QUANTITY', 0);

		if((double)$this->product->data['gm_min_order'] != 1)
		{
			$this->set_content_data('GM_MIN_ORDER', gm_convert_qty($this->product->data['gm_min_order'], false));
		}

		$quantityStepping = (double)$this->product->data['gm_graduated_qty'];
		if((double)$this->product->data['gm_graduated_qty'] != 1)
		{
			$this->set_content_data('GM_GRADUATED_QTY',
			                        gm_convert_qty($this->product->data['gm_graduated_qty'], false));
		}
		$this->set_content_data('QTY_STEPPING', $quantityStepping);

		$this->_assignDeprecatedPurchaseData();
	}


	protected function _assignWishlist()
	{
		if(gm_get_conf('GM_SHOW_WISHLIST') == 'true')
		{
			$this->set_content_data('SHOW_WISHLIST', 1);
		}
		else
		{
			$this->set_content_data('SHOW_WISHLIST', 0);
		}

		$this->_assignDeprecatedWishlist();
	}


	protected function _assignLegalAgeFlag()
	{
		if($this->product->data['products_fsk18'] == '1')
		{
			$this->set_content_data('PRODUCTS_FSK18', 'true');
		}
	}


	/**
	 * @return bool
	 */
	protected function _showPrice()
	{
		return $this->showPrice
		       && ($this->xtcPrice->gm_check_price_status($this->product->data['products_id']) == 0
		           || ($this->xtcPrice->gm_check_price_status($this->product->data['products_id']) == 2
		               && $this->product->data['products_price'] > 0));
	}


	protected function _assignTaxInfo()
	{
		// price incl tax
		$tax_rate = $this->xtcPrice->TAX[$this->product->data['products_tax_class_id']];
		$tax_info = $this->main->getTaxInfo($tax_rate);
		$this->set_content_data('PRODUCTS_TAX_INFO', $tax_info);
	}


	protected function _assignShippingLink()
	{
		if($this->xtcPrice->gm_check_price_status($this->product->data['products_id']) == 0)
		{
			$this->set_content_data('PRODUCTS_SHIPPING_LINK',
			                        $this->main->getShippingLink(true, $this->product->data['products_id']));
		}
	}


	protected function _assignTellAFriend()
	{
		if(gm_get_conf('GM_TELL_A_FRIEND') == 'true')
		{
			$this->set_content_data('GM_TELL_A_FRIEND', 1);
		}
	}


	protected function _assignPriceOffer()
	{
		if($this->product->data['gm_show_price_offer'] == 1
		   && $this->showPrice
		   && $this->xtcPrice->gm_check_price_status($this->product->data['products_id']) == 0
		)
		{
			$this->set_content_data('GM_PRICE_OFFER', 1);
		}
	}


	protected function _assignStatus()
	{
		$this->set_content_data('PRODUCTS_STATUS', $this->product->data['products_status']);
	}


	protected function _assignNumberOfOrders()
	{
		$this->set_content_data('PRODUCTS_ORDERED', $this->product->data['products_ordered']);
	}


	protected function _assignFormTagData()
	{
		$this->set_content_data('FORM_ACTION_URL', xtc_href_link(FILENAME_PRODUCT_INFO,
		                                                         xtc_get_all_get_params(array('action'))
		                                                         . 'action=add_product', 'NONSSL', true, true, true));
		$this->set_content_data('FORM_ID', 'cart_quantity');
		$this->set_content_data('FORM_NAME', 'cart_quantity');
		$this->set_content_data('FORM_METHOD', 'post');

		$this->_assignDeprecatedFormTagData();
	}


	protected function _setPropertiesData()
	{
		$coo_stop_watch = LogControl::get_instance()->get_stop_watch();
		$coo_stop_watch->start('PropertiesView get_selection_form');

		$propertiesSelectionForm = $this->_buildPropertiesSelectionForm();
		$this->_assignPropertiesSelectionForm($propertiesSelectionForm);

		$coo_stop_watch->stop('PropertiesView get_selection_form');
		//$coo_stop_watch->log_total_time('PropertiesView get_selection_form');

		$this->hasProperties = trim($propertiesSelectionForm) != "";
	}


	protected function _assignModelNumber()
	{
		$modelNumber = $this->product->data['products_model'];

		if($this->hasProperties)
		{
			// OVERRIDE PRODUCTS MODEL
			if($this->currentCombiArray != false)
			{
				if(APPEND_PROPERTIES_MODEL == "true" && trim($this->currentCombiArray['combi_model']) != '')
				{
					if(trim($modelNumber) != '')
					{
						$modelNumber .= '-';
					}
					$modelNumber .= $this->currentCombiArray['combi_model'];
					$this->set_content_data('SHOW_PRODUCTS_MODEL', true);
				}
				else if(APPEND_PROPERTIES_MODEL == "false" && trim($this->currentCombiArray['combi_model']) != '')
				{
					$modelNumber = $this->currentCombiArray['combi_model'];
					$this->set_content_data('SHOW_PRODUCTS_MODEL', true);
				}
			}
			else
			{
				$modelNumber = '-';
			}
		}

		$this->set_content_data('PRODUCTS_MODEL', $modelNumber);
	}


	protected function _assignQuantity()
	{
		$quantity     = 0;
		$quantityUnit = '';

		if($this->product->data['gm_show_qty_info'] == 1)
		{
			$quantity = gm_convert_qty(xtc_get_products_stock($this->product->data['products_id']), false);
		}

		if($this->product->data['quantity_unit_id'] > 0)
		{
			$quantityUnit = $this->product->data['unit_name'];
		}

		if($this->hasProperties && $this->product->data['gm_show_qty_info'] == 1)
		{
			// OVERRIDE PRODUCTS QUANTITY
			if(($this->product->data['use_properties_combis_quantity'] == 0
			    && STOCK_CHECK == 'true'
			    && ATTRIBUTE_STOCK_CHECK == 'true')
			   || $this->product->data['use_properties_combis_quantity'] == 2
			)
			{
				$quantity = $this->currentCombiArray['combi_quantity'];
				$this->set_content_data('SHOW_PRODUCTS_QUANTITY', true);

				if($this->currentCombiArray === false)
				{
					$this->set_content_data('SHOW_PRODUCTS_QUANTITY', true);
					$quantity = '-';
				}
			}
		}

		$this->set_content_data('PRODUCTS_QUANTITY', $quantity);
		$this->set_content_data('PRODUCTS_QUANTITY_UNIT', $quantityUnit);
	}


	protected function _assignDeactivatedButtonFlag()
	{
		$deactivateButton = false;

		if($this->hasProperties)
		{
			if($this->currentCombiArray == false)
			{
				$deactivateButton = true;
			}
			else if($this->product->data['gm_show_qty_info'] == 1)
			{
				if(($this->product->data['use_properties_combis_quantity'] == 0
				    && STOCK_CHECK == 'true'
				    && ATTRIBUTE_STOCK_CHECK == 'true')
				   || $this->product->data['use_properties_combis_quantity'] == 2
				)
				{
					if($this->currentCombiArray['combi_quantity'] < gm_convert_qty($this->product->data['gm_min_order'],
					                                                               false)
					   && STOCK_ALLOW_CHECKOUT == 'false'
					)
					{
						$deactivateButton = true;
					}
				}
			}
		}

		$this->set_content_data('DEACTIVATE_BUTTON', $deactivateButton);
	}


	protected function _assignWeight()
	{
		$showWeight = 0;
		$weight     = 0;

		if($this->product->data['gm_show_weight'] == '1')
		{
			$showWeight = 1;
			$weight     = gm_prepare_number($this->product->data['products_weight'],
			                                $this->xtcPrice->currencies[$this->xtcPrice->actualCurr]['decimal_point']);

			if($this->hasProperties)
			{
				// OVERRIDE WEIGHT
				$weight = '-';

				if($this->currentCombiArray != false)
				{
					if($this->product->data['use_properties_combis_weight'] == 0)
					{
						$weight = gm_prepare_number($this->currentCombiArray['combi_weight']
						                            + $this->product->data['products_weight'],
						                            $this->xtcPrice->currencies[$this->xtcPrice->actualCurr]['decimal_point']);
					}
					else
					{
						$weight = gm_prepare_number($this->currentCombiArray['combi_weight'],
						                            $this->xtcPrice->currencies[$this->xtcPrice->actualCurr]['decimal_point']);
					}
				}
			}
		}

		$this->set_content_data('SHOW_PRODUCTS_WEIGHT', $showWeight);
		$this->set_content_data('PRODUCTS_WEIGHT', $weight);
	}


	protected function _assignVpe()
	{
		$vpeHtml = '';

		if($this->product->data['products_vpe_status'] == 1 && $this->product->data['products_vpe_value'] != 0.0
		   && $this->productPriceArray['plain'] > 0
		)
		{
			$price          = $this->productPriceArray['plain'] * (1 / $this->product->data['products_vpe_value']);
			$priceFormatted = $this->xtcPrice->xtcFormat($price, true);
			$vpeName        = xtc_get_vpe_name($this->product->data['products_vpe']);
			$vpeHtml        = $priceFormatted . TXT_PER . $vpeName;

			if($this->hasProperties)
			{
				// OVERRIDE VPE
				if($this->currentCombiArray != false && $this->currentCombiArray['products_vpe_id'] > 0
				   && $this->currentCombiArray['vpe_value'] != 0
				   && $this->productPriceArray['plain'] > 0
				)
				{
					$price          = $this->productPriceArray['plain'] * (1 / $this->currentCombiArray['vpe_value']);
					$priceFormatted = $this->xtcPrice->xtcFormat($price, true);
					$vpeName        = xtc_get_vpe_name($this->currentCombiArray['products_vpe_id']);
					$vpeHtml        = $priceFormatted . TXT_PER . $vpeName;
				}
				elseif($this->cheapestCombiArray['products_vpe_id'] > 0 && $this->cheapestCombiArray['vpe_value'] != 0
				       && $this->productPriceArray['plain'] > 0
				)
				{
					$price          = $this->productPriceArray['plain'] * (1 / $this->cheapestCombiArray['vpe_value']);
					$priceFormatted = $this->xtcPrice->xtcFormat($price, true);
					$vpeName        = xtc_get_vpe_name($this->cheapestCombiArray['products_vpe_id']);
					$vpeHtml        = $priceFormatted . TXT_PER . $vpeName;
				}
			}
		}

		$this->set_content_data('PRODUCTS_VPE', $vpeHtml);
	}


	protected function _assignShippingTime()
	{
		$name  = '';
		$image = '';

		if(ACTIVATE_SHIPPING_STATUS == 'true'
		   && $this->xtcPrice->gm_check_price_status($this->product->data['products_id']) == 0
		)
		{
			$name  = $this->main->getShippingStatusName($this->product->data['products_shippingtime']);
			$image = $this->main->getShippingStatusImage($this->product->data['products_shippingtime']);
		}

		if($this->hasProperties)
		{

			// OVERRIDE SHIPPING STATUS
			if(ACTIVATE_SHIPPING_STATUS == 'true'
			   && $this->xtcPrice->gm_check_price_status($this->product->data['products_id']) == 0
			   && $this->product->data['use_properties_combis_shipping_time'] == 1
			)
			{
				if($this->currentCombiArray != false)
				{
					$name  = $this->main->getShippingStatusName($this->currentCombiArray['combi_shipping_status_id']);
					$image = $this->main->getShippingStatusImage($this->currentCombiArray['combi_shipping_status_id']);
				}
				else
				{
					$name  = '';
					$image = 'admin/html/assets/images/legacy/icons/gray.png';
				}
				$this->set_content_data('SHOW_SHIPPING_TIME', true);
			}
		}

		$this->set_content_data('SHIPPING_NAME', $name);
		$this->set_content_data('SHIPPING_IMAGE', $image);
		$this->set_content_data('ABROAD_SHIPPING_INFO_LINK_ACTIVE',
		                        $this->main->getShippingStatusInfoLinkActive($this->product->data['products_shippingtime']));
		$this->set_content_data('ABROAD_SHIPPING_INFO_LINK', main::get_abroad_shipping_info_link());
	}


	protected function _assignEan()
	{
		$this->set_content_data('PRODUCTS_EAN', $this->product->data['products_ean']);
	}


	protected function _assignId()
	{
		$this->set_content_data('PRODUCTS_ID', $this->product->data['products_id']);
	}


	protected function _assignName()
	{
		$this->set_content_data('PRODUCTS_NAME', $this->product->data['products_name']);
	}


	/**
	 * @return string
	 */
	protected function _buildPropertiesSelectionForm()
	{
		/* @var PropertiesView $view */
		$view                    = MainFactory::create_object('PropertiesView',
		                                                      array($this->getArray, $this->postArray));
		$propertiesSelectionForm = $view->get_selection_form($this->product->data['products_id'], $this->languageId,
		                                                     false, $this->currentCombiArray);

		return $propertiesSelectionForm;
	}


	/**
	 * @param $propertiesSelectionForm
	 */
	protected function _assignPropertiesSelectionForm($propertiesSelectionForm)
	{
		$this->set_content_data('properties_selection_form', $propertiesSelectionForm);
	}
	
	
	protected function _assignAdditionalFields()
	{
		$additionalFieldsHtml = '';
		
		if(gm_get_conf('SHOW_ADDITIONAL_FIELDS_PRODUCT_DETAILS') === 'true')
		{
			$view = MainFactory::create_object('AdditionalFieldContentView');
			$view->setLanguageId($this->languageId);
			$view->setAdditionalFields($this->additionalFields);
			$additionalFieldsHtml = $view->get_html();
		}
		
		$this->set_content_data('additional_fields', $additionalFieldsHtml);
	}


	##### SETTER / GETTER #####

	/**
	 * @return array
	 */
	public function getGetArray()
	{
		return $this->getArray;
	}


	/**
	 * $_GET-Data
	 *
	 * @param array $getArray
	 */
	public function setGetArray(array $getArray)
	{
		$this->getArray = $getArray;
	}


	/**
	 * @return array
	 */
	public function getPostArray()
	{
		return $this->postArray;
	}


	/**
	 * $_POST-Data
	 *
	 * @param array $postArray
	 */
	public function setPostArray(array $postArray)
	{
		$this->postArray = $postArray;
	}


	/**
	 * @param product $product
	 */
	public function setProduct(product $product)
	{
		$this->product = $product;
	}


	/**
	 * @return product
	 */
	public function getProduct()
	{
		return $this->product;
	}


	/**
	 * @param int $p_categoryId
	 */
	public function setCurrentCategoryId($p_categoryId)
	{
		$this->currentCategoryId = (int)$p_categoryId;
	}


	/**
	 * @return int
	 */
	public function getCurrentCategoryId()
	{
		return $this->currentCategoryId;
	}


	/**
	 * @return int
	 */
	public function getCombiId()
	{
		return $this->combiId;
	}


	/**
	 * @param int $p_combiId
	 */
	public function setCombiId($p_combiId)
	{
		$this->combiId = (int)$p_combiId;
	}


	/**
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->languageId;
	}


	/**
	 * @param int $p_languageId
	 */
	public function setLanguageId($p_languageId)
	{
		$this->languageId = (int)$p_languageId;
	}


	/**
	 * @return main
	 */
	public function getMain()
	{
		return $this->main;
	}


	/**
	 * @param mixed $main
	 */
	public function setMain(main $main)
	{
		$this->main = $main;
	}


	/**
	 * @return xtcPrice
	 */
	public function getXtcPrice()
	{
		return $this->xtcPrice;
	}


	/**
	 * @param xtcPrice $xtcPrice
	 */
	public function setXtcPrice(xtcPrice $xtcPrice)
	{
		$this->xtcPrice = $xtcPrice;
	}


	/**
	 * @return string
	 */
	public function getCurrency()
	{
		return $this->currency;
	}


	/**
	 * @param string $p_currency
	 */
	public function setCurrency($p_currency)
	{
		$this->currency = (string)$p_currency;
	}


	/**
	 * @return boolean
	 */
	public function getShowGraduatedPrices()
	{
		return $this->showGraduatedPrices;
	}


	/**
	 * @param boolean $p_showGraduatedPrices
	 */
	public function setShowGraduatedPrices($p_showGraduatedPrices)
	{
		$this->showGraduatedPrices = (bool)$p_showGraduatedPrices;
	}


	/**
	 * @return double
	 */
	public function getCustomerDiscount()
	{
		return $this->customerDiscount;
	}


	/**
	 * @param double $p_customerDiscount
	 */
	public function setCustomerDiscount($p_customerDiscount)
	{
		$this->customerDiscount = (double)$p_customerDiscount;
	}


	/**
	 * @return boolean
	 */
	public function getShowPrice()
	{
		return $this->showPrice;
	}


	/**
	 * @param boolean $p_showPrice
	 */
	public function setShowPrice($p_showPrice)
	{
		$this->showPrice = (bool)$p_showPrice;
	}


	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}


	/**
	 * @param string $p_language
	 */
	public function setLanguage($p_language)
	{
		$this->language = basename((string)$p_language);
	}


	/**
	 * @return boolean
	 */
	public function getFSK18PurchaseAllowed()
	{
		return $this->fsk18PurchaseAllowed;
	}


	/**
	 * @param boolean $p_FSK18PurchaseAllowed
	 */
	public function setFSK18PurchaseAllowed($p_FSK18PurchaseAllowed)
	{
		$this->fsk18PurchaseAllowed = (bool)$p_FSK18PurchaseAllowed;
	}


	/**
	 * @return boolean
	 */
	public function getFSK18DisplayAllowed()
	{
		return $this->fsk18DisplayAllowed;
	}


	/**
	 * @param boolean $p_FSK18DisplayAllowed
	 */
	public function setFSK18DisplayAllowed($p_FSK18DisplayAllowed)
	{
		$this->fsk18DisplayAllowed = (bool)$p_FSK18DisplayAllowed;
	}


	/**
	 * @return string
	 */
	public function getLastListingSql()
	{
		return $this->lastListingSql;
	}


	/**
	 * @param string $p_lastListingSql
	 */
	public function setLastListingSql($p_lastListingSql)
	{
		$this->lastListingSql = (string)$p_lastListingSql;
	}


	/**
	 * @return int
	 */
	public function getCustomerStatusId()
	{
		return $this->customerStatusId;
	}


	/**
	 * @param int $p_customerStatusId
	 */
	public function setCustomerStatusId($p_customerStatusId)
	{
		$this->customerStatusId = (int)$p_customerStatusId;
	}

	/**
	 * @return array
	 */
	public function getAdditionalFields()
	{
		return $this->additionalFields;
	}


	/**
	 * @param array $p_additionalFields
	 */
	public function setAdditionalFields($p_additionalFields)
	{
		$this->additionalFields = $p_additionalFields;
	}


	##### DEPRECATED since GX2.2 #####

	protected function _assignDeprecatedPurchaseData()
	{
		$this->set_content_data('ADD_QTY', xtc_draw_input_field('products_qty', str_replace('.', ',',
		                                                                                    (double)$this->product->data['gm_min_order']),
		                                                        'id="gm_attr_calc_qty"') . ' '
		                                   . xtc_draw_hidden_field('products_id', $this->product->data['products_id'],
		                                                           'id="gm_products_id"'), 2);

		$this->set_content_data('ADD_CART_BUTTON',
		                        xtc_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, 'id="cart_button"'), 2);
	}


	protected function _assignDeprecatedWishlist()
	{
		if(gm_get_conf('GM_SHOW_WISHLIST') == 'true')
		{
			$this->set_content_data('ADD_WISHLIST_BUTTON',
			                        '<a href="javascript:submit_to_wishlist()" id="gm_wishlist_link">'
			                        . xtc_image_button('button_in_wishlist.gif', NC_WISHLIST) . '</a>', 2);
		}
	}


	protected function _assignDeprecatedPrintLink()
	{
		$this->set_content_data('PRODUCTS_PRINT',
		                        '<img src="templates/' . CURRENT_TEMPLATE . '/buttons/' . $this->language
		                        . '/print.gif"  style="cursor:hand;" onclick="javascript:window.open(\''
		                        . xtc_href_link(FILENAME_PRINT_PRODUCT_INFO,
		                                        'products_id=' . $this->product->data['products_id'])
		                        . '\', \'popup\', \'toolbar=0, width=640, height=600\')" alt="" />');
	}


	protected function _assignDeprecatedFormTagData()
	{
		$this->set_content_data('FORM_ACTION', xtc_draw_form('cart_quantity', xtc_href_link(FILENAME_PRODUCT_INFO,
		                                                                                    xtc_get_all_get_params(array('action'))
		                                                                                    . 'action=add_product',
		                                                                                    'NONSSL', true, true, true),
		                                                     'post',
		                                                     'name="cart_quantity" onsubmit="gm_qty_check = new GMOrderQuantityChecker(); return gm_qty_check.check();"'),
		                        2);
		$this->set_content_data('FORM_END', '</form>', 2);
	}


	protected function _assignDeprecatedIdHiddenField()
	{
		$this->set_content_data('GM_PID', xtc_draw_hidden_field('products_id', $this->product->data['products_id'],
		                                                        'id="gm_products_id"'), 2);
	}


	protected function _assignDeprecatedDimensionValues()
	{
		if(PRODUCT_IMAGE_INFO_WIDTH < (190 - 16))
		{
			$this->set_content_data('MIN_IMAGE_WIDTH', 188, 2);
			$this->set_content_data('MIN_INFO_BOX_WIDTH', 156 - 10, 2);
			$this->set_content_data('MARGIN_LEFT', 188 + 10, 2);
		}
		else
		{
			$this->set_content_data('MIN_IMAGE_WIDTH', PRODUCT_IMAGE_INFO_WIDTH + 16, 2);
			$this->set_content_data('MIN_INFO_BOX_WIDTH', PRODUCT_IMAGE_INFO_WIDTH + 16 - 32 - 10, 2);
			$this->set_content_data('MARGIN_LEFT', PRODUCT_IMAGE_INFO_WIDTH + 16 + 10, 2);
		}
	}


	/**
	 * @return string
	 */
	protected function _getMediaContentHtml()
	{
		/* @var ProductMediaContentView $view */
		$view = MainFactory::create_object('ProductMediaContentView');
		$view->setProductId($this->product->data['products_id']);
		$view->setLanguageId($this->languageId);
		$view->setCustomerStatusId($this->customerStatusId);
		$html = $view->get_html();

		return $html;
	}
}
