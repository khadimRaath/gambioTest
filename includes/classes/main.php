<?php
/* --------------------------------------------------------------
   main.php 2016-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(Coding Standards); www.oscommerce.com
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: main.php 1286 2005-10-07 10:10:18Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

 class main_ORIGIN {

 	public function __construct() {
 		$this->SHIPPING = array();




 		// prefetch shipping status
		$status_query = xtDBquery("SELECT
                                     shipping_status_name,
                                     shipping_status_image,
									 shipping_status_id,
									 info_link_active
                                   FROM
									 " . TABLE_SHIPPING_STATUS . "
                                   WHERE
									 language_id = '" . (int)$_SESSION['languages_id'] . "'");

        while ($status_data = xtc_db_fetch_array($status_query, true))
		{
			$this->SHIPPING[$status_data['shipping_status_id']] = array(
				'name' => $status_data['shipping_status_name'],
				'image' => $status_data['shipping_status_image'],
				'info_link_active' => $status_data['info_link_active']
			);
        }


 	}

 	function getShippingStatusName($id) {
 		return $this->SHIPPING[$id]['name'];
 	}
 	function getShippingStatusImage($id) {
 		if ($this->SHIPPING[$id]['image']) {
 		return 'admin/html/assets/images/legacy/icons/'.$this->SHIPPING[$id]['image'];
 		} else {
 			return;
 		}
 	}

	public function getShippingStatusInfoLinkActive($p_id)
	{
		return $this->SHIPPING[$p_id]['info_link_active'];
	}


	 /**
	  * @param bool $p_lightbox
	  * @param int  $p_productId
	  *
	  * @return string
	  */
	 public function getShippingLink($p_lightbox = false, $p_productId = 0)
	{
		if(SHOW_SHIPPING == 'true')
		{
			if($p_productId > 0 && $this->checkFreeShippingByProductId($p_productId))
			{
				$languageTextManager = MainFactory::create_object('languageTextManager', array('product_info', (int)$_SESSION['languages_id']));
				
				return ' ' . $languageTextManager->get_text('text_free_shipping');
			}
			
			return ' '.SHIPPING_EXCL.'
				<a class="gm_shipping_link lightbox_iframe" href="' . $this->gm_get_shipping_link($p_lightbox) . '" 
						target="_self" rel="nofollow" 
						data-modal-settings=\'{"title":"' . SHIPPING_COSTS  . '", "sectionSelector": ".content_text", "bootstrapClass": "modal-lg"}\'>
					<span style="text-decoration:underline">' . SHIPPING_COSTS.'</span>
		        </a>';
		}
	}


	 /**
	  * @param int $p_productId
	  *
	  * @return bool
	  */
	 public function checkFreeShippingByProductId($p_productId)
	 {
		 $query = 'SELECT COUNT(*) AS count 
		 			FROM 
		 				products_attributes a,
		 				products_attributes_download d 
		 			WHERE 
		 				a.products_id = ' . (int)$p_productId . ' AND
		 				a.products_attributes_id = d.products_attributes_id';
		 $result = xtc_db_query($query);
		 $row = xtc_db_fetch_array($result);
		 
		 if($row['count'] > 0)
		 {
			 return true;
		 }

		 return false;
	 }

	function getTaxNotice() {

		// no prices
		if ($_SESSION['customers_status']['customers_status_show_price'] == 0)
			return;

		if ($_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
			return TAX_INFO_INCL_GLOBAL;
		}
		// excl tax + tax at checkout
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
			return TAX_INFO_ADD_GLOBAL;
		}
		// excl tax
		if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
			return TAX_INFO_EXCL_GLOBAL;
		}

		return;
	}

	function getTaxInfo($tax_rate) {

		// price incl tax
				//GM_MOD:
				if(gm_get_conf('TAX_INFO_TAX_FREE') == 'true') {
					$tax_info = GM_TAX_FREE;
				}
				else {
					if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] != 0) {
						$tax_info = sprintf(TAX_INFO_INCL, $tax_rate.'%');
					}
					// excl tax + tax at checkout
					if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
						$tax_info = sprintf(TAX_INFO_ADD, $tax_rate.'%');
					}
					// excl tax
					if ($tax_rate > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0) {
						$tax_info = sprintf(TAX_INFO_EXCL, $tax_rate.'%');
					}
					// do not display tax info
					if(gm_get_conf('DISPLAY_TAX') == '0')
					{
						$tax_info = '';
					}
				}
		return $tax_info;
	}

	function getShippingNotice() {
		if (SHOW_SHIPPING == 'true') {
			// bof gm
			return ' '.SHIPPING_EXCL.'<a class="gm_shipping_link" href="' . $this->gm_get_shipping_link() . '"><span style="text-decoration:underline">'.SHIPPING_COSTS.'</span></a>';
			// eof gm
		}
		return;
	}

	function getContentLink($coID,$text) {
		return '<script language="javascript">document.write(\'<a href="javascript:newWin=void(window.open(\\\''.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.$coID).'\\\', \\\'popup\\\', \\\'toolbar=0, scrollbars=yes, resizable=yes, height=400, width=400\\\'))"><font color="#ff0000">'.$text.'</font></a>\');</script><noscript><a href="'.xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.$coID).'"target=_blank"><font color="#ff0000">'.$text.'</font></a></noscript>';
	}

	/* bof gm */
	function gm_get_shipping_link($p_lightbox = false) {
		static $gm_shipping_link;
		global $gmSEOBoost;

		if($gm_shipping_link !== null)
		{
			return $gm_shipping_link;
		}

		$gm_query = xtc_db_query('
			SELECT
				content_id,
				content_group,
				content_title
			FROM
				content_manager
			WHERE
				content_group = "' . SHIPPING_INFOS .'"
			AND
				languages_id = ' .  $_SESSION['languages_id'] . '
		');
		$row = xtc_db_fetch_array($gm_query);		

		if($gmSEOBoost->boost_content && $p_lightbox == false) {
			$gm_shipping_link = xtc_href_link($gmSEOBoost->get_boosted_content_url($row['content_id'], $_SESSION['languages_id']));	
		} else {
			$SEF_parameter = '';
			if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
				$SEF_parameter = '&content='.xtc_cleanName($row['content_title']);
			}
			$t_lightbox_get_param = '';
			if(lightbox_mode == true)
			{
				$t_lightbox_get_param = '&lightbox_mode=1';
			}
			$gm_shipping_link = xtc_href_link(FILENAME_POPUP_CONTENT, 'coID='.$row['content_group'].$t_lightbox_get_param.$SEF_parameter, 'SSL', false, true, true);
		}

		return $gm_shipping_link;
	}
	/* eof gm */

	public static function get_abroad_shipping_info_link()
	{
		static $t_abroad_shipping_info_link;
		
		if($t_abroad_shipping_info_link !== null)
		{
			return $t_abroad_shipping_info_link;
		}
		
		$t_abroad_shipping_info_link = '';

		$t_query = 'SELECT content_title
					FROM content_manager
					WHERE
						content_group = ' . (int)SHIPPING_INFOS . ' AND
						languages_id = ' .  (int)$_SESSION['languages_id'];
		$t_result = xtc_db_query($t_query);
		
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$coo_seo_boost = MainFactory::create_object('GMSEOBoost');

			$t_sef_parameter = '';
			if(SEARCH_ENGINE_FRIENDLY_URLS == 'true')
			{
				$t_sef_parameter = '&content=' . xtc_cleanName($t_result_array['content_title']);
			}

			if($coo_seo_boost->boost_content)
			{
				$t_abroad_shipping_info_link = xtc_href_link($coo_seo_boost->get_boosted_content_url($coo_seo_boost->get_content_id_by_content_group(SHIPPING_INFOS), $_SESSION['languages_id']));
			}
			else
			{
				$t_abroad_shipping_info_link = xtc_href_link(FILENAME_CONTENT, 'coID=' . (int)SHIPPING_INFOS . $t_sef_parameter);
			}
		}
		
		return $t_abroad_shipping_info_link;
	}
 }