<?php
/* --------------------------------------------------------------
   PopupImageContentView.inc.php 2016-03-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com
   (c) 2003	 nextcommerce www.nextcommerce.org

   XTC-NEWSLETTER_RECIPIENTS RC1 - Contribution for XT-Commerce http://www.xt-commerce.com
   by Matthias Hinsche http://www.gamesempire.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class PopupImageContentView extends ContentView
{
	
	/**
	 * @var IdType
	 */
	protected $productId;
	
	/**
	 * @var IdType
	 */
	protected $imageId;
	
	/**
	 * @var IdType
	 */
	protected $languageId;
	
	public function __construct(IdType $productId, IdType $imageId, IdType $languageId)
	{
		parent::__construct();
		$this->set_content_template('module/popup_image.html');
		$this->set_flat_assigns(true);
		
		$this->productId  = $productId;
		$this->imageId    = $imageId;
		$this->languageId = $languageId;
	}

	function get_html()
	{
		require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');

		if ($t_imgID == 0) {
			$products_query = xtc_db_query("
				SELECT pd.products_name, p.products_image
				FROM ".TABLE_PRODUCTS." p
					LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd ON p.products_id = pd.products_id
				WHERE p.products_status = '1'
					AND p.products_id = '". $this->productId->asInt() ."'
					AND pd.language_id = '". $this->languageId->asInt() ."'");
			$products_values = xtc_db_fetch_array($products_query);
		} else {
			$products_query = xtc_db_query("
				SELECT pd.products_name, p.products_image, pi.image_name
				FROM ".TABLE_PRODUCTS_IMAGES." pi, ".TABLE_PRODUCTS." p
					LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd on p.products_id = pd.products_id
				WHERE p.products_status = '1'
					AND p.products_id = '". $this->productId->asInt() ."'
					AND pi.products_id = '". $this->productId->asInt() ."'
					AND pi.image_nr = '". $this->imageId->asInt() ."'
					AND pd.language_id = '". $this->languageId->asInt() ."'");
			$products_values = xtc_db_fetch_array($products_query);
			$products_values['products_image'] = $products_values['image_name'];
		}

		// get x and y of the image
		$img = DIR_WS_POPUP_IMAGES.$products_values['products_image'];
		$size = getimagesize("$img");

		//get data for mo_images
		$mo_images = xtc_get_products_mo_images($this->productId->asInt());
		$img = DIR_WS_THUMBNAIL_IMAGES.$products_values['products_image'];
		$osize = GetImageSize("$img");
		if ($mo_images != false) {
			$bheight = $osize[1];
			foreach ($mo_images as $mo_img) {
				$img = DIR_WS_THUMBNAIL_IMAGES.$mo_img['image_name'];
				$mo_size = GetImageSize("$img");
				if ($mo_size[1] > $bheight) {
					$bheight = $mo_size[1];
				}
			}
			$bheight += 50;
		}

		$t_product_image = xtc_image(DIR_WS_POPUP_IMAGES . $products_values['products_image'], $products_values['products_name'], $size[0], $size[1]);

		$this->set_content_data('PRODUCTS_NAME', $products_values['products_name']);
		$this->set_content_data('PRODUCT_IMAGE', $t_product_image);
		$this->set_content_data('IMAGE_WIDTH', $size[0]);
		$this->set_content_data('IMAGE_HEIGHT', $size[1] + $bheight);
		$this->set_content_data('HEIGHT_MULTIPLIER', $bheight);
		$this->set_content_data('PRODUCT_ID', $this->productId->asInt());
		$this->set_content_data('IMAGE_ID', $this->imageId->asInt());
		$this->set_content_data('STYLESHEET', 'templates/'.CURRENT_TEMPLATE.'/stylesheet.css');
		$this->set_content_data('DYNAMIC_CSS', 'templates/'.CURRENT_TEMPLATE.'/gm_dynamic.css.php'.$renew_cache);
		$this->set_content_data('CONTENT_HEADING', $products_values['products_name']);
		$this->set_content_data('MORE_IMAGES', $mo_images);
		$this->set_content_data('IFRAME_WIDTH', ($size[0] + 40));
		$this->set_content_data('IFRAME_HEIGHT', ($bheight + 5));

		$t_html_output = $this->build_html();

		return $t_html_output;
	}
}