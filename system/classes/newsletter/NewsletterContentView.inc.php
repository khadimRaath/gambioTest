<?php
/* --------------------------------------------------------------
   NewsletterContentView.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce www.oscommerce.com
   (c) 2003	 nextcommerce www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: newsletter.php,v 1.0)

   XTC-NEWSLETTER_RECIPIENTS RC1 - Contribution for XT-Commerce http://www.xt-commerce.com
   by Matthias Hinsche http://www.gamesempire.de

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class NewsletterContentView extends ContentView
{
	protected $form_send = false;
	protected $email_address = '';
	protected $info_message = '';
	protected $privacy_accepted = '0';
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/newsletter.html');
		$this->set_flat_assigns(true);
	}
	
	protected function set_validation_rules()
	{
		$this->validation_rules_array['form_send']		= array('type' => 'bool');
		$this->validation_rules_array['email_address']	= array('type' => 'string');
		$this->validation_rules_array['info_message']	= array('type' => 'string');
	}
	
	public function prepare_data()
	{
		$this->content_array['VVIMG_URL'] = xtc_href_link(FILENAME_DISPLAY_VVCODES);
		$this->content_array['info_message'] = $this->info_message;
		$this->content_array['FORM_ID'] = 'sign';
		$this->content_array['FORM_ACTION_URL'] = xtc_href_link(FILENAME_NEWSLETTER, 'action=process', 'NONSSL', true, true, true);
		$this->content_array['FORM_METHOD'] = 'post';
		$this->content_array['INPUT_EMAIL_NAME'] = 'email';
		$this->content_array['INPUT_EMAIL_VALUE'] = htmlentities_wrapper($this->email_address);
		$this->content_array['INPUT_CODE_NAME'] = 'vvcode';
		$this->content_array['INPUT_RADIO_NAME'] = 'check';
		$this->content_array['INPUT_SUBSCRIBE_VALUE'] = 'inp';
		$this->content_array['INPUT_UNSUBSCRIBE_VALUE'] = 'del';
		$this->content_array['BUTTON_BACK_LINK'] = xtc_href_link(FILENAME_ACCOUNT, '', 'SSL');
		$this->content_array['BUTTON_BACK_NL_LINK'] = xtc_href_link('newsletter.php', '', 'SSL');
		$this->content_array['form_send'] = $this->form_send;
		$this->content_array['GM_PRIVACY_LINK'] = gm_get_privacy_link('GM_CHECK_PRIVACY_ACCOUNT_NEWSLETTER');
		$this->content_array['show_privacy_checkbox'] = gm_get_conf('PRIVACY_CHECKBOX_NEWSLETTER');
		$this->content_array['privacy_accepted'] = (int)$this->privacy_accepted;
	}
}