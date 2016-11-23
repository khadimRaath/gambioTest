<?php
/* --------------------------------------------------------------
   MegaFlyoverContentView.php 2014-11-18 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(create_account.php,v 1.63 2003/05/28); www.oscommerce.com
   (c) 2003  nextcommerce (create_account.php,v 1.27 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: create_account.php 1311 2005-10-18 12:30:40Z mz $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
   http://www.oscommerce.com/community/contributions,282
   Copyright (c) Strider | Strider@oscworks.com
   Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
   Copyright (c) Andre ambidex@gmx.net
   Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org


   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once (DIR_FS_INC.'xtc_get_vpe_name.inc.php');
require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');

/**
 * Class MegaFlyoverContentView
 */
class MegaFlyoverContentView extends ContentView
{
	protected $productId;
	protected $xtcPrice;
	protected $main;
	protected $showPrice;

	protected $product;
	protected $imagesDataArray = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/gm_mega_flyover.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		if($this->productId === 0)
		{
			$this->_setRandomProductId();
		}
		
		$this->product = new product($this->productId);

		$this->_assignProductData();
		$this->_assignImagesData();
		$this->_assignShippingInfo();
		$this->_assignPriceData();
	}
	

	/**
	 * set random productId for StyleEdit
	 */
	protected function _setRandomProductId()
	{
		$query = 'SELECT products_id
					FROM products
					WHERE products_status = 1
					ORDER BY products_id DESC
					LIMIT 1';
		$result = xtc_db_query($query);

		if(xtc_db_num_rows($result))
		{
			$row = xtc_db_fetch_array($result);
			$this->productId = (int)$row['products_id'];
		}
	}


	/**
	 * @return array
	 */
	protected function _getProductPriceArray()
	{
		$products_price = $this->xtcPrice->xtcGetPrice($this->product->data['products_id'], $format = true, 1,
													   $this->product->data['products_tax_class_id'],
													   $this->product->data['products_price'], 1);

		return $products_price;
	}


	protected function _assignProductData()
	{
		$this->set_content_data('PRODUCTS_NAME', $this->product->data['products_name']);
		$this->set_content_data('PRODUCTS_SHORT_DESCRIPTION', stripslashes($this->product->data['products_short_description']));

		if($this->product->data['products_fsk18'] == '1')
		{
			$this->set_content_data('PRODUCTS_FSK18', 'true');
		}

		$this->set_content_data('PRODUCTS_ID', $this->product->data['products_id']);
		$this->set_content_data('PRODUCTS_NAME', $this->product->data['products_name']);

		$this->set_content_data('GM_TRUNCATE', gm_get_conf('TRUNCATE_FLYOVER'));
		$this->set_content_data('GM_TRUNCATE_TEXT', gm_get_conf('TRUNCATE_FLYOVER_TEXT'));
	}


	protected function _assignImagesData()
	{

		$this->set_content_data('BOX_WIDTH', PRODUCT_IMAGE_INFO_WIDTH);
		$this->set_content_data('BOX_HEIGHT', PRODUCT_IMAGE_INFO_HEIGHT);

		$image = '';
		if($this->product->data['products_image'] != '')
		{
			$image = DIR_WS_INFO_IMAGES . $this->product->data['products_image'];
			$this->_addImage($this->product->data['products_image'], 0, $this->product->data['products_name']);
		}

		$this->set_content_data('PRODUCTS_IMAGE', $image);

		$this->set_content_data('gm_image_width', $this->product->data['products_image_w'] + 20);

		$imagesArray = xtc_get_products_mo_images($this->product->data['products_id']);

		if(!empty($imagesArray))
		{
			foreach($imagesArray as $imageArray)
			{
				$this->_addImage($imageArray['image_name'], $imageArray['image_nr'], $this->product->data['products_name']);
			}
		}

		$this->set_content_data('images_data', $this->imagesDataArray);
	}


	protected function _assignShippingInfo()
	{
		if(ACTIVATE_SHIPPING_STATUS == 'true')
		{
			$this->set_content_data('SHIPPING_NAME', $this->main->getShippingStatusName($this->product->data['products_shippingtime']));
			$this->set_content_data('SHIPPING_IMAGE', $this->main->getShippingStatusImage($this->product->data['products_shippingtime']));
		}

		if($this->showStatus != 0)
		{
			$this->set_content_data('PRODUCTS_SHIPPING_LINK', $this->main->getShippingLink(true, $this->product->data['products_id']));
		}
	}


	protected function _assignPriceData()
	{
		$products_price = $this->_getProductPriceArray();
		$this->set_content_data('PRODUCTS_PRICE', $products_price['formated']);

		if($this->product->data['products_vpe_status'] == 1 
		   && $this->product->data['products_vpe_value'] != 0.0 
		   && $products_price['plain'] > 0
		)
		{
			$price = $products_price['plain'] * (1 / $this->product->data['products_vpe_value']);
			$vpeText = $this->xtcPrice->xtcFormat($price, true) . TXT_PER . xtc_get_vpe_name($this->product->data['products_vpe']);
			$this->set_content_data('PRODUCTS_VPE', $vpeText);
		}

		if($this->showStatus != 0)
		{
			// price incl tax
			$tax_rate = $this->xtcPrice->TAX[$this->product->data['products_tax_class_id']];
			$tax_info = $this->main->getTaxInfo($tax_rate);
			$this->set_content_data('PRODUCTS_TAX_INFO', $tax_info);
		}
	}


	/**
	 * @param string $p_filename
	 * @param int $p_imageNumber
	 * @param string $productName
	 */
	protected function _addImage($p_filename, $p_imageNumber, $productName)
	{
		$c_filename = basename($p_filename);

		if($c_filename != '' && file_exists(DIR_WS_INFO_IMAGES . $c_filename))
		{
			$sizeArray = @getimagesize(DIR_WS_INFO_IMAGES . $c_filename);

			$paddingLeft = 0;
			$paddingTop  = 0;

			if(isset($sizeArray[0]) && $sizeArray[0] < PRODUCT_IMAGE_INFO_WIDTH)
			{
				$paddingLeft = round((PRODUCT_IMAGE_INFO_WIDTH - $sizeArray[0]) / 2);
			}

			if(isset($sizeArray[1]) && $sizeArray[1] < PRODUCT_IMAGE_INFO_HEIGHT)
			{
				$paddingTop = round((PRODUCT_IMAGE_INFO_HEIGHT - $sizeArray[1]) / 2);
			}

			$this->imagesDataArray[] = array('IMAGE'         => DIR_WS_INFO_IMAGES . $c_filename,
											 'IMAGE_NR'      => $p_imageNumber,
											 'PRODUCTS_NAME' => $productName,
											 'PADDING_LEFT'  => $paddingLeft,
											 'PADDING_TOP'   => $paddingTop
			);
		}
	}


	/**
	 * @param int $p_productId
	 */
	public function setProductId($p_productId)
	{
		$this->productId = (int)$p_productId;
	}


	/**
	 * @return int
	 */
	public function getProductId()
	{
		return $this->productId;
	}


	/**
	 * @param main $main
	 */
	public function setMain(main $main)
	{
		$this->main = $main;
	}


	/**
	 * @return main
	 */
	public function getMain()
	{
		return $this->main;
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
	 * @param xtcPrice $xtcPrice
	 */
	public function setXtcPrice(xtcPrice $xtcPrice)
	{
		$this->xtcPrice = $xtcPrice;
	}


	/**
	 * @return xtcPrice
	 */
	public function getXtcPrice()
	{
		return $this->xtcPrice;
	}


	/**
	 * @param bool $p_showPrice
	 */
	public function setShowPrice($p_showPrice)
	{
		$this->showPrice = (bool)$p_showPrice;
	}


	/**
	 * @return bool
	 */
	public function getShowPrice()
	{
		return $this->showPrice;
	}
}