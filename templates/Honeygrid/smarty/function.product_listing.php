<?php
/* --------------------------------------------------------------
   function.product_listing.php 2016-03-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
function smarty_function_product_listing($params, &$smarty)
{
	// ######### PARAMETER INITIALIZATION ##########
	
	$slidesPerViewArray = array(
		1 => 12,
		2 => 6,
		3 => 4,
		4 => 3,
		6 => 2,
		12 => 1
	);
	
	$key = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-col-xs');
	$xsSlidesPerView = 2;
	if(array_key_exists($key, $slidesPerViewArray))
	{
		$xsSlidesPerView = $slidesPerViewArray[$key];
	}
	
	$key = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-col-sm');
	$smSlidesPerView = 3;
	if(array_key_exists($key, $slidesPerViewArray))
	{
		$smSlidesPerView = $slidesPerViewArray[$key];
	}
	
	$key = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-col-md');
	$mdSlidesPerView = 2;
	if(array_key_exists($key, $slidesPerViewArray))
	{
		$mdSlidesPerView = $slidesPerViewArray[$key];
	}
	
	$defaults = array(
		'products'        => array(),
		'truncate'        => 80,
		'id'              => 'swiper_' . rand(),
		'template'        => 'snippets/product_listing/product_listing_swiper.html',
		'productTemplate' => 'templates/' . CURRENT_TEMPLATE . '/snippets/product_listing/product_grid_only.html',
		'startWidget'     => true,
		'hoverable'       => false,
		'itemProperties'  => '',
		'target'          => null,
		'controls'        => null,
		'swiperOptions'   => null,
		'maxHeight'       => null,
		'autoOff'         => 'true',
		'swiperOptions'   => array(
			'spaceBetween'  => 0,
			'loop'          => true,
			'slidesPerView' => 4,
			'autoplay'      => null,
			'breakpoints'   => array(
				array(
					'breakpoint'        => 40,
					'usePreviewBullets' => true,
					'slidesPerView'     => $xsSlidesPerView,
					'centeredSlides'    => true
				),
				array(
					'breakpoint'        => 60,
					'usePreviewBullets' => true,
					'slidesPerView'     => $smSlidesPerView
				),
				array(
					'breakpoint'        => 80,
					'usePreviewBullets' => true,
					'slidesPerView'     => $mdSlidesPerView
				)
			)
		)
	);
	
	$internalDefaults = array(
		'engineAttrEnabled'  => 'data-gambio-widget',
		'engineAttrDisabled' => 'data-_gambio-widget',
	);
	
	if(empty($params['fullscreenPage']))
	{
		$key = $GLOBALS['coo_template_control']->findSettingValueByName('gx-product-listing-col-lg');
		
		$slidesPerView = 4;
		if(array_key_exists($key, $slidesPerViewArray))
		{
			$slidesPerView = $slidesPerViewArray[$key];
		}
		
		$defaults['swiperOptions']['breakpoints'][] = array(
				'breakpoint'        => 100,
				'usePreviewBullets' => true,
				'slidesPerView'     => $slidesPerView
		);
	}
	
	$options = array_merge($defaults, $params);
	
	// ########## GENERATE PARAMETER ##########
	
	// Get the aditional widgets as string.
	$widgets               = ($options['hoverable']) ? 'product_hover' : '';
	$options['engineAttr'] = ($options['startWidget']) ? $internalDefaults['engineAttrEnabled'] : $internalDefaults['engineAttrDisabled'];
	$options['widgets']    = ($widgets !== '') ? $options['engineAttr'] . '="' . $widgets . '"' : '';
	
	// Add the button classes
	$options['next']       = 'js-' . $options['id'] . '-button-next';
	$options['prev']       = 'js-' . $options['id'] . '-button-prev';
	$options['pagination'] = 'js-' . $options['id'] . '-pagination';
	
	// Generate an options string for the swiper
	$options['swiperOptions']['nextButton'] = '.' . $options['next'];
	$options['swiperOptions']['prevButton'] = '.' . $options['prev'];
	$options['swiperOptions']['pagination'] = '.' . $options['pagination'];
	
	$options['configuration'] = '';
	$options['configuration'] .= ($options['target']) ? 'data-swiper-target="' . $options['target'] . '" ' : '';
	$options['configuration'] .= ($options['controls']) ? 'data-swiper-controls="' . $options['controls'] . '" ' : '';
	$options['configuration'] .= 'data-swiper-auto-off="'. $options['autoOff'] .'" data-swiper-slider-options="' . str_replace('"', '&quot;',
	                                                                          json_encode($options['swiperOptions']))
	                             . '" ';
	
	// Add misc options
	$options['maxHeight'] = ($options['maxHeight']) ? 'style="height: ' . $options['maxHeight'] . ' px;"' : '';
	
	// IMAGE SWIPER FALLBACK
	$options['popup']  = '';
	$options['images'] = array();
	
	// ########## UPDATE PRODUCT DATA ##########
	
	// Shorten product descriptions
	foreach($options['products'] AS $index => $product)
	{
		
		$options['products'][$index]['showManufacturerImages'] = $params['showManufacturerImages'];
		$options['products'][$index]['showProductRibbons'] = $params['showProductRibbons'];
		$options['products'][$index]['DELIVERY'] = $product['PRODUCTS_SHIPPING_NAME'];
		$options['products'][$index]['SHORTENED_PRODUCTS_DESCRIPTION'] = $product['PRODUCTS_SHORT_DESCRIPTION'];
		
		if(!empty($product['PRODUCTS_META_DESCRIPTION'])
		   && preg_match('/^.{1,77}\b/s', $product['PRODUCTS_META_DESCRIPTION'], $shortened)
		)
		{
			$extend = (strlen($product['PRODUCTS_META_DESCRIPTION']) > 77) ? '...' : '';
			
			$options['products'][$index]['SHORTENED_META_DESCRIPTION'] = str_replace('"', '&quot;', $shortened[0])
			                                                             . $extend;
		}
		else
		{
			$options['products'][$index]['SHORTENED_META_DESCRIPTION'] = $product['PRODUCTS_NAME'];
		}
		
		mb_regex_encoding('UTF-8');
		mb_regex_set_options('m');
		$pattern = '^.{1,' . $options['truncate'] . '}\b';
		if(mb_ereg($pattern, html_entity_decode_wrapper($product['PRODUCTS_NAME']), $shortened))
		{
			$extend                                  = (strlen($product['PRODUCTS_NAME'])
			                                            > $options['truncate']) ? '...' : '';
			$options['products'][$index]['HEADLINE'] = htmlspecialchars_wrapper($shortened[0]) . $extend;
		}
		else
		{
			$options['products'][$index]['HEADLINE'] = $product['PRODUCTS_NAME'];
		}
		
		$options['products'][$index]['PRODUCTS_IMAGE']     = (!empty($product['PRODUCTS_IMAGE'])) ? $product['PRODUCTS_IMAGE'] : '';
		$options['products'][$index]['PRODUCTS_IMAGE_ALT'] = (!empty($product['PRODUCTS_IMAGE_ALT'])) ? $product['PRODUCTS_IMAGE_ALT'] : $product['PRODUCTS_NAME'];
		$options['products'][$index]['PRODUCTS_IMAGE_ALT'] = str_replace('"', '&quot;',
		                                                                 $options['products'][$index]['PRODUCTS_IMAGE_ALT']);
		$options['products'][$index]['INDEX']              = $options['id'] . '-' . $product['PRODUCTS_ID'];
		$options['products'][$index]['PRODUCTS_VPE']       = (!empty($product['PRODUCTS_VPE'])) ? $product['PRODUCTS_VPE'] : '';
	}
	
	$options['_TYPE'] = 'PRODUCTS';
	
	// ########## GENERATE SWIPER ##########
	
	$contentView = MainFactory::create_object('ContentView');
	$contentView->set_content_template($options['template']);
	$contentView->set_content_data('SWIPER_DATA', $options);
	
	$html = $contentView->get_html();
	
	return $html;
}