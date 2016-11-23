<?php
/* --------------------------------------------------------------
  LayoutContentView.inc.php 2015-04-15 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

// include needed functions
require_once(DIR_FS_INC . 'xtc_banner_exists.inc.php');
require_once(DIR_FS_INC . 'xtc_display_banner.inc.php');
require_once(DIR_FS_INC . 'xtc_update_banner_display_count.inc.php');

class LayoutContentView extends ContentView
{
	protected $account_type;
	protected $bottom_content;
	protected $c_path;
	protected $category_id;
	protected $coo_breadcrumb;
	protected $coo_product;
	protected $coo_xtc_price;
	protected $customer_id;
	protected $error_message;
	protected $head_content;
	protected $info_message;
	protected $main_content;
	protected $popup_notification_content;
	protected $request_type;
	protected $topbar_content;
	protected $cookiebar_content;

	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('index.html');
		$this->set_flat_assigns(true);
	}

	public function prepare_data()
	{
		$this->set_content_data('HEAD', $this->head_content);
		$this->set_content_data('TOPBAR', $this->topbar_content);
		$this->set_content_data('COOKIEBAR', $this->cookiebar_content);
		$this->set_content_data('POPUP_NOTIFICATION', $this->popup_notification_content);

		include(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/source/boxes.php');
		if(MOBILE_ACTIVE == 'true')
		{
			include_once( DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/boxes_configuration.php');
		}

		if($this->customer_id !== null)
		{
			$this->set_content_data('logoff', xtc_href_link(FILENAME_LOGOFF, '', 'SSL'));
		}

		if($this->account_type == '0')
		{
			$this->set_content_data('account', xtc_href_link(FILENAME_ACCOUNT, '', 'SSL'));
		}

		$this->set_content_data('navtrail', $this->coo_breadcrumb->trail(' &raquo; '));
		$this->set_content_data('cart', xtc_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
		$this->set_content_data('checkout', xtc_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
		$this->set_content_data('store_name', TITLE);

		if($this->error_message != '')
		{
			$this->set_content_data('error', '
			<table border="0" width="100%" cellspacing="0" cellpadding="2">
			  <tr class="headerError">
				<td class="headerError">' . htmlspecialchars_wrapper($this->error_message) . '</td>
			  </tr>
			</table>');
		}

		if($this->info_message != '')
		{
			$this->set_content_data('error', '
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
				  <tr class="headerInfo">
					<td class="headerInfo">' . htmlspecialchars_wrapper($this->info_message) . '</td>
				  </tr>
				</table>'
			);
		}

		$this->set_content_data('BANNER', $this->get_banner());

		$this->set_content_data('main_content', $this->main_content);
		$this->set_content_data('BOTTOM', $this->bottom_content);

		$trustedShopsReviewSticker = '';
		$trustedShopsService = MainFactory::create_object('GMTSService');
		$tsid = $trustedShopsService->findRatingID($_SESSION['language_code']);
		if($tsid !== false)
		{
			$reviewSnippet = $trustedShopsService->getReviewStickerSnippet($tsid);
			if($reviewSnippet['enabled'] == true)
			{
				$trustedShopsReviewSticker .= $reviewSnippet['snippet_code'];
			}

			$appendRichSnippet = false;
			if((int)$this->category_id > 0 && $this->coo_product->isProduct == false && $trustedShopsService->richsnippets_enabled_categories == true)
			{
				// category page
				$appendRichSnippet = true;
			}
			else if((int)$this->category_id == 0 && $this->coo_product->isProduct == true && $trustedShopsService->richsnippets_enabled_products == true)
			{
				// product page
				$appendRichSnippet = true;
			}
			else if($trustedShopsService->richsnippets_enabled_other == true)
			{
				// other page
				$appendRichSnippet = true;
			}
			if($appendRichSnippet == true)
			{
				$trustedShopsReviewSticker .= $trustedShopsService->getRichSnippet($tsid);
			}
		}
		$this->set_content_data('TRUSTED_SHOPS_REVIEW_STICKER', $trustedShopsReviewSticker);
	}

	public function get_banner()
	{
		$t_banner = xtc_banner_exists('dynamic', 'banner');
		$t_banner_html = '';

		if($t_banner)
		{
			$t_banner_html = xtc_display_banner('static', $t_banner);
		}

		return $t_banner_html;
	}

	protected function set_validation_rules()
	{
		// GENERAL VALIDATION RULES
		$this->validation_rules_array['account_type']		= array('type' 			=> 'int');
		$this->validation_rules_array['bottom_content']		= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['c_path']				= array('type' 			=> 'string',
																	 'strict' 		=> 'true');
		$this->validation_rules_array['category_id']		= array('type' 			=> 'int');
		$this->validation_rules_array['coo_breadcrumb']		= array('type' 			=> 'object',
																	'object_type' 	=> 'breadcrumb');
		$this->validation_rules_array['coo_product']		= array('type' 			=> 'object',
																	'object_type' 	=> 'product');
		$this->validation_rules_array['coo_xtc_price']		= array('type' 			=> 'object',
																	'object_type' 	=> 'xtcPrice');
		$this->validation_rules_array['customer_id']		= array('type' 			=> 'int');
		$this->validation_rules_array['error_message']		= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['head_content']		= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['info_message']		= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['main_content']		= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['popup_notification_content']		= array('type' 			=> 'string',
																	  			 'strict' 		=> 'true');
		$this->validation_rules_array['request_type']		= array('type' 			=> 'string',
																	'strict' 		=> 'true');
		$this->validation_rules_array['topbar_content']		= array('type' 			=> 'string',
																	 'strict' 		=> 'true');
		$this->validation_rules_array['cookiebar_content']	= array('type' 			=> 'string',
																	 'strict' 		=> 'true');
	}
}
